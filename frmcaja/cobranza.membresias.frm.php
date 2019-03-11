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
$xHP		= new cHPage("", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$xSel		= new cHSelect();
$jxc 		= new TinyAjax();
function jsaCargarCobros($dia, $mes, $nombre = ""){
	$xLi	= new cSQLListas();
	$xF		= new cFecha();
	$otros	= ($nombre == "") ? "" : " AND (`personas`.`nombre` LIKE '%$nombre%') ";
	$dia	= ($otros == "") ? $dia : false;		//quitar si hay busqueda
	
	$xT		= new cTabla($xLi->getListadoDePersonasPagoMembresia($dia, $otros),0);
	$sql	= "SELECT
	`operaciones_recibos`.`numero_socio`         AS `persona`,
	`operaciones_recibos`.`periodo_de_documento` AS `periodo`,
	MAX(`operaciones_recibos`.`fecha_operacion`) AS `ultimo_pago`,
	
	SUM(`operaciones_recibos`.`total_operacion`) AS `monto`
	FROM `operaciones_recibos` 
	WHERE	(`operaciones_recibos`.`tipo_docto` =" . RECIBOS_TIPO_PAGO_APORTACIONES . ") AND (`operaciones_recibos`.`fecha_operacion` >='" . $xF->getFechaInicialDelAnno() . "') AND `periodo_de_documento`=$mes
	GROUP BY `operaciones_recibos`.`numero_socio`, `operaciones_recibos`.`periodo_de_documento`";
	
	//$xT->OButton("TR.Membresia", "var xP=new PersGen();xP.setCobroMembresia(" . HP_REPLACE_ID . ", $mes)", $xT->ODicIcons()->COBROS);
	$xT->OButton("TR.Membresia", "jsGetCobranza(" . HP_REPLACE_ID . ")", $xT->ODicIcons()->COBROS);
	$xT->OButton("TR.ESTADO_DE_CUENTA", "var xP=new PersGen();xP.getReporteAportaciones(" . HP_REPLACE_ID . ", $mes)", $xT->ODicIcons()->ESTADO_CTA);
	$xT->setKeyField("persona");
	if($otros == ""){
		$xT->setOmitidos("dia_de_pago");
	} else {
		$xT->setKey(1);
	}
	
	$xNot	= new cHNotif();
	
	$txt	= $xNot->get("Fecha de ultimo pago {{ultimo_pago}} --- Monto Pagado $ {{monto}}", "", $xNot->WARNING);
	$xT->addSubQuery($sql, "persona", $txt);
	//var xP= new PersGen();xP.getReporteAportaciones(10008)
	return $xT->Show();
}
$jxc->exportFunction('jsaCargarCobros', array('iddiames', 'idnumerodemes', 'idbuscar'), "#divlistado");
$jxc->process();

$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->init();

$xFRM		= new cHForm("frmcbramembresia", "./");
$xFRM->OButton("TR.Obtener", "jsaCargarCobros()", $xFRM->ic()->CARGAR);
$xSelD		= $xSel->getListaDeDiasDelMes("", $xF->dia());
$xSelD->addEvent("onchange", "jsaCargarCobros()");
$xSelM		= $xSel->getListaDeMeses();
$xSelM->addEvent("onchange", "jsaCargarCobros()");
$xFRM->addHElem($xSelD->get("TR.DIA DE PAGO", true));
$xFRM->addHElem($xSelM->get(true));
$xFRM->OText_13("idbuscar", "", "TR.BUSCAR");

$xFRM->addHTML("<div id='divlistado'></div>");
$xFRM->addJsInit("jsaCargarCobros();");
echo $xFRM->get();
?>
<script>
function jsGetCobranza(persona){
	var idmes	= $("#idnumerodemes").val();
	$("#pk-" + persona).parent().addClass("tr-pagar");
	var xP=new PersGen();xP.setCobroMembresia(persona, idmes);
}
</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>