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
$xHP		= new cHPage("TR.EDITAR BONOS", HP_FORM);
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
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto		= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo		= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa	= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo		= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria = parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);

$observaciones= parametro("idobservaciones");

$idleasing		= parametro("idleasing",0, MQL_INT);


$xHP->init();


/* ===========		FORMULARIO EDICION 		============*/
$xTabla		= new cLeasing_bonos();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM	= new cHForm("frmbonos", "leasing-bonos.frm.php?action=$action");

$xFRM->setTitle($xHP->getTitle());
$xSel		= new cHSelect();

if($clave <= 0){
	$xTabla->tipo_bono(1);
	$xTabla->fecha_de_pago(fechasys());
	$xTabla->clave_leasing($idleasing);
	$xTabla->idleasing_bonos("NULL");
}

$xMSel	= $xSel->getListaDeCatalogoGenerico("leasing_bonos_dest", "tipo_destino", $xTabla->tipo_destino()->v());
$xFRM->addHElem( $xMSel->get("TR.DESTINO", true) );
//$xFRM->OMoneda("tipo_destino", $xTabla->tipo_destino()->v(), "TR.TIPO DESTINO");
$xFRM->ODate("fecha", $xTabla->fecha()->v(), "TR.FECHA");

$xFRM->OMoneda("tasa_bono", $xTabla->tasa_bono()->v(), "TR.TASA BONO");
$xFRM->OMoneda2("monto_bono", $xTabla->monto_bono()->v(), "TR.MONTO BONO");


$xFRM->OHidden("tipo_bono", $xTabla->tipo_bono()->v(), "TR.TIPO BONO");
$xFRM->OHidden("fecha_de_pago", $xTabla->fecha_de_pago()->v(), "TR.FECHA DE PAGO");
$xFRM->OHidden("idleasing_bonos", $xTabla->idleasing_bonos()->v());
$xFRM->OHidden("clave_leasing", $xTabla->clave_leasing()->v(), "TR.CLAVE LEASING");

$xFRM->addCRUD($xTabla->get(), true);
//$xFRM->addCRUDSave($xTabla->get(), $clave, true);


echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>