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
//$jxc 		= new TinyAjax();
//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
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

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

$xFRM->OButton("TR.LOCALIDADES", "var xG=newGen();xG.w({url:'../frmmigracion/walook-localidades.frm.php?clean=true', tab:true});");

$xFRM->OButton("TR.PERSONAS", "var xG=newGen();xG.w({url:'../frmmigracion/walook-personas.frm.php?clean=true', tab:true});");

$xFRM->OButton("TR.ACTIVIDAD ECONOMICA", "var xG=newGen();xG.w({url:'../frmmigracion/walook-aeconomica.frm.php?clean=true', tab:true});");

$xFRM->OButton("TR.DOMICILIO", "var xG=newGen();xG.w({url:'../frmmigracion/walook-domicilio.frm.php?clean=true', tab:true});");

$xFRM->OButton("TR.RELACION ACTIVIDAD DOMICILIO", "var xG=newGen();xG.w({url:'../frmmigracion/walook-ae-dom.frm.php?clean=true', tab:true});");


$xFRM->OButton("TR.RELACION EMPRESA PERSONA", "var xG=newGen();xG.w({url:'../frmmigracion/walook-prel-empresa.frm.php', tab:true});");

$xFRM->OButton("TR.PERFIL TRANSACCIONAL", "var xG=newGen();xG.w({url:'../frmmigracion/walook-ptransaccional.frm.php', tab:true});");


$xFRM->OButton("TR.CREDITOS", "var xG=newGen();xG.w({url:'../frmmigracion/walook-creditos.frm.php?clean=true', tab:true});");
$xFRM->OButton("TR.PLAN_DE_PAGO", "var xG=newGen();xG.w({url:'../frmmigracion/walook-plan-pagos.frm.php?clean=true', tab:true});");

$xFRM->OButton("TR.SECUENCIAS", "var xG=newGen();xG.w({url:'../frmmigracion/walook-secuences.frm.php', tab:true});");

//$xFRM->OButton("TR.", "var xG=newGen();xG.w({url:'../', tab:true});");

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>