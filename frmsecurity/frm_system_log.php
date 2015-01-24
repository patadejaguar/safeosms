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
	include_once "../core/core.html.inc.php";
	$xHP		= new cHPage();
	$xHP->setIncludes();

$oficial = elusuario($iduser);
//require_once(TINYAJAX_PATH . "/TinyAjax.php");
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<?php
//$jxc ->drawJavaScript(false, true);
?>
<body>
<hr />

<form name="frmreports" method="post" action="">
	<table border='0' width='100%'>
		<tr>
			<td>Fecha Incial</td>
			<td><?php echo ctrl_date(0); ?></td>
			<td>Fecha Final</td>
			<td><?php echo ctrl_date(1); ?></td>
		</tr>
		<tr>
			<td>Nivel</td>
			<td><select name='cNivel' id="idNivel">
					<option value="developer">Desarrollador</option>
					<option value="common">Comun</option>
					<option value="security">Seguridad</option>
					<option value="todas" selected="true">Todos</option>
				</select></td>
			<td>Texto Buscado</td>
			<td>
				<input type="text" name="cTxtBuscar" />
			</td>
		</tr>
		
		<tr>

			<td>Codigo de Error</td>
			<td colspan="3">
			<?php
			$sqlcs = "SELECT idgeneral_error_codigos, description_error
					    FROM general_error_codigos";
			$ctr = new cSelect("cTipoError", "idTipoError", $sqlcs);
			$ctr->setEsSql();
			$ctr->addEspOption("todas", "Todos");
			$ctr->setOptionSelect("todas");
			$ctr->show(false);
			 ?>
			</td>

		</tr>
		<tr>
			<td>Usuario</td>
			<td colspan="3"><?php
			$sqlSc		= "SELECT id, nombrecompleto FROM usuarios ";
			$xS 		= new cSelect("cUsuario", "idUsuario", $sqlSc);
			$xS			->addEspOption("todas", "Todos");
			$xS			->setOptionSelect("todas");
			$xS			->SetEsSql();
			$xS			->show(false);
			?>
			</td>
		</tr>
		<tr>
			<th>Reporte </th>
			<td><?php
			$sqlCL	= "SELECT * FROM general_reports WHERE aplica='seguridad' ";
			$xC		= new cSelect("ereport", "ireport", $sqlCL);
			$xC->setEsSql();
			$xC->show(false);
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
		<tr>
		<td colspan="3"><input type="button" value="Ejecutar" onclick="openReport();" /></td>
		</tr>
	</table>
</form>
</body>
<script  >
	var	mFrm	= document.frmreports;
	function openReport() {
		vf71 = 0;

		anno0 	= mFrm.elanno0.value;
		mes0 	= mFrm.elmes0.value;
		dia0 	= mFrm.eldia0.value;
		var fi 	= new Date(anno0, mes0, dia0);
		//
		anno1 	= mFrm.elanno1.value;
		mes1 	= mFrm.elmes1.value;
		dia1 	= mFrm.eldia1.value;
		var ff 	= new Date(anno1, mes1, dia1);
		//
		vfor 	= 0;
		vto 	= 0;
		vf1 	= mFrm.cNivel.value;
		vTipo	= mFrm.cTipoError.value;
		vOut 	= mFrm.mostrar_como.value;
		vUser	= mFrm.cUsuario.value;
		vBuscar	= mFrm.cTxtBuscar.value;

		report 		= document.frmreports.ereport.value;

		if (fi > ff) {
			alert("EL INICIO NO PUEDE MAYOR AL FINAL \n ---RANGO INVALIDO!!!!--- \n " + fi + " NO PUEDE SER MAYOR A " + ff);
		} else if (ff < fi) {
			alert("EL INICIO NO PUEDE MAYOR AL FINAL \n ---RANGO INVALIDO!!!!---\n " + ff + " NO PUEDE SER MENOR A " + fi);
		} else {
		fi = anno0 + '-'  + mes0 + '-'  + dia0;
		ff = anno1 + '-'  + mes1 + '-'  + dia1;

			var urlrpt = report + '?on=' + fi + '&off=' + ff + '&for='
						+ vfor + '&to=' + vto + '&out=' + vOut + '&f1=' + vf1 + '&f71=' + vf71
						+ '&codigo=' + vTipo + '&usuario=' + vUser + "&buscar=" + vBuscar;
				prep = window.open(urlrpt, "","width=800,height=600,resizable,fullscreen,scrollbars,menubar");
				prep.focus();
		}
	}
</script>
</html>
