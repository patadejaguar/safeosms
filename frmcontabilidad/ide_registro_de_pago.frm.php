<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package
 * 
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

//require_once("." . TINYAJAX_PATH . "/TinyAjax.php");

$oficial = elusuario($iduser);
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Registro de Pagos del IDE</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<?php
$action			= ( isset( $_GET["a"] ) ) ? $_GET["a"] : false;
$jsb	= new jsBasicForm("frmRegistroDeIde");
$jsb->show();

if ( $action == false ){
//$jxc ->drawJavaScript(false, true);
?>
<body>
<form name="frmRegistroDeIde" method="POST" action="./ide_registro_de_pago.frm.php?a=ok">
<fieldset>
	<legend><script> document.write(document.title); </script></legend>
	<table border='0'   >
		<tbody>
		<tr>
			<td>Fecha de Pago</td>
			<td><?php
				$xF1	= new cFecha(0);
				echo $xF1->show();
			?></td>
		</tr>		
		<tr>
			<td>Banco</td>
			<td><?php
				$gssql= "SELECT idbancos_cuentas, descripcion_cuenta FROM bancos_cuentas";
				$cBc = new cSelect("cBanco", "id-banco", $gssql);
				//$cFJ->addEvent("onchange", "getCambiosFigura");
				$cBc->setEsSql();
				$cBc->show(false);				 
			?></td>
		</tr>
		<tr>
			<td>Numero de Operacion</td>
			<td><input type='text' name='cOperacion' value='0000' id="id-Operacion" class='mny' size='12' maxlength='18' /></td>
		</tr>
		<tr>
			<td>Monto</td>
			<td><input type='text' name='cMonto' value='0.00' id="id-Monto" class='mny' size='8' maxlength='10' /></td>
		</tr>		
		<tr>
			<td>Observaciones</td>
			<td colspan="1"><input name='observaciones' type='text' value='' size="50" id='idObservaciones' /></td>
		</tr>
		<tr>
			<th colspan='2'><input type="submit" value='Guardar Pago' /></th>
		</tr>
		</tbody>
	</table>
</fieldset>
</form>
<?php 
} else {
	$fecha				= $_POST["elanno0"] . "-" . $_POST["elmes0"] . "-" . $_POST["eldia0"];
	$banco				= ( isset($_POST["cBanco"]) ) ? $_POST["cBanco"] : false;
	$numOperacion 		= ( isset($_POST["cOperacion"]) ) ? $_POST["cOperacion"] : false;
	$monto				= ( isset($_POST["cMonto"]) ) ? $_POST["cMonto"] : false;
	$observaciones		= ( isset($_POST["observaciones"]) ) ? $_POST["observaciones"] : "";
	
	if ( ( $monto != false ) AND ( $banco != false ) ){
		$xRec		= new cReciboDeOperacion(201, true);
		$xRec->setGenerarBancos(false);
		$xRec->setGenerarPoliza();
		$xRec->setForceUpdateSaldos();
		$xRec->setCuentaBancaria($banco);
		$idrecibo	= $xRec->setNuevoRecibo(DEFAULT_SOCIO, 1,$fecha, 1, 201, $observaciones, "NA", "efectivo", "NA", DEFAULT_GRUPO, $banco);
		$xRec->setNumeroDeRecibo($idrecibo);
		$xRec->setNuevoMvto($fecha, $monto, 9301, 1, $observaciones, 1, TM_ABONO, DEFAULT_SOCIO);
		$xRec->addMvtoContableByTipoDePago($monto, TM_CARGO);
		$xRec->setFinalizarRecibo(true);
		//Agregar la Operacion Bancaria
		$xB		= new cCuentaBancaria($banco);
		$xB->setNewRetiro($numOperacion, $idrecibo, "PAGO DEL IDE",  $monto, $fecha);
		//
		echo $xRec->getFicha();
		//
		echo "<input type='button' onclick='jsPrintIDE()' value='Imprimir Recibo' />";
	}
}
?>
</body>
<script  >
	function jsPrintIDE() {
		var elUrl			= "../rpt_formatos/recibo.rpt.php?recibo=<?php echo $idrecibo; ?>";
		jsGenericWindow(elUrl);
	}
</script>
</html>
