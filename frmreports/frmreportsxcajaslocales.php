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
include_once "../libs/sql.inc.php";

$oficial = elusuario($iduser);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Reportes x Caja Local</title>
</head>
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
<?php
jsbasic("myform", "1", ".");
?>
<body>
<p class="frmTitle"><script> document.write(document.title ); </script></p>
<form name='myform' action='' method='post'>
	<table width='100%' border='0'>
		<tr>
		<th >Caja Local</th>
		<td><?php
			$xCL		= new cSelect("cajalocal", "", "socios_cajalocal");
			$xCL->addEspOption("todas", "Todas / Cualquiera");
			//$xCL->setOptionSelect("todas");
			$xCL->show(false);

			?></td>
			<th>Reporte </th>
			<td><?php
			$sqlCL	= "SELECT * FROM general_reports WHERE aplica='cajalocal' ";
			$xC		= new cSelect("ereport", "ireport", $sqlCL);
			$xC->setEsSql();
			$xC->show(false);
			?></td>
		</tr>
		<tr>
		<th colspan="2">Filtro de Personas</th>
		<th>Estado en el Sistema</th>
		<td>
			<?php
			$xE		= new cSelect("estatus", "", "socios_estatus");
			$xE->addEspOption("todas", "Todas / Cualquiera");
			$xE->setOptionSelect("todas");
			$xE->show(false);
			?></td>
		</tr>
		<tr>
			<td>Exportar como</td>
			<td colspan="3"><select name="mostrar_como">
				<option value="default" selected>Por Defecto(xml)</option>
				<option value="csv">Archivo Delimitado por comas (cvs)</option>
				<option value="csv">Archivo Delimitado por Tabulaciones(tvs)</option>
				<option value="txt">Archivo de Texto(txt)</option>
				<option value="page">Pagina Web(www)</option>
				<option value="xls">Excel</option>
			</select>
			</td>
		</tr>
	</table>
<input type='button' name='btsend' value='Mostrar Informe'onClick='goform();'>
</form>
</body>
<script>
	function goform() {
		mostrar 	= 'no';
		report 		= document.myform.ereport.value;
		idcl 		= document.myform.cajalocal.value;
		e4 			= document.myform.estatus.value;
		var vf70 = 0;		//Estatus

		var vOut = document.myform.mostrar_como.value;

		url_k = report + '?pa3=' + idcl + '&pa4=' + e4 + '&out=' + vOut + '&f70=' + vf70;
		mywin = window.open(url_k, "","resizable,menubar,fullscreen,scrollbars");
		mywin.focus();
	}
</script>
</html>
