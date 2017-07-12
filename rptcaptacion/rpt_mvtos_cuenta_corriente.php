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
	$xHP			= new cHPage("TR.REPORTE DE ", HP_REPORT);
	$xL				= new cSQLListas();
	$xF				= new cFecha();
	$query			= new MQL();
	
	$subproducto 		= parametro("subproducto", SYS_TODAS, MQL_INT);  
	$producto 		= parametro("producto", SYS_TODAS, MQL_INT);
	$operacion 		= parametro("operacion", SYS_TODAS, MQL_INT);
	
	//$empresa		= parametro("empresa", SYS_TODAS);
	$out 			= parametro("out", SYS_DEFAULT);
	
	$FechaInicial	= parametro("on", false); $FechaInicial	= parametro("fecha-0", $FechaInicial); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
	$FechaFinal		= parametro("off", false); $FechaFinal	= parametro("fecha-1", $FechaFinal); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);

	$senders		= getEmails($_REQUEST);
	
	$xHP->init();
//XXX: Cambiar SQL por uno que facilite la ejecucion


$sql = "SELECT socios_general.codigo,
CONCAT(socios_general.apellidopaterno, ' ', socios_general.apellidomaterno, ' ', socios_general.nombrecompleto) AS 'nombre_completo',
operaciones_mvtos.idoperaciones_mvtos AS 'operacion',
operaciones_recibos.recibo_fiscal, operaciones_mvtos.fecha_afectacion AS 'fecha_de_pago',
operaciones_mvtos.docto_afectado AS 'documento_contrato',
operaciones_tipos.descripcion_operacion AS 'tipo_operacion',
operaciones_mvtos.afectacion_real AS 'monto',
operaciones_mvtos.detalles

FROM

	`operaciones_tipos` `operaciones_tipos`
		INNER JOIN `operaciones_mvtos` `operaciones_mvtos`
		ON `operaciones_tipos`.`idoperaciones_tipos` =
		`operaciones_mvtos`.`tipo_operacion`
			INNER JOIN `socios_general` `socios_general`
			ON `socios_general`.`codigo` =
			`operaciones_mvtos`.`socio_afectado`
				INNER JOIN `operaciones_recibos`
				`operaciones_recibos`
				ON `operaciones_recibos`.
				`idoperaciones_recibos` =
				`operaciones_mvtos`.`recibo_afectado`

WHERE
			 operaciones_tipos.producto_aplicable=21
			 AND operaciones_mvtos.estatus_mvto=30
			 AND operaciones_mvtos.fecha_operacion>='$FechaInicial'
			 AND operaciones_mvtos.fecha_operacion<='$FechaFinal'
			 ORDER BY socios_general.codigo
			 ";

$titulo			= "";
$archivo		= "reporte-de-captacion-del-$FechaInicial-al-$FechaFinal";

$xRPT			= new cReportes($titulo);
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);
$xRPT->setTitle($xHP->getTitle());
//============ Reporte
$xT		= new cTabla($sql, 2);
$xT->setTipoSalida($out);

$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);

$xRPT->addContent($body);

//$xT->setEventKey("jsGoPanel");
//$xT->setKeyField("creditos_solicitud");
$xRPT->addContent( $xT->Show( $xHP->getTitle() ) );
//============ Agregar HTML
//$xRPT->addContent( $xHP->init($jsEvent) );
//$xRPT->addContent( $xHP->end() );


$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);


exit;
?>