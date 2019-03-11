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
$xHP		= new cHPage("", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();

$jxc 		= new TinyAjax();

function jsaSetActualizar($cuenta){
	$xCta	= new cCuentaDeCaptacion($cuenta);
	if($xCta->init() == true){
		$xCta->setCuandoSeActualiza();
	}
	return $xCta->getMessages(OUT_HTML);
}

function jsaSetRegenerarInteres($cuenta){
	$xCta	= new cCuentaDeCaptacion($cuenta);
	if($xCta->init() == true){
		$xUC	= new cUtileriasParaCaptacion();
		$msg	= $xUC->setRegenerarSDPM($xCta->getFechaDeApertura(), fechasys(), true, false, $xCta->getClaveDeCuenta());
	}
	return $msg;//$xCta->getMessages(OUT_HTML);
}


$jxc->exportFunction('jsaSetActualizar', array('idcuenta'), "#idmsg");
$jxc->exportFunction('jsaSetRegenerarInteres', array('idcuenta'), "#idmsg");
$jxc->process();


$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);$action	= strtolower($action);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$observaciones	= parametro("idobservaciones"); $observaciones	= parametro("observaciones", $observaciones);

$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

if($cuenta <= DEFAULT_CUENTA_CORRIENTE){
	$xFRM->addCuentaCaptacionBasico();
	$xFRM->addSubmit();
} else {
	$xCta	= new cCuentaDeCaptacion($cuenta);
	if($xCta->init() == true){
		$xFRM->OHidden("idcuenta", $cuenta);
		$xFRM->OHidden("idcuentacaptacion", $cuenta);
		

		$xFRM->OButton("TR.EDITAR", "var xG= new CaptGen();xG.setActualizarDatos($cuenta);", $xFRM->ic()->EDITAR, "idcmdedit", "yellow");
		if($xCta->isTipoVista() == true){
			if(MODO_DEBUG == true){
				$xFRM->OButton("TR.Regenerar Intereses", "jsaSetRegenerarInteres()", $xFRM->ic()->CALCULAR);
			}
		} else {
			//Inversion
		}
		
		
		/*if($numeroops <= 0){
			$xFRM->OButton("TR.ELIMINAR", "var xG=new Gen();xG.rmRecord({tabla:'captacion_cuentas', id:$cuenta})", $xFRM->ic()->ELIMINAR, "cmdeliminarcta", "red");
		}*/
	}
	$xFRM->addAviso("", "idmsg");
}

echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>