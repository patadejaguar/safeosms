<?php
/**
 * Editor de recibos, operaciones modo RAW.
 * @author Balam Gonzalez Luis Humberto
 * @package operaciones
 * @subpackage forms
 * @version 1.1.20
 */
//=====================================================================================================
//=====>	INICIO_H
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	$theFile					= __FILE__;
	$permiso					= getSIPAKALPermissions($theFile);
	if($permiso === false){		header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//<=====	FIN_H
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
$xHP		= new cHPage("TR.Panel de Recibos");
$jxc 		= new TinyAjax();
$xSQL		= new cSQLListas();

$fechaRecibo	= fechasys();

function getReciboDesc($numero){
	if ( isset($numero) AND ( $numero != 0) ){
		$cRec	= new cReciboDeOperacion(false, false, $numero);
		$cRec->setNumeroDeRecibo($numero, true);
		$d		= $cRec->getDatosInArray();
		$desRec	= $cRec->getDescripcion();
		$tab = new TinyAjaxBehavior();
		$tab -> add( TabSetValue::getBehavior("txtDescRecibo", $desRec) );

		return $tab -> getString();
	}
}
function jsaEliminarRecibo($numero){
	if ( setNoMenorQueCero($numero )> 0 ){
		$oficial 	= elusuario( $_SESSION["log_id"] );
		$cRec		= new cReciboDeOperacion(false, false, $numero);
		$cRec->setNumeroDeRecibo($numero);
		$cRec->setRevertir();

	return  $cRec->getMessages(OUT_HTML);
	}
}

function jsaSetTotal($recibo){
	$xRec	= new cReciboDeOperacion(false, true, $recibo);
	$xRec->init();
	$xRec->setGenerarBancos(false);
	$xRec->setGenerarPoliza(false);
	$xRec->setGenerarTesoreria(false);
	$xRec->setForceUpdateSaldos(true);
	$xRec->setFinalizarRecibo(true);
	$msg	= "";
	if($xRec->getOrigen() == TESORERIA_RECIBOS_ORIGEN_MIXTO OR $xRec->getOrigen() == TESORERIA_RECIBOS_ORIGEN_CRED){
		$credito	= $xRec->getCodigoDeDocumento();
		$xUtil		= new cUtileriasParaCreditos();
		$msg	.= $xUtil->setCuadrarCreditosByMvtos($credito);
	}
	$msg	.= $xRec->getMessages(OUT_TXT);
	return  $msg;
}
function jsaSetFecha($recibo, $fecha, $nuevoperiodo){
	$xRec	= new cReciboDeOperacion(false, true, $recibo);
	$xF		= new cFecha();
	$fecha	= $xF->getFechaISO($fecha);
	$xRec->init();
	if( $xF->getInt($fecha) != $xF->getInt($xRec->getFechaDeRecibo()) ){
		$xRec->setFecha($fecha, true);
	}
	if($xRec->getPeriodo() != $nuevoperiodo){
		$xRec->setPeriodo($nuevoperiodo, true);
	}
	return  $xRec->getMessages(OUT_HTML);
}
function jsaAjustarTotal($recibo, $nuevoTotal, $nuevaletra){
	$xRec	= new cReciboDeOperacion(false, true, $recibo);
	$xRec->init();
	$xRec->setGenerarBancos(false);
	$xRec->setGenerarPoliza(false);
	$xRec->setGenerarTesoreria(false);
	$xRec->setForceUpdateSaldos(true);
	$total	= $xRec->getTotal();
	$QL		= new MQL();
	
	$DMov	= new cOperaciones_mvtos();
	$msg	= "";
	if($nuevoTotal < $total){
		$NRec	= new cReciboDeOperacion($xRec->getTipoDeRecibo(), false, $recibo );
		$idNRec	= $NRec->setNuevoRecibo($xRec->getCodigoDeSocio(), $xRec->getCodigoDeDocumento(), $xRec->getFechaDeRecibo(),
				      0, $xRec->getTipoDeRecibo(), "AJUSTE DEL RECIBO $recibo" );
		$NRec->setForceUpdateSaldos(true);
		$NRec->setGenerarBancos(false);
		$NRec->setGenerarPoliza(false);
		$NRec->setGenerarTesoreria(false);
			/*`idoperaciones_mvtos`,
			`operaciones_mvtos`.`fecha_operacion`,
			`operaciones_mvtos`.`fecha_afectacion`,
			`operaciones_mvtos`.`recibo_afectado`,
			`operaciones_mvtos`.`socio_afectado`,
			`operaciones_mvtos`.`docto_afectado`,
			`operaciones_mvtos`.`tipo_operacion`,
			`operaciones_mvtos`.`afectacion_real`  */
			
		$sql	= "SELECT
			`operaciones_mvtos`.*

		FROM
			`operaciones_mvtos` `operaciones_mvtos` 
				INNER JOIN `operaciones_tipos` `operaciones_tipos` 
				ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.
				`idoperaciones_tipos` 
		WHERE
			(`operaciones_mvtos`.`recibo_afectado` =$recibo) 
		ORDER BY
			`operaciones_mvtos`.`tipo_operacion` DESC ";
		$rs	= $QL->getDataRecord($sql);
		
		$arrops	= array();
		foreach ($rs as $rw ){
			$DMov->setData($rw);
			$NMonto		= $DMov->afectacion_real()->v();
			$IDOpe		= $DMov->idoperaciones_mvtos()->v();
			if($nuevoTotal > 0){
				$nuevoTotal	-= $NMonto;
				$msg	.= $IDOpe . " \t $nuevoTotal DE $NMonto\r\n";
				if($nuevoTotal < 0 ){
					$msg		.= "CUADRAR $NMonto DE $nuevoTotal  \r\n";
					$nuevoTotal	= $nuevoTotal * -1;
					$dif	= $NMonto - $nuevoTotal;
					
					$sql	= "UPDATE operaciones_mvtos 
							SET afectacion_real=$dif, afectacion_cobranza=$dif, afectacion_contable=$dif,
							afectacion_estadistica=$dif WHERE idoperaciones_mvtos=$IDOpe ";
					$x	= my_query($sql);
					$msg	.= $x[SYS_INFO];
					//agregar el movimiento al nuevo recibo con cargos
					$NRec->setNuevoMvto($DMov->fecha_operacion()->v(), $nuevoTotal, $DMov->tipo_operacion()->v(),
							    $DMov->periodo_socio()->v(), "SEPARACION DEL MVTO $IDOpe $nuevoTotal", $DMov->valor_afectacion()->v(),
							    false, $DMov->socio_afectado()->v(), $DMov->docto_afectado()->v(),
							    $DMov->fecha_afectacion()->v(), $DMov->fecha_vcto()->v(),
							    $DMov->saldo_anterior()->v(), $DMov->saldo_actual()->v());
					
					//listo
					$nuevoTotal	= 0;
				}
			} else {
				$arrops[$DMov->idoperaciones_mvtos()->v()] = $DMov->afectacion_real()->v();
			}
		}
		//$total	= $total  * -1;
		foreach($arrops as $operacion => $monto){
			$x	= my_query("UPDATE operaciones_mvtos SET recibo_afectado=$idNRec WHERE idoperaciones_mvtos=$operacion");
			//100 - 50
			//$dif	= $monto - $total;
			$msg	.= $x[SYS_INFO];
		}
		
		$NRec->setFinalizarRecibo(true);
		$msg		.= $NRec->getMessages(OUT_TXT);
		$xRec->setFinalizarRecibo(true);
	} else{
		$msg	.= "WARN\tNO SE MODIFICA NADA($nuevoTotal|$total)\r\n";
	}

	$msg		.= $xRec->getMessages(OUT_TXT);
	return  $msg;
}
function jsaSetGenerarPolizaPorRecibo($numero){
	if ( setNoMenorQueCero($numero) > 0 ){
		$Recibo		= $numero;
		$xPol		= new cPoliza(false);
		$idpoliza	= "";
		$msg		= "";
		if($xPol->setPorRecibo($numero) == true){
			$idpoliza	= $xPol->getCodigo();
			
		} else {
			$xUCont		= new cUtileriasParaContabilidad();
			$xUCont->setRegenerarPrepolizaContable(false, $Recibo);
			$xUCont->setPolizaPorRecibo($Recibo);
			$idpoliza	= $xUCont->getIDPoliza();
			$msg		.= $xUCont->getMessages();
		}
		$msg		.=  $xPol->getMessages();
		//exportar
		$xPolCW		= new cPolizaCompacW(0);
		$xPolCW->initByID($idpoliza);
		$xPolCW->setRun();
		$msg		.= $xPolCW->getMessages();
		$strDown	= $xPolCW->setExport();


		if(MODO_DEBUG == true){
			$xLog	= new cFileLog();
			$xLog->setWrite($msg);
			$strDown	.= $xLog->getLinkDownload("TR.Eventos");
		}
		$xBtn			= new cHButton();
		return $xBtn->getBasic("TR.Modificar Poliza","jsModificarPoliza('$idpoliza')", $xBtn->ic()->EDITAR, "cmdeditpoliza") . $strDown ;
	} else {
		return "NO HAY REGISTRO QUE GENERAR [$numero]";
	}
}
function jsaRegenerarPrepoliza($recibo){
	$xUtilCont	= new cUtileriasParaContabilidad();
	$xUtilCont->setRegenerarPrepolizaContable(false, $recibo);
	return $xUtilCont->getMessages(OUT_HTML);
}

$jxc ->exportFunction('getReciboDesc', array('idNumeroRecibo'));
$jxc ->exportFunction('jsaSetTotal', array('idNumeroRecibo'), '#fb_frmrecibospanel');
$jxc ->exportFunction('jsaSetFecha', array('idNumeroRecibo', 'idfecha-100', 'idnuevoperiodo'), '#fb_frmrecibospanel');
$jxc ->exportFunction('jsaSetGenerarPolizaPorRecibo', array('idNumeroRecibo'), '#fb_frmrecibospanel');
$jxc ->exportFunction('jsaEliminarRecibo', array('idNumeroRecibo'), '#fb_frmrecibospanel');

$jxc ->exportFunction('jsaAjustarTotal', array('idNumeroRecibo', 'idOperacion'), '#fb_frmrecibospanel');
$jxc ->exportFunction('jsaRegenerarPrepoliza', array('idNumeroRecibo'), '#fb_frmrecibospanel');

$jxc ->process();

$idrecibo 	= parametro("cNumeroRecibo", 0, MQL_INT); $idrecibo 	= parametro("recibo", $idrecibo, MQL_INT); $idrecibo 	= parametro("idrecibo", $idrecibo, MQL_INT); $idrecibo 	= parametro("clave", $idrecibo, MQL_INT);


$xHP->setIncludeJQueryUI();
echo $xHP->getHeader();
echo $xHP->setBodyinit();

$xTxt	= new cHText(); $xBtn	= new cHButton(); $xDate		= new cHDate();


?>
<?php
$uri		= "";
$recAct		= "";

if ( $idrecibo <= 0 ){
?>
<fieldset>
<legend>Panel de Control de Recibos</legend>

<form name="frmrecibospanel" id="frmrecibospanel" action="recibos.panel.frm.php" method="post">
<table  >
	<tr>
		<td width="10%">Numero de Recibo</td>
		<td width="20%">
		<input type='text' name='cNumeroRecibo' value='0' id="idNumeroRecibo" onchange="getReciboDesc()" onblur="getReciboDesc()"  class='mny' size='12' />
			<?php echo CTRL_GORECIBOS; ?></td>
		<td width="10%">Descripcion Corta</td>
		<th width="60%"><input type="text" name="txtDescRecibo" id="txtDescRecibo" disabled size="60" /></th>
	</tr>
	<tr>
		<th colspan='4'><input type='button' name='btnEnviar' value='ENVIAR NUMERO DE RECIBO' onclick='frmrecibospanel.submit();'></th>
	</tr>
</table>
</form>
</fieldset>
<?php
$idrecibo		= "0";

} else {
	$xFRM 		= new cHForm("frmrecibospanel");
	
	$xRec		= new cReciboDeOperacion(false, false, $idrecibo);
	$xRec->init();
	$fechaRecibo	= $xRec->getFechaDeRecibo();
	$totalRecibo	= $xRec->getTotal();
	
	$xBtn			= new cHButton();
	$xHNot			= new cHNotif();
	
	$xFRM->OButton("TR.Cambiar fecha", "jsGoActualizarFecha()", "fecha", "cmdGo5");
	$xFRM->OButton("TR.Actualizar Total", "jsaSetTotal()", "actualizar", "cmdGo6");
	$xFRM->OButton("TR.Ajustar parcialidad", "jsSepararLetra()", "dinero", "cmdGo6");
	$xFRM->OButton("TR.Reporte del Recibo", "jsReportRecibo($idrecibo)", "reporte", "cmdGo7");
	
	$xFRM->OButton("TR.Agregar Bancos", "jsAddBancos()", "bancos", "cmdGo11");
	$xFRM->OButton("TR.Agregar Tesoreria", "jsAddTesoreria()", "dinero", "cmdGo12");
	
	$xFRM->OButton("TR.Reimprimir Recibo", "ImprimirRecibo()", "imprimir", "cmdGo8");
	$xFRM->OButton("TR.Editar Recibo", "actualizaRec($idrecibo)", $xFRM->ic()->EDITAR, "cmdGo9");
	$xFRM->OButton("TR.Eliminar Recibo", "jsEliminarRecibo()", $xFRM->ic()->ELIMINAR, "cmdGo10");
	if( getEsModuloMostrado(USUARIO_TIPO_CONTABLE) == true ){
		$xFRM->OButton("TR.Factura", "jsGetFactura($idrecibo)", $xFRM->ic()->EXPORTAR);
		$xFRM->OButton("TR.Generar Poliza", "jsaSetGenerarPolizaPorRecibo()", "poliza", "cmdGo4");
		$xFRM->OButton("TR.Generar Pre-Poliza", "jsaRegenerarPrepoliza()", $xFRM->ic()->EJECUTAR);
	}
	
	$xFRM->addHTML( $xRec->getFicha(true, "", true) );
	
	//echo ;
	$uri 		= $xRec->getURI_Formato();
	$xTabs		= new cHTabs();
/* ----------------- DATOS --------------- */
		$cEdit		= new cTabla($xSQL->getListadoDeOperaciones("", "", $idrecibo));
		$cEdit->addTool(SYS_UNO);
		$cEdit->addTool(SYS_DOS);
		$cEdit->setEventKey("jsEditClick");
		$cEdit->setTdClassByType();
		$cEdit->setKeyField("idoperaciones_mvtos");
		$xTabs->addTab("TR.OPERACIONES", $cEdit->Show());
		$NumOpers	= $cEdit->getRowCount();
		
		$cBan		= new cTabla($xSQL->getListadoDeOperacionesBancarias("", "", "", false, false, " AND `bancos_operaciones`.`recibo_relacionado` = $idrecibo "));
		
		//$cBan->setEventKey("idcontrol");
		$cBan->setKeyField("idcontrol");
		$cBan->addTool(SYS_UNO);
		$cBan->addTool(SYS_DOS);
		$cBan->setFootSum(array(7 => "monto"));
		$xTabs->addTab("TR.BANCOS", $cBan->Show());
		//Operaciones de Tesorería
		$cTes		= new cTabla($xSQL->getListadoDeOperacionesDeTesoreria("", "", $idrecibo));
		$cTes->addTool(SYS_UNO);
		$cTes->addTool(SYS_DOS);
		$xTabs->addTab("TR.TESORERIA", $cTes->Show());
		//agregar contable
		if( getEsModuloMostrado(USUARIO_TIPO_CONTABLE) == true ){
			$xTbl	= new cTabla("SELECT * FROM `contable_polizas_proforma` WHERE `numero_de_recibo`=$idrecibo");
			$xTabs->addTab("TR.Pre Poliza", $xTbl->Show());
			//poliza relacionada
			
			$xTbl	= new cTabla($xSQL->getListadoDePolizasContables(false, false, false,false,false, " AND (`recibo_relacionado`=$idrecibo) "));
			$xTabs->addTab("TR.Poliza", $xTbl->Show());
			//factura XML
			//$xRec->getFactura(false, OUT_RXML);
			//$xDo	= new cDocumentos();
		}
		//
		$xFRM->addHTML($xTabs->get());
	$xFRM->addHTML( "<input type='hidden' name='cNumeroRecibo' id='idNumeroRecibo' value='$idrecibo'>
	<input type='hidden' name='cFechaRecibo' id='idFechaRecibo' value='$fechaRecibo'>
	<input type='hidden' name='cTotalRecibo' id='idTotalRecibo' value='$totalRecibo'>
	<input type='hidden' name='cOperacion' id='idOperacion' value=''>
	");
	$xFRM->addFooterBar("<br/>");
	echo $xFRM->get();
	$recAct		=  $cEdit->getJSActions();
	
	
	$xFRM2	= new cHForm("frmajustarparc");
	//$xFRM2->addHElem( $xTxt->getNumero("idnuevototal", $totalRecibo, "TR.Nuevo Monto") );
	$xFRM2->addHElem( $xDate->get("TR.Nueva fecha", $fechaRecibo, 100 ));
	$xFRM2->addHElem( $xTxt->getNumero("idnuevoperiodo", $xRec->getPeriodo(), "TR.Nueva periodo") );
	$xFRM2->addSubmit("", "jsActualizarFecha()");
	echo "<div class=\"inv\" id=\"ajustarparc\">" . $xFRM2->get() . "</div>";
}
?>
</body>
<?php
	$jxc ->drawJavaScript(false, true);
	$xc	= new jsBasicForm("frmrecibospanel");
	//$xc->setIncludeJQuery();
	$xc->setNombreCtrlRecibo("cNumeroRecibo");
	$xc->show();
?>
<script>
<?php echo $recAct; ?>
var	idRecibo	= <?php echo $idrecibo; ?>;
var xGen		= new Gen();
var xRec		= new RecGen();
var mobj 		= $("#ficharecibo");

function jsSepararLetra(){
	var idrec	= $("#idNumeroRecibo").val();
	var nmonto	=  flotante(window.prompt("Nuevo Monto","0"));
	var sip		= confirm("Desea Repartir las Operaciones Del recibo a  " + nmonto);
	if(sip && nmonto > 0){
		$("#idOperacion").val(nmonto);
		jsaAjustarTotal();
	} else {
		console.log("No se actualizo nada");
	}
}
function actualizaRec(id) {
	var xurl 	= "../utils/frm8db7028bdcdf054882ab54f644a9d36b.php?t=operaciones_recibos&f=idoperaciones_recibos=" + id;
	var xG	= new Gen(); xG.w({url : xurl, h : 600, W : 800, tiny : true });
}
function jsEliminarRecibo(){ xGen.confirmar({ msg : "Eliminar Recibo y Operaciones?\nEl cambio es permanente." , callback : jsaEliminarRecibo }); }
function ImprimirRecibo(){ xGen.w({ url: "<?php echo $uri; ?>", h:600, w : 800}); }
function jsGetFactura(idrec){ xRec.factura(idrec); }
function jsModificarPoliza(id){ var xG	= new Gen(); xG.w({url : "../frmcontabilidad/poliza_movimientos.frm.php?codigo=" +id, h : 600, W : 800, tiny : true }); }
function jsReportRecibo(idrec){ xRec.reporte(idrec); }
function jsGoActualizarFecha(){ getModalTip(mobj, $("#ajustarparc"), xGen.lang(["Actualizar", "Parcialidad"]) ); }
function jsActualizarFecha(){
	var idrec	= $("#idNumeroRecibo").val();
	//var nfecha	=  window.prompt("Nueva fecha","<?php echo $fechaRecibo; ?>");
	var sip		= confirm("Desea Actualizar la fecha del Recibo y de operaciones");
	if(sip){
		//$("#idFechaRecibo").val(nfecha);
		jsaSetFecha();
		mobj.qtip("hide");
	}
}
function jsEditClick(id){ jsUp('operaciones_mvtos','idoperaciones_mvtos', id); }
function jsAddBancos() { xGen.w({url:"../frmbancos/movimientos_bancarios.frm.php?origen=recibo&item=" + idRecibo, tiny : true}); }
function jsAddTesoreria(){ xGen.w({url:"../frmtesoreria/tesoreria_operaciones.frm.php?origen=recibo&item=" + idRecibo, tiny:true}); }
</script>
</html>
