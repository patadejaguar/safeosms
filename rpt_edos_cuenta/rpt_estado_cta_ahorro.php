<?php
/**
 * @author 		Balam Gonzalez Luis
 * @version 	07/04/2008 1.14
 * @since 		2007-12-07
 * @package 	captacion
 *  		Modificaciones
 * 		-07/04/2008 Agregar Formato Moneda a las Operaciones
 *
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
$xHP		= new cHPage("TR.Estado de Cuenta de Depositos a la Vista", HP_REPORT);
$xQL		= new MQL();
$xF			= new cFecha();
$oficial = elusuario($iduser);


$xHP->init();

$xRPT					= new cReportes();
	

$idcuenta				= parametro("f100", false, MQL_INT);
$idcuenta 				= parametro("cuenta", $idcuenta, MQL_INT);
$AppByFechas			= parametro("f73");		//Boolean por fechas
$fecha_inicial 			= parametro("on", EACP_FECHA_DE_CONSTITUCION);
$fecha_final 			= parametro("off", $xF->getFechaMaximaOperativa());
$output					= parametro("out", SYS_DEFAULT);

	$es_por_fechas 		= "";
	

	$xCuenta		= new cCuentaALaVista($idcuenta);
	$xCuenta->init();

	//Datos de la Cuenta
	$rwc		= $xCuenta->getDatosInArray();
	
	// datos generales del socio
	$idsocio 	= $rwc["numero_socio"];	// Numero de Socio	
	$CSocio		= new cSocio($idsocio);
	$CSocio->init();
	
	$mynom 		= $CSocio->getNombreCompleto();
	$xRPT->addContent( $CSocio->getFicha() );
	//Verifica el FORZADO de el Cuadre de Saldos

	// datos de la cuenta
	$tipocuenta 	= $xCuenta->getOTipoDeCuenta()->getNombre();// eltipo("captacion_cuentastipos", $rwc[4]);
	$tasa 			= $xCuenta->getTasaActual() * 100;
	$saldo			= $rwc["saldo_cuenta"];
	$sdoact 		= number_format($xCuenta->getSaldoActual(), 2, '.', ',');
	$depositos 		= 0;
	$retiros 		= 0;
	$sdo_i 			= 0;
	$sdo_f 			= 0;
/*	echo "<hr />
	<table width='100%'>
		<tr>
		<td class='ths'>Numero de cuenta</td><td>$idcuenta</td>
		<td class='ths'>Modalidad de la Cuenta</td><td>$tipocuenta</td>
		</tr>
		<tr>
		<td class='ths'>Tasa Actual</td><td>$tasa %</td>
		<td class='ths'>Fecha de Apertura</td><td>$rwc[5]</td>
		</tr>
		<tr>
		<td class='ths'>Saldo Actual</td><td>$ $sdoact</td>
		<td class='ths'>Fecha de Ult. Afectacion</td><td>$rwc[6]</td>
		</tr>
		</table>
		<hr />";*/
	$xRPT->addContent( $xCuenta->getFicha(true) );

	//DAtOS DE LA CUENTA
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
			(`operaciones_mvtos`.`docto_afectado` =" . $idcuenta .") AND
			(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 3100)

			ORDER BY
				`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
				`operaciones_mvtos`.`fecha_afectacion`,
				`eacp_config_bases_de_integracion_miembros`.`afectacion` DESC
			";
						

	
	$rsmvto 	=  $xQL->getDataRecord($sqlMvtos);
	$xRPT->addContent( "<table width='100%' border='0'><tr>
		<th scope='col'>Fecha Afec.</th>
		<th scope='col'>Recibo</th>
		<th scope='col'>Tipo Operacion</th>
		<th scope='col'>Depositos</th>
		<th scope='col'>Retiros</th>
		<th scope='col'>Saldo</th>
		<th scope='col'>Detalles</th>
	</tr>");

		$sdo_al_corte	= 0;

		foreach ($rsmvto as $ryx) {
			$tr			= "";
			$tipoop 	= $ryx["descripcion"];
			$fecha		= $ryx["fecha_afectacion"];
			
			$sdo_al_corte	+= $ryx["monto"];

			$detallado 	= substr($ryx["detalles"], 0, 15);
			$oes 		= "";
				if ( $ryx["afectacion"] < 0){
					$oes 		= "<td>&nbsp;</td>
							<td class='mny'>" . getFMoney($ryx["afectacion_real"]) . "</td>";
					$retiros 	= $retiros + $ryx["afectacion_real"];
				} else {
					$oes = "<td class='mny'>" . getFMoney($ryx["afectacion_real"]) . "</td>
							<td>&nbsp;</td>";
					$depositos = $depositos + $ryx["afectacion_real"];
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
				if ( ( strtotime($fecha) < strtotime($fecha_inicial) ) OR ( strtotime($fecha) > strtotime($fecha_final) ) ){
					$tr	= "";
				}
			}
			$xRPT->addContent( $tr );
		}
		$saldo_final	= $depositos - $retiros;
		
		$depositos 	= getFMoney($depositos);
		$retiros 	= getFMoney($retiros);
		
	$xRPT->addContent( "<tr>
			<td>&nbsp;</td>
			<th colspan='2'>SUMATORIA TOTAL DE MOVIMIENTOS</th>
			<th>$depositos</th>
			<th>$retiros</th>
			<th class='mny'>" . getFMoney($saldo_final) . "</th>
			<td>&nbsp;</td>
		</tr>
	</table>");
	
	if ( ( round($saldo, 2) != round($saldo_final, 2) ) ){
		if ( FORCE_CUADRE_EN_OPERACIONES == true ){
			$arrUp 	= array( "saldo_cuenta" => $saldo_final);
			$xCuenta->setUpdate($arrUp);
		}
		if ($output != OUT_EXCEL) {
			$xRPT->addContent( "<style>
				body {
					background-image: url(\"../images/error_saldos.png\");
					background-repeat: repeat;
				}
				</style>");
		}
	}
				


	echo $xRPT->render(true);
	
?>