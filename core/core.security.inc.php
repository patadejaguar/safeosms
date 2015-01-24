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

@include_once("../libs/sql.inc.php");

//=====================================================================================================
class cSystemUser{
	private $mCodeUser 			= false;
	private $mTypeById			= true;
	private $mMessages			= "";
	private $mNivelRules		= array(); 
	private $mUserOptions		= array();
	private $mDatosInArray		= array(); 
	private $mUserIniciado		= false;
	private $mNivel				= 2;
	private $mClaveDePersona	= 0;
	private $mID				= false; 
	private $mPWD				= "";
	private $mCuentaDeCaja		= CUENTA_DE_CUADRE; 
	/**
	 * Inicia la Clase
	 * @param mixed $UserCode
	 * @param boolean $TypeById	Iniciar por Numero de ID / false: Iniciar por Nombre del usuario
	 */
	function __construct($UserCode = false, $IniciarPorID = true){
		if ($UserCode == false ){ $UserCode		= $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"]; }
		$this->mCodeUser	= $UserCode;
		$this->mTypeById	= $IniciarPorID;
		$this->init();
	}
	/**
	 * Obtiene en un array las reglas de Nivel Aplicadas
	 */
	function getUserRules(){
		$nivel	= $this->getUserInfo("niveldeacceso");
		$rules 	= array();
		$sqlNiv = "SELECT rules_by_user FROM general_niveles WHERE idgeneral_niveles=$nivel" ;
		$dats 	= obten_filas($sqlNiv);
		$mRules = explode("\n", $dats["rules_by_user"]);
		$mLim 	= sizeof($mRules);
			for($i = 0; $i < $mLim; $i++){
				$rul 			= explode("=", $mRules[$i]);
				$rules[$rul[0]] = trim($rul[1]);
			}
		$this->mNivelRules		= $rules;
	return $rules;
	}
	function getUserInfo($info = false){
		$this->init();
		$val	= ( $info == false ) ? $this->mDatosInArray : $this->mDatosInArray[$info];
		return $val;
	}
	function init(){
		$val				= false;
		$cFld				= "id";
		if ($this->mTypeById == false){
			$cFld			= "nombreusuario";
		}
		$sql 					= "SELECT * FROM usuarios WHERE $cFld = '" . $this->mCodeUser . "' ";
		
		$D						= obten_filas($sql);
		if(isset($D["niveldeacceso"])){
			$this->mDatosInArray	= $D;
			$this->mNivel			= $D["niveldeacceso"];
			$this->mClaveDePersona	= $D["codigo_de_persona"];
			$this->mID				= $D["idusuarios"];
			$this->mPWD				= $D["contrasenna"];
			$this->mCuentaDeCaja	= setNoMenorQueCero($D["cuenta_contable_de_caja"]) <= 0 ? CUENTA_DE_CUADRE : $D["cuenta_contable_de_caja"];
			$this->mUserIniciado	= true;
		}
		unset($D);
		return $this->mUserIniciado;
	}
	function getID(){ return $this->mID; }
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
				$estatus = "baja", $Opciones = "", $sucursal = false, $CodigoUsuario = false, $codigo_de_persona = false){
		$sucursal			= ($sucursal == false) ? getSucursal() : $sucursal;
		//Trabajar la Fecha de Expiracion
		//$dias				= VEN
		$FechaDeExpiracion	= ($FechaDeExpiracion == false) ? fechasys() : $FechaDeExpiracion;
		//Trabajar con la clave de acceso
		$ClaveAcceso		= ( $ClaveAcceso == "" ) ? md5( (ROTTER_KEY . rand(0,999) ) ) : $ClaveAcceso ;
		$codigo_de_persona	= ($codigo_de_persona == false) ? DEFAULT_SOCIO : $codigo_de_persona;
		//controlar el ID de usuario
		$FIdUsr				= "";
		$VIdUsr				= "";
		$msg				= "";
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
				$inStat = my_query($sql);
					if($inStat[SYS_ESTADO] == false) {
						$msg	.= "ERROR\tERROR EN EL ALTA DEL USUARIO; EL SISTEMA DEVOLVIO " . $inStat["error"] . " \t";
					} else {
						$msg	.= "SUCESS\tEL ALTA DEL USUARIO SE HA EFECTUADO SATISFACTORIAMENTE\t";
					}
				$this->mMessages	.= $msg;
		return $msg;
	}
	function addPorPersona($codigo_de_persona, $nivel, $contrasennia){
		
	}
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
		
	}
	function setUserOption(){
		
	}
	/**
	 * Obtiene en un array Las opciones del usuario
	 */
	function getUserOptions(){
		$usr	= $this->mCodeUser;
		$rules	= array();
		$sqlNiv = "SELECT
						`t_03f996214fba4a1d05a68b18fece8e71`.`cuenta_contable_de_caja`,
						`t_03f996214fba4a1d05a68b18fece8e71`.`usr_options` 
					FROM
						`t_03f996214fba4a1d05a68b18fece8e71` `t_03f996214fba4a1d05a68b18fece8e71` 
					WHERE
						(`t_03f996214fba4a1d05a68b18fece8e71`.`idusuarios` =$usr) LIMIT 0,1" ;
		$dats 	= obten_filas($sqlNiv);
		$mRules = explode("\n", $dats["usr_options"]);
		$mLim 	= sizeof($mRules);
			for($i = 0; $i < $mLim; $i++){
				$rul 			= explode("=", $mRules[$i]);
				$rules[$rul[0]] = trim($rul[1]);
			}
		$this->mUserOptions		= $rules;
	return $rules;
	}	
	function setCuentaContableDeCaja($cuenta){
		$usr	= $this->mCodeUser;
		$sqlUC	= "UPDATE t_03f996214fba4a1d05a68b18fece8e71 
				    SET  cuenta_contable_de_caja='$cuenta'
				    WHERE  idusuarios=$usr";
		my_query($sqlUC);
	}
	function getCuentaContableDeCaja(){
		return $this->mCuentaDeCaja;
	}	
	function setEndSession(){
		
		$oficial 		= $this->getUserInfo();
		$iduser 		= $this->mCodeUser;
		$oficial 		= elusuario($iduser);
		/**
		 * Eliminar al Usuarios de Conectados
		 */
		$mduser			= md5($iduser);

		$sqlDELCnn 		= "DELETE FROM usuarios_web_connected WHERE webid='$mduser' ";
		my_query($sqlDELCnn);

		saveError(10, $this->mCodeUser, "$oficial  cerro sesion");
		// sin variables
		session_unset();
		//$_SESSION = array();
		session_destroy();		
	}
	function getDatosInArray(){
		if ( $this->mUserIniciado == false ){
			$this->init();
		}
		return $this->mDatosInArray;
	}
	function getNombreCompleto(){
		$D		= $this->getDatosInArray();
		$nombre	= $D["nombres"] . " " . $D["apellidopaterno"] . " " . $D["apellidomaterno"];
		return $nombre;
	}
	function setDelete($NewUser = false){
		$NewUser	= ($NewUser == false) ? getUsuarioActual() : $NewUser;
		$OldUser	= $this->mCodeUser;
		
		$sqlST	= "SHOW TABLES IN " . MY_DB_IN;
		$rs		= getRecordset($sqlST, cnnGeneral() );
		$msg	= "============= \tCAMBIANDO DATOS DEL  USUARI $OldUserO AL USUARIO $NewUser\r\n";

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
			$usr	= md5($this->mID);
			$pwd	= md5($this->mPWD );			
		} else {
			$usr	= md5($_SESSION["SN_b80bb7740288fda1f201890375a60c8f"]);
			$pwd	= md5($_SESSION["SN_0d35c1f17675a8a2bf3caaacd59a65de"]);
		}
		$ctx	= md5("$usr|$pwd");
		return $ctx;
	}
	//XXX : terminar Actualizar Por  Persona
	function setActualizarPorPersona(){
		$xPer	= new cSocio($this->mClaveDePersona);
		$ready	= false;
		if($xPer->init() == true){
			$xT		= new cT_03f996214fba4a1d05a68b18fece8e71();
			$xT->setData($this->mDatosInArray);
			$xT->apellidomaterno( $xPer->getApellidoPaterno() );
			$xT->apellidomaterno( $xPer->getApellidoMaterno() );
			$xT->nombres( $xPer->getNombre() );
			$ready	= $xT->query()->update()->save( $this->getID() );
			if($ready == false){
				$this->mMessages .= "ERROR\tAl actualizar el usuario " . $this->getID() . " Desde la persona " . $this->mClaveDePersona . "\r\n";
			} else {
				$this->mMessages .= "OK\tSe actualizo el usuario " . $this->getID() . " desde la persona " . $this->mClaveDePersona . "\r\n";
				$ready			= true;
			}
		}
		return $ready;
	}
}
class cSystemPermissions{
	private $mMessages			= "";
	private $mID				= false; 
	
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
	
	function __construct($id = false){ 	$this->mID		= $id;	}
	function setRestore($Fecha = false){

		$gestor = @fopen(PATH_BACKUPS . "safeosms-permissions-" . getSucursal() . "-$Fecha.sbk", "r");
		$msg	= "";
		$iReg 	= 0;
		$cT		= new cTipos();

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
							my_query($sql);
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
		$rs		= getRecordset($sql);
		while($rw = mysql_fetch_array($rs) ){
			$text	= $rw["idgeneral_menu"] . "|" . htmlentities($rw["menu_rules"]) . "\r\n"; 
		 	@fwrite($xLog, $text);
		}
		@fclose($xLog);
		
		return $mFile;		
	}
	function getMessages($put =OUT_TXT){
		$xH		= new cHObject();
		return $xH->Out($this->mMessages, $put);
	}
	function getOMenu($id = false){
		$xMen	= new cGeneral_menu();
		$xMen->setData($xMen->query()->initByID($id) );
		return $xMen;
	}
	function setAgregarPermiso($idgrupo, $id = false){
		$id		= ($id == false) ? $this->mID : $id;
		$D		= $this->getOMenu($id);
		$alls	= explode(",", $D->menu_rules()->v());
		//setLog($D->menu_rules()->v());
		if( (array_search($idgrupo . $this->mRW, $alls) === false) AND (array_search($idgrupo . $this->mRO, $alls) === false) ){
			$alls[]		= $idgrupo . $this->mRW;
		}
		$permisos	= implode(",", $alls);
		$this->mMessages	.= "OK\t$id\tAgregar.- Cambiar permisos de " . $D->menu_rules()->v() .  " a $permisos\r\n";
		return $this->setPermisos($permisos, $id);
	}
	function setEliminarPermiso($idgrupo, $id = false){
		$id		= ($id == false) ? $this->mID : $id;
		$D		= $this->getOMenu($id);
		$alls	= explode(",", $D->menu_rules()->v());
		$nreg	= array();
		foreach ($alls as $ix => $regla){
			if(($regla == $idgrupo . $this->mRW) OR ($regla == $idgrupo . $this->mRO)){
				
			} else {
				$nreg[]		= $regla;
			}
		}
		$permisos	= implode(",", $nreg);
		$this->mMessages	.= "WARN\t$id\tAgregar.- Cambiar permisos de " . $D->menu_rules()->v() .  " a $permisos\r\n";
		return $this->setPermisos($permisos, $id);
	}	
	function setPermisos($permisos, $id = false){
		$msg	= "";
		$id		= ($id == false) ? $this->mID : $id;
		$mql	= new MQL();
		$gl		= $this->mGlobalM;
		//actualizar padres y parent
		$sql 	= "UPDATE general_menu set menu_rules='$permisos' WHERE (menu_parent != $gl) AND (menu_parent=$id OR `idgeneral_menu` = $id )";
		$ics	= my_query($sql);
		$msg	.= "OK\tAplicacion Recursiva de $id " . $ics["rows"] . "\r\n";
		
		$sql2	= "SELECT * FROM general_menu WHERE menu_parent=$id";
		$rs		= $mql->getDataRecord($sql2);
				
		$xMen	= new cGeneral_menu();
		foreach ($rs as $row){
			$xMen->setData($row);
			$ide	= $xMen->idgeneral_menu()->v();
			$sqlP 	= "UPDATE general_menu SET menu_rules='$permisos' WHERE menu_parent=$ide OR `idgeneral_menu` = $ide ";
			$idcs 	= my_query($sqlP);
			$msg	.= "OK\tSubmenu $ide padre $id " . $idcs["rows"] . "\r\n";
		}
		$this->mMessages	.= $msg;
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
		$sql 	= "UPDATE general_menu set menu_rules='99@rw'";
		$inf	= my_query($sql);
		$this->mMessages	.= "OK\tDar permisos solo a ROOT (" . $inf["rows"] . ")\r\n";
		$sqlD 	= "UPDATE general_menu SET menu_rules='2@rw,3@rw,4@rw,5@rw,6@rw,7@rw,8@rw,9@rw,10@rw,11@rw,12@rw,13@rw,14@rw,15@rw,99@rw' WHERE menu_parent = "  . $this->mGlobalM .  " ";
		$inf2	= my_query($sqlD);
		$this->mMessages	.=  "OK\tPermisos establecidos por defecto afectado (" . $inf2["rows"] . ")\r\n";		
		
	}
	function setLiberar(){
		$sql = "UPDATE general_menu SET menu_rules='2@rw,3@rw,4@rw,5@rw,6@rw,7@rw,8@rw,9@rw,10@rw,11@rw,12@rw,13@rw,14@rw,15@rw,99@rw' ";
		my_query($sql);
		$this->mMessages	.=  "OK\tTodos los permisos se han reseteado\r\n";
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
        
        
?>