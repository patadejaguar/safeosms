<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 0.0.01
 * @package
 */
//=====================================================================================================
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	include_once("../core/core.db.inc.php");
	$theFile			= __FILE__;
	$permiso			= getSIPAKALPermissions($theFile);
	if($permiso === false){	header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//=====================================================================================================
$xHP		= new cHPage("", HP_SERVICE);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
//$xDic		= new cHDicccionarioDeTablas();

$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("fecha", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$observaciones	= parametro("idobservaciones");

$FechaFinal		= parametro("fechafinal", false, MQL_DATE);
$acumulado		= parametro("acumulado", false, MQL_BOOL);

$rs				= array();
$rs["error"]	= true;
$rs["message"]	= "Sin datos validos";
header('Content-type: application/json');

$sql			= "SELECT
	`creditos_letras_del_dia`.`fecha_de_pago`,
	COUNT(`creditos_letras_del_dia`.`parcialidad`) AS 'pagos',
	SUM(`creditos_letras_del_dia`.`letra`)	AS 'monto' 
FROM
	`creditos_letras_del_dia` `creditos_letras_del_dia` 
		INNER JOIN `creditos_solicitud` `creditos_solicitud` 
		ON `creditos_letras_del_dia`.`credito` = `creditos_solicitud`.
		`numero_solicitud` 
			INNER JOIN `socios` `socios` 
			ON `creditos_letras_del_dia`.`persona` = `socios`.`codigo` 
WHERE
	`creditos_solicitud`.`omitir_seguimiento` = 0
	AND (`creditos_letras_del_dia`.`fecha_de_pago` >='$fecha') AND (`creditos_letras_del_dia`.`fecha_de_pago` <='$FechaFinal')
	
GROUP BY
	`creditos_letras_del_dia`.`fecha_de_pago`";
$sql			= ($acumulado == true) ? $sql : $xLi->getListaDeLetrasDelDia($fecha, $FechaFinal, false, false, " AND (`creditos_solicitud`.`omitir_seguimiento` = 0) ");
$oficial		= (MODO_DEBUG == true) ? false : getUsuarioActual();
$xSVC			= new MQLService("LIST", $sql);

echo $xSVC->getJSON();
//echo json_encode($rs);
?>