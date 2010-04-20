<?php
define( MODEL, "MODEL");
define( CONTROLLER, "CONTROLLER");
define( MODULE, "MODULE");
define( SCAFFOLD, "SCAFFOLD");

function send_to( $page ) {
	header("Location: " . APP_ADDRESS . "page/{$page}");
}

function add_required_class( $class, $type = '') {
	$root = '';
	switch($type){
		case MODEL:
			$root = BIN_MODEL_ROOT;
			break;
		case CONTROLLER:
			$root = BIN_CONTROLLER_ROOT;
			break;
		case MODULE:
			$root = BIN_MODULE_ROOT;
			break;
		case SCAFFOLD:
			$root = BIN_SCAFFOLD_ROOT;
			break;
		default:
			$root = BIN_ROOT;
			break;
	}
	require_once( $root . $class );
}
?>