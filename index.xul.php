<?php
if ( !file_exists(dirname(__FILE__) . "/core/core.config.os." . strtolower(substr(PHP_OS, 0, 3)) .  ".inc.php") ){ header("location: install/install.php"); } else {  }
//=====>	INICIO_H
	include_once("./core/go.login.inc.php");
	include_once("./core/core.error.inc.php");
	include_once("./core/core.html.inc.php");
	include_once("./core/core.init.inc.php");
	$theFile					= __FILE__;
	$permiso					= getSIPAKALPermissions($theFile);
	if($permiso === false){		header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//<=====	FIN_H

//=====================================================================================================

$xHP						= new cHPage(EACP_NAME . "{" . getSucursal() . "} - S.A.F.E. V " . $version. "", HP_FORM, "", ".");
$fecha_de_sesion			= parametro("f", fechasys());
$MenuParent					= parametro("m", 0, MQL_INT);
$isMobile 					= $xHP->isMobile();
$_SESSION[SYS_CLIENT_MOB]	= $isMobile;


$xUser						= new cSystemUser(getUsuarioActual());
$xUser->init();
$xUser->getUserRules();
$TasksPage					= $xUser->getTasksPage();


/**
 * Procedimientos AJAX
 */
$jxc 				= new TinyAjax();
function jsaGetMoneyChanges(){
	if(!isset($_SESSION["money"])){ $_SESSION["money"] = 0; }
	$_SESSION["money"] += 100;
	return  $_SESSION["money"];
}
function jsaGetMenu($subitems){
	
	$xMen	= new cHMenu();
	$xMen->setID("navigator");
	$menu	= "";
	$xMen->setIsMobile($_SESSION[SYS_CLIENT_MOB]);
	if($subitems > 0){
		$btn			= "";
		if($_SESSION[SYS_CLIENT_MOB] == true){
			$menu 		.= "<li><a onclick='var xG = new Gen(); xG.home();'><i class='fa fa-home fa-lg'></i>Inicio</a></li>";
		}		
		$menu			.= $xMen->getItems($subitems);
	}
	return  $menu;

}
$jxc ->exportFunction('jsaGetMenu', array('id-KeyEditable'), "#navigator");
$jxc ->exportFunction('jsaGetMoneyChanges', array("idMoneyExist"), "#idMoneyExist");

$jxc ->process();




/* ******************************************************************************************************************
//
//--------------------------------------Verifica las tareas Comunes.------------------------------------------------
//
****************************************************************************************************************** */

if(MODO_DEBUG == true){
	
} else {
	//checar cierre del dia
	$xCierre			= new cCierreDelDia();
	$aCierres			= $xCierre->check5Cierres($fecha_de_sesion);
	if($aCierres[SYS_ESTADO] == false){
		setLog($xCierre->getMessages(), 300);
		header("location:utils/frmcierredeldia.php"); exit();
	}
	//if(CREDITO_CONTROLAR_POR_PERIODOS == true){
	$xPerCred		= new cPeriodoDeCredito();
		if($xPerCred->checkPeriodoVigente($fecha_de_sesion) == false ){
			setLog($xPerCred->getMessages(), 300);
			header("location:frmcreditos/cambiarperiodo.frm.php?a=1");
		}
	//}
}

		
$PATHIMG 	= "images/common/";

$xHP->setNoCache();
$xHP->setNoDefaultCSS();

	$xHP->addCSS("css/general.css");
	$xHP->addCSS("css/jmenu.css");
	$xHP->addJsFile("js/jquery/jquery.ui.js");
	$xHP->addJsFile("js/jmenu/jMenu.jquery.js");
	$xHP->addJsFile("js/tinybox.js");

echo $xHP->getHeader();

?>
<style>
	html, body, object  {
		padding: 0 !important;
		margin : 0 !important;
	}
	#header  {
		text-align: center !important;
	}
	#banner {
		right: 0;
		bottom: 0;
		position:fixed !important;
	}
	.menu-trigger {
		right: 0;
		top: 0;
		position:fixed !important;
		display:block; text-shadow:0 -1px 1px #222;line-height:1.4em;color:#ffffff; width:3em; background-color:#000821;text-align:center;font-size:1.2em;border-radius:0 0 0 5px;
	}
	#wprincipal{ width: 100%; max-width: 100%;
	}
</style>
<body  onload="jsInitComponents();">
<?php
$adsense	= (MODO_DEBUG == true) ? "" : getAdsense();
//$xFRM->addToolbar("");
$xMenu		= new cHMenu();
$xMenu->setIsMobile($isMobile);
$menu		= "";

if($isMobile == false ){
	echo "<div id='header'>" . $xMenu->getAll() . "</div>";
	echo "<div id='wprincipal'><iframe id=\"idFPrincipal\" src=\"./$TasksPage\" width='100%' height=\"100px\" ></iframe></div>
	<div id=\"banner\">$adsense $menu</div>";
} else {
$xMenu->setID("navigator");

$menu	= '<div class="jPanelmenu"><nav style="display: none" id="navmenu">' . $xMenu->getAll() . '</nav>
		<input type="hidden" id="id-KeyEditable"/></div>';
	echo "<a href=\"#menu\" class=\"menu-trigger\"><i class=\"fa fa-reorder fa-3x\"></i></a>";
	echo "<div id='wprincipal'>
	<iframe id=\"idFPrincipal\" src=\"./$TasksPage\" width='100%' height=\"100%\" ></iframe>
	</div>
	<div id=\"banner\">$adsense $menu</div>";

}

$jxc ->drawJavaScript(false, true);
?>
<script>
var xG 		= new Gen();
var smenu	= 42;
var mmob	= <?php echo ($isMobile == false) ? 'false': 'true'; ?>;
$(document).ready(function(){
	if(mmob == false){
	    $("#jMenu").jMenu({
	      ulWidth : '200px',
	      effects : {
	        effectSpeedOpen : 200,
	        effectSpeedClose : 200,
	        effectTypeOpen : 'show',
	        effectTypeClose : 'hide',
	        effectOpen : 'slide',
	        effectClose : 'slide'
	      },
	      TimeBeforeOpening : 200,
	      TimeBeforeClosing : 20,
	      animatedText : true,
	      paddingLeft: 1,
	      openClick : true
	    });

	} else {
		smenu	= 8;
		$("#wprincipal").css("height", xG.alto());
		jsGetMenu();
	}
});
function setInFrame(sURI){
	if(typeof jPanelMenu != "undefined"){
		jPanelMenu.close();
	}
	if( $("#jMenu").length >0){
		$("#jMenu").trigger('mouseout');
	}	
	xG.QFrame({ url : sURI, id : 'idFPrincipal' });
}
function jsGetMenuChilds(id){
	var mParent	= $("#" + id).attr("data-key");
	$("#id-KeyEditable").val(mParent);
	jsaGetMenu();
	jPanelMenu.off();
	setTimeout("jsGetMenu(true)", 500);
}
function jsGetMenu(tr){
	if(typeof tr == "undefined"){ tr = false; }
	jPanelMenu = $.jPanelMenu({
		menu: '#navmenu',
		animated: false
	});
	jPanelMenu.on();
	if(tr == true){jPanelMenu.trigger(tr);}
}
function jsGetParent(parentID){	window.location = "./index.xul.php?m=" + parentID;}
function getNewTiny(mFile){
	if(mFile){
		var xG	= new Gen();
		mFile	= mFile + "?";
		xG.w({url: mFile, tiny : true});
	}
}

function getNewWindow(mFile){
	if(mFile){
		var xG	= new Gen();
		xG.w({url: mFile});
	}
	<?php
		if(MODO_DEBUG == false){
			echo "if( window.console ) { window.console.log( '' ) }";
		}
	?>


}

function jsGetMoneyChanges(){
	jsaGetMoneyChanges();
	//Mostrar el Popup
	setTimeout("jsGetMoneyInBox()", 15000);
}
function jsGetMoneyInBox(){
	//setTimeout("jsGetMoneyChanges()", 15000);
}
function jsEndSession(){}
function jsInitComponents(){
	jsGetMoneyChanges();
	var mAlto	= xG.alto()-smenu - 5;
	$("#idFPrincipal").attr("height", mAlto);
	if($('#id-KeyEditable').length >0){
		//setTimeout("jsGetMenu()", 500);
	}
}

</script>
<?php 
$xHP->fin();
?>