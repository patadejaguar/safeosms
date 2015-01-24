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
$xHP		= new cHPage("TR.REPORTE DE PERSONAS CON ACTIVIDADES DE ALTO RIESGO", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
	
$estatus 		= parametro("estado", SYS_TODAS);
$frecuencia 	= parametro("periocidad", SYS_TODAS);
$producto 		= parametro("convenio", SYS_TODAS);  $producto 	= parametro("producto", $producto);
$empresa		= parametro("empresa", SYS_TODAS);
$sucursal		= parametro("sucursal", SYS_TODAS, MQL_RAW);
$out 			= parametro("out", SYS_DEFAULT);

$FechaInicial	= parametro("on", false); $FechaInicial	= parametro("fecha-0", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("fecha-1", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);


$sql			= "SELECT
	`socios`.`codigo`,
	`socios`.`nombre`,
	`personas_actividad_economica_tipos`.`clave_de_actividad`,
	`personas_actividad_economica_tipos`.`nombre_de_la_actividad`,
	`socios_aeconomica`.`ae_clave_de_localidad` AS `localidad`,
	`socios_aeconomica`.`ae_codigo_postal`      AS `codigo_postal`,
	`socios_aeconomica`.`monto_percibido_ae` AS `ingreso_mensual`,
	`personas_actividad_economica_tipos`.`nivel_de_riesgo` 
FROM
	`socios_aeconomica` `socios_aeconomica` 
		INNER JOIN `socios` `socios` 
		ON `socios_aeconomica`.`socio_aeconomica` = `socios`.`codigo` 
			INNER JOIN `personas_actividad_economica_tipos` 
			`personas_actividad_economica_tipos` 
			ON `socios_aeconomica`.`tipo_aeconomica` = 
			`personas_actividad_economica_tipos`.`clave_interna` 
WHERE
	(`personas_actividad_economica_tipos`.`nivel_de_riesgo` >=50)";
$titulo			= "";
$archivo		= "";

$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());
//============ Reporte
$xT		= new cTabla($sql, 1);
$xT->setTipoSalida($out);


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