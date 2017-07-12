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
$xHP		= new cHPage("TR.ABRIR_SESSION DE CAJA", HP_FORM);

$msg		= "";
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);

$xHP->init();
$xFRM		= new cHForm("frmabrir", "abrir_caja.frm.php");
$xFRM->setTitle($xHP->getTitle());
$xSel		= new cHSelect();
$txtP		= new cHText();

if($action == SYS_UNO){
	$oficial_s	= parametro("oficial", "", MQL_RAW);
	$pwd		= parametro("password", "", MQL_RAW);
	$fondos		= parametro("fondodecaja", 0, MQL_FLOAT);
	//$pwd		= strtolower($pwd);
	//Definir bien los PWD
	$cUsr		= new cSystemUser($oficial_s, false);
	$pwd		= $cUsr->getHash($pwd, false);
	$sucess		= $cUsr->getCompareData("contrasenna", $pwd);
	
	$cUsr->init();
	
	if ( $sucess == true ){
		$IOficial	= $cUsr->getID();
		if($fondos < 0){
			$msg		.= "ERROR\tFondos menores a los establecido $fondos \r\n";
		} else {
		
			$cCj 	= new cCaja(false, $fecha);
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
		$msg	.= "ERROR\tERROR AL INICIAR LA SESSION DE CAJA\r\n";
		
	}
	$xFRM->setResultado($sucess, $msg, $msg);
	$xFRM->addCerrar();
} else {
	if(PERMITIR_EXTEMPORANEO == true){
		$xFRM->addFecha($fecha);
	}
	$xFRM->setAction("abrir_caja.frm.php?action=1");
	$txtP->addEvent("var xG = new Gen(); xG.inputMD5(this);", "onchange");
	$xFRM->OText_13("oficial", "", "TR.JEFE_DE_CAJA");
	$xFRM->addHElem( $txtP->getPassword("password", "TR.Password", "") );
	$xFRM->OMoneda("fondodecaja", 0, "TR.FONDO DE CAJA", true);
	$xFRM->addSubmit();
}
echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>