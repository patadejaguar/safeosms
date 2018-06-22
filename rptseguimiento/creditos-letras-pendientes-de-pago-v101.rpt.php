<?php
/**
 * Reporte de
 *
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package seguimiento
 * @subpackage reports
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
$xHP		= new cHPage("TR.REPORTE DE LETRAS PENDIENTES DE PAGO", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
$xFil		= new cSQLFiltros();

	
$estatus 		= parametro("estado", SYS_TODAS, MQL_INT);
$frecuencia 	= parametro("periocidad", SYS_TODAS, MQL_INT);
$producto 		= parametro("convenio", SYS_TODAS, MQL_INT);  $producto 	= parametro("producto", $producto);
$empresa		= parametro("empresa", SYS_TODAS, MQL_INT);
$grupo			= parametro("grupo", SYS_TODAS, MQL_INT);
$sucursal		= parametro("sucursal", SYS_TODAS, MQL_RAW); $sucursal		= parametro("s", $sucursal, MQL_RAW);

$operacion		= parametro("operacion", SYS_TODAS, MQL_INT);
//===========  Individual
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$recibo			= parametro("recibo", 0, MQL_INT); $recibo		= parametro("idrecibo", $recibo, MQL_INT);
//===========  General
$out 			= parametro("out", SYS_DEFAULT);
$FechaInicial	= parametro("on", false); $FechaInicial	= parametro("fecha-0", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("fecha-1", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);

$BySaldo		= $xFil->CreditosPorSaldos(TOLERANCIA_SALDOS, ">");
//Agregar seguimiento
$BySaldo		= $BySaldo . $xFil->CreditosProductosPorSeguimiento(0);
//$BySaldo		= $BySaldo . $xFil->CreditosPorFechaDeMinistracion($FechaInicial, $FechaFinal);
$titulo			= $xHP->getTitle();


$idmunicipio	= parametro("municipioactivo", "");
$ByMunicipio	= $xFil->CreditosPorMunicipioAct($idmunicipio);

if($idmunicipio !== ""){
	$BySaldo	= $BySaldo . $ByMunicipio;
	$xMun		= new cDomicilioMunicipio(); $xMun->initByIDUnico($idmunicipio);
	$municipio	= $xMun->getNombre();
	$entidadfed	= $xMun->getOEstado()->getNombre();
	$titulo		= $titulo . " / Municipio : $entidadfed - $municipio";
}


$idproducto		= setNoMenorQueCero($producto);
if($idproducto > 0){
	$xProd		= new cProductoDeCredito($idproducto);
	$xProd->init();
	$titulo		.= " - " . $xProd->getNombre();
}

$sql			= $xL->getListadoDeLetrasPendientesReporteAcumV101($BySaldo, TASA_IVA, true, $empresa, $producto, $frecuencia); //Se agrega tasa IVA y TRUE para mostrar creditos con planes de pago


$xRPT			= new cReportes($titulo);
$xRPT->setFile($titulo);

$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($titulo);
//============ Reporte
$xT		= new cTabla($sql, 2);
$xT->setTipoSalida($out);


$body		= $xRPT->getEncabezado($titulo, $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);
$xRPT->addContent($body);

$xRPT->addCampoSuma("monto_ministrado");
$xRPT->addCampoSuma("capital");
$xRPT->addCampoSuma("interes");
$xRPT->addCampoSuma("iva");
$xRPT->addCampoSuma("otros");
$xRPT->addCampoSuma("letra_original");
$xRPT->addCampoSuma("moratorio");
$xRPT->addCampoSuma("iva_moratorio");
$xRPT->addCampoSuma("total");
$xRPT->addCampoSuma("ahorro");
$xRPT->addCampoContar("credito");

if(MODULO_CAPTACION_ACTIVADO == false){
	$xRPT->setOmitir("ahorrro");
}


$xRPT->setProcessSQL();

//============ Agregar HTML
//$xRPT->addContent( $xHP->init($jsEvent) );
//$xRPT->addContent( $xHP->end() );


$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);
?>