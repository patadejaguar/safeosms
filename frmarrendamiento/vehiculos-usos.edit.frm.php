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
$xHP		= new cHPage("TR.EDITAR VEHICULOUSO", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();


$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->init();

/* ===========		FORMULARIO EDICION 		============*/
$xTabla		= new cVehiculos_usos();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM	= new cHForm("frmvehiculosusos", "vehiculos-usos.frm.php?action=$action");

$xFRM->setTitle($xHP->getTitle());
$xSel		= new cHSelect();
if($clave <= 0){
	$xTabla->idvehiculos_usos("NULL");
}

$xFRM->OHidden("idvehiculos_usos", $xTabla->idvehiculos_usos()->v());

$xFRM->OText_13("descripcion_uso", $xTabla->descripcion_uso()->v(), "TR.DESCRIPCION");
$xFRM->OMoneda2("limitededucible", $xTabla->limitededucible()->v(), "TR.MEXIMO DEDUCIBLE");

//$xFRM->addCRUD($xTabla->get(), true);
$xFRM->addCRUDSave($xTabla->get(), $clave, true);
echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>