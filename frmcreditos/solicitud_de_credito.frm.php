<?php
/**
 * Solicitud de Creditos, forma de captura
 * @author Balam Gonzalez Luis Humberto
 * @version 1.50
 * @package creditos
 * @subpackage forms
 * 		22/07/2008	Funciones mejoradas de riesgo
 * 					Implementacion de php doc
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
$xHP				= new cHPage("TR.Solicitud de Credito");
//$oficial 	= elusuario($iduser);
$jxc				= new TinyAjax();
$xRuls				= new cReglaDeNegocio();
$xVals				= new cReglasDeValidacion();
$xUsr				= new cSystemUser(); $xUsr->init();
//$SinDatosDispersion	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_AUTORIZACION_SIN_DISP);		//regla de negocio
$SinFinalPlazo		= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_PRODUCTOS_SIN_FINALPZO);		//regla de negocio
$SinFechaVenc		= false;
$SinLugarPag		= false; //ACTUALIZAR
$ConOrigen			= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_SOLICITUD_CON_ORIGEN);		//regla de negocio
$OffByUsr			= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_OFICIAL_POR_USR);		//regla de negocio
$OffByHer			= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_OFICIAL_POR_HER);		//regla de negocio
$OffByProd			= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_OFICIAL_POR_PROD);		//regla de negocio

$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$monto			= parametro("monto",0, MQL_FLOAT);
$producto		= parametro("producto",0, MQL_INT);//DEFAULT_TIPO_CONVENIO
$idorigen		= parametro("idorigen",1, MQL_INT);
$tipoorigen		= parametro("origen",1, MQL_INT); //Tipo de Origen

$fecha			= parametro("fecha", fechasys(), MQL_DATE);
$pagos			= parametro("pagos", 1, MQL_INT);
$frecuencia		= parametro("frecuencia", false, MQL_INT);
$destino		= parametro("destino", FALLBACK_CRED_TIPO_DESTINO, MQL_INT);
$oficial		= parametro("oficial", getUsuarioActual(), MQL_INT);
$tasa			= parametro("tasa", 0, MQL_FLOAT);
$claseproducto	= parametro("claseproducto", 0, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$TasaDeMora		= parametro("tasamora",0, MQL_FLOAT);

$TipoCobro		= 0;
$MontoMaximo	= 0;


function jsaGetPerfilDePersona($persona){
	$tab 		= new TinyAjaxBehavior();
	$xSoc		= new cSocio($persona);
	$pagos		= 1;
	$periocidad	= DEFAULT_PERIOCIDAD_PAGO;
	$producto	= DEFAULT_TIPO_CONVENIO;
	$monto		= 0;
	if($xSoc->init() == true){
		if($xSoc->getClaveDeEmpresa() != FALLBACK_CLAVE_EMPRESA){
			$xEmp		= new cEmpresas($xSoc->getClaveDeEmpresa());
			$xEmp->init();
			$producto	= $xEmp->getProductoPref();
			
			$periocidad	= $xEmp->getPeriocidadPref();
			
			$OConv		= new cProductoDeCredito($producto);
			if($OConv->init() == true){
				$pagos		= $OConv->getNumeroPagosPreferente();
				$periocidad	= ($periocidad > 0) ? $periocidad : $OConv->getPeriocidadPrefente();
			}
		}
		$xEst	= new cPersonasEstadisticas($persona);
		$xEst->initDatosDeCredito();
		$creditoprior	= $xEst->getCreditoPrioritario();
		if($creditoprior > DEFAULT_CREDITO){
			$xCred	= new cCredito($creditoprior);
			if($xCred->init() == true){
				$monto		= $xCred->getMontoSolicitado();
				$periocidad	= ($periocidad > 0) ? $periocidad : $xCred->getPeriocidadDePago();
				if($producto == DEFAULT_TIPO_CONVENIO){
					$producto	= $xCred->getClaveDeConvenio();
				}
				if($pagos <= 1){
					$pagos	= $xCred->getPagosSolicitados();
					
				}
			}
		}
		
	}
	if($monto>0){
		if($pagos > 1){
			$tab -> add(TabSetValue::getBehavior('idnumerodepagos', $pagos ));
		}
		$xProd		= new cProductoDeCredito($producto);
		if($xProd->init() == true){
			$tab -> add(TabSetValue::getBehavior('idproducto', $producto ));
		}
		$tab -> add(TabSetValue::getBehavior('idperiocidad', $periocidad ));
		
		$tab -> add(TabSetValue::getBehavior('idmonto', $monto ));
	}
	return $tab->getString();	
}
function jsaGetPerfilDeProducto($producto,$periocidad,  $pagos){
	$tab 		= new TinyAjaxBehavior();
	//$OConv->init();
	///$tab 		= new TinyAjaxBehavior();
	//$xSoc		= new cSocio($persona);
	//$tab -> add(TabSetValue::getBehavior('idperiocidad', $periocidad ));
	//
	$OConv		= new cProductoDeCredito($producto);
	if($OConv->init() == true){
		if($periocidad == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO OR $pagos <=1){
			$tab -> add(TabSetValue::getBehavior('idnumerodepagos', $OConv->getNumeroPagosPreferente() ));
			$tab -> add(TabSetValue::getBehavior('idperiocidad', $OConv->getPeriocidadPrefente() ));
		}
		//$pagos		= $OConv->getNumeroPagosPreferente();
		//$periocidad	= ($periocidad > 0) ? $periocidad : $OConv->getPeriocidadPrefente();
	}
	return $tab->getString();
}

function jsaValidarCredito($socio){
	
	if($_SESSION[SYS_UUID] == null){
		
	} else {
		$xBtn	= new cHButton();
		$xBtn->setBClass("blue");
		return $xBtn->getBasic("TR.GUARDAR SOLICITUD", "jsFormularioValidado()", $xBtn->ic()->GUARDAR, "idvalidarok", false); 
	}
}

$jxc ->exportFunction('jsaValidarCredito', array('idsocio'), "#creditoaprobado");
$jxc ->exportFunction('jsaGetPerfilDePersona', array("idsocio") );
$jxc ->exportFunction('jsaGetPerfilDeProducto', array("idproducto", "idperiocidad", "idnumerodepagos") );

$jxc ->process();

if($persona > DEFAULT_SOCIO){
	$xHP->init();
}


$xFRM		= new cHForm("frmsolicitudcredito", "", "frmsolicitudcredito");
$xBtn		= new cHButton();
$xTxt		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();
$xCred		= new cCredito();
$ready		= true;
$showProd	= true;	//mostrar control del producto de credito

$xFRM->setAction("solicitud_de_credito.2.frm.php", true);

//========== Origen

//-- Manejar origen
switch($tipoorigen){
	case $xCred->ORIGEN_ARRENDAMIENTO:
		$xArr	= new cCreditosLeasing($idorigen);
		if($xArr->init() == true){
			if($xArr->getDomicilia() == true){
				$TipoCobro	= $xCred->COBRO_DOMICILIADO;
			}
		}
		break;
	case $xCred->ORIGEN_LINEAS:
		$xLinea	= new cCreditosLineas($idorigen);
		if($xLinea->init() == true){
			if($persona<= DEFAULT_SOCIO){
				$persona		= $xLinea->getClaveDePersona();
				$MontoMaximo	= $xLinea->getMontoDisponible();
				$frecuencia		= $xLinea->getPeriodicidad();
				$tasa			= $xLinea->getTasa();
				$producto		= CREDITO_PRODUCTO_REVOLVENTES;
				$SinFechaVenc	= true;
				$pagos			= 0;
			}
		}
		break;
}







//if($tipoorigen == $xCred->ORIGEN_ARRENDAMIENTO){}

//===========
$xFRM->setTitle( $xHP->getTitle() );
$xFRM->setNoAcordion();
$xFRM->addRangeSupport();

if($ConOrigen == true AND ($idorigen == 1 OR $tipoorigen == 1)){
	echo JS_CLOSE;
	$ready		= false;
	$xFRM->addAvisoRegistroError("TR.Requiere un ORIGEN_DE_CREDITO");
}

if($persona<= DEFAULT_SOCIO){
	$xHP->goToPageX("../utils/frmbuscarsocio.php?next=addcredito");
	$ready		= false;
} else {
	$xSoc		= new cSocio($persona);
	
	if($xSoc->init() == true){
		//$xFRM->addEnviar();
		$xFRM->OHidden("persona", $persona);
		$xFRM->OButton("TR.PANEL DE PERSONA", "var xP=new PersGen();xP.goToPanel($persona);", $xFRM->ic()->PERSONA, "cmdgopanelper", "persona");
		
		$xFRM->addSeccion("idinfopers", "TR.Cliente");
		$xFRM->addHElem( $xSoc->getFicha(false, true, "", true) );
		
		$xFRM->endSeccion();
		if($producto <= 0){
			$xFRM->addSeccion("iddsoprod", "TR.PRODUCTO");
			//=========== Producto por persona
			$xFRM->addHElem( $xSel->getListaDeProductosDeCredito("producto", false, true)->get(true) );
			$xFRM->setAction("solicitud_de_credito.frm.php", true);
			$xFRM->addEnviar();
			$ready		= false;
			$xFRM->endSeccion();
			if(MODO_NOOB == true){
				$xFRM->addAvisoInicial("MS.MSG_ELIJA_PRODUCTOCR", true, "TR.XD");
			}
			
		} else {
			$xProd	= new cProductoDeCredito($producto);
			
			if($xProd->init() == true){
				
				$xFRM->addSeccion("iddsoprod", "TR.PRODUCTO");
				$xFRM->OHidden("producto", $producto);
				$xFRM->OHidden("idproducto", $producto);
				$showProd		= false;		//No mostrar control de productos
				
				$xFRM->addHElem( $xProd->getFicha() );
				$xFRM->endSeccion();

				
				if($xProd->getEsProductoDeNomina() == true){
					if($xVals->empresa($empresa) == false){
						$empresa	= $xSoc->getClaveDeEmpresa();
					}
					if($xVals->empresa($empresa) == false){
						$ready		= false;
						$xFRM->setAction("solicitud_de_credito.frm.php", true);
						$xFRM->addEnviar();
						$xFRM->addSeccion("idempx", "TR.EMPRESA");
						$xFRM->addHElem( $xSel->getListaDeEmpresas("empresa", true)->get(true) );
						
						if(MODO_NOOB == true){
							$xFRM->addAvisoInicial("MS.MSG_ELIJA_EMPLEADOR", true, "TR.XD");
						}
						
						$xFRM->endSeccion();
					} else {
						$xFRM->OHidden("empresa", $empresa);
						
						$ready		= true;
					}
					
				} else {
					$xFRM->OHidden("empresa", FALLBACK_CLAVE_EMPRESA);
					$ready		= true;
				}
				
				
				//if($xProd->getE)
				
				
			} else {
				
				$ready		= false;
			}
			
		}
		
		
	} else {
		$xHP->goToPageX("../utils/frmbuscarsocio.php?next=addcredito");
		$ready		= false;
	}
	
}





$xFRM->OHidden("idorigen", $idorigen);
$xFRM->OHidden("origen", $tipoorigen);



if($ready == true){
	
	if($persona > DEFAULT_SOCIO){
		$xPer	= new cSocio($persona);
		if($xPer->init() == true){
			//$xFRM->addSeccion("idinfopers", "TR.Cliente");
			//$xFRM->addHElem( $xPer->getFicha(false, true, "", true) );
			//$xFRM->OHidden("persona", $persona);
			//$xFRM->endSeccion();
		}
	} else {
		
		
		if($ConOrigen == false){
			$xFRM->addPersonaBasico("", false, $persona, "jsaGetPerfilDePersona();");
		} else {
			$xFRM->addPersonaBasico("", false, $persona);
		}
	}
	
	if($xVals->empresa($empresa) == true){
		$xEmp		= new cEmpresas($empresa);
		if($xEmp->init() == true){
			$xFRM->addSeccion("idempx", "TR.EMPRESA");
			$xFRM->addHElem($xEmp->getFicha() );
			//$xFRM->addTag($xFRM->getT("TR.EMPLEADOR") . ":" . $xEmp->getNombreCorto(), "warning");
			$xFRM->endSeccion();
		}
	}
	
	$xFRM->addSeccion("didivgeneral", "TR.INFORMACION_GENERAL");
	
	
	
	
	//========== Producto de Credito
	
	$selPdto		= $xSel->getListaDeProductosDeCredito("", $producto, true);
	
	if($xVals->empresa($empresa) == true){
		$xSel->getListaDeProductosDeCreditoConSeguimiento();
		//$selPdto	= $xSel->getListaDeProductosDeCreditoNomina("", $producto);
		$selPdto	= $xSel->getListaDeProductosDeCreditoXEmpresa("", $empresa, $producto);
	}
	
	if($ConOrigen == false){
		$selPdto->addEvent("onblur", "jsaGetPerfilDeProducto()");
		$selPdto->addEvent("onchange", "jsaGetPerfilDeProducto()");
	}
	if($showProd == true){
		$xFRM->addHElem($selPdto->get("TR.producto de credito", true) );
	}
	//==================== End producto
	
	
	$xFRM->ODate("idFechaSolicitud", $fecha, "TR.Fecha de Solicitud");
	
	//==================== Periodicidad de Pago
	if($producto>0){
		$xHProd		= new cHCreditosProductos($producto); $xHProd->init();
		
		$selFreq	= $xHProd->getListaDePeriocidadDePago("", $frecuencia, $empresa);// $xSel->getListaDePeriocidadDePago("", $frecuencia);
		$selFreq->addEvent("onblur", "jsSetFrecuenciaDePagos(this)");
		$xFRM->addHElem($selFreq->get(true));
		
	} else {
		$selFreq	= $xSel->getListaDePeriocidadDePago("", $frecuencia);
		$selFreq->addEvent("onblur", "jsSetFrecuenciaDePagos(this)");
		$xFRM->addHElem($selFreq->get(true));
	}
	//==================== END .- Periodicidad de Pago
	if($producto>0){
		$xHProd		= new cHCreditosProductos($producto); $xHProd->init();
		$xSelProdTC	= $xHProd->getListaDeTipoDeCuota();
		$xFRM->addHElem($xSelProdTC->get(true));
		
		
	} else {
		$xFRM->addHElem($xSel->getListaDeTipoDePago()->get(true));
	}
	
	//==================== Tipo de Cuota
	
	
	//==================== END.- Tipo de Cuota
	//=============== Tipo y lugart de cobro
	//if($SinLugarPag == true){
	//	$xFRM->OHidden("idtipolugarcobro",);
	//} else {
	$xFRM->addHElem( $xSel->getListaDeTipoDeLugarDeCobro("", $TipoCobro)->get(true) );
	//}
	
	if($tipoorigen == $xCred->ORIGEN_ARRENDAMIENTO AND $pagos > 0){
		$xFRM->ODisabled_13("idnumerodepagos", $pagos, "TR.Numero de pagos");
	} else if ($tipoorigen == $xCred->ORIGEN_LINEAS AND $pagos > 0){
		$xFRM->ODisabled_13("idnumerodepagos", $pagos, "TR.Numero de pagos");
	} else {
		
		//==================== Numero de Pagos de Pago
		if($producto>0){
			$xHProd		= new cHCreditosProductos($producto); $xHProd->init();
			$xFRM->addHElem( $xHProd->getNumeroPagosOtorgable() );
		} else {
			$xFRM->OEntero("idnumerodepagos", $pagos, "TR.Numero de pagos");
		}
		
		//==================== END .- Numero de Pagos de Pago
		
	}
	//==================== Tasa de Interes
	if($tasa > 0){
		$xFRM->OHidden("tasa", $tasa);
	} else {
		if($producto > 0){
			$xHProd		= new cHCreditosProductos($producto); $xHProd->init();
			$xFRM->addHElem( $xHProd->getTasaOtorgable($persona) );
		} else {
			$xFRM->OTasa("tasa", $tasa);
		}
	}
	//==================== Tasa de Mora
	if($TasaDeMora > 0){
		$xFRM->OHidden("tasamora", $TasaDeMora);
	} else {
		if($producto > 0){
			$xHProd		= new cHCreditosProductos($producto); $xHProd->init();
			$xFRM->addHElem( $xHProd->getTasaMoraOtorgable($persona) );
		} else {
			$xFRM->OTasa("tasamora", $TasaDeMora, "TR.TASA MORA");
		}
	}
	
	
	
	
	if($monto > 0){
		$xCant	= new cCantidad($monto);
		$xFRM->OHidden("idmonto", $monto);
		$xFRM->addHElem($xCant->getFicha());
		
	} else {
		
		//==================== Monto de Credito
		if($producto>0){
			$xHProd		= new cHCreditosProductos($producto); $xHProd->init();
			$xFRM->addHElem( $xHProd->getMonedaMontoOtorgable($persona) );
		} else {
			$xFRM->addHElem($xTxt->getDeMoneda("idmonto", "TR.Monto Solicitado", $monto, true));
		}
		
		
		//END ==================== Monto de Credito
	}

	if($producto == CREDITO_PRODUCTO_CON_PRESUPUESTO){
		//Destino Detallado
		$xFRM->OHidden("iddestinodecredito", 98);//98 == DETALLADO
		$xFRM->OHidden("iddescripciondestino", "");
	} else {
		$xFRM->addSeccion("iddivdestino", "TR.DESTINO DE LOS_RECURSOS");
		
		//==================== Destino de Creditos
		$SoloConIVA		= ($xSoc->getEsExentoDeIVA() == true ) ? false : true;
		//TODO : Determinar Destino por producto
		$xSelDestino	= $xSel->getListaDeDestinosDeCredito("", $destino, false);//$SoloConIVA
		$txt2			= new cHText();
		$txt2->setDivClass("");
		$xFRM->addDivSolo($xSelDestino->get(false), $txt2->get("iddescripciondestino", false, "TR.Descripcion del Destino de los_recursos"), "tx24", "tx24" );
		$xFRM->endSeccion();
		
		
		//==================== Destino de Creditos
	}
	$xFRM->endSeccion();
	$xFRM->addSeccion("iddfechasx", "TR.FECHA_PROGRAMADA");
	
	//========================================== Fecha de vencimiento
	$xFRM->ODate("idFechaMinistracion", false, "TR.Fecha de Ministracion");
	
	if($SinFinalPlazo == true OR ($tipoorigen == $xCred->ORIGEN_ARRENDAMIENTO AND $pagos > 0) OR ($SinFechaVenc == true) ){
		$xFRM->OHidden("idFechaVencimiento", fechasys());
	} else {
		$xFRM->ODate("idFechaVencimiento", false, "TR.Fecha de Vencimiento");
	}
	
	$xFRM->endSeccion();
	//====================================== Ahorro condicionado
	if(MODULO_CAPTACION_ACTIVADO == true){
		$xFRM->addSeccion("iddivahorro", "TR.AHORRO_CONDICIONADO");
		$xFRM->addCuentaCaptacionBasico(false, CAPTACION_TIPO_VISTA, false, $cuenta);
		$xFRM->endSeccion();
	} else {
		$xFRM->addHTML("<input type='hidden' value='" . DEFAULT_CUENTA_CORRIENTE . "' id='idcuenta' name='idcuenta' />");
	}
	//====================================== cuestionario
	$xFRM->addSeccion("didivdivision", "TR.CUESTIONARIO");
	//si es credito renovado
	if($tipoorigen == $xCred->ORIGEN_ARRENDAMIENTO AND $idorigen > 0){
		$xFRM->OHidden("idrenovado", "false");
	} else {
		$xFRM->OCheck("TR.Es Credito Renovado", "idrenovado");
	}
	if(MODULO_AML_ACTIVADO == true){
		$xFRM->OCheck("TR.PREGUNTA_AML_CREDITO_2", "idpropietario");
		$xFRM->OCheck("TR.PREGUNTA_AML_CREDITO_1", "idproveedor");
	}
	$xFRM->endSeccion();
	//====================================== Otros
	//-- Oficial de Credito
	
	$xFRM->addSeccion("iddivotros", "TR.OTROS");
	if($OffByHer == true OR $OffByProd == true OR $OffByUsr == true){
		if($OffByUsr == true){
			$oficial	= getUsuarioActual();
		}
		$xFRM->OHidden("oficial", $oficial);
	} else {
		$xFRM->addHElem($xSel->getListaDeOficiales("oficial",SYS_USER_ESTADO_ACTIVO, $oficial)->get(true));
	}
	
	$xFRM->addObservaciones();
	$xFRM->endSeccion();
		
	$xFRM->addCerrar();
	$xFRM->OButton("TR.Validar Credito", "jsValidarCredito()", $xFRM->ic()->CHECAR, "idcheck", "green");
	$xFRM->addToolbar("<span id='creditoaprobado'></span>");
	
	if($tipoorigen == $xCred->ORIGEN_ARRENDAMIENTO AND $idorigen > 0){
		$xFRM->addDisabledInit("iddestinodecredito");
		$xFRM->addDisabledInit("idtipolugarcobro");
		$xFRM->addDisabledInit("idtipodepago");
		$xFRM->addDisabledInit("idproducto");
		$xFRM->addDisabledInit("idperiocidad");
		
	}
	
	if($tipoorigen == $xCred->ORIGEN_LINEAS AND $idorigen > 0){
		//$xFRM->addDisabledInit("iddestinodecredito");
		//$xFRM->addDisabledInit("idtipolugarcobro");
		$xFRM->addDisabledInit("idtipodepago");
		$xFRM->addDisabledInit("idproducto");
		$xFRM->addDisabledInit("idperiocidad");
	}
	if($OffByUsr == true){
		$xFRM->addTag($xFRM->getT("TR.OFICIAL_DE_CREDITO" . " : " . $xUsr->getAlias()) );
	}
	if(SAFE_ON_DEV == true AND $producto > 0){
		$xFRM->OButton("TR.PRODUCTO CREDITO", "var xG=new Gen();xG.w({url:'../frmcreditos/frmdatos_de_convenios_de_creditos.xul.php?producto=$producto', tab:true});", $xFRM->ic()->EJECUTAR, "cmdbtn101", "green2");
	}
	
	
	$xFRM->addAviso(" ");
}


//========================

$xFRM->OHidden("idmontomaximo", $MontoMaximo);
if($MontoMaximo>0){
	$xFRM->addTag($xFRM->getT("TR.MAXVALOR") . " : " . getFMoney($MontoMaximo));
}
echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
?>
<script>
var wFrm 			= document.frmsolicitud;
var mMonto			= 0;
var xGen			= new Gen();
var xF				= new FechaGen();
var mNumPago		= $("#idnumerodepagos");
var mTipoPago		= $("#idtipodepago");
var mPeriocidad		= $("#idperiocidad");


function jsChecarFinalDePlazo(){
	var idnumpagos		= entero(mNumPago.val());
	var idperiodicidad	= entero(mPeriocidad.val());
	var fechaVenc		= $("#idFechaVencimiento").val();
	var fechaMin		= $("#idFechaMinistracion").val();
	
	
	if(idnumpagos <= 0){
		xGen.alerta({ msg : "CREDITO_FALLA_PAGOMIN"});
		return false;
	}
	if(idperiodicidad == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO){
		if(idnumpagos != 1){
			xGen.alerta( { msg : "CREDITO_NPAGOS_INCORRECTO", tipo: "error"} );
			return false;
		}
		if(xF.getDiffEnDias(fechaVenc, fechaMin)>0){
		} else {
			xGen.alerta( { msg : "CREDITO_FALLA_FVENC", tipo: "error"} );
			return false;
		}
		//Validar Fecha  
		
	}
	return true;
}

function jsSetFrecuenciaDePagos(evt){
	var mFreq		= entero(evt.value);
	var mNumPago	= $("#idnumerodepagos");
	var mTipoPago	= $("#idtipodepago");
	var mFechaVenc	= $("#idFechaVencimiento");
	
	if(mFreq == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO ){
		mNumPago.css("display" , "none");
		mNumPago.val(1);
		mTipoPago.css("display" , "none");
		mTipoPago.val(CREDITO_TIPO_PAGO_UNICO);
		mFechaVenc.css("display" , "inherit");
	} else {
		mNumPago.css("display" , "inherit");
		mTipoPago.css("display" , "inherit");
		mFechaVenc.css("display" , "none");
		mTipoPago.val(CREDITO_TIPO_PAGO_PERIODICO);
	}
}
function jsValidarMonto(){
	mMonto	= flotante($("#sol").val());
	jsaValidarMontoDeCredito();
	setTimeout("jsNotificarValidacion()", 1000);
}
function jsNotificarValidacion() {
	var nMonto	= flotante($("#sol").val());
	if (nMonto < mMonto) {
		alert("El Monto capturado " + mMonto + " ha sido Cambiado a " + nMonto + "\npor el sistema en base a Politicas de la Institucion.\Consulte a su Administrador." );
	}
	///jsaGetLetrasByNumero();
}
function jsValidarCredito(){
	var mNumPago	= $("#idnumerodepagos").val();
	var mTipoPago	= $("#idtipodepago").val();
	var mFreq		= $("#idperiocidad").val();
	var FMin		= $("#idFechaMinistracion").val();
	var FVenc		= $("#idFechaVencimiento").val();
	var FAct		= $("#idFechaSolicitud").val();
	
	if($("#persona").length > 0){
		var Pers	= $("#persona").val();
	} else {
		var Pers	= $("#idsocio").val();
	}
	var Monto		= flotante($("#idmonto").val());
	var Cont		= $("#idcuenta").val();
	var Pdto		= $("#idproducto").val();
	var claveorigen	= $("#idorigen").val();
	var tipoorigen	= $("#origen").val();
	var idmontomax	= flotante($("#idmontomaximo").val());
	
	if(jsChecarFinalDePlazo() == false){
		return false;
	}
	
	if(idmontomax > 0){
		if( Monto > idmontomax ){
			xG.alerta({msg: "CREDITO_NO_MAYOR", tipo: "error"});
			return false;
		}
	}
	
	$("#creditoaprobado").empty();
	var murl		= "solicitud_de_credito.validacion.frm.php?persona=" + Pers;
	murl			+= "&monto=" + Monto + "&contrato=" + Cont + "&periocidad=" + mFreq + "&producto=" + Pdto + "&vencido=" + FVenc + "&ministrado=" + FMin + "&pagos=" + mNumPago + "&fecha=" + FAct;
	murl			+= "&tipoorigen=" + tipoorigen + "&claveorigen=" + claveorigen;

	
	$form			= document.getElementById("frmsolicitudcredito");
	if (!$form.checkValidity || $form.checkValidity()){
		xGen.w({ url : murl , tiny: true, callback : jsaValidarCredito });
	} else {
		xG.showInvalidInputs({form: "frmsolicitudcredito",showAlert:true});
		
	}
}
function jsFormularioValidado(){
	$("#frmsolicitudcredito").submit(); 
}

</script>
</html>