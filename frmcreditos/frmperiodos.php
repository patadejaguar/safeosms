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
$xHP		= new cHPage("TR.CREDITOS_PERIODOS", HP_FORM);
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
$observaciones	= parametro("idobservaciones");

$xHP->init();



/* ===========		FORMULARIO		============*/
$clave		= parametro("idcreditos_periodos", null, MQL_INT);
$xTabla		= new cCreditos_periodos();
if($clave != null){$xTabla->setData( $xTabla->query()->initByID($clave));}
$xTabla->setData($_REQUEST);
$clave		= parametro("id", null, MQL_INT);
$xSel		= new cHSelect();
$xF			= new cFecha();

if($clave == null){
	$step		= MQL_ADD;
	$clave		= $xTabla->query()->getLastID() + 1;
	$xTabla->idcreditos_periodos($clave);
	$xTabla->fecha_inicial($xF->getFechaInicialDelAnno());
	$xTabla->fecha_final($xF->getFechaFinAnnio());
	$xTabla->fecha_reunion($xF->setSumarDias(1, $xF->getFechaFinAnnio()));
	
} else {
	$step		= MQL_MOD;
	if($clave != null){$xTabla->setData( $xTabla->query()->initByID($clave));}
}
$xFRM	= new cHForm("frmcreditos_periodos", "frmperiodos.php?action=$step");
$xFRM->setTitle($xHP->getTitle());

if($step == MQL_MOD){ } else { }
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
	$xFRM->addCerrar();
} else {
	$xFRM->addGuardar();
	$xFRM->OMoneda("idcreditos_periodos", $xTabla->idcreditos_periodos()->v(), "TR.CLAVE");
	$xFRM->OText("descripcion_periodos", $xTabla->descripcion_periodos()->v(), "TR.DESCRIPCION");
	$xFRM->ODate("fecha_inicial", $xTabla->fecha_inicial()->v(), "TR.FECHA_INICIAL");
	$xFRM->ODate("fecha_final", $xTabla->fecha_final()->v(), "TR.FECHA_FINAL");
	$xFRM->ODate("fecha_reunion", $xTabla->fecha_reunion()->v(), "TR.FECHA_REUNION");

	$xFRM->addHElem($xSel->getListaDeOficiales("periodo_responsable","", $xTabla->periodo_responsable()->v())->get(true) );
}
echo $xFRM->get();



//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>