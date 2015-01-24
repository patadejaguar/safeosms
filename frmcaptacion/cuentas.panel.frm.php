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
$mSQL		= new cSQLListas();

$DDATA		= $_REQUEST;
$jxc = new TinyAjax();



//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", 0, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->init();
$xJs				= new jsBasicForm("frm");
$xJs->setIncludeJQuery();

$xFRM		= new cHForm("frm", "cuentas.panel.frm.php");
$xBtn		= new cHButton();		
$xTxt		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();
if($cuenta <= 0){
	
	$xFRM->addJsBasico(iDE_CAPTACION);
	$xFRM->addCuentaCaptacionBasico(true);
	$xFRM->addSubmit();
	
} else {
	$xCta	= new cCuentaDeCaptacion($cuenta);
	$xCta->init();
	
	$xFRM->addHTML($xCta->getFicha(true, "", true) );
	$xFRM->addSubmit();
	
	//$xFRM->addToolbar( $xBtn->getBasic("TR.refrescar", "jsRecargar()", "refrescar", "refrescar", false ) );
	//$xFRM->addToolbar( $xBtn->getBasic("TR.imprimir contrato", "jsRecargar()", "refrescar", "refrescar", false ) );
	
	$xFRM->addCaptacionComandos($cuenta);
	
	$xHTabs	= new cHTabs();
	
	$cTblx	= new cTabla($mSQL->getListadoDeRecibos("", $xCta->getClaveDePersona(), $xCta->getNumeroDeCuenta() ));
	$cTblx->setKeyField("idoperaciones_recibos");
	$cTblx->setTdClassByType();
	$cTblx->setEventKey("jsGoPanelRecibos");
	$xHTabs->addTab("TR.RECIBOS", $cTblx->Show());
	$xFRM->addHTML( $xHTabs->get() );
	
	
	$xFRM->OHidden("idcuentacaptacion", $cuenta);
	
	/*
	 * <fieldset>
				<legend>Barra de Acciones</legened>
					<table  align='center'>
						<tr>
							<td>
								<input type='button' name='printcontrato' value='IMPRIMIR CONTRATO DE CAPTACION' onClick='printrec();'>
							</td>
							<td>
								<input type='button' name='command' value='Ver/Guardar Firmas' onClick='captura_firmas();'>
							</td>
							<td>
								<input type='button' name='cmd_edit' value='Editar Datos del Contrato' onClick='feditar_cuenta();'>
							</td>
							<td>
								<a class='button' name='cmd_printMandato'  onClick='printMandato();'>Imprimir Mandato</a>
							</td>
					</table>
			</fieldset>
	 * */
}

echo $xFRM->get();


echo $xJs->get();
?>
<script>
function jsGoPanelRecibos(id){ var xRec = new RecGen(); xRec.panel(id); }
</script>
<?php
$xHP->fin();
//$jxc ->drawJavaScript(false, true);


?>