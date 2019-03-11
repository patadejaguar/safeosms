<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 'on');
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 0.0.01
 * @package
 */
//=====================================================================================================
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	include_once("../core/core.db.inc.php");
	$theFile			= __FILE__;
	$permiso			= getSIPAKALPermissions($theFile);
	if($permiso === false){	header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//=====================================================================================================
$xHP		= new cHPage("TR.PANEL DE USUARIO", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$svc		= new MQLService("", "");
$xErrCod	= new cErrorCodes();
$jxc 		= new TinyAjax();

$svc->setKey(getClaveCifradoTemporal());
function jsaSavePin($pin, $idusuario){
	$xUser	= new cSystemUser($idusuario);
	
	$xUser->init();
	
	$xUser->setPin($pin);
	
	return $xUser->getMessages(OUT_HTML);
}
function jsaNivel($idusuario, $idnivel){
	$idnivel					= setNoMenorQueCero($idnivel);
	if($idnivel > 0){
		$_SESSION["tmp.nivel.de.user"]	= $idnivel;
		$_SESSION[SYS_USER_NIVEL] 		= $idnivel;
		$_SESSION["log_nivel"] 			= $idnivel;
	}
	return "echo!";
}
function jsaEditarPermisoUsr($idusuario, $idp, $vv){
	$xUsr	= new cSystemUser($idusuario);
	
	if($xUsr->init() == true){
		$xPer	= new cSystemPermissions();
		if($vv == 1){
			$xUsr->setUserOption($idp, "true");
		} else {
			$xUsr->setUserOption($idp, "false");
		}
		
		
	}
}
function jsaSendMensajePorMail($idaviso){
	$xMS	= new cSystemUserNotes($idaviso);
	if($xMS->init() == true){
		$xMS->sendByEmail();
	}
	return $xMS->getMessages();
}

$jxc->exportFunction('jsaSavePin', array('idpin', 'usuario'), "#idmsg");
$jxc->exportFunction('jsaSendMensajePorMail', array('idkey'), "#idmsg");

if(MODO_DEBUG == true){
	$jxc->exportFunction('jsaNivel', array('usuario', 'idnivel'), "#idmsg");
	$jxc->exportFunction('jsaEditarPermisoUsr', array('usuario', 'idpermiso', 'idvalor'), "#idmsg");
}


$jxc->process();


$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);$action	= strtolower($action);


$usuario		= parametro("usuario", 0, MQL_INT);
$observaciones	= parametro("idobservaciones");
$pass1			= parametro("idpass1", "", MQL_RAW);
$pass2			= parametro("idpass2", "", MQL_RAW);
$idpin			= parametro("idpin", 0, MQL_INT);

$xHP->addJTableSupport();
$xHP->init();

$xFRM			= new cHForm("frmpersonasusuarios", "./");
$xSel			= new cHSelect();
$xTxt			= new cHText();
$xChk			= new cHCheckBox();
$xFRM->setNoAcordion();
//$xChk->setDivClass("tx18");
//$xChk->setDivClass("");

$xFRM->setTitle($xHP->getTitle());
$xUser2			= new cSystemUser(); $xUser2->init();
if($usuario<=0){
	$usuario	= $xUser2->getID();
}

if($usuario >0 AND $persona <= DEFAULT_SOCIO){
	$xUser		= new cSystemUser($usuario); 
	$xUser->init(); 
	$persona 	= $xUser->getClaveDePersona();
	
}

//$xFRM->addJsBasico();
$xSoc		= new cSocio($persona);

if($xSoc->init() == true){
	
	if($xSoc->getEsUsuario(true) == true OR (MODO_DEBUG == true AND $persona == DEFAULT_SOCIO)){
		
		$pass1		= $svc->getDecryptData($pass1);
		$pass2		= $svc->getDecryptData($pass2);
		
		if($usuario <= 0){
			$xUser	= $xSoc->getOUsuario();
		} else {
			$xUser	= new cSystemUser($usuario);
			$xUser->init();
			//setLog($xUser->getClaveDePersona());
			if($xUser->getClaveDePersona() !== $xSoc->getClaveDePersona()){
				$xHP->goToPageError($xErrCod->SIN_PERMISO_REGLA);
			}
			$usuario	= $xUser->getID();
		}
		

		
		
		$xFRM->OHidden("idsocio", $persona);
		$xFRM->OHidden("usuario", $usuario);
		
		
		
		$xFRM->addHElem( $xUser->getFicha());
		//Es el mismo usuario
		if($action == SYS_NINGUNO){
			
			if(($xUser2->getID() !== $xUser->getID()) AND $xUser2->getPuedeEditarUsuarios() == false ){
				$xHP->goToPageError($xErrCod->SIN_PERMISO_REGLA);
			} else {
				//Reporte de Eliminados
				$xFRM->OButton("TR.VER ELIMINADOS", "jsVerEliminados", $xFRM->ic()->REGISTROS);
			}
			
			$xFRM->addCerrar();
			if( $xUser2->getID() == $xUser->getID() ){
				$idxuser	= $xUser->getID();
				$xFRM->OButton("TR.CAMBIAR PASSWORD", "var xG=new Gen();xG.go({url:'../frmsocios/socios.usuario.frm.php?usuario=$idxuser&action=editpass'});", $xFRM->ic()->PASSWORD, "cmdchangepass", "red");
				$xFRM->OButton("TR.CAMBIAR PIN", "var xG=new Gen();xG.go({url:'../frmsocios/socios.usuario.frm.php?usuario=$idxuser&action=editpin'});", $xFRM->ic()->PASSWORD, "cmdchangepin", "yellow");
				
			} else if ($xUser2->getPuedeEditarUsuarios() == true){
				$idxuser	= $usuario;
				$email 		= $xUser->getCorreoElectronico();
				$xFRM->OButton("TR.REESTABLECER PASSWORD", "var xG=new Gen();xG.go({url:'../frmsocios/socios.usuario.frm.php?usuario=$idxuser&action=editpass'});", $xFRM->ic()->PASSWORD, "cmdchangepass", "red");
				//$xFRM->OButton("TR.CAMBIAR PIN", "var xG=new Gen();xG.go({url:'../frmsocios/socios.usuario.frm.php?usuario=$idxuser&action=editpin'});", $xFRM->ic()->PASSWORD, "cmdchangepin", "yellow");
			}
		} else {
			
			if($action == "editpass" AND ( $xUser2->getID() == $xUser->getID() ) ){
				$xFRM->addSeccion("idsecccampass", "TR.Cambio de password");
				$xTxt->addEvent("var xG=new Gen(); this.value=xG.enc(this.value)", "onchange");
				
				$xFRM->addHElem($xTxt->getPassword("idpass1", "TR.PASSWORD"));
				$xFRM->addHElem($xTxt->getPassword("idpass2", "TR.CONFIRME PASSWORD"));
				
				$xFRM->endSeccion();
				$xFRM->addGuardar();
			}
			
			if($action == "editpin" AND ( $xUser2->getID() == $xUser->getID() ) ){
				$xFRM->addSeccion("idnewpass", "TR.Cambio de Pin");
				$xFRM->ONumero("idpin", "", "TR.PIN");
				$xFRM->setValidacion("idpin", "jsSavePin");
				$xFRM->endSeccion();
				$xFRM->addGuardar();
			}
			
			if($action == "savepass"){
				
				if($xUser->setPassword($pass1) == true){
					$xFRM->addAvisoRegistroOk("TR.El password ha cambiado\r\n");
				} else {
					$xFRM->addAvisoRegistroError($xUser->getMessages());
				}
			}
		}
		
		if($action == SYS_NINGUNO OR ($pass1 !== $pass2)){
			//$xFRM->addGuardar();
			
			if(MODO_DEBUG == true){
				$xFRM->OHidden("idvalor", "");
				$xFRM->OHidden("idpermiso", "");
			}
			
			$xFRM->setAction("socios.usuario.frm.php?action=" . MQL_ADD);
			
			if($pass1 !== $pass2){
				$xFRM->addAvisoRegistroError("MS.MSG_PASS_NO_IGUAL");
			}
			
			$xTbl		= new cHTabla("idtblrules", "listado");
			$xImg		= new cHImg();
			$xTbl->addTH("TR.PARAMETRO");
			$xTbl->addTH("TR.VALOR");
			
			$arrR	= $xUser->getUserRules();
			foreach ($arrR as $idx => $idv){
				$ss		= ($idv == "false") ? "error" : "success";
				$img	= ($idv == "false") ? "busy.png" : "check.png";
				$vv		= ($idv == "false") ? 0 : 1;
				
				$xTbl->initRow($ss);
				$xTbl->addTD("Nivel .- $idx");
				
				
				
				if($xUser2->getPuedeEditarUsuarios() == false OR ($xUser2->getTipoEnSistema() < $xUser->getTipoEnSistema() )){
					$xTbl->addTD($xImg->get16($img));
				} else {
					
					$xTbl->addTD($xChk->getSiNo("", "idchk1-$idx", $vv, true));
					$xFRM->addControEvt("chk-idchk1-$idx", "jsEditPermiso('idchk1-$idx','$idx')", "change");
				}
				

				$xTbl->endRow();
			}
			//Opciones Editables
			$arrR2	= $xUser->getUserOptions();
			foreach ($arrR2 as $idx => $idv){
				$ss		= ($idv == "false") ? "error" : "success";
				$img	= ($idv == "false") ? "busy.png" : "check.png";
				
				$vv		= ($idv == "false") ? 0 : 1;
				
				
				$xTbl->initRow($ss);
				$xTbl->addTD("Usuario .- $idx");
				
				if($xUser2->getPuedeEditarUsuarios() == false OR ($xUser2->getTipoEnSistema() < $xUser->getTipoEnSistema() )){
					$xTbl->addTD($xImg->get16($img));
				} else {
					$xTbl->addTD($xChk->getSiNo("", "idchk2-$idx", $vv, true));
					$xFRM->addControEvt("chk-idchk2-$idx", "jsEditPermiso('idchk2-$idx','$idx')", "change");
				}
				//
				
				$xTbl->endRow();
			}
			if(MODO_DEBUG == true){
				$xLPerm	= new cSystemUserRulesList();
				$arrR3	= $xLPerm->getListInArray();
				
				foreach ($arrR3 as $idx => $idv){
					$idv	= "false";
					$ss		= ($idv == "false") ? "error" : "success";
					$img	= ($idv == "false") ? "busy.png" : "check.png";
					
					$vv		= ($idv == "false") ? 0 : 1;
					
					
					$xTbl->initRow($ss);
					$xTbl->addTD("SU .- $idx");
					
					if($xUser2->getPuedeEditarUsuarios() == false OR ($xUser2->getTipoEnSistema() < $xUser->getTipoEnSistema() )){
						$xTbl->addTD($xImg->get16($img));
					} else {
						
						$xTbl->addTD($xChk->getSiNo("", "idchk3-$idx", $vv, true));
						$xFRM->addControEvt("chk-idchk3-$idx", "jsEditPermiso('idchk3-$idx','$idx')", "change");
					}
					//
					
					$xTbl->endRow();
				}
			}
			//Cambiar Nivel
			/*if(MODO_DEBUG == true){
				$xTbl->initRow();
				$xTbl->addTD("Nivel");
				$nivel	= getUsuarioActual(USR_NIVEL);
				$xSelN	= $xSel->getListaDeNivelDeUsuario("idnivel", false, $nivel);
				$xSelN->addEvent("onchange", "jsaNivel()");
				$xSelN->setLabel("");
				$xTbl->addTD($xSelN->get());
				$xTbl->endRow();
			}*/
			
			
			
			$xFRM->addSeccion("idx", "TR.OPCIONES");
			$xFRM->addHElem($xTbl->get());
			$xFRM->endSeccion();
			
			$xFRM->addSeccion("idlistanotas", "TR.NOTAS");
			$xHG    = new cHGrid("iddivusernotes","TR.NOTAS");
			
			$xHG->setSQL($xLi->getListadoDeTareas($usuario));
			
			$xHG->addList();
			$xHG->setOrdenar();
			
			$xHG->addKey("idusuarios_web_notas");
			
			//$xHG->col("tipo", "TR.TIPO", "10%");
			//$xHG->col("oficial", "TR.OFICIAL", "10%");
			//$xHG->col("oficial_de_origen", "TR.OFICIAL DE ORIGEN", "10%");
			
			$xHG->col("persona", "TR.PERSONA", "10%");
			
			//$xHG->col("documento", "TR.DOCUMENTO", "10%");
			$xHG->col("fecha", "TR.FECHA", "10%");
			$xHG->col("texto", "TR.TEXTO", "60%");
			//$xHG->col("estado", "TR.ESTADO", "10%");
			//$xHG->col("relevancia", "TR.RELEVANCIA", "10%");
			//$xHG->col("tiempo", "TR.TIEMPO", "10%");
			
			$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
			$xHG->OButton("TR.ENVIAR EMAIL", "jsSendMail('+ data.record.codigo +')", "mail-send.png");
			if(MODO_DEBUG == true){
				$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.codigo +')", "edit.png");
				$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.codigo +')", "delete.png");
			}
			$xHG->OButton("TR.BAJA", "jsDeact('+ data.record.codigo +')", "undone.png");
			$xFRM->addHElem("<div id='iddivusernotes'></div>");
			$xFRM->addJsCode( $xHG->getJs(true) );
			
			/* ===========        GRID JS        ============*/
			
			$xHG2    = new cHGrid("iddivcoords",$xHP->getTitle());
			
			$xHG2->setSQL("SELECT * FROM `usuarios_coordenadas` WHERE `idusuario`=$usuario ORDER BY `tiempo` DESC LIMIT 0,100");
			$xHG2->addList();
			$xHG2->setOrdenar();
			$xHG2->addKey("idusuarios_coordenadas");
			
			//$xHG2->col("idusuario", "TR.IDUSUARIO", "10%");
			$xHG2->col("tiempo", "TR.TIEMPO", "10%");
			$xHG2->col("latitud", "TR.LATITUD", "10%");
			$xHG2->col("longitud", "TR.LONGITUD", "10%");
			//$xHG2->col("idenfuente", "TR.IDENFUENTE", "10%");
			
			//$xHG2->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
			//$xHG2->OButton("TR.EDITAR", "jsEdit('+ data.record.idusuarios_coordenadas +')", "edit.png");
			//$xHG2->OButton("TR.ELIMINAR", "jsDel('+ data.record.idusuarios_coordenadas +')", "delete.png");
			//$xHG2->OButton("TR.BAJA", "jsDeact('+ data.record.idusuarios_coordenadas +')", "undone.png");
			
			$xHG2->OButton("TR.MAPA", "jsGetMapa('+ data.record.longitud +',' + data.record.latitud  +')", "placeholder.png");
			
			$xFRM->addHElem("<div id='iddivcoords'></div>");
			$xFRM->addJsCode( $xHG2->getJs(true) ); 
			
			$xFRM->endSeccion();
			
			
			
			$xFRM->addAviso("", "idmsg");
			$xFRM->OHidden("idkey", "0");
		} else {

			//xFRM->addCerrar();
		}
		
	} else {
		$xFRM->addAvisoInicial($xSoc->getNombreCompleto() . " No Puede ser Usuario.<br />Debe existir una relacion Usuario-Persona", true);
	}
} else {
	
	$xFRM->addAvisoInicial("No se puede editar este usuario", true);
}

echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsSendMail(id){
	xG.confirmar({msg: "MSG_CONFIRM_SEND_MSG", callback : function(){
			$("#idkey").val(id);
			jsaSendMensajePorMail();
		}});
}
function jsVerEliminados(){
	var iduser = $("#usuario").val();
	xG.w({url:"../frmsecurity/eliminados.frm.php?usuario=" +  iduser});
}

function jsSavePin(){
	xG.confirmar({msg: "CONFIRMA_ACTUALIZACION", callback: jsaSavePin});
	//var iduser = $("#idpin").val();
	return true;
}
function jsEditPermiso(idx,regla){
	var vv	= entero($("#" + idx).val());
	if(vv == 0){
	xG.confirmar({msg:"¿ Confirma eliminar permiso : " + regla + " ?",
		callback: function(){
			$("#idpermiso").val(regla);
			$("#idvalor").val(0);
			jsaEditarPermisoUsr();
		}});
	} else {
		xG.confirmar({msg:"¿ Confirma agregar permiso : " + regla + " ?",
			callback: function(){
				$("#idpermiso").val(regla);
				$("#idvalor").val(1);
				jsaEditarPermisoUsr();
			}});
		
	}
	
}

function jsEdit(id){
    xG.w({url:"../frmsecurity/usuarios-notas.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivusernotes});
}
function jsAdd(){
    xG.w({url:"../frmsecurity/usuarios-notas.new.frm.php?", tiny:true, callback: jsLGiddivusernotes});
}
function jsDel(id){
    xG.rmRecord({tabla:"usuarios_web_notas", id:id, callback:jsLGiddivusernotes });
}
function jsDeact(id){
    xG.recordInActive({tabla:"usuarios_web_notas", id:id, callback:jsLGiddivusernotes, preguntar:true });
}
function jsGetMapa(lg, lt){
	xG.getMap({ latitud : lt, longitud : lg });
}
</script>
<?php

$jxc->drawJavaScript(false, true);

$xHP->fin();
?>