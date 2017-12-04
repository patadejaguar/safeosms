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
	$xHP			= new cHPage("TR.REPORTE DE CAPTACION", HP_REPORT);
	$xL				= new cSQLListas();
	$xF				= new cFecha();
	$query			= new MQL();
	$xFil			= new cSQLFiltros();
	
	$subproducto 	= parametro("subproducto", SYS_TODAS, MQL_INT);
	$producto 		= parametro("producto", SYS_TODAS, MQL_INT);
	$operacion 		= parametro("operacion", SYS_TODAS, MQL_INT);
	
	//$empresa		= parametro("empresa", SYS_TODAS);
	$out 			= parametro("out", SYS_DEFAULT);
	
	$FechaInicial	= parametro("on", false); $FechaInicial	= parametro("fecha-0", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
	$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("fecha-1", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
	
	$senders		= getEmails($_REQUEST);
	$sucursal		= parametro("sucursal", SYS_TODAS, MQL_RAW); $sucursal		= parametro("s", $sucursal, MQL_RAW);
	
	$BySuc			= $xFil->PersonasPorSucursal($sucursal);
	
	$xHP->init();
	
	$sql 			= "SELECT
	`captacion_cuentas`.`numero_socio`,
	CONCAT(`socios_general`.`apellidopaterno`,' ',
	`socios_general`.`apellidomaterno`,' ',
	`socios_general`.`nombrecompleto`) AS `nombre`,
	`captacion_cuentas`.`numero_cuenta` AS `numero_de_cuenta`,
	`captacion_cuentastipos`.`descripcion_cuentastipos` AS `tipo_de_cuenta`,
	DATE_FORMAT(`captacion_cuentas`.`fecha_afectacion`,'%d/%m/%Y')  AS `fecha_ultima_operacion`,
	IF(`captacion_cuentas`.`tipo_cuenta`=10, '-', DATE_FORMAT(`captacion_cuentas`.`inversion_fecha_vcto`,'%d/%m/%Y')) AS  `fecha_de_vencimiento`,
	
	`captacion_cuentas`.`tasa_otorgada` AS `tasa_interes_anual`,
	
	/*MAX(`operaciones_mvtos`.`fecha_operacion`) AS `fecha`,*/
	SUM(IF(`operaciones_mvtos`.`tipo_operacion` = 220 OR `operaciones_mvtos`.`tipo_operacion` = 221, `operaciones_mvtos`.`afectacion_real`,0)) AS `capital`,
	SUM(IF(`operaciones_mvtos`.`tipo_operacion` = 222, `operaciones_mvtos`.`afectacion_real`,0)) AS `interes`
FROM
	`captacion_cuentas` `captacion_cuentas` 
		LEFT OUTER JOIN `operaciones_mvtos` `operaciones_mvtos` 
		ON `captacion_cuentas`.`numero_cuenta` = `operaciones_mvtos`.
		`docto_afectado` 
			INNER JOIN `socios_general` `socios_general` 
			ON `socios_general`.`codigo` = `captacion_cuentas`.`numero_socio` 
				INNER JOIN `captacion_cuentastipos` `captacion_cuentastipos` 
				ON `captacion_cuentas`.`tipo_cuenta` = `captacion_cuentastipos`.
				`idcaptacion_cuentastipos` 
WHERE
	(`captacion_cuentas`.`numero_socio` !=1) $BySuc
GROUP BY `captacion_cuentas`.`numero_cuenta`";
	
//setLog($sql);

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
		5 => "saldo"
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
	
?>