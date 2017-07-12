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
$xHP		= new cHPage("TR.Editar estructura", HP_FORM);

$xHP->init();	
$xFRM		= new cHForm("frmeditstructure", "sistema-estructura.frm.php");
$xBtn		= new cHButton();
$xTxt		= new cHText();
$xDate		= new cHDate();
$xChk		= new cHCheckBox();
//$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
	
$xSel		= new cSelect("ctable", "ctable", "SHOW TABLES IN " . MY_DB_IN );
$xSel->setEsSql();
	
$xFRM->addHElem( $xSel->get("TR.Nombre de la Tabla", true) );
$xFRM->addHElem( $xChk->get("TR.Actualizar", "forzar") );
$xFRM->OButton("TR.OBTENER", "initCommon()", $xFRM->ic()->EJECUTAR);
$xFRM->OButton("TR.Respaldo", "jsGetBackup()", $xFRM->ic()->DESCARGAR );

$xFRM->addHElem("<iframe id=\"idframel\" src=\"\" width='100%' height=\"100%\" ></iframe>");
		


echo $xFRM->get();
?>
<script>
var xG		= new Gen();
initCommon();
function jsGetBackup(){
    var url			= "../utils/download.php?tabla=general_structure";
    xG.w({ url : url, w : 800, h : 600 });
}
function initCommon(){
	var idt	= $('#ctable').val();
	var iup	= $('#forzar').prop('checked');
	xG.QFrame({url:"../frmutils/sistema-estructura.grid.php?tabla=" +  idt + "&forzar=" + iup, id:"idframel"});
}
</script>
<?php 
$xHP->fin();
?>