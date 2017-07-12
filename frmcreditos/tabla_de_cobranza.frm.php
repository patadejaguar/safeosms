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


$jxc ->exportFunction('jsaEliminarRecibo', array('idkey', 'idkey2', 'idnomina'), "#fb_frmcbza");
$jxc ->exportFunction('jsaEliminarNomina', array('idnomina'), "#fb_frmcbza");
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
$xChk			= new cHCheckBox();
$sql			= $xLi->getListadoDeCobranza($idnomina, SYS_TODAS, ", `empresas_cobranza`.`idempresas_cobranza` AS `clave`, `empresas_cobranza`.`recibo`");
$xFRM->setTitle($xHP->getTitle() . " # $idnomina");
$xFRM->OHidden("idnomina", $idnomina);
//exit($sql);
$xT	= new cTabla($sql);
$xT->setWithMetaData();
/*$xT->setRowCSS("monto", "mnyres");
$xT->setColTitle("monto", "Monto de Retencion");*/



$xT->setKeyField("numero_solicitud");
$xT->setKeyTable("creditos_solicitud");
//$xT->setEventKey("")
$xChk->addEvent("jsAddCola(this, "  . HP_REPLACE_ID . ")", "onchange");
$xT->addEspTool( $xChk->get("", "chk-" . HP_REPLACE_ID) );
//$xT->OButton("TR.Eliminar Recibo", "jsEliminarRecibo(" . HP_REPLACE_ID . ")", $xFRM->ic()->ELIMINAR);
$xT->setTdClassByType();
$xT->setFootSum(array(
		3 => "letra",
		6 => "monto"
));
$xFRM->OHidden("idkey", 0);
$xFRM->OHidden("idkey2", 0);

//$xT->addEditar();
$xT->setWidthTool(100);

$xFRM->OButton("TR.Eliminar Nomina", "jsEliminarNomina()", $xFRM->ic()->ELIMINAR);

$xT->OButton("TR.Editar", "jsEditOperacion("  . HP_REPLACE_ID . ")", $xFRM->ic()->EDITAR);
$xT->OButton("TR.Imprimir Recibo", "jsImprimirRecibo("  . HP_REPLACE_ID . ")", $xFRM->ic()->RECIBO);
$xT->OButton("TR.ELIMINAR OPERACION", "jsEliminarOperacion(" . HP_REPLACE_ID . ")", $xFRM->ic()->ELIMINAR);

$xFRM->addHTML( $xT->Show() );
if($xUser->getPuedeEliminarRecibos() == true){
	$xFRM->OButton("TR.Eliminar Recibo", "jsEliminarRecibos()", $xFRM->ic()->REGISTROS);
}

//$xFRM->addSubmit();
$xFRM->addFooterBar("<br />");
echo $xFRM->get();
?>
<script>
var xG		= new Gen();
var xRec	= new RecGen();
var cola	= {};
function jsAddCola(osrc, id){
	var obj		= processMetaData("#tr-creditos_solicitud-" + id);
	if (osrc.checked == true) {
		if(typeof obj.observaciones != "undefined"){
			if(String(obj.observaciones).indexOf("]") > 0){
				var DPago	= new String(obj.observaciones).split("]");
				//[361295](3887.7)L.5:52 [2015-02-18]
		    	var idrecibo= entero(DPago[0]);
		    	var idxp	= String(DPago[1]).split("L.");
		    	idxp		= String(idxp[1]).split(":");
		    	var idletra	= entero(idxp[0]);
		    	//$("#idkey2").val();
		    	if(idrecibo > 0){
			    	//$("#idkey").val(idrecibo);
			    	xG.alerta({msg : "Se Agrega el Recibo " + idrecibo, type : "warn"});
			    	cola[id] = { recibo : idrecibo, letra : idletra };
		    	} else {
		    		xG.alerta({msg : "No existe el Recibo " + idrecibo + " en el Credito " + id});
		    	}
			} else {
				xG.alerta({msg : "La Parcialidad no tiene Recibo Masivo"});
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
function jsEliminarRecibosConfirmado(){
	var idnomina	= $("#idnomina").val();
	var xRec		= new RecGen();
	for(xcred in cola){
		var obj	= cola[xcred];
		xRec.eliminar({ preguntar : false, recibo : obj.recibo, letra : obj.letra, nomina : idnomina, callback : markItem });
	}
}
function markItem(id){ $("#tr-" + id).removeClass(); $("#tr-" + id).addClass("tr-pagar"); }
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
</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>