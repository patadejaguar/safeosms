<?php
include_once("core.config.inc.php");
include_once("entidad.datos.php");

@include_once ("../libs/medoo.min.php");
@include_once ("../libs/aes.php");

class cCatalogoDeDatos {
	private $mClavePrimaria	= "";
	private $mValorEtiqueta	= "";
	private $mTabla			= "";
	private $mRecords		= array();
	function __construct($tabla){
		
	}
	function get($clave = null, $etiqueta = null, $tabla = null){
		$this->mRecords			= array();		//reset
		$this->mClavePrimaria	= ($clave == null) ? $this->mClavePrimaria : $clave;
		$this->mValorEtiqueta	= ($etiqueta == null) ? $this->mValorEtiqueta : $etiqueta;
		$this->mTabla			= ($tabla == null) ? $this->mTabla : $tabla;
		$ql		= new MQL();
		$data	= $ql->getDataRecord("SELECT * FROM " . $this->mTabla);
		foreach ($data as $rows){ $this->mRecords[$rows[$this->mClavePrimaria]]	= $rows[$this->mValorEtiqueta]; }
		$data	= null;
		return $this->mRecords;
	}
	function initPorTabla($tabla){
		$res	= array();
		$xTa	= new cSQLTabla($tabla);
		if($xTa->obj() != null){
			$this->mValorEtiqueta 	= $xTa->getCampoDescripcion();
			$this->mClavePrimaria	= $xTa->getClaveUnica();
			$this->mTabla			= $tabla;
			
			if($this->mValorEtiqueta != ""){  
				$res				= $this->get($this->mClavePrimaria, $this->mValorEtiqueta, $this->mTabla);
			}
		}
		return $res;
	}
}

class cSQLListas {
	protected $mTitle			= "";
	private $mOperador			= "=";
	private $mFechaFunction		= "";
	private $mObjFiltro			= null;
	private $mOrderASC			= "ASC";
	function setOperador($operador){$this->mOperador = $operador;}
	function setInvertirOrden(){ $this->mOrderASC = "DESC"; }
	function getSumaDeIngresosPorFechas($fecha_inicial, $fecha_final){ return $this->getSumaDeBasePorFechas($fecha_inicial, $fecha_final, 2002);	}
	function OFiltro(){ if($this->mObjFiltro == null){ $this->mObjFiltro = new cSQLFiltros();} return $this->mObjFiltro; }
	function getSumaDeBasePorFechas($fecha_inicial, $fecha_final, $clave_de_base){
		$this->mTitle		= "operacion";
		$sql = "SELECT
		`operaciones_tipos`.`descripcion_operacion`           AS `operacion`,
		SUM(`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion` ) AS 'monto'
		FROM
		`eacp_config_bases_de_integracion_miembros`
		`eacp_config_bases_de_integracion_miembros`
		INNER JOIN `operaciones_tipos` `operaciones_tipos`
		ON `eacp_config_bases_de_integracion_miembros`.`miembro` =
		`operaciones_tipos`.`idoperaciones_tipos`
		INNER JOIN `operaciones_mvtos` `operaciones_mvtos`
		ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.
		`idoperaciones_tipos`
		WHERE
		(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` =$clave_de_base)
		AND
		(`operaciones_mvtos`.`fecha_operacion` >='$fecha_inicial')
		AND
		(`operaciones_mvtos`.`fecha_operacion` <='$fecha_final')
		GROUP BY
		`operaciones_mvtos`.`tipo_operacion`
		ORDER BY `eacp_config_bases_de_integracion_miembros`.`codigo_de_base`
		";
		return $sql;
	}
	function getBasesPorFechasPorDependencia($fecha_inicial, $fecha_final, $clave_de_base){
		$this->mTitle		= "empresa";
		
			$sql	= "SELECT
			/*`socios_general`.`dependencia`,*/
			`socios_aeconomica_dependencias`.`nombre_corto` AS `empresa`,
			SUM(`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`) AS 'monto'
			FROM
			`operaciones_mvtos` `operaciones_mvtos`
			INNER JOIN `socios_general` `socios_general`
			ON `operaciones_mvtos`.`socio_afectado` = `socios_general`.`codigo`
			INNER JOIN `socios_aeconomica_dependencias`
			`socios_aeconomica_dependencias`
			ON `socios_aeconomica_dependencias`.
			`idsocios_aeconomica_dependencias` = `socios_general`.`dependencia`
			INNER JOIN `eacp_config_bases_de_integracion_miembros`
			`eacp_config_bases_de_integracion_miembros`
			ON `eacp_config_bases_de_integracion_miembros`.`miembro` =
			`operaciones_mvtos`.`tipo_operacion`
			WHERE
			(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = $clave_de_base)
			AND
			((`operaciones_mvtos`.`fecha_operacion` >='$fecha_inicial')
			AND
			(`operaciones_mvtos`.`fecha_operacion` <='$fecha_final'))
			GROUP BY
			`socios_general`.`dependencia`,
			`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`";
		return $sql;
	}
	function getClave(){ return $this->mTitle; }
	function getListadoDeRecibos($tipo = "", $socio = "", $docto = "", $fecha_inicial = "", $fecha_final = "", $otros = "", $periodo = false){
		$ByPeriodo	= $this->OFiltro()->RecibosPorPeriodo($periodo);
		$ByTipo		= (setNoMenorQueCero($tipo) > 0) ? "AND (`operaciones_recibos`.`tipo_docto` = $tipo)" : "";
		$BySocio	= (setNoMenorQueCero($socio) > 0) ? "AND (`operaciones_recibos`.`numero_socio` = $socio) " : "";
		$ByDocto	= (setNoMenorQueCero($docto) >  0) ? " AND (`operaciones_recibos`.`docto_afectado` = $docto) " : "";
		$ByFecha	= ($fecha_inicial == "") ? "" : " AND `operaciones_recibos`.`fecha_operacion` = '$fecha_inicial' ";
		if($fecha_inicial != "" AND $fecha_final != ""){
			$ByFecha	= " AND (`operaciones_recibos`.`fecha_operacion` >= '$fecha_inicial' AND `operaciones_recibos`.`fecha_operacion`<='$fecha_final') ";
		}
		$sql = "SELECT
			`operaciones_recibos`.`idoperaciones_recibos`       AS `numero`,
			`operaciones_recibos`.`fecha_operacion`             AS `fecha`,
			`operaciones_recibostipo`.`descripcion_recibostipo` AS `tipo`,
			`operaciones_recibos`.`docto_afectado`              AS `documento`,
			`operaciones_recibos`.`numero_socio`		    AS `socio`,
			CONCAT(`socios_general`.`nombrecompleto`, ' ',
			`socios_general`.`apellidopaterno`, ' ',
			`socios_general`.`apellidomaterno`)		   AS `nombre`,
			`operaciones_recibos`.`total_operacion`   AS `total`,
			`periodo_de_documento` AS `periodo`
			FROM
			`operaciones_recibos` `operaciones_recibos`
			INNER JOIN `socios_general` `socios_general`
			ON `operaciones_recibos`.`numero_socio` = `socios_general`.`codigo`
			INNER JOIN `operaciones_recibostipo` `operaciones_recibostipo`
			ON `operaciones_recibos`.`tipo_docto` = `operaciones_recibostipo`.
			`idoperaciones_recibostipo`
		WHERE
			(`operaciones_recibos`.`idoperaciones_recibos` >0)
			$BySocio
			$ByTipo
			$ByDocto
			$ByFecha
			$otros
			$ByPeriodo
		ORDER BY
			`operaciones_recibos`.`fecha_operacion` " . $this->mOrderASC . ",
					`operaciones_recibos`.`docto_afectado`,
					`operaciones_recibos`.`idoperaciones_recibos` " . $this->mOrderASC . " ";
		//resetear order
		$this->mOrderASC	= "ASC";
		return $sql;
	}
	function getListadoDeRecibosConDocto($tipo = "", $socio = "", $docto = "", $fecha_inicial = "", $fecha_final = "", $otros = "", $periodo = false){
		$ByPeriodo	= $this->OFiltro()->RecibosPorPeriodo($periodo);
		$ByTipo		= (setNoMenorQueCero($tipo) > 0) ? "AND (`operaciones_recibos`.`tipo_docto` = $tipo)" : "";
		$BySocio	= (setNoMenorQueCero($socio) > 0) ? "AND (`operaciones_recibos`.`numero_socio` = $socio) " : "";
		$ByDocto	= (setNoMenorQueCero($docto) >  0) ? " AND (`operaciones_recibos`.`docto_afectado` = $docto) " : "";
		$ByFecha	= ($fecha_inicial == "") ? "" : " AND `operaciones_recibos`.`fecha_operacion` = '$fecha_inicial' ";
		if($fecha_inicial != "" AND $fecha_final != ""){
			$ByFecha	= " AND (`operaciones_recibos`.`fecha_operacion` >= '$fecha_inicial' AND `operaciones_recibos`.`fecha_operacion`<='$fecha_final') ";
		}
		$sql = "SELECT
		`operaciones_recibos`.`idoperaciones_recibos`       AS `numero`,
		`operaciones_recibos`.`fecha_operacion`             AS `fecha`,
		`operaciones_recibostipo`.`descripcion_recibostipo` AS `tipo`,
		`operaciones_recibos`.`docto_afectado`              AS `documento`,
		`vw_doctos_info`.`descripcion`						AS `descripcion`,
		`operaciones_recibos`.`numero_socio`		    AS `socio`,
		CONCAT(`socios_general`.`nombrecompleto`, ' ',
		`socios_general`.`apellidopaterno`, ' ',
		`socios_general`.`apellidomaterno`)		   AS `nombre`,
		`operaciones_recibos`.`total_operacion`   AS `total`,
		`periodo_de_documento` AS `periodo`

		FROM     `operaciones_recibos` 
		INNER JOIN `operaciones_recibostipo`  ON `operaciones_recibos`.`tipo_docto` = `operaciones_recibostipo`.`idoperaciones_recibostipo` 
		INNER JOIN `socios_general`  ON `operaciones_recibos`.`numero_socio` = `socios_general`.`codigo` 
		INNER JOIN `vw_doctos_info`  ON `operaciones_recibos`.`docto_afectado` = `vw_doctos_info`.`documento` 
		WHERE
		(`operaciones_recibos`.`idoperaciones_recibos` >0)
		$BySocio
		$ByTipo
		$ByDocto
		$ByFecha
		$otros
		$ByPeriodo
		ORDER BY
		`operaciones_recibos`.`fecha_operacion` " . $this->mOrderASC . ",
					`operaciones_recibos`.`docto_afectado`,
					`operaciones_recibos`.`idoperaciones_recibos` " . $this->mOrderASC . " ";
		//resetear order
		$this->mOrderASC	= "ASC";
		return $sql;
	}
	function getOperacionesPorEmpresaPorFechas($fecha_inicial = "", $fecha_final = "", $empresa = "", $base = "", $tipo_de_pago = ""){
	$this->mTitle		= "empresa";

	$ByFechas	= ($fecha_inicial != "") ? " (`operaciones_mvtos`.`fecha_operacion` >='$fecha_inicial') " : "";
		$ByFechas	.= ($fecha_final != "") ? " AND (`operaciones_mvtos`.`fecha_operacion` <='$fecha_final')" : "";
		$ByFechas	= ($ByFechas != "") ? " AND ($ByFechas) " : "";

				$ByBase		= ($base != "") ? " AND (`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` =$base) " : "";
				$ByEmpresa	= ($empresa != "" AND $empresa != "todas") ? " AND (`socios`.`iddependencia` = $empresa) " : "";
						$ByPago		= ($tipo_de_pago == "" OR $tipo_de_pago == "todas") ? "" : " AND `operaciones_recibos`.`tipo_pago` ='$tipo_de_pago' ";
						$sql	= "SELECT
						/*`socios`.`iddependencia`                    AS `empresa`,
						`socios`.`dependencia`                      AS `nombre`,*/
						`operaciones_mvtos`.`socio_afectado`        AS `persona`,
						`socios`.`nombre`                           AS `nombre_de_persona`,
						`operaciones_mvtos`.`tipo_operacion`        AS `operacion`,
						`operaciones_tipos`.`descripcion_operacion` AS `nombre_de_operacion`,
						SUM(`eacp_config_bases_de_integracion_miembros`.`afectacion` * `operaciones_mvtos`.`afectacion_real`)  AS `monto`,
						`operaciones_recibos`.`tipo_pago` AS `pago`
						FROM

						`operaciones_mvtos` `operaciones_mvtos`
						INNER JOIN `socios` `socios`
						ON `operaciones_mvtos`.`socio_afectado` = `socios`.`codigo`
						INNER JOIN `operaciones_tipos` `operaciones_tipos`
						ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.
						`idoperaciones_tipos`
						INNER JOIN `operaciones_recibos` `operaciones_recibos`
						ON `operaciones_mvtos`.`recibo_afectado` = `operaciones_recibos`
						.`idoperaciones_recibos`
						INNER JOIN `eacp_config_bases_de_integracion_miembros`
						`eacp_config_bases_de_integracion_miembros`
						ON `eacp_config_bases_de_integracion_miembros`.`miembro` =
						`operaciones_mvtos`.`tipo_operacion`
						WHERE
						(`operaciones_mvtos`.`afectacion_real` !=0)
						$ByBase
						$ByEmpresa
						$ByFechas
						$ByPago
						GROUP BY
						`operaciones_mvtos`.`socio_afectado`,
						`operaciones_mvtos`.`tipo_operacion`
						ORDER BY
						`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
						`operaciones_mvtos`.`socio_afectado` ";
						return $sql;
	}
	function getListadoDeSocios($where = "", $limit = "0,50", $extras=""){
	$where	= ($where == "") ? "" : " WHERE $where ";
	$Ofecha	= ($where == "") ? " fechaalta DESC, " : "";
	$sql	= "SELECT
					`socios_general`.`codigo`          AS `codigo`,
					`socios_general`.`apellidopaterno` AS `apellido_paterno`,
					`socios_general`.`apellidomaterno` AS `apellido_materno`,
					`socios_general`.`nombrecompleto`  AS `nombres`,
					`socios_general`.`curp`
					$extras
					FROM
					`socios_general` `socios_general`
					$where
					ORDER BY
					$Ofecha
					`socios_general`.`apellidopaterno`,
					`socios_general`.`apellidomaterno`,
					`socios_general`.`nombrecompleto`
					LIMIT $limit";
	
					return $sql;
	}
	function getListadoDeBusquedaSocios($Nombre = "",$PrimerAp = "", $SegundoAp = "",  $CURP = "", $RFC = "", $OmitirID = 0, $limit = "0,50"){
		$PrimerAp 	= substr(setCadenaVal($PrimerAp),0,5);
		$Nombre		= substr(setCadenaVal($Nombre),0,5);
		$SegundoAp	= substr(setCadenaVal($SegundoAp),0,4);
		$CURP		= substr(setCadenaVal($CURP),0,8);
		$RFC		= substr(setCadenaVal($RFC),0,8);
		$OmitirID	= setNoMenorQueCero($OmitirID);
		$ByRFC		= ($RFC == "") ? "" : " AND `rfc` LIKE '%$RFC%' ";
		$ByCURP		= ($CURP == "") ? "" : " AND `curp` LIKE '%$CURP%' ";
		$ByNombre	= ($Nombre == "") ? "" : " AND `nombrecompleto` LIKE '%$Nombre%' ";
		$ByPrimerAp	= ($PrimerAp == "") ? "" : " AND `apellidopaterno` LIKE '%$PrimerAp%' ";
		$BySegundoAp	= ($SegundoAp == "") ? "" : " AND `apellidomaterno` LIKE '%$SegundoAp%' ";
		

		
		$sql	= "SELECT
		`socios_general`.`codigo`          AS `codigo`,
		`socios_general`.`apellidopaterno` AS `apellido_paterno`,
		`socios_general`.`apellidomaterno` AS `apellido_materno`,
		`socios_general`.`nombrecompleto`  AS `nombres`,
		`socios_general`.`curp`
		FROM
		`socios_general` `socios_general`
		WHERE `socios_general`.`codigo` != " . DEFAULT_SOCIO . " AND `socios_general`.`codigo` != $OmitirID
		$ByRFC
		$ByCURP
		$ByNombre
		$ByPrimerAp
		$BySegundoAp
		ORDER BY
		
		`socios_general`.`apellidopaterno`,
		`socios_general`.`apellidomaterno`,
		`socios_general`.`nombrecompleto`
		LIMIT $limit";
	
		return $sql;
	}

	function getListadoDeLlamadas($credito = false, $oficial = false, $fecha_inicial = false, $fecha_final = false, $efectuadas = false, $canceladas = false, $vencidas = false, $persona = false){
		$xF				= new cFecha();
		$xT				= new cTipos();
		$credito		= setNoMenorQueCero($credito);
		$ByCred			= ($credito <= DEFAULT_CREDITO) ? "" : " AND (`seguimiento_llamadas`.`numero_solicitud` =	$credito) ";
		$strPer			= "`socios`.`codigo`,`socios`.`nombre`, ";
		$strCred		= "`seguimiento_llamadas`.`numero_solicitud` AS 'credito',";
		$fecha_inicial	= $xF->getFechaISO($fecha_inicial);
		$fecha_final	= $xF->getFechaISO($fecha_final);
		$persona		= setNoMenorQueCero($persona);
		$vencidas		= $xT->cBool($vencidas);
		$canceladas		= $xT->cBool($canceladas);
		$efectuadas		= $xT->cBool($efectuadas);
		$ByPersona		= "";
		$ByNoVenc		= ( $vencidas == true ) ? "" : " AND (`seguimiento_llamadas`.`estatus_llamada` !='vencido')";
		$ByNoCanc		= ( $canceladas == true ) ? "" : " AND (`seguimiento_llamadas`.`estatus_llamada` !='cancelado')";
		$ByNoEfec		= ( $efectuadas == true ) ? "" : " AND (`seguimiento_llamadas`.`estatus_llamada` !='efectuado')";
		$ByOficial		= $this->OFiltro()->LlamadasPorOficial($oficial);
		$ByFecha		= " AND ( (`seguimiento_llamadas`.`fecha_llamada` >= '$fecha_inicial' )	AND	(`seguimiento_llamadas`.`fecha_llamada` <= '$fecha_final' ) ) ";
		$txtEstatus		= "`seguimiento_llamadas`.`estatus_llamada`        AS `estatus`,";
		if($persona > DEFAULT_SOCIO AND $credito <= DEFAULT_CREDITO){
			$strPer		= "";
			$ByPersona	= " AND (`socios`.`codigo`= $persona) ";
			$ByNoVenc	= "";
			$ByNoCanc	= "";
			$ByNoEfec	= "";
			$ByOficial	= "";
			$ByFecha	= "";
		}
		if($efectuadas == false AND $canceladas == false AND $vencidas == false){
			$txtEstatus	= "";
		}
		if($credito > DEFAULT_CREDITO){
			$strCred	= "";
			$strPer		= "";
			$ByNoVenc	= "";
			$ByNoCanc	= "";
			$ByNoEfec	= "";
			$ByOficial	= "";
			$ByFecha	= "";
		}
		$setSql = "	SELECT $strPer $strCred
				`seguimiento_llamadas`.`idseguimiento_llamadas` AS `clave`,
				`seguimiento_llamadas`.`fecha_llamada`          AS `fecha`,
				`seguimiento_llamadas`.`hora_llamada`           AS `hora`,
				$txtEstatus
				`seguimiento_llamadas`.`observaciones`          AS `notas`
			FROM
			`seguimiento_llamadas` `seguimiento_llamadas`
			INNER JOIN `socios` `socios`
			ON `seguimiento_llamadas`.`numero_socio` = `socios`.`codigo`
			INNER JOIN `oficiales` `oficiales`
			ON `seguimiento_llamadas`.`oficial_a_cargo` = `oficiales`.`id`
			WHERE `seguimiento_llamadas`.`idseguimiento_llamadas` > 0 $ByCred $ByPersona
			$ByFecha $ByOficial $ByNoCanc $ByNoEfec $ByNoVenc ORDER BY `socios`.`codigo`,	`seguimiento_llamadas`.`fecha_llamada`,`seguimiento_llamadas`.`hora_llamada`,`seguimiento_llamadas`.`idseguimiento_llamadas` ";
		
		return $setSql;
	}
	function getListadoDeNotificaciones($credito = false, $persona = false, $estado = SYS_TODAS, $FechaInicial = false, $FechaFinal = false, $oficial = false){
		$ByCredito	= $this->OFiltro()->NotificacionesPorCredito($credito);
		$ByPersona	= $this->OFiltro()->NotificacionesPorPersona($persona);
		$ByOficial	= $this->OFiltro()->NotificacionesPorOficial($oficial);
		$ByFecha	= "";
		
		$conCredito	= "";
		$conPersona	= "";
		
		if($FechaFinal != false AND $FechaInicial != false){
			$ByFecha	= " AND (`seguimiento_notificaciones`.`fecha_notificacion`>='$FechaInicial' AND `seguimiento_notificaciones`.`fecha_notificacion`<='$FechaFinal') ";
		} else {
			$ByFecha	= ($FechaInicial != false) ? " AND (`seguimiento_notificaciones`.`fecha_notificacion`='$FechaInicial') "  : "";
		}
		if($ByCredito == "" AND $ByPersona == ""){
			$conCredito	= " `seguimiento_notificaciones`.`numero_solicitud` AS `credito`, ";
			$conPersona	= " `socios`.`codigo`, `socios`.`nombre`, ";
		} else {
			if($ByPersona != ""){
				$conCredito	= " `seguimiento_notificaciones`.`numero_solicitud` AS `credito`, ";
			}
		}				
		$sql = "
		SELECT
		`seguimiento_notificaciones`.`idseguimiento_notificaciones`  AS `clave`,
		$conPersona
		$conCredito	
		`seguimiento_notificaciones`.`fecha_notificacion`   AS `fecha`,
		`seguimiento_notificaciones`.`hora`					AS `hora`,
		`seguimiento_notificaciones`.`estatus_notificacion` AS `estatus`,
		`seguimiento_notificaciones`.`observaciones`        AS `notas`
		FROM
		`seguimiento_notificaciones` `seguimiento_notificaciones`
		INNER JOIN `oficiales` `oficiales`
		ON `seguimiento_notificaciones`.`oficial_de_seguimiento` = `oficiales`.
		`id`
		INNER JOIN `socios` `socios`
		ON `seguimiento_notificaciones`.`socio_notificado` = `socios`.
		`codigo`
		WHERE
		(`seguimiento_notificaciones`.`idseguimiento_notificaciones` >0)
		$ByCredito $ByPersona $ByFecha $ByOficial
		ORDER BY
		`oficiales`.`id`,
		`socios`.`codigo`";
		//setLog($sql);
		return $sql;
	}
	function getListadoDeCompromisos($credito, $estado = SYS_TODAS, $persona = false, $FechaInicial = false , $FechaFinal = false, $oficial = false){
		$ByEstado	= ($estado == "" OR $estado == SYS_TODAS) ? "" : " AND (`seguimiento_compromisos`.`estatus_compromiso`='$estado') ";
		$ByCredito	= $this->OFiltro()->CompromisosPorCredito($credito);
		$ByPersona	= $this->OFiltro()->CompromisosPorPersona($persona);
		$ByUser		= $this->OFiltro()->CompromisosPorOficial($oficial);
		$ByFecha	= "";
		$conCredito	= "";
		$conPersona	= "";
		if($FechaFinal != false AND $FechaInicial != false){
			$ByFecha	= " AND (`seguimiento_compromisos`.`fecha_vencimiento`>='$FechaInicial' AND `seguimiento_compromisos`.`fecha_vencimiento` <='$FechaFinal')";
		} else {
			$ByFecha	= ($FechaInicial != false) ? " AND (`seguimiento_compromisos`.`fecha_vencimiento`='$FechaInicial') "  : "";
		}
		if($ByCredito == "" AND $ByPersona == ""){
			$conCredito	= " `seguimiento_compromisos`.`credito_comprometido` AS `credito`, ";
			$conPersona	= " `socios`.`codigo`, `socios`.`nombre`, ";			
		} else {
			if($ByPersona != ""){
				$conCredito	= " `seguimiento_compromisos`.`credito_comprometido` AS `credito`, ";
			}
		}
		$sql = " SELECT
		`seguimiento_compromisos`.`idseguimiento_compromisos` AS 'clave',
		$conPersona
		$conCredito
		`seguimiento_compromisos`.`fecha_vencimiento` AS `fecha`,
		`seguimiento_compromisos`.`hora_vencimiento` AS `hora`,
		`seguimiento_compromisos`.`tipo_compromiso` AS `tipo`,
		`seguimiento_compromisos`.`estatus_compromiso` AS 'estatus',
		`seguimiento_compromisos`.`anotacion` AS `notas`,
		`monto_comprometido` AS `monto`
	
		FROM
		`seguimiento_compromisos` `seguimiento_compromisos`
		INNER JOIN `socios` `socios`
		ON `seguimiento_compromisos`.`socio_comprometido` = `socios`.`codigo`
		INNER JOIN `oficiales` `oficiales`
		ON `seguimiento_compromisos`.`oficial_de_seguimiento` = `oficiales`.
		`id`
		WHERE
		(`seguimiento_compromisos`.`idseguimiento_compromisos` > 0)
		$ByPersona
		$ByCredito
		$ByFecha
		$ByEstado
		$ByUser
		ORDER BY
		`oficiales`.`id`,
		`seguimiento_compromisos`.`fecha_vencimiento`,
		`seguimiento_compromisos`.`hora_vencimiento`,
		`seguimiento_compromisos`.`tipo_compromiso`";
		return $sql;
	}
	function getListadoDeNotas($socio = false, $credito =false, $tipo = false, $archivado = false){
		$socio		= setNoMenorQueCero($socio);
		$credito	= setNoMenorQueCero($credito);
		$tipo		= setNoMenorQueCero($tipo);
		$archivado		= setNoMenorQueCero($archivado);
		
		$BySocio	= ($socio > DEFAULT_SOCIO) ? " AND (`socios_memo`.`numero_socio` =$socio) " : "";
		$ByCredito	= ($credito > DEFAULT_CREDITO) ? " AND `socios_memo`.`numero_solicitud` = $credito " : "";
		$ByTipo		= ($tipo > 0) ? " AND (`socios_memo`.`tipo_memo`=$tipo) " : "";
		$ByEstado	= ($archivado > 0) ? " AND (`archivado`=$archivado ) " : " AND (`archivado` = 0 ) ";
		$conCredito	= "";
		$conPersona	= "";
		if($BySocio != "" AND $ByCredito != ""){
			$conPersona	= " `personas`.`codigo`,`personas`.`nombre`, ";
			$conCredito	= " `socios_memo`.`numero_solicitud`      AS `documento`, ";
		} else {
			if($ByCredito == ""){ $conCredito	= " `socios_memo`.`numero_solicitud`      AS `documento`, "; }
		}
		$mSQLHist	= "SELECT
			`socios_memo`.`idsocios_memo`         AS `clave`,
			$conPersona
			$conCredito
			`oficiales`.`nombre_completo`         AS `usuario`,
			`socios_memotipos`.`descripcion_memo` AS `tipo`,
			`socios_memo`.`fecha_memo`            AS `fecha`,
			`socios_memo`.`texto_memo`            AS `notas` 
		FROM
			`personas` `personas` 
				INNER JOIN `socios_memo` `socios_memo` 
				ON `personas`.`codigo` = `socios_memo`.`numero_socio` 
					INNER JOIN `socios_memotipos` `socios_memotipos` 
					ON `socios_memo`.`tipo_memo` = `socios_memotipos`.`tipo_memo` 
						INNER JOIN `oficiales` `oficiales` 
						ON `socios_memo`.`idusuario` = `oficiales`.`id`
			WHERE `socios_memo`.`idsocios_memo` > 0 $BySocio $ByCredito $ByTipo $ByEstado ";
		
	return $mSQLHist;
	}
	function getFlujoDeEfectivo($credito = false, $socio = false){
	$BySocio	= ($socio != false) ? " AND(`creditos_flujoefvo`.`socio_flujo` = $socio)  " : "";
	$ByCredito	= ($credito != false) ? " AND `creditos_flujoefvo`.`solicitud_flujo`=$credito " : "";
	$sql_flujo = "SELECT
	`creditos_flujoefvo`.`fecha_captura`             AS `fecha_de_registro`,
	`creditos_tflujo`.`descripcion_tflujo`           AS `tipo`,
	`creditos_origenflujo`.`descripcion_origenflujo` AS `origen`,
	`creditos_periocidadflujo`.`descripcion_periocidadflujo`    AS `frecuencia`,
	(`creditos_flujoefvo`.`afectacion_neta`)              AS `monto`,
	`creditos_flujoefvo`.`descripcion_completa`      AS `descripcion`,
	(`creditos_flujoefvo`.`monto_flujo` * `creditos_origenflujo`.`afectacion`)  AS `monto_neto`
	FROM
	`creditos_tflujo` `creditos_tflujo`
	INNER JOIN `creditos_flujoefvo` `creditos_flujoefvo`
	ON `creditos_tflujo`.`idcreditos_tflujo` = `creditos_flujoefvo`.
	`tipo_flujo`
	INNER JOIN `creditos_periocidadflujo` `creditos_periocidadflujo`
	ON `creditos_periocidadflujo`.`periocidad_flujo` =
	`creditos_flujoefvo`.`periocidad_flujo`
	INNER JOIN `creditos_origenflujo` `creditos_origenflujo`
	ON `creditos_origenflujo`.`idcreditos_origenflujo` =
	`creditos_flujoefvo`.`origen_flujo`
	WHERE (`creditos_flujoefvo`.`idcreditos_flujoefvo` !=0)	$BySocio $ByCredito ";
	return $sql_flujo;
	}
	function getConceptosDePago($solicitud, $socio, $parcialidad = false){
		$parcialidad	= setNoMenorQueCero($parcialidad);
		//2015-09-01.- integra_parcialidad
	$ByLetra			= ($parcialidad > 0) ? " AND (`operaciones_mvtos`.`periodo_socio` = $parcialidad )  AND (`operaciones_tipos`.`integra_parcialidad`!='0') " : "";
	$SQLBody = "
	SELECT
	`operaciones_mvtos`.`socio_afectado`, `operaciones_mvtos`.`docto_afectado`,
	`operaciones_mvtos`.`tipo_operacion`, `operaciones_tipos`.`descripcion_operacion`,
	SUM(`operaciones_mvtos`.`afectacion_real` * `operaciones_tipos`.`afectacion_en_recibo`) AS 'total_operacion',
                `operaciones_tipos`.`codigo_de_valoracion`,
                `operaciones_tipos`.`afectacion_en_recibo`,
                `eacp_config_bases_de_integracion_miembros`.`miembro`
            FROM
			`operaciones_mvtos` `operaciones_mvtos`
				INNER JOIN `operaciones_tipos` `operaciones_tipos`
				ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.
				`idoperaciones_tipos`
					INNER JOIN `eacp_config_bases_de_integracion_miembros`
					`eacp_config_bases_de_integracion_miembros`
					ON `operaciones_mvtos`.`tipo_operacion` =
					`eacp_config_bases_de_integracion_miembros`.`miembro`
            WHERE
            	(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 1001)
            	AND
                (`operaciones_mvtos`.`docto_neutralizador` <= 1)
                AND (`operaciones_mvtos`.`socio_afectado` =" . $socio . ")
                AND (`operaciones_mvtos`.`docto_afectado` =" . $solicitud . ")
                /* condiciones_especiales */
                AND (`operaciones_mvtos`.`estatus_mvto` != 99)
                /*Condiciones de cobro */
				$ByLetra
            GROUP BY `operaciones_mvtos`.`docto_afectado`,
                `operaciones_mvtos`.`tipo_operacion`
			ORDER BY
			`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
					`operaciones_tipos`.`importancia_de_neutralizacion` ";
	
					return $SQLBody;
	}
	/**
	 * @deprecated @since 2016.06.03
	 */
	function getEstadoDeCuentaDeCreditos($where = ""){
	//$where	= ($where == "") ? "" : " WHERE $where ";
	$sql 	= "SELECT operaciones_mvtos.idoperaciones_mvtos AS 'control',
	operaciones_mvtos.fecha_operacion AS 'fecha_operacion',
	operaciones_mvtos.recibo_afectado AS 'recibo',
	operaciones_mvtos.periodo_socio as 'parcialidad',
	operaciones_tipos.descripcion_operacion AS 'tipo_operacion',
	(operaciones_mvtos.afectacion_real * operaciones_mvtos.valor_afectacion) AS 'monto',
	operaciones_mvtos.detalles, operaciones_mvtos.tipo_operacion,
	`operaciones_recibos`.`total_operacion` AS `total_recibo`,
	`operaciones_recibos`.`tipo_pago`       AS `tipo_de_pago`
	FROM
	`operaciones_mvtos` `operaciones_mvtos`
	INNER JOIN `operaciones_tipos` `operaciones_tipos`
	ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.
	`idoperaciones_tipos`
	INNER JOIN `operaciones_recibos` `operaciones_recibos`
	ON `operaciones_mvtos`.`recibo_afectado` = `operaciones_recibos`.
	`idoperaciones_recibos`
	INNER JOIN `operaciones_recibostipo` `operaciones_recibostipo`
	ON `operaciones_recibostipo`.`idoperaciones_recibostipo` =
	`operaciones_recibos`.`tipo_docto`
	INNER JOIN `eacp_config_bases_de_integracion_miembros`
	`eacp_config_bases_de_integracion_miembros`
	ON `eacp_config_bases_de_integracion_miembros`.`miembro` =
	`operaciones_mvtos`.`tipo_operacion`
	WHERE
	(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` =1000)
	$where
	ORDER BY
	`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`, 
	operaciones_mvtos.fecha_operacion,
	operaciones_mvtos.tipo_operacion,
	operaciones_mvtos.fecha_afectacion,
	operaciones_mvtos.periodo_socio
	";
	//setLog($sql);
	return $sql;
	}
	function getListadoDeOperacionesEstadoCuentaCred($credito = false){
		$xFil		= new cSQLFiltros();
		$ByCred		= $xFil->OperacionesPorDocumento($credito);
		$xB			= new cBases();
		$sql	= "
			SELECT operaciones_mvtos.idoperaciones_mvtos 				AS `control`,
				operaciones_mvtos.fecha_operacion 				AS `fecha`,
				operaciones_mvtos.recibo_afectado 				AS `recibo`,
				operaciones_mvtos.periodo_socio 				AS `parcialidad`,
				operaciones_tipos.descripcion_operacion 			AS `operacion`,
				(`operaciones_mvtos`.`afectacion_real` * 
				`operaciones_mvtos`.`valor_afectacion`) 			AS `monto`,
				
				`operaciones_recibos`.`total_operacion` 			AS `total_recibo`,
				`operaciones_recibos`.`tipo_pago`       			AS `tipo_de_pago`,
				
				CONCAT(TRIM(`operaciones_mvtos`.`detalles`),' ', 
				`operaciones_recibos`.`observacion_recibo`)			AS `observaciones`, 
				`operaciones_mvtos`.`tipo_operacion`				AS `tipo_de_operacion`,
				
				IF((`eacp_config_bases_de_integracion_miembros`.`subclasificacion` = 1010),
				`afectacion_real`, 0) 						AS `capital`,
				IF((`eacp_config_bases_de_integracion_miembros`.`subclasificacion` = 2000),
				`afectacion_real`, 0) 						AS `interes`,
				IF((`eacp_config_bases_de_integracion_miembros`.`subclasificacion` = 7021),
				`afectacion_real`, 0) 						AS `iva`,
				IF((`eacp_config_bases_de_integracion_miembros`.`subclasificacion` = 0),
				`afectacion_real`, 0) 						AS `otros`
				FROM
				`operaciones_mvtos` `operaciones_mvtos`
				INNER JOIN `operaciones_tipos` `operaciones_tipos`
				ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.
				`idoperaciones_tipos`
				INNER JOIN `operaciones_recibos` `operaciones_recibos`
				ON `operaciones_mvtos`.`recibo_afectado` = `operaciones_recibos`.
				`idoperaciones_recibos`
				INNER JOIN `operaciones_recibostipo` `operaciones_recibostipo`
				ON `operaciones_recibostipo`.`idoperaciones_recibostipo` =
				`operaciones_recibos`.`tipo_docto`
				INNER JOIN `eacp_config_bases_de_integracion_miembros`
				`eacp_config_bases_de_integracion_miembros`
				ON `eacp_config_bases_de_integracion_miembros`.`miembro` =
				`operaciones_mvtos`.`tipo_operacion`
				WHERE
				(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = " . $xB->BASE_CREDITOS_ESTADO_CUENTA .")
				$ByCred
				ORDER BY 
				`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
				operaciones_mvtos.fecha_operacion,
				operaciones_mvtos.tipo_operacion,
				operaciones_mvtos.fecha_afectacion,
				operaciones_mvtos.periodo_socio
				";
		//setLog($sql);
		return $sql;
	}
	function getListadoDeOperacionesBancarias($operacion = "", $cuenta = "", $cajero = "", $fecha_inicial = false, $fecha_final = false, $extra = ""){
	$ByCuenta		= ($cuenta != SYS_TODAS AND $cuenta != "") ? " AND `bancos_cuentas`.`idbancos_cuentas`=$cuenta " : "";
	$ByOperacion		= ($operacion != SYS_TODAS AND $operacion != "") ? " AND `bancos_operaciones`.`tipo_operacion`='$operacion' " : "";
	$ByFecha		= "";
	$ByFecha		= ($fecha_inicial == false) ? "" : " AND (`bancos_operaciones`.`fecha_expedicion`>='$fecha_inicial') ";
	$ByFecha		.= ($fecha_final == false) ? "" : " AND (`bancos_operaciones`.`fecha_expedicion`<='$fecha_final') ";

	$sql	= "SELECT
	`bancos_operaciones`.`idcontrol`          AS `clave`,
	`bancos_cuentas`.`descripcion_cuenta`     AS `cuenta`,
	`bancos_operaciones`.`recibo_relacionado` AS `recibo`,
	`bancos_operaciones`.`numero_de_socio`    AS `persona`,
	`bancos_operaciones`.`tipo_operacion`     AS `operacion`,
	`bancos_operaciones`.`fecha_expedicion`   AS `fecha`,
	`bancos_operaciones`.`beneficiario`       AS `beneficiarios`,
	`bancos_operaciones`.`monto_real`         AS `monto`
	FROM
	`bancos_operaciones` `bancos_operaciones`
	INNER JOIN `bancos_cuentas` `bancos_cuentas`
	ON `bancos_operaciones`.`cuenta_bancaria` = `bancos_cuentas`.
	`idbancos_cuentas`
	WHERE
	`bancos_operaciones`.`idcontrol` > 0
	$ByFecha
	$ByCuenta
	$ByOperacion
	$extra
	ORDER BY
	`bancos_operaciones`.`fecha_expedicion`,
	`bancos_operaciones`.`tipo_operacion`,
	`bancos_operaciones`.`beneficiario`
	";
	return $sql;
	}

	function getListadoDeOperacionesDeTesoreria($FechaInicial = "", $FechaFinal = "", $Recibo = "", $Expocision = ""){
	$ByFecha	= ($FechaInicial == "") ? "" : " AND `tesoreria_cajas_movimientos`.`fecha` >='$FechaInicial' ";
	$ByFecha	.= ($FechaFinal == "") ? "" :  " AND `tesoreria_cajas_movimientos`.`fecha` <='$FechaFinal' ";
	$ByRecibo	= ($Recibo == "") ? "" : " AND (`tesoreria_cajas_movimientos`.`recibo` =$Recibo ) ";
	$ByExp		= ($Expocision == "") ? "" : " AND ((`tesoreria_cajas_movimientos`.`tipo_de_exposicion` ='$Expocision') ";

	$sql	= "SELECT
	`tesoreria_cajas_movimientos`.`idtesoreria_cajas_movimientos` AS `clave`,
	`tesoreria_cajas_movimientos`.`recibo`,
	`usuarios`.`nombreusuario`                           AS `usuario`,
	`tesoreria_cajas_movimientos`.`fecha`,
	`tesoreria_cajas_movimientos`.`tipo_de_exposicion`   AS `forma_de_pago`,
	`tesoreria_cajas_movimientos`.`monto_del_movimiento` AS `monto`,
	`tesoreria_cajas_movimientos`.`monto_recibido`       AS `recibido`,
	`tesoreria_cajas_movimientos`.`monto_en_cambio`      AS `cambio`,
	`tesoreria_cajas_movimientos`.`observaciones`
	FROM
	`tesoreria_cajas_movimientos` `tesoreria_cajas_movimientos`
	INNER JOIN `usuarios` `usuarios`
	ON `tesoreria_cajas_movimientos`.`idusuario` = `usuarios`.`idusuarios`
	WHERE
	(idtesoreria_cajas_movimientos > 0) $ByFecha $ByRecibo $ByExp
	";
	return $sql;
	}

	function getListadoDeOperaciones($persona = "", $documento = "", $recibo = "", $otros = "" ){
	$ByPersona	= (setNoMenorQueCero($persona) <= 0) ? "" : " AND (`operaciones_mvtos`.`socio_afectado` = $persona) ";
	$ByRecibo	= (setNoMenorQueCero($recibo) <= 0) ? "" : " AND (`operaciones_mvtos`.`recibo_afectado` =$recibo)  ";
	$ByDocto	= (setNoMenorQueCero($documento)<=0) ? "" : " AND (`operaciones_mvtos`.`docto_afectado`=$documento) ";
	$sql = "SELECT
	`operaciones_mvtos`.`idoperaciones_mvtos`   AS `codigo`,
	`operaciones_mvtos`.`socio_afectado`       	AS `socio`,
	`operaciones_mvtos`.`docto_afectado`       	AS `documento`,
	getFechaMX(`operaciones_mvtos`.`fecha_operacion`)       AS `operado`,
	getFechaMX(`operaciones_mvtos`.`fecha_afectacion`)      AS `afectado`,
			`operaciones_mvtos`.`periodo_socio`			AS `periodo`,
			`operaciones_mvtos`.`tipo_operacion`        AS `operacion`,
			`operaciones_tipos`.`descripcion_operacion` AS `descripcion`,
			`operaciones_mvtos`.`afectacion_real`       AS `monto`
			FROM
			`operaciones_mvtos` `operaciones_mvtos`
			INNER JOIN `operaciones_tipos` `operaciones_tipos`
			ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.
			`idoperaciones_tipos`
			WHERE
			(`operaciones_mvtos`.`idoperaciones_mvtos` != 0)
			$ByRecibo $ByPersona $ByDocto $otros
			ORDER BY
			`operaciones_mvtos`.`fecha_operacion` " . $this->mOrderASC . ",
			`operaciones_mvtos`.`socio_afectado`,
			`operaciones_mvtos`.`docto_afectado`,
			`operaciones_mvtos`.`periodo_socio`,
			`operaciones_mvtos`.`tipo_operacion`
			LIMIT 0,1000
			";
			return $sql;
	}

	function getListadoDePersonasActividadesEconomicasTipos($clave = "", $clasificacion = "", $superior = "", $otros = ""){
	$operador	= $this->mOperador;
	$ByClave	= ($clave == "") ? "" : " AND (`personas_actividad_economica_tipos`.`clave_de_actividad` $operador $clave) ";
	$ByClas		= ($clasificacion == "") ? "" : " AND (`personas_actividad_economica_tipos`.`clasificacion` $operador '$clasificacion') ";
	$BySup		= ($superior == "") ? "" : " AND ( `personas_actividad_economica_tipos`.`clave_de_superior` $operador $superior) ";

	$sql	= "SELECT
	`personas_actividad_economica_tipos`.`clave_interna`,
	`personas_actividad_economica_tipos`.`clave_de_actividad`,
	`personas_actividad_economica_tipos`.`nombre_de_la_actividad`,
	`personas_actividad_economica_tipos`.`descripcion_detallada`,
	`personas_actividad_economica_tipos`.`productos`,
	`personas_actividad_economica_tipos`.`clasificacion`,
	`personas_actividad_economica_tipos`.`clave_de_superior`
	FROM
	`personas_actividad_economica_tipos` `personas_actividad_economica_tipos`
	WHERE (`personas_actividad_economica_tipos`.`clave_interna` > 0)
	$ByClas $ByClave $BySup $otros
	";
	return $sql;
	}

	function getListadoDeGarantiasReales($persona = "", $documento = "", $clave = "", $estado = false){
		$persona	= setNoMenorQueCero($persona);
		$documento	= setNoMenorQueCero($documento);
		$clave		= setNoMenorQueCero($clave);
		$estado		= setNoMenorQueCero($estado);
	$sql	= "SELECT
	`creditos_garantias`.`idcreditos_garantias`                AS `clave`,
	`creditos_tgarantias`.`descripcion_tgarantias`             AS `tipo`,
	`creditos_garantias`.`fecha_recibo`                        AS `recibido`,
	`creditos_garantiasestatus`.`descripcion_garantiasestatus` AS `estado`,
	`creditos_tvaluacion`.`descripcion_tvaluacion`             AS `valuacion`,
	`creditos_garantias`.`propietario`,
	`creditos_garantias`.`monto_valuado`                       AS `valor`
	FROM
	`creditos_garantias` `creditos_garantias`
	INNER JOIN `creditos_garantiasestatus` `creditos_garantiasestatus`
				ON `creditos_garantias`.`estatus_actual` = `creditos_garantiasestatus`.
				`idcreditos_garantiasestatus`
					INNER JOIN `creditos_tvaluacion` `creditos_tvaluacion`
					ON `creditos_tvaluacion`.`idcreditos_tvaluacion` =
					`creditos_garantias`.`tipo_valuacion`
						INNER JOIN `creditos_tgarantias` `creditos_tgarantias`
						ON `creditos_garantias`.`tipo_garantia` = `creditos_tgarantias`.
						`idcreditos_tgarantias`
		WHERE (`creditos_garantias`.`idcreditos_garantias` != 0)";
		$sql	.= ($persona <= DEFAULT_SOCIO) ? "" : " AND (`creditos_garantias`.`socio_garantia` = $persona )";
		$sql	.= ($clave <= 0) ? "" : " AND (`creditos_garantias`.`idcreditos_garantias` =$clave) ";
		$sql	.= ($documento <= DEFAULT_CREDITO) ? "" : " AND (`creditos_garantias`.`solicitud_garantia` =$documento) ";
		$sql	.= ($estado <= 0) ? "" : " AND (`creditos_garantias`.`estatus_actual` =$estado) ";
		return $sql;
	}
	function getListadoDeTareas($usuario = false, $FechaInicial = false, $FechaFinal = false, $estado = false){
		$ByUsuario		= (setNoMenorQueCero($usuario) > 0) ? " AND (`usuarios_web_notas`.`oficial` =$usuario) " : "";
		$ByEstado		= " AND	(`usuarios_web_notas`.`estado` != 40)";
		$FechaFinal		= ($FechaFinal == false) ? $FechaInicial : $FechaFinal;
		$ByFecha		= ($FechaInicial == false) ? "" : " AND ( (`usuarios_web_notas`.`fecha` >='$FechaInicial') AND (`usuarios_web_notas`.`fecha` <='$FechaFinal') )";
		if ( MODO_DEBUG == true ){		$ByUsuario = "";	}
	
		$sql			= "SELECT
				`usuarios_web_notas`.`idusuarios_web_notas` AS `codigo`,
				getFechaMX(`usuarios_web_notas`.`fecha`) AS 'fecha',
				`usuarios_web_notas`.`socio` AS 'persona',
				`usuarios_web_notas`.`texto`
					FROM
					`usuarios_web_notas` `usuarios_web_notas`
					INNER JOIN `usuarios` `usuarios`
					ON `usuarios_web_notas`.`oficial_de_origen` = `usuarios`.`idusuarios`
					WHERE `usuarios_web_notas`.`idusuarios_web_notas` > 0 
					$ByFecha
					$ByUsuario
					$ByEstado
				ORDER BY `usuarios_web_notas`.`fecha` DESC, `usuarios_web_notas`.`relevancia` LIMIT 0,50 ";
		
		return $sql;		
	}
	function getListadoDeAlertas($tipo = false, $FechaInicial = false, $FechaFinal = false, $persona = false, $ByOtros = "", $OtrosCampos = ""){
		$ByTipo		= $this->OFiltro()->AMLAlertasPorTipo($tipo);
		$ByFechas	= $this->OFiltro()->AMLAlertasPorFechasR($FechaInicial, $FechaFinal);
		
		$ConPersona	= " `aml_alerts`.`persona_de_origen`, `socios`.`nombre`, ";
		$ByPersona	= $this->OFiltro()->AMLAlertasPorPersona($persona);
		
		if( $ByPersona !== "" ){
			$ConPersona	= "";
		}
		$sql	= "SELECT
				`aml_alerts`.`clave_de_control`,
				$ConPersona
				`aml_alerts`.`documento_relacionado` AS 'documento',
				
				
				`aml_risk_catalog`.`descripcion` ,
				
				/*`aml_alerts`.`tipo_de_documento` AS 'tipo',*/
				/*getFechaByInt(`aml_alerts`.`fecha_de_registro`) AS 'fecha_de_registro',*/
				getFechaMXByInt(`aml_alerts`.`fecha_de_origen`) AS 'fecha_de_origen',
				`aml_alerts`.`riesgo_calificado`
				/*, `aml_alerts`.`mensaje`*/
				$OtrosCampos
				FROM
			`aml_alerts` `aml_alerts` 
				LEFT OUTER JOIN `socios` `socios` 
				ON `aml_alerts`.`persona_de_origen` = `socios`.`codigo` 
					INNER JOIN `aml_risk_catalog` `aml_risk_catalog` 
					ON `aml_alerts`.`tipo_de_aviso` = `aml_risk_catalog`.
					`clave_de_control`
				  
				WHERE `aml_alerts`.`clave_de_control` > 0 $ByTipo $ByPersona $ByFechas $ByOtros 
		ORDER BY `persona_de_origen`, `fecha_de_origen`  ";
		//$sql	.= ($tipo == false) ? "" : " AND (`aml_alerts`.`tipo_de_aviso` =$tipo) ";
		//$sql	.= ($FechaInicial == false) ? "" : " AND getFechaByInt(`aml_alerts`.`fecha_de_registro`) >= '$FechaInicial' ";
		//$sql	.= ($FechaFinal == false) ? "" : " AND getFechaByInt(`aml_alerts`.`fecha_de_registro`) <= '$FechaFinal' ";
		//$sql	.= $ByPersona;
		//$sql	.= $ByOtros;
		
		return $sql;
	}
	function getListadoDeRiesgosConfirmados($fecha_inicial = false, $fecha_final = false, $tipo = false, $clasificacion = false, $persona = false, $otros = "", $otrosCampos = ""){
		$xF				= new cFecha();
		$persona		= setNoMenorQueCero($persona);
		//$fecha_inicial 	= ($fecha_inicial == false) ? $xF->getDiaInicial() : $fecha_inicial;
		//$fecha_final	= ($fecha_final == false) ? $xF->getDiaFinal() : $fecha_final;
		$ByFecha		= ($fecha_inicial == false)  ? "" : "  AND (getFechaByInt(`aml_risk_register`.`fecha_de_reporte`) >='$fecha_inicial' ";
		$ByFecha		.= ($fecha_final == false) ? "" : " AND getFechaByInt(`aml_risk_register`.`fecha_de_reporte`) <='$fecha_final') ";
		//$ByEstado		= ($estado === false) ? "" : "  ";
		
		$ByPersona		= ( $persona <= 0 ) ? "" : " AND (`socios`.`codigo` = $persona) ";
		$sql	= "SELECT
				`aml_risk_register`.`clave_de_riesgo` AS 'clave',
				`aml_risk_register`.`persona_relacionada` AS 'persona',
				`socios`.`nombre`,
								
				/*`aml_risk_register`.`tipo_de_riesgo` AS 'tipo',*/
				
				`aml_risk_catalog`.`descripcion` AS 'descripcion',
				/*`aml_risk_types`.`nombre_del_riesgo` AS 'clasificacion',*/

				getFechaByInt(`aml_risk_register`.`fecha_de_reporte`) AS 'fecha',
	`aml_tipos_de_operacion`.`nombre_de_la_operacion`      AS `operacion`,
	`aml_instrumentos_financieros`.`nombre_de_instrumento` AS `instrumento` 
				/*`aml_risk_register`.`hora_de_reporte` AS 'hora'*/ 
				$otrosCampos
			FROM
	`aml_risk_register` `aml_risk_register` 
		INNER JOIN `aml_instrumentos_financieros` `aml_instrumentos_financieros` 
		ON `aml_risk_register`.`instrumento_financiero` = 
		`aml_instrumentos_financieros`.`tipo_de_instrumento` 
			INNER JOIN `aml_risk_catalog` `aml_risk_catalog` 
			ON `aml_risk_register`.`tipo_de_riesgo` = `aml_risk_catalog`.
			`clave_de_control` 
				INNER JOIN `aml_risk_types` `aml_risk_types` 
				ON `aml_risk_catalog`.`tipo_de_riesgo` = `aml_risk_types`.
				`clave_de_control` 
					INNER JOIN `aml_tipos_de_operacion` `aml_tipos_de_operacion` 
					ON `aml_risk_register`.`tipo_de_operacion` = 
					`aml_tipos_de_operacion`.`tipo_de_operacion_aml` 
						INNER JOIN `socios` `socios` 
						ON `aml_risk_register`.`persona_relacionada` = `socios`.
						`codigo`
			WHERE `aml_risk_register`.`clave_de_riesgo` > 0
			 
				$ByFecha
				$otros
		";
		$sql	.= ($tipo == false) ? "" : " AND (`aml_risk_catalog`.`clave_de_control` =$tipo)  ";
		$sql	.= (setNoMenorQueCero($clasificacion) <= 0) ? "" : " AND (`aml_risk_types`.`clave_de_control` =$clasificacion) ";
		$sql	.= $ByPersona;
		
		return $sql;
	}
	function getListadoDePerfil($persona){
		$sql	= "SELECT
		`personas_perfil_transaccional`.`idpersonas_perfil_transaccional` AS `clave`,
		getFechaMXByInt(`personas_perfil_transaccional`.`fecha_de_registro`)               AS `fecha`,
		`personas_perfil_transaccional`.`clave_de_tipo_de_perfil`         AS `tipo`,
				`personas_perfil_transaccional_tipos`.`nombre_del_perfil`         AS `perfil`,
				`personas_domicilios_paises`.`nombre_oficial`                     AS `pais`,
				`personas_perfil_transaccional`.`maximo_de_operaciones`           AS `numero`,
				`personas_perfil_transaccional`.`cantidad_maxima`                 AS `monto`
				FROM
				`personas_perfil_transaccional` `personas_perfil_transaccional`
				INNER JOIN `personas_perfil_transaccional_tipos`
				`personas_perfil_transaccional_tipos`
				ON `personas_perfil_transaccional`.`clave_de_tipo_de_perfil` =
				`personas_perfil_transaccional_tipos`.
				`idpersonas_perfil_transaccional_tipos`
				INNER JOIN `personas_domicilios_paises` `personas_domicilios_paises`
				ON `personas_domicilios_paises`.`clave_de_control` =
				`personas_perfil_transaccional`.`pais_de_origen`
				WHERE
				(`personas_perfil_transaccional`.`clave_de_persona` =$persona) ";
				return $sql;
	}
	function getListadoResumenPerfilTransaccional($persona = false){
		$ByPersona	= setNoMenorQueCero($persona) > 0 ? "AND (`personas_perfil_transaccional`.`clave_de_persona` =$persona)" : "";
		$sql	= "SELECT
						MAX(`personas_perfil_transaccional_tipos`.`tipo_de_exhibicion`) AS 
						`exhibicion`,
						MAX(`personas_perfil_transaccional`.`pais_de_origen`)           AS `pais`,
						MAX(`tesoreria_monedas`.`clave_de_moneda`)                      AS `moneda`,
						`personas_perfil_transaccional`.`clave_de_tipo_de_perfil`       AS `tipo`,
						SUM(`personas_perfil_transaccional`.`cantidad_maxima`)          AS `monto`,
						SUM(`personas_perfil_transaccional`.`maximo_de_operaciones`)    AS `numero`,
						MAX(`personas_perfil_transaccional_tipos`.`afectacion`)         AS 
						`afectacion` 
					FROM
						`personas_perfil_transaccional` `personas_perfil_transaccional` 
							LEFT OUTER JOIN `tesoreria_monedas` `tesoreria_monedas` 
							ON `personas_perfil_transaccional`.`pais_de_origen` = 
							`tesoreria_monedas`.`pais_de_origen` 
								INNER JOIN `personas_perfil_transaccional_tipos` 
								`personas_perfil_transaccional_tipos` 
								ON `personas_perfil_transaccional`.`clave_de_tipo_de_perfil` = 
								`personas_perfil_transaccional_tipos`.
								`idpersonas_perfil_transaccional_tipos` 
			WHERE
				(`personas_perfil_transaccional`.`idpersonas_perfil_transaccional` >0) $ByPersona 				
							GROUP BY
								`personas_perfil_transaccional`.`clave_de_tipo_de_perfil`";
		return $sql;
	}
	/**
	 * @deprecated @since 2016.10.04
	 */
	function getAMLAcumuladoDeEgresos($periodo_inicial, $periodo_final = "", $persona = "", $moneda = false, $tipo = false){
		$ByPersona		= ($persona == "") ? "" : " AND (`aml_perfil_egresos_por_persona`.`socio_afectado` =$persona)";
		$periodo_final	= ($periodo_final == "") ? $periodo_inicial : $periodo_final;
		$ByMoneda		= ($moneda == false OR $moneda == SYS_TODAS) ? "" : " AND (`aml_perfil_egresos_por_persona`.`moneda` ='$moneda' ) ";
		$ByTipo			= ($tipo == false OR $tipo == SYS_TODAS) ? "" : " AND (`aml_perfil_egresos_por_persona`.`perfil`  ='$tipo' ) ";
		$sql	= "SELECT
				`aml_perfil_egresos_por_persona`.`socio_afectado`,
				MAX(`aml_perfil_egresos_por_persona`.`periodo`) AS `periodo`,
				SUM(`aml_perfil_egresos_por_persona`.`operaciones`) AS `numero`,
				SUM(`aml_perfil_egresos_por_persona`.`monto`)       AS `monto`, 
				MAX(`aml_perfil_egresos_por_persona`.`moneda`)     AS `moneda`,
				SUM(`aml_perfil_egresos_por_persona`.`original`)   AS `original`,
				`aml_perfil_egresos_por_persona`.`perfil`       AS `tipo`,
				MAX(`aml_perfil_egresos_por_persona`.`recibo`) AS `recibo`
			FROM
				`aml_perfil_egresos_por_persona` `aml_perfil_egresos_por_persona`
				WHERE (`aml_perfil_egresos_por_persona`.`periodo` >= $periodo_inicial AND `aml_perfil_egresos_por_persona`.`periodo` <= $periodo_final) 
				 
				$ByPersona $ByMoneda $ByTipo
			GROUP BY
				`aml_perfil_egresos_por_persona`.`socio_afectado`, `aml_perfil_egresos_por_persona`.`perfil`, `aml_perfil_egresos_por_persona`.`moneda` ";
		//setLog($sql);
		return $sql;
	}
	/**
	 * @deprecated @since 2016.10.04
	 */
	function getAMLAcumuladoDeEgresos_RT($periodo_inicial, $periodo_final = "", $persona = "", $moneda = false, $tipo = false){
		$ByPersona		= ($persona == "") ? "" : " AND (`aml_perfil_egresos_por_persona_rt`.`socio_afectado` =$persona)";
		$periodo_final	= ($periodo_final == "") ? $periodo_inicial : $periodo_final;
		$ByMoneda		= ($moneda == false OR $moneda == SYS_TODAS) ? "" : " AND (`aml_perfil_egresos_por_persona_rt`.`moneda` ='$moneda' ) ";
		$ByTipo			= ($tipo == false OR $tipo == SYS_TODAS) ? "" : " AND (`aml_perfil_egresos_por_persona_rt`.`perfil`  ='$tipo' ) ";
		$sql	= "SELECT
		`aml_perfil_egresos_por_persona_rt`.`socio_afectado`,
		MAX(`aml_perfil_egresos_por_persona_rt`.`periodo`) AS `periodo`,
		SUM(`aml_perfil_egresos_por_persona_rt`.`operaciones`) AS `numero`,
		SUM(`aml_perfil_egresos_por_persona_rt`.`monto`)       AS `monto`,
		MAX(`aml_perfil_egresos_por_persona_rt`.`moneda`)     AS `moneda`,
		SUM(`aml_perfil_egresos_por_persona_rt`.`original`)   AS `original`,
		`aml_perfil_egresos_por_persona_rt`.`perfil`       AS `tipo`,
		MAX(`aml_perfil_egresos_por_persona_rt`.`recibo`) AS `recibo`
		FROM
		`aml_perfil_egresos_por_persona_rt` `aml_perfil_egresos_por_persona_rt`
		WHERE (`aml_perfil_egresos_por_persona_rt`.`periodo` >= $periodo_inicial AND `aml_perfil_egresos_por_persona_rt`.`periodo` <= $periodo_final)
			
		$ByPersona $ByMoneda $ByTipo
		GROUP BY
		`aml_perfil_egresos_por_persona_rt`.`socio_afectado`, `aml_perfil_egresos_por_persona_rt`.`perfil`, `aml_perfil_egresos_por_persona_rt`.`moneda` ";
		//setLog($sql);
		return $sql;
		
	}
	function getAMLAcumuladoOperacionesRT($persona = false, $FechaCorte = false, $moneda = false, $tipo = false, $FechaInit = false){
		$xF			= new cFecha();
		$persona	= setNoMenorQueCero($persona);
		$ByPersona	= $this->OFiltro()->RecibosPorPersona($persona);
		$ByMoneda	= $this->OFiltro()->RecibosPorMoneda($moneda);
		$FechaCorte	= $xF->getFechaISO($FechaCorte);
		if($FechaInit === false){
			$FechaInit	= $xF->setRestarDias(30, $FechaCorte);
		} else {
			$FechaInit	= $xF->getFechaISO($FechaInit);
		}
		$ByFechas	= $this->OFiltro()->RecibosPorFecha($FechaInit, $FechaCorte);
		$ByTipo		= ($tipo == false OR $tipo == SYS_TODAS) ? "" : " AND (`personas_perfil_transaccional_tipos`.`tipo_de_exhibicion`  ='$tipo' ) ";
		
		/*AND ( `tesoreria_tipos_de_pago`.`tipo_de_movimiento` =1 )*/
		$sql	= "SELECT
			  `operaciones_recibos`.`numero_socio`                         AS `socio_afectado`,
			  DATE_FORMAT(`operaciones_recibos`.`fecha_operacion`,'%Y%m')  AS `periodo`,
			  `operaciones_recibos`.`clave_de_moneda`                      AS `moneda`,
			  `operaciones_recibos`.`tipo_pago`                      AS `tipo`,
			  COUNT(`operaciones_recibos`.`idoperaciones_recibos`)         AS `operaciones`,
			  SUM(`operaciones_recibos`.`unidades_en_moneda`)              AS `original`,
			  ROUND(SUM(
				IF(UPPER(`operaciones_recibos`.`clave_de_moneda`) != getMonedaLocal(), 
				getEquivalenciaDeMonedas(`operaciones_recibos`.`unidades_en_moneda`, 
				`operaciones_recibos`.`clave_de_moneda`),
				`operaciones_recibos`.`total_operacion`)
				),2)                    AS `monto`,
			
			  IF (`personas_perfil_transaccional_tipos`.`idpersonas_perfil_transaccional_tipos` IS NULL, 
			  LCASE(`operaciones_recibos`.`tipo_pago`), 
			  LCASE(`personas_perfil_transaccional_tipos`.`tipo_de_exhibicion` )) AS 'perfil',
				`operaciones_recibos`.`idoperaciones_recibos`                      AS `recibo`,
				`tesoreria_tipos_de_pago`.`tipo_de_movimiento`				AS `afectacion`,
				COUNT(`operaciones_recibos`.`idoperaciones_recibos`) AS `numero`
			FROM 
			
			   `operaciones_recibos` 
			INNER JOIN `tesoreria_tipos_de_pago`  ON `operaciones_recibos`.`tipo_pago` = `tesoreria_tipos_de_pago`.`tipo_de_pago` 
			LEFT OUTER JOIN `personas_perfil_transaccional_tipos`  ON `operaciones_recibos`.`origen_aml` = `personas_perfil_transaccional_tipos`.`idpersonas_perfil_transaccional_tipos` 
			WHERE  ( `tesoreria_tipos_de_pago`.`equivalente_aml` >0 )
			$ByPersona $ByMoneda $ByTipo $ByFechas
			

			GROUP BY `operaciones_recibos`.`numero_socio`, 
				`operaciones_recibos`.`tipo_pago`,
				`operaciones_recibos`.`clave_de_moneda`
					";
		//setLog($sql);
		return $sql;
	
	}
	
	function getListadoDeAvales($credito, $persona = ""){
		$persona	= setNoMenorQueCero($persona);
		$ByPersona	= ($persona == 0) ? "" : " AND (`socio_relacionado` = $persona) ";
		$sql	= "SELECT DISTINCT socios_relaciones.idsocios_relaciones AS 'num',
						socios_relacionestipos.descripcion_relacionestipos AS 'relacion',
						socios_consanguinidad.descripcion_consanguinidad AS 'consanguinidad',
						CONCAT(socios_relaciones.nombres ,' ', socios_relaciones.apellido_paterno, ' ', socios_relaciones.apellido_materno) AS 'nombre',
						socios_relaciones.curp AS 'curp',
						CONCAT(socios_relaciones.telefono_residencia, '; ' , socios_relaciones.telefono_movil)  AS 'telefonos',
						socios_relaciones.domicilio_completo AS 'domicilio',
						`socios_relaciones`.`numero_socio`
				FROM
					`socios_relaciones` `socios_relaciones`
						INNER JOIN `eacp_config_bases_de_integracion_miembros`
						`eacp_config_bases_de_integracion_miembros`
						ON `socios_relaciones`.`tipo_relacion` =
						`eacp_config_bases_de_integracion_miembros`.`miembro`
							INNER JOIN `socios_relacionestipos` `socios_relacionestipos`
							ON `socios_relaciones`.`tipo_relacion` = `socios_relacionestipos`.
							`idsocios_relacionestipos`
								INNER JOIN `socios_consanguinidad` `socios_consanguinidad`
								ON `socios_relaciones`.`consanguinidad` =
								`socios_consanguinidad`.`idsocios_consanguinidad`
				WHERE
					(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` =5002)
				AND
					((`socios_relaciones`.`credito_relacionado` = " .  $credito . ")
				OR (`socios_relaciones`.`tipo_relacion` = " . PERSONAS_REL_RES_SOLIDARIO . "))
				$ByPersona					
				ORDER BY
					`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
					`socios_relaciones`.`credito_relacionado`";
		//setLog($sql);
		return $sql;
	}
	function getListadoDeFirmantes($credito, $persona = ""){
		$sql = " SELECT `creditos_firmantes`.`idcreditos_firmantes`, `creditos_firmantes`.`credito`, `creditos_firmantes`.`persona`, `personas`.`nombre`,`creditos_firmantes`.`rol_firmante`
		FROM    `creditos_firmantes` INNER JOIN `personas`  ON `creditos_firmantes`.`persona` = `personas`.`codigo`
		WHERE   ( `creditos_firmantes`.`credito` = $credito ) ";
		
		return $sql;
	}
	function getQueryInicialDeCreditos($persona = "", $credito = "", $otros = "" ){
		$persona	= setNoMenorQueCero($persona);
		$credito	= setNoMenorQueCero($credito);
		$where	= "WHERE (`creditos_solicitud`.`numero_solicitud` >0) ";
		$where	.= ($persona <= DEFAULT_SOCIO) ? "" : " AND (`creditos_solicitud`.`numero_socio` = $persona) ";
		$where	.= ($credito <= DEFAULT_CREDITO) ? "" : " AND (`creditos_solicitud`.`numero_solicitud` = $credito ) ";
		$where	.= ($otros == "") ? "" : " $otros ";
		$sql = "SELECT
					`creditos_solicitud`.*,
					`creditos_tipoconvenio`.*,
					`creditos_periocidadpagos`.*,
					`creditos_estatus`.*,
					`creditos_solicitud`.`tasa_interes` AS `tasa_ordinaria_anual`,
					`creditos_solicitud`.`tipo_autorizacion` AS `tipo_de_autorizacion`,
					`creditos_solicitud`.`tasa_ahorro` AS `tasa_de_ahorro`
				FROM
					`creditos_tipoconvenio` `creditos_tipoconvenio`
						INNER JOIN `creditos_solicitud` `creditos_solicitud`
						ON `creditos_tipoconvenio`.`idcreditos_tipoconvenio`
						= `creditos_solicitud`.`tipo_convenio`
							INNER JOIN `creditos_periocidadpagos`
							`creditos_periocidadpagos`
							ON `creditos_periocidadpagos`.
							`idcreditos_periocidadpagos` =
							`creditos_solicitud`.`periocidad_de_pago`
								INNER JOIN `creditos_estatus`
								`creditos_estatus`
								ON `creditos_estatus`.`idcreditos_estatus` =
								`creditos_solicitud`.`estatus_actual` $where ";
		return $sql;
	}
	function getListadoDeCreditosParaPagos($persona = false, $sinsaldo = false, $estado = false, $producto = false, $otros= "", $sin_nombre = false){
		$xFil		= new cSQLFiltros();
		$castigado	= CREDITO_ESTADO_CASTIGADO;
		$ClaveFInP	= CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO;
		$ByPersona	= ($persona == false OR $persona == SYS_TODAS) ? "" : " AND (`creditos_solicitud`.`numero_socio` =$persona) ";
		$BySaldo	= ($sinsaldo == true) ? "" : " AND (`creditos_solicitud`.`saldo_actual` >= " . TOLERANCIA_SALDOS . " ) ";
		$ByEstado	= $xFil->CreditosPorEstado($estado);
		$ByProducto	= ($producto == false OR $producto  == SYS_TODAS) ? "" : " AND (`creditos_solicitud`.`tipo_convenio` =$producto)  ";
		$ConNombres	= ($sin_nombre == true) ? "" : " `creditos_solicitud`.`numero_socio` AS `persona`, `socios`.`nombre`, ";
		$tituloP	= " `creditos_tipoconvenio`.`descripcion_tipoconvenio`       AS `producto`, ";
		if($estado == 0){
			$tituloP	= " CONCAT( `creditos_estatus`.`descripcion_estatus`, '-',
					`creditos_tipoconvenio`.`descripcion_tipoconvenio`)
					AS `producto`, ";
		}
		$sql	= "SELECT
		$ConNombres
		`creditos_solicitud`.`numero_solicitud`                  AS `credito`,
		$tituloP
	
		`creditos_periocidadpagos`.`descripcion_periocidadpagos` AS `periocidad`,
	
		CONCAT(`creditos_solicitud`.`ultimo_periodo_afectado`, '/', `creditos_solicitud`.`pagos_autorizados`) AS 'periodo',
		IF((`creditos_solicitud`.`periocidad_de_pago` = $ClaveFInP), 0, `monto_parcialidad`) AS `monto_parcialidad`,
		`fecha_de_proximo_pago` AS `fecha_proximo_pago`,
		`creditos_solicitud`.`saldo_actual`                      AS `saldo_capital`
		FROM
	
		`creditos_solicitud` `creditos_solicitud`
		INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio`
		ON `creditos_solicitud`.`tipo_convenio` = `creditos_tipoconvenio`.
		`idcreditos_tipoconvenio`
		INNER JOIN `creditos_periocidadpagos` `creditos_periocidadpagos`
		ON `creditos_solicitud`.`periocidad_de_pago` =
		`creditos_periocidadpagos`.`idcreditos_periocidadpagos`
		INNER JOIN `socios` `socios`
		ON `creditos_solicitud`.`numero_socio` = `socios`.`codigo`
		INNER JOIN `creditos_estatus` `creditos_estatus`
		ON `creditos_solicitud`.`estatus_actual` =
		`creditos_estatus`.`idcreditos_estatus`
			
		WHERE (`creditos_solicitud`.`estatus_actual` != $castigado)
		$ByPersona $BySaldo $ByEstado $ByProducto $otros
		ORDER BY
		`creditos_solicitud`.`fecha_de_proximo_pago`,
		`creditos_solicitud`.`saldo_actual` DESC,
		`creditos_solicitud`.`fecha_ministracion`,
			
		`creditos_solicitud`.`fecha_vencimiento` ";
		return $sql;
	
	}	
	function getListadoDeCreditosPagados($persona, $producto = false, $SinNombre = false ){
		$otros		= " AND (`creditos_solicitud`.`saldo_actual` <= " . TOLERANCIA_SALDOS . ")  AND (`creditos_solicitud`.`estatus_actual` != " . CREDITO_ESTADO_AUTORIZADO . ") AND (`creditos_solicitud`.`estatus_actual` != " . CREDITO_ESTADO_SOLICITADO . ") ";
		return $this->getListadoDeCreditos($persona, true, false, $producto, $otros, $SinNombre);
	}
	function getListadoDeCreditos($persona = false, $sinsaldo = false, $estado = false, $producto = false, $otros= "", $sin_nombre = false){
		$xFil		= new cSQLFiltros();
		$castigado	= CREDITO_ESTADO_CASTIGADO;
		$ByPersona	= ($persona == false OR $persona == SYS_TODAS) ? "" : " AND (`creditos_solicitud`.`numero_socio` =$persona) ";
		$BySaldo	= ($sinsaldo == true) ? "" : " AND (`creditos_solicitud`.`saldo_actual` >= " . TOLERANCIA_SALDOS . " ) ";
		$ByEstado	= $xFil->CreditosPorEstado($estado);
		$ByProducto	= ($producto == false OR $producto  == SYS_TODAS) ? "" : " AND (`creditos_solicitud`.`tipo_convenio` =$producto)  ";
		$ConNombres	= ($sin_nombre == true) ? "" : " `creditos_solicitud`.`numero_socio` AS `persona`, `socios`.`nombre`, ";
		$tituloP	= " `creditos_tipoconvenio`.`descripcion_tipoconvenio`       AS `producto`, ";
		if($estado == 0){
			$tituloP	= " CONCAT( `creditos_estatus`.`descripcion_estatus`, '-', 
					`creditos_tipoconvenio`.`descripcion_tipoconvenio`)
					AS `producto`, ";
		}
		$sql	= "SELECT
				$ConNombres
				`creditos_solicitud`.`numero_solicitud`                  AS `credito`,
				$tituloP
				
				`creditos_periocidadpagos`.`descripcion_periocidadpagos` AS `periocidad`,
				
				CONCAT(`creditos_solicitud`.`ultimo_periodo_afectado`, '/', `creditos_solicitud`.`pagos_autorizados`) AS 'periodo',
				`creditos_solicitud`.`fecha_ministracion`                AS `otorgado`,
				`creditos_solicitud`.`fecha_vencimiento`                 AS `vencimiento`,
				`creditos_solicitud`.`monto_autorizado`                  AS `monto`,
				`creditos_solicitud`.`saldo_actual`                      AS `saldo`
				
				FROM
				
	`creditos_solicitud` `creditos_solicitud` 
		INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio` 
		ON `creditos_solicitud`.`tipo_convenio` = `creditos_tipoconvenio`.
		`idcreditos_tipoconvenio` 
			INNER JOIN `creditos_periocidadpagos` `creditos_periocidadpagos` 
			ON `creditos_solicitud`.`periocidad_de_pago` = 
			`creditos_periocidadpagos`.`idcreditos_periocidadpagos` 
				INNER JOIN `socios` `socios` 
				ON `creditos_solicitud`.`numero_socio` = `socios`.`codigo` 
					INNER JOIN `creditos_estatus` `creditos_estatus` 
					ON `creditos_solicitud`.`estatus_actual` = 
					`creditos_estatus`.`idcreditos_estatus`
							
				WHERE (`creditos_solicitud`.`estatus_actual` != $castigado)
						$ByPersona $BySaldo $ByEstado $ByProducto $otros
				ORDER BY
					`creditos_solicitud`.`saldo_actual` DESC,
					`creditos_solicitud`.`fecha_ministracion`,
					
					`creditos_solicitud`.`fecha_vencimiento` ";
		return $sql;

	}
	function getListadoDeLetras($fecha_inicial, $fecha_final = false, $persona = false, $credito = false, $otros = ""){
		$persona		= setNoMenorQueCero($persona);
		$credito		= setNoMenorQueCero($credito);
		$fecha_final	= ($fecha_final == false) ? $fecha_inicial : $fecha_final;
		$ByPersona		= ($persona <= DEFAULT_SOCIO) ? "" : " AND (`socios`.`codigo` = $persona) ";
		$ByCredito		= ($credito <= DEFAULT_CREDITO) ? "" : " AND (`letras`.`docto_afectado` =$credito) ";
		$sql	= "SELECT
					`socios`.`codigo`,
					`socios`.`nombre`,
					`letras`.`docto_afectado` AS `credito`,
					`letras`.`periodo_socio`  AS `parcialidad`,
					`letras`.`fecha_de_pago`,
					`letras`.`capital`,
					`letras`.`interes`,
					`letras`.`iva`,
					`letras`.`ahorro`,
					`letras`.`otros`,
					`letras`.`letra` 
				FROM
					`socios` `socios` 
						INNER JOIN `letras` `letras` 
						ON `socios`.`codigo` = `letras`.`socio_afectado` 
				WHERE
					(`letras`.`fecha_de_pago` >='$fecha_inicial' AND	`letras`.`fecha_de_pago` <= '$fecha_final') $ByPersona $ByCredito 
				ORDER BY
					`socios`.`nombre`,	`letras`.`docto_afectado`,	`letras`.`periodo_socio` ";
		return $sql;
	}
	function getListadoDeLetrasVista($persona = false, $credito = false, $periodo = false){
		$ByPersona		= $this->OFiltro()->OperacionesPorPersona($persona);
		$ByCredito		= $this->OFiltro()->OperacionesPorDocumento($credito);
		$ByPeriodo		= $this->OFiltro()->OperacionesPorPeriodo($periodo);
		
		$sql	= "SELECT
				  `eacp_config_bases_de_integracion_miembros`.`codigo_de_base` AS `codigo_de_base`,
				  `operaciones_mvtos`.`socio_afectado`                         AS `socio_afectado`,
				  `operaciones_mvtos`.`docto_afectado`                         AS `docto_afectado`,
				  `operaciones_mvtos`.`periodo_socio`                          AS `periodo_socio`,
				MIN(`operaciones_mvtos`.`fecha_afectacion`)                  AS `fecha_de_pago`,
				MAX(`operaciones_mvtos`.`fecha_afectacion`)                   AS `fecha_de_vencimiento`,
				SUM(IF(`operaciones_mvtos`.`tipo_operacion` = 410,`operaciones_mvtos`.`afectacion_real`,0)) AS `capital`,
				SUM(IF(`operaciones_mvtos`.`tipo_operacion` = 411,`operaciones_mvtos`.`afectacion_real`,0)) AS `interes`,
				SUM(IF(`operaciones_mvtos`.`tipo_operacion` = 413,`operaciones_mvtos`.`afectacion_real`,0)) AS `iva`,
				SUM(IF(`operaciones_mvtos`.`tipo_operacion` = 412,`operaciones_mvtos`.`afectacion_real`,0)) AS `ahorro`,
				
				SUM(IF((`operaciones_mvtos`.`tipo_operacion` < 410 OR `operaciones_mvtos`.`tipo_operacion` > 413), `operaciones_mvtos`.`afectacion_real`,0)) AS `otros`,
				
				SUM((`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`)) AS `letra`,
				
				SUM(IF((`operaciones_mvtos`.`tipo_operacion` < 410 OR `operaciones_mvtos`.`tipo_operacion` > 413),0, `operaciones_mvtos`.`afectacion_real`)) AS `total_sin_otros`,
				
				MAX(IF((`operaciones_mvtos`.`tipo_operacion` < 410 OR `operaciones_mvtos`.`tipo_operacion` > 413),`operaciones_mvtos`.`tipo_operacion`,0)) AS `clave_otros`
				
				
				FROM (`operaciones_mvtos`
				   JOIN `eacp_config_bases_de_integracion_miembros`
				     ON ((`operaciones_mvtos`.`tipo_operacion` = `eacp_config_bases_de_integracion_miembros`.`miembro`)))
				WHERE (`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 2601)
				AND `operaciones_mvtos`.`tipo_operacion` != 420
				AND `operaciones_mvtos`.`tipo_operacion` != 431
				AND `operaciones_mvtos`.`tipo_operacion` != 146 /*gastos de cobranza*/ 
				$ByPersona $ByCredito $ByPeriodo 
				GROUP BY `operaciones_mvtos`.`docto_afectado`,`operaciones_mvtos`.`periodo_socio`
				ORDER BY
				`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
				`operaciones_mvtos`.`docto_afectado`, `operaciones_mvtos`.`periodo_socio`";
		return $sql;
	}
	
	function getListadoDeLetrasPendientes($idcredito, $tasaIVA = TASA_IVA, $EsSoloInteres = false){
		$idcredito	= setNoMenorQueCero($idcredito);
		$wcapital	= ($EsSoloInteres == false) ? " AND capital >0 " : "";
		//+ `creditos_solicitud`.`tasa_interes`
		$xForm		= new cFormula();
		$xForm->init($xForm->SQL_MORA_X_LETRA);
		$mFormula	= $xForm->getFormula(); //(letras.capital * DATEDIFF(getFechaDeCorte(), `fecha_de_pago`) * (`creditos_solicitud`.`tasa_moratorio` ))/getDivisorDeInteres()
		//TODO: Cargar XFormulas y cargar la lista Formula
		$patch_001	= " AND (`letras`.`fecha_de_pago` != `creditos_solicitud`.`fecha_ministracion`) ";
		$sql		= "SELECT
		`letras`.`socio_afectado` AS `persona`,
		`letras`.`docto_afectado` AS `credito`,
		`letras`.`periodo_socio`  AS `parcialidad`,
		`letras`.`fecha_de_pago`,
		
		`letras`.`capital`,
		`letras`.`interes`,
		`letras`.`iva`,
		`letras`.`ahorro`,
		`letras`.`otros`,
		`letras`.`letra` AS `total`,
		
		(`creditos_solicitud`.`tasa_moratorio`*100) AS `tasa_de_mora`,
		(`creditos_solicitud`.`tasa_interes`*100)   AS `tasa_de_interes` ,
		DATEDIFF(getFechaDeCorte(), fecha_de_pago) 	AS 'dias',
		($mFormula) AS 'mora',
		(($mFormula) * $tasaIVA) AS 'iva_moratorio'
		FROM
		`creditos_solicitud` `creditos_solicitud`
		INNER JOIN `letras` `letras`
		ON `creditos_solicitud`.`numero_solicitud` = `letras`.`docto_afectado`
		
		WHERE  docto_afectado=$idcredito $wcapital AND `letras`.`periodo_socio` > 0
		
		$patch_001 
		AND fecha_de_pago <= getFechaDeCorte()";
		
		return $sql;	
	}
	function getListadoDeLetrasPendientesReporte($otros = "", $tasaIVA = TASA_IVA, $EsSoloInteres = false, $empresa = false, $Producto = false, $Periocidad = false){
		
		$wcapital	= ($EsSoloInteres == false) ? " AND capital >0 " : "";
		$ByEmpresa	= $this->OFiltro()->CreditosPorEmpresa($empresa);
		$ByProducto	= $this->OFiltro()->CreditosPorProducto($Producto);
		$ByPeriocidad= $this->OFiltro()->CreditosPorFrecuencia($Periocidad);
		$wAhorro	= (MODULO_CAPTACION_ACTIVADO == true) ? " `letras`.`ahorro`, " : "";
		//Filtrar por saldo
		//+ `creditos_solicitud`.`tasa_interes`
		$patch_001	= " AND (`letras`.`fecha_de_pago` != `creditos_solicitud`.`fecha_ministracion`) ";
		$sql		= "SELECT
		`letras`.`socio_afectado` AS `persona`,
		`socios`.`nombre` AS `nombre`,
		`letras`.`docto_afectado` AS `credito`,
		`letras`.`periodo_socio`  AS `parcialidad`,
		`letras`.`fecha_de_pago`,
	
		`letras`.`capital`,
		`letras`.`interes`,
		`letras`.`iva`,
		$wAhorro
		`letras`.`otros`,
		`letras`.`letra` AS `total`,
	
		(`creditos_solicitud`.`tasa_moratorio`*100) 	AS `tasa_de_mora`,
		(`creditos_solicitud`.`tasa_interes`*100)   	AS `tasa_de_interes` ,
		DATEDIFF(getFechaDeCorte(), fecha_de_pago) 		AS 'dias',
		
		((letras.capital * DATEDIFF(getFechaDeCorte(), fecha_de_pago) * (`creditos_solicitud`.`tasa_moratorio` ))/getDivisorDeInteres()) AS 'mora',
		(((letras.capital * DATEDIFF(getFechaDeCorte(), fecha_de_pago) * (`creditos_solicitud`.`tasa_moratorio` ))/getDivisorDeInteres()) * $tasaIVA) AS 'iva_moratorio'
FROM
	`creditos_solicitud` `creditos_solicitud` 
		INNER JOIN `socios` `socios` 
		ON `creditos_solicitud`.`numero_socio` = `socios`.`codigo` 
			INNER JOIN `letras` `letras` 
			ON `creditos_solicitud`.`numero_solicitud` = `letras`.
			`docto_afectado` 
				INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio` 
				ON `creditos_solicitud`.`tipo_convenio` = 
				`creditos_tipoconvenio`.`idcreditos_tipoconvenio`
		WHERE `letras`.`periodo_socio` > 0 AND `creditos_solicitud`.`estatus_actual`!= " . CREDITO_ESTADO_CASTIGADO . " $wcapital
		$patch_001
		$ByEmpresa
		$ByPeriocidad
		$ByProducto
		AND fecha_de_pago <= getFechaDeCorte() $otros";
		
		return $sql;
	}
	function getListadoDeLetrasPendientesReporteAcum($otros = "", $tasaIVA = TASA_IVA, $EsSoloInteres = false, $empresa = false, $Producto = false, $Periocidad = false){
	
		
		$ByEmpresa	= $this->OFiltro()->CreditosPorEmpresa($empresa);
		$ByProducto	= $this->OFiltro()->CreditosPorProducto($Producto);
		$ByPeriocidad= $this->OFiltro()->CreditosPorFrecuencia($Periocidad);

		$wAhorro	= (MODULO_CAPTACION_ACTIVADO == true) ? " SUM(`creditos_letras_del_dia`.`ahorro`)        AS `ahorro`, " : "";
		
		$sql		= "SELECT
	`creditos_letras_del_dia`.`persona`,
	`personas`.`nombre`,
	`creditos_letras_del_dia`.`credito`,
	
	COUNT(`creditos_letras_del_dia`.`parcialidad`)   AS `numero_con_atraso`,
	MIN(`creditos_letras_del_dia`.`fecha_de_pago`) AS `fecha_de_atraso`,
	MAX(`creditos_letras_del_dia`.`dias`)          AS `dias`,
	
	`creditos_solicitud`.`monto_autorizado` AS `monto_ministrado`,
	
	SUM(`creditos_letras_del_dia`.`capital`)       AS `capital`,
	SUM(`creditos_letras_del_dia`.`interes`)       AS `interes`,
	SUM(`creditos_letras_del_dia`.`iva`)           AS `iva`,
	
	
	$wAhorro
	SUM(`creditos_letras_del_dia`.`otros`)         AS `otros`,
	SUM(`creditos_letras_del_dia`.`letra`)         AS `letra_original`,
	
	SUM(`creditos_letras_del_dia`.`mora`)          AS `moratorio`,
	SUM(`creditos_letras_del_dia`.`iva_moratorio`) AS `iva_moratorio`,
	
	SUM(`capital`+`interes`+`iva`+`ahorro`+`otros`+`mora`+`iva_moratorio`) AS `total` 
		
FROM
	`creditos_letras_del_dia` `creditos_letras_del_dia` 
		INNER JOIN `creditos_solicitud` `creditos_solicitud` 
		ON `creditos_letras_del_dia`.`credito` = `creditos_solicitud`.
		`numero_solicitud` 
			INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio` 
			ON `creditos_solicitud`.`tipo_convenio` = `creditos_tipoconvenio`.
			`idcreditos_tipoconvenio` 
				INNER JOIN `personas` `personas` 
				ON `creditos_letras_del_dia`.`persona` = `personas`.`codigo` 
		
		WHERE `creditos_letras_del_dia`.`parcialidad` > 0 
		AND `creditos_solicitud`.`estatus_actual`!= " . CREDITO_ESTADO_CASTIGADO . "
		$ByEmpresa
		$ByPeriocidad
		$ByProducto
		 $otros
		GROUP BY
			`creditos_letras_del_dia`.`credito`	
		ORDER BY MIN(`creditos_letras_del_dia`.`fecha_de_pago`)	
		";
		
		return $sql;
	}	
	function getListadoDeLetrasConCreditos($fecha_inicial, $fecha_final = false, $persona = "", $credito = "", $otros = "", $producto = ""){
		$ByProducto		= $this->OFiltro()->CreditosPorProducto($producto);
		$fecha_final	= ($fecha_final == false) ? $fecha_inicial : $fecha_final;
		$ByPersona		= ($persona == "" OR $persona == SYS_TODAS) ? "" : " AND (`socios`.`codigo` = $persona) ";
		$ByCredito		= ($credito == "" OR $credito == SYS_TODAS) ? "" : " AND (`letras`.`docto_afectado` =$credito) ";
		
		$sql	= "SELECT
					`socios`.`codigo`,
					`socios`.`nombre`,
					`letras`.`docto_afectado` AS `credito`,
					`letras`.`periodo_socio`  AS `parcialidad`,
					`letras`.`fecha_de_pago`,
					`letras`.`capital`,
					`letras`.`interes`,
					`letras`.`iva`,
					`letras`.`ahorro`,
					`letras`.`otros`,
					`letras`.`letra` 


			FROM
				`creditos_solicitud` `creditos_solicitud` 
					INNER JOIN `letras` `letras` 
					ON `creditos_solicitud`.`numero_solicitud` = `letras`.`docto_afectado` 
						INNER JOIN `socios` `socios` 
						ON `socios`.`codigo` = `letras`.`socio_afectado` 
							INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio` 
							ON `creditos_solicitud`.`tipo_convenio` = 
							`creditos_tipoconvenio`.`idcreditos_tipoconvenio` 
			WHERE (`letras`.`fecha_de_pago` >='$fecha_inicial' AND	`letras`.`fecha_de_pago` <= '$fecha_final') $ByPersona $ByCredito $ByProducto $otros 
			ORDER BY `socios`.`nombre`,	`letras`.`docto_afectado`,	`letras`.`periodo_socio` ";
		return $sql;
	}
	function getListadoDeLetrasConCreditos_Simple($fecha_inicial, $fecha_final = false, $persona = "", $credito = "", $otros = "", $SinSaldo = false){
		$fecha_final	= ($fecha_final == false) ? $fecha_inicial : $fecha_final;
		$persona		= setNoMenorQueCero($persona);
		$credito		= setNoMenorQueCero($credito);
		$ByPersona		= ($persona <= DEFAULT_SOCIO) ? "" : " AND (`socios`.`codigo` = $persona) ";
		$ByCredito		= ($credito <= DEFAULT_CREDITO) ? "" : " AND (`letras`.`docto_afectado` =$credito) ";
		$BySaldo		= ($SinSaldo == true) ? " AND (`letras`.`letra` > " . TOLERANCIA_SALDOS .  ") " : "";
		$sql	= "SELECT
		`socios`.`codigo`,
		`socios`.`nombre`,
		`letras`.`docto_afectado` AS `credito`,
		`letras`.`periodo_socio`  AS `periodo`,
		`letras`.`letra` AS 'monto'
	
		FROM
		`creditos_solicitud` `creditos_solicitud`
		INNER JOIN `letras` `letras`
		ON `creditos_solicitud`.`numero_solicitud` = `letras`.`docto_afectado`
		INNER JOIN `socios` `socios`
		ON `socios`.`codigo` = `letras`.`socio_afectado`
		INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio`
		ON `creditos_solicitud`.`tipo_convenio` =
		`creditos_tipoconvenio`.`idcreditos_tipoconvenio`
		WHERE (`letras`.`fecha_de_pago` >='$fecha_inicial' AND	`letras`.`fecha_de_pago` <= '$fecha_final') $ByPersona $ByCredito $otros $BySaldo
		ORDER BY `socios`.`nombre`,	`letras`.`docto_afectado`,	`letras`.`periodo_socio` ";
		return $sql;
	}	
	function getListaDeLetrasDelDia($fecha_inicial = false, $fecha_final = false, $persona = false, $credito = false, $otros = ""){
		$xF				= new cFecha();
		$persona		= setNoMenorQueCero($persona);
		$credito		= setNoMenorQueCero($credito);
		$fecha_inicial	= $xF->getFechaISO($fecha_inicial);
		$fecha_final	= ($fecha_final == false) ? $fecha_inicial : $xF->getFechaISO($fecha_final);
		$ByPersona		= ($persona <= DEFAULT_SOCIO) ? "" : " AND (`socios`.`codigo` =$persona) ";
		$ByCredito		= ($credito <= DEFAULT_CREDITO) ? "" : " AND (`creditos_letras_del_dia`.`credito` =$credito) ";
		$ByFecha		= " AND (`creditos_letras_del_dia`.`fecha_de_pago` >='$fecha_inicial') AND (`creditos_letras_del_dia`.`fecha_de_pago` <='$fecha_final') ";
		$sql			= "SELECT
			`socios`.`codigo`,
			`socios`.`nombre`,
			`creditos_letras_del_dia`.`credito`,
			`creditos_letras_del_dia`.`parcialidad`,
			`creditos_letras_del_dia`.`fecha_de_pago`,
			`creditos_letras_del_dia`.`capital`,
			`creditos_letras_del_dia`.`interes`,
			`creditos_letras_del_dia`.`iva`,
			`creditos_letras_del_dia`.`ahorro`,
			`creditos_letras_del_dia`.`otros`,
			`creditos_letras_del_dia`.`letra` 
		FROM
	`creditos_letras_del_dia` `creditos_letras_del_dia` 
		INNER JOIN `creditos_solicitud` `creditos_solicitud` 
		ON `creditos_letras_del_dia`.`credito` = `creditos_solicitud`.
		`numero_solicitud` 
			INNER JOIN `socios` `socios` 
			ON `creditos_letras_del_dia`.`persona` = `socios`.`codigo` 
		WHERE
			(`creditos_letras_del_dia`.`indice` >0) $ByPersona $ByCredito $ByFecha $otros ";
		return $sql;
	}
	function getListadoDeOperacionesDeEmpresas($empresa = SYS_TODAS, $periocidad = SYS_TODAS, $otros = "", $fecha = false, $OrdenB = ""){
		$empresa		= setNoMenorQueCero($empresa);
		$periocidad		= setNoMenorQueCero($periocidad);
		$ByEmpresa		= ($empresa <=0) ? "" : " AND (`empresas_operaciones`.`clave_de_empresa` =$empresa)  ";
		$ByPeriocidad	= ($periocidad <=0) ? "" : "AND	(`empresas_operaciones`.`periocidad` =$periocidad)";
		$xF				= new cFecha(0, $fecha);
		$anno			= $xF->anno();
		$OrdenB			= ($OrdenB == "") ? " `empresas_operaciones`.`idempresas_operaciones` DESC, `empresas_operaciones`.`fecha_de_operacion` " : $OrdenB;
		$sql 			= "SELECT
		`empresas_operaciones`.`clave_de_empresa`,
		`socios_aeconomica_dependencias`.`descripcion_dependencia` AS 'empresa',
		`creditos_periocidadpagos`.`descripcion_periocidadpagos` AS `periocidad`,
		CONCAT(`usuarios`.`nombres`,' ',
		`usuarios`.`apellidopaterno`, ' ',
		`usuarios`.`apellidomaterno`) AS 'oficial',
		`empresas_operaciones`.`periodo_marcado` AS 'periodo',
		`empresas_operaciones`.`fecha_de_operacion` AS 'fecha',
		IF((`tipo_de_operacion` = 1 ) , (`tipo_de_operacion` * `empresas_operaciones`.`monto`), 0) AS `envios`,
		IF((`tipo_de_operacion` = -1 ) , (`tipo_de_operacion` * `empresas_operaciones`.`monto`), 0) AS `cobros`,
		`empresas_operaciones`.`fecha_inicial`,
		`empresas_operaciones`.`fecha_final`,
		`empresas_operaciones`.`observaciones`
		FROM
		`socios_aeconomica_dependencias` `socios_aeconomica_dependencias`
		INNER JOIN `empresas_operaciones` `empresas_operaciones`
		ON `socios_aeconomica_dependencias`.`idsocios_aeconomica_dependencias` =
		`empresas_operaciones`.`clave_de_empresa`
		INNER JOIN `usuarios` `usuarios`
		ON `empresas_operaciones`.`oficial` = `usuarios`.`idusuarios`
		INNER JOIN `creditos_periocidadpagos` `creditos_periocidadpagos`
		ON `creditos_periocidadpagos`.`idcreditos_periocidadpagos` =
		`empresas_operaciones`.`periocidad`
		WHERE (`empresas_operaciones`.`idempresas_operaciones` > 0)
		AND (`fecha_inicial` >= '$anno-01-01' )
		
		AND (`empresas_operaciones`.`fecha_final` >= '2016-01-01')
		AND (`empresas_operaciones`.`fecha_inicial` >= '2016-01-01')
			
		$ByEmpresa $ByPeriocidad $otros
		
		ORDER BY $OrdenB
		
		";
		return $sql;		
	}

	function getListadoDeTesoreria($cajero = "", $fecha_inicial = "", $fecha_final = "", $tipo_exposicion = SYS_TODAS ){
		$ByUsuario2		= ($cajero == SYS_TODAS OR $cajero == "") ? "" : " AND (`tesoreria_cajas_movimientos`.`idusuario` = $cajero) ";
		$ByFecha		= ($fecha_inicial == "" ) ? "" : " AND (`tesoreria_cajas_movimientos`.`fecha` >='$fecha_inicial') ";
		$ByFecha		.= ($fecha_final == "" ) ? "" : " AND (`tesoreria_cajas_movimientos`.`fecha` <='$fecha_final')";
		$ByTipoExp		= ($tipo_exposicion == SYS_TODAS OR $tipo_exposicion == "") ? "" : " AND (`tesoreria_cajas_movimientos`.`tipo_de_exposicion`='$tipo_exposicion') ";
		
		switch ($tipo_exposicion){
			case TESORERIA_COBRO_CHEQUE:
				$sqlTi	= "SELECT
				`tesoreria_cajas_movimientos`.`tipo_de_exposicion` AS 'forma_de_pago',
				`tesoreria_cajas_movimientos`.`fecha`,
				`tesoreria_cajas_movimientos`.`tipo_de_exposicion`,
				`tesoreria_cajas_movimientos`.`numero_de_cheque` ,
				`bancos_entidades`.`nombre_de_la_entidad`, 
				(`tesoreria_cajas_movimientos`.`monto_del_movimiento`) AS `operacion`
				
				FROM 	`tesoreria_cajas_movimientos` `tesoreria_cajas_movimientos` 
		INNER JOIN `bancos_entidades` `bancos_entidades` 
		ON `tesoreria_cajas_movimientos`.`banco` = `bancos_entidades`.
		`idbancos_entidades` WHERE (`tesoreria_cajas_movimientos`.`idtesoreria_cajas_movimientos` !=0)	$ByFecha $ByUsuario2 $ByTipoExp";
								
				break;
			default:
			$sqlTi	= "SELECT
			`tesoreria_cajas_movimientos`.`tipo_de_exposicion` AS 'forma_de_pago',
			`tesoreria_cajas_movimientos`.`fecha`,
			`tesoreria_cajas_movimientos`.`tipo_de_exposicion`,
			/*`tesoreria_cajas_movimientos`.`numero_de_cheque` ,*/
			(`tesoreria_cajas_movimientos`.`monto_del_movimiento`) AS `operacion`,
			(`tesoreria_cajas_movimientos`.`monto_recibido`)       AS `recibido`,
			(`tesoreria_cajas_movimientos`.`monto_en_cambio`)      AS `cambio`
			FROM `tesoreria_cajas_movimientos` WHERE (`tesoreria_cajas_movimientos`.`idtesoreria_cajas_movimientos` !=0)	$ByFecha $ByUsuario2 $ByTipoExp";
			break;
		}
		return $sqlTi;
	}	
	
	function getListadoResumenTesoreria($cajero = "", $fecha_inicial = "", $fecha_final = "" ){
		$ByUsuario2			= ($cajero == SYS_TODAS OR $cajero == "") ? "" : " AND (`tesoreria_cajas_movimientos`.`idusuario` = $cajero) ";
		$ByFecha			= ($fecha_inicial == "" ) ? "" : " AND (`tesoreria_cajas_movimientos`.`fecha` >='$fecha_inicial') ";
		$ByFecha			.= ($fecha_final == "" ) ? "" : " AND (`tesoreria_cajas_movimientos`.`fecha` <='$fecha_final')";
		$sqlTi	= "SELECT
		`tesoreria_cajas_movimientos`.`tipo_de_exposicion` AS 'forma_de_pago',
		`tesoreria_cajas_movimientos`.`fecha`,
		SUM((`tesoreria_cajas_movimientos`.`monto_del_movimiento`*`tipo_de_movimiento`)) AS `operacion`,
		SUM((`tesoreria_cajas_movimientos`.`monto_recibido`*`tipo_de_movimiento`))       AS `recibido`,
		SUM((`tesoreria_cajas_movimientos`.`monto_en_cambio`*`tipo_de_movimiento`))      AS `cambio`
		FROM 
		`tesoreria_cajas_movimientos`
		WHERE
			(`tesoreria_cajas_movimientos`.`idtesoreria_cajas_movimientos` !=0)
			$ByFecha
			$ByUsuario2
		GROUP BY
		`tesoreria_cajas_movimientos`.`tipo_de_exposicion`,
		`tesoreria_cajas_movimientos`.`fecha` ";
				
		return $sqlTi;
	}
	function getListadoDeCajaEnBanco($tipo = "", $cuenta = "", $cajero = "", $fecha_inicial = "", $fecha_final = ""){
		$ByUsuario		= ($cajero == SYS_TODAS OR $cajero == "") ? "" : " AND (`bancos_operaciones`.`idusuario`  =$cajero) ";
		$ByTipo			= ($tipo == "" OR $tipo == SYS_TODAS) ? "" : " AND	(`bancos_operaciones`.`tipo_operacion` = '" . $tipo . "')  ";	
		$ByFecha		= ($fecha_inicial == "") ? "" : " AND (`bancos_operaciones`.`fecha_expedicion` >='$fecha_inicial') ";
		$ByFecha		.= ($fecha_final == "") ? "" : " AND (`bancos_operaciones`.`fecha_expedicion` <='$fecha_final') ";
		$sqlTO	= "SELECT
		`bancos_operaciones`.`tipo_operacion`   AS `tipo`,
		`bancos_operaciones`.`cuenta_bancaria`,
		`bancos_operaciones`.`recibo_relacionado` AS `recibo`,
		`bancos_operaciones`.`beneficiario` ,
		`bancos_operaciones`.`monto_real`  AS `monto`
		FROM
		`bancos_operaciones` `bancos_operaciones`
		WHERE (`bancos_operaciones`.`idcontrol` !=0)
		$ByFecha
		$ByUsuario
		$ByTipo
		ORDER BY
			`bancos_operaciones`.`recibo_relacionado`	, `bancos_operaciones`.`monto_real`
		/*GROUP BY
			`bancos_operaciones`.`cuenta_bancaria`
			`bancos_operaciones`.`fecha_expedicion`,
			`bancos_operaciones`.`tipo_operacion` */";
		
		return $sqlTO;
	}
	function getListadoResumenOperaciones($fecha_inicial = "", $fecha_final = "", $cajero = "", $tipo_de_cobro = ""){
		$ByFecha		= ($fecha_inicial == "") ? "" : " AND (operaciones_recibos.fecha_operacion>='$fecha_inicial')  ";
		$ByFecha		.= ($fecha_final == "") ? "" : "AND (operaciones_recibos.fecha_operacion<='$fecha_final')  ";
		$ByUsuario		= ($cajero == SYS_TODAS OR $cajero == "") ? "" : " AND operaciones_recibos.idusuario=$cajero ";
		$ByTipoCobro	= ($tipo_de_cobro == "" OR $tipo_de_cobro == SYS_TODAS) ? "" : " AND (`operaciones_recibos`.`tipo_pago` ='" . $tipo_de_cobro .  "') ";
		$sqlEmp			= "SELECT
				COUNT(`operaciones_recibos`.`idoperaciones_recibos`) AS 'recibo',
				'' AS `tipo`,
					
				'' AS 'documento',
				`socios`.`iddependencia` AS `persona`,
					`socios`.`dependencia` AS 'nombre',
				SUM((`operaciones_recibos`.`total_operacion`*`tesoreria_tipos_de_pago`.`tipo_de_movimiento`)) AS 'total',
				'' AS 'observacion'
					
					FROM
	`operaciones_recibos` `operaciones_recibos` 
		INNER JOIN `socios` `socios` 
		ON `operaciones_recibos`.`numero_socio` = `socios`.`codigo` 
			INNER JOIN `operaciones_recibostipo` `operaciones_recibostipo` 
			ON `operaciones_recibos`.`tipo_docto` = `operaciones_recibostipo`.
			`idoperaciones_recibostipo` 
				LEFT OUTER JOIN `tesoreria_tipos_de_pago` 
				`tesoreria_tipos_de_pago` 
				ON `operaciones_recibos`.`tipo_pago` = `tesoreria_tipos_de_pago`
				.`tipo_de_pago`
				
				WHERE operaciones_recibostipo.mostrar_en_corte!='0'
						AND (`socios`.`iddependencia` !=" . DEFAULT_EMPRESA . " )
								$ByFecha
								$ByUsuario
								$ByTipoCobro
								GROUP BY `socios`.`iddependencia`
					
								UNION";
		if(PERSONAS_CONTROLAR_POR_EMPRESA == false){$sqlEmp == "";}
		$sql = "$sqlEmp
						SELECT
								`operaciones_recibos`.`idoperaciones_recibos` AS 'recibo',
								`operaciones_recibostipo`.`descripcion_recibostipo` AS `tipo`,
					
								/*`operaciones_recibos`.`recibo_fiscal`,*/
					
					
								`operaciones_recibos`.`docto_afectado` AS 'documento',
								`operaciones_recibos`.`numero_socio`,
								`socios`.`nombre`,
					
								(`operaciones_recibos`.`total_operacion`*`tesoreria_tipos_de_pago`.`tipo_de_movimiento`) AS 'total',
								`operaciones_recibos`.`observacion_recibo` AS 'observacion'
					
								FROM
	`operaciones_recibos` `operaciones_recibos` 
		INNER JOIN `socios` `socios` 
		ON `operaciones_recibos`.`numero_socio` = `socios`.`codigo` 
			INNER JOIN `operaciones_recibostipo` `operaciones_recibostipo` 
			ON `operaciones_recibos`.`tipo_docto` = `operaciones_recibostipo`.
			`idoperaciones_recibostipo` 
				LEFT OUTER JOIN `tesoreria_tipos_de_pago` 
				`tesoreria_tipos_de_pago` 
				ON `operaciones_recibos`.`tipo_pago` = `tesoreria_tipos_de_pago`
				.`tipo_de_pago`
								
								WHERE operaciones_recibostipo.mostrar_en_corte!='0'
								AND (`socios`.`iddependencia` =" . DEFAULT_EMPRESA . " )
								$ByFecha
								$ByUsuario
								$ByTipoCobro
					ORDER BY recibo
					";
		return $sql;
	}
	function getListadoDeProgramacionAlertas($tipo_de_programacion = "", $programacion = ""){
		$ByTipo	= ($tipo_de_programacion == "" OR $tipo_de_programacion == SYS_TODAS) ? "" : " AND  (`sistema_programacion_de_avisos`.`forma_de_creacion` ='$tipo_de_programacion') ";
		$ByPro	= ($programacion == "" OR $programacion == SYS_TODAS) ? "" : " AND	(`sistema_programacion_de_avisos`.`programacion` ='$programacion') ";
		$sql	= " SELECT * FROM	`sistema_programacion_de_avisos` `sistema_programacion_de_avisos` WHERE (`sistema_programacion_de_avisos`.`idprograma` !=0) $ByTipo $ByPro ";
		return $sql;
	}
	
	function getListadoDeIncidenciasAhorro($empresa){
		$sql			= "SELECT
		`socios_general`.`codigo`,
		CONCAT(`socios_general`.`nombrecompleto`, ' ',
		`socios_general`.`apellidopaterno`, ' ', 
		`socios_general`.`apellidomaterno`) AS 'nombre_completo',
		`socios_general`.`descuento_preferente` AS `ahorro`
		FROM
		`socios_general` `socios_general`
		WHERE
		(`socios_general`.`descuento_preferente` >0)
		AND
		(`socios_general`.`dependencia` = $empresa)";
		return $sql;		
	}
	function getListadoDeCompromisosSimple($persona = false, $credito = false, $oficial = "", $estado = "pendiente"){
		$oficial	= setNoMenorQueCero($oficial);
		$persona	= setNoMenorQueCero($persona);
		$credito	= setNoMenorQueCero($credito);
		$ByOficial	= ($oficial <= 0) ? "" : " AND	(`seguimiento_compromisos`.`oficial_de_seguimiento` = $oficial) ";
		$ByPersona	= ($persona <= DEFAULT_SOCIO) ? "" : " AND (`seguimiento_compromisos`.`socio_comprometido` = $persona) ";
		$ByCredito	= ($credito <= DEFAULT_CREDITO) ? "" : " AND (`seguimiento_compromisos`.`credito_comprometido` = $credito) ";
		$ByEstado	= ($estado == SYS_TODAS OR $estado == "") ? "" : " AND (`seguimiento_compromisos`.`estatus_compromiso` ='$estado') "; 
		$sql = "SELECT
			`seguimiento_compromisos`.`idseguimiento_compromisos` AS `control`,
			`seguimiento_compromisos`.`socio_comprometido`        AS `socio`,
			`socios`.`nombre`,
			`seguimiento_compromisos`.`credito_comprometido`      AS `credito`,
			`seguimiento_compromisos`.`tipo_compromiso`           AS `tipo`,
			`seguimiento_compromisos`.`fecha_vencimiento`         AS `fecha`,
			`seguimiento_compromisos`.`hora_vencimiento`          AS `hora`
		FROM
			`seguimiento_compromisos` `seguimiento_compromisos`
				INNER JOIN `socios` `socios`
				ON `seguimiento_compromisos`.`socio_comprometido` = `socios`.`codigo`
		WHERE
			`seguimiento_compromisos`.`idseguimiento_compromisos` > 0
			$ByEstado $ByOficial $ByPersona $ByCredito	
		ORDER BY
			`seguimiento_compromisos`.`fecha_vencimiento`,
			`seguimiento_compromisos`.`hora_vencimiento`,
			`seguimiento_compromisos`.`socio_comprometido`
			";
		
		return $sql;
	}

	function getListadoDePolizasContables($fecha = false, $tipo = false, $ejercicio = false, $periodo = false, $numero = false, $otros = ""){
		$ByFecha		= ($fecha == false OR $fecha == SYS_TODAS) ? "" : " AND (`contable_polizas`.`fecha` = '$fecha' ) ";
		$ByEjercicio	= ($ejercicio == false OR $ejercicio == SYS_TODAS) ? "" : " AND (`contable_polizas`.`ejercicio` = $ejercicio) ";
		$ByPeriodo		= ($periodo == false OR $periodo == SYS_TODAS) ? "" : " AND (`contable_polizas`.`periodo` =$periodo) ";
		$ByTipo			= ($tipo == false OR $tipo == SYS_TODAS) ? "" : " AND (`contable_polizas`.`tipopoliza` =$tipo) ";
		$ByNumero		= ($numero == false ) ? "" : " AND (`contable_polizas`.`numeropoliza` = $numero) ";
		$sql	= "SELECT
			`contable_polizas`.`numeropoliza`                 AS `numero`,
			`contable_polizasdiarios`.`nombre_del_diario`     AS `tipo`,
			`contable_polizas`.`fecha`,
			`contable_polizas`.`cargos`,
			`contable_polizas`.`abonos`,
			`contable_centrodecostos`.`nombre_centrodecostos` AS `centro_de_costos`,
			`contable_polizas`.`concepto`,
	
			CONCAT(	`contable_polizas`.`ejercicio`, '.',
			`contable_polizas`.`periodo`, '.',
			`contable_polizas`.`tipopoliza`, '.',
			`contable_polizas`.`numeropoliza`) AS 'codigo'
				
		FROM
			`contable_centrodecostos` `contable_centrodecostos` 
				INNER JOIN `contable_polizas` `contable_polizas` 
				ON `contable_centrodecostos`.`idcontable_centrodecostos` = 
				`contable_polizas`.`diario` 
					INNER JOIN `contable_polizasdiarios` `contable_polizasdiarios` 
					ON `contable_polizasdiarios`.`idcontable_polizadiarios` = 
					`contable_polizas`.`tipopoliza` WHERE `contable_polizas`.`numeropoliza` != 0 $ByFecha $ByEjercicio $ByPeriodo $ByTipo $ByNumero $otros ";
		return $sql;
	}
	function getListadoDePrepoliza($recibo = false){
		$sql	= "SELECT
			`contable_polizas_proforma`.`numero_de_recibo`,
			`contable_polizas_proforma`.`idcontable_polizas_proforma` AS `clave`,
			`contable_polizas_proforma`.`tipo_de_mvto`                AS `operacion`,
			`contable_polizas_proforma`.`monto`,
			`contable_polizas_proforma`.`socio`                       AS `persona`,
			`contable_polizas_proforma`.`documento`,
			`contable_polizas_proforma`.`banco`,
			`contable_polizas_proforma`.`idusuario`                   AS `usuario`,
			`contable_polizas_proforma`.`sucursal`,
			`contable_polizas_proforma`.`contable_operacion`          AS `asiento` 
		FROM
			`contable_polizas_proforma`
		WHERE
			(`contable_polizas_proforma`.`numero_de_recibo` = $recibo)";
		return $sql;
	}
	function getListadoDeMovimientosContables($codigo_unico = "", $tipo = "", $numero = "", $fecha = ""){
		$ejercicio		= EJERCICIO_CONTABLE;
		$periodo		= EACP_PER_CONTABLE;
		if($codigo_unico != ""){
			$d			= explode(".", $codigo_unico);
			$ejercicio	= setNoMenorQueCero($d[0]);
			$periodo	= setNoMenorQueCero($d[1]);
			$tipo		= setNoMenorQueCero($d[2]);
			$numero		= setNoMenorQueCero($d[3]);			
		}
		if($fecha != ""){
			$xF			= new cFecha(0, $fecha);
			$ejercicio	= $xF->anno();
			$periodo	= $xF->mes();
		}
		$sqlpm = "SELECT
		`contable_movimientos`.`numeromovimiento` AS 'operacion',
		`contable_movimientos`.`numerocuenta` AS 'cuenta',
		`contable_catalogo`.`nombre` AS 'nombre',
		`contable_movimientos`.`tipomovimiento` AS 'tipo',
		`contable_movimientos`.`referencia`,
		`contable_movimientos`.`importe`,
		`contable_movimientos`.`diario`,
		`contable_movimientos`.`concepto`
		
		FROM
		`contable_catalogo` `contable_catalogo`
		INNER JOIN `contable_movimientos` `contable_movimientos`
		ON `contable_catalogo`.`numero` = `contable_movimientos`.`numerocuenta`
		WHERE
		(`contable_movimientos`.`ejercicio` =$ejercicio) AND
		(`contable_movimientos`.`periodo` =$periodo) AND
		(`contable_movimientos`.`tipopoliza` =$tipo) AND
		(`contable_movimientos`.`numeropoliza` =$numero)
		ORDER BY
		`contable_movimientos`.`numeromovimiento` ";
		return $sqlpm;	
	}
	function getListadoDeMvtosDeCaptacion($persona = false, $cuenta = false, $tipo = false, $subtipo = false, $FechaInicial = false, $FechaFinal = false, $operacion = false){
		$xF			= new cFecha();
		$ByPersona	= ( setNoMenorQueCero($persona) <= 0) ? "" : " AND (`captacion_cuentas`.`numero_socio` =$persona) ";
		$ByCuenta	= (setNoMenorQueCero($cuenta) <= 0 OR $cuenta == DEFAULT_CUENTA_CORRIENTE ) ? "" : " AND (`captacion_cuentas`.`numero_cuenta` = $cuenta) ";
		$BySubtipo	= (setNoMenorQueCero($subtipo) <= 0) ? "" : " AND (`captacion_cuentas`.`tipo_subproducto` = $subtipo) ";
		$ByTipo		= (setNoMenorQueCero( $tipo) <= 0) ? "" : " AND	(`captacion_cuentas`.`tipo_cuenta` =$tipo ) ";
		$ByOpera	= (setNoMenorQueCero($operacion) <= 0) ? "" : " AND	(`operaciones_mvtos`.`tipo_operacion` = $operacion ) ";
		$ByFecha	= ($FechaInicial == false) ? "" : " AND (`operaciones_mvtos`.`fecha_afectacion` >='$FechaInicial')";
		$ByFecha	.= ($FechaFinal == false) ? "" : " AND	(`operaciones_mvtos`.`fecha_afectacion` <='$FechaFinal') ";  
		$selPers	= "";
		if($ByPersona == ""){
			$selPers= "		`socios_general`.`codigo`, CONCAT(`socios_general`.`apellidopaterno`, ' ',`socios_general`.`apellidomaterno`, ' ' ,`socios_general`.`nombrecompleto`) AS 'nombre', ";
		}
		/* `captacion_cuentas`.`saldo_cuenta`, `operaciones_mvtos`.`tipo_operacion`,*/
		$sql = " SELECT $selPers
		`captacion_cuentas`.`numero_cuenta`, `operaciones_tipos`.`descripcion_operacion` AS 'tipo_de_operacion', SUM(`operaciones_mvtos`.`afectacion_real`) AS 'monto'
		FROM `captacion_cuentas` `captacion_cuentas` INNER JOIN `operaciones_mvtos` `operaciones_mvtos` ON `captacion_cuentas`.`numero_cuenta` = `operaciones_mvtos`.
		`docto_afectado` INNER JOIN `operaciones_tipos` `operaciones_tipos` ON `operaciones_tipos`.`idoperaciones_tipos` = `operaciones_mvtos`.
		`tipo_operacion` INNER JOIN `socios_general` `socios_general` ON `captacion_cuentas`.`numero_socio` = `socios_general`.`codigo`
		WHERE `captacion_cuentas`.`numero_cuenta` > 0 $ByCuenta $ByPersona $BySubtipo $ByTipo $ByFecha $ByOpera
		GROUP BY `operaciones_mvtos`.`tipo_operacion`,	`operaciones_mvtos`.`docto_afectado` ORDER BY `operaciones_mvtos`.`tipo_operacion`,	`captacion_cuentas`.`numero_socio`,	`captacion_cuentas`.`numero_cuenta` ";
		return $sql;
	}
	function getListadoDeCuentasDeCapt($persona = false, $cuenta = false, $tipo = false, $subtipo = false){
		$tipo		= setNoMenorQueCero($tipo);
		//corregir equivalencias de sistema
		
		
		$ByPersona	= ( setNoMenorQueCero($persona) <= 0) ? "" : " AND (`captacion_cuentas`.`numero_socio` =$persona) ";
		$ByCuenta	= (setNoMenorQueCero($cuenta) <= 0 OR $cuenta == DEFAULT_CUENTA_CORRIENTE ) ? "" : " AND (`captacion_cuentas`.`numero_cuenta` = $cuenta) ";
		$BySubtipo	= (setNoMenorQueCero($subtipo) <= 0) ? "" : " AND (`captacion_cuentas`.`tipo_subproducto` = $subtipo) ";
		$ByTipo		= ($tipo <= 0) ? "" : " AND	(`captacion_cuentas`.`tipo_cuenta` =$tipo ) ";
		$from1		= "`captacion_cuentas` ";
		$from2		= "`captacion_subproductos` `captacion_subproductos` INNER JOIN `captacion_cuentas` `captacion_cuentas` ON `captacion_subproductos`.`idcaptacion_subproductos` = `captacion_cuentas`.`tipo_subproducto`";
		$from3		= "`captacion_cuentas` `captacion_cuentas` INNER JOIN `captacion_subproductos` `captacion_subproductos` ON `captacion_cuentas`.`tipo_subproducto` = `captacion_subproductos`.`idcaptacion_subproductos` INNER JOIN `captacion_cuentastipos` `captacion_cuentastipos` ON `captacion_cuentas`.`tipo_cuenta` = `captacion_cuentastipos`.`idcaptacion_cuentastipos` ";
		$selPers	= "";
		if($ByPersona == ""){
			$from1	= "	`captacion_cuentas` `captacion_cuentas`  INNER JOIN `socios` `socios` ON `captacion_cuentas`.`numero_socio` = `socios`.`codigo`";
			$from2	= "	`captacion_cuentas` `captacion_cuentas`	INNER JOIN `socios` `socios` ON `captacion_cuentas`.`numero_socio` = `socios`.`codigo` INNER JOIN `captacion_subproductos` `captacion_subproductos` ON `captacion_cuentas`.`tipo_subproducto` = `captacion_subproductos`.`idcaptacion_subproductos`";
			$from3	= " `captacion_cuentas` `captacion_cuentas` INNER JOIN `captacion_cuentastipos` `captacion_cuentastipos` ON `captacion_cuentas`.`tipo_cuenta` = `captacion_cuentastipos`.`idcaptacion_cuentastipos` INNER JOIN `socios` `socios` ON `captacion_cuentas`.`numero_socio` = `socios`.`codigo` INNER JOIN `captacion_subproductos` `captacion_subproductos` ON `captacion_cuentas`.`tipo_subproducto` = `captacion_subproductos`.`idcaptacion_subproductos`";
			$selPers= "	`socios`.`codigo`, `socios`.`nombre`, ";
		}
		switch ($tipo){
			case CAPTACION_TIPO_PLAZO:
				$sql	= 	$ssql = "SELECT $selPers
							`captacion_cuentas`.`numero_cuenta`        	AS `cuenta`,
							`captacion_cuentas`.`inversion_fecha_vcto` 	AS `vencimiento`,
							(`captacion_cuentas`.`tasa_otorgada` * 100) AS `tasa`,
							`captacion_cuentas`.`saldo_cuenta`         	AS `saldo`,
							`captacion_cuentas`.`dias_invertidos`      	AS `dias`
							 
						FROM
							$from1
						WHERE `captacion_cuentas`.`numero_cuenta` > 0	$ByPersona $ByCuenta $ByTipo $BySubtipo
						ORDER BY
							`captacion_cuentas`.`saldo_cuenta` DESC,
							`captacion_cuentas`.`inversion_fecha_vcto` DESC
							";
				break;
			case CAPTACION_TIPO_VISTA:
				$sql	= "SELECT $selPers
						`captacion_cuentas`.`numero_cuenta`                 AS `cuenta`,
						`captacion_subproductos`.`descripcion_subproductos` AS `subproducto`,
						(`captacion_cuentas`.`tasa_otorgada` * 100)         AS `tasa`,
						`captacion_cuentas`.`saldo_cuenta`                  AS `saldo`
						 
					FROM
						$from2
					WHERE `captacion_cuentas`.`numero_cuenta` > 0	$ByPersona $ByCuenta $ByTipo $BySubtipo 
					ORDER BY `captacion_cuentas`.`saldo_cuenta` DESC, `captacion_cuentas`.`fecha_apertura` DESC";
				break;
			default:
				$sql = "SELECT $selPers
				`captacion_cuentas`.`numero_cuenta`                 AS `cuenta`,
				`captacion_cuentastipos`.`descripcion_cuentastipos` AS `tipo`,
				`captacion_subproductos`.`descripcion_subproductos` AS `subproducto`,
				(`captacion_cuentas`.`tasa_otorgada` * 100)         AS `tasa`,
				`captacion_cuentas`.`saldo_cuenta`                  AS `saldo`,
				/*`captacion_cuentas`.`numero_socio`,*/
				`captacion_cuentas`.`numero_grupo`                  AS `grupo`,
				`captacion_cuentas`.`numero_solicitud`              AS `credito`,
				`captacion_cuentas`.`observacion_cuenta`            AS `observaciones`
			FROM
				$from3
				WHERE `captacion_cuentas`.`numero_cuenta` > 0	$ByPersona $ByCuenta $ByTipo $BySubtipo
				
				ORDER BY `captacion_cuentas`.`saldo_cuenta` DESC, `captacion_cuentas`.`fecha_apertura` DESC
				";
				break;
		}
		return $sql;
	}
	/**
	 * @param integer $empresa		Clave de Empresa
	 * @param integer $tipo			Tipo.- Ingresos 1, Pagos -1
	 * @param boolean $ConActivos	Activo.- Con saldo Actual
	 * @param string $EnvioFI		Envio.- Fecha Inicial
	 * @param string $EnvioFF		Envio.- Fecha Final
	 * @param string $CobroFI		Cobro.- Fecha Inicial
	 * @param string $CobroFF		Cobro.- Fecha Final
	 * @return string SQL
	 */
	function getListadoDePeriodoPorEmpresa($empresa, $tipo = false, $ConActivos = false, $EnvioFI = false, $EnvioFF = false, $CobroFI = false, $CobroFF = false){
		$xF		= new cFecha();
		$anno	= $xF->anno();
		$ByEmpresa	= $this->OFiltro()->PeriodosEmpresaPorEmpresa($empresa);
		$ByFechasC	= $this->OFiltro()->PeriodosEmpresaPorFechaCobro($CobroFI, $CobroFI);
		$ByFechasE	= $this->OFiltro()->PeriodosEmpresaPorFechaEnvio($EnvioFI, $EnvioFF);
		$InicialAnnio	= "$anno-01-01";
		if($xF->mes() == 1 OR ($xF->mes() == 2)){
			$anno			= ($anno-1);
			$InicialAnnio	= "$anno-06-01";
		}
		$DEmpresa	= ($ByEmpresa == "") ? "  `empresas_operaciones`.`clave_de_empresa`, `socios_aeconomica_dependencias`.`nombre_corto` AS `nombre`, " : "";
		
		$SActivos	= ($ConActivos == false) ? "": ", getNominaMontoAct(`empresas_operaciones`.`idempresas_operaciones`) AS `saldo_activo`";
		$ByTipo		= ($tipo === false) ? "": " AND (`tipo_de_operacion` = $tipo ) ";
		$sql	= "SELECT
			`empresas_operaciones`.`idempresas_operaciones` AS 'codigo',
			$DEmpresa
			`empresas_operaciones`.`periodo_marcado`                 AS `periodo`,
			`creditos_periocidadpagos`.`descripcion_periocidadpagos` AS `nombre_periocidad`,
			SUM(`empresas_operaciones`.`monto` * `empresas_operaciones`.`tipo_de_operacion`)                      AS `saldo`,
			MIN( IF( ISNULL(`fecha_inicial`), `fecha_de_operacion`, `fecha_inicial`))              AS `fecha_inicial`,
			MAX( IF( ISNULL(`fecha_final`), `fecha_de_operacion`, `fecha_final`) )                AS `fecha_final`,
			MAX(IF( ISNULL(`fecha_de_cobro`), `fecha_de_operacion`,`fecha_de_cobro`) )             AS `fecha_de_cobro`,
			 `empresas_operaciones`.`periocidad`
			$SActivos
			FROM     `empresas_operaciones` 
			INNER JOIN `creditos_periocidadpagos`  ON `empresas_operaciones`.`periocidad` = `creditos_periocidadpagos`.`idcreditos_periocidadpagos` 
			INNER JOIN `socios_aeconomica_dependencias`  ON `empresas_operaciones`.`clave_de_empresa` = `socios_aeconomica_dependencias`.`idsocios_aeconomica_dependencias` 
		WHERE 
			(`empresas_operaciones`.`fecha_de_operacion` >= '$InicialAnnio')
			
			AND (`empresas_operaciones`.`fecha_final` >= '2016-01-01')
			AND (`empresas_operaciones`.`fecha_inicial` >= '2016-01-01')
			
			$ByEmpresa $ByFechasC $ByFechasE $ByTipo
			GROUP BY
				`empresas_operaciones`.`periodo_marcado`,
				`empresas_operaciones`.`clave_de_empresa`,
				`empresas_operaciones`.`periocidad`
		 ORDER BY `fecha_de_cobro` DESC ";
		//setLog($sql);
		return $sql;
	}

	function getListadoDeCobranza($periodo, $estado = SYS_TODAS, $CamposExt = ""){
		$ByEstado	= ($estado == SYS_TODAS) ? "": " AND `empresas_cobranza`.`estado` = $estado ";
		$periodo	= setNoMenorQueCero($periodo);
		$sql	= "SELECT
		creditos_solicitud.numero_socio AS 'persona',
		CONCAT(
		socios_general.nombrecompleto, ' ',
		socios_general.apellidopaterno, ' ',
		socios_general.apellidomaterno,	(CASE WHEN (socios_general.dependencia != creditos_solicitud.persona_asociada) THEN '**' ELSE '' END )
		) AS 'nombre',
		
		creditos_solicitud.numero_solicitud AS 'credito',
		
		
		`empresas_cobranza`.`parcialidad`   AS `letra`,
		`creditos_solicitud`.`pagos_autorizados`  AS 'pagos',
		`empresas_cobranza`.`saldo_inicial`,
		`empresas_cobranza`.`monto_enviado` AS `monto`,
		(`empresas_cobranza`.`saldo_inicial` - `empresas_cobranza`.`monto_enviado`) AS 'saldo_final',
		`empresas_cobranza`.`observaciones`
		$CamposExt
		FROM
		`creditos_solicitud` `creditos_solicitud`
		INNER JOIN `socios_general` `socios_general`
		ON `creditos_solicitud`.`numero_socio` = `socios_general`.`codigo`
		INNER JOIN `empresas_cobranza` `empresas_cobranza`
		ON `creditos_solicitud`.`numero_solicitud` = `empresas_cobranza`.
		`clave_de_credito`
		WHERE
		(`empresas_cobranza`.`clave_de_nomina` =$periodo) $ByEstado
		ORDER BY `empresas_cobranza`.`parcialidad` DESC
		";
		//setLog($sql);
		return $sql;		
	}
	function getListadoDePersonasDoctos($persona, $activos =false){
		
		$ByActivos = ($activos == true) ? " AND(`personas_documentacion`.`estatus` = 1) " : "";
		
		$sql	= "SELECT
			`personas_documentacion`.`clave_de_control` AS `clave`,
					`personas_documentacion_tipos`.`nombre_del_documento` AS `tipo`,
					getFechaByint(`personas_documentacion`.`fecha_de_carga`) AS `fecha_de_carga`,
					`personas_documentacion`.`archivo_de_documento`,
					`personas_documentacion`.`numero_de_pagina` AS `clave_documento`,
					`personas_documentacion`.`observaciones`,
					getBooleanMX(`personas_documentacion`.`estatus`) AS `estatusactivo`,
					getBooleanMX(IF(`personas_documentacion`.`fecha_de_verificacion`<=0,0,1)) AS `verificado`
					 
				FROM
					`personas_documentacion` `personas_documentacion` 
						INNER JOIN `personas_documentacion_tipos` `personas_documentacion_tipos` 
						ON `personas_documentacion`.`tipo_de_documento` = 
						`personas_documentacion_tipos`.`clave_de_control`
				 WHERE
				(`personas_documentacion`.`clave_de_persona` = $persona) $ByActivos";
		return $sql;
	}
	
	function getListadoDeCuentasContables($Cuenta = "", $Nivel = 0, $TipoCuentas = "" , $afectables = "", $NivelFinal = 0, $init = false, $end = false){
		//$afectables		= ($afectables == true) ? 1 : $afectables;
		$xT				= new cTipos();
		$ByLimit		= ($init == false AND $end == false) ? "" : " LIMIT $init, $end";
		$ByTipoCta 		= ($TipoCuentas == "" OR $TipoCuentas == SYS_TODAS) ? "" : " AND `contable_catalogo`.`tipo`='$TipoCuentas' ";
		$ByNivel		= (setNoMenorQueCero($Nivel) <= 0) ? "" : " AND `contable_catalogo`.`ctamayor`=$Nivel ";
		//$ByAfecta		= ($afectables == "" OR $afectables == SYS_TODAS OR $afectables == false) ? "" : " AND (`contable_catalogo`.`afectable` = $afectables) ";
		$ByAfecta		= ($xT->cBool($afectables) == true) ? " AND (`contable_catalogo`.`afectable` = $afectables) " : "";
		//var_dump($afectables);
		$ByCuenta		= "";
		
		if($Nivel >= 0 AND $NivelFinal > 0){
			$ByNivel	= " AND (`contable_catalogo`.`ctamayor`>=$Nivel AND `contable_catalogo`.`ctamayor`<=$NivelFinal ) ";
		}
		if(setNoMenorQueCero($Cuenta) >= 1 ){
			$xCta			= new cCuentaContableEsquema($Cuenta);
			$pcta			= $xCta->CUENTARAW;
			$ByCuenta 		= " AND `contable_catalogo`.`numero` LIKE '$pcta%' ";
		}
		$setSql = "SELECT
		`contable_catalogo`.`digitoagrupador`             AS
		`niv`,
		setCuentaFmt(`contable_catalogo`.`numero`)		AS 'clave',
		`contable_catalogo`.`nombre`,
		`contable_catalogotipos`.`nombre_del_tipo`        AS
		`tipo`
		FROM
		`contable_centrodecostos` `contable_centrodecostos`
		INNER JOIN `contable_catalogo` `contable_catalogo`
		ON `contable_centrodecostos`.
		`idcontable_centrodecostos` = `contable_catalogo`.
		`centro_de_costo`
		INNER JOIN `contable_catalogotipos`
		`contable_catalogotipos`
		ON `contable_catalogotipos`.
		`idcontable_catalogotipos` = `contable_catalogo`
		.`tipo`
		WHERE `contable_catalogo`.`numero` != 0
		$ByTipoCta
		$ByNivel
		$ByCuenta
		$ByAfecta
		ORDER BY `contable_catalogo`.`numero`
		$ByLimit ";
		return $setSql;		
	}
	
	function getListadoDeEventos($fechaInicial = "", $fechaFinal = "", $nivel = "", $codigo = "", $usuario = "", $buscar = ""){
				
		$ByLike		= ($buscar == "" OR $buscar == SYS_TODAS) ? "" :" AND `general_log`.`text_log` LIKE '%$buscar%' ";
		$ByUsuario 	= ($usuario == "" OR $usuario == SYS_TODAS) ? "" :" AND `general_log`.`usr_log` = '$usuario' ";
		$ByCodigo 	= ($codigo == "" OR $codigo == SYS_TODAS) ? "" :" AND `general_log`.`type_error` = '$codigo' ";
		$ByNivel 	= ($nivel == "" OR $nivel == SYS_TODAS) ? "" :" AND (`general_error_codigos`.`type_err` = '$nivel') ";
		$ByFecha	= "";
		if($fechaInicial != "" AND $fechaFinal != ""){
			$ByFecha		= " AND (`general_log`.`fecha_log` >='$fechaInicial') AND	(`general_log`.`fecha_log` <='$fechaFinal') ";
		} else {
			if($fechaFinal != ""){
				$ByFecha	= " AND (`general_log`.`fecha_log` <='$fechaFinal') ";
			}
		}
		$setSql = " SELECT
		`idgeneral_log` AS 'clave',
		`general_log`.`fecha_log`            AS `fecha`,
		`general_log`.`hour_log`             AS `hora`,
		`general_error_codigos`.`description_error` AS `Descripcion`,
		getUserByID(`general_log`.`usr_log`)        AS `usuario`,
		LEFT(`general_log`.`text_log`,200)             AS `texto`,
		`general_error_codigos`.`type_err`	AS `tipo`
		FROM
		`general_error_codigos` `general_error_codigos`
		INNER JOIN `general_log` `general_log`
		ON `general_error_codigos`.`idgeneral_error_codigos` =
		`general_log`.`type_error`
		WHERE `idgeneral_log` > 0
		$ByFecha 
		$ByNivel
		$ByCodigo
		$ByUsuario
		$ByLike
ORDER BY
	`general_log`.`fecha_log` DESC,
	`general_log`.`hour_log`,
	`general_log`.`type_error`
			
		LIMIT 0,100
		";
		return $setSql;	
	}

	function getListadoDeRecibosEmitidos($persona = "", $fechaInicial = false, $fechaFinal = false){
		$ByPersona	= (setNoMenorQueCero($persona) > 0) ? " AND ( `operaciones_recibos`.`numero_socio` = $persona) " : "";
		$ByFecha	= ($fechaInicial == false) ? "" : " AND (`operaciones_recibos`.`fecha_operacion`='$fechaInicial') ";
		$setSql = "SELECT
		`usuarios`.`nombreusuario`                          AS `usuario`,
		`operaciones_recibos`.`idoperaciones_recibos`       AS `numero`,
		`operaciones_recibos`.`fecha_operacion`             AS `fecha`,
		`operaciones_recibos`.`numero_socio`                AS `socio`,
		`operaciones_recibos`.`docto_afectado`              AS `documento`,
		`operaciones_recibostipo`.`descripcion_recibostipo` AS `tipo`,
		`operaciones_recibos`.`cheque_afectador`            AS `cheque`,
		`operaciones_recibos`.`tipo_pago`                   AS `forma_de_pago`,
		`operaciones_recibos`.`recibo_fiscal`,
		`operaciones_recibos`.`sucursal` ,
		`operaciones_recibos`.`total_operacion`             AS `total`
		FROM
		`operaciones_recibos` `operaciones_recibos`
		INNER JOIN `usuarios` `usuarios`
		ON `operaciones_recibos`.`idusuario` = `usuarios`.`idusuarios`
		INNER JOIN `operaciones_recibostipo` `operaciones_recibostipo`
		ON `operaciones_recibos`.`tipo_docto` = `operaciones_recibostipo`.
		`idoperaciones_recibostipo`
			
		WHERE `operaciones_recibos`.`idoperaciones_recibos` > 0
		$ByFecha
		$ByPersona
		ORDER BY
		`operaciones_recibos`.`fecha_operacion`,
		`usuarios`.`idusuarios`,
		`operaciones_recibos`.`tipo_pago`,
		operaciones_recibos.tipo_docto
		";
		return $setSql;		
	}
	function getListadoDeRelaciones($persona = "", $documento = false, $tipo_de_relacion = false, $consanguinidad = false, $otros = ""){
		$ByPersona	= (setNoMenorQueCero($persona) > 0) ? "AND (`socios_relaciones`.`socio_relacionado` =$persona) " : "";
		$ByRelacion	= (setNoMenorQueCero($tipo_de_relacion) > 0 ) ? " AND (`socios_relaciones`.`tipo_relacion` =$tipo_de_relacion) " : "";
		$ByConsang	= (setNoMenorQueCero($consanguinidad) > 0) ? " AND (`socios_relaciones`.`consanguinidad` =$consanguinidad) " : "";
		$ByDocto	= (setNoMenorQueCero($documento) > 0) ? " AND (`socios_relaciones`.`credito_relacionado` =$documento) " : "";
		$sql		= "SELECT
			idsocios_relaciones AS 'clave',
			`socios_relacionestipos`.`descripcion_relacionestipos` AS `tipo_de_relacion`,
			`socios_consanguinidad`.`descripcion_consanguinidad`   AS `consanguinidad`,
			`socios_relaciones`.`numero_socio`                     AS `clave_de_persona`,
			CONCAT(`socios_relaciones`.`nombres`, ' ',                          
			`socios_relaciones`.`apellido_paterno`,' ',
			`socios_relaciones`.`apellido_materno`)					AS `nombre_completo`,
			`socios_relaciones`.`curp`,
			`socios_relaciones`.`fecha_nacimiento`                 AS 
			`fecha_de_nacimiento`,
			CONCAT(`socios_relaciones`.`telefono_movil`, '/',
			`socios_relaciones`.`telefono_residencia`)              AS `telefonos`,
			
			`socios_relaciones`.`ocupacion`,
			`socios_relaciones`.`domicilio_completo`               AS `domicilio` 
		FROM
			`socios_relaciones` `socios_relaciones` 
				INNER JOIN `socios_relacionestipos` `socios_relacionestipos` 
				ON `socios_relaciones`.`tipo_relacion` = `socios_relacionestipos`.
				`idsocios_relacionestipos` 
					INNER JOIN `socios_consanguinidad` `socios_consanguinidad` 
					ON `socios_relaciones`.`consanguinidad` = `socios_consanguinidad`.
					`idsocios_consanguinidad`
			WHERE
			(`socios_relaciones`.`idsocios_relaciones` >0) $ByPersona $ByRelacion $ByConsang $ByDocto $otros";
		//setLog($sql);
		return $sql;
	}
	function getListadoDeRelacionesInversa($persona){
		$sql	= "SELECT
		`socios_relaciones`.`numero_socio`,
		`socios_relacionestipos`.`descripcion_relacionestipos` AS `tipo_de_relacion`
		,
		`socios_consanguinidad`.`descripcion_consanguinidad`   AS `parentesco`,
		`socios_relaciones`.`socio_relacionado`                AS `clave_de_persona`
		,
		`socios`.`nombre` 
	FROM
		`socios_relaciones` `socios_relaciones` 
			INNER JOIN `socios_relacionestipos` `socios_relacionestipos` 
			ON `socios_relaciones`.`tipo_relacion` = `socios_relacionestipos`.
			`idsocios_relacionestipos` 
				INNER JOIN `socios` `socios` 
				ON `socios_relaciones`.`socio_relacionado` = `socios`.`codigo` 
					INNER JOIN `socios_consanguinidad` `socios_consanguinidad` 
					ON `socios_relaciones`.`consanguinidad` = 
					`socios_consanguinidad`.`idsocios_consanguinidad` 
	WHERE
		(`socios_relaciones`.`numero_socio` =$persona) ";
		return $sql;	
	}
	function getListadoDeRelacionesPersonales($persona = "", $documento = false, $tipo_de_relacion = false, $consanguinidad = false, $otros = ""){
		return $this->getListadoDeRelaciones($persona, $documento, $tipo_de_relacion, $consanguinidad, $otros );
	}	
	function getListadoDeActividadesEconomicas($persona){
		$ByPersona	= (setNoMenorQueCero($persona) > 0) ? "AND	(`socios_aeconomica`.`socio_aeconomica` =$persona) " : "";
		
		$IDEmpresa	= (PERSONAS_CONTROLAR_POR_EMPRESA == false) ? "" : "`socios_aeconomica`.`dependencia_ae` AS		`clave_de_empresa`,";
		$sql	= "SELECT
		`socios_aeconomica`.`idsocios_aeconomica` AS 'control',
		`personas_actividad_economica_tipos`.`nombre_de_la_actividad`             AS `tipo`,
		/*`socios_aeconomica_sector`.`descripcion_aeconomica_sector` AS `sector`,*/
		$IDEmpresa
		`socios_aeconomica`.`nombre_ae`                            AS `nombre`,
		CONCAT(`socios_aeconomica`.`domicilio_ae`, ' ',
		`socios_aeconomica`.`localidad_ae`,' ',
		`socios_aeconomica`.`municipio_ae`,' ',
		`socios_aeconomica`.`estado_ae`)                            AS `domicilio`,
		CONCAT(`socios_aeconomica`.`telefono_ae`,'/',
		`socios_aeconomica`.`extension_ae`)                         AS `telefono`,
		`socios_aeconomica`.`puesto`,
		`socios_aeconomica`.`departamento_ae`                      AS `departamento`,
		`socios_aeconomica`.`monto_percibido_ae`                   AS `salario`
		FROM
	`socios_aeconomica` `socios_aeconomica` 
		LEFT OUTER JOIN `socios_aeconomica_sector` `socios_aeconomica_sector` 
		ON `socios_aeconomica`.`sector_economico` = `socios_aeconomica_sector`.
		`idsocios_aeconomica_sector` 
			LEFT OUTER JOIN `personas_actividad_economica_tipos` 
			`personas_actividad_economica_tipos` 
			ON `socios_aeconomica`.`tipo_aeconomica` = 
			`personas_actividad_economica_tipos`.`clave_interna`
			
		WHERE (`socios_aeconomica`.`idsocios_aeconomica` >0)	$ByPersona ";
		return $sql;
	}

	function getListadoDeGrupos($idgrupo = "", $other = "", $WhereOther = ""){
		$ByGrupo	= (setNoMenorQueCero($idgrupo) > 0) ? " AND (`socios_grupossolidarios`.`idsocios_grupossolidarios` = $idgrupo) " : "";
		$sql	= "SELECT
		`socios_grupossolidarios`.`idsocios_grupossolidarios`      AS `grupo`,
		`socios_grupossolidarios`.`nombre_gruposolidario`          AS `nombre`,
		`socios_grupossolidarios`.`representante_nombrecompleto`   AS 
		`representante`,
		`socios_grupossolidarios`.`vocalvigilancia_nombrecompleto` AS `vocal`,
		`socios_grupossolidarios`.`fecha_de_alta`                  AS `fecha`,
		`socios_grupossolidarios`.`clave_de_persona`               AS `persona` 
	FROM
		`socios_grupossolidarios` `socios_grupossolidarios`
		WHERE `socios_grupossolidarios`.`idsocios_grupossolidarios` > 0
				$ByGrupo $WhereOther ";
		
		return $sql;
	}
	function getListadoDeSDPMCredito($credito){
		$credito	= setNoMenorQueCero($credito);
		$sql		= "SELECT
		`creditos_sdpm_historico`.`numero_de_credito`,
		`creditos_sdpm_historico`.`fecha_actual`,
		`creditos_sdpm_historico`.`fecha_anterior`,
		`creditos_sdpm_historico`.`dias_transcurridos`,
		`creditos_sdpm_historico`.`monto_calculado`,
		`creditos_sdpm_historico`.`saldo`,
		`creditos_sdpm_historico`.`estatus`,
		`creditos_sdpm_historico`.`interes_normal`,
		`creditos_sdpm_historico`.`interes_moratorio`,
		`creditos_sdpm_historico`.`tipo_de_operacion`,
		`creditos_sdpm_historico`.`periodo`
		FROM
		`creditos_sdpm_historico` `creditos_sdpm_historico`
		WHERE
		(`creditos_sdpm_historico`.`numero_de_credito` =$credito) ORDER BY
		`creditos_sdpm_historico`.`fecha_actual` ";
		return $sql;
	}
	function getListadoDeSDPMCaptacion($cuenta){
		$cuenta		= setNoMenorQueCero($cuenta);
		$sql		= "SELECT
	`captacion_sdpm_historico`.`idcaptacion_sdpm_historico` AS `clave`,
	`captacion_sdpm_historico`.`numero_de_socio`,
	`captacion_sdpm_historico`.`cuenta`,
	`captacion_sdpm_historico`.`recibo`,
	`captacion_sdpm_historico`.`ejercicio`,
	`captacion_sdpm_historico`.`periodo`,
	`captacion_sdpm_historico`.`fecha`,
	`captacion_sdpm_historico`.`dias`,
	`captacion_sdpm_historico`.`tasa`,
	`captacion_sdpm_historico`.`monto` 
FROM
	`captacion_sdpm_historico` `captacion_sdpm_historico` 
WHERE
	(`captacion_sdpm_historico`.`cuenta` =$cuenta) 
ORDER BY
	`captacion_sdpm_historico`.`cuenta`,
	`captacion_sdpm_historico`.`fecha` " . $this->mOrderASC . " ";
		return $sql;
	}	
	function getListadoDeCreditosOtrosDatos($credito, $clave = ""){
		$ByTipo	= ($clave == "" OR $clave == SYS_TODAS) ? "" : " AND (`creditos_otros_datos`.`clave_de_parametro` ='') ";
		$sql	= "SELECT 	`creditos_otros_datos`.`idcreditos_otros_datos`   AS `consecutivo`,
					getTrad(`creditos_otros_datos`.`clave_de_parametro`)       AS `clave`,
					`creditos_otros_datos`.`valor_de_parametro`       AS `valor`,
					`creditos_otros_datos`.`descripcion_de_parametro` AS `observaciones` 
					 FROM `creditos_otros_datos` WHERE
				(`creditos_otros_datos`.`clave_de_credito` ='$credito') $ByTipo ";
		return $sql;
	}
	function getInicialDeRecibos($recibo, $persona = false){
		$BySocio	= ( setNoMenorQueCero( $persona) <= 0 OR $persona == DEFAULT_SOCIO  ) ? "" : " AND (`operaciones_recibos`.`numero_socio` = " . $persona . ") ";
		$sql	= "SELECT		`operaciones_recibos`.*,		`operaciones_recibostipo`.*
		FROM		`operaciones_recibos` `operaciones_recibos`		INNER JOIN `operaciones_recibostipo` `operaciones_recibostipo`		ON `operaciones_recibos`.`tipo_docto` = `operaciones_recibostipo`.`idoperaciones_recibostipo`
		WHERE		(`operaciones_recibos`.`idoperaciones_recibos` = $recibo)		$BySocio		LIMIT 0,1";
		return $sql;
	}
	function getInicialDeCreditos(){
		$sql	=  "SELECT
					`creditos_solicitud`.*,
					`creditos_tipoconvenio`.*,
					`creditos_periocidadpagos`.*,
					`creditos_estatus`.*,
					`creditos_solicitud`.`tasa_interes` AS `tasa_ordinaria_anual`,
					`creditos_solicitud`.`tipo_autorizacion` AS `tipo_de_autorizacion`,
					`creditos_solicitud`.`tasa_ahorro` AS `tasa_de_ahorro`
				FROM
					`creditos_tipoconvenio` `creditos_tipoconvenio`
						INNER JOIN `creditos_solicitud` `creditos_solicitud`
						ON `creditos_tipoconvenio`.`idcreditos_tipoconvenio`
						= `creditos_solicitud`.`tipo_convenio`
							INNER JOIN `creditos_periocidadpagos`
							`creditos_periocidadpagos`
							ON `creditos_periocidadpagos`.
							`idcreditos_periocidadpagos` =
							`creditos_solicitud`.`periocidad_de_pago`
								INNER JOIN `creditos_estatus`
								`creditos_estatus`
								ON `creditos_estatus`.`idcreditos_estatus` =
								`creditos_solicitud`.`estatus_actual` ";
		return $sql;
	}
	function getInicialDeCuentas(){
		$sql	= "SELECT
					`captacion_cuentas`.*,
					`captacion_cuentastipos`.`descripcion_cuentastipos` AS `tipo`,
					`captacion_cuentas`.`numero_cuenta`                 AS `cuenta`,
					`captacion_cuentas`.`fecha_afectacion`              AS `apertura`,
					`captacion_cuentas`.`inversion_fecha_vcto`          AS `vencimiento`,
					`captacion_cuentas`.`tasa_otorgada`                 AS `tasa`,
					`captacion_cuentas`.`dias_invertidos`               AS `dias`,
					`captacion_cuentas`.`observacion_cuenta`            AS `observaciones`,
					`captacion_cuentas`.`saldo_cuenta` 			        AS `saldo`,
					`captacion_subproductos`.`descripcion_subproductos` AS `subproducto`,
					`captacion_subproductos`.`algoritmo_de_premio`,
					`captacion_subproductos`.`algoritmo_de_tasa_incremental`,
					`captacion_subproductos`.`metodo_de_abono_de_interes`,
					`captacion_subproductos`.`destino_del_interes`,
					`captacion_subproductos`.`nombre_del_contrato`,
					`captacion_subproductos`.`algoritmo_modificador_del_interes`
				FROM
					`captacion_cuentas` `captacion_cuentas`
						INNER JOIN `captacion_cuentastipos` `captacion_cuentastipos`
						ON `captacion_cuentas`.`tipo_cuenta` = `captacion_cuentastipos`.
						`idcaptacion_cuentastipos`
							INNER JOIN `captacion_subproductos` `captacion_subproductos`
							ON `captacion_cuentas`.`tipo_subproducto` = `captacion_subproductos`
							.`idcaptacion_subproductos` ";
		return $sql;		
	}
	function getInicialDeCuentaContable($numero_de_cuenta = false){
		$numero_de_cuenta	= setNoMenorQueCero($numero_de_cuenta);
		$sql 	="SELECT `contable_catalogo`.* ,
			
					(`contable_catalogotipos`.`naturaleza` * `contable_catalogotipos`.`naturaleza_del_sector`) AS 'factor',
					`contable_catalogotipos`.`naturaleza`, `contable_catalogotipos`.`nombre_del_tipo`
				FROM
					`contable_catalogotipos` `contable_catalogotipos`
						INNER JOIN `contable_catalogo` `contable_catalogo`
						ON `contable_catalogotipos`.
						`idcontable_catalogotipos` = `contable_catalogo`.`tipo`
				WHERE `contable_catalogo`.`numero`= $numero_de_cuenta LIMIT 0,1";
		return $sql;		
	}
	function getInicialPersonasActividadEc(){
		$sql	= "SELECT
				`socios_aeconomica`.*,
				`socios_aeconomica`.`monto_percibido_ae`,
				`socios_aeconomica`.`socio_aeconomica`,
				`socios_aeconomica`.`idsocios_aeconomica` AS 'id'
			FROM
				`socios_aeconomica` `socios_aeconomica` ";
		return $sql;
	}
	function getListadoDeCajasConUsuario($estado, $fecha = false, $cajero = false){
		$xF			= new cFecha();
		$FA			= $xF->getFechaISO($fecha);
		$cajero		= setNoMenorQueCero($cajero);
		$ByFecha	= ($fecha === false) ? "" : " AND (`tesoreria_cajas`.`fecha_inicio` ='$FA')";
		$ByCajero	= ($cajero <=0) ? "": " AND `tesoreria_cajas`.`idusuario` =$cajero ";
		$sql		= "SELECT `tesoreria_cajas`.`idtesoreria_cajas` AS 'codigo',
					CONCAT(getFechaMX(`tesoreria_cajas`.`fecha_inicio`), '-', `usuarios`.`nombres`, ' ',`usuarios`.`apellidopaterno`,' ',`usuarios`.`apellidomaterno`) AS 'caja'
					FROM
					`tesoreria_cajas` `tesoreria_cajas`
						INNER JOIN `usuarios` `usuarios`
						ON `tesoreria_cajas`.`idusuario` = `usuarios`.`idusuarios`
				WHERE
					(`tesoreria_cajas`.`estatus` = '$estado') $ByFecha $ByCajero";
		
		return $sql;
	}
	function getListadoDeTesoreriaCajas($estado = SYS_TODAS, $FechaInicial = false, $FechaFinal = false){
		$ByEstado	= $this->OFiltro()->TesoreriaCajasPorEstado($estado);
		$ByFechas	= $this->OFiltro()->TesoreriaCajasPorFechas($FechaInicial, $FechaFinal);
		$sql		= "SELECT `tesoreria_cajas`.`idtesoreria_cajas` AS 'codigo',
		CONCAT(getFechaMX(`tesoreria_cajas`.`fecha_inicio`), '-', `usuarios`.`nombres`, ' ',`usuarios`.`apellidopaterno`,' ',`usuarios`.`apellidomaterno`) AS 'caja',
		getBooleanMX(`tesoreria_cajas`.`estatus`) AS 'estatus'
		FROM
		`tesoreria_cajas` `tesoreria_cajas`
		INNER JOIN `usuarios` `usuarios`
		ON `tesoreria_cajas`.`idusuario` = `usuarios`.`idusuarios`
		WHERE `tesoreria_cajas`.`idtesoreria_cajas` != ''
		$ByEstado $ByFechas  ORDER BY `fecha_inicio` DESC, `idusuario` ";
		//setLog($sql);
		return $sql;
	}	
	function getListadoDeCajasLocales(){
		$sql		= "SELECT
	`socios_cajalocal`.`idsocios_cajalocal`    AS `clave`,
	`socios_cajalocal`.`descripcion_cajalocal` AS `nombre`,
	`socios_region`.`descripcion_region`       AS `region`,
	`general_sucursales`.`nombre_sucursal`     AS `sucursal`,
	`socios_cajalocal`.`codigo_postal`         AS `codigo_postal`,
	`socios_cajalocal`.`ultimosocio`           AS `ultimo_registro` 
FROM
	`socios_cajalocal` `socios_cajalocal` 
		INNER JOIN `general_sucursales` `general_sucursales` 
		ON `socios_cajalocal`.`sucursal` = `general_sucursales`.
		`codigo_sucursal` 
			INNER JOIN `socios_region` `socios_region` 
			ON `socios_cajalocal`.`region` = `socios_region`.`idsocios_region` ";
		
		return $sql;
	}	
	function getInicialDePersonas(){ return "SELECT * FROM socios_general "; }
	function getListadoDeSaldosContablesConTitulos($cuenta){
		$sql	= "SELECT
			`contable_saldos`.`cuenta`,
			`contable_saldos`.`ejercicio`,
			`contable_saldos`.`tipo`,
			`contable_saldos`.`saldo_inicial` AS `inicial`,
			`contable_saldos`.`imp1`          AS `enero`,
			`contable_saldos`.`imp2`          AS `febrero`,
			`contable_saldos`.`imp3`          AS `marzo`,
			`contable_saldos`.`imp4`          AS `abril`,
			`contable_saldos`.`imp5`          AS `mayo`,
			`contable_saldos`.`imp6`          AS `junio`,
			`contable_saldos`.`imp7`          AS `julio`,
			`contable_saldos`.`imp8`          AS `agosto`,
			`contable_saldos`.`imp9`          AS `septiembre`,
			`contable_saldos`.`imp10`         AS `octubre`,
			`contable_saldos`.`imp11`         AS `noviembre`,
			`contable_saldos`.`imp12`         AS `diciembre`,
			`contable_saldos`.`imp13`         AS `ajustes`,
			`contable_saldos`.`imp14`         AS `final` 
		FROM
		`contable_saldos` `contable_saldos` WHERE	(`contable_saldos`.`cuenta` ='$cuenta')";
		return $sql;
	}
	function getListadoDeOperacionesContables($cuenta = false, $periodo = false, $ejercicio = false){
		$txtEjercicio	= "`contable_movimientos`.`ejercicio`,";
		$txtMes			= "`contable_movimientos`.`periodo`     AS `mes`,";
		$ByEjercicio	= "";
		$ByMes			= "";
		$ByCuenta		= "";
		$periodo		= setNoMenorQueCero($periodo);
		$ejercicio		= setNoMenorQueCero($ejercicio);
		$cuenta			= setNoMenorQueCero($cuenta);
		if($cuenta > 0){ 
			$ByCuenta		= " AND (`contable_movimientos`.`numerocuenta` =$cuenta)";
		}
		if($ejercicio > 0){
			$txtEjercicio	= "";
			$ByEjercicio	= " AND (`contable_movimientos`.`ejercicio` = $ejercicio) ";
		}
		if($periodo > 0 ){
			$txtMes			= "";
			$ByMes			= " AND (`contable_movimientos`.`periodo` = $periodo) ";
		}
		$sql	= "SELECT
					$txtEjercicio
					$txtMes
					`contable_movimientos`.`fecha`,
					`contable_polizasdiarios`.`nombre_del_diario` AS `tipo`,
					`contable_movimientos`.`numeropoliza`    AS `poliza`,
					`contable_movimientos`.`numeromovimiento`     AS `operacion`,
					`contable_movimientos`.`numerocuenta`    AS `cuenta`,
					
					/*/`contable_movimientos`.`importe`,*/
					
					
					`contable_movimientos`.`cargo`,
					`contable_movimientos`.`abono`,
					
					/*`contable_movimientos`.`moneda`,*/
					`contable_movimientos`.`referencia`,
					`contable_movimientos`.`concepto`					 
				FROM
					`contable_movimientos` `contable_movimientos` 
						INNER JOIN `contable_polizasdiarios` `contable_polizasdiarios` 
						ON `contable_movimientos`.`tipopoliza` = `contable_polizasdiarios`.
						`idcontable_polizadiarios` 
				WHERE
				`clave_unica` != 0
				$ByCuenta
				$ByEjercicio
				$ByMes
			ORDER BY `clave_unica` DESC ";
		return $sql;
	}	
	function getListadoDeSaldosPorMes($cuenta, $fecha = false, $tipo = 1){
		$fecha	= ($fecha == false) ? fechasys() : $fecha;
		$xF		= new cFecha(0, $fecha);
		$mes	= (int) $xF->mes();
		$anno	= $xF->anno();
		
		$xEsq	= new cCuentaContableEsquema($cuenta);
		$rawC	= $xEsq->CUENTARAW;
		$siz	= strlen($rawC);
		$sql	= "SELECT
			`contable_catalogo`.`numero`,
			`contable_catalogo`.`nombre`,
			`contable_catalogo`.`digitoagrupador` AS 'nivel',
			`contable_saldos`.`imp$mes` AS 'monto'
			

		FROM
	`contable_catalogo` `contable_catalogo` 
		INNER JOIN `contable_saldos` `contable_saldos` 
		ON `contable_catalogo`.`numero` = `contable_saldos`.`cuenta`
		
		WHERE
			(`contable_saldos`.`ejercicio` = $anno) AND
			(`contable_saldos`.`tipo` = $tipo) 
			AND 
			(`contable_catalogo`.`digitoagrupador` <=3)
			AND 
			SUBSTR(`contable_catalogo`.`numero`, 1, $siz) = '$rawC'
		ORDER BY numero ";
		return $sql;
	}
	function getListadoDeEmpresas($clave	= false, $persona	= false, $incluirFallback = true){
		$clave		= setNoMenorQueCero($clave);
		$persona	= setNoMenorQueCero($persona);
		$ByClave	= ($clave > DEFAULT_EMPRESA) ? " AND (`socios_aeconomica_dependencias`.`idsocios_aeconomica_dependencias` = $clave) " : "";
		$ByPersona	= ($persona > DEFAULT_SOCIO) ? " AND	(`socios_aeconomica_dependencias`.`clave_de_persona` =$persona) " : "";
		$ByFallback	= ($incluirFallback == false AND $persona <= DEFAULT_SOCIO) ? " AND (`socios_aeconomica_dependencias`.`idsocios_aeconomica_dependencias` != " . FALLBACK_CLAVE_EMPRESA . ") " : "";
		$sql		= "SELECT
			`socios_aeconomica_dependencias`.`idsocios_aeconomica_dependencias` AS `clave`,
			`socios_aeconomica_dependencias`.`descripcion_dependencia`          AS `nombre`,
			`socios_aeconomica_dependencias`.`nombre_corto`                     AS `alias`,
			`socios_aeconomica_dependencias`.`clave_de_persona`,
			`socios_aeconomica_dependencias`.`telefono`                         AS `telefono` 
		FROM
			`socios_aeconomica_dependencias` `socios_aeconomica_dependencias` 
		WHERE
			(`socios_aeconomica_dependencias`.`idsocios_aeconomica_dependencias` != 0) $ByFallback $ByClave $ByPersona 
		ORDER BY `socios_aeconomica_dependencias`.`estatus` DESC, `socios_aeconomica_dependencias`.`nombre_corto`";
		return $sql;
	}
	function getInicialPersonasRelaciones($clave = false, $persona = false, $tipo = false, $relacionado = false, $documento = false){
		$clave		= setNoMenorQueCero($clave);
		$persona	= setNoMenorQueCero($persona);
		$tipo		= setNoMenorQueCero($tipo);
		$relacionado= setNoMenorQueCero($relacionado);
		$documento	= setNoMenorQueCero($documento);
		$ByClave	= ($clave > 0) ? " AND (`socios_relaciones`.`idsocios_relaciones` = $clave) " : "";
		$ByPersona	= ($persona > DEFAULT_SOCIO) ? " AND (`socios_relaciones`.`socio_relacionado` = $persona) " : "";
		$ByRelacion	= ($relacionado > DEFAULT_SOCIO) ? " AND (`socios_relaciones`.`numero_socio` =$relacionado) " : "";
		$ByTipo		= ($tipo > 0) ? " AND (`socios_relaciones`.`tipo_relacion` =$tipo) " : "";
		$sql 	= "SELECT	`socios_relaciones`.*,	`socios_relacionestipos`.`descripcion_relacionestipos` AS `nombre_relacion`,
			`socios_consanguinidad`.`descripcion_consanguinidad`   AS `nombre_consanguinidad`
		FROM `socios_relaciones` `socios_relaciones`		INNER JOIN `socios_relacionestipos` `socios_relacionestipos`
				ON `socios_relaciones`.`tipo_relacion` = `socios_relacionestipos`.`idsocios_relacionestipos`
					INNER JOIN `socios_consanguinidad` `socios_consanguinidad` 	ON `socios_relaciones`.`consanguinidad` = `socios_consanguinidad`.`idsocios_consanguinidad`
		WHERE (`socios_relaciones`.`idsocios_relaciones` > 0) $ByClave $ByPersona $ByRelacion $ByTipo LIMIT 0,1";
		
		return $sql;		
	}
	function getListadoDePersonasExpuestas($persona){
		$persona		= setNoMenorQueCero($persona);
		$ByPersona	= ($persona == 0) ? "" : " AND (`socios_relaciones`.`socio_relacionado` =$persona) ";
		$sql	= "SELECT
				`socios_relaciones`.*,
				`socios_consanguinidad`.`grado_de_consanguinidad`,
				`socios_consanguinidad`.`grado_de_afinidad`,
				`socios_relacionestipos`.`tiene_vinculo_patrimonial`
			FROM
			`socios_relaciones` `socios_relaciones` 
				INNER JOIN `socios_consanguinidad` `socios_consanguinidad` 
				ON `socios_relaciones`.`consanguinidad` = `socios_consanguinidad`.
				`idsocios_consanguinidad` 
					INNER JOIN `socios_relacionestipos` `socios_relacionestipos` 
					ON `socios_relaciones`.`tipo_relacion` = `socios_relacionestipos`.
					`idsocios_relacionestipos` 
			WHERE
				(
				(`socios_consanguinidad`.`grado_de_consanguinidad` <=2) 
				OR
				(`socios_consanguinidad`.`grado_de_afinidad` <=2)
				OR
				(`socios_relacionestipos`.`tiene_vinculo_patrimonial` =1)
				)
				$ByPersona
				LIMIT 0,100
				";
		return $sql;
	}
	function getListadoDeCreditosConOficial($oficial = false, $estado = false, $periocidad = false, $producto = false, $otros =""){
		$oficial	= setNoMenorQueCero($oficial);
		$estado		= setNoMenorQueCero($estado);
		$periocidad	= setNoMenorQueCero($periocidad);
		$producto	= setNoMenorQueCero($producto);
		
		$ByProducto	= ($producto> 0) ? " AND (`creditos_solicitud`.`tipo_convenio` =$producto) " : "";
		$ByOficial	= ($oficial > 0) ? " AND (`creditos_solicitud`.`oficial_credito`=$oficial) " : "";
		$ByPeriodo	= ($periocidad>0) ? " AND (`creditos_solicitud`.`periocidad_de_pago`=$periocidad) " : "";
		$ByEstado	= ($estado>0) ? "AND	(`creditos_solicitud`.`estatus_actual` = $estado) " : "";
		
		$sqlCred = "SELECT
			`socios_general`.`codigo`,
			CONCAT(`socios_general`.`apellidopaterno`, ' ',
			`socios_general`.`apellidomaterno`, ' ',
			`socios_general`.`nombrecompleto`) AS 'nombre',
			`creditos_solicitud`.`numero_solicitud`,
			`creditos_periocidadpagos`.`descripcion_periocidadpagos` AS `periocidad`,
			`creditos_tipoconvenio`.`descripcion_tipoconvenio`       AS `convenio`,
			`creditos_estatus`.`descripcion_estatus`                 AS `estatus`,
			`creditos_solicitud`.`saldo_actual`                      AS `saldo`,
			`oficiales`.`nombre_completo`							AS `oficial`
		FROM
			`creditos_solicitud` `creditos_solicitud`
				INNER JOIN `creditos_estatus` `creditos_estatus`
				ON `creditos_solicitud`.`estatus_actual` = `creditos_estatus`.
				`idcreditos_estatus`
					INNER JOIN `creditos_periocidadpagos` `creditos_periocidadpagos`
					ON `creditos_solicitud`.`periocidad_de_pago` =
					`creditos_periocidadpagos`.`idcreditos_periocidadpagos`
						INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio`
						ON `creditos_solicitud`.`tipo_convenio` =
						`creditos_tipoconvenio`.`idcreditos_tipoconvenio`
							INNER JOIN `socios_general` `socios_general`
							ON `creditos_solicitud`.`numero_socio` = `socios_general`.
							`codigo`
								INNER JOIN `oficiales` `oficiales`
								ON `creditos_solicitud`.`oficial_credito` = `oficiales`.`id`
		WHERE
			(`creditos_solicitud`.`saldo_actual` >=" . TOLERANCIA_SALDOS . ")
			$ByEstado $ByOficial $ByPeriodo	$ByProducto $otros ";
		return $sqlCred;
	}
	function getListadoDeCreditosConOficialSeguimiento($oficial = false, $estado = false, $periocidad = false, $producto = false, $otros =""){
		$oficial	= setNoMenorQueCero($oficial);
		$estado		= setNoMenorQueCero($estado);
		$periocidad	= setNoMenorQueCero($periocidad);
		$producto	= setNoMenorQueCero($producto);
		
		$ixOficial	= (CREDITO_USAR_OFICIAL_SEGUIMIENTO == true) ? "oficial_seguimiento" : "oficial_credito";		
		
		$ByProducto	= ($producto> 0) ? " AND (`creditos_solicitud`.`tipo_convenio` =$producto) " : "";
		$ByOficial	= ($oficial > 0) ? " AND (	`creditos_solicitud`.`$ixOficial` = $oficial) " : "";
		$ByPeriodo	= ($periocidad>0) ? " AND (`creditos_solicitud`.`periocidad_de_pago`=$periocidad) " : "";
		$ByEstado	= ($estado>0) ? "AND (`creditos_solicitud`.`estatus_actual` = $estado) " : "";
		
		$sqlCred = "SELECT
			`socios_general`.`codigo`,
			CONCAT(`socios_general`.`apellidopaterno`, ' ',
			`socios_general`.`apellidomaterno`, ' ',
			`socios_general`.`nombrecompleto`) AS 'nombre',
			`creditos_solicitud`.`numero_solicitud`,
			`creditos_periocidadpagos`.`descripcion_periocidadpagos` AS `periocidad`,
			`creditos_tipoconvenio`.`descripcion_tipoconvenio`       AS `convenio`,
			`creditos_estatus`.`descripcion_estatus`                 AS `estatus`,
			`creditos_solicitud`.`saldo_actual`                      AS `saldo`,
			`oficiales`.`nombre_completo`							AS `oficial`
		FROM
			`creditos_solicitud` `creditos_solicitud`
				INNER JOIN `creditos_estatus` `creditos_estatus`
				ON `creditos_solicitud`.`estatus_actual` = `creditos_estatus`.
				`idcreditos_estatus`
					INNER JOIN `creditos_periocidadpagos` `creditos_periocidadpagos`
					ON `creditos_solicitud`.`periocidad_de_pago` =
					`creditos_periocidadpagos`.`idcreditos_periocidadpagos`
						INNER JOIN `creditos_tipoconvenio` `creditos_tipoconvenio`
						ON `creditos_solicitud`.`tipo_convenio` =
						`creditos_tipoconvenio`.`idcreditos_tipoconvenio`
							INNER JOIN `socios_general` `socios_general`
							ON `creditos_solicitud`.`numero_socio` = `socios_general`.
							`codigo`
								INNER JOIN `oficiales` `oficiales`
								ON 	`creditos_solicitud`.`$ixOficial`  = `oficiales`.`id`
		WHERE
			(`creditos_solicitud`.`saldo_actual` >=" . TOLERANCIA_SALDOS . ")
				$ByEstado $ByOficial $ByPeriodo	$ByProducto $otros
		ORDER BY `creditos_solicitud`.`$ixOficial`, `creditos_estatus`.`idcreditos_estatus`  ";
		return $sqlCred;
	}	
	function getListadoDeOperacionesConTerceros($persona = false){
		$persona	= setNoMenorQueCero($persona);
		$ByPersona	= ($persona > 1) ? " AND (`personas_relaciones_recursivas`.`persona` =$persona) " : "";
		$ql	= "SELECT
			`personas_relaciones_recursivas`.`persona`,
			`personas_relaciones_recursivas`.`relacion`,
			`socios`.`nombre`,
			`operaciones_recibos`.`fecha_operacion`             AS `fecha`,
			`operaciones_recibostipo`.`descripcion_recibostipo` AS `tipo`,
			`operaciones_recibos`.`docto_afectado`              AS `documento`,
			`usuarios`.`nombrecompleto`                         AS `usuario`,
			`operaciones_recibos`.`total_operacion`             AS `monto`,
			`operaciones_recibos`.`tipo_pago`                   AS `pago`,
			`operaciones_recibos`.`indice_origen`               AS `origen`,
			`operaciones_recibos`.`grupo_asociado`              AS `grupo`,
			`operaciones_recibos`.`recibo_fiscal`               AS `recibo`,
			`operaciones_recibos`.`clave_de_moneda`             AS `moneda`,
			`operaciones_recibos`.`origen_aml`                  AS `tipo_aml`,
			`operaciones_recibos`.`persona_asociada`            AS `empresa`,
			`operaciones_recibos`.`sucursal`
			 
		FROM
			`operaciones_recibos` `operaciones_recibos` 
				INNER JOIN `usuarios` `usuarios` 
				ON `operaciones_recibos`.`idusuario` = `usuarios`.`idusuarios` 
					INNER JOIN `operaciones_recibostipo` `operaciones_recibostipo` 
					ON `operaciones_recibos`.`tipo_docto` = `operaciones_recibostipo`.
					`idoperaciones_recibostipo` 
						INNER JOIN `personas_relaciones_recursivas` 
						`personas_relaciones_recursivas` 
						ON `operaciones_recibos`.`numero_socio` = 
						`personas_relaciones_recursivas`.`relacion` 
							INNER JOIN `socios` `socios` 
							ON `personas_relaciones_recursivas`.`relacion` = `socios`.
							`codigo` 
		WHERE (`personas_relaciones_recursivas`.`persona` != 1) $ByPersona	 ";
		return $sql;
	}
	function setCreditosSaldosDeInteres($credito = false, $IntNormalPag = false, $IntNormalDev = false, $IntMorPag = false, $IntMorDev = false, $SdoIntAnt = false){
		$ByCredito	= $this->OFiltro()->CreditoPorClave($credito);
		$txt		= "";
		$ups		= array();
		if($IntNormalPag 	!== false){ $ups[] = "interes_normal_pagado=$IntNormalPag";  }
		if($IntNormalDev 	!== false){ $ups[] = "interes_normal_devengado=$IntNormalDev";  }
		if($IntMorDev 		!== false){ $ups[] = "interes_moratorio_devengado=$IntMorDev";  }
		if($IntMorPag 		!== false){ $ups[] = "interes_moratorio_pagado=$IntMorPag";  }
		if($SdoIntAnt 		!== false){ $ups[] = "sdo_int_ant=$SdoIntAnt";  }
		foreach ($ups as $idx => $cnt){ $txt .= ($txt == "") ? $cnt : ", $cnt"; }
		$sql	= "UPDATE creditos_solicitud SET $txt WHERE numero_solicitud > 0 $ByCredito";
		unset($ups); unset($txt);
		return $sql;
	}

	function getListadoDePeriodosDeCredito($FechaInicial = false, $FechaFinal = false){
		$xF			= new cFecha();
		$ByFechas		= "";
		if($FechaInicial != false){
			$FechaInicial	= $xF->getFechaISO($FechaInicial);
			$ByFechas		.= " AND (`creditos_periodos`.`fecha_inicial` <= '$FechaInicial') AND (`creditos_periodos`.`fecha_final` > '$FechaInicial') ";
		}
		if($FechaFinal != false AND $FechaInicial != false){
			$FechaFinal		= $xF->getFechaISO($FechaFinal);
			$ByFechas		.= " AND (`creditos_periodos`.`fecha_inicial` >= '$FechaInicial') AND (`creditos_periodos`.`fecha_final` <='$FechaFinal')";
		}
		$sql	= "SELECT
		`creditos_periodos`.`idcreditos_periodos`  AS `clave`,
		`creditos_periodos`.`descripcion_periodos` AS `descripcion`,
		`creditos_periodos`.`fecha_inicial`,
		`creditos_periodos`.`fecha_final`,
		`creditos_periodos`.`fecha_reunion`        AS `fecha_de_reunion`,
		`usuarios`.`nombreusuario`                 AS `oficial_de_credito` 
	FROM
		`creditos_periodos` `creditos_periodos` 
			INNER JOIN `usuarios` `usuarios` 
			ON `creditos_periodos`.`periodo_responsable` = `usuarios`.`idusuarios`
		WHERE `creditos_periodos`.`idcreditos_periodos` > 0
		$ByFechas";
		return $sql;
	}
	function getListaDeCreditosAutorizados($periodo = false, $estado = false , $Autorizados = false, $Rechazados = false, $conPeriodo = true, $FechaInicial = false, $FechaFinal = false, $SoloDatosSol = false, $persona = false){
		$SAut		= ($Autorizados == false) ? "" : ",	`creditos_tipo_de_autorizacion`.`descripcion_tipo_de_autorizacion` AS `tipo_de_autorizacion`,
					`creditos_solicitud`.`fecha_autorizacion` AS `fecha_de_autorizacion`, `creditos_solicitud`.`pagos_autorizados` AS	`numero_de_pagos_autorizado`, `creditos_solicitud`.`monto_autorizado`	";
		$ByAut		= ($Autorizados == false) ? " AND (`creditos_solicitud`.`monto_autorizado` <= 0) " : "  ";
		$periodo	= setNoMenorQueCero($periodo);
		$estado		= setNoMenorQueCero($estado);
		$ByPeriodos	= ($periodo > 0) ? " AND (`creditos_solicitud`.`periodo_solicitudes` = $periodo) " : "";
		$ByEstado	= ($estado > 0) ? " AND (`creditos_solicitud`.`estatus_actual` = $estado) " : "";
		$NoRecha	= ($Rechazados == true) ? "" : " AND (SELECT COUNT(*) FROM `creditos_rechazados` WHERE `numero_de_credito`=`creditos_solicitud`.`numero_solicitud`) <= 0 ";
		$strPer		= ($conPeriodo == true) ? "`creditos_solicitud`.`periodo_solicitudes` AS `sesion_de_credito`, `creditos_periodos`.`descripcion_periodos` AS `nombre_de_la_sesion`," : "";
		$strEstat	= ($estado <= 0) ? ", `creditos_estatus`.`descripcion_estatus` AS	`estado_actual` " : "";
		if($Rechazados == true AND $estado <= 0){
			$strEstat	= ", IF( ((SELECT COUNT(*) FROM `creditos_rechazados` WHERE `numero_de_credito`=`creditos_solicitud`.`numero_solicitud`) >0), getTrad('RECHAZADO') ,`creditos_estatus`.`tit_proceso`) AS	`estado_actual` ";
		}
		if($SoloDatosSol == true){
			$ByAut	= "";
			$SAut	= ",	`creditos_destinos`.`descripcion_destinos` AS `aplicacion` ";
		}
		$ByFecha	= $this->OFiltro()->CreditosPorFechaDeAutorizacion($FechaInicial, $FechaFinal);
	
		$ByPersona	= $this->OFiltro()->CreditoPorPersona($persona);
		$strPersona	= ( $ByPersona == "") ? " `socios`.`codigo`, `socios`.`nombre` , " : "";
		
		$sql	= "SELECT
		$strPer
		$strPersona
		`socios`.`alias_dependencia` AS `empresa`,
		`creditos_solicitud`.`numero_solicitud` AS `numero_de_solicitud`,
		`creditos_solicitud`.`fecha_solicitud` AS `fecha_de_registro`,
		`creditos_solicitud`.`monto_solicitado`,
		`creditos_solicitud`.`numero_pagos`                                AS
		`numero_de_pagos_solicitado`,
		`creditos_tipoconvenio`.`descripcion_tipoconvenio`                 AS
		`producto`,
		`creditos_tipo_de_pago`.`descripcion`                              AS
		`tipo_de_pago`,
		`creditos_periocidadpagos`.`descripcion_periocidadpagos`           AS
		`periocidad_de_pago`
		$strEstat
		$SAut
		,`oficiales`.`nombre_completo` AS `oficial`
		FROM
		`creditos_solicitud` `creditos_solicitud` INNER JOIN `creditos_tipo_de_autorizacion`
		`creditos_tipo_de_autorizacion`  ON `creditos_solicitud`.`tipo_autorizacion` = `creditos_tipo_de_autorizacion`.`idcreditos_tipo_de_autorizacion`
		INNER JOIN `creditos_destinos` `creditos_destinos` 	ON `creditos_solicitud`.`destino_credito` = `creditos_destinos`.
		`idcreditos_destinos` LEFT OUTER JOIN `socios_aeconomica_dependencias` `socios_aeconomica_dependencias`	ON `creditos_solicitud`.`persona_asociada` = `socios_aeconomica_dependencias`.`idsocios_aeconomica_dependencias`
		INNER JOIN `creditos_tipo_de_pago` `creditos_tipo_de_pago` ON `creditos_solicitud`.`tipo_de_pago` =	`creditos_tipo_de_pago`.`idcreditos_tipo_de_pago` INNER JOIN `creditos_tipoconvenio`
		`creditos_tipoconvenio` ON `creditos_solicitud`.`tipo_convenio` = `creditos_tipoconvenio`.`idcreditos_tipoconvenio`
		INNER JOIN `oficiales` `oficiales` 	ON `creditos_solicitud`.`oficial_credito` =	`oficiales`.`id`	INNER JOIN `creditos_periocidadpagos` `creditos_periocidadpagos`
		ON `creditos_solicitud`.`periocidad_de_pago` = 	`creditos_periocidadpagos`.`idcreditos_periocidadpagos`	INNER JOIN `creditos_estatus` `creditos_estatus` ON `creditos_solicitud`.`estatus_actual` =
		`creditos_estatus`.`idcreditos_estatus`  INNER JOIN `socios` `socios` ON `creditos_solicitud`.`numero_socio` = `socios`.`codigo` LEFT OUTER JOIN `creditos_periodos` `creditos_periodos` ON `creditos_solicitud`.
		`periodo_solicitudes` = `creditos_periodos`.`idcreditos_periodos`
		WHERE
		(`creditos_solicitud`.`numero_solicitud` > 0) $ByPeriodos $NoRecha $ByAut $ByEstado $ByFecha
		ORDER BY `creditos_solicitud`.`fecha_solicitud` DESC, `socios`.`nombre`";
	
		return $sql;
	}	
	function getListaDeCreditosEnProceso($periodo = false, $estado = false , $Autorizados = false, $Rechazados = false, $conPeriodo = true, $FechaInicial = false, $FechaFinal = false, $SoloDatosSol = false, $persona = false){
		$SAut		= ($Autorizados == false) ? "" : ",	`creditos_tipo_de_autorizacion`.`descripcion_tipo_de_autorizacion` AS `tipo_de_autorizacion`,
					`creditos_solicitud`.`fecha_autorizacion` AS `fecha_de_autorizacion`, `creditos_solicitud`.`pagos_autorizados` AS	`numero_de_pagos_autorizado`, `creditos_solicitud`.`monto_autorizado`	";
		$ByAut		= ($Autorizados === false) ? " AND (`creditos_solicitud`.`monto_autorizado` <= 0) " : " ";
		$periodo	= setNoMenorQueCero($periodo);
		$estado		= setNoMenorQueCero($estado);
		$ByPeriodos	= ($periodo > 0) ? " AND ( `creditos_solicitud`.`periodo_solicitudes` = $periodo ) " : "";
		$ByEstado	= ($estado > 0) ? " AND ( `creditos_solicitud`.`estatus_actual` = $estado ) " : "";
		$NoRecha	= ($Rechazados == true) ? "" : " AND (SELECT COUNT(*) FROM `creditos_rechazados` WHERE `numero_de_credito`=`creditos_solicitud`.`numero_solicitud` ) <= 0 ";
		$strPer		= ($conPeriodo == true) ? "`creditos_solicitud`.`periodo_solicitudes` AS `sesion_de_credito`, `creditos_periodos`.`descripcion_periodos` AS `nombre_de_la_sesion`," : "";
		$strEstat	= ($estado <= 0) ? ", `creditos_estatus`.`descripcion_estatus` AS	`estado_actual` " : "";
		
		$ByPersona	= $this->OFiltro()->CreditoPorPersona($persona);
		$strPersona	= ( $ByPersona == "") ? " `socios`.`codigo`, `socios`.`nombre` , " : "";
		
		if($Rechazados == true AND $estado <= 0){
			$strEstat	= ", IF(((SELECT COUNT(*) FROM `creditos_rechazados` WHERE `numero_de_credito`=`creditos_solicitud`.`numero_solicitud`) >0), getTrad('RECHAZADO') ,`creditos_estatus`.`tit_proceso` ) AS `estado_actual` ";
		}
		if($SoloDatosSol == true){
			$ByAut	= "";
			$SAut	= ",	`creditos_destinos`.`descripcion_destinos` AS `aplicacion` ";
		}
		$ByFecha	= $this->OFiltro()->CreditosPorFechaDeSolicitud($FechaInicial, $FechaFinal);
		
		$sql	= "SELECT
					$strPer				
					$strPersona
					`socios`.`alias_dependencia` AS `empresa`,
					`creditos_solicitud`.`numero_solicitud` AS `numero_de_solicitud`,
					`creditos_solicitud`.`fecha_solicitud` AS `fecha_de_registro`,
					`creditos_solicitud`.`monto_solicitado`,
					`creditos_solicitud`.`numero_pagos`                                AS 
					`numero_de_pagos_solicitado`,
					`creditos_tipoconvenio`.`descripcion_tipoconvenio`                 AS 
					`producto`,
					`creditos_tipo_de_pago`.`descripcion`                              AS 
					`tipo_de_pago`,
					`creditos_periocidadpagos`.`descripcion_periocidadpagos`           AS 
					`periocidad_de_pago` 
					$strEstat
 					$SAut
 					,`oficiales`.`nombre_completo` AS `oficial`
				FROM
	`creditos_solicitud` `creditos_solicitud` INNER JOIN `creditos_tipo_de_autorizacion`
		`creditos_tipo_de_autorizacion`  ON `creditos_solicitud`.`tipo_autorizacion` = `creditos_tipo_de_autorizacion`.`idcreditos_tipo_de_autorizacion` 
			INNER JOIN `creditos_destinos` `creditos_destinos` 	ON `creditos_solicitud`.`destino_credito` = `creditos_destinos`.
			`idcreditos_destinos` LEFT OUTER JOIN `socios_aeconomica_dependencias` `socios_aeconomica_dependencias`	ON `creditos_solicitud`.`persona_asociada` = `socios_aeconomica_dependencias`.`idsocios_aeconomica_dependencias` 
					INNER JOIN `creditos_tipo_de_pago` `creditos_tipo_de_pago` ON `creditos_solicitud`.`tipo_de_pago` =	`creditos_tipo_de_pago`.`idcreditos_tipo_de_pago` INNER JOIN `creditos_tipoconvenio` 
						`creditos_tipoconvenio` ON `creditos_solicitud`.`tipo_convenio` = `creditos_tipoconvenio`.`idcreditos_tipoconvenio` 
							INNER JOIN `oficiales` `oficiales` 	ON `creditos_solicitud`.`oficial_credito` =	`oficiales`.`id`	INNER JOIN `creditos_periocidadpagos` `creditos_periocidadpagos` 
								ON `creditos_solicitud`.`periocidad_de_pago` = 	`creditos_periocidadpagos`.`idcreditos_periocidadpagos`	INNER JOIN `creditos_estatus` `creditos_estatus` ON `creditos_solicitud`.`estatus_actual` = 
									`creditos_estatus`.`idcreditos_estatus`  INNER JOIN `socios` `socios` ON `creditos_solicitud`.`numero_socio` = `socios`.`codigo` LEFT OUTER JOIN `creditos_periodos` `creditos_periodos` ON `creditos_solicitud`.
											`periodo_solicitudes` = `creditos_periodos`.`idcreditos_periodos`
				WHERE
					(`creditos_solicitud`.`numero_solicitud`>0) AND (`socios`.`codigo`!=" . DEFAULT_SOCIO . ")  $ByPeriodos $NoRecha $ByAut $ByEstado $ByFecha $ByPersona
		ORDER BY `creditos_solicitud`.`fecha_solicitud` DESC, `socios`.`nombre`";
		
		return $sql;
	}
	function getListaDePatrimonioPorPersona($persona){
		$sql	= "SELECT
			`socios_patrimonio`.`idsocios_patrimonio`                  AS `clave`,
			`socios_patrimoniotipo`.`descripcion_patrimoniotipo`       AS `tipo`,
			`socios_patrimonio`.`fecha_de_alta` AS `fecha_de_registro`,
			`socios_patrimonioestatus`.`descripcion_patrimonioestatus` AS `estado`,
			`socios_patrimonio`.`descripcion`,
			`socios_patrimonio`.`monto_patrimonio` AS `monto`,
			`socios_patrimonio`.`documento_presentado`
		FROM
			`socios_patrimonio` `socios_patrimonio` 
				INNER JOIN `socios_patrimonioestatus` `socios_patrimonioestatus` 
				ON `socios_patrimonio`.`estatus_actual` = `socios_patrimonioestatus`.
				`idsocios_patrimonioestatus` 
					INNER JOIN `socios_patrimoniotipo` `socios_patrimoniotipo` 
					ON `socios_patrimonio`.`tipo_patrimonio` = `socios_patrimoniotipo`.
					`idsocios_patrimoniotipo` 
		WHERE
			(`socios_patrimonio`.`socio_patrimonio` = $persona ) ";
		return $sql;
	}
	function getListaDeFlujoDeEfvoPorCredito($credito){
		$credito	= setNoMenorQueCero($credito);
		$sql		= "SELECT
				`creditos_flujoefvo`.`idcreditos_flujoefvo`          AS `clave`,
				`creditos_tflujo`.`descripcion_tflujo`                   AS `tipo`,
				`creditos_origenflujo`.`descripcion_origenflujo`         AS `origen`,
				`creditos_periocidadflujo`.`descripcion_periocidadflujo` AS `frecuencia`,
				`creditos_flujoefvo`.`afectacion_neta` AS `monto_diario` 
				
			FROM
				`creditos_flujoefvo` `creditos_flujoefvo` 
					INNER JOIN `creditos_origenflujo` `creditos_origenflujo` 
					ON `creditos_flujoefvo`.`origen_flujo` = `creditos_origenflujo`.
					`idcreditos_origenflujo` 
						INNER JOIN `creditos_tflujo` `creditos_tflujo` 
						ON `creditos_flujoefvo`.`tipo_flujo` = `creditos_tflujo`.
						`idcreditos_tflujo` 
							INNER JOIN `creditos_periocidadflujo` `creditos_periocidadflujo` 
							ON `creditos_flujoefvo`.`periocidad_flujo` = 
							`creditos_periocidadflujo`.`idcreditos_periocidadflujo` 
			WHERE
				(`creditos_flujoefvo`.`solicitud_flujo` = $credito)";
		return $sql;
	}
	function getListadoDeTelefonosPorPersona($persona = false){
		$persona	= setNoMenorQueCero($persona);
		$sqlSc		= "SELECT `socios_general`.`telefono_principal` AS `telefono`, CONCAT(`socios_general`.`telefono_principal`, '-Principal') AS `descripcion`
		FROM `socios_general` `socios_general`  WHERE (`socios_general`.`codigo` =$persona)
		UNION SELECT `socios_vivienda`.`telefono_movil`, CONCAT(`socios_vivienda`.`telefono_movil`, '-ViviendaMovil')
		FROM `socios_vivienda` WHERE (`socios_vivienda`.`socio_numero` =$persona)
		UNION SELECT `socios_vivienda`.`telefono_residencial` ,	CONCAT(`socios_vivienda`.`telefono_residencial`, '-ViviendaFijo')
		FROM `socios_vivienda` WHERE (`socios_vivienda`.`socio_numero` =$persona)
		";
		return $sqlSc;
	}

	function getListadoDePresupuestado($clave = false){
		$clave		= setNoMenorQueCero($clave);

		$sql		= "
				SELECT
					`creditos_destino_detallado`.`idcreditos_destino_detallado` AS `clave`,
					`creditos_destinos`.`descripcion_destinos`                  AS `destino`,
					`socios`.`nombre`                                           AS `proveedor`,
					`creditos_destino_detallado`.`monto`,
					`creditos_destino_detallado`.`observaciones` 
				FROM
					`creditos_destino_detallado` `creditos_destino_detallado` 
						INNER JOIN `socios` `socios` 
						ON `creditos_destino_detallado`.`clave_de_empresa` = `socios`.`codigo` 
							INNER JOIN `creditos_destinos` `creditos_destinos` 
							ON `creditos_destino_detallado`.`clave_de_destino` = 
							`creditos_destinos`.`idcreditos_destinos`
		WHERE `creditos_destino_detallado`.`monto` > 0	AND (`creditos_destino_detallado`.`clave_de_presupuesto` = $clave) ";
		
		return $sql;		
	}
	function getListadoDePresupuesto($clave = false, $persona = false, $estado = false){
		$clave		= setNoMenorQueCero($clave);
		$persona	= setNoMenorQueCero($persona);
		$estado		= setNoMenorQueCero($estado);
		$ByClave	= ($clave <= 0) ? "" : " AND (`creditos_presupuestos`.`clave_de_presupuesto`=$clave) ";
		$ByPersona	= ($persona <= DEFAULT_SOCIO) ? "" : " AND (`creditos_presupuestos`.`clave_de_persona` = $persona) ";
		$ByEstado	= " AND (`creditos_presupuestos`.`estado_actual` = $estado) "; 
		$sql	= "SELECT `creditos_presupuestos`.*	FROM `creditos_presupuestos` WHERE `creditos_presupuestos`.`clave_de_presupuesto` >0 $ByClave $ByPersona ";
		return $sql;
	}
	function getListadoDePersonasConPresupuesto($persona_adicional){
		$persona_adicional	= setNoMenorQueCero($persona_adicional);
		$RTM	= ($persona_adicional > DEFAULT_SOCIO) ? "UNION SELECT `socios`.`codigo`, `socios`.`nombre` FROM `socios` WHERE	(`socios`.`codigo` = $persona_adicional) " : "";
		$sql	= "SELECT `socios_aeconomica_dependencias`.`clave_de_persona`,	`socios`.`nombre`
			FROM`socios_aeconomica_dependencias` `socios_aeconomica_dependencias` INNER JOIN `socios` `socios`	ON `socios_aeconomica_dependencias`.`clave_de_persona` = `socios`.`codigo`
			 $RTM ";
		return $sql;
	}
	function getListadoDePresupuestoPorPagar($proveedor, $estado = 0){
		$proveedor	= setNoMenorQueCero($proveedor);
		$estado		= setNoMenorQueCero($estado);
		//	`creditos_destino_detallado`.`estado_actual`,	`creditos_destino_detallado`.`clave_de_empresa`,
		$sql	= "SELECT
				`creditos_destino_detallado`.`idcreditos_destino_detallado` AS `control`,
				`creditos_destino_detallado`.`clave_de_presupuesto`         AS `presupuesto`
				,
				`creditos_destino_detallado`.`clave_de_persona`             AS `persona`,
				`personas`.`nombre`,
				`creditos_solicitud`.`numero_solicitud`                     AS `credito`,
				`creditos_solicitud`.`fecha_ministracion`                   AS `fecha`,
				`creditos_solicitud`.`monto_autorizado`                     AS `monto_del_credito`,
				`creditos_destinos`.`descripcion_destinos`                  AS `destino`,
				`creditos_destino_detallado`.`monto`,
				(IF( ISNULL(`socios_aeconomica_dependencias`.`comision_por_encargo`), 0,  `socios_aeconomica_dependencias`.`comision_por_encargo`) ) AS 'comision',
				(`creditos_destino_detallado`.`monto` - ((IF( ISNULL(`socios_aeconomica_dependencias`.`comision_por_encargo`), 0,  `socios_aeconomica_dependencias`.`comision_por_encargo`) ) * `creditos_destino_detallado`.`monto`))
				AS `monto_de_cheque`,
				`creditos_destino_detallado`.`observaciones`
			FROM
				`creditos_destino_detallado` `creditos_destino_detallado` 
					INNER JOIN `personas` `personas` 
					ON `creditos_destino_detallado`.`clave_de_persona` = `personas`.`codigo` 
						INNER JOIN `creditos_destinos` `creditos_destinos` 
						ON `creditos_destino_detallado`.`clave_de_destino` = 
						`creditos_destinos`.`idcreditos_destinos` 
							INNER JOIN `creditos_solicitud` `creditos_solicitud` 
							ON `creditos_destino_detallado`.`credito_vinculado` = 
							`creditos_solicitud`.`numero_solicitud` 
								LEFT OUTER JOIN `socios_aeconomica_dependencias` 
								`socios_aeconomica_dependencias` 
								ON `creditos_destino_detallado`.`clave_de_empresa` = 
								`socios_aeconomica_dependencias`.`clave_de_persona` 
			WHERE
				(`creditos_destino_detallado`.`estado_actual` = $estado) 
				AND
				(`creditos_destino_detallado`.`clave_de_empresa` = $proveedor)
		AND `creditos_solicitud`.`saldo_actual` > " . TOLERANCIA_SALDOS . " ";
		return $sql;
	}
	function getListadoDePersonasConPresupuestoPorPagar(){

		//	`creditos_destino_detallado`.`estado_actual`,	`creditos_destino_detallado`.`clave_de_empresa`,
		$sql	= "SELECT
					`personas`.`codigo`,
					`personas`.`nombre`,
					SUM(`creditos_destino_detallado`.`monto`),
					`creditos_destino_detallado`.`clave_de_empresa`,
					COUNT(`creditos_solicitud`.`numero_solicitud`)
				
				FROM
					`creditos_destino_detallado` `creditos_destino_detallado` 
						INNER JOIN `personas` `personas` 
						ON `creditos_destino_detallado`.`clave_de_empresa` = `personas`.`codigo` 
							INNER JOIN `creditos_solicitud` `creditos_solicitud` 
							ON `creditos_destino_detallado`.`credito_vinculado` = 
							`creditos_solicitud`.`numero_solicitud` 
				WHERE
					(`creditos_destino_detallado`.`estado_actual` =0) 
					AND `creditos_solicitud`.`saldo_actual` > " . TOLERANCIA_SALDOS . " 
				GROUP BY
					`creditos_destino_detallado`.`clave_de_empresa`
		 ";
		return $sql;
	}	
	
	function getListadoDeParcialidades($idrecibo = 0){
		$sql = "SELECT
			`operaciones_mvtos`.`periodo_socio`         AS `periodo`,
			`operaciones_mvtos`.`recibo_afectado`,
			MAX(`operaciones_mvtos`.`fecha_afectacion`) AS `fecha`,
			
			SUM(
			IF((`operaciones_mvtos`.`tipo_operacion` = " . OPERACION_CLAVE_PLAN_CAPITAL . "), `operaciones_mvtos`.`afectacion_real`, 0)
			)  AS `capital`,
			
			SUM(
			IF((`operaciones_mvtos`.`tipo_operacion` = " . OPERACION_CLAVE_PLAN_INTERES . "), `operaciones_mvtos`.`afectacion_real`, 0)
			)  AS `interes`,
				
			SUM(
			IF((`operaciones_mvtos`.`tipo_operacion` = " . OPERACION_CLAVE_PLAN_AHORRO . "), `operaciones_mvtos`.`afectacion_real`, 0)
			)  AS `ahorro`,
			
			SUM(
			IF((`operaciones_mvtos`.`tipo_operacion` = " . OPERACION_CLAVE_PLAN_IVA . "), `operaciones_mvtos`.`afectacion_real`, 0)
			)  AS `iva`,
		
			SUM(
			IF(((`operaciones_mvtos`.`tipo_operacion` < " . OPERACION_CLAVE_PLAN_CAPITAL . " OR `operaciones_mvtos`.`tipo_operacion` > " . OPERACION_CLAVE_PLAN_IVA . ") AND `operaciones_mvtos`.`valor_afectacion` >0 ), (`operaciones_mvtos`.`valor_afectacion` * `operaciones_mvtos`.`afectacion_real`), 0)
			)  AS `otros`,	
			
			SUM(
			IF(((`operaciones_mvtos`.`tipo_operacion` < " . OPERACION_CLAVE_PLAN_CAPITAL . " OR `operaciones_mvtos`.`tipo_operacion` > " . OPERACION_CLAVE_PLAN_IVA . ") AND `operaciones_mvtos`.`valor_afectacion` <0 ), (`operaciones_mvtos`.`valor_afectacion` * `operaciones_mvtos`.`afectacion_real`), 0)
			)  AS `descuentos`,	
				
			SUM(`operaciones_mvtos`.`valor_afectacion` * `operaciones_mvtos`.`afectacion_real`)  AS `total`,
			
			MAX(`operaciones_mvtos`.`saldo_actual`)     AS `saldo` 
		FROM
			`operaciones_mvtos` `operaciones_mvtos` 
		WHERE
			(`operaciones_mvtos`.`recibo_afectado` = $idrecibo) 
		GROUP BY
			`operaciones_mvtos`.`periodo_socio` 
		ORDER BY `operaciones_mvtos`.`periodo_socio` ";
		
		
		return $sql;
	}
	function getListadoDeReferenciasBancarias($persona){
		$sql	= "SELECT
			`socios_relaciones`.`idsocios_relaciones` AS `clave` ,
			`socios_relaciones`.`nombres`           AS `nombre`,
			`socios_relaciones`.`fecha_nacimiento`  AS `fecha_de_emision`,
			`socios_relaciones`.`monto_relacionado` AS `limite_de_credito`,
			`socios_relaciones`.`dato_extra_1`      AS `tipo_de_cuenta`,
			`socios_relaciones`.`dato_extra_2`      AS `numero_de_cuenta`,
			`socios_relaciones`.`dato_extra_3`      AS `numero_de_tarjeta`
		FROM
			`socios_relaciones` 
		WHERE
			(`socios_relaciones`.`estatus` !=0)
			AND	(`socios_relaciones`.`tipo_relacion` =" . PERSONAS_REL_REF_BANCARIA . ")
			AND (`socios_relaciones`.`socio_relacionado` = $persona)";
		return $sql;
	}
	function getListadoDeReferenciasComerciales($persona){
		$sql	= "SELECT
			`socios_relaciones`.`idsocios_relaciones` AS `clave` ,
			`socios_relaciones`.`nombres`             AS `nombre`,
			`socios_relaciones`.`domicilio_completo`  AS `domicilio`,
			`socios_relaciones`.`telefono_residencia` AS `telefono` 
		FROM
		`socios_relaciones`
		WHERE
		(`socios_relaciones`.`estatus` !=0)
		AND	(`socios_relaciones`.`tipo_relacion` =" . PERSONAS_REL_REF_COMERCIAL . ")
		AND	(`socios_relaciones`.`socio_relacionado` = $persona)";
		return $sql;
	}
	function getListadoDeSucursales(){
		$sql	= "SELECT
			`general_sucursales`.`codigo_sucursal`               AS `clave`,
			`general_sucursales`.`nombre_sucursal`               AS `nombre`,
			`socios`.`nombre`                                    AS `persona`,
			`general_sucursales`.`clave_numerica`                AS `clave_en_numero`,
			`contable_centrodecostos`.`nombre_centrodecostos`    AS `centro_de_costo`,
			`socios_cajalocal`.`descripcion_cajalocal`           AS `caja_local`,
			`general_sucursales`.`hora_de_inicio_de_operaciones` AS `hora_inicial`,
			`general_sucursales`.`hora_de_fin_de_operaciones`    AS `hora_final` 
		FROM
			`general_sucursales` `general_sucursales` 
				INNER JOIN `socios_cajalocal` `socios_cajalocal` 
				ON `general_sucursales`.`caja_local_residente` = `socios_cajalocal`.
				`idsocios_cajalocal` 
					INNER JOIN `contable_centrodecostos` `contable_centrodecostos` 
					ON `general_sucursales`.`centro_de_costo` = 
					`contable_centrodecostos`.`idcontable_centrodecostos` 
						INNER JOIN `socios` `socios` 
						ON `general_sucursales`.`clave_de_persona` = `socios`.`codigo` ";
		return $sql;
	}
	function getListadoDeEntidadPerfilCuotas($arg1="", $otros=""){
		$sql	= "SELECT
				`entidad_pagos_perfil`.`identidad_pagos_perfil`          AS `clave`,
				`operaciones_tipos`.`descripcion_operacion`              AS `operacion`,
				`creditos_periocidadpagos`.`descripcion_periocidadpagos` AS `periocidad`,
				`entidad_pagos_perfil`.`monto`                           AS `monto`,
				`entidad_pagos_perfil`.`rotacion`                           AS `rotacion`
			FROM
				`entidad_pagos_perfil` `entidad_pagos_perfil` 
					INNER JOIN `operaciones_tipos` `operaciones_tipos` 
					ON `entidad_pagos_perfil`.`tipo_de_operacion` = `operaciones_tipos`.
					`idoperaciones_tipos` 
						INNER JOIN `creditos_periocidadpagos` `creditos_periocidadpagos` 
						ON `entidad_pagos_perfil`.`periocidad` = `creditos_periocidadpagos`.
						`idcreditos_periocidadpagos` 
			WHERE `entidad_pagos_perfil`.`identidad_pagos_perfil` >0 $otros
				";
		return $sql;
	}
	function getListadoDePersonaPerfilCuotas($persona){
		$sql	= "SELECT `personas_pagos_perfil`.`idpersonas_pagos_perfil` AS `clave`,
         `personas_pagos_perfil`.`clave_de_persona`,
         `operaciones_tipos`.`descripcion_operacion` AS `tipo_de_operacion`,
         `creditos_periocidadpagos`.`descripcion_periocidadpagos` AS `periocidad`,
         `personas_pagos_perfil`.`monto`,
         `personas_pagos_perfil`.`prioridad`,
         `personas_pagos_perfil`.`rotacion`,
         `personas_pagos_perfil`.`fecha_de_aplicacion` 
		FROM     `personas_pagos_perfil` 
		INNER JOIN `creditos_periocidadpagos`  ON `personas_pagos_perfil`.`periocidad` = `creditos_periocidadpagos`.`idcreditos_periocidadpagos` 
		INNER JOIN `operaciones_tipos`  ON `personas_pagos_perfil`.`tipo_de_operacion` = `operaciones_tipos`.`idoperaciones_tipos` 
		WHERE    ( `personas_pagos_perfil`.`clave_de_persona` = $persona ) LIMIT 0,100
		";
		return $sql;
	}
	function getListadoDePersonasPorDiaMembresia($dia = false){
		return $this->getListadoDePersonasPagoMembresia($dia);
	}
	function getListadoDePersonasPagoMembresia($dia = false, $otros = ""){
		$dia	= setNoMenorQueCero($dia);
		$ByDia	= ($dia >0) ? " AND (`personas_datos_colegiacion`.`dia_de_pago` =$dia) " : "";
		$sql	= "SELECT
		`personas_datos_colegiacion`.`dia_de_pago`,
		`personas_datos_colegiacion`.`clave_de_persona` AS `persona`,
		`personas`.`nombre`
		FROM
		`personas_datos_colegiacion` `personas_datos_colegiacion`
		INNER JOIN `personas` `personas`
		ON `personas_datos_colegiacion`.`clave_de_persona` = `personas`.`codigo`
		WHERE `personas_datos_colegiacion`.`clave_de_persona` > 0 $ByDia $otros";
		return $sql;
	}
	function getListadoDeCargosPorProductoCred($idproducto, $formato = false){
		$FF	= "	`creditos_productos_costos`.`unidad_de_medida`,	`creditos_productos_costos`.`editable`";
		if($formato == true){
			$FF	= "	getTrad(getBooleanMX(`creditos_productos_costos`.`unidad_de_medida`)) as 'es_porcentaje',
					getTrad(getBooleanMX(`creditos_productos_costos`.`editable`)) AS 'editable' ";
		}
		$sql		= "SELECT
		`creditos_productos_costos`.`idcreditos_productos_costos` AS `clave`,
		`operaciones_tipos`.`descripcion_operacion`               AS `cargo`,
		`creditos_productos_costos`.`unidades`,
		$FF
		FROM
		`operaciones_tipos` `operaciones_tipos`
		INNER JOIN `creditos_productos_costos` `creditos_productos_costos`
		ON `operaciones_tipos`.`idoperaciones_tipos` =
		`creditos_productos_costos`.`clave_de_operacion`
		WHERE
		(`creditos_productos_costos`.`clave_de_producto` =$idproducto)" ;
		//setLog($sql);
		return $sql;		
	}
	function getListadoDeOtrosDatosPorProductoCred($idproducto, $formato = false){
		$FF	= "";
		$idproducto	= setNoMenorQueCero($idproducto);
		$sql		= "SELECT
		`idcreditos_productos_otros_parametros` AS `clave`,
			`creditos_productos_otros_parametros`.`fecha_de_alta` AS `fecha_de_registro`,
			`creditos_productos_otros_parametros`.`fecha_de_expiracion`,
			`creditos_productos_otros_parametros`.`clave_del_parametro` AS `parametro`,
			`creditos_productos_otros_parametros`.`valor_del_parametro` AS `valor`
		FROM
			`creditos_productos_otros_parametros` 
		WHERE
			(`creditos_productos_otros_parametros`.`clave_del_producto` =$idproducto)" ;
		return $sql;
	}
	function getListadoDeEtapasPorProductoCred($idproducto){
		$idproducto	= setNoMenorQueCero($idproducto);
		
		$sql	= "SELECT   `creditos_productos_etapas`.`idcreditos_productos_etapas` AS `clave`,
         `creditos_tipoconvenio`.`descripcion_tipoconvenio` AS `producto`,
         `creditos_etapas`.`descripcion` AS `etapa`,
         `creditos_productos_etapas`.`nombre`,
         `creditos_productos_etapas`.`tags`,
         /*`creditos_productos_etapas`.`permisos`,*/
         `creditos_productos_etapas`.`orden`
		FROM     `creditos_productos_etapas` 
		INNER JOIN `creditos_etapas`  ON `creditos_productos_etapas`.`etapa` = `creditos_etapas`.`idcreditos_etapas` 
		INNER JOIN `creditos_tipoconvenio`  ON `creditos_productos_etapas`.`producto` = `creditos_tipoconvenio`.`idcreditos_tipoconvenio` 
		WHERE    ( `creditos_tipoconvenio`.`idcreditos_tipoconvenio` = $idproducto ) ORDER BY `creditos_productos_etapas`.`orden`";
		return $sql;
	}
	function getListadoDePromosPorProductoCred($idproducto){
		$idproducto	= setNoMenorQueCero($idproducto);
		$sql	= "SELECT `creditos_productos_promo`.`idcreditos_productos_promo` AS `clave`,
         `creditos_productos_promo`.`tipo_promocion` AS `tipo`,
         `operaciones_tipos`.`descripcion_operacion` AS `operacion`,
         `creditos_productos_promo`.`precio`,
         `creditos_productos_promo`.`descuento`,
         `creditos_productos_promo`.`num_items` AS `numero`,
         `creditos_productos_promo`.`sucursal`,
         `creditos_productos_promo`.`fecha_inicial`,
         `creditos_productos_promo`.`fecha_final`

		FROM     `creditos_productos_promo` 
		INNER JOIN `operaciones_tipos`  ON `creditos_productos_promo`.`tipo_operacion` = `operaciones_tipos`.`idoperaciones_tipos` 
		WHERE    ( `creditos_productos_promo`.`producto` = $idproducto )";
		
		return $sql;
	}
	function getListadoDeRequisitosPorProductoCred($idproducto){
		$idproducto	= setNoMenorQueCero($idproducto);
		$sql	= "SELECT `creditos_productos_req`.`idcreditos_productos_req` AS `clave`,
         /*`creditos_productos_req`.`producto`
         `creditos_productos_req`.`clave`,
         `creditos_productos_req`.`tipo_req` AS `tipo`,*/
         `creditos_productos_req`.`descripcion`,
         `creditos_productos_req`.`numero` AS `orden`
         
		FROM     `creditos_productos_req`
		WHERE    ( `creditos_productos_req`.`producto` = $idproducto )";
		return $sql;
	}
	function getListadoDeOperacionesPorBase($Base = false, $Persona = false, $Documento = false, $FechaInicial = false, $FechaFinal = false){
		$ByFechas	= $this->OFiltro()->OperacionesPorFecha($FechaInicial, $FechaFinal);
		$ByPersona	= $this->OFiltro()->OperacionesPorPersona($Persona);
		$ByDoc		= $this->OFiltro()->OperacionesPorDocumento($Documento);
		
		$sql	= "			SELECT operaciones_mvtos.idoperaciones_mvtos 				AS `control`,
				operaciones_mvtos.fecha_operacion 				AS `fecha`,
				operaciones_mvtos.recibo_afectado 				AS `recibo`,
				operaciones_mvtos.periodo_socio 				AS `parcialidad`,
				operaciones_tipos.descripcion_operacion 			AS `operacion`,
				(`operaciones_mvtos`.`afectacion_real` * 
				`operaciones_mvtos`.`valor_afectacion`) 			AS `monto`,
				
				`operaciones_recibos`.`total_operacion` 			AS `total_recibo`,
				`operaciones_recibos`.`tipo_pago`       			AS `tipo_de_pago`,
				
				IF(TRIM(`operaciones_mvtos`.`detalles`) = '',`operaciones_recibos`.`observacion_recibo`, `operaciones_mvtos`.`detalles`)			AS `observaciones`, 
				`operaciones_mvtos`.`tipo_operacion`				AS `tipo_de_operacion`,
				IF(`operaciones_mvtos`.`afectacion_real` >=1, `afectacion_real`,0) AS `activo`,
				IF(`operaciones_mvtos`.`afectacion_real` >=1, 0,`afectacion_real`) AS `pasivo`
				FROM
				`operaciones_mvtos` `operaciones_mvtos`
				INNER JOIN `operaciones_tipos` `operaciones_tipos`
				ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.
				`idoperaciones_tipos`
				INNER JOIN `operaciones_recibos` `operaciones_recibos`
				ON `operaciones_mvtos`.`recibo_afectado` = `operaciones_recibos`.
				`idoperaciones_recibos`
				INNER JOIN `operaciones_recibostipo` `operaciones_recibostipo`
				ON `operaciones_recibostipo`.`idoperaciones_recibostipo` =
				`operaciones_recibos`.`tipo_docto`
				INNER JOIN `eacp_config_bases_de_integracion_miembros`
				`eacp_config_bases_de_integracion_miembros`
				ON `eacp_config_bases_de_integracion_miembros`.`miembro` =
				`operaciones_mvtos`.`tipo_operacion`
				WHERE
				(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = $Base)
				$ByFechas
				$ByPersona
				$ByDoc
				ORDER BY 
				`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
				operaciones_mvtos.fecha_operacion,
				operaciones_mvtos.recibo_afectado,
				operaciones_mvtos.tipo_operacion,
				operaciones_mvtos.periodo_socio";
		return $sql;		
	}
	function getListadoDeIngresos($FechaInicial, $FechaFinal = false, $tipodepago="", $empresa = 0, $producto = 0, $IncEstadistico = true, $OtrosFiltros=""){
		$ByFechas		= $this->OFiltro()->RecibosPorFechaDeRegistro($FechaInicial, $FechaFinal);
		$ByTipoPago		= $this->OFiltro()->RecibosPorTipoDePago($tipodepago);
		$ByEmpresa		= $this->OFiltro()->RecibosPorPersonaAsociada($empresa);
		$ByEstats		= ($IncEstadistico == false) ? "" : " AND (`operaciones_recibos`.`tipo_pago` != '" . TESORERIA_COBRO_NINGUNO .  "' ) ";
		$ByProducto		= $this->OFiltro()->CreditosPorProducto($producto);
		$sql 	= "SELECT 
	  `socios`.`iddependencia`                     AS `clave_empresa`,
	  `socios`.`dependencia`                       AS `empresa`,
	  `socios`.`codigo`                            AS `codigo`,
	  `socios`.`nombre`                            AS `nombre`,
	  `creditos_solicitud`.`tipo_convenio`         AS `producto`,
	  `creditos_solicitud`.`numero_solicitud`      AS `credito`,
	  `operaciones_mvtos`.`fecha_operacion`        AS `fecha`,
	  /*`operaciones_tipos`.`tipo_operacion`         AS `clave_de_operacion`,*/
	  `operaciones_tipos`.`descripcion_operacion`  AS `operacion`,
	  (IF(`eacp_config_bases_de_integracion_miembros`.`subclasificacion` = 2003,`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`,0)) AS `capital`,
	  (IF(`eacp_config_bases_de_integracion_miembros`.`subclasificacion` = 2110,`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`,0)) AS `interes_normal`,
	  (IF(`eacp_config_bases_de_integracion_miembros`.`subclasificacion` = 2210,`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`,0)) AS `interes_moratorio`,
	
	  (IF(`eacp_config_bases_de_integracion_miembros`.`subclasificacion` = 7021,`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`,0)) AS `iva`,
	
	  (IF(`eacp_config_bases_de_integracion_miembros`.`subclasificacion` = 10001,`operaciones_mvtos`.`afectacion_real` * `eacp_config_bases_de_integracion_miembros`.`afectacion`,0)) AS `otros`,
	  `operaciones_recibos`.`tipo_pago`            AS `tipo_de_pago`,
	  `operaciones_mvtos`.`periodo_socio`          AS `parcialidad`,
		`creditos_solicitud`.`periocidad_de_pago`      AS `periocidad`,
	`tmp_recibos_datos_bancarios`.`banco` /*,
	
	`creditos_solicitud`.`oficial_seguimiento` AS `oficial_de_seguimiento`,
	`creditos_solicitud`.`oficial_credito`     AS `oficial_de_credito`,
	`operaciones_recibos`.`persona_asociada`            AS `persona_asociada`*/
	
	FROM 
	
		`operaciones_recibos` `operaciones_recibos` 
			LEFT OUTER JOIN `tmp_recibos_datos_bancarios` `tmp_recibos_datos_bancarios` 
			ON `operaciones_recibos`.`idoperaciones_recibos` = 
			`tmp_recibos_datos_bancarios`.`recibo` 
				INNER JOIN `operaciones_mvtos` `operaciones_mvtos` 
				ON `operaciones_mvtos`.`recibo_afectado` = `operaciones_recibos`.
				`idoperaciones_recibos` 
					INNER JOIN `creditos_solicitud` `creditos_solicitud` 
					ON `operaciones_mvtos`.`docto_afectado` = `creditos_solicitud`.
					`numero_solicitud` 
						INNER JOIN `socios` `socios` 
						ON `creditos_solicitud`.`numero_socio` = `socios`.`codigo` 
							INNER JOIN `operaciones_tipos` `operaciones_tipos` 
							ON `operaciones_mvtos`.`tipo_operacion` = 
							`operaciones_tipos`.`idoperaciones_tipos` 
								INNER JOIN 
								`eacp_config_bases_de_integracion_miembros` 
								`eacp_config_bases_de_integracion_miembros` 
								ON `operaciones_tipos`.`idoperaciones_tipos` = 
								`eacp_config_bases_de_integracion_miembros`.
								`miembro` 
									INNER JOIN `operaciones_recibostipo` 
									`operaciones_recibostipo` 
									ON `operaciones_recibos`.`tipo_docto` = 
									`operaciones_recibostipo`.
									`idoperaciones_recibostipo`
	
	WHERE (`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 10001)
		AND (`operaciones_recibostipo`.`mostrar_en_corte` <> '0')
		$ByFechas
		$ByTipoPago
		$ByEmpresa
		$ByEstats
		$ByProducto
		$OtrosFiltros
	ORDER BY `eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,`operaciones_mvtos`.`fecha_operacion`,`socios`.`iddependencia`,`socios`.`nombre`";
		
		return $sql;
	}
	function getListadoDeDireccionesPorPer($persona, $tipo = false,  $estado = 0){
		$persona	= setNoMenorQueCero($persona);
		$ByTipo		= $this->OFiltro()->DomiciliosPorTipo($tipo);
		$ByEstat	= $this->OFiltro()->DomiciliosPorStatus($estado);
		
		$sql 	= "SELECT `idsocios_vivienda` AS 'clave',
		CONCAT(`tipo_de_acceso`, ' ', `calle`, ' No. ',
		`numero_exterior`, ' ', `numero_interior`, ' Col. ',
		`colonia`, ', ', `localidad`, ', ', estado)
		AS 'domicilio',
		getBooleanMX(socios_vivienda.principal) AS 'principal'
		FROM socios_vivienda WHERE socio_numero=$persona $ByTipo $ByEstat ";
		
		return $sql;
	}
	function getListadoDePersonasConsultasL($persona, $tipo = false,  $estado = 0){
		$persona	= setNoMenorQueCero($persona);
		$ByTipo		= ($tipo == false) ? "" : " AND `personas_consulta_lista`.`tipo` = '$tipo' ";
		$sql 	= "SELECT
			`personas_consulta_lista`.`idpersonas_consulta_lista` AS `clave`,
			`personas_consulta_lista`.`fecha`,
			`personas_consulta_lista`.`tipo`,
			`personas_consulta_lista`.`proveedor`,
			`personas_consulta_lista`.`textocoincidente` AS `nombre`,
			`usuarios`.`nombreusuario`                            AS `usuario`
			/*getBooleanMX(`personas_consulta_lista`.`coincidente`) AS `coincidente`*/
		FROM
			`personas_consulta_lista` `personas_consulta_lista` 
				INNER JOIN `usuarios` `usuarios` 
				ON `personas_consulta_lista`.`idusuario` = `usuarios`.`idusuarios`
		WHERE
			(`personas_consulta_lista`.`persona` =$persona) $ByTipo ";
	
		return $sql;
	}
	/**
	 * Consulta en Lista Negra Interna
	 * @param integer $persona
	 * @return string
	 */
	function getListadoDePersonasConsultasLInt($persona){
		$persona	= setNoMenorQueCero($persona);
		$ByPersona	= ($persona > DEFAULT_SOCIO) ? " AND (`aml_listanegra_int`.`persona`=$persona) " : "";
		$sql 	= "SELECT   `aml_listanegra_int`.`clave_interna`,
          `aml_listanegra_int`.`persona`,
         CONCAT(`socios_general`.`nombrecompleto`, ' ',
         `socios_general`.`apellidopaterno`, ' ',
         `socios_general`.`apellidomaterno`) AS `nombre`,
         `aml_listanegra_int`.`fecha_de_registro`,
         `aml_listanegra_int`.`fecha_de_vencimiento`,
         `entidad_niveles_de_riesgo`.`nombre_del_nivel` AS `riesgo`,
         `aml_risk_types`.`nombre_del_riesgo` AS `motivo`,
         `usuarios`.`alias` AS `usuario`,
         `aml_listanegra_int`.`observaciones`,
         GetBooleanMX( `aml_listanegra_int`.`estatus`) AS `estatus`
		FROM     `aml_listanegra_int` 
		INNER JOIN `entidad_niveles_de_riesgo`  ON `aml_listanegra_int`.`riesgo` = `entidad_niveles_de_riesgo`.`clave_de_nivel` 
		INNER JOIN `socios_general`  ON `aml_listanegra_int`.`persona` = `socios_general`.`codigo` 
		INNER JOIN `aml_risk_types`  ON `aml_listanegra_int`.`idmotivo` = `aml_risk_types`.`clave_de_control` 
		INNER JOIN `usuarios`  ON `aml_listanegra_int`.`idusuario` = `usuarios`.`idusuarios` WHERE `aml_listanegra_int`.`persona` > 0 $ByPersona ";
	
		return $sql;
	}
	/**
	 * Consulta en Lista Blanca Interna
	 * @param integer $persona
	 * @return string
	 */
	function getListadoDePersonasConsultasBInt($persona){
		$persona	= setNoMenorQueCero($persona);
		$ByPersona	= ($persona > DEFAULT_SOCIO) ? " AND (`aml_personas_descartadas`.`clave_de_persona`=$persona) " : "";
		$sql 	= "SELECT   `aml_personas_descartadas`.`idaml_personas_descartadas` AS `clave`,
         CONCAT( `socios_general`.`nombrecompleto`, ' ',
         `socios_general`.`apellidopaterno`,' ',
         `socios_general`.`apellidomaterno` ) AS `nombre`,
         `usuarios`.`nombrecompleto` AS `usuario`,
         `aml_personas_descartadas`.`fecha_de_captura`,
         `aml_personas_descartadas`.`fecha_de_vencimiento`,
         `aml_personas_descartadas`.`clave_de_motivo`
		FROM     `aml_personas_descartadas` 
		INNER JOIN `socios_general`  ON `aml_personas_descartadas`.`clave_de_persona` = `socios_general`.`codigo` 
		INNER JOIN `usuarios`  ON `aml_personas_descartadas`.`clave_de_oficial` = `usuarios`.`idusuarios` WHERE `aml_personas_descartadas`.`clave_de_persona` >0 $ByPersona ORDER BY `aml_personas_descartadas`.`fecha_de_captura` DESC ";
	
		return $sql;
	}
	function getListadoDeMatrizRiesgoV($valor = "", $clasificacion = ""){
		$ByClass	= ($clasificacion == SYS_TODAS OR $clasificacion == "") ? "": " AND (`aml_riesgo_matrices`.`clasificacion`='$clasificacion') ";
		$ByValor	= ($valor == SYS_TODAS OR $valor == "") ? "" : " AND (`aml_riesgo_matrices`.`nombre`='$valor') ";
		$sql 	= "SELECT `aml_riesgo_matrices`.`idaml_riesgo_matrices` AS `clave`,
         `aml_riesgo_matrices`.`nombre`,
         `aml_risk_catalog`.`descripcion` AS `tipo_de_riesgo`,
         `aml_riesgo_matrices`.`clasificacion`,
         `entidad_niveles_de_riesgo`.`nombre_del_nivel` AS `nivel_de_riesgo`,
         `riesgos_probabilidad`.`nombre_probabilidad` AS `probabilidad`,
         `riesgos_consecuencias`.`nombre_consecuencia` AS `consecuencia`,
         `aml_riesgo_matrices`.`finalizador`,
         `aml_riesgo_matrices`.`impacto`
		FROM     `aml_riesgo_matrices` 
		INNER JOIN `riesgos_probabilidad`  ON `aml_riesgo_matrices`.`probabilidad` = `riesgos_probabilidad`.`idriesgos_probabilidad` 
		INNER JOIN `riesgos_consecuencias`  ON `aml_riesgo_matrices`.`consecuencia` = `riesgos_consecuencias`.`idriesgos_consecuencias` 
		INNER JOIN `aml_risk_catalog`  ON `aml_riesgo_matrices`.`clave_riesgo` = `aml_risk_catalog`.`clave_de_control` 
		INNER JOIN `entidad_niveles_de_riesgo`  ON `aml_riesgo_matrices`.`riesgo` = `entidad_niveles_de_riesgo`.`clave_de_nivel`
		WHERE ( `aml_riesgo_matrices`.`estatus` = 1 ) $ByClass $ByValor ";
		
		return $sql;
	}
	function getListadoDeCatalogoRiesgos(){
		$sql 	= "SELECT `aml_risk_catalog`.`clave_de_control`,
         `aml_risk_catalog`.`descripcion`,
         `aml_risk_types`.`nombre_del_riesgo` AS `tipo_de_riesgo`,
         ROUND(`aml_risk_catalog`.`valor_ponderado`,2) AS `valor_ponderado`,
         ROUND(`aml_risk_catalog`.`unidades_ponderadas`,2) AS `unidades_ponderadas`,

		`riesgos_chequeo`.`nombre_chequeo` AS `frecuencia_de_chequeo`,
         `riesgos_reporte`.`nombre_reporte` AS `forma_de_reportar`,
         `riesgos_medidas`.`nombre_medida` AS `unidad_de_medida`
		FROM `aml_risk_catalog`
				INNER JOIN `aml_risk_types`  ON `aml_risk_catalog`.`tipo_de_riesgo` = `aml_risk_types`.`clave_de_control`
				INNER JOIN `riesgos_chequeo`  ON `aml_risk_catalog`.`frecuencia_de_chequeo` = `riesgos_chequeo`.`eq_aml`
				INNER JOIN `riesgos_reporte`  ON `aml_risk_catalog`.`forma_de_reportar` = `riesgos_reporte`.`eq_aml` ,`riesgos_medidas`";
	
		return $sql;
	}
	function getListadoDeLeasingSolicitudes($originador = 0, $suboriginador =0, $persona = false){
		$ByOriginador	= $this->OFiltro()->LeasignSolicitaPorOriginador($originador);
		$BySub			= $this->OFiltro()->LeasignSolicitaPorSubOriginador($suboriginador);
		
		$sql			= "SELECT   `originacion_leasing`.`idoriginacion_leasing` AS `clave`,
         `originacion_leasing`.`fecha_origen` AS `fecha`,
         `originacion_leasing`.`nombre_cliente` 		AS `cliente`,
         `originacion_leasing`.`nombre_atn` 			AS `atn`,
         `originacion_leasing`.`persona` 				AS `clave_de_persona`,
         `originacion_leasing`.`credito` 				AS `clave_de_credito`,
         `originacion_leasing`.`oficial` 				AS `clave_de_oficial`,
         `originacion_leasing`.`paso_proceso` 			AS `proceso`,
         `vehiculos_marcas`.`nombre_marca` 				AS `marca`,
         `vehiculos_usos`.`descripcion_uso` 			AS `uso`,
         `vehiculos_segmento`.`nombre_segmento` 		AS `segmento`,
         `vehiculos_gps`.`nombre_gps` 					AS `paquetesgps`,
         `leasing_tipo_rac`.`nombre_tipo_rac` 			AS `tipo_de_rac`,
         `leasing_usuarios`.`originador` 				AS `originador`,
         `leasing_originadores`.`tipo_de_originador` 	AS `suboriginador`,
         `originacion_leasing`.`modelo`,
         `originacion_leasing`.`annio`,
         `originacion_leasing`.`precio_vehiculo`,
         `originacion_leasing`.`comision_originador`,
         `originacion_leasing`.`comision_apertura`,
         `originacion_leasing`.`tasa_iva`,
         `originacion_leasing`.`tasa_compra`,
         `originacion_leasing`.`financia_seguro`,
         `originacion_leasing`.`financia_tenencia`,
         `originacion_leasing`.`domicilia`,
         `originacion_leasing`.`describe_aliado`,
         `originacion_leasing`.`plazo`,
         `originacion_leasing`.`tasa_credito`,
         `originacion_leasing`.`tasa_tiie`,
         `originacion_leasing`.`monto_aliado`,
         `originacion_leasing`.`monto_accesorios`,
         `originacion_leasing`.`monto_anticipo`,
         `originacion_leasing`.`monto_tenencia`,
         `originacion_leasing`.`monto_garantia`,
         `originacion_leasing`.`monto_mtto`,
         `originacion_leasing`.`monto_gps`,
         `originacion_leasing`.`monto_directo`,
         `originacion_leasing`.`monto_seguro`,
         `originacion_leasing`.`monto_placas`,
         `originacion_leasing`.`monto_gestoria`,
         `originacion_leasing`.`monto_notario`,
         `originacion_leasing`.`monto_residual`,
         `originacion_leasing`.`monto_comision`,
         `originacion_leasing`.`monto_originador`,
         `originacion_leasing`.`total_credito`,
         `originacion_leasing`.`monto_directo`,
		
`leasing_usuarios`.`nombre` AS `nombre_suboriginador`,
`leasing_originadores`.`nombre_originador` AS `nombre_originador`

			FROM     `originacion_leasing` 
		INNER JOIN `vehiculos_marcas`  ON `originacion_leasing`.`marca` = `vehiculos_marcas`.`idvehiculos_marcas` 
		INNER JOIN `vehiculos_usos`  ON `originacion_leasing`.`tipo_uso` = `vehiculos_usos`.`idvehiculos_usos` 
		INNER JOIN `leasing_tipo_rac`  ON `originacion_leasing`.`tipo_rac` = `leasing_tipo_rac`.`idleasing_tipo_rac` 
		INNER JOIN `leasing_usuarios`  ON `originacion_leasing`.`suboriginador` = `leasing_usuarios`.`idleasing_usuarios` 
		INNER JOIN `leasing_originadores`  ON `originacion_leasing`.`originador` = `leasing_originadores`.`idleasing_originadores` 
		INNER JOIN `vehiculos_segmento`  ON `originacion_leasing`.`segmento` = `vehiculos_segmento`.`idvehiculos_segmento` 
		INNER JOIN `vehiculos_gps`  ON `originacion_leasing`.`tipo_gps` = `vehiculos_gps`.`idvehiculos_gps`
		WHERE `originacion_leasing`.`idoriginacion_leasing` > 0 $ByOriginador $BySub";
		
		//setLog($sql);
		return $sql;
	}
	function getListadoDeLeasingUsuarios($originador = 0, $idusuario =0){
		$ByOriginador	= $this->OFiltro()->LeasignUsuariosPorOriginador($originador);
		$BySub			= $this->OFiltro()->LeasignUsuariosPorClave($idusuario);
	
		$sql 	= "SELECT   `leasing_usuarios`.`idleasing_usuarios` AS `clave`,
		`leasing_usuarios`.`nombre`, `leasing_usuarios`.`correo_electronico`,
		getBooleanMX( `leasing_usuarios`.`estatus`) AS `activo`,
		getBooleanMX( `leasing_usuarios`.`administrador`) AS `administrador`
		FROM   `leasing_usuarios` WHERE `leasing_usuarios`.`idleasing_usuarios` >0 $ByOriginador $BySub";
		
		return $sql;
	}
	function getListadoDeRiesgoPorPdto(){
		$sql = "SELECT   `aml_riesgo_producto`.`idaml_riesgo_producto` AS `clave`,
         'CREDITO' AS `tipo`,
         `creditos_tipoconvenio`.`descripcion_tipoconvenio` AS `producto`,
         `entidad_niveles_de_riesgo`.`nombre_del_nivel` AS `riesgo`,
         `aml_riesgo_producto`.`observaciones`
		FROM     `aml_riesgo_producto` 
		INNER JOIN `creditos_tipoconvenio`  ON `aml_riesgo_producto`.`clave_de_producto` = `creditos_tipoconvenio`.`idcreditos_tipoconvenio` 
		INNER JOIN `entidad_niveles_de_riesgo`  ON `aml_riesgo_producto`.`nivel_de_riesgo` = `entidad_niveles_de_riesgo`.`clave_de_nivel` 
		WHERE ( `aml_riesgo_producto`.`tipo_de_producto` = " . iDE_CREDITO . " )
		UNION
		SELECT   `aml_riesgo_producto`.`idaml_riesgo_producto`,
			'CAPTACION' AS `tipo`,
		         `captacion_subproductos`.`descripcion_subproductos`,
		         `entidad_niveles_de_riesgo`.`nombre_del_nivel`,
		         `aml_riesgo_producto`.`observaciones`
		FROM     `aml_riesgo_producto` 
		INNER JOIN `entidad_niveles_de_riesgo`  ON `aml_riesgo_producto`.`nivel_de_riesgo` = `entidad_niveles_de_riesgo`.`clave_de_nivel` 
		INNER JOIN `captacion_subproductos`  ON `aml_riesgo_producto`.`clave_de_producto` = `captacion_subproductos`.`idcaptacion_subproductos` 
		WHERE ( `aml_riesgo_producto`.`tipo_de_producto` = " . iDE_CAPTACION . " )";
		return $sql;
	}
	function getListadoDeFlujoEfvoCred($credito){
		$sql = "SELECT   `creditos_flujoefvo`.`idcreditos_flujoefvo` AS `clave`,
         `creditos_tflujo`.`descripcion_tflujo` AS `tipo`,
         `creditos_origenflujo`.`descripcion_origenflujo` AS `origen`,
         `creditos_periocidadflujo`.`descripcion_periocidadflujo` AS `periocidad`,
         `creditos_flujoefvo`.`descripcion_completa` AS `descripcion`,
         `creditos_flujoefvo`.`monto_flujo` AS `monto`,
         `creditos_flujoefvo`.`afectacion_neta` AS `neto`
	FROM     `creditos_flujoefvo` 
	INNER JOIN `creditos_periocidadflujo`  ON `creditos_flujoefvo`.`periocidad_flujo` = `creditos_periocidadflujo`.`idcreditos_periocidadflujo` 
	INNER JOIN `creditos_tflujo`  ON `creditos_flujoefvo`.`tipo_flujo` = `creditos_tflujo`.`idcreditos_tflujo` 
	INNER JOIN `creditos_origenflujo`  ON `creditos_flujoefvo`.`origen_flujo` = `creditos_origenflujo`.`idcreditos_origenflujo` 
	WHERE    ( `creditos_flujoefvo`.`solicitud_flujo` = $credito )";
		return $sql;
	}
	function getListadoDeLeasingPlanCliente($credito){
		$sql	= "SELECT   `leasing_rentas`.`idleasing_renta` AS `id`,
		`leasing_rentas`.`clave_leasing` AS `idleasing`,
		`leasing_rentas`.`credito`,
		`leasing_rentas`.`periodo`,
		`leasing_rentas`.`fecha`,
		`leasing_rentas`.`deducible`,
		`leasing_rentas`.`no_deducible` AS `nodeducible`,
		(`leasing_rentas`.`iva_no_ded` +      `leasing_rentas`.`iva_ded`) AS `iva`,
		`leasing_rentas`.`total`,
		`leasing_rentas`.`suma_pagos` AS `pagos`
		FROM     `leasing_rentas`
		WHERE    ( `leasing_rentas`.`credito` = $credito )
		ORDER BY `leasing_rentas`.`periodo` LIMIT 0,200";
		return $sql;
	}

	function getListadoDeLineasDeCred($persona = false){
		$persona	= setNoMenorQueCero($persona);
		$ByPersona	= ($persona > DEFAULT_SOCIO) ? " AND `creditos_lineas`.`numero_socio`=$persona " : "";
		$xDO		= new cCreditosDatosDeOrigen();
		$tipo_origen= $xDO->ORIGEN_LINEA;
		$sql		= "SELECT   `creditos_lineas`.`idcreditos_lineas` AS `clave`,
         `creditos_lineas`.`numero_socio` AS `clave_de_persona`,
         `personas`.`nombre`,
         `creditos_lineas`.`monto_linea` AS `monto_autorizado`,
         getMontoActualPorOrigen(`creditos_lineas`.`idcreditos_lineas`, $tipo_origen) AS `monto_ejercido`,
         `creditos_lineas`.`fecha_de_vencimiento`,
         `creditos_lineas`.`estado`
		FROM `personas` INNER JOIN `creditos_lineas`  ON `personas`.`codigo` = `creditos_lineas`.`numero_socio` WHERE `creditos_lineas`.`idcreditos_lineas`>0 $ByPersona ";
		return $sql;
	}

	function getListadoDeCuentasBancarias(){
		$sql = "SELECT   `bancos_cuentas`.`idbancos_cuentas` AS `clave`,
         `bancos_cuentas`.`descripcion_cuenta` AS `nombre`,
		`bancos_cuentas`.`tipo_de_cuenta` AS `tipo`,
         `bancos_cuentas`.`sucursal`,
         `bancos_entidades`.`nombre_de_la_entidad` AS `banco`,
         `bancos_cuentas`.`consecutivo_actual` AS `consecutivo`
		FROM     `bancos_cuentas` 
		INNER JOIN `bancos_entidades`  ON `bancos_cuentas`.`entidad_bancaria` = `bancos_entidades`.`idbancos_entidades` ";
		return $sql;
	}
}


class cSQLTabla {
	private $mTabla				= "";
	private $mLimit				= " LIMIT 0,100";
	private $mSql				= "";
	private $mWhere				= "";
	private $mCampoDesc			= "";
	private $mOnDuplicateKey	= "";
	private $mClavePrincipal	= "";
	
	public $TEMPRESAS_OPERACIONES = "empresas_operaciones";
	
	function __construct($tabla	= ""){	$this->mTabla	= $tabla;	}
	function init($tabla){		if ( $tabla == false ){ } else { $this->mTabla	= $tabla;	}	}
	function getCampos (){
		$arrCampos	= array(
		"operaciones_recibos" 		=> "idoperaciones_recibos, fecha_operacion, numero_socio, docto_afectado, tipo_docto, total_operacion, idusuario, observacion_recibo, cheque_afectador, cadena_distributiva, tipo_pago, indice_origen, grupo_asociado, recibo_fiscal, sucursal, eacp, clave_de_moneda, unidades_en_moneda, fecha_de_registro",
		"operaciones_mvtos" 		=> "idoperaciones_mvtos, fecha_operacion, fecha_afectacion, recibo_afectado, socio_afectado, docto_afectado, tipo_operacion, afectacion_real, afectacion_cobranza, afectacion_contable, valor_afectacion, fecha_vcto, estatus_mvto, codigo_eacp, periodo_socio, periodo_contable, periodo_cobranza, periodo_seguimiento, periodo_mensual, periodo_semanal, periodo_anual, saldo_anterior, saldo_actual, detalles, idusuario, afectacion_estadistica, docto_neutralizador, cadena_heredada, tasa_asociada, dias_asociados, grupo_asociado, sucursal ",
		
		"captacion_cuentas" 		=> "numero_cuenta, numero_socio, numero_grupo, numero_solicitud, tipo_cuenta, fecha_apertura, fecha_afectacion, fecha_baja, estatus_cuenta, saldo_cuenta, eacp, idusuario, inversion_fecha_vcto, inversion_periodo, tasa_otorgada, dias_invertidos, observacion_cuenta, origen_cuenta, tipo_titulo, tipo_subproducto, nombre_mancomunado1, nombre_mancomunado2, minimo_mancomunantes, saldo_conciliado, fecha_conciliada, sucursal, ultimo_sdpm, oficial_de_captacion, cuenta_de_intereses ",
		"bancos_operaciones" 		=> "idcontrol, tipo_operacion, numero_de_documento, cuenta_bancaria, recibo_relacionado, fecha_expedicion, beneficiario, monto_descontado, monto_real, estatus, idusuario, usuario_autorizo, eacp, sucursal, numero_de_socio ",
		"captacion_sdpm_historico" 	=> "idcaptacion_sdpm_historico, ejercicio, periodo, cuenta, fecha, dias, tasa, monto, recibo, numero_de_socio, sucursal ",

		"creditos_solicitud" 		=> "numero_solicitud, fecha_solicitud, fecha_autorizacion, monto_solicitado, monto_autorizado, numero_socio, docto_autorizacion, plazo_en_dias, numero_pagos, tasa_interes, periocidad_de_pago, tipo_credito, estatus_actual, tipo_autorizacion, oficial_credito, fecha_vencimiento, pagos_autorizados, dias_autorizados, periodo_solicitudes, destino_credito, idusuario, nivel_riesgo, saldo_actual, fecha_ultimo_mvto, tipo_convenio, interes_diario, saldo_vencido, ultimo_periodo_afectado, sdo_int_ant, periodo_notificacion, tasa_moratorio, observacion_solicitud, tasa_ahorro, grupo_asociado, descripcion_aplicacion, fecha_ministracion, contrato_corriente_relacionado, monto_parcialidad, oficial_seguimiento, fecha_castigo, saldo_conciliado, notas_auditoria, fecha_conciliada, sucursal, eacp, interes_normal_devengado, tipo_de_pago, interes_normal_pagado, interes_moratorio_devengado, interes_moratorio_pagado, fecha_mora, fecha_vencimiento_dinamico, tipo_de_calculo_de_interes, causa_de_mora ",
		"creditos_flujoefvo" 		=> "idcreditos_flujoefvo, solicitud_flujo, socio_flujo, tipo_flujo, origen_flujo, monto_flujo, afectacion_neta, periocidad_flujo, idusuario, observacion_flujo, descripcion_completa, sucursal, fecha_captura ",
		"creditos_garantias" 		=> "idcreditos_garantias, socio_garantia, solicitud_garantia, tipo_garantia, fecha_recibo, fecha_adquisicion, tipo_valuacion, monto_valuado, observaciones, documento_presentado, estatus_actual, fecha_resguardo, idusuario, propietario, fecha_devolucion, estado_presentado, idsocio_duenno, descripcion, sucursal, observaciones_del_resguardo, eacp ",
		"creditos_lineas" 			=> "idcreditos_lineas, numero_socio, monto_linea, observaciones, numerohipoteca, monto_hipoteca, fecha_de_vencimiento, fecha_de_alta, estado, idusuario, sucursal, eacp ",
		"creditos_sdpm_historico" 	=> "idcreditos_sdpm_historico, numero_de_socio, numero_de_credito, fecha_actual, fecha_anterior, dias_transcurridos, monto_calculado, saldo, estatus, interes_normal, interes_moratorio, tipo_de_operacion, sucursal ",
		"creditos_parametros_negociados" => "idcreditos_parametros_negociados, numero_de_socio, numero_de_credito, nombre_del_valor, valor_original, valor_negociado, fecha_de_negociacion, fecha_de_expiracion, idusuario, sucursal, estatus ",
		"creditos_productos_otros_parametros" => "idcreditos_productos_otros_parametros, clave_del_producto, clave_del_parametro, valor_del_parametro, fecha_de_alta, fecha_de_expiracion ",
		
		"creditos_tipoconvenio" 	=> "idcreditos_tipoconvenio, descripcion_tipoconvenio, tasa_ahorro, tipo_convenio, razon_garantia, creditos_mayores_a, porciento_garantia_liquida, monto_fondo_obligatorio, porcentaje_otro_credito, aplica_gastos_notariales, numero_creditos_maximo, dias_maximo, pagos_maximo, tipo_autorizacion, nivel_riesgo, porcentaje_ica, estatus_predeterminado, leyenda_docto_autorizacion, interes_normal, interes_moratorio, tolerancia_dias_no_pago, maximo_otorgable, tolerancia_dias_primer_abono, numero_avales, nivel_autorizacion_oficial, code_valoracion_javascript, minimo_otorgable, descripcion_completa, oficial_seguimiento, valoracion_php, tipo_de_credito, php_monto_maximo, tipo_de_convenio, tipo_de_garantia, estatus, tasa_iva, contable_cartera_vigente, contable_cartera_vencida, contable_intereses_devengados, contable_intereses_anticipados, contable_intereses_cobrados, contable_intereses_moratorios, iva_incluido, comision_por_apertura, codigo_de_contrato, contable_cartera_castigada, path_del_contrato, tipo_de_integracion, contable_intereses_vencidos, base_de_calculo_de_interes, capital_vencido_renovado, capital_vencido_reestructurado, capital_vencido_normal, capital_vigente_renovado, capital_vigente_reestructurado, capital_vigente_normal, interes_cobrado, moratorio_cobrado, interes_vencido_renovado, interes_vencido_reestructurado, interes_vencido_normal, interes_vigente_renovado, interes_vigente_reestructurado, interes_vigente_normal ",
		
		"seguimiento_compromisos" 	=> "idseguimiento_compromisos, socio_comprometido, oficial_de_seguimiento, fecha_vencimiento, hora_vencimiento, tipo_compromiso, anotacion, credito_comprometido, estatus_compromiso, sucursal, eacp, grupo_relacionado, lugar_de_compromiso ",
		"seguimiento_llamadas" 		=> "idseguimiento_llamadas, numero_socio, numero_solicitud, deuda_total, telefono_uno, telefono_dos, fecha_llamada, hora_llamada, observaciones, estatus_llamada, oficial_a_cargo, sucursal, eacp, grupo_relacionado ",
		"seguimiento_notificaciones" => "", /*pendiente de actualizar*/
		
		"socios_grupossolidarios" 	=> " idsocios_grupossolidarios, nombre_gruposolidario, colonia_gruposolidario, direccion_gruposolidario, representante_numerosocio, representante_nombrecompleto, grupo_solidario, vocalvigilancia_numerosocio, vocalvigilancia_nombrecompleto, estatusactual, nivel_ministracion, sucursal, fecha_de_alta ",		
		"socios_general" 			=> "codigo, nombrecompleto, apellidopaterno, apellidomaterno, rfc, curp, fechaentrevista, fechaalta, estatusactual, region, cajalocal, fechanacimiento, lugarnacimiento, tipoingreso, estadocivil, genero, eacp, observaciones, idusuario, grupo_solidario, personalidad_juridica, dependencia, regimen_conyugal, sucursal, fecha_de_revision, tipo_de_identificacion, documento_de_identificacion ",
		"socios_aeconomica" 		=> "idsocios_aeconomica, socio_aeconomica, tipo_aeconomica, sector_economico, nombre_ae, domicilio_ae, localidad_ae, municipio_ae, estado_ae, telefono_ae, extension_ae, numero_empleado, antiguedad_ae, departamento_ae, monto_percibido_ae, dependencia_ae, idusuario, fecha_alta, puesto, sucursal, fecha_de_verificacion, oficial_de_verificacion, estado_actual ",
		"socios_baja" 				=> "idsocios_baja, numero_de_socio, fecha_de_baja, razon_de_la_baja, observaciones_de_baja, sucursal ",
		"socios_firmas" 			=> "idsocios_firmas, numero_de_socio, tipo, firma, md5_src, idusuario, sucursal, fecha_carga, eacp ",
		"socios_relaciones" 		=> "idsocios_relaciones, socio_relacionado, credito_relacionado, tipo_relacion, numero_socio, nombres, apellido_paterno, apellido_materno, domicilio_completo, telefono_residencia, telefono_movil, fecha_nacimiento, monto_relacionado, porcentaje_relacionado, fecha_alta, curp, observaciones, idusuario, consanguinidad, estatus, dependiente, codigo, ocupacion, sucursal, eacp, calificacion_del_referente ",
		"socios_vivienda" 			=> "idsocios_vivienda, socio_numero, tipo_regimen, calle, numero_exterior, numero_interior, colonia, localidad, estado, municipio, telefono_residencial, telefono_movil, tiempo_residencia, referencia, idusuario, principal, tipo_domicilio, codigo_postal, fecha_alta, codigo, sucursal, eacp, coordenadas_gps, tipo_de_acceso, fecha_de_verificacion, oficial_de_verificacion, estado_actual, clave_de_localidad, clave_de_pais, nombre_de_pais, clave_interna_de_colonia ",
		"socios_memo" 				=> "idsocios_memo, numero_socio, numero_gposolidario, numero_solicitud, fecha_memo, texto_memo, tipo_memo, idusuario, sucursal, eacp ",
		"socios_patrimonio" 		=> "idsocios_patrimonio, socio_patrimonio, tipo_patrimonio, monto_patrimonio, afectacion_patrimonio, fecha_expiracion, observaciones, descripcion, documento_presentado, solicitud_relacionada, estatus_actual, codigo, sucursal, eacp, idusuario, fecha_de_alta ",
		
		"general_colonias"			=> "idgeneral_colonia, codigo_postal, nombre_colonia, tipo_colonia, estado_colonia, ciudad_colonia, municipio_colonia, fecha_de_revision, codigo_de_estado, codigo_de_municipio, sucursal ",
		"general_municipios"		=> "idgeneral_municipios, clave_de_entidad, clave_de_municipio, nombre_del_municipio, habitantes, indice_de_marginacion, grado_de_marginacion, lugar_nacional ",
		"general_estados"			=> "idgeneral_estados, clave_alfanumerica, clave_numerica, nombre, clave_en_sic ",
		"general_sucursales"		=> "codigo_sucursal, nombre_sucursal, gerente_sucursal, caja_local_residente, titular_de_cobranza, titular_de_seguimiento, titular_de_contabilidad, titular_de_inventarios, titular_de_control_interno, titular_de_nominas, titular_de_cumplimiento, hora_de_inicio_de_operaciones, hora_de_fin_de_operaciones, calle, numero_exterior, numero_interior, colonia, codigo_postal, localidad, municipio, estado, telefono, fax ",
		
		"general_niveles"			=> "idgeneral_niveles, descripcion_del_nivel, task_events, work_time_range, rules_by_user ",
		
		"socios_figura_juridica"	=> "idsocios_figura_juridica, descripcion_figura_juridica ",
		
		"operaciones_tipos"			=> "idoperaciones_tipos, descripcion_operacion, clasificacion, subclasificacion, cuenta_contable, descripcion, recibo_que_afecta, tipo_operacion, visible_reporte, class_efectivo, mvto_que_afecta, afectacion_en_recibo, afectacion_en_notificacion, producto_aplicable, constituye_fondo_automatico, integra_vencido, afectacion_en_sdpm, cargo_directo, codigo_de_valoracion, periocidad_afectada, integra_parcialidad, es_estadistico, formula_de_calculo, formula_de_cancelacion, importancia_de_neutralizacion, preservar_movimiento, tasa_iva, nombre_corto, estatus ",
		"creditos_destinos"			=> "idcreditos_destinos, descripcion_destinos, destino_credito, capital_vencido_renovado, capital_vencido_reestructurado, capital_vencido_normal, capital_vigente_renovado, capital_vigente_reestructurado, capital_vigente_normal, interes_vencido_renovado, interes_vencido_reestructurado, interes_vencido_normal, interes_vigente_renovado, interes_vigente_reestructurado, interes_vigente_normal, interes_cobrado, moratorio_cobrado "
		);
		return $arrCampos[ $this->mTabla ];
	}
	function getQueryInicial($tabla = false ){
		$this->init($tabla);
		$ql		= "";
		$xli	= new cSQLListas();
		switch( $this->mTabla ){
			case TCAPTACION_CUENTAS:
				$sql	= $xli->getInicialDeCuentas();
				break;
			case TCREDITOS_REGISTRO:
				$sql = $xli->getInicialDeCreditos();			
				break;
			case TPERSONAS_ACTIVIDAD_ECONOMICA:
				$sql	= $xli->getInicialPersonasActividadEc();
				break;
			case TPERSONAS_DIRECCIONES:
				$sql		= "SELECT `socios_vivienda`.*, `socios_vivienda`.`idsocios_vivienda`   AS 'id'	FROM socios_vivienda ";
				break;
			case TPERSONAS_RELACIONES:
				$sql	= $xli->getInicialPersonasRelaciones();
				break;
		}
		return $sql;
	}
	function getCamposSinClaveUnica(){
		return str_replace( $this->getClaveUnica() . ",", "", $this->getCampos() );
	}
	function getClaveUnica (){
		if($this->mClavePrincipal == ""){
		$arrClaves	= array(
		"operaciones_recibos" 		=> "idoperaciones_recibos",
		"operaciones_mvtos" 		=> "idoperaciones_mvtos",
		"socios_grupossolidarios" 	=> "idsocios_grupossolidarios",
		"creditos_flujoefvo" 		=> "idcreditos_flujoefvo",
		"captacion_cuentas" 		=> "numero_cuenta",
		"bancos_operaciones"		=> "idcontrol",
		
		"captacion_sdpm_historico" 	=> "idcaptacion_sdpm_historico",
		"creditos_solicitud" 		=> "numero_solicitud",
		"socios_firmas" 			=> "idsocios_firmas",
		"creditos_garantias" 		=> "idcreditos_garantias",
		"creditos_lineas" 			=> "idcreditos_lineas",
		"creditos_sdpm_historico" 	=> "idcreditos_sdpm_historico",
		"creditos_parametros_negociados" => "idcreditos_parametros_negociados",
		"creditos_productos_otros_parametros" => "idcreditos_productos_otros_parametros",
		
		"creditos_tipoconvenio" 	=> "idcreditos_tipoconvenio",
		
		"seguimiento_compromisos" 	=> "idseguimiento_compromisos",
		"seguimiento_llamadas" 		=> "idseguimiento_llamadas",
		"seguimiento_notificaciones" 	=> "idseguimiento_notificaciones",
		
		"socios_general"			=> "codigo",
		"socios_aeconomica" 		=> "idsocios_aeconomica",
		"socios_baja" 				=> "idsocios_baja",
		"socios_relaciones" 		=> "idsocios_relaciones",
		"socios_vivienda" 			=> "idsocios_vivienda",
		"socios_patrimonio"			=> "idsocios_patrimonio",
		"socios_memo" 				=> "idsocios_memo",
		
		"socios_figura_juridica" 	=> "idsocios_figura_juridica",
		
		"general_colonias"			=> "idgeneral_colonia",
		"general_municipios"		=> "idgeneral_municipios",
		"general_estados"			=> "idgeneral_estados",
		"general_sucursales"		=> "codigo_sucursal",
		
		"general_niveles"			=> "idgeneral_niveles",
		
		"operaciones_tipos"			=> "idoperaciones_tipos",
		"creditos_destinos"			=> "idcreditos_destinos"
		);
		$this->mClavePrincipal		= isset($arrClaves[ $this->mTabla ]) ? $arrClaves[ $this->mTabla ] : "";
		}
		return $this->mClavePrincipal;		
	}
	function getCampoSocio(){
		$arrTabK		= array(
							"bancos_operaciones" 		=> "numero_de_socio",
							"captacion_cuentas" 		=> "numero_socio",
							"captacion_sdpm_historico" 	=> "numero_de_socio",
							"socios_firmas" 			=> "numero_de_socio",
							"creditos_flujoefvo" 		=> "socio_flujo",
							"creditos_garantias" 		=> "socio_garantia",
							"creditos_lineas" 			=> "numero_socio",
							"creditos_sdpm_historico" 	=> "numero_de_socio",
							"creditos_solicitud" 		=> "numero_socio",
							"creditos_parametros_negociados" => "numero_de_socio",
							"operaciones_mvtos" 		=> "socio_afectado",
							"operaciones_recibos" 		=> "numero_socio",
							"seguimiento_compromisos" 	=> "socio_comprometido",
							"seguimiento_llamadas" 		=> "numero_socio",
							"seguimiento_notificaciones" => "socio_notificado",
							"socios_aeconomica" 		=> "socio_aeconomica",
							"socios_memo" 				=> "numero_socio",
							"socios_baja" 				=> "numero_de_socio",
							"socios_relaciones" 		=> "socio_relacionado",
							"socios_vivienda" 			=> "socio_numero",
							"socios_patrimonio" 		=> "socio_patrimonio",
		
							"socios_general" 			=> "codigo",
							"socios_grupossolidarios" 	=> "representante_numerosocio"
							);							
		return $arrTabK[ $this->mTabla ];		
	}
	function getCampoFechaPrincipal(){
		$arrTabK		= array(
							"bancos_operaciones" 		=> "fecha_expedicion",
							"captacion_cuentas" 		=> "fecha_apertura",
							"captacion_sdpm_historico" 	=> "fecha",
							"socios_firmas" 			=> "fecha_carga",
							"creditos_flujoefvo" 		=> "fecha_captura",
							"creditos_garantias" 		=> "fecha_recibo",
							"creditos_lineas" 			=> "fecha_de_alta",
							"creditos_sdpm_historico" 	=> "fecha_actual",
							"creditos_solicitud" 		=> "fecha_ultimo_mvto",
							"creditos_parametros_negociados" => "fecha_de_negociacion",
							"operaciones_mvtos" 		=> "fecha_operacion",
							"operaciones_recibos" 		=> "fecha_operacion",
							"seguimiento_compromisos" 	=> "fecha_vencimiento",
							"seguimiento_llamadas" 		=> "fecha_llamada",
							"seguimiento_notificaciones" => "fecha_notificacion",
							"socios_aeconomica" 		=> "fecha_alta",
							"socios_memo" 				=> "fecha_memo",
							"socios_baja" 				=> "fecha_de_baja",
							"socios_relaciones" 		=> "fecha_alta",
							"socios_vivienda" 			=> "fecha_alta",
							"socios_general" 			=> "fechaentrevista",
							"socios_patrimonio"			=> "fecha_de_alta",
		
							"socios_grupossolidarios" 	=> "fecha_de_alta",
							"general_colonias"			=> "fecha_de_revision"
							);							
		return $arrTabK[ $this->mTabla ];		
	}	
	function getCampoDocumento(){
		$arrTabK		= array(
							"bancos_operaciones" 		=> "numero_de_documento",
							"captacion_cuentas" 		=> "numero_cuenta",
							"captacion_sdpm_historico" 	=> "cuenta",
		
							"socios_firmas" 			=> "numero_de_socio",
							"creditos_flujoefvo" 		=> "socio_flujo",
							"creditos_garantias" 		=> "socio_garantia",
							"creditos_lineas" 			=> "numero_socio",
							"creditos_sdpm_historico" 	=> "numero_de_socio",
							"creditos_solicitud" 		=> "numero_socio",
							"creditos_parametros_negociados" => "numero_de_socio",
							"operaciones_mvtos" 		=> "socio_afectado",
							"operaciones_recibos" 		=> "numero_socio",
							"seguimiento_compromisos" 	=> "socio_comprometido",
							"seguimiento_llamadas" 		=> "numero_socio",
							"seguimiento_notificaciones" => "socio_notificado",
							"socios_aeconomica" 		=> "socio_aeconomica",
							"socios_memo" 				=> "numero_socio",
							"socios_baja" 				=> "numero_de_socio",
							"socios_relaciones" 		=> "socio_relacionado",
							"socios_vivienda" 			=> "socio_numero",
							"socios_general" 			=> "codigo",
		
							"socios_patrimonio"			=> "solicitud_relacionada",
		
							"socios_grupossolidarios" 	=> "representante_numerosocio"
							);							
		return $arrTabK[ $this->mTabla ];		
	}
	function getNombreRespaldo($FechaDeCorte = false, $RutaCompleta = true){
		$archivo	= ( $RutaCompleta == false ) ? getSucursal() . "-" . $FechaDeCorte . "-" . $this->mTabla . ".sbk" : PATH_BACKUPS . "" . getSucursal() . "-" . $FechaDeCorte . "-" . $this->mTabla . ".sbk";
		return $archivo;
	}
	function getTablasConOperaciones(){
		$arrTab		= array(
							"bancos_operaciones" => "bancos_operaciones",
							"captacion_cuentas" => "captacion_cuentas",
							"creditos_flujoefvo" => "creditos_flujoefvo",
							"creditos_garantias" => "creditos_garantias",
							"creditos_lineas" => "creditos_lineas",
							"creditos_sdpm_historico" => "creditos_sdpm_historico",
							"creditos_solicitud" => "creditos_solicitud",
							"creditos_parametros_negociados" => "creditos_parametros_negociados",
		
							"general_colonias" => "general_colonias",
		
							"operaciones_mvtos" => "operaciones_mvtos",
							"operaciones_recibos" => "operaciones_recibos",
							"seguimiento_compromisos" => "seguimiento_compromisos",
							"seguimiento_llamadas" => "seguimiento_llamadas",
							"seguimiento_notificaciones" => "seguimiento_notificaciones",
							"socios_aeconomica" => "socios_aeconomica",
							"socios_general" => "socios_general",
							"socios_memo" 				=> "socios_memo",
							"socios_baja" 				=> "socios_baja",
							"socios_relaciones" 		=> "socios_relaciones",
							"socios_vivienda" 			=> "socios_vivienda",
							"socios_patrimonio" 		=> "socios_patrimonio",
		
							"socios_grupossolidarios" 	=> "socios_grupossolidarios"
							);
		return $arrTab;
	}
	function getFrom(){
		return " FROM " .$this->mTabla;
	}
	function getSelect($Limitar = 100, $Desde = 0){
		$where	= ( $this->mWhere == "" ) ? "" : " WHERE " . $this->mWhere . " ";
		return "SELECT " . $this->getCampos() . $this->getFrom() . $where . $this->setLimit($Limitar, $Desde);  
	}
	function setLimit($Limitar = 100, $Desde = 0){
		$this->mLimit	= ($Limitar == 0) ? "" : " LIMIT $Desde, $Limitar ";
		return $this->mLimit;
	}
	function getCamposSelector(){
		
	}
	function getCondicionPorClave($mValueKey){
		//agregar valuacion del string
		//$mValueKey		= 
		return $this->getClaveUnica() . "= '$mValueKey' ";
	}
	function setWhere($mWhere){
		$this->mWhere	= $mWhere;
	}
	/**
	 * Genera un query INSERT segun valores dados
	 * @param string $valores
	 * @param string $campos
	 */
	function getInsert($valores, $campos = ""){
		$campos		= ( $campos == "" ) ? $this->getCampos() : $campos;
		$OnKeyExist	= ($this->mOnDuplicateKey == "") ? ""  : $this->mOnDuplicateKey;
		$this->mSql	= "INSERT INTO " . $this->mTabla . " ($campos) VALUES ($valores) $OnKeyExist";
		return $this->mSql;
	}
	function setUpdate($aParam){
		$sucess			= true;
		$sqlBody		= "";
		if ( is_array($aParam) AND count($aParam) >=1 ){
			$BodyUpdate = "";
			foreach ($aParam as $key => $value) {
				//Buscar en el Valor el Nombre del Field
				//$pos	= stripos($value, $key);
				//Si el Valor es una Cadena y no existe el Nombre del field
				if ( is_string($value)  ){
					$value		= "\"" . $value . "\"";
				}
				if ($BodyUpdate == ""){
					$BodyUpdate .= "$key = $value ";
				} else {
					$BodyUpdate .= ", $key = $value ";
				}
			}	//END FOREACH
			if(strlen($this->mWhere) > 4){
				$sqlBody	= "UPDATE " . $this->mTabla . " SET $BodyUpdate
						   WHERE " . $this->mWhere . " ";
				$x 			= my_query($sqlBody);
				$sucess	= $x["stat"];
			} else {
				$sucess	= false;
			}
		} else {
			$sucess	= false;
		}
		return $sucess;
	}
	function setEnClaveDuplicada($Accion){
		if($Accion != ""){
			$this->mOnDuplicateKey	= "ON DUPLICATE KEY UPDATE $Accion";
		}
	}

	function getCampoDescripcion(){ return $this->mCampoDesc; }
	
	function getCatalogoInArray(){
		$arrD		= array();
		$rs			= getRecordset($this->getSelect(0,100) );
		
		while($rw = mysql_fetch_array($rs)){
			$arrD[ $rw[$this->getClaveUnica()] ] = $rw[1];
		}
		return $arrD;
	}
	function obj(){
		$mObj	= null;
		switch($this->mTabla){
			
			case TPERSONAS_RELACIONES:
				$mObj	= new cSocios_relaciones();
				break;
			case TBANCOS_CUENTAS:
				$mObj	= new cBancos_cuentas();
				break;
			case TOPERACIONES_RECIBOS:
				$mObj	= new cOperaciones_recibos();
				break;
			case TOPERACIONES_MVTOS:
				$mObj	= new cOperaciones_mvtos();
				break;
			case TBANCOS_OPERACIONES:
				$mObj	= new cBancos_operaciones();
				break;
			case TBANCOS_CUENTAS:
				$mObj	= new cBancos_cuentas();
				break;
			case TCREDITOS_REGISTRO:
				$mObj	= new cCreditos_solicitud();
				break;
			case TCREDITOS_DESTINO_DETALLE:
				$mObj	= new cCreditos_destino_detallado();
				break;
			case "creditos_garantias":
				$mObj	= new cCreditos_garantias();
				$this->mCampoDesc = $mObj->descripcion()->get();
				break;
			case "creditos_productos_promo":
				$mObj	= new cCreditos_productos_promo();
				//$this->mCampoDesc = $mObj->condiciones()->get();
				break;
			case "creditos_productos_req":
				$mObj	= new cCreditos_productos_req();
				$this->mCampoDesc = $mObj->descripcion()->v();
				break;
			/* Tesoreria */
			case TTESORERIA_MVTOS:
				$mObj	= new cTesoreria_cajas_movimientos();
				break;
			case "tesoreria_monedas":
				$mObj	= new cTesoreria_monedas();
				$this->mCampoDesc = $mObj->nombre_de_la_moneda()->get();
				break;
			case "tesoreria_valoracion_diaria":
				$mObj	= new cTesoreria_valoracion_diaria();
				break;
			case TCATALOGOS_EMPRESAS:
				$mObj	= new cSocios_aeconomica_dependencias();
				$this->mCampoDesc = $mObj->nombre_corto()->get();
				break;
			case TCATALOGOS_RELACIONES:
				$mObj	= new cSocios_relacionestipos();
				$this->mCampoDesc = $mObj->descripcion_relacionestipos()->get();
				break;
			case TCATALOGOS_ACTIVIDADES_ECONOMICAS:
				$mObj	= new cPersonas_actividad_economica_tipos();
				$this->mCampoDesc = $mObj->nombre_de_la_actividad()->get();
				break;
			case TCATALOGOS_LOCALIDADES:
				$mObj	= new cCatalogos_localidades();
				$this->mCampoDesc = $mObj->nombre_de_la_localidad()->get();
				break;
			case TCAPTACION_CUENTAS:
				$mObj	= new cCaptacion_cuentas();
				break;
			case "captacion_subproductos":
				$mObj	= new cCaptacion_subproductos();
				$this->mCampoDesc = $mObj->descripcion_subproductos()->get();
				break;
			case "captacion_tasas":
				$mObj	= new cCaptacion_tasas();
				break;
			case "captacion_sdpm_historico":
				$mObj	= new cCaptacion_sdpm_historico();
				break;
			case TCATALOGOS_PAISES:
				$mObj	= new cPersonas_domicilios_paises();
				$this->mCampoDesc = $mObj->nombre_oficial()->get();
				break;				
			case TPERSONAS_DIRECCIONES:
				$mObj	= new cSocios_vivienda();
				break;
			case TPERSONAS_GENERALES:
				$mObj	= new cSocios_general();
				break;
			case TPERSONAS_PATRIMONIO:
				$mObj	= new cSocios_patrimonio();
				$this->mCampoDesc = $mObj->descripcion()->get();
				break;				
			case TPERSONAS_MEMOS:
				$mObj	= new cSocios_memo();
				break;
			case TPERSONAS_PERFIL_TRANSACCIONAL:
				$mObj	= new cPersonas_perfil_transaccional();
				break;
			case "personas_pagos_perfil":
				$mObj	= new cPersonas_pagos_perfil();
				break;
			case "personas_pagos_plan":
				$mObj	= new cPersonas_pagos_plan();
				break;
			case "personas_documentacion_tipos":
				$mObj				= new cPersonas_documentacion_tipos();
				$this->mCampoDesc 	= $mObj->nombre_del_documento()->get();
				break;
			case "personas_proveedores":
				$mObj				= new cPersonas_proveedores();
				break;
			case "socios_tipoingreso":
				$mObj				= new cSocios_tipoingreso();
				$this->mCampoDesc 	= $mObj->descripcion_tipoingreso()->v();
				break;
			//Seguimiento
			case TSEGUMIENTO_LLAMADAS:
				$mObj	= new cSeguimiento_llamadas();
				break;
			case TSYSTEM_LOG:
				$mObj	= new cGeneral_log();
				break;
			case "sistema_permisos":
				$mObj				= new cSistema_permisos();
				$this->mCampoDesc 	= $mObj->descripcion()->get();
				break;

				
			case TPERSONAS_ACTIVIDAD_ECONOMICA:
				$mObj	= new cSocios_aeconomica();
			break;
			case "personas_actividad_economica_tipos":
				$mObj	= new cPersonas_actividad_economica_tipos();
				$this->mCampoDesc = $mObj->nombre_de_la_actividad()->get();
				break;
			case "personas_consulta_lista":
				$mObj	= new cPersonas_consulta_lista();
				break;
			case "personas_aseguradoras":
				$mObj	= new cPersonas_aseguradoras();
				$this->mCampoDesc = $mObj->alias()->get();
				break;
			case TUSUARIOS_NOTAS:
				$mObj	= new cUsuarios_web_notas();
				break;
			case TAML_PERFIL_RIESGO:
				$mObj	= new cAml_riesgo_perfiles();
			break;
			case "aml_riesgo_producto":
				$mObj	= new cAml_riesgo_producto();
				break;
			case TCATALOGOS_GRADO_RIESGO:
				$mObj	= new cAml_risk_levels();
				$this->mCampoDesc = $mObj->nombre_del_nivel()->get();
			break;
			case TAML_REGISTRO_DE_RIESGOS:
				$mObj	= new cAml_risk_register();
				$this->mCampoDesc = $mObj->mensajes_del_sistema()->get();
			break;
			case "aml_risk_catalog":
				$mObj	= new cAml_risk_catalog();
				$this->mCampoDesc = $mObj->descripcion()->get();
				break;
			case TAML_REGISTRO_DE_ALERTAS:
				$mObj	= new cAml_alerts();
				$this->mCampoDesc = $mObj->mensaje()->get();
			break;
			case "aml_instrumentos_financieros":
				$mObj	= new cAml_instrumentos_financieros();
				$this->mCampoDesc = $mObj->nombre_de_instrumento()->get();
				break;
			case "aml_riesgo_matrices":
				$mObj	= new cAml_riesgo_matrices();
				$this->mCampoDesc = $mObj->descripcion()->get();
				break;
			case "aml_listanegra_int":
				$mObj	= new cAml_listanegra_int();
				break;
			case "aml_personas_descartadas":
				$mObj	= new cAml_personas_descartadas();
				break;
			case TCREDITOS_PRODUCTOS_OTROS_PARAMETROS:
				$mObj	= new cCreditos_productos_otros_parametros();
				break;
			case "creditos_lineas":
				$mObj	= new cCreditos_lineas();
			case TCREDITOS_OTROS_DATOS:
				$mObj	= new cCreditos_otros_datos();
				$this->mCampoDesc = $mObj->clave_de_parametro()->get();
				break;
			case "entidad_pagos_perfil":
				$mObj	= new cEntidad_pagos_perfil();
				//$this->mCampoDesc	= $mObj->
				break;
			case "entidad_reglas":
				$mObj	= new cEntidad_reglas();
				$this->mCampoDesc	= $mObj->nombre()->get();
				break;
			case "creditos_productos_costos":
				$mObj	= new cCreditos_productos_costos();
				break;
			case "creditos_flujoefvo":
				$mObj	= new cCreditos_flujoefvo();
				break;
				
			case "creditos_sic_notas":
				$mObj	= new cCreditos_sic_notas();
				break;
			case "personas_documentacion":
				$mObj	= new cPersonas_documentacion();
				$this->mCampoDesc = $mObj->archivo_de_documento()->get();
				break;
			case "creditos_firmantes":
				$mObj	= new cCreditos_firmantes();
				
				break;
			case "empresas_cobranza":
				$mObj	= new cEmpresas_cobranza();
				$this->mCampoDesc	= $mObj->observaciones()->get();
				break;
			case "empresas_operaciones":
				$mObj	= new cEmpresas_operaciones();
				break;

			case TUSUARIOS_REGISTRO:
				$mObj	= new cT_03f996214fba4a1d05a68b18fece8e71();
				break;
			case "creditos_preclientes":
				$mObj	= new cCreditos_preclientes();
				break;

			case "leasing_originadores_tipos":
				$mObj	= new cLeasing_originadores_tipos();
				break;
			case "leasing_comisiones":
				$mObj	= new cLeasing_comisiones();
				break;
			case "leasing_originadores":
				$mObj	= new cLeasing_originadores();
				$this->mCampoDesc	= $mObj->nombre_originador()->get();
				break;
			case "leasing_usuarios":
				$mObj	= new cLeasing_usuarios();
				$this->mCampoDesc	= $mObj->nombre()->get();
				break;
			case "leasing_originadores_tipos":
				$mObj	= new cLeasing_originadores_tipos();
				$this->mCampoDesc	= $mObj->nombre_tipo_originador()->get();
				break;
			case "leasing_residual":
				$mObj	= new cLeasing_residual();
				break;
			case "leasing_tasas":
				$mObj	= new cLeasing_tasas();
				break;
			case "leasing_tipo_rac":
				$mObj	= new cLeasing_tipo_rac();
				break;
			case "leasing_financiero":
				$mObj	= new cLeasing_financiero();
				break;
			case "leasing_bonos":
				$mObj	= new cLeasing_bonos();
				break;
			case "leasing_activos":
				$mObj	= new cLeasing_activos();
				$this->mCampoDesc	= $mObj->descripcion()->get();
				break;
			case "leasing_rentas":
				$mObj	= new cLeasing_rentas();
				break;
			case "vehiculos_gps_costeo":
				$mObj	= new cVehiculos_gps_costeo();
				break;
			case "vehiculos_gps":
				$mObj	= new cVehiculos_gps();
				$this->mCampoDesc	= $mObj->nombre_gps()->get();
				break;
			case "vehiculos_marcas":
				$mObj	= new cVehiculos_marcas();
				break;
			case "vehiculos_segmento":
				$mObj	= new cVehiculos_segmento();
				break;
			case "vehiculos_tenencia":
				$mObj	= new cVehiculos_tenencia();
				break;
			case "originacion_leasing":
				$mObj	= new cOriginacion_leasing();
				break;
			case "vehiculos_usos":
				$mObj	= new cVehiculos_usos();
				$this->mCampoDesc	= $mObj->descripcion_uso()->get();
				break;
				
			case "general_contratos":
				$mObj	= new cGeneral_contratos();
				$this->mCampoDesc	= $mObj->titulo_del_contrato()->get();
				break;
			case "general_dias_festivos":
				$mObj				= new cGeneral_dias_festivos();
				$this->mCampoDesc	= $mObj->descripcion_festividad()->v();
				break;
				
				
			default:
				$mObj	= null;
				break;
		}
		if($mObj != null) { $this->mClavePrincipal	= $mObj->getKey(); }
		return $mObj;
	}
}


class SystemDB {
	private $mCNX		= false;
	private $mMessage	= "";
	function __construct(){
		$this->mCNX = new mysqli(WORK_HOST, USR_DB, PWD_DB, MY_DB_IN);
		if ($this->mCNX->connect_errno) {
		    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
		}
	}
	function query(){
		
	}
	function exec(){
		
	}
}

class cSystemPatch {
	private $mMessages	= "";

	function __construct(){  }

	function patch($force = false, $version_inicial = false, $soloIdioma = false){
		$xCache		= new cCache();
		$xCache->clean(false);
		
		$ql			= new MQL();
		$xConf		= new cConfiguration();
		$localver	= $xConf->get("safe_osms_database_version");
		//Actualiza la configuracion
		//if($force == true){			$xConf->set("safe_osms_database_version", $version);		}
		if(FORCE_UPDATES_ON_BOOT == true OR $force == true){
			//Ejecutar Vistas y Functions
			if($soloIdioma == false){	$this->setAplicarScripts(); }
			
			$current	= ($version_inicial === false) ? intval(SAFE_DB_VERSION) : $version_inicial; //201406.01 
			$dbversion	= intval(SAFE_VERSION . SAFE_REVISION);
			$sqlMenu	= "INSERT INTO `general_menu` (`idgeneral_menu`, `menu_parent`, `menu_title`, `menu_file`, `menu_type`, `menu_order`) VALUES ";
	
			$lang		= array();
			$sql		= array();
			
			$xSrv		= new MQLService("", "");
			$ulang		= $xSrv->getRequest(URL_UPDATES . "install/updates/lang.php");
			$lang		= json_decode($ulang, true);
			
			$ulang2		= $xSrv->getRequest(URL_UPDATES . "install/updates/lang2.php");
			$lang2		= json_decode($ulang2, true);
					
			$usql		= $xSrv->getRequest(URL_UPDATES . "install/updates/sql.php?version=$localver");
			$sql		= json_decode($usql, true);
			//setLog(URL_UPDATES . "install/updates/sql.php?version=$dbversion");
			$this->mMessages .= "URL\t" . URL_UPDATES . "install/updates/sql.php?version=$localver" . "\r\n";
			
			$upt		= false;	
			if(is_array($lang)){
				foreach( $lang as $version => $patchs ){
					if($current <= intval($version) ){
						foreach ($patchs as $idx => $content){
							$palabra	= $content[0];
							$traduccion	= $content[1];
							$language	= $content[2];
							$this->addLang($palabra, $traduccion, $language);
						}
						$upt	= true;
					}
				}
			}
			if(is_array($lang2)){
				foreach( $lang2 as $version2 => $patchs2 ){
					if($current <= intval($version2) ){
						foreach ($patchs2 as $idx2 => $content2){
							$palabra	= $content2[0];
							$spanish	= $content2[1];
							$english	= $content2[2];
							$this->addLang($palabra, $spanish, "es");
							$this->addLang($palabra, $english, "en");
						}
						$upt	= true;
					}
				}
			}
			if($soloIdioma == false){
				foreach ($sql as $version => $patchs){
					if($current <= intval($version) ){
						$this->mMessages .= "WARN\t===========PATCH $version\r\n";
						foreach ($patchs as $idx => $content){
							$ql->setRawQuery($content);
							$content = preg_replace('!\s+!', ' ', $content);
							$this->mMessages .= "$idx\t====$content====\r\n";
							$upt	= true;
							//syslog(E_ERROR, $content);
						}
					} else {
						$this->mMessages .= "WARN\tDescartado por ser $version de $current\r\n";
					}
				}
				//Obtener el codigo y parcharlo
				//URL_UPDATES
				//Convierte la DB a UTF8
				//ALTER TABLE `operaciones_recibos` CHANGE COLUMN `clave_de_moneda` `clave_de_moneda` VARCHAR(6) NULL DEFAULT 'MXN', ADD COLUMN `archivo_fisico` VARCHAR(200) NULL COMMENT 'Archivo fisico del recibo, almacenado en server ftp' AFTER `origen_aml`
				$codif				= $ql->getDataRecord("SHOW TABLES IN " . MY_DB_IN);
				foreach ($codif as $rows){
					$table		= $rows["Tables_in_" . MY_DB_IN];
					if($current <= 20150402){ //Ultima version parchada
						$ql->setRawQuery("ALTER TABLE `$table` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci");
						$ql->setRawQuery("ALTER TABLE `$table` ENGINE = INNODB");
					}
				}
				$ql->setRawQuery("ALTER SCHEMA `" . MY_DB_IN ."`  DEFAULT CHARACTER SET utf8  DEFAULT COLLATE utf8_general_ci");
			}
			//Actualizar vistas y functions
			if($soloIdioma == false){
				if($this->setAplicarScripts() == false){
					$this->mMessages .= "ERROR\tAl Aplicar Vistas y funciones...\r\n";
				} else {
					$this->mMessages .= "OK\tSe aplicaron Vistas y funciones...\r\n";
				}
			}
			//ejecutar Mensajes
			$this->mMessages	.= $ql->getMessages(OUT_TXT);		
		
			if($upt == true){		/*Actualiza la configuracion*/			
				$xConf->set("safe_osms_database_version", $dbversion);		
			}
		} else {
			$this->mMessages	.= "WARN\tSistema no Actualizado\r\n";
		}
		
		
		return $this->mMessages;
	}
	function addLang($palabra, $traduccion, $idioma = "es"){
		$xLng	= new cSistema_lenguaje();
		$idioma	= strtoupper($idioma);
		//verificar si existe la palabra
		$sql	= "SELECT *, COUNT(`idsistema_lenguaje`) as 'existen' FROM	`sistema_lenguaje` WHERE (`sistema_lenguaje`.`equivalente` ='$palabra') AND (`sistema_lenguaje`.`idioma` ='$idioma')";
		$mql	= new MQL();
		$d		= $mql->getDataRow($sql);
		 $existen = ( setNoMenorQueCero($d["existen"]) > 0) ? true : false;
		if($existen == false){
			$xLng->idsistema_lenguaje( $xLng->query()->getLastID() );
			$xLng->idioma($idioma);
			$xLng->equivalente($palabra);
			$xLng->traduccion($traduccion);
			$xLng->query()->insert()->save();
			//setLog($xLng->query()->insert()->get());
			$this->mMessages	.= "LANG\tAgregar $palabra con traduccion $traduccion\r\n";
		} else {
			$id 	= $d["idsistema_lenguaje"];
			$xLng->idsistema_lenguaje($id);
			$xLng->idioma($idioma);
			$xLng->equivalente($palabra);
			$xLng->traduccion($traduccion);
			$xLng->query()->update()->save($id);
			$this->mMessages	.= "LANG\tActualizar $id $palabra con traduccion $traduccion\r\n";
		}
		
	}
	function downloadUpdate($mfile){
		set_time_limit(0);
		//htdocs.tar.gz
		//$url = 'http://www.freewarelovers.com/android/download/temp/1306495040_Number_Blink_1.1.1.apk';
		$url 	= "https://dl.dropboxusercontent.com/u/68271288/safe/$mfile";
		$out	= PATH_TMP . "/$mfile"; //dirname(__FILE__) . '/downloads/a.apk'
		$fp 	= fopen ($out, 'w+');
		
		$ch 	= curl_init($url);
		
		curl_setopt_array($ch, array(
		CURLOPT_URL            => $url,
		CURLOPT_BINARYTRANSFER => 1,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_FILE           => $fp,
		CURLOPT_TIMEOUT        => 50,
		CURLOPT_USERAGENT      => 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)'
				));
		
		$results = curl_exec($ch);
		if(curl_exec($ch) === false)
		{
			echo 'Curl error: ' . curl_error($ch);
		}		
	}
	function setAplicarScripts(){
		$archivo_vistas	= "xx.vistas.sql";
		$archivo_funcs	= "xx.functions.sql";
		
		//Archivo Scian
		//$archivo_scian	= "";
		
		
		if(is_file(PATH_TMP . $archivo_vistas)){ unlink(PATH_TMP . $archivo_vistas);	}
		if(is_file(PATH_TMP . $archivo_funcs)){	unlink(PATH_TMP . $archivo_funcs);	}
		
		$apVista		= exec("wget --no-check-certificate -O " . PATH_TMP . $archivo_vistas . " https://www.dropbox.com/s/vvophctb4lqlih9/$archivo_vistas");
		$apFuncs		= exec("wget --no-check-certificate -O " . PATH_TMP . $archivo_funcs . " https://www.dropbox.com/s/ehyutpzihekm7uy/$archivo_funcs");
		$res			= exec("mysql --host=localhost --user=" .  USR_DB . " --password=" . PWD_DB ." --force --database=" . MY_DB_IN . " < " . PATH_TMP . $archivo_vistas);
		$res 			= exec("mysql --host=localhost --user=" .  USR_DB . " --password=" . PWD_DB ." --force --database=" . MY_DB_IN . " < " . PATH_TMP . $archivo_funcs);
		return $res;
	}	
}


//=======================================================================================================




class MQLCampo {
	private $mDatos			= array();
	private $mTabla			= "";
	private $mEquivalencias	= array();
	private $mTipoPHP		= null;
	private $mTipoSQL		= null;
	private $mNombre		= null;
	function __construct($datos){
		$this->mDatos			= $datos;
		$mql					= new MQL();
		$this->mEquivalencias	= $mql->getTipos();
		if(is_array($datos)){
			$this->mTipoSQL		= $this->mDatos["T"];
			$this->mTipoPHP 	= $this->mEquivalencias[ $this->mTipoSQL ] ;//(isset($this->mEquivalencias[ $this->mTipoSQL ])) ? $this->mEquivalencias[ $this->mTipoSQL ] : null;
			$this->mNombre		= $this->mDatos["N"];
			//if(!isset($this->mEquivalencias[ $this->mTipoSQL ])){ setLog($this->mDatos); }
		}
	}
	function getTipo(){ return $this->mTipoPHP; }
	function getTipoSQL(){ return $this->mTipoSQL; }
	function isNumber(){ return ($this->getTipo() == MQL_INT OR $this->getTipo() == MQL_FLOAT) ? true : false;	}
	//function isDate(){ return ($this->getTipo() == MQL_DATE) }
	function getLongitud(){ return $this->mDatos["L"]; }
	function getValor($out = OUT_TXT){ return $this->v($out); }

	function v($out = OUT_TXT){
		$dato	= "";
		if($this->getTipoSQL() == "DATE"){ $this->mDatos["V"] = $this->cleanDate($this->mDatos["V"]); }
		if ( $this->getTipo() == MQL_STRING ){
			if( $out == OUT_HTML){
				$dato	= $this->mDatos["V"];
				//if($this->mForceUTF == true){ $cadena	= iconv('UTF-8', 'UTF-8//IGNORE', $cadena); }
				//$dato	= iconv(mb_detect_encoding($dato), 'UTF-8//IGNORE', $dato);
				$html	= @htmlentities(strtolower($dato), ENT_COMPAT, "UTF-8");
				if(!$html){ $html = htmlentities(strtolower($dato)); }
				$dato	= preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml|caron);~i', '$1', $html);
				$dato	= htmlspecialchars_decode($dato);
				$dato 	= html_entity_decode($dato);

			} else {
				$dato	= $this->mDatos["V"];
			}

		} else {
			$dato	= $this->mDatos["V"];
		}
		return $dato;
	}
	function get(){ return $this->mNombre; }
	function isEqual($value){
		return ( $this->getTipo() == MQL_STRING ) ? $this->get() . "=\"$value\" " : $this->get() . "=$value ";
	}
	function cleanDate($valor = false){
		$currDate			= date("Y-m-d");
		if(!isset($valor) ){ $valor  = $currDate;  }
		if(is_null($valor) ){ $valor = $currDate; }
		$valor		= ($valor == "") ? $currDate : $valor;
		$valor		= ($valor == "0000-00-00") ? $currDate : $valor;
		$valor 		= str_replace("/", "-", $valor);
		$valor		= ($valor == false) ? $this->mDatos["V"] : $valor;

		$valor			= str_replace("/", "-", $valor);
		$D				= explode("-", $valor, 3);
		$anno			= date("Y");
		$mes			= date("m");
		$dia			= date("d");
		if( $this->cleanNoCero( $D[0] ) > 31 ){
			$anno		= $this->cleanNoCero($D[0]);
			$mes		= $this->cleanNoCero($D[1]);
			$dia		= $this->cleanNoCero($D[2]);
		} else {
			if(isset($D[2])){
				$panno			= $this->cleanNoCero( $D[2] );
				if($panno > 31){ $anno		= $panno; }
			}
			//if(!isset($D[1])){ if(MODO_DEBUG == true){  setLog("Fecha fallida $valor"); } }
			$mes		= $this->cleanNoCero($D[1]);
			$dia		= $this->cleanNoCero($D[0]);
		}

		$anno			= ($anno > 2099 OR $anno < 1900) ? date("Y", strtotime($currDate)) : $anno;
		$mes			= ($mes < 1 OR $mes > 12) ? date("m", strtotime($currDate)) : $mes;
		$dia			= ($dia < 1 OR $dia > 31) ? date("d", strtotime($currDate)) : $dia;
		$valor			= mktime(0,0,0,$mes,$dia, $anno);
		$valor			= date("Y-m-d", $valor);

		return $valor;

	}
	function cleanNoCero($valor = false){ $valor = $this->cleanNumber($valor); return ($valor < 0) ? 0 : $valor;	}
	function cleanNumber($valor, $digitos = false){
		$valor 		= (!isset($valor) ) ? 0 : $valor;
		$valor		= (is_null($valor) )? 0 : $valor;
		$valor		= ($valor === false) ? 0 : $valor;
		//if(SYS_SEPARADOR_DECIMAL == "."){ 	$valor		= str_replace(",", "", $valor);		}
		$valor		= str_replace(",", "", $valor);
		$valor		= ($valor == "") ? 0 : $valor;
		if( preg_match("/[eE][-]?/", $valor) ){ $digitos	= ($digitos === false) ? 4 : $digitos; $valor = round($valor, $digitos) * -1; }
		$numero		= 0;
		$patron		= "/[^0-9.,-]/";
		$valor		= preg_replace("/([.]|[,]|[-])\\1+/", "$1", $valor);
		$numero		= preg_replace($patron, "", $valor);
		$numero		= trim($numero);
		return $numero;
	}
	function isEqualF($value){
		$value	= $this->cleanNumber($value,2);
		$val2	= $this->cleanNumber($this->v(),2);
		return ($val2 == $value) ? true : false;
	}
}

class MQL {
	private $mDatos		= array();
	private $mTabla		= "";
	private $mPrimary	= "";
	private $cnn		= null;
	private $mMessages	= "";
	private $mSql		= "";
	private $mInsertID	= false;
	private $mNumberRow	= 0;
	private $mDebug		= false;
	private $mConTitulos= false;
	private $mTitulos	= array(); 
	private $mUseCache	= false;


	private $mEquivalencias	= array(
			"INT" 		=> "int",
			"TINYINT" 	=> "int",
			"SMALLINT" 	=> "int",
			"MEDIUMINT" => "int",
			"BIGINT" 	=> "int",
			"YEAR" 		=> "int",
			"TIMESTAMP" => "int",

			"FLOAT" 	=> "float",
			"DOUBLE" 	=> "float",
			"DECIMAL" 	=> "float",

			"VARCHAR" 	=> "string",
			"CHAR" 		=> "string",
			"TEXT" 		=> "string",
			"LONGTEXT" 	=> "string",
			"TINYTEXT" 	=> "string",
			"MEDIUMTEXT" => "string",
			"DATE" 		=> "string",
			"DATETIME" 	=> "string",
			"TIME" 		=> "string",
			"ENUM"		=> "string",

			"BINARY" 	=> "string",
			"BLOB" 		=> "string",
			"MEDIUMBLOB" => "string"
	);


	function __construct($tabla = "", $datos = array(), $primaryK = ""){
		$this->mDatos	= $datos;
		$this->mTabla	= $tabla;
		$this->mPrimary	= $primaryK;
		if(defined("MODO_DEBUG")){ if(MODO_DEBUG == true){ $this->mDebug	= true; } }
	}
	function select(){	return new MQLSelect($this->mTabla, $this->mDatos, $this->mPrimary);	}
	function insert(){	return new MQLInsert($this->mTabla, $this->mDatos, $this->mPrimary);	}
	function delete(){	return new MQLDelete($this->mTabla, $this->mDatos,  $this->mPrimary); }
	function update(){	return new MQLUpdate($this->mTabla, $this->mDatos,  $this->mPrimary);	}
	function setToUTF8(){
		foreach ($this->mDatos as $campo => $opts ){
			if(isset($this->mDatos[$campo]["T"])){
				if( $this->mEquivalencias[ $this->mDatos[$campo]["T"] ] == MQL_STRING ){
					$this->mDatos[$campo]["V"]			= mb_convert_encoding ($this->mDatos[$campo]["V"], mb_detect_encoding($this->mDatos[$campo]["V"]), "UTF-8");
					$this->mDatos[$campo]["V"] = iconv('UTF-8', 'UTF-8//IGNORE', $this->mDatos[$campo]["V"]);
				}
			}
		}
	}
	function getTipos($tipo = false){ return ($tipo == false) ? $this->mEquivalencias : $this->mEquivalencias[$tipo];	}
	function connect(){
		if($this->cnn ==  null){
			$this->cnn = new mysqli( MQL_SERVER, MQL_USER, MQL_PASS, MQL_DB);
			$this->cnn->set_charset("utf8");
		}
		if ($this->cnn->connect_errno) {
			if($this->mDebug == true){ $this->mMessages	.= "ERROR EN LA CONEXION : ". $this->cnn->connect_error . " \n"; $this->getDebug();	}
			$this->cnn			= null;
		}
		return $this->cnn;
	}
	function row($data){ return $this->setData($data); }
	function setData($data = null){
		if($this->mDebug == true){ $this->mMessages	.= "ASIGNANDO DATOS\r\n"; }
		$data		= ($data == null) ? $_REQUEST : $data;
		$EsArray	= false;
		$idx		= 0;
		foreach($this->mDatos as $dato => $field){
			$campo						= $field["N"];
			if(isset($data[$campo])){
				
				$this->mDatos[$campo ]["V"] 	= ($this->mEquivalencias[ $this->mDatos[$campo]["T"] ] == MQL_STRING  ) ? addslashes($data[$campo]) : $data[$campo];
			}
			$idx++;
		}
		return $this->mDatos;
	}
	function getRow($where){
		$select				= new MQLSelect($this->mTabla, $this->mDatos, $this->mPrimary);
		$select->get();
		$datos				= $select->exec($where);
		if($this->mDebug == true){	$this->mMessages	.= $select->log(); }
		return ( isset($datos[0]) ) ? $datos[0] : array();
	}
	function setRow($where){
		$this->setData( $this->getRow($where) );
	}
	function getLastID(){
		$contar				= 0;
		$cnn				= $this->connect();
		$rs					= $cnn->query("SELECT LAST_INSERT_ID() AS 'conteo' FROM " . $this->mTabla . " LIMIT 0,1");
		$row 				= $rs->fetch_assoc();
		$contar				= $row["conteo"];
		if($contar == 0){
			$rs				= $cnn->query("SELECT MAX(" . $this->mPrimary . " ) AS 'conteo' FROM " . $this->mTabla . " LIMIT 0,1");
			$row 			= $rs->fetch_assoc();
			$contar			= $row["conteo"];
				
		}
		return $contar + 1;
	}
	function getLog(){
		return $this->mMessages;
	}
	function initByID($id){
		if( is_string($id)){
			$id			= "'$id'";
		}
		return $this->getRow($this->mPrimary . "=$id" );
	}
	function getRecordset($sql= ""){
		$this->mMessages 	= "";
		$cnn		= $this->connect();
		$rs			= false;
		if($cnn == null OR $sql == ""){
			//
		} else {
			$rs		= $cnn->query($sql);
			if(!$rs){
				if($this->mDebug == true){
					$this->mMessages	= "ERROR(". $cnn->error . ") EN EL QUERY : " . $sql . "  \n"; $this->getDebug();
				}
			}
		}
		
		return $rs;
	}
	function getDataRecord($sql){
		$this->mSql			= $sql;
		$this->mMessages 	= "";
		$this->mNumberRow	= 0;
		$run				= true;
		if($this->mUseCache == true){
			$xCache			= new cCache();
			$idx0			= crc32($this->mSql);
			$idx1			= "SQL.data.$idx0";
			$idx2			= "SQL.title.$idx0";
			$data			= $xCache->get($idx1);
			if(!is_array($data)){
				$run		= true;
			}
			if($this->mConTitulos == true){
				$this->mTitulos	= $xCache->get($idx2);
				if(!is_array($this->mConTitulos)){
					$run	= true;
				}
			}
		} 
		if($run == true){
			
			$cnn				= $this->connect();
			$rs					= $cnn->query($this->mSql);
		
			$data				= array();
			if(!$rs){
				if($this->mDebug == true){
					//$this->mMessages .= "ERROR[" . $cnn->error . "] " . $this->mSql . "\r\n";
					//$this->getDebug();
				}
			} else {
				if($this->mConTitulos == true){
					$arrNums	= array(
							"252" => "VARCHAR",
							"253" => "VARCHAR",
							"254" => "VARCHAR",
							"10" => "VARCHAR",
							"11" => "VARCHAR",
							"16" => "VARCHAR",
							"14" => "VARCHAR",
							"12" => "VARCHAR",
							"246" => "FLOAT",
							"5" => "FLOAT",
							"4" => "FLOAT",
							"1" => "INT",
							"2" => "INT",
							"3" => "INT",
							"7" => "INT",
							"8" => "INT",
							"9" => "INT",
							"13" => "INT",
							"" => "VARCHAR"
							);
					/*name 	El nombre de la columna
					orgname 	El nombre original de la columna en caso que se haya especificado un alias
					table 	El nombre de la tabla al que este campo pertenece (si no es calculado)
					orgtable 	El nombre original de la tabla en caso que se haya especificado un alias
					def 	Reservado para el valor por omisión, por ahora es siempre ""
					db 	Base de datos (desde PHP 5.3.6)
					catalog 	El nombre del catálogo, siempre "def" (desde PHP 5.3.6)
					max_length 	El largo máximo del campo en el resultset
					length 	El largo del campo, tal como se especifica en la definición de la tabla.
					charsetnr 	El número del juego de caracteres del campo.
					flags 	Un entero que representa las banderas de bits del campo.
					type 	El tipo de datos que se usa en este campo
					decimals 	El número de decimales utilizado (para campos de tipo integer)*/
					//"codigo_sucursal" => array("N"=>"codigo_sucursal","T"=>"VARCHAR","V"=>"","L"=>10),
					/*0    MYSQLI_TYPE_DECIMAL: Field is defined as DECIMAL
	1    MYSQLI_TYPE_CHAR: Field is defined as TINYINT. For CHAR, see MYSQLI_TYPE_STRING
	2    MYSQLI_TYPE_SHORT: Field is defined as SMALLINT
	3    MYSQLI_TYPE_LONG: Field is defined as INT
	4    MYSQLI_TYPE_FLOAT: Field is defined as FLOAT
	5    MYSQLI_TYPE_DOUBLE: Field is defined as DOUBLE
	6    MYSQLI_TYPE_NULL: Field is defined as DEFAULT NULL
	7    MYSQLI_TYPE_TIMESTAMP: Field is defined as TIMESTAMP
	8    MYSQLI_TYPE_LONGLONG: Field is defined as BIGINT
	9    MYSQLI_TYPE_INT24: Field is defined as MEDIUMINT
	10   MYSQLI_TYPE_DATE: Field is defined as DATE
	11   MYSQLI_TYPE_TIME: Field is defined as TIME
	12   MYSQLI_TYPE_DATETIME: Field is defined as DATETIME
	13   MYSQLI_TYPE_YEAR: Field is defined as YEAR
	14   MYSQLI_TYPE_NEWDATE: Field is defined as DATE
	16   MYSQLI_TYPE_BIT: Field is defined as BIT (MySQL 5.0.3 and up)
	246  MYSQLI_TYPE_NEWDECIMAL: Precision math DECIMAL or NUMERIC field (MySQL 5.0.3 and up)
	247  MYSQLI_TYPE_ENUM: Field is defined as ENUM
	248  MYSQLI_TYPE_SET: Field is defined as SET
	249  MYSQLI_TYPE_TINY_BLOB: Field is defined as TINYBLOB
	250  MYSQLI_TYPE_MEDIUM_BLOB: Field is defined as MEDIUMBLOB
	251  MYSQLI_TYPE_LONG_BLOB: Field is defined as LONGBLOB
	252  MYSQLI_TYPE_BLOB: Field is defined as BLOB
	253  MYSQLI_TYPE_VAR_STRING: Field is defined as VARCHAR
	254  MYSQLI_TYPE_STRING: Field is defined as CHAR or BINARY
	255  MYSQLI_TYPE_GEOMETRY: Field is defined as GEOMETRY*/
					while($obj	= $rs->fetch_field()){
						$this->mTitulos[$obj->name]["N"] 		= $obj->name;
						$this->mTitulos[$obj->name]["TBL"] 		= $obj->table;
						$this->mTitulos[$obj->name]["L"] 		= $obj->length;
						$this->mTitulos[$obj->name]["T"] 		= $arrNums[$obj->type];
						$this->mTitulos[$obj->name]["V"] 		= $obj->def;
						$this->mTitulos[$obj->name]["NT"]		= $obj->type; //Native Type
					}
					unset($obj);
				}
	
				while ($row = $rs->fetch_assoc()) { $data[]		= $row; $this->mNumberRow++; }
				$rs->free();
				if($this->mUseCache == true){
					$xCache			= new cCache();
					$idx0			= crc32($this->mSql);
					$idx1			= "SQL.data.$idx0";
					$idx2			= "SQL.title.$idx0";
					$xCache->set($idx1, $data);
					$xCache->set($idx2, $this->mTitulos);
				}
			}
			$rs		= null;
		}
		return $data;
	}
	function getNumberOfRows(){ return $this->mNumberRow; }
	function setConTitulos(){ $this->mConTitulos = true; }
	function getTitulos(){ return $this->mTitulos; }
	function getDataRow($sql){
		$this->mSql	= $sql;
		$cnn		= $this->connect();
		$rs			= $cnn->query($this->mSql);
		$data		= array();
		$this->mMessages 	= "";
		if(!$rs){
			if($this->mDebug == true){ 
				$this->mMessages .= "ERROR[" . $cnn->error . "] " . $this->mSql . "\r\n";
				$this->getDebug(); 
			}
		} else {
			while ($row = $rs->fetch_assoc()) { 	$data		= $row; }
			$rs->free();
		}
		unset($rs);
		return $data;
	}
	function getDataValue($sql, $campo){
		$this->mSql	= $sql;
		$cnn		= $this->connect();
		$rs			= $cnn->query($this->mSql);
		$val		= null;
		$this->mMessages 	= "";
		if(!$rs){
			if($this->mDebug == true){
				$this->mMessages .= "ERROR[" . $cnn->error . "] " . $this->mSql . " \r\n";
				$this->getDebug();
			}
		} else {
			$row 	= $rs->fetch_assoc();
			if(isset($row[$campo])){
				$val	= $row[$campo];
			}
			$rs->free();
		}
		unset($rs);
		return $val;
	}
	function getArrayRecord($sql){
		$this->mSql	= $sql;
		$cnn		= $this->connect();
		$rs			= $cnn->query($this->mSql);
		$data		= array();
		if(!$rs){
			if($this->mDebug == true){ 
				$this->mMessages .= "ERROR[" . $cnn->error . "] " . $this->mSql . "\r\n";
				$this->getDebug(); 
			}
		} else {
			while ($row = $rs->fetch_array()) {	$data[$row[0]]	= isset($row[1]) ? $row[1] : $row[0]; }
		}
		$rs					= null;
		return $data;
	}
	function setRawQuery($sql){
		$this->mSql			= $sql;
		$this->mMessages 	= "";
		$res				= true;
		$cnn				= $this->connect();
		$rs					= $cnn->query($this->mSql);
		if($rs === false){
			if($this->mDebug == true){
				$this->mMessages = "ERROR[" . $cnn->error . "] " . $this->mSql . "\r\n";
				$this->getDebug();
			}
			$res			= false;
		} else {
			if(isset($cnn->insert_id)){
				$this->mInsertID	= $cnn->insert_id;
			}
		}
		//$cnn->close();
		unset($rs);
		
		//unset($cnn);
		//syslog(E_WARNING, $sql);
		//syslog(E_WARNING, $cnn->info);
		
		return $res;
	}
	function html(){ return new MQLHtml($this->mTabla, $this->mDatos, $this->mPrimary);	}
	function campo($campo = ""){		return new MQLCampo($this->mDatos[$campo]);	}
	function getCampos($compatible = false){ 
		$data 	= $this->mDatos;
		if($compatible == true){
			foreach($this->mDatos as $dato => $field){
				$data[ $field["N"] ] = $field["V"];
			}
		}
		return $data;
	}
	function getListaDeCampos(){
		$str			= "";
		foreach($this->mDatos as $dato => $field){
			$str		.= ($str == "") ? $field["N"] : "," . $field["N"];
		}
		return $str;
	}
	function setCampos($campos){		$this->mDatos	= $campos;	}
	function getMessages($out = OUT_TXT ){ return $this->mMessages; }
	function getDebug(){
		if($this->mDebug == true){
			if(function_exists("setLog")){ setLog($this->mMessages); }
			$this->mMessages = "";
		}
	}
	function getLastInsertID(){ return $this->mInsertID; }
	function onDebug(){ return $this->mDebug; }
	function setUseCache(){ $this->mUseCache = true; }
	function getContarDe($tabla){
		$items		= 0;
		$d			= $this->getDataRow("SELECT COUNT(*) AS `items` FROM `$tabla`");
		if(isset($d["items"])){
			$items	= $d["items"];
		}
		return $items;
	}
	function setCall($proc){ $this->setRawQuery("CALL `$proc`()");	}
}
class MQLInsert {
	private $mDatos				= array();
	private $mTabla				= "";
	private $mEquivalencias		= array();

	private $mSql				= "";
	private $mIns				= array();
	private $mMessages			= "";
	private $mDebug				= false;
	function __construct($tabla, $datos){
		$this->mDatos	= $datos;
		$this->mTabla	= $tabla;
		$mql					= new MQL($tabla, $datos);
		$this->mEquivalencias	= $mql->getTipos();
		$this->mDebug			= $mql->onDebug();
	}

	function save(){
		$mql	= new MQL();
		$this->get();
		$cnn	= $mql->connect();
		$rs		= $cnn->query($this->mSql);
		$id		= false;
		if($rs == false){
			if($this->mDebug == true){ $this->mMessages	= "ERROR(". $cnn->error . ") EN EL QUERY : " . $this->mSql . "  \n"; $this->getDebug(); }
		} else {
			$id	= ($cnn->insert_id == 0) ? 1 : $cnn->insert_id;
		}
		return $id;
	}
	function get(){
		$sql 	= "";
		$vals	= "";
		$camp	= "";
		$icnt	= 0;

		foreach ($this->mDatos as $t){
			$xFld	= new MQLCampo($t);
			$tipo	= $this->mEquivalencias[$t["T"]];
			$nombre	= $t["N"];
			$valor	= $xFld->v(OUT_TXT);// $t["V"];
			$this->mIns[$nombre] = $valor;		//medoo Insert
			//Fixed fechas
				
			if($tipo == MQL_STRING){
				$valor	= "\"$valor\"";
			}
			$camp 	.= ($icnt == 0) ? "$nombre" : ", $nombre";
			$vals	.= ($icnt == 0) ? "$valor" : ", $valor";
			$icnt++;
		}
		$this->mSql		= "INSERT INTO " . $this->mTabla . "($camp) VALUES ($vals) ";
		return $this->mSql;
	}
	function getMessages($out = OUT_TXT ){ return $this->mMessages; }
	function getDebug(){
		if($this->mDebug == true){
			if(function_exists("setLog")){ setLog($this->mMessages); }
			$this->mMessages = "";
		}
	}
}
class MQLSelect	 {
	private $mDatos			= array();
	private $mTabla			= "";
	private $mPrimary			= "";
	private $mEquivalencias	= array();
	private $mMessages			= "";

	private $mSql				= "";
	private $mWhere				= "";

	private $mIns				= array();
	private $mOrderBy			= "";
	private $mDebug				= false;

	function __construct($tabla, $datos, $primaryK){
		$this->mDatos			= $datos;
		$this->mTabla			= $tabla;
		$this->mPrimary			= $primaryK;

		$mql					= new MQL($tabla, $datos);
		$this->mEquivalencias	= $mql->getTipos();
		$this->mDebug			= $mql->onDebug();
	}
	function order($str){
		$this->mOrderBy	= $str;
		$this->mSql = $this->mSql . " ORDER BY $str";
	}
	function addAnd($txt){
		$this->mWhere	.= " AND  ($txt) ";
	}

	function get($where = ""){
		$sql 	= "";
		$vals	= "";
		$camp	= "";
		$icnt	= 0;
		foreach ($this->mDatos as $t){
			$tipo	= $this->mEquivalencias[$t["T"]];
			$nombre	= $t["N"];
			$valor	= $t["V"];
			$camp .= ($icnt == 0) ? "$nombre" : ",$nombre";
			$icnt++;
		}
		$where			= ( trim($where) == "" ) ? "" : " WHERE $where ";
		if(trim($this->mWhere != "") ){
			$this->mSql		.= ( strpos( strtoupper($this->mSql), "WHERE" ) === false) ?  " WHERE " : "";
			$this->mSql		.= $this->mWhere;
		}

		$this->mSql		= "SELECT $camp FROM " . $this->mTabla . " $where ";
		return $this->mSql;
	}
	function w(){
		
	}
	
	/**
	 * @param mixed $conditions
	 * @return string
	 * @example where("CAMPO = VALOR");
	 * @example where(array("CAMPO" => "VALOR", "CAMPO2" => "VALOR2");
	 * @example where(array(
	 * 					array("CAMPO", "!=", "VALOR")
	 * 					);
	 */
	function where($conditions){
		$where		= "";
		$arrOps		= array(" AND ", " OR ", "!=", "=", ">");
		if (is_array($conditions)){
			$icnt	= 0;
			foreach($conditions as $campo => $valor){
				if( is_array($valor)){
					$items		= count($valor);
					$operador	= ($items == 2) ? "=" : $valor[1];
					$ICampo		= $valor[0];
					$Ivalor		= ($items == 2) ? $valor[1] : $valor[2];
					$Ivalor 	= ($this->mEquivalencias[ $this->mDatos[ $ICampo ]["T"] ] == MQL_STRING) ? "\"$Ivalor\"" : $Ivalor;
					$where 		.= ($icnt == 0) ? "$ICampo $operador $Ivalor" : " AND $ICampo $operador $Ivalor";
					$this->mIns[$ICampo] = $Ivalor;
				} else {
					//array campo , operador, valor
					$valor 		= ($this->mEquivalencias[ $this->mDatos[ $campo ]["T"] ] == MQL_STRING) ? "\"$valor\"" : $valor;
					$where 		.= ($icnt == 0) ? "$campo = $valor" : " AND $campo = $valor";
					$this->mIns[$campo] = $valor;
				}
				$icnt++;
			}
		} else {
			//checkvar
			$isStr	= false;
			foreach($arrOps as $key => $val){
				if (strpos($conditions, $val) !== false){
					$isStr		= true;
				}
			}
			if( $isStr == true ){
				$where		= $conditions;
			} else {
				$where 		.= $this->mPrimary . " = ";
				$where 		.= ($this->mEquivalencias[$this->mDatos[$this->mPrimary]["T"]] == MQL_STRING) ? " \"$conditions\" " : $conditions;
			}
		}
		$where		= ($where != "") ? " WHERE $where " : "";
		$this->mSql	= $this->get() . $where;
		return $this->mSql;
	}
	function limit($init = 0, $end = 1){
		$this->mSql	= $this->get() . " LIMIT $init, $end";
		return $this->mSql;
	}
	function exec($where = "", $orders	= ""){
		if($this->mSql == "") { $this->get(); }
		if($where != ""){ $this->where($where); }
		if($orders != ""){ $this->order($orders); }
		if(trim($this->mWhere != "") ){ //TODO: Analizar
			$this->mSql		.= ( strpos( strtoupper($this->mSql), "WHERE" ) === false) ?  " WHERE " : "";
			$this->mSql		.= $this->mWhere;
		}
		$this->mMessages .= "SQL[ " . $this->mSql . "]\r\n";

		$mql				= new MQL();
		$cnn				= $mql->connect();
		$rs					= $cnn->query($this->mSql);
		if(!$rs){ $this->mMessages .= "ERROR[" . $cnn->error . "] " . $this->mSql . "\r\n"; $this->getDebug(); }
		$data				= array();
		if($rs){
			while ($row = $rs->fetch_assoc()) { 	$data[]		= $row; }
				
		}
		return $data;
	}
	function log(){	return $this->mMessages;	}
	function getMessages(){	return $this->mMessages;	}
	function set($sql){ $this->mSql	= $sql;	}
	function getDebug(){
		if($this->mDebug == true){
			if(function_exists("setLog")){ setLog($this->mMessages); }
			$this->mMessages = "";
		}
	}
	function service($action = ""){
		return new MQLService($action, $this->get());
	}
}

class MQLDelete {
	private $mDatos				= array();
	private $mTabla				= "";
	private $mEquivalencias		= array();	
	private $mDebug				= false;
	private $mMQL				= null;
	function __construct($tabla, $datos, $primaryK){
		$this->mDatos			= $datos;
		$this->mTabla			= $tabla;
		$this->mPrimary			= $primaryK;

		$this->mMQL				= new MQL($tabla, $datos);
		//$this->mEquivalencias	= $this->mMQL->getTipos();
		//$this->mDebug			= $this->mMQL->onDebug();
	}
	function truncate(){
		return $this->mMQL->setRawQuery("TRUNCATE " . $this->mTabla);
	}
}
class MQLUpdate {
	private $mDatos			= array();
	private $mTabla			= "";
	private $mEquivalencias	= array();
	private $mPrimaryKey		= "";
	private $mValueKey			= "";
	private $mSql				= "";
	private $mIns				= array();
	private $mMessages			= "";
	private $mDebug				= false;

	function __construct($tabla, $datos, $primaryKey){
		$this->mDatos			= $datos;
		$this->mTabla			= $tabla;
		$mql					= new MQL($tabla, $datos);
		$this->mEquivalencias	= $mql->getTipos();
		$this->mPrimaryKey		= $primaryKey;
		$this->mDebug			= $mql->onDebug();
	}
	function setID($id){
		$this->mValueKey	= $id;
	}
	function save($idKey){
		$mql		= new MQL();
		if( strpos($idKey, "=") !== false){
			$where		= $idKey;
		} else {
			$this->mValueKey	= $idKey;
			$where				=  $this->mPrimaryKey . "=\"" . $this->mValueKey . "\" ";
		}
		$this->get($where);
		$cnn	= $mql->connect();

		$rs		= $cnn->query($this->mSql);
		if($rs === false AND $this->mDebug == true){
			$this->mMessages	.= "ERROR(". $cnn->error . ") EN EL QUERY : " . $this->mSql . "  \n"; $this->getDebug();
		}

		return ($rs == false) ? false : true; //$cnn->insert_id;
	}
	function get($where = ""){
		$sql 	= "";
		$vals	= "";
		$icnt	= 0;
		//$where	= ($this->mValueKey == "") ? $where : $this->mPrimaryKey . "=\"" . $this->mValueKey . "\" $where ";
		$where		= ($where == "") ? $this->mPrimaryKey . "=\"" . $this->mValueKey . "\"" : $where;
		foreach ($this->mDatos as $t){
			if(isset($t["N"]) ){
				$xFld	= new MQLCampo($t);

				$tipo	= $this->mEquivalencias[$t["T"]];
				$nombre	= $t["N"];
				$valor	= $xFld->v(OUT_TXT);// $t["V"];
				$this->mIns[$nombre] = $valor;		//medoo Insert
				$valor	= ($tipo == MQL_STRING) ? "`$nombre`=\"$valor\"" : "`$nombre`=$valor" ;
				$vals	.= ($icnt == 0) ? "$valor" : ", $valor";
				$icnt++;
			}
		}
		$this->mSql		= "UPDATE " . $this->mTabla . " SET $vals WHERE $where";
		return $this->mSql;
	}
	function getMessages($out = OUT_TXT){ return $this->mMessages; }
	function getDebug(){
		if($this->mDebug == true){
			if(function_exists("setLog")){ setLog($this->mMessages); }
			$this->mMessages = "";
		}
	}
}
class MQLHtml {
	private $mDatos			= array();
	private $mTabla			= "";
	private $mPrimary			= "";
	private $mMessages			= "";

	function __construct($tabla, $datos, $primaryK){
		$this->mDatos			= $datos;
		$this->mTabla			= $tabla;
		$this->mPrimary			= $primaryK;
	}
	function select($label = "", $where = "", $id = ""){
		$q						= new MQLSelect($this->mTabla, $this->mDatos, $this->mPrimary);
		$data					= $q->exec($where);
		$this->mMessages	.= $q->log();
		$options	= array();
		foreach($data as $data){
			$options[ $data[$this->mPrimary] ]	= $data[ $label ];
		}
		unset($data);
		return new cHSelect($id, $options);
	}
	function log(){ return $this->mMessages; }
}
class MQLService {
	private $mSQL		= "";
	private $mAction	= ""; //select insert update
	private $mKey		= "kouko-kaga-123";
	private $mError		= array("code" => 0, "msg" => "");
	private $mMessages	= "";
	public $JTABLE		= "jtable";
	public $XPLAIN		= "xplain";
	private $mDebug		= false;
	
	function __construct($action, $sql){
		$this->mAction	= $action;
		$this->mSQL		= $sql;
		//$this->mKey		= "HIMITSU";
	}
	function setSQL($sql){ $this->mSQL	= $sql; }
	function setKey($key){ $this->mKey = $key; }
	function getJSON($out = ""){
		$json			= array();
		$mql			= new MQL();
		$rs				= $mql->getRecordset($this->mSQL);
		$idx			= 0;
		$rows			= array();
		if($rs){
			switch($out){
				case $this->JTABLE:
					$nnr	= 0;
					while ($row = $rs->fetch_assoc()) { $rows[]	= $row; $nnr++; }
					$json["Result"]				= "OK";
					$json["Records"]			= $rows;
					$dd							= $mql->getDataRow("SELECT FOUND_ROWS() AS 'idx'");
					$json["TotalRecordCount"]	= $dd["idx"];
					$rows 						= null;
					$dd							= null;
					
					break;
				case $this->XPLAIN:
					while ($row = $rs->fetch_assoc()) { $rows[]	= $row; }
					$json	= $rows;
					$rows 	= null;
					break;
				default :
					while ($row = $rs->fetch_assoc()) {
						foreach($row as $campo => $valor){
							if ( is_string($valor) ){
								$valor		= htmlentities($valor, ENT_IGNORE, "utf-8");//htmlentities( (string) $valor, ENT_QUOTES, 'utf-8', FALSE);
							}
							//if($out	== ""){
							$json["record_$idx"][$campo]	= $valor; //base64_encode($valor);//utf8_encode($valor);
							//}
						}
						$idx++;
					}
					break;
			}
		} else {
			if($out == $this->JTABLE){
				$json["Result"]		= "ERROR";
			} else {
				$json			= $json["error"] = $mql->getMessages(OUT_TXT);
			}
		}
		$rs						= null;
		$mql					= null;
		/*
$jTableResult = array();
$jTableResult['Result'] = "OK";
$jTableResult['Records'] = $rows;
*/
		return json_encode($json);
	}
	function getJsonField(){
		$json			= array();
		$mql			= new MQL();
		$rs				= $mql->getRecordset($this->mSQL);
		$idx			= 0;
		$rw				= array();
		if($rs){
			while ($row = $rs->fetch_assoc()) {
				$rw[]		= $row;
			}
			$json["Result"]		= "OK";
		} else {
			$json["error"] 		= $mql->getMessages(OUT_TXT);
			$json["Result"]		= "ERROR";
		}
		$json["Records"] 		= $rw;
		$json["Message"] 		= $mql->getMessages(OUT_TXT);
		return json_encode($json);
	}
	function getJsonSelect(){
		$json			= array();
		$mql			= new MQL();
		$rs				= $mql->getRecordset($this->mSQL);
		$idx			= 0;
		if($rs){
			while ($row = $rs->fetch_assoc()) {
				foreach($row as $campo => $valor){
					if ( is_string($valor) ){
						$valor		= htmlentities($valor);//htmlentities( (string) $valor, ENT_QUOTES, 'utf-8', FALSE);
					}
					$json["record_$idx"][$campo]	= $valor; //base64_encode($valor);//utf8_encode($valor);
				}
				$idx++;
			}
		} else {
			$json			= $json["error"] = $mql->getMessages(OUT_TXT);
		}
		return json_encode($json);
	}
	function getEncryptData($content){
		$xA			= new AesCtr();
		$content	= $xA->encrypt($content, $this->mKey, 256);
		$content	= base64_encode($content);
		return $content;
	}
	function getDecryptData($content){
		$xA			= new AesCtr();
		$content 	= base64_decode($content);
		$content 	= $xA->decrypt($content, $this->mKey, 256);
		return $content;
	}
	function checkCTX($ctx){
		$rs		= ($ctx == $this->getCTX()) ? true : false;
		if($rs == false) { $this->mError = array("code" => 1, "msg" => "No autenticado"); }
		return $rs;
	}
	function getCTX(){
		//SERVERKEY + SERVERDATE + USER
		$usr			= "";
		$date			= date("Ymd");
		$ip 			= ( isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : "0";
		return  md5("|" . $usr . "|" . $date . "|" . $ip);
	}
	function getError(){ return json_encode($this->mError);	}

	function getService($url){
		$obj		= null;
		$req 		= $this->getRequest($url);// file_get_contents($url);
		if(!$req){
			setLog("Error al procesar la url : $url");
		} else {
			$req		= $this->getDecryptData($req);
			$obj		= json_decode($req, true);
			/*if(!$obj){

			} else {

			}*/
			/*	str	= base64.decode(str);
			 str	= Aes.Ctr.decrypt(str, CloudConfig.apiKey, 256)
			*/
			/*
			 $data		= (isset($_REQUEST["data"])) ? $svc->getDecryptData($_REQUEST["data"]) : null;
			$command	= (isset($_REQUEST["cmd"])) ? $svc->getDecryptData($_REQUEST["cmd"]) : null;
			$context	= (isset($_REQUEST["ctx"])) ? $svc->getDecryptData($_REQUEST["ctx"]) : null;
			$obj		= json_decode($data, true);
			*/
		}
		return $obj;
	}
	function getRequest($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
		$return = curl_exec($ch); curl_close ($ch);
		return $return;
	}
}

class MQLHDic {
	private $mHEq	= array(
			"INT" 		=> "number",
			"TINYINT" 	=> "number",
			"SMALLINT" 	=> "number",
			"MEDIUMINT" => "number",
			"BIGINT" 	=> "number",
			"YEAR" 		=> "number",
			"TIMESTAMP" => "number",

			"FLOAT" 	=> "number",
			"DOUBLE" 	=> "number",
			"DECIMAL" 	=> "number",

			"VARCHAR" 	=> "text",
			"CHAR" 		=> "text",
			"TEXT" 		=> "text",
			"LONGTEXT" 	=> "textarea",
			"TINYTEXT" 	=> "text",
			"MEDIUMTEXT" => "textarea",
			"DATE" 		=> "date",
			"DATETIME" 	=> "string",
			"TIME" 		=> "number",
			"ENUM"		=> "select",

			"BINARY" 	=> "textarea",
			"BLOB" 		=> "textarea",
			"MEDIUMBLOB" => "textarea"
	);

	function __construct($tipo, $tamanio){
		$mql		= new MQL();
		$clase		=  $this->mHEq[$tipo] ;//$mql->getTipos($tipo);
		$obj		= null;

		switch ($clase){
			case "number";
			break;
		}
		return $obj;
	}

}

?>