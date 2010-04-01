<?
require_once('Connection.php');
require_once('Patient.php');

class Census {
	private $patientList = null;
	private $unitName = null;
	private $unitNumber = null;

	function Census( $unitName ) {
			$this->unitNumber = 0;
			$this->unitName = $unitName;
	}

	function populateCensus( Connection $connection ) {
		$this->create( $connection );
	}

	function addPatientToCensus() {
	}

	public function create( Connection $connection ) {
		$query = "select patientId, firstName, lastName, roomNumber, unitName from Patient where unitName = '{$this->unitName}';";

		if( !$connection->Connect() ){
			echo "error: " . $connection->getError();
			return false;
		}

		if( !$connection->query( $query ) ){
			echo "error: " . $connection->getError();
			return false;
		}

		$this->patientList = array();

		while( $result = $connection->getObject() ) {
			$patient = new Patient( $result->firstName, $result->lastName );
			$patient->setPatientID( $result->patientId );
			$patient->setRoomNumber( $result->roomNumber );
			$patient->setUnitName( $result->unitName );
			array_push( $this->patientList, $patient );
		}
	}

	function update( $connection ) {
	}

	function delete( $connection ) {
	}

	function serialize() {
	}

	function serializeAsXML() {
	}

	function getPatients() {
		return $this->patientList;
	}
};
?>