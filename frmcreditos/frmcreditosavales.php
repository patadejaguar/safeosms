<?php
/**
 * Avales de creditos, forma de captura
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package creditos
 * @subpackage forms
 * 		22/07/2008	Funciones mejoradas de Datos heredados
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
//=====================================================================================================
$xHP		= new cHPage("TR.Avales de Credito");
$jxc 		= new TinyAjax();
$xF			= new cFecha();
$xLoc		= new cLocal();

$montorelacionado		= 0;
$porcentajerelacionado	= 1;
$documentorelacionado	= DEFAULT_CREDITO;
$fechaderelacion		= fechasys();


function jsaGetDatosHeredados($codigopostal, $idcolonia){
	$tab 		= new TinyAjaxBehavior();
	$xCol		= new cDomiciliosColonias();
	
	if ( setNoMenorQueCero($codigopostal) > 0){
		$xCol->getClavePorCodigoPostal($codigopostal);
	} else {
		if(setNoMenorQueCero($idcolonia) <= 0){
			$xLoc	= new cLocal();
			$xCol->getClavePorCodigoPostal($xLoc->DomicilioCodigoPostal());
		} else {
			$xCol->set($idcolonia);
		}
		
	}
	$tab->add(TabSetValue::getBehavior("idnombrecolonia", $xCol->getNombre() ));
	$tab->add(TabSetValue::getBehavior("idcolonia", $xCol->get() ));
	if(PERSONAS_VIVIENDA_MANUAL == true ){
		$tab->add(TabSetValue::getBehavior("idnombrelocalidad", $xCol->getNombreLocalidad() ));
		$tab->add(TabSetValue::getBehavior("idnombremunicipio", $xCol->getNombreMunicipio() ));
		//$tab->add(TabSetValue::getBehavior("idnombreestado", $xCol->getNombreEstado() ));
	} else {
		//$tab->add(TabSetValue::getBehavior("idlocalidad", $xCol->getNombreLocalidad() ));
		$tab->add(TabSetValue::getBehavior("identidadfederativa", $xCol->getClaveDeEstadoABC() ));		
	}
	//$tab->add(TabSetValue::getBehavior("idestado", $xCol->getClaveDeEstado() ));
	
	return $tab -> getString();
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

function jsaGetDatosDePersona($persona){
	$xSoc 			= new cSocio($persona);
	$xSoc->init();
	$sucess			= true;
	$telefono		= "";
	$domicilio		= "";
	$telefonomovil	= "";
	$nombre			= "";
	$appPaterno		= "";
	$appMaterno		= "";
	$NDia			= "";
	$NMes			= "";
	$NAnno			= "";
	$actividad		= "";
	
	/**
	 * Si el Numero de Socio es menor a 5 digitos
	 */
	$DDom 			= $xSoc->getDatosDomicilio();
	$telefono		= $xSoc->getTelefonoPrincipal();

	$nombre			= $xSoc->getNombre();
	$appPaterno		= $xSoc->getApellidoPaterno();
	$appMaterno		= $xSoc->getApellidoMaterno();
	$curp			= $xSoc->getCURP();
	$FNacimiento	= $xSoc->getFechaDeNacimiento();
	$DOcup			= $xSoc->getDatosActividadEconomica();
	$actividad		= $DOcup["puesto"];
	$socio			= $xSoc->getCodigo();
	
	$tab 			= new TinyAjaxBehavior();
	if ($sucess == true ){
		$tab->add(TabSetvalue::getBehavior("telefono", $telefono));
	
		$tab->add(TabSetvalue::getBehavior("nombre", $nombre));
		$tab->add(TabSetvalue::getBehavior("paterno", $appPaterno));
		$tab->add(TabSetvalue::getBehavior("materno", $appMaterno));
		$tab->add(TabSetvalue::getBehavior('curp', $curp));
	
		$tab->add(TabSetvalue::getBehavior("ocupacion", $actividad));
		$tab->add(TabSetvalue::getBehavior("ingreso", $xSoc->getIngresosMensuales()));
		//$tab->add(TabSetvalue::getBehavior("personarelacionada", $persona));
	
		// "idnombrecolonia" "identidadesfederativas"
		if($xSoc->getODomicilio() == null){
		
		} else {
			$tab->add(TabSetvalue::getBehavior("idnombrecolonia", $DDom["colonia"]));
			$tab->add(TabSetvalue::getBehavior("idnombreacceso", $DDom["calle"]));
			$tab->add(TabSetvalue::getBehavior("idnumeroexterior", $DDom["numero_exterior"]));
			$tab->add(TabSetvalue::getBehavior("idcodigopostal", $DDom["codigo_postal"]));
		
			if(PERSONAS_VIVIENDA_MANUAL == true ){
				$tab->add(TabSetvalue::getBehavior("idnombrelocalidad", $DDom["localidad"]));
				$tab->add(TabSetvalue::getBehavior("idnombremunicipio", $DDom["municipio"]));
			} else {
				
			}
		}
	}
	//$tab -> add(TabSetvalue::getBehavior('idObservaciones', $xSoc->getMessages() ));
	
	return $tab -> getString();
}
$jxc ->exportFunction('jsaGetDatosHeredados', array('idcodigopostal', 'idcolonia'));
$jxc ->exportFunction('jsaGetMunicipios', array('identidadfederativa', 'idpais', 'idcodigopostal'), "#txtmunicipio");
$jxc ->exportFunction('jsaGetLocalidades', array('identidadfederativa', 'idmunicipio', 'idpais', 'idcodigopostal'), "#txtlocalidad");

$jxc ->exportFunction('jsaGetDatosDePersona', array('personarelacionada'));

$jxc ->process();

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT); $credito = parametro("s", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

if($credito != DEFAULT_CREDITO){
	$xCred	= new cCredito($credito);
	$xCred->init();
	$documentorelacionado	= $credito;
	$montorelacionado		= $xCred->getMontoAutorizado();
	$persona				= $xCred->getClaveDePersona();
}

$xHP->init();
$xFRM		= new cHForm("frmrelaciones", "frmcreditosavales.php?action=2");
$xBtn		= new cHButton(); $xTxt = new cHText(); $xDate = new cHDate(); $xSel = new cHSelect(); $xTxN	= new cHText();
$xChk		= new cHCheckBox(); $xHSel = new cHSelect(); $xTxtE = new cHText(); $xTx3 = new cHText();  $xTX2 = new cHText();
$msg		= "";

$xFRM->addJsBasico();
$xFRM->addSubmit();

$xFRM->addHElem( $xSel->getListaDeTiposDeRelaciones("", 5)->get(true) ); //tipo 5 avales
$xFRM->addHElem( $xSel->getListaDeTiposDeParentesco()->get(true) );
$xFRM->addHElem( $xChk->get("TR.es dependiente_economico", "dependiente") );
$xTxN->addEvent( "getPersonasPorOrigen()", "onkeyup");
$xFRM->addHElem( $xTxN->getNormal("nombre", "", "TR.nombre") );
$xFRM->addHElem( $xTxN->getNormal("paterno", "", "TR.primer_apellido") );
$xFRM->addHElem( $xTxN->getNormal("materno", "", "TR.segundo_apellido") );

$xFRM->addHElem( $xDate->get("TR.Fecha de Nacimiento") );
$xFRM->addHElem( $xTxt->getNormal("curp", "", "TR.curp") );
$xFRM->addHElem( $xTxt->getNumero("telefono", "", "TR.Telefono") );
$xFRM->addHElem( $xTxt->getNormal("ocupacion", "", "TR.Ocupacion") );
$xFRM->addHElem( $xTxt->getNumero("ingreso", "", "TR.ingreso mensual") );

$xFRM->addHElem( $xTxt->getNumero("avalado", $montorelacionado, "TR.Monto avalado") );
//$xFRM->addHElem( "<hr />");


$xHSel->setEnclose(false);
$xHSel->addOptions( array( "calle" => "Calle", "avenida" => "Avenida", "andador" => "Andador", "camino_rural"=> "Camino Rural") );
$xTxtE->setDivClass("");
$xFRM->addDivSolo($xHSel->get("idtipoacceso", "", "calle"), $xTxtE->getNormal("idnombreacceso", ""), "tx14", "tx34" );

$xFRM->addHElem( $xTxt->getNormal("idnumeroexterior", "", "TR.Numero_Exterior") );
$xTX2->addEvent("jsaGetDatosHeredados()", "onblur");
$xFRM->addHElem( $xTX2->getNumero("idcodigopostal", DEFAULT_CODIGO_POSTAL, "TR.codigo_postal" ));

$xTx3->addEvent("getListaColoniasPorNombre()", "onkeyup");
$xFRM->addHElem( $xTx3->getNormal("idnombrecolonia", "", "TR.Colonia") );
$sentidades			= $xSel->getListaDeEntidadesFed();
$sentidades->addEvent("onblur", "jsaGetMunicipios");
$xFRM->addHElem( $sentidades->get(true) );
if(PERSONAS_VIVIENDA_MANUAL == true ){
	$xFRM->addHElem( $xTxt->getDeNombreDeMunicipio("idnombremunicipio", $xLoc->DomicilioMunicipio(), "TR.Municipio") );
	$xFRM->addHElem( $xTxt->getDeNombreDeLocalidad("idnombrelocalidad", $xLoc->DomicilioLocalidad(), "TR.localidad") );
} else {
	$xFRM->addHElem("<div class='tx4' id='txtmunicipio'></div>");
	$xFRM->addHElem("<div class='tx4' id='txtlocalidad'></div>");
}

$xFRM->OHidden("idpais", EACP_CLAVE_DE_PAIS, "TR.Pais");

//$xFRM->addHElem( "<hr />");
$xFRM->addObservaciones();

$xFRM->addFootElement("<input type='hidden' name='credito' id='credito' value='$credito' />");
$xFRM->addFootElement("<input type='hidden' name='personarelacionada' id='personarelacionada' value='' />");
$xFRM->addFootElement("<input type='hidden' id='idcolonia' name='idcolonia' value='' />");

if($action == SYS_DOS){
	$xSuc				= new cSucursal(getSucursal()); $xSuc->init();
	$cajalocal			= $xSuc->getCajaLocalResidente();
	$xCL				= new cCajaLocal($cajalocal);
	$xCL->init();
	$numeropropio		= parametro("personarelacionada", DEFAULT_SOCIO);
	$avalado			= parametro("avalado", 0, MQL_FLOAT);
	$depende			= parametro("dependiente", false, MQL_BOOL);
	$tiporelacion		= parametro("idtipoderelacion", DEFAULT_TIPO_RELACION );
	$tipoparentesco		= parametro("idtipodeparentesco", DEFAULT_TIPO_CONSANGUINIDAD);
	$observaciones		= parametro("idobservaciones");
	
	$xSoc 	= new cSocio($numeropropio);
	if($xSoc->existe($numeropropio) == true AND ($numeropropio != DEFAULT_SOCIO)) {
		$msg .= "WARN\tSE ENCONTRARON COINCIDENCIAS CON EL MISMO CODIGO, POR LO QUE SE CARGA LA CLAVE DE PERSONA\r\n";
		//$persona			= $xCL->getUltimoSocioRegistrado(true);
		//$xSoc->set($persona);
	} else {
		$nombre				= parametro("nombre");
		$materno			= parametro("materno");
		$paterno			= parametro("paterno");
		$curp				= parametro("curp");
		$rfc				= parametro("rfc", DEFAULT_PERSONAS_RFC_GENERICO);
		$fnacimiento		= parametro("idfecha-0", false);
		$fnacimiento		= ($fnacimiento == false) ? fechasys() : $xF->getFechaISO($fnacimiento);
		$lnacimiento		= $xLoc->DomicilioEstadoClaveABC();
		$tingreso			= TIPO_INGRESO_RELACION;
		$ecivil				= DEFAULT_ESTADO_CIVIL;
		$genero				= DEFAULT_GENERO;
		$regimenmat			= DEFAULT_REGIMEN_CONYUGAL;
		$figura				= FALLBACK_PERSONAS_FIGURA_JURIDICA;
		$gposol				= DEFAULT_GRUPO;
		
		$identicado_con		= FALLBACK_PERSONAS_TIPO_IDENTIFICACION;
		$documento_de_identificacion	= "NOIDENTIFICADO";
		$correo				= "";
		$movil				= parametro("telefono");
	
		//verificar si existe el socio , $correo, $dependientes, $fentrevista, AML_PERSONA_BAJO_RIESGO, $fiel, $pais
		$xSoc->add($nombre, $paterno, $materno,
				$rfc, $curp, $cajalocal,
				$fnacimiento, $lnacimiento,
				$tingreso, $ecivil,
				$genero, FALLBACK_CLAVE_EMPRESA, $regimenmat,
				$figura, $gposol, $observaciones,
				$identicado_con, $documento_de_identificacion, false, false,
				$movil
		);
		
		
	}
	$xSoc->init();
	$numeropropio	= $xSoc->getCodigo();
	$xSoc->getDatosDomicilio();
	//NO Hay domicilio, agregar
	if($xSoc->getODomicilio() == null ){
		$calle				= parametro("idnombreacceso");
		$nexterior			= parametro("idnumeroexterior");
		$tipo_acceso		= parametro("idtipoacceso", "calle");
		$cpostal			= parametro("idcodigopostal", $xLoc->DomicilioCodigoPostal());
		$ninterior			= parametro("idnumerointerior");
		$referencia			= parametro("idobservaciones");
		$tresidencial		= parametro("telefono");
		$tmovil				= parametro("telefono");
		$principal			= true;
		$regimen			= parametro("idregimendevivienda", DEFAULT_PERSONAS_REGIMEN_VIV);
		$tdomicilio			= parametro("idtipodevivienda", DEFAULT_PERSONAS_TIPO_VIV);
		$tiempo				= parametro("idtiempo", DEFAULT_TIEMPO);
		$localidad			= parametro("idnombrelocalidad", $xLoc->DomicilioLocalidad());
		$estado				= parametro("idnombremunicipio", $xLoc->DomicilioMunicipio());
		$colonia			= parametro("idnombrecolonia", $xLoc->DomicilioColonia());
		
		//if(PERSONAS_VIVIENDA_MANUAL == false ){
		$idlocalidad		= parametro("idlocalidad", $xLoc->DomicilioLocalidadClave());
		//}
		$pais				= parametro("idpais", EACP_CLAVE_DE_PAIS);
		
		$xSoc->addVivienda($calle, $nexterior, $cpostal, $ninterior,
				$referencia, $tresidencial, $tmovil,
				$principal, $regimen, $tdomicilio, $tiempo,
				$colonia, $tipo_acceso, "", $idlocalidad, $pais, "", $estado, $municipio, $localidad);
		$xSoc->init();
	}
	if($xSoc->getOActividadEconomica() == null){
		//Agregar datos economicos
		$tipo_ae 			= parametro("idactividad", FALLBACK_ACTIVIDAD_ECONOMICA);
		$sector_ae 			= parametro("idsectoreconomico", FALLBACK_SECTOR_ECONOMICO);
		$nombre_ae 			= parametro("idrazonsocial");
		$domicilio_ae 		= parametro("iddomiciliodeactividad");
		$localidad_ae 		= parametro("idlocalidad", $xLoc->DomicilioLocalidad());
		$municipio_ae 		= parametro("idmunicipio", $xLoc->DomicilioMunicipio());
		$estado_ae 			= parametro("idestado", $xLoc->DomicilioEstado());
		$telefono_ae 		= parametro("idtelefono");
		$extension_ae 		= parametro("idextension");
		$numero_empleado 	= parametro("idnumeroempleado");
		$antiguedad_ae 		= parametro("idantiguedad", DEFAULT_TIEMPO);
		$departamento_ae 	= parametro("iddepartamento");
		$montoper_ae 		= parametro("ingreso", 0, MQL_FLOAT);
		$dependencia_ae 	= parametro("iddependencia", FALLBACK_CLAVE_EMPRESA);
		$puesto 			= parametro("ocupacion");
		$nss 				= parametro("idnss");
		
		$fechaalta			= fechasys();
		$xSoc->addActividadEconomica($nombre_ae, $montoper_ae, $puesto, $antiguedad_ae, $dependencia_ae, $domicilio_ae, $localidad_ae, $municipio_ae, $estado_ae,
				$telefono_ae, $extension_ae, $numero_empleado, $departamento_ae, $tipo_ae, $sector_ae, false, $nss);
	}
	$xFRM->addHTML( $xSoc->getFicha() );
	//$lastpersona	= $xCL->getUltimoSocioRegistrado(true);
	//$xFRM->addPersonaComandos($persona);
	$xSocRel				= new cSocio($persona);
	$xSocRel->init();
	/*$numero_de_socio = FALLBACK_CLAVE_DE_PERSONA, $tipo_de_relacion = 99, $consanguinidad = 99,
			$depende = 0, $observaciones = "", $monto_relacionado = 0, $porcentaje_relacionado = 1, $fecha_de_alta = false*/
	$porcentajerelacionado	= ($avalado == 0 ) ? 1 : ($avalado / $montorelacionado);
	$montorelacionado		= ($avalado == 0) ? $montorelacionado : $avalado;
	
	$depende				= ($depende == true) ? 1 : 0;
	$xSocRel->addRelacion($numeropropio, $tiporelacion, $tipoparentesco, $depende, $observaciones, $montorelacionado, $porcentajerelacionado,  $fechaderelacion, $documentorelacionado);
	//if(MODO_DEBUG == true){ $msg .= $xSoc->getMessages();	}
	$xFRM->addAviso($msg);	
}

echo $xFRM->get();


$jxc ->drawJavaScript(false, true);
?>
<script>
function getListaColoniasPorCP(){
	var cp	= $("#idcodigopostal").val();
	$("#idcolonia").val("");
	$("#idmunicipio").val("");
	
	var xGen	= new Gen();
	if ( String(cp).length >= 3 ) {
	xGen.QList({
		url : "../svc/colonias.svc.php?cp=" + cp,
		id : "idcodigopostal",
		func : "setCodigoColonia",
		key : "codigo",
		label : "nombre"
	});
	}
}
function getListaColoniasPorNombre(){
	var cp	= $("#idnombrecolonia").val();
	$("#idcolonia").val("");
	$("#idmunicipio").val("");
	//$("#identidadfederativa").val();
	
	var xGen	= new Gen();
	if ( String(cp).length >= 3 ) {
	xGen.QList({
		url : "../svc/colonias.svc.php?n=" + cp + "&e=" + $("#identidadfederativa option:selected").text(),
		id : "idcodigopostal",
		func : "setCodigoColonia",
		key : "codigo",
		label : "nombre"
	});
	}
}
function setCodigoColonia(codigo){
	$("#idcolonia").val(codigo);
	jsaGetDatosHeredados();
	$("#idcodigopostal").qtip("hide");
}
function getPersonasPorOrigen(){
	var nm	= $("#nombre").val();
	var ap	= $("#paterno").val();
	var am	= $("#materno").val();

	
	var xGen	= new Gen();
	//if ( String(cp).length >= 3 ) {
	xGen.QList({
		url : "../svc/personas.svc.php?n=" + nm + "&p=" + ap + "&m=" + am,
		id : "nombre",
		func : "setCodigoPersona",
		key : "codigo",
		label : "nombrecompleto"
	});
	//}	
}
function setCodigoPersona(mId){
	$("#personarelacionada").val(mId);
	jsaGetDatosDePersona();
	$("#nombre").qtip("hide");
}
</script>
<?php
$xHP->fin();
?>