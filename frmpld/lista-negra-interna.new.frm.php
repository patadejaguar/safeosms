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
$xHP		= new cHPage("TR.AGREGAR LISTA_NEGRA", HP_FORM);
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

$xHP->init();

$xFRM	= new cHForm("frmlistanegra", "lista-negra-interna.frm.php?action=$action");
$xFRM->setTitle($xHP->getTitle());
$xSel		= new cHSelect();

/* ===========		FORMULARIO EDICION 		============*/
$xTabla		= new cAml_listanegra_int();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xTabla->clave_interna("NULL");
$xTabla->sucursal(getSucursal());
$xTabla->idusuario(getUsuarioActual());
$xTabla->estatus(SYS_UNO);
$xTabla->fecha_de_registro(fechasys());
$xTabla->fecha_de_vencimiento($xF->getFechaMaximaOperativa());

$xFRM	= new cHForm("frmlistanegra", "lista-negra-interna.frm.php?action=$action");
$xFRM->setTitle($xHP->getTitle());
$xSel		= new cHSelect();


$xFRM->OHidden("clave_interna", $xTabla->clave_interna()->v());
$xFRM->addPersonaBasico("", false, false, "jsUpdatePersona()");



$xFRM->ODate("fecha_de_vencimiento", $xTabla->fecha_de_vencimiento()->v(), "TR.FECHA DE VENCIMIENTO");

$xFRM->addHElem( $xSel->getListaDeNivelDeRiesgo("riesgo", $xTabla->riesgo()->v())->get(true) );
$xFRM->addHElem( $xSel->getListaDeTipoDeRiesgoEnAML("idmotivo", $xTabla->idmotivo()->v())->get(true) );
$xFRM->OText("observaciones", $xTabla->observaciones()->v(), "TR.OBSERVACIONES");

$xFRM->OHidden("fecha_de_registro", $xTabla->fecha_de_registro()->v());
$xFRM->OHidden("estatus", $xTabla->estatus()->v());
$xFRM->OHidden("sucursal", $xTabla->sucursal()->v());
$xFRM->OHidden("idusuario", $xTabla->idusuario()->v());
$xFRM->OHidden("persona", $xTabla->persona()->v());

$xFRM->addCRUD($xTabla->get(), true);

echo $xFRM->get();
?>
<script>
function jsUpdatePersona(){
	$("#persona").val($("#idsocio").val());
}
</script>
<?php

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>