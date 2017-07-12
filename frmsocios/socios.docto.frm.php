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
$xHP		= new cHPage("TR.Visor de Documentos", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xHP->init();
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$docto		= parametro("docto", "", MQL_RAW);
$clave		= parametro("id", 0, MQL_INT); $clave = parametro("clave", $clave, MQL_INT);
if($clave > 0){
	$xDoc	= new cPersonas_documentacion();
	$xDoc->setData($xDoc->query()->initByID($clave));
	$persona	= $xDoc->clave_de_persona()->v();
	$docto		= $xDoc->archivo_de_documento()->v();
}

$xFRM		= new cHForm("frmdocto");
//$docto				= $rows["archivo_de_documento"];
$xDoc		= new cDocumentos($docto);
$xFRM->addHElem("<div class='tx1'>" . $xDoc->getEmbed($docto, $persona) . "</div>");
$xFRM->addCerrar();
echo $xFRM->get();
$xHP->fin();
?>
