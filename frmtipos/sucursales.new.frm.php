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
$xHP		= new cHPage("TR.AGREGAR SUCURSAL", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xLoc		= new cLocal();
$xValid		= new cTiposLimpiadores();


//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();


$xHP->init();


$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$clave 			= parametro("codigo_sucursal", null, MQL_RAW); $clave = parametro("id", $clave, MQL_RAW); $clave = parametro("clave", $clave, MQL_RAW);
$clave			= $xValid->cleanSucursal($clave);

$xSuc			= new cSucursal();
$xTabla			= new cGeneral_sucursales();

if($clave !== ""){
	$xTabla->setData( $xTabla->query()->initByID($clave));
}
$xTabla->setData($_REQUEST);



$persona		= parametro("idsocio", $xTabla->clave_de_persona()->v(), MQL_INT);
$gerente		= parametro("gerente_sucursal", $xTabla->gerente_sucursal()->v(), MQL_INT);
$cumplimiento	= parametro("titular_de_cumplimiento", $xTabla->titular_de_seguimiento()->v(), MQL_INT);
$iddomicilio 	= parametro("iddomicilio");
$step			= "";
//inicializar la persona
$xSoc				= new cSocio($persona);
$ODom				= null;

$xSel				= new cHSelect();
if($clave == null){
	$step			= MQL_ADD;
	$clave			= "";
	$xTabla->clave_de_persona(EACP_ID_DE_PERSONA);
	$xSoc			= new cSocio($xTabla->clave_de_persona()->v());
	if($xSoc->init() ==  true){
		$persona	= $xSoc->getClaveDePersona();
	}
	$idnummax		= $xQL->getContarDe("general_sucursales") + 1;
	$xTabla->clave_numerica($idnummax);
	$xTabla->caja_local_residente(0);
	
	
	$xSoc			= new cSocio($xTabla->clave_de_persona()->v());
	if($xSoc->init() ==  true){
		$persona	= $xSoc->getClaveDePersona();
		$ODom		= $xSoc->getODomicilio();
		if($ODom === null){
			$xTabla->calle( EACP_DOMICILIO_CALLE );
			$xTabla->codigo_postal( EACP_CODIGO_POSTAL );
			$xTabla->colonia( EACP_COLONIA );
			$xTabla->telefono( EACP_TELEFONO_PRINCIPAL );
			$xTabla->municipio( EACP_MUNICIPIO );
			$xTabla->localidad( EACP_LOCALIDAD );
			$xTabla->estado( EACP_ESTADO );
			$xTabla->numero_exterior( EACP_DOMICILIO_NUM_EXT );
			$xTabla->numero_interior( EACP_DOMICILIO_NUM_INT );
			
		} else {
			$xTabla->iddomicilio($ODom->getIDVivienda());
			
			$xTabla->calle( $ODom->getCalle() );
			$xTabla->codigo_postal( $ODom->getCodigoPostal() );
			$xTabla->colonia( $ODom->getColonia() );
			$xTabla->telefono( $ODom->getTelefonoFijo() );
			$xTabla->municipio( $ODom->getMunicipio() );
			$xTabla->localidad( $ODom->getLocalidad() );
			$xTabla->estado( $ODom->getEstado() );
			$xTabla->numero_exterior( $ODom->getNumeroExterior() );
			$xTabla->numero_interior( $ODom->getNumeroInterior() );
		}
	}
	
	
} else {
	if($action === SYS_NINGUNO){
		$step			= MQL_MOD;
		$xTabla->setData( $xTabla->query()->initByID($clave));
		
		$ODom		= new cPersonasVivienda(false, false, $xTabla->iddomicilio()->v());

		
		if($ODom->init() === true){
			$xTabla->calle( $ODom->getCalle() );
			$xTabla->codigo_postal( $ODom->getCodigoPostal() );
			$xTabla->colonia( $ODom->getColonia() );
			$xTabla->telefono( $ODom->getTelefonoFijo() );
			$xTabla->municipio( $ODom->getMunicipio() );
			$xTabla->localidad( $ODom->getLocalidad() );
			$xTabla->estado( $ODom->getEstado() );
			$xTabla->numero_exterior( $ODom->getNumeroExterior() );
			$xTabla->numero_interior( $ODom->getNumeroInterior() );
			
		} else {
			$xTabla->calle( EACP_DOMICILIO_CALLE );
			$xTabla->codigo_postal( EACP_CODIGO_POSTAL );
			$xTabla->colonia( EACP_COLONIA );
			$xTabla->telefono( EACP_TELEFONO_PRINCIPAL );
			$xTabla->municipio( EACP_MUNICIPIO );
			$xTabla->localidad( EACP_LOCALIDAD );
			$xTabla->estado( EACP_ESTADO );
			$xTabla->numero_exterior( EACP_DOMICILIO_NUM_EXT );
			$xTabla->numero_interior( EACP_DOMICILIO_NUM_INT );
			
		}
		
	}
}
$xFRM	= new cHForm("frmgeneral_sucursales", "sucursales.new.frm.php?action=$step");
$xFRM->setTitle($xHP->getTitle());


if($action == MQL_ADD){		//Agregar
	//$clave 		= parametro($xTabla->getKey(), null, MQL_RAW);
	
	
	
	if($clave !== ""){
		$xTabla->setData( $xTabla->query()->initByID($clave));
		$xTabla->setData($_REQUEST);
		
		$xTabla->codigo_sucursal( $clave );
		//modificar la parte de personas asociadas
		$xTabla->gerente_sucursal($gerente);
		$xTabla->titular_de_cumplimiento($cumplimiento);
		$xTabla->clave_de_persona($persona);
		$xTabla->iddomicilio($iddomicilio);
		
		if( $xTabla->caja_local_residente()->v()<= 0 ){
			//Agregar nueva Caja Local
			$xCL	= new cCajaLocal();
			$numcl	= setNoMenorQueCero($xTabla->clave_numerica()->v());
			$res	= $xCL->add($xTabla->nombre_sucursal()->v(), $numcl, false, $clave, $xTabla->codigo_postal()->v(), $xTabla->localidad()->v(), $xTabla->estado()->v(), $xTabla->municipio()->v() );
			
			if($res === false){
				$xTabla->caja_local_residente(DEFAULT_CAJA_LOCAL);
			} else {
				$xTabla->caja_local_residente($xCL->getClave());
			}
		}
		

		
		$res		= $xTabla->query()->insert()->save();
		$xFRM->setResultado($res);
		
		if($xTabla->iddomicilio()->v() > 0){
			$xSuc		= new cSucursal($clave);
			if($xSuc->init() == true){
				
				$xSuc->setIDDomicilio( $xTabla->iddomicilio()->v() );
			}
		}
		
		$xFRM->addCerrar();
	}
	
} else if($action == MQL_MOD){		//Modificar
	//iniciar
	//$clave 		= parametro($xTabla->getKey(), null, MQL_RAW);
	
	if($clave !== ""){
		
		$xTabla->setData( $xTabla->query()->initByID($clave));
		$xTabla->setData($_REQUEST);
		$xTabla->codigo_sucursal( $clave );
		//modificar la parte de personas asociadas
		$xTabla->gerente_sucursal($gerente);
		$xTabla->titular_de_cumplimiento($cumplimiento);
		$xTabla->clave_de_persona($persona);
		$xTabla->iddomicilio($iddomicilio);
		
		$res 	= $xTabla->query()->update()->save($clave);
		
		
		if($xTabla->iddomicilio()->v() > 0){
			
			$xSuc		= new cSucursal($clave);
			if($xSuc->init() == true){
				$xSuc->setIDDomicilio( $xTabla->iddomicilio()->v() );
			}
			
		}
		
		$xFRM->setResultado($res);
		$xFRM->addCerrar();
	}
} else {
	$xFRM->addGuardar();
	
	$xFRM->addSeccion("iddivgral", "TR.PERSONA");
	
	if($xTabla->clave_de_persona()->v() <= DEFAULT_SOCIO){
		$xFRM->addPersonaBasico("", false, $xTabla->clave_de_persona()->v(), "", "TR.Persona Vinculada");
	} else {
		$xFRM->addHElem( $xSoc->getFicha() );
	}
	$xFRM->endSeccion();
	$xFRM->addSeccion("iddivgral", "TR.DATOS_GENERALES");
	
	if($step == MQL_MOD){
		$xFRM->ODisabled_13("idxsucursal", $xTabla->codigo_sucursal()->v(), "TR.codigo sucursal");
		$xFRM->OHidden("codigo_sucursal", $clave);
		$xFRM->OHidden("clave", $clave);
		
	} else {
		$xFRM->OText_13("codigo_sucursal", $xTabla->codigo_sucursal()->v(), "TR.codigo sucursal");
	}
	
	
	
	$xFRM->OText("nombre_sucursal", $xTabla->nombre_sucursal()->v(), "TR.nombre de sucursal");
	$xFRM->OMoneda("clave_numerica", $xTabla->clave_numerica()->v(), "TR.clave en numero");
	//$xFRM->OMoneda("caja_local_residente", , "TR.caja_local", );
	if(SISTEMA_CAJASLOCALES_ACTIVA == false AND $step !== MQL_MOD){
		
		if($xTabla->caja_local_residente()->v() <= 0){
			$xFRM->OHidden("caja_local_residente", DEFAULT_CAJA_LOCAL);
		} else {
			$xFRM->OHidden("caja_local_residente", $xTabla->caja_local_residente()->v());
		}
	} else {
		$xFRM->addHElem( $xSel->getListaDeCajasLocales("caja_local_residente", false, $xTabla->caja_local_residente()->v() )->get(true));
	}
	$xSelDom		= $xSel->getListaDeDomicilioPorPers($persona, "iddomicilio", $xTabla->iddomicilio()->v());
	$hsel1			= $xSelDom->get(true);
	
	if($xSelDom->getCountRows()<=0){
		$xFRM->addTag("NO HAY DOMICILIO", "error");
		$xFRM->OHidden("iddomicilio", 0);
	} else {
		$xFRM->addHElem( $hsel1 );
	}
	
	$xFRM->OButton("TR.PANEL PERSONA", "var xP=new PersGen();xP.goToPanel(" . $xTabla->clave_de_persona()->v() . ")", $xFRM->ic()->PERSONA, "cmdpanelpers", "persona");
	$xFRM->OButton("TR.Agregar Referencias_Domiciliarias", "var xP= new PersGen();xP.setAgregarVivienda(" . $xTabla->clave_de_persona()->v() . ")", "vivienda", "cmdagregarvivienda" );
	
	
	$xFRM->setValidacion("iddomicilio", "validacion.nozero");
	
	$xFRM->endSeccion();
	
	$xFRM->addSeccion("iddivpersona", "TR.EMPLEADOS");
	//$xFRM->addPersonaBasico("2", false, $xTabla->gerente_sucursal()->v(), "", "TR.gerente_de_sucursal");
	//$xFRM->addPersonaBasico("3", false, $xTabla->titular_de_cumplimiento()->v(), "", "TR.Oficial_de_Cumplimiento");
	$xFRM->addHElem( $xSel->getListaDeUsuarios("gerente_sucursal", $xTabla->gerente_sucursal()->v())->get("TR.GERENTE_DE_SUCURSAL", true)) ;
	$xFRM->addHElem( $xSel->getListaDeUsuarios("titular_de_cumplimiento", $xTabla->titular_de_cumplimiento()->v())->get("TR.OFICIAL_DE_CUMPLIMIENTO", true));
	$xFRM->endSeccion();
	
	$xFRM->addSeccion("iddivhoras", "TR.HORARIO");
	
	$xFRM->addHElem($xSel->getListaDeHoras("hora_de_inicio_de_operaciones", $xTabla->hora_de_inicio_de_operaciones()->v(), true)->get("TR.inicio de operaciones", true) );
	$xFRM->addHElem( $xSel->getListaDeHoras("hora_de_fin_de_operaciones", $xTabla->hora_de_fin_de_operaciones()->v(), true)->get("TR.cierre de operaciones", true));
	
	$xFRM->endSeccion();
	
	if(MODULO_CONTABILIDAD_ACTIVADO == false AND $step !== MQL_MOD){
		$xFRM->OHidden("centro_de_costo", DEFAULT_CENTRO_DE_COSTO);
	} else {
		$xFRM->addHElem($xSel->getListaDeCentroDeCostoCont("centro_de_costo", $xTabla->centro_de_costo()->v())->get(true));
	}
	
	//$xFRM->OButton("TR.AGREGAR PERSONA", "jsAgregarPersonaNueva()", $xFRM->ic()->PERSONA, "add_new_persona", "persona");
	
	
	$xFRM->OHidden("mail", EACP_MAIL);
	$xFRM->OHidden("tel", EACP_TELEFONO_PRINCIPAL);
	
	$xFRM->OHidden("idsocio", $persona);
}


echo $xFRM->get();
?>
<script>
var xG	= new Gen();
var xP	= new PersGen();


function jsModificar(sucursal){
	xG.go({url: "frmtipos/sucursales.frm.php?id=" + sucursal})
}

function jsAgregarPersonaNueva(){
	
	
	var tel			= $("#tel").val();
	var mail		= $("#mail").val();
	var nombres		= $("#nombre_sucursal").val();

	xP.goToAgregarMorales({nombre:nombres,tipoingreso:Configuracion.personas.tipoingreso.otros,telefono:tel,email:mail});
}


</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>