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
$xHP		= new cHPage("TR.OTROS CARGOS", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc = new TinyAjax();
//$jxc ->exportFunction('jsAddCargo', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$producto	= parametro("producto", 0, MQL_INT);
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto		= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo		= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa	= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT);
$grupo		= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria = parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);

$observaciones= parametro("idobservaciones");

$xHP->init();

$xFRM		= new cHForm("frm", "./", "idfrm");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
//$xFRM->addJsBasico();
//$xFRM->addDataTag("tabla", "creditos_productos_costos");
$xTabla		= new cCreditos_productos_costos();
if($clave > 0){
	$xTabla->setData($xTabla->query()->initByID($clave));
} else {
	$xTabla->idcreditos_productos_costos("NULL");
	$xTabla->aplicar_desde(fechasys());
	$xTabla->aplicar_hasta($xF->getFechaMaximaOperativa());
	$xTabla->clave_de_producto($producto);
}

$xFRM->OHidden("idcreditos_productos_costos", $xTabla->idcreditos_productos_costos()->v(), "TR.IDCREDITOS PRODUCTOS COSTOS");
$xFRM->OHidden("clave_de_producto", $xTabla->clave_de_producto()->v(), "TR.CLAVE DE PRODUCTO");
//$xFRM->OMoneda("clave_de_operacion", $xTabla->clave_de_operacion()->v(), "TR.CLAVE DE OPERACION");
$xFRM->addHElem($xSel->getListaDeTiposDeOperacion("clave_de_operacion", $xTabla->clave_de_operacion()->v())->get(true) );
$xFRM->OMoneda("unidades", $xTabla->unidades()->v(), "TR.UNIDADES");
$xFRM->ODate("aplicar_desde", $xTabla->aplicar_desde()->v(), "TR.FECHA_INICIAL");
$xFRM->ODate("aplicar_hasta", $xTabla->aplicar_hasta()->v(), "TR.FECHA_FINAL");

$xFRM->OSiNo("TR.ES TASA", "unidad_de_medida", $xTabla->unidad_de_medida()->v());
$xFRM->OSiNo("TR.Editable", "editable", $xTabla->editable()->v());
$xFRM->OSiNo("TR.Exigible", "exigencia", $xTabla->exigencia()->v());
$xFRM->OSiNo("TR.EN PLAN_DE_PAGOS", "en_plan", $xTabla->en_plan()->v());
$xFRM->OSiNo("TR.ESTATUSACTIVO", "estatus", $xTabla->estatus()->v());

if($clave>0){
	$xFRM->addCRUDSave($xTabla->get(), $clave, true);
} else {
	$xFRM->addCRUD($xTabla->get(), true);
}

echo $xFRM->get();
?>
<script>
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>