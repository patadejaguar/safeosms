<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package operaciones
 * @subpackage grids
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
$xHP		= new cHPage("TR.Tipo_de Operaciones", HP_GRID);
$xF			= new cFecha();
$xL			= new cLang();
$tipo		= parametro("tipo", SYS_TODAS);
	$xTabla				= new cOperaciones_tipos();

	$aGridSQL[SYS_TODAS]	= "idoperaciones_tipos,descripcion_operacion,clasificacion,subclasificacion,cuenta_contable,descripcion,recibo_que_afecta,tipo_operacion,visible_reporte,class_efectivo,mvto_que_afecta,afectacion_en_recibo,afectacion_en_notificacion,producto_aplicable,constituye_fondo_automatico,integra_vencido,afectacion_en_sdpm,codigo_de_valoracion,periocidad_afectada,integra_parcialidad,es_estadistico,formula_de_calculo,formula_de_cancelacion,importancia_de_neutralizacion,preservar_movimiento,tasa_iva,nombre_corto,estatus";	
	$aGridSQL["GENERAL"]	= "idoperaciones_tipos,tipo_operacion,descripcion_operacion,nombre_corto,descripcion,estatus";
	$aGridSQL["CLASE"]		= "idoperaciones_tipos,descripcion_operacion,clasificacion,subclasificacion,visible_reporte,class_efectivo,mvto_que_afecta,afectacion_en_notificacion,producto_aplicable,constituye_fondo_automatico,integra_vencido,afectacion_en_sdpm,periocidad_afectada,integra_parcialidad,es_estadistico";	
	$aGridSQL["CLASERECIBOS"]		= "idoperaciones_tipos,descripcion_operacion,recibo_que_afecta,afectacion_en_recibo";
	$aGridSQL["FORMULAS"]		= "idoperaciones_tipos,descripcion_operacion,precio,tasa_iva,cargo_directo,formula_de_calculo";
	$aGridSQL["CANCELACION"]	= "idoperaciones_tipos,descripcion_operacion,importancia_de_neutralizacion,preservar_movimiento,formula_de_cancelacion";
	$aGridSQL["CONTABLE"]	= "idoperaciones_tipos,descripcion_operacion,cuenta_contable";
	$xHP->setNoDefaultCSS();
	echo $xHP->getHeader(true);
	
	//HTML Object END
	echo '<body onmouseup="SetMouseDown(false);" >';
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
	"idoperaciones_tipos" 		=> "TR.Codigo,false,250",
	"descripcion_operacion" 	=> "TR.Nombre,true,300",
	"clasificacion" 			=> "TR.Tipo,true,40",
	"subclasificacion" 			=> "TR.SUBTIPO,true,40",
	"cuenta_contable" 			=> "TR.Cuenta Contable,true,300",
	"descripcion" 				=> "TR.Descripcion,true,300",
	"recibo_que_afecta" 		=> "TR.TIPO_DE Recibo,true,80",
	"tipo_operacion" 			=> "TR.Repetir Codigo,false,40",
	"visible_reporte" 			=> "TR.MOSTRAR Reporte,true,40",
	"class_efectivo" 			=> "TR.CLASE EFECTIVO,true,40",
	"mvto_que_afecta" 			=> "TR.Operacion,true,40",
	"afectacion_en_recibo" 		=> "TR.Afectacion recibo,true,40",
	"afectacion_en_notificacion" => "TR.Notificacion,true,40",
	"producto_aplicable" 		=> "TR.Producto,true,40",
	"constituye_fondo_automatico" => "TR.Tipo en revolvente,true,40",
	"integra_vencido" 			=> "TR.Vencido,true,40",
	"afectacion_en_sdpm" 		=> "TR.SDPM,true,40",
	"cargo_directo" 			=> "TR.cargo directo,true,40",
	"codigo_de_valoracion"		=> "TR.FORMULA de valoracion,true,100",
	"periocidad_afectada" 		=> "TR.periocidad,true,40",
	"integra_parcialidad" 		=> "TR.integra parcialidad,true,40",
	"es_estadistico" 			=> "TR.Estadistico,true,40",
	"formula_de_calculo" 		=> "TR.formula_de_calculo,true,300",
	"formula_de_cancelacion" 	=> "TR.formula_de_cancelacion,true,400",
	"importancia_de_neutralizacion" => "TR.ORDEN,true,40",
	"preservar_movimiento" 		=> "TR.GUARDAR,true,40",
	"tasa_iva" 					=> "TR.IVA,true,40",
	"nombre_corto" 				=> "TR.NOMBRE_CORTO,true,120",
	"estatus" 					=> "TR.Estatus,true,40",
	"precio" 					=> "TR.PRECIO,true,80"
						);
	//===========================================================================================================
	
	$_SESSION["grid"]->SetSqlSelect($mGridSQL, $mGridTable, $mGridWhere);
	$_SESSION["grid"]->SetUniqueDatabaseColumn($mGridKeyField, $mGridKeyEdit);
	$_SESSION["grid"]->SetTitleName($mGridTitulo);
	$_SESSION["grid"]->SetEditModeAdd(false);
	$_SESSION["grid"]->SetEditModeDelete(false);
	//var_dump( $_SESSION["grid"] );
	//===========================================================================================================
	$DCols		= explode(",", $mGridSQL);					
		foreach ($mGridProp as $key => $value) {
			$mVals		= explode(",", $value, 3);
			if(in_array($key, $DCols)){
				if ( isset($mVals[0]) ){ $_SESSION["grid"]->SetDatabaseColumnName($key, $xL->getT($mVals[0]));	}
				if ( isset($mVals[1]) ){ $_SESSION["grid"]->SetDatabaseColumnEditable($key, $mVals[1]); }
				if ( isset($mVals[2]) ){ $_SESSION["grid"]->SetDatabaseColumnWidth($key, $mVals[2]); }
			}	
		}
	//===========================================================================================================
	$_SESSION["grid"]->SetMaxRowsEachPage(30);
	$_SESSION["grid"]->PrintGrid(MODE_EDIT);

$xHP->fin();
?>