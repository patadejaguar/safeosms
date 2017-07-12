<?php
//=====================================================================================================
//=====>	INICIO_H
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	$theFile					= __FILE__;
	$permiso					= getSIPAKALPermissions($theFile);
	if($permiso === false){		header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//<=====	FIN_H
//=====================================================================================================
$xInit      = new cHPage("", HP_SERVICE );
$txt		= "";
$ql			= new MQL();
$lis		= new cSQLListas();
$xF			= new cFecha();
$rs			= array();

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$nombre		= parametro("nombre");
$direccion	= parametro("direccion");
$telefono	= parametro("telefono", 0, MQL_INT);
$error		= false;

header('Content-type: application/json');
//$banco, $limite_de_credito = 0,$tipo_de_cuenta ="", $fecha_de_emision = false, $numero_cuenta = "", $numero_tarjeta = ""
//$empresa, $direccion, $telefono = 0, $observaciones =""
if($persona > DEFAULT_SOCIO){
	$xPer	= new cSocio($persona);
	if($xPer->init() == true){
		$xRel	= new cPersonasRelaciones(0, $persona);
		if($xRel->addRelacionComercial($nombre, $direccion, $telefono) == false){
			$error	= true;
			$txt	.= "ERROR\tNo Se agrega la relacion Bancaria $persona\r\n";
			if(MODO_DEBUG == true){ $txt .= $xRel->getMessages(); }
		} else {
			$error	= false;
			$txt	.= "OK\tRegistro Satisfactorio\r\n";
		}
	} else {
		$error	= true;
		$txt	.= "ERROR\tNo existe la persona $persona\r\n";
	}
	if(MODO_DEBUG == true){ $txt .= $xPer->getMessages(); }
}
$rs["error"]		= $error;
$rs[SYS_MSG]		= $txt;
$rs["messages"]		= $txt;

echo json_encode($rs);
?>