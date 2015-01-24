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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Agregar Tipo</title>
</head>
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
<body>
<p class="frmTitle"><script> document.write(document.title ); </script></p>
<hr>
<hr>
<form name="frmaddoflujo" action="frmaddoflujo.php" method="post">
	<table   width="95%">
		<tr>
			<td>Identificador</td><td><input type='text' name='idoflujo' value='0'></td>
			<td>Descripci&oacute;n</td><td><input name='noflujo' type='text' value='' size="60"></td>
		</tr>
	</table>
	<input type="submit" value="GUADAR REGISTRO">
</form>
<hr>
<?php
	
	$idtflujo = $_POST["idoflujo"];
	$ntflujo = $_POST["noflujo"];
	if ($idtflujo) {
	$sqltd = "INSERT INTO creditos_origenflujo(idcreditos_origenflujo, descripcion_origenflujo, origen_flujo) VALUES ($idoflujo, '$noflujo', $idtflujo)";
	my_query($sqltd);
	echo "<p class='aviso'>EL REGISTRO SE HA GUARDADO SATISFACTORIAMENTE</p>";
	}
	$mtbl = new cTabla("SELECT idcreditos_origenflujo AS 'tipo', descripcion_origenflujo AS 'descripcion' FROM creditos_origenflujo");
	$mtbl->addTool(1);
	$mtbl->addTool(2);
	echo $mtbl->Show();
?>
</body>
<script  >
	function actualizame(id) {
	url = "../utils/frm8db7028bdcdf054882ab54f644a9d36b.php?t=creditos_origenflujo&f=idcreditos_origenflujo=" + id;
			myurl = window.open(url);
			myurl.focus();

	}
	function eliminame(id) {
	var sURL = "../utils/frm9d23d795f8170f495de9a2c3b251a4cd.php?t=creditos_origenflujo&f=idcreditos_origenflujo=" + id;
	delme = window.open( sURL, "window", "width=300,height=300,scrollbars=yes,dependent");
	delme.focus();
	}

</script>
</html>
