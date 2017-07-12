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
$xHP		= new cHPage("TR.Otros Ingresos");
$xCaja		= new cCaja();
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
if( $xCaja->getEstatus() == TESORERIA_CAJA_CERRADA ){	header ("location:../404.php?i=200"); }

function jsaDatosOperacion($idoperacion){
	$xOps	= new cTipoDeOperacion($idoperacion);
	$tasa	= TASA_IVA;
	$precio	= 0;
	if($xOps->init() == true){
		$tasa	= $xOps->getTasaIVA();
		$precio	= $xOps->getPrecio();
	}
	$tab 		= new TinyAjaxBehavior();
	$tab -> add(TabSetvalue::getBehavior('idmonto', $precio));
	$tab -> add(TabSetvalue::getBehavior('idtasaiva', $tasa));
	return $tab -> getString();
}


$jxc = new TinyAjax();
$jxc ->exportFunction('jsaDatosOperacion', array('idtipodeoperacion'));

$jxc ->process();

$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
//$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
//$tipo_operacion	= parametro("idtipodeoperacion", 0, MQL_INT);
$observaciones	= parametro("idobservaciones");	
$cheque			= parametro("cheque");
$foliofiscal	= parametro("foliofiscal");
$comopago		= parametro("ctipo_pago", SYS_NINGUNO, MQL_RAW);
$montoiva		= parametro("idiva", 0, MQL_FLOAT);
$xHP->init();
$xFRM			= new cHForm("frmingresos", "frmingresosvarios.php?action=" . MQL_ADD);
$xFRM->setTitle($xHP->getTitle());

$xSel			= new cHSelect();
$xTxt			= new cHText();
$xBtn			= new cHButton();
$xNot			= new cHNotif();
$LstCon			= "";
$xTxt->setDivClass("txmon");
$xTxt->addEvent("jsUpdateSumas()", "onblur");
$xSLT			= $xSel->getListaDeTiposDeOperacion("", false, false, RECIBOS_TIPO_OINGRESOS, true);
$msg			= "";

if($action == MQL_ADD){
	$credito	= ($credito<= DEFAULT_CREDITO) ? DEFAULT_CREDITO: $credito;
	$xSLT->show();
	$arrConcep	= $xSLT->getListaKeys();
	$cRec 		= new cReciboDeOperacion(RECIBOS_TIPO_OINGRESOS, false);
	$sumas		= 0;
	//$cRec->setGenerarBancos();
	$cRec->setGenerarPoliza();
	$cRec->setGenerarTesoreria();
	
	$idrecibo			= $cRec->setNuevoRecibo($persona, $credito, $fecha, 1, RECIBOS_TIPO_OINGRESOS, $observaciones, $cheque, $comopago, $foliofiscal);
	foreach ($arrConcep as $idx => $cpx){
		//setLog(" $idx => $cpx ");
		$monto			= parametro("id-$cpx", 0, MQL_FLOAT);
		if($monto>0){
			$tipo		= $cpx;
			$cRec->setNuevoMvto($fecha, $monto, $tipo, 1, $observaciones, 1, TM_ABONO, $persona);
			$sumas 		+= $monto;
		}
	}
	if($montoiva>0){
		$cRec->setNuevoMvto($fecha, $montoiva, OPERACION_CLAVE_PAGO_IVA_OTROS, 1, $observaciones, 1, TM_ABONO, $persona);
	}
	$total	= $sumas + $montoiva;
	$cRec->addMvtoContableByTipoDePago($total, TM_CARGO);
	
	
	$cRec->setFinalizarRecibo(true);
	
	$xRec	= new cReciboDeOperacion(false, false, $idrecibo);
	if($xRec->init() == true){
	
		$xFRM->addHElem($cRec->getFichaSocio());
		$xFRM->addHElem($cRec->getFicha(true));
		
		$xFRM->addPrintRecibo();
		$xFRM->addJsCode( $cRec->getJsPrint() );
		$xFRM->addJsInit("jsImprimirRecibo();");
		
		$xFRM->addCerrar();
		$xFRM->addAvisoRegistroOK();
	}
} else {
//$xFRM->addJsBasico();
	$xFRM->setNoAcordion();
	$xFRM->addCreditBasico();
	
	$xSLT->addEvent("onblur", "jsaDatosOperacion()");
	$xFRM->addFecha();
	
	$xFRM->addCobroBasico();
	$xChk	= new cHCheckBox();
	$xChk->addEvent("jsUpdateSumas()","onclick");
	
	
	$xFRM->addObservaciones();
	

	
	$xFRM->OHidden("idtasaiva", TASA_IVA);
	$xFRM->addSeccion("idagregarop", "TR.AGREGAR OPERACION");

	$xT			= new cHTabla("idtconceptos");
	$xT->initRow();
	$xT->addTH("TR.CONCEPTO");
	$xT->addTH("TR.MONTO");
	$xT->addTH("TR.Acciones");
	$xT->endRow();
	$xT->initRow();
	$xSLT->setLabel("");
	$xT->addTD($xSLT->get());
	$xT->addTD($xTxt->getDeMoneda("idmonto", ""));
	$xT->addTD($xBtn->getBasic("TR.AGREGAR", "jsAddNewConcepto()", $xFRM->ic()->AGREGAR, "idcmdadd"));
	$xT->endRow();
	//$xT->initRow();
	//$xT->addFootTD(".");
	$xT->addFootTD($xTxt->getLabel("TR.IVA"));
	$xT->addFootTD("0", " id='idsumaiva' class='mny' ");
	$xT->addFootTD(".", "", true);
	
	$xFRM->OHidden("idiva", 0);
	$xFRM->OHidden("idtotal", 0);
	
	
	$xT->addFootTD($xTxt->getLabel("TR.TOTAL"));
	$xT->addFootTD("0", " id='idsumatotal' class='mny' ");
	$xT->addFootTD(".", "");	
	//$xT->endRow();
	
	$xFRM->addHElem($xT->get());

	//$xFRM->addHElem("<div id='iddivtable'></div>");
	
	//$xFRM->addDivSolo( $xTxt->getDeMoneda("idiva", "TR.IVA") );
	//$xFRM->addDivSolo( $xTxt->getDeMoneda("idtotal", "TR.TOTAL") );
	
	$xFRM->addHElem($xChk->get("TR.OMITIR IVA", "idnoiva"));
	
	$xFRM->endSeccion();
	
	$xFRM->addAviso("", "idmsg");
	$xFRM->addGuardar();
	$xFRM->OButton("TR.NUEVA PERSONA", "jsAddNuevaPersona", $xFRM->ic()->AGREGAR);
	$LstCon			= implode(",", $xSLT->getListaKeys());
}
echo $xFRM->get();

?>
<script>
var xG		= new Gen();
var tw		= new TableW();
var idCon	= new Array(<?php echo $LstCon; ?>);
function jsAddNewConcepto(){
	var ixIva		= $("#idtasaiva").val();
	var ixMonto		= $("#idmonto").val();
	var ixConcepto	= $("#idtipodeoperacion").val();
	var ixText		= $("#idtipodeoperacion option:selected").text();

		
	if(!document.getElementById("id-" + ixConcepto)){
		
		HEl		= "<input type='hidden' value='"+ ixMonto  +"' name='id-" + ixConcepto + "' id='id-" + ixConcepto + "' />";
		HEl		+= "<input type='hidden' value='"+ ixIva  +"' name='id-iva-" + ixConcepto + "' id='id-iva-" + ixConcepto + "' />";
		tw.addRow({tableid:"#idtconceptos", vals:[ixText, getFMoney(ixMonto), HEl] });
		$("#idmonto").val(0);
		jsUpdateSumas();
	}
}
function jsUpdateSumas(){
	var niva	= $('#idnoiva').prop('checked');
	var mIva	= 0;
	var mTotal	= 0;
	var mBase	= 0;
	for(ik in idCon){
		var iCE	= idCon[ik];
		
		if(document.getElementById("id-"+iCE)){
			var vTIva	= (niva == true) ? 0: $("#id-iva-"+iCE).val();
			
			mIva 	+= flotante($("#id-"+iCE).val())*vTIva;
			mTotal += flotante($("#id-"+iCE).val());
		}
	}
	
	var siva	= mIva;
	$("#idiva").val( redondear(siva) );
	$("#idsumaiva").html(getFMoney(siva));
	var sm 	= flotante(mTotal)+flotante($("#idiva").val());
	$("#idtotal").val( sm );
	$("#idsumatotal").html( getFMoney(sm) );
}
function jsAddNuevaPersona(){
	xG.w({url:"../frmsocios/personas.registro-simple.frm.php?control=idsocio", tiny:true, w:800 ,h:400});
}
</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin();

?>