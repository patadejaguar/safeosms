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
$xHP		= new cHPage("", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);

$observaciones= parametro("idobservaciones");

$xHP->addChartSupport();
$xHP->init();

$xFRM		= new cHForm("frmscorecard", "./");
//$xFRM->addJsBasico();
//================================= Cer30
$CerRow		= $xQL->getDataRow("SELECT	SUM(`saldo`) AS `suma_cer`,	AVG(`dias_morosos`) AS `dias_en_mora`
	 FROM	`creditos_solicitud` `creditos_solicitud` INNER JOIN `dias_en_mora` `dias_en_mora`	ON `creditos_solicitud`.`numero_solicitud` = `dias_en_mora`.`numero_solicitud` 
	 WHERE `saldo` > 1	AND `dias_morosos` >=31 AND `creditos_solicitud`.`estatus_actual` != 50	AND `creditos_solicitud`.`tipo_autorizacion` != 3");
$ResRow		= $xQL->getDataRow("SELECT SUM(`saldo_actual`) AS `suma_res` FROM `creditos_solicitud`  WHERE `saldo_actual` > 1 AND `creditos_solicitud`.`estatus_actual` != 50 AND `creditos_solicitud`.`tipo_autorizacion` = 3");
$sumaCER	= $CerRow["suma_cer"];
$carteraCER	= $ResRow["suma_res"];

$xG			= new cChart("iddiv");
$xG->addData("CARTERA EN RIESGO", $sumaCER);
$xFRM->addJsCode($xG->getJs());
$xFRM->addHTML($xG->getDiv());

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>