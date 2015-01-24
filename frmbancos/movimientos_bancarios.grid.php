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
$xHP		= new cHPage("TR.Movimientos Bancarios", HP_GRID);


$xF		= new cFecha();
$out 		= (isset($_GET["out"])) ? $_GET["out"] : SYS_DEFAULT;

$mx 		= (isset($_GET["mx"])) ? true : false;
if($mx == true){
	$fechaInicial	= (isset($_GET["on"])) ? $xF->getFechaISO( $_GET["on"]) : FECHA_INICIO_OPERACIONES_SISTEMA;
	$fechaFinal	= (isset($_GET["off"])) ? $xF->getFechaISO( $_GET["off"]) : fechasys();
} else {
	$fechaInicial	= (isset($_GET["on"])) ? $_GET["on"] : FECHA_INICIO_OPERACIONES_SISTEMA;
	$fechaFinal	= (isset($_GET["off"])) ? $_GET["off"] : fechasys();
}


$cuenta				= (isset($_GET["cuenta"]))  ? $_GET["cuenta"] : SYS_TODAS;
$operacion			= (isset($_GET["operacion"])) ? $_GET["operacion"] : SYS_TODAS;
$busqueda			= parametro("busqueda", "", MQL_STRING);

$ByCuenta			= ($cuenta != SYS_TODAS AND $cuenta != "") ? " AND `bancos_operaciones`.`cuenta_bancaria`=$cuenta " : "";
$ByOperaciones		= ($operacion != SYS_TODAS AND $operacion != "") ? " AND `bancos_operaciones`.`tipo_operacion`='$operacion' " : "";
$ByBusqueda			= ($busqueda == "") ? "" : " AND (`bancos_operaciones`.`beneficiario`  LIKE '%$busqueda%' ) ";

	$filtro1			= "";
	$filtro2			= "";
		
	$xHP->setNoDefaultCSS();
	echo $xHP->getHeader(true);
	//HTML Object END
	echo '<body onmouseup="SetMouseDown(false);" ><div id="onGrid">';
        // Define your grid
	$_SESSION["grid"]->SetDatabaseConnection(MY_DB_IN, USR_DB, PWD_DB);
	//Propiedades del GRID
	$mGridTitulo		= $xHP->getTitle();
	$mGridKeyField		= "idcontrol";	//Nombre del Campo Unico
	$mGridKeyEdit		= true;					//Es editable el Campo
	$mGridTable		= "bancos_operaciones";	//Nombre de la tabla
	$mGridSQL			= "idcontrol, fecha_expedicion, recibo_relacionado, monto_descontado, monto_real, clave_de_conciliacion, beneficiario";
	$mGridWhere		= " (`bancos_operaciones`.`fecha_expedicion`>= '$fechaInicial' ) AND (`bancos_operaciones`.`fecha_expedicion`<= '$fechaFinal' ) $ByCuenta $ByOperaciones $ByBusqueda ";
	//exit($mGridWhere);
	//exit($mGridWhere);
	//, tipo_operacion, cuenta_bancaria, fecha_expedicion, 
	//layout: [Campo] => Titulo, Editable, Tamaño
	/*"tipo_operacion" => "Tipo,true,8",
	 "cuenta_bancaria" => "Cuenta, true,10",
	"fecha_expedicion" => "Fecha,false,10",*/
	$mGridProp			= array(
						"idcontrol" => "Clave,false,4",
						"fecha_expedicion" => "Fecha,true,10",
						"recibo_relacionado" => "Recibo,true,10",
						"monto_descontado" => "Descuento,true,10",
						"monto_real" => "Monto,true,10",
						"clave_de_conciliacion" => "Conc.,true,5",
						"beneficiario" => "Beneficiario,true,20"
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
			
			if ( isset($mVals[0]) ) {
				$_SESSION["grid"]->SetDatabaseColumnName($key, $mVals[0]);
			}
			
			if ( isset($mVals[1]) ) {
				$_SESSION["grid"]->SetDatabaseColumnEditable($key, $mVals[1]);
			}
			if ( isset($mVals[2]) ) {
				$_SESSION["grid"]->SetDatabaseColumnWidth($key, $mVals[2]);
			}	
		}
	//===========================================================================================================
	$_SESSION["grid"]->SetMaxRowsEachPage(25);
	$_SESSION["grid"]->PrintGrid(MODE_EDIT);

echo $xHP->end();
?>