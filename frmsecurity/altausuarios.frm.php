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
	
//=====================================================================================================
$xHP		= new cHPage("TR.Agregar Usuario del Sistema", HP_FORM);
$xF			= new cFecha();
$msg		= "";
$jxc 		= new TinyAjax();


function jsaGetUInfo($user){
	$xUsr	= new cSystemUser($user, false);
	$xUsr->init();
	$VD		= $xUsr->getDatosInArray(); //getSAFEUserInfoByName($user);
	$tab 	= new TinyAjaxBehavior();
	if ( isset($VD["idusuarios"]) ){
		
		$tab -> add(TabSetValue::getBehavior('idNivelAcceso', $VD["niveldeacceso"] ));
		$tab -> add(TabSetValue::getBehavior('idSucursal', $VD["sucursal"] ));
		$tab -> add(TabSetValue::getBehavior('idUsuario', $VD["idusuarios"] ));
		$tab -> add(TabSetValue::getBehavior('idPuesto', $VD["puesto"] ));
		if($xUsr->getClaveDePersona() > DEFAULT_SOCIO){
			$tab -> add(TabSetValue::getBehavior('idsocio', $xUsr->getClaveDePersona() ));
		}
	} else {
		$tab -> add(TabSetValue::getBehavior('idUsuario', 0 ));
	}
	return $tab -> getString();
}

function jsaSetBaja($nombreusuario){
	$xUsr	= new cSystemUser($user, false);
	if($xUsr->init() == true){
		$xUsr->setBaja();
	}
}
function jsSetSuspender($nombreusuario){
	$xUsr	= new cSystemUser($user, false);
	if($xUsr->init() == true){
		$xUsr->setSuspender();
	}	
}
$jxc ->exportFunction('jsaGetUInfo', array('idNombreUsuario'));
$jxc ->exportFunction('jsaSetBaja', array('idNombreUsuario'));

$jxc ->process();
//$PubKey	= md5(MY_KEY . $iduser . date("YmdH"));
$xHP->addJsFile("../js/md5.js");
$xHP->init();

$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xFRM		= new cHForm("frmAltaUsuarios", "altausuarios.frm.php");


$xUser		= new cSystemUser();
$xUser->init();
$iduser		= $xUser->getID();
if($xUser->getPuedeAgregarUsuarios() == false){
	$xErr	= new cErrorCodes();
	$xHP->goToPageError($xErr->SIN_PERMISO_REGLA);
} else {

	if ($action == SYS_NINGUNO ) {
		$xFRM->OHidden("action", MQL_MOD);
		$xFRM->setTitle($xHP->getTitle());
		
		$xFRM->addGuardar();
		/*$xFRM->OButton("TR.Baja", "jsSetBaja()", $xFRM->ic()->BLOQUEAR);*/
		/*$xFRM->OButton("TR.Suspender", "jsSetSuspender()", $xFRM->ic()->BLOQUEAR);*/
		//$xFRM->addAtras();
		
		$xText2	= new cHText("");
		$xText2->setDiv13();
		
		$xText	= new cHText("");
		$xSel	= new cHSelect();
		
		$xFRM->addPersonaBasico();
		
		$xText2->addEvent("posAction()", "onchange");
		$xFRM->addHElem( $xText2->get("idNombreUsuario", "", $xText->lang("NOMBRE DE", "USUARIO")) );
			
		$xFRM->addHElem( $xText2->getPassword("idContrasenna",  $xText->lang("PASSWORD"), "") );
		$xText2->addEvent("evalPWD()", "onblur");
		$xFRM->addHElem( $xText2->getPassword("idContrasenna2", $xText->lang("CONFIRME", "PASSWORD"), "") );
		
		
		
		$xSel->addEvent("update_puesto", "onchange");
		$xFRM->addHElem( $xSel->get("idNivelAcceso", $xText->lang("TIPO DE", "ROL"), "2", TCATALOGOS_USUARIOS_ROLES) );
		$xText->setClearEvents();
		$xText->setClearProperties();
		
		$xFRM->addHElem( $xText->get("idPuesto", "", $xText->lang("PUESTO")) );
	
		$xSel->setClearEvents();
		
		$xSelSuc	= $xSel->getListaDeSucursales("idSucursal", getSucursal());
		
		
		$xFRM->addHElem( $xSelSuc->get(true) );	
		
	
		if(MODULO_CONTABILIDAD_ACTIVADO == true){
			$xFRM->addHElem($xText->getDeCuentaContable("idcuentacontable", CUENTA_CONTABLE_EFECTIVO, false, CUENTA_CONTABLE_EFECTIVO, "TR.CUENTA_CONTABLE DE CAJA") );
		} else {
			$xFRM->OHidden("idcuentacontable", CUENTA_CONTABLE_EFECTIVO);
		}
		
		$xFRM->OCheck("TR.CORPORATIVO", "corporativo");
		
			
		$xFRM->addHTML("<p class='aviso' id='thAction'>$msg</p>");
		$xFRM->addHTML('<input type="hidden" id="idUsuario" name="idUsuario" />');
		
	} else {
		$sucess					= true;
		
		//O = ALta , 1=Update
		$contrasenna			= parametro("idContrasenna", "", MQL_RAW);
		$tam_pass				= strlen($contrasenna);
		//$contrasenna 			= trim(md5(substr($contrasenna,0,20)));
		$idusuario 				= parametro("idUsuario", "", MQL_INT);// ( isset($_POST["idUsuario"])  ) ? $_POST["idUsuario"] : 0;
		$clavedepersona			= parametro("idsocio", DEFAULT_SOCIO, MQL_INT);
		$nombreusuario 			= parametro("idNombreUsuario", "", MQL_RAW); //$_POST["idNombreUsuario"];
		$tam_nombreuser			= strlen($nombreusuario);
		$nivelacceso 			= parametro("idNivelAcceso",0, MQL_INT);
		$puesto 				= parametro("idPuesto", "", MQL_STRING);
		$sucursal 				= parametro("idSucursal", "matriz", MQL_RAW);
		$cuentacontable			= parametro("idcuentacontable",0, MQL_INT);
		$corporativo			= parametro("corporativo", false, MQL_BOOL);
		
		if($clavedepersona <= DEFAULT_SOCIO){
			$sucess				= false;
			$msg 				.= "ERROR\tEl Usuario debe estar relacionado con una persona\r\n";
		} else {
	
			
		}
		//$xBtn					= new cHButton("");
		///$xFRM					= new cHForm("frmausuarios", "altausuarios.frm.php");
		
	
			if($tam_nombreuser > 40){
				$msg 		.= "ERROR\tEl Nombre de Usuario no puede tener mas de 20 caracteres\r\n";
				$sucess		= false;			
			}
			if($tam_nombreuser < 4 ){
				$msg 		.= "ERROR\tEl Nombre de Usuario no puede tener menos de 4 caracteres\r\n";
				$sucess		= false;			
			}
			if ($nivelacceso > $_SESSION["SN_d567c9b2d95fbc0a51e94d665abe9da3"] AND MODO_DEBUG == false) {
				$msg 		.= "ERROR\tUsted no Puede Asignar Permisos Mayores a su Nivel\r\n";
				$sucess		= false;
			}
	
			if($sucess		== true ){
				$xUsr		= new cSystemUser($nombreusuario, false);
				if($xUsr->init() == true){
					//Actualizar
					//nombre de usuario
					if($xUsr->getNombreDeUsuario() != $nombreusuario){
						$xUsr->setNombreUsuario($nombreusuario);
					}
					//pass de usuario.- comparar
					if($xUsr->getComparePassword(md5($contrasenna)) == false AND $tam_pass > 4){
						$xUsr->setPassword($contrasenna);
					} else {
						$msg 		.= "WARN\tEl PAssword no se actualiza\r\n";
					}
					//cuenta contable
					$xUsr->setCuentaContableDeCaja($cuentacontable);
					
					//clave de persona
					if($xUsr->getClaveDePersona() !== $clavedepersona){
						$xUsr->setCodigoDePersona($clavedepersona);
					}
					$xUsr->setSucursal($sucursal);
					$xUsr->setPuesto($puesto);
					if($nivelacceso > 0){
						$xUsr->setNivelAcceso($nivelacceso);
					}
				} else {
					$FechaDeExpiracion	=$xF->setSumarDias(EXPIRE_PASSWORDS_IN_DAYS, $xF->get());
					$xUsr->add($nombreusuario, $contrasenna, $nivelacceso,"", "", "", $puesto, $FechaDeExpiracion, "", "", $sucursal, false, $clavedepersona);
					if($xUsr->init() == true){
						$xUsr->setActualizarPorPersona();
						$xUsr->setCuentaContableDeCaja($cuentacontable);
						if($corporativo == true){
							$xUsr->setEsCorporativo();
						}
					}			
				}
				$xFRM->addAvisoRegistroOK($msg);
				$xFRM->addCerrar();
			} else {
				$xFRM->addAvisoRegistroError($msg);
				$xFRM->addAtras();
			}
			
			//
			
	}
}
//setLog($action);
echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
?>
<script  >
var xG	= new Gen();
function getValidaNombre(){
	
}

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
		document.getElementById("thAction").innerHTML = "Actualizar Datos de Este Usuario<br>Si no Cambia la Contrase&ntilde;a Dejela en Blanco";
	} else {
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
function jsSetSuspender(){
	var si	= confirm("DESEA SUSPENDER A ESTE USUARIO?");
	if(si){ jsaSetSuspension(); }
}
</script>
<?php 
$xHP->fin();
?>
