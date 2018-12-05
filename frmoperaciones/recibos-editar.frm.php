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
$xHP		= new cHPage("TR.EDITAR RECIBO", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$xRuls		= new cReglaDeNegocio();
$ForceFis	= $xRuls->getValorPorRegla($xRuls->reglas()->RECIBOS_CON_RECFISCAL);

//$jxc 		= new TinyAjax();
//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);$action	= strtolower($action);

/*$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);

$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$observaciones	= parametro("idobservaciones"); $observaciones	= parametro("observaciones", $observaciones);
*/

$xHP->init();


/* ===========        FORMULARIO EDICION         ============*/
$xTabla        = new cOperaciones_recibos();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM    = new cHForm("frmeditarrecs", "recibos.frm.php?action=$action");

$xFRM->setTitle($xHP->getTitle());
$xSel        = new cHSelect();


$xFRM->OHidden("idoperaciones_recibos", $xTabla->idoperaciones_recibos()->v());

$xFRM->ODisabled_13("fecha_operacion", $xTabla->fecha_operacion()->v(), "TR.FECHA OPERACION");
$xFRM->ODisabled_13("fecha_de_registro", $xTabla->fecha_de_registro()->v(), "TR.FECHA DE REGISTRO");
$xFRM->ODisabled_13("fecha_valor", $xTabla->fecha_valor()->v(), "TR.FECHA VALOR");
//$xFRM->ODisabled_13("tipo_pago", $xTabla->tipo_pago()->v(), "TR.TIPO PAGO");

//$xFRM->OText_13("tipo_pago", $xTabla->tipo_pago()->v(), "TR.TIPO PAGO");

$xFRM->addHElem( $xSel->getListaDeTipoDePagoTesoreria("tipo_pago", TESORERIA_TIPO_INGRESOS, $xTabla->tipo_pago()->v())->get(true) );

//$xFRM->ODate("fecha_operacion", $xTabla->fecha_operacion()->v(), "TR.FECHA OPERACION");
//$xFRM->ODate("fecha_de_registro", $xTabla->fecha_de_registro()->v(), "TR.FECHA DE REGISTRO");
//$xFRM->ODate("fecha_valor", $xTabla->fecha_valor()->v(), "TR.FECHA VALOR");

//$xFRM->OHidden("numero_socio", $xTabla->numero_socio()->v());
//$xFRM->OHidden("docto_afectado", $xTabla->docto_afectado()->v());
//$xFRM->OEntero("grupo_asociado", $xTabla->grupo_asociado()->v(), "TR.GRUPO ASOCIADO");

//$xFRM->OHidden("tipo_docto", $xTabla->tipo_docto()->v());
//$xFRM->OMoneda("total_operacion", $xTabla->total_operacion()->v(), "TR.TOTAL OPERACION");
//$xFRM->OHidden("idusuario", $xTabla->idusuario()->v(), "TR.IDUSUARIO");


$xFRM->OText_13("cheque_afectador", $xTabla->cheque_afectador()->v(), "TR.CHEQUE AFECTADOR");

$xFRM->OText("observacion_recibo", $xTabla->observacion_recibo()->v(), "TR.OBSERVACION RECIBO");
$xFRM->OText("cadena_distributiva", $xTabla->cadena_distributiva()->v(), "TR.CADENA DISTRIBUTIVA");



//$xFRM->OEntero("indice_origen", $xTabla->indice_origen()->v(), "TR.INDICE ORIGEN");
if($ForceFis == true){
	$xFRM->OText_13("recibo_fiscal", $xTabla->recibo_fiscal()->v(), "TR.RECIBO FISCAL");
}

//$xFRM->OHidden("sucursal", $xTabla->sucursal()->v(), "TR.SUCURSAL");
//$xFRM->OHidden("eacp", $xTabla->eacp()->v(), "TR.EACP");

if(getEsModuloMostrado(false, MMOD_AML) == true){
	$xFRM->addHElem( $xSel->getListaDeMonedas("clave_de_moneda", $xTabla->clave_de_moneda()->v())->get(true) );
	$xFRM->OMoneda("unidades_en_moneda", $xTabla->unidades_en_moneda()->v(), "TR.UNIDADES EN MONEDA");
}
//============ Archivo fisico XML
$xFRM->OText("archivo_fisico", $xTabla->archivo_fisico()->v(), "TR.ARCHIVO FISICO");

$xFRM->OMoneda("montohist", $xTabla->montohist()->v(), "TR.SALDO");

//$xFRM->OText_13("clave_de_moneda", $xTabla->clave_de_moneda()->v(), "TR.CLAVE DE MONEDA");
//$xFRM->OHidden("origen_aml", $xTabla->origen_aml()->v());

//$xFRM->OEntero("persona_asociada", $xTabla->persona_asociada()->v(), "TR.PERSONA ASOCIADA");
//$xFRM->OEntero("periodo_de_documento", $xTabla->periodo_de_documento()->v(), "TR.PERIODO DE DOCUMENTO");
//$xFRM->OEntero("cuenta_bancaria", $xTabla->cuenta_bancaria()->v(), "TR.CUENTA BANCARIA");
//$xFRM->OMoneda("montohist", $xTabla->montohist()->v(), "TR.MONTOHIST");

//$xFRM->OEntero("tiempo", $xTabla->tiempo()->v(), "TR.TIEMPO");

//$xFRM->OEntero("idtipocbza", $xTabla->idtipocbza()->v(), "TR.IDTIPOCBZA");

//$xFRM->OEntero("idusuario_cbza", $xTabla->idusuario_cbza()->v(), "TR.IDUSUARIO CBZA");




//$xFRM->addCRUD($xTabla->get(), true);

$xFRM->addCRUDSave($xTabla->get(), $clave, true);


echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>