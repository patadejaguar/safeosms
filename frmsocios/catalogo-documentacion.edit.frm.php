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
$xHP		= new cHPage("TR.CATALOGO DOCUMENTACION", HP_FORM);
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


$xHP->addJTagSupport();
$xHP->init();

/* ===========		FORMULARIO EDICION 		============*/
$xTabla		= new cPersonas_documentacion_tipos();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM	= new cHForm("frmdocumentaciontipos", "catalogo-documentacion.frm.php?action=$action");

$xFRM->setTitle($xHP->getTitle());
$xSel		= new cHSelect();
$xSel2		= new cHSelect();
$xSel->addOptions(array(
		"DG" => "Documentos Generales",
		"IP" => "Personas Naturales/Fisicas",
		"IPM" => "Personas Morales/Juridicas"
));
$xSel2->addOptions(array(
		"entregaa" => "Entrega A",
		"entregab" => "Entrega B",
		"entregac" => "Entrega C",
		"entregad" => "Entrega D",
		"entregae" => "Entrega E",
		"entregaf" => "Entrega F",
		"entregag" => "Entrega G",
		"entregah" => "Entrega H",
		"entregai" => "Entrega I",
		"entregaj" => "Entrega J",
		"entregak" => "Entrega K",
		"entregal" => "Entrega L",
		"entregam" => "Entrega M",
		"entregan" => "Entrega N",
		"entregao" => "Entrega O",
		"entregap" => "Entrega P",
		"entregaq" => "Entrega Q",
		"entregar" => "Entrega R",
		"entregas" => "Entrega S",
		"entregta" => "Entrega T",
		SYS_NINGUNO => "Ninguno"
));
$xFRM->OHidden("clave_de_control", $xTabla->clave_de_control()->v());

$xFRM->OText("nombre_del_documento", $xTabla->nombre_del_documento()->v(), "TR.NOMBRE DEL DOCUMENTO");
$xFRM->addHElem($xSel->get("clasificacion", "TR.CLASIFICACION",$xTabla->clasificacion()->v()) );
//$xFRM->OText("clasificacion", $xTabla->clasificacion()->v(), "TR.CLASIFICACION");
$xFRM->OMoneda("vigencia_dias", $xTabla->vigencia_dias()->v(), "TR.VIGENCIA DIAS");

$xFRM->addHElem($xSel2->get("checklist", "TR.CHECKLIST",$xTabla->checklist()->v()) );

$xFRM->OSiNo("TR.ARCHIVO","almacen", $xTabla->almacen()->v());
$xFRM->OSiNo("TR.ESTATUSACTIVO","estatus", $xTabla->estatus()->v());
$xFRM->OSiNo("TR.ES IDENTIFICACION_OFICIAL","es_ident", $xTabla->es_ident()->v());
$xFRM->OSiNo("TR.ES DE CONTRATO","es_cont", $xTabla->es_cont()->v());

$xFRM->OSiNo("TR.ES MULTIPLE","es_mult", $xTabla->es_mult()->v());

$arrV	= array("todas" => "Para Cualquiera", "pf" => "Personas Fisicas", "pm" => "Personas Morales", "originacion" => "De Originacion", "analisis" => "De Analisis");

$xFRM->OTagControl("tags", $xTabla->tags()->v(), "TR.TAGS", $arrV);

//$xFRM->addCRUD($xTabla->get(), true);
$xFRM->addCRUDSave($xTabla->get(), $clave, true);

echo $xFRM->get();


//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>