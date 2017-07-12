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
$xHP		= new cHPage("TR.COBRO PROMOCIONES", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc 		= new TinyAjax();
//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
//$tipo		= parametro("tipo", iDE_CREDITO, MQL_INT);

$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto		= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo		= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa	= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo		= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria = parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);

$observaciones= parametro("idobservaciones");
$cheque			= parametro("cheque");
$foliofiscal	= parametro("foliofiscal");
$comopago		= parametro("ctipo_pago", SYS_NINGUNO, MQL_RAW);
$montoiva		= parametro("idiva", 0, MQL_FLOAT);


$xHP->init();
$xProm		= new cCreditosPromociones();
$xFRM		= new cHForm("frmpromociones", "../frmcaja/cobrar-promociones.frm.php");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$jsTotal	= "0";
$total		= 0;

if($credito <= DEFAULT_CREDITO){
	$xFRM->addCreditBasico();
	$xFRM->addSubmit();
} else {
	$xFRM->addGuardar();
	$xFRM->setNoAcordion();
	
	$xTxt2			= new cHText();
	$xTxt2->setDivClass("");
	
	if($credito > DEFAULT_CREDITO){
		$xT			= new cHTabla("idtconceptos");
		$xT->initRow();
		$xT->addTH("TR.CONCEPTO");
		$xT->addTH("TR.PROMOCION");
		$xT->addTH("TR.ACREDITADOS");
		$xT->addTH("TR.PRECIO");
		$xT->addTH("TR.NUMERO");
		$xT->addTH("TR.MONTO");
		$xT->endRow();
		
		if($action == SYS_NINGUNO){
			$xCred	= new cCredito($credito);
			if($xCred->init() == true){
				$xFRM->addSeccion("iddcred", "TR.CREDITO");
				
				$xFRM->addHElem( $xCred->getFichaMini() );
				$idproducto		= $xCred->getClaveDeProducto();
				$arrGratis		= $xProm->initItemsGratuitos($idproducto);
				$arrAcred		= $xProm->initItemsAcreditados($credito);
				$arrPrecio		= $xProm->getArrPrecios();
				$xFRM->OHidden("credito", $credito);
				$xFRM->endSeccion();
				$xFRM->addSeccion("iddcob", "TR.DATOS DEL RECIBO");
				$xFRM->addFecha();
				
				$xFRM->OHidden("ctipo_pago", $xProm->TIPO_PAGO_PROMO);
				//$xFRM->addCobroBasico("", $xProm->TIPO_PAGO_PROMO);
				
				$xFRM->addObservaciones();
				$xFRM->endSeccion();
				$xFRM->addSeccion("iddops", "TR.OPERACIONES");
				
				foreach ($arrGratis as $tipo => $items){
					$acreditado	= (isset($arrAcred[$tipo])) ? $arrAcred[$tipo] : 0;
					$precio		= $arrPrecio[$tipo];
					$restantes	= $items - $acreditado;
					$xTipoOp	= new cTipoDeOperacion($tipo);
					$xTipoOp->init();
					
					$xT->initRow();
					$xT->addTD($xTipoOp->getNombre());
					$xT->addTD($items);
					$xT->addTD($acreditado);
					$xT->addTD(getFMoney($precio), " class='mny' ");
					$xTxt			= new cHText();
					$xTxt->setDivClass("");
					$xTxt->addEvent("jsActualizarMonto($tipo)", "oninput");
					$xTxt->addDataX("id", $tipo);
					$nn		= ($restantes >= 1) ? 1 : 0;
					if($nn <= 0){
						$xT->addTDM("0");
						$xFRM->OHidden("item-tipo",0);
					} else {
						$xT->addTD($xTxt->getDeConteo("item-$tipo", "", 0, $restantes));
					}
					$pp		= $nn * $precio;
					
					$xT->addTD($xTxt2->getDeMoneda3("op-$tipo", "",  $pp));
					$xFRM->OHidden("precio-$tipo", $precio);
					$total	+= $pp;
					
					$xT->endRow();
					
					$jsTotal	.= "+flotante($(\"#op-$tipo\").val())";
				}
				$xT->addFootTD("");
				$xT->addFootTD("");
				$xT->addFootTD("");
				$xT->addFootTD("");
				$xT->addFootTD("Total");
				$xT->addFootTD($xTxt2->getDeMoneda3("op-total", "", $total));
	
				
				//$rs	= $xQL->getDataRecord($xLi->getListadoDePromosPorProductoCred($idproducto) );
				
				//Agregar Action
				$xFRM->setAction("../frmcaja/cobrar-promociones.frm.php?action=" . MQL_ADD);
			
				$xFRM->addHElem($xT->get());
				$xFRM->endSeccion();
			}
		} else {
			$xCred	= new cCredito($credito);
			if($xCred->init() == true){
				$total		= parametro("op-total", 0, MQL_FLOAT);
				if($total > 0){
					$xFRM->addHElem( $xCred->getFichaMini() );
					$idproducto		= $xCred->getClaveDeProducto();
					$arrGratis		= $xProm->initItemsGratuitos($idproducto);
					$xRec			= new cReciboDeOperacion(RECIBOS_TIPO_OINGRESOS);
					$idrecibo		= $xRec->setNuevoRecibo($xCred->getClaveDePersona(), $xCred->getClaveDeCredito(), $fecha, 0, RECIBOS_TIPO_OINGRESOS, $observaciones, $cheque, $comopago, $foliofiscal);
					//$arrAcred		= $xProm->initItemsAcreditados($credito);
					//$arrPrecio		= $xProm->getArrPrecios();
					
					foreach ($arrGratis as $tipo => $items){
						$precio 	= parametro("precio-$tipo",0, MQL_FLOAT);
						$numero		= parametro("item-$tipo",0, MQL_INT);
						if($numero>=1){
							for($i = 1; $i <= $numero; $i++){
								//Agregar Operacion
								$xRec->addMovimiento($tipo, $precio);
							}
						}
					}
					
					$xRec->addMvtoContableByTipoDePago($total, TM_CARGO);
					
					
					$xRec->setFinalizarRecibo(true);
					
					$xRec	= new cReciboDeOperacion(false, false, $idrecibo);
					if($xRec->init() == true){
					
						$xFRM->addHElem($xCred->getFichaMini());
						$xFRM->addHElem($xRec->getFicha(true));
					
						$xFRM->addPrintRecibo();
						$xFRM->addJsCode( $xRec->getJsPrint() );
						$xFRM->addJsInit("jsImprimirRecibo();");
					
						$xFRM->addCerrar();
						$xFRM->addAvisoRegistroOK();
					}
				}
			}
		}
		
	}
}

echo $xFRM->get();
?>
<script>
function jsActualizarMonto(id){
	var precio 	= flotante($("#precio-" + id).val());
	var items	= entero($("#item-" + id).val());

	var pp		= precio * items;

	$("#op-" + id).val(pp);
	$("#op-" + id + "_dis").val(getFMoney(pp));

	jsActualizarTotal();
}
function jsActualizarTotal(){
	var tt	= <?php echo $jsTotal; ?>;

	$("#op-total").val(tt);
	$("#op-total_dis").val(getFMoney(tt));	
}
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>