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
$xHP			= new cHPage("TR.Editar FORMS_Y_DOCS");
$idcontrato 	= parametro("idcontrato", 0 , MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->addJsFile("../js/ckeditor/ckeditor.js");

$xFRM			= new cHForm("frmeditor", "contratos-editor.edit.frm.php", false, "GET");
$xBtn			= new cHButton();
$xTxt			= new cHText();
$xDate			= new cHDate();
$xSel			= new cHSelect();
$xFMT			= new cFormato(false);

$xHP->addJTableSupport();

//ini_set("display_errors", "on");

$xHP->init("initComponents()");
$xFRM->setTitle($xHP->getTitle());

if($idcontrato <= 0){
	$xFRM->addCerrar();
	echo $xFRM->get();
} else {
	if($action == MQL_ADD){
		$s 				= isset($_REQUEST["ckeditor"]) ? $_REQUEST["ckeditor"] : "";
		//$text_default 	= addslashes($s);
		$text_default 	= $s;
		$xFRM->OHidden("idcontrato", $idcontrato);
		if(strlen($text_default) > 8){
			$xFMT->init($idcontrato);
			$res 		= $xFMT->setTexto($text_default, "", true);
			//$sqluc 			= "UPDATE general_contratos SET texto_del_contrato='$s' WHERE idgeneral_contratos=$idcontrato";
			//$x 				= my_query($sqluc);
			//$res			= $x[SYS_ESTADO];
		} else {
			$res			= false;
		}
		$xFRM->addCerrar();
		//$xFRM->addAtras();
		$xFRM->setResultado($res);
		if($res !== false){
			$xFRM->OButton("TR.VISTA_PREVIA", "getForma($idcontrato)", $xFRM->ic()->VER);
		}
	} else {
		$xFRM		= new cHForm("frmeditor", "contratos-editor.edit.frm.php?idcontrato=$idcontrato&action=" . MQL_ADD, false, "POST");
		$xFMT->init($idcontrato);
		
		$xFRM->OHidden("idcontrato", $idcontrato);
		$titulo			= $xFMT->getTitulo();
		$xFRM->setTitle($titulo);
		
		$xFRM->addHElem($xFMT->getSelectVariables("", "onchange=\"jsAddText(this.value)\" ", "tx4"));
		
		$arrVars		= $xFMT->getListaDeVars();
		
		
		$mArrVars		= $arrVars["variables_de_leasing"];
		asort($mArrVars);
		
		$xSel->addEvent("jsAddText(this.value)", "onchange");
		$xSel->addOptions($mArrVars);
		$xSel->setDivClass("tx4");
		$xFRM->addHElem( $xSel->get("idvars2", "TR.LEASING") );
		
		
		$xFRM->addGuardar();
		//$xFRM->addAtras();
		$text_default  = $xFMT->get();
		
		$xFRM->addHTML("<textarea class=\"ckeditor\" name=\"ckeditor\" id=\"ckeditor\" rows=\"20\" cols=\"15\">$text_default</textarea>");
		//$xFRM->addFootElement("<input type='hidden' value='$idcontrato' name='idcontrato' />");
		//$xFRM->OButton("Test", "test()");
	}
	//$xFRM->addJsInit("setTimeout('jsLoadEditor()',1500);");
	
	echo $xFRM->get();
}



//echo "$datos_del_contrato[4] en contrato $idcontrato";


?>
<script>
var xG	= new Gen();



function jsAddText(txt){
	//var txt = document.getElementById("idvariables").value;
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
function jsGuardar(){
	CKEDITOR.instances['ckeditor'].commands.save.exec();
}	
function initComponents(){
	$("#cke_1_contents").css("height", "650px");
}
function test(){
	/*editor = CKEDITOR.instances.fck; //fck is just my instance name you will need to replace that with yours

var edata = editor.getData();

var replaced_text = edata.replace("idontwant", "iwant this instead"); // you could also use a regex in the replace 

editor.setData(replaced_text);*/
	
	//CKEDITOR.instances['ckeditor'].commands.cellProperties.exec();
//CKEDITOR.instances['ckeditor'].commands.removeFormat.exec();
	//console.log(CKEDITOR.instances['ckeditor'].commands );
	/*CKEDITOR.styleCommand.prototype.exec = function( editor ) {
		editor.focus();

		if ( this.state == CKEDITOR.TRISTATE_OFF )
			editor.applyStyle( this.style );
		else if ( this.state == CKEDITOR.TRISTATE_ON )
			editor.applyStyle( paragraphStyle );
	};*/
	console.log("Test..");
}
function getForma(id){
	xG.w({ url : "../frmutils/forma.vista_previa.rpt.php?forma=" + id});
}


</script>
<?php
$xHP->fin();
?>
