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
$xHP				= new cHPage("TR.Reportes de Inversion");

$oficial = elusuario($iduser);

$xHP->init();

//jsbasic("frmreports", "", ".");
$jsB	= new jsBasicForm("frmreports", iDE_CINVERSION);

?>
<body>
<fieldset>
	<legend>Estados de Cuenta de Inversiones a Plazo</legend>

<form name="frmreports" action="frmreportsxcuenta.php" method="post">
	<table >
		<tr>
			<td>Clave de Persona</td>
			<td><input type='text' name='idsocio' value='' onchange="envsoc(20,0);" class='mny' size='10' maxlength='20' /></td>
			<td>Nombre Completo</td>
			<td><input disabled name='nombresocio' type='text' size="60"></td>
		</tr>
		<tr>
			<td>Numero de cuenta de Inversi&oacute;n</td>
			<td><input type='text' name='idcuenta' value='' onchange="envcta(20);" class='mny' size='10' maxlength='20' />
			<?php echo CTRL_GOCUENTAS_I; ?></td>
			<td>Descripcion Corta</td>
			<td><input disabled name='nombrecuenta' type='text' value='' size="60"></td>
		</tr>
		<tr>
			<td>Fecha Inicial</td>
			<td><input name="sifechas" type="checkbox" />
				<?php echo ctrl_date(0); ?></td>
			<td>Fecha Final</td>
			<td><?php echo ctrl_date(1); ?></td>
		</tr>
		<tr>
			<td>Del Registro:</td>
			<td><input type="text" name="for_r" value="0"></td>
			<td>Al Registro:</td>
			<td><input type="text" name="to_r" value="1000"></td>
		</tr>
		<tr>
			<td >Reporte a Ejecutar</td>
			<td colspan="3"><?php ctrl_select("general_reports", " name='ereport' ", " WHERE aplica='inversion_individual'"); ?></td>
		</tr>

		<tr>

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
	<input type='button' name='btsend' value='EJECUTAR / VER REPORTE'onClick='open_rpt_x_date();'>
</form>
</fieldset>
</body>
<?php
echo $jsB->get(); 
?>
<script  >
var wfrm	= document.frmreports;
	function open_rpt_x_date() {
		vf73	= 0;
		//
		isUrl 	= document.frmreports.ereport.value;
		//
		anno0 	= document.frmreports.elanno0.value;
		mes0 	= document.frmreports.elmes0.value;
		dia0 	= document.frmreports.eldia0.value;
		var fi 	= new Date(anno0, mes0, dia0);
		//
		anno1 	= document.frmreports.elanno1.value;
		mes1 	= document.frmreports.elmes1.value;
		dia1	= document.frmreports.eldia1.value;
		var ff 	= new Date(anno1, mes1, dia1);
		
		vfor 	= document.frmreports.for_r.value;
		vto 	= document.frmreports.to_r.value;
		vfi		= 0;	//document.frmreports.frecuenciapagos.value;
		vf2 	= 0; 	//document.frmreports.estatuscredito.value;
		vf3 	= 0;	//document.frmreports.tipo_operacion.value;
		vOut 	= document.frmreports.mostrar_como.value;
		vDoc 	= document.frmreports.idcuenta.value;
		
		if(wfrm.sifechas.checked){
			vf73 = 1;
		}
		
		if (fi > ff) {
			alert("EL INICIO NO PUEDE MAYOR AL FINAL \n ---????RANGO INVALIDO!!!!--- \n " + fi + " NO PUEDE SER MAYOR A " + ff);
		} else if (ff < fi) {
			alert("EL INICIO NO PUEDE MAYOR AL FINAL \n ---????RANGO INVALIDO!!!!---\n " + ff + " NO PUEDE SER MENOR A " + fi);
		} else {

			fi = anno0 + '-'  + mes0 + '-'  + dia0;
			ff = anno1 + '-'  + mes1 + '-'  + dia1;
				var urlrpt = isUrl + 'on=' + fi + '&off=' + ff + '&for=' + vfor + '&to=' + vto + '&f1=' + vfi + '&f2=' + vf2  + '&f3=' + vf3 + '&out=' + vOut + '&docto=' + vDoc + '&f73=' + vf73;
					prep = window.open(urlrpt, "window" + vOut,"width=800,height=600,resizable,fullscreen,scrollbars");
					prep.focus();
		}
	}

</script>
</html>
