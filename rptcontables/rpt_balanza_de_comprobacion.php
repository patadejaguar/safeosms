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
$xHP					= new cHPage("TR.Balanza de comprobacion", HP_REPORT);
$xF						= new cFecha();
$QL						= new MQL();

$cuenta_inicial			= parametro("for", 0, MQL_INT);
$cuenta_final			= parametro("to", 0, MQL_INT);
$out 					= parametro("out", SYS_DEFAULT, MQL_RAW);
$ejercicio				= parametro("ejercicio", 0, MQL_INT);
$periodo				= parametro("periodo", 0, MQL_INT);

//---------------- Valuar Tipo de Cuenta ----------------
$tipo_cuentas			= parametro("tipo", SYS_TODAS, MQL_RAW);
$nivel_cuentas			= parametro("nivel", SYS_TODAS, MQL_RAW);
$incluir_cuentas		= parametro("estado", SYS_TODAS, MQL_RAW);


$ByCuentas				= "";
$ByCuentasSaldos		= "";

if($cuenta_inicial > 0 AND ($cuenta_final > 0) ){
	$xCtaInit			= new cCuentaContableEsquema($cuenta_inicial);
	$cuenta_inicial		= $xCtaInit->CUENTA;
	$ByCuentas			.= " AND (`contable_catalogo`.`numero`>=$cuenta_inicial) ";
	$xCtaFin			= new cCuentaContableEsquema($cuenta_final);
	$cuenta_final		= $xCtaFin->CUENTA;
	$ByCuentas			.= " AND (`contable_catalogo`.`numero`<=$cuenta_final) ";
	$ByCuentasSaldos	= "  AND (`contable_saldos`.`cuenta` >= $cuenta_inicial) AND  (`contable_saldos`.`cuenta` <= $cuenta_final) ";	
}

$FechaInicial			= $xF->getDiaInicial("$ejercicio-$periodo-01");
$FechaFinal				= $xF->getDiaFinal("$ejercicio-$periodo-01");

$xRPT					= new cReportes( $xHP->getTitle() );
$xRPT->addContent( $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal) );
$InitRecords			= parametro("init", 0, MQL_INT);
$xRPT->addCSSFiles("../css/flags.css");
$xRPT->setOut($out);
//=================================================================================================================
//$xRPT->setToPagination($InitRecords);

/**
 * Paginacion
 * I.- Parte
 */
$rowLimit				= 1500; //4Paginas aprox

//marca el Final de los Registros
$EndRecords				= $InitRecords + $rowLimit;
//captura el URI para manipularlo
//$mURI					= $_SERVER['REQUEST_URI'];
//encapsula el patrom
//$patron					=  "/init=\d*/";
//if ( $InitRecords > 0){
//	$mURI				= preg_replace($patron, "init=$EndRecords", $mURI);
//} else {
//	$mURI				.= "&init=$EndRecords";
//}
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
//---------------- valuar Tipo de Cuentas a Mostrar ----------------






$sqlCta = "SELECT
	`contable_catalogo`.`numero`,
	`contable_catalogo`.`nombre`,
	`contable_catalogotipos`.`naturaleza`,
	`contable_catalogotipos`.`naturaleza_del_sector`,
	`contable_catalogotipos`.`operador_del_sector`,
	`contable_catalogo`.`ctamayor` AS 'mayor',
	`contable_catalogo`.`afectable` AS 'afecta',
	/* -  TEMPORAL  - */
	`contable_catalogotipos`.`naturaleza_real`,
	`contable_catalogo`.`tipo`,
	`contable_catalogo`.`digitoagrupador` AS `nivel`
FROM
	`contable_catalogo` `contable_catalogo`
		INNER JOIN `contable_catalogotipos`
		`contable_catalogotipos`
		ON `contable_catalogo`.`tipo` =
		`contable_catalogotipos`.`idcontable_catalogotipos`
WHERE
	`contable_catalogo`.`numero` != 0
	$ByCuentas
	$ByTipo
	$ByNivel
ORDER BY
	`contable_catalogo`.`numero`
LIMIT $InitRecords, $rowLimit
";




//Sumas y Datos Generales
	$SCargosI		= 0;		//Suma de Cargos
	$SAbonosI		= 0;		//SUma de Abonos
	$SCargos		= 0;		//Suma de Cargos
	$SAbonos		= 0;		//SUma de Abonos
	$SCargosF		= 0;		//Suma de Cargos
	$SAbonosF		= 0;		//SUma de Abonos
	$arrCmp	= array (0 =>"inicial", 1=>"cargos_actual",
					 2 => "abonos_actual", 3 => "cargos_anterior",
					 4=>"abonos_anterior", 5 => "final");
//echo $sqlCta;

$xRPT->addContent("<table>
<thead>
	<tr>
		<th width=\"10%\" rowspan='2'>Cuenta</th>
		<th width=\"30%\" rowspan='2'>Numero</th>
		<th colspan='2' width='20%'>Saldos Iniciales</th>
		<th colspan='2' width='20%'>Movimientos</th>
		<th colspan='2' width='20%'>Saldos Finales</th>
	</tr>
	<tr>
		<th>Deudor</th>
		<th>Acreedor</th>
		<th>Cargos</th>
		<th>Abonos</th>
		<th>Deudor</th>
		<th>Acreedor</th>
	</tr>
</thead>
<tbody> ");
$rs 			= $QL->getDataRecord($sqlCta);
/**
* ----------------------------------------------- SALDOS
**/
	$antPer		= $periodo -1;
	if($antPer<=0){
		$antPer	= "saldo_inicial";
	} else {
		$antPer = "imp" . $antPer;
	}
	$curPer			= "imp" . $periodo;

	$sqlTamX = "SELECT SQL_CACHE
	`contable_saldos`.`cuenta` ,
	`contable_saldos`.`tipo`,
	$antPer as 'inicial',
	$curPer as 'movimiento'
FROM
	`contable_saldos` `contable_saldos`
		INNER JOIN `contable_catalogo` `contable_catalogo`
		ON `contable_saldos`.`cuenta` = `contable_catalogo`.`numero`
WHERE
	(`contable_saldos`.`ejercicio` = $ejercicio) $ByCuentasSaldos ";
//setLog($sqlTamX);

		$ArrSdos	= array();
		$rsmX 		= $QL->getDataRecord($sqlTamX);
		/**
		 * @var $MActuales: se refiere a numero de Movimientos en el periodo
		 */
		foreach ($rsmX as $rmX){
			$tipoMvto = $rmX["tipo"];
			switch($tipoMvto){
				case 1:
					$ArrSdos[ $rmX["cuenta"] ]["inicial"] 		= $rmX["inicial"];
					$ArrSdos[ $rmX["cuenta"] ]["final"]			= $rmX["movimiento"];
				break;

				case 2:
					$ArrSdos[ $rmX["cuenta"] ]["cargos"]		= $rmX["movimiento"];
				break;

				case 3:
					$ArrSdos[ $rmX["cuenta"] ]["abonos"]		= $rmX["movimiento"];
				break;
			}
		} // END mysql Movimientos
//========================================================================================
$MActuales			= false;
foreach ($rs as $rw ){
	$exoCuenta 		= "";
	$cta			= $rw["numero"];
	$nombre			= $rw["nombre"];
	$naturaleza		= $rw["naturaleza_real"];
	$mayor			= $rw["mayor"];
	$afecta			= $rw["afecta"];
	$datosI			= getDatosInicialSFecha($cta, $naturaleza, $FechaInicial );
	$sdoinicial		= $datosI["saldo"];
	$movimientos	= $datosI["movimientos"];
	$xEsq			= new cCuentaContableEsquema($cta);
	$nivel			= $rw["nivel"];

	/**
	 * Datos especiales
	 */
	$mvtos 						= array();
	$mvtos["inicial"]			= $ArrSdos[$cta]["inicial"];
	$mvtos["cargos_actual"]		= $ArrSdos[$cta]["cargos"];
	$mvtos["abonos_actual"]		= $ArrSdos[$cta]["abonos"];
	$mvtos["cargos_anterior"]	= 0;
	$mvtos["abonos_anterior"]	= 0;
	$mvtos["final"]				= $ArrSdos[$cta]["final"];

	for ($u = 0; $u <= 5; $u++ ){
		if ( !isset($mvtos[ $arrCmp[$u] ]) ){
			$mvtos[ $arrCmp[$u] ] = 0;
		}
	}
	
	if($mvtos["cargos_actual"] > 0 OR $mvtos["abonos_actual"] > 0 ){ $MActuales				= true;	}
		
	$tds 				= "";
	$saldo 				= $sdoinicial;
	$td_final 			= "";
	$td_inicial			= "";

	if($nivel_cuentas > CONTABLE_CUENTA_NIVEL_MAYOR OR $nivel_cuentas == 0){
		if($afecta == 1){
			$SCargos	+= $mvtos["cargos_actual"];
			$SAbonos	+= $mvtos["abonos_actual"];	
			$SCargosI	+= ($naturaleza == NC_DEUDORA) ? $mvtos["inicial"] : 0;
			$SCargosF	+= ($naturaleza == NC_DEUDORA) ? $mvtos["final"] : 0;
			$SAbonosI	+= ($naturaleza == NC_ACREEDORA) ? $mvtos["inicial"] : 0;
			$SAbonosF	+= ($naturaleza == NC_ACREEDORA) ? $mvtos["final"] : 0;
		}
	} else {
		if($nivel_cuentas == $mayor){
			$SCargos	+= $mvtos["cargos_actual"];
			$SAbonos	+= $mvtos["abonos_actual"];
			$SCargosI	+= ($naturaleza == NC_DEUDORA) ? $mvtos["inicial"] : 0;
			$SCargosF	+= ($naturaleza == NC_DEUDORA) ? $mvtos["final"] : 0;
			$SAbonosI	+= ($naturaleza == NC_ACREEDORA) ? $mvtos["inicial"] : 0;
			$SAbonosF	+= ($naturaleza == NC_ACREEDORA) ? $mvtos["final"] : 0;
		}		
	}	
/*		
			$SCargosI	+= ($afecta == 1) ? $mvtos["inicial"] : 0;
			$SCargosF	+= ($afecta == 1) ? $mvtos["final"] : 0;		
		} else {
			$SCargosI	+= ($nivel_cuentas == $mayor) ? $mvtos["inicial"] : 0;
			$SCargosF	+= ($nivel_cuentas == $mayor) ? $mvtos["final"] : 0;		
		}
*/
	$saldo			= $mvtos["final"];
	
	if ($naturaleza == NC_DEUDORA){
		$td_inicial = "<td class='mny'>" . getFMoney($mvtos["inicial"]) . "</td><td></td>";
		$td_final	= "<td class='mny'>" . getFMoney($mvtos["final"]) . "</td><td></td>";
	} else {
		$td_inicial = "<td></td><td class='mny'>" . getFMoney($mvtos["inicial"]) . "</td>";
		$td_final	= "<td></td><td class='mny'>" . getFMoney($mvtos["final"]) . "</td>";
	}
	
	$nombre			= substr($nombre, 0,80);
	$exoCuenta = "<tr class='nivel$nivel'><td>" . $xEsq->CUENTA_FORMATEADA . "</td>
			<td class='nombrecuenta'>$nombre</td>$td_inicial<td class='mny'>" . getFMoney($mvtos["cargos_actual"]) . "</td>
			<td class='mny'>" . getFMoney($mvtos["abonos_actual"]) . "</td>$td_final</tr>";
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
	$xRPT->addContent( $exoCuenta );
} //END WHILE
// ------------------------ Fin Tabla
$xRPT->addContent("</tbody><tfoot>	<tr>
		<th colspan='2'>SUMAS</th>
		<th class='mny'>" . getFMoney($SCargosI) . "</th>
		<th class='mny'" . getFMoney($SAbonosI) . "</th>
		<th class='mny'>" . getFMoney($SCargos) . "</th>
		<th class='mny'>" . getFMoney($SAbonos) . "</th>
		<th class='mny'>" . getFMoney($SCargosF) . "</th>
		<th class='mny'>" . getFMoney($SAbonosF) . "</th>
	</tr></tfoot></table>");

echo $xRPT->render(true);

?>