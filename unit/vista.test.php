<?php 
/**
 * Generador de Cuentas a la Vista Ficticias
 **/
header("Content-type: text/x-csv");
//header("Content-type: text/csv");
//header("Content-type: application/csv");
header("Content-Disposition: attachment; filename=vista.unit." . date("Ymd") . ".csv");

$arrPlazo	= array(
			7,
			14,
			30,
			60,
			90,
			180,
			360);
$dia		= (24 * 60 * 60);
$Op			= array ("-", "-");
$BFecha		= date("Y-m-d");
//socio - cuenta - fecha de apertura - producto - saldo - observaciones

for ( $i = 0; $i <= 100; $i++ ){
	$socio	= rand(100001, 100999 );
	$cuenta	= "10" . $socio . rand(10, 50);
	$monto	= rand( 5000, 20000 );
	$fecha	= date("Y-m-d", strtotime("$BFecha" . $Op[ rand(0,1) ]  . rand(0 , 30) . " day"));
	
	echo "$socio,$cuenta,$fecha,1,$monto,Generado aleatoriamente en $i\r\n";
}

?>