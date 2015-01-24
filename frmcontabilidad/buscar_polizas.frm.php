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
include_once("../core/core.contable.inc.php");
require_once("." . TINYAJAX_PATH . "/TinyAjax.php");

$oficial = elusuario($iduser);
$jxc = new TinyAjax();

function jsaGetPolizas($tipo = false, $dia = false){
	$ByDia 		= "";
	$ByTipo		= "";
	$tbl		= "";
	$td			= "";
	if ( $tipo != false AND $tipo != "todas" ){
		$ByTipo = " AND
		(`contable_polizas`.`tipopoliza` = $tipo ) ";
	}
	if ( $dia != false AND $dia != "todas" ){
		$dia = EJERCICIO_CONTABLE . "-" . EACP_PER_CONTABLE . "-" . $dia;
		$ByDia = " AND
		(`contable_polizas`.`fecha` = '$dia' ) ";
	}
		$sql = "
SELECT
	CONCAT(`contable_polizas`.`ejercicio`, '" . STD_LITERAL_DIVISOR ."' ,
	`contable_polizas`.`periodo`, '" . STD_LITERAL_DIVISOR ."' ,
	`contable_polizas`.`tipopoliza`, '" . STD_LITERAL_DIVISOR ."' ,
	`contable_polizas`.`numeropoliza`) AS 'codigo',
	`contable_polizas`.`numeropoliza` AS 'numero',
	`contable_polizasdiarios`.`nombre_del_diario` AS 'tipo',
	`contable_polizas`.`fecha`,
	`contable_polizas`.`cargos`,
	`contable_polizas`.`abonos`
FROM
	`contable_polizas` `contable_polizas`
		INNER JOIN `contable_polizasdiarios` `contable_polizasdiarios`
		ON `contable_polizas`.`tipopoliza` = `contable_polizasdiarios`.
		`idcontable_polizadiarios`
WHERE
		`contable_polizas`.`ejercicio` = " .  EJERCICIO_CONTABLE . "
		AND
		`contable_polizas`.`periodo` = " . EACP_PER_CONTABLE . "
		$ByDia
		$ByTipo
ORDER BY
		`contable_polizas`.`ejercicio`,
		`contable_polizas`.`periodo`,
		`contable_polizas`.`fecha` DESC,
		`contable_polizas`.`tipopoliza`,
		`contable_polizas`.`numeropoliza` DESC
LIMIT 0,100
	";

	$rs = mysql_query($sql, cnnGeneral());
	while($rw = mysql_fetch_array($rs) ){
		$td	.= "<tr>
					<th onclick=\"setValueKey('" . $rw["codigo"] . "')\">" . $rw["numero"] . "</th>
					<td>" . $rw["tipo"] . "</td>
					<td>" . $rw["fecha"] . "</td>
					<td>" . $rw["cargos"] . "</td>
					<td>" . $rw["abonos"] . "</td>
				</tr>";
	}
	$tbl = "<table width='100%' aling='center'>
				<thead>
					<th>Codigo</th>
					<th>Tipo</th>
					<th>Fecha</th>
					<th>Cargos</th>
					<th>Abonos</th>
				</thead>
				<tbody>
				$td
				</tbody>
			</table>";
	return $tbl;
}

$jxc ->exportFunction('jsaGetPolizas', array('idTipoPoliza', 'idDiaPoliza'), "#divListado");
$jxc ->process();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Buscar Polizas</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<?php
$jxc ->drawJavaScript(false, true);
?>
<body onload="initComponents()">
<form name="frmSearchPol" method="POST" action="./">
<fieldset>
	<legend>Listado de Polizas</legend>
	<table    >
		<tbody>
			<tr>
				<th>D&iacute;a</th>
				<th><select name="cDiaPoliza" id="idDiaPoliza" onchange="jsObtenPolizas()"><?php
					//
					$diaI = EJERCICIO_CONTABLE . "-" . EACP_PER_CONTABLE . "-01";
					$diaF = date("t", strtotime($diaI));
					for($i = 1; $i <= $diaF; $i++ ){
						$sel = "";
						$d		= date("j", strtotime(fechasys()));
							if ($i	== $d ){
								$sel	= " selected ='true'";
							}
						echo "<option value='$i' $sel>$i</option>";
					}

				 ?>
				 <option value="todas">Todos</option>
				 </select></th>
				<th>Tipo</th>
				<th><?php
				$xS = new cSelect("cTipoPoliza", "idTipoPoliza", "contable_polizasdiarios");
				$xS->addEspOption("todas", "Todas");
				$xS->setOptionSelect("todas");
				$xS->addEvent("onchange", "jsObtenPolizas");
				$xS->show(false);
				 ?></th>
			</tr>
		</tbody>
	</table>
<div id="divListado">
<?php
	echo jsaGetPolizas();
?>
</div>
</fieldset>
</form>
</body>
<script  >
var vLSep	= "<?php echo STD_LITERAL_DIVISOR; ?>";
	function jsObtenPolizas(mArg){
		jsaGetPolizas();
	}
	function setValueKey(vKey){
		var vDPoliza = vKey.split(vLSep);
		//ejercicio/periodo/tipo/numero
		opener.onEdit 		= true;
		opener.vNotifEdit	= false;
		opener.document.getElementById("idejercicio").value	= vDPoliza[0];
		opener.document.getElementById("idperiodo").value	= vDPoliza[1];
		opener.document.getElementById("idtipopol").value	= vDPoliza[2];
		opener.document.getElementById("idnumeropol").value	= vDPoliza[3];
		opener.document.getElementById("idNumeroAnterior").value	= vDPoliza[3];
		opener.document.getElementById("idconceptopol").focus();
		opener.chkPolizaRegistrada();
		window.close();
	}
function resizeMainWindow(){
	var mWidth	= 384;
	var mHeight	= 512;
	window.resizeTo(mWidth, mHeight);
}
function initComponents(){
	resizeMainWindow();
	//window.moveTo(mLeft, mTop);
}
</script>
</html>
