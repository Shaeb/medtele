<?
add_required_class( 'Header.Class.php', MODEL );
$baseURL = 'http://' . $_SERVER['HTTP_HOST'] . '/chart/';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
	<head>
	<title>Magic: The Gathering Collection - Concept Huge</title>
		<link rel="stylesheet" title="Standard" href="<? echo $baseURL; ?>resources/css/screen2.css" type="text/css" media="screen" />
		<link rel="stylesheet" href="<? echo $baseURL; ?>resources/css/bubbles.css" type="text/css" media="screen" charset="utf-8" />
		<link rel="stylesheet" href="<? echo $baseURL; ?>resources/css/menu.css" type="text/css" media="screen" charset="utf-8" />
		<link type="text/css" rel="stylesheet" href="<? echo $baseURL; ?>resources/css/lightbox.css" media="screen" />
		<link type="text/css" rel="stylesheet" href="<? echo $baseURL; ?>resources/css/autosuggest_inquisitor.css" media="screen" />
		<script type="text/javascript" src="<? echo $baseURL; ?>resources/javascript/wow.js"></script>
		<script type="text/JavaScript" src="<? echo $baseURL; ?>resources/javascript/rounded_corners.inc.js"></script>
		<script type="text/JavaScript" src="<? echo $baseURL; ?>resources/javascript/bsn.AutoSuggest_c_2.0.js"></script>
		<script type="text/JavaScript" src="<? echo $baseURL; ?>resources/javascript/Application.js"></script>
	</head>
	<body>
		<div id="header">
		<h1>Patient Name</h1>
		<ul id="nav">
			<li><a href="kardex.php" class="flyoutMenu" menu="kardexMenu">Kardex</li>
			<li><a href="mar.html">MAR</a></li>
			<li><a href="#">Labs</a></li>
			<li><a href="#">Studies</a></li>
			<li><a href="#">Orders</a></li>
			<li><a href="#">Assessments</a></li>
			<li><a href="#">Notes</a></li>
		</ul>
<!--
		<ul id="subnav">
			<li><a href="#">Kardex</li>
			<li><a href="mar.html">MAR</a></li>
			<li><a href="#">Labs</a></li>
			<li><a href="#">Studies</a></li>
			<li><a href="#">Orders</a></li>
			<li><a href="#">Assessments</a></li>
			<li><a href="#">Notes</a></li>
		</ul>
-->
			<ul id="kardexMenu" class="hide">
				<li><a href="kardex.php">Kardex</li>
				<li><a href="mar.html">MAR</a></li>
				<li><a href="#">Labs</a></li>
				<li><a href="#">Studies</a></li>
				<li><a href="#">Orders</a></li>
				<li><a href="#">Assessments</a></li>
				<li><a href="#">Notes</a></li>
			</ul>
		</div>
<div id="wrapper">
	<div id="content">