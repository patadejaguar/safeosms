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
$xHP		= new cHPage("TR.REGISTRO DATOS_EXTRANJEROS", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc = new TinyAjax();
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
$empresa	= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT);
$grupo		= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria = parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);

$observaciones= parametro("idobservaciones");

$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
//$xFRM->addJsBasico();
if($persona <= DEFAULT_SOCIO){
	$xFRM->addPersonaBasico();
	$xFRM->addSubmit();
} else {
	$xFRM->OText("idextranjeropermiso", "", "TR.PERMISO_DE_RESIDENCIA");
	$xFRM->ODate("idextranjeroregistro", false, "TR.EXTRANJERO_REGISTRO");
	$xFRM->ODate("idextranjerovencimiento", false, "TR.EXTRANJERO_VENCIMIENTO");
	$xFRM->addHElem( $xSel->getListaDeNacionalidad()->get(true));
	$xFRM->addGuardar("jsGuardarExtranjero()");
}

echo $xFRM->get();
?>
<script>
var xPer	= new PersGen();
var xG		= new Gen();
var idxpersona		= "<?php echo setNoMenorQueCero($persona); ?>";
function jsGuardarExtranjero(){
	xG.confirmar({msg:"CONFIRMA GUARDAR LOS DATOS_EXTRANJEROS", callback: jsSiGuardarExtranjero});
}

function jsSiGuardarExtranjero(){
	var idcarnet	= $("#idextranjeropermiso").val();
	var idfechaI	= $("#idextranjeroregistro").val();
	var idfechaF	= $("#idextranjerovencimiento").val();
	var idnal		= $("#idnacionalidad").val();
	xG.spinInit();
	xPer.addDatosExtr({persona:idxpersona, fechainicial:idfechaI, fechafinal:idfechaF,nacionalidad:idnal,documento:idcarnet,callback:jsSalir});
}
function jsSalir(){
	xG.close();
}
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>