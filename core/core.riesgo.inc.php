<?php
/**
 * @see Core Captacion File
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package seguimiento
 *  Core Captacion File
 * 		10/04/2008 Iniciar Funcion de Notificaciones 360
 */

include_once("core.deprecated.inc.php");
include_once("entidad.datos.php");
include_once("core.config.inc.php");
include_once("core.fechas.inc.php");
include_once("core.db.inc.php");
@include_once("../libs/sql.inc.php");

//=====================================================================================================
//------------------------------- Reglas de negocio
define("RN_NOMINA_AL_CERRAR", "NOMINA.AL_CERRAR");
define("RN_NOMINA_AL_DESPEDIR", "NOMINA.AL_DESPEDIR");
define("RN_NOMINA_AL_DESVINCULAR", "NOMINA.AL_DESVINCULAR");

define("RN_CAJA_AL_CERRAR",  "CAJA.AL_CERRAR");
define("RN_DATOS_AL_ELIMINAR", "DATA.AL_ELIMINAR");
define("RN_DATOS_AL_ACTUALIZAR",  "DATA.AL_ACTUALIZAR");
//=====================================================================================================

//define("")
function getRiesgoDeVencimiento($credito){
	//$CEdoCivil	= "";

	//Buscar antecedentes de vencimiento
	//Buscar Antecendentes de Atraso
	//riesgo de no pago por enfermedad
	//rango productivo del socio
	//$DSoc	= getDatosSocio($idsocio);
	/**
	 * Medir la capacidad de pago
	 */
}

function getRiesgoDeIncidencia($socio){  }
function getRiesgoComunPorNucleoFamiliar($socio, $explain = false){
	$sqlRC = "SELECT
	`eacp_config_bases_de_integracion`.`descripcion`,
	`socios_relaciones`.`socio_relacionado`,
	COUNT(`socios_relaciones`.`numero_socio`)      AS `relaciones`,
	COUNT(`creditos_solicitud`.`numero_solicitud`) AS `creditos`,
	SUM(`creditos_solicitud`.`saldo_actual`)       AS `monto`
FROM
	`socios_relaciones` `socios_relaciones`
		INNER JOIN `eacp_config_bases_de_integracion_miembros` `eacp_config_bases_de_integracion_miembros`
		ON `socios_relaciones`.`tipo_relacion` = `eacp_config_bases_de_integracion_miembros`.
		`miembro`
			INNER JOIN `eacp_config_bases_de_integracion` `eacp_config_bases_de_integracion`
			ON `eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = `eacp_config_bases_de_integracion`.
			`codigo_de_base`
				INNER JOIN `creditos_solicitud` `creditos_solicitud`
				ON `socios_relaciones`.`numero_socio` = `creditos_solicitud`.
				`numero_socio`
WHERE
	(`socios_relaciones`.`socio_relacionado` = $socio)
	AND
	(`creditos_solicitud`.`saldo_actual` >" . TOLERANCIA_SALDOS . ")
	AND
	(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 5001)
	AND
	(`socios_relaciones`.`consanguinidad` !=99)
GROUP BY
	`socios_relaciones`.`socio_relacionado`,
	`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`";

	$aRxN	= obten_filas($sqlRC);

	if ( $explain == false ){
		return $aRxN["monto"];
	} else {
		return "<fieldset>
					<legend>Riesgo Comun por Nucleo Familiar</legend>
						Numero de Personas Relacionados    : " .  $aRxN["relaciones"] .  " <br />
						Numero de Creditos Relacionados  : " .  $aRxN["creditos"] .  " <br />
						Monto de Creditos Relacionados   : " .  getFMoney($aRxN["monto"]) .  "
				</fieldset>";
	}

}
function getRiesgoComunPorAvales($socio, $explain = false){
$sqlRxA = "SELECT
	`eacp_config_bases_de_integracion`.`descripcion`,
	`socios_relaciones`.`numero_socio`,
	COUNT(`socios_relaciones`.`socio_relacionado`) AS `relaciones`,
	COUNT(`creditos_solicitud`.`numero_solicitud`) AS `creditos`,
	SUM(`creditos_solicitud`.`saldo_actual`)       AS `riesgo`
FROM
	`socios_relaciones` `socios_relaciones`
		INNER JOIN `eacp_config_bases_de_integracion_miembros` `eacp_config_bases_de_integracion_miembros`
		ON `socios_relaciones`.`tipo_relacion` = `eacp_config_bases_de_integracion_miembros`.
		`miembro`
			INNER JOIN `eacp_config_bases_de_integracion` `eacp_config_bases_de_integracion`
			ON `eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = `eacp_config_bases_de_integracion`.
			`codigo_de_base`
				INNER JOIN `creditos_solicitud` `creditos_solicitud`
				ON `socios_relaciones`.`socio_relacionado` =
				`creditos_solicitud`.`numero_socio`
WHERE
	(`socios_relaciones`.`numero_socio` =$socio) AND

	(`creditos_solicitud`.`saldo_actual` >" . TOLERANCIA_SALDOS . ") AND
	(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 5002)
GROUP BY
	`socios_relaciones`.`numero_socio`,
	`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`
";
	$aRxN	= obten_filas($sqlRxA);
	if ( $explain == false ){
		return $aRxN["monto"];
	} else {
		return "<fieldset>
					<legend>Riesgo Comun por Avales Otorgados</legend>
						Numero de Personas Relacionados    : " .  $aRxN["relaciones"] .  " <br />
						Numero de Creditos Relacionados  : " .  $aRxN["creditos"] .  " <br />
						Monto de Creditos Relacionados   : " .  getFMoney($aRxN["riesgo"]) .  "
				</fieldset>";
	}
}

function getRiesgoPorEdad($edad){
	$factor_por_edad	= 1;
	//44 a�os de vida productiva
	//edad productiva
	$factor_lineal 		= 100 / (EDAD_PRODUCTIVA_MAXIMA - EDAD_PRODUCTIVA_MINIMA);
	$avanzado 			= ($edad - EDAD_PRODUCTIVA_MINIMA) * $factor_lineal;
	$factor_por_edad 	= $avanzado * 100;
}
/**
 * @param int $socio
 * @return float
 * @see obtiene e� riesgo en monto por el promedio de creditos
 */
function getRiesgoDeRetrasoDePago($socio){

	$x	= array();

	$sql = "SELECT COUNT(numero_solicitud) AS 'creditos', SUM(monto_autorizado) AS 'monto',
			SUM(DATEDIFF(fecha_ultimo_mvto, fecha_vencimiento)) AS 'retraso'
			FROM creditos_solicitud
			WHERE numero_socio=$socio
			AND saldo_actual<=0
			GROUP BY numero_socio";
	$d 			= obten_filas($sql);


	$dias		= round($d["retraso"] / $d["creditos"], 0);
	$monto		= $d["monto"] / $d["creditos"];

	$riesgo		= getRiesgoPorDias($dias) * $monto;
	$x["monto"]		= $monto;
	$x["dias"]		= $dias;
	$x["riesgo"]	= $riesgo;

	return 		$x;
}
function getRiesgoPorDias($dias = 0, $tipo_de_credito = 1){

	$riesgo	= 0.01;

	if ($dias >= 1 and $dias<=7){
		$riesgo	= 0.04;
	} elseif($dias>=8 and $dias<=30){
		$riesgo	= 0.15;
	} elseif($dias>=31 and $dias<=60){
		$riesgo	= 0.30;
	} elseif($dias>=61 and $dias<=91){
		$riesgo	= 0.50;
	} elseif($dias>=91 and $dias<=120){
		$riesgo	= 0.75;
	} elseif($dias>=120 and $dias<=180){
		$riesgo	= 0.90;
	} elseif($dias>=181){
		$riesgo	= 1.00;
	}
return $riesgo;
}
/*
 * Criterios de Riesgo Factores de Riesgo
1. Estructura financiera y capacidad de pago
2. Fuentes de financiamiento
3. Administraci�n y toma de decisiones
1. Riesgo financiero
4. Calidad y oportunidad de la informaci�n financiera
5. Posicionamiento y mercado en el que participa
2. Riesgo industria - Mercados Objetivo
- Criterios de Aceptaci�n de Riesgos
3. Experiencia crediticia 6. Experiencia crediticia
4. Riesgo Pa�s 7. Riesgo Pa�s
 */
function getRiesgoPorIncumplimiento($socio){
	//cuenta los compromisos incumplidos
	//cuenta las notificaciones vencidas
}


class cReglasDeNegocioLista {
	//public $SOLICITAR_PERFIL_TRANSACCIONAL = true;
	
	public $RN_ELIMINAR_PERSONA			= "PERSONAS.ELIMINAR";
	public $RN_CASTIGAR_CREDITO			= "CREDITOS.CASTIGOS";
	public $RN_CANCELAR_CREDITO			= "CREDITOS.CANCELAR";
	public $RN_MINISTRAR_CREDITO		= "CREDITOS.MINISTRAR";
	
	public $RN_NOMINA_AL_CERRAR			= "NOMINA.AL_CERRAR";
	public $RN_NOMINA_AL_DESPEDIR		= "NOMINA.AL_DESPEDIR";
	public $RN_NOMINA_AL_DESVINCULAR	= "NOMINA.AL_DESVINCULAR";
	public $RN_CAJA_AL_CERRAR			= "CAJA.AL_CERRAR";
	
	public $RN_DATOS_AL_ELIMINAR		= "DATA.AL_ELIMINAR";
	public $RN_DATOS_AL_ACTUALIZAR		= "DATA.AL_ACTUALIZAR";
	public $RN_DATOS_AL_ELIMINAR_RECIBO	= "DATA.AL.ELIMINAR.RECIBO";
	
	public $ELIMINAR_PERIODO_NOMINA		= "NOMINA.ELIMINAR_PERIODO";
	
	function getInArray(){
		$arr	= array();
		//eventos
		$arr[$this->RN_CAJA_AL_CERRAR]				= $this->RN_CAJA_AL_CERRAR;
		$arr[$this->RN_DATOS_AL_ACTUALIZAR]			= $this->RN_DATOS_AL_ACTUALIZAR;
		$arr[$this->RN_DATOS_AL_ELIMINAR]			= $this->RN_DATOS_AL_ELIMINAR;
		$arr[$this->RN_NOMINA_AL_CERRAR]			= $this->RN_NOMINA_AL_CERRAR;
		$arr[$this->RN_NOMINA_AL_DESPEDIR]			= $this->RN_NOMINA_AL_DESPEDIR;
		$arr[$this->RN_NOMINA_AL_DESVINCULAR]		= $this->RN_NOMINA_AL_DESVINCULAR;
		$arr[$this->RN_DATOS_AL_ELIMINAR_RECIBO]	= $this->RN_DATOS_AL_ELIMINAR_RECIBO;
		
		$arr[$this->RN_ELIMINAR_PERSONA]			= $this->RN_ELIMINAR_PERSONA;
		$arr[$this->RN_CASTIGAR_CREDITO]			= $this->RN_CASTIGAR_CREDITO;
		$arr[$this->RN_CANCELAR_CREDITO]			= $this->RN_CANCELAR_CREDITO;
		$arr[$this->RN_MINISTRAR_CREDITO]			= $this->RN_MINISTRAR_CREDITO;
		$arr[$this->ELIMINAR_PERIODO_NOMINA]		= $this->ELIMINAR_PERIODO_NOMINA;
		return $arr;
	}
}


class cReglaDeNegocio {
	private $mEvento	= false;
	private $mMessages	= "";
	private $mVars		= array();
	private $mContrato	= array();
	private $mReglas	= null;
	function __construct($evento = false){
		$this->mEvento	= $evento;
		//Obtener de la BD
		//evaluar
		//dispara eventos
	}
	
	function getEnviarMensajeEnEvento($evento = false){
		$evento			= ($evento == false ) ? $this->mEvento : $evento;
		$this->mEvento	= $evento;
		//TODO: Terminar
		$arr	= $this->getEventos();
		foreach ($arr as $clave => $valor){ $arr[$clave] = true; }
		//enviar mensajes
		return true;
	}
	function getNecesitaAutorizacion($evento = false){
		$evento			= ($evento == false ) ? $this->mEvento : $evento;
		$this->mEvento	= $evento;
		return true;
	}
	function getAutorizacion($usuario, $objeto, $clave){	}
	function reglas(){
		if($this->mReglas == null){ $this->getEventos(); }
		return $this->mReglas;
	}	
	function getEventos(){
		$xLis			= new cReglasDeNegocioLista();
		$this->mReglas	= $xLis;
		return $xLis->getInArray();
	}
	function setVariables($vars){ $this->mVars	= array_merge($this->mVars, $vars); }
	function addVariable($tipo, $valor){ $this->mVars[$tipo] = $valor; }
	function setEjecutarAlertas($evento = false){
		$evento			= ($evento == false ) ? $this->mEvento : $evento;
		$this->mEvento	= $evento;
		//$xAlerts		= new cAlertasDelSistema();
		//obtener eventos
		$xAlerts		= new cAlertasDelSistema();
		$rs				= $xAlerts->getDatAlertasPorEvento($evento);
		$xPrg			= new cSistema_programacion_de_avisos();
		foreach ($rs as $datos){
//				$xPrg->setData($rs);
				$xAlerts->setProcesarProgramacion($datos["idprograma"], $this->mVars );
				$this->mMessages	.= $xAlerts->getMessages();
		}		
	}
	function setExecuteActions($evento = false){	$this->setEjecutarAlertas($evento);	}
	function getMessages($put = OUT_HTML){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
}

?>