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
	$permiso			= getSIPAKALPermissions($theFile); //subitem 7200
	if($permiso === false){	header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//=====================================================================================================
$xHP		= new cHPage("", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();

$xAmlCat	= new cAMLRiesgos();


//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); 
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->init();

/* -----------------  -----------------------*/
$clave 	= parametro("clave_de_control", null, MQL_INT);
$xTabla	= new cAml_risk_catalog();
if($clave != null){$xTabla->setData( $xTabla->query()->initByID($clave));}
$xTabla->setData($_REQUEST);
$clave	= parametro("id", null, MQL_INT);
$xSel	= new cHSelect();
if($clave == null){
	$step		= MQL_ADD;
	$clave		= $xTabla->query()->getLastID() + 1;
} else {
	$step		= MQL_MOD;
	if($clave != null){$xTabla->setData( $xTabla->query()->initByID($clave));}
}
$xFRM	= new cHForm("frmaml_risk_catalog", "catalogo.riesgos.editar.frm.php?action=$step");
$xFRM->addSubmit();

if($action == MQL_ADD){		//Agregar
	$clave 		= parametro($xTabla->getKey(), null, MQL_INT);
	if($clave != null){ 
		$xTabla->setData( $xTabla->query()->initByID($clave)); 
		$xTabla->setData($_REQUEST);
		$xTabla->query()->insert()->save();
		$xFRM->addAvisoRegistroOK();
	}	
} else if($action == MQL_MOD){		//Modificar
	//iniciar
	$clave 		= parametro($xTabla->getKey(), null, MQL_INT);
	if($clave != null){ 
		$xTabla->setData( $xTabla->query()->initByID($clave)); 
		$xTabla->setData($_REQUEST);
		$xTabla->query()->update()->save($clave);
		$xFRM->addAvisoRegistroOK();
	}
}
$xSel1	= $xSel->getListaDeTipoDeRiesgoEnAML("tipo_de_riesgo");
$xSel1->setOptionSelect($xTabla->tipo_de_riesgo()->v());
$xSel2	= $xSel->getListaDeUnidadMedidaAML("unidad_de_medida");
$xSel2->setOptionSelect($xTabla->unidad_de_medida()->v(OUT_TXT));
$xSel3	= $xSel->getListaDeFormaReportaRiesgo("forma_de_reportar");
$xSel3->setOptionSelect($xTabla->forma_de_reportar()->v(OUT_TXT));
$xSel4	= $xSel->getListaDeFrecuenciaChequeoRiesgo("frecuencia_de_chequeo");
$xSel4->setOptionSelect($xTabla->frecuencia_de_chequeo()->v(OUT_TXT));

$xFRM->OMoneda("clave_de_control", $xTabla->clave_de_control()->v(), "TR.clave de control");
$xFRM->OText("descripcion", $xTabla->descripcion()->v(), "TR.descripcion");
$xFRM->addHElem( $xSel1->get(true) );
$xFRM->OMoneda("valor_ponderado", $xTabla->valor_ponderado()->v(), "TR.valor ponderado");
$xFRM->OMoneda("unidades_ponderadas", $xTabla->unidades_ponderadas()->v(), "TR.unidades ponderadas");
$xFRM->addHElem( $xSel2->get(true) );
$xFRM->addHElem( $xSel3->get(true) );
$xFRM->addHElem( $xSel4->get(true) );
$xFRM->OTextArea("fundamento_legal", $xTabla->fundamento_legal()->v(), "TR.fundamento legal");

echo $xFRM->get();


//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>