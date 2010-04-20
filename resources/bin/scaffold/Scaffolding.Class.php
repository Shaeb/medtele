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
}
?>