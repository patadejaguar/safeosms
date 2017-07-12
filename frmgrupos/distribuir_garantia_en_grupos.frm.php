<?php
/**
 * @see
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package
 * 		-
 *		-
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
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");
include_once("../core/core.captacion.inc.php");

require_once("." . TINYAJAX_PATH . "/TinyAjax.php");

$oficial = elusuario($iduser);
function jsaGetNombreGrupo($idgrupo){
	$x = new cGrupo($idgrupo);
	return $x->getNombre();

}

$jxc = new TinyAjax();
$jxc ->exportFunction('jsaGetNombreGrupo', array('idgrupo'), "#idNombreDelGrupo");
$jxc ->process();

$action = $_GET["a"];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<body>

<?php
if ( !isset($action) ){
//$jsb	= new jsBasicForm("", iDE_CAPTACION);
//$jsb->show();
$jxc ->drawJavaScript(false, true);
?>

<form name="frmRGrupo" method="POST" action="distribuir_garantia_en_grupos.frm.php?a=1">
<fieldset>
	<legend><script> document.write(document.title); </script></legend>
	<table border='0' width='100%'  >
		<tbody>
		<tr>
			<td>Numero de Grupo</td>
			<td><input type='text' name="idgrupo" value='' id="idgrupo" onchange="jsaGetNombreGrupo()" maxlength="4" size="6" /></td>
			<td>Nombre del Grupo</td>
			<td><input type='text' name="cNombreDelGrupo" value='' id="idNombreDelGrupo" disabled="true" size="45" /></td>
		</tr>
		<tr>
			<td>Simular</td>
			<th colspan="3"> <?php
				echo cBoolSelect("solo_simular");
			?>
			</th>
		</tr>
		<tr>
			<th colspan="4"><a class="button" onclick="document.frmRGrupo.submit()">&nbsp;&nbsp;&nbsp;Enviar&nbsp;&nbsp;&nbsp;</a></th>
		</tr>
		</tbody>
	</table>
</fieldset>
</form>
<?php
} elseif ($action == 1) {
	//Mostrar Informacion
	$grupo		= $_POST["idgrupo"];
	$afectar	= $_POST["solo_simular"];
	$messages	= "";

	if ( isset($grupo) ){
	$xc 		= new cGrupo($grupo);
	$codigo_rep	= $xc->getRepresentanteCodigo();
		if ($codigo_rep == 1 OR $codigo_rep == 0 OR !isset($codigo_rep)  OR $codigo_rep == "" ){
			echo "<p class='warn'>Clave de Persona de la representante Invalido</p>";
		} else {
			//mostrar un Array de Grupos son sus integrantes con Credito
				//seleccionar el credito actual con saldo que sea grupal
				$sqlG = "SELECT
						`creditos_solicitud`.*,
						`creditos_tipoconvenio`.*
					FROM
						`creditos_solicitud` `creditos_solicitud`
							INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio`
							ON `creditos_solicitud`.`tipo_convenio` = `creditos_tipoconvenio`.
							`idcreditos_tipoconvenio`
					WHERE
						(`creditos_tipoconvenio`.`tipo_en_sistema` =" . CREDITO_PRODUCTO_GRUPOS . ") AND
						(`creditos_solicitud`.`numero_socio` = $codigo_rep) AND
						(`creditos_solicitud`.`saldo_actual` >=" . TOLERANCIA_SALDOS . ")
					ORDER BY
						`creditos_solicitud`.`fecha_autorizacion` DESC
						LIMIT 0,1";
						//echo $sqlG;
							$DC 		= obten_filas($sqlG);
							$autorizado	= $DC["monto_autorizado"];
							$tasa_gtia	= $DC["porciento_garantia_liquida"];
							$credito	= $DC["numero_solicitud"];
							//Estimar la garantia liquida
							$monto_gtia	= $autorizado * $tasa_gtia;
							//Obtener la Planeacion de credito
							$sqlRplan	= "SELECT MAX(numero_socio) AS 'socio', MAX(idoperaciones_recibos) AS 'recibo',
												MAX(fecha_operacion) AS 'fecha'  FROM operaciones_recibos
												WHERE
												(grupo_asociado = $grupo)
												AND
												(tipo_docto=14)";
							$DRec		= obten_filas($sqlRplan);
							if ($DRec["socio"] != $codigo_rep){
								echo "<p class='warn'>La Clave de Persona de la representante(" . $codigo_rep .") <br />
										No concide con el Numero de la Planeacion (" . $DRec["socio"] .") <br />
										Segun la Planeacion " . $DRec["recibo"] ." </p>";
							} else {
									//retirar de la cuenta 50
									//obtener recibo de la garantia

									if ( $solo_simular == 0){
										$messages .= "Efectuar Retiro de la Socia $codigo_rep <br /> ";
									$sqlSumGtiaLiquida = "SELECT
														`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
														`eacp_config_bases_de_integracion_miembros`.`miembro`,
														`operaciones_mvtos`.`docto_afectado`,
														SUM( (`eacp_config_bases_de_integracion_miembros`.`afectacion` *
														`operaciones_mvtos`.`afectacion_real`) ) AS 'monto'
													FROM
														`operaciones_mvtos` `operaciones_mvtos`
															INNER JOIN `eacp_config_bases_de_integracion_miembros`
															`eacp_config_bases_de_integracion_miembros`
															ON `operaciones_mvtos`.`tipo_operacion` =
															`eacp_config_bases_de_integracion_miembros`.`miembro`
													WHERE
														(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` =2500)
														AND
														(`operaciones_mvtos`.`docto_afectado` = $credito)
													GROUP BY
														`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
														`operaciones_mvtos`.`docto_afectado` ";

														$DG	= obten_filas($sqlSumGtiaLiquida);
											$GDocto			= $DG["docto_afectado"];
											$monto			= $DG["monto"];
											$GDate			= date("Y-m-d");
										//=============================retirar de garantia Liquida
										if ($monto > 0){
												$xr = new cReciboDeOperacion(17);
											$xr->setNuevorecibo($codigo_rep, $GDocto, $GDate, 1, 17, "GENERADO_AUTOMATICAMENTE_POR_$iduser",
												"NA", "ninguno", "NA", $grupo);
											$xr->setNuevoMvto($GDate, $monto, 353, 1, "GENERADO_AUTOMATICAMENTE_POR_$iduser", 1,
																TM_CARGO);
											$xr->setFinalizarRecibo();
											//Mensages
											$messages .= $xr->getMessages() . "<br />";
										}

										//===============================Retirar el Ahorro
										//Buscar Ahorro
										$xCR = new cCuentaALaVista("50" . $codigo_rep . "01");
										$exist = $xCR->setContarCuentaBySocio($codigo_rep, 50);
										//Si el Ahorro exisre proseguir en el retiro
											if ($exist >= 1){
												$messages .= "Efectuar Retiro de Ahorro de la Socia $codigo_rep <br /> ";

												$sqlSCta	= "SELECT
																`captacion_cuentas`.*
															FROM
																`captacion_cuentas` `captacion_cuentas`
															WHERE
																(`captacion_cuentas`.`numero_socio` = $codigo_rep)
																AND
																(`captacion_cuentas`.`tipo_subproducto` = 50)
															LIMIT 0,1";

															$RDG = obten_filas($sqlSCta);
															$RCta	= $RDG["numero_cuenta"];
															$RMonto	= $RDG["saldo_cuenta"];

															$xNC	= new cCuentaALaVista($RCta);
															$xNC->setSocioTitular($codigo_rep);
															$xNC->setRetiro($RMonto, "NA", "ninguno", "NA", "MVTO_AUTOMATICO_POR_$iduser",
																			$grupo);

															$messages .= $xNC->getMessages() . "<br />";
																$messages .= "Se retiro de la Socia $codigo_rep un Monto de $RMonto<br /> ";
											} else{
												$messages .= "No hay Cuenta de Retiro de la Socia $codigo_rep <br /> ";
											}
									}
								//=================================================Otras Cuentas
								$recibo		= $DRec["recibo"];
								$sqlPlan	= "SELECT * FROM operaciones_mvtos
												WHERE recibo_afectado = $recibo
												AND
												tipo_operacion = 112";
								$arrDatos	= array();
								//echo $sqlPlan;

								$rsPlan		= mysql_query($sqlPlan, cnnGeneral());
								$sumPlan	= 0;
								$td			= "";

									while ( $rw = mysql_fetch_array($rsPlan)){
										//$arrDatos[ $rw["socio_afectado"] ]	= $rw["afectacion_real"];

										$sumPlan							+= $rw["afectacion_real"];

										$monto_gtia_individual				= $rw["afectacion_real"] * $tasa_gtia;
										$NSocio								= getNombreSocio($rw["socio_afectado"]);
										//imprimir socio + monto de los planeado + monto de la garantia
										$c	= "";
										if ($rw["socio_afectado"] == $codigo_rep ){
											$c	= " class='aviso' ";
										}
										$td .= "<tr>
													<td $c>" . $rw["socio_afectado"] . "</td>
													<td>" . $NSocio . "</td>
													<td class='mny'>" . $rw["afectacion_real"] . "</td>
													<td class='mny'>" . $monto_gtia_individual . "</td>
												</tr>";
											if ($solo_simular == 0){
												$cuenta	= "50" . $rw["socio_afectado"] . "01";
												$xCN = new cCuentaALaVista($cuenta);

												//Contar las cuentas si existen
												$ctas = $xCN->setContarCuentaBySocio($rw["socio_afectado"], 50);
													if ($ctas == 0){
														//Crear una Cuenta Nueva
														$xCN->setNuevaCuenta(3,50, $rw["socio_afectado"], "ALTA_AUTOMATICA_POR_$iduser",
															 $credito, "", "", $grupo);
													} else {
														$xCN->initCuentaByCodigo();
													}
												//Agregar el Movimiento
												$xCN->setSocioTitular($rw["socio_afectado"]);
												$xCN->setDeposito($monto_gtia_individual, "NA", "ninguno", "NA",
																"ALTA_AUTOMATICA_POR_$iduser", $grupo);
												$messages	.= $xCN->getMessages() . "<br />";
											}
									}
								//Imprimir TABLA
								echo "<table width='100%' align='center'>
										<thead>
										<tr>
											<th>Numero de <br />Socio</th>
											<th>Nombre del <br />Socio</th>
											<th>Monto Prestado</th>
											<th>% de garantia Liquida</th>
										</tr>
										<tbody>
											$td
										</tbody>
										</thead>
									</table>	";
								if ($sumPlan <> $autorizado ){
									echo "<p class='warn'>El Monto del Credito ($autorizado) No coincide con la Division de Montos ($sumPlan)<br />
										Segun recibo $recibo</p>";
								} else {



								} 	//end if suma plan
							}		//end if comp representante

		}							//end if representante invalido
	}
}
echo "<p class='aviso'>" . $messages . "</p>";
?>
</body>
<script  >
</script>
</html>
