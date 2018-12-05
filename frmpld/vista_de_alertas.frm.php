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
$xHP		= new cHPage("TR.Operaciones con Alertas", HP_FORM);
$xF			= new cFecha();
$xlistas	= new cSQLListas();
$xRuls		= new cReglaDeNegocio();
$jxc 		= new TinyAjax();

//$UsarFotos	= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_USAR_FOTOS);		//regla de negocio

function jsaGetListadoDeAvisos($subtipo, $fecha_inicial, $fecha_final, $todas, $byfechas){
	$subtipo		= setNoMenorQueCero($subtipo);
	$xT				= new cTipos();
	$xF				= new cFecha();
	$xAl			= new cAml_alerts();
	$xlistas		= new cSQLListas();
	$xBtn			= new cHButton();
	$xImg			= new cHImg();
	//
	$ByEstado		= ($xT->cBool($todas) == true) ? "" : " AND `estado_en_sistema`= " . SYS_UNO;
	$ByEstado		.= (setNoMenorQueCero($subtipo) <= 0 ) ? "" : "  AND (`aml_alerts`.`tipo_de_aviso` =$subtipo) ";
	$fecha_inicial	= $xF->getFechaISO($fecha_inicial);
	$fecha_final	= $xF->getFechaISO($fecha_final);
	if($byfechas == 1){
		$sql		= $xlistas->getListadoDeAlertas(false, $fecha_inicial, $fecha_final, false, $ByEstado);
	} else {
		$sql		= $xlistas->getListadoDeAlertas(false, false, false, false, $ByEstado);
	}
	$xT				= new cTabla($sql, 0, "tblvistaalertas");
	$xT->setWithMetaData();
	
	//$xT->OButton("TR.Dictaminar", "jsModificarEstatus(_REPLACE_ID_)", $xT->ODicIcons()->REPORTE );
	//$xT->OButton("TR.Panel", "jsToPanel(_REPLACE_ID_)", $xT->ODicIcons()->EJECUTAR );
	
	
	$xT->setEventKey("jsGetPanelDeAlertas");
	$xT->setKeyField( $xAl->getKey() );
	$xT->setKeyTable( $xAl->get() );
	$xT->setOmitidos("persona_de_origen");
	
	$xT->addEditar();
	$xT->addEliminar();
	
	$xT->setPagination(100);
	
	return $xT->Show();
}

$jxc->exportFunction('jsaGetListadoDeAvisos', array('idsubtipo', 'idfecha1', 'idfecha2', 'idactivas', 'idporfecha'), "#lstalertas");

$jxc->process();

$clave		= parametro("id", SYS_TODAS);
$xHP->init("jsGetListadoAvisos()");
//$jsb		= new jsBasicForm("");



$xFRM		= new cHForm("frm_alertas", "./");
$xFRM->setNoAcordion();
$xBtn		= new cHButton();		
$xTxt		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();

//$jsb->setNameForm( $xFRM->getName() );
$xFRM->addCerrar();
$xFRM->setTitle($xHP->getTitle());


$selcat		= $xSel->getListaDeTipoDeRiesgoEnAMLCAT('idsubtipo');
$selcat->addEvent("onblur", "jsGetListadoAvisos()");
$selcat->addEvent("onchange", "jsGetListadoAvisos()");
$selcat->addEspOption(SYS_TODAS);
$selcat->setOptionSelect(SYS_TODAS);



$xFRM->addSeccion("iddivtools", $xHP->getTitle());
$xFRM->addHElem( $selcat->get(true) );
$xFRM->OButton("TR.Obtener", "jsGetListadoAvisos()", $xFRM->ic()->DESCARGAR);


$xFRM->OSiNo("TR.FILTRO POR FECHA", "idporfecha"); $xFRM->addControEvt("chk-idporfecha", "jsDisFechas", "change");

$xFRM->ODate("idfecha1", $xF->getFechaInicialDelAnno(), "TR.FECHA_INICIAL");
$xFRM->ODate("idfecha2", $xF->getDiaFinal(), "TR.FECHA_FINAL");


$xFRM->OCheck_13("TR.VER INACTIVO", "idactivas");

$xFRM->endSeccion();
$xFRM->addSeccion("iddivalertas", "TR.LISTA DE ALERTAS");
$xFRM->addHTML("<div id='lstalertas'></div>");

$xFRM->addAviso("", "idmsg");
echo $xFRM->get();


//$jsb->show();

$jxc ->drawJavaScript(false, true);
?>
<!-- HTML content -->
<script>
var xG		= new Gen();
var xAml	= new AmlGen();
function jsGetListadoAvisos(){
	jsaGetListadoDeAvisos();
	jsDisFechas();
}
function jsDisFechas(){
	var idStat	= entero($("#idporfecha").val());
	console.log("Estatus: " + idStat);
	if(idStat == 1){
		xG.verControl("idfecha1", true);
		xG.verControl("idfecha2", true);
	} else {
		xG.verControl("idfecha1");
		xG.verControl("idfecha2");
	}

	
}
/*function jsModificarEstatus(id){
	xG.w({ url : "estatus_de_alerta.frm.php?codigo=" +id , w: 800, h: 800, tiny : true, callback: jsGetListadoAvisos  });
}*/
function jsCancelarAccion(){	$(window).qtip("hide");    }
function jsGuardarDescarto(){
	jsaDescartarRiesgo();
	jsCancelarAccion();
	jsaGetListadoDeAvisos();
}
function jsGetPanelDeAlertas(id){	xAml.getPanelDeAlerta(id); }
/*function jsToPanel(id){
	var obj		= processMetaData("#tr-aml_alerts-" + id);
	var xPer	= new PersGen();
	xPer.goToPanel(obj.persona_de_origen, false);
}*/
</script>
<?php
$xHP->fin();
?>