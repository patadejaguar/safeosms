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

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();

$xFRM->OButton("TR.Datos Generales", "jsGoToGeneral()", "ejecutar");
$xFRM->OButton("TR.Tasas", "jsGoToTasas()", "tasa");
$xFRM->OButton("TR.Dias", "jsGoToDias()", "fecha");
$xFRM->OButton("TR.cantidades", "jsGoToCantidades()", "moneda");
$xFRM->OButton("TR.Garantias", "jsGoToGarantias()", "garantia");

$xFRM->OButton("TR.Contabilidad de Capital", "jsGoToContableCapital()", "contabilidad");
$xFRM->OButton("TR.Contabilidad de Intereses", "jsGoToContableInteres()", "contabilidad");

$xFRM->OButton("TR.Comisiones", "jsGoToComisiones()", "dinero");
$xFRM->OButton("TR.Permisos", "jsGoToPermisos()", "permisos");
$xFRM->OButton("TR.Scripting", "jsGoToScript()", "codigo");
$xFRM->OButton("TR.Duplicar", "jsClonarProducto()", $xFRM->ic()->EJECUTAR);
$xFRM->OButton("TR.Otros parametros", "jsOtrosParametros()", $xFRM->ic()->CONTROL);
//$xSel->setDivClass();
$msg		= "";

//$xFRM->addSubmit();
$xFRM->addDivSolo($xSel->getListaDeProductosDeCredito()->get());
//$xFRM->addHTML("<div id=\"content\"><object type=\"text/html\" id=\"idFPrincipal\" data=\"../utils/frm_system_tasks.php\" width='100%' height=\"800px\" ></object></div>");
echo $xFRM->get();


?>
<script >
var xG		= new Gen();

function jsGoToGeneral(){jsLoadObject("generales"); }
function jsGoToTasas(){jsLoadObject("tasas"); }
function jsGoToDias(){jsLoadObject("dias"); }
function jsGoToCantidades(){jsLoadObject("cantidades"); }
function jsGoToPermisos(){ jsLoadObject("permisos"); }
function jsGoToScript(){ jsLoadObject("codigo"); }
function jsGoToComisiones(){ jsLoadObject("comisiones"); }
function jsGoToGarantias(){ jsLoadObject("garantias"); }
function jsGoToContableCapital(){ jsLoadObject("contablecapital"); }
function jsGoToContableInteres(){ jsLoadObject("contableinteres"); }
function jsLoadObject(tema){
	var idproducto = $("#idproducto").val();
	sURI	= "../frmcreditos/creditos.productos.frm.php?tema="  + tema  + "&id=" + idproducto; xG.w({url: sURI, tiny : true});
}
function jsClonarProducto(){
	var idproducto = $("#idproducto").val();
	sURI	= "../frmcreditos/creditos.productos.add.frm.php?producto=" + idproducto;
	 xG.w({url: sURI, tiny : true, w: 400, callback: jsRecargar});
}
function jsRecargar(){ window.location = "frmdatos_de_convenios_de_creditos.xul.php"; }
function jsOtrosParametros(){ var idproducto = $("#idproducto").val(); xG.w({url: "../frmcreditos/creditos.productos.otros-datos.frm.php?id=" + idproducto, tiny : true, w: 600}); }
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>