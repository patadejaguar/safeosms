<?php
/**
 * @see Modulo de Carga de Respaldos a la Matriz
 * @author Balam Gonzalez Luis Humberto
 * @version 1.1
 * @package common
 *  Actualizacion
 * 		16/04/2008
 *		2008-06-10 Se Agrego la Linea de Informacion del Actualizacion de Movimeintos y recibos
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
$xHP		= new cHPage("TR.Carga de Creditos", HP_FORM);
$xLog		= new cCoreLog();
$xQL		= new MQL();
$cT			= new cTipos();

ini_set("max_execution_time", 1800);


$aceptarnegativos	= parametro("idnegativos", false, MQL_BOOL);
$aceptarcapializados= parametro("idcapitalizados", false, MQL_BOOL);
$jscallback			= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$conplanes			= parametro("idconplanes", false, MQL_BOOL);
$idtasacargos		= parametro("idtasacargos", 0, MQL_FLOAT);
$idtipocargos		= parametro("idtipocargos",0, MQL_INT);
$iddestinodecredito	= parametro("iddestinodecredito", 501, MQL_INT);
$guardaruno			= parametro("idguardaruno", false, MQL_BOOL);
//$jxc ->drawJavaScript(false, true);

$xHP->init();


$xFRM			= new cHForm("frmSendFiles", "mae.prestamos.upload.frm.php");
$xHSel			= new cHSelect();

//Si la Operacion es Configurar los Datos
if ( $action == SYS_NINGUNO ){
	
$xFRM->setAction("mae.prestamos.upload.frm.php?action=" .MQL_ADD);
$xFRM->setTitle($xHP->getTitle());
$xFRM->setEnc("multipart/form-data");
$xFRM->OFile("cFile1", "", "TR.Archivo");
$xFRM->addHElem( $xHSel->getListaDeProductosDeCredito()->get(true) );

$xFRM->OMoneda("idtasacargos", 0, "TR.TASA DE CARGOS");
$selOps	= $xHSel->getListaDeTiposDeOperacion("idtipocargos", SYS_TODAS);
$selOps->setOptionSelect(OPERACION_CLAVE_PLAN_DESGLOSE);
$xFRM->addHElem( $selOps->get("TR.CONCEPTO OTROS CARGOS", true) );

$xFRM->addHElem($xHSel->getListaDeDestinosDeCredito("", CREDITO_DEFAULT_DESTINO)->get(true));

$xFRM->OCheck("TR.Aceptar creditos Capitalizados", "idcapitalizados");
$xFRM->OCheck("TR.Aceptar creditos Negativos", "idnegativos");
$xFRM->OCheck("TR.Generar Planes", "idconplanes");
$xFRM->OCheck("TR.INCLUIR ACCESORIOS AL ULTIMO PAGO", "idguardaruno");

$xFRM->addSubmit();

} elseif ( $action ==  MQL_ADD ) {
/*echo '<form name="frmConvs" method="POST" action="mae.prestamos.upload.frm.php?o=s">
<fieldset>
	<legend>Mostrar Resultado de la Carga</legend> ';*/


$usrFiles		= array();
$usrFiles[0]	= $_FILES["cFile1"];
$msg			= "";
$arrPeriodos	= array (
	"D" => CREDITO_TIPO_PERIOCIDAD_DECENAL,	"Q" => CREDITO_TIPO_PERIOCIDAD_QUINCENAL,	"C" => CREDITO_TIPO_PERIOCIDAD_CATORCENAL,
	"S" => CREDITO_TIPO_PERIOCIDAD_SEMANAL,	"M" => CREDITO_TIPO_PERIOCIDAD_MENSUAL,	"" => CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO,
	"O" => CREDITO_TIPO_PERIOCIDAD_DIARIO, "F" => CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO
);

$prePath		= PATH_BACKUPS;
$i				= 0;
if(isset($usrFiles[$i])==true){
	//Obtener Extension
	$DExt 		= explode(".", substr($usrFiles[$i]['name'], -6));
	$mExt		= $DExt[1];
	$producto	= parametro("idproducto", DEFAULT_TIPO_CONVENIO, MQL_INT);
	if($mExt == "csv"){
		$completePath	= $prePath . $usrFiles[$i]['name'];
		if(file_exists($completePath)==true){
			unlink($completePath);
			$xLog->add("SE ELIMINO EL ARCHIVO " . $usrFiles[$i]['name'] . "\r\n");
		}
		if(move_uploaded_file($usrFiles[$i]['tmp_name'], $completePath )) {
			$xLog->add("SE GUARDO EXITOSAMENTE EL ARCHIVO " . $usrFiles[$i]['name'] . "\r\n", $xLog->DEVELOPER);
		} else {
			$xLog->add("SE FALLO AL GUARDAR " . $usrFiles[$i]['name'] . "\r\n", $xLog->DEVELOPER);
		}
		//analizar el Archivo
		$gestor = @fopen($completePath, "r");

		$iReg 	= 0;
		
		
		if ($gestor) {
			while (!feof($gestor)) {
				$bufer			= fgets($gestor, 4096);
				//$bufer			= stream_get_line($gestor, "\r\n");
				if (!isset($bufer) ){
					$xLog->add("$iReg\t\tERROR\tLa Linea($iReg) no se leyo($bufer)\r\n");
				} else {
					$bufer					= trim($bufer);
					$datos					= explode("|", $bufer, 18);
					$xF						= new cFecha();
					
					$socio					= $cT->cInt($datos[1]);
					$credito				= $cT->cInt($datos[17]); //ID CREDITO COLUMNA

					$monto					= $cT->cFloat($datos[8]);
					$equiv_periodos			= isset($arrPeriodos[trim($datos[7])]) ? $arrPeriodos[trim($datos[7])] :CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO;
					$periocidad				= $cT->cInt( $equiv_periodos );
					$pagos					= $cT->cInt($datos[6]);
					$ministracion			= $xF->getFechaISO($datos[10]);
					$fechaSolicitado		= $xF->getFechaISO($datos[5]);
					$descDestino			= $cT->cChar($datos[9]);
					$parcialidad			= $cT->cInt($datos[14]);
					$tasa					= $cT->cFloat($datos[15]);
					$tasa					= $tasa /100;
					
					$dias					= $periocidad * $pagos;
					$aplicacion				= $iddestinodecredito; //($cT->cChar($datos[17]) == "S") ? 501 : 100; //destino
					
					$vencimiento			= $xF->setSumarDias($dias, $ministracion); //$cT->cFecha($datos[5]);
														
					$saldo					= $cT->cFloat($datos[11]);
					$UltimaOperacion		= $xF->getFechaISO($datos[12]);
					$ContratoCorriente		= CTA_GLOBAL_CORRIENTE; //$cT->cInt($datos[10]);
					$interes_pagado			= 0;
					
					//Eliminar créditos anteriores
					//$Creds					= new cCreditos_solicitud();
					//$rs						= $Creds->query()->select()->exec("numero_socio=$socio");
					//foreach($rs as $data){
						/*$Creds->setData($data);
						$solicitud 	= $Creds->numero_solicitud()->v();
						$socio		= $Creds->numero_socio()->v();
						$xCred		= new cCredito($solicitud, $socio);
						$msg		.= $xCred->setDelete();*/
					//}
					if($socio == 0){
						$xLog->add("$iReg\t\tERROR\tLa Linea($iReg) no se leyo($bufer)\r\n");
					} else {
						$xQL->setRawQuery("UPDATE socios_general SET dependencia=" . DEFAULT_EMPRESA . " WHERE codigo=$socio");
						
						
						$xCred				= new cCredito();
						$xConv				= new cProductoDeCredito($producto); $xConv->init();
						//Crear Contrato corriente si el producto tiene ahorro
						$DConv				= $xCred->getDatosDeProducto($producto);
						$tasaAhorro			= $xConv->getTasaDeAhorro(); // $cT->cFloat( $DConv["tasa_ahorro"] );
						if($ContratoCorriente == 0 AND $tasaAhorro > 0){
							/*$xCapta				= new cCuentaALaVista(false);
							$ContratoCorriente	= $xCapta->setNuevaCuenta(99, DEFAULT_SUBPRODUCTO_CAPTACION, $socio, "CUENTA POR IMPORTACION", $credito);
							$msg 			.= "$iReg\t$socio\t$credito\tAgregando una Cuenta Corriente $ContratoCorriente NUEVO\r\n";*/
						}
						//Agregar
						$xLog->add("$iReg\t---------------------------------\t$credito\t-------------------------\r\n");
						/*
						$TipoDeConvenio, $NumeroDeSocio, $ContratoCorriente, $MontoSolicitado, $PeriocidadDePago = 0, $NumeroDePagos = 0, $PlazoEnDias = 0, 
						$DestinoDeCredito = CREDITO_DEFAULT_DESTINO, $NumeroDeCredito = false, $GrupoAsociado = DEFAULT_GRUPO, $DescripcionDelDestino = "", 
						$Observaciones = "", $OficialDeCredito = false, $FechaDeSolicitud = false, 
						$TipoDePago = CREDITO_TIPO_PAGO_UNICO, $TipoDeCalculo = INTERES_POR_SALDO_INSOLUTO, $TasaDeInteres = false, 
						$FechaDeMinistracion = false, $persona_asociada = false, 
						$TipoDeAutorizacion = false, $id_de_origen = 1, $tipo_de_origen = false, $LugarDeCobro = false, $TipoDeDesembolso = false 
						 */
						//============ Corregir Saldo Mayor al Saldo Actual
						if($saldo > $monto){
							if($aceptarcapializados == true){
								$xLog->add("$iReg\t$socio\t$credito\tMonto del Credito de $monto a Saldo $saldo\r\n");
								$monto	= $saldo;
							} else {
								$xLog->add("$iReg\t$socio\t$credito\tMonto del Credito de $monto a CERO\r\n");
								$monto	= 0;
								$ok		= false;
							}
						}
						//============ Corregir Saldo Negativo
						if($saldo < 0){
							if($aceptarnegativos == true){
								$xLog->add("$iReg\t$socio\t$credito\tNegativos de $saldo Modificar Inicial $monto\r\n");
								$monto	= $monto + ($saldo * -1);
								$saldo	= 0;
							} else {
								$ok		= false;
							}
						}
						if($monto > 0){
							$rcred	 = $xCred->add($producto, $socio, $ContratoCorriente, $monto, $periocidad, $pagos, $dias, $aplicacion, $credito,
								DEFAULT_GRUPO, $descDestino, "CREDITO IMPORTADO #$iReg", DEFAULT_USER, $fechaSolicitado,
								CREDITO_TIPO_PAGO_PERIODICO,INTERES_POR_SALDO_INSOLUTO, $tasa);
							$credito	= $xCred->getNumeroDeCredito();
							$xLog->add($xCred->getMessages());
							$ok		= ($rcred === false) ? false: true;
						} else {
							$ok		= false;
							$xLog->add("$iReg\t$socio\t$credito\tEL Saldo del Credito es Invalido $monto\r\n");
						}
						if($ok == true){
							//=============================================== Autorizar
							//usleep(100);
							$xCred		= new cCredito($credito);
							///Inicializar
							$xCred->init();
							$credito	= $xCred->getNumeroDeCredito();
							/*$Monto, $Pagos, $Periocidad, $TipoDeAutorizacion, $FechaDeAutorizacion, $DocumentoDeAutorizacion, $FormaDePago = false, $FechaDeMinistracionPropuesta = false, 
			$NivelDeRiesgo = 2, $PlazoEnDias = false, $FechaDeVencimiento = false, $EstatusImpuesto = false, $SaldoImpuesto = 0, $InteresImpuesto = 0, $FechaOperacionImpuesta = false, $TasaDeInteres = false, 
			$LugarDeCobro = false, $TipoDeDesembolso = false*/
							//autorizar
							$rauth 	= $xCred->setAutorizado($monto, $pagos, $periocidad, CREDITO_TIPO_AUTORIZACION_NORMAL, $ministracion, 
									"", false, $ministracion,2, false, 
									$vencimiento, CREDITO_ESTADO_AUTORIZADO, $monto, 0, $UltimaOperacion);
							//usleep(1000);
							$xCred->setCuandoSeActualiza();
							$xLog->add($xCred->getMessages());
							$ok		= ($rauth === false) ? false : true; 
						}
						//=============================================== Ministrar
						if($ok == true){
							
							$xCred		= new cCredito($credito);
							///Inicializar
							$xCred->init();
							$credito	= $xCred->getNumeroDeCredito();
							
							//ministrar
							$xCred->setForceMinistracion();
							$xCred->setMinistrar(DEFAULT_RECIBO_FISCAL, DEFAULT_CHEQUE, $monto, DEFAULT_CUENTA_BANCARIA, 0, DEFAULT_CUENTA_BANCARIA, "", $ministracion);
							$xLog->add($xCred->getMessages());
							
							//usleep(1000);
							$xCred		= new cCredito($credito);
							///Inicializar
							$xCred->init();
							$credito	= $xCred->getNumeroDeCredito();
							
							if( $monto > $saldo ){
								$abono			= ($monto - $saldo);
								$xLog->add("$iReg\t$socio\t$credito\tAgregando un Abono por $abono por el Saldo $saldo del Monto $monto\r\n");
								$xRec			= new cReciboDeOperacion(RECIBOS_TIPO_PAGO_CREDITO, true );
								$parcialidad	= ($parcialidad <= 0) ? 1 : $parcialidad;
								$FechaRecibo	= $xF->getFechaISO(SYS_FECHA_DE_MIGRACION);
								/*$socio, $documento, $fecha, $parcialidad,
						$Tipo = false, $cadena = "", $cheque_afectador = "NA",
						$TipoPago = "", $recibo_fiscal = "", $grupo = false,
						$cuenta_bancaria = false, $moneda = "", $unidades = 0, $persona_asociada = false, $periodo = false*/
								$idreciboAb		= $xRec->setNuevoRecibo($socio, $credito, $FechaRecibo, $parcialidad, RECIBOS_TIPO_PAGO_CREDITO);
								/*($monto, $parcialidad = SYS_UNO, $cheque = DEFAULT_CHEQUE, $tipo_de_pago = DEFAULT_TIPO_PAGO, $recibo_fiscal = DEFAULT_RECIBO_FISCAL,
			$observaciones = "", $grupo = false, $fecha = false, $recibo = false)*/
								$xCred->setAbonoCapital($abono, $parcialidad, DEFAULT_CHEQUE, TESORERIA_COBRO_EFECTIVO, "", "",  false, $FechaRecibo, $idreciboAb);
								$xRec->setFecha($FechaRecibo, true);
								$xRec->setFinalizarRecibo(true);
								$xLog->add($xRec->getMessages(), $xLog->DEVELOPER);
								$xCred->setCuandoSeActualiza();
								$xCred->setRevisarSaldo();
							}
						//==================================================================
							if($conplanes == true){
							//============================================ AGREGAR PLAN DE PAGOS
								$xEm			= new cPlanDePagosGenerador();
								$xT				= new cTipos();
								$xCred			= new cCredito($xCred->getNumeroDeCredito());
								$montootros		= 0;
								$idotros		= false;
								$xEm->setGuardarPrimeraDiferencia($guardaruno);
								//usleep(1000);
								if($xCred->init() == true){
									if($idtasacargos > 0){
										$idtasacargos	= $idtasacargos;
										$idotros		= $idtipocargos;
										$montootros		= setNoMenorQueCero( (($xCred->getMontoAutorizado()*$idtasacargos)/100),2);
										$xLog->add("$iReg\t$socio\t$credito\tMonto Extra de $idotros a Tasa $idtasacargos por un monto de $montootros\r\n");
									}									
									if($xCred->getPeriocidadDePago() !== CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO){
										$fechaMinistracion	= $xCred->getFechaDeMinistracion();
										$idcredito			= $credito;
										$TipoDePago			= CREDITO_TIPO_PAGO_PERIODICO;
										$redondeo			= $xCred->getFactorRedondeo();
										$dia1				= false;
										$dia2				= false;
										$dia3				= false;
										$primer_pago		= false;
										$PrimeraFecha		= $xCred->getFechaDeMinistracion();
										switch ($xCred->getPeriocidadDePago() ){
											case CREDITO_TIPO_PERIOCIDAD_MENSUAL:
												$PrimeraFecha		= $xF->setSumarMeses(1,$PrimeraFecha);
												$dia1				= $xF->dia($PrimeraFecha);
												//$idotros			= false;
												//$montootros			= 0;
												
								
												//$xCred->setCambiarFechaMinistracion($fechaMinistracion, true);
												$xEm->initPorCredito($xCred->getNumeroDeCredito(), $xCred->getDatosInArray());
												$xEm->setDiasDeAbonoFijo($dia1, $dia2, $dia3);
												//$primer_pago= $xT->cBool($primer_pago);
												$xEm->setFechaArbitraria($PrimeraFecha);
												$parcial 			= $xEm->getParcialidadPresumida($redondeo, $idotros, $montootros, $primer_pago);
												$xEm->setCompilar($TipoDePago);
												$xEm->getVersionFinal(true);
												break;
											case CREDITO_TIPO_PERIOCIDAD_QUINCENAL:
												//$xCred->setCambiarFechaMinistracion($fechaMinistracion, true);
												$xEm->initPorCredito($xCred->getNumeroDeCredito(), $xCred->getDatosInArray());
												$Fecha1Dia		= $xF->setSumarDias(15, $fechaMinistracion);
												$dia1			= $xF->dia($Fecha1Dia);
												$Fecha2Dia		= $xF->setSumarDias(15, $Fecha1Dia);
												$dia2			= $xF->dia($Fecha2Dia);
												//$idotros		= false;
												//$montootros		= 0;
												//$primer_pago	= false;
												$PrimeraFecha	= $Fecha1Dia;
												$xLog->add("$iReg\t$socio\t$credito\tQuincenal Primera Fecha $PrimeraFecha dias $dia1 y $dia2 Ministrado $fechaMinistracion \r\n");
												$xEm->setDiasDeAbonoFijo($dia1, $dia2, $dia3);
												//$primer_pago= $xT->cBool($primer_pago);
												$xEm->setFechaArbitraria($PrimeraFecha);
												$parcial 	= $xEm->getParcialidadPresumida($redondeo, $idotros, $montootros, $primer_pago);
												$xEm->setCompilar($TipoDePago);
												$xEm->getVersionFinal(true);
												break;
											case CREDITO_TIPO_PERIOCIDAD_SEMANAL:
												//$xCred->setCambiarFechaMinistracion($fechaMinistracion, true);
												$xEm->initPorCredito($xCred->getNumeroDeCredito(), $xCred->getDatosInArray());
												$Fecha1Dia		= $xF->setSumarDias(7, $fechaMinistracion);
												$dia1			= $xF->getDiaDeLaSemana($Fecha1Dia);
												//$idotros		= false;
												//$montootros		= 0;
												//$primer_pago	= false;
												$PrimeraFecha	= $Fecha1Dia;
													
												$xEm->setDiasDeAbonoFijo($dia1, $dia2, $dia3);
												//$primer_pago= $xT->cBool($primer_pago);
												$xEm->setFechaArbitraria($PrimeraFecha);
												$parcial 	= $xEm->getParcialidadPresumida($redondeo, $idotros, $montootros, $primer_pago);
												$xEm->setCompilar($TipoDePago);
												$xEm->getVersionFinal(true);
												break;
												case CREDITO_TIPO_PERIOCIDAD_DECENAL:
													/*$xCred->setCambiarFechaMinistracion($fechaMinistracion, true);
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
													$xEm->getVersionFinal(true);*/
													break;												
										}
								
									}
									//$xLog->add($xEm->getMessages(), $xLog->DEVELOPER);
								}
							}			
						} else {
							$xLog->add("$iReg\t$socio\t$credito\tEL Credito no se pudo agregar\r\n");
						}
					
						$xLog->add($xCred->getMessages(OUT_TXT), $xLog->DEVELOPER);
						$xCred		= null;
						$socio		= 0;
						$credito	= 0;
					}
				}
			$iReg++;
			}
			//Actualizacion
			
			$xQL->setRawQuery("UPDATE `operaciones_mvtos` SET `fecha_operacion` = (SELECT `fecha_operacion` FROM `operaciones_recibos` WHERE `idoperaciones_recibos`=`operaciones_mvtos`.`recibo_afectado` LIMIT 0,1) WHERE `tipo_operacion`=120");
			$xQL->setRawQuery("UPDATE `operaciones_mvtos` SET `fecha_afectacion` = (SELECT `fecha_operacion` FROM `operaciones_recibos` WHERE `idoperaciones_recibos`=`operaciones_mvtos`.`recibo_afectado` LIMIT 0,1) WHERE `tipo_operacion`=120");
		}
		@fclose ($gestor);
		
		
	}	else {
		echo "<p class='aviso'>EL TIPO DE ARCHIVO DE " . $usrFiles[$i]['name'] . "(" .$mExt . ") NO SE ACEPTA</p>";
	}
	$xFRM->addLog($xLog->getMessages());
} //end valid file

} //end action


echo $xFRM->get();

$xHP->fin();
?>