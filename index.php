<?
ob_start();
require_once( "resources/bin/constants.php");
add_required_class( 'Page.Class.php', MODEL );
//$application = new ApplicationSettings( "MedTeleNursing.AppSettings", ENVIRONMENT);
$pageName = $_REQUEST[ 'page' ];
$page = new Page( $pageName );
$page->process();
echo htmlspecialchars_decode( $page->output() );
echo ob_get_clean();
?>