<?php
/**
 * Core de Operaciones
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package operaciones
 *  Core Operaciones File
 * 		16/05/2008
 * 		Se agrego el Numero de recibo
 * 		9/oct/2010.- Se Mejora la Ficha
 */

include_once("core.deprecated.inc.php");
include_once("entidad.datos.php");
include_once("core.config.inc.php");
include_once("core.creditos.inc.php");
include_once("core.captacion.inc.php");
include_once("core.fechas.inc.php");
@include_once("../libs/sql.inc.php");

//=====================================================================================================
class cUtileriasParaOperaciones{
	function __construct(){
		
	}
	function setGenerarRecibosGlobales(){
		//funcion que genera un recibo por dia y asigna operaciones huerfanas al dicho recibo
		$sql	= "SELECT COUNT(idoperaciones_mvtos) AS 'mvtos',
					fecha_operacion
					FROM operaciones_mvtos 
					WHERE (SELECT COUNT(idoperaciones_recibos) FROM operaciones_recibos 
							WHERE idoperaciones_recibos = operaciones_mvtos.recibo_afectado) = 0
					GROUP BY fecha_operacion";
		$rs		= getRecordset($sql);
		$msg	= "UTILERIA DE CORRECION DE MVTOS HUERFANOS\r\n";
		
		while( $rw = mysql_fetch_array($rs) ) {
			
			//generar el recibo
			$fecha_operacion	= $rw["fecha_operacion"];
			$mvtos				= $rw["mvtos"];
			$cadena				= "Documento de Ajuste por $mvtos Movimientos Huerfanos";
			$recibo 			= setNuevoRecibo(DEFAULT_SOCIO, DEFAULT_CREDITO, $fecha_operacion, 1, 10, $cadena, DEFAULT_CHEQUE, DEFAULT_TIPO_PAGO, DEFAULT_RECIBO_FISCAL, DEFAULT_GRUPO);
			$sqlU				= "UPDATE operaciones_mvtos SET recibo_afectado=$recibo WHERE 
																			(SELECT COUNT(idoperaciones_recibos) FROM operaciones_recibos 
																			WHERE idoperaciones_recibos = operaciones_mvtos.recibo_afectado) = 0 
									AND fecha_operacion='$fecha_operacion' ";
			//actualizar el mvto
			$xT					= my_query($sqlU);
			if( $xT["stat"] == true ){
				$msg			.= "$fecha_operacion\t$recibo\t$mvtos Movimentos Actualizados\r\n";
			} else {
				$msg			.= "$fecha_operacion\t$recibo\tERROR EN LA ACTUALIZACION\r\n";
			}
			
		}
		return 	$msg;		
	}
	function setPurgarMovimientos(){
		$msg	= "";
				//Valorar Recibos y Movimientos
		/*	$uM						= "UPDATE operaciones_mvtos SET tipo_operacion = 99 WHERE ( SELECT COUNT(idoperaciones_tipos) 
										FROM operaciones_tipos WHERE operaciones_tipos.idoperaciones_tipos = operaciones_mvtos.tipo_operacion ) = 0 ";
			$xt						= my_query( $uM );
			$msg					.= $xt["info"];
			
			$uO						= " UPDATE operaciones_recibos SET tipo_docto = 99 WHERE ( SELECT COUNT(idoperaciones_recibostipo) 
											FROM operaciones_recibostipo WHERE operaciones_recibostipo.idoperaciones_recibostipo = operaciones_recibos.tipo_docto ) = 0 ";
			$xE						= my_query( $uO );
			$msg					.= $xE["info"];
			//Eliminar recibos y movimientos sin documentos y sin socios
			$sqlDR					= "DELETE FROM operaciones_mvtos WHERE ( SELECT COUNT(numero_solicitud) FROM creditos_solicitud WHERE numero_solicitud = operaciones_mvtos.docto_afectado) = 0
										AND ( SELECT COUNT(numero_cuenta) FROM captacion_cuentas WHERE numero_cuenta = operaciones_mvtos.docto_afectado) = 0 ";
			$xES					= my_query( $sqlDR );
			$msg					.= $xES["info"];
			
			$sqlDRM					= "DELETE FROM operaciones_mvtos WHERE ( SELECT COUNT(codigo) FROM socios_general WHERE codigo = operaciones_mvtos.socio_afectado) = 0 ";
			$xER					= my_query( $sqlDRM );
			$msg					.= $xER["info"];
			//
			$sqlAM					= "DELETE FROM operaciones_recibos WHERE ( SELECT COUNT(numero_solicitud) FROM creditos_solicitud WHERE numero_solicitud = operaciones_recibos.docto_afectado) = 0
										AND ( SELECT COUNT(numero_cuenta) FROM captacion_cuentas WHERE numero_cuenta = operaciones_recibos.docto_afectado) = 0 ";
			$xEt					= my_query( $sqlAM );
			$msg					.= $xEt["info"];
			
			$sqlAO					= "DELETE FROM operaciones_recibos WHERE ( SELECT COUNT(codigo) FROM socios_general WHERE codigo = operaciones_recibos.numero_socio) = 0 ";
			$xEP					= my_query( $sqlAO );
			$msg					.= $xEP["info"];*/
			//Corrige las Sumas de los recibos
			$sqlDerma				= "SELECT
											`operaciones_recibos`.`idoperaciones_recibos`,
											`operaciones_recibos`.`tipo_docto`,
											`operaciones_recibos`.`total_operacion`,
											SUM(`operaciones_mvtos`.`afectacion_real`)       AS `total`,
											COUNT(`operaciones_mvtos`.`idoperaciones_mvtos`) AS `operaciones` 
										FROM
											`operaciones_mvtos` `operaciones_mvtos` 
												INNER JOIN `operaciones_recibos` `operaciones_recibos` 
												ON `operaciones_mvtos`.`recibo_afectado` = `operaciones_recibos`.
												`idoperaciones_recibos` 
											WHERE ( `operaciones_recibos`.`tipo_docto` != 2 
											AND `operaciones_recibos`.`tipo_docto` != 11
											AND `operaciones_recibos`.`tipo_docto` != 7 
											AND `operaciones_recibos`.`tipo_docto` != 6 )
											
											GROUP BY
												`operaciones_mvtos`.`recibo_afectado`
											
										HAVING `operaciones_recibos`.`total_operacion` != total		
											ORDER BY
												`operaciones_recibos`.`tipo_docto`,
												`operaciones_recibos`.`idoperaciones_recibos`
										";
			$rsMx				= mysql_query($sqlDerma, cnnGeneral() );
			$msg			.= "============ CORRIGIENDO RECIBOS DESCUADRADOS \r\n";	
			$msg			.= "ID\tRECIBO\tTIPO\tORIGINAL\tCALCULADO\tMOVIMIENTOS\r\n";
			$iContar		= 0;		
			while ($rw	= mysql_fetch_array($rsMx) ){
				//corregir
				$recibo			= $rw["idoperaciones_recibos"];
				$TotalRecibo	= $rw["total_operacion"];
				$TotalMvtos		= $rw["total"];
				$Tipo			= $rw["tipo_docto"];
				$NumsMvtos		= $rw["operaciones"];
				$msg			.= "$iContar\t$recibo\t$Tipo\t$TotalRecibo\t$TotalMvtos\t$NumsMvtos\r\n";
				$sqlDefUp		= "UPDATE operaciones_recibos SET total_operacion = $TotalMvtos WHERE idoperaciones_recibos = $recibo ";
				my_query($sqlDefUp);
				$iContar++;
			}
		return $msg;		
	}
	function setEliminarRecibosDuplicados(){
		$msg = "============= PURGANDO FOLIOS DUPLICADOS AL " . date("Y-m-d") . "\r\n";
		
		$sql = "SELECT idoperaciones_recibos,
				COUNT(idoperaciones_recibos) AS 'repetidos' FROM operaciones_recibos
				GROUP BY idoperaciones_recibos
				HAVING repetidos>1 ";
		$rs = mysql_query($sql, cnnGeneral());
		while($rw = mysql_fetch_array($rs)){
			$SQLoD	= "SELECT idoperaciones_recibos, numero_socio, fecha_operacion FROM operaciones_recibos
					WHERE idoperaciones_recibos =  " . $rw["idoperaciones_recibos"] . "
					ORDER BY fecha_operacion ASC
					LIMIT 0,1";
			$DFol	= obten_filas($SQLoD);
			$xRec	= new cReciboDeOperacion(false, false, $DFol["idoperaciones_recibos"]);
			$msg 	.= $xRec->setCambiarCodigo();
			unset($DFol);
		}
		return $msg;
	}	
}
?>