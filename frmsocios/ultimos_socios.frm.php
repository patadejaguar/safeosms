<?php
/**
 * Archivo que muestra los ultimos socios
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package socios
 * 
 * 		-
 *		-
 */
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
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");
include_once("../core/core.creditos.inc.php");
include_once("../core/core.captacion.inc.php");
include_once("../core/core.common.inc.php");
include_once("../core/core.html.inc.php");
include_once("../core/core.operaciones.inc.php");

//require_once("." . TINYAJAX_PATH . "/TinyAjax.php");

$oficial = elusuario($iduser);
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

	$i 			= $_GET["i"];		//CLAVE
	$f 			= $_GET["f"];		//form dependiente
	$OtherEvent	= "";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Ultimos Socios Registrados por Caja Local</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<?php
//$jsb	= new jsBasicForm("", iDE_CAPTACION);
//$jsb->show();
//$jxc ->drawJavaScript(false, true);
?>
<body  onload='initComponents();'>
	<?php
	$sqlT	= "SELECT
					`socios`.`codigo`,
					`socios`.`nombre` 
				FROM
					`socios` `socios`
				WHERE
					(`socios`.`numero_caja_local` = $i ) 
				ORDER BY
					`socios`.`codigo` DESC
				LIMIT 0,20 ";
			$cTbl = new cTabla($sqlT);
			$cTbl->setEventKey("setSocio");
			$cTbl->setRowCSS("codigo", "center");
			$cTbl->Show("SOCIOS QUE COINCIDEN CON LA BUSQUEDA", false);
	?>

</body>
<script  >

function setSocio(id){
<?php
	if( isset($f) ){
		echo "
		opener.document.$f.idsocio.value = id;
		opener.document.$f.idsocio.focus();
		$OtherEvent
		window.close();
		";
	}
?>
}
function initComponents(){
	resizeMainWindow();
}
function resizeMainWindow(){
	var mWidth	= 800;
	var mHeight	= 600;
	window.resizeTo(mWidth, mHeight);	
}
</script>
</html>
