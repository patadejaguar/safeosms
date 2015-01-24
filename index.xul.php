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
	$iduser = $_SESSION["log_id"];
//=====================================================================================================

$xHP						= new cHPage(EACP_NAME . "{" . getSucursal() . "} - S.A.F.E. V " . $version. "", HP_FORM, "", ".");
$fecha_de_sesion			= parametro("f", fechasys());
$MenuParent					= parametro("m", 0, MQL_INT);
$isMobile 					= $xHP->isMobile();
$_SESSION[SYS_CLIENT_MOB]	= $isMobile;


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
	
	$xPerCred		= new cPeriodoDeCredito();
	if($xPerCred->checkPeriodoVigente($fecha_de_sesion) == false ){
		setLog($xPerCred->getMessages(), 300);
		header("location:frmcreditos/cambiarperiodo.frm.php?a=1");
	}
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
		display:block; text-shadow:0 -1px 1px #222;line-height:1.4em;color:#f7f7f7; width:2em;
	}
	#content {  }
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
	echo "<div id='content'><iframe id=\"idFPrincipal\" src=\"./utils/frm_calendar_tasks.php\" width='100%' height=\"1000px\" ></iframe></div>
	<div id=\"banner\">$adsense $menu</div>";
} else {
$xMenu->setID("navigator");

$menu	= '<div class="jPanelmenu"><nav style="display: none" id="navmenu">' . $xMenu->getAll() . '</nav>
		<input type="hidden" id="id-KeyEditable"/></div>';
	echo "<a href=\"#menu\" class=\"menu-trigger\"><i class=\"fa fa-reorder fa-2x\"></i></a>";
	echo "<div id='content'>
	<iframe id=\"idFPrincipal\" src=\"./utils/frm_calendar_tasks.php\" width='100%' height=\"100%\" ></iframe>
	</div>
	<div id=\"banner\">$adsense $menu</div>";

}

$jxc ->drawJavaScript(false, true);
?>
<script>
var xG 	= new Gen();
<?php
if( $isMobile == false){
	echo "
	$(document).ready(function(){
	    $(\"#jMenu\").jMenu({
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
	  });
	";	
} else {
echo "
	$(document).ready(function(){
		$(\"#content\").css(\"height\", xG.alto());
	  });
	";	
}
if(MODO_DEBUG == true){
	//echo "var xG = new Gen(); window.setInterval(xG.getLog, 2000); ";
}
?>
function setInFrame(sURI){	xG.QFrame({ url : sURI, id : 'idFPrincipal' }); }
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
	if(tr == true){
		jPanelMenu.trigger(tr);
	}
}
function jsGetParent(parentID){
	window.location = "./index.xul.php?m=" + parentID;
}

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
		if($isMobile == false ){
			//echo "setTimeout(function(){\$.jMenu._closeAll();},opts.TimeBeforeClosing);";
		}	
		if(MODO_DEBUG != true){
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
	if($('#id-KeyEditable').length >0){
		setTimeout("jsGetMenu()", 1000);
	}
}
</script>
<?php 
$xHP->end();
?>