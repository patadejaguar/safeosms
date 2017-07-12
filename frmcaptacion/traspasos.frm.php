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
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");
include_once("../core/core.common.inc.php");
include_once("../core/core.html.inc.php");
include_once("../core/core.captacion.inc.php");
require_once("." . TINYAJAX_PATH . "/TinyAjax.php");


$oficial 		= elusuario($iduser);
$jxc 			= new TinyAjax();
$action			= (isset($_GET["a"])) ? $_GET["a"] : false;

$ReciboTrasp	= 0;
$msg			= "";


function getCuentasForPut($socio, $tipoOrigen, $tipoDestino){
	$xT	= new cTipos(0);
	
	$sql = "SELECT
	`captacion_cuentas`.`numero_cuenta`                 AS `cuenta`,
	CONCAT(`captacion_cuentas`.`numero_cuenta`, '|',
	`captacion_subproductos`.`descripcion_subproductos`, '|',
	`captacion_cuentas`.`saldo_cuenta`) AS 'descripcion'
FROM
	`captacion_cuentas` `captacion_cuentas` 
		INNER JOIN `captacion_cuentastipos` `captacion_cuentastipos` 
		ON `captacion_cuentas`.`tipo_cuenta` = `captacion_cuentastipos`.
		`idcaptacion_cuentastipos` 
			INNER JOIN `captacion_subproductos` `captacion_subproductos` 
			ON `captacion_cuentas`.`tipo_subproducto` = `captacion_subproductos`
			.`idcaptacion_subproductos` 
WHERE
	(`captacion_cuentas`.`numero_socio` = $socio )
	AND
	( `captacion_cuentas`.`tipo_cuenta` = _KEY_ )
	_OTHER_
		ORDER BY
			`captacion_cuentas`.`tipo_cuenta`,
			`captacion_cuentas`.`saldo_cuenta` DESC,
			`captacion_cuentas`.`fecha_afectacion`";
			
	$sql1	= str_replace("_KEY_", $tipoOrigen , $sql);
	if ( $tipoOrigen == CAPTACION_TIPO_PLAZO ){
		$sql1	= str_replace("_OTHER_", " AND (`captacion_cuentas`.`saldo_cuenta` > " .  TOLERANCIA_SALDOS . ") AND ( inversion_fecha_vcto =CURDATE() ) " , $sql1);
	} else {
		$sql1	= str_replace("_OTHER_", " AND (`captacion_cuentas`.`saldo_cuenta` > " .  TOLERANCIA_SALDOS . ") " , $sql1);
	}
	$sql2	= str_replace("_KEY_", $tipoDestino, $sql);
	if ( $tipoDestino == CAPTACION_TIPO_PLAZO ){
		$sql2	= str_replace("_OTHER_", "  AND (`captacion_cuentas`.`saldo_cuenta` > " .  TOLERANCIA_SALDOS . ") AND ( inversion_fecha_vcto =CURDATE() )  " , $sql2);
	} else {
		$sql2	= str_replace("_OTHER_", "" , $sql2);
	}
	
	$xSel1 = new cSelect("cCuentaOrigen", "idCuentaOrigen", $sql1);
	$xSel1->setEsSql();
	$xSel1->addEvent("onchange", "jsSetSaldoCuenta");
	$xSel1->setNRows(5);
	//
	$xSel2 = new cSelect("cCuentaDestino", "idCuentaDestino", $sql2);
	$xSel2->setEsSql();
	$xSel2->setNRows(5);

	return "<td colspan='2'>De la Cuenta:<br />
			" . $xSel1->show() . "</td>
			<td colspan='2'>A la Cuenta:<br />
			" . $xSel2->show() . "</td>
";
}
function jsGetSaldoCuenta($cuenta, $socio){
	$xCta	= new cCuentaDeCaptacion($cuenta, $socio);
	$DC		= $xCta->getDatosInArray();
	$montoRet	= $DC["saldo_cuenta"] ; //$xCta->getMaximoRetirable();
		$tab = new TinyAjaxBehavior();
		$tab -> add( TabSetValue::getBehavior("idMonto", $montoRet ) );
		$tab -> add( TabSetValue::getBehavior("idLetras", convertirletras( $montoRet ) ) );
		
		return $tab -> getString();
}
function jsaSetEmularTraspaso($socio, $cuentaOrigen, $CuentaDestino, $monto){
	
}
$jxc ->exportFunction('getCuentasForPut', array('idsocio', 'idTipoOrigen', 'idTipoDestino'), "#trCuentas");	
$jxc ->exportFunction('jsGetSaldoCuenta', array('idCuentaOrigen', 'idsocio'));	
$jxc ->process();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>TRASPASO ENTRE CUENTAS DE CAPTACION</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<?php
//$jsb	= new jsBasicForm("frmtraspasoinversion", iDE_CAPTACION);
//$jsb->show();
jsbasic("frmtraspasos", "", ".");
$jxc ->drawJavaScript(false, true); 

//Formato de salida: cuenta + monto
//Mostrar solo Cuentas de Inversion sin vencimiento

?>
<body>
<?php
if ( !isset($action) OR ($action == false) ){
?>
<form name="frmtraspasos" method="POST" action="./traspasos.frm.php?a=i">
<fieldset>
	<legend>TRASPASO ENTRE CUENTAS DE CAPTACION</legend>
	<table border='0' width='100%'  >
		<tbody>
		<tr>
			<td>Clave de Persona</td>
			<td><input type='text' name='idsocio' value='' onchange="envsoc(10,0)" id="idsocio" class="mny" size='12' maxlength='20' /></td>
			<td>Nombre Completo</td>
			<td><input disabled name='nombresocio' type='text' value='' size="50" id="idnombresocio" /></td>
		
		</tr>
		<tr>
			<td colspan='4'>
		<fieldset>
			<legend>|    OPERACION    |</legend>
		<table>
		<tr>
			<td>Tipo de Cuenta de Origen.</td>
			<td>
			<?php
			$sqlTC	= "SELECT idcaptacion_cuentastipos, descripcion_cuentastipos, tipo_cuenta 
					FROM captacion_cuentastipos
				    WHERE  idcaptacion_cuentastipos!=99"; 
			$xSel1	= new cSelect("cTipoOrigen", "idTipoOrigen", $sqlTC);
			$xSel1->setEsSql();
			$xSel1->addEvent("onchange", "jsGetCuentas");
			$xSel1->addEvent("onblur", "jsGetCuentas");
			echo $xSel1->show();
			?>
			</td>
			<td>Tipo de Cuenta de Destino.</td>
			<td><?php
			$xSel2		= new cSelect("cTipoDestino", "idTipoDestino", $sqlTC);
			$xSel2->setEsSql();
			$xSel2->addEvent("onchange", "jsGetCuentas");
			$xSel2->addEvent("onblur", "jsGetCuentas");
			echo $xSel2->show();
			?></td>
		</tr>
		<tr id="trCuentas">
			<td>De la Cuenta:</td>
			<td></td>
			
			<td>A la Cuenta:</td>
			<td></td>
		</tr>
	</table>
		</fieldset>
		
		</td>
		</tr>
		<tr>
			<td>Monto</td>
			<td><input type='text' name='cMonto' value='0' id="idMonto" class="mny" /></td>
			<th colspan="2"><input type='text' name='cLetras' value='' id="idLetras" disabled size="50" /></th>
		</tr>

		<tr>
			<td>Observaciones</td>
			<td colspan="2"><input type='text' name='cObservaciones' value='' id="idObservaciones" size='50' maxlength='80'/></td>
		</tr>
		<tr>
			<td colspan='4'><input type='submit' value='Enviar Traspaso' /></td>
		</tr>
		</tbody>
	</table>
</fieldset>
</form>
<?php
} elseif ( $action == 'i' ) {
	$xT				= new cTipos();
	
	
	$socio			= $_POST["idsocio"];
	$monto			= $_POST["cMonto"];
	$cuentaOrigen	= $_POST["cCuentaOrigen"];
	$cuentaDestino	= $_POST["cCuentaDestino"];
	$observaciones	= $_POST["cObservaciones"];
	$tipoOrigen		= $_POST["cTipoOrigen"];
	$tipoDestino	= $_POST["cTipoDestino"];
	

	$sucess			= true;
	$xSoc			= new cSocio($socio);
	$xSoc->init();
	
	if( $xT->getEvalNotNull( array($cuentaOrigen, $cuentaDestino) ) == false ){
		$sucess		= false;
		$msg		.= "ERROR\tLa cuenta de Origen($cuentaOrigen) o la cuenta de Destino($cuentaDestino) NO EXISTE\r\n";		
	}
	if ( $cuentaOrigen == $cuentaDestino ){
		$sucess		= false;
		$msg		.= "ERROR\tLa cuenta de Origen($cuentaOrigen) es igual a la cuenta de Destino($cuentaDestino) \r\n";
	}
	
	if ( ($xSoc->existeCuenta($cuentaOrigen) == 0) OR ( $xSoc->existeCuenta($cuentaDestino) == 0 )){
		$sucess		= false;
		$msg		.= "ERROR\tLa cuenta de Origen($cuentaOrigen) o la cuenta de Destino($cuentaDestino) NO son del Misma Persona ($socio)\r\n";
	}
	if ( $sucess == true ) {
		if ($tipoOrigen == CAPTACION_TIPO_PLAZO ){
			$xCOrigen	= new cCuentaInversionPlazoFijo($cuentaOrigen, $socio);
		} else {
			$xCOrigen	= new cCuentaALaVista($cuentaOrigen, $socio);
		}
		$xCOrigen->init();
		//
		if ($tipoDestino == CAPTACION_TIPO_PLAZO ){
			$xCDestino	= new cCuentaInversionPlazoFijo($cuentaDestino, $socio);
		} else {
			$xCDestino	= new cCuentaALaVista($cuentaDestino, $socio);
		}
		$xCDestino->init();
			
		$msg			.= $xCOrigen->setTraspaso($cuentaDestino, $tipoDestino, $observaciones, $monto);
		
		$ReciboTrasp	= $xCOrigen->getReciboDeOperacion();
		
		$msg 			.= $xCOrigen->getMessages();
		$msg 			.= $xCDestino->getMessages();
		//Imprime la Ficha del socio
		echo $xSoc->getFicha();
		//$cCta->init();
		//Imprime la Ficha del Documento
		$xCDestino->init();
		$fd				= $xCDestino->getFicha(true);
		$xCOrigen->init();
		$fo				= $xCOrigen->getFicha(true);
		
		echo "<fieldset><legend>DATOS DEL TRAPASO</legend>
				Cuenta de Origen:<br />
				$fo	
		Cuenta de Destino:<br />
		$fd
		</fieldset>";



		
		$mRec			= new cFicha(iDE_RECIBO, $ReciboTrasp);
		$mRec->setTableWidth();
		$mRec->show();
		// *****************************************************************************
		echo "<input type='button' name='btsend' value='IMPRIMIR/VER RECIBO DE TRASPASO' onClick='jsGetComprobanteDeTraspaso();'>";

	} else {
		$xBtn	= new cHButton("");
		if (MODO_DEBUG == true){
			$msg	.= $xSoc->getMessages("html");
		}			
		echo "<p class='aviso'>$msg<br />" . $xBtn->getRegresar() ."</p>";
	}

}

?>
</body>
<script  >
function jsGetCuentas(){
	getCuentasForPut();
}
function jsSetSaldoCuenta(){
	jsGetSaldoCuenta();
}
function jsGetComprobanteDeTraspaso(){
	var elUrl= "../rpt_formatos/frmrecibogeneral.php?recibo=<?php echo $ReciboTrasp; ?>";
	jsGenericWindow(elUrl);	
}
</script>
</html>
