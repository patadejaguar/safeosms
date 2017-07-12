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
require_once("." . TINYAJAX_PATH . "/TinyAjax.php");

$oficial 		= elusuario($iduser);
$jxc 			= new TinyAjax();
function jsGetCreditosByCriteria($convenio, $estatus, $periocidad, $oficial){
$ByConvenio 	= "";
$ByEstatus		= "";
$ByPeriocidad	= "";
$ByOficial		= "";
$ByInclude		= "";

if ($estatus != "todas" ){
$ByEstatus	= " AND
		(`creditos_solicitud`.`estatus_actual` = $estatus) ";
}
if ($oficial != "todas" ){
	$ByOficial	= "	AND
	(`creditos_solicitud`.`oficial_credito` = $oficial)";
}

if ( $convenio != "todas"){
	$ByConvenio		= "	 AND
	(`creditos_solicitud`.`tipo_convenio` =$convenio) ";
}

if ( $periocidad != "todas" ){
	$ByPeriocidad	= " AND (`creditos_solicitud`.`periocidad_de_pago` =$periocidad)";
}


$sqlCred = "SELECT
	`socios_general`.`codigo`,
	CONCAT(
		`socios_general`.`apellidopaterno`, ' ',
		`socios_general`.`apellidomaterno`, ' ',
		`socios_general`.`nombrecompleto`
		)	AS 'nombre',
	`creditos_solicitud`.`grupo_asociado`,
	`creditos_solicitud`.`contrato_corriente_relacionado`,

	`creditos_solicitud`.`numero_solicitud`,
	`creditos_solicitud`.`tipo_convenio`,
	`creditos_solicitud`.`fecha_ministracion`,
	`creditos_solicitud`.`fecha_vencimiento`,
	`creditos_solicitud`.`pagos_autorizados`,
	`creditos_solicitud`.`saldo_actual`,
	`creditos_solicitud`.`monto_parcialidad` ,
	`creditos_solicitud`.`ultimo_periodo_afectado`,
	`creditos_solicitud`.`tasa_ahorro`,
	`creditos_solicitud`.`periocidad_de_pago`
FROM
	`socios_general` `socios_general`
		INNER JOIN `creditos_solicitud` `creditos_solicitud`
		ON `socios_general`.`codigo` = `creditos_solicitud`.`numero_socio`
WHERE
	(`creditos_solicitud`.`saldo_actual` >=" . TOLERANCIA_SALDOS . ")
	$ByEstatus
	$ByInclude
	$ByPeriocidad
	$ByConvenio
	$ByOficial
	";
	$rs				= mysql_query($sqlCred, cnnGeneral() );
	$tds			= "";
	$xTbl			= "";
	$ix				= 0;

	$SqlConv		= "SELECT
							`creditos_tipoconvenio`.`idcreditos_tipoconvenio`,
							`creditos_tipoconvenio`.`descripcion_tipoconvenio`
						FROM
							`creditos_tipoconvenio` `creditos_tipoconvenio`
						WHERE
							(`creditos_tipoconvenio`.`idcreditos_tipoconvenio` !=99) ";

	$SConvenio		= new cSelect("c-$ix-conv", "i-$ix-conv", $SqlConv);
	$SConvenio->setEsSql();

	while($rw = mysql_fetch_array($rs) ){
		//Informacion del credito

		$socio				= $rw["codigo"];
		$nombre				= htmlentities( $rw["nombre"] );
		$credito			= $rw["numero_solicitud"];
		$grupo				= $rw["grupo_asociado"];
		$contrato			= $rw["contrato_corriente_relacionado"];
		$convenio			= $rw["tipo_convenio"];
		$ministrado			= $rw["fecha_ministracion"];
		$vencimiento		= $rw["fecha_vencimiento"];
		$pagos				= $rw["pagos_autorizados"];
		$saldo				= $rw["saldo_actual"];
		$parcialidad		= $rw["monto_parcialidad"];
		$periodo			= $rw["ultimo_periodo_afectado"];
		$tasa_ahorro		= $rw["tasa_ahorro"];
		$vperiocidad		= $rw["periocidad_de_pago"];

		$Cred				= new cCredito($credito, $socio);
		//opciones Especiales
		$CtrlGroup			= "hidden";
		$CtrlAhorro			= "text";
		$CtrlPeriocidad		= "text";
		//Convenio
		$SConvenio->setOptionSelect($convenio);
		$SConvenio->addEvent("onchange", "markMe", $ix);
		$iConv				= $SConvenio->show();
		$DConv				= $Cred->getDatosDeProducto($convenio);
		$clase_grupal			= $DConv["tipo_de_integracion"];
		$OConv				= $Cred->getOProductoDeCredito($convenio);
		//Variaciones de los controles
		if ( $OConv->getEsProductoDeGrupos() == true ){
			$CtrlGroup		= "text";
		}
		if ( $tasa_ahorro == 0 ){
			$CtrlAhorro			= "hidden";
		}
		if ( $vperiocidad	== 360 ){
			$CtrlPeriocidad		= "hidden";
		}

		$tds	.= "<tr>
						<td><input type='hidden' id='i-$ix-soc' name='c-$ix-soc' value='$socio' />$socio</td>
						<td>$nombre</td>
						<td><input type='hidden' id='i-$ix-cred' name='c-$ix-cred' value='$credito' />$credito</td>
						<td><input type='$CtrlGroup' id='i-$ix-grup' name='c-$ix-grup' value='$grupo' size='3' class='mny' onchange=\"markMe($ix)\" /></td>
						<td><input type='$CtrlAhorro' id='i-$ix-capt' name='c-$ix-capt' value='$contrato' size='10' class='mny' onchange=\"markMe($ix)\" /></td>
						<td>$iConv</td>
						<td><input type='text' id='i-$ix-fminis' name='c-$ix-fminis' value='$ministrado' size='10' onchange=\"markMe($ix)\" /></td>
						<td><input type='text' id='i-$ix-fvenc' name='c-$ix-fvenc' value='$vencimiento' size='10' onchange=\"markMe($ix)\" /></td>
						<td><input type='$CtrlPeriocidad' id='i-$ix-pagos' name='c-$ix-pagos' value='$pagos' size='3' class='mny' /></td>
						<td><input type='text' id='i-$ix-saldo' name='c-$ix-saldo' value='$saldo' size='12' class='mny' onchange=\"markMe($ix)\" /></td>
						<td><input type='text' id='i-$ix-parc' name='c-$ix-parc' value='$parcialidad' size='10' class='mny' onchange=\"markMe($ix)\" /></td>
						<td><input type='$CtrlPeriocidad' id='i-$ix-per' name='c-$ix-per' value='$periodo' size='3' class='mny' onchange=\"markMe($ix)\" /></td>
						<th><input type=\"checkbox\"  id=\"chk-$ix\" /></th>
					</tr>";
					$ix++;
	}

	$xTbl	= "<table width='100%'>
				<tr>
					<th>Socio</th>
					<th>Nombre</th>
					<th>Num.<br/>Solicitud</th>
					<th>Grupo</th>
					<th>Contrato<br />de Ahorro</th>
					<th>Convenio</th>
					<th>Ministracion</th>
					<th>Vencimiento</th>
					<th>Pagos</th>
					<th>Saldo</th>
					<th>Parcialidad</th>
					<th>#Letra</th>
					<th></th>
				</tr>
				<tbody>
					$tds
				</tbody>
				</table>
				<input type='hidden' name='cCount' id='idCount' value = '$ix' />";

	return $xTbl;

}
$jxc ->exportFunction('jsGetCreditosByCriteria', array('idTipoConvenio', 'idEstatusCredito',
						       'idPeriocidad', 'idOficial'), "#id-listado-de-creditos");
$jxc ->process();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Asignaci&oacute;n de Causas de la Cartera Vencida</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<?php
//$jsb	= new jsBasicForm("", iDE_CAPTACION);
//$jsb->show();
$jxc ->drawJavaScript(false, true);
?>
<body>

<form name="frmAsignarCausas" method="POST" action="./">
<fieldset>
	<legend> Asignaci&oacute;n de Causas de la Cartera Vencida </legend>

				<fieldset>
					<legend>
						Criterios de los Creditos a Mostrar
					</legend>
					<table align='center' width='100%'>
					<tr>
					<!-- Estatus del Convenio -->
							<th>Estatus del Cr&eacute;dito</th>
							<td><?php
								$sqlTE = "SELECT idcreditos_estatus, descripcion_estatus
    									FROM creditos_estatus";
    							$xTE = new cSelect("cEstatusCredito", "idEstatusCredito", $sqlTE);
    							$xTE->setEsSql();
    							$xTE->addEspOption("todas", "Todos");
    							//$xTE->setOptionSelect("todas");
								$xTE->show(false);
							?></td>
					<!-- Tipo de Convenio -->
							<th>Tipo de Convenio</th>
							<td><?php
								$sqlTC = "SELECT idcreditos_tipoconvenio, descripcion_tipoconvenio
    										FROM creditos_tipoconvenio";
								$xTC = new cSelect("cTipoConvenio", "idTipoConvenio", $sqlTC);
								$xTC->setEsSql();
								$xTC->setOptionSelect("todas");
								$xTC->addEspOption("todas", "Todos");
								$xTC->show(false);
							?></td>

							<th>Periocidad</th>
							<td><?php
								$sqlTP = "SELECT idcreditos_periocidadpagos, descripcion_periocidadpagos
    										FROM creditos_periocidadpagos";
    							$xTP = new cSelect("cPeriocidad", "idPeriocidad", $sqlTP);
								$xTP->setEsSql();
								$xTP->addEspOption("todas", "Todos");
								$xTP->setOptionSelect("todas");
								$xTP->show(false);
							?>
							</td>
					</tr>
					<tr>
							<th>Oficial de Credito</th>
							<td><?php
								$sqlTO = "SELECT id, nombre_completo FROM oficiales /* WHERE estatus='activo' */ ";
								$xTO = new cSelect("cOficial", "idOficial", $sqlTO);
								$xTO->setEsSql();
								$xTO->addEspOption("todas", "Todos");
								$xTO->show(false);
								$xTO->setOptionSelect("todas");
							?></td>

							<!-- Acciones -->

						<td><a class='button' onclick='jsGetCreditosByCriteria()'><img src='../images/common/icon-new.png'>Obtener Creditos</a></td>
						<td colspan='2'><a class='button' onclick='jsMarkAll()'><img src='../images/common/default.png'>Marcar Todos</a></td>
						</tr>
					</table>
				</fieldset>

				<fieldset>
					<legend>
					</legend>

					<div id="id-listado-de-creditos"></div>
				</fieldset>
				<div id="PMsg" class='aviso'></div>
</fieldset>

</form>
</body>
<script language='javascript' src='../js/jsrsClient.js'></script>
<script  >

//<?php echo STD_LITERAL_DIVISOR; ?>

var Frm 					= document.frmAsignarCausas;
var jsrCommonSeguimiento	= "../js/creditos.common.js.php";
var divLiteral				= "-";
var jsrsContextMaxPool 		= 300;

function jsSetCausas(){
	  	var isLims 		= Frm.cCount.value;
  		for(i=0; i<=isLims; i++){
			var mVal	= Frm.elements[i].checked;
			//Verificar si es mayor a cero o no nulo
			if ( mVal == true ){
				//jsrsExecute(jsrCommonSeguimiento, jsEchoMsg, "jsSetCausaDeMora", aID[1] + divLiteral + vCausa );
  			}

  		}
  	//document.getElementById("PMsg").innerHTML = "";
}
function jsEchoMsg(msg){
	document.getElementById("PMsg").innerHTML += " <br />" + msg;
}
function jsReloadCreditos(args){
	jsGetCreditosByCriteria();
}
function jsMarkAll(){
	  	var isLims 			= Frm.cCount.value - 1;
  		for(i=0; i<=isLims; i++){
			if ( document.getElementById("chk-" + i).checked) {
				document.getElementById("chk-" + i).checked = false;
			} else {
				document.getElementById("chk-" + i).checked = true;
			}
  		}
}
function markMe(i){
	document.getElementById("chk-" + i).checked = true;;
}
</script>
</html>
