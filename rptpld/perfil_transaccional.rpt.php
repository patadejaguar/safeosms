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
$xHP		= new cHPage("TR.PERFIL TRANSACCIONAL", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
	
$estatus 		= parametro("estado", SYS_TODAS);
$frecuencia 	= parametro("periocidad", SYS_TODAS);
$producto 		= parametro("convenio", SYS_TODAS);  $producto 	= parametro("producto", $producto);
$empresa		= parametro("empresa", SYS_TODAS);
$out 			= parametro("out", SYS_DEFAULT);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);

$FechaInicial	= parametro("on", false); $FechaInicial	= parametro("fecha-0", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("fecha-1", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);


$sql			= "SELECT
	`operaciones_recibos`.`numero_socio`                      AS `persona`,
	`operaciones_recibos`.`fecha_operacion`                   AS `fecha`,
	`operaciones_recibostipo`.`descripcion_recibostipo`       AS `tipo`,
	`operaciones_recibos`.`docto_afectado`                   AS `documento`,
	`personas_perfil_transaccional_tipos`.`nombre_del_perfil` AS `origen`,
	`operaciones_recibos`.`idoperaciones_recibos`	          AS `recibo`,
	`operaciones_recibos`.`total_operacion`                   AS `monto`,
	
	`operaciones_recibos`.`idusuario`                         AS `usuario`,
	`operaciones_recibos`.`observacion_recibo`                AS `observaciones`,
	`operaciones_recibos`.`tipo_pago`                         AS `forma_de_pago`,
	`operaciones_recibos`.`clave_de_moneda`                   AS `moneda`,
	`operaciones_recibos`.`unidades_en_moneda`                AS `unidades`,
	IF(UPPER(`operaciones_recibos`.`clave_de_moneda`) != getMonedaLocal(), getEquivalenciaDeMonedas(`operaciones_recibos`.`unidades_en_moneda`, `operaciones_recibos`.`clave_de_moneda`),
	`operaciones_recibos`.`total_operacion`)
	                AS `equivalencia`
FROM
	`operaciones_recibos` `operaciones_recibos` 
		INNER JOIN `operaciones_recibostipo` `operaciones_recibostipo` 
		ON `operaciones_recibos`.`tipo_docto` = `operaciones_recibostipo`.
		`idoperaciones_recibostipo` 
			INNER JOIN `personas_perfil_transaccional_tipos` 
			`personas_perfil_transaccional_tipos` 
			ON `operaciones_recibos`.`origen_aml` = 
			`personas_perfil_transaccional_tipos`.
			`idpersonas_perfil_transaccional_tipos` 
WHERE
	(`operaciones_recibos`.`numero_socio` =$persona)";
$titulo			= "";
$archivo		= "";

$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());
//============ Reporte
$xSoc		= new cSocio($persona); $xSoc->init();
$xT		= new cTabla($sql, 2);
$xT->setTipoSalida($out);
$xT->setFootSum(array( 4 => "monto", 9 => "unidades", 10 => "equivalencia"));

$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);
$xRPT->addContent($body);

$xRPT->addContent($xSoc->getFicha(true));

//$xT->setEventKey("jsGoPanel");
//$xT->setKeyField("creditos_solicitud");

$xRPT->addContent( $xT->Show( $xHP->getTitle() ) );
$xRPT->addContent( "<h3>" . $xHP->lang("PERFIL_TRANSACCIONAL") . "</h3>" );
$xT		= new cTabla($xL->getListadoDePerfil($persona) );
$xRPT->addContent( $xT->Show(  ) );
$xRPT->addContent( "<h3>" . $xHP->lang("ACUMULADO") . "</h3>" );
//if( MODO_DEBUG == true ){
		
	$xT2	= new cTabla($xL->getAMLAcumuladoOperacionesRT($persona, $FechaFinal,false, false, $FechaInicial));
	$xRPT->addContent($xT2->Show());
//}
$xRPT->addContent( "<h3>" . $xHP->lang("NOTAS") . "</h3>" );
//============ Agregar HTML
//$xRPT->addContent( $xHP->init($jsEvent) );
//$xRPT->addContent( $xHP->end() );
$xAml		= new cAMLPersonas($persona);
$xAml->init();
$validar	= false; //(MODO_DEBUG == true) ? true : false;
$xAml->setVerificarPerfilTransaccional(false, $validar);
$xAml->setVerificarOperacionesSemestrales();
$xRPT->addContent( $xAml->getMessages(OUT_HTML) );


$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);
?>