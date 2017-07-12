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
$xHP		= new cHPage("TR.Actualizar Usuario", HP_FORM);
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
$empresa	= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT);
$grupo		= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria = parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);

$observaciones= parametro("idobservaciones");

$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xFRM->addCerrar();
$xFRM->setNoAcordion();


$xUser		= new cSystemUser();
$xUser->init();
$iduser		= $xUser->getID();
$nivel		= $xUser->getNivel();

$w			= ($xUser->getPuedeEditarUsuarios() == false) ? " AND `idusuarios`=$iduser " : " AND (`niveldeacceso` <= $nivel) ";


$sql		= "SELECT
	`usuarios`.`codigo_de_persona`,
	`usuarios`.`idusuarios`     AS `clave`,
	`usuarios`.`nombreusuario`  AS `nombre`,
	`usuarios`.`nombrecompleto` AS `nombre_completo`,
	`usuarios`.`alias`          AS `alias`,
	`usuarios`.`puesto`         AS `puesto`,
	`usuarios`.`niveldeacceso`  AS `nivel_de_acceso`,
	`usuarios`.`estatus`        AS `estatus`,
	`usuarios`.`expira`,
	`usuarios`.`sucursal`
	 
FROM
	`usuarios` `usuarios`
WHERE `usuarios`.`estatus` ='" . SYS_USER_ESTADO_ACTIVO . "' $w ORDER BY `usuarios`.`estatus` DESC, `usuarios`.`niveldeacceso`, `usuarios`.`nombrecompleto`";
$xFRM->addSeccion("idactivos", "TR.ESTATUSACTIVO");
$xT		= new cTabla($sql,1);
$xT->setKeyTable(TUSUARIOS_REGISTRO);
$xT->setKeyField("idusuarios");
$xT->OButton("TR.Cambiar password","jsVerMiPassword(" . HP_REPLACE_ID . ")", $xFRM->ic()->PASSWORD);
$xT->OButton("TR.Baja","jsBajaUsuario(" . HP_REPLACE_ID . ")", $xFRM->ic()->BANEAR);
$xT->OButton("TR.Supender","jsSuspenderUsuario(" . HP_REPLACE_ID . ")", $xFRM->ic()->PARAR);
//$xT->OButton("TR.Activar","jsActivarUsuario(" . HP_REPLACE_ID . ")", $xFRM->ic()->SALUD);
$xT->setOmitidos("estatus");
if($xUser->getPuedeEditarUsuarios() == true){
	$xT->addEditar();
} else {
	$xT->setOmitidos("nivel_de_acceso");
}
$xFRM->addHElem( $xT->Show() );
$xFRM->endSeccion();

if($xUser->getPuedeEditarUsuarios() == true){
	//BAJA
	$sql		= "SELECT
	`usuarios`.`codigo_de_persona`,
	`usuarios`.`idusuarios`     AS `clave`,
	`usuarios`.`nombreusuario`  AS `nombre`,
	`usuarios`.`nombrecompleto` AS `nombre_completo`,
	`usuarios`.`alias`          AS `alias`,
	`usuarios`.`puesto`         AS `puesto`,
	`usuarios`.`niveldeacceso`  AS `nivel_de_acceso`,
	`usuarios`.`estatus`        AS `estatus`,
	`usuarios`.`expira`,
	`usuarios`.`sucursal`
	
FROM
	`usuarios` `usuarios`
WHERE `usuarios`.`estatus` ='" . SYS_USER_ESTADO_BAJA. "' $w ORDER BY `usuarios`.`estatus` DESC, `usuarios`.`niveldeacceso`, `usuarios`.`nombrecompleto`";
	$xFRM->addSeccion("idbajas", "TR.BAJA");
	$xT		= new cTabla($sql,1);
	$xT->setKeyTable(TUSUARIOS_REGISTRO);
	$xT->setKeyField("idusuarios");
	$xT->OButton("TR.Cambiar password","jsVerMiPassword(" . HP_REPLACE_ID . ")", $xFRM->ic()->PASSWORD);
	//$xT->OButton("TR.Baja","jsBajaUsuario(" . HP_REPLACE_ID . ")", $xFRM->ic()->BANEAR);
	//$xT->OButton("TR.Supender","jsSuspenderUsuario(" . HP_REPLACE_ID . ")", $xFRM->ic()->PARAR);
	$xT->OButton("TR.Activar","jsActivarUsuario(" . HP_REPLACE_ID . ")", $xFRM->ic()->SALUD);
	$xT->setOmitidos("estatus");
	$xT->addEditar();
	
	$xFRM->addHElem( $xT->Show() );
	$xFRM->endSeccion();
	
	//SUSPENDIDOS
	$sql		= "SELECT
	`usuarios`.`codigo_de_persona`,
	`usuarios`.`idusuarios`     AS `clave`,
	`usuarios`.`nombreusuario`  AS `nombre`,
	`usuarios`.`nombrecompleto` AS `nombre_completo`,
	`usuarios`.`alias`          AS `alias`,
	`usuarios`.`puesto`         AS `puesto`,
	`usuarios`.`niveldeacceso`  AS `nivel_de_acceso`,
	`usuarios`.`estatus`        AS `estatus`,
	`usuarios`.`expira`,
	`usuarios`.`sucursal`
	
FROM
	`usuarios` `usuarios`
WHERE `usuarios`.`estatus` ='" . SYS_USER_ESTADO_SUSP . "' $w ORDER BY `usuarios`.`estatus` DESC, `usuarios`.`niveldeacceso`, `usuarios`.`nombrecompleto`";
	$xFRM->addSeccion("idsuspendidos", "TR.SUSPENDIDO");
	$xT		= new cTabla($sql,1);
	$xT->setKeyTable(TUSUARIOS_REGISTRO);
	$xT->setKeyField("idusuarios");
	$xT->OButton("TR.Cambiar password","jsVerMiPassword(" . HP_REPLACE_ID . ")", $xFRM->ic()->PASSWORD);
	$xT->OButton("TR.Baja","jsBajaUsuario(" . HP_REPLACE_ID . ")", $xFRM->ic()->BANEAR);
	//$xT->OButton("TR.Supender","jsSuspenderUsuario(" . HP_REPLACE_ID . ")", $xFRM->ic()->PARAR);
	$xT->OButton("TR.Activar","jsActivarUsuario(" . HP_REPLACE_ID . ")", $xFRM->ic()->SALUD);
	$xT->setOmitidos("estatus");
	$xT->addEditar();
	
	$xFRM->addHElem( $xT->Show() );
	$xFRM->endSeccion();
}


echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsBajaUsuario(id){
	xG.svc({url:"su.svc.php?action=baja&usuario=" + id, callback: jsAvisos});
}
function jsSuspenderUsuario(id){
	xG.svc({url:"su.svc.php?action=suspension&usuario=" + id, callback: jsAvisos});
}
function jsActivarUsuario(id){
	xG.svc({url:"su.svc.php?action=activar&usuario=" + id, callback: jsAvisos});
}
function jsAvisos(data){
	if(data.msg !== "undefined"){
		xG.alerta({msg:data.msg});
	}
}
function jsVerMiPassword(id){ 
	var xrl		= "../frmsocios/socios.usuario.frm.php?usuario=" + id;
	xG.w({ url: xrl, tiny : true }); 	
}
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>