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
function jsGetCreditosByCriteria($convenio, $estatus, $periocidad, $oficial, $include){
$ByConvenio 	= "";
$ByEstatus		= "";
$ByPeriocidad	= "";
$ByOficial	= "";
$ByInclude	= "";

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

if ( $include == "on" ){
	$ByInclude	= "AND
		(`creditos_solicitud`.`causa_de_mora` = 99)";
}

$sqlCred = "SELECT
	`socios_general`.`codigo`,

	CONCAT(`socios_general`.`apellidopaterno`, ' ',
	`socios_general`.`apellidomaterno`, ' ',
	`socios_general`.`nombrecompleto`) AS 'nombre',
	`creditos_solicitud`.`numero_solicitud`,
	`creditos_periocidadpagos`.`descripcion_periocidadpagos` AS `periocidad`,
	`creditos_tipoconvenio`.`descripcion_tipoconvenio`       AS `convenio`,
	`creditos_estatus`.`descripcion_estatus`                 AS `estatus`,
	`creditos_solicitud`.`saldo_actual`                      AS `saldo`,
	`creditos_causa_de_vencimientos`.`descripcion_de_la_causa`		AS `causa`
FROM
	`creditos_solicitud` `creditos_solicitud`
		INNER JOIN `creditos_estatus` `creditos_estatus`
		ON `creditos_solicitud`.`estatus_actual` = `creditos_estatus`.
		`idcreditos_estatus`
			INNER JOIN `creditos_periocidadpagos` `creditos_periocidadpagos`
			ON `creditos_solicitud`.`periocidad_de_pago` =
			`creditos_periocidadpagos`.`idcreditos_periocidadpagos`
				INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio`
				ON `creditos_solicitud`.`tipo_convenio` =
				`creditos_tipoconvenio`.`idcreditos_tipoconvenio`
					INNER JOIN `socios_general` `socios_general`
					ON `creditos_solicitud`.`numero_socio` = `socios_general`.
					`codigo`
						INNER JOIN `creditos_causa_de_vencimientos` `creditos_causa_de_vencimientos`
						ON `creditos_solicitud`.`causa_de_mora` = `creditos_causa_de_vencimientos`.
						`idcreditos_causa_de_vencimientos`
WHERE
	(`creditos_solicitud`.`saldo_actual` >=" . TOLERANCIA_SALDOS . ")
	$ByEstatus
	$ByInclude
	$ByPeriocidad
	$ByConvenio
	$ByOficial
	";

	$xTbl = new cTabla($sqlCred, 2);
	$xTbl->addEspTool("<input type=\"checkbox\"  id=\"chk" . STD_LITERAL_DIVISOR . "_REPLACE_ID_\" />");
	$xTbl->setTdClassByType();
	$xTbl->setWidth();
	return $xTbl->show();
	//return $sqlCred;

}
$jxc ->exportFunction('jsGetCreditosByCriteria', array('idTipoConvenio', 'idEstatusCredito',
						       'idPeriocidad', 'idOficial',
						       'idNoIncludeClass' ), "#id-listado-de-creditos");
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
					<table align='center'>
						<tr>
					<!-- Estatus del Convenio -->
							<th>Estatus del Credito</th>
							<td><?php
								$sqlTE = "SELECT idcreditos_estatus, descripcion_estatus
    									FROM creditos_estatus";
    							$xTE = new cSelect("cEstatusCredito", "idEstatusCredito", $sqlTE);
    							$xTE->setEsSql();
    							$xTE->addEspOption("todas", "Todos");
    							$xTE->setOptionSelect("todas");
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

					</tr>
					<tr>
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
					</tr>
					<tr>
						<td>No Incluir Clasificados: <input type='checkbox' id='idNoIncludeClass' checked='false' /></td>
						<td><a class='button' onclick='jsGetCreditosByCriteria()'><img src='../images/common/icon-new.png'>Obtener Creditos</a></td>
						<td colspan='2'><a class='button' onclick='jsMarkAll()'><img src='../images/common/default.png'>Marcar Todos</a></td>
					</tr>
					</table>
				</fieldset>

				<fieldset>
					<legend>
						Causas de Morosidad a Asignar y creditos buscados
					</legend>
					<table align='center'>
					<!-- Tipo de Convenio -->
						<tr>
							<th>Causa de la Morosidad</th>
							<td colspan='2'><?php
								$sqlTC = " SELECT idcreditos_causa_de_vencimientos, descripcion_de_la_causa FROM creditos_causa_de_vencimientos ";

								$xTC = new cSelect("cCausas", "idCausas", $sqlTC);
								$xTC->setEsSql();
								$xTC->addEvent("onchange", "jsReloadCreditos");
								$xTC->setOptionSelect($iduser);
								$xTC->show(false);
							?></td>
							<!-- Acciones -->
						<td>
						<td><a class='button' onclick='jsSetCausas()'><img src='../images/common/default.png'>Asignar Causas</a></td>
						</tr>
					
					</table>

					<div id="id-listado-de-creditos"></div>
				</fieldset>
				<div id="PMsg" class='aviso'></div>
</fieldset>
</form>
</body>
<script language='javascript' src='../js/jsrsClient.js'></script>
<script  >

var Frm 					= document.frmAsignarCausas;
var jsrCommonSeguimiento	= "./jseguimiento.js.php";
var divLiteral				= "<?php echo STD_LITERAL_DIVISOR; ?>";
var jsrsContextMaxPool 		= 300;

function jsSetCausas(cmd, stat){
	  	var isLims 		= Frm.elements.length - 1;
		var vCausa		= Frm.cCausas.value;
  		for(i=0; i<=isLims; i++){
			var mTyp 	= Frm.elements[i].getAttribute("type");
			var mID 	= Frm.elements[i].getAttribute("id");
			var mVal	= Frm.elements[i].checked;

			//Verificar si es mayor a cero o no nulo
			if ( (mID!=null) && (mID.indexOf("chk@")!= -1) && (mTyp == "checkbox") && (mVal == true) ){
				//Despedazar el ID para obtener el denominador comun
				var aID	= mID.split(divLiteral);
				jsrsExecute(jsrCommonSeguimiento, jsEchoMsg, "jsSetCausaDeMora", aID[1] + divLiteral + vCausa );
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
	  	var isLims 			= Frm.elements.length - 1;
		var vCausa		= Frm.cCausas.value;
  		for(i=0; i<=isLims; i++){
			var mTyp 	= Frm.elements[i].getAttribute("type");
			var mID 	= Frm.elements[i].getAttribute("id");

			//Verificar si es mayor a cero o no nulo
			if ( (mID!=null) && (mID.indexOf("chk@")!= -1) && (mTyp == "checkbox") ) {
				if ( document.getElementById(mID).checked) {
					document.getElementById(mID).checked = false;
				} else {
					document.getElementById(mID).checked = true;
				}
  			}

  		}
}
</script>
</html>
