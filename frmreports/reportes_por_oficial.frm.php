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

	$xHP		= new cHPage("TR.Reportes por oficial_de_credito");
	echo $xHP->getHeader();
	
	echo $xHP->setBodyinit();
	
	$xRPT		= new cPanelDeReportes(iDE_USUARIO, "oficiales");
	$xRPT->setConOperacion(false);
	$xRPT->setConRecibos(false);
	$xRPT->setConCreditos();
	//$xRPT->addTipoDePago()
	
	echo $xRPT->get();
	echo $xRPT->getJs();
?>
</body>
</html>