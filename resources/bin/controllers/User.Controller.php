<?
// processor: user.processor.php
// purpose: create a valid user
// version 1.0 - created 04/13/2009

require_once( '../constants.php' );
add_required_class( 'Connection.Class.php', MODEL );
add_required_class( 'User.Class.php', MODEL );
add_required_class( 'Application.Controller.php', CONTROLLER );
// already a part of the app, here for doc ...

class UserController {
	private static $instance;
	private $username;
	private $password;
	private $submitted;
	private $userLevel;
	private $user;
	private $session;
	
	private function __construct(){
		$message = $_REQUEST['message'];
		$this->username = $_REQUEST[ "username_{$message}" ];
		$this->password = $_REQUEST[ "password_{$message}" ];
		$this->userLevel = $_REQUEST[ 'userLevel' ];
		$this->submitted = $_REQUEST[ 'submit' ];
		
		if( $this->submitted == null || $this->submitted == '' ) {
			$this->submitted = false;
			echo "error: not submitted: - " . print_r($_REQUEST);
			exit();
		} else {
			$this->submitted = true;
			$this->user = new User( $this->username, $this->password );
		}
	}
	
	public static function getInstance() {
		if( !isset( self::$instance ) ) {
			self::$instance = new UserController();
		}
		return self::$instance;
	}
	
	public function registration( Connection $connection ) {
		if( null == $connection || null == $this->user ) {
			echo "connection null";
			return;
		}
		$this->user->register( $connection );
		$logged_in = $this->user->login( $connection );
		if ( 1 == $logged_in ) {
			$this->session = SessionController::getInstance();
			$this->session->setMessage( "Beinvenido {$this->user->username}");
			$this->session->start( $this->user );
		} else {
			
		echo "will not log in: {$logged_in}";
		exit();
		}
	}
	
	public function login( Connection $connection ) {
		if( null == $connection || null == $this->user ) {
			echo "something null";
			return;
		}
		if ( $this->user->login( $connection ) ) {
			$this->session = SessionController::getInstance();
			$this->session->setMessage( "Welcome back {$this->user->username}");
			$this->session->start( $this->user );
		} else {
			echo "not logged in";
			print_r( $this->user);
		}
	}
}

$application = ApplicationController::getInstance();

$userProcessor = UserController::getInstance();

$message = $_REQUEST[ 'message'];

switch( $message ) {
	case 'registration':
		$userProcessor->registration( $application->connection );
		break;
	case 'login':
		$userProcessor->login(  $application->connection );
		break;
	default:
		break;
}
?>