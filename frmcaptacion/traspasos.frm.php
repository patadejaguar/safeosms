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
$xHP		= new cHPage("TR.TRASPASO ENTRE CUENTAS", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$xV			= new cReglasDeValidacion();
$xLog		= new cCoreLog();
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

$cuenta_origen	= parametro("cuentaorigen", 0, MQL_INT);
$cuenta_destin	= parametro("cuentadestino", 0, MQL_INT);


$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$observaciones	= parametro("idobservaciones");

$xHP->init();

$xFRM			= new cHForm("frm", "../frmcaptacion/traspasos.frm.php");
$xSel			= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xFRM->setNoAcordion();



if($action == SYS_NINGUNO){
	$xFRM->addSubmit();
	$xFRM->addPersonaBasico();
	$xFRM->setAction("../frmcaptacion/traspasos.frm.php?action=" . MQL_MOD, true);
} else {
	if($xV->cuenta($cuenta_destin) == true AND $xV->cuenta($cuenta_origen) == true){
		$xSoc	= new cSocio($persona);
		if($xSoc->init() == true){
			$run		= true;
			if($xV->cuenta($cuenta_destin) == false OR $xV->cuenta($cuenta_origen) == false){
				$run	= false;
				$xLog->add("WARN\tLa Cuenta de origen($cuenta_origen) y de destino($cuenta_destin) deben ser validas\r\n");
			}
			//valida si son iguales
			if($cuenta_destin == $cuenta_origen){
				$run	= false;
				$xLog->add("WARN\tLa Cuenta de Origen no debe ser la Misma que la destino\r\n");
			}
			$xCtaOrg	= new cCuentaDeCaptacion($cuenta_origen);
			if($xCtaOrg->init() == false){
				$run	= false;
				$xLog->add("WARN\tLa Cuenta de Origen debe existir\r\n");
			} else {
				if($xCtaOrg->getEsOperable($fecha) == false){
					$run	= false;
					$xLog->add("WARN\tLa Cuenta de Origen debe estar operativa\r\n");
				}
			}
			$xCtaDest	= new cCuentaDeCaptacion($cuenta_destin);
			if($xCtaDest->init() == false){
				$run		= false;
				$xLog->add("WARN\tLa Cuenta de Destino debe existir\r\n");
			} else {
				if($xCtaDest->getEsOperable($fecha) == false){
					$run	= false;
					$xLog->add("WARN\tLa Cuenta de Destino debe estar operativa\r\n");
				}
			}
			if($monto <= 0){
				$run	= false;
				$xLog->add("WARN\tEl Monto no es valido\r\n");
			}
			
			if($run == true){
				$tipoDestino	= $xCtaDest->getTipoDeCuenta();
				$tipoOrigen		= $xCtaOrg->getTipoDeCuenta();
				
				//setLog($tipoDestino);
				
				if ($tipoOrigen == CAPTACION_TIPO_PLAZO ){
					$xCOrigen	= new cCuentaInversionPlazoFijo($cuenta_origen, $persona);
				} else {
					$xCOrigen	= new cCuentaALaVista($cuenta_origen, $persona);
				}
				if($xCOrigen->init() == true){
					$res				= $xCOrigen->setTraspaso($cuenta_destin, $tipoDestino, $observaciones, $monto);
					if($res == true){
						$ReciboTrasp	= $xCOrigen->getReciboDeOperacion();
						$xRec			= new cReciboDeOperacion(false, false, $ReciboTrasp);
						
						$xCtaDest->init(false, true);
						$xCtaOrg->init(false, true);
						
						if($xRec->init() == true){
							$xFRM->addJsCode($xRec->getJsPrint());
							$xFRM->addPrintRecibo();
							$xFRM->addHElem($xSoc->getFicha(false, true, "", true));
							$xFRM->addHElem($xCtaOrg->getFicha(true));
							$xFRM->addHElem($xCtaDest->getFicha(true));
							$xFRM->addHElem($xRec->getFicha(true));
							$xFRM->addAvisoRegistroOK();
						}
					} else {
						$xLog->add($xCOrigen->getMessages());
						
						$xFRM->addAvisoRegistroError($xLog->getMessages());
					}
					
				}
			} else {
				$xFRM->addAvisoRegistroError($xLog->getMessages());
			}
			$xFRM->addCerrar();
		}//end socio
	} else {
		$xSoc	= new cSocio($persona);
		if($xSoc->init() == true){
			$xFRM->addGuardar();
			$xFRM->addHElem($xSoc->getFicha(false, true, "", true));
			$xFRM->OHidden("persona", $persona);
			$xFRM->setAction("../frmcaptacion/traspasos.frm.php?action=" . MQL_ADD, true);
			
			$xFRM->addSeccion("idcta", "TR.CUENTAS");
			
			//$xTxt->getDeCuentaCaptacion()
			$xFRM->addHElem($xSel->getListaDeCuentasCaptaPers("cuentaorigen", false, $persona)->get("TR.CUENTA ORIGEN", true));
			$xFRM->addHElem($xSel->getListaDeCuentasCaptaPers("cuentadestino", false, $persona)->get("TR.CUENTA DESTINO", true));
			
			$xFRM->setValidacion("cuentaorigen", "jsValidarCuenta", "TR.LA CUENTA DE ORIGEN Y DESTINO NO DEBE SER LA MISMA");
			$xFRM->setValidacion("cuentadestino", "jsValidarCuenta", "TR.LA CUENTA DE ORIGEN Y DESTINO NO DEBE SER LA MISMA");
			
			$xFRM->setValidacion("idmonto", "jsValidarMonto", "TR.LA CUENTA DE ORIGEN Y DESTINO NO DEBE SER LA MISMA");
			
			$xFRM->endSeccion();
			$xFRM->addSeccion("iddm", "TR.TRASPASO");
			$xFRM->addMonto();
			$xFRM->addObservaciones();
			$xFRM->endSeccion();
		}
	}
}

//$xFRM->addCreditBasico();



echo $xFRM->get();
//$jxc ->drawJavaScript(false, true);
?>
<script>
function jsValidarCuenta(){
	var res	= true;
	var org	= $("#cuentaorigen").val();
	var des	= $("#cuentadestino").val();
	if(org == des){
		$res	= false;
	}
	return res;
}
function jsValidarMonto(){
	var res	= true;

	return res;
}
</script>
<?php
$xHP->fin();
?>