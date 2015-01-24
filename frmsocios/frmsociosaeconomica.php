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

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$empresa	= parametro("empresa", SYS_TODAS, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT);
$estado		= $xLoc->DomicilioEstadoClaveNum();
$msg		= (isset($_GET["msg"])) ? $_GET["msg"] : "";
//$tipo_ae	= (isset($_POST["idactividad"])) ? $_POST["idactividad"] : SYS_UNO;

$jxc 		= new TinyAjax();

function jsaSetDomicilioMismo($socio, $HDomicilio){
	$HDomicilio	= strtoupper($HDomicilio);
	
	if($HDomicilio == "MISMO"){
	$xSoc 			= new cSocio($socio);
	$xSoc->init();
	$DDom 		= $xSoc->getDatosDomicilio(99);
	$domicilio 	= $xSoc->getDomicilio();
	
	$cDom		= new cSocios_aeconomica();
	$cDom->setData($DDom);
	
	$telefono 	= $cDom->telefono_ae()->v();
	$estado		= $cDom->estado_ae()->v();
	$municipio	= $cDom->municipio_ae()->v();
	$localidad	= $cDom->localidad_ae()->v();
	
			$tab = new TinyAjaxBehavior();
			$tab -> add(TabSetValue::getBehavior('idtelefono', $telefono));
			$tab -> add(TabSetValue::getBehavior('identidadfederativa', $estado));
			$tab -> add(TabSetValue::getBehavior('idmunicipio', $municipio));
			$tab -> add(TabSetValue::getBehavior('idlocalidad', $localidad));
			$tab -> add(TabSetValue::getBehavior('iddomiciliodeactividad', $domicilio));
			
		return $tab -> getString();
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
	$tab = new TinyAjaxBehavior();
	
	$tab->add(TabSetValue::getBehavior('idtelefono', $telefono));
	//$tab->add(TabSetValue::getBehavior('iddomiciliodeactividad', $domicilio));
	$tab->add(TabSetValue::getBehavior('idrazonsocialtrabajo', $razon));
	$xSoc		= $Emp->getOPersona();
	//$xSoc		= new cSocio();
	if($xSoc == null){
	} else {
		$xOBA		= $xSoc->getOActividadEconomica();
		if($xOBA != null){

			$idactividad			= $xOBA->getClaveDeActividad();
			$idsector				= $xOBA->getClaveDeSector();
			$idlocalidad			= $xOBA->getClaveDeLocalidad();
			$idmunicipio			= $xOBA->getClaveDeMunicipio();
			$identidadfederativa	= $xOBA->getClaveDeEstado();
			// idlocalidad  idmunicipio identidadfederativa
			$tab->add(TabSetValue::getBehavior('idactividad', $idactividad));
			//$tab->add(TabSetValue::getBehavior('idsectoreconomico', $idsector));
			$tab->add(TabSetValue::getBehavior('idlocalidad', $idlocalidad));
			$tab->add(TabSetValue::getBehavior('identidadfederativa', $identidadfederativa));
			$tab->add(TabSetValue::getBehavior('idmunicipio', $idmunicipio));
			$tab->add(TabSetValue::getBehavior('idnombreacceso', $xOBA->getCalle()));
			$tab->add(TabSetValue::getBehavior('idcodigopostal', $xOBA->getCodigoPostal()));
		}
	}
	//TODO: cargar estado y municiopios
	return $tab -> getString();

}

function jsaImportarDeAsociada($persona){
	$xSoc	= new cSocio($persona);
	$xSoc->getImportarDesdeAsociada(TPERSONAS_ACTIVIDAD_ECONOMICA);
}

function jsaGetMunicipios($estado, $pais, $cp){
	$txt	= "";
	$text	= new cHText();
	$xSel	= new cHSelect();
	$text->setDivClass("");
	$mun	= false;
	if(setNoMenorQueCero($cp) > 0){
		if($pais == EACP_CLAVE_DE_PAIS){
			$xCol		= new cDomiciliosColonias();
			$xCol->existe($cp);
			$mun		= $xCol->getClaveDeMunicipio();
		}
	}
	return ($pais != EACP_CLAVE_DE_PAIS OR PERSONAS_VIVIENDA_MANUAL == true) ? $text->getDeNombreDeMunicipio("idnombremunicipio", "", "TR.Municipio") : $xSel->getListaDeMunicipios("", $estado, $mun)->get(false);
}
function jsaGetLocalidades($estado, $municipio, $pais, $cp){
	$xSel	= new cHSelect();
	$text	= new cHText();
	$txt	= "";
	$mun	= false;
	if(setNoMenorQueCero($cp) > 0){
		if($pais == EACP_CLAVE_DE_PAIS){
			$xCol		= new cDomiciliosColonias();
			$xCol->existe($cp);
			$mun		= $xCol->getClaveDeMunicipio();
			//$xCol->getNombreMunicipio();
		}
	}
	if(PERSONAS_VIVIENDA_MANUAL == true ){
		$text->setDivClass("");
		$txt	= $text->getDeNombreDeLocalidad("idnombrelocalidad", "", "TR.Localidad");
	} else {
		$xS 		= $xSel->getListaDeLocalidades("", $estado, $pais);
		$txt		= $xS->get(false);
		if($xS->getCountRows() <= 0){						//Corregir si no hay registros
			$text->setDivClass("");
			$txt	= $text->getDeNombreDeLocalidad("idnombrelocalidad", "", "TR.Localidad");
		}
	}
	return $txt;
}

$jxc ->exportFunction('jsaGetMunicipios', array('identidadfederativa', 'idpais', 'idcodigopostal'), "#txtmunicipio");
$jxc ->exportFunction('jsaGetLocalidades', array('identidadfederativa', 'idmunicipio', 'idpais', 'idcodigopostal'), "#txtlocalidad");

$jxc ->exportFunction('jsaSetDomicilioMismo', array('idsociorelacionado', 'iddomiciliodeactividad'));
$jxc ->exportFunction('jsaGetDatosEmpresa', array('iddependencia'));
$jxc ->exportFunction('jsaImportarDeAsociada', array('idsociorelacionado'), "#msg");

$jxc ->process();

$jsb	= new jsBasicForm("frmaeconomica", iDE_CREDITO);
$xFRM	= new cHForm("frmaeconomica", "frmsociosaeconomica.php?socio=$persona&action=" . MQL_ADD);
$xLog	= new cCoreLog();
$xBtn	= new cHButton();
$xTxt	= new cHText();
$xTxt2	= new cHText();
$xTxt3	= new cHText();
$xTxt4	= new cHText();
$xTxt5	= new cHText();
$xTxt6	= new cHText();
$xSel	= new cHSelect();
$xHSel	= new cHSelect();
$xTxtE = new cHText();

$xHP->init("initComponents()");

/* verifica si hay un dato */
$tipo_ae 			= parametro("idactividad", FALLBACK_ACTIVIDAD_ECONOMICA);
$sector_ae 			= parametro("idsectoreconomico", FALLBACK_SECTOR_ECONOMICO);
$nombre_ae 			= parametro("idrazonsocialtrabajo");
$domicilio_ae 		= parametro("iddomiciliodeactividad");
$localidad_ae 		= parametro("idnombrelocalidad", $xLoc->DomicilioLocalidad());
$idlocalidad 		= parametro("idlocalidad", $xLoc->DomicilioLocalidadClave(), MQL_INT);

$municipio_ae 		= parametro("idnombremunicipio", $xLoc->DomicilioMunicipio());
$idmunicipio 		= parametro("idmunicipio", $xLoc->DomicilioMunicipioClave(), MQL_INT);
$estado 			= parametro("identidadfederativa", $xLoc->DomicilioEstadoClaveNum(), MQL_INT ); //DEFAULT_NOMBRE_ESTADO
$telefono_ae 		= parametro("idtelefono");
$extension_ae 		= parametro("idextension");
$numero_empleado 	= parametro("idnumeroempleado");
$antiguedad_ae 		= parametro("idantiguedad", DEFAULT_TIEMPO);
$departamento_ae 	= parametro("iddepartamento");
$montoper_ae 		= parametro("idsalario", 0, MQL_FLOAT);
$empresa 			= parametro("iddependencia", FALLBACK_CLAVE_EMPRESA);
$puesto 			= parametro("idpuesto");
$nss 				= parametro("idnss");
$cp					= parametro("idcodigopostal", $xLoc->DomicilioCodigoPostal(), MQL_INT);
$fechaalta			= fechasys();
$idcolonia			= parametro("idcp_idcodigopostal", 0, MQL_INT);
$nombrecolonia		= parametro("dlidcodigopostal");
$nexterior			= parametro("idnumeroexterior");
$tipo_acceso		= parametro("idtipoacceso", "calle", MQL_RAW);
$calle				= parametro("idnombreacceso");
$estado_ae			= "";
$asalariado			= false;
$pais				= parametro("idpais", EACP_CLAVE_DE_PAIS, MQL_RAW);
$loaded				= false;

//Agregar
if(setNoMenorQueCero($persona) > DEFAULT_SOCIO){
	/* verifica si el socio o datos son validos */
	$xSoc					= new cSocio($persona);
	if($xSoc->init() == true){
		if( $action == MQL_ADD){
		
	
			//$estado_ae			= $xLoc->DomicilioEstado();
			if($pais == EACP_CLAVE_DE_PAIS){
				if($idcolonia > 1 AND $nombrecolonia == "" ){
					$xCol			= new cDomiciliosColonias($idcolonia);
					$xCol->set($idcolonia);
					//setLog("Colonia Cargada del id $idcolonia");
					if($xCol->init() == true){
						$nombrecolonia	= $xCol->getNombre();
						$localidad_ae	= $xCol->getNombreLocalidad();
						$municipio_ae	= $xCol->getNombreMunicipio();
						$localidad_ae	= $xCol->getNombreLocalidad();
						$idlocalidad	= $xCol->getClaveDeLocalidad();
						$cp				= $xCol->getCodigoPostal();
						$loaded			= true;
					}
				}
				if($cp > 1 AND $nombrecolonia == "" ){
					$xCol			= new cDomiciliosColonias($idcolonia);
					$xCol->getClavePorCodigoPostal($cp);
					//setLog("Colonia Cargada del id $idcolonia");
					if($xCol->init() == true){
						$nombrecolonia	= $xCol->getNombre();
						$idlocalidad	= $xCol->getClaveDeLocalidad();
						if(PERSONAS_VIVIENDA_MANUAL == false){
							$localidad_ae	= $xCol->getNombreLocalidad();
							$municipio_ae	= $xCol->getNombreMunicipio();
							$localidad_ae	= $xCol->getNombreLocalidad();
							
							$loaded			= true;
						}
					}
				}
					
			} else {
				if($idlocalidad > 0){
					$xDLoc		= new cDomicilioLocalidad($idlocalidad);
					if( $xDLoc->init() == true){
						$localidad_ae		= $xDLoc->getNombre();
						$estado_ae			= $xDLoc->getNombre();
					}
				}
			}
			
			//TODO: Terminar
			$success	= $xSoc->addVivienda($calle, $nexterior, $cp, "", "", $telefono_ae, "", false, PERSONAS_REG_VIV_NINGUNO, PERSONAS_TIPO_DOM_LABORAL, $antiguedad_ae, $nombrecolonia, PERSONAS_TIPO_ACCESO_CALLE, "",
					$idlocalidad, $pais, "",$estado_ae, $municipio_ae, $localidad_ae);
				if($success == true){
					$iddomicilio	= $xSoc->getIDDeVivienda();
					$xAE			= new cPersonaActividadEconomica($xSoc->getCodigo());
					if($empresa != FALLBACK_CLAVE_EMPRESA){
						$xAE->setEmpresa($empresa, $puesto, $departamento_ae, $numero_empleado, $nss, $extension_ae);
					}
					if($iddomicilio > 1){
						$xAE->setDomicilioVinculado($iddomicilio);
					}
					$success	= $xAE->add($tipo_ae, $montoper_ae, $antiguedad_ae, $nombre_ae, $cp, $telefono_ae, $idlocalidad);
					$msg		.= $xAE->getMessages();
				}

			
			if($success == true){
				$xFRM->addAvisoRegistroOK();
			} else {
				$xFRM->addAvisoRegistroError();
			}
		} else {
			$empresa			= $xSoc->getClaveDeEmpresa();
			
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
						$estado		= $DDomicilio->getClaveDeEstado();
						
						$localidad_ae 		= $DDomicilio->getCiudad();
						$idlocalidad 		= $DDomicilio->getClaveDeLocalidad();
						
						$municipio_ae 		= $DDomicilio->getMunicipio();
						$idmunicipio 		= $DDomicilio->getClaveDeMunicipio();
						$domicilio_ae		= $DDomicilio->getDireccionBasica();
						$cp					= $DDomicilio->getCodigoPostal();
						$calle				= $DDomicilio->getCalle();
						$nexterior			= $DDomicilio->getNumeroExterior();
					}
					$DActividad				= $OPersona->getOActividadEconomica();
					if( $DActividad != null){
						$tipo_ae		= $DActividad->getClaveDeActividad();
						$sector_ae		= $DActividad->getClaveDeSector();
					}
					//setLog( $OPersona->getMessages() );
				}
				$asalariado				= true;
			}
		
		}
	}
	if(MODO_DEBUG == true){	 $msg .= $xSoc->getMessages(OUT_TXT); $xFRM->addLog($msg);	}
}



$xTx3		= new cHText();
//$gssql= "SELECT * FROM socios_aeconomica_dependencias";
$cDE = $xSel->getListaDeEmpresas("iddependencia");// new cSelect("iddependencia", "iddependencia", $gssql);
$cDE->addEvent("onchange", "jsGetDatosEmpresa"); 
$cDE->addEvent("onblur", "jsGetDatosEmpresa");
$cDE->setEsSql();
$cDE->setOptionSelect($empresa);

$xFRM->addHElem($cDE->get("TR.Empresa Relacionada", true) );

$xFRM->addHElem($xTxt2->get("idrazonsocialtrabajo", $nombre_ae, "TR.Nombre_Comercial / razon_social"));


$xFRM->addHElem( $xTxt6->getDeActividadEconomica("idactividad", $tipo_ae, "TR.Clave de Actividad") );
$xTxt->setClearEvents();

$xHSel->setEnclose(false);
$xHSel->addOptions( array( "calle" => "Calle", "avenida" => "Avenida", "andador" => "Andador", "camino_rural"=> "Camino Rural") );
$xTxtE->setDivClass("");
$xFRM->addDivSolo($xHSel->get("idtipoacceso", "TR.calle", $tipo_acceso), $xTxtE->getNormal("idnombreacceso", $calle), "tx14", "tx34" );

$xFRM->OText("idnumeroexterior", $nexterior, "TR.Numero_Exterior");
$xFRM->addHElem($xTxt->getDeMoneda("idtelefono", "TR.Telefono", $telefono_ae));
$xFRM->addHElem($xTxt->getDeMoneda("idextension", "TR.Extension", ""));



$xCP	= new cHText();
$xFRM->addHElem( $xCP->getNumero("idcodigopostal", $xLoc->DomicilioCodigoPostal(), "TR.codigo_postal" ));
$xFRM->addHElem( $xTx3->getDeNombreDeColonia("idnombrecolonia", EACP_COLONIA, "TR.Colonia" ) );

$xFRM->addHElem( $xSel->getListaDePaises()->get(true) );
$xFRM->addHElem( $xSel->getListaDeEntidadesFed("", true)->get(true) );


$xFRM->addHElem("<div class='tx4' id='txtmunicipio'></div>");
$xFRM->addHElem("<div class='tx4' id='txtlocalidad'></div>");

$xTxt->setClearProperties();
if( $asalariado == true ){
	$xFRM->OText("idpuesto", $puesto, "TR.Cargo");
	$xFRM->OText("iddepartamento", $departamento_ae, "TR. Departamento");
	$xFRM->OText("idnumeroempleado", $numero_empleado, "TR.Clave de Empleado");
	$xFRM->OText("idnss", $nss, "TR.ID_DE_SEGURIDADSOCIAL");
} else {
	
}

$xFRM->addHElem($xSel->getListaDeTiempo("idantiguedad")->get("TR.Tiempo en el Puesto", true));
$xFRM->addHElem($xTxt->getDeMoneda("idsalario", "TR.Ingreso Mensual", $montoper_ae));

$xFRM->OHidden("idsociorelacionado", $persona);

$xFRM->addAviso("", "msg");
$xFRM->addSubmit("", "frmSubmit(true)");
$xFRM->OButton("TR.Importar de Asociada", "jsaImportarDeAsociada", "importar");

echo $xFRM->get();

$jsb->show();
$jxc ->drawJavaScript(false, true, $estado);
?>
<script>
	var tform 		= document.frmaeconomica;
	var mSocio		= <?php echo  $persona; ?>;
	var nGen		= new Gen();
	function initComponents(){ $("#iddependencia").focus(); }
	function jsGetDatosEmpresa(){
		if( entero($("#iddependencia").val()) == FALLBACK_CLAVE_EMPRESA){
			$("#idrazonsocialtrabajo").focus();
		} else { 
			jsaGetDatosEmpresa();
			$("#idpuesto").focus();
		}
	}
</script>
<?php $xHP->end(); ?>
