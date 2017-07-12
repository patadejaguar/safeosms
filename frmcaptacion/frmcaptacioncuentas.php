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
$xHP		= new cHPage("TR.Registro de Cuentas de Captacion");
$xF			= new cFecha();
$xFRM		= new cHForm("frmcaptacion", "./");
$xBtn		= new cHButton();
$xTxt		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();
$msg		= "";

//-------------------------------------------------------------
$jxc = new TinyAjax();
function jsaGetValidacion($persona, $producto, $origen){
	$html	= "";
	$xPd	= new cCaptacionProducto($producto);
	if($xPd->getClase() == CAPTACION_TIPO_PLAZO){
		$html	.= $xPd->getListaDeDias();
	}
	if($xPd->getDestinoInteres() == CAPTACION_DESTINO_CTA_INTERES){
		$xTxt2	= new cHText();
		$xSoc	= new cSocio($persona); $xSoc->init();
		$html	.= $xTxt2->getDeCuentaCaptacionInteres("", $xSoc->getCuentaDeCaptacionPrimaria(CAPTACION_TIPO_VISTA, CAPTACION_PRODUCTO_INTERESES));
	}
	//
	return $html;
}
$jxc ->exportFunction('jsaGetValidacion', array('idsocio', 'idproductocaptacion', 'idorigencaptacion'), "#idotrosdatos" );
// ejecuta el script .
$jxc ->process();

$html	= new cHTMLObject();

echo $xHP->getHeader();
	
//Datos de importacion externa

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);

$e_socio 			= $persona;
$e_origen			= ( isset($_GET["o"]) ) ? $_GET["o"] : 1;
$e_credito 			= ( isset($_GET["c"]) ) ? $_GET["c"] : DEFAULT_CREDITO;
$e_tipo 			= ( isset($_GET["t"]) ) ? $_GET["t"] : CAPTACION_TIPO_VISTA;
$e_tipotitulo		= ( isset($_GET["ti"]) ) ? $_GET["ti"] : 99;
$e_observacion		= ( isset($_GET["n"]) ) ? $_GET["n"] : "";
$e_grupo			= ( isset($_GET["g"]) ) ? $_GET["g"] : DEFAULT_GRUPO;
$e_producto 		= ( isset($_GET["p"]) ) ? $_GET["p"] : DEFAULT_SUBPRODUCTO_CAPTACION ;
$e_cuenta_intereses	= ( isset($_GET["i"]) ) ? $_GET["i"] : CTA_GLOBAL_CORRIENTE ;
$e_cuenta			= ( isset($_GET["x"]) ) ? $_GET["x"] : CTA_GLOBAL_CORRIENTE ;
$msg				= ( isset($_GET[SYS_MSG]) ) ? $_GET[SYS_MSG] : "" ;

$action				= parametro("action", SYS_NINGUNO);
$contrato			= "404.php";
$idcuenta			= parametro("idcuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT);
$idsocio 			= parametro("idsocio", DEFAULT_SOCIO, MQL_INT);

?>
<body>
<?php 
if($action == SYS_NINGUNO){
	$xFRM->addJsBasico(iDE_CAPTACION);
	$xFRM->setAction("frmcaptacioncuentas.php?action=2");
	$xFRM->addPersonaBasico("", false, $e_socio);
	$xFRM->ODate("idfecha", false, "TR.fecha de registro");
	$xSp	= $xSel->getListaDeProductosDeCaptacion();
	$xSp->setOptionSelect($e_producto);
	$xFRM->addHElem( $xSp->get(true) );
	$xFRM->addHElem( $xSel->getListaDeOrigenDeCaptacion()->get(true) );
	$xFRM->addHElem( $xSel->getListaDeTituloDeCaptacion()->get(true) );
	$xFRM->addObservaciones();
	
	$xFRM->addHElem("<h3>" . $xFRM->l()->getT("TR.Otros") . "</h3><div id='idotrosdatos'></div>");
	if(PERSONAS_CONTROLAR_POR_GRUPO == true){
		$xFRM->addGrupoBasico("", DEFAULT_GRUPO);
	} else {
		$xFRM->OHidden("idgrupo", DEFAULT_GRUPO);
	}
	if(GARANTIA_LIQUIDA_EN_CAPTACION == true){
		$xFRM->addCreditBasico(DEFAULT_CREDITO, false, false, "TR.Credito Relacionado");
	} else {
		$xFRM->OHidden("idsolicitud", DEFAULT_CREDITO);
	}
	//$xFRM->addCuentaCaptacionInteres();
	
	$xFRM->addHElem("<h3>" . $xFRM->l()->getT("TR.Mancomunados") . "</h3>");
	$xFRM->addPersonaBasico("2");
	$xFRM->addPersonaBasico("3");

	$xFRM->addGuardar();
	
} else {

	$xS								= new cSocio($idsocio);
	$idsocio 						= ( $xS->existe() == false ) ? false : $idsocio;
	if ($idsocio == DEFAULT_SOCIO ) {
		$msg						.= "ERROR\tPersona $idsocio OR cuenta $idcuenta INVALIDA\r\n";
	} else {
		$credito					= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
		
		$idsolicitud 				= $credito;
		$idgrupo 					= parametro("idgrupo", DEFAULT_GRUPO, MQL_INT);
		$observacion				= parametro("idobservaciones");
		$origencuenta 				= parametro("idorigencaptacion", DEFAULT_CAPTACION_ORIGEN, MQL_INT);
		$tipotitulo					= parametro("idtitulocaptacion");
		$dias						= parametro("iddias", 0, MQL_INT);
		$tasa						= 0;
		
		$sucursal					= getSucursal();
		$fechabaja 					= "2029-12-31";
		$fechaalta 					= parametro("idfecha", fechasys());
		$estatus 					= 10;
		$man1 						= parametro("idsocio2", 0, MQL_INT);
		$man2 						= parametro("idsocio3", 0, MQL_INT);
		$cuentaDeIntereses			= parametro("idcuentainteres", DEFAULT_CUENTA_CORRIENTE, MQL_INT);
		$fechaalta					=$xF->getFechaISO($fechaalta);
		$subpdto 					= parametro("idproductocaptacion");
		$sucess						= true;
		//Iniciar socio
		$xSoc						= new cSocio($idsocio);
		if($xSoc->init() == true){
			$idcuenta					= $xSoc->getIDNuevoDocto(iDE_CAPTACION, $subpdto);
			if($xSoc->existeCredito($idcuenta) == true OR $xSoc->existeCuenta($idcuenta) == true ){
					$idcuenta			= $xSoc->getIDNuevoDocto(iDE_CAPTACION, $subpdto);
					$msg					.= "WARN\tNumero de Cuenta Cambiada $idcuenta\r\n";
			}
					
			//if( $xSoc->existeCuenta($idcuenta) )
			$sqlPdto 					= "SELECT * FROM captacion_subproductos WHERE idcaptacion_subproductos=$subpdto";
			$dPdto 						=  obten_filas($sqlPdto);
		
			$tipocuenta 				= $dPdto["tipo_de_cuenta"];
			$destino_de_intereses		= $dPdto["destino_del_interes"];
			//la cuenta debe de ser de intereses
					$eCta		= new cCuentaDeCaptacion($cuentaDeIntereses, $idsocio);
					$eCta->init();
					$eTS		= $eCta->getTipoDeSubproducto();
		
				if ( ($destino_de_intereses == CAPTACION_DESTINO_CTA_INTERES) AND ( $eTS != CAPTACION_PRODUCTO_INTERESES)  ){
					$msg				.= "ERROR\tLa Cuenta debe tener una CUENTA VALIDA PARA INTERESES, Si no Existe agrege una NUEVA y Asociela con esta Cuenta\r\n";
					exit ( $html->setJsDestino("frmcaptacioncuentas.php?s=$idsocio&x=$idcuenta&i=$cuentaDeIntereses&o=$origencuenta&ti=$tipotitulo&n=$observacion&g=$idgrupo&c=$idsolicitud&p=$subpdto&msg=$msg") );
				}
				//verifica si existe la Cuenta
				/*$sqlcuenta_hay 			= "SELECT COUNT(numero_cuenta) AS 'cuentame' FROM captacion_cuentas WHERE numero_cuenta=$idcuenta";
				$sihayc 				= the_row($sqlcuenta_hay);
		
				$cuentas_existentes 		= $sihayc["cuentame"];
		
				if ($cuentas_existentes > 0) {
					$sql_ultima_cuenta 	= "SELECT MAX(numero_cuenta) AS 'ultima_cuenta' FROM captacion_cuentas";
					$ultima_cuenta 		= the_row($sql_ultima_cuenta);
					$ultima_cuenta 		= $ultima_cuenta["ultima_cuenta"];
				}*/
		
			// Si es Inversion la Cuenta Estara Inactiva
			if(setNoMenorQueCero($man1) > 0){
				$xMan1	= new cSocio($man1);
				if($xMan1->init() == true){
					//agregar relacion
					$xSoc->addRelacionPorDocumento($man1, $idcuenta, PERSONAS_REL_MANCOMUNADO);
					$man1	= "$man1-" . $xMan1->getNombreCompleto();
				}
			}
			if(setNoMenorQueCero($man2) > 0){
				$xMan2		= new cSocio($man2);
				if($xMan2->init() == true){
					//agregar relacion
					$xSoc->addRelacionPorDocumento($man2, $idcuenta, PERSONAS_REL_MANCOMUNADO);
					$man2	= "$man2-" . $xMan2->getNombreCompleto();
				}
			}		
			$xCta		= new cCuentaDeCaptacion($idcuenta, $idsocio, $dias, $tasa, $fechaalta);
			$idcuenta 	= $xCta->setNuevaCuenta($origencuenta, $subpdto, $idsocio,
									$observacion, $idsolicitud,
									$man1, $man2,
									$idgrupo, $fechaalta,
									$tipocuenta, $tipotitulo, $dias, $tasa, $cuentaDeIntereses, $fechaalta);


			$xCta->init();
			
			$xFRM->addCerrar();
			$xFRM->addHTML( $xCta->getFicha(true) );
			$contrato 				= $xCta->getURLContrato();
			$msg					.= $xCta->getMessages();
		}
		
		$msg					.= $xSoc->getMessages();
		if ( MODO_DEBUG == true){
			//$xCta->getMessages(
			$xFL	= new cFileLog(false, true);
			$xFL->setWrite($msg);
			$xFL->setClose();
			$xFRM->addToolbar( $xFL->getLinkDownload("Archivo de sucesos", ""));
		}
		$xFRM->addToolbar( $xBtn->getBasic("TR.Imprimir contrato", "printrec()", "imprimir", "idpcont", false));
		$xFRM->addToolbar( $xBtn->getBasic("TR.Imprimir mandato", "printMandato()", "imprimir", "idpcont", false));
		//Agregar Mancomunados
		
		$xFRM->addAviso($msg, "idmsg", true);
		//-------------------------------------------------------------------------------------------------------
	}
	
}
echo $xFRM->get();

$jxc ->drawJavaScript(false, true);

?>
<script>
$(document).ready(function () {
	$('#id-frmcaptacion').isHappy({
	    fields: {
	      '#idproductocaptacion': {
			required : true,
	        message: 'Necesita Capturar un producto',
			test : jsCargarDatos
	      
	      },
	      "#idorigencaptacion" : {
		      test: jsEsGrupal
	      }
	    }
	  });	
});
function jsCargarDatos(){
	jsaGetValidacion();
	return true;
}
function jsEsGrupal(){
	if(entero($("#idorigencaptacion").val()) == CAPTACION_ORIGEN_CONDICIONADO){
		$("#dividgrupo").css("display", "inline-flex");
	} else {
		$("#dividgrupo").css("display", "none");
	}
	return true;
}
var jsWorkForm	= document.frmcaptacion;
	function printrec(){
		var url 	= "../rpt_formatos/<?php echo $contrato; ?>.php?idcuenta=<?php echo $idcuenta; ?>";
		var mywin 	= window.open(url, "" ,"resizable,fullscreen,scrollbars,menubar");
		mywin.focus();
	}
	function captura_firmas() {
		var url 	= "frmcaptacionfirmas.php?id=<?php echo $idsocio; ?>";
		var mywin 	= window.open(url, "" ,"width=800,height=600,resizable,fullscreen,scrollbars");
		mywin.focus();
	}
	function printMandato(){
		var elUrl	= "../rpt_formatos/mandato_en_depositos.rpt.php?i=<?php echo $idcuenta; ?>";
		var rptrecibo 	= window.open(elUrl, "");
			rptrecibo.focus();
	}

</script>
<?php $xHP->fin(); ?>