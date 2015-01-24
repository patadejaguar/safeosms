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
<title>Reportes de Creditos por oficial</title>
</head>
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
<?php
jsbasic("frmreports", "1", ".");
?>
<body>
<fieldset>
	<legend>Reportes por Oficial de Credito</legend>

<form name="frmreports" action="frmreportsxcredito.php" method="post">
<hr />

	<table border='0'  >
		<tr>
			<td>Oficial</td>
			<td colspan="3"><?php
			$sqlSc		= "SELECT id, nombre_completo FROM oficiales";
			$xS 		= new cSelect("cOficial", "idOficiales", $sqlSc);
			$xS			->addEspOption("todas", "Todas");
			$xS			->setOptionSelect("todas");
			$xS			->SetEsSql();
			$xS			->show(false);
			?>
			</td>
		</tr>

		<tr>
			<td>Fecha Incial</td>
			<td><?php echo ctrl_date(0); ?></td>
			<td>Fecha Final</td>
			<td><?php echo ctrl_date(1); ?></td>
		</tr>
		<tr>

			<td colspan="2">
			<fieldset>
			<legend>Reporte a Ejecutar</legend>
			<?php ctrl_select("general_reports", " name='ereport' ", " WHERE aplica='operaciones_por_oficial'"); ?>
			 <br />
			</fieldset>
			</td>


		<td colspan="2">
			<fieldset>
			<legend>Exportar Reporte Como</legend>
			<select name="mostrar_como">
				<option value="default" selected>Por Defecto(xml)</option>
				<option value="csv">Archivo Delimitado por comas (cvs)</option>
				<option value="csv">Archivo Delimitado por Tabulaciones(tvs)</option>
				<option value="txt">Archivo de Texto(txt)</option>
				<option value="page">Pagina Web(www)</option>
				<option value="xls">Excel</option>
			</select>
			 <br />
			</fieldset>

		</td>
		</tr>

		<tr>
			<td colspan="2">
			<fieldset>
			<legend>Datos referentes a Reportes de Creditos</legend>
			Frecuencia de Pago:<br />
			<?php

				$xSF	= new cSelect("frecuenciapagos", "", "creditos_periocidadpagos");
				$xSF->addEspOption("todas", "Todas");
				$xSF->setOptionSelect("todas");
				$xSF->show(false);
			 ?>
			 <br />
			Estatus Actual	<br />
			<?php

			 	$xse	= new cSelect("estatuscredito", "", "creditos_estatus");
			 	$xse->addEspOption("todas", "Todos");
			 	$xse->setOptionSelect("todas");
			 	$xse->show(false);
			 ?><br />
			Tipo de Producto:	<br />
			<?php
				$xTP	= new cSelect("tipo_convenio", "", "creditos_tipoconvenio");
			 	$xTP->addEspOption("todas", "Todos");
			 	$xTP->setOptionSelect("todas");
			 	$xTP->show(false); ?>
			</fieldset>
			</td>

			<td colspan="2">
			<fieldset>
			<legend>Datos Referentes a Reportes de Movimientos</legend>
			Tipo de Operacion:<br />
			<?php
            $xTO = new cSelect("tipo_operacion", "idTipoDeOperacion", "operaciones_tipos");
			$xTO->addEspOption("todas", "Todos");
			$xTO->setOptionSelect("todas");
			$xTO->show(false); ?>
			
			 <br />
			Tipo de Pago de la Operacion:<br />
			<select name="tipo_de_pago">
				<option value="todas" selected>Todas o Cualquiera</option>
				<option value="efectivo">Pago en Efectivo</option>
				<option value="cheque">Pago en Cheque Interno</option>
				<option value="ninguno">Ninguno</option>
                <option value="foraneo">Cheque Foraneo</option>
                <option value="descuento">Pago Descontado en Cheque</option>
			</select>
			 <br />
			</fieldset>
			</td>
		</tr>



  </table>
  <input type="button" name="cmdRun" value="VER / IMPRIMIR REPORTE" onClick="open_rpt_x_date(document.frmreports.ereport.value);">
</form>
</fieldset>
</body>
<script>
var workform = document.frmreports;
var wfrm = document.frmreports;

	function open_rpt_x_date(isUrl) {
				//control de opciones
		var vf71 = 0;		//Frecuencia
		var vf70 = 0;		//Estatus
		var vf72 = 0;		//Convenio
		var vf73 = 0;		//Fechas

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
		vfor 	= 0;
		vto 	= 0;
		vf1 	= document.frmreports.frecuenciapagos.value;
		vf2 	= document.frmreports.estatuscredito.value;
		vf3 	= document.frmreports.tipo_operacion.value;
		vf5 	= document.frmreports.tipo_convenio.value;

		vf700 	= document.frmreports.cOficial.value;
        vf7     = document.frmreports.tipo_de_pago.value;

		vOut 	= document.frmreports.mostrar_como.value;

		if (fi > ff) {
			alert("EL INICIO NO PUEDE MAYOR AL FINAL \n ---RANGO INVALIDO!!!!--- \n " + fi + " NO PUEDE SER MAYOR A " + ff);
		} else if (ff < fi) {
			alert("EL INICIO NO PUEDE MAYOR AL FINAL \n ---RANGO INVALIDO!!!!---\n " + ff + " NO PUEDE SER MENOR A " + fi);
		} else {
		fi = anno0 + '-'  + mes0 + '-'  + dia0;
		ff = anno1 + '-'  + mes1 + '-'  + dia1;
			var urlrpt = isUrl + 'on=' + fi + '&off=' + ff + '&for=' + vfor + '&to='
                        + vto + '&f1=' + vf1 + '&f2=' + vf2  + '&f3=' + vf3 + '&f5='
                        + vf5 + '&out=' + vOut + '&f70=' + vf70 + '&f71=' + vf71 + '&f72='
                        + vf72  + '&f73=' + vf73  + '&f700=' + vf700
                        + '&f7=' + vf7;
				prep = window.open(urlrpt, "","width=800,height=600,resizable,fullscreen,scrollbars,menubar");
				prep.focus();
		}
	}
</script>
</html>