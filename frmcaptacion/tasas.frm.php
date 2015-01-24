<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
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
$xHP		= new cHPage("TR.Tasa de captacion", HP_GRID);


$xF			= new cFecha();
$out 		= (isset($_GET["out"])) ? $_GET["out"] : SYS_DEFAULT;
$mx 		= (isset($_GET["mx"])) ? true : false;
if($mx == true){
	$fechaInicial	= (isset($_GET["on"])) ? $xF->getFechaISO( $_GET["on"]) : FECHA_INICIO_OPERACIONES_SISTEMA;
	$fechaFinal		= (isset($_GET["off"])) ? $xF->getFechaISO( $_GET["off"]) : fechasys();
} else {
	$fechaInicial	= (isset($_GET["on"])) ? $_GET["on"] : FECHA_INICIO_OPERACIONES_SISTEMA;
	$fechaFinal		= (isset($_GET["off"])) ? $_GET["off"] : fechasys();
}


$cuenta				= (isset($_GET["cuenta"]))  ? $_GET["cuenta"] : SYS_TODAS;
$operacion			= (isset($_GET["operacion"])) ? $_GET["operacion"] : SYS_TODAS;
$busqueda			= parametro("busqueda", "", MQL_STRING);

$ByCuenta			= ($cuenta != SYS_TODAS AND $cuenta != "") ? " AND `bancos_operaciones`.`cuenta_bancaria`=$cuenta " : "";
$ByOperaciones		= ($operacion != SYS_TODAS AND $operacion != "") ? " AND `bancos_operaciones`.`tipo_operacion`='$operacion' " : "";
$ByBusqueda			= ($busqueda == "") ? "" : " AND (`bancos_operaciones`.`beneficiario`  LIKE '%$busqueda%' ) ";

$filtro1			= "";
$filtro2			= "";

echo $xHP->getHeader(true);

//HTML Object END
echo '<body onmouseup="SetMouseDown(false);" ><div id="onGrid">';
// Define your grid
$_SESSION["grid"]->SetDatabaseConnection(MY_DB_IN, USR_DB, PWD_DB);
//Propiedades del GRID
$mGridTitulo		= $xHP->getTitle();
$mGridKeyField		= "idcaptacion_tasas";	//Nombre del Campo Unico
$mGridKeyEdit		= true;					//Es editable el Campo
$mGridTable			= "captacion_tasas";	//Nombre de la tabla
$mGridSQL			= "	`captacion_tasas`.`idcaptacion_tasas`,
	`captacion_tasas`.`tasa_efectiva`,
	`captacion_tasas`.`modalidad_cuenta`,
		`captacion_tasas`.`subproducto`,
	`captacion_tasas`.`monto_mayor_a`,
	`captacion_tasas`.`monto_menor_a`,
	`captacion_tasas`.`dias_mayor_a`,
	`captacion_tasas`.`dias_menor_a`
	 ";
$mGridWhere			= "";
//exit($mGridWhere);
//exit($mGridWhere);
//, tipo_operacion, cuenta_bancaria, fecha_expedicion,
//layout: [Campo] => Titulo, Editable, Tamaño
/*"tipo_operacion" => "Tipo,true,8",
 "cuenta_bancaria" => "Cuenta, true,10",
"fecha_expedicion" => "Fecha,false,10",*/
$mGridProp			= array(
				"idcaptacion_tasas"	=> "Clave,false",
				"tasa_efectiva"	=> "Tasa,true",
				"modalidad_cuenta"	=> "Tipo,true",
				"subproducto" 	=> "Producto,true",
				"monto_mayor_a"	=> "Montos Mayores a",
				"monto_menor_a"	=> "Montos Menores a",
				"dias_mayor_a"	=> "Dias Mayores a",
				"dias_menor_a"	=> "Dias Menores a"
				
						);
	//Obtiene el Grid de la Tabla de general_description
/*
if( $mGridTable != "" ){
$xTs		= new cTableStructure($mGridTable);
//$mGridSQL	= $xTs->getCampos_InText();
$xAF		= explode(",", $mGridSQL);

foreach ($xAF as $key => $value) {
$DField							= $xTs->getInfoField( trim($value) );
$mGridProp[ $DField["campo"] ]	=  $DField["titulo"]  .",true," . $DField["longitud"] ;
}
unset($xAF, $key, $value);
}
*/
//===========================================================================================================

$_SESSION["grid"]->SetSqlSelect($mGridSQL, $mGridTable, $mGridWhere);
$_SESSION["grid"]->SetUniqueDatabaseColumn($mGridKeyField, $mGridKeyEdit);
$_SESSION["grid"]->SetTitleName($mGridTitulo);
$_SESSION["grid"]->SetEditModeAdd(false);
//$_SESSION["grid"]->SetEditModeDelete(false);
//var_dump( $_SESSION["grid"] );
//===========================================================================================================
foreach ($mGridProp as $key => $value) {
$mVals		= explode(",", $value, 10);
	
if ( isset($mVals[0]) ) { $_SESSION["grid"]->SetDatabaseColumnName($key, $mVals[0]); }
	if ( isset($mVals[1]) ) { $_SESSION["grid"]->SetDatabaseColumnEditable($key, $mVals[1]); }
	if ( isset($mVals[2]) ) { $_SESSION["grid"]->SetDatabaseColumnWidth($key, $mVals[2]);	}
}
//===========================================================================================================
$_SESSION["grid"]->SetMaxRowsEachPage(25);
$_SESSION["grid"]->PrintGrid(MODE_EDIT);

echo $xHP->end();
?>