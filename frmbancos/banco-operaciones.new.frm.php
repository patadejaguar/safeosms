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
$xHP		= new cHPage("TR.OPERACIONES EN BANCOS", HP_FORM);
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

$conciliar		= parametro("conciliar", false, MQL_BOOL);

$operacion		= parametro("idtipooperacionbanco", BANCOS_OPERACION_DEPOSITO, MQL_RAW);

$xHP->init();


/* ===========		FORMULARIO EDICION 		============*/
$xTabla		= new cBancos_operaciones();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM	= new cHForm("frmopsbancs", "banco-operaciones.frm.php?action=$action");
$xFRM->setTitle($xHP->getTitle());
$xSel		= new cHSelect();

$xBancops	= new cOperacionBancaria();



if($clave<=0){
	$xTabla->idcontrol('NULL');
	$xTabla->eacp(EACP_CLAVE);
	$xTabla->sucursal(getSucursal());
	
	$xTabla->idusuario(getUsuarioActual());
	$xTabla->usuario_autorizo(getUsuarioActual());
	$xTabla->estatus($xBancops->NOAUTORIZADO);
	$xTabla->tipo_operacion($operacion);
	$xTabla->cuenta_bancaria($cuenta);
}



$xFRM->OHidden("idcontrol", $xTabla->idcontrol()->v());
$xFRM->OHidden("eacp", $xTabla->eacp()->v());
$xFRM->OHidden("sucursal", $xTabla->sucursal()->v());
$xFRM->OHidden("idusuario", $xTabla->idusuario()->v());
$xFRM->OHidden("usuario_autorizo", $xTabla->usuario_autorizo()->v());
$xFRM->OHidden("estatus", $xTabla->estatus()->v());
$xFRM->OHidden("clave_de_conciliacion", $xTabla->clave_de_conciliacion()->v());


$xFRM->addSeccion("iddivt0", "TR.RELACION");
$xFRM->OMoneda("numero_de_socio", $xTabla->numero_de_socio()->v(), "TR.CLAVE_DE_PERSONA");
$xFRM->OMoneda("documento_de_origen", $xTabla->documento_de_origen()->v(), "TR.CONTRATO");
$xFRM->ONumero("recibo_relacionado", $xTabla->recibo_relacionado()->v(), "TR.RECIBO");
$xFRM->endSeccion();
$xFRM->addSeccion("iddivt1", "TR.OPERACION");

if($cuenta>0){
	$xFRM->OHidden("cuenta_bancaria", $xTabla->cuenta_bancaria()->v());
} else {
	$xFRM->addHElem( $xSel->getListaDeCuentasBancarias("cuenta_bancaria", true, $xTabla->cuenta_bancaria()->v())->get(true) );
}

$xFRM->ODate("fecha_expedicion", $xTabla->fecha_expedicion()->v());
$xFRM->addHElem( $xSel->getListaDeTiposDeOperacionesBancarias("tipo_operacion", $xTabla->tipo_operacion()->v())->get(true) );


//TODO: Validar bien de donde es originado esta Dato

//$xFRM->OMoneda("cuenta_de_origen", $xTabla->cuenta_de_origen()->v(), "TR.CUENTA DE ORIGEN");
$xFRM->OHidden("cuenta_de_origen", $xTabla->cuenta_de_origen()->v());
$xFRM->OText_13("numero_de_documento", $xTabla->numero_de_documento()->v(), "TR.CHEQUE / REFERENCIA");
$xFRM->OText("beneficiario", $xTabla->beneficiario()->v(), "TR.BENEFICIARIO");

$xFRM->endSeccion();
$xFRM->addSeccion("iddivt2", "TR.MONTO");

$xFRM->addHElem( $xSel->getListaDeMonedas("clave_de_moneda", $xTabla->clave_de_moneda()->v())->get(true) );
$xFRM->addHElem( $xSel->getListaDeTipoDePagoTesoreria("tipo_de_exhibicion", false, $xTabla->tipo_de_exhibicion()->v())->get(true) );
$xFRM->OMoneda("monto_descontado", $xTabla->monto_descontado()->v(), "TR.MONTO DESCONTADO");

$xFRM->OMoneda("monto_real", $xTabla->monto_real()->v(), "TR.MONTO REAL");

//$xFRM->OSelect("estatus", $xTabla->estatus()->v() , "TR.ESTATUS", array("autorizado"=>"AUTORIZADO", "noautorizado"=>"NOAUTORIZADO", "cancelado"=>"CANCELADO"));

$xFRM->endSeccion();




$xFRM->addCRUD($xTabla->get(), true);

//$xFRM->addCRUDSave($xTabla->get(), $clave, true);





echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>