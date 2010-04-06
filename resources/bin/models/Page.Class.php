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
	public  $isLoaded;

	function __construct( $pageName ) {
		if( $pageName ) {
				$this->pageName = $pageName;
				$this->url = PAGE_PATH . $this->pageName . PAGE_EXTENSION;
				if( $this->urlExists( $this->url ) ) {
					$this->log( "<!-- loading page $this->url -->" );
					$this->isLoaded = $this->load( $this->url );
					$this->tags = array( TEMPLATE_TAG, MODULES_TAG, DEPENDENCY_TAG, TITLE_TAG );
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
							global $application;							
							if('true' == $module->getAttribute(AUTHENTICATED_ATTRIBUTE_NAME) && true == $application->session->isSessionAuthenticated()) {
								array_push( $this->moduleList[ $attributeValue ], new Module( $module->nodeValue ) );								
							} else {
								// for modules that should show up only if there is no session
								if('false' == $module->getAttribute(AUTHENTICATED_ATTRIBUTE_NAME) && false == $application->session->isSessionAuthenticated()) {
									array_push( $this->moduleList[ $attributeValue ], new Module( $module->nodeValue ) );
								}
							}
						} else {
							array_push( $this->moduleList[ $attributeValue ], new Module( $module->nodeValue ) );
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
	
				if($node->hasAttribute("type")){
					$dependencyType = $node->getAttribute("type");
					switch($dependencyType){
						case DEPENDENCY_TYPE_STYLE_VALUE:
							$element = $this->template->createElement("link");
							$element->setAttribute("rel","stylesheet");
							$element->setAttribute("href", STYLE_PATH . $node->nodeValue);
							$element->setAttribute("type","text/css");
							$element->setAttribute("media",(($node->hasAttribute("media")) ? $node->getAttribute("media") : "media"));
							$this->template->appendUniqueChildToParent($head,$element,$compareElements);							
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
				$tempOutput .= $module->output();
			}
			$bind[ $key ] = $tempOutput;
			//$this->log( "<!-- $key: \n\n $tempOutput -->\n\n" );
		}

		$this->template->bind( $bind );

		$output = $this->template->saveHTML();
		return htmlspecialchars_decode( $output );
	}
}

?>