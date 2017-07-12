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
$monto			= parametro("cantidad",0, MQL_FLOAT); $monto = parametro("monto",$monto, MQL_FLOAT);
$frecuencia		= parametro("frecuencia",CREDITO_TIPO_PERIOCIDAD_MENSUAL, MQL_INT);
$tipo			= parametro("tipo", 0, MQL_INT);
$rs				= array();
$rs["error"]	= true;
$rs["message"]	= "";
$idmembresia	= 0;

//=================================== 
$xSoc			= new cSocio($persona);

if($xSoc->init() == true AND $tipo >0){
	$xMC		= new cPersonas_pagos_perfil();
	$xMC->clave_de_persona($persona);
	$xMC->fecha_de_aplicacion(fechasys());
	$xMC->idpersonas_pagos_perfil("NULL");
	$xMC->monto($monto);
	$xMC->periocidad($frecuencia);
	$xMC->tipo_de_operacion($tipo);
	$res = $xMC->query()->insert()->save();
	if($res === false){
		
	} else {
		$rs["error"]	= false;
		$rs["message"]	= "OK Agregado";
	}
}


$rs["id"]		= 0;

header('Content-type: application/json');
echo json_encode($rs);
?>