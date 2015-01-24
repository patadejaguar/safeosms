<?php
//=====================================================================================================
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	include_once("../core/core.db.inc.php");
	$theFile			= __FILE__;
	$permiso			= getSIPAKALPermissions($theFile);
	if($permiso === false){	header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//=====================================================================================================
$xHP		= new cHPage("TR.REPORTE DE MOVIMIENTOS DE AUXILIARES", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$ql			= new MQL();


$fecha_inicial			= parametro("on", $xF->getDiaInicial(), MQL_DATE);
$fecha_final			= parametro("off", $xF->getDiaFinal(), MQL_DATE);
$cuenta_inicial			= parametro("for", 0, MQL_INT);
$cuenta_final			= parametro("to", 0, MQL_INT);
$out 					= parametro("out", SYS_DEFAULT, MQL_RAW);

//---------------- Valuar Tipo de Cuenta ----------------
$tipo_cuentas			= parametro("tipo", SYS_TODAS, MQL_RAW);
$nivel_cuentas			= parametro("nivel", SYS_TODAS, MQL_RAW);
$incluir_cuentas		= parametro("estado", SYS_TODAS, MQL_RAW);
$netoCargoRpt			= 0;
$netoAbonoRpt			= 0;

$ByCuentas				= "";

$xRPT					= new cReportes( $xHP->getTitle() );
$xRPT->addContent( $xRPT->getEncabezado($xHP->getTitle(), $fecha_inicial, $fecha_final) );
$InitRecords			= parametro("init", 0, MQL_INT);

$xRPT->setOut($out);
//=================================================================================================================
//$xRPT->setToPagination($InitRecords);

/**
 * Paginacion
 * I.- Parte
 */
$rowLimit				= 900; //4Paginas aprox

//marca el Final de los Registros
$EndRecords				= $InitRecords + $rowLimit;
//=================================================================================================================
/**
 * Obtiene Parametros a traves de un GET
 * este parametro determinara como filtrado por un registro en particular
 */

$ByTipo			= "";
$nivel_cuentas	= setNoMenorQueCero($nivel_cuentas);
$ByNivel		= ($nivel_cuentas > 0) ? " AND	`contable_catalogo`.`ctamayor`='$nivel_cuentas' " : "";
switch ($tipo_cuentas){
	case SYS_TODAS:
		$ByCuentas			= "";
		$ByCuentasSaldos	= "";
		break;
	case "cuadre":
		$ByCuentas 			= " AND	`contable_catalogo`.`numero`=" . CUENTA_DE_CUADRE;
		$ByCuentasSaldos	= " AND (`contable_saldos`.`cuenta` = " . CUENTA_DE_CUADRE . ") ";
		break;
	case "algunas":
		//solo cuentas
		break;
	default:
		$ByTipo 	= " AND	`contable_catalogo`.`tipo`='$tipo_cuentas' ";

		break;
}



ini_set("display_errors", "on");
$sqlCta = "SELECT
			`contable_catalogo`.`numero`,
			`contable_catalogo`.`nombre`,
			`contable_catalogotipos`.`naturaleza`,
			`contable_catalogotipos`.`naturaleza_del_sector`,
			`contable_catalogotipos`.`operador_del_sector`,
			/* -  TEMPORAL  - */
			`contable_catalogotipos`.`naturaleza_real`,
			`contable_catalogo`.`tipo`,
			/* - MOVIMIENTOS - */
			`contable_movimientos`.`fecha`,
			`contable_polizasdiarios`.`nombre_del_diario` AS `tipo`,
			`contable_movimientos`.`numeropoliza`         AS `poliza`,
			`contable_movimientos`.`concepto`,
			`contable_movimientos`.`referencia`,
			`contable_movimientos`.`cargo`,
			`contable_movimientos`.`abono`
			
			FROM
				`contable_catalogo` `contable_catalogo`
				INNER JOIN `contable_movimientos` `contable_movimientos`
				ON `contable_catalogo`.`numero` = `contable_movimientos`.`numerocuenta`
				INNER JOIN `contable_catalogotipos` `contable_catalogotipos`
				ON `contable_catalogo`.`tipo` = `contable_catalogotipos`.
				`idcontable_catalogotipos`
				INNER JOIN `contable_polizasdiarios` `contable_polizasdiarios`
				ON `contable_movimientos`.`tipopoliza` =
				`contable_polizasdiarios`.`idcontable_polizadiarios`
			WHERE
				`contable_catalogo`.`afectable` = 1
				$ByCuentas
				AND (`contable_movimientos`.`fecha`>='$fecha_inicial')
				AND (`contable_movimientos`.`fecha`<='$fecha_final')
			ORDER BY
				`contable_catalogo`.`numero`,
				`contable_movimientos`.`fecha`,
				`contable_movimientos`.`tipopoliza`,
				`contable_movimientos`.`numeropoliza`,
				`contable_movimientos`.`numeromovimiento`
			LIMIT $InitRecords, $rowLimit
		";

//setLog($sqlCta);


	$movimientos		= 0;
	$cuenta_anterior	= "_INIT_";
	$exoTR				= "";
	$exoTD				= "";
	$SCargos			= 0;		//Suma de Cargos
	$SAbonos			= 0;		//SUma de Abonos
	$SFinal				= 0;
	$exoCuenta 			= "";
	$MActuales			= 0;
	$tcuenta			= "";
//echo $sqlCta;
	//Imprime la Cuenta
$xRPT->addContent( "<table>
<thead>
	<tr>
		<th width=\"10%\" >Fecha</th>
		<th width=\"10%\" >Poliza</th>
		<th width=\"10%\" >Numero</th>
		<th width=\"25%\" >Concepto</th>
		<th width=\"15%\" >Referencia</th>
		<th width=\"10%\" >Cargo</th>
		<th width=\"10%\" >Abono</th>
		<th width=\"10%\" >Saldo</th>
	</tr>
	</thead>
	<tbody>");

	$rs 	= $ql->getDataRecord($sqlCta);
	$rows	= $ql->getNumberOfRows();
	$crows	= 1;
	foreach ($rs as $rw){
		//if(!isset($tcuenta)){$tcuenta 	= ""; }
		if($crows == $rows){
			$cuenta_anterior	= "_FIN_";
			//======================================================================
			$MFecha			= $rw["fecha"];
			$MTPoliza		= $rw["tipo"];
			$MNumero		= $rw["poliza"];
			$MConcepto  	= $rw["concepto"];
			$MReferencia 	= $rw["referencia"];
			$MCargo			= $rw["cargo"];
			$MAbono			= $rw["abono"];


			if($naturaleza == NC_DEUDORA){	//Deudor
				$saldo = ($saldo + $MCargo) - $MAbono;
			} else {				//Acreedor
				$saldo = ($saldo + $MAbono) - $MCargo;
			}
			$SAbonos += $MAbono;	//suma el abono
			$SCargos += $MCargo;	//suma de cargo
			$MActuales++;			//Suma el Movimiento

			$disCargo	= getFMoney($MCargo);
			$disAbono	= getFMoney($MAbono);
			if($MCargo == 0){ $disCargo = ""; } 
			if($MAbono == 0){ $disAbono = ""; }
			$exoTD  .= "<tr>
						<td width=\"10%\" >" . getFechaCorta($MFecha) . "</td>
						<td width=\"10%\" >$MTPoliza</td>
						<td width=\"10%\" >$MNumero</td>
						<td width=\"25%\" >$MConcepto</td>
						<td width=\"15%\" >$MReferencia</td>
						<td width=\"10%\" class=\"mny\" >" .  $disCargo . "</td>
						<td width=\"10%\" class=\"mny\" >" .  $disAbono . "</td>
						<td width=\"10%\"  class=\"mny\" >" .  getFMoney($saldo) . "</td>
				</tr>";
			//=============================================================================
			$tcuenta = "<tr>
					<td colspan=\"8\" width=\"100%\">
					<table align=\"center\" width=\"100%\" border=\"0\">
  					<tbody>
    					<tr>
      					<th width=\"20%\" class='izq'>$ctafmt</th>
      					<th width=\"40%\" class='izq'>$nombre</th>
      					<td width=\"20%\">Saldo Inicial</td>
      					<th class=\"mny\" width=\"20%\" >" . getFMoney($sdoinicial) . "</th>
    					</tr>
  					</tbody></table></td></tr>";
		}
		$cta			= $rw["numero"];
		$naturaleza		= $rw["naturaleza_real"];
		//================================== NUEVA CUENTA?
		if($cuenta_anterior != $cta ){

			/* Corregir BUG */
			//if($cuenta_anterior == "_FIN_")	{		}
			$nombre			= $rw["nombre"];
			//$naturaleza		= $rw["naturaleza"] * $rw["naturaleza_del_sector"];

			$datosI			= getDatosInicialSFecha($cta, $naturaleza, $fecha_inicial );
			$sdoinicial		= $datosI["saldo"];
			$movimientos	= $datosI["movimientos"];
			//echo $datosI["sql"];
			$saldo 			= $sdoinicial;
			$ctafmt 		= getCuentaFormateada($cta);
			$exoCuenta = "$tcuenta	$exoTD
					<tr><td colspan=\"8\" >
					<table>
					  <tbody>
					    <tr>
					      <td class=\"mny\" colspan=\"4\" ><hr /></td>
					    </tr>
					    <tr>
					      <td class=\"mny\" width=\"70%\" >SUMAS: </td>
					      <th class=\"mny\" width=\"10%\" >" .  getFMoney($SCargos) . "</th>
					      <th class=\"mny\" width=\"10%\" >" .  getFMoney($SAbonos) . "</th>
					      <th class=\"mny\" width=\"10%\" ></th>
    					</tr>

  					</tbody>
					</table>
					</td>
					</tr>";
				/**
			 * @see Re refiere a la decision de imprimir o no ciertas cuentas
			 */
			switch($incluir_cuentas){
				case "con_movimientos":
					if($MActuales == false){ $exoCuenta = ""; }
					break;
				case "saldo_no_cero":
					if($saldo==0){ $exoCuenta = ""; }
					break;
				case "saldo_no_cero_con_mvtos":		//XXX: DUDAAAAAAA
					if(($saldo==0) and ($MActuales == false)){ $exoCuenta = ""; }
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
			if($crows == 1){ $exoCuenta = "";}
			$xRPT->addContent($exoCuenta);
			//Resetea Datos propio de la Cuenta
			$exoTD			= "";
			$SCargos		= 0;		//Suma de Cargos
			$SAbonos		= 0;		//SUma de Abonos
			$SFinal			= 0;
			$exoCuenta		= "";
			$MActuales		= 0;
		}
		//================================================================================
		$MFecha			= $rw["fecha"];
		$MTPoliza		= $rw["tipo"];
		$MNumero		= $rw["poliza"];
		$MConcepto  	= $rw["concepto"];
		$MReferencia 	= $rw["referencia"];
		$MCargo			= $rw["cargo"];
		$MAbono			= $rw["abono"];

		if($naturaleza == NC_DEUDORA){	//Deudor
				$saldo = ($saldo + $MCargo) - $MAbono;
		} else {				//Acreedor
				$saldo = ($saldo + $MAbono) - $MCargo;
		}
		$SAbonos += $MAbono;	//suma el abono
		$SCargos += $MCargo;	//suma de cargo
		$MActuales++;			//Suma el Movimiento

		$disCargo	= getFMoney($MCargo);
		$disAbono	= getFMoney($MAbono);
		if($MCargo == 0){ $disCargo = ""; }
		if($MAbono == 0){	$disAbono = "";	}
		$exoTD  .= "
							<tr>
								<td width=\"10%\" >" . getFechaCorta($MFecha) . "</td>
								<td width=\"10%\" >$MTPoliza</td>
								<td width=\"10%\" >$MNumero</td>
								<td width=\"25%\" >$MConcepto</td>
								<td width=\"15%\" >$MReferencia</td>
								<td width=\"10%\" class=\"mny\" >" .  $disCargo . "</td>
								<td width=\"10%\" class=\"mny\" >" .  $disAbono . "</td>
								<td width=\"10%\"  class=\"mny\" >" .  getFMoney($saldo) . "</td>
							</tr>
							";
		if($cta != "_FIN_")	{
			$netoAbonoRpt	+= $SAbonos; 
			$netoCargoRpt	+= $SCargos;
		}
//====================================================================================================
		$tcuenta = "
					<tr>
					<td colspan=\"8\" width=\"100%\">
					<table align=\"center\" width=\"100%\" border=\"0\">
  					<tbody>
    					<tr>
      					<th width=\"20%\" class='izq'>$ctafmt</th>
      					<th width=\"40%\" class='izq'>$nombre</th>
      					<td width=\"20%\">Saldo Inicial</td>
      					<th class=\"mny\" width=\"20%\" >" . getFMoney($sdoinicial) . "</th>
    					</tr>
  					</tbody>
					</table>
					</td>
					</tr>";


		$cuenta_anterior	= $cta;
		$crows++;

	}

	//PIE DEL REPORTE
$xRPT->addContent( "
	<tr>
		<th colspan='5'>TOTAL REPORTE</th>
		<th>" .  getFMoney($netoCargoRpt) . "</th>
		<th>" .  getFMoney($netoAbonoRpt) . "</th>
		<th></th>
	</tr>
</tbody>
</table>");

echo $xRPT->render(true);
?>