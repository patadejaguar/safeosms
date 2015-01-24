<?php 
/**
 * Generador de Inversiones Ficticias
 **/
header("Content-type: text/x-csv");
//header("Content-type: text/csv");
//header("Content-type: application/csv");
header("Content-Disposition: attachment; filename=inversiones.unit." . date("Ymd") . ".csv");

$arrPlazo	= array(
			30,
			60,
			90,
			180,
			360);
$dia		= (24 * 60 * 60);
$Op			= array ("-", "-");
$BFecha		= date("Y-m-d");

//date("Y-m-d", strtotime("$fecha+$ndias day"));
for ( $i = 0; $i <= 100; $i++ ){
	$socio		= rand(100001, 100099 );
	$monto		= rand( 5000, 20000 );
	$plazo		= $arrPlazo[ rand(0, 4) ];
	$diffdays	= rand(30 , 120);
	if( intval($plazo) < intval($diffdays) ){
		$plazo	+= 30;
	}
	$mark		= "$BFecha" . $Op[ rand(0,1) ]  . $diffdays . " day";
	$fecha		= date("Y-m-d", strtotime($mark) );
	$tasa		= rand(4, 12) / 100;
	
	echo "$socio,$monto,$fecha,$plazo,$tasa,$i - Generado aleatoriamente\r\n";
}

?>