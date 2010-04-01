<?
require_once( "../models/Connection.php" );

$connection = Connection::getInstance();
$term = $_REQUEST[ 'input' ];
$query = "select distinct genericName, commonDose, unit from medication where genericName like '{$term}%';";
$resultsFound = true;
$xml = '<results>';
$i = 0;
$name = '';

if( $connection == null ) {
	$resultsFound = false;
}

if( !$connection->isConnected ) {
	if( !$connection->Connect() ){
		$resultsFound = false;
	}
}

if( !$connection->query( $query ) ) {
	$resultsFound = false;
}

if( 0 == $connection->numRows ) {
	$resultsFound = false;
}

if ( $resultsFound ) {
	while( $results = $connection->getObject() ) {
		$i++;
		$name = addslashes( $results->genericName );
		$xml .= "<rs id='{$i}' info='{$name} {$results->commonDose}{$results->unit}'>{$name}</rs>";
	}
} else {
	$xml .= "<rs id='1' info='null_set'>{$query}</rs>";
}

$xml .= '</results>';
header("Content-type: application/xml");
echo $xml;
?>