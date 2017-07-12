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
$xHP		= new cHPage("TR.EDITAR COMISIONES", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc 		= new TinyAjax();
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

$xHP->init();

$xFRM	= new cHForm("frmleasing_comisiones", "leasing-comisiones.frm.php?action=$action");

$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xFRM->setFieldsetClass("fieldform frmpanel");

/* ===========		FORMULARIO EDICION 		============*/
$xTabla		= new cLeasing_comisiones();
$xTabla->setData( $xTabla->query()->initByID($clave));



$xFRM->OHidden("idleasing_comisiones", $xTabla->idleasing_comisiones()->v());
$xFRM->addHElem($xSel->getListaDeOriginadoresTipos("tipo_de_originador", $xTabla->tipo_de_originador()->v())->get(true));
//$xFRM->OMoneda("tipo_de_originador", $xTabla->tipo_de_originador()->v(), "TR.TIPO DE ORIGINADOR");
$xFRM->OMoneda("tasa_comision", $xTabla->tasa_comision()->v(), "TR.COMISION ORIGINADOR");
$xFRM->OMoneda("comision_ejecutivo", $xTabla->comision_ejecutivo()->v(), "TR.COMISION EJECUTIVO");
$xFRM->OMoneda("comision_regional", $xTabla->comision_regional()->v(), "TR.COMISION REGIONAL");
$xFRM->OMoneda("bono", $xTabla->bono()->v(), "TR.BONO");

$xFRM->addCRUDSave($xTabla->get(), $clave, true);
echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>