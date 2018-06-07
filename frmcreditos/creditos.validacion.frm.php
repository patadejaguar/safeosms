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
$xHP		= new cHPage("TR.Validacion de Credito", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$jxc 		= new TinyAjax();


function jsaRevalidar($idcredito, $pasocredito){
	$xCred	= new cCredito($idcredito);
	$msg	= "";
	if($xCred->init() == true){
		$msg	= $xCred->setVerificarValidez(OUT_HTML, $pasocredito);
	}
	return $msg;
}

$jxc ->exportFunction('jsaRevalidar', array('idcredito', 'idpasocredito'), "#idmsg");


$jxc ->process();
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto			= parametro("monto",0, MQL_FLOAT); $monto		= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo		= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo		= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$observaciones	= parametro("idobservaciones");

$pasocredito	= parametro("pasocredito", 0, MQL_INT);

$xHP->init();

$xFRM			= new cHForm("frm", "./");
$xSel			= new cHSelect();
$xT				= new cHTabla();
$xFRM->setTitle($xHP->getTitle());
//$xFRM->addJsBasico();
if($credito <= DEFAULT_CREDITO){
	$xFRM->addCreditBasico();
	$xFRM->addSubmit();
} else {
	$xFRM->addCerrar();
	
	$xCred	= new cCredito($credito);
	
	$xFRM->OHidden("idcredito", $credito);
	$xFRM->OHidden("idpasocredito", $pasocredito);
	
	if($xCred->init() == true){
		$xFRM->OButton("TR.REVALIDAR", "jsaRevalidar()", $xFRM->ic()->VALIDAR);
		//titulos
		$xT->initRow();
		$xT->addTH("TR.TIPO");
		$xT->addTH("TR.TOPICO");
		$xT->addTH("TR.CUMPLE");
		$xT->endRow();	
		$xImg	= new cHImg();
		$xBtn	= new cHButton();
		
		$xVal	= new cReglasDeCalificacion();
		$xVal->setPersona($xCred->getClaveDePersona());		
		$vals	= $xVal->getValoresDeCalificacion();
			
		foreach ($vals as $k => $v){
			$xT->initRow();
			$img	= ($v == 1) ? $xBtn->setIcon("fa-check", "fa-lg") : $xBtn->setIcon("fa-close", "fa-lg");
			$prop	= ($v == 1) ? " class='success' " : " class='error' ";
			$xT->addTD($xBtn->setIcon("fa-user", "fa-lg"), $prop);
			$xT->addTD($xFRM->l()->getMensajeByTop($k), $prop);			
			$xT->addTD($img, $prop);
			$xT->endRow();
		}
				
		$xVal	= new cReglasDeCalificacion();
		$xVal->setCredito($credito);
		
		$vals	= $xVal->getValoresDeCalificacion();
			
		foreach ($vals as $k => $v){
			$xT->initRow();

			$img	= ($v == 1) ? $xBtn->setIcon("fa-check", "fa-lg") : $xBtn->setIcon("fa-close", "fa-lg");
			$prop	= ($v == 1) ? " class='success' " : " class='error' ";
			$xT->addTD($xBtn->setIcon("fa-money", "fa-lg"), $prop);
			$xT->addTD($xFRM->l()->getMensajeByTop($k), $prop);			
			$xT->addTD($img, $prop);
			$xT->endRow();
		}
		
		$xFRM->addAviso("", "idmsg");
		
	} else {
		$xFRM->addAvisoRegistroOK();
	}
	//Valores
/*	$xT->initRow();
	foreach ($vals as $k => $v){
	
	}
	$xT->endRow();*/	
}
$xFRM->addHTML($xT->get());
echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>