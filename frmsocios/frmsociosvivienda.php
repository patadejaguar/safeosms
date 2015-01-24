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
function jsaImportarDeAsociada($persona){ 	$xSoc	= new cSocio($persona);	$xSoc->getImportarDesdeAsociada(TPERSONAS_DIRECCIONES); }

$jxc ->exportFunction('jsaGetMunicipios', array('identidadfederativa', 'idpais', 'idcodigopostal'), "#txtmunicipio");
$jxc ->exportFunction('jsaGetLocalidades', array('identidadfederativa', 'idmunicipio', 'idpais', 'idcodigopostal'), "#txtlocalidad");
$jxc ->exportFunction('jsaImportarDeAsociada', array('idsocio'), "#idmsg");
$jxc ->process();

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->init("initComponents()");

$xFRM		= new cHForm("frmvivienda", "frmsociosvivienda.php?action=2&persona=$persona");
$xBtn		= new cHButton(); $xTxtE = new cHText(); $xTX2 = new cHText(); $xDate		= new cHDate(); $xSel = new cHSelect(); $xChk	= new cHCheckBox();
$xTxt		= new cHText(); $xHSel	= new cHSelect(); $xTx3		= new cHText();

if($action == SYS_NINGUNO){
	
} else {
	//agregar
	$xSoc				= new cSocio($persona); $xSoc->init();
	$calle				= parametro("idnombreacceso");
	if(trim($calle) == ""){
		
	} else {
		
		$xCol				= null;
		$nexterior			= parametro("idnumeroexterior");
		$tipo_acceso		= parametro("idtipoacceso", "calle", MQL_RAW);
		$ninterior			= parametro("idnumerointerior");
		$referencia			= parametro("idobservaciones");
		$tresidencial		= parametro("idtelefono1");
		$tmovil				= parametro("idtelefono2");
		$principal			= parametro("idprincipal", false, MQL_BOOL);
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
		if(setNoMenorQueCero($idlocalidad) <= 0){
				$idlocalidad		= $xLoc->DomicilioLocalidadClave();
		}
		if($pais == EACP_CLAVE_DE_PAIS){
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
		} else {
			
		}
		$xSoc->addVivienda($calle, $nexterior, $cpostal, $ninterior,
				$referencia, $tresidencial, $tmovil,
				$principal, $regimen, $tdomicilio, $tiempo,
				$colonia, $tipo_acceso, "", $idlocalidad, $pais, $nombre_pais, $nombre_estado, $nombremunicipio, $nombrelocalidad);
		if(MODO_DEBUG == true){ 	$xFRM->addAviso( $xSoc->getMessages() );	}
		$xFRM->addAvisoRegistroOK();
		
	}
}

$xFRM->addSubmit();
$xFRM->OButton("TR.Importar de Asociadada", "jsaImportarDeAsociada", "importar");

$xFRM->addHElem( $xSel->getListaDeRegimenDeVivienda()->get(true) );
$xFRM->addHElem( $xSel->getListaDeTiposDeVivienda()->get(true) );
$xFRM->addHElem( $xSel->getListaDeTiempo()->get("TR.Tiempo_de_Residencia", true) );
$xFRM->addHElem( $xSel->getListaDePaises()->get(true) );

$xCP	= new cHText();
$xFRM->addHElem( $xCP->getNumero("idcodigopostal", $xLoc->DomicilioCodigoPostal(), "TR.codigo_postal" ));

$xFRM->addHElem( $xSel->getListaDeEntidadesFed("", true)->get(true) );

$xHSel->setEnclose(false);
$xHSel->addOptions( array( "calle" => "Calle", "avenida" => "Avenida", "andador" => "Andador", "camino_rural"=> "Camino Rural") );
$xTxtE->setDivClass("");
$xFRM->addDivSolo($xHSel->get("idtipoacceso", "", "TR.calle"), $xTxtE->getNormal("idnombreacceso", ""), "tx14", "tx34" );

$xFRM->addHElem( $xTxt->getNormal("idnumeroexterior", "", "TR.Numero_Exterior") );
$xFRM->addHElem( $xTxt->getNormal("idnumerointerior", "", "TR.Numero_Interior") );

$xFRM->addHElem( $xTx3->getDeNombreDeColonia("idnombrecolonia", EACP_COLONIA, "TR.Colonia" ) );

$xFRM->addHElem("<div class='tx4' id='txtmunicipio'></div>");
$xFRM->addHElem("<div class='tx4' id='txtlocalidad'></div>");

$xFRM->addHElem( $xTxt->getNumero("idtelefono1", "", "TR.TELEFONO_FIJO") );
$xFRM->addHElem( $xTxt->getNumero("idtelefono2", "", "TR.TELEFONO_MOVIL") );

$xFRM->addObservaciones();
$xFRM->addHElem( $xChk->get("TR.Domicilio Principal?", "idprincipal") );
$xFRM->addFootElement("<input type='hidden' id='idcolonia' name='idcolonia' value='' />");
$xFRM->addFootElement("<input type='hidden' id='idsocio' name='idsocio' value='$persona' />");

echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
?>
<script>
var mEdoAc	= <?php echo $xLoc->DomicilioEstadoClaveNum(); ?>;
var xGen	= new Gen();
var xVal	= new ValidGen();
$(document).ready(function () {
	$('#id-frmvivienda').isHappy({
	    fields: {
	      '#idcodigopostal': {
			required : true,
	        message: 'Necesita Capturar un Codigo postal',
			test : jsGetDatosHeredados
	      },
	      "#idnombreacceso" : {
		      required : true,
		      message : "Necesita una Nombre para la calle",
		      test : jsCheckCalle
	    }
	    }
	});	
});
function jsCheckCalle(){ return xVal.NoVacio($("#idnombreacceso").val()); }
function jsGetDatosHeredados(){
	var xPais	= ($("#idpais").length > 0) ? $("#idpais").val() : 0; //EACP_CLAVE_DE_PAIS
	if ($("#idcodigopostal").length > 0) {
		var cp		= $("#idcodigopostal").val();
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
		if (flotante(mdats.estado) > 0) {
			mEdoAc	= mdats.estado;
			$("#identidadfederativa").val(mEdoAc);
		
			setTimeout("jsaGetMunicipios()", 1000);
			setTimeout("jsaGetLocalidades()", 1000);
		}
	}
}

function initComponents(){  }
function jsSetEstadoPorPais(osrc){
	var mpais	= osrc.value;
	if(mpais != EACP_CLAVE_DE_PAIS){
		$("#identidadfederativa").val(98);
		$("#identidadfederativa").css("display", "none");
		jsaGetMunicipios();
		jsaGetLocalidades();		
	} else {
		$("#identidadfederativa").val(mEdoAc);
		$("#identidadfederativa").css("display", "inherit");		
	}
}
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>