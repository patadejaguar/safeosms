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
$xHP		= new cHPage("TR.Resguardo de Garantias", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$jxc 		= new TinyAjax();
function jsaGetListaDeGarantias($idcredito){
	if($idcredito > DEFAULT_CREDITO){
		$xLi		= new cSQLListas();
		$sql_final = $xLi->getListadoDeGarantiasReales("", $idcredito, false, CREDITO_GARANTIA_ESTADO_PRESENTADO);
		$myTab 		= new cTabla($sql_final);
		$myTab->setEventKey("setToGoGuardar");
		$myTab->setKeyField("idcreditos_garantias");
		return $myTab->Show();	
	}
}
$jxc ->exportFunction('jsaGetListaDeGarantias', array('idsolicitud'), "#idlistado");
$jxc ->process();

$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$clave			= parametro("id", 0, MQL_INT); $clave = parametro("clave", $clave, MQL_INT);
$xHP->init();
$observaciones	= parametro("idobservaciones");

$xFRM			= new cHForm("frmcredsresgtias", "frmresguardogarantias.php");
$msg			= "";
$xFRM->setTitle($xHP->getTitle());
//$xFRM->addJsBasico();
if($clave <= 0){
	$xFRM->addCreditBasico();
	$xFRM->OButton("TR.Obtener Garantias", "jsaGetListaDeGarantias()", $xFRM->ic()->EJECUTAR);
	$xFRM->addHTML("<div id='idlistado'></div>");
	$xFRM->OHidden("clave", 0);
	$xFRM->setAction("frmresguardogarantias.php?action=" . MQL_MOD);
	$xFRM->addCerrar();
} else {
	//$xFRM->addAtras();
	$xGar	= new cCreditosGarantias($clave);
	if($xGar->init() == true){
		$xFRM->addHElem( $xGar->getFicha() );
		$xFRM->OHidden("clave", $clave);
		if($action == MQL_MOD){
			if($xGar->setEstatus(CREDITO_GARANTIA_ESTADO_RESGUARDADO, $fecha, $observaciones) == true){
				$xFRM->addAvisoRegistroOK();
				$xFRM->OButton("TR.Imprimir Acuse", "getRecibo()", $xFRM->ic()->IMPRIMIR);
			} else {
				$xFRM->addAvisoRegistroError();
			}
			$xFRM->addLog($xGar->getMessages());
			$xFRM->addCerrar();
		} else {
			$xFRM->ODate("idfechaactual", $fecha, "TR.Fecha de resguardo");
			$xFRM->addObservaciones();
			$xFRM->setAction("frmresguardogarantias.php?action=" . MQL_MOD);
			$xFRM->addGuardar();
		}
	}
	//
	
}
//$xFRM->addSubmit();
echo $xFRM->get();
?>
<script>
	var xG	= new Gen(); 
	var idG	= "<?php echo $clave; ?>";
	function setToGoGuardar(id){
		$("#clave").val(id);
		xG.confirmar({
			msg : "Desea Guardar el Resguardo?",
			callback : setSave
			});
		
	}
	function setSave(){
		$("#id-frm").submit();
	}
	function getRecibo(){
		//xG.w({ url : "../rpt_formatos/entrega_de_garantias.rpt.php?clave=" + idG, fullscreen: true});
		xG.w({ url : "../rpt_formatos/rptreciboresguardo.php?clave=" + idG, fullscreen: true});
	}
</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>