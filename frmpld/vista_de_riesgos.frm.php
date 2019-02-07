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
$xHP		= new cHPage("TR.lista de riesgos confirmados", HP_FORM);
$xF			= new cFecha();
$xlistas	= new cSQLListas();
$DDATA		= $_REQUEST;
$jxc 		= new TinyAjax();


function jsaGetListadoDeAvisos($tipo, $fecha_inicial, $fecha_final, $byfechas){
	$tipo			= ($tipo == SYS_TODAS) ? false : $tipo;
	$xF				= new cFecha();
	$xAl			= new cAml_risk_register();
	$xlistas		= new cSQLListas();
	$xBtn			= new cHButton();
	$xImg			= new cHImg();
	
	$fecha_inicial	= $xF->getFechaISO($fecha_inicial);
	$fecha_final	= $xF->getFechaISO($fecha_final);
	if($byfechas == 1){
		$sql		= $xlistas->getListadoDeRiesgosConfirmados($fecha_inicial, $fecha_final, false, $tipo, false,  " AND (`aml_risk_register`.`estado_de_envio` =0) AND (`aml_risk_register`.`fecha_de_checking` =0) ");
	} else {
		$sql		= $xlistas->getListadoDeRiesgosConfirmados(false, false, false, $tipo, false,  " AND (`aml_risk_register`.`estado_de_envio` =0) AND (`aml_risk_register`.`fecha_de_checking` =0) ");
	}
	$xT				= new cTabla($sql,0, "idtblvistariesgos");
	//setLog($sql);
	$xT->OButton("TR.Dictaminar", "jsModificarEstatus(_REPLACE_ID_)", $xT->ODicIcons()->REPORTE);
	$xT->OButton("TR.Modificar", "jsEditarRiesgo(_REPLACE_ID_)", $xT->ODicIcons()->EDITAR);
	//$xT->addTool(1);
	$xT->setKeyField( $xAl->getKey() );
	$xT->setKeyTable( $xAl->get() );
	$xT->setOmitidos("persona");
	
	if(MODO_CORRECION == true OR MODO_DEBUG == true OR MODO_MIGRACION == true){
		$xT->addEliminar();
	}
		
	return $xT->Show();
}

$jxc ->exportFunction('jsaGetListadoDeAvisos', array('idtipoderiesgoaml', 'idfecha1', 'idfecha2', 'idporfecha'), "#lstalertas");


$jxc ->process();

$xHP->init("jsGetListadoAvisos()");

//$jsb		= new jsBasicForm("");

$xFRM		= new cHForm("frm_alertas", "./");
$xBtn		= new cHButton();		
$xTxt		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();
$xFRM->setNoAcordion();
$xFRM->addCerrar();
$xFRM->setTitle($xHP->getTitle());

//$jsb->setNameForm( $xFRM->getName() );
$selcat		= $xSel->getListaDeTipoDeRiesgoEnAML();
//$xSel->addOptions(array(SYS_TODAS => SYS_TODAS));
//$selcat		= $xSel->getCatalogoDeRiesgos();
$selcat->addEvent("onblur", "jsGetListadoAvisos()");
$selcat->addEvent("onchange", "jsGetListadoAvisos()");

$selcat->addEspOption(SYS_TODAS);
$selcat->setOptionSelect(SYS_TODAS);



$xFRM->addHElem( $selcat->get(true) );

$xFRM->OButton("TR.Obtener", "jsGetListadoAvisos()", $xFRM->ic()->DESCARGAR, "idcmdobtener", "blue");


$xFRM->OSiNo("TR.FILTRAR POR FECHA", "idporfecha"); $xFRM->addControEvt("chk-idporfecha", "jsDisFechas", "change");

$xFRM->ODate("idfecha1", $xF->getFechaInicialDelAnno(), "TR.FECHA_INICIAL");
$xFRM->ODate("idfecha2", $xF->getDiaFinal(), "TR.FECHA_FINAL");


$xta		= new cHTextArea();

//$xFRM->addCreditBasico();
$xFRM->addHTML("<div id='lstalertas'></div>");

$xFRM->addAviso("", "idmsg");
echo $xFRM->get();


//$jsb->show();
$jxc ->drawJavaScript(false, true);
?>
<!-- HTML content -->
<script>
var xG		= new Gen();

function jsGetListadoAvisos(){
	jsaGetListadoDeAvisos();
	jsDisFechas();
}
function jsModificarEstatus(id){ xG.w({ url : "estatus_de_riesgo.frm.php?codigo=" +id , w: 800, h: 800, tiny : true, callback: jsGetListadoAvisos }); }
function jsEditarRiesgo(id){ xG.w({ url : "riesgo.editar.frm.php?id=" +id , w: 800, h: 800, tiny : true, callback: jsGetListadoAvisos }); /*xG.editar({tabla: "aml_risk_register", id: id});*/ }

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

</script>
<?php
$xHP->fin();
?>