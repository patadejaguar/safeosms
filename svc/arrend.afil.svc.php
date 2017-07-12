<?php

/**
 * Modulo para agregar Actividad Economica.- Arrendadora
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
$xHP		= new cHPage("Afiliacion.- datos extensos", HP_SERVICE);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xImp		= new cCoreImport();

$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("fecha", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$observaciones	= parametro("idobservaciones");
$ExtFechaI		= parametro("fechaextrai", false, MQL_DATE);
$ExtFechaF		= parametro("fechaextraf", false, MQL_DATE);
$idpermiso		= parametro("permiso");
$idafilia		= parametro("idafilia", 0, MQL_INT);
$membresia		= 1;
$gradoacademico	= 0;
$lugarpago		= 1;
$nacionalidad	= "XX";


$rs				= array();
$rs["error"]	= true;
$rs["message"]	= "";
$idmembresia	= 0;

//=================================== 
$ocupacion		= parametro("ocupacion");
$ocupacion		= $xImp->setOcupacion($ocupacion);

if(PERSONAS_CONTROLAR_POR_APORTS == true AND $ocupacion !== ""){
	$DDL				= $xQL->getDataRow("SELECT * FROM `personas_zclasificacion` WHERE `descripcion_zclasificacion` LIKE '%$ocupacion%' LIMIT 0,1");
	if(isset($DDL["idpersonas_zclasificacion"])){
		$xPx			= new cPersonas_zclasificacion();
		$xPx->setData($DDL);
		$gradoacademico	= $xPx->idpersonas_zclasificacion()->v();
	}
}

$xSoc		= new cSocio($persona);

if($xSoc->init() == true){
	if($gradoacademico > 0){
		$xSoc->setDatosColegiacion($membresia, $lugarpago, $xF->dia($fecha), $gradoacademico, "", $idafilia);
		$idmembresia	= $gradoacademico;
		
	}
	if($idpermiso !== ""){
		$xSoc->setDatosExtranjero($idpermiso, $ExtFechaI, $ExtFechaF, $nacionalidad);
		
	}
	$rs["error"]	= false;
}

$rs["message"]		.= $xSoc->getMessages();
$rs["idmembresia"]	= $idmembresia;

header('Content-type: application/json');
echo json_encode($rs);
?>