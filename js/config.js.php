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
include_once("../core/core.deprecated.inc.php");
include_once("../core/entidad.datos.php");
include_once("../core/core.config.inc.php");
$strOrder 	= (isset($_GET["o"])) ? $_GET["o"] : "";
$NewOrder 	= (isset($_GET["x"])) ? $_GET["x"] : "";
$cmdOrder 	= array();
$form		= "";
$subpath	= "";
header("Content-type:text/javascript");

echo "var TIPO_INGRESO_GRUPO	= " . TIPO_INGRESO_GRUPO . ";\n";
echo "var TIPO_INGRESO_RELACION	= " . TIPO_INGRESO_RELACION . ";\n";
echo "var DEFAULT_GENERO	= " . DEFAULT_GENERO . ";\n";
echo "var DEFAULT_SOCIO		= " . DEFAULT_SOCIO . ";\n";
echo "var DEFAULT_ESTADO_CIVIL	= " . DEFAULT_ESTADO_CIVIL . ";\n";
echo "var ESTADO_CIVIL_CASADO	= " . ESTADO_CIVIL_CASADO . ";\n";
echo "var CREDITO_ESTADO_AUTORIZADO	= " . CREDITO_ESTADO_AUTORIZADO . ";\n";
echo "var CREDITO_ESTADO_VIGENTE	= " . CREDITO_ESTADO_VIGENTE . ";\n";
echo "var CREDITO_ESTADO_SOLICITADO	= " . CREDITO_ESTADO_SOLICITADO . ";\n";
echo "var TESORERIA_MAXIMO_CAMBIO	= " . TESORERIA_MAXIMO_CAMBIO . ";\n";
echo "var DEFAULT_CUENTA_BANCARIA	= " . DEFAULT_CUENTA_BANCARIA . ";\n";
echo "var FALLBACK_CUENTA_BANCARIA	= " . FALLBACK_CUENTA_BANCARIA . ";\n";
echo "var FALLBACK_CLAVE_EMPRESA	= " . FALLBACK_CLAVE_EMPRESA . ";\n";

echo "var SYS_AUTOMATICO		= '" . SYS_AUTOMATICO . "';\n";
echo "var TESORERIA_COBRO_TRANSFERENCIA		= '" . TESORERIA_COBRO_TRANSFERENCIA . "';\n";
echo "var TESORERIA_COBRO_EFECTIVO		= '" . TESORERIA_COBRO_EFECTIVO . "';\n";
echo "var SVC_REMOTE_HOST		= '" . SVC_REMOTE_HOST . "';\n";
echo "var CREDITO_TIPO_DIAS_DE_PAGO_PERSONALIZADOS		= '" . CREDITO_TIPO_DIAS_DE_PAGO_PERSONALIZADOS . "';\n";
echo "var CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO		= " . CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO . ";\n";
echo "var CREDITO_TIPO_PERIOCIDAD_DIARIO	= " . CREDITO_TIPO_PERIOCIDAD_DIARIO . ";\n";
echo "var TESORERIA_MONTO_MAXIMO_OPERADO		= " . TESORERIA_MONTO_MAXIMO_OPERADO . ";\n";

echo "var CREDITO_TIPO_PAGO_UNICO		= " . CREDITO_TIPO_PAGO_UNICO. ";\n";
echo "var CREDITO_TIPO_PAGO_PERIODICO		= " . CREDITO_TIPO_PAGO_PERIODICO . ";\n";

echo "var DIGITOS_DE_CODIGO_POSTAL	= " . DIGITOS_DE_CODIGO_POSTAL . ";\n";

echo "var EACP_CLAVE_DE_PAIS	= \"" . EACP_CLAVE_DE_PAIS . "\";\n";


echo "var CAPTACION_TIPO_PLAZO	= " . CAPTACION_TIPO_PLAZO . ";\n";
echo "var CAPTACION_ORIGEN_CONDICIONADO	= " . CAPTACION_ORIGEN_CONDICIONADO . ";\n";

echo "var iDE_CREDITO	= " . iDE_CREDITO . ";\n";
echo "var iDE_CAPTACION	= " . iDE_CAPTACION . ";\n";
echo "var iDE_SOCIO	= " . iDE_SOCIO . ";\n";
echo "var STD_LITERAL_DIVISOR	= '" . STD_LITERAL_DIVISOR . "';\n";
$xLoc	= new cLocal();

echo "var LOCAL_DOMICILIO_CLAVE_ENTIDAD	= '" . $xLoc->DomicilioEstadoClaveNum() . "';\n";
if(PERSONAS_VIVIENDA_MANUAL == true){ echo "var PERSONAS_VIVIENDA_MANUAL		= true;\n"; } else{ 	echo "var PERSONAS_VIVIENDA_MANUAL		= false;\n"; }


$xB		= new cBases();
$strA	= $xB->getMembers_InString(false, BASE_ES_PERSONA_MORAL);
echo "var ARR_FIGURA_MORAL		= new Array($strA);\n";
?>
