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
$cnt		= array();
$sql		= "";
$json 		= json_decode(file_get_contents("php://input"), true);
// Access your $json['this']
if(isset($json["table"])){
	$table		= $json["table"];
	if(isset($json["key"])){
		$key		= $json["key"];
		$vkey		= "";
		$action		= $json["action"];
		$target		= (isset($json["target"])) ? $json["target"] : "";
		foreach ($json as $record => $value){
			
			if(strpos($record, "record_") !== false){
				//$cnt[$record] = $value;
				$id		= str_replace("record_", "", $record);
				$str	= "";
				$fd		= "";
				$vl		= "";
				
				foreach ($value as $clave => $valor){
					if($clave == $key){
						$vkey		= $valor;
					}
					
					//$cnt[$id][$clave] = $valor;
					if($action == "translate"){
						if($clave == "translate"){
							if(trim($valor) == ""){
								
							} else {
								$str	.= " $target='$valor' ";
							}
						}
					} else {
						$str	.= " $clave='$valor', ";
					}
				}
				//$cnt[$id]["sql"] = ;
				if(trim($str) == ""){
					$cnt[]["result"] = "false";
					$cnt[]["message"] = "NO DATA UPDATE";
				} else {
					$sql	= "UPDATE $table SET $str WHERE $key='$vkey'";
					
					$exec	= my_query($sql);
					if($exec[SYS_ESTADO] == false){
						$cnt[]["result"] = "UPDATE";
						$cnt[]["message"] = "ERROR for Update $id";						
					} else {
						$cnt[]["result"] = "true";
						$cnt[]["message"] = "READY for Update $id";
					}
				}			
			}
		}
	}
}

//var_dump($json);

// then when you are done
header("Content-type: application/json");

print json_encode($cnt);

?>