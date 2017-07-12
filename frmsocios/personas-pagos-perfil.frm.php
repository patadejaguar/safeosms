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
$xHP		= new cHPage("TR.PERFIL DE APORTACIONES", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$jxc 		= new TinyAjax();

function jsaCrearPerfil($persona){
	$xSoc	= new cSocio($persona);
	if($xSoc->init() == true){
		$membresia = $xSoc->getTipoDeMembresia();
		$xPP	= new cPersonasPerfilDePagos();
		$xPP->setCrearPorPersonaMembresia($persona, $membresia);
	}
}

$jxc ->exportFunction('jsaCrearPerfil', array('idpersona'), "#idaviso");
$jxc ->process();
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
$xHP->addJTableSupport();
$xHP->init();



$xFRM		= new cHForm("frmperfilpagos", "personas-pagos-perfil.frm.php?action=$action");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

$xFRM->addCerrar();




/* ===========		GRID JS		============*/

$xHG	= new cHGrid("iddivperfil",$xHP->getTitle());

$xHG->setSQL($xLi->getListadoDePersonaPerfilCuotas($persona));

$xHG->addList();
$xHG->addKey("idpersonas_pagos_perfil");
//$xHG->col("clave_de_persona", "TR.CLAVE DE PERSONA", "10%");
//$xHG->col("fecha_de_aplicacion", "TR.FECHA DE APLICACION", "10%");
$xHG->col("prioridad", "TR.ORDEN", "10%");
$xHG->col("tipo_de_operacion", "TR.OPERACION", "10%");
$xHG->col("periocidad", "TR.PERIOCIDAD", "10%");
$xHG->col("monto", "TR.MONTO", "10%");

//$xHG->col("rotacion", "TR.ROTACION", "10%");

$xFRM->OHidden("idpersona", $persona);

$xHG->OToolbar("TR.AGREGAR", "jsAdd($persona)", "grid/add.png");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.clave +')", "edit.png");
$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.clave +')", "delete.png");
$xFRM->addHElem("<div id='iddivperfil'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );
$xFRM->addAviso("", "idaviso");
$xFRM->OButton("TR.GENERAR PERFIL", "jsaCrearPerfil()", $xFRM->ic()->EJECUTAR);

echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsEdit(id){
	xG.w({url:"../frmsocios/personas-pagos-perfil.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivperfil});
}
function jsAdd(idpersona){
	xG.w({url:"../frmsocios/personas-pagos-perfil.new.frm.php?persona="+idpersona, tiny:true, callback: jsLGiddivperfil});
}
function jsDel(id){
	xG.rmRecord({tabla:"personas_pagos_perfil", id:id, callback:jsLGiddivperfil});
}
</script>
<?php
	


$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>