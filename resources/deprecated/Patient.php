<?
require_once('Connection.php');

class Patient {
	private $patientId = 0;
	private $firstName ='';
	private $lastName = '';
	private $roomNumber = 0;
	private $unitName = '';

	function __construct( $firstName, $lastName ) {
		$this->firstName = $firstName;
		$this->lastName = $lastName;
	}

	function getFirstName() {
		return $this->firstName;
	}

	function setFirstName( $firstName ) {
		$this->firstName = $firstName;
	}

	function getLastName() {
		return $this->lastName;
	}

	function setLastName( $lastName ) {
		$this->lastName = $lastName;
	}

	function getRoomNumber() {
		return $this->roomNumber;
	}

	function setRoomNumber( $roomNumber ) {
		$this->roomNumber = $roomNumber;
	}

	function getUnitName() {
		return $this->unitName;
	}

	function setUnitName( $unitName ) {
		$this->unitName = $unitName;
	}
	
	function setPatientID( $patientId ) {
		$this->patientId = $patientId;
	}
	
	function getPatientID() {
		return $this->patientId;
	}

	function __toString() {
		return $this->lastName . ', ' . $this->firstName;
	}

	function toXML() {
		$output = '<patient>';
		foreach( $this as $key => $value ) {
			$output .= "<{$key}>{$value}</{$key}>";
		}
		$output .= '</patient>';
		return $output;
	}
};
?>