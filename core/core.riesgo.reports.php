<?php
/**
 * @see Core Riesgo Reportes
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package seguimiento
 *  Core Captacion File
 * 		10/04/2008 Iniciar Funcion de Notificaciones 360
 */
include_once("core.config.inc.php");
include_once("entidad.datos.php");
include_once("core.db.dic.php");
include_once("core.db.inc.php");

include_once("core.deprecated.inc.php");
include_once("core.fechas.inc.php");
include_once("core.html.inc.php");

include_once("core.common.inc.php");
include_once("core.personas.inc.php");

include_once("core.contable.inc.php");
include_once "core.contable.utils.inc.php";


include_once("core.creditos.inc.php");
include_once("core.creditos.utils.inc.php");
include_once("core.operaciones.inc.php");



include_once("core.riesgo.inc.php");
@include_once("../libs/sql.inc.php");

//=====================================================================================================
//=====================================================================================================


class cReporteBuroDeCredito_tipo {
	private $mFechaDeCorte		= false;
	private $mOPer				= null;
	private $mOCred				= null;
	private $mMessages			= "";
	
	private $mTelefonoExt		= "";
	private $mTelefono			= "";
	
	private $mClaveDeCredito	= false;
	private $mInfoLinea			= array();
	private $mOut					= "csv";
	private $mEquivFiguraJu			= array( 1 => "PF", 3 => "PF", 4 => "PF", 2 => "PM", 5 => "PM", 9 => "PF" );
	private $mEquivGenero			= array(1 => "M", 2=> "F", 99 => "");
	private $mTituloPersonal    	= array(1=>"SR",2=>"SRA", 99 => "");//Hiber Tadeo
	private $mEquivEstadoCivil		= array(1 => "M" , 2 => "S", 3 => "D", 4 => "F", 5 => "S", 6 => "W", 7 => "S",99 => "S");
	private $mEquivResponsabilidad	= array(1 => "I", 99 => "I", "3" => "S", 2=> "J"); //J=Mancomunado
	private $mTipoDePagos			= array(1 => "I", 99 => "O", 3 => "O", 6 => "O", 5=> "O");
	private $mEquivFrecPagos		= array (
			1 => "D",
			7 => "W",
			14 => "K",
			10 => "V",
			15 => "S",
			30 => "M",
			60 => "B",
			90 => "Q",
			360 => "V"
	);	//Z pago Minimo
	function __construct($credito, $fecha = false){
		$xF							= new cFecha();
		$this->mClaveDeCredito		= $credito;
		$this->mFechaDeCorte		= ($fecha == false) ? fechasys() : $fecha;
		$xF->set($this->mFechaDeCorte);
		$this->mFechaDeCorte		= $xF->get();
		$this->mMessages			.= "WARN\t1FECHA_DE_REPORTE AS " . $this->mFechaDeCorte . " \r\n";
	}
	function init($arrData = false, $credito = false, $dataPagos = false){
		$fecha						= $this->mFechaDeCorte;
		$credito					= ($credito == false) ? $this->mClaveDeCredito : $credito;
	
		$xCred		= new cCredito($credito);
		$xCred->init($arrData);
		$xCred->initPagosEfectuados($dataPagos, $fecha);
		//setLog($dataPagos);
		$xSoc		= new cSocio( $xCred->getClaveDePersona());
		$xSoc->init();

		$this->mInfoLinea[1]		= $this->getText($this->getClaveDeOtorgante());
		$this->mInfoLinea[2]		= $this->getText($this->getNombreOtorgante());
		$this->mInfoLinea[3]		= $this->getFechaDeReporte();
		
		$this->mInfoLinea[4]		= $this->getText( $xSoc->getApellidoPaterno());
		$this->mInfoLinea[5]		= $this->getText($xSoc->getApellidoMaterno());
		$this->mInfoLinea[6]		= "";
		$nombres					= explode(" ", $xSoc->getNombre(),  2);
		$this->mInfoLinea[7]		= (isset($nombres[SYS_CERO])) ? $this->getText($nombres[SYS_CERO]) : "";
		$this->mInfoLinea[8]		= (isset($nombres[SYS_UNO])) ? $this->getText( $nombres[SYS_UNO] ) : "";
		
		$this->mInfoLinea[9]		= $this->getDate($xSoc->getFechaDeNacimiento());
		$this->mInfoLinea[10]		= $this->getText($xSoc->getRFC() );
		$this->mInfoLinea[11]		= isset($this->mTituloPersonal[ $xSoc->getTipoGenero() ]) ? $this->mTituloPersonal[ $xSoc->getTipoGenero() ] : "";//Hiber Tadeo
		$this->mInfoLinea[12]		= $this->getEstadoCivil( $xSoc->getEstadoCivil() );
		$this->mInfoLinea[13]		= $this->mEquivGenero[ $xSoc->getTipoGenero() ];
		//TODO: terminar defuncion/muerte de la persona
		$this->mInfoLinea[14]		= "";
		$this->mInfoLinea[15]		= "";
		
		$xDom						= $xSoc->getODomicilio();
		if($xDom == null){
			for($i=16;$i<=23;$i++){ $this->mInfoLinea[$i] = "";}
		} else {
			$this->mInfoLinea[16]		= $this->getText( $xDom->getCalleConNumero() );
			$this->mInfoLinea[17]		= "";
			$this->mInfoLinea[18]		= $this->getText($xDom->getColonia());
			$this->mInfoLinea[19]		= $this->getText($xDom->getMunicipio());
			$this->mInfoLinea[20]		= $this->getText($xDom->getCiudad());
			$this->mInfoLinea[21]		= $this->getText($xDom->getClaveDeEstadoEnSIC()); 
			$this->mInfoLinea[22]		= $this->getText($xDom->getCodigoPostal() );
			$xTel                       = $xSoc->getTelefonoPrincipal(); //Hiber Tadeo
        
		    
			$this->mInfoLinea[23]		=($xTel== 0) ? "":$xTel; //Hiber Tadeo
			
		}
		
		
		$xAE							= $xSoc->getOActividadEconomica();
		if($xAE == null){
			$this->mInfoLinea[24]		=  $this->getText($xSoc->getRFC(true) );//Para Finsureste debe de ser este RFC del cliente
			$this->mInfoLinea[33]		= 0;
		} else {
			$this->mInfoLinea[24]		= $this->getText($xSoc->getRFC(true) );//Para Finsureste debe de ser este RFC del cliente//$this->getText($xAE->getNombreEmpresa());
			$this->mInfoLinea[33]		= $this->getText( $xDom->getCalleConNumero() );//Para Finsureste debe de ser direccion del cliente //Hiber Tadeo Aca es direccion
		}
		
		//direccion 2
		$this->mInfoLinea[25]		= "";
		$this->mInfoLinea[26]		= $this->getText($xDom->getColonia());//Para Finsureste
		$this->mInfoLinea[27]		= $this->getText($xDom->getMunicipio());//Para Finsureste
		$this->mInfoLinea[28]		= $this->getText($xDom->getCiudad());//Para Finsureste
		$this->mInfoLinea[29]		= $this->getText($xDom->getClaveDeEstadoEnSIC() );//Para Finsureste
		$this->mInfoLinea[30]		= $this->getText($xDom->getCodigoPostal() );//Para Finsureste
		$this->mInfoLinea[31]		= ($xTel== 0) ? "":$xTel; //Hiber Tadeo  //Para Finsureste
		$this->mInfoLinea[32]		= "";
		
		$this->mInfoLinea[34]		= $this->getClaveDeOtorgante();
		$this->mInfoLinea[35]		= $this->getText($this->getNombreOtorgante());
		$this->mInfoLinea[36]		= $xCred->getNumeroDeCredito();
		
		$this->mInfoLinea[38]		= $this->getTipoDeCuenta( $xCred->getTipoDePago() );
		//datos del convenio
		$DConv						= $xCred->getOProductoDeCredito();
		
		$this->mInfoLinea[37]		= $this->getTipoDeResponsabilidad( $DConv->getTipoDeIntegracion() );
		
		$this->mInfoLinea[39]		= $DConv->getTipoDeContratoCR(); //"PL";//$DConv->getTipoDeContratoCR(); //Hiber Tadeo CL=LINEA DE CREDITO PL=PRESTAMO PERSONAL
		
		$this->mInfoLinea[40]		= AML_CLAVE_MONEDA_LOCAL; //TODO: mejorar
		$this->mInfoLinea[41]		= $xCred->getPagosAutorizados();
		if($xCred->getTipoEnSistema() == CREDITO_PRODUCTO_NOMINA ){ //TODO: Parchar con el de TADEO
			$this->mInfoLinea[42]	= "P";
		} else {
			$this->mInfoLinea[42]	= $this->mEquivFrecPagos[ $xCred->getPeriocidadDePago() ];
		}
		$this->mInfoLinea[43]		= $this->getDate($xCred->getFechaDeMinistracion() );
		$this->mInfoLinea[44]		= $this->getMonto($xCred->getMontoDeParcialidad() );
		$this->mInfoLinea[45]		= $this->getDate( $xCred->getFechaUltimoDePago() );
		$this->mInfoLinea[46]		= $this->getDate( $xCred->getFechaDeMinistracion() );
		$this->mInfoLinea[47]		= "";//$this->getDate("2029-12-31" );
		if($xCred->getSaldoActual($fecha) <= TOLERANCIA_SALDOS){
			$this->mInfoLinea[47]	= $this->getDate( $xCred->getFechaUltimoDePago() );
		}
		$this->mInfoLinea[48]		= $this->getFechaDeReporte();
		$DCapacidad					= $xSoc->getOCapacidadDePago();
		$this->mInfoLinea[49]		= $this->getMonto($DCapacidad->getMontoDeCreditoMaximo());
		$this->mInfoLinea[50]		= $this->getMonto($xCred->getSaldoIntegrado($fecha));//$xCred->getSaldoActual();
		$this->mInfoLinea[51]		= $this->getMonto( $DCapacidad->getLimiteDeCredito() );
		$this->mInfoLinea[52]		= $this->getMonto( $xCred->getSaldoVencido());
		//53 numero de pagos vencidos
		$this->mInfoLinea[53]		= 0;
		//54 forma mop
		$this->mInfoLinea[54]		= $xCred->getMOP($fecha);// ($this->mOut == OUT_CSV) ? "01" : "'01";
		//55 clave de observacion, segun catalogo
		$this->mInfoLinea[55]		= "";
		//56 clave de otorgante anterior
		//57 nombre otorgante anterior
		$this->mInfoLinea[56]		= "";
		$this->mInfoLinea[57]		= "";
		$this->mInfoLinea[58]		= ""; //Numero de cuenta anterior en caso de cartera tranferida
		$this->mInfoLinea[59]		= ($xCred->getFechaDePrimerAtraso() == null) ? "" : $this->getDate( $xCred->getFechaDePrimerAtraso() ); //TODO: Fecha de Primera atraso
		$this->mInfoLinea[60]		= $this->getMonto( $xCred->getSaldoActual($fecha) ); //Saldo Insoluto del Principal
		$this->mInfoLinea[61]		= $this->getMonto( $xCred->getMontoUltimoPago() );// $xCred->getMontoDeParcialidad();//0; //TODO: Monto de Ultimo pago
		if(MODO_DEBUG == true){
			$this->mMessages			.= $xCred->getMessages(OUT_TXT);
		}
	}
	function setOut($out){ $this->mOut = $out;}
	function getColumnas($out){
		$datos	= "Clave_Otorgante_1,Nombre del Otorgante_1,Fecha del reporte,Apellido_Paterno,Apellido_Materno,Apellido_Adicional,Primer_Nombre,Segundo_Nombre,Fecha_de_Nacimiento,RFC,Prefijo,Estado_Civil,Sexo,Fecha_Defuncion,Indicador_Defuncion,Direccion_calle_numero,Direccion_complemento,Colonia_o_Poblacion,Delegacion_o_Municipio,Ciudad,Estado,CP,Telefono,Empresa,Direccion_calle_numero_1,Direccion_complemento_1,Colonia_o_Poblacion_1,Delegacion_o_Municipio_1,Ciudad_1,Estado_1,CP._1,Telefono_1,Salario,Clave_Otorgante,Nombre del Otorgante,Numero_Cuenta,Tipo_Responsabilidad_Cuenta,Tipo_Cuenta,Tipo_Contrato,Moneda,Numero_de_Pagos,Frecuencia_de_Pagos,Fecha_Apertura,Monto_a_Pagar,Fecha_Ultimo_Pago,Fecha_Ultima_Compra,Fecha_Cierre_Credito,Fecha_Reporte,Credito_Maximo,Saldo_Actual,Limite_de_Credito,Saldo_Vencido,Numero_Pagos_Vencidos,Forma_Pago_Mop,Clave_Observacion,Clave_Anterior_Otorgante,Nombre_Anterior_Otorgante,Numero_Cta_Anterior,Fecha_Primer_Incumplimiento,Saldo_Insoluto,Monto_Ultimo_Pago\r\n";
		$datos	= strtoupper($datos);
		if($out != OUT_CSV){
			$texto		= "";
			$datos		= explode(",", $datos);
			$idx		= 1;
			foreach($datos as $id => $txt){
				$texto	.= (MODO_DEBUG == true) ? "<th>[$idx]$txt</th>" : "<th>$txt</th>";
				$idx++;
			}
			$datos		= "<tr>" . $texto . "</tr>";
		}
		return $datos;
	}
	function getClaveDeOtorgante(){ return ENTIDAD_CLAVE_SIC; 	}
	function getNombreOtorgante(){ return $this->getText( ENTIDAD_NOMBRE_SIC );	}
	function getFechaDeReporte(){ return $this->getDate($this->mFechaDeCorte);	}
	function getLinea($out = OUT_CSV){
		$xT		= new cTipos();
		$texto	= "";
		foreach($this->mInfoLinea as $id => $txt){
			if($out == OUT_CSV){
				$texto	.=	$xT->getCSV($txt);
				$texto	.= ($id==61) ? "\r\n" : ",";
			} else {
				$texto	.= ($id==1) ? "<tr>" : "";
				//$texto	.= "<td style=mso-number-format:'@'>$txt</td>";
				$texto	.= "<td class=\"xl25\" x:str=\"$txt\">$txt</td>";
				$texto	.= ($id==61) ? "</tr>\r\n" : "";
			}
			 
		}
		return $texto;
	}
	function getEstadoCivil($valor){
		return $this->getText( $this->mEquivEstadoCivil[$valor] );
	}
	function getTipoDeCuenta($valor){
		/*I = pagos fijos, M = hipoteca, O = sin Limite, R = revolvente */
		return isset( $this->mTipoDePagos [$valor] ) ? $this->mTipoDePagos[$valor] : "I";
	}
	function getTipoDeResponsabilidad($valor){
		/*I = pagos fijos, M = hipoteca, O = sin Limite, R = revolvente */
		return isset( $this->mEquivResponsabilidad[$valor] ) ? $this->mEquivResponsabilidad[$valor] : "I";
	}
	function getText($txt	= "", $tamannio = false){
		$xT		= new cTipos();
		$txt	= $xT->setNoAcentos($txt);
		$txt	= strtoupper($txt);
		if($tamannio != false){
			$txt	= $xT->cSerial($tamannio, $txt);
		}
		return $txt;
	}
	function getDate($fecha){
		$xF		= new cFecha();
		return ($this->mOut == OUT_CSV) ? date("dmY", $xF->getInt($fecha)) : "" . date("dmY", $xF->getInt($fecha));
	}
	function getMonto($monto){
		$monto		= setNoMenorQueCero($monto);
		$monto		= ($monto <= (TOLERANCIA_SALDOS + 0.01)) ? 0 : ceil($monto);
		return $monto;
	}
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
}


class cReporteCirculoDeCredito_tipo {
	private $mOut				= "csv";
	private $mFechaDeCorte		= false;
	private $mTelefonoExt		= "";
	private $mTelefono			= "";	
	
	function __construct($credito = false, $fecha = false){
		$this->mClaveDeCredito		= $credito;
		$this->mFechaDeCorte		= ($fecha == false) ? fechasys() : $fecha;
	}
	function setOut($out){ $this->mOut = $out;}
	function getText($txt	= "", $serializar = false, $limitar = false){
		$xT		= new cTipos();
		$xFI	= new cFileImporter();
		$arrR	= array(",", "-", ".", "|");
		//$txt	= $xFI->cleanString($txt, $arrR);
		//$xT->setToUTF8();
		//$txt	= iconv('UTF-8', 'UTF-8//IGNORE', $txt);
		//$txt	= "$txt-" . mb_detect_encoding($txt, 'ASCII,UTF-8,ISO-8859-1');
		$txt	= $xT->cChar($txt);
		//setLog($txt);
		//$txt	= html_entity_decode($txt);
		//$txt = mb_convert_encoding($txt, 'UTF-8', mb_detect_encoding($txt, 'ASCII,UTF-8,ISO-8859-1'));
		$txt	= strtoupper($txt);
		if($serializar != false){
			$txt	= $xT->cSerial($serializar, $txt);
		}
		if($limitar != false){
			$txt	= substr($txt, 0, $limitar);
		}
		return $txt;
	}
	function getDate($fecha){
		$xF		= new cFecha();
		return ($this->mOut == OUT_CSV) ? date("Ymd", $xF->getInt($fecha)) : "" . date("Ymd", $xF->getInt($fecha));
	}
	function getPurgueDomicilio($domicilio){
		$domicilio		= $this->getText($domicilio);
		$domicilio		= str_replace("CALLE", "", $domicilio);
		$domicilio		= str_replace("C.", "", $domicilio);
		return $domicilio;
	}
	function getClaveDeOtorgante(){ return ENTIDAD_CLAVE_SIC; 	}
	function getNombreOtorgante(){ return $this->getText( ENTIDAD_NOMBRE_SIC );	}
	function getFechaDeReporte(){ return $this->getDate($this->mFechaDeCorte);	}
	function getMonto($monto){
		$monto		= setNoMenorQueCero($monto);
		$monto		= ($monto <= (TOLERANCIA_SALDOS + 0.01)) ? 0 : ceil($monto);
		return $monto;
	}
	function getETipoColonia($valor){
		$equiv		= array(
				"Aeropuerto"			=>1,
				"Ampliacion"			=>7,
				"Barrio"				=>2,
				"Campamento"			=>4,
				"Ciudad"				=>51,
				"Colonia"				=>7,
				"Condominio"			=>8,
				"Congregacion"			=>9,
				"Conjunto habitacional"	=>10,
				"Ejido"					=>17,
				"Equipamiento"			=>4,
				"Estacion"				=>19,
				"Fraccionamiento"		=>24,
				"Gran usuario"			=>53,
				"Hacienda"				=>20,
				"Ingenio"				=>29,
				"Poblado comunal"		=>35,
				"Pueblo"				=>36,
				"Rancho o rancheria"	=>37,
				"Residencial"			=>38,
				"Unidad habitacional"	=>42,
				"Villa"					=>39,
				"Zona comercial"		=>54,
				"Zona federal"			=>41,
				"Zona industrial"		=>43,
				"" => ""
		);
		return (isset($equiv[$valor])) ? $equiv[$valor] : "";
	}
	function getEPeriocidad($valor){
		$equiv		= array (
				7 => "S",
				14 => "C",
				15 => "Q",
				30 => "M",
				60 => "B",
				90 => "T",
				360 => "U",
					
				//TODO: Verificar
				10 => "D",
				1 => "K"
		);
		return (isset($equiv[$valor])) ? $equiv[$valor] : "U";
	}
	function getETipoDomicilio($valor){
		$equiv		= array( 1 => "C", 2 => "N", 3 => "E", 99 => "O"	);
		return (isset($equiv[$valor])) ? $equiv[$valor] : "O";
	}
	function getETipoDeRegimenViv($valor){
		$equiv		= array(1 => 1, 2=> 2, 3 => 4, 4 => 3, 99 => "");
		return (isset($equiv[$valor])) ? $equiv[$valor] : "";
	}	
	function getETipoECivil($valor){
		$equiv		= array(1 => "C" , 2 => "S", 3 => "D", 4 => "L", 5 => "", 6 => "V", 7 => "E",99 => "");
		return (isset($equiv[$valor])) ? $equiv[$valor] : "";
	}
	function getETipoPGenero($valor){
		$equiv		= array(1 => "M", 2=> "F", 99 => "");
		return (isset($equiv[$valor])) ? $equiv[$valor] : "";
	}
	function getETipoPersona($valor){
		//$arrPersonalidad		= 
		$equiv		= array( 1 => "PF", 3 => "PF", 4 => "PF", 2 => "PM", 5 => "PM", 9 => "PF" );
		return (isset($equiv[$valor])) ? $equiv[$valor] : "PF";
	}
	function getPurgueTel($valor){
		$xT		= new cTipos();
		$valor	= strtoupper($valor);
		$valor	= str_replace(".", "", $valor);
		if( strpos($valor, "EXT") !== false ){
			$d		= explode("EXT", $valor); 
			$this->mTelefono	= $xT->cInt($d[0]);
			$this->mTelefonoExt	= $xT->cInt($d[1]);
		} else {
			$this->mTelefono	= $xT->cInt($valor);
		}
		if(strlen($this->mTelefono) <= 5){
			$this->mTelefono	= "";
			$this->mTelefonoExt	= "";
		}
	}
	function getETelefono(){	return $this->mTelefono;	}
	function getETelefonoExt(){	return $this->mTelefonoExt;	}
}

class cReporteAMLRelevantes_tipo {
	
}
class cReporteAMLInusuales {
	
}

class cReportesAML_Layout {
	public $OPERACIONES_RELEVANTES	= "operaciones.relevantes";
	public $OPERACIONES_INUSUALES	= "operaciones.inusuales";
	public $OPERACIONES_PREOCUPANTES= "operaciones.preocupantes";
	public $mExo					= array();
	private $mTipo					= "";
	function __construct($tipo = ""){ $this->mTipo	= $tipo; }
	function setTipo($tipo){ $this->mTipo = $tipo; }
	function read(){
		$json = file_get_contents("../layouts/pld.json");
		$this->mExo	= json_decode($json, true);
		return $this->mExo;
	}
	function getTitulo(){ return isset($this->mExo["titulo"]) ?$this->mExo["titulo"] : ""; 	}
	function getSeparador(){ return isset($this->mExo["separador"]) ?$this->mExo["separador"] : STD_LITERAL_DIVISOR; }
}

class cReportes_Layout {
	private $mEsquema			= "";
	private $mExo				= array();
	private $mTipo				= "";
	
	public $OPERACIONES_RELEVANTES	= "pld.relevantes.json";
	public $OPERACIONES_INUSUALES	= "pld.inusuales.json";
	public $OPERACIONES_INTERNAS	= "pld.preocupantes.json";
		
	function __construct($esquema = ""){ $this->mEsquema	= $esquema;	}
	function read(){
		$json 		= file_get_contents("../layouts/" . $this->mEsquema);
		$this->mExo	= json_decode($json, true);
		return $this->mExo;
	}
	function setTipo($tipo){ $this->mEsquema= $tipo; }
	function getTitulo(){ return isset($this->mExo["titulo"]) ?$this->mExo["titulo"] : ""; 	}
	function getSeparador(){ return isset($this->mExo["separador"]) ?$this->mExo["separador"] : STD_LITERAL_DIVISOR; }
	function getContent(){ return isset($this->mExo["contenido"]) ? $this->mExo["contenido"] : array(); }
	function getClave(){ return isset($this->mExo["clave_de_reporte"]) ?$this->mExo["clave_de_reporte"] : ""; }
}

class cReportes_LayoutTipos {
	private $NOMBRE		= "nombre";
	private $VALOR			= "valor";
	private $CAMPO			= "campo";
	private $TIPO			= "tipo";
	private $ACEPTADOS		= "aceptados";
	private $OBLIGATORIO	= "obligatorio";
	private $FORMATO		= "formato";
	private $LONGITUD		= "longitud";
	private $DESCRIPCION	= "descripcion";

	private $mMessages		= "";
	private $mValor			= "";
	private $mDatos			= array();
	private $mObligatorio	= false;
	private $mTipo			= "boolean";
	private $mFormato		= "";
	private $mLargo		= 0;
	private $mNombre		= "";
	public $ERRORES			= 0;
	private $mDelimit		= "";
	function __construct($parametros, $valor = "", $delimiter = ""){
		$this->mValor	= $valor;
		$this->mDelimit	= $delimiter;	
		$xT				= new cTipos();
		//$this->mMessages		.= "OK\tValor a $valor\r\n";
		$this->mNombre	= (isset($parametros[$this->NOMBRE])) ? $parametros[$this->NOMBRE] : "?";
		if(isset($parametros[$this->TIPO])){
			$this->mTipo		= strtolower($parametros[$this->TIPO]);
			//setLog($this->mTipo . " --- $valor");
				switch($this->mTipo){
					case MQL_DATE:
						$this->mValor	= setFechaValida($this->mValor);
						break;
					case MQL_BOOL:
						$this->mValor	= $xT->cBool($this->mValor);
						break;
					case MQL_STRING:
						$this->mValor	= $this->cleanText($this->mValor);
						$this->mValor	= setCadenaVal($this->mValor);
						break;
					case MQL_FLOAT:
						$this->mValor	= setNoMenorQueCero($this->mValor);
						break;
					case MQL_INT:
						$this->mValor	= setNoMenorQueCero($this->mValor);
						$this->mValor	= (int)$this->mValor;
						break;						
				}
		}
		//$this->mMessages		.= "OK\tValor queda en (" . $this->mValor . ") de ($valor)\r\n";
		$this->mObligatorio				= (isset($parametros[$this->OBLIGATORIO])) ? $xT->cBool($parametros[$this->OBLIGATORIO]) : false;
		$this->mFormato					= (isset($parametros[$this->FORMATO])) ? $parametros[$this->FORMATO] : "";
		$this->mLargo					= (isset($parametros[$this->LONGITUD])) ? setNoMenorQueCero($parametros[$this->LONGITUD]) : 0;
		//if($this->mObligatorio == true){
			if(isset($parametros[$this->ACEPTADOS])){
				if($this->aceptado($this->mValor, $parametros[$this->ACEPTADOS]) === false){
					$this->mValor			= null;
					$this->mMessages		.= "ERROR\tError en la columna " .$this->mNombre . "  : Valor no aceptado " . $this->mValor  .", los valores deben ser (" . $parametros[$this->ACEPTADOS] . ")\r\n";
					$this->ERRORES++;
				}
			}
		//}
		if(is_array($parametros)){
			$this->mDatos	= $parametros;
		}
	}
	function get(){
		$valor	= $this->mValor;
		$xT		= new cTipos();
		switch($this->mTipo){
			case MQL_DATE:
				
				$xF		= new cFecha();
				$valor	= $xF->out($this->mFormato, $valor);
				break;
			case MQL_BOOL:
				
				break;
			case MQL_STRING:
				//largo maximo
				$valor			= strtoupper( substr($valor, 0, $this->mLargo) );
				break;
			case MQL_FLOAT:
				if(setNoMenorQueCero($valor) > 0 OR $this->mObligatorio == true){
					$serializar	= strlen($this->mFormato);
					$valor		= (strpos($this->mFormato, "#") === false) ? $valor : $xT->cSerial($serializar, $valor);
				} else {
					$valor 		= "";
				}
				break;
			case MQL_INT:
				if(setNoMenorQueCero($valor) > 0 OR $this->mObligatorio == true){
					$serializar	= strlen($this->mFormato);
					//setLog($this->mFormato . $serializar);
					$valor		= (strpos($this->mFormato, "#") === false) ? $valor : $xT->cSerial($serializar, $valor);				
				} else {
					$valor 		= "";
				}
				break;
		}
		return $valor;
	}
	function getNombre(){ return $this->mDatos[$this->NOMBRE]; }
	
	private function aceptado($valor, $aceptados){
		$aceptado	= true;
		$operandos	= array("=", ">", "<", "!=");
		$xLog		= new cCoreLog();
		$xLog->add("WARN\tValores Aceptados $aceptados. " . $this->mNombre, $xLog->DEVELOPER);
		if(trim($aceptados) == "" OR trim($aceptados) == "*"){
			$xLog->add("WARN\tTodos los valores Aceptados $aceptados. " . $this->mNombre, $xLog->DEVELOPER);
		} else {
			//buscar separador
			if(strpos($aceptados, "&") !== false){
				//separar
				$arr	= explode("&", $aceptados);
				foreach ($arr as $idx => $vals){
					$xLog->add("WARN\tSeparar $aceptados y comparar $valor con $vals. " . $this->mNombre, $xLog->DEVELOPER);
					$aceptado	= ($this->testVal($vals, $valor) == false) ? false : $aceptado;
				}
			} else if(strpos($aceptados, "AND") !== false){
				//separar
				$arr	= explode("AND", $aceptados);
				foreach ($arr as $idx => $vals){
					$xLog->add("WARN\tSeparar con AND $aceptados y comparar $valor con $vals. " . $this->mNombre, $xLog->DEVELOPER);
					$aceptado	= ($this->testVal($vals, $valor) == false) ? false : $aceptado;
				}
			} else {
				//buscar operandos
				$aceptado	= $this->testVal($aceptados, $valor);
				$xLog->add("WARN\tComparar $aceptado con $valor. " . $this->mNombre, $xLog->DEVELOPER);
			}
		}
		//$this->mMessages	.= $xLog->getMessages();
		return $aceptado;
	}
	private function testVal($evaluador, $valor){
		$cumple		= false;
		if((trim($evaluador) == "*") OR (trim($evaluador)) == ""){
		} else {
			if($this->mTipo == MQL_STRING){ $valor = "'" . $valor . "'"; }
			eval('if (' . $valor . ' ' .  $evaluador. '){ $cumple = true; }');
			//$this->mMessages	.= "OK\tAl evaluar resulto en [$cumple] ($valor $evaluador)  \r\n";
		}
		return $cumple;
	}
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	private function cleanText($txt){
		$txt		= str_replace($this->mDelimit, " ", $txt);
		$txt		= str_replace("ERORR\t", "", $txt);
		$txt		= str_replace("WARN\t", "", $txt);
		$txt		= str_replace("OK\t", "", $txt);
		$txt		= str_replace("\n", " ", $txt);
		$txt		= str_replace("\r", "", $txt);
		$txt		= str_replace("'", "", $txt);
		$txt		= str_replace("\"", "", $txt);
		$txt		= str_replace("\\", "", $txt);
		$txt 		= preg_replace('/[ ]{2,}|[\t]/', ' ', trim($txt)); //dobles espacios y tabuladores
		return $txt;
	}
	/*			"nombre" : "periodo de reporte",
	 "campo" : "",
	"valor" : "",
	"tipo" : "fecha",
	"aceptados" : "fecha",
	"obligatorio" : true,
	"formato" : ["Ym", "Ymd"],
	"longitud" : "formato",
	"descripcion" : "Periodo AAAAMM para relevantes, AAAAMMDD para lo demas"*/
}

?>