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
$xHP		= new cHPage("TR.Cambiar Periodo de mesa_de_credito", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$periodo	= parametro("idperiododecredito", false, MQL_INT);
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$msg		= "";

$xHP->init();

$xFRM		= new cHForm("frmcambiarperiodo", "cambiarperiodo.frm.php");
$xSel		= new cHSelect();
$msg		= "";

if(setNoMenorQueCero($periodo) > 0){
	$xP		= new cPeriodoDeCredito($periodo);
	$msg	= $xP->setCambiar($periodo);
	$xFRM->addAviso($msg);
	$xFRM->addAvisoRegistroOK();
}

$xFRM->addHElem($xSel->getListaDePeriodosDeCredito("", fechasys())->get(true) );
$xFRM->OButton("TR.Agregar Nuevo", "addPeriodo()", $xFRM->ic()->AGREGAR);

$xT			= new cTabla($xLi->getListadoDePeriodosDeCredito($xF->get()));
$xFRM->addHTML($xT->Show());
$xFRM->addSubmit("", "", "setTerminar()");

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
?>
<script  >
var xG	= new Gen();
	function setPeriodo(Id){
		document.frmupdateperiodo.cPeriodo.value = Id;
	}
	function setTerminar(){	window.location = "../utils/clssalir.php";	}
	function jsToAction(){
		if ( document.getElementById("idPeriodo").value == "nuevo" ){
			jsGenericWindow("./frmperiodos.php");
		}
			
	}
	function addPeriodo(){ xG.w({ url: "./frmperiodos.php?", tiny : true});	}
</script>
<?php
$xHP->fin();
?>
