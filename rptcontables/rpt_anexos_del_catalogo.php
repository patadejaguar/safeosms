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
$xHP					= new cHPage("TR.Movimientos de Auxiliares del Catalogo", HP_REPORT);
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
	$ByCuentasSaldos	= "  AND (`contable_movimientos`.`numerocuenta` >= $cuenta_inicial) AND  (`contable_movimientos`.`numerocuenta` <= $cuenta_final) ";	
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
		$ByCuentasSaldos	= " AND (`contable_movimientos`.`numerocuenta` = " . CUENTA_DE_CUADRE . ") ";
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



	
	$sqlGI = "SELECT
			`contable_movimientos`.`numerocuenta`,
			COUNT(`contable_movimientos`.`numeromovimiento`) AS 'mvtos'
		FROM
			`contable_movimientos` `contable_movimientos`
				INNER JOIN `contable_catalogo` `contable_catalogo`
				ON `contable_movimientos`.`numerocuenta` = `contable_catalogo`.`numero`
		WHERE
			(`contable_movimientos`.`periodo` =". $periodo . ") AND
			(`contable_movimientos`.`ejercicio` = $ejercicio)
			$ByCuentasSaldos
		GROUP BY
			`contable_movimientos`.`numerocuenta` ";
	$rsCM	= $QL->getDataRecord($sqlGI);// mysql_query($sqlGI, cnnGeneral());

	$arrCM	= array();
	foreach ($rsCM as $rwCM ){
		$arrCM[ $rwCM["numerocuenta"] ] = $rwCM["mvtos"];
	}

//echo "$sqlCta <br />$sqlGI <br />";
$xRPT->addContent( "<table width=\"100%\">
<tbody>
	<tr>
		<th width=\"15%\" rowspan='2'>Cuenta</th>
		<th width=\"25%\" rowspan='2'>Numero</th>
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
	</tr>  ");
$rs 		= $QL->getDataRecord($sqlCta);
//setLog($sqlCta);

foreach ($rs as $rw){
	$exoCuenta 		= "";
	$cta			= $rw["numero"];
	$nombre			= $rw["nombre"];
	$naturaleza		= $rw["naturaleza_real"];
	$mayor			= $rw["mayor"];
	$digito			= $rw["nivel"];
	$afectable		= $rw["afecta"];
	$css			= "class=\"nivel$digito\" ";

	//$naturaleza_segun_reporte

	$xEsq			= new cCuentaContableEsquema($cta);
	
	$ctafmt 		= $xEsq->CUENTA_FORMATEADA;

	/**
	 * Obtiene el Periodo a Trabajar
	 *
	 */
	$antPer		= $periodo - 1;
		if( (int)$antPer <= 0){
			$antPer	= "saldo_inicial";
		} else {
			$antPer = "imp" . (int)$antPer;
		}
	$curPer		= "imp" . (int)$periodo;

	$ths 		= "";

	//echo "$antPer --- $curPer <br />";
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
	$td_final 					= "";
	$td_inicial					= "";
	$saldo						= 0;

	$MActuales 					= 0;
	//$arrCM[$cta];
	if ( isset($arrCM[$cta]) ){ 	$MActuales	= $arrCM[$cta];	}
	//echo "$MActuales  <br />";
	$sqlTam = "
SELECT
	tipo,
	$antPer as 'inicial',
	$curPer as 'movimiento'
FROM
	`contable_saldos` `contable_saldos`
WHERE
	(`contable_saldos`.`cuenta` = $cta) AND
	(`contable_saldos`.`ejercicio` =$ejercicio)
	";
	$tds 		= "";


	$rsm 		= $QL->getDataRecord($sqlTam);// mysql_query(, cnnGeneral());

	/**
	 * @var $MActuales: se refiere a numero de Movimientos en el periodo
	 */

	

	foreach ($rsm as $rm ){
		//$cuenta		= $rm["cuenta"];
		$tipoMvto	= $rm["tipo"];

		//Si el Movimiento es SALDO
		switch ($tipoMvto){

			case 1;
				$mvtos["inicial"]	= $rm["inicial"];
				$mvtos["final"]		= $rm["movimiento"];
				$saldo				= $rm["movimiento"];
				//if ()

					//if
				if ($naturaleza == NC_DEUDORA){
					$td_inicial = "<td class='mny'>" . getFMoney($mvtos["inicial"]) . "</td>
									<td></td>";
					$td_final	= "<td class='mny'>" . getFMoney($mvtos["final"]) . "</td>
									<td></td>";
				} else {
					$td_inicial = "<td></td>
									<td class='mny'>" . getFMoney($mvtos["inicial"]) . "</td>";
					$td_final	= "<td></td>
									<td class='mny'>" . getFMoney($mvtos["final"]) . "</td>";
				}
			break;
			case 2:
				$mvtos["cargos_actual"]		= $rm["movimiento"];

			break;
			case 3:
				$mvtos["abonos_actual"]		= $rm["movimiento"];
			break;
		}
		//$MActuales++;

	} // END mysql Movimientos*/



		$exoCuenta 	= "
		<tr $css>
			<td>" . $xEsq->CUENTA_FORMATEADA . "</td>
			<td>$nombre</td>
			$td_inicial
			<td class='mny'>" . getFMoney($mvtos["cargos_actual"]) . "</td>
			<td class='mny'>" . getFMoney($mvtos["abonos_actual"]) . "</td>
			$td_final
		</tr>
		";
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
$xRPT->addContent("</tbody></table>");


echo $xRPT->render(true);
?>