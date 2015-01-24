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
$xHP			= new cHPage("TR.Editor de contratos");

$oficial 		= elusuario($iduser);
$idcontrato 	= parametro("idcontrato", 0 , MQL_INT);
$a 				= parametro("s");
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->addJsFile("../js/ckeditor/ckeditor.js");

$xFRM		= new cHForm("frmeditor", "frmeditor_contratos.php?action=1");
$xBtn		= new cHButton();
$xTxt		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();
$xFMT		= new cFormato(false);


$jxc = new TinyAjax();
function jsAgregarContrato($id, $nombre){
	$xForma		= new cFormato();
}
//$jxc ->exportFunction('jsAgregarContrato', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");

$jxc ->process();

//ini_set("display_errors", "on");

$xHP->init("initComponents()");

if( $action == SYS_NINGUNO ){
	$xFRM->addHElem( $xFMT->getSelectDeFormatos()->get(true) );
	$xFRM->addSubmit();
	
} elseif ($action == SYS_UNO) { //editar
	
	$xFMT->init($idcontrato);
	$titulo		= $xFMT->getTitulo();
	$xFRM->setTitle($titulo);

	
	$xFRM->addHElem( $xFMT->getSelectVariables("", "onchange=\"jsAddText()\" ") );
	$xFRM->addSubmit("", "", "");
	$text_default  = $xFMT->get();
	$xFRM->addHTML("<textarea class=\"ckeditor\" name=\"ckeditor\" id=\"ckeditor\" rows=\"20\" cols=\"15\">$text_default</textarea>");
	$xFRM->addFootElement("<input type='hidden' value='$idcontrato' name='idcontrato' />");
	$xFRM->setAction("frmeditor_contratos.php?action=2");
	
} elseif ($action == SYS_DOS){ //guardar
	$s 				= isset($_REQUEST["ckeditor"]) ? $_REQUEST["ckeditor"] : "";
	$i 				= parametro("idcontrato");
	$text_default 	= stripslashes($s) ;
	
	if( $s != "" && $i != "" ){
		$sqluc 	= "UPDATE general_contratos SET texto_del_contrato='$s' WHERE idgeneral_contratos=$i";
		$x 		= my_query($sqluc);
		if($x[SYS_ESTADO] != false){
			//$xFRM->addSubmit("", "", "");echo $text_default;
			$xFRM->addSubmit("", "getForma($i)");
		} else {
			$xFRM->addAviso("SURGIO UN PROBLEMA AL GUARDAR");
		}
	} else {
		$xFRM->addAviso("FALTAN PARAMETROS");
	}
}


echo $xFRM->get();
//echo "$datos_del_contrato[4] en contrato $idcontrato";


$jxc ->drawJavaScript(false, true);
?>
</body>
<script>
var xG	= new Gen();

	function jsAddText(){
		var txt = document.getElementById("idvariables").value;
		//var curSel = document.getSelection();
		InsertHTML(txt);
	}
	function setSelectSize(mSize){
		var mSelect = document.getElementById("idvariables");
			mSelect.removeAttribute("size");
			mSelect.setAttribute("size", mSize);
	}
	function InsertHTML(strText){
		CKEDITOR.instances['ckeditor'].insertText(strText);
	}
	function initComponents(){
		$("#cke_1_contents").css("height", "600px");
	}
	function getForma(id){
		xG.w({ url : "../frmutils/forma.vista_previa.rpt.php?forma=" + id});
	}
</script>
</html>
