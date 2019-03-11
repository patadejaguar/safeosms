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
$xHP		= new cHPage("Carga Articulo 69B del CFF", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc 		= new TinyAjax();
//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);$action	= strtolower($action);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$observaciones	= parametro("idobservaciones"); $observaciones	= parametro("observaciones", $observaciones);

$xHP->init();

$xFRM		= new cHForm("frmfrm69b", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

$arrEstat	= array(
	"presunto" => 1,
	"desvirtuado" => 2,
	"definitivo" => 3
);

function catCreateIfNotExists($tabla, $nombre, $id = 'NULL'){
	$xDD	= new cSQLTabla($tabla);
	$xQL	= new MQL();
	

	$items		= 0;
	$d			= $this->getDataRow("SELECT * FROM `$tabla` WHERE ");
	if(isset($d["items"])){
		$items	= $d["items"];
	}
}

//No.|RFC|Nombre del Contribuyente|Situación del contribuyente|Número y fecha de oficio global de presunción
//|Publicación página SAT presuntos|Número y fecha de oficio global de presunción|Publicación DOF presuntos|Publicación página
if($action == SYS_NINGUNO){
	
	
	//$xFRM->addHElem("<div class='tx4'><label for='f1'>" . $xFRM->lang("archivo") . "</label><input type='file'  name='f1' id='f1'  /></div>");
	$xFRM->OFileText("f1");
	
	$xFRM->addHElem( $xSel->getListaDePersonasXClass("classx")->get("TR.CLASE X", true) );
	$xFRM->addHElem( $xSel->getListaDePersonasYClass("classy")->get("TR.CLASE Y", true) );
	$xFRM->addHElem( $xSel->getListaDePersonasZClass("classz")->get("TR.CLASE Z", true) );
	
	
	$xFRM->addObservaciones();
	
	$xFRM->OCheck("TR.LIMPIAR BASE_DE_DATOS", "idlimpiardb");
	$xFRM->addSubmit();
	

} else {
	$doc1				= (isset($_FILES["f1"])) ? $_FILES["f1"] : false;
	
	
	
	$xFil				= new cFileImporter();
	//$xFil->setCharDelimiter("\",");
	$xFil->setCharDelimiter("|");
	$xTmp				= new cTmp();
	$xFil->setForceClean(true);
	//$xFil->setArrClean(array('/"/', "/=/"));
	$clave_de_actividad	= 9411998;
	//var_dump($_FILES["f1"]);
	if($limpiardb == true){
		
	}
	
	
	$xFRM->addCerrar();
}


echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>