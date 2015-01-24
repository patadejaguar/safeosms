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
$xHP		= new cHPage("", HP_FORM);
$xDoc		= new cDocumentos();

$DDATA		= $_REQUEST;
$persona	= ( isset($DDATA["persona"]) ) ? $DDATA["persona"] : DEFAULT_SOCIO;
$action		= ( isset($DDATA["action"]) ) ? $DDATA["action"] : SYS_CERO;

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

echo $xHP->getHeader();

$jsb	= new jsBasicForm("frmdocumentos");
//$jxc ->drawJavaScript(false, true);
$ByType	= "";
echo $xHP->setBodyinit();
if($persona != DEFAULT_SOCIO){
	$xSoc	= new cSocio($persona);
	$xSoc->init();
	$ByType	= ($xSoc->getEsPersonaFisica() == true) ? BASE_DOCTOS_PERSONAS_FISICAS : BASE_DOCTOS_PERSONAS_MORALES;
}
$xFRM	= new cHForm("frmfirmas", "personas_documentos.frm.php?action=" . SYS_UNO . "&persona=$persona");
$xFRM->setEnc("multipart/form-data");
$xFRM->setTitle($xHP->getTitle());

$xBtn	= new cHButton();
$xTxt	= new cHText();
$xTxt2	= new cHText();
$xTxtF	= new cHText();
$xSel	= new cHSelect();
$xImg	= new cHImg();

if($action == SYS_CERO){
	$xTxtF->setDivClass("");
	//$xTxtF->setProperty("class", "")
	$xFRM->OFile("idnuevoarchivo","", "TR.Cargar Documento");
	$xFRM->OText("iddocumento", "", "TR.Nombre del Archivo", true, $xImg->get24("common/search.png", " onclick='jsGetDocto()' "));
	$xFRM->addHElem( $xSel->getTiposDeDoctosPersonales("", $ByType)->get(true) );
	$xFRM->addHElem( $xTxt2->getDeMoneda("idnumeropagina", "TR.Numero de Pagina") );
	$xFRM->addObservaciones();
	$xFRM->addSubmit();
	
} else {
	$xFRM->addCerrar();
	$doc1			= parametro("iddocumento", "", MQL_RAW);
	$observaciones	= ( isset($DDATA["idobservaciones"]) ) ? $DDATA["idobservaciones"] : "";
	$tipodedocto	= ( isset($DDATA["idtipodedocto"]) ) ? $DDATA["idtipodedocto"] : "";
	$pagina			= ( isset($DDATA["idnumeropagina"]) ) ? $DDATA["idnumeropagina"] : "";
	$archivoenviado	= (isset($_FILES["idnuevoarchivo"])) ? $_FILES["idnuevoarchivo"] : null;
	if(isset($_FILES["idnuevoarchivo"])){
		if(trim($_FILES["idnuevoarchivo"]["name"]) == ""){ $archivoenviado = null; }
	}
	$xSoc		= new cSocio($persona);
	$xSoc->init();
	if($doc1 !== false){
		$ready		= $xSoc->setGuardarDocumento($tipodedocto, $doc1, $pagina, $observaciones, false, $archivoenviado);
		if($ready == true){
			$xFRM->addAvisoRegistroOK();
		} else {
			$xFRM->addAvisoRegistroError();
		}
	}
	if(MODO_DEBUG == true){ $xFRM->addLog($xSoc->getMessages(OUT_TXT) ); }
}
echo $xFRM->get();

//$jsb->show();
?>
<!-- HTML content -->
<script>
var xG	= new Gen();
function jsGetDocto(){
	xG.w({
		url : "../frmutils/docs.explorer.php?callback=jsSetDocto"
		});
}
function jsSetDocto(mfile){
	$("#iddocumento").val(mfile);
}
</script>
<?php
$xHP->fin();
?>