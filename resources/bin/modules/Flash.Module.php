<?php
$data = '';
$flash = $_SESSION[ 'flash' ];
if( isset($_SESSION['flash'] ) ) {
	$data = "<p>{$flash}</p>";
} else {
	$data = print_r( $_SESSION );
}
echo $data;
?>