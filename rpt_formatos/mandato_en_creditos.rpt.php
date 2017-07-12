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
$xHP			= new cHPage("TR.Mandato", HP_REPORT);
	
//include_once("../core/core.captacion.inc.php");

$idsolicitud 	= parametro("i", DEFAULT_CREDITO, MQL_INT); $idsolicitud = parametro("credito", $idsolicitud, MQL_INT); $idsolicitud = parametro("solicitud", $idsolicitud, MQL_INT);
$formato		= parametro("forma", 3, MQL_INT);
$oficial 		= elusuario($iduser);
$xHP->addCSS("../css/contrato.css.php");
//ini_set("display_errors", "on");
$xHP->init();
?>
<style>
body {	padding-top:1in;
		padding-bottom:1in;
		padding-left:1in;
		padding-right:1in;
		
	}
</style>
<!-- -->
<?php
$xFecha				= new cFecha();
$xCred 				= new cCredito($idsolicitud);
$xCred->initCredito();
$acreditante		= $xCred->getClaveDePersona();

$xFMT		= new cFormato($formato);
$xFMT->setCredito($idsolicitud);
$xFMT->setProcesarVars();
echo $xFMT->get();

$mSQL		= new cSQLListas();
$mql		= new MQL();
$sql 		= $mSQL->getListadoDeAvales($idsolicitud, $acreditante);
$rs			= $mql->getDataRecord($sql);
//setLog($sql);
$forma		= 5002;
//$xAval		= new cSocios_relaciones();

foreach ($rs as $rows){
	echo "<br class='nuevapagina' />";
	$persona	= $rows["numero_socio"];
	$idrelacion	= $rows["num"];
	$xSoc		= new cSocio($persona);
	//$xRel		= new cPersonasRelaciones($idrelacion); $xRel->init();
	if( $xSoc->init() == true ){
		$xFMT2	= new cFormato($forma);
		$xFMT2->setCredito($idsolicitud);
		$xFMT2->setProcesarVars(array("aval_nombre_completo" => $xSoc->getNombreCompleto()));
		echo $xFMT2->get();
		
	}
}

?>
</body>
</html>
