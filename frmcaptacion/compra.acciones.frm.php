<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package
 * 		-
 *		-
 */
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
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");
include_once("../core/core.creditos.inc.php");
include_once("../core/core.captacion.inc.php");
include_once("../core/core.common.inc.php");
include_once("../core/core.html.inc.php");
include_once("../core/core.operaciones.inc.php");
require_once("." . TINYAJAX_PATH . "/TinyAjax.php");

$oficial 	= elusuario($iduser);
$jxc 		= new TinyAjax();
function jsaGetCalculos($socio, $acciones, $tipo_de_pago){
		$xSoc	= new cSocio($socio);

		$coste	= COSTE_POR_ACCION * $acciones;
		$monto	=  ($tipo_de_pago == "efectivo") ? $coste : 0;

		$ide	= ($tipo_de_pago == "efectivo") ? $xSoc->getIDExPagarByPeriodo(false, $monto) : 0;

		$tab 	= new TinyAjaxBehavior();
		$tab -> add( TabSetValue::getBehavior("idCoste", getFMoney($coste) ) );
		$tab -> add( TabSetValue::getBehavior("idide", getFMoney($ide) ) );
		
		//$tab -> add( TabSetValue::getBehavior("idObservaciones", $xSoc->getMessages("txt") ) );
		return $tab -> getString();
}
$jxc ->exportFunction('jsaGetCalculos', array('idsocio', 'idNumeroAcciones', "idtipo_pago") );
$jxc ->process();

$html	= new cHTMLObject();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Compra de Acciones  V 0.09.06</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<body>
<?php
//0 Compra
//1 Venta

$action			= ( isset( $_GET["o"] ) ) ? $_GET["o"] : false;
$socio			= ( isset( $_GET["s"] ) ) ? $_GET["s"] : false;
$cuenta			= ( isset( $_GET["c"] ) ) ? $_GET["c"] : false;
$RDeposito		= "0";
$ReciboIDE		= "0";
$RInversion		= "0";
$fecha_actual	= fechasys();

if ( $socio == false OR $cuenta == false ){
		$txt	= "No Existen Parametros con los cuales se pueda trabajar, rellene bien los datos y reenvielo";
		exit ( $html->setJsDestino("acciones.frm.php?msg=$txt") );	
} else {
	$xC	= new cCuentaInversionPlazoFijo($cuenta, $socio);
	$xC->init();
	//Si la cuenta no tiene 365 dias la cuenta esta bloqueada para operaciones
	$FVcto			= $xC->getFechaDeVencimiento();
	$D				= $xC->getDatosInArray();
	$mPeriodo		= $xC->getNumeroDePeriodo();
	if ( $mPeriodo > 1 AND ($FVcto != $fecha_actual)  ){
		//el periodo no ha vencido
		$txt	= "La cuenta no $cuenta no esta habilitada para Recibir Movimientos, seleccione otra";
		exit ( $html->setJsDestino("acciones.frm.php?s=$socio&c=$cuenta&msg=$txt") );				
	}
}

if ( $action == false){
	//Operar acciones
?>
<fieldset>
<legend>|&nbsp;&nbsp;&nbsp;&nbsp;COMPRA DE ACCIONES&nbsp;&nbsp;&nbsp;&nbsp;|</legend>
<form name="OperacionesEnAcciones" action="compra.acciones.frm.php?o=1&s=<?php echo $socio ?>&c=<?php echo $cuenta; ?>" method="POST">
	<table    >
		<tr>
			<td colspan='4'>
			<?php
			$cSoc	= new cSocio($socio);
			$cSoc->init();
			echo $cSoc->getFicha();
			?>
			</td>
		</tr>
		<tr>
			<td colspan='4'>
			<?php
			$xC	= new cCuentaDeCaptacion($cuenta, $socio);
			$xC->init();
			echo $xC->getFicha(true);
			?>
			</td>
		</tr>
		<tr>
			<td>Numero de Acciones</td>
			<td><input name='cNumeroAcciones' id='idNumeroAcciones' type='text' value='0' class='mny' size='4'
						onchange="jsaGetCalculos()"  onblur='jsaGetCalculos()' maxlength='6' /></td>
		</tr>
		<tr>
			<td>Monto a Pagar</td>
			<td><input type='text' name='cCoste' value='0'  class="mny" disabled id="idCoste" size='8'/></td>
		</tr>
		<tr>
			<td>I.D.E. a Retener(Estimado)</td>
			<td><input type='text' name='cide' value='0'  class="mny" disabled id="idide" size='8'/></td>
		</tr>
		<tr>
			<td>Tipo de Pago</td>
			<td><?php echo ctrl_tipo_pago("ingresos", "ctipo_pago", "idtipo_pago", "", " onchange='jsaGetCalculos();' "); ?></td>
			<td>Recibo Fiscal</td>
			<td><input type='text' name='recibofiscal' value='NA' /></td>
		</tr>
		<tr>
			<td>Numero de Cheque</td>
			<td><input type='text' name='cheque' value='' id="idcheque" /></td>
		</tr>
		<tr>
			<td>Observaciones</td>
			<td colspan="3"><input name='observaciones' type='text' value='' size="50" id='idObservaciones' /></td>
		</tr>
		<tr>
			<th colspan="4"><input type='button' name='' value='ENVIAR / GUARDAR DATOS' onClick="frmSubmit();"></th>
		</tr>
	</table>

	<input type='hidden' id='idsocio' name='csocio' value='<?php echo $socio; ?>' />
</form>

</fieldset>
<?php
} elseif ($action == 1) {
	//Guardar Acciones
	$acciones		= ( isset($_POST["cNumeroAcciones"]) ) ? $_POST["cNumeroAcciones"] : 0;
	$cheque			= ( isset($_POST["cheque"]) ) ? $_POST["cheque"] : "NA";
	$observa		= ( isset($_POST["observaciones"]) ) ? $_POST["observaciones"] : "";
	$reciboFisc		= ( isset($_POST["recibofiscal"]) ) ? $_POST["recibofiscal"] : "NA";
	$tipo_de_pago	= ( isset($_POST["ctipo_pago"]) ) ? $_POST["ctipo_pago"] : "ninguno";
	//( isset($_POST[""]) ) ? $_POST[""] : 0;
	$invertido		= $acciones	* COSTE_POR_ACCION;
	$dias			= 180;
	$tasa			= 0.08;
	$tasa2			= 0.09;
	
	$msg			= "";
	
	if ( ( $acciones > 0 ) AND ($socio != false) AND ( $cuenta != 0 ) ){


			$cSoc	= new cSocio($socio);
			$cSoc->init();

			$xC	= new cCuentaInversionPlazoFijo($cuenta, $socio, $dias, $tasa );
			$xC->init();
			//Si la cuenta no tiene 365 dias la cuenta esta bloqueada para operaciones
			$FVcto			= $xC->getFechaDeVencimiento();
			$D				= $xC->getDatosInArray();
			$mPeriodo		= $xC->getNumeroDePeriodo();
			$ide			= $xC->getMontoIDE($fecha_actual, $invertido);			
			$RDeposito		= $xC->setDeposito($invertido, $cheque,  $tipo_de_pago, $reciboFisc, $observaciones );
			
			$xC->init();
			$saldo			= $xC->getNuevoSaldo();
			//si el IDE es mayor a cero
			if ( $ide > 0 ){
				$ide_observacion	= "Retencion Generada por un Deposito de $invertido, Recibo $RDeposito, saldo de $saldo";
				//Si el Saldo de la Cuenta es Mayor al IDE
				if ( ($saldo > $ide) ){
					$ReciboIDE 	= $xC->setRetenerIDE($fecha_actual, false, $ide, $ide_observacion);
				} else {
				//Si no el IDE es igual al Saldo
					$ide 		= $saldo;
					$ReciboIDE 	= $xC->setRetenerIDE($fecha_actual, false, $ide, $ide_observacion);
				}
				//
				//$xC->init();
				//$saldo			= $xC->getNuevoSaldo();
			}			
			//Algoritmo de inversion parcial de
			$cientos			= floor( $acciones / 100 );
			if ( $cientos >= 1 ){
				$xC->init();
				$saldo			= $xC->getNuevoSaldo();
				///
				$msg				.= "MAS_CIEN\tExisten $cientos CENTENAS DE ACCIONES\r\n";
				//inversiones de 100
				$IDeCien			= (COSTE_POR_ACCION * ( $cientos * 100 ) );
				//prevee que no se invierta mas de el saldo
				$IDeCien			= ( $IDeCien > $saldo ) ? $saldo : $IDeCien;
				$RInversion			= $xC->setReinversion($fecha_actual, true, $tasa2, $dias, true, $IDeCien);
				$msg				.= "MAS_CIEN\tLa Inversion a tasa de $tasa2 es de $IDeCien\r\n";
				//inversiones < 100
				$IMenorDeCien		= $saldo - $IDeCien;
				if ( $IMenorDeCien > 0 ){
					$RInversion2			= $xC->setReinversion($fecha_actual, true, $tasa, $dias, true, $IMenorDeCien);
					$msg					.= "REM_CIEN\tEl remanente a invertir a tasa de $tasa es de $IMenorDeCien\r\n";
				}
				$xC->setUpdateInversion(true);
			} else {
				//
				$RInversion			= $xC->setReinversion($fecha_actual, true, $tasa, $dias);
			}
			
			
			echo $cSoc->getFicha(true);
			//$xC->init();
			echo $xC->getFicha(true);
			if ( MODO_DEBUG == true ){
				echo $xC->getMessages("html");
				echo $msg;
			}
			echo "<input type='button' name='btsend' value='IMPRIMIR RECIBO DE DEPOSITO' onClick='jsPrintDeposito()' /> ";
			echo "<input type='button' name='btsend' value='IMPRIMIR/VER CONSTANCIA DE INVERSION' onClick='jsPrintReporto();' />";

			if ($ReciboIDE != false ){
				echo "<input type='button' name='btsend' value='IMPRIMIR/VER RECIBO DE RETENCION IDE' onClick='jsPrintIDE();'>";
			}			
	}
}
?>
</body>

<?php
$jxc ->drawJavaScript(false, true);
?>
<?php
$jsb	= new jsBasicForm("OperacionesEnAcciones", iDE_CAPTACION);
//$jsb->setIncludeCaptacion(true);
//$jsb->setInputProp("descripcion_de_la_cuenta", "name", "nombrecuenta" );
$jsb->setTypeCaptacion(20);
$jsb->setSubproducto(70);
$jsb->show();

?>
<script   >
/* Imprime el Recibo de Reinversion */
	function jsPrintReporto() {
	<?php
		if ( isset( $RInversion ) ){
			echo "		var elUrl			= \"../rpt_formatos/frmreciboinversion.php?recibo=$RInversion\";
			jsGenericWindow(elUrl);
		";
		}
		if ( isset( $RInversion2 )  ){
			echo "		var elUrl			= \"../rpt_formatos/frmreciboinversion.php?recibo=$RInversion2\";
			jsGenericWindow(elUrl); ";
		}
	?>
	}
//	Imprime el Recibo de Deposito*/
	function jsPrintDeposito() {
		var elUrl		= "../frmextras/frmrecibodepago.php?recibo=<?php echo $RDeposito; ?>";
		jsGenericWindow(elUrl);
	}
	function jsPrintIDE() {
		var elUrl			= "../rpt_formatos/frmreciboretiro.php?recibo=<?php echo $ReciboIDE; ?>";
		jsGenericWindow(elUrl);
}
</script>
</html>
