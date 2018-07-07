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
$xHP		= new cHPage("TR.GARANTIAS", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc 		= new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto		= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT);
$recibo		= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa	= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo		= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria = parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);

$observaciones= parametro("idobservaciones");
$xHP->addJTableSupport();
$xHP->init();



$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
if($credito<= DEFAULT_CREDITO){
	$xFRM->addCreditBasico();	
} else {
	
	$xCred	= new cCredito($credito);
	if($xCred->init() == true){
		$xFRM->addHElem($xCred->getFichaMini());
	}
	
	$sql = $xLi->getListadoDeGarantiasReales("", $credito);
	
	$xFRM->addCerrar();
	$xFRM->OHidden("credito", $credito);
	$xFRM->OButton("TR.AGREGAR VEHICULO", "jsAddAuto()", $xFRM->ic()->VEHICULO);
	$xFRM->OButton("TR.AGREGAR INMUEBLE", "jsAddInmueble()", $xFRM->ic()->EDIFICIO);
	/* ===========		GRID JS		============*/
	
	$xHG	= new cHGrid("iddivgarantias",$xHP->getTitle());
	
	$xHG->setSQL($sql);
	$xHG->addList();
	$xHG->addKey("clave");

	$xHG->col("tipo", "TR.TIPO", "10%");
	$xHG->col("recibido", "TR.FECHA", "10%");
	$xHG->col("estado", "TR.ESTATUS", "10%");
	$xHG->col("valuacion", "TR.VALUADO", "10%");
	$xHG->col("propietario", "TR.PROPIETARIO", "10%");
	$xHG->col("valor", "TR.VALOR", "10%");
	
//	$xHG->OToolbar("TR.AGREGAR VEHICULO", "jsAddAuto()", "grid/car.png");
//	$xHG->OToolbar("TR.AGREGAR INMUEBLE", "jsAddInmueble()", "grid/building.png");
	if($xCred->getEsCreditoYaAfectado() == false){
		
		if($xCred->getEsAutorizado()){
			$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.clave +')", "edit.png");
			$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.clave +')", "delete.png");
		}
		if($xCred->getEsAfectable() == true OR $xCred->getEsAutorizado() == true){
			//$xHG->OButton("TR.RESGUARDO", "jsGoToResguardo('+ data.record.clave +')", "archive.png");
		}
		//$xHG->OButton("TR.DEVOLUCION", "jsGoToDevolucion('+ data.record.clave +')", "go-back-arrow.png");
	} else {
		if($xCred->getEsPagado() == true){
			//$xHG->OButton("TR.DEVOLUCION", "jsGoToDevolucion('+ data.record.clave +')", "go-back-arrown.png");
		}
	}
	$xHG->OButton("TR.PANEL", "jsGoToPanel('+ data.record.clave +')", "web.png");
	
	$xFRM->addHElem("<div id='iddivgarantias'></div>");
	
	$xFRM->addJsCode( $xHG->getJs(true) );
	
	echo $xFRM->get();
	
	?>
	<script>
	var xG	= new Gen();
	function jsEdit(id){
		xG.w({url:"../frmcreditos/creditos-garantias.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivgarantias});
	}
	function jsAddAuto(){
		var idcredito = $("#credito").val();
		xG.w({url:"../frmcreditos/creditos-garantias.new.frm.php?tipo=2&credito=" +idcredito, tiny:true, callback: jsLGiddivgarantias});
	}
	function jsAddInmueble(){
		var idcredito = $("#credito").val();
		xG.w({url:"../frmcreditos/creditos-garantias.new.frm.php?tipo=1&credito=" +idcredito, tiny:true, callback: jsLGiddivgarantias});
	}	
	function jsDel(id){
		xG.rmRecord({tabla:"creditos_garantias", id:id, callback:jsLGiddivgarantias});
	}
	function jsGoToPanel(id){
		var idcredito = $("#credito").val();
		xG.w({url:"../frmcreditos/creditos-garantias.panel.frm.php?credito=" + idcredito + "&clave=" + id, tab:true, callback: jsLGiddivgarantias});
	}
	function jsGoToResguardo(id){
		var idcredito = $("#credito").val();
		xG.w({url:"../frmcreditos/frmresguardogarantias.php?credito=" +idcredito + "&clave=" + id, tiny:true, callback: jsLGiddivgarantias});
	}
	function jsGoToDevolucion(id){
		var idcredito = $("#credito").val();
		xG.w({url:"../frmcreditos/frmdevgarantiaresguardo.php?credito=" +idcredito + "&clave=" + id, tiny:true, callback: jsLGiddivgarantias});
	}	
	</script>
	<?php
	
}


//$jxc ->drawJavaScript(false, true);
$xHP->fin();

?>