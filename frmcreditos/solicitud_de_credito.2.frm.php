<?php
/**
 * Titulo:		Solicitud de Credito.- Modulo de proceso
 * @since Actualizado:	12-Abril-2007
 * @author Responsable:	Balam Gonzalez Luis
 * @package creditos
 * @subpackage forms
 * @version 1.2
 * 		2008/07/22 Algunas correcciones de codigo
 * 		Recepta los Datos de los Creditos y los guarda
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
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
$xHP				= new cHPage("TR.Solicitud de Credito .- Modulo de Validacion");
$xHP->setNoCache();
$xFecha				= new cFecha();
$xCred				= new cCredito();

$msg				= "";
$persona			= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito			= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta				= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback			= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$periocidad			= parametro("idperiocidad", 0, MQL_INT);
$tipoconvenio 		= parametro("idproducto", 0, MQL_INT);
$numeropagos 		= parametro("idnumerodepagos", 0, MQL_INT);
$rubro_destino 		= parametro("iddestinodecredito", 0, MQL_INT);
$amp_destino 		= parametro("iddescripciondestino");
$observaciones 		= parametro("idobservaciones");
$monto_solicitado 	= parametro("idmonto", 0, MQL_FLOAT);
$contrato_corriente = $cuenta;
$tipo_de_pago		= parametro("idtipodepago", 0, MQL_INT);
$vencido			= parametro("idFechaVencimiento", false);
$ministrado			= parametro("idFechaMinistracion", false);
$solicitado			= parametro("idFechaSolicitud", false);
$TipoDeAutorizacion	= false;
$esrenovado			= parametro("idrenovado", false, MQL_BOOL);

$tieneprops			= parametro("idpropietario", false, MQL_BOOL);				//propietarios reales
$tieneprovs			= parametro("idproveedor", false, MQL_BOOL);				//proveedores de recursos

$idorigen			= parametro("idorigen",1, MQL_INT);
$origen				= parametro("origen",1, MQL_INT);
$TipoLugarCobro		= parametro("idtipolugarcobro",0, MQL_INT);

$oficial_de_credito	= parametro("oficial", getUsuarioActual(), MQL_INT);

$TasaDeInteres		= parametro("tasa",false, MQL_FLOAT);

$fecha_solicitud 	= $xFecha->getFechaISO($solicitado);
$fecha_ministracion = $xFecha->getFechaISO($ministrado);
$fecha_vencimiento 	= $xFecha->getFechaISO($vencido);

//$oficial_de_credito	= getUsuarioActual();
$xBtn				= new cHButton();
$xFRM				= new cHForm("frmcreditoautorizado");
$xFRM->setTitle($xHP->getTitle());
$xHP->init();
//================== Cargar datos del Presupuesto
switch ($origen){
	case $xCred->ORIGEN_ARRENDAMIENTO:
		$xLeas		= new cCreditosLeasing($idorigen);
		if($xLeas->init() == true){
			$TasaDeInteres	= $xLeas->getTasaInteres()+$xLeas->getTasaTiie();
		}
		break;
}

//================== Cargar datos de Arrendamiento puro

//Correccciones
$contrato_corriente	= (setNoMenorQueCero($contrato_corriente) <= 0) ? DEFAULT_CUENTA_CORRIENTE : $contrato_corriente;
$xSoc				= new cSocio($persona);
$arrDatos			= array(
						"periocidad_de_pago" => $periocidad,
						"tipo_de_producto" => $tipoconvenio,
						"numero_de_pagos" => $numeropagos,
						"contrato_corriente_relacionado" => $contrato_corriente,
						"fecha_de_ministracion" => $ministrado,
						"fecha_de_vencimiento" => $vencido,
						"monto_solicitado"		=> $monto_solicitado,
						"tipo_de_origen" => $origen,
						"clave_de_origen" => $idorigen
						);
$sucess				= true;
if($xSoc->isOperable() == true){
	$sucess			= $xSoc->setPrevalidarCredito($arrDatos);
	if($xSoc->getUUID() != $_SESSION[SYS_UUID]){
		$msg		.= "ERROR\tEl credito Validado no es el mismo que intenta guardar(" . $xSoc->getUUID() . "|" . $_SESSION[SYS_UUID] . ")\r\n";
		$sucess		= false;
	}
} else {
	$sucess		= false;
}
if($sucess == true){
	$grupo_asociado				= $xSoc->getClaveDeGrupo();
	if($periocidad == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO){
		$dias_solicitados 		= $xFecha->setRestarFechas($fecha_vencimiento, $fecha_ministracion);
	} elseif($periocidad == CREDITO_TIPO_PERIOCIDAD_DIARIO){
		$dias_solicitados 		= $xFecha->setRestarFechas($fecha_vencimiento, $fecha_ministracion);
		$numeropagos			= $dias_solicitados - $xFecha->getDiasHabilesEnRango($fecha_vencimiento, $fecha_ministracion);
	} else	{
		$dias_solicitados 		= $numeropagos * $periocidad;
		$fecha_vencimiento 		= $xFecha->setSumarDias($dias_solicitados, $fecha_ministracion);
	}
	$xConv				= new cProductoDeCredito($tipoconvenio); $xConv->init();
	if ( USE_OFICIAL_BY_PRODUCTO == true ){
		$oficial_de_seguimiento		= $xConv->getOficialDeSeguimiento();
	}	
	if($esrenovado == true){
		$TipoDeAutorizacion			= CREDITO_AUTORIZACION_RENOVADO;
		$msg						.= "WARN\tCredito marcado como Renovado \r\n";
	}
	//INTERES_POR_SALDO_INSOLUTO
	
	$result		= $xCred->add($tipoconvenio, $persona,$contrato_corriente, $monto_solicitado, $periocidad, $numeropagos, $dias_solicitados, $rubro_destino, false,
			$grupo_asociado, $amp_destino, $observaciones, $oficial_de_credito, $fecha_solicitud, $tipo_de_pago,
			$xConv->getTipoDeBaseCalc(), $TasaDeInteres,  $fecha_ministracion, $xSoc->getClaveDeEmpresa(), $TipoDeAutorizacion, $idorigen, $origen);
	if($result == false){
		$xFRM->addToolbar($xBtn->getRegresar("solicitud_de_credito.frm.php", true) );
		$xFRM->addAviso($xHP->lang(MSG_ERROR_SAVE));
		$xFL	= new cFileLog();
		$xFL->setWrite($xCred->getMessages(OUT_TXT));
		$xFL->setWrite($xSoc->getMessages());
		$xFL->setClose();
		$xFRM->addToolbar( $xFL->getLinkDownload("Log de eventos", "") );
		$xFRM->addAviso($xCred->getMessages());
	} else {
		if($TipoLugarCobro > 0){
			$xCred->setTipoDeLugarDeCobro($TipoLugarCobro, true);
		}
		$xCred				= new cCredito($xCred->getNumeroDeCredito());
		$xCred->init(); $credito	= $xCred->getNumeroDeCredito();
		//Si es Automatizado
		$xCat						= new cCreditosOtrosDatos();
		if($tieneprops == true){
			$xCred->setOtrosDatos($xCat->AML_CON_PROPIETARIO, "1");
		}
		if($tieneprovs == true){
			$xCred->setOtrosDatos($xCat->AML_CON_PROVEEDOR, "1");
		}
		if($xCred->getTipoDeAutorizacion() == CREDITO_TIPO_AUTORIZACION_AUTOMATICA){
			//$saldo_actual = $monto_autorizado;
			//TODO: Acabar con este modulo
			$xFRM->addToolbar( $xBtn->getBasic("TR.Imprimir Orden de Desembolso", "jsImprimirOrdenDeDesembolso()", "imprimir", "cmdprintdes", false) );
		}
		//----------------------------------------------------------------------
		
		$xFRM->addHTML( $xCred->getFichaDeSocio() );
		$xFRM->addHTML( $xCred->getFicha() );
		$xFRM->addCreditoComandos($xCred->getNumeroDeCredito(), $xCred->getEstadoActual());
		$xFRM->addCerrar();
		
		//$xFRM->addToolbar( $xBtn->getBasic("TR.Autorizar credito", "var CGen=new CredGen();CGen.getFormaAutorizacion($credito)", "imprimir", "cmdprintdes5", false) );
		//$xFRM->addToolbar($xBtn->getBasic("TR.GENERAR PLAN_DE_PAGOS", "var CGen=new CredGen();CGen.getFormaPlanPagos($credito)", "reporte", "generar-plan", false ) );
	}	
} else {
	$xFRM->addAtras();
}
	$msg		.= $xSoc->getMessages();
	$xFRM->addAviso($msg);
	echo $xFRM->get();

?>
<script>
var xGen	= new Gen();
var CGen	= new CredGen();

var idcredito	= <?php echo $credito; ?>;
var idsocio		= <?php echo $persona; ?>;

function gogarantias() {  CGen.getFormaGarantias(idcredito); }
function goavales() {    CGen.getFormaAvales(idcredito); }
function goflujoefvo() { CGen.getFormaFlujoEfectivo(idcredito); }
function printsol() { CGen.getImprimirSolicitud(idcredito); }
function jsImprimirOrdenDeDesembolso(){ CGen.getImprimirOrdenDeDesembolso(idcredito);}
</script>
<?php
	echo $xHP->end(); 
?>