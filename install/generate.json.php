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
$xHP		= new cHPage("", HP_FORM);

$sql		= "SELECT
	`general_menu`.`idgeneral_menu`,
	/*`general_menu`.`menu_parent`,*/
	`general_menu`.`menu_title`,
	'' AS 'translate'
FROM
	`general_menu` `general_menu` 
WHERE
	(`general_menu`.`menu_title` !='') ";


//$OB				= new MQL();

//$rs			= $OB->getRecordset($sql);
//$sel			= $OB->
//$ob				= new MQLSelect($tabla, $datos, $primaryK);
//$men				= new cGeneral_menu();
header('Content-type: application/json');
$svc				= new MQLService("LIST", $sql);
echo $svc->getJSON();
//$q					= $men->query()->select();
//$q->exec("(`general_menu`.`menu_title` !='')");

?>