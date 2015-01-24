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
$xHP				= new cHPage();

	
//jsbasic("frmgtoscbza", "1", ".");
$xF		= new jsBasicForm("frmgtoscbza");
//$xF->setIncludeJQuery();
$xF->setConCommon();
$xF->setConSocios();
$xF->setConCreditos();
echo $xHP->getHeader();


?>
<script  >
var jsrFile		= "../clsfunctions.inc.php";
var miform 		= document.frmgtoscbza;
var TasaIVA		= "<?php echo TASA_IVA; ?>";

	function jsGetIVA(){
		var iva = document.getElementById("id_monto_cargado").value * TasaIVA;
		document.getElementById("id_iva_cargado").value		= redondear(iva);
		jsGetTotal();
	}
	function jsGetTotal(){
		document.getElementById("id_total_cargado").value = redondear(document.getElementById("id_monto_cargado").value) + redondear(document.getElementById("id_iva_cargado").value);
	}

</script>

<body>
<fieldset>
<legend>|&nbsp;&nbsp;&nbsp;&nbsp;GASTOS DE COBRANZA Y OTROS CONCEPTOS.- CARGOS POR&nbsp;&nbsp;&nbsp;&nbsp;|</legend>

<form name="frmgtoscbza" action="frmgastosdecobranza.php" method="post" >
<table    >
		<tr>
			<td>Clave de Persona</td>
			<td><input type='text' name='idsocio' value='' onchange="envsoc();"  size='12' class='mny' />
			<?php echo CTRL_GOSOCIO; ?></td>
			<td colspan='2'><input name='nombresocio' type='text' disabled value='' size="40"></td>
		</tr>
	<tr>
	<td>N&uacute;mero de Solicitud</td>
	<td><input type="text" name="idsolicitud" onchange="envsol();" size='12' class='mny' />
	<?php echo CTRL_GOCREDIT; ?></td>
	<td colspan='2'><input disabled name="nombresolicitud" type="text" size="40"></td>
	</tr>
	<tr>
	<td>Parcialidad Tratada</td>
	<td><input type="text" name="idparcialidad" value="1" onfocus="envparc();">
	<?php echo CTRL_GOLETRAS; ?></td>
	</tr>
	<tr>
		<td>Monto a Cargar</td>
		<td><input type="text" name="monto_cargado" value="0" id="id_monto_cargado" onchange="jsGetIVA();" class='mny' /></td>
	</tr>
	<tr>
		<td>Impuesto al Valor Agregado</td>
		<td><input type="text" name="iva_cargado" id="id_iva_cargado" value="0" onchange="jsGetIVA();" class='mny' /></td>
	</tr>

	<tr>
		<td>Total Cargado</td>
		<td><input type="text" name="total_cargado" id="id_total_cargado" value="0" onchange="" readonly="true" class='mny' /></td>
	</tr>

	<tr>
		<td>Observaciones</td>
		<td colspan="3"><input type="text" name="observaciones" value="" size="50" /></td>
	</tr>

	<tr>
		<th colspan="4"><input type="button" name="sendeme" value="GUARDAR GASTOS DE COBRANZA" onClick="document.frmgtoscbza.submit();"></th>
	</tr>
</table>
<input type="hidden" name="basecalculo" value="0" onchange=""></td>
<input type="hidden" name="tasa_calculo" value="0" onchange="">


</form>
</fieldset>
<?php
echo $xF->get();

//.- Agrega el Monto de Gastos de Cobranza
$idsolicitud 	= $_POST["idsolicitud"];
$montoop 		= $_POST["monto_cargado"];
$idsocio	 	= $_POST["idsocio"];


	if (( !isset($idsolicitud) ) OR ( !isset($idsocio) ) or $montoop<=0){
		echo("<p class='aviso'>AGREGE UN NUMERO DE SOLICITUD. \n
		 si sabe a Que Parcialidad afectara, indique el Numero
		</p>");

	} else {
		$ulper 				= $_POST["idparcialidad"];
		$idparcialidad 		= $_POST["idparcialidad"];
		$iva		 		= $_POST["iva_cargado"];
		//$idsocio 			= mifila($sqlvs, "numero_socio");
		$observaciones 		= $_POST["observaciones"];
		$fecha				= fechasys();
		$cRec				= new cReciboDeOperacion(97);
		$recibo				= $cRec->setNuevoRecibo($idsocio, $idsolicitud, $fecha, $idparcialidad,
							 						97, $observaciones);
		$cRec->setDefaultEstatusOperacion(40);
		$cRec->setNuevoMvto($fecha, $montoop, 601, $idparcialidad, $observaciones );
		$cRec->setNuevoMvto($fecha, $iva, 1202, $idparcialidad, $observaciones );

		$mSoc = new cFicha(iDE_SOCIO, $idsocio);
		$mSoc->setTableWidth();
		$mSoc->show();

		$cRec->setFinalizarRecibo(true);

		$cRec->getFicha();

		echo "<p class='aviso'>EL MONTO DEL CARGO FUE DE: $ " . getFMoney($montoop) . ", SE PODRA DISMUIR DESDE EL MODULO DE DESCUENTOS</p>";
		echo $msg_rec_end;
	}
?>
</body>
<script  >
	function printrec() {
		var elUrl= "../rpt_formatos/frmrecibogeneral.php?recibo=<?php echo $recibo; ?>";
		rptrecibo = window.open( elUrl, "window");
		rptrecibo.focus();
	}
</script>
</html>