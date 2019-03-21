<?php
/**
 * @see Core de Impuestos
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package creditos
 *  Core Tax File
 * 		16/05/2008
 */

include_once("core.deprecated.inc.php");
include_once("entidad.datos.php");
include_once("core.config.inc.php");
include_once("core.common.inc.php");
include_once("core.html.inc.php");
include_once("core.fechas.inc.php");
@include_once("../libs/sql.inc.php");

//=====================================================================================================
function getTaxIDE(){

}
class cTax{
	function __construct(){

	}
}
class cDeclaracionIDE {
	function __construct(){

	}

}
class cImpuestoIDE{
		protected 	$mIDEPendiente		= 0;
		protected	$mBaseGravadaIDE	= 0;
		protected	$mIDECalculado		= 0;
		protected	$mMessages			= 0;

		function __construct(){

		}
		function getIDEPagado($socio, $fecha = false){
		if ( $fecha == false ){
			$fecha 		= fechasys();
		}
		$xF				= new cFecha(0, $fecha);

		$dia_inicial	= $xF->getDiaInicial();
		$dia_final		= $xF->getDiaFinal();
		$mvto_ide		= 235;
		$idePagado		= 0;

		if ( !isset($this->mIDEPagado) OR $this->mIDEPagado <= 0){

			$sqlIDE = "SELECT
						`operaciones_mvtos`.`tipo_operacion`,
						`operaciones_mvtos`.`socio_afectado`,
						COUNT(`operaciones_mvtos`.`idoperaciones_mvtos`) AS 'numero',
						SUM(`operaciones_mvtos`.`afectacion_real`) AS 'monto',
						`operaciones_mvtos`.`fecha_operacion`
					FROM
						`operaciones_mvtos` `operaciones_mvtos`
					WHERE
						(`operaciones_mvtos`.`tipo_operacion` = $mvto_ide)
						AND
						(`operaciones_mvtos`.`socio_afectado` =" . $socio . ")
						AND
						(`operaciones_mvtos`.`fecha_operacion` >='$dia_inicial')
						AND
						(`operaciones_mvtos`.`fecha_operacion` <='$dia_final')
					GROUP BY
						`operaciones_mvtos`.`tipo_operacion`,
						`operaciones_mvtos`.`socio_afectado`";
			$MD			= obten_filas($sqlIDE);
			$idePagado	= $MD["monto"];
			if ( !isset($idePagado) ){
				$idePagado = 0;
			}
			$this->mIDEPagado = $idePagado;
			unset($MD);
		}
		return $this->mIDEPagado;

	}
	
	function getIDExPagar($socio, $fecha = false, $monto = 0){
		if ( $fecha == false ){
			$fecha = fechasys();
		}
		//$xF		= new cFecha(0, $fecha);
		
		$mes 	= date("m", strtotime($fecha));
		$anno	= date("Y", strtotime($fecha));

		$dia_inicial	= date("Y-m-", strtotime($fecha) ) . "01";
		$dia_final		= date("Y-m-t", strtotime($fecha) );



		$sqlGravados	= "SELECT
								SUM(`operaciones_mvtos`.`afectacion_real`) AS 'monto'
							FROM
								`operaciones_mvtos` `operaciones_mvtos`
									INNER JOIN `operaciones_recibos` `operaciones_recibos`
									ON `operaciones_mvtos`.`recibo_afectado` = `operaciones_recibos`.
									`idoperaciones_recibos`
										INNER JOIN `eacp_config_bases_de_integracion_miembros`
										`eacp_config_bases_de_integracion_miembros`
										ON `operaciones_mvtos`.`tipo_operacion` =
										`eacp_config_bases_de_integracion_miembros`.`miembro`
						WHERE
							(`operaciones_mvtos`.`fecha_afectacion` >='$dia_inicial')
							AND
							(`operaciones_mvtos`.`fecha_afectacion` <='$dia_final')
							AND
							(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 2600)
							AND
							(`operaciones_mvtos`.`socio_afectado` = " . $socio ." )
							AND
							(`operaciones_recibos`.`tipo_pago` = 'efectivo' )
						GROUP BY
							`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
							`operaciones_mvtos`.`socio_afectado`,
							`operaciones_recibos`.`tipo_pago` ";

			$DGrav					= obten_filas($sqlGravados);
			/**
			 * Obtiene la formula IDE
			 */
			$base_gravada			= 0;
			$base_de_calculo		= $DGrav["monto"] + $monto;

			 $tasa_ide				= TASA_IDE;
			 $excencion				= EXCENCION_IDE;

			 $ide					= 0;
			 $cFormulaIDE			= new cFormula("formula_ide");



			 $ide_pagado 			= $this->getIDEPagado($socio, $fecha);

			 $this->mIDEPagado	= $ide_pagado;

			 if ( $base_de_calculo > EXCENCION_IDE ){
			 	eval( $cFormulaIDE->getFormula() );
			 	$this->mIDECalculado		= $ide;
			 	$this->mBaseGravadaIDE	= $base_gravada;
			 } else {
			 	$this->mMessages 		.= "NO_IDE\tA la Fecha $fecha, no hay IDE por que el Monto Exento(" . EXCENCION_IDE . ") es Mayor a la Base de Calculo $base_de_calculo \r\n";
			 }
			 /**
			  * Disminuir el IDE Retenido
			  */
			 $this->mIDEPendiente	= ($ide - $ide_pagado);

			 $this->mMessages 		.= "INF_IDE\tEl IDE es de $ide, IDE Pagado de $ide_pagado, IDE por pagar " . $this->mIDEPendiente . ", Base de Calculo $base_de_calculo\r\n";
			 return $this->mIDEPendiente;
	}
		/**
	 * Retorna la Base de IDE en una fecha Dada, generado por la Funcion getIDExPagarByPeriodo
	 * @param $fecha
	 * @return float	Base Gravada del IDE
	 */
	function getBaseGravada($fecha = false){
		if ($this->mIDEAsSet == false){
			$this->getIDExPagar($fecha);
		}
		return $this->mBaseGravadaIDE;
	}
	function getIDECalculado($fecha = false){
			if ($this->mIDEAsSet == false){
			$this->getIDExPagar($fecha);
		}
		return $this->mIDECalculado;
	}
	function getIDENoPagado($socio, $fecha = false){
		if ( $fecha == false ){
			$fecha = fechasys();
		}

		$dia_inicial	= date("Y-m-", strtotime($fecha) ). "01";
		$dia_final		= date("Y-m-", strtotime($fecha) ). date("t", strtotime($fecha) );
		$sql	= "SELECT
						`operaciones_mvtos`.`tipo_operacion`,
						`operaciones_mvtos`.`socio_afectado`,
						COUNT(`operaciones_mvtos`.`idoperaciones_mvtos`) AS 'numero',
						SUM(`operaciones_mvtos`.`afectacion_real`) AS 'monto'
			FROM
				`operaciones_mvtos` `operaciones_mvtos`
			WHERE
				(`operaciones_mvtos`.`tipo_operacion` =236)
				AND
				(`operaciones_mvtos`.`socio_afectado` =" . $socio . ")
				AND
				(
					(`operaciones_mvtos`.`fecha_operacion` >='$fecha_inicial')
						AND
					(`operaciones_mvtos`.`fecha_operacion` <='$fecha_final')
				)
			GROUP BY
				`operaciones_mvtos`.`tipo_operacion`,
				`operaciones_mvtos`.`socio_afectado`";
			$MD			= obten_filas($sql);
			$idePerAnts	= setNoMenorQueCero($MD["monto"]);
		return $idePerAnts;
	}
}
class cUtilsTaxs {
	function __construct(){

	}
	function setGenerarIDExPagar($fecha){
		$fecha_inicial	= date( "Y-m-", strtotime($fecha))  . "01";
		$fecha_final	= date( "Y-m-t", strtotime($fecha));
		$sql = "SELECT
						`operaciones_mvtos`.`socio_afectado`,
						`operaciones_recibos`.`tipo_pago`,
						`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
						SUM(`operaciones_mvtos`.`afectacion_real`) AS 'monto'
					FROM
						`operaciones_mvtos` `operaciones_mvtos`
							INNER JOIN `operaciones_recibos` `operaciones_recibos`
							ON `operaciones_mvtos`.`recibo_afectado` = `operaciones_recibos`.
							`idoperaciones_recibos`
								INNER JOIN `eacp_config_bases_de_integracion_miembros`
								`eacp_config_bases_de_integracion_miembros`
								ON `operaciones_mvtos`.`tipo_operacion` =
								`eacp_config_bases_de_integracion_miembros`.`miembro`
				WHERE
					(`operaciones_mvtos`.`fecha_afectacion` >='$fecha_inicial')
					AND
					(`operaciones_mvtos`.`fecha_afectacion` <='$fecha_final')
					AND
					(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 2600)
					AND
					(`operaciones_recibos`.`tipo_pago` = 'efectivo' )
					$BySucursal
					GROUP BY
					`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
					`operaciones_mvtos`.`socio_afectado`,
					`operaciones_recibos`.`tipo_pago`";

			$rsIDE	= mysql_query($sql, cnnGeneral());
			while($rwIDE = mysql_fetch_array($rsIDE)){
				$socio			= $rwIDE["socio_afectado"];
				$monto			= $rwIDE["monto"];
				if ($monto > EXCENCION_IDE ){
					$cSoc			= new cSocio($socio, true);
					$nombre			= $cSoc->getNombreCompleto();
					$DSoc			= $cSoc->getDatosInArray();
					$rfc			= $DSoc["rfc"];

					$ide_pagado		= $cSoc->getIDEPagadoByPeriodo($fecha_final);
					$ide_pendiente	= $cSoc->getIDExPagarByPeriodo($fecha_final);
					$base_gravada	= $cSoc->getBaseGravadaIDE();

				}
			}
	}
}

?>