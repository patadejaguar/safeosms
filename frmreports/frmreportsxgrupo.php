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
$xHP			= new cHPage();
$xHP->init();


?>
<fieldset>
	<legend>Estados de Cuenta de Grupos Solidarios</legend>
<form name="frmreports" action="frmreportsxgrupo.php" method="post">
	<table >
		<tr>
			<td>Numero de Grupo</td>
			<td><input name="sigrupo" type="checkbox" /><input type='text' name='idgrupo' value='99' onchange="envgpo();"  class='mny' size='4' maxlength='20' /></td>
			<td>Nombre Del Grupo</td>
			<td><input disabled name='nombregrupo' type='text' size="60"></td>
		</tr>

		<tr>
			<td>Reporte a Ejecutar</td><td colspan="2"><?php ctrl_select("general_reports", "name='ereport'", " WHERE aplica='grupos'"); ?></td>
		</tr>
		
		<tr>
			<td>Fecha Incial</td><td><input name="sifechas" type="checkbox" /><?php echo ctrl_date(0); ?></td>
			<td>Fecha Final</td><td><?php echo ctrl_date(1); ?></td>
		</tr>
		
		<tr>
			<td>Frecuencia de Pago</td>
			<td colspan="3"><input name="sifrecuencia" type="checkbox" /><?php echo ctrl_select("creditos_periocidadpagos", " name='frecuenciapagos' "); ?></td>
		</tr>
		<tr>
			<td>Estatus Actual</td>
			<td colspan="3"><input name="siestatus" type="checkbox" /><?php echo ctrl_select("creditos_estatus", " name='estatuscredito' "); ?></td>
		</tr>
		<!-- <tr>
			<td>Tipo de Producto</td>
			<td colspan="3"><input name="siconvenio" type="checkbox" /><?php echo ctrl_select("creditos_tipoconvenio", " name='tipo_convenio' "); ?></td>
		</tr>	-->	
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
	<input type='button' name='btsend' value='EJECUTAR / VER REPORTE'onClick='execreport();'>
</form>
</fieldset>
<script>
var workform = document.frmreports;
var wfrm = document.frmreports;
	function execreport() {
		var vf71 = 0;		//Frecuencia
		var vf70 = 0;		//Estatus
		var vf72 = 0;		//Convenio
		var vf73 = 0;		//Fechas
		var vf74 = 0;		//Grupo
		if(wfrm.sifrecuencia.checked){
			vf71 = 1;
		}
		if(wfrm.siestatus.checked){
			vf70 = 1;
		}		
		//if(wfrm.siconvenio.checked){
		//	vf72 = 1;
		//}
		if(wfrm.sifechas.checked){
			vf73 = 1;
		}
		if(wfrm.sigrupo.checked){
			vf74 = 1;
		}	
		idg = workform.idgrupo.value;
		//
		anno0 = workform.elanno0.value;
		mes0 = workform.elmes0.value;
		dia0 = workform.eldia0.value;
		var fi = new Date(anno0, mes0, dia0);
		//
		anno1 = workform.elanno1.value;
		mes1 = workform.elmes1.value;
		dia1 = workform.eldia1.value;
		var ff = new Date(anno1, mes1, dia1);
		//
		vfor 	= 0;
		vto 	= 0;	
		vf1 	= workform.frecuenciapagos.value;
		vf2 	= workform.estatuscredito.value;
		vf3 	= 0;	//workform.tipo_operacion.value;
		vf5 	= 0;    //workform.tipo_convenio.value;		
		
		if (fi > ff) {
			alert("EL INICIO NO PUEDE MAYOR AL FINAL \n ---????RANGO INVALIDO!!!!--- \n " + fi + " NO PUEDE SER MAYOR A " + ff);
		} else if (ff < fi) {
			alert("EL INICIO NO PUEDE MAYOR AL FINAL \n ---????RANGO INVALIDO!!!!---\n " + ff + " NO PUEDE SER MENOR A " + fi);
		} else {
		fi = anno0 + '-'  + mes0 + '-'  + dia0;
		ff = anno1 + '-'  + mes1 + '-'  + dia1;
			var vOut = document.frmreports.mostrar_como.value;
			
			var urlrpt = workform.ereport.value + '?id=' + idg + '&on=' + fi + '&off=' + ff + '&for=' + vfor + '&to=' + vto + '&f1=' + vf1 + '&f2=' + vf2  + '&f3=' + vf3 + '&f5=' + vf5 + '&out=' + vOut + '&f70=' + vf70 + '&f71=' + vf71 + '&f72=' + vf72  + '&f73=' + vf73 + '&f74=' + vf74;

				prep = window.open(urlrpt, "","resizable,menubar,fullscreen,scrollbars");
				prep.focus();

		} 
	}
</script>
<?php
$xJS	= new jsBasicForm("frmreports");//jsbasic("frmreports", "1", ".");
echo $xJS->get();
$xHP->fin(); 
?>