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
$xHP		= new cHPage("REPORTE DE INGRESOS POR EMPRESA ACUMULADO", HP_REPORT);
	

/**
 */
$xF		= new cFecha();
$estatus 	= (isset($_GET["estado"]) ) ? $_GET["estado"] : SYS_TODAS;
$frecuencia 	= (isset($_GET["periocidad"]) ) ? $_GET["periocidad"] : SYS_TODAS;
$convenio 	= (isset($_GET["convenio"]) ) ? $_GET["convenio"] : SYS_TODAS;
$empresa	= (isset($_GET["empresa"]) ) ? $_GET["empresa"] : SYS_TODAS;
$out 		= (isset($_GET["out"])) ? $_GET["out"] : SYS_DEFAULT;
$mx 		= (isset($_GET["mx"])) ? true : false;

$senders		= getEmails($_REQUEST);

if($mx == true){
	$FechaInicial	= (isset($_GET["on"])) ? $xF->getFechaISO( $_GET["on"]) : FECHA_INICIO_OPERACIONES_SISTEMA;
	$FechaFinal	= (isset($_GET["off"])) ? $xF->getFechaISO( $_GET["off"]) : fechasys();
} else {
	$FechaInicial	= (isset($_GET["on"])) ? $_GET["on"] : FECHA_INICIO_OPERACIONES_SISTEMA;
	$FechaFinal		= (isset($_GET["off"])) ? $_GET["off"] : fechasys();
}

$ByConvenio			= ($convenio == SYS_TODAS) ? "" : " AND	(`listado_de_ingresos`.`producto` =$convenio) ";
$ByEmpresa			= ($empresa == SYS_TODAS) ? "" : " AND (`listado_de_ingresos`.`clave_empresa` = $empresa) ";
$ByFecha			= "";


$xRPT				= new cReportes($xHP->getTitle());
//$xRPT->setFile($archivo);
$xRPT->setOut($out);

//============ Reporte



$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);
//============ Agregar HTML
//$xRPT->addContent( $xHP->init($jsEvent) );
$xRPT->addContent( $body );

//$xT->setEventKey("jsGoPanel");
//$xT->setKeyField("creditos_solicitud");

$xRPT->setTitle($xHP->getTitle());

$sql	= "
SELECT
	`listado_de_ingresos`.`clave_empresa`,
	`listado_de_ingresos`.`empresa`,
	SUM(`listado_de_ingresos`.`capital`)           AS `capital`,
	SUM(`listado_de_ingresos`.`interes_normal`)    AS `interes`,
	SUM(`listado_de_ingresos`.`interes_moratorio`) AS `moratorios`,
	SUM(`listado_de_ingresos`.`otros`)                  AS `otros` ,
	SUM(`listado_de_ingresos`.`iva`)                  AS `impuesto` ,
	SUM(`capital`+ `interes_normal`+`interes_moratorio`+`iva`+`otros`) AS 'total',
	
	SUM(CASE WHEN (`listado_de_ingresos`.`banco`  = 892360369) THEN 0 ELSE (`capital`+ `interes_normal`+`interes_moratorio`+`iva`+`otros`) END) AS 'neto'
	
	
FROM
	`listado_de_ingresos` `listado_de_ingresos`
WHERE  
(`listado_de_ingresos`.`fecha` >='$FechaInicial') AND (`listado_de_ingresos`.`fecha` <='$FechaFinal') $ByConvenio $ByEmpresa
AND (tipo_de_pago !='" . TESORERIA_COBRO_NINGUNO .  "')
AND (`listado_de_ingresos`.`clave_empresa` != '" . DEFAULT_EMPRESA . "')
		
GROUP BY
	`listado_de_ingresos`.`clave_empresa` 
	ORDER BY `listado_de_ingresos`.`empresa`
	";
//echo $sql;
$xTBL	= new cTabla($sql);
$xTBL->setTdClassByType();
$xTBL->setTipoSalida($out);
$xTBL->setFootSum(array(
			2 => "capital",
			3 => "interes",
			4 => "moratorios",
			5 => "otros",
			6 => "impuesto",
			7 => "total",
			8 => "neto"
		)
	);
$xRPT->setSQL($sql);
$xRPT->addContent( $xTBL->Show() );
$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);
?>