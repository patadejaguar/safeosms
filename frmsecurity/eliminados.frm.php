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
$xHP		= new cHPage("TR.Objetos ELIMINADOS", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc 		= new TinyAjax();
//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
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

$usuario	= parametro("usuario", 0, MQL_INT);
$strExt		= " getFechaByInt(`sistema_eliminados`.`tiempo`) = '\" + idfecha + \"' ";
$xHP->addJTableSupport();

$xHP->init();

$xFRM		= new cHForm("frmeliminados", "eliminados.frm.php?action=$action");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

$ByUser		= ($usuario <= 0) ? "" : " WHERE `sistema_eliminados`.`idusuario` = $usuario ";
$strExt		= ($usuario <= 0) ? $strExt : " AND $strExt ";

$xHG	= new cHGrid("iddiveliminados",$xHP->getTitle());

$xHG->setSQL("SELECT   `sistema_eliminados`.`idsistema_eliminados`,
         `sistema_eliminados`.`tipoobjeto`,
         `usuarios`.`nombreusuario` AS `usuario`,
         getFechaByInt(`sistema_eliminados`.`tiempo`) AS `fecha`,
		`sistema_eliminados`.`persona` AS `persona`
		
FROM     `sistema_eliminados` 
INNER JOIN `usuarios`  ON `sistema_eliminados`.`idusuario` = `usuarios`.`idusuarios` $ByUser ");
$xHG->addList();
$xHG->addKey("idsistema_eliminados");

$xHG->col("tipoobjeto", "TR.OBJETO", "10%");
$xHG->col("usuario", "TR.USUARIO", "10%");
$xHG->ColFecha("fecha", "TR.FECHA", "10%");
$xHG->col("persona", "TR.PERSONA", "10%");

//$xHG->col("contenido", "TR.CONTENIDO", "50%");
//$xHG->OColFunction("contenido", "TR.CONTENIDO", "50%", "jsVerContenido");

//$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.idsistema_eliminados +')", "edit.png");
$xHG->OButton("TR.VER", "jsVerContenido('+ data.record.idsistema_eliminados +')", "view.png");
$xFRM->OButton("TR.FILTRAR", "jsSetFiltro()", $xFRM->ic()->FILTRO);
$xFRM->addFecha();

$xHG->setOrdenar();
//$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.idsistema_eliminados +')", "delete.png");
$xFRM->addHElem("<div id='iddiveliminados'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );
echo $xFRM->get();
?>
<script>
var xG	= new Gen();
var xF	= new FechaGen();


function jsEdit(id){
	xG.w({url:"../frmsecurity/eliminados.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddiveliminados});
}
function jsAdd(){
	xG.w({url:"../frmsecurity/eliminados.new.frm.php?", tiny:true, callback: jsLGiddiveliminados});
}
function jsDel(id){
	//xG.rmRecord({tabla:"sistema_eliminados", id:id, callback:jsLGiddiveliminados});
}
function jsVerContenido(id){
	xG.w({url:"../frmsecurity/eliminados-ver.frm.php?clave=" + id, tiny:true, callback: jsLGiddiveliminados});
}
function jsSetFiltro(){
	var idfecha	= xF.get($("#idfechaactual").val());
	var str 	= "<?php echo $strExt; ?>";
	
	str			= "&w="  + base64.encode(str);

	$('#iddiveliminados').jtable('destroy');
	
	jsLGiddiveliminados(str);
}
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>