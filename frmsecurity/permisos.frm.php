<?php
//=====================================================================================================
//=====>	INICIO_H
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	$theFile					= __FILE__;
	$permiso					= getSIPAKALPermissions($theFile);
	if($permiso === false){		header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//<=====	FIN_H
//=====================================================================================================
$xHP			= new cHPage("TR.Permisos del Sistema", HP_FORM);

$jxc = new TinyAjax();
function taRetMenuChilds($iparent){
	$sql = "SELECT
				`general_menu`.`idgeneral_menu` AS 'codigo',
				`general_menu`.`menu_title` AS 'titulo',
				`general_menu`.`menu_rules` AS 'permisos'
			FROM
				`general_menu` `general_menu`
			WHERE menu_parent='$iparent'";
	$tMenu = new cTabla($sql);
	$tMenu->setEventKey("returnMenuNode");

	$xTbl 	= $tMenu->show();
	return $xTbl;

}
function taRetOneNode($iMenu){
	$sql = "SELECT
				*
			FROM
				`general_menu` `general_menu`
			WHERE idgeneral_menu='$iMenu'
			ORDER BY menu_title ";
	$Itbl = obten_filas($sql);

	return "<table >
			<tbody>
				<tr>
					<th class='izq'>Indice de Elemento</th>
					<td onclick='DetailGetPermisosByID(" . $Itbl["idgeneral_menu"] . ")'>" . $Itbl["idgeneral_menu"] . "</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<th class='izq'>Titulo del Elemento</th>
					<td>" . $Itbl["menu_title"] . "</td>
					<th class='izq'>Archivo del Elemento</th>
					<td>" . $Itbl["menu_file"] . "</td>
				</tr>
				<tr>
					<!-- <th class='izq'>Tipo de Menu</th>
					<td>" . $Itbl["menu_type"] . "</td> -->
					<th class='izq'>Elemento Superior</th>
					<th class='izq'>" . $Itbl["menu_parent"] . "</td>
				</tr>
				<!--<tr>
					<th class='izq'>Permisos</th>
					<td colspan=\"3\">" . $Itbl["menu_rules"] . "</td>
				</tr> -->
			</tbody>
		</table>
";


}

function getPermisosByID($iMenu){
	$sql = "SELECT menu_rules	FROM `general_menu`
			WHERE idgeneral_menu='$iMenu' LIMIT 0,1";
	$Itbl 	= obten_filas($sql);
	$sPerms	= $Itbl["menu_rules"];
	$aPerms	= explode(",", $sPerms);
	$tab 	= new TinyAjaxBehavior();
	foreach ( $aPerms as $key => $value ){
		$DItem	= explode( STD_LITERAL_DIVISOR, $value);
		$nivel	= $DItem[0];
		$perm	= $DItem[1];
		if ( $nivel != 99 ){
			$tab -> add(TabSetValue::getBehavior($nivel . "@false", "false"));
			$tab -> add(TabSetValue::getBehavior($nivel . "@" . $perm, "on"));
			$tab -> add(TabSetValue::getBehavior("idNiv" . $nivel, $value));
		}
	}
	return $tab -> getString();
}
/***/
function getListenPermissions($iMenu){
	$sql = "SELECT
				menu_rules
			FROM
				`general_menu` `general_menu`
			WHERE idgeneral_menu='$iMenu' LIMIT 0,1";
	$Itbl 	= obten_filas($sql);
	$sPerms	= $Itbl["menu_rules"];
	$aPerms	= explode(",", $sPerms);
	//Array de Tipos de Permisos
	$aTipoPerms	= array(
						0 => "false",
						1 => "rw",
						2 => "ro"
					);

	$options	= "";
	
	$sqlP = "SELECT
				`general_niveles`.`idgeneral_niveles`,
				`general_niveles`.`descripcion_del_nivel` 
			FROM
				`general_niveles` `general_niveles` WHERE idgeneral_niveles != 99 ";
	$rs			= getRecordset($sqlP );
	$xHSel		= new cHSelect();
	$xHSel->addOptions( array("" => "Ninguno", "ro" => "Solo Puede Ver", "rw" => "Todo") );
	while( $rw = mysql_fetch_array($rs) ){
		
		$nivel	= $rw["idgeneral_niveles"];
		$desc	= $rw["descripcion_del_nivel"];
		$tds	= "";
		$selOp	= "";
		foreach ( $aTipoPerms as $key => $value ){
			if ( in_array("$nivel@$value", $aPerms ) ){
				$selOp	= $value;
			}
		}	
		$xHSel->setDivClass("tx1");	
		$mNivel	= $xHSel->get("idNiv$nivel",  "$desc", $selOp);
		$options .= $mNivel;

	}
	return	$options;
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

//$jxc ->exportFunction('taRetMenuChilds', array('idParents'), "#ilstChilds");
//$jxc ->exportFunction('taRetOneNode', array('idKeyNow'), "#ilstChilds");
//$jxc ->exportFunction('getPermisosByID', array('idKeyNow') );
$jxc ->exportFunction('getListenPermissions', array('idKeyNow'), "#idPermisos" );
$jxc ->exportFunction('jsaSetAplicarPermisos', array('idKeyNow', 'idusuario'), "#idSalida" );
$jxc ->exportFunction('jsaSetClearPermisos', array('idKeyNow'), "#idSalida" );
$jxc ->exportFunction('jsaSetAplicarPerfiles', array('idKeyNow'), "#idSalida" );

$jxc ->exportFunction('jsaSetLiberarPermisos', array('idKeyNow'), "#idSalida" );

$jxc ->process();

$xHP->init("getListenPermissions()");


$xFRM		= new cHForm("frmpermisos", "./");
$xBtn		= new cHButton();		
$xTxt		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();


			$vSql = "SELECT
				`general_menu`.`idgeneral_menu`,
				`general_menu`.`menu_title` AS 'descripcion'
			FROM
				`general_menu` `general_menu`
			WHERE menu_type='parent'
			ORDER BY `general_menu`.`menu_parent`,`general_menu`.`idgeneral_menu` ";
			$cSel = new cSelect("cParents", "idParents", $vSql);
			$cSel->setEsSql();
			$cSel->addEspOption("0", "Principal");
			$cSel->addEspOption("9999", "No Asignados");
			//$cSel->addEvent("onchange", "getListChilds");
			
			$xFRM->addDivSolo($cSel->get("TR.Menu"), "", "tx34", "", array( 1=> array("id" => "trNY") ));
			$xFRM->addFootElement('<input type="hidden" id="strCompPermissions" /><input type="hidden" id="idKeyNow" />
					<input type="hidden" id="idusuario" />');
			
			$xFRM->addDivSolo("<div id=\"idPermisos\" ></div>",
					"<div id=\"idSalida\" ></div>", "tx24", "tx24", array(
					1 => array("id" => "idforms"),
					2 => array("id" => "ilstChilds")
			));
			$xFRM->addHTML('<div id="tdCompilado"></div><input type="hidden" value="99@rw" name="cPermisosTotales" id="idPermisosTotales" />');
			
			//$xFRM->OButton("TR.Aplicar de Forma Recursiva", "compilePermissions(); tasetSendRecursive();", "ejecutar");
			//$xFRM->OButton("TR.Salvar Permisos", "compilePermissions(); tasetSavePermissions();", "guardar");
			
			$xFRM->OButton("TR.Limpiar Permisos", "jsaSetClearPermisos()", "eliminar");
			$xFRM->OButton("TR.Liberar Permisos", "jsaSetLiberarPermisos()", "libre");
			$xFRM->OButton("TR.Aplicar Perfiles", "jsaSetAplicarPerfiles()", "usuarios");
			$xFRM->addAviso(" ");
			echo $xFRM->get();
$jxc ->drawJavaScript(false, true);
?>
<script>
var mSelParent	= document.getElementById("idParents");
function transID(evt){ }
function compilePermissions(){ }
function getListChilds(id){
	//var mIdx = mSelParent.options[mSelParent.selectedIndex].value;
	//$("#idKeyNow").val(mIdx);
	//taRetMenuChilds();
	//DetailGetPermisosByID(mIdx);
}
function DetailGetPermisosByID(mID){
	$("#idKeyNow").val(mID);	
	getListenPermissions();
}
function returnMenuNode(id){
	$("#idKeyNow").val(id);
	taRetOneNode();
}
function jsSaveLog(str){
	<?php
	if(MODO_DEBUG == true){
		echo "if( window.console ) { 
		window.console.log( str );
		}";
	}
		?>	
}
<?php
$xHP->fin(); 
?>