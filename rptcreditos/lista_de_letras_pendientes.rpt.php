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
$xHP		= new cHPage("TR.REPORTE DE PARCIALIDADES POR PAGAR", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
$xFil		= new cSQLFiltros();

	
$estatus 		= parametro("estado", SYS_TODAS);
$frecuencia 	= parametro("periocidad", SYS_TODAS);
$producto 		= parametro("convenio", SYS_TODAS);  $producto 	= parametro("producto", $producto);
$empresa		= parametro("empresa", SYS_TODAS);
$grupo			= parametro("grupo", SYS_TODAS, MQL_INT);
$sucursal		= parametro("sucursal", SYS_TODAS, MQL_RAW);

$operacion		= parametro("operacion", SYS_TODAS, MQL_INT);
//===========  Individuales
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
//===========  General
$out 			= parametro("out", SYS_DEFAULT);
$FechaInicial	= parametro("on", false); $FechaInicial	= parametro("fecha-0", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("fecha-1", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);

$xCred			= new cCredito($credito);
$xCred->init();

$sql			= $xL->getListadoDeLetrasPendientes($credito, $xCred->getTasaIVAOtros(), $xCred->getPagosSinCapital());
$titulo			= "";
$archivo		= "";

$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());
//============ Reporte

$xRPT->addContent($xCred->getFicha(true, "", false, true));
$xT				= new cTabla($sql, 2);
$xT->setFechaCorte($FechaFinal);
$xT->setTipoSalida($out);

	$xT->setOmitidos("codigo_de_base");
	$xT->setOmitidos("socio_afectado");
	$xT->setOmitidos("persona");
	$xT->setOmitidos("credito");
	$xT->setOmitidos("docto_afectado");
	$xT->setOmitidos("periodo_socio");
	$xT->setOmitidos("fecha_de_vencimiento");
	$xT->setOmitidos("tasa_de_mora");
	$xT->setOmitidos("mora");
	$xT->setOmitidos("capital_exigible");
	$xT->setOmitidos("interes_exigible");
	$xT->setOmitidos("otros_exigible");
	$xT->setOmitidos("ahorro_exigible");
	$xT->setOmitidos("iva_exigible");
	$xT->setOmitidos("total_sin_otros");
	$xT->setOmitidos("clave_otros");
	$xT->setOmitidos("int_corriente");
	$xT->setOmitidos("int_corriente_letra");
	//$xT->setOmitidos("letra");
	
	$xT->setForzarTipoSQL("dias", "int");
	
	$arrSum	= array(
			2 => "capital", 3 => "interes", 4 => "iva", 5 => "interes_moratorio", 6=>"iva_moratorio", 8=> "otros", 9=> "letra",10=> "neto"
	);
	if(MODULO_CAPTACION_ACTIVADO == true){
		$arrSum[5] = "ahorro";
		$arrSum[6] = "interes_moratorio";
		$arrSum[7] = "iva_moratorio";
		$arrSum[9] = "otros";
		$arrSum[10] = "letra";
		$arrSum[11] = "neto";
	} else {
		$xT->setOmitidos("ahorro");
		
	}
	$xT->setUsarNullPorCero();
	
	
	$xT->setFootSum($arrSum);
	


$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);

$xRPT->addContent($body);

//$xT->setEventKey("jsGoPanel");
//$xT->setKeyField("creditos_solicitud");
$xRPT->addContent( $xT->Show(  ) );
//============ Agregar HTML
//$xRPT->addContent( $xHP->init($jsEvent) );
//$xRPT->addContent( $xHP->end() );


$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);
?>