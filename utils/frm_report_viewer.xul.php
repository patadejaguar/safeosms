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

include_once( "../core/entidad.datos.php" );
include_once( "../core/core.deprecated.inc.php" );
include_once( "../core/core.fechas.inc.php" );
include_once( "../core/core.config.inc.php" );

$pUSRNivel = $_SESSION["SN_d567c9b2d95fbc0a51e94d665abe9da3"];
$oficial = elusuario($iduser);

//header("Content-type: application/vnd.mozilla.xul+xml");
header("Content-type: text/plain");


echo "<?xml version=\"1.0\"?>
<?xml-stylesheet href=\"chrome://global/skin/\" type=\"text/css\"?>
<?xml-stylesheet href=\"./css/xul.css\" type=\"text/css\"?>";

$rpt = $_GET["r"];
$i = $_GET["i"];
$f = $_GET["f"];
if(!$i){
	$i = 0;
}
if(!$f){
	$f = 100;
}
if(!$rpt){
	$rpt = "./principal.php";
}
echo $rpt; exit;
?>
<window id="report-main-window"
title=""
 
 
sizemode="maximized"
xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">
<vbox id="hToolbar" flex="1" maxheight="30px">
<toolbox>
  <toolbar id="nav-toolbar">
    <button label="Anterior" oncommand="backPage();" />
    <button label="Siguiente" oncommand="nextPage();" />
    <button label="Imprimir" oncommand="printPage();" />
    <label control="id-rows-per-page" value="Numero de Registros:" />
    <textbox id="id-rows-per-page" size="3" value="100" />
  </toolbar>
</toolbox>

</vbox>
<vbox id="hFrame" flex="2" maxheight="777px">
<browser id="idFReport" src="<?php echo $rpt; ?>"   height="1600px" flex="1" />
</vbox>
<script>
var IPage = <?php echo $i; ?>;
var FPage = <?php echo $f; ?>;
var mReport  = "<?php echo $rpt; ?>";
var tmpURL = "./principal.php";
function nextPage(){
	var maxPerPage = document.getElementById("id-rows-per-page").value;
	var tmpInit = FPage;
	var tmpEnd = FPage + maxPerPage;
	var iFr = document.getElementById("idFReport");

	if (mReport.indexOf("?")!= -1) {
		var tmpURL = (mReport + "%3minit=" + tmpInit + "%3mend=" + tmpEnd);
	} else {
		var tmpURL = (mReport + "?minit=" + tmpInit + "%3mend=" + tmpEnd);
	}
	alert(tmpURL);
	iFr.removeAttribute("src");
	iFr.setAttribute("src", tmpURL);
}
function backPage(){
	var maxPerPage = document.getElementById("id-rows-per-page").value;
	var tmpInit = IPage - maxPerPage;
	var tmpEnd = IPage;
	var iFr = document.getElementById("idFReport");

}
function printPage(){

}
</script>
</window>