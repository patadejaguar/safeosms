<?php
/**
 * Reporte de
 *
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package migracion
 * @subpackage tcb
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
include_once("../libs/sql.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../core/core.config.inc.php");
include_once("../core/core.common.inc.php");
include_once("../core/core.html.inc.php");
include_once("../core/core.creditos.inc.php");


$oficial = elusuario($iduser);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title></title>
</head>
<link href="<?php echo CSS_REPORT_FILE; ?>" rel="stylesheet" type="text/css">
<body onLoad="initComponents();">
<?php
echo getRawHeader();
?>
<!-- -->
<table       >
	<thead>
		<tr>
			<th colspan="3">REPORTE DE MIGRACION DE MOVIMIENTOS DE CUENTAS DE CAPTACION</th>
		</tr>
<!-- DATOS GENERALES DEL REPORTE  -->
		<tr>
			<td  >&nbsp;</td>
			<td width="20%">Fecha de Elaboracion:</td>
			<td width="30%"><?php echo fecha_larga(); ?></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>Preparado por:</td>
			<td><?php echo $oficial; ?></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td></td>
			<td></td>
		</tr>

	</thead>
</table>
<?php


$limit    = 0;

	$filename = $_SERVER['SCRIPT_NAME'];
    $xHt    = new cHTMLObject();
    $filename = $xHt->getNombreExportable( $filename );


	$arrConvMvto	= array (
			"1" => "D",
			"-1" => "R"
			);

$sql = "
SELECT SQL_CACHE
	`operaciones_mvtos`.`socio_afectado`,
	`operaciones_mvtos`.`docto_afectado`,
	`captacion_cuentas`.`saldo_cuenta`,
	`eacp_config_bases_de_integracion_miembros`.`afectacion`,
	`operaciones_mvtos`.`fecha_afectacion`,
	`operaciones_mvtos`.`afectacion_real`,
	`operaciones_mvtos`.`detalles`
FROM
	`eacp_config_bases_de_integracion_miembros`
	`eacp_config_bases_de_integracion_miembros`
		INNER JOIN `operaciones_mvtos` `operaciones_mvtos`
		ON `eacp_config_bases_de_integracion_miembros`.`miembro` =
		`operaciones_mvtos`.`tipo_operacion`
			INNER JOIN `captacion_cuentas` `captacion_cuentas`
			ON `captacion_cuentas`.`numero_cuenta` = `operaciones_mvtos`.
			`docto_afectado`
WHERE
	(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 8003)
	AND
	(`captacion_cuentas`.`saldo_cuenta` > 0)
	/* AND
	(`captacion_cuentas`.`sucursal` = '" . getSucursal() . "' ) */
	/* Agregar tipo de cuenta */
	AND
	(`captacion_cuentas`.`tipo_cuenta` = 10)
ORDER BY
	`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
	`operaciones_mvtos`.`docto_afectado`,
	`operaciones_mvtos`.`fecha_afectacion`
	/* LIMIT 0,1000 */ ";

$rs	    = mysql_query($sql, cnnGeneral() );
$txt    = "";

while (  $rw = mysql_fetch_array($rs) ){
	$socio		= $rw["socio_afectado"];
	$cuenta		= $rw["docto_afectado"];
	$importe	= $rw["afectacion_real"];
	$fecha		= $rw["fecha_afectacion"];
	$detalles	= $rw["detalles"];

	if ( $cuenta_movible != $cuenta ){
		$mvto	= 0;
		$saldo	= 0;
		$init	= "<tr><td colspan='8' ><hr /></td></tr>";
	} else {
		$mvto++;
		$init 	= "";
	}
	$saldo	+= ( $importe * $rw["afectacion"] );


	$txt    .=  	"$socio\t$cuenta\t$mvto\t$fecha\t" . round($importe, 2) ."\t" . $arrConvMvto[ $rw["afectacion"] ] ."\t" . round($saldo, 2) ."\t$detalles\r\n";
	$cuenta_movible	= $cuenta;
    $limit++;
    if( $limit == 35000 ){
        $x = new cFileLog($filename . "_hasta_" . $cuenta);
        $x->setWrite($txt);
        $x->setClose();
        echo $x->getLinkDownload("Archivo para Importar") . "<br />";
        $limit  = 0;
        $txt    = "";
    }
}
?>
</body>
<script  >
function initComponents(){
	//window.print();
}
</script>
</html>