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
$xHP		= new cHPage("TR.REPORTE DE ", HP_REPORT);
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
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$recibo		= parametro("recibo", 0, MQL_INT); $recibo		= parametro("idrecibo", $recibo, MQL_INT);
//===========  General
$out 			= parametro("out", SYS_DEFAULT);
$FechaInicial	= parametro("on", false); $FechaInicial	= parametro("fecha-0", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("fecha-1", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);


$sql			= "SELECT * FROM socios LIMIT 0,100";
$titulo			= "";
$archivo		= "";

$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());

//=============== Personas por genero

//=============== Personas por Nacionalidad

//=============== Personas por Moral/Fisica

//=============== Personas Con Credito Moral/Fisica


//============ Reporte
$xT				= new cTabla($sql, 2);
$xT->setTipoSalida($out);
$body			= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
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
exit;
?>
<?php


/**
 * @author Balam Gonzalez Luis
 * @version 1.2
 * @since 2007-06-01
 * 
 * Changes:
 * 05/05/2008 Reescritura
 */
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
include_once "../libs/sql.inc.php";
include_once "../core/core.fechas.inc.php";
//include_once "../libs/graph.oo.php";
include_once "../core/core.config.inc.php";
include_once "../core/core.common.inc.php";
include_once "../libs/open_flash_chart_object.php";

$oficial = elusuario($iduser);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Ficha de Informacion de Sucursal</title>
</head>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
<body >
<!--  onLoad="javascript:window.print();"-->
<?php
echo getRawHeader();
/**
 * Filtrar si hay Fecha
 */
$fecha_inicial 		= $_GET["on"];
$fecha_final 		= $_GET["off"];


$BySuc				= "";
$mSuc				= ( isset($_GET["s"]) ) ? $_GET["s"] : "todas";
$mSuc				= ( $mSuc	== "todas" ) ? getSucursal() : $mSuc;
if($mSuc!="todas"){
	$BySuc			= " 	AND
	(`creditos_solicitud`.`sucursal` ='$mSuc') ";
}


?>
<!-- -->
<table       >
	<thead>
		<tr>
			<td class="subtitle">REPORTE DE CIFRAS GENERALES POR SUCURSAL</td>
		</tr>
<!-- DATOS GENERALES DEL REPORTE  -->
		<tr>
			<td width="60%">&nbsp;</td>
			<td width="20%">Fecha de Elaboracion:</td>
			<td width="20%"><?php echo fecha_larga(); ?></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>Preparado por:</td>
			<td><?php echo $oficial; ?></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>Sucursal</td>
			<td><?php echo $mSuc ; ?></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>Fecha Inicial:</td>
			<td><?php echo fecha_corta($fecha_inicial) ; ?></td>
		</tr>
								<tr>
			<td>&nbsp;</td>
			<td>Fecha Final</td>
			<td><?php echo fecha_corta($fecha_final) ; ?></td>
		</tr>
	</thead>
</table>
<?php
$cSuc		= new cSucursal($mSuc);
//Socios
	$lbl	= array();
	$val	= array();

$W1		= "
	AND
	(`socios_general`.`fechaalta` >='$fecha_inicial')
	AND
	(`socios_general`.`fechaalta` <='$fecha_final')
	";
$DSG	= $cSuc->getSociosTotales($W1);
$lbl[]		= "Socios";
$val[]		= $DSG["socios"];
//Socios.- Hombres
$DSG	= $cSuc->getSociosTotales($W1 . " AND (`socios_general`.`genero` =1)");
$lbl[]		= "Hombres";
$val[]		= $DSG["socios"];
//Socios.- Mujeres
$DSG	= $cSuc->getSociosTotales($W1 . " AND (`socios_general`.`genero` =2)");
$lbl[]		= "Mujeres";
$val[]		= $DSG["socios"];
//Socios.- Otros
$DSG	= $cSuc->getSociosTotales($W1 . " AND (`socios_general`.`genero` =99)");
$lbl[]		= "Otros";
$val[]		= $DSG["socios"];

//graficos
	$x = new SAFEChart();
	$x->setValues($val);
	$x->setLabels($lbl);
	$x->setTitle("CLAVES GENERALES DE PERSONAS");
	$mFile	= $x->Chart3DBar(1000);

open_flash_chart_object( 648, 512, $mFile, true, "../" );

//====================================================================================================

//CAPTACION.
	$lblMC	= array();
	$valMC	= array();

	$lblNC	= array();
	$valNC	= array();

$W2			= "	AND
				(`captacion_cuentas`.`saldo_cuenta` >0)
				AND
				((`captacion_cuentas`.`fecha_apertura` >='$fecha_inicial')
				AND
				(`captacion_cuentas`.`fecha_apertura` <='$fecha_final')) ";

$DCapTot	= $cSuc->getCaptacionTotal($W2);
$lblNC[]		= "Cuentas";
$valNC[]		= $DCapTot["numero"];

$lblMC[]		= "Tot. Captacion";
$valMC[]		= round( ($DCapTot["monto"] / 1000), 2);
//Capatcion.- A la Vista
$DCapTot	= $cSuc->getCaptacionTotal($W2 . " AND (`captacion_cuentas`.`tipo_cuenta` =10)");
$lblNC[]		= "A_La_Vista";
$valNC[]		= $DCapTot["numero"];

$lblMC[]		= "Tot. Vista";
$valMC[]		= round( ($DCapTot["monto"] / 1000), 2);
//Inversiones
$DCapTot	= $cSuc->getCaptacionTotal($W2 . " AND (`captacion_cuentas`.`tipo_cuenta` =20)");
$lblNC[]		= "Inversion";
$valNC[]		= $DCapTot["numero"];

$lblMC[]		= "Tot. Inversion";
$valMC[]		= round( ($DCapTot["monto"] / 1000), 2);

	$x = new SAFEChart();
	$x->setValues($valNC);
	$x->setLabels($lblNC);
	$x->setTitle("NUMERO DE CUENTAS INGRESADAS DE CAPTACION");
	$mFileC	= $x->Chart3DBar(1000);

open_flash_chart_object( 648, 512, $mFileC, true, "../" );
//====================================================================================================
//Chart2
	$x2 = new SAFEChart();
	$x2->setValues($valMC);
	$x2->setLabels($lblMC);
	$x2->setTitle("CIFRAS DE CAPTACION EN MILES");
	$mFile2	= $x2->Chart3DBar(10000);

open_flash_chart_object( 648, 512, $mFile2, true, "../" );

//====================================================================================================

//CREDITOS
	$lblMD	= array();
	$valMD	= array();

	$lblND	= array();
	$valND	= array();

$W3			= "	AND
				(`creditos_solicitud`.`saldo_actual` >0)
				AND
				(`creditos_solicitud`.`fecha_ministracion` >= '$fecha_inicial')
				AND
				(`creditos_solicitud`.`fecha_ministracion` <= '$fecha_final')
";

$DCreds	= $cSuc->getColocacionTotal($W3);
$lblND[]		= "Creditos";
$valND[]		= $DCreds["numero"];

$lblMD[]		= "Tot. Solicitado";
$valMD[]		= round( ($DCreds["solicitado"] / 1000), 2);
$lblMD[]		= "Tot. Ministrado";
$valMD[]		= round( ($DCreds["ministrado"] / 1000), 2);
$lblMD[]		= "Tot. Insoluto";
$valMD[]		= round( ($DCreds["saldo"] / 1000), 2);


	$x2 = new SAFEChart();
	$x2->setValues($valMD);
	$x2->setLabels($lblMD);
	$x2->setTitle("CIFRAS GENERALES DE CREDITOS(MILES)");
	$mFile2	= $x2->Chart3DBar(20000);

open_flash_chart_object( 648, 512, $mFile2, true, "../" );

echo getRawFooter();
?>
</body>
</html>