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
$xHP		= new cHPage("TR.SOLICITUD GRUPOS_SOLIDARIOS", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$xHT		= new cHTabs();
$montonivel	= 0;

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


$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);$action	= strtolower($action);

$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT);
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);

$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);



$xHP->addJTableSupport();
$xHP->init();

$xFRM		= new cHForm("frmgrupospanel", "../frmgrupos/grupos-add-solicitud.frm.php");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());



$xGr		= new cGrupo($grupo);
if($xGr->init() == true){
	//$xFRM->addCerrar();
	$xFRM->addGuardar();
	
	$xGNivel	= new cGruposNiveles($xGr->getProximoNivel());
	$xGNivel->init();
	$montonivel	= $xGNivel->getMontoBase();
	
	$xG2	= new cGruposCotizaciones();
	$xG3	= new cGruposCotizacionesDetalle();
	
	$xFRM->OHidden("idgrupo", $grupo);
	
	
	$xFRM->addHElem($xGr->getFicha(true, "", true));
	
	
	//$xFRM->addHElem( $xSel->getListaDePeriocidadDePago("periocidad", $xTabla->producto()->v(), false, true)->get(true));
	//$xFRM->addHElem( $xSel->getListaDeDestinosDeCredito("aplicacion", $xTabla->aplicacion()->v())->get(true));
	//$xFRM->addHElem( $xSel->getListaDeTipoDePago("tipocuota_id", $xTabla->tipocuota_id()->v(),true )->get(true) );
	
	
	
	$xHG	= new cHGrid("idcredito","TR.INTEGRANTES");
	$xHG->setSQL($xLi->getListadoDePersonasV2(" AND ( `socios_general`.`grupo_solidario` = $grupo ) ", "0,100"));
	$xHG->addList();
	$xHG->setOrdenar();
	$xHG->addKey("codigo");
	
	$xHG->col("codigo", "TR.CODIGO", "10%");
	$xHG->col("nombre", "TR.NOMBRE_COMPLETO", "60%");
	$xHG->OColFunction("curp", "TR.MONTO", "10%", "jsTest");
	

	

	$xFRM->addJsCode( $xHG->getJs(true) );
	

	$xFRM->addHElem($xHG->getDiv());
	
	
} else {
	$xFRM->addEnviar();
	$xFRM->addGrupoBasico();
}
echo $xFRM->get();
?>
<script>
var xG 		= new Gen();
var xP		= new PersGen();
var monton	= <?php echo $montonivel; ?>;
//var xGpo	= new 
function jsTest(dd){
	//console.log(dd);
	var idpersona	= dd.codigo;
	
	return "<input id='solicitud_" + idpersona + "' type='number' class='mny' step='0.01' value='" + monton + "' />";
}
function jsGetSuma(){
	
}
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>