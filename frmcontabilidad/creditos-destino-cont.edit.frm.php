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



/* ===========        FORMULARIO EDICION         ============*/
$xTabla        = new cCreditos_destinos();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM    = new cHForm("frmdestcont", "creditos-destino-cont.edit.frm.php?action=$action");

$xFRM->setTitle($xHP->getTitle());
$xSel        = new cHSelect();

$xFRM->OHidden("idcreditos_destinos", $xTabla->idcreditos_destinos()->v());
$xFRM->ODisabled("descripcion_destinos", $xTabla->descripcion_destinos()->v(), "TR.DESCRIPCION DESTINOS");
$xFRM->OTextContable("capital_vencido_renovado", $xTabla->capital_vencido_renovado()->v(), "TR.CAPITAL VENCIDO RENOVADO");
$xFRM->OTextContable("capital_vencido_reestructurado", $xTabla->capital_vencido_reestructurado()->v(), "TR.CAPITAL VENCIDO REESTRUCTURADO");
$xFRM->OTextContable("capital_vencido_normal", $xTabla->capital_vencido_normal()->v(), "TR.CAPITAL VENCIDO NORMAL");
$xFRM->OTextContable("capital_vigente_renovado", $xTabla->capital_vigente_renovado()->v(), "TR.CAPITAL VIGENTE RENOVADO");
$xFRM->OTextContable("capital_vigente_reestructurado", $xTabla->capital_vigente_reestructurado()->v(), "TR.CAPITAL VIGENTE REESTRUCTURADO");
$xFRM->OTextContable("capital_vigente_normal", $xTabla->capital_vigente_normal()->v(), "TR.CAPITAL VIGENTE NORMAL");
$xFRM->OTextContable("interes_vencido_renovado", $xTabla->interes_vencido_renovado()->v(), "TR.INTERES VENCIDO RENOVADO");
$xFRM->OTextContable("interes_vencido_reestructurado", $xTabla->interes_vencido_reestructurado()->v(), "TR.INTERES VENCIDO REESTRUCTURADO");
$xFRM->OTextContable("interes_vencido_normal", $xTabla->interes_vencido_normal()->v(), "TR.INTERES VENCIDO NORMAL");
$xFRM->OTextContable("interes_vigente_renovado", $xTabla->interes_vigente_renovado()->v(), "TR.INTERES VIGENTE RENOVADO");
$xFRM->OTextContable("interes_vigente_reestructurado", $xTabla->interes_vigente_reestructurado()->v(), "TR.INTERES VIGENTE REESTRUCTURADO");
$xFRM->OTextContable("interes_vigente_normal", $xTabla->interes_vigente_normal()->v(), "TR.INTERES VIGENTE NORMAL");
$xFRM->OTextContable("interes_cobrado", $xTabla->interes_cobrado()->v(), "TR.INTERES COBRADO");
$xFRM->OTextContable("moratorio_cobrado", $xTabla->moratorio_cobrado()->v(), "TR.MORATORIO COBRADO");



//$xFRM->addCRUD($xTabla->get(), true);
$xFRM->addCRUDSave($xTabla->get(), $clave, true);



echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>