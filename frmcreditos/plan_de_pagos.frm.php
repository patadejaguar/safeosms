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
$xHP		= new cHPage("TR.GENERAR PLAN_DE_PAGOS", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$msg		= "";
$jxc 		= new TinyAjax();
$xRuls		= new cReglaDeNegocio();


$idPlanAnt		= 0;
$SinOtrosEnPlan	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_PLAN_SIN_OTROS);	//regla de negocio
$SinOptsEnPlan	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_PLAN_SIN_OPTS);	//regla de negocio
$ConPagosEsp	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_PLAN_CON_PAGESP);	//regla de negocio

function jsaEmularPagos($idcredito, $fechaMinistracion, $TipoDePago, $redondeo, 
		$dia1, $dia2, $dia3, $idotros, $montootros, $primer_pago, $PrimeraFecha, $completo, $sinintereses, $guardaruno, $promovido, $residual, $ajustecap){
	$xEm			= new cPlanDePagosGenerador();
	$xT				= new cTipos();	
	$xCred			= new cCredito($idcredito);
	$completo		= $xT->cBool($completo);
	$sinintereses	= $xT->cBool($sinintereses);
	$guardaruno		= $xT->cBool($guardaruno);
	$ajustecap		= $xT->cBool($ajustecap);
	
	if($xCred->init() == true){
		$xEm->setIgnorarIntsNoPag($sinintereses);
		$xEm->setMostrarCompleto($completo);
		$xEm->setGuardarPrimeraDiferencia($guardaruno);
		$xCred->setCambiarFechaMinistracion($fechaMinistracion, true);
		$xEm->initPorCredito($idcredito);
		$xEm->setDiasDeAbonoFijo($dia1, $dia2, $dia3);
		$primer_pago= $xT->cBool($primer_pago);
		$xEm->setFechaArbitraria($PrimeraFecha);
		$xEm->setTipoDePago($TipoDePago);
		$xEm->setMontoParcialidad($promovido);
		$xEm->setValorResidual($residual);
		$xEm->setAjustarCapital($ajustecap);
		
		$parcial 	= $xEm->getParcialidadPresumida($redondeo, $idotros, $montootros, $primer_pago);
		$xEm->setCompilar($TipoDePago);
		return $xEm->getVersionFinal();
	}
}

function jsaGuardarPagos($idcredito, $fechaMinistracion, $TipoDePago, $redondeo,
		$dia1, $dia2, $dia3, $idotros, $montootros, $primer_pago, $PrimeraFecha, $completo, $sinintereses, $guardaruno, $promovido, $residual, $ajustecap){
	$xEm			= new cPlanDePagosGenerador();
	$xT				= new cTipos();
	$completo		= $xT->cBool($completo);
	$sinintereses	= $xT->cBool($sinintereses);
	$guardaruno		= $xT->cBool($guardaruno);
	$ajustecap		= $xT->cBool($ajustecap);
	//Actualizar Fecha de Ministracion
	$xCred			= new cCredito($idcredito);
	if($xCred->init() == true){
		$xEm->setIgnorarIntsNoPag($sinintereses);
		$xEm->setMostrarCompleto($completo);
		$xEm->setGuardarPrimeraDiferencia($guardaruno);	
		$xCred->setCambiarFechaMinistracion($fechaMinistracion, true);
		$xEm->initPorCredito($idcredito);
		$xEm->setDiasDeAbonoFijo($dia1, $dia2, $dia3);
		$primer_pago		= $xT->cBool($primer_pago);
		$xEm->setFechaArbitraria($PrimeraFecha);
		$xEm->setMontoParcialidad($promovido);
		$xEm->setValorResidual($residual);
		$xEm->setAjustarCapital($ajustecap);
		
		$parcial 			= $xEm->getParcialidadPresumida($redondeo, $idotros, $montootros, $primer_pago);
		
		$xEm->setCompilar($TipoDePago);
		return $xEm->getVersionFinal(true);
	}
}

$jxc ->exportFunction('jsaEmularPagos', array('idsolicitud', 'idfechaministracion', 'idtipodepago', 'idredondeo', 'dia_primer_abono', 'dia_segundo_abono', 'dia_tercer_abono', 
		'idtipootros', 'idotros', 'idprimerpago', 'idprimerafecha', 'idcompleto', 'idnointereses', 'idguardaruno', 'idmontopromovido', 'idvalorresidual', 'idajustecapital'), "#iddatos_pago");
$jxc ->exportFunction('jsaGuardarPagos', array('idsolicitud', 'idfechaministracion', 'idtipodepago', 'idredondeo', 'dia_primer_abono', 'dia_segundo_abono', 'dia_tercer_abono', 
		'idtipootros', 'idotros', 'idprimerpago', 'idprimerafecha', 'idcompleto', 'idnointereses', 'idguardaruno', 'idmontopromovido', 'idvalorresidual', 'idajustecapital'), "#iddatos_pago");

$jxc ->process();

$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); 
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$auto		= parametro("auto", false, MQL_BOOL);
$xHP->init();

$xFRM		= new cHForm("frm", "plan_de_pagos.frm.php");
$xFRM->setTitle($xHP->getTitle());
$xSel		= new cHSelect();
if($credito > DEFAULT_CREDITO){
	$xCred		= new cCredito($credito);
	$xCred->init();
	$persona	= $xCred->getClaveDePersona();
	$DProd		= $xCred->getOProductoDeCredito();
	$idPlanAnt	= $xCred->getNumeroDePlanDePagos();
	$xFRM->addCerrar();
	$xFRM->OHidden("idsolicitud", $credito);
	$xFRM->OHidden("idsocio", $persona);
	
	$xFRM->addHElem( $xCred->getFicha(true, "", false, true) );
	
	$xFRM->addHElem( $xSel->getListaDeTipoDePago("", $xCred->getTipoDePago())->get(true) );

	//if(getCantidadRendonda($cantidad, $redondeo))
	$xFRM->addHElem( $xSel->getListaDeTipoDeRedondeo("", $xCred->getFactorRedondeo() )->get(true) );
	
	$xFRM->ODate("idfechaministracion", $xCred->getFechaDeMinistracion(), "TR.Fecha de ministracion");
	
	if($xCred->getTipoEnSistema() == SYS_PRODUCTO_NOMINA){	
		
	}
	$xGen		= new cPlanDePagosGenerador();
	$xGen->initPorCredito($credito, $xCred->getDatosInArray());
	//$xFRM->addHElem("<div id='id' class='tx4'>" . $xGen->getControlDias() . "</div>");
	$xFRM->addHElem($xGen->getControlDias());
	if($xCred->getPeriocidadDePago() != CREDITO_TIPO_PERIOCIDAD_DECENAL){
		if($xCred->getPeriocidadDePago() == CREDITO_TIPO_PERIOCIDAD_QUINCENAL){
			$xFRM->OHidden("dia_tercer_abono", 0);
		} else {
			$xFRM->OHidden("dia_tercer_abono", 0);
			$xFRM->OHidden("dia_segundo_abono", 0);
		}
	}
	$xFRM->ODate("idprimerafecha", $xCred->getFechaPrimeraParc(), "TR.Fecha de Primer Pago");
	$xFRM->setValidacion("idprimerafecha", "jsGetPrimeraFecha", "La fecha no coincide con los dias de pago", true);
	$idotros		= OPERACION_CLAVE_COMISION_APERTURA;
	$montootros		= 0;
	$valotros		= CREDITO_EN_PLAN_COM_APERTURA;
	if(CREDITO_TASA_COM_APERTURA_GLOBAL > 0){
		$montootros	= setNoMenorQueCero(($xCred->getMontoAutorizado() * CREDITO_TASA_COM_APERTURA_GLOBAL),2);
	}
	if($xCred->getEsArrendamientoPuro() == true){
		$idotros		= OPERACION_CLAVE_PLAN_DESGLOSE;
		$xOrg			= new cCreditosLeasing();
		if($xOrg->initByCredito($credito) == true){
			$montootros = $xOrg->getCuotasNoCapitalizadas()*$xCred->getPagosAutorizados();
			$valotros	= "false";
			$xFRM->ODisabled_13("idotros", $montootros, "TR.Monto otros");
			$xFRM->OHidden("idtipootros", $idotros);
			
		}
		
	} else {
	
		if($SinOtrosEnPlan == true){
			$xFRM->OHidden("idtipootros", $idotros);
			$xFRM->OHidden("idotros", $montootros);
			$xFRM->OHidden("idprimerpago", $valotros);
		} else {
			if($xCred->getEsCreditoYaAfectado() == true){
				$DProd->initOtrosCargos($xCred->getFechaDeMinistracion(), $xCred->getMontoAutorizado());
			} else {
				$DProd->initOtrosCargos();
			}
			$xSelOtros		= $xSel->getListaDeOperacionesPorBase(1001, "idtipootros", false, $idotros);
			$SumOtr			= $DProd->getSumaOtrosCargosEnParcs();
			
			if($SumOtr > 0){
				$xSelOtros->setOptionSelect(OPERACION_CLAVE_PLAN_DESGLOSE);
				$montootros	= setNoMenorQueCero( ($xCred->getMontoAutorizado()*$SumOtr),2 );
			}
			
			$xFRM->addHElem(  $xSelOtros->get("TR.Otros", true));
			$xFRM->OMoneda("idotros", $montootros, "TR.Monto otros");
			
			
			if($valotros == true){
				$xFRM->OHidden("idprimerpago", $valotros);
			} else {
				$xFRM->OCheck("TR.Guardar en el primer pago", "idprimerpago");
			}
		}
	}
	//=============== Esto debe ser opcional
	
	if($SinOptsEnPlan == false){
		
		$xFRM->OCheck("TR.IGNORAR INTERESES NO PAGADOS", "idnointereses");
		$xFRM->OCheck("TR.INCLUIR ACCESORIOS AL ULTIMO PAGO", "idguardaruno");
	} else {
		$xFRM->OHidden("idcompleto", "false");
		$xFRM->OHidden("idnointeres", "false");
		$xFRM->OHidden("idguardaruno", "false");
	}
	if($ConPagosEsp == false){
		if($xF->getInt($xCred->getFechaDeMinistracion()) <= $xF->getInt($xGen->FECHA_v11)){
			$xFRM->OMoneda("idmontopromovido", 0, "TR.Monto Promovido");
		} else {
			$xFRM->OHidden("idmontopromovido", 0);
		}
	} else {
		$xFRM->OMoneda("idmontopromovido", $xCred->getMontoDeParcialidad(), "TR.Monto Promovido");
	}
	$xFRM->addObservaciones();
	$xFRM->OCheck("TR.MOSTRAR COMPLETO", "idcompleto");
	$xFRM->OCheck("TR.AJUSTAR CAPITAL", "idajustecapital");
	
	$xFRM->addHTML("<div id='iddatos_pago'></div>");
	$xFRM->OButton("TR.Calcular", "jsEmularPagos()", $xFRM->ic()->CALCULAR, "idcplan");
	$xFRM->OButton("TR.Guardar", "jsGuardarPagos()", $xFRM->ic()->GUARDAR, "idgplan");
	
	$xFRM->OHidden("idperiocidad", $xCred->getPeriocidadDePago());
	$idnumeroplan = $xCred->getNumeroDePlanDePagos();
	if($idnumeroplan > 0){
		$xFRM->OButton("TR.PLAN_DE_PAGOS", "jsGetPlanDePagos()", $xFRM->ic()->CALENDARIO1);
	}
	if($xCred->getEsAfectable() == true){
		$xFRM->OButton("TR.ESTADO_DE_CUENTA", "var xC=new CredGen();xC.getEstadoDeCuenta($credito);", $xFRM->ic()->ESTADO_CTA);
	}
	//Agregar Valor residual del Credito
	$xFRM->OHidden("idvalorresidual", $xCred->getvalorResidual());
	
	$xFRM->addJsInit("jsInitComponents()");
} else {
	$xFRM->addJsBasico();
	$xFRM->addCreditBasico();
	$xFRM->addSubmit();
}

echo $xFRM->get();
?>
<script>
var xG		= new Gen();
var xF		= new FechaGen();
var xP		= new PlanGen();
var isAuto	= <?php echo ($auto == true) ? "true" : "false" ?>;
var idPlanA	= <?php echo setNoMenorQueCero($idPlanAnt); ?>;
function jsInitComponents(){
	var idprimerafecha		= $("#idprimerafecha").val();
	var dia_primer_abono	= $("#dia_primer_abono").val();
	var dia_segundo_abono	= $("#dia_segundo_abono").val();
	var dia_tercer_abono	= $("#dia_tercer_abono").val();
	var idfechaministracion	= $("#idfechaministracion").val();
	var idperiocidad		= entero($("#idperiocidad").val());
	/*var xd					= new XDate( xF.get(idprimerafecha) );
	var ready				= false;
	var mMDia				= xd.getDate();
	var mMes				= xd.getMonth() + 1;
	var mAnno				*/
	switch(idperiocidad){
		case 30:
				var nPD			= new XDate(xF.get(idprimerafecha));
				var nFM			= new XDate(xF.get(idfechaministracion));
				if((nPD.getDate() != dia_primer_abono) && idPlanA <= 0){
					var nF		= new XDate(nPD.getFullYear() , nPD.getMonth(), dia_primer_abono);
					$("#idprimerafecha").val(nF.toString("dd-MM-yyyy"));
				}
			break;
	}
	if(isAuto == true){
		jsaGuardarPagos(); //generar el Plan directo
		xG.spin({ callback : jsAutoSalir, time : 10000 });
	}
}
function jsAutoSalir(){xG.close();}
function jsEmularPagos(){
	$("#idprimerafecha").trigger("blur");
	if(xG.happy() == true){
		jsaEmularPagos();
		//obtener plan de pagos
		xG.spin({ time : 3000 });		
	}
}

function jsGuardarPagos(){
	$("#idprimerafecha").trigger("blur");
	if(xG.happy() == true){
		xG.confirmar({ msg : "Confirma Guardar el PLAN_DE_PAGOS ?" , callback : function (){
				jsaGuardarPagos();
				//obtener plan de pagos
				xG.desactiva("#idgplan");
				xG.desactiva("#idcplan");
				xG.spin({ callback : jsGetPlanDePagos, time : 20000 });
			}
		});
	}
}
function jsGetPlanDePagos(){
	var idcredito = $("#idsolicitud").val();
	var AjxOpts	= {
			url		: "../svc/plan-de-pagos.svc.php?credito=" + idcredito , // cmd + tabla + primaryKey + registro
			contentType	: "json",
			success		: function(rs){
				if(rs[SYS_ERROR] == false){
					var xC	= new CredGen();
					var idnumplan = entero(rs[SYS_NUMERO]);
					if(idnumplan >0){
						xC.getImprimirPlanPagos(rs[SYS_NUMERO]);
						xG.spinEnd();
					}
					
				} else {
					xG.alerta( { "msg" : rs[SYS_MSG] } );
				}
			}
		};
		$.ajax(AjxOpts);	
}
function jsGetPrimeraFecha(v){
	$("#idprimerafecha").val( String($("#idprimerafecha").val()).replace(/\//g, "-") );
	var idprimerafecha		= $("#idprimerafecha").val();
	var dia_primer_abono	= $("#dia_primer_abono").val();
	var dia_segundo_abono	= $("#dia_segundo_abono").val();
	var dia_tercer_abono	= $("#dia_tercer_abono").val();
	var idfechaministracion	= $("#idfechaministracion").val();
	var idperiocidad		= entero($("#idperiocidad").val());
	var xd					= new XDate( xF.get(idprimerafecha) );
	var ready				= false;
	var mWDia				= xd.getDay();	//dia de la semana
	var mMDia				= xd.getDate(); //dia del mes
	var mMes				= xd.getMonth()+1;//Mes de annio
	var mMesMax				= new Date(xd.getFullYear(), mMes, 0).getDate();
	var mDiasMax			= 31;
	var msg					= "";
	var aDias				= [dia_primer_abono,dia_segundo_abono,dia_tercer_abono];
	aDias.sort();
	var mAbsDiasMax			= aDias[2];
	var mIgnore				= false;
	if(dia_primer_abono > mDiasMax||dia_segundo_abono > mDiasMax||dia_tercer_abono > mDiasMax){
		ready				= false;
		msg 				= "Numero de dia invalido";
	} else {
		if(mAbsDiasMax > mMesMax){
			xG.alerta({msg:"Es recomendable que el dia de Abono Maximo(" + mAbsDiasMax + ") No sea mayor al del Mes(" + mMesMax + ")", nivel : "warn"});
			mIgnore			= true;
		}
		switch(idperiocidad){
			case 7:
				if(mWDia == dia_primer_abono){
					ready = true;
				} else {
					msg 		= "Dia semana (" + mWDia + ") No coincide (" + dia_primer_abono + ")";
				}
				break;
			case 10:
				if(mMDia == dia_primer_abono||mMDia == dia_segundo_abono||mMDia == dia_tercer_abono){
					ready = true;
				} else {
					msg 	= "Dia Decena " + mMDia + "No coincide con los dias : " + dia_primer_abono + " o " + dia_segundo_abono;
				}
				break;			
			case 14:
				if(mWDia == dia_primer_abono){
					ready = true;
				} else {
					msg 		= "Dia Catorcena (" + mWDia + ") No coincide (" + dia_primer_abono + ")";
				}
				break;			
			case 15:
				//if(mMDia == dia_primer_abono||mMDia == dia_segundo_abono||mMDia == dia_tercer_abono){
				if(mMDia == dia_primer_abono||mMDia == dia_segundo_abono){
					ready = true;
				} else {
					msg 	= "Dia Quincena " + mMDia + "No coincide con los dias : " + dia_primer_abono + " o " + dia_segundo_abono;
				}
				break;
			case 30:
				if(mMDia == 31){
					ready	= false;
					msg		= "No calcule los pagos a  dias 31, no todos los meses tienen 31 dias";					
				} else {
					if(mMDia == dia_primer_abono){
						ready = true;
					} else {
						msg 	= "La fecha de pago " + dia_primer_abono + " debe ser el mismo dia de Calculo  " + mMDia;
					}
				}
				break;
			default:
				if(idperiocidad == 360){
					ready 	= false;
					msg		= "No se califica la Fecha " + idprimerafecha + "(" + mMDia + "|" + mWDia + ") con los dias " + dia_primer_abono + "-" + dia_segundo_abono + "-" + dia_tercer_abono;
				} else {
					if(mMDia == dia_primer_abono){
						ready 	= true;
					} else {
						msg 	= "Dia Mes " + mMDia + "No coincide " + dia_primer_abono;
					}
				}
				break;
		}
		
	}
	if(ready == false){
		xG.alerta({msg:msg, nivel : "error"});
	}
	return ready;
}
function jsSetAnualidad(obj){
	var dd	= String(obj.id).split("-");	
	xP.setAnualidadLetra({credito:dd[0], periodo:dd[1], monto:obj.value });
}
function jsSetCapital(obj){
	var dd	= String(obj.id).split("-");	
	xP.setPagoEspecial({credito:dd[0], periodo:dd[1], monto:obj.value });
}
</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>