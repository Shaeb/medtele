<?
// processor: createUser.php
// purpose: create a valid user
// version 1.0 - created 04/13/2009

require_once( "../models/Connection.php" );
require_once( "../models/User.php" );


$username = $_REQUEST[ 'username' ];
$password = $_REQUEST[ 'password' ];
$redirect = $_REQUEST[ 'redirect' ];
$submitted = $_REQUEST[ 'submit' ];

if( $submitted == null || $submitted == '' ) {
	echo "error: not submitted: - " . $submitted;
	exit();
}

$connection = Connection::getInstance();

$user = new User( $username, $password );

if ( !$user->login( $connection ) ) {
	echo "error, please try again: - {$connection->error}";
	exit();
}

header( "Location: /chart/{$redirect}.php" );
?>