<?php
/**
 * Core de Seguridad
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package security
 * @subpackage common
*/
include_once("core.config.inc.php");
include_once("core.deprecated.inc.php");
include_once("entidad.datos.php");

include_once("core.common.inc.php");
include_once("core.html.inc.php");
include_once("core.fechas.inc.php");
include_once("core.db.inc.php");

@include_once("../libs/sql.inc.php");
@include_once("../libs/aes.inc.php");

//=====================================================================================================
class cSystemUser{
	private $mCodeUser 			= false;
	private $mTypeById			= true;
	private $mMessages			= "";
	private $mNivelRules		= array(); 
	private $mUserOptions		= array();
	private $mDatosInArray		= array(); 
	private $mUserIniciado		= false;
	private $mNivel				= 0;
	private $mClaveDePersona	= 0;
	private $mID				= false; 
	private $mPWD				= "";
	private $mCuentaDeCaja		= CUENTA_DE_CUADRE;
	private $mData				= null;
	public $FPASSWORD			= "f_34023acbff254d34664f94c3e08d836e";
	public $FNAME				= "f_28fb96d57b21090705cfdf8bc3445d2a";
	public $FNIVEL				= "f_f2cd801e90b78ef4dc673a4659c1482d";
	private $mEstado			= "";
	private $mNombreUser		= "";
	private $mAlias				= "";
	private $USR_ACTIVO			= "activo";
	private $USR_BAJA			= "baja";
	private $mReglasAsInit		= false;
	private $mFechaExpira		= false;
	private $mIndexPage			= "index.xul.php";
	private $mTasksPage			= "utils/frm_calendar_tasks.php";
	private $mIDCache			= "";
	private $mUserType			= 0; //Tipo de Usuario en el Sistema
	private $mCorporativo		= false;
	private $mSucursal			= "";
	private $mPuesto			= "";
	private $mCorreoElectronico	= "";
	private $mNombreCompleto	= "";
	private $mOptions			= "";
	private $mOListaRules		= null;
	//private $mIDCacheReglaN		= "";
	//private $mClaveUser			= "";//
	/**
	 * Inicia la Clase
	 * @param mixed $UserCode
	 * @param boolean $TypeById	Iniciar por Numero de ID / false: Iniciar por Nombre del usuario
	 */
	function __construct($UserCode = false, $IniciarPorID = true){
		//$UserCode		= setNoMenorQueCero($UserCode);
		if ($UserCode === false ){
			$UserCode			= getUsuarioActual();
			$IniciarPorID		= true;
		}
		$this->mCodeUser	= $UserCode;
		$this->mTypeById	= $IniciarPorID;
		$this->mEstado		= SYS_USER_ESTADO_BAJA;
		$this->mOListaRules	= new cSystemUserRulesList();
		if($UserCode !== false ){ $this->init(); }
	}
	function getPuedeEditarUsuarios(){
		return $this->getReglaDeUsuario($this->mOListaRules->PUEDE_EDITAR_USUARIOS);		
	}
	function getPuedeAgregarUsuarios(){
		return $this->getReglaDeUsuario($this->mOListaRules->PUEDE_AGREGAR_USUARIOS);
	}
	function getPuedeEliminarRecibos(){
		return $this->getReglaDeUsuario($this->mOListaRules->PUEDE_ELIMINAR_RECS);
	}
	function getPuedeEditarRecibos(){
		return $this->getReglaDeUsuario($this->mOListaRules->PUEDE_ELIMINAR_RECS);
	}
	function getPuedeUsarPrintPOS(){
		return $this->getReglaDeUsuario($this->mOListaRules->ACTIVE_PRINTER_POS);
	}
	function getPuedeCerrarCajas(){
		if($this->getTipoEnSistema() == USUARIO_TIPO_JEFECAJA){
			return true;
		}
		return $this->getReglaDeUsuario($this->mOListaRules->PUEDE_CERRAR_CAJAS);
	}
	function getPuedeCobrar(){
		$res	= false;
		if($this->getTipoEnSistema() == USUARIO_TIPO_JEFECAJA OR $this->getTipoEnSistema() == USUARIO_TIPO_CAJERO){
			return true;
		}
		return $res;
	}
	function getPuedeOperarCreditos(){
		$res	= false;
		if($this->getTipoEnSistema() == USUARIO_TIPO_OFICIAL_CRED OR $this->getTipoEnSistema() == USUARIO_TIPO_ORIGINADOR){
			return true;
		}
		return $res;
	}
	private function getReglaDeUsuario($regla = ""){
		//$this->mOListaRules
		if($this->mReglasAsInit == false){  $this->getUserRules(); }
		$xT		= new cTipos();
		$arr	= array_merge($this->mNivelRules, $this->mUserOptions);
		$regla	= strtoupper($regla);
		return (isset($arr[$regla])) ? $xT->cBool($arr[$regla]) : false;
	}
	/**
	 * Obtiene en un array las reglas de Nivel Aplicadas
	 */
	function getUserRules(){
		if($this->mUserIniciado == false){ $this->init(); }
		$rules 	= array();
		$dats 	= $this->getDataByNivel();
		$mRules = explode(";", $dats["rules_by_user"]);
		foreach ($mRules as $idx => $regla){
			$regla				= explode("=", $regla);
			$idregla			= (isset($regla[0])) ? $regla[0] : "";
			$valor				= (isset($regla[1])) ? $regla[1] : "";
			if($idregla !== ""){
				$rules[$idregla]= trim($valor);
			}
		}
		$this->mReglasAsInit	= true;
		$this->mNivelRules		= $rules;
		return $rules;
	}
	private function getDataByNivel(){
		//if($this->mUserIniciado == false){ $this->init(); }
		$data		= array();
		$idcache	= "general_niveles-". $this->mNivel;
		$xCache		= new cCache();
		$data		= $xCache->get($idcache);
		if(!is_array($data)){
			$xQL	= new MQL();
			$data	= $xQL->getDataRow("SELECT * FROM `general_niveles` WHERE `idgeneral_niveles`=" . $this->mNivel . " LIMIT 0,1");
			
			if(isset($data["idgeneral_niveles"])){
				$xCache->set($idcache, $data, $xCache->EXPIRA_UNDIA);
			}
		}
		if(isset($data["initpage"])){
			$this->mIndexPage	= $data["initpage"];
			$this->mTasksPage	= $data["taskspage"];
			$this->mUserType	= $data["tipo_sistema"];
		}
		return $data;
	}
	function getUserInfo($info = false){
		if($this->mUserIniciado == false){ $this->init(); }
		if( $info === false ){
			$val	=  $this->mDatosInArray;
		} else {
			$val	= (isset($this->mDatosInArray[$info])) ? $this->mDatosInArray[$info] : null;	
		}
		return $val;
	}
	function getFicha(){
		if($this->mUserIniciado == false){ $this->init(); }
		$xP		= new cGeneral_niveles();
		$xP->setData($xP->query()->initByID($this->getNivel()));
		$tdUniq	= "";
		
		
		if(getUsuarioActual() == $this->getID()){
			$tdUniq = "				<tr>
				<th class='izq'>Clave API</th><td>" . $this->getCTX() . "</td>
				<th class='izq'>Cuenta Contable de Caja</th><td>" . $this->getCuentaContableDeCaja() . "</td>
				</tr>";
		}
		$table = "				
				<table>
				<tr>
				<th class='izq'>Nombre de Usuario / ID</th><td>" . $this->getNombreDeUsuario() . " / " . $this->getID()  . "</td>
				<th class='izq'>Nombre Completo</th><td>" . $this->getNombreCompleto() . "</td>
				</tr>
				<tr>
				<th class='izq'>Nivel</th><td>" . $xP->descripcion_del_nivel()->v() . "</td>
				<th class='izq'>Estado</th><td>" . $this->getEstado() . "</td>
				</tr>
				$tdUniq
				</table>";
		return $table;
	}
	function init(){
		$xCache				= new cCache();
		//$xT					= new cT_03f996214fba4a1d05a68b18fece8e71();
		$val				= false;
		$cFld				= "id";
		$inCache			= true;
		if ($this->mTypeById == false){ $cFld = "nombreusuario";	}
		$this->mIDCache		= "data-user-$cFld-". $this->mCodeUser;
		$D					= $xCache->get($this->mIDCache);
		if($D === null){
			$sql 			= "SELECT * FROM usuarios WHERE $cFld = '" . $this->mCodeUser . "' LIMIT 0,1";
			$D				= obten_filas($sql);
			$inCache		= false;
		}
		if(isset($D["niveldeacceso"])){
			$this->mDatosInArray	= $D;
			$this->mNivel			= $D["niveldeacceso"];
			$this->mClaveDePersona	= setNoMenorQueCero($D["codigo_de_persona"]);
			$this->mID				= $D["idusuarios"];
			$this->mPWD				= $D["contrasenna"];
			$this->mNombreUser		= $D["nombreusuario"];
			$this->mAlias			= $D["alias"];
			$this->mEstado			= $D["estatus"];
			$this->mCuentaDeCaja	= setNoMenorQueCero($D["cuenta_contable_de_caja"]) <= 0 ? CUENTA_DE_CUADRE : $D["cuenta_contable_de_caja"];
			$this->mFechaExpira		= $D["expira"];
			$this->mCorporativo		= ($D["corporativo"] == 1) ? true : false;
			$this->mSucursal		= $D["sucursal"];
			$this->mPuesto			= $D["puesto"];
			$this->mNombreCompleto	= $D["apellidopaterno"] . " " . $D["apellidomaterno"] . " " . $D["nombres"];
			$this->mOptions			= $D["opciones"];
			$this->mUserIniciado	= true;
			if($this->mNivel == 99){
				if( isset($_SESSION["tmp.nivel.de.user"]) ){
					$this->mNivel	= setNoMenorQueCero($_SESSION["tmp.nivel.de.user"]);
				}
			}
			//procesar reglas
			$this->initOptions();
			if($inCache == false){
				$xCache->set($this->mIDCache, $D, $xCache->EXPIRA_UNHORA);
			}
		} else {
			$this->mMessages	.= "ERROR\tError al Iniciar al usuario " .  $this->mCodeUser . "\r\n";
		}
		//setLog($sql);
		unset($D);
		return $this->mUserIniciado;
	}
	function getID(){ return $this->mID; }
	function getIDParaRegla(){ return $this->mNivel . "@rw"; }
	function getComparePassword($md5txt){
		$md5txt	= $md5txt;
		$pwd	= $this->mPWD;
		return ($pwd == $md5txt) ? true : false;
	}
	function getCompareData($field, $value){
		$sucess = false;
		$d		= $this->getUserInfo($field);
		
		if ( trim($d) == trim($value)){
			$sucess = true;
		} else {
			$this->mMessages	.= "ERROR\tEl valor de $field no es el mismo\r\n";
		}
		return $sucess;
	}
	function add($NombreUsuario = "", $ClaveAcceso = "", $nivel = 2,
				$nombre = "", $ApPaterno = "", $ApMaterno = "", $Puesto="", $FechaDeExpiracion = false,
				$estatus 	= "", $Opciones = "", $sucursal = false, $CodigoUsuario = false, $codigo_de_persona = false){
		$sucursal			= ($sucursal == false) ? getSucursal() : $sucursal;
		$sucursal			= strtolower($sucursal);
		//Trabajar la Fecha de Expiracion
		//$dias				= VEN
		$FechaDeExpiracion	= ($FechaDeExpiracion == false) ? fechasys() : $FechaDeExpiracion;
		//Trabajar con la clave de acceso
		$ClaveAcceso		= ( $ClaveAcceso == "" ) ? md5( (ROTTER_KEY . rand(0,999) ) ) : $ClaveAcceso ;
		//validar el cifrado y comparar si esta cifrado.
		$ClaveAcceso		= $this->getHash($ClaveAcceso);
		$codigo_de_persona	= setNoMenorQueCero($codigo_de_persona);
		$codigo_de_persona	= ($codigo_de_persona <= DEFAULT_SOCIO) ? DEFAULT_SOCIO : $codigo_de_persona;
		//controlar el ID de usuario
		$FIdUsr				= "";
		$VIdUsr				= "";
		$msg				= "";
		$estatus			= trim($estatus) == "" ? $this->USR_ACTIVO : $estatus;
		if ( $CodigoUsuario != false){
			$FIdUsr			= "idusuarios, ";
			$VIdUsr			= " $CodigoUsuario, ";
		} //
		
		
		$sql = "INSERT INTO t_03f996214fba4a1d05a68b18fece8e71(
					$FIdUsr f_28fb96d57b21090705cfdf8bc3445d2a, f_34023acbff254d34664f94c3e08d836e,
				nombres, apellidopaterno, apellidomaterno, puesto,
				f_f2cd801e90b78ef4dc673a4659c1482d, periodo_responsable,
				estatus, sucursal, usr_options, date_expire, cuenta_contable_de_caja, codigo_de_persona)
    			VALUES($VIdUsr '$NombreUsuario', '$ClaveAcceso',
					'$nombre', '$ApPaterno', '$ApMaterno', '$Puesto',
					 $nivel, 99, '$estatus', '$sucursal', '$Opciones', '$FechaDeExpiracion', 'CUENTA_DE_CUADRE', $codigo_de_persona) ";
		$xQL	= new MQL();
		$inStat = $xQL->setRawQuery($sql);
					if($inStat === false) {
						$msg	.= "ERROR\tERROR EN EL ALTA DEL USUARIO\r\n";
					} else {
						//Agregar Nuevo Mail
						$xLog	= new cCoreLog();
						$xLog->add("OK\tNuevo Usuario: $NombreUsuario");
						$xLog->guardar($xLog->OCat()->USUARIO_NUEVO, $codigo_de_persona);
						
						$msg	.= "SUCESS\tEL ALTA DEL USUARIO SE HA EFECTUADO SATISFACTORIAMENTE\r\n";
					}
				$this->mMessages	.= $msg;
		return $msg;
	}
	function addPorPersona($codigo_de_persona, $nivel, $contrasennia){
		
	}
	function getMD5(){ return md5($this->mCodeUser); }
	function getMessages($put = OUT_TXT){
		$xH		= new cHObject();
		return $xH->Out($this->mMessages, $put);
	}
	/**
	 * Evalua si existe un socio
	 * @param string $user
	 * @return boolean
	 */
	function existe($user = false){
		$user	= ( $user == false) ? $this->mCodeUser : $user;
		$existentes	= 0;
		if ( isset($user) AND ($user != false) ){
			$sql		= "SELECT COUNT(idusuarios) AS 'existentes' FROM t_03f996214fba4a1d05a68b18fece8e71 WHERE idusuarios = $user";
			$existentes	= mifila($sql, "existentes");
		}
		return		($existentes == 0 ) ? false : true;
	}
	function getPermission($mFile){
		$xPer	= new cSystemPermissions();
		$access	= $xPer->getAccessFile($mFile, $this->getNivel());
		$this->mMessages	.= $xPer->getMessages();
		return $access;
	}
	function setUserOption(){
		
	}
	/**
	 * Obtiene en un array Las opciones del usuario
	 * @deprecated @since 2018.03.02
	 */
	function getUserOptions(){ return $this->initOptions(); }	
	function setCuentaContableDeCaja($cuenta){ return $this->setUpdate("cuenta_contable_de_caja", $cuenta);	}
	function setEsCorporativo($EsCorp = true){
		$res		= false;
		if($EsCorp == true OR $EsCorp == 1){
			$res 	= $this->setUpdate("corporativo", "1");
		} else {
			$res 	= $this->setUpdate("corporativo", "0");
		}
		
		return $res;
	}
	function getCuentaContableDeCaja(){	return $this->mCuentaDeCaja; }	
	function setEndSession($ByeBye = false, $onInit = false, $msg = ""){
		$oficial 		= $this->getUserInfo();
		$iduser 		= $this->mCodeUser;
		$oficial 		= $this->getNombreCompleto();
		$ql				= new MQL();
		/**
		 * Eliminar al Usuarios de Conectados
		 */
		if($onInit == false){
			saveError(10, $this->mCodeUser, "$oficial  cerro sesion");
		}
		$ql->setRawQuery("DELETE FROM usuarios_web_connected WHERE webid='" . $this->getID() . "' ");
		// sin variables
		session_unset();
		session_destroy();
		$msg		= ($msg == "") ? "" : "?" . SYS_MSG . "=$msg";
		if($ByeBye == true){ header ("location:inicio.php$msg"); exit; }
	}
	function getDatosInArray(){
		if ( $this->mUserIniciado == false ){
			$this->init();
		}
		return $this->mDatosInArray;
	}
	function getNombreCompleto(){
		return $this->mNombreCompleto;
	}
	function getEstado(){ return $this->mEstado; }
	function setDelete($NewUser = false){
		$NewUser	= ($NewUser == false) ? getUsuarioActual() : $NewUser;
		$OldUser	= $this->mCodeUser;
		
		$sqlST		= "SHOW TABLES IN " . MY_DB_IN;
		$rs			= getRecordset($sqlST, cnnGeneral() );
		$msg		= "============= \tCAMBIANDO DATOS DEL  USUARIO $OldUserO AL USUARIO $NewUser\r\n";

		while( $rw = mysql_fetch_array($rs) ){
			$table 		= $rw[0];
			$sqlMT		= "UPDATE $table SET idusuario = $NewUser WHERE idusuario = $OldUser ";
			$x			=  my_query($sqlMT);
			$msg		.= $x["info"];
		}
		return $msg;		
	}
	function getNivel(){ return $this->mNivel;	}
	function getClaveDePersona(){ return $this->mClaveDePersona; }
	function getCTX(){
		if($this->mUserIniciado == true){
			$usr	= $this->getHash($this->mID);
			$pwd	= $this->getHash($this->mPWD);			
		} else {
			$usr	= $this->getHash($_SESSION["SN_b80bb7740288fda1f201890375a60c8f"]);
			$pwd	= $this->getHash($_SESSION["SN_0d35c1f17675a8a2bf3caaacd59a65de"]);
		}
		$rnd1	= rand(0,25);
		$rnd2	= rand(0,25);
		$usr	= substr($usr, $rnd1, 5);
		$pwd	= substr($pwd, $rnd2, 5);
		$ctx	= strtoupper("$rnd1.$usr-$rnd2.$pwd");

		
		return $ctx;
	}
	function initByCTX($ctx){
		$res	= false;
		$xQL	= new MQL();
		$ctx	= strtolower($ctx);
		$saveLog= true;
		//setLog($sql);
		$xCache	= new cCache();
		$cid	= "ctx-$ctx";
		$DD		= explode("-", $ctx);
		$DD1	= (isset($DD[0])) ? explode(".", $DD[0]) : array();
		$DD2	= (isset($DD[1])) ? explode(".", $DD[1]) : array();
		$idx1	= (isset($DD1[0])) ? setNoMenorQueCero($DD1[0]) : null;
		$idx2	= (isset($DD2[0])) ? setNoMenorQueCero($DD2[0]) : null;
		if($idx1 !== null AND $idx2 !== null){
			$c1		= (isset($DD1[1])) ? $DD1[1] : md5(rand(9999,99999));
			$c2		= (isset($DD2[1])) ? $DD2[1] : md5(rand(99991,999991));
			$idx1	= $idx1+1;
			$idx2	= $idx2+1;
			/*$sql	= "SELECT	`t_03f996214fba4a1d05a68b18fece8e71`.* FROM `t_03f996214fba4a1d05a68b18fece8e71` WHERE
			CONCAT( getHash(`idusuarios`), '-',getHash(`f_34023acbff254d34664f94c3e08d836e`) ) = '$ctx' LIMIT 0,1";*/
			$sql	= "SELECT	`t_03f996214fba4a1d05a68b18fece8e71`.* FROM `t_03f996214fba4a1d05a68b18fece8e71` WHERE 
			SUBSTRING(getHash(`idusuarios`), $idx1,5 ) = '$c1' AND SUBSTRING(getHash(`f_34023acbff254d34664f94c3e08d836e`), $idx2,5) = '$c2' LIMIT 0,1";
			
			
			$DCTX	= $xCache->get($cid);
			if(!is_array($DCTX)){
				$DCTX		= $xQL->getDataRow($sql);
			} else {
				$saveLog	= false;
			}
			if(isset($DCTX["idusuarios"])){
				$this->mTypeById 	= true;
				$this->mCodeUser	= $DCTX["idusuarios"];
				$this->mEstado		= $DCTX["estatus"];
				$usr				= $DCTX["f_28fb96d57b21090705cfdf8bc3445d2a"];
				//SN_b80bb7740288fda1f201890375a60c8f
				//Iniciar Session
				//$xSVC				= new MQLService("", "");
				//$xSVC->setKey(getClaveCifradoTemporal());			
				$pass				= $DCTX["f_34023acbff254d34664f94c3e08d836e"];
				//$pass				= $xSVC->getEncryptData($pass);
				
				$this->initSession($usr, "", $pass, $saveLog);
				$xCache->set($cid, $DCTX, $xCache->EXPIRA_5MIN);
				$res				= true;
				$this->init();
			}
		}
		return $res;
	}
	function getNombreDeUsuario(){ return $this->mNombreUser;  }
	function getCorreoElectronico(){ return $this->mCorreoElectronico; }
	function getPuesto(){ return $this->mPuesto; }
	function setActualizarPorPersona(){
		$xPer	= new cSocio($this->mClaveDePersona);
		$ready	= false;
		if($xPer->init() == true){
			$xT				= new cT_03f996214fba4a1d05a68b18fece8e71();
			$xT->setData( $xT->query()->initByID($this->getID()) );
			$xT->apellidomaterno( $xPer->getApellidoMaterno() );
			$xT->apellidopaterno( $xPer->getApellidoPaterno() );
			$xT->nombres( $xPer->getNombre() );
			$alias			= explode(" ", $xPer->getNombre());
			$alias			= $alias[0] . " " . $xPer->getApellidoPaterno();
			$alias			= substr($alias, 0,19);
			$this->mNombreCompleto	= $xPer->getNombreCompleto();
			
			$xT->alias($alias);
			$this->mCorreoElectronico	= $xPer->getCorreoElectronico();
			
			$ready			= $xT->query()->update()->save( $this->getID() );
			if($ready == false){
				$this->mMessages .= "ERROR\tAl actualizar el usuario " . $this->getID() . " Desde la persona " . $this->mClaveDePersona . "\r\n";
			} else {
				$this->mMessages .= "OK\tSe actualizo el usuario " . $this->getID() . " desde la persona " . $this->mClaveDePersona . "\r\n";
				$ready			= true;
				$this->setCuandoSeActualiza();
			}
		}
		return $ready;
	}
	function setSuspender(){ $this->mMessages .= "Usuario suspendido"; return $this->setUpdate("estatus", SYS_USER_ESTADO_SUSP);  }
	function setBaja(){ $this->mMessages .= "Usuario en Baja";  return $this->setUpdate("estatus", SYS_USER_ESTADO_BAJA); }
	function setActivo(){ $this->mMessages .= "Usuario Activado"; return $this->setUpdate("estatus", SYS_USER_ESTADO_ACTIVO); }
	function setPassword($rawpass){
		$xLog			= new cCoreLog();
		$xLog->add("Cambio de password del usuario " . $this->mCodeUser . " por "  . getUsuarioActual()  . "\r\n");
		$xLog->guardar($xLog->OCat()->PASSWORD_MODIFICADO);
		$rawpass 		= $this->getHash($rawpass);
		return $this->setUpdate($this->FPASSWORD, $rawpass);
	}
	function setNombreUsuario($rawnombre){
		$rawnombre	= trim(substr($rawnombre, 0,20));
		return $this->setUpdate($this->FNAME, $rawnombre);
	}
	function setPuesto($rawnombre){
		$rawnombre	= trim($rawnombre);
		return $this->setUpdate("puesto", $rawnombre);
	}
	function setPin($pin){
		$pin	= setNoMenorQueCero($pin);
		return $this->setUpdate("pin_app", $pin);
	}
	function setSucursal($rawnombre){
		$rawnombre	= trim(strtolower($rawnombre));
		return $this->setUpdate("sucursal", $rawnombre);
	}
	function setNivelAcceso($nivel){
		$nivel		= setNoMenorQueCero($nivel);
		return $this->setUpdate($this->FNIVEL, $nivel);
	}
	function setCodigoDePersona($codigo){
		$codigo = setNoMenorQueCero($codigo);
		if($codigo > DEFAULT_SOCIO){
			$this->setUpdate("codigo_de_persona", $codigo);
			$this->setActualizarPorPersona();
		}
	}
	private function setUpdate($campo, $valor){
		$sql	= "UPDATE `t_03f996214fba4a1d05a68b18fece8e71` SET  `$campo`='$valor' WHERE `idusuarios`=" . $this->mID . " ";
		$xQL	= new MQL();
		$rs		= $xQL->setRawQuery($sql);
		$this->setCuandoSeActualiza();
		return ($rs === false) ? false : true;
	}
	function getEsBaja(){ return ($this->mEstado == SYS_USER_ESTADO_BAJA) ? true : false; }
	function getEsCorporativo(){ return $this->mCorporativo; }
	function getHash($str, $mMd5 = true){
		if($mMd5 == true){
			$str	= md5(trim($str));
		}
		return sha1(sha1($str, true));
	}
	function getConnectEstatus($new = false){
		if(FORCE_SESSION_LOCKED == false){
			$connected	= false;
		} else {
			$connected	= false;
			$xQL		= new MQL();
			$fecha		= date("Y-m-d");
			$id			= $this->mID;
			$sql 		= "SELECT count(webid) AS 'connected' FROM usuarios_web_connected WHERE webid='$id' AND option1='$fecha' ";
			$DD			= $xQL->getDataRow($sql);
			if(isset($DD["connected"])){
				$connected	= true;
			}
			if($new == true){
				$opt2		= date("H:i:s");
				$sqlNC = "INSERT INTO usuarios_web_connected
				(webid, option1, option2) VALUES ('$id', '$fecha', '$opt2')";
				$xQL->setRawQuery($sqlNC);
			}
		}
		return  $connected;
	}
	function initSession($user, $password, $RawPass = "", $saveLog = true){
		$mKey	= getClaveCifradoTemporal();
		$res	= false;
		$xSVC	= new MQLService("", "");
		$xQL	= new MQL();
		$xF		= new cFecha();
		$xLog	= new cCoreLog();
		
		$xSVC->setKey($mKey);
		$pwd	= $xSVC->getDecryptData($password);
		$pwd	= $this->getHash($pwd);
		if($RawPass !== "" AND $password == ""){
			$pwd	= $RawPass;
		}
		$sql 	= "SELECT * FROM `usuarios` WHERE `nombreusuario` = '$user' AND `contrasenna`='$pwd' AND `estatus`='activo' LIMIT 0,1";
		$DD		= $xQL->getDataRow($sql);
		
		if(isset($DD["idusuarios"])){
			$res 						= true;
			$nivel						= $DD["niveldeacceso"];
			$nuser						= $DD["nombreusuario"];
			$iduser						= $DD["idusuarios"];
			$estado						= $DD["estatus"];
			
			$_SESSION["en_depurado"] 	= ($nivel == 99) ? true : false;
			$_SESSION["log_id"] 		= $iduser;
			$_SESSION["log_user"] 		= $nuser;
			$_SESSION["log_nivel"] 		= $nivel;

			//--------------------------------------------------------
			
			//define SN_0a744893951e0d1706ff74a7afccf561 == USR
			//SN_0d35c1f17675a8a2bf3caaacd59a65de		= Password
			//SN_b80bb7740288fda1f201890375a60c8f
			$_SESSION[SYS_USER_ID] 		= $iduser;
			$_SESSION[SYS_USER_NIVEL] 	= $nivel;
			$_SESSION["SN_0d35c1f17675a8a2bf3caaacd59a65de"] = $pwd;
			$_SESSION["SN_0a744893951e0d1706ff74a7afccf561"] = $nuser;		//Nombre de Usuario

			//--------------------------------------------------------
			$this->mCodeUser	= $iduser;
			$this->mTypeById	= true;
			$this->mEstado		= $estado;
			$this->mNivel		= $nivel;
			$res				= $this->init();
			$ddE				= $this->getDataByNivel();
			$corporativo		= $this->getEsCorporativo();
			
			//Tipo de Usuario en el Sistema
			$_SESSION[SYS_USER_TIPO] 	= $this->getTipoEnSistema();

			if (FORCE_PASSWORD_EXPIRE == true){
				if($xF->getInt($this->getFechaExpira()) < $xF->getInt(fechasys()) ){
					$res		= false;
					$xLog->add("La contraseña del usuario $nuser ya expiro\r\n");
				}
			}
			if(OPERACION_LIBERAR_SUCURSALES == false  AND ($DD["sucursal"] !== getSucursal()) AND $corporativo == false ){
				$xLog->add("El Usuario $nuser No tiene Acceso a esta Sucursal (" . getSucursal() . ")\r\n");
				$res			= false;
			}
			if($this->getConnectEstatus(true) == true){
				$xLog->add("El Usuario $nuser ya inicio sesion en otra Terminal\r\n");
				$res			= false;
			}
			if($res == true){
				
				$xLog->add("El usuario $nuser ha iniciado con exito.\r\n");
			}
		} else {
			$xLog->add("Usuario Desconocido.\r\n");
		}
		$this->mMessages		.= $xLog->getMessages(); 
		$tt		= ($res == false) ? $xLog->OCat()->ERROR_LOGIN : $xLog->OCat()->SUCCESS_LOGIN;
		if($saveLog == true){
			$xLog->guardar($tt);
		}
		return $res;
	}
	function getFechaExpira(){ return $this->mFechaExpira; }
	function getIndexPage(){ return $this->mIndexPage;	}
	function getTasksPage(){ return $this->mTasksPage;	}
	function getSucursal(){ return $this->mSucursal; }
	function getAlias(){ return $this->mAlias; }
	function getEsOriginador(){
		return($this->mNivel == USUARIO_TIPO_ORIGINADOR) ? true : false;
	}
	
	function setCuandoSeActualiza(){ $this->setCleanCache(); }
	private function setCleanCache(){
		if($this->mIDCache !== ""){
			$xCache = new cCache();
			$xCache->clean($this->mIDCache);
			$xCache->clean("data-user-nombreusuario-". $this->mCodeUser);
			$xCache->clean("data-user-id-". $this->mCodeUser);
		}
	}
	private function initOptions(){
		if($this->mUserIniciado == false){ $this->init(); }
		$rules 	= array();
		$mRules = explode(";", $this->mOptions);
		foreach ($mRules as $idx => $regla){
			$regla				= explode("=", $regla);
			$idregla			= (isset($regla[0])) ? $regla[0] : "";
			$valor				= (isset($regla[1])) ? $regla[1] : "";
			if($idregla !== ""){
				$idregla		= strtoupper($idregla);
				$rules[$idregla]= trim($valor);
			}
		}
		$this->mUserOptions		= $rules;
		return $this->mUserOptions;
	} 
	function puede($form, $titulo, $obj = ""){
		$puede		= true;
		if($titulo == "TR.SALIR"){
			
		} else {
			$xPerms	= new cSystemPermissions();		
			$idcx	= crc32($form) . "-f-" . crc32($titulo);
			$obj	= ($obj == "") ? "FORM" : $obj;
			$arr	= $xPerms->initPueden();
			
			if(!isset($arr[$idcx])){
				//verificar si existe
				//Insertar en SQL
				$xT	= new cSistema_permisos();
				$xT->idsistema_permisos("NULL");
				$xT->accion($idcx);
				$xT->descripcion($titulo);
				$xT->nombre_objeto($form);
				$xT->tipo_objeto($obj);
				$res = $xT->query()->insert()->save();
				if($res !== false){
					//Insertar en Cache
					$xCache	= new cCache();
					$arr[$idcx]		= "";
					$xCache->set($xPerms->IDCACHE_PUEDEN, $arr);
				}
			} else {
				//verificar si existe entre los negados
				if(strpos($arr[$idcx], $this->getNivel() . "@r") !== false ){
					$puede	= false;
					//setError();
				}
			}
		}
		
		return $puede;
	}
	function getTipoEnSistema(){
		return ($this->mUserType <= 0) ? $this->mNivel : $this->mUserType;
	}
	function getSucursalAccede(){
		$sucursal			= getSucursal();
		if(OPERACION_LIBERAR_SUCURSALES == false){
			if($this->getEsCorporativo() == false){
				$sucursal	= $this->getSucursal();
			}
		}
		return $sucursal;
	}
	function getEnDesarrollo(){
		$res	= false;
		if(MODO_DEBUG == true AND (MODO_CORRECION == true OR MODO_MIGRACION == true) ){
			return true;
		}
		return $res;
	}
}
class cSystemUserRulesList {
	public $PUEDE_EDITAR_USUARIOS 	= "PUEDE_EDITAR_USUARIOS";
	public $PUEDE_AGREGAR_USUARIOS 	= "PUEDE_AGREGAR_USUARIOS";
	public $PUEDE_ELIMINAR_RECS 	= "PUEDE_ELIMINAR_RECIBOS";
	public $PUEDE_EDITAR_RECS 		= "PUEDE_EDITAR_RECIBOS";
	public $PUEDE_CERRAR_CAJAS 		= "PUEDE_CERRAR_CAJAS";
	
	public $ACTIVE_PRINTER_POS 		= "IMPRESORA_POS_ACTIVA";
	
	function  __construct(){}
	
}
class cSystemPermissions{
	private $mMessages			= "";
	private $mID				= false; 
	private $mPublicFile		= false;

	private $mPerfiles			= array(
		4 => array(18000, 15000,1000),
		7 => array(18000, 15000,3000,4000, 2000),
		6 => array(18000, 15000, 5000),
		10 => array(18000, 15000, 7000, 2000),
		8 => array(18000, 15000, 8000, 2000)
		);
	private $mProhibidos	= array(
		10 => array(2001, 2002, 2050,2060, 2014, 2012)
	);
	private $mRW				= "@rw";
	private $mRO				= "@ro";
	private $mGlobalM			= 9999;
	private $mObj				= null;
	private $mInit				= false;
	private $mPermisos			= array();
	public $IDCACHE_PUEDEN		= "sistema_permisos-tabla";
	public $DEF_PERMISOS		= DEFAULT_PERMISOS;
	public $DIV_PERMISOS		= ",";
	private $mParentID			= 0;
	private $mTabla				= "general_menu";
	
	function __construct($id = false){
		$this->mID				= setNoMenorQueCero($id);
		//$this->DEF_PERMISOS		= DEFAULT_PERMISOS;
	/*
	 1000	Caja
	 2000	Personas
	 3000	Creditos
	 7000	PLD/FT
	 8000	Captacion
	 5000	Contabilidad
	 4000	Seguimiento
	 11000	Herramientas
	 10000	Seguridad
	 15000	Soporte/Ayuda
	 18000	Reportes
	 */
	}
	function setRestore($Fecha = false){

		$gestor = @fopen(PATH_BACKUPS . "safeosms-permissions-" . getSucursal() . "-$Fecha.sbk", "r");
		$msg	= "";
		$iReg 	= 0;
		$cT		= new cTipos();
		$xQL	= new MQL();

		if ($gestor) {
			while (!feof($gestor)) {
					$bufer			= fgets($gestor, 4096);

					if (!isset($bufer) ){
						$msg .= "$iReg\tERROR\tLa Linea($iReg) no se leyo($bufer)\r\n";
					} else {
						$bufer			= trim($bufer);
						$datos			= explode("|", $bufer, 2);
						$indice			= $cT->cInt($datos[0]);
						if($indice != 0){
							$perms			= html_entity_decode($datos[1]);
							$sql			= "UPDATE general_menul SET menu_rules = \"$perms\" WHERE idgeneral_menu = $indice ";
							$msg			.= "Cargando el Indice $indice con Permisos ($perms)\r\n";
							$xQL->setRawQuery($sql);
						}
					}
				$iReg++;
			}
		}
		@fclose ($gestor);
		$this->mMessages	.= $msg;
		return $msg;
		
	}
	function setBackup($Fecha){
		$Fecha	= ($Fecha == false ) ? date("Y-m-d") : $Fecha;
		$mFile	= PATH_BACKUPS . "safeosms-permissions-" . getSucursal() . "-$Fecha.sbk";
		$sql	= "SELECT idgeneral_menu, menu_rules FROM general_menu";
		$xLog	= @fopen($mFile, "w+");
		$xQL	= new MQL();
		$rs		= $xQL->getDataRecord($sql);
		foreach ($rs as $rw){
			$text	= $rw["idgeneral_menu"] . "|" . htmlentities($rw["menu_rules"]) . "\r\n"; 
		 	@fwrite($xLog, $text);
		}
		@fclose($xLog);
		
		return $mFile;		
	}
	function getMessages($put =OUT_TXT){ $xH		= new cHObject(); return $xH->Out($this->mMessages, $put); }
	function init($data = false){
		$xT				= new cGeneral_menu();
		$xCache			= new cCache();
		$inCache		= true;
		$idx			= $this->mTabla . "-" . $this->mID;
		if(!is_array($data)){
			$data		= $xCache->get($idx);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `general_menu` WHERE `idgeneral_menu`=" . $this->mID . " LIMIT 0,1");
				$inCache = false;
			}
		}
		if(isset($data[$xT->IDGENERAL_MENU])){
			$this->mInit		= true;
			$this->mParentID	= $data[$xT->MENU_PARENT];
			$this->mPermisos	= $this->setDecompilePermisos($data[$xT->MENU_RULES]);
			if($inCache == false){
				$xCache->set($idx, $data);
			}
		}
		return $this->mInit;
	}
	function getOMenu($id = false){
		$id			= setNoMenorQueCero($id);
		$id			= ($id == 0) ? $this->mID : $id;
		$this->mID	= $id;
		if($this->mObj == null){
			$this->mObj	= new cGeneral_menu();
			$this->mObj->setData($this->mObj->query()->initByID($id) );
		}
		return $this->mObj;
	}
	private function getCompilePermisos(){ 
		$txt		= "";
		$arrPerms	= array();
		
		foreach ($this->mPermisos as $id2 => $val2){
			$idx	= setNoMenorQueCero($val2);
			//setLog($idx);
			$arrPerms[$idx]	= $idx;
		}
		//setLog($arrPerms);
		asort($arrPerms);
		foreach ($arrPerms as $id => $val){
			$val	= setNoMenorQueCero($val);
			if($val > 0){
				$np		= $val . $this->mRW;
				$txt	.= ($txt == "") ? $np : $this->DIV_PERMISOS . $np;
			}
		}
		//setLog($txt);
		$this->setDecompilePermisos($txt);
		return $txt;
	}
	private function setDecompilePermisos($str){
		$arr		= explode($this->DIV_PERMISOS, $str);
		$pm			= array();
		
		foreach ($arr as $id => $val){
			$cnt	= setNoMenorQueCero($val);
			if($cnt > 0){
				$pm[$cnt.$this->mRW]		= $cnt.$this->mRW;
			}
		}
		$arr		= $pm;
		$pm			= null;
		asort($arr);
		return $arr;
	}
	function setAgregarPermiso($niveldeusuario, $id = false){
		$niveldeusuario	= setNoMenorQueCero($niveldeusuario);
		$id			= setNoMenorQueCero($id);
		$id			= ($id <= 0) ? $this->mID : $id;
		
		$this->mID	= $id;
		if($this->mInit == false){ $this->init(); }

		unset($this->mPermisos[$niveldeusuario.$this->mRO]); //old
		unset($this->mPermisos[$niveldeusuario.$this->mRW]); //old
		
		$this->mPermisos[$niveldeusuario . $this->mRW] 	= $niveldeusuario . $this->mRW;
		//setLog($this->mPermisos);
		$permisos				= $this->getCompilePermisos();
		
		//setLog(print_r($this->mPermisos, true));
		$this->mMessages		.= "OK\t$id\tAgregar.- Cambiar permisos a $permisos\r\n";
		return $this->setPermisos($permisos, $id);
	}
	function setEliminarPermiso($niveldeusuario, $id = false){
		$niveldeusuario	= setNoMenorQueCero($niveldeusuario);
		$id			= setNoMenorQueCero($id);
		$id			= ($id <=0) ? $this->mID : $id;
		$this->mID	= $id;
		if($this->mInit == false){ $this->init(); }
		unset($this->mPermisos[$niveldeusuario.$this->mRO]);
		unset($this->mPermisos[$niveldeusuario.$this->mRW]);
		$permisos				= $this->getCompilePermisos();
		$this->mMessages	.= "WARN\t$id\tAgregar.- Cambiar permisos a $permisos\r\n";
		return $this->setPermisos($permisos, $id);
	}	
	function setPermisos($permisos, $id = false){
		$xQL		= new MQL();
		$msg		= "";
		$id			= setNoMenorQueCero($id);
		$id			= ($id <=0) ? $this->mID : $id;
		$this->mID	= $id;
		if($this->mInit == false){ $this->init(); }
		
		if($this->mParentID == $this->mGlobalM){
			$sql 	= "UPDATE general_menu set menu_rules='$permisos' WHERE `idgeneral_menu` = $id ";
			$ics	= $xQL->setRawQuery($sql);
			if($ics === false){
				
			} else {
				$msg	.= "OK\tActualizacion de permisos para $id\r\n";
			}
		} else {
			$gl		= $this->mGlobalM;
			//actualizar padres y parent
			$sql 	= "UPDATE general_menu set menu_rules='$permisos' WHERE (menu_parent != $gl) AND (menu_parent=$id OR `idgeneral_menu` = $id )";
			//setLog("UPDATE general_menu set menu_rules='$permisos' WHERE (menu_parent != $gl) AND (menu_parent=$id OR `idgeneral_menu` = $id )");
			$ics	= $xQL->setRawQuery($sql);
			//setLog($sql);
			$msg	.= "OK\tAplicacion Recursiva de $id " . $xQL->getNumberOfRows() . "\r\n";
			
			$sql2	= "SELECT * FROM general_menu WHERE menu_parent=$id";
			$rs		= $xQL->getDataRecord($sql2);
					
			$xMen	= new cGeneral_menu();
			foreach ($rs as $row){
				$xMen->setData($row);
				$ide	= $xMen->idgeneral_menu()->v();
				$sqlP 	= "UPDATE general_menu SET menu_rules='$permisos' WHERE menu_parent=$ide OR `idgeneral_menu` = $ide ";
				$idcs 	= $xQL->setRawQuery($sqlP);
				$msg	.= "OK\tSubmenu $ide padre $id " . $idcs["rows"] . "\r\n";
			}
		}
		$this->mMessages	.= $msg;
		//setLog($msg);
		return $msg;
	}
	function setAplicarPerfil($tipo = false){
		if($tipo == false){
			foreach ($this->mPerfiles as $nivel => $items){
				foreach ($items as $indice => $valor){
					$this->setAgregarPermiso($nivel, $valor);
				}
			}
			foreach ($this->mProhibidos as $nivel2 => $items2){
				foreach ($items2 as $indice2 => $valor2){
					$this->setEliminarPermiso($nivel2, $valor2);
				}
			}			
		} else {
			if(isset($this->mPerfiles[$tipo] )){
				$items	= $this->mPerfiles[$tipo];
				foreach ($items as $indice => $valor){
					$this->setAgregarPermiso($tipo, $valor);
				}
			}
			if(isset($this->mProhibidos[$tipo] )){
				$items2	= $this->mProhibidos[$tipo];
				foreach ($items2 as $indice2 => $valor2){
					$this->setEliminarPermiso($tipo, $valor2);
				}
			}			
		}
	}
	function setClear(){
		$xQL	= new MQL();
		$sql 	= "UPDATE general_menu set menu_rules='99@rw'";
		$xQL->setRawQuery($sql);
		$this->mMessages	.= "OK\tDar permisos solo a ROOT\r\n";
		$sqlD 	= "UPDATE general_menu SET menu_rules='" . $this->DEF_PERMISOS . "' WHERE menu_parent = "  . $this->mGlobalM .  " ";
		$xQL->setRawQuery($sqlD);
		$this->mMessages	.=  "OK\tPermisos establecidos por defecto afectado\r\n";		
		
	}
	function setLiberar(){
		$sql = "UPDATE general_menu SET menu_rules='" . $this->DEF_PERMISOS . "' ";
		$xQL	= new MQL();
		$xQL->setRawQuery($sql);
		$this->mMessages	.=  "OK\tTodos los permisos se han reseteado\r\n";
	}
	function setTraducir($txt, $out = OUT_TXT){
		$perms	= explode(",", $txt);
		$salida	= "";
		
		return $perms;
	}
	function setCrearNuevoNivel($nuevo, $heredarDe = 0){
		$nuevo	= setNoMenorQueCero($nuevo);
		$hg		= setNoMenorQueCero($heredarDe);
		
		
		$rs		= $this->getPermitidos($nuevo);
		foreach ($rs as $rw){
			$this->init($rw);
			$this->setAgregarPermiso($nuevo);
		}
	}
	function getPermitidos($nivel, $superior = false, $buscar = ""){

		$xQL	= new MQL();
		$wS		= ($superior !== false)? " AND `menu_parent`=$superior " : "";
		$wF		= ($buscar == "") ? "" : " AND (`general_menu`.`menu_title` LIKE '%$buscar%' OR `general_menu`.`menu_file` LIKE '%$buscar%') ";
		
		$rs		= $xQL->getDataRecord("SELECT * FROM `general_menu` 
				WHERE FIND_IN_SET('$nivel@rw',`menu_rules`) >0 $wS $wF
				ORDER BY `general_menu`.`menu_type` DESC, `menu_parent`,`menu_order`, `general_menu`.`menu_title` ");
		return $rs;
	}
	function getNegados($nivel, $superior = false, $buscar = ""){
		$xQL	= new MQL();
		$wS		= ($superior !== false)? " AND `menu_parent`=$superior " : "";
		$wF		= ($buscar == "") ? "" : " AND (`general_menu`.`menu_title` LIKE '%$buscar%' OR `general_menu`.`menu_file` LIKE '%$buscar%') ";
		
		$rs		= $xQL->getDataRecord("SELECT * FROM `general_menu` 
				WHERE FIND_IN_SET('$nivel@rw',`menu_rules`) =0  $wS $wF
				ORDER BY `general_menu`.`menu_type` DESC, `menu_parent`, `general_menu`.`menu_title` ");
		return $rs;
		
	}
	function getParents(){
		$xQL	= new MQL();
		$rs		= $xQL->getDataRecord("SELECT * FROM `general_menu` WHERE `menu_type`='parent' ORDER BY `menu_parent`");
		return $rs;		
	}
	function getPublicFiles(){
		//"personas.svc.php" => true,
		$PSVC		= array("personas.actividades.economicas.php" => true,
				"listanegra.svc.php" => true, "equivalente.moneda.svc.php" => true,
				"cantidad_en_letras.php" => true, "peps.svc.php" => true, "cotizador.plan.svc.php" => true, "pc.svc.php" => true, 
				"importar.svc.php" => true, "exportar.svc.php" => true
		);			//servicios publicos
		return $PSVC;
	}
	function getAccessFile($mFile, $nivel = 0){
		$permiso	= false;
		//$win		= (SAFE_OS == "win") ? true : false;
		$PFile		= $this->getFileName($mFile);
		$xCache		= new cCache();
		$idx		= "cls-" . $PFile . "-$nivel";
		
		$permiso	= $xCache->get($idx);
		$permiso	= ($permiso === "TRUE") ? true : $permiso;
		
		if(!is_bool($permiso)){
			$xQL		= new MQL();
			if($mFile !== ""){
				$PSVC	= $this->getPublicFiles();
				
				if($nivel >= 1){
					$DRules	= $xQL->getDataRow("SELECT COUNT(`idgeneral_menu`) AS `items`, `menu_rules` FROM `general_menu` WHERE `menu_file` LIKE '%$PFile' AND FIND_IN_SET('$nivel@rw',`menu_rules`) >0");
					$items	= (isset($DRules["items"])) ? setNoMenorQueCero($DRules["items"]) : 0; 
					if($items <= 0){
						$this->mMessages	.= "ERROR\tSin permisos para el Archivo $PFile\r\n";
					}
					if( $items <= 0 AND $nivel >2 AND (MODO_DEBUG == true OR MODO_CORRECION == true OR MODO_MIGRACION == true)){
						$this->mMessages	.= "WARN\tPermisos Automaticos para el Archivo $PFile\r\n";
						setLog("WARN\tPermisos Automaticos para el Archivo $PFile $nivel\r\n");
						$ins	= $xQL->setRawQuery("INSERT INTO `general_menu` (`menu_title`, `menu_file`) VALUES ('$PFile', '$PFile')");
						$items	= ($ins === false) ? 0 : 1;
					}
					
					$permiso	= ($items > 0) ? true : false;
				}
				if(isset($PSVC[$PFile])){
					$this->mMessages	.= "WARN\tEl archivo $PFile es Publico\r\n";
					$permiso			= true;
					$this->mPublicFile	= true;
				}
			}
			$ensave	= ($permiso == true) ? "TRUE" : "FALSE";
			$xCache->set($idx, $ensave); 
		}
		
		
		return $permiso;
	}
	function getEsPublico($archivo = ""){
		if($archivo !== ""){
			$archivo	= $this->getFileName($archivo);
			$PSVC		= $this->getPublicFiles();
			if(isset($PSVC[$archivo])){
				$this->mMessages	.= "WARN\tEl archivo $archivo es Publico\r\n";
				$this->mPublicFile	= true;
			}
		}
		return $this->mPublicFile; 
	}
	private function getFileName($mFile){
		$mFile	= str_replace("/", STD_LITERAL_DIVISOR, $mFile);
		$mFile	= str_replace("\\", STD_LITERAL_DIVISOR, $mFile);
		$DFile	= explode(STD_LITERAL_DIVISOR, $mFile);
		$inum	= count($DFile);
		$PFile	= $DFile[ ($inum -1) ];
		$DFile	= null; $mFile= null;
		return $PFile;
	}
	function initPueden(){
		$xCache		= new cCache();
		$arr		= $xCache->get($this->IDCACHE_PUEDEN);
		if(!is_array($arr)){
			$xQL	= new MQL();
			$dd		= $xQL->getDataRecord("SELECT `accion`,`denegado` FROM `sistema_permisos`");
			foreach ($dd as $rw){
				$arr[$rw["accion"]] = $rw["denegado"];
			}
			$xCache->set($this->IDCACHE_PUEDEN, $arr);
			$dd		= null;
			$xQL	= null;
		}
		return $arr;
	}
	function setCuandoSeActualiza(){
		
	}
}

/**
 * copiado desde: no me acuerdo
 * Obtiene ubicacion ip, requiere curl
 * @param string $ip
 */
function GetUbicacionesIP($ip){
                                    
            $archivo_xml = "http://api.hostip.info/get_xml.php?ip=".$ip ."";            
            $ch = curl_init();
            $timeout = 0; // set to zero for no timeout
            curl_setopt ($ch, CURLOPT_URL, $archivo_xml);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $procedencia_xml = curl_exec($ch);
            curl_close($ch);
            
            //$procedencia_xml = file_get_contents($archivo_xml);
        
        
            if (empty($procedencia_xml)){
                $array["pais"] = "desconocido";
                $array["lugar"] = "desconocido";
                $array["sigla"] = "desconocido";
            }else{
                preg_match_all("|<Hostip>(.*)</Hostip>|sU", $procedencia_xml, $items);
                $lista_nodos = array();
                foreach ($items[1] as $key => $item)
                {
                    preg_match("|<gml:name>(.*)</gml:name>|s", $item, $mi_lugar);
                    preg_match("|<countryName>(.*)</countryName>|s", $item, $mi_pais);
                    preg_match("|<countryAbbrev>(.*)</countryAbbrev>|s", $item, $mi_sigla);
                    
                    $lista_nodos[$key]['mi_lugar'] = $mi_lugar[1];
                    $lista_nodos[$key]['mi_pais'] = $mi_pais[1];
                    $lista_nodos[$key]['mi_sigla'] = $mi_sigla[1];
                }
                
                for ($i = 0; $i < 1; $i++)
                {
                    $array["pais"] = $lista_nodos[$i]['mi_pais'];
                    $array["lugar"] = $lista_nodos[$i]['mi_lugar'];
                    $array["sigla"] = $lista_nodos[$i]['mi_sigla'];
                }
                $procedencia_xml = "";
            }
            
            return $array;
            
}  
        
class cUserEstadisticas {
	private $mCodigoUsuario	= false;
	function __construct($user	= false){
		$user	= setNoMenorQueCero($user);
		$this->mCodigoUsuario	= ($user <= 0) ? getUsuarioActual() : $user;
	}
	function  getNumeroTareasPendientes(){
		$sql	= "SELECT	COUNT(*) AS 'numero'	FROM
					`usuarios_web_notas` `usuarios_web_notas`
					INNER JOIN `usuarios` `usuarios`
					ON `usuarios_web_notas`.`oficial_de_origen` = `usuarios`.`idusuarios`
					WHERE `usuarios_web_notas`.`idusuarios_web_notas` = " . $this->mCodigoUsuario . "
					
					
					 AND	(`usuarios_web_notas`.`estado` != 40)";
		$xQL	= new MQL();
		$DD		= $xQL->getDataRow($sql);
		return setNoMenorQueCero($DD["numero"]);
	}
}  

class cSystemPerfiles {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mIDCache	= "";
	private $mTabla		= "general_niveles";
	private $mTipo		= 0;

	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= $this->mTabla . "-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache			= new cCache();
		$xT				= new cGeneral_niveles();//Tabla
		if(!is_array($data)){
			$data		= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL	= new MQL();
				$data	= $xQL->getDataRow("SELECT * FROM `" . $this->mTabla . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			$this->mObj		= $xT; //Cambiar
			$this->mClave	= $data[$xT->getKey()];
			$this->mNombre	= $xT->descripcion_del_nivel()->v();
			$this->mTipo	= $xT->tipo_sistema()->v();
			
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
	function setCuandoSeActualiza(){ $this->setCleanCache(); }
	function add(){}
	function initNivelByIDX($idx){
		$id 			= $this->getTraducir($txt);
		$data			= $this->initNiveles();
		$dd				= (isset($data[$id])) ? $data[$id] : false;
		$this->mClave	= $id;
		return $this->init($dd);
	}
	function getTraducir($txt){
		$dd	= explode(STD_LITERAL_DIVISOR, $txt);
		$id	= (isset($dd[0])) ? $dd[0] : 0;
		return $id;
	}
	function initNiveles(){
		$xCache	= new cCache();
		$idx	= $this->mTabla . "-all";
		$data	= $xCache->get($idx);
		if(!is_array($data)){
			$data	= array();
			$xQL	= new MQL();
			$rs		= $xQL->getRecordset("SELECT * FROM `general_niveles`");
			while($rw = $rs->fetch_assoc()){
				$data[$rw["idgeneral_niveles"]] = $rw;
			}
			$rs->free();
			//Almacenar en cache
			$xCache->set($idx, $data);
		}
		return $data;
	}
}
?>