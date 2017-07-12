<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 0.0.01
 * @package
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
$xHP		= new cHPage("TR.Exportar Excel", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$jxc = new TinyAjax();
function jsaGetArchivo($idempresa, $idfrecuencia, $idfecha){
	$xF		= new cFecha();
	$xFreq	= new cPeriocidadDePago($idfrecuencia);
	$xFreq->init();
	$IDX	= $xFreq->getNombre();
	$IDX	= substr($IDX, 0,1);
	$idfecha= $xF->getFechaISO($idfecha);
	$xQL	= new MQL();
	$sql	= "SELECT
	'02' AS `mov`,
	LEFT(`creditos_periocidadpagos`.`descripcion_periocidadpagos`,1) AS `frecuencia`,
	`creditos_solicitud`.`numero_socio`,
	'' AS `SUC`,
	'' AS `DP`,
	`creditos_solicitud`.`monto_parcialidad`,
	
	'' AS `aportacionextra`,
	'' AS `pagoprestamo`,	
	
	CONCAT(`socios_general`.`apellidopaterno`, ' ',
	`socios_general`.`apellidomaterno`, '/',
	`socios_general`.`nombrecompleto`) AS `persona`,
	
	DATE_FORMAT('$idfecha','%Y/%m/%d') AS `fecha`, 
	'' AS `adicional`
FROM
	`creditos_solicitud` `creditos_solicitud` 
		INNER JOIN `creditos_periocidadpagos` `creditos_periocidadpagos` 
		ON `creditos_solicitud`.`periocidad_de_pago` = 
		`creditos_periocidadpagos`.`idcreditos_periocidadpagos` 
			INNER JOIN `socios_general` `socios_general` 
			ON `creditos_solicitud`.`numero_socio` = `socios_general`.`codigo` 
	WHERE (`creditos_solicitud`.`persona_asociada` =$idempresa) AND (`creditos_solicitud`.`saldo_actual` >0)
UNION	
SELECT
	'01' AS `mov`,
	'$IDX' AS `frecuencia`,
	`socios_general`.`codigo`,
	'' AS `SUC`,
	'' AS `DP`,	
	`socios_general`.`descuento_preferente`,

	'' AS `aportacionextra`,
	'' AS `pagoprestamo`,	
		
	CONCAT(`socios_general`.`apellidopaterno`, ' ',
	`socios_general`.`apellidomaterno`, '/',
	`socios_general`.`nombrecompleto`) AS `persona`	,

	DATE_FORMAT('$idfecha','%Y/%m/%d'),
	'' AS `adicional`	
	
	FROM `socios_general` `socios_general` WHERE (`socios_general`.`dependencia`  =$idempresa) AND (`socios_general`.`descuento_preferente` >0)
	";
	$xXls	= new cHExcelNew("Cedula de retenciones");
//	$xXls->setRenameSheet(0, "pruebas");
	$rs		= $xQL->getDataRecord($sql);
	$idx	= 2;
	$arrTit = array("Mov","Apo","Codigo de Empleado","Suc","Dp","Importe","Aportación Extraordinaria","Pago por préstamos","Nombre","Fecha","Adicional"	);
	$xXls->addArray($arrTit, 1);
	foreach ($rs as $rw){
		$xXls->addArray($rw, $idx);
		$idx++;
	}
	$rs		= null;
	$xXls->setExportar("Cedula de Ahorro");
	return $xXls->getLinkDownload("TR.Descargar", "");
}
$jxc ->exportFunction('jsaGetArchivo', array('idempresa', 'idperiocidad', 'idfecha'), "#getarchivoxls");
$jxc ->process();
$empresa		= parametro("empresa", SYS_TODAS);

$xHP->init();

$xFRM			= new cHForm("frmlayoutcedulaahorro", "./");
$xSel			= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

//$xFRM->addJsBasico();
$xFRM->OButton("TR.Obtener Archivo", "jsaGetArchivo()", $xFRM->ic()->EXCEL);
$xFRM->addToolbar("<span id='getarchivoxls'></span>");
$xFRM->addHElem($xSel->getListaDePeriocidadDePago("", false)->get("TR.FRECUENCIA", true));
$xFRM->ODate("idfecha", false, "TR.FECHA DE ENVIO");
$xFRM->OHidden("idempresa", $empresa);

echo $xFRM->get();
?>
<script>

</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>