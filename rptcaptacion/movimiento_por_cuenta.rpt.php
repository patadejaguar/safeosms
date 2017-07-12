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
$xHP			= new cHPage("TR.REPORTE DE ", HP_REPORT);
$xL				= new cSQLListas();
$xF				= new cFecha();
$query			= new MQL();

$subproducto 	= parametro("subproducto", SYS_TODAS, MQL_INT);
$producto 		= parametro("producto", SYS_TODAS, MQL_INT);
$operacion 		= parametro("operacion", SYS_TODAS, MQL_INT);

$out 			= parametro("out", SYS_DEFAULT);

$FechaInicial	= parametro("on", false); $FechaInicial	= parametro("fecha-0", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("fecha-1", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);

$senders		= getEmails($_REQUEST);

$xHP->init();

$sql = $xL->getListadoDeMvtosDeCaptacion(false, false, $producto, $subproducto, $FechaInicial, $FechaFinal, $operacion);



//exit($sql);

$titulo			= "";
$archivo		= "reporte-de-captacion-del-$FechaInicial-al-$FechaFinal";

$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());
//============ Reporte
$xT		= new cTabla($sql, 2);
$xT->setTipoSalida($out);

$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);

$xRPT->addContent($body);

$xT->setFootSum(array(
		4 => "monto"
));

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
/**
 * Reporte de Movimiento especifico por cuenta
 * 
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package seguimiento
 * @subpackage reports
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
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");
include_once("../core/core.common.inc.php");
include_once("../core/core.html.inc.php");

include_once("../reports/PHPReportMaker.php");

$oficial = elusuario($iduser);
//=====================================================================================================
$fecha_inicial 		= $_GET["on"];
$fecha_final 		= $_GET["off"];
$f3 				= $_GET["f3"];			//saldos no cero
$tipo_cuenta		= $_GET["f1"];			//Tipo de Cuenta ;-)
$producto			= $_GET["f2"];			//Tipo de Producto
$operacion			= $_GET["f700"];		//Tipo de Producto

$ByTipoCuenta		= "";
$ByProducto			= "";
$ByTipoMvto			= "";

if (isset($operacion) AND $operacion  != "todas"){
	$ByTipoMvto		= " AND
	(`operaciones_mvtos`.`tipo_operacion` = $operacion) ";
}

if (isset($tipo_cuenta) AND $tipo_cuenta != "todas"){
	$ByTipoCuenta	= " AND
	(`captacion_cuentas`.`tipo_cuenta` = $tipo_cuenta)";
}

if (isset($producto) AND $producto != "todas"){
	$ByProducto	= " AND
	(`captacion_cuentas`.`tipo_subproducto` = $producto) ";
}

$input 				= $_GET["out"];
	if (!$input) {
		$input = "default";
	}


	$setSql = " SELECT
	`socios_general`.`codigo`,
	CONCAT( 
	`socios_general`.`apellidopaterno`, ' ',
	`socios_general`.`apellidomaterno`, ' ' ,
	`socios_general`.`nombrecompleto`) AS 'nombre',
	`captacion_cuentas`.`numero_cuenta`,
	`captacion_cuentas`.`saldo_cuenta`,
	`operaciones_mvtos`.`tipo_operacion`,
	`operaciones_tipos`.`descripcion_operacion` AS 'tipo_de_operacion',
	SUM(`operaciones_mvtos`.`afectacion_real`)AS 'monto'

FROM
	`captacion_cuentas` `captacion_cuentas` 
		INNER JOIN `operaciones_mvtos` `operaciones_mvtos` 
		ON `captacion_cuentas`.`numero_cuenta` = `operaciones_mvtos`.
		`docto_afectado` 
			INNER JOIN `operaciones_tipos` `operaciones_tipos` 
			ON `operaciones_tipos`.`idoperaciones_tipos` = `operaciones_mvtos`.
			`tipo_operacion` 
				INNER JOIN `socios_general` `socios_general` 
				ON `captacion_cuentas`.`numero_socio` = `socios_general`.
				`codigo` 
WHERE
	(`operaciones_mvtos`.`fecha_afectacion` >='$fecha_inicial') 
	AND
	(`operaciones_mvtos`.`fecha_afectacion` <='$fecha_final')
	$ByProducto
	$ByTipoCuenta
	$ByTipoMvto
GROUP BY
	`operaciones_mvtos`.`tipo_operacion`,
	`operaciones_mvtos`.`docto_afectado` 
ORDER BY
	`operaciones_mvtos`.`tipo_operacion`,
	`captacion_cuentas`.`numero_socio`, 
	`captacion_cuentas`.`numero_cuenta`";
//			*/

if ($input!=OUT_EXCEL) {
	$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setSQL($setSql);
	$oRpt->setXML("../repository/report91.xml");
	$oOut = $oRpt->createOutputPlugin($input);
	$oRpt->setOutputPlugin($oOut);
	$oRpt->run();
} else {
	$xOb		= new cHObject();
	$xOb->setExcelType($sucursal . "-movimientos-de-captacion-$producto-$operacion-");
// 

	$cTbl = new cTabla($setSql);
	$cTbl->setWidth();
	$cTbl->Show("", false);
}
?>