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
$xHP		= new cHPage("TR.Cerrar Caja", HP_FORM);

$msg		= "";
$jxc = new TinyAjax();

function jsaCerrarCaja($oficial, $pwd, $caja){
	$xBtn		= new cHButton();
	$xCaja		= new cCaja($caja);
	$cUsr		= new cSystemUser($oficial, false);
	$pwd 		= $cUsr->getHash($pwd, false);
	
	$sucess		= $cUsr->getCompareData("contrasenna", $pwd);
	$msg		= "";
	
	if($sucess == true){
		$IOficial	= $cUsr->getID();
		$xCaja->init($caja);
		
		if( $xCaja->setCloseBox($IOficial, 0) == true){
			$url	= $xCaja->getLinkDeCorte();
			$msg	= $xBtn->getBasic("TR.Imprimir Corte", "var xG = new Gen(); xG.w({url:'$url'});", "imprimir", "printcorte", false);
		} else {
			//TODO: Checar el problema con esto
			if(MODO_DEBUG == true){ setLog($xCaja->getMessages(OUT_TXT)); }
			$msg	= "<p class='warn'>CLAVE DE ERROR " . $err . "</p>";
		}
		
	} else {
		$msg	= "<p class='warn'>ERROR</p>";
	}
	return $msg;
}
function jsaEliminarArqueo($caja){ $xArq	= new cCajaArqueos($caja);$xArq->setEliminarArqueo(); }
function jsaGetResumenDeCaja($caja){ $xCaja		= new cCaja();	$xCaja->init($caja);	return $xCaja->getResumenDeCaja(); }
$jxc ->exportFunction('jsaCerrarCaja', array('oficial', 'password', "idcaja"), "#cajacerrada");
$jxc ->exportFunction('jsaGetResumenDeCaja', array("idcaja"), "#resumen");

$jxc ->exportFunction('jsaEliminarArqueo', array("idcaja"));

$jxc ->process();

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$fecha		= parametro("fecha", false, MQL_RAW);

$xHP->setIncludeJQueryUI();
$xHP->init();

$xFRM		= new cHForm("frmcerrar", "cerrar_caja.frm.php?action=1", "frmcerrar");
$xBtn		= new cHButton();
$xTxt		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();
$txtP		= new cHText();

$txtP->addEvent("var xG = new Gen(); xG.inputMD5(this);", "onchange");
$xFRM->addCerrar();

$xFRM->OButton("TR.Cerrar Caja", "jsCerrarCaja()", "bloquear", "cmdlock");
$xFRM->OButton("TR.Guardar Arqueo", "jsRegistrarArqueo()", $xFRM->ic()->GUARDAR, "cmdcerrar","whiteblue");

$xFRM->OButton("TR.Eliminar Arqueo", "jsEliminarArqueo()", $xFRM->ic()->ELIMINAR, "cmddel", "red");
$xFRM->addToolbar("<span id='cajacerrada'></span>");
 
$lsCajas	= $xSel->getListaDeCajasAbiertas("", "", $fecha);
$lsCajas->addEvent("onchange", "jsDatosDeCaja()");
$lsCajas->addEvent("onblur", "jsDatosDeCaja()");
$xFRM->addHElem( $lsCajas->get(true) );

$xTxt->addEvent("jsDatosDeCaja()", "onfocus");
$xTxt->setDiv13();

$xFRM->addHElem( $xTxt->getNormal("oficial", "", "TR.JEFE_DE_CAJA") );
$xFRM->addHElem( $txtP->getPassword("password", "TR.Password", "") );

$xTxM		= new cHText();
$xDiv		= new cHDiv("txm");
$xTab		= new cHTabs();
$xTxM->addEvent("jsActualizarMonedas", "onchange");
//$xTxM->setDiv13();

$xDiv->addHElem("<h3>" . $xFRM->lang("Valores") . "<id id='totalmonedas'></i></h3>");
$xDiv->addHElem( $xTxM->getDeMoneda("mone-10-cents", "TR.Monedas de 10 centimos", 0) );
$xDiv->addHElem( $xTxM->getDeMoneda("mone-20-cents", "TR.Monedas de 20 centimos", 0) );
$xDiv->addHElem( $xTxM->getDeMoneda("mone-50-cents", "TR.Monedas de 50 centimos", 0) );
$xDiv->addHElem( $xTxM->getDeMoneda("mone-1", "TR.Monedas de 1", 0) );
$xDiv->addHElem( $xTxM->getDeMoneda("mone-2", "TR.Monedas de 2", 0) );
$xDiv->addHElem( $xTxM->getDeMoneda("mone-5", "TR.Monedas de 5", 0) );
$xDiv->addHElem( $xTxM->getDeMoneda("mone-10", "TR.Monedas de 10", 0) );
$xDiv->addHElem( $xTxM->getDeMoneda("bille-20", "TR.Billetes de 20", 0) );
$xDiv->addHElem( $xTxM->getDeMoneda("bille-50", "TR.Billetes de 50", 0) );
$xDiv->addHElem( $xTxM->getDeMoneda("bille-100", "TR.Billetes de 100", 0) );
$xDiv->addHElem( $xTxM->getDeMoneda("bille-200", "TR.Billetes de 200", 0) );
$xDiv->addHElem( $xTxM->getDeMoneda("bille-500", "TR.Billetes de 500", 0) );
$xDiv->addHElem( $xTxM->getDeMoneda("bille-1000", "TR.Billetes de 1000", 0) );

$xDiv->addHElem("<h3>" . $xFRM->lang("Documentos") . "<id id='totaldocumentos'></i></h3>");
$xHT	= new cHTabla();
$xTxD	= new cHText();
$xTxD->setDivClass("");

$xHT->initRow();
$xTxD->addEvent("jsActualizarDoctos", "onchange");
$xHT->addTH("#");
$xHT->addTH("TR.Documento");
$xHT->addTH("TR.Monto");
$xHT->addTH("TR.Observaciones");
$xHT->endRow();

$xHT->initRow();
$xTxD->addEvent("jsActualizarDoctos", "onchange");
$xHT->addTD("1");
$xHT->addTD($xTxD->getNormal("documento-01"));
$xHT->addTD($xTxD->getDeMoneda("idmontodoc-01") );
$xHT->addTD($xTxD->getDeObservaciones("idobserva-01"));
$xHT->endRow();

$xHT->initRow();
$xTxD->addEvent("jsActualizarDoctos", "onchange");
$xHT->addTD("2");
$xHT->addTD($xTxD->getNormal("documento-02"));
$xHT->addTD($xTxD->getDeMoneda("idmontodoc-02") );
$xHT->addTD($xTxD->getDeObservaciones("idobserva-02"));
$xHT->endRow();

$xHT->initRow();
$xTxD->addEvent("jsActualizarDoctos", "onchange");
$xHT->addTD("3");
$xHT->addTD($xTxD->getNormal("documento-03"));
$xHT->addTD($xTxD->getDeMoneda("idmontodoc-03") );
$xHT->addTD($xTxD->getDeObservaciones("idobserva-03"));
$xHT->endRow();


$xHT->initRow();
$xTxD->addEvent("jsActualizarDoctos", "onchange");
$xHT->addTD("4");
$xHT->addTD($xTxD->getNormal("documento-04"));
$xHT->addTD($xTxD->getDeMoneda("idmontodoc-04") );
$xHT->addTD($xTxD->getDeObservaciones("idobserva-04"));
$xHT->endRow();

$xDiv->addHElem( $xHT->get() );

$xDiv->addHElem("<h3>" . $xFRM->l()->getT("TR.Diferencia en Arqueo") . "<id id='diferenciacorte'></i></h3>");
//

$xTab->addTab("TR.Resumen", "<div id='resumen'></div>");
$xTab->addTab("TR.Efectivo", $xDiv->get());

$xFRM->OHidden("idsumavalores", 0, "");
$xFRM->OHidden("idsumadoctos", 0, "");

$xFRM->addHTML( $xTab->get()  );

$xFRM->addAviso($msg);
$xFRM->addJsInit("jsDatosDeCaja()");

echo $xFRM->get();
$jxc ->drawJavaScript(false, true);
?>
<script >
var xg			= new Gen();
var xG			= new Gen();
var sumaArq		= 0;
var ordenCbza	= {};
var sumaVals	= 0;
var sumaDocts	= 0;

function jsDatosDeCaja(){ jsaGetResumenDeCaja(); }
function jsEliminarArqueo(){ xG.confirmar({msg:"Desea Eliminar el Arqueo para esta caja?.", callback:jsEliminarCajaConfirmado}); }
function jsEliminarCajaConfirmado(){
	//xG.spinInit();
	jsaEliminarArqueo();
	var mf = function(){
		$('#frmcerrar').trigger("reset");
		jsDatosDeCaja();
		//xG.spinEnd();
	}
	setTimeout(mf,2000);
}
function jsRegistrarArqueo(){
	if($("#idsumacobros").length >0 ){
		var cobrado	= redondear($("#idsumacobros").val());
		var operado	= redondear($("#idsumaoperaciones").val());
		var arqueo	= redondear($("#idsumavalores").val()) + redondear($("#idsumadoctos").val());
		if(operado != (arqueo +  cobrado)){
			alert("Descuadre, no se cierra Caja, verifique sus Valores." + operado + "|A:" + arqueo + "|C:" + cobrado);
		} else {
			var sicon	= confirm("Desea guardar el Arqueo Actual?\nSe Eliminara lo Anterior.");
			if(sicon){
				jsaEliminarArqueo(); 
			}
		}
	} else {
		alert("No existe el arqueo a guardar!");
	}
	setTimeout("jsGuardarArqueo()", 2000);
}
function jsCerrarCaja(){
	var valoresops		= 0;
	var valorescobro	= 0;
	var success			= true;
	var mf 				= function(){ xG.spinEnd(); }
	var mWait			= 5500;
	if( $('#idsumaoperaciones').length > 0){
		valorescobro	= redondear($("#idsumacobros").val(),2);
		valoresops		= redondear($("#idsumaoperaciones").val(),2);
	} else {
		alert("Seleccione una Caja Antes de seguir!");
		success			= false;
	}
	if($.trim($("#oficial").val()) == ""||$.trim($("#oficial").val()) == ""){
		alert("Necesita Capturar el Usuario y Clave!");
		success			= false;		
	}
	if(valorescobro < valoresops){
		alert("Su Caja esta descuadrada!");
		success			= false;
	} 
	if(success == true){
		var sip	= confirm("CONFIRMA CERRAR LA CAJA CON VALORES DE " + valorescobro + "?");
		if(sip){
			xg.disTime("#cmdlock", mWait);
			setTimeout(mf,mWait);
			jsaCerrarCaja();
		}
	}
}
function jsActualizarMonedas(){
	var $inputs = $('#frmcerrar :input');
	var mSum	=  0;
    $inputs.each(function() {
        if ( String(this.name).indexOf("mone") !== -1||String(this.name).indexOf("bille") !== -1  ){
            var mEsq	= String(this.name).split("-");
            var mValor	= (String(this.name).indexOf("cents") !== -1) ? redondear((entero(mEsq[1])/100), 2) : entero(mEsq[1]);
            var mUnits	= this.value;
            var mTot	= redondear( (mUnits * mValor), 2);
            mSum 		= mSum + mTot;
            ordenCbza[this.name] = { valor : mValor, numero : mUnits, notas : "", documento : "" };
        }
    });	
    $("#idsumavalores").val(mSum);
    $("#totalmonedas").html( getInMoney(mSum) );
    jsActualizarTotal();
}
function jsActualizarDoctos(){
	var $inputs = $('#frmcerrar :input');
	var mSum	=  0;
    $inputs.each(function() {
        if ( String(this.name).indexOf("idmontodoc") !== -1){
            var mEsq	= String(this.name).split("-");
            var mValor	= redondear(this.value, 2);
            var pidm	= mEsq[1];
            
            var mNotas	= $("#idobserva-" + pidm).val();
            var mUnits	= 1;
            var mDocto	= $("#documento-" + pidm).val();
            
            ordenCbza[this.name] = { valor : mValor, numero : 1, notas : mNotas, documento : mDocto  };
            mSum 		= mSum + mValor; 
        }
    });
    $("#idsumadoctos").val(mSum);	
    $("#totaldocumentos").html( getInMoney(mSum) );
    jsActualizarTotal();
}
function jsActualizarTotal(){
	if($("#idsumacobros").length >0 ){
		var cobrado	= redondear($("#idsumacobros").val());
		var operado	= redondear($("#idsumaoperaciones").val());
		var arqueo	= redondear($("#idsumavalores").val()) + redondear($("#idsumadoctos").val());
		//console.log("Diferencia en caja" +  operado + "--" + arqueo + "---" + cobrado);
		$("#diferenciacorte").html( getInMoney( (operado - (arqueo +  cobrado)) ) );
	}
}
function jsGuardarArqueo(){
	var mCaja	= $("#idcaja").val();
	var cnt		= 1;
	var siz		= Object.keys(ordenCbza).length;
	//xG.spinInit();
  	for (var mObj in ordenCbza) {
		var itms	= ordenCbza[mObj];
	
	    var url	= "../svc/envioarqueo.svc.php?caja=" + mCaja + "&valor=" + itms.valor + "&numero=" + itms.numero + "&notas=" + itms.notas + "&documento=" + itms.documento;
	    
	    if(flotante(itms.valor) > 0 && flotante(itms.numero) > 0){
		    xg.pajax({
			url : url, result : "json",
				callback : function(data){
					try { data = JSON.parse(data); } catch (e){ }
					if (typeof data != "undefined"){
						$("#idmsgs").html(data.message);
						if(cnt >= siz){
							$('#frmcerrar').trigger("reset");
							//xG.spinEnd();
							jsDatosDeCaja();
						}
						cnt++;
					} else {
						xG.alerta({msg:"ERROR AL GUARDAR EL ARQUEO"})
					}
				}
			});
	    } else {
	    	setLog("Entrada ignorada");
	    }
  	}	
}
</script>
<?php 
$xHP->fin();
?>