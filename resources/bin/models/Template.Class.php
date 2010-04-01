<?
//require_once( 'resources/bin/constants.php' );

class Template extends Document {
	private $replacementTags;
	private $templateName;
	private $url;
	public  $isLoaded;

	function __construct( $templateName ) {
		if( $templateName ) {
			$this->templateName = $templateName;
			$this->url = TEMPLATE_PATH . $this->templateName;
			$this->replacementTags = array();
			if( $this->urlExists( $this->url ) ) {
				$this->loadTemplate();
			}
		}
	}

	private function loadTemplate() {
		$this->log( "<!-- loading template $this->url -->" );
		$this->isLoaded = $this->load( $this->url );

		if( !$this->isLoaded ) {
			return;
		}

		$xpath = new DOMXPath( $this );
		$nodes = $xpath->query( '//' . REPLACE_TAG );

		if( $nodes->length ) {
			foreach( $nodes as $node ) {
				if( REPLACE_TAG == $node->nodeName && $node->hasAttribute( REPLACE_TAG_REPLACE_ATTRIBUTE ) ) {
					$attr = $node->getAttribute( REPLACE_TAG_REPLACE_ATTRIBUTE );
					if( $attr ) {
						if( !array_key_exists( $attr, $this->replacementTags ) ) {
							$this->replacementTags[ $attr ] = $node;
							//$this->log( ": found attribute " . $attr . "  -> " . $attr );
						}
					}
				}
			}
		}
	}

	public function bind( $input ) {
		if( null === $input ) {
			return false;
		}

		$keys = array_keys( $input );
		$node = null;
		$parent = null;
		$replacementNode = null;

		foreach( $keys as $key ) {
			if( array_key_exists( $key, $this->replacementTags ) ) {
				$node = $this->replacementTags[ $key ];
				if( null != $node ) {
					$parent = $node->parentNode;
					$replacementNode = new DOMElement( "div", $input[ $key ] );
					$parent->replaceChild( $replacementNode, $node );

					$node = null;
					$parent = null;
					$replacementNode = null;
				}
			}
		}
	}
	
	public function replaceValueOfTag($tagName, $newContent, $numberToReplace = 1){
		if( 0 >= $numberToReplace) { 
			return null;
		}
		$nodes = $this->query($tagName);
		$i = 0;
		foreach( $nodes as $node ){
			if($i >= $numberToReplace){
				break;
			}
			$node->nodeValue = $newContent;
			$i++;
		}
	}
}
?>