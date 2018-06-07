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
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->init(); //"jsInit()"

$xFRM		= new cHForm("frm", "./");

$msg		= "";

//$xFRM->setNoAcordion(false);

/*$xFRM->addJsBasico();
$xFRM->addCreditBasico();
$xFRM->addSubmit();*/
$xFRM->addSeccion("idtphp", "php");
$xFRM->addHElem("<code id='php-code' class=\"php\"></code>");
$xFRM->endSeccion();

$xFRM->addSeccion("idtjs", "Inputs");
$xFRM->addHElem("<code id='js-code' class=\"javascript\"></code>");
$xFRM->endSeccion();
//$xFRM->OTextArea("idtexto", "", "TR.Texto");

echo $xFRM->get();
?>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/styles/default.min.css">


<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.12.0/highlight.min.js"></script>

<script>
/*function jsInit(){
	$("#php-code").html(session("var.serialize.php"));
	$("#js-code").html(session("var.serialize.js"));
	//hljs.initHighlightingOnLoad();
	//$("#idtexto").val(session("var.serialize"));
}*/
$(document).ready(function() {
	$("#php-code").html(session("var.serialize.php"));
	$("#js-code").html(session("var.serialize.js"));
	
	  $('code').each(function(i, block) {
	    hljs.highlightBlock(block);
	  });
});
</script>
<style>
	#idtexto {
		min-height: 400px;
	}
</style>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>