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
$xHP		= new cHPage("TR.DOCUMENTACION", HP_FORM);
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
$tipo			= parametro("tipo", 0, MQL_INT);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT);
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$observaciones	= parametro("idobservaciones"); $observaciones	= parametro("observaciones", $observaciones);

$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);$action	= strtolower($action);

$producto		= parametro("producto", 0, MQL_INT);

$xHP->init();

/* ===========        FORMULARIO EDICION         ============*/
$xTabla        = new cCreditos_prods_doctos();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM    		= new cHForm("frmiddivcredptodsdoctos", "creditos-productos-doctos.frm.php?action=$action");

if($clave<=0){
	if($producto>0){
		$xTabla->producto_credito_id($producto);
	}
	$xTabla->idcreditos_prods_doctos("NULL");
}

$xFRM->setTitle($xHP->getTitle());
$xSel        = new cHSelect();

$xFRM->OHidden("idcreditos_prods_doctos", $xTabla->idcreditos_prods_doctos()->v());

$xFRM->addHElem( $xSel->getListaDeEtapasDeCred("etapa_id", $xTabla->etapa_id()->v())->get(true) );

$xFRM->addHElem( $xSel->getTiposDeDoctosPersonalesArch("documento_id", "", false)->get(true) );

//$xFRM->OEntero("documento_id", $xTabla->documento_id()->v(), "TR.DOCUMENTO ID");
//$xFRM->OEntero("etapa_id", $xTabla->etapa_id()->v(), "TR.ETAPA ID");

$xFRM->OSiNo("TR.ESTATUSACTIVO", "estatus", $xTabla->estatus()->v(), true);
$xFRM->OSiNo("TR.OPCIONAL", "opcional", $xTabla->opcional()->v(), true);



$xFRM->OHidden("producto_credito_id", $xTabla->producto_credito_id()->v(), "TR.PRODUCTO CREDITO ID");

//$xFRM->addCRUD($xTabla->get(), true);

$xFRM->addCRUDSave($xTabla->get(), $clave, true);


echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>