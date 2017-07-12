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
$xHP		= new cHPage("", HP_FORM);

$DDATA		= $_REQUEST;
$jxc 		= new TinyAjax();
function jsaGetListadoDeAvisos($tipo, $fecha_inicial, $fecha_final){
	$tipo			= ($tipo == SYS_TODAS) ? false : $tipo;
	$xF				= new cFecha();
	$xAl			= new cAml_alerts();
	$xlistas		= new cSQLListas();
	$xBtn			= new cHButton();
	$xImg			= new cHImg();
	
	$fecha_inicial	= $xF->getFechaISO($fecha_inicial);
	$fecha_final	= $xF->getFechaISO($fecha_final);
	$sql			= $xlistas->getListadoDeRiesgosConfirmados($fecha_inicial, $fecha_final, $tipo);// getListadoDeAlertas($tipo, $fecha_inicial, $fecha_final, false, " AND `estado_en_sistema`= " . SYS_UNO);
	$xT				= new cTabla($sql);
	
	$xT->addEspTool($xImg->get24("check", " onclick=\"jsConfirmRiesgo(_REPLACE_ID_)\" "));
	$xT->addEspTool($xImg->get24("delete", " onclick=\"jsDescartarRiesgo(_REPLACE_ID_)\" "));

	$xT->setKeyField( $xAl->getKey() );
	$xT->setKeyTable( $xAl->get() );
	return $xT->Show();
}
function jsaConfirmRiesgo($id){
	$xAML	= new cAMLAlertas($id);
	$xAML->setConfirmAlerta($observaciones);
	return $xAML->getMessages(OUT_HTML);
}

function jsaDescartarRiesgo($id, $observaciones){
	$xAML	= new cAMLAlertas($id);
	$xAML->setDescartarAlerta($observaciones);
	return $xAML->getMessages(OUT_HTML);
}

$jxc ->exportFunction('jsaGetListadoDeAvisos', array('idtipoderiesgo', 'idfecha-1', 'idfecha-2'), "#lstalertas");
$jxc ->exportFunction('jsaConfirmRiesgo', array('idriesgo'), "#idmsg");
$jxc ->exportFunction('jsaDescartarRiesgo', array('idriesgo', 'iddetalles'), "#idmsg");

$jxc ->process();

echo $xHP->getHeader();
$jsb	= new jsBasicForm("");



echo $xHP->setBodyinit();

$xFRM		= new cHForm("frm_alertas", "./");
$xBtn		= new cHButton();		
$xTxt		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();

$jsb->setNameForm( $xFRM->getName() );
$selcat		= $xSel->getCatalogoDeRiesgos();
$selcat->addEvent("onblur", "jsGetListadoAvisos()");
$selcat->addEvent("onchange", "jsGetListadoAvisos()");

$selcat->addEspOption(SYS_TODAS);
$selcat->setOptionSelect(SYS_TODAS);

$xFRM->addHElem( $xDate->get( $xFRM->lang("fecha inicial"), false, 1 ));
$xFRM->addHElem( $xDate->get( $xFRM->lang("fecha final"), false, 2 ));

$xFRM->addHElem( $selcat->get( $xFRM->lang(array("tipo de", "Riesgo")), true) );

$xFRM->addSubmit("", "jsGetListadoAvisos()");
$xta		= new cHTextArea();
$xFRM9	= new cHForm("frmupdateriesgo");
$xFRM9->addHElem("<div id='tx1'>" . $xta->get("iddetalles", "", "TR.Notas") . "</div>");
//$xFRM9->addObservaciones();
$xFRM9->addSubmit("", "jsGuardarDescarto()", "jsCancelarAccion()");
$xFRM->addHTML("<div class='inv' id='iduriesgo'>" . $xFRM9->get() . "</div>");

//$xFRM->addCreditBasico();
$xFRM->addHTML("<div id='lstalertas'></div>");
$xFRM->addHTML("<input type='hidden' id='idriesgo' />");
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

    function jsConfirmRiesgo(is){
        $("#idriesgo").val(is);
        var siR		= confirm("Desea Confirmar la ALERTA como RIESGO AML?");
        if(siR){  jsaConfirmRiesgo();   }
    }
	function jsDescartarRiesgo(is){
		$("#idriesgo").val(is);
		getModalTip(window, $("#iduriesgo"), xG.lang(["actualizar", "riesgo"]));
	}
	function jsCancelarAccion(){	$(window).qtip("hide");    }
	function jsGuardarDescarto(){
		jsaDescartarRiesgo();
		jsCancelarAccion();
		jsaGetListadoDeAvisos();
	}
</script>
<?php
$xHP->end();
?>