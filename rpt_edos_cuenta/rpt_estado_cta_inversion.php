<?php
/**
 * @author 		Balam Gonzalez Luis
 * @version 	07/04/2008 1.14
 * @since 		2007-12-07
 * @package 	captacion
 *  		Modificaciones
 * 		- 07/04/2008 Agregar Formato Moneda a las Operaciones
 *		- 09/09/2014 Reescritura total
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
$xHP			= new cHPage("TR.Estado de Cuenta de Depositos plazo_fijo", HP_REPORT);
$xQl			= new MQL();
$xF				= new cFecha();

$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT); $cuenta = parametro("docto", $cuenta, MQL_INT);
$idcuenta 		= $cuenta; //parametro("docto", false, MQL_INT);
$AppByFechas	= parametro("v73", false, MQL_BOOL);		//Boolean por fechas
$out 			= parametro("out", SYS_DEFAULT);
$es_por_fechas 	= "";
$xHT			= new cHTabla();
$FechaInicial	= parametro("on", $xF->getFechaMinimaOperativa(), MQL_DATE); $FechaInicial	= parametro("fechainicial", $FechaInicial, MQL_DATE); $FechaInicial	= parametro("fecha-0", $FechaInicial, MQL_DATE); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", $xF->getFechaMaximaOperativa(), MQL_DATE); $FechaFinal	= parametro("fechafinal", $FechaFinal, MQL_DATE); $FechaFinal	= parametro("fecha-1", $FechaFinal, MQL_DATE); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);

//ini_set("display_errors", "on");

$xCuenta	= new cCuentaInversionPlazoFijo($cuenta);
$xCuenta->init();
$saldo		= $xCuenta->getSaldoActual();
$xRPT		= new cReportes();
$persona	= $xCuenta->getClaveDePersona();
$xSoc		= new cSocio($persona); $xSoc->init();

$xRPT->setTitle($xHP->getTitle(), true);

$xRPT->addContent( $xSoc->getFicha() );
$xRPT->addContent( $xCuenta->getFicha(true, "", true) );

//Datos de la Cuenta
$sqlMvtos = "SELECT
	`operaciones_tipos`.`descripcion_operacion` AS `descripcion`,
			`operaciones_mvtos`.*,
				(`operaciones_mvtos`.`afectacion_real` *
				`eacp_config_bases_de_integracion_miembros`.`afectacion`) AS 'monto',
				`eacp_config_bases_de_integracion_miembros`.`afectacion`
FROM
	`eacp_config_bases_de_integracion_miembros` 
	`eacp_config_bases_de_integracion_miembros` 
		INNER JOIN `operaciones_mvtos` `operaciones_mvtos` 
		ON `eacp_config_bases_de_integracion_miembros`.`miembro` = 
		`operaciones_mvtos`.`tipo_operacion` 
			INNER JOIN `operaciones_tipos` `operaciones_tipos` 
			ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.
			`idoperaciones_tipos`
			WHERE
			(`operaciones_mvtos`.`docto_afectado` =" . $cuenta .") AND
			(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 3200)

			ORDER BY
				`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
				`operaciones_mvtos`.`fecha_afectacion`,
				`eacp_config_bases_de_integracion_miembros`.`afectacion` DESC
			";

	$xHT->initRow();
		$xHT->addTH("TR.Fecha");
		$xHT->addTH("TR.Recibo");
		$xHT->addTH("TR.Operacion");
		$xHT->addTH("TR.Deposito");
		$xHT->addTH("TR.Retiro");
		$xHT->addTH("TR.Inversion");
		$xHT->addTH("TR.Saldo");
		$xHT->addTH("TR.Notas");
	$xHT->endRow();
	$rsmvto 		=  $xQl->getDataRecord($sqlMvtos);
	$sdo_al_corte	= 0;
	$depositos		= 0;
	$retiros		= 0;
	$inversiones	= 0;
	
	foreach ($rsmvto as $ryx) {
			$tr		= "";
			$tipoop 	= $ryx["descripcion"];
			$fecha		= $ryx["fecha_afectacion"];
			
			$sdo_al_corte	+= $ryx["monto"];

			$detallado 	= substr($ryx["detalles"], 0, 15);
			$oes 		= "";
				if ( $ryx["afectacion"] == -1){
					$oes 		= "<td>&nbsp;</td>
							<td class='mny'>" . getFMoney($ryx["afectacion_real"]) . "</td>
							<td>&nbsp;</td>";
					$retiros 	+= $ryx["afectacion_real"];
				} elseif ( $ryx["afectacion"] == 1){
					$oes = "<td class='mny'>" . getFMoney($ryx["afectacion_real"]) . "</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>";
					$depositos += $ryx["afectacion_real"];
				} else {
					$oes = "<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td class='mny'>" . getFMoney($ryx["afectacion_real"]) . "</td>";
					//$depositos = $depositos + $ryx["afectacion_real"];
					$inversiones	+= $ryx["afectacion_real"];
				}
			$tr	= "<tr>
			<td>" . getFechaMX($fecha) . "</td>
			<td>" . $ryx["recibo_afectado"] . "</td>
			<td>$tipoop</td>
			$oes

			<td class='mny'>" .getFMoney($sdo_al_corte) . "</td>
			<td>$detallado</td>
			</tr>";
			//Si Aplica por Fechas
			if ( $AppByFechas == 1  ){
				if ( $xF->getInt($fecha) > $xF->getInt($FechaFinal) OR $xF->getInt($fecha) < $xF->getInt($FechaInicial)  ){
					$tr	= "";
				}
			}
			$xHT->addRaw( $tr );
		}
		$saldo_final	= $depositos - $retiros;
		
		
	$xHT->addRaw( "<tr>
			<td>&nbsp;</td>
			<th colspan='2'>SUMATORIA TOTAL DE MOVIMIENTOS</th>
			<th>" . getFMoney($depositos) . "</th>
			<th>" . getFMoney($retiros) . "</th>
			<th>" . getFMoney($inversiones). "</th>
			<th class='mny'>" . getFMoney($saldo_final) . "</th>
			<td>&nbsp;</td>
		</tr>");
	
	if ( ( round($saldo, 2) != round($saldo_final, 2) ) ){
		if ( FORCE_CUADRE_EN_OPERACIONES == true ){
			$arrUp 	= array( "saldo_cuenta" => $saldo_final);
			$xCuenta->setUpdate($arrUp);
		}
		if ($out != OUT_EXCEL) {
			$xRPT->addContent( "<style>
				body {
					background-image: url(\"../images/error_saldos.png\");
					background-repeat: repeat;
				}
				</style>");
		}
	}

$xRPT->setOut($out);
$xRPT->addContent($xHT->get());

echo $xRPT->render(true);
?>
