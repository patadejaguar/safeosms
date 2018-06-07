<?php
/**
 * @see Ministracion de Creditos
 * @author Balam Gonzalez Luis Humberto
 * @version 1.1
 * @package creditos
 *  Forma para enviar datos de Creditos a Ministraciom
 * 		06Junio08 	- se Agrego Mejor presentacion
 *					-
 *
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
$xHP		= new cHPage("TR.Desembolso de Creditos", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$jxc 		= new TinyAjax();
$xCaja		= new cCaja();
$xRuls		= new cReglaDeNegocio();
//$xPaso		= new cCreditosEtapas();
$xPaso		= new cCreditosProceso();

$SinDatosDescuentos	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_DESEMBOLSO_SIN_DESC);		//regla de negocio
$SinRecFiscal		= $xRuls->getValorPorRegla($xRuls->reglas()->RECIBOS_SIN_VERSIONIMP);		//regla de negocio

$SinCheque			= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_DESEMBOLSO_SIN_CHQ);		//regla de negocio

if( $xCaja->getEstatus() == TESORERIA_CAJA_CERRADA ){ header ("location:../404.php?i=200"); }

function jsaGetUltimoCheque($persona, $credito, $cuenta_bancaria){
	$xBanc		= new cCuentaBancaria($cuenta_bancaria);
	$xBanc->init();
	$cheque 	= $xBanc->getUltimoCheque();
	$xCred		= new cCredito($credito);
	$xCred->init();
	$montocheque= $xCred->getMontoAutorizado();
	$tab = new TinyAjaxBehavior();
	$tab -> add(TabSetValue::getBehavior("idnumerocheque", $cheque));
	return $tab -> getString();	
}

function jsaCargaDeCreditos($persona){
	$xL		= new cSQLListas();
	$ql		= new MQL();
	$xs		= new cHSelect();
	$xT		= new cHText();
	$xs->setDivClass("");
	$sql 	= $xL->getListadoDeCreditos($persona);
	$rs		= $ql->getDataRecord($sql);
	$aOpts	= array();
	//setLog($sql);
	$items	= 0;
	foreach ($rs as $row){
		$aOpts[$row["credito"]]	 = $row["credito"] . "-" . $row["producto"] . "-" . $row["periocidad"] . "-" . $row["saldo"];
		$items++;   
	}
	$xs->addOptions($aOpts);
	return ($items <= 0) ? $xT->getHidden("idcreditodescontado", 10, 0) :  $xs->get("idcreditodescontado", "TR.CLAVE_de_credito");
}
$jxc ->exportFunction('jsaGetUltimoCheque', array('idsocio', 'idsolicitud', 'idcodigodecuenta' ));
$jxc ->exportFunction('jsaCargaDeCreditos', array('idsocio'), "#iddivcreditos");
$jxc ->process();

$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

echo $xHP->getHeader();

$xFRM		= new cHForm("frmministracion", "frmcreditosministracion.php");
$xSel		= new cHSelect();
$xTxM		= new cHText();
$xNot		= new cHNotif();
$xTxt		= new cHText();
$jsSum		= "";
$sum		= 0;
$etapa		= $xPaso->PASO_ADESEMBOLSO;

$xFRM->addDataTag("role", "ministracion");
$xFRM->setTitle($xHP->getTitle() );
$xFRM->setNoAcordion();
if($credito <= DEFAULT_CREDITO){
	
	$xFRM->addCreditBasico();
	$xFRM->addSubmit();
	$jsSum	= 0;
} else {
	//$xFRM->addSubmit();
	$xCred	= new cCredito($credito);
	?> <style> #idavisopago, #idimporte, #iMontoRecibido { font-size : 1.3em !important; } </style> <?php	
	if($xCred->init() == true){
		$valido		= true;
		if(MODULO_AML_ACTIVADO == true){
			$valido		= $xCred->getEsValido();
		}
		
		if($xCred->isPagable() == true OR $valido == false){
			$xFRM->addAvisoRegistroError($xCred->getMessages());
			$xFRM->addCerrar();
		} else {
			$xFRM->addGuardar();
			$xFRM->setAction("clscreditosministracion.php");
			$xFRM->addHElem($xCred->getFicha(true, "", false, true));
			$persona=$xCred->getClaveDePersona();
		
			$xFRM->OButton("TR.PLAN_DE_PAGOS", "var xC=new CredGen();xC.getImprimirPlanPagosPorCred($credito);", $xFRM->ic()->CALENDARIO1);
			$xFRM->OButton("TR.Validacion", "var xGen=new CredGen(); xGen.getFormaValidacion($credito, $etapa);", $xFRM->ic()->CHECAR, "", "green");
			
			$xFRM->OHidden("idsolicitud", $credito);
			$xFRM->OHidden("idsocio", $persona);
		//descuento //comisiones
		//Cargos y comisiones
		if($SinDatosDescuentos == true){
			$xFRM->OHidden("idmontocreditodescontado", 0);
			$xFRM->OHidden("idcreditodescontado", 0);
		} else {
				$tt		= (TASA_IVA>0) ? "TR.DESCUENTOS ( CON IMPUESTO_AL_CONSUMO )" : "TR.DESCUENTOS";
				$xFRM->addSeccion("iddivdesc", $tt);
	
				$xT		= new cHTabla();
				$xTxt->setDivClass("");
				$xTxt->addEvent("jsGetSumas()", "onchange");			
				$sum	= 0;
				//Vincular descuento de Credito Renovado
				if($xCred->getEsRenovado() == true){
					$xCredOrg	= new cCredito($xCred->getClaveDeOrigen());
					if($xCredOrg->init() == true AND $xCred->getClaveDeOrigen() > DEFAULT_CREDITO){
						if($xCredOrg->getEsPagado() == false){
							$mdesc	= $xCredOrg->getSaldoActual();
							$sum 	+= $mdesc;
							$txt	= $xTxt->getHidden("idmontocreditodescontado", 0, $mdesc);
							$txt	.= $xTxt->getHidden("idcreditodescontado", 0, $xCredOrg->getClaveDeCredito());
							$xT->addRaw("<tr><td>"  . $xCredOrg->getDescripcion() ." $txt</td><td class='mny'>" . getFMoney($xCredOrg->getSaldoActual()) . "</td></tr>");
							$jsSum	.= "flotante(\$('#idmontocreditodescontado').val())";
							$xFRM->addJsInit("jsGetSumas();");
						}
					} else {
						//if($xCred->getClaveDeOrigen() <= DEFAULT_CREDITO){
							$xFRM->addTag($xFRM->getT("MS.CREDITO_FALTA_DRENOV"), "error");
							$xFRM->addJsInit("jsRequiereDatosRenovacion($credito);");
						//}
					}
				} else {
					//Lista de Creditos
					$xSCreditos	= $xSel->getListaDeCreditosPorPersona($persona, "idcreditodescontado", false, $credito);
					$xSCreditos->setLabel("TR.CLAVE_DE_CREDITO CON DESCUENTO CAPITAL");
					$txt		= $xSCreditos->get(false);
					$items		= $xSCreditos->getCountRows();
					if($items > 0){
						$xT->addRaw("<tr><td>$txt</td><td>"  . $xTxt->getDeMoneda("idmontocreditodescontado", "TR.MONTO A DESCONTAR" ) . "</td></tr>");
						$jsSum	.= "flotante(\$('#idmontocreditodescontado').val())";
					}
				}
							
				$rs		= $xQL->getDataRecord($xLi->getListadoDeCargosPorProductoCred($xCred->getClaveDeProducto()));
				foreach ($rs as $rw){
					//idcreditos_productos_costos, clave_de_producto, clave_de_operacion, unidades, unidad_de_medida,
					$xLCosto		= new cCreditos_productos_costos();
					$xLCosto->setData($xLCosto->query()->initByID($rw["clave"]));
					$monto			= ($xLCosto->unidad_de_medida()->v() == 1) ? ($xCred->getMontoAutorizado()*($xLCosto->unidades()->v()/100)) : $xLCosto->unidades()->v();
					$operacion		= $xLCosto->clave_de_operacion()->v();
					$txtoperacion	= $rw["cargo"];
					if($operacion == OPERACION_CLAVE_PAGO_CAPTACION){
						$xSoc	= new cSocio($persona);
						$xSoc->init();
						$idcuentadeposito		= $xSoc->getCuentaDeCaptacionPrimaria(CAPTACION_TIPO_VISTA, CAPTACION_PRODUCTO_GARANTIALIQ);
						if($idcuentadeposito == 0){
							$xCta				= new cCuentaDeCaptacion(false);
							$idcuentadeposito	= $xCta->setNuevaCuenta(DEFAULT_CAPTACION_ORIGEN, CAPTACION_PRODUCTO_GARANTIALIQ, $persona, "", $credito);
							
						}	
						$txtoperacion		= $xSel->getListaDeAhorroPorPersona($persona, CAPTACION_PRODUCTO_GARANTIALIQ)->get("TR.CUENTA DE DEPOSITO", false);
					}
					if($xLCosto->editable()->v() == 1){
						$xT->initRow();
						$xT->addTD($txtoperacion);
						$xT->addTD($xTxt->getDeMoneda("idm-$operacion","", $monto ));
						$xT->endRow();
					} else {
						$xT->initRow();
						$xT->addTD($txtoperacion);
						$xT->addTD($monto);
						$xT->endRow();
						$xFRM->OHidden("idm-$operacion", $monto);
					}
					
					$sum += $monto;
					$jsSum	.= ($jsSum == "") ? "flotante(\$('#idm-$operacion').val())" :"+flotante(\$('#idm-$operacion').val())";
				}
				
				$xT->addRaw("<tr><th>TOTAL</th><td class='mny total' id='idtdsum'>"  . getFMoney($sum) . "</td></tr>");
				$xFRM->addHElem($xT->get());
				$xFRM->endSeccion();
				$xFRM->OHidden("idsumadescuentos", $sum);
				$xFRM->addAviso($xFRM->l()->getT("MS.OPERACION_COM_CON_IVA") );
				if($xF->getCompare(fechasys(), $xCred->getFechaDeMinistracion()) == false){
					$xFRM->addAviso($xFRM->l()->getT("MS.CREDITO_FECHA_MIN_NO_EQ") . " : " . $xF->getFechaMediana($xCred->getFechaDeMinistracion()), "", true, $xNot->WARNING);
				}
			}
			
			
			
			//fragmentacion del cheque
			$xFRM->addSeccion("iddivcheque", "TR.CHEQUE");
			$xCant		= new cCantidad($xCred->getMontoAutorizado());
			$xFRM->addHElem($xCant->getFicha());
			
			//$xFRM->ODate("idfechaactual", $xCred->getFechaDeMinistracion(), "TR.Fecha de otorgacion");
			$xFRM->addFechaRecibo($xCred->getFechaDeMinistracion());
			
			$xSelBancos	= $xSel->getListaDeCuentasBancarias("", true); $xSelBancos->addEvent("onchange", "jsGetCheque()");
			$xFRM->addHElem( $xSelBancos->get(true) );
			
			
			$xTxM->addEvent("jsGetCheque()", "onfocus");
			$xFRM->addHElem($xTxM->get("idnumerocheque", "", "TR.Codigo de Cheque / Numero de Transferencia") );
			//$xFRM->OText("idnumerocheque", "", "TR.Codigo de Cheque / Numero de Transferencia");
			
			$xFRM->OHidden("idmontocheque", $xCred->getMontoAutorizado());
			if($SinRecFiscal == true){
				$xFRM->OHidden("idfoliofiscal", "");
			} else {
				$xFRM->OText("idfoliofiscal", "", "TR.Folio Impreso");
			}
			$xFRM->addObservaciones();
			$xFRM->OCheck("TR.ES TRANSFERENCIA", "idestransferencia");
			$xFRM->endSeccion();
			
			if($SinCheque == false){
				$xFRM->setValidacion("idnumerocheque", "validacion.novacion", "Numero de cheque es obligatorio", true);
			}
			
			$xFRM->addHElem( $xNot->get($xFRM->l()->getT("TR.CANTIDAD A RECIBIR") . " : <mark id='idtotal'>" . getFMoney(($xCred->getMontoAutorizado()-$sum)) . "</mark>", "idavisopago", $xNot->SUCCESS) );
		}
	}
}
echo $xFRM->get();

$jxc ->drawJavaScript(false, true);

?>
<script>
var gn			= new Gen();
var val			= new ValidGen();
var errors		= 0;
var isUpdate	= false;
var xG			= new Gen();

function jsGetCheque(){
	var idnumerocheque	= $("#idnumerocheque").val();
	if(entero(idnumerocheque) <=0){
		jsaGetUltimoCheque();
	}
	return true;
}
function jsGetSumas(){

	
	var mTotal = <?php echo ($jsSum == "") ? "0" : $jsSum; ?>;
	$("#idtdsum").html( getFMoney(mTotal));
	var nnto 	= flotante($("#idmontocheque").val())-mTotal;
	$("#idtotal").html( getFMoney(nnto));
	$("#idsumadescuentos").val(mTotal);
}
function jsRequiereDatosRenovacion(id){
	xG.requiere({
		callback: function(){ 
			xG.w({tiny:true, url:"../frmcreditos/creditos.datos-origen.new.frm.php?tipo=" +Configuracion.credito.origen.renovacion + "&credito=" + id, callback: jsRefresh});
		},
		msg : 'CREDITO_FALTA_DRENOV'
	});
}
function jsRefresh(){
	window.location.reload(true);
}
</script>
</body>
</html>
