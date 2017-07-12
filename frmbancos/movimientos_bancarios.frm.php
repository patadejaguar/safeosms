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
$xHP		= new cHPage("TR.Operaciones Bancarias", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
//$cuenta_bancaria	= para
$operacion	= parametro("idtipooperacionbanco", BANCOS_OPERACION_DEPOSITO, MQL_RAW);
$recibo		= parametro("idrecibo", 1, MQL_INT); $recibo = parametro("item", $recibo, MQL_INT); $recibo	= parametro("recibo", $recibo, MQL_INT);
$monto		= parametro("idmonto", 0, MQL_FLOAT);
$monto		= parametro("monto", $monto, MQL_FLOAT);

$documento	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $documento = parametro("idsolicitud", $documento, MQL_INT); $documento = parametro("solicitud", $documento, MQL_INT);
$documento	= parametro("cuenta", $documento, MQL_INT); $documento = parametro("idcuenta", $documento, MQL_INT);
$documento	= parametro("iddocumento", $documento, MQL_INT);

$numero_de_cuenta		= parametro("idcodigodecuenta", 0, MQL_INT);
$descuento				= parametro("iddescuento",0, MQL_FLOAT);
$beneficiario			= "";
$tituloDoc				= "TR.Documento";
$origen					= parametro("origen", 0, MQL_INT);
$idorigen				= parametro("idorigen", 0, MQL_INT);
$idobservaciones		= parametro("idobservaciones");


if($recibo > 1 AND $recibo!= DEFAULT_RECIBO  ){
	$xRec		= new cReciboDeOperacion(false, true, $recibo);
	if($xRec->init() == true){
		$monto			= $xRec->getTotal();
		$documento		= $xRec->getCodigoDeDocumento();
		$fecha			= $xRec->getFechaDeRecibo();
		$operacion		= BANCOS_OPERACION_DEPOSITO;
		$persona		= $xRec->getCodigoDeSocio();
		$xSoc			= new cSocio($persona, true);
		$beneficiario	= $xSoc->getNombreCompleto();
	}
} else {
	if($documento > DEFAULT_CREDITO){
		$xCred			= new cCredito($documento);
		if($xCred->init() == true){
			$operacion		= BANCOS_OPERACION_CHEQUE;
			$persona		= $xCred->getClaveDePersona();
			$xSoc			= new cSocio($persona, true);
			$beneficiario	= $xSoc->getNombreCompleto();
			$fecha			= $xCred->getFechaDeMinistracion();
			$monto			= $xCred->getMontoAutorizado();
		}
	}
}

$xHP->init();

$xFRM			= new cHForm("frm", "movimientos_bancarios.frm.php?action=" . MQL_ADD);
$xSel			= new cHSelect();

if($action == MQL_ADD){
	if($monto > 0 AND $numero_de_cuenta > 0 ){
		$xBanc			= new cCuentaBancaria($numero_de_cuenta);
		$r				= $xBanc->addOperacion($operacion, $documento, $recibo, $beneficiario, $monto, $persona, $fecha, "", false, $descuento);
		if($r== false){
			$xFRM->addAvisoRegistroError();
		} else {
			$xFRM->addAvisoRegistroOK();
			switch ($origen){
				case iDE_PRESUPUESTO:
					//Actualizar Presupuesto a Pagado
					$sql	= $xLi->getListadoDePresupuestoPorPagar($persona);
					$rs		= $xQL->getDataRecord($sql);
					foreach ($rs as $rw){
						$idx	= $rw["control"];
						$xDep	= new cCreditosPresupuestoDetalle($idx);
						if( $xDep->init() == true){
							if($xDep->setPagado($documento, $idobservaciones, $fecha) == false){
								$xFRM->addAvisoRegistroError();
							}
						} else {
							$xFRM->addAvisoRegistroError();
						}
						//$uql	= "UPDATE `creditos_destino_detallado` SET `estado_actual`=1 WHERE `idcreditos_destino_detallado`=$idx";
						//$xQL->setRawQuery($uql);
						//TODO: Agregar memo
						//ID de cheque en todos los pagos
						//setLog($uql);
					}
					break;
			}
			//Agregar Impŕimir
			
		}
	} else {
		$xFRM->addAvisoRegistroOK();
	}
}

$xFRM->OHidden("idorigen", $idorigen);
$xFRM->OHidden("origen", $origen);
if($operacion == BANCOS_OPERACION_CHEQUE){
	$tituloDoc		= "TR.Numero de Cheque";
}
$xFRM->setTitle($xHP->getTitle());
if($persona > DEFAULT_SOCIO){
	$xSoc	= new cSocio($persona);
	if($xSoc->init() == true){
		$xFRM->addHElem( $xSoc->getFicha() );
	}
} else {
	$xFRM->addPersonaBasico("", false, $persona, "", "TR.Nombre del Beneficiario");
}
if($monto > 0){
	$xFRM->OHidden("idmonto", $monto);
	$xCat	= new cCantidad($monto);
	$xFRM->addHElem($xCat->getFicha());
} else {
	$xFRM->addMonto($monto, true);
}

if($operacion == BANCOS_OPERACION_DEPOSITO){
	$xFRM->addHElem( $xSel->getListaDeTiposDeOperacionesBancarias("", $operacion)->get(true) );
} else {
	$xFRM->OHidden("idtipooperacionbanco", $operacion);
}

$xFRM->addFecha($fecha);
$xFRM->addHElem($xSel->getListaDeCuentasBancarias("", true, $numero_de_cuenta)->get(true) );

$xFRM->OText("iddocumento", $documento, $tituloDoc);

$xFRM->OMoneda("idrecibo", $recibo, "TR.Recibo Relacionado");

$xFRM->addObservaciones("", $idobservaciones);
$xFRM->addGuardar();
echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);

$xHP->fin();
?>