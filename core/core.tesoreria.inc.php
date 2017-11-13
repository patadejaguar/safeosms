<?php
/**
 * @see Core de Impuestos
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package creditos
 *  Core Tax File
 * 		16/05/2008
 */

include_once("core.deprecated.inc.php");
include_once("entidad.datos.php");
include_once("core.config.inc.php");
include_once("core.fechas.inc.php");
include_once("core.db.inc.php");
include_once("core.common.inc.php");
include_once("core.operaciones.inc.php");

@include_once("../libs/sql.inc.php");



/*
 			"efectivo" =>  "cobro-efectivo.frm.php",
			"efectivo.egreso" =>  "",
			"cheque.ingreso" => "cobro-cheques-internos.frm.php",
	
			"cheque" => 9200,
			
			"transferencia" => "cobro-transferencia.frm.php",
			"transferencia.egreso" => 9101,
	
			"foraneo" => "cobro-cheques.frm.php",
			"descuento" => "cobro-cargo-documento.frm.php",
			
			"multiple"	=> "cobro-multiple.frm.php",
			"ninguno" => 99
		);	
 */
//=====================================================================================================
class cCaja{
	private $mFecha			= false;
	private $mMessages		= "";
	private $mKey			= "";
	private $mCajero		= "";
	private $mIP			= "";
	private $mMoneda		= "";
	private $mMontoOrigen	= 0;
	private $mTiposDeCobro	= array();
	private $mObj			= null;
	private $mTotalPago		= 0;
	private $mListaFormas	= array();
	private $mListaDeFechas	= array();
	private $mClaveDeRecibo	= null;
	private $mBancoActivo		= null; //datos del pago
	private $mCuentaBancoActivo	= null;
	private $mMonedaActiva		= "MXN";
	private $mChequeActivo		= null;
	private $mFondoInicial		= 0;
	private $mFondosArqueados	= 0;
	private $mDatos				= array(); 
	private $mEstadoActual		= false;
	private $mCajaIniciada		= false; 
	private $mSumaRecibos		= 0;
	private $mSumaCobros		= 0;
	private $mSumaArqueo		= 0;
	private $mArqueoInit		= false; 
	 
	function __construct($clave = false, $fecha = false){
		$xF				= new cFecha();
		$fecha			= $xF->getFechaISO($fecha);
		$this->mFecha	= $fecha;
		$this->mMoneda	= AML_CLAVE_MONEDA_LOCAL;
		$this->mEstadoActual	= TESORERIA_CAJA_CERRADA;
		$this->mCajero			= getUsuarioActual();
		if($clave == false){ $this->initByFechaUsuario($fecha, $this->mCajero); }
	}
	function getKey(){
		
		$this->mCajero 		= ($this->mCajero == "") ? $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"] : $this->mCajero;
		$this->mIP			= ($this->mIP != "") ? $this->mIP : $_SERVER['REMOTE_ADDR'];
		$this->mFecha		= $this->mFecha;
		$this->mMoneda		= AML_CLAVE_MONEDA_LOCAL;
		$this->mKey			= md5($this->mCajero . $this->mFecha);
		
		return $this->mKey;	}
	function initByFechaUsuario($fecha, $cajero){
		$this->mKey			= md5($cajero . $fecha);
		$this->mFecha		= $fecha;
		$this->mCajero		= $cajero;
		return $this->init($this->mKey);
	}
	function init($clave = false){
		$iniciar		= false;
		$clave			= ($clave == false) ? $this->getKey() : $clave;

		$this->mKey			= $clave;
			//cargar datos de la BD
			$xCaja				= new cTesoreria_cajas();
			$d					= $this->getInfoBox($clave);
			$this->mMessages	.= "OK\tCargar Datos con ID $clave\r\n";
			$xCaja->setData($d);
			if(isset($d[ $xCaja->getKey()] )){
				$this->mCajero		= $xCaja->idusuario()->v();
				$this->mIP			= $xCaja->maquina()->v();
				
				$this->mFondoInicial	= $xCaja->fondos_iniciales()->v();
				$this->mFecha			= $xCaja->fecha_inicio()->v();
				$this->mEstadoActual	= $xCaja->estatus()->v();
				
				$iniciar				= true;
			}
			
			if(MODULO_CAJA_ACTIVADO	== false OR MODO_DEBUG == true) { $this->mEstadoActual = TESORERIA_CAJA_ABIERTA; }
		$this->mCajaIniciada	= $iniciar;
		return $this->mCajaIniciada;
	}
	function setOpenBox($oficial_superior, $fondo_inicial = 0){
		$osuperior		= $oficial_superior;
		unset($_SESSION["codigo_de_caja"]);
		$stat			= false;
		$osuperior		= setNoMenorQueCero($osuperior);	
		$key			= $this->getKey();
		$usr 			= $this->mCajero;
		$ip				= $this->mIP;
		$hora			= date("H:i:s");
		$fecha			= $this->mFecha;
		$xLog			= new cCoreLog();
		$xQL			= new MQL();
		

		//VALIDAR SI ESTA ABIERTA NO CERRADA
		$eacp 		= EACP_CLAVE;
		$suc		= getSucursal();

		$sqlNC = "INSERT INTO tesoreria_cajas
		(idtesoreria_cajas, eacp, sucursal, maquina, idusuario, fecha_inicio, hora_inicio, estatus, usuario_que_autoriza, firma_digital, fondos_iniciales)
    	VALUES('$key', '$eacp', '$suc', '$ip', $usr, '$fecha', '$hora', '" . TESORERIA_CAJA_ABIERTA . "', $osuperior, '', $fondo_inicial)";
		if($usr == $osuperior AND (MODO_DEBUG == false)){
			$stat	= false;
			$xLog->add("ERROR\tUsuario no autorizado para abrir CAJA\r\n");
			if (TESORERIA_FORZAR_SESSION == true){
				$xLog->add("WARN\tSession forzada\r\n");
				$stat	= $xQL->setRawQuery($sqlNC);
			}
		} else {
			if($osuperior <= 0){ 
				$xLog->add("ERROR\tNo existe el Usuario superior\r\n");
				$stat			= false; 
			} else { 
				if( $this->existe() == true ){
					$this->init($key);
					if($this->getEstatus() == TESORERIA_CAJA_CERRADA ){
						$xLog->add("ERROR\tCERRADO\tLa Caja con Clave $key al esta cerrada!!\r\n");
						$stat	= false;					
					} else {
						$_SESSION["codigo_de_caja"] = $key;
						$xLog->add("OK\tACTIVA\tLa Caja con Clave $key al parecer existe!!\r\n");
						$stat	= true;
					}	
				} else { 
					$stat		= $xQL->setRawQuery($sqlNC);
				}
			}
		}
		$xLog->add("OK\tCLAVE\tLa Clave de Caja Cargada es ($key)\r\n");
		$this->mMessages	.= $xLog->getMessages();
		return $stat;
	}
	function setCloseBox($oficial_de_cierre = false, $fondos_arqueados = 0){
		$result				= true;
		if($oficial_de_cierre == false){
			$this->mMessages.= "ERROR\tEl Oficial de Cierre no existe\r\n";
			$result		= false;
		} else if( $this->mCajaIniciada == false ){
			$this->mMessages.= "ERROR\tNo se cierra una NO inicializada\r\n";
			$result		= false;
		} else {
			$this->init();
			
			if($this->getEstatus() == TESORERIA_CAJA_CERRADA){
				$result		= false;
				$this->mMessages	.= "ERROR\tLa Caja ya esta cerrada\r\n";
			} else { 
				//actualizar estado
				$sql		= "UPDATE tesoreria_cajas SET estatus='" . TESORERIA_CAJA_CERRADA . "', usuario_que_autoriza=$oficial_de_cierre, firma_digital='', fondos_arqueados=$fondos_arqueados  WHERE idtesoreria_cajas='" . $this->mKey . "' ";
				$xQL		= new MQL();
				$rs			= $xQL->setRawQuery($sql);
				$result		= ($rs === false) ? false : true;	
				//Ejecutar regla de negocio
			
				$xRegla		= new cReglaDeNegocio(RN_CAJA_AL_CERRAR);
				$xRegla->setVariables(array("clave_de_usuario" => $this->mCajero, "fecha" => $this->mFecha));
				$xRegla->setExecuteActions();
				unset($_SESSION["codigo_de_caja"]);
			}
		}
		return $result;
	}
	function getEstatus(){ 
		if($this->mCajaIniciada == false){ $this->init(); }
		return $this->mEstadoActual;	
	}
	function existe(){
		$key	= $this->getKey();
		$sql	= "SELECT COUNT(idtesoreria_cajas) AS 'existentes' FROM tesoreria_cajas WHERE idtesoreria_cajas='$key' ";
		return ( mifila($sql, "existentes") >= 1) ? true : false;
	}
	function setMoneda($moneda){
		$this->mMoneda	= $moneda;
	}
	function setMontoOriginal($monto){ $this->mMontoOrigen = $monto; }
	function getInfoBox($key = false){
		//obtener el ID por IP + fecha
		$arrInfo 	= array();
		$key		= ($key == false) ? $this->getKey() : $key;
		$this->mKey	= $key;
		$this->mMessages.= "OK\tLa Clave de Caja es $key\r\n";
		$sqlIF 		= "SELECT * FROM	`tesoreria_cajas` WHERE (`tesoreria_cajas`.`idtesoreria_cajas` = '$key') LIMIT 0,1";
		return obten_filas($sqlIF);
	}
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }	
	function getCorteDeCaja($put = OUT_HTML){}
	/**
	 * Determina si un recibo esta saldado en tesoreria
	 * @param integer $recibo
	 */
	function getReciboEnCorte($recibo){
		$monto			= 0;
		$run			= true;
		//elegir el tipo de recibo, buscar en los lugares adecuados
		$xRec			= new cReciboDeOperacion(false, false, $recibo);
		if($xRec->init() == true){
			$xQL		= new MQL();
			$credito	= $xRec->getCodigoDeDocumento();
			$sql		= "SELECT  COUNT(`idempresas_cobranza`) AS `items` FROM `empresas_cobranza` WHERE `recibo`=$recibo AND `clave_de_credito`=$credito";
			$items		= $xQL->getDataValue($sql, "items");
			if($items > 0){
				$monto	= $xRec->getTotal();
				$run	= false;
			}
		}
		if($run == true) {
			$xQL	= new MQL();
			$sql	= "SELECT pagado  FROM tesoreria_recibos_pagados WHERE recibo = $recibo";
			$monto	= $xQL->getDataValue($sql, "pagado");
		}
		return $monto;
	}
	function setCobroEfectivo($recibo, $MontoRecibido, $MontoOperado = 0, $notas = "", $fecha = false, $Moneda = AML_CLAVE_MONEDA_LOCAL, $MontoOriginal = 0){
		return $this->addOperacion($recibo, TESORERIA_COBRO_EFECTIVO, $MontoRecibido, $MontoOperado, 0,FALLBACK_CLAVE_DE_BANCO, 
				"", 0, 0, $notas, $fecha, false, $Moneda, $MontoOriginal);		
	}
	function setCobroChequeInterno($recibo, $MontoRecibido, $cuenta, $cheque, $diferencia = 0){
		$xCta	= new cCuentaBancaria($cuenta); $xCta->init();
		$banco	= $xCta->getClaveDeBanco();
		return $this->addOperacion($recibo, TESORERIA_COBRO_INTERNO, $MontoRecibido, ($MontoRecibido-$diferencia), $diferencia, $banco, $cheque, $cuenta);
	}
	function setCobroDescuentoCheque($recibo, $MontoRecibido, $cuenta, $cheque, $diferencia = 0){
		$xCta	= new cCuentaBancaria($cuenta); $xCta->init();
		$banco	= $xCta->getClaveDeBanco();
		$xCta->addOperacion(BANCOS_OPERACION_DEPOSITO, $cheque, $recibo, "", $MontoRecibido);
		return $this->addOperacion($recibo, TESORERIA_COBRO_DESCTO, $MontoRecibido, ($MontoRecibido-$diferencia), $diferencia, $banco, $cheque, $cuenta);
	}	
	
	/**
	 * Agrega un cobro por Transferencia SPEI o Deposito en Cuenta
	 */
	function setCobroTransferencia($recibo, $cuenta_bancaria, $monto_depositado, $diferencia = 0, $fecha = false, $notas = "",
			$persona = false, $documento = false, $transaccion = SYS_NINGUNO, $bancodeorigen = false){
		$cheque		= DEFAULT_CHEQUE;
		$fecha		= ($fecha == false) ? fechasys() : $fecha;
		$persona	= ($persona == false) ? DEFAULT_SOCIO : $persona;
		$documento	= ($documento == false) ? DEFAULT_CREDITO : $documento;
		//agregar operacion a Movimientos Bancarios
		$xBanc		= new cCuentaBancaria($cuenta_bancaria); $xBanc->init();
		$banco		= ($bancodeorigen == false) ? $xBanc->getClaveDeBanco() : $bancodeorigen;
		$xBanc->setNuevoDeposito($monto_depositado, $fecha, 0, $persona, $recibo, $notas, $documento );
		//
		return $this->addOperacion($recibo, TESORERIA_COBRO_TRANSFERENCIA , $monto_depositado,
					   	($monto_depositado-$diferencia), $diferencia, $banco, "", $cuenta_bancaria, 0, $notas, $fecha, false,
						false,0, $persona, $documento, $transaccion);
	}
	function setCobroChequeForaneo($recibo, $MontoRecibido, $banco = FALLBACK_CLAVE_DE_BANCO, $cheque = DEFAULT_CHEQUE, $diferencia = 0){
		return $this->addOperacion($recibo, TESORERIA_COBRO_CHEQUE, $MontoRecibido, ($MontoRecibido-$diferencia), $diferencia, $banco, $cheque);
	}

	function setCobroCargoDocumento($recibo, $MontoRecibido, $documento, $diferencia = 0){
		$banco	= 0;
		$cheque = 0;
		$cuenta	= 0;
		return $this->addOperacion($recibo, TESORERIA_COBRO_DOCTO, $MontoRecibido, ($MontoRecibido-$diferencia), $diferencia, $banco, $cheque, $cuenta);
	}
	/**
	 * Agrega una operacion a tesoreria
	 * @param integer $recibo				Numero de recibo
	 * @param string $tipoDeExposicion		Tipo de pago ninguno efectivo			
	 * @param number $MontoRecibido		Monto que se recibe en la operacion
	 * @param number $MontoOperacion		Monto total de la Operacion
	 * @param number $MontoCambio
	 * @param string $banco
	 * @param number $cheque
	 * @param number $CuentaBancaria
	 * @param number $DocumentoDescontado	Documento/cuenta al que va dirigido el Cargo.
	 * @param string $Observaciones
	 * @param string $fecha
	 * @param string $hora
	 * @param string $moneda
	 * @param number $monto_original
	 * @param string $persona
	 * @param string $documento				Documento de Origen. Credito etc.
	 * @param string $transaccionAML		Transaccion AML equivalencia de instrumento Financiero
	 * @return boolean
	 */
	function addOperacion($recibo, $tipoDeExposicion, $MontoRecibido, $MontoOperacion = 0, $MontoCambio = 0, 
		$banco = FALLBACK_CLAVE_DE_BANCO, $cheque = 0, $CuentaBancaria = 0, $DocumentoDescontado = 0, $Observaciones = "", $fecha = false, $hora = false, $moneda	= false, 
			$monto_original = 0, $persona = false, $documento = false, $transaccionAML = SYS_NINGUNO ){
		$xF				= new cFecha();
		$xT				= new cTipos();
		$xQL			= new MQL();		
		$sucess			= false;
		$persona		= setNoMenorQueCero($persona);
		$documento		= setNoMenorQueCero($documento);
		$hora			= ($hora == false ) ? date("H:i:s") : $hora;
		$CodigoDeCaja	= $this->getKey();
		$cajero			= $this->mCajero;
		$cheque			= setNoMenorQueCero($cheque);
		$banco			= (setNoMenorQueCero($banco) <= 0) ? FALLBACK_CLAVE_DE_BANCO : $banco;
		$this->mMoneda	= ($this->mMoneda == "") ? AML_CLAVE_MONEDA_LOCAL : $this->mMoneda;
		if(($moneda == false) OR ($moneda == AML_CLAVE_MONEDA_LOCAL)) {
			$moneda		= ($this->mMoneda != AML_CLAVE_MONEDA_LOCAL) ? $this->mMoneda : AML_CLAVE_MONEDA_LOCAL;
		}
		$monto_original	= ($monto_original == 0) ? $this->mMontoOrigen : $monto_original;
		
		$xRec			= new cReciboDeOperacion(false, false, $recibo);
		if($xRec->init() == true){
			$persona		= ($persona <=DEFAULT_SOCIO) ? $xRec->getCodigoDeSocio() : $persona;
			$documento		= ($documento <= DEFAULT_CREDITO) ? $xRec->getCodigoDeDocumento() : $documento;
			$afectaCaja		= $xRec->getOTipoRecibo()->getAfectacionEnEfvo(); // $DRec["afectacion_en_flujo_efvo"]
			$MontoOperacion	= ($MontoOperacion	== 0 ) ? $xRec->getTotal() : $MontoOperacion;
			$fecha			= ($fecha === false) ? $xRec->getFechaDeRecibo() : $fecha;//TODO: cambiar a la de captura
		}
		$fecha			= $xF->getFechaISO($fecha);
		//end rec
		$MontoCambio	= ($MontoCambio == 0 ) ? $MontoRecibido - $MontoOperacion : $MontoCambio;
		//Obtener Banco
		$pais			= EACP_CLAVE_DE_PAIS;
		$eacp			= EACP_CLAVE;
		$sucursal		= getSucursal();
		if($banco == FALLBACK_CLAVE_DE_BANCO AND $CuentaBancaria > 0){
			$xCta		= new cCuentaBancaria($CuentaBancaria);
			if($xCta->init() == true){
				$banco		= $xCta->getClaveDeBanco();
				$pais		= $xCta->getPaisDeOrigen();
			}
		}
		$xBanco			= new cBancos_entidades();
		$xBanco->setData( $xBanco->query()->initByID($banco) );
		$pais			= $xBanco->pais_de_origen()->v();
		//Actualizar Moneda por pais
		if($banco != FALLBACK_CLAVE_DE_BANCO AND $moneda == AML_CLAVE_MONEDA_LOCAL AND $pais != EACP_CLAVE_DE_PAIS){
			$xPais	= new cDomiciliosPaises($pais);
			if($xPais->init() == true){
				$moneda		= $xPais->getMoneda();
			}
		}
		$xTes		= new cTesoreria_cajas_movimientos();
		$xTes->banco($banco);
		$xTes->codigo_de_caja($CodigoDeCaja);
		$xTes->cuenta_bancaria($CuentaBancaria);
		$xTes->documento($documento);
		$xTes->documento_descontado($DocumentoDescontado);
		$xTes->eacp(EACP_CLAVE);
		$xTes->fecha($fecha);
		$xTes->hora($hora);
		$xTes->idtesoreria_cajas_movimientos( $xTes->query()->getLastID() );
		$xTes->idusuario($cajero);
		$xTes->moneda_de_operacion($moneda);
		if($moneda	!= AML_CLAVE_MONEDA_LOCAL ){  }
		//EVALUAR MONEDA
		$xTes->monto_del_movimiento($MontoOperacion);
		$xTes->monto_en_cambio($MontoCambio);
		$xTes->monto_recibido($MontoRecibido);
		$xTes->numero_de_cheque($cheque);
		$xTes->observaciones($Observaciones);
		$xTes->recibo($recibo);
		$xTes->sucursal($sucursal);
		$xTes->tipo_de_exposicion($tipoDeExposicion);
		$xTes->tipo_de_movimiento($afectaCaja);
		$xTes->unidades_de_moneda( setNoMenorQueCero($monto_original) );
		$xTes->persona($persona);
		
		$sucess		= $xT->getEvalNotNull( array($cajero, $CodigoDeCaja) );
		if( $sucess == true ){
			$q		= $xTes->query()->insert();
			$id		= $q->save();
			$sucess	= ($id == false) ? false : true;
			if($sucess == true){
				$this->mMessages	.= "OK\tRegistro Agregado exitosamente, relacionado con el Recibo $recibo, Operacion $id ($moneda|$monto_original|$cheque|$tipoDeExposicion|$transaccionAML)\r\n";
				//Agregar recibo
				$xRec->setDatosDePago($moneda, $monto_original, $cheque, $tipoDeExposicion, $transaccionAML, $CuentaBancaria);
				$this->mMessages	.= $xRec->getMessages(OUT_TXT);
			}
		} else {
			$this->mMessages	.= "ERROR\tSe produjo un error al Agregar la Operacion de Tesoreria($cajero, $CodigoDeCaja)\r\n";
		}
		return $sucess;
	}
	/**
	 * Retorna un array con codigo numerico de Operaciones de Circulante
	 */
	function getAOperacionesDeCirculante(){ $xCat	= new cCatalogoOperacionesDeCaja(); return $xCat->getCatalogoEquivalente();	}
	function getClaveDeRecibo(){ return $this->mClaveDeRecibo; }
	function initByRecibo($recibo	= false){
		$recibo	= ($recibo == false) ? $this->getClaveDeRecibo()  : $recibo;
		$this->mClaveDeRecibo	= $recibo;
		//$xC	= new cTesoreria_cajas_movimientos();
		$mql	= new MQL();
		$sql	= "SELECT
				`tesoreria_cajas_movimientos`.`recibo`                    AS `recibo`,
				MAX(`tesoreria_cajas_movimientos`.`documento`)            AS `documento` ,
				`tesoreria_cajas_movimientos`.`tipo_de_exposicion`        AS `forma`,
				`tesoreria_cajas_movimientos`.`fecha`                     AS `fecha`,
				SUM(`tesoreria_cajas_movimientos`.`monto_del_movimiento`) AS `monto`,
				MAX(`tesoreria_cajas_movimientos`.`numero_de_cheque`)     AS `cheque`,
				MAX(`tesoreria_cajas_movimientos`.`cuenta_bancaria`)      AS `cuentabancaria`,
				MAX(`tesoreria_cajas_movimientos`.`banco`)      AS `banco`,
				SUM(`tesoreria_cajas_movimientos`.`moneda_de_operacion`)  AS `moneda`,
				`tesoreria_cajas_movimientos`.`unidades_de_moneda`        AS 
				`numerooriginal`
				
			FROM
				`tesoreria_cajas_movimientos` `tesoreria_cajas_movimientos` 
			WHERE
				(`tesoreria_cajas_movimientos`.`recibo` =" . $recibo. ") 				
			GROUP BY
				`tesoreria_cajas_movimientos`.`recibo`,
				`tesoreria_cajas_movimientos`.`tipo_de_exposicion`,
				`tesoreria_cajas_movimientos`.`fecha`,
				`tesoreria_cajas_movimientos`.`unidades_de_moneda` ";
		$datos		= $mql->getDataRecord($sql);
		foreach ($datos as $rows){
			$this->mListaDeFechas[] 	= $rows["fecha"];
			$this->mListaFormas[]		= $rows["forma"];
			$this->mTotalPago			+= $rows["monto"];
			$this->mMonedaActiva		= $rows["moneda"];
			$this->mBancoActivo			= $rows["banco"];
			$this->mChequeActivo		= $rows["cheque"];
			$this->mCuentaBancoActivo	= $rows["cuentabancaria"];
		}
	}
	function getBancoActivo(){ return $this->mBancoActivo; }
	function getCuentaBancoActivo(){ return $this->mCuentaBancoActivo; }
	
	function getMonedaActiva(){ return $this->mMonedaActiva; }
	function getChequeActivo(){ return $this->mChequeActivo; }
	function getLinkDeCorte(){
		$fecha	= $this->mFecha;
		$cajero	= $this->mCajero;
		$url	="/rpttesoreria/corte_de_caja.rpt.php?on=$fecha&off=$fecha&cajero=$cajero&out=default&dependencia=todas";
		return $url;
	}
	function getResumenDeCaja(){
		$xF				= new cFecha();
		$xSQL			= new cSQLListas();
		$xTxt			= new cHText();
		$xLn			= new cLang();
		$xTbl			= new cHTabla();
		$this->mSumaRecibos	= 0;
		$this->mSumaCobros	= 0;
		
		$resumen		= "";
		$fecha_inicial	= $this->mFecha;
		$fecha_final	= $this->mFecha;
		$cajero			= $this->mCajero;
		//==================================================================== TOTAL CORTE
		$resumen		.= "<h3>" . $xLn->getT("TR.Resumen de caja") . "</h3>";
		$sqlTi 			= $xSQL->getListadoResumenTesoreria($cajero, $fecha_inicial, $fecha_final);
		$xT				= new cTabla($sqlTi);
		$xT->setTdClassByType();
		$xT->setFootSum(array( 2 =>"operacion", 3 =>"recibido", 4 =>"cambio" ));
		$resumen		.=$xT->Show("TR.Resumen");
		$resumen		.="<input type='hidden' id='idsumacaja' value='" . $xT->getFieldsSum("recibido") . "' />";
		
		$resumen		.= "<h3>" . $xLn->getT("TR.Efectivo") . "</h3>";
		//==================================================================== EFECTIVO
		$sqlTE			= $xSQL->getListadoResumenOperaciones($fecha_inicial, $fecha_final, $cajero, TESORERIA_COBRO_EFECTIVO);
		$xTE			= new cTabla($sqlTE); $xTE->setTdClassByType();
		$xTE->setFootSum(array( 5 =>"total" ));
		$resumen		.=$xTE->Show("TR.Cobros por Efectivo");
		$this->mSumaRecibos	+= $xTE->getFieldsSum("total");
		
		$sqlTG			= $xSQL->getListadoResumenOperaciones($fecha_inicial, $fecha_final, $cajero, TESORERIA_PAGO_EFECTIVO);
		$xTG			= new cTabla($sqlTG); $xTG->setTdClassByType();
		$xTG->setFootSum(array( 5 =>"total" ));
		$resumen		.=$xTG->Show("TR.Gastos en Efectivo");
		
		$this->mSumaRecibos	+= $xTG->getFieldsSum("total");		
		//-------------------------------------------------------- retiros y gastos
		
		
		$resumen		.= "<h3>" . $xLn->getT("TR.Documentos") . "</h3>";
		$sqlArq			= "SELECT
				`tesoreria_caja_arqueos`.`fecha_de_arqueo`,
				`tesoreria_caja_arqueos`.`documento`,
				`numero_arqueado` AS `unidades`,
				`tesoreria_caja_arqueos`.`monto_total_arqueado` AS `monto`,
				`tesoreria_caja_arqueos`.`observaciones` 
			FROM
				`tesoreria_caja_arqueos` `tesoreria_caja_arqueos` 
			WHERE
				(`tesoreria_caja_arqueos`.`codigo_de_caja` ='" . $this->getKey() . "') ORDER BY `documento`, `monto_total_arqueado`";
		
		$xTArq				= new cTabla($sqlArq);
		$xTArq->setTdClassByType();
		$xTArq->setFootSum(array( 2 =>"monto_total_arqueado" ));
		$resumen			.= $xTArq->Show("TR.Arqueo");
		$this->mSumaCobros	+= $xTArq->getFieldsSum("monto");
		
		//==================================================================== CHEQUES Y DOCUMENTOS
		$sqlLC				= $xSQL->getListadoDeTesoreria($cajero, $fecha_inicial, $fecha_final, TESORERIA_COBRO_CHEQUE);
		$xT2				= new cTabla($sqlLC);
		$xT2->setTdClassByType();
		$xT2->setFootSum(array( 5 =>"operacion" ));
		$resumen			.= $xT2->Show("TR.Operaciones en Cheque"); 
		$this->mSumaCobros	+= $xT2->getFieldsSum("operacion");
	
		$sqlTD				= $xSQL->getListadoResumenOperaciones($fecha_inicial, $fecha_final, $cajero, TESORERIA_COBRO_CHEQUE);
		$xTD				= new cTabla($sqlTD); $xTD->setTdClassByType(); $xTD->setFootSum(array( 5 =>"total" ));
		$resumen			.= $xTD->Show("TR.Cobros por Cheque");
		$this->mSumaRecibos	+= $xTD->getFieldsSum("total");
		$resumen		.= "<h3>" . $xLn->getT("TR.Bancos") . "</h3>";
		//==================================================================== TRANFERENCIAS
		$sqlTO				= $xSQL->getListadoDeCajaEnBanco(BANCOS_OPERACION_DEPOSITO, "", $cajero, $fecha_inicial, $fecha_final);
		$xT					= new cTabla($sqlTO);
		$xT->setTdClassByType();
		$xT->setFootSum(array( 4 =>"monto" ));
		$resumen			.= $xT->Show("TR.Operaciones Bancarias");
		$this->mSumaCobros	+= $xT->getFieldsSum("monto");
		//-------------------------------------------
		
		$sqlT				= $xSQL->getListadoResumenOperaciones($fecha_inicial, $fecha_final, $cajero, TESORERIA_COBRO_TRANSFERENCIA);
		$xT					= new cTabla($sqlT);
		$xT->setTdClassByType();
		$xT->setFootSum(array( 5 =>"total" ));
		$resumen			.=  $xT->Show("TR.Cobros por Transferencia");
		$this->mSumaRecibos	+= $xT->getFieldsSum("total");
		
		//==================================================================== 
		$xTbl->initRow();
		$xTbl->addTH("TR.Suma de Recibos");
		$xTbl->addTH("TR.Suma de Cobranza");
		$xTbl->endRow();

		$xTbl->initRow();
		
		$xTbl->addTD( getFMoney($this->mSumaRecibos) );
		$xTbl->addTD( getFMoney($this->mSumaCobros) );
		$xTbl->endRow();		
		
		$resumen	.= $xTbl->get();
		$resumen		.= "<input type='hidden' id='idsumaoperaciones' value='" .  $this->mSumaRecibos . "' />";
		$resumen		.= "<input type='hidden' id='idsumacobros' value='" .  $this->mSumaCobros . "' />";
		$xNot			= new cHNotif();
		if($this->mSumaRecibos > $this->mSumaCobros){
			$resumen		.= $xNot->get($xLn->get("FALTANTE") . "" . getFMoney(($this->mSumaRecibos-$this->mSumaCobros)), "idavisodif", $xNot->ERROR);
		} else if($this->mSumaRecibos < $this->mSumaCobros){
			$resumen		.= $xNot->get($xLn->get("SOBRANTE") . "" . getFMoney(($this->mSumaCobros - $this->mSumaRecibos)), "idavisodif", $xNot->WARNING);
		} else {
			$resumen		.= $xNot->get($xLn->getT("TR.Caja Cuadrada"), "idavisodif", $xNot->SUCCESS);
		}
		$this->mArqueoInit	= true;
		return $resumen;
	}
	function getCajasAbiertas($fecha = false){
		$fecha	= ($fecha == false) ? fechasys() : $fecha;
		$count	= 0;
		if(MODULO_CAJA_ACTIVADO == true){
			$sql	= "SELECT COUNT(*) AS 'existentes' FROM `tesoreria_cajas` WHERE (`tesoreria_cajas`.estatus = " . TESORERIA_CAJA_ABIERTA . ") AND (`tesoreria_cajas`.`fecha_inicio` ='$fecha')";
			$count	= mifila($sql, "existentes");
		}
		return $count;
	}
	function getSumaDeRecibos(){ return $this->mSumaRecibos; }
	function getSumaDeCobros(){ return $this->mSumaCobros; }
	function setActualizaFondosCobrados($fondos = false){
		if($fondos === false){
			if($this->mArqueoInit == false){ $this->getResumenDeCaja(); }		//obtiene la suma de valores
			$fondos				= $this->getSumaDeRecibos() - $this->getSumaDeCobros();			
		}
		$sql	= "UPDATE `tesoreria_cajas` SET `total_cobrado` = '$fondos' WHERE `idtesoreria_cajas` = '" . $this->getKey() . "' ";
		$ql		= new MQL();
		$ql->setRawQuery($sql);
		return setNoMenorQueCero($fondos);
	}
	function setReactivar(){
		$sql	= "UPDATE `tesoreria_cajas` SET `estatus` = '" .TESORERIA_CAJA_ABIERTA . "' WHERE `idtesoreria_cajas` = '" . $this->getKey() . "' ";
		$ql		= new MQL();
		$rs		= $ql->setRawQuery($sql);
		return ($rs === false) ? false : true;		
	}
	function setPagoEfectivo($recibo, $MontoRecibido, $MontoOperado = 0, $notas = "", $fecha = false, $Moneda = AML_CLAVE_MONEDA_LOCAL, $MontoOriginal = 0){
		return $this->addOperacion($recibo, TESORERIA_PAGO_EFECTIVO, $MontoRecibido, $MontoOperado, 0,FALLBACK_CLAVE_DE_BANCO,
				"", 0, 0, $notas, $fecha, false, $Moneda, $MontoOriginal);
	}	
}
class cCuentaBancaria{
	private	$mCuenta		= false;
	private	$mMessages		= "";
	private $mClaveBanco	= false;
	private $mNombreBanco	= "";
	private	$mPaisDeOrigen	= "";
	private	$mDatosInArray	= array(); 
	private	$mInit			= false; 
	private $mCuentaContable= CUENTA_DE_CUADRE;
	private $mOBanco		= null;
	private	$mObj			= null;
	private	$mConsecutivo	= 0;
	public	$DEPOSITO		= "deposito";
	public $CHEQUE			= "cheque";
	
	function __construct($numero_de_cuenta){
		$this->mCuenta			= setNoMenorQueCero($numero_de_cuenta);
		$this->mPaisDeOrigen	= EACP_CLAVE_DE_PAIS;
		$this->mClaveBanco		= FALLBACK_CLAVE_DE_BANCO;
	}
	/**
	 * Agrega una Nueva Operacion a Bancos
	 * @param string $Operacion deposito, retiro, cheque
	 * @param string $DoctoDeSoporte Numero de Cheque Credito Recibo etc
	 * @param integer $recibo
	 * @param string $beneficiario
	 * @param float $monto
	 * @param integer $socio
	 * @param date $fecha
	 * @param integer $autorizo
	 * @param float $descuento
	 * @param integer $cuenta
	 */
	function addOperacion($Operacion, $DoctoDeSoporte, $recibo, $beneficiario,  $monto,	$socio = false,
							$fecha = false, $estado = "", $autorizo = false, $descuento = 0, $cuenta_de_origen = false ){
		$xOper	= new cOperacionBancaria();
		$estado	= ($estado == "") ? $xOper->AUTORIZADO : $estado;
		$id 	= $xOper->add($this->mCuenta, $Operacion, $DoctoDeSoporte, $recibo, $beneficiario, $monto, $socio, $fecha, $descuento, $cuenta_de_origen);
		return ($id >  0) ? true : false;
	}
	function setNuevoCheque($cheque, $cuenta, $recibo, $beneficiario,  $monto, 	$fecha = false, $autorizo = false, $descuento = 0, $socio = false, $cuenta_de_origen = false ){
		return $this->addOperacion(BANCOS_OPERACION_CHEQUE, $cheque, $recibo, $beneficiario, $monto, $socio, $fecha, "", $autorizo, $descuento);
	}
	function setNuevoRetiro($documento, $recibo, $beneficiario,  $monto, $fecha = false, $autorizo = false, $descuento = 0, $socio = false ){
		return $this->addOperacion(BANCOS_OPERACION_RETIRO, $documento, $recibo, $beneficiario, $monto, $socio, $fecha, "", $autorizo, $descuento);
	}
	function setNuevoDeposito($monto, $fecha = false, $descuento = 0, $socio = false, $recibo = false, $beneficiario = "", $documento = false){
				$recibo			= ($recibo == false) ? 0 : $recibo;
		$fecha			= ($fecha == false) ? fechasys() : $fecha;
		if($beneficiario == "" AND (setNoMenorQueCero( $recibo ) > 0 AND setNoMenorQueCero( $recibo ) != DEFAULT_RECIBO) ){
			$xRec			= new cReciboDeOperacion(false, false, $recibo); $xRec->init();
			$persona		= $xRec->getCodigoDeSocio();
			if($xRec->getOPersona() != null){ //inicializar persona
				$beneficiario	= $xRec->getOPersona()->getNombreCompleto(OUT_TXT);
			}
			if($socio == false){ $socio = $persona; }
			if($documento == false){ $documento = $xRec->getCodigoDeDocumento(); }
		}
		$beneficiario	= ($beneficiario == "") ?  "DEPOSITO BANCARIO DEL $fecha" : $beneficiario;
		$socio			= ($socio == false) ? DEFAULT_SOCIO : $socio;
		$documento		= ($documento == false) ? DEFAULT_CREDITO : $documento;
		$autorizo 		= getUsuarioActual();
		
		return $this->addOperacion(BANCOS_OPERACION_DEPOSITO, $documento, $recibo, $beneficiario, $monto, $socio, $fecha, "", $autorizo, $descuento);
	}
	
	function getMontoCheque($cheque){
		$cuenta		= $this->mCuenta;
		$sql		= "SELECT idcontrol, tipo_operacion, numero_de_documento, cuenta_bancaria, recibo_relacionado, fecha_expedicion, 
							beneficiario, monto_descontado, monto_real, estatus, idusuario, usuario_autorizo, eacp, sucursal, 
							numero_de_socio 
							    FROM bancos_operaciones
							WHERE tipo_operacion = 'cheque'
							AND numero_de_documento='$cheque'
							AND cuenta_bancaria=$cuenta LIMIT 0,1 ";
		
		return mifila($sql, "monto_real");
	}
	function getDatosInArray(){
		$sql	= " SELECT
					`bancos_cuentas`.*,
					`bancos_entidades`.*
				FROM
					`bancos_cuentas` `bancos_cuentas`
						INNER JOIN `bancos_entidades` `bancos_entidades`
						ON `bancos_cuentas`.`entidad_bancaria` = `bancos_entidades`.
						`idbancos_entidades`
				WHERE idbancos_cuentas = " . $this->mCuenta . " LIMIT 0,1";
		return obten_filas($sql);
	}
	function getBuscarCuentaContableXCheque($cheque, $campo = "codigo_contable"){
		$xT			= new cTipos();
		$cheque 	= $xT->cInt($cheque);
		$sqlIC 		= "SELECT * FROM " . TBANCOS_OPERACIONES . " WHERE tipo_operacion='cheque'
				AND numero_de_documento LIKE '%$cheque'
				ORDER BY
				fecha_expedicion DESC
				LIMIT 0,1 ";
	
		$bcheque = mifila($sqlIC, "cuenta_bancaria");
	
		$sqlBC = "SELECT
				`bancos_cuentas`.*,
				`bancos_entidades`.`nombre_de_la_entidad`
			FROM
				`bancos_cuentas` `bancos_cuentas`
					INNER JOIN `bancos_entidades` `bancos_entidades`
					ON `bancos_cuentas`.`entidad_bancaria` = `bancos_entidades`.
					`idbancos_entidades`
			WHERE
				idbancos_cuentas=$bcheque ";
	
		$cuenta = mifila($sqlBC, $campo);
		if ( (!isset($cuenta)) or ($cuenta == "0") ){
			if($campo!="codigo_contable"){
				$cuenta	= MSG_NO_PARAM_VALID;
			} else {
				$cuenta = CUENTA_DE_CUADRE;
			}
		}
		return $cuenta;
	}
	function setUltimoCheque($cheque = 0, $CuentaBancaria = false){
		$this->set($CuentaBancaria);
		$CuentaBancaria		= $this->mCuenta;
		$documento 			= 1;
		$xT					= new cTipos();
		$xQL				= new MQL();
		$cheque 			= setNoMenorQueCero($cheque);
				
		if($cheque <= 0) {
			//Obtiene el Cheque de un Conteo SQL
			$sql 			= "SELECT numero_de_documento FROM bancos_operaciones WHERE cuenta_bancaria = $CuentaBancaria ORDER BY idcontrol ASC, fecha_expedicion ASC LIMIT 0,1";
			$D				= $xQL->getDataRow($sql);
			if(isset($D["numero_de_documento"])){
				$documento	= $D["numero_de_documento"];
			}
			$documento 		= setNoMenorQueCero($documento);
			$documento 		= $documento + 1;
		} else {
			$documento 		= $cheque;
		}
		
		$sqlD = "UPDATE bancos_cuentas SET consecutivo_actual = $documento WHERE idbancos_cuentas = $CuentaBancaria";
		$xQL->setRawQuery($sqlD);
	}
	function init($arr = false){
		
		if(is_array($arr)){
			$D					= $arr;
		} else {
			$ql					= new MQL();
			$D					= $ql->getDataRow("SELECT * FROM `bancos_cuentas` WHERE `idbancos_cuentas` = " . $this->mCuenta . " LIMIT 0,1");
		}
		if(isset($D["idbancos_cuentas"])){
			$xB						= new cBancos_cuentas();
			$xB->setData($D);
			$this->mClaveBanco		= $xB->entidad_bancaria()->v();
			//Clave de Pais del banco
			$xBE					= new cBancos_entidades();
			$xBE->setData( $xBE->query()->initByID($this->mClaveBanco) );
			$this->mNombreBanco		= $xBE->nombre_de_la_entidad()->v();
			$this->mPaisDeOrigen	= $xBE->pais_de_origen()->v(OUT_TXT);
			$this->mCuentaContable	= $xB->codigo_contable()->v(OUT_TXT);
			$this->mConsecutivo		= setNoMenorQueCero($xB->consecutivo_actual()->v());
			$this->mObj				= $xB;
			$this->mOBanco			= $xBE;
			$this->mCuenta			= $xB->idbancos_cuentas()->v();
			$this->mInit			= true;
			$xB	= null; $xBE = null;
		}
		return $this->mInit;
	}
	function getClaveDeBanco(){ return $this->mClaveBanco; }
	function getNumeroDeCuenta(){ return $this->mCuenta; }
	function getPaisDeOrigen(){ return $this->mPaisDeOrigen; }
	function getNombreDelBanco(){ return $this->mNombreBanco; }
	
	function set($cuenta = false ){		if( setNoMenorQueCero($cuenta) > 0 ){ $this->mCuenta = $cuenta; $this->init(); }	}
	function getUltimoCheque($CuentaBancaria = false){ $this->set($CuentaBancaria); return $this->mConsecutivo+1; 	}
	function getDatosDeChequeInArray(){	}
	function getVerificarCheque($Cheque, $MontoComparado){
		$msg		= "";
		$Cuenta		= $this->mCuenta;
		$sucess		= true;
		$sql		= "SELECT
						`bancos_operaciones`.`cuenta_bancaria`,
						`bancos_operaciones`.`tipo_operacion`,
						`bancos_operaciones`.`numero_de_documento`,
						`bancos_operaciones`.`monto_real` 
					FROM
						`bancos_operaciones` `bancos_operaciones` 
					WHERE
						(`bancos_operaciones`.`cuenta_bancaria` ='$Cuenta') AND
						(`bancos_operaciones`.`tipo_operacion` ='cheque') AND
						(`bancos_operaciones`.`numero_de_documento` ='$Cheque') ";
		$DC			= obten_filas($sql);
		if ( !isset($DC["monto_real"]) ){
			$msg		.= "ERROR\tEl Cheque $Cheque No Existe en la cuenta $Cuenta\r\n";
			$sucess		= false;
		} else {
			$MontoCheque	= $DC["monto_real"];
			if( floatval($MontoCheque) != floatval($MontoComparado) ){
				$sucess		= false;
				$msg		.= "ERROR\tEl Monto $MontoCheque del Cheque($Cheque) no debe ser diferente al Monto Comparado($MontoComparado)(\r\n";
			}
			
		}
		$this->mMessages	.= $msg;
		return $sucess;
	}
	function getCuentaContable(){ return $this->mCuentaContable; }
	function getMessages($put = OUT_HTML){ $xH = new cHObject();	return $xH->Out($this->mMessages, $put); }
}
class cOperacionBancaria {
	private $mCodigo			= null;
	private $mCodigoDeRecibo	= null;
	private $mClaveDeBanco		= null;
	private $mNumeroBancario	= null;
	private $mTotalMonto		= 0;
	private $mFechaDePago		= false;
	private $mObj				= null;
	private $mOBanco			= null;
	
	private $mNumeroDeCheque	= 0;
	private $mTipoDeOperacion	= "deposito";
	private $mMonto				= 0;
	public $CHEQUE				= "cheque";
	public $DEPOSITO			= "deposito";
	public $COMISION			= "comision";
	public $RETIRO				= "retiro";
	public $TRASPASO			= "traspaso";
	
	public $AUTORIZADO			= "autorizado";
	
	function __construct($clave = false){
		$this->mCodigo		= $clave;
	}
	//function setCodigoDeRecibo($recibo){  $this->mCodigoDeRecibo = $recibo; return $this->mCodigoDeRecibo; }
	function getCodigoDeRecibo(){ return $this->mCodigoDeRecibo; }
	function getCuentaBancaria(){ return $this->mNumeroBancario; }
	function getNumeroDeCheque(){ return $this->mNumeroDeCheque; }
	function getMonto(){ return $this->mMonto; }
	function initByRecibo($recibo = false){
		$recibo	= ($recibo == false) ? $this->getCodigoDeRecibo() : $recibo;
		$ql		= new MQL();
		$sql	= "SELECT * FROM `bancos_operaciones` WHERE (`bancos_operaciones`.`recibo_relacionado` =$recibo) ORDER BY `bancos_operaciones`.`monto_real` DESC LIMIT 0,1";
		$row	= $ql->getDataRow($sql);
		if( isset($row["idcontrol"]) ){
			$this->mCodigo	= $row["idcontrol"];
			$this->init($row);
		}
		//$this->setCodigoDeRecibo($recibo);
	}
	function init($data = false){
		if(!is_array($data)){
			$ql		= new MQL();
			$sql	= "SELECT * FROM `bancos_operaciones` WHERE (`idcontrol` =" . $this->mCodigo . ") LIMIT 0,1";
			$data	= $ql->getDataRow($sql);
		}
		$this->mObj		= new cBancos_operaciones();
		$this->mObj->setData($data);
		$this->mNumeroBancario	= $this->mObj->cuenta_bancaria()->v();
		$this->mTipoDeOperacion	= $this->mObj->tipo_operacion()->v(OUT_TXT);
		$this->mMonto			= setNoMenorQueCero($this->mObj->monto_real()->v(OUT_TXT) );
		switch ($this->mTipoDeOperacion){
			case $this->CHEQUE:
				$this->mNumeroDeCheque	= setNoMenorQueCero($this->mObj->numero_de_documento()->v(OUT_TXT));
			break;
			
		}
	}
	function add($cuenta, $Operacion, $DoctoDeSoporte, $recibo, $beneficiario,  $monto,	$persona = false,
							$fecha = false, $autorizo = false, $descuento = 0, $cuenta_de_origen = false, $tipo_de_exhibicion = "", $moneda = AML_CLAVE_MONEDA_LOCAL){
		$xF					= new cFecha();
		$cuenta				= setNoMenorQueCero($cuenta);
		$recibo				= setNoMenorQueCero($recibo);
		$cuenta_de_origen	= setNoMenorQueCero($cuenta_de_origen);
		$monto				= setNoMenorQueCero($monto);
		
		$DoctoDeSoporte		= setNoMenorQueCero($DoctoDeSoporte);
		$persona			= (setNoMenorQueCero($persona) == 0) ? DEFAULT_SOCIO : setNoMenorQueCero($persona);
		$autorizo			= setNoMenorQueCero($autorizo);
		$autorizo			= ($autorizo <= 0) ? getUsuarioActual() : $autorizo;
		$id					= 0;
		$documento			= DEFAULT_CREDITO;
		//AND ($persona <= DEFAULT_SOCIO)
		if($recibo > 0 ){
			$xRec			= new cReciboDeOperacion(false, false, $recibo);
			if($xRec->init() == true){
				$persona			= ($persona <= DEFAULT_SOCIO) ? $xRec->getCodigoDeSocio() : $persona;
				$tipo_de_exhibicion	= ($tipo_de_exhibicion == "") ? $xRec->getTipoDePago() : $tipo_de_exhibicion;
				$fecha				= ($fecha == false) ? $xRec->getFechaDeRecibo() : $fecha;
				$documento			= ($documento <= DEFAULT_CREDITO) ? $xRec->getCodigoDeDocumento() : $documento;
				//$DoctoDeSoporte		= ($DoctoDeSoporte <=DEFAULT_CREDITO) ? $xRec->getCodigoDeDocumento() : $DoctoDeSoporte;
				if(trim($beneficiario) == ""){
					if($xRec->getOPersona() != null){
						$beneficiario	= $xRec->getOPersona()->getNombreCompleto(OUT_TXT);
					}
				}
			}
		}
		$fecha				= $xF->getFechaISO($fecha);
		$tipo_de_exhibicion	= ($tipo_de_exhibicion == "") ? "transferencia" : $tipo_de_exhibicion;
		if($cuenta > 0 AND $monto > 0){
			$xOp	= new cBancos_operaciones();
			$xOp->idcontrol( $xOp->query()->getLastID() );
			$xOp->beneficiario($beneficiario);
			$xOp->clave_de_conciliacion(0);
			$xOp->clave_de_moneda($moneda);
			$xOp->cuenta_bancaria($cuenta);
			$xOp->cuenta_de_origen($cuenta_de_origen);
			$xOp->eacp(EACP_CLAVE);
			$xOp->estatus($this->AUTORIZADO);
			$xOp->fecha_expedicion($fecha);
			$xOp->idusuario( getUsuarioActual() );
			$xOp->monto_descontado($descuento);
			$xOp->monto_real($monto);
			$xOp->numero_de_documento($DoctoDeSoporte);
			$xOp->numero_de_socio($persona);
			$xOp->recibo_relacionado($recibo);
			$xOp->sucursal(getSucursal());
			$xOp->tipo_de_exhibicion($tipo_de_exhibicion);
			$xOp->tipo_operacion($Operacion);
			$xOp->usuario_autorizo($autorizo);
			$xOp->documento_de_origen($documento);
			$id	= $xOp->query()->insert()->save();
		}
		return $id;
	}
} 
class cOperacionDeCaja {
	private $mCodigo			= null;
	private $mCodigoDeRecibo	= null;
	private $mClaveDeBanco		= null;
	private $mNumeroBancario	= null;
	private $mTotalMonto		= 0;
	private $mFechaDePago		= false;
	private $mDataArray			= array();
	private $mObj				= null; 
	private $mInit				= false;
	private $mMontoRecibido		= 0;
	private $mMontoOperado		= 0;
	private $mMontoDevuelto		= 0;
	private $mTipoPago			= "";
	private $mTabla				= "tesoreria_cajas_movimientos";
	
	
	function __construct($clave = false){
		$this->mCodigo		= setNoMenorQueCero($clave);
		$this->mTipoPago	= SYS_NINGUNO;
	}
	function setCodigoDeRecibo($recibo){  $this->mCodigoDeRecibo = $recibo; return $this->mCodigoDeRecibo; }
	function getCodigoDeRecibo(){ return $this->mCodigoDeRecibo; }
	function initByRecibo($recibo = false){
		
		$recibo	= ($recibo == false) ? $this->getCodigoDeRecibo() : $recibo;
		$recibo	= setNoMenorQueCero($recibo);
		$idx	= $this->mTabla . "-by-rec-$recibo";
		$xCache	= new cCache();
		$data	= $xCache->get($idx);
		$this->setCodigoDeRecibo($recibo);
		
		if(!is_array($data)){
			$data	= obten_filas("SELECT * FROM `tesoreria_cajas_movimientos` WHERE `recibo`=$recibo LIMIT 0,1");
			if(isset($data["idtesoreria_cajas_movimientos"])){
				$xCache->set($idx, $data);
			}
		}
		return $this->init($data);
	}
	function init($arrDatos	= false){
		$this->mDataArray	= $arrDatos;
		$xCache				= new cCache();
		$idx				= $this->mTabla . "-" . $this->mCodigo;
		if(!is_array($this->mDataArray)){
			$this->mDataArray	= $xCache->get($idx);
			if(!is_array($this->mDataArray)){
				$this->mDataArray 	= obten_filas("SELECT * FROM `tesoreria_cajas_movimientos` WHERE `idtesoreria_cajas_movimientos` =" . $this->mCodigo . " LIMIT 0,1");
			}
		}
		$this->mObj	= new cTesoreria_cajas_movimientos();
		if(isset($this->mDataArray["idtesoreria_cajas_movimientos"])){
			$this->mObj->setData($this->mDataArray);
			$this->mCodigo 			= $this->mObj->idtesoreria_cajas_movimientos()->v();
			$this->mClaveDeBanco	= $this->mObj->cuenta_bancaria()->v();
			$this->mCodigoDeRecibo	= $this->mObj->recibo()->v();
			$this->mMontoDevuelto	= $this->mObj->monto_en_cambio()->v();
			$this->mMontoOperado	= $this->mObj->monto_del_movimiento()->v();
			$this->mMontoRecibido	= $this->mObj->monto_recibido()->v();
			$this->mTipoPago		= $this->mObj->tipo_de_exposicion()->v();
			$this->mCodigo			= $this->mObj->idtesoreria_cajas_movimientos()->v();
			$this->mInit			= true;
			
		}
		return $this->mInit;
	}
	function getMontoOperado(){ return $this->mMontoOperado; }
	function getMontoDevuelto(){ return $this->mMontoDevuelto; }
	function getMontoRecibido(){ return $this->mMontoRecibido; }
	function getTipoDeExpocision(){ return $this->mTipoPago; }
	function getBanco(){ return $this->getObj()->banco()->v(); }
	function getClave(){ return $this->mCodigo; }
	function del(){
		
	}
	function getObj(){
		if($this->mObj == null){ $this->init(); }
		return $this->mObj;
	}
	function __destruct(){	$this->mObj	= null;	}
}


class cCajaArqueos{
	private $mClaveDecaja	= null;
	private $mFecha			= false;
	private $mMessages		= "";
	function __construct($caja = ""){
		$this->mClaveDecaja	= $caja;
	}	
	function addValorArqueado($valor_arqueado, $numero_arqueado, $documento, $notas = "", $fecha = false, $hora = false){
		//eliminar valor anterior
		$fecha	= ($fecha == false) ? fechasys() : $fecha;
		$hora	= ($hora == false) ? time() : $hora;
		$monto	= $valor_arqueado * $numero_arqueado;
		$xArq	= new cTesoreria_caja_arqueos();
		if($monto >0){
			$xArq->codigo_de_caja($this->mClaveDecaja);
			$xArq->documento($documento);
			$xArq->eacp(EACP_CLAVE);
			$xArq->fecha_de_arqueo($fecha);
			$xArq->hora_de_arqueo( $hora );
			$xArq->idusuario(getUsuarioActual());
			$xArq->monto_total_arqueado($monto);
			$xArq->observaciones($notas);
			$xArq->sucursal(getSucursal());
			$xArq->valor_arqueado($valor_arqueado);
			$xArq->numero_arqueado($numero_arqueado);
			$id		= $xArq->query()->getLastID();
			$xArq->codigo_de_arqueo( $id );
			$cmd	= $xArq->query()->insert();
			if($cmd->save() == false){
				$this->mMessages	.= "ERROR\tAl Agregar Monto $valor_arqueado y Numero $numero_arqueado\r\n";
			} else {
				$this->mMessages	.= "OK\tAgregar Monto $valor_arqueado y Numero $numero_arqueado\r\n";
			}
		} else {
			$this->mMessages	.= "WARN\tSe omite el registro por no existir monto\r\n";
		}
		//if(MODO_DEBUG == true){ setLog($cmd->getMessages()); }
		return $id;
	}
	function getValoresArqueados($fecha = false){
		$sql	= "SELECT SUM(`monto_total_arqueado`) AS 'total' FROM `tesoreria_caja_arqueos` WHERE `codigo_de_caja`='" . $this->mClaveDecaja . "'";
		$valor	= mifila($sql, "total");
		return $valor;
	}
	function getMessages($put = OUT_HTML){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function setEliminarArqueo(){
		$sql 	= "DELETE FROM `tesoreria_caja_arqueos` WHERE `codigo_de_caja`='" . $this->mClaveDecaja . "'";
		$xQL	= new MQL(); $xQL->setRawQuery($sql);
	}
}
class cTesoreriaEstadisticas {
	function __construct(){
		
	}
	function getNumeroCajasAbiertas(){
		$xli		= new cSQLListas();
		$sqlSc		= $xli->getListadoDeCajasConUsuario(TESORERIA_CAJA_ABIERTA);
		$xQL		= new MQL();
		$rs			= $xQL->getDataRecord($sqlSc);
		$rs			= null;
		return $xQL->getNumberOfRows();		
	}
}

class cTesoreriaMonedas {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTable		= "tesoreria_monedas";
	private $mValor		= 0;
	function __construct($clave = ""){ 
		$this->mClave	= $clave; 
		if($clave !== ""){
			$this->setIDCache($this->mClave);
		}
	}
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= "tesoreria_monedas" . "-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cTesoreria_monedas();
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . $xT->get() . "` WHERE `" . $xT->getKey() . "`='". $this->mClave . "' LIMIT 0,1");
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			$this->mObj		= $xT; //Cambiar
			$this->mClave	= $data[$xT->getKey()];
			$this->mNombre	= $xT->nombre_de_la_moneda()->v();
			$this->mValor	= $xT->quivalencia_en_moneda_local()->v();
			$this->setIDCache($this->mClave);
			$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			$this->mInit	= true;
			$xT 			= null;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){ $this->mObj = null; $this->mMessages	= "";	}
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}
	function getValor(){ return $this->mValor; }
	function setCuandoSeActualiza(){
		$this->setCleanCache();
	}
	function add(){}

}

?>