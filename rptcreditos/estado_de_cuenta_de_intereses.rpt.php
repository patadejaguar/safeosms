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
$xHP		= new cHPage("TR.Estado de Cuenta de Interes ", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
	
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);

$estatus 		= parametro("estado", SYS_TODAS);
$frecuencia 	= parametro("periocidad", SYS_TODAS);
$producto 		= parametro("convenio", SYS_TODAS);  $producto 	= parametro("producto", $producto);
$empresa		= parametro("empresa", SYS_TODAS);
$out 			= parametro("out", SYS_DEFAULT);

$FechaInicial	= parametro("on", false); $FechaInicial	= parametro("fecha-0", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("fecha-1", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);

$credito		= parametro("x", $credito, MQL_INT); $credito		= parametro("pb", $credito, MQL_INT); //compatible

$ByFecha		= "";

$sql			= "SELECT
			
				/* `operaciones_mvtos`.`socio_afectado`,
				`operaciones_mvtos`.`docto_afectado`, */
				`operaciones_mvtos`.`recibo_afectado`,
			
				`operaciones_mvtos`.`fecha_operacion` AS 'fecha_de_operacion',
				`operaciones_tipos`.`descripcion_operacion` AS 'tipo_de_operacion',
			
				(`operaciones_mvtos`.`afectacion_real` *
				`eacp_config_bases_de_integracion_miembros`.`afectacion`) AS 'monto'
			
			
			FROM
			
			
				`operaciones_mvtos` `operaciones_mvtos`
					INNER JOIN `eacp_config_bases_de_integracion_miembros`
					`eacp_config_bases_de_integracion_miembros`
					ON `operaciones_mvtos`.`tipo_operacion` =
					`eacp_config_bases_de_integracion_miembros`.`miembro`
						INNER JOIN `operaciones_tipos` `operaciones_tipos`
						ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.
						`idoperaciones_tipos`
			WHERE
				(`operaciones_mvtos`.`docto_afectado` = $credito)
				AND
				(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` =2000)
				$ByFecha
			ORDER BY
				`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
				`operaciones_mvtos`.`fecha_operacion`";
$titulo			= "";
$archivo		= "";

$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());
//Ficha
$xCred			= new cCredito($credito); $xCred->init();
$xRPT->addContent( $xCred->getFicha(true, "", true, true) );

//============ Reporte
//$xT		= new cTabla($sql, 2);
//$xT->setTipoSalida($out);


$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);

$xRPT->addContent($body);

$rs 	= $query->getDataRecord($sql);
$sumP	= 0;
$sumD	= 0;
$td		= "";

foreach ($rs as $rw ){
	$montoD		= 0;
	$montoP		= 0;

	$recibo		= $rw["recibo_afectado"];
	$operacion	= $rw["tipo_de_operacion"];
	$fecha		= $rw["fecha_de_operacion"];

	if ( $rw["monto"] < 0 ){
		$montoP		= 	$rw["monto"];
		$sumP		+= 	$rw["monto"];
	} else {
		$montoD		= 	$rw["monto"];
		$sumD		+= 	$rw["monto"];
	}

	$td		.= "
	<tr>
	<td>$recibo</td>
	<td>$fecha</td>
	<td>$operacion</td>
	<td class='mny'>" . getFMoney($montoD) . "</td>
					<td class='mny'>" . getFMoney($montoP) . "</td>
				</tr>";
	}
	$xRPT->addContent("<table width=\"100%\" align=\"center\" >
	<thead>
	<tr>
	<th width=\"15%\">Recibo</th>
	<th width=\"15%\">Fecha</th>
	<th width=\"40%\">Tipo de Operacion</th>
	<th width=\"15%\">Devengado</th>
	<th width=\"15%\">Pagado</th>
	</tr>
	</thead>
	<tbody>
	$td
	</tbody>
	<tfoot>
	<tr>
	<td />
	<th>SUMA DE NORMALES</th>
	<td class='mny'>" . getFMoney( ($sumD + $sumP) ) . "</td>
	<th class='mny'>" . getFMoney($sumD) . "</th>
	<th class='mny'>" . getFMoney($sumP) . "</th>
	</tr>
			</tfoot>
		</table>
	");

	/**
	* Iniciar con el Estado de Cuenta de Intereses Normales
	*/
	$sqlM = "SELECT

	/* `operaciones_mvtos`.`socio_afectado`,
	`operaciones_mvtos`.`docto_afectado`, */
	`operaciones_mvtos`.`recibo_afectado`,

	`operaciones_mvtos`.`fecha_operacion` AS 'fecha_de_operacion',
	`operaciones_tipos`.`descripcion_operacion` AS 'tipo_de_operacion',

	(`operaciones_mvtos`.`afectacion_real` *
	`eacp_config_bases_de_integracion_miembros`.`afectacion`) AS 'monto'


	FROM

	`operaciones_mvtos` `operaciones_mvtos`
	INNER JOIN `eacp_config_bases_de_integracion_miembros`
	`eacp_config_bases_de_integracion_miembros`
	ON `operaciones_mvtos`.`tipo_operacion` =
	`eacp_config_bases_de_integracion_miembros`.`miembro`
	INNER JOIN `operaciones_tipos` `operaciones_tipos`
	ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.
	`idoperaciones_tipos`

	WHERE
	(`operaciones_mvtos`.`docto_afectado` =$credito)
	AND
	(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` =2001)
	$ByFecha
	ORDER BY
	`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
	`operaciones_mvtos`.`fecha_operacion` ";

	$td		= "";


	$rs2 	= $query->getDataRecord($sqlM);
	$sumP	= 0;
	$sumD	= 0;
	foreach ( $rs2 as $rw ){
	$montoD		= 0;
	$montoP		= 0;

	$recibo		= $rw["recibo_afectado"];
	$operacion	= $rw["tipo_de_operacion"];
	$fecha		= $rw["fecha_de_operacion"];

	if ( $rw["monto"] < 0 ){
			$montoP		= 	$rw["monto"];
			$sumP		+= 	$rw["monto"];
	} else {
	$montoD		= 	$rw["monto"];
	$sumD		+= 	$rw["monto"];
	}

	$td		.= "
			<tr>
			<td>$recibo</td>
			<td>$fecha</td>
			<td>$operacion</td>
	<td class='mny'>" . getFMoney($montoD) . "</td>
	<td class='mny'>" . getFMoney($montoP) . "</td>
	</tr>";
	}
	$xRPT->addContent(	 "	<table width=\"100%\" align=\"center\" >
			<thead>
			<tr>
			<th width=\"15%\">Recibo</th>
			<th width=\"15%\">Fecha</th>
			<th width=\"40%\">Tipo de Operacion</th>
			<th width=\"15%\">Devengado</th>
			<th width=\"15%\">Pagado</th>
			</tr>
			</thead>
			<tbody>
			$td
			</tbody>
			<tfoot>
			<tr>
			<td />
			<th>SUMA DE MORATORIOS </th>
			<td class='mny'>" . getFMoney( ($sumD + $sumP) ) . "</td>
			<th class='mny'>" . getFMoney($sumD) . "</th>
			<th class='mny'>" . getFMoney($sumP) . "</th>
			</tr>
			</tfoot>
		</table>
	");



//$xT->setEventKey("jsGoPanel");
//$xT->setKeyField("creditos_solicitud");
//$xRPT->addContent( $xT->Show( $xHP->getTitle() ) );
//============ Agregar HTML
//$xRPT->addContent( $xHP->init($jsEvent) );
//$xRPT->addContent( $xHP->end() );


$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);
?>