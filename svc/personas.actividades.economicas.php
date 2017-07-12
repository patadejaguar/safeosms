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
	$iduser = $_SESSION["log_id"];
//=====================================================================================================

//$xH			= new cHObject(HP_SERVICE);
$xInit          = new cHPage(HP_SERVICE );
//header("Content-type: text/xml");
header('Content-type: application/json');
$txt		    = "";
$action		    = "";
$DDATA          = $_REQUEST;
$action         = isset($DDATA["action"]) ? $DDATA["action"] : SVC_LIST;
$arg            = isset($DDATA["arg"]) ? $DDATA["arg"] : SYS_NINGUNO; //ID 
$arg2           = isset($DDATA["arg2"]) ? $DDATA["arg2"] : PERSONAS_ACTIVIDAD_ECONOMICA_CLASE;

//$relacionado    = isset($DDATA["relacionado"]) ? $DDATA["relacionado"] : SYS_NINGUNO; //ID

$lim            = isset($DDATA["lim"]) ? $DDATA["lim"] : 100;
$arg            = substr($arg, 0,20);
$where          = "";

$superior       = "";
$clase          = "";

switch($action){
    case SVC_LIST:
    $where      = " AND (
    			(`personas_actividad_economica_tipos`.`nombre_de_la_actividad` LIKE '%$arg%')
               /* OR
                (`personas_actividad_economica_tipos`.`descripcion_detallada` LIKE '%$arg%')
                
                (`personas_actividad_economica_tipos`.`productos` LIKE '%$arg%')*/
				) LIMIT 0,$lim";
    $clase      = $arg2;
            break;
    case SVC_GET:
        $where  = " AND ( `personas_actividad_economica_tipos`.`clave_de_actividad` = $arg ) LIMIT 0,1";
        break;
}
//GET PUT DELETE LIST
$xT			= new cSQLListas();
//$xT->setOperador("LIKE");
$sql        = $xT->getListadoDePersonasActividadesEconomicasTipos("", $clase, $superior, $where);
//echo $sql;
$xSVC       = new MQLService($action, $sql);
echo $xSVC->getJSON();
//$xLog       = new cSystemLog();
//$xLog->setRotate();
//$xLog->setSave($sql);
//$xT->clave_de_actividad()
//print json_encode($jTableResult);
/*
echo "<?xml version =\"1.0\" ?>\n<data>\n" . $txt . "</data>";
*/
?>