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
$xHP		= new cHPage("TR.AGREGAR USUARIO", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$xUser		= new cSystemUser(getUsuarioActual()); $xUser->init();

$originador	= parametro("originador",0, MQL_INT);
$suborigen	= 0;
$EsAdmin	= false;
//$EsActivo	= false;
if($xUser->getEsOriginador() == true){
	$xOrg	= new cLeasingUsuarios();
	if($xOrg->initByIDUsuario($xUser->getID()) == true){
		$originador	= $xOrg->getOriginador();
		$suborigen	= $xOrg->getSubOriginador();
		if($xOrg->getEsAdmin() == true){
			$suborigen			= 0;
			$EsAdmin			= true;
		} else {
			$originador			= 0;
		}
		if($xOrg->getEsActivo() == false){
			$xHP->goToPageError(403);
		}
	}
}

//$jxc 		= new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$observaciones	= parametro("idobservaciones");



$xHP->addJTableSupport();
$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xTabla		= new cLeasing_usuarios();
$xTabla->setData($xTabla->query()->initByID($clave));
$xFRM->setNoAcordion();


$xFRM->OHidden("idleasing_usuarios", "NULL");

if($xUser->getEsOriginador() == false){
	if($originador <= 0){
		$xFRM->addHElem($xSel->getListaDeOriginadores("originador", $xTabla->originador()->v())->get(true) );
	} else {
		$xFRM->OHidden("originador", $originador);
	}
} else {
	$xFRM->OHidden("originador", $originador);
}
if($originador>0){
	$xOU	= new cLeasingOriginadores($originador);
	$xOU->init();
	$xFRM->addSeccion("idx12", "TR.ORIGINADOR");
	$xFRM->addHElem( $xOU->getFicha() );
	$xFRM->endSeccion();
}
$xFRM->addSeccion("idnx1", "TR.DATOS");

$xFRM->OText("nombre", $xTabla->nombre()->v(), "TR.NOMBRE");
$xFRM->OMail("correo_electronico", $xTabla->correo_electronico()->v(), "TR.CORREO_ELECTRONICO");
$xFRM->OMoneda("telefono", $xTabla->telefono()->v(), "TR.TELEFONO");
$xFRM->ONumero("tasa_com", $xTabla->tasa_com()->v(), "TR.TASA COMISION");

if($EsAdmin == true){
	$xFRM->OSiNo("TR.ESTATUSACTIVO","estatus", $xTabla->estatus()->v());
}
if($originador == 0 AND $suborigen == 0){
	$xFRM->OSiNo("TR.ADMINISTRADOR","administrador", $xTabla->administrador()->v());
}
$xFRM->endSeccion();


$xFRM->addCRUD($xTabla->get(), true);

echo $xFRM->get();



//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>