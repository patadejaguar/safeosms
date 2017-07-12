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
$xHP		= new cHPage("", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc = new TinyAjax();
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

$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

$sql		= "SELECT DISTINCT INFORMATION_SCHEMA.COLUMNS.TABLE_SCHEMA,
INFORMATION_SCHEMA.TABLES.TABLE_TYPE,
INFORMATION_SCHEMA.COLUMNS.TABLE_NAME, INFORMATION_SCHEMA.COLUMNS.COLUMN_NAME

FROM
INFORMATION_SCHEMA.COLUMNS
INNER JOIN
INFORMATION_SCHEMA.TABLES

ON
INFORMATION_SCHEMA.TABLES.TABLE_SCHEMA = INFORMATION_SCHEMA.COLUMNS.TABLE_SCHEMA

WHERE
INFORMATION_SCHEMA.COLUMNS.TABLE_SCHEMA='matriz' AND INFORMATION_SCHEMA.TABLES.TABLE_TYPE NOT LIKE '%VIEW%'

AND INFORMATION_SCHEMA.COLUMNS.TABLE_NAME NOT LIKE 'tmp_%'
AND INFORMATION_SCHEMA.COLUMNS.TABLE_NAME NOT LIKE 'temp_%'
AND INFORMATION_SCHEMA.COLUMNS.TABLE_NAME NOT LIKE 'vv_%'
AND INFORMATION_SCHEMA.COLUMNS.TABLE_NAME NOT LIKE 'vw_%' ";

$sql		= "SELECT TABLE_NAME, CONCAT(TABLE_NAME, '.-', TABLE_COMMENT) AS 'name' FROM information_schema.tables WHERE table_schema='" . MY_DB_IN . "' AND TABLE_TYPE != 'VIEW'
AND TABLE_NAME NOT LIKE 'tmp_%'
AND TABLE_NAME NOT LIKE 'temp_%'
AND TABLE_NAME NOT LIKE 'vv_%'
AND TABLE_NAME NOT LIKE 'vw_%'";
//$xT			= new cTabla($sql);
//$xT->setEventKey("jsGetTable");
$xNSel			= new cSelect("idtabla", "idtabla", $sql);
$xNSel->setEsSql();
$xNSel->setNoMayus();
$xNSel->setLabel("TR.TABLA");
$lbl			= $xNSel->getLabel();
$xNSel->setLabel("");

$xFRM->addDivSolo($lbl, $xNSel->get(false), "tx14", "tx34");
$xFRM->OButton("TR.CODIGO", "jsGetTable()", $xFRM->ic()->EJECUTAR);
$xFRM->OText("idgrid", "iddiv", "GRID ID");
$xFRM->OText("idform", "frm", "Nombre Formulario");
$xFRM->OText("idfile", "", "Nombre Archivo");
$xFRM->OText("idruta", "frm", "Ruta Archivo");

$xFRM->OText("idtitle", "", "Titulo Forma/pagina");

$xFRM->OMoneda("idmenu", 0, "MENU ID");
$xFRM->addHElem($xSel->getListaDeMenuParents("idmenuparent")->get(true));
//$xFRM->OMoneda("idmenuparent", 0, "MENU PARENT ID");

$xFRM->OTextArea("idsql", "SQL", "TR.SQL");

echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsGetTable(){
	var n 		= $("#idtabla").val();
	var sql		= $("#idsql").val();
	var idgrid	= $("#idgrid").val();
	var idfrm	= $("#idform").val();
	var idfil	= $("#idfile").val();
	var idpar	= $("#idmenuparent").val();
	var idmnu	= $("#idmenu").val();
	var idtit	= $("#idtitle").val();
	var idrt	= $("#idruta").val();
	
	xG.w({url: "../tools/orm.php?tabla=" + n + "&idgrid=" + idgrid + "&nombrefile=" + idfil + "&nombreforma=" + idfrm + "&menuparent=" + idpar + "&menu=" + idmnu + "&title=" + idtit + "&ruta=" + idrt,  tab: true});
}
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>