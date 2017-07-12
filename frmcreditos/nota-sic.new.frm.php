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
$xHP		= new cHPage("TR.AGREGAR NOTAS SIC", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc 		= new TinyAjax();
//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
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

$xHP->init();


/* ===========		FORMULARIO EDICION 		============*/
$xTabla		= new cCreditos_sic_notas();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM	= new cHForm("frmnotasic", "nota-sic.frm.php?action=$action");

$xFRM->setTitle($xHP->getTitle());
$xSel		= new cHSelect();

if($clave <= 0){
	$xTabla->idcreditos_sic_notas("NULL");
}
if($credito> DEFAULT_CREDITO){
	
	$xCred			= new cCredito($credito); $xCred->init();
	$xFRM->addHElem($xCred->getFichaMini());
	
	if($clave <= 0){
		$xTabla->credito($credito);
		$xTabla->estatus($xCred->getEstadoActual());
	}
} else {
	if($clave > 0){
		$credito	= $xTabla->credito()->v();
		$xCred		= new cCredito($credito); $xCred->init();
		$xFRM->addHElem($xCred->getFichaMini());
	} else {
		$xFRM->addCreditBasico();
	}
}

$xSelCN	= $xSel->getListaDeCatalogoGenerico("catalogo_notas_sic", "clave_nota", $xTabla->clave_nota()->v());

$xFRM->OHidden("credito", $xTabla->credito()->v(), "TR.CREDITO");
$xFRM->addHElem( $xSelCN->get("TR.CLAVE", true) );

//$xFRM->OText("clave_nota", $xTabla->clave_nota()->v(), "TR.CLAVE");
$xFRM->addHElem( $xSel->getListaDeEstadosDeCredito("estatus", $xTabla->estatus()->v())->get("TR.ESTATUS FORZADO", true) );

$xFRM->OTextArea("texto_nota", $xTabla->texto_nota()->v(), "TR.TEXTO");


$xFRM->OHidden("idcreditos_sic_notas", $xTabla->idcreditos_sic_notas()->v());



$xFRM->addCRUD($xTabla->get(), true);
//$xFRM->addCRUDSave($xTabla->get(), $clave, true);
echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
?>
<script>
function jsEvaluarSalida(){
	$("#credito").val($("#idsolicitud").val());
}
</script>
<?php
$xHP->fin();
?>