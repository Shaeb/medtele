<?php
require_once( "Connection.php" );

class Profile{
	var $username = "";
	var	$conn;
	var	$results;
	var	$query = "";
	var	$error;
	var	$results;
	var	$type;
	function Profile( $username, $type = "mediarequest" ){
		$this->username = $username;
		$this->conn = Connection::getInstance();
		$this->type = $type;
	}
	function GetType(){
		return $this->type;
	}
	function SetType( $type ){
		$this->type = $type;
	}
	function Build(){
		switch( $this->type ){
			case "mediarequest":
				$this->query = "select filename as 'name', file_name as 'file', active " . 
				"from media m, mediarequest r where m.mediaid = r.mediaid and " . 
				"r.username = '{$this->username}';";				
				break;
			default:
				break;
		}
		if( !$conn->Connect() ){
			$this->error = $conn->getError();
			return false;
		}
		if( !$conn->query( $query ) ){
			$this->error = $conn->getError();
			return false;
		}
		return true;
	}
	function Display(){
	$html = "";
		switch( $this->type ){
			case "mediarequests":
				$html = DisplayMediaRequests();
				break;
			default:
				break;
		}
		return $html;
	}
	function DisplayMediaRequests(){
		$html =
			"<table><tr><td valign=\"top\"><a href=\"#\" onclick=\"toggle(mediareqs);\">Your Requests</a></td></tr>" . 
			"<tr><td valign=\"top\"><div id=\"mediareqs\" style=\"display: none\"><table>";
		while( $media = $conn->getObject() ){
			$html .= "<tr><td valign=\"top\">";
			if( $media->active == 1 ){
				$html .= "<a href=\"{$media->file}\">{$media->name}</a>";
			}
			else{
				$html .= "{$media->name}";
			}
			$html .= "</td></tr>";
		}
		$html .= "</table></div></td></tr></table>";
		return $html;
	}
}
?>