<?
require_once( "../../models/Census.php" );
require_once( "../../models/Patient.php" );

$census = new Census( 'MSU' );
$census->populateCensus( Connection::getInstance() );
$patients = $census->getPatients();
/*********************************************8
$dom = new DOMDocument("1.0");
$xml = $dom->createElement( "module" );
$dom->appendChild( $xml );
$name = $dom->createAttribute( "name" );
$value = $dom->createTextNode( "PatientModuleList" );
$name->appendChild( $value );
$xml->appendChild( $name );

$patientNode = null;
$firstName = null;
$lastName = null;
$value = null;

foreach( $patients as $patient ) {
	$patientNode = $dom->createElement( "patient" );

	$firstName = $dom->createElement( "firstName" );
	$value = $dom->createTextNode( $patient->getFirstName() . " " );
	$firstName->appendChild( $value );

	$lastName = $dom->createElement( "lastName" );
	$value = $dom->createTextNode( $patient->getLastName() );
	$lastName->appendChild( $value );

	$patientNode->appendChild( $firstName );
	$patientNode->appendChild( $lastName );

	$xml->appendChild( $patientNode );
}
**************************************************/
$output = '<module>';
foreach( $patients as $patient ) {
	$output .= $patient->toXML();
}
$output .= '</module>';
header("Content-type: application/xml");
//echo $dom->saveXML();
echo $output;
?>