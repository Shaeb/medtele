<?php
class Order {
	private $orderId;
	private $orderType;
	private $orderDescription;
	private $patientId;
	private $enteredTime;
	
	function __constructor() {
		
	}
	
	public function setOrderId( $orderId ) {
		$this->orderId = $orderId;
	}
	
	public function getOrderId() {
		return $this->orderId;
	}
	
	public function setOrderType( $OrderType ) {
		$this->orderType = $OrderType;
	}
	
	public function getOrderType() {
		return $this->orderType;
	}
	
	public function setOrderDescription( $OrderDescription ) {
		$this->orderDescription = $OrderDescription;
	}
	
	public function getOrderDescription() {
		return $this->orderDescription;
	}
	
	public function setPatientId( $patientId ) {
		$this->patientId = $patientId;
	}
	
	public function getPatientId() {
		return $this->patientId;
	}
	
	public function setEnteredTime( $EnteredTime ) {
		$this->enteredTime = $EnteredTime;
	}
	
	public function getEnteredTime() {
		return $this->enteredTime;
	}
}
?>