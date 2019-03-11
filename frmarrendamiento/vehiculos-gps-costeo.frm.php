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
$xHP		= new cHPage("TR.COSTEO PAQUETESGPS", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$xUser			= new cSystemUser(getUsuarioActual()); $xUser->init();
$xRuls			= new cReglaDeNegocio();
$originador		= 0;
$suborigen		= 0;
$EsAdmin		= false;
$NoUsarUsers	= $xRuls->getArrayPorRegla($xRuls->reglas()->CREDITOS_ARREND_NOUSERS);
$EsOriginador	= false;

if($xUser->getEsOriginador() == true){
	$xOrg	= new cLeasingUsuarios();
	if($xOrg->initByIDUsuario($xUser->getID()) == true){
		$originador	= $xOrg->getOriginador();
		$suborigen	= $xOrg->getSubOriginador();
		//$EsActivo	= $xOrg->getEsActivo();
		$EsAdmin	= $xOrg->getEsAdmin();
		if($xOrg->getEsAdmin() == true){
			$suborigen			= 0;
		}
		if($xOrg->getEsActivo() == false){
			$xHP->goToPageError(403);
		} else {
			$EsOriginador	= true;
		}
	}
}
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

$xFRM		= new cHForm("frmvehiculos_gps_costeo", "vehiculos-gps-costeo.frm.php?action=$action");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xFRM->addCerrar();


/* ===========		GRID JS		============*/

$xHG	= new cHGrid("iddivgpscosteo",$xHP->getTitle());
$xHG->setOrdenar();

$xHG->setSQL("SELECT   `vehiculos_gps_costeo`.`idvehiculos_gps_costeo` AS `clave`,
		 `vehiculos_gps`.`nombre_gps` AS `paquetesgps`,
         `creditos_periocidadpagos`.`descripcion_periocidadpagos` AS `frecuencia`,
         `vehiculos_gps_costeo`.`limite_inferior` AS `limiteinferior`,
         `vehiculos_gps_costeo`.`limite_superior` AS `limitesuperior`,
        
         `vehiculos_gps_costeo`.`monto_gps` AS `monto`
FROM     `vehiculos_gps_costeo` 
INNER JOIN `creditos_periocidadpagos`  ON `vehiculos_gps_costeo`.`frecuencia` = `creditos_periocidadpagos`.`idcreditos_periocidadpagos` 
INNER JOIN `vehiculos_gps`  ON `vehiculos_gps_costeo`.`tipo_de_gps` = `vehiculos_gps`.`idvehiculos_gps` ");

$xHG->addList();
$xHG->addKey("clave");

$xHG->col("paquetesgps", "TR.PAQUETESGPS", "30%");
$xHG->col("frecuencia", "TR.FRECUENCIA", "20%");
$xHG->col("limiteinferior", "TR.LIMITEINFERIOR", "15%");
$xHG->col("limitesuperior", "TR.LIMITESUPERIOR", "15%");

$xHG->col("monto", "TR.MONTO", "15%");
if($EsOriginador == false){
	$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "grid/add.png");
	$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.clave +')", "edit.png");
	$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.clave +')", "delete.png");
}

$xFRM->addHElem("<div id='iddivgpscosteo'></div>");
$xFRM->addTag("Registre Precios con IVA", "warning");
$xFRM->addJsCode( $xHG->getJs(true) );
echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsEdit(id){
	xG.w({url:"../frmarrendamiento/vehiculos-gps-costeo.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivgpscosteo});
}
function jsAdd(){
	xG.w({url:"../frmarrendamiento/vehiculos-gps-costeo.new.frm.php?", tiny:true, callback: jsLGiddivgpscosteo});
}
function jsDel(id){
	xG.rmRecord({tabla:"vehiculos_gps_costeo", id:id, callback:jsLGiddivgpscosteo});
}
</script>
<?php

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>