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
	//include_once("core.aml.utils.inc.php");
	@include_once("../libs/Encoding.php");
	
	
class cAML {
	protected $mMessages				= "";
	protected $mForceAvisos				= false;
	protected $mForceRegRiesgo			= false;
	protected $mTipoDeDocto				= false;
	
	function __construct(){ $this->mTipoDeDocto = iDE_RECIBO; }
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
		$xLog				= new cCoreLog();
		$xSuc				= new cSucursal();
		$xF					= new cFecha();
		$xAl				= new cAml_alerts();
		$xRisk				= new cAMLCatalogoDeRiesgos($TipoDeAlerta); $xRisk->init();
		
		
		$hora				= ( setNoMenorQueCero($hora) <= 0) ? date("Hi") : $hora;
		$fecha				= ($fecha == false) ? fechasys() : $fecha;
		$documento			= ($documento == false) ? DEFAULT_RECIBO : $documento;
		$tipo_de_docto		= ($tipo_de_docto == false) ? $this->mTipoDeDocto : $tipo_de_docto;
		$this->mTipoDeDocto	= $tipo_de_docto;
		$resultado			= true;
		$xSuc->init();
		$xQL				= new MQL();
		$idnumerico			= $xSuc->getClaveNumerica();
		
		
		$riesgo				= $xRisk->getValorPonderado();// $xRsk->valor_ponderado()->v();
		$nombreRies			= $xRisk->getNombre();
		$claseRies			= $xRisk->getTipoDeRiesgo();
		$valorDeterminado	= ($valorDeterminado == 0) ? $xRisk->getValorPonderado(): $valorDeterminado;
		$fecha				= $xF->getInt($fecha); //La fecha se maneja en Int por estupidez mia
		//No deb existir la misma alerta con el mismo documento, el mismo dia
		$DD					= $xQL->getDataRow("SELECT   COUNT(*) AS `items` FROM `aml_alerts` WHERE ( `aml_alerts`.`tipo_de_aviso` = $TipoDeAlerta ) 
								AND ( `aml_alerts`.`fecha_de_origen` = $fecha ) AND ( `aml_alerts`.`documento_relacionado` = $documento ) AND (`aml_alerts`.`persona_de_origen`=$PersonaDeOrigen) ");
		$items				= (isset($DD["items"])) ? setNoMenorQueCero($DD["items"]) : 0;
		if($items <= 0){

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
			$res		= $xAl->query()->insert()->save();
			if( $res === false ){
				$xLog->add("ERROR\tNo se registro la Alerta con ID $idnumerico\r\n");
				$resultado			= false;
			}
			$nombre_riesgo	= $xRisk->getNombre();
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
				$xLog->add("ERROR\tNo existe la persona de envio, el usuario $userP\r\n");
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
			$xFmt				= new cFormato(800);
			$xFmt->setUsuario($PersonaDeDestino);
			$xFmt->setPersona($PersonaDeOrigen);
			$xFmt->setProcesarVars($arrV);
			
			$txtMail			= $xFmt->get();
			if($valorDeterminado >= SYS_RIESGO_MEDIO OR $this->mForceAvisos == true){
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
		} else {
			$xLog->add("WARN\tYa existe la alerta del Documento $documento en la Fecha $fecha del Tipo $TipoDeAlerta\r\n", $xLog->DEVELOPER);
			$resultado			= true;
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
			$res			= $this->sendAlerts($PersonaDeDestino, getOficialAML(), $motivo, $mensaje, $documento, $fecha, false, 0, $tipo_de_docto , $persona);
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
	
	private $mPaisDeOrigen		= "";
	private $mEsVigilado		= null;
	private $mNacionalidad		= "";
	private $mDataBusqueda		= array();
	private $mURLConsulta		= "";
	public $TIPO_PEPS			= "PEPS";
	public $TIPO_BLOQ			= "BLOQUEADOS";
	public $RIESGO_BAJO			= 10;
	
	function __construct($persona){ $this->mClaveDePersona		= $persona;	}
	function setForceAlerts() { $this->mForceAvisos = true; }
	function init($clave_de_persona = false, $DatosHeredados = false){
		$clave_de_persona		= ($clave_de_persona == false) ? $this->mClaveDePersona : $clave_de_persona;
		$this->mClaveDePersona	= $clave_de_persona;
		$xSoc					= new cSocio($clave_de_persona);
		$xSoc->init($DatosHeredados);
		$this->mPaisDeOrigen	= $xSoc->getPaisDeOrigen();
		$this->mNacionalidad	= $xSoc->getNacionalidad();
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
			$PersonaDeDestino	= getOficialAML();
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
	function setGuardarPerfilTransaccional($tipo, $pais, $monto, $numero, $observaciones, $fecha = false, $origen = "", $destino = ""){
		$fecha	= ($fecha == false) ? fechasys(): $fecha;
		$xPT	= new cPersonas_perfil_transaccional();
		$xTT	= new cPersonas_perfil_transaccional_tipos();
		$xF		= new cFecha();
		$ql		= new MQL();
		$pais	= strtoupper($pais);
		$origen	= setCadenaVal($origen);
		$destino= setCadenaVal($destino);
		
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
		$xPT->recurso_origen($origen);
		$xPT->recurso_aplicacion($destino);
		
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
		$PersonaDeDestino	= getOficialAML();
					
		$q			= $mql->query()->select(); $q->set($sql);
		$data		= $q->exec();
		$doctos		= array();
		$msg		= "";
		
		foreach ($data as $campos){
			$v		= $campos[ $mql->tipo_de_documento()->get() ];
			$doctos[$v]		= $v;
			//$this->mMessages	.= "Agregar " . $doctos[$mql->tipo_de_documento()->get()]	 . "\r\n";
		}

		if(is_array($mems)){
			foreach ($mems as $clave => $valor){
				$tdoctos->setData( $tdoctos->query()->initByID($valor) );
				//var_dump($doctos[$valor]);
				if( !isset( $doctos[$valor] ) ){	
					$msg	.= "ERROR\t" . $this->mClaveDePersona . "\tDocumento ($valor) " . $tdoctos->nombre_del_documento()->v() . " NO encontrado\r\n" ;
				} else {
					$msg	.= "OK\t" . $this->mClaveDePersona . "\tDocumento " . $tdoctos->nombre_del_documento()->v() . " encontrado\r\n" ;
				}
			}
		}
		//Verificar domicilios
		$dv					= $xSoc->getODomicilio();
		if($dv == null){
			$msg	.= "ERROR\t" . $this->mClaveDePersona . "\tNo existen datos de la Vivienda\r\n" ;
		} else {
			if( $dv->isInit() == false ){
				$msg	.= "ERROR\t" . $this->mClaveDePersona . "\tNo existen datos de la Vivienda\r\n" ;
			}
		}
		//Verificar Actividad Economica
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
			//$xAml	= new cAML();
			//$xAml->setForceAlerts(true);
			//$xAml->sendAlerts(getUsuarioActual(), $PersonaDeDestino, $TipoDeAlerta, $msg);
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
		//Validar documentos Vencidos
		$xQL		= new MQL();
		$persona	= $this->mClaveDePersona;
		$sql		= "SELECT `personas_documentacion`.`clave_de_control`,`personas_documentacion`.`clave_de_persona`,`personas_documentacion`.`fecha_de_carga`,
         `personas_documentacion`.`fecha_de_verificacion`,`personas_documentacion`.`estado_en_sistema`, `personas_documentacion`.`estatus`, `personas_documentacion_tipos`.`vigencia_dias`
			FROM     `personas_documentacion` INNER JOIN `personas_documentacion_tipos`  ON `personas_documentacion`.`tipo_de_documento` = `personas_documentacion_tipos`.`clave_de_control` 
			WHERE    ( `personas_documentacion`.`clave_de_persona` = " . $persona . " )";
		$rs				= $xQL->getDataRecord($sql);
		$xLog			= new cCoreLog();
		$xCO			= new cPersonas_documentacion();
		$xF				= new cFecha();
		$markT			= (AML_KYC_DIAS_PARA_REVISAR_DOCTOS * SYS_FACTOR_DIAS);
		$FechaVer		= $xF->getInt($fecha_de_verificacion);
		foreach ($rs as $rw){
			$limite		= $rw["fecha_de_carga"]+$markT;
			$iddocto	= $rw["clave_de_control"];
			$fechabaja	= $rw["fecha_de_carga"] + ($rw["vigencia_dias"]* SYS_FACTOR_DIAS);
			//Validar documentos sin Verificar
			if($rw["fecha_de_verificacion"] <= 0){
				//$xLog->add("WARN\t")
				if($limite < $FechaVer){
					$xLog->add("ERROR\t$persona\tDocumento $iddocto no ha sido verificado " . $xF->getFechaByInt($limite) . "\r\n");
				}
			}
			if($fechabaja < $FechaVer){
				$xLog->add("ERROR\t$persona\tDocumento $iddocto ha sido dado de baja " . $xF->getFechaByInt($limite) . "\r\n");
			}
		}
		$rs				= null;
		$this->mMessages.= $xLog->getMessages();
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
		$sql				= $xQL->getAMLAcumuladoOperacionesRT($persona, $fecha_final, $moneda, $tipo );
		
		$datos				= $ql->getDataRow($sql);
		
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
	function setVerificarPerfilTransaccional($fecha = false, $generar_avisos = false){
		$xLi			= new cSQLListas();
		$xQL			= new MQL();
		$xLog			= new cCoreLog();
		$xPerf			= new cAMLPersonasPerfilTransaccional($this->mClaveDePersona);
		$xF				= new cFecha();
		$fecha			= ($fecha == false) ? fechasys() : $fecha;
		
		$persona		= $this->getCodigoDePersona();
		$sql			= $xLi->getAMLAcumuladoOperacionesRT($this->mClaveDePersona, $fecha);
		$datos			= $xQL->getDataRecord($sql);
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
				$xLog->add("ERROR\t$tipo|$moneda\tNo tiene Perfil para operar con la Moneda $moneda \r\n", $xLog->DEVELOPER);
				$xLog->add("REPORTE\tLa persona no tiene perfil para operar en la Moneda $moneda, Operacion $tipo\r\n");
				$excedido			= true;					
			}
			if($monto > $pmonto){
				$xLog->add("ERROR\t$tipo|$moneda\tMonto excedido por ($montoDiff), Perfil: $ $pmonto, Operaciones: $ $monto\r\n", $xLog->DEVELOPER);
				$xLog->add("REPORTE\tEsta operacion excede en monto($monto) por $montoDiff, segun su perfil $pmonto \r\n");
				$excedido			= true;
			} else {
				$xLog->add("OK\t$tipo|$moneda\tMonto Normal, Perfil: $ $pmonto, Operaciones: $ $monto\r\n");
			}
			if($numero > $pnumero){
				$ops_exc			= $numero - $pnumero;
				$xLog->add("ERROR\t$tipo|$moneda\tNumero de Operaciones excedidas por $ops_exc, su perfil $pnumero, realiza $numero\r\n", $xLog->DEVELOPER);
				$xLog->add("REPORTE\tEsta operacion excede en Numero($numero) por $ops_exc, segun su perfil $pnumero \r\n");
				$excedido			= true;
			} else {
				$xLog->add("OK\t$tipo|$moneda\tNumero Normal, su perfil $pnumero, realiza $numero\r\n", $xLog->DEVELOPER);
			}
			$mes_activo				= $mes;
			if($tipo == TESORERIA_COBRO_EFECTIVO){  $suma_efectivo	= $monto; }
		}
		$xLog->add($xPerf->getMessages(OUT_TXT), $xLog->DEVELOPER);
		
		//$this->mMessages			.= "OK\t \r\n";
		//$efectivo		= TESORERIA_COBRO_EFECTIVO . "-" . AML_CLAVE_MONEDA_LOCAL; //efectivo nacional
		//verificar pagos mayores a 500 USD
		//verificar pagos
		if($excedido == true){
			
			if($this->mForceAvisos == true OR $generar_avisos == true){
				
				$xAml				= new cAML();
				$xAml->setForceAlerts();
				$res				= $xAml->sendAlerts($persona, getOficialAML(), AML_DEBITOS_MAYORES_AL_PERFIL, $xLog->getMessages(), $recibo, $fecha, false, 0, iDE_RECIBO);
				if($res === false){ 
					$xLog->add("ERROR\tError al enviar alertas\r\n", $xLog->DEVELOPER);
				}
				$xLog->add($xAml->getMessages(), $xLog->DEVELOPER);
			} else {
				$xLog->add("WARN\tNo se enviaron alertas\r\n", $xLog->DEVELOPER);
			}
		}
		$this->mMessages	.= $xLog->getMessages();
		return $excedido;
	}
	function getBuscarEnListaNegra($nombre = "", $primerapellido = "", $segundoapellido = ""){
		$this->init();
		$AlterApp1			= "";
		$AlterApp2			= "";
		$AlterNom			= "";
		$xRuls				= new cReglaDeNegocio();
		$useGWS				= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_AML_GWS_ACTIVO);
		if($this->mClaveDePersona > DEFAULT_SOCIO){
			$AlterApp1		= $this->getOPersona()->getApellidoPaterno();
			$AlterApp2		= $this->getOPersona()->getApellidoMaterno();
			$AlterNom		= $this->getOPersona()->getNombre();
		}
		$nombre				= ($nombre == "") ? $AlterNom : $nombre;
		$primerapellido		= ($primerapellido == "") ? $AlterApp1 : $primerapellido;
		$segundoapellido	= ($segundoapellido == "") ? $AlterApp2 : $segundoapellido;
		$xProv				= new cAMLListasProveedores();
		$result				= $xProv->getConsultaInterna($nombre, $primerapellido, $segundoapellido, $this->mClaveDePersona);
		if($useGWS == true AND $result == false){
			$result			= $xProv->getConsultaGWS($nombre, $primerapellido, $segundoapellido, $this->mClaveDePersona);
		}
		return $result;
	}
	function getBuscarEnListaPEP($nombre = "", $primerapellido = "", $segundoapellido = ""){
		$this->init();
		$AlterApp1			= "";
		$AlterApp2			= "";
		$AlterNom			= "";
		$xRuls				= new cReglaDeNegocio();
		$useGWS				= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_AML_GWS_ACTIVO);
		if($this->mClaveDePersona > DEFAULT_SOCIO){
			$AlterApp1		= $this->getOPersona()->getApellidoPaterno();
			$AlterApp2		= $this->getOPersona()->getApellidoMaterno();
			$AlterNom		= $this->getOPersona()->getNombre();
		}
		$xProv				= new cAMLListasProveedores();
		$nombre				= ($nombre == "") ? $AlterNom : $nombre;
		$primerapellido		= ($primerapellido == "") ? $AlterApp1 : $primerapellido;
		$segundoapellido	= ($segundoapellido == "") ? $AlterApp2 : $segundoapellido;
		$result				= $xProv->getConsultaInterna($nombre, $primerapellido, $segundoapellido, $this->mClaveDePersona, true);
		if($useGWS == true AND $result == false){
			$result			= $xProv->getConsultaGWS($nombre, $primerapellido, $segundoapellido, $this->mClaveDePersona);
		}
		return $result;
	}
		
	function getReporteConsultaListaNegra(){ return $this->mURLConsulta; }
	function getDatosBuscados(){ return $this->mDataBusqueda; }
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
	function getPaisDeOrigen(){ return strtoupper($this->mPaisDeOrigen); }
	function getNacionalidad(){ return $this->mNacionalidad; }
	function setAnalizarNivelDeRiesgo($reportar = false){
		if($this->mOSocio == null){ $this->getOPersona(); }
		$riesgo				= 1;
		$factores			= 0;
		$riesgoMantenido	= 0;
		$xLog				= new cCoreLog();
		$xAML				= new cAML();
		
		if(MODULO_AML_ACTIVADO == true){
			$xOmit			= new cAMLPersonasOmisiones();
			$xMat			= new cAMLMatrizDeRiesgo();
			$omitido		= $xOmit->initByPersona($this->getCodigoDePersona());
			
			//if($xOmit->initByPersona($this->getCodigoDePersona()) == true){
				//La persona está omitida
			//	$xLog->add("WARN\tLa persona esta en lista de excepcion\r\n");
				//$riesgo		= $this->RIESGO_BAJO;		
			//	$riesgo		= $this->RIESGO_BAJO;
			//} else {
				//revisar matrices
				//Riesgo por ser extranjero
				//$xMat->initByTopico($xMat->P_RIESGO_EXTRANJERO);
				
				/*if($this->mOSocio->getEsExtranjero() == true){
					//Si NO tiene el pais, elevar a ALTO
					if($this->getNacionalidad() == EACP_CLAVE_DE_PAIS){
						$factores++;
						$riesgo += $xMat->getNivelRiesgo(); //riesgo medio
						
						$xLog->add("WARN\tRiesgo a $riesgo por ser Extranjero con Nacionalidad Desconocida\r\n");
					} else {
						$xMat->initByTopico($xMat->P_RIESGO_ES_NACIONAL);
						$factores++;
						$riesgo += $xMat->getNivelRiesgo();
						$xLog->add("WARN\tRiesgo a $riesgo por ser Extranjero\r\n");
					}
				}*/
				//Riesgo por pais de origen
				/*if($this->getPaisDeOrigen() != EACP_CLAVE_DE_PAIS){
					$xPais		= new cDomiciliosPaises($this->getPaisDeOrigen());
					if($xPais->init() == true){
						$factores++;
						$riesgo		+= $xPais->getRiesgoAMLAsociado();
						$xLog->add("WARN\tRiesgo de Pais clave " . $this->getPaisDeOrigen() . " a $riesgo\r\n");
					}
				}*/
				if($this->mOSocio->getEsExtranjero() == true){
					$xMat->initByTopico($xMat->P_RIESGO_EXTRANJERO);
					$xLog->add("REPORTE\tRiesgo a $riesgo por ser extranjero\r\n");
					$factores++;
					$riesgo 			+= $xMat->getNivelRiesgo();
					$riesgoMantenido	= $xMat->getMantenerRiesgo($xMat->getNivelRiesgo(), $riesgoMantenido);
					if($xMat->getEsFinalizador() == true AND $reportar == true){
						$xAML->setForceAlerts();
						$xAML->sendAlerts($this->mClaveDePersona, getOficialAML(), $xMat->getTipoRiesgo(), $xLog->getMessages());
					}
				} else {
					$xMat->initByTopico($xMat->P_RIESGO_ES_NACIONAL);
					$factores++;
					$riesgo 			+= $xMat->getNivelRiesgo();
					$riesgoMantenido	= $xMat->getMantenerRiesgo($xMat->getNivelRiesgo(),$riesgoMantenido);
					$xLog->add("REPORTE\tRiesgo a $riesgo por ser Nacional con Nacionalidad Desconocida\r\n");
					if($xMat->getEsFinalizador() == true AND $reportar == true){
						$xAML->setForceAlerts();
						$xAML->sendAlerts($this->mClaveDePersona, getOficialAML(), $xMat->getTipoRiesgo(), $xLog->getMessages());
					}
				}
				
				//riesgo por Nacionalidad
				if($this->getNacionalidad() != EACP_CLAVE_DE_PAIS){
					
					$xPais	= new cDomiciliosPaises($this->getNacionalidad());
					if($xPais->init() == true){
						$xMat->initByTopico($xMat->P_RIESGO_PAIS);
						$factores++;
						$riesgo				+= $xPais->getRiesgoAMLAsociado();
						$xLog->add("REPORTE\tRiesgo de Nacionalidad del pais clave " . $this->getNacionalidad() . " a $riesgo\r\n");
						//Nota
						//if($xPais->getRiesgoAMLAsociado() == SYS_RIESGO_ALTO){
							$riesgoMantenido		= $xMat->getMantenerRiesgo($xPais->getRiesgoAMLAsociado(), $riesgoMantenido);
							$xLog->add("REPORTE\tRiesgo Mantenido de Nacionalidad del pais clave " . $this->getNacionalidad() . " a $riesgoMantenido\r\n");
						//}
						if($xMat->getEsFinalizador() == true AND $reportar == true){
							$xAML->setForceAlerts();
							$xAML->sendAlerts($this->mClaveDePersona, getOficialAML(), $xMat->getTipoRiesgo(), $xLog->getMessages());
						}
					}
				}			
				//Trabajar en localidades riesgosas, paises riesgosas

				//si es persona moral, obtener datos del representante
				//Riesgo PM Si no hay representante
				if($this->mOSocio->getEsPersonaFisica() == false){
					$xRels			= $this->mOSocio->getORepresentanteLegal();
					if($xRels == null){
						$xMat->initByTopico($xMat->P_RIESGO_PM_NO_REP);
						$factores++; 
						$riesgo 				+= $xMat->getNivelRiesgo();
						$riesgoMantenido		= $xMat->getMantenerRiesgo($xMat->getNivelRiesgo(), $riesgoMantenido);
						$xLog->add("REPORTE\tRiesgo a $riesgo  por no tener Representante Legal\r\n");
						if($xMat->getEsFinalizador() == true AND $reportar == true){
							$xAML->setForceAlerts();
							$xAML->sendAlerts($this->mClaveDePersona, getOficialAML(), $xMat->getTipoRiesgo(), $xLog->getMessages());
						}
					}
				}
				//validar perfil transaccional
				$xPer	= new cAMLPersonasPerfilTransaccional($this->mOSocio->getCodigo());
				if($xPer->getNumeroEntradas() <= 0){
					$xMat->initByTopico($xMat->P_RIESGO_SIN_PERFIL_T);
					$factores++;
					$riesgo 				+= $xMat->getNivelRiesgo();
					$riesgoMantenido		= $xMat->getMantenerRiesgo($xMat->getNivelRiesgo(), $riesgoMantenido);
					$xLog->add("REPORTE\tRiesgo a $riesgo  por no tener perfil trasaccional\r\n");
					if($xMat->getEsFinalizador() == true AND $reportar == true){
						$xAML->setForceAlerts();
						$xAML->sendAlerts($this->mClaveDePersona, getOficialAML(), $xMat->getTipoRiesgo(), $xLog->getMessages());
					}
				}
				//revisar su actividad economica
				$OAE	= $this->mOSocio->getOActividadEconomica();
				if($OAE == null){
					$xMat->initByTopico($xMat->P_RIESGO_SIN_ACTIVIDAD);
					$factores++;
					$riesgo 				+= $xMat->getNivelRiesgo();
					$riesgoMantenido		= $xMat->getMantenerRiesgo($xMat->getNivelRiesgo(), $riesgoMantenido);
					$xLog->add("WARN\tRiesgo a $riesgo  por no tener Actividad Economica (Medio)\r\n");
					if($xMat->getEsFinalizador() == true AND $reportar == true){
						$xAML->setForceAlerts();
						$xAML->sendAlerts($this->mClaveDePersona, getOficialAML(), $xMat->getTipoRiesgo(), $xLog->getMessages());
					}
				}  else {
					$OAE->getEsRiesgosa();
					$factores++;
					$xMat->initByTopico($xMat->P_RIESGO_ACTIVIDAD);
					$rae				= $OAE->getRiesgoAMLAsociado();
					$riesgo 			+= $rae;
					$riesgoMantenido	= $xMat->getMantenerRiesgo($rae, $riesgoMantenido);
					$claveAE			= $OAE->getClaveDeActividad();
					$xLog->add("REPORTE\tRiesgo a $riesgo  en Actividad Economica con clave $claveAE\r\n");
					$xLog->add($OAE->getMessages(), $xLog->DEVELOPER);
					//checar si su domicilio no esta en paises
					//100% de actividades Riesgosas
					/*if($OAE->getRiesgoAMLAsociado() >= SYS_RIESGO_ALTO){
						$factores					= 1;
						$riesgoMantenido			= $OAE->getRiesgoAMLAsociado();
					}*/
					//actualiza el riesgo de la persona por pep
					if($xMat->getEsFinalizador() == true AND $reportar == true){
						$xAML->setForceAlerts();
						$xAML->sendAlerts($this->mClaveDePersona, getOficialAML(), $xMat->getTipoRiesgo(), $xLog->getMessages(),false, false, false, $riesgoMantenido);
					}
					if($OAE->getGeneraPEP() == true){
						$xOCat		= new cPersonasCatalogoOtrosDatos();
						$this->mOSocio->addOtrosParametros($xOCat->AML_PEP_PRINCIPAL, 1);
						if($xMat->initByTopico($xMat->P_RIESGO_ES_PEP) == true){
							if($xMat->getEsFinalizador() == true AND $reportar == true){
								$xAML->setForceAlerts();
								$xAML->sendAlerts($this->mClaveDePersona, getOficialAML(), $xMat->getTipoRiesgo(), $xLog->getMessages(),false, false, false, $riesgoMantenido);
							}
						}
					}

				}
				//revisar su actividad economica idlocalidad o domicilio
	
				
				$ODom	= $this->mOSocio->getODomicilio();
				if($ODom == null){
					$factores++;
					$xMat->initByTopico($xMat->P_RIESGO_SIN_DOM);
					$riesgo += $xMat->getNivelRiesgo();
					$xLog->add("REPORTE\tRiesgo a $riesgo por no tener domicilio\r\n");
					if($xMat->getEsFinalizador() == true AND $reportar == true){
						$xAML->setForceAlerts();
						$xAML->sendAlerts($this->mClaveDePersona, getOficialAML(), $xMat->getTipoRiesgo(), $xLog->getMessages());
					}
				}  else {
					$xMat->initByTopico($xMat->P_RIESGO_DOM_PAIS);
					$xViv	= new cPersonasVivienda($this->getCodigoDePersona());
					$xViv->setValidarDomicilios(MMOD_AML);				
					$factores++;
					//es pais riesgoso
					//$pais	= $ODom->getClaveDePais();
					//if($pais != EACP_CLAVE_DE_PAIS){
						//$xPais				= new cDomiciliosPaises($pais);
						//$xPais->init();
					$riesgoPais			= $xViv->getRiesgoAML();
					$riesgo				+= $riesgoPais;
					$riesgoMantenido	= $xMat->getMantenerRiesgo($riesgoPais, $riesgoMantenido);
						
						//$riesgo				+= $riesgoPais;//setLog($riesgoPais);
						/*if($riesgoPais == SYS_RIESGO_ALTO){
							$riesgo			= $riesgoPais;
							$factores		= 1;
						}*/
						
					$xLog->add("REPORTE\tRiesgo Pais a $riesgo agregado $riesgoPais\r\n");
					
						
					//} else {
						//$riesgo				+= SYS_RIESGO_BAJO;
						//$this->mMessages	.= "WARN\tRiesgo a $riesgo agregado del pais $pais\r\n";
					//}
					$xLog->add($xViv->getMessages(), $xLog->DEVELOPER);
					if($xMat->getEsFinalizador() == true AND $reportar == true){
						$xAML->setForceAlerts();
						$xAML->sendAlerts($this->mClaveDePersona, getOficialAML(), $xMat->getTipoRiesgo(), $xLog->getMessages(), false, false, false, $riesgoMantenido);
						$xLog->add($xAML->getMessages(), $xLog->DEVELOPER);
						
					}
				}
				
				$riesgo				= ($riesgo /$factores);
				if($riesgoMantenido > 0){
					$riesgo			= $riesgoMantenido;
					$factores		= 1;
					$xLog->add("WARN\tRiesgo Mantenido a $riesgoMantenido \r\n", $xLog->DEVELOPER);
				}
				$xRsk				= new cRiesgos();
				$riesgo				= $xRsk->getNivelarR($riesgo);
				/*if($riesgo >= (SYS_RIESGO_MEDIO + 1) ){
					$xLog->add("WARN\tRiesgo a ALTO por $riesgo con Factores $factores \r\n");
					$riesgo		= SYS_RIESGO_ALTO;
				} else if($riesgo > $this->RIESGO_BAJO AND $riesgo <= SYS_RIESGO_MEDIO){
					$xLog->add("WARN\tRiesgo a MEDIO por $riesgo con Factores $factores \r\n");
					$riesgo		= SYS_RIESGO_MEDIO;
				} else {
					$xLog->add("WARN\tRiesgo a BAJO por $riesgo con Factores $factores \r\n");
					$riesgo		= $this->RIESGO_BAJO;
				}*/
				//$this->mMessages	.= "WARN\tRiesgo a $riesgo con Factores $factores \r\n";
				//revisar los productos con riesgo
			//} //end omisiones
		} else {
			//El Modulo no esta activado
			$xLog->add("WARN\tRiesgo a BAJO porque el MODULO No esta ACTIVO\r\n", $xLog->DEVELOPER);
			$riesgo		= $this->RIESGO_BAJO;
		}
		
		$this->mMessages	.= $xLog->getMessages();
		
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
		//setLog($this->mMessages);
	}
	function getEsPersonaVigilada(){
		if($this->mEsVigilado == null){
		//verificar SQL
		$ql	= new MQL();
		//aml_personas_vigiladas es
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
		//Buscar en Lista Negra
		
		//Verificar en Personas no vigiladas
		$xOmi	= new cAMLPersonasOmisiones();
		if($xOmi->initByPersona($this->mClaveDePersona) == true){
			$this->mEsVigilado	= ($xOmi->omitir() == true) ? false : $this->mEsVigilado;
			$this->mMessages	.= "OK\tEsta persona " . $this->mClaveDePersona ." es NO VIGILADA por OMISION\r\n";
		}
		return $this->mEsVigilado;
	}
	function getEsPersonaOmitida(){
		$omitir	= false;
		$xOmi	= new cAMLPersonasOmisiones();
		if($xOmi->initByPersona($this->getCodigoDePersona()) == true){
			$omitir	= $xOmi->omitir();
		}
		return $omitir;
	}
	function addToListaNegra($motivo, $riesgo, $observaciones = "", $vencimiento = false){
		$xPL	= new cAMLListaNegraInterna(false, $this->mClaveDePersona);
		$xPL->add($this->mClaveDePersona, $motivo, $observaciones, $riesgo, $vencimiento);
	}
}
class cAMLPersonasOmisiones {
	private $mMessages			= "";
	private $mClaveDePersona	= false;
	private $mClave				= false;
	private $mSoloVigentes		= true;
	private $mEstados			= false;
	private $mInit				= false;
	private $mObj				= null;
	private $mEstado			= false;
	private $mFechaVencimiento	= false;
	private $mIDCache			= "";
	function __construct($clave	= false, $persona = false){
		$this->mClave			= setNoMenorQueCero($clave);
		$this->mClaveDePersona	= setNoMenorQueCero($persona);
		$this->setIDCache($this->mClave);
	}
	function setIDCache($clave){if($clave >0){ $this->mIDCache = TAML_PERSONAS_OMITIDAS . "-" . $this->mClave; } }
	function setCleanCache($id = ""){ 
		$xCache = new cCache();
		if($this->mIDCache !== ""){ $xCache->clean($this->mIDCache); }
		if($this->mClave >0){
			$xCache->clean(TAML_PERSONAS_OMITIDAS . "-". $this->mClave);
		}
		$xCache->clean(TAML_PERSONAS_OMITIDAS . "-por-persona-". $this->mClaveDePersona);
	}		
	function initByPersona($persona = false){
		$persona	= setNoMenorQueCero($persona);
		$xCache		= new cCache();
		$xF			= new cFecha();
		$fecha		= $xF->get();
		$this->mClaveDePersona	= $persona;
		$xD			= $xCache->get(TAML_PERSONAS_OMITIDAS . "-por-persona-$persona");
		if(!is_array($xD)){
			$sql	= "SELECT `aml_personas_descartadas`.* FROM `aml_personas_descartadas` 
					WHERE (`aml_personas_descartadas`.`clave_de_persona` = $persona) 
					AND (`aml_personas_descartadas`.`fecha_de_vencimiento` >'$fecha') LIMIT 0,1";
			$xQL	= new MQL();
			$xD		= $xQL->getDataRow($sql);
			$xCache->set(TAML_PERSONAS_OMITIDAS . "-por-persona-$persona", $xD);
		}
		return $this->init($xD);
	}
	function getObj(){ if($this->mObj == null){ $this->init(); } return $this->mObj; }
	function init($data = false){
		$xQL	= new MQL();
		$xF		= new cFecha();
		$fecha	= $xF->get();
		$sql	= "SELECT  * FROM `aml_personas_descartadas` WHERE (`aml_personas_descartadas`.`idaml_personas_descartadas` =" . $this->mClave . ") LIMIT 0,1";
		$data	= (is_array($data)) ? $data : $xQL->getDataRow($sql);
		if(isset($data["idaml_personas_descartadas"])){
			$this->mObj				= new cAml_personas_descartadas();
			$this->mObj->setData($data);
			$this->mClave			= $this->mObj->idaml_personas_descartadas()->v();
			$this->mClaveDePersona 	= $this->mObj->clave_de_persona()->v();
			$this->mFechaVencimiento= $this->mObj->fecha_de_vencimiento()->v();
			$this->mInit			= true;
			$this->mEstado			= true;
			$this->setIDCache($this->mClave);
		}
		return $this->mInit;	
	}
	function omitir(){ return $this->mEstado; }
	function add($notas, $persona = false, $motivo = 1){
		$persona	= setNoMenorQueCero($persona);
		$motivo		= setNoMenorQueCero($motivo);
		$xF			= new cFecha();
		$obj		= new cAml_personas_descartadas();
		$res		= false;
		//======= vencer las demas
		$persona	= ($persona <= DEFAULT_SOCIO) ? $this->mClaveDePersona : $persona;
		if($persona > DEFAULT_SOCIO){
			$this->mClaveDePersona	= $persona;
			$agregar				= true;
			/*/if($this->initByPersona($persona) == true){
				if($this->getEsVigente() == true){
					$this->mMessages.= "WARN\tNo se agrega porque es vigente\r\n";
					$agregar		= false;
				}
			}*/
			//if($agregar == true){
				$vence		= $xF->getFechaMaximaOperativa();
				$obj->clave_de_motivo($motivo);
				$obj->clave_de_oficial(getUsuarioActual());
				$obj->clave_de_persona($persona);
				$obj->descripcion_del_motivo($notas);
				$obj->fecha_de_captura( $xF->get() );
				$obj->fecha_de_vencimiento( $vence );
				
				$obj->idaml_personas_descartadas("NULL");
				$res	= $obj->query()->insert()->save();
			
			//}
		}
		
		return ($res == false) ? false : true;
	}
	
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function setCancelar($motivo = 1){
		$xQL	= new MQL();
		$xF		= new cFecha();
		$fecha	= $xF->get();			
		$sql	= "UPDATE `aml_personas_descartadas` SET `fecha_de_vencimiento` ='$fecha' WHERE `clave_de_persona`=" . $this->mClaveDePersona . " AND `clave_de_motivo`=$motivo ";
		$xQL->setRawQuery($sql);
		$this->setCleanCache();
	}
	function getEsVigente($fecha =false){
		$xF		= new cFecha();
		return ($xF->getInt($fecha) < $xF->getInt($this->mFechaVencimiento)) ? true : false;
	}
}
class cAMLPersonasPerfilTransaccional {
	private $mDatos 				= array();
	private $mMessages				= "";
	private $mClaveDePersona		= false;
	private $mIngresosMensuales		= 0;
	private $mEgresosMensuales		= 0;
	private $mAPerfil				= array();
	private $mItems					= 0;
	
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
		$this->mAPerfil[$clave][SYS_NUMERO]	+= AML_TOLERA_OPS_NUM_PERFIL;
		$this->mAPerfil[$clave][SYS_MONTO]	+= AML_TOLERA_OPS_MTO_PERFIL;
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
			$this->mAPerfil[$clave][SYS_NUMERO]		= 0;
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
	private $mTipoDeReporte		= "C"; //Calificado
	private $mPaisDeOrigen		= EACP_CLAVE_DE_PAIS;
	private $mNivelDeRiesgo		= 0;
	private $mFactores			= 0;
	function __construct(){}
	
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function analizarOperacion($persona, $monto_operado, $moneda, $tipo_de_pago, $fecha = false, $recibo = false, $perfil = false){
		//Esta funcion analiza por Moneda y evento los riesgos que conlleva
		$moneda	= strtoupper($moneda);
		
		$xCache	= new cCache();
		$xLog	= new cCoreLog();
		$xRisk	= new cAMLCatalogoDeRiesgos(AML_CLAVE_RIESGO_OPS_INDIVIDUALES);
		$raised	= false;
		$xAML	= new cAML();
		//Datos de Operaciones Mensuales
		$xAMLP	= new cAMLPersonas($persona);
		$xAMLP->init();
		
		
		$OMens				= $xAMLP->getOAcumuladoDeOperaciones($fecha, $fecha, $moneda, $tipo_de_pago);
		$monto_original		= $OMens->getMonto() + $monto_operado;
		$xLog->add("WARN\tOperaciones acumuladas por " . $OMens->getMonto() . " en la Moneda $moneda\r\n", $xLog->DEVELOPER);
		if($perfil === false){
			$xLog->add("ERROR\tNo existe el perfil tipo de pago $tipo_de_pago en Moneda $moneda \r\n", $xLog->DEVELOPER);
			//agregar perfil a cero
		} else {
			$perfil		= setNoMenorQueCero($perfil);
			if($perfil>0){
				$xTipoP	= new cAMLPersonasPerfilTransTipos($perfil);
				if($xTipoP->init() == true){
					$tipo_de_pago	= $xTipoP->getTipoDeExhibicion();
					$xLog->add("OK\tCarga del perfil $perfil  con tipo de pago $tipo_de_pago\r\n", $xLog->DEVELOPER);
				}
			}
		}
		//verificar operaciones con reglas
		if($xRisk->init() == true){
			//$risk->setData($rows);
			$reporteMoneda		=  $xRisk->getUnidadDeMedida();
			if($reporteMoneda == $moneda){
				if($raised == false){
					$reporteMoneda		= $xRisk->getUnidadDeMedida();
					$xMon				= new cMonedas($reporteMoneda);
					$valor_local		= $xMon->getValor();
					
					if($xAML->isTransaccionVigilada($tipo_de_pago) != false){
						$unidadesReportadas	= $xRisk->getUnidadesPonderadas();
						$unidadesOperadas	= 0;
						if($reporteMoneda == AML_CLAVE_MONEDA_LOCAL){
							$unidadesOperadas	= $monto_original;
						} else {
							$xLog->add("WARN\tUnidades reportadas $unidadesOperadas en Moneda $moneda con valor local de $valor_local\r\n", $xLog->DEVELOPER);
							$unidadesReportadas	= ($valor_local * $unidadesReportadas) / VALOR_ACTUAL_DOLAR;
							$unidadesOperadas	= ($monto_original * $valor_local) / VALOR_ACTUAL_DOLAR;
						}
						if($unidadesOperadas >= $unidadesReportadas ){
							$xLog->add("ERROR\tUnidades excedidas de $unidadesReportadas operados $unidadesOperadas en la Moneda $reporteMoneda\r\n");
							$raised					= true;
							$this->mTipoDeAlerta	= $xRisk->getClave();
							$this->mTipoDeReporte	= $xRisk->getFormaDeReporte();
							$this->mNivelDeRiesgo	+= $xRisk->getValorPonderado();
							$this->mFactores++;
						} else {
							$xLog->add("OK\tOperacion Normal de $unidadesOperadas no sobrepasan $unidadesReportadas\r\n", $xLog->DEVELOPER);
						}
						//Si la moneda es diferente a la Local
						if($moneda !== AML_CLAVE_MONEDA_LOCAL){
							//Validar sivienen de paises con alto riesgo
							$xP							= new cDomiciliosPaises();
							$xP->getPaisPorMoneda($reporteMoneda);
							$nombrepais					= $xP->getNombre();
							if($xP->getRiesgoAMLAsociado() >= SYS_RIESGO_MEDIO){
								//Agregar alerta por operaciones en paises de  alto y medio riesgo
								$xLog->add("REPORTE\tFondos provenientes de paises con Riesgo Elevado $nombrepais\r\n");
								//
								$xAv			= new cAML();
								$xMat			= new cAMLMatrizDeRiesgo();
								if($xMat->initByTopico($xMat->O_RIESGO_ORIGEN) == true){
									$xAv->sendAlerts($persona, getOficialAML(), $xMat->getTipoRiesgo(), $xLog->getMessages(), $recibo, $fecha, false, $xMat->getNivelRiesgo());
									$this->mFactores++;
									$this->mNivelDeRiesgo	+= $xP->getRiesgoAMLAsociado();
								}
							}
						}

					} else {
						$xLog->add("OK\tOperacion Omitida por ser tipo de pago $tipo_de_pago ($reporteMoneda )\r\n", $xLog->DEVELOPER);
					}
				}
			} else {
				$xLog->add("OK\tOperacion Omitida por ser Moneda $moneda de $reporteMoneda del Riesgo\r\n", $xLog->DEVELOPER);
			}
		}
		$this->mMessages	= $xLog->getMessages();
		$this->mMsgAlert	= $xLog->getMessages();
		return $raised;
	}
	function getTipoDeAlerta(){ return $this->mTipoDeAlerta; }
	function getTipoDeReporte(){ return $this->mTipoDeReporte; }
	function getMessageAlert(){ return $this->mMsgAlert; }
	function getPaisDeOrigen(){ return $this->mPaisDeOrigen; }
	function getNivelDeRiesgo(){ 
		$riesgo	= setNoMenorQueCero( ($this->mNivelDeRiesgo/$this->mFactores),0);
		$xRisk	= new cRiesgos();
		return $xRisk->getNivelarR($riesgo);
	}
	function getNumeroFactores(){ return $this->mFactores; }
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
	
	private $mFechaOrigen	= false;
	private $mFechaRegistro	= false;
	private $mFechaChecking	= false;
	private $mFechaEnvio	= false;
	private $mNotasChecking	= "";
	private $mNotasSistema	= "";
	private $mNotasPrev		= "";//Notas de prevencion
	private $mNotasReporte	= "";//Razones por la cual se reporta
	private $mInstrumentoF	= 0;//Instrumento Financiero
	private $mTipoDocumento	= 0;
	private $mTipoOperacion	= 0;
	private $mTipo			= false;
	private $mDocumento		= 0;
	private $mPersona		= 0;
	private $mTercero		= 0; //Tercero relacionado
	private $mOficial		= 0;//persona_de_destino 
	private $mUsuario		= 0;
	
	function __construct($id = false){
		$this->mCodigo	= setNoMenorQueCero($id);
			
	}
	function init(){
		$this->mObj		= new cAml_risk_register();
		$xF				= new cFecha();
		if($this->mCodigo > 0){
			$data			= $this->mObj->query()->initByID($this->mCodigo);
			$clave			= (isset($data["clave_de_riesgo"])) ? $data["clave_de_riesgo"] : 0; 
			if($clave > 0){
				$this->mObj->setData( $data );
				$this->mIsInit			= true;
				$this->mFechaOrigen		= $xF->getFechaByInt($this->mObj->fecha_de_reporte()->v());
				$this->mFechaChecking	= $xF->getFechaByInt($this->mObj->fecha_de_checking()->v());
				$this->mFechaRegistro	= $xF->getFechaByInt($this->mObj->fecha_de_reporte()->v());
				$this->mFechaEnvio		= $xF->getFechaByInt($this->mObj->fecha_de_envio()->v());
				$this->mNotasChecking	= $this->mObj->notas_de_checking()->v();
				$this->mNotasSistema	= $this->mObj->mensajes_del_sistema()->v();
				$this->mNotasPrev		= $this->mObj->acciones_tomadas()->v();
				$this->mNotasReporte	= $this->mObj->razones_de_reporte()->v();
				$this->mPersona			= $this->mObj->persona_relacionada()->v();
				$this->mDocumento		= $this->mObj->documento_relacionado()->v();
				$this->mTercero			= $this->mObj->tercero_relacionado()->v();
				$this->mTipo			= $this->mObj->tipo_de_riesgo()->v();
				$this->mTipoDocumento	= $this->mObj->tipo_de_documento()->v();
				$this->mTipoOperacion	= $this->mObj->tipo_de_operacion()->v();
				$this->mInstrumentoF	= $this->mObj->instrumento_financiero()->v();
				$this->mUsuario			= $this->mObj->usuario()->v();
			}
			
		}
		return $this->mIsInit;
	}
	function add($persona , $tipo, $fecha, $valor, $documento, $tipo_de_documento = false, $usuario = 
			false, $hora = false, $instrumento = false, $tipo_de_operacion = false, 
			$tercero_relacionado = DEFAULT_SOCIO, $mensajes = "", $inmediato = false){
		$hora				= ($hora == false) ? date("Hi") : $hora;
		$usuario			= ($usuario == false) ? getUsuarioActual() : $usuario;
		$tipo_de_documento	= ($tipo_de_documento == false) ? iDE_RECIBO : $tipo_de_documento;
		$instrumento		= setNoMenorQueCero($instrumento);
		$tipo_de_operacion	= setNoMenorQueCero($tipo_de_operacion);
		if( $instrumento <=0 OR $tipo_de_operacion <= 0){
			switch ($tipo_de_documento){
				case iDE_RECIBO:
					$EqOps		= new cSistemaEquivalencias(TOPERACIONES_RECIBOSTIPOS);
					$EqTes		= new cSistemaEquivalencias(TTESORERIA_TIPOS_DE_PAGO);
					
					$xRec				= new cReciboDeOperacion(false, false, $documento);
					if($xRec->init() == true){
						$instrumento		= $EqTes->get($xRec->getTipoDePago());
						$tipo_de_operacion	= $EqOps->get( $xRec->getTipoDeRecibo()  );
						if($xRec->isDivisaExtranjera() == true){
							$instrumento	= AML_OPERACIONES_CLAVE_DIVISA;
						}
						//if($tipo_de_operacion == null){ $tipo_de_operacion	= "01"; }
						//if($instrumento == null){  }
						$tipo_de_operacion	= setNoMenorQueCero($tipo_de_operacion);
						$instrumento		= setNoMenorQueCero($instrumento);
						$tipo_de_operacion	= ($tipo_de_operacion <=0) ? 9 : $tipo_de_operacion;//9= Pago de credito
						$instrumento		= ($instrumento <= 0) ? 1 : $instrumento;//1 Efectivo 
					}
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
		$xPR->oficial_de_checking(getUsuarioActual());
		$xPR->monto_total_relacionado(0);
		//cargar datos del recibo
		$xPR->instrumento_financiero($instrumento); //mejorar segun catalogo CNBV
		$xPR->tipo_de_operacion($tipo_de_operacion);
		$xPR->tercero_relacionado($tercero_relacionado);
		$xPR->mensajes_del_sistema($mensajes);
		$xPR->reporte_inmediato($inmediato);
		$ql		= $xPR->query()->insert();
		$res	= $ql->save();
		//$this->mMessages	.= $ql->getMessages(OUT_TXT);
		if($res === false){
			$this->mMessages	.= "ERROR\tMensaje a $persona NO Enviado\r\n";
		} else {
			$this->mMessages	.= "OK\tMensaje a $persona Enviado con exito\r\n";
		}
		
	}
	function agregarChecking(){
		
	}
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function getFechaEnvio(){return $this->mFechaEnvio; }
	function getFechaOrigen(){return $this->mFechaOrigen; }
	function getFechaRegistro(){return $this->mFechaRegistro; }
	function getFechaChecking(){ return $this->mFechaChecking; }
	function getNotasSistema(){return $this->mNotasSistema; }
	function getNotasChecking(){return $this->mNotasChecking; }
	function getNotasReporte(){ return $this->mNotasReporte; }
	function getNotasPrevencion(){ return $this->mNotasPrev; }
	function getUsuarioOrigen(){ return $this->mUsuario; }
	function getOficialDest(){ return $this->mOficial; }		
}
class cAMLAlertas {
	protected $mMessages= "";
	private $mCodigo	= false;
	private $mObj		= null;
	private $mIsInit	= false;
	
	private $mFechaOrigen	= false;
	private $mFechaRegistro	= false;
	private $mFechaChecking	= false;
	private $mFechaEnvio	= false;
	private $mNotasChecking	= "";
	private $mNotasSistema	= "";

	private $mInstrumentoF	= 0;//Instrumento Financiero
	private $mTipoDocumento	= 0;
	private $mTipoOperacion	= 0;
	private $mTipo			= false;
	private $mDocumento		= 0;
	private $mPersona		= 0;
	private $mTercero		= 0; //Tercero relacionado
	private $mOficial		= 0;//persona_de_destino
	private $mOficialDeChecking		= 0;//persona_de_destino
	private $mUsuario		= 0;
	private $mEsRiesgo		= false;
	private $mEstadoActual	= 0;
	private $mEnviadoRMS	= false;
	private $mFechaEnvioRMS	= 0;
	private $mTabla			= "aml_alerts";
	private $mIDCache		= "";
	
			
	function __construct($id = false){
		$this->mCodigo	= setNoMenorQueCero($id);
		$this->setIDCache($this->mCodigo);
	}
	function init(){
		$this->mObj		= new cAml_alerts();
		$xF				= new cFecha();
		$xCache			= new cCache();
		
		if($this->mCodigo > 0){
			$data			= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . $this->mTabla . "` WHERE `" . $this->mObj->getKey() . "`=". $this->mCodigo . " LIMIT 0,1");
			}
			//$this->mObj->query()->initByID($this->mCodigo);
			
			if(isset($data["clave_de_control"])){
				$this->mObj->setData( $data );
				$this->mIsInit			= true;
				$this->mCodigo			= $this->mObj->clave_de_control()->v();
				$this->mFechaOrigen		= $xF->getFechaByInt($this->mObj->fecha_de_origen()->v());
				$this->mFechaChecking	= $xF->getFechaByInt($this->mObj->fecha_de_checking()->v());
				
				$this->mFechaRegistro	= $xF->getFechaByInt($this->mObj->fecha_de_registro()->v());
				$this->mFechaEnvio		= $xF->getFechaByInt($this->mObj->fecha_de_checking()->v());
				$this->mNotasChecking	= $this->mObj->notas_de_checking()->v();
				$this->mNotasSistema	= $this->mObj->mensaje()->v();

				$this->mPersona			= $this->mObj->persona_de_origen()->v();
				$this->mDocumento		= $this->mObj->documento_relacionado()->v();
				$this->mTercero			= $this->mObj->tercero_relacionado()->v();
				$this->mTipo			= $this->mObj->tipo_de_aviso()->v();
				$this->mTipoDocumento	= $this->mObj->tipo_de_documento()->v();
				$this->mUsuario			= $this->mObj->usuario()->v();
				$this->mOficial			= $this->mObj->persona_de_destino()->v();//Persona de destino es ID de Oficial de cumplimiento
				$this->mOficialDeChecking	= $this->mObj->usuario_checking()->v();
				$this->mEsRiesgo		= ($this->mObj->resultado_de_checking()->v() <= 0) ? false : true;
				$this->mEnviadoRMS		= ($this->mObj->envio_rms()->v() > 0 ) ? true : false;
				$this->mFechaEnvioRMS	= $xF->getFechaByInt($this->mObj->envio_rms()->v());
				//$this->mTipoOperacion	= $this->mObj->tipo_de_operacion()->v();
				
				$this->setIDCache($this->mCodigo);
				$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			}
		}
		return $this->mIsInit;
	}
	function getPersonaDeOrigen(){ return $this->mObj->persona_de_origen()->v(); }
	function getPersonDeDestino(){ $this->mObj->persona_de_destino()->v(); }
	function getPersonaDeDestino(){ $this->mObj->persona_de_destino()->v(); }
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
		$xRiesgo	= new cAMLRiesgos();
		$xSVC		= new MQLService("", "");
		$xRuls		= new cReglaDeNegocio();
		$xQL		= new MQL();
		$xF			= new cFecha();
		$xUsr		= new cSystemUser();
		$ctx		= $xUsr->getCTX();
		
		$autoRMS	= $xRuls->getValorPorRegla($xRuls->reglas()->AML_AUTOENVIAR_RMS);
		
		$fecha		= ($fecha == false) ? fechasys() : $fecha;
		$fecha		= $xF->getInt($fecha);
		$clave		= $this->mCodigo;
		$notas		= setCadenaVal($notas);
		$iduser		= getUsuarioActual();
		//Actualizar la alerta 1 = OK a riesgo 0 = Descartado
		$sql 		= "UPDATE aml_alerts SET fecha_de_checking=$fecha, estado_en_sistema=" . SYS_CERO . ", resultado_de_checking=1, notas_de_checking=\"$notas\", `usuario_checking`=$iduser WHERE clave_de_control=$clave";
		
		$xQL->setRawQuery($sql);
		
		//instrumento -- tipo de operacion, tercero-relacionado, $mensajes
		if($autoRMS == true){
			$xSVC->getRequest(SAFE_HOST_URL . "svc/send-to-rms.svc.php?clave=$clave&ctx=$ctx");
		}
		$xRiesgo->add($this->getPersonaDeOrigen(), $this->getTipoDeAlerta(), $fecha,
				$this->getNivelDeRiesgo(), $this->getDocumento() ,
				$this->getTipoDeDocto(), $this->getPersonDeDestino(), $this->getHora(), 
				false, false, $this->getTercero(), $this->getMensajes(), $inmediato);
		$this->mMessages	.= "OK\tAlerta # $clave de fecha $fecha promocionada a RIESGO\r\n";
		$this->mMessages	.= $xRiesgo->getMessages();
	}
	/**
	 * Dsecarta Alertas con Dictamen
	 * @param string $notas Notas del Dictamen
	 * @param string $fecha Fecha en que se dictamina
	 * @param boolean $recursivo Indica si va a descartar anteriors 
	 */
	function setDescartaAlerta($notas = "", $fecha = false, $recursivo = false){
		$fecha	= ($fecha == false) ? fechasys() : $fecha;
		$xF		= new cFecha();
		$xLog	= new cCoreLog();
		$xQL	= new MQL();
		$fecha	= $xF->getInt($fecha);
		$clave	= $this->mCodigo;
		$iduser	= getUsuarioActual();
		//setLog($notas);
		$notas	= setCadenaVal($notas);
		$res	= true;
		//setLog($notas);
		//Actualizar la alerta
		$sql = "UPDATE aml_alerts SET fecha_de_checking=$fecha, estado_en_sistema=" . SYS_CERO . ", notas_de_checking=\"$notas\", `usuario_checking`=$iduser WHERE clave_de_control=$clave";
		$res 	= $xQL->setRawQuery($sql);
		$res	= ($res === false) ? false : true;
		if($recursivo == true){
			$this->init();
			$tipo	= $this->getTipoDeAlerta();
			$persona= $this->getPersonaDeOrigen();
			
			$sql 	= "UPDATE aml_alerts SET `fecha_de_checking`=$fecha, `estado_en_sistema`=" . SYS_CERO . ", `notas_de_checking`=\"$notas\", `usuario_checking`=$iduser WHERE
			`estado_en_sistema`=" . SYS_UNO . " AND `fecha_de_checking`<= $fecha AND `tipo_de_aviso`=$tipo AND `persona_de_origen`=$persona ";
			$res 	= $xQL->setRawQuery($sql);
			$xLog->add("WARN\tALERTA_AML Descartada recursivamente al tipo $tipo\r\n");
			$res	= ($res === false) ? false : true;
		}
		$xLog->add("WARN\tALERTA_AML Con Clave $clave se ha descartado\r\n");
		
		$this->mMessages	.= $xLog->getMessages();
		return $res;
	}
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function getDescripcion(){  return $this->mObj->mensaje()->v(OUT_TXT); }
	
	function getFechaEnvio(){return $this->mFechaEnvio; }
	function getFechaOrigen(){return $this->mFechaOrigen; }
	function getFechaRegistro(){return $this->mFechaRegistro; }
	function getFechaChecking(){ return $this->mFechaChecking; }
	function getNotasSistema(){return $this->mNotasSistema; }
	function getNotasChecking(){return $this->mNotasChecking; }
	function getNotasReporte(){ return $this->mNotasReporte; }
	function getNotasPrevencion(){ return $this->mNotasPrev; }
	function getUsuarioOrigen(){ return $this->mUsuario; }
	function getOficialDest(){ return $this->mOficial; }
	function getOficialDeChecking(){ return $this->mOficialDeChecking; }
	function getEsRiesgo(){return $this->mEsRiesgo; }
	function getFicha(){
		$xLng	= new cLang();
		$xSoc	= new cSocio($this->getPersonaDeOrigen());
		$xNot	= new cHNotif();
		$xHT	= new cHObject();
		
		$xSoc->init();
		$nombre	= $xSoc->getNombreCompleto();
		
		$xOfi	= new cOficial($this->getOficialDest()); $xOfi->init();
		$oficial= $xOfi->getNombreCompleto();
		
		$xRisk	= new cAMLCatalogoDeRiesgos($this->getTipoDeAlerta());	$xRisk->init();
		$nombreriesgo = $xRisk->getNombre();
		
		$html	= "<table><tbody>";
		$html	.= "<tr><th>" . $xLng->getT("TR.CLAVE") . "</th><td class='key'>" . $this->mCodigo . "</td>";
		$html	.= "<th>" . $xLng->getT("TR.FECHA"). "</th><td>" . $this->getFechaOrigen() . "</td></tr>";
		$html	.= "<tr><th>" . $xLng->getT("TR.TIPO") . "</th><td colspan='3'>$nombreriesgo</td></tr>";
		$html	.= "<tr><th>" . $xLng->getT("TR.PERSONA") . "</th><td colspan='3'>$nombre</td></tr>";
		$html	.= "<tr><th>" . $xLng->getT("TR.OFICIAL_DE_CUMPLIMIENTO") . "</th><td colspan='3'>$oficial</td></tr>";
		//--- Contenido
		$notas	= $xHT->Out($this->getNotasSistema(), OUT_HTML);
		$html	.= "<tr><th colspan='4'>" . $xLng->getT("TR.MENSAJE DEL SISTEMA") . "</th></tr>";
		$html	.= "<tr><td colspan='4'>" . $xNot->get($notas, "", $xNot->NOTICE) . "</td></tr>";
		//---- Datos del checking
		if($this->mObj->fecha_de_checking()->v() >0 ){
			$xUsr	= new cOficial($this->getOficialDeChecking()); $xUsr->init();
			
			$html	.= "<tr><th>" . $xLng->getT("TR.FECHA_DE CHEQUEO"). "</th><td>" . $this->getFechaChecking() . "</td>";
			$sino	= ($this->getEsRiesgo() == true) ? $xLng->getT("TR.SI") : $xLng->getT("TR.NO");
			
			$html	.= "<th>" . $xLng->getT("TR.CONFIRMADO") . "</th><td>$sino</td></tr>";
			$html	.= "<tr><th>" . $xLng->getT("TR.OFICIAL_DE_CUMPLIMIENTO"). "</th><td colspan='3'>" . $xUsr->getNombreCompleto() . "</td></tr>";
			
			$dictamen	= ($this->getEsRiesgo() == true) ? $xNot->get($this->getNotasChecking(), "", $xNot->ERROR) : $xNot->get($this->getNotasChecking(), "", $xNot->SUCCESS);
	

			$html	.= "<tr><th colspan='4'>" . $xLng->getT("TR.DICTAMEN") . "</th></tr>";
			$html	.= "<tr><td colspan='4'>" . $dictamen . "</td></tr>";
			
			$html	.= "";
			$html	.= "";
			$html	.= "";
			$html	.= "";
			$html	.= "";
		}
		$html	.= "";
		$html	.= "";
		$html	.= "";
		$html	.= "";
		$html	.= "</tbody>";
		$html	.= "</table>";
		return $html;
	}
	function setEnviadoRMS(){
		$xQL					= new MQL();
		$xF						= new cFecha();
		$tt						= time();
		$xQL->setRawQuery("UPDATE `aml_alerts` SET `envio_rms`=$tt WHERE `clave_de_control` = " . $this->mCodigo);
		$this->mEnviadoRMS		= true;
		$this->setCuandoSeActualiza();
	}
	function getEsEnviadoRMS(){ return $this->mEnviadoRMS; }
	function setCuandoSeActualiza(){ $this->setCleanCache(); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mCodigo : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= $this->mTabla . "-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
}


class cAMLMatrizDeRiesgo {
	private $mMessages			= "";
	public $DEF_SYSTEM			= "SISTEMA";
	public $DEF_USUARIO			= "USUARIO";
	public $TIPO_PERSONA		= "PERSONA";
	public $TIPO_PRODUCTO		= "PRODUCTO";
	public $TIPO_OPERACION		= "OPERACION";
	
	public $P_RIESGO_EXTRANJERO	= "PERSONA_RIESGO_EXTRANJERO";
	public $P_RIESGO_ES_NACIONAL= "PERSONA_RIESGO_ES_NACIONAL";
	public $P_RIESGO_ES_PEP=	 "PERSONA_RIESGO_ES_PEP";
	public $P_RIESGO_EN_EXCEPCION= "PERSONA_RIESGO_EN_EXCEPCION";
	
	public $P_RIESGO_ACTIVIDAD	= "PERSONA_RIESGO_ACTIVIDAD";
	public $P_RIESGO_PAIS		= "PERSONA_RIESGO_PAIS";
	public $P_RIESGO_DOM_PAIS	= "PERSONA_RIESGO_DOM_PAIS";
	public $P_RIESGO_SIN_DOM	= "PERSONA_RIESGO_SIN_DOM";
	
	public $P_RIESGO_PM_NO_REP		= "PERSONA_MORAL_SIN_REPRESENTANTE";
	public $P_RIESGO_SIN_PERFIL_T	= "PERSONA_SIN_PERFIL_T";
	public $P_RIESGO_SIN_ACTIVIDAD	= "PERSONA_SIN_ACTIVIDAD";
	
	public $O_RIESGO_EXTRANJERO		= "OPERACION_CON_EXTRANJERO";
	public $O_RIESGO_INSTRUMENTO	= "OPERACION_INSTRUMENTO_RIESGO";
	public $O_RIESGO_PDTO			= "OPERACION_PRODUCTO_RIESGO";
	public $O_RIESGO_ORIGEN			= "OPERACION_RIESGO_PAIS_ORIGEN";
	public $O_RIESGO_ORIGEN_LOC		= "OPERACION_RIESGO_LOC_ORIGEN";
	
	public $O_PERSONA_ALTORIESGO= "OPERACION_PERSONA_ALTO_RIESGO";
	public $O_PERSONA_BLOQUEADO	= "OPERACION_PERSONA_BLOQUEADA";
	public $O_PERSONA_PEPS		= "OPERACION_PERSONA_PEPS";
	
	
	private $mClave				= false;
	private $mTopico			= "";
	private $mTipo				= ""; //persona credito etc

	private $mInit				= false;
	private $mObj				= null;
	private $mFinalizador		= false;
	private $mNivelRiesgo		= 1;
	private $mTipoRiesgo		= 1;
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function init($data = false){
		$xCache			= new cCache();
		$xT				= new cAml_riesgo_matrices();
		if(!is_array($data)){
			$data		= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . $xT->get() . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			$this->mObj				= $xT; //Cambiar
			$this->mClave			= $xT->idaml_riesgo_matrices()->v();
			$this->mTipo			= $xT->clasificacion()->v();
			$this->mTopico			= $xT->nombre()->v();
			$this->mFinalizador		= ($xT->finalizador()->v() == 1) ? true : false;
			$this->mNivelRiesgo		= $xT->riesgo()->v();
			$this->mTipoRiesgo		= $xT->clave_riesgo()->v();
			$this->setIDCache($this->mClave);
			$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			$this->mInit			= true;
			$xT 					= null;
			
		}
		return $this->mInit;
	}
	function getEsFinalizador(){ return $this->mFinalizador; }
	function getNivelRiesgo(){ return $this->mNivelRiesgo; }
	function getTipoRiesgo(){ return $this->mTipoRiesgo; }
	function getMantenerRiesgo($riesgo,$Anterior = 0){
		if($this->getEsFinalizador() == true ){
			$riesgo	= ($Anterior>$riesgo) ? $Anterior : $riesgo;
		} else {
			$riesgo	= $Anterior;
		}
		return $riesgo;
	}
	function getListaTopicosInArray(){
		$arr	= array();
		$arr[$this->P_RIESGO_ACTIVIDAD]		= $this->P_RIESGO_ACTIVIDAD;
		$arr[$this->P_RIESGO_EXTRANJERO]	= $this->P_RIESGO_EXTRANJERO;
		$arr[$this->P_RIESGO_ES_NACIONAL]	= $this->P_RIESGO_ES_NACIONAL;
		$arr[$this->P_RIESGO_EN_EXCEPCION]	= $this->P_RIESGO_EN_EXCEPCION;
		$arr[$this->P_RIESGO_PM_NO_REP]		= $this->P_RIESGO_PM_NO_REP;
		$arr[$this->P_RIESGO_SIN_PERFIL_T]	= $this->P_RIESGO_SIN_PERFIL_T;
		$arr[$this->P_RIESGO_DOM_PAIS]		= $this->P_RIESGO_DOM_PAIS;
		$arr[$this->P_RIESGO_SIN_DOM]		= $this->P_RIESGO_SIN_DOM;
		$arr[$this->P_RIESGO_SIN_ACTIVIDAD]	= $this->P_RIESGO_SIN_ACTIVIDAD;
		$arr[$this->P_RIESGO_PAIS]			= $this->P_RIESGO_PAIS;
		$arr[$this->P_RIESGO_ES_PEP]		= $this->P_RIESGO_ES_PEP;
		//$arr[$this->]		= $this->;
		//$arr[$this->]		= $this->;
		//$arr[$this->]		= $this->;
		
		$arr[$this->O_RIESGO_EXTRANJERO]	= $this->O_RIESGO_EXTRANJERO;
		$arr[$this->O_RIESGO_INSTRUMENTO]	= $this->O_RIESGO_INSTRUMENTO;
		$arr[$this->O_RIESGO_ORIGEN]		= $this->O_RIESGO_ORIGEN;
		$arr[$this->O_RIESGO_ORIGEN_LOC]	= $this->O_RIESGO_ORIGEN_LOC;

		$arr[$this->O_PERSONA_ALTORIESGO]	= $this->O_PERSONA_ALTORIESGO;
		$arr[$this->O_PERSONA_BLOQUEADO]	= $this->O_PERSONA_BLOQUEADO;
		$arr[$this->O_PERSONA_PEPS]			= $this->O_PERSONA_PEPS;
		$arr[$this->O_RIESGO_PDTO]			= $this->O_RIESGO_PDTO;
		//$arr[$this->]		= $this->;
		//$arr[$this->]		= $this->;
		//$arr[$this->]		= $this->;
		//$arr[]
		return $arr;
	}
	function initByTopico($topico){
		$xCache	= new cCache();
		$this->mTopico	= $topico;
		$idc			= "aml_riesgo_matrices-by-topico-$topico";
		$data			= $xCache->get($idc);
		
		if(!is_array($data)){
			$xQL	= new MQL();
			$data	= $xQL->getDataRow("SELECT * FROM `aml_riesgo_matrices` WHERE `nombre`='" . $this->mTopico . "' LIMIT 0,1");
		}
		if(isset($data["nombre"])){
			$xCache->set($idc, $data);
			$this->mClave	= $data["idaml_riesgo_matrices"];
		}
		return $this->init($data);
	}
	private function setCuandoSeActualiza(){ $this->setCleanCache(); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= "aml_riesgo_matrices-" . $clave;
	}
	private function setCleanCache(){
		if($this->mIDCache !== ""){
			$xCache = new cCache();
			$xCache->clean($this->mIDCache);
			$xCache->clean("aml_riesgo_matrices-by-tipo-" . $this->mTipo);
			$xCache->clean("aml_riesgo_matrices-by-topico-" . $this->mTopico);
		} 
	}
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
			$ready		= $actualizar;
			$xAml->init($persona, $rows);
			$riesgo			= $xAml->setAnalizarNivelDeRiesgo();
			$NivelActual	= $xAml->getOPersona()->getNivelDeRiesgo();
			$xLog->add("===============\t$persona\t================\r\n", $xLog->DEVELOPER);
			if($riesgo != $NivelActual){
				if($riesgo >= SYS_RIESGO_MEDIO){ 
					//Si el nuevo riesgo es ALTO y el Anterior es BAJO.- Verificar si se puede actualizar
					if($NivelActual < SYS_RIESGO_MEDIO){
						$xOm		= new cAMLPersonasOmisiones();
						$xOm->initByPersona($persona);
						if($xOm->omitir() == true){
							$xLog->add("WARN\t$persona\tPersona no Actualizada porque existe Omision $riesgo\r\n");
							$ready			= false;
						}
					}
					//$xLog->add($xAml->getMessages(), $xLog->DEVELOPER);
				}
				if($ready == true){	$xAml->getOPersona()->setActualizarNivelDeRiesgo($riesgo, $xAml->getMessages()); }
				$xLog->add($xAml->getOPersona()->getMessages(), $xLog->DEVELOPER);
			} else {
				$xLog->add("WARN\t$persona\tEl Riesgo Actual ($NivelActual) es el mismo que el calculado ($riesgo)\r\n");
			}
			$xLog->add($xAml->getMessages(), $xLog->DEVELOPER);
		}
		return $xLog->getMessages();
	}
}

class cAMLEstadisticas{
	function __construct(){}
	function getNumeroAlertasPendientes(){
		$xQL	= new MQL();
		//$xLs	= new cSQLListas();
		$DD		= $xQL->getDataRow("SELECT COUNT(*) AS 'numero' FROM `aml_alerts` WHERE `estado_en_sistema`=0");
		
		//
		//SELECT COUNT(*) AS 'numero' FROM `aml_alerts` WHERE `estado_en_sistema`=1
		return setNoMenorQueCero($DD["numero"]);
	}
	function getNumeroRiesgosPorReportar(){
		$xQL	= new MQL();
		//$xLs	= new cSQLListas();
		$DD		= $xQL->getDataRow("SELECT COUNT(*) AS 'numero' FROM `aml_risk_register` WHERE (`estado_de_envio` =0) AND (`fecha_de_checking` =0) ");
			
		//
		//SELECT COUNT(*) AS 'numero' FROM `aml_alerts` WHERE `estado_en_sistema`=1
		return setNoMenorQueCero($DD["numero"]);
	}		
}

class cAMLRiesgosNiveles {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= TAML_NIVEL_DE_RIESGOS . "-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache	= new cCache();
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . TAML_NIVEL_DE_RIESGOS . "` WHERE `clave_de_control`=". $this->mClave . " LIMIT 0,1");
			}
		}
		
		if(isset($data["clave_de_control"])){
			$this->mObj		= new cAml_risk_levels(); //Cambiar
			$this->mObj->setData($data);
			$this->mClave	= $this->mObj->clave_de_control()->v();
			$this->mNombre	= $this->mObj->nombre_del_nivel()->v();
			$this->setIDCache($this->mClave);
			$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			$this->mInit	= true;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){
		$this->mObj			= null;
		$this->mMessages	= "";
	}
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}
	function add(){}

}
class cPersonasConsultaEnListas {
	private $mClave			= false;
	private $mObj			= null;
	private $mInit			= false;
	private $mNombre		= "";
	private $mMessages		= "";
	private $mIDCache		= "";
	private $mTable			= "";
	public $PROV_INTERNO	= "INTERNO";
	public $PROV_QESQ		= "QUIENESQUIEN";
	public $PROV_GWS		= "GWS";
	public $TIPO_PEPS		= "PEPS";
	public $TIPO_BLOQ		= "BLOQUEADOS";
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= "personas_consulta_lista" . "-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cPersonas_consulta_lista();
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . $xT->get() . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			$this->mObj		= $xT; //Cambiar
			$this->mClave	= $data[$xT->getKey()];
			$this->setIDCache($this->mClave);
			$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			$this->mInit	= true;
			$xT 			= null;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){
		$this->mObj			= null;
		$this->mMessages	= "";
	}
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}
	function add($persona, $nombrecoincidente = "", $tipo = "", $proveedor = "", $url = "", $fecha = false, $data = ""){
		$xT		= new cPersonas_consulta_lista();
		$xF		= new cFecha();
		$fecha	= $xF->getFechaISO($fecha);
		$persona= setNoMenorQueCero($persona);
		
		//$xT->coincidente() 0 = NO ; 1 = si
		$xT->fecha($fecha);
		$xT->idpersonas_consulta_lista("NULL");
		$xT->idusuario(getUsuarioActual());
		$xT->persona($persona);
		$xT->proveedor($proveedor);
		$xT->tiempo(time());
		$xT->textocoincidente($nombrecoincidente);
		
		$xT->contenido($data);
		//$xT->razones()
		$xT->tipo($tipo);
		$xT->url($url);
		$this->mClave	= $xT->query()->getLastInsertID();
		$res	= $xT->query()->insert()->save();
	}

}
class cAMLListaNegraInterna {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTable		= "";
	private $mClaveDePersona	= 0;
	function __construct($clave = false, $persona = false){ 
		$this->mClave			= setNoMenorQueCero($clave); $this->setIDCache($this->mClave);
		$this->mClaveDePersona	= setNoMenorQueCero($persona);
	}
	function getIDCache(){return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= "aml_listanegra_int" . "-" . $clave;
		
	}
	private function setCleanCache(){
		$xCache = new cCache();
		if($this->mIDCache !== ""){ 
			 $xCache->clean($this->mIDCache); 
		}
		if($this->mClaveDePersona > DEFAULT_SOCIO){
			$xCache->clean("aml_listanegra_int" . "-persona-" . $this->mClaveDePersona);
		}
	}
	function initPorPersona($persona = false){
		$persona	= setNoMenorQueCero($persona);
		$persona	= ($persona<=DEFAULT_SOCIO) ? $this->mClaveDePersona : $persona;
		$res		= false;
		if($persona > 0){
			$xCache	= new cCache();
			$data	= $xCache->get("aml_listanegra_int" . "-persona-" . $this->mClaveDePersona);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `aml_listanegra_int` WHERE `persona`=". $this->mClaveDePersona . " AND `estatus`=1 LIMIT 0,1");
				if(isset($data["persona"])){
					$xCache->set("aml_listanegra_int" . "-persona-" . $this->mClaveDePersona, $data);
				}
			}
			$res	= $this->init($data);
		}
		return $res;
	}
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cAml_listanegra_int();//Tabla
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . $xT->get() . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			$this->mObj		= $xT; //Cambiar
			$this->mClave	= $data[$xT->getKey()];
			$this->setIDCache($this->mClave);
			$this->mClave	= $xT->persona()->v();
			$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			$this->mInit	= true;
			$xT 			= null;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){
		$this->mObj			= null;
		$this->mMessages	= "";
	}
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}
	function add($persona, $motivo,$observaciones = "", $riesgo = SYS_RIESGO_ALTO, $FechaVencimiento = false, $FechaRegistro = false){
		$xQL			= new MQL();
		$xF				= new cFecha();
		$FechaRegistro	= $xF->getFechaISO($FechaRegistro);
		$FechaVencimiento= $xF->getFechaISO($FechaVencimiento);
		$motivo			= setNoMenorQueCero($motivo);
		$persona		= setNoMenorQueCero($persona);
		$riesgo			= setNoMenorQueCero($riesgo);
		$persona		= ($persona <= DEFAULT_SOCIO) ? $this->mClaveDePersona : $persona;
		
		if($persona > DEFAULT_SOCIO){ 
			//Actualizar el Anterior si existe
			$xQL->setRawQuery("UPDATE `aml_listanegra_int` SET `estatus`=0 WHERE `persona`=$persona");
			//Eliminar de Lista Blanca
			$xPLB		= new cAMLPersonasOmisiones(false, $this->mClaveDePersona);
			$xPLB->setCancelar();
			
			
			$xT		= new cAml_listanegra_int();
			$xT->clave_interna("NULL");
			$xT->estatus(SYS_UNO);
			$xT->fecha_de_registro($FechaRegistro);
			$xT->fecha_de_vencimiento($FechaVencimiento);
			$xT->idmotivo($motivo);
			$xT->idusuario(getUsuarioActual());
			$xT->observaciones($observaciones);
			$xT->persona($persona);
			$xT->riesgo($riesgo);
			$xT->sucursal(getSucursal());
			$res			= $xT->query()->insert()->save();
			$xSoc			= NEW cSocio($persona);
			$xSoc->setCuandoSeActualiza();
			
		}
	}
}

class cAMLCatalogoDeRiesgos {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTable		= "";
	private $mTipoRiesgo		= 0;
	private $mValorPonderado	= 0;
	private $mUnidadMedida		= "";
	private $mPonderacion		= 0;
	private $mFormaReporte		= "I";//Inmediato
	private $mFormaChequeo		= "I";
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= "aml_risk_catalog-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cAml_risk_catalog();//Tabla
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . $xT->get() . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			$this->mObj				= $xT; //Cambiar
			$this->mFormaChequeo	= $xT->frecuencia_de_chequeo()->v();
			$this->mTipoRiesgo		= $xT->tipo_de_riesgo()->v();
			$this->mFormaReporte	= $xT->forma_de_reportar()->v();
			$this->mPonderacion		= $xT->unidades_ponderadas()->v();
			$this->mValorPonderado	= $xT->valor_ponderado()->v();
			$this->mUnidadMedida	= strtoupper($xT->unidad_de_medida()->v());
			$this->mNombre			= $xT->descripcion()->v();
			//`tipo_de_riesgo`,`valor_ponderado`,`unidades_ponderadas`,`unidad_de_medida`,`forma_de_reportar`,`frecuencia_de_chequeo`,
			$this->mClave			= $xT->clave_de_control()->v();
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
	function getFormaDeChequeo(){ return $this->mFormaChequeo; }
	function getFormaDeReporte(){ return $this->mFormaReporte; }
	function getTipoDeRiesgo(){ return $this->mTipoRiesgo; }
	function getUnidadDeMedida(){ return $this->mUnidadMedida; }
	function getValorPonderado(){ return $this->mValorPonderado; }
	function getUnidadesPonderadas(){ return $this->mPonderacion; }
	function setCuandoSeActualiza(){
		$this->setCleanCache();
	}
	function add(){}

}

class cAMLPersonasPerfilTransTipos {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTable		= "";
	private $mTipoExhibicion	= "";
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	=  "personas_perfil_transaccional_tipos-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cPersonas_perfil_transaccional_tipos();//Tabla
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . $xT->get() . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			$this->mObj				= $xT;
			$this->mClave			= $xT->idpersonas_perfil_transaccional_tipos()->v();
			$this->mTipoExhibicion	= $xT->tipo_de_exhibicion()->v();
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
	function getTipoDeExhibicion(){ return $this->mTipoExhibicion; }
	function setCuandoSeActualiza(){
		$this->setCleanCache();
	}
	function add(){}

}
class cAMLListasProveedores {
	private $mMessages		= "";
	private $mCookie		= "";
	
	private $mDataBusqueda		= array();
	private $mURLConsulta		= "";
	private $mItems				= 0;
	private $mIDGuardado		= 0;
	
	function __construct(){
		
	}
	function getDataBusqueda(){ return $this->mDataBusqueda; }
	function getConsultaGWS($nombre, $PrimerApellido = "", $SegundoApellido = "", $persona = false){
		$nombre				= setCadenaVal($nombre);
		$PrimerApellido		= setCadenaVal($PrimerApellido);
		$SegundoApellido	= setCadenaVal($SegundoApellido);
		
		$nombre				= urlencode($nombre);
		$PrimerApellido 	= urlencode($PrimerApellido);
		$SegundoApellido	= urlencode($SegundoApellido);
		$persona			= setNoMenorQueCero($persona);
		$persona			= ($persona <= 0) ? DEFAULT_SOCIO : $persona;
		
		$items				= 0;
		$dataOut			= array();
		$xLog				= new cCoreLog();
		$ejecutar			= true;
		
		$hxml		= "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
		$urlLogg	= SVC_HOST_CONSULTA_GWS . "login?format=xml";
		$xmlLogg	= "$hxml<spotlight><iniciar_sesion><usuario>" . AML_GWS_USER . "</usuario><clave>" .  AML_GWS_PWD . "</clave><token>" . AML_GWS_TOKEN . "</token></iniciar_sesion></spotlight>";
		$urlLout	= SVC_HOST_CONSULTA_GWS . "logout?format=xml";
		$xmlLout	= "$hxml<spotlight><logout>logout</logout></spotlight>";
		
		if(AML_GWS_USER =="" OR AML_GWS_TOKEN =="" OR AML_GWS_PWD == ""){
			$xLog->add("ERROR\tNo se lleva a cabo la consulta GSW\r\n", $xLog->DEVELOPER);
			$ejecutar	= false;
		}
		//$data3		= $this->getQueryXML($urlLout, $xmlLout);
		//Login
		$dlog		= $this->getQueryXML($urlLogg, $xmlLogg);
		$dlog		= json_decode(json_encode(simplexml_load_string($dlog)), true);
		//
		/*<?xml version="1.0" encoding="UTF-8"?><response><status>success</status></response>*/
		
		//if(isset($dlog[""]))
		$urlQ		= SVC_HOST_CONSULTA_GWS . "api/fetch?format=xml";
		$result		= false;
		
		if($PrimerApellido == "" AND $SegundoApellido == ""){
			//Persona Moral
			$xmlC	= "$hxml<spotlight><criterio><pep><tipo_lista>1</tipo_lista><nombre>$nombre</nombre></pep></criterio></spotlight>";
		} else {
			//Persona Fisica
			$xmlC	= "$hxml<spotlight><criterio><pep><tipo_lista>1</tipo_lista><nombre>$nombre</nombre><apellido_paterno>$PrimerApellido</apellido_paterno><apellido_materno>$SegundoApellido</apellido_materno></pep></criterio></spotlight>";
		}
		if($ejecutar == true){
			$data2 		= $this->getQueryXML($urlQ, $xmlC);
			//
			$cdata		= json_decode(json_encode(simplexml_load_string($data2)), true);
			$items		= 0;
			$ndata		= array();
			//{"folio":"281579","fecha_busqueda":"15 de Diciembre del 2016, 13:54 pm",
			
			if(isset($cdata["busqueda_peps"])){
				$folio		= $cdata["folio"];
				$arrPep		= $cdata["busqueda_peps"];
				$encuentra	= $arrPep["encontrados"];
				$xPList			= new cPersonasConsultaEnListas();
				
				foreach ($encuentra as $idx => $subobj){
					$tipo						= $subobj["clasificacion"];
					$subobj["nombres"]			= $subobj["nombre"];
					$subobj["primerapellido"]	= $subobj["apellido_paterno"];
					$subobj["segundoapellido"]	= $subobj["apellido_materno"];
					$subobj["tipo"]				= $subobj["clasificacion"];
					$subobj["curp"]				= "";
					$dd							= base64_encode(json_encode($subobj));
					$nombre 					= $subobj["nombres"] . " ". $subobj["primerapellido"] . " ". $subobj["segundoapellido"];
					$xPList->add($persona, $nombre, $tipo, $xPList->PROV_GWS, $urlQ, false, $dd);
					//"clasificacion":"PEP","cargo":"Presidente Constitucional de los Estados Unidos Mexicanos","ciudad":"Cd. de M\u00e9xico",
					//"institucion":"Oficina de la Presidencia de la Rep\u00fablica.",
					//"observaciones":"Periodo: 2012-2018. Lugar y fecha de nacimiento: 20 de julio de 1966 en Atlacomulco, Estado de M\u00e9xico; 
					//Cargos anteriores: Secretario de Administraci\u00f3n (2000 a 2002), Diputado por el Estado de M\u00e9xico (Dtto. XIII, Legislatura LV) (2003 a 2004) y Coordinador del Grupo Parlamentario del PRI, Gobernador del Estado de M\u00e9xico (16-Sep-2005 al 15-Sep-2011)"}},"no_encontrados":[]}}
					//"busqueda_peps":{"encontrados":{"pep":{"nombre":"Enrique","apellido_paterno":"Pe\u00f1a","apellido_materno":"Nieto",
					$xLog->add("Coincide GWS:\t" . $subobj["curp"] . " - Coincidencia " . $subobj["primerapellido"] . " ". $subobj["segundoapellido"] . " " . $subobj["nombres"] . " encontrado con  " . $subobj["tipo"] . "\r\n");
					$items++;
					$dataOut[]					= $subobj;
					$result						= true;
				}
				if($items <= 0){
					$xLog->add("OK\tNo existen coincidencias en nombres : $nombre $PrimerApellido $SegundoApellido.\r\n");
				}
			} else {
				$xLog->add("WARN\tNo existe la etiqueta ($nombre $PrimerApellido $SegundoApellido) \r\n", $xLog->DEVELOPER);
			}
		//Logout
			$this->getQueryXML($urlLout, $xmlLout);
			$this->mDataBusqueda	= $dataOut;
			$this->mURLConsulta		= $urlQ;
			$this->mItems			= $items;
		} else {
			$xLog->add("ERROR\tAlgo sucedio en la consulta, verifique con su administrador\r\n");
		}
		if($this->mItems <= 0){
			$this->setSalvarSinResultados("$nombre $PrimerApellido $SegundoApellido", $persona);
		}
		$this->mMessages			.= $xLog->getMessages();
		return $result;
	}
	private function getQueryXML($url, $xml, $loggin = false){
		$ch 		= curl_init();
		

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

		$arrHead	= array('Content-Type: text/xml','Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7', 'Connection: keep-alive');
		
		curl_setopt($ch, CURLOPT_HTTPHEADER, $arrHead);
		curl_setopt($ch, CURLOPT_COOKIEJAR, PATH_TMP .  "cookie-gws.txt");
		curl_setopt($ch, CURLOPT_COOKIEFILE, PATH_TMP . "cookie-gws.txt"); //saved cookies
		
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
		
	
		$data 		= curl_exec($ch);

		curl_close($ch);
		

		return $data;
	}
	function getConsultaInterna($nombre = "", $primerapellido = "", $segundoapellido = "", $persona = false, $Peps = false){
		$nombre				= urlencode($nombre);
		$primerapellido		= urlencode($primerapellido);
		$segundoapellido	= urlencode($segundoapellido);
		$persona			= setNoMenorQueCero($persona);
		$persona			= ($persona <= 0) ? DEFAULT_SOCIO : $persona;
		
		$xUser				= new cSystemUser();
		$xLog				= new cCoreLog();
		$ctx				= $xUser->getCTX();
		$extras				= (AML_BUSQUEDA_PERSONAS_REFORZADA == true) ? "&jarowinkler=true&metaphone=true" : "";
		//$nombre				= urlencode($nombre);
		$result				= false;
		$items				= 0;
		
		$this->mMessages		= "";
		$this->mURLConsulta		= "";
		$this->mItems			= 0;
		
		if($Peps == false){
			$mURL			= SVC_HOST_CONSULTA_SDN . "svc/listanegra.svc.php?n=" . $nombre . "&p=" . $primerapellido . "&m=" . $segundoapellido . "&ctx=$ctx" . $extras;
		} else {
			$mURL			= SVC_HOST_CONSULTA_PEP . "svc/peps.svc.php?n=" . $nombre . "&p=" . $primerapellido . "&m=" . $segundoapellido . "&ctx=$ctx" . $extras;
		}
		$xLog->add($mURL, $xLog->DEVELOPER);
		
		$ql					= new MQLService("", "");
		$json 				= $ql->getRequest($mURL); //file_get_contents($mURL);
		$data 				= json_decode($json, true);
		$this->mDataBusqueda= array();
		if(!$data){
			$xLog->add("OK\tNo existen coincidencias en nombres : $nombre $primerapellido $segundoapellido.\r\n");
		} else {
			//$this->mDataBusqueda = $data;
			$xPList			= new cPersonasConsultaEnListas();
			$tipo			= ($Peps == false) ? $xPList->TIPO_BLOQ : $xPList->TIPO_PEPS;
			$proveedor		= $xPList->PROV_INTERNO;
			
			foreach ($data as $subobj){
				$items++;
				//if($this->mClaveDePersona > DEFAULT_SOCIO){
				$dd			= base64_encode(json_encode($subobj));

				$nombre 	= $subobj["nombres"] . " ". $subobj["primerapellido"] . " ". $subobj["segundoapellido"];
				$xPList->add($persona, $nombre, $tipo, $proveedor, $mURL, false, $dd);
				$this->mDataBusqueda[]	= $subobj;
				//}
				//{"codigo":"16145","primerapellido":"CALLE","segundoapellido":"QUIROS","nombres":" LUIS SANTIAGO","curp":"SDNTK-16145","tipo":"metaphone"}
				//var_dump($subobj);
				//setLog( $subobj->primerapellido );
				//foreach ($subobj as $cls){
				//}
				$xLog->add( "Coincide:\t" . $subobj["curp"] . " : Coincidencia " . $subobj["primerapellido"] . " ". $subobj["segundoapellido"] . " " . $subobj["nombres"] . " encontrado con  " . $subobj["tipo"] . "\r\n");
			}
		}
		//setLog("NUMERO DE ITEMS $items");
		$xLog->add("OK\tReporte : $mURL&report=true&ret=true \r\n", $xLog->DEVELOPER);
		//$this->mURLConsulta		= "$mURL&report=true&ret=true";
		//var_dump($data);
		if($items >= 1){
			$result	= true;
			if($Peps == false){
				$xLog->add("ERROR\tPersona en Lista Negra o con Alto riesgo con $items posibles concindencias\r\n");
			} else {
				$xLog->add("ERROR\tPersona en Lista PEP con $items posibles concindencias\r\n");
			}
		} else {
			$this->setSalvarSinResultados("$nombre $primerapellido $segundoapellido", $persona);
		}
		$this->mMessages		.= $xLog->getMessages();
		$this->mURLConsulta		= $mURL;
		$this->mItems			= $items;
			
		return $result;
	}
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function getLinkReporte(){ return $this->mURLConsulta;  }
	function setSalvarSinResultados($texto = "", $persona = 0){
		$xLog	= new cCoreLog();
		$xLog->add("Persona: $persona .- Texto Buscado sin resultados : $texto");
		$xLog->guardar($xLog->OCat()->PERSONA_NO_EN_LISTA, $persona);
	}
}

class cAMLRiesgoProducto {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTable		= "";
	private $mTipoProd	= 0;
	private $mClaveProd	= 0;
	private $mNivelRiesgo= 1;
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= "aml_riesgo_producto-" . $clave;
	}
	private function setCleanCache(){
		if($this->mIDCache !== ""){
			$xCache = new cCache();
			$xCache->clean($this->mIDCache);
			$xCache->clean("aml_riesgo_producto-p-t-" . $this->mClaveProd . "-" . $this->mTipoProd);
		} 
	}
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cAml_riesgo_producto();//Tabla
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . $xT->get() . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			$this->mObj			= $xT; //Cambiar
			$this->mClave		= $xT->idaml_riesgo_producto()->v();
			$this->mTipoProd	= $xT->tipo_de_producto()->v();
			$this->mClaveProd	= $xT->clave_de_producto()->v();
			$this->mNivelRiesgo	= $xT->nivel_de_riesgo()->v();
			$this->setIDCache($this->mClave);
			$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			$this->mInit		= true;
			$xT 				= null;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){ $this->mObj = null; $this->mMessages	= "";	}
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}
	function setCuandoSeActualiza(){
		$this->setCleanCache();
	}
	function add(){}
	function getNivelDeRiesgo(){ return $this->mNivelRiesgo; }
	function initByTipoAndProd($tipo, $producto){
		$idc	= "aml_riesgo_producto-p-t-$producto-$tipo";
		$xCache	= new cCache();
		$data	= $xCache->get($idc);
		if(isset($data["idaml_riesgo_producto"])){
			$this->mClave		= $data["idaml_riesgo_producto"];
			$this->mTipoProd	= $data["tipo_de_producto"];
			$this->mClaveProd	= $data["clave_de_producto"];
			$xCache->set($idc, $data);
		}
		return $this->init($data);
	}
}
?>