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
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

echo $xHP->getHeader();

$jsb	= new jsBasicForm("frmjson");
//$jxc ->drawJavaScript(false, true);
echo $xHP->setBodyinit();

$xFRM	= new cHForm("frmjson", "../install/update.json.php");
$xBtn	= new cHButton();		
$xTxt	= new cHText();

$xArea	= new cHTextArea("idjson", "idjson", "Texto JSON a Exportar");

$xFRM->addHElem($xArea->get());

$xFRM->addHElem( $xBtn->getBasic("Enviar", "jsSubmit()", "guardar") );


echo $xFRM->get();

echo $xHP->setBodyEnd();
$jsb->show();
?>
<style>
<!--

-->
#idjson {
	width: 99%;
	height: 400px;
}
</style>
<!-- HTML content -->
<script>
function jsSubmit(){
	var myobj = $.parseJSON( $("#idjson").val() );
	$.ajax({
	  url: "../install/update.json.php",
	  data: JSON.stringify(myobj),
	  processData: false,
	  dataType: "json",
	  type:'POST',
	  success:function(a) { },
	  error:function() {}
	});
}
</script>
<?php
$xHP->end();
?>