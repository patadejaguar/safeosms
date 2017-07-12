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
$xHP			= new cHPage("TR.EXPEDIENTE", HP_REPORT);
$xQL			= new MQL();
$xLi			= new cSQLListas();

$oficial 		= elusuario($iduser);
$idsocio 		= parametro("pa", false, MQL_INT);
$idsocio 		= parametro("socio", $idsocio, MQL_INT);
$idsocio 		= parametro("persona", $idsocio, MQL_INT);

$todo 			= parametro("f14", false, MQL_BOOL);

$xHP->init();

echo getRawHeader();
	echo "<p class='bigtitle'>EXPEDIENTE DE PERSONAS</p><hr />";
	
	$subf 		= ($todo == true) ? "" : " AND estatus_mvto=30";
	// REPORTES DE SOCIOS
	$cSocio			= new cSocio($idsocio);
	$cSocio->init();
	echo $cSocio->getFicha(true);
	
		
		$cTae		= new cTabla($xLi->getListadoDeActividadesEconomicas($idsocio));
		
		$cTae->setTdClassByType();
		echo $cTae->Show("TR.ACTIVIDAD_ECONOMICA");
		//
		$cTpr		= new cTabla($xLi->getListadoDeRelaciones($idsocio));
		$cTpr->setTdClassByType();
		echo $cTpr->Show(PERSONAS_TITULO_PARTES);			
		//
		
		$sqlcred 	= $xLi->getListadoDeCreditos($idsocio, true);
		
		$cTcred		= new cTabla($sqlcred);
		$cTcred->setTdClassByType();
		echo $cTcred->Show("TR.Creditos");	
		//
	
		$cTcta		= new cTabla($xLi->getListadoDeCuentasDeCapt($idsocio));
		$cTcta->setTdClassByType();
		echo $cTcta->Show("TR.CUENTAS DE CAPTACION");
		//
		
		$cTgar		= new cTabla($xLi->getListadoDeGarantiasReales($idsocio) );
		echo $cTgar->Show("TR.GARANTIAS DE CREDITOS");
	

		
		$cTbl = new cTabla($xLi->getListadoDeRecibosEmitidos($idsocio) );
		
		echo $cTbl->Show("TR.Recibos");	
		// MOVIMIENTOS

		$sqli = $sqlb18d . " AND socio_afectado=$idsocio " . $subf;
		//$xLi->getListadoDeOperaciones($idsocio);
		$cTi		= new cTabla($xLi->getListadoDeOperaciones($idsocio) );
		$cTi->setTdClassByType();
		echo $cTi->Show("TR.MOVIMIENTOS GENERALES");
		
		// NOTAS
		
		
		
		
		$cTi		= new cTabla($xLi->getListadoDeNotas($idsocio));
		$cTi->setTdClassByType();
		echo $cTi->Show("TR.NOTAS");
		
		
		//
echo getRawFooter();
?>
</body>
</html>