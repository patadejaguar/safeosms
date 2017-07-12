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
$xHP		= new cHPage("TR.TASAS DE CAPTACION", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc 		= new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
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

$producto	= parametro("producto", 0, MQL_INT);

$xHP->addJTableSupport();
$xHP->init();

$W			= ($producto >0) ? " WHERE (`captacion_tasas`.`subproducto`=$producto) " : " ";

$xFRM		= new cHForm("frmtasa", "tasas.frm.php?action=$action");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xFRM->addCerrar();
$xFRM->OHidden("producto", $producto);

/* ===========		GRID JS		============*/

$xHG	= new cHGrid("iddivtasas",$xHP->getTitle());

$xHG->setSQL("SELECT   `captacion_tasas`.`idcaptacion_tasas` AS `clave`,
         `captacion_cuentastipos`.`descripcion_cuentastipos` AS `tipo`,
         `captacion_subproductos`.`descripcion_subproductos` AS `producto`,
         `captacion_tasas`.`monto_mayor_a`,
         `captacion_tasas`.`monto_menor_a`,
         `captacion_tasas`.`dias_mayor_a`,
         `captacion_tasas`.`dias_menor_a`,
         ( `captacion_tasas`.`tasa_efectiva`*100 ) AS `tasa`
FROM     `captacion_tasas` 
LEFT OUTER JOIN `captacion_subproductos`  ON `captacion_tasas`.`subproducto` = `captacion_subproductos`.`idcaptacion_subproductos` 
INNER JOIN `captacion_cuentastipos`  ON `captacion_tasas`.`modalidad_cuenta` = `captacion_cuentastipos`.`idcaptacion_cuentastipos` $W ORDER BY `captacion_cuentastipos`.`descripcion_cuentastipos` DESC,
         `captacion_subproductos`.`descripcion_subproductos` DESC,
         `captacion_tasas`.`monto_mayor_a` ASC,
         `captacion_tasas`.`dias_mayor_a` ASC");

$xHG->addList();
$xHG->addKey("clave");

$xHG->col("tipo", "TR.TIPO", "10%");
$xHG->col("producto", "TR.SUBPRODUCTO", "10%");

$xHG->col("monto_mayor_a", "TR.LIMITEINFERIOR", "10%");
$xHG->col("monto_menor_a", "TR.LIMITESUPERIOR", "10%");

$xHG->col("dias_mayor_a", "TR.DIAS MAYOR A", "10%");
$xHG->col("dias_menor_a", "TR.DIAS MENOR A", "10%");


$xHG->col("tasa", "TR.TASA", "10%");

$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.clave +')", "edit.png");
$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.clave +')", "delete.png");
$xFRM->addHElem("<div id='iddivtasas'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );
echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsEdit(id){
	xG.w({url:"../frmcaptacion/tasas.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivtasas});
}
function jsAdd(){
	var idp	= $("#producto").val();
	xG.w({url:"../frmcaptacion/tasas.new.frm.php?producto="+idp, tiny:true, callback: jsLGiddivtasas});
}
function jsDel(id){
	xG.rmRecord({tabla:"captacion_tasas", id:id, callback:jsLGiddivtasas});
}
</script>
<?php
	



//$jxc ->drawJavaScript(false, true);
$xHP->fin();
exit;
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