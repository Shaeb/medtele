<?
require_once( "resources/bin/constants.php");
add_required_class( 'Page.Class.php', MODEL );
session_start();

$application = new ApplicationSettings( "MedTeleNursing.AppSettings", ENVIRONMENT);
print_r($application->getSettings());
exit();
$pageName = $_REQUEST[ 'page' ];
$page = new Page( $pageName );
$page->process();
echo htmlspecialchars_decode( $page->output() );
?>