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
echo "var DEFAULT_GENERO		= " . DEFAULT_GENERO . ";\n";
echo "var DEFAULT_SOCIO			= " . DEFAULT_SOCIO . ";\n";
echo "var DEFAULT_CREDITO		= " . DEFAULT_CREDITO . ";\n";
echo "var DEFAULT_RECIBO		= " . setNoMenorQueCero(DEFAULT_RECIBO) . ";\n";
echo "var DEFAULT_ESTADO_CIVIL	= " . DEFAULT_ESTADO_CIVIL . ";\n";
echo "var DEFAULT_EMPRESA	= " . DEFAULT_EMPRESA . ";\n";
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
echo "var SAFE_HOST_URL = '" . SAFE_HOST_URL . "';\n";
echo "var DIGITOS_DE_CODIGO_POSTAL	= " . DIGITOS_DE_CODIGO_POSTAL . ";\n";
echo "var SYS_RETARDO	= " . SYS_RETARDO . ";\n";

echo "var EACP_CLAVE_DE_PAIS	= \"" . EACP_CLAVE_DE_PAIS . "\";\n";

echo "var FECHA_ACTUAL	= \"" . fechasys() . "\";\n";

echo "var CAPTACION_TIPO_PLAZO	= " . CAPTACION_TIPO_PLAZO . ";\n";
echo "var CAPTACION_TIPO_VISTA	= " . CAPTACION_TIPO_VISTA . ";\n";
echo "var CAPTACION_ORIGEN_CONDICIONADO	= " . CAPTACION_ORIGEN_CONDICIONADO . ";\n";

echo "var iDE_CREDITO	= " . iDE_CREDITO . ";\n";
echo "var iDE_CAPTACION	= " . iDE_CAPTACION . ";\n";
echo "var iDE_SOCIO	= " . iDE_SOCIO . ";\n";
echo "var STD_LITERAL_DIVISOR	= '" . STD_LITERAL_DIVISOR . "';\n";

echo "var SEGUIMIENTO_ESTADO_PENDIENTE = '" . SEGUIMIENTO_ESTADO_PENDIENTE . "';\n";
echo "var SEGUIMIENTO_ESTADO_CANCELADO = '" . SEGUIMIENTO_ESTADO_CANCELADO . "';\n";
echo "var SEGUIMIENTO_ESTADO_EFECTUADO = '" . SEGUIMIENTO_ESTADO_EFECTUADO . "';\n";
echo "var SEGUIMIENTO_ESTADO_VENCIDO = '" . SEGUIMIENTO_ESTADO_VENCIDO . "';\n";

echo "var CREDITO_PRODUCTO_CON_PRESUPUESTO = '" . CREDITO_PRODUCTO_CON_PRESUPUESTO . "';\n";

echo "var PERSONAS_LARGO_IDFISCAL = " . PERSONAS_LARGO_IDFISCAL . ";\n";
echo "var PERSONAS_LARGO_IDPOBLACIONAL = " . PERSONAS_LARGO_IDPOBLACIONAL . ";\n";

$ConMenores		= (PERSONAS_ACEPTAR_MENORES == true) ? "true" : "false";
echo "var PERSONAS_ACEPTAR_MENORES = " . $ConMenores . ";\n";
echo "var EDAD_PRODUCTIVA_MAXIMA = " . EDAD_PRODUCTIVA_MAXIMA . ";\n";
echo "var EDAD_PRODUCTIVA_MINIMA = " . EDAD_PRODUCTIVA_MINIMA . ";\n";

$EmpresaActiva	= (PERSONAS_CONTROLAR_POR_EMPRESA == true) ? "true" : "false";
echo "var PERSONAS_CONTROLAR_POR_EMPRESA = " . $EmpresaActiva . ";\n";

$ViviendaManual	= (PERSONAS_VIVIENDA_MANUAL == true) ? "true" : "false"; 
echo "var PERSONAS_VIVIENDA_MANUAL = " . $ViviendaManual . ";\n";

$ModuloAML		= (MODULO_AML_ACTIVADO == true) ? "true" : "false";
echo "var MODULO_AML_ACTIVADO = " . $ModuloAML . ";\n";

echo "var iDE_PRESUPUESTO = '" . iDE_PRESUPUESTO . "';\n";
echo "var BANCOS_OPERACION_CHEQUE = '" . BANCOS_OPERACION_CHEQUE . "';\n";
$xLoc	= new cLocal();
if(MODO_DEBUG == true){ echo "var MODO_DEBUG = true;\n"; } else {echo "var MODO_DEBUG = false;\n"; }

echo "var LOCAL_DOMICILIO_CLAVE_ENTIDAD	= '" . $xLoc->DomicilioEstadoClaveNum() . "';\n";
if(PERSONAS_VIVIENDA_MANUAL == true){ echo "var PERSONAS_VIVIENDA_MANUAL		= true;\n"; } else{ 	echo "var PERSONAS_VIVIENDA_MANUAL		= false;\n"; }

/*echo "var SEGUIMIENTO_ESTADO_PENDIENTE = '" . SEGUIMIENTO_ESTADO_PENDIENTE . "';\n";
echo "var SEGUIMIENTO_ESTADO_EFECTUADO = '" . SEGUIMIENTO_ESTADO_EFECTUADO . "';\n";
echo "var SEGUIMIENTO_ESTADO_CANCELADO = '" . SEGUIMIENTO_ESTADO_CANCELADO . "';\n";
echo "var SEGUIMIENTO_ESTADO_VENCIDO = '" . SEGUIMIENTO_ESTADO_VENCIDO . "';\n";*/
echo "var AML_CLAVE_MONEDA_LOCAL = '" . AML_CLAVE_MONEDA_LOCAL . "';\n";

$xB		= new cBases();
$strA	= $xB->getMembers_InString(false, BASE_ES_PERSONA_MORAL);
echo "var ARR_FIGURA_MORAL		= new Array($strA);\n";
echo "var SYS_UUID_TMP		= '" . getClaveCifradoTemporal() . "';\n";


echo "var MQL_ADD		= '" . MQL_ADD . "';\n";
echo "var MQL_DEL		= '" . MQL_DEL . "';\n";

?>
var Configuracion = {
	credito : {
		productos : {
			arrendamientopuro : <?php echo CREDITO_PRODUCTO_LEASING_PURO; ?>,
			lineas: <?php echo CREDITO_PRODUCTO_REVOLVENTES; ?>
		},
		destinos :{
			arrendamientopuro : <?php echo CREDITO_DESTINO_LEASING_PURO; ?>
		},
		etapas : {
			registrado : 1,
			atendido : 100,
			con_oficial: 101,
			con_persona : 102,
			con_credito : 103,
			
			solicitado: 99,
			autorizado: 98,
			
			por_ministrar: 501,
			
			vigente: 10,
			moroso: 30,
			vencido: 20
			
		},
		origen : {
			arrendamiento : 281,
			lineas: 295,
			nomina:290,
			precliente : 270,
			renovacion : 3,
			reestructura : 4
		},
		eventos : {
			pago : "credito-pago"
		},
		periocidad : {
			semanal: 7,
			quincenal: 15,
			catorcenal: 14,
			mensual:30
		}
	},
	opciones : {
		dialogID : "dialog.id"
	},
	rutas : {
		credito : "credito",
		panel : "panel",
		persona : "persona"
	},
	variables : {
		tinyajax : {
			callback : "tinyajax.callback",
			delay : "tinyajax.delay"
		}
	},
	personas : {
		tipoingreso : {
			otros : 900,
			usuario : 800
		}
	}
}

