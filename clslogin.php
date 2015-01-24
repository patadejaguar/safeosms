<?php
include_once("core/go.login.inc.php");
include_once("core/core.error.inc.php");

$mKey		= ROTTER_KEY;
$fileSet	= "index.xul.php";
$arrFiles	= array(
					1 => "./frmutils/cierre_de_colocacion.frm.php?k=" . MY_KEY,
					2 => "./frmutils/cierre_de_captacion.frm.php?k=" . MY_KEY,
					3 => "./frmutils/cierre_de_seguimiento.frm.php?k=" . MY_KEY,
					4 => "./frmutils/cierre_de_contabilidad.frm.php?k=" . MY_KEY,
					5 => "./frmutils/cierre_de_sistema.frm.php?k=" . MY_KEY
				);
if(isset($_POST["u$mKey"]) and isset($_POST["p$mKey"])) {

	//definir sucursal
	if(isset($_POST["idsucursal"]) ){
		$_SESSION["sucursal"]			= $_POST["idsucursal"];
	}

$iUser		= "";
$iPwd		= "";

settype($iUser, "string");
settype($iPwd, "string");

$iUser 		=	$_POST["u$mKey"];
$iPwd 		= 	$_POST["p$mKey"];

if ( $iUser == TASK_USR ){
	$mOP	= $_GET["o"];
	if ( isset($mOP) ){
		$fileSet	= $arrFiles[$mOP];
	}
}

$iUser		= addslashes($iUser);
$pWd		= addslashes($iPwd);

	if (!$iUser) {
		saveError(98, session_id() , "$iUser - Usuario sin definir ");
		session_unset();
		// Finalmente, destruye la sesi&oacute;n
		session_destroy();
		header ("location:inicio.php");
		exit();
	}

	if (!$iPwd) {
		saveError(98, session_id() , "$iUser - Password sin definir ");
		session_unset();
		// Finalmente, destruye la sesi&oacute;n
		session_destroy();
		header ("location:inicio.php");
		exit();
	}


	$cUser = goLogged("nombreusuario", $iUser);

	if (!$cUser) {
		saveError(98, session_id() , "$iUser - Usuario sin Definir");
		session_unset();
		// Finalmente, destruye la sesi&oacute;n
		session_destroy();
		header ("location:inicio.php");
		exit();
	}

	$cPwd 		= goLogged("contrasenna", $iUser);
	$nivel 		= goLogged("niveldeacceso", $iUser);
	$ciduser 	= goLogged("idusuarios", $iUser);
	$expira		= goLogged("expira", $iUser);

	if (FORCE_PASSWORD_EXPIRE == true){
		if ( strtotime( date("Y-m-d") ) > strtotime($expira) ){
			saveError(10, session_id() , "El Usuario $iUser NO Inicio Sesion pues su contrasenna Expiro en $expira");

			session_unset();
			header ("location:inicio.php");
		}
	}
	if ( (FORCE_SESSION_LOCKED == true) AND ( $iUser != TASK_USR ) ){
		/**
		 * verificar si el usuario esta conectado
		 * verificar si el usuario ya tiene ID
 		*/
		if ( getStatusConnected($ciduser) == true ){
				saveError(98, session_id() , "El Usuario $iUser esta Conectado en otra Terminal");
				session_unset();
				// 	Finalmente, destruye la sesion
				session_destroy();
				header ("location:inicio.php?msg=USTED_INICIO_SESSION_EN_OTRA_TERMINAL");
				exit();
		}

	}
		if($iPwd == $cPwd) {
			$_SESSION["en_depurado"] 	= false;
			$_SESSION["log_id"] 		= $ciduser;
			$_SESSION["log_user"] 		= $cUser;
			$_SESSION["log_nivel"] 		= $nivel;

			//--------------------------------------------------------

			//define SN_0a744893951e0d1706ff74a7afccf561 == USR
			//define SN_b80bb7740288fda1f201890375a60c8f == id
			//define SN_d567c9b2d95fbc0a51e94d665abe9da3 == nivel
			//SN_0d35c1f17675a8a2bf3caaacd59a65de		= Password
			$_SESSION["SN_b80bb7740288fda1f201890375a60c8f"] = $ciduser;
			$_SESSION["SN_d567c9b2d95fbc0a51e94d665abe9da3"] = $nivel;
			$_SESSION["SN_0d35c1f17675a8a2bf3caaacd59a65de"] = $iPwd;
			$_SESSION["SN_0a744893951e0d1706ff74a7afccf561"] = $iUser;		//Nombre de Usuario
			//--------------------------------------------------------
				if ($_SESSION["log_nivel"] == 99) {
						$_SESSION["en_depurado"] = true;
				}
			saveError(10, session_id() , "El Usuario $iUser Inicio Sesion ");
			/**
			 * Agregar a Conectados al usuario
			 */
			 $mdID = md5($ciduser);

			//option1 = fecha
			//option2 = hora
			$opt1		= date("Y-m-d");
			$opt2		= date("H:i:s");

			$sqlNCnn = "INSERT INTO usuarios_web_connected
						(webid, option1, option2)
    					VALUES
						('$mdID', '$opt1', '$opt2')";

					$xcnn = new mysqli(WORK_HOST, USR_ERROR, PWD_ERROR, MY_DB_IN, PORT_HOST);
					$xcnn->query($sqlNCnn);
					$xcnn->close();

			header ("location:$fileSet");
			//====== Eliminar datos del usuario
			unset($ciduser); unset($nivel); unset($iPwd); unset($iUser);
		} else {
			saveError(98, session_id() , "$iUser Datos Incorrectos ");
			header ("location:inicio.php");
			exit();
		}

} else {
			saveError(98, session_id() , "Faltan Datos para Iniciar session");
			header ("location:inicio.php");
			exit();
}
?>
