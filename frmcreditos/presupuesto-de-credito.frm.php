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
$xHP		= new cHPage("TR.Presupuesto de Credito", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$jxc 		= new TinyAjax();

$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$jsIDPer	= "idsocio";

function jsaAddDetallePresupuesto($persona, $proveedor, $destino, $monto, $observaciones, $clave){
	$xF			= new cFecha();
	$monto		= setNoMenorQueCero($monto);
	$destino	= setNoMenorQueCero($destino);
	$proveedor	= setNoMenorQueCero($proveedor);
	

	if($persona > DEFAULT_SOCIO AND $monto > 0){
		$xSoc	= new cSocio($persona);
		if($xSoc->init() == true){
			$xPre	= new cCreditosPresupuesto($clave, $persona);
			$xPre->addItem($proveedor, $destino, $monto, $observaciones, false);
		}
	}
	
	//return "";
}

function jsaGetDetallePresupuesto($persona,$clave){
	$xF			= new cFecha();
	$xLi		= new cSQLListas();
	$sql		= $xLi->getListadoDePresupuestado($clave);
	$xT		= new cTabla($sql);
	$xT->setKeyTable("creditos_destino_detallado");
	$xT->setKeyField("idcreditos_destino_detallado");
	$xT->setFootSum(array(
			3 => "monto"
	));
	//$xT->addEliminar();

	return $xT->Show();
}
function jsaCancelarPresupuesto($persona,$clave){
	$xPres	= new cCreditosPresupuesto($clave);
	if($xPres->setCancelar() == false){
		return $xPres->getMessages(OUT_HTML);
	}
}
function jsaEliminarPresupuesto($persona,$clave){
	$xPres	= new cCreditosPresupuesto($clave);
	if($xPres->setEliminar() == false){
		return $xPres->getMessages(OUT_HTML);
	}
}

function jsaGetPresupuestos($idpersona){
	$xSel	= new cHSelect();
	return $xSel->getListaDePresupuestoPorPersona("", false, $idpersona, 0)->get();
}
if($persona > DEFAULT_SOCIO){ $jsIDPer	= "persona"; }

$jxc ->exportFunction('jsaAddDetallePresupuesto', array($jsIDPer, 'idcodigodeproveedor', 'iddestinodecredito', 'idmonto', 'idobservaciones', 'idpresupuesto'), "#idlista");
$jxc ->exportFunction('jsaGetPresupuestos', array($jsIDPer), "#iddivpresupuesto");
$jxc ->exportFunction('jsaGetDetallePresupuesto', array($jsIDPer, 'idpresupuesto'), "#idlista");
$jxc ->exportFunction('jsaCancelarPresupuesto', array($jsIDPer, 'idpresupuesto'), "#idlista");
$jxc ->exportFunction('jsaEliminarPresupuesto', array($jsIDPer, 'idpresupuesto'), "#idlista");
$jxc ->process();

$xHP->init();

$xFRM		= new cHForm("frm", "./", "idfrm");
$xSel		= new cHSelect();
$msg		= "";
$idPres		= "";
$xPres		= null;				//Iniciar de NULL
if(setNoMenorQueCero($clave) > 1){
	$xPres	= new cCreditosPresupuesto($clave);
	if($xPres->init() == true){
		if($persona > DEFAULT_SOCIO AND ($persona != $xPres->getClaveDePersona())){
			$xFRM->addAvisoRegistroError("TR.Persona Diferente al Presupuesto ($persona)");
		}
		$persona	= $xPres->getClaveDePersona();
	}
}
if($persona > DEFAULT_SOCIO){
	$xSoc	= new cSocio($persona);
	$xSoc->init();
	$xFRM->addHElem($xSoc->getFicha(false, true, "", true));
	$xFRM->OHidden("persona", $persona);
	$mSel	= $xSel->getListaDePresupuestoPorPersona("", false, $persona,0);
	$mSel->addEvent("onblur", "jsLoadLista()");
	if(setNoMenorQueCero($clave) > 1 AND $xPres != null){
		$xFRM->addHElem($xPres->getFicha());
		$idPres	= "";
		$xFRM->OHidden("idpresupuesto", $clave);
	} else {
		$idPres	= $mSel->get();
		if($mSel->getCountRows() <=0){
			$xFRM->addJsInit("jsCrearPresupuesto();");
		}		
	}
} else {
	$xFRM->addPersonaBasico();
	$xFRM->OButton("TR.Obtener Presupuesto", "jsLoadPresupuestos()", $xFRM->ic()->CARGAR);
	
}


	$xFRM->addHElem("<div id='iddivpresupuesto' class='tx1'>$idPres</div>");

	$xFRM->addHElem( $xSel->getListaDePersonasConPresupuesto("", false, $persona)->get(true) );
	$xFRM->addHElem( $xSel->getListaDeDestinosDeCredito()->get(true) );
	
	$xFRM->addMonto();
	$xFRM->addObservaciones();
	
	$xFRM->OButton("TR.Guardar Presupuesto", "jsAddPresupuesto()", $xFRM->ic()->GUARDAR);
	$xFRM->OButton("TR.Obtener", "jsaGetDetallePresupuesto()", $xFRM->ic()->RECARGAR);
	$xFRM->OButton("TR.Agregar Presupuesto", "jsCrearPresupuesto()", $xFRM->ic()->AGREGAR);
	$xFRM->OButton("TR.Cancelar Presupuesto", "jsCancelarPresupuesto()", $xFRM->ic()->CERRAR);
	$xFRM->OButton("TR.Eliminar Presupuesto", "jsEliminarPresupuesto()", $xFRM->ic()->ELIMINAR);
	
	$xFRM->OButton("TR.Imprimir Presupuesto", "jsImprimirPresupuesto()", $xFRM->ic()->IMPRIMIR);
	$xFRM->OButton("TR.Agregar Credito", "jsCrearCredito()", $xFRM->ic()->DINERO);
	

$xFRM->addHElem("<div id='idlista' class='tx1'></div>");


$xFRM->addJsInit("jsLoadPresupuestos();");
echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
?>
<script>
var jsid	= "#<?php echo $jsIDPer; ?>";
var xCred	= new CredGen();
var xGen	= new Gen();
var cPer	= $(jsid);
var cMon	= $("#idmonto");
function jsAddPresupuesto(){
	if( $("#idpresupuesto").length > 0){
		var idpersona		= entero($(jsid).val());
		var idpresupuesto	= entero($("#idpresupuesto").val());
		if(idpersona > 0 && idpresupuesto >0 && cMon.val() > 0){
			jsaAddDetallePresupuesto();
			setTimeout("jsPresupuestoGuardado()", 2000);
			xGen.spinInit();
		} else {
			xGen.alerta({msg: "Faltan Datos de persona y presupuesto"});
		}
	} else {
		xGen.alerta({msg: "Faltan Datos"});
	}
}
function jsPresupuestoGuardado(){
	$("#idfrm").trigger("reset");
	jsLoadLista();
	xGen.spinEnd();
}
function jsCancelarPresupuesto(){
	xGen.confirmar({msg: "Desea Cancelar el Presupuesto", callback : jsaCancelarPresupuesto });
}
function jsEliminarPresupuesto(){
	xGen.confirmar({msg: "Desea Eliminar el Presupuesto", callback : jsConfirmarEliminarPresupuesto });
}
function jsConfirmarEliminarPresupuesto(){
	jsaEliminarPresupuesto();
	xGen.spin({callback: jsSalir });
}
function jsSalir(){xGen.close();}
function jsLoadPresupuestos(){
	jsaGetPresupuestos();
	setTimeout("jsaGetDetallePresupuesto()", 1000);
	return true;
}
function jsLoadLista(){	jsaGetDetallePresupuesto();	return true; }
function jsCrearCredito(){
	if( $("#idpresupuesto").length > 0){
		var idpersona	= $(jsid).val();
		var idpresupuesto	= entero($("#idpresupuesto").val());
		var monto	= $("#idsum-monto").val();
		if(flotante(monto) > 0){
			xCred.addCredito({persona : idpersona, monto : monto, producto : CREDITO_PRODUCTO_CON_PRESUPUESTO, origen : iDE_PRESUPUESTO, idorigen : idpresupuesto, monto : monto });
			xGen.close();
		} else {
			xGen.alerta({msg: "Necesita una cantidad"});
		}
	}
}
function jsCrearPresupuesto(){
	var idpersona	= $(jsid).val();
	xGen.w({url : "../frmcreditos/creditos-presupuestos_nuevo.frm.php?persona=" + idpersona, tiny : true , w : 600, callback : jsLoadPresupuestos});
}
function jsImprimirPresupuesto(){
	if( $("#idpresupuesto").length > 0){
		var idpresupuesto	= entero($("#idpresupuesto").val());
		var xPer	= new PersGen();
		xPer.getReportePresupuesto(idpresupuesto);
	}	
}
</script>
<?php
$xHP->fin();
?>