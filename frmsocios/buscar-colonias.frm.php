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
$xHP		= new cHPage("TR.BUSCAR COLONIA", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$xTxt		= new cHText();

$jxc = new TinyAjax();
function jsaGetListadoDeColonias($idnombre){
	$sql	= "SELECT   `general_colonias`.`codigo_postal`,
         `general_colonias`.`tipo_colonia` AS `tipo`,
         `general_colonias`.`nombre_colonia` AS `colonia`,
         `general_estados`.`nombre` AS `entidadfederativa`
FROM     `general_colonias` 
INNER JOIN `general_estados`  ON `general_colonias`.`codigo_de_estado` = `general_estados`.`clave_numerica`   
	WHERE ( `general_estados`.`operacion_habilitada` = 1  ) AND  `general_colonias`.`nombre_colonia` LIKE '%$idnombre%' LIMIT 0,50";
	$xT	= new cTabla($sql);
	$xT->setEventKey("jsSetIDColonia");
	$xT->setWithMetaData();
	return $xT->Show();
}
$jxc ->exportFunction('jsaGetListadoDeColonias', array('idtextobusqueda'), "#iddiv");
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
$xTxt->addEvent("jsGetListadoDeColonias(this)", "onkeyup");

$xFRM->OButton("TR.Buscar", "jsaGetListadoDeColonias()", $xFRM->ic()->BUSCAR);
$xFRM->addCerrar();

$lbl	= $xTxt->getLabel("TR.Texto de busqueda");


$xFRM->addSeccion("idopciones", "TR.BUSCAR COLONIA");
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
/*idcodigopostal*/
var idfrm	= "idcodigopostal";//"<?php echo $idcontrol; ?>";
function jsGetListadoDeColonias(msrc){
	var mstr	= new String( msrc.value );
	if (mstr.length > 4) {
		jsaGetListadoDeColonias();
	}	
}
function jsSetIDColonia(id){
	var mObj	= processMetaData("#tr-general_colonias-" + id);
	if (window.parent){ msrc = window.parent.document; }
	if (opener){ msrc = opener.document; }
	if(msrc == null){} else {
		if(msrc.getElementById(idfrm)){
			oid			=  msrc.getElementById(idfrm);
			oid.value	= id;
			oid.focus();
			oid.select();

			if(msrc.getElementById("idnombrecolonia")){
				msrc.getElementById("idnombrecolonia").value = mObj.colonia;
			}
			xG.close();		
		}		
	}	
}
</script>
<?php
$xHP->fin();
?>