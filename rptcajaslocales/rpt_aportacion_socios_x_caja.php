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
$xHP		= new cHPage("TR.REPORTE DE ", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$xQL		= new MQL();
$xFil		= new cSQLFiltros();

$cajalocal		= parametro("cajalocal", SYS_TODAS, MQL_INT);
$estatus 		= parametro("estado", SYS_TODAS, MQL_INT);
$frecuencia 	= parametro("periocidad", SYS_TODAS, MQL_INT);
$producto 		= parametro("convenio", SYS_TODAS, MQL_INT);  $producto 	= parametro("producto", $producto);
$empresa		= parametro("empresa", SYS_TODAS, MQL_INT);
$grupo			= parametro("grupo", SYS_TODAS, MQL_INT);
$sucursal		= parametro("sucursal", SYS_TODAS, MQL_RAW); $sucursal		= parametro("s", $sucursal, MQL_RAW);
$oficial		= parametro("oficial", SYS_TODAS ,MQL_INT);

$operacion		= parametro("operacion", SYS_TODAS, MQL_INT);
//===========  Individual
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$recibo			= parametro("recibo", 0, MQL_INT); $recibo		= parametro("idrecibo", $recibo, MQL_INT);
//===========  General
$out 			= parametro("out", SYS_DEFAULT);
$FechaInicial	= parametro("on", false, MQL_DATE); $FechaInicial	= parametro("fechainicial", $FechaInicial, MQL_DATE); $FechaInicial	= parametro("fecha-0", $FechaInicial, MQL_DATE); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false, MQL_DATE); $FechaFinal	= parametro("fechafinal", $FechaFinal, MQL_DATE); $FechaFinal	= parametro("fecha-1", $FechaFinal, MQL_DATE); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);

$ByCL			= $xL->OFiltro()->PersonasPorCajaLocal($cajalocal);
$xB				= new cBases(101);
$xB->init();
$items			= $xB->getMembers_InArray();
//var_dump($items);
$DD				= $xQL->getArrayRecord("SELECT `idoperaciones_tipos`,`nombre_corto` FROM  `operaciones_tipos`");
$tsql			= "";
$arrSumas		= array();
$init			= 5;
foreach ($items as $key => $value){
	$nombre				= isset($DD[$value]) ? str_replace(" ", "_", $DD[$value]) : "";
	$tsql				.= ($tsql == "") ? " SUM(IF(`operaciones_mvtos`.`tipo_operacion`=$value,`operaciones_mvtos`.`afectacion_real`,0)) AS `$nombre` " : ",SUM(IF(`operaciones_mvtos`.`tipo_operacion`=$value,`operaciones_mvtos`.`afectacion_real`,0)) AS `$nombre`";
	//setLog(" SUM(IF(`operaciones_mvtos`.`tipo_operacion`=$value,`operaciones_mvtos`.`afectacion_real`,0)) AS `$nombre` ");
	$arrSumas[$init]	= "$nombre";
	$init++;
}
$sql			= "SELECT
`operaciones_mvtos`.`socio_afectado` AS `persona`,
	CONCAT(`socios_general`.`nombrecompleto`,' ',
	`socios_general`.`apellidopaterno`,' ',
	`socios_general`.`apellidomaterno`) AS `nombre`,
	`socios_general`.`curp` ,
	
	`socios_cajalocal`.`descripcion_cajalocal`  AS `caja_local`,
	`socios_region`.`descripcion_region`      	AS `region`,	
	MAX(`operaciones_mvtos`.`fecha_operacion`)  AS `ultima_fecha`,
	
	$tsql       
	
FROM
	`socios_general` `socios_general` 
		INNER JOIN `socios_cajalocal` `socios_cajalocal` 
		ON `socios_general`.`cajalocal` = `socios_cajalocal`.
		`idsocios_cajalocal` 
			INNER JOIN `operaciones_mvtos` `operaciones_mvtos` 
			ON `operaciones_mvtos`.`socio_afectado` = `socios_general`.`codigo` 
				INNER JOIN `eacp_config_bases_de_integracion_miembros` 
				`eacp_config_bases_de_integracion_miembros` 
				ON `operaciones_mvtos`.`tipo_operacion` = 
				`eacp_config_bases_de_integracion_miembros`.`miembro` 
					INNER JOIN `socios_region` `socios_region` 
					ON `socios_general`.`region` = `socios_region`.
					`idsocios_region` 
WHERE
	(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` =101)
		$ByCL
GROUP BY
	`operaciones_mvtos`.`socio_afectado`
		";
$titulo			= "";
$archivo		= "";

$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());
//============ Reporte
$xT		= new cTabla($sql, 2);
$xT->setTipoSalida($out);
$xT->setFootSum($arrSumas);


$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);

$xRPT->addContent($body);

//$xT->setEventKey("jsGoPanel");
//$xT->setKeyField("creditos_solicitud");
$xRPT->addContent( $xT->Show() );
//============ Agregar HTML
//$xRPT->addContent( $xHP->init($jsEvent) );
//$xRPT->addContent( $xHP->end() );


$xRPT->setResponse();
$xRPT->setSenders($senders);

echo $xRPT->render(true);

?>