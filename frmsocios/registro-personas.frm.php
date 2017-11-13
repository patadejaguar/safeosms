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
$xHP					= new cHPage("TR.Registro de Personas");
$xT						= new cTipos();
$xLoc					= new cLocal();
$xSel					= new cHSelect();
$xFRM					= new cHForm("frmnuevapersona");
$ready					= true;
$xLog					= new cCoreLog();
$xRuls					= new cReglaDeNegocio();
$xTipoIng				= new cPersonasTipoDeIngreso(0);
$xHP->setNoCache();

$DomicilioSimple		= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_RELS_DOM_SIMPLE);
$userNoDNI				= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_SIN_DNI_INGRESO);
$useDExtranjero			= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_USAR_DEXTRANJERO);
$useDatosAccidente		= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_USAR_DATO_ACCIDENTE);
$useDColegiacion		= $xRuls->getValorPorRegla($xRuls->reglas()->PERSONAS_USAR_DCOLEGIACION);
$persona				= false;																//persona sin inicializar
$agregardom				= false;

$xHP->addJTableSupport();
$xHP->init();

$idfecharegistro 		= parametro("idfecharegistro", false, MQL_DATE);
$idtipodeingreso 		= parametro("idtipodeingreso", DEFAULT_TIPO_INGRESO, MQL_INT);
$idfigurajuridica 		= parametro("idfigurajuridica", PERSONAS_FIGURA_FISICA, MQL_INT);
$idnombrecompleto 		= parametro("idnombrecompleto");
$idapellidopaterno 		= parametro("idapellidopaterno");
$idapellidomaterno 		= parametro("idapellidomaterno");
$idregimenfiscal 		= parametro("idregimenfiscal", DEFAULT_REGIMEN_FISCAL, MQL_INT);
$idgenero 				= parametro("idgenero", DEFAULT_GENERO, MQL_INT);
$idpais 				= parametro("idpaisdeorigen", EACP_CLAVE_DE_PAIS);
$idfechanacimiento 		= parametro("idfechanacimiento", false, MQL_DATE);
$identidadfederativanac	= parametro("identidadfederativanacimiento", EACP_CLAVE_DE_ENTIDADFED);
$identidadfederativa 	= parametro("identidadfederativa", EACP_CLAVE_NUM_ENTIDADFED);
$idlugardenacimiento 	= parametro("idlugardenacimiento", "");
$idestadocivil 			= parametro("idestadocivil", DEFAULT_ESTADO_CIVIL, MQL_INT);
$idregimenmatrimonial 	= parametro("idregimenmatrimonial", DEFAULT_REGIMEN_CONYUGAL);
$idtipoidentificacion 	= parametro("idtipoidentificacion", DEFAULT_TIPO_IDENTIFICACION);
$idnumerodocumento 		= parametro("idnumerodocumento");
$idemail 				= parametro("idemail", "", MQL_RAW);
$idtelefono 			= parametro("idtelefono", 0, MQL_INT);
$idcurp 				= parametro("idcurp");
$idrfc 					= parametro("idrfc");
$idclavefiel 			= parametro("idclavefiel");
$idrazonnofiel 			= parametro("idrazonnofiel");
$idobservaciones 		= parametro("idobservaciones");
$idcajalocal 			= parametro("idcajalocal", $xLoc->getCajaLocal() , MQL_INT);
$iddependientes 		= parametro("iddependientes", 0, MQL_INT);
$eacp 					= EACP_CLAVE;
$razonSocial			= parametro("idrazonsocial");
$descuento				= parametro("iddescuento",0, MQL_FLOAT);
$sucursal				= parametro("idsucursal", getSucursal(), MQL_RAW);
$xSuc					= new cSucursal($sucursal); $xSuc->init();
$gruposolidario			= parametro("idgrupo", DEFAULT_GRUPO , MQL_INT);
$gruposolidario			= (setNoMenorQueCero($gruposolidario) <= 0 ) ? DEFAULT_GRUPO : $gruposolidario;
$empresa				= parametro("empresa", FALLBACK_CLAVE_EMPRESA, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("idcodigodeempresas", $empresa, MQL_INT);
$empresa				= (setNoMenorQueCero($empresa)<= 0) ? FALLBACK_CLAVE_EMPRESA : $empresa;
$calle					= parametro("idnombreacceso");
$representante_legal	= parametro("idsocio2", false, MQL_INT);

$ingresos 				= parametro("idingresos", 0, MQL_FLOAT);
$idactividad			= parametro("idactividad", FALLBACK_ACTIVIDAD_ECONOMICA);
$nombreempresa			= parametro("idrazonsocialtrabajo", "");
$espep					= parametro("espep", false, MQL_BOOL);
$esextranjero			= parametro("esextranjero", false, MQL_BOOL);
$origen_relacion		= parametro("idorigenrelacionado", false, MQL_INT);
$iddireccionsimple		= parametro("iddomiciliosimple");
$profesion				= parametro("idprofesion");

$xclasificacion			= parametro("idxclasificacion", 0, MQL_INT);
$yclasificacion			= parametro("idyclasificacion", 0, MQL_INT);
$zclasificacion			= parametro("idzclasificacion", 0, MQL_INT);
$idregion				= parametro("idregionpersona", getRegion(), MQL_INT);
//=== Datos de personas morales
$idpoder				= parametro("idpodernotarial", "");
$fechapoder				= parametro("idfechapoder", $idfechanacimiento, MQL_DATE);
$idnotariopoder			= parametro("idnotariopoder", "");
$idnotariapoder			= parametro("ididnotariapoder", "");
$idfoliospoder			= parametro("idfoliospoder", "");

$idnotarioconst			= parametro("idnotarioconst", "");
$idnotariaconst			= parametro("ididnotariaconst", "");
$idfoliosconst			= parametro("idfolioconst", "");
//====== Datos de Origen
$tipoorigen				= parametro("tipoorigen",0, MQL_INT);
$claveorigen			= parametro("claveorigen",0, MQL_INT);
//===== Datos de Colegiacion
$idextranjeropermiso	= parametro("idextranjeropermiso");
$idextranjeroregistro	= parametro("idextranjeroregistro", false, MQL_DATE);
$idextranjerovencimiento= parametro("idextranjerovencimiento", false, MQL_DATE);
$idtipomembresia		= parametro("idtipomembresia",0, MQL_INT);
$iddiames				= parametro("iddiames", 0, MQL_INT);
$idtipolugarcobro		= parametro("idtipolugarcobro", 0, MQL_INT);
$idgradoacademico		= parametro("idgradoacademico",0, MQL_INT);
$iddatosemergencia		= parametro("iddatosemergencia");
$idcolegiacion			= parametro("idcolegiacion", "");
$idinterno				= parametro("idinterno", "");$idinterno	= parametro("claveinterna", $idinterno);
$omitirAML				= false;
//Datos de Relaciones
$idtipoderelacion		= parametro("idtipoderelacion", 0, MQL_INT);
$xTipoRels				= new cPersonasTiposDeRelacion($idtipoderelacion);
if($xTipoRels->init() == true){
	$omitirAML			= $xTipoRels->getOmitirAML();
}
//===================== Comandos Basicos
$xFRM->addCerrar();
$xFRM->addAtras();
$xFRM->setNoAcordion();
$xFRM->setTitle($xHP->getTitle());

//===================== Corregir CURP
if(trim($idcurp) == "" AND $idtipoidentificacion == PERSONAS_CLAVE_ID_POBLACIONAL){
	if(trim($idnumerodocumento)!= ""){$idcurp = $idnumerodocumento;}
}
//
$idlugardenacimiento	= ($identidadfederativanac != "") ? "$identidadfederativanac : $idlugardenacimiento " : "$idlugardenacimiento";
//verificar si es persona moral
$xFJ					= new cPersonaFiguraJuridica($idfigurajuridica);
	if($xFJ->isFisica() == false){
		$idnombrecompleto	= $razonSocial;
		//rfc completo
		$idapellidopaterno	= "";
		$idapellidomaterno	= "";
	}
	$agregardom				= (trim($calle) == "") ? false : true;
	if(SISTEMA_CAJASLOCALES_ACTIVA == false) {
		$idcajalocal		= $xSuc->getCajaLocalResidente();
	} else {
		$idcajalocal		= parametro("idcajalocal", $xSuc->getCajaLocalResidente(), MQL_INT);
	}
	if(trim($idnombrecompleto . $idapellidopaterno) == ""){
		$xLog->add("ERROR\tNo existe la persona en alta\r\n");
		$ready			= false;
	}
	if($ready == true){
		$xCL		= new cCajaLocal($idcajalocal);
		$xCL->init();
		$xSoc		= new cSocio(false);
		//verificar si existe el socio
		
		$success	= $xSoc->add($idnombrecompleto, $idapellidopaterno, $idapellidomaterno,
					$idrfc, $idcurp, $idcajalocal,
					$idfechanacimiento, $idlugardenacimiento,
					$idtipodeingreso, $idestadocivil,
					$idgenero, $empresa, $idregimenmatrimonial, $idfigurajuridica,

					$gruposolidario, 
				
					$idobservaciones,$idtipoidentificacion, $idnumerodocumento, false, $sucursal,
					$idtelefono, $idemail, $iddependientes, $idfecharegistro, AML_PERSONA_BAJO_RIESGO, $idclavefiel, $idpais, $idregimenfiscal, $profesion
		);
		
		//razones de no fiel ... como nota
		if($success == true){
			//Agregar ID Interna
			$xSoc->setIDInterno($idinterno);
			//Agregar Clasificaciones
			$xSoc->setClasificacionesExtras($xclasificacion, $yclasificacion, $zclasificacion);
			$xSoc->setRegion($idregion, true);
			if($useDatosAccidente == true AND $useDColegiacion == false){
				$idtipomembresia	= ($idtipomembresia <= 0) ? SYS_UNO : $idtipomembresia;
				$idtipolugarcobro	= ($idtipolugarcobro <= 0) ? SYS_UNO : $idtipolugarcobro;
				$iddiames			= ($iddiames <= 0) ? SYS_UNO : $iddiames;
				$idgradoacademico	= ($idgradoacademico <= 0) ? SYS_UNO : $idgradoacademico;
			}
			$xSoc->setDatosColegiacion($idtipomembresia, $idtipolugarcobro, $iddiames, $idgradoacademico, $iddatosemergencia, $idcolegiacion);
			$xSoc->setDatosExtranjero($idextranjeropermiso, $idextranjeroregistro, $idextranjerovencimiento);
			//TODO: Actualizar Datos de proveedor
			if($idtipodeingreso == $xTipoIng->TIPO_PROVEEDOR){
				$xSoc->setDatosDeProveedor();
			}
			
			if($descuento > 0){ $xSoc->setMontoAhorroPreferente($descuento);	}
			if($empresa != FALLBACK_CLAVE_EMPRESA){	$xSoc->setResetEmpresa($empresa);	}
			if(trim($idrazonnofiel)== ""){ } else { $xSoc->setRazonesDeNoFIEL($idrazonnofiel); }
			$xSoc->init();
			//agregar reprsentante legal
			$representante_legal	= setNoMenorQueCero($representante_legal);
			if( $representante_legal > DEFAULT_SOCIO ){
				$xSoc->addRepresentanteLegal($representante_legal);
			}
			//verificar si es grupo solidario
			if($xFJ->isFisica() == false AND $idtipodeingreso == TIPO_INGRESO_GRUPO ){
				//checar si existe
				if($xSoc->getOGrupoSol() == null){
					
				} else {
					$vocal			= parametro("idsocio3", false, MQL_INT);
					$xGrupo			= $xSoc->getOGrupoSol();
					$xGrupo->setVocal($vocal);
					$xGrupo->setRepresentante($representante_legal);

					$integrantes[]	= parametro("idsocio4", false, MQL_INT);
					$integrantes[]	= parametro("idsocio5", false, MQL_INT);
					$integrantes[]	= parametro("idsocio6", false, MQL_INT);
					$integrantes[]	= parametro("idsocio7", false, MQL_INT);
					$integrantes[]	= parametro("idsocio8", false, MQL_INT);
					$integrantes[]	= parametro("idsocio9", false, MQL_INT);
					foreach ($integrantes as $key => $idpersona){
						if(setNoMenorQueCero($idpersona) > DEFAULT_SOCIO){
							$xGrupo->addIntegrante($idpersona);
						}
					}
					$xLog->add($xGrupo->getMessages(), $xLog->DEVELOPER);
				}
			}
			//======= Agregar Datos de Constitucion
			if($xSoc->getEsPersonaFisica() == false){
				$xSoc->setDatosPersonasMorales($idfoliosconst, $idnumerodocumento, $idfechanacimiento, $idnotarioconst, $idnotariaconst, $idpoder, $fechapoder, $idnotariopoder, $idnotariapoder);
			}
			//Agregar Domicilio si existe
			$persona		= $xSoc->getCodigo();
			$xFRM->addHElem( $xSoc->getFicha() );
			$lastpersona	= $xCL->getUltimoSocioRegistrado(true);
			
			$xFRM->addPersonaComandos($persona, $xSoc->getOEventos()->REGISTRO);
			$xFRM->addAvisoRegistroOK();
			//==================================== Nuevas Relaciones
			if($origen_relacion == false){
				$xTDic	= new cHTablaDic();
				$xFRM->addHElem( $xTDic->getHGuardarRelacion($persona, "jsRefreshTable", $xSoc->getEsPersonaFisica()) );
				/*$xTbl	= new cHTabla("idtblrels");$xHSel		= new cHSelect(); $xChk	= new cHCheckBox(); $xText	= new cHText(); $xText->setDivClass(""); $xChk->setDivClass("");
				$xBtn	= new cHButton(); 
				$xUl		= new cHUl("idtools", "ul", "tags blue");
				$xUl->setTags("");
				$xUl->li($xBtn->getBasic("TR.Guadar", "jsGuardarReferencia()", $xBtn->ic()->GUARDAR, "idguardar", false, true));
				$xTbl->initRow();
				$xTbl->addTD($xText->getDeNombreDePersona());
				$xTbl->addTD($xHSel->getListaDeTiposDeRelaciones("", "")->get("") );
				$xTbl->addTD($xHSel->getListaDeTiposDeParentesco()->get("")  );
				$xTbl->addTD($xChk->get("TR.es dependiente_economico", "dependiente") );
				$xTbl->addRaw("<td class='toolbar-24'>". $xUl->get() . "</td>" );
				$xTbl->endRow();
				$xFRM->addHElem("<h2>" . PERSONAS_TITULO_PARTES . "</h2>");
				$xFRM->addHElem( $xTbl->get() );*/
				$xFRM->addHElem('<div id="ListaDeRelaciones"></div>');
			}
		}
		
		if($agregardom == true AND setNoMenorQueCero($persona) > 0){
			$xSoc->set($persona);
			if($xSoc->init() == true){
				$xCol				= null;
				$nexterior			= parametro("idnumeroexterior");
				$tipo_acceso		= parametro("idtipoacceso", "calle", MQL_RAW);
				$ninterior			= parametro("idnumerointerior");
				$referencia			= parametro("idreferencias");
				$tresidencial		= parametro("idtelefono1");
				$tmovil				= parametro("idtelefono2");
				$principal			= parametro("idprincipal", true, MQL_BOOL);
				$regimen			= parametro("idregimendevivienda", DEFAULT_PERSONAS_REGIMEN_VIV);
				$tdomicilio			= parametro("idtipodevivienda", DEFAULT_PERSONAS_TIPO_VIV);
				$tiempo				= parametro("idtiempo", DEFAULT_TIEMPO);
				$colonia			= parametro("idnombrecolonia", false);
				$nombremunicipio	= parametro("idnombremunicipio", "");
				$nombrelocalidad	= parametro("idnombrelocalidad", "");
				$idlocalidad		= parametro("idlocalidad", false, MQL_INT);
				$pais				= parametro("idpais", EACP_CLAVE_DE_PAIS);
				$idcolonia			= parametro("idcolonia", false, MQL_INT);
				$cpostal			= parametro("idcodigopostal", false, MQL_INT);
				$nombre_pais		= "";
				$nombre_estado		= "";
				$iddomicilio		= FALLBACK_DOMICILIO;
				
				if(setNoMenorQueCero($idlocalidad) <= 0){
					$idlocalidad		= $xLoc->DomicilioLocalidadClave();
				}
				if( setNoMenorQueCero($cpostal) > 0 ){
					$xCol					= new cDomiciliosColonias();
					if($xCol->existe($cpostal) == true){
						$pais				= EACP_CLAVE_DE_PAIS;
						$nombre_estado		= $xCol->getNombreEstado();
						$nombremunicipio	= ($nombremunicipio == "" OR PERSONAS_VIVIENDA_MANUAL == false) ? $xCol->getNombreMunicipio() : "";
						$nombrelocalidad	= ($nombrelocalidad == "" OR PERSONAS_VIVIENDA_MANUAL == false) ? $xCol->getNombreLocalidad() : "";
						if(trim($colonia) == ""){
							$colonia		= $xCol->getNombre();
						}
					}
				}
				if($colonia == false||$colonia == ""){
					if( setNoMenorQueCero($idcolonia) > 0 ){
						$xCol				= new cDomiciliosColonias($idcolonia); $xCol->init();
						$colonia			= $xCol->getNombre();
						$nombre_estado		= $xCol->getNombreEstado();
						$nombremunicipio	= ($nombremunicipio == "" OR PERSONAS_VIVIENDA_MANUAL == false) ? $xCol->getNombreMunicipio() : "";
						$nombrelocalidad	= ($nombrelocalidad == "" OR PERSONAS_VIVIENDA_MANUAL == false) ? $xCol->getNombreLocalidad() : "";
					} else {
						$colonia 			= (trim($colonia) == "") ? $xLoc->DomicilioCodigoPostal() : $colonia;
					}
				}

				if($identidadfederativa > 0 ){
					$xEstado			= new cDomiciliosEntidadFederal($identidadfederativa);
					$nombre_estado		= $xEstado->getNombre();
				}
				$addDom	= $xSoc->addVivienda($calle, $nexterior, $cpostal, $ninterior,
						$referencia, $tresidencial, $tmovil,
						$principal, $regimen, $tdomicilio, $tiempo,
						$colonia, $tipo_acceso, "", $idlocalidad, $pais, $nombre_pais, $nombre_estado, $nombremunicipio, $nombrelocalidad);
				if($addDom == false){ 
					$xLog->add("ERROR\tError al agregar el domicilio \r\n"); 
				} else {
					$iddomicilio			= $xSoc->getIDDeVivienda();
				}
				
			}
		}
		if(setNoMenorQueCero($ingresos) > 0 AND setNoMenorQueCero($persona) > DEFAULT_SOCIO){
			$addAct			= $xSoc->addActividadEconomica($nombreempresa, $ingresos, $profesion, DEFAULT_TIEMPO, FALLBACK_CLAVE_EMPRESA, $calle . "/" . $nexterior, $nombrelocalidad, $nombremunicipio, $nombre_estado,
					$tmovil, 0, 0, "", $idactividad, FALLBACK_SECTOR_ECONOMICO
					,$sucursal, "0", $cpostal, $idlocalidad
			);
			if($addAct == false){ $xLog->add("ERROR\tError al agregar la Actividad Economica \r\n"); }
		}
		//========================= Origen por Originacion de Creditos
		if($claveorigen >0){
		$xCred	= new cCredito();
			switch($tipoorigen){
				case $xCred->ORIGEN_PRECLIENTE:
					$xPreCred	= new cCreditosPreclientes($claveorigen);
					if($xPreCred->init() == true){
						$xPreCred->setPersona($persona);
					}
					$xLog->add($xPreCred->getMessages());
					break;
				case $xCred->ORIGEN_ARRENDAMIENTO:
					$xOrg		= new cCreditosLeasing($claveorigen);
					if($xOrg->init() == true){
						$xOrg->setPersona($persona);
					}
					$xLog->add($xOrg->getMessages());
					break;
			}
		}
		if(setNoMenorQueCero($origen_relacion) > 0 AND setNoMenorQueCero($persona) > DEFAULT_SOCIO){
			$documentorelacionado	= parametro("iddocumentorelacionado", 0, MQL_INT);
			$personarelacionado		= parametro("idpersonarelacionado", 0, MQL_INT);
			
			$dependiente			= parametro("dependiente", false, MQL_BOOL);
			$idtipodeparentesco		= parametro("idtipodeparentesco", DEFAULT_TIPO_CONSANGUINIDAD, MQL_INT);

			if($origen_relacion == iDE_CREDITO){
				$xCred			= new cCredito($documentorelacionado);
				$xCred->init();
				$MontoAvalado	= $xCred->getMontoAutorizado();
				$addAval		= $xCred->addAval($persona, $MontoAvalado, $idtipoderelacion, $idtipodeparentesco, $dependiente, $idobservaciones);
				$xLog->add($xCred->getMessages(), $xLog->DEVELOPER);
			}
			
			//captacion
			//persona
			if($origen_relacion == iDE_SOCIO){
				if($personarelacionado > 0){
					$xPer	= new cSocio($personarelacionado);
					if($xPer->init() == true){
						$addRel	= $xPer->addRelacion($persona, $idtipoderelacion, $idtipodeparentesco, $dependiente, $idobservaciones);
					}
					$xLog->add($xPer->getMessages(), $xLog->DEVELOPER);
				} else {
					$xLog->add("ERROR\tError al agregar a la Relacion, no existe relacionado \r\n");
				}
			}
			//ORIGEN del Credito
		}
		if(MODULO_AML_ACTIVADO == true ){
			$xCat	= new cPersonasCatalogoOtrosDatos();
			//AGREGAR PEP
			if($espep == true AND setNoMenorQueCero($persona) > DEFAULT_SOCIO){
				$xSoc->addOtrosParametros($xCat->AML_PEP_PRINCIPAL, "1");
			}
			if($esextranjero == true AND setNoMenorQueCero($persona) > 0){
				$xSoc->addOtrosParametros($xCat->PERSONAS_ES_EXTRANJERO, "1");
				$xSoc->setUpdate(array("nacionalidad_extranjera" => "1"), true);
				if($useDExtranjero == false){
					//================
					$xNot	= new cHNotif();
					$btn	= $xNot->getNoticon("","jsGuardarExtranjero()", $xFRM->ic()->GUARDAR);
					$xFRM->addSeccion("idivextranjeros", "TR.DATOS EXTRANJEROS", $btn);
					$xFRM->OText("idextranjeropermiso", "", "TR.PERMISO_DE_RESIDENCIA");
					$xFRM->ODate("idextranjeroregistro", false, "TR.EXTRANJERO_REGISTRO");
					$xFRM->ODate("idextranjerovencimiento", false, "TR.EXTRANJERO_VENCIMIENTO");
					$xFRM->addHElem( $xSel->getListaDeNacionalidad()->get(true));
					//NACIONALIDAD
					$xFRM->endSeccion();
				}				
			}
			//Actualizar Nivel de Riesgo
			if($omitirAML == false){ $xSoc->setAMLAutoActualizarNivelRiesgo(); }
			
		}
		//=================================== IR AL PANEL DE PERSONA
		$xHP->goToPageX("../frmsocios/socios.panel.frm.php?persona=$persona");
		//agregar Relacion

		$xLog->add($xSoc->getMessages(), $xLog->DEVELOPER);
		
	} else {
		$xFRM->addCerrar();
		$xFRM->addAvisoRegistroError();
	}
	if(MODO_DEBUG == true){
		$xFRM->addLog($xLog->getMessages());
	} else {
		$xFRM->addAviso($xLog->getMessages());
	}
	

	$xFRM->addJQDates("");
	
	echo $xFRM->get();
	//--------------------------------- ACTUALIZA EL ULTIMO SOCIO EN LA CAJA LOCAL
?>
<script>
var xPer	= new PersGen();
var xG		= new Gen();
var idxpersona		= "<?php echo setNoMenorQueCero($persona); ?>";
$(document).ready(function () {
	var idxpersona		= "<?php echo setNoMenorQueCero($persona); ?>";
	session(ID_PERSONA, entero(idxpersona));
	//alert(session(ID_PERSONA));
    $('#ListaDeRelaciones').jtable({
        title: 'Referencias',
        actions: {
            listAction: '../svc/referencias.svc.php?out=jtable&persona=' + idxpersona

        },
        fields: {
            clave: {
                key: true,
                list: false
            },
            tipo_de_relacion : {
                title: 'Relacion',
                width: '20%'
            },
            nombre_completo: {
                title: 'Nombre',
                width: '30%'
            },
            ocupacion: {
                title: 'Ocupacion',
                width: '20%'
            },
            domicilio: {
                title: 'Domicilio',
                width: '30%'
            }
        }
    });
    	
});
function jsGuardarReferencia(){
	
	var idpersona			= idxpersona;
	var idrelacionado		= $("#idpersona").val();
	var idtipoderelacion	= $("#idtipoderelacion").val();
	var idtipodeparentesco	= $("#idtipodeparentesco").val();
	var stat				= $('#depende').prop('checked');
	xPer.addRelacion({ persona : idpersona, relacionado : idrelacionado, tipo : idtipoderelacion, parentesco : idtipodeparentesco, depende : stat, callback : jsRefreshTable });
	$("#idpersona").val(0);
}
function jsRefreshTable(){ 
	$('#ListaDeRelaciones').jtable('load');
}
function onCloseVentanaRelaciones(){ jsRefreshTable(); }
function jsGuardarExtranjero(){
	xG.confirmar({msg:"CONFIRMA GUARDAR LOS DATOS_EXTRANJEROS", callback: jsSiGuardarExtranjero});
}
function jsSiGuardarExtranjero(){
	var idcarnet	= $("#idextranjeropermiso").val();
	var idfechaI	= $("#idextranjeroregistro").val();
	var idfechaF	= $("#idextranjerovencimiento").val();
	var idnal		= $("#idnacionalidad").val();
	xPer.addDatosExtr({persona:idxpersona, fechainicial:idfechaI, fechafinal:idfechaF,nacionalidad:idnal,documento:idcarnet});
}
</script>	
<?php
echo $xHP->fin();
?>