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
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc 		= new TinyAjax();
//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);$action	= strtolower($action);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$observaciones	= parametro("idobservaciones"); $observaciones	= parametro("observaciones", $observaciones);

$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xLog		= new cCoreLog();

ini_set("max_execution_time", 600);

//$xQL->setRawQuery("CALL `proc_creditos_letras_del_dia`");
$xT			= new cGeneral_log();


$errors		= 0;
$exitos		= 0;

$rs			= $xQL->getDataRecord("SELECT * FROM general_log WHERE text_log LIKE '%Reversion del Recibo%' AND `idrecibo`<=0 ");
foreach ($rs as $rw){
	$xT->setData($rw);
	$clave	= $rw[$xT->IDGENERAL_LOG];
	$subtxt	= substr($rw[$xT->TEXT_LOG],0,200);
	$dd		= explode("-", $subtxt);
	$rec	= setNoMenorQueCero($dd[0]);
	$per	= setNoMenorQueCero($dd[1]);
	$cred	= setNoMenorQueCero($dd[2]);
	
	$txtP	= ($xT->idpersona()->v()>DEFAULT_SOCIO) ? "" : " `idpersona`=$per, ";
	$txtD	= ($xT->iddocumento()->v() > 0) ? "" : " `iddocumento`=$cred, ";
	$res	= $xQL->setRawQuery("UPDATE `general_log` SET $txtP $txtD `idrecibo`=$rec WHERE `idgeneral_log`=$clave");
	if($res !== false){
		$exitos++;
	}
	//$xFRM->addTag("CLAVE[$clave]-RECIBO[$rec]-PERSONA[$per]-CREDITO[$cred]");
	//$xFRM->addTag($subtxt);
	//Reversion del Recibo 16630] - Persona : 1000628 - Documento: 20000062801 
}


$rs			= $xQL->getDataRecord("SELECT * FROM general_log WHERE text_log LIKE '%REVERSION DE RECIBO[%' AND `idrecibo`<=0 ");
foreach ($rs as $rw){
	$xT->setData($rw);
	$clave	= $rw[$xT->IDGENERAL_LOG];
	$subtxt	= substr($rw[$xT->TEXT_LOG],0,200);
	$dd		= explode("]", $subtxt);
	$rec	= setNoMenorQueCero($dd[0]);

	$res	= $xQL->setRawQuery("UPDATE `general_log` SET `idrecibo`=$rec WHERE `idgeneral_log`=$clave");
	if($res !== false){
		$exitos++;
	}
}

$rs			= $xQL->getDataRecord("SELECT * FROM general_log WHERE text_log LIKE '%. Actualizando la fecha%' AND `idrecibo`<=0 ");
foreach ($rs as $rw){
	$xT->setData($rw);
	$clave	= $rw[$xT->IDGENERAL_LOG];
	$subtxt	= substr($rw[$xT->TEXT_LOG],0,100);
	$dd		= explode(".", $subtxt);
	$rec	= setNoMenorQueCero($dd[0]);
	
	$res	= $xQL->setRawQuery("UPDATE `general_log` SET `idrecibo`=$rec WHERE `idgeneral_log`=$clave");
	if($res !== false){
		$exitos++;
	}
}

$rs			= $xQL->getDataRecord("SELECT * FROM general_log WHERE `type_error`=1051 ");
foreach ($rs as $rw){
	$clave	= $rw[$xT->IDGENERAL_LOG];
	$xT->setData($rw);
	$subtxt	= substr($rw[$xT->TEXT_LOG],0,25);
	$cred	= setNoMenorQueCero($subtxt);
	
	$res	= $xQL->setRawQuery("UPDATE `general_log` SET `iddocumento`=$cred WHERE `idgeneral_log`=$clave");
	if($res !== false){
		$exitos++;
	}
	
}


$rs			= $xQL->getDataRecord("SELECT * FROM general_log WHERE `type_error`=102 ");
foreach ($rs as $rw){
	$clave	= $rw[$xT->IDGENERAL_LOG];
	$xT->setData($rw);
	$subtxt	= substr($rw[$xT->TEXT_LOG],0,100);
	$dd		= explode(":", $subtxt);
	$per	= setNoMenorQueCero($dd[0]);
	
	$res	= $xQL->setRawQuery("UPDATE `general_log` SET `idpersona`=$per WHERE `idgeneral_log`=$clave");
	if($res !== false){
		$exitos++;
	}
	
}


$rs			= $xQL->getDataRecord("SELECT * FROM general_log WHERE text_log LIKE '%Pago Cancelado%' AND `idpersona`<=0 ");
foreach ($rs as $rw){
	$xT->setData($rw);
	$clave	= $rw[$xT->IDGENERAL_LOG];
	$subtxt	= substr($rw[$xT->TEXT_LOG],0,300);
	$subtxt	= str_replace("Pago Cancelado Letra incompleta", "", $subtxt);
	$subtxt	= str_replace("Pago Cancelado Credito sin Saldo.", "", $subtxt);
	$subtxt	= str_replace("Pago Cancelado", "", $subtxt);
	$subtxt	= trim($subtxt);
	if(strpos($subtxt, ")") !== false){
		$dd		= explode(")", $subtxt);
		$subtxt	= trim($dd[1]);
	}
	
	$dd		= explode("|", trim($subtxt));
	$per	= setNoMenorQueCero($dd[0]);
	$cred	= setNoMenorQueCero($dd[1]);

	$res	= $xQL->setRawQuery("UPDATE `general_log` SET `idpersona`=$per,`iddocumento`=$cred WHERE `idgeneral_log`=$clave");
	if($res !== false){
		$exitos++;
	}

}

//actualizar la mora del credito
/*
$rs			= $xQL->getDataRecord("SELECT * FROM general_log WHERE text_log LIKE '%Reversion del Recibo%'");
foreach ($rs as $rw){
	$clave	= $rw[$xT->IDGENERAL_LOG];
	$xT->setData($rw);
} 
 * */

$xFRM->addAvisoRegistroError("Errores : $errors");
$xFRM->addAvisoRegistroOK("Bien : $exitos");
$xFRM->addLog($xLog->getMessages());



echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>