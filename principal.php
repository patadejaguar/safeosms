<?php
//=====================================================================================================
	include_once("core/go.login.inc.php");
	include_once("core/core.error.inc.php");
	include_once("core/core.html.inc.php");
	include_once("core/core.init.inc.php");	
	$theFile					= __FILE__;
	$permiso					= getSIPAKALPermissions($theFile);
	if($permiso === false){		header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
	$iduser = $_SESSION["log_id"];
//=====================================================================================================

include_once "core/entidad.datos.php";
include_once "core/core.deprecated.inc.php";
include_once "core/core.fechas.inc.php";

$oficial = elusuario($iduser);
?>
<html>
<head>
<title>SISTEMA DE ADMINISTRACION FINANCIERA Y ESTADISTICA</title>
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<body>
<br>
<?php

	$td = "";
	$th = "";
	//for($i=0; $i<=6; $i++){
		$dia        = fechasys();
		$ndia       = dia_semana($dia);
		$ncdia      = fecha_corta($dia);
		$compro     = "";
		
		$sqlob = "SELECT socios.codigo, 
				socios.nombre, 
				seguimiento_compromisos.tipo_compromiso, 
				seguimiento_compromisos.anotacion, 
				seguimiento_compromisos.idseguimiento_compromisos AS 'id',
				seguimiento_compromisos.estatus_compromiso
				FROM socios, seguimiento_compromisos 
				WHERE seguimiento_compromisos.socio_comprometido=socios.codigo
				AND seguimiento_compromisos.fecha_vencimiento='$dia'
				AND seguimiento_compromisos.oficial_de_seguimiento=$iduser ";

		$rs = mysql_query($sqlob);
		$ncomps = mysql_num_rows($rs);
	if($ncomps>0){
			while($rwc = mysql_fetch_array($rs)){
				if($rwc[5] == "no_cumplido"){
					$imgestat = "red_dot.png";
					$msgt = "NO CUMPLIDO";
				} elseif($rwc[5] == "pendiente"){
					$imgestat = "yellow_dot.png";
					$msgt = "PENDIENTE";
				} else {
					$imgestat = "green_dot.png";
					$msgt = "CUMPLIDO";
				}
				$compro = $compro . "\n 
				<div class='compromisos'><img alt=\"Estatus: $rwc[5] \" src=\"../images/seguimiento/$imgestat\" align=\"middle\" title='$msgt [$rwc[1]]' />
				$rwc[0]
					<img alt=\"Mostrar Detalle de Compromisos\" 
					src=\"../images/common/datetime.gif\" align=\"middle\" onclick='show_compromiso($rwc[4]);' title='Detalles del Compromiso' />
					<img  alt=\"Editar Compromiso\" src=\"../images/common/edit.png\" align=\"middle\" 
					onclick='editar_compromiso($rwc[4]);' title='Editar Compromiso' />
				- $rwc[2] -$rwc[5]
				</div>";
			}
		@mysql_free_result($rs);
		
		$td = $td . "\n <tr>
		<td >
		<p class='aviso'>SEGUIMIENTO: COMPROMISOS PARA HOY</p>
		<div>$ndia-$ncdia<img alt=\"Editar Compromiso\" src=\"../images/common/stock_form-date-field.png\" align=\"middle\" onclick='mostrar_un_dia(\"$dia\");'/><div>
		$compro</td>
		</tr>";

		/*echo "<table class='micro_cal'	cellpadding='2' cellspacing='2' border='2' align='center'>
				$td
			</table>
			<script languaje='javascript'> alert('TIENE $ncomps COMPROMISOS PARA HOY'); </script>";*/
	}
?>
</body>
<script  >
function editar_compromiso(Id){
	var pfcred = "../seguimiento/frm_agregar_compromisos.php?i=";
		frmSw = window.open(pfcred + Id, "", "width=800,height=500,dependent=yes");
		frmSw.focus();
}
function show_compromiso(Id){
	//var id = fecha;
	var pfcred = "../seguimiento/frm_show_compromiso.php?i=";
		frmSw = window.open(pfcred + Id, "", "width=600,height=400,dependent=yes");
		frmSw.focus();

}
function mostrar_un_dia(fecha){
	var vDate = fecha;
	var pfMud = "../seguimiento/frm_compromiso_dia.php?i=";
		frmMud = window.open(pfMud + vDate, "", "width=600,height=400,dependent=yes,scrollbars");
		frmMud.focus();
}
</script>
</html>
