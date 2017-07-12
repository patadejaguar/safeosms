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
    require("../libs/jsrsServer.inc.php");
    include_once("../core/core.deprecated.inc.php");
	include_once("../core/entidad.datos.php");
	include_once("../core/core.contable.inc.php");
	include_once("../core/core.config.inc.php");

jsrsDispatch("JS_chkCuenta, JSchkCuentaU, JSgetNombreCuenta, mnuActionMvto, JSsetConfirmSuperior, JScheckSaldoCuenta, JSAddToolTip, JSsetMvtoEditable, JSRetMvtoSalvado, jrsVerifyExistPoliza");
/*-------------------- SQl Instruccion for query ----------------------------------------- */
function JS_chkCuenta($cuenta){
	$xT			= new cTipos();
	$cuenta		= $xT->cInt($cuenta);
	$cuenta 	= getCuentaCompleta($cuenta);
	//checa si la cuenta existe
	$sql_va 	= "SELECT numero, afectable FROM contable_catalogo WHERE numero=$cuenta LIMIT 0,1";
	$dcuenta 	= getFilas($sql_va);
	$afectable 	= $dcuenta["afectable"];
	$msg = "";

	switch ($afectable){
		case "":
		$msg = "if (onAltaC == false) {
			var siNuevaCta = confirm(\" LA CUENTA $cuenta NO EXISTE, DESEA DARLE DE ALTA?\");
			if (siNuevaCta) {
				CuentaNueva(\"$cuenta\");
				onAltaC = true;
			} else {
				//wFrm.cuenta.value = 0;
				wFrm.cuenta.focus();
				wFrm.cuenta.select();
			}
		}";
		if($cuenta == ZERO_EXO){ $msg	= ""; }
			break;
		case 1:
			break;
		case 0:
		$msg = " alert('LA CUENTA $cuenta NO ES AFECTABLE($afectable) chk1');
				wFrm.cuenta.focus();
				wFrm.cuenta.select(); ";
			break;
	}
	return $msg;
}
function JSchkCuentaU($cuenta){
	$xT		= new cTipos();
	$cuenta	= $xT->cInt($cuenta);	
	$cuenta = getCuentaCompleta($cuenta);
	//checa si la cuenta existe
	$sql_va = "SELECT numero, afectable FROM contable_catalogo WHERE numero=$cuenta LIMIT 0,1";
	$dcuenta = getFilas($sql_va);
	$afectable = $dcuenta["afectable"];
	$msg = "";

	switch ($afectable){
		case "":
		$msg = "
		if (onAltaC == false) {
			var siNuevaCta = confirm(\" LA CUENTA $cuenta NO EXISTE, DESEA DARLE DE ALTA\");
			if (siNuevaCta) {
				CuentaNueva(\"$cuenta\");
				onAltaC = true;
			} else {
				//wFrm.cuenta.value = 0;
				document.getElementById(\"idCuentaU\").focus();
				document.getElementById(\"idCuentaU\").select();
			}
		} ";
			break;
		case 1:
			break;
		case 0:
		$msg = " alert('LA CUENTA $cuenta NO ES AFECTABLE($afectable) chk2');
				document.getElementById(\"idCuentaU\").focus();
				document.getElementById(\"idCuentaU\").select(); ";
			break;
	}
	return $msg;
}
function JSgetNombreCuenta($cuenta){
	$xT		= new cTipos();
	$cuenta	= $xT->cInt($cuenta);	
	$numero = getCuentaCompleta($cuenta);
	$sql_va = "SELECT numero, nombre, afectable FROM contable_catalogo WHERE numero=$cuenta LIMIT 0,1";
	return 	substr(mifila($sql_va, "nombre"), 0, 20);
}
function mnuActionMvto($act){
	$pim = vIMG_PATH;
	return "<table border=\"3\" >
	<tbody id=\"TBLMenu\" onmouseout=\"\" >
	<tr>
		<td onClick=\"ActionEditar('$act');\"><img src=\"$pim/common/edit.png\"  width=\"12\" height=\"12\" />&nbsp;Editar Movimiento</td>
	</tr>
	<tr>
		<td onClick=\"ActionEliminar('$act');\"><img src=\"$pim/common/save.gif\" width=\"12\" height=\"12\" />&nbsp;Eliminar Movimiento</td>
	</tr>
	<tr>
		<td onClick=\"ActionCancelar();\"><img src=\"$pim/common/icon-stop.png\" width=\"12\" height=\"12\" />&nbsp;Cancelar Operacion</td>
	</tr>	
	</tbody>
</table>";
}
function JSsetConfirmSuperior($superior){
	$superior = getCuentaCompleta($superior);
		$sql_CS = "SELECT numero, equivalencia, afectable, nombre FROM contable_catalogo WHERE numero=$superior LIMIT 0,1";
		$dSuperior = getFilas($sql_CS);
		$SNumero = $dSuperior["numero"];
		$SAfectable = $dSuperior["afectable"];

		return $SAfectable;
}
function JScheckSaldoCuenta($cuenta){
	$xT		= new cTipos();
	$cuenta	= $xT->cInt($cuenta);
	
	$cuenta = getCuentaCompleta($cuenta);
		$stpeval =  "
		setEliminarCuenta();
		";
	$cuenta = cuenta_completa($cuenta);
	$sql_CMvto = "SELECT SUM(importe) AS 'neto' FROM contable_movimientos WHERE numerocuenta = $cuenta";
	$sql_CSup = "SELECT COUNT(subcuenta) AS 'ctas' FROM contable_catalogorelacion WHERE cuentasuperior=$cuenta";
	$subcuentas = mifila($sql_CSup, "ctas");
	$saldo = mifila($sql_CMvto, "neto");
	if($saldo>0){
		$stpeval =  "alert(\"     La Cuenta Tiene Movimientos     \\n \" +
			  \"       por un Monto de $saldo      \\n \" +
			  \" Las Cuentas con saldo No se eliminan\");	";
	}

	if($subcuentas>0){
		$stpeval =  "alert(\"    La Cuenta Tiene $subcuentas Subcuentas     \\n \" +
			  \" Las Cuentas con Subcuentas No se eliminan\");";
	}
	return $stpeval;
}
function JSAddToolTip($IndexAndElement){
	$dats			= explode("|", $IndexAndElement);
	$index 			= $dats[0];
	$Element 		= $dats[1];
	$sqlToolTip 		= "SELECT * FROM general_help WHERE idgeneral_help=$index";
	$txtToolTip 	= mifila($sqlToolTip, "general_help_content_short");
	return "
	document.getElementById(\"$Element\").setAttribute(\"title\", \"$txtToolTip\");

	";
}
function JSsetMvtoEditable($idkeymvto){
	//obtiene datos del movimientos
	$dMvto 			= explode("@", $idkeymvto);
	$ejercicio 		= $dMvto[1];
	$periodo 		= $dMvto[2];
	$poliza  		= $dMvto[3];
	$tipopoliza 	= $dMvto[4];
	$mvto 			= $dMvto[5];
	$ikeymvto 		= "$ejercicio@$periodo@$poliza@$tipopoliza@$mvto";
	$sqlMvto = "SELECT
	`contable_movimientos`.*
FROM
	`contable_movimientos` `contable_movimientos`
WHERE
	`contable_movimientos`.`ejercicio` = $ejercicio
	AND `contable_movimientos`.`periodo` = $periodo
	AND `contable_movimientos`.`tipopoliza` = $tipopoliza
	AND `contable_movimientos`.`numeropoliza` = $poliza
	AND `contable_movimientos`.`numeromovimiento` =$mvto";
	$Datos 			= getFilas($sqlMvto);
	$NCta 			= getNombreCuenta($Datos[5]);
	$td = "
	<th><input type=\"button\" id=\"cmd@$ikeymvto\" value=\"$Datos[4]\" class=\"rwMvtoG\" onclick=\"menu_x_id(event);\" /></th>
	<td><input type=\"text\" value=\"$Datos[5]\" id=\"idCuentaU\" size=\"20\" onkeyup=\"charEventUp(event);\" /></td>
	<td id=\"tdNombreCuentaU\"></td>
	<td><input type=\"text\" value=\"$Datos[13]\" id=\"idCargoU\" onkeyup=\"charEventUp(event);\" onblur='check_cargoU();' class='imny' onfocus=\"validar_cuenta_U();\" /></td>
	<td><input type=\"text\" value=\"$Datos[14]\" id=\"idAbonoU\"  onkeyup=\"charEventUp(event);\"  onblur='check_cargoU();' class='imny' onfocus=\"validar_cuenta_U();\" /></td>
	<td><input type=\"text\" value=\"$Datos[7]\" id=\"idReferenciaU\"  onkeyup=\"charEventUp(event);\" /></td>
	<td><input type=\"text\" value=\"$Datos[11]\" id=\"idConceptoU\" onkeyup=\"charEventUp(event);\" onblur=\"goUpdate();\" /> <!-- </td>
	<td> --> <input type=\"hidden\" value=\"$Datos[9]\" id=\"idDiarioU\" />
	<input type=\"hidden\" value=\"i@$ikeymvto\" id=\"idkeymvtoU\" /></td>
	";
	return $td;
}
function JSRetMvtoSalvado($idkeymvto){
	//obtiene datos del movimientos
	$dMvto 			= explode("@", $idkeymvto);
	$ejercicio 		= $dMvto[1];
	$periodo 		= $dMvto[2];
	$poliza 		= $dMvto[3];
	$tipopoliza 	= $dMvto[4];
	$mvto 			= $dMvto[5];
	$nkey 			= "$ejercicio@$periodo@$poliza@$tipopoliza@$mvto";
	$sqlMvto = "SELECT
	`contable_movimientos`.*
FROM
	`contable_movimientos` `contable_movimientos`
WHERE
	`contable_movimientos`.`ejercicio` = $ejercicio
	AND `contable_movimientos`.`periodo` = $periodo
	AND `contable_movimientos`.`tipopoliza` = $tipopoliza
	AND `contable_movimientos`.`numeropoliza` = $poliza
	AND `contable_movimientos`.`numeromovimiento` =$mvto";
	$Datos 			= getFilas($sqlMvto);
	$NCta 			= getNombreCuenta($Datos[5]);
	$concepto 		= substr($Datos[11],0, 20);
	$td = "
	<th><input type=\"button\" id=\"cmd@$nkey\" value=\"$Datos[4]\" class=\"rwMvtoG\" onclick=\"menu_x_id(event);\" /></th>
	<td>$Datos[5]</td>
	<td id=\"tdNombreCuentaU\">$NCta</td>
	<td class=\"imny\">$Datos[13]</td>
	<td class=\"imny\">$Datos[14]</td>
	<td>$Datos[7]</td>
	<td>$concepto
	<!-- </td>
	<td>$Datos[9] --></td>
	";
	return $td;
}
function jrsVerifyExistPoliza($args){
	$hay 			= 0;
	$Dclave 		= explode(STD_LITERAL_DIVISOR, $args, STD_MAX_ARRAY_JS);
	$ejercicio 		= $Dclave[0];
	$periodo		= $Dclave[1];
	$tipopoliza 	= $Dclave[2];
	$numeropoliza 	= $Dclave[3];

	$sqle = "SELECT count(numeropoliza) AS 'ids' FROM contable_polizas
	WHERE ejercicio=$ejercicio
	AND periodo=$periodo
	AND tipopoliza=$tipopoliza
	AND numeropoliza=$numeropoliza";
	$hay = mifila($sqle, "ids");
	return $hay;
}
?>