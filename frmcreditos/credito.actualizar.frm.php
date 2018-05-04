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
$xHP		= new cHPage("TR.Actualizar Creditos", HP_FORM);
$credito	= (isset($_REQUEST["credito"])) ? $_REQUEST["credito"] : false;
$idrecibo	= 0;

function jsaCambiarFechaMinistracion($credito, $fechaNueva){
    $xF		= new cFecha(0);
    $fechaNueva	= $xF->getFechaISO($fechaNueva);
    $xCred	= new cCredito($credito); $xCred->init();
    $msg	= $xCred->setCambiarFechaMinistracion($fechaNueva);
    $msg	.= "<br>CAMBIE EL PLAN DE PAGOS";
    return $msg;
}

function jsaCambiarMontoMinistrado($credito, $nuevoMonto){
    $xF		= new cFecha(0);
    //$fechaNueva	= $xF->getFechaISO($fechaNueva);
    $xCred	= new cCredito($credito);
    $xCred->init();
    $xCred->setCambiarMontoMinistrado($nuevoMonto);
    $msg	= $xCred->getMessages(OUT_HTML);
    $msg	.= "<br>CAMBIE EL PLAN DE PAGOS";
    return $msg;
}
function jsaCambiarMontoAutorizado($credito, $nuevoMonto, $nuevosPagos){
    $xF		= new cFecha(0);
    //$fechaNueva	= $xF->getFechaISO($fechaNueva);
    $xCred	= new cCredito($credito); 
    $xCred->init();
    if($xCred->getPagosAutorizados() != $nuevosPagos){
    	$xCred->setUpdate(array("numero_pagos"=>$nuevosPagos, "pagos_autorizados" => $nuevosPagos), true);
    }
    $xCred->setCambiarMontoMinistrado($nuevoMonto);
    
    $msg	= $xCred->getMessages(OUT_HTML);
    return $msg;
}

function jsaCambiarEstadoActual($credito, $estado, $fechanueva){
    $xF		= new cFecha(0);
    $msg	= "";
    $fechanueva	=$xF->getFechaISO($fechanueva);
    
    //$fechaNueva	= $xF->getFechaISO($fechaNueva);
    $xCred	= new cCredito($credito); $xCred->init();
    $xCred->setCambiarEstadoActual($estado, $fechanueva);
    $msg	.= $xCred->getMessages(OUT_HTML);
    //$msg	.= "WARN\tCAMBIE EL PLAN DE PAGOS";
    return $msg;
}
function jsaCambiarProducto($credito, $producto, $tasa, $mora, $iddestino){
	$tasa	= $tasa /100;
	$mora	= $mora / 100;
	$xCred	= new cCredito($credito); $xCred->init();
	$xCred->setCambioProducto($producto, $tasa, $mora, $iddestino);
	$msg 	= $xCred->getMessages();
	return $msg;
}
function jsaCambiarPeriocidad($credito, $periocidad, $tipodepago, $pagos, $fecha, $pago_actual){
	//$xTCred	= new cCreditos_solicitud();
	$xCred	= new cCredito($credito); $xCred->init();
	if($xCred->getPeriodoActual() != $pago_actual){
		$xCred->setPeriodoActual($pago_actual);
	}
	$xCred->setCambiarPeriocidad($periocidad, $pagos, $tipodepago, $fecha);
	
	return $xCred->getMessages(OUT_HTML);
}
function jsaEliminarCredito($credito){
    $xCred	= new cCredito($credito); $xCred->init();
    $msg	= $xCred->setDelete();
    return $msg;
}
function jsaReestructurarIntereses($credito){
    $xCred	= new cCredito($credito); $xCred->init();
    $xCred->setReestructurarIntereses(false, false, true);
    $xCred->getInteresDevengado();
    
    $msg	= $xCred->getMessages(OUT_TXT);
    $xFLog	= new cFileLog("log-de-procesos-de-intereses", true);
    $xFLog->setWrite($msg);
    $xFLog->setClose();
    return $xFLog->getLinkDownload("Log de Intereses");
}

function jsaVincularEmpresa($credito, $observaciones, $empresa){
	
	$xCred	= new cCreditosDeNomina($credito);
	if($xCred->init() == true){
		if($empresa == FALLBACK_CLAVE_EMPRESA){
			$msg	= $xCred->setDesvincularEmpresa($observaciones);
		} else {
			$msg	= $xCred->setVincularEmpresa($empresa, $observaciones);
		}
		
	}

	return $msg;
}

function jsaSetCambiarPersona($credito, $nuevapersona){
	$xUtil	= new cUtileriasParaCreditos();
	
	$xUtil->setCambiarPersonaDeCredito($credito, $nuevapersona);
	return $xUtil->getMessages(OUT_HTML);
}

function jsaSetCat($credito){
	$xCred	= new cCredito($credito);
	if($xCred->init() == true){
		$xCred->setUpdate(array("tasa_cat" => 0), true);
		$xCred->getCAT();
	}
	return $xCred->getMessages(OUT_HTML);
}
function jsaSetEstatus($credito){
	$xCred	= new cCredito($credito);
	$exp	= "";
	if($xCred->init() == true){
		$exp	= $xCred->setDetermineDatosDeEstatus(false, true, true);
	}
	return $exp;
}
$jxc = new TinyAjax();
$jxc ->exportFunction('jsaCambiarMontoAutorizado', array('idsolicitud', 'idmontoaut', 'idnumeroaut'), "#avisos");
$jxc ->exportFunction('jsaCambiarMontoMinistrado', array('idsolicitud', 'idmonto'), "#avisos");
$jxc ->exportFunction('jsaCambiarFechaMinistracion', array('idsolicitud', 'idfecha-1'), "#avisos");
$jxc ->exportFunction('jsaCambiarEstadoActual', array('idsolicitud', 'idestadoactual', 'idfecha-3'), "#avisos");

$jxc ->exportFunction('jsaVincularEmpresa', array('idsolicitud', 'idobservacionesw', 'idcodigodeempresas'), "#avisos");

$jxc ->exportFunction('jsaCambiarProducto', array('idsolicitud', 'idpdto', 'idtasa', 'idtasamora', 'iddestinodecredito'), "#avisos");
$jxc ->exportFunction('jsaCambiarPeriocidad', array('idsolicitud', 'idperiocidad', 'idtipopago', 'idpagos', 'idfecha-2', 'idpagoactual'), "#avisos");

$jxc ->exportFunction('jsaEliminarCredito', array('idsolicitud'), "#avisos");
$jxc ->exportFunction('jsaReestructurarIntereses', array('idsolicitud'), "#avisos");
$jxc ->exportFunction('jsaSetCambiarPersona', array('idsolicitud', 'idnuevapersona'), "#avisos");

$jxc ->exportFunction('jsaSetCAT', array('idsolicitud'), "#avisos");
$jxc ->exportFunction('jsaSetEstatus', array('idsolicitud'), "#avisos");

//
$jxc ->process();

$xHP->init();

echo "<input type='hidden' id='idsolicitud' value='$credito'>";

$xCred	= new cCredito($credito); $xCred->init();

$xFRM	= new cHForm("frmrenegociar", "./", "idfrmmain");
$oBtn	= new cHButton();		
$oTxt	= new cHText();
$oHSel	= new cHSelect();
$oFch	= new cHDate(0);

$oSel	= new cSelect("");

$oUL	= new cHUl();

$lguardar	= $xFRM->lang("guardar");
$lcancelar	= $xFRM->lang("cancelar");

$xFRM->setTitle( $xHP->getTitle() );

$xFRM->OButton("TR.Cambiar Monto Ministrado", "jsCambiarMonto()", "mas-dinero", "idcambiarmonto" );
$xFRM->OButton("TR.Cambiar Fecha de Ministracion", "jsCambiarFechaMinistracion()", "fecha", "idcmdministracion" );
//$xFRM->addHElem( $oBtn->getBasic("Fecha de Autorizacion", "jsCambiarFechaAutorizacion", "fecha", "idcmdautorizacion" ) );
$xFRM->OButton("TR.Cambiar Monto Autorizado", "jsCambiarMontoAutorizado()", "dinero", "idcambiarmontoaut" ) ;
$xFRM->OButton("TR.Cambiar Estado", "jsCambiarEstado()", "trabajo", "idcambiarestado" );
$xFRM->OButton("TR.Cambiar Producto", "jsCambiarProducto()", "colaborar", "idcambiarpdto" );
$xFRM->OButton("TR.Cambiar Periocidad", "jsCambiarPeriocidad()", "calendario", "idcambiarpers" );

$xFRM->addHElem($xCred->getFichaMini());


if($xCred->getPeriocidadDePago() !=  CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO ){
	//$xFRM->OButton("TR.GENERAR PLAN_DE_PAGOS", "regenerarPlanDePagos()", "reporte", "generar-plan");
	$xFRM->OButton("TR.importar plan_de_pagos", "jsImportarPlanDePagos()", "csv", "idimportar");
	if($xCred->getNumeroDePlanDePagos() > 0){
		$idrecibo	= $xCred->getNumeroDePlanDePagos();
		$xFRM->OButton("TR.EDITAR PLAN_DE_PAGOS #$idrecibo", "jsEditarPlan($idrecibo)", $xFRM->ic()->EDITAR, "edit-plan");
		$xFRM->OButton("TR.EDITAR PLAN_DE_PAGOS CERO", "jsEditarPlan2($idrecibo)", $xFRM->ic()->CALENDARIO1, "edit-plan2");
	}
}


$xFRM->OButton("TR.vincular_a empresa", "jsVincularEmpresa()", "empresa", "idvincularemp" );
$xFRM->OButton("TR.Reestructurar Intereses", "jsaReestructurarIntereses();", "tasa", "idrestints"  );
$xFRM->OButton("TR.Cambiar Persona", "jsCambiarPersona()", $xFRM->ic()->PERSONA, "idchange"  );

$xFRM->OButton("TR.Actualizar CAT", "jsaSetCAT()", "tasa", "idacat"  );
$xFRM->OButton("TR.Actualizar ESTATUS", "jsaSetEstatus()", $xFRM->ic()->GENERAR );

$xNotaSIC			= new cCreditosNotasSIC();
if($xNotaSIC->initByCredito($xCred->getClaveDeCredito())  == true){
	$xFRM->OButton("TR.EDITAR NOTAS SIC", "jsEditNotaSic($credito)", $xFRM->ic()->NOTA  );
	$idnotasic	= $xNotaSIC->getClave();
	$xFRM->OButton("TR.ELIMINAR NOTAS SIC", "jsDelNotaSic($idnotasic)", $xFRM->ic()->ELIMINAR  );
} else {
	$xFRM->OButton("TR.AGREGAR NOTAS SIC", "jsAddNotaSic($credito)", $xFRM->ic()->NOTA  );
}

if(MODO_DEBUG == true OR (MODO_CORRECION == true OR MODO_MIGRACION == true)){
	$xFRM->OButton("TR.Borrado Permanente", "jsEliminarCredito()", "eliminar", "ideliminarcredito", "red"  );
}


$xFRM->addCerrar();

$xFRM->addHElem("<p id='avisos'></p>");
echo $xFRM->get();

$xCE	= new cCreditos_estatus();
$xSelEA	= $xCE->query()->html()->select( $xCE->descripcion_estatus()->get() );

$xCP	= new cCreditos_periocidadpagos();
$xSelCP	= $xCP->query()->html()->select( $xCP->descripcion_periocidadpagos()->get() );

$xCTP	= new cCreditos_tipo_de_pago();
$xSelTP	= $xCTP->query()->html()->select( $xCTP->descripcion()->get()  );

$xPP	= new cCreditos_tipoconvenio();
$xSelPP	= new cHSelect();//$xPP->query()->html()->select($xPP->descripcion_tipoconvenio()->get());

?>
<!--  MONTO MINISTRADO -->
<div class="inv formoid-default" id="divmontomin">
    <?php
	$oFrm2	= new cHForm("frmmonto", "", "idfrmmonto");
	$oFrm2->setNoFormTags();
	
	$oFrm2->OMoneda("idmonto", $xCred->getMontoAutorizado(), "TR.MONTO MINISTRADO");
	
	
	$oFrm2->addHTML(
		$oUL->li("Modificar el Monto que se autoriz&oacute;")->
		li("Eliminar Plan de Pagos")->
		li("Recalcular Intereses Devengados")->
		li("Reestructurar SDPM")->
		end()
	);
	$oFrm2->addGuardar("jsaCambiarMontoMinistrado()", "jsCancelarAccion()");
	echo $oFrm2->get(false);
    ?>
</div>
<!--  FECHA DE MINISTRACION -->
<div class="inv formoid-default" id="divfechamin">
    <?php
	$oFrm3	= new cHForm("frmfechamin", "", "idfrmfechamin");
	$oFrm3->setNoFormTags();
	$oFrm3->addHElem( $oFch->get($xFRM->lang("Fecha", "Nueva"), $xCred->getFechaDeMinistracion(), 1) );
	$oFrm3->addHTML("<ul><li>Modificar la Fecha de Ministraci&oacute;n</li>
	<li>Cambiar la Fecha del Recibo de Ministraci&oacute;n</li><li>Eliminar Plan de Pagos</li>
	<li>Reestructurar SDPM</li><li>Recalcular Intereses Devengados</li></ul>");
	$oFrm3->addGuardar("jsaCambiarFechaMinistracion()", "jsCancelarAccion()");

	echo $oFrm3->get();
    ?>
</div>
<!--  ESTADO ACTUAL -->
<div class="inv formoid-default" id="divestatus">
    <?php
	$oFrm4	= new cHForm("frmestatus", "", "idfrmestatus");
	$oFrm4->setNoFormTags();
	$oFrm4->addHElem( $xSelEA->get("idestadoactual", "Estado Actual", $xCred->getEstadoActual()) );
	$oFrm4->addHElem( $oFch->get("TR.Fecha Nueva", $xCred->getFechaDeVencimiento(), 3) );
	$oFrm4->addHTML(
			$oUL->li("Modificar la Fecha de Ministraci&oacute;n")->
			li("Cambiar la Fecha del Recibo de Ministraci&oacute;n")->
			li("Eliminar Plan de Pagos")->
			li("Recalcular Intereses Devengados")->
			li("Reestructurar SDPM")->
			end()
	);
	
	$oFrm4->addHTML("<p class='aviso'>No se puede afectan estatus VENCIDO/MOROSO</p>");
	$oFrm4->addGuardar("jsaCambiarEstadoActual()", "jsCancelarAccion()");

	echo $oFrm4->get(false);
    ?>
</div>
<!-- MONTO AUTORIZADO -->
<div class="inv formoid-default" id="divmontoautorizado">
    <?php
	$oFrm5	= new cHForm("frmmontoaut", "", "idfrmmontoaut");
	$oFrm5->setNoFormTags();
	$oFrm5->addHElem( $oTxt->getDeMoneda("idmontoaut", "TR.MONTO AUTORIZADO", $xCred->getMontoAutorizado()) );
	$oFrm5->addHElem( $oTxt->getDeMoneda("idnumeroaut", "TR.PAGOS AUTORIZADOS", $xCred->getPagosAutorizados()) );
	$oFrm5->addHTML($oUL->li("Modificar el Monto que se autoriz&oacute;")->end() );
	
	$oFrm5->addGuardar("jsaCambiarMontoAutorizado()", "jsCancelarAccion()");
	

	echo $oFrm5->get(false);
    ?>
</div>

<!--  PRODUCTO -->
<div class="inv formoid-default" id="divpdto">
    <?php
	$oFrm6	= new cHForm("frmpdto", "", "idfrmpdto");
	$oFrm6->setNoFormTags();
	$oFrm6->addHElem($xSelPP->getListaDeProductosDeCredito("idpdto", $xCred->getClaveDeProducto() )->get(true));
	
	$oFrm6->addHElem( $oTxt->getDeMoneda("idtasa", $xFRM->lang("Tasa", "Actual"), ($xCred->getTasaDeInteres()*100) ) );
	$oFrm6->addHElem( $oTxt->getDeMoneda("idtasamora", "TR.Tasa Moratorio", ($xCred->getTasaDeMora()*100) ) );
	
	$oFrm6->addHElem( $xSelPP->getListaDeDestinosDeCredito("", $xCred->getClaveDeDestino())->get(true) );
	
	$oFrm6->addHTML(
			$oUL->li("Modificar el Monto que se autoriz&oacute;")->
			li("Eliminar Plan de Pagos")->
			li("Recalcular Intereses Devengados")->
			li("Reestructurar SDPM")->
			end()
	);
	$oFrm6->addGuardar("jsaCambiarProducto()", "jsCancelarAccion()");

	echo $oFrm6->get(false);
    ?>
</div>
<!--  CAMBIAR EMPRESA -->
<div class="inv formoid-default" id="divcambiarsoc">
<?php 
$oFrm7	= new cHForm("frmcambiarpers", "", "idfrmcambiarpers");
$oFrm7->setNoFormTags();
$oTxt->setDivClass("");
$oFrm7->addHElem( $oTxt->getDeNombreDePersona("idnuevapersona", "", "TR.Nueva Persona") );
$oFrm7->addGuardar("jsSetCambiarPersona()", "jsCancelarAccion()");
echo $oFrm7->get();
?>
</div>
<!--  PERIOCIDAD DE PAGO -->
<div class="inv formoid-default" id="divperiocidad">
    <?php
	$oFrm8	= new cHForm("frmperiocidad", "", "idfrmperiocidad");
	$oFrm8->setNoFormTags();
	
	$oFrm8->addHElem( $xSelCP->get("idperiocidad", $xFRM->lang("Nueva", "Periocidad"), $xCred->getPeriocidadDePago() ) );
	$oFrm8->addHElem( $xSelTP->get("idtipopago", $xFRM->lang(array("Nuevo", "Tipo de", "Pago")), $xCred->getTipoDePago() ) );
	$oFrm8->addHElem( $oTxt->getDeMoneda("idpagos", $xFRM->lang("Numero de", "Pagos"), $xCred->getPagosAutorizados() ) );
	$oFrm8->addHElem( $oFch->get($xFRM->lang("Fecha de", "Vencimiento"), $xCred->getFechaDeVencimiento(), 2) );
	$oFrm8->OMoneda("idpagoactual", $xCred->getPeriodoActual(), "TR.Ultima Parcialidad");
	
	$oFrm8->addHTML(
			$oUL->li("Eliminar Plan de Pagos")->
			li("Reestructurar SDPM")->
			li("Recalcular Intereses Devengados")->
			li("Cambiar el Numero de Pagos")->
			li("Generar Movimiento de Fin de Mes")->
			end()
			);
	$oFrm8->addGuardar("jsaCambiarPeriocidad()", "jsCancelarAccion()");
	
	echo $oFrm8->get(false);
    ?>
</div>
<!--  VINCULAR A EMPRESA -->
<div class="inv formoid-default" id="divvincular">
    <?php
    $oHSel->setDivClass("");
	$oFrm9	= new cHForm("frmvincular", "", "idfrmvincular");
	$oFrm9->setNoFormTags();
	
	$oFrm9->addHElem( $oHSel->getListaDeEmpresas("", false, $xCred->getClaveDeEmpresa())->get("TR.EMPRESA" ) );
	$oTxt->setDivClass("");
	$oFrm9->addHElem( $oTxt->getDeObservaciones("idobservacionesw", "",  $xFRM->lang("observaciones")) );
	$oFrm9->addGuardar("jsaVincularEmpresa()", "jsCancelarAccion()");
	echo $oFrm9->get(false);
    ?>	
</div>
<!--  ELIMINAR -->
<div class="inv formoid-default" id="diveliminar">
    <h3>Acciones</h3>
    <ul>
	<li>Eliminar Credito</li>
	<li>Eliminar Operaciones</li>
	<li>Eliminar Recibos</li>
	<li>Eliminar Avales</li>
	<li>Eliminar Garantias</li>
	<li>Eliminar SDPM</li>
    </ul>
    <p class="aviso">Usted va a eliminar este cr&eacute;dito; No hay como deshacer este evento.</p>
    <input type="button" value="Aceptar" onclick="jsConfirmarEliminarCredito();">
    <input type="button" value="Cancelar" onclick="jsCancelarAccion()">
</div>



<?php
/*echo $xHP->setBodyEnd();
$jsb	= new jsBasicForm("frmrenegociar");
$jsb->show();*/

$jxc ->drawJavaScript(false, true);
?>

<script>
var xGen	= new Gen();
var ogen	= new Gen();
var mobj	= "#menu-nav";
var idCredito	= <?php echo $xCred->getNumeroDeCredito(); ?>;
var idSocio		= <?php echo $xCred->getClaveDePersona(); ?>;
var idRecibo	= <?php echo $idrecibo; ?>;
    
function jsCambiarEstado(){  			xGen.postajax("jsCancelarTip()"); getModalTip(mobj, $("#divestatus"), xGen.lang(["Modificar", "Estado"]));   }
function jsCambiarMonto(){ 				xGen.postajax("jsCancelarTip()"); getModalTip(mobj, $("#divmontomin"), xGen.lang(["Modificar" ,"Monto", "Ministrado"]));  }
function jsCambiarFechaMinistracion(){	xGen.postajax("jsCancelarTip()"); getModalTip(mobj, $("#divfechamin"), xGen.lang(["Modificar", "Fecha_de",  "Ministracion"]) );  }
function jsCambiarProducto(){ 			xGen.postajax("jsCancelarTip()"); getModalTip(mobj, $("#divpdto"), xGen.lang(["Modificar", "Producto"]));  }
function jsCambiarPeriocidad(){			xGen.postajax("jsCancelarTip()"); getModalTip(mobj, $("#divperiocidad"), xGen.lang(["Modificar", "Periocidad"]));    }    
function jsEliminarCredito(){ 			xGen.postajax("jsCancelarTip()"); getModalTip(mobj, $("#diveliminar"), xGen.lang(["Eliminar", "Credito"]) );  }
function jsCambiarPersona(){			xGen.postajax("jsCancelarTip()"); getModalTip(mobj, $("#divcambiarsoc"), xGen.lang(["Cambiar", "Persona"]) );  }
function jsConfirmarEliminarCredito(){
	var sip	= confirm("Esta seguro de Eliminar el credito?\nNO HAY FORMA DE DESHACERLO.");
	if (sip){ jsaEliminarCredito(); } else { jsCancelarAccion(); }
}
function jsCancelarAccion(){ jsCancelarTip(); }
function jsCancelarTip(){
	var vid = session(Configuracion.opciones.dialogID);
	$("#" + vid).dialog('close');
	 
}
function jsCambiarMontoAutorizado() {
	var xTit	= xGen.lang( ["cambiar", "Monto", "Autorizado"] );
	xGen.postajax("jsCancelarTip()"); 
	
	getModalTip(mobj, $("#divmontoautorizado"), xTit);
}
function jsVincularEmpresa() {   xGen.postajax("jsCancelarTip()");  getModalTip(mobj, $("#divvincular"), xGen.lang(["vincular_a", "empresa"]));	}

function jsTipTimer(){ setTimeout("jsReTipTimer()", 500); }
function jsReTipTimer(){ tip(mobj, "Espere...!", 5000, false); }

function jsImportarPlanDePagos(){	xGen.w({ url: '../frmcreditos/importar.plan_de_pagos.frm.php?credito=' + idCredito, tab : true }); 	}
function regenerarPlanDePagos(){ xGen.w({ url: '../frmcreditos/frmcreditosplandepagos.php?r=1&c=' + idCredito + "&s=" + idSocio }); }
function jsEditarPlan(mPlan){ 	xGen.w({ url: '../frmcreditos/plan_de_pagos.edicion.frm.php?activeplan=true&recibo=' + mPlan, tab:true }); }
function jsEditarPlan2(mPlan){ 	xGen.w({ url: '../frmcreditos/plan_de_pagos.edicion.frm.php?sinceros=true&activeplan=true&recibo=' + mPlan, tab:true }); }
function jsSetCambiarPersona(){ xGen.confirmar({msg: "Confirma el cambio de persona?" , callback : "jsaSetCambiarPersona()"}); }
function jsAddNotaSic(idCredito){
	xGen.w({ url: '../frmcreditos/nota-sic.new.frm.php?credito=' + idCredito, tiny : true });
}
function jsEditNotaSic(idCredito){
	xGen.w({ url: '../frmcreditos/nota-sic.edit.frm.php?credito=' + idCredito, tiny : true });
}
function jsDelNotaSic(id){
	xG.rmRecord({tabla:"creditos_sic_notas", id:id});
}
</script>
<?php
$xHP->fin();
?>