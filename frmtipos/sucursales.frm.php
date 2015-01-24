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
$xLoc		= new cLocal();

$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();


$xHP->init();

$clave 			= parametro("codigo_sucursal", null, MQL_RAW);
$xTabla			= new cGeneral_sucursales();
if($clave != null){$xTabla->setData( $xTabla->query()->initByID($clave));}
$xTabla->setData($_REQUEST);
$clave			= parametro("id", null, MQL_RAW);
$persona		= parametro("idsocio", null, MQL_INT);
$gerente		= parametro("idsocio2", null, MQL_INT);
$cumplimiento	= parametro("idsocio3", null, MQL_INT);

//inicializar la persona
$xSoc			= new cSocio($persona);
$ODom			= null;
if($xSoc->init() == true){
	$ODom		= $xSoc->getODomicilio();
}
$xSel			= new cHSelect();
if($clave == null){
	$step		= MQL_ADD;
	$clave		= $xTabla->query()->getLastID() + 1;
} else {
	$step		= MQL_MOD;
	if($clave != null){$xTabla->setData( $xTabla->query()->initByID($clave));}
}
$xFRM	= new cHForm("frmgeneral_sucursales", "sucursales.frm.php?action=$step");
$xFRM->addSubmit();

if($action == MQL_ADD){		//Agregar
	$clave 		= parametro($xTabla->getKey(), null, MQL_RAW);
	if($clave != null){
		$xTabla->setData( $xTabla->query()->initByID($clave));
		$xTabla->setData($_REQUEST);
		//modificar la parte de personas asociadas
		$xTabla->gerente_sucursal($gerente);
		$xTabla->titular_de_cumplimiento($cumplimiento);
		$xTabla->clave_de_persona($persona);
		if($ODom != null){
			$xTabla->calle( $ODom->getCalle() );
			$xTabla->codigo_postal( $ODom->getCodigoPostal() );
			$xTabla->colonia( $ODom->getColonia() );
			$xTabla->telefono( $xSoc->getTelefonoPrincipal() );
			$xTabla->municipio( $ODom->getMunicipio() );
			$xTabla->localidad( $ODom->getClaveDeLocalidad() );
			$xTabla->estado( $ODom->getEstado() );
			$xTabla->numero_exterior( $ODom->getNumeroExterior() );
			$xTabla->numero_interior( $ODom->getNumeroInterior() );
		}
		$xTabla->query()->insert()->save();
		$xFRM->addAvisoRegistroOK();
	}
} else if($action == MQL_MOD){		//Modificar
	//iniciar
	$clave 		= parametro($xTabla->getKey(), null, MQL_RAW);
	if($clave != null){
		$xTabla->setData( $xTabla->query()->initByID($clave));
		$xTabla->setData($_REQUEST);
		//modificar la parte de personas asociadas
		$xTabla->gerente_sucursal($gerente);
		$xTabla->titular_de_cumplimiento($cumplimiento);
		$xTabla->clave_de_persona($persona);
		if($ODom != null){
			$xTabla->calle( $ODom->getCalle() );
			$xTabla->codigo_postal( $ODom->getCodigoPostal() );
			$xTabla->colonia( $ODom->getColonia() );
			$xTabla->telefono( $xSoc->getTelefonoPrincipal() );
			$xTabla->municipio( $ODom->getMunicipio() );
			$xTabla->localidad( $ODom->getClaveDeLocalidad() );
			$xTabla->estado( $ODom->getEstado() );
			$xTabla->numero_exterior( $ODom->getNumeroExterior() );
			$xTabla->numero_interior( $ODom->getNumeroInterior() );
		}				
		$xTabla->query()->update()->save($clave);
		$xFRM->addAvisoRegistroOK();
	}
}


$msg		= "";
//clave de sucursal
//$xFRM->addJsBasico();
$xFRM->addPersonaBasico("", false, $xTabla->clave_de_persona()->v(), "", "TR.Clave Vinculada");
$xFRM->OText("codigo_sucursal", $xTabla->codigo_sucursal()->v(), "TR.codigo sucursal");
$xFRM->OText("nombre_sucursal", $xTabla->nombre_sucursal()->v(), "TR.nombre sucursal");
$xFRM->OMoneda("clave_numerica", $xTabla->clave_numerica()->v(), "TR.clave numerica");

$xFRM->OMoneda("caja_local_residente", $xTabla->caja_local_residente()->v(), "TR.caja local residente");
$xFRM->addPersonaBasico("2", false, $xTabla->gerente_sucursal()->v(), "", "TR.gerente sucursal");
$xFRM->addPersonaBasico("3", false, $xTabla->titular_de_cumplimiento()->v(), "", "TR.Oficial de Cumplimiento");

$xFRM->OMoneda("hora_de_inicio_de_operaciones", $xTabla->hora_de_inicio_de_operaciones()->v(), "TR.hora de inicio de operaciones");
$xFRM->OMoneda("hora_de_fin_de_operaciones", $xTabla->hora_de_fin_de_operaciones()->v(), "TR.hora de fin de operaciones");


echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>