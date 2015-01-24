<?php
//=====================================================================================================

	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	$permiso = getSIPAKALPermissions(__FILE__);
	if($permiso === false){
		saveError(2, $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"],
"Acceso no permitido a :" . addslashes(__FILE__));
		header ("location:404.php?i=999");	
	}

	$iduser = $_SESSION["log_id"];
//=====================================================================================================
include_once "../core/entidad.datos.php";
include_once "../core/core.deprecated.inc.php";
include_once "../core/core.fechas.inc.php";
include_once "../libs/sql.inc.php";

$oficial = elusuario($iduser);
$i = $_GET["i"];
if (!$i){
	echo JS_CLOSE;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>LISTADO DE CREDITOS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>

<body>
<hr />
	<p class="frmTitle"><script> document.write(document.title); </script></p>
<hr />

<form name="" method="post" action="">
	<table border='0'  >
		<tr>
			<th>Solicitud</th>
			<th>Producto</th>
			<th>Frecuencia</th>
			<th>Ministrado</th>
			<th>Vencimiento</th>
			<th>Saldo</th>
		</tr>	
	<?php
		$sql = "SELECT * FROM creditos WHERE numero_socio=$i 
			ORDER BY saldo_actual DESC, 
			fecha_vencimiento DESC";
		$rs = mysql_query($sql);
		$trs = "";
		while($rw = mysql_fetch_array($rs)){
			$trs = $trs . "<tr>
								<td onclick='setCredito($rw[1])' class='key'>$rw[1]</td>
								<td>$rw[3]</td>
								<td>$rw[11]</td>
								<td>$rw[4]</td>
								<td>$rw[7]</td>
								<td>$rw[8]</td>
							</tr>";
		}
		
		echo $trs;
	?>
	</table>
	<p class="aviso"><input type="button" onclick="window.close();" value="cerrar ventana" /></p>
</form>
</body>
<script  >
function setCredito(id){
	opener.document.frmd300c597.c_a614d.value = id;
	window.close();
}
</script>
</html>
