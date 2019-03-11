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
$xHP		= new cHPage("TR.CREDITO RECHAZADO", HP_FORM);
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
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
//function jsaSetSaveRechazados($solicitud, $texto, $fecha){	$xCred		= new cCredito($solicitud); 	$xCred->init(); 	$xCred->setRazonRechazo($texto, "", $fecha); }
$xHP->init();

$xFRM		= new cHForm("frmcredsrechazados", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xFRM->setNoAcordion();

$xCred		= new cCredito($credito);
if($xCred->init() == true){
	

	
	
	if($action == SYS_NINGUNO){
		
		
		//$xFRM->OButton("TR.RECHAZAR", "jsSetCancelado()", $xFRM->ic()->CERRAR);
		$xFRM->addGuardar("", "", "TR.GUARDAR RECHAZADO");
		
		$xFRM->addSeccion("idfrs", "TR.CREDITO");
		$xFRM->OHidden("credito", $credito);
		$xFRM->addHElem( $xCred->getFicha(true, "", false, true) );
		$xFRM->endSeccion();
		
		$xFRM->addSeccion("ifres", "TR.RAZONES");
		$xFRM->addFecha();
		$xFRM->addHElem( $xSel->getListaDeTipoDeRechazoCred()->get(true) );
		$xFRM->OTextArea("iddetalles", "", "TR.DETALLES");
		$xFRM->setAction("creditos-rechazados.frm.php?action=" . MQL_ADD);
		
		$xFRM->endSeccion();
	} else {
		//Guardar
		$detalles		= parametro("iddetalles");
		$idrazon		= parametro("idtiporechazo",0, MQL_INT);
		
		$res 			= $xCred->setRazonRechazo($detalles, "", $fecha, $idrazon);
		
		$xFRM->addSeccion("idfrs", "TR.CREDITO");
		$xFRM->OHidden("credito", $credito);
		$xFRM->addHElem( $xCred->getFicha(true, "", false, true) );
		$xFRM->endSeccion();
		
		$xFRM->setResultado($res);
		
		$xFRM->addCerrar("", 5);
	}
}


echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
?>
<script>
function jsSetCancelado(){
	
}
</script>
<?php
$xHP->fin();
?>