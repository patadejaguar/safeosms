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
$xHP		= new cHPage("TR.Buscar Grupos_SOLIDARIOS", HP_FORM);
$jxc 		= new TinyAjax();
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xTDic		= new cHDicccionarioDeTablas();

function jsaGetGrupos($txt){
	$nombre		= $txt;
	$xLi		= new cSQLListas();
	$ByNombre	= ($nombre != "") ? " AND (`nombre_gruposolidario` LIKE '%$nombre%' OR `representante_nombrecompleto` LIKE '%$nombre%' OR `vocalvigilancia_nombrecompleto` LIKE '%$nombre%') " : "";	
	$sql 		= $xLi->getListadoDeGrupos() . " $ByNombre ORDER BY	`nombre_gruposolidario`	LIMIT 0,10 ";
	$xT			= new cTabla($sql);
	$xT->setEventKey("setGrupo");
	return $xT->Show();
}
$jxc ->exportFunction('jsaGetGrupos', array('idbusqueda'), "#listabusqueda");
$jxc ->process();

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$OtherEvent	= parametro("ev", "", MQL_RAW);	//Otro Evento Desatado
$OtherEvent	= parametro("callback", "", MQL_RAW);
$control	= parametro("control", "idsocio", MQL_RAW);
$tiny 		= parametro("tinybox", false, MQL_BOOL);


$xHP->init();

$xFRM		= new cHForm("frmbuscargrupos", "./");
$xTxt		= new cHText();
$msg		= "";
$xFRM->setTitle($xHP->getTitle());

$sql 	= $xLi->getListadoDeGrupos() . " ORDER BY `nombre_gruposolidario` LIMIT 0,10 ";
$xT		= new cTabla($sql);
$xT->setKeyField("idsocios_grupossolidarios");
$xT->setEventKey("setGrupo");


$xTxt->setDivClass("");
$xTxt->addEvent("jsaGetGrupos()", "onkeyup");
$xFRM->addDivSolo($xTxt->getLabel("TR.NOMBRE"), $xTxt->getNormal("idbusqueda", ""), "tx14", "tx34");
$xFRM->OButton("TR.Buscar", "jsaGetGrupos()", "buscar");
$xFRM->addHTML("<div class='tx1' id='listabusqueda'>" . $xT->Show() . "</div>");

echo $xFRM->get();
$jxc ->drawJavaScript(false, true);

?>
<script>
var idgrp		= "<?php echo $control; ?>";
var xG			= new Gen();
function setGrupo(id){
	if (window.parent){ msrc = window.parent.document; }
	if (opener){ msrc = opener.document; }
	if(msrc == null){} else {
	<?php
	if($OtherEvent != ""){
		echo "if(msrc.$OtherEvent != \"undefined\"){ msrc.$OtherEvent(id); }";
	}
	?>		
		if(msrc.getElementById(idgrp)){
			oid			=  msrc.getElementById(idgrp);
			oid.value	= id;
			oid.focus();
			oid.select();
			if(msrc.getElementById("nombregrupo")){
				//msrc.getElementById("nombregrupo").value = 
			}
			xG.close();		
		}

	}
}
</script>
<?php
$xHP->fin();
?>