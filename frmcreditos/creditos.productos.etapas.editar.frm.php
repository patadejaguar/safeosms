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


/* ===========        FORMULARIO EDICION         ============*/
$xTabla        = new cCreditos_productos_reglas();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM    = new cHForm("frmpreglas", "creditos.productos.reglas.frm.php?action=$action");

$xFRM->setTitle($xHP->getTitle());
$xSel        = new cHSelect();

$xFRM->OHidden("idcreditos_productos_reglas", $xTabla->idcreditos_productos_reglas()->v());
$xFRM->OMoneda("producto_id", $xTabla->producto_id()->v(), "TR.PRODUCTO ID");
$xFRM->OMoneda("tipo_regla", $xTabla->tipo_regla()->v(), "TR.TIPO REGLA");
$xFRM->OText("clave_Interna", $xTabla->clave_Interna()->v(), "TR.CLAVE INTERNA");
$xFRM->OMoneda("evoluciona", $xTabla->evoluciona()->v(), "TR.EVOLUCIONA");
$xFRM->OMoneda("contador", $xTabla->contador()->v(), "TR.CONTADOR");
$xFRM->OMoneda("num_minimo", $xTabla->num_minimo()->v(), "TR.NUM MINIMO");
$xFRM->OMoneda("num_maximo", $xTabla->num_maximo()->v(), "TR.NUM MAXIMO");
$xFRM->OMoneda("monto_min", $xTabla->monto_min()->v(), "TR.MONTO MIN");
$xFRM->OMoneda("monto_max", $xTabla->monto_max()->v(), "TR.MONTO MAX");
$xFRM->OMoneda("tasa_min", $xTabla->tasa_min()->v(), "TR.TASA MIN");
$xFRM->OMoneda("tasa_max", $xTabla->tasa_max()->v(), "TR.TASA MAX");

//$xFRM->addCRUD($xTabla->get(), true);

$xFRM->addCRUDSave($xTabla->get(), $clave, true);


echo $xFRM->get();


//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>