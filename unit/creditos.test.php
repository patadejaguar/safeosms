<?php
include_once("unit.utils.inc.php");
/**
 * Generador de Cuentas a la Vista Ficticias
 **/
header("Content-type: text/x-csv");
//header("Content-type: text/csv");
//header("Content-type: application/csv");
header("Content-Disposition: attachment; filename=creditos-unit-" . date("Ymd") . ".csv");

$arrDestino	= array(
		"COMERCIAL",
		"CONSUMO",
		"VIVIENDA"
);

$arrPlazo	= array(
			7,
			15,
			30
			);

$arrPlazoL	= array(
		7 => "S",
		15 => "Q",
		30 => "M"
);

$arrAbonos	= array(
			0,
			100,
			200,
			500,
			1000
			);
$arrTasa	= array(
		20,25,30,35,40,45,50,55,60
);

$dia		= (24 * 60 * 60);
$Op			= array ("-", "-");

$BFecha		= date("Y-m-d");
$divisor	= "|";
$sucursal	= 'matriz';

//socio - cuenta - fecha de apertura - producto - saldo - observaciones
/*
SUCURSAL|CLAVE CLIENTE|AP PAT CLIENTE|AP MAT CLIENTE|NOMBRE CLIENTE|FECHA SOLICITUD|PLAZO|PERIODO|MONTO ORIGINAL|DESTINO DEL CREDITO|FECHA DE DESEMBOLSO|SALDO ACTUAL|FECHA ULTIMO PAGADO|TIPO DE CREDITO|NUMERO DE PAGO ACTUAL|TASA|MONTO DE LA CUOTA|CLAVE DE CREDITO
|1901850||||21/04/2016|12|M|81000|PAG DE DEUDA|21/04/2016|81000||EJEMPLO||33.6||
|1901850||||21/04/2016|24|M|81000|PAG DE DEUDA|21/04/2016|81000||EJEMPLO||33.6||
|1901850||||21/04/2016|12|M|81000|PAG DE DEUDA|21/04/2016|81000||EJEMPLO||30||
|1901850||||21/04/2016|24|M|81000|PAG DE DEUDA|21/04/2016|81000||EJEMPLO||30||
 */

//echo "CODPERSONA,CODCREDITO,CLAVEPRODUCTO,MONTOMINISTRADO,FECHAMINISTRACION,FECHAVENCIMIENTO,NUMPAGOS,PERIOCIDADPAGO,SALDOACTUAL,FECHAULTIMOABOCAP,FI\r\n";
echo "SUCURSAL|CLAVE CLIETE|AP PAT CLIENTE|AP MAT CLIENTE|NOMBRE CLIENTE|FECHA SOLICITUD|PLAZO|PERIODO|MONTO ORIGINAL|DESTINO DEL CREDITO|FECHA DE DESEMBOLSO|SALDO ACTUAL|FECHA ULTIMO PAGADO|TIPO DE CREDITO|NUMERO DE PAGO ACTUAL|TASA|MONTO DE LA CUOTA|CLAVE DE CREDITO\r\n";




for ( $i = 0; $i <= 100; $i++ ){
	//10000
	$socio			= rand(10001, 10999 );
	$credito		= "12" . $socio . rand(10, 50);
	$monto			= rand( 5000, 50000 );
	$fechaMin		= date("Y-m-d", strtotime("$BFecha" . $Op[ rand(0,1) ]  . rand(60 , 260) . " day"));
	$periocidad		= $arrPlazo[ rand(0, 2) ];
	$periocidadL	= $arrPlazoL[ $periocidad ];
	$destino		= $arrDestino[ rand(0,2) ];
	$tipo			= $arrDestino[ rand(0,2) ];
	$tasa			= $arrTasa[ rand(0,8) ];
	$pagos			= rand(1,20 );
	$DiasCredito	= ($pagos *  $periocidad);
	
	$producto		= 13; //Credito Personal
	$pagoactual		= 0;
	$cuota			= 0;
	$id				= $credito;
	
	$vencimiento	= unit_SumarDias($fechaMin, $DiasCredito );
	
	$saldo			= $monto - $arrAbonos[ rand(0,4) ];
	
	
	$fechaUltOp		= unit_RestarDias($vencimiento, rand( $periocidad, ($DiasCredito - $periocidad) ) );
	
	if(strtotime($fechaUltOp) > ( strtotime( date("Y-m-d"))  ) ){
		$fechaUltOp		= date("Y-m-d");
	}
	//"SUCURSAL|CLAVE CLIENTE|AP PAT CLIENTE|AP MAT CLIENTE|NOMBRE CLIENTE|FECHA SOLICITUD|PLAZO|PERIODO|MONTO ORIGINAL|DESTINO DEL CREDITO|FECHA DE DESEMBOLSO|
	//SALDO ACTUAL|FECHA ULTIMO PAGADO|TIPO DE CREDITO|NUMERO DE PAGO ACTUAL|TASA|MONTO DE LA CUOTA|CLAVE DE CREDITO";

	$linea			= $sucursal . $divisor . $socio . $divisor . "" . $divisor. "" . $divisor . "" . $divisor . $fechaMin . $divisor . $pagos . $divisor . $periocidadL . $divisor . $monto . $divisor . $destino . $divisor . $fechaMin . $divisor;
	$linea			.= $saldo . $divisor . $fechaUltOp . $divisor . $tipo . $divisor . $pagoactual . $divisor . $tasa . $divisor . $cuota . $divisor . $id;
	echo "$linea\r\n";
}

?>