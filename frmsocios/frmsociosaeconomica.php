<?php
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
$xHP		= new cHPage("TR.Actividad economica");
$xLoc		= new cLocal();
$xRuls		= new cReglaDeNegocio();
$jxc 		= new TinyAjax();
$xLog		= new cCoreLog();

$SinDetalleAcceso 		= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_SIN_DETALLE_ACCESO);		//regla de negocio
$EsSimple				= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_ACTIVIDAD_EC_SIMPLE);		//regla de negocio
$TratarComoSalarios		= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_ACTIVIDAD_EC_ASALARIADO);		//regla de negocio
$SinDispersion			= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_ACTIVIDAD_SIN_DISPERSION);	//regla de negocio
$SinDomicilio			= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_ACTIVIDAD_SIN_DOMICILIO);		//regla de negocio
$SinSCIAN				= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_ACTIVIDAD_SIN_SCIAN);		//regla de negocio

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$empresa	= parametro("empresa", SYS_TODAS, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT);
$identidadfederativa		= $xLoc->DomicilioEstadoClaveNum();
$msg		= (isset($_GET["msg"])) ? $_GET["msg"] : "";


function jsaSetDomicilioMismo($socio, $HDomicilio){
	$HDomicilio	= strtoupper($HDomicilio);
	
	if($HDomicilio == "MISMO"){
		$xSoc 			= new cSocio($socio);
		if($xSoc->init() == true){
			$ODOM		= $xSoc->getODomicilio();
			if($ODOM != null){
				$tab = new TinyAjaxBehavior();
				$tab -> add(TabSetValue::getBehavior('idtelefono', $xSoc->getTelefonoPrincipal()));
				$tab -> add(TabSetValue::getBehavior('identidadfederativa', $ODOM->getClaveDeEstado()));
				$tab -> add(TabSetValue::getBehavior('idmunicipio', $ODOM->getClaveDeMunicipio()));
				$tab -> add(TabSetValue::getBehavior('idlocalidad', $ODOM->getClaveDeLocalidad()));
				return $tab -> getString();
			}
		}
	}
}
function jsaGetDatosEmpresa($dependencia){
	//verificar si existe en la BD
	
	$Emp	= new cEmpresas($dependencia);
	
	$idpersona	= $Emp->getClaveDePersona();
	
	$Emp->init();
	
	$telefono 	= $Emp->getTelefono();
	$domicilio	= $Emp->getDomicilio();
	$razon		= $Emp->getNombre();
	$tab 		= new TinyAjaxBehavior();
	
	$tab->add(TabSetValue::getBehavior('idtelefono', $telefono));
	$tab->add(TabSetValue::getBehavior('idrazonsocialtrabajo', $razon));
	$xSoc		= $Emp->getOPersona();

	if($xSoc == null){
	} else {
		$xOBA		= $xSoc->getOActividadEconomica();
		if($xOBA != null){

			$idactividad			= $xOBA->getClaveDeActividad();
			$idsector				= $xOBA->getClaveDeSector();
			$idlocalidad			= $xOBA->getClaveDeLocalidad();
			$idmunicipio			= $xOBA->getClaveDeMunicipio();
			$identidadfederativa	= $xOBA->getClaveDeEstado();

			$tab->add(TabSetValue::getBehavior('idactividad', $idactividad));
			$tab->add(TabSetValue::getBehavior('identidadfederativa', $identidadfederativa));
			$tab->add(TabSetValue::getBehavior('idnombreacceso', $xOBA->getCalle()));
			$tab->add(TabSetValue::getBehavior('idcodigopostal', $xOBA->getCodigoPostal()));
			$tab->add(TabSetValue::getBehavior('idnombrecolonia', $xOBA->getNombreColonia()));
		}
	}
	//TODO: cargar estado y municiopios
	return $tab -> getString();

}

function jsaImportarDeAsociada($persona){ $xSoc	= new cSocio($persona);	$xSoc->getImportarDesdeAsociada(TPERSONAS_ACTIVIDAD_ECONOMICA); }

function jsaGetMunicipios($identidadfederativa, $pais, $cp){
	$txt	= "";
	$text	= new cHText();
	$xSel	= new cHSelect();
	$text->setDivClass("");
	$mun	= false;
	if(setNoMenorQueCero($cp) > 0){
		if($pais == EACP_CLAVE_DE_PAIS){
			$xCol		= new cPersonasVivCodigosPostales($cp);
			if($xCol->init() == true){
				$mun		= $xCol->getClaveDeMunicipio();
				$txt		= $xCol->getNombreMunicipio();
			}
		}
	}
	return ($pais != EACP_CLAVE_DE_PAIS OR PERSONAS_VIVIENDA_MANUAL == true) ? $text->getDeNombreDeMunicipio("idnombremunicipio", $txt, "TR.Municipio") : $xSel->getListaDeMunicipios("", $identidadfederativa, $mun)->get(false);
}
function jsaGetLocalidades($identidadfederativa, $municipio, $pais, $cp){
	$xSel	= new cHSelect();
	$text	= new cHText();
	$txt	= "";
	$v		= "";
	if(setNoMenorQueCero($cp) > 0){
		if($pais == EACP_CLAVE_DE_PAIS){
			$xCol		= new cDomiciliosColonias();
			if($xCol->existe($cp) == true){
				$v		= $xCol->getNombreLocalidad();
			}
		}
	}
	if(PERSONAS_VIVIENDA_MANUAL == true ){
		$text->setDivClass("");
		$txt		= $text->getDeNombreDeLocalidad("idnombrelocalidad", $v, "TR.Localidad");
	} else {
		$xS 		= $xSel->getListaDeLocalidades("", $identidadfederativa, $pais);
		$txt		= $xS->get(false);
		if($xS->getCountRows() <= 0){						//Corregir si no hay registros
			$text->setDivClass("");
			$txt	= $text->getDeNombreDeLocalidad("idnombrelocalidad", $v, "TR.Localidad");
		}
	}
	return $txt;
}

$jxc ->exportFunction('jsaGetMunicipios', array('identidadfederativa', 'idpais', 'idcodigopostal'), "#txtmunicipio");
$jxc ->exportFunction('jsaGetLocalidades', array('identidadfederativa', 'idmunicipio', 'idpais', 'idcodigopostal'), "#txtlocalidad");

$jxc ->exportFunction('jsaSetDomicilioMismo', array('idsociorelacionado', 'iddomiciliodeactividad'));
$jxc ->exportFunction('jsaGetDatosEmpresa', array('iddependencia'));
$jxc ->exportFunction('jsaImportarDeAsociada', array('idsociorelacionado'), "#fb_id-frmaeconomica");

$jxc ->process();

$jsb	= new jsBasicForm("frmaeconomica", iDE_CREDITO);
$xFRM	= new cHForm("frmaeconomica", "frmsociosaeconomica.php?socio=$persona&action=" . MQL_ADD);

$xTxt6	= new cHText();
$xSel	= new cHSelect();
$xHSel	= new cHSelect();
$xTxtE 	= new cHText();
$xTxtE->setDivClass("");

$xHP->init("initComponents()");

/* verifica si hay un dato */
$tipo_ae 			= parametro("idactividad", FALLBACK_ACTIVIDAD_ECONOMICA, MQL_INT);
$tipo_aescian		= parametro("idactividadscian", FALLBACK_ACTIVIDAD_ECONOMICA_SCIAN, MQL_INT);

$sector_ae 			= parametro("idsectoreconomico", FALLBACK_SECTOR_ECONOMICO, MQL_INT);
$nombre_ae 			= parametro("idrazonsocialtrabajo");
$domicilio_ae 		= parametro("iddomiciliodeactividad");
$nombrelocalidad 	= parametro("idnombrelocalidad");
$idlocalidad 		= parametro("idlocalidad", $xLoc->DomicilioLocalidadClave(), MQL_INT);
$nombremunicipio 	= parametro("idnombremunicipio");
$idmunicipio 		= parametro("idmunicipio", $xLoc->DomicilioMunicipioClave(), MQL_INT);

$identidadfederativa= parametro("identidadfederativa", $xLoc->DomicilioEstadoClaveNum(), MQL_INT ); //DEFAULT_NOMBRE_ESTADO
$telefono_ae 		= parametro("idtelefono");
$extension_ae 		= parametro("idextension");
$numero_empleado 	= parametro("idnumeroempleado");
$antiguedad_ae 		= false;
$departamento_ae 	= parametro("iddepartamento");
$montoper_ae 		= parametro("idsalario", 0, MQL_FLOAT);
$empresa 			= parametro("iddependencia", FALLBACK_CLAVE_EMPRESA, MQL_INT);
$puesto 			= parametro("idpuesto");
$nss 				= parametro("idnss");
$idvivienda			= parametro("idvivienda", 0, MQL_INT);
$idviviendaant		= ($idvivienda>0) ? true : false;

$fechaalta			= fechasys();

$cp					= parametro("idcodigopostal", $xLoc->DomicilioCodigoPostal(), MQL_INT);
$idcolonia			= parametro("idclavecolonia", 0, MQL_INT);
$nombrecolonia		= parametro("idnombrecolonia");
$nexterior			= parametro("idnumeroexterior");
$ninterior			= parametro("idnumerointerior");
$idreferencias		= parametro("idreferencias");

$tipo_acceso		= parametro("idtipoacceso", "calle", MQL_RAW);
$calle				= parametro("idnombreacceso");
$fecha_ingreso		= parametro("idfechaingreso", fechasys(), MQL_DATE);
$tipodispersion		= parametro("idtipodispersion", FALLBACK_PERSONAS_AE_TIPO_DISPERSION, MQL_INT );
$empleoanterior		= parametro("idempleoanterior", false, MQL_BOOL);
$asalariado			= false;
$pais				= parametro("idpais", EACP_CLAVE_DE_PAIS, MQL_RAW);
$iddescribe			= parametro("iddescripcionactividad");
$loaded				= false;
$nombreestado		= "";

//Agregar
if(setNoMenorQueCero($persona) > DEFAULT_SOCIO){
	/* verifica si el socio o datos son validos */
	$xSoc			= new cSocio($persona);
	if($xSoc->init() == true){
		if( $action == MQL_ADD){
			if(PERSONAS_VIVIENDA_MANUAL == true OR $pais != EACP_CLAVE_DE_PAIS){
				//sila vivienda es manual o el pais es diferente al actual
				$xLog->add("WARN\tLa vivienda es manual y el pais no es Mexico\r\n", $xLog->DEVELOPER);
			} else {
				$xCol				= new cDomiciliosColonias();
				if($xCol->existe($cp, "", "", true) == true){
						$nombrecolonia		= ($nombrecolonia == "") ? $xCol->getNombre() : $nombrecolonia;
						$nombremunicipio	= ($nombremunicipio == "") ? $xCol->getNombreMunicipio() : $nombremunicipio;
						$nombreestado		= $xCol->getNombreEstado();
						$nombrelocalidad	= ($nombrelocalidad == "") ? $xCol->getNombreLocalidad() : $nombrelocalidad;
						$xLog->add("WARN\tSe carga datos de Vivienda por Codigo Postal\r\n", $xLog->DEVELOPER);
				}				
			}
			if(MODULO_AML_ACTIVADO == true AND $pais != EACP_CLAVE_DE_PAIS AND $idlocalidad > 0){
				$xDLoc		= new cDomicilioLocalidad($idlocalidad);
				if( $xDLoc->init() == true){
					$nombrelocalidad			= ($nombrelocalidad == "") ? $xDLoc->getNombre() : $nombrelocalidad;
					$nombreestado				= $xDLoc->getNombre();
					$nombremunicipio			= ($nombremunicipio == "") ? $xDLoc->getNombre() : $nombremunicipio;
					$xLog->add("WARN\tSe Cargan Datos de Vivienda por Localidad\r\n", $xLog->DEVELOPER);
				}				
			}

				if($idviviendaant == true){
					$iddomicilio	= $idvivienda;				//Copia el ID de Vivienda Anterior
					$xLog->add("WARN\tSe Importa la vivienda anterior con ID $idvivienda\r\n", $xLog->DEVELOPER);
					if($iddomicilio<= 0){
						$iddomicilio			= $xSoc->getIDDeVivienda(); //Clave de Vivienda neutra
						$xLog->add("WARN\tNo existe la vivienda, se importa $iddomicilio\r\n", $xLog->DEVELOPER);
					}
					$success	= true;//Omitir Vivienda
				} else {
					if($identidadfederativa > 0){
						$xEstado			= new cDomiciliosEntidadFederal($identidadfederativa);
						$nombreestado		= $xEstado->getNombre();
						$xLog->add("WARN\tNombre de Entidad Federativa por ID\r\n", $xLog->DEVELOPER);
					}
					if($SinDomicilio == false){

						$success	= $xSoc->addVivienda($calle, $nexterior, $cp, "", $idreferencias, $telefono_ae, "", 
								false, PERSONAS_REG_VIV_NINGUNO, PERSONAS_TIPO_DOM_LABORAL, $antiguedad_ae, 
								$nombrecolonia, PERSONAS_TIPO_ACCESO_CALLE, "",	$idlocalidad, $pais, 
								"",$nombreestado, $nombremunicipio, $nombrelocalidad, $fecha_ingreso);
						if($success == false){
							$xLog->add("ERROR\tNo se agrega el Domicilio de la Actividad Economica\r\n");
						}
					} else {
						$success	= true;//Omitir Vivienda
						$xLog->add("WARN\tSe Omite el domicilio en la Actividad Economica\r\n", $xLog->DEVELOPER);
						
					}
					$iddomicilio			= $xSoc->getIDDeVivienda(); //Clave de Vivienda neutra
				}
				if($success == true){
					
					$xAE			= new cPersonaActividadEconomica($xSoc->getCodigo());
					if($empresa != FALLBACK_CLAVE_EMPRESA OR $TratarComoSalarios == true){
						$xAE->setEmpresa($empresa, $puesto, $departamento_ae, $numero_empleado, $nss, $extension_ae, $tipodispersion, $fecha_ingreso);
						$xLog->add("WARN\tSe Actualizan datos de la Empresa\r\n", $xLog->DEVELOPER);
					}
					if($iddomicilio > 1){
						$xAE->setDomicilioVinculado($iddomicilio);
						$xLog->add("WARN\tSe Actualizan el Domicilio Vinculado\r\n", $xLog->DEVELOPER);
					}
					$success	= $xAE->add($tipo_ae, $montoper_ae, $antiguedad_ae, $nombre_ae, $cp, $telefono_ae, $idlocalidad, $nombrelocalidad, $nombremunicipio, $nombreestado, $empleoanterior, $iddescribe);
					$xLog->add( $xAE->getMessages());
				}
			$xFRM->setResultado($success);
		} else {
			$empresa			= $xSoc->getClaveDeEmpresa();
			if(PERSONAS_CONTROLAR_POR_EMPRESA == true){
				if($empresa != FALLBACK_CLAVE_EMPRESA){
					$xEmp			= new cEmpresas($empresa);
					$xEmp->init();
					$OPersona		= $xEmp->getOPersona();
					if( $OPersona !== null){
						$nombre_ae		= $OPersona->getNombreCompleto();
						$telefono_ae 	= $OPersona->getTelefonoPrincipal();
						//domiclio
						$DDomicilio		= $OPersona->getODomicilio();
						if( $DDomicilio != null){
							$identidadfederativa				= $DDomicilio->getClaveDeEstado();
							$nombrelocalidad 	= $DDomicilio->getCiudad();
							$idlocalidad 		= $DDomicilio->getClaveDeLocalidad();
							$nombremunicipio 	= $DDomicilio->getMunicipio();
							$idmunicipio 		= $DDomicilio->getClaveDeMunicipio();
							$cp					= $DDomicilio->getCodigoPostal();
							$calle				= $DDomicilio->getCalle();
							$nexterior			= $DDomicilio->getNumeroExterior();
							$nombrecolonia		= $DDomicilio->getColonia();
						}
						$DActividad				= $OPersona->getOActividadEconomica();
						if( $DActividad != null){
							$tipo_ae		= $DActividad->getClaveDeActividad();
							$sector_ae		= $DActividad->getClaveDeSector();
							$tipo_aescian	= $DActividad->getClaveActividadSCIAN();
						}
					}
					$asalariado				= true;
				}
			}
		}
	}
	if(MODO_DEBUG == true){	 $msg .= $xSoc->getMessages(OUT_TXT); $xFRM->addLog($msg);	}

}
if($action == SYS_NINGUNO){
	$xTx3			= new cHText();
	//===================================== GENERALES
	$xFRM->OHidden("idsociorelacionado", $persona);
	$xFRM->setTitle($xHP->getTitle());
	$xFRM->setNoAcordion();
	$xFRM->addGuardar();
	//===================================== EMPRESA
	$xFRM->addSeccion("idddatosgeneralesae", "TR.DATOS_GENERALES");
	$cDE 			= $xSel->getListaDeEmpresas("iddependencia", false, $empresa);
	$cDE->addEvent("onblur", "jsGetDatosEmpresa");
	
	if(PERSONAS_CONTROLAR_POR_EMPRESA == true){
		$xFRM->addHElem($cDE->get("TR.Empresa Relacionada", true) );
	} else {
		$xFRM->OHidden("iddependencia", DEFAULT_EMPRESA);
	}
	if($empresa == DEFAULT_EMPRESA OR $empresa == FALLBACK_CLAVE_EMPRESA){
		$xFRM->OText("idrazonsocialtrabajo", $nombre_ae, "TR.Nombre_Comercial / razon_social");
	} else {
		$xFRM->OHidden("idrazonsocialtrabajo", $nombre_ae);
	}
	if($EsSimple == true){
		$xFRM->addHElem( $xSel->getListaDeTiposdeActividadEconomica("idactividad", $tipo_ae)->get(true) );
		$xFRM->ODate("idfechaingreso", $fecha_ingreso, "TR.Fecha de Ingreso");
		$xFRM->OMoneda("idsalario", $montoper_ae, "TR.Ingreso_Mensual");
	} else {
		$xFRM->ODate("idfechaingreso", $fecha_ingreso, "TR.Fecha de Ingreso");
		$xFRM->OMoneda("idsalario", $montoper_ae, "TR.Ingreso_Mensual");
		
		
		if(MODULO_AML_ACTIVADO == true){
			$xFRM->addHElem( $xTxt6->getDeActividadEconomica("idactividad", $tipo_ae, "TR.Clave UIF") );
			$xFRM->setValidacion("idactividad", "validacion.actividadeconomica", "TR.ACTIVIDAD_ECONOMICA invalido");
		} else {
			$xFRM->OHidden("idactividad", $tipo_ae);
		}
		if($SinSCIAN == false){
			$xFRM->addHElem( $xTxt6->getDeActividadEconomicaSCIAN("idactividadscian", $tipo_aescian, "TR.Clave SCIAN") );
			$xFRM->setValidacion("idactividadscian", "validacion.actividadeconomica", "TR.ACTIVIDAD_ECONOMICA invalido");
		} else {
			$xFRM->OHidden("idactividadscian", $tipo_aescian);
		}
	}
	$xFRM->setValidacion("idsalario", $xFRM->VALIDARCANTIDAD, "TR.El Salario debe ser mayor a 0");
	if( $asalariado == true OR $TratarComoSalarios == true){
		if($SinDispersion == true){
			$xFRM->OHidden("idtipodispersion", $tipodispersion);
		} else {
			$xFRM->addHElem( $xSel->getListaDeTipoDeDispersion("", $tipodispersion)->get(true) );
		}
		
		$xFRM->OText_13("idpuesto", $puesto, "TR.Cargo");
		$xFRM->OText_13("iddepartamento", $departamento_ae, "TR.Departamento");
		$xFRM->OText_13("idnumeroempleado", $numero_empleado, "TR.Clave_de_Empleado");
		$xFRM->OText_13("idnss", $nss, "TR.ID_DE_SEGURIDADSOCIAL");
	}
	$xFRM->OText("iddescripcionactividad", "", "TR.DESCRIPCION DE ACTIVIDADES");
	
	$xFRM->OCheck("TR.Empleo Anterior", "idempleoanterior");
	
	//$xFRM->OCheck("TR.Domicilio Existente", "iddomicilioexistente");
	
	
	$xFRM->endSeccion();
//====================== Domicilio Existente
	$xFRM->addSeccion("idviviendaanterior", "TR.Domicilio_Existente");
	$xSelDExt	= $xSel->getListaDeDomicilioPorPers($persona, "idvivienda", 0);
	$xSelDExt->addEspOption(SYS_NINGUNO, $xFRM->getT("TR.AGREGAR NUEVO"));
	$xSelDExt->setOptionSelect(SYS_NINGUNO);
	$xSelDExt->setLabel("TR.UBICACION");
	$xSelDExt->addEvent("onchange", "jsSetDomicilioPrevio()");
	
	$xFRM->addHElem( $xSelDExt->get(true ) );
//====================== Domicilio Nuevo

if($SinDomicilio == false){
	$xFRM->addSeccion("idviviendadatos", "TR.Agregar Nuevo Domicilio");
	
	$xHSel->setEnclose(false);
	$xHSel->addOptions( array( "calle" => "Calle", "avenida" => "Avenida", "andador" => "Andador", "camino_rural"=> "Camino Rural") );
	
	if($SinDetalleAcceso == true){
		$xFRM->OText("idnombreacceso", "", "TR.Domicilio");
		$xFRM->OHidden("idtipoacceso", $tipo_acceso);
		$xFRM->setValidacion("idnombreacceso", $xFRM->VALIDARVACIO, "", true);
	} else {
		$xFRM->addDivSolo($xHSel->get("idtipoacceso", "", $tipo_acceso), $xTxtE->getNormal("idnombreacceso", ""), "tx14", "tx34" );
		$xFRM->setValidacion("idnombreacceso", $xFRM->VALIDARVACIO, "", true);
	}
	
	$xFRM->OText_13("idnumeroexterior", $nexterior, "TR.Numero_Exterior");
	$xFRM->setValidacion("idnumeroexterior", $xFRM->VALIDARVACIO, "", true);
	$xFRM->OMoneda("idtelefono", $telefono_ae, "TR.Telefono");
	$xFRM->OMoneda("idextension", $extension_ae, "TR.Extension");
	//Validaciones
	
	//===================================== DOMICILIO
	if(MODULO_AML_ACTIVADO == true){
		$xFRM->addHElem( $xSel->getListaDePaises("", $pais)->get(true) );
	} else {
		$xFRM->OHidden("idpais", EACP_CLAVE_DE_PAIS);
	}	
	$xCP	= new cHText();
	if(PERSONAS_VIVIENDA_MANUAL == true){
		$xFRM->OMoneda("idcodigopostal", $cp, "TR.codigo_postal");
	} else {
		$xFRM->OMoneda("idcodigopostal", $cp, "TR.codigo_postal");
	}
	$xFRM->OButton("TR.BUSCAR COLONIA", "var xD=new DomGen();xD.getBuscarColonias()", $xFRM->ic()->BUSCAR);
	$xFRM->setValidacion("idcodigopostal","validacion.codigopostal", "", true);
	$xFRM->addHElem( $xTx3->getDeNombreDeColonia("idnombrecolonia", $nombrecolonia, "TR.Colonia" ) );
	

	$xFRM->addHElem( $xSel->getListaDeEntidadesFed("", true, $identidadfederativa)->get(true) );
	
	$xFRM->addHElem("<div class='tx4' id='txtmunicipio'></div>");
	$xFRM->addHElem("<div class='tx4' id='txtlocalidad'></div>");
	
	
	
	$xFRM->endSeccion();
}
	//$xFRM->addAviso("", "msg");
	
	
	if(PERSONAS_COMPARTIR_CON_ASOCIADA == true ){ 
		$xFRM->OButton("TR.Importar de Asociada", "jsaImportarDeAsociada", "importar");
		$xFRM->addFooterBar(" ");
	}
	
	
} else {
	$xFRM->addCerrar("", 3);
	$xFRM->addAtras();
}
echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
?>
<script>
var mSocio		= <?php echo  $persona; ?>;
var xG			= new Gen();

function initComponents(){ 
	$("#iddependencia").focus();
	if(PERSONAS_VIVIENDA_MANUAL == true){
		setTimeout("jsaGetMunicipios()", 1000);
		setTimeout("jsaGetLocalidades()", 1000);		
	}
}
function jsSetDomicilioPrevio(){
	var iddom = entero($("#idvivienda").val());
	if(iddom >0){
		xG.verDiv("idviviendadatos", false);
		$("#idnombreacceso").val("DOMICILIO CONOCIDO");
		$("#idnumeroexterior").val("CONOCIDO");
	} else {
		xG.verDiv("idviviendadatos", true);
		$("#idnombreacceso").val("");
		$("#idnumeroexterior").val("");
		
	}	
}
function jsGetDatosEmpresa(){
	var idEmp	= entero($("#iddependencia").val());
	if(validacion.empresa(idEmp) == false){
		//$("#idrazonsocialtrabajo").focus();
	} else { 
		jsaGetDatosEmpresa();
		//$("#idfechaingreso").focus();
	}
}
</script>
<?php $xHP->fin(); ?>
