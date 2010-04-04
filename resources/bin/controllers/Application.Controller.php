<?php
//add_required_class( 'Connection.Class.php', MODEL );
add_required_class( 'User.Class.php', MODEL );
add_required_class( 'Document.Class.php', MODEL );
// tools already a part of the app
//require_once( 'resources/bin/helpers/tools.php' );

class ApplicationController {
	public $isLoggedIn;
	private $user;
	private  $connection;
	public  $session;
	private $appSettings;
	public $registrationPage;
	private static  $instance;
	
	private function __construct(){
		$this->connection = DatabaseConnection::getInstance();
		$this->session = SessionController::getInstance();
		$this->appSettings = new ApplicationSettings(APPLICATION_SETTINGS_FILE, ENVIRONMENT);
		$username = '';
		if( isset($_SESSION['username'])){
			$username = $_SESSION['username'];
		}
		$this->user = new User($username,'');
		if($this->session->isSessionAuthenticated()){
			$this->user->restoreUserFromUsername( $this->connection );
		}
	}
	
	public static function getInstance() {
		if( !isset( self::$instance ) ) {
			self::$instance = new ApplicationController();
		}
		return self::$instance;
	}
	
	public function getUser(){
		return $this->user;
	}
	
	public function getDatabaseConnection(){
		$settings = $this->appSettings->getSettingsFor("database");
		if(isset($settings)){
			if(array_key_exists("username",$settings)){
				$this->connection->setUsername($settings["username"]);
			}
			if(array_key_exists("password",$settings)){
				$this->connection->setPassword( $settings["password"]);
			}
			if(array_key_exists("hostname",$settings)){
				$this->connection->setHost( $settings["hostname"]);
			}
			if(array_key_exists("databasename",$settings)){
				$this->connection->setDatabase( $settings["databasename"]);
			}
		}
		return $this->connection;
	}
	
	public function isPageGated($pageName){
		$isGated = false;
		$settings = $this->appSettings->getSettingsFor("security");
		if(isset($settings)){
			if(array_key_exists("gatedPages",$settings)){
				$isGate = in_array($pageName,$settings["gatedPages"]);
			}
		} 
		return $isGated;
	}
	
	public function enforceGate(){
		$settings = $this->appSettings->getSettingsFor("security");
		if(isset($settings)){
			if(array_key_exists("registration",$settings)){
				if(array_key_exists("defaultPage",$settings["registration"])){
					send_to( $settings["registration"]["defaultPage"] );
				}
			}
		} 
	}
}


class SessionController {
	private static $instance;
	private $sessionHasBegun;
	private $message;
	
	private function __contruct() {
		$this->sessionHasBegun = false;
	}
	
	public static function getInstance() {
		if( !isset( self::$instance ) ) {
			self::$instance = new SessionController();
		}
		return self::$instance;
	}
	
	public function setMessage( $message ) {
		if(isset($message)){
			$this->message = $message;
		}
	}
	
	public function start(){
		if(!$this->sessionHasBegun){
			$this->sessionHasBegun = true;
			session_start();
		}
	}
	
	public function setupAuthorizedSession( User $user ) {
		//session_name( $user->username );
		session_start();
		if( isset( $this->message ) ) {
			$_SESSION[ 'flash' ] = $this->message;
		}		
		$_SESSION[ 'username' ] = $user->username;
		$_SESSION[ 'is_logged_in' ] = ( 1 == $user->isAuthenticated ) ? true : false;
		//session_write_close();
		
		if( $_REQUEST[ 'redirect'] ) {
			send_to( $_REQUEST[ 'redirect'] );
		}
	}
	
	public function isSessionAuthenticated() {
		$isAuthenticated = false;
		if(isset($_SESSION['is_logged_in']) && 'true' == $_SESSION['is_logged_in']){
			$isAuthenticated = true;
		}
		return $isAuthenticated;
	}
}

class ApplicationSettings extends Document {
	private $environment;
	private $url;
	private $appSettingsFileName;
	private $tags;
	private $settings;
	private $environmentRootNode;
	public  $isLoaded;
	//private static $instance; 

	public function __construct($appSettingsFileName, $environment) {
		$this->reload($appSettingsFileName, $environment);
	}
	
	public function reload($appSettingsFileName, $environment){
		if( isset( $appSettingsFileName ) && isset($environment) ) {
			$this->appSettingsFileName = $appSettingsFileName;
			$this->url = APPSETTINGS_PATH . $this->appSettingsFileName . PAGE_EXTENSION;
			if( $this->urlExists( $this->url ) ) {
				$this->preserveWithWhiteSpace = false;
				$this->log( "<!-- loading page $this->url -->" );
				$this->isLoaded = $this->load( $this->url, LIBXML_NOBLANKS );
				$this->settings = array();
				$nodes = $this->getElementsByTagName( $environment );
				if(isset($nodes) && 1 <= $nodes->length){
					$this->environmentRootNode = $nodes->item(0);
					$this->settings = $this->processSettings( $nodes );
				}
			} else {
				$this->log( "url not found: $this->url." );
			}
		}
		//print_r($this->settings);
		//exit();
	}
	private function processSettings( $nodes ){
		$map = array();
		foreach( $nodes as $node ){
			if($node->hasChildNodes() && ($node->firstChild instanceof DOMText)){
				if(1 == $this->getNumberOfTagsByName($node->nodeName)) {
					$map[$node->nodeName] = $node->nodeValue;
				} else {
					$map[] = $node->nodeValue;
				}
			} else {
				$map[$node->nodeName] = $this->processSettings($node->childNodes)	;
			}	
		}
		return $map;
	}
	
	public function getNumberOfTagsByName($tagName){
		$numberOfTags = 0;
		if(isset($tagName)){
			$nodes = $this->query($tagName,$this->environmentRootNode);
			if(isset($nodes)){
				$numberOfTags = $nodes->length;
			}
		}
		return $numberOfTags;
	}
	public function getSettings(){
		return $this->settings;
	}
	public function getSettingsFor($target){
		if(array_key_exists($target,$this->settings[ENVIRONMENT])){
			return $this->settings[ENVIRONMENT][$target];
		} else {
			return null;
		}
	}
	
	/**
	public static function getInstance($appSettingsFileName, $environment) {
		if( !isset( self::$instance ) ) {
			self::$instance = new ApplicationSettings();
			self::$instance->reload($appSettingsFileName, $environment);
		}
		return self::$instance;
	}
	**/
}

class DatabaseConnection{
	private $username = "";
	private $password = "";
	private $database = "";
	private $host = "";
	private static $instance;
	
	// database link
	private $dblink = "";
	
	// errors
	private $error = "";
	public $isConnected = false;
	
	// result set
	private $results = "";
	private $numRows;
	
	// connect to the mysql server
	// test for errors
	// if error, store error info
	
	private function __construct(){
	}
	
	public static function getInstance() {
		if( !isset( self::$instance ) ) {
			self::$instance = new DatabaseConnection();
		}
		return self::$instance;
	}
	
	function Connect(){
		$this->isConnected = false;
		if( !( $this->dblink = mysql_pconnect( $this->host, $this->username, $this->password ) ) )
		{
			// failure
			$this->error = mysql_error();
			return $this->isConnected;
		}
	
		if( !mysql_select_db( $this->database, $this->dblink ) )
		{
			$this->error = mysql_error();
			return $this->isConnected;
		}
		$this->isConnected = true;
		return $this->isConnected;
	}
	
	// connect to the database
	// test for errors
	// if error, display error information with admin email
	function query($query){
		if( !( $this->results = mysql_query( $query) ) )
		{
			// failure
			$this->error = mysql_error();
			return false;
		}
		$this->numRows = mysql_num_rows( $this->results );
		return true;
	}
	
	function queryExecute($query){
		if( !( mysql_query( $query) ) )
		{
			// failure
			$this->error = mysql_error();
			return false;
		}
		return true;
	}
	
	function setUsername( $user ){
		$this->username = $user;
	}
	
	function getUsername(){
		return $this->username;
	}
	
	function setPassword( $pass ){
		$this->password = $pass;
	}
	
	function getPassword(){
		return $this->password;
	}
	
	function setHost( $host ){
		$this->host = $host;
	}
	
	function getHost(){
		return $this->host;
	}
	
	function setDatabase( $db ){
		$this->database = $db;
	}
	
	function getDatabase(){
		return $this->database;
	}
	
	function getConnection(){
		return $this->dblink;
	}
	
	function getError(){
		return $this->error;
	}
	
	function getResults(){
		return $this->results;
	}
	
	function getNumRows(){
		return $this->numRows;
	}
	
	function getObject(){
		return mysql_fetch_object( $this->results );
	}
	function getID(){
		return mysql_insert_id( $this->dblink );
	}
}
?>