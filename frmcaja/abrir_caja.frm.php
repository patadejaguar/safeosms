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

$msg		= "";
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->init();

if($action == SYS_UNO){
	//		$oficial	= trim(substr($_POST["cOficialDeApertura"], 0, 15));
	//		$pwd		= trim( md5( substr($_POST["cOficialClave"],0,20) ) );
	$oficial_s	= parametro("oficial", "", MQL_RAW);
	$pwd		= parametro("password", "", MQL_RAW);
	$fondos		= parametro("fondodecaja", 0, MQL_FLOAT);
	$pwd		= strtolower($pwd);
	//Definir bien los PWD
	$cUsr		= new cSystemUser($oficial_s, false);
	$sucess		= $cUsr->getCompareData("contrasenna", $pwd);
	
	$cUsr->init();
	
	if ( $sucess == true ){
		$IOficial	= $cUsr->getID();
		if($fondos <= 0){
			$msg		.= "ERROR\tFondos menores a los establecido $fondos \r\n";
		} else {
		
			$cCj 	= new cCaja();
			$ropen	= $cCj->setOpenBox($IOficial, $fondos);
			if($ropen == true ){
				$msg	.= "OK\tLa Caja esta abierta\r\n";
			} else {
				if(MODO_DEBUG == true){  setLog($cCj->getMessages()); }
				$msg	.= "ERROR\tProblemas al iniciar la Caja\r\n";
			}
			
			if(MODO_DEBUG == true){ $msg	.= $cCj->getMessages(OUT_TXT);	}
		}
	} else {
		if(MODO_DEBUG == true){ $msg	.= $cUsr->getMessages(OUT_TXT);	}
		$msg		.= "WARN\tERROR AL INICIAR LA SESSION DE CAJA\r\n";
	}
}

$xFRM		= new cHForm("frmabrir", "abrir_caja.frm.php?action=1");
$xBtn		= new cHButton();		
$xTxt		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();
$txtP		= new cHText();

$txtP->addEvent("var xG = new Gen(); xG.inputMD5(this);", "onchange");

//$xFRM->addJsBasico();
//$xFRM->addCreditBasico();
$xFRM->addHElem( $xTxt->getNormal("oficial", "", "TR.Usuario superior") );
$xFRM->addHElem( $txtP->getPassword("password", "TR.Password", "") );
$xFRM->addHElem( $xTxt->getDeMoneda("fondodecaja", "TR.Fondo de Caja", 0, true) );
$xFRM->addAviso($msg);
$xFRM->addSubmit();

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();


//} else {
	//evaluar al usuario

//}
?>