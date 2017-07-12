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
$xHP		= new cHPage("TR.OPERACIONES CON CAJA", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$jxc = new TinyAjax();
function jsaDesactivarCaja($id){
	$xCaja	= new cCaja($id);
	if($xCaja->init($id) == true){
		$xCaja->setCloseBox(getUsuarioActual(),0);
	}
	return $xCaja->getMessages();
}
function jsaActivarCaja($id){
	$xCaja	= new cCaja($id);
	if($xCaja->init($id) == true){
		//$xCaja->setOpenBox(getUsuarioActual(), 0);
		$xCaja->setReactivar();
	}
	return $xCaja->getMessages();
}
$jxc ->exportFunction('jsaDesactivarCaja', array('idcaja'), "#idaviso");
$jxc ->exportFunction('jsaActivarCaja', array('idcaja'), "#idaviso");
$jxc ->process();


$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xChk		= new cHCheckBox();
$xFRM->setTitle($xHP->getTitle());
$sql		= $xLi->getListadoDeTesoreriaCajas(SYS_TODAS, $xF->getDiaInicial(), $xF->get());
$xT			= new cTabla($sql);

$xT->OCheckBox("jsDesactivarCaja('". HP_REPLACE_ID ."')", "estatus", "chk");
$xFRM->addHElem($xT->Show());
$xFRM->addCerrar();
$xFRM->addAviso("", "idaviso");
$xFRM->OHidden("idcaja", "");

echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsDesactivarCaja(id){
	$("#idcaja").val(id);
	var idactiva	= $('#chk-' + id).prop('checked');
	//xG.alerta({msg:id});
	
	if(idactiva == true){
		jsaActivarCaja();
	} else {
		jsaDesactivarCaja();
	}
}
</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>