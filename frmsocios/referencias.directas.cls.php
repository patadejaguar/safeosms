<?php
//=====================================================================================================
//=====>	INICIO_H
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	$theFile					= __FILE__;
	$permiso					= getSIPAKALPermissions($theFile);
	if($permiso === false){		header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//<=====	FIN_H
	$iduser = $_SESSION["log_id"];
//=====================================================================================================

include_once "../core/entidad.datos.php";
include_once "../core/core.deprecated.inc.php";
include_once "../core/core.fechas.inc.php";
include_once "../core/core.common.inc.php";

$oficial = elusuario($iduser);//include_once "core/core.fechas.inc.php";
// Variables
$idsocio 	= $_GET["idsocio"];
$idsocio 	= $_POST["SocioRelacionado"];
if (!$idsocio){
	echo "<html>
			<body onload='javascript:history.back();'>
			</body>
		</html>";
	exit;
}

$documento 					= 1; //numero de credito que no existe
$tipo_de_relacion 			= parametro("relacion");
$numero_de_socio 			= parametro("idsocio");	//clave de socio
$nombres 					= parametro("nombres");
$apellido_paterno			= parametro("appaterno");
$apellido_materno 			= parametro("apmaterno");
$telefono_fijo 				= parametro("tfijo");
$telefono_movil 			= parametro("tmovil");
$fecha_de_nacimiento 			= parametro("elanno0") . "-" . parametro("elmes0") . "-" . parametro("eldia0");
$monto_relacionado 			= parametro("montorel", SYS_UNO, MQL_INT);
$porcentaje_relacionado 		= parametro("porrel",SYS_UNO, MQL_INT);
if($porcentaje_relacionado > 1){
	$porcentaje_relacionado 		= $porcentaje_relacionado / 100;
}

$calle						= parametro("idcalle");
$numero						= parametro("idnumero");
$colonia					= parametro("idcolonia");
$codigo_postal				= parametro("idcodigopostal");
$referencia					= parametro("idreferencia");
$numero_interior			= parametro("idnumero");

$curp 						= parametro("curp");
$observaciones 				= parametro("observaciones");
$consanguinidad 			= parametro("consan");
$depende 					= parametro("depende");
$ocupacion 					= parametro("ocupacion");

$rfc						= "";
$grupo_solidario			= DEFAULT_GRUPO;
$genero						= DEFAULT_GENERO;
$tipo_persona				= TIPO_JURIDICO_FISICA;

$numero_de_socio			= ($numero_de_socio == DEFAULT_SOCIO) ? false : $numero_de_socio;
$xRel						= new cSocio($numero_de_socio);

//$email					= $xRel->getCorreoElectronico();

if($xRel->existe() != true){


	$xRel->add($nombres, $apellido_paterno, $apellido_materno, $rfc, $curp, getCajaLocal(),
	   $fecha_de_nacimiento, "", TIPO_INGRESO_RELACION, DEFAULT_ESTADO_CIVIL,
	   $genero, DEFAULT_EMPRESA, DEFAULT_REGIMEN_CONYUGAL,
	   $tipo_persona, $grupo_solidario, $observaciones, 
			DEFAULT_TIPO_IDENTIFICACION, "", false, false,
		$telefono_movil, "", 0);
	if(trim($calle) != ""){
		$xRel->addVivienda($calle, $numero, $codigo_postal, "", $referencia, $telefono_fijo, $telefono_movil,
			   TIPO_DOMICILIO_PRINCIPAL, TIPO_VIVIENDA_PROPIA, TIPO_DOMICILIO_PARTICULAR, DEFAULT_TIEMPO,
			   $colonia, "calle" );
	}
	if( trim($ocupacion) != "" ){
		$xRel->addActividadEconomica("", 0, $ocupacion, DEFAULT_TIEMPO, DEFAULT_EMPRESA);
	}
	
	$xRel->init();
	$numero_de_socio	= $xRel->getCodigo();
}

$xSoc			= new cSocio($idsocio);

$xSoc->addRelacion($numero_de_socio, $tipo_de_relacion, $consanguinidad, $depende, $observaciones,
		   $monto_relacionado, $porcentaje_relacionado);	
//echo $xRel->getMessages("txt");
//echo $xSoc->getMessages("txt");
header("location:referencias.directas.frm.php?msg=OK&socio=". $idsocio);
?>