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
$jxc = new TinyAjax();

function jsaEliminarMovimiento($id){
	
	$xCont	= new cContableOperacion($id);
	$xCont->init();
	$xCont->setEliminar();
		
}
function jsaEditarMovimiento($id, $cuenta, $cargo, $abono, $referencia, $concepto){
	$xCont	= new cContableOperacion($id);
	$xCont->init();
	$xCont->setEditar($cuenta, $cargo, $abono, $referencia, $concepto);
	return $xCont->getMessages(OUT_HTML);		
}
$jxc ->exportFunction('jsaEditarMovimiento', array('clave_unica', 'numerocuenta', 'cargo', 'abono', 'referencia', 'concepto'), "#idmsgs");
$jxc ->exportFunction('jsaEliminarMovimiento', array('clave_unica'), "#idmsgs");


$jxc ->process();
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$xHP->init();
$idx		= parametro("id", null, MQL_RAW);
$xOp		= new cContableOperacion();
$xOp->setPorCodigo($idx);
$xTabla		= $xOp->getObj();

$xFRM		= new cHForm("frmeditarmvto");
$xTxt		= new cHText(); $xSel	= new cHSelect();
$xTxtM		= new cHText(); $xTxtM->setDivClass("");
$msg		= "";

$xFRM->addGuardar("jsEditarMovimiento()");
$xFRM->OButton("TR.Eliminar", "jsEliminarMovimiento()", $xFRM->ic()->ELIMINAR);
$xFRM->addHElem( $xTxt->getDeCuentaContable("numerocuenta", $xTabla->numerocuenta()->v(), true ) );
$xFRM->addDivSolo(
		$xTxtM->getDeMoneda("cargo", "TR.Cargos", $xTabla->cargo()->v()),
		$xTxtM->getDeMoneda("abono", "TR.Abonos", $xTabla->abono()->v()),
		"tx24", "tx24"
);
//$xFRM->OMoneda("cargo", $xTabla->cargo()->v(), "TR.cargo");
//$xFRM->OMoneda("abono", $xTabla->abono()->v(), "TR.abono");

$xFRM->OText("referencia", $xTabla->referencia()->v(), "TR.referencia");
$xFRM->OText("concepto", $xTabla->concepto()->v(), "TR.concepto");

//$xFRM->OMoneda("diario", , "TR.diario");
$xFRM->addHElem( $xSel->getListaDeDiarioDeMvtosContables("diario", $xTabla->diario()->v())->get(true) );

$xFRM->addAviso("");

$xFRM->OHidden("moneda", $xTabla->moneda()->v(), "TR.moneda");
$xFRM->OHidden("fecha", $xTabla->fecha()->v(), "TR.fecha");
$xFRM->OHidden("importe", $xTabla->importe()->v(), "TR.importe");
$xFRM->OHidden("clave_unica", $xTabla->clave_unica()->v(), "TR.clave unica");
$xFRM->OHidden("ejercicio", $xTabla->ejercicio()->v(), "TR.ejercicio");
$xFRM->OHidden("periodo", $xTabla->periodo()->v(), "TR.periodo");
$xFRM->OHidden("tipopoliza", $xTabla->tipopoliza()->v(), "TR.tipopoliza");
$xFRM->OHidden("numeropoliza", $xTabla->numeropoliza()->v(), "TR.numeropoliza");
$xFRM->OHidden("numeromovimiento", $xTabla->numeromovimiento()->v(), "TR.numeromovimiento");
$xFRM->OHidden("tipomovimiento", $xTabla->tipomovimiento()->v(), "TR.tipomovimiento");

//$xFRM->OHidden("poliza_clave_unica", $xTabla->tipomovimiento()->v(), "");



//$xFRM->OMoneda("idnumerode", $valor, $titulo)
//$xFRM->addJsBasico();
//$xFRM->addCreditBasico();
//$xFRM->addSubmit();
echo $xFRM->get();
?>
<script>
var xG		= new Gen();
var xCont	= new ContGen();
function jsEliminarMovimiento(){ xG.confirmar({msg: "Confirma Eliminar este Movimiento?", callback: "jsaEliminarMovimiento()"}); }
function jsConfirmarEliminado(){ jsaEliminarMovimiento(); xG.close(); }
function jsEditarMovimiento(){  xG.confirmar({msg: "Confirma guardar los cambios?", callback: "jsaEditarMovimiento()"}); }
</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>