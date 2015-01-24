<?php
//=====================================================================================================
//=====>	INICIO_H
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	$theFile					= __FILE__;
	$permiso					= getSIPAKALPermissions($theFile);
	if($permiso === false){		header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//<=====	FIN_H
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
$xHP		= new cHPage("Alta de Usuarios del Sistema", HP_FORM);
$xHP->setIncludes();

	
$oficial 	= elusuario($iduser);
$jxc 		= new TinyAjax();


function jsaGetUInfo($user){
	$xUsr	= new cSystemUser($user, false);
	$xUsr->init();
	$VD		= $xUsr->getDatosInArray(); //getSAFEUserInfoByName($user);
	$tab 	= new TinyAjaxBehavior();
	if ( isset($VD["idusuarios"]) ){
		
		//$tab -> add(TabSetValue::getBehavior('idNombreCompleto', $VD["nombres"] ));
		//$tab -> add(TabSetValue::getBehavior('idApellidoPaterno', $VD["apellidopaterno"] ));
		//$tab -> add(TabSetValue::getBehavior('idApellidoMaterno', $VD["apellidomaterno"] ));
		$tab -> add(TabSetValue::getBehavior('idNivelAcceso', $VD["niveldeacceso"] ));
		$tab -> add(TabSetValue::getBehavior('idSucursal', $VD["sucursal"] ));
		$tab -> add(TabSetValue::getBehavior('idUsuario', $VD["idusuarios"] ));
		$tab -> add(TabSetValue::getBehavior('idPuesto', $VD["puesto"] ));
		$tab -> add(TabSetValue::getBehavior('idsocio', $xUsr->getClaveDePersona() ));
	} else {
		$tab -> add(TabSetValue::getBehavior('idUsuario', 0 ));
	}
	return $tab -> getString();
}

function jsaSetBaja($nombreusuario){
	$sql	= "UPDATE t_03f996214fba4a1d05a68b18fece8e71 SET  estatus='" . SYS_USER_ESTADO_BAJA . "' WHERE f_28fb96d57b21090705cfdf8bc3445d2a='$nombreusuario' ";
	my_query($sql);
}
function jsSetSuspender($nombreusuario){
	$sql	= "UPDATE t_03f996214fba4a1d05a68b18fece8e71 SET  estatus='" . SYS_USER_ESTADO_BAJA . "' WHERE f_28fb96d57b21090705cfdf8bc3445d2a='$nombreusuario' ";
	my_query($sql);	
}
$jxc ->exportFunction('jsaGetUInfo', array('idNombreUsuario'));
$jxc ->exportFunction('jsaSetBaja', array('idNombreUsuario'));

$jxc ->process();
//$PubKey	= md5(MY_KEY . $iduser . date("YmdH"));

$operation		= (!isset($_GET["o"]) ) ? 99 : $_GET["o"] ;
$msg			= (!isset($_GET["m"]) ) ? "" : $_GET["m"] ;

$xHP->addJsFile("../js/md5.js");

echo $xHP->getHeader();

$jxc ->drawJavaScript(false, true);
echo $xHP->setBodyinit();

if ($operation == 99 ) {
	$xBtn		= new cHButton("iact");
	$oFRM		= new cHForm("frmAltaUsuarios", "altausuarios.frm.php?o=0");
	$oFRM->setTitle($xHP->getTitle());
	//<legend>M&oacute;dulo de Alta / Actualizacion de Usuarios del Sistema</legend>
	
	$xText2		= new cHText("");
	$xText		= new cHText("");
	$oFRM->addPersonaBasico();
	
	$xText2->addEvent("jsaGetUInfo(); posAction()", "onchange");
	$oFRM->addHElem( $xText2->get("idNombreUsuario", "", $xText->lang("NOMBRE DE", "USUARIO")) );
		
	$oFRM->addHElem( $xText2->getPassword("idContrasenna",  $xText->lang("PASSWORD"), "") );
	$xText2->addEvent("evalPWD()", "onblur");
	$oFRM->addHElem( $xText2->getPassword("idContrasenna2", $xText->lang("CONFIRME", "PASSWORD"), "") );
	$xSel	= new cHSelect();
	
	
	$xSel->addEvent("update_puesto", "onchange");
	$oFRM->addHElem( $xSel->get("idNivelAcceso", $xText->lang("TIPO DE", "ROL"), "2", TCATALOGOS_USUARIOS_ROLES) );
	$xText->setClearEvents();
	$xText->setClearProperties();
	
	$oFRM->addHElem( $xText->get("idPuesto", "", $xText->lang("PUESTO")) );

	$xSel->setClearEvents();
	$oFRM->addHElem( $xSel->get("idSucursal", $xText->lang("SUCURSAL"), getSucursal(), TCATALOGOS_ENTIDAD_SUCURSALES) );	
	
	$oFRM->addToolbar($xBtn->getRegresar("", true) );
	
	$oFRM->addToolbar($xBtn->getBasic("TR.Baja", "jsSetBaja()", "baja", "idcmdbaja", false) );
	
		
	$oFRM->addHTML("<p class='aviso' id='thAction'>$msg</p>");
	$oFRM->addHTML('<input type="hidden" id="idUsuario" name="idUsuario" />');
	
	$oFRM->addSubmit();
	

		
	echo $oFRM->get();

	$xJs		= new jsBasicForm($oFRM->getName());
	echo $xJs->get();
} else {
	$sucess					= true;
	$msg					= "";
	//O = ALta , 1=Update
	$contrasenna			= $_POST["idContrasenna"];

	$contrasenna 			= trim(md5(substr($contrasenna,0,20)));
	$idusuario 				= ( isset($_POST["idUsuario"])  ) ? $_POST["idUsuario"] : 0;

	$xTi					= new cTipos();

	$clavedepersona			= parametro("idsocio", DEFAULT_SOCIO, MQL_INT);
	$nombreusuario 			= $_POST["idNombreUsuario"];
	$nombrecompleto 		= "";
	$apellidomaterno 		= "";
	$apellidopaterno 		= "";
	
	if($clavedepersona == DEFAULT_SOCIO){
		$sucess				= false;
		$msg 		.= "ERROR\tEl Usuario debe estar relacionado con una persona\r\n";
	} else {
		$xSoc					= new cSocio($clavedepersona);
		$xSoc->init();
		$nombrecompleto 		= $xSoc->getNombre();
		$apellidomaterno 		= $xSoc->getApellidoMaterno();
		$apellidopaterno 		= $xSoc->getApellidoPaterno();
	}

	$nivelacceso 			= $_POST["idNivelAcceso"];
	$puesto 				= $_POST["idPuesto"];
	$estatus 				= "activo";
	$sucursal 				= $_POST["idSucursal"];
	$FechaDeExpiracion		= sumardias(fechasys(), EXPIRE_PASSWORDS_IN_DAYS);

	$xBtn		= new cHButton("");
	$oFRM		= new cHForm("frmausuarios", "altausuarios.frm.php");
	
	foreach($_POST as $campo => $valor){
		//echo "$campo === $valor<br />";
	}
		if(strlen($nombreusuario) > 20){
			$msg 		.= "ERROR\tEl Nombre de Usuario no puede tener mas de 20 caracteres\r\n";
			$sucess		= false;			
		}
		if(strlen($nombreusuario) < 4){
			$msg 		.= "ERROR\tEl Nombre de Usuario no puede tener menos de 4 caracteres\r\n";
			$sucess		= false;			
		}
		if ($nivelacceso >= $_SESSION["SN_d567c9b2d95fbc0a51e94d665abe9da3"]) {
			$msg 		.= "ERROR\tUsted no Puede Asignar Permisos Mayores a su Nivel\r\n";
			$sucess		= false;
		}
		if( $operation == 1 AND ( ($idusuario	==  0) OR ($idusuario	==  false ) OR ($idusuario	==  "") )){
			$msg 		.= "ERROR\tNo se Puede Actualizar un USUARIO sin Codigo\r\n";
			$sucess		= false;			
		}
		if($sucess		== true ){
			switch ($operation){
				case 0:
					$xUsr		= new cSystemUser();
					$xUsr->add($nombreusuario, $contrasenna, $nivelacceso,
								$nombrecompleto, $apellidopaterno, $apellidomaterno, $puesto, $FechaDeExpiracion, $estatus, "", $sucursal, false, $clavedepersona);
					$msg		.= $xUsr->getMessages("txt");				
					break;
				case 1:
					$sqlset 	= "
					f_34023acbff254d34664f94c3e08d836e='$contrasenna',
					nombres = '$nombrecompleto',
					apellidopaterno = '$apellidopaterno',
					apellidomaterno = '$apellidomaterno',
					puesto = '$puesto',
					f_f2cd801e90b78ef4dc673a4659c1482d = $nivelacceso,
					estatus = '$estatus',
					sucursal ='$sucursal',
					date_expire ='$FechaDeExpiracion',
					`codigo_de_persona` = $clavedepersona
					";
	
					$sqli 		= "UPDATE t_03f996214fba4a1d05a68b18fece8e71 SET $sqlset
										WHERE f_28fb96d57b21090705cfdf8bc3445d2a='$nombreusuario' ";
					$inStat		= my_query($sqli);
					//exit($sqli);
					
					if($inStat["stat"] == false){
						$msg 	.= "ERROR\tERROR EN LA ACTUALIZACION DEL USUARIO; EL SISTEMA DEVOLVIO " . $inStat["error"] . "\r\n ";
					} else {
						$msg 	.= "SUCESS\tLA ACTUALIZACION DEL USUARIO SE HA EFECTUADO SATISFACTORIAMENTE\r\n";
					}				
					break;
					
				default:
					$msg 		.= "ERROR\tNO HA ESTABLECIDO UNA ACCION\r\n";
					break;
			}
			
			$oFRM->addToolbar($xBtn->getRegresar("", true) );
			$oFRM->addToolbar( $xBtn->getSalir("", true) );
			
			
			$oFRM->addAviso($msg);			
			
		} else {
			$oFRM->addAviso($msg);
		}
		echo $oFRM->get();
}
?>
<script  >
jsWorkForm		= document.frmAltaUsuarios;

function update_puesto() {
		idopt 			= document.getElementById("idNivelAcceso").selectedIndex;
		document.getElementById("idPuesto").value = document.getElementById("idNivelAcceso").options[idopt].text;
}
function posAction(){
	setTimeout("evaluateAction()", 500);
}
function evaluateAction(){
	var idus	= parseInt(document.getElementById("idUsuario").value);
	if ( !isNaN(idus) && (idus != 0) && (idus != "") ){
		jsWorkForm.action			= "./altausuarios.frm.php?o=1";
		
		document.getElementById("thAction").innerHTML = "Actualizar Datos de Este Usuario<br>Si no Cambia la Contrase&ntilde;a Dejela en Blanco";
	} else {
		jsWorkForm.action 		= "./altausuarios.frm.php?o=0";
		document.getElementById("thAction").innerHTML = "Agregar Nuevo Usuario";
	}

}
function evalPWD(){
	var pwd1 	= hex_md5(document.getElementById("idContrasenna").value);
	var pwd2	= hex_md5(document.getElementById("idContrasenna2").value);
	if ( pwd1 != pwd2 ){
		alert("La Contrase%a No es la Misma!!!");
		document.getElementById("idContrasenna").focus();
		document.getElementById("idContrasenna2").value = "";

	} else {
		if (pwd1 == "" || pwd2 == "" ){
			alert("Faltan Datos de la Contrase%a!!!");
			document.getElementById("idContrasenna").focus();
			document.getElementById("idContrasenna2").value = "";
		} else {
			//document.getElementById("thAction").innerHTML = "<a class='button' onclick=\"document.eltrue.submit()\">Guardar Informacion Actualizada</p>";
		}
	}
}
function jsSetBaja(){
	var si	= confirm("DESEA DAR DE BAJA A ESTE USUARIO?");
	if(si){ jsaSetBaja(); }
}
</script>
<?php 
echo $xHP->setBodyEnd();
$xHP->end();
?>
