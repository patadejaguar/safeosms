<?php
/**
 *  @see        Formulario avanzado de seguimiento_llamadas
 *  @since    2008-05-10 23:32
 *  @author    PHP Form Wizard V 0.75 - Balam Gonzalez Luis (2007)
 **/
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
$xHP		= new cHPage("TR.Llamadas", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$fecha				= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);
$persona			= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito			= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta				= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback			= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$idfecha			= parametro("idfecha", false, MQL_DATE);
$idhora				= parametro("idhora");
$idmonto			= parametro("idmonto", 0, MQL_FLOAT);
$idtelefono1		= parametro("idtelefono1");
$idtelefono2		= parametro("idtelefono2");
$idoficial			= parametro("idoficial", 0, MQL_INT);
$idobservaciones	= parametro("idobservaciones");

//$idestado			= parametro("idestadodellamada");
$xHP->init();


$xFRM		= new cHForm("frmllamadaslista", "llamadas.frm.php?action=" . MQL_ADD);
$xFRM->setTitle($xHP->getTitle());
$xSel		= new cHSelect();
$xDat		= new cHDate();
$msg		= "";
if($credito > DEFAULT_CREDITO){
	$xCred	= new cCredito($credito);
	if($xCred->init() == true){
		$persona	= $xCred->getClaveDePersona();
		$idoficial	= $xCred->getClaveDeOficialDeCredito();
		$xFRM->addHElem($xCred->getFichaMini());
	}
}
//=== Aqui vamos a insertar
if($action == MQL_ADD AND $credito > DEFAULT_CREDITO){
	$xCall	= new cLlamadas();
	$rs		= $xCall->add($credito, $idfecha, $idhora, false, $idobservaciones, $idoficial, $idtelefono1, $idtelefono2);
	$xFRM->setResultado($rs, "", "", true);
	
}
if($persona > DEFAULT_SOCIO AND $credito > DEFAULT_CREDITO){
	$xFRM->OHidden("credito",$credito);
	$xFRM->OHidden("persona",$persona);	
} else {
	//$xFRM->addJsBasico();
	$xFRM->addCreditBasico();	
}
if($idtelefono1 == ""){
	$xPer		= new cSocio($persona);
	if($xPer->init() == true){
		$idtelefono1	= $xPer->getTelefonoPrincipal();
	}
}

$xFRM->ODate("idfecha", $idfecha, "TR.Fecha");
$xFRM->addHElem( $xSel->getListaDeHoras("", $idhora)->get(true) );

//$xFRM->addHElem( $xSel->getListaDeEstadoDeLlamada("", $idestado)->get(true) );
if($persona > DEFAULT_SOCIO AND $credito > DEFAULT_CREDITO){
	$xSelT1	= $xSel->getListaDeTelefonosPorPersona($persona, "idtelefono1", $idtelefono1);
	$tel1	= $xSelT1->get(true);
	$tel2	= $xSel->getListaDeTelefonosPorPersona($persona, "idtelefono2", $idtelefono2)->get(true);
	if($xSelT1->getCountRows()<=0){
		$xFRM->OTelefono("idtelefono1", $idtelefono1, "TR.Telefono 1");
		$xFRM->setValidacion("idtelefono1", "validacion.nozero");
		
		$xFRM->OTelefono("idtelefono2", $idtelefono2, "TR.Telefono 2");
	} else {
		$xFRM->addHElem( $tel1 );
		$xFRM->addHElem( $tel1 );
	}

} else {
	$xFRM->OTelefono("idtelefono1", $idtelefono1, "TR.Telefono 1");
	$xFRM->setValidacion("idtelefono1", "validacion.nozero");
	
	$xFRM->OTelefono("idtelefono2", $idtelefono2, "TR.Telefono 2");
}

//$xFRM->addHElem( $xSel->getListaDeOficiales("", SYS_USER_ESTADO_ACTIVO, $idoficial)->get(true) );
$xFRM->OHidden("idoficial", $idoficial);

$xFRM->addObservaciones();
$xFRM->addGuardar();

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>