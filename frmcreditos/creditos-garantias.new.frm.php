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
$xHP		= new cHPage("TR.AGREGAR GARANTIA", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$xPTipoI	= new cPersonasTipoDeIngreso(0);

//$jxc 		= new TinyAjax();
//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$tipo_origen	= parametro("tipodeorigen", 0, MQL_INT); $tipo_origen = parametro("tipoorigen", $tipo_origen, MQL_INT); $tipo_origen = parametro("origen", $tipo_origen, MQL_INT);
$clave_origen	= parametro("claveorigen", 0, MQL_INT); $clave_origen = parametro("clavedeorigen", $clave_origen, MQL_INT); $clave_origen = parametro("idorigen", $clave_origen, MQL_INT);


$tipo_origen	= parametro("tipodeorigen", 0, MQL_INT); $tipo_origen	= parametro("tipoorigen", $tipo_origen, MQL_INT);
$clave_origen	= parametro("clavedeorigen", 0, MQL_INT); $clave_origen	= parametro("claveorigen", $clave_origen, MQL_INT);

$xHP->init();
$tipo			= parametro("tipo", 0, MQL_INT);
$xGar			= new cCreditosGarantias();
$xTabla			= new cCreditos_garantias();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xFRM			= new cHForm("frmgarantias", "creditos-garantias.frm.php?action=$action");
$xFRM->setTitle($xHP->getTitle());
$xSel			= new cHSelect();
$xTxt			= new cHText();
$xFRM->setNoAcordion();


if($credito <= DEFAULT_CREDITO){
	$xFRM->addCreditBasico();
	$xFRM->addSubmit();
} else {
	/* ===========		FORMULARIO EDICION 		============*/

	
	//Cargar datos del Credito
	$xCred		= new cCredito($credito);
	if($xCred->init() == true){
		$xFRM->addHElem( $xCred->getFichaMini() );
		$persona	= $xCred->getClaveDePersona();
		$xTabla->socio_garantia($persona);
		$xTabla->solicitud_garantia($credito);
		//$xTabla->tipo_origen($xCred->getTipoDeOrigen());
		//$xTabla->clave_origen($xCred->getClaveDeOrigen());
		
		$xSoc			= new cSocio($persona);
		if($xSoc->init() == true){
			$oDom		= $xSoc->getODomicilio();
			if($oDom !== null ){
				$existe	= $oDom->getExisteID($xTabla->domicilio_vinculado()->v());
				if($existe == false){
					$xTabla->domicilio_vinculado($oDom->getIDVivienda());
				}
			}
		}
		
	}
	if($clave <= 0){ //nuevo
		$xTabla->idcreditos_garantias("NULL");
		$xTabla->tipo_garantia($tipo);
		$xTabla->estatus_actual($xGar->ESTADO_COTEJO);
		$xTabla->eacp(EACP_CLAVE);
		$xTabla->sucursal(getSucursal());
		$xTabla->idusuario(getUsuarioActual());
		$xTabla->fecha_recibo($xF->get());
		$xTabla->fecha_resguardo($xF->getFechaMaximaOperativa());
		$xTabla->fecha_devolucion($xF->getFechaMaximaOperativa());
		$xTabla->idsocio_duenno($persona);
		$xTabla->tipo_origen($tipo_origen);
		$xTabla->clave_origen($clave_origen);
		//=========== Evalua si existe otras garantias
		$xTipoO	= new cCreditosDatosDeOrigen();
		if($tipo_origen == $xTipoO->ORIGEN_ARRENDAMIENTO){
			$xDOrg	= new cCreditosLeasing($clave_origen);
			if($xDOrg->init() == true){
				if($xDOrg->getClaveDeVehiculo() >0){
					$xFRM->addAvisoRegistroError("La garantia Existe");
					$xFRM->addCerrar("", 5);
				}
			}
		}
		
		if($tipo == $xGar->TIPO_PRENDARIA){
			$xTabla->tipo_valuacion($xGar->VALUADO_DOCTO);
		} else {
			$xTabla->tipo_valuacion($xGar->VALUADO_LEGAL);
		}
	}
	$xFRM->OHidden("idcreditos_garantias", $xTabla->idcreditos_garantias()->v());
	$xFRM->OHidden("socio_garantia", $xTabla->socio_garantia()->v());
	$xFRM->OHidden("solicitud_garantia", $xTabla->solicitud_garantia()->v());
	$xFRM->OHidden("tipo_garantia", $xTabla->tipo_garantia()->v());
	
	$xFRM->OHidden("fecha_devolucion", $xTabla->fecha_devolucion()->v());
	
	$xFRM->OHidden("sucursal", $xTabla->sucursal()->v());
	$xFRM->OHidden("eacp", $xTabla->eacp()->v());
	
	$xFRM->OHidden("estatus_actual", $xTabla->estatus_actual()->v());
	$xFRM->OHidden("fecha_resguardo", $xTabla->fecha_resguardo()->v());
	$xFRM->OHidden("idusuario", $xTabla->idusuario()->v());
	$xFRM->OHidden("fecha_recibo", $xTabla->fecha_recibo()->v());
	$xFRM->OHidden("observaciones_del_resguardo", $xTabla->observaciones_del_resguardo()->v());
	$xFRM->OHidden("idsocio_duenno", $xTabla->idsocio_duenno()->v());
	$xFRM->OHidden("propietario", $xTabla->propietario()->v());
	
	$xFRM->OHidden("tipo_origen", $xTabla->tipo_origen()->v());
	$xFRM->OHidden("clave_origen", $xTabla->clave_origen()->v());
	
	$xFRM->addSeccion("idgen", "TR.DATOS GENERALES");
	
	$xFRM->ODate("fecha_adquisicion", $xTabla->fecha_adquisicion()->v(), "TR.FECHA COMPRA");
	
	if($xCred->getEsArrendamientoPuro() == true AND $clave <= 0){
		$xGar		= new cCreditosGarantias();
		$xTabla->estado_presentado($xGar->COMO_NUEVO);
		$xTabla->tipo_valuacion($xGar->VALUADO_DOCTO);
		
		$xFRM->OHidden("estado_presentado", $xTabla->estado_presentado()->v());
		$xFRM->OHidden("tipo_valuacion", $xTabla->tipo_valuacion()->v());
		
		
		
		$xCot		= new cCreditosDatosDeOrigen(false, $xCred->getNumeroDeCredito());
		if($xCot->initByCredito($xCred->getNumeroDeCredito()) == true){
			
			$xOrg		= new cCreditosLeasing($xCot->getClaveDeOrigen());
			if($xOrg->init() == true){
				//$xOrg->initByCredito($xCred->getClaveDeCredito());
				$xTabla->caracteristica4($xOrg->getVehiculoAnnio());
				$xTabla->descripcion($xOrg->getVehiculoDescripcion());
				$xTabla->monto_valuado($xOrg->getVehiculoValor());
				$xTabla->extras($xOrg->getVehiculoExtras());
				$xTabla->marca($xOrg->getVehiculoMarca());
			}
		}
		
	} else {
		$xFRM->addHElem( $xSel->getListaDeTipoDeValuacionGar("tipo_valuacion", $xTabla->tipo_valuacion()->v())->get(true) );
		$xFRM->addHElem( $xSel->getListaDeEstadosDePatrimonioPersonal("estado_presentado", $xTabla->estado_presentado()->v())->get("TR.ESTATUS",true));
	}
	
	$xFRM->OText("descripcion", $xTabla->descripcion()->v(), "TR.DESCRIPCION");
	
	if($xTabla->tipo_garantia()->v() == $xGar->TIPO_PRENDARIA){
		$xFRM->OText_13("documento_presentado", $xTabla->documento_presentado()->v(), "TR.FACTURA");
	} else {
		$xFRM->OText_13("documento_presentado", $xTabla->documento_presentado()->v(), "TR.DOCUMENTO");
	}
	$xFRM->OMoneda("monto_valuado", $xTabla->monto_valuado()->v(), "TR.VALOR");
	
	$xFRM->endSeccion();
	//Automovil
	if($xTabla->tipo_garantia()->v() == $xGar->TIPO_PRENDARIA){
		$xFRM->addSeccion("iddp", "TR.DATOS DEL VEHICULO");
		$xFRM->OHidden("domicilio_vinculado", $xTabla->domicilio_vinculado()->v());
		
		$xFRM->addPersonaBasico("", false,$xTabla->idsocio_duenno()->v(), "jsActualizaProp()", "TR.PROVEEDOR");

		
		$xMarca	= $xSel->getListaDeVehiculosMarcas("marca", $xTabla->marca()->v());
		//$xMarca->addEspOption("0", "Ninguno");
		$xFRM->addHElem($xMarca->get(true));
		
		$xFRM->OText("extras", $xTabla->extras()->v(), "TR.EQUIPO EXTRA");

		$xFRM->OText_13("caracteristica3", $xTabla->caracteristica3()->v(), "TR.Color");
		$xFRM->OText_13("caracteristica4", $xTabla->caracteristica4()->v(), "TR.ANNIO");
		
		$xFRM->OText_13("caracteristica1", $xTabla->caracteristica1()->v(), "TR.NUMERO DE SERIE");
		$xFRM->OText_13("caracteristica2", $xTabla->caracteristica2()->v(), "TR.NUMERO DE MOTOR");
		
		$xFRM->OText_13("caracteristica52", $xTabla->caracteristica5()->v(), "TR.NUMERO DE PLACAS");
		
		
	} else {
		$xFRM->addSeccion("iddp", "TR.DATOS DEL INMUEBLE");
		
		$xFRM->addPersonaBasico("", false,$xTabla->idsocio_duenno()->v(), "jsActualizaProp()", "TR.PROPIETARIO");
		$xFRM->addHElem( $xSel->getListaDeDomicilioPorPers($xTabla->idsocio_duenno()->v(), "domicilio_vinculado", $xTabla->domicilio_vinculado()->v())->get("TR.UBICACION", true) );
		$xFRM->OHidden("marca", $xTabla->marca()->v());
		$xFRM->OHidden("extras", $xTabla->extras()->v());
		
		$xFRM->OText_13("caracteristica1", $xTabla->caracteristica1()->v(), "TR.IDESCRITURA");
		$xFRM->OText("caracteristica2", $xTabla->caracteristica2()->v(), "TR.NOMBRE DEL NOTARIO");
		$xFRM->OText_13("caracteristica3", $xTabla->caracteristica3()->v(), "TR.NUMERO DE NOTARIA");
		
		$xFRM->ODate("caracteristica4", $xTabla->caracteristica4()->v(), "TR.FECHA ESCRITURA");
	}
	
	$xFRM->OText("observaciones", $xTabla->observaciones()->v(), "TR.OBSERVACIONES");
	$xFRM->endSeccion();
	
	$xFRM->addDataTag("tipodepersona", $xPTipoI->TIPO_PROVEEDOR);
	
	
	$xFRM->addCRUD($xTabla->get(), true);
	//$xFRM->addCRUDSave($xTabla->get(), $clave, true);
}


echo $xFRM->get();
?>
<script>
var xPG = new PersGen();
function jsActualizaProp(){
	var id	= $("#idsocio").val();
	xPG.getNombre(id, 'propietario')
	$("#idsocio_duenno").val(id);
}
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>