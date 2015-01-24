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

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); 
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);


$xHP->init();


$xFRM	= new cHForm("frmseguimiento_llamadas", "llamadas.frm.php");
if($credito > DEFAULT_CREDITO){
	
	$xCred		= new cCredito($credito);
	$xCred->init();
	$xFRM->addHTML($xCred->getFicha(true, "", false, true) );
	/* -----------------  -----------------------*/
	$clave		= parametro("idseguimiento_llamadas", null, MQL_INT);
	$xTabla		= new cSeguimiento_llamadas();
	if($clave != null){$xTabla->setData( $xTabla->query()->initByID($clave));}
	$xTabla->setData($_REQUEST);
	$clave		= parametro("id", null, MQL_INT);
	$xSel		= new cHSelect();
	if($clave == null){
		$step		= MQL_ADD;
		$clave		= $xTabla->query()->getLastID() + 1;
		$xTabla->idseguimiento_llamadas($clave);
	} else {
		$step		= MQL_MOD;
		if($clave != null){$xTabla->setData( $xTabla->query()->initByID($clave));}
	}
	$xFRM->setAction("llamadas.frm.php?action=$step");
	
	if($step == MQL_MOD){ $xFRM->addGuardar(); } else { $xFRM->addSubmit(); }
	$clave 		= parametro($xTabla->getKey(), null, MQL_INT);
	
	if( ($action == MQL_ADD OR $action == MQL_MOD) AND ($clave != null) ){
		$xTabla->setData( $xTabla->query()->initByID($clave));
		$xTabla->setData($_REQUEST);	
	
		if($action == MQL_ADD){
			$xTabla->query()->insert()->save();
		} else {
			$xTabla->query()->update()->save($clave);
		}
		$xFRM->addAvisoRegistroOK();
	}
	
	
	$xFRM->OMoneda("deuda_total", $xTabla->deuda_total()->v(), "TR.total");
	$xFRM->OText("telefono_uno", $xTabla->telefono_uno()->v(), "TR.telefono 1");
	$xFRM->OText("telefono_dos", $xTabla->telefono_dos()->v(), "TR.telefono 2");
	$xFRM->OText("fecha_llamada", $xTabla->fecha_llamada()->v(), "TR.fecha");
	$xFRM->OText("hora_llamada", $xTabla->hora_llamada()->v(), "TR.hora");
	$xFRM->OTextArea("observaciones", $xTabla->observaciones()->v(), "TR.observaciones");
	$xFRM->OSelect("estatus_llamada", $xTabla->estatus_llamada()->v() , "TR.estatus llamada", array("efectuado"=>"EFECTUADO", "cancelado"=>"CANCELADO", "pendiente"=>"PENDIENTE", "vencido"=>"VENCIDO"));
	
	$xFRM->OMoneda("grupo_relacionado", $xTabla->grupo_relacionado()->v(), "TR.grupo relacionado");
	
	$xFRM->OHidden("idseguimiento_llamadas", $xTabla->idseguimiento_llamadas()->v(), "TR.idseguimiento llamadas");
	$xFRM->OHidden("numero_socio", $xTabla->numero_socio()->v(), "TR.numero socio");
	$xFRM->OHidden("numero_solicitud", $xTabla->numero_solicitud()->v(), "TR.numero solicitud");
	$xFRM->OHidden("oficial_a_cargo", $xTabla->oficial_a_cargo()->v(), "TR.oficial a cargo");
	$xFRM->OHidden("sucursal", $xTabla->sucursal()->v(), "TR.sucursal");
	$xFRM->OHidden("eacp", $xTabla->eacp()->v(), "TR.eacp");
} else {
	$xFRM->addCreditBasico();
	$xFRM->addSubmit();
	
}
echo $xFRM->get();


//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>