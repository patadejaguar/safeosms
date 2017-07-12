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
$xHP		= new cHPage("TR.DATOS DE COLEGIACION", HP_FORM);
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


$idtipomembresia	= parametro("idtipomembresia");
$iddiames			= parametro("iddiames");
$idtipolugarcobro	= parametro("idtipolugarcobro");
$idgradoacademico	= parametro("idgradoacademico");
$iddatosemergencia	= parametro("iddatosemergencia");
$idmembresia		= parametro("idcolegiacion");


$xHP->init();
$xSel		= new cHSelect();
$xFRM		= new cHForm("frm", "./");
$xFRM->setTitle($xHP->getTitle());
if($persona <= DEFAULT_SOCIO){
	$xFRM->addPersonaBasico();
	$xFRM->addSubmit();
} else {
	$xSoc	= new cSocio($persona);
	if($action == SYS_NINGUNO){
		
		if($xSoc->init() == true){
			$xFRM->addHElem($xSoc->getFicha(false, false, "", true));
			$xFRM->OHidden("idsocio", $persona);
			$xFRM->setAction("datos-de-colegiacion.frm.php?persona=$persona&action=". MQL_ADD);
			$xFRM->addGuardar();
			$xFRM->addHElem( $xSel->getListaDePersonasMembresia("idtipomembresia", $xSoc->getMembresiaTipo())->get("TR.TIPO_MEMBRESIA",true));
			$xFRM->addHElem( $xSel->getListaDeDiasDelMes("", $xSoc->getMembresiaDiaPag())->get("TR.DIA DE PAGO", true) );
			$xFRM->addHElem( $xSel->getListaDeTipoDeLugarDeCobro("", $xSoc->getMembresiaLugarPag())->get(true) );
			$xFRM->addHElem( $xSel->getListaDePersonasZClass("idgradoacademico", $xSoc->getMembresiaGrado())->get("TR.GRADO_ACADEMICO",true));
			$xFRM->OText("idcolegiacion", $xSoc->getMembresiaID(), "TR.IDCOLEGIACION");
			$xFRM->OText("iddatosemergencia", $xSoc->getMembresiaAcc(), "TR.DATO_EMERGENCIA");
		}
	} else {
		$xFRM->addCerrar("", 3);
		if($xSoc->init() == false){
			$ready	= false;
		} else {
			$ready	= $xSoc->setDatosColegiacion($idtipomembresia, $idtipolugarcobro, $iddiames, $idgradoacademico, $iddatosemergencia, $idmembresia);
			
		}
		$xFRM->setResultado($ready);
		$xFRM->addLog($xSoc->getMessages());
	}
}



echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>