<?
require_once( 'resources/bin/constants.php' );
require_once( 'resources/bin/classes/ModuleProcessor.php' );

class PageProcessor {
}

class Page {
	private $title;
	private $pageName;
	private $dependentClass; // php class
	private $dependentStyle; // css
	private $dependentScript; // js
	private $dependentParameter; // query string
	private $xml;
	private $xslt;
	private $xsltProcessor;
	private $moduleProcessor;
	private $pageOutput; // result of parsing the modules
	private $security;
	private $template;

	function __construct( $pageName ) {
		$this->pageName = $pageName;
		if ( $this->url_exists( PAGE_PATH . $this->pageName . PAGE_EXTENSION ) ) {
			$this->xml = new DOMDocument();
			$this->xslt = new DOMDocument();
			$this->xsltProcessor = new XSLTProcessor();
			$this->dependentClass = array();
			$this->dependentStyle = array();
			$this->dependentScript = array();
			$this->dependentParameter = array();
			$this->moduleProcessor = new ModuleProcessor();
		} else {
			echo 'error: ' . PAGE_PATH . $this->pageName;
		}
	}

	public function process() {
		$url = PAGE_PATH . $this->pageName . PAGE_EXTENSION;
		if( $this->url_exists( $url ) ) {
			$this->xml->load( $url );
			$root = $this->xml->firstChild;
			$this->processNode( $root );
			if( $this->template ) {
				$output = '';
				$xpath = new DOMXPath( $this->template );
				$nodes = $xpath->query( '//replace' );
				if( $nodes->length ) {
					$output = $this->moduleProcessor->processModules();
					$div = $this->template->createElement( 'div', $output );
					foreach( $nodes as $node ) {
						$node->parentNode->replaceChild( $div, $node );
						//print $node->saveXML();
						//exit();
					}
					$this->pageOutput .= $this->template->saveHTML();
				}
			} else {
				$this->pageOutput .=  'no template: ' . $this->moduleProcessor->processModules();
			}

			return $this->pageOutput;
		}
	}

	private function processNode( DOMNode $node ) {
		if( $node ) {
			// make sure we are processing a module document
			if ( PAGE_TAG == $node->tagName ) {
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
				case TITLE_TAG:
					$this->addTitle( $node );
					break;
				case MODULE_TAG:
					$this->addModule( $node );
					break;
				case TEMPLATE_TAG:
					$this->addTemplate( $node );
					break;
			}
			if( $node->hasChildNodes() ) {
				$this->processNodeList( $node->childNodes );
			}
		}
	}

	private function addTemplate( $node ) {
		$url = TEMPLATE_PATH . $node->nodeValue;
		$xml = null;
		$this->pageOutput .= "<!-- loading template $url -->";
		if( $this->url_exists( $url ) ) {
			$xml = new DOMDocument();
			$xml->load( TEMPLATE_PATH . $node->nodeValue );
			if( null != $xml ) {
				//$this->pageOutput .= "<-- from template " . $xml->saveXML() . "-->";
				$this->template = $xml;
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

	private function addTitle( DOMNode $node ) {
		$this->title = $node->nodeValue;
	}

	private function addModule( DOMNode $node ) {
		$url = MODULE_PATH . $node->nodeValue . MODULE_EXTENSION;
		if( $this->url_exists( $url ) ) {
			$this->moduleProcessor->addModule( $node->nodeValue );
		}
	}

	private function url_exists($url) {
		$hdrs = @get_headers($url);
		return is_array($hdrs) ? preg_match('/^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$/',$hdrs[0]) : false;
	}
}

class SecurityOptions {
	private $session;
	private $gate;
	private $userAccess;
	private $groupAccess;
	private $logging;

	function __construct() {
		$this->session = false;
		$this->gate = '';
		$this->userAccess = array();
		$this->groupAccess = array();
		$this->logging = false;
	}
}
?>