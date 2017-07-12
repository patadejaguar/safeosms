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
$xHP		= new cHPage("REPORTE DE PAGOS PENDIENTES POR SEGURO DE GASTOS MEDICOS", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$xQL		= new MQL();
$xFil		= new cSQLFiltros();

	
$estatus 		= parametro("estado", SYS_TODAS, MQL_INT);
$frecuencia 	= parametro("periocidad", SYS_TODAS, MQL_INT);
$producto 		= parametro("convenio", SYS_TODAS, MQL_INT);  $producto 	= parametro("producto", $producto);
$empresa		= parametro("empresa", SYS_TODAS, MQL_INT);
$grupo			= parametro("grupo", SYS_TODAS, MQL_INT);
$sucursal		= parametro("sucursal", SYS_TODAS, MQL_RAW); $sucursal		= parametro("s", $sucursal, MQL_RAW);
$oficial		= parametro("oficial", SYS_TODAS ,MQL_INT);

$operacion		= parametro("operacion", SYS_TODAS, MQL_INT);
//===========  Individual
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$recibo			= parametro("recibo", 0, MQL_INT); $recibo		= parametro("idrecibo", $recibo, MQL_INT);
//===========  General
$out 			= parametro("out", SYS_DEFAULT);
$FechaInicial	= parametro("on", $xF->getFechaMinimaOperativa(), MQL_DATE); $FechaInicial	= parametro("fechainicial", $FechaInicial, MQL_DATE); $FechaInicial	= parametro("fecha-0", $FechaInicial, MQL_DATE); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", $xF->getFechaMaximaOperativa(), MQL_DATE); $FechaFinal	= parametro("fechafinal", $FechaFinal, MQL_DATE); $FechaFinal	= parametro("fecha-1", $FechaFinal, MQL_DATE); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);

//$ByPersona		= ($persona > DEFAULT_SOCIO) ? " AND `socios_general`.`codigo`=$persona " : "";

/*$sql			= "SELECT
	`tmp_personas_aport_cal`.`persona`,
	CONCAT(`socios_general`.`nombrecompleto`,' ',
	`socios_general`.`apellidopaterno`,' ',
	`socios_general`.`apellidomaterno`) AS `nombre`,
	`socios_general`.`curp`,
	`socios_general`.`fechaalta` AS `fecha_de_alta`,
	`socios_cajalocal`.`descripcion_cajalocal` AS `caja_local`,
	`socios_region`.`descripcion_region`       AS `region`,
	
	`tmp_personas_aport_cal`.`tipo`,
	`tmp_personas_aport_cal`.`enero`,
	`tmp_personas_aport_cal`.`febrero`,
	`tmp_personas_aport_cal`.`marzo`,
	`tmp_personas_aport_cal`.`abril`,
	`tmp_personas_aport_cal`.`mayo`,
	`tmp_personas_aport_cal`.`junio`,
	`tmp_personas_aport_cal`.`julio`,
	`tmp_personas_aport_cal`.`agosto`,
	`tmp_personas_aport_cal`.`septiembre`,
	`tmp_personas_aport_cal`.`octubre`,
	`tmp_personas_aport_cal`.`noviembre`,
	`tmp_personas_aport_cal`.`diciembre` 
FROM
	`socios_general` `socios_general` 
		INNER JOIN `socios_cajalocal` `socios_cajalocal` 
		ON `socios_general`.`cajalocal` = `socios_cajalocal`.
		`idsocios_cajalocal` 
			INNER JOIN `tmp_personas_aport_cal` `tmp_personas_aport_cal` 
			ON `tmp_personas_aport_cal`.`persona` = `socios_general`.`codigo` 
				INNER JOIN `socios_region` `socios_region` 
				ON `socios_general`.`region` = `socios_region`.`idsocios_region`
WHERE
`tmp_personas_aport_cal`.`tipo_de_operacion` = 5103";
*/

$sql			= "SELECT
	`tmp_personas_aport_cal`.`persona`,
	CONCAT(`socios_general`.`nombrecompleto`,' ',
	`socios_general`.`apellidopaterno`,' ',
	`socios_general`.`apellidomaterno`) AS `nombre`,
	`socios_general`.`curp`,
	`socios_general`.`fechaalta` AS `fecha_de_alta`,
	`socios_cajalocal`.`descripcion_cajalocal` AS `caja_local`,
	`socios_region`.`descripcion_region`       AS `region`,
	
	
	SUM((`tmp_personas_aport_cal`.`enero`*`tmp_personas_aport_cal`.`tipo`)) AS `enero`,
	SUM((`tmp_personas_aport_cal`.`febrero`*`tmp_personas_aport_cal`.`tipo`)) AS `febrero`,
	SUM((`tmp_personas_aport_cal`.`marzo`*`tmp_personas_aport_cal`.`tipo`)) AS `marzo`,
	SUM((`tmp_personas_aport_cal`.`abril`*`tmp_personas_aport_cal`.`tipo`)) AS `abril`,
	SUM((`tmp_personas_aport_cal`.`mayo`*`tmp_personas_aport_cal`.`tipo`)) AS `mayo`,
	SUM((`tmp_personas_aport_cal`.`junio`*`tmp_personas_aport_cal`.`tipo`)) AS `junio`,
	SUM((`tmp_personas_aport_cal`.`julio`*`tmp_personas_aport_cal`.`tipo`)) AS `julio`,
	SUM((`tmp_personas_aport_cal`.`agosto`*`tmp_personas_aport_cal`.`tipo`)) AS `agosto`,
	SUM((`tmp_personas_aport_cal`.`septiembre`*`tmp_personas_aport_cal`.`tipo`)) AS `septiembre`,
	SUM((`tmp_personas_aport_cal`.`octubre`*`tmp_personas_aport_cal`.`tipo`)) AS `octubre`,
	SUM((`tmp_personas_aport_cal`.`noviembre`*`tmp_personas_aport_cal`.`tipo`)) AS `noviembre`,
	SUM((`tmp_personas_aport_cal`.`diciembre`*`tmp_personas_aport_cal`.`tipo`)) AS `diciembre`

FROM
	`socios_general` `socios_general` 
		INNER JOIN `socios_cajalocal` `socios_cajalocal` 
		ON `socios_general`.`cajalocal` = `socios_cajalocal`.
		`idsocios_cajalocal` 
			INNER JOIN `tmp_personas_aport_cal` `tmp_personas_aport_cal` 
			ON `tmp_personas_aport_cal`.`persona` = `socios_general`.`codigo` 
				INNER JOIN `socios_region` `socios_region` 
				ON `socios_general`.`region` = `socios_region`.`idsocios_region`
WHERE
`tmp_personas_aport_cal`.`tipo_de_operacion` = 5103

GROUP BY `tmp_personas_aport_cal`.`persona`, `tmp_personas_aport_cal`.`tipo_de_operacion`";
$titulo			= "";
$archivo		= "";

$init			= 5;
$arrSum			= array(
		$init+1 => "enero",
		$init+2 => "febrero",
		$init+3 => "marzo",
		$init+4 => "abril",
		$init+5 => "mayo",
		$init+6 => "junio",
		$init+7 => "julio",
		$init+8 => "agosto",
		$init+9 => "septiembre",
		$init+10 => "octubre",
		$init+11 => "noviembre",
		$init+12 => "diciembre"
		);

$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());
//============ Reporte
$xT		= new cTabla($sql, 2);
$xT->setTipoSalida($out);
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