<?php
$order = $_GET["o"];
function jstest() {
	$vFecha = date("Y-m-d-h");
	return "function jtest(){ alert (\"$vFecha y $order\");	}	";
}
?>