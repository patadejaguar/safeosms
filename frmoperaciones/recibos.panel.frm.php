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
$xHP			= new cHPage("TR.Panel de Recibos");
$jxc 			= new TinyAjax();
$xSQL			= new cSQLListas();
$xUser			= new cSystemUser(); $xUser->init();

$fechaRecibo	= fechasys();
$agregar		= parametro("agregar", 0, MQL_FLOAT);

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


function jsaSetTotal($recibo){
	$xRec	= new cReciboDeOperacion(false, true, $recibo);
	$xRec->init();
	$xRec->setGenerarBancos(false);
	$xRec->setGenerarPoliza(false);
	$xRec->setGenerarTesoreria(false);
	$xRec->setCuandoSeActualiza();
	$msg	= "";
	$msg	.= $xRec->getMessages(OUT_HTML);
	return  $msg;
}
function jsaSetFecha($recibo, $fecha){
	$xRec	= new cReciboDeOperacion(false, true, $recibo);
	$xF		= new cFecha();
	$fecha	= $xF->getFechaISO($fecha);
	$xRec->init();
	if( $xF->getInt($fecha) != $xF->getInt($xRec->getFechaDeRecibo()) ){
		$xRec->setFecha($fecha, true);
	}
	return  $xRec->getMessages(OUT_HTML);
}
function jsaSetPeriodo($recibo, $nuevoperiodo){
	$xRec	= new cReciboDeOperacion(false, true, $recibo);
	$xF		= new cFecha();
	
	$xRec->init();
	
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
	$xQL	= new MQL();
	
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
		$rs	= $xQL->getDataRecord($sql);
		
		$arrops	= array();
		foreach ($rs as $rw ){
			$DMov->setData($rw);
			$NMonto		= $DMov->afectacion_real()->v();
			$IDOpe		= $DMov->idoperaciones_mvtos()->v();
			
			if($nuevoTotal > 0){
				$nuevoTotal	-= $NMonto;
				$msg	.= $IDOpe . " \t $nuevoTotal DE $NMonto\r\n";
				if($nuevoTotal < 0 ){
					$msg			.= "CUADRAR $NMonto DE $nuevoTotal  \r\n";
					$nuevoTotal		= $nuevoTotal * -1;
					$dif			= $NMonto - $nuevoTotal;
					
					$sql	= "UPDATE operaciones_mvtos 
							SET afectacion_real=$dif, afectacion_cobranza=$dif, afectacion_contable=$dif,
							afectacion_estadistica=$dif WHERE idoperaciones_mvtos=$IDOpe ";
					$x	= $xQL->setRawQuery($sql);
					//$msg	.= $x[SYS_INFO];
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
			$x	= $xQL->setRawQuery("UPDATE operaciones_mvtos SET recibo_afectado=$idNRec WHERE idoperaciones_mvtos=$operacion");
			//100 - 50
			//$dif	= $monto - $total;
			//$msg	.= $x[SYS_INFO];
		}
		
		$NRec->setFinalizarRecibo(true);
		$msg	.= $NRec->getMessages(OUT_TXT);
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
		if($xPol->initByRecibo($numero) == true){
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

function jsaCambiarClaveDocto($idrecibo, $nuevodocto){
	$xRec	= new cReciboDeOperacion(false, false, $idrecibo);
	if($xRec->init() == true){
		$xRec->setCambiarDocumento($nuevodocto, true);
	}
	return $xRec->getMessages(OUT_HTML);
}
function jsaCambiarTipo($idrecibo, $nuevotipo){
	$xRec	= new cReciboDeOperacion(false, false, $idrecibo);
	if($xRec->init() == true){
		$xRec->setCambiarTipo($nuevotipo);
		//$xRec->setCambiarDocumento($nuevodocto, true);
		//$xRec->setCambiarCodigo();
	}
	return $xRec->getMessages(OUT_HTML);
}

$jxc ->exportFunction('getReciboDesc', array('idNumeroRecibo'));
$jxc ->exportFunction('jsaSetTotal', array('idNumeroRecibo'), '#fb_frmrecibospanel');
$jxc ->exportFunction('jsaSetFecha', array('idNumeroRecibo', 'idnuevafecha'), '#fb_frmrecibospanel');
$jxc ->exportFunction('jsaSetPeriodo', array('idNumeroRecibo', 'idnuevoperiodo'), '#fb_frmrecibospanel');

$jxc ->exportFunction('jsaCambiarClaveDocto', array('idNumeroRecibo', 'idncontrato'), '#fb_frmrecibospanel');
$jxc ->exportFunction('jsaCambiarTipo', array('idNumeroRecibo', 'idnuevotipo'), '#fb_frmrecibospanel');

$jxc ->exportFunction('jsaSetGenerarPolizaPorRecibo', array('idNumeroRecibo'), '#fb_frmrecibospanel');
//$jxc ->exportFunction('jsaEliminarRecibo', array('idNumeroRecibo'), '#fb_frmrecibospanel');

$jxc ->exportFunction('jsaAjustarTotal', array('idNumeroRecibo', 'idOperacion'), '#fb_frmrecibospanel');
$jxc ->exportFunction('jsaRegenerarPrepoliza', array('idNumeroRecibo'), '#fb_frmrecibospanel');

$jxc ->process();

$idrecibo 	= parametro("cNumeroRecibo", 0, MQL_INT); $idrecibo 	= parametro("recibo", $idrecibo, MQL_INT); $idrecibo 	= parametro("idrecibo", $idrecibo, MQL_INT); $idrecibo 	= parametro("clave", $idrecibo, MQL_INT);


//$xHP->setIncludeJQueryUI();
$xHP->init();

$xTxt	= new cHText(); $xBtn	= new cHButton(); $xDate		= new cHDate(); $xSel	= new cHSelect();


?>
<?php
$uri		= "";
$recAct		= "";
$xFRM 		= new cHForm("frmrecibospanel", "../frmoperaciones/recibos.panel.frm.php");


if ( $idrecibo <= 0 ){
	
	$xFRM->addEnviar();
	
	$xFRM->addReciboBasico();
	
	$xFRM->addAvisoInicial("MS.MSG_ELIJA_RECIBO", true, "TR.RECIBO");
	
	echo $xFRM->get();
	
$idrecibo		= "0";

} else {
	//$xFRM 		= new cHForm("frmrecibospanel");
	$xRec		= new cReciboDeOperacion(false, false, $idrecibo);
	
	$xFRM->addCerrar();
	
	if($xRec->init() == true){
		
		$fechaRecibo	= $xRec->getFechaDeRecibo();
		$totalRecibo	= $xRec->getTotal();
		
		
		$xBtn			= new cHButton();
		$xHNot			= new cHNotif();
		
		$xFRM->addRefrescar();
		$xFRM->setTitle($xHP->getTitle());
		
		$xFRM->addRecibosComando($xRec->getCodigoDeRecibo(), $xRec->getURI_Formato());
		
		if($xUser->getPuedeEditarRecibos() == true){
			$xFRM->OButton("TR.Cambiar fecha", "jsGoActualizarFecha()", $xFRM->ic()->CALENDARIO );
			$xFRM->OButton("TR.Cambiar PERIODO", "jsGoActualizarPeriodo()",$xFRM->ic()->CALENDARIO1 );
			$xFRM->OButton("TR.Actualizar Total", "jsaSetTotal()", $xFRM->ic()->DINERO );
			$xFRM->OButton("TR.Actualizar DOCUMENTO", "jsGoActualizarDocumento()", $xFRM->ic()->CONTRATO, "idcmdactualizardocto", "blue" );
			$xFRM->OButton("TR.Actualizar Tipo", "jsGoActualizarTipo()", $xFRM->ic()->CONTROL, "idcmdactualizartipo", "" );
			
			$xFRM->OButton("TR.Ajustar parcialidad", "jsSepararLetra()",$xFRM->ic()->EJECUTAR);
			$xFRM->OButton("TR.Agregar Operacion", "jsAddMvto($idrecibo)", $xFRM->ic()->OPERACION);
			$xFRM->OButton("TR.Cambiar Banco", "jsCambiarBanco($idrecibo)", $xFRM->ic()->BANCOS);
			
			//Cambiar Documento
			
		}
		if( getEsModuloMostrado(USUARIO_TIPO_CONTABLE, MMOD_CONTABILIDAD) == true ){
			
			$xPol	= new cPoliza(false);
			if($xPol->initByRecibo($xRec->getCodigoDeRecibo()) == true){
				if(SAFE_ON_DEV == true){
					$xFRM->OButton("TR.Generar Poliza", "jsaSetGenerarPolizaPorRecibo()", "poliza", "idcmdgenpol");
					$xFRM->OButton("TR.Generar Pre-Poliza", "jsaRegenerarPrepoliza()", $xFRM->ic()->EJECUTAR, "idcmdgenprepol");
				}
				$xFRM->OButton("TR.Poliza", "var xC=new ContGen();xC.editarPolizaById('" . $xPol->getCodigoId() . "');", $xFRM->ic()->BALANZA, "idcmdeditpol");
				$xFRM->OButton("TR.REPORTE Poliza", "var xC=new ContGen();xC.ImprimirPolizaById('" . $xPol->getCodigoId() . "');", $xFRM->ic()->BALANZA, "idcmdrptpol");
			} else {
				$xFRM->OButton("TR.Generar Poliza", "jsaSetGenerarPolizaPorRecibo()", "poliza", "idcmdgenpol");
				$xFRM->OButton("TR.Generar Pre-Poliza", "jsaRegenerarPrepoliza()", $xFRM->ic()->EJECUTAR, "idcmdgenprepol");
			}
			

		}	
		
		$xFRM->addHTML( $xRec->getFicha(true, "", true) );
		
		//echo ;
		$uri 		= $xRec->getURI_Formato();
		$xTabs		= new cHTabs();
	/* ----------------- DATOS --------------- */
			$cEdit		= new cTabla($xSQL->getListadoDeOperaciones("", "", $idrecibo), 0, "idtbllistaopsrp");
			if($xUser->getPuedeEditarRecibos() == true){
				$cEdit->addEditar();
				$cEdit->addEliminar();
				$cEdit->setEventKey("jsEditClick");
				$cEdit->OButton("TR.Copiar", "jsSetClonar(" . HP_REPLACE_ID . ")", $xFRM->ic()->EXPORTAR);
			}
			$cEdit->setTdClassByType();
			$cEdit->setKeyField("idoperaciones_mvtos");
			if(MODO_DEBUG == true){
				$cEdit->setFootSum(array(8 => "monto"));
			} else {
				$cEdit->setOmitidos("operacion");
				$cEdit->setFootSum(array(7 => "monto"));
			}
			
					
			
			$xTabs->addTab("TR.OPERACIONES", $cEdit->Show());
			$NumOpers	= $cEdit->getRowCount();
			
			$cBan		= new cTabla($xSQL->getListadoDeOperacionesBancarias("", "", "", false, false, " AND `bancos_operaciones`.`recibo_relacionado` = $idrecibo "),0, "idtbllistaopsbancsrp");
			if(MODULO_CAJA_ACTIVADO == true){
				//$cBan->setEventKey("idcontrol");
				$cBan->setKeyField("idcontrol");
				if($xUser->getPuedeEditarRecibos() == true){
					$cBan->addEditar();
					$cBan->addEliminar();
				}
				$cBan->setFootSum(array(7 => "monto"));
				$xTabs->addTab("TR.BANCOS", $cBan->Show());
				//Operaciones de Tesorería
				$cTes		= new cTabla($xSQL->getListadoDeOperacionesDeTesoreria("", "", $idrecibo),0, "idtbllistaopstesrp");
				if($xUser->getPuedeEditarRecibos() == true){
					$cTes->addEditar();
					$cTes->addEliminar();
				}
				$xTabs->addTab("TR.TESORERIA", $cTes->Show());
			}
			//agregar contable
			if( getEsModuloMostrado(USUARIO_TIPO_CONTABLE, MMOD_CONTABILIDAD) == true ){
				$xTbl	= new cTabla($xSQL->getListadoDePrepoliza($idrecibo),0, "idtbllistamvtospolrp");
				$xTabs->addTab("TR.Forma Poliza", $xTbl->Show());
				//poliza relacionada
				$xTbl	= new cTabla($xSQL->getListadoDePolizasContables(false, false, false,false,false, " AND (`recibo_relacionado`=$idrecibo) "),0,"idtbllistapolscontrp");
				$xTabs->addTab("TR.Poliza", $xTbl->Show());
				//factura XML
				//$xRec->getFactura(false, OUT_RXML);
				//$xDo	= new cDocumentos();
			}
			//
			$xFRM->addHTML($xTabs->get());
		$xFRM->addHTML( "<input type='hidden' name='cNumeroRecibo' id='idNumeroRecibo' value='$idrecibo'>
		<input type='hidden' name='cFechaRecibo' id='idFechaRecibo' value='$fechaRecibo'><input type='hidden' name='cTotalRecibo' id='idTotalRecibo' value='$totalRecibo'>
		<input type='hidden' name='cOperacion' id='idOperacion' value=''>
		");
		
		$xFRM->OHidden("agregar", $agregar);
		$xFRM->addFooterBar("<br/>");
		
		//======================== Si puede eliminar Recibo
		//if($xUser->getPuedeEliminarRecibos() == true){
		//	$xFRM->OButton("TR.Eliminar Recibo", "var xRec = new RecGen();xRec.confirmaEliminar($recibo);", $xFRM->ic()->ELIMINAR, "del-$idrecibo", "red");
		//}
		
		
		$recAct		=  $cEdit->getJSActions();
		
		//ACTUALIZAR Parcialidad y Numero 1
		$xFRM2	= new cHForm("frmajustarparc");
		$xFRM2->setNoFormTags();
		$xFRM2->ODate("idnuevafecha", $fechaRecibo, "TR.NUEVA FECHA");
		$xFRM2->setNoJsEvtKeyForm();
		$xFRM2->addGuardar("jsActualizarFecha()", "jsCancelAction()");
		
		echo "<div class=\"inv formoid-default\" id=\"ajustarparc\">" . $xFRM2->get(false) . "</div>";
		
		//ACTUALIZAR Parcialidad y Numero 2
		$xFRM3	= new cHForm("frmajustarparc2");
		$xFRM3->setNoFormTags();
		if($xRec->getEsDeCredito() == true){
			//$xSel->getListaDePeriodosDeCredito();
			$xCred	= new cCredito($xRec->getCodigoDeDocumento());
			if($xCred->init() == true){
				$xHSel	= new cHSelect();
				$arr	= array("0" => "0");
				for($i=1;$i<= $xCred->getPagosAutorizados(); $i++){
					$arr["" . $i . ""] = "" . $i . "";
				}
				$xHSel->addOptions($arr);
				
				$xFRM3->addHElem( $xHSel->get("idnuevoperiodo", "TR.NUEVO PERIODO", $xRec->getPeriodo()) );
			}
		} else {
				$xFRM3->addHElem( $xTxt->getNumero("idnuevoperiodo", $xRec->getPeriodo(), "TR.Nuevo Periodo") );
		}
		
		$xFRM3->addGuardar("jsActualizarPeriodo()", "jsCancelAction()");
		$xFRM3->setNoJsEvtKeyForm();
		
		echo "<div class=\"inv formoid-default\" id=\"ajustarparc2\">" . $xFRM3->get(false) . "</div>";
		
		//Actualizar Documento
		$xSelC	= $xSel->getListaDeContratosPorPers("idncontrato", false, $xRec->getCodigoDeSocio());
		$xSelC->addEspOption(FALLBACK_CLAVE_DE_DOCTO, "Ninguno");
		
		$xFRM4	= new cHForm("frmnewdocto");
		$xFRM4->setNoFormTags();
		$xFRM4->addHElem( $xSelC->get("TR.NUEVO DOCUMENTO", true) );
		$xFRM4->addGuardar("jsActualizarDocumento()", "jsCancelAction()");
		$xFRM4->setNoJsEvtKeyForm();
		
		echo "<div class=\"inv formoid-default\" id=\"newdoctodiv\">" . $xFRM4->get(false) . "</div>";
		
		//Actualizar Tipo
		//idnuevotipo
		
		
		
		
		$xFRM5	= new cHForm("frmnewtipo");
		$xFRM5->setNoFormTags();
		$xFRM5->addHElem( $xSel->getListaDeTiposDeRecibos("idnuevotipo", $xRec->getTipoDeRecibo())->get(true) );
		$xFRM5->addGuardar("jsActualizarTipo()", "jsCancelAction()");
		$xFRM5->setNoJsEvtKeyForm();
		
		echo "<div class=\"inv formoid-default\" id=\"newtipodiv\">" . $xFRM5->get(false) . "</div>";
	
	
	} else {
		$xFRM->addCerrar();
		
		if($xRec->getEsEliminado() == true){
			$xFRM->OButton("TR.Reporte del Recibo", "var xRec = new RecGen();xRec.reporte($idrecibo);", $xFRM->ic()->REPORTE, "rpt-$idrecibo");
			$xFRM->addAvisoRegistroError("MS.RECIBO_EN_ARCHIVO");
		} else {
			$xFRM->addAvisoRegistroError("MS.RECIBO_NO_EXISTE");
		}
	}
	//echo "<div id='idnotedel' class='inv formoid-default'>...</div>";
	
	echo $xFRM->get();
}



?>
</body>
<?php
	$jxc ->drawJavaScript(false, true);
	//$xc	= new jsBasicForm("frmrecibospanel");
	//$xc->setIncludeJQuery();
	//$xc->setNombreCtrlRecibo("cNumeroRecibo");
	//$xc->show();
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
	if(nmonto > 0){
		var sip		= confirm("Desea Repartir las Operaciones Del recibo a  " + nmonto);
		if(sip && nmonto > 0){
			$("#idOperacion").val(nmonto);
			jsaAjustarTotal();
		} else {
			console.log("No se actualizo nada");
		}
	}
}

function jsModificarPoliza(id){ var xG	= new Gen(); xG.w({url : "../frmcontabilidad/poliza_movimientos.frm.php?codigo=" +id, h : 600, W : 800, tiny : true }); }
function jsGoActualizarFecha(){ getModalTip(mobj, $("#ajustarparc"), xGen.lang(["Actualizar", "Fecha"]) ); }
function jsGoActualizarPeriodo(){ getModalTip(mobj, $("#ajustarparc2"), xGen.lang(["Actualizar", "Parcialidad"]) ); }
function jsGoActualizarDocumento(){ getModalTip(mobj, $("#newdoctodiv"), xGen.lang(["Actualizar", "Documento"]) ); }

function jsGoActualizarTipo(){ getModalTip(mobj, $("#newtipodiv"), xGen.lang(["Actualizar", "Tipo"]) ); }

function jsActualizarTipo(){
	xGen.confirmar({msg: "Desea Actualizar el Tipo en el Recibo", callback : jsaCambiarTipo });
}

function jsActualizarDocumento(){
	xGen.confirmar({msg: "Desea Actualizar el documento en el Recibo y sus operaciones", callback : jsaCambiarClaveDocto });
}
function jsActualizarFecha(){
	var idrec	= $("#idNumeroRecibo").val();
	xGen.confirmar({msg: "Desea Actualizar la fecha del Recibo y de operaciones", callback : jsaSetFecha });
}
function jsActualizarPeriodo(){
	var idrec	= $("#idNumeroRecibo").val();
	xGen.confirmar({msg: "Desea Actualizar el periodo del Recibo y de operaciones", callback : jsaSetPeriodo });
}
function jsEditClick(id){ xG.editar({tabla:'operaciones_mvtos',id:id}); }
function jsBuscarPoliza(idrecibo){
	$("#idclaveactual").val(idrecibo);
	xRec.getExistePolizaContable({
		recibo : idrecibo,	open : true
		});
}
function jsSetClonar(id){
	xGen.w({url:"operaciones.mvtos.clon.frm.php?id="+id, tiny:true,w:400});
}
function jsAddMvto(idrecibo){
	var agregar	= $("#agregar").val();
	xGen.w({url:"operaciones.mvtos.add.frm.php?recibo="+idrecibo + "&monto=" +agregar, tiny:true,w:400});
}
function jsCambiarBanco(id){
	xGen.w({url:"recibos.cambiar-banco.frm.php?recibo="+id, tiny:true,w:400});
}
function jsCancelAction(){
	var id  = "#" + session(Configuracion.opciones.dialogID);
	$(id).dialog( "close" );
}
</script>
</html>
