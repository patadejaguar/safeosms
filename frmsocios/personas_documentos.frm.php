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
$xHP		= new cHPage("TR.CARGA DE DOCUMENTO", HP_FORM);
$xDoc		= new cDocumentos();
$xF			= new cFecha();
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
$xFRM->setNoAcordion();
$xFRM->setTitle($xHP->getTitle());

if($action == SYS_CERO){
	$xFRM->addSeccion("iddivar", "TR.ARCHIVO");
	$xTxtF->setDivClass("");
	//$xTxtF->setProperty("class", "")
	$xFRM->OFile("idnuevoarchivo","", "TR.Cargar Documento");
	
	$items	= count($xDoc->FTPListFiles());
	if($items>0){
		$xFRM->OText("nombrearchivo", "", "TR.Nombre del Archivo", true, $xImg->get24("common/search.png", " onclick='jsGetDocto()' "));
	} else {
		$xFRM->OHidden("nombrearchivo", "");
	}
	$xFRM->endSeccion();
	$xFRM->addSeccion("iddotros", "TR.DATOS");
	$xFRM->addHElem( $xSel->getTiposDeDoctosPersonales("", $ByType)->get(true) );
	$xFRM->ODate("idfechacarga", false, "TR.FECHA_DE EMISION");
	$xFRM->OText_13("idnumeropagina", 0, "TR.Numero de Documento");
	
	//$xFRM->ODate("idfechavencimiento", $xF->getFechaMaximaOperativa(), "TR.FECHA_DE VENCIMIENTO");
	$xFRM->addObservaciones();
	$xFRM->addGuardar();
	$xFRM->endSeccion();
} else {
	$xFRM->addCerrar();
	$nombrearchivo	= parametro("nombrearchivo", "", MQL_RAW);
	$observaciones	= (isset($DDATA["idobservaciones"]) ) ? $DDATA["idobservaciones"] : "";
	$tipodedocto	= (isset($DDATA["idtipodedocto"]) ) ? $DDATA["idtipodedocto"] : "";
	$pagina			= parametro("idnumeropagina", "");
	$archivonuevo	= (isset($_FILES["idnuevoarchivo"])) ? $_FILES["idnuevoarchivo"] : null;
	$fechacarga		= parametro("idfechacarga", false, MQL_DATE);
	
	$fechavenc		= false; //parametro("idfechavencimiento", $xF->getFechaMaximaOperativa(), MQL_DATE);
	if(isset($_FILES["idnuevoarchivo"])){
		if(trim($_FILES["idnuevoarchivo"]["name"]) == ""){ $archivoenviado = null; }
	}
	$xSoc		= new cSocio($persona);
	if($xSoc->init() == true){
	//if($doc1 !== false){
		$ready		= $xSoc->setGuardarDocumento($tipodedocto, $nombrearchivo, $pagina, $observaciones, $fechacarga, $archivonuevo, $fechavenc);
		if($ready == true){
			$xFRM->addAvisoRegistroOK($xSoc->getMessages());
		} else {
			$xFRM->addAvisoRegistroError($xSoc->getMessages());
		}
	}
	//if(MODO_DEBUG == true){ $xFRM->addLog($xSoc->getMessages(OUT_TXT) ); }
}
echo $xFRM->get();

//$jsb->show();
?>
<!-- HTML content -->
<script>
var xG	= new Gen();
function jsGetDocto(){
	xG.w({
		url : "../frmutils/docs.explorer.php?callback=jsSetDocto", tiny:true
		});
}
function jsSetDocto(mfile){
	$("#nombrearchivo").val(mfile);
}
</script>
<?php
$xHP->fin();
?>