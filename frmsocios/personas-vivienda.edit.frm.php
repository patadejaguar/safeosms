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
$xHP		= new cHPage("TR.Datos de Vivienda", HP_FORM);
$jxc 		= new TinyAjax();
$xLoc		= new cLocal();
$xRuls		= new cReglaDeNegocio();
$xLog		= new cCoreLog();
$xViv		= new cPersonasVivienda();

$SinDetalleAcceso 		= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_SIN_DETALLE_ACCESO);		//regla de negocio
$clave					= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$calle					= parametro("idnombreacceso");
$nexterior				= parametro("idnumeroexterior");
$ninterior				= parametro("idnumerointerior");
$tipo_acceso			= parametro("idtipoacceso", "calle", MQL_RAW);
$referencia				= parametro("idobservaciones");
$tresidencial			= parametro("idtelefono1");
$tmovil					= parametro("idtelefono2");
$principal				= parametro("idprincipal", false, MQL_BOOL);
$regimen				= parametro("idregimendevivienda", DEFAULT_PERSONAS_REGIMEN_VIV);
$tdomicilio				= parametro("idtipodevivienda", DEFAULT_PERSONAS_TIPO_VIV);
$tiempo					= parametro("idtiempo", DEFAULT_TIEMPO);
$colonia				= parametro("idnombrecolonia", "");
$nombremunicipio		= parametro("idnombremunicipio");
$nombrelocalidad		= parametro("idnombrelocalidad");
$idlocalidad			= parametro("idlocalidad", 0, MQL_INT);
$pais					= parametro("idpais", EACP_CLAVE_DE_PAIS);
$idcolonia				= parametro("idcolonia", false, MQL_INT);
$cpostal				= parametro("idcodigopostal", false, MQL_INT);
$identidadfederativa 	= parametro("identidadfederativa", EACP_CLAVE_NUM_ENTIDADFED);

$VivExtranjera			= parametro("esextranjero", false, MQL_BOOL);
$seconstruye			= parametro("idconstruye", false, MQL_BOOL);

$idmunicipio 			= parametro("idmunicipio");


$persona				= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito				= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta					= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback				= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$nombre_pais			= "";
$nombre_estado			= "";
$isEdit					= false;


/* ===========        FORMULARIO EDICION         ============*/
$xTabla        = new cSocios_vivienda();
if($clave > 0){
	$xTabla->setData( $xTabla->query()->initByID($clave));
	$isEdit				= true;
} else {
	
	//$xTabla->idsocios_vivienda();
	$xTabla->socio_numero($persona);
	$xTabla->tipo_regimen($regimen);
	
	//$xTabla->colonia();
	//$xTabla->localidad();
	//$xTabla->estado();
	//$xTabla->municipio();
	//$xTabla->telefono_residencial();
	//$xTabla->telefono_movil();
	//$xTabla->nombre_de_pais();
	//$xTabla->referencia();
	//$xTabla->fecha_alta();
	//$xTabla->codigo();
	//$xTabla->fecha_de_verificacion();
	
	$xTabla->tiempo_residencia($tiempo);
	$xTabla->idusuario(getUsuarioActual());
	$xTabla->principal($principal);
	$xTabla->tipo_domicilio($tdomicilio);
	$xTabla->codigo_postal($xLoc->DomicilioCodigoPostal());
	$xTabla->sucursal(getSucursalPorPersona($persona));
	$xTabla->eacp(EACP_CLAVE);
	$xTabla->tipo_de_acceso($tipo_acceso);
	$xTabla->oficial_de_verificacion(getUsuarioActual());
	$xTabla->estado_actual($xViv->DOMICILIO_NO_VERIFICADO);
	$xTabla->clave_de_localidad($idlocalidad);
	$xTabla->clave_de_pais($pais);
	$xTabla->clave_de_municipio($idmunicipio);
	$xTabla->clave_de_entidadfederativa();
	$xTabla->construye($seconstruye);
	
}



function jsaGetMunicipios($identidadfederativa, $pais, $cp, $iddomicilio){
	$txt	= "";
	$text	= new cHText();
	$xSel	= new cHSelect();
	$text->setDivClass("");
	$mun	= false;
	
	if($iddomicilio > 0){
		$xViv	= new cPersonasVivienda($iddomicilio);
		if($xViv->init() == true){
			$mun					= $xViv->getClaveDeMunicipio();
			$identidadfederativa	= $xViv->getClaveDeEstado();
		}
	} else if(setNoMenorQueCero($cp) > 0){
		if($pais == EACP_CLAVE_DE_PAIS){
			$xCol		= new cDomiciliosColonias();
			$xCol->existe($cp);
			$mun		= $xCol->getClaveDeMunicipio();
			$txt		= $xCol->getNombreMunicipio();
		}
	}
	return ($pais != EACP_CLAVE_DE_PAIS OR PERSONAS_VIVIENDA_MANUAL == true) ? $text->getDeNombreDeMunicipio("idnombremunicipio", $txt, "TR.Municipio") : $xSel->getListaDeMunicipios("", $identidadfederativa, $mun)->get(false);
}
function jsaGetLocalidades($identidadfederativa, $municipio, $pais, $cp, $iddomicilio){
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
		$idlocalidad	= false;
		if($iddomicilio > 0){
			$xViv	= new cPersonasVivienda($iddomicilio);
			if($xViv->init() == true){
				$idlocalidad	= $xViv->getClaveDeLocalidad();
			}
		}
		$xS 		= $xSel->getListaDeLocalidades("", $identidadfederativa, $pais, $idlocalidad);
		$txt		= $xS->get(false);
		if($xS->getCountRows() <= 0){						//Corregir si no hay registros
			$text->setDivClass("");
			$txt	= $text->getDeNombreDeLocalidad("idnombrelocalidad", $v, "TR.Localidad");
		}
	}
	return $txt;
}
function jsaImportarDeAsociada($persona){
	$xSoc	= new cSocio($persona);
	$xSoc->getImportarDesdeAsociada(TPERSONAS_DIRECCIONES);
}

$jxc ->exportFunction('jsaGetMunicipios', array('identidadfederativa', 'idpais', 'idcodigopostal', 'idclave'), "#txtmunicipio");
$jxc ->exportFunction('jsaGetLocalidades', array('identidadfederativa', 'idmunicipio', 'idpais', 'idcodigopostal', 'idclave'), "#txtlocalidad");

$jxc ->exportFunction('jsaImportarDeAsociada', array('idsocio'), "#idmsg");
$jxc ->process();



$xHP->init("initComponents()");

$xFRM		= new cHForm("frmvivienda", "personas-vivienda.edit.frm.php?action=" . MQL_ADD . "&persona=$persona");
$xFRM->setTitle( $xHP->getTitle() );

$xBtn		= new cHButton();
$xTxtE 		= new cHText();
$xTX2 		= new cHText();
$xDate		= new cHDate();
$xSel 		= new cHSelect();
$xChk		= new cHCheckBox();
$xTxt		= new cHText();

$xHSel		= new cHSelect();
$xTx3		= new cHText();

$xSoc				= new cSocio($persona);
if($xSoc->init() == true){

	if($action == MQL_MOD){
		$ready				= false;
		if(trim($calle) == "" OR trim($nexterior) == ""){
			$xLog->add("ERROR\tEl domicilio debe tener al menos CALLE/ACCESO/DIRECCION y un NUMERO\r\n");
		} else {
			if(MODULO_AML_ACTIVADO == false AND $idlocalidad<= 0){
				$idlocalidad	= $xLoc->DomicilioLocalidadClave();
			}
			$xViv	= new cPersonasVivienda(false, false, $clave);
			$xViv->setID($clave);
			if($xViv->init() == true){
				$ready 	= $xViv->update($calle, $nexterior, $ninterior, $referencia, $tresidencial, $tmovil, $tipo_acceso, $colonia, $tdomicilio,
						$regimen, $tiempo, $principal, $cpostal, $idlocalidad, $pais, $nombrelocalidad, $nombremunicipio, $nombre_estado,
						$nombre_pais, false, false);
				if($ready == true){
					if($seconstruye == true){
						$xViv->setSeConstruye();
					} else {
						$xViv->setSeConstruye(false);
					}
					
				}
			}
			
			$xLog->add($xSoc->getMessages(), $xLog->DEVELOPER);
		}
		$xFRM->setResultado($ready, $xLog->getMessages(), $xLog->getMessages());
		
		$xFRM->addCerrar("", 5);
		

		
		
	} else if($action == MQL_ADD){
		//agregar
		$ready				= false;
		if(trim($calle) == "" OR trim($nexterior) == ""){
			$xLog->add("ERROR\tEl domicilio debe tener al menos CALLE/ACCESO/DIRECCION y un NUMERO\r\n");
		} else {
			if(MODULO_AML_ACTIVADO == false AND $idlocalidad<= 0){
				$idlocalidad	= $xLoc->DomicilioLocalidadClave();
			}
			
			$ready				= $xSoc->addVivienda($calle, $nexterior, $cpostal, $ninterior,	$referencia, $tresidencial, $tmovil,
					$principal, $regimen, $tdomicilio, $tiempo,
					$colonia, $tipo_acceso, "", $idlocalidad, $pais, $nombre_pais, $nombre_estado, $nombremunicipio, $nombrelocalidad);
			if($ready == true AND $seconstruye == true){
				
				$xViv->setID($xSoc->getIDDeVivienda());
				if($xViv->init() == true){ $xViv->setSeConstruye(); }
			}
			
			$xLog->add($xSoc->getMessages(), $xLog->DEVELOPER);
		}
		$xFRM->setResultado($ready, $xLog->getMessages(), $xLog->getMessages());
		
		$xFRM->addCerrar("", 5);
		//$xFRM->addAtras();
	} else {
		if($isEdit == true){
			$xFRM->setTitle($xHP->getTitle() . " # " . $clave);
			$xFRM->setAction("personas-vivienda.edit.frm.php?action=" . MQL_MOD . "&persona=$persona&clave=$clave", true);
			//$xFRM->addActualizar();
			$xFRM->addGuardar();
		} else {
			$xFRM->addGuardar();
		}
		$xFRM->OHidden("idclave", $clave);
		
		if(MODULO_AML_ACTIVADO == true OR $xSoc->getEsExtranjero() == true){
			$xFRM->OButton("TR.DOMICILIO_EXTRANJERO", "jsGoToPaisExtranjero()", $xFRM->ic()->GRUPO);
		}
		
		if(PERSONAS_COMPARTIR_CON_ASOCIADA ==true){
			$xFRM->OButton("TR.Importar de Asociada", "jsaImportarDeAsociada", "importar");
		}
		
		$xFRM->addHElem( $xSel->getListaDeRegimenDeVivienda("", $xTabla->tipo_regimen()->v())->get(true) );
		$xFRM->OHidden("idtipodevivienda", PERSONAS_REG_VIV_PROPIA);
		
		$xFRM->addHElem( $xSel->getListaDeTiempo("", $xTabla->tiempo_residencia()->getValor())->get("TR.Tiempo_de_Residencia", true) );
		//if($VivExtranjera == true){
		if(MODULO_AML_ACTIVADO == true OR $VivExtranjera == true){
			$xFRM->addHElem( $xSel->getListaDePaises("", $xTabla->clave_de_pais()->v())->get(true) );
		} else {
			$xFRM->OHidden("idpais", EACP_CLAVE_DE_PAIS);
		}
		
		$xCP	= new cHText();
		
		if(PERSONAS_VIVIENDA_MANUAL == true OR $VivExtranjera == true OR $clave > 0){
			$xFRM->addHElem( $xCP->getNumero("idcodigopostal", $xTabla->codigo_postal()->v(), "TR.codigo_postal" ));
		} else {
			$xFRM->addHElem( $xCP->getDeCodigoPostal("idcodigopostal", $xTabla->codigo_postal()->v() ));
		}
		$xFRM->OButton("TR.BUSCAR COLONIA", "var xD=new DomGen();xD.getBuscarColonias()", $xFRM->ic()->BUSCAR);
		$xFRM->setValidacion("idcodigopostal","validacion.codigopostal", "TR.Obligatorio codigo_postal", true);
		
		//Mostrar entidades primero
		if(PERSONAS_VIVIENDA_MANUAL == true){
			$xFRM->addHElem( $xSel->getListaDeEntidadesFed("", true, $xTabla->clave_de_entidadfederativa()->v())->get(true) );
		}
		
		$xHSel->setEnclose(false);
		//$xHSel->addOptions( array( "calle" => "Calle", "avenida" => "Avenida", "andador" => "Andador", "camino_rural"=> "Camino Rural") );
		$xTxtE->setDivClass("");
		if($SinDetalleAcceso == true){
			$xFRM->OText("idnombreacceso", $xTabla->calle()->v(), "TR.Domicilio");
			$xFRM->OHidden("idtipoacceso", $xTabla->tipo_de_acceso()->v());
			$xFRM->setValidacion("idnombreacceso", "validacion.calle", "TR.Obligatorio nombre de Acceso", true);
		} else {
			
			$xFRM->addDivSolo($xSel->getListaDeTiposDeAcceso("", $xTabla->tipo_de_acceso()->v())->get(false), $xTxtE->getNormal("idnombreacceso", $xTabla->calle()->v(), "TR.NOMBRE DEL ACCESO"), "tx14", "tx34" );
			$xFRM->setValidacion("idnombreacceso", "validacion.calle", "TR.Obligatorio nombre de Acceso", true);
		}
		$xTxt->setDiv13();
		$xFRM->OText_13("idnumeroexterior", $xTabla->numero_exterior()->v(), "TR.Numero_Exterior");
		$xFRM->OText_13("idnumerointerior", $xTabla->numero_interior()->v(), "TR.Numero_Interior");
		$xFRM->setValidacion("idnumeroexterior", "validacion.novacio", "TR.Obligatorio Numero_exterior", true);
		
		
		if(PERSONAS_VIVIENDA_MANUAL == true OR $VivExtranjera == true){
			$xFRM->addHElem( $xTx3->getDeNombreDeColonia("idnombrecolonia", $xTabla->colonia()->v(), "TR.Colonia" ) );
			if($VivExtranjera == true){
				$xFRM->OHidden("identidadfederativa", FALLBACK_CLAVE_ENTIDADFED);
			}
		} else {
			if($clave > 0){
				$xFRM->addHElem( $xTx3->getDeNombreDeColonia("idnombrecolonia", $xTabla->colonia()->v(), "TR.Colonia" ) );
			}
			$xFRM->addHElem( $xSel->getListaDeEntidadesFed("", true, $xTabla->clave_de_entidadfederativa()->v())->get(true) );
		}
		
		$xFRM->addHElem("<div class='tx4' id='txtmunicipio'></div>");
		$xFRM->addHElem("<div class='tx4' id='txtlocalidad'></div>");
		
		$xFRM->OTelefono("idtelefono1", $xTabla->telefono_residencial()->v(), "TR.TELEFONO_FIJO");
		$xFRM->OTelefono("idtelefono2", $xTabla->telefono_movil()->v(), "TR.TELEFONO_MOVIL");
		
		
		$xFRM->addObservaciones("", $xTabla->referencia()->v());
		
		$xEstat				= new cPersonasEstadisticas($persona);
		if($xEstat->getTotalDomicilios()<=0){
			$xFRM->OHidden("idprincipal", 1);
		} else {
			$xFRM->addHElem( $xChk->get("TR.Domicilio Principal ?", "idprincipal", $xTabla->principal()->v()) );
		}
		
		
		$xFRM->addHElem( $xChk->get("TR.Domicilio ENCONSTRUCCION ?", "idconstruye", $xTabla->construye()->v()) );
		
		
		$xFRM->addFootElement("<input type='hidden' id='idcolonia' name='idcolonia' value='' />");
		$xFRM->addFootElement("<input type='hidden' id='idsocio' name='idsocio' value='$persona' />");
		
		$xFRM->addJsInit("jsSiEsViviendaManual();");
		$vvExt	= ($VivExtranjera == true) ? "1" : "0";
		$xFRM->OHidden("esextranjero", $vvExt);
	}
	
} else {
	$xFRM->addAvisoRegistroError("La personas $persona no existe");
}
echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
?>
<script>
var mEdoAc		= <?php echo $xLoc->DomicilioEstadoClaveNum(); ?>;
var xGen		= new Gen();
var xVal		= new ValidGen();
var claveDom	= <?php echo ($clave >0 ) ? $clave : 0; ?>;

function jsLoadMunicipiosYLocalidades(){
	jsaGetMunicipios();
	xG.postajax("jsaGetLocalidades()");
}

function jsSiEsViviendaManual(){
	//carga los municipios de inicio
	if(PERSONAS_VIVIENDA_MANUAL == true){
		jsLoadMunicipiosYLocalidades();
		setLog("Es Vivienda Manual");
	}
	$("#idregimendevivienda").focus();	
}
function jsCheckCalle(){ return xVal.NoVacio($("#idnombreacceso").val()); }
function jsGetDatosHeredados(){
	var xPais		= ($("#idpais").length > 0) ? $("#idpais").val() : 0; //EACP_CLAVE_DE_PAIS
	if ($("#idcodigopostal").length > 1) {
		var cp		= entero($("#idcodigopostal").val());
		if( cp > 0){
			//si esl pais es diferente...
			if (xPais == EACP_CLAVE_DE_PAIS) {
				//buscar por codigo postal
				xGen.pajax({
					url : "../svc/colonias.svc.php?limit=1&cp=" + cp,
					finder : "codigo",
					result : "json",
					callback: setDatosPorCodigoPostal
					});
			}
		}
		return xVal.NoCero($("#idcodigopostal").val());
	}
	return true;
}

function setDatosPorCodigoPostal(obj) {
	for(mob in obj){
		var mdats	= obj[mob];
		if (entero(mdats.estado) > 0) {
			var mEdoAc	= entero(mdats.estado);
			setLog("Set datos por codigo postal");
			$("#identidadfederativa").val(mEdoAc);
			if(PERSONAS_VIVIENDA_MANUAL == false){
				jsLoadMunicipiosYLocalidades();
			}
		}
	}
}

function initComponents(){ 
	if(claveDom >0){
		var xDG = new DomGen(); xDG.getColoniasXCP(document.getElementById("idcodigopostal"));
		
		jsLoadMunicipiosYLocalidades();
		
	}
}
function jsSetEstadoPorPais(osrc){
	var mpais	= osrc.value;
	if(mpais != EACP_CLAVE_DE_PAIS){
		$("#identidadfederativa").val(98);
		$("#identidadfederativa").css("display", "none");
		setLog("Cargar Estados por Pais");
		if(PERSONAS_VIVIENDA_MANUAL == false){
			
			jsLoadMunicipiosYLocalidades();
		}		
	} else {
		$("#identidadfederativa").val(mEdoAc);
		$("#identidadfederativa").css("display", "inherit");		
	}
}
function jsGoToPaisExtranjero(){
	var urlExt	= '<?php echo "../frmsocios/personas-vivienda.edit.frm.php?action=ninguno&esextranjero=true&persona=$persona"; ?>';
	xGen.w({url:urlExt, tab:true});
}
</script>
<?php
$xHP->fin();
?>