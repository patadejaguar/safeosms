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
$xHP		= new cHPage("TR.Otras_Referencias", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$jxc 		= new TinyAjax();

function jsaDatos($persona){
	$xLi	= new cSQLListas();
	$xT		= new cTabla($xLi->getListadoDeReferenciasBancarias($persona));
	$xT2	= new cTabla($xLi->getListadoDeReferenciasComerciales($persona));
	$xT->setKeyTable("socios_relaciones");
	$xT2->setKeyTable("socios_relaciones");
	$xT2->addEliminar();
	$xT->addEliminar();
	return $xT->Show("TR.REFERENCIAS_BANCARIAS") . $xT2->Show("TR.REFERENCIAS_COMERCIALES") ; 
}

$jxc ->exportFunction('jsaDatos', array('persona'), "#idlistado");

$jxc ->process();
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); 
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);

$observaciones	= parametro("idobservaciones");

$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$msg		= "";
if($persona <= DEFAULT_SOCIO){
	
} else {
	$xSoc	= new cSocio($persona);
	if($xSoc->init() == true){
		$xFRM->OHidden("persona", $persona);
		$xFRM->addSeccion("idbancario", "TR.REFERENCIAS_BANCARIAS");
		$xFRM->addHElem($xSel->getListaDeBancos("idbanco")->get(true) );
		//$xFRM->OSelect("idtipocuenta", $valor, $titulo)
		$xFRM->OText("idtipocuenta", "CHEQUES", "TR.Tipo de cuenta");
		$xFRM->OMoneda("idnumerocuenta", 0, "TR.Numero de cuenta");
		$xFRM->OMoneda("idnumerotarjeta", 0, "TR.Numero_de_Tarjeta");
		$xFRM->ODate("idfechaemision", false, "TR.Fecha_de_emision");
		$xFRM->OMoneda("idlimite", 0, "TR.Limite_de_Credito");
		$xFRM->endSeccion();
		$xFRM->addSeccion("idcomercial", "TR.REFERENCIAS_COMERCIALES");
		$xFRM->OText("idnombre", "", "TR.NOMBRE");
		$xFRM->OText("iddireccion", "", "TR.DOMICILIO");
		$xFRM->OMoneda("idtelefono", 0, "TR.TELEFONO");
		
		$xFRM->endSeccion();
		
		$xFRM->addSeccion("idlista", "TR.LISTA");
		$xFRM->addHElem("<div id='idlistado'></div>");
		$xFRM->endSeccion();
		$xFRM->setNoAcordion();
		$xFRM->addJsInit("jsaDatos();");
	}
}
//$xFRM->addJsBasico();
//$xFRM->addCreditBasico();
$xFRM->addGuardar("jsAgregarReferencia()");
//$xFRM->addSubmit();
echo $xFRM->get();
?>
<script>
var xP	= new PersGen();
function jsAgregarReferencia(){

	var idbanco	= $("#idbanco").val();
	var idtipocuenta	= $("#idtipocuenta").val();
	var idnumerocuenta	= $("#idnumerocuenta").val();
	var idnumerotarjeta	= $("#idnumerotarjeta").val();
	var idfechaemision	= $("#idfechaemision").val();
	var idnombre		= $("#idnombre").val();
	var iddireccion		= $("#iddireccion").val();
	var idtelefono		= $("#idtelefono").val();
	var idpersona		= $("#persona").val();
	var idlimite		= $("#idlimite").val();
	//guardar referencia
	if($.trim(idnombre) != "" && $.trim(iddireccion) != ""){
		xP.setAddReferenciaComercial({ nombre : idnombre, direccion : iddireccion, telefono : idtelefono, persona : idpersona, callback : jsAgregado });
	}
	//guardar banco
	if(entero(idnumerocuenta) > 0 || entero(idnumerotarjeta) > 0){
		xP.setAddReferenciaBancaria({ banco : idbanco, tipo : idtipocuenta, tarjeta : idnumerotarjeta, limite: idlimite, fecha : idfechaemision, cuenta : idnumerocuenta, persona : idpersona, callback : jsAgregado });
	}
}
function jsAgregado(v){
	$("#id-frm").trigger("reset");
	xG.spin({ time : 2500, callback : jsaDatos });
}
</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>