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
	include_once("core.personas.utils.inc.php");
	include_once("core.personas.inc.php");
	
	include_once("core.region.inc.php");
	include_once("core.riesgo.inc.php");
	
@include_once("../libs/sql.inc.php");

class cPersonasRegiones {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	function __construct($clave = false){
		$this->mClave	= setNoMenorQueCero($clave);
	}
	function init($data = false){
		$xQL	= new MQL();
		$data	= (is_array($data)) ? $data : $xQL->getDataRow("SELECT * FROM `socios_region` WHERE `idsocios_region`=". $this->mClave);
		if(isset($data["idsocios_region"])){
			$this->mObj		= new cSocios_region(); //Cambiar
			$this->mObj->setData($data);
			$this->mNombre	= $this->mObj->descripcion_region()->v();
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
}

/**
 * Obtiene y efectua Operaciones sobre una caja local
 * @author Balam Gonzalez Luis Humberto
 * @package common
 * @subpackage caja
 */
class cCajaLocal{
	private $mNumeroDeCaja	= false;
	private $mArrDatos		= array();
	private $mCodigoPostal	= 0;
	private $mCaja_AsInit	= false;
	private $mUltimoSocio	= false;
	private $mMessages		= "";
	private $mSucursal		= "";
	private $mRegion		= 0;
	/**
	 * funcion de inicializacion
	 * @param integer $codigo	Codigo de la Caja Local
	 */
	function __construct($codigo = false){
		$this->mNumeroDeCaja	= setNoMenorQueCero($codigo);
		if( $codigo != false ){ $this->init();	}
	}
	function getUltimoSocioRegistrado($force_update = false){
		$xQL		= new MQL();
		$xFol		= new cFolios();
		if( SISTEMA_CAJASLOCALES_ACTIVA == true AND MULTISUCURSAL == false){
			if ($force_update == true ){
				$this->setReestablecerNumeracion();
				$this->init();
			}
			if ( $this->mCaja_AsInit == false ){ $this->init();	}
			$xT			= new cTipos(0);
			$this->mUltimoSocio 	= $this->mUltimoSocio+1;
			$numSerializado			= $this->mNumeroDeCaja . $xT->cSerial(DIGITOS_DE_SOCIO, 1);
			if($numSerializado > $this->mUltimoSocio){
				$this->mUltimoSocio	= $numSerializado;
			}
	
			if ($this->getExistenciaSocio($this->mUltimoSocio) > 0){
				$this->mMessages	.= "SOCIO\tEl socio " . $this->mArrDatos["ultimosocio"] . " EXISTE!!\r\n";
				//obtiene el numero de socio
				$sql				= "SELECT COUNT(codigo)+1 AS 'cMaxSocio' FROM socios_general WHERE cajalocal = " . $this->mNumeroDeCaja;
				$v					= mifila($sql, "cMaxSocio");
				$this->mMessages	.= "SOCIO\tObtenido el Numero  " . $v . " como base de Calculo\r\n";
				//hace un substr del numero de socio de cuatro digitos
				//$this->mUltimoSocio	= $xT->cSerial(DIGITOS_DE_CAJA_LOCAL,  $this->mNumeroDeCaja) . $xT->cSerial(DIGITOS_DE_SOCIO, $v);
				$this->mUltimoSocio	= $this->mNumeroDeCaja . $xT->cSerial(DIGITOS_DE_SOCIO, $v);
				$this->mMessages	.= "SOCIO\tSe actualiza el socio " . $this->mUltimoSocio . " como inicial\r\n";
				//verificacion anidada nivel a 100 busquedas
				for( $i = 1; $i<=100; ){
					if ($this->getExistenciaSocio($this->mUltimoSocio) == 0){
						$this->mMessages	.= "SOCIO\tOK\tEl Codigo " . $this->mUltimoSocio . " Esta Disponible\r\n";
						break;
					} else {
						//$sql		= "SELECT COUNT(codigo)+2 AS 'cMaxSocio' FROM socios_general WHERE cajalocal = " . $this->mNumeroDeCaja;
						//$v			= mifila($sql, "cMaxSocio");
						$this->mUltimoSocio+$i;
						$this->mMessages	.= "SOCIO\tDUPLI\tEl Codigo " . $this->mUltimoSocio . " Existe!!!\r\n";
						//$v
						//$this->mUltimoSocio	= $this->mNumeroDeCaja . $xT->cSerial(DIGITOS_DE_SOCIO, $v);
					}
				}
				//verificacion anidada nivel 3, ultima Opcion
				if ($this->getExistenciaSocio($this->mUltimoSocio) > 0){
						$this->mMessages	.= "SOCIO\tDUPLI\tEL Codigo " . $this->mUltimoSocio . " Existe!!!\r\n";
						$sql 			= "SELECT MAX(codigo)+1 AS 'iNuevoCodigo' FROM socios_general";
						$this->mUltimoSocio	= mifila($sql, "iNuevoCodigo");
				}
			}
		} else {
			$data				= $xQL->getDataRow("SELECT MAX(codigo) AS 'ultimo', COUNT(codigo) AS 'numero' FROM socios_general");
			$this->mUltimoSocio	= $data["ultimo"]+1;
			//verificar si no estar reservado y Guardar Folios (Reservar)
			$this->mUltimoSocio	= $xFol->getClaveDePersonas(true, $this->mUltimoSocio);
			//TODO: terminar soporte de numero por sucursal
			/*if(MULTISUCURSAL == true OR SYSTEM_ON_LINE == false ){
				
			} else {
				
				$data	= $xQL->getDataRecord("SELECT MAX(codigo) AS 'ultimo', COUNT(codigo) AS 'numero' FROM socios_general");
				foreach ($data as $rows){
					
				}
				
			}*/
		}
		if ( $this->mUltimoSocio <= 0 OR  $this->mUltimoSocio == false OR is_null($this->mUltimoSocio)  ){
			$this->mUltimoSocio = $this->mNumeroDeCaja . "0001";
		}		
		return $this->mUltimoSocio;
	}
	function setReestablecerNumeracion($mUltimoSocio = false){
		if ( $mUltimoSocio == false ){
            $sql 		=  "UPDATE socios_cajalocal SET ultimosocio = (SELECT MAX(codigo) FROM socios_general
						WHERE cajalocal=socios_cajalocal.idsocios_cajalocal)";
		} else {
			$sql 	=  "UPDATE socios_cajalocal SET ultimosocio = " . $mUltimoSocio . "
						WHERE socios_cajalocal.idsocios_cajalocal = " . $this->mNumeroDeCaja;
		}
		$x 		=  my_query($sql);
		//estandarizar numeracion
		//$xT	= new cTipos(0);
		
	    return		$x["info"];
	}
	/**
	 * Obtiene el numero de veces que existe un socio
	 * @param integer $socio
	 */
	function getExistenciaSocio($socio){
		$exist	= 0;
		if(isset($socio) ){
			$sql	= "SELECT COUNT(codigo) AS 'iExistentes' FROM socios_general WHERE codigo=$socio ";
			//setLog($sql);
			$exist	= mifila($sql, "iExistentes");
		}
		return $exist;
	}
	function init($ArrDatos = false){
		if ( is_array($ArrDatos) ){
			$this->mArrDatos	= $ArrDatos;
		} else {
			$sql = "SELECT * FROM `socios_cajalocal` WHERE (`idsocios_cajalocal` =" . $this->mNumeroDeCaja . ") LIMIT 0,1";
			$this->mArrDatos	= obten_filas($sql);
		}
		if(isset($this->mArrDatos["ultimosocio"])){
			$this->mUltimoSocio		= $this->mArrDatos["ultimosocio"];
			$this->mRegion			= $this->mArrDatos["region"];
			$this->mCodigoPostal	= $this->mArrDatos["codigo_postal"];
			$this->mSucursal		= $this->mArrDatos["sucursal"];
		} else {
			$this->mUltimoSocio		= 0;
			$this->mRegion			= FALLBACK_PERSONAS_REGION;
			$this->mCodigoPostal	= EACP_CODIGO_POSTAL;
			$this->mSucursal		= getSucursal();
			$this->mArrDatos["descripcion_cajalocal"]	= "";
		}
		$this->mCaja_AsInit		= true;
		return $this->mCaja_AsInit;
	}
	function getDatosInArray(){ return $this->mArrDatos; }
	function getFicha($marco = true){
		if($this->mCaja_AsInit == false){$this->init();}

		//cargar formato
		$xCache			= new cCache();
		$this->mIDCache	= EACP_CLAVE . ".ficha.cl." . $this->mNumeroDeCaja . "";
		$ficha			= $xCache->get($this->mIDCache);
		if($ficha == null){
			$xFMT	= new cFormato(12001);
			$xFMT->setCajaLocal($this->mNumeroDeCaja, $this->getDatosInArray());
			$xFMT->setRegionLocal($this->getRegion());
			$xFMT->setProcesarVars();
			$ficha		=$xFMT->get();
			$xCache->set($this->mIDCache, $ficha);
			//setLog("Procesar " . $this->mIDCache);
		}
		return $ficha;
				
	}
	/**
	 * Funcion que Agrega una Caja Local
	 *
	 **/
	function setNew($nombre, $numero = false, $ultimo_socio = false, $region = 99, $sucursal = false,
			$codigo_postal = false, $localidad = false, $estado = false, $municipio = false ){
		$sql = "INSERT INTO socios_cajalocal
				(idsocios_cajalocal, descripcion_cajalocal, ultimosocio, region, sucursal, codigo_postal, localidad, estado, municipio)
			    VALUES($numero, '$nombre', $ultimo_socio, $region, '$sucursal', $codigo_postal, '$localidad', '$estado', '$municipio')";
		$x	= my_query($sql);
	}
	function add($nombre, $numero = false, $region = false, $sucursal = "",
			$codigo_postal = false, $localidad = "", $estado = "", $municipio = "", $ultimo_socio = false ){
		$xQL			= new MQL();
		$region			= setNoMenorQueCero($region);
		$ultimo_socio	= setNoMenorQueCero($ultimo_socio);
		$codigo_postal	= setNoMenorQueCero($codigo_postal);
		//Def
		$region			= ($region <= 0) ? getRegion() : $region;
		$sucursal		= ($sucursal == "") ? getSucursal() : $sucursal;
		$numero			= setNoMenorQueCero($numero);
		if($numero	<= 0){
			$xCL		= new cSocios_cajalocal();
			$numero		= $xCL->query()->getLastID()+1;
		}
		$xCP		= new cDomiciliosColonias();
		$idcp		= $xCP->getClavePorCodigoPostal($codigo_postal);
		if($idcp > 0){
			$localidad	= ($localidad == "") ? $xCP->getNombreLocalidad() : $localidad;
			$municipio	= ($municipio == "") ? $xCP->getNombreMunicipio() : $municipio;
			$estado		= ($estado == "") ? $xCP->getNombreEstado() : $estado;
		}
		$sql = "INSERT INTO socios_cajalocal
		(idsocios_cajalocal, descripcion_cajalocal, ultimosocio, region, sucursal, codigo_postal, localidad, estado, municipio, `clave_de_centro`)
		VALUES($numero, '$nombre', $ultimo_socio, $region, '$sucursal', $codigo_postal, '$localidad', '$estado', '$municipio','$numero')";
		$res	= $xQL->setRawQuery($sql);
		return ($res == false) ? false : true;
	}
	function edit($nombre, $region = false, $sucursal = "",	$codigo_postal = false, $localidad = "", $estado = "", $municipio = "" ){
		$xQL			= new MQL();
		$region			= setNoMenorQueCero($region);
		
		$codigo_postal	= setNoMenorQueCero($codigo_postal);
		$ultimo_socio	= $this->getUltimoSocioRegistrado();
		//Def
		$region			= ($region <= 0) ? $this->getRegion() : $region;
		$sucursal		= ($sucursal == "") ? $this->getSucursal() : $sucursal;
		$codigo			= $this->mNumeroDeCaja;
		$xCP		= new cDomiciliosColonias();
		$idcp		= $xCP->getClavePorCodigoPostal($codigo_postal);
		if($idcp > 0){
			$localidad	= ($localidad == "") ? $xCP->getNombreLocalidad() : $localidad;
			$municipio	= ($municipio == "") ? $xCP->getNombreMunicipio() : $municipio;
			$estado		= ($estado == "") ? $xCP->getNombreEstado() : $estado;
		}
		$sql = "UPDATE socios_cajalocal 
	    SET descripcion_cajalocal='$nombre', ultimosocio=$ultimo_socio, region=$region, sucursal='$sucursal', codigo_postal=$codigo_postal, 
	    localidad='$localidad', estado='$estado', municipio='$municipio', clave_de_centro='$codigo'	    WHERE idsocios_cajalocal=$codigo";
		$res	= $xQL->setRawQuery($sql);
		if($res !== false){ $this->setCuandoSeActualiza(); }
		return ($res == false) ? false : true;
	}	
	function getNombre(){ return $this->mArrDatos["descripcion_cajalocal"];	}
	function getSucursal(){ return $this->mSucursal;}
	function getRegion(){ return $this->mRegion; }
	function getCodigoPostal(){ return $this->mCodigoPostal; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function setCuandoSeActualiza(){
		$xCache			= new cCache();
		$this->mIDCache	= EACP_CLAVE . ".ficha.cl." . $this->mNumeroDeCaja . "";
		$xCache->clean($this->mIDCache);		
	}
	/**
	 * Admite un socio
	 * @param integer $socio
	 */
	function setAdmitirSocio( $socio ){
		$sql	= "UPDATE socios_general SET estatusactual=10 WHERE codigo=$socio";
		$x		= my_query($sql);
		return $x["stat"];
	}
	function setValidar(){
		$msg		= "";
		$sql1 		= "UPDATE socios_cajalocal SET localidad = UCASE( (SELECT ciudad_colonia FROM general_colonias WHERE codigo_postal=socios_cajalocal.codigo_postal LIMIT 0,1) )";
		$x1			= my_query($sql1);

		$sql2 		= "UPDATE socios_cajalocal SET localidad = UCASE( descripcion_cajalocal ) WHERE ISNULL(localidad) = TRUE OR localidad = '' " ;
		$x2			= my_query($sql2);

		$sql3 		= "UPDATE socios_cajalocal SET ultimosocio = CONCAT(idsocios_cajalocal, '0001') WHERE ISNULL(ultimosocio) = TRUE OR ultimosocio = 0";
		$x3			= my_query($sql3);

		$sql4 		= "UPDATE socios_cajalocal SET estado = UCASE( (SELECT estado_colonia FROM general_colonias WHERE codigo_postal=socios_cajalocal.codigo_postal LIMIT 0,1) )";
		$x4			= my_query($sql4);

		$sql5 		= " UPDATE socios_cajalocal SET municipio = UCASE( (SELECT municipio_colonia FROM general_colonias WHERE codigo_postal=socios_cajalocal.codigo_postal LIMIT 0,1) )";
		$x5			= my_query($sql5);

		$msg		.= $x1["info"];
		$msg		.= $x2["info"];
		$msg		.= $x3["info"];
		$msg		.= $x4["info"];
		$msg		.= $x5["info"];
		
		$msg		.= "===============\tSOCIOS A SUS SUCURSALES\r\n";
		$rs_cl 		= "SELECT * FROM socios_cajalocal";
		$rsm 		= getRecordset( $rs_cl );
		while ($rwcl = mysql_fetch_array($rsm)){
			$sql_us = "UPDATE socios_general set sucursal='$rwcl[4]' WHERE cajalocal=$rwcl[0]";
			my_query($sql_us);
			$msg	.= date("H:i:s") . "\t\tActualizando la sucursal de la caja local $rwcl[0] a $rwcl[4]\r\n";
		}
		@mysql_free_result($rsm);
		$xTbl		= new cSQLTabla();
		//array de tablas
		$arrTab		= $xTbl->getTablasConOperaciones();
		unset($arrTab["socio_general"]);
		unset($arrTab["general_colonias"]);

		//array de socios_por_tabla

		foreach ( $arrTab as $key => $value ){
		//Actualizar Cuentas de Credito
		$tabla		= $value;
		$xTbl->init($tabla);
		$tablaK		= $xTbl->getCampoSocio();
		
			$sql_us = "UPDATE $tabla SET $tabla.sucursal = getSucursalBySocio($tablaK)";
			$t 		= my_query($sql_us);
			$msg	.= date("H:i:s") . "\tActualizando la Tabla $tabla\r\n";
			$msg	.= $t[SYS_MSG] . "\r\n";			
		}
		return $msg;		
	}
}
/**
 * Clase manejo de información de la sucursal
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0.1
 * @package common
 * @subpackage core
 */
class cSucursal{
	private $mClave				= false;
	private $mTitularCobranza 	= "";
	private $mTitularContable	= "";
	private $mGerente			= "";
	private $mDatosInArray		= array();
	private $mHorarioDeCierre	= 16;
	private $mHorarioDeEntrada	= 8;
	private $mCajaLocalRes		= false;
	private $mRegionLocal		= false;
	private $mMunicipio			= "";
	private $mEstado			= "";
	private $mMessages			= "";
	private $mTelefono			= "";
	private $mCodigoPostal		= 0;
	private $mClaveLocalidad	= 0;
	private $mClaveDeEstado		= 0;
	private $mClaveDeColonia	= 0;
	private $mClaveDePersona	= 0;
	private $mClaveNumerica		= 0;
	private $mInit				= false;
	private $mInitPersona		= false;
	private $mDomicilioCorto	= "";
	private $mNombreLocalidad	= "";
	private $mClaveDeMunicipio	= ""; 
	
	private $mClaveDeEstadoABC	= "";
	private $mClaveDeEstadoSIC	= "";
	
	private $mCalle				= "";
	private $mNumeroExt			= "";
	private $mNumeroInt			= "";
	private $mColonia			= "";
	private $mCentroDeCosto		= 0;
	private $mOficialAML		= 0;
	
	function __construct($clave = false){	
		if ( $clave == false ){	$clave = getSucursal(); }
		$this->mClave			= $clave;
		$this->mCentroDeCosto	= DEFAULT_CENTRO_DE_COSTO;
	}
	function init($datos = false){
		$xCach 					= new cCache();
		if(!is_array($datos)){
			$datos				= $xCach->get("sucursal-" . $this->mClave);
			$sql				= "SELECT * FROM general_sucursales	WHERE	(`general_sucursales`.`codigo_sucursal` =\"" . $this->mClave . "\") ";
			$D					= ($datos == null) ? obten_filas($sql) : $datos;
		} else {
			$D					= $datos;
		}
		$this->mInitPersona		= false;
		$xT						= new cTipos();
		
		if(isset($D["titular_de_cobranza"])){
			$this->mTitularCobranza		= $D["titular_de_cobranza"];
			$this->mTitularContable		= $D["titular_de_contabilidad"];
			$this->mGerente				= $D["gerente_sucursal"];
			$this->mHorarioDeCierre		= $D["hora_de_fin_de_operaciones"];
			$this->mCajaLocalRes		= $D["caja_local_residente"];
			$this->mHorarioDeEntrada	= $D["hora_de_inicio_de_operaciones"];
			$this->mMunicipio			= $D["municipio"];
			$this->mEstado				= $D["estado"];
			$this->mCodigoPostal		= $D["codigo_postal"];
			$this->mClaveDePersona		= $D["clave_de_persona"];
			$this->mNombreLocalidad		= $D["localidad"];
			$this->mClaveNumerica		= $D["clave_numerica"];
			$this->mCentroDeCosto		= $D["centro_de_costo"];
			$this->mOficialAML			= $D["titular_de_cumplimiento"];
			$this->mOficialAML			= ($this->mOficialAML<=0) ? AML_OFICIAL_DE_CUMPLIMIENTO : $this->mOficialAML;
			$this->mDatosInArray		= $D;
			//Guardar en cache
			$xCach->set("sucursal-" . $this->mClave, $D);
			//$xL							= new cLocal($this->mClave);
			//$this->mMessages		.= "ERROR\tLa Persona " . $this->mClaveDePersona . " de Sucursal No existe\r\n";
			$this->mDomicilioCorto		= EACP_DOMICILIO_CORTO;
			$localidad					= defined("EACP_LOCALIDAD") ? EACP_LOCALIDAD : "NO_DEFINIDO";
			$this->mMunicipio			= (trim($this->mMunicipio) == "") ? EACP_MUNICIPIO : $this->mMunicipio;
			$this->mEstado				= (trim($this->mEstado) == "") ? EACP_ESTADO : $this->mEstado;
			$this->mCodigoPostal		= ((int) $this->mCodigoPostal == 0) ? EACP_CODIGO_POSTAL : $this->mCodigoPostal;
			$this->mNombreLocalidad		= (trim($this->mNombreLocalidad) == "") ? $localidad : $this->mNombreLocalidad;
			$this->mClaveDeMunicipio	= EACP_CLAVE_DE_MUNICIPIO;
			$this->mClaveDeEstado		= EACP_CLAVE_NUM_ENTIDADFED;
			$this->mClaveDeEstadoABC	= EACP_CLAVE_DE_ENTIDADFED;
			$this->mClaveDeEstadoSIC	= EACP_CLAVE_DE_ENTIDAD_SIC;
			$this->mClaveLocalidad		= EACP_CLAVE_DE_LOCALIDAD;
			$this->mCalle				= EACP_DOMICILIO_CALLE;
			$this->mNumeroExt			= EACP_DOMICILIO_NUM_EXT;
			$this->mNumeroInt			= EACP_DOMICILIO_NUM_INT;
			$this->mColonia				= EACP_COLONIA;
			if(SISTEMA_CAJASLOCALES_ACTIVA == true){
				$xCL	= new cCajaLocal($this->mCajaLocalRes);
				if($xCL->init() == true){
					$this->mRegionLocal	= $xCL->getRegion();
				}
			}
			if(setNoMenorQueCero($this->mClaveDePersona) > 0 AND $this->mClaveDePersona != DEFAULT_SOCIO){
			$xSoc						= new cSocio($this->mClaveDePersona);
			if($xSoc->existe($this->mClaveDePersona) == true){
				if( $xSoc->init() == true){
					//verificar si existe domicilio
					$xViv		= new cPersonasVivienda($this->mClaveDePersona);
					$xDB		= new cSQLTabla(TPERSONAS_DIRECCIONES);
					$sql 		= $xDB->getQueryInicial() . " WHERE socio_numero=" . $this->mClaveDePersona . "  ORDER BY principal DESC, fecha_alta DESC LIMIT 0,1";
					$DCOL		= $xCach->get("sucursal-dom-". $this->mClave);
					if($DCOL == null){
						$DCOL		= obten_filas($sql);
						$xCach->set("sucursal-dom-". $this->mClave, $DCOL);
					}
					$this->mTelefono	= $xSoc->getTelefonoPrincipal();
					if(isset($DCOL["idsocios_vivienda"]) ){
						$xViv->init(false, $DCOL);
						$this->mMunicipio			= $xViv->getMunicipio();
						$this->mEstado				= $xViv->getEstado(OUT_TXT);
						$this->mCodigoPostal		= $xViv->getCodigoPostal();
						$this->mClaveDeEstado		= $xViv->getClaveDeEstado();
						$this->mDomicilioCorto		= $xViv->getCalleConNumero();
						$this->mNombreLocalidad		= $xViv->getCiudad();
						$this->mClaveLocalidad		= $xViv->getClaveDeLocalidad();
						$this->mInitPersona			= true;
						$this->mClaveDeMunicipio 	= $xViv->getClaveDeMunicipio();
						$this->mClaveDeEstado		= $xViv->getClaveDeEstado();
						$this->mClaveDeEstadoABC	= $xViv->getClaveDeEstadoABC();
						$this->mClaveDeEstadoSIC	= $xViv->getClaveDeEstadoEnSIC();
						$this->mCalle				= $xViv->getCalle();
						$this->mNumeroExt			= $xViv->getNumeroExterior();
						$this->mNumeroInt			= $xViv->getNumeroInterior();
						$this->mColonia				= $xViv->getColonia();
						
					} else {
						$this->mMessages		.= "ERROR\tLa Persona " . $this->mClaveDePersona . " de Sucursal, no tiene Domicilio\r\n";
					}
				} else {
					$this->mMessages		.= "ERROR\tLa Persona " . $this->mClaveDePersona . " de Sucursal NO EXISTE\r\n";
					$this->mInitPersona		= false;					
				}
			}
			//Guardar en la session
				if(isset($_SESSION)){
					
				}
			}
			$this->mInit					= true;
		}
		return $this->mInit;
	}
	function getClaveDeMunicipio(){ return $this->mClaveDeMunicipio; }
	function getClaveDeEstado(){ return $this->mClaveDeEstado; }
	function getClaveDeEstadoABC(){ return $this->mClaveDeEstadoABC; }
	function getClaveDeEstadoSIC(){ return $this->mClaveDeEstadoSIC; }
	function getClaveDeLocalidad(){ return $this->mClaveLocalidad; }
	function getClaveNumerica(){ return $this->mClaveNumerica; }
	function isInitPersona(){ return $this->mInitPersona; }
	function getTitularDeCobranza(){ return $this->mTitularCobranza;	}
	function getOficialDeCumplimiento(){ return $this->mOficialAML; }
	function getTitularContable(){ return $this->mTitularContable; }
	function getGerente(){ return $this->mGerente;	}
	function getDatosInArray(){	return $this->mDatosInArray; }
	function getHorarioDeCierre(){ return $this->mHorarioDeCierre;	}
	function getHorarioDeEntrada(){ return $this->mHorarioDeEntrada; }
	function getColocacionTotal($WhereCondition = ""){
		$sql	= "SELECT
					`creditos_solicitud`.`sucursal`,
					COUNT(`creditos_solicitud`.`numero_solicitud`) AS `numero`,
					SUM(`creditos_solicitud`.`monto_solicitado`)   AS `solicitado`,
					SUM(`creditos_solicitud`.`monto_autorizado`)   AS `ministrado`,
					SUM(`creditos_solicitud`.`saldo_actual`)       AS `saldo`
				FROM
					`creditos_solicitud` `creditos_solicitud`
				WHERE
					(`creditos_solicitud`.`sucursal` ='" . $this->mClave . "')
					$WhereCondition
				GROUP BY
					`creditos_solicitud`.`sucursal`";
		return obten_filas($sql);
	}
	function getCaptacionTotal($WhereCondition = ""){
		$sqlTCapt	= "SELECT
						`captacion_cuentas`.`sucursal`,
						COUNT(`captacion_cuentas`.`numero_cuenta`) AS `numero`,
						SUM(`captacion_cuentas`.`saldo_cuenta`)    AS `monto`
					FROM
						`captacion_cuentas` `captacion_cuentas`
					WHERE
						(`captacion_cuentas`.`sucursal` ='" . $this->mClave . "')
						$WhereCondition
					GROUP BY
						`captacion_cuentas`.`sucursal` ";

		return obten_filas($sqlTCapt);
	}
	function getCaptacionRetiros($WhereCondition = ""){

	}
	function getCaptacionDepositos($WhereCondition = ""){

	}
	function getSociosTotales($WhereCondition = "" ){
		$SqlSG		= "SELECT
			`socios_general`.`sucursal`,
			COUNT(`socios_general`.`codigo`) AS 'socios'
		FROM
			`socios_general` `socios_general`
		WHERE
			(`socios_general`.`sucursal` ='" . $this->mClave . "')
			$WhereCondition
		GROUP BY
			`socios_general`.`sucursal`
			";
		return obten_filas($SqlSG);
	}
	/**
	 * Funcion que retorna false?true si un credito existe
	 * @param $credito	Numero de Credito
	 * @return boolean	False/True
	 */
	function existeCredito($credito){
		//$socio		= $this->mCodigo;
		$existentes	= 0;
			$sql		= "SELECT COUNT(numero_solicitud) AS 'n' FROM creditos_solicitud WHERE numero_solicitud = $credito ";
			$existentes	= mifila($sql, "n");

		return		($existentes == 0 ) ? false : true;
	}
	/**
	 * Funcion que retorna false?true si un credito existe
	 * @param $cuenta	Numero de Credito
	 * @return boolean	False/True
	 */
	function existeCuenta($cuenta, $tipo = false){

		$existentes	= 0;
		$ByTipo		= ( $tipo == false ) ? "" : "AND (`captacion_cuentas`.`tipo_cuenta` =$tipo) ";
			$sql		= "SELECT
							COUNT(`captacion_cuentas`.`numero_socio`) AS `existentes` 
						FROM
							`captacion_cuentas`
						WHERE
							(`captacion_cuentas`.`numero_cuenta` =$cuenta) 
							$ByTipo";
			$existentes	= mifila($sql, "existentes");

		return		($existentes == 0 ) ? false : true;
	}
	function existeSocio($socio){
		$exist	= 0;
		$sql	= "SELECT COUNT(codigo) AS 'i' FROM socios_general WHERE codigo=$socio ";
		$exist	= mifila($sql, "i");
		return ( $exist == 0 ) ? false : true;
	}	
	function setValidar(){
		$msg		= "";
		$this->init();
		$cajaLocalR	= $this->mCajaLocalRes;
      	//Actualiza le socio por default a la actual sucursal
      	$sqlUS		= "UPDATE socios_general SET sucursal = '" . getSucursal() . "', cajalocal = " . $cajaLocalR . " WHERE codigo =" . DEFAULT_SOCIO . " ";
      	my_query($sqlUS);		
		//NOTE: Verifica si existe el Socio por default
		$xCL		= new cCajaLocal($cajaLocalR);
		if ( $xCL->getExistenciaSocio(DEFAULT_SOCIO) == 0 ){
			$msg	.= "EL socio por defecto no existe\r\n";
			$xSoc	= new cSocio(DEFAULT_SOCIO);
			$xSoc->add("", "PUBLICO_GENERAL", "","POR_REGISTRAR","POR_REGISTRAR", $cajaLocalR, false, "DESCONOCIDO",
			99, 99, 99, 99, 99, 1, DEFAULT_GRUPO, "", 1, "0", DEFAULT_SOCIO, getSucursal());
		}
		//Actualiza al User 99 a la CURS SUCURSAL
		$sqlUUserRoot	= "UPDATE t_03f996214fba4a1d05a68b18fece8e71
							SET sucursal='" . getSucursal() . "' WHERE idusuarios = " . DEFAULT_USER;
		my_query($sqlUUserRoot);
		$msg			.= "" . "\tActualizando ROOT a la sucursal para manejo de Ops. Huerfanas\r\n";		
		return $msg;		
	}
	function getCajaLocalResidente(){
		if($this->mCajaLocalRes == false){ $this->mCajaLocalRes	= DEFAULT_CAJA_LOCAL;	}
		return $this->mCajaLocalRes;
	}
	function getRegionLocal(){
		//TODO: Modificar y cambiar a un fallback
		if($this->mRegionLocal == false){ $this->mRegionLocal = 99;  }
		return $this->mRegionLocal;
	}
	function getMunicipio(){ return $this->mMunicipio; 	}
	function getEstado(){ return $this->mEstado;	}
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function getCodigoPostal(){ return $this->mCodigoPostal;	}
	function getClaveLocalidad(){ return $this->mClaveLocalidad; }
	function getNombreLocalidad(){ return $this->mNombreLocalidad; }
	function getDomicilioCorto(){ return $this->mDomicilioCorto; }
	function getCalle(){ return $this->mCalle; }
	function getNumeroInterior(){ return $this->mNumeroInt; }
	function getNumeroExterior(){ return $this->mNumeroExt; }
	function getColonia(){ return $this->mColonia; }
	function getCentroDeCosto(){ return $this->mCentroDeCosto; }
	function setActualizarPorPersona(){
		if($this->mInit == false){ $this->init(); }
		if($this->mInitPersona == true){
			$xSuc		= new cGeneral_sucursales();
			$xSuc->setData( $xSuc->query()->initByID($this->mClave) );
			$xSuc->calle( $this->mCalle );
			$xSuc->codigo_postal( $this->mCodigoPostal );
			$xSuc->numero_exterior( $this->mNumeroExt );
			$xSuc->numero_interior( $this->mNumeroInt );
			$xSuc->colonia( $this->mColonia );
			$xSuc->localidad( $this->mNombreLocalidad );
			$xSuc->municipio( $this->mMunicipio );
			$xSuc->estado( $this->mEstado );
			$xSuc->telefono( $this->mTelefono );
			$xSuc->query()->update()->save($this->mClave);
		}
	}	
	
}
/**
*	Clase de Funciones sobre socios
*	@version 1.1.2
*	@package common
*	@subpackage core
*	@date 18/Sept/2008
*   	@author Balam Gonzalez Luis Humberto
*/
class cSocio{
	protected $mCodigo				= false;
	protected $mNombre				= false;
	protected $mNombres				= false;
	protected $mApMaterno			= false;
	protected $mApPaterno			= false;
	protected $mRFC					= false;
	protected $mCURP				= false;
	protected $mPropietario			= false;

	protected $mMessages			= "";
	protected $mDSocioByArray		= array();
	protected $mDatosDomicilio		= false;
	protected $mDatosActividad		= array();
	protected $mDatosAports			= array();
	
	protected $mGrupoAsociado		= false;
	protected $mCajaLocal			= false;
	protected $mDependencia			= false;
	protected $mTelefonoP			= "";
	protected $mEmail			= "";
	protected $mDependientes		= 0;
	protected $mGenero				= "";
	protected $mObservaciones		= "";
	protected $mSucursal			= "";
	protected $mEstadoActual		= 10;
	protected $mRegion				= 0;

	protected $mTipoDeIngreso		= false;
	protected $mFechaDeIngreso		= false;
	protected $mDatosDeIngreso		= false;
	protected $mTipoDeIdent			= false;
	protected $mNumeroDeIdent		= "";
	protected $mFechaDeNacimiento		= "";
	protected $mTipoFiguraJu		= false;
	protected $mLugarDeNacimiento		= "";

	protected $mEstadoCivil			= false;
	protected $mDatosEstadoCivil	= false;
	protected $mUsuarioActual		= false;
	private $mUsuarioProp			= false;
	protected $mNivelDeRiesgo		= false;
	protected $mTipoRegimenMat		= false;
	protected $mPaisDeOrigen		= "";
	protected $mTituloPersona		= "";
	//Datos del IDE
	protected $mBaseGravadaIDE		= 0;
	protected $mIDEPagado			= 0;
	protected $mIDEPendiente		= 0;
	protected $mIDECalculado		= 0;
	protected $mIDEAsSet			= false;
	//Datos del IDE pero por periodo Mensual identificado por 1 - 12
	//
	protected $mSocioIniciado		= false;
	protected $mOutFormat			= OUT_HTML;
	protected $mOBDomicilio			= null;
	protected $mOBActividadE		= null;
	protected $mTCredsSaldo			= 0;
	protected $mTCredsAut			= 0;
	protected $mTCredsNum			= 0;
	protected $mTCredsActivos		= 0;
	protected $mAListaDeCreds		= array();
	protected $mObjUser				= null;
	protected $mObjEmpresa			= null;
	protected $mObjGrupoS			= null;
	protected $mORepLegal			= null;
	protected $mOTipoIngreso		= null;
	
	protected $mNoAML				= false; 
	protected $mUUID				= "";
	protected $mIDVivienda			= 0;
	protected $mIDSucursal			= null; //ID de la sucursal asociada como persona
	protected $mIDEnCache			= null;
	protected $mObjEstats			= null;
	protected $mIDNucleoRiesgo		= 0;
	
	private $mExtranjeroFechaInicial=false;
	private $mExtranjeroPermiso		= "";//clave de permiso
	private $mExtranjeroEs			= false;
	private $mExtranjeroFechaFin	= false;
	private $mNacionalidad			= "";
	//`dia_de_pago`,`tipo_de_lugar_de_pago`,`tipo_de_afiliacion`,`datos_de_emergencia`,`grado_academico`
	private $mMembresiaTipo			= 0;
	private $mMembresiaLugarPago	= 0;
	private $mMembresiaGradoAca		= 0;
	private $mMembresiaDiaPago		= 0;
	private $mMembresiaID			= "";
	private $mMembresiaAcc			= "";
	//private $mProfesion				= ""; 
	private $mValidacionERRS		= 0;
	private $mValidacionWARNS		= 0;
	private $mIDInterna				= "";
	
	function __construct($codigo_de_socio, $init = false){

		$this->mCodigo			= setNoMenorQueCero($codigo_de_socio);
		$this->mUsuarioActual	= getUsuarioActual();
		$this->mIDEnCache		= EACP_CLAVE . ".ficha.$codigo_de_socio";
		$this->mNacionalidad	= EACP_CLAVE_DE_PAIS;
		if ( $init == true AND $this->mCodigo >= DEFAULT_SOCIO ){ $this->init(); }
	}
	function initSocio($ArrDatos = false){ 	return $this->init($ArrDatos);	}
	/**
	 * Inicializa los datos del socio en un array
	 * @param $ArrDatos	array	Array heredada
	 * @return array			Array Resultante
	 */
	function init($ArrDatos = false){
		$D 			= array();
		$xCach		= new cCache();
		
		if( setNoMenorQueCero($this->mCodigo) <= 0 ){
			$this->mSocioIniciado		= false;
		} else {
			if (!is_array($ArrDatos) ){
				$ArrDatos	= $xCach->get("persona-". $this->mCodigo);
				if (!is_array($ArrDatos) ){
					$sql 		= "SELECT * FROM socios_general WHERE codigo=" . $this->mCodigo . " LIMIT 0,1";
					$ArrDatos	= obten_filas($sql);
				}
				$D		= $ArrDatos;
			} else {
				$D		= $ArrDatos;
				//setLog("Iniciado con datos " . $this->mCodigo);
			}
			$ArrDatos	= null;
			if(isset($D["codigo"])){
				$xSoc		= new cSocios_general();
				$xSoc->setData($D);
				/*		fechaentrevista, fechaalta, estatusactual, region, cajalocal,
				,genero, eacp, observaciones, idusuario,
				 grupo_solidario, personalidad_juridica,
				regimen_conyugal, sucursal, fecha_de_revision */
				$this->mNombre				= $D["nombrecompleto"];
				//setLog($D["nombrecompleto"]);
				$this->mApMaterno			= $D["apellidomaterno"];
				$this->mApPaterno			= $D["apellidopaterno"];
				$this->mTipoDeIngreso		= $D["tipoingreso"];
				$this->mEstadoCivil			= $D["estadocivil"];
				$this->mGrupoAsociado		= $D["grupo_solidario"];
				$this->mCajaLocal			= $D["cajalocal"];
				$this->mDependencia			= $D["dependencia"];
				$this->mRFC					= $D["rfc"];
				$this->mCURP				= $D["curp"];
				$this->mPropietario			= $D["idusuario"];
				$this->mFechaDeIngreso		= $D["fechaalta"];
				$this->mTipoDeIdent			= $D["tipo_de_identificacion"];
				$this->mNumeroDeIdent		= $D["documento_de_identificacion"];
				$this->mEmail				= $D["correo_electronico"];
				$this->mTelefonoP			= $D["telefono_principal"];
				$this->mDependientes		= $D["dependientes_economicos"];
				$this->mSucursal			= $D["sucursal"];
				$this->mRegion				= $D["region"];
				//Agrega Valores de array pedidos
				$D["fecha_de_nacimiento"]	= $D["fechanacimiento"];
				$D["codigo_de_socio"]		= $D["codigo"];
				$this->mFechaDeNacimiento	= $D["fechanacimiento"];
				$this->mTipoFiguraJu		= $D["personalidad_juridica"];
				$this->mLugarDeNacimiento	= $D["lugarnacimiento"];
				$this->mTituloPersona		= $xSoc->titulo_personal()->v();
				$this->mPaisDeOrigen		= strtoupper($xSoc->pais_de_origen()->v(OUT_TXT));
				$this->mGenero				= $xSoc->genero()->v();
				$this->mNivelDeRiesgo		= $xSoc->nivel_de_riesgo_aml()->v();
				$this->mObservaciones		= $xSoc->observaciones()->v(OUT_TXT);
				$this->mEstadoActual		= $xSoc->estatusactual()->v();
				$this->mTipoRegimenMat		= $xSoc->regimen_conyugal()->v();
				$this->mExtranjeroEs		= ($xSoc->nacionalidad_extranjera()->v() == 1) ? true : false;
				$this->mIDInterna			= $xSoc->idinterna()->v();
				$this->mUsuarioProp			= $xSoc->idusuario()->v();
				//setLog($this->mCodigo . " --Nacionalidad " . $xSoc->nacionalidad_extranjera()->v());
				//$this->mTituloP			= $xSoc->titulo_personal()->v();
				if($this->mExtranjeroEs == true){
					$this->getDatosExtrajero();
				}
				if(PERSONAS_CONTROLAR_POR_APORTS == true){
					$this->getDatosColegiacion();
				}
				//
				$this->mDSocioByArray		= $D;
				
				$this->mSocioIniciado		= true;
				//Guardar en Cache
				$xCach->set("persona-". $this->mCodigo, $this->mDSocioByArray, $xCach->EXPIRA_UNHORA);
				$D							= null;
			}
		}
		if($this->mSocioIniciado == false){ $this->mMessages	.= "ERROR\tAl Iniciar a la persona : " . $this->mCodigo . "\r\n";}
		return $this->mSocioIniciado;
	}
	/**
	 * Retorna en un Array los datos de un Domicilio del Socio segun tipo solicitado
	 * @param 	integer	$tipo	 Tipo de Domicilio
	 * @return	array	Array de Datos del socio
	 */
	function getDatosDomicilio($tipo = false){
		$this->mIDVivienda 		= setNoMenorQueCero($this->mIDVivienda);
		if($this->mOBDomicilio == null){ $this->getODomicilio($tipo); }
		if($this->mOBDomicilio != null){ 
			$this->mIDVivienda 	= ($this->mIDVivienda<=0) ? $this->mOBDomicilio->getClaveUnica() : $this->mIDVivienda;
		}
		return $this->mDatosDomicilio;
	}

	function getODomicilio($tipo = false){
		$principal	= ($tipo == 99 OR setNoMenorQueCero($tipo) <=0) ? false : true;
		$xObj		= new cPersonasVivienda($this->mCodigo);
		$xObj->init($principal);
		if( $xObj->isInit() == true){
			$this->mDatosDomicilio	= $xObj->getDatosInArray();
			$this->mOBDomicilio		= $xObj;
			$this->mMessages		= "OK\tDomicilio Iniciado con el ID " . $xObj->getClaveUnica() . "\r\n";
		}
		return $this->mOBDomicilio;
	}
	/**
	 * Retorna el domicilio del socio
	 * @param $Tipo		Integer		//Tipo de Domicilio
	 * @return string				//Domicilio del Asociado
	 */
	function getDomicilio($Tipo = 99, $MostrarTelefonos = false){
		$xH			= new cHObject();
		if ( ($this->mDatosDomicilio == false) OR (!is_array($this->mDatosDomicilio) ) ){
			$DDom 		= $this->getDatosDomicilio($Tipo);
		} else {
			$DDom		= $this->mDatosDomicilio;
		}
		$Calle			= $DDom["calle"];
		$NumExt			= $DDom["numero_exterior"];
		$NumInt			= $DDom["numero_interior"];
		$Colonia		= $DDom["colonia"];
		$CP				= $DDom["codigo_postal"];
		$localidad		= $DDom["localidad"];
		$estado			= $DDom["estado"];

		$telMovil		= trim($DDom["telefono_movil"]);
		$telFijo		= trim($DDom["telefono_residencial"]);

		$referen		= trim($DDom["referencia"]);
		$Tels			= "";

			if (strlen($referen) > 2){
				$referen	= ", $referen";
			}

			if ( strlen($telMovil) < 7 ){
				$telMovil = "";
			}

			if ( strlen($telFijo) < 5 ){
				$telFijo = "";
			}

			if ($telFijo != "" or $telMovil != "") {
				$Tels	= "Tel(s) $telFijo $telMovil";
				$Tels 	= trim($Tels);
			}

			$Calle 	= str_ireplace("calle", "", $Calle);

			if ( $NumInt == 0 or  $NumInt == "" ){
				$NumInt		= "";
			}

			if ( $NumInt != "" ){
				$NumInt = "-" . $NumInt;
			}

			$Numero	= trim($NumExt . $NumInt );
				
			$CP 	= ( strlen($CP) < 4) ? "" : " C.P. $CP, "; 

			$Tels	= ($MostrarTelefonos == false) ? "" : " $Tels, ";

			$Direccion 	= trim("$Calle, $Numero, $Colonia, $CP$Tels $localidad, $estado");
			$Direccion	= str_ireplace(",  ,", ",", $Direccion);
			$Direccion	= str_ireplace(", ,", ",", $Direccion);
			$Direccion	= str_ireplace("   ", " ", $Direccion);
			$Direccion	= str_ireplace("  ", " ", $Direccion);
			if(trim($Direccion) == ", ,"){
				$Direccion	= "";
			}
		return  $xH->Out($Direccion, $this->mOutFormat);
	}
	/**
	 * Obtiene el Nombre completo del Socio
	 * @return array		Nombre del Socio
	 */
	function getNombreCompleto($out = false){
		$out	= ($out == false) ? $this->mOutFormat : $out;
		$xH	= new cHObject();
		$n	 = (trim($this->mNombre . " " .$this->mApPaterno . " " . $this->mApMaterno));
		return $xH->Out($n, $out);
	}
	function getNombre(){ return $this->mNombre;	}
	function getApellidoPaterno(){ return $this->mApPaterno; }
	function getApellidoMaterno(){ return $this->mApMaterno;}
	function getCURP($validar = false){
		$curp	= $this->mCURP;
		if($validar == true){
			$xMex	= new cReglasDePais();
			
			if($this->getEsPersonaFisica() == true){
				$curp		= $xMex->getValidIDPoblacional($curp);
				if($xMex->isValid() == false){ $this->mMessages	.= "ERROR\tCURP $curp Invalido\r\n"; }
					
			} else {
				$curp					= "";
				$this->mMessages		.= "ERROR\tCURP $curp No para personas morales\r\n";
			}
		}
		return $curp;
	}
	function getRFC($validar = false, $retornar = false){
		$rfc	= $this->mRFC;
		if($validar == true){
			$xReg	= new cReglasDePais();
			$rfc	= $xReg->getValidIDFiscal($rfc);
			if($xReg->isValid() == false AND $retornar == false){
				$rfc	="";
				$this->mMessages	.= "ERROR\tRFC $rfc Invalido\r\n";
			}
		}
		return $rfc;
	}
	function getGenero(){ return $this->mGenero; }
	function getPersonalidadJuridica(){ return $this->mTipoFiguraJu; }
	function getFechaDeNacimiento(){ return $this->mFechaDeNacimiento;	}
	function getFechaDeRegistro(){ return$this->mFechaDeIngreso; }
	function getTelefonoPrincipal(){ return $this->mTelefonoP; }
	function getFiguraJuridica(){ return $this->mTipoFiguraJu; }
	function getEstadoCivil(){ return $this->mEstadoCivil; }
	function getLugarDeNacimiento(){ return $this->mLugarDeNacimiento; }
	function getCorreoElectronico(){ return (filter_var($this->mEmail, FILTER_VALIDATE_EMAIL)) ? $this->mEmail : ""; }
	function getDependientesEconomico(){ return $this->mDependientes; }
	function getTituloPersonal(){ return $this->mTituloPersona; }
	function getPaisDeOrigen(){ return $this->mPaisDeOrigen; }
	function getNumeroDeCajaLocal(){ return $this->mCajaLocal; }
	function getCodigo(){ return $this->mCodigo;}
	function getClaveDePersona(){ return $this->mCodigo; }
	function getClaveDeIFE(){ $ife = ( $this->mTipoDeIdent == 1) ? $this->mNumeroDeIdent : "" ; return $ife;	}
	function getTipoDeIdentificacion(){ return $this->mTipoDeIdent; }
	function getClaveDeIdentificacion(){ return $this->mNumeroDeIdent; }
	function getIDInterna(){ return $this->mIDInterna; }
	function getClaveLicenciaConducir(){ $licencia	= ( $this->mTipoDeIdent == 3) ? $this->mNumeroDeIdent : "" ; return $licencia;	}
	function getDatosSocioInArray(){ return $this->mDSocioByArray; }
	function getTutor(){ $socio_tutor	= DEFAULT_SOCIO; }
	function getTipoDeIngreso(){ return $this->mTipoDeIngreso; }
	function getTipoGenero(){ return $this->mGenero; }
	function getTipoDeMembresia(){ return $this->mMembresiaTipo; }
	function getTipoRegimenMatrimonial(){ return $this->mTipoRegimenMat; }
	function getClaveDeGrupo(){ return $this->mGrupoAsociado; } 
	function set($clave_de_persona){ $this->mCodigo = $clave_de_persona;}
	function setResetEmpresa($empresa = FALLBACK_CLAVE_EMPRESA){	$sql	= "UPDATE socios_general SET dependencia=$empresa WHERE codigo =" . $this->mCodigo; $xQL = new MQL();  $xQL->setRawQuery($sql);	}
	function setViviendaByDependencia(){}
	function getDatosDeCreditos(){ $this->getOEstats()->initDatosDeCredito();	return $this->getOEstats()->getDatosDeCreditos(); }
	/**
	 * Obtiene el Monto Total de Creditos Comprometidos
	 * @return float	Monto de los creditos comprometidos
	 */
	function getCreditosComprometidos(){ return $this->getOEstats()->getTotalCreditosSaldo(); }
	function getCreditoMaximo(){
		$xCache		= new cCache();
		$idcx		= "persona-creditomaximo-". $this->mCodigo;
		$monto		= $xCache->get($idcx);
		if($monto === null){
			$xQL	= new MQL();
			$DMonto	= $xQL->getDataRow("SELECT MAX(monto_autorizado) AS 'CreditoMaximo' FROM creditos_solicitud WHERE numero_socio = " . $this->mCodigo);
			$monto	= setNoMenorQueCero($DMonto["CreditoMaximo"]);
		}
		return $monto;
	}
	function getTotalCaptacionActual(){ return $this->getOEstats()->getTotalCaptacionActual();	}
	function getTotalColocacionActual($tipo_de_convenio = false){
		$datos	= $this->getOEstats()->getTotalColocacionActual($tipo_de_convenio);
		return $datos;
	}
	
	function getIDEPagadoByPeriodo($fecha = false){
		if ( $fecha == false ){
			$fecha = fechasys();
		}

		$dia_inicial	= date("Y-m-", strtotime($fecha) ). "01";
		$dia_final		= date("Y-m-t", strtotime($fecha) ) ;
		$mvto_ide		= 235;
		$idePagado		= 0;
		$xT				= new cTipos();
		
		if ( !isset($this->mIDEPagado) OR $this->mIDEPagado <= 0){

			$sqlIDE = "SELECT
						`operaciones_mvtos`.`tipo_operacion`,
						`operaciones_mvtos`.`socio_afectado`,
						COUNT(`operaciones_mvtos`.`idoperaciones_mvtos`) AS 'numero',
						SUM(`operaciones_mvtos`.`afectacion_real`) AS 'monto'
					FROM
						`operaciones_mvtos` `operaciones_mvtos`
					WHERE
						(`operaciones_mvtos`.`tipo_operacion` = $mvto_ide)
						AND
						(`operaciones_mvtos`.`socio_afectado` =" . $this->mCodigo . ")
						AND
						(`operaciones_mvtos`.`fecha_operacion` >='$dia_inicial')
						AND
						(`operaciones_mvtos`.`fecha_operacion` <='$dia_final')
					GROUP BY
						`operaciones_mvtos`.`tipo_operacion`,
						`operaciones_mvtos`.`socio_afectado` ";
			$MD			= obten_filas($sqlIDE);
			$idePagado	= $xT->cFloat($MD["monto"],2);
			if ( !isset($idePagado) ){
				$idePagado = 0;
			}
			$this->mIDEPagado = $idePagado;
			unset($MD);
		}
		return $this->mIDEPagado;

	}
	function getIDExPagarByPeriodo($fecha = false, $monto = 0, $tipodepago = "efectivo"){
		$fecha			= ( $fecha == false ) ? fechasys() : $fecha;
		$xF				= new cFecha(0, $fecha);
		$mes 			= $xF->mes();
		$anno			= $xF->anno();

		$dia_inicial	= $xF->getDiaInicial();
		$dia_final		= $xF->getDiaFinal();
		$monto			= ( $tipodepago != "efectivo" ) ? 0 : $monto;
		$xT				= new cTipos();
		$monto			= 0;
		if(CAPTACION_IMPUESTOS_A_DEPOSITOS_ACTIVO == true){
		$sqlGravados	= "SELECT
								SUM(`operaciones_mvtos`.`afectacion_real`) AS 'monto'
							FROM
								`operaciones_mvtos` `operaciones_mvtos`
									INNER JOIN `operaciones_recibos` `operaciones_recibos`
									ON `operaciones_mvtos`.`recibo_afectado` = `operaciones_recibos`.
									`idoperaciones_recibos`
										INNER JOIN `eacp_config_bases_de_integracion_miembros`
										`eacp_config_bases_de_integracion_miembros`
										ON `operaciones_mvtos`.`tipo_operacion` =
										`eacp_config_bases_de_integracion_miembros`.`miembro`
						WHERE
							(`operaciones_mvtos`.`fecha_afectacion` >='$dia_inicial')
							AND
							(`operaciones_mvtos`.`fecha_afectacion` <='$dia_final')
							AND
							(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 2600)
							AND
							(`operaciones_mvtos`.`socio_afectado` = " . $this->mCodigo ." )
							AND
							(`operaciones_recibos`.`tipo_pago` = 'efectivo' )
						GROUP BY
							`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
							`operaciones_mvtos`.`socio_afectado`,
							`operaciones_recibos`.`tipo_pago` ";

			$DGrav						= obten_filas($sqlGravados);
			/**
			 * Obtiene la formula IDE
			 */
			$base_gravada				= ( isset($DGrav["monto"]) ) ? $DGrav["monto"] : 0;
			$base_de_calculo			=  $base_gravada + $monto;
			$base_gravada				= $base_de_calculo;
			
			$this->mMessages 			.= "WARN\tINF_IDE\tLa Base Gravada Registrada es $base_gravada, y el Monto de Referencia es $monto\r\n";

			$tasa_ide					= TASA_IDE;
			$excencion					= EXCENCION_IDE;
			$this->mMessages 			.= "WARN\tINF_IDE\tLa Tasa de IDE es $tasa_ide, y la Excencion es de $excencion\r\n";
			$ide						= 0;
			$cFormulaIDE				= new cFormula("formula_ide");



			 $ide_pagado 				= $this->getIDEPagadoByPeriodo($fecha);

			 $this->mIDEPagado			= $ide_pagado;

			 if ( $base_de_calculo > EXCENCION_IDE ){
			 	eval( $cFormulaIDE->getFormula() );
			 	$this->mIDECalculado	= $xT->cFloat($ide, 2);
			 	$this->mBaseGravadaIDE	= $xT->cFloat($base_gravada,2);
			 	$this->mMessages 		.= "WARN\tINF_IDE\tA la Fecha $fecha, La Base de Calculo es " . $this->mBaseGravadaIDE . ", el IDE Pagado es " . $this->mIDEPagado . " y el IDE Calculado es " . $this->mIDECalculado . "\r\n";
			 } else {
			 	$this->mMessages 		.= "OK\tNO_IDE\tA la Fecha $fecha, no hay IDE por que el Monto Exento(" . EXCENCION_IDE . ") es Mayor a la Base de Calculo $base_de_calculo \r\n";
			 }
			 /**
			  * Disminuir el IDE Retenido
			  */
			 $this->mIDEPendiente		= ($ide - $ide_pagado);

			 $this->mMessages 			.= "WARN\tINF_IDE\tEl IDE es de $ide, IDE Pagado de $ide_pagado, IDE por pagar " . $this->mIDEPendiente . ", Base de Calculo $base_de_calculo\r\n";
			 $this->mIDEAsSet			= true;
			 $monto						= $this->mIDEPendiente;
		}
		return $monto;
	}
	/**
	 * Retorna la Base de IDE en una fecha Dada, generado por la Funcion getIDExPagarByPeriodo
	 * @param $fecha
	 * @return float	Base Gravada del IDE
	 */
	function getBaseGravadaIDE($fecha = false){
		if ($this->mIDEAsSet == false){
			$this->getIDExPagarByPeriodo($fecha);
		}
		return $this->mBaseGravadaIDE;
	}
	function getIDECalculado($fecha = false){
		if ($this->mIDEAsSet == false){
			$this->getIDExPagarByPeriodo($fecha);
		}
		return $this->mIDECalculado;
	}
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }

	function getBuildScoring($put = OUT_HTML ){
		$sql = "SELECT numero_socio, fecha_de_calificacion,
			((puntaje_de_proveedores + puntaje_de_clientes + puntaje_de_organizacion +
				 puntaje_de_fuerza_laboral + puntaje_capacidad_de_pago + puntaje_caracter + puntaje_factor_macro) / 8) AS 'puntaje'
    			FROM socios_scoring_simple
    			WHRERE numero_socio = " . $this->mCodigo . " LIMIT 0,1";
		return mifila($sql, "puntaje");

	}
	function setNuevoScoring($proveedores, $clientes, $organizacion, $laboral, $fecha = false ){
		if ($fecha == false){
			$fecha	= fechasys();
		}

		//Caracter
		$sqlCaracter = "SELECT
							`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
							COUNT(`socios_relaciones`.`idsocios_relaciones`)      AS `numero`,
							SUM(`socios_relaciones`.`calificacion_del_referente`) AS `puntaje`

						FROM
							`socios_relaciones` `socios_relaciones`
								INNER JOIN `eacp_config_bases_de_integracion_miembros`
								`eacp_config_bases_de_integracion_miembros`
								ON `socios_relaciones`.`tipo_relacion` =
								`eacp_config_bases_de_integracion_miembros`.`miembro`
						WHERE
							(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` =5003) AND
							(`socios_relaciones`.`socio_relacionado` =" . $this->mCodigo . ")
						GROUP BY
							`socios_relaciones`.`socio_relacionado` ";
		$DCaracter	= obten_filas($sqlCaracter);
		$caracter	= $DCaracter["puntaje"] / $DCaracter["numero"];
		//Capacidad de pago
		//capacidad_compromedida_de_credito = (promedio_creditos_solicitados / promedio_dias)
		//sumas_flujo_efvo
		//sumas_de_credito_diario
		$sqlCpromD	= "SELECT
							AVG(`creditos_solicitud`.`monto_autorizado`) AS 'creditos',
							AVG(`creditos_solicitud`.`plazo_en_dias`) AS 'dias',
							`creditos_solicitud`.`numero_socio`
						FROM
							`creditos_solicitud` `creditos_solicitud`
						WHERE
							(`creditos_solicitud`.`numero_socio` =37002)
						GROUP BY
							`creditos_solicitud`.`numero_socio`";
		$DCPD						= obten_filas($sqlCpromD);
		$credito_promedio_diario	= $DCPD["creditos"] / $DCPD["dias"];
		$factor_maximo_de_deuda		= 0.30;

		//capacidad_patrimonial_diaria
		$sqlPatDiario		= "SELECT
									`socios_patrimonio`.`socio_patrimonio`,
									COUNT(`socios_patrimonio`.`idsocios_patrimonio`) AS `numero`,
									SUM(`socios_patrimonio`.`monto_patrimonio` *
									`socios_patrimoniotipo`.`subclasificacion`) AS 'capital'
								FROM
									`socios_patrimonio` `socios_patrimonio`
										INNER JOIN `socios_patrimoniotipo` `socios_patrimoniotipo`
										ON `socios_patrimonio`.`tipo_patrimonio` = `socios_patrimoniotipo`.
										`idsocios_patrimoniotipo`
								WHERE
									(`socios_patrimonio`.`socio_patrimonio` =" . $this->mCodigo . ")
								GROUP BY
									`socios_patrimonio`.`socio_patrimonio`";
		$DPat				= obten_filas($sqlPatDiario);


		$patrimonio_diario	= $DPat["capital"] / EACP_DIAS_INTERES;
		//flujo diario
		$sqlFlujo = "SELECT
							`creditos_flujoefvo`.`socio_flujo`,

							SUM(`creditos_flujoefvo`.`afectacion_neta`) AS 'capacidad',
							COUNT(`creditos_flujoefvo`.`idcreditos_flujoefvo`) AS `numero`
						FROM
							`creditos_flujoefvo` `creditos_flujoefvo`
						WHERE
							(`creditos_flujoefvo`.`socio_flujo` =" . $this->mCodigo . ")
						GROUP BY
							`creditos_flujoefvo`.`socio_flujo`";
		$DFlujo 				= obten_filas($sqlFlujo);
		$maximo_endeudable		= ($DFlujo["capacidad"] + $DPat["capital"]) * $factor_maximo_de_deuda;
		$capacidad_por_credito	= $maximo_endeudable / $credito_promedio_diario;

		//endeudamiento_promedio	= activos - pasivos /360
	$sql = "INSERT INTO socios_scoring_simple(clave_de_persona,
				fecha_de_calificacion,
				puntaje_de_proveedores,
				puntaje_de_clientes,
				puntaje_de_organizacion,
				puntaje_de_fuerza_laboral,
				puntaje_capacidad_de_pago,
				puntaje_caracter
				puntaje_factor_macro)
				VALUES(" . $this->mCodigo . ", '$fecha', $proveedores,
					$clientes,
					$organizacion,
					$laboral,
					$capacidad_por_credito,
					,$caracter,
    			0.5)";
		$x = my_query($sql);

	}
	/**
	 * Genera un Array con Datos del Socio
	 * @return array
	 */
	function getDatosInArray(){ if($this->mSocioIniciado == false ){ $this->init(); } 	return $this->mDSocioByArray;	}
	
	function initByIDLegal($idlegal = ""){
		$this->mMessages	.= "Buscar socio con CURP $idlegal \r\n";
		//si NO hay socio buscar
		$patron		= "/[^a-zA-Z0-9]/";///"/[#\$%-_!\?,\*]|[[:space:]]/"; [^a-zA-Z0-9\s\p{P}]
		$idlegal		= preg_replace($patron, "", $idlegal);
		$idlegal		= substr($idlegal, 0,10);
		$idlegal	= trim( strtoupper($idlegal) );
		$sqlSocio	= "SELECT * FROM socios_general WHERE (curp LIKE '%$idlegal%') OR (rfc LIKE '%$idlegal%') LIMIT 0,1";
		//setLog($sqlSocio);
		$datos		= obten_filas($sqlSocio);
		if(isset($datos["codigo"])){
			$this->mCodigo		= $datos["codigo"];
			$this->init($datos); $this->mSocioIniciado = true;
		} else {
			$this->mSocioIniciado	= false;
		}
		return $this->mSocioIniciado;
	}

	function getRiesgoComunPorAvales($explain = false){	$xRisk	= new cPersonasRiesgosDeCredito($this->mCodigo); return $xRisk->getEndeudamientoPorAvalesOtorgados($explain);	}
	function getRiesgoComunPorNucleoFamiliar($explain = false){$xRisk	= new cPersonasRiesgosDeCredito($this->mCodigo); return $xRisk->getEndeudamientoPorFamilia($explain);	}

	/**
	 * funcion que genera un codigo html con el listado de obligados solidarios
	 * @param $style		Estilo de muestra ficha/firma
	 * @return string		Codigo HTML del listado de Obligados Solidarios solidarios/avales
	 * @deprecated @since 2016.02.01
	 */
	function getCoResponsables($style = "ficha", $TipoDeResponsables = "5004", $documento = false){
		$arrEQBase_vs_Tipos		= array (
										"5004"			=> 5004,
										"5002" 			=> 5002,
										"solidarios" 	=> 5004,
										"avales" 		=> 5002
										);
		$arrEQTituloF			= array (
										"5004"			=> "EL OBLIGADO SOLIDARIO",
										"5002" 			=> "EL AVAL",
										"solidarios" 	=> "EL OBLIGADO SOLIDARIO",
										"avales" 		=> "EL AVAL"
										);
		$codigo_de_base		= $arrEQBase_vs_Tipos[ $TipoDeResponsables ];
		$ByDocumento	= "";
		if ( $documento != false ){
			$ByDocumento	= " AND `socios_relaciones`.`credito_relacionado`  = $documento ";
		}

		$sql = "SELECT socios_relaciones.idsocios_relaciones AS 'num',
						socios_relacionestipos.descripcion_relacionestipos AS 'relacion',
						socios_consanguinidad.descripcion_consanguinidad AS 'consanguinidad',
						CONCAT(socios_relaciones.nombres ,' ', socios_relaciones.apellido_paterno, ' ', socios_relaciones.apellido_materno) AS 'nombre',
						socios_relaciones.curp AS 'curp',
						CONCAT(socios_relaciones.telefono_residencia, '; ' , socios_relaciones.telefono_movil)  AS 'telefonos',
						socios_relaciones.domicilio_completo AS 'domicilio',
						`socios_relaciones`.`numero_socio`
				FROM
					`socios_relaciones` `socios_relaciones`
						INNER JOIN `eacp_config_bases_de_integracion_miembros`
						`eacp_config_bases_de_integracion_miembros`
						ON `socios_relaciones`.`tipo_relacion` =
						`eacp_config_bases_de_integracion_miembros`.`miembro`
							INNER JOIN `socios_relacionestipos` `socios_relacionestipos`
							ON `socios_relaciones`.`tipo_relacion` = `socios_relacionestipos`.
							`idsocios_relacionestipos`
								INNER JOIN `socios_consanguinidad` `socios_consanguinidad`
								ON `socios_relaciones`.`consanguinidad` =
								`socios_consanguinidad`.`idsocios_consanguinidad`

				WHERE
					(`socios_relaciones`.`socio_relacionado` = " .  $this->mCodigo . ")
					AND
					(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = $codigo_de_base )
					$ByDocumento
				ORDER BY
					`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
					`socios_relaciones`.`credito_relacionado`	";
		$xQL	= new MQL();
		$rs 	= $xQL->getDataRecord($sql);
		$tbl	= "";
		$tds	= "";

		foreach($rs as $rw){
			if ($style == "firmas"){
			$tds	.= "
					<tr>
			            <td>" . $arrEQTituloF[ $TipoDeResponsables ] . "</td>
			          </tr>
			          <tr>
			            <td>
			              <br /><br /><br /><tr>
			              </td>
			          </tr>
			          <tr>
			            <td>" . $rw["nombre"] . "</td>
					</tr>
					";
			} else {
				$tds	.= "
						<tr>
							<th id=\"id-relacion-" . $rw["num"] . "\" colspan='4'>" . $rw["relacion"] . "</th>
						</tr>
						<tr>
							<td>Socio Num.</td>
							<th>" . $rw["numero_socio"] . "</th>
							<td>Consanguinidad</td>
							<th>" . $rw["consanguinidad"] . "</th>
						</tr>
						<tr>
							<td>Nombre</td>
							<td colspan='3'>" . $rw["nombre"] . "</td>
						</tr>
						<tr>
							<td>Telefono(s)</td>
							<td>" . $rw["telefonos"] . "</td>
							<td>C.U.R.P.</td>
							<td>" . $rw["curp"] . "</td>
						</tr>
						<tr>
							<td>Domicilio</td>
							<td colspan='3'>" . $rw["domicilio"] . "</td>
						</tr>
						";
			}
		}
		if ( $style == "firmas"){
		$tbl = "
			<table border='0' width='100%' align='center'>
				$tds
			</table>
			";
		} else {
		$tbl = "
			<fieldset>
			<legend>|&nbsp;&nbsp;&nbsp;INFORMACI&Oacute;N DE(LOS) OBLIGADOS SOLIDARIOS&nbsp;&nbsp;&nbsp;|</legend>
			<table border='0' width='100%' align='center'>
				$tds
			</table>
			</fieldset>";
		}
		return $tbl;
	}
	/**
	 * Funcion que retorna los Datos del Grupo Solidario en un Array
	 * @return array Datos del Grupo
	 */
	function getDatosDeGrupoSolidario(){}

	function getDatosEstadoCivil_InArray($tipo = 99){
		if ( $this->mEstadoCivil != false ){
			$tipo		= $this->mEstadoCivil;
		}
		$sql = "SELECT idsocios_estadocivil, descripcion_estadocivil, valor_scoring
    			FROM socios_estadocivil
    			WHERE idsocios_estadocivil = $tipo LIMIT 0,1";
		$this->mDatosEstadoCivil	= obten_filas($sql);
		return $this->mDatosEstadoCivil;
	}
	function getDatosActividadEconomica($tipo = false){
		$ByTipo	= "";
		if ($tipo != false ){
			$ByTipo	= "AND
				(`socios_aeconomica`.`tipo_aeconomica` = $tipo)";
		}
		$xDB	= new cSQLTabla(TPERSONAS_ACTIVIDAD_ECONOMICA);
		$sql = $xDB->getQueryInicial() . "
			WHERE
				(`socios_aeconomica`.`socio_aeconomica` =" .  $this->mCodigo . ")
				$ByTipo
			ORDER BY
				`socios_aeconomica`.`monto_percibido_ae` DESC
			LIMIT 0,1";
			$this->mDatosActividad	= obten_filas($sql);
			return $this->mDatosActividad;
	}

	function getDatosRelacionInArray($Id = false){
		$ById	= "";
		if ( $Id != false ){
			$ById	= " AND
						(`socios_relaciones`.`idsocios_relaciones` = $Id) ";
		}
		$sql	= "SELECT
						`socios_relaciones`.`idsocios_relaciones`,
						`socios_relaciones`.`socio_relacionado`,
						`socios_relaciones`.`credito_relacionado`,
						`socios_relaciones`.`tipo_relacion`,
						`socios_relaciones`.`numero_socio`,
						`socios_relaciones`.`nombres`,
						`socios_relaciones`.`apellido_paterno`,
						`socios_relaciones`.`apellido_materno`,
						`socios_relaciones`.`domicilio_completo`,
						`socios_relaciones`.`telefono_residencia`,
						`socios_relaciones`.`fecha_nacimiento`,
						`socios_relaciones`.`telefono_movil`,
						`socios_relaciones`.`monto_relacionado`,
						`socios_relaciones`.`fecha_alta`,
						`socios_relaciones`.`porcentaje_relacionado`,
						`socios_relaciones`.`curp`,
						`socios_relaciones`.`dependiente`,
						`socios_relaciones`.`estatus`,
						`socios_relaciones`.`consanguinidad`,
						`socios_relaciones`.`idusuario`,
						`socios_relaciones`.`observaciones`,
						`socios_relaciones`.`ocupacion`,
						`socios_relaciones`.`sucursal`,
						`socios_relaciones`.`eacp`,
						`socios_relaciones`.`calificacion_del_referente`
					FROM
						`socios_relaciones` `socios_relaciones`
					WHERE
						(`socios_relaciones`.`socio_relacionado` = " . $this->mCodigo . ")
						$ById
					LIMIT 0,1";
		return obten_filas($sql);
	}
	/**
	 * Muestra una Ficha de Informacion del Socio
	 *
	 * @param string $domicilio_extendido
	 * @param string $trTool
	 * @param string $marco
	 * @return string
	 */
	function getFicha($domicilio_extendido = false, $marco = true, $trTool = "", $simple = false){
		$eldom		= "";
		$idpersona	= $this->mCodigo;
		$ccache		= ($domicilio_extendido == true) ? EACP_CLAVE . ".ficha.$idpersona.ext" : EACP_CLAVE . ".ficha.$idpersona";
		$ccache		= ($simple == false) ? $ccache : "$ccache.tiny";
		$ccache		= ($marco == false) ? "$ccache.sm" : $ccache; 
		$this->mIDEnCache	= $ccache;
		
		$xCache		= new cCache();
		$exoFicha	= null;
		$xF			= new cFecha();
		
		if($xCache->isReady() == true){
			$exoFicha	= $xCache->get($ccache);
		}
		if($exoFicha == null){
			$xRuls		= new cReglaDeNegocio();
			$DSocio 	= $this->getDatosInArray();
			$empresa	= $this->getClaveDeEmpresa();
			
			$SinDatosFiscales	= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_SIN_DATOS_FISCALES);		//regla de negocio
			$SinDatoPoblacional = $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_SIN_DATO_POBLACIONAL);		//regla de negocio
			
			$tingreso	= $this->getOTipoIngreso()->getNombre();
			$xLng		= new cLang();
			$xT			= new cTipos();
			$xT->setForceMayus();
			$xT->setToUTF8();
			$mdom		= $this->getDomicilio(99);
			$elnombre 	= $this->getNombreCompleto(OUT_HTML);// $DSocio["apellidopaterno"] . " " . $DSocio["apellidomaterno"] . " " . $DSocio["nombrecompleto"];
			if ($domicilio_extendido == false){
				$eldom 	= (strlen(trim($mdom)) < 6) ? "" : "<tr><th class='izq'>" . $xLng->getT("TR.Domicilio") . "</th><td colspan='3'>" . htmlentities( $xT->cChar($mdom)) . "</td></tr>";
			} else {
				if( $this->getODomicilio() != null){
					$eldom		= "<tr><td colspan='4'>". $this->getODomicilio()->getFicha($this->getTelefonoPrincipal(), false, $this->getCorreoElectronico()) . "</td></tr>";
				}
			}
			if($simple == true){
				$tdExtra	= "";
				$trRFC		= "";
				$tool		= "";
			} else {
				$rfc 		= $this->getRFC();
				$curp 		= $this->getCURP();
				$tool 		= $trTool;
				$wTable		= "";
				$tdExtra	= "";
				$tdCurp		= "<th class='izq'>" . $xLng->getT("TR.CURP") . "</th><td>$curp</td>";
				$tdRFC		= "<th class='izq'>" . $xLng->getT("TR.RFC") . "</th><td>$rfc</td>";
				if($this->getEsPersonaFisica() == false){
					$tdCurp	= "";
					//OBTENER REPRESENTANTE LEGAL
					//22Octubre2013
					$xRels	= $this->getORepresentanteLegal();
					if($xRels != null){
						$tdCurp		= "<th class='izq'>" . $xRels->getNombreRelacion() . "</th><td>" . $xRels->getCodigoDePersona() . "-" . $xRels->getNombreDelRelacionado() . "</td>";
					}
				} else {
					if($SinDatoPoblacional == true){ $tdCurp	= ""; }
					if($SinDatosFiscales === true){ $tdRFC = ""; }
					
				}
				$trRFC		= (trim("$tdCurp$tdRFC") == "") ? "" :  "<tr>$tdRFC $tdCurp</tr>";
				
				if($this->mGrupoAsociado != DEFAULT_GRUPO){
					$dG		= new cGrupo($this->mGrupoAsociado);
					$grupo		= $dG->getNombre();
					$tdExtra	.= "<tr><th class='izq'>" . $xLng->getT("TR.Grupo Solidario") . "</th><td colspan='3'>$grupo</td></tr>";
				}
				if( $empresa != DEFAULT_EMPRESA ){
					$xEmp		= new cEmpresas($empresa); $xEmp->init();
					$Nempresa	= cleanString( $xEmp->getNombre());
					$tdExtra	.= "<tr><th class='izq'>" . $xLng->getT("TR.Empresa") . "</th><td colspan='3'>$Nempresa</td></tr>";
				}
				if(getEsModuloMostrado(USUARIO_TIPO_OFICIAL_AML) == true){
					$xDPais		= new cDomiciliosPaises($this->getPaisDeOrigen());
					$xDPais->init();
					$NPais		= $xDPais->getNombre();
					//Nacionalidad
					$xNPais		= new cDomiciliosPaises($this->getNacionalidad());
					$xNPais->init();
					$NalP		= $xNPais->getGentilicio();
					
					$xRiesgo	= new cAMLRiesgosNiveles($this->getNivelDeRiesgo());// new cAml_risk_levels(); $xRiesgo->setData( $xRiesgo->query()->initByID($this->getNivelDeRiesgo()) );
					$xRiesgo->init();
					$tdExtra	.= "<tr><th class='izq'>" . $xLng->getT("TR.Riesgo de Persona") . "</th><td>" . $xRiesgo->getNombre() . "</td>
							<th class='izq'>" . $xLng->getT("TR.pais de origen / Nacionalidad") . "</th><td>$NPais / $NalP</td></tr>";
				}
			}
			$tdMem		= "";
			if(PERSONAS_CONTROLAR_POR_APORTS == true AND $simple == false){
				$xTM	= new cPersonasMembresiasTipos($this->getTipoDeMembresia());
				$xTM->init();
				$tdMem	= "<tr><th class='izq'>" . $xLng->getT("TR.TIPO_MEMBRESIA") . "</th>
						<td>" . $xTM->getNombre() . "</td>
					<th class='izq'>" . $xLng->getT("TR.FECHA DE REGISTRO") . " - " . $xLng->getT("TR.PAGO") . " - " . $xLng->getT("TR.MATRICULA") . "</th>
					<td>" . $xF->getFechaMX($this->getFechaDeRegistro()) . " - [" . $this->mMembresiaDiaPago . "] - [" . $this->mMembresiaID . "]</td>
					</tr>";
			}
			$tdFor		= "";
			if($this->getEsExtranjero() == true AND $simple == false AND $this->getEsPersonaFisica() == true){
				$tdFor	= "<tr><th class='izq'>" . $xLng->getT("TR.PERMISO_DE_RESIDENCIA") . "</th>
						<td>" . $this->mExtranjeroPermiso. "</td>
				 <th class='izq'>" . $xLng->getT("TR.EXTRANJERO_VENCIMIENTO") . "</th>
				 <td>" . $xF->getFechaMX( $this->mExtranjeroFechaFin) ."</td>
				 </tr>";				
			}
			$tdCL		= "";
			if(SISTEMA_CAJASLOCALES_ACTIVA == true AND $simple == false){
				$xCL	= new cCajaLocal($this->getNumeroDeCajaLocal());
				$xCL->init();
				$xReg	= new cPersonasRegiones($this->getRegion());
				$xReg->init();
				$tdCL	= "<tr><th class='izq'>" . $xLng->getT("TR.CAJA_LOCAL") . "</th><td>" . $xCL->getNombre() . "</td>
					<th class='izq'>" . $xLng->getT("TR.REGION") . "</th><td>" . $xReg->getNombre() . "</td>
									</tr>";				
			}
			$idinterna	= ($this->mIDInterna == "") ? "" : " / " . $this->mIDInterna;
			$txtinterna	= ($idinterna == "") ? "" : " / " . $xLng->getT("TR.IDINTERNO");
			$exoFicha 	=  "
			<table>
			<tbody>
				<tr><th class='izq'>" . $xLng->getT("TR.Codigo") . "$txtinterna</th><td class='green'>" . $this->mCodigo . "$idinterna</td>
					<th class='izq'>" . $xLng->getT("TR.Nombre_completo") . "</th><td>$elnombre</td>
				</tr>
				$eldom
				$trRFC
				$tdExtra
				$tdMem
				$tdFor
				$tdCL
			</tbody><tfoot>$tool</tfoot>
			</table>";
			if ($marco == true){
						$exoFicha = "<fieldset><legend>|&nbsp;&nbsp;" . $xLng->getT("TR.Ficha de Informacion") . "&nbsp;&nbsp;|</legend>
						$exoFicha
						</fieldset>";
			}
			if($xCache->isReady() == true){
				$xCache->set($ccache, $exoFicha);
			}			
		}
		return $exoFicha;
	}
	
	/**
	 * funcion que Retorna el Monto de Ingresos Mensuales por socios
	 * @param integer $tipo
	 * @return float Monto de Ingresos Mensuales
	 */
	function getIngresosMensuales($tipo = false){
		$ByTipo	= "";
		$total	= 0;
			if ($tipo != false ){
				$ByTipo	= "AND
						(`socios_aeconomica`.`tipo_aeconomica` = $tipo)";
			}
		$sql = "SELECT
				SUM(`socios_aeconomica`.`monto_percibido_ae`) AS 'total'
			FROM
				`socios_aeconomica` `socios_aeconomica`
			WHERE
				(`socios_aeconomica`.`socio_aeconomica` =" .  $this->mCodigo . ")
				$ByTipo
			GROUP BY
				`socios_aeconomica`.`socio_aeconomica`
			";
			$d			= obten_filas($sql);
			$total		= (isset($d["total"])) ? $d["total"] : 0;
			unset($d);
			return $total;
	}

	function getOEstats(){
		if($this->mObjEstats == null){
			$this->mObjEstats	= new cPersonasEstadisticas($this->getCodigo());
		}
		return $this->mObjEstats;
	}
	function getOTipoIngreso(){
		if($this->mOTipoIngreso == null){
			$this->mOTipoIngreso	= new cPersonasTipoDeIngreso($this->getTipoDeIngreso());
			$this->mOTipoIngreso->init();
		}
		$this->mDatosDeIngreso	= $this->mOTipoIngreso->getDatosInArray();
		return $this->mOTipoIngreso;	
	}
	
	function getNombreDelRepresentanteLegal(){
		$nombre	= "";
		//OBTENER REPRESENTANTE LEGAL
		//22Octubre2013
		$xRels	= new cPersonasRelaciones(false, $this->getCodigo());
		if($xRels->initRelacionPorTipo(PERSONAS_REL_REP_LEGAL) == true){
			$nombre	= $xRels->getNombreDelRelacionado();
			//$tdCurp		= "<th class='izq'>" . $xRels->getNombreRelacion() . "</th><td>" .  . "</td>";
		}
		return $nombre;
	}
	function getValidacion($out = OUT_TXT, $corregir = false){
		$txt		= "";
		$cTipo		= new cTipos();
		$ready		= true;
		$xLog		= new cCoreLog();
		$EsOperable	= true;
		$xLoc		= new cLocal();
		$xReg		= new cReglasDeCalificacion();
		$xRuls		= new cReglaDeNegocio();
		
		$SinValAE	= $xRuls->getValorPorRegla($xRuls->reglas()->VAL_NO_PERSONA_FALTA_ACT_ECONOM);
		//$SinValAEF	= $xRuls->getValorPorRegla($xRuls->reglas()->VAL_NO_PERSONA_FALLA_ACT_ECONOM);
		
		$xReg->setPersona($this->getCodigo());
		
		if($this->mSocioIniciado == false){ $this->init(); }
		//Validar si puede operar
		if($this->getPermisoParaOperar() == false){
			$ready	= false;
			$xReg->add($xReg->PERS_BLOQUEADA);
			$this->mValidacionERRS++;
		} else {
			$xReg->add($xReg->PERS_BLOQUEADA, true);
		}
		if(PERSONAS_CONTROLAR_POR_GRUPO == true){
			//Validar GrupoSolidario
			if($this->getClaveDeGrupo() > DEFAULT_GRUPO){
				$xGrupo	= new cGrupo($this->getClaveDeGrupo());
				if($xGrupo->init() == false){
					$ready		= false;
					$xLog->add("ERROR\t" . $this->mCodigo . "\tGRUPO\tEl Grupo " . $this->mGrupoAsociado . " No Existe \r\n");
					if($corregir == true){
						$xGrupo->add("_GRUPO_NO_EXISTENTE_", "_GRUPO_EN_ALTA_POR_CORRECION_SOCIO_". $this->mCodigo, $this->mCodigo, DEFAULT_SOCIO, 10, 1, $this->mGrupoAsociado);
						$xLog->add( "WARN\t" . $this->mCodigo . "\tGRUPO\tEl Grupo " . $this->mGrupoAsociado . " Ha sido AGREGADO \r\n");
						$xLog->add($xGrupo->getMessages(), $xLog->DEVELOPER);
					}
					$this->mValidacionERRS++;
					$xReg->add($xReg->PERS_FALTA_GRUPO);
				} else {
					$xReg->add($xReg->PERS_FALTA_GRUPO, true);
				}
			}
		}
		//Validar Caja Local
		if(SISTEMA_CAJASLOCALES_ACTIVA == true){
			$xCL	= new cCajaLocal($this->getNumeroDeCajaLocal());
			if($xCL->init() == false ){ 
				$xLog->add( "ERROR\t" . $this->mCodigo . "\tCAJALOCAL\tLa Caja Local " . $this->mCajaLocal . " No Existe \r\n");
				$ready		= false;
				$xReg->add($xReg->PERS_FALTA_CL);
				$this->mValidacionERRS++;
			} else {
				$xReg->add($xReg->PERS_FALTA_CL, true);
			}
		}
		if(PERSONAS_CONTROLAR_POR_EMPRESA == true){
			//Validar Dependencias
			if($this->getClaveDeEmpresa() != DEFAULT_EMPRESA){
				$xEmp		= new cEmpresas($this->getClaveDeEmpresa());
				if($xEmp->init() === false){
					$xLog->add("ERROR\t" . $this->mCodigo . "\tDEPENDENCIA_E\tLa Dependencia " . $this->mDependencia . " No Existe \r\n");
					$xLog->add($xEmp->getMessages(), $xLog->DEVELOPER);
					$ready	= false;
					$xReg->add($xReg->PERS_FALTA_EMP);
					$this->mValidacionERRS++;				
				} else {
					$xReg->add($xReg->PERS_FALTA_EMP, true);
				}
			}
		}
		//Validar propietario
		if($this->getClaveDeUsuario() != DEFAULT_USER){
			$xUsr	= new cSystemUser($this->getClaveDeUsuario());
			if($xUsr->init() == false){
				$xLog->add("ERROR\t" . $this->mCodigo . "\tPROPIETARIO\tEl Propietario # " . $this->mPropietario . " NO EXISTE \r\n");
				if($corregir == true){
					$xUsr->add("usr" . $this->mPropietario, "", 2, "USUARIO", "AGREGADO", "EN_VERIFICACION", "", false, "baja", "", false, $this->mPropietario);
				}
				$xLog->add($xUsr->getMessages(), $xLog->DEVELOPER);
				$xReg->add($xReg->PERS_FALTA_OFICIAL);
				$this->mValidacionERRS++;
			} else {
				$xReg->add($xReg->PERS_FALTA_OFICIAL, true);
			}
		}
		//Validar persona Moral
		if($this->getEsPersonaFisica() == false){
			if($this->getORepresentanteLegal() == null){
				$xLog->add("ERROR\t" . $this->mCodigo . "\tREP_LEGAL\tEl representante legal NO EXISTE \r\n");
				$ready			= false;
				$EsOperable		= false;
				$xReg->add($xReg->PERS_FALTA_REPLEGAL);
				$this->mValidacionERRS++;				
			} else {
				$xReg->add($xReg->PERS_FALTA_REPLEGAL, true);
			}
		}
		//Validar Tipo de Ingreso
		$xT		= new cPersonasTipoDeIngreso($this->mTipoDeIngreso);
		if($xT->init() == false){
			$txt	.= "ERROR\t" . $this->mCodigo . "\tTIPO_INGRESO\tLa Dependencia " .$this->mTipoDeIngreso . " No Existe \r\n";
			$ready	= false;
			$xReg->add($xReg->PERS_FALTA_TING);
			$this->mValidacionERRS++;
		} else {
			$xReg->add($xReg->PERS_FALTA_TING, true);
		}
		
		//Validar Actividad Economica
		//setError(($SinValAE == false) ? "fase" :"sipi");
		if($SinValAE == false){
			$xOA	= $this->getOActividadEconomica();
			if($xOA == null){
				$ready		= false;
				$EsOperable	= false;
				$xLog->add("ERROR\t" . $this->mCodigo . "\tACTIVIDAD\tLa Persona NO tiene Registrado alguna ACTIVIDAD ECONOMICA \r\n");
				$xReg->add($xReg->PERS_FALTA_AEC);
				$this->mValidacionERRS++;			
			} else {
				$xReg->add($xReg->PERS_FALTA_AEC, true);
				if($xOA->isInit() == false){
					$ready			= false;
					$EsOperable		= false;
					$xLog->add("ERROR\t" . $this->mCodigo . "\tACTIVIDAD\tLa Persona NO tiene Registrado alguna ACTIVIDAD ECONOMICA Valida \r\n");
					$xLog->add($this->getOActividadEconomica()->getMessages(), $xLog->DEVELOPER);
					$xReg->add($xReg->PERS_FALLA_AEC);
					$this->mValidacionERRS++;				
				} else {
					$xReg->add($xReg->PERS_FALLA_AEC, true);
				}
			}
		} else {
			//$xReg->add($xReg->PERS_FALTA_AEC, true);
			//$xLog->add("WARN\t" . $this->mCodigo . "\tACTIVIDAD\tNo se valida la Actividad Economica \r\n");
		}
		//Validar Vivienda
		if($this->getODomicilio() == null){
			$ready		= false;
			$EsOperable	= false;			
			$xLog->add("ERROR\t" . $this->mCodigo . "\tVIVIENDA\tEL Socio NO tiene Registrado algun DOMICILIO Valido \r\n");
			if($corregir == true){
				$codigo_postal	= $xLoc->DomicilioCodigoPostal();
				$this->addVivienda("DESCONOCIDO", "POR_REGISTRAR", $codigo_postal);
			}
			$xReg->add($xReg->PERS_FALTA_DOM);
			$this->mValidacionERRS++;		
			
		} else {
			//Terminar Validacion de codigo postal y busqueda por domicilio aternativo
			$xReg->add($xReg->PERS_FALTA_DOM, true);
		}
		

		//Validar modulo de AML
		if(MODULO_AML_ACTIVADO == true){
			$xAml	= new cAMLPersonas($this->getCodigo());
			
			$xAml->init($this->getCodigo(), $this->getDatosInArray());
			$NRiesgo	= $xAml->setAnalizarNivelDeRiesgo();
			if($this->getNivelDeRiesgo() != $NRiesgo){
				//$ready		= false;
				//$EsOperable	= false;
				$xLog->add("ERROR\t" . $this->mCodigo . "\tRiesgo\tEl riesgo($NRiesgo) es diferente al actual(" . $this->getNivelDeRiesgo() . ") \r\n");
				if($corregir == true){ 
					$this->setAMLAutoActualizarNivelRiesgo();
					$xReg->add($xReg->PERS_FALLA_AML1, true);
				} else {
					$xReg->add($xReg->PERS_FALLA_AML1, true);
					//$this->mValidacionERRS++;
				}
			} else {
				$xReg->add($xReg->PERS_FALLA_AML1, true);
			}
			
			$xLog->add($xAml->getMessages(), $xLog->DEVELOPER);
			//Perfil Transaccional
			$xTrans			= new cAMLPersonasPerfilTransaccional($this->getCodigo());
			if($xTrans->getNumeroEntradas() <= 0){
				$ready		= false;
				$EsOperable	= false;
				$xLog->add("ERROR\t" . $this->mCodigo . "\tPerfilT\tNo existe perfil transaccional \r\n");
				$xReg->add($xReg->PERS_FALLA_AML2);
				$this->mValidacionERRS++;				
			} else {
				$xReg->add($xReg->PERS_FALLA_AML2, true);
			}
		}
		//Validar si es Extranjero
		if($this->getEsExtranjero() == true){
			if($this->getNacionalidad() == EACP_CLAVE_DE_PAIS){
				$xLog->add("ERROR\t" . $this->mCodigo . "\tExtranjero\tNo tiene Nacionalidad Asignada\r\n");
				$xReg->add($xReg->PERS_FALTA_DEXT);
				$this->mValidacionERRS++;				
			} else {
				$xReg->add($xReg->PERS_FALTA_DEXT, true);
			}
		}
		//Validar todos los domicilios
		$xViv	= new cPersonasVivienda($this->getClaveDePersona());
		$xViv->setValidarDomicilios();
		$xLog->add($xViv->getMessages());
		$this->mMessages		.= $xLog->getMessages($out);
		
		return ($out === false) ? $ready : $xLog->getMessages($out);
	}
	function addMemo($tipo = MEMOS_TIPO_PENDIENTE, $txt = "", $documento = false, $fecha = false, $notificar = false, $sms = false){ return $this->setNewMemo($tipo, $txt, $documento, $fecha, $notificar, $sms); }
	function setNewMemo($tipo= MEMOS_TIPO_PENDIENTE, $txt = "", $documento = false, $fecha = false, $notificar = false, $sms = false){
		$xF			= new cFecha(0);
		$grupo		= ($this->mGrupoAsociado == false) ? DEFAULT_GRUPO : $this->mGrupoAsociado;
		$fecha		= $xF->getFechaISO($fecha);
		$documento	= ($documento == false)? DEFAULT_CREDITO : $documento;
		$socio		= $this->mCodigo;
		$usuario 	= getUsuarioActual();
		$sucursal	= getSucursal();
		$eacp		= EACP_CLAVE;
		$xL			= new cLang();
		$mTit		= $xL->getT("TR.Notificacion");
		$archivo	= 0; //($tipo == 12) ? "0": "1"; //12 memo de caja
		$txt		= setCadenaVal($txt);
		$msg		= "";
		$sql = "INSERT INTO socios_memo ( numero_socio,
					numero_gposolidario,
					numero_solicitud,
					fecha_memo,
					texto_memo,
					tipo_memo,
					idusuario,
					sucursal,
					eacp, `archivado` )
					    VALUES( $socio, $grupo, $documento, '$fecha', '$txt', $tipo, $usuario, '$sucursal', '$eacp', $archivo) ";
		//setLog($sql);
		$xQL	= new MQL();
		$x		= $xQL->setRawQuery($sql);
		$x		= ($x === false) ? false : true;
		
		if($x == true ){
			if($notificar == true){
				if($this->mSocioIniciado == false){ $this->init(); }
				$xNot			= new cNotificaciones();
				$xNot->sendMail("$sucursal - $mTit [$tipo]", $txt, $this->getCorreoElectronico());
				if($sms == true){
					$xLoc		= new cReglasDePais();
					$xWap		= new cSeguimientoWathsApp();
					$telMovil	= $xLoc->getTelMovil($this->getTelefonoPrincipal());
					if($telMovil != null){ 
						$xNot->sendSMS($telMovil, $txt);
						$xWap->sendMessage($telMovil, $txt);
					}
					$this->mMessages	.= $xNot->getMessages();
					$this->mMessages	.= $xLoc->getMessages();
				}
			} else {
				$msg	.= "OK\tNota Agregada : $txt\r\n";
			}
		} else {
			$msg	.= "ERROR\tNota no Agregada\r\n";
		}
		$this->mMessages	.= $msg;
		return $msg;
	}
	/**
	 * Modifica un numero de socio, del Anterior a uno indicado
	 * @param integer	$nuevo_numero	Numero de Socio Nuevo
	 * @return string	Mensajes del Proceso
	 */
	function setChangeCodigo($numero_nuevo, $ParaFork = false){
			$numero_socio 	= $this->mCodigo ;
			$xQL			= new MQL();
			$msg = "";
			$msg .= "================== MODIFICANDO UN NUMERO DE SOCIO \r\n";
			$msg .= "================== SE ACTUALIZA DEL $numero_socio AL  $numero_nuevo \r\n";

			$var["aml_alerts"]						= "persona_de_destino";
			$var["aml_risk_register"]				= "persona_relacionada";
			$var["bancos_operaciones"]				= "numero_de_socio";
			$var["captacion_cuentas"]				= "numero_socio";
			$var["captacion_sdpm_historico"]		= "numero_de_socio";
			$var["contable_polizas_proforma"]		= "socio";
			$var["creditos_flujoefvo"]				= "socio_flujo";
			$var["creditos_garantias"]				= "socio_garantia";
			$var["creditos_lineas"]					= "numero_socio";
			$var["creditos_parametros_negociados"]	= "numero_de_socio";
			$var["creditos_sdpm_historico"]			= "numero_de_socio";
			$var["creditos_solicitud"]				= "numero_socio";
			
			$var["general_sucursales"]				= "clave_de_persona";
			$var["operaciones_mvtos"]				= "socio_afectado";
			$var["operaciones_recibos"]				= "numero_socio";
			
			$var["personas_documentacion"]			= "clave_de_persona";
			$var["personas_perfil_transaccional"]	= "clave_de_persona";
			$var["seguimiento_compromisos"]			= "socio_comprometido";
			$var["seguimiento_llamadas"]			= "numero_socio";
			$var["seguimiento_notificaciones"]		= "socio_notificado";
			$var["socios_aeconomica"]				= "socio_aeconomica";
			$var["socios_aeconomica_dependencias"]	= "clave_de_persona";
			$var["socios_aportaciones"]				= "numero_socio";
			$var["socios_baja"]						= "numero_de_socio";
			$var["socios_cajalocal"]				= "ultimosocio";
			$var["socios_firmas"]					= "numero_de_socio";
			$var["socios_general"]					= "codigo";
			if($ParaFork == true){				//Si se va a Forkear
				unset($var["socios_general"]);
			}

			$var["socios_grupossolidarios"]			= "clave_de_persona";
			$var["socios_memo"]						= "numero_socio";
			$var["socios_otros_parametros"]			= "clave_de_persona";
			$var["socios_patrimonio"]				= "socio_patrimonio";
			$var["socios_relaciones"]				= "socio_relacionado";
			
			$var["socios_scoring_simple"]			= "clave_de_persona";
			$var["socios_vivienda"]					= "socio_numero";
			$var["t_03f996214fba4a1d05a68b18fece8e71"]	= "codigo_de_persona";
			$var["tesoreria_cajas_movimientos"]			= "persona";
			$var["usuarios"]							= "codigo_de_persona";
			$var["usuarios_web_notas"]					= "socio";
			
			$var["creditos_presupuestos"]				= "clave_de_persona";
			$var["creditos_destino_detallado"]			= "clave_de_persona";
			$var["personas_datos_extranjero"]			= "clave_de_persona";
			//2017-04-11
			$var["personas_relaciones_recursivas"]		= "persona";
			$var["creditos_destino_detallado"]			= "clave_de_persona";
			$var["personas_consulta_centralriesgos"]	= "persona";
			$var["creditos_presupuestos"]				= "clave_de_persona";
			$var["aml_personas_descartadas"]			= "clave_de_persona";
			$var["personas_pagos_perfil"]				= "clave_de_persona";
			$var["personas_datos_extranjero"]			= "clave_de_persona";
			$var["personas_datos_colegiacion"]			= "clave_de_persona";
			$var["personas_checklist"]					= "clave_de_persona";
			$var["personas_pagos_plan"]					= "persona";
			$var["creditos_preclientes"]				= "idpersona";
			$var["personas_morales_anx"]				= "persona";
			$var["personas_consulta_lista"]				= "persona";
			$var["aml_listanegra_int"]					= "persona";
			$var["leasing_originadores"]				= "clave_de_persona";
			$var["originacion_leasing"]					= "persona";
			$var["personas_proveedores"]				= "persona";
			
			
			foreach ($var as $tabla => $campo){
				$msg	.= "WARN\tCambiar Registros de la Tabla $tabla, Campo $campo de $numero_socio a $numero_nuevo\r\n";
				$xQL->setRawQuery("UPDATE $tabla SET $campo = $numero_nuevo WHERE $campo = $numero_socio");
			}
			$var2["aml_alerts"]								= "persona_de_origen";
			$var2["creditos_solicitud"]						= "persona_asociada";
			$var2["operaciones_recibos"]					= "persona_asociada";
			$var2["socios_relaciones"]						= "numero_socio";
			$var2["socios_grupossolidarios"]				= "representante_numerosocio";
			//2017-04-11
			$var2["personas_relaciones_recursivas"]			= "relacion";
			//$var2["socios_grupossolidarios"]				= "vocalvigilancia_numerosocio";
			

				
			foreach ($var2 as $tabla => $campo){
				$msg	.= "WARN\tCambiar Registros de la Tabla $tabla, Campo $campo de $numero_socio a $numero_nuevo\r\n";
				$xQL->setRawQuery("UPDATE $tabla SET $campo = $numero_nuevo WHERE $campo = $numero_socio");
			}
		return $msg;
	}
	/**
	 * elimina un socio de la base de datos
	 * @return string	Mensajes del proceso.
	 */
	function setDeleteSocio(){
		$socio		=  $this->mCodigo;
		$msg 		=  "";
		$msg 		.= "================== ELIMINANDO PERSONA CODIGO $socio \r\n";

		$xRuls		= new cReglaDeNegocio();
		$xQL		= new MQL();
		$xRuls->reglas()->RN_ELIMINAR_PERSONA;		
		/**
		 * Elimina un socio de las Tabla de Socios
		 */
		//================================================= Eliminacion
		//Eliminar Socio
		$sqlD[] = "DELETE FROM socios_general WHERE codigo=$socio ";
		//Eliminar Relaciones
		$sqlD[] = "DELETE FROM socios_relaciones WHERE socio_relacionado=$socio ";
		//Eliminar
		$sqlD[] = "DELETE FROM socios_vivienda WHERE socio_numero=$socio ";
		//Eliminar actividad economica
		$sqlD[] = "DELETE FROM socios_aeconomica WHERE socio_aeconomica=$socio ";
		//Eliminar Patrimonio
		$sqlD[] = "DELETE FROM socios_patrimonio WHERE socio_patrimonio=$socio ";
		$sqlD[]	= "DELETE FROM creditos_solicitud WHERE numero_socio = $socio ";
		
		$sqlD[]	= "DELETE FROM captacion_cuentas WHERE numero_socio = $socio ";
		$sqlD[]	= "DELETE FROM captacion_sdpm_historico WHERE numero_de_socio = $socio ";
		$sqlD[]	= "DELETE FROM creditos_flujoefvo WHERE socio_flujo =  $socio ";
		$sqlD[]	= "DELETE FROM creditos_garantias WHERE socio_garantia = $socio ";
		$sqlD[]	= "DELETE FROM creditos_lineas WHERE numero_socio = $socio ";
		
		//Eliminar Memos
		$sqlD[] = "DELETE FROM socios_memo WHERE numero_socio=$socio ";
		$sqlD[] = "DELETE FROM operaciones_mvtos WHERE socio_afectado =$socio ";
		$sqlD[] = "DELETE FROM operaciones_recibos WHERE numero_socio =$socio ";
		//Nuevos
		$sqlD[] = "DELETE FROM socios_baja WHERE numero_de_socio = $socio";
		//creditos
		$sqlD[]	= "DELETE FROM creditos_parametros_negociados 						WHERE numero_de_socio=$socio";
		$sqlD[]	= "DELETE FROM creditos_sdpm_historico 								WHERE numero_de_socio = $socio ";
		
		$sqlD[]	= "DELETE FROM `seguimiento_compromisos` 							WHERE `socio_comprometido`=$socio";
		$sqlD[]	= "DELETE FROM `seguimiento_llamadas` 								WHERE `numero_socio`=$socio";
		$sqlD[]	= "DELETE FROM `seguimiento_notificaciones` 						WHERE `socio_notificado`=$socio";

		$sqlD[]	= "DELETE FROM `socios_memo` 										WHERE `numero_socio`=$socio";
		$sqlD[]	= "DELETE FROM `socios_otros_parametros`							WHERE `clave_de_persona`=$socio";
		
		$sqlD[]	= "DELETE FROM `personas_documentacion`								WHERE `clave_de_persona`=$socio";
		$sqlD[]	= "DELETE FROM `personas_perfil_transaccional`						WHERE `clave_de_persona`=$socio";
		
		$sqlD[]	= "DELETE FROM `creditos_presupuestos`								WHERE `clave_de_persona`=$socio";
		$sqlD[]	= "DELETE FROM `creditos_destino_detallado`							WHERE `clave_de_persona`=$socio";
		$sqlD[]	= "DELETE FROM `personas_datos_extranjero` 							WHERE `clave_de_persona`=$socio";
		//2017-04-11
		$sqlD[]	= "DELETE FROM `personas_datos_colegiacion` 						WHERE `clave_de_persona`=$socio";
		
		$sqlD[]	= "DELETE FROM `personas_relaciones_recursivas` 					WHERE `persona`=$socio";
		$sqlD[]	= "DELETE FROM `personas_relaciones_recursivas` 					WHERE `relacion`=$socio";
		$sqlD[]	= "DELETE FROM `personas_consulta_centralriesgos` 					WHERE `persona`=$socio";
		$sqlD[]	= "DELETE FROM `aml_personas_descartadas` 							WHERE `clave_de_persona`=$socio";
		$sqlD[]	= "DELETE FROM `personas_pagos_perfil` 								WHERE `clave_de_persona`=$socio";
		$sqlD[]	= "DELETE FROM `personas_checklist` 								WHERE `clave_de_persona`=$socio";
		$sqlD[]	= "DELETE FROM `personas_pagos_plan` 								WHERE `persona`=$socio";
		$sqlD[]	= "DELETE FROM `personas_morales_anx` 								WHERE `persona`=$socio";
		$sqlD[]	= "DELETE FROM `personas_consulta_lista` 							WHERE `persona`=$socio";
		$sqlD[]	= "DELETE FROM `aml_listanegra_int` 								WHERE `persona`=$socio";
		$sqlD[]	= "DELETE FROM `originacion_leasing` 								WHERE `persona`=$socio";
		$sqlD[]	= "DELETE FROM `personas_proveedores` 								WHERE `persona`=$socio";
		
		//================================================= Actualizaciones		
		$sqlD[]	= "UPDATE contable_polizas_proforma SET socio=" . DEFAULT_SOCIO . " WHERE socio=$socio";
		$sqlD[]	= "UPDATE `usuarios_web_notas` SET `socio`=" . DEFAULT_SOCIO . " 	WHERE `socio`=$socio";
		$sqlD[]	= "UPDATE `socios_aeconomica_dependencias` SET `clave_de_persona`		=" . DEFAULT_SOCIO . " 	WHERE `clave_de_persona`=$socio";
		$sqlD[]	= "UPDATE `socios_grupossolidarios` SET `representante_numerosocio`		=" . DEFAULT_SOCIO . " 	WHERE `representante_numerosocio`=$socio";
		$sqlD[]	= "UPDATE `socios_grupossolidarios` SET `vocalvigilancia_numerosocio`	=" . DEFAULT_SOCIO . " WHERE `vocalvigilancia_numerosocio`=$socio";
		$sqlD[]	= "UPDATE `socios_grupossolidarios` SET `clave_de_persona`		=" . DEFAULT_SOCIO . " 	WHERE `clave_de_persona`=$socio";
		
		$sqlD[]	= "UPDATE `aml_alerts` SET `persona_de_destino`	=" . DEFAULT_SOCIO . " WHERE `persona_de_destino`=$socio";
		$sqlD[]	= "UPDATE `aml_alerts` SET `persona_de_origen`	=" . DEFAULT_SOCIO . " WHERE `persona_de_origen`=$socio";
		$sqlD[]	= "UPDATE `aml_risk_register` SET `persona_relacionada`	=" . DEFAULT_SOCIO . " WHERE `persona_relacionada`=$socio";
		$sqlD[]	= "UPDATE `bancos_operaciones` SET `numero_de_socio`	=" . DEFAULT_SOCIO . " WHERE `numero_de_socio`=$socio";
		
		$sqlD[]	= " UPDATE creditos_solicitud SET persona_asociada = " . DEFAULT_SOCIO . " WHERE persona_asociada = $socio ";
		$sqlD[]	= " UPDATE `t_03f996214fba4a1d05a68b18fece8e71` SET `codigo_de_persona` = " . DEFAULT_SOCIO . " WHERE `codigo_de_persona` = $socio ";
		$sqlD[]	= " UPDATE `tesoreria_cajas_movimientos` SET `persona` = " . DEFAULT_SOCIO . " WHERE `persona` = $socio ";
		$sqlD[]	= " UPDATE `socios_relaciones` SET `numero_socio` = " . DEFAULT_SOCIO . " WHERE `numero_socio` = $socio ";
		
		$sqlD[]	= " UPDATE `general_sucursales` SET `clave_de_persona` = " . DEFAULT_SOCIO . " WHERE `clave_de_persona` = $socio ";
		$sqlD[]	= " UPDATE `operaciones_recibos` SET `persona_asociada` = " . DEFAULT_SOCIO . " WHERE `persona_asociada` = $socio ";
		$sqlD[]	= " UPDATE `socios_aeconomica_dependencias` SET `clave_de_persona` = " . DEFAULT_SOCIO . " WHERE `clave_de_persona` = $socio ";
		
		$sqlD[]	= " UPDATE `creditos_preclientes` SET `idpersona`=" . DEFAULT_SOCIO . " WHERE `idpersona`=$socio ";
		
		//2017-04-11
		$sqlD[]	= " UPDATE `leasing_originadores` SET `clave_de_persona`=" . DEFAULT_SOCIO . " WHERE `clave_de_persona`=$socio ";
		//$sqlD[] = " UPDATE operaciones_mvtos SET socio_afectado = " . DEFAULT_SOCIO . " WHERE socio_afectado =$socio ";
		//$sqlD[] = " UPDATE operaciones_recibos SET numero_socio = " . DEFAULT_SOCIO . " WHERE numero_socio =$socio ";
		//
		
		
		//Cambiar referencias, garantias
		foreach ($sqlD as $key => $send){
			$x		= $xQL->setRawQuery($send);
			//$msg	.= $x[SYS_INFO];
		}
		//Agregar Memo de usuario
		if(MODO_CORRECION == false AND MODO_MIGRACION == false){
			$xUsr	= new cOficial();
			if($xUsr->init()==true){
				$xUsr->addNote(MEMOS_TIPO_HISTORIAL, false, $socio, DEFAULT_CREDITO, $msg);
			}
		}
		$xLog		= new cCoreLog();
		$xLog->add("Persona Clave $socio Eliminada totalmente de la BD");
		$xLog->guardar(199);
		
		$this->setCuandoSeActualiza();	
		return $msg;
	}
	/**
	 * Agrega una nueva persona  a la base de datos
	 * @param string $nombre
	 * @param string $apellidopaterno
	 * @param string $apellidomaterno
	 * @param string $rfc
	 * @param string $curp
	 * @param number $cajalocal
	 * @param string $fecha_de_nacimiento
	 * @param string $lugar_de_nacimiento
	 * @param string $tipo_de_ingreso
	 * @param string $estado_civil
	 * @param string $genero
	 * @param string $dependencia
	 * @param string $regimen_conyugal
	 * @param number $personalidad_juridica
	 * @param string $grupo_solidario
	 * @param string $observaciones
	 * @param number $identificado_con
	 * @param string $documento_de_identificacion
	 * @param string $codigo
	 * @param string $sucursal
	 * @param string $movil
	 * @param string $correo
	 * @param number $dependientes
	 * @param string $fecha
	 * @param string $riesgo
	 * @param string $clave_fiel
	 * @param string $pais
	 * @return boolean
	 */
	function add($nombre, $apellidopaterno = "", $apellidomaterno = "",
			$rfc = "", $curp = "", $cajalocal = DEFAULT_CAJA_LOCAL,
			$fecha_de_nacimiento = false, $lugar_de_nacimiento = "",
			$tipo_de_ingreso = FALLBACK_PERSONAS_TIPO_ING, $estado_civil = DEFAULT_ESTADO_CIVIL,
			$genero = DEFAULT_GENERO, $dependencia = FALLBACK_CLAVE_EMPRESA, $regimen_conyugal = DEFAULT_REGIMEN_CONYUGAL,
			$personalidad_juridica = PERSONAS_FIGURA_FISICA, $grupo_solidario = DEFAULT_GRUPO, $observaciones = "",
			$identificado_con = 1, $documento_de_identificacion = "0", $codigo = false, $sucursal = false,
			$movil	= "", $correo = "", $dependientes = 0, $fecha = false, $riesgo = AML_PERSONA_BAJO_RIESGO, $clave_fiel = "", 
			$pais = EACP_CLAVE_DE_PAIS, $regimen_fiscal = DEFAULT_REGIMEN_FISCAL, $tituloPersonal = ""){
		$sucess					= false;
		$xF						= new cFecha();
		$xLoc					= new cLocal();
		$xQL					= new MQL();
		//Reparando
		$fecha_de_entrevista 	= $xF->getFechaISO($fecha);
		$fecha_de_alta			= $xF->getFechaISO($fecha);
		$fecha_de_revision		= $xF->getFechaISO($fecha);
		$estatus				= FALLBACK_PERSONAS_ESTADO;
		$region					= FALLBACK_PERSONAS_REGION;
		
		$nombre					= addslashes($nombre);
		$apellidomaterno		= addslashes($apellidomaterno);
		$apellidopaterno		= addslashes($apellidopaterno);
		
		$nombre					= utf8_decode( strtoupper($nombre) );
		$apellidopaterno		= utf8_decode( strtoupper($apellidopaterno) );
		$apellidomaterno		= utf8_decode( strtoupper($apellidomaterno) );
		$dependientes			= setNoMenorQueCero($dependientes);
		$eacp					= EACP_CLAVE;
		$sucursal				= ($sucursal == false) ? getSucursal() : $sucursal;
		$usuario				= getUsuarioActual();
		$codigo					= setNoMenorQueCero($codigo);
		$codigo					= ($codigo <= DEFAULT_SOCIO) ? $this->mCodigo : $codigo;
		$fecha_de_nacimiento	= $xF->getFechaISO($fecha_de_nacimiento);
		$cajalocal				= setNoMenorQueCero($cajalocal);
		$cajalocal				= ($cajalocal <= 0) ? $xLoc->getCajaLocal() : $cajalocal;
		if($codigo <= DEFAULT_SOCIO){
			$xCL				= new cCajaLocal($cajalocal); $xCL->init();
			$codigo				= $xCL->getUltimoSocioRegistrado(true)+1;
			$this->mCodigo		= $codigo;
		}
		//purgar RFC
		if($pais == "MX"){
			$xMex	= new cReglasDePais();
			$rfc	= $xMex->getValidIDFiscal($rfc);
			
		}
		$rfc		= ($rfc == "") ? DEFAULT_PERSONAS_RFC_GENERICO : $rfc;
		$sql = "INSERT INTO socios_general(codigo, nombrecompleto, apellidopaterno, apellidomaterno, rfc, curp, 
					fechaentrevista, fechaalta, estatusactual, region, cajalocal,
					fechanacimiento, lugarnacimiento, tipoingreso,
					estadocivil, genero, eacp, observaciones, idusuario,
					grupo_solidario, personalidad_juridica, dependencia,
					regimen_conyugal, sucursal, fecha_de_revision, tipo_de_identificacion, documento_de_identificacion,
					correo_electronico, telefono_principal, dependientes_economicos, pais_de_origen, nivel_de_riesgo_aml, clave_de_firma_electronica,
			regimen_fiscal, `titulo_personal`)
    			VALUES
					($codigo, '$nombre', '$apellidopaterno', '$apellidomaterno', '$rfc', '$curp',
					'$fecha_de_entrevista', '$fecha_de_alta', $estatus, $region, $cajalocal,
					'$fecha_de_nacimiento', '$lugar_de_nacimiento', $tipo_de_ingreso,
					$estado_civil, $genero, '$eacp', '$observaciones', $usuario,
					$grupo_solidario, $personalidad_juridica, $dependencia,
					'$regimen_conyugal', '$sucursal', '$fecha_de_revision', $identificado_con, '$documento_de_identificacion',
					'$correo', '$movil', $dependientes, '$pais', $riesgo, '$clave_fiel', $regimen_fiscal, '$tituloPersonal')";
		
		$x			=$xQL->setRawQuery($sql);
		$x			= ($x === false) ? false : true;
		$this->mCodigo	= $codigo;
		if ($x == false){
			$this->mMessages .= "ERROR\tSe fallo al agregar la persona $codigo\r\n";
			$sucess			= false;
		} else {
			$this->mMessages .= "OK\tSe agrego el Socio $codigo con Nombre $nombre $apellidopaterno $apellidomaterno \r\n";
			$this->init();		//Iniciar
			//Agregar en la Tabla de Grupos Solidarios si aplica
			//2012-06-20 si es grupo y es persojna moral
			if ( $tipo_de_ingreso == TIPO_INGRESO_GRUPO AND $personalidad_juridica == PERSONAS_FIGURA_MORAL ){
				$xGrup		= new cGrupo($codigo);
				$this->addToGrupos(DEFAULT_SOCIO, DEFAULT_SOCIO, $sucursal, $fecha);
				//$xGrup->add($nombre, "", DEFAULT_SOCIO, DEFAULT_SOCIO, 10, 1, $codigo, $sucursal, $fecha_de_alta, $codigo);
				//$this->mMessages .= "OK\tSe Agrega Nuevo Grupo con clave $codigo\r\n";
			}
			if(MODULO_AML_ACTIVADO == true){
				if( $this->mNoAML == false ){
					
					//checar lista negra
					$xAml	= new cAMLPersonas($codigo);
					$xAml->init($codigo);
					$ln		= $xAml->getBuscarEnListaNegra($nombre, $apellidopaterno, $apellidomaterno);
					if($ln == true){
						$xCM	= new cAML();
						$xCM->sendAlerts($codigo, getOficialAML(), 901001, $xAml->getMessages());
					}
					if($pais != EACP_CLAVE_DE_PAIS){
						//verificar persona extranjera
						$xCM	= new cAML();
						$xCM->sendAlerts($codigo, getOficialAML(), 801009, "PERSONA EXTRANJERA REGISTRADA");
					}
					if($riesgo == AML_PERSONA_ALTO_RIESGO){
						//verificar persona extranjera
						$xCM	= new cAML();
						$xCM->sendAlerts($codigo, getOficialAML(), 901001, "PERSONA ALTAMENTE RIESGOSA REGISTRADA");
					}
					//buscar persona SDN
				}
			}
			$sucess	= true;
		}
		return $sucess;
	}
	/**
	 * Agrega una vivienda a la persona
	 * @param string $calle
	 * @param string $numero_exterior
	 * @param string $codigo_postal
	 * @param string $numero_interior
	 * @param string $referencia
	 * @param string $telefono_fijo
	 * @param string $telefono_movil
	 * @param string $es_principal
	 * @param number $regimen
	 * @param number $tipo
	 * @param number $tiempo_de_residir
	 * @param string $colonia				Nombre del colonia
	 * @param string $TipoDeAcceso			Calle, Andador, etc
	 * @param string $gps					Claves GPS 0,0,0
	 * @param string $clave_de_localidad
	 * @param string $clave_de_pais			Ejemplo MX
	 * @param string $nombre_pais			Ejemplo Mexico
	 * @return string	Mensaje de resultado
	 */
	function addVivienda($calle, $numero_exterior, $codigo_postal = false, $numero_interior = "", $referencia = "", $telefono_fijo = "0", $telefono_movil = "0",
			$principal = false, $regimen = FALLBACK_PERSONAS_REGIMEN_VIV, $tipo = FALLBACK_PERSONAS_TIPO_VIV, $tiempo_de_residir = DEFAULT_TIEMPO, 
			$colonia = "", $TipoDeAcceso = "", $gps = "",
			$clave_de_localidad = false, $clave_de_pais = EACP_CLAVE_DE_PAIS, 
			$nombre_pais = "", $nombre_estado = "", $nombre_municipo = "", $nombre_localidad = "", $fecha_de_ingreso = false){
		$xLog				= new cCoreLog();
		$xF					= new cFecha();	
		$xT					= new cTipos();
		$xViv				= new cPersonasVivienda($this->getCodigo(), $tipo);
		$codigo_postal		= setNoMenorQueCero($codigo_postal);
		$tiempo_de_residir	= setNoMenorQueCero($tiempo_de_residir);
		$fecha_de_ingreso	= $xF->getFechaISO($fecha_de_ingreso);
		$principal			= $xT->cBool($principal);
		
		if($tiempo_de_residir < DEFAULT_TIEMPO){
			if($xF->getInt($fecha_de_ingreso) >= $xF->getInt(fechasys()) ){ //si es mayor o igual al actual
				$tiempo_de_residir				= DEFAULT_TIEMPO;
			} else {
				$tiempo_de_residir				= $xViv->getTipoPorFecha($fecha_de_ingreso);
			}
		}
		
		$id			= $xViv->add($calle, $numero_exterior, $numero_interior, $referencia, $telefono_fijo, $telefono_movil,
				$TipoDeAcceso, $colonia, $tipo, $regimen, $tiempo_de_residir,
				$principal, $codigo_postal, $clave_de_localidad, $clave_de_pais, $nombre_localidad, $nombre_municipo, $nombre_estado, $nombre_pais, false, $fecha_de_ingreso);
		$id			= setNoMenorQueCero($id);
		if ( $id > 0 ){
			$this->mIDVivienda	= $id;
			$xLog->add("OK\tSe agrega la Vivienda con ID $id CP $codigo_postal y Localidad $clave_de_localidad del pais $nombre_pais\r\n");
			//Actualiza el Dato de Domicilio del Grupo Solidario
			if( ($this->getTipoDeIngreso() == TIPO_INGRESO_GRUPO) AND ($principal == true) ){
				$xGrp	= new cGrupo($this->mCodigo);
				$DDom	= $this->getDatosDomicilio();
				$arrUp	= array(
						"direccion_gruposolidario" => $this->getDomicilio(),
						"colonia_gruposolidario" => $DDom["colonia"]
				);
				$xGrp->setUpdate($arrUp);
				$xLog->add($xGrp->getMessages());
			}
			$xLog->add($xViv->getMessages(), $xLog->DEVELOPER);
			$this->setCuandoSeActualiza();
		} else {
			$xLog->add("ERROR\tAl agregar la Vivienda con CP $codigo_postal y Localidad $clave_de_localidad del pais $nombre_pais\r\n");
		}
		$this->mMessages	.= $xLog->getMessages();
		return ($id > 0) ? true : false;
	}
	function getIDDeVivienda(){ return $this->mIDVivienda; }
	/**
	 * @deprecated @since 20141100
	 */
	function addActividadEconomica($nombre, $ingreso, $puesto = "", $antiguedad = DEFAULT_TIEMPO,  $dependencia = FALLBACK_CLAVE_EMPRESA,
			$domicilio = "", $localidad = "", $municipio = "", $estado = "",
			$telefono = "0", $extension = 0, $numEmpleado = "0",
			$departamento = "", $clave_de_actividad = 99, $sector = 99, $sucursal = false, $nss='', $cp = false, $idlocalidad = false){
		$xAE			= new cPersonaActividadEconomica($this->mCodigo);
		if(setNoMenorQueCero($this->mIDVivienda) > 0){ $xAE->setDomicilioVinculado($this->mIDVivienda); }
		if($dependencia != FALLBACK_CLAVE_EMPRESA){
			$xAE->setEmpresa($dependencia, $puesto, $departamento, $numEmpleado, $nss, $extension);
		}
		$success			= $xAE->add($clave_de_actividad, $ingreso, $antiguedad, $nombre, $cp, $telefono, $idlocalidad, $localidad, $municipio, $estado);
		//Actualizar Perfil
		if(MODULO_AML_ACTIVADO == true){
			$xAML				= new cAMLPersonas($this->mCodigo);
			if($dependencia != FALLBACK_CLAVE_EMPRESA){	$xAML->addTransaccionalidadPorEmpresa($dependencia, $ingreso);	}
		}
		//agregar relacion
		$this->setCuandoSeActualiza();
		$this->mMessages	.= $xAE->getMessages(); 
		return $success;
	}	
	/**
	 * Agrega un nuevo Patrimonio
	 * @param integer $Tipo
	 * @param float $Monto
	 * @param integer $Estado
	 * @param integer $Afectacion
	 * @param string $DocumentoProbatorio
	 * @param string $Descripcion
	 * @param string $Observaciones
	 * @param date $Expira
	 * @param date $Fecha
	 */
	function addPatrimonio($Tipo, $Monto, $Estado, $Afectacion = 1, $DocumentoProbatorio = "", $Descripcion = "",
			$Observaciones = "", $Expira = "2029-01-01",
			$Fecha = false){
		$xF				= new cFecha(0, $Fecha);
		$xDBT			= new cSQLTabla(TPERSONAS_PATRIMONIO);
		$Fecha			= $xF->get();
		if( $Afectacion == -1 AND $Monto > 0 ){
			$Monto		= $Monto * -1;
		}
		$Socio			= $this->mCodigo;
		$Documento		= DEFAULT_CREDITO;
		$Sucursal		= getSucursal();
		$Eacp			= EACP_CLAVE;
		$Usr			= getUsuarioActual();
		$valores		= "$Socio, $Tipo, $Monto, $Afectacion, '$Expira', '$Observaciones', '$Descripcion', '$DocumentoProbatorio', ";
		$valores		.= "$Documento, $Estado, $Socio, '$Sucursal', '$Eacp', $Usr, '$Fecha' ";
		$SQLI			= $xDBT->getInsert($valores, $xDBT->getCamposSinClaveUnica() );
		$xQL			= new MQL();
		$rs				= $xQL->setRawQuery($SQLI);
		return ($rs != false) ? true : false;
	}
	function addRelacionPorDocumento($relacionado, $documento, $tipo = DEFAULT_TIPO_RELACION){
		$monto		= 0;
		if($tipo == PERSONAS_REL_AVAL_HIPO OR $tipo == PERSONAS_REL_AVAL_QUIRO){
			$xCred	= new cCredito($documento);
			if($xCred->init() == true){
				$monto			= $xCred->getMontoAutorizado();
			}
		}
		return $this->addRelacion($relacionado, $tipo, DEFAULT_TIPO_CONSANGUINIDAD, false, "", $monto, 1, false, $documento);
	}
	function addRelacion($numero_de_socio = FALLBACK_CLAVE_DE_PERSONA, $tipo_de_relacion = DEFAULT_TIPO_RELACION, $consanguinidad = DEFAULT_TIPO_CONSANGUINIDAD,
			$depende = 0, $observaciones = "", $monto_relacionado = 0, $porcentaje_relacionado = 1, $fecha_de_alta = false, $documento = false){
		$xRel	= new cPersonasRelaciones(0, $this->mCodigo);
		$success	= $xRel->addRelacion($numero_de_socio, $tipo_de_relacion, $consanguinidad,$depende, $observaciones, $monto_relacionado,
				 $porcentaje_relacionado, $fecha_de_alta, $documento);
		if($success == true){ $this->setCuandoSeActualiza();	}
		$this->mMessages	.= $xRel->getMessages();
		return $success;
	}
	function addRepresentanteLegal($codigo_de_persona){ return $this->addRelacion($codigo_de_persona, PERSONAS_REL_REP_LEGAL);	}
	/**
	 * Obtiene telefonos en una Array
	 * @return array
	 */
	function getTelefonos(){
		$idpersona	= $this->mCodigo;
		$xCache		= new cCache();
		$idcx		= "persona-telefonos-$idpersona";
		//$xCache->clean();
		$arr		= $xCache->get($idcx);
		if(is_array($arr)){
			$xQL	= new MQL();
		
			$sql = "SELECT
						TRIM(`socios_vivienda`.`telefono_residencial`) AS 'telefono'
					FROM
						`socios_vivienda` `socios_vivienda`
					WHERE
						(`socios_vivienda`.`socio_numero` =$idpersona)
					UNION
					SELECT
						TRIM(`socios_vivienda`.`telefono_movil`)
					FROM
						`socios_vivienda` `socios_vivienda`
					WHERE
						(`socios_vivienda`.`socio_numero` =$idpersona)";
			$rs		= $xQL->getRecordset($sql);
			while($rw = $rs->fetch_assoc() ){
				$arr[]	= $rw["telefono"];
			}
			$arr["principal"]	= $this->mTelefonoP;
			
			$xCache->set($idcx, $arr);
		}
		return $arr;
	}
	/**
	 * Obtiene en un array los datos de la sumas de aportaciones sociales
	 * @return array
	 */
	function getDatosAportaciones(){
		$socio	= $this->mCodigo;
		$sql = "SELECT
				`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
				`operaciones_mvtos`.`socio_afectado`,
				MAX(`operaciones_mvtos`.`fecha_operacion`) AS `fecha`,
				SUM(`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`) AS `aportaciones`
			FROM
				`eacp_config_bases_de_integracion_miembros`
				`eacp_config_bases_de_integracion_miembros`
					INNER JOIN `operaciones_mvtos` `operaciones_mvtos`
					ON `eacp_config_bases_de_integracion_miembros`.`miembro` =
					`operaciones_mvtos`.`tipo_operacion`
			WHERE
				(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` =2609)
				AND
				(`operaciones_mvtos`.`socio_afectado` =$socio)
			GROUP BY
				`operaciones_mvtos`.`socio_afectado`
			ORDER BY
				`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
				`operaciones_mvtos`.`socio_afectado` ";
		$this->mDatosAports		= obten_filas($sql);
		if(!isset($this->mDatosAports["aportaciones"])){
			$this->mDatosAports["aportaciones"]	= 0;
		}
		return $this->mDatosAports;
	}
	/**
	 * Obtiene el Monto de Aportaciones Pagadas
	 */
	function getAportacionesSociales(){ $D	= $this->getDatosAportaciones(); return $D["aportaciones"]; 	}
	/**
	 * Fondo de Defuncion pagado
	 */
	function getFondoDeDefuncion(){
	 	$fecha	= fechasys();
	 	$maximo_de_antiguedad	=  restardias($fecha, DIAS_A_ROTAR_FONDO_DE_DEFUNCION);
	 	$sql			= "SELECT
					`operaciones_mvtos`.`socio_afectado`,
					`operaciones_mvtos`.`tipo_operacion`,
					MAX(`operaciones_mvtos`.`fecha_afectacion`) AS `fecha_de_pago`,
					SUM(`operaciones_mvtos`.`afectacion_real`)  AS `monto_pagado`
				FROM
					`operaciones_mvtos` `operaciones_mvtos`
				WHERE
					(`operaciones_mvtos`.`socio_afectado` = " . $this->mCodigo . ")
					AND
					(`operaciones_mvtos`.`tipo_operacion` =902)
				GROUP BY `operaciones_mvtos`.`socio_afectado` ";
		$monto	= 0;
		
		$Datos	= obten_filas($sql);
		if(isset($Datos["fecha_de_pago"])){
			$fecha	= $Datos["fecha_de_pago"];
			$monto	= setNoMenorQueCero($Datos["monto_pagado"]);
			$this->mMessages	.= "El Socio ha Pagado por Defuncion $monto, el Ultimo pago fue $fecha \r\n";
			if ( $monto != 0){
				//Validar la Rotación
				if ( strtotime($fecha) < strtotime($maximo_de_antiguedad) ){
					$this->mMessages	.= "El Fondo ha Expirado, el pago fue $fecha, Maximo de Antiguedad en $maximo_de_antiguedad \r\n";
					$monto				= 0;
				}
			}
		}
		return $monto;
	 }
	function getGarantiasFisicasDepositadas(){
		$sql			= "SELECT SUM(monto_valuado) AS 'garantias' FROM creditos_garantias WHERE estatus_actual=2 AND socio_garantia=" . $this->mCodigo;
		$resguardado 	= mifila($sql, "garantias");
		return			$resguardado;
	 }
	 /**
	  * Devuelve el monto del fondo patrimonial
	  * @return float
	  */
	function getFondoPatrimonial(){
		$sql = "SELECT
			SUM(`operaciones_mvtos`.`afectacion_real` *
			`eacp_config_bases_de_integracion_miembros`.`afectacion`) AS 'monto'

		FROM
			`operaciones_mvtos` `operaciones_mvtos`
				INNER JOIN `eacp_config_bases_de_integracion_miembros`
				`eacp_config_bases_de_integracion_miembros`
				ON `operaciones_mvtos`.`tipo_operacion` =
				`eacp_config_bases_de_integracion_miembros`.`miembro`
		WHERE
			(`operaciones_mvtos`.`socio_afectado` =" . $this->mCodigo . ")
			AND
			(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 2607)
			GROUP BY
				`operaciones_mvtos`.`fecha_afectacion`,
				`operaciones_mvtos`.`socio_afectado`,
				`operaciones_mvtos`.`docto_afectado`,
				`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`
			ORDER BY
		`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`";

		$monto = mifila($sql, "monto");
		return $monto;
	}
	/**
	 * Obtiene el saldo de la linea de credito activa
	 * @return float	Monto de la linea
	 */
	function getMontoLineaDeCredito($clave = false){
		$rlinea 	= 0;
		$clave		= setNoMenorQueCero($clave);
		if($clave > 0){
			$xDO			= new cCreditosLineas($clave);
			
			if($xDO->init() == true){
				$rlinea		= $xDO->getMontoDisponible();
			}
									
			
		} else {
			$sql 		= "SELECT monto_linea FROM creditos_lineas WHERE numero_socio=" .  $this->mCodigo . " AND estado=1";
			$mlinea 	= mifila($sql, "monto_linea");
			$DCreds		= $this->getTotalColocacionActual();
			$sdoscreds 	= $DCreds["saldo"];
			$rlinea		= $mlinea - $sdoscreds;
		}
		return $rlinea;
	}
	/**
	 * Obtiene el numero de beneficiarios por socio
	 * @return integer
	 */
	function getNumeroDeBeneficiarios(){
			$benefs = 0;
			$sqlcreel = "SELECT COUNT(idsocios_relaciones) AS 'beneficiarios'
							FROM socios_relaciones
							WHERE socio_relacionado=" . $this->mCodigo . " AND tipo_relacion=11";
			$benefs = mifila($sqlcreel, "beneficiarios");
			return $benefs;
	}
	function getNumeroDeDependientes(){
		$socio	= $this->mCodigo;
		
		$sql	= "SELECT	COUNT(`socios_relaciones`.`idsocios_relaciones`) AS 'dependientes'
					FROM
						`socios_relaciones` 
					WHERE
						(`socios_relaciones`.`socio_relacionado` = $socio) AND
						(`socios_relaciones`.`dependiente` =1) 
					GROUP BY
						`socios_relaciones`.`socio_relacionado`,
						`socios_relaciones`.`dependiente`";
		$dependientes	= mifila($sql, "dependientes");
		return $dependientes;
	}
	/**
	 * Esta funcion obtiene le numero de cuenta por tipo, segun lo solicitado o forma de ordenar
	 * @param integer $tipo		Tipo de cuenta
	 * @return integer			Numero de cuenta Primaria
	 */
	function getCuentaDeCaptacionPrimaria($tipo, $subproducto = false){
		$withSubproducto	= "";
		$tipo				= setNoMenorQueCero($tipo);
		$subproducto		= setNoMenorQueCero($subproducto );
		if ( $subproducto  > 0 ){ $withSubproducto	= "  AND (`captacion_cuentas`.`tipo_subproducto` = $subproducto) ";	}
		$clave				= 0;
		$xQL				= new MQL();
		$sql = "SELECT * FROM `captacion_cuentas` WHERE
					(`captacion_cuentas`.`numero_socio` =" . $this->mCodigo . " )
					AND
					(`captacion_cuentas`.`tipo_cuenta` =$tipo)
					$withSubproducto
				ORDER BY
					`captacion_cuentas`.`saldo_cuenta` DESC,
					`captacion_cuentas`.`fecha_afectacion` DESC,
					`captacion_cuentas`.`fecha_apertura` DESC
				LIMIT 0,1";
		$data		= $xQL->getDataRow($sql);
		
		if(isset($data["numero_cuenta"])){ $clave	= $data["numero_cuenta"]; }
		return $clave; 
	}
	/**
	 * Funcion que retorna false?true si un socio existe
	 * @param $socio	Numero de Socio
	 * @return boolean	False/True
	 */
	function existe($socio = false){
		$socio		= setNoMenorQueCero($socio);
		$socio		= ( $socio <= 0 ) ? $this->mCodigo : $socio;
		$existentes	= 0;
		if ( $socio > 0 ){
			$xCache			= new cCache();
			$existentes		= $xCache->get("personas-existentes-$socio");
			if($existentes == null){
				$sql		= "SELECT COUNT(codigo) AS 'existentes' FROM socios_general WHERE codigo = $socio";
				$existentes	= mifila($sql, "existentes");
				$xCache->set("personas-existentes-$socio", $existentes);
			}
		}
		return		( setNoMenorQueCero($existentes) <= 0 ) ? false : true;
	}
	/**
	 * Funcion que retorna false?true si un credito del socio existe
	 * @param $credito	Numero de Credito
	 * @return boolean	False/True
	 */
	function existeCredito($credito){
		$existentes		= $this->getContarDoctos(iDE_CREDITO, false, $credito);
		return		($existentes == 0 ) ? false : true;
	}
	/**
	 * Funcion que retorna false?true si un credito del socio existe
	 * @param $cuenta	Numero de Credito
	 * @return boolean	False/True
	 */
	function existeCuenta($cuenta, $tipo = false){
		$existentes		= setNoMenorQueCero($this->getContarDoctos(iDE_CAPTACION, $tipo, $cuenta));
		return		($existentes <= 0 ) ? false : true;
	}		
	/**
	 * Funcion que Actualiza los Datos del Socio segun un array tipo Campo=>valor
	 * @param	array	$aParam
	 */
	function setUpdate($aParam, $soloActualizar = false){
		$idpersona	= $this->getCodigo();
		$sqlBody	= "";
		$BodyUpdate = "";
		if ( is_array($aParam) AND count($aParam) >=1 ){
			foreach ($aParam as $key => $value) {
				//Buscar en el Valor el Nombre del Field
				//$pos	= stripos($value, $key);
				//Si el Valor es una Cadena y no existe el Nombre del field
				if ( is_string($value)  ){
					$value	= "\"" . $value . "\"";
				}
				if ($BodyUpdate == ""){
					$BodyUpdate .= "$key = $value ";
				} else {
					$BodyUpdate .= ", $key = $value ";
				}
			}	//END FOREACH
			$sqlBody	= "UPDATE socios_general
							    SET $BodyUpdate
							    WHERE
						(codigo =" . $this->mCodigo . ")";
			
			$xQL	= new MQL();
			$x		= $xQL->setRawQuery($sqlBody);
			$x		= ($x === false) ? false : true;
			//$x = my_query($sqlBody);
			//eliminar ID en cache
			if($soloActualizar == false){
				$this->setCuandoSeActualiza();
				$this->init();
			}
			return $x;
		} else {
			return false;
		}	
	}

	function setPrevalidarCredito( $arrDatos, $retornarMonto = false ){
		//$periocidad, $convenio, $contrato
		$xF				= new cFecha(0);
		$sucess			= true;
		$xT				= new cTipos();
		$socio			= $this->mCodigo;
		$xLog			= new cCoreLog();
		$xRuls			= new cReglaDeNegocio();
		$SinFechaAnt	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_SOLICITUD_SIN_FECHA_ANT);		//regla de negocio
		$SinPerAnt		= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_SOLICITUD_SIN_PERIODO_ANT);		//regla de negocio
		//Datos a Manejar
		$monto_maximo 		= 0;		
		
		$solicitud			= ( isset($arrDatos["numero_de_solicitud"]) ) ? $arrDatos["numero_de_solicitud"] : 0;
		$periocidad			= ( isset($arrDatos["periocidad_de_pago"]) ) ? $arrDatos["periocidad_de_pago"] : 1;
		$convenio			= ( isset($arrDatos["tipo_de_producto"]) ) ? $arrDatos["tipo_de_producto"] : false;
		$pagos				= ( isset($arrDatos["numero_de_pagos"]) ) ? $arrDatos["numero_de_pagos"] : 0;
		$fecha_de_venc		= ( isset($arrDatos["fecha_de_vencimiento"]) ) ? $arrDatos["fecha_de_vencimiento"] : $xF->get() ;
		$fecha_de_min		= ( isset($arrDatos["fecha_de_ministracion"]) ) ? $arrDatos["fecha_de_ministracion"] : $xF->get() ;
		$contrato_corr		= ( isset($arrDatos["contrato_corriente_relacionado"]) ) ? $arrDatos["contrato_corriente_relacionado"] : DEFAULT_CUENTA_CORRIENTE ;
		$monto				= ( isset($arrDatos["monto_solicitado"]) ) ? $arrDatos["monto_solicitado"] : 0 ;
		$fecha_de_solicitud	= ( isset($arrDatos["fecha_de_solicitud"]) ) ? $arrDatos["fecha_de_solicitud"] : $xF->get() ;
		$tipo_de_origen		= ( isset($arrDatos["tipo_de_origen"]) ) ? $arrDatos["tipo_de_origen"] : 0 ;
		$clave_de_origen	= ( isset($arrDatos["clave_de_origen"]) ) ? $arrDatos["clave_de_origen"] : 0 ;
		
		$convenio			= setNoMenorQueCero($convenio);
		
		$dias				= ( $periocidad == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO OR $periocidad == CREDITO_TIPO_PERIOCIDAD_DIARIO ) ? ( $xF->setRestarFechas($fecha_de_venc, $fecha_de_min) ) : ($periocidad * $pagos) ;
		if(MODO_MIGRACION == true  ){
			//omitir evaluacion
			$xLog->add("WARN\t$socio\t$solicitud\tNo se evalua el periodo\r\n");
		} else {
			$xPC			= new cPeriodoDeCredito();
			$xPC->init();
			$fechaFinal		= $xPC->getFechaFinal();
			$fechaInicial	= $xPC->getFechaInicial();
			if($SinFechaAnt == true){
				if($xF->getInt($fecha_de_solicitud) < $xF->getInt(fechasys())){
					$sucess			= false;
					$xLog->add("ERROR\t$socio\tNo se aceptan fechas pasadas\r\n");
				}
			} else {
				//Validar si estan dentro del periodo
				if($SinPerAnt == true){
					if($xPC->getEsFechaDentroDelPeriodo($fecha_de_solicitud) == false){
						$sucess			= false;
						$xLog->add("ERROR\t$socio\tLa Fecha $fecha_de_solicitud No se encuentra dentro del periodo\r\n");						
					}
				}
			}
			//Validar si existe el periodo
			if($xPC->getExistePeriodoPorFecha($fecha_de_solicitud) == false){
				$sucess			= false;
				$xLog->add("ERROR\t$socio\tLa Fecha $fecha_de_solicitud No hay periodo para la fecha\r\n");				
			}
		}
		
		if( $convenio <= 0 ){
			$sucess			= false;
			$xLog->add("ERROR\t$socio\tEl tipo de Convenio no es Valido\r\n");
		} else {
			$xConv					= new cProductoDeCredito($convenio);
			
			//si es nomina y no tiene empresa. mostrar false
			if($xConv->getEsProductoDeNomina() == true){
				$xLog->add("WARN\t$socio\t$solicitud\tEl Producto es de Nomina\r\n");
				if($this->getClaveDeEmpresa() == FALLBACK_CLAVE_EMPRESA){
					$sucess			= false;
					$xLog->add("ERROR\t$socio\tEl Producto es de Nomina y No hay EMPRESA\r\n");
				}
			}
			$xDO					= new cCreditosDatosDeOrigen();
			$DConv 					= $xConv->getDatosInArray();
			$dias_maximo			= $DConv["dias_maximo"];
			$tasa_ahorro			= $DConv["tasa_ahorro"];
			$pagos_maximo 			= $DConv["pagos_maximo"];
			$validacion_php			= $DConv["php_monto_maximo"];
			//cargar datos economicos del socio
			$ingreso_mensual		= $this->getIngresosMensuales();

			
			$DTotCreds				= $this->getTotalColocacionActual();
			$DTotCredsByConvenio	= $this->getTotalColocacionActual($convenio);
			
			$DTotCapt				= $this->getTotalCaptacionActual();
			
			//Datos de los Creditos segun referencias
			$monto_de_aportaciones 	= $this->getFondoPatrimonial();
			$saldos_de_creditos 	= $xT->cFloat($DTotCreds["saldo"]);
			$saldo_por_producto 	= $xT->cFloat($DTotCredsByConvenio["saldo"]);
			
			$monto_de_planeacion 	= get_monto_planeacion_credito($socio);//TODO: modificar
			$monto_linea_credito 	= 0;
			
			
			$saldo_de_captacion 	= $xT->cFloat($DTotCapt["saldo"]);
			
			if($tipo_de_origen == $xDO->ORIGEN_LINEA){
				$monto_linea_credito= $this->getMontoLineaDeCredito($clave_de_origen);
			}
			
			
			//Datos de los Creditos segun Productos
			$producto_monto_maximo	= $xConv->getMontoMaximoOtorgable(); //$DConv["maximo_otorgable"];
		
			
			
			//Valorar Dias Maximo del Credito
			if($dias 	> $dias_maximo){
				$sucess			= false;
				$xLog->add("ERROR\t$socio\tLos Dias del Credito[$dias] se exceden a lo permitido[$dias_maximo]\r\n");
			} else {
				if( $dias < EACP_DIAS_MINIMO_CREDITO ){
					$sucess				= false;
					$xLog->add("ERROR\t$socio\tLos Dias del Credito[$dias] son Menores al Minimo Permitido [" . EACP_DIAS_MINIMO_CREDITO . "] por la Institucion\r\n");					
				} else {
					$xLog->add("OK\t$socio\tDias de Pago dentro del Rango Permitido:[$dias]\r\n");
				}
			}
			
			
			//Valorar Pagos Maximo del Credito
			if($pagos > $pagos_maximo){
				$sucess		 		= false;
				$xLog->add("ERROR\t$socio\tLos Numero de Pagos[$pagos] Execeden a lo Permitido[$pagos_maximo]\r\n");
			} else {
				$xLog->add("OK\t$socio\tNumero de Pagos dentro del Rango Permitido[$pagos]\r\n");
			}
			//tasa de ahorro
			if ($tasa_ahorro > 0 ){
				$xLog->add("WARN\t$socio\tEl Ahorro para este Convenio es Obligatorio\r\n");
				//Valorar Contrato de Captacion
				if ( ($contrato_corr == DEFAULT_CUENTA_CORRIENTE) OR ( strlen($contrato_corr) < DIGITOS_DE_CLAVE_DE_SOCIO ) ){
					$xLog->add("ERROR\t$socio\tDebe Capturar un Numero de Contrato de Cuenta Corriente	para que los Pagos de Credito se Acumulen\r\n");
					$sucess				= false;
				} else {
					$xCta			= new cCuentaDeCaptacion($contrato_corr);
					$existe			= $this->existeCuenta($contrato_corr, CAPTACION_TIPO_VISTA);
					if ( $existe == false){
						//
						$xLog->add("ERROR\t$socio\tLa Cuenta $contrato_corr No existe, o no es del Tipo requerido\r\n");
						$sucess			= false;						
					} else {
						//
						$xLog->add("OK\t$socio\tLa Cuenta $contrato_corr  es valida para Utilizar\r\n");
					}
				}//end: contrato
			}// end: tasa ahorro
			//
			eval( $validacion_php );
			if( $monto > $monto_maximo ){
				$xLog->add("ERROR\t$socio\tEl Monto Solicitado $monto es mayor al Maximo Permitido $monto_maximo ($producto_monto_maximo)\r\n");
				$sucess				= false;				
			}
			if($periocidad == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO AND $pagos > 1){
				$xLog->add("ERROR\t$socio\tLos pagos debe ser Igual a 1\r\n");
				$sucess				= false;				
			}
			if($monto <= 0){
				$xLog->add("ERROR\t$socio\tEl Monto Solicitado $monto debe ser mayor a 0\r\n");
				$sucess				= false;				
			}
		}
		$this->mMessages	.= $xLog->getMessages();
		$this->mUUID		= md5($socio . $convenio . $monto . $pagos . ROTTER_KEY . date("Ymd") );
	return ($retornarMonto == false ) ? $sucess : $monto_maximo;	
		//obtener mensajes
	}
	function getIDNuevoDocto( $tipo_de_docto, $subtipo = false ){
		$socio			= $this->mCodigo;
		$id				= false;
		$xT				= new cTipos();
		$xLog			= new cCoreLog();
		$xQL			= new MQL();
		$msg			= "";
		
		$proximoDocto	= setNoMenorQueCero( $this->getContarDoctos($tipo_de_docto)) + 1; //$subtipo
		$clave_socio	= $xT->cSerial(DIGITOS_DE_CLAVE_DE_SOCIO, $socio);
		$docto			= $xT->cSerial(DIGITOS_DE_DOCUMENTO, $proximoDocto);
		if(setNoMenorQueCero($docto)< $proximoDocto){
			$docto		= $proximoDocto;
		}
		//tipo-de-docto + socio + numero-de-credito
		//200 + 990000 + 99 =20099000099
		$id				= str_replace("0", "", "$tipo_de_docto") . $clave_socio . $docto;
		switch ($tipo_de_docto){
			case iDE_PRESUPUESTO:
				
				break;
			default:
				$xQL	= new MQL();
				$id		= setNoMenorQueCero($id);
				$D		= $xQL->getDataRow("SELECT COUNT(`numero_solicitud`) AS 'existe' FROM `creditos_solicitud` WHERE `numero_solicitud`=$id");
				$proximo= setNoMenorQueCero($proximoDocto);
				$cnt	= $proximo;
				$existe	= (isset($D["existe"])) ? setNoMenorQueCero($D["existe"]) : 0;
				
				if($existe > 0){
					
					for($bb = $proximo; $bb <= 10000; $bb++){
						//$nn	= $xT->cSerial(DIGITOS_DE_DOCUMENTO, $bb);
						$nn		= str_replace("0", "", "$tipo_de_docto") . $clave_socio . $bb;
						
						$D	= $xQL->getDataRow("SELECT COUNT(`numero_solicitud`) AS 'existe' FROM `creditos_solicitud` WHERE `numero_solicitud`=$nn");
						$existe	= (isset($D["existe"])) ? setNoMenorQueCero($D["existe"]) : 0;
						if($existe >0){
							$xLog->add("$socio\tEl ID Credito $nn existe, se cambia a otro contar $bb\r\n", $xLog->DEVELOPER);
						} else {
							$id		= $nn;
							$cnt	= $bb;
							$xLog->add("$socio\tEl ID Credito se establece en $id y contados $bb\r\n", $xLog->DEVELOPER);
							break;
						}
					}
				} else {
					$xLog->add("$socio\tEl ID Credito se establece en $id\r\n", $xLog->DEVELOPER);
				}
				
				if(MODULO_CAPTACION_ACTIVADO == true){
					$D		= $xQL->getDataRow("SELECT COUNT(`numero_cuenta`) AS 'existe' FROM `captacion_cuentas` WHERE `numero_cuenta`=$id");
					$existe	= (isset($D["existe"])) ? setNoMenorQueCero($D["existe"]) : 0;
					if($existe >= 1){
						for($bb = $cnt; $bb <= 10000; $bb++){
							//$nn	= $xT->cSerial(DIGITOS_DE_DOCUMENTO, $bb);
							$nn		= str_replace("0", "", "$tipo_de_docto") . $clave_socio . $bb;
							
							$D	= $xQL->getDataRow("SELECT COUNT(`numero_cuenta`) AS 'existe' FROM `captacion_cuentas` WHERE `numero_cuenta`=$nn");
							$existe	= (isset($D["existe"])) ? setNoMenorQueCero($D["existe"]) : 0;
							if($existe >0){
								$xLog->add("$socio\tEl ID Cuenta $nn existe, se cambia a otro\r\n", $xLog->DEVELOPER);
							} else {
								$id		= $nn;
								$cnt	= $bb;								
								$xLog->add("$socio\tEl ID Cuenta $nn existe, se cambia a otro\r\n", $xLog->DEVELOPER);
								break;
							}
						}
					} else {
						$xLog->add("$socio\tEl ID Cuenta $id No existe \r\n", $xLog->DEVELOPER);
					}
				}
				//verifica si existe el credito
				/*if ( $this->getContarDoctos(iDE_CREDITO, false, $id) > 0 ) {
					$xLog->add("$socio\t$id\El Credito Existe, se Cambia a " . ($id + 1) . "\r\n", $xLog->DEVELOPER);
						$xT->cSerial(DIGITOS_DE_DOCUMENTO, $proximoDocto);
					$id		= $this->getMaxDoctos(iDE_CREDITO, ($id+1));
						
					//vuelve a verificar el credito
					if ( $this->getContarDoctos(iDE_CREDITO, false, $id) > 0 ) {
						$xLog->add("$socio\t$id\El Credito Existe, se Cambia a " . ($id + 1) . "\r\n", $xLog->DEVELOPER);
						$mID		= $id+1;		
						for($buscar = $mID; $this->getContarDoctos(iDE_CREDITO, false, $buscar) <= 0; $buscar++){
							$xLog->add("$socio\t$buscar\El Credito Existe, se Cambia a " . $buscar . "\r\n", $xLog->DEVELOPER);
							$id		= $buscar;
						}
					}
				}
				if(MODULO_CAPTACION_ACTIVADO == true){
					//verifica si existe en captacion
					if ( $this->getContarDoctos(iDE_CAPTACION, false, $id) > 0 ) {
						$xLog->add("$socio\t$id\La CUENTA Existe, se Cambia a " . ($id + 1) . "\r\n", $xLog->DEVELOPER);
						$id		= $this->getMaxDoctos(iDE_CAPTACION, ($id+1));
				
						//vuelve a verficar en captacion
						if ( $this->getContarDoctos(iDE_CAPTACION, false, $id) > 0 ) {
							$mID		= $id+1;
							for($buscar = $mID; $this->getContarDoctos(iDE_CAPTACION, false, $buscar) <= 0; $buscar++){
								$xLog->add("$socio\t$buscar\La CUENTA Existe, se Cambia a " . $buscar . "\r\n", $xLog->DEVELOPER);
							}
							
							
						}
					}
				}*/


				break;
		}
		if(MODO_DEBUG == true){
			setLog($xLog->getMessages());
		}
		$this->mMessages	.= $xLog->getMessages();
		return $id;
	}
	function getContarDoctos( $tipo_de_docto, $subtipo = false, $codigo = false){
		$socio			= $this->mCodigo;
		$existentes		= 0;
		$codigo			= setNoMenorQueCero($codigo);
		//checar
		if ($codigo <= DEFAULT_CREDITO){
			switch ($tipo_de_docto){
				case iDE_CREDITO:
					$ByTipo		= ( $subtipo == false ) ? "" : " AND (`creditos_solicitud`.`tipo_convenio` =$subtipo) ";
					$ByID		= ( $codigo == false ) ? "" : " AND (`creditos_solicitud`.numero_solicitud = $codigo) ";
					$socio		= $this->mCodigo;
					$sql		= "SELECT COUNT(numero_solicitud) AS 'existentes' FROM creditos_solicitud WHERE  numero_socio = $socio $ByTipo $ByID ";
					$existentes	= mifila($sql, "existentes");
					break;
				//obtiene una cuenta de captacion 10 vista 20 inversion
				case iDE_CAPTACION:
					$ByTipo		= ( $subtipo == false ) ? "" : " AND (`captacion_cuentas`.`tipo_cuenta` =$subtipo) ";
					$ByID		= ( $codigo == false ) ? "" : " AND (`captacion_cuentas`.`numero_cuenta` =$codigo)  ";
					$sql		= "SELECT COUNT(`captacion_cuentas`.`numero_socio`) AS `existentes` FROM
									`captacion_cuentas` `captacion_cuentas` WHERE (`captacion_cuentas`.`numero_socio` = $socio) $ByTipo $ByID ";
					$existentes	= mifila($sql, "existentes");
					break;
				default:
					break;
			}
		} else {
			$sql		= "SELECT( SELECT COUNT(*) FROM creditos_solicitud WHERE numero_solicitud=$codigo ) + (SELECT COUNT(*) FROM `captacion_cuentas` WHERE `numero_cuenta`=$codigo) AS 'existentes'";
			$existentes	= mifila($sql, "existentes");
		}
		return $existentes;
	}
	function getMaxDoctos( $tipo_de_docto, $proposal = 0){
		$socio		= $this->mCodigo;
		$existentes	= 0;
		
		$xQL		= new MQL();
		//checar
		
			switch ($tipo_de_docto){
				case iDE_CREDITO:
					$sql		= "SELECT MAX(`numero_solicitud`) AS 'maximo' FROM `creditos_solicitud` WHERE `numero_socio`=$socio";
					$D			= $xQL->getDataRow($sql);
					$existentes	= isset($D["maximo"]) ? setNoMenorQueCero($D["maximo"]) : $proposal;
					break;
					//obtiene una cuenta de captacion 10 vista 20 inversion
				case iDE_CAPTACION:
					$sql		= "SELECT MAX(`numero_cuenta`) AS 'maximo' FROM `captacion_cuentas` WHERE `numero_socio`=$socio";
					$D			= $xQL->getDataRow($sql);
					$existentes	= isset($D["maximo"]) ? setNoMenorQueCero($D["maximo"]) : $proposal;
					break;
				default:
					break;
			}
		$existentes		= $existentes+1;
		if($existentes < $proposal){
			$existentes	= $proposal+1;
		}
		return $existentes;
	}
	
	function getSucursal(){ return $this->mSucursal; }
	function setVerificacion($tipo , $id, $fecha = false, $notas = "", $oficial = false ){
		$fecha		= ($fecha == false ) ? fechasys() : $fecha;
		$oficial	= ($oficial == false ) ? getUsuarioActual() : $oficial;

		$xDB		= new cSQLTabla($tipo);
		$arrUp		= array( "fecha_de_verificacion" => $fecha, "oficial_de_verificacion" => $oficial, "estado_actual" => 1 );
		$xDB->setWhere( $xDB->getClaveUnica() . "=$id" );
		$stat		= $xDB->setUpdate($arrUp);
		$xCache		= new cCache();
		$xCache->clean("$tipo-$id");
		if( $stat == false ){
			$this->mMessages	.= $this->mCodigo . "\tERROR\tHubo un error al guardar la verificacion\r\n";
		}
		if($notas != ""){
			$this->setNewMemo(4, $notas, DEFAULT_CREDITO, $fecha);
		}
		
	}
	function getClaveDeEmpresa(){ return $this->mDependencia; }
	function getClaveDeUsuario(){ return $this->mUsuarioProp; } 
	function puedeSerRelacion($tipo){
		$success	= true;
		$xTR		= new cPersonasTiposDeRelacion($tipo);
		$xTR->init();
		
		//$this->init();
		
		//validar si el socio tiene domiclio y ocupacion
		$DDom		= $this->getDatosDomicilio();
		if( !isset($DDom["id"]) && $xTR->requiereDomicilio() == true ){
			$this->mMessages	.= "NO SE PUEDE AGREGAR UNA PERSONA SIN DOMICILIO\r\n";
			$success		= false;
		}
		$DOcup		= $this->getDatosActividadEconomica();
		if( !isset($DOcup["id"]) && $xTR->requiereActividadEconomica() == true ){
			$this->mMessages	.= "NO SE PUEDE AGREGAR UNA PERSONA SIN ACTIVIDAD ECONOMICA\r\n";
			$success		= false;
		}
		return $success;
	}
	function getClaveDePersonaDeConyuge(){
		$sqlconyuge = "SELECT numero_socio FROM socios_relaciones where consanguinidad=3
					AND socio_relacionado=" . $this->mCodigo . " LIMIT 0,1";
		return getFila($sqlconyuge, "numero_socio");
	}
	function getOActividadEconomica($tipo = false){
		$xAct	= new cPersonaActividadEconomica($this->mCodigo, $tipo);
		$xAct->init();
		$this->mOBActividadE	= $xAct->obj();
		return $xAct;
	}
	
	function setGuardarDocumento($tipo, $archivo, $pagina=0, $observaciones='', $fecha = false, $archivonuevo = null, $FechaVencimiento = false, $Contrato=false){
		$xF					= new cFecha();
		if($FechaVencimiento === false){
			$xTA	= new cPersonas_documentacion_tipos();
			$xTA->setData( $xTA->query()->initByID($tipo) );
			$dias	= setNoMenorQueCero($xTA->vigencia_dias()->v());
			$FechaVencimiento	= $xF->setSumarDias($dias, $fecha);
		}
		$FechaVencimiento	= $xF->getFechaISO($FechaVencimiento); setLog($fecha);
		$fecha				= $xF->getFechaISO($fecha);
		$socio				= $this->getCodigo();
		$ready				= true;
		//si existe el archivo enviado.
		if(is_array($archivonuevo)){
			$xDoc			= new cDocumentos();
			$ready			= $xDoc->FTPUpload($archivonuevo);
			$archivo		= $xDoc->getNombreArchivo();
			if(MODO_DEBUG == true){ 	if($ready == false){ $this->mMessages 			.= $xDoc->getMessages(); }	}
			
		}
		if(isset($archivo) AND $ready == true) {
			$xDoc			= new cDocumentos($archivo);
			$ready			= $xDoc->FTPMove($archivo, $socio);
			if($ready == true){
				$ready		= $xDoc->add($tipo, $pagina, $observaciones, $Contrato, $socio, $archivo, $fecha, $FechaVencimiento);
			}
			if(MODO_DEBUG == true){ $xDoc->getMessages(); 	}
		} else {
			$this->mMessages 	.= "ERROR\tNO EXISTE EL ARCHIVO $archivo\r\n";
			$ready				= false;
		}
		if($ready == true){
			//Actualizar Check List
			
		}
		//setLog($this->mMessages);
		return $ready;
	}
	function getDocumentoGuardado($tipo){
		$persona		= $this->getCodigo();
		$result 		= getFila("SELECT archivo_de_documento FROM personas_documentacion WHERE clave_de_persona = $persona AND tipo_de_documento=$tipo AND estado_en_sistema=" . SYS_UNO ." LIMIT 0,1", "archivo_de_documento");
		return $result;
	}
	function getEsPersonaFisica(){
		$xB		= new cBases(BASE_ES_PERSONA_MORAL);
		$this->mMessages	.= (MODO_DEBUG ==  true) ? "WARN\tPERSONALIDAD LEGAL " . $this->mTipoFiguraJu . "\r\n" : "";
		return ($xB->getIsMember($this->mTipoFiguraJu) == false) ? true : false;
	}
	function getEsPersonaPoliticamenteExpuesta(){
		$rs			= false;
		$xCache		= new cCache();
		
		if(MODULO_AML_ACTIVADO == true){
			$rs			= $xCache->get("getEsPersonaPoliticamenteExpuesta-". $this->mCodigo);
			if($rs === null ){
				$xCat	= new cPersonasCatalogoOtrosDatos();
				$rs		= ($this->getTipoDeIngreso() == TIPO_INGRESO_PEP) ? true : false;
				$PEPA	= $this->getOtrosParametros($xCat->AML_PEP_AFINIDAD);
				$PEPC	= $this->getOtrosParametros($xCat->AML_PEP_CONSANGUINIDAD);
				$PEP	= $this->getOtrosParametros($xCat->AML_PEP_PRINCIPAL);
				if(setNoMenorQueCero($PEPA) > 0){
					$rs					= true;
					$this->mMessages	.= "WARN\tEsta persona es PEP por Afinidad\r\n";
				}
				if(setNoMenorQueCero($PEPC) > 0){
					$rs					= true;
					$this->mMessages	.= "WARN\tEsta persona es PEP por Consanguinidad\r\n";
				}
				if(setNoMenorQueCero($PEP) > 0){
					$rs					= true;
					$this->mMessages	.= "WARN\tEsta persona es PEP Principal\r\n";
				}
				$xCache->set("getEsPersonaPoliticamenteExpuesta-". $this->mCodigo, $rs);
			}
		}
		return $rs;
	}
	function getEsPersonaSDN(){ 
		$rs			= false;	
		
		if(MODULO_AML_ACTIVADO == true){
			$rs		= ($this->getTipoDeIngreso() == TIPO_INGRESO_SDN) ? true : false;
			$xPL	= new cAMLListaNegraInterna(false, $this->mCodigo);
			$existe	= $xPL->initPorPersona($this->mCodigo);
			//if($existe == true){ $rs = true;	}
		}
		return $rs;
	}
	function getNivelDeRiesgo(){ return $this->mNivelDeRiesgo; }
	function setAMLAutoActualizarNivelRiesgo(){
		$xLog	= new cCoreLog();
		if(MODULO_AML_ACTIVADO == true){
			$xAml	= new cAMLPersonas($this->getCodigo());
			if($xAml->init($this->getCodigo(), $this->getDatosInArray())== true){
				if($xAml->getEsPersonaOmitida() == false){ //Si no está en omitidos
					
					//Actualizar Nivel de Riesgo
					$riesgoAML	= $xAml->setAnalizarNivelDeRiesgo();
					if($riesgoAML != $this->getNivelDeRiesgo()){
						$this->setActualizarNivelDeRiesgo($riesgoAML, $xAml->getMessages());
						$xLog->add("WARN\tSe actualiza el Nivel de Riesgo a $riesgoAML \r\n");
					}
				}
			}
			$xLog->add($xAml->getMessages(), $xLog->DEVELOPER);
		}
		$this->mMessages	.= $xLog->getMessages();
	}
	function setActualizarNivelDeRiesgo($nivel = SYS_RIESGO_BAJO, $mensaje = "", $fecha = false, $GuardarOmision = false){ 
		$xNot		= new cNotificaciones();
		$xF			= new cFecha();
		$fecha		= $xF->getFechaISO($fecha);
		$mensaje	= $xNot->cleanString($mensaje);
		
		//AML.- Agregar aviso de cambio
		if($nivel >= SYS_RIESGO_ALTO AND MODULO_AML_ACTIVADO == true AND PERSONAS_COMPARTIR_CON_ASOCIADA == true ){
			$xAml	= new cAML();
			$xAml->setForceAlerts();
			$xAml->sendAlerts($this->mCodigo, getOficialAML(), AML_RISK_INTERNAL_OPERATION, $mensaje, false, $fecha );
			//TODO: Exportar a asociada, falta relaciones exportar relaciones
			$this->getExportarAsociada(TPERSONAS_GENERALES, SVC_REMOTE_HOST);
			$this->getExportarAsociada(TPERSONAS_DIRECCIONES, SVC_REMOTE_HOST);
			$this->getExportarAsociada(TPERSONAS_ACTIVIDAD_ECONOMICA, SVC_REMOTE_HOST);
			$this->mMessages	.= $xAml->getMessages();
		}
		if(MODULO_AML_ACTIVADO == true AND $GuardarOmision == true AND $nivel <= AML_PERSONA_BAJO_RIESGO){
			$xOmit	= new cAMLPersonasOmisiones(false, $this->getClaveDePersona());
			$xOmit->setCleanCache();
			
			if($xOmit->initByPersona($this->getClaveDePersona()) == true ){
				if($xOmit->getEsVigente($fecha) == false){
					if($xOmit->add($mensaje, $this->getCodigo()) ){
						$this->mMessages	.= "OK\tPersona agrega a Excepcion con el nivel $nivel  porque no es vigente \r\n";
					}
				}
			} else {
				$this->mMessages	.= "OK\tPersona agrega a Excepcion con el nivel $nivel al no existir \r\n";
				$xOmit->add($mensaje, $this->getClaveDePersona());
			}
		}
		
		//Omitir AML en la actualizacion
		$this->setOmitirAML(true);
		$this->setUpdate(array("nivel_de_riesgo_aml" => $nivel) );		
		//agregar una Noticiacion a la Persona de Nota
		if($GuardarOmision == true){
			if(MODO_DEBUG == true){
				//$this->addMemo(MEMOS_TIPO_HISTORIAL, $mensaje, false, $fecha);
			}
		}
	}
	function getEsPersonaRiesgosa(){
		$rs	= false;
		if( $this->getEsPersonaSDN() == true ){ $rs	= true; }
		//TODO: Revisar politica
		//if( $this->getEsPersonaPoliticamenteExpuesta() == true ){ $rs	= true; }
		if( $this->mNivelDeRiesgo > AML_PERSONA_MEDIO_RIESGO ){ 
			$rs = true;
			$this->mMessages		.= "ERROR\tPersona de Alto Riesgo\r\n";
		}
		return $rs;
	}
	function setOmitirAML($omitir = true){ $this->mNoAML	= $omitir; }
	function setMontoAhorroPreferente($monto){
		//guardar el otro parametro
		$xOP	= new cPersonasCatalogoOtrosDatos();
		$this->addOtrosParametros($xOP->PERSONAS_MONTO_AHORRO_PREFERENTE, $monto);
	
		$this->setUpdate(array("descuento_preferente" => $monto) );
	
		if(PERSONAS_COMPARTIR_CON_ASOCIADA == true){
			//ejecutar el url
			$this->getExportarAsociada(TPERSONAS_GENERALES);
			$this->getExportarAsociada(TPERSONAS_DIRECCIONES);
			$this->getExportarAsociada(TPERSONAS_ACTIVIDAD_ECONOMICA);
			if($this->getEsEmpresaConConvenio() == true){
				$this->getExportarAsociada(TCATALOGOS_EMPRESAS);
			}
		}
	}
	function getEsExtranjero(){ return $this->mExtranjeroEs;}
	function getOCapacidadDePago(){ $xCap	= new cPersonaCapacidadDePago($this->mCodigo); return $xCap; }
	function getEdad(){
		$fecha_de_nacimiento	= $this->getFechaDeNacimiento();
		$xF1					= new cFecha();
		$diferencia				= $xF1->getInt(fechasys()) - $xF1->getInt($fecha_de_nacimiento );
		return floor($diferencia/31104000); 
	}
	function getEsEmpresaConConvenio($init = false){
		$persona	= $this->mCodigo;
		$result		= false;
		$default	= FALLBACK_CLAVE_EMPRESA;
		$xCache		= new cCache();
		$d			= $xCache->get("persona-empresa-conv-$persona");
		if($d === null){
			$sql	= "SELECT * FROM `socios_aeconomica_dependencias` WHERE (`socios_aeconomica_dependencias`.`clave_de_persona` = $persona) AND (`socios_aeconomica_dependencias`.`clave_de_persona` != $default) LIMIT 0,1";
			$d		= obten_filas($sql);
			$xCache->set("persona-empresa-conv-$persona", $d);
		}
		
		if(isset( $d["idsocios_aeconomica_dependencias"] )){
			$result	= true;
			$clave	= $d["idsocios_aeconomica_dependencias"];
			if($init == true){
				$xSA	= new cEmpresas($clave); $xSA->init();
				$this->mObjEmpresa	= $xSA;
			}
		} else {
			$this->mMessages	.= "WARN\tNo hay empresa asociada\r\n";
		}
		return $result;
	}
	function getEsGrupoSolidario($init = false){
		$persona	= $this->mCodigo;
		$result		= false;
		$default	= FALLBACK_CLAVE_DE_PERSONA;
		$xCache		= new cCache();
		$d			= $xCache->get("persona-grupo-sol-$persona");
		if($d === null){
			$sql	= "SELECT * FROM `socios_grupossolidarios` WHERE (`socios_grupossolidarios`.`clave_de_persona` =$persona) AND (`socios_grupossolidarios`.`clave_de_persona` != $default) LIMIT 0,1";
			$d		= obten_filas($sql);
			$xCache->get("persona-grupo-sol-$persona", $d);
		}
		if(isset( $d["clave_de_persona"] )){
			$result	= true;
			$clave	= $d["clave_de_persona"];
			if($init == true){
				$xG		= new cGrupo($clave); $xG->init($d);
				$this->mObjGrupoS	= $xG;
			}
		} else {
			$this->mMessages	.= "WARN\tNo hay GRUPO asociado\r\n";
		}
		return $result;
	}
	function getEsUsuario($init = false){
		$persona	= $this->mCodigo;
		$result		= false;
		$default	= FALLBACK_CLAVE_DE_PERSONA;
		$sql		= "SELECT * FROM `t_03f996214fba4a1d05a68b18fece8e71` WHERE	(`t_03f996214fba4a1d05a68b18fece8e71`.`codigo_de_persona` =$persona) 
		AND (`t_03f996214fba4a1d05a68b18fece8e71`.`codigo_de_persona` != $default) ORDER BY `f_f2cd801e90b78ef4dc673a4659c1482d` DESC LIMIT 0,1";
		$xCache		= new cCache();
		$d			= $xCache->get("persona-es-user-$persona");
		if($d === null){
			$d		= obten_filas($sql);
			$xCache->set("persona-es-user-$persona", $d);
		}
		if(isset( $d["codigo_de_persona"] )){
			$result	= true;
			if($init == true){
				$clave	= $d["idusuarios"];
				$xSA	= new cSystemUser($clave);
				$xSA->init();
				$this->mObjUser	= $xSA;
			}
		} else {
			$this->mMessages	.= "WARN\tNo hay Usuario asociado\r\n";
		}
		return $result;
	}
	function getIDSucursalAsociada(){ return $this->mIDSucursal; }
	function getEsSucursal(){
		
		$persona	= $this->mCodigo;
		$result		= false;
		$sql		= "SELECT	`general_sucursales`.* FROM	`general_sucursales` `general_sucursales` WHERE	(`general_sucursales`.`clave_de_persona` = $persona ) LIMIT 0,1";
		$xCache		= new cCache();
		$D			= $xCache->get("persona-es-sucursal-$persona");
		if($D === null){
			$xQL	= new MQL();
			$D		= $xQL->getDataRow($sql);
			$xCache->set("persona-es-sucursal-$persona", $D);
		}
		if(isset($D["codigo_sucursal"]) ){
			$this->mIDSucursal	= $D["codigo_sucursal"];
			$result	= true;
		}
		return $result;
	}
	function getOEmpresa(){ return $this->mObjEmpresa; }
	function getOUsuario(){ return $this->mObjUser; }
	function getOGrupoSol(){ return $this->mObjGrupoS; }
	function getEstadoActual(){ return $this->mEstadoActual; }
	function getORepresentanteLegal(){
		if($this->getFiguraJuridica() == PERSONAS_FIGURA_MORAL OR $this->getFiguraJuridica() == PERSONAS_FIGURA_MORAL_EXENTA){
			if($this->mORepLegal == null){
				//OBTENER REPRESENTANTE LEGAL
				$xRels	= new cPersonasRelaciones(false, $this->getCodigo());
				if($xRels->initRelacionPorTipo(PERSONAS_REL_REP_LEGAL) == true){
					$this->mORepLegal	= $xRels;
				}
			}
		}
		return $this->mORepLegal;
	}

	function getNumeroDeCreditoPrimario($tipo = false){
		$ByTipo = " AND monto_autorizado > 0 ";//($tipo == "todos") ? "AND monto_autorizado > 0" : "  ";
		$socio	= $this->mCodigo;
		switch($tipo){
			case CREDITO_ESTADO_SOLICITADO:
				$ByTipo	= " AND getEsCancelado(numero_solicitud) = 0 AND `estatus_actual` =  " . CREDITO_ESTADO_SOLICITADO;
				break;
			case CREDITO_ESTADO_AUTORIZADO:
				$ByTipo	= " AND `estatus_actual` =  " . CREDITO_ESTADO_AUTORIZADO;
				break;
			default:
				$ByTipo	= " AND monto_autorizado > 0 ";
				break;
		}
		$sqllc  = "SELECT numero_solicitud  FROM creditos_solicitud
		WHERE numero_socio=$socio $ByTipo
		ORDER BY saldo_actual DESC, fecha_vencimiento ASC, fecha_solicitud DESC LIMIT 0,1";
		$miro = 0;
		$miro = mifila($sqllc, "numero_solicitud");
		return $miro;
	}
	function getAhorroPreferente(){
		$xOP	= new cPersonasCatalogoOtrosDatos();
		$monto 	= $this->getOtrosParametros($xOP->PERSONAS_MONTO_AHORRO_PREFERENTE);
		return setNoMenorQueCero($monto);
	}

	function addOtrosParametros($tipo, $valor, $expira = false){
		$ql			= new MQL();
		$xF			= new cFecha();
		$fecha		= $xF->get();
		$expira		= ($expira == false) ? $xF->getFechaMaximaOperativa() : $expira;
		$tipo		= strtoupper($tipo);
		$persona	= $this->mCodigo;
		$user		= getUsuarioActual();
		$suc		= getSucursal();
		$run		= true;
		$D			= $ql->getDataRow("SELECT * FROM socios_otros_parametros WHERE clave_de_persona=$persona AND clave_del_parametro='$tipo' AND fecha_de_expiracion > '$fecha' LIMIT 0,1");
		if(isset($D["fecha_de_expiracion"])){
			if($D["valor_del_parametro"] == $valor){
				$run = false;
				$this->mMessages	.= "WARN\tNada que actualizar en $tipo  con Valor $valor\r\n";
			}
		} 
		if($run == true) {
			//actualizar el otro
			$sqlU	= "UPDATE socios_otros_parametros SET fecha_de_expiracion='$fecha'  WHERE clave_de_persona=$persona AND clave_del_parametro='$tipo' AND fecha_de_expiracion > '$fecha'";
			$ql->setRawQuery($sqlU);
			//agregar
			$sqlI	= "INSERT INTO socios_otros_parametros		(clave_de_persona, clave_del_parametro, valor_del_parametro, fecha_de_alta, fecha_de_expiracion, idusuario, sucursal) 
	    VALUES($persona, '$tipo', '$valor', '$fecha', '$expira', '$user', '$suc')";
			$ql->setRawQuery($sqlI);
			$this->mMessages		.= "WARN\tProcesando el parametro $tipo a valor $valor y vigencia $expira\r\n";
			//Agregar si encuentra la palabra AML
			if(MODULO_AML_ACTIVADO == true){
				//
				$xCat		= new cPersonasCatalogoOtrosDatos();
				//if(preg_match("/AML_/", $tipo)){
				if($tipo == $xCat->AML_PEP_PRINCIPAL){
					//$xNot	= new cNotificaciones();
					//$xNot->send($mensaje);
					$xAML = new cAMLPersonas($this->mCodigo);
					$xAML->setActualizarRiesgoPorNucleo();
				}
			}
		}
	}
	function getOtrosParametros($tipo){
		$ret		= null;
		
		$xF			= new cFecha();
		$fecha		= $xF->get();
		$xCache		= new cCache();
		$tipo		= strtoupper($tipo);
		$persona	= $this->mCodigo;
		$IDCache	= TPERSONAS_OPARAMS . "-persona-$persona-clave-$tipo";
		$d			= $xCache->get($IDCache);
		if(!is_array($d)){
			$ql			= new MQL();
			$sqlS		= "SELECT *	FROM socios_otros_parametros WHERE clave_de_persona = $persona AND  clave_del_parametro='$tipo' AND fecha_de_expiracion >'$fecha' LIMIT 0,1";
			$d			= obten_filas($sqlS);
			$ql			= null;
			$d			= $xCache->set($IDCache, $d);
		}
		$xOD		= new cSocios_otros_parametros();
		
		if(isset($d[ $xOD->idsocios_otros_parametros()->get() ])){
			$xOD->setData($d);
			$ret	= $xOD->valor_del_parametro()->v(OUT_TXT);
		}
		return $ret;
	}
	
	function getExportarAsociada($tipo, $host = SVC_ASOCIADA_HOST){
		//TPERSONAS_GENERALES
		$svc		= new MQLService("", "");
		$xTu		= new cSystemUser( TASK_USR, false );
		$xTu->init();
		$ctx		= $xTu->getCTX();
		
		//Datos generales
		$cmd		= $svc->getEncryptData($tipo);
		$data		= $svc->getEncryptData($this->mCodigo);
		$url		= $host . "svc/importar.svc.php?ctx=$ctx&cmd=$cmd&data=$data";
		$svc->getService($url);
		$this->mMessages				.= "WARN\tEjecutando servicio de importacion a la URL $url\r\n";		
	}
	
	function getImportarDesdeAsociada($tipo){
		$result		= true;
		$svc		= new MQLService("", "");
		$cmd		= $svc->getEncryptData($tipo);
		$xTu		= new cSystemUser( TASK_USR, false );
		$xTu->init();
		$ctx		= $xTu->getCTX();
		$data		= $svc->getEncryptData($this->mCodigo);
		$host		= SVC_ASOCIADA_HOST;
		switch ($tipo){
			case TPERSONAS_GENERALES:
				$dpersona	= $svc->getService($host . "svc/exportar.svc.php?ctx=$ctx&data=$data&cmd=$cmd");
		
				if(is_array($dpersona)){
					$xSoc	= new cSocios_general($dpersona);
					if(setNoMenorQueCero( $xSoc->codigo()->v()) > 0){
						$xSoc->query()->insert()->save();
						$result		= true;
					}
				}
				break;
			case TPERSONAS_DIRECCIONES:
				$ddomicilio	= $svc->getService($host . "svc/exportar.svc.php?ctx=$ctx&data=$data&cmd=$cmd");
				if(is_array($ddomicilio)){
					$xDom	= new cSocios_vivienda($ddomicilio);
					if(setNoMenorQueCero( $xDom->idsocios_vivienda()->v() ) ){
						$xDom->query()->insert()->save();
						$result		= true;
					}
				}
				break;
			case TPERSONAS_ACTIVIDAD_ECONOMICA:
				$dtrabajo	= $svc->getService($host . "svc/exportar.svc.php?ctx=$ctx&data=$data&cmd=$cmd");
				if(is_array($dtrabajo)){
					$xTrab	= new cSocios_aeconomica ($dtrabajo);
					if(setNoMenorQueCero( $xTrab->idsocios_aeconomica()->v() ) ){
						$xTrab->query()->insert()->save();
						$result		= true;
					}
				}
				break;
			case TCATALOGOS_EMPRESAS:
				$dempresa	= $svc->getService($host . "svc/exportar.svc.php?ctx=$ctx&data=$data&cmd=$cmd");
				if(is_array($dempresa)){
					$xEmp	= new cSocios_aeconomica_dependencias($dempresa);
					if(setNoMenorQueCero( $xEmp->idsocios_aeconomica_dependencias()->v() ) ){
						$xEmp->query()->insert()->save();
						$result		= true;
					}
				}
				break;				
		}
		return $result;		
	}
	function ODatosDeBajaSupension(){
		$fecha		= $xF->getFechaISO();
		$xQL		= new MQL();
		$sql1		= "SELECT `socios_baja`.* FROM `socios_baja` `socios_baja` WHERE (`socios_baja`.`fecha_de_vencimiento` <='$fecha') ";
		$D			= $xQL->getDataRow($sql1);
		$xOB		= null;
		if(isset($D["idsocios_baja"])){
			$xOB	= new cSocios_baja();
			$xOB->setData($D);
		}
		return $xOB;
	}
	function getPermisoParaOperar($evento = false){
		$xF			= new cFecha();
		$operable	= $this->mSocioIniciado;
		$xLog		= new cCoreLog();
		$xRuls		= new cReglaDeNegocio();
		$conPersonaR= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_OPERAR_ALTO_R);
		if($this->getEstadoActual() == PERSONAS_ESTADO_INACTIVO){
			$operable	= false;
			$xLog->add("ERROR\tNo puede operar por estar inactivo\n");
		}
		//obtener AML
		if(MODULO_AML_ACTIVADO == true){
			$xR		= new cReglasDeNegocioLista();
			//if($xR->SOLICITAR_PERFIL_TRANSACCIONAL == true){}
			if ( $this->getEsPersonaRiesgosa() == true AND $conPersonaR == false){
				$operable		= false;
				$xLog->add("WARN\tNo puede operar por ser Riesgosa\r\n");
			}
			if($this->getEsPersonaSDN() == true){
				$operable		= false;
				$xLog->add("ERROR\tNo puede operar por Estar en LISTA_NEGRA\r\n");
			}
			//Leer Omisiones
			$xOPer	= new cAMLPersonasOmisiones();
			if($xOPer->initByPersona($this->getCodigo()) == true){
				if($xOPer->omitir() == true){
					$operable		= true;
					$xLog->add("WARN\tOmitido por el Oficial de Cumplimiento\r\n");					
				}
			}
		}
		
		$this->mMessages	.= $xLog->getMessages();
		return $operable;
	}
	function isOperable(){ if($this->mSocioIniciado == false){$this->init();}; return $this->getPermisoParaOperar(false); 	}
	function getUUID(){ return $this->mUUID; }
	function setRazonesDeNoFIEL($razones){ $xOP	= new cPersonasCatalogoOtrosDatos(); $this->addOtrosParametros($xOP->RAZONES_DE_FALTA_DE_FIEL, $razones);	}
	function addToGrupos($clave_de_presidente, $clave_de_vocal = false, $sucursal = false, $fecha = false){
		$xGpo	= new cGrupo(false);
		//$nombre, $direccion = "", $representante = false, $vocal_de_vigilancia = false, $estatus = 10, $nivel = 1, $numero = false, $sucursal = false, $fecha = false, $clave_de_persona = false
		$xPViv	= new cPersonasVivienda($clave_de_presidente);
		$xPViv->init();
		$dom	= "";
		if($xPViv->isInit() == true){
			$xPViv->setDuplicarDomicilio($this->mCodigo);
			//duplicar
			$dom	= $xPViv->getDireccionBasica();
		} else {
			$this->mMessages	.= "WARN\tError en la carga de domicilio\r\n";
		}
		$add	= $xGpo->add($this->getNombreCompleto(), $dom, $clave_de_presidente, $clave_de_vocal, 10, 1, $this->getCodigo(), $sucursal, $fecha, $this->getCodigo());
		if($add == false){
			$this->mMessages	.= "ERROR\tError No se agrego el grupo\r\n";
			if(MODO_DEBUG == true){ $this->mMessages	.= $xGpo->getMessages(); }
		} else {
			$xGpo->init();
			$this->mObjGrupoS	= $xGpo;
		}
		return $add;
	}
	function getAlias(){
		$ql	= new MQL();
		$sql	= "SELECT * FROM `socios_relaciones` WHERE `socio_relacionado`=" . $this->getCodigo() . " AND (`tipo_relacion`= 511 OR `tipo_relacion`= 512)";
		$rs		= $ql->getDataRecord($sql);
		$xRel	= new cSocios_relaciones();
		$alias	= "";
		foreach ($rs as $rw){
			$xRel->setData($rw);
			$alias	.= ($alias == "") ? "" : ", ";
			$alias	.= $xRel->ocupacion()->v() . ": " . $xRel->apellido_paterno()->v() . " " . $xRel->apellido_materno()->v() . " " . $xRel->nombres()->v();
		}
		return strtoupper($alias);
	}
	function getObservaciones(){ return $this->mObservaciones; }
	function getPuedeSerRelacion($tipo){
		
	}
	function setCuandoSeActualiza(){
		//Actualizar Relaciones
		$xQl		= new MQL();
		$rs			= $xQl->getDataRecord("SELECT * FROM `socios_relaciones` WHERE `numero_socio`= ". $this->mCodigo);
		$xcRel		= new cSocios_relaciones();
		$idpersona	= $this->mCodigo;
		$xCache		= new cCache();
		
		if($this->mIDEnCache !== null){
			$xCache->clean($this->mIDEnCache);
		}
		
		$xCache->clean("persona-$idpersona");
		$xCache->clean("personas_datos_colegiacion-persona-$idpersona");//datos de colegiacion
		$xCache->clean(TPERSONAS_GENERALES . "-$idpersona");
		$xCache->clean(EACP_CLAVE . ".ficha.$idpersona.ext");
		$xCache->clean(EACP_CLAVE . ".ficha.$idpersona.ext.tiny");
		$xCache->clean(EACP_CLAVE . ".ficha.$idpersona.tiny");
		
		$xCache->clean(EACP_CLAVE . ".ficha.$idpersona.ext.sm");
		$xCache->clean(EACP_CLAVE . ".ficha.$idpersona.ext.tiny.sm");
		$xCache->clean(EACP_CLAVE . ".ficha.$idpersona.tiny.sm");
		$xCache->clean("persona-creditomaximo-$idpersona");
		$xCache->clean("persona-telefonos-$idpersona");
		//
		foreach ($rs as $rows){
			$xcRel->setData($rows);
			$id			= $xcRel->idsocios_relaciones()->v();
			$persona	= $xcRel->socio_relacionado()->v();
			
			$xRe		= new cPersonasRelaciones($id, $persona);
			
			if($xRe->init() == true){ $xRe->setActualizarPorPersona(); $this->mMessages .= $xRe->getMessages(); }
		}
		//Actualizar Empresa
		if($this->getEsEmpresaConConvenio(true) == true){
			$xEmp		= $this->getOEmpresa();
			if($xEmp != null){ $xEmp->setActualizarPorPersona(); $this->mMessages .= $xEmp->getMessages(); }
		}
		//Actualizar Grupo Solidario
		if($this->getEsGrupoSolidario(true)){
			$xGrp		= $this->getOGrupoSol();
			if($xGrp != null){ $xGrp->setActualizarPorPersona(); $this->mMessages .= $xGrp->getMessages(); }
		}
		//actualizar Sucursal
		if($this->getEsSucursal() == true){
			$xSuc		= new cSucursal($this->mIDSucursal);
			$xSuc->setActualizarPorPersona();
			$this->mMessages	.= $xSuc->getMessages();
		}
		//Actualizar Usuario
		if($this->getEsUsuario(true)){
			$xUsr	= $this->getOUsuario();
			if($xUsr != null){	$xUsr->setActualizarPorPersona(); $this->mMessages .= $xUsr->getMessages(); }
		}
		//if(MODULO_AML_ACTIVADO == true AND $this->mNoAML == false){
			//$this->setAMLAutoActualizarNivelRiesgo();
			//$xAML	= new cAMLPersonas($this->mCodigo);
			//$xAML->init($this->mCodigo, $this->getDatosInArray());
			//$xAML->setActualizarRiesgoPorNucleo();
			//$this->mMessages .= $xAML->getMessages();
		//}
	}
	function getNacionalidad(){ return $this->mNacionalidad; }
	
	function setBaja($idrazon, $notas = "", $fecha = false, $fecha_de_vencimiento = false, $documento = "1", $fecha_de_documento	= false){
		$persona	= $this->mCodigo;
		$xF			= new cFecha();
		$xB			= new cSocios_baja();
		$xQL		= new MQL();
		$fecha		= $xF->getFechaISO($fecha);
		$fecha_de_vencimiento	= ($fecha_de_vencimiento == false) ?  $xF->getFechaMaximaOperativa() : $fecha_de_vencimiento;
		$fecha_de_documento		= ($fecha_de_documento == false) ? $fecha : $fecha_de_documento;
		//Actualizar las bajas activas
		$sqlU		= "UPDATE `socios_baja` SET `fecha_de_vencimiento` = '$fecha' WHERE `fecha_de_vencimiento`>='$fecha' AND `numero_de_socio`= $persona";
		$xQL->setRawQuery($sqlU);
		
		$xB->docto_presentado($documento);
		$xB->fecha_de_baja($fecha);
		$xB->fecha_de_documento($fecha_de_documento);
		$xB->fecha_de_vencimiento($fecha_de_vencimiento);
		$xB->numero_de_socio($persona);
		$xB->observaciones_de_baja($notas);
		$xB->razon_de_la_baja($idrazon);
		$xB->sucursal(getSucursal());
		$xB->idsocios_baja( $xB->query()->getLastID() );
		$succ	= $xB->query()->insert()->save();
		if($succ == false){
			
		} else {
			$this->addMemo(MEMOS_TIPO_HISTORIAL, "BAJA-SUSPENSION: $notas", $fecha);
			$this->setUpdate(array("estatusactual" => 20));
		}
		//agregar Nota
		
		return ($succ == false) ? false : true;
	}
	function getIDNucleoDeRiesgo(){
		$ql			= new MQL();
		$persona	= $this->getCodigo();
		$D			= $ql->getDataRow("SELECT * FROM `personas_relaciones_recursivas` WHERE relacion=$persona OR persona=$persona LIMIT 0,1");
		if(isset($D["persona"])){	$this->mIDNucleoRiesgo	= $D["persona"]; }
		return $this->mIDNucleoRiesgo;
	}
	function setClasificacionesExtras($xclass = false, $yclass = false, $zclass= false){
		$xclass	= setNoMenorQueCero($xclass);
		$yclass	= setNoMenorQueCero($yclass);
		$zclass	= setNoMenorQueCero($zclass);
		$arr	= array();
		if($xclass>0){ $arr["xclasificacion"]=$xclass; }
		if($yclass>0){ $arr["yclasificacion"]=$yclass; }
		if($zclass>0){ $arr["zclasificacion"]=$zclass; }
		$this->setUpdate($arr);
	}
	function setRegion($region = false, $actualizar = false){
		$region	= setNoMenorQueCero($region);
		if($region >0){
			$this->mRegion	= $region;
			if($actualizar == true){
				$this->setUpdate(array("region" => $region), true);
			}
		}
	}
	function getRegion(){return $this->mRegion;}
	function setDatosExtranjero($idpermiso, $FechaDeInicio = false, $FechaDeVencimiento = false, $nacionalidad = EACP_CLAVE_DE_PAIS){
		$xQL				= new MQL();
		$xF					= new cFecha();
		$FechaDeInicio		= $xF->getFechaISO($FechaDeInicio);
		$FechaDeVencimiento	=$xF->getFechaISO($FechaDeVencimiento);
		$persona			= $this->mCodigo;
		$nacionalidad		= strtoupper($nacionalidad);
		$ready				= false;
		if(trim($idpermiso) != ""){
		$xQL->setRawQuery("DELETE FROM `personas_datos_extranjero` WHERE `clave_de_persona`=$persona");
			$sql		= "INSERT INTO `personas_datos_extranjero`(`clave_de_persona`,`clave_permiso_de_residencia`,`fecha_de_inicio_residencia`,`fecha_de_vencimiento`, `pais_de_nacionalidad`)
					VALUES ($persona, '$idpermiso', '$FechaDeInicio', '$FechaDeVencimiento', '$nacionalidad') ";
			$ready		= $xQL->setRawQuery($sql);
			$this->mMessages	.= "OK\tADD_EXTR\tAgregar extranjero $idpermiso fechas $FechaDeInicio\r\n";
			$this->setUpdate(array("nacionalidad_extranjera" => "1"), true);
			if(MODULO_AML_ACTIVADO == true){
				$xCat	= new cPersonasCatalogoOtrosDatos();
				$this->addOtrosParametros($xCat->PERSONAS_ES_EXTRANJERO, "1");
				$this->setAMLAutoActualizarNivelRiesgo();
			}			
		}
		return $ready;
	}
	function getDatosExtrajero(){
		$xQL			= new MQL();
		$persona		= $this->mCodigo;
		$datos			= $xQL->getDataRow("SELECT * FROM `personas_datos_extranjero` WHERE `clave_de_persona`=$persona LIMIT 0,1");
		$xF				= new cFecha();
		if(isset($datos["clave_de_persona"])){
			$xExt		= new cPersonas_datos_extranjero();
			$xExt->setData($datos);
			$this->mExtranjeroFechaInicial 	= $xF->getFechaISO($xExt->fecha_de_inicio_residencia()->v());
			$this->mExtranjeroFechaFin		= $xF->getFechaISO($xExt->fecha_de_vencimiento()->v());
			$this->mExtranjeroPermiso		= $xExt->clave_permiso_de_residencia()->v();
			$this->mNacionalidad			= strtoupper($xExt->pais_de_nacionalidad()->v());
			//setLog(" NADA " . $this->mNacionalidad);
		}
	}
	function setDatosColegiacion($membresia, $lugarpago, $diapago, $gradoacademico, $contactoemergencia ="", $idcolegiacion = ""){
		$membresia		= setNoMenorQueCero($membresia);
		$lugarpago		= setNoMenorQueCero($lugarpago);
		$diapago		= setNoMenorQueCero($diapago);
		$gradoacademico	= setNoMenorQueCero($gradoacademico);
		$idcolegiacion	= trim(setCadenaVal($idcolegiacion));
		$xQL			= new MQL();
		$xF				= new cFecha();
		$persona		= $this->mCodigo;
		$ready			= false;
		if($membresia > 0 AND $lugarpago > 0 AND $diapago > 0 AND $gradoacademico > 0){
			$t			= "personas_datos_colegiacion";
			$DD			= $xQL->getDataRow("SELECT * FROM `$t` WHERE `clave_de_persona`=$persona LIMIT 0,1");
			if(isset($DD["idpersonas_datos_colegiacion"])){
				$id		= $DD["idpersonas_datos_colegiacion"];
				$sql	= "UPDATE `personas_datos_colegiacion` SET 
						`dia_de_pago`=$diapago,
						`tipo_de_lugar_de_pago`=$lugarpago,
						`tipo_de_afiliacion`=$membresia,
						`datos_de_emergencia`='$contactoemergencia',
						`grado_academico`=$gradoacademico,
						`numero_de_colegiacion`='$idcolegiacion'
						WHERE `idpersonas_datos_colegiacion`=$id";
				$ready	= $xQL->setRawQuery($sql);
			} else {
				$sql	= "INSERT INTO `personas_datos_colegiacion` (`clave_de_persona`,`dia_de_pago`,`tipo_de_lugar_de_pago`,`tipo_de_afiliacion`,`datos_de_emergencia`,`grado_academico`,`numero_de_colegiacion`)
				VALUES ($persona,  $diapago, $lugarpago, $membresia, '$contactoemergencia', $gradoacademico, '$idcolegiacion') ";
				$ready	= $xQL->setRawQuery($sql);
			}
				
			
			$this->mMessages	.= "OK\tADD_MEMB\tAgregar Membresia $membresia, Grado $gradoacademico\r\n";
			$this->setCuandoSeActualiza();
		}
		if($ready === false){
			$ready	= false;
		} else {
			$ready	= true;
			$this->mMembresiaDiaPago	= $diapago;
			$this->mMembresiaGradoAca	= $gradoacademico;
			$this->mMembresiaLugarPago	= $lugarpago;
			$this->mMembresiaTipo		= $membresia;
			$this->mMembresiaID			= $idcolegiacion;
			$this->mMembresiaAcc		= $contactoemergencia;
			$this->setCuandoSeActualiza();
		}
		return $ready;		
	}
	function getDatosColegiacion(){
		$persona		= $this->mCodigo;
		$xCache			= new cCache();
		$idc			= "personas_datos_colegiacion-persona-$persona";
		$datos			= $xCache->get($idc);
		if(!is_array($datos)){
			$xQL		= new MQL();
			$xF			= new cFecha();
			$datos		= $xQL->getDataRow("SELECT * FROM `personas_datos_colegiacion` WHERE `clave_de_persona`=$persona LIMIT 0,1");
		}
		if(isset($datos["clave_de_persona"])){
			$xExt		= new cPersonas_datos_colegiacion();
			$xExt->setData($datos);
			$this->mMembresiaDiaPago	= $xExt->dia_de_pago()->v();
			$this->mMembresiaGradoAca	= $xExt->grado_academico()->v();
			$this->mMembresiaLugarPago	= $xExt->tipo_de_lugar_de_pago()->v();
			$this->mMembresiaTipo		= $xExt->tipo_de_afiliacion()->v();
			$this->mMembresiaID			= $xExt->numero_de_colegiacion()->v();
			$this->mMembresiaAcc		= $xExt->datos_de_emergencia()->v();
			$xCache->set($idc, $datos, $xCache->EXPIRA_MEDDIA);
		}		
	}
	function setDatosDeProveedor(){
		$xQL	= new MQL();
		//Eliminar datos anteriores
		$xQL->setRawQuery("DELETE FROM `personas_proveedores` WHERE `persona`=" . $this->mCodigo);
		$xProv	= new cPersonas_proveedores();
		$xProv->idpersonas_proveedores("NULL");
		$xProv->persona($this->mCodigo);
		$res	= $xProv->query()->insert()->save();
		
	}
	function getMembresiaID(){ return $this->mMembresiaID; }
	function getMembresiaGrado(){ return $this->mMembresiaGradoAca; }
	function getMembresiaDiaPag(){ return $this->mMembresiaDiaPago; }
	function getMembresiaAcc(){ return $this->mMembresiaAcc; }
	function getMembresiaLugarPag(){ return $this->mMembresiaLugarPago; }
	function getMembresiaTipo(){ return $this->mMembresiaTipo; }
	function setEsCliente(){
		$this->setUpdate(array("tipoingreso" => TIPO_INGRESO_CLIENTE), true);
	}
	function setSitioWeb($http){$this->setUpdate(array("sitioweb" => $http), true);	}
	function setIDInterno($id){ $this->setUpdate(array("idinterna" => $id), true); }
	function setDatosPersonasMorales($IDRegistroPublico, $IDActaConst = "", $FechaConst = false, $NombreNotario = "", $IdNotaria ="", $IDPoder = "", $FechaPoder = false, $NotarioPoder = "", $IDNotariaPoder  =""){
		$xQL		= new MQL();
		$xF			= new cFecha();
		$FechaConst	= $xF->getFechaISO($FechaConst);
		$FechaPoder	= $xF->getFechaISO($FechaPoder);
		$FechaBaja	= $xF->getFechaMaximaOperativa();
		$FechaReg	= $xF->get();
		
		$persona	= $this->mCodigo;
		$sql		= "INSERT INTO `personas_morales_anx`(`persona`,`idregistro_publico`,`fecha_de_constitucion`,`idacta_constitucion`,`idpoder_representante`,
			`fechapoder_representante`,`nombre_notario`,`clave_notaria`,`activo`,`fecha_de_baja`,`notaria_poder`,`notario_poder`) VALUES 
				($persona, '$IDRegistroPublico', '$FechaConst', '$IDActaConst', '$IDPoder', '$FechaPoder', '$NombreNotario', '$IdNotaria', " . SYS_UNO . ", '$FechaBaja', '$IDNotariaPoder', '$NotarioPoder')";
		//Desactivar el anterior.
		$sqlU		= "UPDATE `personas_morales_anx` SET `activo` = " . SYS_CERO . ",`fecha_de_baja`='$FechaReg' WHERE `persona`= $persona ";
		$xQL->setRawQuery($sqlU);
		//Agregar el registro Nuevo.
		$rs 		= $xQL->setRawQuery($sql);
	}
	function getOEventos(){
		$mEvt	= new cPersonasProceso();
		return $mEvt;
	}
}
/**
 * Clase de Manejo de Tipo de Ingreso
 */
class cPersonasTipoDeIngreso{
 	private $mTipoDeIngreso		= 99;
 	private $mArrDatos			= false;
	private $mInit				= false;
	private $mDescripcion		= "";
	public $TIPO_CLIENTE		= 200;
	public $TIPO_GRUPO			= 300;
	public $TIPO_PROVEEDOR		= 905;
	public $TIPO_RELACION		= 500;
	
 	function __construct($tipo_de_ingreso){
 		$this->mTipoDeIngreso	= setNoMenorQueCero( $tipo_de_ingreso );
		//if(this->mTipoDeIngreso > 0){ $this->init();	}
 	}
 	function getCuotasSociales(){
		$cuota	= $this->mArrDatos["parte_social"] + $this->mArrDatos["parte_permanente"];
		return $cuota;
 	}
 	function getPartePermanente(){
		return $this->mArrDatos["parte_permanente"];
 	}
 	function getParteSocial(){
		return $this->mArrDatos["parte_social"];
 	}
	function init(){
		$xCache		= new cCache();
		
		
		//idsocios_tipoingreso, descripcion_tipoingreso, descripcion_detallada, parte_social, parte_permanente
		$this->mArrDatos		= $xCache->get("datos-tipo-ingreso-" . $this->mTipoDeIngreso);
		if($this->mArrDatos == null){
			$ql					= new MQL();
			$sql 				= "SELECT * FROM socios_tipoingreso WHERE idsocios_tipoingreso = " . $this->mTipoDeIngreso . " LIMIT 0,1";
			$this->mArrDatos	= $ql->getDataRow($sql);
			$xCache->set("datos-tipo-ingreso-" . $this->mTipoDeIngreso, $this->mArrDatos);
		}
		if(isset($this->mArrDatos["idsocios_tipoingreso"])){
			$this->mInit		= true;
			$this->mDescripcion	= $this->mArrDatos["descripcion_tipoingreso"];
		}
		return $this->mInit;
	}
	function getNombre(){ return $this->mDescripcion; }
	function getDatosInArray(){ return $this->mArrDatos; }
}

/**
 * Clase de trabajo del Oficial de Credito
 * @version 1.01
 * @package common
 * @subpackage core
 */
class cOficial{
	protected $mCodigoDeOficial		= false;
	protected $mDatosInArray		= array();
	private $mMessages				= "";
	private $mMail					= "";
	private $mOPers					= null;
	private $mClaveDePersona		= 0;
	private $mAsInit				= false;
	
	function __construct($oficial = false){
		$oficial	= setNoMenorQueCero($oficial);
		$oficial	= ($oficial <= 0) ? getUsuarioActual() : $oficial;
		$this->mCodigoDeOficial	= $oficial;
	}
	/**
	 * Agrega una Nota al Oficial de Destino
	 * @return boolena Estatus de la Operacion
	 */
	function addNote($tipo, $oficial, $socio, $docto, $texto, $fecha = false){
		$xF					= new cFecha();
		$xT					= new cTipos();
		$xQL				= new MQL();
		$oficial_de_origen	= $this->mCodigoDeOficial;
		$oficial			= setNoMenorQueCero($oficial);
		$oficial			= ($oficial <= 0) ? $this->getCodigo() : 1;
		$fecha				= $xF->getFechaISO($fecha);
		
		$msg				= "";
		$texto				= $xT->cChar( trim($texto) );
		
		$sqlFR		= "INSERT INTO usuarios_web_notas( tipo, oficial, oficial_de_origen, socio, documento, fecha, texto)
    					VALUES
						('$tipo', $oficial, $oficial_de_origen, $socio, $docto, '$fecha', '$texto')";
		$x 			=  $xQL->setRawQuery($sqlFR);
		if($x == false){
			$msg		.= "ERROR\tAviso al oficial $oficial NO GENERADO ($tipo|$socio|$docto|$fecha)\r\n";
		} else {
			$msg		.= "ERROR\tAviso al oficial $oficial Agregado con Exito ($tipo|$socio|$docto|$fecha)\r\n";
		}
		return $msg;
	}
	function init(){
		$sql	= "SELECT
				`t_03f996214fba4a1d05a68b18fece8e71`.`idusuarios` AS `codigo`,
				`t_03f996214fba4a1d05a68b18fece8e71`.`nombres`,
				`t_03f996214fba4a1d05a68b18fece8e71`.`apellidopaterno`,
				`t_03f996214fba4a1d05a68b18fece8e71`.`apellidomaterno`,
				`t_03f996214fba4a1d05a68b18fece8e71`.`puesto`,
				`t_03f996214fba4a1d05a68b18fece8e71`.`sucursal`,
				`t_03f996214fba4a1d05a68b18fece8e71`.`estatus`,
				`t_03f996214fba4a1d05a68b18fece8e71`.`codigo_de_persona`
			FROM
				`t_03f996214fba4a1d05a68b18fece8e71` `t_03f996214fba4a1d05a68b18fece8e71` 
			WHERE
				(`t_03f996214fba4a1d05a68b18fece8e71`.`idusuarios` =" . $this->mCodigoDeOficial . ") LIMIT 0,1";
		$this->mDatosInArray			= obten_filas($sql);
		$this->mClaveDePersona			= $this->mDatosInArray["codigo_de_persona"];
		if(isset($this->mDatosInArray["nombres"])){
			$this->mAsInit	= true;
		}
		return $this->mAsInit; 
	}
	function getDatos(){
		$this->init();
		return $this->mDatosInArray;
	}
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function getNombreCompleto(){
		$xT			= new cTipos();
		$xT->setForceMayus();
		$nombre		= $this->mDatosInArray["nombres"] . " " . $this->mDatosInArray["apellidopaterno"] .  " " . $this->mDatosInArray["apellidomaterno"];
		$nombre		= $xT->cChar($nombre);
		return $nombre;
	}
	function getOPersona(){
		if($this->mOPers == null){
			if( ($this->mClaveDePersona != DEFAULT_SOCIO) AND ( setNoMenorQueCero($this->mClaveDePersona) > 1 ) ){
				$this->mOPers		= new cSocio($this->mClaveDePersona); $this->mOPers->init();
			}
		}
		return $this->mOPers;
	}
	function getEmail(){
		$mail		= "";
		if($this->getOPersona() != null){
			$mail		= $this->getOPersona()->getCorreoElectronico();
		}
		return $mail;
	}
	function getCodigo(){ return $this->mCodigoDeOficial; }
}




class cEmpresas {
	private $mClave					= "";
	private $mDatos					= "";
	private $mPeriodo				= false;
	private $mClaveDePersona		= false;
	private $mOB					= null;
	private $mObjPersona			= null;
	private $mMessages				= "";
	private $mClearPeriodo			= false;
	private $mMailsEnvio			= array();
	private $mMails					= "";
	private $mOficial				= 1;
	private $mOperacionesEnv		= 0;
	private $mMontoEnv				= 0;
	private $mPeriocidadPref		= false;
	private $mProductoPref			= false;
	private $mDiasDeAviso			= "";			//dias en que se le avisa
	private $mDiasDePago			= "";			//dias en que debe emitir el pago
	private $mDiasDeNomina			= "";			///dias en que paga su noina
	private $mFormatoDeAviso		= 4001;
	private $mIDPersonaCont			= 0;		//Persona de contacto
	private $mNombrePersonaCont		= "";
	private $mListaDePeriocidad		= array();
	private $mInit					= false;
	private $mODomicilio			= null;
	private $mTasaComision			= 0;
	private $mNombreLargo			= "";
	private $mIDDomicilio			= 0;
	private $mDDDomicilio			= array();
	private $mIDCache				= "";
	function __construct($clave = false){		$this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave);	}
	function setIDCache($clave){if($clave >0){ $this->mIDCache = TCATALOGOS_EMPRESAS . "-" . $this->mClave; } }
	function setCleanCache(){$xCache = new cCache(); $xCache->clean($this->mIDCache); }
	function getClaveDeEmpresa(){ return $this->mClave; }
	function init($datos = false){
		$this->mOB					= new cSocios_aeconomica_dependencias();
		$xCache						= new cCache();
		
		if(!is_array($datos)){
			$datos					= $xCache->get($this->mIDCache);
			if(!is_array($datos)){
				$datos				= obten_filas("SELECT * FROM `socios_aeconomica_dependencias` WHERE `idsocios_aeconomica_dependencias` = " . $this->mClave  . " LIMIT 0,1");
				if(isset($datos["idsocios_aeconomica_dependencias"])){
					$xCache->set($this->mIDCache, $datos);
				}
			}
		}
		$this->mDatos				= $datos;
		$datos						= null;
		$this->mOB->setData( $this->mDatos );
		$this->mPeriodo				= $this->mOB->ultimo_periodo_enviado()->v();
		$this->mClaveDePersona		= $this->mOB->clave_de_persona()->v();
		$this->mMails				= $this->mOB->email_de_envio()->v();
		$this->mOficial				= $this->mOB->oficial_que_cierra()->v();
		$this->mProductoPref		= $this->mOB->producto_preferente()->v();
		$this->mPeriocidadPref		= $this->mOB->periocidad_de_avisos()->v();
		$this->mDiasDeAviso			= $this->mOB->dias_de_avisos()->v();
		$this->mDiasDeNomina		= $this->mOB->dias_de_pago_nomina()->v();
		$this->mDiasDePago			= $this->mOB->dias_de_liquidacion()->v();
		$this->mFormatoDeAviso		= $this->mOB->formato_de_envio()->v();
		$this->mIDPersonaCont		= setNoMenorQueCero($this->mOB->clave_de_directivo()->v());
		$this->mNombrePersonaCont	= $this->mOB->directivo_principal()->v();
		$this->mTasaComision		= $this->mOB->comision_por_encargo()->v();
		$this->mInit				= true;
		$this->mNombreLargo			= $this->mOB->descripcion_dependencia()->v();
		return $this->mDatos;
	}
	function getDatos(){	return $this->mDatos;	}
	function getDomicilio(){	return $this->mDatos["domicilio_completo"];	}
	function getTelefono(){	return $this->mDatos["telefono"];	}
	function getNombre(){		return $this->mNombreLargo;	}
	function getNombreCorto(){		return $this->mDatos["nombre_corto"];	}
	function getClaveDeContacto(){ return $this->mIDPersonaCont; }
	function getNombreContacto(){ return $this->mNombrePersonaCont; }
	function getOPersona(){
		if($this->mObjPersona == null){
			$xSoc	= new cSocio($this->mClaveDePersona);
			if( $xSoc->init($this->mClaveDePersona) == true ){
				$this->mMessages	.= "OK\tPersona Cargada con el ID " . $xSoc->getCodigo() . "\r\n";
				$this->mObjPersona	= $xSoc;
			} else {
				$this->mMessages	.= "ERROR\tAl cargar persona con el ID " . $this->mClaveDePersona . "\r\n";
			}
		}
		return $this->mObjPersona;
	}
	function add($clave_de_persona, $nombre_del_contacto, $clave_de_contacto  = false, $dias_de_aviso = "", 
			$periocidad_de_aviso, $nombre_corto = "", $oficial_a_cargo = false, $emails = "", $producto_preferente = DEFAULT_TIPO_CONVENIO, $dias_de_nomina = "", $dias_de_pago = "", $comision = 0, $tasa =0){
		$res			= false;
		
		$clave_de_contacto	= ($clave_de_contacto == false) ? DEFAULT_SOCIO : $clave_de_contacto;
		$id				= $this->getLastID()+1;
		$xSoc			= new cSocio($clave_de_persona); $xSoc->init();
		
		$nombre			= $xSoc->getNombreCompleto();
		$domicilio		= $xSoc->getDomicilio();
		$DTels			= $xSoc->getTelefonos();
		$telefono		= $DTels["principal"];
		$mail			= $emails . $xSoc->getCorreoElectronico();
		$comision		= setNoMenorQueCero($comision);
		//$dias_de_aviso			= "";
		//$periocidad_de_aviso	= "";
		$oficial_a_cargo		= ($oficial_a_cargo == false) ? getUsuarioActual() : $oficial_a_cargo;
		//$nombre_corto			= "";
/*
descripcion_dependencia, domicilio_completo, directivo_principal, 
telefono, fecha_preferente_de_pago, clave_de_persona, clave_de_directivo, 
ultimo_periodo_enviado, fecha_de_envio, 
`formato_de_envio`
`formato_de_relacion`
 */
		$sql 			= "INSERT INTO socios_aeconomica_dependencias
		(idsocios_aeconomica_dependencias, descripcion_dependencia, domicilio_completo,
		directivo_principal, telefono, clave_de_persona, clave_de_directivo, dias_de_avisos, periocidad_de_avisos, 
		oficial_que_cierra, nombre_corto, email_de_envio, producto_preferente, dias_de_pago_nomina, dias_de_liquidacion, comision_por_encargo,`tasa_preferente`)
		VALUES ($id, '$nombre', '$domicilio', '$nombre_del_contacto', '$telefono', $clave_de_persona, $clave_de_contacto, '$dias_de_aviso', '$periocidad_de_aviso',
		 $oficial_a_cargo, '$nombre_corto', '$emails', $producto_preferente, '$dias_de_nomina', '$dias_de_pago', $comision, $tasa)";
		$xQL		= new MQL(); $cx = $xQL->setRawQuery($sql);
		$cx			= ($cx === false) ? false : true;
		if( $cx == true ) {
			$res	= 	$id;
			$this->mMessages	.= "OK\tRegistro de Empresa correcto\r\n";
		} else { $this->mMessages	.= "ERROR\tRegistro de Empresa fallido\r\n"; $id = false; }
		return $id;
	}
	function getClaveDePersona(){ return $this->mClaveDePersona; }
	function getClaveDeOficial(){ return $this->mOficial; }
	function getClaveDeDomicilio(){	if($this->mIDDomicilio<=0){ $this->initDDomicilio(); }; return $this->mIDDomicilio;	}
	function getDatosDeDomicilio(){	if($this->mIDDomicilio<=0){ $this->initDDomicilio(); }; return $this->mDDDomicilio; }
	private function initDDomicilio(){ 
		$xQL	= new MQL();
		$sql	= "SELECT `socios_vivienda`.* FROM `socios_vivienda` WHERE `socio_numero`  = " . $this->mClaveDePersona . " ORDER BY `calle` DESC,	`numero_exterior` DESC,	`fecha_alta` DESC LIMIT 0,1";
		$this->mDDDomicilio	= $xQL->getDataRow($sql);
		if(isset($this->mDDDomicilio["idsocios_vivienda"])){
			$this->mIDDomicilio	= $this->mDDDomicilio["idsocios_vivienda"];
		}		
	}
	function getLastID(){ 
		$xQL	 = new MQL(); $v = $xQL->getDataValue("SELECT MAX(idsocios_aeconomica_dependencias) AS 'lastid' FROM socios_aeconomica_dependencias", "lastid");
		return setNoMenorQueCero($v);
	}
	function setClearPeriodo($clear = true) { $this->mClearPeriodo = $clear; }
	function addOperacion($monto, $periodo, $periocidad, $fecha = false, $tipo = -1, $oficial = DEFAULT_USER, $observaciones = "", $FechaInicial = false, $FechaFinal = false, $FechaCobro = false){
		$xLog			= new cCoreLog();
		$fecha			= ($fecha == false) ? fechasys() : $fecha;
		$xEmp			= new cEmpresas_operaciones();
		$oficial		= ($oficial == DEFAULT_USER OR $oficial == false) ? getUsuarioActual() : $oficial;
		$FechaFinal		= ($FechaFinal == false) ? $fecha : $FechaFinal;
		$FechaCobro		= ($FechaCobro == false) ? $FechaFinal : $FechaCobro;
		$FechaInicial	= ($FechaInicial == false) ? $fecha : $FechaInicial;
		$lastID			= $xEmp->query()->getLastID();
		$xEmp->clave_de_empresa( $this->mClave );
		$xEmp->fecha_de_operacion($fecha);
		$xEmp->idempresas_operaciones( $lastID );
		$xEmp->monto($monto);
		$xEmp->oficial($oficial);
		$xEmp->tipo_de_operacion($tipo);
		$xEmp->periodo_marcado($periodo);
		$xEmp->periocidad($periocidad);
		$xEmp->observaciones($observaciones);
		$xEmp->fecha_de_cobro($FechaCobro);
		$xEmp->fecha_final($FechaFinal);
		$xEmp->fecha_inicial($FechaInicial);
		if($monto != 0){
			if($this->mClearPeriodo == true){ $this->setEliminarPeriodo($periodo, $periocidad, $FechaInicial);	}
			$rs		= $xEmp->query()->insert()->save();
			if($rs === false){
				
				$xLog->guardar($xLog->OCat()->NOMINA_NO_GUARDADA);
			} else {
				$xLog->add("OK\tSe Agrega el Periodo $periodo de la Empresa " . $this->mClave .  " con Fecha Inicial $FechaInicial de la frecuencia $periocidad\r\n");
				$xLog->guardar($xLog->OCat()->NOMINA_GUARDADA);				
			}
		}
		return $lastID;
	}
	function setEliminarPeriodo($periodo, $periocidad, $fecha = false){
		
		$xLog				= new cCoreLog();
		$periodo			= setNoMenorQueCero($periodo);
		$periocidad			= setNoMenorQueCero($periocidad);
		$xF					= new cFecha(0, $fecha);
		$anno				= $xF->anno();
		$xQL				= new MQL();
		$this->mMessages	.= "WARN\tEliminar Periodo $periodo con periocidad $periocidad\r\n";
		$sqlID	= " SELECT `idempresas_operaciones` AS 'id' FROM `empresas_operaciones` 
				WHERE (`empresas_operaciones`.`clave_de_empresa` =" . $this->mClave . ") 
				AND (`empresas_operaciones`.`periodo_marcado` =$periodo)
				AND (`empresas_operaciones`.`periocidad` =$periocidad)
				AND (`empresas_operaciones`.`fecha_inicial` >= '$anno-01-01')
				AND `tipo_de_operacion` = 1 LIMIT 0,1	";
		//$id		= mifila($sqlID, "id");
		$rs		=  $xQL->getDataRecord($sqlID);
		foreach ($rs as $rw){
			$idperiodo	= $rw["id"];
			$xPer		= new cEmpresasCobranzaPeriodos($idperiodo);
			if($xPer->init() == true){
				$xLog->add("WARN\tSe Elimina el Periodo $idperiodo de la Empresa " . $this->mClave . " con frecuencia $periocidad Ejercicio $anno\r\n");
				$xPer->setEliminar();
				$xLog->guardar($xLog->OCat()->NOMINA_ELIMINADA);
			}
		}
	}
	function getMontoDelPeriodo($periodo, $periocidad){
		$this->mMontoEnv		= 0;
		$this->mOperacionesEnv	= 0;
		$xF						= new cFecha();
		$anno					= $xF->anno();
		$sql	= "SELECT
			`empresas_operaciones`.`clave_de_empresa`,
			COUNT(`empresas_operaciones`.`idempresas_operaciones`) AS `operaciones`,
			SUM(`empresas_operaciones`.`monto` * `empresas_operaciones`.`tipo_de_operacion`)                    AS `saldo`,
			`empresas_operaciones`.`periodo_marcado` 
		FROM
			`empresas_operaciones` `empresas_operaciones`
		WHERE
			`empresas_operaciones`.`clave_de_empresa` = " . $this->mClave . "
			AND `empresas_operaciones`.`periodo_marcado` = $periodo
			AND `empresas_operaciones`.`periocidad` = $periocidad
			AND (`empresas_operaciones`.`fecha_de_operacion` >= '$anno-01-01')
		GROUP BY
			`empresas_operaciones`.`clave_de_empresa`,
			`empresas_operaciones`.`periodo_marcado` ";
		$D		= obten_filas($sql);
		if( isset($D["saldo"]) ){
			$this->mMontoEnv		= $D["saldo"];
			$this->mOperacionesEnv	= $D["operaciones"];
		}
		return $this->mMontoEnv;
	}
	function getOperacionesDePeriodo(){ return $this->mOperacionesEnv; }
	function getPeriodo(){ 	return	$this->mPeriodo;	}
	function getPeriocidadPref(){ return $this->mPeriocidadPref; }
	function getProductoPref(){ return $this->mProductoPref; }
	function setPeriodo($periodo){
		$fecha		= fechasys();
		$cEmp		= new cSocios_aeconomica_dependencias();
		$periodo	= setNoMenorQueCero($periodo);
		my_query("UPDATE socios_aeconomica_dependencias SET ultimo_periodo_enviado=$periodo, fecha_de_envio='$fecha' WHERE idsocios_aeconomica_dependencias=". $this->mClave);
	}
	function getFicha(){
		$cEmp	= new cSocios_aeconomica_dependencias();
		$cEmp->idsocios_aeconomica_dependencias($this->mClave);
		$cEmp->setData( $cEmp->query()->getRow("idsocios_aeconomica_dependencias=" . $this->mClave) );
		$this->mPeriodo		= $cEmp->periocidad_de_avisos()->get();
		
		return "<table>
			<tr>
				<th>Clave</th>
				<td>"  . $this->mClave . "</td>
				<th>Nombre</th>
				<td>" . $cEmp->descripcion_dependencia()->v() . "</td>
			</tr>
			<tr>
				<th>Domicilio</th>
				<td>"  . $cEmp->domicilio_completo()->v() . "</td>
				<th>Telefono</th>
				<td>" . $cEmp->telefono()->v() . "</td>
			</tr>
		</table>";
		
	}
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function getEsPeriodoCerrado($periocidad, $periodo, $claveperiodo = false){
		$result			= false;
		$periocidad		= setNoMenorQueCero($periocidad);
		$periodo		= setNoMenorQueCero($periodo);
		$claveperiodo	= setNoMenorQueCero($claveperiodo);
		
		$xF				= new cFecha();
		$anno			= $xF->anno();
		$filtro			= "				(`empresas_operaciones`.`clave_de_empresa` =" . $this->mClave . ") AND
				(`empresas_operaciones`.`periocidad` =$periocidad) AND
				(`empresas_operaciones`.`periodo_marcado` =$periodo)
				AND (`fecha_de_operacion` >= '$anno-01-01' ) ";
		if($claveperiodo > 0){
			$filtro			= " (`empresas_operaciones`.`idempresas_operaciones` = $claveperiodo) ";
		}
		$sql			= "SELECT `empresas_operaciones`.* 
			FROM
				`empresas_operaciones` `empresas_operaciones` 
			WHERE
					$filtro
				LIMIT 0,1 
		";
		$datos		= obten_filas($sql);
		if(isset($datos["monto"])){
			$result		= true;
		}
		return $result;
	}
	function getEmailsDeEnvio(){
		$mails		= explode(",", $this->mMails);
		$mails[]	= NOMINA_MAIL;
		
		if( setNoMenorQueCero($this->mOficial) > DEFAULT_SOCIO  ){
			//Mail del oficial
			$xOf		=  new cOficial($this->mOficial); $xOf->init();
			$mails[]	= $xOf->getEmail();
		}
		return $mails;
	}
	function getFechaDeAviso($periocidad = false, $fecha = false, $periodoInit = false, $periodoEnd = false){
		$xF			= new cFecha();
		$dias		= explode(",", strtoupper($this->mDiasDeAviso));
		$diasSem	= $xF->getDiasDeSemanaInArray();
		$periocidad	= ($periocidad == false) ? $this->getPeriocidadPref() : $periocidad;
		$fecha		= ($fecha == false) ? fechasys() : $fecha;
		$result		= null;
		$items		= 1;
		
		foreach ($diasSem as $numero => $nombre){
				/*if(isset($dias[$nombre]) ){
					unset($dias[$nombre]);
					$dias[$numero]
				}*/
		}
		foreach ($dias as $id => $dia){
			
		}
		foreach ($dias as $id => $dia){
			//limpiar
			if($periocidad != CREDITO_TIPO_PERIOCIDAD_SEMANAL){
				foreach ($diasSem as $numero => $nombre){
					if($dia == $nombre){
						unset($dias[$id]);
					}
				}
				if(setNoMenorQueCero($dia) > $xF->getDiasDelMes() OR setNoMenorQueCero($dia) == 0 ){
					unset($dias[$id]);
				}
				switch ($periocidad){
					case CREDITO_TIPO_PERIOCIDAD_DECENAL:
						if($id > 3){ unset($dias[$id]); 	}
						break;
					case CREDITO_TIPO_PERIOCIDAD_CATORCENAL:
						if($id > 2){ unset($dias[$id]); 	}
						break;
					case CREDITO_TIPO_PERIOCIDAD_QUINCENAL:
						if($id > 2){ unset($dias[$id]); 	}	
						break;
					case CREDITO_TIPO_PERIOCIDAD_MENSUAL:
						if($id > 1){ unset($dias[$id]); 	}	
						break;
							
				}
			} else {
				//convertir dias
				foreach ($diasSem as $numero => $nombre){
					if($dia == $nombre){
						$dias[$id]	= $numero;
					}
				}
				if(setNoMenorQueCero($dia) > 7 OR setNoMenorQueCero($dia) == 0 ){
					unset($dias[$id]);
				}				
				if($id > 1){ unset($dias[$id]); 	}
			}
		}
		//$items		= count($dias);
		//encontrar el periodo cero
		switch ($periocidad){
			case CREDITO_TIPO_PERIOCIDAD_DECENAL:
					
				break;
			case CREDITO_TIPO_PERIOCIDAD_CATORCENAL:
					
				break;
			case CREDITO_TIPO_PERIOCIDAD_QUINCENAL:
					
				break;
			case CREDITO_TIPO_PERIOCIDAD_MENSUAL:
				$xF1			= new cFecha(0, $fecha);
				$result			= $xF1->anno() . "-" . $xF1->mes() . "-" . $dias[0];
				break;
			case CREDITO_TIPO_PERIOCIDAD_SEMANAL:
				//$fechaInicial	= $xF->setFechaPorSemana();
				$xF1			= new cFecha(0, $fecha);
				$buscar			= 1;
				foreach($dias as $id => $cnt){ $buscar = $cnt; }
				$semana			= date("W", $xF1->getInt());
				$result			= $xF1->setFechaPorSemana($semana, $buscar);
				/*
				 $first_day_of_week = date('m-d-Y', strtotime('Last Monday', time()));
				$last_day_of_week = date('m-d-Y', strtotime('Next Sunday', time()));
				*/
				break;
		}
		return $result;
	}
	function getListadoDeCobranza($empresa, $periocidad, $variacion, $periodo, $fechaInicial = false, $fechaFinal = false, $uso = false){
		$xQL			= new MQL();
		$xEmp			= new cEmpresas($empresa);
		$xF				= new cFecha();
		$xLng			= new cLang();
		$idioma			= array(
				"plan" => $xLng->getT("TR.PLAN_DE_PAGOS"),
				"nuevoplan" => $xLng->getT("TR.Agregar PLAN_DE_PAGOS"),
				"quitar" => $xLng->getT("TR.Desvincular"),
				"estado" => $xLng->getT("TR.ESTADO_DE_CUENTA"),
				"historial" => $xLng->getT("TR.HISTORIAL"),
				"editar" => $xLng->getT("TR.EDITAR")
		);
    	$ByPeriodo		= ($periocidad == "todos") ? "" : " AND creditos_solicitud.periocidad_de_pago = $periocidad ";
    	
    	$fechaFinal		= $xF->getFechaISO($fechaFinal);
    	$fechaInicial	= $xF->getFechaISO($fechaInicial);

    	$creditoON		= array();
    	$DDias			= $xEmp->getDiasDeAviso($periocidad);
    	$sqletras		= "SELECT	`creditos_solicitud`.`persona_asociada`, `letras`.* 
						FROM	`creditos_solicitud` `creditos_solicitud`		INNER JOIN `letras` `letras`	ON `creditos_solicitud`.`numero_solicitud` = `letras`.`docto_afectado` 
    					WHERE	(`creditos_solicitud`.`persona_asociada` =$empresa) ";
    //setLog($sqletras);
    $rsCal			= $xQL->getDataRecord($sqletras);
    $DCal			= array();
    foreach ($rsCal as $dscal ){
    	$ixcredito						= $dscal["docto_afectado"];
    	$ixperiodo						= $dscal["periodo_socio"];
    	$DCal["$ixcredito-$ixperiodo"]	= $dscal["total_sin_otros"];
    }
        
    $ByMinistracion	= "";
    
    $periodo		= $periodo + $variacion;
    
    //filtrar domicilio -> socio -> credito -> letra
    $sql	= "SELECT
    creditos_solicitud.numero_socio AS 'persona',
    CONCAT(
    (CASE WHEN (socios_general.dependencia != creditos_solicitud.persona_asociada) THEN '(*)' ELSE '' END ), 
    socios_general.nombrecompleto, ' ',
    socios_general.apellidopaterno, ' ',
    socios_general.apellidomaterno 
	) AS 'nombre', 

    creditos_solicitud.numero_solicitud AS 'credito',
    `ultimo_periodo_afectado` AS `periodo_actual`,
	getParcialidadPorFecha(`ultimo_periodo_afectado`, $variacion, '$fechaFinal', `primeras_letras`.`fecha_de_pago`, fecha_ministracion) AS 'letra',
	getUltimaLetraEnviada(creditos_solicitud.numero_solicitud) AS `ultimo_envio`,
   `creditos_solicitud`.`pagos_autorizados`  AS 'pagos',
   
    creditos_solicitud.monto_parcialidad AS 'monto'
    
	FROM
		`creditos_solicitud` `creditos_solicitud` 
			INNER JOIN `creditos_periocidadpagos` `creditos_periocidadpagos` 
			ON `creditos_solicitud`.`periocidad_de_pago` = 
			`creditos_periocidadpagos`.`idcreditos_periocidadpagos` 
				INNER JOIN `primeras_letras` `primeras_letras` 
				ON `creditos_solicitud`.`numero_solicitud` = `primeras_letras`.
				`docto_afectado` 
					INNER JOIN `socios_general` `socios_general` 
					ON `creditos_solicitud`.`numero_socio` = `socios_general`.
					`codigo`
    
    WHERE 
    `creditos_solicitud`.persona_asociada	= $empresa
    AND saldo_actual > " . TOLERANCIA_SALDOS . "
    AND (`primeras_letras`.`fecha_de_pago` <= '$fechaFinal'  OR creditos_solicitud.ultimo_periodo_afectado >= 2)
    $ByPeriodo
    AND (creditos_solicitud.ultimo_periodo_afectado+(1+($variacion))) > 0
    AND (creditos_solicitud.ultimo_periodo_afectado+(1+($variacion))) <= creditos_solicitud.pagos_autorizados
    ORDER BY ultimo_periodo_afectado DESC, fecha_ministracion,
	socios_general.nombrecompleto
    ";
    //setLog($sql);
    $dato		= $xQL->getDataRecord($sql);
    $tr			= "";
    //$xBt		= new cHImg();
    $xBtn		= new cHButton();
    $xNot		= new cHNotif();
    $ixx		= 1;
    $numero		= 0;
    $suma		= 0;
    $xT			= new cTipos();
    foreach ($dato as $rw){
    	
    	$credito				= $rw["credito"];
    	$creditoON[$credito]	= $credito;
    	$npagos					= $rw["pagos"];
    	$letraCalc				= $rw["letra"];
    	$letraAct				= $rw["periodo_actual"];
    	$ultEnvio				= $rw["ultimo_envio"];
    	$proxLetra				= $ultEnvio+1;
    	$letra					= $proxLetra + $variacion;
    	$DPendientes			= $xQL->getDataRow("SELECT getLetrasPorPagarNomina($credito, $letra) AS 'pendientes'");
    	$pendientes				= $DPendientes["pendientes"];
    	$lmonto					= $xT->cFloat($rw["monto"],2);
    	$suma					+= $lmonto;
		$cssN					= "";
    	$notas					= "";
    	$cls					= ($ixx == 2) ? "trOdd" : "";
    	$dif					= 0;
    	$agregar				= true;
    	$numero++;
    	if($letra > $npagos){
    		$cls			= $xNot->ERROR;
    		$notas			.= "Los pagos Actuales $letra No pueden ser Mayor a $npagos";
    		$letra			= $npagos;
    	}    	
    	if( isset($DCal["$credito-$letra"]) ){
    		$dlmonto	= round($DCal["$credito-$letra"], 2);
    		$dif		= round(($dlmonto - $lmonto), 2);
    		if($dif != 0 ){
    			if($dif > 0.01 OR $dif < -0.01){
    				$notas			= "Parcialidad $letra Ajustada, Plan de Pagos : " . $dlmonto  .  " Monto de Parcialidad : " . $lmonto;
    				$cls			= $xNot->NOTICE;
    				$lmonto			= $dlmonto;
    			} else {
    				//+01 -01
    				$lmonto			= round($dlmonto,2);
    				$cls			= $xNot->NOTICE;
    			}
    			
    		}
    	}

    	if($proxLetra <= 1){
    		$cls			= $xNot->SUCCESS;
    		$notas			.= "Posible Nuevo Envio, Ultimo envio : $ultEnvio; Pago Actual : $letraAct  ";
    	}
    	if($letraAct <= 0 AND $proxLetra >1 ){
    		$cls			= $xNot->SUCCESS;
    		$notas			.= "Los Pagos Aplicados son 0  ";
    	}
    	
    	if($letra == $npagos){
    		$xParc		= new cCreditosLetraDePago($credito, $letra);
    		if($xParc->init() == true){
    			if($xParc->getTotal() !== $dlmonto){
    				$notas			.= " - Pago Ajustado a la Ultima Letra($letra/$npagos) de un monto $lmonto a " . $xParc->getTotal();
    				$lmonto			= $xParc->getTotal();
    				$cls			= $xNot->NOTICE;
    			}
    		}
    	}
    	//Verificar si ya esta enviado en otro periodo
		$xDet	= new cEmpresasCobranzaDetalle();
		if($xDet->getExisteEnOtraNomina($letra, $credito, $empresa, $periodo, $periocidad) > 0){
			$cls			= $xNot->WARNING;
			$notas			.= "- La letra se han enviado en un periodo Anterior";			
		}
    	$xBtn->setClearEvents(); $xBtn->setClearHTML(); $xBtn->setClearProperties();
    	$mark	= ($cls == "") ? "" : " class='$cls' "; 
    	$td		= "<tr$mark id='tr-$credito'>";
    	$td		.= "<th>" . $rw["persona"] . "</th>";
    	
    	
    	$td		.= "<td>" . $rw["nombre"] . "</td>";
    	$td		.= "<td>$credito</td>";
    	
    	$td		.= "<td style='width:50px;'><input type=\"number\" id=\"periodo-$credito\" value=\"" . $letra . "\" max=\"$npagos\" onchange=\"if(this.value>$npagos){alert('No debe ser  mayor a $npagos');this.value=this.max;}\" /></td>";
    	$td		.= "<th>" . $npagos . "</th>";
    	$td		.= "<td style='width:120px;'><input type=\"number\" id=\"monto-$credito\" value=\"" . $lmonto . "\" step=\"0.01\" /></td>";
    	
    	$td		.= "<td><input type=\"text\" id=\"notas-$credito\" value=\"" . "" . "\"/></td>";
    	
    	$td		.= "<td class='toolbar-24' id=\"options-$credito\">";
    	if($agregar == true){
    		$td		.= "<div class='coolCheck'><input type='checkbox' id='chk$credito' onclick='jsSetAlimentarEnvio(this, $credito)' /><label for='chk$credito'></label></div>";
    	}
    	$td		.= "<ul class=\"tags green\"><li>";
    	$td		.= $xBtn->getBasic($idioma["historial"], "var xC=new CredGen();xC.getHistorialNomina($credito)" , $xBtn->ic()->REPORTE2, "cmd-hist-$credito" ,false, true);
    	$td		.= "</li><li>";
    	
    	$td		.= $xBtn->getBasic($idioma["plan"], "getPlanDePagos($credito)" , $xBtn->ic()->CALENDARIO, "cmd-plan-$credito" ,false, true);
    	$td		.= "</li><li>";
    	$td		.= $xBtn->getBasic($idioma["estado"], "getEstadoDeCuenta($credito)",  $xBtn->ic()->REPORTE, "cmd-ecta-$credito",false, true);
    	$td		.= "</li><li>";
    	$td		.= $xBtn->getBasic($idioma["nuevoplan"], "generarPlanDePagos($credito)" , $xBtn->ic()->AGREGAR, "cmd-addplan-$credito" ,false, true);
    	$td		.= "</li><li>";
    	$td		.= $xBtn->getBasic($idioma["quitar"], "desvincular($credito)", $xBtn->ic()->ELIMINAR, "cmd-des-$credito",false, true);
    	$td		.= "</li>";

    	$td		.= "<li>";
    	$td		.= $xBtn->getBasic($idioma["editar"], "jsEditarEnvioPorCredito($credito)", $xBtn->ic()->EDITAR, "cmd-edit-$credito",false, true);
    	$td		.= "</li>";
    	if($dif == 0){
    		//$td		.= "<li>";
    		//$td		.= $xBtn->getBasic("TR.VER", "setOcultar($credito)", $xBtn->ic()->VER, "cmd-ver-$credito", false, true);
    		//$td		.= "</li>";
    	}
    	$td		.= "</ul>";
    	if($pendientes >0){
    		$td		.= "" . $xNot->getNoticon($pendientes, "", $xBtn->ic()->REPORTE2) . "";
    	}    	
    	$td		.= "</td>";
    	
    	$td		.= "</tr>";
    	
    	$ixx 	= ($ixx >= 2) ? 1 : $ixx + 1;
    	if($notas != ""){
    		$notas	= $xNot->get($notas, "msg-$credito",  $cls, "xG.empty(\"#w-$credito\")"); 
    		$td	.= "<tr id='w-$credito'><td colspan='8'>$notas</td></tr>";
    	}
    	$tr		.= $td;
    }
    
    //filtrar domicilio -> socio -> credito -> letra
    $sql	= "SELECT
    creditos_solicitud.numero_socio AS 'persona',
    CONCAT(
    (CASE WHEN (socios_general.dependencia != creditos_solicitud.persona_asociada) THEN '(*)' ELSE '' END ),
    socios_general.nombrecompleto, ' ',
    socios_general.apellidopaterno, ' ',
    socios_general.apellidomaterno
    ) AS 'nombre',
    
    creditos_solicitud.numero_solicitud AS 'credito',
    
    getParcialidadPorFecha(ultimo_periodo_afectado, $variacion, '$fechaFinal', `primeras_letras`.`fecha_de_pago`, fecha_ministracion) AS 'letra',
    `creditos_solicitud`.`pagos_autorizados`  AS 'pagos',
     
    creditos_solicitud.monto_parcialidad AS 'monto'
    FROM
    `creditos_solicitud` `creditos_solicitud`
    INNER JOIN `creditos_periocidadpagos` `creditos_periocidadpagos`
    ON `creditos_solicitud`.`periocidad_de_pago` =
    `creditos_periocidadpagos`.`idcreditos_periocidadpagos`
    INNER JOIN `primeras_letras` `primeras_letras`
    ON `creditos_solicitud`.`numero_solicitud` = `primeras_letras`.
    `docto_afectado`
    INNER JOIN `socios_general` `socios_general`
    ON `creditos_solicitud`.`numero_socio` = `socios_general`.
    `codigo`
    
    WHERE `creditos_solicitud`.persona_asociada	= $empresa
    
    ORDER BY ultimo_periodo_afectado DESC, fecha_ministracion,
    socios_general.nombrecompleto
    ";
    //setLog($sql);
    $dato		= $xQL->getDataRecord($sql);
    $xT			= new cTipos();
    foreach ($dato as $rw){
    	$credito	= $rw["credito"];
	    if( !isset($creditoON[$credito]) ){
		    $credito	= $rw["credito"];
		    $letra		= $rw["letra"];
		    $lmonto		= $xT->cFloat($rw["monto"],2);
		    
		    $cssN		= "";
		    $notas		= "";
		    
		   
		    $cls	= ($ixx == 2) ? " class='trOdd' " : "";
		  /*  $td		= "<tr$cls id='tr-$credito'>";
		    $td		.= "<th>" . $rw["persona"] . "</th>";
		    $td		.= "<td>" . $rw["nombre"] . "</td>";
		    $td		.= "<td>" . $rw["credito"] ."</td>";
	   		$td		.= "<td" . $rw["letra"] . "</td>";
	   		$td		.= "<th>" . $rw["pagos"] . "</th>";
	   		$td		.= "<td>" . $lmonto . "</td>";
	   				 
	   		$td		.= "<td>" . $notas . "</td>";
	   		 
	   		$td		.= "<td class='toolbar-24' id=\"options-$credito\">";
	   
	   		$td		.= "</td>";
	   		 
	   		$td		.= "</tr>";
 
	   		$tr		.= $td;*/
	    }    
    }
    //$xT			= new cHTabla();
    $tfoot		= "<tr><th colspan='3'>TOTALES</td><th><input id=\"idconteo\" value=\"0\" type=\"number\"></th>";
    $tfoot		.= "<td /><th><input id=\"idsuma\" value=\"0\" type=\"number\"></th><td colspan='2'><input type='text' id='idenletras' disabled='true' /></td></tr>";
    $ttcaption	= "<caption>Periodo de Trabajo : $periodo</caption>";
    $thead		= "<thead><tr><th>" . $xLng->getT("TR.CLAVE_DE_PERSONA") . "</th><th>" . $xLng->getT("TR.NOMBRE") . "</th>
    		<th>" . $xLng->getT("TR.CLAVE_DE_CREDITO") . "</th>
			<th colspan='2'>" . $xLng->getT("TR.PERIODO") . "</th>
			<th>" . $xLng->getT("TR.MONTO") . "</th>
			<th>" . $xLng->getT("TR.OBSERVACIONES") . "</th>
			<th>" . $xLng->getT("TR.HERRAMIENTAS") . "</th>
    		</tr><thead>";

    return "<table class='listado'>$ttcaption $thead $tr $tfoot</table>";
	}
	function getTasaComision(){ return $this->mTasaComision; }
	function getOPeriodo($periocidad = false, $periodo = false, $clave = false, $fecha = false){
		$periocidad	= ($periocidad == false) ? $this->getPeriocidadPref() : $periocidad;
		$periodo	= ($periodo == false) ? $this->getPeriodo() : $periodo;
		$xPer		= new cPeriodoDeEmpresa();
		$empresa	= $this->getClaveDeEmpresa();
		$xF			= new cFecha(0, $fecha);
		$anno		= $xF->anno($fecha);
		if($clave == false){
		$sql	= "SELECT	* FROM `empresas_operaciones` WHERE
			(`empresas_operaciones`.`clave_de_empresa` =$empresa) AND
			(`empresas_operaciones`.`periodo_marcado` =$periodo) AND
			(`empresas_operaciones`.`tipo_de_operacion` =1) AND
			(`empresas_operaciones`.`periocidad` = $periocidad)
			AND (`fecha_inicial` >= '$anno-01-01' )
		LIMIT 0,1 ";
		//setLog($sql);
			$dat	= obten_filas($sql);
		} else {
			$dat	= $xPer->query()->initByID($clave);
		}
		$xPer->setData($dat);
		$xPer->fecha_de_cobro( setFechaValida($xPer->fecha_de_cobro()->v()));
		$xPer->fecha_inicial( setFechaValida($xPer->fecha_inicial()->v()) );
		$xPer->fecha_final( setFechaValida($xPer->fecha_final()->v()) );
		
		
		return $xPer;
	}
	function getIDDeFormatoDeAviso(){ return $this->mFormatoDeAviso; }
	function isInit(){ return $this->mInit; }
	function initPorPersona($persona){
		$persona	= setNoMenorQueCero($persona);
		if($persona > DEFAULT_SOCIO){
			$xQL	= new MQL();
			$datos	= $xQL->getDataRow("SELECT * FROM `socios_aeconomica_dependencias` WHERE `clave_de_persona`= " . $persona . " LIMIT 0,1");
			if(isset($datos["idsocios_aeconomica_dependencias"])){
				$this->init($datos);
			}
		}
		return $this->isInit();
	}
	function setActualizarPorPersona(){
		$res		= false;
		$OPersona	= $this->getOPersona();
		if($OPersona != null){
			if($this->mInit == false){ $this->init(); }
			$ODom		= $OPersona->getODomicilio();
			$this->mOB->descripcion_dependencia($OPersona->getNombreCompleto());
			//$this->mOB->nombre_corto( $OPersona->getAlias() );
			if($ODom != null){
				
				$this->mOB->domicilio_completo( $ODom->getDireccionBasica() );
				$this->mOB->telefono( $OPersona->getTelefonoPrincipal() );
				$rs		= $this->mOB->query()->update()->save($this->mOB->idsocios_aeconomica_dependencias()->v());
				//$this->mMessages	.= $ODOm->get
				if($rs == false){
					
				} else {
					$res		= true;
					$this->setCleanCache();
				}
			}
			$this->mMessages	.= $OPersona->getMessages();
		}
		return $res;
	}
	function getDiasDePago($periocidad, $fmt = false){ 
		$cnt	= $this->extractDias($this->mDiasDePago, $periocidad);
		return ($fmt == MQL_STRING) ? implode(",", $cnt) : $cnt;
	}
	function getDiasDeNomina($periocidad, $fmt = false){ 
		$cnt	= $this->extractDias($this->mDiasDeNomina, $periocidad);
		return ($fmt == MQL_STRING) ? implode(",", $cnt) : $cnt;
	}
	function getDiasDeAviso($periocidad, $fmt = false){ 
		$cnt	= $this->extractDias($this->mDiasDeAviso, $periocidad);
		return ($fmt == MQL_STRING) ? implode(",", $cnt) : $cnt; 
	}
	
	private function extractDias($valor, $periocidad = false){
		$content	= array();
		$periocidad	= setNoMenorQueCero($periocidad);
		$pers		= explode("|", $valor);
		
		foreach ($pers as $idxPer => $cntPer){
			$DPer			= explode("=", $cntPer);
			$xperiocidad	= isset($DPer[0]) ? $DPer[0] : null;
			$xperiocidad	= (setNoMenorQueCero($xperiocidad) <= 0) ? null : $xperiocidad;
			if($xperiocidad != null){
				$xdias		= null;
				if( isset($DPer[1])  ){
					$xdias		= trim($DPer[1]) == "" ?  null : explode(",", $DPer[1]);
				}
				if($xdias != null){ $content[$xperiocidad]	= $xdias; $this->mListaDePeriocidad[] = $xperiocidad; }
			}
		}
		if($periocidad > 0){
			$content		= isset($content[$periocidad]) ? $content[$periocidad] : array();
		}
		return $content;
	}
	function getConjugarPeriodo($periocidad, $arrDias){
		$strDias	= "";
		if(is_array($arrDias)){
			foreach ($arrDias as $idx => $dias){
				$strDias	.= "$dias,";
			}
		} else {
			$arrDias		= str_replace(" ", "", $arrDias);
			$arrDias		= str_replace("Y", ",", $arrDias);
			$arrDias		= str_replace("y", ",", $arrDias);
			if($periocidad != CREDITO_TIPO_PERIOCIDAD_SEMANAL AND $periocidad != CREDITO_TIPO_PERIOCIDAD_CATORCENAL){
				$arrDias	= preg_replace("/[^0-9,.]/", "", $arrDias);
			}
			$strDias		= $arrDias;
		}
		if(setNoMenorQueCero($periocidad) <= 0 OR trim($strDias) == ""){
			$strDias		= "";
		} else {
			$strDias		= "$periocidad=$strDias|";
		}
		return $strDias;
	}
	function getListaDePeriocidad(){
		$str	= $this->extractDias($this->mDiasDeNomina);
		return $this->mListaDePeriocidad;
	}
}

class cPersonasRelaciones {
	private $mID					= false;
	private $mNombreRelacion		= "";
	private $mNombreConsan			= "";
	private $mInit					= false;
	private $mPorcientoRel			= 0;
	private $mMontoRel				= 0;
	private $mPersona				= false;
	private $mPersonaRelacionada 	= false;
	private $mMessages				= "";
	private $mNombreDelRelacionado	= "";
	private $mClaveDePersona		= null;
	public $CONSANGUINIDAD_NINGUNA	= 99;
	public $ESTADO_ACTIVO			= 10;
	private $mTelefonoFijo			= 0;
	private $mTelefonoMovil			= 0;
	private $mDireccion				= "";
	private $mNombres				= "";
	private $mApellidoMaterno		= "";
	private $mApellidoPaterno		= "";
	private $mFechaDeNacimiento		= false;
	
	function __construct($id , $persona){
		$xF							= new cFecha();
		$this->mFechaDeNacimiento	= $xF->get(); 
		$this->mID					= setNoMenorQueCero($id); $this->mPersona = $persona;
	}
	function init($data = false){
		if(is_array($data)){
			$datos	= $data;
		} else {
			$xLi	= new cSQLListas();
			$sql 	= $xLi->getInicialPersonasRelaciones($this->mID);
			$datos	= obten_filas($sql);
		}
		if(isset($datos["idsocios_relaciones"])){
			$xRel							= new cSocios_relaciones();
			$xRel->setData($datos);
			$this->mNombreConsan			= $datos["nombre_consanguinidad"];
			$this->mNombreRelacion			= $datos["nombre_relacion"];
			$this->mMontoRel				= $datos["monto_relacionado"];
			$this->mPorcientoRel			= $datos["porcentaje_relacionado"];
			$this->mClaveDePersona			= $datos["numero_socio"];
			$this->mTelefonoFijo			= $xRel->telefono_residencia()->v();
			$this->mTelefonoMovil			= $xRel->telefono_movil()->v();
			$this->mDireccion				= $xRel->domicilio_completo()->v();
			$this->mNombres					= $xRel->nombres()->v();
			$this->mApellidoMaterno			= $xRel->apellido_materno()->v();
			$this->mApellidoPaterno			= $xRel->apellido_paterno()->v();
			$this->mFechaDeNacimiento		= $xRel->fecha_nacimiento()->v();
			$this->mNombreDelRelacionado	= trim($xRel->nombres()->v() . " " . $xRel->apellido_paterno()->v() . " " . $xRel->apellido_materno() ->v());
			$this->mInit		= true;
		}
		return $this->mInit;
	}
	function setTelefonoFijo($telefono){ $this->mTelefonoFijo = $telefono; }
	function setTelefonoMovil($telefono){ $this->mTelefonoMovil = $telefono; }
		
	function getTelefonoFijo(){ return $this->mTelefonoFijo; }
	function getTelefonoMovil(){ return $this->mTelefonoMovil; }
	
	function getNombreRelacion(){ return $this->mNombreRelacion;	}
	function getNombreParentesco(){ return $this->mNombreConsan;	}
	function getPorcientorelacionado(){ return $this->mPorcientoRel; }
	function getMontoRelacionado(){ return $this->mMontoRel; }
	function getNombreDelRelacionado(){ return $this->mNombreDelRelacionado; }
	//function addRelacion(){
	function getCodigoDePersona(){ return $this->mClaveDePersona; }
	function getDomicilio(){return $this->mDireccion; }
	
	function addRelacion($numero_de_socio = FALLBACK_CLAVE_DE_PERSONA, $tipo_de_relacion = DEFAULT_TIPO_RELACION, $consanguinidad = DEFAULT_TIPO_CONSANGUINIDAD,
			$depende = 0, $observaciones = "", $monto_relacionado = 0, $porcentaje_relacionado = 1, $fecha_de_alta = false, $documento = false){
		$xSocRel			= new cSocio($numero_de_socio);
		$xT					= new cTipos();
		$xF					= new cFecha();
		$fecha_de_alta		= $xF->getFechaISO($fecha_de_alta);
		$documento			= setNoMenorQueCero($documento);
		$documento 			= ($documento <= 0) ? DEFAULT_CREDITO : $documento;
		$nombres 			= $this->mNombres;
		$apellido_paterno	= $this->mApellidoPaterno;
		$apellido_materno 	= $this->mApellidoMaterno;
		$domicilio 			= $this->mDireccion;
		$ocupacion			= "";
		$curp				= "";
		$fecha_de_nacimiento= $this->mFechaDeNacimiento;
		if($xSocRel->init() == true){
			
			$nombres 			= $xSocRel->getNombre();
			$apellido_paterno	= $xSocRel->getApellidoPaterno();
			$apellido_materno 	= $xSocRel->getApellidoMaterno();
			$domicilio 			= $xSocRel->getDomicilio();
			
			if($xSocRel->getODomicilio() == null){
				$xRelDom			= "";
				$domicilio			= $this->getDomicilio();
				$telefono_fijo		= $this->getTelefonoFijo();
				$telefono_movil		= $this->getTelefonoMovil();
			} else {
				$DDom				= $xSocRel->getODomicilio();
				$domicilio 			= $DDom->getCalleConNumero();
				$telefono_fijo 		= $DDom->getTelefonos(false);
				$telefono_movil 	= $xSocRel->getTelefonoPrincipal();
			
			}
			
			$fecha_de_nacimiento = $xSocRel->getFechaDeNacimiento();
			$curp 				= $xSocRel->getCURP();
			$DOcup				= $xSocRel->getOActividadEconomica();
			if($DOcup !== null){
				$ocupacion		= $DOcup->getPuesto();
			}			
		}

		$monto_relacionado	= setNoMenorQueCero($monto_relacionado);
		$socio_relacionado	= $this->mPersona;
		$sucursal			= getSucursal();
		$fecha_de_alta		= ( $fecha_de_alta == false ) ? fechasys() : $fecha_de_alta;
		$iduser				= getUsuarioActual();
		$depende			= $xT->cBool($depende);
		$depende			= ($depende == true) ? "1" : "0";		

		$this->mClaveDePersona	= $numero_de_socio;
		$xRel				= new cSocios_relaciones();
		$xRel->apellido_materno($apellido_materno);
		$xRel->apellido_paterno($apellido_paterno);
		$xRel->nombres($nombres);
		$xRel->calificacion_del_referente(1);
		$xRel->consanguinidad($consanguinidad);
		$xRel->credito_relacionado($documento);
		$xRel->curp($curp);
		$xRel->dato_extra_1("");//
		$xRel->dato_extra_2("");
		$xRel->dato_extra_3("");
		$xRel->dependiente($depende);
		$xRel->domicilio_completo($domicilio);
		$xRel->eacp(EACP_CLAVE);
		$xRel->estatus($this->ESTADO_ACTIVO);
		$xRel->fecha_alta($fecha_de_alta);
		$xRel->fecha_nacimiento($fecha_de_nacimiento);
		$xRel->idusuario(getUsuarioActual());
		$xRel->monto_relacionado($monto_relacionado);
		$xRel->numero_socio($numero_de_socio);
		
		$xRel->observaciones($observaciones);
		$xRel->ocupacion($ocupacion);
		$xRel->porcentaje_relacionado($porcentaje_relacionado);
		$xRel->socio_relacionado($this->mPersona);
		$xRel->codigo($this->mPersona);
		$xRel->sucursal(getSucursal());
		$xRel->telefono_movil($telefono_movil);
		$xRel->telefono_residencia($telefono_fijo);
		$xRel->tipo_relacion($tipo_de_relacion);
		$id		= $xRel->query()->getLastID();
		$xRel->idsocios_relaciones($id);
		$success	= $xRel->query()->insert()->save();
		$this->mID	= ($success == false) ? $this->mID : $id;

		if($success == true){
			//agregar modificaciones de Grupo
			switch ($tipo_de_relacion){
				case GRUPO_CLAVE_INTEGRANTE:
					$xPer	= new cSocio($numero_de_socio);
					$xPer->setUpdate( array("grupo_solidario" => "$socio_relacionado") );
					break;
				case GRUPO_CLAVE_PRESIDENTA:
					$xGr	= new cGrupo($socio_relacionado);
					$xGr->setUpdate( array("representante_numerosocio" => "$numero_de_socio",
							"representante_nombrecompleto" => "$apellido_paterno $apellido_materno $nombres") );
					$xPer	= new cSocio($numero_de_socio);
					$xPer->setUpdate( array("grupo_solidario" => "$socio_relacionado") );
					break;
				case GRUPO_CLAVE_VOCAL:
					$xGr	= new cGrupo($socio_relacionado);
					$xGr->setUpdate( array("vocalvigilancia_numerosocio" => "$numero_de_socio",
							"vocalvigilancia_nombrecompleto" => "$apellido_paterno $apellido_materno $nombres") );
					$xPer	= new cSocio($numero_de_socio);
					$xPer->setUpdate( array("grupo_solidario" => "$socio_relacionado") );
					break;
			}
			//Agregar relación Inversa
		} else {
			//$this->mMessages	.= "ERROR\tError al guardar la relacion\r\n";
			$this->mMessages	.= "ERROR\tError al guardar la relacion de la Persona $socio_relacionado a la Persona $numero_de_socio\r\n";
		}
		return $success;
	}
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	//function getOApoderadoLegal(){  return $this->getORelacion(PERSONAS_REL_TUTOR_LEGAL);	}
	//function getORepresentanteLegal(){ return $this->getORelacion(PERSONAS_REL_REP_LEGAL);	}
	function initRelacionPorTipo($tipo, $initPersona = false){
		$xLi		= new cSQLListas();
		$sql 		= $xLi->getInicialPersonasRelaciones(false, $this->mPersona, $tipo);		
		$ql			= new MQL();
		$data		= $ql->getDataRow($sql);
		if(isset($data["idsocios_relaciones"])){
			if(setNoMenorQueCero($data["idsocios_relaciones"]) > 0){
				$this->mID	= $data["idsocios_relaciones"];
				return $this->init($data);
			}
		}
		return false;
	}
	function setActualizarPorPersona(){
		$idpersona		= $this->mClaveDePersona;
		$res			= false;
		if($idpersona != null){
			$xSoc		= new cSocio($idpersona);
			if($xSoc->init() == true){
				$xRel		= new cSocios_relaciones();
				if($this->mInit == true){
					$xRel->setData( $xRel->query()->initByID($this->mID) );
					$xRel->domicilio_completo( $xSoc->getDomicilio() );
					$xRel->apellido_materno( $xSoc->getApellidoMaterno() );
					$xRel->apellido_paterno( $xSoc->getApellidoPaterno() );
					$xRel->curp(  $xSoc->getCURP() );
					$xRel->fecha_nacimiento( $xSoc->getFechaDeNacimiento() );
					$xRel->nombres( $xSoc->getNombre() );
					$xRel->telefono_movil( $xSoc->getTelefonoPrincipal() );
					
					$id	= $xRel->query()->update()->save($this->mID);
					if($id == false){
						$this->mMessages	.= "ERROR\tId " . $this->mID . "\tAl Actualizar a la Persona $idpersona \r\n";
					} else {
						$res	= true;
					}
					//Actualizar PEP
				}
			}			
		} else {
			$this->mMessages	.= "ERROR\tAl cargar a la Persona $idpersona\r\n";
		}
		return $res;	
	}
	function initArbolRelaciones(){
		$QL		= new MQL();
		$id		= $this->mPersona;
		$msql	= "SELECT `personas_relaciones_recursivas`.*, 
		CONCAT(	LEFT(`socios_relaciones`.`nombres`,5), ' ',
	`socios_relaciones`.`apellido_paterno`, ' ',
	LEFT(`socios_relaciones`.`apellido_materno`,5) ) AS `nombres`
	  
		FROM 

		`personas_relaciones_recursivas` `personas_relaciones_recursivas` 
		LEFT OUTER JOIN `socios_relaciones` `socios_relaciones` 
		ON `personas_relaciones_recursivas`.`relacion` = `socios_relaciones`.
		`numero_socio` ";
		$sql	= "$msql WHERE	(`personas_relaciones_recursivas`.`persona` =$id)	ORDER BY	`personas_relaciones_recursivas`.`persona`,	`personas_relaciones_recursivas`.`nivel`";
		//setLog($sql);
		$rs		= $QL->getDataRecord($sql);
		if($QL->getNumberOfRows() <= 0){
			$sql	= "$msql WHERE	(`personas_relaciones_recursivas`.`proxy` =$id) ";
			$rs		= $QL->getDataRecord($sql);
		}
		if($QL->getNumberOfRows() <= 0){
			$sql	= "$msql WHERE	(`personas_relaciones_recursivas`.`relacion` =$id) ";
			$rs		= $QL->getDataRecord($sql);
		}		
		return $rs;
	}
	function getBuildArbol($out = null){
		$nodes		= "";
		$edges		= "";
		
		$id			= $this->mPersona;
		$ext		= array();
		$xPer		= new cSocio($id); $xPer->init();
		$nn			= $xPer->getNombreCompleto();
		$nodes		.= "{\"id\": \"$id\", \"label\": \"$id $nn\", \"x\": 0, \"y\": 0, \"size\": 10 }";
		$ext[$id]	= true;
		$cnt		= 1;
		$pos		= array();
		$pos["X"]	= array("2", "2", "-2", "-2", "3", "3", "-3", "-3", "4", "4", "-4", "-4", "5", "5", "-5", "-5", "6", "6", "-6", "-6", "7", "7", "-7", "-7");
		$pos["Y"]	= array("2", "-2", "-2", "2", "3", "-3", "-3", "3", "4", "-4", "-4", "4", "5", "-5", "-5", "5", "6", "-6", "-6", "6", "7", "-7", "-7", "7");
		$colors		= array(1 => "#BB1900", 2 => "#EB009E", 3 => "#0057A3", 4 => "#183444", 5=> "#535353", 6 => "#000000");
		$cnt			= 0;
		$rs				= $this->initArbolRelaciones();
		foreach ($rs as $rw){
			$relacion	= $rw["relacion"];
			$proxy		= $rw["proxy"];
			$nivel		= $rw["nivel"];
			$nombres	= $rw["nombres"];
			$e1			= "$proxy$relacion";
			$e2			= "$id$proxy";
			$e3			= "$id$relacion";
			$lvl		= 10 - $nivel;
			//Iniciar arbol
			if(isset($pos["Y"][$cnt])){
			$posY		= $pos["Y"][$cnt];
			$posX		= $pos["X"][$cnt];
			$mcolor 	= (isset($colors[$nivel])) ? $colors[$nivel] : '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);

			if(!isset($ext[$relacion])){
				$nodes			.= ",{\"id\": \"$relacion\", \"label\": \"L$nivel $nombres\", \"x\": $posX, \"y\": $posY, \"size\": $lvl, \"color\" :\"$mcolor\" }";
				$ext[$relacion]	= $proxy;
				$cnt++;
			}
			if(!isset($ext[$proxy])){
				$posX2			= $posX+1;
				$nodes			.= ",{\"id\": \"$proxy\", \"label\": \"L$nivel $nombres\", \"x\": $posX2, \"y\": $posY, \"size\": $lvl, \"color\" :\"$mcolor\" }";
				$ext[$proxy]	= $proxy;
				$cnt++;
			}
			if(!isset($ext[$e1])){
				$edges	.= ($edges == "") ? "" : ",";
				$edges		.= "{\"id\": \"E$e1\", \"source\": \"$proxy\", \"target\": \"$relacion\"}";
				$ext[$e1]	= true;
			}
			if(!isset($ext[$e2])){
				$edges		.= ",{\"id\": \"E$e2\", \"source\": \"$id\", \"target\": \"$proxy\", \"type\" : \"arrow\"}";
				$ext[$e2]	= true;
			}
			if(!isset($ext[$e3])){
				$edges		.= ",{\"id\": \"E$e3\", \"source\": \"$id\", \"target\": \"$relacion\"}";
				$ext[$e3]	= true;
			}
			}
		}

		return "{\"nodes\":[ $nodes ], \"edges\" : [$edges] }";
	}
	function addRelacionBancaria($banco, $limite_de_credito = 0,$tipo_de_cuenta ="", $fecha_de_emision = false, $numero_cuenta = "", $numero_tarjeta = ""){
		$xRel		= new cSocios_relaciones();
		$xF			= new cFecha();
		$fecha_de_emision	= $xF->getFechaISO($fecha_de_emision);
		$xBan		= new cBancos_entidades(); $xBan->setData( $xBan->query()->initByID($banco) );
		$datobanco	= $xBan->idbancos_entidades()->v() . "-" . $xBan->nombre_de_la_entidad()->v();
		$xRel->apellido_materno("");
		$xRel->apellido_paterno("");
		
		$datobanco	= substr($datobanco, 0,99);
				
		$xRel->nombres($datobanco);
		$xRel->calificacion_del_referente(1);
		$xRel->consanguinidad($this->CONSANGUINIDAD_NINGUNA);
		$xRel->credito_relacionado(DEFAULT_CREDITO);
		$xRel->curp("");
		$xRel->dato_extra_1($tipo_de_cuenta);//
		$xRel->dato_extra_2($numero_cuenta);
		$xRel->dato_extra_3($numero_tarjeta);
		$xRel->dependiente("0");
		$xRel->domicilio_completo("");
		$xRel->eacp(EACP_CLAVE);
		$xRel->estatus($this->ESTADO_ACTIVO);
		$xRel->fecha_alta(fechasys());
		$xRel->fecha_nacimiento($fecha_de_emision);
		$xRel->idusuario(getUsuarioActual());
		$xRel->monto_relacionado($limite_de_credito);
		$xRel->numero_socio(DEFAULT_SOCIO);
		
		$xRel->observaciones("");
		$xRel->ocupacion("");
		$xRel->porcentaje_relacionado(0);
		$xRel->socio_relacionado($this->mPersona);
		$xRel->codigo($this->mPersona);
		$xRel->sucursal(getSucursal());
		$xRel->telefono_movil("");
		$xRel->telefono_residencia("");
		$xRel->tipo_relacion(PERSONAS_REL_REF_BANCARIA);
		$id		= $xRel->query()->getLastID();
		$xRel->idsocios_relaciones($id);
		$res	= $xRel->query()->insert()->save();
		$this->mID	= ($res == false) ? $this->mID : $id;
		return  ($res == false) ? false : true;
	}
	function addRelacionComercial($empresa, $direccion, $telefono = 0, $observaciones =""){
		$xRel		= new cSocios_relaciones();
		$xF			= new cFecha();
		$xRel->apellido_materno("");
		$xRel->apellido_paterno("");
		$xRel->nombres($empresa);
		$xRel->calificacion_del_referente(1);
		$xRel->consanguinidad($this->CONSANGUINIDAD_NINGUNA);
		$xRel->credito_relacionado(DEFAULT_CREDITO);
		$xRel->curp("");
		$xRel->dato_extra_1("");//
		$xRel->dato_extra_2("");
		$xRel->dato_extra_3("");
		$xRel->dependiente("0");
		$xRel->domicilio_completo($direccion);
		$xRel->eacp(EACP_CLAVE);
		$xRel->estatus($this->ESTADO_ACTIVO);
		$xRel->fecha_alta(fechasys());
		$xRel->fecha_nacimiento(fechasys());
		$xRel->idusuario(getUsuarioActual());
		$xRel->monto_relacionado(0);
		$xRel->numero_socio(DEFAULT_SOCIO);
		
		$xRel->observaciones($observaciones);
		$xRel->ocupacion("");
		$xRel->porcentaje_relacionado(0);
		$xRel->socio_relacionado($this->mPersona);
		$xRel->codigo($this->mPersona);
		$xRel->sucursal(getSucursal());
		$xRel->telefono_movil("");
		$xRel->telefono_residencia($telefono);
		$xRel->tipo_relacion(PERSONAS_REL_REF_COMERCIAL);
		$id		= $xRel->query()->getLastID();
		$xRel->idsocios_relaciones($id);
		$res	= $xRel->query()->insert()->save();
		$this->mID	= ($res == false) ? $this->mID : $id;
		return  ($res == false) ? false : true;		
	}
}

class cPersonasTiposDeRelacion {
	private $mDatos		= array();
	private $mClave		= false;
	private $mOmitirAML	= false;
	private $mInit		= false;
	function __construct($clave = 0){		$this->mClave	= $clave;	}
	function init(){
		$sql			= "SELECT * FROM socios_relacionestipos WHERE idsocios_relacionestipos=" . $this->mClave . " LIMIT 0,1";
		$this->mDatos	= obten_filas($sql);
		if(isset($this->mDatos["requiere_domicilio"])){
			$this->mOmitirAML	= setNoMenorQueCero($this->mDatos["checar_aml"]);
			$this->mOmitirAML	= ($this->mOmitirAML == 0) ? true: false;
			$this->mInit	= false;
		}
		return $this->mInit;
	}
	function requiereDomicilio(){
		return ($this->mDatos["requiere_domicilio"] == 0) ? false : true;
	}
	function getOmitirAML(){ return $this->mOmitirAML; }
	function requiereActividadEconomica(){
		return ($this->mDatos["requiere_actividadeconomica"] == 0) ? false : true;
	}
	function requiereValidacion(){
		return ($this->mDatos["requiere_validacion"] == 0) ? false : true;
	}
	function getNombre(){ return $this->mDatos["descripcion_relacionestipos"]; }
}


class cPersonasVivienda{
	private $mTipo			= 99;
	private $mPersona		= false;
	private $mDatosInArray	= array();
	private $mInit			= false;
	private $mOB			= null;
	private $mObjColonia	= null;
	private $mObjEstado		= null;
	private $mMessages		= "";
	private $mIDCargado		= false;
	private $mClaveDeMun	=  0; 
	private $mCodigoPostal		= 0;
	private $mMunicipio			= "";
	private $mClaveDeEstado		= 0;
	private $mClaveDeEstadoSIC	= 0;
	private $mClaveDeEstadoABC	= 0;
	private $mClaveDeLocal		= 0;
	private $mClaveDePais		= "";
	private $mNombrePais		= "";
	private $mNombreLocalidad	= "";
	private $mNombreCiudad		= "";
	private $mNombreColonia		= ""; 
	private $mOficialVerifica	= 0;
	private $mFechaVerifica		= false;
	private $mFechaRegistro		= false;
	public $DOMICILIO_NO_VERIFICADO = 99;
	public $DOMICILIO_VERIFICADO 	= 1;
	private $mNombreEntidadFed		= "";
	private $mTelefonoFijo			= "";
	private $mTelefonoMovil			= "";
	private $mValidacionERRS		= 0;
	private $mValidacionWARNS		= 0;
	private $mRiesgoAML				= 0;
	private $mReferenciaDom			= "";
	private $mIDCache				= "";
	private $mEstadoRegistro		= 99;
	private $mOficial				= 0;
	private $mRegimen				= 0;
	private $mFirmCache				= "";
	
	function __construct($persona = false, $tipo = false){
		$this->mPersona	= setNoMenorQueCero($persona);
		$this->mTipo	= setNoMenorQueCero($tipo);
		//Iniciar Variables Globales
		$this->mCodigoPostal		= EACP_CODIGO_POSTAL;
		$this->mClaveDeLocal		= EACP_CLAVE_DE_LOCALIDAD;
		$this->mClaveDePais			= EACP_CLAVE_DE_PAIS;
		$this->mClaveDeMun			= EACP_CLAVE_DE_MUNICIPIO;
		$this->mClaveDeEstadoABC	= EACP_CLAVE_DE_ENTIDADFED;
		$this->mClaveDeEstado		= EACP_CLAVE_NUM_ENTIDADFED;
		$this->mClaveDeEstadoSIC	= EACP_CLAVE_DE_ENTIDAD_SIC;
		$this->mNombrePais			= EACP_DOMICILIO_PAIS;
		$this->mNombreLocalidad		= EACP_LOCALIDAD;
		$this->mNombreEntidadFed	= EACP_ESTADO;
		//$this->mNombreCiudad		= EACP_LOCALIDAD;
		//Iniciar Variables Locales
		if(isset($_SESSION[SYS_LOCAL_VARS_LOAD])){
			$xLoc						= new cLocal();
			$this->mClaveDeMun			= $xLoc->DomicilioMunicipioClave();
			$this->mClaveDeEstadoABC	= $xLoc->DomicilioEstadoClaveABC();
			$this->mClaveDeEstado		= $xLoc->DomicilioEstadoClaveNum();
			$this->mClaveDeEstadoSIC	= $xLoc->DomicilioEstadoClaveSIC();
			$this->mCodigoPostal		= $xLoc->DomicilioCodigoPostal();
			$this->mNombreEntidadFed	= $xLoc->DomicilioEstado();
		}				
	}
	function getClaveDePersona(){ return $this->mPersona; }
	function setID($id){ 
		if($id >0){
			$this->mIDCargado	= $id;
			$this->setIDCache($id);
		} 
	}
	function setIDCache($clave){if($clave >0){ $this->mIDCache = TPERSONAS_DIRECCIONES . "-$clave"; } }
	function setCleanCache(){
		$xCache = new cCache();
		if($this->mIDCache !== ""){
			$xCache->clean($this->mIDCache);
		}
		if($this->mFirmCache !==""){
			$xCache->clean($this->mFirmCache);
		}
	}
	
	function init($principal = false, $datos = false){
		$ByPrinc				= "";//($principal  == false) ? "" : " AND principal='1' ";

		$ByTipo					= ($this->mTipo > 0 AND $this->mTipo != 99 ) ?  " AND `tipo_domicilio`= " . $this->mTipo : "";
		$xCache					= new cCache();

		if(!is_array($datos)){
			
			$xDB				= new cSQLTabla(TPERSONAS_DIRECCIONES);
			$sql 				=  $xDB->getQueryInicial();
			if($this->mIDCargado > 0){
				$sql				.= " WHERE `idsocios_vivienda` = " . $this->mIDCargado . " LIMIT 0,1";
			} else {
				$sql				.= " WHERE socio_numero=" . $this->mPersona . " AND `estado_actual`>0 $ByPrinc $ByTipo ORDER BY principal DESC, fecha_alta DESC LIMIT 0,1";
			}
			$this->mFirmCache	= sha1($sql);
			$datos 				=  $xCache->get($this->mFirmCache);
			if(!is_array($datos)){
				$datos 				= obten_filas($sql);
				$xCache->set($this->mFirmCache, $datos);
				//setLog("Se lee " . $this->mFirmCache);
			}
		}
		$this->mDatosInArray	= $datos;
		//setLog($sql);
		$this->mOB				= new cSocios_vivienda();
		$this->mInit			= (isset($this->mDatosInArray["idsocios_vivienda"])) ? true : false;
		//DEFAULT_TIPO_DOMICILIO
		//setLog($sql);
		if($this->mInit == false){
			$this->mIDCargado	= false;
			$this->mMessages	.= "ERROR\tDomicilio no cargado\r\n";
		} else {
			$this->mIDCargado		= $this->mDatosInArray["idsocios_vivienda"];
			$this->mOB->setData($this->mDatosInArray);
			$this->mCodigoPostal	= setNoMenorQueCero($this->mOB->codigo_postal()->v());
			$this->mClaveDeLocal	= $this->mOB->clave_de_localidad()->v();
			$this->mClaveDePais		= strtoupper($this->mOB->clave_de_pais()->v());
			$this->mNombrePais		= strtoupper($this->mOB->nombre_de_pais()->v());
			$this->mNombreLocalidad	= strtoupper($this->mOB->localidad()->v());
			$this->mNombreColonia	= strtoupper($this->mOB->colonia()->v());
			$this->mNombreCiudad	= strtoupper($this->mOB->localidad()->v());
			$this->mNombreEntidadFed= setCadenaVal($this->mOB->estado()->v());
			$this->mMunicipio		= strtoupper($this->mOB->municipio()->v());
			$this->mReferenciaDom	= setCadenaVal($this->mOB->referencia()->v());
			$this->mPersona			= $this->mOB->socio_numero()->v();
			$this->mTipo			= $this->mOB->tipo_domicilio()->v();
			$this->mTelefonoFijo	= $this->mOB->telefono_residencial()->v();
			$this->mTelefonoMovil	= $this->mOB->telefono_movil()->v();
			$this->mEstadoRegistro	= $this->mOB->estado_actual()->v();
			$this->mOficialVerifica	= $this->mOB->oficial_de_verificacion()->v();
			$this->mFechaVerifica	= $this->mOB->fecha_de_verificacion()->v();
			$this->mFechaRegistro	= $this->mOB->fecha_alta()->v();
			$this->mOficial			= $this->mOB->idusuario()->v();
			$this->mRegimen			= $this->mOB->tipo_regimen()->v();
			$this->setIDCache($this->mIDCargado);
			$xCache->set($this->mIDCache, $this->mDatosInArray);
			//setLog($this->mCodigoPostal);
			//iniciar colonia si No hay Manual
			if(PERSONAS_VIVIENDA_MANUAL == false){
				if($this->mCodigoPostal > 0 ){
					$xCPActivo		= new cPersonasVivCodigosPostales($this->mCodigoPostal);
					if($xCPActivo->init() == true){
				
						$this->mClaveDeMun			= $xCPActivo->getClaveDeMunicipio();
						$this->mClaveDeEstadoABC	= $xCPActivo->getClaveDeEstadoABC();
						$this->mClaveDeEstado		= $xCPActivo->getClaveDeEstado();
						$this->mClaveDeEstadoSIC	= $xCPActivo->getClaveDeEstadoEnSIC();
					}
				}
			}
		}
		
		return $this->mDatosInArray;
	}
	function isInit(){ return $this->mInit; }
	function obj(){ 	if($this->mOB == null){ $this->init(); }	return $this->mOB;	}
	function getCiudad(){	return $this->mNombreCiudad;	}
	function getTelefonos($todos = true){	return ($todos == true) ? $this->mOB->telefono_residencial()->v() . "/" . $this->mOB->telefono_movil()->v() : setNoMenorQueCero($this->mOB->telefono_residencial()->v());	}
	function getCodigoPostal(){ return $this->mCodigoPostal;	}
	function getMunicipio(){	return $this->mMunicipio; 	}
	function getDatosInArray(){ return $this->mDatosInArray;	}
	function getEstado($out = OUT_HTML){ return $this->mNombreEntidadFed;	/*return strtoupper($this->mOB->estado()->v($out));*/	}
	function getAcceso(){	return $this->mOB->tipo_de_acceso()->v();	}
	function getCalle(){	return strtoupper($this->mOB->calle()->v());	}
	function getColonia(){ return $this->mNombreColonia; }
	function getNumeroExterior(){		return $this->mOB->numero_exterior()->v();	}
	function getNumeroInterior(){		return $this->mOB->numero_interior()->v();	}
	function getClaveUnica(){ return $this->mIDCargado; }
	function getClaveDeEstadoEnSIC(){ return $this->mClaveDeEstadoSIC; 	}
	function getClaveDeEstadoABC(){ return $this->mClaveDeEstadoABC;	}
	function getClaveDeEstado(){ return  $this->mClaveDeEstado;	}		
	function getClaveDeLocalidad(){ return $this->mClaveDeLocal; }
	function getLocalidad(){ return $this->mNombreLocalidad; }
	function getReferencia(){ return $this->mReferenciaDom; }
	function getClaveDePais(){return $this->mClaveDePais; }
	function getNombreDePais(){  return $this->mNombrePais; }
	function getDireccionBasica($ext = false){
		
		$direccion		= "";
		//$tipo_acceso	= (PERSONAS)
		$direccion		.= strtoupper($this->getAcceso()) . " " . $this->getCalle();
		$direccion		.= (trim($this->getNumeroExterior()) != "") ? " # " . $this->getNumeroExterior() : "";
		$direccion		.= (trim($this->getNumeroInterior()) != "") ? " INT " . $this->getNumeroInterior() : "";
		if($ext == true){
			$direccion		.= ",".$this->getColonia() . "," . $this->getMunicipio() . ",".$this->getEstado();
		}
		return $direccion;
	}
	function getCalleConNumero(){
		$direccion	= $this->getAcceso() . " " . $this->getCalle();
		$direccion	.= (trim( $this->getNumeroExterior() ) == "") ? " SN" : " " . $this->getNumeroExterior();
		$direccion	.= (trim( $this->getNumeroInterior() ) == "") ? "" : "-" . $this->getNumeroInterior();
		return strtoupper($direccion);
	}
	function getOEstado(){		return $this->mObjEstado;	}
	function getOColonia(){
		if($this->mObjColonia == null){
			$this->mObjColonia	= new cDomiciliosColonias();
			if($this->mObjColonia->existe($this->mCodigoPostal, "", "", true) == true){
				
			}
		} 
		return $this->mObjColonia;
	}
	function getClaveDeMunicipio(){		return $this->mClaveDeMun;	}
	function getTipoDeDomicilio(){ return $this->obj()->tipo_domicilio()->v();	}
	function getTipoDeRegimen(){	return $this->obj()->tipo_regimen()->v(); 	}
	function getTelefonoFijo(){ return $this->mTelefonoFijo; }
	function getTelefonoMovil(){ return $this->mTelefonoMovil; }
	function setDuplicarDomicilio($persona){
		$result		= false;
		if(setNoMenorQueCero($this->mIDCargado) <= 0){
			
		} else { /*socio_numero=20100816*/
		$sql	= " INSERT INTO socios_vivienda(
					socio_numero, tipo_regimen, calle, numero_exterior, numero_interior, colonia, localidad, estado, municipio, telefono_residencial, telefono_movil, tiempo_residencia, referencia, idusuario, principal, tipo_domicilio, codigo_postal, fecha_alta, codigo, sucursal, eacp, coordenadas_gps, tipo_de_acceso, fecha_de_verificacion, oficial_de_verificacion, estado_actual, clave_de_localidad, clave_de_pais, nombre_de_pais) 
					SELECT $persona, tipo_regimen, calle, numero_exterior, numero_interior, colonia, localidad, estado, municipio, telefono_residencial, telefono_movil, tiempo_residencia, referencia, idusuario, principal, tipo_domicilio, codigo_postal, fecha_alta, codigo, sucursal, eacp, coordenadas_gps, tipo_de_acceso, fecha_de_verificacion, oficial_de_verificacion, estado_actual, clave_de_localidad, clave_de_pais, nombre_de_pais
					FROM socios_vivienda WHERE `idsocios_vivienda` = " . $this->mIDCargado . "  LIMIT 0,1";
			$x		= my_query($sql);
			$result	= $x[SYS_ESTADO];
		}
		return $result;
	}
	function getFicha( $telefonoPrincipal = "", $extenso = false, $email ="" ){
		$xLng		= new cLang();
		$xT			= new cTipos();
		$xF			= new cFecha();
		$xT->setForceMayus();
		$xDS		= $this->obj();
		$tel1		= setNoMenorQueCero($xDS->telefono_residencial()->v());
		$tel2		= setNoMenorQueCero($xDS->telefono_movil()->v());
		$titMail	= "";
		$telefonoPrincipal	= setNoMenorQueCero($telefonoPrincipal);
		$telefonos	= ($telefonoPrincipal <= 0 OR $tel1 == $telefonoPrincipal OR $tel2 == $telefonoPrincipal ) ? "" : $telefonoPrincipal;
		$telefonos	.= ($telefonos == "") ? "" : " /";
		$telefonos	.= ( $tel1 <= 0 ) ? "" : $tel1;
		$telefonos	.= ($telefonos == "") ? "" : " /";
		$telefonos	.= ( $tel2 <= 0 ) ? "" : $tel2;
		
		if($email !== ""){
			$telefonos	.= ($telefonos == "") ? "$email" : " / $email";
			$titMail	= " / ". $xLng->getT("TR.EMAIL");
		}
		$numero		= $xDS->numero_exterior()->v();
		$numero		.= (trim($xDS->numero_interior()->v()) == "") ? "" : " /" . $xDS->numero_interior()->v();
		$numero		= strtoupper($numero);
		$callenum	= $xT->cChar($this->getCalleConNumero());
		if($this->mClaveDePais != EACP_CLAVE_DE_PAIS){
			$trDOM		= "<th class='izq'>" . $xLng->getT("TR.Municipio"). " / ". $xLng->getT("TR.Estado") . " / ". $xLng->getT("TR.PAIS") . "</th>	<td>" . $xT->cChar($this->getMunicipio()). " / " . $xT->cChar( $this->getEstado()) . " / " . $xT->cChar( $this->mNombrePais)  . "</td>";
		} else {
			$trDOM		= "<th class='izq'>" . $xLng->getT("TR.Municipio"). " / ". $xLng->getT("TR.Estado") . "</th>	<td>" . $xT->cChar($this->getMunicipio()). " / " . $xT->cChar( $this->getEstado()) . "</td>";
			if($this->getCiudad() == $this->getMunicipio()){
				$trDOM		= "<th class='izq'>" . $xLng->getT("TR.Estado") . "</th>	<td>" . $xT->cChar( $this->getEstado()) . "</td>";
			}			
		}

		$trExt		= "";
		$trExt2		= "";
		if( $extenso == true){
			$xOf	= new cOficial($this->mOficial);
			$xOf->init();
			$xTipoV	= new cPersonasViviendaTipo($this->mTipo);
			$xTipoV->init();
			$xReg	= new cPersonasViviendaRegimen($this->mRegimen);
			$xReg->init();
			$xNot	= new cHNotif();
			
			
			if($this->mEstadoRegistro == 0){
				$stat	= $xNot->get("BAJA", "idstat", $xNot->NOTICE);
			} else if($this->mEstadoRegistro == $this->DOMICILIO_VERIFICADO){
				$stat	= $xNot->get("VERIFICADO", "idstat", $xNot->SUCCESS);
			} else {
				$stat	= $xNot->get("NO VERIFICADO", "idstat", $xNot->WARNING);
			}
			$trExt	= "<tr>
							<th class='izq'>" . $xLng->getT("TR.TIPO") . "</th>
							<td>" . $xTipoV->getNombre() . "</td>					
							<th class='izq'>" . $xLng->getT("TR.REGIMEN") . "</th>
							<td>" . $xReg->getNombre() . "</td>
						</tr><tr>
							<th class='izq'>" . $xLng->getT("TR.Referencias") . "</th>
							<td colspan='3'>" . $xT->cChar($this->getReferencia()) . "</td>
						</tr><tr>
							<th class='izq'>" . $xLng->getT("TR.ESTATUS") . "</th>
							<td>" . $stat . "</td>					
							<th class='izq'>" . $xLng->getT("TR.USUARIO") . "</th>
							<td>" . $xOf->getNombreCompleto() . " / " .$xF->getFechaCorta($this->mFechaRegistro). "</td>
						</tr>";
			if($this->getEsVerificado() == true){
				$xOf	= new cOficial($this->mOficialVerifica);
				$xOf->init();
				$trExt	.= "<tr>
							<th class='izq'>" . $xLng->getT("TR.FECHA_DE VALIDACION") . "</th>
							<td>" . $xF->getFechaCorta($this->mFechaVerifica) . "</td>
							<th class='izq'>" . $xLng->getT("TR.USUARIO VALIDACION") . "</th>
							<td>" . $xOf->getNombreCompleto() . "</td>									
						</tr>";
			}
		}
		
		$eldom		= "<table>
						<tr>
							<th class='izq'>" . $xLng->getT("TR.Acceso") . " / ". $xLng->getT("TR.Codigo_postal") . "</th>
							<td>" . $callenum . " / " . $this->getCodigoPostal() . "</td>
							<th class='izq'>" . $xLng->getT("TR.Numero") . "</th>
							<td>$numero</td>
						</tr>
						<tr>
							<th class='izq'>" . $xLng->getT("TR.Colonia") . "</th>
							<td>" . $xT->cChar($this->getColonia()) . "</td>
							<th class='izq'>" . $xLng->getT("TR.TELEFONO") . $titMail . "</th>
							<td>$telefonos</td>
						</tr>
						<tr>
							<th class='izq'>" . $xLng->getT("TR.Ciudad") . "</th>
							<td>" . $xT->cChar($this->getCiudad()). "</td>
							$trDOM
						</tr>
						$trExt
						</table>";
		
		return $eldom;
	}
	function add($acceso, $NumeroExterior = "", $NumeroInterior = "", $Referencias = "", $TelefonoFijo = false, $TelefonoMovil = false, $TipoDeAcceso = "calle", $NombreColonia = "", $TipoDeDomicilio = false, 
			$TipoDeRegimen = false, $TiempoDeResidir = false, $EsPrincipal = false, $codigopostal = 0, $idlocalidad = false, $idpais = false, $nombre_localidad = "", $nombre_municipio = "", $nombre_entidad = "",
			$nombre_pais = "", $fecha_de_registro = false, $fecha_de_ingreso = false){
		$xT					= new cTipos();
		$xClean				= new cTiposLimpiadores();
		$xF					= new cFecha();
		$xQL				= new MQL();
		$xPLoc				= new cLocal();
		$xLog				= new cCoreLog();
		$valido				= true;
		$fecha_de_registro	= $xF->getFechaISO($fecha_de_registro);
		$idlocalidad		= setNoMenorQueCero($idlocalidad);
		$idlocalidad		= ($idlocalidad <= 0) ? FALLBACK_CLAVE_LOCALIDAD : $idlocalidad;
		$codigopostal		= setNoMenorQueCero($codigopostal);
		$TipoDeDomicilio	= setNoMenorQueCero($TipoDeDomicilio);
		$TipoDeRegimen		= setNoMenorQueCero($TipoDeRegimen);
		$TiempoDeResidir	= setNoMenorQueCero($TiempoDeResidir);
		$TelefonoFijo		= setNoMenorQueCero($TelefonoFijo);
		$TelefonoMovil		= setNoMenorQueCero($TelefonoMovil);

		//validar segun el pais
		$EsPrincipal		= $xT->cBool($EsPrincipal);
		$EsPrincipal		= ($EsPrincipal == true) ? SYS_UNO : SYS_CERO;
		$TipoDeDomicilio	= ($TipoDeDomicilio <= 0) ? DEFAULT_TIPO_DOMICILIO : $TipoDeDomicilio;
		$TipoDeRegimen		= ($TipoDeRegimen <= 0) ? FALLBACK_PERSONAS_REGIMEN_VIV : $TipoDeRegimen;
		$TiempoDeResidir	= ($TiempoDeResidir <= 0) ? DEFAULT_TIEMPO : $TiempoDeResidir;
		$acceso				= $xClean->cleanCalle($acceso);
		$NombreColonia		= setCadenaVal($NombreColonia);
		$nombre_entidad		= setCadenaVal($nombre_entidad);
		$nombre_localidad	= setCadenaVal($nombre_localidad);
		$nombre_municipio	= setCadenaVal($nombre_municipio);
		$nombre_pais		= setCadenaVal($nombre_pais);
		$clave_municipio	= $xPLoc->DomicilioMunicipioClave();
		$clave_estado		= $xPLoc->DomicilioEstadoClaveNum();
		$idpais				= setCadenaVal($idpais);
		
		$idpersona			= $this->mPersona;
		$xCol				= new cPersonasVivCodigosPostales($codigopostal);
		//if($codigopostal > 0 AND $idpais == EACP_CLAVE_DE_PAIS){}		
		$xLoc				= new cDomicilioLocalidad($idlocalidad);
		if(MODULO_AML_ACTIVADO == true){
			if($xLoc->init() == true){
				$idpais				= ($idpais == "") ? $xLoc->getClaveDePais() : $idpais;
				if($idpais != EACP_CLAVE_DE_PAIS){
					$nombre_localidad	= $xLoc->getNombre();
					$nombre_municipio	= ($nombre_municipio == "") ? $nombre_localidad : $nombre_municipio;
					$nombre_entidad		= ($nombre_entidad == "") ? $nombre_localidad : $nombre_entidad;
				} else {
					$nombre_localidad	= ($nombre_localidad == "") ? $xLoc->getNombre() : $nombre_localidad;
				}
			}
		}
		$xPais				= new cDomiciliosPaises($idpais);
		if($xPais->init() == true){
			$nombre_pais	= $xPais->getNombre();
		}
		if($idpais == EACP_CLAVE_DE_PAIS){
			if(PERSONAS_VIVIENDA_MANUAL == false){
				if($xCol->init() == true){
					$nombre_entidad		= ($nombre_entidad == "") ? $xCol->getNombreEstado() : $nombre_entidad;
					$nombre_localidad	= ($nombre_localidad == "") ? $xCol->getNombreLocalidad() : $nombre_localidad;
					$nombre_municipio	= ($nombre_municipio == "") ? $xCol->getNombreMunicipio() : $nombre_municipio;
					$NombreColonia		= ($NombreColonia == "") ? $xCol->getNombre() : $NombreColonia;
					$clave_estado		= $xCol->getClaveDeEstado();
					$clave_municipio	= $xCol->getClaveDeMunicipio();
				} else {
					//Si no es vivienda manual, es false
					$valido				= false;
					$xLog->add("ERROR\tLa vivienda no es manual, se necesita un Codigo Postal\r\n");
				}
			} else {
				if($xCol->init() == true){
					$nombre_entidad		= ($nombre_entidad == "") ? $xCol->getNombreEstado() : $nombre_entidad;
					$nombre_localidad	= ($nombre_localidad == "") ? $xCol->getNombreLocalidad() : $nombre_localidad;
					$nombre_municipio	= ($nombre_municipio == "") ? $xCol->getNombreMunicipio() : $nombre_municipio;
					$NombreColonia		= ($NombreColonia == "") ? $xCol->getNombre() : $NombreColonia;
					$clave_estado		= $xCol->getClaveDeEstado();
					$clave_municipio	= $xCol->getClaveDeMunicipio();
				}
			}
			
		} else {
			$clave_municipio		= FALLBACK_CLAVE_MUNICIPIO;
			$clave_estado			= FALLBACK_CLAVE_ENTIDADFED;
			//TODO: Validar si la entidad es local
			if($nombre_localidad !== ""){
				$nombre_municipio	= ($nombre_municipio == "") ? $nombre_localidad : $nombre_municipio;
				$nombre_entidad		= ($nombre_entidad == "") ? $nombre_localidad : $nombre_entidad;
			}
		}
		if($acceso == "" OR $NumeroExterior == ""){
			$valido		= false;
			$xLog->add("ERROR\tLa vivienda debe tener Acceso $acceso y Numero ext $NumeroExterior\r\n");
		}
		$xV	= new cSocios_vivienda();
		$xV->calle($acceso);
		$xV->clave_de_localidad($idlocalidad);
		$xV->clave_de_pais($idpais);
		
		$xV->clave_de_municipio($clave_municipio);
		$xV->clave_de_entidadfederativa($clave_estado);
		$xV->codigo($idpersona);
		$xV->codigo_postal($codigopostal);
		$xV->colonia($NombreColonia);
		$xV->coordenadas_gps("");
		$xV->eacp(EACP_CLAVE);
		$xV->estado($nombre_entidad);
		$xV->estado_actual($this->DOMICILIO_NO_VERIFICADO);
		$xV->fecha_alta($fecha_de_registro);
		$xV->fecha_de_verificacion($fecha_de_registro);
		$xV->idusuario(getUsuarioActual());
		$xV->localidad($nombre_localidad);
		$xV->municipio($nombre_municipio);
		$xV->nombre_de_pais($nombre_pais);
		$xV->numero_exterior($NumeroExterior);
		$xV->numero_interior($NumeroInterior);
		$xV->oficial_de_verificacion(getUsuarioActual());
		$xV->principal($EsPrincipal);
		$xV->referencia($Referencias);
		$xV->socio_numero($idpersona);
		$xV->sucursal(getSucursal());
		$xV->telefono_movil($TelefonoMovil);
		$xV->telefono_residencial($TelefonoFijo);
		$xV->tiempo_residencia($TiempoDeResidir);
		$xV->tipo_de_acceso($TipoDeAcceso);
		$xV->tipo_domicilio($TipoDeDomicilio);
		$xV->tipo_regimen($TipoDeRegimen);
		$xV->idsocios_vivienda( $xV->query()->getLastID() );
		
		$rs	= false;
		if ($valido == true){ 
			$rs	= $xV->query()->insert()->save();
			if($rs !== false){
				$idviv	= $xV->idsocios_vivienda()->v();
				$this->mIDCargado = $idviv;
				$xLog->add("OK\tVivienda con ID $idviv Agregada\r\n");
				$EsPrincipal		= $xT->cBool($EsPrincipal);
				if($EsPrincipal == true){ //Actualizar principales
					$xQL->setRawQuery("UPDATE socios_vivienda SET `principal`='0' WHERE `principal`='1' AND `socio_numero`= " . $this->mPersona .  " AND `idsocios_vivienda`!= $idviv ");
				}
				//Personas Actualizar Nivel de Riesgo
				if(MODULO_AML_ACTIVADO == true){
					$xAML	= new cAMLPersonas($idpersona);
					if($xAML->init($idpersona) == true){
						$xAML->setAnalizarNivelDeRiesgo(true);
						
					}
				}
			}
		}
		$this->mMessages	= $xLog->getMessages();
		return ($rs == false) ? false: true;
	}
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function getTipoPorFecha($fecha, $fecha_final = false){
		$xAE	= new cPersonaActividadEconomica($this->mPersona);
		return $xAE->getTipoPorFecha($fecha, $fecha_final);
	}
	function setInactivo(){
		if($this->mIDCargado > 0){
			$xQL	= new MQL();
			$xQL->setRawQuery("UPDATE `socios_vivienda` SET `estado_actual`=0 WHERE `idsocios_vivienda`=" . $this->mIDCargado);
			$this->mMessages	.= "WARN\tSe desactiva el Domicilio " . $this->mIDCargado . "\r\n";
		}
	}
	function setPrincipal($v = '1'){
		if($this->mIDCargado > 0){
			$xQL	= new MQL();
			
			$xQL->setRawQuery("UPDATE `socios_vivienda` SET `principal`='$v' WHERE `idsocios_vivienda`=" . $this->mIDCargado);
			if($v == "1"){
				$xQL->setRawQuery("UPDATE `socios_vivienda` SET `principal`='0' WHERE `idsocios_vivienda`!=" . $this->mIDCargado);
			}
			$xQL	= null;
			$this->setCleanCache();
			$this->mMessages	.= "WARN\tEl Domicilio " . $this->mIDCargado . " se marca como principal\r\n";
		}
	}
	function setEliminar(){
		$id			= $this->mIDCargado;
		$xQL		= new MQL();
		$persona	= $this->mPersona;
		$idnuevo	= 1;
		//cambiar a su otro domicilio
		$DD			= $xQL->getDataRow("SELECT * FROM `socios_vivienda` WHERE `socio_numero`=$persona AND `idsocios_vivienda`!=$id ORDER BY `fecha_alta` ASC, `tipo_domicilio` LIMIT 0,1");
		//setLog("SELECT * FROM `socios_vivienda` WHERE `socio_numero`=$persona AND `idsocios_vivienda`!=$id ORDER BY `fecha_alta` ASC, `tipo_domicilio` LIMIT 0,1");
		if(isset($DD["idsocios_vivienda"])){
			$idnuevo			= $DD["idsocios_vivienda"];
			$this->mMessages	.= "WARN\tSe obtuvo el nuevo ID a $idnuevo\r\n";
		}
		$res1	= $xQL->setRawQuery("UPDATE `socios_aeconomica` SET `domicilio_vinculado`=$idnuevo WHERE `domicilio_vinculado`=$id");
		$this->mMessages		.= "WARN\tActualizar Actividad Economica del Domiclio $id al $idnuevo\r\n";
		$res	= $xQL->setRawQuery("DELETE FROM `socios_vivienda` WHERE `idsocios_vivienda`=$id");
		return ($res == false) ? false : true;
	}
	function getRiesgoAML(){ return $this->mRiesgoAML; }
	
	function setValidarDomicilios($contexto	= false){
		$xQL	= new MQL();
		$rs		= $xQL->getDataRecord("SELECT * FROM `socios_vivienda` WHERE `socio_numero`=". $this->mPersona);
		$xViv	= new cSocios_vivienda();
		$valido	= true;
		$xReg	= new cReglasDeCalificacion();
		$factor	= 0;
		foreach ($rs as $rw){
			$xViv->setData($rw);
			$calle			= setCadenaVal($xViv->calle()->v());
			$numero			= trim($xViv->numero_exterior()->v());
			$xReg->setPersona($xViv->socio_numero()->v());
			$codigopostal	= setNoMenorQueCero($xViv->codigo_postal()->v());
			$cestado		= $xViv->clave_de_entidadfederativa()->v();
			$cmunicipio		= $xViv->clave_de_municipio()->v();
			$cpais			= $xViv->clave_de_pais()->v();
			//Calle
			if(strlen($calle) <= 0){
				$this->mValidacionERRS++;
				$valido	= false;
				$xReg->add($xReg->PERS_DOM_INC);
			}
			//Numero Exterior
			if(strlen($numero) <= 0){
				$this->mValidacionERRS++;
				$valido	= false;
				$xReg->add($xReg->PERS_DOM_INC);
			}
			//Validar codigo postal
			//se valida cada X dias
			//$xReg->CRED_FALLA_CAHORR;
			if(PERSONAS_VIVIENDA_MANUAL == false AND ($cpais == EACP_CLAVE_DE_PAIS)){
				$xReg->getValoresDeCalificacion();
				if($xReg->getEsVigente($xReg->PERS_DOMCP_VALID) == false){
					$xCP	= new cPersonasVivCodigosPostales($codigopostal);
					if($xCP->init() == true){
						//clave de estado
						$cpval	= ($cestado != $xCP->getClaveDeEstado()) ? false : true;
						$cpval	= ($cmunicipio != $xCP->getClaveDeMunicipio()) ? false : $cpval;

						if($cpval == true){
							$xReg->add($xReg->PERS_DOMCP_VALID, true);
						} else {
							$xReg->add($xReg->PERS_DOMCP_VALID);
						}
					} else {
						$xReg->add($xReg->PERS_DOMCP_VALID);
					}
					//$xCol->isCiudad()
				}
			} else {
				$xReg->add($xReg->PERS_DOMCP_VALID, true);
			}
			switch($contexto){
				case MMOD_AML:
					$xPais	= new cDomiciliosPaises($cpais);
					//$xMat	= new cAMLMatrizDeRiesgo();
					//$xMat->initByTopico($xMat->P_RIESGO_DOM_PAIS);
					if($xPais->init() == true){
						if($this->mRiesgoAML == SYS_RIESGO_ALTO){
							//Nada que hacer
						} else {
							$amlriesgo	= $xPais->getRiesgoAMLAsociado();
							if($amlriesgo == SYS_RIESGO_ALTO){
								$factor				= 1;
								$this->mRiesgoAML	= $amlriesgo;
							} else {
								$factor++;
								$this->mRiesgoAML	+= $amlriesgo;
							}
							
						}
					}
					break;
			}
		}
		if($valido == true){
			$xReg->add($xReg->PERS_DOM_INC, true);
		}
		if($factor > 1){
			//setLog($this->mRiesgoAML);
			$this->mRiesgoAML	= round(( $this->mRiesgoAML / $factor ),0);
		}
		if(MODULO_AML_ACTIVADO == true){
			$xRk	= new cRiesgos();
			$this->mRiesgoAML	= $xRk->getNivelarR($this->mRiesgoAML);
		}
		return $valido;
	}
	function setActualizarCiudad($ciudad){
		$ciudad	= setCadenaVal($ciudad);
		$this->obj()->localidad($ciudad);
		$this->obj()->query()->update()->save($this->obj()->idsocios_vivienda()->v());
	}
	function getIDVivienda(){ return $this->mIDCargado; }
	function getEsVerificado(){
		return ($this->mEstadoRegistro == $this->DOMICILIO_VERIFICADO) ? true : false;
	}
	function getEsActivo(){
		return ($this->mEstadoRegistro == 0) ? false : true;
	}
	function setCuandoSeActualiza(){
		$this->setCleanCache();
		
	}
	function getExisteID($id){
		$id		= setNoMenorQueCero($id);
		$xQL	= new MQL();
		$cnt	= 0;
		$D		= $xQL->getDataRow("SELECT COUNT(`idsocios_vivienda`) AS 'cnt' FROM `socios_vivienda` WHERE `idsocios_vivienda`=$id");
		if(isset($D["cnt"])){
			$cnt	= $D["cnt"];
		}
		$xQL	= null;
		return ($cnt>0 ) ? true : false;
	}
}

class cPersonaActividadEconomica {
	private $mTipo					= 99;
	private $mPersona				= false;
	private $mDatosInArray			= array();
	private $mInit					= false;
	private $mOB					= null;
	private $mOEmp					= null;
	private $mOTipo					= null;
	private $mMessages				= "";
	private $mLoc					= null;
	private $mEmpresaClavePersona	= null;
	private $mClaveEmpresa			= null;
	private $mCodigoPostal			= null;
	private $mIDDomicilio			= null;
	private $mDomicilio				= "";
	private $mClaveDeLocalidad		= null;
	private $mClaveDeMunicipio		= null;
	private $mClaveDeEstado			= null;
	private $mClaveDeEstadoSIC		= null;
	private $mClaveDePais			= null;
	private $mClaveDeActAML			= 0;
	private $mClaveDeActSCIAN		= 0;
	private $mDescripcion			= "";
	
	private $mIDCargado			= null;
	private $mNombreDeLocalidad	= null;
	private $mNombreDeMunicipio	= null;
	private $mNombreDeEstado		= null;
	private $mNombreEmpresa		= "";
	private $mNombreColonia		= "";
	private $mCalle				= "";
	private $mNumeroExt			= "";
	private $mViviendaInit			= false;
	private $mPuesto				= "";
	private $mAntiguedad			= null;
	private $mDepto				= "";
	private $mIDEmpleado			= "";
	private $mNSS					= 0;
	private $mExtTelefonica		= 0;
	private $mTelefono				= 0;
	private $mAMLNivelRiesgo		= 1;
	private $mAMLGeneraPEP			= false;
	private $mIDCache				= "";
	private $mTipoDispersion		= 0;
	private $mFechaDeIngreso		= false;
	private $mSectorEconomico		= 0;

	
	public $ESTADO_NOVERIFICADO		= 99;
	public $ESTADO_VERIFICADO		= 1;
	public $ESTADO_ANTERIOR			= 0;
	//private $mNumero		= "";
	//private $mCalle			= "";
	
	function __construct($persona = false, $tipo = false){
		$this->mPersona				= setNoMenorQueCero($persona);
		$tipo						= setNoMenorQueCero($tipo);
		$this->mTipo				= ($tipo <= 0) ? FALLBACK_ACTIVIDAD_ECONOMICA : $tipo;
		$this->mLoc					= new cLocal();
		$this->mClaveDeEstado		= $this->mLoc->DomicilioEstadoClaveNum();
		$this->mClaveDeMunicipio	= $this->mLoc->DomicilioMunicipioClave();
		$this->mClaveDeLocalidad	= $this->mLoc->DomicilioLocalidadClave();
		$this->mClaveDeEstadoSIC	= $this->mLoc->DomicilioEstadoClaveSIC();
		
		$this->mNombreDeLocalidad	= $this->mLoc->DomicilioLocalidad();
		$this->mNombreDeMunicipio	= $this->mLoc->DomicilioMunicipio();
		$this->mNombreDeEstado		= $this->mLoc->DomicilioEstado();
		$this->mCodigoPostal		= $this->mLoc->DomicilioCodigoPostal();
		$this->mAntiguedad			= DEFAULT_TIEMPO;
		$this->mClaveEmpresa		= FALLBACK_CLAVE_EMPRESA;
		$this->mClaveDePais			= EACP_CLAVE_DE_PAIS;
		$this->mIDCache				= EACP_CLAVE . "." . $this->mPersona . ".domicilio";
		$this->mFechaDeIngreso		= fechasys();
		$this->mTipoDispersion		= FALLBACK_PERSONAS_AE_TIPO_DISPERSION;
		$this->setIDCache(0, $this->mPersona);
	}
	function isInit(){ return $this->mInit; }
	function setID($id){ $this->mIDCargado = setNoMenorQueCero($id); }
	function setIDCache($clave = 0, $persona = 0){
		if($clave > 0){
			$this->mIDCache		= TPERSONAS_ACTIVIDAD_ECONOMICA . "-" . $clave;
		} else {
			if($persona > DEFAULT_SOCIO){
				$this->mIDCache	= TPERSONAS_ACTIVIDAD_ECONOMICA . "-persona-$persona-tipo-" . $this->mTipo;
			}
		}
	}
	function setCleanCache($id = ""){ $xCache = new cCache(); $id = ($id== "") ? $this->mIDCache : $id; $xCache->clean($id); }	
	function init($data = false){
		$ql		= new MQL();
		$xCache	= new cCache();
		if($this->mPersona > 0){
			$ByTipo							= "";// ($this->mTipo == false ) ? "" : "AND (`socios_aeconomica`.`tipo_aeconomica` = $tipo)";//no aplica tipo
			$ByID							= (setNoMenorQueCero($this->mIDCargado) > 1)? " AND `idsocios_aeconomica` = " . $this->mIDCargado : "";
			if(!is_array($data)){
				$data						= $xCache->get($this->mIDCache);
				if(!is_array($data)){
					$xDB					= new cSQLTabla(TPERSONAS_ACTIVIDAD_ECONOMICA);
					$sql 					= $xDB->getQueryInicial() . "	WHERE (`socios_aeconomica`.`socio_aeconomica` =" .  $this->mPersona . ") $ByTipo $ByID	ORDER BY `fecha_alta` DESC, `socios_aeconomica`.`monto_percibido_ae` DESC LIMIT 0,1";
					$data					= $ql->getDataRow($sql);
				}
			}
			$this->mDatosInArray			= $data;
			$this->mInit					= (isset($this->mDatosInArray["idsocios_aeconomica"])) ? true : false;
			$this->mTipo					= FALLBACK_ACTIVIDAD_ECONOMICA;
			if($this->mInit == true){
				$this->mOB					= new cSocios_aeconomica();
				$this->mOB->setData($this->mDatosInArray);
				//$this->mOB->query()->initByID($this->mDatosInArray["idsocios_aeconomica"]);
				$this->mIDCargado			= $this->mOB->idsocios_aeconomica()->v();
				$this->mClaveEmpresa		= $this->mOB->dependencia_ae()->v();
				$this->mDomicilio			= $this->mOB->domicilio_ae()->v();
				$this->mCodigoPostal		= setNoMenorQueCero($this->mOB->ae_codigo_postal()->v());
				$this->mIDDomicilio			= setNoMenorQueCero($this->mOB->domicilio_vinculado()->v());
				$this->mNombreColonia		= "";
				//Iniciar Empresa
				$this->mOEmp				= new cEmpresas( $this->mClaveEmpresa); $this->mOEmp->init();
				$this->mEmpresaClavePersona	= $this->mOEmp->getClaveDePersona();
				
				$this->mPuesto				= $this->mOB->puesto()->v(OUT_TXT);
				$this->mTipo				= $this->mOB->tipo_aeconomica()->v();
				$this->mDepto				= $this->mOB->departamento_ae()->v(OUT_TXT);
				$this->mTipoDispersion		= $this->mOB->empleado_tipo_de_dispersion()->v();
				$this->mFechaDeIngreso		= $this->mOB->fecha_de_ingreso()->v();
				$this->mDescripcion			= $this->mOB->descripcion()->v();
				$this->mClaveDeActAML		= $this->mOB->tipo_aeconomica()->v();
				$this->mClaveDeActSCIAN		= $this->mOB->clave_scian()->v();
				$this->mSectorEconomico		= $this->mOB->sector_economico()->v();
				
				//Iniciar por codigo postal
				$xViv					= new cPersonasVivienda($this->mPersona, PERSONAS_TIPO_DOM_LABORAL);
				if($this->mIDDomicilio > 1){ $xViv->setID($this->mIDDomicilio); }
				$xViv->init();
				if( $xViv->isInit() == true ){
					$this->mDomicilio				= $xViv->getDireccionBasica();
					$this->mClaveDeEstado			= $xViv->getClaveDeEstado();
					$this->mClaveDeEstadoSIC		= $xViv->getClaveDeEstadoEnSIC();
					$this->mClaveDeMunicipio		= $xViv->getClaveDeMunicipio();
					$this->mClaveDeLocalidad		= $xViv->getClaveDeLocalidad();
					$this->mCodigoPostal			= $xViv->getCodigoPostal();
					$this->mNombreDeLocalidad		= $xViv->getLocalidad();
					$this->mNombreDeMunicipio		= $xViv->getMunicipio();
					$this->mNombreDeEstado			= $xViv->getEstado();
					$this->mCalle					= $xViv->getCalle();
					$this->mNumeroExt				= $xViv->getNumeroExterior();
					$this->mNombreColonia			= $xViv->getColonia();
					
					$this->mClaveDePais				= $xViv->getClaveDePais();
					$this->mViviendaInit			= true;						
				} else {
					if($this->mCodigoPostal > 0){
						$xCol		= new cPersonasVivCodigosPostales($this->mCodigoPostal);
						if($xCol->init() == true){
							$this->mClaveDeEstado			= $xCol->getClaveDeEstado();
							$this->mClaveDeMunicipio		= $xCol->getClaveDeMunicipio();
							$this->mClaveDeLocalidad		= $xCol->getClaveDeLocalidad();
							$this->mNombreDeLocalidad		= $xCol->getNombreLocalidad();
							$this->mNombreDeMunicipio		= $xCol->getNombreMunicipio();
							$this->mNombreDeEstado			= $xCol->getNombreEstado();
							$this->mClaveDeEstadoSIC		= $xCol->getClaveDeEstadoEnSIC();
							$this->mViviendaInit			= true;							
						} else {
							
						}
					}
				}
				/*if(PERSONAS_VIVIENDA_MANUAL == false){
					if($this->mIDDomicilio <= 1 AND $this->mCodigoPostal > 0) {
						$xCols						= new cDomiciliosColonias();
						$xCols->getClavePorCodigoPostal($this->mCodigoPostal);
						
						$this->mClaveDeEstado			= $xCols->getClaveDeEstado();
						$this->mClaveDeMunicipio		= $xCols->getClaveDeMunicipio();
						$this->mClaveDeLocalidad		= $xCols->getClaveDeEstado();	
						$this->mNombreDeLocalidad		= $xCols->getNombreLocalidad();
						$this->mNombreDeMunicipio		= $xCols->getNombreMunicipio();
						$this->mNombreDeEstado			= $xCols->getNombreEstado();
						$this->mNombreColonia			= $xCols->getNombre();
						$this->mClaveDeEstadoSIC		= $xCols->getClaveEstadoEnSIC();
						$this->mClaveDePais				= $xCols->getClaveDePais();
						$this->mViviendaInit			= true;
					}
				}*/
			}
		}
		if($this->mOB == null){
			$this->mOB					= new cSocios_aeconomica();
			$this->mClaveEmpresa		= FALLBACK_CLAVE_EMPRESA;
			$this->mEmpresaClavePersona	= FALLBACK_CLAVE_DE_PERSONA;
		}
		return $this->mDatosInArray;
	}
	function getDatosInArray(){ return $this->mDatosInArray;	}
	function obj(){ if($this->mOB == null){ $this->init(); } return $this->mOB;	}
	function getOTipo(){
		if($this->mOTipo == null){ $this->mOTipo = new cPersonaActividadEconomicaCatalogo($this->mTipo); }
		return $this->mOTipo;
	}
	function getPuesto($evaluar  =false, $PorDefecto = ""){
		$xClean	= new cTiposLimpiadores();
		if($evaluar == true ){ $this->mPuesto = $xClean->cleanEmpleo($this->mPuesto, $PorDefecto); } 
		return $this->mPuesto;	
	}
	function getDomicilio(){  return $this->mDomicilio; }
	function getNombreEmpresa(){ return $this->obj()->nombre_ae()->v(OUT_TXT); }
	function getTelefono(){ return $this->obj()->telefono_ae()->v(); }
	function getClaveDeActividad(){ return $this->obj()->tipo_aeconomica()->v(); }
	function getClaveDeSector(){ return $this->obj()->sector_economico()->v(); }
	function getEstadoActual(){ return $this->obj()->estado_actual()->v(); }
	function getFechaVerificacion(){ $xF	= new cFecha(); return $xF->getFechaISO($this->obj()->fecha_de_verificacion()->v()); }
	function getFechaIngreso(){
		$fecha	= $this->obj()->fecha_de_ingreso()->v();
		$xF		= new cFecha();
		if($fecha == "" OR $fecha == "0000-00-00"){
			$fecha	= $xF->setRestarDias($this->obj()->antiguedad_ae()->v(), fechasys());
		}
		return $xF->getFechaISO($fecha);
	}
	function getCalle(){ return $this->mCalle;  }
	function getNumeroExterior(){ return $this->mNumeroExt; }
	function getNombreMunicipio(){ return $this->mNombreDeMunicipio;}
	function getNombreEstado(){ return $this->mNombreDeEstado; }
	function getNombreColonia(){ return $this->mNombreColonia; }
	function getLocalidad(){ return $this->mNombreDeLocalidad; }
	function getClaveDeLocalidad(){ return $this->mClaveDeLocalidad;	}
	function getClaveDeMunicipio(){ return $this->mClaveDeMunicipio;	}
	function getClaveDeEstado(){ return $this->mClaveDeEstado;	}
	function getClaveDeEstadoEnSIC(){ return $this->mClaveDeEstadoSIC;	}
	function getCodigoPostal(){ return $this->mCodigoPostal;	}
	function getClaveDeEmpresa(){ return $this->mClaveEmpresa; }
	function getClaveDePais(){ return $this->mClaveDePais; }
	function getSalarioMensual(){ return $this->obj()->monto_percibido_ae()->v(); }
	function getSectorEconomico(){ return $this->mSectorEconomico; }
	
	function getOEmpresa(){ return $this->mOEmp;	}
	function getNumeroDeSeguridadSocial(){ return $this->obj()->numero_de_seguridad_social()->v();	}
	function setUpdatePorEmpresa($guardarVinculado = false){
		$idx		= false;
		if($this->mInit == true){
			if($this->mClaveEmpresa != FALLBACK_CLAVE_EMPRESA){
				$xEmp				= new cEmpresas($this->mClaveEmpresa);
				if($xEmp->init() == true){
					$OPers		= $xEmp->getOPersona();
					if($OPers != null){
						$ODom	= $OPers->getODomicilio();
						if($ODom != null){
							$this->mOB->ae_clave_de_localidad($ODom->getClaveDeLocalidad());
							$this->mOB->ae_codigo_postal( $ODom->getCodigoPostal() );
							$this->mOB->localidad_ae( $ODom->getCiudad() );
							$this->mOB->municipio_ae( $ODom->getMunicipio() );
							$this->mOB->estado_ae( $ODom->getEstado() );
							$this->mOB->domicilio_ae( $ODom->getDireccionBasica() );
							if($guardarVinculado == true){
								$this->mOB->domicilio_vinculado( $ODom->getClaveUnica() );							
							}
							$idx	= $this->mOB->query()->update()->save( $this->mOB->idsocios_aeconomica()->v() );
							if($idx == false){
								$this->mMessages	.= "ERROR\tAl Actualizar por EMPRESA " . $this->mClaveEmpresa . "\r\n";
							} else {
								$this->mMessages	.= "OK\tActualizacion correcta por EMPRESA " . $this->mClaveEmpresa . "\r\n";
							}
						}
					}

				}
			}
		}
		return $idx;
	}
	function setUpdatePorDomicilio($ClaveUnica = false){
		$idx			= false;
		$ClaveUnica		= setNoMenorQueCero($ClaveUnica);
		if($this->mInit == true){
			$ODom			= new cPersonasVivienda($this->mPersona, PERSONAS_TIPO_DOM_LABORAL);
			if(setNoMenorQueCero($ClaveUnica) > 1){ $ODom->setID($ClaveUnica); }
			$ODom->init();
			if($ODom->isInit() == true){
				$this->mOB->ae_clave_de_localidad($ODom->getClaveDeLocalidad());
				$this->mOB->ae_codigo_postal( $ODom->getCodigoPostal() );
				$this->mOB->localidad_ae( $ODom->getCiudad() );
				$this->mOB->municipio_ae( $ODom->getMunicipio() );
				$this->mOB->estado_ae( $ODom->getEstado() );
				$this->mOB->domicilio_ae( $ODom->getDireccionBasica() );
				//if($guardarVinculado == true){
				$this->mOB->domicilio_vinculado( $ODom->getClaveUnica() );
				//}
				$idx	= $this->mOB->query()->update()->save( $this->mOB->idsocios_aeconomica()->v() );
				if($idx == false){
					$this->mMessages	.= "ERROR\tAl Actualizar por Domicilio " . $ODom->getClaveUnica() . "\r\n";
				} else {
					$this->mMessages	.= "OK\tActualizacion correcta por Domicilio " . $ODom->getClaveUnica() . "\r\n";
				}								
			}
		}
		return $idx;
	}
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function getFicha(){
		//empleado publico	99	BANCO NACIONAL DE OBRAS Y SERVICIOS PUBLICOS S.N.C.	Mérida MERIDA YUCATAN	52701200/0	SUBGERENTE	DIRECCION FIDUCIARIA	
		$ta		= "";
		$Tipo	= $this->getOTipo();
		$lng	= new cLang();
		$ta		.= "<tr><th>";
		$ta		.= $lng->getT("TR.Tipo de Actividad");
		$ta		.= "</th><td>";
		$ta		.= $Tipo->getNombre();
		$ta		.= "</td><th>";
		$ta		.= $lng->getT("TR.Empresa");
		$ta		.= "</th><td>";
		$ta		.= $this->getNombreEmpresa();
		$ta		.= "</td></tr>";
		
		$ta		.= "<tr><th>";
		$ta		.= $lng->getT("TR.Puesto");
		$ta		.= "</th><td>";
		$ta		.= $this->getPuesto(true);
		$ta		.= "</td><th>";
		$ta		.= $lng->getT("TR.Departamento");
		$ta		.= "</th><td>";
		$ta		.= $this->getDepartamento();
		$ta		.= "</td></tr>";		

		//domicilio
		//telefono extension
		//puesto/cargo
		
		return "<table>" . $ta . "</table>";
	}
	function setEmpresa($empresa, $puesto, $depto = "", $idempleado = "", $nss = 0, $ExtTelefonica = 0, $tipo_de_dispersion = 1, $fecha_de_ingreso = false ){
		$xF						= new cFecha();
		//cargar datos de la empresa
		$this->mClaveEmpresa	= $empresa;
		$this->mPuesto			= $puesto;
		$this->mDepto			= $depto;
		$this->mIDEmpleado		= $idempleado;
		$this->mNSS				= $nss;
		$this->mExtTelefonica	= $ExtTelefonica;
		$this->mFechaDeIngreso	= $xF->getFechaISO($fecha_de_ingreso);
		$this->mTipoDispersion	= $tipo_de_dispersion;
		//iniciar domicilio?
		if(PERSONAS_CONTROLAR_POR_EMPRESA == true){
			if($this->mClaveEmpresa != FALLBACK_CLAVE_EMPRESA AND $this->mClaveEmpresa != DEFAULT_EMPRESA){
				$xEmp					= new cEmpresas($this->mClaveEmpresa);
				if($xEmp->init() == true){
					$this->mNombreEmpresa	= $xEmp->getNombre();
					$this->mDomicilio		= $xEmp->getDomicilio();
					$OPers					= $xEmp->getOPersona();
					if($OPers != null){
						$xViv				= $OPers->getODomicilio();
						if($xViv != null){
							$this->mClaveDeEstado		= $xViv->getClaveDeEstado();
							$this->mClaveDeMunicipio	= $xViv->getClaveDeMunicipio();
							$this->mClaveDeLocalidad	= $xViv->getClaveDeLocalidad();
							
							$this->mNombreDeLocalidad	= $xViv->getLocalidad();
							$this->mNombreDeMunicipio	= $xViv->getMunicipio();
							$this->mNombreDeEstado		= $xViv->getEstado();
							$this->mCodigoPostal		= $xViv->getCodigoPostal();
							$this->mIDDomicilio			= $xViv->getClaveUnica();		
						}
					}
				}
			}
		}
	}
	function setDomicilioVinculado($iddomicilio){
		$iddomicilio	= setNoMenorQueCero($iddomicilio);
		if($iddomicilio > 1){
			//cargar datos del Domicilio
			$xViv			= new cPersonasVivienda();
			$xViv->setID($iddomicilio);
			$xViv->init();
			if($xViv->isInit() == true){
				$this->mClaveDeEstado		= $xViv->getClaveDeEstado();
				$this->mClaveDeMunicipio	= $xViv->getClaveDeMunicipio();
				$this->mClaveDeLocalidad	= $xViv->getClaveDeLocalidad();
					
				$this->mNombreDeLocalidad	= $xViv->getLocalidad();
				$this->mNombreDeMunicipio	= $xViv->getMunicipio();
				$this->mNombreDeEstado		= $xViv->getEstado();
				$this->mCodigoPostal		= $xViv->getCodigoPostal();
				$this->mDomicilio			= $xViv->getDireccionBasica();
				$this->mIDDomicilio			= $xViv->getClaveUnica();
			}
		}
	}
	//nombre o razon social
	//ingreso mensual
	//agregar en aml y perfil
	function add($clave_de_actividad, $ingreso, $antiguedad = DEFAULT_TIEMPO, $nombrecomercial = "", 
			$codigo_postal = 0, $telefono = 0, $idlocalidad = 0, $nombrelocalidad = "", $nombremunicipio = "", $nombreestado = "", $empleoAnterior = false, $descripcion = ""){
		$codigo_postal	= (setNoMenorQueCero($codigo_postal) <= 0) ? $this->mCodigoPostal : $codigo_postal;
		$idlocalidad	= (setNoMenorQueCero($idlocalidad) <= 0) ? $this->mClaveDeLocalidad : $idlocalidad;
		$nombreestado	= ($nombreestado == "") ? $this->mNombreDeEstado : $nombreestado;
		$nombremunicipio= ($nombremunicipio == "") ? $this->mNombreDeMunicipio : $nombremunicipio;
		$nombrelocalidad= ($nombrelocalidad == "") ? $this->mNombreDeLocalidad : $nombrelocalidad;
		$nombrecomercial= ($nombrecomercial == "") ? $this->mNombreEmpresa : $nombrecomercial;
		$telefono		= setNoMenorQueCero($telefono);
		$telefono		= ($telefono <= 0) ? $this->mTelefono : $telefono;
		$ingreso		= setNoMenorQueCero($ingreso);
		$this->mIDDomicilio	= (setNoMenorQueCero($this->mIDDomicilio) <= 0) ? FALLBACK_DOMICILIO : $this->mIDDomicilio;
		$persona		= $this->mPersona;
		$xAE			= new cSocios_aeconomica();
		$xSoc			= new cSocio($persona);
		$success		= false;
		$estatus		= ($empleoAnterior == true) ? $this->ESTADO_ANTERIOR : $this->ESTADO_NOVERIFICADO;
		
		if($codigo_postal > 0){  //revisar efectos
			//setLog("CP CARGADO $codigo_postal");
			$xCP	=  new cPersonasVivCodigosPostales($codigo_postal);
			if($xCP->init() == true){
				$nombreestado		= $xCP->getNombreEstado();
				$nombremunicipio	= $xCP->getNombreMunicipio();
				$nombrelocalidad	= $xCP->getNombreLocalidad();
			}
		}
		//corrige la clave de actividad
			//Trata de buscar una actividad por nombre
			if($clave_de_actividad <= 0 AND ($nombrecomercial !== "" OR $descripcion !== "") ){
				$xCat					= new cPersonaActividadEconomicaCatalogo();
				if($xCat->initByNombreDescribe($nombrecomercial, $descripcion) == true){
					$clave_de_actividad	= $xCat->getCodigoUIF();
				}
			}
			//Estable a default
			$clave_de_actividad			= ($clave_de_actividad <= 0) ? FALLBACK_ACTIVIDAD_ECONOMICA : $clave_de_actividad;
		//
		$xAE->ae_clave_de_localidad($idlocalidad);
		$xAE->ae_codigo_postal($codigo_postal);
		$xAE->antiguedad_ae($antiguedad);
		$xAE->departamento_ae($this->mDepto);
		$xAE->dependencia_ae($this->mClaveEmpresa);
		$xAE->domicilio_ae($this->mDomicilio);
		$xAE->domicilio_vinculado($this->mIDDomicilio);
		$xAE->estado_actual($estatus);	//99= No verificado
		$xAE->estado_ae($nombreestado);
		$xAE->extension_ae($this->mExtTelefonica);
		$xAE->telefono_ae($telefono);
		$xAE->fecha_alta(fechasys());
		$xAE->fecha_de_verificacion(fechasys());
		$xAE->fecha_de_ingreso($this->mFechaDeIngreso);
		$xAE->idusuario(getUsuarioActual());
		$xAE->localidad_ae($nombrelocalidad);
		$xAE->monto_percibido_ae($ingreso);
		$xAE->municipio_ae($nombremunicipio);
		$xAE->nombre_ae($nombrecomercial);
		$xAE->numero_de_seguridad_social($this->mNSS);
		$xAE->numero_empleado($this->mIDEmpleado);
		$xAE->oficial_de_verificacion(getUsuarioActual());
		$xAE->puesto($this->mPuesto);
		$xAE->sector_economico(FALLBACK_SECTOR_ECONOMICO);
		$xAE->socio_aeconomica($this->mPersona);
		$xAE->sucursal(getSucursal());
		$xAE->telefono_ae($telefono);
		$xAE->tipo_aeconomica($clave_de_actividad);
		$xAE->descripcion($descripcion);
		
		$this->mClaveDeActAML	= $clave_de_actividad;
		
		if($xSoc->init() == true){
			$id			= $xAE->query()->getLastID();
			$xAE->idsocios_aeconomica($id);
			//$clave_de_actividad != FALLBACK_ACTIVIDAD_ECONOMICA AND
			if( $ingreso > 0){
				$idx					= $xAE->query()->insert()->save();
				//setLog($xAE->query()->insert()->get());
				
				if($idx !== false){
					$this->mIDCargado	= $id; 
					$success 			= true;
					$this->mMessages	.= "OK\t$persona\tSe agrego con exito la ACTIVIDAD ECONOMICA $clave_de_actividad ($idx) \r\n";
					//actualizar la persona a la dependencia o empresa
					if($this->mClaveEmpresa != FALLBACK_CLAVE_EMPRESA AND PERSONAS_CONTROLAR_POR_EMPRESA == true ){
						$xSoc->setResetEmpresa($this->mClaveEmpresa);
						$this->mMessages	.= "OK\t$persona\tSe Actualiza la empresa relacionada a " . $this->mClaveEmpresa . "\r\n";
					}
					if(MODULO_AML_ACTIVADO == true){
						$xAML	= new cAMLPersonas($persona);
						if($xAML->init($persona) == true){
							$xAML->setAnalizarNivelDeRiesgo(true);
						}
					}
					$xSoc->setCuandoSeActualiza();
				} else {
					$this->mMessages		.= "ERROR\t$persona\tHubo un error al dar de alta a la ACTIVIDAD ECONOMICA\r\n";
				}
			} else {
				$this->mMessages			.= "ERROR\t$persona\tEl Ingreso ($ingreso) debe ser superior a CERO y la Clave ($clave_de_actividad) debe tener un valor valido\r\n";
			}
			//Aviso AML
			
			$this->mMessages		.= $xSoc->getMessages();			
		}
		return $success;
	}	
	function getEsRiesgosa(){
		$sql	= "SELECT * FROM `aml_riesgo_perfiles` WHERE `objeto_de_origen`='personas_actividad_economica_tipos' AND `valor_de_origen`='" . $this->getClaveDeActividad() .  "' LIMIT 0,1";
		$xQL	= new MQL();
		$xCache	= new cCache();
		$idcx	= "aml_riesgo_perfiles-byact-" . $this->getClaveDeActividad();
		$res	= false;
		$media	= 0;
		$this->mAMLNivelRiesgo	= 0;
		if(MODULO_AML_ACTIVADO == true){
			$data	= $xCache->get($idcx);
			if(!is_array($data)){
				$data	= $xQL->getDataRow($sql);
			}
			//Riesgo personalizado
			if(isset($data["nivel_de_riesgo"])){
				$res 					= true;
				$this->mAMLNivelRiesgo	+= setNoMenorQueCero($data["nivel_de_riesgo"]);
				$this->mMessages		.= "WARN\tLa Actividad ". $this->getClaveDeActividad() . " ES " . $this->mAMLNivelRiesgo . " % RIESGOSA Perfil Personalizado\r\n";
				$xCache->set($idcx, $data, $xCache->EXPIRA_UNDIA);
				$media++;
			} else {
				$this->mMessages	.= "OK\tLa Actividad ". $this->getClaveDeActividad() . " No se encontro en el catalogo de perfil personalizado\r\n";
				$idcx2				= "personas_actividad_economica_tipos-byact-" . $this->getClaveDeActividad();
				
				$sql2				= "SELECT `nivel_de_riesgo` FROM `personas_actividad_economica_tipos` WHERE `clave_de_actividad` = '" . $this->getClaveDeActividad() .  "' OR `clave_interna`='" . $this->getClaveDeActividad() .  "'  LIMIT 0,1";
				$data2				= $xCache->get($idcx2);
				if(!is_array($data2)){
					$data2			= $xQL->getDataRow($sql2);
				}
				if(isset($data2["nivel_de_riesgo"])){
					$this->mAMLNivelRiesgo	+= setNoMenorQueCero($data2["nivel_de_riesgo"]);
					$this->mMessages		.= "WARN\tLa Actividad ". $this->getClaveDeActividad() . " ES " . $this->mAMLNivelRiesgo . " % RIESGOSA Perfil Estandar\r\n";
					$media++;
					$xCache->set($idcx2, $data2, $xCache->EXPIRA_UNDIA);
				}
			}

			//riesgo geografico
			$pais					= $this->getClaveDePais();
			if($pais != EACP_CLAVE_DE_PAIS){
				$xPais					= new cDomiciliosPaises($pais);
				$xPais->init();
				$nivel					= $xPais->getRiesgoAMLAsociado();
				$media++;
				$this->mAMLNivelRiesgo += $this->mAMLNivelRiesgo;
				$this->mMessages		.= "WARN\tEl Pais ". $pais . " en tabla es $nivel, queda en " . $this->mAMLNivelRiesgo . " % RIESGOSA\r\n";
			}
			//riesgo por tipo
			$otipo	= $this->getOTipo();
			if($otipo != null){
				$nivel					= $otipo->getNivelRiesgoAML();
				$media++;
				$this->mAMLNivelRiesgo += $nivel;
				$this->mMessages		.= "WARN\tEl tipo de Actividad ". $this->getClaveDeActividad() . " en tabla es $nivel, queda en " . $this->mAMLNivelRiesgo . " % RIESGOSA\r\n";
				if($otipo->getGeneraPEP() == true){
					$media 					= 1;
					$this->mAMLNivelRiesgo = SYS_RIESGO_ALTO;
					$this->mMessages		.= "WARN\tEl tipo de Actividad ". $this->getClaveDeActividad() . " es PEP, se lleva a ALTO, queda en " . $this->mAMLNivelRiesgo . " % RIESGOSA\r\n";
					$this->mAMLGeneraPEP	= true;
				}
			}
						
			$this->mAMLNivelRiesgo			= ($this->mAMLNivelRiesgo/$media);
			
		}
		return $res;
	}
	function getRiesgoAMLAsociado(){ return $this->mAMLNivelRiesgo;  }
	function getGeneraPEP(){ return $this->mAMLGeneraPEP; }
	function getDepartamento(){ return $this->mDepto; }
	function getTipoPorFecha($fecha, $fecha_final = false){
		$xF		= new cFecha(0, $fecha_final);
		$dias	= $xF->setRestarFechas($fecha_final, $fecha);
		$tiempo	= 99;
		if($dias <= 189){
			$tiempo	= 189;
		} else if ($dias >= 190 AND $dias <= 365){
			$tiempo	= 365;
		} else if ($dias >= 366 AND $dias <= 755){
			$tiempo = 755;
		} else if ($dias >= 756 AND $dias <= 1133){
			$tiempo = 1133;
		} else if ($dias >= 1134 AND $dias <= 1510){
			$tiempo = 1510;
		} else if ($dias >= 1511 AND $dias <= 1888){
			$tiempo = 1888;
		} else if ($dias >= 1889 AND $dias <= 2266){
			$tiempo = 2266;
		} else if ($dias >= 2267 AND $dias <= 2643){
			$tiempo = 2643;
		} else if ($dias >= 2644 AND $dias <= 3021){
			$tiempo = 3021;
		} else if ($dias >= 3022 AND $dias <= 3398){
			$tiempo = 3398;
		} else if ($dias >= 3399 AND $dias <= 3776){
			$tiempo = 3776;
		} else if ($dias >= 3777){
			$tiempo = 9000;
		}
		return $tiempo;
	}
	function InitDatosSCIAN($clave = false){
	
		$clave	= setNoMenorQueCero($clave);
		$clave	= ($clave <= 0) ? $this->mClaveDeActSCIAN : $clave;
		$clave	= setNoMenorQueCero($clave);
		
		if($clave >0){
			$xCache	= new cCache();
			$idc	= "personas_ae_scian-$clave";
			$DD		= $xCache->get($idc);
			if(!is_array($DD)){
				$xQL	= new MQL();
				$DD		= $xQL->getDataRow("SELECT * FROM `personas_ae_scian` WHERE `clave_de_actividad`='$clave' LIMIT 0,1");
			}
			if(isset($DD["clave_interna"])){
				$xSCIAN	= new cPersonas_ae_scian();
				$xSCIAN->setData($DD);
				if($this->mClaveDeActAML <= 0){
					$this->mClaveDeActAML	= $xSCIAN->clave_aml()->v();
				}
				$this->mClaveDeActSCIAN	= $xSCIAN->clave_de_actividad()->v();
				$this->mDescripcion		= $xSCIAN->nombre_de_la_actividad()->v();
				$this->mSectorEconomico	= $xSCIAN->sector()->v();
				$xCache->set($idc, $DD);
				$this->mMessages		.= "WARN\tSe carga SCIAN ID $clave\r\n";
			} else {
				$this->mMessages		.= "ERROR\tError al cargar SCIAN ID $clave\r\n";
			}
		}
	}
	function getClaveActividadAML(){ return $this->mClaveDeActAML; }
	function getClaveActividadSCIAN(){ return $this->mClaveDeActSCIAN; }
	function getDescripcionAct(){return $this->mDescripcion;}
	function getIDDeActividad(){ return $this->mIDCargado; }
	function setActualizarDescripcion($describe){
		
		$describe	= setCadenaVal($describe);
		$this->obj()->descripcion($describe);
		$this->mDescripcion		= $describe;
		$this->obj()->query()->update()->save($this->obj()->idsocios_aeconomica()->v());
	}
	function getAntiguedadEnMeses($Fecha = false){
		$xF		= new cFecha();
		$Fecha	= $xF->getFechaISO($Fecha);
		$dias	= $xF->setRestarFechas($Fecha, $this->getFechaIngreso());
		$mes	= round(($dias/SYS_FACTOR_DIAS_MES),0);
		
		return $mes;
	}
}
class cPersonaCapacidadDePago{
	private $mClaveDePersona	= false;
	function __construct($clave_de_persona){ $this->mClaveDePersona	=	$clave_de_persona;	}
	function getMontoDeCreditoMaximo(){
		$sql	= "SELECT * FROM personas_credito_maximo WHERE persona= " . $this->mClaveDePersona . " LIMIT 0,1";
		return mifila($sql, "credito_maximo");
	}
	function getLimiteDeCredito(){
		return $this->getMontoDeCreditoMaximo();
	}
}
class cPersonaFiguraJuridica {
	private $mClave		= false;
	private $mAgrup		= false;
	function __construct($clave = false){
		$this->mClave		= $clave;
		if(setNoMenorQueCero($clave) > 0 ){
			$this->init();
		}
	}
	function init(){
		$xObj	= new cSocios_figura_juridica();
		$xObj->setData( $xObj->query()->initByID($this->mClave) );
		//$this->mAgrup	= $xObj->persona_fisica()
		$this->mAgrup	= $xObj->tipo_de_integracion()->v();
	}
	function isFisica(){ return ($this->mAgrup == PERSONAS_ES_FISICA) ? true : false;	}
}

class cPersonaActividadEconomicaCatalogo {
	private $mID			= false;
	private $mObj			= null;
	private $mClaveUIF		= "";
	private $mAMLRiesgoN	= 1;
	private $mGeneraPEP		= false;
	private $mNombre		= "";
	private $mMessages		= "";
	private $mIDCache		= "";
	private $mTable			= "";
	private $mInit			= false;
	function __construct($clave = false){
		$this->mID			= setNoMenorQueCero($clave);
		//Clave es clave ID real, no id interna
		if($this->mID > 0){
			$this->setIDCache($this->mID);
			$this->init();
		}
	}
	function init($data = false){
		$xCache	= new cCache();
		$xT		= new cPersonas_actividad_economica_tipos();
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL				= new MQL();
				$this->mClaveUIF	= (setNoMenorQueCero($this->mClaveUIF)<=0) ? $this->mID : $this->mClaveUIF;
				$data				= $xQL->getDataRow("SELECT * FROM `" . $xT->get() . "` WHERE `" . $xT->getKey() . "`=". $this->mID . " OR `clave_de_actividad`='". $this->mClaveUIF . "' LIMIT 0,1");
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			$this->mObj			= $xT; //Cambiar
			$this->mClave		= $data[$xT->getKey()];

			$this->mClaveUIF	= $xT->clave_de_actividad()->v();
			$this->mAMLRiesgoN	= $xT->nivel_de_riesgo()->v();
			$this->mGeneraPEP	= (setNoMenorQueCero($xT->califica_para_pep()->v()) <= 0 )? false : true;
			$this->mNombre		= $xT->nombre_de_la_actividad()->v();
			
			$this->setIDCache($this->mClave);
			$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			$this->mInit	= true;
			$xT 			= null;
		}
		return $this->mInit;
	}
	function getCodigoUIF(){ return $this->mClaveUIF;	}
	function getNivelRiesgoAML(){ return $this->mAMLRiesgoN; }
	function getGeneraPEP(){ return $this->mGeneraPEP; }
	function getNombre(){ return $this->mNombre; }
	function initByNombreDescribe($nombre = "", $describe = ""){
		$sql		= "SELECT * FROM `personas_actividad_economica_tipos` WHERE ";
		if($nombre !== "" AND $describe !== ""){
			$sql 	.= " ((`nombre_de_la_actividad` LIKE '%$nombre%') OR (`descripcion_detallada` LIKE '%$nombre%')) OR ((`nombre_de_la_actividad` LIKE '%$describe%') OR (`descripcion_detallada` LIKE '%$describe%')) ";
			
		} else {
			$param	= ($nombre == "") ? $describe : $nombre;
			$sql 	.= " ((`nombre_de_la_actividad` LIKE '%$param%') OR (`descripcion_detallada` LIKE '%$param%')) ";
		}
		$sql		.= " LIMIT 0,1";
		$xQL		= new MQL();
		$DD			= $xQL->getDataRow($sql);
		if(isset($DD["clave_interna"])){
			$this->mID		= $DD["clave_interna"];
			$this->mClaveUIF= $DD["clave_de_actividad"];
		} else {
			$DD				= false;
		}
		$xQL		= null;
		
		return $this->init($DD);
	}
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= "personas_actividad_economica_tipos-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
}
class cPersonasCatalogoOtrosDatos {
	public $ACTIVIDAD_PRODUCTOS_PRINCIPALES 	= "ACTIVIDAD_PRODUCTOS_PRINCIPALES";
	public $ACTIVIDAD_INSUMOS_PRINCIPALES 		= "ACTIVIDAD_INSUMOS_PRINCIPALES";
	public $PERSONAS_MONTO_AHORRO_PREFERENTE	= "PERSONAS_MONTO_AHORRO_PREFERENTE";
	public $PERSONAS_ES_EXTRANJERO				= "PERSONAS_ES_EXTRANJERO";
	public $AML_OPERA_EN_PARAISOS_FISCALES		= "AML_OPERA_EN_PARAISOS_FISCALES";
	public $AML_RAZONES_DE_OP_EN_PARAISOS		= "AML_RAZONES_DE_OP_EN_PARAISOS";
	public $RAZONES_DE_FALTA_DE_FIEL			= "RAZONES_DE_FALTA_DE_FIEL";
	public $AML_PEP_PRINCIPAL					= "AML_PEP_PRINCIPAL";
	
	
	public $AML_PEP_CONSANGUINIDAD				= "AML_PEP_POR_CONSANGUINIDAD";
	public $AML_PEP_AFINIDAD					= "AML_PEP_POR_AFINIDAD";
	public $AML_PEP_VINCULO_ECONOM				= "AML_PEP_POR_VINCULO_ECONOM";
}


/**
 * Clase de Grupos Solidarios
 * @version 1.1
 * @author Balam Gonzalez Luis Humberto
 * @package common
 * @subpackage core
 */
class cGrupo{
	private $mNombre			= "";
	private $mCodigo			= false;
	private $mRepSocio			= false;
	private $mGrupoDom			= false;
	private $mRepNom			= false;
	private $mVocalSocio		= false;
	private $mVocalNom			= false;
	private $mVocalDom			= false;
	private $mSucursal			= "";
	private $mClaveDePersona	= false;

	private $mNivelActual		= false;
	private $mNivelAnterior		= false;
	private $mNivelProximo		= false;

	private $mMessages			= "";
	private $mDatosInArray		= array();
	private $mGrupoIniciado		= false;
	private $mOPlaneacion		= null;
	private $mDatosReciboPlan	= array();
	private $mReciboPlan		= false;
	private $mObjPersona		= null;
	function __construct($codigo_de_grupo, $iniciar = true){ $this->mCodigo		= $codigo_de_grupo; if ( $iniciar  == true AND setNoMenorQueCero($codigo_de_grupo) > 0 ){ $this->init(); }	}
	function getDescripcion(){
		$desc	= "";
		if($this->mGrupoIniciado == true){ $desc	= $this->mNombre . "-" . $this->mRepNom . "-" . $this->mVocalNom ; }
		return $desc;
	}
	function init($D = false){

		$sqlG 	= "SELECT * FROM socios_grupossolidarios	WHERE idsocios_grupossolidarios = " . $this->mCodigo . " LIMIT 0,1";
		if ( ($D == false) OR ( !is_array($D) ) ){
			$D		= obten_filas($sqlG);
		}
		if(isset($D["nombre_gruposolidario"])){
			$this->mNombre		= $D["nombre_gruposolidario"];
			$this->mRepSocio	= $D["representante_numerosocio"];
			$this->mRepNom		= $D["representante_nombrecompleto"];
			$this->mGrupoDom	= $D["direccion_gruposolidario"];
			$this->mVocalNom	= $D["vocalvigilancia_nombrecompleto"];
			$this->mVocalSocio	= $D["vocalvigilancia_numerosocio"];
			$this->mNivelActual		= $D["nivel_ministracion"];
			$this->mSucursal		= $D["sucursal"];
			$this->mClaveDePersona	= $D["clave_de_persona"];
			//Presume los Niveles de Trabajo
			if ( $this->mNivelActual > 1){
				$this->mNivelAnterior	= $this->mNivelActual - 1;
			} else {
				$this->mNivelAnterior	= 1;
			}
			$this->mNivelProximo		= $this->mNivelActual + 1;
			$this->mDatosInArray		= $D;

			$this->mGrupoIniciado		= true;
			unset($D);
		} else {
			$this->mGrupoIniciado		= false;
			$this->mMessages			.= "ERROR\tError al cargar el grupo " . $this->mCodigo . " \r\n";
		}
		return $this->mGrupoIniciado;
	}
	function getDatosDelCreditoGrupalInArray(){
		//busca el ultimo  credito otorgado por
		$sql = "SELECT
					`creditos_solicitud`.*,
					`creditos_tipoconvenio`.*
				FROM
					`creditos_solicitud` `creditos_solicitud`
						INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio`
						ON `creditos_solicitud`.`tipo_convenio` = `creditos_tipoconvenio`.
						`idcreditos_tipoconvenio`
				WHERE
					(`creditos_tipoconvenio`.`tipo_en_sistema` = " . CREDITO_PRODUCTO_GRUPOS . ")
					AND
					(`creditos_solicitud`.`numero_socio` =" . $this->mRepSocio . ")
					AND
					(`creditos_solicitud`.`saldo_actual` > " . TOLERANCIA_SALDOS . ")
				ORDER BY
					`creditos_solicitud`.`fecha_ministracion` DESC
				LIMIT 0,1";
		$in	= obten_filas($sql);
		return $in;
	}
	
	/**
	 * Returna el Nombre del Grupo
	 *
	 * @return string Nombre del Grupo
	 */
	function getNombre(){		return $this->mNombre;	}
	function getNombreRepresentante(){		return $this->mRepNom;	}
	function getNombreVocal(){		return $this->mVocalNom;	}
	function getRepresentanteCodigo(){		return  $this->mRepSocio;	}
	function getDatosNivelActual(){
		$arrD	= array();
		$sql 	= "SELECT * FROM creditos_nivelesdegrupo WHERE nivel=" . $this->mNivelActual;
		$d		= obten_filas($sql);
		$arrD["monto"] = $d["monto_xintegrante"];
		return $arrD;

	}
	function getDatosNivelProximo(){
		$arrD	= array();
		$sql 	= "SELECT * FROM creditos_nivelesdegrupo WHERE nivel=" . $this->mNivelProximo;
		$d		= obten_filas($sql);
		$arrD["monto"] = $d["monto_xintegrante"];
		return $arrD;
	}
	function getDatosDePlaneacionInArray(){
		$in		= array();
		$xQL	= new cSQLListas();
		
		$sql 	=  "SELECT
					`operaciones_recibos`.*, `operaciones_recibostipo`.*, DATEDIFF(CURDATE(), `operaciones_recibos`.`fecha_operacion`) AS `antiguedad`
				FROM
					`operaciones_recibos` `operaciones_recibos`
						INNER JOIN `operaciones_recibostipo` `operaciones_recibostipo`
						ON `operaciones_recibos`.`tipo_docto` = `operaciones_recibostipo`.`idoperaciones_recibostipo`
				WHERE
					/* Tipo de Docto 14: Es Planeacion de Creditos Grupales */
					(`operaciones_recibos`.`tipo_docto` = 14)
					AND
					(`operaciones_recibos`.`numero_socio` =" . $this->mRepSocio . ")
					AND
					(`operaciones_recibos`.`grupo_asociado` =" . $this->mCodigo . ")

					HAVING antiguedad <= "  . DIAS_ESPERA_CREDITO .  "";
		$in			= obten_filas($sql);
		if(!isset($in["idoperaciones_recibos"])){
			$this->mMessages	.= "ERROR\tNo hay planeacion que cargar\r\n";
		} else {
			$this->mReciboPlan		= $in["idoperaciones_recibos"];
			$this->mDatosReciboPlan	= $in;
		}
		return $in;
	}
	function getNumeroDeReciboDePlan(){
		if($this->mReciboPlan == false){ $this->getDatosDePlaneacionInArray(); }
		return $this->mReciboPlan;
	}
	function getFicha($Fieldset = false, $HTML_tools = ""){

		$vKey 		= $this->mCodigo;
		$nombreg 	= $this->mNombre;
		$direccion 	= $this->mGrupoDom;
		$repre 		= $this->mRepNom;
		$vocal 		= $this->mVocalNom;
		$nsrepre 	= $this->mRepSocio;
		$nsvocal 	= $this->mVocalSocio;
		$tool 		= $HTML_tools;
		$wTable		= "";

		$exoFicha =  "
		<table $wTable border='0'>
		<tbody>
		<tr>
		<th class='izq'>Numero de Grupo</th>
		<td>$vKey</td>
		<th class='izq'>Nombre del Grupo</th>
		<td>$nombreg</td>
		</tr>
		<tr>
		<th class='izq'>Domicilio</th>
		<td colspan='3'>$direccion</td>
		</tr>
		<tr>
		<th class='izq'>Representante</th>
		<td>$nsrepre</td><td colspan='2'>$repre</td>
		</tr>
		<tr>
		<th class='izq'>Vocal de Vigilancia</th>
		<td>$nsvocal</td>
		<td colspan='2'>$vocal</td>
		</tr>
		$tool
		</tbody>
		</table>";
		if ($Fieldset == true){
			$exoFicha = "<fieldset><legend>&nbsp;&nbsp;&nbsp;INFORMACI&Oacute;N DEL GRUPO&nbsp;&nbsp;&nbsp;</legend>$exoFicha</fieldset>";
		}
		return $exoFicha;
	}
	function setUpdate($aParam){

		$sqlBody	= "";

		$BodyUpdate = "";
		foreach ($aParam as $key => $value) {
			if ( is_string($value) ){
				$value	= "\"" . $value . "\"";
			}
			if ($BodyUpdate == ""){
				$BodyUpdate .= "$key = $value ";
			} else {
				$BodyUpdate .= ", $key = $value ";
			}
		}	//END FOREACH
		$sqlBody	= "UPDATE socios_grupossolidarios
		SET $BodyUpdate
		WHERE
		(`socios_grupossolidarios`.`idsocios_grupossolidarios` =" . $this->mCodigo . ")
		";
		//$x = my_query($sqlBody);
		$xQL	= new MQL();
		$res	= $xQL->setRawQuery($sqlBody);

		return ($res === false) ? false : true;
	}
	/**
	* Funcion para agregar un Nuevo Grupo
	* @param string $nombre				Nombre del Grupo Solidario
	* @param string $direccion				Direccion Completa, Iniciando con Calle + Numero del Grupo Solidario
	* @param string $representante			Numero de Socio de la Representante de Grupo
	* @param string $vocal_de_vigilancia	Numero de Socio de la Vocal de Vigilancia
	* @param string $estatus				Estatus Actual del Grupo
	* @param string $nivel					Nivel de Ministracion de Credito del Grupo
	* @param string $numero				Numero que tendra el Grupo Solidario
	*/
	function add($nombre, $direccion = "", $representante = false, $vocal_de_vigilancia = false, $estatus = 10, $nivel = 1, $numero = false, $sucursal = false, $fecha = false, $clave_de_persona = false ){
		$xLoc		= new cLocal();
		$xF			= new cFecha();
		$numero		= false;
		//codigo postal
		$colonia	= $xLoc->DomicilioCodigoPostal();
		
		$numero				= setNoMenorQueCero($numero) <= 0 ? $this->getLast() : $numero;
		$fecha				= ($fecha == false) ? fechasys() : $xF->getFechaISO($fecha);
		$sucursal			= ($sucursal == false) ? getSucursal() : $sucursal;
		$clave_de_persona	= (setNoMenorQueCero($clave_de_persona) <= 0) ? DEFAULT_SOCIO : $clave_de_persona;
		if(setNoMenorQueCero($clave_de_persona) > DEFAULT_SOCIO){
			$numero			= $clave_de_persona;
		}
		if ( setNoMenorQueCero($representante) <= DEFAULT_SOCIO ){
			$representante	= DEFAULT_SOCIO;
			$NombreRep		= "POR_REGISTRAR";
		} else {
			$xRep		= new cSocio($representante);
			if( $xRep->init() == true){
				$NombreRep	= $xRep->getNombreCompleto();
				if ( $direccion == "" ){
					if($xRep->getODomicilio() != null){
						$colonia = $xRep->getODomicilio()->getCodigoPostal();
						$direccion	= $xRep->getDomicilio();
					}
				}
			}
		}
		if ( setNoMenorQueCero($vocal_de_vigilancia) <= DEFAULT_SOCIO ){
			$vocal_de_vigilancia	= DEFAULT_SOCIO;
			$NombreVoc				= "POR_REGISTRAR";
		} else {
			$xVoc		= new cSocio($vocal_de_vigilancia);
			$xVoc->init();
			$NombreVoc	= $xVoc->getNombreCompleto();
		}
		$sql	= "INSERT INTO socios_grupossolidarios
					(idsocios_grupossolidarios, nombre_gruposolidario, colonia_gruposolidario, direccion_gruposolidario,
					representante_numerosocio, representante_nombrecompleto, grupo_solidario,
					vocalvigilancia_numerosocio, vocalvigilancia_nombrecompleto,
					estatusactual,
					nivel_ministracion,
					sucursal,
					fecha_de_alta, clave_de_persona)
					VALUES
					($numero, '$nombre', '$colonia', '$direccion', $representante, '$NombreRep', $numero, $vocal_de_vigilancia, '$NombreVoc',
					$estatus, $nivel, '$sucursal', '$fecha', $clave_de_persona)";
					$x	= my_query($sql);
		if($x[SYS_ESTADO] == true){
			$this->mCodigo		= $numero;
			$this->init(false);
		}
		return $x[SYS_ESTADO];
	}
					function getLast(){
					$sqlgp 	= "SELECT MAX(idsocios_grupossolidarios) AS  'maxid' FROM socios_grupossolidarios";
		$numero = mifila($sqlgp, "maxid") + 1;
		return $numero;
					}
	/**
	* funcion que ejecuta una validacion de los grupos
	* */
	function setVerificarValidez($GenerarAvisos = false, $CorreccionAutomatica = false){
		$D				= $this->mDatosInArray;
		$msg			= getSucursal();
		$presidenta		= $this->mRepSocio;
		$vocal			= $this->mVocalSocio;
		$tmpSucursal	= "";

		$arrUpdate		= array();
		//$DGrupo
		if ( $presidenta == DEFAULT_SOCIO ){		$msg	.= "ERROR\tLa Presidenta del Grupo tiene un Numero Invalido\r\n";		}
		if ( $vocal == DEFAULT_SOCIO ){		$msg	.= "ERROR\tLa Vocal del Grupo tiene un Numero Invalido\r\n";		}
		//Verificar si la Presidenta existe
		if ( $presidenta != DEFAULT_SOCIO ){
			$xPred			= new cSocio($presidenta, true);
			$xPred->init();
			$DPred			= $xPred->getDatosInArray();
			$nombre			= trim( $xPred->getNombreCompleto() );
			$tmpSucursal	= $DPred["sucursal"];
			if ( !isset($tmpSucursal) ){		$tmpSucursal	= getSucursal();		}
			if ( $nombre == "" ){		
				$msg	.= "ERROR\tLa Presidenta del Grupo no Existe\r\n";		
			} else {
				if ( $CorreccionAutomatica == true ){
					$arrUpdate["representante_nombrecompleto"]	= $nombre;
					$msg		.= "ACTUAL\tLa Presidenta del Grupo se actualiza a $nombre \r\n";
					//Actualiza la Colonia a Codigo Postal
					$xDom		= $xPred->getDatosDomicilio();
				if (!isset($xDom["codigo_postal"]) OR empty($xDom["codigo_postal"]) ){ 	$xDom["codigo_postal"] = DEFAULT_CODIGO_POSTAL; }
					$arrUpdate["colonia_gruposolidario"]	= $xDom["codigo_postal"];
					$arrUpdate["direccion_gruposolidario"]	= trim($xPred->getDomicilio());
					$msg		.= "ACTUAL\tEl Codigo Postal del Grupo se actualiza a " . $xDom["codigo_postal"] . " \r\n";
				}
			}
		}
		//Verificar si la Vocal de Vigilancia Existe
		if ( $vocal != DEFAULT_SOCIO ){
			$xVocal	= new cSocio($vocal, true);
			$xVocal->init();
			$nombre	= trim( $xVocal->getNombreCompleto() );
			if ( $nombre == "" ){
				$msg	.= "ERROR\tLa Vocal de Vigilancia del Grupo no Existe\r\n";
			} else {
				if ( $CorreccionAutomatica == true ){
					$arrUpdate["vocalvigilancia_nombrecompleto"]	= $nombre;
					$msg	.= "ERROR\tLa Vocal de Vigilancia del Grupo se actualiza a $nombre\r\n";
				}
			}
		}
		//Verificar si la Sucursal actual
		if ( ($this->mSucursal != $tmpSucursal) AND ( $vocal != DEFAULT_SOCIO ) ){
			$msg	.= "ERROR\tLa Sucursal del Grupo(" . $this->mSucursal . ") no es el mismo que el de la Presidenta del Grupo($tmpSucursal)\r\n";
			if ( $CorreccionAutomatica == true ){ $arrUpdate["sucursal"]	= $tmpSucursal; }
		}
		//Actualizar
		if ( $CorreccionAutomatica == true ){	$this->setUpdate($arrUpdate);	}
		return $msg;
	}

	function existe($grupo = false){
		$grupo	= ( $grupo == false) ? $this->mCodigo : $grupo;
		$existentes	= 0;
		if ( isset($grupo) AND ($grupo != false) ){
			$sql		= "SELECT COUNT(idsocios_grupossolidarios) AS 'existentes' FROM socios_grupossolidarios WHERE idsocios_grupossolidarios = $grupo";
			$existentes	= mifila($sql, "existentes");
		}
		return		($existentes == 0 ) ? false : true;
	}
	function getClaveDePersona(){ return $this->mClaveDePersona; }
	function getCodigo(){ return $this->mCodigo; }
	function setActualizarPlaneacion($fecha, $persona, $credito){
		/**
		 * Neutraliza el Recibo de Planeacion por socio
		 * Neutraliza las Operaciones de Planeacion por Grupo
		 */
		$xF						= new cFecha();
		$fecha_esperar_hasta 	= $xF->setRestarDias( DIAS_ESPERA_CREDITO, $fecha );
		$grupo					= $this->getCodigo();
		$sqlURec 				= "UPDATE operaciones_recibos set docto_afectado=$credito WHERE numero_socio=$persona AND tipo_docto=14		AND fecha_operacion>='$fecha_esperar_hasta' ";
		$sqlUMvto 				= "UPDATE operaciones_mvtos set docto_afectado=$credito WHERE grupo_asociado=$grupo AND tipo_operacion=112 AND fecha_operacion>='$fecha_esperar_hasta'";
		my_query($sqlURec);
		my_query($sqlUMvto);
	}
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function addIntegrante($clave_de_persona){
		$xRel	= new cPersonasRelaciones(false, $this->mClaveDePersona);
		$xRel->addRelacion($clave_de_persona, GRUPO_CLAVE_INTEGRANTE);
		$this->mMessages	.= $xRel->getMessages();
	}
	function setRepresentante($clave_de_persona){
		$xLoc		= new cLocal();
		if(setNoMenorQueCero($clave_de_persona) > DEFAULT_SOCIO){
			$xPer	= new cSocio($clave_de_persona);
			if($xPer->init() == true){
				$codigo	= $xPer->getCodigo();
				$nombre	= $xPer->getNombreCompleto();
				$dom	= "";
				$cp		= $xLoc->DomicilioCodigoPostal();
				if($xPer->getODomicilio() != null){
					$dom	= $xPer->getODomicilio()->getDireccionBasica();
					$cp		= $xPer->getODomicilio()->getCodigoPostal();
				}
				$this->setUpdate(array(
						"colonia_gruposolidario" => $cp,
						"direccion_gruposolidario" => $dom,
						"representante_numerosocio" => $codigo,
						"representante_nombrecompleto" => $nombre
				));				
				$this->addIntegrante($clave_de_persona);
			}			
		}
	}
	function setVocal($clave_de_persona){
		if(setNoMenorQueCero($clave_de_persona) > DEFAULT_SOCIO){
			$xPer	= new cSocio($clave_de_persona);
			if($xPer->init() == true){ 
				$codigo	= $xPer->getCodigo();
				$nombre	= $xPer->getNombreCompleto();
				$this->setUpdate(array(
						"vocalvigilancia_numerosocio" => $codigo,
						"vocalvigilancia_nombrecompleto" => $nombre
				));
				$this->addIntegrante($clave_de_persona);
			}
		}
	}
	function getOPersona(){
		if($this->mObjPersona == null){
			$xSoc	= new cSocio($this->mClaveDePersona);
			if( $xSoc->init($this->mClaveDePersona) == true ){
				$this->mMessages	.= "OK\tPersona Cargada con el ID " . $xSoc->getCodigo() . "\r\n";
				$this->mObjPersona	= $xSoc;
			} else {
				$this->mMessages	.= "ERROR\tAl cargar persona con el ID " . $this->mClaveDePersona . "\r\n";
			}
		}
		return $this->mObjPersona;
	}	
	function setActualizarPorPersona(){
		$OPersona	= $this->getOPersona();
		$res		= false;
		if($OPersona != null){
			$xGrp	= new cSocios_grupossolidarios();
			$xGrp->setData( $xGrp->query()->initByID($this->mCodigo) );
			
			$ODom	= $OPersona->getODomicilio();
			if($ODom != null){
				$xGrp->direccion_gruposolidario( $ODom->getDireccionBasica() );
				$xGrp->colonia_gruposolidario( $ODom->getColonia() );
			}
			$rs	= $xGrp->query()->update()->save($this->mCodigo);
			if($rs == false){
				$this->mMessages	.= "ERROR\tAl Actualizar el Grupo con ID " . $this->mCodigo . "\r\n";
			} else {
				$res					= true;
			}
		}
		return $res;
	}	
}
/**
 * @deprecated @since 2015.03.01
 */
class cPeriodoDeEmpresa extends cEmpresas_operaciones {
	function getCobrados(){
		$id		= setNoMenorQueCero( $this->idempresas_operaciones()->v());
		$ql		= new MQL();
		$sql	= "SELECT COUNT(`idempresas_cobranza`) AS 'cobrados' FROM `empresas_cobranza` WHERE `clave_de_nomina` = $id AND `estado`=0";
		$datos	= $ql->getDataRow($sql);
		return (isset($datos["cobrados"])) ? setNoMenorQueCero($datos["cobrados"]) : 0;
	}
}

class cPersonasVivCodigosPostales {
	private $mClave				= false;
	private $mObj				= null;
	private $mInit				= false;
	private $mNombre			= "";
	private $mMessages			= "";
	private $mClaveDeEstadoABC	= "";
	private $mClaveDeEstadoSIC	= "";
	private $mClaveDeEstado		= 0;
	private $mClaveDeMunicipio	= 0;
	private $mNombreEstado		= "";
	private $mNombreMunicipio	= "";
	private $mNombreLocalidad	= "";
	private $mClaveDeLocal		= "";
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); }
	function init($data = false){
		if(!is_array($data)){
			$xCache	= new cCache();
			$data	= $xCache->get("cp-activos-". $this->mClave);
			if($data == null){
				$xQL	= new MQL();
				
				$data	= $xQL->getDataRow("SELECT * FROM `tmp_colonias_activas` WHERE `codigo_postal`=". $this->mClave);
				$xCache->set("cp-activos-". $this->mClave, $data);
			}
		}
		if(isset($data["codigo_postal"])){
			$this->mObj		= new cTmp_colonias_activas(); //Cambiar
			
			$this->mObj->setData($data);
			$this->mNombre			= $this->mObj->nombre()->v();
			$this->mClave			= $this->mObj->codigo_postal()->v();
			$this->mClaveDeEstado	= $this->mObj->codigo_de_estado()->v();
			$this->mClaveDeEstadoABC= $this->mObj->clave_alfanumerica()->v();
			$this->mClaveDeEstadoSIC= $this->mObj->clave_en_sic()->v();
			$this->mClaveDeMunicipio= $this->mObj->codigo_de_municipio()->v();
			$this->mNombreEstado	= $this->mObj->nombre_estado()->v();
			$this->mNombreLocalidad	= $this->mObj->nombre_localidad()->v();
			$this->mNombreMunicipio	= $this->mObj->nombre_municipio()->v();
			$this->mClaveDeLocal	= $this->mObj->idlocalidad()->v();
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
	function getClaveDeEstadoEnSIC(){ return $this->mClaveDeEstadoSIC; 	}
	function getClaveDeEstadoABC(){ return $this->mClaveDeEstadoABC;	}
	function getClaveDeEstado(){ return  $this->mClaveDeEstado;	}
	function getClaveDeMunicipio(){ return  $this->mClaveDeMunicipio;	}
	function getClaveDeLocalidad(){ return $this->mClaveDeLocal; }
	function getNombreEstado(){	return $this->mNombreEstado;	}
	function getNombreMunicipio(){	return $this->mNombreMunicipio;	}
	function getNombreLocalidad(){	return $this->mNombreLocalidad;	}
	
}

?>