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

//$xDic		= new cHDicccionarioDeTablas();
$jxc 			= new TinyAjax();
$xUser			= new cSystemUser(getUsuarioActual()); $xUser->init();
$xRuls			= new cReglaDeNegocio();
$NoUsarTIIE		= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_ARREND_SIN_TIIE);
$NoUsarResidual	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_ARREND_COT_NORES);

$originador		= 0;
$suborigen		= 0;
$oficial		= 0;
$EsAdmin		= false;
$OnEdit			= false;
$TasaComision	= 0;
$EsOriginador	= false;

//$EsActivo	= false;
if($xUser->getEsOriginador() == true){
	$xOrg	= new cLeasingUsuarios();
	if($xOrg->initByIDUsuario($xUser->getID()) == true){
		$originador	= $xOrg->getOriginador();
		$suborigen	= $xOrg->getSubOriginador();
		if($xOrg->getEsAdmin() == true){
			$EsAdmin			= true;
		}
		if($xOrg->getEsActivo() == false){
			$xHP->goToPageError(403);
		}
		$xOrg	= new cLeasingOriginadores($originador);
		if($xOrg->init() == true){
			$TasaComision	= $xOrg->getTasaComision();
		}
	}
	$EsOriginador	= true;
} else {
	$oficial	= $xUser->getID();
}
function jsaGetTasa($rac, $plazo){
	$xQL	= new MQL();
	//"#tasa_credito"
	$tab 	= new TinyAjaxBehavior();

	$xTLeas	= new cLeasingTasas();
	if($xTLeas->initByPlazoRAC($plazo, $rac) == true){
		$tab->add(TabSetValue::getBehavior("tasa_credito", $xTLeas->getTasa() ));
		$tab->add(TabSetValue::getBehavior("comision_apertura", $xTLeas->getComisionApertura() ));
		
		//$tab->add(TabSetValue::getBehavior("tasa_credito", $xTLeas->getTasa() ));
		$tab->add(TabSetValue::getBehavior("comision_apertura_mny", $xTLeas->getComisionApertura() ));
		
	}
	$xEsc		= new cLeasing_escenarios();
	$rs			= $xQL->getDataRecord("SELECT * FROM `leasing_escenarios`");
	foreach ($rs as $rw){
		$xEsc->setData($rw);
		$idx	= $xEsc->plazo()->v();
		$xTLeas	= new cLeasingTasas();
		if($xTLeas->initByPlazoRAC($idx, $rac) == true){
			//setLog($idx);
			$tab->add(TabSetValue::getBehavior("tasa_credito_$idx", $xTLeas->getTasa() ));
		}
	}
	return $tab->getString();
}
function jsaGetComision($originador){
	$xOrg	= new cLeasingOriginadores($originador);
	$tasa	= 0;
	if($xOrg->init()){
		$tasa = $xOrg->getTasaComision();
	}
	return $tasa;
}
function jsaGetCostoGPS($plazo, $plan){
	
	$xQL	= new MQL();
	//"#tasa_credito"
	$tab = new TinyAjaxBehavior();
	
	$xGPS	= new cLeasingGPSCosteo();
	if($xGPS->initByPlazoTipo($plazo, $plan) == true){
		$tab->add(TabSetValue::getBehavior("monto_gps", $xGPS->getMonto() ));
	}
	$xEsc		= new cLeasing_escenarios();
	$rs			= $xQL->getDataRecord("SELECT * FROM `leasing_escenarios`");
	foreach ($rs as $rw){
		$xEsc->setData($rw);
		$idx	= $xEsc->plazo()->v();
		$xGPS	= new cLeasingGPSCosteo();
		if($xGPS->initByPlazoTipo($idx, $plan) == true){
			$tab->add(TabSetValue::getBehavior("monto_gps_$idx", $xGPS->getMonto() ));
			
		}
	}
	return $tab->getString();
}
function jsaGetCostos($entidad, $precio){
	$sql	= "SELECT * FROM `vehiculos_tenencia` WHERE `entidadfederativa`=$entidad LIMIT 0,1";
	$xQL	= new MQL();
	$xT		= new cVehiculos_tenencia();
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
function jsaGetResidual($precio, $aliado, $plazo, $residuales, $anticipo){
	$xEmul		= new cLeasingEmulaciones($plazo, 0 ,0);
	$xEsc		= new cLeasing_escenarios();
	$tab 		= new TinyAjaxBehavior();
	$xQL		= new MQL();
	$tasares	= 0;
	$DRes		= explode(",", $residuales, 10);
	$NRes		= count($DRes);
	
	
	if($NRes <= 0){
		$rs			= $xQL->getDataRecord("SELECT * FROM `leasing_escenarios`");
		foreach ($rs as $rw){
			$xEsc->setData($rw);
			$idx	= $xEsc->plazo()->v();
			$res 	= $xEmul->getValorResidual($precio, $aliado, $idx, false, $anticipo);
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
$jxc ->exportFunction('jsaGetTasa', array('tipo_rac', 'plazo'));
$jxc ->exportFunction('jsaGetComision', array('originador'), "#comision_originador");
$jxc ->exportFunction('jsaGetCostoGPS', array('plazo', 'tipo_gps'));
$jxc ->exportFunction('jsaGetCostos', array('entidadfederativa', 'precio_vehiculo'));
$jxc ->exportFunction('jsaGetResidual', array('precio_vehiculo','monto_aliado', 'plazo', 'residuales', 'monto_anticipo'));
$jxc ->exportFunction('jsaAsociar', array('persona', 'idoriginacion_leasing'), "#idavisos");

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
//$xTxt		= new cHText();

$xFRM->setTitle($xHP->getTitle());
$xFRM->OButton("TR.CALCULAR", "jsCalcularEscenarios()", $xFRM->ic()->CALCULAR);

$xFRM->setNoAcordion();// $xFRM->setIsWizard();

$xTabla		= new cOriginacion_leasing();
$xLeas		= new cCreditosLeasing();
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
			$xFRM->OButton("TR.AGREGAR PERSONA", "jsAgregarPersona()", $xFRM->ic()->PERSONA);
			$xFRM->OButton("TR.VINCULAR PERSONA", "jsVincularPersona()", $xFRM->ic()->PERSONA);
		} else {
			$xFRM->OButton("TR.VER PERSONA", "jsVerPersona()", $xFRM->ic()->PERSONA);
		}
		//Si el credito no ha sido asignado
		if($xTabla->credito()->v() <= DEFAULT_CREDITO){
			
		}
		if($xTabla->credito()->v() <= DEFAULT_CREDITO){
			//Imprimir Propuesta
			$xFRM->OButton("TR.IMPRIMIR PROPUESTA", "var xC=new CredGen();xC.getLeasingPropuesta($clave)", $xFRM->ic()->IMPRIMIR);
		}
	} else {
		if($xTabla->persona()->v() <= DEFAULT_SOCIO){
			//$xFRM->OButton("TR.AGREGAR PERSONA", "jsAgregarPersona()", $xFRM->ic()->PERSONA);
		}
	}
	//Iniciar $xleas
	$xLeas	= new cCreditosLeasing($clave);
	$xLeas->init();
	
	$xFRM->OButton("TR.AGREGAR DOCUMENTOS", "jsAgregarDocumentos()", $xFRM->ic()->ARCHIVOS);
}


$xFRM->addSeccion("iddoriginador", "TR.DATOS DEL ORIGINADOR");

$xFRM->OHidden("idoriginacion_leasing", $xTabla->idoriginacion_leasing()->v());
$xFRM->OHidden("fecha_origen", $xTabla->fecha_origen()->v());

$xFRM->OHidden("tasa_credito", $xTabla->tasa_credito()->v());
$xFRM->OHidden("tasa_tiie", $xTabla->tasa_tiie()->v());

/*if(MODO_DEBUG == true){
	$xFRM->OMoneda("total_credito", $xTabla->total_credito()->v(), "TR.TOTAL CREDITO");
	$xFRM->OMoneda("cuota_accesorios", $xTabla->cuota_accesorios()->v(), "TR.CUOTA ACCESORIOS");
	$xFRM->OMoneda("cuota_tenencia", $xTabla->cuota_tenencia()->v(), "TR.CUOTA TENENCIA");
	$xFRM->OMoneda("cuota_mtto", $xTabla->cuota_mtto()->v(), "TR.CUOTA MTTO");
	$xFRM->OMoneda("cuota_seguro", $xTabla->cuota_seguro()->v(), "TR.CUOTA SEGURO");
	$xFRM->OMoneda("cuota_garantia", $xTabla->cuota_garantia()->v(), "TR.CUOTA GARANTIA");
} else {*/
	$xFRM->OHidden("total_credito", $xTabla->total_credito()->v());
	$xFRM->OHidden("cuota_accesorios", $xTabla->cuota_accesorios()->v());
	$xFRM->OHidden("cuota_tenencia", $xTabla->cuota_tenencia()->v());
	$xFRM->OHidden("cuota_mtto", $xTabla->cuota_mtto()->v());
	$xFRM->OHidden("cuota_seguro", $xTabla->cuota_seguro()->v());
	$xFRM->OHidden("cuota_garantia", $xTabla->cuota_garantia()->v());
//}
$xFRM->OHidden("paso_proceso", $xTabla->paso_proceso()->v());
$xFRM->OHidden("usuario", $xTabla->usuario()->v());
$xFRM->OHidden("tipo_leasing", $xTabla->tipo_leasing()->v());
$xFRM->OHidden("tasa_iva", $xTabla->tasa_iva()->v());
$xFRM->OHidden("tasa_compra", $xTabla->tasa_compra()->v());

$xFRM->OHidden("monto_gps", $xTabla->monto_gps()->v());
$xFRM->OHidden("monto_directo", $xTabla->monto_directo()->v());

//$xFRM->OHidden("monto_gps", $xTabla->monto_gps()->v());

$xFRM->OHidden("monto_residual", $xTabla->monto_residual()->v());
//
$xFRM->OHidden("cuota_vehiculo", $xTabla->cuota_vehiculo()->v());
$xFRM->OHidden("cuota_aliado", $xTabla->cuota_aliado()->v());

//$xFRM->OHidden("cuota_gps", $xTabla->cuota_gps()->v());
$xFRM->OHidden("monto_comision", $xTabla->monto_comision()->v());
$xFRM->OHidden("monto_originador", $xTabla->monto_originador()->v());
$xFRM->OHidden("estatus", $xTabla->estatus()->v());

$xFRM->OHidden("usuario", $xTabla->usuario()->v());

//$xFRM->OMoneda("tipo_leasing", $xTabla->tipo_leasing()->v(), "TR.TIPO LEASING");
if($xUser->getEsOriginador() == false){
	//Si no hay credito se habilitan los campos de edicion
	if($xTabla->credito()->v() <= DEFAULT_CREDITO){
		$xFRM->addHElem( $xSel->getListaDeOficiales("oficial", SYS_USER_ESTADO_ACTIVO,  $xTabla->oficial()->v())->get(true) );
		$xSelOrg	= $xSel->getListaDeOriginadores("originador", $xTabla->originador()->v());
		if($xTabla->credito()->v() <= DEFAULT_CREDITO){
			$xSelOrg->addEvent("onblur", "jsDatosIniciales()");
			$xSelOrg->addEvent("onchange", "jsDatosIniciales()");
		}
		$xFRM->addHElem($xSelOrg->get(true) );
		$xFRM->addHElem($xSel->getListaDeSubOriginadores("suboriginador", $xTabla->suboriginador()->v())->get(true) );
		
		$xFRM->OMoneda("comision_originador", $xTabla->comision_originador()->v(), "TR.COMISION ORIGINADOR");
		$xFRM->OMoneda2("comision_apertura", $xTabla->comision_apertura()->v(), "TR.COMISION_POR_APERTURA");
	//$xFRM->setValidacion("comision_originador", "jsGetComision");
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
	}
	$xFRM->OHidden("nombre_cliente", $xTabla->nombre_cliente()->v());
	//Vinculo de Credito
	if($xTabla->credito()->v() > DEFAULT_CREDITO){
		$xCred	= new cCredito($xTabla->credito()->v());
		if($xCred->init() == true){
			$xFRM->addHElem($xCred->getFicha());
			if($EsOriginador == false){
				$xFRM->OButton("TR.VER CREDITO", "jsVerCredito()", $xFRM->ic()->CREDITO);
				$xFRM->OButton("TR.DATOS_DE_TRANSFERENCIA", "var cGen=new CredGen();cGen.setAgregarBancos(" .$xTabla->credito()->v() . ");", $xFRM->ic()->BANCOS);
				//Validar Si existe Dato del Vehiculo
				$xArr	= new cCreditosLeasing($clave);
				if($xArr->init() == true){
					$xFRM->OButton("TR.AGREGAR FLOTA", "jsAgregarDatosVehiculo()", $xFRM->ic()->CREDITO);
				}
				//======================== Plan Cliente
				$xFRM->OButton("TR.PLAN_DE_PAGOS CLIENTE", "jsGetPlanCliente()", $xFRM->ic()->CALENDARIO1);
			}
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
}
$xFRM->OText("nombre_atn", $xTabla->nombre_atn()->v(), "TR.NOMBRE ATN");
//--- 25/Mayo/2017
$xFRM->OText_13("tel", $xTabla->tel()->v(), "TR.TELEFONO");
$xFRM->OMail("mail", $xTabla->mail()->v(), "TR.CORREO_ELECTRONICO");

//---
$xSelRac	= $xSel->getListaDeLeasingRAC("tipo_rac", $xTabla->tipo_rac()->v());
$xSelRac->addEvent("onblur", "jsaGetTasa()");
$xSelRac->addEvent("onchange", "jsaGetTasa()");
$xFRM->addHElem( $xSelRac->get(true) );
if($PersonaCargado == false){
	$xFRM->OSiNo("TR.ES PERSONA_MORAL", "es_moral", $xTabla->es_moral()->v());
} else {
	$xFRM->OHidden("es_moral", $xTabla->es_moral()->v());
}
$xFRM->endSeccion();
//========================================= VEHICULO ====================================
$xFRM->addSeccion("iddvehi", "TR.DATOS DEL VEHICULO");

$xFRM->addHElem($xSel->getListaDeVehiculosMarcas("marca", $xTabla->marca()->v())->get(true) );

$xFRM->addHElem($xSel->getListaDeVehiculosUsos("tipo_uso", $xTabla->tipo_uso()->v())->get(true));

$xFRM->addHElem($xSel->getListaDeVehiculosSegmentos("segmento", $xTabla->segmento()->v())->get(true));


$xFRM->OText_13("modelo", $xTabla->modelo()->v(), "TR.VERSION");
$xFRM->OText_13("annio", $xTabla->annio()->v(), "TR.ANNIO");


$xFRM->OMoneda2("precio_vehiculo", $xTabla->precio_vehiculo()->v(), "TR.PRECIO VEHICULO");
$xFRM->OMoneda2("monto_anticipo", $xTabla->monto_anticipo()->v(), "TR.MONTO ANTICIPO");

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

$xSelGPS	= $xSel->getListaDeVehiculosGPS("tipo_gps", $xTabla->tipo_gps()->v());
$xSelGPS->addEvent("onblur", "jsaGetCostoGPS()");
$xFRM->addHElem($xSelGPS->get(true) );



if($xUser->getEsOriginador() == false){

	$xFRM->OMoneda2("monto_aliado", $xTabla->monto_aliado()->v(), "TR.EQUIPOALIADO");
	$xFRM->OText("describe_aliado", $xTabla->describe_aliado()->v(), "TR.DESCRIPCION EQUIPOALIADO");
	
	
	$xFRM->OMoneda2("monto_accesorios", $xTabla->monto_accesorios()->v(), "TR.ACCESORIOS");
	$xFRM->OMoneda2("monto_seguro", $xTabla->monto_seguro()->v(), "TR.AUTOSEGURO");
	$xFRM->OMoneda2("monto_tenencia", $xTabla->monto_tenencia()->v(), "TR.TENENCIA");
	$xFRM->OMoneda2("monto_garantia", $xTabla->monto_garantia()->v(), "TR.GARANTIA");
	$xFRM->OMoneda2("monto_mtto", $xTabla->monto_mtto()->v(), "TR.MTTO");
	
	
	$xFRM->OMoneda2("monto_gps", $xTabla->monto_gps()->v(), "TR.MONTO PAQUETESGPS");
	$xFRM->OMoneda2("monto_placas", $xTabla->monto_placas()->v(), "TR.COSTOPLACAS");
	$xFRM->OMoneda2("monto_gestoria", $xTabla->monto_gestoria()->v(), "TR.GASTOSGESTORIA");
	$xFRM->OMoneda2("monto_notario", $xTabla->monto_notario()->v(), "TR.GASTOSNOTARIALES");
	
	$xFRM->ODisabledM("trenta_deposito", $xTabla->renta_deposito()->v(), "TR.RENTADEPOSITO");
	
	$xFRM->OHidden("renta_deposito", $xTabla->renta_deposito()->v());
	$xFRM->ODisabledM("trenta_proporcional", $xTabla->renta_proporcional()->v(), "TR.RENTA PROPORCIONAL");
	$xFRM->OHidden("renta_proporcional", $xTabla->renta_proporcional()->v());
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
$xFRM->OSiNo("TR.TENENCIA FINANCIADO","financia_tenencia", $xTabla->financia_tenencia()->v());
if($xUser->getEsOriginador() == false){
	$xFRM->OSiNo("TR.DOMICILIA","domicilia", $xTabla->domicilia()->v());
} else {
	$xFRM->OHidden("domicilia", $xTabla->domicilia()->v());
}

$xFRM->OHidden("residuales", $xTabla->residuales()->v(), "TR.RESIDUALES");
$xFRM->OHidden("cuota_iva", $xTabla->cuota_iva()->v());

$xFRM->endSeccion();
$xFRM->addSeccion("iddescenas", "TR.ESCENARIOS");

//================= No usar residuales



//validaciones de recalculo
/*$xFRM->setValidacion("chk-financia_seguro", "jsCalculaFinanciamiento");
$xFRM->setValidacion("chk-financia_tenencia", "jsCalculaFinanciamiento");
$xFRM->setValidacion("chk-domicilia", "jsCalculaFinanciamiento");

$xFRM->setValidacion("monto_aliado_mny", "jsCalculaFinanciamiento");
$xFRM->setValidacion("monto_accesorios_mny", "jsCalculaFinanciamiento");
$xFRM->setValidacion("monto_tenencia_mny", "jsCalculaFinanciamiento");
$xFRM->setValidacion("monto_garantia_mny", "jsCalculaFinanciamiento");
$xFRM->setValidacion("monto_mtto_mny", "jsCalculaFinanciamiento");
$xFRM->setValidacion("monto_gps_mny", "jsCalculaFinanciamiento");
$xFRM->setValidacion("monto_seguro_mny", "jsCalculaFinanciamiento");

$xFRM->setValidacion("monto_anticipo_mny", "jsCalculaFinanciamiento");
$xFRM->setValidacion("precio_vehiculo_mny", "jsCalculaFinanciamiento");*/
$xFRM->setValidacion("precio_vehiculo_mny", $xFRM->VALIDARCANTIDAD);

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
	$xFRM->OHidden("tasa_credito_$idx", 0);
	$xFRM->OHidden("residual_$idx", 0);
	$xFRM->OHidden("monto_gps_$idx", 0);
	
	$tt		.= "<th>" . $xEsc->descripcion_escenario()->v() . "</th>";
}
$tt		.= "</tr>";

//==== ================================================ Residual
$tt		.= "<tr>";
$tt		.= "<th>" . $xFRM->getT("TR.RESIDUAL") . "</th>";
$jsVRes	= "";

foreach ($rs as $rw){
	$xEsc->setData($rw);
	$idx	= $xEsc->plazo()->v();
	$xHS	= new cHSelect();
	$D		= $xQL->getDataRow("SELECT * FROM `leasing_residual` WHERE $idx >=`limite_inferior` AND $idx <= `limite_superior` ");
	$li		= setNoMenorQueCero($D["porciento_residual"]);
	$ls		= setNoMenorQueCero($D["porciento_final"]);

	if($li == $ls){
		//Agregar control de solo lectura
		$tt		.= "<td class='tit'>$li</td>";
		$xFRM->OHidden("tasaresidual_$idx", $li);
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
	//Suma JS
	$jsVRes	.= ($jsVRes == "") ? "'$idx-' + $(\"#tasaresidual_$idx\").val()" : " + ',$idx-' + $(\"#tasaresidual_$idx\").val()";
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
$tt		.= "<tr class='tr-plan'>";
$tt		.= "<th>" . $xFRM->getT("TR.VALORRESIDUAL") . "</th>";
foreach ($rs as $rw){
	$xEsc->setData($rw);
	$idx	= $xEsc->plazo()->v();
	$tt		.= "<td class='mny' id='residual-$idx'></td>";
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

$tt		.= "<tr>";
$tt		.= "<th>" . $xFRM->getT("TR.TASA_ANUALIZADA") . "</th>";
foreach ($rs as $rw){
 	$xEsc->setData($rw);$idx	= $xEsc->plazo()->v();
 	$tt		.= "<td class='mny' id='tasa_credito-$idx'></td>";
}
$tt		.= "</tr>";

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
	$xFRM->addJsInit("jsCalcularEscenarios();");
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
		if($xTabla->oficial()->v() > 0 ){
			$paso	= $xCProc->PASO_CON_OFICIAL;
		} else {
			$paso	= $xCProc->PASO_ATENDIDO;
		}
	}
	//Editar Bonos
	$xFRM->OButton("TR.BONOS", "jsEditarBonos()", $xFRM->ic()->DINERO);
	
	//Generar Bonos Automaticamente.
	
	
	//Actualizar
	
	$xQL->setRawQuery("UPDATE `originacion_leasing` SET `paso_proceso`=$paso WHERE `idoriginacion_leasing`=$clave");
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
var mFactorRentaDep		= 1;
var mFactorRentaProp	= 1;
var vTasaIVA			= <?php echo TASA_IVA; ?>;
var vCalcComXTodo		= true; //Calcular la comision por apertura por el Monto de Vehiculo sin IVA
var mSumarIVA			= true;
var mFactorMinAnticipo	= 0.2;
var mFactorSeguro		= 0.03;
var mMontoMinGtosNot	= 400;
var vFactorIVA			= 1 / (1+vTasaIVA);

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
	var anticipo			= $("#monto_anticipo").val();
	var minanticipo			= (precio*vFactorIVA) * mFactorMinAnticipo;
	minanticipo				= redondear(minanticipo,2);
	if(minanticipo > anticipo){
		$("#monto_anticipo_mny").val( getFMoney(minanticipo) );
		$("#monto_anticipo").val( minanticipo );
	}


	//jsCargarCostos
	jsGetCostos();
	//Calcular minimos de costos	
	var notario				= $("#monto_notario").val();
	if(mMontoMinGtosNot > notario){
		$("#monto_notario_mny").val( getFMoney(mMontoMinGtosNot) );
		$("#monto_notario").val( mMontoMinGtosNot );
	}
	var seguro				= $("#monto_seguro").val();
	var minseguro			= (precio*vFactorIVA) * mFactorSeguro;
	minseguro				= redondear(minseguro,2);
	if(minseguro > seguro){
		$("#monto_seguro_mny").val( getFMoney(minseguro) );
		$("#monto_seguro").val( minseguro );		
	}

	//Todos a moneda
	xG.aMonedaForm();
	xG.spinInit();
	jsCalculaFinanciamiento();
	setTimeout(jsCalcularEscenariosII,2000);
}

function jsCalcularEscenariosII(){
	var idsolicitud	= $("#idoriginacion_leasing").val();
	var idtr		= flotante($("#tasa_credito_12").val());
	if(idtr <=0){
		jsCalcularEscenarios();
	} else {	
			//==================== Asignar Costos GPS al Valor 
			$("#monto_gps-12").html( getFMoney($("#monto_gps_12").val()) );
			$("#monto_gps-24").html( getFMoney($("#monto_gps_24").val()) );
			$("#monto_gps-36").html( getFMoney($("#monto_gps_36").val()) );
			$("#monto_gps-48").html( getFMoney($("#monto_gps_48").val()) );
			$("#monto_gps-60").html( getFMoney($("#monto_gps_60").val()) );
			//== Mostrar Tasa de Interes
			$("#tasa_credito-12").html( getFMoney($("#tasa_credito_12").val()) );
			$("#tasa_credito-24").html( getFMoney($("#tasa_credito_24").val()) );
			$("#tasa_credito-36").html( getFMoney($("#tasa_credito_36").val()) );
			$("#tasa_credito-48").html( getFMoney($("#tasa_credito_48").val()) );
			$("#tasa_credito-60").html( getFMoney($("#tasa_credito_60").val()) );			
			//====================
			jsCalcular(12);
			jsCalcular(24);
			jsCalcular(36);
			jsCalcular(48);	
			jsCalcular(60);
			
		xG.spinEnd();
		//Actualizar el Programa
		if(entero(idsolicitud)>0){
			xG.save({form:'frmcotizacion',tabla:'originacion_leasing', id:idsolicitud});
		}
	}
}

function jsCalcular(idx){
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
	
	//Opcional
	var seguro				= getVRaw($("#monto_seguro").val());
	var tenencia			= getVRaw($("#monto_tenencia").val());
	//Directo
	var notario				= getVRaw($("#monto_notario").val());
	var placas				= getVRaw($("#monto_placas").val());
	var gestoria			= getVRaw($("#monto_gestoria").val());
	var idplazo				= $("#plazo").val();
	var montogps			= getVRaw($("#monto_gps_"+idx).val());

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
	var cc1				= xC.getCuotaDePago({capital:vCoste, tasa: idtasa, residual: residual, frecuencia: idfrecuencia, pagos: idpagos, iva: 0});
	//Equipo Aliado, base de Original
	//var cc6				= xC.getCuotaDePago({capital:aliado, tasa: idtasa, residual:0, frecuencia: idfrecuencia, pagos: idpagos, iva: 0});
	//Equipo GPS a la renta Principal
	var cc7				= xC.getCuotaDePago({capital:montogps, tasa: idtasa, residual:0, frecuencia: idfrecuencia, pagos: idpagos, iva: 0});
	//setLog(cc7);
	var trenta			= cc1+cc7;
	//Calcular Renta
	$("#renta-" + idx).html(getFMoney(trenta));
	if(idx == vPzoElegido){
		var rd	= getVConIVA(trenta) * mFactorRentaDep;
		var rp	= trenta * mFactorRentaProp;
		$("#trenta_deposito").val(getFMoney(rd));
		$("#renta_deposito").val(rd);

		$("#trenta_proporcional").val(getFMoney(rp));
		$("#renta_proporcional").val(rp);		
		//=========== IVA de Renta
		if(mSumarIVA == true){
			var mCuotaIva	= redondear((trenta * vTasaIVA),2);
			$("#cuota_iva").val(mCuotaIva); setLog(mCuotaIva);
		}
	}
	//Seguro
	if(financia_seguro== true){
		var cc3				= xC.getCuotaDePago({capital:seguro, tasa: idtasa, residual:0, frecuencia: idfrecuencia, pagos: idpagos, iva:0});
		$("#seguro-" + idx).html(getFMoney(cc3));
		//Guardar la Cuota por Seguro
	} else {
		$("#seguro-" + idx).html(getFMoney(0));
	}
	//tenencia
	if(financia_tenencia== true){
		var cc2				= xC.getCuotaDePago({capital:tenencia, tasa: idtasa, residual:0, frecuencia: idfrecuencia, pagos: idpagos, iva: 0});
		$("#tenencia-" + idx).html(getFMoney(cc2));
	} else {
		$("#tenencia-" + idx).html(getFMoney(0));
	}
	//Mantenimiento
	if(mtto > 0){
		var cc4				= xC.getCuotaDePago({capital:mtto, tasa: idtasa, residual:0, frecuencia: idfrecuencia, pagos: idpagos, iva: 0});
		$("#mtto-" + idx).html(getFMoney(cc4));		
	}
	//Accesorios
	if(accesorios >0){
		var cc5				= xC.getCuotaDePago({capital:accesorios, tasa: idtasa, residual:0, frecuencia: idfrecuencia, pagos: idpagos, iva: 0});
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
		} else {
			xG.alerta({msg: "El Credito ya existe"});
		}		
	} else {
		xG.alerta({msg: "Debe Vincular o Agregar una Persona"});
	}
}
function jsAgregarPersona(){
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
	xG.w({url:"../frmarrendamiento/leasing-activos.frm.php?idleasing=" + idcontrol, tab:true, h:600, w:800});
}
function jsDesactivarCotizacion(){
	xG.soloLeerForma(false, false);
}
function jsActualizaResiduales(){
	var xx = <?php  echo $jsVRes; ?>;
	$("#residuales").val(xx);
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
		xG.go({url: "../frmarrendamiento/cotizador.edit.frm.php?clave=" + xid});
	}
}
function jsEditarBonos(){
	var idcontrol	= $("#idoriginacion_leasing").val();
	xG.w({url: "../frmarrendamiento/leasing-bonos.frm.php?clave=" + idcontrol, tab: true});
}

</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>