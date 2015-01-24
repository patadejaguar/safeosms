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
$xHP		= new cHPage("TR.Poliza_Contable", HP_FORM);
$xQL		= new cSQLListas();
$jxc 		= new TinyAjax();

function jsaGetPolizas($fecha, $tipo){
	$xF			= new cFecha();
	$fecha 		= $xF->getFechaISO($fecha);
	$xQL		= new cSQLListas();
	$xT			= new cTabla($xQL->getListadoDePolizasContables($fecha, $tipo), 7);
	$xBtn		= new cHImg();
	$xT->setKeyField("codigo");
	$xT->OButton("TR.Modificar", "jsAgregarMovimientos('" . HP_REPLACE_ID . "')\"", $xT->ODicIcons()->AGREGAR);
	$xT->OButton("TR.Imprimir", "jsImprimirPoliza('" . HP_REPLACE_ID . "')\"", $xT->ODicIcons()->IMPRIMIR);
	$xT->OButton("TR.Eliminar", "jsEliminarPoliza('" . HP_REPLACE_ID . "')\"", $xT->ODicIcons()->ELIMINAR);

	return $xT->Show();	
}

function jsaEliminarPoliza($clave){
	$xPol	= new cPoliza(false);
	$xPol->setPorCodigo($clave);
	$xPol->init();
	$xPol->setDeletePoliza();
	return $xPol->getMessages(OUT_HTML);
}

$jxc ->exportFunction('jsaGetPolizas', array('idfecha-0', 'idtipodepoliza'), "#idlistadopolizas");
$jxc ->exportFunction('jsaEliminarPoliza', array('idpolizaactiva'), "#idmsgs");

$jxc ->process();

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->init();

$xFRM		= new cHForm("frmpolizas", "frm_poliza_contable.php");
$msg		= "";
$xBtn		= new cHImg();
$xHF		= new cHDate();
$xHS		= new cHSelect();
$xDiv		= new cHDiv();

$xHS->addEvent("jsaGetPolizas()", "onchange");
//`contable_polizasdiarios`
$xHF->addEvents(" onchange=\"jsaGetPolizas()\" ");
$xFRM->addHElem( $xHF->get("TR.Fecha") );
$xSelPol		= $xHS->getListaDeTiposDePolizas();
$xSelPol->addEspOption(SYS_TODAS, $xFRM->lang("Todas") );
$xSelPol->setOptionSelect(SYS_TODAS);

$xFRM->addHElem( $xSelPol->get(true) );

$xFRM->addDivSolo(jsaGetPolizas(false, false), "", "tx34", "txt14", array(1 => array("id" => "idlistadopolizas")));
//fecha tipo
$xFRM->OHidden("idpolizaactiva", "", "");
$xFRM->OButton("TR.Agregar", "jsAgregarPoliza()", "agregar");
$xFRM->addCerrar(); 
$xFRM->addRefrescar("jsaGetPolizas()");

$xFRM->addAviso(" ");

echo $xFRM->get();
?>
<script>
var xG = new Gen();
function jsAgregarPoliza(){ xG.w({ url : "../frmcontabilidad/nueva_poliza.frm.php?", w : 640, h: 480, tiny : true  }); }
function jsAgregarMovimientos(id){	xG.w({ url : "../frmcontabilidad/poliza_movimientos.frm.php?codigo=" + id, w : 800, h: 600, tiny : true  });}
function jsImprimirPoliza(id){ var xCont	= new ContGen(); xCont.ImprimirPoliza(id);}
function jsEliminarPoliza(id){
	$("#idpolizaactiva").val(id);
	xG.confirmar({ msg: "Desea ELiminar la Poliza Contable?", callback: jsaEliminarPoliza});
}
</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>