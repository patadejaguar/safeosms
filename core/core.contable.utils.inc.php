<?php

include_once("core.deprecated.inc.php");
include_once("entidad.datos.php");
include_once("core.config.inc.php");
include_once("core.common.inc.php");
include_once("core.contable.inc.php");
include_once("core.html.inc.php");
include_once("core.fechas.inc.php");
include_once("core.security.inc.php");
@include_once("../libs/sql.inc.php");

class cUtileriasParaContabilidad{
	private $mIDDePoliza	= 0;
	private $mMessages		= "";
	 
	function __construct(){
		
	}
	function setGenerarSaldosDelEjercicio($ejercicio){
			//$ejercicio 			= $c1;
			$msg				= "====\tGENERAR SALDO DE CUENTAS CONTABLES\r\n";
			$msg				.= "====\tEjercicio: $ejercicio\r\n";
			$msg				.= "CUENTA\tSALDO_INICIAL\r\n";
			
			$ejercicioAnt		= $ejercicio - 1;
			$saldosAnteriores 	= array();
			$sqlSAnt			= "SELECT cuenta, tipo, imp14 FROM contable_saldos WHERE ejercicio = $ejercicioAnt AND tipo=1 ";
				//Cargar en un Array los Saldos del Ejercicio Anterior
				$rsAnt 			= mysql_query($sqlSAnt, cnnGeneral() );
					while($rwA = mysql_fetch_array($rsAnt) ){
						$saldosAnteriores[ $rwA["cuenta"] ]	= $rwA["imp14"];
					}
				//
	
			$sqlM			= "DELETE FROM contable_saldos WHERE ejercicio = $ejercicio";
			my_query($sqlM);
	
			$sqlsc 			= "SELECT * FROM contable_catalogo";
			$rsc			= mysql_query($sqlsc, cnnGeneral() );
			while($rwc = mysql_fetch_array($rsc)){
				$cuenta 	= $rwc["numero"];
				$monto		= ( !isset($saldosAnteriores[$cuenta]) ) ? 0 : $saldosAnteriores[$cuenta];

				$sqlis1 = "INSERT INTO contable_saldos(cuenta, ejercicio, tipo, saldo_inicial,
													imp1, imp2, imp3, imp4, imp5, imp6, imp7, imp8, imp9, imp10,
													imp11, imp12, imp13, imp14, captado)
	    		VALUES ($cuenta,
					$ejercicio,
					1,
					$monto, $monto, $monto, $monto, $monto, $monto, $monto, $monto, $monto, $monto, $monto, $monto, $monto, $monto, $monto, 'false'),
					($cuenta,
					$ejercicio,
					2,
					0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'false'),
					($cuenta,
					$ejercicio,
					3,
					0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'false')";
				my_query($sqlis1);
				$msg	.= "$cuenta\t$monto\r\n";
			}
			@mysql_free_result($rsc);
			return $msg;	
	}
	function setRegenerarSaldosByMvto($ejercicio = false, $periodo = false){
		//TODO: 2011-09-27 TERMINAR
			//Contruir Saldos a Partir de Movimientos
			//$ejercicio			= $c1;
			$ejercicioAnt		= $ejercicio - 1;
			$saldosAnteriores 	= array();
			$sqlSAnt			= "SELECT cuenta, tipo, imp14 FROM contable_saldos WHERE ejercicio = $ejercicioAnt AND tipo=1 ";
				//Cargar en un Array los Saldos del Ejercicio Anterior
				$rsAnt 			= getRecordset($sqlSAnt );
					while($rwA = mysql_fetch_array($rsAnt) ){
						$saldosAnteriores[ $rwA["cuenta"] ]	= $rwA["imp14"];
					}
				//		
	}
	function setRegenerarPrepolizaContable($FechaDeRecibo = false, $NumeroDeRecibo = false){
		$msg	= "";
		$wLim	= " AND ( `operaciones_recibos`. `fecha_operacion` = '$FechaDeRecibo') ";
		$wLim	= ( setNoMenorQueCero($NumeroDeRecibo) > 0  ) ? " AND ( `operaciones_recibos`. `idoperaciones_recibos` = '$NumeroDeRecibo') " : $wLim;
		$xCat	= new cCatalogoOperacionesDeCaja();
		$ql		= new MQL();
		$xLog	= new cCoreLog();
		$xPol	= new cPoliza(false);
		$msg	.= "Recibo\tDocumento\tSocio\tOperacion\tMonto\tContable\r\n";
		$DAfecta= array();
		$DAlt	= array();
		$FForm	= array();
		$sqlAfecta		= "SELECT * FROM `contable_polizas_perfil`";// WHERE (`contable_polizas_perfil`.`tipo_de_recibo` = $TipoDeRec)";
		$rsAfecta		= $ql->getDataRecord($sqlAfecta);
		
		foreach ($rsAfecta as $rwAfecta){
			$DAfecta[$rwAfecta["tipo_de_recibo"]][$rwAfecta["tipo_de_operacion"]]		= $rwAfecta["operacion"];			//operacion cargo/abono
			if(setNoMenorQueCero($rwAfecta["cuenta_alternativa"]) > 0){
				$DAlt[$rwAfecta["tipo_de_recibo"]][$rwAfecta["tipo_de_operacion"]]= $rwAfecta["cuenta_alternativa"];	//cuenta alternativa
			}
			if(trim($rwAfecta["formula_posterior"]) != ""){
				$DForm[$rwAfecta["tipo_de_recibo"]][$rwAfecta["tipo_de_operacion"]]	= $rwAfecta["formula_posterior"];	//formula evaluada
			}
		}
		$rsAfecta		= null;
		//$xLogg->add(, $xLogg->DEVELOPER);
		//
		$sql	= "SELECT
							`operaciones_recibos`.*,
							`operaciones_recibostipo`.*
						FROM
							`operaciones_recibos` `operaciones_recibos`
								INNER JOIN `operaciones_recibostipo` `operaciones_recibostipo`
								ON `operaciones_recibos`.`tipo_docto` = `operaciones_recibostipo`.
								`idoperaciones_recibostipo`
						WHERE
							( SELECT COUNT(tipo_de_recibo) FROM contable_polizas_perfil 
							WHERE tipo_de_recibo = `operaciones_recibos`.`tipo_docto` ) > 0 
							 $wLim ";
		$rs 				= $ql->getDataRecord($sql);
		//setLog($sql);
		foreach ( $rs as $rw  ){
			$Recibo			= $rw["idoperaciones_recibos"];
			$TipoDeRec		= $rw["tipo_docto"];
			$TipoDePago		= $rw["tipo_pago"];
			$TotalRecibo	= $rw["total_operacion"];
			$socio_de_rec	= $rw["numero_socio"];
			$docto_de_rec	= $rw["docto_afectado"];
			$usrRec			= $rw["idusuario"];
			$banco			= $rw["cuenta_bancaria"];
			$fecha			= $rw["fecha_operacion"];
			$TIPO_DE_RECIBO	= $TipoDeRec;			//vars en formulas
			$TIPO_DE_PAGO	= $TipoDePago;			//vars en formulas
			//setLog("$TIPO_DE_RECIBO ... REC");
			
			$DatosAfect		= (isset($DAfecta[$TipoDeRec])) ? $DAfecta[$TipoDeRec] : array();
			$DatosAlter		= (isset($DAlt[$TipoDeRec])) ? $DAlt[$TipoDeRec] : array();
			$DatosForm		= (isset($DForm[$TipoDeRec])) ? $DForm[$TipoDeRec] : array();
			$xRec			= new cReciboDeOperacion(false, false, $Recibo);
			
			if($xRec->init($rw) == true){
				$xRec->getDatosDeCobro();
				if($banco <= FALLBACK_CUENTA_BANCARIA){
					$OBanco			= $xRec->getOCuentaBancaria();
					if($OBanco != null){
						$xLog->add("WARN\tCambio de Banco $banco a " . $OBanco->getNumeroDeCuenta() . "\r\n", $xLog->DEVELOPER);
						$banco 		= $OBanco->getNumeroDeCuenta();
					} else {
						$xLog->add("ERROR\tAl Obtener el banco $banco\r\n", $xLog->DEVELOPER);
					}					
				}
				//setLog($xRec->getMessages());
			} else {
				$xLog->add("ERROR\tAl Iniciar el recibo $Recibo\r\n", $xLog->DEVELOPER);
			}
			if($banco <= FALLBACK_CUENTA_BANCARIA){
				$banco	= FALLBACK_CUENTA_BANCARIA;
			}
			$xLog->add($xRec->getMessages(), $xLog->DEVELOPER);
			$TotalSuma		= 0;

			$xLog->add("OK\tRecibo $Recibo Tipo $TipoDeRec con Pago $TipoDePago y Banco $banco \r\n", $xLog->DEVELOPER);
			//Eliminar Prepoliza de ese dia.
			$sqlDR			= "DELETE FROM `contable_polizas_proforma` WHERE numero_de_recibo = $Recibo ";
			$ql->setRawQuery($sqlDR);
					
			$sqlM		= "SELECT
							`operaciones_mvtos`.*,
							`operaciones_mvtos`.`recibo_afectado` 
						FROM
							`operaciones_mvtos` `operaciones_mvtos` 
						WHERE
							(`operaciones_mvtos`.`recibo_afectado` = $Recibo) ";
			//setLog($sqlM);
			//=======================
			$rsM 				= $ql->getDataRecord($sqlM);
			foreach ($rsM as  $rwM  ){
				//
				$TipoOperacion	= $rwM["tipo_operacion"];
				$Monto			= $rwM["afectacion_real"];
				$socio			= $rwM["socio_afectado"];
				$docto			= $rwM["docto_afectado"];
				$usr			= $rwM["idusuario"];
				$REVERTIR_ASIENTOS=false;
				//setLog($sqlAfecta);
				$xLog->add("OK\tOperacion $TipoOperacion con Monto $Monto ($usr)\r\n", $xLog->DEVELOPER);
				
				$AfectaCont		= (isset($DatosAfect[$TipoOperacion] ) ) ? $DatosAfect[$TipoOperacion] : 1;
				$otro			= (isset($DatosAlter[$TipoOperacion])) ? $DatosAlter[$TipoOperacion] : 0;
				$AfectaRev		= ($AfectaCont == TM_ABONO) ? TM_CARGO : TM_ABONO;
				$TotalSuma		+= $Monto;//( ( $Monto * $AfectaCont) * -1);
				if(isset($DatosForm[$TipoOperacion])){
					eval($DatosForm[$TipoOperacion]);
				}
				$xPol->addProforma($Recibo, $TipoOperacion, $Monto, $socio, $docto, $AfectaCont, $usr, $banco, $otro, $fecha);
				$msg			.= "$Recibo\t$socio\t$docto\t$TipoOperacion\t$Monto\t$AfectaCont\r\n";
				//para tipos de pago Ninguno
				if($REVERTIR_ASIENTOS == true){
					$xPol->addProforma($Recibo, $TipoOperacion, $Monto, $socio, $docto, $AfectaRev, $usr, $banco, $otro, $fecha);
					$msg			.= "$Recibo\t$socio\t$docto\t$TipoOperacion\t$Monto\t$AfectaRev\tREV\r\n";
					$TotalSuma		= ($TotalSuma - $Monto);
				}
			}
			if($TotalSuma != 0){
				//por el pago
				$OperacionPago	= $xCat->getTipoOperacionByTipoPago($TipoDePago);
				//setLog("$Recibo --- $TipoDePago");
				$AfectaCont		= (isset($DatosAfect[$OperacionPago] ) ) ? $DatosAfect[$OperacionPago] : 1;
				//Agregar un movimiento por el Tipo de Pago.. Cambiado monto por Monto
				$alternativo	= (isset($DatosAlter[$OperacionPago] ) ) ? $DatosAlter[$OperacionPago] : 0;
				//setLog($DatosAfect[$OperacionPago]);
				$xPol->addProforma($Recibo, $OperacionPago, $TotalSuma, $socio_de_rec, $docto_de_rec, $AfectaCont, $usrRec, $banco, $alternativo, $fecha);
				$msg				.= "$Recibo\t$socio_de_rec\t$docto_de_rec\t$OperacionPago\t$TotalSuma\t$AfectaCont\r\n";
			}
			//TODO: Ampliar funcionalidad de poliza, agregar suma de cargos, suma de abonos y si debe se cargo o abono
		}
		$xLog->add($msg, $xLog->DEVELOPER);
		$this->mMessages	.= $xLog->getMessages();
		$rs					= null;
		return $xLog->getMessages();	
	}
	function setPolizaPorRecibo($recibo, $generador = false){
		$sucess					= false;
		$QL						= new MQL();
		$xLogg					= new cCoreLog();
		//$arrEquivEfvo			= 
		//if (GENERAR_CONTABILIDAD == true){
			$xLogg->add("=======\tGENERAR POLIZA POR RECIBO NUM $recibo\r\n", $xLogg->DEVELOPER);
			$xT					= new cCatalogoOperacionesDeCaja();
			$centro_de_costo	= DEFAULT_CENTRO_DE_COSTO;
			//($generador == GENERAR_POLIZAS_AL_CIERRE) AND
			if (  setNoMenorQueCero($recibo) > 0 ){
				$xRec	= new cReciboDeOperacion(false, false, $recibo);
				if($xRec->init() == true){
					//Iniciar centro de costo por Sucursal
					$xSuc	= new cSucursal($xRec->getSucursal());
					if($xSuc->init() == true){
						if($xSuc->getCentroDeCosto() > 0){
							$centro_de_costo	= $xSuc->getCentroDeCosto();
						}
					}
					$sucess = true;
					//Obten datos del recibo para la Poliza
					$sqlRec = "SELECT
							`operaciones_recibos`.*,
							`operaciones_recibostipo`.`tipo_poliza_generada`,
						`operaciones_recibostipo`.`afectacion_en_flujo_efvo`
						FROM
							`operaciones_recibos` `operaciones_recibos`
								INNER JOIN `operaciones_recibostipo`
								`operaciones_recibostipo`
								ON `operaciones_recibos`.`tipo_docto` =
								`operaciones_recibostipo`.
								`idoperaciones_recibostipo`
						WHERE
							(`operaciones_recibos`.`idoperaciones_recibos` = $recibo)
						LIMIT 0,1 ";
					$dRec 				= obten_filas($sqlRec);
					$tipo_de_pago		= $xRec->getTipoDePago();
					//Corrige los cargos de la PolizaS
					$total_poliza		= 0;
					$SQLSumCargos		= "SELECT SUM(monto) AS 'total' FROM contable_polizas_proforma
												WHERE contable_operacion = '" . TM_CARGO . "'
												AND numero_de_recibo=$recibo ";
					$TMPCargos			= mifila($SQLSumCargos, "total");
					
					$SQLSumAbonos		= "SELECT SUM(monto) AS 'total' FROM contable_polizas_proforma
												WHERE contable_operacion = '" . TM_ABONO . "'
												AND numero_de_recibo=$recibo ";
					$TMPAbonos			= mifila($SQLSumAbonos, "total");
					
					if($TMPCargos > $TMPAbonos){
						$diferencia		= $TMPAbonos - $TMPCargos;
						$diferencia		= ( $diferencia < 0 ) ? ( $diferencia * -1 ) : $diferencia;
						$total_poliza	= $TMPCargos;
						setPolizaProforma($recibo, $xT->getTipoOperacionByTipoPago($tipo_de_pago), $diferencia, 1,1, TM_ABONO);
						$xLogg->add("OK\tCUADRAR\tABONO\tOperacion Agregada por DESCUADRE en $tipo_de_pago por $diferencia\r\n" , $xLogg->DEVELOPER);
						//$xLogg->add( , $xLogg->DEVELOPER);
					} elseif($TMPCargos < $TMPAbonos){
						$diferencia		= $TMPCargos - $TMPAbonos;
						$diferencia		= ( $diferencia < 0 ) ? ( $diferencia * -1 ) : $diferencia;
						$total_poliza	= $TMPAbonos;
						setPolizaProforma($recibo, $xT->getTipoOperacionByTipoPago($tipo_de_pago), $diferencia, 1,1, TM_CARGO);
						$xLogg->add("OK\tCUADRAR\tCARGOS\tOperacion Agregada por DESCUADRE en $tipo_de_pago por $diferencia\r\n" , $xLogg->DEVELOPER);
					} else {
						$total_poliza	= $TMPCargos;
					}
				
					//tipos de Poliza 1=ingreso, 2=egreso, 3 Diario, 4=orden 5
	
					$xD						= new cFecha(0, $xRec->getFechaDeRecibo());
					$numero_de_recibo		= $recibo;
					$tipo_de_poliza			= $xRec->getOTipoRecibo()->getTipoPolizaContable();
					$fecha_de_poliza		= $xRec->getFechaDeRecibo();
					$ejercicio_de_poliza	= $xD->anno();
					$periodo_de_poliza		= $xD->mes();
					$recibo_fiscal			= ( strlen($xRec->getReciboFiscal()) > 2) ? ";RF:" . $xRec->getReciboFiscal() : "";
					$observacion_recibo		= $xRec->getObservaciones();
					$cheque					= ( strlen($xRec->getNumeroDeCheque()) > 2 ) ? ";Ch:" . $xRec->getNumeroDeCheque() : "";
					$concepto_poliza		= substr( ("R:" . $numero_de_recibo . $recibo_fiscal . $cheque . ";" . $xRec->getObservaciones()), 0,80) ;
		
					$flujo_efectivo			= $xRec->getOTipoRecibo()->getAfectacionEnEfvo();
					$socio					= $xRec->getCodigoDeSocio();
					$propietario			= $xRec->getCodigoDeUsuario();
					
					$xLogg->add("=====\tRECIBO TIPO: " . $xRec->getTipoDeRecibo() ." SUMA: " . $xRec->getTotal() . " \r\n" , $xLogg->DEVELOPER);
		//------------------ Agregar Poliza
					$xPol					= new cPoliza($tipo_de_poliza, false, $ejercicio_de_poliza, $periodo_de_poliza);
					$xPol->add($concepto_poliza, $fecha_de_poliza, false, 0, 0, $propietario, $centro_de_costo, $recibo);
					$numero_de_poliza		= $xPol->get();
					$xLogg->add("=====\tPOLIZA NUM: $numero_de_poliza | TIPO: $tipo_de_poliza | EJERCICIO: $ejercicio_de_poliza | PERIODO: $periodo_de_poliza\r\n", $xLogg->DEVELOPER);
					
					$sucess					= ($xPol->mRaiseError == true) ? false : true;
		//------------------ Leer la PROFORMA
					$sqlMvtosToPoliza = "SELECT
					`contable_polizas_proforma`.*,
					`operaciones_tipos`.*
		
					FROM
					`operaciones_tipos` `operaciones_tipos`
						INNER JOIN `contable_polizas_proforma`
						`contable_polizas_proforma`
						ON `operaciones_tipos`.`idoperaciones_tipos` =
						`contable_polizas_proforma`.`tipo_de_mvto`
						AND `contable_polizas_proforma`.`numero_de_recibo` = $numero_de_recibo
					ORDER BY
						`contable_polizas_proforma`.`contable_operacion` DESC,
						`contable_polizas_proforma`.`socio`,
						`contable_polizas_proforma`.`tipo_de_mvto`				
					";
					if ( $sucess == true ){
						
						$rs =  $QL->getDataRecord($sqlMvtosToPoliza);
						
					foreach ($rs as  $rw ){
						$cuenta 				= CUENTA_DE_CUADRE;
						$nombre					= "";
						$socio					= $rw["socio"];
						
						if ( CONTABLE_CUENTAS_POR_SOCIO == true ){
							$xSoc				= new cSocio($socio, true);
							$nombre				= $xSoc->getNombreCompleto();
						}
	
						$documento				= $rw["documento"];
						$monto_movimiento		= $rw["monto"];
						$tipoOp					= $rw["tipo_de_mvto"];
						$tipo_movimiento 		= $rw["contable_operacion"];
						$RecUsr					= $rw["idusuario"];
						$cuenta_bancaria		= $rw["banco"];
						$alternativo			= $rw["cuenta_alternativa"];
						$cargo_movimiento 		= 0;
						$abono_movimiento		= 0;
						if ( $tipo_movimiento == TM_CARGO ){
							$cargo_movimiento	= $monto_movimiento;
							$abono_movimiento	= 0;
						} else {
							$cargo_movimiento	= 0;
							$abono_movimiento	= $monto_movimiento;
						}				
						$formula 				= $rw["cuenta_contable"];
						//Corregir de urgencia: OK: 06Oct2011
						$sForms					= new cValorarFormulas();
						$cuenta 				= $sForms->getCuentaContable($socio, $documento, $formula, $RecUsr, $xRec->getNumeroDeCheque(), $cuenta_bancaria);
						$xLogg->add($sForms->getMessages() , $xLogg->DEVELOPER);
						if(setNoMenorQueCero($alternativo) > 0){
							$cuenta				= $alternativo;
							$xLogg->add("WARN\tCUENTA_ALT\tLa Cuenta $cuenta Es alternativa\r\n" , $xLogg->DEVELOPER);
						}
						//tipo de cuenta es Abonos a efectivo
						
						if ( $cuenta != "NO_CONTABILIZAR" ){
							$xCuenta			= new cCuentaContable($cuenta);
							$xCuenta->init();
							$cuenta				= $xCuenta->get();
							//Carga los datos del Oficial
							if ( $xCuenta->getEsCuentaDeCirculante() == true ){
								$xOf			= new cSystemUser($RecUsr);
								$xOf->init();
								$nombre			=  $xOf->getNombreCompleto();
								$xLogg->add("OK\tCUENTA_ADD\tLa Cuenta $cuenta de Carga por Usuario [ $nombre ]\r\n" , $xLogg->DEVELOPER);
							} else {
								$xLogg->add("OK\tCUENTA\tLa Cuenta de Trabajo es $cuenta Originado del Socio $socio\r\n" , $xLogg->DEVELOPER);
							}
							//verifica para dar de alta a la cuenta
							$Existentes			= $xCuenta->getCountCuenta();
							if ( $Existentes == false ){
								$xLogg->add("WARN\tCUENTA_ADD\tLa Cuenta de Trabajo $cuenta NO EXISTE, se AGREGA\r\n" , $xLogg->DEVELOPER);
								$cuenta			= $xCuenta->add($nombre);
											
								if ( $xCuenta->mRaiseError == true ){
									//$msg				.= "ERROR\tLa Cuenta de Trabajo $cuenta NO EXISTE\r\n";
									//$msg		= $xCuenta->getMessages();
								}
							}
							$xCuenta->init();
							
							$xPol->addMovimiento($cuenta, $cargo_movimiento, $abono_movimiento, "$socio", "$documento:$tipoOp", false, $fecha_de_poliza);
							//$xLogg->add("WARN\tNO_CONT\tAGREGAR $cuenta, $cargo_movimiento, $abono_movimiento\r\n" , $xLogg->DEVELOPER);
							$xLogg->add( $xCuenta->getMessages(), $xLogg->DEVELOPER);
						} else {
							$xLogg->add("WARN\tNO_CONT\tEl Movimiento de $socio | $documento | $tipoOp de Monto $monto_movimiento se OMITE\r\n" , $xLogg->DEVELOPER);
						}
					}
					
						$xPol->setFinalizar();
					} //sucess
					
					$xLogg->add( $xPol->getMessages() , $xLogg->DEVELOPER);
					$this->mIDDePoliza		= $xPol->getCodigo();
				}		
			} 	//END VALUE.- GENERAR AL FINAL
		//}		//END VALUE.- GENERAR CONTABILIDAD
		$this->mMessages	.= $xLogg->getMessages();
		return $xLogg->getMessages();
	}
	function setPolizaPorCajero($cajero, $FechaDeCorte, $NumeroDePoliza = false, $TipoDePoliza = 1, $TipoDePago = "efectivo", $generador = false){
		$sucess							= true;
		$msg							= "=======\tGENERAR POLIZA POR USUARIO NUM $cajero\r\n";
		//if (GENERAR_CONTABILIDAD == true){
		//($generador == GENERAR_POLIZAS_AL_CIERRE) AND 
			if (  isset($cajero) ){
	
				$xUsr					= new cOficial($cajero);
				$DUsr					= $xUsr->getDatos();
				
				$UsrName				= $DUsr["nombres"] . " " . $DUsr["apellidopaterno"]; 
				$tipo_de_poliza			= $TipoDePoliza;
				$xD						= new cFecha(0, $FechaDeCorte);
				$fecha_de_poliza		= $FechaDeCorte;
				$ejercicio_de_poliza	= $xD->anno();
				$periodo_de_poliza		= $xD->mes();
				
				$numero_de_poliza		= ($NumeroDePoliza == false ) ? false : $NumeroDePoliza;
				
				$concepto_poliza		= "$UsrName.-Movimientos del Corte de Fecha $FechaDeCorte";	
				//Obten datos de la Poliza
				$sqlRec 		= "SELECT
									`operaciones_recibos`.*,
									`operaciones_recibostipo`.`tipo_poliza_generada`,
									`operaciones_recibostipo`.`afectacion_en_flujo_efvo`
								FROM
									`operaciones_recibos` `operaciones_recibos`
										INNER JOIN `operaciones_recibostipo`
										`operaciones_recibostipo`
										ON `operaciones_recibos`.`tipo_docto` =
										`operaciones_recibostipo`.
										`idoperaciones_recibostipo`
								WHERE
									(`operaciones_recibos`.`idusuario` = $cajero )
									AND
									(`operaciones_recibos`.`fecha_operacion` = '$FechaDeCorte' )
									AND
									(`operaciones_recibostipo`.`tipo_poliza_generada` = $TipoDePoliza )
									AND
									(`operaciones_recibos`.`tipo_pago` = '$TipoDePago' )
								ORDER BY
									`operaciones_recibos`.`tipo_pago`,
									`operaciones_recibos`.`idusuario`,
									`operaciones_recibos`.`idoperaciones_recibos`
									";
				//$msg		.= "$sqlRec\r\n";
				$rsP = getRecordset($sqlRec);

					//Obtener el la Ultima Poliza Registrada
		//------------------ Agregar Poliza
					$xPol					= new cPoliza($tipo_de_poliza, $numero_de_poliza, $ejercicio_de_poliza, $periodo_de_poliza);
					if ( ( $numero_de_poliza != false ) AND ( $xPol->getCountPolizaByNumero($numero_de_poliza) > 1 ) ){
						$xPol->setDeletePoliza();
					}
					$msg					.= $xPol->add($concepto_poliza, $fecha_de_poliza, $numero_de_poliza, 0, 0, $cajero );
					
					$numero_de_poliza		= $xPol->get();
					$msg					.= "=====\tPOLIZA NUM: $numero_de_poliza | TIPO: $tipo_de_poliza | EJERCICIO: $ejercicio_de_poliza | PERIODO: $periodo_de_poliza\r\n";
					$sucess					= ($xPol->mRaiseError == true) ? false : true;
				//seleccionar todos los recibos				
				while($rwP = mysql_fetch_array($rsP)){

					$dRec 					= $rwP;
					
					$numero_de_recibo		= $dRec["idoperaciones_recibos"];
					$recibo_fiscal			= ( strlen($dRec["recibo_fiscal"]) > 2) ? ";RF:" .trim($dRec["recibo_fiscal"]) : "";
					$observacion_recibo		= $dRec["observacion_recibo"];
					$flujo_efectivo			= $dRec["afectacion_en_flujo_efvo"];
					$cheque					= ( strlen($dRec["cheque_afectador"]) > 2 ) ? ";Ch:" . $dRec["cheque_afectador"] : "";
					$socio					= $dRec["numero_socio"];
					
					$concepto_mvto			= substr( ("R:" . $numero_de_recibo . $recibo_fiscal . $cheque . ";" . $dRec["observacion_recibo"]), 0,80) ;
					
					$msg					.= "$numero_de_recibo\tRECIBO TIPO: " . $dRec["tipo_docto"] ." SUMA: " . $dRec["total_operacion"] . " \r\n";
					//regenera el perfil contable
					//if ( $Regenerar == true ){
						$xUCont		= new cUtileriasParaContabilidad();
						$msg		.= $xUCont->setRegenerarPrepolizaContable($dRec["fecha_operacion"], $numero_de_recibo);
					//}				

		//------------------ Leer la PROFORMA
					$sqlMvtosToPoliza = "SELECT
					`contable_polizas_proforma`.*,
					`operaciones_tipos`.*
		
					FROM
					`operaciones_tipos` `operaciones_tipos`
						INNER JOIN `contable_polizas_proforma`
						`contable_polizas_proforma`
						ON `operaciones_tipos`.`idoperaciones_tipos` =
						`contable_polizas_proforma`.`tipo_de_mvto`
						AND `contable_polizas_proforma`.`numero_de_recibo` = $numero_de_recibo
					ORDER BY
						`contable_polizas_proforma`.`contable_operacion` DESC,
						`contable_polizas_proforma`.`socio`,
						`contable_polizas_proforma`.`documento`,
						`contable_polizas_proforma`.`tipo_de_mvto`			
					";
					if ( $sucess == true ){
						//$msg		.= "$sqlMvtosToPoliza\r\n";
						$rs = mysql_query($sqlMvtosToPoliza, cnnGeneral());
						if(!$rs){
							//error en MYSQL
							$sucess = false;
							saveError(2,$_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Depurar :" . mysql_error() . "|Numero: " .mysql_errno() . "|Instruccion SQL:". $sqlMvtosToPoliza . "|EN:" . $_SESSION["current_file"]);
						}
						
						while( $rw = mysql_fetch_array($rs) ){
							$cuenta 				= CUENTA_DE_CUADRE;
							$nombre					= "";
							$socio					= $rw["socio"];
							
							if ( CONTABLE_CUENTAS_POR_SOCIO == true ){
								$xSoc				= new cSocio($socio, true);
								$nombre				= $xSoc->getNombreCompleto();
							}
							//si la cuenta es de efectivo o similar
							$documento				= $rw["documento"];
							$monto_movimiento		= $rw["monto"];
							$tipoOp					= $rw["tipo_de_mvto"];
							$tipo_movimiento 		= $rw["contable_operacion"];
			
							$cargo_movimiento 		= 0;
							$abono_movimiento		= 0;
							
							if ( $tipo_movimiento == TM_CARGO ){
								$cargo_movimiento	= $monto_movimiento;
								$abono_movimiento	= 0;
							} else {
								$cargo_movimiento	= 0;
								$abono_movimiento	= $monto_movimiento;
							}				
							$formula 				= $rw["cuenta_contable"];
							//Corregir de urgencia: OK: 06Oct2011
							$sForms					= new cValorarFormulas();
							$cuenta 				= $sForms->getCuentaContable($socio, $documento, $formula, $cajero);
							$msg					.= $sForms->getMessages();
							
							if ( $cuenta != "NO_CONTABILIZAR" ){
								$xCuenta			= new cCuentaContable($cuenta);
								$cuenta				= $xCuenta->get();
								//Carga los datos del Oficial
								if ( $xCuenta->getEsCuentaDeCirculante() == true ){
									$nombre			= "EFVO:$UsrName";
									$msg			.= "CUENTA_ADD\tLa Cuenta $cuenta de Carga por Cajero [ $nombre ]\r\n";
								} else {
									$msg			.= "CUENTA\tLa Cuenta de Trabajo es $cuenta Originado del Socio $socio\r\n";
								}
								//verifica para dar de alta a la cuenta
								$Existentes			= $xCuenta->getCountCuenta();
								if ( $Existentes == false ){
									$msg			.= "$numero_de_recibo\tCUENTA_ADD\tLa Cuenta de Trabajo $cuenta NO EXISTE, Se AGREGA\r\n";
									$cuenta			= $xCuenta->add($nombre);
												
									if ( $xCuenta->mRaiseError == true ){
										//$msg				.= "ERROR\tLa Cuenta de Trabajo $cuenta NO EXISTE\r\n";
										//$msg		= $xCuenta->getMessages();
									}
								}
								$xCuenta->init();
								$msg				.= $xCuenta->getMessages();
								$msg				.= $xPol->addMovimiento($cuenta, $cargo_movimiento, $abono_movimiento, "$socio:$documento:$tipoOp", $concepto_mvto);
							} else {
								$msg				.= "$numero_de_recibo\tNO_CONT\tEl Movimiento de $socio | $documento | $tipoOp de Monto $monto_movimiento se OMITE\r\n";
							}
						}	//END MOVIMIENTOS
					
					
					} else {
						$msg				.= "$numero_de_recibo\tEXISTE UN ERROR AL CARGAR EL RECIBO\r\n";
					} 	//	END SUCESS
				
				}		//	END RECIBOS
				$xPol->setReestructurarEfvo();
				$msg				.= $xPol->setFinalizar();
				$msg				.= $xPol->getMessages();
				$this->mIDDePoliza	= $xPol->getCodigo();
			} 	//			END VALUE.- GENERAR AL CIERRE DEL DIA
		//}		//			END VALUE.- GENERAR CONTABILIDAD
		return $msg;
	}
	function getIDPoliza(){ return $this->mIDDePoliza;	}
	/**
	 * Genera las polizas del dia por Cajero
	 * @param variant $fecha
	 */
	function setGenerarPolizasAlCierre($fecha = false){
		$xLi		= new cSQLListas();
		$ql			= new MQL();
		$msg		= "";
		$otros		= " AND (`operaciones_recibostipo`.`tipo_poliza_generada` != 999) AND `operaciones_recibos`.`total_operacion` > 0 ";
		$sql		= $xLi->getListadoDeRecibos("", "", "", $fecha, $fecha, $otros);
		$rs			= $ql->getDataRecord($sql);
		foreach ($rs as $rw){
			$recibo		= $rw["numero"];
			$xPol		= new cPoliza(false);
			if ( $xPol->setPorRecibo($recibo) == true ){
				$msg	.= "WARN\tLa Poliza del recibo $recibo Existe con el ID " . $xPol->getCodigoUnico() . "\r\n";
			} else {
				$msg	.= $this->setRegenerarPrepolizaContable($fecha, $recibo);
				$msg	.= $this->setPolizaPorRecibo($recibo, true);
				if ( $xPol->setPorRecibo($recibo) == false ){
					$msg.= "ERROR\tAl generar la Poliza del recibo $recibo\r\n";
				}
			}
			$msg		.= $xPol->getMessages();
		}
		$this->mMessages.= $msg;
		return $msg;
	} //end function
	function setResetearContabilidad(){
		$xQL	= new MQL();
		$sql	= array();
		$notas	= "";
		$sql[]	= "TRUNCATE `contable_movimientos`";
		$sql[]	= "TRUNCATE `contable_polizas`";
		$sql[]	= "TRUNCATE `contable_polizas_proforma`";
		$sql[]	= "UPDATE contable_saldos SET saldo_inicial=0, imp1=0, imp2=0, imp3=0, imp4=0, imp5=0, imp6=0, imp7=0, imp8=0, imp9=0, imp10=0, imp11=0, imp12=0, imp13=0, imp14=0 ";
		//$sql[]	= "TRUNCATE ";
		foreach ($sql as $idx => $cnt){
			$rs	= $xQL->setRawQuery($cnt);
		}
		return $notas;
	}
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
}


/**
 * Efectua las operaciones para exportar a formato CompacW 2004
 * @version 1.0.02
 * @package contable
 * @subpackage core
 */
class cPolizaCompacW{
	private $mFecha 				= false;
	private $mTipoDePoliza 			= false;
	private $mNumeroDePoliza		= false;
	private $mConceptoPoliza		= false;
	private $mDatosDePoliza			= "";
	private $mMovimientosDePoliza	= "";
	private $mEjercicio				= 0;
	private $mPeriodo				= 0;
	private $mMessages				= "";

	function __construct($numero, $ejercicio = false, $periodo = false, $tipo = false){
		$periodo				= ( $periodo == false ) ? EACP_PER_CONTABLE : $periodo;
		$ejercicio				= ( $ejercicio == false ) ? EJERCICIO_CONTABLE : $ejercicio;
		$tipo					= ( $tipo == false ) ? POLIZA_INGRESOS : $tipo; 
		
		$this->mNumeroDePoliza	= $numero;
		$this->mEjercicio		= $ejercicio;
		$this->mPeriodo			= $periodo;
		$this->mTipoDePoliza	= $tipo;
		
	}
	function setDatosDePoliza($tipo, $concepto, $fecha = false){
		if ( $fecha == false ){
			$fecha	= fechasys();
		}
		$this->mfecha			= $fecha;
		$this->mTipoDePoliza	= $tipo;
		$this->mConceptoPoliza	= $concepto;
	}
	function addMovimiento($TipoDeMovimiento, $cuenta, $monto, $referencia = "", $concepto = "" ){
		$CWTipoMvto 	= array("1"=>1,"-1"=>"2");

		$xCuenta		= new cCuentaContable($cuenta);
		$cuenta			= $xCuenta->get();
			//Corrige la Cuenta de Cuadre
			if ($cuenta	== CUENTA_DE_CUADRE){
				$cuenta = "_CUADRE";
			}
							//Tipo M + espacio
							//Cuenta   20
							//Referencia 10
							//TipoMvto 2 espacios 1 Cargo 2 Abono
							//Importe 16 Alineado
							//espacio + 000 + espacio + "            0.00 "
							//concepto 30 + espacio
						$this->mMovimientosDePoliza		 .= "M " . substr(str_pad($cuenta, 20, " ", STR_PAD_RIGHT), 0, 20);
						$this->mMovimientosDePoliza		.= " " . substr(str_pad($referencia, 10, " ", STR_PAD_RIGHT), 0, 10);
						$this->mMovimientosDePoliza		.= " " . $CWTipoMvto[$TipoDeMovimiento];
						$this->mMovimientosDePoliza		.= " " . substr(str_pad($monto, 16, " ", STR_PAD_LEFT), -16);
						$this->mMovimientosDePoliza		.= " 000 " . "            0.00 " .  substr(str_pad($referencia, 30, " ", STR_PAD_RIGHT), 0, 30) . "
";
	}
	function setExport($BtnClass = "button"){
			$WriteText	= "P " . date("Ymd", strtotime($this->mfecha));
			$WriteText .= " " . $this->mTipoDePoliza;
			$WriteText .= " " . substr(str_pad($this->mNumeroDePoliza, 8, "0", STR_PAD_LEFT), -8);
			$WriteText .= " 1 000 " . substr(str_pad($this->mConceptoPoliza, 100, " " , STR_PAD_RIGHT),0, 100);
			$WriteText .= " 01 2
";

			$WriteText	.= $this->mMovimientosDePoliza;

			$nombre		= getSucursal() . "-poliza-" . $this->mEjercicio . "." . $this->mPeriodo . "-" . $this->mTipoDePoliza . "." . $this->mNumeroDePoliza . "";
			$xFile		= new cFileLog($nombre, true);
			$xFile->setWrite($WriteText);
			$xFile->setClose();
			return $xFile->getLinkDownload($nombre, $BtnClass);
	}
	function setRun(){
		$ejercicio		= $this->mEjercicio;
		$periodo		= $this->mPeriodo;
		$tipoPoliza		= $this->mTipoDePoliza;
		$numeroPoliza	= $this->mNumeroDePoliza;
		$QL				= new MQL();
		$sqlMvtos = "SELECT
						`contable_movimientos`.* 
						FROM
							`contable_movimientos` `contable_movimientos` 
						WHERE
							(`contable_movimientos`.`ejercicio` =$ejercicio) AND
							(`contable_movimientos`.`periodo` =$periodo) AND
							(`contable_movimientos`.`tipopoliza` =$tipoPoliza) AND
							(`contable_movimientos`.`numeropoliza` =$numeroPoliza)
						ORDER BY `contable_movimientos`.`ejercicio`,
						`contable_movimientos`.`periodo`,
						`contable_movimientos`.`tipopoliza`,
						`contable_movimientos`.`numeropoliza`,
						`contable_movimientos`.`numeromovimiento` ";
			$MRs 				=  $QL->getDataRecord($sqlMvtos);
			foreach ($MRs as $MRw){
				$cuenta 		= $MRw["numerocuenta"];
				$referencia		= $MRw["referencia"];
				//$TipoDeMovimiento, $cuenta, $monto, $referencia = "", $concepto = ""
				$tipoMvto		= $MRw["tipomovimiento"];
				$monto			= $MRw["importe"];
				$concepto		= $MRw["concepto"];
				$this->addMovimiento($tipoMvto, $cuenta, $monto, $referencia, $concepto);
			}		
	}
	function initByID($strID = ""){
		$xPol		= new cPoliza(false);
		$xPol->setPorCodigo($strID);

		$this->mNumeroDePoliza	= $xPol->getNumero();
		$this->mEjercicio		= $xPol->getEjercicio();
		$this->mPeriodo			= $xPol->getPeriodo();
		$this->mTipoDePoliza	= $xPol->getTipo();
				
		$DPComp		= $xPol->getDatos();
		$concepto	= $xPol->getConcepto();
		$fecha		= $xPol->getFecha();
		
		$this->setDatosDePoliza($this->mTipoDePoliza, $concepto, $fecha);
	}
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
}
class cCatalogoCompacW{
	private $mEquivalencias	=  array(
					"AD"=>"A",
					"AA"=>"B",
					"PD"=>"C",
					"PA"=>"D",
					"CD"=>"E",
					"CA"=>"F",
					"RD"=>"G",
					"RA"=>"H",
					"OD"=>"K",
					"OA"=>"L",
					"ED"=>"I",
					"EA"=>"J"
					);
	private $mEquivalenciasCW	=  array(
			"A" => "AD",
			"B" => "AA",
			"C" => "PD",
			"D" => "PA",
			"E" => "CD",
			"F" => "CA",
			"G" => "RD",
			"H" => "RA",
			"K" =>  "OD",
			"L" =>  "OA",
			"I" =>  "ED",
			"J" => "EA"
	);
		
	function __construct(){	}
	function getEquivalenciaCW(){	}
	function getEquivalencia($digitoCW){ return $this->mEquivalenciasCW[$digitoCW]; }
	function setExport($FechaInicial = false, $FechaFinal = false ){
		$wByFi		= ( $FechaInicial == false) ? "" : " WHERE fecha_de_alta>='$FechaInicial' ";
		$wByFf		= ( $FechaFinal == false) ? "" : " AND fecha_de_alta<='$FechaFinal' ";
		$sucursal	= getSucursal();
		$arrTipos = array(
					"AD"=>"A",
					"AA"=>"B",
					"PD"=>"C",
					"PA"=>"D",
					"CD"=>"E",
					"CA"=>"F",
					"RD"=>"G",
					"RA"=>"H",
					"OD"=>"K",
					"OA"=>"L",
					"ED"=>"I",
					"EA"=>"J"
					);
		//safe => Compaq
		$arrMayor = array(
			"3"	=> "1",	
			"4"	=> "2",	
			"1"	=> "3", 
			"2"	=> "4"
			);
	//3 safe mayor
	$WriteText	= "F  00000000000000\r\n"; //cuenta de flujo de efectivo
	
		$sql = "SELECT numero, equivalencia, nombre, tipo, ctamayor, afectable, centro_de_costo, fecha_de_alta, digitoagrupador 
    			FROM contable_catalogo $wByFi $wByFf 
    			ORDER BY numero
    			";
		$rs		= mysql_query($sql, cnnGeneral() );
		if (!$rs){
			//Codigo de Control de Error
			saveError(2,$_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Depurar :" . mysql_error() . "|||Numero: " .mysql_errno() . "|||Instruccion SQL: \n ". $sql);
		}		
		//$WriteText	.= "$sql\r\n";
		while ( $rw = mysql_fetch_array($rs) ) {
			//XXX: Si el titulo asignar 0 a superior
			//$WriteText	.= "C ";
			//CompAQi
			$WriteText	.= "C  ";
			//$WriteText	.= substr(str_pad($rw["numero"], 20, " ", STR_PAD_RIGHT), 0, 20);
			$WriteText	.= substr(str_pad($rw["numero"], 30, " ", STR_PAD_RIGHT), 0, 30);
			$WriteText	.= " ";
			$WriteText	.= substr(str_pad( trim($rw["nombre"]), 50, " ", STR_PAD_RIGHT), 0, 50);
			$WriteText	.= " ";
			$WriteText	.= substr(str_pad("", 50, " ", STR_PAD_RIGHT), 0, 50);
			$WriteText	.= " ";
			//cuenta superior, mod a 30. compaqi
			//$WriteText	.= substr(str_pad( cuenta_superior( $rw["numero"] ), 20, " ", STR_PAD_RIGHT), 0, 20);
			
			$WriteText	.= ( $rw["ctamayor"] == 1) ?  substr(str_pad( cuenta_superior( "0" ), 30, " ", STR_PAD_RIGHT), 0, 30) : substr(str_pad( cuenta_superior( $rw["numero"] ), 30, " ", STR_PAD_RIGHT), 0, 30);
			
			$WriteText	.= " ";
			$WriteText	.= $arrTipos[ $rw["tipo"] ];
			$WriteText	.= " ";
			$WriteText	.= "0 ";		//Baja
			$WriteText	.= $arrMayor[ $rw["ctamayor"] ];
			$WriteText	.= " ";
			$WriteText	.= "0 ";
			$WriteText	.= date("Ymd" , strtotime( $rw["fecha_de_alta"]) );
			$WriteText	.= " ";
			//Actualizacion ContPAQi
			$WriteText	.= "81 "; //Sistema de Origen
			$WriteText	.= "   1 "; //Moneda
			$WriteText	.= "   0 "; //Digito Agrupador
			$WriteText	.= "0    "; //Segmento de Negocio 
			$WriteText	.= "0 "; //Mvto.Segmento de Negocio
			$WriteText	.= "\r\n";
			//$WriteText	.= "01 01 0000 000\r\n";
		}
		$nombre		= "$sucursal-catalogo-contable-" . date("Ymd", strtotime( fechasys() ) ) . "-" . rand(0, 1000) . "";
		
		$xFile		= new cFileLog($nombre);
		$xFile->setWrite($WriteText);
		$xFile->setClose();

		return $xFile->getLinkDownload($nombre);
	}
}




?>