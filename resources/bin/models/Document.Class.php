<?

abstract class Document extends DOMDocument {

	protected function urlExists($url) {
		$hdrs = @get_headers($url);
		return is_array($hdrs) ? preg_match("/^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$/",$hdrs[0]) : false;
	}

	protected function log( $data ) {
		return; // just temporary because i dont wont to work with output buffering ATM
		if( DEBUGGING ) {
			ob_start();
			echo "$data\n";
			ob_flush();
		}
	}

	protected function query( $tagName, DOMNode $relativeNode = null) {
		//$this->log( "<!-- start: load $tagName -->" );
		$xpath = new DOMXPath( $this );
		//$query = (isset($relativeNode)) ? ".//{$tagName}" : "//{$tagName}";
		if(isset($relativeNode)){
			$nodes = $xpath->query( ".//{$tagName}", $relativeNode );
		} else {
			$nodes = $xpath->query( "//{$tagName}" );
		}
		return $nodes;
	}

	protected function stripHTMLOutput( $data ) {
		$data = preg_replace( '/^<!DOCTYPE.+?>/', '',
							str_replace( array('<html>', '</html>', '<body>', '</body>'),
							array('', '', '', ''), $data ) );
		return $data;
	}
	
	// for this function, a child is unique if at least the nodeValue is different from any other child of the same nodeType 
	public function appendUniqueChildToParent($parent, $child, $compare) {
		$success = false;
		if(isset($parent) && isset($child)){
			$nodes = $this->query($child->nodeName, $parent);
			
			// if there are none of the same tags in the parent, then it is unique
			if(0 == $nodes->length) {
				$parent->appendChild($child);
				$success = true;
			} else {
				$duplicate = false;
				foreach($nodes as $node){
					if(isset($compare)) {
						//will call user-supplied compare function
						$duplicate = call_user_func($compare, $child, $node);
						//$duplicate = $compare($child, $node);
						if($duplicate){
							break;
						}
					} else {
						if($node->nodeValue == $child->nodeValue) {
							$duplicate = true;
							$success = false;
							break;
						}
					}
				}
				if(!$duplicate){
					$parent->appendChild($child);
					$success = true;
				}
			}
		} else {
			echo "parent or child not set";
			exit();
		}
		return success;
	}
}

?>