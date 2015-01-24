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
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$jxc = new TinyAjax();

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

$jxc ->exportFunction('jsaSetGenerarPolizaPorRecibo', array('idclaveactual'), "#idmsgs");
$jxc ->process();

$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); 
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);


$xHP->init();

$xFRM		= new cHForm("frm", "./");
$msg		= "";
$FechaInit	= $xF->getDiaInicial();
$FechaFin	= $xF->getDiaFinal();
$otros		= " AND (`operaciones_recibostipo`.`tipo_poliza_generada` != 999) AND `operaciones_recibos`.`total_operacion` > 0 ";
$xTbl		= new cTabla($xLi->getListadoDeRecibos("", "", "", $FechaInit, $FechaFin, $otros));
//$xTbl->addEspTool();
$xTbl->OButton("TR.Poliza", "jsBuscarPoliza(". HP_REPLACE_ID .   ")", $xTbl->ODicIcons()->CONTABLE );
$xTbl->OButton("TR.Factura", "jsBuscarFactura(". HP_REPLACE_ID .   ")", $xTbl->ODicIcons()->REPORTE );
$xTbl->setEventKey("jsGetPanel");
//$table_s->addEspTool("\$xS=new cSocio(_REPLACE_ID_,true);\$D=\$xS->getTotalColocacionActual();PHP::(\$D[SYS_NUMERO]>0) ? \"<div class='noticon'><i class='fa fa-credit-card fa-lg'></i><span class='noticount'>\" . \$D[SYS_NUMERO] . \"</span></div>\":\"\";");
$xFRM->addHTML( $xTbl->Show() );

//$xFRM->addJsBasico();
//$xFRM->addCreditBasico();
//$xFRM->addSubmit();

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
function jsGenerarPoliza(){ xG.confirmar({msg: "La Poliza no existe. Desea Generarla?", callback : "jsaSetGenerarPolizaPorRecibo()" }); }
</script>
<?php
$xHP->fin();
?>