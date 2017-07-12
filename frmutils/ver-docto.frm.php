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

$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$tipoorigen	= parametro("tipoorigen", 0, MQL_INT);
$docto		= parametro("docto", "", MQL_RAW);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xFRM		= new cHForm("frmdocto");
//$docto				= $rows["archivo_de_documento"];
$xDoc		= new cDocumentos();
$prePath	= $xDoc->getPathPorTipo($tipoorigen);
if($action == SYS_NINGUNO){
	$xFRM->addHElem("<div class='tx1'>" . $xDoc->getEmbedByName($docto, $prePath) . "</div>");
	$xFRM->OHidden("docto", $docto);
	$xFRM->OHidden("clave", $clave);
	$xFRM->OHidden("tipoorigen", $tipoorigen);
	
	$xFRM->setAction("../frmutils/ver-docto.frm.php?action=" . MQL_DEL);
	
	$xFRM->addCerrar();
	$xFRM->OButton("TR.ELIMINAR", "jsEliminar()", $xFRM->ic()->ELIMINAR);
	
} else {
	$xFRM->addCerrar("", 3);
	$xFRM->addAvisoRegistroOK("");
	$xDoc->FTPDeleteFile($docto, $prePath);
}

echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsEliminar(){
	xG.confirmar({msg: "Confirma Eliminar este Documento?", callback: jsExec });
	
}
function jsExec(){ $('#id-frmdocto').submit(); }
</script>
<?php
$xHP->fin();
?>
