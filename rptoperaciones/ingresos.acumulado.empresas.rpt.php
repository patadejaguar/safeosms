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
	
$idx		= "persona_asociada";

/**
 */
$xF			= new cFecha();
$estatus 	= (isset($_GET["estado"]) ) ? $_GET["estado"] : SYS_TODAS;
$frecuencia = (isset($_GET["periocidad"]) ) ? $_GET["periocidad"] : SYS_TODAS;
$convenio 	= (isset($_GET["convenio"]) ) ? $_GET["convenio"] : SYS_TODAS;
$empresa	= (isset($_GET["empresa"]) ) ? $_GET["empresa"] : SYS_TODAS;
$out 		= (isset($_GET["out"])) ? $_GET["out"] : SYS_DEFAULT;
$mx 				= (isset($_GET["mx"])) ? true : false;
$estadisticos		= parametro("estadisticos", false, MQL_BOOL);

$senders			= getEmails($_REQUEST);

$FechaInicial		= parametro("on", $xF->getFechaMinimaOperativa(), MQL_DATE); $FechaInicial	= parametro("fechainicial", $FechaInicial, MQL_DATE); $FechaInicial	= parametro("fecha-0", $FechaInicial, MQL_DATE); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal			= parametro("off", $xF->getFechaMaximaOperativa(), MQL_DATE); $FechaFinal	= parametro("fechafinal", $FechaFinal, MQL_DATE); $FechaFinal	= parametro("fecha-1", $FechaFinal, MQL_DATE); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);

$ByConvenio			= ($convenio == SYS_TODAS) ? "" : " AND	(`listado_de_ingresos`.`producto` =$convenio) ";
$ByEmpresa			= ($empresa == SYS_TODAS) ? "" : " AND (`listado_de_ingresos`.`$idx` = $empresa) ";
$ByFecha			= "";

$tipo_de_pago		= parametro("tipodepago", SYS_TODAS, MQL_RAW);
$tipo_de_pago		= parametro("pago", $tipo_de_pago, MQL_RAW);
$ByTipoDePago		= ($tipo_de_pago == "" OR $tipo_de_pago == SYS_TODAS) ? "" : " AND tipo_de_pago ='$tipo_de_pago' ";

if($estadisticos == true){ $ByTipoDePago	.= " AND tipo_de_pago !='" . TESORERIA_COBRO_NINGUNO .  "' AND tipo_de_pago !='" . TESORERIA_COBRO_DESCTO .  "'  ";  }

$banco 				= parametro("cuentabancaria", SYS_TODAS, MQL_INT);
$banco 				= parametro("cuenta", $banco, MQL_INT);
$banco				= setNoMenorQueCero($banco);
$ByBanco			= ($banco <= 0) ? "" : " AND (`listado_de_ingresos`.`banco`=$banco) ";
if($banco <= 0 AND setNoMenorQueCero(BANCOS_CUENTA_OMITIDA) > 0){
	$ByBanco		= " AND (`listado_de_ingresos`.`banco` != " . BANCOS_CUENTA_OMITIDA . ") ";
}

$xRPT				= new cReportes($xHP->getTitle());
//$xRPT->setFile($archivo);
$xRPT->setOut($out);



$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);
//============ Agregar HTML
//$xRPT->addContent( $xHP->init($jsEvent) );
$xRPT->addContent( $body );

//$xT->setEventKey("jsGoPanel");
//$xT->setKeyField("creditos_solicitud");

$xRPT->setTitle($xHP->getTitle());

///*,	SUM(CASE WHEN (`listado_de_ingresos`.`banco`  = 892360369) THEN 0 ELSE (`capital`+ `interes_normal`+`interes_moratorio`+`iva`+`otros`) END) AS 'neto'*/
$sql	= "
SELECT
	`listado_de_ingresos`.`$idx`,
	`listado_de_ingresos`.`empresa`,
	SUM(`listado_de_ingresos`.`capital`)           AS `capital`,
	SUM(`listado_de_ingresos`.`interes_normal`)    AS `interes`,
	SUM(`listado_de_ingresos`.`interes_moratorio`) AS `moratorio`,
	SUM(`listado_de_ingresos`.`otros`)                  AS `otros` ,
	SUM(`listado_de_ingresos`.`iva`)                  AS `impuestos` ,
	SUM(`capital`+ `interes_normal`+`interes_moratorio`+`iva`+`otros`) AS 'total'
	
	
	
FROM
	`listado_de_ingresos`
WHERE  
(`listado_de_ingresos`.`fecha` >='$FechaInicial') AND (`listado_de_ingresos`.`fecha` <='$FechaFinal') $ByConvenio $ByEmpresa
AND (`listado_de_ingresos`.`$idx` != '" . DEFAULT_EMPRESA . "')
$ByTipoDePago
/*AND ( (`tipo_de_pago` !='" . TESORERIA_COBRO_NINGUNO .  "')  OR (tipo_de_pago !='" . TESORERIA_COBRO_DESCTO .  "'))*/

$ByBanco
	
GROUP BY
	`listado_de_ingresos`.`$idx` 
	ORDER BY `listado_de_ingresos`.`empresa`
	";
//exit($sql);
$xTBL	= new cTabla($sql);
$xTBL->setTdClassByType();
$xTBL->setTipoSalida($out);
//			8 => "neto"
$xTBL->setFootSum(array(
			2 => "capital",
			3 => "interes",
			4 => "moratorio",
			5 => "otros",
			6 => "impuestos",
			7 => "total"
		)
	);
$xRPT->setSQL($sql);
$xRPT->addContent( $xTBL->Show() );
$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);
?>