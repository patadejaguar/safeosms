<?php
/**
 * Titulo: Creditos Autorizados
 * @since Actualizado: 27-Agosto-2007
 * @author Responsable: Balam Gonzalez Luis
 * Funcion: Autoriza los Creditos
 * Se modifico el saldo actual del Mopnto Autorizado a Cero, este cambio se hara hasta la ministracion
 * 20080602	Se efectuaron algunas modificaciones menores
 * 20080702	Mejor soporte en Datos de Creditos
 * 20080722	Se Agrego el Documento de Autorizacion para Grupos Solidarios
 * 2011-02-01 se corrigio la fecha arrojada en min e inicio de pagos
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
$xHP			= new cHPage("TR.MODULO DE AUTORIZACION", HP_FORM);
$xRuls			= new cReglaDeNegocio();
$xF				= new cFecha();
$SinTasa		= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_AUTORIZACION_SIN_TASA);		//regla de negocio
$SinLugarPag	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_AUTORIZACION_SIN_LUGAR);		//regla de negocio Sin lugar de pago
$SinTipoDisp	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_AUTORIZACION_SIN_DISP);		//regla de negocio Sin Tipo Dispersion
$PuedeTasaCero	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_PUEDEN_TASA_CERO);
$SinTipoAut		= false;


$oficial 		= elusuario($iduser);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xFRM			= new cHForm("frmcreditoautorizado", "frmcreditosautorizados.php?action=2", "frmcreditoautorizado");
$xSel			= new cHSelect();
$xTxt			= new cHText();
$xTxt2			= new cHText();
$xBtn			= new cHButton();
$xLog			= new cCoreLog();
$jxc			= new TinyAjax();
$msg			= "";
$jsInit			= "";
$txt			= "";
$remoto			= false;
$xCred			= null;


$pagos_autorizados		= 0;
$monto_autorizado		= 0;
$TipoDeAutorizacion		= FALLBACK_CREDITOS_TIPO_AUTORIZACION;
$NivelDeRiesgo			= FALLBACK_CREDITOS_RIESGO;
$TipoDePeriocidad		= DEFAULT_PERIOCIDAD_PAGO;
$TipoDePago				= FALLBACK_CREDITOS_TIPO_PAGO;
$TasaDeInteres	        = 0;
$fecha_de_ministracion	= $xF->get();
$fecha_de_autorizacion	= $xF->get();
$TipoDeDispersion		= FALLBACK_CREDITOS_TIPO_DESEMBOLSO;
$TipoDeLugarDeCobro		= FALLBACK_CREDITOS_LUGAR_DE_PAGO;
$cedula_grupal			= 0;

if( setNoMenorQueCero($credito) > DEFAULT_CREDITO){
	$xCred	= new cCredito($credito);
	$xCred->init();
	
	if($xCred->getEsValido() == false){
		$xHP->goToPageError(20011);
	}
		
	$persona	= $xCred->getClaveDePersona();
	$jsInit		= "jsInit()";
	getPersonaEnSession($persona);
	$remoto			= true;
	
	$pagos_autorizados		= $xCred->getPagosSolicitados();
	$monto_autorizado		= $xCred->getMontoSolicitado();
	$TipoDeAutorizacion		= $xCred->getTipoDeAutorizacion();
	$NivelDeRiesgo			= $xCred->getNiVelDeRiesgo();
	$TipoDePeriocidad		= $xCred->getPeriocidadDePago();
	$TipoDePago				= $xCred->getTipoDePago();
	$TasaDeInteres	        = $xCred->getTasaDeInteres();
	$fecha_de_ministracion	= $xCred->getFechaDeMinistracion();
	$fecha_de_autorizacion	= $xCred->getFechaDeAutorizacion();	
}
$xFRM->addDataTag("role", "autorizacion");

function jsaGetDatos($solicitud){
	if( $solicitud!=0 and $solicitud!='' ){
		$xCred			= new cCreditos_solicitud();
		$xCred->setData($xCred->query()->getRow("numero_solicitud=$solicitud"));
		$OCred			= new cCredito($solicitud); $OCred->init();		
		$pagos			= $xCred->numero_pagos()->v();
		$monto			= $xCred->monto_solicitado()->v();
		$periocidad		= $xCred->periocidad_de_pago()->v();
		$tasa			= $xCred->tasa_interes()->v();
		
		$xF				= new cFecha();
		$xT				= new cTipos();

			$tab = new TinyAjaxBehavior();
			$tab -> add(TabSetvalue::getBehavior('idpagos', $pagos));
			$tab -> add(TabSetvalue::getBehavior('idmonto', $monto));
			$tab -> add(TabSetvalue::getBehavior('idtasa', $tasa*100));
			$tab -> add(TabSetvalue::getBehavior('idperiocidad', $periocidad));
			$tab -> add(TabSetvalue::getBehavior('idtipodepago', $xCred->tipo_de_pago()->v()));
			//Fechas de ministracion
			$tab -> add(TabSetvalue::getBehavior('idfecha1', $xF->getFechaMX( $xCred->fecha_autorizacion()->v(), "-" ) ));
			$tab -> add(TabSetvalue::getBehavior('idfecha2', $xF->getFechaMX($xCred->fecha_ministracion()->v(), "-") ));
			
			$tab -> add(TabSetvalue::getBehavior('idautorizacion', $xCred->docto_autorizacion()->v() ));

			//$tab -> add(TabSetvalue::getBehavior('idtipodeautorizacion', $xCred->tipo_autorizacion()->v() ));
			
			$tab -> add(TabSetvalue::getBehavior('idtipodispersion', $xCred->tipo_de_dispersion()->v() ));
			$tab -> add(TabSetvalue::getBehavior('idtipolugarcobro', $xCred->tipo_de_lugar_de_pago()->v() ));
			
			if($OCred->getEsValido() == false){
				$tab -> add(TabSetvalue::getBehavior('idvalido', "0" ));
			} else {
				$tab -> add(TabSetvalue::getBehavior('idvalido', "1" ));
			}
			return $tab -> getString();
	}
}
function getListadoDeGrupoParaGuardar($solicitud, $socio){
	$xNot			= new cHNotif();
	$xCred 			= new cCredito($solicitud, $socio);
	$xCred->initCredito();
	$DCred			= $xCred->getDatosDeCredito();
	$OConv			= $xCred->getOProductoDeCredito();
	$grupo			= $xCred->getClaveDeGrupo();
	$body			= "";
	$elements		= 0;
	$avisos			= "";

	if ($OConv->getEsProductoDeGrupos() == true){
		$body		.= "<p class='aviso'>SE HA DETECTADO QUE ESTE CREDITO ES DEL GRUPO $grupo</p>";
		$xGrp		= new cGrupo($grupo);
		$DPlan		= $xGrp->getDatosDePlaneacionInArray();
		$recibo		= $DPlan["idoperaciones_recibos"];
		$presidenta	= $xGrp->getRepresentanteCodigo();
		
		$tds	= "";
		 if ( isset($recibo) ){
		 	$body .= "<p class='aviso'>SE CARGAN DATOS DE LA PLANEACION # $recibo</p>";
		 	$sql = "SELECT
					`operaciones_mvtos`.`tipo_operacion`,
					`operaciones_mvtos`.`recibo_afectado`,
					`socios_general`.`codigo`,
					CONCAT(`socios_general`.`apellidopaterno`, ' ',
					`socios_general`.`apellidomaterno`, ' ',
					`socios_general`.`nombrecompleto`) AS 'nombre',
					`operaciones_mvtos`.`afectacion_real` AS 'monto',
					`operaciones_mvtos`.`detalles`
				FROM
					`operaciones_mvtos` `operaciones_mvtos`
						INNER JOIN `socios_general` `socios_general`
						ON `operaciones_mvtos`.`socio_afectado` = `socios_general`.`codigo`
				WHERE
					(`operaciones_mvtos`.`tipo_operacion` =112) AND
					(`operaciones_mvtos`.`recibo_afectado` =$recibo)";
		 	$rs = mysql_query($sql, cnnGeneral());
		 		while($rw = mysql_fetch_array($rs)){
		 			$socio	= $rw["codigo"];

					$tds .= "
							<tr id=\"tr-$grupo-$socio\">
								<th>" . $socio . " <input type=\"hidden\" id=\"socio-$grupo-$socio\" value=\"" . $socio . "\" /></th>
								<td>" . htmlentities($rw["nombre"]) . "</td>
								<td><input type=\"text\" id=\"monto-$grupo-$socio\" value=\"" . $rw["monto"] . "\" class='mny' onchange=\"jsUpdateAutorizacion();\" maxlength=\"20\" /></td>
								<td><input type=\"text\" id=\"detalles-$grupo-$socio\" value=\"" . htmlentities($rw["detalles"]) . "\" maxlength=\"60\" /></td>
							</tr>";
					$elements++;
		 		}
		 		$body .= "<fieldset>
							<legend>|&nbsp;&nbsp;GUARDAR DATOS DE LA AUTORIZACION POR GRUPO&nbsp;&nbsp;|</legend>
								<table width='100%' align='center'>
							<th>Socio(a)</th>
							<th>Nombre Completo</th>
							<th>Monto Autorizado</th>
							<th>Observaciones</th>
									<tbody>
										$tds
									</tbody>
									<th colspan='4'><a class='button' id='icmdGoGroup' onclick='jsSavePlaneacion($elements)'>Guardar Autorizacion Grupal y Enviar Autorizacion</a></th>
								</table>
							</fieldset>";
		 //Crear el recibo

						$fecha		= fechasys();
						$oficial 	= elusuario($_SESSION["SN_b80bb7740288fda1f201890375a60c8f"]);
		 		$xRec	= setNuevorecibo($presidenta, $solicitud, $fecha, 1, 40, "CREDITO_DE_GRUPO_ELABORADO_POR_$oficial", DEFAULT_CHEQUE, FALLBACK_TIPO_PAGO_CAJA,
		 								DEFAULT_RECIBO_FISCAL, $grupo );
		 		$body .= "<p class='aviso'>Se Crea el Recibo # $xRec para Trabajar</p>";
				if ( isset($_SESSION["recibo_en_proceso"]) ){
					$body .= "<p class='aviso'>Se Ha Liberado el Recibo # "  . $_SESSION["recibo_en_proceso"] . " para Trabajar con # $xRec</p>";
		 			unset($_SESSION["recibo_en_proceso"]);
				}
		 		$_SESSION["recibo_en_proceso"] = $xRec;

		 }
	} else {
		//XXX: COOOOOOOOOOOOOOOOOOOOOOOOOOOREGIR
		$body	.= $xNot->get("Credito Sin problemas<input type='hidden' id='idesautorizado' />");
	}
		$body	.= $avisos;
		return $body;
}
function jsaSetSaveRechazados($solicitud, $texto, $fecha){	$xCred		= new cCredito($solicitud); 	$xCred->init(); 	$xCred->setRazonRechazo($texto, "", $fecha); }


$jxc ->exportFunction('jsaGetDatos', array('idsolicitud'));
$jxc ->exportFunction('jsaSetSaveRechazados', array('idsolicitud', 'txtRazones', 'idfecha1'));
$jxc ->exportFunction('getListadoDeGrupoParaGuardar', array('idsolicitud', "idsocio"), "#informacion");
$jxc ->process();

$xHP->init($jsInit);
$xFRM->setTitle($xHP->getTitle());


if  ( $action == SYS_DOS ){
	//$cTipo	= new cTipos();
	/* ----------------------------------------- MUEVE EL CREDITO AUTORIZADO y FILTRA ------------------------ */
	$xF							= new cFecha();
	$idsolicitud				= $credito;
	if (setNoMenorQueCero($idsolicitud) <= DEFAULT_CREDITO){
		echo("<p class='aviso'>C&Oacute;DIGO DE SOLICITUD INCORRECTA</p>");
	} else {
		$xCred 					= new cCredito($idsolicitud);
		$xCred->init();
		if($xCred->getEsValido() == false){
			$xHP->goToPageError(20011);
		}
		
		$idpagos 				= parametro("idpagos", $xCred->getPagosSolicitados(), MQL_INT);
		$idmonto 				= parametro("idmonto", $xCred->getMontoSolicitado(), MQL_FLOAT);
		$idautorizacion 		= parametro("idautorizacion");
		$sdoactual 				= 0;					//Saldo Actual es igual a Cero, hasta la ministracion se cambia
		$idnivelderiesgo		= parametro("idnivelderiesgo", $xCred->getNiVelDeRiesgo(), MQL_INT);
		$periocidad				= parametro("idperiocidad", $xCred->getPeriocidadDePago(), MQL_INT);
		$idtipodepago		    = parametro("idtipodepago", $xCred->getTipoDePago(), MQL_INT);
	
		$TipoDeAutorizacion	    = parametro("idtipodeautorizacion", $xCred->getTipoDeAutorizacion(), MQL_INT);
		$TasaDeInteres	        = parametro("idtasa", 0, MQL_FLOAT);
		$avisos					= "";
		$fecha1					= parametro("idfecha1", $xCred->getFechaDeAutorizacion(), MQL_DATE);
		$fecha2					= parametro("idfecha2", $xCred->getFechaDeMinistracion(), MQL_DATE);
		$TipoDeDispersion		= parametro("idtipodispersion", $xCred->getTipoDeDispersion(), MQL_INT);
		$TipoDeLugarDeCobro		= parametro("idtipolugarcobro", $xCred->getTipoDeLugarDeCobro(), MQL_INT);
		
		if($PuedeTasaCero == true){
			
		} else {
			$TasaDeInteres		= ($TasaDeInteres <=0) ? ($xCred->getTasaDeInteres()*100) : $TasaDeInteres;
		}
		if($TasaDeInteres > 1){	$TasaDeInteres = ($TasaDeInteres/100); }
				
	
		/* verifica si el credito ya ha sido autorizado */
		$ds_sol 				= $xCred->getDatosDeCredito();
	
		$estatus				= $xCred->getEstadoActual();
		$gpoasoc				= $xCred->getClaveDeGrupo();
		$tipodeconv				= $xCred->getClaveDeProducto();
		$montosolicitado		= $xCred->getMontoSolicitado();
		$socio					= $xCred->getClaveDePersona();
		$fechavcto				= $xCred->getFechaDeVencimiento();
		$diasaut				= $xCred->getDiasSolicitados();
	
		$sucess					= true;
	
		if ($estatus != CREDITO_ESTADO_SOLICITADO){
			$msg	.= "ERROR\tEL CREDITO YA HA SIDO MODIFICADO, NO SE PUEDE AUTORIZAR\r\n";
			$sucess				= false;
		}
		if ($estatus == CREDITO_ESTADO_AUTORIZADO){
			$msg	.= "ERROR\tEL CREDITO ESTA AUTORIZADO MAS NO MINISTRADO, SE PERMITE MODIFICACION\r\n";
			$sucess				= true;
		}
		if ($idmonto > $montosolicitado ){
			$msg	.= "ERROR\tEL CREDITO AUTORIZADO NO PUEDE SER MAYOR AL SOLICITADO\r\n";
			$sucess				= false;
		}
		//Datos del Convenio
		$dconv 					= $xCred->getDatosDeProducto();
		$OConv					= $xCred->getOProductoDeCredito();
		$tipo_de_integracion 	= $dconv["tipo_de_integracion"];
	
		/* OBTIENE EL PERIODO EN QUE SE DEBIO AUTORIZAR */
	
		$periodo                        = $ds_sol["periodo_solicitudes"];;
		$fechaaut 						= $xF->getFechaISO($fecha1);
		$fecha_ministracion_propuesta 	= $xF->getFechaISO($fecha2);
		$fechaultmvto 					= $fechaaut;				//Fecha de Ultimo Movimiento = Fecha de Autorizacion;
		if($idmonto <= TOLERANCIA_SALDOS){
			//Cambiar a 0 de saldo autorizado y 50 de estatus
			$xCred->setCancelado($idautorizacion, $fechaaut);
			$sucess				= false;
		}
		//Actualiza a Clientes
		$xSoc	= new cSocio($xCred->getClaveDePersona());
		if($xSoc->init() == true){
			$xSoc->setEsCliente();
		}
		/*------------------------------ Obtiene datos mediante sentencias dinamicas */
	
	
		$estatusactual 					= CREDITO_ESTADO_AUTORIZADO;
	
		/* Determina si el Pago es en una sola Ministracion, genera el IDAD */
	
		if ($periocidad == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO) {
			$intdev 		= ($idmonto * $TasaDeInteres) / EACP_DIAS_INTERES;	// Interes Diario para Pagos Fijos
			$idpagos 		= 1;
			$fechavcto		= $ds_sol["fecha_vencimiento"];
			$diasaut		= restarfechas($fechavcto, $fecha_ministracion_propuesta);
	
			$msg			.= "WARN\tLos Dias Autorizados cambian a $diasaut, se respeta la fecha de vencimiento ($fechavcto)\r\n";
			$xFRM->addCreditoComandos($idsolicitud);
			
		} else {
			$intdev 		=  ($idmonto * $TasaDeInteres) / EACP_DIAS_INTERES;	// Interes Diario a Cero para otros Casos
			$fechavcto		=  sumardias($fecha_ministracion_propuesta, $diasaut);
			$msg			.= "WARN\tLa Fecha de Vencimiento es actualizada al " . getFechaLarga($fechavcto) . "; los dias autorizados son de $diasaut\r\n";
			$msg			.= "WARN\tPARA OBTENER LOS DEMAS DOCUMENTOS DEBE GENERAR EL PLAN DE PAGOS\r\n";
		}
	
		/* ------------------------------ sentencia update -------------------------- */
		if($sucess	== true){
			$sucess	= $xCred->setAutorizado($idmonto, $idpagos, $periocidad, $TipoDeAutorizacion, $fechaaut, $idautorizacion,
					$idtipodepago, $fecha_ministracion_propuesta, $idnivelderiesgo, $diasaut, $fechavcto,
					$estatusactual, $sdoactual, $intdev, $fechaultmvto, $TasaDeInteres, $TipoDeLugarDeCobro, $TipoDeDispersion);
			if($sucess == true){
				/* si es Credito de Grupos solidarios, Actualiza los Mvtos de Otorgacion */
				if( $OConv->getEsProductoDeGrupos() == true) {
					$sqlusolc = "UPDATE operaciones_mvtos	SET estatus_mvto = 10 WHERE grupo_asociado=$gpoasoc	AND (tipo_operacion=112) AND (estatus_mvto=40)";
					$xQL	= new MQL();
					$xQL->setRawQuery($sqlusolc);
				}
				//Eliminar Plan de Pagos
				$plan 	= setNoMenorQueCero($xCred->getNumeroDePlanDePagos());
				if($plan > 0){
					$xPlan	= new cPlanDePagos($plan);
					$xPlan->setEliminar();
					$msg	.= $xPlan->getMessages();
				}
				$xFRM->OButton("TR.GENERAR PLAN_DE_PAGOS", "var CGen=new CredGen();CGen.getFormaPlanPagos($credito)", $xFRM->ic()->CALCULAR);
			}
		}
		$msg		.= $xCred->getMessages();
		//------------------------------- IMPRIME UNA PEQUE%A DESCRIPCION DE LA SOLICITUD -----------------------
		$xCred->init();
		$xFRM->addHTML( $xCred->getFichaDeSocio(true) );
		$xFRM->addHTML( $xCred->getFicha(true) );
	
		if($sucess == true){
			//$urlsend 				= "elUrl='" . $OConv->getPathPagare($idsolicitud) . "';";
			$cedula_grupal 	       			= 0;
			//$urctr 					= "esUrl='" . $xCred->getPathDelContrato() . "';";
			if ($OConv->getEsProductoDeGrupos() == true) {
				//Si la cedula Grupal existe y el Tipo de Integracion el GRUPO
				if ( isset( $_SESSION["recibo_en_proceso"] ) ){ $cedula_grupal	= $_SESSION["recibo_en_proceso"]; }
				$xFRM->addToolbar( $xBtn->getBasic("TR.IMPRIMIR CEDULA GRUPAL DE AUTORIZACION", "jsPrintCedulaGrupal())", "personas", "print-cedulagrupo", false ) );
			}
			$xFRM->addAvisoRegistroOK();
		} else {
			$xFRM->addAvisoRegistroError();
		}
		//$xFRM->addToolbar( $xBtn->getBasic("TR.IMPRIMIR CEDULA DE AUTORIZACION", "printcedula()", "documento", "print-cedula", false ) );
		$xFRM->addCerrar();
		
		$msg			.= "WARN\tLos Datos de Fecha de vencimiento, Dias Autorizados\r\n";
		$msg			.= "WARN\tInteres Diario, Monto de la Parcialidad, etc. Varian cuando se elabore el PLAN DE PAGOS (Cuando son PAGOS PERIODICOS)\r\n";
		$msg			.= "WARN\tNo es recomendable que se Impriman los Documentos a esta Altura del Proceso\r\n";
		$xFRM->addAviso($msg);
		
	}	
} else {
?>
	<div class="inv" id="divrazones">
		<form class="formoid-default" style="background-color:#FFFFFF;font-size:14px;font-family:'Open Sans','Helvetica Neue','Helvetica',Arial,Verdana,sans-serif;color:#666666;width:30em" title="frmRechazados" method="post">
			<div class="element-text" ><h2 class="title">Rechazados</h2></div>
			<div class="element-textarea" ><label class="title">Razones de Rechazo</label><textarea name="txtRazones" id="txtRazones" cols="20" rows="5" ></textarea></div>
			<div class="element-submit" >
				<input type="button" onclick="jsSetSaveRechazados()" value="Guardar"/>
				<input type="button" onclick="jsCancelRechazados()" value="Cancelar"/>
			</div>
		</form>
	</div>
	<?php
	$msg		= "";
	$xTA		= $xSel->getListaDeTipoDeAutorizacion("", $TipoDeAutorizacion);
	$xTA->addEvent("onfocus", "jsaGetDatos()");
	//$xTA->addEvent("onchange", "jsaGetDatos()");
	//si existe credito y persona
	if( $remoto	== true  ){
		$xFRM->addHElem( $xCred->getFicha(true, "", false, true) );
		$xFRM->addHElem("<input type='hidden' id='idsocio' name='idsocio' value='$persona' /> <input type='hidden' id='idsolicitud' name='idsolicitud' value='$credito' /> ");
		$xCOrg				= new cCreditosDatosDeOrigen();
		
		switch($xCred->getTipoDeOrigen()){
			case $xCOrg->ORIGEN_RENOVACION:
				$SinTipoAut	= true;
				break;
			case $xCOrg->ORIGEN_ARRENDAMIENTO:
				$xFRM->addDisabledInit("idmonto");
				$xFRM->addDisabledInit("idpagos");
				$xFRM->addDisabledInit("idperiocidad");
				$xFRM->addDisabledInit("idtipodepago");
				$SinTasa		= true;
				$SinLugarPag	= true;
				
				break;
		}
		
	} else {
		$xFRM->addCreditBasico($credito, $persona);
	}
//================================ Item de Validacion
	//$xFRM->OHidden("idvalido", 1);
	$xFRM->OHidden("idvalido", 0);
	if($SinTipoAut == false){
		$xFRM->addHElem( $xTA->get(true) );
	} else {
		$xFRM->OHidden("idtipodeautorizacion", $TipoDeAutorizacion);
	}
	
	$xFRM->addHElem( $xSel->getListaDeTipoDeRiesgoEnCreds("", $NivelDeRiesgo)->get(true) );
	
	$xFRM->OMoneda("idpagos",  $pagos_autorizados, "TR.Pagos Autorizados" );
	$xFRM->OMoneda("idmonto", $monto_autorizado, "TR.Monto Autorizado");
	$xFRM->setValidacion("idmonto", "jsEvaluateMonto");
	if($SinTasa == true){
		$xFRM->OHidden("idtasa", $TasaDeInteres);
	} else {
		$xFRM->OMoneda("idtasa", $TasaDeInteres, "TR.Tasa Autorizada");
	}
	
	$xFRM->addHElem( $xSel->getListaDePeriocidadDePago("", $TipoDePeriocidad)->get(true) );
	$xFRM->addHElem( $xSel->getListaDeTipoDePago("", $TipoDePago)->get(true) );
//=============== Tipo y lugart de cobro
	if($SinLugarPag == true){
		$xFRM->OHidden("idtipolugarcobro", $TipoDeLugarDeCobro);
	} else {
		$xFRM->addHElem( $xSel->getListaDeTipoDeLugarDeCobro("", $TipoDeLugarDeCobro)->get(true) );
	}
	if($SinTipoDisp == true){
		$xFRM->OHidden("idtipodispersion", $TipoDeDispersion);
	} else {
		$xFRM->addHElem( $xSel->getListaDeTipoDeDispersionCreditos("", $TipoDeDispersion)->get(true) );
	}
	
	$xFRM->ODate("idfecha1", $fecha_de_autorizacion, "TR.Fecha de Autorizacion");
	$xFRM->ODate("idfecha2", $fecha_de_ministracion, "TR.Fecha de Ministracion");
	
	$xFRM->OTextArea("idautorizacion", "", "TR.Documento de Autorizacion");
	$xFRM->setValidacion("idautorizacion", "jsGetValidacion");
	$xFRM->addGuardar("jsGuardarAutorizacion()");
	$xFRM->OButton("TR.Validacion", "jsGetFormaValidacion", $xFRM->ic()->CHECAR);
	
	//$xFRM->OButton("TR.RECHAZAR", "jsSetCancelado()", $xFRM->ic()->CERRAR);
	
	
	$xFRM->addHTML('<form name="frmOthersProcess"><div id="informacion"></div></form>');
	//2011-02-01
	$idsolicitud					= 0;
	$urlsend						= "";
	$urctr							= "";
	$cedula_grupal					= 0;


}

//$xJs->setLoadDefaults(false);
echo $xFRM->get();

$jxc ->drawJavaScript(false, true);

?>
</body>
<script  >
var jsrCreditosCommon	= "../js/creditos.common.js.php";
var divLiteral			= "<?php echo STD_LITERAL_DIVISOR; ?>";
var mFormaValidada		= false;
var xG					= new Gen();
var xC					= new CredGen();
function jsInit(){ autoEjecutar = false; jsaGetDatos(); $("#idtipodeautorizacion").focus();  }
function jsGetValidacion(){ getListadoDeGrupoParaGuardar(); mFormaValidada = true; return true;}
function jsGuardarAutorizacion(){
	if($("#idvalido").val() == 0){
		xG.alerta({ msg : "El Credito contiene Datos Faltantes" });
		
	} else {
		if(mFormaValidada == false){
			jsGetValidacion();
		}
	
		var subf			= function(){
				if($("#idesautorizado").length > 0){
					$("#frmcreditoautorizado").submit();
				} else {
					xG.alerta({ msg : "Forma no validada" });
				}		
			}	
		
		if(flotante($("#idmonto").val()) <= 0){
			jsEvaluateMonto();	
		} else {
			xG.spin({ callback : subf });
		}
	}
}
function jsUpdateAutorizacion(){
	var Frm 					= document.frmOthersProcess;
	var isLims 					= Frm.elements.length - 1;
	var mSuma					= 0;
  		for(i=0; i<=isLims; i++){
			var mTyp 			= Frm.elements[i].getAttribute("type");
			var mID 			= Frm.elements[i].getAttribute("id");

			//Verificar si es mayor a cero o no nulo
			if ( (mID!=null) && (mID.indexOf("monto-")!= -1) && (mTyp == "text") ){
					mSuma 		+= parseFloat(document.getElementById(mID).value);
  			}

  		}
  	document.getElementById("idmonto").value = mSuma;
}
function jsSavePlaneacion(iMembers){
    //Netraliza el Boton
    document.getElementById("icmdGoGroup").disable = true;

	var Frm 					= document.frmOthersProcess;
	var isLims 					= Frm.elements.length - 1;

  		for(i=0; i<=isLims; i++){
			var mTyp 	= Frm.elements[i].getAttribute("type");
			var mID 	= Frm.elements[i].getAttribute("id");

			//Verificar si es mayor a cero o no nulo
			if ( (mID!=null) && (mID.indexOf("socio-")!= -1) && (mTyp == "hidden") ){
				//Despedazar el ID para obtener el denominador comun
				//socio-{grupo}-{socio}
				var aID		= mID.split("-");
				var mGrupo	= aID[1];
				var mSocio	= aID[2];
				var mCred	= document.getElementById("idsolicitud").value;
				var mMonto	= document.getElementById("monto-" + mGrupo + "-" + mSocio).value;
				var mNota	= document.getElementById("detalles-" + mGrupo + "-" + mSocio).value;

				jsrsExecute(jsrCreditosCommon, null, "Common_a92d70128878fe0e88050362ac797763", mGrupo + divLiteral + mCred + divLiteral + mSocio + divLiteral + mMonto + divLiteral + mNota );
  			}
  		}
  		setTimeout("jsSetFinalizarPlaneacion()", 5000);
}
function jsSetFinalizarPlaneacion(){
	jsrsExecute(jsrCreditosCommon, jsEchoMsg, "Common_d7823d8fb813a0f5223b914a9bf892d4", false);
	$("#frmcreditoautorizado").submit();
}
function jsEchoMsg(msg){ alert(msg); }
function creditogpo() {
	elsoc = document.frmcreditoautorizado.idsocio.value;
	jsrsExecute('../clsfunctions.inc.php', obcredgpo,'gposolcred', elsoc);
}

function obcredgpo(elmnto){	adar = elmnto;
	document.frmcreditoautorizado.idmonto.value = adar;
}
function jsPrintCedulaGrupal(){
	uvar = "../rpt_formatos/rptplaneacioncredito.php?plan=<?php echo $cedula_grupal; ?>";
	rptrecibo = window.open( uvar, "","width=800,heigth=700,scrollbars,resizable");
	rptrecibo.focus();
}
function jsEvaluateMonto(){
	var vM	= $("#idmonto").val();
	if(flotante(vM) == 0){
		getModalTip("#frmcreditoautorizado", $("#divrazones"), "");
		$("#txtRazones").focus();
	}
	return true;
}
function jsSetSaveRechazados() {
	jsaSetSaveRechazados();
	$("#ididautorizacion").val( $("#txtRazones").val() );
	$("#frmcreditoautorizado").qtip("hide");
	$("#frmcreditoautorizado").submit();
}
function jsCancelRechazados(){
	$("#frmcreditoautorizado").qtip("hide");
}
function jsSetCancelado(){
	$("#idmonto").val(0);
	$("#idmonto").trigger("blur");
	var jsda = function(){ $("#txtRazones").focus(); }
	xG.spin({time:1000,callback:jsda});
}
function jsGetFormaValidacion(){
	xC.getFormaValidacion($("#idsolicitud").val());
}
</script>
</html>
