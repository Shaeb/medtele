<?
define(APP_PREFIX, "/medtele/");
define(	"APP_ADDRESS", 'http://' . $_SERVER['HTTP_HOST'] . APP_PREFIX);
define( "RESOURCES_PATH", APP_ADDRESS . 'resources/' );
define( "CONTROLLER_PATH", RESOURCES_PATH . 'bin/controllers/' );
define( "PAGE_PATH", RESOURCES_PATH . "templates/pages/" );
define( "APPSETTINGS_PATH", RESOURCES_PATH . "templates/appsettings/" );
define( "PAGE_EXTENSION", ".xml" );
define( "PAGE_STREAM_PATH", RESOURCES_PATH . "bin/pages/streams/" );
define( "PAGE_XSL_PATH", RESOURCES_PATH . "templates/pages/stylesheets/" );
define( "MODULE_PATH", RESOURCES_PATH . "templates/modules/" );
define( "MODULE_EXTENSION", ".xml" );
define( "MODULE_FILE_PATH", $_SERVER[ 'DOCUMENT_ROOT' ] . APP_PREFIX . "resources/bin/modules/" );
define( "MODULE_STREAM_PATH", RESOURCES_PATH . "bin/modules/streams/" );
define( "MODULE_XSL_PATH", RESOURCES_PATH . "templates/modules/stylesheets/" );
define( "OBJECT_PATH", APP_PREFIX . "resources/bin/models/" ); // so as to not mess up CLASS_PATH
define( "OBJECT_EXTENSION", ".php" );
define( "STYLE_PATH", RESOURCES_PATH . "css/" );
define( "STYLE_EXTENSION", ".css" );
define( "JAVASCRIPT_PATH", RESOURCES_PATH . "javascript/" );
define( "JAVASSCRIPT_EXTENSION", ".js" );

define("BIN_ROOT", $_SERVER["DOCUMENT_ROOT"] . APP_PREFIX . "resources/bin/");
define("BIN_MODEL_ROOT", BIN_ROOT . "models/" );
define("BIN_CONTROLLER_ROOT", BIN_ROOT . "controllers/" );
define("BIN_MODULE_ROOT", BIN_ROOT . "modules/" );
define("BIN_SCAFFOLD_ROOT", BIN_ROOT . "scaffold/" );

define( PAGE_TAG, "page" );
define( TITLE_TAG, "title" );
define( MODULE_TAG, "module" );
define( MODULE_NAME_ATTRIBUTE, "name" );
define( MODULES_TAG, "modules" );
define( MODULES_REPLACE_ATTRIBUTE, "replace" );
define( DEPENDENCY_TAG, "dependency" );
define( DEPENDENCY_TYPE_ATTRIBUTE_NAME, "type" );
define( DEPENDENCY_TYPE_CLASS_VALUE, "class" );
define( DEPENDENCY_TYPE_STYLE_VALUE, "style" );
define( DEPENDENCY_TYPE_SCRIPT_VALUE, "script" );
define( DEPENDENCY_TYPE_PARAMETER_VALUE, "parameter" );
define( AUTHENTICATED_ATTRIBUTE_NAME, "authenticated");
define( DATA_TAG, "data" );
define( DATA_TYPE_ATTRIBUTE_NAME, "type" );
define( DATA_TYPE_STREAM_VALUE, "Stream" );
define( DATA_TYPE_DYNAMICFILE_VALUE, "DynamicFile" );
define( DATA_TYPE_STATICFILE_VALUE, "StaticFile" );
define( DATA_TYPE_PROCESSOR_VALUE, "Processor" );
define( STYLESHEET_TAG, "stylesheet" );
define( TEMPLATE_PATH, 'http://' . $_SERVER['HTTP_HOST'] .  APP_PREFIX . 	"resources/templates/templates/" );
define( TEMPLATE_TAG, "template" );
define( REPLACE_TAG, "replace" );
define( REPLACE_TAG_REPLACE_ATTRIBUTE, "id" );
define( SCAFFOLDING_TEMPLATE_PATH, RESOURCES_PATH .  "templates/scaffolding/" );
define(SCAFFOLDING_TAG,"scaffolding");
define(SCAFFOLDING_TEMPLATES_TAG,"templates");
define(SCAFFOLDING_TEMPLATES_TABLE_ATTRIBUTE,"table");
define(SCAFFOLDING_FIND_TAG,"find");
define(SCAFFOLDING_ADD_TAG,"add");
define(SCAFFOLDING_DELETE_TAG,"delete");
define(SCAFFOLDING_UPDATE_TAG,"update");
define(SCAFFOLDING_LIST_TAG,"list");

define( IMAGE_ROOT, 'http://' . $_SERVER[ 'HTTP_HOST' ] . APP_PREFIX . 'resources/images/' );

define( DEBUGGING, 1 );
define( ENVIRONMENT, "development");
define( GLOBAL_ENVIRONMENT, "global" );
define(APPLICATION_SETTINGS_FILE, "MedTeleNursing.AppSettings");

require_once( BIN_ROOT . 'helpers/tools.php');
add_required_class( 'Document.Class.php', MODEL );
add_required_class( 'Application.Controller.php', CONTROLLER );
$application = ApplicationController::getInstance();
?>