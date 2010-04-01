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
					//$this->processDependency( $nodes );
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
						$tempModule = new Module( $module->nodeValue );
						array_push( $this->moduleList[ $attributeValue ], $tempModule );
						$tempModule = null;
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

	public function output() {
		$output = '';
		$tempOutput = '';
		$bind = array();
				
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