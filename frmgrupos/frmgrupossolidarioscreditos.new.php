<?php
/**
 * @see
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package creditos
 * @subpackage grupos
 * 		-070708	- Reescritura Total
 *		-
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
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");
require_once("." . TINYAJAX_PATH . "/TinyAjax.php");

$oficial = elusuario($iduser);
$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
$jxc ->process();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<?php
$jsb	= new jsBasicForm("frm_division_montos", iDE_GRUPO);
$jsb->setNCtrlGrupo("cGrupo");
$jsb->mIncludeCaptacion		= false;
$jsb->mIncludeCommon		= false;
$jsb->mIncludeSocio			= false;
$jsb->mIncludeCreditos		= false;
$jsb->mIncludeRecibos		= false;
$jsb->mSubPath				= ".";

//$jxc ->drawJavaScript(false, true);
?>
<body>
<?php
$action		= $_GET["a"];
if ( !isset($action) ){
?>
<form name="frm_division_montos" method="POST" action="./frmgrupossolidarioscreditos.php?a=1">
<fieldset>
	<legend>Planeacion de Creditos de Grupos</legend>
	<table border='0' width='100%'  >
		<tbody>
		<tr>
			<td></td>
			<td><input type='text' name='cGrupo' value='<?php echo DEFAULT_GRUPO; ?>' id="idGrupo" onchange="envgpo();" /></td>
			<td></td>
			<td><input type='text' name='nombregrupo' value='' id="idNombreGrupo" size="50" disabled /></td>
		</tr>
		<tr>
			<td colspan="3"><input type="submit" value="Iniciar Planeacion de Credito" /></td>
		</tr>
		</tbody>
	</table>
	
</fieldset>

</form>
<?php
} else {
	
?>
<form name="frm_division_montos" method="POST" action="./frmgrupossolidarioscreditos.php?a=2">

<?php


	//Imprimir el Grupos
	
	$Grupo		= $_POST["cGrupo"];
	$tds		= "";
	$cG			= new cGrupo($Grupo);
	
	$DMonto		= $cG->getDatosNivelProximo();
	$monto		= $DMonto["monto"];
	
	$sqlGrupos	= "SELECT
						`socios_general`.`codigo`,
						CONCAT(`socios_general`.`nombrecompleto`, ' ',
						`socios_general`.`apellidopaterno`, ' ',
						`socios_general`.`apellidomaterno`) AS 'nombre'
					
					FROM
						`socios_general` `socios_general` 
					WHERE
						(`socios_general`.`grupo_solidario` =$Grupo) 
						AND
						(`socios_general`.`grupo_solidario` !=" . DEFAULT_GRUPO . ")";
	$rs	= mysql_query($sqlGrupos, cnnGeneral());
	$i	= 0;
	while( $rw = mysql_fetch_array($rs)){
		$codigo		= $rw["codigo"];
		$nombre		= $rw["nombre"];
		
		$tds	.= "<tr id='tr-$i'>
						<td>$codigo
							<input type='hidden' name='cSocio-$i' id='idSocio-$i' value='$codigo'></td>
						<td>$nombre</td>
						<td><input type='text' name='cMonto-$i' id='idMonto-$i' value='$monto' class='mny'></td>
						<td><input type='text' name='cObservacion-$i' id='idObservacion-$i' value='' size='50'  /></td>
					</tr>";
		$i++;
	}
	//Imprimir datos de grupo
	echo "<input type='hidden' name='cGrupo' id='idGrupo' value='$Grupo' />";
	echo $cG->getFicha(true);
	
	echo "<fieldset>
		<legend>Planeaci&oacute;n del Cr&eacute;dito</legend>
		<table width='100%' align='center'>
			<tr>
				<th>Numero</th>
				<th>Nombre</th>
				<th>Monto</th>
				<th>Observaciones</th>
			</tr>
			$tds
		</table>
		</fieldset>";
	
}
?>
</form>
<?php

?>
</body>
<?php
$jsb->show();
?>
<script  >
</script>
</html>