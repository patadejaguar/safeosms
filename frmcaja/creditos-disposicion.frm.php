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
$xHP		= new cHPage("TR.DISPOSICION DE CREDITO", HP_FORM);
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
$empresa	= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT);
$grupo		= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria = parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);

$observaciones= parametro("idobservaciones");

$cheque 		= parametro("cheque", DEFAULT_CHEQUE);
$comopago 		= parametro("ctipo_pago", DEFAULT_TIPO_PAGO, MQL_RAW);
$foliofiscal 	= parametro("foliofiscal", DEFAULT_RECIBO_FISCAL);

$CuentaBancaria	= parametro("idcodigodecuenta", 0, MQL_INT);

$xHP->init();

$xFRM		= new cHForm("frm", "creditos-disposicion.frm.php");
$xSel		= new cHSelect();
$xCob		= new cHCobros();
$xFRM->setTitle($xHP->getTitle());

//$xFRM->addJsBasico();
if($credito <= DEFAULT_CREDITO){
	$xFRM->addCreditBasico();
	$xFRM->addSubmit();
} else {
	if($action == SYS_NINGUNO){
		$xCred	= new cCredito($credito);
		if($xCred->init() == true){
			$xFRM->setAction("creditos-disposicion.frm.php?action=" . MQL_ADD);
			$xFRM->addGuardar();
			$xFRM->addAtras();
			$xFRM->addHElem($xCred->getFichaMini());
			//====================== Agregar controles
			$xFRM->addFechaRecibo();
			
			$xFRM->addHElem( $xCob->get(TESORERIA_TIPO_EGRESOS) );
			$xFRM->addHElem($xSel->getListaDeCuentasBancarias("", true)->get(true));
			$xFRM->OMoneda("idmonto", 0, "TR.MONTO DE DISPOSICION");
			//$xFRM->addMonto(0, true)
			$xFRM->addObservaciones();
			
			$xFRM->OHidden("credito", $credito);
		} else {
			$xFRM->addAtras();
			
		}
	} else {
		$xCred	= new cCredito($credito);
		if($xCred->init() == true){
			$xFRM->addHElem($xCred->getFichaMini());
			//Moneda, periodo, recibo
			$idrecibo	= $xCred->setDispocision($monto, $comopago, $cheque, $fecha, $observaciones, $CuentaBancaria, $foliofiscal);
			$xRec		= new cReciboDeOperacion(false, false, $idrecibo);
			if($xRec->init() == true){
				$xFRM->addHTML($xRec->getJsPrint(true));
				$xFRM->addImprimir();
			}
		}
		$xFRM->addAvisoRegistroOK($xCred->getMessages());
		$xFRM->addCerrar();
	}
}

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>