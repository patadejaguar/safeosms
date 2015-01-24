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
$xHP		= new cHPage("TR.Tipo de Operaciones", HP_GRID);
$xF			= new cFecha();
$tipo		= parametro("tipo", SYS_TODAS);
	$xTabla				= new cOperaciones_tipos();

	$aGridSQL[SYS_TODAS]	= "idoperaciones_tipos,descripcion_operacion,clasificacion,subclasificacion,cuenta_contable,descripcion,recibo_que_afecta,tipo_operacion,visible_reporte,class_efectivo,mvto_que_afecta,afectacion_en_recibo,afectacion_en_notificacion,producto_aplicable,constituye_fondo_automatico,integra_vencido,afectacion_en_sdpm,cargo_directo,codigo_de_valoracion,periocidad_afectada,integra_parcialidad,es_estadistico,formula_de_calculo,formula_de_cancelacion,importancia_de_neutralizacion,preservar_movimiento,tasa_iva,nombre_corto,estatus";	
	$aGridSQL["GENERAL"]	= "idoperaciones_tipos,descripcion_operacion,clasificacion,subclasificacion,descripcion,tasa_iva,nombre_corto,estatus";
	$aGridSQL["CLASE"]		= "idoperaciones_tipos,descripcion_operacion,clasificacion,subclasificacion,recibo_que_afecta,tipo_operacion,visible_reporte,class_efectivo,mvto_que_afecta,afectacion_en_recibo,afectacion_en_notificacion,producto_aplicable,constituye_fondo_automatico,integra_vencido,afectacion_en_sdpm,cargo_directo,periocidad_afectada,integra_parcialidad,es_estadistico";
	$aGridSQL["FORMULAS"]	= "idoperaciones_tipos,descripcion_operacion,formula_de_calculo,formula_de_cancelacion,importancia_de_neutralizacion,preservar_movimiento";
	$aGridSQL["CONTABLE"]	= "idoperaciones_tipos,descripcion_operacion,cuenta_contable";
	$xHP->setNoDefaultCSS();
	echo $xHP->getHeader(true);
	
	//HTML Object END
	echo '<body onmouseup="SetMouseDown(false);" ><div id="onGrid">';
        // Define your grid
	$_SESSION["grid"]->SetDatabaseConnection(MY_DB_IN, USR_DB, PWD_DB);
	//Propiedades del GRID
	$mGridTitulo		= $xHP->getTitle();
	$mGridKeyField		= $xTabla->getKey();	//Nombre del Campo Unico
	$mGridKeyEdit		= true;					//Es editable el Campo
	$mGridTable			= $xTabla->get();	//Nombre de la tabla
	$mGridSQL			= ($tipo == SYS_TODAS) ? "*" : $aGridSQL[$tipo];
	$mGridWhere			= "";

	$mGridProp			= array(
	"idoperaciones_tipos" 		=> "Codigo,false,4",
	"descripcion_operacion" 	=> "Nombre,true,40",
	"clasificacion" 			=> "Tipo,true,4",
	"subclasificacion" 			=> "Sub-Tipo,true,4",
	"cuenta_contable" 			=> "Cuenta Contable,true,100",
	"descripcion" 				=> "Descripcion,true,50",
	"recibo_que_afecta" 		=> "Recibo,true,2",
	"tipo_operacion" 			=> "Codigo,true,2",
	"visible_reporte" 			=> "En Reporte,true,2",
	"class_efectivo" 			=> "Tipo Efvo,true,2",
	"mvto_que_afecta" 			=> "Operacion,true,2",
	"afectacion_en_recibo" 		=> "Tipo en recibo,true,2",
	"afectacion_en_notificacion" => "Notificacion,true,2",
	"producto_aplicable" 		=> "Producto,true,2",
	"constituye_fondo_automatico" => "Tipo en revolvente,true,2",
	"integra_vencido" 			=> "Vencido,true,2",
	"afectacion_en_sdpm" 		=> "SDPM,true,2",
	"cargo_directo" 			=> "cargo directo,true,2",
	"codigo_de_valoracion"		=> "codigo de valoracion,true,0",
	"periocidad_afectada" 		=> "periocidad afectada,true,4",
	"integra_parcialidad" 		=> "integra parcialidad,true,4",
	"es_estadistico" 			=> "Estadistico,true,4",
	"formula_de_calculo" 		=> "formula de calculo,true,40",
	"formula_de_cancelacion" 	=> "formula de cancelacion,true,40",
	"importancia_de_neutralizacion" => "Cancelar,true,4",
	"preservar_movimiento" 		=> "Preservar,true,0",
	"tasa_iva" 					=> "IVA,true,6",
	"nombre_corto" 				=> "Seudonimo,true,15",
	"estatus" 					=> "Estado,true,2"
						);
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
			if ( isset($mVals[1]) ) { $_SESSION["grid"]->SetDatabaseColumnEditable($key, $mVals[1]); }
			if ( isset($mVals[0]) ) { $_SESSION["grid"]->SetDatabaseColumnName($key, $mVals[0]); 	}
			if ( isset($mVals[2]) ) { $_SESSION["grid"]->SetDatabaseColumnWidth($key, $mVals[2]);	}	
		}
	//===========================================================================================================
	$_SESSION["grid"]->SetMaxRowsEachPage(30);
	$_SESSION["grid"]->PrintGrid(MODE_EDIT);

echo $xHP->end();
?> 