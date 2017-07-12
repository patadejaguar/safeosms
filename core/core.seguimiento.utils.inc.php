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

	function __construct(){	}

	function setCancelarLlamadasAnteriores($fecha = false){
		$xF		= new cFecha();
		$xQL	= new MQL();
		$fecha	= $xF->getFechaISO($fecha);
		$fecha	= $xF->setRestarDias(DIAS_DE_INTERVALO_POR_LLAMADAS, $fecha);
		//cancelar llamadas Anteriores
		$sqlCA 		= "UPDATE seguimiento_llamadas		SET estatus_llamada='"  . SEGUIMIENTO_ESTADO_CANCELADO . "' 	WHERE	(estatus_llamada='" . SEGUIMIENTO_ESTADO_PENDIENTE . "')	AND (fecha_llamada < '$fecha') ";
		return $xQL->setRawQuery($sqlCA);
	}


}


?>
