<?php
//=====================================================================================================
//=====>	INICIO_H
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	$theFile					= __FILE__;
	$permiso					= getSIPAKALPermissions($theFile);
	if($permiso === false){		header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//<=====	FIN_H
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
$xHP				= new cHPage("TR.Poliza Contable", HP_FORM);
$poliza				= parametro("codigo", "");
$id					= parametro("id", 0, MQL_INT);


$jxc 				= new TinyAjax();
function jsaGetCuentas($cuenta){
	$xCta	= new cCuentaContableEsquema($cuenta);	
	$sql 	= "SELECT numero, nombre FROM contable_catalogo WHERE numero LIKE '" . $xCta->CUENTARAW . "%' AND afectable=1  ORDER BY numero LIMIT 0,10";
	$ql		= new MQL();
	$rs		= $ql->getDataRecord($sql);
	$h		= "";
	foreach($rs as $rows){
		$xCta2	= new cCuentaContableEsquema($rows["numero"]);	
		$h	.= "<option value=\"" . $rows["numero"] . "\">" . $xCta2->CUENTARAW . "-" . $rows["nombre"] . "</option>";
	}
	return $h;
}
function jsaGetListadoDeMovimientos($poliza){
	$xPol		= new cPoliza(false);
	$xPol->setPorCodigo($poliza);
	$comp	= "";
	return $xPol->getListadoDeMovimientos($comp);
}
function jsaGuardarMovimiento($poliza, $cuenta, $cargo, $abono, $referencia, $concepto){
	$msg		= "";
	$xPol		= new cPoliza(false);
	$xPol->setPorCodigo($poliza);
	$xPol->addMovimiento($cuenta, $cargo, $abono, $referencia, $concepto);
	return $msg;
}
function jsaGetNombreCuenta($cuenta){
	if(setNoMenorQueCero($cuenta) > 1){
		$xCta	= new cCuentaContable($cuenta); $xCta->init();
		//setLog($cuenta);
		return $xCta->getNombreCuenta();		
	}
}
function jsaSetFinalizarPoliza($poliza, $cargos, $abonos){
	$xPol		= new cPoliza(false);
	$xPol->setPorCodigo($poliza);
	$msg		= "";
	if( setNoMenorQueCero($cargos) <= 0 OR ($cargos != $abonos)  ){
		$msg	.= "ERROR\tPoliza descuadrada $cargos - $abonos\r\n";
	} else {
		$xPol->setTotalAbonos($abonos);
		$xPol->setTotalCargos($cargos);
		
		$xPol->setFinalizar();
		//$msg	.= "";
	}
	$msg		.= $xPol->getMessages();
	return $msg;
}
function jsaEliminarPoliza($poliza){
	$xPol		= new cPoliza(false);
	$xPol->setPorCodigo($poliza);
	$msg		= "";
	$xPol->setDeletePoliza();
	$msg		.= $xPol->getMessages(OUT_HTML);
	return $msg;
}

$jxc ->exportFunction('jsaSetFinalizarPoliza', array('idpoliza', 'idsumacargos', 'idsumaabonos'), "#idmsgs");
$jxc ->exportFunction('jsaEliminarPoliza', array('idpoliza'), "#idmsgs");

$jxc ->exportFunction('jsaGetListadoDeMovimientos', array('idpoliza'), "#idlistado");
$jxc ->exportFunction('jsaGetCuentas', array('idcuenta'), "#listadocuentas");
$jxc ->exportFunction('jsaGetNombreCuenta', array('idcuenta'), "#idnombrecuenta");
$jxc ->exportFunction('jsaGuardarMovimiento', array('idpoliza', 'idcuenta', 'idcargo', 'idabono', 'idreferencia', 'idconcepto'), "#idmsgs");

$jxc ->process();

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->init("initComponents()");

$xFRM		= new cHForm("frmpolizacont", "poliza_movimientos.frm.php");
$msg		= "";
$xPol		= new cPoliza(false);

if($id>0){
	if($xPol->initById($id) == true){
		$poliza	= $xPol->getCodigoCompuesto();
	}
}



$xPol->initByCodigo($poliza);
$xPol->init();

$xFRM ->setTitle($xHP->getTitle());


$xFRM->addHElem( $xPol->getFicha() );

$comp	= "<fieldset class='mvtoscontables'><legend>" . $xFRM->lang("Captura") . "</legend><table class='mvtoscontables'><td class=\"cuenta\"><input type='text' id='idcuenta' onkeyup='jsKeyAction(event, this)' list='listadocuentas' placeholder='numero de cuenta' autocomplete='off'  onfocus='this.select()' onblur='jsaGetNombreCuenta()' /></td>";
$comp	.= "<th class=\"nombrecuenta\"><input type='text' id='idnombrecuenta' disabled /></td>";
$comp	.= "<td class=\"cargos\"><input type='number' id='idcargo' value='0' onfocus='this.select()' onchange='setFMonto(this)' onfocus='setFMonto(this)' /></td>";
$comp	.= "<td class=\"abonos\"><input type='number' id='idabono' value='0' onfocus='this.select()' onchange='setFMonto(this)' onfocus='setFMonto(this)' /></td>";
$comp	.= "<td class=\"referencia\"><input type='text' id='idreferencia' onfocus='this.select()' /></td>";
$comp	.= "<td class=\"concepto\"><input type='text' id='idconcepto' onfocus='this.select()' onblur='jsSaveMvto()' /></td></table></fieldset>";
$xFRM->addDivSolo($comp, "", "tx34", "txt14", array( 1 => array("id" => "idagregados")));

//$xFRM->addDivSolo(", "", "tx34", "txt14", array( 1 => array("id" => "idlistado")));
$xFRM->addHElem("<fieldset class='mvtoscontables'><legend>" . $xFRM->lang("Movimientos") . "</legend><div id='idlistado'></div></fieldset>");

$xFRM->OButton("TR.Eliminar", "jsEliminarPoliza()", $xFRM->ic()->ELIMINAR);
$xFRM->OButton("TR.Imprimir", "jsImprimirPoliza()", "imprimir");
$xFRM->OButton("TR.Guardar", "jsFinalizarPoliza()", "guardar");
$xFRM->addAviso($poliza);
$xFRM->addFootElement("<input type='hidden' id='idpoliza' value='$poliza' />");
$xFRM->addFootElement("<datalist id='listadocuentas'></datalist>");

$xFRM->addRefrescar("jsaGetListadoDeMovimientos()");
$xFRM->addCerrar();
echo $xFRM->get();

?>
<script>
var xCont	= new ContGen();
var xG		= new Gen();
function setFMonto(obj){
	obj.select();
	var oMod	= null;
	if(obj.id == "idcargo"){ oMod = $("#idabono"); } else { oMod = $("#idcargo"); }	
	if(obj.value > 0){ oMod.val(0); 	$("#idreferencia").focus();	}	
}
function jsImprimirPoliza(){ var xCont	= new ContGen(); xCont.ImprimirPoliza($("#idpoliza").val() );}
function jsSaveMvto(){
	jsaGuardarMovimiento();
	setTimeout("jsaGetListadoDeMovimientos()", 500);
	setTimeout("jsClearMovimientos()", 1000);
}
function jsClearMovimientos(){
	$("#idnombrecuenta").val('');
	$("#idabono").val(0);
	$("#idcargo").val(0);
	$("#idcuenta").focus();	
}
function initComponents(){	jsaGetListadoDeMovimientos(); }
function jsFinalizarPoliza(){
	//alert($("#2014_7_1_1_8").html());
	var tcargos		= redondear($("#idsumacargos").val(), 2);
	var tabonos		= redondear($("#idsumaabonos").val(), 2);
	if(tcargos != tabonos){
		alert("Poliza descuadrada!");
	} else {
		jsaSetFinalizarPoliza();
	}
}
function jsKeyAction(evt, ctrl){
    evt=(evt) ? evt:event;
    var charCode = (evt.charCode) ? evt.charCode : ((evt.which) ? evt.which : evt.keyCode);
	var cta	= String(ctrl.value);
	if ((charCode >= 48 && charCode <= 57)||(charCode >= 96 && charCode <= 105)) {
		if (cta.length > 2) { jsaGetCuentas();	}
	}
}
function jsEditarMvto(idx){
	xCont.setEditarMovimiento({clave:idx, callback: jsaGetListadoDeMovimientos});
}
function jsEliminarPoliza(){
	xG.confirmar({msg: "Desea Eliminar la Poliza?", callback: "jsaEliminarPoliza()"});
}
</script>
<?php 
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>