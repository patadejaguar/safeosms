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
$xHP		= new cHPage("TR.PAGO POR MEMBRESIA", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$jxc 		= new TinyAjax();
$xSel		= new cHSelect();

function jsaAddCargos($membresia, $periocidad, $operacion, $monto, $fecha, $rotacion){
	$xPer	= new cPersonasMembresiasTipos($membresia);
	$xPer->addPerfil($operacion, $monto, $periocidad, $fecha, $rotacion);
}

function jsaListaDeCargos($idmem){
	$xLi	= new cSQLListas();
	$sql	= $xLi->getListadoDeEntidadPerfilCuotas("", " AND `tipo_de_membresia` =$idmem");
	$xT		= new cTabla($sql);
	
	$xT->setKeyTable("entidad_pagos_perfil");
	$xT->setKeyField("identidad_pagos_perfil");
	$xT->addEditar();
	$xT->addEliminar();
	
	return $xT->Show();
}

$jxc->exportFunction('jsaListaDeCargos', array('idmembresia'), "#idlista");
$jxc->exportFunction('jsaAddCargos', array('idmembresia', 'idperiocidad', 'idtipodeoperacion', 'idmonto', 'idfecha', 'idrotacion'), "#iddatos_pago");

$jxc->process();
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  

$xHP->init();

$xFRM			= new cHForm("frmpagospormembresia");

$xFRM->setNoAcordion();

$xFRM->addSeccion("iddivdatos", $xHP->getTitle());

//$xFRM->OButton("TR.Obtener", "jsRefreshGrid()", $xFRM->ic()->RECARGAR);

$xFRM->ODate("idfecha", false, "TR.AFECTACION DESDE");

$xFRM->addHElem($xSel->getListaDePersonasMembresia("idmembresia")->get(true));

$xFRM->addHElem($xSel->getListaDeTiposDeOperacion()->get(true));
$xFRM->addHElem($xSel->getListaDePeriocidadDePago()->get(true));
$xFRM->addMonto();

$xFRM->OText("idrotacion", "0", "TR.PERIOCIDAD DE ROTACION");

$xFRM->setValidacion("idmonto", $xFRM->VALIDARCANTIDAD, "TR.MONTO_INVALIDO");

$xFRM->endSeccion();

/*

$xHG			= new cHGrid("idgridcuotas", "TR.CUOTAS");
$xHG->setSQL($xLi->getListadoDeEntidadPerfilCuotas("", " AND `tipo_de_membresia` =?"));
$xHG->addList();
$xHG->addKey("clave");

*/

//$xFRM->addSeccion("iddivresultados", "TR.RESULTADOS");
$xFRM->addGuardar("jsGuardar()");

/*
$xHG->col("operacion", "TR.OPERACION", "10%");
$xHG->col("periocidad", "TR.PERIOCIDAD", "10%");
$xHG->col("monto", "TR.MONTO", "10%");
$xHG->col("rotacion", "TR.ROTACION", "10%");
$xFRM->addHElem($xHG->getDiv());
*/
$xFRM->addHTML("<div id='idlista'></div>");

//$xFRM->endSeccion();
echo $xFRM->get();

/*echo $xHG->getJsHeaders();
echo $xHG->getJs(false, true);*/
?>
<script>
var xG	= new Gen();
function jsRefreshGrid(){
	var tipoM	= $("#idmembresia").val();
	//jsLGidgridcuotas("&vars=" + tipoM);
	jsaListaDeCargos();
	$("#id-frm").trigger("reset");
}
function jsGuardar(){
	var idmonto	= $("#idmonto").val();
	if(flotante(idmonto)>0){
		jsaAddCargos();
		xG.spin({callback:jsRefreshGrid});
	} else {
		xG.alerta({msg:"MONTO_INVALIDO"});
	}
}
</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>