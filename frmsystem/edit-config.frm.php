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
$xHP		= new cHPage("TR.EDITAR ARCHIVO", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc 		= new TinyAjax();
//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO); $action	= strtolower($action);
$cnt			= parametro("idconf", "", MQL_RAW);
$arrNoEdits		= array("demo.sipakal.com"=>"demo.sipakal.com", "english.sipakal.com" => "english.sipakal.com");
$URL			= $xHP->getServerName();//$_SERVER["SERVER_NAME"] == "") ? $_SERVER['SERVER_ADDR'] : $_SERVER["SERVER_NAME"];

if(MODO_DEBUG == false OR isset($arrNoEdits[$URL]) === true OR SAFE_ON_DEV == false ){
	$xHP->goToPageError(999);
}

$xHP->init();

$xFRM		= new cHForm("frmeditconfig", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

?>
<style>
#idconf {
min-height: 25em;
}
.fieldform { text-align:left; }
</style>
<?php
$xFS		= new cFileSystem();
//$fileconf	= PATH_HTDOCS . "/core/core.config.os.lin.inc.php";

if($action == SYS_NINGUNO){
	$xFRM->addSubmit("", "jsSetSave()");
	$rr			= $xFS->getReadFile("core.config.os.lin.inc.php", PATH_HTDOCS . "/core/");
	$xFRM->OTextArea("idconf", $rr, "TR.TEXTO");
	$xFRM->setAction("../frmsystem/edit-config.frm.php?action=" . MQL_MOD);
	
} else {
	$cnt 	= base64_decode($cnt);
	
	//$xLog = new cFileLog();
	//$xLog->setWrite($text);
	//@fwrite($this->mRFile, $text);
	
	$xFS->setSaveFile($cnt, "core.config.os.lin.inc.php", PATH_HTDOCS . "/core/", true);
	
	$xFRM->addHElem(highlight_string($cnt, true));
	$xFRM->addCerrar();
}



echo $xFRM->get();
?>
<script>
function jsSetSave(){
	var str	= $("#idconf").val();
	$("#idconf").val(base64.encode(str));
	$("#id-frm").submit();
}
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();


?>