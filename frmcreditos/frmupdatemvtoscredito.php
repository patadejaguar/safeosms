<?php
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
$xHP			= new cHPage("Edicion de Movimientos de Credito");
$docto 			= ( isset($_REQUEST["idsolicitud"]))? $_REQUEST["idsolicitud"] : SYS_NINGUNO;
echo $xHP->getHeader();

echo $xHP->setBodyinit();
if($docto == SYS_NINGUNO){
	$xFRM	= new cHForm("frmeditmvtos", "frmupdatemvtoscredito.php");
	$xBtn	= new cHButton();
	$xTxt	= new cHText();
	
	$xTb	= new cOperaciones_tipos();
	$xSel	= new cSelect("idtipo_de_operacion", "idtipo_de_operacion", $xTb->get() );
	$xSel->addEspOption(SYS_TODAS);
	$xSel->setOptionSelect(SYS_TODAS);
	
	
	$xFRM->addCreditBasico();
	
	$xFRM->addHElem($xSel->get($xHP->lang("Tipo de Operacion"), true) );
	
	$xFRM->addSubmit("Aceptar", "frmSubmit()");
	echo $xFRM->get();

} else {
	//CREDITO
	$xCred		= new cCredito($docto);
	$xCred->init();
	echo $xCred->getFicha(true);
	$otros		= "";
	
	$xSQL		= new cSQLListas();
	$sql		= $xSQL->getListadoDeOperaciones("", $docto, "", $otros);
	$cEdit		= new cTabla($sql);
	$cEdit->addTool(SYS_UNO);
	$cEdit->addTool(SYS_DOS);
	$cEdit->setEventKey("jsEditClick");
	$cEdit->setTdClassByType();
	$cEdit->setKeyField("idoperaciones_mvtos");
	echo $cEdit->Show();
	
	echo $cEdit->getJSActions(true);
}

?>
</body>
	<?php
	$xc	= new jsBasicForm("frmeditmvtos");
	//$xc->setIncludeJQuery();
	//$xc->setNombreCtrlRecibo("cNumeroRecibo");
	$xc->show();
	?>
<script>

</script>
</html>
