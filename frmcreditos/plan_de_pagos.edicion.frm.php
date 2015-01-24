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
	$theFile			= __FILE__;
	$permiso			= getSIPAKALPermissions($theFile);
	if($permiso === false){	header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//=====================================================================================================
$xHP		= new cHPage("Editar Recibo", HP_GRID);
$plan		= ( isset($_REQUEST["i"]) ) ? $_REQUEST["i"] : 0;

	$xHP->setNoDefaultCSS();
	echo $xHP->getHeader(true);
	//HTML Object END
	
	echo '<body onmouseup="SetMouseDown(false);" ><div id="onGrid">';
        // Define your grid
	$_SESSION["grid"]->SetDatabaseConnection(MY_DB_IN, USR_DB, PWD_DB, WORK_HOST);
	//Propiedades del GRID
	$mGridTitulo		= "Editar Recibo";
	$mGridKeyField		= "idoperaciones_mvtos";	//Nombre del Campo Unico
	$mGridKeyEdit		= true;					//Es editable el Campo
	$mGridTable			= "operaciones_mvtos";	//Nombre de la tabla
	$mGridSQL			= "
	`operaciones_mvtos`.`idoperaciones_mvtos`,
	`operaciones_mvtos`.`fecha_operacion`,
	`operaciones_mvtos`.`fecha_afectacion`,
	`operaciones_mvtos`.`tipo_operacion`,
	`operaciones_mvtos`.`afectacion_real`,
	`operaciones_mvtos`.`periodo_socio`,
	`operaciones_mvtos`.`saldo_anterior`,
	`operaciones_mvtos`.`saldo_actual`
	";
	$mGridWhere			= " (`operaciones_mvtos`.`recibo_afectado` =$plan)";
	//layout: [Campo] => Titulo, Editable, Tamaño
	$mGridProp			= array(
						"idoperaciones_mvtos" => "Codigo,false,10",
						"fecha_operacion" => "Generado,true,12",
						"fecha_afectacion" => "Afectado,true,12",
						"tipo_operacion" => "Operacion,true,4",
						"afectacion_real" => "Monto,true,12",
						"periodo_socio" => "Periodo,true,3",
						"saldo_anterior" => "Anterior,true,14",
						"saldo_actual" => "Saldo,true,14"
						);
	//Obtiene el Grid de la Tabla de general_description
	if( $mGridTable != "" ){
		/*$xTs			= new cTableStructure($mGridTable);
		$mGridSQL		= $xTs->getCampos_InText();
		$xAF			= explode(",", $mGridSQL);
		
		foreach ($xAF as $key => $value) {
			$DField							= $xTs->getInfoField( trim($value) );
			$mGridProp[ $DField["campo"] ]	=  $DField["titulo"]  .",true," . $DField["longitud"] ; 
		}
		unset($xAF, $key, $value);*/
	}
	//===========================================================================================================
	
	$_SESSION["grid"]->SetSqlSelect($mGridSQL, $mGridTable, $mGridWhere);
	$_SESSION["grid"]->SetUniqueDatabaseColumn($mGridKeyField, $mGridKeyEdit);
	$_SESSION["grid"]->SetTitleName($mGridTitulo);
	
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

	//Create the grid.
//}
echo "</div></body>";
echo $xHP->end();
?>