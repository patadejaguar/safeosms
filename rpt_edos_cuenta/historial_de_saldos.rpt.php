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
$xHP		= new cHPage("TR.Historial de Saldos", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
$xFil		= new cSQLFiltros();

$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT); $credito = parametro("pb", $credito, MQL_INT);
$FechaInicial	= parametro("on", false); $FechaInicial	= parametro("fecha-0", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("fecha-1", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$senders		= getEmails($_REQUEST);
$out 			= parametro("out", SYS_DEFAULT);

$titulo			= "";
$archivo		= "";
$sql			= "SELECT
	`creditos_sdpm_historico`.`idcreditos_sdpm_historico` AS `control`,
	`creditos_sdpm_historico`.`numero_de_socio`,
	`creditos_sdpm_historico`.`numero_de_credito`,
	`creditos_sdpm_historico`.`fecha_anterior`,
	`creditos_sdpm_historico`.`fecha_actual`,

	
	`creditos_sdpm_historico`.`estatus`,	
	`creditos_sdpm_historico`.`tipo_de_operacion`,
	`creditos_sdpm_historico`.`dias_transcurridos`,
	`creditos_sdpm_historico`.`saldo`,
	`creditos_sdpm_historico`.`monto_calculado`,

	`creditos_sdpm_historico`.`interes_normal`,
	`creditos_sdpm_historico`.`interes_moratorio`
FROM
	`creditos_sdpm_historico` `creditos_sdpm_historico` 
WHERE
	(`creditos_sdpm_historico`.`numero_de_credito` =$credito)
ORDER BY
	`creditos_sdpm_historico`.`fecha_anterior` ASC, `creditos_sdpm_historico`.`saldo` DESC";

$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());

//============ Reporte
$xT		= new cTabla($sql, 2);
$xT->setTipoSalida($out);
$xT->setFootSum(array( 
		7 => "dias_transcurridos",
		9 => "monto_calculado",
		8 => "saldo",
		10 => "interes_normal" ,
		11 => "interes_moratorio"
));
$xCred		= new cCredito($credito);
if($xCred->init() == true){
	$xRPT->addContent($xCred->getFicha(true, "", true, true));
}

$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);

$xRPT->addContent($body);


$xRPT->addContent( $xT->Show( ) );
//============ Agregar HTML

$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);

?>