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
$xHP		= new cHPage("", HP_SERVICE);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xImp		= new cCoreImport();

$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); 
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$observaciones	= parametro("idobservaciones");


$rs				= array();
$rs["error"]	= true;
$rs["message"]	= "";
$idactividad	= 0;

//=================================== 
$scian			= parametro("scian",FALLBACK_ACTIVIDAD_ECONOMICA, MQL_INT);
$describe		= parametro("descripcion");
$cp				= parametro("cp", 0, MQL_INT);


$xSoc		= new cSocio($persona);

if($xSoc->init() == true){
	$ingreso	= 1;
	$xAE		= new cPersonaActividadEconomica($xSoc->getClaveDePersona());
	if($scian >0){
		$xAE->InitDatosSCIAN($scian);
		
		$describe		= ($describe == "") ? $xAE->getDescripcionAct() : $describe;
		$xAE->add($xAE->getClaveActividadAML(), 1, false, $describe, $cp);
		$xAE->setActualizarDescripcion($describe);
		$idactividad	= setNoMenorQueCero($xAE->getIDDeActividad());
		if($idactividad >0){
			$rs["error"]	= false;
		}
	}
	$rs["message"]		.= $xAE->getMessages();
}

$rs["message"]		.= $xSoc->getMessages();
$rs["idactividad"]	= $idactividad;
$rs["idvivienda"]	= $idactividad; //fix excel error

header('Content-type: application/json');
echo json_encode($rs);
?>