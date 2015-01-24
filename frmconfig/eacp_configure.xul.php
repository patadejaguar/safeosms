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
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");

$pUSRNivel = $_SESSION["SN_d567c9b2d95fbc0a51e94d665abe9da3"];
$oficial = elusuario($iduser);

header("Content-type: application/vnd.mozilla.xul+xml");
//header("Content-type: text/plain");


echo "<?xml version=\"1.0\"?>
<?xml-stylesheet href=\"chrome://global/skin/\" type=\"text/css\"?>
<?xml-stylesheet href=\"./css/xul.css\" type=\"text/css\"?>";


$mImgPath = vIMG_PATH . "/common";
//echo $rpt; exit;
?>
<window id="report-main-window"
title=""
 
 
sizemode="maximized"
xmlns="http://www.mozilla.org/keymaster/gatekeeper/there.is.only.xul">
<hbox flex="2">
<vbox id="hToolbar" flex="1"  maxwidth="64px" width="64px" heigth="768" maxheigth="864px">

<tabbox>
  <tabs>
	<tab label="Datos Generales"/>
	<tab label="Domicilio"/>
	<tab label="Configuracion de Creditos"/>
	<tab label="Configuracion de Seguimiento"/>
	<tab label="Configuracion de Tesoreria"/>
  </tabs>
  <tabpanels>

    <tabpanel id="mailtab">

    </tabpanel>

    <tabpanel id="newstab">


    </tabpanel>

  </tabpanels>
</tabbox>



</vbox>

</hbox>
<script>
function jsSetGenerales(){

}
function jsSetDomicilio(){

}
function jsSetConfigCifras(){

}
function jsSetConfigOperaciones(){

}
function jsSetControlInterno(){

}

function jsSetConfigCreditos(){

}
function setFramePrincipal(sURI){

}

function addNuevoConvenio(){

}
</script>
</window>