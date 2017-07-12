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
include_once "../core/entidad.datos.php";
include_once "../core/core.deprecated.inc.php";
include_once "../core/core.fechas.inc.php";
include_once "../core/core.config.inc.php";

$id = $_GET["is0"];
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

$sql = "SELECT idoperaciones_recibos, numero_socio 
FROM operaciones_recibos where fecha_operacion>='$id' AND fecha_operacion<='$ip'";

	$rs1 = mysql_query($sql) ;
	while($rw = mysql_fetch_array($rs1)) {
		$sqltress = $sqlb18c . " AND valor_afectacion<>0 AND recibo_afectado=$rw[0]";
		$sqlcbt = "SELECT COUNT(idoperaciones_mvtos) AS 'numero' FROM operaciones_mvtos WHERE valor_afectacion<>0 AND recibo_afectado=$rw[0]";
		$nopers = mifila($sqlcbt, "numero");
//		echo "<hr></hr>";
		if($nopers > 0) {
			echo "<hr></hr>";
			minificha(6, $rw[0]);
			echo "<hr></hr>";
				//echo "$sqltress \n <br>";
			sqltabla($sqltress, "", "fieldnames");
		}		
		
		
	}
	@mysql_free_result($sqltress);	//	*/
echo getRawFooter();
?>
</body>
</html>
