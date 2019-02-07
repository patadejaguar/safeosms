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
$xHP		= new cHPage("TR.BUSCAR ACTIVIDAD_ECONOMICA UIF", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$xTxt		= new cHText();

$jxc = new TinyAjax();
function jsaGetListadoDeActividades($idnombre){
	$sql	= "SELECT `clave_de_actividad`, `nombre_de_la_actividad` FROM `personas_actividad_economica_tipos` WHERE `nombre_de_la_actividad` LIKE '%$idnombre%' LIMIT 0,50";
	$xT	= new cTabla($sql);
	$xT->setEventKey("jsSetIDActividad");
	$xT->setWithMetaData();
	return $xT->Show();
}
$jxc ->exportFunction('jsaGetListadoDeActividades', array('idtextobusqueda'), "#iddiv");
$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$idcontrol 	= parametro("idcontrol", "", MQL_RAW);

$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();

$xFRM->setNoAcordion();
$xFRM->setTitle($xHP->getTitle());

$xTxt->setDivClass("");
$xTxt->addEvent("jsGetListadoDeActividades(this)", "onkeyup");

$xFRM->OButton("TR.Buscar", "jsaGetListadoDeActividades()", $xFRM->ic()->BUSCAR);
$xFRM->addCerrar();

$lbl	= $xTxt->getLabel("TR.Texto de busqueda");


$xFRM->addSeccion("idopciones", "TR.BUSCAR ACTIVIDAD_ECONOMICA");
$xFRM->addDivSolo($lbl, $xTxt->getNormal("idtextobusqueda"), "tx14", "tx34");
$xFRM->endSeccion();
$xFRM->addSeccion("idresultados", "TR.RESULTADOBUSCAR");
//$xFRM->addJsBasico();
$xFRM->addHTML("<div class='tx1' id='iddiv'></div>");
$xFRM->endSeccion();

echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
?>
<script>
var idfrm	= "<?php echo $idcontrol; ?>";
var xG		= new Gen();

function jsGetListadoDeActividades(xsrc){
	if(xsrc){
		var mstr	= new String( xsrc.value );
		if (mstr.length > 4) {
			jsaGetListadoDeActividades();
		}
	}
}
function jsSetIDActividad(id){
	var mObj	= processMetaData("#tr-personas_actividad_economica_tipos-" + id);
	var msrc	= xG.winOrigen();
	if(msrc){
		if(msrc.getElementById(idfrm)){
			oid			=  msrc.getElementById(idfrm);
			oid.value	= id;
			oid.focus();
			oid.select();
			//alert(mObj.nombre_de_la_actividad);
			var idx	= mObj.clave_de_actividad;
			session("ae-scian-" + idx, JSON.stringify(mObj));
			if (msrc.getElementById("iddescripcion" + idfrm)) {
				msrc.getElementById("iddescripcion" + idfrm).value = mObj.nombre_de_la_actividad;
			}
			xG.close();		
		}		
	}
	

}
</script>
<?php
$xHP->fin();
?>