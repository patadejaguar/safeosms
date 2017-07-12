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
$xHP		= new cHPage("TR.PERMISOS DEL SISTEMA", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$jxc 		= new TinyAjax();
$buscar		= ""; 

$idreporte	= parametro("idreporte", "", MQL_RAW);
if($idreporte > 0){
	$xRpt	= new cGeneral_reports();
	$xRpt->setData( $xRpt->query()->initByID($idreporte) );
	$darch		= explode("/", $xRpt->idgeneral_reports()->v());
	$dcnt		= count($darch) - 1;
	$buscar		= $darch[$dcnt];
	$buscar		= str_replace("?", "", $buscar);
}


function jsaListarPermisos($usuario, $superior, $buscar){
	//$superior	= setNoMenorQueCero($superior);
	$xPerm		= new cSystemPermissions();
	$superior	= ($superior == SYS_TODAS) ? false : $superior;
	$rs			= $xPerm->getPermitidos($usuario, $superior, $buscar);
	$xTab		= new cHTabla();
	
	
	$xTab->initRow();
	$xTab->addTH("TR.CLAVE");
	$xTab->addTH("TR.SUPERIOR");
	$xTab->addTH("TR.NOMBRE");
	$xTab->addTH("TR.PERMISO");
	$xTab->endRow();

	$xItem	= new cGeneral_menu();
	$xChk	= new cHCheckBox();
	foreach ($rs as $rw){
		$xItem->setData($rw);		
		$xTab->initRow();
		$id		= $xItem->idgeneral_menu()->v();
		$xTab->addTD($id," class='success' ");
		$xTab->addTD($xItem->menu_parent()->v() . "-" . $xItem->menu_type()->v());
		$xTab->addTD($xItem->menu_title()->v(), " class='success' ");
		$xChk->addEvent("jsGuardarPermisos(this)", "onchange", true);
		$xTab->addTD($xChk->get("", "id-$id", true));
		$xTab->endRow();
	}
	$rs			= $xPerm->getNegados($usuario, $superior, $buscar);
	foreach ($rs as $rw){
		$xItem->setData($rw);
		$xTab->initRow();
		$id		= $xItem->idgeneral_menu()->v();
		$xTab->addTD($id, " class='warning' ");
		$xTab->addTD($xItem->menu_parent()->v());
		$xTab->addTD($xItem->menu_title()->v(), " class='warning' ");
		$xChk->addEvent("jsGuardarPermisos(this)", "onchange", true);
		$xTab->addTD($xChk->get("", "id-$id", false));
		$xTab->endRow();
	}	
	$rs	= null;
	return $xTab->get();
}
function jsaSetClearPermisos($id){ 	$xP	= new cSystemPermissions();	$xP->setLiberar();	$xP->setClear(); return $xP->getMessages(OUT_HTML); }
function jsaSetLiberarPermisos($id){	$xP	= new cSystemPermissions();	$xP->setLiberar(); return $xP->getMessages(OUT_HTML); }
function jsaSetAplicarPerfiles($id){
	$xP					= new cSystemPermissions();
	$xP->setAplicarPerfil();
	$xFil				= new cFileLog();
	$xFil->setWrite($xP->getMessages()); $xFil->setClose();
	return $xFil->getLinkDownload("Cambios");
}
$jxc ->exportFunction('jsaListarPermisos', array('idniveldeusuario', 'idmenusuperior', 'idbuscar'), "#idmenu" );

//$jxc ->exportFunction('jsaSetAplicarPermisos', array('idclaveactual', 'idniveldeusuario'), "#idSalida" );
$jxc ->exportFunction('jsaSetClearPermisos', array('idclaveactual'), "#idmsg" );
$jxc ->exportFunction('jsaSetAplicarPerfiles', array('idclaveactual'), "#idmsg" );
$jxc ->exportFunction('jsaSetLiberarPermisos', array('idclaveactual'), "#idmsg" );

//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
//$xFRM->addJsBasico();
$xSelUser	= $xSel->getListaDeNivelDeUsuario();
//$xSelUser->setDivClass("tx12");
$xSelUser->addEvent("onchange", "jsaListarPermisos()");

$xSelMenu	= $xSel->getListaDeMenuParents();
$xSelMenu->addEspOption(SYS_TODAS,SYS_TEXTO_TODAS);
$xSelMenu->setOptionSelect(SYS_TODAS);
$xSelMenu->addEspOption("0", "INICIO");
//$xSelMenu->setDivClass("tx12");
$xSelMenu->addEvent("onchange", "jsaListarPermisos()");

$xFRM->addHElem($xSelUser->get(true));
$xFRM->addHElem($xSelMenu->get(true));
$xFRM->OText_13("idbuscar", $buscar, "TR.BUSCAR");

$xFRM->addHTML("<div id='idmenu' class='tx1'></div>");


$xFRM->addAviso("", "idmsg");
//$xFRM->OButton("TR.GUARDAR Permisos", "jsaSetClearPermisos()", $xFRM->ic()->ELIMINAR);
$xFRM->OButton("TR.Obtener Permisos", "jsaListarPermisos()", $xFRM->ic()->CARGAR);
$xFRM->OButton("TR.Limpiar Permisos", "jsaSetClearPermisos()", $xFRM->ic()->ELIMINAR);
$xFRM->OButton("TR.Liberar Permisos", "jsaSetLiberarPermisos()", $xFRM->ic()->LIBERAR);
$xFRM->OButton("TR.Aplicar Perfiles", "jsaSetAplicarPerfiles()", $xFRM->ic()->GRUPO);
echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsGuardarPermisos(obj){
	var idP		= $("#idniveldeusuario").val();	
	var isEna	= $('#'+obj.id).prop('checked');
	var DD		= String(obj.id).split("-");
	var idmenu	= DD[1];
	$.cookie.json 	= true;
	var mURL	= "../svc/sudo.svc.php?id=" + idmenu  + "&enable=" + isEna + "&perfil="+idP;
	//var si		= confirm(this.lang("Confirma Eliminar el Registro"));
	//if (si) {
		$.getJSON( mURL, function( data ) {
			  //var str     = "";
			  if (data.error == true) {
				xG.alerta({msg:data.message});
			  } else {
				xG.alerta({msg:data.message, nivel:"ok"});
				//$("#tr-" + tbl + "-" + id).empty();
			  }
			}
		);	
}
</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin();

?>