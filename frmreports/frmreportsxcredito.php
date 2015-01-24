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
$xHP					= new cHPage("TR.Reportes de Credito");
$oficial 				= elusuario($iduser);

$xHP->init();

?>
<fieldset>
<legend>Reportes Generales por Solicitud de Credito</legend>
<form name="frmreports" action="frmreportsxcredito.php" method="post">
	<table >
		<tr>
			<td>Clave de Persona</td>
			<td><input type='text' name='idsocio' value='' onchange="envsoc();" size='12' class='mny' />
			<?php echo CTRL_GOSOCIO; ?>
			</td>
			<td>Nombre Completo</td>
			<td><input disabled name='nombresocio' type='text' size="40"></td>
		</tr>
		<tr>
			<td>Numero de Solicitud</td>
			<td><input type='text' name='idsolicitud' value='' onchange="envsol();" size='12' class='mny' />
			<img style="width: 16px; height: 16px;" alt="" src="../images/common/search.png" align='middle' onclick="goCredit_();"/></td>
			<td>Descripcion Corta</td>
			<td><input disabled name='nombresolicitud' type='text' value='' size="40"></td>
		</tr>
		<tr>
			<td>Fecha Incial</td>
			<td><?php echo ctrl_date(0); ?></td>
			<td>Fecha Final</td>
			<td><?php echo ctrl_date(1); ?></td>
		</tr>
		
		<tr>
			<td>Reporte a Ejecutar</td>
			<td colspan="2"><?php ctrl_select("general_reports", "name='ereport'", " WHERE aplica='creditos'"); ?></td>
		</tr>
		<tr>
		<td >Mostrar Movimiento Especifico</td>
		<td><?php echo select_bool("mostrar");?></td>
		<td >Tipo de Movimiento</td>
		<td><?php echo ctrl_select("operaciones_tipos", " name='tipo_operacion' "); ?></td>
		</tr>

	</table>
	<input type='button' name='btsend' value='EJECUTAR / VER REPORTE'onClick='execreport();' />
</form>
</fieldset>
</body>
<?php
$xJs	= new jsBasicForm("frmreports");
echo $xJs->get();

?>
<script>
var workform = document.frmreports;
	function execreport() {
	idsol 			= document.frmreports.idsolicitud.value;
	mostrar_uno 	= document.frmreports.mostrar.value;
	tipo_mostrar 	= document.frmreports.tipo_operacion.value;
	elsoc			= document.frmreports.idsocio.value;
	//=====================================================================
		anno0 = document.frmreports.elanno0.value;
		mes0 = document.frmreports.elmes0.value;
		dia0 = document.frmreports.eldia0.value;
		var fi = new Date(anno0, mes0, dia0);
		//
		anno1 	= document.frmreports.elanno1.value;
		mes1 	= document.frmreports.elmes1.value;
		dia1 	= document.frmreports.eldia1.value;
		var ff = new Date(anno1, mes1, dia1);
		//

		if (fi > ff) {
			alert("EL INICIO NO PUEDE MAYOR AL FINAL \n ---????RANGO INVALIDO!!!!--- \n " + fi + " NO PUEDE SER MAYOR A " + ff);
		} else if (ff < fi) {
			alert("EL INICIO NO PUEDE MAYOR AL FINAL \n ---????RANGO INVALIDO!!!!---\n " + ff + " NO PUEDE SER MENOR A " + fi);
		} else {

		fi = anno0 + '-'  + mes0 + '-'  + dia0;
		ff = anno1 + '-'  + mes1 + '-'  + dia1;

	//=====================================================================
			if (idsol == 0) {
				alert ("NUMERO DE CREDITO INVALIDO");
			} else if ( isNaN(idsol)) {
				alert ("NUMERO DE CREDITO VACIO");
			} else {
				var urlrpt = workform.ereport.value + '?pb=' + idsol + '&f18=' + mostrar_uno + '&f19=' + tipo_mostrar  + '&f50=' + elsoc + '&on=' + fi + '&off=' + ff;
	//				alert(urlrpt);
					prep = window.open(urlrpt, "","resizable,fullscreen,scrollbars,menubar");
					prep.focus();

			} // end if hija
		}
	}
</script>
</html>
