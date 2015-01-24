<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package
 */
//=====================================================================================================
//=====>	INICIO_H
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	$theFile					= __FILE__;
	$permiso					= getSIPAKALPermissions($theFile);
	if($permiso === false){		header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//<=====	FIN_H
	
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
	require_once("." . TINYAJAX_PATH . "/TinyAjax.php");
	
	$cuenta		= (isset( $_POST["NumeroDeCuenta"] )) ? $_POST["NumeroDeCuenta"] : false;
	
	$xP			= new cHPage("Guia Contable");
	$xP->setIncludes();
	echo $xP->getHeader();

	
	
	$oficial	= elusuario($iduser);
	$jxc = new TinyAjax();
	
	function jsaSaveOperacion($cuenta, $operacion, $descripcion){
		
	}
	
	$jxc ->exportFunction('jsaSaveOperacion', array('idCuenta', 'idOperacion', 'idDescripcion') );
	$jxc ->process();
	$xHTxt		= new cHText("id");
	$xHTxt->setIncludeLabel();
	
	$jsb		= new jsBasicForm("frmGuia");
	
	$jsb->setIncludeJQuery();
	$jsb->show();
	$jxc ->drawJavaScript(false, true);
?>
<style>

#wrapper {
    width:600px;
}
ul.tabs {
    width:600px;
    margin:0;
    padding:0;
}
ul.tabs li {
    display:block;
    float:left;
    padding:0 5px;

}
ul.tabs li a {
    display:block;
    float:left;
    padding:5px;
    font-size:0.8em;
    background-color:#e0e0e0;
    color:#666;
    text-decoration:none;
    border-radius: 3px;
}
.selected {
    font-weight:bold;
}
.tab-content {
    clear:both;
    border:1px solid #ddd;
    padding:10px;
    border-radius: 3px;
}
</style>
<body>
<?php 
if ( $cuenta == false ){
	$xHForm	= new cHForm("frmGuia", "guia-contable.frm.php");
	$xHForm->addHElem( $xHTxt->get("NumeroDeCuenta", CUENTA_DE_CUADRE, "Numero de Cuenta") );
	$xHForm->addSubmit("Empezar la Captura");
	echo $xHForm->get();
} else {
?>
<div id="wrapper">
<?php
$xCta		= new cCuentaContable($cuenta);
$xCta->init();
echo $xCta->getFicha();

/* echo $xCta->getMessages("html"); */

?>
    <ul class="tabs">
        <li><a href="#" class="defaulttab" rel="tabs1">Se Carga Cuando</a></li>
        <li><a href="#" rel="tabs2">Se Abona Cuando</a></li>
        <li><a href="#" rel="tabs3">Agregar Nuevo</a></li>
    </ul>
 
    <div class="tab-content" id="tabs1">
	</div>
    <div class="tab-content" id="tabs2">
    </div>
    <div class="tab-content" id="tabs3">

    <?php
	$xHForm		= new cHForm("frmGuia", "guia-contable.frm.php");
	//$xHForm->addHElem( $xHTxt->get("NumeroDeCuenta", $cuenta, "Numero de Cuenta") );
	$EspSel		= "<select name='cOperacion' id='idOperacion'><option value='" . TM_ABONO . "'>ESTA CUENTA SE ABONA CUANDO</option>
					<option value='" . TM_CARGO . "'>ESTA CUENTA SE CARGA CUANDO</option></select>";
	$xHForm->addHElem( $EspSel );
	$xHForm->addHElem( "<textarea name='tDescription' id='idDescripcion' cols='60' rows='10'></textarea>" );
	$xHForm->addHElem( "<input  type='hidden' name='idCuenta' id='idCuenta' value='$cuenta' /> " );
	$xHForm->addSubmit("Guardar", "jsSaveOperacion");
	echo $xHForm->get();
    
    ?>

    </div>
</div>
<?php 
}
?>
</body>
<!-- JS to add -->
<script type="text/javascript">
$(document).ready(function() {
	 
	$('.tabs a').click(function(){
		switch_tabs($(this));
		//
	});
 
	switch_tabs($('.defaulttab'));
 
});
 
function switch_tabs(obj)
{
	$('.tab-content').hide();
	$('.tabs a').removeClass("selected");
	var id = obj.attr("rel");
 
	$('#'+id).show();
	obj.addClass("selected");
}
function jsSaveOperacion(){
	
}
</script>
<?php
$xP->end();
?>