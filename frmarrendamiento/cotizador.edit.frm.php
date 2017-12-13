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
$xHP			= new cHPage("TR.COTIZADOR", HP_FORM);
$xQL			= new MQL();
$xLi			= new cSQLListas();
$xF				= new cFecha();
$xCProc			= new cCreditosProceso();
$xFormu			= new cFormula();
//$xDic		= new cHDicccionarioDeTablas();
$jxc 			= new TinyAjax();
$xUser			= new cSystemUser(getUsuarioActual()); $xUser->init();
$xRuls			= new cReglaDeNegocio();
$NoUsarTIIE		= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_ARREND_SIN_TIIE);
$NoUsarResidual	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_ARREND_COT_NORES);
$AunMasSimple	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_ARREND_COT_RSIMPLE);
$ArrAjustes		= $xRuls->getArrayPorRegla($xRuls->reglas()->CREDITOS_ARREND_AJUSTM);
$NoUsarUsers	= $xRuls->getArrayPorRegla($xRuls->reglas()->CREDITOS_ARREND_NOUSERS);

$itemsNoVer		= "cmdImprimirPropuesta,comision_originador,tasa_compra";

$OlvidarTodo	= parametro("olvidar", false, MQL_BOOL);
$Nuevo			= parametro("nuevo", false, MQL_BOOL);


if($OlvidarTodo == true){
	$AunMasSimple	= ($AunMasSimple == true) ? false : true;
	$xHP->setTitle("TR.COTIZADOR .- ADMINISTRADOR");
}

$xFormu->init($xFormu->JS_LEAS_COT_VARS);	
$jsFormulaPrec	= $xFormu->getFormula();

$originador		= 1;
$suborigen		= 1;
$oficial		= getUsuarioActual();
$EsAdmin		= false;
$OnEdit			= false;
$TasaComision	= 0;
$EsOriginador	= false;
$EsAdministrado	= false;

//$EsActivo	= false;
if($xUser->getEsOriginador() == true){
	$xOrg	= new cLeasingUsuarios();
	if($xOrg->initByIDUsuario($xUser->getID()) == true){
		$originador				= $xOrg->getOriginador();
		$suborigen				= $xOrg->getSubOriginador();
		if($xOrg->getEsAdmin() == true){
			$EsAdmin			= true;
		}
		if($xOrg->getEsActivo() == false){
			$xHP->goToPageError(403);
		}
		$xOrg				= new cLeasingOriginadores($originador);
		if($xOrg->init() == true){
			$TasaComision	= $xOrg->getTasaComision();
		}
	}
	$EsOriginador	= true;
} else {
	$oficial	= $xUser->getID();
}
function jsaGetTasa($rac, $plazo, $clave, $olvidar, $nuevo){
	$tab 	= new TinyAjaxBehavior();
	$xTLeas	= new cLeasingTasas();
	$xEsc	= new cLeasingEscenarios();
	
	if($clave > 0 AND $nuevo == 0){
		$xLeas		= new cCreditosLeasing($clave);
		$xLeas->init();
		
		$rs			= $xEsc->getAll();
		foreach ($rs as $rw){
			$xEsc->init($rw);
			$idx	= $xEsc->getPlazo();
			$tab->add(TabSetValue::getBehavior("tasa_credito_$idx", $xLeas->getTasaInteres( $xEsc->getPlazo() ) ));
			$tab->add(TabSetValue::getBehavior("tasavec_$idx", $xLeas->getTasaVec( $xEsc->getPlazo() ) ));
		}
		
	} else {
		
		if($xTLeas->initByPlazoRAC($plazo, $rac) == true){
			
			$tab->add(TabSetValue::getBehavior("tasa_credito", $xTLeas->getTasa() ));
			$tab->add(TabSetValue::getBehavior("comision_apertura", $xTLeas->getComisionApertura() ));
			$tab->add(TabSetValue::getBehavior("comision_apertura_mny", $xTLeas->getComisionApertura() ));
			$tab->add(TabSetValue::getBehavior("tasa_compra", $xTLeas->getTasaVEC() ));
			
		}
		
		$rs			= $xEsc->getAll();
		foreach ($rs as $rw){
			$xEsc->init($rw);
			$idx	= $xEsc->getPlazo();
			if($xTLeas->initByPlazoRAC($idx, $rac) == true){
				$tab->add(TabSetValue::getBehavior("tasa_credito_$idx", $xTLeas->getTasa() ));
				$tab->add(TabSetValue::getBehavior("tasavec_$idx", $xTLeas->getTasaVEC() ));
				
			}
			
		}

	}
	return $tab->getString();
}
function jsaGetComision($originador, $clave, $olvidar, $nuevo){
	$xOrg	= new cLeasingOriginadores($originador);
	$tasa	= 0;
	if($xOrg->init() ){
		$tasa = $xOrg->getTasaComision();
	}
	return $tasa;
}
function jsaGetCostoGPS($plazo, $plan, $montogps, $clave, $olvidar, $nuevo){
	
	$xQL	= new MQL();
	//"#tasa_credito"
	$tab = new TinyAjaxBehavior();
	
	$xGPS	= new cLeasingGPSCosteo();
	if($xGPS->initByPlazoTipo($plazo, $plan) == true){
		$monto	= $xGPS->getMonto();
		$monto	= ($monto >= $montogps) ? $monto : $montogps;
		
		$tab->add(TabSetValue::getBehavior("monto_gps", $monto ));
		
	}
	$xEsc		= new cLeasing_escenarios();
	$rs			= $xQL->getDataRecord("SELECT * FROM `leasing_escenarios`");
	foreach ($rs as $rw){
		$xEsc->setData($rw);
		$idx	= $xEsc->plazo()->v();
		$xGPS	= new cLeasingGPSCosteo();
		if($xGPS->initByPlazoTipo($idx, $plan) == true){
			$monto		= $xGPS->getMonto();
			if($idx == $plazo){ //Si es igual al plazo actual, evaluar si es mayor 
				$monto	= ($monto >= $montogps) ? $monto : $montogps;
			}
			$tab->add(TabSetValue::getBehavior("monto_gps_$idx", $xGPS->getMonto() ));
		} else {
			$tab->add(TabSetValue::getBehavior("monto_gps_$idx", 0 ));
		}
	}
	return $tab->getString();
}
function jsaGetCostos($entidad, $precio, $clave, $olvidar, $nuevo){
	$sql		= "SELECT * FROM `vehiculos_tenencia` WHERE `entidadfederativa`=$entidad LIMIT 0,1";
	$xQL		= new MQL();
	$xT			= new cVehiculos_tenencia();
	$xT->setData($xQL->getDataRow($sql));

	$tenencia	= $precio  * ($xT->tenencia()->v()/100);
	$tenencia	= round($tenencia,2);
	if($tenencia > $xT->limitetenencia()->v()){
		$tenencia	= $xT->limitetenencia()->v();
	}
	
	$tab = new TinyAjaxBehavior();
	
	$tab -> add(TabSetValue::getBehavior("monto_placas", $xT->placas()->v()));
	$tab -> add(TabSetValue::getBehavior("monto_gestoria", $xT->cobrogestoria()->v()));
	$tab -> add(TabSetValue::getBehavior("monto_tenencia", $tenencia));
	
	$tab -> add(TabSetValue::getBehavior("monto_placas_mny", getFMoney($xT->placas()->v()) ));
	$tab -> add(TabSetValue::getBehavior("monto_gestoria_mny", getFMoney($xT->cobrogestoria()->v()) ));
	$tab -> add(TabSetValue::getBehavior("monto_tenencia_mny", getFMoney($tenencia) ));
	
	//$tab -> add(TabSetValue::getBehavior("monto_notario", 0)); //Pendiente de aclarar

	return $tab -> getString();
}
function jsaGetResidual($precio, $aliado, $plazo, $residuales, $anticipo, $clave, $olvidar, $nuevo, $rac){
	$xEmul		= new cLeasingEmulaciones($plazo, 0 ,0);
	$xEsc		= new cLeasingEscenarios();
	$tab 		= new TinyAjaxBehavior();
	$xQL		= new MQL();
	$tasares	= 0;
	$DRes		= explode(",", $residuales, 10);
	$NRes		= count($DRes);
	$NRes		= ($nuevo == 1) ? 0  : $NRes;
	$rs			= $xEsc->getAll();
	
	foreach ($rs as $rw){
		$xEsc->init($rw);
		$idx		= $xEsc->getPlazo();
		$tasavec	= 0;
		$valorvec	= 0;
		
		if($clave <= 0){
			$valorvec		= $xEmul->getValorDeVenta($precio, $anticipo, $tasavec, $idx, $aliado, $rac);
		} else {
			$xLeas	= new cCreditosLeasing($clave);
			if($xLeas->init()){
				$valorvec	= $xLeas->getValorDeVenta($idx);
			}
			
		}
		$tab->add(TabSetValue::getBehavior("valorvec_$idx", $valorvec ));
	}
	if($NRes <= 0){
		$rs			= $xEsc->getAll();
		foreach ($rs as $rw){
			$xEsc->init($rw);
			$idx		= $xEsc->getPlazo();
			$tr			= false;
			//$tasavec	= 0;
			if($clave > 0){
				$xLeas	= new cCreditosLeasing($clave);
				if($xLeas->init()){
					$tr			= $xLeas->getTasaResidualPzo($idx);
					//$tasavec	= $xLeas->getTasaVec($idx);
				}
				
			}
			if($nuevo == 1){
				if($tr <= 0){
					$xRes	= new cLeasingValorResidual();
					if($xRes->initByPlazoTipo($idx)== true){
						$tr	= $xRes->getPorcientoResidual();
					}
				}
			}
			
			
			$res 	= $xEmul->getValorResidual($precio, $aliado, $idx, $tr, $anticipo);
			
			$tab->add(TabSetValue::getBehavior("tasaresidual_$idx", $tr ));
			$tab->add(TabSetValue::getBehavior("residual_$idx", $res ));
			
			if($idx == $plazo){
				$tab->add(TabSetValue::getBehavior("monto_residual", $res ));
			}
		}
	} else {
		foreach ($DRes as $idx => $IRes){
			$DTasa	= explode("-", $IRes);
			$PRes	= setNoMenorQueCero($DTasa[0]);
			$TRes	= (isset($DTasa[1])) ? $DTasa[1] : 0;
			$TRes	= setNoMenorQueCero($TRes);
			
			$res 	= $xEmul->getValorResidual($precio, $aliado, $PRes, $TRes, $anticipo);
			$tab->add(TabSetValue::getBehavior("residual_$PRes", $res ));
			
			//setError($TRes);
			
			//$tab->add(TabSetValue::getBehavior("tasaresidual_$PRes", $TRes ));
			
			if($PRes == $plazo){
				$tab->add(TabSetValue::getBehavior("monto_residual", $res ));
			}
		}
	}
	
	return $tab->getString();
}
function jsaAsociar($idpersona, $idcontrol){
	$xP		= new cCreditosLeasing($idcontrol);
	if($xP->init()){
		$xP->setPersona($idpersona);
	}
	return $xP->getMessages();
}

function jsaActualizarCredito($credito, $montosolicitado){
	$montosolicitado	= setNoMenorQueCero($montosolicitado);
	
	if($montosolicitado > 0 AND $credito > DEFAULT_CREDITO){
		$xCred		= new cCredito($credito);
		$msg		= "";
		if($xCred->init() == true){
			$msg	.= $xCred->setCambiarMontoSolicitado($montosolicitado, true);
			$msg	.= $xCred->setCambiarMontoAutorizado($montosolicitado, true);
		}
		$msg		.= $xCred->getMessages();
		return $msg;
	}
}

function jsaAgregarOmitidos($idomitir, $idquitar, $idid){
	$idx	= "arrendamiento.omitidos.$idid";
	$xCache	= New cCache();
	
	$xCot	= new cCreditosLeasing($idid);
	
	$xCot->setOmitidos($idomitir, $idquitar);
	
}

$jxc ->exportFunction('jsaGetTasa', array('tipo_rac', 'plazo', 'idoriginacion_leasing', 'idolvidar', 'idnuevo'));
$jxc ->exportFunction('jsaGetComision', array('originador','idoriginacion_leasing','idolvidar', 'idnuevo'), "#comision_originador");
$jxc ->exportFunction('jsaGetCostoGPS', array('plazo', 'tipo_gps', 'monto_gps','idoriginacion_leasing','idolvidar', 'idnuevo'));
$jxc ->exportFunction('jsaGetCostos', array('entidadfederativa', 'precio_vehiculo','idoriginacion_leasing', 'idolvidar', 'idnuevo'));
$jxc ->exportFunction('jsaGetResidual', array('precio_vehiculo','monto_aliado', 'plazo', 'residuales', 'monto_anticipo','idoriginacion_leasing','idolvidar', 'idnuevo', 'tipo_rac'));

$jxc ->exportFunction('jsaAsociar', array('persona', 'idoriginacion_leasing'), "#idavisos");
$jxc ->exportFunction('jsaActualizarCredito', array('credito', 'total_credito'), "#idavisos");

$jxc ->exportFunction('jsaAgregarOmitidos', array('idomitidos1', 'idomitidos2', 'idid'), "#idavisos");


$jxc ->process();

$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT);
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$observaciones	= parametro("idobservaciones");

$xHP->init();

$xFRM		= new cHForm("frmcotizacion", "./", "frmcotizacion");
$xSel		= new cHSelect();


$xFRM->setTitle($xHP->getTitle());

if($clave>0){
	$xFRM->OButton("TR.CALCULAR / GUARDAR", "jsCalcularEscenarios()", $xFRM->ic()->CALCULAR, "btn_calcular", "green");
}

$xFRM->setNoAcordion();// $xFRM->setIsWizard();

$xTabla		= new cOriginacion_leasing();
$xLeas		= new cCreditosLeasing($clave);
$xMon		= new cTesoreriaMonedas("TIIE"); $xMon->init();

$valorTIIE	= ($NoUsarTIIE == true) ? 0 : $xMon->getValor();

//=======================  Datos para Creditos Nuevos



$xTabla->originador($originador);
$xTabla->suboriginador($suborigen);
$xTabla->oficial($oficial);
$xTabla->usuario(getUsuarioActual());
$xTabla->tasa_tiie($valorTIIE);
$xTabla->comision_originador($TasaComision);
$xTabla->tasa_iva(TASA_IVA);
$xTabla->fecha_origen(fechasys());
$xTabla->tipo_leasing($xLeas->TIPO_PURO);
$xTabla->idoriginacion_leasing("NULL");
$xTabla->persona($persona);
$xTabla->credito($credito);
$xTabla->paso_proceso($xCProc->PASO_REGISTRADO);
$xTabla->estatus(SYS_UNO);
$xTabla->domicilia(SYS_UNO);
$xTabla->tipo_rac($xLeas->TIPO_RAC_PEQ);
$xTabla->montoajuste(0);
$xTabla->administrado(SYS_CERO);
$xTabla->monto_tenencia(0);

//=======================  Datos para Creditos Editados.
if($clave >0){
	$xTabla->setData($xTabla->query()->initByID($clave));
	$OnEdit		= true;
	//Imprimir Propuesta
	if($xTabla->credito()->v() <= DEFAULT_CREDITO){
		$xFRM->OButton("TR.IMPRIMIR COTIZACION", "var xC=new CredGen();xC.getLeasingCotizacion($clave)", $xFRM->ic()->IMPRIMIR);
	}
	
	//Si el Usuario no es Originador
	if($EsOriginador == false){
		if($xTabla->persona()->v() <= DEFAULT_SOCIO){
			$xFRM->OButton("TR.AGREGAR PERSONA", "jsAgregarPersona()", $xFRM->ic()->PERSONA, "cmdagregarpersona", "persona");

		} else {
			$xFRM->OButton("TR.VER PERSONA", "jsVerPersona()", $xFRM->ic()->PERSONA, "cmdverpersona", "persona");
		}
		//Si el credito no ha sido asignado
		if($xTabla->credito()->v() <= DEFAULT_CREDITO){
			
		}
		if($xTabla->credito()->v() <= DEFAULT_CREDITO){
			//Imprimir Propuesta
			$xFRM->OButton("TR.IMPRIMIR PROPUESTA", "var xC=new CredGen();xC.getLeasingPropuesta($clave)", $xFRM->ic()->IMPRIMIR, "cmdImprimirPropuesta");
		}
	} else {
		if($xTabla->persona()->v() <= DEFAULT_SOCIO){
			//$xFRM->OButton("TR.AGREGAR PERSONA", "jsAgregarPersona()", $xFRM->ic()->PERSONA);
		}
	}
	//Si es Administrado
	if($xTabla->administrado()->v() == SYS_UNO){
		$EsAdministrado	= true;
	}
	//Iniciar $xleas
	$xLeas	= new cCreditosLeasing($clave);
	$xLeas->init();
	
	$xFRM->OButton("TR.AGREGAR DOCUMENTOS", "jsAgregarDocumentos()", $xFRM->ic()->ARCHIVOS);
}




$xFRM->OHidden("idoriginacion_leasing", $xTabla->idoriginacion_leasing()->v());
$xFRM->OHidden("fecha_origen", $xTabla->fecha_origen()->v());

$xFRM->OHidden("tasa_credito", $xTabla->tasa_credito()->v());
$xFRM->OHidden("tasa_tiie", $xTabla->tasa_tiie()->v());


$xFRM->OHidden("total_credito", $xTabla->total_credito()->v());
$xFRM->OHidden("cuota_accesorios", $xTabla->cuota_accesorios()->v());
$xFRM->OHidden("cuota_tenencia", $xTabla->cuota_tenencia()->v());
$xFRM->OHidden("cuota_mtto", $xTabla->cuota_mtto()->v());
$xFRM->OHidden("cuota_seguro", $xTabla->cuota_seguro()->v());
$xFRM->OHidden("cuota_garantia", $xTabla->cuota_garantia()->v());

$xFRM->OHidden("paso_proceso", $xTabla->paso_proceso()->v());
$xFRM->OHidden("usuario", $xTabla->usuario()->v());
$xFRM->OHidden("tipo_leasing", $xTabla->tipo_leasing()->v());

$xFRM->OHidden("tasa_iva", $xTabla->tasa_iva()->v());



$xFRM->OHidden("monto_directo", $xTabla->monto_directo()->v());
$xFRM->OHidden("monto_residual", $xTabla->monto_residual()->v());
//
$xFRM->OHidden("cuota_vehiculo", $xTabla->cuota_vehiculo()->v());
$xFRM->OHidden("cuota_aliado", $xTabla->cuota_aliado()->v());

$xFRM->OHidden("monto_comision", $xTabla->monto_comision()->v());
$xFRM->OHidden("monto_originador", $xTabla->monto_originador()->v());

$xFRM->OHidden("estatus", $xTabla->estatus()->v());



$xFRM->addSeccion("iddoriginador", "TR.DATOS DEL ORIGINADOR");

//$xFRM->OMoneda("tipo_leasing", $xTabla->tipo_leasing()->v(), "TR.TIPO LEASING");

if($xUser->getEsOriginador() == false){
	//Si no hay credito se habilitan los campos de edicion
	if($xTabla->credito()->v() <= DEFAULT_CREDITO){
		if($OlvidarTodo == true){
			$xFRM->addHElem( $xSel->getListaDeOficiales("oficial", SYS_USER_ESTADO_ACTIVO,  $xTabla->oficial()->v())->get(true) );
		} else {
			$xFRM->OHidden("oficial", $xTabla->oficial()->v());
		}
		$xSelOrg	= $xSel->getListaDeOriginadores("originador", $xTabla->originador()->v());
		if($xTabla->credito()->v() <= DEFAULT_CREDITO){
			$xSelOrg->addEvent("onblur", "jsDatosIniciales()");
			$xSelOrg->addEvent("onchange", "jsDatosIniciales()");
		}
		
		if($NoUsarUsers == false){
			$xFRM->addHElem($xSelOrg->get(true) );
			$xFRM->addHElem($xSel->getListaDeSubOriginadores("suboriginador", $xTabla->suboriginador()->v())->get(true) );
		} else {
			$xFRM->OHidden("originador", $xTabla->originador()->v());
			$xFRM->OHidden("suboriginador", $xTabla->suboriginador()->v());
		}
		
		if($OnEdit == false){
			$xFRM->OHidden("comision_originador", $xTabla->comision_originador()->v());
			$xFRM->OHidden("comision_apertura", $xTabla->comision_apertura()->v());
		} else {
			$xFRM->OMoneda("comision_originador", $xTabla->comision_originador()->v(), "TR.COMISION ORIGINADOR");
			$xFRM->OMoneda2("comision_apertura", $xTabla->comision_apertura()->v(), "TR.COMISION_POR_APERTURA");
		}
		
	} else {
		$xFRM->OHidden("oficial", $xTabla->oficial()->v());
		$xFRM->OHidden("originador", $xTabla->originador()->v());
		$xFRM->OHidden("suboriginador", $xTabla->suboriginador()->v());
		$xFRM->OHidden("comision_originador", $xTabla->comision_originador()->v());
		$xFRM->OHidden("comision_apertura", $xTabla->comision_apertura()->v());
		//Agrega ficha de ejecutivo
		//Agregar datos de originador y usuario
		//Bloquear los demás controles.
	}
} else {
	$xFRM->OHidden("oficial", $xTabla->oficial()->v());
	$xFRM->OHidden("originador", $xTabla->originador()->v());
	if($EsAdmin == true){
		$xFRM->addHElem($xSel->getListaDeSubOriginadores("suboriginador", $xTabla->suboriginador()->v(), $xTabla->originador()->v())->get(true) );
	} else {
		$xFRM->OHidden("suboriginador", $xTabla->suboriginador()->v());
	}
	$xFRM->OHidden("comision_originador", $xTabla->comision_originador()->v());
	$xFRM->OHidden("comision_apertura", $xTabla->comision_apertura()->v());
	//$xFRM->addHElem( $xUser->getFicha() );
	$xUsrOrg	= new cLeasingUsuarios($xTabla->suboriginador()->v());
	if($xUsrOrg->init() == true){
		$xFRM->addHElem( $xUsrOrg->getFicha() );
	}
}


$xFRM->endSeccion();
//========================================================================================
//========================================= CLIENTE ======================================
//========================================================================================
$PersonaCargado			= false;
$xFRM->addSeccion("iddcliente", "TR.DATOS DEL CLIENTE");

$xFRM->OHidden("persona", $xTabla->persona()->v());
$xFRM->OHidden("credito",$xTabla->credito()->v());

if($xTabla->persona()->v() > DEFAULT_SOCIO){
	$xSoc	= new cSocio($xTabla->persona()->v());
	if($xSoc->init() == true){
		$xFRM->addHElem($xSoc->getFicha(false, false, "", true) );
		$xTabla->nombre_cliente($xSoc->getNombreCompleto());
		$PersonaCargado	= true;
		if($xSoc->getEsPersonaFisica() == false){
			$xTabla->es_moral(SYS_UNO);
		} else {
			$xTabla->es_moral(SYS_CERO);
		}
		$itemsNoVer	.= ",mail,tel";
	}
	$xFRM->OHidden("nombre_cliente", $xTabla->nombre_cliente()->v());
	//Vinculo de Credito
	if($xTabla->credito()->v() > DEFAULT_CREDITO){
		$xCred	= new cCredito($xTabla->credito()->v());
		if($xCred->init() == true){
			$xFRM->addHElem($xCred->getFicha());
			if($EsOriginador == false){
				$xFRM->OButton("TR.VER CREDITO", "jsVerCredito()", $xFRM->ic()->CREDITO, "", "credito");
				$xFRM->OButton("TR.DATOS_DE_TRANSFERENCIA", "var cGen=new CredGen();cGen.setAgregarBancos(" .$xTabla->credito()->v() . ");", $xFRM->ic()->BANCOS);
				//Validar Si existe Dato del Vehiculo
				$xArr	= new cCreditosLeasing($clave);
				if($xArr->init() == true){
					$xFRM->OButton("TR.AGREGAR FLOTA", "jsAgregarDatosVehiculo()", $xFRM->ic()->TRUCK);
				}
				//======================== Plan Cliente
				$xFRM->OButton("TR.PLAN_DE_PAGOS CLIENTE", "jsGetPlanCliente()", $xFRM->ic()->CALENDARIO1);
			}
			if($xCred->getEsAfectable() == true){
				
				$xFRM->addJsInit("jsDesactivarCotizacion();");
				//============================ Agregar
				$xFRM->addDisabledInit("tipo_rac");
				$xFRM->addDisabledInit("marca");
				$xFRM->addDisabledInit("tipo_uso");
				$xFRM->addDisabledInit("segmento");
				$xFRM->addDisabledInit("entidadfederativa");
				$xFRM->addDisabledInit("plazo");
				$xFRM->addDisabledInit("oficial");
				$xFRM->addDisabledInit("originador");
				$xFRM->addDisabledInit("suboriginador");
				$itemsNoVer	.= ",iddopts";
			}
			//$xFRM->addDisabledInit("");
			//$xFRM->addDisabledInit("");
			
		} else {
			if($EsOriginador == false){
				$xFRM->OButton("TR.AGREGAR CREDITO", "jsAgregarCredito()", $xFRM->ic()->DINERO);
			}
		}
	} else {
		if($EsOriginador == false){
			$xFRM->OButton("TR.AGREGAR CREDITO", "jsAgregarCredito()", $xFRM->ic()->DINERO);
		}
	}
} else {
	$xFRM->OText("nombre_cliente", $xTabla->nombre_cliente()->v(), "TR.NOMBRE CLIENTE");
	$xFRM->setValidacion("nombre_cliente", $xFRM->VALIDARVACIO, "", true);
}
$xFRM->OText("nombre_atn", $xTabla->nombre_atn()->v(), "TR.NOMBRE ATN");
//--- 25/Mayo/2017
$xFRM->OText_13("tel", $xTabla->tel()->v(), "TR.TELEFONO");
$xFRM->OMail("mail", $xTabla->mail()->v(), "TR.CORREO_ELECTRONICO");


//============================= Agregar Tipo de Rac Condicionado
$xSelRac	= $xSel->getListaDeLeasingRAC("tipo_rac", $xTabla->tipo_rac()->v());
$xSelRac->addEvent("onblur", "jsaGetTasa()");
$xSelRac->addEvent("onchange", "jsaGetTasa()");

if($EsOriginador == true){
	if($xRuls->getInArrayPorRegla($xRuls->reglas()->CREDITOS_ARREND_FRM_DIS, "tipo_rac") == true){
		$xFRM->OHidden("tipo_rac", $xTabla->tipo_rac()->v());
	} else {
		$xFRM->addHElem( $xSelRac->get(true) );
	}
} else {
	$xFRM->addHElem( $xSelRac->get(true) );
}

if($PersonaCargado == false){
	$xFRM->OSiNo("TR.ES PERSONA_MORAL", "es_moral", $xTabla->es_moral()->v());
} else {
	$xFRM->OHidden("es_moral", $xTabla->es_moral()->v());
}
//======================== Tasa de Compra
if($OlvidarTodo == true){
	$xFRM->OMoneda("tasa_compra", $xTabla->tasa_compra()->v(), "TR.TASAVEC");
	$xFRM->setValidacion("tasa_compra", "jsActualizaResiduales", true);
} else {
	$xFRM->OHidden("tasa_compra", $xTabla->tasa_compra()->v());
}

$xFRM->endSeccion();
//========================================= VEHICULO ====================================
$xFRM->addSeccion("iddvehi", "TR.DATOS DEL VEHICULO");

$xFRM->addHElem($xSel->getListaDeVehiculosMarcas("marca", $xTabla->marca()->v())->get(true) );

$xFRM->addHElem($xSel->getListaDeVehiculosUsos("tipo_uso", $xTabla->tipo_uso()->v())->get(true));

$xFRM->addHElem($xSel->getListaDeVehiculosSegmentos("segmento", $xTabla->segmento()->v())->get(true));


$xFRM->OText_13("modelo", $xTabla->modelo()->v(), "TR.VERSION");
$xFRM->setValidacion("modelo", $xFRM->VALIDARVACIO, "", true);

$xFRM->OText_13("annio", $xTabla->annio()->v(), "TR.ANNIO");


$xFRM->OMoneda2("precio_vehiculo", $xTabla->precio_vehiculo()->v(), "TR.PRECIO VEHICULO");


if($AunMasSimple == true AND $EsOriginador == true){
	$xFRM->OHidden("monto_anticipo", $xTabla->monto_anticipo()->v());
} else {
	$xFRM->OMoneda2("monto_anticipo", $xTabla->monto_anticipo()->v(), "TR.ANTICIPORENTA");
}


$xSelEntidad	= $xSel->getListaDeEntidadesFed("entidadfederativa", true, $xTabla->entidadfederativa()->v());
$xSelEntidad->addEvent("onblur", "jsaGetCostos()");
$xSelEntidad->addEvent("onchange", "jsaGetCostos()");

$xFRM->addHElem( $xSelEntidad->get("TR.LUGAR DE EMPLACAMIENTO",true) );

$xSelEsc	= $xSel->getListaDeLeasingEscenarios("plazo", $xTabla->plazo()->v());
$xSelEsc->addEvent("onchange", "jsGetParametros()");
$xFRM->addHElem( $xSelEsc->get(true));


//========================================= ACCESORIOS ====================================
if($xUser->getEsOriginador() == false){
	$xFRM->endSeccion();
	$xFRM->addSeccion("iddacces", "TR.ACCESORIOS");
}

if(($xRuls->getInArrayPorRegla($xRuls->reglas()->CREDITOS_ARREND_FRM_DIS, "tipo_gps") == true) AND $OlvidarTodo == false){
	$xFRM->OHidden("tipo_gps", $xTabla->tipo_gps()->v() );
} else {

$xSelGPS	= $xSel->getListaDeVehiculosGPS("tipo_gps", $xTabla->tipo_gps()->v());
$xSelGPS->addEvent("onblur", "jsaGetCostoGPS()");
$xFRM->addHElem($xSelGPS->get(true) );

}

if($xUser->getEsOriginador() == false){

	if(($xRuls->getInArrayPorRegla($xRuls->reglas()->CREDITOS_ARREND_FRM_DIS, "tipo_gps") == true) AND $OlvidarTodo == false){
		$xFRM->OHidden("monto_gps", $xTabla->monto_gps()->v());
	} else {
		$xFRM->OMoneda2("monto_gps", $xTabla->monto_gps()->v(), "TR.MONTO PAQUETESGPS");
	}
	if(($xRuls->getInArrayPorRegla($xRuls->reglas()->CREDITOS_ARREND_FRM_DIS, "monto_aliado") == true) AND $OlvidarTodo == false){
		$xFRM->OHidden("monto_aliado", $xTabla->monto_aliado()->v());
		$xFRM->OHidden("describe_aliado", $xTabla->describe_aliado()->v());
	} else {
		$xFRM->OMoneda2("monto_aliado", $xTabla->monto_aliado()->v(), "TR.EQUIPOALIADO");
		$xFRM->OText("describe_aliado", $xTabla->describe_aliado()->v(), "TR.DESCRIPCION EQUIPOALIADO");
	}
	
	if(($xRuls->getInArrayPorRegla($xRuls->reglas()->CREDITOS_ARREND_FRM_DIS, "monto_accesorios") == true) AND $OlvidarTodo == false){
		
		$xFRM->OHidden("monto_accesorios", $xTabla->monto_accesorios()->v());
		$xFRM->OHidden("monto_tenencia", $xTabla->monto_tenencia()->v());
		$xFRM->OHidden("monto_garantia", $xTabla->monto_garantia()->v());
		$xFRM->OHidden("monto_mtto", $xTabla->monto_mtto()->v());
		$xFRM->OHidden("monto_gestoria", $xTabla->monto_gestoria()->v());
		
	} else {
		$xFRM->OMoneda2("monto_accesorios", $xTabla->monto_accesorios()->v(), "TR.ACCESORIOS");
		$xFRM->OMoneda2("monto_tenencia", $xTabla->monto_tenencia()->v(), "TR.TENENCIA");
		$xFRM->OMoneda2("monto_garantia", $xTabla->monto_garantia()->v(), "TR.GARANTIA");
		$xFRM->OMoneda2("monto_mtto", $xTabla->monto_mtto()->v(), "TR.MTTO");
		$xFRM->OMoneda2("monto_gestoria", $xTabla->monto_gestoria()->v(), "TR.GASTOSGESTORIA");
	}
	//================== Ajuste de Servicio
	$xSelS		= new cHSelect();
	$xSelS->setDivClass("tx4 tx18 green");
	
	if(count($ArrAjustes)>0){
		$arr		= array();
		$arr["0"]	= SYS_NINGUNO;
		foreach ($ArrAjustes as $idx => $idm){
			$arr[$idm] = getFMoney($idm);		}
		$xSelS->addOptions($arr);
		
		$xFRM->addHElem( $xSelS->get("montoajuste", "TR.SERVICIOS", $xTabla->montoajuste()->v()) );
		
	} else {
		$xFRM->OHidden("montoajuste", $xTabla->montoajuste()->v());
	}
	//==================
	$xFRM->OMoneda2("monto_seguro", $xTabla->monto_seguro()->v(), "TR.AUTOSEGURO");
		
	$xFRM->OMoneda2("monto_placas", $xTabla->monto_placas()->v(), "TR.COSTOPLACAS");
	
	$xFRM->OMoneda2("monto_notario", $xTabla->monto_notario()->v(), "TR.GASTOSNOTARIALES");
	
	if($OlvidarTodo == true){
		
		$xFRM->OMoneda2("renta_deposito", $xTabla->renta_deposito()->v(), "TR.RENTADEPOSITO");
		$xFRM->OMoneda2("renta_proporcional", $xTabla->renta_proporcional()->v(), "TR.PRIMERARENTA");
		
		$xFRM->OHidden("trenta_deposito", $xTabla->renta_deposito()->v());
		$xFRM->OHidden("trenta_proporcional", $xTabla->renta_proporcional()->v());
		
	} else {
		$xFRM->ODisabledM("trenta_deposito", $xTabla->renta_deposito()->v(), "TR.RENTADEPOSITO");
		$xFRM->OHidden("renta_deposito", $xTabla->renta_deposito()->v());
		$xFRM->ODisabledM("trenta_proporcional", $xTabla->renta_proporcional()->v(), "TR.PRIMERARENTA");
		$xFRM->OHidden("renta_proporcional", $xTabla->renta_proporcional()->v());
	}
	
} else {
	$xFRM->OHidden("monto_aliado", $xTabla->monto_aliado()->v());
	$xFRM->OHidden("describe_aliado", $xTabla->describe_aliado()->v());
	
	
	$xFRM->OHidden("monto_accesorios", $xTabla->monto_accesorios()->v());
	$xFRM->OHidden("monto_seguro", $xTabla->monto_seguro()->v());
	$xFRM->OHidden("monto_tenencia", $xTabla->monto_tenencia()->v());
	$xFRM->OHidden("monto_garantia", $xTabla->monto_garantia()->v());
	$xFRM->OHidden("monto_mtto", $xTabla->monto_mtto()->v());
	
	
	$xFRM->OHidden("monto_gps", $xTabla->monto_gps()->v());
	$xFRM->OHidden("monto_placas", $xTabla->monto_placas()->v());
	$xFRM->OHidden("monto_gestoria", $xTabla->monto_gestoria()->v());
	$xFRM->OHidden("monto_notario", $xTabla->monto_notario()->v());
	
	$xFRM->OHidden("renta_deposito", $xTabla->renta_deposito()->v());
	$xFRM->OHidden("renta_proporcional", $xTabla->renta_proporcional()->v());
}

if($xUser->getEsOriginador() == false){
	$xFRM->endSeccion();
}
//$xFRM->addHElem( );
//========================================= OPCIONES ====================================
if($xUser->getEsOriginador() == false){
	$xFRM->addSeccion("iddopts", "TR.OPCIONES");
}
$xFRM->OSiNo("TR.AUTOSEGURO FINANCIADO","financia_seguro", $xTabla->financia_seguro()->v(), false);
if($AunMasSimple == true){
	$xFRM->OHidden("financia_tenencia", "0");
} else {
	$xFRM->OSiNo("TR.TENENCIA FINANCIADO","financia_tenencia", $xTabla->financia_tenencia()->v());
}

if($xUser->getEsOriginador() == false){
	$xFRM->OSiNo("TR.DOMICILIA","domicilia", $xTabla->domicilia()->v());
} else {
	$xFRM->OHidden("domicilia", $xTabla->domicilia()->v());
}

//setLog($xTabla->residuales()->v());


$xFRM->OHidden("cuota_iva", $xTabla->cuota_iva()->v());

$xFRM->endSeccion();
$xFRM->addSeccion("iddescenas", "TR.ESCENARIOS");
if($OlvidarTodo == true){
	$xFRM->OHidden("idolvidar", "1");
	$xFRM->OHidden("administrado", SYS_UNO);
} else {
	$xFRM->OHidden("idolvidar", "0");
}

if($Nuevo == true){
	$xFRM->OHidden("idnuevo", "1");
} else {
	$xFRM->OHidden("idnuevo", "0");
}

$xFRM->OHidden("vecs", $xTabla->vecs()->v());
$xFRM->OHidden("tasas", $xTabla->tasas()->v());
$xFRM->OHidden("residuales", $xTabla->residuales()->v());

//================= No usar residuales



//validaciones de recalculo
$xFRM->setValidacion("precio_vehiculo_mny", "jsCargarPrecio", "El precio no debe ser menor al Anticipo", true);

if($OnEdit == false){
	$xFRM->addCRUD($xTabla->get(), false, "jsRegistroGuardado");
} else {
	if($xTabla->credito()->v() <= DEFAULT_SOCIO){
		$xFRM->addCRUDSave($xTabla->get(), $clave);
	}
}

$xEsc	= new cLeasing_escenarios();
$sql	= "SELECT * FROM `leasing_escenarios`";
$rs		= $xQL->getDataRecord($sql);
$tt		= "<table>";

//==== ================================================ Plazo
$tt		.= "<tr>";
$tt		.= "<th>" . $xFRM->getT("TR.PLAZO") . "</th>";
foreach ($rs as $rw){

	$xEsc->setData($rw);
	$idx	= $xEsc->plazo()->v();
	
	$xFRM->OHidden("residual_$idx", 0);
	$xFRM->OHidden("valorvec_$idx", 0);
	$xFRM->OHidden("monto_gps_$idx", 0);
	
	if($OlvidarTodo == false){
		$xFRM->OHidden("tasa_credito_$idx", $xLeas->getTasaInteres($idx));
		$xFRM->OHidden("tasavec_$idx", $xLeas->getTasaVec($idx));
	}
	$xChk		= new cHCheckBox();

	$xChk->addEvent("jsAddOmitidos($idx)", "onchange");

	$txtsi		= "";
	if($OnEdit == false){
		$xFRM->OHidden("idactive$idx", "false");
	} else {
		$txtsi	= $xChk->get("", "idactive$idx", true);
	}
	$tt		.= "<th>" . $xEsc->descripcion_escenario()->v() . "$txtsi</th>";
}
$tt		.= "</tr>";

//==== ================================================ Residual
$tt		.= "<tr>";

if($OlvidarTodo == true){
	//==================================================== Tasas de Credito
	$tt		.= "<tr>";
	$tt		.= "<th>" . $xFRM->getT("TR.TASA INTERES") . "</th>";
	foreach ($rs as $rw){
		$xTxt	= new cHText();
		$xTxt->setDivClass("");
		$xTxt->addEvent("jsActualizaResiduales", "onchange");
		
		$xEsc->setData($rw);$idx	= $xEsc->plazo()->v();
		$tt		.= "<td>" . $xTxt->getDeMoneda("tasa_credito_$idx", "", $xLeas->getTasaInteres($idx)) . "</td>";
	}
	$tt		.= "</tr>";
	
	//==================================================== Tasas de Vecs
	$tt		.= "<tr>";
	$tt		.= "<th>" . $xFRM->getT("TR.TASAVEC") . "</th>";
	foreach ($rs as $rw){
		$xTxt	= new cHText();
		$xTxt->setDivClass("");
		$xTxt->addEvent("jsActualizaResiduales", "onchange");
		
		$xEsc->setData($rw);$idx	= $xEsc->plazo()->v();
		$tt		.= "<td>" . $xTxt->getDeMoneda("tasavec_$idx", "", $xLeas->getTasaVec($idx)) . "</td>";
	}
	$tt		.= "</tr>";
}


$tt		.= "<th>" . $xFRM->getT("TR.TASARESIDUAL") . "</th>";
$jsVRes	= "";//Js de vlores residuales
$jsVTas	= "";//Js de Tasas
$jsVVecs	= "";//Js de Residuales

foreach ($rs as $rw){
	$xEsc->setData($rw);
	$idx	= $xEsc->plazo()->v();
	$xHS	= new cHSelect();
	$D		= $xQL->getDataRow("SELECT * FROM `leasing_residual` WHERE $idx >=`limite_inferior` AND $idx <= `limite_superior` ");
	$li		= setNoMenorQueCero($D["porciento_residual"]);
	$ls		= setNoMenorQueCero($D["porciento_final"]);

	if($OlvidarTodo == true){
		$xTxt	= new cHText();
		$xTxt->setDivClass("");
		$xTxt->addEvent("jsActualizaResiduales", "onchange");
		
		//setLog("674.- " . $xLeas->getTasaResidualPzo($idx));
		
		$tt		.= "<td>" . $xTxt->getDeMoneda("tasaresidual_$idx", "", $xLeas->getTasaResidualPzo($idx)) . "</td>";
		
		
	} else {
	
		if($li == $ls){
			//Agregar control de solo lectura
			$tr		= $xLeas->getTasaResidualPzo($idx);
			
			$tt		.= "<td class='tit' id='tasaresidual-$idx'>$tr</td>";
			$xFRM->OHidden("tasaresidual_$idx", $tr);
		} else {
		
			if($ls <= $li){ $ls	= $li + 20;	}
			$arrOpts= array();
			
			for($ii = $li; $ii <= $ls;){
				$arrOpts[$ii] = round($ii,2) . " %";
				$ii = $ii + 5;
			}
			$xHS->setDivClass("");
			$xHS->addOptions($arrOpts);
			$xHS->setDefault($xLeas->getTasaResidualPzo($idx));
			$xHS->addEvent("jsActualizaResiduales", "onchange");
			
			if($xUser->getEsOriginador() == true){
				if($NoUsarResidual == true){
					$xFRM->addDisabledInit("tasaresidual_$idx");
				}
			}
			
			$tt		.= "<th>" . $xHS->get("tasaresidual_$idx") . "</th>";
		
		}
	}
	//Suma JS
	$jsVRes	.= ($jsVRes == "") ? "'$idx-' + $(\"#tasaresidual_$idx\").val()" : " + ',$idx-' + $(\"#tasaresidual_$idx\").val()";
	//Suma Tasa Vecs
	$jsVVecs.= ($jsVVecs == "") ? "'$idx-' + $(\"#tasavec_$idx\").val()" : " + ',$idx-' + $(\"#tasavec_$idx\").val()";
	//Tasa Ints
	$jsVTas	.= ($jsVTas == "") ? "'$idx-' + $(\"#tasa_credito_$idx\").val()" : " + ',$idx-' + $(\"#tasa_credito_$idx\").val()";
}
$tt		.= "</tr>";


//==================================================== RENTA
$tt		.= "<tr>";
$tt		.= "<th>" . $xFRM->getT("TR.RENTA") . "</th>";
foreach ($rs as $rw){
	$xEsc->setData($rw);$idx	= $xEsc->plazo()->v();
	$tt		.= "<td class='mny' id='renta-$idx'></td>";
}
$tt		.= "</tr>";


//==================================================== Accesorios
$tt		.= "<tr>";
$tt		.= "<th>" . $xFRM->getT("TR.ACCESORIOS") . "</th>";
foreach ($rs as $rw){
	$xEsc->setData($rw);$idx	= $xEsc->plazo()->v();
	$tt		.= "<td class='mny' id='accesorios-$idx'></td>";
}
$tt		.= "</tr>";





//==================================================== Mantenimiento
$tt		.= "<tr>";
$tt		.= "<th>" . $xFRM->getT("TR.MTTO") . "</th>";
foreach ($rs as $rw){
	$xEsc->setData($rw);$idx	= $xEsc->plazo()->v();
	$tt		.= "<td class='mny' id='mtto-$idx'></td>";
}
$tt		.= "</tr>";

//==================================================== Garantia
$tt		.= "<tr>";
$tt		.= "<th>" . $xFRM->getT("TR.GARANTIA") . "</th>";
foreach ($rs as $rw){
	$xEsc->setData($rw);$idx	= $xEsc->plazo()->v();
	$tt		.= "<td class='mny' id='garantia-$idx'></td>";
}
$tt		.= "</tr>";

//==================================================== TENENCIA
$tt		.= "<tr>";
$tt		.= "<th>" . $xFRM->getT("TR.TENENCIA") . "</th>";
foreach ($rs as $rw){
	$xEsc->setData($rw);$idx	= $xEsc->plazo()->v();
	$tt		.= "<td class='mny' id='tenencia-$idx'></td>";
}
$tt		.= "</tr>";

//==================================================== Seguro
$tt		.= "<tr>";
$tt		.= "<th>" . $xFRM->getT("TR.AUTOSEGURO") . "</th>";
foreach ($rs as $rw){
	$xEsc->setData($rw);$idx	= $xEsc->plazo()->v();
	$tt		.= "<td class='mny' id='seguro-$idx'></td>";
}
$tt		.= "</tr>";


//==================================================== Total Pago Mensual
$tt		.= "<tr class='trOdd'>";
$tt		.= "<th>" . $xFRM->getT("TR.TOTAL PAGO MENSUAL") . "</th>";
foreach ($rs as $rw){
	$xEsc->setData($rw);$idx	= $xEsc->plazo()->v();
	$tt		.= "<td class='mny total' id='total-cuota-$idx'></td>";
}
$tt		.= "</tr>";

//==================================================== Anticipo
$tt		.= "<tr class='tr-pagar'>";
$tt		.= "<th>" . $xFRM->getT("TR.ANTICIPO") . "</th>";
foreach ($rs as $rw){
	$xEsc->setData($rw); $idx	= $xEsc->plazo()->v();
	$tt		.= "<td class='mny' id='anticipo-$idx'></td>";
}
$tt		.= "</tr>";

//==================================================== Comision
$tt		.= "<tr>";
$tt		.= "<th>" . $xFRM->getT("TR.COMISION") . "</th>";
foreach ($rs as $rw){
	$xEsc->setData($rw);$idx	= $xEsc->plazo()->v();
	$tt		.= "<td class='mny' id='comision-$idx'></td>";
}
$tt		.= "</tr>";



//==================================================== Valor Residual
if($EsOriginador == false){
	$tt		.= "<tr class='tr-plan'>";
	$tt		.= "<th>" . $xFRM->getT("TR.VALORRESIDUAL") . "</th>";
	foreach ($rs as $rw){
		$xEsc->setData($rw);
		$idx	= $xEsc->plazo()->v();
		$tt		.= "<td class='mny' id='residual-$idx'></td>";
	}
	$tt		.= "</tr>";
}

$tt		.= "<tr class='tr-plan'>";
$tt		.= "<th>" . $xFRM->getT("TR.VEC") . "</th>";
foreach ($rs as $rw){
	$xEsc->setData($rw);
	$idx	= $xEsc->plazo()->v();
	$tt		.= "<td class='mny' id='valorvec-$idx'></td>";
}
$tt		.= "</tr>";

//==================================================== Planes GPS
$tt		.= "<tr>";
$tt		.= "<th>" . $xFRM->getT("TR.PAQUETESGPS") . "</th>";
foreach ($rs as $rw){
	$xEsc->setData($rw);$idx	= $xEsc->plazo()->v();
	$tt		.= "<td class='mny' id='monto_gps-$idx'></td>";
}
 $tt		.= "</tr>";
 
 
//==================================================== Tasas de Interes
if($EsOriginador == false){
	$tt		.= "<tr>";
	$tt		.= "<th>" . $xFRM->getT("TR.TASA_ANUALIZADA") . "</th>";
	foreach ($rs as $rw){
	 	$xEsc->setData($rw);$idx	= $xEsc->plazo()->v();
	 	$tt		.= "<td class='mny' id='tasa_credito-$idx'></td>";
	}
	$tt		.= "</tr>";

//==================================================== Tasas de Vec

	$tt		.= "<tr>";
	$tt		.= "<th>" . $xFRM->getT("TR.TASAVEC") . "</th>";
	foreach ($rs as $rw){
		$xEsc->setData($rw);$idx	= $xEsc->plazo()->v();
		$tvc	= $xLeas->getTasaVec($idx);
		
		$tt		.= "<td class='mny' id='tasavec-$idx'>" . $tvc . "</td>";
	}
	$tt		.= "</tr>";

}
//==================================================== Aliado
/*$tt		.= "<tr>";
 $tt		.= "<th>" . $xFRM->getT("TR.EQUIPOALIADO") . "</th>";
 foreach ($rs as $rw){
 $xEsc->setData($rw);$idx	= $xEsc->plazo()->v();
 $tt		.= "<td class='mny' id='aliado-$idx'></td>";
 }
 $tt		.= "</tr>";*/






$tt		.= "</table>";

$xFRM->addHElem($tt);

$xFRM->endSeccion();

//$xFRM->OMoneda("originador", $xTabla->originador()->v(), "TR.ORIGINADOR");
//$xFRM->OMoneda("suboriginador", $xTabla->suboriginador()->v(), "TR.SUBORIGINADOR"); //getOriginador();
$xFRM->addAviso("", "idavisos");
//$xFRM->addHElem('');
//==============================================================   Determinar Paso
$paso		= $xTabla->paso_proceso()->v();
if($clave > 0){
	//Iniciar Calculo
	$xFRM->addJsInit("jsInitEdit();");
	if($xTabla->persona()->v() > DEFAULT_SOCIO){
		if($xTabla->credito()->v() > DEFAULT_CREDITO){
			$xCred	= new cCredito($xTabla->credito()->v());
			if($xCred->init() == true){
				$paso 	= $xCred->getEstadoActual();
			} else {
				$paso 	= $xCProc->PASO_CON_CREDITO;
			}
		} else {
			$paso	= $xCProc->PASO_CON_PERSONA;
		}
	} else {
		if($xTabla->oficial()->v() > 0 AND ($xTabla->oficial()->v() != $xTabla->suboriginador()->v()) ){
			$paso	= $xCProc->PASO_CON_OFICIAL;
		} else {
			$paso	= $xCProc->PASO_ATENDIDO;
		}
	}

	//Editar Bonos
	$xFRM->OButton("TR.BONOS", "jsEditarBonos()", $xFRM->ic()->DINERO);
	
	//Generar Bonos Automaticamente.
	
	
	//Actualizar
	$xLeas->setPaso($paso);
	
	
	//$xFRM->addCerrar();
}

$xFRM->OHidden("idomitidos1", "");
$xFRM->OHidden("idomitidos2", "");
$xFRM->OHidden("idid", $clave);

if($Nuevo == true){
	$xFRM->addJsInit("setTimeout(jsActualizaResiduales,1000);");
}
if($OnEdit == false){
	$xFRM->addJsInit("jsInitNew();");
	
} else {
	$itemsNoVer	.= ",btn_guardar";
}

echo $xFRM->get();
$jsOnEdit		= ($clave>0) ? "true": "false";

?>
<script>
var xG					= new Gen();
var xC					= new CredGen();
var xP					= new PersGen();
var vTipoOrigen			= Configuracion.credito.origen.arrendamiento;
var onEdit				= <?php echo $jsOnEdit; ?>;
var mkPzo				= 0;

var vTasaIVA			= <?php echo TASA_IVA; ?>;
var vFactorIVA			= 1 / (1+vTasaIVA);
var mFactorRentaDep		= 1;
var mFactorRentaProp	= 1;
var vCalcComXTodo		= true;
var mSumarIVA			= true;
var mFactorMinAnticipo	= 0.2;
var mFactorSeguro		= 0.03;
var mMontoMinGtosNot	= 400;
var mOlvidar			= <?php echo ($OlvidarTodo == true) ? "true" : "false"; ?>;
var vReloadID			= "v1.reload.this.form";
var mAdministrado		= <?php echo ($EsAdministrado == true) ? "true" : "false"; ?>;

var vEscenarios			= [12,24,36,48,60];

<?php

echo $jsFormulaPrec;

?>

window.onfocus 			= function() {
	if(session(vReloadID) !== null){
		if(session(vReloadID) == "1"){
			location.reload();
			session(vReloadID, "0"); 
		}
	}
};
function jsInitEdit(){
	jsHidenItems();
	setTimeout(jsCalcularEscenarios,500);
}
function jsInitNew(){
	jsHidenItems();
	xG.verDiv("iddescenas");
}
function jsHidenItems(){
	var mNoVerItems	= String("<?php echo $itemsNoVer; ?>").split(",");
	var arrayLength = mNoVerItems.length;
	for (var i = 0; i < arrayLength; i++) {
		var idx	= mNoVerItems[i];
		xG.ver(idx);
	}
}
function jsCargarPrecio(v){
	$("#precio_vehiculo").val( flotante( $("#precio_vehiculo_mny").val() ) );

	jsActualizaResiduales();
	jsaGetTasa();
	jsaGetCostoGPS();
	jsGetCostos();
	jsaGetResidual();

	jsCalcularEscenarios();
	
	v	= flotante(v);
	$("#monto_anticipo").val( flotante( $("#monto_anticipo_mny").val() ) );
	var ant = $("#monto_anticipo").val(); 
	if(v <= ant){
		return false;
	}
	return validacion.nozero(v);
}
function jsCalculaFinanciamiento(){
	//Constituir Residuales.
	jsActualizaResiduales();
	jsaGetTasa();
	jsaGetCostoGPS();
	jsGetCostos();
	jsaGetResidual();
	
	var tt		= 0;
	var financia_seguro		= ($("#financia_seguro").val() == 1) ? true : false;
	var financia_tenencia	= ($("#financia_tenencia").val() == 1) ? true : false;
	var domicilia			= ($("#domicilia").val() == 1) ? true : false;

	var comision_apertura	= $("#comision_apertura").val();
	var comision_originador	= $("#comision_originador").val();
	
	var tasa_iva			= $("#tasa_iva").val();
	//financiado
	var precio				= $("#precio_vehiculo").val();
	var anticipo			= $("#monto_anticipo").val();

	var accesorios			= $("#monto_accesorios").val();
	var aliado				= $("#monto_aliado").val();
	var garantia			= $("#monto_garantia").val();
	var mtto				= $("#monto_mtto").val();
	var monto_gps			= $("#monto_gps").val();
	//Opcional
	var seguro				= $("#monto_seguro").val();
	var tenencia			= $("#monto_tenencia").val();
	//Directo
	var notario				= $("#monto_notario").val();
	var placas				= $("#monto_placas").val();
	var gestoria			= $("#monto_gestoria").val();
	
	
	var financiado			= 0;
	var directo				= 0;
	//PRECIO ALIADO VAN CON iva
	setLog("Anticipo : " + anticipo);
	financiado				= (getVRaw(precio)+getVRaw(monto_gps)+getVRaw(aliado))-anticipo;
	//No van en Cuota Financiada
	//Seguro	Tenencia	Accesorios y otros gastos	Garantía Extendida	N°
	//+getVRaw(accesorios)+getVRaw(garantia);
	directo					= getVRaw(notario)+getVRaw(placas)+getVRaw(gestoria);
	if(financia_seguro== true){
		//financiado	= financiado + getVRaw(seguro); 
	} else {
		directo		= directo + getVRaw(seguro);
	}
	if(financia_tenencia== true){
		//financiado	= financiado + getVRaw(tenencia); 
	} else {
		directo		= directo + getVRaw(tenencia);
	}

	financiado		= redondear(financiado,2);
	directo			= redondear(directo,2);
		
	$("#monto_directo").val(directo);
	$("#total_credito").val(financiado);

	var monto_comision		= redondear((financiado * (flotante(comision_apertura)/100)));
	var monto_originador	= redondear((financiado * (flotante(comision_originador)/100)));

	if(vCalcComXTodo == true){
		var vCoste			= getVRaw($("#precio_vehiculo").val());
		var monto_comision	= redondear(( vCoste * (flotante(comision_apertura)/100)));
		
	}
	
	$("#monto_comision").val(monto_comision);
	$("#monto_originador").val(monto_originador);
		
	setLog("Financiado : " + financiado +" ---- Directo : " + directo + "  ---- Comision Apertura : " + monto_comision + " ---- Monto originador :" + monto_originador);
	//calcular escenarios
	
	return true;
}

function jsCalcularEscenarios(){
	// Calcular Minimos
	var precio				= $("#precio_vehiculo").val();
	var anticipo			= (onEdit == true) ? $("#monto_anticipo").val() : 0;
	var minanticipo			= (precio*vFactorIVA) * mFactorMinAnticipo;
	minanticipo				= redondear(minanticipo,2);
	var idcotiza			= $("#idoriginacion_leasing").val();

	if(mOlvidar == false && mAdministrado == false){
		
		if(minanticipo > anticipo){
			$("#monto_anticipo_mny").val( getFMoney(minanticipo) );
			$("#monto_anticipo").val( minanticipo );
		}

	
		//jsCargarCostos
		jsGetCostos();
		//Calcular minimos de costos	
		var notario				= (onEdit == true) ? $("#monto_notario").val() : 0;
		if(mMontoMinGtosNot > notario){
			$("#monto_notario_mny").val( getFMoney(mMontoMinGtosNot) );
			$("#monto_notario").val( mMontoMinGtosNot );
		}
		var seguro				= (onEdit == true) ? $("#monto_seguro").val() : 0;
		var minseguro			= (precio*vFactorIVA) * mFactorSeguro;
		minseguro				= redondear(minseguro,2);
		if(minseguro > seguro){
			$("#monto_seguro_mny").val( getFMoney(minseguro) );
			$("#monto_seguro").val( minseguro );		
		}
	}
	//Todos a moneda
	xG.aMonedaForm();

	if(onEdit == true){
		xG.spinInit();
		jsCalculaFinanciamiento();
		
		if( ($("#frmcotizacion").attr("data-mod") == "true") && idcotiza > 0){
			xG.save({form:'frmcotizacion',tabla:'originacion_leasing', id : idcotiza, nomsg: true});
		}
		setTimeout(jsCalcularEscenariosII,2000);
	}
}

function jsCalcularEscenariosII(){
	var idcotiza	= $("#idoriginacion_leasing").val();
	var idtr		= flotante($("#tasa_credito_12").val());
	if(idtr <=0){
		xG.alerta({msg:"No hay tasa para los Plazos " + idtr});

		//jsCalcularEscenarios();
		
	} else {	
		var acnt 			= vEscenarios.length;
		for (var i_ = 0; i_ < acnt; i_++) {
			var p_			= vEscenarios[i_];
			//console.log( p_);
			if($("#tasa_credito_" + p_).length > 0){
				//==================== Asignar Costos GPS al Valor
				$("#monto_gps-" + p_).html( getFMoney($("#monto_gps_" + p_).val()) );
				//==================== Mostrar Tasas de Interes
				$("#tasa_credito-" + p_).html( getFTasa($("#tasa_credito_" + p_).val()) );
				//==================== Mostrar Tasa VEC
				$("#tasavec-" + p_).html( getFTasa($("#tasavec_" + p_).val()) );
				//==================== Mostrar Tasa RES
				$("#tasaresidual-" + p_).html( getFTasa($("#tasaresidual_" + p_).val()) );
				jsCalcular(p_);
			}
		}
		//Actualizar el Programa
		if(entero(idcotiza)>0){
			xG.save({form:'frmcotizacion',tabla:'originacion_leasing', id:idcotiza, nomsg: true});
			jsaActualizarCredito();
		}

		xG.spinEnd();
	}
}
function xLog(msg){
	var idx	= entero( $("#plazo").val() );
	if(mkPzo == idx){
		setLog(msg);
		console.log(msg);
	}
}
function jsCalcular(idx){
	mkPzo					= idx;
	
	var financia_seguro		= ($("#financia_seguro").val() == 1) ? true : false;
	var financia_tenencia	= ($("#financia_tenencia").val() == 1) ? true : false;
	var domicilia			= ($("#domicilia").val() == 1) ? true : false;
	
	var idsiniva			= false;
	var idconredondeo		= false;
	var idsolo				= false;
	var anticipo			= $("#monto_anticipo").val();
	var idmonto				= redondear($("#total_credito").val(),2);
	var idpagos				= idx;
	var idtasa				= flotante($("#tasa_credito_"+idx).val());
	var tasatiie			= flotante($("#tasa_tiie").val());
	idtasa					= (idtasa+tasatiie);
	//setLog("Tasa de plazo " + idx +  " a " + idtasa + "%");
	var residual			= $("#residual_" + idx).val();
	var valorvec			= $("#valorvec_" + idx).val();
	
	var idiva				= $("#tasa_iva").val();
	var idfrecuencia		= 30;
	
	var precio				= getVRaw($("#precio_vehiculo").val());
	
	
	var accesorios			= getVRaw($("#monto_accesorios").val());
	var aliado				= getVRaw($("#monto_aliado").val());
	var garantia			= getVRaw($("#monto_garantia").val());
	var mtto				= getVRaw($("#monto_mtto").val());
	//Coste
	var vAnticipo			= flotante($("#monto_anticipo").val());
	var vCoste				= (precio+aliado) - vAnticipo;
	
	xLog(idx + " . - Precio Vehiculo : " + precio);
	xLog(idx + " . - Monto Aliado : " + aliado);
	xLog(idx + " . - Anticipo : " + vAnticipo);
	
	//Opcional
	var seguro				= getVRaw($("#monto_seguro").val());
	var tenencia			= getVRaw($("#monto_tenencia").val());
	//Directo
	var notario				= getVRaw($("#monto_notario").val());
	var placas				= getVRaw($("#monto_placas").val());
	var gestoria			= getVRaw($("#monto_gestoria").val());
	var idplazo				= $("#plazo").val();
	//Monto GPS
	var montogps			= getVRaw($("#monto_gps_"+idx).val());
	if(montogps <= 0){
		montogps			= getVRaw($("#monto_gps").val());
	}

	var monto_comision		= $("#monto_comision").val();
	var monto_originador	= $("#monto_originador").val();

	var vPzoElegido			= $("#plazo").val();
	
	var cc1					= 0;
	var cc2					= 0;
	var cc3					= 0;
	var cc4					= 0;
	var cc5					= 0;
	var cc6					= 0;
	var cc7					= 0;
	var cc8					= 0;
	
	//Anticipo
	$("#anticipo-" + idx).html(getFMoney(anticipo));
	//renta Calculada
	xLog(idx + " . - Coste : " + vCoste);
	xLog(idx + " . - Valor Residual : " + residual);
	xLog(idx + " . - Valor VEC : " + valorvec);
	xLog(idx + " . - Tasa de Interes : " + idtasa);
	
	var cc1				= xC.getCuotaDePago({capital:vCoste, tasa: idtasa, residual: residual, frecuencia: idfrecuencia, pagos: idpagos, iva: 0});
	xLog(idx + " . - Cuota Vehiculo : " + cc1);
	//Equipo Aliado, base de Original
	//var cc6				= xC.getCuotaDePago({capital:aliado, tasa: idtasa, residual:0, frecuencia: idfrecuencia, pagos: idpagos, iva: 0});
	//Equipo GPS a la renta Principal
	var cc7				= xC.getCuotaDePago({capital:montogps, tasa: idtasa, residual:0, frecuencia: idfrecuencia, pagos: idpagos, iva: 0});
	xLog(idx + " . - Cuota GPS : " + cc7);
	//setLog(cc7);
	var trenta			= cc1+cc7;
	xLog(idx + " . - Cuota Renta : " + trenta);
	
	//Calcular Renta
	$("#renta-" + idx).html(getFMoney(trenta));
	if(idx == vPzoElegido){
		var rd	= getVConIVA(trenta) * mFactorRentaDep;
		var rp	= trenta * mFactorRentaProp;
		var rda	= flotante($("#renta_deposito").val());
		var rpa	= flotante($("#renta_proporcional").val());

		if(mOlvidar == true || mAdministrado == true){
			if(rd < rda){
				$("#trenta_deposito").val(getFMoney(rd));
				$("#renta_deposito").val(rd);
			}
			if(rp < rpa){
				$("#trenta_proporcional").val(getFMoney(rp));
				$("#renta_proporcional").val(rp);
			}
		} else {
			$("#trenta_deposito").val(getFMoney(rd));
			$("#renta_deposito").val(rd);
			$("#trenta_proporcional").val(getFMoney(rp));
			$("#renta_proporcional").val(rp);
		}

		//=========== IVA de Renta
		if(mSumarIVA == true){
			var mCuotaIva	= redondear((trenta * vTasaIVA),2);
			$("#cuota_iva").val(mCuotaIva);
			xLog(idx + " . - Cuota IVA : " + mCuotaIva);
		}
	}
	//Seguro
	if(financia_seguro== true){
		var cc3				= xC.getCuotaDePago({capital:seguro, tasa: idtasa, residual:0, frecuencia: idfrecuencia, pagos: idpagos, iva:0});
		$("#seguro-" + idx).html(getFMoney(cc3));
		//Guardar la Cuota por Seguro
		xLog(idx + " . - Cuota Seguro : " + cc3);
	} else {
		$("#seguro-" + idx).html(getFMoney(0));
	}
	//tenencia
	if(financia_tenencia== true){
		var cc2				= xC.getCuotaDePago({capital:tenencia, tasa: idtasa, residual:0, frecuencia: idfrecuencia, pagos: idpagos, iva: 0});
		xLog(idx + " . - Cuota Tenencia : " + cc2);
		$("#tenencia-" + idx).html(getFMoney(cc2));
	} else {
		$("#tenencia-" + idx).html(getFMoney(0));
	}
	//Mantenimiento
	if(mtto > 0){
		var cc4				= xC.getCuotaDePago({capital:mtto, tasa: idtasa, residual:0, frecuencia: idfrecuencia, pagos: idpagos, iva: 0});
		xLog(idx + " . - Cuota Mtto : " + cc4);
		$("#mtto-" + idx).html(getFMoney(cc4));		
	}
	//Accesorios
	if(accesorios >0){
		var cc5				= xC.getCuotaDePago({capital:accesorios, tasa: idtasa, residual:0, frecuencia: idfrecuencia, pagos: idpagos, iva: 0});
		xLog(idx + " . - Cuota Mtto : " + cc5);
		$("#accesorios-" + idx).html(getFMoney(cc5));
	}
	//Equipo Aliado
	//Equipo aliado en la renta
	/*if(aliado >0){
		var cc6				= xC.getCuotaDePago({capital:aliado, tasa: idtasa, residual:0, frecuencia: idfrecuencia, pagos: idpagos, iva: 0});
		$("#aliado-" + idx).html(getFMoney(cc6));
	}*/
	//Planes GPS
	/*if(montogps > 0){
		var cc7				= xC.getCuotaDePago({capital:montogps, tasa: idtasa, residual:0, frecuencia: idfrecuencia, pagos: idpagos, iva: 0});
		$("#monto_gps-" + idx).html(getFMoney(cc7));		
	}*/
	//Valor de la Garantia
	if(garantia > 0){
		var cc8				= xC.getCuotaDePago({capital:garantia, tasa: idtasa, residual:0, frecuencia: idfrecuencia, pagos: idpagos, iva: 0});
		xLog(idx + " . - Cuota Garantia : " + cc8);
		$("#garantia-" + idx).html(getFMoney(cc8));
	}	
	$("#total-cuota-" + idx).html(getFMoney(cc1+cc2+cc3+cc4+cc5+cc6+cc7+cc8));
	
	if(idx == idplazo){
		$("#cuota_vehiculo").val(cc1);
		$("#cuota_tenencia").val(cc2);
		$("#cuota_seguro").val(cc3);
		$("#cuota_mtto").val(cc4);
		$("#cuota_accesorios").val(cc5);
		$("#cuota_aliado").val(cc6);
		$("#cuota_gps").val(cc7);
		$("#cuota_garantia").val(cc8);
	}
	
	$("#comision-" + idx).html(getFMoney(monto_comision));
	$("#residual-" + idx).html(getFMoney(residual));
	$("#valorvec-" + idx).html(getFMoney(valorvec));

	return true;
}
function getVConIVA(monto){
	var FIVA	= (1 + vTasaIVA);
	monto		= monto * FIVA;
	return redondear(monto,2);
}
function getVRaw(monto){
	var FIVA	= 1/(1 + vTasaIVA);
	monto		= monto * FIVA;
	return redondear(monto,2);
}
function jsGetParametros(){}
function jsDatosIniciales(){
	jsaGetComision();
	//Cargar Sub originadores
	xG.DataList({id: "suboriginador", tabla: "leasing_usuarios", buscar: $("#originador").val(), buscado: "originador"});
}
function jsGoAsociar(){
	jsaAsociar();
	xG.spin({time:2000, callback:onRefresh});
}
function onRefresh(){
	window.location.reload();
}
function jsAgregarCredito(){
	var idpersona			= $("#persona").val();
	var idcredito			= $("#credito").val();
	if(idpersona > DEFAULT_SOCIO){
		var producto		= Configuracion.credito.productos.arrendamientopuro;//$("#producto").val();
		var periocidad		= 30;//$("#periocidad").val(); Provisional
		var pagos			= $("#plazo").val();
		var monto			= $("#total_credito").val();
		var aplicacion		= Configuracion.credito.destinos.arrendamientopuro;//$("#aplicacion").val();
		var notas			= $("#notas").val();
		
		var idcontrol		= $("#idoriginacion_leasing").val();
		var idpersona		= $("#persona").val();
		var idcredito		= $("#credito").val();
		var idoficial		= $("#oficial").val();
		var tasa_tiie		= $("#tasa_tiie").val();
		var tasa_credito	= $("#tasa_credito").val();
		var tasa			= tasa_tiie+tasa_credito;
		//origen 270 PRECLIENTES
		if(idcredito <= DEFAULT_CREDITO){
			xC.addCredito({persona: idpersona, monto: monto, producto:producto, origen:vTipoOrigen, idorigen:idcontrol, frecuencia: periocidad, pagos: pagos, destino:aplicacion, oficial:idoficial, tasa:tasa});
			session(vReloadID, "1");
		} else {
			xG.alerta({msg: "El Credito ya existe"});
		}		
	} else {
		xG.alerta({msg: "Debe Vincular o Agregar una Persona"});
	}
}
function jsAgregarPersona(){
	xG.confirmar({ msg:"¿ PERSONA_YA_EXISTE ?", callback: jsVincularPersona, cancelar: jsAgregarPersonaNueva});
}
function jsAgregarPersonaNueva(){
	var idpersona	= $("#persona").val();
	var idcredito	= $("#credito").val();
	var es_moral	= $("#es_moral").val();
	var tel			= $("#tel").val();
	var mail		= $("#mail").val();
	var idcontrol	= $("#idoriginacion_leasing").val();
	if(idpersona > DEFAULT_SOCIO){
		
	} else {
		//var fecha_de_registro	= $("#fecha_de_registro").val();
		var nombres				= $("#nombre_cliente").val();
		//var telefono			= $("#telefono").val();
		//var email				= $("#email").val();
		if(es_moral == 1){
			xP.goToAgregarMorales({nombre:nombres,tipoorigen:vTipoOrigen,claveorigen:idcontrol,telefono:tel,email:mail});
		} else {
			xP.goToAgregarFisicas({nombrecompleto:nombres,tipoorigen:vTipoOrigen,claveorigen:idcontrol,telefono:tel,email:mail});
		}
	}
}
function jsVincularPersona(){
	xP.getFormaBusqueda({control: "persona", callback: jsGoAsociar});
}
function jsVerPersona(){
	var idpersona	= $("#persona").val();
	var idcredito	= $("#credito").val();
	xP.goToPanel(idpersona);
}
function jsVerCredito(){
	var idpersona	= $("#persona").val();
	var idcredito	= $("#credito").val();
	xC.goToPanelControl(idcredito);
}
function jsAgregarDatosVehiculo(){
	var idcredito	= $("#credito").val();
	var idcontrol	= $("#idoriginacion_leasing").val();
	xG.w({url:"../frmarrendamiento/leasing-activos.frm.php?idleasing=" + idcontrol, tab:true});
}
function jsDesactivarCotizacion(){
	xG.soloLeerForma(false, false);
}
function jsActualizaResiduales(vDesde){
	
	vDesde	= (typeof vDesde == "undefined") ? 0 : vDesde;
	
	var pzo	= $("#plazo").val()

	var xx 	= <?php  echo $jsVRes; ?>;
	var yy	= <?php  echo $jsVTas; ?>;
	var ww	= <?php  echo $jsVVecs; ?>;

	if(vDesde > 0){ //Se origina desde el Control de tasa_compra
		$("#tasavec_" + pzo).val(vDesde);
		//Condicionar la regla de que el VEC no puede ser mayor al Residual
		var ttv	= redondear($("#tasavec_" + pzo).val(),2);
		var ttr	= redondear($("#tasaresidual_" + pzo).val(),2);
		if(ttv < ttr){
			$("#tasavec_" + pzo).val(ttr);
			xG.alerta({msg:"La TASAVEC no puede ser menor a la Tasa Residual", type: "warn"});
		}
	} else {
		var acnt 		= vEscenarios.length;
		for (var i_ = 0; i_ < acnt; i_++) {
			var p_		= vEscenarios[i_];
			var ttv		= redondear($("#tasavec_" + p_).val(),2);
			var ttr		= redondear($("#tasaresidual_" + p_).val(),2);
			if(ttv < ttr){
				$("#tasavec_" + p_).val(ttr);
				xG.alerta({msg:"La TASAVEC no puede ser menor a la Tasa Residual en el Plazo " + p_, type: "warn"});
			}
		}		
		$("#tasa_compra").val( $("#tasavec_" + pzo).val() );
		$("#tasa_credito").val( $("#tasa_credito_" + pzo).val() );
		
		//console.log("SIN el control");
	}
	
	
	//Actualizar
	
	$("#vecs").val(ww);
	$("#tasas").val(yy);
	$("#residuales").val(xx);
	return true;
}

function jsGetPlanCliente(){
	var idcredito	= $("#credito").val();
	xG.w({url:"../frmarrendamiento/leasing-plan_cliente.frm.php?credito=" + idcredito, tab:true});
}
function jsAgregarDocumentos(){
	var idcontrol	= $("#idoriginacion_leasing").val();
	xG.addDocuments(vTipoOrigen, idcontrol);
}
function jsGetCostos(){
	var notario				= flotante($("#monto_notario").val());
	var placas				= flotante($("#monto_placas").val());
	var gestoria			= flotante($("#monto_gestoria").val());

	var sumacostos			= flotante((notario + placas + gestoria),2);
	
	if(sumacostos <=0){
		jsaGetCostos();
		//setLog("se calculan costos");
	}

}
function jsRegistroGuardado(d){
	var xid	= entero(d.id);
	if(xid > 0){
		xG.go({url: "../frmarrendamiento/cotizador.edit.frm.php?nuevo=true&clave=" + xid});
	}
}
function jsEditarBonos(){
	var idcontrol	= $("#idoriginacion_leasing").val();
	xG.w({url: "../frmarrendamiento/leasing-bonos.frm.php?clave=" + idcontrol, tab: true});
}
function jsAddOmitidos(id){

	var mid 	= "#idactive" + id;
	
	$("#idomitidos1").val(0);
	$("#idomitidos2").val(0);
	//console.log($(mid).prop('checked'));
	
	if($(mid).prop('checked') == false){
		//console.log("omitir " + id);
		$("#idomitidos1").val(id);
	} else {
		$("#idomitidos2").val(id);
	}
	jsaAgregarOmitidos();
}
</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>