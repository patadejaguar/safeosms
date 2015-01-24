<?php
include_once("unit.utils.inc.php");
/**
 * Generador de Cuentas a la Vista Ficticias
 **/
header("Content-type: text/x-csv");
//header("Content-type: text/csv");
//header("Content-type: application/csv");
header("Content-Disposition: attachment; filename=creditos.unit." . date("Ymd") . ".csv");

$arrPlazo	= array(
			7,
			15,
			30
			);
$arrAbonos	= array(
			0,
			100,
			200,
			500,
			1000
			);
$dia		= (24 * 60 * 60);
$Op			= array ("-", "-");
$BFecha		= date("Y-m-d");
//socio - cuenta - fecha de apertura - producto - saldo - observaciones
echo "CODPERSONA,CODCREDITO,CLAVEPRODUCTO,MONTOMINISTRADO,FECHAMINISTRACION,FECHAVENCIMIENTO,NUMPAGOS,PERIOCIDADPAGO,SALDOACTUAL,FECHAULTIMOABOCAP,FI\r\n";

for ( $i = 0; $i <= 100; $i++ ){
	$socio			= rand(100001, 100999 );
	$credito		= "12" . $socio . rand(10, 50);
	$monto			= rand( 5000, 9500 );
	$fechaMin		= date("Y-m-d", strtotime("$BFecha" . $Op[ rand(0,1) ]  . rand(60 , 260) . " day"));
	$periocidad		= $arrPlazo[ rand(0, 2) ];
	$pagos			= rand(10,20 );
	$DiasCredito	= ($pagos *  $periocidad);
	$vencimiento	= unit_SumarDias($fechaMin, $DiasCredito ); 
	$producto		= 13; //Credito Personal
	
	$saldo			= $monto - $arrAbonos[ rand(0, 4) ];
	$fechaUltOp		= unit_RestarDias($vencimiento, rand( $periocidad, ($DiasCredito - $periocidad) ) );
	if(strtotime($fechaUltOp) > ( strtotime( date("Y-m-d"))  ) ){
		$fechaUltOp		= date("Y-m-d");
	}
	echo "$socio,$credito,$producto,$monto,$fechaMin,$vencimiento,$pagos,$periocidad,$saldo,$fechaUltOp,0\r\n";
}

?>