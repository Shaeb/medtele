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

	protected function query( $tagName ) {
		//$this->log( "<!-- start: load $tagName -->" );
		$xpath = new DOMXPath( $this );
		$nodes = $xpath->query( "//$tagName" );
		//$this->log( "<!-- # of nodes: $nodes->length -->" );
		return $nodes;
	}

	protected function stripHTMLOutput( $data ) {
		$data = preg_replace( '/^<!DOCTYPE.+?>/', '',
							str_replace( array('<html>', '</html>', '<body>', '</body>'),
							array('', '', '', ''), $data ) );
		return $data;
	}
}

?>