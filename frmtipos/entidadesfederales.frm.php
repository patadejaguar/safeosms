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
$xHP		= new cHPage("TR.ENTIDAD_FEDERATIVA", HP_FORM);
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
$xFRM->addCerrar();


/* ===========		GRID JS		============*/

$xHG	= new cHGrid("iddivestados",$xHP->getTitle());

$xHG->setSQL("SELECT   `general_estados`.`idgeneral_estados`,
         `general_estados`.`clave_alfanumerica`,
         `general_estados`.`clave_numerica`,
         `general_estados`.`nombre`,
         `general_estados`.`clave_en_sic`,
         `general_estados`.`codigo_postal_inicial`,
         `general_estados`.`codigo_postal_final`,
         getBooleanMX(`general_estados`.`operacion_habilitada`) AS `operacion_habilitada`
FROM `general_estados` LIMIT 0,100");
$xHG->addList();
$xHG->setOrdenar();

$xHG->addKey("idgeneral_estados");
$xHG->col("clave_alfanumerica", "TR.CLAVE A", "8%");
$xHG->col("clave_numerica", "TR.NUMERO", "8%");
$xHG->col("clave_en_sic", "TR.CLAVE SIC", "8%");

$xHG->col("nombre", "TR.NOMBRE", "30%");

//$xHG->col("codigo_postal_inicial", "TR.CODIGO_POSTAL INICIAL", "10%");
//$xHG->col("codigo_postal_final", "TR.CODIGO_POSTAL FINAL", "10%");
//$xHG->OColFunction("operacion_habilitada", "TR.ESTATUSACTIVO", "8%");
$xHG->col("operacion_habilitada", "TR.ESTATUSACTIVO", "8%");

$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.idgeneral_estados +')", "edit.png");
//$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.idgeneral_estados +')", "delete.png");

$xFRM->addHElem("<div id='iddivestados'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );
echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsEdit(id){
	xG.w({url:"../frmtipos/entidadesfederales.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivestados});
}
function jsAdd(){
	xG.w({url:"../frmtipos/entidadesfederales.new.frm.php?", tiny:true, callback: jsLGiddivestados});
}
function jsDel(id){
	//xG.rmRecord({tabla:"general_estados", id:id, callback:jsLGiddivestados});
}
</script>
<?php


//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>