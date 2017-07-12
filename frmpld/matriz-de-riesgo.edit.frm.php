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
$xHP		= new cHPage("TR.EDITAR MATRIZRIESGO", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc 		= new TinyAjax();
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
$empresa	= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo		= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria = parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);

$observaciones= parametro("idobservaciones");

$xHP->init();

$xMat		= new cAMLMatrizDeRiesgo();

/* ===========		FORMULARIO EDICION 		============*/
$xTabla		= new cAml_riesgo_matrices();
$xTabla->setData( $xTabla->query()->initByID($clave));
$xSel		= new cHSelect();
$xFRM	= new cHForm("frmriesgomatriz", "matriz-de-riesgo.frm.php?action=$action");
$xFRM->setTitle($xHP->getTitle());

//Valores por defecto
$xTabla->usuario(getUsuarioActual());
$xTabla->tiempo(time());
//$xTabla->estatus(SYS_UNO);

//
$xSel4	= new cHSelect();
$xSel4->setDivClass("tx4 orange");
$xSel4->addOptions($xMat->getListaTopicosInArray());
$xSel3	= new cHSelect();
$opts3	= array(
		$xMat->TIPO_PERSONA => $xMat->TIPO_PERSONA,
		$xMat->TIPO_PRODUCTO => $xMat->TIPO_PRODUCTO,
		$xMat->TIPO_OPERACION => $xMat->TIPO_OPERACION
);

if(MODO_DEBUG == true){
	$xSel2	= new cHSelect();
	$xSel2->setDivClass("tx4 tx18 green");
	$opts2	= array("SISTEMA" => "SISTEMA", "USUARIO" => "USUARIO");
	$xSel2->addOptions($opts2);	
	$xFRM->addHElem($xSel2->get("define", "TR.DEFINE", $xTabla->define()->v()) );
	$xSel3->setDivClass("tx4 tx18 green");
	$xSel3->addOptions($opts3);
	$xFRM->addHElem($xSel3->get("clasificacion", "TR.CLASIFICACION", $xTabla->clasificacion()->v() ) );
	$xFRM->addHElem( $xSel4->get("nombre", "TR.NOMBRE", $xTabla->nombre()->v()) );
	
} else {
	$xTabla->define($xMat->DEF_USUARIO);
	$xFRM->OHidden("usuario", $xTabla->define()->v());
	$xFRM->OHidden("nombre", $xTabla->nombre()->v());
	$xFRM->OHidden("clasificacion", $xTabla->clasificacion()->v());
}

$xFRM->OText("descripcion", $xTabla->descripcion()->v(), "TR.DESCRIPCION");

$xFRM->addHElem( $xSel->getListaDeNivelDeRiesgo("riesgo", $xTabla->riesgo()->v())->get(true) );
$xFRM->addHElem( $xSel->getListaDeRiesgosDeProbabilidad("probabilidad", $xTabla->probabilidad()->v())->get(true) );
$xFRM->addHElem( $xSel->getListaDeRiesgosConsecuencias("consecuencia", $xTabla->consecuencia()->v())->get(true) );
$xFRM->addHElem( $xSel->getListaDeRiesgosAML("clave_riesgo",false, $xTabla->clave_riesgo()->v())->get(true) );

$xFRM->OSiNo("TR.FINALIZADOR","finalizador", $xTabla->finalizador()->v());

$xFRM->OHidden("idaml_riesgo_matrices", $xTabla->idaml_riesgo_matrices()->v());
$xFRM->OHidden("estatus", $xTabla->estatus()->v());
$xFRM->OHidden("usuario", $xTabla->usuario()->v());
$xFRM->OHidden("tiempo", $xTabla->tiempo()->v());


//$xFRM->addCRUD($xTabla->get(), true);
$xFRM->addCRUDSave($xTabla->get(), $clave, true);
echo $xFRM->get();


//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>