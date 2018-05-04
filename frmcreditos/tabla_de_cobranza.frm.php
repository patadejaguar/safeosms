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
$xHP		= new cHPage("TR.Panel de Nomina", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$jxc 		= new TinyAjax();
$xUser		= new cSystemUser();
$xChk		= new cHCheckBox();
$xTxt		= new cHText();

function jsaEliminarRecibo($idrecibo, $periodo, $idnomina){
	$xRec	= new cReciboDeOperacion(false, false, $idrecibo);
	$xLog	= new cCoreLog();
	if($xRec->init() == true){
		$credito	= $xRec->getCodigoDeDocumento();
		$xRec->setRevertir(true);
		$xPer		= new cEmpresasCobranzaPeriodos($idnomina);
		if($xPer->init() == false){
			$xLog->add("ERROR\tAl Cargar el Periodo $idnomina\r\n", $xLog->DEVELOPER);
		} else {
			if($xPer->setRevertirOperacion($credito, $periodo)== false){
				$xLog->add("ERROR\tError al cancelar la Operacion del credito $credito y periodo $periodo\r\n", $xLog->DEVELOPER);
			}
		}
		$xLog->add($xPer->getMessages(), $xLog->DEVELOPER);
		$xLog->add($xRec->getMessages(), $xLog->DEVELOPER);
	}
	return $xLog->getMessages(OUT_HTML);
}
function jsaEliminarNomina($clave){
	$xPer	= new cEmpresasCobranzaPeriodos($clave);
	if($xPer->init() == true){
		$xPer->setEliminar();
	}
	return $xPer->getMessages();
}
function jsaCancelarNomina($clave){
	$xPer	= new cEmpresasCobranzaPeriodos($clave);
	if($xPer->init() == true){
		$xPer->setCerrar();
	}
	return $xPer->getMessages();
}

$jxc ->exportFunction('jsaEliminarRecibo', array('idkey', 'idkey2', 'idnomina'), "#idavisos");
$jxc ->exportFunction('jsaEliminarNomina', array('idnomina'), "#idavisos");
$jxc ->exportFunction('jsaCancelarNomina', array('idnomina'), "#idavisos");

$jxc ->process();

$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); 
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$observaciones	= parametro("idobservaciones");
$idnomina		= parametro("nomina", 0, MQL_INT);
$xHP->init();

$xFRM			= new cHForm("frmcbza", "./");

$sql			= $xLi->getListadoDeCobranza($idnomina, SYS_TODAS, ", `empresas_cobranza`.`idempresas_cobranza` AS `clave`, `empresas_cobranza`.`recibo`, getBooleanMX(`empresas_cobranza`.`estado`) AS `estatusactivo`");
$xFRM->setTitle($xHP->getTitle() . " # $idnomina");
$xFRM->OHidden("idnomina", $idnomina);
//exit($sql);
$xT	= new cTabla($sql,2);
$xT->setWithMetaData();
$xTxt->setDivClass("tx4");

$xFRM->OButton("TR.CEDULA DE COBRANZA", "var xE=new EmpGen();xE.getOrdenDeCobranza($idnomina)", $xFRM->ic()->REPORTE);
$xFRM->OButton("TR.RECIBOS", "var xE=new EmpGen();xE.getPreRecibosDeCobranza($idnomina)", $xFRM->ic()->REPORTE);


$xT->setKeyField("numero_solicitud");
$xT->setKeyTable("creditos_solicitud");
$xT->setEventKey("var xC=new CredGen();xC.goToPanelControl");
$xChk->addEvent("jsAddCola(this, "  . HP_REPLACE_ID . ")", "onchange");
$xChk->setDivClass("");

//$xT->setFieldReplace("monto", "_X_MONTO");
//$xT->addEspTool($xTxt->getDeMoneda("mny_" . HP_REPLACE_ID, "", "_X_MONTO"));

$xT->addEspTool( $xChk->get("", "chk-" . HP_REPLACE_ID) );
//$xT->OButton("TR.Eliminar Recibo", "jsEliminarRecibo(" . HP_REPLACE_ID . ")", $xFRM->ic()->ELIMINAR);
$xT->setTdClassByType();
$xT->setFootSum(array(
		3 => "letra",
		5 => "monto"
));
$xT->setOmitidos("saldo_final");

$xFRM->OHidden("idkey", 0);
$xFRM->OHidden("idkey2", 0);

//$xT->addEditar();
//$xT->setWidthTool("200px");
$xT->setResumidos("observaciones");

$xFRM->OButton("TR.Eliminar Nomina", "jsEliminarNomina()", $xFRM->ic()->ELIMINAR, "idcmdeliminarnomina", "red");
$xFRM->OButton("TR.Cancelar Nomina", "jsCancelarNomina()", $xFRM->ic()->PARAR, "idcmdcancelarnomina", "orange");

$xT->OInput("monto", "number", "mny", "style='max-width:6em;'");

$xT->setOmitidos("saldo_inicial");

$xT->OButton("TR.Editar", "jsEditOperacion("  . HP_REPLACE_ID . ")", $xFRM->ic()->EDITAR);
$xT->OButton("TR.Imprimir Recibo", "jsImprimirRecibo("  . HP_REPLACE_ID . ")", $xFRM->ic()->IMPRIMIR);
$xT->OButton("TR.ELIMINAR OPERACION", "jsEliminarOperacion(" . HP_REPLACE_ID . ")", $xFRM->ic()->ELIMINAR);
$xT->OButton("TR.COBRO LIBRE", "jsSetCobroLibre(" .  HP_REPLACE_ID . ")", $xFRM->ic()->DINERO);

if($xUser->getPuedeEditarRecibos() == true){
	$xT->OButton("TR.Editar Recibo", "jsGoPanelRecibo(" . HP_REPLACE_ID . ")", $xFRM->ic()->RECIBO, "idcmdeditrecs");
}


$xT->OButton("TR.ENVIOS", "jsListEnvios(" .  HP_REPLACE_ID . ")", "fa-wifi");

$xPer	= new cEmpresasCobranzaPeriodos($idnomina);
if($xPer->init() == true){
	$xEmp	= new cEmpresas($xPer->getClaveDeEmpresa());
	$xFMT			= new cFormato( $xEmp->getIDDeFormatoDeAviso() );
	$xFMT->setEmpresaPeriodo($xPer->getClaveDeEmpresa(), $idnomina);
	$xFMT->setProcesarVars();
	$xFRM->addHElem( $xFMT->get() );
}




$xFRM->addHTML( $xT->Show() );

if($xUser->getPuedeEliminarRecibos() == true){
	$xFRM->OButton("TR.Eliminar Recibo", "jsEliminarRecibos()", $xFRM->ic()->REGISTROS, "idcmdeliminarrecs", "orange");
}

$xFRM->addHTML("<div id='dgl2'><ol id='oldgl2'></ol></div>");
//$xFRM->addSubmit();
//$xFRM->addFooterBar("<br />");
$xFRM->addAviso("", "idavisos");
echo $xFRM->get();
?>
<script>
var xG		= new Gen();
var xRec	= new RecGen();
var xCred	= new CredGen();
var cola	= {};
function jsAddCola(osrc, id){
	var obj		= processMetaData("#tr-creditos_solicitud-" + id);
	if (osrc.checked == true) {
		if(typeof obj.recibo != "undefined"){
			var idrecibo	= entero(obj.recibo);
			var idletra		= entero(obj.letra);
			var idcredito	= entero(obj.credito);
	    	if(idrecibo > 0){
		    	//$("#idkey").val(idrecibo);
		    	xG.alerta({msg : "Se Agrega el Recibo " + idrecibo, type : "warn"});
		    	cola[id] = { recibo : idrecibo, letra : idletra, credito : idcredito };
	    	} else {
	    		xG.alerta({msg : "No existe el Recibo " + idrecibo + " en el Credito " + id});
	    	}
		}
	} else {
		//eliminar de la matriz
		if(typeof cola[id] != "undefined"){
			delete cola[id];
			xG.alerta({msg : "Eliminar el recibo del Credito " + id, type : "ok"});
		}
	}
}
function jsSetCobroLibre(idcred){
	var xmonto	= $("#mny-" + idcred).val();
	xCred.goToCobroMasivoDeCredito({credito:idcred, monto: xmonto});
}
function jsImprimirRecibo(id){
	var obj			= processMetaData("#tr-creditos_solicitud-" + id);
	var idrecibo	= obj.recibo;
	if(idrecibo >0){
		xRec.formatoNT(idrecibo);
	} else {
		xG.alerta({msg: "Recibo Invalido", tipo: "error"});
	}
}
function jsEliminarRecibos(){
	xG.confirmar({msg: "Confirma Eliminar estos recibos? no existe como recuperarlos.", callback : jsEliminarRecibosConfirmado} );
}
function jsEliminarNomina(){
	xG.confirmar({msg: "Confirma Eliminar la Nomina? no existe como recuperarlos.", callback : jsaEliminarNomina} );
}
function jsCancelarNomina(){
	xG.confirmar({msg: "Confirma Cancelar la Nomina? Los pagos activos se cancelaran.", callback : jsaCancelarNomina} );
}
function jsEliminarRecibosConfirmado(){
	var idnomina	= $("#idnomina").val();
	var xRec		= new RecGen();
	for(xcred in cola){
		var obj		= cola[xcred];

		xG.spinInit();
		xRec.eliminar({ preguntar : false, recibo : obj.recibo, letra : obj.letra, nomina : idnomina, callback : markItem });
	}
}
function markItem(id){
	xG.spinEnd();
	$("#tr-" + id).removeClass(); 
	$("#tr-" + id).addClass("tr-pagar");
	
}
function jsEditOperacion(id){
	var obj		= processMetaData("#tr-creditos_solicitud-" + id);
	if(typeof obj.observaciones != "undefined"){
		var clave = obj.clave;
		xG.w({url:"../utils/frm8db7028bdcdf054882ab54f644a9d36b.php?tabla=empresas_cobranza&clave="+clave,tiny:true});
	}
	//http://localhost/20100352&tinybox=true
}
function jsEliminarOperacion(id){
	var obj		= processMetaData("#tr-creditos_solicitud-" + id);
	var clave	= obj.clave;
	xG.rmRecord({tabla: "empresas_cobranza", id:clave});
}
function jsGoPanelRecibo(id){
	var obj			= processMetaData("#tr-creditos_solicitud-" + id);
	var idrecibo	= obj.recibo;
	var idcredito	= entero(obj.credito);
	var xmonto		= $("#mny-" + idcredito).val();
	if(idrecibo >0){
		xRec.panel(idrecibo, {agregar:xmonto});
	} else {
		xG.alerta({msg: "Recibo Invalido", tipo: "error"});
	}

}
function jsListEnvios(id){
	var obj			= processMetaData("#tr-creditos_solicitud-" + id);
	var idletra		= entero(obj.letra);
	var idcredito	= entero(obj.credito);
	
	xG.QList({url : "../svc/nom-letras-envios.svc.php?credito=" + idcredito + "&letra=" + idletra, id:"oldgl2",key:"id", label: "descripcion", callback: jsShowEnvios});
}
function jsShowEnvios(){
	xG.winTip({content: $("#dlg")});
}
</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>