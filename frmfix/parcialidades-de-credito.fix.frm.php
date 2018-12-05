<?php
/**
 * Corrige el monto de la parcialidad por credito
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

$sql		= "SELECT * FROM creditos_solicitud";

/*$sql		= "SELECT   `creditos_plan_de_pagos`.`clave_de_credito`,
         COUNT( `creditos_plan_de_pagos`.`plan_de_pago` )  AS `pagos`,
         MIN( `creditos_plan_de_pagos`.`fecha_de_pago` )  AS `fecha_inicial`,
         `creditos_solicitud`.`monto_autorizado`,
         SUM( `creditos_plan_de_pagos`.`capital` )  AS `capital`,
         SUM( `creditos_plan_de_pagos`.`interes` )  AS `interes`,
         
         SUM( `creditos_plan_de_pagos`.`otros` )  AS `otros`,
         SUM( `creditos_plan_de_pagos`.`ahorro` )  AS `ahorro`,
         SUM( `creditos_plan_de_pagos`.`penas` )  AS `penas`,
         SUM( `creditos_plan_de_pagos`.`gtoscbza` )  AS `gtoscbza`,
         SUM( `creditos_plan_de_pagos`.`mora` )  AS `mora`,
         SUM( `creditos_plan_de_pagos`.`descuentos` )  AS `descuentos`,
         SUM( `creditos_plan_de_pagos`.`iva_castigos` )  AS `ivas_castigos`,
         SUM( `creditos_plan_de_pagos`.`total_base` )  AS `total_base`,
         SUM( `creditos_plan_de_pagos`.`total_c_otros` )  AS `total_c_cargos`,
         SUM( `creditos_plan_de_pagos`.`total_c_castigos` )  AS `total_c_castigos`,
         SUM( `creditos_plan_de_pagos`.`impuesto` )  AS `impuesto`
FROM     `creditos_plan_de_pagos` 
INNER JOIN `creditos_solicitud`  ON `creditos_plan_de_pagos`.`clave_de_credito` = `creditos_solicitud`.`numero_solicitud` 
WHERE    ( `creditos_plan_de_pagos`.`estatusactivo` = 1 ) AND `creditos_solicitud`.`monto_autorizado` >0 
GROUP BY clave_de_credito
HAVING capital != monto_autorizado";*/

$rs			= $xQL->getRecordset($sql);
$xT			= new cCreditos_solicitud();
$errors		= 0;
$exitos		= 0;
while ($rw = $rs->fetch_assoc()){
	$idcredito	= $rw["clave_de_credito"];//$rw[$xT->NUMERO_SOLICITUD];
	$xCred		= new cCredito($idcredito);
	
	if($xCred->init() == true){
		$montoparcialidad				= $xQL->getDataValue("SELECT MAX(`letra`) AS `monto` FROM `tmp_creds_prox_letras` WHERE `docto_afectado`=$idcredito", "monto");
		$montoparcialidad				= setNoMenorQueCero($montoparcialidad,2);
		if($montoparcialidad<=0){
			$montoparcialidad			= $xQL->getDataValue("SELECT MAX(`total_base`) AS `monto` FROM `creditos_plan_de_pagos` WHERE `clave_de_credito`=$idcredito AND `estatusactivo`=1", "monto");
			$montoparcialidad			= setNoMenorQueCero($montoparcialidad,2);
		}
		if($montoparcialidad<=0){
			if($xCred->getMontoAutorizado()>0){
				$xEm					= new cPlanDePagosGenerador();
				$xEm->initPorCredito($idcredito, $xCred->getDatosInArray());
				$xEm->setFechaArbitraria($xCred->getFechaDePrimerPago());
				$xEm->setMostrarCompleto(true);
				$montoparcialidad 		= $xEm->getParcialidadPresumida($xCred->getFactorRedondeo());
				
			}
			//$xEm->setCompilar();
			//$xEm->getVersionFinal(false);
			
		}
		$arr[$xT->MONTO_PARCIALIDAD]	= $montoparcialidad;
		
		$xCred->setUpdate($arr);
		//$xLog->add("UPDATE `creditos_solicitud` SET `monto_parcialidad`=$montoparcialidad WHERE `numero_solicitud`=$idcredito;\r\n");
		
		if($montoparcialidad<=0){
			$errors++;
			$xFRM->addAviso($idcredito);
		} else {
			$exitos++;
		}
	}
}
$xFRM->addAvisoRegistroError("Errores : $errors");
$xFRM->addAvisoRegistroOK("Bien : $exitos");
$xFRM->addLog($xLog->getMessages());



echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>