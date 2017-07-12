<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 0.0.01
 * @package
 */
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
$xHP		= new cHPage("TR.Reportes DE CONTABILIDAD", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();


$xHP->init();

$xRPT		= new cPanelDeReportes(iDE_CONTABLE, "contable_general");
$xRPT->setTitle($xHP->getTitle());

$xRPT->setConCajero(false);
$xRPT->setConOperacion(false);
$xRPT->setConRecibos(false);

echo $xRPT->get();

echo $xRPT->getJs(TRUE);
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
exit;
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
<title>Reportes Contables Generales</title>
</head>
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
<?php
jsbasic("frmreports", "1", ".");
?>
<body>
<p class="frmTitle"><script> document.write(document.title ); </script></p>
<hr></hr>
<form name="frmreports" action="frmreportsxcredito.php" method="post">
<p class="aviso">Informe Generales x Rango de fechas</p>
<hr />

	<table border='0'  >
		<tr>
			<td>Fecha Incial</td><td><?php echo ctrl_date(0); ?></td>
			<td>Fecha Final</td><td><?php echo ctrl_date(1); ?></td>
		</tr>
		<tr>
			<td>Del Registro:</td><td><input type="text" name="for_r" value="0"></td>
			<td>Al Registro:</td><td><input type="text" name="to_r" value="10000"></td>
		</tr>
		<tr>
			<td >Reporte a Ejecutar</td>
			<td colspan="3"><?php ctrl_select("general_reports", " name='ereport' ", " WHERE aplica='contable_general'"); ?></td>

		</tr>
		<tr>
			<td>Tipo de Operaci&oacute;n</td>
			<td colspan="3"><?php echo ctrl_select("operaciones_tipos", " name='tipo_operacion' "); ?></td>
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
		</tr><tr>
			<td>Tipo de Cedula</td>
			<td colspan="3"><select name="cedula">
				<option value="default" selected>Por Defecto Todos</option>
				<option value="110501">Automaticos Vigentes</option>
				<option value="111201">Automaticos Vencidos</option>
				<option value="110601">Solidarios Vigentes</option>
				<option value="111203">Solidarios Vencidos</option>
			</select>
			</td>
		</tr>

		<tr>
			<td>Estatus Actual</td>
			<td colspan="3"><?php echo ctrl_select("creditos_estatus", " name='estatuscredito' "); ?></td>
		</tr>
		<tr>
			<td>Tipo de Convenio</td>
			<td colspan="3"><?php echo ctrl_select("creditos_tipoconvenio", " name='tipoconvenio' "); ?></td>
		</tr>

		<tr>
			<td>Credito</td><td><input type="text" name="doc" value="0"></td>
		</tr>
		<tr>
		<td>Caja Local</td>
		<td><?php ctrl_select("socios_cajalocal", "name='cajalocal'"); ?></td>
		</tr>
  </table>
  <input type="button" name="cmdRun" value="VER / IMPRIMIR REPORTE" onClick="open_rpt_x_date(document.frmreports.ereport.value);">
</form>
</body>
<script>
var workform = document.frmreports;

	function open_rpt_x_date(isUrl) {

		anno0 = document.frmreports.elanno0.value;
		mes0 = document.frmreports.elmes0.value;
		dia0 = document.frmreports.eldia0.value;
		var fi = new Date(anno0, mes0, dia0);
		//
		anno1 = document.frmreports.elanno1.value;
		mes1 = document.frmreports.elmes1.value;
		dia1 = document.frmreports.eldia1.value;
		var ff = new Date(anno1, mes1, dia1);
		//
		vfor = document.frmreports.for_r.value;
		vto = document.frmreports.to_r.value;
		vfi = ""; //document.frmreports.frecuenciapagos.value;
		vf2 = ""; //document.frmreports.estatuscredito.value;
		vf3 = document.frmreports.tipo_operacion.value;
		vf21 = document.frmreports.cedula.value;
		vOut = document.frmreports.mostrar_como.value;
		vdoc = document.frmreports.doc.value;
		vCl = document.frmreports.cajalocal.value;
		if (fi > ff) {
			alert("EL INICIO NO PUEDE MAYOR AL FINAL \n ---RANGO INVALIDO!!!!--- \n " + fi + " NO PUEDE SER MAYOR A " + ff);
		} else if (ff < fi) {
			alert("EL INICIO NO PUEDE MAYOR AL FINAL \n ---RANGO INVALIDO!!!!---\n " + ff + " NO PUEDE SER MENOR A " + fi);
		} else {
		fi = anno0 + '-'  + mes0 + '-'  + dia0;
		ff = anno1 + '-'  + mes1 + '-'  + dia1;

			var urlrpt = isUrl + 'on=' + fi + '&off=' + ff + '&for=' + vfor + '&to=' + vto + '&f1=' + vfi + '&f2=' + vf2  + '&f3=' + vf3 + '&out=' + vOut + '&f21=' + vf21 + '&doc=' + vdoc + '&cl=' + vCl;
				prep = window.open(urlrpt, "","resizable,fullscreen,scrollbars,menubar");
				prep.focus();
		}
	}
</script>
</html>