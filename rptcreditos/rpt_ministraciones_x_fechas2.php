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
include_once( "../core/entidad.datos.php");
include_once( "../core/core.deprecated.inc.php");
include_once( "../core/core.fechas.inc.php");
include_once( "../libs/sql.inc.php");
include_once( "../core/core.config.inc.php");
include_once( "../reports/PHPReportMaker.php");


$id = $_GET["is0"];
	if (!$id){
		echo "EL IDENTIFICADOR NO EXISTE";
		exit;
	}


$ip = $_GET["is1"];

$oficial = elusuario($iduser);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title></title>
</head>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
<body>
<!-- -->
<?php
echo getRawHeader();
echo "<p class='bigtitle'>MINISTRACIONES DE CREDITO DEL $id AL $ip</p><hr></hr>";
$sqlcc = $sql19b_ext . " AND operaciones_mvtos.tipo_operacion=110 AND operaciones_mvtos.fecha_afectacion>='$id' AND operaciones_mvtos.fecha_afectacion <='$ip'";


echo getRawFooter();
?>
</body>
</html>
<?php
$xml_f = $_GET["xm"];
$inp_f = $_GET["in"];
$input = "default";
	//require_once("PHPReportMaker.php");

  if (($inp_f) && ($inp_f!="")) {
  		$input = $inp_f;
  }
  /******************************************************************************
	*																										*
	*	Use this file to see a sample of PHPReports.											*
	*	Please check the PDF manual for see how to use it.									*
	*	It need to be placed on a directory reached by the web server.					*
	*																										*
	******************************************************************************/

		$oRpt = new PHPReportMaker();
	$oRpt->setDatabase(MY_DB_IN);
	$oRpt->setUser(RPT_USR_DB);
	$oRpt->setPassword(RPT_PWD_DB);
	$oRpt->setXML($xml_f);
	$oOut = $oRpt->createOutputPlugin($input);
	$oRpt->setOutputPlugin($oOut);
	$oRpt->run();		//	*/
?>