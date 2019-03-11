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
$xHP		= new cHPage("TR.CARGOS POR GASTOS_DE_COBRANZA", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto		= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT);
$recibo		= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$parcialidad	= parametro("idparcialidad", 1, MQL_INT);
$observaciones	= parametro("idobservaciones");

$xHP->init();

$xFRM		= new cHForm("frmgtoscbza", "./frmgastosdecobranza.php");
$xFRM->setTitle($xHP->getTitle());
//$xFRM->addJsBasico();
if($credito <= DEFAULT_CREDITO){
	$xFRM->addCreditBasico();
	$xFRM->addSubmit();
} else {
	$xCred		= new cCredito($credito);
	if($xCred->init() == true){
		if($action == SYS_NINGUNO){
			$xFRM->setAction("./frmgastosdecobranza.php?action=" . MQL_ADD);
			$xFRM->addGuardar();
			$xFRM->addHElem( $xCred->getFicha(true, "", false, true) );
			$xFRM->OHidden("idsolicitud", $credito);
			$xFRM->addFecha();
			if($xCred->getPeriocidadDePago() == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO ){
				//
				$xTxt		= new cHText();
				//$xTxt->setDivClass("");
				$xFRM->addHElem( $xTxt->getNumero("idparcialidad", $xCred->getPeriodoActual()+1, "TR.Numero de Parcialidad") );
			} else {
				$plan		= $xCred->getNumeroDePlanDePagos();
				if($plan != false){
				$xPlan		= new cPlanDePagos($plan); $xPlan->init();
				$parcs		= $xPlan->getParcsPendientes();
				//$txt		= "";
				$arrD		= array();
					foreach ($parcs as $p){
						//setLog( $p[SYS_NUMERO]. " " . $xF->getFechaDDMM($p[SYS_FECHA]) . " ". getFMoney($p[SYS_TOTAL]));
						if( setNoMenorQueCero($p[SYS_TOTAL]) > 0){ $arrD[$p[SYS_NUMERO]]	= $p[SYS_NUMERO]. " " . $xF->getFechaDDMM($p[SYS_FECHA]) . " ". getFMoney($p[SYS_TOTAL]); }
					}
					$xSel		= new cHSelect();
					$xSel->addOptions($arrD);
					//$xSel->setEnclose(false);
					$xFRM->addHElem( $xSel->get("idparcialidad", "TR.Numero de Parcialidad", $xCred->getPeriodoActual()+1));
				} else {
					if(MODO_CORRECION == true){
								$xTxt		= new cHText();
								//$xTxt->setDivClass("");
								$xFRM->addHElem( $xTxt->getNumero("idparcialidad", $xCred->getPeriodoActual()+1, "TR.Numero de Parcialidad") );
					}
				}
			}
			$xFRM->addMonto();
			$xFRM->addObservaciones();
										
		} else {
			//Agregar
			$xFRM->addCerrar();
			$xPagos	= new cCreditosPagos($credito);
			$xPagos->init();
			$recibo	= $xPagos->addCargosCobranza($monto, $parcialidad, $observaciones, TESORERIA_COBRO_NINGUNO, $fecha);
			$xRec	= new cReciboDeOperacion(false, false, $recibo);
			if($xRec->init() == true){
				$xFRM->addHElem( $xRec->getFicha(true) );
				$xFRM->addAvisoRegistroOK();
				$xFRM->addImprimir();
				$xFRM->addJsCode($xRec->getJsPrint());
			}
		}
	}
}

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();

?>