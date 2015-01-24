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
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	include_once("../core/core.db.inc.php");
	$theFile			= __FILE__;
	$permiso			= getSIPAKALPermissions($theFile);
	if($permiso === false){	header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//=====================================================================================================
$xHP		= new cHPage("TR.REPORTE DE OPERACIONES DE CAJA", HP_REPORT);
$mql		= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
	
$xHP->setTitle($xHP->getTitle() );

$oficial = elusuario($iduser);


$estatus 		= parametro("estado", SYS_TODAS);
$frecuencia 	= parametro("periocidad", SYS_TODAS);
$producto 		= parametro("convenio", SYS_TODAS);  $producto 	= parametro("producto", $producto);
$empresa		= parametro("empresa", SYS_TODAS);

$out 			= parametro("out", SYS_DEFAULT);


//$fechaInicial	= (isset($_GET["on"])) ? $xF->getFechaISO( $_GET["on"]) : FECHA_INICIO_OPERACIONES_SISTEMA;
//$fechaFinal		= (isset($_GET["off"])) ? $xF->getFechaISO( $_GET["off"]) : fechasys();
$fecha_inicial	= (isset($_GET["on"])) ?  $_GET["on"] : "";
$fecha_final	= (isset($_GET["off"])) ?  $_GET["off"] : "";

$cajero		= (isset($_GET["f3"])) ? $_GET["f3"] : SYS_TODAS;
$cajero		= (isset($_GET["cajero"])) ? $_GET["cajero"] : $cajero;

$ByCajero	= ($cajero == "" OR $cajero == SYS_TODAS) ? "" : " AND (`tesoreria_cajas_movimientos`.`idusuario` = $cajero) ";

echo $xHP->getHeader();
$sql 	= "
SELECT
	`tesoreria_cajas_movimientos`.`fecha`,
	`tesoreria_cajas_movimientos`.`recibo`,
	`tesoreria_cajas_movimientos`.`tipo_de_exposicion`   AS `tipo_de_pago` ,
	
	`tesoreria_cajas_movimientos`.`monto_del_movimiento` AS `monto`,
	`tesoreria_cajas_movimientos`.`monto_recibido`       AS `recibido`,
	`tesoreria_cajas_movimientos`.`monto_en_cambio`      AS `cambio`,
	/*(CASE WHEN ( `tipo_de_exposicion` = 'transferencia') THEN (`tesoreria_cajas_movimientos`.`cuenta_bancaria`) ELSE NULL END) AS 'banco2', */

	/*`tesoreria_cajas_movimientos`.`banco`                AS `banco`,*/
	(CASE WHEN ( `tipo_de_exposicion` = 'foraneo' OR `tipo_de_exposicion` = '" . TESORERIA_PAGO_CHEQUE . "'
	OR `tipo_de_exposicion` = '" . TESORERIA_COBRO_TRANSFERENCIA . "'
 ) THEN CONCAT(`tesoreria_cajas_movimientos`.`numero_de_cheque`, '|', `tesoreria_cajas_movimientos`.`cuenta_bancaria`, '|',
 (SELECT `nombre_de_la_entidad` FROM `bancos_entidades`	WHERE `idbancos_entidades` = `tesoreria_cajas_movimientos`.`banco` LIMIT 0,1)) 
	ELSE '' END) AS 'datos'
FROM
	`tesoreria_cajas_movimientos` `tesoreria_cajas_movimientos`
WHERE 
		(`tesoreria_cajas_movimientos`.`fecha` >='$fecha_inicial'
		AND
		`tesoreria_cajas_movimientos`.`fecha` <='$fecha_final')
		$ByCajero
ORDER BY
	`tesoreria_cajas_movimientos`.`fecha`,
	`tesoreria_cajas_movimientos`.`tipo_de_exposicion`,
	`tesoreria_cajas_movimientos`.`recibo`,
	`tesoreria_cajas_movimientos`.`cuenta_bancaria`
	";

if($out == OUT_EXCEL ){
	echo $xHP->setBodyinit();
	$xls	= new cHExcel();
	$xls->convertTable($sql, $xHP->getTitle());
} else {
	echo $xHP->setBodyinit("initComponents();");
	$xRPT			= new cReportes();
	
	$xTBL			= new cTabla($sql);
	$xTBL->setTdClassByType();
	echo $xRPT->getHInicial($xHP->getTitle());
	
	$xTBL->setFootSum(array(
		3 => "monto",
		4 => "recibido",
		5 => "cambio"
	));
	
	echo $xTBL->Show();
	
	echo $xRPT->getPie();
	?>
	<script>
	<?php ?>
	function initComponents(){ window.print();	}
	</script>
	<?php
	
}

echo $xHP->setBodyEnd();
$xHP->end(); 
?>