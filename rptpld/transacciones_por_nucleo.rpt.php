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
$xHP		= new cHPage("TR.REPORTE DE TRANSACCIONES", HP_REPORT);
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

$xSoc			= new cSocio($persona); $xSoc->init();
$idnucleo		= $xSoc->getIDNucleoDeRiesgo();
$xF->set($FechaFinal);
$FechaInicial	= $xF->getDiaInicial();
$sql			= "
SELECT
    `personas_relaciones_recursivas`.`persona`     AS `persona`,
    
    `personas_relaciones_recursivas`.`relacion`     AS `relacion`,
	CONCAT(`socios_general`.`nombrecompleto`,' ',
	`socios_general`.`apellidopaterno`, ' ',
	`socios_general`.`apellidomaterno`) AS  `nombre_de_relacion`,
	    
    `personas_relaciones_recursivas`.`nivel`,
    `operaciones_recibos`.`idusuario`                    AS `usuario`,
    `usuarios`.`nombrecompleto` AS 'nombre_de_usuario',
    
     `operaciones_recibos`.`fecha_operacion`    AS `fecha`,
    COUNT(`operaciones_recibos`.`idoperaciones_recibos`) AS `operaciones`,
    SUM(`operaciones_recibos`.`total_operacion`) AS `monto`
	

FROM
	`personas_relaciones_recursivas` `personas_relaciones_recursivas` 
		INNER JOIN `socios_general` `socios_general` 
		ON `personas_relaciones_recursivas`.`relacion` = `socios_general`.
		`codigo` 
			INNER JOIN `operaciones_recibos` `operaciones_recibos` 
			ON `operaciones_recibos`.`numero_socio` = 
			`personas_relaciones_recursivas`.`persona` 
				INNER JOIN `usuarios` `usuarios` 
				ON `operaciones_recibos`.`idusuario` = `usuarios`.`idusuarios` 
WHERE
    (`operaciones_recibos`.`origen_aml` >0)
	AND (`personas_relaciones_recursivas`.`persona` = $idnucleo)
	AND (`operaciones_recibos`.`fecha_operacion` >='$FechaInicial')
    GROUP BY
        `personas_relaciones_recursivas`.`persona`,
        `operaciones_recibos`.`idusuario`,
        `operaciones_recibos`.`fecha_operacion`,
        `personas_relaciones_recursivas`.`relacion`
				
		";
$titulo			= "";
$archivo		= "";

$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());
//============ Reporte
$xSoc2		= new cSocio($idnucleo); $xSoc2->init();
$xT		= new cTabla($sql, 2);

$xT->setTipoSalida($out);
//$xT->setFootSum(array( 4 => "monto", 9 => "unidades", 10 => "equivalencia"));

$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);


$xRPT->addContent($body);

//$xT->setEventKey("jsGoPanel");
//$xT->setKeyField("creditos_solicitud");
$xRPT->addContent($xSoc->getFicha(true));
$xRPT->addContent($xSoc2->getFicha(true));

$xRPT->addContent( $xT->Show( $xHP->getTitle() ) );

//$xT		= new cTabla($xL->getListadoDePerfil($persona) );
//$xRPT->addContent( $xT->Show(  ) );


//============ Agregar HTML
//$xRPT->addContent( $xHP->init($jsEvent) );
//$xRPT->addContent( $xHP->end() );


$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);
?>