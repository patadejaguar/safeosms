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
$xHP		= new cHPage("TR.lista de Alertas", HP_FORM);
$xF			= new cFecha();
$xlistas	= new cSQLListas();

$jxc 		= new TinyAjax();


function jsaGetListadoDeAvisos($tipo, $fecha_inicial, $fecha_final, $todas){
	$tipo			= ($tipo == SYS_TODAS) ? false : $tipo;
	$xT				= new cTipos();
	$xF				= new cFecha();
	$xAl			= new cAml_alerts();
	$xlistas		= new cSQLListas();
	$xBtn			= new cHButton();
	$xImg			= new cHImg();
	//
	$ByEstado		= ($xT->cBool($todas) == true) ? "" : " AND `estado_en_sistema`= " . SYS_UNO;
	$ByEstado		.= (setNoMenorQueCero($tipo) <= 0 ) ? "" : "  AND (`aml_risk_catalog`.`tipo_de_riesgo` =$tipo) ";
	$fecha_inicial	= $xF->getFechaISO($fecha_inicial);
	$fecha_final	= $xF->getFechaISO($fecha_final);
	$sql			= $xlistas->getListadoDeAlertas(false, false, false, false, $ByEstado);
	$xT				= new cTabla($sql);
	$xT->setWithMetaData();
	
	$xT->OButton("TR.Dictaminar", "jsModificarEstatus(_REPLACE_ID_)", $xT->ODicIcons()->REPORTE );
	$xT->OButton("TR.Panel", "jsToPanel(_REPLACE_ID_)", $xT->ODicIcons()->EJECUTAR );
	
	$xT->setKeyField( $xAl->getKey() );
	$xT->setKeyTable( $xAl->get() );
	return $xT->Show();
}

$jxc->exportFunction('jsaGetListadoDeAvisos', array('idtipoderiesgoaml', 'idfecha-1', 'idfecha-2', 'idactivas'), "#lstalertas");

$jxc->process();

$clave		= parametro("id", SYS_TODAS);
$xHP->init("jsGetListadoAvisos()");
$jsb		= new jsBasicForm("");



$xFRM		= new cHForm("frm_alertas", "./");
$xBtn		= new cHButton();		
$xTxt		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();

$jsb->setNameForm( $xFRM->getName() );
$selcat		= $xSel->getListaDeTipoDeRiesgoEnAML();
$selcat->addEvent("onblur", "jsGetListadoAvisos()");
$selcat->addEvent("onchange", "jsGetListadoAvisos()");

$selcat->addEspOption(SYS_TODAS);
$selcat->setOptionSelect(SYS_TODAS);
$xFRM->OHidden("idfecha-1", "", "");
$xFRM->OHidden("idfecha-2", "", "");
//$xFRM->addHElem( $xDate->get( $xFRM->lang("fecha inicial"), $xF->getDiaInicial(), 1 ));
//$xFRM->addHElem( $xDate->get( $xFRM->lang("fecha final"), $xF->getDiaFinal(), 2 ));

$xFRM->addHElem( $selcat->get(true) );
$xFRM->addSubmit("", "jsGetListadoAvisos()");

$xFRM->OCheck("TR.Mostrar Inactivas", "idactivas");

//$xFRM->addCreditBasico();
$xFRM->addHTML("<div id='lstalertas'></div>");

$xFRM->addAviso("", "idmsg");
echo $xFRM->get();

echo $xHP->setBodyEnd();
$jsb->show();
$jxc ->drawJavaScript(false, true);
?>
<!-- HTML content -->
<script>
var xG		= new Gen();
function jsGetListadoAvisos(){
	jsaGetListadoDeAvisos();
}
function jsModificarEstatus(id){
	xG.w({ url : "estatus_de_alerta.frm.php?codigo=" +id , w: 800, h: 600, tiny : true, callback: jsGetListadoAvisos  });
}
function jsCancelarAccion(){	$(window).qtip("hide");    }
function jsGuardarDescarto(){
	jsaDescartarRiesgo();
	jsCancelarAccion();
	jsaGetListadoDeAvisos();
}
function jsToPanel(id){
	var obj		= processMetaData("#tr-aml_alerts-" + id);
	var xPer	= new PersGen();
	xPer.goToPanel(obj.persona_de_origen, true);
}
</script>
<?php
$xHP->end();
?>