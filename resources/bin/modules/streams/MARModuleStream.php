<?php
require_once( '../../models/Connection.php' );
require_once( '../../models/MAR.php' );

$patientId = $_REQUEST[ 'patientId' ];

$MAR = new MAR( $patientId );

$MAR->load( Connection::getInstance());
$medications = $MAR->getMedicationList();
$output = '<module>';
$output .= "<patientId>{$patientId}</patientId>";
foreach( $medications as $medication ) {
	$output .= $medication->toXML();
}
$output .= '</module>';

header( 'Content-type: application/xml' );
echo $output;
?>