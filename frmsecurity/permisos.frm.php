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


function jsaListarPermisos($usuario, $superior, $buscar, $tipo){
	//$superior	= setNoMenorQueCero($superior);
	$xPerm		= new cSystemPermissions();
	$superior	= ($superior == SYS_TODAS) ? false : $superior;
	$rs			= $xPerm->getPermitidos($usuario, $superior, $buscar);
	$xTab		= new cHTabla();
	$bloq		= true;
	$nobloq		= true;
	
	if($tipo !== SYS_TODAS){
		$tipo	= setNoMenorQueCero($tipo);
		if($tipo == 1){
			$bloq	= false;
		} else {
			$nobloq	= false;
		}
	}
	
	
	$xTab->initRow();
	$xTab->addTH("TR.CLAVE");
	$xTab->addTH("TR.SUPERIOR");
	$xTab->addTH("TR.DESCRIPCION");
	$xTab->addTH("TR.TIPO");
	$xTab->addTH("TR.PERMISO");
	$xTab->endRow();

	$xItem	= new cGeneral_menu();
	$xChk	= new cHCheckBox();
	if($nobloq == true){
		foreach ($rs as $rw){
			$xItem->setData($rw);		
			$xTab->initRow();
			$id		= $rw[$xItem->IDGENERAL_MENU];
			$tit	= ($rw[$xItem->MENU_TITLE] == "") ? $rw[$xItem->MENU_FILE] : $rw[$xItem->MENU_TITLE];
			$xTab->addTD($id," class='success' ");
			$xTab->addTD($rw[$xItem->MENU_PARENT]);
			$xTab->addTD($tit, " class='success' ");
			$xTab->addTD($rw[$xItem->MENU_TYPE]);
			$xChk->addEvent("jsGuardarPermisos(this)", "onchange", true);
			$xTab->addTD($xChk->get("", "id-$id", true));
			
			$xTab->endRow();
		}
	}
	$rs			= $xPerm->getNegados($usuario, $superior, $buscar);
	if($bloq == true){
		foreach ($rs as $rw){
			$xItem->setData($rw);
			$xTab->initRow();
			$id		= $rw[$xItem->IDGENERAL_MENU];
			$tit	= ($rw[$xItem->MENU_TITLE] == "") ? $rw[$xItem->MENU_FILE] : $rw[$xItem->MENU_TITLE];
			
			$xTab->addTD($id, " class='warning' ");
			
			$xTab->addTD($rw[$xItem->MENU_PARENT]);
			$xTab->addTD($tit, " class='warning' ");
			$xTab->addTD($rw[$xItem->MENU_TYPE]);
			
			$xChk->addEvent("jsGuardarPermisos(this)", "onchange", true);
			$xTab->addTD($xChk->get("", "id-$id", false));
			$xTab->endRow();
		}	
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
function jsaSetClonarPerfiles($idnivel, $idclonar){
	$xP					= new cSystemPermissions();
	$xP->setCrearNuevoNivel($idclonar, $idnivel);
	return $xP->getMessages(OUT_HTML);
}

$jxc ->exportFunction('jsaListarPermisos', array('idniveldeusuario', 'idmenusuperior', 'idbuscar', 'idtipo'), "#idmenu" );

//$jxc ->exportFunction('jsaSetAplicarPermisos', array('idclaveactual', 'idniveldeusuario'), "#idSalida" );
$jxc ->exportFunction('jsaSetClearPermisos', array('idclaveactual'), "#idmsg" );
$jxc ->exportFunction('jsaSetAplicarPerfiles', array('idclaveactual'), "#idmsg" );
$jxc ->exportFunction('jsaSetLiberarPermisos', array('idclaveactual'), "#idmsg" );
$jxc ->exportFunction('jsaSetClonarPerfiles', array('idniveldeusuario', 'idclonar'), "#idmsg" );

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

$xHSel2	= new cHSelect();
$xHSel2->addOptions(array(
		SYS_TODAS => SYS_TODAS,
		SYS_UNO	 => "PERMITIDOS",
		SYS_CERO => "BLOQUEADOS"
));
$xHSel2->setDivClass("tx4 tx18 green");

$xFRM->addHElem( $xHSel2->get("idtipo", "TR.TIPO", SYS_TODAS) );

//$xFRM->ONumero("idclonar", 0, "TR.COPIAR");
$xFRM->addHElem( $xSel->getListaDeNivelDeUsuario("idclonar")->get("TR.CLONAR DE", true) );

$xFRM->addHElem("<div id='idmenu' class='tx1'></div>");


$xFRM->addAviso("", "idmsg");
//$xFRM->OButton("TR.GUARDAR Permisos", "jsaSetClearPermisos()", $xFRM->ic()->ELIMINAR);
$xFRM->OButton("TR.Obtener Permisos", "jsaListarPermisos()", $xFRM->ic()->CARGAR);
$xFRM->OButton("TR.Limpiar Permisos", "jsaSetClearPermisos()", $xFRM->ic()->ELIMINAR);
$xFRM->OButton("TR.Liberar Permisos", "jsaSetLiberarPermisos()", $xFRM->ic()->LIBERAR);
$xFRM->OButton("TR.Aplicar Perfiles", "jsaSetAplicarPerfiles()", $xFRM->ic()->GRUPO);

$xFRM->OButton("TR.Copiar Perfiles", "jsSetClonarPerfiles()", $xFRM->ic()->CONTROL);

echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsSetClonarPerfiles(){
	xG.confirmar({msg : "¿ Confirma copiar el perfil ?", callback: jsaSetClonarPerfiles});
}
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