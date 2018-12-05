<?php
/**
 * Core Captacion File
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package seguimiento
 *  Core Captacion File
 * 		10/04/2008 Iniciar Funcion de Notificaciones 360
 * 		29/04/2008 Termino de efectuar lamadas No 360
 */
include_once("core.deprecated.inc.php");
include_once("entidad.datos.php");
include_once("core.config.inc.php");
include_once("core.fechas.inc.php");
//include_once("../libs/sql.inc.php");
include_once("core.riesgo.inc.php");
include_once("core.common.inc.php");
include_once("core.seguimiento.inc.php");
include_once("core.html.inc.php");


class cUtileriasParaSeguimiento {
	private $mLimitRecords		= 20;
	private $mMessages			= "";
	
	function __construct(){	}

	function setCancelarLlamadasAnteriores($fecha = false){
		$xF		= new cFecha();
		$xQL	= new MQL();
		$fecha	= $xF->getFechaISO($fecha);
		$fecha	= $xF->setRestarDias(DIAS_DE_INTERVALO_POR_LLAMADAS, $fecha);
		//cancelar llamadas Anteriores
		$sqlCA 		= "UPDATE seguimiento_llamadas SET estatus_llamada='"  . SEGUIMIENTO_ESTADO_CANCELADO . "' 	WHERE	(estatus_llamada='" . SEGUIMIENTO_ESTADO_PENDIENTE . "')	AND (fecha_llamada < '$fecha') ";
		return $xQL->setRawQuery($sqlCA);
	}
	
	function setGenNotificacionCobroPrev(){
		//$fecha = false
		$xVis	= new cSQLVistas();
		$xQL	= new MQL();
		$xF		= new cFecha();
		$fechaEn= $xF->setSumarDias(1);
		$fecha	= $xF->setSumarDias(DIAS_DE_ANTICIPACION_PARA_LLAMADAS, $fechaEn);
		$canal	= SEG_CANAL_NOTIF_CBZA_PREV;
		$hora	= "09:00:00";
		$fmt	= 0;
		$sql	= $xVis->getVistaLetrasConNombre($fecha);
		$rs		= $xQL->getRecordset($sql);
		
		$xNot	= new cSeguimientoNotificaciones();
		if($rs){
			while($rw = $rs->fetch_assoc()){
				/*					`socios`.`codigo`,
					`socios`.`nombre`,
					`letras`.`docto_afectado` AS ``,
					`letras`.`periodo_socio`  AS `parcialidad`,
					`letras`.`fecha_de_pago`,
					`letras`.`capital`,
					`letras`.`interes`,
					`letras`.`iva`,
					`letras`.`ahorro`,
					`letras`.`otros`,
					`letras`.`letra` */
				$persona	= $rw["codigo"];
				$credito	= $rw["credito"];
				$total		= $rw["letra"];
				$xNot->add($credito, $fechaEn, $hora, $total, "", false, SEG_CANAL_NOTIF_CBZA_PREV, $xNot->FMT_SMS_PREV);
			}
		}
	}

}


?>
