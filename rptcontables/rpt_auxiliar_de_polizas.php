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
$xHP					= new cHPage("TR.Poliza_contable", HP_REPORT);
$ql						= new MQL();
$xF						= new cFecha();
$xL						= new cLang();

$xHP->init("initComponents()");

$fechas					= parametro("f");
$folios					= parametro("n");
$fecha_inicial			= parametro("idfecha-0", false, MQL_DATE);
$fecha_inicial			= parametro("idfechafinal", $fecha_inicial, MQL_DATE);
$fecha_inicial			= parametro("on", $fecha_inicial, MQL_DATE);

$fecha_final			= parametro("idfecha-1", false, MQL_DATE);
$fecha_final			= parametro("idfechainicial", $fecha_final, MQL_DATE);
$fecha_final			= parametro("off", $fecha_final, MQL_DATE);

$folio_inicial			= parametro("init", 0, MQL_INT);
$folio_final			= parametro("end", 99999, MQL_INT);

$tipos_de_poliza		= parametro("t", false, MQL_INT);
$tipos_de_poliza		= parametro("tipo", $tipos_de_poliza, MQL_INT);
$mostrar_como			= parametro("v", SYS_DEFAULT, MQL_RAW);
$mostrar_como			= parametro("vista", SYS_DEFAULT, MQL_RAW);

$imprimir_y_cerrar		= parametro("c");
$codigo_unico			= parametro("codigo");


//--------------------	OLD
if($fechas != ""){
	$fechas				= explode("|", $_GET["f"]);
	$fecha_inicial		= $fechas[0];
	$fecha_final		= $fechas[1];
}
if($folios != ""){
	$folios				= explode("|", $_GET["n"]);
	$folio_inicial		= $folios[0];
	$folio_final		= $folios[1];		
}

$fecha_final			= $xF->getFechaISO($fecha_final);
$fecha_inicial			= $xF->getFechaISO($fecha_inicial);
//Filtros Opcionales
$ByCodigo				= "";
$WhereTipos				= (setNoMenorQueCero($tipos_de_poliza) <= 0) ? "" : " AND `contable_polizas`.`tipopoliza`=$tipos_de_poliza ";

$ByFolio				= ($folio_inicial <= 0) ? "" : " AND `contable_polizas`.`numeropoliza`>=$folio_inicial ";
$ByFolio				.= ($folio_final <= 0) ? "" : " AND `contable_polizas`.`numeropoliza`<=$folio_final ";
$ByFecha				= " AND `contable_polizas`.`fecha`>='$fecha_inicial'	AND `contable_polizas`.`fecha`<= '$fecha_final' ";

if($codigo_unico != ""){
	$xPol				= new cPoliza(false);
	$xPol->setPorCodigo($codigo_unico);
	$ByCodigo			= " AND (`contable_polizas`.`ejercicio` = " . $xPol->getEjercicio() . " AND	`contable_polizas`.`periodo`=" . $xPol->getPeriodo() . "
							AND `contable_polizas`.`tipopoliza`=" . $xPol->getTipo() . " AND `contable_polizas`.`numeropoliza` =" . $xPol->getNumero() . ") ";
	$mostrar_como		= SYS_TODAS;
	$ByFecha			= "";
	$ByFolio			= "";
	$WhereTipos			= "";
}

$exoTbl						= "";
$NetoCargos					= 0;
$NetoAbonos					= 0;

$xT							= new cTipos();
$xFe						= new cFecha(0);
$titleSuma					= $xL->getT("TR.SUMA");
$SQL_poliza = "SELECT
	`contable_polizas`.`fecha`,
	`contable_polizasdiarios`.`nombre_del_diario`,
	`contable_polizas`.`numeropoliza`,
	`contable_polizas`.`concepto`,
	`contable_polizas`.`cargos`,
	`contable_polizas`.`abonos`,
	`contable_polizas`.`tipopoliza`,
	`contable_polizas`.`ejercicio`,
	`contable_polizas`.`periodo`
FROM
	`contable_polizas` `contable_polizas` 
		INNER JOIN `contable_polizasdiarios` 
		`contable_polizasdiarios` 
		ON `contable_polizas`.`tipopoliza` = 
		`contable_polizasdiarios`.`idcontable_polizadiarios`
		
		WHERE `contable_polizas`.`numeropoliza` != 0 
		$ByCodigo
		$ByFecha
		$ByFolio

		$WhereTipos

ORDER BY
	`contable_polizas`.`ejercicio`,
	`contable_polizas`.`periodo`,
	`contable_polizas`.`tipopoliza`,
	`contable_polizas`.`numeropoliza`
	";
//exit( $SQL_poliza );

	$rsPol =  $ql->getDataRecord($SQL_poliza);
	foreach ($rsPol as $rwPol){
		$ejercicio		= $rwPol["ejercicio"];
		$periodo		= $rwPol["periodo"];
		$TPoliza		= $rwPol["tipopoliza"];
		$NPoliza		= $rwPol["numeropoliza"];
		$FPoliza		= $xFe->getFechaCorta($rwPol["fecha"]);
		$CPoliza		= $rwPol["concepto"];
		$TCargos		= $rwPol["cargos"];
		$TAbonos		= $rwPol["abonos"];
		$NTPoliza		= $rwPol["nombre_del_diario"];
		
		$exoTD			= "";
		
		$exoPol = "
		<tr>
			<td>$FPoliza</td>
			<td class='cwarn'>$NTPoliza</td>
			<td class='mny'>$NPoliza</td>
			<td >$CPoliza</td>
			<td ></td>
			<td ></td>
		</tr>
		";
		switch($mostrar_como){
			case SYS_TODAS:
		$SQL_mvtos = "SELECT
		`contable_movimientos`.`numeromovimiento`,
		`contable_movimientos`.`referencia`,
		`contable_movimientos`.`numerocuenta`,
		`contable_catalogo`.`nombre`,
		`contable_movimientos`.`concepto`,
		`contable_movimientos`.`cargo`,
		`contable_movimientos`.`abono` 
		FROM
	`contable_movimientos` `contable_movimientos` 
		INNER JOIN `contable_catalogo` `contable_catalogo` 
		ON `contable_movimientos`.`numerocuenta` = 
		`contable_catalogo`.`numero`
		
		WHERE
			(`contable_movimientos`.`ejercicio` =$ejercicio) AND
			(`contable_movimientos`.`periodo` =$periodo) AND
			(`contable_movimientos`.`tipopoliza` =$TPoliza) AND
			(`contable_movimientos`.`numeropoliza` =$NPoliza) 
		";
		$exoTMP = "";
		$rsMvtos = $ql->getDataRecord($SQL_mvtos);
		
			foreach ($rsMvtos as $rwMov){
				
					$MCargo = "";
					$MAbono = "";
		
					if ($rwMov["cargo"] != 0){
						$MCargo = getFMoney($rwMov["cargo"]);
					}
					if($rwMov["abono"] != 0){
						$MAbono = getFMoney($rwMov["abono"]);
					}
					$MConcepto = $xT->cChar($rwMov["concepto"], 40);
					//$MConcepto = $rwMov["concepto"];
			$exoTMP .= "
				<tr>
					<th class='mny'>" . $rwMov["numeromovimiento"] . "</th>
					<td>" . $xT->cChar($rwMov["referencia"], 12) . "</td>
					<td>" . getCuentaFormateada($rwMov["numerocuenta"]) . "</td>
					<td>" . $xT->cChar($rwMov["nombre"], 30) . "</td>
					<td>" . $MConcepto . "</td>
					<td class='mny'>$MCargo</td>
					<td class='mny'>$MAbono</td>
				</tr>
				";
			}
				$exoTD .= "
				<tr>
				<td colspan=\"6\">
						<table align=\"center\" width=\"100%\">
						  <tbody>
  							<tr>
								<th width=\"4%\">N#</th>
								<th width=\"11%\">Referencia</th>
								<th width=\"10%\">Cuenta</th>
								<th width=\"22%\">Nombre</th>
								<th width=\"33%\">Concepto</th>
								<th width=\"10%\"></th>
								<th width=\"10%\"></th>
							</tr>
							$exoTMP
						  </tbody>
						</table>				
				</td>
				</tr>";
			break;
		}
	
		//Exo esqueleto de la Poliza
		$exoTbl .= $exoPol . $exoTD . "
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<th >$titleSuma</th>
			<th class='mny'>" .  getFMoney($TCargos) . "</th>
			<th class='mny'>" . getFMoney($TAbonos) . "</th>		
		</tr>
		<tr>
		<td colspan='6'><hr /></td>
		</tr>
		";
		$NetoAbonos		+= 	$TAbonos;
		$NetoCargos		+=	$TCargos;
	}
	//Impirmir la Tabla
	echo getRawHeader();
	echo "
<table align=\"center\" width=\"100%\">
  <tbody>
  	<tr>
		<th width=\"10%\">Fecha</th>
		<th width=\"15%\">Tipo</th>
		<th width=\"5%\">Numero</th>
		<th width=\"50%\">Concepto</th>
		<th width=\"10%\">Cargos</th>
		<th width=\"10%\">Abonos</th>
	</tr>
	$exoTbl
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td>TOTAL REPORTE</td>
		<th class='mny'>" . getFMoney($NetoCargos) . "</th>
		<th class='mny'>" . getFMoney($NetoAbonos) . "</th>
	</tr>
  </tbody>
</table>	
	";
	echo getRawFooter();
?>
</body>
<script  >
function initComponents(){
	<?php
		if($imprimir_y_cerrar != ""){
			echo "window.print(); \n
				window.close();";
		}
	?>
}
</script>
</html>