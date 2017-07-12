<?php
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
$xHP		= new cHPage("TR.Operaciones Bancarias", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
$xFil		= new cSQLFiltros();
$xF			= new cFecha();
$out 		= (isset($_GET["out"])) ? $_GET["out"] : SYS_DEFAULT;


$FechaInicial	= parametro("on", false); $FechaInicial	= parametro("fecha-0", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("fecha-1", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$senders		= getEmails($_REQUEST);
$out 			= parametro("out", SYS_DEFAULT);

$cuenta			= (isset($_GET["cuenta"]))  ? $_GET["cuenta"] : SYS_TODAS;
$operacion		= (isset($_GET["operacion"])) ? $_GET["operacion"] : SYS_TODAS;

$ByCuenta		= $xFil->BancosPorCuenta($cuenta);
$ByOperaciones	= $xFil->BancosPorTipoDeOperacion($operacion);


	$setSql = " SELECT
	`bancos_cuentas`.`idbancos_cuentas` AS `cuenta`,
	`bancos_cuentas`.`descripcion_cuenta`  AS `nombre`,
		`bancos_operaciones`.`tipo_operacion`  AS `operacion`,
	`bancos_operaciones`.`numero_de_documento` ,
	`bancos_operaciones`.`recibo_relacionado`,
	getFechaMX(`bancos_operaciones`.`fecha_expedicion`) AS `fecha`,
	`bancos_operaciones`.`beneficiario`,
	`bancos_operaciones`.`monto_descontado` AS `descuento`,
	`bancos_operaciones`.`monto_real`  AS `monto`
FROM
	`bancos_operaciones` `bancos_operaciones` 
		INNER JOIN `bancos_cuentas` `bancos_cuentas` 
		ON `bancos_operaciones`.`cuenta_bancaria` = 
		`bancos_cuentas`.`idbancos_cuentas`
WHERE
	(`bancos_operaciones`.`fecha_expedicion`>= '$FechaInicial' )
	AND
	(`bancos_operaciones`.`fecha_expedicion`<= '$FechaFinal' )
	$ByCuenta $ByOperaciones
	ORDER BY `bancos_cuentas`.`idbancos_cuentas`, `bancos_operaciones`.`fecha_expedicion`, `bancos_operaciones`.`tipo_operacion`
	";
	
	$sql			= $setSql;
	$titulo			= "";
	$archivo		= "";
	
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
			7 => "descuento", 8 => "monto"
	));
	//$xT->setEventKey("jsGoPanel");
	//$xT->setKeyField("creditos_solicitud");
	$xRPT->addContent( $xT->Show( ) );
	//============ Agregar HTML
	//$xRPT->addContent( $xHP->init($jsEvent) );
	//$xRPT->addContent( $xHP->end() );
	
	
	$xRPT->setResponse();
	$xRPT->setSenders($senders);
	echo $xRPT->render(true);

?>