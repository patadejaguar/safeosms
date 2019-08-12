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
$xHP		= new cHPage("TR.parcialidades pendientes de pago ", HP_REPORT);
$xL			= new cSQLListas();
$xV			= new cSQLVistas();
$xF			= new cFecha();
$query		= new MQL();
$xVals		= new cReglasDeValidacion();

	
$estatus 	= parametro("estado", SYS_TODAS);
$frecuencia = parametro("periocidad", SYS_TODAS);
$producto 	= parametro("convenio", SYS_TODAS);  $producto 	= parametro("producto", $producto);
$empresa	= parametro("empresa", SYS_TODAS);
$out 		= parametro("out", SYS_DEFAULT);

$FechaInicial	= parametro("on", false); $FechaInicial	= parametro("fecha-0", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("fecha-1", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);


$ByConvenio		= ($producto == SYS_TODAS) ? "" : " AND (`creditos_tipoconvenio`.`tipo_en_sistema` = $producto ) ";
$sql			= $xV->getVistaLetrasConNombre($FechaFinal, "", "", "", $producto);
// $xL->getListadoDeLetrasConCreditos($FechaFinal, false, "", "", $ByConvenio);
$titulo			= $xHP->getTitle();
$archivo		= "";

$xRPT			= new cReportes($titulo . "-" . $xF->getFechaCorta($FechaFinal));
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
//============ REPORTE " AND (`creditos_tipoconvenio`.`tipo_en_sistema` =" . CREDITO_PRODUCTO_INDIVIDUAL . ") "

$xT		= new cTabla($sql, 2);
$xT->setTipoSalida($out);
if(MODULO_CAPTACION_ACTIVADO == false){
	$xT->setOmitidos("ahorro");
}
$xT->setColSum("ahorro");
$xT->setColSum("capital");
$xT->setColSum("interes");
$xT->setColSum("iva");
$xT->setColSum("otros");
$xT->setColSum("letra");

$xRPT->addContent( $xRPT->getEncabezado("", $FechaInicial, $FechaFinal) );


$xRPT->addContent( $xT->Show( $xRPT->getTitle() ) );
$xRPT->setBodyMail( $xHP->getTitle() );
$xRPT->setSumarRegistros($xT->getRowCount());

//============ Agregar HTML
$xRPT->setTitle($xHP->getTitle());
$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);
?>