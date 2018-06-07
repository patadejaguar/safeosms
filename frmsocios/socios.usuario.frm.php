<?php
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

$jxc->exportFunction('jsaSavePin', array('idpin', 'usuario'), "#idmsg");
if(MODO_DEBUG == true){
	$jxc->exportFunction('jsaNivel', array('usuario', 'idnivel'), "#idmsg");
}


$jxc->process();


$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$usuario		= parametro("usuario", 0, MQL_INT);
$observaciones	= parametro("idobservaciones");
$pass1			= parametro("idpass1", "", MQL_RAW);
$pass2			= parametro("idpass2", "", MQL_RAW);
$idpin			= parametro("idpin", 0, MQL_INT);


$xHP->init();

$xFRM			= new cHForm("frm", "./");
$xSel			= new cHSelect();
$xTxt			= new cHText();
$xChk			= new cHCheckBox();

$xFRM->setTitle($xHP->getTitle());
$xUser2			= new cSystemUser(); $xUser2->init();
if($usuario >0 AND $persona <= DEFAULT_SOCIO){
	$xUser	= new cSystemUser($usuario); $xUser->init(); $persona = $xUser->getClaveDePersona();
	
}

//$xFRM->addJsBasico();
$xSoc		= new cSocio($persona);

if($xSoc->init() == true){
	if($xSoc->getEsUsuario(true) == true){
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
		if(($xUser2->getID() !== $xUser->getID()) AND $xUser2->getPuedeEditarUsuarios() == false ){
			$xHP->goToPageError($xErrCod->SIN_PERMISO_REGLA);
		}
		
		
		$xFRM->OHidden("idsocio", $persona);
		$xFRM->OHidden("usuario", $usuario);
		
		
		//Reporte de Eliminados
		$xFRM->OButton("TR.VER ELIMINADOS", "jsVerEliminados", $xFRM->ic()->REGISTROS);
		
		if($action == SYS_NINGUNO OR ($pass1 !== $pass2)){
			
			$xFRM->addHElem( $xUser->getFicha());
			$xFRM->setNoAcordion();
			$xFRM->addSeccion("idnomsg", "TR.Cambio de password");
			$xTxt->addEvent("var xG=new Gen(); this.value=xG.enc(this.value)", "onchange");
			
			$xFRM->addHElem($xTxt->getPassword("idpass1", "TR.PASSWORD"));
			$xFRM->addHElem($xTxt->getPassword("idpass2", "TR.CONFIRME PASSWORD"));
			
			$xFRM->endSeccion();
			
			if( $xUser2->getID() == $xUser->getID() ){
				$xFRM->addSeccion("idnewpass", "TR.Cambio de Pin");
				$xFRM->ONumero("idpin", "", "TR.PIN");
				$xFRM->setValidacion("idpin", "jsSavePin");
				$xFRM->endSeccion();
			}
			
			$xFRM->addGuardar();
			
			
			$xFRM->setAction("socios.usuario.frm.php?action=" . MQL_ADD);
			if($pass1 !== $pass2){
				$xFRM->addAvisoRegistroError("TR.LA PASSWORD No es igual\r\n");
			}
			
			$xTbl		= new cHTabla("idtblrules", "listado");
			$xImg		= new cHImg();
			$xTbl->addTH("TR.PARAMETRO");
			$xTbl->addTH("TR.VALOR");
			
			$arrR	= $xUser2->getUserRules();
			foreach ($arrR as $idx => $idv){
				$ss		= ($idv == "false") ? "error" : "success";
				$img	= ($idv == "false") ? "busy.png" : "check.png";
				
				$xTbl->initRow($ss);
				$xTbl->addTD($idx);
				$xTbl->addTD($xImg->get16($img));
				
				$xTbl->endRow();
			}
			//Opciones Editables
			$arrR	= $xUser2->getUserOptions();
			foreach ($arrR as $idx => $idv){
				$ss		= ($idv == "false") ? "error" : "success";
				$vv		= ($idv == "false") ? 0 : 1;
				
				$xChk->setDivClass("tx18");
				$xTbl->initRow($ss);
				$xTbl->addTD($idx);
				$xTbl->addTD($xChk->getSiNo("", "idchk-$idx", $vv, true));
				
				$xTbl->endRow();
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
			
			$xFRM->addAviso("", "idmsg");
		} else {
			if($xUser->setPassword($pass1) == true){
				$xFRM->addAvisoRegistroOk("TR.El password ha cambiado\r\n");
			} else {
				$xFRM->addCerrar();
				$xFRM->addAvisoRegistroError($xUser->getMessages());
			}
			
		}
	}
} else {
	$xFRM->addAvisoInicial("No se puede editar este usuario", true);
}
echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsVerEliminados(){
	var iduser = $("#usuario").val();
	xG.w({url:"../frmsecurity/eliminados.frm.php?usuario=" +  iduser});
}
function jsSavePin(){
	xG.confirmar({msg: "CONFIRMA_ACTUALIZACION", callback: jsaSavePin});
	//var iduser = $("#idpin").val();
	return true;
}
</script>
<?php

$jxc->drawJavaScript(false, true);

$xHP->fin();
?>