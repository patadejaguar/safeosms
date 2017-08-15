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
$xInit      = new cHPage("", HP_SERVICE );
$txt		= "";
$ql			= new MQL();
$lis		= new cSQLListas();
$xF			= new cFecha();

$tabla	= parametro("tabla", "", MQL_RAW);
$clave	= parametro("clave", "", MQL_RAW);
$query	= parametro("q", "", MQL_RAW);


$where	= parametro("w", "", MQL_RAW);
$out	= parametro("out", "", MQL_RAW);
$param	= parametro("vars", "", MQL_RAW);
$jTLim	= parametro("jtStartIndex", 0, MQL_INT);
$jTPag	= parametro("jtPageSize", 0, MQL_INT);

$err	= false;
$rs		= array();
$run	= true;

header('Content-type: application/json');
//exit(base64_encode("nombrecompleto LIKE '%pedro%' "));
if($query !== "" OR $where !== ""){
	if($tabla !== ""){							//tabla con where
		$xObj	= new cSQLTabla($tabla);
		if( $xObj->obj() == null){
			$err	= true;
		} else {
			$obj	= $xObj->obj();
			$sel	= $obj->query()->select();
			$sql	= $sel->get( base64_decode($where) );
			$svc	= new MQLService("list", $sql);
			echo $svc->getJSON($out);
			exit;
		}		
	} else {
		$sql	= base64_decode($query);
		if($where == ""){
			
		} else {
			$where	= base64_decode($where);
			//checar si tiene where
			if(strpos($where, "WHERE") === false){
				$where	= " WHERE " . $where;
			}
			$sql	= $sql . $where;
		}
		
		//$sql	.= ($where == "") ? "" : " " . base64_decode($where);
		
		
		//setLog($sql);
		
		if($param !== ""){
			$sql	= str_replace("?", $param, $sql);
		}
		
		$svc	= new MQLService("list", $sql);
		if($out == $svc->JTABLE){
			if($jTPag > 0){
				if(strpos($sql, "UNION") === false){ //Si no hay UNION
					if(strpos($sql, "LIMIT") !== false){
						$sql	= preg_replace("/LIMIT[\s][0-9],[\d][0-9]+/", "LIMIT $jTLim,$jTPag", $sql);
					} else {
						$sql 	= $sql . " LIMIT $jTLim,$jTPag";
					}
					$sql	= preg_replace("/SELECT[\s]/", "SELECT SQL_CALC_FOUND_ROWS ", $sql);
				}
				
				
				$svc->setSQL($sql);
			}
		}
		
		
		echo $svc->getJSON($out); exit;		
	}
}
if ($tabla !== "" AND $clave !== "" AND $run == true){
	$xObj	= new cSQLTabla($tabla);
	if( $xObj->obj() == null){
		$err	= true;
	} else {
		$obj	= $xObj->obj();
		//$obj	= new cSocios_general();
		$sel	= $obj->query()->select();
		$sql	= $sel->get( $obj->getKey() . " = '$clave' " );
		$svc	= new MQLService("list", $sql);
		//echo $sql;
		echo $svc->getJSON($out);
		exit;
	}
}

if($err == true){
	$rs[MSG_NO_PARAM_VALID]		= "ERROR\tEn la Tabla $tabla y clave $clave\r\n";
	$rs["error"]				= true;
}
echo json_encode($rs);
?>