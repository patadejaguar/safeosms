<?php
/**
 * Reporte de
 *
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package seguimiento
 * @subpackage reports
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
$xHP					= new cHPage("TR.Historial de Saldos", HP_REPORT);

$oficial = elusuario($iduser);

$output					= ( !isset( $_GET["out"] ) ) ? "default" : $_GET["out"];

$idsolicitud 			= $_GET["pb"];		//Numero de Solicitud
$id 					= ( !isset( $_GET["pa"] ) ) ? false : $_GET["pa"];		//Numero de Socio
$f15 					= ( !isset( $_GET["f15"] ) ) ? false : $_GET["f15"];
$f14 					= ( !isset( $_GET["f14"] ) ) ? false : $_GET["f14"];
$f16 					= ( !isset( $_GET["f16"] ) ) ? false : $_GET["f16"];
$f18 					= ( !isset( $_GET["f18"] ) ) ? false : $_GET["f18"];		//Mostrar Movimiento Especifico
$TOperacion 			= ( !isset( $_GET["f19"] ) ) ? false : $_GET["f19"];		//Codigo de Tipo de Operacion.- Mvto Especifico

$fecha_inicial 			= $_GET["on"];
$fecha_final 			= $_GET["off"];


$xHP->init("initComponents()");

$xRPT					= new cReportes($xHP->getTitle());
echo $xHP->getEncabezado();
echo $xRPT->getEncabezado();

$cCred	= new cCredito($idsolicitud); $cCred->init();
//TODO: Modificar
echo $cCred->getFicha(true, "", true, true);

$sql = "SELECT
	`creditos_sdpm_historico`.`idcreditos_sdpm_historico` AS `control`,
	`creditos_sdpm_historico`.`numero_de_socio`,
	`creditos_sdpm_historico`.`numero_de_credito`,
	`creditos_sdpm_historico`.`fecha_anterior`,
	`creditos_sdpm_historico`.`fecha_actual`,
	`creditos_sdpm_historico`.`dias_transcurridos`,
	`creditos_sdpm_historico`.`monto_calculado`,
	`creditos_sdpm_historico`.`saldo`,
	`creditos_sdpm_historico`.`estatus`,
	`creditos_sdpm_historico`.`interes_normal`
FROM
	`creditos_sdpm_historico` `creditos_sdpm_historico` 
WHERE
	(`creditos_sdpm_historico`.`numero_de_credito` =$idsolicitud)
ORDER BY
	`creditos_sdpm_historico`.`fecha_anterior` ASC
	/*`creditos_sdpm_historico`.`fecha_actual` DESC */";

	$cTbl 	= new cTabla($sql);
	$cTbl->setTdClassByType();
	
	$cTbl->setWidth();
	$cTbl->Show("", false);
	$TSum	= $cTbl->getFieldsSum();
echo " <table width='100%'>
		<tr>
		<td />
		<td />
		<td />
		<td />
		
		<th class='mny'>"  . getFMoney($TSum["dias_transcurridos"]) . "</th>
		<th class='mny'>"  . getFMoney($TSum["monto_calculado"]) . "</th>
		<th class='mny'>"  . getFMoney($TSum["saldo"]) . "</th>
		<td />
		<th class='mny'>"  . getFMoney($TSum["interes_normal"]) . "</th>
		</tr>
		</table ";

echo $xHP->getPieDePagina();

?>
</body>
<script  >
<?php

?>
function initComponents(){
	window.print();
}
</script>
</html>