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
$xLog		= new cCoreLog();
$jxc 		= new TinyAjax();
//$tab 		= new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
function jsaFixSucursales(){
	$xQL	= new MQL();
	$xLog	= new cCoreLog();
	
	$sql	= "SELECT DISTINCT INFORMATION_SCHEMA.COLUMNS.TABLE_SCHEMA, INFORMATION_SCHEMA.TABLES.TABLE_TYPE, INFORMATION_SCHEMA.COLUMNS.TABLE_NAME, INFORMATION_SCHEMA.COLUMNS.COLUMN_NAME
FROM INFORMATION_SCHEMA.COLUMNS INNER JOIN INFORMATION_SCHEMA.TABLES ON INFORMATION_SCHEMA.TABLES.TABLE_SCHEMA = INFORMATION_SCHEMA.COLUMNS.TABLE_SCHEMA
WHERE INFORMATION_SCHEMA.COLUMNS.TABLE_SCHEMA='" . MY_DB_IN . "' AND INFORMATION_SCHEMA.TABLES.TABLE_TYPE NOT LIKE '%VIEW%' AND ((column_name LIKE '%idsucursal%') OR (column_name LIKE '%sucursal%'))";
	
	$xQL->setRawQuery("ALTER TABLE `general_sucursales` CHANGE COLUMN `codigo_sucursal` `codigo_sucursal` VARCHAR(20) NOT NULL");
	
	$rs		= $xQL->getRecordset($sql);
	while($rw = $rs->fetch_assoc()){
		$tabla	= $rw["TABLE_NAME"];
		$campo	= $rw["COLUMN_NAME"];
		
		$res	= $xQL->setRawQuery("ALTER TABLE `$tabla` CHANGE `$campo` `$campo` VARCHAR(20) CHARSET utf8 COLLATE utf8_general_ci DEFAULT 'matriz' NULL");
		if($res === false){
			$xLog->add("ERROR\tFallo en la tabla $tabla campo $campo \r\n");
		} else {
			$xLog->add("OK\tla tabla $tabla campo $campo \r\n");
		}
		//$xLog->add($xQL->getMessages());
		$xQL->setRawQuery("UPDATE `$tabla` SET `$campo`=LOWER(`$campo`) ");
		
		$xQL->setRawQuery("UPDATE `$tabla` SET `$campo`='matriz' WHERE (SELECT COUNT(*) FROM `general_sucursales` WHERE `general_sucursales`.`codigo_sucursal`=`$tabla`.`$campo`)<=0");
	}
}
function jsaChangeSucursales($idanterior, $idnuevo){
	$xQL	= new MQL();
	$xLog	= new cCoreLog();
	$sql	= "SELECT DISTINCT INFORMATION_SCHEMA.COLUMNS.TABLE_SCHEMA, INFORMATION_SCHEMA.TABLES.TABLE_TYPE, INFORMATION_SCHEMA.COLUMNS.TABLE_NAME, INFORMATION_SCHEMA.COLUMNS.COLUMN_NAME
FROM INFORMATION_SCHEMA.COLUMNS INNER JOIN INFORMATION_SCHEMA.TABLES ON INFORMATION_SCHEMA.TABLES.TABLE_SCHEMA = INFORMATION_SCHEMA.COLUMNS.TABLE_SCHEMA
WHERE INFORMATION_SCHEMA.COLUMNS.TABLE_SCHEMA='" . MY_DB_IN . "' AND INFORMATION_SCHEMA.TABLES.TABLE_TYPE NOT LIKE '%VIEW%' AND ((column_name LIKE '%idsucursal%') OR (column_name LIKE '%sucursal%'))";
	
	$rs		= $xQL->getRecordset($sql);
	while($rw = $rs->fetch_assoc()){
		$tabla	= $rw["TABLE_NAME"];
		$campo	= $rw["COLUMN_NAME"];
		
		$res	= $xQL->setRawQuery("UPDATE `$tabla` SET `$campo`='$idnuevo' WHERE  `$campo`='$idanterior' ");
		
	}
	$xQL->setRawQuery("UPDATE `general_sucursales` SET `codigo_sucursal`='$idnuevo' WHERE `codigo_sucursal`='$idanterior' ");
}
$jxc->exportFunction('jsaFixSucursales', array('idsucursalanterior', 'idsucursalnuevo'), "#idmsg");
$jxc->exportFunction('jsaChangeSucursales', array('idsucursalanterior', 'idsucursalnuevo'), "#idmsg");

$jxc->process();
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);$action	= strtolower($action);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$observaciones	= parametro("idobservaciones"); $observaciones	= parametro("observaciones", $observaciones);

$xHP->init();

$idsucursalanterior = parametro("idsucursalanterior"); $idsucursalanterior = strtolower($idsucursalanterior);
$idsucursalnuevo = parametro("idsucursalnuevo"); $idsucursalnuevo = strtolower($idsucursalnuevo);



$xFRM		= new cHForm("frm", "./sucursales.fix.frm.php");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

$xFRM->OButton("TR.Corregir", "jsFixSucursales()");

$xFRM->OButton("TR.CAMBIAR", "jsChangeSucursales()");


//if($action == SYS_NINGUNO){
	//$xFRM->addCreditBasico();
	$xFRM->OText_13("idsucursalanterior", "", "TR.SUCURSAL ANTERIOR");
	$xFRM->OText_13("idsucursalnuevo", "", "TR.SUCURSAL NUEVO");
	
	$xFRM->addSubmit();
//}
//$xFRM->addLog($xLog->getMessages());

$xFRM->addAviso("", "idmsg");

echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsFixSucursales(){
	xG.confirmar({msg: "CONFIRMA_ACTUALIZACION", callback: jsaFixSucursales});
}
function jsChangeSucursales(){
	xG.confirmar({msg: "CONFIRMA_ACTUALIZACION", callback: jsaChangeSucursales});
}
</script>
<?php

$jxc ->drawJavaScript(false, true);

$xHP->fin();
?>