<?php
/**
 * Reporte de
 *
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package seguimiento
 * @subpackage reports
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
$xHP		= new cHPage("TR.REPORTE DE ", HP_FORM);
$mql		= new cSQLListas();
$xF			= new cFecha();
$query		= new MQL();
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->init();
//$xForm	= new cFormato($forma);
//echo $xForm->get();
// establecer una conexión básica
$xFTP			= new cDocumentos();

$contents 		= $xFTP->FTPListFiles();
//$xFTP->FTPMakeDir("PRUEBAS");

$xFRM			= new cHForm("frm", "./");
$xBTN			= new cHButton();
$xDiv			= new cHDiv("tx12");

$msg			= "";
$fils			= 0;

foreach ($contents as $archivos){
	$xFil		= new cDocumentos($archivos);
	$icon		= "desconocido";
	if ($xFil->isDocto() == true){ 	$icon	= "documento";	}
	if ($xFil->isImagen() == true){ $icon	= "imagen";	}
	//$xFRM->OButton($archivos, "");
	if($icon != "desconocido"){
		$xFRM->addDivSolo( $xFil->getEmbed($archivos), $xBTN->getBasic("", "setFile('$archivos')", $icon, "id$fils"), "tx34", "tx14" );
		//$xFRM->addHElem(  );
	}
	$fils++;
}

$xFRM->addCerrar();
echo $xFRM->get();
?>
<script>
function setFile(mfil){
	if(opener){
		<?php echo "opener.$jscallback(mfil); window.close();";?>
	}
}
</script>
<?php
echo $xHP->fin();
?>