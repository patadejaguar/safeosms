<?php
//=====================================================================================================
	include_once("../core/go.login.inc.php");
	include_once ("../core/core.error.inc.php");
	$permiso = getSIPAKALPermissions(__FILE__);
	if($permiso === false){
		header ("location:../404.php?i=999");	
	}
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");

	
$oficial = elusuario($iduser);
//TODO: Actualizar Modulo
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title></title>
</head>
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
<body>
<p class="aviso">LISTADO DE CREDITOS A EDITAR EN LA SESI&Oacute;N DE CR&Eacute;DITO ACTUAL</p>
<!-- -->
<?php
//echo getRawHeader();
echo "<hr>";
sqltabla($sqlb11 . " WHERE creditos_periodos.idcreditos_periodos=$periodosolicitudes", "", "fieldnames");
echo "<hr /> <hr />";
$sqlI = $sqlb19c . " AND  creditos_solicitud.periodo_solicitudes=$periodosolicitudes AND creditos_solicitud.estatus_actual=99";
//echo getRawFooter();
$cTbl = new cTabla($sqlI, 2);
	$cTbl->setWidth();
	$cTbl->addTool(1);
	//$cTbl->addTool(2);
	$cTbl->setKeyTable("creditos_solicitud");
	$cTbl->setKeyField("numero_solicitud");
	$cTbl->Show("", false);

?>
</body>
<script  >
	<?php
		echo $cTbl->getJSActions();
	?>	
</script>
</html>
