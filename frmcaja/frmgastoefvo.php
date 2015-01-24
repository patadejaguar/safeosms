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
$xHP				= new cHPage("TR.Gastos en Efectivo");
$xF					= new cFecha();
$xCaja				= new cCaja();
if( $xCaja->getEstatus() == TESORERIA_CAJA_CERRADA ){	header ("location:../404.php?i=200"); }

echo $xHP->getHeader();

$jscallback			= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto 				= parametro("monto", 0, MQL_FLOAT);
$tipo_de_recibo		= parametro("tiporecibo", 0, MQL_INT);

$xJS				= new jsBasicForm("frmgtosefvo");
$lowdate 			= $xF->setSumarDias( DIAS_PAGO_UNICOS);
$ndia 				= $xF->dia();
$nmes 				= $xF->mes();
$nano 				= $xF->anno();


$xFRM		= new cHForm("frm", "./");
$xBtn		= new cHButton();
$xTxt		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();

?>
<script>
	function fechaminima() {
			document.frmgtosefvo.eldia0.value = <?php echo $ndia; ?>;
			document.frmgtosefvo.elmes0.value = <?php echo $nmes; ?>;
			document.frmgtosefvo.elanno0.value = <?php echo $nano; ?>;
    }
	function maxdeudores() {
		if( document.frmgtosefvo.tiporecibo.value == 31) {
			maxdeuda = <?php echo DEUDORES_DIVERSOS_MAXIMO; ?>;
		} else {
			maxdeuda = <?php echo DEUDORES_DIVERSOS_MAXIMO; ?>;
		}
		ladeuda = document.frmgtosefvo.monto.value;
		fechaminima();
		if (ladeuda > maxdeuda) {
			document.frmgtosefvo.monto.value = maxdeuda;
			alert("NO SE LE PUEDE OTORGAR MAS DE $" + maxdeuda + "\n EN EL RUBRO SOLICITADO");
			return 0;
		}
	}
</script>
<body>
<fieldset>
	<legend>Egresos Varios.</legend>
<?php
if($action == SYS_NINGUNO){ 
?>
<form name="frmgtosefvo" action="frmgastoefvo.php?action=2" method="post">

	<table    >
	  <tr>
		<td>Recibo Fiscal</td><td><input type='text' name='foliofiscal' value='-'></td>
	  </tr>
		<tr>
		<td>Tipo de Pago</td>
		<td><?php echo ctrl_tipo_pago("egresos"); ?></td>
		<td>Numero de Cheque</td>
		<td><input type='text' name='cheque' value=''></td>
	</tr>
		<tr>
			<td>Concepto del Pago</td>
			<td><?php ctrl_select("operaciones_recibostipo", "name='tiporecibo'", " WHERE subclasificacion=5"); ?></td>
			<td>Fecha Max. de Devoluci&oacute;n</td>
			<td><?php echo ctrl_date(0);?></td>
			
		</tr>
		<tr>
			<td>Beneficiario</td>
			<td colspan='2'><input name='beneficiario' type='text' value='' size="50" maxlength="80"></td>
		</tr>
		<tr>
			<td>Monto</td>
			<td><input type='text' name='monto' value='0' onchange="maxdeudores();" class="mny"></td>
		</tr>
		<tr>
			<td>Observaciones</td>
			<td colspan="2"><input name='observaciones' type='text' value='' size="50" maxlength="100"></td>
		</tr>
	</table>
	<input type='button' name='' value='ENVIAR / GUARDAR DATOS' onClick="fechaminima(); frmSubmit();">
</form>
<?php
	echo $xJS->get();

	
	} else {
		//Cambio de ROL Detalles > observaciones
		$detalles 		= parametro("observaciones");
		$cheque 		= parametro("cheque", DEFAULT_CHEQUE);
		$comopago 		= parametro("ctipo_pago", DEFAULT_TIPO_PAGO, MQL_RAW);
		$foliofiscal 	= parametro("foliofiscal");
	
		// -------------------------------------- DATOS DEL MOVIMIENTO ------------------------------------------
		$observaciones 	= parametro("beneficiario");
		$fechavenct 	= parametro("elanno0") . "-" . parametro("elmes0") . "-" . parametro("eldia0");
		$fecha			= fechasys();
		$idsocio		= DEFAULT_SOCIO;
		
		
			// Determina el Tipo de Operacion segun tRec
			if ($tipo_de_recibo == 31) {				// Gastos a Comprobar
			
				$tipo_de_operacion = 1010;
				
			} elseif ($tipo_de_recibo == 32) {			// Prestamo Personal
			
				$tipo_de_operacion = 1011;
				
			} else {
			
				$tipo_de_operacion = 99;
				
			}
		$cRec 				= new cReciboDeOperacion($tipo_de_recibo, false);
		//$cRec->setGenerarBancos();
		$cRec->setGenerarPoliza();
		$cRec->setGenerarTesoreria();
	
		$idrecibo			= $cRec->setNuevoRecibo($idsocio, DEFAULT_CREDITO, $fecha, 1, $tipo_de_recibo, $observaciones, $cheque, $comopago, $foliofiscal);
	
		$cRec->setNuevoMvto($fecha, $monto, $tipo_de_operacion, 1, $observaciones, 1, TM_ABONO, $idsocio);
	
		$cRec->addMvtoContableByTipoDePago($monto, TM_CARGO);
	
		$cRec->setFinalizarRecibo(true);
	
		$cfSocio = new cFicha(iDE_SOCIO, $idsocio);
		$cfSocio->setTableWidth();
		$cfSocio->show();
		echo $cRec->getFicha();
		echo $msg_rec_end;
	}

?>
</fieldset>
</body>
<script  >
	function printrec() {
		var elUrl= "frmrecibodeegresos.php?recibo=<?php echo $idrecibo; ?>";
		rptrecibo = window.open( elUrl, "window");
		rptrecibo.focus();
	}
</script>
</html>