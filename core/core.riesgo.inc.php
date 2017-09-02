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
	//================================== Reglas de accion
	public $RN_ELIMINAR_PERSONA					= "PERSONAS.ELIMINAR";
	public $RN_CASTIGAR_CREDITO					= "CREDITOS.CASTIGOS";
	public $RN_CANCELAR_CREDITO					= "CREDITOS.CANCELAR";
	public $RN_MINISTRAR_CREDITO				= "CREDITOS.MINISTRAR";
	
	public $RN_NOMINA_AL_CERRAR					= "NOMINA.AL_CERRAR";
	public $RN_NOMINA_AL_DESPEDIR				= "NOMINA.AL_DESPEDIR";
	public $RN_NOMINA_AL_DESVINCULAR			= "NOMINA.AL_DESVINCULAR";
	public $RN_NOMINA_AL_VINCULAR				= "NOMINA.AL_VINCULAR";
	public $RN_CAJA_AL_CERRAR					= "CAJA.AL_CERRAR";
	
	public $RN_DATOS_AL_ELIMINAR				= "DATA.AL_ELIMINAR";
	public $RN_DATOS_AL_ACTUALIZAR				= "DATA.AL_ACTUALIZAR";
	public $RN_DATOS_AL_ELIMINAR_RECIBO			= "DATA.AL.ELIMINAR.RECIBO";
	
	public $RN_CREDITOS_AL_LIQUIDAR				= "CREDITOS.CUANDO_SE_LIQUIDAN";
	public $RN_CREDITOS_ESTADOCTA_EXEC			= "CREDITOS.CUANDO_EDOCTA_EXEC";
	
	//=========== Reglas sin accion
	public $ELIMINAR_PERIODO_NOMINA				= "NOMINA.ELIMINAR_PERIODO";
	public $PERSONAS_SIN_DATOS_FISCALES			= "PERSONAS.SIN_DATOS_FISCALES";
	public $PERSONAS_SIN_DATO_POBLACIONAL		= "PERSONAS.SIN_DATO_POBLACIONAL";
	public $PERSONAS_SIN_REG_MATRIMONIAL		= "PERSONAS.SIN_REGIMEN_MATRIMONIAL";
	public $PERSONAS_SIN_DATOS_DOCTOS			= "PERSONAS.SIN_DATOS_DOCUMENTALES";
	
	public $PERSONAS_RELS_DOM_SIMPLE			= "PERSONAS.RELS.DOMICILIO_SIMPLE";
	public $PERSONAS_RELS_SIN_DOM				= "PERSONAS.RELS.SIN_DOMICILIO";
	public $PERSONAS_RELS_SOLOACTIV				= "PERSONAS.RELS.SOLO_ACTIVOS";
	
	public $PERSONAS_SIN_DETALLE_ACCESO			= "PERSONAS.DOMICILIO.NO_ACCESO";
	public $PERSONAS_USAR_XCLASIFICACION		= "PERSONAS.USAR_XCLASIFICACION";
	public $PERSONAS_USAR_YCLASIFICACION		= "PERSONAS.USAR_YCLASIFICACION";
	public $PERSONAS_USAR_ZCLASIFICACION		= "PERSONAS.USAR_ZCLASIFICACION";
	public $PERSONAS_USAR_DEXTRANJERO			= "PERSONAS.USAR_DATOSEXTRANJERO";
	public $PERSONAS_USAR_DCOLEGIACION			= "PERSONAS.USAR_DATOSCOLEGIACION";
	
	public $PERSONAS_SIN_DNI_INGRESO			= "PERSONAS.INGRESO.SIN_DNI";
	public $PERSONAS_NO_VALIDAR_DNI				= "PERSONAS.INGRESO.NO_VALIDAR_DNI";
	
	public $PERSONAS_BUSQUEDA_IDINT				= "PERSONAS.BUSQUEDA.ID_INTERNA";
	public $PERSONAS_USAR_DATO_ACCIDENTE		= "PERSONAS.USAR.DATOS_DE_ACCIDENTE";
	public $PERSONAS_CHECKLIST_DINA				= "PERSONAS.USAR.CHECKLIST_DINAMICO";
	
	public $PERSONAS_ACTIVIDAD_EC_SIMPLE		= "PERSONAS.ACTIVIDAD_ECONOMICA.SIMPLE";
	public $PERSONAS_ACTIVIDAD_EC_ASALARIADO	= "PERSONAS.ACTIVIDAD_ECONOMICA.COMO_SALARIOS";
	public $PERSONAS_ACTIVIDAD_SIN_DISPERSION	= "PERSONAS.ACTIVIDAD_ECONOMICA.SIN_DISPERSION";
	public $PERSONAS_ACTIVIDAD_SIN_DOMICILIO	= "PERSONAS.ACTIVIDAD_ECONOMICA.SIN_DOMICILIO";
	public $PERSONAS_ACTIVIDAD_SIN_SCIAN		= "PERSONAS.ACTIVIDAD_ECONOMICA.SIN_SCIAN";
	public $PERSONAS_AML_GWS_ACTIVO				= "PERSONAS.CONSULTA.GSW.DEFECTO";
	public $PERSONAS_OPERAR_ALTO_R				= "PERSONAS.OPERAR.CON_ALTO_RIESGO";
	
	public $CREDITOS_AUTORIZACION_SIN_TASA		= "CREDITOS.AUTORIZACION.SIN_TASA";
	public $CREDITOS_AUTORIZACION_SIN_DISP		= "CREDITOS.AUTORIZACION.SIN_DISPERSION";
	public $CREDITOS_AUTORIZACION_SIN_LUGAR		= "CREDITOS.AUTORIZACION.SIN_LUGAR_PAGO";
	public $CREDITOS_PUEDEN_TASA_CERO			= "CREDITOS.TASAS_PUEDEN_CERO";
	
	public $CREDITOS_PRODUCTOS_SIN_FINALPZO		= "CREDITOS.PRODUCTOS.SIN_FINAL_DE_PLAZO";
	
	public $CREDITOS_ESTADO_CUENTA_SIMPLE		= "CREDITOS.ESTADO_DE_CUENTA.SIMPLE";
	public $CREDITOS_ESTADO_CUENTA_FSIMPLE		= "CREDITOS.ESTADO_DE_CUENTA.FICHA_SIMPLE";
	public $CREDITOS_ESTADO_CUENTA_DETALLE		= "CREDITOS.ESTADO_DE_CUENTA.DETALLADO";
	//public $CREDITOS_ESTADO_CUENTA_EMULA		= "CREDITOS.ESTADO_DE_CUENTA.EMULADO";

	public $CREDITOS_SOLICITUD_SIN_FECHA_ANT	= "CREDITOS.SOLICITUD.SIN_FECHA_PASADA";
	public $CREDITOS_SOLICITUD_SIN_PERIODO_ANT	= "CREDITOS.SOLICITUD.SIN_PERIODO_PASADO";
	public $CREDITOS_SOLICITUD_CON_ORIGEN		= "CREDITOS.SOLICITUD.REQUIEREN_ORIGEN";
	
	public $CREDITOS_ARREND_RES_CON_ANT			= "CREDITOS.ARRENDAMIENTO.RESIDUAL_CON_ANT";
	public $CREDITOS_ARREND_RES_CON_IVA			= "CREDITOS.ARRENDAMIENTO.RESIDUAL_CON_IVA";
	//public $CREDITOS_ARREND_ANT_SIN_IVA			= "CREDITOS.ARRENDAMIENTO.RESIDUAL_CON_IVA";
	
	
	public $CREDITOS_ARREND_SIN_TIIE			= "CREDITOS.ARRENDAMIENTO.NO_USAR_TIIE";
	public $CREDITOS_ARREND_IVA_NOINC			= "CREDITOS.ARRENDAMIENTO.IVA_NO_INC";
	public $CREDITOS_ARREND_COT_NORES			= "CREDITOS.ARRENDAMIENTO.NO_RESIDUALES";
	public $CREDITOS_ARREND_COT_RSIMPLE			= "CREDITOS.ARRENDAMIENTO.FRM_RESIPLE";
	public $CREDITOS_ARREND_ANT_DIV				= "CREDITOS.ARRENDAMIENTO.DIV_ANTICIPO";
	public $CREDITOS_ARREND_FRM_DIS				= "CREDITOS.ARRENDAMIENTO.DISABLE_FLD";
	
	
	public $CREDITOS_DESEMBOLSO_SIN_DESC		= "CREDITOS.DESEMBOLSO_SIN_DESCUENTOS";
	public $CREDITOS_DESEMBOLSO_SIN_CHQ			= "CREDITOS.DESEMBOLSO_SIN_CHEQUE";
	
	public $CREDITOS_REQUIERE_DOMICILIO			= "CREDITOS.TODOS_REQUIEREN_DOMICILIO";
	public $CREDITOS_REQUIERE_ACTIVIDAD			= "CREDITOS.TODOS_REQUIEREN_ACTIVIDAD_ECON";
	public $CREDITOS_PLAN_SIN_OTROS				= "CREDITOS.PLAN_SIN_OTROS";
	public $CREDITOS_PLAN_SIN_OPTS				= "CREDITOS.PLAN_SIN_OPCIONES";
	public $CREDITOS_PLAN_SIN_ANUAL				= "CREDITOS.PLAN_SIN_ANUALIDAD";
	public $CREDITOS_PLAN_CON_PAGESP			= "CREDITOS.PLAN_CON_PAGESP";
	public $CREDITOS_PLAN_CON_CEROS				= "CREDITOS.PLAN_CON_CEROS";
	public $CREDITOS_PLAN_SIN_FINAL				= "CREDITOS.PLAN_SIN_AJUSTE_FINAL";
	
	public $CREDITOS_PLAN_SIMPLE				= "CREDITOS.PLAN_PAGOS.SIMPLE";
	
	public $CREDITOS_PLAN_SDO_FCAP				= "CREDITOS.PLAN_PAGOS.SDO_FINAL_CAP";
	
	public $CREDITOS_ECUENTA_VALIDADOR			= "CREDITOS.ESTADO_DE_CUENTA.VALIDADOR";  //valida cada segmento del estado de cuenta
	
	public $CREDITOS_PAGO_LETRAF				= "CREDITOS.PAGOS_LETRA_FIJA";
	
	public $RECIBOS_SIN_VERSIONIMP				= "RECIBOS.SIN_VERSION_IMPRESA";
	public $RECIBOS_RPT_USE_FECHAREAL			= "RECIBOS.REPORTE.USAR.FECHA_REAL";
	
	public $RECIBOS_ELIM_USE_BACK				= "RECIBOS.AL.ELIMINAR.BACKUP_IMP";
	
	public $AML_CIERRE_NV_RIESGO				= "AML.CIERRE.NO_VALIDAR_RIESGO";
	public $AML_AUTOENVIAR_RMS					= "AML.RIESGO_AUTOENVIAR_RMS";
	
	public $VAL_NO_PERSONA_FALTA_ACT_ECONOM		= "OMITE.PERSONA_FALTA_ACT_ECONOM";
	public $VAL_NO_PERSONA_FALLA_ACT_ECONOM		= "OMITE.PERSONA_FALLA_ACT_ECONOM";
	
	public $RN_USAR_REDIRECTS					= "RN_USAR_REDIRECTS";
	public $RN_USAR_MENU_ALT					= "RN_USAR_USAR_MENU_ALTER";
	
	function getInArray(){
		$arr	= array();
		//eventos
		$arr[$this->RN_CAJA_AL_CERRAR]				= $this->RN_CAJA_AL_CERRAR;
		$arr[$this->RN_DATOS_AL_ACTUALIZAR]			= $this->RN_DATOS_AL_ACTUALIZAR;
		$arr[$this->RN_DATOS_AL_ELIMINAR]			= $this->RN_DATOS_AL_ELIMINAR;
		$arr[$this->RN_NOMINA_AL_CERRAR]			= $this->RN_NOMINA_AL_CERRAR;
		$arr[$this->RN_NOMINA_AL_DESPEDIR]			= $this->RN_NOMINA_AL_DESPEDIR;
		$arr[$this->RN_NOMINA_AL_DESVINCULAR]		= $this->RN_NOMINA_AL_DESVINCULAR;
		$arr[$this->RN_NOMINA_AL_VINCULAR]			= $this->RN_NOMINA_AL_VINCULAR;
		$arr[$this->RN_DATOS_AL_ELIMINAR_RECIBO]	= $this->RN_DATOS_AL_ELIMINAR_RECIBO;
		$arr[$this->RN_CREDITOS_AL_LIQUIDAR]		= $this->RN_CREDITOS_AL_LIQUIDAR;
		
		$arr[$this->RN_ELIMINAR_PERSONA]				= $this->RN_ELIMINAR_PERSONA;
		$arr[$this->RN_CASTIGAR_CREDITO]				= $this->RN_CASTIGAR_CREDITO;
		$arr[$this->RN_CANCELAR_CREDITO]				= $this->RN_CANCELAR_CREDITO;
		$arr[$this->RN_MINISTRAR_CREDITO]				= $this->RN_MINISTRAR_CREDITO;
		$arr[$this->ELIMINAR_PERIODO_NOMINA]			= $this->ELIMINAR_PERIODO_NOMINA;
		$arr[$this->PERSONAS_SIN_DATOS_FISCALES]		= $this->PERSONAS_SIN_DATOS_FISCALES;
		$arr[$this->PERSONAS_SIN_DATO_POBLACIONAL]		= $this->PERSONAS_SIN_DATO_POBLACIONAL;
		$arr[$this->PERSONAS_SIN_DATOS_DOCTOS]			= $this->PERSONAS_SIN_DATOS_DOCTOS;
		$arr[$this->PERSONAS_SIN_DNI_INGRESO]			= $this->PERSONAS_SIN_DNI_INGRESO;
		$arr[$this->PERSONAS_NO_VALIDAR_DNI]			= $this->PERSONAS_NO_VALIDAR_DNI;
		
		$arr[$this->PERSONAS_SIN_REG_MATRIMONIAL]		= $this->PERSONAS_SIN_REG_MATRIMONIAL;
		$arr[$this->PERSONAS_SIN_DETALLE_ACCESO]		= $this->PERSONAS_SIN_DETALLE_ACCESO;
		$arr[$this->PERSONAS_ACTIVIDAD_EC_SIMPLE]		= $this->PERSONAS_ACTIVIDAD_EC_SIMPLE;
		$arr[$this->PERSONAS_ACTIVIDAD_EC_ASALARIADO]	= $this->PERSONAS_ACTIVIDAD_EC_ASALARIADO;
		$arr[$this->PERSONAS_ACTIVIDAD_SIN_DISPERSION]	= $this->PERSONAS_ACTIVIDAD_SIN_DISPERSION;
		$arr[$this->PERSONAS_ACTIVIDAD_SIN_DOMICILIO]	= $this->PERSONAS_ACTIVIDAD_SIN_DOMICILIO;
		$arr[$this->PERSONAS_ACTIVIDAD_SIN_SCIAN]		= $this->PERSONAS_ACTIVIDAD_SIN_SCIAN;
		$arr[$this->PERSONAS_USAR_XCLASIFICACION]		= $this->PERSONAS_USAR_XCLASIFICACION;
		$arr[$this->PERSONAS_USAR_YCLASIFICACION]		= $this->PERSONAS_USAR_YCLASIFICACION;
		$arr[$this->PERSONAS_USAR_ZCLASIFICACION]		= $this->PERSONAS_USAR_ZCLASIFICACION;
		$arr[$this->PERSONAS_USAR_DEXTRANJERO]			= $this->PERSONAS_USAR_DEXTRANJERO;
		$arr[$this->PERSONAS_USAR_DCOLEGIACION]			= $this->PERSONAS_USAR_DCOLEGIACION;
		
		$arr[$this->CREDITOS_AUTORIZACION_SIN_TASA]		= $this->CREDITOS_AUTORIZACION_SIN_TASA;
		
		$arr[$this->PERSONAS_RELS_DOM_SIMPLE]			= $this->PERSONAS_RELS_DOM_SIMPLE;
		$arr[$this->PERSONAS_RELS_SOLOACTIV]			= $this->PERSONAS_RELS_SOLOACTIV;
		$arr[$this->PERSONAS_BUSQUEDA_IDINT]			= $this->PERSONAS_BUSQUEDA_IDINT;
		$arr[$this->PERSONAS_CHECKLIST_DINA]			= $this->PERSONAS_CHECKLIST_DINA;
		
		$arr[$this->CREDITOS_REQUIERE_DOMICILIO]		= $this->CREDITOS_REQUIERE_DOMICILIO;
		$arr[$this->CREDITOS_REQUIERE_ACTIVIDAD]		= $this->CREDITOS_REQUIERE_ACTIVIDAD;
		$arr[$this->CREDITOS_PLAN_SIN_OTROS]			= $this->CREDITOS_PLAN_SIN_OTROS;
		$arr[$this->CREDITOS_PLAN_SIN_OPTS]			= $this->CREDITOS_PLAN_SIN_OPTS;
		$arr[$this->CREDITOS_PLAN_SIN_ANUAL]			= $this->CREDITOS_PLAN_SIN_ANUAL;
		$arr[$this->CREDITOS_PLAN_CON_PAGESP]			= $this->CREDITOS_PLAN_CON_PAGESP;
		$arr[$this->CREDITOS_PLAN_CON_CEROS]			= $this->CREDITOS_PLAN_CON_CEROS;
		
		$arr[$this->CREDITOS_AUTORIZACION_SIN_DISP]		= $this->CREDITOS_AUTORIZACION_SIN_DISP;
		$arr[$this->CREDITOS_AUTORIZACION_SIN_LUGAR]	= $this->CREDITOS_AUTORIZACION_SIN_LUGAR;
				
		$arr[$this->CREDITOS_DESEMBOLSO_SIN_DESC]		= $this->CREDITOS_DESEMBOLSO_SIN_DESC;
		$arr[$this->CREDITOS_DESEMBOLSO_SIN_CHQ]		= $this->CREDITOS_DESEMBOLSO_SIN_CHQ;
		
		$arr[$this->CREDITOS_PRODUCTOS_SIN_FINALPZO]	= $this->CREDITOS_PRODUCTOS_SIN_FINALPZO;
		$arr[$this->CREDITOS_SOLICITUD_SIN_FECHA_ANT]	= $this->CREDITOS_SOLICITUD_SIN_FECHA_ANT;
		$arr[$this->CREDITOS_SOLICITUD_SIN_PERIODO_ANT]	= $this->CREDITOS_SOLICITUD_SIN_PERIODO_ANT;
		$arr[$this->CREDITOS_SOLICITUD_CON_ORIGEN]		= $this->CREDITOS_SOLICITUD_CON_ORIGEN;
		
		$arr[$this->CREDITOS_PLAN_SIMPLE]				= $this->CREDITOS_PLAN_SIMPLE;
		$arr[$this->CREDITOS_PLAN_SIN_FINAL]			= $this->CREDITOS_PLAN_SIN_FINAL;
		
		$arr[$this->CREDITOS_ESTADO_CUENTA_SIMPLE]		= $this->CREDITOS_ESTADO_CUENTA_SIMPLE;
		$arr[$this->CREDITOS_ESTADO_CUENTA_FSIMPLE]		= $this->CREDITOS_ESTADO_CUENTA_FSIMPLE;
		$arr[$this->CREDITOS_ECUENTA_VALIDADOR]			= $this->CREDITOS_ECUENTA_VALIDADOR;
		$arr[$this->CREDITOS_ESTADO_CUENTA_DETALLE]		= $this->CREDITOS_ESTADO_CUENTA_DETALLE;
		$arr[$this->CREDITOS_ARREND_RES_CON_ANT]		= $this->CREDITOS_ARREND_RES_CON_ANT;
		$arr[$this->CREDITOS_ARREND_RES_CON_IVA]		= $this->CREDITOS_ARREND_RES_CON_IVA;
		$arr[$this->CREDITOS_ARREND_SIN_TIIE]			= $this->CREDITOS_ARREND_SIN_TIIE;
		$arr[$this->CREDITOS_ARREND_IVA_NOINC]			= $this->CREDITOS_ARREND_IVA_NOINC;
		$arr[$this->CREDITOS_ARREND_COT_NORES]			= $this->CREDITOS_ARREND_COT_NORES;
		$arr[$this->CREDITOS_ARREND_ANT_DIV]			= $this->CREDITOS_ARREND_ANT_DIV;
		$arr[$this->CREDITOS_ARREND_COT_RSIMPLE]		= $this->CREDITOS_ARREND_COT_RSIMPLE;
		//$arr[$this->]			= $this->;
		//$arr[$this->]			= $this->;
		//$arr[$this->CREDITOS_ESTADO_CUENTA_EMULA]		= $this->CREDITOS_ESTADO_CUENTA_EMULA;
		$arr[$this->CREDITOS_ARREND_FRM_DIS]			= $this->CREDITOS_ARREND_FRM_DIS;
		$arr[$this->CREDITOS_PAGO_LETRAF]				= $this->CREDITOS_PAGO_LETRAF;
		
		//$arr[$this->]			= $this->;
		
		$arr[$this->RECIBOS_SIN_VERSIONIMP]				= $this->RECIBOS_SIN_VERSIONIMP;		
		$arr[$this->RECIBOS_RPT_USE_FECHAREAL]			= $this->RECIBOS_RPT_USE_FECHAREAL;
		$arr[$this->RECIBOS_ELIM_USE_BACK]				= $this->RECIBOS_ELIM_USE_BACK;
		
		//$arr[$this->]			= $this->;
		$arr[$this->PERSONAS_RELS_SIN_DOM]				= $this->PERSONAS_RELS_SIN_DOM;
		$arr[$this->CREDITOS_PUEDEN_TASA_CERO]			= $this->CREDITOS_PUEDEN_TASA_CERO;
		$arr[$this->PERSONAS_USAR_DATO_ACCIDENTE]		= $this->PERSONAS_USAR_DATO_ACCIDENTE;
		//$arr[$this->]			= $this->;
		$arr[$this->AML_CIERRE_NV_RIESGO]				= $this->AML_CIERRE_NV_RIESGO;
		$arr[$this->VAL_NO_PERSONA_FALTA_ACT_ECONOM]	= $this->VAL_NO_PERSONA_FALTA_ACT_ECONOM;
		$arr[$this->VAL_NO_PERSONA_FALLA_ACT_ECONOM]	= $this->VAL_NO_PERSONA_FALLA_ACT_ECONOM;
		$arr[$this->PERSONAS_OPERAR_ALTO_R]				= $this->PERSONAS_OPERAR_ALTO_R;
		$arr[$this->RN_USAR_REDIRECTS]					= $this->RN_USAR_REDIRECTS;
		$arr[$this->RN_USAR_MENU_ALT]					= $this->RN_USAR_MENU_ALT;
		//$arr[$this->]			= $this->;
		
		$arr[$this->AML_AUTOENVIAR_RMS]					= $this->AML_AUTOENVIAR_RMS;
		return $arr;
	}
}


class cReglaDeNegocio {
	private $mEvento	= false;
	private $mMessages	= "";
	private $mVars		= array();
	private $mContrato	= array();
	private $mReglas	= null;
	private $mRead		= array();
	private $mValores	= null;
	private $mCodigo	= array();
	private $mDestPers	= array(); //Personas destinatarios
	private $mLista		= array();
	 
	function __construct($evento = false){
		$this->mEvento	= $evento;
		//Obtener de la BD
		//evaluar
		//dispara eventos
	}
	function addPersonasDestinatarios($arr){
		if(is_array($arr)){
			$this->mDestPers		= array_merge($this->mDestPers, $arr);
		} else {
			$this->mDestPers[$arr]	= $arr;
		}
	}
	function getEnviarMensajeEnEvento($evento = false){
		$evento			= ($evento == false ) ? $this->mEvento : $evento;
		$this->mEvento	= $evento;
		//TODO: Terminar
		$arr			= $this->getEventos();
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
		$this->mRead	= $xLis->getInArray();
		return $this->mRead;
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
		//$xPrg			= new cSistema_programacion_de_avisos();
		$this->mMessages	.= "====\tEvento: $evento\r\n";
		foreach ($rs as $datos){
//				$datos["idprograma"]
				$this->mMessages	.= "====\tProgramacion: " . $datos["idprograma"] . "\r\n";
				$xAlerts->addPersonasDestinatarios($this->mDestPers);
				$xAlerts->setProcesarProgramacion($datos["idprograma"], $this->mVars );
				$this->mMessages	.= $xAlerts->getMessages();
		}		
	}
	function setExecuteActions($evento = false){	$this->setEjecutarAlertas($evento);	}
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function readValores(){
		$xCache	= new cCache();
		
		$idxv	= "reglas-entidad-vals";
		$idxc	= "reglas-entidad-cod";
		
		$this->mCodigo	= $xCache->get($idxc);
		$this->mValores	= $xCache->get($idxv);
		
		if(!is_array($this->mCodigo) OR !is_array($this->mValores)){
		
			$rs		= $xCache->get("reglas-entidad");
			if($rs == null){
				$xQL	= new MQL();
				$rs		= $xQL->getDataRecord("SELECT * FROM `entidad_reglas`");
				$xCache->set("reglas-entidad", $rs);
			}
			$xRul		= new cEntidad_reglas();
			
			//setLog(getMemoriaLibre(true));
			
			foreach($rs as $rw){
				$xRul->setData($rw);
				if(isset( $this->mRead[ $xRul->nombre()->v() ] )){
					$this->mCodigo[$xRul->nombre()->v()]	= $xRul->reglas()->v();
					
					//eval($xRul->reglas()->v());
					
					$this->mValores[$xRul->nombre()->v()]	= ($xRul->valor()->v() == 1) ? true : false;
				}
			}
			$xCache->set($idxc, $this->mCodigo);
			$xCache->set($idxv, $this->mValores);
		}
	}
	/**
	 * Devuelve un valor true/false de una regla
	 * @param string $regla	Nombre de la regla a ejecutar
	 * @return boolean
	 */
	function getValorPorRegla($regla = null){
		if($this->mValores == null){ $this->readValores(); }
		$val		= null;
		if($regla != null){ 
			if(isset($this->mValores[$regla])){ $val = $this->mValores[$regla]; }
		}
		return $val;
	}
	/**
	 * Retorna el valor contenido en el campo reglas como codigo php
	 * @param string $regla
	 * @return string
	 */
	function getCodigoPorRegla($regla = null){
		if($this->mValores == null){ $this->readValores(); }
		$val		= "";
		if($regla !== null){
			if(isset($this->mCodigo[$regla])){ $val = $this->mCodigo[$regla]; }
		}
		return $val;
	}
	function getArrayPorRegla($regla){
		$lst	= $this->getCodigoPorRegla($regla);
		return explode(",", $lst);
	}
	function getInArrayPorRegla($regla, $key){
		$arr	= $this->getArrayPorRegla($regla);
		
		return (in_array($key, $arr)) ? true : false; 
	}
}

class cReglasDeCalificacion {
	private $mClave		= false;
	private $mObj		= null;
	private $mInit		= false;
	private $mNombre	= "";
	private $mMessages	= "";
	private $mTipo		= 0; //iDE_RECIBO iDE_CREDITO etc
	private $mDocumento	= 0;
	private $mValores	= array();//valores si no
	private $mVigentes	= array();//valores si no
	private $mTiempoEspera		= 1;
	private $mIncumple			= 0;
	private $mCumple			= 0;
	private $mRegla				= "";
	
	public $CRED_FALTA_AVALES	= "CREDITO_FALTA_AVALES";
	public $CRED_FALTA_GARANT	= "CREDITO_FALTA_GARANTIAS";
	public $CRED_FALTA_GRUPO	= "CREDITO_SIN_GRUPO";
	public $CRED_FALTA_PLAN		= "CREDITO_SIN_PLAN";
	public $CRED_FALTA_CAHORR	= "CREDITO_SIN_CUENTAAHORRO";
	public $CRED_FALLA_CAHORR	= "CREDITO_FALLA_CUENTAAHORRO";
	public $CRED_FALLA_OFICIAL	= "CREDITO_FALLA_OFICIAL";
	public $CRED_FALLA_STATUS	= "CREDITO_ESTADO_INCORRECTO";
	public $CRED_FALLA_NPAGOS	= "CREDITO_NPAGOS_INCORRECTO";
	public $CRED_FALLA_PAGMAX	= "CREDITO_FALLA_PAGOMIN";
	public $CRED_FALLA_PAGMIN	= "CREDITO_FALLA_PAGOMAX";
	public $CRED_FALLA_DEST		= "CREDITO_FALLA_DESTINO";
	public $CRED_FALLA_DESTIVA	= "CREDITO_FALLA_DESTINO_IVA";
	public $CRED_FALLA_FPERIODO	= "CREDITO_FALLA_FECHA_PERIODO";
	public $CRED_FALTA_FPERIODO	= "CREDITO_FALTA_PERIODO";
	
	public $CRED_FALLA_PERSONA	= "CREDITO_FALLA_PERSONA";
	
	public $PERS_FALTA_GRUPO	= "PERSONA_FALTA_GRUPO";
	public $PERS_FALTA_CL		= "PERSONA_FALTA_CAJALOCAL";
	public $PERS_FALTA_EMP		= "PERSONA_FALTA_EMPRESA";
	public $PERS_FALTA_OFICIAL	= "PERSONA_FALTA_OFICIAL";
	public $PERS_FALTA_REPLEGAL	= "PERSONA_FALTA_REP_LEGAL";
	public $PERS_FALTA_DOM		= "PERSONA_FALTA_DOMICILIO";
	public $PERS_FALLA_DOM		= "PERSONA_FALLA_DOMICILIO";
	public $PERS_DOM_INC		= "PERSONA_DOMICILIO_INCOMP";
	
	public $PERS_FALTA_AEC		= "PERSONA_FALTA_ACT_ECONOM";
	public $PERS_FALLA_AEC		= "PERSONA_FALLA_ACT_ECONOM";
	public $PERS_FALTA_TING		= "PERSONA_FALTA_TIPO_INGRESO";
	public $PERS_FALLA_AML1		= "PERSONA_FALLA_AML1";
	public $PERS_FALLA_AML2		= "PERSONA_FALLA_AML2";
	public $PERS_BLOQUEADA		= "PERSONA_BLOQUEADA";
	//public $PERS_FALLA_AML2		= "PERSONA_FALLA_AML2";
	public $PERS_FALTA_DEXT		= "PERSONA_FALTA_DEXTRA";
	public $PERS_DOMCP_VALID	= "PERSONA_DOM_CP_VALIDO";
	
	//public $ESPERA_REG_HEAVY	= 5; //Tiempo de espera en reglas pesadas
	//public $ESPERA_REG_NORM		= 1; //Tiempo de espera en reglas Normales
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); }
	function init($data = false){
		$xQL	= new MQL();
		$data	= (is_array($data)) ? $data : $xQL->getDataRow("SELECT * FROM `entidad_calificacion` WHERE `identidad_calificacion`=". $this->mClave);
		if(isset($data["identidad_calificacion"])){
			$this->mObj		= new cEntidad_calificacion(); //Cambiar
			$this->mObj->setData($data);
			$this->mClave	= $this->mObj->identidad_calificacion()->v();
			$this->mTipo	= $this->mObj->tipo_de_objeto()->v();
			$this->mDocumento= $this->mObj->clave_de_documento()->v();
			$this->mRegla	= $this->mObj->topico()->v();
			$this->mCumple	= $this->mObj->cumple()->v();
			
			$this->mInit	= true;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){
		$this->mObj			= null;
		$this->mMessages	= "";
	}
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}
	function initByRegla($regla){
		$xCache		= new cCache();
		$xQL		= new MQL();
		$datos		= $xQL->getDataRow("SELECT * FROM `entidad_calificacion` WHERE `tipo_de_objeto`='" . $this->mTipo . "' AND `clave_de_documento`='" . $this->mDocumento . "' AND `topico`='$regla' LIMIT 0,1");

		return $this->init($datos);
	}
	function add($regla, $cumple = false, $user=false,$vencimiento = false, $riesgo = false, $fecha = false){
		$xQL		= new MQL();
		$xCache		= new cCache();
		$xF			= new cFecha(0, $fecha);
		$fecha		= $xF->getFechaISO($fecha);
		$regla		= strtoupper($regla);
		$cumple		= ($cumple == true) ? 1 : 0;
		$riesgo		= setNoMenorQueCero($riesgo,2);
		//$semanaSiguiente = time() + (7 * 24 * 60 * 60);
		$tiempo		= time();
		//Hora standard 1 Hora
		$vencimiento= ($vencimiento === false) ? $tiempo + ($this->mTiempoEspera * 60 * 60) : $xF->getInt($vencimiento);
		$user		= setNoMenorQueCero($user);
		$usuario	= ($user <= 0) ? getUsuarioActual() : $user;
		//ver si esta en cache
		$IDCache	= $this->mDocumento . "-" . $this->mTipo . "-$regla-$cumple"; //si es usuario
		$ready		= false;
		$existe		= $xCache->get($IDCache);
		if($existe === null){
			//Eliminar primero el contenedor
			//$xQL->setRawQuery("DELETE FROM `entidad_calificacion` WHERE `tipo_de_objeto`='" . $this->mTipo . "' AND `clave_de_documento`='" . $this->mDocumento . "' AND `topico`='$regla'");
			
			if($this->initByRegla($regla) == true){
				//Actualizar si es diferente
				if($this->mCumple !== $cumple){
					$xQL->setRawQuery("UPDATE `entidad_calificacion` SET fecha_de_revision='$fecha', usuario=$usuario,  cumple=$cumple, tiempo= $tiempo, vencimiento=$vencimiento, riesgo=$riesgo WHERE `tipo_de_objeto`='" . $this->mTipo . "' AND `clave_de_documento`='" . $this->mDocumento . "' AND `topico`='$regla'");
				}
				$ready	= true;
				
			} else {
				$sql	= "INSERT INTO entidad_calificacion (tipo_de_objeto, clave_de_documento, fecha_de_revision, usuario, topico, cumple, tiempo, vencimiento, riesgo)
	    			VALUES(" . $this->mTipo . ", " . $this->mDocumento .", '$fecha', $usuario, '$regla', $cumple, $tiempo, $vencimiento, $riesgo)";
				$xQL->setRawQuery($sql);
				$ready	= true;
				
			}
			//Guardar en Cache por 15 minutos 15 *60
			$xCache->set($IDCache, $tiempo, 900);
		} else {
			$ready		= true;
		}
		
		return $ready;
	}
	function setCredito($credito = false){
		$credito			= setNoMenorQueCero($credito);
		$this->mDocumento	= $credito;
		$this->mTipo		= iDE_CREDITO;
		
	}
	function setPersona($persona	= false){
		$persona			= setNoMenorQueCero($persona);
		$this->mDocumento	= $persona;
		$this->mTipo		= iDE_SOCIO;
	}
	function getValoresDeCalificacion(){
		$xQL	= new MQL();
		$xF		= new cFecha();
		$rs		= $xQL->getDataRecord("SELECT * FROM `entidad_calificacion` WHERE `tipo_de_objeto`='" . $this->mTipo . "' AND `clave_de_documento`='" . $this->mDocumento . "'");
		$FInt	= $xF->getInt(false);
		foreach ($rs as $rw){
			//```identidad_calificacion`,`tipo_de_objeto`,`clave_de_documento`,`fecha_de_revision`,`usuario`,`topico`,`cumple`,`tiempo`,`vencimiento`,`riesgo`,
			$this->mValores[$rw["topico"]]	= $rw["cumple"];//($rw["cumple"] == 0) ? false : true; 
			$this->mVigentes[$rw["topico"]]	= ($rw["cumple"] == SYS_UNO AND ($FInt>$rw["vencimiento"])) ? true : false;
			if($rw["cumple"] == SYS_UNO){
				$this->mCumple++;
			} else {
				$this->mIncumple++;
			}
		}
		return $this->mValores;
	}
	function getEsVigente($regla){
		return (isset($this->mVigentes[$regla])) ? $this->mVigentes[$regla] : false;
	}
	function getNumeroIncumplido(){ return $this->mIncumple; }
}

class cRiesgos {
	public $URL_RISK_EXPORT	= "";
	
	function __construct(){
		
	}
	function getNivelarR($riesgo){
		
		if($riesgo <= 25){
			$riesgo	= SYS_RIESGO_BAJO;
		} else if($riesgo >=26 AND $riesgo <=75){
			$riesgo	= SYS_RIESGO_MEDIO;
		} else if ($riesgo>=76){
			$riesgo	= SYS_RIESGO_ALTO;
		}
		return $riesgo;
	}

	function setExportToRMS(){
		
	}
}
?>