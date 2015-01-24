<?php
//=====================================================================================================
//=====>	INICIO_H
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	$theFile					= __FILE__;
	$permiso					= getSIPAKALPermissions($theFile);
	if($permiso === false){		header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//<=====	FIN_H
//window.print()
$xHP				= new cHPage("TR.Reporte de Credito", HP_REPORT);


$fecha_inicial 		= parametro("on", fechasys());
$fecha_final 		= parametro("off", fechasys());
$si_es_por_fecha 	= "";
$outG 				= (isset($_GET["outg"])) ? $_GET["outg"] : false;
$outG				= ($outG == "no") ? false : true;




$sucursal				= parametro("s", SYS_TODAS, MQL_RAW);
$sucursal				= parametro("sucursal", $sucursal, MQL_RAW);


$BySuc				= ""; //;
if($sucursal != SYS_TODAS){ $BySuc = " AND creditos.`sucursal` = '$sucursal' "; }
if($fecha_inicial && $fecha_final){ $si_es_por_fecha = " AND operaciones_mvtos.fecha_operacion>='$fecha_inicial' AND operaciones_mvtos.fecha_operacion<='$fecha_final' "; }



$senders				= getEmails($_REQUEST);
$tipo_operacion			= parametro("f711", SYS_TODAS, MQL_INT);
$tipo_operacion			= parametro("operacion", $tipo_operacion, MQL_INT);
$out 					= parametro("out", SYS_DEFAULT);

$def_type = 110;

	$sql = "SELECT creditos.convenio AS 'tipo', 
			COUNT(operaciones_mvtos.idoperaciones_mvtos) AS 'numero',

			
			SUM(operaciones_mvtos.afectacion_real) AS 'monto',

			/*SUM(creditos.monto_autorizado) AS 'monto_original',*/
			(SUM(operaciones_mvtos.afectacion_real) - SUM(creditos.saldo_actual)) AS 'cobros',
			SUM(creditos.saldo_actual) AS 'saldo_de_credito'
			 
FROM
	`operaciones_mvtos` `operaciones_mvtos` 
		INNER JOIN `operaciones_recibos` `operaciones_recibos` 
		ON `operaciones_mvtos`.`recibo_afectado` = `operaciones_recibos`.
		`idoperaciones_recibos` 
			INNER JOIN `creditos` `creditos` 
			ON `operaciones_mvtos`.`docto_afectado` = `creditos`.`solicitud` 
WHERE
	(`operaciones_recibos`.`tipo_pago` !='" . TESORERIA_COBRO_NINGUNO . "')
	AND (operaciones_mvtos.tipo_operacion=$def_type)
	$BySuc
	$si_es_por_fecha
GROUP BY creditos.convenio";

	
	
	$xHP->setTitle("TR.Reporte de Colocacion");
	$titulo			= $xHP->getTitle();
	$archivo		= "$titulo.pdf";
	
	$xRPT			= new cReportes($titulo);
	$xRPT->setFile($archivo);
	$xRPT->setOut($out);
	$xRPT->setSQL($sql);
	$xRPT->setTitle($xHP->getTitle());
	//============ Reporte
	$xT		= new cTabla($sql, 2);
	$xT->setTipoSalida($out);

	$xT->setFootSum(array(
			1 => "numero",
			2 => "monto",
			
			3 => "cobros",
			4 => "saldo_de_credito",
	));	
	
	$body		= $xRPT->getEncabezado($xHP->getTitle(), $fecha_inicial, $fecha_final);
	$xRPT->setBodyMail($body);
	$xRPT->addContent($body);
	$xRPT->addContent("<h1>" . $sucursal . "</h1>");
	//$xT->setEventKey("jsGoPanel");
	//$xT->setKeyField("creditos_solicitud");
	$xRPT->addContent( $xT->Show( $xHP->getTitle() ) );
	//============ Agregar HTML
	//$xRPT->addContent( $xHP->init($jsEvent) );
	//$xRPT->addContent( $xHP->end() );
	//$xRPT->addContent("<table>$TR_parent</table>");
	
	$xRPT->setResponse();
	$xRPT->setSenders($senders);
	echo $xRPT->render(true);
	
	
?>