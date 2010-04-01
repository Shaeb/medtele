<?
require_once( 'resources/bin/constants.php' );
require_once( 'resources/bin/classes/Header.php' );

class ModuleProcessor {
	private $moduleID;
	private $modules;
	private $dependentClass; // php class
	private $dependentStyle; // css
	private $dependentScript; // js
	private $dependentParameter; // query string
	private $moduleOutput;

	function __construct() {
		$this->modules = array();
		$this->dependentClass = array();
		$this->dependentStyle = array();
		$this->dependentScript = array();
		$this->dependentParameter = array();
	}

	public function addModule( $moduleName ) {
		$module = new Module( $moduleName );
		if ( $module ) {
			array_push( $this->modules, $module );
		}
	}

	public function processModules() {
		foreach( $this->modules as $module ) {
			$this->moduleOutput .= $module->process();
		}
		return $this->moduleOutput;
	}

	public function getModules() {
		return $this->modules;
	}

	public function getDependentClasses() {
		return $this->dependentClass;
	}

	public function getDependentStyles() {
		return $this->dependentStyle;
	}

	public function getDependentScripts() {
		return $this->dependentScript;
	}

	public function getDependentParameters() {
		return $this->dependentParameter;
	}
}

class Module {
	private $moduleName;
	private $dependentClass; // php class
	private $dependentStyle; // css
	private $dependentScript; // js
	private $dependentParameter; // query string
	private $xml;
	private $xslt;
	private $xsltProcessor;
	private $hasStyleSheet;
	private $output;

	function __construct( $moduleName ) {
		$this->moduleName = $moduleName;
		if ( $this->url_exists( MODULE_PATH . $this->moduleName . MODULE_EXTENSION ) ) {
			$this->xml = new DOMDocument();
			$this->hasStyleSheet = false;
			$this->dependentClass = array();
			$this->dependentStyle = array();
			$this->dependentScript = array();
			$this->dependentParameter = array();
			$this->output = '';
		} else {
			echo 'error: ' . MODULE_PATH . $this->moduleName;
		}
	}

	public function process() {
		$url = MODULE_PATH . $this->moduleName . MODULE_EXTENSION;
		if( $this->url_exists( $url ) ) {
			$this->xml->load( $url );
			$root = $this->xml->firstChild;
			$this->processNode( $root );
		}
		return $this->output;
	}

	private function processNode( DOMNode $node ) {
		if( $node ) {
			// make sure we are processing a module document
			if ( MODULE_TAG == $node->tagName ) {
				// process the child documents
				if ( $node->hasChildNodes() ) {
					$this->processNodeList( $node->childNodes );
				}
			}
		}
	}

	private function processNodeList( DOMNodeList $nodes ) {
		foreach( $nodes as $node ) {
			switch( $node->tagName ) {
				case DEPENDENCY_TAG:
					$this->addDependency( $node );
					break;
				case DATA_TAG:
					$this->addData( $node );
					break;
				case STYLESHEET_TAG:
					$this->addStyleSheet( $node );
					break;
			}
			if( $node->hasChildNodes() ) {
				$this->processNodeList( $node->childNodes );
			}
		}
	}

	public function getModuleName() {
		return $this->moduleName;
	}

	public function getDependentClasses() {
		return $this->dependentClass;
	}

	public function getDependentStyles() {
		return $this->dependentStyle;
	}

	public function getDependentScripts() {
		return $this->dependentScript;
	}

	public function getDependentParameters() {
		return $this->dependentParameter;
	}

	public function getXML() {
		return $this->xml;
	}

	private function addDependency( DOMNode $node ) {
		if( $node->hasAttribute( DEPENDENCY_TYPE_ATTRIBUTE_NAME ) ) {
			switch( $node->getAttribute( DEPENDENCY_TYPE_ATTRIBUTE_NAME ) ) {
				case DEPENDENCY_TYPE_CLASS_VALUE:
					require_once( $node->nodeValue . OBJECT_EXTENSION );
					array_push( $this->dependentClass, $node->nodeValue );
					break;
				case DEPENDENCY_TYPE_STYLE_VALUE:
					array_push( $this->dependentStyle, $node->nodeValue );
					break;
				case DEPENDENCY_TYPE_SCRIPT_VALUE:
					array_push( $this->dependentScript, $node->nodeValue );
					break;
				case DEPENDENCY_TYPE_PARAMETER_VALUE:
					array_push( $this->dependentParameter, $node->nodeValue );
					break;
			}
		}
	}

	private function addData( DOMNode $node ) {
		if( $node->hasAttribute( DATA_TYPE_ATTRIBUTE_NAME ) ) {
			switch( $node->getAttribute( DEPENDENCY_TYPE_ATTRIBUTE_NAME ) ) {
				case DATA_TYPE_STREAM_VALUE:
					$url = $this->buildURL( MODULE_STREAM_PATH . $node->nodeValue );
					if( $this->url_exists( $url ) ) {
						$this->xml->load( $url );
					}
					break;
				case DATA_TYPE_PROCESSOR_VALUE:

					break;
				case DATA_TYPE_DYNAMICFILE_VALUE:
					if ( file_exists( MODULE_FILE_PATH . $node->nodeValue ) ) {
						ob_start();
						$file = MODULE_FILE_PATH . $node->nodeValue;
						include $file;
						$this->output .= ob_get_contents();
						ob_end_clean();
					}
					break;
				case DATA_TYPE_STATICFILE_VALUE:
					if ( file_exists( MODULE_FILE_PATH . $node->nodeValue ) ) {
						$this->output .= file_get_contents( MODULE_FILE_PATH . $node->nodeValue );
					}
					break;
			}
		}
	}

	private function addStyleSheet( DOMNode $node ) {
		$url = MODULE_XSL_PATH . $node->nodeValue;
		if( $this->url_exists( $url ) ) {
			$this->xslt = new DOMDocument();
			$this->xsltProcessor = new XSLTProcessor();
			$this->xslt->load( $url );
			$this->xsltProcessor->importStyleSheet( $this->xslt );
			$this->output .= $this->xsltProcessor->transformToXML( $this->xml );
		}
	}

	private function url_exists($url) {
		$hdrs = @get_headers($url);
		return is_array($hdrs) ? preg_match('/^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$/',$hdrs[0]) : false;
	}

	private function buildURL( $base ) {
		$url = $base;

		if ( count( $this->dependentParameter ) ) {
			$url .= '?';
			foreach( $this->dependentParameter as $parameter ) {
				$url .= "{$parameter}={$_REQUEST[$parameter]}";
			}
		}
		return $url;
	}
}
?>