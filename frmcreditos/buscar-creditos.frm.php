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
$xHP		= new cHPage("TR.BUSCAR CREDITOS", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc 		= new TinyAjax();
//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$observaciones	= parametro("idobservaciones");

$producto 		= parametro("convenio", SYS_TODAS, MQL_INT); $producto 	= parametro("producto", $producto);

$tiposistema	= parametro("tiposistema",0, MQL_INT);$tiposistema	= parametro("tipoensistema",$tiposistema, MQL_INT);
$next			= parametro("next", "", MQL_RAW);

$xHP->addJTableSupport();
$xHP->init();

$ByTipoSistema	= ($tiposistema > 0) ? " AND (`creditos_tipoconvenio`.`tipo_en_sistema`=$tiposistema) " : "";


$xFRM		= new cHForm("frmbuscarcreds", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

$xFRM->addCerrar();

$xTxt		= new cHText();
$txtB		= $xFRM->l()->getT("TR.BUSCAR");
//$xTxt->setDivClass("");
$xTxt->setIncludeLabel(false);
$xTxt->setDivClass("");
$xTxt->addEvent("jsSetFiltro()", "onkeyup");
$xTxt->setPlaceholder("Escriba cualquier texto : Nombre, periodicidad, empleador, etc");
$xTxt->setNoCleanProps();

$xFRM->addDivMedio($txtB, $xTxt->get("idbuscar", false, ""), "tx12", "tx12" );


$xHG	= new cHGrid("divcreditos",$xHP->getTitle());

$xHG->setSQL("SELECT `personas`.`codigo` AS `persona`,
		`personas`.`nombre` AS `nombre`,
		`personas`.`nombre_dependencia` AS `empleador`,
		`creditos_solicitud`.`numero_solicitud` AS `credito`,
		`creditos_tipoconvenio`.`descripcion_tipoconvenio` AS `producto`,
		`creditos_solicitud`.`fecha_ministracion` AS `ministrado`,
        CONCAT(`creditos_solicitud`.`pagos_autorizados`, '/', `creditos_periocidadpagos`.`descripcion_periocidadpagos`) AS `plazo`,
		`creditos_solicitud`.`saldo_actual` AS `saldo`
FROM `creditos_solicitud` INNER JOIN `personas`  ON `creditos_solicitud`.`numero_socio` = `personas`.`codigo`
INNER JOIN `creditos_tipoconvenio`  ON `creditos_solicitud`.`tipo_convenio` = `creditos_tipoconvenio`.`idcreditos_tipoconvenio`
INNER JOIN `creditos_periocidadpagos`  ON `creditos_solicitud`.`periocidad_de_pago` = `creditos_periocidadpagos`.`idcreditos_periocidadpagos`

WHERE (`creditos_solicitud`.`saldo_actual` >0) AND (( `personas`.`nombre` LIKE '%?%' ) OR ( `personas`.`nombre_dependencia`  LIKE '%?%' )
OR ( `creditos_tipoconvenio`.`descripcion_tipoconvenio`  LIKE '%?%' ) OR (`creditos_periocidadpagos`.`descripcion_periocidadpagos`  LIKE '%?%' )) $ByTipoSistema
LIMIT 0,100");

$xHG->addList();
$xHG->addKey("credito");

$xHG->col("persona", "TR.PERSONA", "10%");
$xHG->col("nombre", "TR.NOMBRE", "20%");
$xHG->col("credito", "TR.CREDITO", "10%");
$xHG->col("producto", "TR.PRODUCTO", "20%");
$xHG->col("plazo", "TR.PLAZO", "10%");
$xHG->ColMoneda("saldo", "TR.SALDO", "10%");

$xHG->setOrdenar();

if($next == ""){
	$xHG->OButton("TR.PANEL", "var xC=new CredGen();xC.goToPanelControl(' + data.record.credito +',{principal:true})", "right-arrow.png");
} else {
	$xHG->OButton("TR.SELECCIONAR", "jsNext(' + data.record.credito +')", "right-arrow.png");
}
//$xHG->OButton("TR.VER", "jsVerContenido('+ data.record.idsistema_eliminados +')", "view.png");
//$xFRM->OButton("TR.FILTRAR", "jsSetFiltro()", $xFRM->ic()->FILTRO);



//$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.idsistema_eliminados +')", "delete.png");
$xFRM->addHElem("<div id='divcreditos'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );



echo $xFRM->get();
?>
<script>
var xG	= new Gen();


function jsSetFiltro(){
	var idd		= $("#idbuscar").val();
	var str		= "&vars=" + idd;
	$('#divcreditos').jtable('destroy');
	jsLGdivcreditos(str);
}
function jsNext(id){
	var nn	= "<?php echo $next ?>";

	xG.go({url: nn + "?credito=" + id });
}
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>