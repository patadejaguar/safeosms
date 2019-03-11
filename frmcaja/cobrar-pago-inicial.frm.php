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
$xHP		= new cHPage("TR.ARRENDAMIENTO .- COBRO DE PAGO INICIAL", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$xRuls		= new cReglaDeNegocio();


//$jxc 		= new TinyAjax();
//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$observaciones	= parametro("idobservaciones");

$xHP->init();

$xFRM		= new cHForm("frmcobropagoinit", "./");
$xSel		= new cHSelect();
$xTxt		= new cHText();

$xFRM->setTitle($xHP->getTitle());
$xFRM->setNoAcordion();

$IvaNoInc		= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_ARREND_IVA_NOINC);

if($credito <= DEFAULT_CREDITO){
	//$xFRM->addCreditBasico();
	$xHP->goToPageX("../frmcreditos/buscar-creditos.frm.php?tipoensistema=" . SYS_PRODUCTO_ARREND . "&next=../frmcaja/cobrar-pago-inicial.frm.php");
} else {
	$xCred			= new cCredito($credito);
	if($xCred->init() == true){
		$persona	= $xCred->getClaveDePersona();
		$periodo	= 0;
		
		
		
		$idleasing	= $xCred->getClaveDeOrigen();
		$xLeas		= new cCreditosLeasing($idleasing); $xLeas->init();
		$idrecarch	= $xLeas->getCodigoReciboPagoInit();
		
		if($idrecarch > 0){
			$xFRM->addAvisoInicial("El Pago Inicial Existe.", true);
			$xFRM->addCerrar();
		} else {
			$TasaIVA	= TASA_IVA;
			
			$xFRM->addSeccion("iddc", "TR.CREDITO");
			$xFRM->addHElem( $xCred->getFichaMini() );
			$xFRM->OHidden("idleasing", $idleasing);
			$xFRM->OHidden("credito", $credito);
			$xFRM->endSeccion();
			
			$xTxt->setDivClass("");
			
	 		$xFRM->OButton("TR.COTIZADOR", "var xC=new CredGen();xC.getLeasingCotizacion(" . $xCred->getClaveDeOrigen() . ");", $xFRM->ic()->REPORTE, "cmdcotizador");
			
			
			if($action == SYS_NINGUNO){
				$xFRM->setAction("../frmcaja/cobrar-pago-inicial.frm.php?action=" . MQL_ADD, true);
				
				$xFRM->addFechaRecibo($fecha);
				
				$xFRM->addCobroBasico();
				$xFRM->addObservaciones();
				
				$AnticipoRentas			= $xLeas->getAnticipo();
				$ComisionApertura		= $xLeas->getMontoComision();
				$RentaEnDeposito		= $xLeas->getMontoDepositoGarantia();
				$RentaAnticipada		= $xLeas->getMontoRentaProporcional();
				$RatificacionContrato	= $xLeas->getMontoNotario(); //Gastos Notariales
				$SeguroInicial			= $xLeas->getSeguroInicial(); //Editable
				$Placas					= $xLeas->getMontoPlacas(); //Editable
				$factorIVA				= 1;
				
				$BaseIVA				= 0;
				
				if($IvaNoInc == true){
					$factorIVA			= 1 / (1+($TasaIVA));
					$AnticipoRentas		= $AnticipoRentas * $factorIVA;
					$ComisionApertura	= $ComisionApertura * $factorIVA;
					$RentaEnDeposito	= $RentaEnDeposito * $factorIVA;
					$RentaAnticipada	= $RentaAnticipada * $factorIVA;
					$RatificacionContrato = $RatificacionContrato * $factorIVA;
					$SeguroInicial		= $SeguroInicial * $factorIVA;
					$Placas				= $Placas * $factorIVA;
				}
				
				$BaseIVA				= $AnticipoRentas + $ComisionApertura + $RentaAnticipada + $RatificacionContrato + $SeguroInicial + $Placas;
				$Iva					= round(($BaseIVA * $TasaIVA),2);
				$SubTotal				= $BaseIVA + $RentaEnDeposito;
				$Total					= $Iva + $SubTotal;
				
				$xFRM->OHidden("anticiporentas", $AnticipoRentas);
				$xFRM->OHidden("comisionapertura", $ComisionApertura);
				$xFRM->OHidden("rentadeposito", $RentaEnDeposito);
				$xFRM->OHidden("rentaanticipada", $RentaAnticipada);
				$xFRM->OHidden("iva", $Iva);
				$xFRM->OHidden("subtotal", $SubTotal);
				$xFRM->OHidden("tasaiva", $TasaIVA);
				$xFRM->OHidden("gastosnotariales", $RatificacionContrato);
				
				$xTbl		= new cHTabla("idtblpagos", "listado");
				$xTbl->initRow();
				$xTbl->addTH("TR.CONCEPTO");
				$xTbl->addTH("TR.MONTO");
				$xTbl->endRow();
				/* ------ Anticipo Rentas ------*/
				$xTbl->initRow();
				$xTbl->addTD($xFRM->getT("TR.ANTICIPORENTA"));
				$xTbl->addTD(getFMoney($AnticipoRentas), " class='izq' ");
				$xTbl->endRow();
				/* ------ Comision por Apertura ------*/
				$xTbl->initRow("trOdd");
				$xTbl->addTD($xFRM->getT("TR.COMISION_POR_APERTURA"));
				$xTbl->addTD(getFMoney($ComisionApertura), " class='izq' ");
				$xTbl->endRow();
				/* ------ Renta de Deposito ------*/
				$xTbl->initRow();
				$xTbl->addTD($xFRM->getT("TR.RENTADEPOSITO"));
				$xTbl->addTD(getFMoney($RentaEnDeposito), " class='izq' ");
				$xTbl->endRow();
				/* ------ Primera Renta ------*/
				$xTbl->initRow("trOdd");
				$xTbl->addTD($xFRM->getT("TR.PRIMERARENTA"));
				$xTbl->addTD(getFMoney($RentaAnticipada), " class='izq' ");
				$xTbl->endRow();
				/* ------ Gastos Notariales ------*/
				$xTbl->initRow();
				$xTbl->addTD($xFRM->getT("TR.GASTOSNOTARIALES"));
				$xTbl->addTD(getFMoney($RatificacionContrato), " class='izq' ");
				$xTbl->endRow();
				/* ------ Seguro de Auto ------*/
				$xTbl->initRow("trOdd");
				$xTbl->addTD($xFRM->getT("TR.AUTOSEGURO"));
				$xTbl->addTD($xTxt->getDeMoneda2("montoseguro", "", $SeguroInicial));
				$xTbl->endRow();
				/* ------ Costo Placas ------*/
				$xTbl->initRow("");
				$xTbl->addTD($xFRM->getT("TR.COSTOPLACAS"));
				$xTbl->addTD($xTxt->getDeMoneda2("montoplacas", "", $Placas));
				$xTbl->endRow();
				
				/* ------ Subtotal ------*/
				$xTbl->initRow("trOdd");
				$xTbl->addTD($xFRM->getT("TR.SUBTOTAL"));
				$xTbl->addTD(getFMoney($SubTotal), " class='izq total' id='tdsubtotal' ");
				$xTbl->endRow();
				/* ------ IVA ------*/
				$xTbl->initRow("");
				$xTbl->addTD($xFRM->getT("TR.IVA"));
				$xTbl->addTD(getFMoney($Iva), " class='izq' id='tdiva' ");
				$xTbl->endRow();
				/* ------ Total ------*/
				$xTbl->initRow("trOdd");
				$xTbl->addTD($xFRM->getT("TR.TOTAL"));
				$xTbl->addTD(getFMoney($Total), " class='izq total' id='tdtotal' ");
				$xTbl->endRow();
				
				$xFRM->addSeccion("idddp", "TR.PAGO");
				$xFRM->addHElem( $xTbl->get() );
				$xFRM->endSeccion();
				
				$xFRM->setValidacion("montoseguro_mny", "jsActualizarTotal");
				$xFRM->setValidacion("montoplacas_mny", "jsActualizarTotal");
				
				
				$xFRM->addGuardar("", "", "TR.GUARDAR PAGO");
				
			} else {
				
				$montoseguro		= parametro("montoseguro",0, MQL_FLOAT);
				$montoplacas		= parametro("montoplacas",0, MQL_FLOAT);
				$idleasing			= parametro("idleasing",0, MQL_INT);
				$anticiporentas		= parametro("anticiporentas",0, MQL_FLOAT);
				$comisionapertura	= parametro("comisionapertura",0, MQL_FLOAT);
				$rentadeposito		= parametro("rentadeposito",0, MQL_FLOAT);
				$rentaanticipada	= parametro("rentaanticipada",0, MQL_FLOAT);
				$iva				= parametro("iva",0, MQL_FLOAT);
				$subtotal			= parametro("subtotal",0, MQL_FLOAT);
				$tasaiva			= parametro("tasaiva",0, MQL_FLOAT);
				$gastosnotariales	= parametro("gastosnotariales",0, MQL_FLOAT);
				
				$ctipo_pago			= parametro("ctipo_pago", SYS_NINGUNO, MQL_RAW);
				$cheque				= parametro("cheque");
				$foliofiscal		= parametro("foliofiscal");
				$idobservaciones	= parametro("idobservaciones");
				
				//Aplicar pago
				if($subtotal > 0){
					//var_dump($_REQUEST);
					$xRec			= new cReciboDeOperacion();
					$recibo			= $xRec->setNuevoRecibo($persona, $credito, $fecha, $periodo, RECIBOS_TIPO_PRIMERPAG, $observaciones, $cheque, $ctipo_pago, $foliofiscal);
					
					if($montoseguro>0){
						$xRec->addMovimiento(OPERACION_CLAVE_PAGO_SEGURO_V, $montoseguro, $periodo);
					}
					if($montoplacas>0){
						$xRec->addMovimiento(OPERACION_CLAVE_PAGO_PLACAS, $montoplacas, $periodo);
					}
					if($anticiporentas > 0){
						$xRec->addMovimiento(178, $anticiporentas, $periodo);
					}
					if($rentadeposito > 0){
						$xRec->addMovimiento(179, $rentadeposito, $periodo);
					}
					if($comisionapertura > 0){
						$xRec->addMovimiento(OPERACION_CLAVE_COMISION_APERTURA, $comisionapertura, $periodo);
					}
					if($gastosnotariales > 0){
						$xRec->addMovimiento(177, $gastosnotariales, $periodo);
					}
					$IvaPagado		= 0;
					if($rentaanticipada > 0){
						//Aplicar Al Pago 1
						$xLetra	= new cParcialidadDeCredito();
						$xPag	= new cCreditosPagos($credito);
						$diluir	= $rentaanticipada;
						
						if($xLetra->init($persona, $credito, 1) == true){
							$capital	= $xLetra->getCapital();
							$interes	= $xLetra->getInteres();
							$otros		= $xLetra->getOtros();
							$idperiodo	= 1;
							$xLetra->setClaveDePlan($xCred->getNumeroDePlanDePagos()); //necesario para actualizacion RAW
							//== Interes
							if($interes >= $diluir){
								$interes	= $diluir;
								$diluir		= 0;
							} else {
								$diluir		= $diluir - $interes;
							}
							if($interes > 0){
								$xPag->addPagoInteres($interes, $idperiodo, $observaciones, $ctipo_pago, $fecha, $recibo);
								$xLetra->setActualizarInteres($interes, "-");
							}
							//== Otros
							if($otros >= $diluir){
								$otros		= $diluir;
								$diluir		= 0;
							} else {
								$diluir		= $diluir - $otros;
							}
							if($otros > 0){
								$xPag->addPagoOtros($otros, $idperiodo, $observaciones, $ctipo_pago, $fecha, $recibo);
								$xLetra->setActualizarDesglose($otros, "-");
							}
							//== Cpital
							if($capital >= $diluir){
								$capital	= $diluir;
								$diluir		= 0;
							} else {
								$diluir		= $diluir - $capital;
							}
							
							if($capital>0){
								$xPag->setAbonoCapital($capital, $idperiodo, $cheque, $ctipo_pago, $foliofiscal, $observaciones, false, $fecha, $recibo);
								$xLetra->setActualizarCapital($capital, "-");
							}
							$IvaPagado	= $xPag->getIVAAplicado();
						}
					}
					//================= Pago de IVA
					$iva	= $iva - $IvaPagado;
					if($iva>0){
						$xRec->addMovimiento(OPERACION_CLAVE_PAGO_IVA_ARR, $iva, $periodo);
					}
					
					$xRec->setForceUpdateSaldos(true);
					$xRec->setFinalizarRecibo(true);
					
					$xFRM->addJsCode($xRec->getJsPrint(false));
					$xFRM->addImprimir("", "printrec();");
					$xFRM->addCerrar();
					/*------------------------ AHORA -------------------------------- */
					
					$xTbl		= new cHTabla("idtblpagos", "listado");
					$xTbl->initRow();
					$xTbl->addTH("TR.CONCEPTO");
					$xTbl->addTH("TR.MONTO");
					$xTbl->endRow();
					/* ------ Anticipo Rentas ------*/
					$xTbl->initRow();
					$xTbl->addTD($xFRM->getT("TR.ANTICIPORENTA"));
					$xTbl->addTD(getFMoney($anticiporentas), " class='izq' ");
					$xTbl->endRow();
					/* ------ Comision por Apertura ------*/
					$xTbl->initRow("trOdd");
					$xTbl->addTD($xFRM->getT("TR.COMISION_POR_APERTURA"));
					$xTbl->addTD(getFMoney($comisionapertura), " class='izq' ");
					$xTbl->endRow();
					/* ------ Renta de Deposito ------*/
					$xTbl->initRow();
					$xTbl->addTD($xFRM->getT("TR.RENTADEPOSITO"));
					$xTbl->addTD(getFMoney($rentadeposito), " class='izq' ");
					$xTbl->endRow();
					/* ------ Primera Renta ------*/
					$xTbl->initRow("trOdd");
					$xTbl->addTD($xFRM->getT("TR.PRIMERARENTA"));
					$xTbl->addTD(getFMoney($rentaanticipada), " class='izq' ");
					$xTbl->endRow();
					/* ------ Gastos Notariales ------*/
					$xTbl->initRow();
					$xTbl->addTD($xFRM->getT("TR.GASTOSNOTARIALES"));
					$xTbl->addTD(getFMoney($gastosnotariales), " class='izq' ");
					$xTbl->endRow();
					/* ------ Seguro de Auto ------*/
					$xTbl->initRow("trOdd");
					$xTbl->addTD($xFRM->getT("TR.AUTOSEGURO"));
					$xTbl->addTD(getFMoney($montoseguro), " class='izq' ");
					$xTbl->endRow();
					/* ------ Costo Placas ------*/
					$xTbl->initRow("");
					$xTbl->addTD($xFRM->getT("TR.COSTOPLACAS"));
					$xTbl->addTD(getFMoney($montoplacas), " class='izq' ");
					$xTbl->endRow();
					
					/* ------ Subtotal ------*/
					$xTbl->initRow("trOdd");
					$xTbl->addTD($xFRM->getT("TR.SUBTOTAL"));
					$xTbl->addTD(getFMoney($subtotal), " class='izq total' id='tdsubtotal' ");
					$xTbl->endRow();
					/* ------ IVA ------*/
					$xTbl->initRow("");
					$xTbl->addTD($xFRM->getT("TR.IVA"));
					$xTbl->addTD(getFMoney($iva), " class='izq' id='tdiva' ");
					$xTbl->endRow();
					/* ------ Total ------*/
					$total	= $subtotal + $iva;
					$xTbl->initRow("trOdd");
					$xTbl->addTD($xFRM->getT("TR.TOTAL"));
					$xTbl->addTD(getFMoney($total), " class='izq total' id='tdtotal' ");
					$xTbl->endRow();
					
					$xFRM->addSeccion("idddp", "TR.PAGO");
					$xFRM->addHElem( $xTbl->get() );
					$xFRM->endSeccion();
					
					
				}
			}
		}
	}
}

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
?>
<script>
var xG	= new Gen();
function jsActualizarTotal(){

	var tasaiva				= flotante($("#tasaiva").val());
	var montoseguro			= flotante($("#montoseguro_mny").val());
	//var montoseguro	= $("#montoseguro").val();
	var montoplacas			= flotante($("#montoplacas_mny").val());
	//var montoplacas	= $("#montoplacas").val();
	//var idleasing	= $("#idleasing").val();
	var anticiporentas		= flotante($("#anticiporentas").val());
	var comisionapertura	= flotante($("#comisionapertura").val());
	var rentadeposito		= flotante($("#rentadeposito").val());
	var rentaanticipada		= flotante($("#rentaanticipada").val());
	var ratificacion		= flotante($("#gastosnotariales").val());

	var baseiva				= montoseguro + montoplacas + anticiporentas + comisionapertura + rentaanticipada + ratificacion;
	var iva					= redondear((baseiva * tasaiva));
	var subtotal			= baseiva + rentadeposito;
	//setLog(montoseguro);
	$("#iva").val(iva);
	$("#subtotal").val(subtotal);

	$("#tdiva").html(getFMoney(iva));
	$("#tdsubtotal").html(getFMoney(subtotal));
	$("#tdtotal").html(getFMoney((iva+subtotal)));
	return true;
}

</script>
<?php
$xHP->fin();
?>