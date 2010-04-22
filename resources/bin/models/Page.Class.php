<?
//require_once( 'resources/bin/constants.php' );
add_required_class( 'Template.Class.php', MODEL );
add_required_class( 'Module.Class.php', MODEL );

class Page extends Document {
	private $template;
	private $pageName;
	private $url;
	private $tags;
	private $moduleList;
	private $title;
	private $application;
	private $scaffolding;
	public  $isLoaded;

	function __construct( $pageName ) {
		$this->application = ApplicationController::getInstance();
		if(isset($_REQUEST["scaffolding"]) && "true" == $_REQUEST["scaffolding"]){	
			add_required_class( 'Scaffold.Controller.php', SCAFFOLD );
			$this->application->loadScaffoldingSettings("");
			$settings = $this->application->getSettings();
			$actions = array(ACTION_LIST,ACTION_DELETE,ACTION_UPDATE,ACTION_FIND,ACTION_ADD);
			$action = "";
			/***
			 * we are going to change page=AddUsersPage to
			 * action = add
			 * table = Users
			 * page = DefaultScaffoldingPage
			 * 
			 * this is because we want to pull out the information from the query string to make it simple, give it a dynamic url
			 * and we are only using one scaffolding page in ApplicationSettings->global->scaffolding->default_page, to skin the page
			 */
			foreach($actions as $actionType){
				$ucActionType = ucfirst($actionType);
				$regexAction = "/^{$ucActionType}/";
				if(1 == preg_match($regexAction, $pageName)){
					$action = $actionType;
					break;
				}
			}
			$table = preg_replace($regexAction, "", $pageName);
			$table = preg_replace("/Page$/", "", $table);
			$_REQUEST["action"] = $action;
			$_REQUEST["table"] = $table;
			$pageName = $settings["global"]["scaffolding"]["default_page"];
			$_REQUEST["page"] = $pageName;
			$this->scaffolding = ScaffoldController::getInstance($this->application);
		}
		if( $pageName ) {
				$this->pageName = $pageName;
				$this->url = PAGE_PATH . $this->pageName . PAGE_EXTENSION;
				if( $this->urlExists( $this->url ) ) {
					$this->log( "<!-- loading page $this->url -->" );
					$this->isLoaded = $this->load( $this->url );
					$this->tags = array( TEMPLATE_TAG, MODULES_TAG, DEPENDENCY_TAG, TITLE_TAG, MESSAGING_TAG );
					$this->moduleList = array();
				} else {
					$this->log( "url not found: $this->url." );
				}
		}
	}

	public function process() {
		foreach( $this->tags as $tag ) {
			$nodes = $this->query( $tag );
			switch( $tag ) {
				case DEPENDENCY_TAG:
					$this->processDependency( $nodes );
					break;
				case TITLE_TAG:
					$this->processTitle( $nodes );
					break;
				case MODULES_TAG:
					$this->processModules( $nodes );
					break;
				case TEMPLATE_TAG:
					$this->processTemplate( $nodes );
					break;
				case MESSAGING_TAG:
					//$this->processScaffolding($nodes);
					break;
			}
			if( $nodes->length ) {
				foreach( $nodes as $node ) {
					$this->log( "<!-- found: $node->nodeName - $node->nodeValue -->" );
				}
			}
		}
	}

	private function processTemplate( $nodes ) {
		if( $nodes->length ) {
			//will only apply 1 template, so take the first one
			$node = $nodes->item( 0 );
			$this->log( "<!-- processing template $node->nodeValue -->" );
			$this->template = new Template( $node->nodeValue );
		}
	}

	private function processModules( $nodes ) {
		$this->log( "<!-- processing modules --> ");
		//this is all of the <modules>
		$attributeValue = '';
		foreach( $nodes as $modules ) {
			// if there are no <module> elements in <modules>, then we don't care about processing it
			if( $modules->hasChildNodes() ) {
				// determine attributes for replacement in template, this will be how they are grouped
				if( $modules->hasAttribute( MODULES_REPLACE_ATTRIBUTE ) ) {
					$attributeValue = $modules->getAttribute( MODULES_REPLACE_ATTRIBUTE );
					if( $attributeValue ) {
						if( !array_key_exists( $attributeValue, $this->moduleList ) ) {
							// will populate with the list of module tags once they have been turned into objects
							$this->moduleList[ $attributeValue ] = array();
						}
					}
				} else {
					$this->log( "<!-- ERROR: attribute not found! --> " );
				}

				// process the <module> elements in <modules>
				foreach( $modules->childNodes as $module ) {
					// now, we only care about processing <module>
					if( MODULE_TAG == $module->nodeName ) {
						if($module->hasAttribute(AUTHENTICATED_ATTRIBUTE_NAME)){
							if('true' == $module->getAttribute(AUTHENTICATED_ATTRIBUTE_NAME) && true == $this->application->session->isSessionAuthenticated()) {
								array_push( $this->moduleList[ $attributeValue ], new Module( $module->nodeValue ) );								
							} else {
								// for modules that should show up only if there is no session
								if('false' == $module->getAttribute(AUTHENTICATED_ATTRIBUTE_NAME) && false == $this->application->session->isSessionAuthenticated()) {
									array_push( $this->moduleList[ $attributeValue ], new Module( $module->nodeValue ) );
								}
							}
						} else {
							array_push( $this->moduleList[ $attributeValue ], new Module( $module->nodeValue ) );
						}
					} else {
						if( SCAFFOLDING_TAG == $module->nodeName ){
							print_r($_REQUEST);
							//foreach($module as $node){
								if($module->hasAttribute("dynamic") && "true" == $module->getAttribute("dynamic")){
									array_push( $this->moduleList[ $attributeValue ], $this->scaffolding );
								}
							//}
						}
					}
				}
				$attributeValue = ''; //reset
			}
		}
		// go through all of the <modules> to find <module>
	}
	
	public function processTitle($nodes){
		if(!isset($nodes) && $nodes->length <= 0){
			return;
		}
		$this->title = $nodes->item(0)->nodeValue;
	// update page title ...
		if(isset($this->title)){
			$this->template->replaceValueOfTag( TITLE_TAG, $this->title);
		}
	}
	
	// add script and link tag dependencies
	public function processDependency($nodes){
		if(!isset($nodes)){
			return;
		}

		foreach($nodes as $node){		
			if($node->hasAttributes()){
				// setup dependencies
				$head = $this->template->query("head");
				if(!isset($head) || 0 == $head->length){
					return;
				}
				// we are really only expecting one head element in an xhtml document ...
				$head = $head->item(0);
				$dependencyType = '';
				$element = null;
				
				/********************************************
				 * current host is 5.2.1, does not support closures, will update when host updates
				 
				$compareElements = function($node1,$node2){
					$linkType = '';
					$duplicate = true;
					if(isset($node1) && isset($node2)){
						$linkType = ($node1->nodeName == "link") ? "href" : "src";
						if($node1->hasAttribute($linkType) && $node2->hasAttribute($linkType)){
							$duplicate = (0 == strcmp($node1->getAttribute($linkType), $node2->getAttribute($linkType))) ? true : false;						
						}
					}
					return $duplicate;
				};
				*/
				
				if($node->hasAttribute("type")){
					$dependencyType = $node->getAttribute("type");
					switch($dependencyType){
						case DEPENDENCY_TYPE_STYLE_VALUE:
							$element = $this->template->createElement("link");
							$element->setAttribute("rel","stylesheet");
							$element->setAttribute("href", STYLE_PATH . $node->nodeValue);
							$element->setAttribute("type","text/css");
							$element->setAttribute("media",(($node->hasAttribute("media")) ? $node->getAttribute("media") : "media"));
							$this->template->appendUniqueChildToParent($head,$element,array("Page", "compareElements"));							
							break;
						case DEPENDENCY_TYPE_SCRIPT_VALUE:
							$element = $this->template->createElement("script");
							$element->setAttribute("src", JAVASCRIPT_PATH . $node->nodeValue);
							$element->setAttribute("type","text/javascript");
							$this->template->appendUniqueChildToParent($head,$element,$compareElements);
							break;
						case DEPENDENCY_TYPE_CLASS_VALUE:
							if($node->hasAttribute("subtype")){
								add_required_class( $node->nodeValue, $node->getAttribute("subtype") );
							}
							break;
						default:
							break;
					}
				}
			}
		}
	}
	
	public static function compareElements($node1,$node2){
		$linkType = '';
		$duplicate = true;
		if(isset($node1) && isset($node2)){
			$linkType = ($node1->nodeName == "link") ? "href" : "src";
			if($node1->hasAttribute($linkType) && $node2->hasAttribute($linkType)){
				$duplicate = (0 == strcmp($node1->getAttribute($linkType), $node2->getAttribute($linkType))) ? true : false;						
			}
		}
		return $duplicate;
	}
	
	public function buildMessages(){
		$messageTags = $this->template->getElementsByTagName(MESSAGING_TAG);
		if(0 != $messageTags->length){
			$messageTag = $messageTags->item(0);
			$messages = $this->application->getMessages();
			$div = $this->template->createElement("div");
			$messageKeys = array_keys($messages);
			foreach($messageKeys as $key){
				if(0 < count($messages[$key])){
					$h2 = $this->template->createElement("h2",$key);
					$div->appendChild($h2);
					$ul = $this->template->createElement("ul");
					$ul->setAttribute("class","message_{$key}");
					$messageGroups = $messages[$key];
					foreach($messageGroups as $message){
						$li = $this->template->createElement("li", $message);
						$ul->appendChild($li);
					}
					$div->appendChild($ul);
				}
			}
			$messageTag->parentNode->replaceChild($div,$messageTag);
		}
	}

	public function output() {
		$output = '';
		$tempOutput = '';
		$bind = array();
		
		// done now on to output ...			
		// first, we need a list of keys
		$keys = array_keys( $this->moduleList );

		foreach( $keys as $key ) {
			$tempOutput = '';
			foreach( $this->moduleList[ $key ] as $module ) {
				$tempOutput .= $module->getOutput(XML_FORMAT,$_REQUEST);
			}
			$bind[ $key ] = $tempOutput;
			//$this->log( "<!-- $key: \n\n $tempOutput -->\n\n" );
		}
		// get any messages
		
		$this->application->addMessage("this is a test", ERROR_MESSAGES);
//		$this->application->addMessage("this is a test", WARNING_MESSAGES);
		$this->application->addMessage("this is a test",INFORMATIONAL_MESSAGES);
		$this->buildMessages();
		$this->template->bind( $bind );

		$output = $this->template->saveHTML();
		return htmlspecialchars_decode( $output );
	}
}

?>