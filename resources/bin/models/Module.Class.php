<?
//require_once( 'resources/bin/constants.php' );

class Module extends Document {
	private $replacementTags;
	private $moduleName;
	private $definedName; // from XML file
	private $url;
	private $dynamicFileOutput;
	private $staticFileOutput;
	private $xmlOutput;
	public  $isLoaded;

	function __construct( $moduleName ) {
		if( $moduleName ) {
			$this->moduleName = $moduleName;
			$this->url = MODULE_PATH . $this->moduleName . MODULE_EXTENSION;
			$this->replacementTags = array();
			$this->dynamicFileOutput = '';
			$this->staticFileOutput = '';
			$this->xmlOutput = new DOMDocument();
			if( $this->urlExists( $this->url ) ) {
				$this->loadModule();
			}
		}
	}

	private function loadModule() {
		$this->log( "<!-- loading module $this->url -->" );
		$this->isLoaded = $this->load( $this->url );

		if( !$this->isLoaded ) {
			return;
		}

		if( !$this->hasChildNodes() ) {
			return;
		}

		$parent = $this->firstChild;

		// defined name is used to wrap the module in a div with the name and id set to the defined name
		// <module name=DEFINED_NAME>
		if( $parent->hasAttribute( MODULE_NAME_ATTRIBUTE ) ) {
			$this->definedName = $parent->getAttribute( MODULE_NAME_ATTRIBUTE );
		}

		// process the child nodes
		$this->processTags( $parent->childNodes );
		$this->getOutput();
	}

	private function processTags( DOMNodeList $nodes ) {
		foreach( $nodes as $node ) {
			switch( $node->tagName ) {
				case DEPENDENCY_TAG:
					//$this->addDependency( $node );
					break;
				case DATA_TAG:
					$this->processDataTag( $node );
					break;
				case STYLESHEET_TAG:
					//$this->addStyleSheet( $node );
					break;
			}
			if( $node->hasChildNodes() ) {
				$this->processTags( $node->childNodes );
			}
		}
	}

	private function processDataTag( DOMNode $node ) {
		if( $node->hasAttribute( DATA_TYPE_ATTRIBUTE_NAME ) ) {
			switch( $node->getAttribute( DEPENDENCY_TYPE_ATTRIBUTE_NAME ) ) {
				case DATA_TYPE_STREAM_VALUE:
				/******* nothing for now
					$url = $this->buildURL( MODULE_STREAM_PATH . $node->nodeValue );
					if( $this->url_exists( $url ) ) {
						$this->xml->load( $url );
					}
				**********/
					break;
				case DATA_TYPE_PROCESSOR_VALUE:

					break;
				case DATA_TYPE_DYNAMICFILE_VALUE:
					if ( file_exists( MODULE_FILE_PATH . $node->nodeValue ) ) {
						ob_start();
						$file = MODULE_FILE_PATH . $node->nodeValue;
						include $file;
						$this->dynamicFileOutput .= ob_get_contents();
						ob_end_clean();
					}
					break;
				case DATA_TYPE_STATICFILE_VALUE:
					if ( file_exists( MODULE_FILE_PATH . $node->nodeValue ) ) {
						$this->staticFileOutput .= file_get_contents( MODULE_FILE_PATH . $node->nodeValue );
					} else {
						
						//echo "loading: " . MODULE_FILE_PATH . $node->nodeValue;
					}
					break;
			}
		}
	}

	public function getOutput($format = XML_FORMAT) {
		$output = "<div id='{$this->definedName}' name='{$this->definedName}'>\n";
		$output .= $this->dynamicFileOutput . "\n";
		$output .= $this->staticFileOutput . "\n";
		$output .= "</div>\n";
		$this->xmlOutput->loadHTML( $output );
		//$this->log( "<!-- " . $this->stripHTMLOutput( $this->xmlOutput->saveHTML() ) . " --> ");
		return $this->stripHTMLOutput( $this->xmlOutput->saveHTML() );
	}
}
?>