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

$jxc 		= new TinyAjax();


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
	$xT				= new cTabla($sql);
	$xT->setWithMetaData();
	
	//$xT->OButton("TR.Dictaminar", "jsModificarEstatus(_REPLACE_ID_)", $xT->ODicIcons()->REPORTE );
	//$xT->OButton("TR.Panel", "jsToPanel(_REPLACE_ID_)", $xT->ODicIcons()->EJECUTAR );
	
	
	$xT->setEventKey("jsGetPanelDeAlertas");
	$xT->setKeyField( $xAl->getKey() );
	$xT->setKeyTable( $xAl->get() );
	
	
	if(getSePuedeMostrar(iDE_AML, MQL_MOD)== true){
		$xT->addEditar();
	}
	if(getSePuedeMostrar(iDE_AML, MQL_DEL)== true){
		$xT->addEliminar();
	}
	$xT->setPagination(100);
	return $xT->Show();
}

$jxc->exportFunction('jsaGetListadoDeAvisos', array('idsubtipo', 'idfecha-1', 'idfecha-2', 'idactivas', 'idporfecha'), "#lstalertas");

$jxc->process();

$clave		= parametro("id", SYS_TODAS);
$xHP->init("jsGetListadoAvisos()");
$jsb		= new jsBasicForm("");



$xFRM		= new cHForm("frm_alertas", "./");
$xFRM->setNoAcordion();
$xBtn		= new cHButton();		
$xTxt		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();

$jsb->setNameForm( $xFRM->getName() );

$selcat		= $xSel->getListaDeTipoDeRiesgoEnAMLCAT('idsubtipo');
$selcat->addEvent("onblur", "jsGetListadoAvisos()");
$selcat->addEvent("onchange", "jsGetListadoAvisos()");
$selcat->addEspOption(SYS_TODAS);
$selcat->setOptionSelect(SYS_TODAS);



$xFRM->addSeccion("iddivtools", $xHP->getTitle());
$xFRM->addHElem( $selcat->get(true) );
$xFRM->OButton("TR.Obtener", "jsGetListadoAvisos()", $xFRM->ic()->CARGAR);
$xFRM->addCerrar();



$xFRM->ODate("idfecha-1", $xF->getFechaInicialDelAnno(), "TR.FECHA_INICIAL");
$xFRM->ODate("idfecha-2", $xF->getDiaFinal(), "TR.FECHA_FINAL");
$xFRM->OSiNo("TR.FILTRO POR FECHA", "idporfecha");

$xFRM->OCheck("TR.VER INACTIVO", "idactivas");

$xFRM->endSeccion();
$xFRM->addSeccion("iddivalertas", "TR.LISTA DE ALERTAS");
$xFRM->addHTML("<div id='lstalertas'></div>");

$xFRM->addAviso("", "idmsg");
echo $xFRM->get();


$jsb->show();
$jxc ->drawJavaScript(false, true);
?>
<!-- HTML content -->
<script>
var xG		= new Gen();
var xAml	= new AmlGen();
function jsGetListadoAvisos(){
	jsaGetListadoDeAvisos();
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