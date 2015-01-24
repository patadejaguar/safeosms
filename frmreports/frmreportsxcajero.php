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
	$iduser 	= $_SESSION["log_id"];
//=====================================================================================================

	$xHP		= new cHPage("TR.Reportes por Cajero");
	
	
	echo $xHP->init();
	$xJs		= new jsBasicForm("frmreports");
?>
<fieldset>
<legend>Reportes por Cajeros</legend>
<form name="frmreports" action="frmreportsxcredito.php" method="post">


	<table>
		<tr>
			<td>Fecha Incial</td><td><?php echo ctrl_date(0); ?></td>
			<td>Fecha Final</td><td><?php echo ctrl_date(1); ?></td>
		</tr>
		<tr>
			<td>Del Registro:</td><td><input type="text" name="for_r" value="0"></td>
			<td>Al Registro:</td><td><input type="text" name="to_r" value="100"></td>
		</tr>
		<tr>
			<td >Reporte a Ejecutar</td>
			<td colspan="3"><?php ctrl_select("general_reports", " name='ereport' ", " WHERE aplica='caja_tesoreria' ORDER BY order_index"); ?></td>
		</tr>
		<tr>
			<td>Cajero</td>
			<td colspan="3">
			<?php
			$sqlSc		= "SELECT id, nombre_completo FROM cajeros";
			$xS 		= new cSelect("cajero", "", $sqlSc);
			$xS->addEspOption(SYS_TODAS);
			//$xS->setOptionSelect(SYS_TODAS);
			$xS->SetEsSql();
			$xS->show(false);
			?>
			</td>
		</tr>
		<!-- empresa -->
		<tr>
			<td>Empresa</td>
			<td colspan="3"><?php 	
						$cTC = new cSelect("empresa", "", "socios_aeconomica_dependencias" );
						$cTC->addEspOption(SYS_TODAS);
						$cTC->setOptionSelect(SYS_TODAS);
						$cTC->show(false);
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
  <input type="button" name="cmdRun" value="VER / IMPRIMIR REPORTE" onClick="open_rpt_x_date(document.frmreports.ereport.value);">
</form>
</fieldset>
</body>
<?php
echo $xJs->get(); 
?>
<script  >
var workform = document.frmreports;
function open_rpt_x_date(isUrl) {

	anno0 	= document.frmreports.elanno0.value;
	mes0 	= document.frmreports.elmes0.value;
	dia0 	= document.frmreports.eldia0.value;
	var fi 	= new Date(anno0, mes0, dia0);
	//
	anno1 	= document.frmreports.elanno1.value;
	mes1 	= document.frmreports.elmes1.value;
	dia1 	= document.frmreports.eldia1.value;
	var ff 	= new Date(anno1, mes1, dia1);
	//
	vfor 	= document.frmreports.for_r.value;
	vto 	= document.frmreports.to_r.value;
	vfi 	= 0;
	vf2 	= 0;
	vf3 	= workform.cajero.value;
	vOut 	= document.frmreports.mostrar_como.value;
	empresa	= workform.empresa.value;
	

	if (fi > ff) {
		alert("EL INICIO NO PUEDE MAYOR AL FINAL \n ---RANGO INVALIDO!!!!--- \n " + fi + " NO PUEDE SER MAYOR A " + ff);
	} else if (ff < fi) {
		alert("EL INICIO NO PUEDE MAYOR AL FINAL \n ---RANGO INVALIDO!!!!---\n " + ff + " NO PUEDE SER MENOR A " + fi);
	} else {
	fi = anno0 + '-'  + mes0 + '-'  + dia0;
	ff = anno1 + '-'  + mes1 + '-'  + dia1;

		var urlrpt 	= isUrl + 'on=' + fi + '&off=' + ff + '&for=' + vfor + '&to=' + vto + '&f1=' + vfi + '&f2=' + vf2  + '&f3=' + vf3 + '&out=' + vOut + "&dependencia=" + empresa;
			prep 	= window.open(urlrpt, "","width=800,height=600,resizable,fullscreen,scrollbars,menubar");
			prep.focus();
	}
}
</script>
</html>