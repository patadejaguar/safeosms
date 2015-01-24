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
    $xCred	= new cCredito($credito); $xCred->init();
    $msg	= $xCred->setCambiarMontoMinistrado($nuevoMonto);
    $msg	.= "<br>CAMBIE EL PLAN DE PAGOS";
    return $msg;
}
function jsaCambiarMontoAutorizado($credito, $nuevoMonto){
    $xF		= new cFecha(0);
    //$fechaNueva	= $xF->getFechaISO($fechaNueva);
    $xCred	= new cCredito($credito); $xCred->init();
    $msg	= $xCred->setCambiarMontoMinistrado($nuevoMonto);
    $msg	.= "<br>CAMBIE EL PLAN DE PAGOS";
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
function jsaCambiarProducto($credito, $producto, $tasa, $mora){
	$tasa	= $tasa /100;
	$mora	= $mora / 100;
	$xCred	= new cCredito($credito); $xCred->init();
	$msg	= $xCred->setCambioProducto($producto, $tasa, $mora);
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
    
    $msg	= $xCred->getMessages(OUT_TXT);
    $xFLog	= new cFileLog("log-de-procesos-de-intereses", true);
    $xFLog->setWrite($msg);
    $xFLog->setClose();
    return $xFLog->getLinkDownload("Log de Intereses");
}

function jsaVincularEmpresa($credito, $observaciones, $empresa){
    $msg		= "";
    $xCred		= new cCredito($credito);
    $xCred->init();
    
    $xdat	= new cFecha(0);
    $fecha	= $xdat->get();// FechaISO($fecha);

    $xCred->init();
    $socio	= $xCred->getClaveDePersona();
    $xSoc	= new cSocio($socio);
    $xSoc->init();

    $xCred->setCambioProducto( CREDITO_PRODUCTO_NOMINA );
    $xCred->setResetPersonaAsociada($fecha, $observaciones, $empresa);
    //Agregar operacion de desvinculacion
    $xRe	= new cReciboDeOperacion(RECIBOS_TIPO_ESTADISTICO, false, DEFAULT_RECIBO);
    $xRe->init();
    $xRe->setNuevoMvto($fecha, $xCred->getSaldoActual(), OPERACION_CLAVE_VINCULACION, $xCred->getPeriodoActual(), "", 1, false, 
    $socio, $credito, $fecha);
    $xRe->setFinalizarRecibo();
    
    $msg	.= $xSoc->getMessages(OUT_TXT);
    $msg	.= $xCred->getMessages(OUT_TXT);
    $msg	.= $xRe->getMessages(OUT_TXT);
    
    $xF		= new cFileLog();
    $xF->setWrite($msg);
    $xF->setClose();
    
    return  $xF->getLinkDownload("Descarga de Log");
}

function jsaSetCambiarPersona($credito, $nuevapersona){
	$xUtil	= new cUtileriasParaCreditos();
	
	$xUtil->setCambiarPersonaDeCredito($credito, $nuevapersona);
	return $xUtil->getMessages(OUT_HTML);
}


$jxc = new TinyAjax();
$jxc ->exportFunction('jsaCambiarMontoAutorizado', array('idsolicitud', 'idmontoaut'), "#avisos");
$jxc ->exportFunction('jsaCambiarMontoMinistrado', array('idsolicitud', 'idmonto'), "#avisos");
$jxc ->exportFunction('jsaCambiarFechaMinistracion', array('idsolicitud', 'idfecha-1'), "#avisos");
$jxc ->exportFunction('jsaCambiarEstadoActual', array('idsolicitud', 'idestadoactual', 'idfecha-3'), "#avisos");

$jxc ->exportFunction('jsaVincularEmpresa', array('idsolicitud', 'idobservacionesw', 'idcodigodeempresas'), "#avisos");

$jxc ->exportFunction('jsaCambiarProducto', array('idsolicitud', 'idpdto', 'idtasa', 'idtasamora'), "#avisos");
$jxc ->exportFunction('jsaCambiarPeriocidad', array('idsolicitud', 'idperiocidad', 'idtipopago', 'idpagos', 'idfecha-2', 'idpagoactual'), "#avisos");

$jxc ->exportFunction('jsaEliminarCredito', array('idsolicitud'), "#avisos");
$jxc ->exportFunction('jsaReestructurarIntereses', array('idsolicitud'), "#avisos");
$jxc ->exportFunction('jsaSetCambiarPersona', array('idsolicitud', 'idnuevapersona'), "#avisos");
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

if($xCred->getPeriocidadDePago() !=  CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO ){
	$xFRM->OButton("TR.GENERAR PLAN_DE_PAGOS", "regenerarPlanDePagos()", "reporte", "generar-plan");
	$xFRM->OButton("TR.importar plan_de_pagos", "jsImportarPlanDePagos()", "csv", "idimportar");
	if($xCred->getNumeroDePlanDePagos() > 0){
		$idrecibo	= $xCred->getNumeroDePlanDePagos();
		$xFRM->OButton("TR.EDITAR PLAN_DE_PAGOS #$idrecibo", "jsEditarPlan($idrecibo)", "editar", "edit-plan");
	}
}


$xFRM->OButton("TR.vincular_a empresa", "jsVincularEmpresa()", "empresa", "idvincularemp" );
$xFRM->OButton("TR.Reestructurar Intereses", "jsaReestructurarIntereses();jsTipTimer()", "tasa", "idrestints"  );
$xFRM->OButton("TR.Cambiar Persona", "jsCambiarPersona()", $xFRM->ic()->PERSONA, "idchange"  );
$xFRM->OButton("TR.Borrado Permanente", "jsEliminarCredito()", "eliminar", "ideliminar"  );

$xFRM->OButton($lcancelar, "jsCancelarAccion()", "salir", "idsalir" );

$xFRM->addHElem("<p id='avisos'></p>");
echo $xFRM->get();

$xCE	= new cCreditos_estatus();
$xSelEA	= $xCE->query()->html()->select( $xCE->descripcion_estatus()->get() );

$xCP	= new cCreditos_periocidadpagos();
$xSelCP	= $xCP->query()->html()->select( $xCP->descripcion_periocidadpagos()->get() );

$xCTP	= new cCreditos_tipo_de_pago();
$xSelTP	= $xCTP->query()->html()->select( $xCTP->descripcion()->get()  );

$xPP	= new cCreditos_tipoconvenio();
$xSelPP	= $xPP->query()->html()->select($xPP->descripcion_tipoconvenio()->get());

?>
<!--  MONTO MINISTRADO -->
<div class="inv" id="divmontomin">
    <?php
	$oFrm2	= new cHForm("frmmonto", "", "idfrmmonto");
	$oFrm2->addHElem( $oTxt->getDeMoneda("idmonto", "", $xCred->getMontoAutorizado()) );
	$oFrm2->addHTML(
		$oUL->li("Modificar el Monto que se autoriz&oacute;")->
		li("Eliminar Plan de Pagos")->
		li("Recalcular Intereses Devengados")->
		li("Reestructurar SDPM")->
		end()
	);
	$oFrm2->addFootElement($oBtn->getBasic($lguardar, "jsaCambiarMontoMinistrado();jsTipTimer()", "guardar", "idmonto" ) );
	$oFrm2->addFootElement($oBtn->getBasic($lcancelar, "jsCancelarAccion()", "cancelar", "idcancela2" ) );
	echo $oFrm2->get();
    ?>
</div>
<!--  FECHA DE MINISTRACION -->
<div class="inv" id="divfechamin">
    <?php
	$oFrm3	= new cHForm("frmfechamin", "", "idfrmfechamin");
	$oFrm3->addHElem( $oFch->get($xFRM->lang("Fecha", "Nueva"), $xCred->getFechaDeMinistracion(), 1) );
	$oFrm3->addHTML("<ul><li>Modificar la Fecha de Ministraci&oacute;n</li>
	<li>Cambiar la Fecha del Recibo de Ministraci&oacute;n</li><li>Eliminar Plan de Pagos</li>
	<li>Reestructurar SDPM</li><li>Recalcular Intereses Devengados</li></ul>");
	$oFrm3->addFootElement($oBtn->getBasic($lguardar, "jsaCambiarFechaMinistracion();jsTipTimer()", "guardar", "idsafechamin" ) );
	$oFrm3->addFootElement($oBtn->getBasic($lcancelar, "jsCancelarAccion()", "cancelar", "idcancela3" ) );
	echo $oFrm3->get();
    ?>
</div>
<!-- MONTO AUTORIZADO -->
<div class="inv" id="divmontoautorizado">
    <?php
	$oFrm5	= new cHForm("frmmontoaut", "", "idfrmmontoaut");
	$oFrm5->addHElem( $oTxt->getDeMoneda("idmontoaut", "", $xCred->getMontoAutorizado()) );
	$oFrm5->addHTML($oUL->li("Modificar el Monto que se autoriz&oacute;")->end() );
	$oFrm5->addFootElement($oBtn->getBasic($lguardar, "jsaCambiarMontoAutorizado();jsTipTimer()", "guardar", "idmonto" ) );
	$oFrm5->addFootElement($oBtn->getBasic($lcancelar, "jsCancelarAccion()", "cancelar", "idcancela5" ) );
	echo $oFrm5->get();
    ?>
</div>
<!--  ESTADO ACTUAL -->
<div class="inv" id="divestatus">
    <?php
	$oFrm4	= new cHForm("frmestatus", "", "idfrmestatus");
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
	
	$oFrm4->addFootElement($oBtn->getBasic($lguardar, "jsaCambiarEstadoActual();jsTipTimer()", "guardar", "idsafechamin" ) );
	$oFrm4->addFootElement($oBtn->getBasic($lcancelar, "jsCancelarAccion()", "cancelar", "idcancela4" ) );
	echo $oFrm4->get();
    ?>
</div>
<!--  PRODUCTO -->
<div class="inv" id="divpdto">
    <?php
	$oFrm6	= new cHForm("frmpdto", "", "idfrmpdto");
	$oFrm6->addHElem( $xSelPP->get("idpdto", $xFRM->lang("Producto", "Actual"), $xCred->getClaveDeProducto() ) );
	$oFrm6->addHElem( $oTxt->getDeMoneda("idtasa", $xFRM->lang("Tasa", "Actual"), ($xCred->getTasaDeInteres()*100) ) );
	$oFrm6->addHElem( $oTxt->getDeMoneda("idtasamora", "TR.Tasa Moratorio", ($xCred->getTasaDeMora()*100) ) );
	$oFrm6->addHTML(
			$oUL->li("Modificar el Monto que se autoriz&oacute;")->
			li("Eliminar Plan de Pagos")->
			li("Recalcular Intereses Devengados")->
			li("Reestructurar SDPM")->
			end()
	);
	//$oFrm6->addHTML("<p class='aviso'></p>");
	
	$oFrm6->addFootElement($oBtn->getBasic($lguardar, "jsaCambiarProducto();jsTipTimer()", "guardar", "idsapdto" ) );
	$oFrm6->addFootElement($oBtn->getBasic($lcancelar, "jsCancelarAccion()", "cancelar", "idcancela6" ) );
	echo $oFrm6->get();
    ?>
</div>

<!--  PERIOCIDAD DE PAGO -->
<div class="inv" id="divperiocidad">
    <?php
	$oFrm5	= new cHForm("frmperiocidad", "", "idfrmperiocidad");
	$oFrm5->addHElem( $xSelCP->get("idperiocidad", $xFRM->lang("Nueva", "Periocidad"), $xCred->getPeriocidadDePago() ) );
	$oFrm5->addHElem( $xSelTP->get("idtipopago", $xFRM->lang(array("Nuevo", "Tipo de", "Pago")), $xCred->getTipoDePago() ) );
	$oFrm5->addHElem( $oTxt->getDeMoneda("idpagos", $xFRM->lang("Numero de", "Pagos"), $xCred->getPagosAutorizados() ) );
	$oFrm5->addHElem( $oFch->get($xFRM->lang("Fecha de", "Vencimiento"), $xCred->getFechaDeVencimiento(), 2) );
	$oFrm5->OMoneda("idpagoactual", $xCred->getPeriodoActual(), "TR.Ultima Parcialidad");
	
	$oFrm5->addHTML(
			$oUL->li("Eliminar Plan de Pagos")->
			li("Reestructurar SDPM")->
			li("Recalcular Intereses Devengados")->
			li("Cambiar el Numero de Pagos")->
			li("Generar Movimiento de Fin de Mes")->
			end()
			);
	$oFrm5->addFootElement($oBtn->getBasic($lguardar, "jsaCambiarPeriocidad();jsTipTimer()", "guardar", "idmonto" ) );
	$oFrm5->addFootElement($oBtn->getBasic($lcancelar, "jsCancelarAccion()", "cancelar", "idcancela" ) );
	echo $oFrm5->get();
    ?>
</div>

<!--  ELIMINAR -->
<div class="inv" id="diveliminar">
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
    <input type="button" value="Aceptar" onclick="jsConfirmarEliminarCredito();jsTipTimer()">
    <input type="button" value="Cancelar" onclick="jsCancelarAccion()">
</div>

<div class="inv" id="divvincular">
    <?php
	$oFrm6	= new cHForm("frmvincular", "", "idfrmvincular");
	$oFrm6->addHElem( $oHSel->getListaDeEmpresas()->get($xFRM->lang("vincular", "empresa"), true ) );
	/*$oFrm6->addHTML(
		$oUL->li("Modificar el Monto que se autoriz&oacute;")->
		li("Eliminar Plan de Pagos")->
		li("Recalcular Intereses Devengados")->
		li("Reestructurar SDPM")->
		end()
	);*/
	$oFrm6->addHElem( $oTxt->getDeObservaciones("idobservacionesw", "",  $xFRM->lang("observaciones")) );
	$oFrm6->addFootElement($oBtn->getBasic($lguardar, "jsaVincularEmpresa();jsTipTimer()", "guardar", "idvinculocmd" ) );
	$oFrm6->addFootElement($oBtn->getBasic($lcancelar, "jsCancelarAccion()", "cancelar", "idcancela6" ) );
	echo $oFrm6->get();
    ?>	
</div>
<div class="inv" id="divcabiarsoc">
<?php 
$oFrm7	= new cHForm("frmcambiarpers", "", "idfrmcambiarpers"); $oFrm7->addHElem( $oTxt->getDeNombreDePersona("idnuevapersona", "", "TR.Nueva Persona") );
$oFrm7->addSubmit("", "jsSetCambiarPersona()", "jsCancelarAccion()");
echo $oFrm7->get();
?>
</div>
<?php
echo $xHP->setBodyEnd();
$jsb	= new jsBasicForm("frmrenegociar");
$jsb->show();
$jxc ->drawJavaScript(false, true);
?>

<script>
    var xGen	= new Gen();
    var ogen	= new Gen();
    var mobj	= "#avisos";
	var idCredito	= <?php echo $xCred->getNumeroDeCredito(); ?>;
	var idSocio		= <?php echo $xCred->getClaveDePersona(); ?>;
	var idRecibo	= <?php echo $idrecibo; ?>;
    
    function jsCambiarEstado(){ 				getModalTip(mobj, $("#divestatus"), xGen.lang(["Modificar", "Estado"]));   }
    function jsCambiarMonto(){ 				getModalTip(mobj, $("#divmontomin"), xGen.lang(["Modificar" ,"Monto", "Ministrado"]));  }
    function jsCambiarFechaMinistracion() {	getModalTip(mobj, $("#divfechamin"), xGen.lang(["Modificar", "Fecha_de",  "Ministracion"]) );  }
    function jsCambiarProducto(){ 				getModalTip(mobj, $("#divpdto"), xGen.lang(["Modificar", "Producto"]));  }
    function jsCambiarPeriocidad(){			getModalTip(mobj, $("#divperiocidad"), xGen.lang(["Modificar", "Periocidad"]));    }    
    function jsEliminarCredito(){ 				getModalTip(mobj, $("#diveliminar"), xGen.lang(["Eliminar", "Credito"]) );  }
    function jsCambiarPersona(){			getModalTip(mobj, $("#divcabiarsoc"), xGen.lang(["Cambiar", "Persona"]) );  }
    function jsConfirmarEliminarCredito(){
		var sip	= confirm("Esta seguro de Eliminar el credito?\nNO HAY FORMA DE DESHACERLO.");
		if (sip){ jsaEliminarCredito(); } else { jsCancelarAccion(); }
    }
    function jsCancelarAccion(){	$(mobj).qtip("hide");	xGen.close();    }
    function jsCancelarTip(){	$(mobj).qtip("hide");    }
    function jsCambiarMontoAutorizado() {
		var xTit	= xGen.lang( ["cambiar", "Monto", "Autorizado"] );
		getModalTip(window, $("#divmontoautorizado"), xTit);
    }
	function jsVincularEmpresa() {  getModalTip(window, $("#divvincular"), xGen.lang(["vincular_a", "empresa"]));	}
	function jsTipTimer(){ setTimeout("jsReTipTimer()", 500); }
	function jsReTipTimer(){ tip(mobj, "Espere...!", 5000, false); }
	function jsImportarPlanDePagos(){	ogen.w({ url: '../frmcreditos/importar.plan_de_pagos.frm.php?credito=' + idCredito, tiny : true }); 	}
	function regenerarPlanDePagos(){ ogen.w({ url: '../frmcreditos/frmcreditosplandepagos.php?r=1&c=' + idCredito + "&s=" + idSocio }); }
	function jsEditarPlan(mPlan){ 	ogen.w({ url: '../frmcreditos/plan_de_pagos.edicion.frm.php?i=' + mPlan }); }
	function jsSetCambiarPersona(){ xGen.confirmar({msg: "Confirma el cambio de persona?" , callback : "jsaSetCambiarPersona()"}); }
</script>
<?php
$xHP->end();
?>