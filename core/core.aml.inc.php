<?php
/**
 * Core Common File.- Contiene Clases de uso general
 * @author Balam Gonzalez Luis Humberto
 * @version 1.4.01
 * @package common
 */
//=====================================================================================================
	include_once("go.login.inc.php");
	include_once("core.error.inc.php");
	include_once("core.html.inc.php");
	include_once("core.db.inc.php");
	include_once("core.db.dic.php");
	include_once("core.init.inc.php");
	include_once("core.deprecated.inc.php");
	include_once("entidad.datos.php");
	include_once("core.config.inc.php");
	include_once("core.utils.inc.php");
	include_once("core.fechas.inc.php");
	include_once("core.operaciones.inc.php");
	include_once("core.creditos.inc.php");
	include_once("core.security.inc.php");
	include_once("core.taxs.inc.php");
	include_once("core.common.inc.php");
	@include_once("../libs/Encoding.php");
	
	
	class cAML {
		protected $mMessages				= "";
		protected $mForceAvisos				= false;
		protected $mForceRegRiesgo			= false;
		protected $mTipoDeDocto				= false;
		
		function __construct(){
			$this->mTipoDeDocto				= iDE_RECIBO;
		}
		function setForceRegistroRiesgo($force = true){ $this->mForceRegRiesgo = $force; }
		
		function setForceAlerts() { $this->mForceAvisos = true; }
		function analyzePerson(){
			
		}
		function analyzeAccount(){
			
		}
		function analizeOperaciones(){
			//checar si la operacion
		}
		function sendAlerts($PersonaDeOrigen, $PersonaDeDestino, $TipoDeAlerta, $mensaje = "", $documento = false, 
				$fecha = false, $hora = false, $valorDeterminado = 0, $tipo_de_docto = false, $tercero_relacionado = DEFAULT_SOCIO){
			$hora				= ( setNoMenorQueCero($hora) <= 0) ? date("Hi") : $hora;
			$fecha				= ($fecha == false) ? fechasys() : $fecha;
			$documento			= ($documento == false) ? DEFAULT_RECIBO : $documento;
			$tipo_de_docto		= ($tipo_de_docto == false) ? $this->mTipoDeDocto : $tipo_de_docto;
			$this->mTipoDeDocto	= $tipo_de_docto;
			$resultado			= true;
			$xRsk				= new cAml_risk_catalog();
			$xSuc				= new cSucursal(); $xSuc->init();
			$idnumerico			= $xSuc->getClaveNumerica();
			
			$xRsk->setData( $xRsk->query()->initByID($TipoDeAlerta) );
			$riesgo		= $xRsk->valor_ponderado()->v();
			$nombreRies	= $xRsk->descripcion()->v();
			$claseRies	= $xRsk->tipo_de_riesgo()->v();
			
			$valorDeterminado	= ($valorDeterminado == 0) ? $xRsk->valor_ponderado()->v() : $valorDeterminado;
			
			$xF			= new cFecha();
			$fecha		= $xF->getInt($fecha);
			$xAl		= new cAml_alerts();

			$xAl->estado_en_sistema(SYS_UNO);
			$xAl->fecha_de_checking(SYS_CERO);
			$xAl->fecha_de_origen($fecha);
			$xAl->fecha_de_registro($xF->getInt(fechasys()) );
			$xAl->hora_de_proceso($hora);
			$xAl->documento_relacionado($documento);
			$xAl->medio_de_envio(AML_ALERT_MAIL);
			$xAl->mensaje($mensaje);
			$xAl->riesgo_calificado($valorDeterminado);
			$xAl->tipo_de_aviso($TipoDeAlerta);
			$xAl->sucursal($idnumerico);				//Verificar
			$xAl->entidad(EACP_CLAVE_CASFIN);			//Actualizar
			$xAl->usuario(getUsuarioActual());
			$xAl->persona_de_destino($PersonaDeDestino);
			$xAl->persona_de_origen($PersonaDeOrigen);
			$xAl->tipo_de_documento($tipo_de_docto );
			$xAl->tercero_relacionado($tercero_relacionado);
			$id_de_alerta	= $xAl->query()->getLastID() ;
			$xAl->clave_de_control( $id_de_alerta );
						
			$ql		= $xAl->query()->insert();
			$res	= $ql->save();
			if( setNoMenorQueCero($res) <= 0 ){
				$this->mMessages	.= "ERROR\tAl guardar registro ($idnumerico)\r\n";
				if(MODO_DEBUG == true){
					$this->mMessages	.= $ql->getMessages(OUT_TXT);
				}
				$resultado			= false;
			}
			//$xCatRiesgo				= new cAml_risk_catalog();
			//$xCatRiesgo->setData( $xCatRiesgo->query()->initByID($TipoDeAlerta) );
			//
			$xCatRiesgos	= new cAml_risk_catalog(); $xCatRiesgos->setData($xCatRiesgos->query()->initByID($TipoDeAlerta) );
			$nombre_riesgo	= $xCatRiesgos->descripcion()->v();
			//Enviar SMS
			
			$xMail			= new cNotificaciones();
			//obtener los datos del usuario
			$xVUser			= new cVistaUsuarios();
			$xD				= $xVUser->query()->initByID($PersonaDeDestino);
			$xVUser->setData($xD);
			$userP			= $xVUser->codigo_de_persona()->v();
			$mailD			= false;
			$telD			= false;
						
			$xSoc			= new cSocio($userP);
			
			if( $xSoc->existe($userP) == true ){
				$xSoc->init();
				$mailD			= $xSoc->getCorreoElectronico();
				$telD			= $xSoc->getTelefonoPrincipal();
			} else {
				$this->mMessages	.= "ERROR\tNo existe la persona de envio $userP\r\n";
			}
			
			//Array de valores
			$arrV			= array();
			$fechacorta		= $xF->getFechaCorta( $xF->getFechaByInt( $fecha) );
			$arrV["variable_documento_codigo"]			= $documento;
			$arrV["variable_docto_fecha"]				= $fechacorta;
			$arrV["variable_docto_hora"]				= $hora;
			$arrV["variable_nivel_de_riesgo"]			= $riesgo;
			$arrV["variable_tipo_de_riesgo"]			= $nombreRies;
			$arrV["variable_clasificacion_de_riesgo"]	= $claseRies;
			$arrV["variable_codigo_de_alerta"]			= $id_de_alerta;
			$arrV["variable_mensaje_de_alerta"]			= $mensaje;
			$arrV["variable_url_del_sistema"]			= SAFE_HOST_URL;
			//Enviar Mail
			$xFmt			= new cFormato(800);
			$xFmt->setUsuario($PersonaDeDestino);
			$xFmt->setPersona($PersonaDeOrigen);
			$xFmt->setProcesarVars($arrV);
			
			$txtMail		= $xFmt->get();
			if($valorDeterminado > 51 OR $this->mForceAvisos == true){
				$xMail->setTitulo(AML_TITULO_DE_ALERTA);
				$this->mMessages	.= $xMail->send($txtMail, $mailD, $telD, $PersonaDeDestino, "$fechacorta-$claseRies-$mensaje", "aml.$mailD");
				if($this->mForceRegRiesgo == true){
					//Agregar el riesgo
					$xAlert		= new cAMLAlertas($id_de_alerta);
					$regFecha	= $xF->getFechaByInt( $fecha);
					$xAlert->setConfirmaAlerta($mensaje, $regFecha);
					$this->mMessages	.= $xAlert->getMessages();
					//$xPAML		= new cAMLPersonas($PersonaDeOrigen);
					//$xPAML->setAgregarPerfilDeRiesgo($TipoDeAlerta, $fecha, $valorDeterminado, $documento, $tipo_de_docto, $PersonaDeDestino, $hora, $tercero_relacionado, $mensaje );
					//$this->mMessages	.= $xPAML->getMessages();
				}
			}
			return $resultado;
		}
		function getAlerts($OficialDeDestino, $id){
			
		}
		function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }	
		function setReportarUsuario($usuarioreportado, $motivo, $mensaje, $documento = false, $fecha = false, $persona = false, $tipo_de_documento = iDE_RECIBO){
			$msg				= "";
			$xF					= new cFecha(0, $fecha);
			$documento			= setNoMenorQueCero($documento);
			$persona			= setNoMenorQueCero($persona);
			$usuarioreportado	= setNoMenorQueCero($usuarioreportado);
			$cDUsr1				= new cVistaUsuarios(); $cDUsr1->setData( $cDUsr1->query()->initByID($usuarioreportado) );
			$PersonaDeDestino	= $cDUsr1->codigo_de_persona()->v();
			$tipo_de_docto		= setNoMenorQueCero($tipo_de_documento);

			
			if($documento > DEFAULT_CREDITO){
				//$tipo_de_docto	= iDE_CREDITO;
			} else {
				if($persona > DEFAULT_SOCIO){
					$documento		= $persona;
					$tipo_de_docto	= iDE_SOCIO;
				}
			}
			//verificar si existe la persona
			$xSoc				= new cSocio($PersonaDeDestino);
			if($xSoc->existe($PersonaDeDestino) == false){
				$msg			.= "ERROR\tLa persona $PersonaDeDestino NO EXISTE, no hay datos para reportar\r\n";
			} else {
				$this->setForceAlerts();
				$res			= $this->sendAlerts($PersonaDeDestino, AML_OFICIAL_DE_CUMPLIMIENTO, $motivo, $mensaje, $documento, $fecha, false, 0, $tipo_de_docto , $persona);
				$msg			.= ($res == false) ? "ERROR\tError al agregar el registro\r\n" : "OK\tRegistro correcto\r\n";
			}
			$this->mMessages	.= $msg;
			return  $msg;
		}
		


		function isTransaccionVigilada($tipo){ $arrMatrix	= array(SYS_NINGUNO => true, TESORERIA_COBRO_NINGUNO => true, TESORERIA_PAGO_NINGUNO => true);	return ( !isset( $arrMatrix[ strtolower($tipo)]) ) ? true : false; }
	
	}
	
	class cAMLPersonas {
		private $mRiesgoDevuelto	= 0;
		private $mDatosHeredados	= array();
		private $mCodigo			= false;
		private $mClaveDePersona	= false;
		private $mMessages			= "";
		private $mOSocio			= null;
		private $mForceAvisos		= false;
		private $mAsInit			= false;
		private $mURLConsulta		= "";
		private $mEsVigilado		= null;
		function __construct($persona){ $this->mClaveDePersona		= $persona;	}
		function setForceAlerts() { $this->mForceAvisos = true; }
		function init($clave_de_persona = false, $DatosHeredados = false){
			$clave_de_persona		= ($clave_de_persona == false) ? $this->mClaveDePersona : $clave_de_persona;
			$this->mClaveDePersona	= $clave_de_persona;
			$xSoc					= new cSocio($clave_de_persona);
			$xSoc->init($DatosHeredados);
			$this->setDatosHeredados($xSoc->getDatosInArray());
			$this->mOSocio			= $xSoc;
			$this->mAsInit			= true;
			return $this->mAsInit;
		}
		function getCodigoDePersona(){ return $this->mClaveDePersona; }
		function buscarSimilares(){
			if($this->mAsInit == false){ $this->init(); }
			//consultar en la lista de aml
		}
		function getSimilaresPorNombre($nombre = "", $apellido1 = "", $apellido2 = "", $soloBusqueda = false){
			if($nombre == ""){
				$xSoc		= $this->getOPersona();// new cSocio($this->mClaveDePersona);
				$nombre		= $xSoc->getNombre();
				$apellido1	= $xSoc->getApellidoPaterno();
				$apellido2	= $xSoc->getApellidoMaterno();
			}
			$nombre			= strtoupper($nombre);
			$apellido1	 	= strtoupper($apellido1);
			$PersonaDeOrigen= $this->getCodigoDePersona();
			$xPO			= $this->getOPersona();
			$NombreDeOrigen	= $xPO->getNombreCompleto();
			
			$snom			= substr($nombre, 0,3);
			$sapp1			= substr($apellido1, 0,3);
			
			$apellido2	 	= strtoupper($apellido2);
			$sapp2			= substr($apellido2, 0,3);
			$sql			= "SELECT * FROM socios_general WHERE (`socios_general`.`nombrecompleto` LIKE '%$snom%') ";
			if($apellido1 != ""){
				$sql		= "  AND (`socios_general`.`apellidopaterno` LIKE '%$sapp1%') ";
			}
			if($apellido2 != ""){
				$sql		.= " AND (`socios_general`.`apellidomaterno` LIKE '%$sapp2%') ";
			}
			$this->mMessages	.= "WARN\tBuscando similitudes para $nombre $apellido1 $apellido2 \r\n";
			
			$xPer			= new cSocios_general();
			$xMql			= new MQL();
			$data			= $xMql->getDataRecord($sql);
			foreach ($data as $rows){
				$xPer->setData($rows);
				
				$PersonaCoincide	= $xPer->codigo()->v();
				$PersonaDeDestino	= AML_OFICIAL_DE_CUMPLIMIENTO;
				$TipoDeAlerta		= 901002;
				$minimo				= 90;
				
				$parecido		= 0;
				$mNombre		= strtoupper($xPer->nombrecompleto()->v());
				$mApp1			= strtoupper($xPer->apellidopaterno()->v());
				$mApp2			= strtoupper($xPer->apellidomaterno()->v());
				$divisor		= 1;
				$parecido_a2	= 0;
				$parecido_a1	= 0;
				
				similar_text( $mNombre, $nombre, $parecido_n);
				similar_text( $mApp1, $apellido1, $parecido_a1);
				if($apellido1 != ""){ similar_text( $mApp1, $apellido1, $parecido_a1); $divisor=2; $minimo = 80; }
				if($apellido2 != ""){ similar_text( $mApp2, $apellido2, $parecido_a2);	 $divisor=3; $minimo = 70;}
				$parecido		= ($parecido_n + $parecido_a1 + $parecido_a2) / $divisor;
				if($parecido > 51){
					if($parecido >= $minimo){
						$xAML		= new cAML();
						$xSocP		= new cSocio($PersonaCoincide);
						$xSocP->init();
						$origenN	= $xSocP->getNombreCompleto();
						$mensaje	= "$NombreDeOrigen .- Concidencia($mNombre|$mApp1|$mApp2) del $parecido con la persona $origenN ";
						if($xSocP->getEsPersonaSDN() == true){
							//aviso inminente
							$xAML->setForceRegistroRiesgo();
							$xAML->setForceAlerts();
							$xAML->sendAlerts($PersonaDeOrigen, $PersonaDeDestino, 901002, $mensaje);
						} elseif( $xSocP->getEsPersonaPoliticamenteExpuesta() == true ){
							//enviar a oficial de cumplimiento
							$xAML->setForceAlerts();
							$xAML->sendAlerts($PersonaDeOrigen, $PersonaDeDestino, 901010, $mensaje); //registro pep
						} else {
							//enviar a oficial de cumplimiento
							$xAML->setForceAlerts();
							$xAML->sendAlerts($PersonaDeOrigen, $PersonaDeDestino, 901010, $mensaje); //registro pep
						}
						$this->mMessages	.= $xSocP->getMessages();
						$this->mMessages	.= $xAML->getMessages();
					} else {
						$this->mMessages	.= "WARN\tEl parecido con $mNombre $mApp1 $mApp2 es $parecido %\r\n";
					}
				}
			}
		}
		function setAgregarPerfilDeRiesgo($tipo, $fecha, $valor, $documento, $tipo_de_documento = false, $usuario= false, $hora = false, $tercero_relacionado = DEFAULT_SOCIO, $mensajes = ""){
			$xRiegos			= new cAMLRiesgos();
			$xRiegos->add($this->mClaveDePersona, $tipo, $fecha, $valor, $documento,$tipo_de_documento, $usuario, $hora, false, false, $tercero_relacionado, $mensajes);
			$this->mMessages	.= $xRiegos->getMessages();
		}
		function getPerfilDeRiesgo(){
			//verificar actividades
			//verificar trasacciones
			//obtener ingresos declarados vs depositos
			//riesgo geografico //pais //estado
			//canales de distribucion
			
		}
		function getSimilaresPorNacimiento(){
			
		}
		function getSimilarePorDomicilio(){
			
		}
		function setGuardarPerfilTransaccional($tipo, $pais, $monto, $numero, $observaciones, $fecha = false){
			$fecha	= ($fecha == false) ? fechasys(): $fecha;
			$xPT	= new cPersonas_perfil_transaccional();
			$xTT	= new cPersonas_perfil_transaccional_tipos();
			$xF		= new cFecha();
			$ql		= new MQL();
			$pais	= strtoupper($pais);
			
			$persona	= $this->mClaveDePersona;
			$id	= $xPT->query()->getLastID();
			$xPT->cantidad_calculada(0);
			$fv	= $xF->setSumarDias(AML_KYC_PERFIL_VIGENCIA, $fecha);
			$xTT->setData( $xTT->query()->initByID($tipo) );
			$ntipo	= $xTT->nombre_del_perfil()->v();
			//Eliminar perfil parecido..
			$sql		= "DELETE FROM personas_perfil_transaccional WHERE clave_de_persona = $persona AND clave_de_tipo_de_perfil = $tipo AND pais_de_origen='$pais' ";
			$ql->setRawQuery($sql);
			
			$xPT->afectacion( $xTT->afectacion()->v() );
			$xPT->cantidad_maxima($monto);
			$xPT->clave_de_persona( $persona );
			$xPT->clave_de_tipo_de_perfil($tipo);
			$xPT->fecha_de_calculo( $xF->getInt($fecha) );
			$xPT->fecha_de_registro( $xF->getInt($fecha) );
			$xPT->fecha_de_vencimiento( $xF->getInt($fv) );
			$xPT->idpersonas_perfil_transaccional($id);
			$xPT->maximo_de_operaciones($numero);
			$xPT->observaciones($observaciones);
			$xPT->operaciones_calculadas(0);
			$xPT->pais_de_origen($pais);
			$ql	= $xPT->query()->insert();
			$id	= $ql->save();
			$this->mMessages	.= ($id == false) ? "ERROR\tError al agregar el perfil tipo $ntipo por un monto de $monto\r\n" : "OK\tSe agrego el perfil $id de tipo $ntipo por un monto de $monto\r\n";
			if(MODO_DEBUG == true){
				$this->mMessages	.= $ql->getMessages(OUT_TXT);
			}
		}
		function getOPersona($data = false){
			if($this->mOSocio == null){
				$xSoc	= new cSocio($this->mClaveDePersona);
				$xSoc->init($data);
				$this->mOSocio	= $xSoc;
			}
			return $this->mOSocio;
		}
		function setDatosHeredados($datos){ $this->mDatosHeredados	= $datos; }
		function setVerificarDocumentosCompletos($fecha_de_verificacion = false){
			$xB		= new cBases();
			$xSoc	= $this->getOPersona();
			$mems	= ($xSoc->getEsPersonaFisica() == true) ? $xB->getMembers_InArray(false, BASE_DOCTOS_PERSONAS_FISICAS) : $xB->getMembers_InArray(false, BASE_DOCTOS_PERSONAS_MORALES);
			$sql	= "SELECT `clave_de_control`, `tipo_de_documento`,`clave_de_persona` FROM `personas_documentacion` WHERE 
					(`clave_de_persona` =" . $this->mClaveDePersona . ") AND (`estado_en_sistema` =" . AML_KYC_DOCTO_ACTIVO . ") ";
			$mql		= new cPersonas_documentacion();
			$tdoctos	= new cPersonas_documentacion_tipos();
			$TipoDeAlerta		= 801005;
			$PersonaDeDestino	= AML_OFICIAL_DE_CUMPLIMIENTO;
						
			$q			= $mql->query()->select(); $q->set($sql);
			$data		= $q->exec();
			$doctos		= array();
			$msg		= "";
			
			foreach ($data as $campos){
				$v		= $campos[ $mql->tipo_de_documento()->get() ];
				$doctos[$v]		= $v;
				//$this->mMessages	.= "Agregar " . $doctos[$mql->tipo_de_documento()->get()]	 . "\r\n";
			}
			if(MODO_DEBUG == true){
				//$this->mMessages	.= $q->log();
			}
			foreach ($mems as $clave => $valor){
				$tdoctos->setData( $tdoctos->query()->initByID($valor) );
				//var_dump($doctos[$valor]);
				if( !isset( $doctos[$valor] ) ){	
					$msg	.= "ERROR\t" . $this->mClaveDePersona . "\tDocumento ($valor) " . $tdoctos->nombre_del_documento()->v() . " NO encontrado\r\n" ;
				} else {
					$msg	.= "OK\t" . $this->mClaveDePersona . "\tDocumento " . $tdoctos->nombre_del_documento()->v() . " encontrado\r\n" ;
				}
			}
			//TODO: Verificar domicilios
			$dv					= $xSoc->getODomicilio();
			if($dv == null){
				$msg	.= "ERROR\t" . $this->mClaveDePersona . "\tNo existen datos de la Vivienda\r\n" ;
			} else {
				if( $dv->isInit() == false ){
					$msg	.= "ERROR\t" . $this->mClaveDePersona . "\tNo existen datos de la Vivienda\r\n" ;
				}
			}
			//TODO. Verificar Actividad Economica
			$da					= $xSoc->getOActividadEconomica();
			if($da == null){
				$msg	.= "ERROR\t" . $this->mClaveDePersona . "\tNo existen datos del la Actividad Economica\r\n" ;
			} else {
				if( $da->isInit() == false ){
					$msg	.= "ERROR\t" . $this->mClaveDePersona . "\tNo existen datos del Actividad Economica\r\n" ;
				}
			}
			$this->mMessages	.=		$msg;
			if($this->mForceAvisos == true){
				//generar aviso
				$xAml	= new cAML();
				$xAml->setForceAlerts(true);
				$xAml->sendAlerts(getUsuarioActual(), $PersonaDeDestino, $TipoDeAlerta, $msg);
			}			
		}
		function setGuardarDocumentoValidado($clave_de_docto, $resultado, $observaciones, $fecha = false){
			$fecha		= ($fecha == false) ? fechasys(): $fecha;
			$oficial	= getUsuarioActual();
			$sql		= "UPDATE personas_documentacion SET fecha_de_verificacion='$fecha', oficial_que_verifico=$oficial, resultado_de_la_verificacion=$resultado,
					     notas='$observaciones' WHERE clave_de_control=$clave_de_docto ";
			$q			= my_query($sql);
		}
		function setVerificarDocumentosVencidos($fecha_de_verificacion = false){
			$xCO			= new cPersonas_documentacion();
			$xF				= new cFecha();
			$where			= $xCO->clave_de_persona()->get() . "=" . $this->mClaveDePersona;
			$where			.= " AND " . $xCO->resultado_de_la_verificacion()->get() . "=" . AML_KYC_DOCTO_NO_VERIFICADO;
			$q				= $xCO->query()->select();
			$data			= $q->exec($where);
			$fecha_actual	= $xF->getInt($fecha_de_verificacion);
			$markT			= (AML_KYC_DIAS_PARA_REVISAR_DOCTOS * SYS_FACTOR_DIAS);
			$TipoDeAlerta	= 801006;
			$PersonaDeDestino	= AML_OFICIAL_DE_CUMPLIMIENTO;
			$msg			= "";
			//$this->mMessages	.= $q->log();
			foreach ($data as $datos){
				$xCO->setData($datos);
				$persona	= $xCO->clave_de_persona()->v();
				//checar los dias sin verificar
				$fecha_limite	= ($xCO->fecha_de_carga()->v() + $markT );
				$clave_docto	= $xCO->clave_de_control()->v();
				if($fecha_limite < $fecha_actual){
					$msg	.= "ERROR\t$persona\tDocumento $clave_docto VENCIDO " . $xF->getFechaByInt($fecha_limite) . "\r\n";
				} else {
					$msg	.= "WARN\t$persona\tDocumento $clave_docto no verificado al " . $xF->getFechaByInt($fecha_actual) . " limite al  " . $xF->getFechaByInt($fecha_limite) . "\r\n";
				}
			}
			$this->mMessages	.=		$msg;
			if($this->mForceAvisos == true){
				//generar aviso
				$xAml	= new cAML();
				$xAml->setForceAlerts(true);
				$xAml->sendAlerts(getUsuarioActual(), $PersonaDeDestino, $TipoDeAlerta, $msg);
			}
		}
		function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
		function getOAcumuladoDeOperaciones($fecha_inicial, $fecha_final = false, $moneda = false, $tipo = false){
			$xF					= new cFecha();
			$xQL				= new cSQLListas();
			$moneda				= ($moneda == false) ? AML_CLAVE_MONEDA_LOCAL : $moneda;
			$fecha_final		= ($fecha_final == false) ? $fecha_inicial : $fecha_final;
			$periodo_inicial	= date("Ym", $xF->getInt($fecha_inicial));
			$periodo_final		= date("Ym", $xF->getInt($fecha_final));
			$ql					= new MQL();
			$persona			= $this->getCodigoDePersona();
			$sql				= $xQL->getAMLAcumuladoDeEgresos($periodo_inicial, $periodo_final, $persona, $moneda, $tipo );
			
			$datos				= $ql->getDataRow($sql);
			if(!isset($datos[SYS_MONTO]) OR setNoMenorQueCero($datos[SYS_MONTO]) <= 0){
				$sql				= $xQL->getAMLAcumuladoDeEgresos_RT($periodo_inicial, $periodo_final, $persona, $moneda, $tipo );
				$datos				= $ql->getDataRow($sql);
			}
			$xT					= new cAMLTipoDatos_PersonasTransacciones();
			$xT->set($datos);
			return $xT;
		}
		function setVerificarOperacionesMensuales($fecha){
			//$obj			= $this->getOAcumuladoDeOperaciones($fecha, $fecha);
			//return $obj;
		}
		function setVerificarOperacionesSemestrales($fecha = false){
			//fecha inicial - 6 meses
			$fecha			= ($fecha == false) ? fechasys() : $fecha;
			$xF				= new cFecha();
			$fecha_inicial	= $xF->setRestarMeses(6, $fecha);
			$obj			= $this->getOAcumuladoDeOperaciones($fecha_inicial, $fecha);
			//return $obj;
		}
		function setVerificarPerfilTransaccional($fecha= false, $generar_avisos = false){
			$xQL			= new cSQLListas();
			$mq				= new MQL();
			$xPerf			= new cAMLPersonas_PerfilTransaccional($this->mClaveDePersona);
			$xF				= new cFecha();
			$fecha			= ($fecha == false) ? fechasys() : $fecha;
			
			$fechaI6M		= $xF->setRestarMeses(6, $fecha);													//6 Meses
			$periodoI6M		= date("Ym", $xF->getInt($fechaI6M));												//6 Meses
			$periodo_inicial= date("Ym", $xF->getInt($fecha));
			$periodo_final	= date("Ym", $xF->getInt($fecha));
						
			$persona		= $this->getCodigoDePersona();
			$sql			= $xQL->getAMLAcumuladoDeEgresos($periodo_inicial, $periodo_final, $persona);
			
			$datos			= $mq->getDataRecord($sql);
			//checar datos si no existen en la consulta acumulada
			if(!isset($datos[SYS_MONTO]) OR setNoMenorQueCero($datos[SYS_MONTO]) <= 0){
				$sql		= $xQL->getAMLAcumuladoDeEgresos_RT($periodo_inicial, $periodo_final, $persona);
				$datos		= $mq->getDataRecord($sql);
			}
			$suma_efectivo	= 0;
			$mes_activo		= 0;
			$excedido		= false;
			
			//setLog($sql);
			foreach ($datos as $row){
				$tipo		= strtolower($row[SYS_TIPO]);
				$moneda		= strtoupper($row["moneda"]);
				$monto		= $row[SYS_MONTO];
				$numero		= $row[SYS_NUMERO];
				$mes		= $row["periodo"];
				$recibo		= $row["recibo"];
				
				$xDPerfil	= $xPerf->concepto($tipo);
				$xMonto		= new cCantidad($monto);
				
				
				$monto		= $xMonto->v();
				$pmonto		= $xDPerfil->v();		//Monto permitido
				$montoDiff	= $xMonto->diff($pmonto);
				$pnumero	= $xDPerfil->getNumero();
				
				if($moneda != $xDPerfil->getClaveDeMoneda() ){
					$this->mMessages	.= "ERROR\t$tipo|$moneda\tNo tiene Perfil para operar con la Moneda $moneda \r\n";
					$excedido			= true;					
				}
				if($monto > $pmonto){
					$this->mMessages	.= "ERROR\t$tipo|$moneda\tMonto excedido ($montoDiff) por de $pmonto tuvo operaciones por $monto\r\n";
					$excedido			= true;
				} else {
					$this->mMessages	.= "OK\t$tipo|$moneda\tMonto Normal de $pmonto hizo operaciones por $monto\r\n";
				}
				if($numero > $pnumero){
					$this->mMessages	.= "ERROR\t$tipo|$moneda\tNumero excedido de $pnumero tuvo operaciones por $numero\r\n";
					$excedido			= true;
				} else {
					$this->mMessages	.= "OK\t$tipo|$moneda\tNumero normal de $pnumero hizo operaciones por $numero\r\n";
				}
				$mes_activo				= $mes;
				if($tipo == TESORERIA_COBRO_EFECTIVO){  $suma_efectivo	= $monto; }
			}
			if(MODO_DEBUG == true){
				$this->mMessages			.= $xPerf->getMessages(OUT_TXT);
			}
			//$this->mMessages			.= "OK\t \r\n";
			//$efectivo		= TESORERIA_COBRO_EFECTIVO . "-" . AML_CLAVE_MONEDA_LOCAL; //efectivo nacional
			//verificar pagos mayores a 500 USD
			//verificar pagos
			if($excedido == true){
				
				if($this->mForceAvisos == true OR $generar_avisos == true){
					$xAml				= new cAML();
					$xAml->setForceAlerts();
					$res				= $xAml->sendAlerts($persona, AML_OFICIAL_DE_CUMPLIMIENTO, AML_DEBITOS_MAYORES_AL_PERFIL, $this->mMessages, $recibo, $fecha, false, 0, iDE_RECIBO);
					if($res == false){ $this->mMessages	.= "ERROR\tError al enviar alertas\r\n"; }
					$this->mMessages	.= $xAml->getMessages();
				} else {
					$this->mMessages	.= "WARN\tNo se enviaron alertas\r\n";
				}
			}
			return $excedido;
		}
		function getBuscarEnListaNegra($nombre = "", $primerapellido = "", $segundoapellido = ""){
			$this->init();
			$nombre				= ($nombre == "") ? $this->getOPersona()->getNombre() : $nombre;
			$primerapellido		= ($primerapellido == "") ? $this->getOPersona()->getApellidoPaterno() : $primerapellido;
			$segundoapellido	= ($segundoapellido == "") ? $this->getOPersona()->getApellidoMaterno() : $segundoapellido;
			
			$xUser				= new cSystemUser();
			$ctx				= $xUser->getCTX();
			$extras				= (AML_BUSQUEDA_PERSONAS_REFORZADA == true) ? "&jarowinkler=true&metaphone=true" : "";
			//$nombre				= urlencode($nombre);
			$result				= false;
			$items				= 0;//count($data);
			$mURL				= SVC_REMOTE_HOST . "svc/listanegra.svc.php?n=" . urlencode($nombre) . "&p=" . urlencode($primerapellido) . "&m=" . urlencode($segundoapellido) . "&ctx=$ctx" . $extras;
			
			if(MODO_DEBUG == true){ setLog($mURL);}
			$ql					= new MQLService("", "");
			$json 				= $ql->getRequest($mURL); //file_get_contents($mURL);
			$data 				= json_decode($json, true);
			if(!$data){
				$this->mMessages	.= "ERROR\tNo existen ITEMS\r\n";
			} else {
				foreach ($data as $subobj){
					$items++;
					//{"codigo":"16145","primerapellido":"CALLE","segundoapellido":"QUIROS","nombres":" LUIS SANTIAGO","curp":"SDNTK-16145","tipo":"metaphone"}
					//var_dump($subobj);
					//setLog( $subobj->primerapellido );
					//foreach ($subobj as $cls){
					//}
					$this->mMessages	.= "WARN\t" . $subobj["curp"] . " : Coincidencia " . $subobj["primerapellido"] . " ". $subobj["segundoapellido"] . " " . $subobj["nombres"] . " encontrado con  " . $subobj["tipo"] . "\r\n";
				}
			}
			//setLog("NUMERO DE ITEMS $items");
			$this->mMessages		.= "WARN\tReporte : $mURL&report=true&ret=true \r\n";
			$this->mURLConsulta		= "$mURL&report=true&ret=true";
			//var_dump($data);
			if($items >= 1){
				$result	= true;
				$this->mMessages	.= "ERROR\tPersona en Lista Negra o con Alto riesgo con $items posibles concindencias\r\n";
				
			}

			return $result;
		}
		function getReporteConsultaListaNegra(){ return $this->mURLConsulta; }
		//function setReportarCambioNivel()
		function setActualizarRiesgoPorNucleo() {
			//si todos son riesgo
			$xLi	= new cSQLListas();
			$xQL	= new MQL();
			$sql	= $xLi->getListadoDePersonasExpuestas($this->mClaveDePersona);
			$rs		= $xQL->getDataRecord($sql);
			
			$xCat	= new cPersonasCatalogoOtrosDatos();
			foreach ($rs as $rows){
				$idpersona		= $rows["numero_socio"];
				$afinidad		= $rows["grado_de_afinidad"];
				$consanguinidad	= $rows["grado_de_consanguinidad"];
				$vinculoeconom	= $rows["tiene_vinculo_patrimonial"];
				//agregar
				$tipoaml	= ($consanguinidad > 0) ? $xCat->AML_PEP_CONSANGUINIDAD : "";
				$tipoaml	= ($afinidad > 0 AND $tipoaml == "") ? $xCat->AML_PEP_AFINIDAD : $tipoaml;
				$tipoaml	= ($vinculoeconom > 0 AND $tipoaml == "") ? $xCat->AML_PEP_VINCULO_ECONOM : $tipoaml;
				if($tipoaml != ""){
					$xRel			= new cSocio($idpersona);
					if($xRel->init() == true){
						$xRel->addOtrosParametros($tipoaml, "1");
						$this->mMessages	.= $xRel->getMessages();
					}
				}
			}
		}
		function getNucleoDeRiesgo($tipo){
			//tipo PEP, ALTORIESGO
		}
		function addTransaccionalidadPorEmpresa($empresa, $monto, $fecha = false){
			$xEmp	= new cEmpresas($empresa);
			$monto	= setNoMenorQueCero($monto);
			if($xEmp->init() == true){
				$pais		= EACP_CLAVE_DE_PAIS;
				$numero		= ceil(setNoMenorQueCero( ((365 / $xEmp->getPeriocidadPref()) / 12) )) + 1;
				//$xEmp->getOPersona();
				if($monto > 0 AND $numero > 0){
					$monto	= $monto * 2;
					$numero	= $numero * 2;
					$this->setGuardarPerfilTransaccional(AML_OPERACIONES_PAGOS_EFVO, $pais, $monto, $numero, "Generado de la Empresa $empresa", $fecha);
					//$this->setGuardarPerfilTransaccional(AML_OPERACIONES_PAGOS_EFVO, $pais, $monto, $numero, "Generado de la Empresa $empresa", $fecha);
					//deposito bancario
				}
			}
		}
		
		function setAnalizarNivelDeRiesgo(){
			if($this->mOSocio == null){ $this->getOPersona(); }
			$riesgo		= 1;
			$factores	= 0;
			$this->mMessages	.= "====\tPersona: " . $this->mClaveDePersona . "\t====\r\n";
			//revisar matrices
			//revisar si es extranjero
			//Trabajar en localidades riesgosas, paises riesgosas
			if($this->mOSocio->getEsExtranjero() == true){
				$factores++;
				$riesgo += 50; //riesgo medio
				$this->mMessages	.= "WARN\tRiesgo a $riesgo por ser Extranjero (Medio)\r\n";
			}
		
			//si es persona moral, obetener datos del representante
			if($this->mOSocio->getEsPersonaFisica() == false){
				$xRels	= $this->mOSocio->getORepresentanteLegal();
				if($xRels == null){ $factores++; $riesgo += 50; $this->mMessages	.= "WARN\tRiesgo a $riesgo  por no tener Representante Legal\r\n"; }
			}
			//validar perfil transaccional
			$xPer	= new cAMLPersonas_PerfilTransaccional($this->mOSocio->getCodigo());
			if($xPer->getNumeroEntradas() <= 0){
				$factores++;
				$riesgo				+= SYS_RIESGO_MEDIO;
				$this->mMessages	.= "WARN\tRiesgo a $riesgo  por no tener perfil trasaccional\r\n";
			}
			//revisar su actividad economica
			$OAE	= $this->mOSocio->getOActividadEconomica();
			if($OAE == null){
				$factores++;
				$riesgo += 50; //riesgo medio por no tener info
				$this->mMessages	.= "WARN\tRiesgo a $riesgo  por no tener Actividad Economica (Medio)\r\n";
			}  else {
				$factores++;
				$riesgo 			+= ( $OAE->getEsRiesgosa() == true ) ? $OAE->getRiesgoAMLAsociado() : 10;
				$this->mMessages	.= "WARN\tRiesgo a $riesgo  en Actividad Economica\r\n";
				$this->mMessages	.= $OAE->getMessages();
				//checar si su domicilio no esta en paises
				//100% de actividades Riesgosas
				if($OAE->getRiesgoAMLAsociado() >= SYS_RIESGO_ALTO){
					$factores		= 1;
					$riesgo			= SYS_RIESGO_ALTO;
				}
				//actualiza el riesgo de la persona por pep
				if($OAE->getGeneraPEP() == true){
					$xOCat		= new cPersonasCatalogoOtrosDatos();
					$this->mOSocio->addOtrosParametros($xOCat->AML_PEP_PRINCIPAL, 1);
				}
			}
			//revisar su actividad economica idlocalidad o domicilio
			$ODom	= $this->mOSocio->getODomicilio();
			if($ODom == null){
				$factores++;
				$riesgo += 50; //riesgo medio por no tener info
				$this->mMessages	.= "WARN\tRiesgo a $riesgo por no tener domicilio (Medio)\r\n";
			}  else {
				$factores++;
				//es pais riesgoso
				$pais	= $ODom->getClaveDePais();
				if($pais != EACP_CLAVE_DE_PAIS){
					$xPais				= new cDomiciliosPaises($pais);
					$xPais->init();
					$riesgoPais			= $xPais->getRiesgoAMLAsociado();
					$riesgo				+= $riesgoPais;
					if($riesgoPais == SYS_RIESGO_ALTO){
						$riesgo			= $riesgoPais;
						$factores		= 1;
					}
					$this->mMessages	.= "WARN\tRiesgo a $riesgo agregado $riesgoPais del pais $pais\r\n";
				} else {
					$riesgo				+= SYS_RIESGO_BAJO;
					$this->mMessages	.= "WARN\tRiesgo a $riesgo agregado del pais $pais\r\n";
				}
			}
						
			$riesgo				= ($riesgo /$factores);
			if($riesgo > 10 AND $riesgo <= 50){
				$this->mMessages	.= "WARN\tRiesgo a MEDIO por $riesgo con Factores $factores \r\n";
				$riesgo		= SYS_RIESGO_MEDIO;
			} else if ($riesgo >= 51 ){
				$this->mMessages	.= "WARN\tRiesgo a ALTO por $riesgo con Factores $factores \r\n";
				$riesgo		= SYS_RIESGO_ALTO;				
			} else {
				$this->mMessages	.= "WARN\tRiesgo a BAJO por $riesgo con Factores $factores \r\n";
				$riesgo		= SYS_RIESGO_BAJO;				
			}
			//$this->mMessages	.= "WARN\tRiesgo a $riesgo con Factores $factores \r\n";
			//revisar los productos con riesgo
			return $riesgo;
		}
		function setAnalizarTransaccionalidadPorNucleo($recibo = false, $fecha = false, $usuario = false, $alertar = false){
			$xF			= new cFecha();
			$xQ			= new MQL();
			$usuario	= setNoMenorQueCero($usuario);
			$fecha		= $xF->getFechaISO($fecha);
			if($this->mOSocio == null){ $this->getOPersona(); }
			$idnucleo				= $this->mOSocio->getIDNucleoDeRiesgo();
			$persona_relacionada	= $this->mOSocio->getCodigo();
			$sql1		= "SELECT * FROM personas_operaciones_recursivas WHERE persona =$idnucleo AND usuario=$usuario LIMIT 0,1";
			$DNormal	= $xQ->getDataRow($sql1);
			$operaciones= 1;
			$monto		= null;
			$aviso		= false;
			
			if(isset($DNormal["operaciones"])){
				$operaciones 	= $DNormal["operaciones"];
				$monto 			= $DNormal["monto"];
				$sql2			= "SELECT
					`personas_relaciones_recursivas`.`persona`     AS `persona`,
					`operaciones_recibos`.`idusuario`              AS `usuario`, 
						
					COUNT(`operaciones_recibos`.`idoperaciones_recibos`) AS `operaciones`,
					SUM(`operaciones_recibos`.`total_operacion`) AS `monto`
				FROM
					`operaciones_recibos` `operaciones_recibos` 
						INNER JOIN `personas_relaciones_recursivas` 
						`personas_relaciones_recursivas` 
						ON `operaciones_recibos`.`numero_socio` = 
						`personas_relaciones_recursivas`.`persona`
				WHERE
					(`operaciones_recibos`.`origen_aml` >0)
					AND (`operaciones_recibos`.`idusuario`=$usuario)
					AND (`operaciones_recibos`.`fecha_operacion`  = '$fecha') 
					AND (`personas_relaciones_recursivas`.`clave_interna` = $idnucleo)
					GROUP BY
						`personas_relaciones_recursivas`.`persona`,
						`operaciones_recibos`.`idusuario` ";
				$DEfectuado	= $xQ->getDataRow($sql2);
				if(isset($DEfectuado["operaciones"])){					//evalua lo operado
					if($DEfectuado["operaciones"] > $operaciones  ){	//se pasa en numero
						$dif				= $DEfectuado["operaciones"] - $operaciones;
						$this->mMessages	.= "ERROR\tNUCLEOS\tNumero de Operaciones Excedidas en $dif del usuario $usuario con el Nucleo $idnucleo \r\n";
						$aviso				= true;
					}
					if($DEfectuado["monto"] > $monto  ){	//se pasa en numero
						$dif				= $DEfectuado["monto"] - $monto;
						$this->mMessages	.= "ERROR\tNUCLEOS\tMonto de Operaciones Excedidas en $dif del usuario $usuario con el Nucleo $idnucleo\r\n";
						$aviso				= true;
					}
					if($aviso == true and $alertar == true){
						$xAml	= new cAML();
						$xAml->setForceAlerts(true);
						$xAml->setReportarUsuario($usuario, AML_ID_OPERACIONES_ATOMICAS, $this->mMessages, $recibo, $fecha, $persona_relacionada, iDE_RECIBO);
						$this->mMessages	.= $xAml->getMessages();
					}
				} else {
					$this->mMessages	.= "WARN\tNUCLEOS\tNo hay Monto para Evaluar al usuario $usuario con el Nucleo $idnucleo\r\n";
				}
			} else {
				$this->mMessages	.= "WARN\tNUCLEOS\tNo hay parametros para Evaluar al usuario $usuario con el Nucleo $idnucleo\r\n";
			}
			setLog($this->mMessages);
		}
		function getEsPersonaVigilada(){
			if($this->mEsVigilado == null){
			//verificar SQL
			$ql	= new MQL();
			$rs	= $ql->getDataRow("SELECT * FROM `aml_personas_vigiladas` WHERE persona=" . $this->mClaveDePersona . " LIMIT 0,1");
				if(isset($rs["persona"])){
					$this->mEsVigilado	= true;
					$monto				= setNoMenorQueCero($rs["obligaciones_contratadas"]);
					$this->mMessages	.= "WARN\tEsta persona es VIGILADA con un monto de " . $monto . "\r\n";				
					
				} else {
					$this->mMessages	.= "OK\tEsta persona " . $this->mClaveDePersona ." es NO VIGILADA\r\n";
					$this->mEsVigilado	= false;
				}
			}
			return $this->mEsVigilado;
		}
	}
	class cAMLPersonas_PerfilTransaccional {
		private $mDatos 				= array();
		private $mMessages				= "";
		private $mClaveDePersona		= false;
		private $mIngresosMensuales	= 0;
		private $mEgresosMensuales		= 0;
		private $mAPerfil				= array();
		private $mItems				= 0;
		
		public $PAGOS_NAC_EFECTIVO		= 101;
		public $PAGOS_INT_EFECTIVO		= 102;
		
		public $PAGOS_NAC_CHEQUE		= 401;
		public $PAGOS_INT_CHEQUE		= 402;
		
		public $PAGOS_NAC_ELECTRONICO	= 201;
		public $PAGOS_INT_ELECTRONICO	= 202;

		public $PAGOS_NAC_PLASTICO		= 301;
		public $PAGOS_INT_PLASTICO		= 302;
		
		function __construct($persona){
				$this->mClaveDePersona	= $persona;
				$xQL					= new MQL();
				$xLi					= new cSQLListas();
				$datos					= $xQL->getDataRecord($xLi->getListadoResumenPerfilTransaccional($persona));
				
				foreach ($datos as $row){
					$this->mItems++;
					$tipo				= $row["tipo"];
					$numero				= $row[SYS_NUMERO];
					$monto				= $row[SYS_MONTO];
					$exhibicion			= strtolower($row["exhibicion"]);
					
					if(isset($this->mAPerfil[$tipo])){
						$this->mAPerfil[$tipo][SYS_NUMERO]			+= $numero;
						$this->mAPerfil[$tipo][SYS_MONTO]			+= $monto;
						$this->mMessages	.= "WARN\t$tipo|" .  $row[SYS_MONEDA] . "\tSumando Monto $monto con Numero $numero\r\n";
					} else {
						$this->mAPerfil[$tipo][SYS_NUMERO]			= $numero;
						$this->mAPerfil[$tipo][SYS_MONTO]			= $monto;
						$this->mAPerfil[$tipo]["pais"]				= $row["pais"];
						$this->mAPerfil[$tipo][SYS_MONEDA]			= $row[SYS_MONEDA];
						$this->mAPerfil[$tipo][SYS_TIPO]			= $exhibicion;
						$this->mMessages	.= "WARN\t$tipo|" .  $row[SYS_MONEDA] . "\tAgregando Monto $monto con Numero $numero\r\n";
					}
					//agregar por exhibicion
					if(isset($this->mAPerfil[$exhibicion])){
						$this->mAPerfil[$exhibicion][SYS_NUMERO]	+= $numero;
						$this->mAPerfil[$exhibicion][SYS_MONTO]		+= $monto;
						$this->mMessages	.= "WARN\t$exhibicion|" .  $row[SYS_MONEDA] . "\tSumando Monto $monto con Numero $numero\r\n";
					} else {
						$this->mAPerfil[$exhibicion][SYS_NUMERO]	= $numero;
						$this->mAPerfil[$exhibicion][SYS_MONTO]		= $monto;
						$this->mAPerfil[$exhibicion]["pais"]		= $row["pais"];
						$this->mAPerfil[$exhibicion][SYS_MONEDA]	= $row[SYS_MONEDA];
						$this->mAPerfil[$exhibicion][SYS_TIPO]		= $tipo;
						$this->mMessages	.= "WARN\t$exhibicion|" .  $row[SYS_MONEDA] . "\tAgregando Monto $monto con Numero $numero \r\n";
					}				
				}
				
				
		}
		function concepto($clave){
			if(!isset($this->mAPerfil[$clave])){
				$this->mAPerfil[$clave][SYS_MONTO]	= 0;
				$this->mAPerfil[$clave][SYS_NUMERO]	= 0;
				$this->mAPerfil[$clave][SYS_MONEDA]	= AML_CLAVE_MONEDA_LOCAL;
			}
			//2015-01-08
			$this->mAPerfil[$clave][SYS_NUMERO]	+= 1;
			$this->mAPerfil[$clave][SYS_MONTO]	+= AML_MINIMO_EXCESO_EN_OPERACIONES_POR_PERFIL;
			$xCant		= new cCantidad(0);
			$xCant->set($this->mAPerfil[$clave]);
			
			return $xCant;
		}
		function getPagosEnEfectivoNac($tipo = false){ return $this->getRAWConcepto(101, $tipo); }
		function getPagosEnEfectivoInt($tipo = false){ return $this->getRAWConcepto(102, $tipo); }
		function getPagosEnChequeNac($tipo = false){ return $this->getRAWConcepto(401, $tipo); }
		function getPagosEnChequeInt($tipo = false){ return $this->getRAWConcepto(402, $tipo); }
		function getPagosElectronicoNac($tipo = false){ return $this->getRAWConcepto(201, $tipo); }
		function getPagosElectronicoInt($tipo = false){ return $this->getRAWConcepto(202, $tipo); }
		
		function getPagosPlasticoVisa($tipo = false){	return $this->getRAWConcepto(301, $tipo); }
		function getPagosPlasticoMasterCard($tipo = false){	return $this->getRAWConcepto(302, $tipo); }
		function getPagosPlasticoProsa($tipo = false){ return $this->getRAWConcepto(303, $tipo); }
		function getPagosPlasticoOtros($tipo = false){ return $this->getRAWConcepto(399, $tipo); }
		
		function getRAWConcepto($clave, $tipo = false){
			if(!isset($this->mAPerfil[$clave])){
				$this->mAPerfil[$clave][SYS_MONTO]		= 0;
				$this->mAPerfil[$clave][SYS_NUMERO]	= 0;
			}
			return ($tipo == false) ? $this->mAPerfil[$clave] : $this->mAPerfil[$clave][$tipo];			
		}
		function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
		function getNumeroEntradas(){ return $this->mItems; }
	}
	class cAMLOperaciones{
		private $mMessages			= "";
		private $mMsgAlert			= "";
		private $mTipoDeAlerta		= false;
		private $mTipoDeReporte	= "C"; //Calificado
		private $mPaisDeOrigen		= EACP_CLAVE_DE_PAIS;
		function __construct(){}
		
		function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
		function analizarOperacion($persona, $monto_operado, $moneda, $tipo_de_pago, $fecha = false, $recibo = false, $perfil = false){
			$moneda	= strtoupper($moneda);
			$sql	= "SELECT * FROM `aml_risk_catalog` 
					WHERE (`aml_risk_catalog`.`tipo_de_riesgo` = 912)
					AND (`aml_risk_catalog`.`clave_de_control` != " . AML_CLAVE_RIESGO_OPS_INDIVIDUALES . ")
					ORDER BY 
					`aml_risk_catalog`.`unidad_de_medida`,	`aml_risk_catalog`.`unidades_ponderadas` DESC ";
			$ql		= new MQL();
			$risk	= new cAml_risk_catalog();
			
			$raised	= false;
			$xAML	= new cAML();
			$rs		= $ql->getDataRecord($sql);
			//Datos de Operaciones Mensuales
			$xAMLP	= new cAMLPersonas($persona);
			$xAMLP->init();
			$OMens				= $xAMLP->getOAcumuladoDeOperaciones($fecha, $fecha, $moneda, $tipo_de_pago);
			$monto_original		= $OMens->getMonto() + $monto_operado;
			$this->mMessages	.= "WARN\tOperaciones acumuladas por " . $OMens->getMonto() . "\r\n";
			if($perfil == false){
				$this->mMessages.= "ERROR\tNo existe el perfil tipo de pago $tipo_de_pago en Moneda $moneda \r\n";
				//agregar perfil a cero
			} else {
				$perfil		= setNoMenorQueCero($perfil);
				$sql		= "SELECT * FROM `personas_perfil_transaccional_tipos` WHERE `idpersonas_perfil_transaccional_tipos` = $perfil LIMIT 0,1";
				$d			= $ql->getDataRow($sql);
				if(isset($d["tipo_de_exhibicion"])){
					$tipo_de_pago	= strtolower($d["tipo_de_exhibicion"]);
				}
				$this->mMessages	.= "OK\tCarga del perfil $perfil  con tipo de pago $tipo_de_pago\r\n";
			}
			//verificar operaciones con reglas
			foreach ($rs as $rows){
				$risk->setData($rows);
				$reporteMoneda		=  strtoupper( $risk->unidad_de_medida()->v() );
				if($reporteMoneda == $moneda){
					if($raised == false){
						$reporteMoneda		= strtoupper( $risk->unidad_de_medida()->v());
						$xMon				= new cMonedas($reporteMoneda);
						$valor_local		= $xMon->getValor();
						$clave				= $risk->clave_de_control()->v();
						if($xAML->isTransaccionVigilada($tipo_de_pago) != false){
							$unidadesReportadas	= $risk->unidades_ponderadas()->v();
							$unidadesOperadas	= 0;
							if($reporteMoneda == AML_CLAVE_MONEDA_LOCAL){
								$unidadesOperadas	= $monto_original;
							} else {
								$this->mMessages	.= "WARN\tUnidades reportadas $unidadesOperadas en Moneda $moneda con valor local de $valor_local\r\n";
								$unidadesReportadas	= ($valor_local * $unidadesReportadas) / VALOR_ACTUAL_DOLAR;
								$unidadesOperadas	= ($monto_original * $valor_local) / VALOR_ACTUAL_DOLAR;
							}
							if($unidadesOperadas >= $unidadesReportadas ){
								$this->mMsgAlert		= "ERROR\tUnidades excedidas de $unidadesReportadas operados $unidadesOperadas en la Moneda $reporteMoneda\r\n";
								$this->mMessages		.= $this->mMsgAlert;
								$raised					= true;
								$this->mTipoDeAlerta	= $clave;
								$this->mTipoDeReporte	= $risk->forma_de_reportar()->v();
							} else {
								$this->mMessages	.= "OK\tOperacion Normal de $unidadesOperadas no sobrepasan $unidadesReportadas\r\n";
							}
							//Validar sivienen de paises con alto riesgo
							$xP						= new cDomiciliosPaises();
							$xP->getPaisPorMoneda($reporteMoneda);
							$nombrepais					= $xP->getNombre();
							if($xP->getRiesgoAMLAsociado() >= SYS_RIESGO_MEDIO){
								//Agregar alerta por operaciones en paises de  alto y medio riesgo
								$this->mMsgAlert	.= "ERROR\tFondos provenientes de paises con Riesgo Elevado $nombrepais\r\n";
								$this->mMessages	.= "ERROR\tFondos provenientes de paises con Riesgo Elevado $nombrepais\r\n";
							}					
						} else {
							$this->mMessages	.= "OK\tOperacion Omitida por ser tipo de pago $tipo_de_pago ($reporteMoneda )\r\n";
						}
					}
				} else {
					$this->mMessages	.= "OK\tOperacion Omitida por ser Moneda $moneda de $reporteMoneda\r\n";
				}
			}
			//setLog($this->mMessages);
			if(MODO_DEBUG == true){ setLog($this->getMessages()); }
			return $raised;
		}
		function getTipoDeAlerta(){ return $this->mTipoDeAlerta; }
		function getTipoDeReporte(){ return $this->mTipoDeReporte; }
		function getMessageAlert(){ return $this->mMsgAlert; }
		function getPaisDeOrigen(){ return $this->mPaisDeOrigen; }
	}

	class cAMLTipoDatos_PersonasTransacciones {
		private $mDatos 			= array();
		private $mClaveDePersona	= false;
		function __construct(){}
		function get(){ return $this->mDatos; }
		function set($aDatos){ $this->mDatos	= $aDatos; }
		function getMonto(){
			return isset($this->mDatos[SYS_MONTO]) ? $this->mDatos[SYS_MONTO] : 0;
		}
		function getNumero(){
			return isset($this->mDatos[SYS_NUMERO]) ? $this->mDatos[SYS_NUMERO] : 0;
		}
		function getMoneda(){
			return isset($this->mDatos["moneda"]) ? $this->mDatos["moneda"] : 0;
		}
		function getOriginal(){
			return isset($this->mDatos["original"]) ? $this->mDatos["original"] : 0;
		}
		function getTipo(){
			return isset($this->mDatos["tipo"]) ? $this->mDatos["tipo"] : "ninguno";
		}
		function getSQL(){
			return isset($this->mDatos[SYS_SQL]) ? $this->mDatos[SYS_SQL] : 0;
		}
	}

	class cAMLRiesgos {
		private $mCodigo		= false;
		private $mObj			= null;
		private $mIsInit		= false;
		private $mMessages		= "";
		function __construct($id = false){
			$this->mCodigo	= $id;
				
		}
		function init(){
			$this->mObj		= new cAml_risk_register();
			$data			= $this->mObj->query()->initByID($this->mCodigo);
			$this->mObj->setData( $data );
			return $this->mIsInit;
		}
		function add($persona , $tipo, $fecha, $valor, $documento, $tipo_de_documento = false, $usuario = 
				false, $hora = false, $instrumento = false, $tipo_de_operacion = false, 
				$tercero_relacionado = DEFAULT_SOCIO, $mensajes = "", $inmediato = false){
			$hora				= ($hora == false) ? date("Hi") : $hora;
			$usuario			= ($usuario == false) ? getUsuarioActual() : $usuario;
			$tipo_de_documento	= ($tipo_de_documento == false) ? iDE_RECIBO : $tipo_de_documento;
			$instrumento		= setNoMenorQueCero($instrumento);
			if( $instrumento <=0 OR setNoMenorQueCero($tipo_de_operacion) <= 0){
				switch ($tipo_de_documento){
					case iDE_RECIBO:
						$EqOps		= new cSistemaEquivalencias(TOPERACIONES_RECIBOSTIPOS);
						$EqTes		= new cSistemaEquivalencias(TTESORERIA_TIPOS_DE_PAGO);
						
						$xRec				= new cReciboDeOperacion(false, false, $documento);
						$instrumento		= $EqTes->get($xRec->getTipoDePago());
						$tipo_de_operacion	= $EqOps->get( $xRec->getTipoDeRecibo()  );
						if($xRec->isDivisaExtranjera() == true){
							$instrumento	= AML_OPERACIONES_CLAVE_DIVISA;
						}
						if($tipo_de_operacion == null){ $tipo_de_operacion	= "01"; }
						break;
				}
			}
			if($inmediato == true){$inmediato = 1;}
			$inmediato	= setNoMenorQueCero($inmediato);
			$xPR		= new cAml_risk_register();
			$xPR->clave_de_riesgo( $xPR->query()->getLastID() );
			$xPR->escore($valor);
			$xPR->fecha_de_reporte($fecha);
			$xPR->hora_de_reporte($hora);
			$xPR->persona_relacionada($persona);
			$xPR->tipo_de_riesgo($tipo);
			$xPR->usuario_de_origen($usuario);
			$xPR->tipo_de_documento($tipo_de_documento);
			$xPR->documento_relacionado($documento);
			$xPR->estado_de_envio(SYS_UNO);
				
			$xPR->fecha_de_envio(0);
			$xPR->estado_de_envio(0);
			$xPR->fecha_de_checking(0);
			$xPR->oficial_de_checking(AML_OFICIAL_DE_CUMPLIMIENTO);
			$xPR->monto_total_relacionado(0);
			//cargar datos del recibo
			$xPR->instrumento_financiero($instrumento); //mejorar segun catalogo CNBV
			$xPR->tipo_de_operacion($tipo_de_operacion);
			$xPR->tercero_relacionado($tercero_relacionado);
			$xPR->mensajes_del_sistema($mensajes);
			$xPR->reporte_inmediato($inmediato);
			$ql	= $xPR->query()->insert();
			$ql->save();
			$this->mMessages	.= $ql->getMessages(OUT_TXT);
		}
		function agregarChecking(){
			
		}
		function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	}
	class cAMLAlertas {
		protected $mMessages= "";
		private $mCodigo	= false;
		private $mObj		= null;
		private $mIsInit	= false;
		
		function __construct($id = false){
			$this->mCodigo	= $id;
			
		}
		function init(){
			$this->mObj		= new cAml_alerts();
			$data			= $this->mObj->query()->initByID($this->mCodigo);
			$this->mObj->setData( $data );
			return $this->mIsInit;
		}
		function getPersonaDeOrigen(){ return $this->mObj->persona_de_origen()->v(); }
		function getPersonDeDestino(){ $this->mObj->persona_de_destino()->v(); }
		function getTipoDeAlerta(){ return $this->mObj->tipo_de_aviso()->v(); }
		function getNivelDeRiesgo(){ return $this->mObj->riesgo_calificado()->v(); }
		function getDocumento(){ return $this->mObj->documento_relacionado()->v(); }
		function getTipoDeDocto(){ return $this->mObj->tipo_de_documento()->v(); }
		function getHora(){ return $this->mObj->hora_de_proceso()->v(); }
		function getTercero(){ return $this->mObj->tercero_relacionado()->v(); }
		//function getInstrumento(){ return $this->mObj->()->v(); }
		function getMensajes(){ return $this->mObj->mensaje()->v(OUT_TXT); }
		
		function setConfirmaAlerta($notas = "", $fecha = false, $inmediato = false){
			if($this->mObj == null){ $this->init(); }
			$fecha	= ($fecha == false) ? fechasys() : $fecha;
			$xF		= new cFecha();
			$fecha	= $xF->getInt($fecha);
			$clave	= $this->mCodigo;
			//Actualizar la alerta 1 = OK a riesgo 0 = Descartado
			$sql = "UPDATE aml_alerts SET fecha_de_checking=$fecha, estado_en_sistema=" . SYS_CERO . ", resultado_de_checking=1, notas_de_checking=\"$notas\" WHERE clave_de_control=$clave";
			my_query($sql);
			
			//instrumento -- tipo de operacion, tercero-relacionado, $mensajes
			$xRiesgo		= new cAMLRiesgos();

			$xRiesgo->add($this->getPersonaDeOrigen(), $this->getTipoDeAlerta(), $fecha,
					$this->getNivelDeRiesgo(), $this->getDocumento() ,
					$this->getTipoDeDocto(), $this->getPersonDeDestino(), $this->getHora(), 
					false, false, $this->getTercero(), $this->getMensajes(), $inmediato);
			$this->mMessages	.= "OK\tAlerta # $clave de fecha $fecha promocionada a RIESGO\r\n";
			$this->mMessages	.= $xRiesgo->getMessages();
		}
		function setDescartaAlerta($notas = "", $fecha = false){
			$fecha	= ($fecha == false) ? fechasys() : $fecha;
			$xF		= new cFecha();
			$fecha	= $xF->getInt($fecha);
			$clave	= $this->mCodigo;
			//Actualizar la alerta
			$sql = "UPDATE aml_alerts SET fecha_de_checking=$fecha, estado_en_sistema=" . SYS_CERO . ", notas_de_checking=\"$notas\" WHERE clave_de_control=$clave";
			my_query($sql);
			$this->mMessages	.= "WARN\tAlerta # $clave descartada\r\n";
		}
		function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
		function getDescripcion(){  return $this->mObj->mensaje()->v(OUT_TXT); }
	}
	
	
	class cAMLMatrizDeRiesgo {
		private $mMessages			= "";
		function getRiesgoPorProductosServicios(){
			
		}
		function getRiesgosPorTipoClientes(){}
		function getRiesgoPorCanalesDeDistribucion(){}
		function getRiesgoGeografico(){}
	}


	class cUtileriasParaAML {
		function __construct(){
			
		}
		function setGenerarPerfilesPorActividadEconomica(){
			$xQL	= new MQL();
			$rs		= $xQL->getDataRecord("SELECT `socios_aeconomica`.* FROM `socios_aeconomica` `socios_aeconomica`");
			$xAct	= new cSocios_aeconomica();
			$msg	= "";
			foreach ($rs as $rows){
				$xAct->setData($rows);
				$xAml	= new cAMLPersonas($xAct->socio_aeconomica()->v());
				$xAml->addTransaccionalidadPorEmpresa($xAct->dependencia_ae()->v(), $xAct->monto_percibido_ae()->v(), $xAct->fecha_alta()->v());
				$msg	.= $xAml->getMessages();
			}
			return $msg;
		}
		function setActualizarNivelDeRiesgo($actualizar = false){
			$xQL	= new MQL();
			$xLi	= new cSQLListas();
			$xLog	= new cCoreLog();
			//$xLi->getInicialDeCuentas()
			
			$rs		= $xQL->getDataRecord($xLi->getInicialDePersonas());
			foreach ($rs as $rows){
				$persona	= $rows["codigo"];
				$xAml		= new cAMLPersonas($persona);
				$xAml->init($persona, $rows);
				$riesgo			= $xAml->setAnalizarNivelDeRiesgo();
				$NivelActual	= $xAml->getOPersona()->getNivelDeRiesgo();
				if($riesgo != $NivelActual){
					if($riesgo >= SYS_RIESGO_MEDIO){ $xLog->add($xAml->getMessages(), $xLog->DEVELOPER);	}
					if($actualizar == true){ $xAml->getOPersona()->setActualizarNivelDeRiesgo($riesgo, $xAml->getMessages());}
					$xLog->add($xAml->getOPersona()->getMessages(), $xLog->DEVELOPER);
				}
			}
			return $xLog->getMessages();
		}
	}
?>