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
$xHP				= new cHPage("TR.REPORTE DE OPERACIONES POR PERSONA", HP_REPORT);
$xF					= new cFecha();
$idx				= "persona_asociada";

$oficial			= parametro("oficial", 0, MQL_INT);

$estatus 			= (isset($_GET["estado"]) ) ? $_GET["estado"] : SYS_TODAS;
$frecuencia 		= (isset($_GET["periocidad"]) ) ? $_GET["periocidad"] : SYS_TODAS;
$convenio 			= (isset($_GET["convenio"]) ) ? $_GET["convenio"] : SYS_TODAS;
$empresa			= (isset($_GET["empresa"]) ) ? $_GET["empresa"] : SYS_TODAS;
$out 				= (isset($_GET["out"])) ? $_GET["out"] : SYS_DEFAULT;
$mx 				= (isset($_GET["mx"])) ? true : false;

$senders			= getEmails($_REQUEST);


$FechaInicial	= parametro("on", $xF->getFechaMinimaOperativa(), MQL_DATE); $FechaInicial	= parametro("fechainicial", $FechaInicial, MQL_DATE); $FechaInicial	= parametro("fecha-0", $FechaInicial, MQL_DATE); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", $xF->getFechaMaximaOperativa(), MQL_DATE); $FechaFinal	= parametro("fechafinal", $FechaFinal, MQL_DATE); $FechaFinal	= parametro("fecha-1", $FechaFinal, MQL_DATE); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);


$ByConvenio			= ($convenio == SYS_TODAS) ? "" : " AND	(`listado_de_ingresos`.`producto` =$convenio) ";
$ByEmpresa			= ($empresa == SYS_TODAS) ? "" : " AND (`listado_de_ingresos`.`$idx` = $empresa) ";
$ByFecha			= "";
$ByOficial			= "";

$banco 				= parametro("cuentabancaria", SYS_TODAS);
$banco 				= parametro("cuenta", $banco);
$ByBanco			= ($banco == SYS_TODAS) ? "" : " AND (`listado_de_ingresos`.`banco`=$banco) ";
$mrkEmpresa			= ($empresa == DEFAULT_EMPRESA OR($convenio == CREDITO_PRODUCTO_INDIVIDUAL AND count($senders) >=1) ) ? "" : " `listado_de_ingresos`.`$idx`,	`listado_de_ingresos`.`empresa`, ";

$tipo_de_pago		= parametro("tipodepago", SYS_TODAS, MQL_RAW);
$tipo_de_pago		= parametro("pago", $tipo_de_pago, MQL_RAW);

$CampoTipoPago		= ", `listado_de_ingresos`.`tipo_de_pago` ";
$ByTipoDePago		= ($tipo_de_pago == "" OR $tipo_de_pago == SYS_TODAS) ? "" : " AND tipo_de_pago ='$tipo_de_pago' "; //" AND tipo_de_pago !='" . TESORERIA_COBRO_NINGUNO .  "' ";
$xRPT				= new cReportes($xHP->getTitle());
$GByTipoPago		="";
//$xRPT->setFile($archivo);
$estadisticos		= parametro("estadisticos", false, MQL_BOOL);
if($estadisticos == true){ $ByTipoDePago	.= " AND tipo_de_pago !='" . TESORERIA_COBRO_NINGUNO .  "' AND tipo_de_pago !='" . TESORERIA_COBRO_DESCTO .  "'  ";  }
if($ByTipoDePago == ""){
	$GByTipoPago	= ", `listado_de_ingresos`.`tipo_de_pago`";
}
$xRPT->setOut($out);
//============ Reporte
if($oficial != DEFAULT_USER AND $oficial > 0){
	if(CREDITO_USAR_OFICIAL_SEGUIMIENTO == true){
		$ByOficial	= " AND `oficial_de_seguimiento`=$oficial ";
	} else {
		$ByOficial	= " AND `oficial_de_credito`=$oficial ";
	}
}
$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);
//============ Agregar HTML
//$xRPT->addContent( $xHP->init($jsEvent) );
$xRPT->addContent( $body );

//$xT->setEventKey("jsGoPanel");
//$xT->setKeyField("creditos_solicitud");

$xRPT->setTitle($xHP->getTitle());
/*$sql	= "
SELECT
	$mrkEmpresa
	`listado_de_ingresos`.`codigo`,
	`listado_de_ingresos`.`nombre`,
	`listado_de_ingresos`.`fecha`,
	SUM(`listado_de_ingresos`.`capital`)            AS `capital`,
	SUM(`listado_de_ingresos`.`interes_normal`)    	AS `interes`,
	SUM(`listado_de_ingresos`.`interes_moratorio`) 	AS `moratorio`,
	SUM(`listado_de_ingresos`.`iva`)               	AS `iva`,
	SUM(`listado_de_ingresos`.`otros`)             	AS `otros` ,
	SUM(`capital`+ `interes_normal`+`interes_moratorio`+`iva`+`otros`) AS 'total',
	MAX(`listado_de_ingresos`.`parcialidad`)      	AS `parcialidad`,
	MAX(`listado_de_ingresos`.`periocidad`)			AS `periocidad`,
	MAX(`listado_de_ingresos`.`banco`)				AS `banco`
	$CampoTipoPago
FROM
	`listado_de_ingresos` `listado_de_ingresos`
WHERE
	(`listado_de_ingresos`.`fecha` >='$FechaInicial') AND (`listado_de_ingresos`.`fecha` <='$FechaFinal') $ByConvenio $ByEmpresa $ByBanco $ByTipoDePago $ByOficial
GROUP BY
	`listado_de_ingresos`.`clave_empresa`,
	`listado_de_ingresos`.`codigo`,
	`listado_de_ingresos`.`fecha`,
	`listado_de_ingresos`.`banco`
	$GByTipoPago
ORDER BY
	`listado_de_ingresos`.`fecha`,
	`listado_de_ingresos`.`clave_empresa`,
	`listado_de_ingresos`.`periocidad`,
	`listado_de_ingresos`.`nombre`
	";*/

$sql	= "SELECT
	$mrkEmpresa
	`listado_de_ingresos`.`codigo`,
	`listado_de_ingresos`.`nombre`,
	`listado_de_ingresos`.`fecha`,
	SUM(`listado_de_ingresos`.`capital`)            AS `capital`,
	SUM(`listado_de_ingresos`.`interes_normal`)    	AS `interes`,
	SUM(`listado_de_ingresos`.`interes_moratorio`) 	AS `moratorio`,
	SUM(`listado_de_ingresos`.`iva`)               	AS `iva`,
	SUM(`listado_de_ingresos`.`otros`)             	AS `otros` ,
	SUM(`capital`+ `interes_normal`+`interes_moratorio`+`iva`+`otros`) AS 'total',
	MAX(`listado_de_ingresos`.`parcialidad`)      	AS `parcialidad`,
	MAX(`listado_de_ingresos`.`periocidad`)			AS `periocidad`,
	MAX(`listado_de_ingresos`.`banco`)				AS `banco`
	$CampoTipoPago
FROM
	`listado_de_ingresos` `listado_de_ingresos`
WHERE
	(`listado_de_ingresos`.`fecha` >='$FechaInicial') AND (`listado_de_ingresos`.`fecha` <='$FechaFinal') $ByConvenio $ByEmpresa $ByBanco $ByTipoDePago $ByOficial
GROUP BY
	`listado_de_ingresos`.`$idx`,
	`listado_de_ingresos`.`codigo`,
	`listado_de_ingresos`.`fecha`,
	`listado_de_ingresos`.`banco`
	$GByTipoPago
ORDER BY
	`listado_de_ingresos`.`fecha`,
	`listado_de_ingresos`.`$idx`,
	`listado_de_ingresos`.`periocidad`,
	`listado_de_ingresos`.`nombre`
	";
//exit( $sql);
	$xTBL	= new cTabla($sql);
	$xTBL->setEventKey("jsGoEstadoDeCuentaDeCreditosPorPersona");
	$xTBL->setKeyField("codigo");
	$xTBL->setTdClassByType();
	$xTBL->setTipoSalida($out);
	$arrCSumas	= ($empresa != DEFAULT_EMPRESA) ? array( 5 => "capital", 6 => "interes", 7 => "moratorio", 8 => "iva", 9 => "otros", 10 => "total") : array( 3 => "capital", 4 => "interes", 5 => "moratorios", 6 => "iva", 7 => "otros", 8 => "total");
	$xTBL->setFootSum($arrCSumas);
	
$xRPT->setSQL($sql);
$xRPT->addContent( $xTBL->Show() );
$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);
?>