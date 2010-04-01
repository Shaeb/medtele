<?php
require_once( "../models/Connection.php" );

$connection = Connection::getInstance();
$userId = $_REQUEST[ 'userId' ];
$patientId = $_REQUEST[ 'patientId' ];
$medications = explode( ',', $_REQUEST[ 'medications' ] );
$queries = array();
$error = false;
$results = array();

foreach( $medications as $medication ) {
	$query = "insert into AdministeredMedications( administeredBy, administeredTo, administeredMedication, administrationTime ) values( {$userId}, {$patientId}, {$medication}, NOW() );";
	array_push( $queries, $query );
}

$i = 0;
$name = '';

if( $connection == null ) {
	$resultsFound = false;
}

if( !$connection->isConnected ) {
	if( $connection->Connect() ){
		foreach( $queries as $query ) {
			array_push( $results, $connection->queryExecute( $query ) );
		}
	}
}

$xml = '<results>';
$i = 0;
$length = count( $medications );
for( $i; $i < $length; ($i++)) {
	$xml .= "<result><medication>{$medications[ $i ]}</medication><success>{$results[ $i ]}</success></result>";
}
$xml .= '</results>';

header("Content-type: application/xml");
echo $xml;
?>