<?php

/**
 * Modulo para agregar Vivienda.- Arrendadora
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
$xHP		= new cHPage("TR.Vivienda", HP_SERVICE);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xImp		= new cCoreImport();

$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); 
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$observaciones	= parametro("idobservaciones");


$arrEstados		= array();
$arrMunicipio	= array();
$arrIDEstados	= array();

$rs				= array();
$rs["error"]	= true;
$rs["message"]	= "";

//=================================== Separar la direccion
$ddir			= parametro("direccion");
//$DatosDi		= explode(",",$ddir);
$colonia		= parametro("colonia");
$entidadFed		= parametro("ef");
$idEntidadF		= parametro("idef");
$ciudad			= parametro("ciudad");
$municipio		= parametro("municipio");
$entidadFed		= parametro("estado", $entidadFed);
$calle			= parametro("calle");
$numeroExt		= parametro("numero");
$numeroInt		= parametro("numerointerior");
$cp				= parametro("cp", 0, MQL_INT);
$completo		= $ddir;// parametro("completo");


if($completo !== ""){
	$xImp->setDireccionCompleta($completo);

	$calle				= $xImp->getCalle();
	$numeroExt			= $xImp->getNumeroExt();
	
	if($colonia == ""){
		$colonia		= $xImp->getColonia();
	}
	if($entidadFed == ""){
		$entidadFed		= $xImp->getEntidadFed();
	}
	if($municipio == ""){
		$municipio		= $xImp->getMunicipio();
	}
	if($cp <= 0){
		$cp				= $xImp->getCodigoPostal();
	}
	
}
if($cp <= 0){

	if($municipio !== "" AND $cp <= 0){
		$DDE		= $xQL->getDataRow("SELECT  `codigo_postal`,`codigo_de_estado`,`estado_colonia` FROM `general_colonias` WHERE `municipio_colonia` LIKE '%$municipio%' LIMIT 0,1");
		if(isset($DDE["codigo_postal"])){
			$cp				= $DDE["codigo_postal"];
			$idEntidadF		= $DDE["codigo_de_estado"];
			if($entidadFed ===""){
				$entidadFed	= $DDE["estado_colonia"];
			}
		}		
	}
	if($entidadFed !== "" AND $cp <= 0){
		$DDE			= $xQL->getDataRow("SELECT  `codigo_postal`,`codigo_de_estado` FROM `general_colonias` WHERE `estado_colonia` LIKE '%$entidadFed%' LIMIT 0,1");
		if(isset($DDE["codigo_postal"])){
			$cp			= $DDE["codigo_postal"];
			$idEntidadF	= $DDE["codigo_de_estado"];
		}
	}	
}

if($cp > 0 AND ($municipio == "" OR $entidadFed == "" OR $idEntidadF <=0)){
	$xCol	= new cPersonasVivCodigosPostales($cp);
	if($xCol->init() == true){
		if($entidadFed == ""){
			$entidadFed		= $xCol->getNombreEstado();
		}
		if($municipio == ""){
			$municipio		= $xCol->getNombreMunicipio();
		}
		if($idEntidadF <= 0){
			$idEntidadF		= $xCol->getClaveDeEstado();
		}
		
	}
	
}


//exit ("Acceso: $calle , EXT $numeroExt, Colonia $colonia, IDEntidadFed $idEntidadF, Entidad fed $entidadFed , Municipio $municipio, CODIGO Postal $cp");

$xSoc			= new cSocio($persona);
$clavevivienda	= 0;
if($xSoc->init() == true){
	if($calle != "" AND $numeroExt != ""){
		$referenciaDom	= "";
		$xViv	= new cPersonasVivienda($persona);
		$res = $xViv->add($calle, $numeroExt, $numeroInt, $referenciaDom, false, false, "calle", $colonia, false, false, false, true, $cp, false, false, $municipio, $entidadFed);
		if($res === false){
			$rs["error"]	= true;
		} else {
			$xViv->setActualizarCiudad($ciudad);
			$clavevivienda	= setNoMenorQueCero($xViv->getIDVivienda());
			$rs["error"]	= false;
		}
		
		
		$rs["message"]	.= $xViv->getMessages();
		
	} else {
		$rs["message"]		.= "ERROR\tDatos Invalidos $calle y $numeroExt\r\n";
	}
}

$rs["message"]		.= $xSoc->getMessages();
$rs["idvivienda"]	= $clavevivienda;
$rs["idactividad"]	= $clavevivienda; //fix excel error

header('Content-type: application/json');
echo json_encode($rs);
?>