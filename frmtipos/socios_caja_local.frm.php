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
$xHP		= new cHPage("TR.MODULO CAJA_LOCAL", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$xLoc		= new cLocal();
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave				= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha				= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$jscallback			= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$nombre				= parametro("idnombre");
$idregionpersona	= parametro("idregionpersona", getRegion(), MQL_INT);
$idsucursal			= parametro("idsucursal", getSucursal(), MQL_RAW);
$idcodigopostal		= parametro("idcodigopostal", $xLoc->DomicilioCodigoPostal(), MQL_INT );




$xHP->init();

$xFRM		= new cHForm("frmsocioscajaloc", "");
$xFRM->setTitle($xHP->getTitle());
$xSel		= new cHSelect();
$xTxt		= new cHText();

$xFRM->addCerrar();

if($action == SYS_NINGUNO){
	$xFRM->OButton("TR.Agregar CAJA_LOCAL", "var xP=new PersGen();xP.setNuevaCajaLocal();", $xFRM->ic()->AGREGAR);
	$xT		= new cTabla($xLi->getListadoDeCajasLocales());
	$xT->OButton("TR.Editar", "var xP=new PersGen();xP.setEditarCajaLocal(" . HP_REPLACE_ID . ");", $xFRM->ic()->EDITAR);
	$xFRM->addHElem( $xT->Show() );
} else {
	
	if($action == MQL_ADD){
		//Accion Guardar
		$xFRM->addCerrar();
		$xCL		= new cCajaLocal($clave);
		$res		= $xCL->add($nombre, $clave, $idregionpersona, $idsucursal, $idcodigopostal);
		$xFRM->setResultado($res);
	} else if($action == MQL_MOD){
		//Accion editar
		$xFRM->addCerrar();
		$xCL		= new cCajaLocal($clave);
		if($xCL->init() == true){
			$res		= $xCL->edit($nombre, $idregionpersona, $idsucursal, $idcodigopostal); 
			$xFRM->setResultado($res);
		}
	} else {
		if($clave <= 0){
			//Agregar formulario
			$xTabla		= new cSocios_cajalocal();
			$clave		= $xTabla->query()->getLastID()+1;
			$xFRM->setAction("socios_caja_local.frm.php?action=" . MQL_ADD);
		} else {
			//Editar existente
			$xFRM->setAction("socios_caja_local.frm.php?action=" . MQL_MOD);
			$xCL		= new cCajaLocal($clave);
			if( $xCL->init() == true ){
				$xFRM->addHElem( $xCL->getFicha(true) );
				$nombre			= $xCL->getNombre();
				$idcodigopostal	= $xCL->getCodigoPostal();
				$idsucursal		= $xCL->getSucursal();
				$idregionpersona= $xCL->getRegion();
			}
		}
		$xFRM->addGuardar();
		$xFRM->OHidden("id", $clave);
		$xFRM->OText("idnombre", $nombre, "TR.NOMBRE");
		$xFRM->addHElem( $xSel->getListaDeRegionDePersonas("", $idregionpersona)->get(true) );
		$xFRM->addHElem( $xSel->getListaDeSucursales("", $idsucursal)->get(true) );
		//$xFRM->addHElem( $xTxt->getDeCodigoPostal("", $idcodigopostal) );
		$xFRM->OMoneda("idcodigopostal", $idcodigopostal, "TR.CODIGO_POSTAL");
	}
}

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
exit;
?>
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
$xHP		= new cHPage("TR.CAJA_LOCAL", HP_GRID);
$xF			= new cFecha();
$xL			= new cLang();
$xTabla		= new cSocios_cajalocal();
	
$xHP->setNoDefaultCSS();
echo $xHP->getHeader(true);
//HTML Object END
echo '<body onmouseup="SetMouseDown(false);" >';
//Define your grid
	$_SESSION["grid"]->SetDatabaseConnection(MY_DB_IN, USR_DB, PWD_DB);
	//Propiedades del GRID
	$mGridTitulo		= $xHP->getTitle();
	$mGridKeyField		= $xTabla->getKey();	//Nombre del Campo Unico
	$mGridKeyEdit		= false;					//Es editable el Campo
	$mGridTable			= $xTabla->get();	//Nombre de la tabla
	$mGridSQL			= $xTabla->query()->getListaDeCampos();//  "*"; //$xTabla->query()->getCampos();
	$mGridSQL			= "idsocios_cajalocal,clave_de_centro,descripcion_cajalocal,region,sucursal,codigo_postal,localidad,estado,municipio,ultimosocio";
	$mGridWhere			= "";

	$mGridProp			= array(
	"idsocios_cajalocal" 		=> "TR.CAJA_LOCAL,true,50",
	"descripcion_cajalocal" 	=> "TR.NOMBRE,true,200",
	"ultimosocio" 				=> "TR.ULTIMO REGISTRO,true,100",
	"region" 					=> "TR.REGION,false,50",
	"sucursal" 					=> "TR.SUCURSAL,true,120",
	"codigo_postal" 			=> "TR.codigo_postal,true,100",
	"localidad" 				=> "TR.localidad,true,180",
	"estado" 					=> "TR.estado,true,120",
	"municipio" 				=> "TR.municipio,true,120",
	"clave_de_centro" 			=> "TR.clave,true,60"
						);
	//===========================================================================================================
	
	$_SESSION["grid"]->SetSqlSelect($mGridSQL, $mGridTable, $mGridWhere);
	$_SESSION["grid"]->SetUniqueDatabaseColumn($mGridKeyField, $mGridKeyEdit);
	$_SESSION["grid"]->SetTitleName($mGridTitulo);
	$_SESSION["grid"]->SetEditModeAdd(false);
	//$_SESSION["grid"]->SetEditModeDelete(false);
	//===========================================================================================================					
		foreach ($mGridProp as $key => $value) {
			$mVals		= explode(",", $value, 10);
			if ( isset($mVals[0]) ){ $_SESSION["grid"]->SetDatabaseColumnName($key, $xL->getT($mVals[0]));	}
			if ( isset($mVals[1]) ) { $_SESSION["grid"]->SetDatabaseColumnEditable($key,	$mVals[1]); }
			if ( isset($mVals[2]) ) { $_SESSION["grid"]->SetDatabaseColumnWidth($key, 		$mVals[2]); }	
		}
	//===========================================================================================================
	$_SESSION["grid"]->SetMaxRowsEachPage(25);
	$_SESSION["grid"]->PrintGrid(MODE_EDIT);

echo $xHP->fin(); exit;
?>
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