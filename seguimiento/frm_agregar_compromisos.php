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
$xHP			= new cHPage("TR.Compromisos", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();

/**
 * Obtiene Parametros a traves de un explode
 */

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$monto		= parametro("idmonto",0, MQL_FLOAT);
$xHP->init();

$xFRM		= new cHForm("frmCompromisos", "frm_agregar_compromisos.php?action=" . MQL_ADD );
$xSel		= new cHSelect();
$msg		= "";
$anotacion	= "";
$hora		= "";
$compromiso	="promesa_de_pago";
$lugar		= parametro("idlugardecompromiso",99, MQL_INT);

$fecha		= $xF->get();
$oficial	= getUsuarioActual();

if($credito > DEFAULT_CREDITO){
	$xCred	= new cCredito($credito);
	if($xCred->init() == true){
		$persona	= $xCred->getClaveDePersona();
		$xFRM->addHElem($xCred->getFichaMini());
	}
}
$xFRM->setTitle($xHP->getTitle());
if($clave > 0){
	$xSeg			= new cSeguimientoCompromisos($clave);
	if($xSeg->init() == true){
		$xFRM->setAction("frm_agregar_compromisos.php?id=$clave&action=" . MQL_MOD);
		$credito	= $xSeg->getClaveDeCredito();
		$persona	= $xSeg->getClaveDePersona();
		if ($action == SYS_NINGUNO){ $xFRM->addHElem( $xSeg->getFicha() ); }
		$anotacion	= $xSeg->getNota();
		$fecha		= $xSeg->getFecha();
		$hora		= $xSeg->getHora();
		$oficial	= $xSeg->getOficial();
		$monto		= $xSeg->getMonto();
		$lugar		= $xSeg->getLugar();
	}
}
if ($action == SYS_NINGUNO){
	if($persona > DEFAULT_SOCIO AND $credito > DEFAULT_CREDITO){
		$xFRM->OHidden("credito",$credito);
		$xFRM->OHidden("persona",$persona);
	} else {
		$xFRM->addCreditBasico();
	}
	if($clave > 0){
		$xFRM->addActualizar();
	} else {
		$xFRM->addGuardar();
		if($credito > DEFAULT_CREDITO){
			//cargar datos de Deuda
			$xInt			= new cCreditosMontos($xCred->getNumeroDeCredito());
			$xInt->getOCredito($xCred->getDatosInArray());
			$xInt->setActualizarPorLetras();
			$capital		= $xInt->getCapitalPendiente();
			$interes		= $xInt->getInteresNormalPendiente();
			$moratorio		= $xInt->getInteresMoratorioPendiente();
			$otros			= $xInt->getOtros();
			$impuestos		= $xInt->getIVAPendiente();
			$monto			= $capital + $interes + $moratorio + $otros + $impuestos;		
		}
	}
	$xFRM->addHElem($xSel->getListaDeOficiales("idoficial", "", $oficial)->get(true) );
	$xFRM->addHElem($xSel->getListaDeTiposDeCompromisos("", $compromiso)->get(true));
	$xFRM->addHElem($xSel->getListaDeTiposDeCompromisosLugares("", $lugar)->get(true));
	
	$xFRM->addFecha($fecha);
	$xFRM->addHElem($xSel->getListaDeHoras("idhora", $hora) ->get(true) );
	$xFRM->addMonto($monto);
	$xFRM->setValidacion("idmonto", "jsValidarMonto", "TR.En PROMESA_DE_PAGO se requiere monto", true);
	$xFRM->OTextArea("idnotas", $anotacion, "TR.Notas");

} else {
	
	//Insertar Nuevo Registro
	$socio			= $persona;
	$solicitud		= $credito;
	$oficial		= parametro("idoficial", getUsuarioActual(), MQL_INT);
	$fecha			= parametro("idfechaactual", false, MQL_DATE);
	$fecha			= $xF->getFechaISO($fecha);
	$hora			= parametro("idhora");
	$compromiso		= parametro("idtipodecompromiso", "", MQL_RAW);
	$anotacion		= parametro("idnotas");
	$rs				= false;
	if($action == MQL_ADD){
		$xSeg		= new cSeguimientoCompromisos(false);
		$rs 		= $xSeg->addCompromiso($credito, $compromiso, $anotacion, $fecha, $oficial, $hora, $persona, $monto);
		$clave		= $xSeg->getClave();
	} else {
		$xSeg		= new cSeguimientoCompromisos($clave);
		if($xSeg->init() == true){
			$obj	= $xSeg->obj();
			$obj->anotacion($anotacion);
			$obj->fecha_vencimiento($fecha);
			$obj->oficial_de_seguimiento($oficial);
			$obj->hora_vencimiento($hora);
			$obj->monto_comprometido($monto);
			$obj->lugar_de_compromiso($lugar);
			
			$rs		= $obj->query()->update()->save($clave);
		}		
	}
	$xSeg->init();
	$xFRM->addHElem( $xSeg->getFicha() );
	
	//$xFRM->addAtras();
	$xFRM->setResultado($rs, "", "", true);
//} else if ($action == MQL_MOD){
	//actualizar Nuevo Registro

}
	
echo $xFRM->get();
?>
<script>
function jsValidarMonto(v){
	var mTipo	= $("#idtipodecompromiso").val();
	var res		= true;
	if(mTipo == "promesa_de_pago" && flotante(v) <= 0){ 
		res = false;
	}
	return res;
}
</script>
<?php
$xHP->fin();
?>