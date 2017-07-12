<?php

/**
 * Modulo para agregar Personas.- Arrendadora
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
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); $fecha = parametro("fecha", $fecha, MQL_DATE); 
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto		= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo		= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$observaciones	= parametro("idobservaciones");
$letra			= parametro("letra", false, MQL_INT);
$completo		= parametro("completo");
$codigo			= parametro("codigo");
$centro			= parametro("centro");
$sucursal		= parametro("sucursal", getSucursal(), MQL_STRING);
$sucursal		= strtolower($sucursal);

$rs				= array();
$rs["error"]	= true;
$rs["message"]	= "";
$notas			= parametro("notas");
$notas			= trim($notas);

$primerapellido	= parametro("pa");
$segundoapellido= parametro("sa");
$nombre			= parametro("nm");
if($completo !== ""){
	$xImp->setNombreCompleto($completo);
	$primerapellido	= $xImp->getPrimerAp();
	$segundoapellido= $xImp->getSegundoAp();
	$nombre			= $xImp->getNombre();
	//exit("$primerapellido $segundoapellido $nombre");
}

$genero			= parametro("genero");
$genero			= $xImp->getGenero($genero);
$rfc			= parametro("rfc");
$rfc			= $xImp->getRFC($rfc);

$curp			= parametro("curp");


$lugarnac		= parametro("ln"); //Lugar de Nacimiento
$fechanac		= parametro("fn",fechasys(), MQL_DATE); //Lugar de Nacimiento
$estadocivil	= parametro("ec");
$estadocivil	= $xImp->getEstadoCivil($estadocivil);
$ocupacion		= parametro("ocupacion");
$ocupacion		= $xImp->setOcupacion($ocupacion);

$datosIdent		= parametro("identificacion",0,MQL_INT);
$telefono		= parametro("tel", 0, MQL_INT);
$email			= parametro("email", "", MQL_RAW);

$figuraJur		= ($segundoapellido == "" AND $primerapellido == "")? PERSONAS_FIGURA_MORAL : PERSONAS_FIGURA_FISICA;

$clavedecentro	= DEFAULT_CAJA_LOCAL;
if($centro !== ""){
	$DCL		= $xQL->getDataRow("SELECT `idsocios_cajalocal` FROM `socios_cajalocal` WHERE `descripcion_cajalocal` LIKE '%$centro%' LIMIT 0,1");
	if(isset($DCL["idsocios_cajalocal"])){
		$clavedecentro	= $DCL["idsocios_cajalocal"];
	}
}
if(PERSONAS_CONTROLAR_POR_APORTS == true AND $ocupacion !== ""){
	$DDL	= $xQL->getDataRow("SELECT `idpersonas_zclasificacion` FROM `personas_zclasificacion` WHERE `descripcion_zclasificacion` LIKE '%$ocupacion%' LIMIT 0,1");
	if(!isset($DDL["idpersonas_zclasificacion"])){
		$xPx	= new cPersonas_zclasificacion();
		$xPx->descripcion_zclasificacion($ocupacion);
		$xPx->idpersonas_zclasificacion("NULL");
		$xPx->query()->insert()->save();
	}
}
/*
AceNombre				--
AceNombre2				--
AceApellidoPat			--
AceApellidoMat			--
AceSexo					--
AceNombreCompleto		XX
AceRfc					--
AceDomicilio			--
AceNacionalidad			??
AceEdoCivil				--
AceOcupacion			--
AceLugarNac				--
AceFechaNac				--
AceDiaNac				XX
AceMesNac				XX
AceAñoNac				XX
AceFormaId				--

AceColonia				--
AceEstado				XX
AceCveEdo				--
AceCiudad				--
AceCP					--

AceDomCompleto			XX

AceLetraAñoNac			XX
AceDiaNacLetra			XX
AceNacLetra				XX
AceTel1					--
*/
/*
AcmDenominacion
AcmRfc
AcmDomicilio
AcmColonia
AcmEstado
AcmCveEDO
AcmCiudad
AcmCP
//======================== Representante
AcmNombreRepresenta
AcmDomicilioCompletoRepresenta
AcmNacionalidadRepresenta
AcmEdoCivilRepresenta
AcmOcupacionRepresenta
AcmLugarNacRepresenta
AcmFechaNacRepresenta
AcmDiaNacRepresenta					XX
AcmMesNacRepresenta					XX
AcmAñoNacRepresenta					XX
AcmFormaIdRepresenta

AcmColoniaRepresenta
AcmEstadoRepresenta
AcmCveEdoRepresenta
AcmCiudadRepresenta
AcmCPRepresenta
AcmDiaEscConstLetra
AcmDiaEscPoderLetra
AcmAñoNacRepresentaLetra
AcmDiaNacRepresentaLetra
AcmEscConstLetra
AcmEscPoderLetra
AcmNRepLetra

AcmNoEscConst
AcmLetraNoEscConst
AcmFechaEscConst
AcmDiaEscConst						XX
AcmMesEscConst						XX
AcmAñoEscConst						XX
AcmLetraAñoEscConst					XX
//--------------------------------- Notario
AcmNombreNotEscConst
AcmNumeroNotEscConst
AcmLetraNotEscConst

AcmCdEdoEscConst
AcmDatosRegEscConst
AcmPlazaRegEscConst
AcmNoEscPoder
AcmLetraEscPoder
AcmFechaEscPoder
AcmDiaEscPoder						XX
AcmMesEscPoder						XX
AcmAñoEscPoder						XX
AcmLetraAñoEscPoder					XX
AcmNombreNotEscPoder
AcmNumeroNotEscPoder
AcmLetraNotEscPoder
AcmCdEdoEscPoder					XX
AcmDatosRegPoder					XX
AcmPlazaRegPoder					XX
 */
//========================= Persona Moral

$xSoc		= new cSocio(false);
$clavepersona			= 0;
$tipoidentificacion		= 1;
$xSoc->setOmitirAML(true);

if($primerapellido != "" OR $segundoapellido != "" OR $nombre != ""){
	$resultado = $xSoc->add($nombre, $primerapellido, $segundoapellido, $rfc, $curp, $clavedecentro, $fechanac, $lugarnac, DEFAULT_TIPO_INGRESO, $estadocivil, $genero, FALLBACK_CLAVE_EMPRESA, 
			DEFAULT_REGIMEN_CONYUGAL, $figuraJur, DEFAULT_GRUPO, $observaciones, $tipoidentificacion, $datosIdent, $codigo, $sucursal, $telefono, $email,0, $fecha, AML_PERSONA_BAJO_RIESGO, "", 
			EACP_CLAVE_DE_PAIS, DEFAULT_REGIMEN_FISCAL, $ocupacion);
	if($resultado === false){
		
	} else { 
		//agregar Domicilio
		if($xSoc->init() == true){
			if($notas !== ""){
				$xSoc->addMemo(MEMOS_TIPO_PENDIENTE, $notas);
				
			}
			$clavepersona	= $xSoc->getClaveDePersona();
			$rs["error"]	= false;
		}
	}
} else {
	$clavepersona	= 0;
	$rs["message"]		.= "ERROR\tNombres No validos ($primerapellido - $segundoapellido - $nombre)\r\n";
}
$rs["message"]		.= $xSoc->getMessages();
$rs["persona"]		= $clavepersona;

header('Content-type: application/json');
echo json_encode($rs);
?>