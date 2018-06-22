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
$xHP		= new cHPage("TR.VINCULAR PERSONA", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();

$jxc 		= new TinyAjax();
//$tab 		= new TinyAjaxBehavior();

//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();



function jsaVincularPersona($tipo, $RelPersona, $monto, $consanguinidad, $vinculado, $depende = false){
	$xSoc	= new cSocio($vinculado);
	$xT		= new cTipos();
	$depende= $xT->cBool($depende);
	
	if($xSoc->init() == true){
		$xSoc->addRelacion($RelPersona, $tipo, $consanguinidad, $depende);
	}
	return $xSoc->getMessages(OUT_HTML);
}
		
$jxc ->exportFunction('jsaVincularPersona', array('idtipoderelacion', 'idsocio', 'idmonto', 'idtipodeparentesco', 'idpersonarelacionado', 'dependiente'), "#idmsgs");
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

$observaciones	= parametro("idobservaciones");

$personarel		= parametro("idpersonarelacionado", 0, MQL_INT);

$xHP->init();

$xFRM		= new cHForm("frmvincularpersona", "./");
$xSel		= new cHSelect();
$xRels		= new cPersonasRelaciones(false, false);

$xFRM->setTitle($xHP->getTitle());


$xHSel		= new cHSelect(); $xChk	= new cHCheckBox(); $xText	= new cHText();

$xFRM->addPersonaBasico();
$xFRM->addGuardar("jsVincularPersona()");

$xVinc		= new cSocio($personarel);
if($xVinc->init() == true){

	$EsFisica		= $xVinc->getEsPersonaFisica();
	
	
	$xFRM->addHElem( $xHSel->getListaDeTiposDeRelaciones2("", false, $EsFisica )->get(true));
	
	if($EsFisica == false){
		$xFRM->OHidden("idtipoparentesco", $xRels->CONSANGUINIDAD_NINGUNA);
		$xFRM->OHidden("depende", "0");
		
	} else {
		$xFRM->addHElem( $xChk->get("TR.es dependiente_economico", "dependiente") );
		$xFRM->addHElem( $xHSel->getListaDeTiposDeParentesco("", false, DEFAULT_TIPO_CONSANGUINIDAD)->get(true)  );
	}
		
}

$xFRM->OHidden("idpersonarelacionado", $personarel);
$xFRM->addAviso("");


echo $xFRM->get();

?>
<script>
var xG	= new Gen();
function jsVincularPersona(){
	session(TINYAJAX_CALLB, "jsClose()");
	jsaVincularPersona();
}
function jsClose(){ xG.close(); }
</script>
<?php

$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>