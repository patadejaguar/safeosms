<?php
@session_start();

require_once ("core.config.inc.php");
require_once ("core.error.inc.php");

function goLogged($logcampo, $username) {
$TID 				= session_id();

		if( (isset($logcampo) ) AND (isset($username))){
				$loguser 	= USR_LOGIN;
				$logpwd 	= PWD_LOGIN;
				$logdb 		= MY_DB_IN;
				$loghost 	= WORK_HOST;

				$logcnn = mysql_connect($loghost, $loguser, $logpwd);
					if (!$logcnn) {
							header ("location:inicio.php?msg=FALTAN_DATOS");
							exit();
						}

				$logdbo = mysql_select_db($logdb, $logcnn);
				$sqllog = "SELECT * FROM usuarios
								WHERE nombreusuario='$username'
								AND estatus=\"activo\"
								LIMIT 0,1";
				$rslog = mysql_query($sqllog, $logcnn);
						//
						if ( !isset($rslog) ) {
								saveError(98, $TID, "$sqllog " . mysql_error());
								session_unset();
								//Finalmente, destruye la sesi&oacute;n
								session_destroy();
								header("location:inicio.php?msg=NO_PERMITIDO");
								exit();
						}
					//
					if (mysql_num_rows($rslog) < 1) {
						saveError(98, $TID, $sqllog);
						session_unset();
						//Finalmente, destruye la sesion
						session_destroy();
						header("location:inicio.php?msg=NO_EXISTE_EL_USUARIO");
						exit();
					}
					return mysql_result($rslog, 0, $logcampo);

				@mysql_free_result($rslog);
				@mysql_close($logcnn);
				unset($logcnn);
				unset($logdbo);
				unset($rslog);
		} else {
						saveError(98, $TID, "Intento Fallido de Logearse");
						session_unset();
						// Finalmente, destruye la sesi&oacute;n
						session_destroy();
						header ("location:inicio.php");
						exit;
		}
}

function getStatusConnected($iduser) {
$stat	= true;

	if( isset($iduser) ){
	 		$loguser 	= USR_LOGIN;
			$logpwd 	= PWD_LOGIN;
			$logdb 		= MY_DB_IN;
			$loghost 	= WORK_HOST;

			$logcnn = mysql_connect($loghost, $loguser, $logpwd);
				if (!$logcnn) {
						$stat = true;
					}

			$logdbo = mysql_select_db($logdb, $logcnn);

			$iduser	= md5($iduser);
			$fecha	= date("Y-m-d");
			$sqllog = "SELECT count(webid) AS 'connected'
						FROM usuarios_web_connected
						WHERE webid='$iduser'
						AND
						option1='$fecha' ";
			$rslog = mysql_query($sqllog, $logcnn);
				if (!isset($rslog)) {
					$stat = false;
				} else {
					$counts = mysql_result($rslog, 0, "connected");

					if ( !isset($counts) or $counts == 0 ){
						$stat = false;
					} else {
						$stat = true;
					}
				}

			@mysql_free_result($rslog);
			@mysql_close($logcnn);
			unset($logcnn);
			unset($logdbo);
			unset($rslog);

	} else {
		$stat = true;
	}

return $stat;
}

function getSIPAKALPermissions($myFile){
	//Tratar el __FILE__, eliminar el directorio
	$vUno 				= substr_count($myFile, "/");
	$vDos 				= substr_count($myFile, "\\");
	$notes				= "";
	$pUSRID				= ( isset($_SESSION["SN_b80bb7740288fda1f201890375a60c8f"]) ) ? $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"] : 0;
	$pUSRNivel 			= ( isset($_SESSION["SN_d567c9b2d95fbc0a51e94d665abe9da3"]) ) ? $_SESSION["SN_d567c9b2d95fbc0a51e94d665abe9da3"] : 0;
	$pUSRPWD			= ( isset($_SESSION["SN_0d35c1f17675a8a2bf3caaacd59a65de"]) ) ? $_SESSION["SN_0d35c1f17675a8a2bf3caaacd59a65de"] : "";
	$mUSR				= ( isset($_SESSION["SN_0a744893951e0d1706ff74a7afccf561"]) ) ? $_SESSION["SN_0a744893951e0d1706ff74a7afccf561"] : "";
	//
	//obtener variables por CONTEXT
	if(isset($_REQUEST)){
		if(isset($_REQUEST["ctx"])){
			$ctx	= md5($_REQUEST["ctx"]);
			$sql	= "SELECT	`t_03f996214fba4a1d05a68b18fece8e71`.*
			FROM `t_03f996214fba4a1d05a68b18fece8e71` WHERE 
			(MD5(MD5(CONCAT(MD5(`t_03f996214fba4a1d05a68b18fece8e71`.`idusuarios`) , '|', MD5(`t_03f996214fba4a1d05a68b18fece8e71`.`f_34023acbff254d34664f94c3e08d836e`)))) = '$ctx') 
			OR
			(MD5(MD5(CONCAT(MD5(`t_03f996214fba4a1d05a68b18fece8e71`.`f_28fb96d57b21090705cfdf8bc3445d2a`) , '|', MD5(`t_03f996214fba4a1d05a68b18fece8e71`.`f_34023acbff254d34664f94c3e08d836e`)))) = '$ctx')
			LIMIT 0,1 ";
			//$notes		= $sql;
			$xMQL				= new MQL();
			$data 	= $xMQL->getDataRecord($sql);
			foreach ($data as $rows){
				$pUSRID		= $rows["idusuarios"];
				$pUSRNivel	= $rows["f_f2cd801e90b78ef4dc673a4659c1482d"];
				$pUSRPWD	= $rows["f_34023acbff254d34664f94c3e08d836e"];
				$mUSR		= $rows["f_28fb96d57b21090705cfdf8bc3445d2a"];
				
				$_SESSION["SN_b80bb7740288fda1f201890375a60c8f"]	= $pUSRID;
				$_SESSION["SN_d567c9b2d95fbc0a51e94d665abe9da3"]	= $pUSRNivel;
				$_SESSION["SN_0d35c1f17675a8a2bf3caaacd59a65de"]	= $pUSRPWD;
				$_SESSION["SN_0a744893951e0d1706ff74a7afccf561"]	= $mUSR;
			}
			//
		}
	}

	$PUBLICSVC			= array("personas.svc.php" => true, "personas.actividades.economicas.php" => true,
							"listanegra.svc.php" => true, "equivalente.moneda.svc.php" => true,
							"cantidad_en_letras.php" => true
	);			//servicios publicos
	$PFile				= "";
	$myPermission 		= false;

	if($vUno>=1){
		$DCFile 	= explode("/", $myFile);
		$elems 		= count($DCFile) - 1;
		if($elems>=0){ $PFile 	= $DCFile[$elems]; }
	} else {
		$DCFile 	= explode("\\", $myFile);
		$elems 		= count($DCFile) - 1;
		if($elems>=0){ $PFile 	= $DCFile[$elems];	}
	}
	if(isset($PUBLICSVC[$PFile])){
		$myPermission = true;
		//setLog("Acceso Publico al Servicio $PFile");
	} else {
		$tmpPWD				= ( $mUSR != "" ) ? goLogged("contrasenna", $mUSR) : md5( session_id() ) ;
		if ( $tmpPWD != $pUSRPWD ){
		    $myPermission = false;
			//salvar el error
			saveError(98, session_id() , "NO HA DEFINIDO UNA SESSION PARA EL ARCHIVO $myFile $notes");
			//salir si no esta definida la session
			session_unset();
			// Finalmente, destruye la sesi&oacute;n
			session_destroy();
			header ("location:inicio.php");
			exit();
		}
	
		$myPermission 		= false;
		//checar si la variable esta inicializada
		//si no enviar un unsetsession
		if( isset($pUSRNivel) AND ($pUSRNivel > 0) ){
	
			$sqlRULES = "SELECT COUNT(idgeneral_menu) AS 'items', menu_rules FROM general_menu
						WHERE menu_file LIKE '%$PFile'
						AND (FIND_IN_SET('$pUSRNivel@rw', menu_rules)>0
						OR FIND_IN_SET('$pUSRNivel@ro', menu_rules)>0)
						
						/*LIMIT 0,1*/ ";
				//setLog($sqlRULES);
				$cnxT 					= mysql_connect(WORK_HOST, USR_PERMISSIONS, PWD_PERMISSIONS);
				$dbT 					= mysql_select_db(MY_DB_IN, $cnxT);
				$rsRULES 				= mysql_query($sqlRULES, $cnxT);
				if( !isset($rsRULES) ){ saveError(98, $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], mysql_error($cnxT));	}
			
				$aRULES 				= mysql_fetch_array($rsRULES);
				/**
				 * Verifica la autenticacion
				 * busca la pocision del permiso
				 * //
				 **/
				$mos 		= strtoupper(substr(PHP_OS, 0, 3));
				$myFile		= str_replace("/", "|", $myFile);
				$myFile		= str_replace("\\", "|", $myFile);
				$dFile		= explode("|", $myFile);
				$idfile		= sizeof($dFile) -1;
				$myFile		= $dFile[($idfile - 1)] . "/". $dFile[$idfile];  
				//DIRECTORY_SEPARATOR
				
				if( $aRULES["items"] == 0 ){
					$sqlA	= "INSERT INTO `general_menu` (`menu_title`, `menu_file`) VALUES ('$PFile', '$myFile')";
					@mysql_query($sqlA, $cnxT);
				}
			if ( ( !isset($aRULES["menu_rules"]) ) OR ( empty($aRULES["menu_rules"]) ) OR ( $aRULES["menu_rules"] == "" ) ){
				//saveError(97, $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], $sqlRULES);
				saveError(999, $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Acceso no permitido a :" . addslashes($myFile) . " $notes");
				$myPermission 		=  false;
			} else {
				$ARls = explode(",", $aRULES["menu_rules"]);
		
				if(in_array("$pUSRNivel@rw", $ARls)){
					$myPermission 	=  "ReadWrite";
				} else {
					$myPermission 	=  "ReadOnly";
				}
			}
			@mysql_free_result($rsRULES); @mysql_close($cnxT);
			unset($rsRULES); unset($cnxT); unset($dbT);
		} else {
			$myPermission = false;
			//salvar el error
			saveError(98, session_id() , "NO HA DEFINIDO UNA SESSION PARA EL ARCHIVO $myFile $notes\r\n");
			//salir si no esta definida la session
			session_unset();
			// Finalmente, destruye la sesi&oacute;n
			session_destroy();
			header ("location:inicio.php");
			exit();
		}
	}
		return  $myPermission;
}
?>