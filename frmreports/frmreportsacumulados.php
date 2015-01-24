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
$xHP				= new cHPage("TR.Reportes acumulados");

$xHP->init();


$xRPT				= new cPanelDeReportes();
$xRPT->addSucursales();

?>
<body>
<fieldset>
	<legend>Informe Generales Acumulados en un Rago de Fechas</legend>
	
<form name='frmreports'>
	<table >
		<tr>
			<td>Fecha Incial</td><td><?php echo ctrl_date(0); ?></td>
			<td>Fecha Final</td><td><?php echo ctrl_date(1); ?></td>
		</tr>
		<tr>
			<td>Tipo de Operaci&oacute;n</td>
			<td colspan="3"><?php
						$sqlOp	= "SELECT
										`operaciones_tipos`.`idoperaciones_tipos`,
										`operaciones_tipos`.`descripcion_operacion`
									FROM
										`operaciones_tipos` `operaciones_tipos`";
						$cOper	= new cSelect("cTipoOperacion", "idTipoOperacion", $sqlOp);
						$cOper	->setEsSql();
						
						$cOper	->addEspOption("todas", "Todas");
						$cOper	->setOptionSelect("todas");
						$cOper	->show(false);
						?></td>
		</tr>
		<tr>
			<td >Reporte a Ejecutar</td>
			<td colspan="3"><?php
							$SqlRpt	= "SELECT * FROM general_reports WHERE aplica='general_acumulados' ";
							$cSRpt	= new cSelect("ereport", "id-report", $SqlRpt );
							$cSRpt->setEsSql();
							$cSRpt->setNoMayus();
							$cSRpt->show(false);
							
							?>
			</td>
		</tr>
		<tr>
			<td>Mostrar Graficos</td>
			<td colspan="3"><?php echo select_bool("mostrar_grafico"); ?>
			</td>
		</tr>
		<tr>
			<td>Sucursal</td><td><?php
								$cSuc = new cSelect("cSucursal", "idSucursal", "general_sucursales");
								$cSuc->addEspOption("todas", "Todas");
								$cSuc->setOptionSelect("todas");
								$cSuc->setNoMayus();
								$cSuc->show(false);
							?></td>
		</tr>
		<tr>
			<td>Estatus del Credito</td>
			<td><?php
			 	$xse	= new cSelect("estatuscredito", "", "creditos_estatus");
			 	$xse->addEspOption("todas", "Todos");
			 	$xse->setOptionSelect("todas");
			 	$xse->show(false);
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
  </table>
  <input type="button" name="cmdRun" value="VER / IMPRIMIR REPORTE" onClick="open_rpt_x_date(workform.ereport.value);">
</form>

</fieldset>
</body>
<script>
var workform = document.frmreports;


	function open_rpt_x_date(isUrl) {

		anno0 	= workform.elanno0.value;
		mes0 	= workform.elmes0.value;
		dia0 	= workform.eldia0.value;
		var fi 	= new Date(anno0, mes0, dia0);
		//
		anno1 	= workform.elanno1.value;
		mes1 	= workform.elmes1.value;
		dia1 	= workform.eldia1.value;
		var ff 	= new Date(anno1, mes1, dia1);
		//
		vfor 	= 0;	//workform.for_r.value;
		vto 	= 0;	//workform.to_r.value;
		vfi 	= 0;	//workform.frecuenciapagos.value;
		vf2 	= workform.estatuscredito.value;
		vf3 	= workform.cTipoOperacion.value;
		vOut 	= workform.mostrar_como.value;
		vOutG 	= workform.mostrar_grafico.value;
		mSuc	= workform.cSucursal.value;
		vf700	= workform.cOficial.value;
		if (fi > ff) {
			alert("EL INICIO NO PUEDE MAYOR AL FINAL \n ---????RANGO INVALIDO!!!!--- \n " + fi + " NO PUEDE SER MAYOR A " + ff);
		} else if (ff < fi) {
			alert("EL INICIO NO PUEDE MAYOR AL FINAL \n ---????RANGO INVALIDO!!!!---\n " + ff + " NO PUEDE SER MENOR A " + fi);
		} else {
		fi = anno0 + '-'  + mes0 + '-'  + dia0;
		ff = anno1 + '-'  + mes1 + '-'  + dia1;

			var urlrpt = isUrl + 'on=' + fi + '&off=' + ff + '&for=' + vfor + '&to=' + vto + '&f1=' + vfi + '&f2=' + vf2  + '&f3=' + vf3 + '&out=' + vOut + '&outg=' + vOutG + "&s=" + mSuc  + '&f700=' + vf700;
			//setLog(urlrpt);
				prep = window.open(urlrpt, "","width=800,height=600,resizable,fullscreen,scrollbars,menubar");
				prep.focus();
		}
	}
</script>
</html>