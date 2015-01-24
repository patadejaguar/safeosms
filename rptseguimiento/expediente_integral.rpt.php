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
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../core/core.config.inc.php");
include_once("../core/core.common.inc.php");
include_once("../core/core.html.inc.php");

$oficial = elusuario($iduser);
//=====================================================================================================
$fecha_inicial 		= $_GET["on"];
$fecha_final 		= $_GET["off"];
$credito			= $_GET["c"];
$socio				= $_GET["s"];
/**
 * @var $sOrden Indica que una cadena compuesta va a pasar en vez de parametros
 * tipo compuesto socio/solicitud
 * */

$sOrden				= $_GET["o"];
if ( isset($sOrden) ){
	$DO 		= explode("|", $sOrden);
	$socio		= $DO[0];
	$credito	= $DO[1];
}
$compCredito		= "";
$compFecha			= "";
$BySocio			= "";

$compCredito2		= "";
$compFecha2			= "";
$BySocio2			= "";

$compCredito3		= "";
$compFecha3			= "";
$BySocio3			= "";

$compCredito4		= "";
$compFecha4			= "";
$BySocio4			= "";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title></title>
</head>
<link href="../css/reporte.css" rel="stylesheet" type="text/css">
<body onLoad="initComponents();">
<?php
echo getRawHeader();
?>
<!-- -->
<table       >
	<thead>
		<tr>
			<th colspan="3">EXPEDIENTE INTEGRAL DE SEGUIMIENTO</th>
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

if (isset($credito)){
	$compCredito	= "	AND
	(`seguimiento_llamadas`.`numero_solicitud` = $credito)";
	$compCredito2	= "	AND
	(`socios_memo`.`numero_solicitud` = $credito)";
	$compCredito3	= "	AND
	(`seguimiento_notificaciones`.`numero_solicitud` = $credito)";
	$compCredito4	= "	AND
	(`seguimiento_compromisos`.`credito_comprometido` = $credito)";
}

if ( isset($fecha_final) ){
	if ( isset($fecha_inicial) ){
		$compFecha	= " AND
		(`seguimiento_llamadas`.`fecha_llamada` >= '$fecha_inicial')
		AND
		(`seguimiento_llamadas`.`fecha_llamada` <= '$fecha_final')";
		$compFecha2	= " AND
		(`socios_memo`.`fecha_memo` >= '$fecha_inicial')
		AND
		(`socios_memo`.`fecha_memo` <= '$fecha_final')";
		$compFecha3	= " AND
		(`seguimiento_notificaciones`.`fecha_notificacion` >= '$fecha_inicial')
		AND
		(`seguimiento_notificaciones`.`fecha_notificacion` <= '$fecha_final')";
		$compFecha4	= " AND
		(`seguimiento_compromisos`.`fecha_vencimiento` >= '$fecha_inicial')
		AND
		(`seguimiento_compromisos`.`fecha_vencimiento` <= '$fecha_final')";
	} else {
		$compFecha	= "
		AND
		(`seguimiento_llamadas`.`fecha_llamada` = '$fecha_final')";
		$compFecha2	= "
		AND
		(`socios_memo`.`fecha_memo` = '$fecha_final')";
		$compFecha3	= "
		AND
		(`seguimiento_notificaciones`.`fecha_notificacion` = '$fecha_final')";
		$compFecha4	= "
		AND
		(`seguimiento_compromisos`.`fecha_vencimiento` = '$fecha_final')";
	}
}

$input 				= $_GET["out"];
	if (!$input) {
		$input = "default";
	}


	$setSql = "

SELECT
	`seguimiento_llamadas`.`idseguimiento_llamadas` AS `clave`,
	/*`oficiales`.`id`								AS `oficial`,*/
	`oficiales`.`nombre_completo`					AS 'nombre_del_oficial',
	/*`oficiales`.`puesto`,
	`oficiales`.`sucursal`,
	`socios`.`codigo`,
	`socios`.`nombre`,*/
	`seguimiento_llamadas`.`numero_solicitud`       AS `credito`,
	
	`seguimiento_llamadas`.`fecha_llamada`          AS `fecha`,
	`seguimiento_llamadas`.`hora_llamada`           AS `hora`,
	`seguimiento_llamadas`.`estatus_llamada`        AS `estatus`,
	`seguimiento_llamadas`.`observaciones`          AS `resultados`
FROM
	`seguimiento_llamadas` `seguimiento_llamadas`
		INNER JOIN `socios` `socios`
		ON `seguimiento_llamadas`.`numero_socio` = `socios`.`codigo`
			INNER JOIN `oficiales` `oficiales`
			ON `seguimiento_llamadas`.`oficial_a_cargo` = `oficiales`.`id`
WHERE
	(`socios`.`codigo` =	$socio)
	$compCredito
	$compFecha
ORDER BY
	`oficiales`.`id`,
	`seguimiento_llamadas`.`fecha_llamada`,
	`seguimiento_llamadas`.`hora_llamada`,
	`seguimiento_llamadas`.`estatus_llamada`";
	
	$setSql2 = "
SELECT
	`socios_memo`.`idsocios_memo`			AS 'clave',
	/*`socios`.`codigo`,
	`socios`.`nombre`,*/
	`oficiales`.`nombre_completo`         AS `oficial`,
	
	`socios_memo`.`numero_solicitud`      AS `documento`,
	`socios_memo`.`fecha_memo`            AS `fecha`,
	`socios_memotipos`.`descripcion_memo` AS `tipo`,
	
	`socios_memo`.`texto_memo`            AS `texto` 
FROM
	`socios_memo` `socios_memo` 
		INNER JOIN `socios_memotipos` `socios_memotipos` 
		ON `socios_memo`.`tipo_memo` = `socios_memotipos`.`tipo_memo` 
			INNER JOIN `oficiales` `oficiales` 
			ON `socios_memo`.`idusuario` = `oficiales`.`id` 
				INNER JOIN `socios` `socios` 
				ON `socios_memo`.`numero_socio` = `socios`.`codigo` 

WHERE
	(`socios`.`codigo` = $socio)
	$compCredito2
	$compFecha2
	ORDER BY
		`socios`.`codigo`,
		`socios_memo`.`fecha_memo`";

	
	$setSql3 = "
SELECT
	`seguimiento_notificaciones`.`numero_notificacion`  AS `clave`,
	/*`oficiales`.`id`                                    AS `oficial`,*/
	`oficiales`.`nombre_completo`						AS 'nombre_del_oficial',
	/*`oficiales`.`puesto`,
	`oficiales`.`sucursal`,*/
	/*`socios`.`codigo`,
	`socios`.`nombre`,*/
	`seguimiento_notificaciones`.`numero_solicitud`     AS `solicitud`,
	
	`seguimiento_notificaciones`.`fecha_notificacion`   AS `fecha`,
	`seguimiento_notificaciones`.`fecha_vencimiento`    AS `vencimiento`,
	`seguimiento_notificaciones`.`estatus_notificacion` AS `estatus`,
	`seguimiento_notificaciones`.`observaciones`        AS `observaciones` 
FROM
	`seguimiento_notificaciones` `seguimiento_notificaciones` 
		INNER JOIN `oficiales` `oficiales` 
		ON `seguimiento_notificaciones`.`oficial_de_seguimiento` = `oficiales`.
		`id` 
			INNER JOIN `socios` `socios` 
			ON `seguimiento_notificaciones`.`socio_notificado` = `socios`.
			`codigo` 
WHERE
	(`socios`.`codigo` =	$socio)
	$compCredito3
	$compFecha3
	
	ORDER BY
		`oficiales`.`id`,
		`socios`.`codigo`";
	
	$setSql4 = " SELECT
	`seguimiento_compromisos`.`idseguimiento_compromisos` AS 'clave',
	/*`oficiales`.`id` AS 'oficial',*/
	`oficiales`.`nombre_completo`	AS 'nombre_del_oficial',
	/*`oficiales`.`puesto`,
	`oficiales`.`sucursal`,*/

	/*`seguimiento_compromisos`.`socio_comprometido`,
	`socios`.`nombre`,*/

	`seguimiento_compromisos`.`credito_comprometido`,
	
	`seguimiento_compromisos`.`fecha_vencimiento`,
	`seguimiento_compromisos`.`hora_vencimiento`,
	`seguimiento_compromisos`.`tipo_compromiso`,
	`seguimiento_compromisos`.`estatus_compromiso` AS 'estatus',
	`seguimiento_compromisos`.`anotacion`

FROM
	`seguimiento_compromisos` `seguimiento_compromisos`
		INNER JOIN `socios` `socios`
		ON `seguimiento_compromisos`.`socio_comprometido` = `socios`.`codigo`
			INNER JOIN `oficiales` `oficiales`
			ON `seguimiento_compromisos`.`oficial_de_seguimiento` = `oficiales`.
			`id`
WHERE
	(`seguimiento_compromisos`.`socio_comprometido` =$socio)
	$compCredito4
	$compFecha4
ORDER BY
	`oficiales`.`id`,
	`seguimiento_compromisos`.`fecha_vencimiento`,
	`seguimiento_compromisos`.`hora_vencimiento`,
	`seguimiento_compromisos`.`tipo_compromiso`";
	
	$cSocio = new cSocio($socio, true);
	echo $cSocio->getFicha(true);
	
	$c4Tbl = new cTabla($setSql4, 5);
	$c4Tbl->setWidth();
	$c4Tbl->Show("COMPROMISOS DE LA PERSONA", false);	
	
	$cTbl = new cTabla($setSql,3);
	$cTbl->setWidth();
	$cTbl->Show("LLAMADAS DE LA PERSONA", false);

	$c3Tbl = new cTabla($setSql3, 3);
	$c3Tbl->setWidth();
	$c3Tbl->Show("NOTIFICACIONES DE LA PERSONA", false);
	
	$c2Tbl = new cTabla($setSql2, 0);
	$c2Tbl->setWidth();
	$c2Tbl->Show("MEMOS DE LA PERSONA", false);


echo getRawFooter();
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