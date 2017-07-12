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
$frecuencia 	= parametro("periocidad", SYS_TODAS, MQL_INT);
$producto 		= parametro("convenio", SYS_TODAS, MQL_INT);  $producto 	= parametro("producto", $producto);
$empresa		= parametro("empresa", SYS_TODAS, MQL_INT);
$grupo			= parametro("grupo", SYS_TODAS, MQL_INT);
$sucursal		= parametro("sucursal", SYS_TODAS, MQL_RAW); $sucursal		= parametro("s", $sucursal, MQL_RAW);
$oficial		= parametro("oficial", SYS_TODAS ,MQL_INT);

$operacion		= parametro("operacion", SYS_TODAS, MQL_INT);
//$operacion		= parametro("f3", $operacion, MQL_INT);
$forma_de_pago	= parametro("tipodepago", SYS_TODAS, MQL_RAW);
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

$ByPersona		= $xL->OFiltro()->OperacionesPorPersona($persona);
$BySucursal		= $xL->OFiltro()->RecibosPorSucursal($sucursal);
$ByOperacion	= $xL->OFiltro()->OperacionesPorTipo($operacion);
$ByPago			= $xL->OFiltro()->RecibosPorTipoDePago($forma_de_pago);
$fmt			= ($output != OUT_EXCEL ) ? "getFechaMX" : ""; 

$sql			= "SELECT
				operaciones_mvtos.sucursal,
				operaciones_recibos.tipo_pago 				AS 'tipo_de_pago',
				operaciones_mvtos.socio_afectado 			AS 'numero_de_socio',
				CONCAT(socios_general.apellidopaterno, ' ', socios_general.apellidomaterno, ' ',socios_general.nombrecompleto) AS
				'nombre_completo',
				operaciones_tipos.descripcion_operacion 	AS 'tipo_de_operacion',
				$fmt(operaciones_mvtos.fecha_afectacion) 			AS 'fecha',
				`operaciones_mvtos`.`idoperaciones_mvtos`	AS `operacion`,
				`operaciones_mvtos`.`recibo_afectado`   	AS `recibo`,
				`operaciones_recibos`.`recibo_fiscal`   	AS `fiscal`,
				operaciones_mvtos.docto_afectado 			AS 'documento',
				operaciones_mvtos.afectacion_real			AS 'monto',
				operaciones_mvtos.detalles 					AS 'observaciones'
				FROM
				`socios_general` `socios_general`
				INNER JOIN `operaciones_mvtos` `operaciones_mvtos`
				ON `socios_general`.`codigo` = `operaciones_mvtos`.`socio_afectado`
					INNER JOIN `operaciones_recibos` `operaciones_recibos`
					ON `operaciones_recibos`.`idoperaciones_recibos` =
					`operaciones_mvtos`.`recibo_afectado`
						INNER JOIN `operaciones_tipos` `operaciones_tipos`
						ON `operaciones_tipos`.`idoperaciones_tipos` =
						`operaciones_mvtos`.`tipo_operacion`
				WHERE operaciones_mvtos.fecha_afectacion>='$FechaInicial' AND operaciones_mvtos.fecha_afectacion<='$FechaFinal'
					$ByPersona
					$BySucursal
					$ByOperacion
					$ByPago
			ORDER BY
				`operaciones_mvtos`.`sucursal`,
				`operaciones_recibos`.`fecha_operacion`,
				`operaciones_recibos`.`idoperaciones_recibos`,
				`operaciones_mvtos`.`idoperaciones_mvtos` ";
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
$xRPT->addCampoSuma("monto");
$xRPT->setProcessSQL();
//$xT->setEventKey("jsGoPanel");
//$xT->setKeyField("creditos_solicitud");
//$xRPT->addContent( $xT->Show(  ) );
//============ Agregar HTML
//$xRPT->addContent( $xHP->init($jsEvent) );
//$xRPT->addContent( $xHP->end() );


$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);

?>