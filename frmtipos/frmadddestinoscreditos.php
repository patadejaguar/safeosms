<?php
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
$xHP		= new cHPage("", HP_FORM);

$oficial = elusuario($iduser);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Agregar Destino/Aplicaci&oacute;n de Creditos</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<body>
<hr></hr>
	<p class="frmTitle"><script> document.write(document.title); </script></p>
<hr></hr>

<form name="frmadddestinos" method="post" action="frmadddestinoscreditos.php">
	<table border='0'  >
		<tr>
			<th>Identificador</th><td><input type='text' name='id' value='0'></td>
			<th>Descripcion</th><td><input type='text' name='descripcion' value='0' size="50"></td>
		</tr>
	</table>
	<input type="submit" value="Enviar">
</form>
<?php 
$description = $_POST["descripcion"];
$id = $_POST["id"];
$sqlp = "SELECT idcreditos_destinos AS 'Identificador', descripcion_destinos AS 'Descripcion' FROM creditos_destinos";
	if ($description) {
		$sql = "INSERT INTO creditos_destinos(idcreditos_destinos, descripcion_destinos, destino_credito) VALUES ($id, '$description', $id)";
		my_query($sql);
		echo "<p class='aviso'>EL REGISTRO SE EFECTUO CORRECTAMENTE</p><hr></hr>";
	}
	$mtbl = new cTabla($sqlp);
	$mtbl->addTool(1);
	$mtbl->addTool(2);
	echo $mtbl->Show();
	
?>
</body>
<script  >
	function actualizame(id) {
	url = "../utils/frm8db7028bdcdf054882ab54f644a9d36b.php?t=creditos_destinos&f=idcreditos_destinos=" + id;
			myurl = window.open(url);
			myurl.focus();

	}
	function eliminame(id) {
		var sURL = "../utils/frm9d23d795f8170f495de9a2c3b251a4cd.php?t=creditos_destinos&f=idcreditos_destinos=" + id;
	delme = window.open( sURL, "window", "width=300,height=300,scrollbars=yes,dependent");
	delme.focus();

	}

</script>
</html>
