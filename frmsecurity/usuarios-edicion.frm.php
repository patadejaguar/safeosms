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

$xHP->addJTableSupport();
$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xTxt		= new cHText();

$xFRM->setTitle($xHP->getTitle());
$xFRM->addCerrar();
$xFRM->setNoAcordion();


$xUser		= new cSystemUser();
$xUser->init();
$iduser		= $xUser->getID();
$nivel		= $xUser->getNivel();

$w			= ($xUser->getPuedeEditarUsuarios() == false) ? " AND `idusuarios`=$iduser " : " AND (`niveldeacceso` <= $nivel) ";

$xSelLE		= $xSel->getListaDeCatalogoGenerico("usuarios_estatus", "idestatus", SYS_TODAS);
$xSelLE->setNoMayus();
$xSelLE->addEvent("onchange", "jsLoadFiltro()");
$xSelLE->setDivClass("tx4 tx18 red");

$xFRM->addHElem( $xSelLE->get("TR.ESTATUS", true) );

$xSelLN 	= $xSel->getListaDeNivelDeUsuario("idnivel", 0, $nivel);

$xTxt->addEvent("jsLoadFiltro()", "onkeyup");
$xFRM->addHElem( $xTxt->get("idbuscar", "", "TR.BUSCAR") );
//$xFRM->addHElem( $xSelLN->get(true) );
//$xFRM->OText("idbusqueda", "", "TR.BUSCAR");


$sql		= "SELECT
	`usuarios`.`codigo_de_persona`,
	`usuarios`.`idusuarios`     AS `clave`,
	`usuarios`.`nombreusuario`  AS `nombre`,
	`usuarios`.`nombrecompleto` AS `nombre_completo`,
	`usuarios`.`alias`          AS `alias`,
	`usuarios`.`puesto`         AS `puesto`,
	`usuarios`.`niveldeacceso`  AS `nivel_de_acceso`,
	`usuarios`.`estatus`        AS `estatus`,
	`usuarios`.`uuid_mail`        AS `email`,
	`usuarios`.`expira`,
	`usuarios`.`sucursal`
	 
FROM
	`usuarios` `usuarios`
WHERE `usuarios`.`idusuarios`>0 $w ORDER BY `usuarios`.`estatus` DESC, `usuarios`.`niveldeacceso`, `usuarios`.`nombrecompleto`";

$xHG	= new cHGrid("iddiv",$xHP->getTitle());

$xHG->setSQL($sql);
$xHG->addList();
$xHG->setOrdenar();

$xHG->col("clave", "TR.CLAVE", "5%");
$xHG->col("nombre", "TR.NOMBRE", "10%");

$xHG->col("sucursal", "TR.SUCURSAL", "10%");

//$xHG->col("nombre_completo", "TR.NOMBRE_COMPLETO", "40%");
$xHG->col("alias", "TR.ALIAS", "20%");
$xHG->col("puesto", "TR.PUESTO", "10%");

$xHG->col("email", "TR.CORREO_ELECTRONICO", "10%");

$xHG->col("estatus", "TR.ESTATUS", "10%");

if( $xUser->getPuedeEditarUsuarios() == true ){
	$xHG->OButton("TR.EDITAR", "jsEditarUsuario('+ data.record.clave +')", "edit.png");
	$xHG->OButton("TR.PASSWORD", "jsVerMiPassword('+ data.record.clave +')", "unlocked.png");
	
	$xHG->OButton("TR.BAJA", "jsActionBaja('+ data.record.clave +',\''+ data.record.estatus +'\')", "prohibition.png");
}

if($xUser->getPuedeAgregarUsuarios()== true){
	$xHG->OToolbar("TR.AGREGAR USUARIO","jsAgregarUsuario()", "grid/add.png");
}

$xFRM->addHElem("<div id='iddiv'></div>");

$xFRM->addJsCode( $xHG->getJs(true) );


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
	if(data){
		if(typeof data.msg !== "undefined"){
			xG.alerta({msg:data.msg, callback: jsReloadGrid});
		}
	}
}
function jsAgregarUsuario(){
	var xrl		= "../frmsecurity/altausuarios.frm.php?";
	xG.w({ url: xrl, tiny : true, callback: jsReloadGrid }); 
}
function jsEditarUsuario(id){
	var xrl		= "../frmsecurity/usuarios.edit.frm.php?clave=" + id;
	xG.w({ url: xrl, tiny : true, callback: jsReloadGrid }); 
}
function jsVerMiPassword(id){ 
	var xrl		= "../frmsocios/socios.usuario.frm.php?usuario=" + id;
	xG.w({ url: xrl, tiny : true, callback: jsReloadGrid }); 	
}
function jsActionBaja(id, estatus){
	//var id	= dd.clave;
	var jsOkBaja	= function(){  jsBajaUsuario(id); }
	
	if(estatus == "activo"){
		xG.confirmar({msg: "MSG_USER_CONFIRM_BAJA", callback: jsOkBaja });
	} else if(estatus == "baja"){
		xG.alerta({msg : "MSG_USER_EN_BAJA"});
		
	} else if(estatus == "suspension"){
		xG.confirmar({msg: "MSG_USER_CONFIRM_BAJA", callback: jsOkBaja });
	}
}
function jsLoadFiltro(){
	var ids	= $("#idestatus").val();
	var ss	= $("#idbuscar").val();
	
	var str	= "";
	if(ids !== "todas"){
		str	+= " AND (estatus='" + ids + "') ";
	}
	if($.trim(ss) !== ""){
		str += " AND (`puesto` LIKE '%" + ss + "%' OR `nombreusuario` LIKE '%" + ss + "%' OR `nombrecompleto` LIKE '%" + ss + "%' OR `niveldeacceso` LIKE '%" + ss + "%')";
	}
	if($.trim(str) !== ""){
		str		= "&w=" + base64.encode(str);
		$("#iddiv").jtable("destroy");
		jsLGiddiv(str);
	}
}
function jsReloadGrid(){
	$("#iddiv").jtable("destroy");
	jsLGiddiv();
}
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>