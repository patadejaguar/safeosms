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
$xHP		= new cHPage("TR.Buscar Grupos", HP_FORM);
$jxc 		= new TinyAjax();
function jsaGetGrupos($txt){
	$nombre		= $txt;
	$xLi		= new cSQLListas();
	$ByNombre	= ($nombre != "") ? " AND (`nombre_gruposolidario` LIKE '%$nombre%' OR `representante_nombrecompleto` LIKE '%$nombre%' OR `vocalvigilancia_nombrecompleto` LIKE '%$nombre%') " : "";	
	$sql 		= $xLi->getListadoDeGrupos() . "
	$ByNombre
	ORDER BY
	`nombre_gruposolidario`
	LIMIT 0,10 ";
	$xT		= new cTabla($sql);
	
	return $xT->Show();
}
$jxc ->exportFunction('jsaGetGrupos', array('idbusqueda'), "#listabusqueda");
$jxc ->process();

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->init();

$xFRM		= new cHForm("frmbuscargrupos", "./");
$xTxt		= new cHText();
$msg		= "";

//$xFRM->OText("idbusqueda", "", "TR.Buscar Grupo");
//$xFRM->addHElem(  );
$xTxt->setDivClass("");
$xTxt->addEvent("jsaGetGrupos()", "onkeyup");
$xFRM->addDivSolo($xTxt->getNormal("idbusqueda", "", "TR.Nombre"), " ");
$xFRM->OButton("TR.Buscar", "jsaGetGrupos()", "buscar");
$xFRM->addHTML("<div class='tx1' id='listabusqueda'></div>");

//$xFRM->addJsBasico();
//$xFRM->addCreditBasico();

//$xFRM->addSubmit();

echo $xFRM->get();
$jxc ->drawJavaScript(false, true);

?>
<script>
function setGrupo(id){
	if (window.parent){ msrc = window.parent.document; }
	if (opener){ msrc = opener.document; }
<?php
	if(trim($jscallback) != ""){ echo "msrc.$jscallback(id);"; }
		if( trim($form) != "" ){
			
			/*echo "
			if(msrc == null){} else {
				msrc.$f.$c.value 	= id;
				msrc.$f.$c.focus();
				msrc.$f.$c.select();
			}";*/
			//if( $OtherEvent != ""){
					//echo "if(msrc == null){} else { msrc.$OtherEvent;}";
			//} 
		}
?>
}
</script>
<?php

$xHP->fin();
?>