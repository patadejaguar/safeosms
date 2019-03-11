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
$xHP		= new cHPage("TR.CATALOGO PAIS", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); 
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->addJTableSupport();
$xHP->init();

$xFRM	= new cHForm("frmpaises", "catalogo_paises.frm.php?action=$action");
$xFRM->setTitle($xHP->getTitle());
$msg		= "";
$xFRM->addCerrar();

/* ===========		GRID JS		============*/

$xHG	= new cHGrid("iddivpaises",$xHP->getTitle());

$xHG->setSQL("SELECT   `personas_domicilios_paises`.`clave_de_control`,
         `personas_domicilios_paises`.`clave_numerica`,
         `personas_domicilios_paises`.`clave_alfanumerica`,		
         `personas_domicilios_paises`.`nombre_oficial`,
         getBooleanMX( `personas_domicilios_paises`.`es_paraiso_fiscal` ) AS `es_paraiso_fiscal`,
         `entidad_niveles_de_riesgo`.`nombre_del_nivel` AS `nivel_riesgo`,
         `personas_domicilios_paises`.`gentilicio`
FROM     `personas_domicilios_paises` 
INNER JOIN `entidad_niveles_de_riesgo`  ON `personas_domicilios_paises`.`es_considerado_riesgo` = `entidad_niveles_de_riesgo`.`clave_de_nivel` LIMIT 0,50 ");
$xHG->addList();
$xHG->setOrdenar();
$xHG->addKey("clave_de_control");

$xHG->col("clave_numerica", "TR.NUMERO", "10%");
$xHG->col("clave_alfanumerica", "TR.CLAVE", "10%");

$xHG->col("nombre_oficial", "TR.NOMBRE", "10%");
$xHG->col("es_paraiso_fiscal", "TR.PARAISOFISCAL", "10%");
$xHG->col("nivel_riesgo", "TR.NIVEL_DE_RIESGO", "10%");

//$xHG->col("gentilicio", "TR.GENTILICIO", "10%");

//$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
$xHG->OButton("TR.EDITAR", "jsEdit(\''+ data.record.clave_de_control +'\')", "edit.png");

$xFRM->addHElem("<div id='iddivpaises'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );
echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsEdit(id){
	xG.w({url:"../frmsocios/catalogo_paises.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivpaises,w:640});
}
function jsAdd(){
	xG.w({url:"../frmsocios/catalogo_paises.new.frm.php?", tiny:true, callback: jsLGiddivpaises});
}

</script>
<?php
	

$xHP->fin();
?>