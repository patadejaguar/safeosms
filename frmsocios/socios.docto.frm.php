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

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$docto		= parametro("docto", "", MQL_RAW);
$contrato	= parametro("contrato",0, MQL_INT); $contrato	= parametro("credito",$contrato, MQL_INT); $contrato	= parametro("cuenta",$contrato, MQL_INT);$contrato	= parametro("idcredito",$contrato, MQL_INT);
$tipo		= parametro("tipo", 0, MQL_INT);
$clave		= parametro("id", 0, MQL_INT); $clave = parametro("clave", $clave, MQL_INT);
$descargar	= parametro("descargar", false, MQL_BOOL);

	

$xHP->init();



$xDoc		= new cDocumentos($docto);
$xPD		= new cPersonasDocumentacion($clave);
if($xPD->init() == true){
	$persona	= $xPD->getClaveDePersona();
	$docto		= $xPD->getNombre();
}
if($persona> DEFAULT_SOCIO AND $tipo > 0){
	if($xPD->initByTipo($tipo, $persona) == true){
		$persona	= $xPD->getClaveDePersona();
		$docto		= $xPD->getNombre();
	}
}



//$oFRM->addHTML("<a href=\"../utils/download.php?type=txt&download=$log&file=$log\" target=\"_blank\" class='button'>Descargar Archivo de EVENTOS de CIERRE DE RIESGOS</a><br /><br />");

$xFRM		= new cHForm("frmdocto");
//$docto				= $rows["archivo_de_documento"];
$xFRM->addHElem("<div class='tx1'>" . $xDoc->getEmbed($docto, $persona) . "</div>");

$xDoc->setNombreArchivo($docto);

$xFRM->OButton("TR.DESCARGAR", "var xG=new Gen();xG.w({url:'../utils/download.php?type="  . $xDoc->getTipo() . "&download=" . $xDoc->getNombreArchivo() . "&pathabsoluto=true&file=" . $xDoc->getRutaLocal() . "'})", $xFRM->ic()->DESCARGAR);

$xFRM->addCerrar();
echo $xFRM->get();
$xHP->fin();
?>
