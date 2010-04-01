<?php
// database connection script
// created on 12/29/2003

// username, password, host, database
// this is the information for the admin, see database.inc for
// customer connection

class Connection{
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
			self::$instance = new Connection();
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