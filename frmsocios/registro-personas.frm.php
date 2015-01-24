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
$xFRM					= new cHForm("frmnuevapersona");
$ready					= true;
$msg					= "";
$persona				= false;		//persona sin inicializar
$agregardom				= false;

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
$identidadfederativa 	= parametro("identidadfederativanacimiento", EACP_CLAVE_DE_ENTIDADFED);
$idlugardenacimiento 	= parametro("idlugardenacimiento");
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

	//verificar si es persona moral
	$xFJ				= new cPersonaFiguraJuridica($idfigurajuridica);
	if($xFJ->isFisica() == false){
		$idnombrecompleto	= $razonSocial;
		//rfc completo
		$idapellidopaterno	= "";
		$idapellidomaterno	= "";
	}
	$agregardom				= (trim($calle) == "") ? false : true;
	if(SISTEMA_CAJASLOCALES_ACTIVA == false) {
		$idcajalocal		= $xSuc->getCajaLocalResidente();
	}
	if(trim($idnombrecompleto . $idapellidopaterno) == ""){
		$msg			.= "ERROR\tNo existe la persona en alta\r\n";
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
					$idtelefono, $idemail, $iddependientes, $idfecharegistro, AML_PERSONA_BAJO_RIESGO, $idclavefiel, $idpais, $idregimenfiscal
		);
		//razones de no fiel ... como nota
		if($success == true){
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
					if(MODO_DEBUG == true){ $msg .= $xGrupo->getMessages(); }
				}
			}			
			//Agregar Domicilio si existe
			$persona		= $xSoc->getCodigo();
			$xFRM->addHTML( $xSoc->getFicha() );
			$lastpersona	= $xCL->getUltimoSocioRegistrado(true);
			$xFRM->addPersonaComandos($persona);
			$xFRM->addAvisoRegistroOK();
			//==================================== Nuevas Relaciones
			if($origen_relacion == false){
				$xTbl	= new cHTabla("idtblrels");$xHSel		= new cHSelect(); $xChk	= new cHCheckBox(); $xText	= new cHText(); $xText->setDivClass(""); $xChk->setDivClass("");
				$xBtn	= new cHButton(); $xUl		= new cHUl(); $li = $xUl->getO(); $li->setT("ul"); $li->setClass("tags blue");
				$li->add($xBtn->getBasic("TR.Guadar", "jsGuardarReferencia()", $xBtn->ic()->GUARDAR, "idguardar", false, true), "");
				$xTbl->initRow();
				$xTbl->addTD($xText->getDeNombreDePersona());
				$xTbl->addTD($xHSel->getListaDeTiposDeRelaciones("", "")->get("") );
				$xTbl->addTD($xHSel->getListaDeTiposDeParentesco()->get("")  );
				$xTbl->addTD($xChk->get("TR.es dependiente_economico", "dependiente") );
				$xTbl->addRaw("<td class='toolbar-24'>". $xUl->get() . "</td>" );
				$xTbl->endRow();
				$xFRM->addHTML("<h2>" . PERSONAS_TITULO_PARTES . "</h2>");
				$xFRM->addHTML( $xTbl->get() );
				$xFRM->addHTML('<div id="ListaDeRelaciones"></div>');
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
						$nombremunicipio	= $xCol->getNombreMunicipio();
						$nombrelocalidad	= $xCol->getNombreLocalidad();
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
						$nombremunicipio	= $xCol->getNombreMunicipio();
						$nombrelocalidad	= $xCol->getNombreLocalidad();
					} else {
						$colonia 			= (trim($colonia) == "") ? $xLoc->DomicilioCodigoPostal() : $colonia;
					}
				}
				$addDom	= $xSoc->addVivienda($calle, $nexterior, $cpostal, $ninterior,
						$referencia, $tresidencial, $tmovil,
						$principal, $regimen, $tdomicilio, $tiempo,
						$colonia, $tipo_acceso, "", $idlocalidad, $pais, $nombre_pais, $nombre_estado, $nombremunicipio, $nombrelocalidad);
				if($addDom == false){ $msg	.= "ERROR\tError al agregar el domicilio \r\n"; } else {
					$iddomicilio			= $xSoc->getIDDeVivienda();
				}
				
			}
		}
		if(setNoMenorQueCero($ingresos) > 0 AND setNoMenorQueCero($persona) > 0){
			$addAct			= $xSoc->addActividadEconomica($nombreempresa, $ingresos, "", DEFAULT_TIEMPO, FALLBACK_CLAVE_EMPRESA, $calle . "/" . $nexterior, $nombrelocalidad, $nombremunicipio, $nombre_estado,
					$tmovil, 0, 0, "", $idactividad, FALLBACK_SECTOR_ECONOMICO
					,$sucursal, "0", $cpostal, $idlocalidad
			);
			if($addAct == false){ $msg	.= "ERROR\tError al agregar la Actividad Economica \r\n"; }
		}
		
		if(setNoMenorQueCero($origen_relacion) > 0 AND setNoMenorQueCero($persona) > 0){
			$documentorelacionado	= parametro("iddocumentorelacionado", 0, MQL_INT);
			$personarelacionado		= parametro("idpersonarelacionado", 0, MQL_INT);
			$idtipoderelacion		= parametro("idtipoderelacion", 0, MQL_INT);
			$dependiente			= parametro("dependiente", false, MQL_BOOL);
			$idtipodeparentesco		= parametro("idtipodeparentesco", DEFAULT_TIPO_CONSANGUINIDAD, MQL_INT);

			if($origen_relacion == iDE_CREDITO){
				$xCred			= new cCredito($documentorelacionado);
				$xCred->init();
				$MontoAvalado	= $xCred->getMontoAutorizado();
				$addAval		= $xCred->addAval($persona, $MontoAvalado, $idtipoderelacion, $idtipodeparentesco, $dependiente, $idobservaciones);
				$msg			.= $xCred->getMessages();
			}
			
			//captacion
			//persona
			if($origen_relacion == iDE_SOCIO){
				if($personarelacionado > 0){
					$xPer	= new cSocio($personarelacionado);
					if($xPer->init() == true){
						$addRel	= $xPer->addRelacion($persona, $idtipoderelacion, $idtipodeparentesco, $dependiente, $idobservaciones);
					}
					$msg	.= $xPer->getMessages();
				} else {
					$msg	.= "ERROR\tError al agregar a la Relacion, no existe relacionado \r\n";
				}
			}
		}
		$xCat	= new cPersonasCatalogoOtrosDatos();
		//AGREGAR PEP
		if($espep == true AND setNoMenorQueCero($persona) > 0){
			$xSoc->addOtrosParametros($xCat->AML_PEP_PRINCIPAL, "1");
		}
		if($esextranjero == true AND setNoMenorQueCero($persona) > 0){
			$xSoc->addOtrosParametros($xCat->PERSONAS_ES_EXTRANJERO, "1");
		}		
		//agregar Relacion

		if(MODO_DEBUG == true){ $msg .= $xSoc->getMessages(); }
	} else {
		$xFRM->addCerrar();
	}
	$xFRM->addAviso($msg);
	
	$xFRM->addJQDates("");
	
	echo $xFRM->get();
	//--------------------------------- ACTUALIZA EL ULTIMO SOCIO EN LA CAJA LOCAL
?>
<link href="../css/jtable/lightcolor/orange/jtable.min.css" rel="stylesheet" type="text/css" />
<script src="../js/jtable/jquery-ui-1.8.16.custom.min.js" type="text/javascript"></script>
<script src="../js/jtable/jquery.jtable.js" type="text/javascript"></script>

<script>
var xPer	= new PersGen();

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
	var idxpersona		= "<?php echo setNoMenorQueCero($persona); ?>";
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

</script>	
<?php
echo $xHP->fin();
?>