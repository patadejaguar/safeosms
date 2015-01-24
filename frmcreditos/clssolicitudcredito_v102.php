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
$xHP					= new cHPage("Solicitud de Credito.- Modulo de Ingreso");
$xFecha					= new cFecha();

$oficial 				= elusuario($iduser);
$key 					= $_GET["s"];
//$valores
$socio 					= $_POST["idsocio"];
$solicitud 				= $_POST["idsolicitud"];
$tipoconvenio 			= $_POST["tipoconvenio"];
$periocidad 			= $_POST["periocidadpagos"];
$numeropagos 			= $_POST["numpagos"];
$rubro_destino 			= $_POST["destinocredito"];
$amp_destino 			= $_POST["ampliaciondestino"];
$observaciones 			= $_POST["observaciones"];
$monto_solicitado 		= $_POST["montosol"];
$contrato_corriente 	= isset($_POST["idcuenta"]) ? $_POST["idcuenta"] : DEFAULT_CUENTA_CORRIENTE;
$tipo_de_pago			= $_POST["tipo_de_pago"];

$key_process 			= md5($socio . $solicitud . ROTTER_KEY . date("Ymd") );
$str_orden 				= "";
$xBtn					= new cHButton();
$xFRM					= new cHForm("frmcreditoautorizado");
echo $xHP->getHeader(true);

//Correccciones
$contrato_corriente		= ($contrato_corriente == 0) ? DEFAULT_CUENTA_CORRIENTE : $contrato_corriente;
?>
<body>
<fieldset>
	<legend>Solicitud de Credito.- Modulo de Validacion</legend>
<?php
if ($key_process != $key){
	exit("DOCUMENTO NO VALIDADO");
} else {
	if($monto_solicitado <= 0){
		echo VJS_REGRESAR;
	}
	$fecha_solicitud 			= fechasys();
	$fecha_ministracion 		= $_POST["elanno1"] . "-" . $_POST["elmes1"] . "-" . $_POST["eldia1"];
	$fecha_vencimiento 			= $_POST["elanno0"] . "-" . $_POST["elmes0"] . "-" . $_POST["eldia0"];
	
	$fecha_u_mvto 				= $fecha_solicitud;
	$dias_solicitados 			= 0;
	$monto_parcialidad 			= 0;
	$tipo_de_calculo_interes	= 2;
	if(PERMITIR_EXTEMPORANEO == true){ $fecha_solicitud = $_POST["elanno98"] . "-" . $_POST["elmes98"] . "-" . $_POST["eldia98"]; }
	
	//Si es a Final de Plazo
	if($periocidad == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO){
		$dias_solicitados = restarfechas($fecha_vencimiento, $fecha_ministracion);
	} elseif($periocidad == CREDITO_TIPO_PERIOCIDAD_DIARIO){
		$dias_solicitados 		= restarfechas($fecha_vencimiento, $fecha_ministracion);
		$numeropagos			= $dias_solicitados - $xFecha->getDiasHabilesEnRango($fecha_vencimiento, $fecha_ministracion);
	} else	{	
		$dias_solicitados 		= $numeropagos * $periocidad;
		$fecha_vencimiento 		= sumardias($fecha_ministracion, $dias_solicitados);
	}
	$dias_autorizados 			= 0;
	$monto_autorizado 			= 0;
	$pagos_autorizados 			= 0;
	$saldo_actual 				= 0;

	$xConv						= new cProductoDeCredito($tipoconvenio);
	$dconv 						= $xConv->getDatosInArray();
	//Datos de los Creditos segun Productos
	$producto_monto_maximo 		= $dconv["maximo_otorgable"];
	$tipo_autorizacion 			= $dconv["tipo_autorizacion"];
	$docto_autorizacion 		= $dconv["leyenda_docto_autorizacion"];
	$interes_normal 			= $xConv->getTasaDeInteres();
	$interes_moratorio 			= $dconv["interes_moratorio"];
	$tipo_credito 				= $dconv["tipo_de_credito"];
	$estatus_pred 				= $dconv["estatus_predeterminado"];
	$nivel_riesgo 				= $dconv["nivel_riesgo"];
	$tasa_ahorro 				= $dconv["tasa_ahorro"];
	$tipo_de_calculo_interes	= $dconv["base_de_calculo_de_interes"];

		//Si es Automatizado
	if($tipo_autorizacion == CREDITO_TIPO_AUTORIZACION_AUTOMATICA){
		$monto_autorizado 		= $monto_solicitado;
		$dias_autorizados 		= $dias_solicitados;
		$pagos_autorizados 		= $numeropagos;
		//$saldo_actual = $monto_autorizado;
		//TODO: Acabar con este modulo
		$xFRM->addToolbar( $xBtn->getBasic($xHP->lang("Imprimir", "Orden de Desembolso"), "jsImprimirOrdenDeDesembolso()", "imprimir", "cmdprintdes", false) );
	}
	//----------------------------------------------------------------------
	//Regla: Si hay mas creditos, agregar notas a los demás creditos
	
	$interes_diario 				= ($monto_solicitado * $interes_normal) / EACP_DIAS_INTERES;
	
	if($periocidad != CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO){
		$monto_parcialidad			= ($monto_solicitado / $numeropagos) + ($interes_diario * $periocidad);
	}
	
	//-- socio
	$xSoc							= new cSocio($socio);
	$xSoc->init();
	//----- * Reestructura la Fecha de Vcto y los dias Autorizados * ------
	$dsoc							= $xSoc->getDatosInArray();
	$grupo_asociado					= $dsoc["grupo_solidario"];
	// ------------------ Distinguir si es Grupal, si lo es asociar la ultima planeacion al credito
	if($xConv->getEsProductoDeGrupos() == true) {
		/**
		 * Neutraliza el Recibo de Planeacion por socio
		 * Neutraliza las Operaciones de Planeacion por Grupo
		 */
		$fecha_esperar_hasta 		= restardias($fecha_solicitud, DIAS_ESPERA_CREDITO );

		$sqlURec 					= "UPDATE operaciones_recibos set docto_afectado=$solicitud
										WHERE numero_socio=$socio AND tipo_docto=14
										AND fecha_operacion>='$fecha_esperar_hasta' ";
		$sqlUMvto 					= "UPDATE operaciones_mvtos set docto_afectado=$solicitud WHERE grupo_asociado=$grupo_asociado
										AND tipo_operacion=112 AND fecha_operacion>='$fecha_esperar_hasta'";
		my_query($sqlURec);
		my_query($sqlUMvto);
	}
	$oficial_de_credito 				= $iduser;
	$oficial_de_seguimiento			= $iduser;
	if ( USE_OFICIAL_BY_PRODUCTO == true ){
		$oficial_de_credito 		= $dconv["oficial_seguimiento"];
		$oficial_de_seguimiento		= $dconv["oficial_seguimiento"];
	}
	//----------------------------------------------------------------------
	/* @param string $Observaciones
	 * @param integer $OficialDeCredito
	 * @param mixed $FechaDeSolicitud
	 * @param integer $TipoDePago
	 * @param integer $TipoDeCalculo
	 * @param float $TasaDeInteres*/
	$xCred		= new cCredito();
	$result		= $xCred->add($tipoconvenio, $socio,$contrato_corriente, $monto_solicitado, $periocidad, $numeropagos, $dias_solicitados, $rubro_destino, $solicitud,
		    $grupo_asociado, $amp_destino, $observaciones, $oficial_de_credito, $fecha_solicitud, $tipo_de_pago,
		    INTERES_POR_SALDO_INSOLUTO, false,  $fecha_ministracion);
	if($result == false){
			$xFRM->addToolbar($xBtn->getRegresar("frmsolicitudcredito_v102.php", true) );
			
			$xFRM->addAviso($xHP->lang(MSG_ERROR_SAVE));
			$xFL	= new cFileLog();
			$xFL->setWrite($xCred->getMessages(OUT_TXT));
			$xFL->setClose();
			$xFRM->addToolbar( $xFL->getLinkDownload("Log de eventos", "") );
			$xFRM->addAviso($xCred->getMessages(OUT_HTML));
	} else {
		$xCred->init();
		$xFRM->addHTML( $xCred->getFichaDeSocio() );
		$xFRM->addHTML( $xCred->getFicha() );
		$xFRM->addCreditoComandos($xCred->getNumeroDeCredito());
		$xFRM->addToolbar( $xBtn->getBasic("TR.Autorizar credito", "var CGen=new CredGen();CGen.getFormaAutorizacion($solicitud)", "imprimir", "cmdprintdes5", false) );
		$xFRM->addToolbar($xBtn->getBasic("TR.GENERAR PLAN_DE_PAGOS", "var CGen=new CredGen();CGen.getFormaPlanPagos($solicitud)", "reporte", "generar-plan", false ) );
	}
	echo $xFRM->get();
}
?>
</fieldset>
</body>
<script>
var xGen	= new Gen();
var CGen	= new CredGen();

var idcredito	= <?php echo $solicitud; ?>;
var idsocio	= <?php echo $socio; ?>;

function gogarantias() {  CGen.getFormaGarantias(idcredito); }
function goavales() {    CGen.getFormaAvales(idcredito); }
function goflujoefvo() { CGen.getFormaFlujoEfectivo(idcredito); }
function printsol() { CGen.getImprimirSolicitud(idcredito); }
function jsImprimirOrdenDeDesembolso(){ CGen.getImprimirOrdenDeDesembolso(idcredito);}

</script>
</html>