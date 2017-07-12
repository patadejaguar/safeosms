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
$xHP		= new cHPage("TR.CONCENTRACION DE LA CARTERA POR FRECUENCIA", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
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
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$recibo		= parametro("recibo", 0, MQL_INT); $recibo		= parametro("idrecibo", $recibo, MQL_INT);
//===========  General
$out 			= parametro("out", SYS_DEFAULT);
$FechaInicial	= parametro("on", false, MQL_DATE); $FechaInicial	= parametro("fechainicial", $FechaInicial, MQL_DATE); $FechaInicial	= parametro("fecha-0", $FechaInicial, MQL_DATE); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false, MQL_DATE); $FechaFinal	= parametro("fechafinal", $FechaFinal, MQL_DATE); $FechaFinal	= parametro("fecha-1", $FechaFinal, MQL_DATE); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);
$ByFechas		= $xFil->OperacionesPorFecha($FechaInicial, $FechaFinal);

$sql			= "SELECT
	`creditos_periocidadpagos`.`descripcion_periocidadpagos`   AS `frecuencia`,
	EnMiles(SUM(`creditos_solicitud`.`saldo_actual`)) AS `monto`
	
	FROM
	`creditos_solicitud` `creditos_solicitud` 
		INNER JOIN `creditos_periocidadpagos` `creditos_periocidadpagos` 
		ON `creditos_solicitud`.`periocidad_de_pago` = 
		`creditos_periocidadpagos`.`idcreditos_periocidadpagos`

	GROUP BY
		`creditos_solicitud`.`periocidad_de_pago`
	HAVING `monto`  > 0
	ORDER BY `monto` DESC
";
$titulo			= "";
$archivo		= "";

$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());
//============ Reporte
$xT		= new cTabla($sql, 0);
$xT->setTipoSalida($out);
//$xT->setPrepareChart(true, $xT->CHART_PIE);

$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);

$xRPT->addContent($body);
$xRPT->addContent("<div id='idivchart'></div>");
$xCh	= new cChart("idivchart");
$xCh->addDataset($sql, "monto", "frecuencia");
$xCh->setProcess();
$xRPT->addJsCode($xCh->getJs());
$xT->setFootSum(array(1=> "monto"));

$xRPT->addContent( $xT->Show("", true, "idtbl"  ) );

$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);
?>