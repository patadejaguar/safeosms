<?php
//=====================================================================================================
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	$permiso = getSIPAKALPermissions(__FILE__);
	if($permiso === false){
		saveError(999, $_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Acceso no permitido a :" . addslashes(__FILE__));
		header ("location:404.php?i=999");
	} else {
        //$_SESSION["current_file"]   = addslashes(__FILE__);
    }
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../core/core.config.inc.php");

include_once("../core/core.creditos.inc.php");
include_once("../core/core.creditos.utils.inc.php");

include_once("../core/core.html.inc.php");
include_once("../core/core.captacion.inc.php");
include_once("../core/core.common.inc.php");
include_once("../core/core.operaciones.inc.php");

include_once("../core/core.contable.inc.php");

$oficial 	= elusuario($iduser);
$parent 	= ( isset($_POST["c"]) ) ? $_POST["c"] : false;

$txtBuscar	= ( isset($_POST["cBuscar"]) ) ? $_POST["cBuscar"]: "";

$xHP		= new cHPage();

$xHP->setTitle("Editar/Agregar Cajas Locales");



/*if($parent == false){

	echo $xHP->getHeader();

echo '
<body>
<fieldset>
	<legend>Editar Configuracion del Sistema</legend>

<form name="frmmenu" method="POST" action="./socios_caja_local.frm.php">
	<table>
		<tr>
			<td>';*/


	/*$sqlMost 	= "SELECT tipo, CONCAT(tipo, ' (' , COUNT(nombre_del_parametro), ')' ) AS 'conceptos'
					    FROM entidad_configuracion
					GROUP BY tipo
					ORDER BY tipo ";
	$cSel 		= new cSelect("cmenu", "idmenu", $sqlMost);
	$cSel->setEsSql();
	$cSel->addEspOption("todas", "TODAS");
	$cSel->setOptionSelect("todas");
	echo $cSel->show();*/

	/*echo '			</td>
			<td>
				Buscar Texto Parecido a:</td>
			<td>
				<input type="text" id="idBuscar" name="cBuscar" />
			</td>
			<td>
				<input type="submit" value="Mostrar" />
			</td>
	
		</tr>

	</table>
</form>
</fieldset>';

} else {*/
	
	include(GRID_SOURCE."class/gridclasses.php"); //Include the grid engine.

	@session_start();

	//Define identifying name(s) to your grid(s). Must be unqiue name(s).
	$grid_id = array("grid");

	//Remember to comment the again line when publishing PHP Grid, or else PHP Grid wont remember the settings between page loads.
	unset($_SESSION["grid"]);
	include(GRID_SOURCE . "class/gridcreate.php"); //Creates grid objects.
	$filtro1			= "";
	$filtro2			= "";
		

	//HTML Object Init	
	$xHP->addJsFile(GRID_SOURCE . "javascript/javascript.js");
	$xHP->addCSS("../css/grid.css");
	$xHP->setNoDefaultCSS();
	$xHP->addJsFile(GRID_SOURCE . "server.php?client=all");
	$xHP->addHSnip("<script> HTML_AJAX.defaultServerUrl = '" . GRID_SOURCE. "server.php';	</script>");

	echo $xHP->getHeader(true);
	//HTML Object END
	
	echo '<body onmouseup="SetMouseDown(false);" ><div id="onGrid">';
        // Define your grid
	$_SESSION["grid"]->SetDatabaseConnection(MY_DB_IN, USR_DB, PWD_DB);
	//Propiedades del GRID
	$mGridTitulo		= "Cajas Locales";
	$mGridKeyField		= "idsocios_cajalocal";	//Nombre del Campo Unico
	$mGridKeyEdit		= true;					//Es editable el Campo
	$mGridTable			= "socios_cajalocal";	//Nombre de la tabla
	$mGridSQL			= "*";
	$mGridWhere			= "";
	//layout: [Campo] => Titulo, Editable, Tamaño
	$mGridProp			= array();
	//Obtiene el Grid de la Tabla de general_description
	if( $mGridTable != "" ){
		$xTs			= new cTableStructure($mGridTable);
		$mGridSQL		= $xTs->getCampos_InText();
		$xAF			= explode(",", $mGridSQL);
		
		foreach ($xAF as $key => $value) {
			$DField							= $xTs->getInfoField( trim($value) );
			$mGridProp[ $DField["campo"] ]	=  $DField["titulo"]  . STD_LITERAL_DIVISOR ."true" . STD_LITERAL_DIVISOR . $DField["longitud"] ; 
		}
		unset($xAF, $key, $value);
	}
	$xT			= new cTipos();
	//===========================================================================================================
	
	$_SESSION["grid"]->SetSqlSelect($mGridSQL, $mGridTable, $mGridWhere);
	$_SESSION["grid"]->SetUniqueDatabaseColumn($mGridKeyField, $mGridKeyEdit);
	$_SESSION["grid"]->SetTitleName($mGridTitulo);
	
	//===========================================================================================================					
		foreach ($mGridProp as $mCampo => $mProps) {
			$mVals		= explode(STD_LITERAL_DIVISOR, $mProps, 3);
			$xCampo		= $mCampo;
			
			//echo "$mProps $xCampo|$mCampo<br>";
			if ( isset($mVals[0]) ) {
				$_SESSION["grid"]->SetDatabaseColumnName($xCampo, $mVals[0]);
				//$xCampo			= trim($mVals[0]);
				
			}
			
			if ( isset($mVals[1]) ) {
				$mEdit			= $mVals[1];
				$_SESSION["grid"]->SetDatabaseColumnEditable($xCampo, $mEdit);
				$_SESSION["grid"]->SetDatabaseColumnEditable($mCampo, $mEdit);
			}
			if ( isset($mVals[2]) ) {
				$_SESSION["grid"]->SetDatabaseColumnWidth($xCampo, $mVals[2]);
				$_SESSION["grid"]->SetDatabaseColumnWidth($mCampo, $mVals[2]);
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