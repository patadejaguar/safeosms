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
$xHP		= new cHPage("TR.Ingresos varios");
$xCaja		= new cCaja();
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();

if( $xCaja->getEstatus() == TESORERIA_CAJA_CERRADA ){	header ("location:../404.php?i=200"); }

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);


$xHP->init();
$xFRM		= new cHForm("frmingresos", "frmingresosvarios.php");
$msg		= "";

//$xFRM->addJsBasico();
$xFRM->addCreditBasico();
$xFRM->addSubmit();

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
exit;
?>

<fieldset>
	<legend>Registro de Ingresos  Varios</legend>
<form name="frmdeudoresdiversos" action="frmingresosvarios.php" method="post">
	<table>
		<?php	if(PERMITIR_EXTEMPORANEO == true){ echo CTRL_FECHA_EXTEMPORANEA;	}
		
		
		?>
		<tr>
			<td>Clave de Persona</td>
			<td><input type='text' name='idsocio' value='1' onchange="envsoc();" class='mny' size='12' />
			<?php echo CTRL_GOSOCIO; ?>
			</td>
			<td colspan='2'><input name='nombresocio' type='text' size="40"></td>
		</tr>
		<tr>
			<td>Numero de Solicitud</td><td><input type="text"
							name="idsolicitud" onchange="envsol(); envparc(); montoparc();"
							value = '0' class='mny' size='12' />
			<?php echo CTRL_GOCREDIT; ?></td>
			<td colspan='2'><input disabled name="nombresolicitud" type="text" size="40"></td>	
		</tr>
		<tr>
			<td>Fecha del Prestamo Personal</td>
			<td><?php echo ctrl_date(1); ?></td>
		</tr>
		<tr>
		<td>Tipo de Devolucion</td>
		<td colspan="3"><?php ctrl_select("operaciones_tipos", " name='tiporecibo'", " WHERE recibo_que_afecta=99"); ?></td>
		</tr>
		<tr>
			<td>Monto Original</td>
			<td><input type='text' name='montooriginal' value = '0' class='mny' size='12' /></td>
		</tr>
		<tr>
			<td>Monto que Abona</td>
			<td><input type='text' name='montoabonado' onchange="chkmonto(montoabonado.value);" value = '0' class='mny' size='12' /></td>
		</tr>
	  <tr>
		<td>Recibo Fiscal</td>
		<td><input type='text' name='foliofiscal' value='-'></td>
	  </tr>
		<tr>
		<td>Tipo de Pago</td>
		<td><?php echo ctrl_tipo_pago(); ?></td>
	</tr>
		
	<tr>
			<td>Observaciones</td>
			<td colspan="3"><input name='observaciones' type='text' value='' size="50"></td>
	</tr>
	<tr>
		<th colspan='4'><input type="button" name="sendme" value="ENVIAR / GUARDAR DATOS" onClick="frmdeudoresdiversos.submit();"></th>
	</tr>
	</table>
	<input type='hidden' name='cheque' value=''>
</form>
<script  >
var miform = document.frmdeudoresdiversos;
function dev_name() {
	ids = document.frmdeudoresdiversos.idsocio.value;
		if (ids!=1) {
			document.frmdeudoresdiversos.nombredeudor.value = document.frmdeudoresdiversos.nombresocio.value;
		}
}
</script>

<?php
jsbasic("frmdeudoresdiversos","1",".");

	$arrValores	= array (
				"montoabonado" => MQL_FLOAT,
				"idsocio" => MQL_INT,
				"observaciones" => MQL_STRING,
				"nombresocio" => MQL_STRING,
				"elanno98" => MQL_INT,
				"elmes98" => MQL_INT,
				"eldia98" => MQL_INT,
				"cheque" => MQL_STRING,
				"ctipo_pago" => MQL_STRING,
				"foliofiscal" => MQL_STRING,
				"montooriginal" => MQL_FLOAT
	);

	
	$monto 			= (isset($_POST["montoabonado"]) ) ? $_POST["montoabonado"] : 0;
	
	
	if ($monto <= 0) {
			
	} else {
		//echo "<code>";
		//var_dump($_POST);
		//echo "</code><hr />";
		$VR				= getVariablesSanas($_POST, $arrValores);
		//echo "<code>";
		//var_dump($VR);
		//echo "</code><hr />"; exit;
		
		$tipo_de_operacion	= $VR["tiporecibo"];
		$idsocio 				= $VR["idsocio"];
		$observaciones 		= $VR["observaciones"];
		$cadena 				= $VR["nombresocio"];
		$fecha 					= $VR["elanno98"] . "-" . $VR["elmes98"] . "-" . $VR["eldia98"];
		$cheque 				= $VR["cheque"];
		$comopago 				= $VR["ctipo_pago"];
		$foliofiscal 			= $VR["foliofiscal"];
		
		//--------------------------------------- DATOS DEL RECIBO -----------------------------------------------
		$montooperacion  		= $monto; 				// AFECTACION DEL MONTO, SEGUN RECIBO.
		$sdoin 					= $VR["montooriginal"];		// SALDO INICIAL DEL RECIBO.
		$sdofin 				= $sdoin - $monto;				// SALDO FINAL DEL RECIBO.
	
		$cRec 					= new cReciboDeOperacion(RECIBOS_TIPO_ESTADISTICO, false);
		//$cRec->setGenerarBancos();
		$cRec->setGenerarPoliza();
		$cRec->setGenerarTesoreria();
	
		$idrecibo			= $cRec->setNuevoRecibo($idsocio, DEFAULT_CREDITO, $fecha, 1, 99, $observaciones, $cheque, $comopago, $foliofiscal);
	
		$cRec->setNuevoMvto($fecha, $monto, $tipo_de_operacion, 1, $observaciones, 1, TM_ABONO, $idsocio);
	
		$cRec->addMvtoContableByTipoDePago($monto, TM_CARGO);
	
		$cRec->setFinalizarRecibo(true);
		
			$cfSocio = new cFicha(iDE_SOCIO, $idsocio);
			$cfSocio->setTableWidth();
			$cfSocio->show();
			echo $cRec->getFicha(true);
			echo $msg_rec_end;
	
			echo $cRec->getJsPrint(true);
	}


	?>
</fieldset>
</body>

</html>