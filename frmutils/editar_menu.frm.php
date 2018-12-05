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
$xHP		= new cHPage("TR.MENU", HP_FORM);
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

$observaciones= parametro("idobservaciones");
$xHP->addJTableSupport();
$xHP->init();



$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

$sqlMost 	= "SELECT idgeneral_menu, CONCAT(menu_title, ' ', idgeneral_menu) AS 'menu' FROM general_menu WHERE menu_type='parent' ORDER BY menu_title";

$xSelM		= $xSel->getListadoGenerico($sqlMost, "idmenu");
$xSelM->setEsSql();$xSelM->addTodas(true);

$xFRM->addHElem($xSelM->get("TR.MENU", true));

$xFRM->OBuscar("idbuscar", "", "", "jsBuscar()");

$xFRM->addCerrar();

/* ===========        GRID JS        ============*/

$xHG    = new cHGrid("iddiv",$xHP->getTitle());

$xHG->setSQL("SELECT   `general_menu`.`idgeneral_menu` AS `clave`,
         `general_menu`.`menu_parent` AS `superior`,`general_menu`.`menu_title` AS `titulo`,`general_menu`.`menu_file` AS `archivo`,`general_menu`.`menu_image` AS `icono`,
         `general_menu`.`menu_type` AS `tipo`,`general_menu`.`menu_order` AS `orden`,`general_menu`.`menu_destination` AS `destino`
FROM     `general_menu` WHERE `general_menu`.`idgeneral_menu` > 0 ORDER BY `general_menu`.`menu_parent`,`general_menu`.`idgeneral_menu` ");
$xHG->addList();
$xHG->setOrdenar();
$xHG->col("clave", "TR.CLAVE", "10%");
$xHG->col("superior", "TR.SUPERIOR", "10%");
$xHG->col("titulo", "TR.TITULO", "10%");
$xHG->col("archivo", "TR.ARCHIVO", "10%");
$xHG->col("icono", "TR.ICONO", "10%");
$xHG->col("tipo", "TR.TIPO", "10%");
//$xHG->col("orden", "TR.ORDEN", "10%");
//$xHG->col("destino", "TR.DESTINO", "10%");

$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.clave +')", "edit.png");
//$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.clave +')", "delete.png");
$xHG->OButton("TR.BAJA", "jsDeact('+ data.record.clave +')", "undone.png");
$xFRM->addHElem("<div id='iddiv'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );
echo $xFRM->get();
?>

<script>
var xG    = new Gen();
function jsEdit(id){
    xG.w({url:"../frmutils/menu.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddiv});
}
function jsAdd(){
    xG.w({url:"../frmutils/menu.new.frm.php?", tiny:true, callback: jsLGiddiv});
}
function jsDel(id){
    //xG.rmRecord({tabla:"general_menu", id:id, callback:jsLGiddiv });
}
function jsDeact(id){
    xG.recordInActive({tabla:"general_menu", id:id, callback:jsLGiddiv, preguntar:true });
}
function jsBuscar(){
	var idbuscar = $("#idbuscar").val();
	
	//var str	= 
}

</script>
<?php



//$jxc ->drawJavaScript(false, true);
$xHP->fin();

exit;
?><?php
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
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
$xHP		= new cHPage("TR.Editar Menu", HP_FORM);
$parent 	= parametro("cmenu", 0, MQL_INT);
$txtBuscar	= parametro("cBuscar", "", MQL_RAW);

if($parent <=0){
	$xHP->init();
	$xFRM		= new cHForm("frmeditmenu", "editar_menu.frm.php");
	
	$xFRM->addEnviar();
	
	$sqlMost 	= "SELECT idgeneral_menu, CONCAT(menu_title, ' ', idgeneral_menu) AS 'menu' FROM general_menu WHERE menu_type='parent' ORDER BY menu_title";
	$cSel 		= new cSelect("cmenu", "idmenu", $sqlMost);
	$cSel->setEsSql();
	$cSel->addEspOption("todas", "TODAS");
	$cSel->setOptionSelect("todas");
	$xFRM->addHElem($cSel->show());
	$xFRM->OText("txrbuscar", "", "TR.Buscar");
	
	echo $xFRM->get(true);
		
} else {
	$xHP		= new cHPage("TR.Editar Menu", HP_GRID);
	$xHP->setNoDefaultCSS();
	$xHP->init();
	$filtro1	= "";
	$filtro2	= "";
	
	if ($parent != "todas"){
		$filtro1	= " menu_parent=$parent ";
	}
	if ( $txtBuscar !=  "" ){
		$filtro2	= " ( menu_file LIKE '%$txtBuscar%' OR menu_title LIKE '%$txtBuscar%' OR menu_description LIKE '%$txtBuscar%' )";
		if ( $filtro1 != ""){
			$filtro2	= " AND " . $filtro2;
		}
	}
	// Define your grid
	$_SESSION["grid"]->SetDatabaseConnection(MY_DB_IN, USR_DB, PWD_DB);
	//,menu_type
	$_SESSION["grid"]->SetSqlSelect('idgeneral_menu, menu_title, menu_file, menu_parent, menu_order', 'general_menu', " $filtro1 $filtro2 ");
	$_SESSION["grid"]->SetUniqueDatabaseColumn("idgeneral_menu", false);
	$_SESSION["grid"]->SetTitleName("Editar Menu del Sistema");
	// End definition
	$_SESSION["grid"]->SetDatabaseColumnWidth("menu_parent",120);
	$_SESSION["grid"]->SetDatabaseColumnName("menu_parent", "Sup.");
	$_SESSION["grid"]->SetDatabaseColumnWidth("menu_title",300);
	$_SESSION["grid"]->SetDatabaseColumnName("menu_title", "Titulo");
	$_SESSION["grid"]->SetDatabaseColumnWidth("menu_file",300);
	$_SESSION["grid"]->SetDatabaseColumnName("menu_file", "Archivo");
	
	$_SESSION["grid"]->SetDatabaseColumnWidth("menu_order",80);
	$_SESSION["grid"]->SetDatabaseColumnName("menu_order", "Orden");
	
	$_SESSION["grid"]->SetMaxRowsEachPage(40);
	$_SESSION["grid"]->PrintGrid(MODE_EDIT);
	
	
}
$xHP->fin();
?>