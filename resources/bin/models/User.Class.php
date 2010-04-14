<?
add_required_class( 'Application.Controller.php', CONTROLLER );
class User {
	var $username = '';
	var $password = '';
	var $userLevel = 2;
	var $lastLogin = null;
	var $isAuthenticated = false;

	function User( $username, $password ) {
		$this->username = $username;
		$this->password = $password;
		$this->isAuthenticated = false;
	}

	public function login( DatabaseConnection $connection ) {
		$this->isAuthenticated = false;

		if( $connection == null ) {
			return $this->isAuthenticated;
		}

		if( !$connection->isConnected ) {
			if( !$connection->Connect() ){
				return $this->isAuthenticated;
			}
		}

		$query = "select userId, username, userLevel from users where username='{$this->username}' and password='{$this->password}';";

		if( !$connection->query( $query ) ) {
			return $this->isAuthenticated;
		} 

		if( 1 > $connection->getNumRows() ) {
			//echo "user {$query}";
			//print_r($connection->getObject());
			return $this->isAuthenticated;
		}

		$user = $connection->getObject();

		// updated time logged in
		$lastLogin = date( 'Y-m-d H:i:s' );
		$ipAddress = $_SERVER['REMOTE_ADDR'];

		$query = "update users set lastLogin = '{$lastLogin}', ipaddress = '{$ipAddress}', isLoggedIn = 1 where userId={$user->userId}";

		if( !$connection->queryExecute( $query ) ){
			return $this->isAuthenticated;
		}

		$this->restoreUserFromUsername( $connection );
		return $this->isAuthenticated;
	}

	public function logout( DatabaseConnection $connection ) {
	}

	public function register( DatabaseConnection $connection ) {
		// updated time logged in
		$lastLogin = date( 'Y-m-d H:i:s' );
		$ipAddress = $_SERVER['REMOTE_ADDR'];
		$query = "insert into users( username, password, userLevel, lastLogin, isLoggedIn, ipaddress ) values( '{$this->username}', '{$this->password}', " .
			"{$this->userLevel}, '{$lastLogin}', true, '{$ipAddress}' );";

		if( !$connection->Connect() ){
			echo "error - not connected: " . $connection->getError();
			return false;
		}

		if( !$connection->queryExecute( $query ) ){
			echo "error executing query {$query}: " . $connection->getError();
			return false;
		}

		return $this->login( $connection );
	}
	
	public function restoreUserFromUsername( DatabaseConnection $connection ) {
		if(!isset($connection) || !isset($this->username)) {
			$this->isAuthenticated = false;
			return$this->isAuthenticated;
		}
		if( !$connection->isConnected ) {
			if( !$connection->Connect() ){
				return $this->isAuthenticated;
			}
		}

		$query = "select userId, username, userLevel, ipaddress, isLoggedIn, lastLogin from users where username='{$this->username}';";

		if( !$connection->query( $query ) ) {
			return $this->isAuthenticated;
		} 

		if( 1 > $connection->getNumRows() ) {
			//echo "user {$query}";
			//print_r($connection->getObject());
			return $this->isAuthenticated;
		}

		$user = $connection->getObject();
		$this->userLevel = $user->userLevel;
		$this->lastLogin = $user->lastLogin;
		$this->ipaddress = $user->ipaddress;
		$this->isAuthenticated = $user->isLoggedIn;
	} 

	function update( DatabaseConnection $connection ) {
	}

	function delete( DatabaseConnection $connection ) {
	}

	function serialize() {
	}

	function serializeAsXML() {
	}
};
?>