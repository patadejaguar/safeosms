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
$xHP		= new cHPage("TR.Cobro de Comisiones");
$xCaja		= new cCaja();
$xF			= new cFecha();
$xLi		= new cSQLListas();
$xQL		= new MQL();

$jxc 		= new TinyAjax();


function jsaGetComisionPorApertura($idcredito){
	$xCred		= new cCredito($idcredito);
	$cantidad	= 0;
	if( $xCred->init() == true){
		$tasa		= $xCred->getOProductoDeCredito()->getTasaComisionApertura();
		$cantidad = round(($xCred->getMontoAutorizado() * $tasa), 2);
	}
	return $cantidad;
}
$jxc ->exportFunction('jsaGetComisionPorApertura', array('idsolicitud'), '#idcom1');

$jxc ->process();

if( $xCaja->getEstatus() == TESORERIA_CAJA_CERRADA ){	header ("location:../404.php?i=200"); }

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->init();

$xBase		= new cBases(BASE_IVA_OTROS);
$xBase->init();
$xFRM		= new cHForm("frmcomisiones", "cobro_de_comisiones.frm.php");
$xTxt		= new cHText();
$msg		= "";
$xFRM->setTitle($xHP->getTitle());
//$xFRM->setNoAcordion();
//$xFRM->addSeccion("uddu", $titulo)
if($action == MQL_ADD){
	
	$xRec 					= new cReciboDeOperacion(PERIODO_CERO,RECIBOS_TIPO_OINGRESOS, false);
	$xRec->setGenerarPoliza();
	$xRec->setGenerarTesoreria();
	
	$detalles 		= parametro("idobservaciones", "");
	//$monto 		= parametro("idmonto", 0, MQL_FLOAT);
	$cheque 		= parametro("cheque", DEFAULT_CHEQUE);
	$comopago 		= parametro("ctipo_pago", DEFAULT_TIPO_PAGO, MQL_RAW);
	$foliofiscal 	= parametro("foliofiscal", DEFAULT_RECIBO_FISCAL);
	$fecha			= parametro("idfecha", fechasys());
	$fecha			= $xF->getFechaISO($fecha);
	$numero_cargos	= 0;
	$idrecibo		= $xRec->setNuevoRecibo($persona, $credito, $fecha, PERIODO_CERO,RECIBOS_TIPO_OINGRESOS, $detalles, $cheque, $comopago, $foliofiscal);
	if(setNoMenorQueCero($idrecibo) > 0){
		$xCred	= new cCredito($credito);
		$jsSum	= "";
		if($xCred->init() == true){
			$rs		= $xQL->getDataRecord($xLi->getListadoDeCargosPorProductoCred($xCred->getClaveDeProducto()));
			$mCreditoIVA	= $xCred->getTasaIVA();
			$montoIVA		= 0;
			foreach ($rs as $rw){
				$xLCosto	= new cCreditos_productos_costos();
				$xLCosto->setData($xLCosto->query()->initByID($rw["clave"]));
				$idx		= "idm-" . $xLCosto->clave_de_operacion()->v();
				$monto		= parametro($idx,0, MQL_FLOAT);
				$TasaIVA	= ($xBase->getIsMember($xLCosto->clave_de_operacion()->v()) == true) ? $xCred->getTasaIVAOtros() : 0;
				$factorIVA	= (1/(1+$TasaIVA));
				$monto		= setNoMenorQueCero( ($monto * $factorIVA),2);
				$montoIVA	+= setNoMenorQueCero(($monto*$TasaIVA),2);
				if($monto > 0){
					$xRec->setNuevoMvto($fecha, $monto, $xLCosto->clave_de_operacion()->v(), PERIODO_CERO, "", 1, TM_ABONO, $persona);
					$numero_cargos++;
				}
			}
			//Insertar Monto de Otras comisiones
			
			if($montoIVA >0){
				$xRec->setNuevoMvto($fecha, $montoIVA, OPERACION_CLAVE_PAGO_IVA_OTROS, PERIODO_CERO, "", 1, TM_ABONO, $persona);
			}				
		}		
		if($xRec->setFinalizarRecibo(true) == true){
			$xFRM->setAction("");
			$xFRM->addHElem( $xRec->getFichaSocio() );
			$xFRM->addHElem( $xRec->getFicha(true) );
			$xFRM->OButton("TR.Imprimir Recibo", "jsImprimirRecibo()", "imprimir");
			$xFRM->addAvisoRegistroOK();
			$xFRM->addCerrar();
		
			echo $xRec->getJsPrint(true);	
		}
		if(MODO_DEBUG == true){
			$xFRM->addLog($xRec->getMessages());
		}
	} else {
		$xFRM->addAviso($xRec->getMessages());
	}
	$jsSum	= "0";
} else {
	
	
	if($credito > DEFAULT_CREDITO){
		$xFRM->setAction("cobro_de_comisiones.frm.php?action=" . MQL_ADD);
		$xCred			= new cCredito($credito);
		$jsSum			= "";
		$numero_cargos	= 0;
		if($xCred->init() == true){
			$xFRM->OHidden("idsolicitud", $credito);
			$xFRM->OHidden("idsocio", $xCred->getClaveDePersona());
			$xFRM->OHidden("idmontoautorizado", $xCred->getMontoAutorizado());
			
			$xFRM->addHElem($xCred->getFichaMini());
			$xFRM->addGuardar();
			$rs		= $xQL->getDataRecord($xLi->getListadoDeCargosPorProductoCred($xCred->getClaveDeProducto()));
			$xT		= new cHTabla();
			$xFRM->addFecha();
			$xFRM->addCobroBasico();
			$xFRM->addObservaciones();
			$sum	= 0;
			$xTxt->setDivClass("");
			$xTxt->addEvent("jsGetSumas()", "onchange");
			foreach ($rs as $rw){
				//idcreditos_productos_costos, clave_de_producto, clave_de_operacion, unidades, unidad_de_medida,
				$xLCosto	= new cCreditos_productos_costos();
				$xLCosto->setData($xLCosto->query()->initByID($rw["clave"]));
				$monto		= ($xLCosto->unidad_de_medida()->v() == 1) ? ($xCred->getMontoAutorizado()*($xLCosto->unidades()->v()/100)) : $xLCosto->unidades()->v();
				$operacion	= $xLCosto->clave_de_operacion()->v(); 
				
				
				 if($xLCosto->editable()->v() == 1){
				 	$xT->initRow();
				 	$xT->addTD($rw["cargo"]);
				 	$xT->addTD($xTxt->getDeMoneda("idm-$operacion","", $monto ));
				 	$xT->endRow();
				 	$numero_cargos++;
				 } else {
				 	$xT->initRow();
				 	$xT->addTD($rw["cargo"]);
				 	$xT->addTD($monto);
				 	$xT->endRow();
				 	$xFRM->OHidden("idm-$operacion", $monto);
				 	$numero_cargos++;
				 }
				$sum += $monto;
				$jsSum	.= ($jsSum == "") ? "flotante(\$('#idm-$operacion').val())" :"+flotante(\$('#idm-$operacion').val())";
			}
			/*$xT->initRow();
			$xT->addTD("TOTAL");
			$xT->addTD($sum);
			$xT->endRow();*/
			$xT->addRaw("<tr><th>TOTAL</th><td class='mny' id='idtdsum'>"  . getFMoney($sum) . "</td></tr>");
			$xFRM->addHElem($xT->get());
			$xFRM->addAviso($xFRM->l()->getT("TR.Las Comisiones deben incluir IMPUESTO_AL_CONSUMO") );
		}
	} else {
		$xFRM->addCreditBasico();
		$xFRM->addEnviar();
		$jsSum	= "0";
	}
}
echo $xFRM->get();
?>
<script>
function jsGetSumas(){
	var mTotal = <?php echo $jsSum ?>;
	$("#idtdsum").html( getFMoney(mTotal));
}
</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>