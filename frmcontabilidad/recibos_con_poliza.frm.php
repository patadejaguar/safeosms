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
$xHP		= new cHPage("TR.Contabilidad de Recibos", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$jxc 		= new TinyAjax();
$xFe		= new cHDate();

function jsaSetGenerarPolizaPorRecibo($numero){
	if ( setNoMenorQueCero($numero) > 0 ){
		//return $tab -> getString();
		$Recibo		= $numero;
		$msg		= "";
		$Regenerar	= true; //( strtoupper($id2) == "SI") ? true : false;
		$xUCont		= new cUtileriasParaContabilidad();
		$xBtn		= new cHButton();
			
		if ( $Regenerar == true ){
			$msg		.= $xUCont->setRegenerarPrepolizaContable(false, $Recibo);
		}
		$xUCont->setPolizaPorRecibo($Recibo);
		$idPol		= trim($xUCont->getIDPoliza());
		$xPolCW		= new cPolizaCompacW(0);
		$xPolCW->initByID($idPol);
		$xPolCW->setRun();
		$strDown	= $xPolCW->setExport();
		if(MODO_DEBUG == true){
			$xLog	= new cFileLog();
			$xLog->setWrite($xUCont->getMessages());
			$xLog->setClose();
			$strDown	.= $xLog->getLinkDownload("TR.Log");
		}
		//return $xBtn->getBasic("TR.Modificar Poliza","jsModificarPoliza('$idPol')", $xBtn->ic()->EDITAR, "cmdeditpoliza") . $strDown ;
	} else {
		return "NO HAY REGISTRO QUE GENERAR [$numero]";
	}
}
function jsaGetListaDeRecibos($idtipo, $fecha){
	$xF			= new cFecha();
	$xLi		= new cSQLListas();
	$FechaInit	= $xF->getFechaISO($fecha); //$xF->getDiaInicial();
	$FechaFin	= $xF->getFechaISO($fecha); //$xF->getDiaFinal();
	$otros		= " AND (`operaciones_recibostipo`.`tipo_poliza_generada` != " . FALLBACK_TIPO_DE_POLIZA . ") AND `operaciones_recibos`.`total_operacion` > 0 ";
	$xTbl		= new cTabla($xLi->getListadoDeRecibos($idtipo, "", "", $FechaInit, $FechaFin, $otros));
	$xTbl->setWithMetaData();
	
	$xTbl->OButton("TR.Poliza", "jsBuscarPoliza(". HP_REPLACE_ID .   ")", $xTbl->ODicIcons()->CONTABLE );
	$xTbl->OButton("TR.Factura", "jsBuscarFactura(". HP_REPLACE_ID .   ")", $xTbl->ODicIcons()->REPORTE );
	$xTbl->OButton("TR.Panel", "var xR=new RecGen(); xR.panel(". HP_REPLACE_ID .   ")", $xTbl->ODicIcons()->CONTROL );
	$xTbl->setFootSum(array(
			6 => "total"
	));
	$xTbl->setEventKey("jsGetPanel");
	return $xTbl->Show();
}

$jxc->exportFunction('jsaSetGenerarPolizaPorRecibo', array('idclaveactual'), "#idmsgs");
$jxc->exportFunction('jsaGetListaDeRecibos', array('idtipoderecibo', 'idfecha'), "#idlistado");
$jxc->process();

$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); 
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();

$xFRM->addCerrar();
$xFRM->OButton("TR.Obtener", "jsaGetListaDeRecibos()", $xFRM->ic()->CARGAR );
$xFRM->OButton("TR.Exportar Dia", "jsaExportarDia()", $xFRM->ic()->EXPORTAR );

$xFRM->setTitle($xHP->getTitle());
$xLisR		= $xSel->getListaDeTiposDeRecibosContabilizables();
$xLisR->addEspOption(SYS_TODAS, SYS_TODAS);
$xLisR->setOptionSelect(SYS_TODAS);
$xFRM->addDivSolo($xFe->get("TR.FECHA", false, "idfecha",false), $xLisR->get(false), "tx14", "tx34" );

$xFRM->addHTML("<div class='tx1' id='idlistado'></div>" );
$xFRM->OHidden("idclaveactual", 0, "");
$xFRM->addAviso("");

echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
?>
<script>
var xRec	= new RecGen();
var xG		= new Gen();
function jsBuscarPoliza(idrecibo){
	$("#idclaveactual").val(idrecibo);
	
	xRec.getExistePolizaContable({
		recibo : idrecibo,
		open : true,
		callback : "jsGenerarPoliza()"
		});
}
function jsBuscarFactura(idrecibo){
	
}
function jsGetPanel(id){ xRec.panel(id); }
function jsGenerarPoliza(){ xG.confirmar({msg: "La Poliza no existe. Desea Generarla?", callback : "jsSetGenerarPoliza()" }); }
function jsRebuscarPoliza(){
	var idx	= $("#idclaveactual").val();
	xG.spinEnd();
	jsBuscarPoliza(idx);
}
function jsSetGenerarPoliza(){
	var idx	= $("#idclaveactual").val();
	$("#tr-operaciones_recibos-" + idx).attr("class", "tr-pagar");
	jsaSetGenerarPolizaPorRecibo();
	xG.spinInit();
	setTimeout("jsRebuscarPoliza()",2500);
}
function jsaExportarDia(){
	var idF	= $("#idfecha").val();
	xG.w({url : "../frmcontabilidad/polizas-exportar-dia.frm.php?idfecha=" +  idF, tiny : true});
}
</script>
<?php
$xHP->fin();
?>