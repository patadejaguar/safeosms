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


function jsaGetListadoDeAvisos($tipo, $fecha_inicial, $fecha_final){
	$tipo			= ($tipo == SYS_TODAS) ? false : $tipo;
	$xF				= new cFecha();
	$xAl			= new cAml_risk_register();
	$xlistas		= new cSQLListas();
	$xBtn			= new cHButton();
	$xImg			= new cHImg();
	
	$fecha_inicial	= $xF->getFechaISO($fecha_inicial);
	$fecha_final	= $xF->getFechaISO($fecha_final);
	$sql			= $xlistas->getListadoDeRiesgosConfirmados(false, false, false, $tipo, false,  " AND (`aml_risk_register`.`estado_de_envio` =0) AND (`aml_risk_register`.`fecha_de_checking` =0) ");
	$xT				= new cTabla($sql);
	//setLog($sql);
	$xT->OButton("TR.Dictaminar", "jsModificarEstatus(_REPLACE_ID_)", $xT->ODicIcons()->REPORTE);
	$xT->OButton("TR.Modificar", "jsEditarRiesgo(_REPLACE_ID_)", $xT->ODicIcons()->EDITAR);
	//$xT->addTool(1);
	$xT->setKeyField( $xAl->getKey() );
	$xT->setKeyTable( $xAl->get() );
	return $xT->Show();
}

$jxc ->exportFunction('jsaGetListadoDeAvisos', array('idtipoderiesgoaml', 'idfecha-1', 'idfecha-2'), "#lstalertas");


$jxc ->process();

$xHP->init("jsGetListadoAvisos()");

//$jsb		= new jsBasicForm("");

$xFRM		= new cHForm("frm_alertas", "./");
$xBtn		= new cHButton();		
$xTxt		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();

//$jsb->setNameForm( $xFRM->getName() );
$selcat		= $xSel->getListaDeTipoDeRiesgoEnAML();
//$xSel->addOptions(array(SYS_TODAS => SYS_TODAS));
//$selcat		= $xSel->getCatalogoDeRiesgos();
$selcat->addEvent("onblur", "jsGetListadoAvisos()");
$selcat->addEvent("onchange", "jsGetListadoAvisos()");

$selcat->addEspOption(SYS_TODAS);
$selcat->setOptionSelect(SYS_TODAS);

$xFRM->setTitle($xHP->getTitle());

$xFRM->OHidden("idfecha-1", "", "");
$xFRM->OHidden("idfecha-2", "", "");
//$xFRM->addHElem( $xDate->get( $xFRM->lang("fecha inicial"), $xF->getDiaInicial(), 1 ));
//$xFRM->addHElem( $xDate->get( $xFRM->lang("fecha final"), $xF->getDiaFinal(), 2 ));

$xFRM->addHElem( $selcat->get(true) );
$xFRM->addSubmit("", "jsGetListadoAvisos()");

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
}
function jsModificarEstatus(id){ xG.w({ url : "estatus_de_riesgo.frm.php?codigo=" +id , w: 800, h: 600, tiny : true, callback: jsGetListadoAvisos }); }
function jsEditarRiesgo(id){ xG.w({ url : "riesgo.editar.frm.php?id=" +id , w: 800, h: 600, tiny : true, callback: jsGetListadoAvisos }); /*xG.editar({tabla: "aml_risk_register", id: id});*/ }
</script>
<?php
$xHP->fin();
?>