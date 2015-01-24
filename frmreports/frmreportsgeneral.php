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
<title>Reportes Generales</title>
</head>
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
<?php
jsbasic("frmreports", "1", ".");
?>
<body>
<p class="frmTitle"><script> document.write(document.title ); </script></p>
<hr></hr>
<form name="frmreports" action="frmreportsxcredito.php" method="post">
	<table width="95%"  >
		<tr>
			<td >Listado de Personas x Estatus</td>
			<td><?php ctrl_select("socios_estatus", "name='estatus'"); ?></td>
			<td><input type='button' name='btsend' value='EJECUTAR / VER REPORTE'onClick='rptlistadosocios();' /></td>
		</tr>
		<tr>
			<th colspan="3">Otras Generalidades</th>
		</tr>
		<tr>
			<td>Consultar un recibo</td>
			<td><input type="text" name="idrecibo" value="0"></td>
			<td><input type='button' name='btsend' value='EJECUTAR / VER REPORTE'onClick='consulta_recibo();' />
			<input type='button' name='btsend' value='REIMPRIMIR RECIBO' onClick='reimprime_recibo();' /></td>
		</tr>
	</table>


<p class="aviso">Informe Generales x Rango de fechas</p>
<hr />

	<table border='0'  >
		<tr>
			<td>Fecha Incial</td><td><input name="sifechas" type="checkbox" /><?php echo ctrl_date(0); ?></td>
			<td>Fecha Final</td><td><?php echo ctrl_date(1); ?></td>
		</tr>
		<tr>
			<td>Del Registro:</td><td><input type="text" name="for_r" value="0"></td>
			<td>Al Registro:</td><td><input type="text" name="to_r" value="10000"></td>
		</tr>
		<tr>
			<td>Sucursal</td>
			<td colspan="3"><?php
			$sqlSc		= "SELECT codigo_sucursal, nombre_sucursal FROM general_sucursales";
			$xS 		= new cSelect("cSucursal", "idSucursal", $sqlSc);
			$xS->addEspOption("todas", "Todas");
			$xS->setOptionSelect("todas");
			$xS->SetEsSql();
			$xS->show(false);
			?>
</td>
		</tr>
		<tr>
			<td >Reporte a Ejecutar</td>
			<td colspan="3"><?php ctrl_select("general_reports", " name='ereport' ", " WHERE aplica='general_x_fechas'"); ?></td>
		</tr>
		<tr>
			<td>Frecuencia de Pago</td>
			<td colspan="3"><input name="sifrecuencia" type="checkbox" /><?php echo ctrl_select("creditos_periocidadpagos", " name='frecuenciapagos' "); ?></td>
		</tr>
		<tr>
			<td>Estatus Actual</td>
			<td colspan="3"><input name="siestatus" type="checkbox" /><?php echo ctrl_select("creditos_estatus", " name='estatuscredito' "); ?></td>
		</tr>
		<tr>
			<td>Tipo de Operaci&oacute;n</td>
			<td colspan="3"><?php echo ctrl_select("operaciones_tipos", " name='tipo_operacion' "); ?></td>
		</tr>
		<tr>
			<td>Tipo de Producto</td>
			<td colspan="3"><input name="siconvenio" type="checkbox" /><?php echo ctrl_select("creditos_tipoconvenio", " name='tipo_convenio' "); ?></td>
		</tr>
		<tr>
			<td>Exportar como</td>
			<td colspan="3">
			<select name="mostrar_como">
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
  <input type="button" name="cmdRun" value="VER / IMPRIMIR REPORTE" onClick="open_rpt_x_date(document.frmreports.ereport.value);">
</form>
</body>
<script>
var workform = document.frmreports;
var wfrm = document.frmreports;
	function rptlistadosocios() {
	est = document.frmreports.estatus.value;
			var urlrpt = 'rpt_listado_socios.php?pa3=' + est;
				prep = window.open(urlrpt, "", "width=800,height=600,resizable,fullscreen,scrollbars,menubar");
				prep.focus();
	}
	function rptmvtos() {
		anno0 = document.frmreports.elanno0.value;
		mes0 = document.frmreports.elmes0.value;
		dia0 = document.frmreports.eldia0.value;
		fi = anno0 + '-' + mes0 + '-' + dia0;
		//
		anno1 = document.frmreports.elanno1.value;
		mes1 = document.frmreports.elmes1.value;
		dia1 = document.frmreports.eldia1.value;
		ff = anno1 + '-' + mes1 + '-' + dia1;

			var urlrpt = '../rptoperaciones/rpt_mvtos_x_fechas.php?is0=' + fi + '&is1=' + ff;
				prep = window.open(urlrpt, "", "width=800,height=600,resizable,fullscreen,scrollbars,menubar");
				prep.focus();

	}
	function consulta_recibo() {
		var idrec = workform.idrecibo.value;

		var urlrpt = '../rptoperaciones/rpt_consulta_recibos_individual.php?f10=' + idrec;
		prep = window.open(urlrpt, "", "width=800,height=600,resizable,fullscreen,scrollbars,menubar");
		prep.focus();

	}
	function reimprime_recibo() {
		var idrec = workform.idrecibo.value;

		var urlrpt = '../frmextras/frmrecibodepago.php?recibo=' + idrec;
		prep = window.open(urlrpt, "", "width=800,height=600,resizable,fullscreen,scrollbars,menubar");
		prep.focus();

	}
	function rptmimistraciones() {
		anno0 = document.frmreports.elanno0.value;
		mes0 = document.frmreports.elmes0.value;
		dia0 = document.frmreports.eldia0.value;
		fi = anno0 + '-' + mes0 + '-' + dia0;
		//
		anno1 = document.frmreports.elanno1.value;
		mes1 = document.frmreports.elmes1.value;
		dia1 = document.frmreports.eldia1.value;
		ff = anno1 + '-' + mes1 + '-' + dia1;

			var urlrpt = '../rptcreditos/rpt_ministraciones_x_fechas.php?is0=' + fi + '&is1=' + ff;
				prep = window.open(urlrpt, "",  "width=800,height=600,resizable,fullscreen,scrollbars,menubar");
				prep.focus();

	}

	function open_rpt_x_date(isUrl) {
				//control de opciones
		var vf71 = 0;		//Frecuencia
		var vf70 = 0;		//Estatus
		var vf72 = 0;		//Convenio
		var vf73 = 0;		//Fechas
		if(wfrm.sifrecuencia.checked){
			vf71 = 1;
		}
		if(wfrm.siestatus.checked){
			vf70 = 1;
		}
		if(wfrm.siconvenio.checked){
			vf72 = 1;
		}
		if(wfrm.sifechas.checked){
			vf73 = 1;
		}
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
		vf1 = document.frmreports.frecuenciapagos.value;
		vf2 = document.frmreports.estatuscredito.value;
		vf3 = document.frmreports.tipo_operacion.value;
		vf5 = document.frmreports.tipo_convenio.value;

		vf700 = document.frmreports.cSucursal.value;
		vOut = document.frmreports.mostrar_como.value;

		if (fi > ff) {
			alert("EL INICIO NO PUEDE MAYOR AL FINAL \n ---RANGO INVALIDO!!!!--- \n " + fi + " NO PUEDE SER MAYOR A " + ff);
		} else if (ff < fi) {
			alert("EL INICIO NO PUEDE MAYOR AL FINAL \n ---RANGO INVALIDO!!!!---\n " + ff + " NO PUEDE SER MENOR A " + fi);
		} else {
		fi = anno0 + '-'  + mes0 + '-'  + dia0;
		ff = anno1 + '-'  + mes1 + '-'  + dia1;

			var urlrpt = isUrl + 'on=' + fi + '&off=' + ff + '&for=' + vfor + '&to=' + vto + '&f1=' + vf1 + '&f2=' + vf2  + '&f3=' + vf3 + '&f5=' + vf5 + '&out=' + vOut + '&f70=' + vf70 + '&f71=' + vf71 + '&f72=' + vf72  + '&f73=' + vf73  + '&f700=' + vf700;
				prep = window.open(urlrpt, "","width=800,height=600,resizable,fullscreen,scrollbars,menubar");
				prep.focus();
		}
	}
</script>
</html>