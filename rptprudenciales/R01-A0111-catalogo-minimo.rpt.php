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
include_once "../core/entidad.datos.php";
include_once "../core/core.deprecated.inc.php";
include_once "../core/core.fechas.inc.php";
include_once "../libs/sql.inc.php";
include_once "../core/core.contable.inc.php";
include_once "../core/core.config.inc.php";

$oficial 			= elusuario($iduser);

$cuenta_inicial 	= $_GET["for"];
$cuenta_final		= $_GET["to"];
$cuenta_inicial 	= getCuentaCompleta($cuenta_inicial);
$cuenta_final		= getCuentaCompleta($cuenta_final);

$ejercicio			= $_GET["e"];
$periodo			= $_GET["p"];

$fecha_inicial		= getFechaUS($fecha_inicial);
$fecha_final		= getFechaUS($fecha_final);
//---------------- Valuar Tipo de Cuenta ----------------
$tipo_cuentas		= $_GET["f1"];
$incluir_cuentas	= $_GET["f2"];
/**
 * Obtiene Parametros a traves de un GET
 * este parametro determinara como filtrado por un registro en particular
 */
$iswhere = "";
switch ($tipo_cuentas){
	case "todas":
		$W_tc = "";
		break;
	case "cuadre":
		$W_tc = " AND	`contable_catalogo`.`numero`>=" . NINE_EXO;
		break;
	case "algunas":
		$W_tc = " AND	`contable_catalogo`.`numero`>=$cuenta_inicial
	AND `contable_catalogo`.`numero`<=$cuenta_final ";
		break;
	default:
		$W_tc = " AND	`contable_catalogo`.`tipo`='$tipo_cuentas' ";
		break;
}
//---------------- valuar Cuentas ----------------



$sqlCta = "SELECT
	`contable_catalogo`.`numero`,
	`contable_catalogo`.`nombre`,
	`contable_catalogotipos`.`naturaleza`,
	`contable_catalogotipos`.`naturaleza_del_sector`,
	`contable_catalogotipos`.`operador_del_sector`,
	/* -  TEMPORAL  - */
	`contable_catalogotipos`.`naturaleza_real`,
	`contable_catalogo`.`tipo`
FROM
	`contable_catalogo` `contable_catalogo`
		INNER JOIN `contable_catalogotipos`
		`contable_catalogotipos`
		ON `contable_catalogo`.`tipo` =
		`contable_catalogotipos`.`idcontable_catalogotipos`
WHERE
	`contable_catalogo`.`afectable` = 1
	$W_tc
	$W_c
ORDER BY
	`contable_catalogo`.`numero`
";
//echo $sqlCta;
Header('Content-type: text/plain');
echo "";
$rs = mysql_query($sqlCta);
while($rw = mysql_fetch_array($rs)){
	$exoCuenta = "";
	$cta			= $rw["numero"];
	$nombre			= $rw["nombre"];
	//$naturaleza	= $rw["naturaleza"] * $rw["naturaleza_del_sector"];
	$naturaleza		= $rw["naturaleza_real"];
	//$naturaleza_segun_reporte
	$datosI			= getDatosInicialSFecha($cta, $naturaleza, $fecha_inicial );
	$sdoinicial		= $datosI["saldo"];
	$movimientos	= $datosI["movimientos"];
	$SCargos		= 0;		//Suma de Cargos
	$SAbonos		= 0;		//SUma de Abonos
	$SFinal			= 0;
	$ctafmt = getCuentaFormateada($cta);

	/**
	 * Obtiene el Periodo a Trabajar
	 *
	 */
	$antPer		= $periodo -1;
		if($antPer<=0){
			$antPer	= "saldo_inicial";
		} else {
			$antPer = "imp" . $antPer;
		}
	$curPer			= "imp" . $periodo;

	$ths 			= "";

	/**
	 * Datos especiales
	 */
	$mvtos = array();
	$mvtos["inicial"]			= 0;
	$mvtos["cargos_actual"]		= 0;
	$mvtos["abonos_actual"]		= 0;
	$mvtos["cargos_anterior"]	= 0;
	$mvtos["abonos_anterior"]	= 0;
	$mvtos["final"]				= 0;

	$sqlTam = "
				SELECT
					tipo,
					$antPer as 'inicial',
					$curPer as 'movimiento'
				FROM
					`contable_saldos` `contable_saldos`
				WHERE
					(`contable_saldos`.`cuenta` =$cta) AND
					(`contable_saldos`.`ejercicio` =$ejercicio)
	";
	$tds = "";
	//echo $sqlTam;
	$saldo = $sdoinicial;
	$rsm = mysql_query($sqlTam);
	$td_final 	= "";
	$td_inicial	= "";
	/**
	 * @var $MActuales: se refiere a numero de Movimientos en el periodo
	 */
	$MActuales = 0;
	while ($rm = mysql_fetch_array($rsm)){
		/*	`contable_saldos`.`tipo`,
		`contable_saldos`.`imp1`,
	`contable_saldos`.`saldo_inicial` */
		//$cuenta		= $rm["cuenta"];
		$tipoMvto	= $rm["tipo"];

		//Si el Movimiento es SALDO
		if($tipoMvto == 1) {
			$mvtos["inicial"]	= $rm["inicial"];
			$mvtos["final"]		= $rm["movimiento"];

			/*if ($naturaleza == NC_DEUDORA){
				$td_inicial = "<td class='mny'>" . getFMoney($mvtos["inicial"]) . "</td>
								<td></td>";
				$td_final	= "<td class='mny'>" . getFMoney($mvtos["final"]) . "</td>
								<td></td>";
			} else {
				$td_inicial = "<td></td>
								<td class='mny'>" . getFMoney($mvtos["inicial"]) . "</td>";
				$td_final	= "<td></td>
								<td class='mny'>" . getFMoney($mvtos["final"]) . "</td>";
			} */

		}
		if($tipoMvto == 2) {
			$mvtos["cargos_actual"]		= $rm["movimiento"];
			//$mvtos["cargos_anterior"]	= $rm["inicial"];
		}
		if($tipoMvto == 3) {
			$mvtos["abonos_actual"]	= $rm["movimiento"];
			//$mvtos["abonos_anterior"]	= $rm["inicial"];
		}
	} // END mysql Movimientos

	$saldo		= $mvtos["final"];
$exoCuenta = "" . getCuentaFormateada($cta) . "|$nombre|" . getFMoney($mvtos["cargos_actual"]) . "|" . getFMoney($mvtos["abonos_actual"]) . "\r\n";
/**
 * @see Re refiere a la decision de imprimir o no ciertas cuentas
 * 		<option value="todas" selected>Todas
 * 		<option value="con_movimientos">Con Movimientos</option>
 * 		<option value="saldo_no_cero">Con Saldos Diferentes a Cero</option>
 * 		<option value="saldo_no_cero_con_mvtos">Con Movimientos y Saldo Diferentes a Cero</option>
 * 		<option value="saldo_no_cero_o_mvtos">Con Movimientos o Saldo Diferentes a Cero</option>
 */
switch($incluir_cuentas){
	case "con_movimientos":
		if($MActuales>0) {
			//$exoCuenta = "";
		} else {
			$exoCuenta = "";
		}
		break;
	case "saldo_no_cero":
		if($saldo!=0){

		} else {
			$exoCuenta = "";
		}
		break;
	case "saldo_no_cero_con_mvtos":
		if(($saldo!=0) and ($MActuales>0)){

		} else {
			$exoCuenta = "";
		}
		break;
	case "saldo_no_cero_o_mvtos":
		if(($saldo!=0) or ($MActuales>0)){

		} else {
			$exoCuenta = "";
		}
		break;
	default:
		break;
}
	echo $exoCuenta;
} //END WHILE
// ------------------------ Fin Tabla
?>