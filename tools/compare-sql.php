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
$xQL		= new MQL();
$msg		= "";
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->init();
if($action == MQL_LOAD){
	$doc1					= (isset($_FILES["idarchivo"])) ? $_FILES["idarchivo"] : false;
	$primerosql				= parametro("primerosql", false, MQL_BOOL);
	$sql					= parametro("idsql", "", MQL_RAW);
	$clave					= parametro("idclave", "", MQL_RAW);
	$clave2					= parametro("idclave2", "", MQL_RAW);
	
	$keyFile				= array();
	$keySQL					= array();
	
	$aKeyFile				= explode(",", $clave2);
	$aKeySql				= explode(",", $clave);
	
	$xFi					= new cFileImporter();
	$xT						= new cTipos();
	$delimiter				= "|";
	//var_dump($_FILES);
	$xFi->setCharDelimiter($delimiter);
	$xFi->setLimitCampos(16);
	$xFi->setToUTF8();
	
	if($primerosql == true){
		
		if($xFi->processFile($doc1) == true){
			$data				= $xFi->getData();
			$conteo				= 1;
			foreach ($data as $rows){
				$xFi->setDataRow($rows);
				$pkey			= "";
				//var_dump($rows);
				foreach ($aKeyFile as $ikey){
					$pkey		.= $xFi->getV($ikey);// $rows[$ikey];
				}
				$pkey = preg_replace("/[^a-zA-Z0-9]+/", "", $pkey);
				$keyFile[$pkey]		= $pkey;
				//$msg				.= "WARN\tAgregar ARCHIVO con ID $pkey\r\n";
				//echo "WARN\tAgregar ARCHIVO con ID $pkey\r\n";
			}
		} 
		
		//$msg	.= $xFi->getMessages();
		
		
		$rs		= $xQL->getDataRecord($sql);
		foreach ($rs as $rec){
			$pkey			= "";
			foreach ($aKeySql as $ikey){
				$pkey		.= $rec[$ikey];
			}
			$pkey = preg_replace("/[^a-zA-Z0-9]+/", "", $pkey);
			//$msg				.= "WARN\tAgregar DB con ID $pkey\r\n";
			if(isset($keyFile[$pkey])){
				//$msg			.= "OK\tEXISTE $pkey\r\n";
			} else {
				$rt				= "";
				//$msg			.= "ERROR\tNO EXISTE $pkey\r\n";
				foreach ($rec as $datos){
					$rt			.= $datos . $delimiter;
				}
				$msg			.= $rt . "\r\n";
			}
			//$keyFile[$pkey]		= $pkey;		
		}
	}
}
$xFRM		= new cHForm("frm", "compare-sql.php?action=" . MQL_LOAD);


$xFRM->OTextArea("idsql", "SELECT `listado_de_ingresos`.`clave_empresa`, `listado_de_ingresos`.`empresa`, `listado_de_ingresos`.`codigo`, `listado_de_ingresos`.`nombre`, DATE_FORMAT(`listado_de_ingresos`.`fecha`, '%d-%m-%Y') AS `fecha`, SUM(`listado_de_ingresos`.`capital`) AS `capital`, SUM(`listado_de_ingresos`.`interes_normal`) AS `interes`, SUM(`listado_de_ingresos`.`interes_moratorio`) AS `moratorios`, SUM(`listado_de_ingresos`.`iva`) AS `iva`, SUM(`listado_de_ingresos`.`otros`) AS `otros` , ROUND(SUM(`capital`+ `interes_normal`+`interes_moratorio`+`iva`+`otros`),2) AS 'total', MAX(`listado_de_ingresos`.`parcialidad`) AS `parcialidad`, MAX(`listado_de_ingresos`.`periocidad`) AS `periocidad`, MAX(`listado_de_ingresos`.`banco`) AS `banco` , `listado_de_ingresos`.`tipo_de_pago` FROM `listado_de_ingresos` `listado_de_ingresos` \n WHERE (`listado_de_ingresos`.`fecha` >='2014-01-01') AND (`listado_de_ingresos`.`fecha` <='2014-01-31') \n GROUP BY `listado_de_ingresos`.`clave_empresa`, `listado_de_ingresos`.`codigo`, `listado_de_ingresos`.`fecha`, `listado_de_ingresos`.`banco` ORDER BY `listado_de_ingresos`.`fecha`, `listado_de_ingresos`.`clave_empresa`, `listado_de_ingresos`.`periocidad`, `listado_de_ingresos`.`nombre` ", "TR.SQL");
$xFRM->OText("idclave", "codigo,fecha,total", "TR.clave");
$xFRM->OText("idclave2", "3,5,11", "TR.Columnas");
$xFRM->OFile("idarchivo", "", "TR.Archivo");
$xFRM->OCheck("TR.Comparar SQL -> Archivo", "primerosql");
$xFRM->addSubmit();
//$xFRM->addAviso($msg);
if(MODO_DEBUG == true){
	$xLog		= new cFileLog();
	$xLog->setWrite($msg);
	$xLog->setClose();
	$xFRM->addToolbar( $xLog->getLinkDownload("TR.Archivo del proceso", ""));
}
echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>