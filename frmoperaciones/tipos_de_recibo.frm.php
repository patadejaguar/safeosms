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
$xHP		= new cHPage("TR.Tipo de recibo", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$xHP->addJTableSupport();

$xHP->init();
$xFRM		= new cHForm("frmtiporecibos");

$xFRM->setTitle($xHP->getTitle());

$xFRM->addCerrar();

/* ===========        GRID JS        ============*/

$xHG    = new cHGrid("iddivtiporecibos",$xHP->getTitle());

$xHG->setSQL("SELECT
    `operaciones_recibostipo`.`idoperaciones_recibostipo` AS `clave`,
    `operaciones_recibostipo`.`descripcion_recibostipo`   AS `nombre`,
    `contable_polizasdiarios`.`nombre_del_diario`         AS `poliza`,
    `operaciones_recibostipo`.`path_formato`              AS `formato`
FROM
    `operaciones_recibostipo` `operaciones_recibostipo`
        LEFT OUTER JOIN `contable_polizasdiarios` `contable_polizasdiarios`
        ON `operaciones_recibostipo`.`tipo_poliza_generada` =
        `contable_polizasdiarios`.`idcontable_polizadiarios`
    ORDER BY
        `operaciones_recibostipo`.`idoperaciones_recibostipo`");
$xHG->addList();
$xHG->setOrdenar();
$xHG->col("clave", "TR.CLAVE", "5%");
$xHG->col("nombre", "TR.NOMBRE", "40%");
$xHG->col("poliza", "TR.POLIZA", "15%");
$xHG->col("formato", "TR.FORMATO", "30%");

//$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");

$xHG->OButton("TR.EDITAR", "jsEditarTipoRecibo('+ data.record.clave +')", "edit.png");

//$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.clave +')", "delete.png");

$xHG->OButton("TR.BAJA", "jsDeact('+ data.record.clave +')", "undone.png");

$xFRM->addHElem("<div id='iddivtiporecibos'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );


/*$sql		= "SELECT
	`operaciones_recibostipo`.`idoperaciones_recibostipo` AS `clave`,
	`operaciones_recibostipo`.`descripcion_recibostipo`   AS `nombre`,
	`contable_polizasdiarios`.`nombre_del_diario`         AS `poliza`,
	`operaciones_recibostipo`.`path_formato`              AS `formato` 
FROM
	`operaciones_recibostipo` `operaciones_recibostipo` 
		LEFT OUTER JOIN `contable_polizasdiarios` `contable_polizasdiarios` 
		ON `operaciones_recibostipo`.`tipo_poliza_generada` = 
		`contable_polizasdiarios`.`idcontable_polizadiarios` 
	ORDER BY
		`operaciones_recibostipo`.`idoperaciones_recibostipo` ";

$xT			= new cTabla($sql);
$xT->OButton("TR.Editar", "jsEditarTipoRecibo(" . HP_REPLACE_ID .  ")", $xFRM->ic()->EDITAR);
$xFRM->addHElem($xT->Show());*/

echo $xFRM->get();
//$jxc ->drawJavaScript(false, true);
?>
<script>
var xG	= new Gen();
function jsEditarTipoRecibo(id){
	xG.w({ url : "tipos_de_recibo.editor.frm.php?id=" + id, tiny : true, w : 880 , h : 700 });
}
function jsEditarPerfilContable(id){
	xG.w({ url : "tipos_de_recibo.editor.frm.php?id=" + id, tiny : true, w : 880 , h : 700 });
}
function jsDeact(id){
    xG.recordInActive({tabla:"operaciones_recibostipo", id:id, callback:jsLGiddivtiporecibos, preguntar:true });
}
</script>
<?php
$xHP->fin();
?>