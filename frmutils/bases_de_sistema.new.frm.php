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
$xHP		= new cHPage("TR.AGREGAR ITEM", HP_FORM);
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
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$base		= parametro("base", 0, MQL_INT);
$tiporecibo	= parametro("tiporecibo", 0, MQL_INT);


$xBase		= new cBases($base); $xBase->init();

$xHP->init();

$ByRec		= ($tiporecibo <= 0) ? "" : " AND (`operaciones_tipos`.`recibo_que_afecta` = $tiporecibo ) ";


/* ===========		FORMULARIO EDICION 		============*/
$xTabla		= new cEacp_config_bases_de_integracion_miembros();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM	= new cHForm("frmbasesnew", "bases_de_sistema.frm.php?action=$action");

$xFRM->setTitle($xHP->getTitle());
$xSel		= new cHSelect();
if($clave <= 0){
	$xTabla->ideacp_config_bases_de_integracion_miembros('NULL');
	$xTabla->codigo_de_base($base);
	
	if($xBase->getEsDeOperaciones() == true){
		$sqlO		= "SELECT `idoperaciones_tipos`,`descripcion_operacion` FROM `operaciones_tipos` WHERE `operaciones_tipos`.`es_estadistico` != '1' AND (`estatus`!=0) $ByRec 
AND (SELECT COUNT(*) FROM `eacp_config_bases_de_integracion_miembros` WHERE `codigo_de_base`=$base AND `eacp_config_bases_de_integracion_miembros`.`miembro`=`operaciones_tipos`.`idoperaciones_tipos` )<=0";
		
		
		$xSelO		=  $xSel->getListadoGenerico($sqlO, "miembro"); //$xSel->getListaDeTiposDeOperacion("miembro", $xTabla->miembro()->v());
		$xSelO->setEsSql();
		$xSelO->setOptionSelect($xTabla->miembro()->v());
		$xSelO->addEvent("onchange", "jsUpdateText()");
		
		$xFRM->addHElem( $xSelO->get(true) );
		$xFRM->OHidden("descripcion_de_la_relacion", "");
		
	} else {
		$xFRM->OMoneda("miembro", $xTabla->miembro()->v(), "TR.MIEMBRO");
		$xFRM->OText_13("descripcion_de_la_relacion", $xTabla->descripcion_de_la_relacion()->v(), "TR.NOMBRE");
	}
}

$xFRM->OHidden("ideacp_config_bases_de_integracion_miembros", $xTabla->ideacp_config_bases_de_integracion_miembros()->v());
$xFRM->OHidden("codigo_de_base", $xTabla->codigo_de_base()->v(), "TR.CODIGO DE BASE");


$xFRM->addHElem( $xSel->getListaDeTiposDeAfectacionOperaciones("afectacion", $xTabla->afectacion()->v())->get("TR.AFECTACION", true));

//$xFRM->OMoneda("afectacion", $xTabla->afectacion()->v(), "TR.AFECTACION");

$xFRM->OMoneda("subclasificacion", $xTabla->subclasificacion()->v(), "TR.SUBCLASIFICACION");




$xFRM->addCRUD($xTabla->get(), true);

//$xFRM->addCRUDSave($xTabla->get(), $clave, true);

echo $xFRM->get();

?>
<script>
function jsUpdateText(){
	var str = $('#miembro :selected').text();;//$('#miembro').find(":selected").text();
	str		= String(str).substring(0,40);
	$("#descripcion_de_la_relacion").val(str);
}
//var conceptName = $('#aioConceptName').find(":selected").text();
</script>
<?php

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>