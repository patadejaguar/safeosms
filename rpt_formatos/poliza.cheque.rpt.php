<?php
/**
 * Formato de
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
$xHP		= new cHPage("TR.REPORTE DE ", HP_RECIBO);
$xL			= new cSQLListas();
$xF			= new cFecha();
$xQL		= new MQL();
$xFil		= new cSQLFiltros();

	
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);


$xHP->init();

//===========  General
$out 			= parametro("out", SYS_DEFAULT);
$FechaInicial	= parametro("on", false); $FechaInicial	= parametro("fecha-0", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("fecha-1", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);

$xFMT			= new cFormato(70021);
$xCred			= new cCredito($credito);
$xCant			= new cCantidad();

if($xCred->init() == true){
	$xFMT->setCredito($credito, $xCred->getDatosDeCredito());
	$recibo		= $xCred->getNumeroReciboDeMinistracion();
	$xFMT->setRecibo($recibo);
	$sql		= "SELECT
	`operaciones_tipos`.`descripcion_operacion` AS `operacion`,
	SUM(`operaciones_mvtos`.`afectacion_real`)  AS `monto`/*,
	`operaciones_mvtos`.`tipo_operacion`,
	`operaciones_mvtos`.`recibo_afectado`,
	`bancos_operaciones`.`documento_de_origen`,
	`bancos_operaciones`.`fecha_expedicion`,
	`bancos_operaciones`.`tipo_operacion` */
FROM
	`operaciones_mvtos` `operaciones_mvtos` 
		INNER JOIN `operaciones_tipos` `operaciones_tipos` 
		ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.
		`idoperaciones_tipos` 
			INNER JOIN `bancos_operaciones` `bancos_operaciones` 
			ON `bancos_operaciones`.`recibo_relacionado` = `operaciones_mvtos`.
			`recibo_afectado` 
WHERE
	(`bancos_operaciones`.`documento_de_origen` =" . $credito . ") AND
	(`bancos_operaciones`.`fecha_expedicion` ='" . $xCred->getFechaDeMinistracion() .  "') AND
	(`bancos_operaciones`.`tipo_operacion` !='" . TESORERIA_PAGO_CHEQUE . "') 
GROUP BY
	`operaciones_mvtos`.`tipo_operacion`,
	`operaciones_mvtos`.`recibo_afectado` ";
	$rsDed		= $xQL->getDataRecord($sql);
	$tt			= "";
	$deducciones= 0;
	foreach ($rsDed as $rw){
		$monto	= $rw["monto"];
		$tt		.= "<tr><td>" . $rw["operacion"] . "<td><td class='mny'>" . $xCant->moneda($monto) . "</td></tr>";
		$deducciones += $monto;
	}
	$monto_cheque	= $xCred->getMontoAutorizado() - $deducciones;
	$tt			.= "<tr><th>" . $xFMT->getOLang()->getT("TR.TOTAL DEUCCIONES") . "<th><th class='mny'>" . $xCant->moneda($deducciones) . "</th></tr>";
	$tt			.= "<tr><th>" . $xFMT->getOLang()->getT("TR.MONTO DEL CHEQUE") . "<th><th class='mny'>" . $xCant->moneda($monto_cheque) . "</th></tr>";
	$tt			= "<table>$tt</table>";
}

$xFMT->setProcesarVars(array("variable_lista_de_deducciones" => $tt, "variable_cheque_monto" => $xCant->moneda($monto_cheque), "variable_cheque_en_letras_monto" => $xCant->letras($monto_cheque) ));
//cargar cuenta bancaria
//cargar id de cheque

echo $xFMT->get();

$xHP->fin();
?>