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

$xHP 		= new cHPage("Reportes Individuales de Personas");
$oficial 	= elusuario($iduser);
$xF		= new cFecha(0, EACP_FECHA_DE_CONSTITUCION);
echo $xHP->getHeader();

?>
<body>
<fieldset>
	<legend>Reportes Individuales de Personas</legend>
<form name='frmreports' action='' method='post'>
	<table width='100%' border='0'>
		<tr>
			<td>Clave de Persona</td>
			<td><input type='text' name='idsocio' value='' onchange="envsoc();" size='12' class='mny' />
			<?php echo CTRL_GOSOCIO; ?>
			</td>
			<td colspan='2'><input name='nombresocio' type='text' disabled value='' size="60"></td>
		</tr>

		<tr>
			<td >Mostrar Movimientos Estadisticos:</td>
			<td> <?php echo select_bool("mostrar");?></td>
			<td >Mostrar Cadena Importada:</td>
			<td> <?php echo select_bool("importada");?> </td>
		</tr>
		<tr>
			<td>Fecha Incial</td><td><?php echo $xF->show(true, FECHA_TIPO_OPERATIVA); ?></td>
			<td>Fecha Final</td><td><?php echo ctrl_date(1); ?></td>
		</tr>
		<tr>
			<td >Mostrar Solo Creditos con Saldo</td>
			<td><?php echo select_bool("saldo_activo");?></td>

		</tr>
		<tr>
			<td>Tipo de Operaci&oacute;n</td>
			<td colspan="3"><?php echo ctrl_select("operaciones_tipos", " name='tipo_operacion' "); ?></td>
		</tr>
		<tr>
			<td >Reporte a Ejecutar</td>
			<td><?php ctrl_select("general_reports", "name='ereport'", " WHERE aplica='socios' "); ?></td>
		</tr>
		<tr>
			<th colspan='4'><input type='button' name='btsend' value='Mostrar Informe'onClick='open_rpt_x_date(document.frmreports.ereport.value);'></th>
		</tr>
	</table>

</form>
</fieldset>
</body>
<?php
$xJS	= new jsBasicForm("frmreports");
echo $xJS->get();
?>
<script>

	function open_rpt_x_date(isUrl) {
	//Version Anterior de Socio
			elsoc = document.frmreports.idsocio.value;
			//report =document.frmreports.ereport.value;
			mostrar =document.frmreports.mostrar.value;
			allme  = document.frmreports.importada.value;
			en_saldos  = document.frmreports.saldo_activo.value;
	//
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
		vfor = 0;	//document.frmreports.for_r.value;
		vto = 0;	//document.frmreports.to_r.value;
		vfi = 0;	//document.frmreports.frecuenciapagos.value;
		vf2 = 0;	//document.frmreports.estatuscredito.value;
		vf3 = document.frmreports.tipo_operacion.value;
		vOut = 0;  //document.frmreports.mostrar_como.value;

		if (fi > ff) {
			alert("EL INICIO NO PUEDE MAYOR AL FINAL \n ---????RANGO INVALIDO!!!!--- \n " + fi + " NO PUEDE SER MAYOR A " + ff);
		} else if (ff < fi) {
			alert("EL INICIO NO PUEDE MAYOR AL FINAL \n ---????RANGO INVALIDO!!!!---\n " + ff + " NO PUEDE SER MENOR A " + fi);
		} else {
		fi = anno0 + '-'  + mes0 + '-'  + dia0;
		ff = anno1 + '-'  + mes1 + '-'  + dia1;

			var urlrpt = isUrl + 'on=' + fi + '&off=' + ff + '&for=' + vfor + '&to=' + vto + '&f1=' + vfi + '&f2=' + vf2  + '&f3=' + vf3 + '&out=' + vOut + '&pa=' + elsoc + '&f14=' + mostrar + '&f15=' + allme + '&f16=' + en_saldos  + '&f50=' + elsoc  + '&s=' + elsoc;
				prep = window.open(urlrpt, "","width=800,height=600,resizable,fullscreen,scrollbars,menubar");
				prep.focus();
		}
	}
</script>
</html>