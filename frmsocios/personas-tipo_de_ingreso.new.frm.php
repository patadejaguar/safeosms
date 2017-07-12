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
$xHP		= new cHPage("TR.AGREGAR TIPO_DE INGRESO", HP_FORM);
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

/* ===========		FORMULARIO EDICION 		============*/
$xTabla		= new cSocios_tipoingreso();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM		= new cHForm("frmtipoingreso", "socios_tipoingreso.frm.php?action=$action");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());



$xFRM->OHidden("idsocios_tipoingreso", $xTabla->idsocios_tipoingreso()->v());
$xFRM->OText_13("descripcion_tipoingreso", $xTabla->descripcion_tipoingreso()->v(), "TR.NOMBRE");
$xFRM->OText("descripcion_detallada", $xTabla->descripcion_detallada()->v(), "TR.DESCRIPCION");
$xFRM->addHElem( $xSel->getListaDeNivelDeRiesgo("nivel_de_riesgo",$xTabla->nivel_de_riesgo()->v())->get(true) );
$xFRM->OMoneda("parte_social", $xTabla->parte_social()->v(), "TR.PARTE_SOCIAL");
$xFRM->OMoneda("parte_permanente", $xTabla->parte_permanente()->v(), "TR.PARTE_PERMANENTE");
$xFRM->OSiNo("TR.ESTATUSACTIVO", "estado", $xTabla->estado()->v() );


$xFRM->OHidden("tipo_de_persona", $xTabla->tipo_de_persona()->v(), "TR.TIPO DE PERSONA");

$xFRM->addCRUD($xTabla->get(), true);
//$xFRM->addCRUDSave($xTabla->get(), $clave, true);
echo $xFRM->get();



//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>