<?php
require_once( 'Connection.php' );
require_once( 'Patient.php' );

class MAR {
	private $medicationList;
	private $medicationIdList;
	private $medicationQuery;
	private $patientId;
	
	public function __construct( $patientId ){
		$this->patientId = $patientId;
		$this->medicationQuery = "select m.medicationId, m.genericName, m.commonDose, m.route, m.unit, ml.active, ml.confirmed, ml.userId as 'confirmedBy' from medication as m, medicationList as ml where ml.medicationId = m.medicationId and ml.patientId = {$this->patientId};";
		$this->medicationList = array();	
	}
	
	public function load( Connection $connection ) {
		if( !$connection->isConnected ) {
			$connection->Connect();
		}
		if( !$connection->query($this->medicationQuery) ) {
			return false;
		} else {
			$result = null;
			$medication = null;
			while( $result = $connection->getObject()) {
				$medication = new Medication();
				$medication->setCommonDose( $result->commonDose );
				$medication->setGenericName( $result->genericName );
				$medication->setRoute( $result->route );
				$medication->setUnit( $result->unit );
				$medication->setMedicationID( $result->medicationId );
				$medication->setActive($result->active);
				$medication->setConfirmed($result->confirmed);
				$medication->setConfirmedBy($result->confirmedBy);
				array_push( $this->medicationList, $medication );
			}
		}
		return true;
	}
	
	public function getMedicationList(){
		return $this->medicationList;
	}
}

class Medication {
	private $genericName;
	private $commonDose;
	private $route;
	private $unit;
	private $medicationId;
	private $active;
	private $confirmed;
	private $confirmedBy;
	
	public function getGenericName() {
		return $this->genericName;
	}
	
	public function setGenericName( $genericName ) {
		$this->genericName = $genericName;
	}
	
	public function getCommonDose() {
		return $this->commonDose;
	}
	
	public function setCommonDose( $CommonDose ) {
		$this->commonDose = $CommonDose;
	}
	
	public function getRoute() {
		return $this->route;
	}
	
	public function setRoute( $Route ) {
		$this->route = $Route;
	}
	
	public function getUnit() {
		return $this->unit;
	}
	
	public function setUnit( $Unit ) {
		$this->unit = $Unit;
	}
	
	public function getMedicationID(){
		return $this->medicationId;
	}
	
	public function setMedicationID($medicationId) {
		$this->medicationId = $medicationId;
	}
	
	public function getActive(){
		return $this->active;
	}
	
	public function setActive($Active) {
		$this->active = $Active;
	}
	
	public function getConfirmed(){
		return $this->confirmed;
	}
	
	public function setConfirmed($Confirmed) {
		$this->confirmed = $Confirmed;
	}
	
	public function getConfirmedBy(){
		return $this->confirmedBy;
	}
	
	public function setConfirmedBy($ConfirmedBy) {
		$this->confirmedBy = $ConfirmedBy;
	}
	
	public function toXML() {
		$output = '<medication>';
		foreach( $this as $key => $value ) {
			$output .= "<{$key}>{$value}</{$key}>";
		}
		$output .= '</medication>';
		return $output;
	}
}
?>