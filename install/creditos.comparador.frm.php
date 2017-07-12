<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 0.0.01
 * @package core
 * @subpackage templates
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
$xHP		= new cHPage("", HP_FORM);
$xQL		= new MQL();
$xLog		= new cCoreLog();
$xF			= new cFecha();
$xEm		= new cPlanDePagosGenerador();
$xT			= new cTipos();
$fechaop	= $xF->get();
ini_set("max_execution_time", 600);

class cTmp {
	public $INTEGRADO				= 2;
	public $CAPITAL					= 3;
	public $IDCREDITO				= 1;
	public $INTERES					= 4;
}
$xTmp		= new cTmp();
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

echo $xHP->init();



$ByType	= "";
$xFRM	= new cHForm("frmfirmas", "creditos.comparador.frm.php?action=" . SYS_UNO);
$xLog	= new cCoreLog();

$xFRM->setTitle($xHP->getTitle());
$msg	= "";
if($action == SYS_NINGUNO){
	$xFRM->OHidden("MAX_FILE_SIZE", "1024000");
	$xFRM->OFile("idarchivo","",  "TR.Archivo");
	$xFRM->OCheck("TR.Comparar Capital", "idcomparar");
	$xFRM->OCheck("TR.Comparar Exigibles", "idexigibles");
	$xFRM->OCheck("TR.Actualizar Saldos de Capital", "idajustar");
	$xFRM->OCheck("TR.Generar Plan", "idconplan");
	$xFRM->OCheck("TR.Efectuar Ajustes", "idajustes");
	
	$xFRM->addObservaciones();
	$xFRM->addSubmit();
} else {
	$doc1				= (isset($_FILES["idarchivo"])) ? $_FILES["idarchivo"] : false;
	$observaciones		= parametro("idobservaciones", "");
	$delimiter			= parametro("idlimitador", "|");
	$comparar			= parametro("idcomparar", false, MQL_BOOL);
	$exigibles			= parametro("idexigibles", false, MQL_BOOL);
	$ajustar			= parametro("idajustar", false, MQL_BOOL);
	$conplan			= parametro("idconplan", false, MQL_BOOL);
	$ajustarok			= parametro("idajustes");
	$xFil				= new cFileImporter();
	$xFil->setCharDelimiter($delimiter);
	//if($auditados	== true){
		$xQL->setRawQuery("UPDATE `creditos_solicitud` SET `saldo_conciliado`=(`saldo_actual`+
				 
				(`interes_normal_devengado`-`interes_normal_pagado`)+(`interes_moratorio_devengado`-`interes_moratorio_pagado`) +`gastoscbza`-`bonificaciones` +
				(((`interes_normal_devengado`-`interes_normal_pagado`)+(`interes_moratorio_devengado`-`interes_moratorio_pagado`) +`gastoscbza`-`bonificaciones`)*0.16) 
				)");
		/*$xQL->setRawQuery("UPDATE `creditos_solicitud` SET `saldo_conciliado`=(`saldo_actual`+
				(`interes_normal_devengado`-`interes_normal_pagado`)+(`interes_moratorio_devengado`-`interes_moratorio_pagado`) +`gastoscbza`-`bonificaciones` 
				)");*/
		$xQL->setRawQuery("SET @fecha_de_corte='$fechaop';");
		$xQL->setRawQuery("CALL `sp_correcciones`()");
		//reconstruir db de pagos
		$xQL->setRawQuery("CALL `proc_historial_de_pagos` ");
		$xQL->setRawQuery("CALL `proc_creditos_a_final_de_plazo`()");
		$xQL->setRawQuery("CALL `proc_creditos_abonos_por_mes`()");		//una vez por mes
		$xQL->setRawQuery("CALL `proc_personas_extranjeras`()");		//personas extranjeras
		$xQL->setRawQuery("CALL `proc_listado_de_ingresos` ");
		//$ql->setRawQuery("CALL `proc_historial_de_pagos` ");
		$xQL->setRawQuery("CALL `sp_clonar_actividades` ");
		$xQL->setRawQuery("CALL `proc_perfil_egresos_por_persona` ");
		$xQL->setRawQuery("CALL `proc_creditos_letras_pendientes` ");
		$xQL->setRawQuery("CALL `sp_tabla_cal_aports`() ");
		$xQL->setRawQuery("CALL `tmp_personas_aport_cal`() ");
		$xQL->setRawQuery("CALL `sp_correcciones`()");
	//}
	//var_dump($_FILES["f1"]);
	if($xFil->processFile($doc1) == true){
		$data				= $xFil->getData();
		$linea				= 0;
		foreach($data as $valores => $cont){
			$xFil->setDataRow($cont);
			$credito	= $xFil->getEntero($xTmp->IDCREDITO);
			$integrado	= $xFil->getFlotante($xTmp->INTEGRADO, 0);
			$capital	= $xFil->getFlotante($xTmp->CAPITAL, 0);
			$integrado	= setNoMenorQueCero($integrado,2);
			$capital	= setNoMenorQueCero($capital,2);
			
			if($integrado < TOLERANCIA_SALDOS){ $integrado = 0; }
			if($capital < TOLERANCIA_SALDOS){  $capital = 0; } 
			$xCred		= new cCredito($credito);
			
			if($xCred->init() == true){
				$persona		= $xCred->getClaveDePersona();
				if($ajustar == true){
					$abono		= $xCred->getSaldoActual() - $capital;
					$xCred->setAbonoCapital($abono, SYS_UNO, DEFAULT_CHEQUE, TESORERIA_COBRO_EFECTIVO, DEFAULT_RECIBO_FISCAL, "", false, SYS_FECHA_DE_MIGRACION, false);
					$xCred->init();
				}
				if($exigibles == true){
					$sistemaInt		= $xCred->getSaldoIntegrado(false, false);
					$diferencia		= $sistemaInt - $integrado;
					$diferencia		= round($diferencia,2);
					if($diferencia > TOLERANCIA_SALDOS){
						$xLog->add("ERROR\tINTEGRADO\t$persona\t$credito\tMAYOR\t$diferencia\tEl Saldo INTEGRADO en Sistema(" . $xCred->getSaldoAuditado()  . ") es Mayor a $integrado por $diferencia\r\n");
						//eliminar diferencias
						if($ajustarok == true){
						//======== Abono de Interes Moratorio
						if($diferencia > 0){
							$mora		= $xCred->getInteresMoratorioDev() - $xCred->getInteresMoratorioPagado();
							$mora		= setNoMenorQueCero($mora);
							if($mora > 0){
								 //238 500
								 if($diferencia <= $mora){
								 	$mora		= $diferencia;
								 	$diferencia	= 0;
								 } else {
								 	$diferencia	= $diferencia - $mora;
								 }
								 
								 $xPag		= new cCreditosPagos($xCred->getNumeroDeCredito());
								 if($xPag->init($xCred->getDatosInArray()) == true){
								 $recibo	= $xPag->addPagoMora($mora, $xCred->getPeriodoActual(), "", TESORERIA_COBRO_NINGUNO, SYS_FECHA_DE_MIGRACION);
								 $xLog->add("ERROR\tINTEGRADO\t$persona\t$credito\tMORA\tAgregar Recibo $recibo con Mora $mora\r\n");
								 }
							}
						}
						//Mediante Interes Normal						
						$xPlan		= new cPlanDePagos($xCred->getNumeroDePlanDePagos());
						if($xPlan->init() == true){
							for ($i=1; $i <= $xCred->getPagosAutorizados(); $i++){
								if($diferencia > (TOLERANCIA_SALDOS * -1)){
									$parcialidad	= $i;
									$OLetra			= $xPlan->getOLetra($i);
									$interes		= $OLetra->getInteres();
									if($interes > 0){
										if($diferencia <= $interes ){
											if($diferencia < $interes){
												$interes	= $interes - $diferencia;
											}
											$diferencia	= 0;
										} else {
											$diferencia	= $diferencia - $interes;
										}
										//$diferencia	= 0;
										if($interes > 0){
										$xPag		= new cCreditosPagos($xCred->getNumeroDeCredito());
										if($xPag->init($xCred->getDatosInArray()) == true){
											$recibo	= $xPag->addPagoInteres($interes, $parcialidad, "", TESORERIA_COBRO_NINGUNO, SYS_FECHA_DE_MIGRACION);
											$xLog->add("ERROR\tINTEGRADO\t$persona\t$credito\tMORA\tAgregar Recibo $recibo con Interes $interes\r\n");
										}									
										$xLog->add("ERROR\tINTEGRADO\t$persona\t$credito\tL-$parcialidad\tEliminar $interes con $diferencia\r\n");
										}
									}
								}
							} 
						}
						} //end ajustes ok
						

					} else if($diferencia < (TOLERANCIA_SALDOS * -1) ){
						$gastos	= $diferencia * -1;
						$xLog->add("ERROR\tINTEGRADO\t$persona\t$credito\tMENOR\t$diferencia\tEl Saldo INTEGRADO en Sistema(" . $xCred->getSaldoAuditado()  . ") es Menor a $integrado por $diferencia\r\n");
						if($ajustarok == true){
							//========== Agregar Gastos de Cobranza
							//if($gastos < 1000){
								$gastos		= $gastos;
								$xPag		= new cCreditosPagos($xCred->getNumeroDeCredito());
								if($xPag->init($xCred->getDatosInArray()) == true){
									$recibo	= $xPag->addCargosCobranza($gastos, $xCred->getPeriodoActual(), "", TESORERIA_COBRO_NINGUNO, SYS_FECHA_DE_MIGRACION);
									
									$xLog->add("ERROR\tINTEGRADO\t$persona\t$credito\tGCBZA\tAgregar Recibo $recibo con Gastos de Cobranza por $gastos\r\n");
								}
							//}
						} else {
							$xLog->add("ERROR\tINTEGRADO\t$persona\t$credito\tGCBZA\tSe debe Ajustar $gastos\r\n");
						}
					} else {
						$xLog->add("ERROR\t$persona\t$credito\t$integrado\t$sistemaInt\tEl Saldo($integrado|$sistemaInt) es igual o difiere por $diferencia\r\n");
					}
				}
				if($comparar == true){
					$cdiff		= $xCred->getSaldoActual() - $capital;
					$cdiff		= round($cdiff,2);
					if($cdiff > 0){
						$xLog->add("ERROR\tCAPITAL\t$persona\t$credito\tEl Saldo en Sistema(" . $xCred->getSaldoActual()  . ") es Mayor a $capital por $cdiff\r\n");
					} else if($cdiff < 0){
						$xLog->add("ERROR\tCAPITAL\t$persona\t$credito\tEl Saldo en Sistema(" . $xCred->getSaldoActual()  . ") es MENOR a $capital por $cdiff\r\n");
					} else {
						//$xLog->add("ERROR\t$persona\t$credito\tEl Saldo es igual\r\n");
					}					
				}
				if($conplan == true AND $xCred->getSaldoActual() > TOLERANCIA_SALDOS ){
					if($xCred->getPeriocidadDePago() !== CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO){
						$fechaMinistracion	= $xCred->getFechaDeMinistracion();
						$idcredito			= $credito;
						$TipoDePago			= CREDITO_TIPO_PAGO_PERIODICO;
						$redondeo			= $xCred->getFactorRedondeo();
						$dia1				= false;
						$dia2				= false;
						$dia3				= false;
						$PrimeraFecha		= $fechaMinistracion;
						switch ($xCred->getPeriocidadDePago() ){
							case CREDITO_TIPO_PERIOCIDAD_MENSUAL:
								$PrimeraFecha		= $xF->setSumarMeses(1,$PrimeraFecha);
								$dia1				= $xF->dia($PrimeraFecha);
								$idotros			= false;
								$montootros			= 0;
								$primer_pago		= false;
					
								$xCred->setCambiarFechaMinistracion($fechaMinistracion, true);
								$xEm->initPorCredito($xCred->getNumeroDeCredito());
								$xEm->setDiasDeAbonoFijo($dia1, $dia2, $dia3);
								//$primer_pago= $xT->cBool($primer_pago);
								$xEm->setFechaArbitraria($PrimeraFecha);
								$parcial 			= $xEm->getParcialidadPresumida($redondeo, $idotros, $montootros, $primer_pago);
								$xEm->setCompilar($TipoDePago);
								$xEm->getVersionFinal(true);
								break;
							case CREDITO_TIPO_PERIOCIDAD_QUINCENAL:
								$xCred->setCambiarFechaMinistracion($fechaMinistracion, true);
								$xEm->initPorCredito($xCred->getNumeroDeCredito());
								$Fecha1Dia		= $xF->setSumarDias(15, $fechaMinistracion);
								$dia1			= $xF->dia($Fecha1Dia);
								$Fecha2Dia		= $xF->setSumarDias(15, $Fecha1Dia);
								$dia2			= $xF->dia($Fecha2Dia);
								$idotros		= false;
								$montootros		= 0;
								$primer_pago	= false;
								$PrimeraFecha	= $Fecha1Dia;
									
								$xEm->setDiasDeAbonoFijo($dia1, $dia2, $dia3);
								//$primer_pago= $xT->cBool($primer_pago);
								$xEm->setFechaArbitraria($PrimeraFecha);
								$parcial 	= $xEm->getParcialidadPresumida($redondeo, $idotros, $montootros, $primer_pago);
								$xEm->setCompilar($TipoDePago);
								$xEm->getVersionFinal(true);
								break;
							case CREDITO_TIPO_PERIOCIDAD_SEMANAL:
								$xCred->setCambiarFechaMinistracion($fechaMinistracion, true);
								$xEm->initPorCredito($xCred->getNumeroDeCredito());
								$Fecha1Dia		= $xF->setSumarDias(7, $fechaMinistracion);
								$dia1			= $xF->getDiaDeLaSemana($Fecha1Dia);
								$idotros		= false;
								$montootros		= 0;
								$primer_pago	= false;
								$PrimeraFecha	= $Fecha1Dia;
									
								$xEm->setDiasDeAbonoFijo($dia1, $dia2, $dia3);
								//$primer_pago= $xT->cBool($primer_pago);
								$xEm->setFechaArbitraria($PrimeraFecha);
								$parcial 	= $xEm->getParcialidadPresumida($redondeo, $idotros, $montootros, $primer_pago);
								$xEm->setCompilar($TipoDePago);
								$xLog->add($xEm->getVersionFinal(true, OUT_TXT, HP_SERVICE, SYS_FECHA_DE_MIGRACION), $xLog->DEVELOPER);
								break;
						}
						
					}
				}//end con plan
				//$xCred->getSaldoAuditado();
				//$xCred->getSaldoIntegrado();
				
			}//end credito init
			$linea++;
		}
	}
	$xLog->add($xFil->getMessages(), $xLog->DEVELOPER);
	$xFRM->addLog($xLog->getMessages());
}
echo $xFRM->get();
?>
<!-- HTML content -->
<script>
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>