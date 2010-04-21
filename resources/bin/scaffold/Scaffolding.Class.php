<?php
//require_once( '../constants.php' );
add_required_class( 'Scaffold.Class.php', SCAFFOLD );

class Scaffolding extends Document{
	private $template;
	private $url;
	private $scaffoldObject;
	private $willUseCustomTemplate;
	
	public function __construct(ScaffoldObject $scaffoldingObject, $url){
		if(!isset($scaffoldingObject)){
			throw new Exception( "ScaffoldObject not set.  Cannot build scaffold.");
		}
		
		$this->scaffoldObject = $scaffoldingObject;
		$this->willUseCustomTemplate = false;
		
		if(isset($url)){
			$this->url = SCAFFOLDING_TEMPLATE_PATH . $url; // MODULE_EXTENSION = ".xml" ATM
			if($this->urlExists($this->url)){
				if($this->load($this->url)){
					$this->willUseCustomTemplate = true;
				} else {
					throw new Exception( "Unable to load scaffold template @{$this->url}.");
				}
			} 
		}
	}
	
	public function bind(){
		// first will save the template as an html string, then will loop throguh parameters and replace as needed
		if($this->willUseCustomTemplate){
			$data = $this->saveXML();
			$keys = array_keys($this->scaffoldObject->values);
			foreach($keys as $key){
				// will look for the following tokens
				// [FIELDNAME] = the field name ex: [UserId] = $o->UserId
				// [FIELDNAME_VALUE] = the value for the field. ex: [UserId_VALUE] = value of $o->UserId
				$objectName = "[OBJECTNAME]";
				$fieldName = "[$key]";
				$fieldValue = "[{$key}_VALUE]";
				//echo "looking for {$fieldName} and {$fieldValue}<br/>";
				$data = str_replace($objectName, $this->scaffoldObject->table_name, $data);
				$data = str_replace($fieldName, $key, $data);
				$data = str_replace($fieldValue, $this->scaffoldObject->values[$key], $data);
			}
			if(!$this->loadXML($data)){
				throw new Exception( "Could not successfully bind/load data to template @{$this->url}.  Please check formatting");
			}
		}
	}
	
	public function buildDefaultForm($action){
		if(!isset($action)){
			throw new Exception("Action is not set.  Could not build scaffold form.");
		}
		switch($action){
		case ACTION_LIST:
			$this->buildList($action);
			break;
		case ACTION_ADD:
		case ACTION_DELETE:
		case ACTION_UPDATE:
		case ACTION_FIND:
		default:
			$this->buildForm($action);
			break;
		}
	}
	
	private function buildForm($action){
		$primaryKey = $this->scaffoldObject->primaryKey;
		$this->loadXML("<form></form>");
		$root = $this->documentElement;
		$root->setAttribute("action", $_SERVER["REQUEST_URI"]);
		$root->setAttribute("method","POST");
		$root->setAttribute("class", "scaffolding_form scaffolding_{$action}");
		
		$input = $this->createElement("input");
		$input->setAttribute("type", "hidden");
		$input->setAttribute("name", "object");
		$input->setAttribute("value", $this->scaffoldObject->table_name);
		$root->appendChild($input);
		
		$input = $this->createElement("input");
		$input->setAttribute("type", "hidden");
		$input->setAttribute("name", "id");
		$input->setAttribute("value", $this->scaffoldObject->$primaryKey);
		$root->appendChild($input);
		
		$input = $this->createElement("input");
		$input->setAttribute("type", "hidden");
		$input->setAttribute("name", $this->scaffoldObject->primaryKey);
		$input->setAttribute("value", $this->scaffoldObject->$primaryKey);
		$root->appendChild($input);
		
		$input = $this->createElement("input");
		$input->setAttribute("type", "hidden");
		$input->setAttribute("name", "action");
		$input->setAttribute("value", $action);
		$root->appendChild($input);
		
		$keys = array_keys($this->scaffoldObject->values);
		
		foreach($keys as $key){
			if($key != $primaryKey){
				$p = $this->createElement("p");
				//<label for="username">Username:</label>
				$label = $this->createElement("label", "{$key}:");
				$label->setAttribute("for", $key);
				$p->appendChild($label);
				
				//<input type="text" name="username" id="username_field" value="">
				$input = $this->createElement("input");
				$input->setAttribute("type", "text");
				$input->setAttribute("name", $key);
				$input->setAttribute("id", "{$key}_field");
				$input->setAttribute("value", $this->scaffoldObject->$key);
				$p->appendChild($input);
				$root->appendChild($p);
			}
		}
		//<input type="submit" name="submit" value="Delete">
		$input = $this->createElement("input");
		$input->setAttribute("type", "submit");
		$input->setAttribute("name", "submit");
		$input->setAttribute("id", "submit_field");
		$input->setAttribute("value", $action);
		$root->appendChild($input);
	}
	
	private function buildList($action){
		$objects = $this->scaffoldObject->find("all");
		$length = count($objects);
		if(0 == $length){
			$session = SessionController::getInstance();
			$session->addMessage("Could not find and objects of type {$this->scaffoldObject->table_name}.  Please create a new {$this->scaffoldObject->table_name} object.");
			print_r($_SESSION);
			exit(0);
		}
		$this->loadXML("<table></table>");
		$root = $this->documentElement;
		$root->setAttribute("class", "scaffolding_list");

		$thead = $this->createElement("thead");
		
		$keys = array_keys($objects[0]->values);
		$tr = $this->createElement("tr");
		foreach($keys as $key){
			$td = $this->createElement("td", $key);
			$tr->appendChild($td);
		}
		$thead->appendChild($tr);
		$root->appendChild($thead);
		
		$tbody = $this->createElement("tbody");
		for($i = 0; $i < $length; ($i++)){
			$tr = $this->createElement("tr");
			$class = ( 0 == ($i % 2 )) ? "scaffolding_list_row_even" : "scaffolding_list_row_odd";
			$tr->setAttribute("class", $class);
			foreach($keys as $key){			
				$td = $this->createElement("td", $objects[$i]->$key);
				$tr->appendChild($td);
			}
			$tbody->appendChild($tr);
		}
		$root->appendChild($tbody);
	}
}
?>