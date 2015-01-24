<?php
/**
*	Buscar Beneficiarios segun numero de socio
*/
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
$xHP		= new cHPage("TR.Listado de Recibos");
$msg		= "";
$oficial 	= elusuario($iduser);
$jxc 		= new TinyAjax();
function getListRecibos($tipo, $socio){
	$sql	= new cSQLListas();
	$cTbl	= new cTabla($sql->getListadoDeRecibos($tipo, $socio));
	$xImg	= new cHImg();
	$cTbl->setKeyField("idoperaciones_recibos");
	$cTbl->setTdClassByType();
	$cTbl->OButton("TR.Reporte", "jsGetReporteRecibo(" . HP_REPLACE_ID . ")", $cTbl->ODicIcons()->REPORTE);
	$cTbl->OButton("TR.Panel", "var xRec = new RecGen(); xRec.panel(" . HP_REPLACE_ID . ")", $cTbl->ODicIcons()->EJECUTAR);
	$cTbl->setEventKey("setRecibo");
	return $cTbl->Show();
}
$jxc ->exportFunction('getListRecibos', array('idTipoRecibo', 'idsocio'), "#lst-resultados");
$jxc ->process();

$c			= parametro("c", false, MQL_RAW);
$f			= parametro("f", false, MQL_RAW);

$xHP->init();

$xFRM		= new cHForm("frmsearchrecs", "./");
$xSel		= new cHSelect();

$xFRM->addPersonaBasico();

$sqlsel = "SELECT
							`operaciones_recibostipo`.`idoperaciones_recibostipo` AS `tipo`,
							`operaciones_recibostipo`.`descripcion_recibostipo`   AS `descripcion`
						FROM
							`operaciones_recibostipo` `operaciones_recibostipo`";
$mSel	= $xSel->getListadoGenerico($sqlsel, "idTipoRecibo");
$mSel->addEspOption(SYS_TODAS);

$mSel->addEvent("onchange", "getListRecibos()");
$xFRM->addHElem( $mSel->get("TR.Tipo de Recibo", true) );
$xFRM->addCerrar();
$xFRM->OButton("TR.Buscar", "getListRecibos()", "buscar");
//$xFRM->addSubmit();
$xFRM->addJsBasico(iDE_SOCIO);

$xFRM->addHTML('<fieldset><legend>Resultados de la Busqueda</legend><div id="lst-resultados"></div></fieldset>');

echo $xFRM->get();

?>
</body>
<?php $jxc ->drawJavaScript(false, true); ?>
<script  >
function setRecibo(id){
	if (window.parent){ msrc = window.parent.document; }
	if (opener){ msrc = opener.document; }
<?php
		if( $f != false ){
			echo "
			if(msrc == null){} else {
				msrc.$f.$c.value 	= id;
				msrc.$f.$c.focus();
				msrc.$f.$c.select();
			}";
			/*if( $OtherEvent != ""){
					echo "if(msrc == null){} else { msrc.$OtherEvent;}";
			} */
		}
?>
jsEnd();
}
function jsEnd(){	var xGen = new Gen();	xGen.close(); }
function jsGetReporteRecibo(id){
	var xR	= new RecGen(); xR.reporte(id);
}
</script>
</html>
