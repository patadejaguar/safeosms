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

$xCapt		= new cCuentaDeCaptacion(0);
$xValids	= new cReglasDeValidacion();

$xRuls		= new cReglaDeNegocio();
$NoDiasPre	= $xRuls->getValorPorRegla($xRuls->reglas()->CAPTACION_SIN_DIASPRE); //Sin dias preestablecidos


//-------------------------------------------------------------
$jxc 		= new TinyAjax();
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

$xHP->init();


	
//Datos de importacion externa

$persona		= parametro("persona", 0, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$observaciones	= parametro("idobservaciones");

$origencuenta 		= parametro("idorigencaptacion", DEFAULT_CAPTACION_ORIGEN, MQL_INT); $origencuenta 	= parametro("origen", $origencuenta, MQL_INT); $origencuenta 	= parametro("tipoorigen", $origencuenta, MQL_INT);
$tipotitulo			= parametro("idtitulocaptacion", $xCapt->TITULO_NINGUNO, MQL_INT); $tipotitulo		= parametro("titulo", $tipotitulo, MQL_INT);
$dias				= parametro("iddias", 0, MQL_INT);
$alias				= parametro("alias");
$cuentaDeIntereses	= parametro("idcuentainteres", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuentaDeIntereses	= parametro("cuentainteres", $cuentaDeIntereses, MQL_INT);
$producto			= parametro("idproductocaptacion", 0, MQL_INT); $producto = parametro("producto", $producto, MQL_INT);
$tipocuenta			= parametro("tipocuenta", 0, MQL_INT); $tipocuenta = parametro("clase", $tipocuenta, MQL_INT);

$msg				= parametro(SYS_MSG);

$action				= parametro("action", SYS_NINGUNO);
$contrato			= "404.php";
?>
<body>
<?php

$xFRM->setTitle($xHP->getTitle());
$xFRM->setNoAcordion();

if($action == SYS_NINGUNO){
	//$xFRM->addJsBasico(iDE_CAPTACION);
	$xFRM->setAction("frmcaptacioncuentas.php?action=" . MQL_ADD);
	$xSoc		= new cSocio($persona);
	if($xSoc->init() == true){
		$xFRM->OHidden("idsocio", $persona);
		$xFRM->addHElem( $xSoc->getFicha(false, true, "", true) );
	} else {
		$xFRM->addPersonaBasico("", false, $persona);
	}
	
	$xFRM->ODate("idfecha", false, "TR.fecha de registro");
	
	$xFRM->addHElem( $xSel->getListaDeProductosDeCaptacion("", $producto, $tipocuenta)->get(true) );
	
	if($tipocuenta == CAPTACION_TIPO_PLAZO){
		$xFRM->OHidden("idorigencaptacion", $origencuenta);
	} else {
		$xFRM->addHElem( $xSel->getListaDeOrigenDeCaptacion("", $origencuenta)->get(true) );
	}
	if($NoDiasPre == true){
		$xFRM->OHidden("idtitulocaptacion", $xCapt->TITULO_NINGUNO);
	} else {
		$xFRM->addHElem( $xSel->getListaDeTituloDeCaptacion("", $tipotitulo)->get(true) );
	}
	
	$xFRM->OText_13("alias", "", "TR.ALIAS");
	
	$xFRM->addObservaciones();
	
		
	if($tipocuenta == CAPTACION_TIPO_PLAZO){
		$xFRM->OHidden("idgrupo", DEFAULT_GRUPO);
		$xFRM->OHidden("idsolicitud", $credito);
	} else {
		$xFRM->addSeccion("iddotros", "TR.OTROS");
		
		if(PERSONAS_CONTROLAR_POR_GRUPO == true){
			$xFRM->addGrupoBasico("", DEFAULT_GRUPO);
		} else {
			$xFRM->OHidden("idgrupo", DEFAULT_GRUPO);
		}
		
		if(GARANTIA_LIQUIDA_EN_CAPTACION == true){
			$xFRM->addCreditBasico($credito, false, false, "TR.Credito Relacionado");
		} else {
			$xFRM->OHidden("idsolicitud", $credito);
		}
		$xFRM->endSeccion();
	}
	//$xFRM->addCuentaCaptacionInteres();
	
	$xFRM->addSeccion("idmanc", "TR.MANCOMUNADOS");
	
	
	$xFRM->addPersonaBasico("2");
	$xFRM->addPersonaBasico("3");
	$xFRM->endSeccion();
	
	$xFRM->addGuardar();
	
} else {

	$xS								= new cSocio($persona);
	$persona 						= ( $xS->existe() == false ) ? false : $persona;
	if ($persona == DEFAULT_SOCIO ) {
		$msg						.= "ERROR\tPersona $persona OR cuenta $cuenta INVALIDA\r\n";
	} else {
		//$credito					= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
		
		$idsolicitud 				= $credito;
		
		

		
		$tasa						= 0;
		
		$sucursal					= getSucursal();
		$fechabaja 					= "2029-12-31";
		$fechaalta 					= parametro("idfecha", fechasys());
		$estatus 					= 10;
		$man1 						= parametro("idsocio2", 0, MQL_INT);
		$man2 						= parametro("idsocio3", 0, MQL_INT);
		
		$fechaalta					=$xF->getFechaISO($fechaalta);
		
		$sucess						= true;
		//Iniciar socio
		$xSoc						= new cSocio($persona);
		if($xSoc->init() == true){
			$cuenta					= $xSoc->getIDNuevoDocto(iDE_CAPTACION, $producto);
			if($xSoc->existeCredito($cuenta) == true OR $xSoc->existeCuenta($cuenta) == true ){
				$cuenta				= $xSoc->getIDNuevoDocto(iDE_CAPTACION, $producto);
				$msg				.= "WARN\tNumero de Cuenta Cambiada $cuenta\r\n";
			}
			$xCaptP					= new cCaptacionProducto($producto);
			//$tipocuenta				= CAPTACION_TIPO_VISTA;
			$destino_de_intereses	= "";
			if($xCaptP->init() == true){
				$tipocuenta				= $xCaptP->getClase();
				$destino_de_intereses	= $xCaptP->getDestinoInteres();
			}
		
			//la cuenta debe de ser de intereses
			$eTS						= 0;
			if($xValids->cuenta($cuentaDeIntereses) == true){
				$eCta		= new cCuentaDeCaptacion($cuentaDeIntereses, $persona);
				if($eCta->init() == true){
					$eTS		= $eCta->getTipoDeSubproducto();
				}
			}
			
			if ( ($destino_de_intereses == CAPTACION_DESTINO_CTA_INTERES) AND ( $eTS != CAPTACION_PRODUCTO_INTERESES)  ){
				$msg				.= "ERROR\tLa Cuenta debe tener una CUENTA VALIDA PARA INTERESES, Si no Existe agrege una NUEVA y Asociela con esta Cuenta\r\n";
				$xHP->goToPageX("frmcaptacioncuentas.php?persona=$persona&x=$cuenta&cuentainteres=$cuentaDeIntereses&origen=$origencuenta&titulo=$tipotitulo&idobservaciones=$observaciones&grupo=$grupo&credito=$credito&producto=$producto&msg=$msg");
				exit ();
			}
			
			// Si es Inversion la Cuenta Estara Inactiva
			if(setNoMenorQueCero($man1) > 0){
				$xMan1	= new cSocio($man1);
				if($xMan1->init() == true){
					//agregar relacion
					$xSoc->addRelacionPorDocumento($man1, $cuenta, PERSONAS_REL_MANCOMUNADO);
					$man1	= "$man1-" . $xMan1->getNombreCompleto();
				}
			}
			if(setNoMenorQueCero($man2) > 0){
				$xMan2		= new cSocio($man2);
				if($xMan2->init() == true){
					//agregar relacion
					$xSoc->addRelacionPorDocumento($man2, $cuenta, PERSONAS_REL_MANCOMUNADO);
					$man2	= "$man2-" . $xMan2->getNombreCompleto();
				}
			}
			$xCta		= new cCuentaDeCaptacion($cuenta, $persona, $dias, $tasa, $fechaalta);
			$cuenta 	= $xCta->setNuevaCuenta($origencuenta, $producto, $persona,
					$observaciones, $credito,
					$man1, $man2,
					$grupo, $fechaalta,
					$tipocuenta, $tipotitulo, $dias, $tasa, $cuentaDeIntereses, $fechaalta);
			$xCta->setAlias($alias);
			
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
		var url 	= "../rpt_formatos/<?php echo $contrato; ?>.php?idcuenta=<?php echo $cuenta; ?>";
		var mywin 	= window.open(url, "" ,"resizable,fullscreen,scrollbars,menubar");
		mywin.focus();
	}
	function captura_firmas() {
		var url 	= "frmcaptacionfirmas.php?id=<?php echo $persona; ?>";
		var mywin 	= window.open(url, "" ,"width=800,height=600,resizable,fullscreen,scrollbars");
		mywin.focus();
	}
	function printMandato(){
		var elUrl	= "../rpt_formatos/mandato_en_depositos.rpt.php?i=<?php echo $cuenta; ?>";
		var rptrecibo 	= window.open(elUrl, "");
			rptrecibo.focus();
	}

</script>
<?php $xHP->fin(); ?>