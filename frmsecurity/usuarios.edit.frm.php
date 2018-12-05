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
$xHP		= new cHPage("TR.EDITAR USUARIO", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$xUsrCurr	= new cSystemUser(); $xUsrCurr->init();
$xText		= new cHText();
//$jxc 		= new TinyAjax();
//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->init();


$xFRM		= new cHForm("frmusuarios", "../frmsecurity/usuarios.edit.frm.php");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());


$xUser		= new cSystemUser($clave);
if($xUser->init() == true){
	
	$xFRM->OHidden("clave", $clave);
	$xFRM->setAction("../frmsecurity/usuarios.edit.frm.php?action=" . MQL_MOD);
	
	
	
	$idpersona	= $xUser->getClaveDePersona();
	if($idpersona > DEFAULT_SOCIO){
		$xSoc	= new cSocio($idpersona);
		if($xSoc->init() == true){
			$xFRM->addHElem( $xSoc->getFicha(false, false, "", true) );
		}
	}
	
	$xFRM->addHElem($xUser->getFicha());
	
	if($action == MQL_MOD){
		$xFRM->addCerrar("", 5);
		$rawnombre			= parametro("nombreusuario", $xUser->getNombreDeUsuario(), MQL_RAW);
		$rawsucursal		= parametro("idsucursal", $xUser->getSucursal(), MQL_RAW);
		$rawpuesto			= parametro("idpuesto", $xUser->getPuesto(), MQL_RAW);
		$rawcuenta			= parametro("idcuentacontable", $xUser->getCuentaContableDeCaja(), MQL_RAW);
		$rawcorp			= parametro("idcorporativo", $xUser->getEsCorporativo(), MQL_BOOL);
		$nivel				= parametro("idtipousuario", $xUser->getNivel(), MQL_INT);
		
		
		$xUser->setCodigoDePersona($persona);	
		$xUser->setNombreUsuario($rawnombre);
		$xUser->setSucursal($rawsucursal);
		$xUser->setPuesto($rawpuesto);
		
		$xUser->setEsCorporativo($rawcorp);
		if($nivel>0){
			$xUser->setNivelAcceso($nivel);
		}
		
		$xFRM->addAvisoRegistroOK();
		
	} else {
		
		$xFRM->addGuardar();
		
		$xFRM->addPersonaBasico("", false, $idpersona, "", "TR.PERSONA USUARIO");
		$xFRM->OText_13("nombreusuario", $xUser->getNombreDeUsuario(), "TR.NOMBRE USUARIO");
		
		$xFRM->addHElem( $xSel->getListaDeNivelDeUsuario("idtipousuario", $xUser->getNivel(), $xUsrCurr->getTipoEnSistema())->get(true) );
		
		
		$xFRM->OText("idpuesto", $xUser->getPuesto(), "TR.PUESTO");
		
		if(MULTISUCURSAL == false){
			
			$xFRM->OHidden("idsucursal", $xUser->getSucursal() );
		} else {
			$xFRM->addHElem( $xSel->getListaDeSucursales("idsucursal", $xUser->getSucursal())->get(true));
		}
		
		
		if(MODULO_CONTABILIDAD_ACTIVADO == true){
			
			$xFRM->addHElem($xText->getDeCuentaContable("idcuentacontable", CUENTA_CONTABLE_EFECTIVO, true, CUENTA_CONTABLE_EFECTIVO, "TR.CUENTA_CONTABLE DE CAJA") );
		} else {
			$xFRM->OHidden("idcuentacontable", CUENTA_CONTABLE_EFECTIVO);
		}
		$xFRM->OCheck("TR.CORPORATIVO", "idcorporativo", $xUser->getEsCorporativo());
		
	}
	
	
} else {
	$xFRM->addCerrar("", 3);
}	

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
?>
<script>

</script>
<?php
$xHP->fin();
?>