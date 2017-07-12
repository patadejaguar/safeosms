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
$xHP		= new cHPage("TR.REPORTE DE ", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$xQL		= new MQL();
$xFil		= new cSQLFiltros();


$estatus 		= parametro("estado", SYS_TODAS, MQL_INT);
$frecuencia 	= parametro("periocidad", SYS_TODAS, MQL_INT); $frecuencia 	= parametro("frecuencia", $frecuencia, MQL_INT);
$producto 		= parametro("convenio", SYS_TODAS, MQL_INT); $producto 	= parametro("producto", $producto);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo			= parametro("grupo", SYS_TODAS, MQL_INT);
$sucursal		= parametro("sucursal", SYS_TODAS, MQL_RAW); $sucursal		= parametro("s", $sucursal, MQL_RAW);
$oficial		= parametro("oficial", SYS_TODAS ,MQL_INT);

$TipoDePago		= parametro("tipodepago", SYS_TODAS, MQL_RAW); $TipoDePago	= parametro("formadepago", $TipoDePago, MQL_RAW); $TipoDePago	= parametro("pago", $TipoDePago, MQL_RAW);

$operacion		= parametro("operacion", SYS_TODAS, MQL_INT); $operacion = parametro("tipodeoperacion", $operacion, MQL_INT);
//===========  Individual
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$recibo		= parametro("recibo", 0, MQL_INT); $recibo		= parametro("idrecibo", $recibo, MQL_INT);
//===========  General
$out 			= parametro("out", SYS_DEFAULT);

$FechaInicial	= parametro("on", $xF->getFechaMinimaOperativa(), MQL_DATE); $FechaInicial	= parametro("fechainicial", $FechaInicial, MQL_DATE); $FechaInicial	= parametro("fecha-0", $FechaInicial, MQL_DATE); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", $xF->getFechaMaximaOperativa(), MQL_DATE); $FechaFinal	= parametro("fechafinal", $FechaFinal, MQL_DATE); $FechaFinal	= parametro("fecha-1", $FechaFinal, MQL_DATE); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);


$sql			= $xL->getListadoDeFlujoEfvoCred($credito);
$titulo			= "";
$archivo		= "";

$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());
//============ Reporte

$xT				= new cTabla($sql);
$xT->setTipoSalida($out);
$xT->setFootSum(array(6 => "neto") );

$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);
//$xRPT->addContent($body);
//
$xCred			= new cCredito($credito); $xCred->init();
$xPer			= new cSocio($xCred->getClaveDePersona()); $xPer->init();

$xUser			= new cSystemUser();
$nombreoficial	= $xUser->getNombreCompleto();
$nombrepersona	= $xPer->getNombreCompleto();
$ley_art134_i	= "";

$xRPT->addContent($xPer->getFicha() );
$xRPT->addContent( $xT->Show() );
$suma		= $xT->getFieldsSum("neto");

$txt		= "<hr>
<table><tr>
<th>CAPACIDAD DE PAGO DIARIA:</th>
<td>$ $suma</td>
</tr></table> <p>Declaro Bajo protesta de Decir Verdad, que los Datos contenidos en la presente <b>DECLARACION DE FLUJO DE EFECTIVO</b>
son Verdad y que Servira para que Cubra el credito Solicitado con Numero de Control <b>$credito</b>; Teniendo Conocimiento a
las Dispocisiones de Ley, contenidas en el Articulo 134 Fraccion I de la Ley de Ahorro y Credito Popular, que indica <b>$ley_art134_i</b>
a quienes Incurren en falsedad de Informacion para Obtener un Prestamo</p>

<hr><table border='0' width='100%'>
<tr>
<td><center>Firma del Solicitante<br>
Bajo Protesta de Decir Verdad</center></td>
<td><center>Recibe la Solicitud</center></td>
</tr>
<tr>
<td><br><br><br></td>
</tr>
<tr>
<td><center>$nombrepersona</center></td>
<td><center>$nombreoficial</center></td>
</tr>
</table>";
$xRPT->addContent($txt);

//============ Agregar HTML


$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);

?>