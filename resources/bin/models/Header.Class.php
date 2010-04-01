<?
class Header {
	private $styleDocuments;
	private $javascriptDocuments;
	private $classDocuments;
	private $title;
	private static $instance;

	private function __construct() {
		$this->styleDocuments = array();
		$this->javascriptDocuments = array();
	}

	public function registerCSSDocument( CSSDocument $css ) {
		if( !in_array($this->styleDocuments, $css ) ) {
			array_push( $this->styleDocuments, $css );
		}
	}

	public function registerJavacriptDocument( JavascriptDocument $js ) {
		if( !in_array($this->javascriptDocuments, $js ) ) {
			array_push( $this->javascriptDocuments, $js );
		}
	}

	public function registerClass( ClassDocument $class ) {
		if( !in_array($this->classDocuments, $class ) ) {
				array_push( $this->classDocuments, $class );
		}
	}

	public function renderHeader() {
	}

	private function buildCSSList() {
		$output = '';
		foreach( $this->styleDocuments as $css ) {
			$output .= "<link type='text/css' rel='stylesheet' href='{$css->path}{$css->file}' media='{$css->media}' />";
		}
		return $output;
	}

	private function buildJSList() {
		$output = '';
		foreach( $this->styleDocuments as $css ) {
			$output .= "<script type='text/javascript' src='{$js->path}{js->file}'></script>";
		}
		return $output;
	}

	private function buildClassList() {
	}

	public static function getInstance() {
		if( !isset( self::$instance ) ) {
			self::$instance = new Header();
		}
		return self::$instance;
	}
}

class DocumentType {
	private $file;
	private $path;

	public function __construct( $file, $path ) {
		$this->file = $file;
		$this->path = $path;
	}

	public function getFile() {
		return $this->file;
	}

	public function setFile( $file ) {
		$this->file = $file;
	}

	public function getPath() {
		return $this->path;
	}

	public function setPath( $path ) {
		$this->path = $path;
	}

	public function doesDocumentExistURL() {
		$hdrs = @get_headers($this->path . $this->file );
		return is_array($hdrs) ? preg_match('/^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$/',$hdrs[0]) : false;
	}

	public function doesDocumentExistFilePath() {
		return file_exists( $this->path . $this->file );
	}
}

class CSSDocument extends DocumentType {
	private $media;

	function __construct( $file, $path ) {
		parent::__construct( $file, $path );
	}

	public function getMedia() {
		return $this->media;
	}

	public function setMedia( $media ) {
		$this->media = $media;
	}
}

class JavascriptDocument extends DocumentType {
}

class ClassDocument extends Document {

}
?>