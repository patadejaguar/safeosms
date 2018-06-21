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
	include_once("../vendor/autoload.php");
	
	$theFile			= __FILE__;
	$permiso			= getSIPAKALPermissions($theFile);
	if($permiso === false){	header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//=====================================================================================================
$xHP		= new cHPage("TR.PRECLIENTES", HP_FORM);
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
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$observaciones	= parametro("idobservaciones");

$forcesync		= parametro("sync", false, MQL_BOOL);


$xHP->addJTableSupport();
$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();


$xFRM->setTitle($xHP->getTitle());
$xFRM->addCerrar();
$xFRM->OButton("TR.AGREGAR", "jsAdd()", $xFRM->ic()->AGREGAR, "cmdaddprecli", "green");



$xFRM->addSeccion("idasig", "TR.CREDITOSNOASIGNADOS");
$xHG = new cHGrid("iddivpreclientes",$xHP->getTitle());

$xHG->setSQL("SELECT
`creditos_preclientes`.`idcontrol` AS `clave`,
`creditos_preclientes`.`fecha_de_registro` AS `fecha`, CONCAT(`creditos_preclientes`.`nombres`, ' ',`creditos_preclientes`.`apellido1`, ' ',`creditos_preclientes`.`apellido2`) AS `nombre`,
`creditos_preclientes`.`telefono`,`creditos_preclientes`.`email`,`creditos_periocidadpagos`.`descripcion_periocidadpagos` AS `periocidad`,`creditos_preclientes`.`pagos`, `creditos_preclientes`.`monto`
FROM `creditos_preclientes` `creditos_preclientes`
INNER JOIN `creditos_periocidadpagos` `creditos_periocidadpagos` ON `creditos_preclientes`.`periocidad` = `creditos_periocidadpagos`.`idcreditos_periocidadpagos`
INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio` ON `creditos_preclientes`.`producto` = `creditos_tipoconvenio`.`idcreditos_tipoconvenio`
WHERE (`creditos_preclientes`.`idestado` =" . SYS_UNO . ") AND (`creditos_preclientes`.`idoficial` = 0) ");
$xHG->addList();
$xHG->setOrdenar();


$xHG->col("clave", "TR.CLAVE", "8%");
$xHG->col("fecha", "TR.FECHA", "8%");
$xHG->col("nombre", "TR.NOMBRE", "40%");
//$xHG->col("telefono", "TR.TELEFONO", "8%");
$xHG->col("email", "TR.EMAIL", "8%");
$xHG->col("periocidad", "TR.PERIOCIDAD", "10%");
$xHG->col("pagos", "TR.PAGOS", "8%");
$xHG->col("monto", "TR.MONTO", "10%");

$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.clave +')", "edit.png");

$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.clave +')", "delete.png");

$xFRM->addHElem("<div id=\"iddivpreclientes\"></div>");

$xFRM->addJsCode( $xHG->getJs(true) );


$xFRM->endSeccion();
$xFRM->addSeccion("idasig", "TR.CREDITOSASIGNADOS");
$sql		= "SELECT
	`creditos_preclientes`.`idcontrol` AS `clave`,
	`creditos_preclientes`.`fecha_de_registro` AS `fecha`,
	CONCAT(`creditos_preclientes`.`nombres`, ' ',
	`creditos_preclientes`.`apellido1`, ' ',
	`creditos_preclientes`.`apellido2`)  AS `nombre`,
	`creditos_preclientes`.`telefono`,
	`creditos_preclientes`.`email`,
	`creditos_periocidadpagos`.`descripcion_periocidadpagos` AS `periocidad`,
	`creditos_preclientes`.`pagos`,
	`creditos_preclientes`.`monto`
FROM
	`creditos_preclientes` `creditos_preclientes`
		INNER JOIN `creditos_periocidadpagos` `creditos_periocidadpagos`
		ON `creditos_preclientes`.`periocidad` = `creditos_periocidadpagos`.
		`idcreditos_periocidadpagos`
			INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio`
			ON `creditos_preclientes`.`producto` = `creditos_tipoconvenio`.
			`idcreditos_tipoconvenio`
WHERE
	(`creditos_preclientes`.`idestado` = ". SYS_UNO .") AND (`creditos_preclientes`.`idoficial` = " . getUsuarioActual() .  ") ";


$xT			= new cTabla($sql);

//$xT->setEventKey("jsGoPanel");

$xT->OButton("TR.PANEL", "jsGoPanel(" . HP_REPLACE_ID . ")", $xFRM->ic()->CONTROL);

$xFRM->addHElem($xT->Show());

$xFRM->endSeccion();


echo $xFRM->get();




/* ===========		GRID JS		============*/
/*
$xHG	= new cHGrid("iddivpreclientes",$xHP->getTitle());

$xHG->setSQL("SELECT * FROM `creditos_preclientes` LIMIT 0,100");
$xHG->addList();
$xHG->addKey("idcontrol");
$xHG->col("nombres", "TR.NOMBRES", "10%");
$xHG->col("apellido1", "TR.APELLIDO1", "10%");
$xHG->col("apellido2", "TR.APELLIDO2", "10%");
$xHG->col("rfc", "TR.RFC", "10%");
$xHG->col("curp", "TR.CURP", "10%");
$xHG->col("telefono", "TR.TELEFONO", "10%");
$xHG->col("fecha_de_registro", "TR.FECHA DE REGISTRO", "10%");
$xHG->col("producto", "TR.PRODUCTO", "10%");
$xHG->col("periocidad", "TR.PERIOCIDAD", "10%");
$xHG->col("pagos", "TR.PAGOS", "10%");
$xHG->col("aplicacion", "TR.APLICACION", "10%");
$xHG->col("notas", "TR.NOTAS", "10%");
$xHG->col("monto", "TR.MONTO", "10%");
$xHG->col("email", "TR.EMAIL", "10%");
$xHG->col("idpersona", "TR.IDPERSONA", "10%");
$xHG->col("idcredito", "TR.IDCREDITO", "10%");
$xHG->col("idorigen", "TR.IDORIGEN", "10%");
$xHG->col("idestado", "TR.IDESTADO", "10%");
$xHG->col("idoficial", "TR.IDOFICIAL", "10%");
$xHG->col("idexterno", "TR.IDEXTERNO", "10%");

$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.idcontrol +')", "edit.png");
$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.idcontrol +')", "delete.png");
$xFRM->addHElem("<div id='iddivpreclientes'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );
echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsEdit(id){
	xG.w({url:"../frmcreditos/creditos-preclientes.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivpreclientes});
}
function jsAdd(){
	xG.w({url:"../frmcreditos/creditos-preclientes.new.frm.php?", tiny:true, callback: jsLGiddivpreclientes});
}
function jsDel(id){
	xG.rmRecord({tabla:"creditos_preclientes", id:id, callback:jsLGiddivpreclientes});
}
</script>
<?php
*/
?>
<script>
var xG	= new Gen();


function jsEdit(id){
    xG.w({url:"../frmcreditos/creditos-preclientes.panel.frm.php?clave=" + id, tab:true, callback: jsLGiddivpreclientes});
}
function jsAdd(){
    xG.w({url:"../frmcreditos/creditos-preclientes.new.frm.php?", tiny:true, callback: jsLGiddivpreclientes});
}
function jsDel(id){
    xG.rmRecord({tabla:"creditos_preclientes", id:id, callback:jsLGiddivpreclientes });
}


function jsGoPanel(id){
	xG.w({url: "../frmcreditos/creditos-preclientes.panel.frm.php?clave=" + id, tab:true});
}
function jsAdd(){
	xG.w({url:"../frmcreditos/creditos-preclientes.new.frm.php?", tiny:true}); //, callback: jsLGiddivpreclientes
}
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>