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
$xHP		= new cHPage("TR.EDITAR NOTAS", HP_FORM);
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

$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

/* ===========        FORMULARIO EDICION         ============*/
$xTabla        = new cUsuarios_web_notas();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM    = new cHForm("frmusernotes", "usuarios-notas.frm.php?action=$action");
$xFRM->setTitle($xHP->getTitle());
$xSel        = new cHSelect();

$xN				= new cSystemUserNotes();

if($clave<=0){
	$xTabla->idusuarios_web_notas("NULL");
	$xTabla->tiempo(time());
	$xTabla->estado($xN->ESTADO_ACT);
	$xTabla->socio(DEFAULT_SOCIO);
	$xTabla->documento(DEFAULT_CREDITO);
	$xTabla->oficial_de_origen(getUsuarioActual());
	$xTabla->oficial(getUsuarioActual());
	$xTabla->fecha(fechasys());
	
	$xFRM->addHElem( $xSel->getListaDeUsuarios("oficial", $xTabla->oficial()->v())->get(true) );
	$xFRM->ODate("fecha", $xTabla->fecha()->v(), "TR.FECHA");
} else {
	$xFRM->OHidden("oficial", $xTabla->oficial()->v(), "TR.OFICIAL");
}

$xFRM->OHidden("idusuarios_web_notas", $xTabla->idusuarios_web_notas()->v());
$xFRM->OHidden("oficial_de_origen", $xTabla->oficial_de_origen()->v(), "TR.OFICIAL DE ORIGEN");
$xFRM->OHidden("estado", $xTabla->estado()->v(), "TR.ESTADO");


//$xFRM->OEntero("relevancia", $xTabla->relevancia()->v(), "TR.RELEVANCIA");
$xFRM->addHElem( $xSel->getListaDeNivelDeRiesgo("relevancia", $xTabla->relevancia())->get(true));

//$xFRM->OEntero("tipo", $xTabla->tipo()->v(), "TR.TIPO");
$xFRM->addHElem( $xSel->getListaDeTiposDeMemoPersonas("tipo", $xTabla->tipo()->v())->get(true) );




$xFRM->OEntero("socio", $xTabla->socio()->v(), "TR.PERSONA");
$xFRM->OEntero("documento", $xTabla->documento()->v(), "TR.CONTRATO");


$xFRM->OTextArea("texto", $xTabla->texto()->v(), "TR.TEXTO");




//$xFRM->OEntero("tiempo", $xTabla->tiempo()->v(), "TR.TIEMPO");

//$xFRM->addCRUD($xTabla->get(), true);

$xFRM->addCRUDSave($xTabla->get(), $clave, true);


echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>