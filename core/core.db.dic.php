<?php
include_once("core.db.inc.php");
include_once("core.config.inc.php");

/*	ORM: Tabla:	general_sucursales	-	Generado:	[24/9/2014 13:42]	*/
class cGeneral_sucursales {
	private $mCampos	= array(
			"codigo_sucursal" => array("N"=>"codigo_sucursal","T"=>"VARCHAR","V"=>"","L"=>10),
			"nombre_sucursal" => array("N"=>"nombre_sucursal","T"=>"VARCHAR","V"=>"","L"=>55),
			"gerente_sucursal" => array("N"=>"gerente_sucursal","T"=>"BIGINT","V"=>"1","L"=>20),
			"caja_local_residente" => array("N"=>"caja_local_residente","T"=>"INT","V"=>"1","L"=>4),
			"titular_de_cobranza" => array("N"=>"titular_de_cobranza","T"=>"BIGINT","V"=>"1","L"=>20),
			"titular_de_seguimiento" => array("N"=>"titular_de_seguimiento","T"=>"BIGINT","V"=>"1","L"=>20),
			"titular_de_contabilidad" => array("N"=>"titular_de_contabilidad","T"=>"BIGINT","V"=>"1","L"=>20),
			"titular_de_inventarios" => array("N"=>"titular_de_inventarios","T"=>"BIGINT","V"=>"1","L"=>20),
			"titular_de_control_interno" => array("N"=>"titular_de_control_interno","T"=>"BIGINT","V"=>"1","L"=>20),
			"titular_de_nominas" => array("N"=>"titular_de_nominas","T"=>"BIGINT","V"=>"1","L"=>20),
			"titular_de_cumplimiento" => array("N"=>"titular_de_cumplimiento","T"=>"BIGINT","V"=>"1","L"=>20),
			"hora_de_inicio_de_operaciones" => array("N"=>"hora_de_inicio_de_operaciones","T"=>"INT","V"=>"8","L"=>4),
			"hora_de_fin_de_operaciones" => array("N"=>"hora_de_fin_de_operaciones","T"=>"INT","V"=>"16","L"=>4),
			"calle" => array("N"=>"calle","T"=>"VARCHAR","V"=>"","L"=>100),
			"numero_exterior" => array("N"=>"numero_exterior","T"=>"VARCHAR","V"=>"","L"=>25),
			"numero_interior" => array("N"=>"numero_interior","T"=>"VARCHAR","V"=>"","L"=>25),
			"colonia" => array("N"=>"colonia","T"=>"VARCHAR","V"=>"","L"=>45),
			"codigo_postal" => array("N"=>"codigo_postal","T"=>"INT","V"=>"1","L"=>11),
			"localidad" => array("N"=>"localidad","T"=>"VARCHAR","V"=>"","L"=>50),
			"municipio" => array("N"=>"municipio","T"=>"VARCHAR","V"=>"","L"=>50),
			"estado" => array("N"=>"estado","T"=>"VARCHAR","V"=>"","L"=>50),
			"telefono" => array("N"=>"telefono","T"=>"VARCHAR","V"=>"","L"=>20),
			"fax" => array("N"=>"fax","T"=>"VARCHAR","V"=>"","L"=>20),
			"clave_de_persona" => array("N"=>"clave_de_persona","T"=>"BIGINT","V"=>"","L"=>20),
			"clave_numerica" => array("N"=>"clave_numerica","T"=>"INT","V"=>"0","L"=>11),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "general_sucursales";}
	function getKey(){ return "codigo_sucursal";}
	function codigo_sucursal($v = false){ if($v !== false){$this->mCampos["codigo_sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["codigo_sucursal"]);}
	function nombre_sucursal($v = false){ if($v !== false){$this->mCampos["nombre_sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_sucursal"]);}
	function gerente_sucursal($v = false){ if($v !== false){$this->mCampos["gerente_sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["gerente_sucursal"]);}
	function caja_local_residente($v = false){ if($v !== false){$this->mCampos["caja_local_residente"]["V"] =  $v; } return new MQLCampo($this->mCampos["caja_local_residente"]);}
	function titular_de_cobranza($v = false){ if($v !== false){$this->mCampos["titular_de_cobranza"]["V"] =  $v; } return new MQLCampo($this->mCampos["titular_de_cobranza"]);}
	function titular_de_seguimiento($v = false){ if($v !== false){$this->mCampos["titular_de_seguimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["titular_de_seguimiento"]);}
	function titular_de_contabilidad($v = false){ if($v !== false){$this->mCampos["titular_de_contabilidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["titular_de_contabilidad"]);}
	function titular_de_inventarios($v = false){ if($v !== false){$this->mCampos["titular_de_inventarios"]["V"] =  $v; } return new MQLCampo($this->mCampos["titular_de_inventarios"]);}
	function titular_de_control_interno($v = false){ if($v !== false){$this->mCampos["titular_de_control_interno"]["V"] =  $v; } return new MQLCampo($this->mCampos["titular_de_control_interno"]);}
	function titular_de_nominas($v = false){ if($v !== false){$this->mCampos["titular_de_nominas"]["V"] =  $v; } return new MQLCampo($this->mCampos["titular_de_nominas"]);}
	function titular_de_cumplimiento($v = false){ if($v !== false){$this->mCampos["titular_de_cumplimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["titular_de_cumplimiento"]);}
	function hora_de_inicio_de_operaciones($v = false){ if($v !== false){$this->mCampos["hora_de_inicio_de_operaciones"]["V"] =  $v; } return new MQLCampo($this->mCampos["hora_de_inicio_de_operaciones"]);}
	function hora_de_fin_de_operaciones($v = false){ if($v !== false){$this->mCampos["hora_de_fin_de_operaciones"]["V"] =  $v; } return new MQLCampo($this->mCampos["hora_de_fin_de_operaciones"]);}
	function calle($v = false){ if($v !== false){$this->mCampos["calle"]["V"] =  $v; } return new MQLCampo($this->mCampos["calle"]);}
	function numero_exterior($v = false){ if($v !== false){$this->mCampos["numero_exterior"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_exterior"]);}
	function numero_interior($v = false){ if($v !== false){$this->mCampos["numero_interior"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_interior"]);}
	function colonia($v = false){ if($v !== false){$this->mCampos["colonia"]["V"] =  $v; } return new MQLCampo($this->mCampos["colonia"]);}
	function codigo_postal($v = false){ if($v !== false){$this->mCampos["codigo_postal"]["V"] =  $v; } return new MQLCampo($this->mCampos["codigo_postal"]);}
	function localidad($v = false){ if($v !== false){$this->mCampos["localidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["localidad"]);}
	function municipio($v = false){ if($v !== false){$this->mCampos["municipio"]["V"] =  $v; } return new MQLCampo($this->mCampos["municipio"]);}
	function estado($v = false){ if($v !== false){$this->mCampos["estado"]["V"] =  $v; } return new MQLCampo($this->mCampos["estado"]);}
	function telefono($v = false){ if($v !== false){$this->mCampos["telefono"]["V"] =  $v; } return new MQLCampo($this->mCampos["telefono"]);}
	function fax($v = false){ if($v !== false){$this->mCampos["fax"]["V"] =  $v; } return new MQLCampo($this->mCampos["fax"]);}
	function clave_de_persona($v = false){ if($v !== false){$this->mCampos["clave_de_persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_persona"]);}
	function clave_numerica($v = false){ if($v !== false){$this->mCampos["clave_numerica"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_numerica"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

//--- creditos_rechazados 		Generado: 13-09-24 12
class cCreditos_rechazados {
	private $mCampos	= array(
			"idcreditos_rechazados" => array("N"=>"idcreditos_rechazados","T"=>"INT","V"=>"","L"=>11),
			"numero_de_credito" => array("N"=>"numero_de_credito","T"=>"BIGINT","V"=>"","L"=>20),
			"fecha_de_rechazo" => array("N"=>"fecha_de_rechazo","T"=>"DATE","V"=>"","L"=>0),
			"razones" => array("N"=>"razones","T"=>"TEXT","V"=>"","L"=>0),
			"notas" => array("N"=>"notas","T"=>"VARCHAR","V"=>"","L"=>200),

	);
	function __construct(){}
	function get(){ return "creditos_rechazados";}
	function getKey(){ return "idcreditos_rechazados";}
	function idcreditos_rechazados($v=false){
		if($v!==false){$this->mCampos["idcreditos_rechazados"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["idcreditos_rechazados"]);
	}
	function numero_de_credito($v=false){
		if($v!==false){$this->mCampos["numero_de_credito"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["numero_de_credito"]);
	}
	function fecha_de_rechazo($v=false){
		if($v!==false){$this->mCampos["fecha_de_rechazo"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["fecha_de_rechazo"]);
	}
	function razones($v=false){
		if($v!==false){$this->mCampos["razones"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["razones"]);
	}
	function notas($v=false){
		if($v!==false){$this->mCampos["notas"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["notas"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function row($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->row($datos);
	}

}

/*	ORM: Tabla:	creditos_tipo_de_pago	-	Generado:	[07/11/2013 15:11]	*/
class cCreditos_tipo_de_pago {
	private $mCampos	= array(
		"idcreditos_tipo_de_pago" => array("N"=>"idcreditos_tipo_de_pago","T"=>"INT","V"=>"","L"=>10),
		"descripcion" => array("N"=>"descripcion","T"=>"VARCHAR","V"=>"","L"=>45),

	);
	function __construct(){}
	function get(){ return "creditos_tipo_de_pago";}
	function getKey(){ return "idcreditos_tipo_de_pago";}
	function idcreditos_tipo_de_pago($v=false){
 		if($v!==false){$this->mCampos["idcreditos_tipo_de_pago"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["idcreditos_tipo_de_pago"]);
	}
	function descripcion($v=false){
 		if($v!==false){$this->mCampos["descripcion"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["descripcion"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}

//--- creditos_nivelesriesgo 		Generado: 13-09-24 16
class cCreditos_nivelesriesgo {
	private $mCampos	= array(
			"idcreditos_nivelesriesgo" => array("N"=>"idcreditos_nivelesriesgo","T"=>"INT","V"=>"0","L"=>4),
			"descripcion_nivelesriesgo" => array("N"=>"descripcion_nivelesriesgo","T"=>"VARCHAR","V"=>"","L"=>65),
			"nivel_riesgo" => array("N"=>"nivel_riesgo","T"=>"INT","V"=>"0","L"=>4)

	);
	function __construct(){}
	function get(){ return "creditos_nivelesriesgo";}
	function getKey(){ return "idcreditos_nivelesriesgo";}
	function idcreditos_nivelesriesgo($v=false){
		if($v!==false){$this->mCampos["idcreditos_nivelesriesgo"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["idcreditos_nivelesriesgo"]);
	}
	function descripcion_nivelesriesgo($v=false){
		if($v!==false){$this->mCampos["descripcion_nivelesriesgo"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["descripcion_nivelesriesgo"]);
	}
	function nivel_riesgo($v=false){
		if($v!==false){$this->mCampos["nivel_riesgo"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["nivel_riesgo"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}



/*	ORM: Tabla:	general_dias_festivos	-	Generado:	[21/7/2014 10:43]	*/
class cGeneral_dias_festivos {
	private $mCampos	= array(
			"fecha_marcado" => array("N"=>"fecha_marcado","T"=>"DATE","V"=>"","L"=>0),
			"descripcion_festividad" => array("N"=>"descripcion_festividad","T"=>"VARCHAR","V"=>"","L"=>100),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "general_dias_festivos";}
	function getKey(){ return "fecha_marcado";}
	function fecha_marcado($v = false){ if($v !== false){$this->mCampos["fecha_marcado"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_marcado"]);}
	function descripcion_festividad($v = false){ if($v !== false){$this->mCampos["descripcion_festividad"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_festividad"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	creditos_solicitud	-	Generado:	[08/7/2014 17:50]	*/
class cCreditos_solicitud {
	private $mCampos	= array(
			"numero_solicitud" => array("N"=>"numero_solicitud","T"=>"BIGINT","V"=>"0","L"=>20),
			"fecha_solicitud" => array("N"=>"fecha_solicitud","T"=>"DATE","V"=>"0000-00-00","L"=>0),
			"fecha_autorizacion" => array("N"=>"fecha_autorizacion","T"=>"DATE","V"=>"0000-00-00","L"=>0),
			"monto_solicitado" => array("N"=>"monto_solicitado","T"=>"FLOAT","V"=>"0.00","L"=>25),
			"monto_autorizado" => array("N"=>"monto_autorizado","T"=>"FLOAT","V"=>"0.00","L"=>25),
			"numero_socio" => array("N"=>"numero_socio","T"=>"BIGINT","V"=>"0","L"=>20),
			"docto_autorizacion" => array("N"=>"docto_autorizacion","T"=>"VARCHAR","V"=>"NO_AUTORIZADO","L"=>100),
			"plazo_en_dias" => array("N"=>"plazo_en_dias","T"=>"INT","V"=>"0","L"=>10),
			"numero_pagos" => array("N"=>"numero_pagos","T"=>"INT","V"=>"0","L"=>10),
			"tasa_interes" => array("N"=>"tasa_interes","T"=>"FLOAT","V"=>"0.00000","L"=>17),
			"periocidad_de_pago" => array("N"=>"periocidad_de_pago","T"=>"INT","V"=>"0","L"=>10),
			"tipo_credito" => array("N"=>"tipo_credito","T"=>"INT","V"=>"99","L"=>4),
			"estatus_actual" => array("N"=>"estatus_actual","T"=>"INT","V"=>"99","L"=>4),
			"tipo_autorizacion" => array("N"=>"tipo_autorizacion","T"=>"INT","V"=>"99","L"=>4),
			"oficial_credito" => array("N"=>"oficial_credito","T"=>"INT","V"=>"99","L"=>4),
			"fecha_vencimiento" => array("N"=>"fecha_vencimiento","T"=>"DATE","V"=>"0000-00-00","L"=>0),
			"pagos_autorizados" => array("N"=>"pagos_autorizados","T"=>"INT","V"=>"0","L"=>10),
			"dias_autorizados" => array("N"=>"dias_autorizados","T"=>"INT","V"=>"0","L"=>10),
			"periodo_solicitudes" => array("N"=>"periodo_solicitudes","T"=>"INT","V"=>"0","L"=>10),
			"destino_credito" => array("N"=>"destino_credito","T"=>"INT","V"=>"99","L"=>10),
			"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"99","L"=>4),
			"nivel_riesgo" => array("N"=>"nivel_riesgo","T"=>"INT","V"=>"99","L"=>4),
			"saldo_actual" => array("N"=>"saldo_actual","T"=>"FLOAT","V"=>"0.00","L"=>25),
			"fecha_ultimo_mvto" => array("N"=>"fecha_ultimo_mvto","T"=>"DATE","V"=>"","L"=>0),
			"tipo_convenio" => array("N"=>"tipo_convenio","T"=>"INT","V"=>"99","L"=>6),
			"interes_diario" => array("N"=>"interes_diario","T"=>"FLOAT","V"=>"0.000000","L"=>25),
			"saldo_vencido" => array("N"=>"saldo_vencido","T"=>"FLOAT","V"=>"0.00","L"=>25),
			"ultimo_periodo_afectado" => array("N"=>"ultimo_periodo_afectado","T"=>"INT","V"=>"0","L"=>4),
			"sdo_int_ant" => array("N"=>"sdo_int_ant","T"=>"FLOAT","V"=>"0.00","L"=>25),
			"periodo_notificacion" => array("N"=>"periodo_notificacion","T"=>"INT","V"=>"0","L"=>4),
			"tasa_moratorio" => array("N"=>"tasa_moratorio","T"=>"FLOAT","V"=>"0.00000","L"=>17),
			"observacion_solicitud" => array("N"=>"observacion_solicitud","T"=>"VARCHAR","V"=>"","L"=>200),
			"cadena_heredada" => array("N"=>"cadena_heredada","T"=>"VARCHAR","V"=>"","L"=>200),
			"tasa_ahorro" => array("N"=>"tasa_ahorro","T"=>"FLOAT","V"=>"0.00000","L"=>17),
			"grupo_asociado" => array("N"=>"grupo_asociado","T"=>"BIGINT","V"=>"999","L"=>20),
			"descripcion_aplicacion" => array("N"=>"descripcion_aplicacion","T"=>"VARCHAR","V"=>"N/A","L"=>150),
			"fecha_ministracion" => array("N"=>"fecha_ministracion","T"=>"DATE","V"=>"2005-12-31","L"=>0),
			"contrato_corriente_relacionado" => array("N"=>"contrato_corriente_relacionado","T"=>"BIGINT","V"=>"2000001","L"=>20),
			"monto_parcialidad" => array("N"=>"monto_parcialidad","T"=>"FLOAT","V"=>"0.00","L"=>25),
			"oficial_seguimiento" => array("N"=>"oficial_seguimiento","T"=>"INT","V"=>"99","L"=>4),
			"fecha_revision" => array("N"=>"fecha_revision","T"=>"DATE","V"=>"2006-01-01","L"=>0),
			"fecha_castigo" => array("N"=>"fecha_castigo","T"=>"DATE","V"=>"2006-12-04","L"=>0),
			"saldo_conciliado" => array("N"=>"saldo_conciliado","T"=>"FLOAT","V"=>"0.00","L"=>25),
			"notas_auditoria" => array("N"=>"notas_auditoria","T"=>"VARCHAR","V"=>"","L"=>200),
			"fecha_conciliada" => array("N"=>"fecha_conciliada","T"=>"DATE","V"=>"2006-12-04","L"=>0),
			"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"MATRIZ","L"=>10),
			"eacp" => array("N"=>"eacp","T"=>"VARCHAR","V"=>"EN_TRAMITE","L"=>15),
			"interes_normal_devengado" => array("N"=>"interes_normal_devengado","T"=>"FLOAT","V"=>"0.00","L"=>25),
			"tipo_de_pago" => array("N"=>"tipo_de_pago","T"=>"INT","V"=>"2","L"=>4),
			"interes_normal_pagado" => array("N"=>"interes_normal_pagado","T"=>"FLOAT","V"=>"0.00","L"=>25),
			"interes_moratorio_devengado" => array("N"=>"interes_moratorio_devengado","T"=>"FLOAT","V"=>"0.00","L"=>25),
			"interes_moratorio_pagado" => array("N"=>"interes_moratorio_pagado","T"=>"FLOAT","V"=>"0.00","L"=>25),
			"fecha_mora" => array("N"=>"fecha_mora","T"=>"DATE","V"=>"2008-08-01","L"=>0),
			"fecha_vencimiento_dinamico" => array("N"=>"fecha_vencimiento_dinamico","T"=>"DATE","V"=>"2008-08-01","L"=>0),
			"tipo_de_calculo_de_interes" => array("N"=>"tipo_de_calculo_de_interes","T"=>"INT","V"=>"2","L"=>2),
			"causa_de_mora" => array("N"=>"causa_de_mora","T"=>"INT","V"=>"99","L"=>2),
			"estatus_de_negociacion" => array("N"=>"estatus_de_negociacion","T"=>"ENUM","V"=>"|ninguno|reestructurado|renovado|","L"=>0),
			"persona_asociada" => array("N"=>"persona_asociada","T"=>"BIGINT","V"=>"0","L"=>20),
			"perfil_de_intereses" => array("N"=>"perfil_de_intereses","T"=>"INT","V"=>"1","L"=>4),
			"fuente_de_fondeo" => array("N"=>"fuente_de_fondeo","T"=>"INT","V"=>"1","L"=>4),
			"fecha_de_primer_pago" => array("N"=>"fecha_de_primer_pago","T"=>"DATE","V"=>"2014-01-01","L"=>0),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_solicitud";}
	function getKey(){ return "numero_solicitud"; }
	function numero_solicitud($v = false){ if($v !== false){$this->mCampos["numero_solicitud"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_solicitud"]);}
	function fecha_solicitud($v = false){ if($v !== false){$this->mCampos["fecha_solicitud"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_solicitud"]);}
	function fecha_autorizacion($v = false){ if($v !== false){$this->mCampos["fecha_autorizacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_autorizacion"]);}
	function monto_solicitado($v = false){ if($v !== false){$this->mCampos["monto_solicitado"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_solicitado"]);}
	function monto_autorizado($v = false){ if($v !== false){$this->mCampos["monto_autorizado"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_autorizado"]);}
	function numero_socio($v = false){ if($v !== false){$this->mCampos["numero_socio"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_socio"]);}
	function docto_autorizacion($v = false){ if($v !== false){$this->mCampos["docto_autorizacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["docto_autorizacion"]);}
	function plazo_en_dias($v = false){ if($v !== false){$this->mCampos["plazo_en_dias"]["V"] =  $v; } return new MQLCampo($this->mCampos["plazo_en_dias"]);}
	function numero_pagos($v = false){ if($v !== false){$this->mCampos["numero_pagos"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_pagos"]);}
	function tasa_interes($v = false){ if($v !== false){$this->mCampos["tasa_interes"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_interes"]);}
	function periocidad_de_pago($v = false){ if($v !== false){$this->mCampos["periocidad_de_pago"]["V"] =  $v; } return new MQLCampo($this->mCampos["periocidad_de_pago"]);}
	function tipo_credito($v = false){ if($v !== false){$this->mCampos["tipo_credito"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_credito"]);}
	function estatus_actual($v = false){ if($v !== false){$this->mCampos["estatus_actual"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus_actual"]);}
	function tipo_autorizacion($v = false){ if($v !== false){$this->mCampos["tipo_autorizacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_autorizacion"]);}
	function oficial_credito($v = false){ if($v !== false){$this->mCampos["oficial_credito"]["V"] =  $v; } return new MQLCampo($this->mCampos["oficial_credito"]);}
	function fecha_vencimiento($v = false){ if($v !== false){$this->mCampos["fecha_vencimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_vencimiento"]);}
	function pagos_autorizados($v = false){ if($v !== false){$this->mCampos["pagos_autorizados"]["V"] =  $v; } return new MQLCampo($this->mCampos["pagos_autorizados"]);}
	function dias_autorizados($v = false){ if($v !== false){$this->mCampos["dias_autorizados"]["V"] =  $v; } return new MQLCampo($this->mCampos["dias_autorizados"]);}
	function periodo_solicitudes($v = false){ if($v !== false){$this->mCampos["periodo_solicitudes"]["V"] =  $v; } return new MQLCampo($this->mCampos["periodo_solicitudes"]);}
	function destino_credito($v = false){ if($v !== false){$this->mCampos["destino_credito"]["V"] =  $v; } return new MQLCampo($this->mCampos["destino_credito"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function nivel_riesgo($v = false){ if($v !== false){$this->mCampos["nivel_riesgo"]["V"] =  $v; } return new MQLCampo($this->mCampos["nivel_riesgo"]);}
	function saldo_actual($v = false){ if($v !== false){$this->mCampos["saldo_actual"]["V"] =  $v; } return new MQLCampo($this->mCampos["saldo_actual"]);}
	function fecha_ultimo_mvto($v = false){ if($v !== false){$this->mCampos["fecha_ultimo_mvto"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_ultimo_mvto"]);}
	function tipo_convenio($v = false){ if($v !== false){$this->mCampos["tipo_convenio"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_convenio"]);}
	function interes_diario($v = false){ if($v !== false){$this->mCampos["interes_diario"]["V"] =  $v; } return new MQLCampo($this->mCampos["interes_diario"]);}
	function saldo_vencido($v = false){ if($v !== false){$this->mCampos["saldo_vencido"]["V"] =  $v; } return new MQLCampo($this->mCampos["saldo_vencido"]);}
	function ultimo_periodo_afectado($v = false){ if($v !== false){$this->mCampos["ultimo_periodo_afectado"]["V"] =  $v; } return new MQLCampo($this->mCampos["ultimo_periodo_afectado"]);}
	function sdo_int_ant($v = false){ if($v !== false){$this->mCampos["sdo_int_ant"]["V"] =  $v; } return new MQLCampo($this->mCampos["sdo_int_ant"]);}
	function periodo_notificacion($v = false){ if($v !== false){$this->mCampos["periodo_notificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["periodo_notificacion"]);}
	function tasa_moratorio($v = false){ if($v !== false){$this->mCampos["tasa_moratorio"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_moratorio"]);}
	function observacion_solicitud($v = false){ if($v !== false){$this->mCampos["observacion_solicitud"]["V"] =  $v; } return new MQLCampo($this->mCampos["observacion_solicitud"]);}
	function cadena_heredada($v = false){ if($v !== false){$this->mCampos["cadena_heredada"]["V"] =  $v; } return new MQLCampo($this->mCampos["cadena_heredada"]);}
	function tasa_ahorro($v = false){ if($v !== false){$this->mCampos["tasa_ahorro"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_ahorro"]);}
	function grupo_asociado($v = false){ if($v !== false){$this->mCampos["grupo_asociado"]["V"] =  $v; } return new MQLCampo($this->mCampos["grupo_asociado"]);}
	function descripcion_aplicacion($v = false){ if($v !== false){$this->mCampos["descripcion_aplicacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_aplicacion"]);}
	function fecha_ministracion($v = false){ if($v !== false){$this->mCampos["fecha_ministracion"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_ministracion"]);}
	function contrato_corriente_relacionado($v = false){ if($v !== false){$this->mCampos["contrato_corriente_relacionado"]["V"] =  $v; } return new MQLCampo($this->mCampos["contrato_corriente_relacionado"]);}
	function monto_parcialidad($v = false){ if($v !== false){$this->mCampos["monto_parcialidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_parcialidad"]);}
	function oficial_seguimiento($v = false){ if($v !== false){$this->mCampos["oficial_seguimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["oficial_seguimiento"]);}
	function fecha_revision($v = false){ if($v !== false){$this->mCampos["fecha_revision"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_revision"]);}
	function fecha_castigo($v = false){ if($v !== false){$this->mCampos["fecha_castigo"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_castigo"]);}
	function saldo_conciliado($v = false){ if($v !== false){$this->mCampos["saldo_conciliado"]["V"] =  $v; } return new MQLCampo($this->mCampos["saldo_conciliado"]);}
	function notas_auditoria($v = false){ if($v !== false){$this->mCampos["notas_auditoria"]["V"] =  $v; } return new MQLCampo($this->mCampos["notas_auditoria"]);}
	function fecha_conciliada($v = false){ if($v !== false){$this->mCampos["fecha_conciliada"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_conciliada"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function eacp($v = false){ if($v !== false){$this->mCampos["eacp"]["V"] =  $v; } return new MQLCampo($this->mCampos["eacp"]);}
	function interes_normal_devengado($v = false){ if($v !== false){$this->mCampos["interes_normal_devengado"]["V"] =  $v; } return new MQLCampo($this->mCampos["interes_normal_devengado"]);}
	function tipo_de_pago($v = false){ if($v !== false){$this->mCampos["tipo_de_pago"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_pago"]);}
	function interes_normal_pagado($v = false){ if($v !== false){$this->mCampos["interes_normal_pagado"]["V"] =  $v; } return new MQLCampo($this->mCampos["interes_normal_pagado"]);}
	function interes_moratorio_devengado($v = false){ if($v !== false){$this->mCampos["interes_moratorio_devengado"]["V"] =  $v; } return new MQLCampo($this->mCampos["interes_moratorio_devengado"]);}
	function interes_moratorio_pagado($v = false){ if($v !== false){$this->mCampos["interes_moratorio_pagado"]["V"] =  $v; } return new MQLCampo($this->mCampos["interes_moratorio_pagado"]);}
	function fecha_mora($v = false){ if($v !== false){$this->mCampos["fecha_mora"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_mora"]);}
	function fecha_vencimiento_dinamico($v = false){ if($v !== false){$this->mCampos["fecha_vencimiento_dinamico"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_vencimiento_dinamico"]);}
	function tipo_de_calculo_de_interes($v = false){ if($v !== false){$this->mCampos["tipo_de_calculo_de_interes"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_calculo_de_interes"]);}
	function causa_de_mora($v = false){ if($v !== false){$this->mCampos["causa_de_mora"]["V"] =  $v; } return new MQLCampo($this->mCampos["causa_de_mora"]);}
	function estatus_de_negociacion($v = false){ if($v !== false){$this->mCampos["estatus_de_negociacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus_de_negociacion"]);}
	function persona_asociada($v = false){ if($v !== false){$this->mCampos["persona_asociada"]["V"] =  $v; } return new MQLCampo($this->mCampos["persona_asociada"]);}
	function perfil_de_intereses($v = false){ if($v !== false){$this->mCampos["perfil_de_intereses"]["V"] =  $v; } return new MQLCampo($this->mCampos["perfil_de_intereses"]);}
	function fuente_de_fondeo($v = false){ if($v !== false){$this->mCampos["fuente_de_fondeo"]["V"] =  $v; } return new MQLCampo($this->mCampos["fuente_de_fondeo"]);}
	function fecha_de_primer_pago($v = false){ if($v !== false){$this->mCampos["fecha_de_primer_pago"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_primer_pago"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	creditos_otros_datos	-	Generado:	[12/9/2014 10:34]	*/
class cCreditos_otros_datos {
	private $mCampos	= array(
			"idcreditos_otros_datos" => array("N"=>"idcreditos_otros_datos","T"=>"INT","V"=>"","L"=>11),
			"clave_de_credito" => array("N"=>"clave_de_credito","T"=>"BIGINT","V"=>"0","L"=>20),
			"fecha_de_expiracion" => array("N"=>"fecha_de_expiracion","T"=>"DATE","V"=>"","L"=>0),
			"clasificacion_de_parametro" => array("N"=>"clasificacion_de_parametro","T"=>"VARCHAR","V"=>"","L"=>20),
			"clave_de_parametro" => array("N"=>"clave_de_parametro","T"=>"VARCHAR","V"=>"","L"=>20),
			"valor_de_parametro" => array("N"=>"valor_de_parametro","T"=>"VARCHAR","V"=>"","L"=>100),
			"descripcion_de_parametro" => array("N"=>"descripcion_de_parametro","T"=>"VARCHAR","V"=>"","L"=>100),
			"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"matriz","L"=>20)

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_otros_datos";}
	function getKey(){ return "idcreditos_otros_datos";}
	function idcreditos_otros_datos($v = false){ if($v !== false){$this->mCampos["idcreditos_otros_datos"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_otros_datos"]);}
	function clave_de_credito($v = false){ if($v !== false){$this->mCampos["clave_de_credito"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_credito"]);}
	function fecha_de_expiracion($v = false){ if($v !== false){$this->mCampos["fecha_de_expiracion"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_expiracion"]);}
	function clasificacion_de_parametro($v = false){ if($v !== false){$this->mCampos["clasificacion_de_parametro"]["V"] =  $v; } return new MQLCampo($this->mCampos["clasificacion_de_parametro"]);}
	function clave_de_parametro($v = false){ if($v !== false){$this->mCampos["clave_de_parametro"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_parametro"]);}
	function valor_de_parametro($v = false){ if($v !== false){$this->mCampos["valor_de_parametro"]["V"] =  $v; } return new MQLCampo($this->mCampos["valor_de_parametro"]);}
	function descripcion_de_parametro($v = false){ if($v !== false){$this->mCampos["descripcion_de_parametro"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_de_parametro"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
}

//--- general_reports 		Generado: 13-10-03 10
class cGeneral_reports {
	private $mCampos	= array(
		"idgeneral_reports" => array("N"=>"idgeneral_reports","T"=>"VARCHAR","V"=>"0","L"=>100),
		"descripcion_reports" => array("N"=>"descripcion_reports","T"=>"VARCHAR","V"=>"","L"=>200),
		"aplica" => array("N"=>"aplica","T"=>"VARCHAR","V"=>"","L"=>35),
		"idreport" => array("N"=>"idreport","T"=>"INT","V"=>"","L"=>10),
		"explicacion" => array("N"=>"explicacion","T"=>"TEXT","V"=>"","L"=>0),

	);
	function __construct(){}
	function get(){ return "general_reports";}
	function getKey(){ return "idgeneral_reports";}
	function idgeneral_reports($v=false){
 		if($v!==false){$this->mCampos["idgeneral_reports"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["idgeneral_reports"]);
	}
	function descripcion_reports($v=false){
 		if($v!==false){$this->mCampos["descripcion_reports"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["descripcion_reports"]);
	}
	function aplica($v=false){
 		if($v!==false){$this->mCampos["aplica"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["aplica"]);
	}
	function idreport($v=false){
 		if($v!==false){$this->mCampos["idreport"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["idreport"]);
	}
	function explicacion($v=false){
 		if($v!==false){$this->mCampos["explicacion"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["explicacion"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}

//--- creditos_periocidadpagos 		Generado: 13-10-11 12
class cCreditos_periocidadpagos {
	private $mCampos	= array(
		"idcreditos_periocidadpagos" => array("N"=>"idcreditos_periocidadpagos","T"=>"INT","V"=>"0","L"=>4),
		"descripcion_periocidadpagos" => array("N"=>"descripcion_periocidadpagos","T"=>"VARCHAR","V"=>"","L"=>45),
		"periocidad_de_pago" => array("N"=>"periocidad_de_pago","T"=>"INT","V"=>"0","L"=>4),
		"titulo_en_informe" => array("N"=>"titulo_en_informe","T"=>"ENUM","V"=>"|PAGO","L"=>0),
		"tolerancia_en_dias_para_vencimiento" => array("N"=>"tolerancia_en_dias_para_vencimiento","T"=>"INT","V"=>"89","L"=>4),

	);
	function __construct(){}
	function get(){ return "creditos_periocidadpagos";}
	function getKey(){ return "idcreditos_periocidadpagos";}
	function idcreditos_periocidadpagos($v=false){
 		if($v!==false){$this->mCampos["idcreditos_periocidadpagos"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["idcreditos_periocidadpagos"]);
	}
	function descripcion_periocidadpagos($v=false){
 		if($v!==false){$this->mCampos["descripcion_periocidadpagos"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["descripcion_periocidadpagos"]);
	}
	function periocidad_de_pago($v=false){
 		if($v!==false){$this->mCampos["periocidad_de_pago"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["periocidad_de_pago"]);
	}
	function titulo_en_informe($v=false){
 		if($v!==false){$this->mCampos["titulo_en_informe"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["titulo_en_informe"]);
	}
	function tolerancia_en_dias_para_vencimiento($v=false){
 		if($v!==false){$this->mCampos["tolerancia_en_dias_para_vencimiento"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["tolerancia_en_dias_para_vencimiento"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}

//--- creditos_modalidades 		Generado: 13-10-21 13
class cCreditos_modalidades {
	private $mCampos	= array(
		"idcreditos_modalidades" => array("N"=>"idcreditos_modalidades","T"=>"INT","V"=>"0","L"=>4),
		"descripcion_modalidades" => array("N"=>"descripcion_modalidades","T"=>"VARCHAR","V"=>"","L"=>65),
		"tipo_credito" => array("N"=>"tipo_credito","T"=>"VARCHAR","V"=>"0","L"=>4),
		"tasa_de_iva" => array("N"=>"tasa_de_iva","T"=>"FLOAT","V"=>"0.1600","L"=>25)
	);
	function __construct(){}
	function get(){ return "creditos_modalidades";}
	function getKey(){ return "idcreditos_modalidades";}
	function idcreditos_modalidades($v=false){
 		if($v!==false){$this->mCampos["idcreditos_modalidades"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["idcreditos_modalidades"]);
	}
	function descripcion_modalidades($v=false){
 		if($v!==false){$this->mCampos["descripcion_modalidades"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["descripcion_modalidades"]);
	}
	function tipo_credito($v=false){
 		if($v!==false){$this->mCampos["tipo_credito"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["tipo_credito"]);
	}
	function tasa_de_iva($v=false){
 		if($v!==false){$this->mCampos["tasa_de_iva"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["tasa_de_iva"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}

//--- creditos_destinos 		Generado: 13-10-22 10
class cCreditos_destinos {
	private $mCampos	= array(
		"idcreditos_destinos" => array("N"=>"idcreditos_destinos","T"=>"INT","V"=>"","L"=>4),
		"descripcion_destinos" => array("N"=>"descripcion_destinos","T"=>"VARCHAR","V"=>"","L"=>45),
		"destino_credito" => array("N"=>"destino_credito","T"=>"INT","V"=>"0","L"=>4),
		"capital_vencido_renovado" => array("N"=>"capital_vencido_renovado","T"=>"VARCHAR","V"=>"0","L"=>20),
		"capital_vencido_reestructurado" => array("N"=>"capital_vencido_reestructurado","T"=>"VARCHAR","V"=>"0","L"=>20),
		"capital_vencido_normal" => array("N"=>"capital_vencido_normal","T"=>"VARCHAR","V"=>"0","L"=>20),
		"capital_vigente_renovado" => array("N"=>"capital_vigente_renovado","T"=>"VARCHAR","V"=>"0","L"=>20),
		"capital_vigente_reestructurado" => array("N"=>"capital_vigente_reestructurado","T"=>"VARCHAR","V"=>"0","L"=>20),
		"capital_vigente_normal" => array("N"=>"capital_vigente_normal","T"=>"VARCHAR","V"=>"0","L"=>20),
		"interes_cobrado" => array("N"=>"interes_cobrado","T"=>"VARCHAR","V"=>"0","L"=>20),
		"moratorio_cobrado" => array("N"=>"moratorio_cobrado","T"=>"VARCHAR","V"=>"0","L"=>20),
		"interes_vencido_renovado" => array("N"=>"interes_vencido_renovado","T"=>"VARCHAR","V"=>"0","L"=>20),
		"interes_vencido_reestructurado" => array("N"=>"interes_vencido_reestructurado","T"=>"VARCHAR","V"=>"0","L"=>20),
		"interes_vencido_normal" => array("N"=>"interes_vencido_normal","T"=>"VARCHAR","V"=>"0","L"=>20),
		"interes_vigente_renovado" => array("N"=>"interes_vigente_renovado","T"=>"VARCHAR","V"=>"0","L"=>20),
		"interes_vigente_reestructurado" => array("N"=>"interes_vigente_reestructurado","T"=>"VARCHAR","V"=>"0","L"=>20),
		"interes_vigente_normal" => array("N"=>"interes_vigente_normal","T"=>"VARCHAR","V"=>"0","L"=>20),
		"tasa_de_iva" => array("N"=>"tasa_de_iva","T"=>"FLOAT","V"=>"0.1600","L"=>25),

	);
	function __construct(){}
	function get(){ return "creditos_destinos";}
	function getKey(){ return "idcreditos_destinos";}
	function idcreditos_destinos($v=false){
 		if($v!==false){$this->mCampos["idcreditos_destinos"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["idcreditos_destinos"]);
	}
	function descripcion_destinos($v=false){
 		if($v!==false){$this->mCampos["descripcion_destinos"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["descripcion_destinos"]);
	}
	function destino_credito($v=false){
 		if($v!==false){$this->mCampos["destino_credito"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["destino_credito"]);
	}
	function capital_vencido_renovado($v=false){
 		if($v!==false){$this->mCampos["capital_vencido_renovado"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["capital_vencido_renovado"]);
	}
	function capital_vencido_reestructurado($v=false){
 		if($v!==false){$this->mCampos["capital_vencido_reestructurado"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["capital_vencido_reestructurado"]);
	}
	function capital_vencido_normal($v=false){
 		if($v!==false){$this->mCampos["capital_vencido_normal"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["capital_vencido_normal"]);
	}
	function capital_vigente_renovado($v=false){
 		if($v!==false){$this->mCampos["capital_vigente_renovado"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["capital_vigente_renovado"]);
	}
	function capital_vigente_reestructurado($v=false){
 		if($v!==false){$this->mCampos["capital_vigente_reestructurado"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["capital_vigente_reestructurado"]);
	}
	function capital_vigente_normal($v=false){
 		if($v!==false){$this->mCampos["capital_vigente_normal"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["capital_vigente_normal"]);
	}
	function interes_cobrado($v=false){
 		if($v!==false){$this->mCampos["interes_cobrado"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["interes_cobrado"]);
	}
	function moratorio_cobrado($v=false){
 		if($v!==false){$this->mCampos["moratorio_cobrado"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["moratorio_cobrado"]);
	}
	function interes_vencido_renovado($v=false){
 		if($v!==false){$this->mCampos["interes_vencido_renovado"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["interes_vencido_renovado"]);
	}
	function interes_vencido_reestructurado($v=false){
 		if($v!==false){$this->mCampos["interes_vencido_reestructurado"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["interes_vencido_reestructurado"]);
	}
	function interes_vencido_normal($v=false){
 		if($v!==false){$this->mCampos["interes_vencido_normal"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["interes_vencido_normal"]);
	}
	function interes_vigente_renovado($v=false){
 		if($v!==false){$this->mCampos["interes_vigente_renovado"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["interes_vigente_renovado"]);
	}
	function interes_vigente_reestructurado($v=false){
 		if($v!==false){$this->mCampos["interes_vigente_reestructurado"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["interes_vigente_reestructurado"]);
	}
	function interes_vigente_normal($v=false){
 		if($v!==false){$this->mCampos["interes_vigente_normal"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["interes_vigente_normal"]);
	}
	function tasa_de_iva($v=false){
 		if($v!==false){$this->mCampos["tasa_de_iva"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["tasa_de_iva"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}

/*	ORM: Tabla:	creditos_tipoconvenio	-	Generado:	[18/11/2014 17:52]	*/
class cCreditos_tipoconvenio {
	private $mCampos	= array(
			"idcreditos_tipoconvenio" => array("N"=>"idcreditos_tipoconvenio","T"=>"INT","V"=>"0","L"=>4),
			"descripcion_tipoconvenio" => array("N"=>"descripcion_tipoconvenio","T"=>"VARCHAR","V"=>"","L"=>100),
			"tasa_ahorro" => array("N"=>"tasa_ahorro","T"=>"FLOAT","V"=>"0.00000","L"=>17),
			"tipo_convenio" => array("N"=>"tipo_convenio","T"=>"INT","V"=>"0","L"=>4),
			"razon_garantia" => array("N"=>"razon_garantia","T"=>"FLOAT","V"=>"0.00000","L"=>17),
			"creditos_mayores_a" => array("N"=>"creditos_mayores_a","T"=>"FLOAT","V"=>"0.000","L"=>29),
			"porciento_garantia_liquida" => array("N"=>"porciento_garantia_liquida","T"=>"FLOAT","V"=>"0.00000","L"=>17),
			"monto_fondo_obligatorio" => array("N"=>"monto_fondo_obligatorio","T"=>"FLOAT","V"=>"0.0000","L"=>25),
			"porcentaje_otro_credito" => array("N"=>"porcentaje_otro_credito","T"=>"FLOAT","V"=>"0.00000","L"=>17),
			"aplica_gastos_notariales" => array("N"=>"aplica_gastos_notariales","T"=>"INT","V"=>"0","L"=>2),
			"numero_creditos_maximo" => array("N"=>"numero_creditos_maximo","T"=>"INT","V"=>"1","L"=>2),
			"dias_maximo" => array("N"=>"dias_maximo","T"=>"INT","V"=>"0","L"=>5),
			"pagos_maximo" => array("N"=>"pagos_maximo","T"=>"INT","V"=>"0","L"=>5),
			"tipo_autorizacion" => array("N"=>"tipo_autorizacion","T"=>"INT","V"=>"1","L"=>4),
			"nivel_riesgo" => array("N"=>"nivel_riesgo","T"=>"INT","V"=>"1","L"=>4),
			"porcentaje_ica" => array("N"=>"porcentaje_ica","T"=>"FLOAT","V"=>"0.00000","L"=>17),
			"estatus_predeterminado" => array("N"=>"estatus_predeterminado","T"=>"INT","V"=>"98","L"=>4),
			"leyenda_docto_autorizacion" => array("N"=>"leyenda_docto_autorizacion","T"=>"VARCHAR","V"=>"","L"=>50),
			"interes_normal" => array("N"=>"interes_normal","T"=>"FLOAT","V"=>"0.00000","L"=>17),
			"interes_moratorio" => array("N"=>"interes_moratorio","T"=>"FLOAT","V"=>"0.00000","L"=>17),
			"tolerancia_dias_no_pago" => array("N"=>"tolerancia_dias_no_pago","T"=>"INT","V"=>"5","L"=>4),
			"maximo_otorgable" => array("N"=>"maximo_otorgable","T"=>"FLOAT","V"=>"0.00","L"=>25),
			"tolerancia_dias_primer_abono" => array("N"=>"tolerancia_dias_primer_abono","T"=>"INT","V"=>"0","L"=>4),
			"numero_avales" => array("N"=>"numero_avales","T"=>"INT","V"=>"2","L"=>4),
			"nivel_autorizacion_oficial" => array("N"=>"nivel_autorizacion_oficial","T"=>"INT","V"=>"6","L"=>4),
			"code_valoracion_javascript" => array("N"=>"code_valoracion_javascript","T"=>"MEDIUMTEXT","V"=>"","L"=>0),
			"minimo_otorgable" => array("N"=>"minimo_otorgable","T"=>"FLOAT","V"=>"1000.00","L"=>25),
			"descripcion_completa" => array("N"=>"descripcion_completa","T"=>"MEDIUMTEXT","V"=>"","L"=>0),
			"oficial_seguimiento" => array("N"=>"oficial_seguimiento","T"=>"INT","V"=>"99","L"=>4),
			"valoracion_php" => array("N"=>"valoracion_php","T"=>"MEDIUMTEXT","V"=>"","L"=>0),
			"tipo_de_credito" => array("N"=>"tipo_de_credito","T"=>"INT","V"=>"1","L"=>4),
			"php_monto_maximo" => array("N"=>"php_monto_maximo","T"=>"MEDIUMTEXT","V"=>"","L"=>0),
			"tipo_de_convenio" => array("N"=>"tipo_de_convenio","T"=>"ENUM","V"=>"|1|3|","L"=>0),
			"tipo_de_garantia" => array("N"=>"tipo_de_garantia","T"=>"ENUM","V"=>"|todas|cuenta_inversion|aportacion|","L"=>0),
			"estatus" => array("N"=>"estatus","T"=>"ENUM","V"=>"|baja|activo|","L"=>0),
			"tasa_iva" => array("N"=>"tasa_iva","T"=>"FLOAT","V"=>"0.00000","L"=>17),
			"contable_cartera_vigente" => array("N"=>"contable_cartera_vigente","T"=>"VARCHAR","V"=>"0","L"=>20),
			"contable_cartera_vencida" => array("N"=>"contable_cartera_vencida","T"=>"VARCHAR","V"=>"0","L"=>20),
			"contable_intereses_devengados" => array("N"=>"contable_intereses_devengados","T"=>"VARCHAR","V"=>"0","L"=>20),
			"contable_intereses_anticipados" => array("N"=>"contable_intereses_anticipados","T"=>"VARCHAR","V"=>"0","L"=>20),
			"contable_intereses_cobrados" => array("N"=>"contable_intereses_cobrados","T"=>"VARCHAR","V"=>"0","L"=>20),
			"contable_intereses_moratorios" => array("N"=>"contable_intereses_moratorios","T"=>"VARCHAR","V"=>"0","L"=>20),
			"iva_incluido" => array("N"=>"iva_incluido","T"=>"ENUM","V"=>"|1|0|","L"=>0),
			"comision_por_apertura" => array("N"=>"comision_por_apertura","T"=>"FLOAT","V"=>"0.00000","L"=>17),
			"codigo_de_contrato" => array("N"=>"codigo_de_contrato","T"=>"INT","V"=>"0","L"=>4),
			"contable_cartera_castigada" => array("N"=>"contable_cartera_castigada","T"=>"VARCHAR","V"=>"0","L"=>20),
			"path_del_contrato" => array("N"=>"path_del_contrato","T"=>"VARCHAR","V"=>"","L"=>100),
			"tipo_de_integracion" => array("N"=>"tipo_de_integracion","T"=>"TINYINT","V"=>"1","L"=>2),
			"contable_intereses_vencidos" => array("N"=>"contable_intereses_vencidos","T"=>"VARCHAR","V"=>"0","L"=>20),
			"base_de_calculo_de_interes" => array("N"=>"base_de_calculo_de_interes","T"=>"INT","V"=>"2","L"=>2),
			"capital_vencido_renovado" => array("N"=>"capital_vencido_renovado","T"=>"VARCHAR","V"=>"0","L"=>20),
			"capital_vencido_reestructurado" => array("N"=>"capital_vencido_reestructurado","T"=>"VARCHAR","V"=>"0","L"=>20),
			"capital_vencido_normal" => array("N"=>"capital_vencido_normal","T"=>"VARCHAR","V"=>"0","L"=>20),
			"capital_vigente_renovado" => array("N"=>"capital_vigente_renovado","T"=>"VARCHAR","V"=>"0","L"=>20),
			"capital_vigente_reestructurado" => array("N"=>"capital_vigente_reestructurado","T"=>"VARCHAR","V"=>"0","L"=>20),
			"capital_vigente_normal" => array("N"=>"capital_vigente_normal","T"=>"VARCHAR","V"=>"0","L"=>20),
			"interes_cobrado" => array("N"=>"interes_cobrado","T"=>"VARCHAR","V"=>"0","L"=>20),
			"moratorio_cobrado" => array("N"=>"moratorio_cobrado","T"=>"VARCHAR","V"=>"0","L"=>20),
			"interes_vencido_renovado" => array("N"=>"interes_vencido_renovado","T"=>"VARCHAR","V"=>"0","L"=>20),
			"interes_vencido_reestructurado" => array("N"=>"interes_vencido_reestructurado","T"=>"VARCHAR","V"=>"0","L"=>20),
			"interes_vencido_normal" => array("N"=>"interes_vencido_normal","T"=>"VARCHAR","V"=>"0","L"=>20),
			"interes_vigente_renovado" => array("N"=>"interes_vigente_renovado","T"=>"VARCHAR","V"=>"0","L"=>20),
			"interes_vigente_reestructurado" => array("N"=>"interes_vigente_reestructurado","T"=>"VARCHAR","V"=>"0","L"=>20),
			"interes_vigente_normal" => array("N"=>"interes_vigente_normal","T"=>"VARCHAR","V"=>"0","L"=>20),
			"tipo_de_interes" => array("N"=>"tipo_de_interes","T"=>"INT","V"=>"0","L"=>11),
			"aplica_mora_por_cobranza" => array("N"=>"aplica_mora_por_cobranza","T"=>"INT","V"=>"0","L"=>4),
			"pre_modificador_de_interes" => array("N"=>"pre_modificador_de_interes","T"=>"MEDIUMTEXT","V"=>"","L"=>0),
			"pos_modificador_de_interes" => array("N"=>"pos_modificador_de_interes","T"=>"MEDIUMTEXT","V"=>"","L"=>0),
			"pre_modificador_de_ministracion" => array("N"=>"pre_modificador_de_ministracion","T"=>"MEDIUMTEXT","V"=>"","L"=>0),
			"pre_modificador_de_autorizacion" => array("N"=>"pre_modificador_de_autorizacion","T"=>"MEDIUMTEXT","V"=>"","L"=>0),
			"pre_modificador_de_vencimiento" => array("N"=>"pre_modificador_de_vencimiento","T"=>"MEDIUMTEXT","V"=>"","L"=>0),
			"pre_modificador_de_solicitud" => array("N"=>"pre_modificador_de_solicitud","T"=>"MEDIUMTEXT","V"=>"","L"=>0),
			"clave_de_tipo_de_producto" => array("N"=>"clave_de_tipo_de_producto","T"=>"VARCHAR","V"=>"UK","L"=>10),
			"perfil_de_interes" => array("N"=>"perfil_de_interes","T"=>"INT","V"=>"99","L"=>11),
			"fuente_de_fondeo_predeterminado" => array("N"=>"fuente_de_fondeo_predeterminado","T"=>"INT","V"=>"1","L"=>11),
			"tipo_de_periocidad_preferente" => array("N"=>"tipo_de_periocidad_preferente","T"=>"INT","V"=>"7","L"=>4),
			"numero_de_pagos_preferente" => array("N"=>"numero_de_pagos_preferente","T"=>"INT","V"=>"0","L"=>11),
			"tipo_en_sistema" => array("N"=>"tipo_en_sistema","T"=>"INT","V"=>"1","L"=>4),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_tipoconvenio";}
	function getKey(){ return "idcreditos_tipoconvenio";}
	function idcreditos_tipoconvenio($v = false){ if($v !== false){$this->mCampos["idcreditos_tipoconvenio"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_tipoconvenio"]);}
	function descripcion_tipoconvenio($v = false){ if($v !== false){$this->mCampos["descripcion_tipoconvenio"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_tipoconvenio"]);}
	function tasa_ahorro($v = false){ if($v !== false){$this->mCampos["tasa_ahorro"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_ahorro"]);}
	function tipo_convenio($v = false){ if($v !== false){$this->mCampos["tipo_convenio"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_convenio"]);}
	function razon_garantia($v = false){ if($v !== false){$this->mCampos["razon_garantia"]["V"] =  $v; } return new MQLCampo($this->mCampos["razon_garantia"]);}
	function creditos_mayores_a($v = false){ if($v !== false){$this->mCampos["creditos_mayores_a"]["V"] =  $v; } return new MQLCampo($this->mCampos["creditos_mayores_a"]);}
	function porciento_garantia_liquida($v = false){ if($v !== false){$this->mCampos["porciento_garantia_liquida"]["V"] =  $v; } return new MQLCampo($this->mCampos["porciento_garantia_liquida"]);}
	function monto_fondo_obligatorio($v = false){ if($v !== false){$this->mCampos["monto_fondo_obligatorio"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_fondo_obligatorio"]);}
	function porcentaje_otro_credito($v = false){ if($v !== false){$this->mCampos["porcentaje_otro_credito"]["V"] =  $v; } return new MQLCampo($this->mCampos["porcentaje_otro_credito"]);}
	function aplica_gastos_notariales($v = false){ if($v !== false){$this->mCampos["aplica_gastos_notariales"]["V"] =  $v; } return new MQLCampo($this->mCampos["aplica_gastos_notariales"]);}
	function numero_creditos_maximo($v = false){ if($v !== false){$this->mCampos["numero_creditos_maximo"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_creditos_maximo"]);}
	function dias_maximo($v = false){ if($v !== false){$this->mCampos["dias_maximo"]["V"] =  $v; } return new MQLCampo($this->mCampos["dias_maximo"]);}
	function pagos_maximo($v = false){ if($v !== false){$this->mCampos["pagos_maximo"]["V"] =  $v; } return new MQLCampo($this->mCampos["pagos_maximo"]);}
	function tipo_autorizacion($v = false){ if($v !== false){$this->mCampos["tipo_autorizacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_autorizacion"]);}
	function nivel_riesgo($v = false){ if($v !== false){$this->mCampos["nivel_riesgo"]["V"] =  $v; } return new MQLCampo($this->mCampos["nivel_riesgo"]);}
	function porcentaje_ica($v = false){ if($v !== false){$this->mCampos["porcentaje_ica"]["V"] =  $v; } return new MQLCampo($this->mCampos["porcentaje_ica"]);}
	function estatus_predeterminado($v = false){ if($v !== false){$this->mCampos["estatus_predeterminado"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus_predeterminado"]);}
	function leyenda_docto_autorizacion($v = false){ if($v !== false){$this->mCampos["leyenda_docto_autorizacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["leyenda_docto_autorizacion"]);}
	function interes_normal($v = false){ if($v !== false){$this->mCampos["interes_normal"]["V"] =  $v; } return new MQLCampo($this->mCampos["interes_normal"]);}
	function interes_moratorio($v = false){ if($v !== false){$this->mCampos["interes_moratorio"]["V"] =  $v; } return new MQLCampo($this->mCampos["interes_moratorio"]);}
	function tolerancia_dias_no_pago($v = false){ if($v !== false){$this->mCampos["tolerancia_dias_no_pago"]["V"] =  $v; } return new MQLCampo($this->mCampos["tolerancia_dias_no_pago"]);}
	function maximo_otorgable($v = false){ if($v !== false){$this->mCampos["maximo_otorgable"]["V"] =  $v; } return new MQLCampo($this->mCampos["maximo_otorgable"]);}
	function tolerancia_dias_primer_abono($v = false){ if($v !== false){$this->mCampos["tolerancia_dias_primer_abono"]["V"] =  $v; } return new MQLCampo($this->mCampos["tolerancia_dias_primer_abono"]);}
	function numero_avales($v = false){ if($v !== false){$this->mCampos["numero_avales"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_avales"]);}
	function nivel_autorizacion_oficial($v = false){ if($v !== false){$this->mCampos["nivel_autorizacion_oficial"]["V"] =  $v; } return new MQLCampo($this->mCampos["nivel_autorizacion_oficial"]);}
	function code_valoracion_javascript($v = false){ if($v !== false){$this->mCampos["code_valoracion_javascript"]["V"] =  $v; } return new MQLCampo($this->mCampos["code_valoracion_javascript"]);}
	function minimo_otorgable($v = false){ if($v !== false){$this->mCampos["minimo_otorgable"]["V"] =  $v; } return new MQLCampo($this->mCampos["minimo_otorgable"]);}
	function descripcion_completa($v = false){ if($v !== false){$this->mCampos["descripcion_completa"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_completa"]);}
	function oficial_seguimiento($v = false){ if($v !== false){$this->mCampos["oficial_seguimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["oficial_seguimiento"]);}
	function valoracion_php($v = false){ if($v !== false){$this->mCampos["valoracion_php"]["V"] =  $v; } return new MQLCampo($this->mCampos["valoracion_php"]);}
	function tipo_de_credito($v = false){ if($v !== false){$this->mCampos["tipo_de_credito"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_credito"]);}
	function php_monto_maximo($v = false){ if($v !== false){$this->mCampos["php_monto_maximo"]["V"] =  $v; } return new MQLCampo($this->mCampos["php_monto_maximo"]);}
	function tipo_de_convenio($v = false){ if($v !== false){$this->mCampos["tipo_de_convenio"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_convenio"]);}
	function tipo_de_garantia($v = false){ if($v !== false){$this->mCampos["tipo_de_garantia"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_garantia"]);}
	function estatus($v = false){ if($v !== false){$this->mCampos["estatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus"]);}
	function tasa_iva($v = false){ if($v !== false){$this->mCampos["tasa_iva"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_iva"]);}
	function contable_cartera_vigente($v = false){ if($v !== false){$this->mCampos["contable_cartera_vigente"]["V"] =  $v; } return new MQLCampo($this->mCampos["contable_cartera_vigente"]);}
	function contable_cartera_vencida($v = false){ if($v !== false){$this->mCampos["contable_cartera_vencida"]["V"] =  $v; } return new MQLCampo($this->mCampos["contable_cartera_vencida"]);}
	function contable_intereses_devengados($v = false){ if($v !== false){$this->mCampos["contable_intereses_devengados"]["V"] =  $v; } return new MQLCampo($this->mCampos["contable_intereses_devengados"]);}
	function contable_intereses_anticipados($v = false){ if($v !== false){$this->mCampos["contable_intereses_anticipados"]["V"] =  $v; } return new MQLCampo($this->mCampos["contable_intereses_anticipados"]);}
	function contable_intereses_cobrados($v = false){ if($v !== false){$this->mCampos["contable_intereses_cobrados"]["V"] =  $v; } return new MQLCampo($this->mCampos["contable_intereses_cobrados"]);}
	function contable_intereses_moratorios($v = false){ if($v !== false){$this->mCampos["contable_intereses_moratorios"]["V"] =  $v; } return new MQLCampo($this->mCampos["contable_intereses_moratorios"]);}
	function iva_incluido($v = false){ if($v !== false){$this->mCampos["iva_incluido"]["V"] =  $v; } return new MQLCampo($this->mCampos["iva_incluido"]);}
	function comision_por_apertura($v = false){ if($v !== false){$this->mCampos["comision_por_apertura"]["V"] =  $v; } return new MQLCampo($this->mCampos["comision_por_apertura"]);}
	function codigo_de_contrato($v = false){ if($v !== false){$this->mCampos["codigo_de_contrato"]["V"] =  $v; } return new MQLCampo($this->mCampos["codigo_de_contrato"]);}
	function contable_cartera_castigada($v = false){ if($v !== false){$this->mCampos["contable_cartera_castigada"]["V"] =  $v; } return new MQLCampo($this->mCampos["contable_cartera_castigada"]);}
	function path_del_contrato($v = false){ if($v !== false){$this->mCampos["path_del_contrato"]["V"] =  $v; } return new MQLCampo($this->mCampos["path_del_contrato"]);}
	function tipo_de_integracion($v = false){ if($v !== false){$this->mCampos["tipo_de_integracion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_integracion"]);}
	function contable_intereses_vencidos($v = false){ if($v !== false){$this->mCampos["contable_intereses_vencidos"]["V"] =  $v; } return new MQLCampo($this->mCampos["contable_intereses_vencidos"]);}
	function base_de_calculo_de_interes($v = false){ if($v !== false){$this->mCampos["base_de_calculo_de_interes"]["V"] =  $v; } return new MQLCampo($this->mCampos["base_de_calculo_de_interes"]);}
	function capital_vencido_renovado($v = false){ if($v !== false){$this->mCampos["capital_vencido_renovado"]["V"] =  $v; } return new MQLCampo($this->mCampos["capital_vencido_renovado"]);}
	function capital_vencido_reestructurado($v = false){ if($v !== false){$this->mCampos["capital_vencido_reestructurado"]["V"] =  $v; } return new MQLCampo($this->mCampos["capital_vencido_reestructurado"]);}
	function capital_vencido_normal($v = false){ if($v !== false){$this->mCampos["capital_vencido_normal"]["V"] =  $v; } return new MQLCampo($this->mCampos["capital_vencido_normal"]);}
	function capital_vigente_renovado($v = false){ if($v !== false){$this->mCampos["capital_vigente_renovado"]["V"] =  $v; } return new MQLCampo($this->mCampos["capital_vigente_renovado"]);}
	function capital_vigente_reestructurado($v = false){ if($v !== false){$this->mCampos["capital_vigente_reestructurado"]["V"] =  $v; } return new MQLCampo($this->mCampos["capital_vigente_reestructurado"]);}
	function capital_vigente_normal($v = false){ if($v !== false){$this->mCampos["capital_vigente_normal"]["V"] =  $v; } return new MQLCampo($this->mCampos["capital_vigente_normal"]);}
	function interes_cobrado($v = false){ if($v !== false){$this->mCampos["interes_cobrado"]["V"] =  $v; } return new MQLCampo($this->mCampos["interes_cobrado"]);}
	function moratorio_cobrado($v = false){ if($v !== false){$this->mCampos["moratorio_cobrado"]["V"] =  $v; } return new MQLCampo($this->mCampos["moratorio_cobrado"]);}
	function interes_vencido_renovado($v = false){ if($v !== false){$this->mCampos["interes_vencido_renovado"]["V"] =  $v; } return new MQLCampo($this->mCampos["interes_vencido_renovado"]);}
	function interes_vencido_reestructurado($v = false){ if($v !== false){$this->mCampos["interes_vencido_reestructurado"]["V"] =  $v; } return new MQLCampo($this->mCampos["interes_vencido_reestructurado"]);}
	function interes_vencido_normal($v = false){ if($v !== false){$this->mCampos["interes_vencido_normal"]["V"] =  $v; } return new MQLCampo($this->mCampos["interes_vencido_normal"]);}
	function interes_vigente_renovado($v = false){ if($v !== false){$this->mCampos["interes_vigente_renovado"]["V"] =  $v; } return new MQLCampo($this->mCampos["interes_vigente_renovado"]);}
	function interes_vigente_reestructurado($v = false){ if($v !== false){$this->mCampos["interes_vigente_reestructurado"]["V"] =  $v; } return new MQLCampo($this->mCampos["interes_vigente_reestructurado"]);}
	function interes_vigente_normal($v = false){ if($v !== false){$this->mCampos["interes_vigente_normal"]["V"] =  $v; } return new MQLCampo($this->mCampos["interes_vigente_normal"]);}
	function tipo_de_interes($v = false){ if($v !== false){$this->mCampos["tipo_de_interes"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_interes"]);}
	function aplica_mora_por_cobranza($v = false){ if($v !== false){$this->mCampos["aplica_mora_por_cobranza"]["V"] =  $v; } return new MQLCampo($this->mCampos["aplica_mora_por_cobranza"]);}
	function pre_modificador_de_interes($v = false){ if($v !== false){$this->mCampos["pre_modificador_de_interes"]["V"] =  $v; } return new MQLCampo($this->mCampos["pre_modificador_de_interes"]);}
	function pos_modificador_de_interes($v = false){ if($v !== false){$this->mCampos["pos_modificador_de_interes"]["V"] =  $v; } return new MQLCampo($this->mCampos["pos_modificador_de_interes"]);}
	function pre_modificador_de_ministracion($v = false){ if($v !== false){$this->mCampos["pre_modificador_de_ministracion"]["V"] =  $v; } return new MQLCampo($this->mCampos["pre_modificador_de_ministracion"]);}
	function pre_modificador_de_autorizacion($v = false){ if($v !== false){$this->mCampos["pre_modificador_de_autorizacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["pre_modificador_de_autorizacion"]);}
	function pre_modificador_de_vencimiento($v = false){ if($v !== false){$this->mCampos["pre_modificador_de_vencimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["pre_modificador_de_vencimiento"]);}
	function pre_modificador_de_solicitud($v = false){ if($v !== false){$this->mCampos["pre_modificador_de_solicitud"]["V"] =  $v; } return new MQLCampo($this->mCampos["pre_modificador_de_solicitud"]);}
	function clave_de_tipo_de_producto($v = false){ if($v !== false){$this->mCampos["clave_de_tipo_de_producto"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_tipo_de_producto"]);}
	function perfil_de_interes($v = false){ if($v !== false){$this->mCampos["perfil_de_interes"]["V"] =  $v; } return new MQLCampo($this->mCampos["perfil_de_interes"]);}
	function fuente_de_fondeo_predeterminado($v = false){ if($v !== false){$this->mCampos["fuente_de_fondeo_predeterminado"]["V"] =  $v; } return new MQLCampo($this->mCampos["fuente_de_fondeo_predeterminado"]);}
	function tipo_de_periocidad_preferente($v = false){ if($v !== false){$this->mCampos["tipo_de_periocidad_preferente"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_periocidad_preferente"]);}
	function numero_de_pagos_preferente($v = false){ if($v !== false){$this->mCampos["numero_de_pagos_preferente"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_de_pagos_preferente"]);}
	function tipo_en_sistema($v = false){ if($v !== false){$this->mCampos["tipo_en_sistema"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_en_sistema"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}


/*	ORM: Tabla:	socios_aeconomica	-	Generado:	[14/11/2014 11:47]	*/
class cSocios_aeconomica {
	private $mCampos	= array(
			"idsocios_aeconomica" => array("N"=>"idsocios_aeconomica","T"=>"INT","V"=>"","L"=>10),
			"socio_aeconomica" => array("N"=>"socio_aeconomica","T"=>"BIGINT","V"=>"0","L"=>20),
			"tipo_aeconomica" => array("N"=>"tipo_aeconomica","T"=>"BIGINT","V"=>"99","L"=>20),
			"sector_economico" => array("N"=>"sector_economico","T"=>"BIGINT","V"=>"99","L"=>20),
			"nombre_ae" => array("N"=>"nombre_ae","T"=>"VARCHAR","V"=>"","L"=>100),
			"domicilio_ae" => array("N"=>"domicilio_ae","T"=>"VARCHAR","V"=>"","L"=>100),
			"localidad_ae" => array("N"=>"localidad_ae","T"=>"VARCHAR","V"=>"","L"=>50),
			"municipio_ae" => array("N"=>"municipio_ae","T"=>"VARCHAR","V"=>"","L"=>50),
			"estado_ae" => array("N"=>"estado_ae","T"=>"VARCHAR","V"=>"","L"=>40),
			"telefono_ae" => array("N"=>"telefono_ae","T"=>"VARCHAR","V"=>"","L"=>18),
			"extension_ae" => array("N"=>"extension_ae","T"=>"VARCHAR","V"=>"","L"=>10),
			"numero_empleado" => array("N"=>"numero_empleado","T"=>"VARCHAR","V"=>"","L"=>10),
			"antiguedad_ae" => array("N"=>"antiguedad_ae","T"=>"INT","V"=>"0","L"=>10),
			"departamento_ae" => array("N"=>"departamento_ae","T"=>"VARCHAR","V"=>"","L"=>45),
			"monto_percibido_ae" => array("N"=>"monto_percibido_ae","T"=>"FLOAT","V"=>"0.00","L"=>25),
			"dependencia_ae" => array("N"=>"dependencia_ae","T"=>"INT","V"=>"99","L"=>10),
			"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"99","L"=>10),
			"fecha_alta" => array("N"=>"fecha_alta","T"=>"DATE","V"=>"","L"=>0),
			"puesto" => array("N"=>"puesto","T"=>"VARCHAR","V"=>"NA","L"=>65),
			"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"MATRIZ","L"=>20),
			"fecha_de_verificacion" => array("N"=>"fecha_de_verificacion","T"=>"DATE","V"=>"2012-01-01","L"=>0),
			"oficial_de_verificacion" => array("N"=>"oficial_de_verificacion","T"=>"INT","V"=>"1","L"=>10),
			"estado_actual" => array("N"=>"estado_actual","T"=>"INT","V"=>"99","L"=>10),
			"numero_de_seguridad_social" => array("N"=>"numero_de_seguridad_social","T"=>"VARCHAR","V"=>"","L"=>20),
			"domicilio_vinculado" => array("N"=>"domicilio_vinculado","T"=>"INT","V"=>"1","L"=>11),
			"ae_clave_de_localidad" => array("N"=>"ae_clave_de_localidad","T"=>"BIGINT","V"=>"1","L"=>20),
			"ae_codigo_postal" => array("N"=>"ae_codigo_postal","T"=>"INT","V"=>"0","L"=>11),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "socios_aeconomica";}
	function getKey(){ return "idsocios_aeconomica";}
	function idsocios_aeconomica($v = false){ if($v !== false){$this->mCampos["idsocios_aeconomica"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsocios_aeconomica"]);}
	function socio_aeconomica($v = false){ if($v !== false){$this->mCampos["socio_aeconomica"]["V"] =  $v; } return new MQLCampo($this->mCampos["socio_aeconomica"]);}
	function tipo_aeconomica($v = false){ if($v !== false){$this->mCampos["tipo_aeconomica"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_aeconomica"]);}
	function sector_economico($v = false){ if($v !== false){$this->mCampos["sector_economico"]["V"] =  $v; } return new MQLCampo($this->mCampos["sector_economico"]);}
	function nombre_ae($v = false){ if($v !== false){$this->mCampos["nombre_ae"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_ae"]);}
	function domicilio_ae($v = false){ if($v !== false){$this->mCampos["domicilio_ae"]["V"] =  $v; } return new MQLCampo($this->mCampos["domicilio_ae"]);}
	function localidad_ae($v = false){ if($v !== false){$this->mCampos["localidad_ae"]["V"] =  $v; } return new MQLCampo($this->mCampos["localidad_ae"]);}
	function municipio_ae($v = false){ if($v !== false){$this->mCampos["municipio_ae"]["V"] =  $v; } return new MQLCampo($this->mCampos["municipio_ae"]);}
	function estado_ae($v = false){ if($v !== false){$this->mCampos["estado_ae"]["V"] =  $v; } return new MQLCampo($this->mCampos["estado_ae"]);}
	function telefono_ae($v = false){ if($v !== false){$this->mCampos["telefono_ae"]["V"] =  $v; } return new MQLCampo($this->mCampos["telefono_ae"]);}
	function extension_ae($v = false){ if($v !== false){$this->mCampos["extension_ae"]["V"] =  $v; } return new MQLCampo($this->mCampos["extension_ae"]);}
	function numero_empleado($v = false){ if($v !== false){$this->mCampos["numero_empleado"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_empleado"]);}
	function antiguedad_ae($v = false){ if($v !== false){$this->mCampos["antiguedad_ae"]["V"] =  $v; } return new MQLCampo($this->mCampos["antiguedad_ae"]);}
	function departamento_ae($v = false){ if($v !== false){$this->mCampos["departamento_ae"]["V"] =  $v; } return new MQLCampo($this->mCampos["departamento_ae"]);}
	function monto_percibido_ae($v = false){ if($v !== false){$this->mCampos["monto_percibido_ae"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_percibido_ae"]);}
	function dependencia_ae($v = false){ if($v !== false){$this->mCampos["dependencia_ae"]["V"] =  $v; } return new MQLCampo($this->mCampos["dependencia_ae"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function fecha_alta($v = false){ if($v !== false){$this->mCampos["fecha_alta"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_alta"]);}
	function puesto($v = false){ if($v !== false){$this->mCampos["puesto"]["V"] =  $v; } return new MQLCampo($this->mCampos["puesto"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function fecha_de_verificacion($v = false){ if($v !== false){$this->mCampos["fecha_de_verificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_verificacion"]);}
	function oficial_de_verificacion($v = false){ if($v !== false){$this->mCampos["oficial_de_verificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["oficial_de_verificacion"]);}
	function estado_actual($v = false){ if($v !== false){$this->mCampos["estado_actual"]["V"] =  $v; } return new MQLCampo($this->mCampos["estado_actual"]);}
	function numero_de_seguridad_social($v = false){ if($v !== false){$this->mCampos["numero_de_seguridad_social"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_de_seguridad_social"]);}
	function domicilio_vinculado($v = false){ if($v !== false){$this->mCampos["domicilio_vinculado"]["V"] =  $v; } return new MQLCampo($this->mCampos["domicilio_vinculado"]);}
	function ae_clave_de_localidad($v = false){ if($v !== false){$this->mCampos["ae_clave_de_localidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["ae_clave_de_localidad"]);}
	function ae_codigo_postal($v = false){ if($v !== false){$this->mCampos["ae_codigo_postal"]["V"] =  $v; } return new MQLCampo($this->mCampos["ae_codigo_postal"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}


/*	ORM: Tabla:	socios_vivienda	-	Generado:	[31/5/2014 13:10]	*/
class cSocios_vivienda {
	private $mCampos	= array(
			"idsocios_vivienda" => array("N"=>"idsocios_vivienda","T"=>"INT","V"=>"","L"=>10),
			"socio_numero" => array("N"=>"socio_numero","T"=>"BIGINT","V"=>"1","L"=>20),
			"tipo_regimen" => array("N"=>"tipo_regimen","T"=>"INT","V"=>"99","L"=>4),
			"calle" => array("N"=>"calle","T"=>"VARCHAR","V"=>"","L"=>60),
			"numero_exterior" => array("N"=>"numero_exterior","T"=>"VARCHAR","V"=>"","L"=>45),
			"numero_interior" => array("N"=>"numero_interior","T"=>"VARCHAR","V"=>"","L"=>45),
			"colonia" => array("N"=>"colonia","T"=>"VARCHAR","V"=>"","L"=>150),
			"localidad" => array("N"=>"localidad","T"=>"VARCHAR","V"=>"","L"=>100),
			"estado" => array("N"=>"estado","T"=>"VARCHAR","V"=>"","L"=>100),
			"municipio" => array("N"=>"municipio","T"=>"VARCHAR","V"=>"","L"=>100),
			"telefono_residencial" => array("N"=>"telefono_residencial","T"=>"VARCHAR","V"=>"","L"=>20),
			"telefono_movil" => array("N"=>"telefono_movil","T"=>"VARCHAR","V"=>"","L"=>20),
			"tiempo_residencia" => array("N"=>"tiempo_residencia","T"=>"INT","V"=>"99","L"=>10),
			"referencia" => array("N"=>"referencia","T"=>"VARCHAR","V"=>"","L"=>200),
			"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"99","L"=>4),
			"principal" => array("N"=>"principal","T"=>"ENUM","V"=>"|0|1|","L"=>0),
			"tipo_domicilio" => array("N"=>"tipo_domicilio","T"=>"INT","V"=>"99","L"=>4),
			"codigo_postal" => array("N"=>"codigo_postal","T"=>"VARCHAR","V"=>"97000","L"=>15),
			"fecha_alta" => array("N"=>"fecha_alta","T"=>"DATE","V"=>"2005-12-31","L"=>0),
			"codigo" => array("N"=>"codigo","T"=>"BIGINT","V"=>"1","L"=>20),
			"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"MATRIZ","L"=>20),
			"eacp" => array("N"=>"eacp","T"=>"VARCHAR","V"=>"EN_TRAMITE","L"=>20),
			"coordenadas_gps" => array("N"=>"coordenadas_gps","T"=>"VARCHAR","V"=>"00,00,00","L"=>20),
			"tipo_de_acceso" => array("N"=>"tipo_de_acceso","T"=>"VARCHAR","V"=>"calle","L"=>20),
			"fecha_de_verificacion" => array("N"=>"fecha_de_verificacion","T"=>"DATE","V"=>"2012-01-01","L"=>0),
			"oficial_de_verificacion" => array("N"=>"oficial_de_verificacion","T"=>"INT","V"=>"1","L"=>10),
			"estado_actual" => array("N"=>"estado_actual","T"=>"INT","V"=>"99","L"=>10),
			"clave_de_localidad" => array("N"=>"clave_de_localidad","T"=>"INT","V"=>"","L"=>10),
			"clave_de_pais" => array("N"=>"clave_de_pais","T"=>"VARCHAR","V"=>"MX","L"=>10),
			"nombre_de_pais" => array("N"=>"nombre_de_pais","T"=>"VARCHAR","V"=>"Mexico","L"=>100),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "socios_vivienda";}
	function getKey(){ return "idsocios_vivienda";}

	function idsocios_vivienda($v = false){ if($v !== false){$this->mCampos["idsocios_vivienda"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsocios_vivienda"]);}
	function socio_numero($v = false){ if($v !== false){$this->mCampos["socio_numero"]["V"] =  $v; } return new MQLCampo($this->mCampos["socio_numero"]);}
	function tipo_regimen($v = false){ if($v !== false){$this->mCampos["tipo_regimen"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_regimen"]);}
	function calle($v = false){ if($v !== false){$this->mCampos["calle"]["V"] =  $v; } return new MQLCampo($this->mCampos["calle"]);}
	function numero_exterior($v = false){ if($v !== false){$this->mCampos["numero_exterior"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_exterior"]);}
	function numero_interior($v = false){ if($v !== false){$this->mCampos["numero_interior"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_interior"]);}
	function colonia($v = false){ if($v !== false){$this->mCampos["colonia"]["V"] =  $v; } return new MQLCampo($this->mCampos["colonia"]);}
	function localidad($v = false){ if($v !== false){$this->mCampos["localidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["localidad"]);}
	function estado($v = false){ if($v !== false){$this->mCampos["estado"]["V"] =  $v; } return new MQLCampo($this->mCampos["estado"]);}
	function municipio($v = false){ if($v !== false){$this->mCampos["municipio"]["V"] =  $v; } return new MQLCampo($this->mCampos["municipio"]);}
	function telefono_residencial($v = false){ if($v !== false){$this->mCampos["telefono_residencial"]["V"] =  $v; } return new MQLCampo($this->mCampos["telefono_residencial"]);}
	function telefono_movil($v = false){ if($v !== false){$this->mCampos["telefono_movil"]["V"] =  $v; } return new MQLCampo($this->mCampos["telefono_movil"]);}
	function tiempo_residencia($v = false){ if($v !== false){$this->mCampos["tiempo_residencia"]["V"] =  $v; } return new MQLCampo($this->mCampos["tiempo_residencia"]);}
	function referencia($v = false){ if($v !== false){$this->mCampos["referencia"]["V"] =  $v; } return new MQLCampo($this->mCampos["referencia"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function principal($v = false){ if($v !== false){$this->mCampos["principal"]["V"] =  $v; } return new MQLCampo($this->mCampos["principal"]);}
	function tipo_domicilio($v = false){ if($v !== false){$this->mCampos["tipo_domicilio"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_domicilio"]);}
	function codigo_postal($v = false){ if($v !== false){$this->mCampos["codigo_postal"]["V"] =  $v; } return new MQLCampo($this->mCampos["codigo_postal"]);}
	function fecha_alta($v = false){ if($v !== false){$this->mCampos["fecha_alta"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_alta"]);}
	function codigo($v = false){ if($v !== false){$this->mCampos["codigo"]["V"] =  $v; } return new MQLCampo($this->mCampos["codigo"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function eacp($v = false){ if($v !== false){$this->mCampos["eacp"]["V"] =  $v; } return new MQLCampo($this->mCampos["eacp"]);}
	function coordenadas_gps($v = false){ if($v !== false){$this->mCampos["coordenadas_gps"]["V"] =  $v; } return new MQLCampo($this->mCampos["coordenadas_gps"]);}
	function tipo_de_acceso($v = false){ if($v !== false){$this->mCampos["tipo_de_acceso"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_acceso"]);}
	function fecha_de_verificacion($v = false){ if($v !== false){$this->mCampos["fecha_de_verificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_verificacion"]);}
	function oficial_de_verificacion($v = false){ if($v !== false){$this->mCampos["oficial_de_verificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["oficial_de_verificacion"]);}
	function estado_actual($v = false){ if($v !== false){$this->mCampos["estado_actual"]["V"] =  $v; } return new MQLCampo($this->mCampos["estado_actual"]);}
	function clave_de_localidad($v = false){ if($v !== false){$this->mCampos["clave_de_localidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_localidad"]);}
	function clave_de_pais($v = false){ if($v !== false){$this->mCampos["clave_de_pais"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_pais"]);}
	function nombre_de_pais($v = false){ if($v !== false){$this->mCampos["nombre_de_pais"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_de_pais"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	socios_baja	-	Generado:	[26/12/2014 12:56]	*/
class cSocios_baja {
	private $mCampos	= array(
		"idsocios_baja" => array("N"=>"idsocios_baja","T"=>"INT","V"=>"","L"=>10),
		"numero_de_socio" => array("N"=>"numero_de_socio","T"=>"BIGINT","V"=>"","L"=>20),
		"fecha_de_baja" => array("N"=>"fecha_de_baja","T"=>"DATE","V"=>"","L"=>0),
		"razon_de_la_baja" => array("N"=>"razon_de_la_baja","T"=>"INT","V"=>"","L"=>10),
		"observaciones_de_baja" => array("N"=>"observaciones_de_baja","T"=>"VARCHAR","V"=>"","L"=>100),
		"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"matriz","L"=>20),
		"docto_presentado" => array("N"=>"docto_presentado","T"=>"VARCHAR","V"=>"ninguno","L"=>40),
		"fecha_de_documento" => array("N"=>"fecha_de_documento","T"=>"DATE","V"=>"","L"=>0),
		"fecha_de_vencimiento" => array("N"=>"fecha_de_vencimiento","T"=>"DATE","V"=>"","L"=>0),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "socios_baja";}
	function getKey(){ return "idsocios_baja";}
	function idsocios_baja($v = false){ if($v !== false){$this->mCampos["idsocios_baja"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsocios_baja"]);}
	function numero_de_socio($v = false){ if($v !== false){$this->mCampos["numero_de_socio"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_de_socio"]);}
	function fecha_de_baja($v = false){ if($v !== false){$this->mCampos["fecha_de_baja"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_baja"]);}
	function razon_de_la_baja($v = false){ if($v !== false){$this->mCampos["razon_de_la_baja"]["V"] =  $v; } return new MQLCampo($this->mCampos["razon_de_la_baja"]);}
	function observaciones_de_baja($v = false){ if($v !== false){$this->mCampos["observaciones_de_baja"]["V"] =  $v; } return new MQLCampo($this->mCampos["observaciones_de_baja"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function docto_presentado($v = false){ if($v !== false){$this->mCampos["docto_presentado"]["V"] =  $v; } return new MQLCampo($this->mCampos["docto_presentado"]);}
	function fecha_de_documento($v = false){ if($v !== false){$this->mCampos["fecha_de_documento"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_documento"]);}
	function fecha_de_vencimiento($v = false){ if($v !== false){$this->mCampos["fecha_de_vencimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_vencimiento"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}


//--- eacp_config_bases_de_integracion 		Generado: 13-10-25 10
class cEacp_config_bases_de_integracion {
	private $mCampos	= array(
			"codigo_de_base" => array("N"=>"codigo_de_base","T"=>"INT","V"=>"","L"=>10),
			"descripcion" => array("N"=>"descripcion","T"=>"VARCHAR","V"=>"","L"=>100),
			"tipo_de_base" => array("N"=>"tipo_de_base","T"=>"VARCHAR","V"=>"","L"=>25),

	);
	function __construct(){}
	function get(){ return "eacp_config_bases_de_integracion";}
	function getKey(){ return "codigo_de_base";}
	function codigo_de_base($v=false){
		if($v!==false){$this->mCampos["codigo_de_base"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["codigo_de_base"]);
	}
	function descripcion($v=false){
		if($v!==false){$this->mCampos["descripcion"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["descripcion"]);
	}
	function tipo_de_base($v=false){
		if($v!==false){$this->mCampos["tipo_de_base"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["tipo_de_base"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}

//--- creditos_estatus 		Generado: 13-11-01 13
class cCreditos_estatus {
	private $mCampos	= array(
		"idcreditos_estatus" => array("N"=>"idcreditos_estatus","T"=>"INT","V"=>"0","L"=>4),
		"descripcion_estatus" => array("N"=>"descripcion_estatus","T"=>"VARCHAR","V"=>"","L"=>45),
		"estatus_actual" => array("N"=>"estatus_actual","T"=>"INT","V"=>"0","L"=>4),
		"titulo_general" => array("N"=>"titulo_general","T"=>"VARCHAR","V"=>"","L"=>45),
		"orden_clasificacion" => array("N"=>"orden_clasificacion","T"=>"INT","V"=>"","L"=>4),
		"respetar_plan_de_pagos" => array("N"=>"respetar_plan_de_pagos","T"=>"ENUM","V"=>"|0|1|","L"=>0),

	);
	function __construct(){}
	function get(){ return "creditos_estatus";}
	function getKey(){ return "idcreditos_estatus";}
	function idcreditos_estatus($v=false){
 		if($v!==false){$this->mCampos["idcreditos_estatus"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["idcreditos_estatus"]);
	}
	function descripcion_estatus($v=false){
 		if($v!==false){$this->mCampos["descripcion_estatus"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["descripcion_estatus"]);
	}
	function estatus_actual($v=false){
 		if($v!==false){$this->mCampos["estatus_actual"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["estatus_actual"]);
	}
	function titulo_general($v=false){
 		if($v!==false){$this->mCampos["titulo_general"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["titulo_general"]);
	}
	function orden_clasificacion($v=false){
 		if($v!==false){$this->mCampos["orden_clasificacion"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["orden_clasificacion"]);
	}
	function respetar_plan_de_pagos($v=false){
 		if($v!==false){$this->mCampos["respetar_plan_de_pagos"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["respetar_plan_de_pagos"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}

/*	ORM: Tabla:	creditos_reconvenio	-	Generado:	[11/11/2013 11:50]	*/
class cCreditos_reconvenio {
	private $mCampos	= array(
		"idcreditos_reconvenio" => array("N"=>"idcreditos_reconvenio","T"=>"INT","V"=>"","L"=>10),
		"numero_solicitud" => array("N"=>"numero_solicitud","T"=>"BIGINT","V"=>"0","L"=>20),
		"fecha_reconvenio" => array("N"=>"fecha_reconvenio","T"=>"DATE","V"=>"0000-00-00","L"=>0),
		"monto_reconvenido" => array("N"=>"monto_reconvenido","T"=>"FLOAT","V"=>"0.00","L"=>25),
		"periocidad_reconvenida" => array("N"=>"periocidad_reconvenida","T"=>"INT","V"=>"0","L"=>4),
		"tasa_reconvenida" => array("N"=>"tasa_reconvenida","T"=>"FLOAT","V"=>"0.000","L"=>7),
		"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"99","L"=>4),
		"codigo" => array("N"=>"codigo","T"=>"BIGINT","V"=>"1","L"=>20),
		"pagos_reconvenidos" => array("N"=>"pagos_reconvenidos","T"=>"INT","V"=>"1","L"=>4),
		"dias" => array("N"=>"dias","T"=>"INT","V"=>"1","L"=>10),
		"vence" => array("N"=>"vence","T"=>"DATE","V"=>"","L"=>0),
		"interes_diario_re" => array("N"=>"interes_diario_re","T"=>"FLOAT","V"=>"0.00","L"=>25),
		"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"MATRIZ","L"=>10),
		"interes_pendiente" => array("N"=>"interes_pendiente","T"=>"FLOAT","V"=>"0.00","L"=>25),
		"eacp" => array("N"=>"eacp","T"=>"VARCHAR","V"=>"","L"=>25),
		"credito_origen" => array("N"=>"credito_origen","T"=>"BIGINT","V"=>"0","L"=>20),

	);
	function __construct(){}
	function get(){ return "creditos_reconvenio";}
	function getKey(){ return "idcreditos_reconvenio";}
	function idcreditos_reconvenio($v=false){
		if($v!==false){$this->mCampos["idcreditos_reconvenio"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["idcreditos_reconvenio"]);
	}
	function numero_solicitud($v=false){
		if($v!==false){$this->mCampos["numero_solicitud"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["numero_solicitud"]);
	}
	function fecha_reconvenio($v=false){
		if($v!==false){$this->mCampos["fecha_reconvenio"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["fecha_reconvenio"]);
	}
	function monto_reconvenido($v=false){
		if($v!==false){$this->mCampos["monto_reconvenido"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["monto_reconvenido"]);
	}
	function periocidad_reconvenida($v=false){
		if($v!==false){$this->mCampos["periocidad_reconvenida"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["periocidad_reconvenida"]);
	}
	function tasa_reconvenida($v=false){
		if($v!==false){$this->mCampos["tasa_reconvenida"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["tasa_reconvenida"]);
	}
	function idusuario($v=false){
		if($v!==false){$this->mCampos["idusuario"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["idusuario"]);
	}
	function codigo($v=false){
		if($v!==false){$this->mCampos["codigo"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["codigo"]);
	}
	function pagos_reconvenidos($v=false){
		if($v!==false){$this->mCampos["pagos_reconvenidos"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["pagos_reconvenidos"]);
	}
	function dias($v=false){
		if($v!==false){$this->mCampos["dias"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["dias"]);
	}
	function vence($v=false){
		if($v!==false){$this->mCampos["vence"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["vence"]);
	}
	function interes_diario_re($v=false){
		if($v!==false){$this->mCampos["interes_diario_re"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["interes_diario_re"]);
	}
	function sucursal($v=false){
		if($v!==false){$this->mCampos["sucursal"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["sucursal"]);
	}
	function interes_pendiente($v=false){
		if($v!==false){$this->mCampos["interes_pendiente"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["interes_pendiente"]);
	}
	function eacp($v=false){
		if($v!==false){$this->mCampos["eacp"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["eacp"]);
	}
	function credito_origen($v=false){
		if($v!==false){$this->mCampos["credito_origen"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["credito_origen"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}

/*	ORM: Tabla:	creditos_tipo_de_autorizacion	-	Generado:	[25/9/2014 13:51]	*/
class cCreditos_tipo_de_autorizacion {
	private $mCampos	= array(
			"idcreditos_tipo_de_autorizacion" => array("N"=>"idcreditos_tipo_de_autorizacion","T"=>"INT","V"=>"","L"=>10),
			"descripcion_tipo_de_autorizacion" => array("N"=>"descripcion_tipo_de_autorizacion","T"=>"VARCHAR","V"=>"","L"=>50),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_tipo_de_autorizacion";}
	function getKey(){ return "idcreditos_tipo_de_autorizacion";}
	function idcreditos_tipo_de_autorizacion($v = false){ if($v !== false){$this->mCampos["idcreditos_tipo_de_autorizacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_tipo_de_autorizacion"]);}
	function descripcion_tipo_de_autorizacion($v = false){ if($v !== false){$this->mCampos["descripcion_tipo_de_autorizacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_tipo_de_autorizacion"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}


/*	ORM: Tabla:	operaciones_tipos	-	Generado:	[10/12/2013 11:35]	*/
class cOperaciones_tipos {
	private $mCampos	= array(
		"idoperaciones_tipos" => array("N"=>"idoperaciones_tipos","T"=>"INT","V"=>"","L"=>4),
		"descripcion_operacion" => array("N"=>"descripcion_operacion","T"=>"VARCHAR","V"=>"","L"=>100),
		"clasificacion" => array("N"=>"clasificacion","T"=>"INT","V"=>"0","L"=>4),
		"subclasificacion" => array("N"=>"subclasificacion","T"=>"INT","V"=>"0","L"=>4),
		"cuenta_contable" => array("N"=>"cuenta_contable","T"=>"VARCHAR","V"=>"","L"=>100),
		"descripcion" => array("N"=>"descripcion","T"=>"VARCHAR","V"=>"","L"=>200),
		"recibo_que_afecta" => array("N"=>"recibo_que_afecta","T"=>"INT","V"=>"99","L"=>4),
		"tipo_operacion" => array("N"=>"tipo_operacion","T"=>"INT","V"=>"0","L"=>4),
		"visible_reporte" => array("N"=>"visible_reporte","T"=>"INT","V"=>"1","L"=>4),
		"class_efectivo" => array("N"=>"class_efectivo","T"=>"INT","V"=>"1","L"=>4),
		"mvto_que_afecta" => array("N"=>"mvto_que_afecta","T"=>"INT","V"=>"99","L"=>4),
		"afectacion_en_recibo" => array("N"=>"afectacion_en_recibo","T"=>"INT","V"=>"0","L"=>4),
		"afectacion_en_notificacion" => array("N"=>"afectacion_en_notificacion","T"=>"INT","V"=>"0","L"=>4),
		"producto_aplicable" => array("N"=>"producto_aplicable","T"=>"INT","V"=>"0","L"=>4),
		"constituye_fondo_automatico" => array("N"=>"constituye_fondo_automatico","T"=>"ENUM","V"=>"|1|0|","L"=>0),
		"integra_vencido" => array("N"=>"integra_vencido","T"=>"ENUM","V"=>"|1|0|","L"=>0),
		"afectacion_en_sdpm" => array("N"=>"afectacion_en_sdpm","T"=>"INT","V"=>"0","L"=>4),
		"cargo_directo" => array("N"=>"cargo_directo","T"=>"INT","V"=>"0","L"=>2),
		"codigo_de_valoracion" => array("N"=>"codigo_de_valoracion","T"=>"TEXT","V"=>"","L"=>0),
		"periocidad_afectada" => array("N"=>"periocidad_afectada","T"=>"ENUM","V"=>"|ninguna|todas|vencimiento|periodico|","L"=>0),
		"integra_parcialidad" => array("N"=>"integra_parcialidad","T"=>"ENUM","V"=>"|1|0|","L"=>0),
		"es_estadistico" => array("N"=>"es_estadistico","T"=>"ENUM","V"=>"|1|0|","L"=>0),
		"formula_de_calculo" => array("N"=>"formula_de_calculo","T"=>"TINYTEXT","V"=>"","L"=>0),
		"formula_de_cancelacion" => array("N"=>"formula_de_cancelacion","T"=>"TEXT","V"=>"","L"=>0),
		"importancia_de_neutralizacion" => array("N"=>"importancia_de_neutralizacion","T"=>"INT","V"=>"0","L"=>4),
		"preservar_movimiento" => array("N"=>"preservar_movimiento","T"=>"ENUM","V"=>"|0|1|","L"=>0),
		"tasa_iva" => array("N"=>"tasa_iva","T"=>"FLOAT","V"=>"0.000","L"=>17),
		"nombre_corto" => array("N"=>"nombre_corto","T"=>"VARCHAR","V"=>"","L"=>15),
		"estatus" => array("N"=>"estatus","T"=>"INT","V"=>"","L"=>2),

	);
	function __construct(){}
	function get(){ return "operaciones_tipos";}
	function getKey(){ return "idoperaciones_tipos";}
	function idoperaciones_tipos($v=false){
 		if($v!==false){$this->mCampos["idoperaciones_tipos"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["idoperaciones_tipos"]);
	}
	function descripcion_operacion($v=false){
 		if($v!==false){$this->mCampos["descripcion_operacion"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["descripcion_operacion"]);
	}
	function clasificacion($v=false){
 		if($v!==false){$this->mCampos["clasificacion"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["clasificacion"]);
	}
	function subclasificacion($v=false){
 		if($v!==false){$this->mCampos["subclasificacion"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["subclasificacion"]);
	}
	function cuenta_contable($v=false){
 		if($v!==false){$this->mCampos["cuenta_contable"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["cuenta_contable"]);
	}
	function descripcion($v=false){
 		if($v!==false){$this->mCampos["descripcion"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["descripcion"]);
	}
	function recibo_que_afecta($v=false){
 		if($v!==false){$this->mCampos["recibo_que_afecta"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["recibo_que_afecta"]);
	}
	function tipo_operacion($v=false){
 		if($v!==false){$this->mCampos["tipo_operacion"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["tipo_operacion"]);
	}
	function visible_reporte($v=false){
 		if($v!==false){$this->mCampos["visible_reporte"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["visible_reporte"]);
	}
	function class_efectivo($v=false){
 		if($v!==false){$this->mCampos["class_efectivo"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["class_efectivo"]);
	}
	function mvto_que_afecta($v=false){
 		if($v!==false){$this->mCampos["mvto_que_afecta"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["mvto_que_afecta"]);
	}
	function afectacion_en_recibo($v=false){
 		if($v!==false){$this->mCampos["afectacion_en_recibo"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["afectacion_en_recibo"]);
	}
	function afectacion_en_notificacion($v=false){
 		if($v!==false){$this->mCampos["afectacion_en_notificacion"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["afectacion_en_notificacion"]);
	}
	function producto_aplicable($v=false){
 		if($v!==false){$this->mCampos["producto_aplicable"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["producto_aplicable"]);
	}
	function constituye_fondo_automatico($v=false){
 		if($v!==false){$this->mCampos["constituye_fondo_automatico"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["constituye_fondo_automatico"]);
	}
	function integra_vencido($v=false){
 		if($v!==false){$this->mCampos["integra_vencido"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["integra_vencido"]);
	}
	function afectacion_en_sdpm($v=false){
 		if($v!==false){$this->mCampos["afectacion_en_sdpm"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["afectacion_en_sdpm"]);
	}
	function cargo_directo($v=false){
 		if($v!==false){$this->mCampos["cargo_directo"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["cargo_directo"]);
	}
	function codigo_de_valoracion($v=false){
 		if($v!==false){$this->mCampos["codigo_de_valoracion"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["codigo_de_valoracion"]);
	}
	function periocidad_afectada($v=false){
 		if($v!==false){$this->mCampos["periocidad_afectada"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["periocidad_afectada"]);
	}
	function integra_parcialidad($v=false){
 		if($v!==false){$this->mCampos["integra_parcialidad"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["integra_parcialidad"]);
	}
	function es_estadistico($v=false){
 		if($v!==false){$this->mCampos["es_estadistico"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["es_estadistico"]);
	}
	function formula_de_calculo($v=false){
 		if($v!==false){$this->mCampos["formula_de_calculo"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["formula_de_calculo"]);
	}
	function formula_de_cancelacion($v=false){
 		if($v!==false){$this->mCampos["formula_de_cancelacion"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["formula_de_cancelacion"]);
	}
	function importancia_de_neutralizacion($v=false){
 		if($v!==false){$this->mCampos["importancia_de_neutralizacion"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["importancia_de_neutralizacion"]);
	}
	function preservar_movimiento($v=false){
 		if($v!==false){$this->mCampos["preservar_movimiento"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["preservar_movimiento"]);
	}
	function tasa_iva($v=false){
 		if($v!==false){$this->mCampos["tasa_iva"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["tasa_iva"]);
	}
	function nombre_corto($v=false){
 		if($v!==false){$this->mCampos["nombre_corto"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["nombre_corto"]);
	}
	function estatus($v=false){
 		if($v!==false){$this->mCampos["estatus"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["estatus"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}

/*	ORM: Tabla:	operaciones_archivo_de_facturas	-	Generado:	[15/10/2014 12:39]	*/
class cOperaciones_archivo_de_facturas {
	private $mCampos	= array(
		"uuid" => array("N"=>"uuid","T"=>"VARCHAR","V"=>"","L"=>200),
		"clave_de_recibo" => array("N"=>"clave_de_recibo","T"=>"BIGINT","V"=>"1","L"=>25),
		"contenido" => array("N"=>"contenido","T"=>"LONGTEXT","V"=>"","L"=>0),
		"impreso" => array("N"=>"impreso","T"=>"LONGTEXT","V"=>"","L"=>0),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "operaciones_archivo_de_facturas";}
	function getKey(){ return "uuid";}
	function uuid($v = false){ if($v !== false){$this->mCampos["uuid"]["V"] =  $v; } return new MQLCampo($this->mCampos["uuid"]);}
	function clave_de_recibo($v = false){ if($v !== false){$this->mCampos["clave_de_recibo"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_recibo"]);}
	function contenido($v = false){ if($v !== false){$this->mCampos["contenido"]["V"] =  $v; } return new MQLCampo($this->mCampos["contenido"]);}
	function impreso($v = false){ if($v !== false){$this->mCampos["impreso"]["V"] =  $v; } return new MQLCampo($this->mCampos["impreso"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}


/*	ORM: Tabla:	operaciones_mvtos	-	Generado:	[11/12/2013 12:51]	*/
class cOperaciones_mvtos {
	private $mCampos	= array(
		"idoperaciones_mvtos" => array("N"=>"idoperaciones_mvtos","T"=>"BIGINT","V"=>"","L"=>20),
		"fecha_operacion" => array("N"=>"fecha_operacion","T"=>"DATE","V"=>"0000-00-00","L"=>0),
		"fecha_afectacion" => array("N"=>"fecha_afectacion","T"=>"DATE","V"=>"0000-00-00","L"=>0),
		"recibo_afectado" => array("N"=>"recibo_afectado","T"=>"BIGINT","V"=>"1","L"=>20),
		"socio_afectado" => array("N"=>"socio_afectado","T"=>"BIGINT","V"=>"1","L"=>20),
		"docto_afectado" => array("N"=>"docto_afectado","T"=>"BIGINT","V"=>"1","L"=>20),
		"tipo_operacion" => array("N"=>"tipo_operacion","T"=>"INT","V"=>"99","L"=>4),
		"afectacion_real" => array("N"=>"afectacion_real","T"=>"FLOAT","V"=>"0.00","L"=>25),
		"afectacion_cobranza" => array("N"=>"afectacion_cobranza","T"=>"FLOAT","V"=>"0.00","L"=>25),
		"afectacion_contable" => array("N"=>"afectacion_contable","T"=>"FLOAT","V"=>"0.00","L"=>25),
		"valor_afectacion" => array("N"=>"valor_afectacion","T"=>"FLOAT","V"=>"0.00","L"=>25),
		"fecha_vcto" => array("N"=>"fecha_vcto","T"=>"DATE","V"=>"0000-00-00","L"=>0),
		"estatus_mvto" => array("N"=>"estatus_mvto","T"=>"INT","V"=>"99","L"=>4),
		"codigo_eacp" => array("N"=>"codigo_eacp","T"=>"VARCHAR","V"=>"EN_TRAMITE","L"=>15),
		"periodo_socio" => array("N"=>"periodo_socio","T"=>"INT","V"=>"1","L"=>4),
		"periodo_contable" => array("N"=>"periodo_contable","T"=>"INT","V"=>"99","L"=>4),
		"periodo_cobranza" => array("N"=>"periodo_cobranza","T"=>"INT","V"=>"99","L"=>4),
		"periodo_seguimiento" => array("N"=>"periodo_seguimiento","T"=>"INT","V"=>"99","L"=>4),
		"periodo_mensual" => array("N"=>"periodo_mensual","T"=>"INT","V"=>"99","L"=>4),
		"periodo_semanal" => array("N"=>"periodo_semanal","T"=>"INT","V"=>"99","L"=>4),
		"periodo_anual" => array("N"=>"periodo_anual","T"=>"INT","V"=>"99","L"=>4),
		"saldo_anterior" => array("N"=>"saldo_anterior","T"=>"FLOAT","V"=>"0.00","L"=>25),
		"saldo_actual" => array("N"=>"saldo_actual","T"=>"FLOAT","V"=>"0.00","L"=>25),
		"detalles" => array("N"=>"detalles","T"=>"VARCHAR","V"=>"","L"=>200),
		"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"99","L"=>4),
		"afectacion_estadistica" => array("N"=>"afectacion_estadistica","T"=>"FLOAT","V"=>"0.00","L"=>25),
		"docto_neutralizador" => array("N"=>"docto_neutralizador","T"=>"BIGINT","V"=>"1","L"=>20),
		"cadena_heredada" => array("N"=>"cadena_heredada","T"=>"VARCHAR","V"=>"","L"=>200),
		"tasa_asociada" => array("N"=>"tasa_asociada","T"=>"FLOAT","V"=>"0.00","L"=>25),
		"dias_asociados" => array("N"=>"dias_asociados","T"=>"INT","V"=>"0","L"=>10),
		"grupo_asociado" => array("N"=>"grupo_asociado","T"=>"BIGINT","V"=>"0","L"=>20),
		"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"MATRIZ","L"=>10),

	);
	function __construct(){}
	function get(){ return "operaciones_mvtos";}
	function getKey(){ return "idoperaciones_mvtos";}
	function idoperaciones_mvtos($v=false){
 		if($v!==false){$this->mCampos["idoperaciones_mvtos"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["idoperaciones_mvtos"]);
	}
	function fecha_operacion($v=false){
 		if($v!==false){$this->mCampos["fecha_operacion"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["fecha_operacion"]);
	}
	function fecha_afectacion($v=false){
 		if($v!==false){$this->mCampos["fecha_afectacion"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["fecha_afectacion"]);
	}
	function recibo_afectado($v=false){
 		if($v!==false){$this->mCampos["recibo_afectado"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["recibo_afectado"]);
	}
	function socio_afectado($v=false){
 		if($v!==false){$this->mCampos["socio_afectado"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["socio_afectado"]);
	}
	function docto_afectado($v=false){
 		if($v!==false){$this->mCampos["docto_afectado"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["docto_afectado"]);
	}
	function tipo_operacion($v=false){
 		if($v!==false){$this->mCampos["tipo_operacion"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["tipo_operacion"]);
	}
	function afectacion_real($v=false){
 		if($v!==false){$this->mCampos["afectacion_real"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["afectacion_real"]);
	}
	function afectacion_cobranza($v=false){
 		if($v!==false){$this->mCampos["afectacion_cobranza"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["afectacion_cobranza"]);
	}
	function afectacion_contable($v=false){
 		if($v!==false){$this->mCampos["afectacion_contable"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["afectacion_contable"]);
	}
	function valor_afectacion($v=false){
 		if($v!==false){$this->mCampos["valor_afectacion"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["valor_afectacion"]);
	}
	function fecha_vcto($v=false){
 		if($v!==false){$this->mCampos["fecha_vcto"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["fecha_vcto"]);
	}
	function estatus_mvto($v=false){
 		if($v!==false){$this->mCampos["estatus_mvto"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["estatus_mvto"]);
	}
	function codigo_eacp($v=false){
 		if($v!==false){$this->mCampos["codigo_eacp"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["codigo_eacp"]);
	}
	function periodo_socio($v=false){
 		if($v!==false){$this->mCampos["periodo_socio"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["periodo_socio"]);
	}
	function periodo_contable($v=false){
 		if($v!==false){$this->mCampos["periodo_contable"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["periodo_contable"]);
	}
	function periodo_cobranza($v=false){
 		if($v!==false){$this->mCampos["periodo_cobranza"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["periodo_cobranza"]);
	}
	function periodo_seguimiento($v=false){
 		if($v!==false){$this->mCampos["periodo_seguimiento"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["periodo_seguimiento"]);
	}
	function periodo_mensual($v=false){
 		if($v!==false){$this->mCampos["periodo_mensual"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["periodo_mensual"]);
	}
	function periodo_semanal($v=false){
 		if($v!==false){$this->mCampos["periodo_semanal"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["periodo_semanal"]);
	}
	function periodo_anual($v=false){
 		if($v!==false){$this->mCampos["periodo_anual"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["periodo_anual"]);
	}
	function saldo_anterior($v=false){
 		if($v!==false){$this->mCampos["saldo_anterior"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["saldo_anterior"]);
	}
	function saldo_actual($v=false){
 		if($v!==false){$this->mCampos["saldo_actual"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["saldo_actual"]);
	}
	function detalles($v=false){
 		if($v!==false){$this->mCampos["detalles"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["detalles"]);
	}
	function idusuario($v=false){
 		if($v!==false){$this->mCampos["idusuario"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["idusuario"]);
	}
	function afectacion_estadistica($v=false){
 		if($v!==false){$this->mCampos["afectacion_estadistica"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["afectacion_estadistica"]);
	}
	function docto_neutralizador($v=false){
 		if($v!==false){$this->mCampos["docto_neutralizador"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["docto_neutralizador"]);
	}
	function cadena_heredada($v=false){
 		if($v!==false){$this->mCampos["cadena_heredada"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["cadena_heredada"]);
	}
	function tasa_asociada($v=false){
 		if($v!==false){$this->mCampos["tasa_asociada"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["tasa_asociada"]);
	}
	function dias_asociados($v=false){
 		if($v!==false){$this->mCampos["dias_asociados"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["dias_asociados"]);
	}
	function grupo_asociado($v=false){
 		if($v!==false){$this->mCampos["grupo_asociado"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["grupo_asociado"]);
	}
	function sucursal($v=false){
 		if($v!==false){$this->mCampos["sucursal"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["sucursal"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}

/*	ORM: Tabla:	letras	-	Generado:	[18/12/2013 12:32]	*/
class cLetrasVista {
	private $mCampos	= array(
		"codigo_de_base" => array("N"=>"codigo_de_base","T"=>"INT","V"=>"","L"=>10),
		"socio_afectado" => array("N"=>"socio_afectado","T"=>"BIGINT","V"=>"1","L"=>20),
		"docto_afectado" => array("N"=>"docto_afectado","T"=>"BIGINT","V"=>"1","L"=>20),
		"periodo_socio" => array("N"=>"periodo_socio","T"=>"INT","V"=>"1","L"=>4),
		"fecha_de_pago" => array("N"=>"fecha_de_pago","T"=>"DATE","V"=>"","L"=>0),
		"fecha_de_vencimiento" => array("N"=>"fecha_de_vencimiento","T"=>"DATE","V"=>"","L"=>0),
		"capital" => array("N"=>"capital","T"=>"DOUBLE","V"=>"","L"=>39),
		"interes" => array("N"=>"interes","T"=>"DOUBLE","V"=>"","L"=>39),
		"iva" => array("N"=>"iva","T"=>"DOUBLE","V"=>"","L"=>39),
		"ahorro" => array("N"=>"ahorro","T"=>"DOUBLE","V"=>"","L"=>39),
		"otros" => array("N"=>"otros","T"=>"DOUBLE","V"=>"","L"=>39),
		"letra" => array("N"=>"letra","T"=>"DOUBLE","V"=>"","L"=>43),

	);
	function __construct(){}
	function getKey(){return false;}
	function get(){ return "letras";}
	function codigo_de_base($v=false){
		if($v!==false){$this->mCampos["codigo_de_base"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["codigo_de_base"]);
	}
	function socio_afectado($v=false){
		if($v!==false){$this->mCampos["socio_afectado"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["socio_afectado"]);
	}
	function docto_afectado($v=false){
		if($v!==false){$this->mCampos["docto_afectado"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["docto_afectado"]);
	}
	function periodo_socio($v=false){
		if($v!==false){$this->mCampos["periodo_socio"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["periodo_socio"]);
	}
	function fecha_de_pago($v=false){
		if($v!==false){$this->mCampos["fecha_de_pago"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["fecha_de_pago"]);
	}
	function fecha_de_vencimiento($v=false){
		if($v!==false){$this->mCampos["fecha_de_vencimiento"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["fecha_de_vencimiento"]);
	}
	function capital($v=false){
		if($v!==false){$this->mCampos["capital"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["capital"]);
	}
	function interes($v=false){
		if($v!==false){$this->mCampos["interes"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["interes"]);
	}
	function iva($v=false){
		if($v!==false){$this->mCampos["iva"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["iva"]);
	}
	function ahorro($v=false){
		if($v!==false){$this->mCampos["ahorro"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["ahorro"]);
	}
	function otros($v=false){
		if($v!==false){$this->mCampos["otros"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["otros"]);
	}
	function letra($v=false){
		if($v!==false){$this->mCampos["letra"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["letra"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}

/*	ORM: Tabla:	socios_estadocivil	-	Generado:	[19/12/2013 15:37]	*/
class cSocios_estadocivil {
	private $mCampos	= array(
		"idsocios_estadocivil" => array("N"=>"idsocios_estadocivil","T"=>"INT","V"=>"0","L"=>4),
		"descripcion_estadocivil" => array("N"=>"descripcion_estadocivil","T"=>"VARCHAR","V"=>"","L"=>45),
		"valor_scoring" => array("N"=>"valor_scoring","T"=>"FLOAT","V"=>"0.000","L"=>13)
	);
	function __construct(){}
	function get(){ return "socios_estadocivil";}
	function getKey(){ return "idsocios_estadocivil";}
	function idsocios_estadocivil($v=false){
		if($v!==false){$this->mCampos["idsocios_estadocivil"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["idsocios_estadocivil"]);
	}
	function descripcion_estadocivil($v=false){
		if($v!==false){$this->mCampos["descripcion_estadocivil"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["descripcion_estadocivil"]);
	}
	function valor_scoring($v=false){
		if($v!==false){$this->mCampos["valor_scoring"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["valor_scoring"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}

/*	ORM: Tabla:	operaciones_recibos	-	Generado:	[15/9/2014 17:00]	*/
class cOperaciones_recibos {
	private $mCampos	= array(
			"idoperaciones_recibos" => array("N"=>"idoperaciones_recibos","T"=>"BIGINT","V"=>"","L"=>20),
			"fecha_operacion" => array("N"=>"fecha_operacion","T"=>"DATE","V"=>"0000-00-00","L"=>0),
			"numero_socio" => array("N"=>"numero_socio","T"=>"BIGINT","V"=>"1","L"=>20),
			"docto_afectado" => array("N"=>"docto_afectado","T"=>"BIGINT","V"=>"1","L"=>20),
			"tipo_docto" => array("N"=>"tipo_docto","T"=>"INT","V"=>"99","L"=>4),
			"total_operacion" => array("N"=>"total_operacion","T"=>"FLOAT","V"=>"0.00","L"=>25),
			"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"99","L"=>4),
			"observacion_recibo" => array("N"=>"observacion_recibo","T"=>"VARCHAR","V"=>"","L"=>200),
			"cheque_afectador" => array("N"=>"cheque_afectador","T"=>"VARCHAR","V"=>"N/A","L"=>20),
			"cadena_distributiva" => array("N"=>"cadena_distributiva","T"=>"VARCHAR","V"=>"N/A","L"=>200),
			"tipo_pago" => array("N"=>"tipo_pago","T"=>"VARCHAR","V"=>"efectivo","L"=>25),
			"indice_origen" => array("N"=>"indice_origen","T"=>"INT","V"=>"99","L"=>4),
			"grupo_asociado" => array("N"=>"grupo_asociado","T"=>"BIGINT","V"=>"99","L"=>20),
			"recibo_fiscal" => array("N"=>"recibo_fiscal","T"=>"VARCHAR","V"=>"N/A","L"=>15),
			"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"MATRIZ","L"=>10),
			"eacp" => array("N"=>"eacp","T"=>"VARCHAR","V"=>"EN_TRAMITE","L"=>20),
			"clave_de_moneda" => array("N"=>"clave_de_moneda","T"=>"VARCHAR","V"=>"MXN","L"=>6),
			"unidades_en_moneda" => array("N"=>"unidades_en_moneda","T"=>"FLOAT","V"=>"0.0000","L"=>33),
			"origen_aml" => array("N"=>"origen_aml","T"=>"INT","V"=>"0","L"=>4),
			"archivo_fisico" => array("N"=>"archivo_fisico","T"=>"VARCHAR","V"=>"","L"=>200),
			"persona_asociada" => array("N"=>"persona_asociada","T"=>"BIGINT","V"=>"0","L"=>20),
			"fecha_de_registro" => array("N"=>"fecha_de_registro","T"=>"DATE","V"=>"0000-00-00","L"=>0),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "operaciones_recibos";}
	function getKey(){ return "idoperaciones_recibos";}
	function idoperaciones_recibos($v = false){ if($v !== false){$this->mCampos["idoperaciones_recibos"]["V"] =  $v; } return new MQLCampo($this->mCampos["idoperaciones_recibos"]);}
	function fecha_operacion($v = false){ if($v !== false){$this->mCampos["fecha_operacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_operacion"]);}
	function numero_socio($v = false){ if($v !== false){$this->mCampos["numero_socio"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_socio"]);}
	function docto_afectado($v = false){ if($v !== false){$this->mCampos["docto_afectado"]["V"] =  $v; } return new MQLCampo($this->mCampos["docto_afectado"]);}
	function tipo_docto($v = false){ if($v !== false){$this->mCampos["tipo_docto"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_docto"]);}
	function total_operacion($v = false){ if($v !== false){$this->mCampos["total_operacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["total_operacion"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function observacion_recibo($v = false){ if($v !== false){$this->mCampos["observacion_recibo"]["V"] =  $v; } return new MQLCampo($this->mCampos["observacion_recibo"]);}
	function cheque_afectador($v = false){ if($v !== false){$this->mCampos["cheque_afectador"]["V"] =  $v; } return new MQLCampo($this->mCampos["cheque_afectador"]);}
	function cadena_distributiva($v = false){ if($v !== false){$this->mCampos["cadena_distributiva"]["V"] =  $v; } return new MQLCampo($this->mCampos["cadena_distributiva"]);}
	function tipo_pago($v = false){ if($v !== false){$this->mCampos["tipo_pago"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_pago"]);}
	function indice_origen($v = false){ if($v !== false){$this->mCampos["indice_origen"]["V"] =  $v; } return new MQLCampo($this->mCampos["indice_origen"]);}
	function grupo_asociado($v = false){ if($v !== false){$this->mCampos["grupo_asociado"]["V"] =  $v; } return new MQLCampo($this->mCampos["grupo_asociado"]);}
	function recibo_fiscal($v = false){ if($v !== false){$this->mCampos["recibo_fiscal"]["V"] =  $v; } return new MQLCampo($this->mCampos["recibo_fiscal"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function eacp($v = false){ if($v !== false){$this->mCampos["eacp"]["V"] =  $v; } return new MQLCampo($this->mCampos["eacp"]);}
	function clave_de_moneda($v = false){ if($v !== false){$this->mCampos["clave_de_moneda"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_moneda"]);}
	function unidades_en_moneda($v = false){ if($v !== false){$this->mCampos["unidades_en_moneda"]["V"] =  $v; } return new MQLCampo($this->mCampos["unidades_en_moneda"]);}
	function origen_aml($v = false){ if($v !== false){$this->mCampos["origen_aml"]["V"] =  $v; } return new MQLCampo($this->mCampos["origen_aml"]);}
	function archivo_fisico($v = false){ if($v !== false){$this->mCampos["archivo_fisico"]["V"] =  $v; } return new MQLCampo($this->mCampos["archivo_fisico"]);}
	function persona_asociada($v = false){ if($v !== false){$this->mCampos["persona_asociada"]["V"] =  $v; } return new MQLCampo($this->mCampos["persona_asociada"]);}
	function fecha_de_registro($v = false){ if($v !== false){$this->mCampos["fecha_de_registro"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_registro"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}


/*	ORM: Tabla:	operaciones_recibostipo	-	Generado:	[14/8/2014 14:45]	*/
class cOperaciones_recibostipo {
	private $mCampos	= array(
			"idoperaciones_recibostipo" => array("N"=>"idoperaciones_recibostipo","T"=>"INT","V"=>"0","L"=>4),
			"descripcion_recibostipo" => array("N"=>"descripcion_recibostipo","T"=>"VARCHAR","V"=>"","L"=>45),
			"detalles_del_concepto" => array("N"=>"detalles_del_concepto","T"=>"VARCHAR","V"=>"","L"=>100),
			"subclasificacion" => array("N"=>"subclasificacion","T"=>"FLOAT","V"=>"","L"=>0),
			"nombre_sublasificacion" => array("N"=>"nombre_sublasificacion","T"=>"VARCHAR","V"=>"","L"=>50),
			"mostrar_en_corte" => array("N"=>"mostrar_en_corte","T"=>"ENUM","V"=>"|1|0|","L"=>0),
			"tipo_poliza_generada" => array("N"=>"tipo_poliza_generada","T"=>"INT","V"=>"","L"=>4),
			"afectacion_en_flujo_efvo" => array("N"=>"afectacion_en_flujo_efvo","T"=>"ENUM","V"=>"|aumento|disminucion|ninguna|","L"=>0),
			"path_formato" => array("N"=>"path_formato","T"=>"VARCHAR","V"=>"","L"=>100),
			"origen" => array("N"=>"origen","T"=>"ENUM","V"=>"|colocacion|captacion|otros|mixto|","L"=>0),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "operaciones_recibostipo";}
	function getKey(){ return "idoperaciones_recibostipo";}
	function idoperaciones_recibostipo($v = false){ if($v !== false){$this->mCampos["idoperaciones_recibostipo"]["V"] =  $v; } return new MQLCampo($this->mCampos["idoperaciones_recibostipo"]);}
	function descripcion_recibostipo($v = false){ if($v !== false){$this->mCampos["descripcion_recibostipo"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_recibostipo"]);}
	function detalles_del_concepto($v = false){ if($v !== false){$this->mCampos["detalles_del_concepto"]["V"] =  $v; } return new MQLCampo($this->mCampos["detalles_del_concepto"]);}
	function subclasificacion($v = false){ if($v !== false){$this->mCampos["subclasificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["subclasificacion"]);}
	function nombre_sublasificacion($v = false){ if($v !== false){$this->mCampos["nombre_sublasificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_sublasificacion"]);}
	function mostrar_en_corte($v = false){ if($v !== false){$this->mCampos["mostrar_en_corte"]["V"] =  $v; } return new MQLCampo($this->mCampos["mostrar_en_corte"]);}
	function tipo_poliza_generada($v = false){ if($v !== false){$this->mCampos["tipo_poliza_generada"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_poliza_generada"]);}
	function afectacion_en_flujo_efvo($v = false){ if($v !== false){$this->mCampos["afectacion_en_flujo_efvo"]["V"] =  $v; } return new MQLCampo($this->mCampos["afectacion_en_flujo_efvo"]);}
	function path_formato($v = false){ if($v !== false){$this->mCampos["path_formato"]["V"] =  $v; } return new MQLCampo($this->mCampos["path_formato"]);}
	function origen($v = false){ if($v !== false){$this->mCampos["origen"]["V"] =  $v; } return new MQLCampo($this->mCampos["origen"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	general_structure	-	Generado:	[26/12/2013 10:20]	*/
class cGeneral_structure {
	private $mCampos	= array(
		"index_struct" => array("N"=>"index_struct","T"=>"INT","V"=>"","L"=>11),
		"tabla" => array("N"=>"tabla","T"=>"VARCHAR","V"=>"","L"=>100),
		"campo" => array("N"=>"campo","T"=>"VARCHAR","V"=>"","L"=>100),
		"valor" => array("N"=>"valor","T"=>"VARCHAR","V"=>"","L"=>250),
		"tipo" => array("N"=>"tipo","T"=>"VARCHAR","V"=>"","L"=>20),
		"longitud" => array("N"=>"longitud","T"=>"INT","V"=>"0","L"=>4),
		"descripcion" => array("N"=>"descripcion","T"=>"VARCHAR","V"=>"","L"=>200),
		"titulo" => array("N"=>"titulo","T"=>"VARCHAR","V"=>"","L"=>100),
		"control" => array("N"=>"control","T"=>"ENUM","V"=>"|text|textarea|select|hidden|","L"=>0),
		"sql_select" => array("N"=>"sql_select","T"=>"TINYTEXT","V"=>"","L"=>0),
		"orientacion" => array("N"=>"orientacion","T"=>"ENUM","V"=>"|izquierda|derecha|","L"=>0),
		"order_index" => array("N"=>"order_index","T"=>"INT","V"=>"0","L"=>10),
		"script_field" => array("N"=>"script_field","T"=>"TEXT","V"=>"","L"=>0),
		"help_text" => array("N"=>"help_text","T"=>"TEXT","V"=>"","L"=>0),
		"tab_num" => array("N"=>"tab_num","T"=>"VARCHAR","V"=>"","L"=>25),
		"css_class" => array("N"=>"css_class","T"=>"VARCHAR","V"=>"normalfield","L"=>20),
		"input_events" => array("N"=>"input_events","T"=>"VARCHAR","V"=>"","L"=>120),

	);
	function __construct(){}
	function get(){ return "general_structure";}
	function getKey(){ return "index_struct";}
	function index_struct($v=false){
 		if($v!==false){$this->mCampos["index_struct"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["index_struct"]);
	}
	function tabla($v=false){
 		if($v!==false){$this->mCampos["tabla"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["tabla"]);
	}
	function campo($v=false){
 		if($v!==false){$this->mCampos["campo"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["campo"]);
	}
	function valor($v=false){
 		if($v!==false){$this->mCampos["valor"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["valor"]);
	}
	function tipo($v=false){
 		if($v!==false){$this->mCampos["tipo"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["tipo"]);
	}
	function longitud($v=false){
 		if($v!==false){$this->mCampos["longitud"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["longitud"]);
	}
	function descripcion($v=false){
 		if($v!==false){$this->mCampos["descripcion"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["descripcion"]);
	}
	function titulo($v=false){
 		if($v!==false){$this->mCampos["titulo"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["titulo"]);
	}
	function control($v=false){
 		if($v!==false){$this->mCampos["control"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["control"]);
	}
	function sql_select($v=false){
 		if($v!==false){$this->mCampos["sql_select"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["sql_select"]);
	}
	function orientacion($v=false){
 		if($v!==false){$this->mCampos["orientacion"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["orientacion"]);
	}
	function order_index($v=false){
 		if($v!==false){$this->mCampos["order_index"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["order_index"]);
	}
	function script_field($v=false){
 		if($v!==false){$this->mCampos["script_field"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["script_field"]);
	}
	function help_text($v=false){
 		if($v!==false){$this->mCampos["help_text"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["help_text"]);
	}
	function tab_num($v=false){
 		if($v!==false){$this->mCampos["tab_num"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["tab_num"]);
	}
	function css_class($v=false){
 		if($v!==false){$this->mCampos["css_class"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["css_class"]);
	}
	function input_events($v=false){
 		if($v!==false){$this->mCampos["input_events"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["input_events"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}

/*	ORM: Tabla:	bancos_cuentas	-	Generado:	[17/9/2014 10:36]	*/
class cBancos_cuentas {
	private $mCampos	= array(
			"idbancos_cuentas" => array("N"=>"idbancos_cuentas","T"=>"BIGINT","V"=>"","L"=>20),
			"descripcion_cuenta" => array("N"=>"descripcion_cuenta","T"=>"VARCHAR","V"=>"","L"=>45),
			"fecha_de_apertura" => array("N"=>"fecha_de_apertura","T"=>"DATE","V"=>"","L"=>0),
			"estatus_actual" => array("N"=>"estatus_actual","T"=>"ENUM","V"=>"|activo|baja|","L"=>0),
			"consecutivo_actual" => array("N"=>"consecutivo_actual","T"=>"VARCHAR","V"=>"","L"=>15),
			"saldo_actual" => array("N"=>"saldo_actual","T"=>"FLOAT","V"=>"","L"=>25),
			"eacp" => array("N"=>"eacp","T"=>"VARCHAR","V"=>"EN_TRAMITE","L"=>15),
			"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"todas","L"=>20),
			"codigo_contable" => array("N"=>"codigo_contable","T"=>"VARCHAR","V"=>"00000000000000","L"=>20),
			"entidad_bancaria" => array("N"=>"entidad_bancaria","T"=>"INT","V"=>"0","L"=>10),
			"tipo_de_cuenta" => array("N"=>"tipo_de_cuenta","T"=>"ENUM","V"=>"|cheques|inversion|","L"=>0),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "bancos_cuentas";}
	function getKey(){ return "idbancos_cuentas";}
	function idbancos_cuentas($v = false){ if($v !== false){$this->mCampos["idbancos_cuentas"]["V"] =  $v; } return new MQLCampo($this->mCampos["idbancos_cuentas"]);}
	function descripcion_cuenta($v = false){ if($v !== false){$this->mCampos["descripcion_cuenta"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_cuenta"]);}
	function fecha_de_apertura($v = false){ if($v !== false){$this->mCampos["fecha_de_apertura"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_apertura"]);}
	function estatus_actual($v = false){ if($v !== false){$this->mCampos["estatus_actual"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus_actual"]);}
	function consecutivo_actual($v = false){ if($v !== false){$this->mCampos["consecutivo_actual"]["V"] =  $v; } return new MQLCampo($this->mCampos["consecutivo_actual"]);}
	function saldo_actual($v = false){ if($v !== false){$this->mCampos["saldo_actual"]["V"] =  $v; } return new MQLCampo($this->mCampos["saldo_actual"]);}
	function eacp($v = false){ if($v !== false){$this->mCampos["eacp"]["V"] =  $v; } return new MQLCampo($this->mCampos["eacp"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function codigo_contable($v = false){ if($v !== false){$this->mCampos["codigo_contable"]["V"] =  $v; } return new MQLCampo($this->mCampos["codigo_contable"]);}
	function entidad_bancaria($v = false){ if($v !== false){$this->mCampos["entidad_bancaria"]["V"] =  $v; } return new MQLCampo($this->mCampos["entidad_bancaria"]);}
	function tipo_de_cuenta($v = false){ if($v !== false){$this->mCampos["tipo_de_cuenta"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_cuenta"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}



/*	ORM: Tabla:	bancos_operaciones	-	Generado:	[15/9/2014 16:32]	*/
class cBancos_operaciones {
	private $mCampos	= array(
			"idcontrol" => array("N"=>"idcontrol","T"=>"INT","V"=>"","L"=>10),
			"tipo_operacion" => array("N"=>"tipo_operacion","T"=>"ENUM","V"=>"|cheque|deposito|comision|retiro|traspaso|","L"=>0),
			"numero_de_documento" => array("N"=>"numero_de_documento","T"=>"VARCHAR","V"=>"","L"=>20),
			"cuenta_bancaria" => array("N"=>"cuenta_bancaria","T"=>"BIGINT","V"=>"0","L"=>20),
			"recibo_relacionado" => array("N"=>"recibo_relacionado","T"=>"BIGINT","V"=>"0","L"=>25),
			"fecha_expedicion" => array("N"=>"fecha_expedicion","T"=>"DATE","V"=>"2014-01-01","L"=>0),
			"beneficiario" => array("N"=>"beneficiario","T"=>"VARCHAR","V"=>"","L"=>80),
			"monto_descontado" => array("N"=>"monto_descontado","T"=>"FLOAT","V"=>"0.00","L"=>25),
			"monto_real" => array("N"=>"monto_real","T"=>"FLOAT","V"=>"0.00","L"=>25),
			"estatus" => array("N"=>"estatus","T"=>"ENUM","V"=>"|autorizado|noautorizado|cancelado|","L"=>0),
			"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"","L"=>4),
			"usuario_autorizo" => array("N"=>"usuario_autorizo","T"=>"INT","V"=>"0","L"=>4),
			"eacp" => array("N"=>"eacp","T"=>"VARCHAR","V"=>"EN_TRAMITE","L"=>15),
			"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"matriz","L"=>20),
			"numero_de_socio" => array("N"=>"numero_de_socio","T"=>"BIGINT","V"=>"0","L"=>20),
			"clave_de_conciliacion" => array("N"=>"clave_de_conciliacion","T"=>"VARCHAR","V"=>"","L"=>20),
			"clave_de_moneda" => array("N"=>"clave_de_moneda","T"=>"VARCHAR","V"=>"MXN","L"=>4),
			"tipo_de_exhibicion" => array("N"=>"tipo_de_exhibicion","T"=>"VARCHAR","V"=>"efectivo","L"=>10),
			"cuenta_de_origen" => array("N"=>"cuenta_de_origen","T"=>"BIGINT","V"=>"0","L"=>20)
	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "bancos_operaciones";}
	function getKey(){ return "idcontrol";}
	function idcontrol($v = false){ if($v !== false){$this->mCampos["idcontrol"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcontrol"]);}
	function tipo_operacion($v = false){ if($v !== false){$this->mCampos["tipo_operacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_operacion"]);}
	function numero_de_documento($v = false){ if($v !== false){$this->mCampos["numero_de_documento"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_de_documento"]);}
	function cuenta_bancaria($v = false){ if($v !== false){$this->mCampos["cuenta_bancaria"]["V"] =  $v; } return new MQLCampo($this->mCampos["cuenta_bancaria"]);}
	function recibo_relacionado($v = false){ if($v !== false){$this->mCampos["recibo_relacionado"]["V"] =  $v; } return new MQLCampo($this->mCampos["recibo_relacionado"]);}
	function fecha_expedicion($v = false){ if($v !== false){$this->mCampos["fecha_expedicion"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_expedicion"]);}
	function beneficiario($v = false){ if($v !== false){$this->mCampos["beneficiario"]["V"] =  $v; } return new MQLCampo($this->mCampos["beneficiario"]);}
	function monto_descontado($v = false){ if($v !== false){$this->mCampos["monto_descontado"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_descontado"]);}
	function monto_real($v = false){ if($v !== false){$this->mCampos["monto_real"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_real"]);}
	function estatus($v = false){ if($v !== false){$this->mCampos["estatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function usuario_autorizo($v = false){ if($v !== false){$this->mCampos["usuario_autorizo"]["V"] =  $v; } return new MQLCampo($this->mCampos["usuario_autorizo"]);}
	function eacp($v = false){ if($v !== false){$this->mCampos["eacp"]["V"] =  $v; } return new MQLCampo($this->mCampos["eacp"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function numero_de_socio($v = false){ if($v !== false){$this->mCampos["numero_de_socio"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_de_socio"]);}
	function clave_de_conciliacion($v = false){ if($v !== false){$this->mCampos["clave_de_conciliacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_conciliacion"]);}
	function clave_de_moneda($v = false){ if($v !== false){$this->mCampos["clave_de_moneda"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_moneda"]);}
	function tipo_de_exhibicion($v = false){ if($v !== false){$this->mCampos["tipo_de_exhibicion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_exhibicion"]);}
	function cuenta_de_origen($v = false){ if($v !== false){$this->mCampos["cuenta_de_origen"]["V"] =  $v; } return new MQLCampo($this->mCampos["cuenta_de_origen"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	tesoreria_cajas	-	Generado:	[23/10/2014 10:31]	*/
class cTesoreria_cajas {
	private $mCampos	= array(
			"idtesoreria_cajas" => array("N"=>"idtesoreria_cajas","T"=>"VARCHAR","V"=>"","L"=>100),
			"eacp" => array("N"=>"eacp","T"=>"VARCHAR","V"=>"","L"=>20),
			"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"matriz","L"=>20),
			"maquina" => array("N"=>"maquina","T"=>"VARCHAR","V"=>"","L"=>45),
			"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"0","L"=>10),
			"fecha_inicio" => array("N"=>"fecha_inicio","T"=>"DATE","V"=>"","L"=>0),
			"hora_inicio" => array("N"=>"hora_inicio","T"=>"TIME","V"=>"","L"=>0),
			"estatus" => array("N"=>"estatus","T"=>"VARCHAR","V"=>"","L"=>20),
			"usuario_que_autoriza" => array("N"=>"usuario_que_autoriza","T"=>"INT","V"=>"0","L"=>10),
			"firma_digital" => array("N"=>"firma_digital","T"=>"TEXT","V"=>"","L"=>0),
			"fondos_iniciales" => array("N"=>"fondos_iniciales","T"=>"FLOAT","V"=>"0.000","L"=>25),
			"fondos_arqueados" => array("N"=>"fondos_arqueados","T"=>"FLOAT","V"=>"0.0000","L"=>33),
			"total_cobrado" => array("N"=>"total_cobrado","T"=>"FLOAT","V"=>"0.0000","L"=>33),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "tesoreria_cajas";}
	function getKey(){ return "idtesoreria_cajas";}
	function idtesoreria_cajas($v = false){ if($v !== false){$this->mCampos["idtesoreria_cajas"]["V"] =  $v; } return new MQLCampo($this->mCampos["idtesoreria_cajas"]);}
	function eacp($v = false){ if($v !== false){$this->mCampos["eacp"]["V"] =  $v; } return new MQLCampo($this->mCampos["eacp"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function maquina($v = false){ if($v !== false){$this->mCampos["maquina"]["V"] =  $v; } return new MQLCampo($this->mCampos["maquina"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function fecha_inicio($v = false){ if($v !== false){$this->mCampos["fecha_inicio"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_inicio"]);}
	function hora_inicio($v = false){ if($v !== false){$this->mCampos["hora_inicio"]["V"] =  $v; } return new MQLCampo($this->mCampos["hora_inicio"]);}
	function estatus($v = false){ if($v !== false){$this->mCampos["estatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus"]);}
	function usuario_que_autoriza($v = false){ if($v !== false){$this->mCampos["usuario_que_autoriza"]["V"] =  $v; } return new MQLCampo($this->mCampos["usuario_que_autoriza"]);}
	function firma_digital($v = false){ if($v !== false){$this->mCampos["firma_digital"]["V"] =  $v; } return new MQLCampo($this->mCampos["firma_digital"]);}
	function fondos_iniciales($v = false){ if($v !== false){$this->mCampos["fondos_iniciales"]["V"] =  $v; } return new MQLCampo($this->mCampos["fondos_iniciales"]);}
	function fondos_arqueados($v = false){ if($v !== false){$this->mCampos["fondos_arqueados"]["V"] =  $v; } return new MQLCampo($this->mCampos["fondos_arqueados"]);}
	function total_cobrado($v = false){ if($v !== false){$this->mCampos["total_cobrado"]["V"] =  $v; } return new MQLCampo($this->mCampos["total_cobrado"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	tesoreria_cajas_movimientos	-	Generado:	[02/5/2014 14:18]	*/
class cTesoreria_cajas_movimientos {
	private $mCampos	= array(
			"idtesoreria_cajas_movimientos" => array("N"=>"idtesoreria_cajas_movimientos","T"=>"INT","V"=>"","L"=>10),
			"codigo_de_caja" => array("N"=>"codigo_de_caja","T"=>"VARCHAR","V"=>"","L"=>100),
			"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"","L"=>10),
			"documento" => array("N"=>"documento","T"=>"BIGINT","V"=>"","L"=>20),
			"recibo" => array("N"=>"recibo","T"=>"BIGINT","V"=>"","L"=>25),
			"tipo_de_movimiento" => array("N"=>"tipo_de_movimiento","T"=>"INT","V"=>"","L"=>4),
			"tipo_de_exposicion" => array("N"=>"tipo_de_exposicion","T"=>"VARCHAR","V"=>"","L"=>25),
			"fecha" => array("N"=>"fecha","T"=>"DATE","V"=>"","L"=>0),
			"hora" => array("N"=>"hora","T"=>"TIME","V"=>"","L"=>0),
			"monto_del_movimiento" => array("N"=>"monto_del_movimiento","T"=>"FLOAT","V"=>"","L"=>25),
			"monto_recibido" => array("N"=>"monto_recibido","T"=>"FLOAT","V"=>"","L"=>25),
			"monto_en_cambio" => array("N"=>"monto_en_cambio","T"=>"FLOAT","V"=>"","L"=>25),
			"banco" => array("N"=>"banco","T"=>"INT","V"=>"1","L"=>4),
			"numero_de_cheque" => array("N"=>"numero_de_cheque","T"=>"VARCHAR","V"=>"","L"=>20),
			"observaciones" => array("N"=>"observaciones","T"=>"VARCHAR","V"=>"","L"=>100),
			"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"","L"=>20),
			"eacp" => array("N"=>"eacp","T"=>"VARCHAR","V"=>"","L"=>20),
			"cuenta_bancaria" => array("N"=>"cuenta_bancaria","T"=>"BIGINT","V"=>"0","L"=>20),
			"documento_descontado" => array("N"=>"documento_descontado","T"=>"BIGINT","V"=>"0","L"=>20),
			"moneda_de_operacion" => array("N"=>"moneda_de_operacion","T"=>"VARCHAR","V"=>"MXN","L"=>10),
			"unidades_de_moneda" => array("N"=>"unidades_de_moneda","T"=>"FLOAT","V"=>"0.0000","L"=>33),
			"persona" => array("N"=>"persona","T"=>"BIGINT","V"=>"1","L"=>20),

	);
	function __construct(){}
	function get(){ return "tesoreria_cajas_movimientos";}
	function getKey(){ return "idtesoreria_cajas_movimientos";}
	function idtesoreria_cajas_movimientos($v=false){
		if($v!==false){$this->mCampos["idtesoreria_cajas_movimientos"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["idtesoreria_cajas_movimientos"]);
	}
	function codigo_de_caja($v=false){
		if($v!==false){$this->mCampos["codigo_de_caja"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["codigo_de_caja"]);
	}
	function idusuario($v=false){
		if($v!==false){$this->mCampos["idusuario"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["idusuario"]);
	}
	function documento($v=false){
		if($v!==false){$this->mCampos["documento"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["documento"]);
	}
	function recibo($v=false){
		if($v!==false){$this->mCampos["recibo"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["recibo"]);
	}
	function tipo_de_movimiento($v=false){
		if($v!==false){$this->mCampos["tipo_de_movimiento"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["tipo_de_movimiento"]);
	}
	function tipo_de_exposicion($v=false){
		if($v!==false){$this->mCampos["tipo_de_exposicion"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["tipo_de_exposicion"]);
	}
	function fecha($v=false){
		if($v!==false){$this->mCampos["fecha"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["fecha"]);
	}
	function hora($v=false){
		if($v!==false){$this->mCampos["hora"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["hora"]);
	}
	function monto_del_movimiento($v=false){
		if($v!==false){$this->mCampos["monto_del_movimiento"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["monto_del_movimiento"]);
	}
	function monto_recibido($v=false){
		if($v!==false){$this->mCampos["monto_recibido"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["monto_recibido"]);
	}
	function monto_en_cambio($v=false){
		if($v!==false){$this->mCampos["monto_en_cambio"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["monto_en_cambio"]);
	}
	function banco($v=false){
		if($v!==false){$this->mCampos["banco"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["banco"]);
	}
	function numero_de_cheque($v=false){
		if($v!==false){$this->mCampos["numero_de_cheque"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["numero_de_cheque"]);
	}
	function observaciones($v=false){
		if($v!==false){$this->mCampos["observaciones"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["observaciones"]);
	}
	function sucursal($v=false){
		if($v!==false){$this->mCampos["sucursal"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["sucursal"]);
	}
	function eacp($v=false){
		if($v!==false){$this->mCampos["eacp"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["eacp"]);
	}
	function cuenta_bancaria($v=false){
		if($v!==false){$this->mCampos["cuenta_bancaria"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["cuenta_bancaria"]);
	}
	function documento_descontado($v=false){
		if($v!==false){$this->mCampos["documento_descontado"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["documento_descontado"]);
	}
	function moneda_de_operacion($v=false){
		if($v!==false){$this->mCampos["moneda_de_operacion"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["moneda_de_operacion"]);
	}
	function unidades_de_moneda($v=false){
		if($v!==false){$this->mCampos["unidades_de_moneda"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["unidades_de_moneda"]);
	}
	function persona($v=false){
		if($v!==false){$this->mCampos["persona"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["persona"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}

/*	ORM: Tabla:	tesoreria_tipos_de_pago	-	Generado:	[19/1/2015 15:30]	*/
class cTesoreria_tipos_de_pago {
	private $mCampos	= array(
			"tipo_de_pago" => array("N"=>"tipo_de_pago","T"=>"VARCHAR","V"=>"","L"=>25),
			"tipo_de_movimiento" => array("N"=>"tipo_de_movimiento","T"=>"INT","V"=>"","L"=>4),
			"descripcion" => array("N"=>"descripcion","T"=>"VARCHAR","V"=>"","L"=>45),
			"descripcion_completa" => array("N"=>"descripcion_completa","T"=>"VARCHAR","V"=>"","L"=>150),
			"equivalente_aml" => array("N"=>"equivalente_aml","T"=>"INT","V"=>"0","L"=>5),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "tesoreria_tipos_de_pago";}
	function getKey(){ return "tipo_de_pago";}
	function tipo_de_pago($v = false){ if($v !== false){$this->mCampos["tipo_de_pago"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_pago"]);}
	function tipo_de_movimiento($v = false){ if($v !== false){$this->mCampos["tipo_de_movimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_movimiento"]);}
	function descripcion($v = false){ if($v !== false){$this->mCampos["descripcion"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion"]);}
	function descripcion_completa($v = false){ if($v !== false){$this->mCampos["descripcion_completa"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_completa"]);}
	function equivalente_aml($v = false){ if($v !== false){$this->mCampos["equivalente_aml"]["V"] =  $v; } return new MQLCampo($this->mCampos["equivalente_aml"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}


/*	ORM: Tabla:	tesoreria_monedas	-	Generado:	[08/3/2014 10:49]	*/
class cTesoreria_monedas {
	private $mCampos	= array(
			"clave_de_moneda" => array("N"=>"clave_de_moneda","T"=>"VARCHAR","V"=>"","L"=>10),
			"nombre_de_la_moneda" => array("N"=>"nombre_de_la_moneda","T"=>"VARCHAR","V"=>"","L"=>100),
			"quivalencia_en_moneda_local" => array("N"=>"quivalencia_en_moneda_local","T"=>"FLOAT","V"=>"0.0000","L"=>33),
			"pais_de_origen" => array("N"=>"pais_de_origen","T"=>"VARCHAR","V"=>"MX","L"=>10),

	);
	function __construct(){}
	function get(){ return "tesoreria_monedas";}
	function getKey(){ return "clave_de_moneda";}
	function clave_de_moneda($v=false){
		if($v!==false){$this->mCampos["clave_de_moneda"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["clave_de_moneda"]);
	}
	function nombre_de_la_moneda($v=false){
		if($v!==false){$this->mCampos["nombre_de_la_moneda"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["nombre_de_la_moneda"]);
	}
	function quivalencia_en_moneda_local($v=false){
		if($v!==false){$this->mCampos["quivalencia_en_moneda_local"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["quivalencia_en_moneda_local"]);
	}
	function pais_de_origen($v=false){
		if($v!==false){$this->mCampos["pais_de_origen"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["pais_de_origen"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}

/*	ORM: Tabla:	tesoreria_tipos_de_denominaciones	-	Generado:	[09/9/2014 12:05]	*/
class cTesoreria_tipos_de_denominaciones {
	private $mCampos	= array(
			"denominacion" => array("N"=>"denominacion","T"=>"INT","V"=>"","L"=>11),
			"nombre" => array("N"=>"nombre","T"=>"VARCHAR","V"=>"","L"=>45),
			"valor_contra_uno" => array("N"=>"valor_contra_uno","T"=>"FLOAT","V"=>"","L"=>25),
			"tipo_de_valor" => array("N"=>"tipo_de_valor","T"=>"VARCHAR","V"=>"","L"=>20),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "tesoreria_tipos_de_denominaciones";}
	function getKey(){ return "denominacion";}
	function denominacion($v = false){ if($v !== false){$this->mCampos["denominacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["denominacion"]);}
	function nombre($v = false){ if($v !== false){$this->mCampos["nombre"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre"]);}
	function valor_contra_uno($v = false){ if($v !== false){$this->mCampos["valor_contra_uno"]["V"] =  $v; } return new MQLCampo($this->mCampos["valor_contra_uno"]);}
	function tipo_de_valor($v = false){ if($v !== false){$this->mCampos["tipo_de_valor"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_valor"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	tesoreria_caja_arqueos	-	Generado:	[09/9/2014 12:07]	*/
class cTesoreria_caja_arqueos {
	private $mCampos	= array(
			"codigo_de_arqueo" => array("N"=>"codigo_de_arqueo","T"=>"INT","V"=>"","L"=>11),
			"codigo_de_caja" => array("N"=>"codigo_de_caja","T"=>"VARCHAR","V"=>"","L"=>100),
			"fecha_de_arqueo" => array("N"=>"fecha_de_arqueo","T"=>"DATE","V"=>"","L"=>0),
			"valor_arqueado" => array("N"=>"valor_arqueado","T"=>"FLOAT","V"=>"0.000","L"=>33),
			"numero_arqueado" => array("N"=>"numero_arqueado","T"=>"FLOAT","V"=>"0.000","L"=>33),
			"monto_total_arqueado" => array("N"=>"monto_total_arqueado","T"=>"FLOAT","V"=>"0.000","L"=>33),
			"hora_de_arqueo" => array("N"=>"hora_de_arqueo","T"=>"BIGINT","V"=>"","L"=>20),
			"documento" => array("N"=>"documento","T"=>"VARCHAR","V"=>"","L"=>40),
			"observaciones" => array("N"=>"observaciones","T"=>"VARCHAR","V"=>"","L"=>100),
			"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"99","L"=>11),
			"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"matriz","L"=>20),
			"eacp" => array("N"=>"eacp","T"=>"VARCHAR","V"=>"","L"=>20),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "tesoreria_caja_arqueos";}
	function getKey(){ return "codigo_de_arqueo";}
	function codigo_de_arqueo($v = false){ if($v !== false){$this->mCampos["codigo_de_arqueo"]["V"] =  $v; } return new MQLCampo($this->mCampos["codigo_de_arqueo"]);}
	function codigo_de_caja($v = false){ if($v !== false){$this->mCampos["codigo_de_caja"]["V"] =  $v; } return new MQLCampo($this->mCampos["codigo_de_caja"]);}
	function fecha_de_arqueo($v = false){ if($v !== false){$this->mCampos["fecha_de_arqueo"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_arqueo"]);}
	function valor_arqueado($v = false){ if($v !== false){$this->mCampos["valor_arqueado"]["V"] =  $v; } return new MQLCampo($this->mCampos["valor_arqueado"]);}
	function numero_arqueado($v = false){ if($v !== false){$this->mCampos["numero_arqueado"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_arqueado"]);}
	function monto_total_arqueado($v = false){ if($v !== false){$this->mCampos["monto_total_arqueado"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_total_arqueado"]);}
	function hora_de_arqueo($v = false){ if($v !== false){$this->mCampos["hora_de_arqueo"]["V"] =  $v; } return new MQLCampo($this->mCampos["hora_de_arqueo"]);}
	function documento($v = false){ if($v !== false){$this->mCampos["documento"]["V"] =  $v; } return new MQLCampo($this->mCampos["documento"]);}
	function observaciones($v = false){ if($v !== false){$this->mCampos["observaciones"]["V"] =  $v; } return new MQLCampo($this->mCampos["observaciones"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function eacp($v = false){ if($v !== false){$this->mCampos["eacp"]["V"] =  $v; } return new MQLCampo($this->mCampos["eacp"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}




/*	ORM: Tabla:	general_menu	-	Generado:	[21/1/2014 11:20]	*/
class cGeneral_menu {
	private $mCampos	= array(
			"idgeneral_menu" => array("N"=>"idgeneral_menu","T"=>"INT","V"=>"","L"=>10),
			"menu_parent" => array("N"=>"menu_parent","T"=>"INT","V"=>"9999","L"=>4),
			"menu_title" => array("N"=>"menu_title","T"=>"VARCHAR","V"=>"NO_TITLE","L"=>45),
			"menu_file" => array("N"=>"menu_file","T"=>"VARCHAR","V"=>"404.php","L"=>100),
			"menu_destination" => array("N"=>"menu_destination","T"=>"VARCHAR","V"=>"principal","L"=>45),
			"menu_description" => array("N"=>"menu_description","T"=>"VARCHAR","V"=>"NO_DESCRIPTION","L"=>150),
			"menu_image" => array("N"=>"menu_image","T"=>"VARCHAR","V"=>"null.png","L"=>45),
			"menu_rules" => array("N"=>"menu_rules","T"=>"VARCHAR","V"=>"99@ro,15@ro,14@ro,15@ro,14@ro,13@ro,12@ro,11@ro,10@ro,9@ro,8@ro,7@ro,6@ro,5@ro,4@ro,3@ro,2@ro","L"=>200),
			"menu_type" => array("N"=>"menu_type","T"=>"ENUM","V"=>"|general|command|parent|","L"=>0),
			"menu_order" => array("N"=>"menu_order","T"=>"INT","V"=>"0","L"=>5),
			"menu_help_id" => array("N"=>"menu_help_id","T"=>"INT","V"=>"9999","L"=>6),
			"menu_showin_toolbar" => array("N"=>"menu_showin_toolbar","T"=>"ENUM","V"=>"|false|true|","L"=>0),

	);
	function __construct(){}
	function get(){ return "general_menu";}
	function getKey(){ return "idgeneral_menu";}
	function idgeneral_menu($v=false){
		if($v!==false){$this->mCampos["idgeneral_menu"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["idgeneral_menu"]);
	}
	function menu_parent($v=false){
		if($v!==false){$this->mCampos["menu_parent"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["menu_parent"]);
	}
	function menu_title($v=false){
		if($v!==false){$this->mCampos["menu_title"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["menu_title"]);
	}
	function menu_file($v=false){
		if($v!==false){$this->mCampos["menu_file"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["menu_file"]);
	}
	function menu_destination($v=false){
		if($v!==false){$this->mCampos["menu_destination"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["menu_destination"]);
	}
	function menu_description($v=false){
		if($v!==false){$this->mCampos["menu_description"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["menu_description"]);
	}
	function menu_image($v=false){
		if($v!==false){$this->mCampos["menu_image"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["menu_image"]);
	}
	function menu_rules($v=false){
		if($v!==false){$this->mCampos["menu_rules"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["menu_rules"]);
	}
	function menu_type($v=false){
		if($v!==false){$this->mCampos["menu_type"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["menu_type"]);
	}
	function menu_order($v=false){
		if($v!==false){$this->mCampos["menu_order"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["menu_order"]);
	}
	function menu_help_id($v=false){
		if($v!==false){$this->mCampos["menu_help_id"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["menu_help_id"]);
	}
	function menu_showin_toolbar($v=false){
		if($v!==false){$this->mCampos["menu_showin_toolbar"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["menu_showin_toolbar"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}

/*	ORM: Tabla:	aml_alerts	-	Generado:	[15/12/2014 14:11]	*/
class cAml_alerts {
	private $mCampos	= array(
		"clave_de_control" => array("N"=>"clave_de_control","T"=>"INT","V"=>"","L"=>11),
		"tipo_de_aviso" => array("N"=>"tipo_de_aviso","T"=>"INT","V"=>"1","L"=>11),
		"persona_de_destino" => array("N"=>"persona_de_destino","T"=>"BIGINT","V"=>"1","L"=>20),
		"documento_relacionado" => array("N"=>"documento_relacionado","T"=>"BIGINT","V"=>"1","L"=>20),
		"persona_de_origen" => array("N"=>"persona_de_origen","T"=>"BIGINT","V"=>"1","L"=>20),
		"fecha_de_origen" => array("N"=>"fecha_de_origen","T"=>"BIGINT","V"=>"0","L"=>20),
		"fecha_de_checking" => array("N"=>"fecha_de_checking","T"=>"BIGINT","V"=>"0","L"=>20),
		"hora_de_proceso" => array("N"=>"hora_de_proceso","T"=>"BIGINT","V"=>"0","L"=>20),
		"medio_de_envio" => array("N"=>"medio_de_envio","T"=>"VARCHAR","V"=>"","L"=>20),
		"estado_en_sistema" => array("N"=>"estado_en_sistema","T"=>"INT","V"=>"1","L"=>11),
		"riesgo_calificado" => array("N"=>"riesgo_calificado","T"=>"INT","V"=>"0","L"=>11),
		"mensaje" => array("N"=>"mensaje","T"=>"MEDIUMTEXT","V"=>"","L"=>0),
		"usuario" => array("N"=>"usuario","T"=>"INT","V"=>"1","L"=>11),
		"sucursal" => array("N"=>"sucursal","T"=>"INT","V"=>"1","L"=>11),
		"entidad" => array("N"=>"entidad","T"=>"INT","V"=>"1","L"=>11),
		"fecha_de_registro" => array("N"=>"fecha_de_registro","T"=>"BIGINT","V"=>"0","L"=>20),
		"notas_de_checking" => array("N"=>"notas_de_checking","T"=>"TEXT","V"=>"","L"=>0),
		"tipo_de_documento" => array("N"=>"tipo_de_documento","T"=>"INT","V"=>"0","L"=>5),
		"tercero_relacionado" => array("N"=>"tercero_relacionado","T"=>"BIGINT","V"=>"1","L"=>20),
		"resultado_de_checking" => array("N"=>"resultado_de_checking","T"=>"INT","V"=>"0","L"=>2),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "aml_alerts";}
	function getKey(){ return "clave_de_control";}
	function clave_de_control($v = false){ if($v !== false){$this->mCampos["clave_de_control"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_control"]);}
	function tipo_de_aviso($v = false){ if($v !== false){$this->mCampos["tipo_de_aviso"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_aviso"]);}
	function persona_de_destino($v = false){ if($v !== false){$this->mCampos["persona_de_destino"]["V"] =  $v; } return new MQLCampo($this->mCampos["persona_de_destino"]);}
	function documento_relacionado($v = false){ if($v !== false){$this->mCampos["documento_relacionado"]["V"] =  $v; } return new MQLCampo($this->mCampos["documento_relacionado"]);}
	function persona_de_origen($v = false){ if($v !== false){$this->mCampos["persona_de_origen"]["V"] =  $v; } return new MQLCampo($this->mCampos["persona_de_origen"]);}
	function fecha_de_origen($v = false){ if($v !== false){$this->mCampos["fecha_de_origen"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_origen"]);}
	function fecha_de_checking($v = false){ if($v !== false){$this->mCampos["fecha_de_checking"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_checking"]);}
	function hora_de_proceso($v = false){ if($v !== false){$this->mCampos["hora_de_proceso"]["V"] =  $v; } return new MQLCampo($this->mCampos["hora_de_proceso"]);}
	function medio_de_envio($v = false){ if($v !== false){$this->mCampos["medio_de_envio"]["V"] =  $v; } return new MQLCampo($this->mCampos["medio_de_envio"]);}
	function estado_en_sistema($v = false){ if($v !== false){$this->mCampos["estado_en_sistema"]["V"] =  $v; } return new MQLCampo($this->mCampos["estado_en_sistema"]);}
	function riesgo_calificado($v = false){ if($v !== false){$this->mCampos["riesgo_calificado"]["V"] =  $v; } return new MQLCampo($this->mCampos["riesgo_calificado"]);}
	function mensaje($v = false){ if($v !== false){$this->mCampos["mensaje"]["V"] =  $v; } return new MQLCampo($this->mCampos["mensaje"]);}
	function usuario($v = false){ if($v !== false){$this->mCampos["usuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["usuario"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function entidad($v = false){ if($v !== false){$this->mCampos["entidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["entidad"]);}
	function fecha_de_registro($v = false){ if($v !== false){$this->mCampos["fecha_de_registro"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_registro"]);}
	function notas_de_checking($v = false){ if($v !== false){$this->mCampos["notas_de_checking"]["V"] =  $v; } return new MQLCampo($this->mCampos["notas_de_checking"]);}
	function tipo_de_documento($v = false){ if($v !== false){$this->mCampos["tipo_de_documento"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_documento"]);}
	function tercero_relacionado($v = false){ if($v !== false){$this->mCampos["tercero_relacionado"]["V"] =  $v; } return new MQLCampo($this->mCampos["tercero_relacionado"]);}
	function resultado_de_checking($v = false){ if($v !== false){$this->mCampos["resultado_de_checking"]["V"] =  $v; } return new MQLCampo($this->mCampos["resultado_de_checking"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
}


/*	ORM: Tabla:	aml_risk_register	-	Generado:	[20/1/2015 17:48]	*/
class cAml_risk_register {
	private $mCampos	= array(
			"clave_de_riesgo" => array("N"=>"clave_de_riesgo","T"=>"INT","V"=>"","L"=>11),
			"tipo_de_riesgo" => array("N"=>"tipo_de_riesgo","T"=>"INT","V"=>"0","L"=>11),
			"persona_relacionada" => array("N"=>"persona_relacionada","T"=>"BIGINT","V"=>"0","L"=>20),
			"fecha_de_reporte" => array("N"=>"fecha_de_reporte","T"=>"BIGINT","V"=>"0","L"=>20),
			"hora_de_reporte" => array("N"=>"hora_de_reporte","T"=>"BIGINT","V"=>"0","L"=>20),
			"escore" => array("N"=>"escore","T"=>"FLOAT","V"=>"0.000000","L"=>37),
			"usuario_de_origen" => array("N"=>"usuario_de_origen","T"=>"INT","V"=>"0","L"=>11),
			"documento_relacionado" => array("N"=>"documento_relacionado","T"=>"BIGINT","V"=>"0","L"=>20),
			"tipo_de_documento" => array("N"=>"tipo_de_documento","T"=>"INT","V"=>"1","L"=>4),
			"instrumento_financiero" => array("N"=>"instrumento_financiero","T"=>"INT","V"=>"","L"=>11),
			"fecha_de_envio" => array("N"=>"fecha_de_envio","T"=>"BIGINT","V"=>"0","L"=>20),
			"estado_de_envio" => array("N"=>"estado_de_envio","T"=>"INT","V"=>"","L"=>11),
			"fecha_de_checking" => array("N"=>"fecha_de_checking","T"=>"BIGINT","V"=>"","L"=>20),
			"oficial_de_checking" => array("N"=>"oficial_de_checking","T"=>"INT","V"=>"","L"=>11),
			"firma_de_checking" => array("N"=>"firma_de_checking","T"=>"TINYTEXT","V"=>"","L"=>0),
			"monto_total_relacionado" => array("N"=>"monto_total_relacionado","T"=>"FLOAT","V"=>"","L"=>37),
			"metadata" => array("N"=>"metadata","T"=>"TEXT","V"=>"","L"=>0),
			"notas_de_checking" => array("N"=>"notas_de_checking","T"=>"VARCHAR","V"=>"","L"=>200),
			"tipo_de_operacion" => array("N"=>"tipo_de_operacion","T"=>"VARCHAR","V"=>"","L"=>4),
			"razones_de_reporte" => array("N"=>"razones_de_reporte","T"=>"TEXT","V"=>"","L"=>0),
			"acciones_tomadas" => array("N"=>"acciones_tomadas","T"=>"TEXT","V"=>"","L"=>0),
			"tercero_relacionado" => array("N"=>"tercero_relacionado","T"=>"BIGINT","V"=>"1","L"=>20),
			"mensajes_del_sistema" => array("N"=>"mensajes_del_sistema","T"=>"MEDIUMTEXT","V"=>"","L"=>0),
			"reporte_inmediato" => array("N"=>"reporte_inmediato","T"=>"INT","V"=>"","L"=>3),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "aml_risk_register";}
	function getKey(){ return "clave_de_riesgo";}
	function clave_de_riesgo($v = false){ if($v !== false){$this->mCampos["clave_de_riesgo"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_riesgo"]);}
	function tipo_de_riesgo($v = false){ if($v !== false){$this->mCampos["tipo_de_riesgo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_riesgo"]);}
	function persona_relacionada($v = false){ if($v !== false){$this->mCampos["persona_relacionada"]["V"] =  $v; } return new MQLCampo($this->mCampos["persona_relacionada"]);}
	function fecha_de_reporte($v = false){ if($v !== false){$this->mCampos["fecha_de_reporte"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_reporte"]);}
	function hora_de_reporte($v = false){ if($v !== false){$this->mCampos["hora_de_reporte"]["V"] =  $v; } return new MQLCampo($this->mCampos["hora_de_reporte"]);}
	function escore($v = false){ if($v !== false){$this->mCampos["escore"]["V"] =  $v; } return new MQLCampo($this->mCampos["escore"]);}
	function usuario_de_origen($v = false){ if($v !== false){$this->mCampos["usuario_de_origen"]["V"] =  $v; } return new MQLCampo($this->mCampos["usuario_de_origen"]);}
	function documento_relacionado($v = false){ if($v !== false){$this->mCampos["documento_relacionado"]["V"] =  $v; } return new MQLCampo($this->mCampos["documento_relacionado"]);}
	function tipo_de_documento($v = false){ if($v !== false){$this->mCampos["tipo_de_documento"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_documento"]);}
	function instrumento_financiero($v = false){ if($v !== false){$this->mCampos["instrumento_financiero"]["V"] =  $v; } return new MQLCampo($this->mCampos["instrumento_financiero"]);}
	function fecha_de_envio($v = false){ if($v !== false){$this->mCampos["fecha_de_envio"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_envio"]);}
	function estado_de_envio($v = false){ if($v !== false){$this->mCampos["estado_de_envio"]["V"] =  $v; } return new MQLCampo($this->mCampos["estado_de_envio"]);}
	function fecha_de_checking($v = false){ if($v !== false){$this->mCampos["fecha_de_checking"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_checking"]);}
	function oficial_de_checking($v = false){ if($v !== false){$this->mCampos["oficial_de_checking"]["V"] =  $v; } return new MQLCampo($this->mCampos["oficial_de_checking"]);}
	function firma_de_checking($v = false){ if($v !== false){$this->mCampos["firma_de_checking"]["V"] =  $v; } return new MQLCampo($this->mCampos["firma_de_checking"]);}
	function monto_total_relacionado($v = false){ if($v !== false){$this->mCampos["monto_total_relacionado"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_total_relacionado"]);}
	function metadata($v = false){ if($v !== false){$this->mCampos["metadata"]["V"] =  $v; } return new MQLCampo($this->mCampos["metadata"]);}
	function notas_de_checking($v = false){ if($v !== false){$this->mCampos["notas_de_checking"]["V"] =  $v; } return new MQLCampo($this->mCampos["notas_de_checking"]);}
	function tipo_de_operacion($v = false){ if($v !== false){$this->mCampos["tipo_de_operacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_operacion"]);}
	function razones_de_reporte($v = false){ if($v !== false){$this->mCampos["razones_de_reporte"]["V"] =  $v; } return new MQLCampo($this->mCampos["razones_de_reporte"]);}
	function acciones_tomadas($v = false){ if($v !== false){$this->mCampos["acciones_tomadas"]["V"] =  $v; } return new MQLCampo($this->mCampos["acciones_tomadas"]);}
	function tercero_relacionado($v = false){ if($v !== false){$this->mCampos["tercero_relacionado"]["V"] =  $v; } return new MQLCampo($this->mCampos["tercero_relacionado"]);}
	function mensajes_del_sistema($v = false){ if($v !== false){$this->mCampos["mensajes_del_sistema"]["V"] =  $v; } return new MQLCampo($this->mCampos["mensajes_del_sistema"]);}
	function reporte_inmediato($v = false){ if($v !== false){$this->mCampos["reporte_inmediato"]["V"] =  $v; } return new MQLCampo($this->mCampos["reporte_inmediato"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	aml_tipos_de_operacion	-	Generado:	[21/1/2015 14:29]	*/
class cAml_tipos_de_operacion {
	private $mCampos	= array(
			"tipo_de_operacion_aml" => array("N"=>"tipo_de_operacion_aml","T"=>"INT","V"=>"","L"=>11),
			"nombre_de_la_operacion" => array("N"=>"nombre_de_la_operacion","T"=>"VARCHAR","V"=>"","L"=>50),
			"descripcion" => array("N"=>"descripcion","T"=>"VARCHAR","V"=>"","L"=>200),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "aml_tipos_de_operacion";}
	function getKey(){ return "tipo_de_operacion_aml";}
	function tipo_de_operacion_aml($v = false){ if($v !== false){$this->mCampos["tipo_de_operacion_aml"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_operacion_aml"]);}
	function nombre_de_la_operacion($v = false){ if($v !== false){$this->mCampos["nombre_de_la_operacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_de_la_operacion"]);}
	function descripcion($v = false){ if($v !== false){$this->mCampos["descripcion"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}


/*	ORM: Tabla:	aml_risk_catalog	-	Generado:	[12/3/2014 11:59]	*/
class cAml_risk_catalog {
	private $mCampos	= array(
			"clave_de_control" => array("N"=>"clave_de_control","T"=>"INT","V"=>"","L"=>11),
			"descripcion" => array("N"=>"descripcion","T"=>"VARCHAR","V"=>"","L"=>150),
			"tipo_de_riesgo" => array("N"=>"tipo_de_riesgo","T"=>"INT","V"=>"1","L"=>11),
			"valor_ponderado" => array("N"=>"valor_ponderado","T"=>"FLOAT","V"=>"0.000000","L"=>37),
			"unidades_ponderadas" => array("N"=>"unidades_ponderadas","T"=>"FLOAT","V"=>"0.0000","L"=>37),
			"unidad_de_medida" => array("N"=>"unidad_de_medida","T"=>"VARCHAR","V"=>"","L"=>10),
			"forma_de_reportar" => array("N"=>"forma_de_reportar","T"=>"VARCHAR","V"=>"C","L"=>4),
			"frecuencia_de_chequeo" => array("N"=>"frecuencia_de_chequeo","T"=>"VARCHAR","V"=>"D","L"=>4),
			"fundamento_legal" => array("N"=>"fundamento_legal","T"=>"TEXT","V"=>"","L"=>0),

	);
	function __construct(){}
	function get(){ return "aml_risk_catalog";}
	function getKey(){ return "clave_de_control";}
	function clave_de_control($v=false){
		if($v!==false){$this->mCampos["clave_de_control"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["clave_de_control"]);
	}
	function descripcion($v=false){
		if($v!==false){$this->mCampos["descripcion"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["descripcion"]);
	}
	function tipo_de_riesgo($v=false){
		if($v!==false){$this->mCampos["tipo_de_riesgo"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["tipo_de_riesgo"]);
	}
	function valor_ponderado($v=false){
		if($v!==false){$this->mCampos["valor_ponderado"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["valor_ponderado"]);
	}
	function unidades_ponderadas($v=false){
		if($v!==false){$this->mCampos["unidades_ponderadas"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["unidades_ponderadas"]);
	}
	function unidad_de_medida($v=false){
		if($v!==false){$this->mCampos["unidad_de_medida"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["unidad_de_medida"]);
	}
	function forma_de_reportar($v=false){
		if($v!==false){$this->mCampos["forma_de_reportar"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["forma_de_reportar"]);
	}
	function frecuencia_de_chequeo($v=false){
		if($v!==false){$this->mCampos["frecuencia_de_chequeo"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["frecuencia_de_chequeo"]);
	}
	function fundamento_legal($v=false){
		if($v!==false){$this->mCampos["fundamento_legal"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["fundamento_legal"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}

/*	ORM: Tabla:	aml_risk_levels	-	Generado:	[07/2/2014 12:25]	*/
class cAml_risk_levels {
	private $mCampos	= array(
			"clave_de_control" => array("N"=>"clave_de_control","T"=>"INT","V"=>"","L"=>11),
			"nombre_del_nivel" => array("N"=>"nombre_del_nivel","T"=>"VARCHAR","V"=>"","L"=>50),

	);
	function __construct(){}
	function get(){ return "aml_risk_levels";}
	function getKey(){ return "clave_de_control";}
	function clave_de_control($v=false){
		if($v!==false){$this->mCampos["clave_de_control"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["clave_de_control"]);
	}
	function nombre_del_nivel($v=false){
		if($v!==false){$this->mCampos["nombre_del_nivel"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["nombre_del_nivel"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}

/*	ORM: Tabla:	aml_risk_types	-	Generado:	[07/2/2014 13:40]	*/
class cAml_risk_types {
	private $mCampos	= array(
			"clave_de_control" => array("N"=>"clave_de_control","T"=>"INT","V"=>"","L"=>11),
			"nombre_del_riesgo" => array("N"=>"nombre_del_riesgo","T"=>"VARCHAR","V"=>"0","L"=>100),
			"valor_ponderado" => array("N"=>"valor_ponderado","T"=>"FLOAT","V"=>"","L"=>37),

	);
	function __construct(){}
	function get(){ return "aml_risk_types";}
	function getKey(){ return "clave_de_control";}
	function clave_de_control($v=false){
		if($v!==false){$this->mCampos["clave_de_control"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["clave_de_control"]);
	}
	function nombre_del_riesgo($v=false){
		if($v!==false){$this->mCampos["nombre_del_riesgo"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["nombre_del_riesgo"]);
	}
	function valor_ponderado($v=false){
		if($v!==false){$this->mCampos["valor_ponderado"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["valor_ponderado"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}


/*	ORM: Tabla:	aml_riesgo_matrices	-	Generado:	[14/11/2014 11:20]	*/
class cAml_riesgo_matrices {
	private $mCampos	= array(
			"idaml_riesgo_matrices" => array("N"=>"idaml_riesgo_matrices","T"=>"INT","V"=>"","L"=>11),
			"nombre_de_la_matriz" => array("N"=>"nombre_de_la_matriz","T"=>"VARCHAR","V"=>"","L"=>40),
			"tipo_de_persona" => array("N"=>"tipo_de_persona","T"=>"INT","V"=>"","L"=>11),
			"pais_de_origen" => array("N"=>"pais_de_origen","T"=>"VARCHAR","V"=>"MX","L"=>4),
			"clave_de_actividad" => array("N"=>"clave_de_actividad","T"=>"BIGINT","V"=>"","L"=>20),
			"producto_nivel_riesgo" => array("N"=>"producto_nivel_riesgo","T"=>"INT","V"=>"","L"=>11),
			"riesgo_resultante" => array("N"=>"riesgo_resultante","T"=>"INT","V"=>"","L"=>11),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "aml_riesgo_matrices";}
	function getKey(){ return "idaml_riesgo_matrices";}
	function idaml_riesgo_matrices($v = false){ if($v !== false){$this->mCampos["idaml_riesgo_matrices"]["V"] =  $v; } return new MQLCampo($this->mCampos["idaml_riesgo_matrices"]);}
	function nombre_de_la_matriz($v = false){ if($v !== false){$this->mCampos["nombre_de_la_matriz"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_de_la_matriz"]);}
	function tipo_de_persona($v = false){ if($v !== false){$this->mCampos["tipo_de_persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_persona"]);}
	function pais_de_origen($v = false){ if($v !== false){$this->mCampos["pais_de_origen"]["V"] =  $v; } return new MQLCampo($this->mCampos["pais_de_origen"]);}
	function clave_de_actividad($v = false){ if($v !== false){$this->mCampos["clave_de_actividad"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_actividad"]);}
	function producto_nivel_riesgo($v = false){ if($v !== false){$this->mCampos["producto_nivel_riesgo"]["V"] =  $v; } return new MQLCampo($this->mCampos["producto_nivel_riesgo"]);}
	function riesgo_resultante($v = false){ if($v !== false){$this->mCampos["riesgo_resultante"]["V"] =  $v; } return new MQLCampo($this->mCampos["riesgo_resultante"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	aml_riesgo_perfiles	-	Generado:	[04/12/2014 10:38]	*/
class cAml_riesgo_perfiles {
	private $mCampos	= array(
			"idaml_riesgo_perfiles" => array("N"=>"idaml_riesgo_perfiles","T"=>"INT","V"=>"","L"=>11),
			"objeto_de_origen" => array("N"=>"objeto_de_origen","T"=>"VARCHAR","V"=>"","L"=>50),
			"campo_de_origen" => array("N"=>"campo_de_origen","T"=>"VARCHAR","V"=>"","L"=>50),
			"valor_de_origen" => array("N"=>"valor_de_origen","T"=>"VARCHAR","V"=>"","L"=>50),
			"nivel_de_riesgo" => array("N"=>"nivel_de_riesgo","T"=>"INT","V"=>"","L"=>4),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "aml_riesgo_perfiles";}
	function getKey(){ return "idaml_riesgo_perfiles";}
	function idaml_riesgo_perfiles($v = false){ if($v !== false){$this->mCampos["idaml_riesgo_perfiles"]["V"] =  $v; } return new MQLCampo($this->mCampos["idaml_riesgo_perfiles"]);}
	function objeto_de_origen($v = false){ if($v !== false){$this->mCampos["objeto_de_origen"]["V"] =  $v; } return new MQLCampo($this->mCampos["objeto_de_origen"]);}
	function campo_de_origen($v = false){ if($v !== false){$this->mCampos["campo_de_origen"]["V"] =  $v; } return new MQLCampo($this->mCampos["campo_de_origen"]);}
	function valor_de_origen($v = false){ if($v !== false){$this->mCampos["valor_de_origen"]["V"] =  $v; } return new MQLCampo($this->mCampos["valor_de_origen"]);}
	function nivel_de_riesgo($v = false){ if($v !== false){$this->mCampos["nivel_de_riesgo"]["V"] =  $v; } return new MQLCampo($this->mCampos["nivel_de_riesgo"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}





/*	ORM: Tabla:	usuarios	-	Generado:	[10/2/2014 14:48]	*/
class cVistaUsuarios {
	private $mCampos	= array(
			"idusuarios" => array("N"=>"idusuarios","T"=>"INT","V"=>"0","L"=>4),
			"nombreusuario" => array("N"=>"nombreusuario","T"=>"VARCHAR","V"=>"","L"=>15),
			"contrasenna" => array("N"=>"contrasenna","T"=>"VARCHAR","V"=>"","L"=>45),
			"nombres" => array("N"=>"nombres","T"=>"VARCHAR","V"=>"","L"=>45),
			"apellidopaterno" => array("N"=>"apellidopaterno","T"=>"VARCHAR","V"=>"","L"=>45),
			"apellidomaterno" => array("N"=>"apellidomaterno","T"=>"VARCHAR","V"=>"","L"=>45),
			"puesto" => array("N"=>"puesto","T"=>"VARCHAR","V"=>"NOTVALID","L"=>45),
			"niveldeacceso" => array("N"=>"niveldeacceso","T"=>"INT","V"=>"1","L"=>10),
			"periodo_responsable" => array("N"=>"periodo_responsable","T"=>"INT","V"=>"0","L"=>4),
			"estatus" => array("N"=>"estatus","T"=>"ENUM","V"=>"|baja|activo|suspension|","L"=>0),
			"expira" => array("N"=>"expira","T"=>"DATE","V"=>"","L"=>0),
			"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"matriz","L"=>10),
			"nombrecompleto" => array("N"=>"nombrecompleto","T"=>"VARCHAR","V"=>"","L"=>137),
			"id" => array("N"=>"id","T"=>"INT","V"=>"0","L"=>4),
			"cuenta_contable_de_caja" => array("N"=>"cuenta_contable_de_caja","T"=>"VARCHAR","V"=>"CUENTA_DE_CUADRE","L"=>100),
			"codigo_de_persona" => array("N"=>"codigo_de_persona","T"=>"BIGINT","V"=>"","L"=>20),

	);
	function __construct(){}
	function get(){ return "usuarios";}
	function getKey(){ return "idusuarios";}
	
	function idusuarios($v=false){
		if($v!==false){$this->mCampos["idusuarios"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["idusuarios"]);
	}
	function nombreusuario($v=false){
		if($v!==false){$this->mCampos["nombreusuario"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["nombreusuario"]);
	}
	function contrasenna($v=false){
		if($v!==false){$this->mCampos["contrasenna"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["contrasenna"]);
	}
	function nombres($v=false){
		if($v!==false){$this->mCampos["nombres"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["nombres"]);
	}
	function apellidopaterno($v=false){
		if($v!==false){$this->mCampos["apellidopaterno"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["apellidopaterno"]);
	}
	function apellidomaterno($v=false){
		if($v!==false){$this->mCampos["apellidomaterno"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["apellidomaterno"]);
	}
	function puesto($v=false){
		if($v!==false){$this->mCampos["puesto"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["puesto"]);
	}
	function niveldeacceso($v=false){
		if($v!==false){$this->mCampos["niveldeacceso"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["niveldeacceso"]);
	}
	function periodo_responsable($v=false){
		if($v!==false){$this->mCampos["periodo_responsable"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["periodo_responsable"]);
	}
	function estatus($v=false){
		if($v!==false){$this->mCampos["estatus"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["estatus"]);
	}
	function expira($v=false){
		if($v!==false){$this->mCampos["expira"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["expira"]);
	}
	function sucursal($v=false){
		if($v!==false){$this->mCampos["sucursal"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["sucursal"]);
	}
	function nombrecompleto($v=false){
		if($v!==false){$this->mCampos["nombrecompleto"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["nombrecompleto"]);
	}
	function id($v=false){
		if($v!==false){$this->mCampos["id"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["id"]);
	}
	function cuenta_contable_de_caja($v=false){
		if($v!==false){$this->mCampos["cuenta_contable_de_caja"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["cuenta_contable_de_caja"]);
	}
	function codigo_de_persona($v=false){
		if($v!==false){$this->mCampos["codigo_de_persona"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["codigo_de_persona"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}

/*	ORM: Tabla:	socios_figura_juridica	-	Generado:	[28/8/2014 11:37]	*/
class cSocios_figura_juridica {
	private $mCampos	= array(
		"idsocios_figura_juridica" => array("N"=>"idsocios_figura_juridica","T"=>"INT","V"=>"","L"=>11),
		"descripcion_figura_juridica" => array("N"=>"descripcion_figura_juridica","T"=>"VARCHAR","V"=>"","L"=>45),
		"tipo_de_integracion" => array("N"=>"tipo_de_integracion","T"=>"INT","V"=>"1","L"=>3),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "socios_figura_juridica";}
	function getKey(){ return "idsocios_figura_juridica";}
	function idsocios_figura_juridica($v = false){ if($v !== false){$this->mCampos["idsocios_figura_juridica"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsocios_figura_juridica"]);}
	function descripcion_figura_juridica($v = false){ if($v !== false){$this->mCampos["descripcion_figura_juridica"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_figura_juridica"]);}
	function tipo_de_integracion($v = false){ if($v !== false){$this->mCampos["tipo_de_integracion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_integracion"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	socios_general	-	Generado:	[14/8/2014 14:42]	*/
class cSocios_general {
	private $mCampos	= array(
			"codigo" => array("N"=>"codigo","T"=>"BIGINT","V"=>"0","L"=>20),
			"nombrecompleto" => array("N"=>"nombrecompleto","T"=>"VARCHAR","V"=>"","L"=>200),
			"apellidopaterno" => array("N"=>"apellidopaterno","T"=>"VARCHAR","V"=>"","L"=>25),
			"apellidomaterno" => array("N"=>"apellidomaterno","T"=>"VARCHAR","V"=>"","L"=>25),
			"rfc" => array("N"=>"rfc","T"=>"VARCHAR","V"=>"POR_REGISTRAR","L"=>20),
			"curp" => array("N"=>"curp","T"=>"VARCHAR","V"=>"POR_REGISTRAR","L"=>20),
			"fechaentrevista" => array("N"=>"fechaentrevista","T"=>"DATE","V"=>"2005-12-31","L"=>0),
			"fechaalta" => array("N"=>"fechaalta","T"=>"DATE","V"=>"2005-12-31","L"=>0),
			"estatusactual" => array("N"=>"estatusactual","T"=>"INT","V"=>"99","L"=>4),
			"region" => array("N"=>"region","T"=>"INT","V"=>"99","L"=>4),
			"cajalocal" => array("N"=>"cajalocal","T"=>"INT","V"=>"99","L"=>4),
			"fechanacimiento" => array("N"=>"fechanacimiento","T"=>"DATE","V"=>"2005-12-31","L"=>0),
			"lugarnacimiento" => array("N"=>"lugarnacimiento","T"=>"VARCHAR","V"=>"POR_REGISTRAR","L"=>45),
			"tipoingreso" => array("N"=>"tipoingreso","T"=>"INT","V"=>"99","L"=>4),
			"estadocivil" => array("N"=>"estadocivil","T"=>"INT","V"=>"99","L"=>4),
			"genero" => array("N"=>"genero","T"=>"INT","V"=>"99","L"=>4),
			"eacp" => array("N"=>"eacp","T"=>"VARCHAR","V"=>"EN_TRAMITE","L"=>15),
			"observaciones" => array("N"=>"observaciones","T"=>"VARCHAR","V"=>"","L"=>100),
			"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"99","L"=>4),
			"grupo_solidario" => array("N"=>"grupo_solidario","T"=>"BIGINT","V"=>"99","L"=>20),
			"personalidad_juridica" => array("N"=>"personalidad_juridica","T"=>"INT","V"=>"1","L"=>4),
			"dependencia" => array("N"=>"dependencia","T"=>"INT","V"=>"99","L"=>4),
			"regimen_conyugal" => array("N"=>"regimen_conyugal","T"=>"ENUM","V"=>"|NINGUNO|SOCIEDAD_CONYUGAL|BIENES_SEPARADOS|","L"=>0),
			"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"MATRIZ","L"=>10),
			"fecha_de_revision" => array("N"=>"fecha_de_revision","T"=>"DATE","V"=>"2008-06-01","L"=>0),
			"tipo_de_identificacion" => array("N"=>"tipo_de_identificacion","T"=>"TINYINT","V"=>"1","L"=>4),
			"documento_de_identificacion" => array("N"=>"documento_de_identificacion","T"=>"VARCHAR","V"=>"0","L"=>18),
			"correo_electronico" => array("N"=>"correo_electronico","T"=>"VARCHAR","V"=>"","L"=>40),
			"telefono_principal" => array("N"=>"telefono_principal","T"=>"VARCHAR","V"=>"","L"=>20),
			"dependientes_economicos" => array("N"=>"dependientes_economicos","T"=>"INT","V"=>"0","L"=>11),
			"pais_de_origen" => array("N"=>"pais_de_origen","T"=>"VARCHAR","V"=>"MX","L"=>10),
			"titulo_personal" => array("N"=>"titulo_personal","T"=>"VARCHAR","V"=>"NA","L"=>10),
			"nivel_de_riesgo_aml" => array("N"=>"nivel_de_riesgo_aml","T"=>"INT","V"=>"1","L"=>4),
			"clave_de_firma_electronica" => array("N"=>"clave_de_firma_electronica","T"=>"VARCHAR","V"=>"","L"=>200),
			"descuento_preferente" => array("N"=>"descuento_preferente","T"=>"FLOAT","V"=>"0.0000","L"=>29),
			"regimen_fiscal" => array("N"=>"regimen_fiscal","T"=>"INT","V"=>"1","L"=>4),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "socios_general";}
	function getKey(){ return "codigo";}
	function codigo($v = false){ if($v !== false){$this->mCampos["codigo"]["V"] =  $v; } return new MQLCampo($this->mCampos["codigo"]);}
	function nombrecompleto($v = false){ if($v !== false){$this->mCampos["nombrecompleto"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombrecompleto"]);}
	function apellidopaterno($v = false){ if($v !== false){$this->mCampos["apellidopaterno"]["V"] =  $v; } return new MQLCampo($this->mCampos["apellidopaterno"]);}
	function apellidomaterno($v = false){ if($v !== false){$this->mCampos["apellidomaterno"]["V"] =  $v; } return new MQLCampo($this->mCampos["apellidomaterno"]);}
	function rfc($v = false){ if($v !== false){$this->mCampos["rfc"]["V"] =  $v; } return new MQLCampo($this->mCampos["rfc"]);}
	function curp($v = false){ if($v !== false){$this->mCampos["curp"]["V"] =  $v; } return new MQLCampo($this->mCampos["curp"]);}
	function fechaentrevista($v = false){ if($v !== false){$this->mCampos["fechaentrevista"]["V"] =  $v; } return new MQLCampo($this->mCampos["fechaentrevista"]);}
	function fechaalta($v = false){ if($v !== false){$this->mCampos["fechaalta"]["V"] =  $v; } return new MQLCampo($this->mCampos["fechaalta"]);}
	function estatusactual($v = false){ if($v !== false){$this->mCampos["estatusactual"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatusactual"]);}
	function region($v = false){ if($v !== false){$this->mCampos["region"]["V"] =  $v; } return new MQLCampo($this->mCampos["region"]);}
	function cajalocal($v = false){ if($v !== false){$this->mCampos["cajalocal"]["V"] =  $v; } return new MQLCampo($this->mCampos["cajalocal"]);}
	function fechanacimiento($v = false){ if($v !== false){$this->mCampos["fechanacimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["fechanacimiento"]);}
	function lugarnacimiento($v = false){ if($v !== false){$this->mCampos["lugarnacimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["lugarnacimiento"]);}
	function tipoingreso($v = false){ if($v !== false){$this->mCampos["tipoingreso"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipoingreso"]);}
	function estadocivil($v = false){ if($v !== false){$this->mCampos["estadocivil"]["V"] =  $v; } return new MQLCampo($this->mCampos["estadocivil"]);}
	function genero($v = false){ if($v !== false){$this->mCampos["genero"]["V"] =  $v; } return new MQLCampo($this->mCampos["genero"]);}
	function eacp($v = false){ if($v !== false){$this->mCampos["eacp"]["V"] =  $v; } return new MQLCampo($this->mCampos["eacp"]);}
	function observaciones($v = false){ if($v !== false){$this->mCampos["observaciones"]["V"] =  $v; } return new MQLCampo($this->mCampos["observaciones"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function grupo_solidario($v = false){ if($v !== false){$this->mCampos["grupo_solidario"]["V"] =  $v; } return new MQLCampo($this->mCampos["grupo_solidario"]);}
	function personalidad_juridica($v = false){ if($v !== false){$this->mCampos["personalidad_juridica"]["V"] =  $v; } return new MQLCampo($this->mCampos["personalidad_juridica"]);}
	function dependencia($v = false){ if($v !== false){$this->mCampos["dependencia"]["V"] =  $v; } return new MQLCampo($this->mCampos["dependencia"]);}
	function regimen_conyugal($v = false){ if($v !== false){$this->mCampos["regimen_conyugal"]["V"] =  $v; } return new MQLCampo($this->mCampos["regimen_conyugal"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function fecha_de_revision($v = false){ if($v !== false){$this->mCampos["fecha_de_revision"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_revision"]);}
	function tipo_de_identificacion($v = false){ if($v !== false){$this->mCampos["tipo_de_identificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_identificacion"]);}
	function documento_de_identificacion($v = false){ if($v !== false){$this->mCampos["documento_de_identificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["documento_de_identificacion"]);}
	function correo_electronico($v = false){ if($v !== false){$this->mCampos["correo_electronico"]["V"] =  $v; } return new MQLCampo($this->mCampos["correo_electronico"]);}
	function telefono_principal($v = false){ if($v !== false){$this->mCampos["telefono_principal"]["V"] =  $v; } return new MQLCampo($this->mCampos["telefono_principal"]);}
	function dependientes_economicos($v = false){ if($v !== false){$this->mCampos["dependientes_economicos"]["V"] =  $v; } return new MQLCampo($this->mCampos["dependientes_economicos"]);}
	function pais_de_origen($v = false){ if($v !== false){$this->mCampos["pais_de_origen"]["V"] =  $v; } return new MQLCampo($this->mCampos["pais_de_origen"]);}
	function titulo_personal($v = false){ if($v !== false){$this->mCampos["titulo_personal"]["V"] =  $v; } return new MQLCampo($this->mCampos["titulo_personal"]);}
	function nivel_de_riesgo_aml($v = false){ if($v !== false){$this->mCampos["nivel_de_riesgo_aml"]["V"] =  $v; } return new MQLCampo($this->mCampos["nivel_de_riesgo_aml"]);}
	function clave_de_firma_electronica($v = false){ if($v !== false){$this->mCampos["clave_de_firma_electronica"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_firma_electronica"]);}
	function descuento_preferente($v = false){ if($v !== false){$this->mCampos["descuento_preferente"]["V"] =  $v; } return new MQLCampo($this->mCampos["descuento_preferente"]);}
	function regimen_fiscal($v = false){ if($v !== false){$this->mCampos["regimen_fiscal"]["V"] =  $v; } return new MQLCampo($this->mCampos["regimen_fiscal"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}


/*	ORM: Tabla:	socios_memo	-	Generado:	[19/11/2014 10:21]	*/
class cSocios_memo {
	private $mCampos	= array(
			"idsocios_memo" => array("N"=>"idsocios_memo","T"=>"INT","V"=>"","L"=>4),
			"numero_socio" => array("N"=>"numero_socio","T"=>"BIGINT","V"=>"1","L"=>20),
			"numero_gposolidario" => array("N"=>"numero_gposolidario","T"=>"BIGINT","V"=>"1","L"=>20),
			"numero_solicitud" => array("N"=>"numero_solicitud","T"=>"BIGINT","V"=>"1","L"=>20),
			"fecha_memo" => array("N"=>"fecha_memo","T"=>"DATE","V"=>"0000-00-00","L"=>0),
			"texto_memo" => array("N"=>"texto_memo","T"=>"VARCHAR","V"=>"","L"=>250),
			"tipo_memo" => array("N"=>"tipo_memo","T"=>"INT","V"=>"99","L"=>4),
			"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"99","L"=>4),
			"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"MATRIZ","L"=>10),
			"eacp" => array("N"=>"eacp","T"=>"VARCHAR","V"=>"","L"=>20),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "socios_memo";}
	function getKey(){ return "idsocios_memo";}
	function idsocios_memo($v = false){ if($v !== false){$this->mCampos["idsocios_memo"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsocios_memo"]);}
	function numero_socio($v = false){ if($v !== false){$this->mCampos["numero_socio"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_socio"]);}
	function numero_gposolidario($v = false){ if($v !== false){$this->mCampos["numero_gposolidario"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_gposolidario"]);}
	function numero_solicitud($v = false){ if($v !== false){$this->mCampos["numero_solicitud"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_solicitud"]);}
	function fecha_memo($v = false){ if($v !== false){$this->mCampos["fecha_memo"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_memo"]);}
	function texto_memo($v = false){ if($v !== false){$this->mCampos["texto_memo"]["V"] =  $v; } return new MQLCampo($this->mCampos["texto_memo"]);}
	function tipo_memo($v = false){ if($v !== false){$this->mCampos["tipo_memo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_memo"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function eacp($v = false){ if($v !== false){$this->mCampos["eacp"]["V"] =  $v; } return new MQLCampo($this->mCampos["eacp"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	socios_relacionestipos	-	Generado:	[04/12/2014 14:10]	*/
class cSocios_relacionestipos {
	private $mCampos	= array(
			"idsocios_relacionestipos" => array("N"=>"idsocios_relacionestipos","T"=>"INT","V"=>"0","L"=>4),
			"descripcion_relacionestipos" => array("N"=>"descripcion_relacionestipos","T"=>"VARCHAR","V"=>"","L"=>45),
			"subclasificacion" => array("N"=>"subclasificacion","T"=>"INT","V"=>"0","L"=>4),
			"descripcion_larga" => array("N"=>"descripcion_larga","T"=>"VARCHAR","V"=>"","L"=>150),
			"tipo_relacion" => array("N"=>"tipo_relacion","T"=>"INT","V"=>"0","L"=>4),
			"puntuacion_en_credit_scoring" => array("N"=>"puntuacion_en_credit_scoring","T"=>"FLOAT","V"=>"","L"=>13),
			"requiere_domicilio" => array("N"=>"requiere_domicilio","T"=>"INT","V"=>"0","L"=>4),
			"requiere_actividadeconomica" => array("N"=>"requiere_actividadeconomica","T"=>"INT","V"=>"0","L"=>4),
			"requiere_validacion" => array("N"=>"requiere_validacion","T"=>"INT","V"=>"0","L"=>4),
			"tiene_vinculo_patrimonial" => array("N"=>"tiene_vinculo_patrimonial","T"=>"INT","V"=>"0","L"=>4),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "socios_relacionestipos";}
	function getKey(){ return "idsocios_relacionestipos";}
	function idsocios_relacionestipos($v = false){ if($v !== false){$this->mCampos["idsocios_relacionestipos"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsocios_relacionestipos"]);}
	function descripcion_relacionestipos($v = false){ if($v !== false){$this->mCampos["descripcion_relacionestipos"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_relacionestipos"]);}
	function subclasificacion($v = false){ if($v !== false){$this->mCampos["subclasificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["subclasificacion"]);}
	function descripcion_larga($v = false){ if($v !== false){$this->mCampos["descripcion_larga"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_larga"]);}
	function tipo_relacion($v = false){ if($v !== false){$this->mCampos["tipo_relacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_relacion"]);}
	function puntuacion_en_credit_scoring($v = false){ if($v !== false){$this->mCampos["puntuacion_en_credit_scoring"]["V"] =  $v; } return new MQLCampo($this->mCampos["puntuacion_en_credit_scoring"]);}
	function requiere_domicilio($v = false){ if($v !== false){$this->mCampos["requiere_domicilio"]["V"] =  $v; } return new MQLCampo($this->mCampos["requiere_domicilio"]);}
	function requiere_actividadeconomica($v = false){ if($v !== false){$this->mCampos["requiere_actividadeconomica"]["V"] =  $v; } return new MQLCampo($this->mCampos["requiere_actividadeconomica"]);}
	function requiere_validacion($v = false){ if($v !== false){$this->mCampos["requiere_validacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["requiere_validacion"]);}
	function tiene_vinculo_patrimonial($v = false){ if($v !== false){$this->mCampos["tiene_vinculo_patrimonial"]["V"] =  $v; } return new MQLCampo($this->mCampos["tiene_vinculo_patrimonial"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	socios_consanguinidad	-	Generado:	[04/12/2014 14:06]	*/
class cSocios_consanguinidad {
	private $mCampos	= array(
			"idsocios_consanguinidad" => array("N"=>"idsocios_consanguinidad","T"=>"INT","V"=>"0","L"=>4),
			"descripcion_consanguinidad" => array("N"=>"descripcion_consanguinidad","T"=>"VARCHAR","V"=>"","L"=>45),
			"grado_de_consaguinidad" => array("N"=>"grado_de_consaguinidad","T"=>"INT","V"=>"1","L"=>2),
			"grado_de_afinidad" => array("N"=>"grado_de_afinidad","T"=>"INT","V"=>"0","L"=>2),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "socios_consanguinidad";}
	function getKey(){ return "idsocios_consanguinidad";}
	function idsocios_consanguinidad($v = false){ if($v !== false){$this->mCampos["idsocios_consanguinidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsocios_consanguinidad"]);}
	function descripcion_consanguinidad($v = false){ if($v !== false){$this->mCampos["descripcion_consanguinidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_consanguinidad"]);}
	function grado_de_consaguinidad($v = false){ if($v !== false){$this->mCampos["grado_de_consaguinidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["grado_de_consaguinidad"]);}
	function grado_de_afinidad($v = false){ if($v !== false){$this->mCampos["grado_de_afinidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["grado_de_afinidad"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	personas_documentacion	-	Generado:	[14/2/2014 16:22]	*/
class cPersonas_documentacion {
	private $mCampos	= array(
			"clave_de_control" => array("N"=>"clave_de_control","T"=>"INT","V"=>"","L"=>11),
			"clave_de_persona" => array("N"=>"clave_de_persona","T"=>"BIGINT","V"=>"","L"=>20),
			"tipo_de_documento" => array("N"=>"tipo_de_documento","T"=>"INT","V"=>"0","L"=>11),
			"fecha_de_carga" => array("N"=>"fecha_de_carga","T"=>"BIGINT","V"=>"0","L"=>20),
			"observaciones" => array("N"=>"observaciones","T"=>"VARCHAR","V"=>"","L"=>100),
			"archivo_de_documento" => array("N"=>"archivo_de_documento","T"=>"MEDIUMBLOB","V"=>"","L"=>0),
			"valor_de_comprobacion" => array("N"=>"valor_de_comprobacion","T"=>"VARCHAR","V"=>"","L"=>100),
			"estado_en_sistema" => array("N"=>"estado_en_sistema","T"=>"INT","V"=>"1","L"=>11),
			"fecha_de_verificacion" => array("N"=>"fecha_de_verificacion","T"=>"BIGINT","V"=>"","L"=>20),
			"oficial_que_verifico" => array("N"=>"oficial_que_verifico","T"=>"INT","V"=>"","L"=>11),
			"resultado_de_la_verificacion" => array("N"=>"resultado_de_la_verificacion","T"=>"INT","V"=>"","L"=>11),
			"notas" => array("N"=>"notas","T"=>"VARCHAR","V"=>"","L"=>100),
			"version_de_documento" => array("N"=>"version_de_documento","T"=>"VARCHAR","V"=>"","L"=>20),
			"numero_de_pagina" => array("N"=>"numero_de_pagina","T"=>"INT","V"=>"","L"=>11),
			"usuario" => array("N"=>"usuario","T"=>"INT","V"=>"1","L"=>11),
			"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"","L"=>20),
			"entidad" => array("N"=>"entidad","T"=>"VARCHAR","V"=>"","L"=>20),

	);
	function __construct(){}
	function get(){ return "personas_documentacion";}
	function getKey(){ return "clave_de_control";}
	function clave_de_control($v=false){
		if($v!==false){$this->mCampos["clave_de_control"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["clave_de_control"]);
	}
	function clave_de_persona($v=false){
		if($v!==false){$this->mCampos["clave_de_persona"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["clave_de_persona"]);
	}
	function tipo_de_documento($v=false){
		if($v!==false){$this->mCampos["tipo_de_documento"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["tipo_de_documento"]);
	}
	function fecha_de_carga($v=false){
		if($v!==false){$this->mCampos["fecha_de_carga"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["fecha_de_carga"]);
	}
	function observaciones($v=false){
		if($v!==false){$this->mCampos["observaciones"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["observaciones"]);
	}
	function archivo_de_documento($v=false){
		if($v!==false){$this->mCampos["archivo_de_documento"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["archivo_de_documento"]);
	}
	function valor_de_comprobacion($v=false){
		if($v!==false){$this->mCampos["valor_de_comprobacion"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["valor_de_comprobacion"]);
	}
	function estado_en_sistema($v=false){
		if($v!==false){$this->mCampos["estado_en_sistema"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["estado_en_sistema"]);
	}
	function fecha_de_verificacion($v=false){
		if($v!==false){$this->mCampos["fecha_de_verificacion"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["fecha_de_verificacion"]);
	}
	function oficial_que_verifico($v=false){
		if($v!==false){$this->mCampos["oficial_que_verifico"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["oficial_que_verifico"]);
	}
	function resultado_de_la_verificacion($v=false){
		if($v!==false){$this->mCampos["resultado_de_la_verificacion"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["resultado_de_la_verificacion"]);
	}
	function notas($v=false){
		if($v!==false){$this->mCampos["notas"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["notas"]);
	}
	function version_de_documento($v=false){
		if($v!==false){$this->mCampos["version_de_documento"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["version_de_documento"]);
	}
	function numero_de_pagina($v=false){
		if($v!==false){$this->mCampos["numero_de_pagina"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["numero_de_pagina"]);
	}
	function usuario($v=false){
		if($v!==false){$this->mCampos["usuario"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["usuario"]);
	}
	function sucursal($v=false){
		if($v!==false){$this->mCampos["sucursal"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["sucursal"]);
	}
	function entidad($v=false){
		if($v!==false){$this->mCampos["entidad"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["entidad"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}

/*	ORM: Tabla:	creditos_garantias	-	Generado:	[18/2/2014 13:36]	*/
class cCreditos_garantias {
	private $mCampos	= array(
			"idcreditos_garantias" => array("N"=>"idcreditos_garantias","T"=>"INT","V"=>"","L"=>4),
			"socio_garantia" => array("N"=>"socio_garantia","T"=>"BIGINT","V"=>"0","L"=>20),
			"solicitud_garantia" => array("N"=>"solicitud_garantia","T"=>"BIGINT","V"=>"1","L"=>20),
			"tipo_garantia" => array("N"=>"tipo_garantia","T"=>"INT","V"=>"99","L"=>4),
			"fecha_recibo" => array("N"=>"fecha_recibo","T"=>"DATE","V"=>"0000-00-00","L"=>0),
			"fecha_adquisicion" => array("N"=>"fecha_adquisicion","T"=>"DATE","V"=>"0000-00-00","L"=>0),
			"tipo_valuacion" => array("N"=>"tipo_valuacion","T"=>"INT","V"=>"99","L"=>4),
			"monto_valuado" => array("N"=>"monto_valuado","T"=>"FLOAT","V"=>"0.00","L"=>25),
			"observaciones" => array("N"=>"observaciones","T"=>"VARCHAR","V"=>"","L"=>100),
			"documento_presentado" => array("N"=>"documento_presentado","T"=>"VARCHAR","V"=>"","L"=>250),
			"estatus_actual" => array("N"=>"estatus_actual","T"=>"INT","V"=>"1","L"=>4),
			"fecha_resguardo" => array("N"=>"fecha_resguardo","T"=>"DATE","V"=>"0000-00-00","L"=>0),
			"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"99","L"=>4),
			"propietario" => array("N"=>"propietario","T"=>"VARCHAR","V"=>"","L"=>60),
			"fecha_devolucion" => array("N"=>"fecha_devolucion","T"=>"DATE","V"=>"0000-00-00","L"=>0),
			"estado_presentado" => array("N"=>"estado_presentado","T"=>"INT","V"=>"99","L"=>4),
			"idsocio_duenno" => array("N"=>"idsocio_duenno","T"=>"BIGINT","V"=>"1","L"=>20),
			"descripcion" => array("N"=>"descripcion","T"=>"VARCHAR","V"=>"NA","L"=>250),
			"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"MATRIZ","L"=>10),
			"observaciones_del_resguardo" => array("N"=>"observaciones_del_resguardo","T"=>"VARCHAR","V"=>"","L"=>60),
			"eacp" => array("N"=>"eacp","T"=>"VARCHAR","V"=>"EN_TRAMITE","L"=>20),

	);
	function __construct(){}
	function get(){ return "creditos_garantias";}
	function getKey(){ return "idcreditos_garantias";}
	function idcreditos_garantias($v=false){
		if($v!==false){$this->mCampos["idcreditos_garantias"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["idcreditos_garantias"]);
	}
	function socio_garantia($v=false){
		if($v!==false){$this->mCampos["socio_garantia"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["socio_garantia"]);
	}
	function solicitud_garantia($v=false){
		if($v!==false){$this->mCampos["solicitud_garantia"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["solicitud_garantia"]);
	}
	function tipo_garantia($v=false){
		if($v!==false){$this->mCampos["tipo_garantia"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["tipo_garantia"]);
	}
	function fecha_recibo($v=false){
		if($v!==false){$this->mCampos["fecha_recibo"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["fecha_recibo"]);
	}
	function fecha_adquisicion($v=false){
		if($v!==false){$this->mCampos["fecha_adquisicion"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["fecha_adquisicion"]);
	}
	function tipo_valuacion($v=false){
		if($v!==false){$this->mCampos["tipo_valuacion"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["tipo_valuacion"]);
	}
	function monto_valuado($v=false){
		if($v!==false){$this->mCampos["monto_valuado"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["monto_valuado"]);
	}
	function observaciones($v=false){
		if($v!==false){$this->mCampos["observaciones"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["observaciones"]);
	}
	function documento_presentado($v=false){
		if($v!==false){$this->mCampos["documento_presentado"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["documento_presentado"]);
	}
	function estatus_actual($v=false){
		if($v!==false){$this->mCampos["estatus_actual"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["estatus_actual"]);
	}
	function fecha_resguardo($v=false){
		if($v!==false){$this->mCampos["fecha_resguardo"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["fecha_resguardo"]);
	}
	function idusuario($v=false){
		if($v!==false){$this->mCampos["idusuario"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["idusuario"]);
	}
	function propietario($v=false){
		if($v!==false){$this->mCampos["propietario"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["propietario"]);
	}
	function fecha_devolucion($v=false){
		if($v!==false){$this->mCampos["fecha_devolucion"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["fecha_devolucion"]);
	}
	function estado_presentado($v=false){
		if($v!==false){$this->mCampos["estado_presentado"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["estado_presentado"]);
	}
	function idsocio_duenno($v=false){
		if($v!==false){$this->mCampos["idsocio_duenno"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["idsocio_duenno"]);
	}
	function descripcion($v=false){
		if($v!==false){$this->mCampos["descripcion"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["descripcion"]);
	}
	function sucursal($v=false){
		if($v!==false){$this->mCampos["sucursal"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["sucursal"]);
	}
	function observaciones_del_resguardo($v=false){
		if($v!==false){$this->mCampos["observaciones_del_resguardo"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["observaciones_del_resguardo"]);
	}
	function eacp($v=false){
		if($v!==false){$this->mCampos["eacp"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["eacp"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}
/*	ORM: Tabla:	personas_documentacion_tipos	-	Generado:	[21/2/2014 15:15]	*/
class cPersonas_documentacion_tipos {
	private $mCampos	= array(
			"clave_de_control" => array("N"=>"clave_de_control","T"=>"INT","V"=>"","L"=>11),
			"nombre_del_documento" => array("N"=>"nombre_del_documento","T"=>"VARCHAR","V"=>"","L"=>100),

	);
	function __construct(){}
	function get(){ return "personas_documentacion_tipos";}
	function getKey(){ return "clave_de_control";}
	function clave_de_control($v=false){
		if($v!==false){$this->mCampos["clave_de_control"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["clave_de_control"]);
	}
	function nombre_del_documento($v=false){
		if($v!==false){$this->mCampos["nombre_del_documento"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["nombre_del_documento"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}




/*	ORM: Tabla:	personas_perfil_transaccional	-	Generado:	[25/2/2014 16:54]	*/
class cPersonas_perfil_transaccional {
	private $mCampos	= array(
			"idpersonas_perfil_transaccional" => array("N"=>"idpersonas_perfil_transaccional","T"=>"INT","V"=>"","L"=>11),
			"clave_de_persona" => array("N"=>"clave_de_persona","T"=>"BIGINT","V"=>"","L"=>20),
			"fecha_de_registro" => array("N"=>"fecha_de_registro","T"=>"BIGINT","V"=>"","L"=>20),
			"fecha_de_vencimiento" => array("N"=>"fecha_de_vencimiento","T"=>"BIGINT","V"=>"","L"=>20),
			"clave_de_tipo_de_perfil" => array("N"=>"clave_de_tipo_de_perfil","T"=>"INT","V"=>"","L"=>11),
			"pais_de_origen" => array("N"=>"pais_de_origen","T"=>"VARCHAR","V"=>"","L"=>20),
			"maximo_de_operaciones" => array("N"=>"maximo_de_operaciones","T"=>"INT","V"=>"","L"=>11),
			"cantidad_maxima" => array("N"=>"cantidad_maxima","T"=>"FLOAT","V"=>"","L"=>37),
			"operaciones_calculadas" => array("N"=>"operaciones_calculadas","T"=>"INT","V"=>"","L"=>11),
			"cantidad_calculada" => array("N"=>"cantidad_calculada","T"=>"FLOAT","V"=>"","L"=>37),
			"fecha_de_calculo" => array("N"=>"fecha_de_calculo","T"=>"BIGINT","V"=>"","L"=>20),
			"afectacion" => array("N"=>"afectacion","T"=>"INT","V"=>"","L"=>11),
			"observaciones" => array("N"=>"observaciones","T"=>"VARCHAR","V"=>"","L"=>200),

	);
	function __construct(){}
	function get(){ return "personas_perfil_transaccional";}
	function getKey(){ return "idpersonas_perfil_transaccional";}
	function idpersonas_perfil_transaccional($v=false){
		if($v!==false){$this->mCampos["idpersonas_perfil_transaccional"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["idpersonas_perfil_transaccional"]);
	}
	function clave_de_persona($v=false){
		if($v!==false){$this->mCampos["clave_de_persona"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["clave_de_persona"]);
	}
	function fecha_de_registro($v=false){
		if($v!==false){$this->mCampos["fecha_de_registro"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["fecha_de_registro"]);
	}
	function fecha_de_vencimiento($v=false){
		if($v!==false){$this->mCampos["fecha_de_vencimiento"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["fecha_de_vencimiento"]);
	}
	function clave_de_tipo_de_perfil($v=false){
		if($v!==false){$this->mCampos["clave_de_tipo_de_perfil"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["clave_de_tipo_de_perfil"]);
	}
	function pais_de_origen($v=false){
		if($v!==false){$this->mCampos["pais_de_origen"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["pais_de_origen"]);
	}
	function maximo_de_operaciones($v=false){
		if($v!==false){$this->mCampos["maximo_de_operaciones"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["maximo_de_operaciones"]);
	}
	function cantidad_maxima($v=false){
		if($v!==false){$this->mCampos["cantidad_maxima"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["cantidad_maxima"]);
	}
	function operaciones_calculadas($v=false){
		if($v!==false){$this->mCampos["operaciones_calculadas"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["operaciones_calculadas"]);
	}
	function cantidad_calculada($v=false){
		if($v!==false){$this->mCampos["cantidad_calculada"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["cantidad_calculada"]);
	}
	function fecha_de_calculo($v=false){
		if($v!==false){$this->mCampos["fecha_de_calculo"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["fecha_de_calculo"]);
	}
	function afectacion($v=false){
		if($v!==false){$this->mCampos["afectacion"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["afectacion"]);
	}
	function observaciones($v=false){
		if($v!==false){$this->mCampos["observaciones"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["observaciones"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}

/*	ORM: Tabla:	creditos_tvaluacion	-	Generado:	[25/2/2014 11:54]	*/
class cCreditos_tvaluacion {
	private $mCampos	= array(
		"idcreditos_tvaluacion" => array("N"=>"idcreditos_tvaluacion","T"=>"INT","V"=>"0","L"=>4),
		"descripcion_tvaluacion" => array("N"=>"descripcion_tvaluacion","T"=>"VARCHAR","V"=>"","L"=>45),
		"tipo_valuacion" => array("N"=>"tipo_valuacion","T"=>"INT","V"=>"0","L"=>4),

	);
	function __construct(){}
	function get(){ return "creditos_tvaluacion";}
	function getKey(){ return "idcreditos_tvaluacion";}
	function idcreditos_tvaluacion($v=false){
 		if($v!==false){$this->mCampos["idcreditos_tvaluacion"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["idcreditos_tvaluacion"]);
	}
	function descripcion_tvaluacion($v=false){
 		if($v!==false){$this->mCampos["descripcion_tvaluacion"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["descripcion_tvaluacion"]);
	}
	function tipo_valuacion($v=false){
 		if($v!==false){$this->mCampos["tipo_valuacion"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["tipo_valuacion"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}

/*	ORM: Tabla:	creditos_tgarantias	-	Generado:	[25/2/2014 11:56]	*/
class cCreditos_tgarantias {
	private $mCampos	= array(
		"idcreditos_tgarantias" => array("N"=>"idcreditos_tgarantias","T"=>"INT","V"=>"0","L"=>4),
		"descripcion_tgarantias" => array("N"=>"descripcion_tgarantias","T"=>"VARCHAR","V"=>"","L"=>100),
		"tipo_garantia" => array("N"=>"tipo_garantia","T"=>"INT","V"=>"0","L"=>4),

	);
	function __construct(){}
	function get(){ return "creditos_tgarantias";}
	function getKey(){ return "idcreditos_tgarantias";}
	function idcreditos_tgarantias($v=false){
 		if($v!==false){$this->mCampos["idcreditos_tgarantias"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["idcreditos_tgarantias"]);
	}
	function descripcion_tgarantias($v=false){
 		if($v!==false){$this->mCampos["descripcion_tgarantias"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["descripcion_tgarantias"]);
	}
	function tipo_garantia($v=false){
 		if($v!==false){$this->mCampos["tipo_garantia"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["tipo_garantia"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}

/*	ORM: Tabla:	personas_perfil_transaccional_tipos	-	Generado:	[25/2/2014 17:33]	*/
class cPersonas_perfil_transaccional_tipos {
	private $mCampos	= array(
		"idpersonas_perfil_transaccional_tipos" => array("N"=>"idpersonas_perfil_transaccional_tipos","T"=>"INT","V"=>"","L"=>11),
		"nombre_del_perfil" => array("N"=>"nombre_del_perfil","T"=>"VARCHAR","V"=>"","L"=>100),
		"tipo_de_exhibicion" => array("N"=>"tipo_de_exhibicion","T"=>"VARCHAR","V"=>"","L"=>100),
		"afectacion" => array("N"=>"afectacion","T"=>"INT","V"=>"1","L"=>11),

	);
	function __construct(){}
	function get(){ return "personas_perfil_transaccional_tipos";}
	function getKey(){ return "idpersonas_perfil_transaccional_tipos";}
	function idpersonas_perfil_transaccional_tipos($v=false){
 		if($v!==false){$this->mCampos["idpersonas_perfil_transaccional_tipos"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["idpersonas_perfil_transaccional_tipos"]);
	}
	function nombre_del_perfil($v=false){
 		if($v!==false){$this->mCampos["nombre_del_perfil"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["nombre_del_perfil"]);
	}
	function tipo_de_exhibicion($v=false){
 		if($v!==false){$this->mCampos["tipo_de_exhibicion"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["tipo_de_exhibicion"]);
	}
	function afectacion($v=false){
 		if($v!==false){$this->mCampos["afectacion"]["V"] =  $v; }
 		return new MQLCampo($this->mCampos["afectacion"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}

/*	ORM: Tabla:	personas_regimen_fiscal	-	Generado:	[23/8/2014 20:45]	*/
class cPersonas_regimen_fiscal {
	private $mCampos	= array(
			"clave_de_regimen" => array("N"=>"clave_de_regimen","T"=>"INT","V"=>"","L"=>11),
			"nombre_del_regimen" => array("N"=>"nombre_del_regimen","T"=>"VARCHAR","V"=>"","L"=>100),
			"tipo_de_persona" => array("N"=>"tipo_de_persona","T"=>"INT","V"=>"","L"=>4),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "personas_regimen_fiscal";}
	function getKey(){ return "clave_de_regimen";}
	function clave_de_regimen($v = false){ if($v !== false){$this->mCampos["clave_de_regimen"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_regimen"]);}
	function nombre_del_regimen($v = false){ if($v !== false){$this->mCampos["nombre_del_regimen"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_del_regimen"]);}
	function tipo_de_persona($v = false){ if($v !== false){$this->mCampos["tipo_de_persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_persona"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	bancos_entidades	-	Generado:	[08/3/2014 10:29]	*/
class cBancos_entidades {
	private $mCampos	= array(
			"idbancos_entidades" => array("N"=>"idbancos_entidades","T"=>"INT","V"=>"","L"=>10),
			"nombre_de_la_entidad" => array("N"=>"nombre_de_la_entidad","T"=>"VARCHAR","V"=>"","L"=>100),
			"rfc_de_la_entidad" => array("N"=>"rfc_de_la_entidad","T"=>"VARCHAR","V"=>"","L"=>15),
			"pais_de_origen" => array("N"=>"pais_de_origen","T"=>"VARCHAR","V"=>"MX","L"=>10),

	);
	function __construct(){}
	function get(){ return "bancos_entidades";}
	function getKey(){ return "idbancos_entidades";}
	function idbancos_entidades($v=false){
		if($v!==false){$this->mCampos["idbancos_entidades"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["idbancos_entidades"]);
	}
	function nombre_de_la_entidad($v=false){
		if($v!==false){$this->mCampos["nombre_de_la_entidad"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["nombre_de_la_entidad"]);
	}
	function rfc_de_la_entidad($v=false){
		if($v!==false){$this->mCampos["rfc_de_la_entidad"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["rfc_de_la_entidad"]);
	}
	function pais_de_origen($v=false){
		if($v!==false){$this->mCampos["pais_de_origen"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["pais_de_origen"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}

/*	ORM: Tabla:	general_colonias	-	Generado:	[10/3/2014 15:08]	*/
class cGeneral_colonias {
	private $mCampos	= array(
			"idgeneral_colonia" => array("N"=>"idgeneral_colonia","T"=>"INT","V"=>"","L"=>10),
			"codigo_postal" => array("N"=>"codigo_postal","T"=>"INT","V"=>"0","L"=>10),
			"nombre_colonia" => array("N"=>"nombre_colonia","T"=>"VARCHAR","V"=>"","L"=>100),
			"tipo_colonia" => array("N"=>"tipo_colonia","T"=>"VARCHAR","V"=>"","L"=>100),
			"estado_colonia" => array("N"=>"estado_colonia","T"=>"VARCHAR","V"=>"campeche","L"=>100),
			"ciudad_colonia" => array("N"=>"ciudad_colonia","T"=>"VARCHAR","V"=>"","L"=>100),
			"municipio_colonia" => array("N"=>"municipio_colonia","T"=>"VARCHAR","V"=>"","L"=>100),
			"fecha_de_revision" => array("N"=>"fecha_de_revision","T"=>"DATE","V"=>"2008-12-31","L"=>0),
			"codigo_de_estado" => array("N"=>"codigo_de_estado","T"=>"INT","V"=>"4","L"=>4),
			"codigo_de_municipio" => array("N"=>"codigo_de_municipio","T"=>"INT","V"=>"1","L"=>4),
			"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"matriz","L"=>25),

	);
	function __construct(){}
	function get(){ return "general_colonias";}
	function getKey(){ return "idgeneral_colonia";}

	function idgeneral_colonia($v=false){
		if($v!==false){$this->mCampos["idgeneral_colonia"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["idgeneral_colonia"]);
	}
	function codigo_postal($v=false){
		if($v!==false){$this->mCampos["codigo_postal"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["codigo_postal"]);
	}
	function nombre_colonia($v=false){
		if($v!==false){$this->mCampos["nombre_colonia"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["nombre_colonia"]);
	}
	function tipo_colonia($v=false){
		if($v!==false){$this->mCampos["tipo_colonia"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["tipo_colonia"]);
	}
	function estado_colonia($v=false){
		if($v!==false){$this->mCampos["estado_colonia"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["estado_colonia"]);
	}
	function ciudad_colonia($v=false){
		if($v!==false){$this->mCampos["ciudad_colonia"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["ciudad_colonia"]);
	}
	function municipio_colonia($v=false){
		if($v!==false){$this->mCampos["municipio_colonia"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["municipio_colonia"]);
	}
	function fecha_de_revision($v=false){
		if($v!==false){$this->mCampos["fecha_de_revision"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["fecha_de_revision"]);
	}
	function codigo_de_estado($v=false){
		if($v!==false){$this->mCampos["codigo_de_estado"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["codigo_de_estado"]);
	}
	function codigo_de_municipio($v=false){
		if($v!==false){$this->mCampos["codigo_de_municipio"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["codigo_de_municipio"]);
	}
	function sucursal($v=false){
		if($v!==false){$this->mCampos["sucursal"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["sucursal"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}

/*	ORM: Tabla:	general_estados	-	Generado:	[10/3/2014 16:34]	*/
class cGeneral_estados {
	private $mCampos	= array(
			"idgeneral_estados" => array("N"=>"idgeneral_estados","T"=>"INT","V"=>"","L"=>11),
			"clave_alfanumerica" => array("N"=>"clave_alfanumerica","T"=>"VARCHAR","V"=>"CC","L"=>4),
			"clave_numerica" => array("N"=>"clave_numerica","T"=>"TINYINT","V"=>"4","L"=>4),
			"nombre" => array("N"=>"nombre","T"=>"VARCHAR","V"=>"","L"=>60),
			"clave_en_sic" => array("N"=>"clave_en_sic","T"=>"VARCHAR","V"=>"YUC","L"=>8),

	);
	function __construct(){}
	function get(){ return "general_estados";}
	function getKey(){ return "idgeneral_estados";}
	function idgeneral_estados($v=false){
		if($v!==false){$this->mCampos["idgeneral_estados"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["idgeneral_estados"]);
	}
	function clave_alfanumerica($v=false){
		if($v!==false){$this->mCampos["clave_alfanumerica"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["clave_alfanumerica"]);
	}
	function clave_numerica($v=false){
		if($v!==false){$this->mCampos["clave_numerica"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["clave_numerica"]);
	}
	function nombre($v=false){
		if($v!==false){$this->mCampos["nombre"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["nombre"]);
	}
	function clave_en_sic($v=false){
		if($v!==false){$this->mCampos["clave_en_sic"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["clave_en_sic"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}



/*	ORM: Tabla:	general_contratos	-	Generado:	[23/6/2014 10:54]	*/
class cGeneral_contratos {
	private $mCampos	= array(
			"idgeneral_contratos" => array("N"=>"idgeneral_contratos","T"=>"INT","V"=>"","L"=>10),
			"tipo_contrato" => array("N"=>"tipo_contrato","T"=>"INT","V"=>"0","L"=>4),
			"estatus" => array("N"=>"estatus","T"=>"ENUM","V"=>"|alta|baja|","L"=>0),
			"titulo_del_contrato" => array("N"=>"titulo_del_contrato","T"=>"VARCHAR","V"=>"","L"=>100),
			"texto_del_contrato" => array("N"=>"texto_del_contrato","T"=>"MEDIUMTEXT","V"=>"","L"=>0),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "general_contratos";}
	function getKey(){ return "idgeneral_contratos";}
	function idgeneral_contratos($v = false){ if($v !== false){$this->mCampos["idgeneral_contratos"]["V"] =  $v; } return new MQLCampo($this->mCampos["idgeneral_contratos"]);}
	function tipo_contrato($v = false){ if($v !== false){$this->mCampos["tipo_contrato"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_contrato"]);}
	function estatus($v = false){ if($v !== false){$this->mCampos["estatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus"]);}
	function titulo_del_contrato($v = false){ if($v !== false){$this->mCampos["titulo_del_contrato"]["V"] =  $v; } return new MQLCampo($this->mCampos["titulo_del_contrato"]);}
	function texto_del_contrato($v = false){ if($v !== false){$this->mCampos["texto_del_contrato"]["V"] =  $v; } return new MQLCampo($this->mCampos["texto_del_contrato"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}



/*	ORM: Tabla:	captacion_cuentas	-	Generado:	[08/4/2014 15:39]	*/
class cCaptacion_cuentas {
	private $mCampos	= array(
			"numero_cuenta" => array("N"=>"numero_cuenta","T"=>"BIGINT","V"=>"1","L"=>20),
			"numero_socio" => array("N"=>"numero_socio","T"=>"BIGINT","V"=>"1","L"=>20),
			"numero_grupo" => array("N"=>"numero_grupo","T"=>"BIGINT","V"=>"1","L"=>20),
			"numero_solicitud" => array("N"=>"numero_solicitud","T"=>"BIGINT","V"=>"1","L"=>20),
			"tipo_cuenta" => array("N"=>"tipo_cuenta","T"=>"INT","V"=>"99","L"=>4),
			"fecha_apertura" => array("N"=>"fecha_apertura","T"=>"DATE","V"=>"0000-00-00","L"=>0),
			"fecha_afectacion" => array("N"=>"fecha_afectacion","T"=>"DATE","V"=>"0000-00-00","L"=>0),
			"fecha_baja" => array("N"=>"fecha_baja","T"=>"DATE","V"=>"0000-00-00","L"=>0),
			"estatus_cuenta" => array("N"=>"estatus_cuenta","T"=>"INT","V"=>"99","L"=>4),
			"saldo_cuenta" => array("N"=>"saldo_cuenta","T"=>"FLOAT","V"=>"0.00","L"=>25),
			"eacp" => array("N"=>"eacp","T"=>"VARCHAR","V"=>"","L"=>10),
			"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"99","L"=>4),
			"inversion_fecha_vcto" => array("N"=>"inversion_fecha_vcto","T"=>"DATE","V"=>"","L"=>0),
			"inversion_periodo" => array("N"=>"inversion_periodo","T"=>"INT","V"=>"1","L"=>4),
			"tasa_otorgada" => array("N"=>"tasa_otorgada","T"=>"FLOAT","V"=>"0.0000","L"=>13),
			"dias_invertidos" => array("N"=>"dias_invertidos","T"=>"INT","V"=>"0","L"=>4),
			"observacion_cuenta" => array("N"=>"observacion_cuenta","T"=>"VARCHAR","V"=>"","L"=>200),
			"origen_cuenta" => array("N"=>"origen_cuenta","T"=>"INT","V"=>"0","L"=>4),
			"tipo_titulo" => array("N"=>"tipo_titulo","T"=>"INT","V"=>"1","L"=>4),
			"tipo_subproducto" => array("N"=>"tipo_subproducto","T"=>"INT","V"=>"99","L"=>4),
			"nombre_mancomunado1" => array("N"=>"nombre_mancomunado1","T"=>"VARCHAR","V"=>"","L"=>50),
			"nombre_mancomunado2" => array("N"=>"nombre_mancomunado2","T"=>"VARCHAR","V"=>"","L"=>50),
			"minimo_mancomunantes" => array("N"=>"minimo_mancomunantes","T"=>"INT","V"=>"1","L"=>4),
			"saldo_conciliado" => array("N"=>"saldo_conciliado","T"=>"FLOAT","V"=>"0.00","L"=>25),
			"fecha_conciliada" => array("N"=>"fecha_conciliada","T"=>"DATE","V"=>"2006-12-31","L"=>0),
			"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"MATRIZ","L"=>10),
			"ultimo_sdpm" => array("N"=>"ultimo_sdpm","T"=>"FLOAT","V"=>"0.000","L"=>25),
			"oficial_de_captacion" => array("N"=>"oficial_de_captacion","T"=>"INT","V"=>"99","L"=>4),
			"cuenta_de_intereses" => array("N"=>"cuenta_de_intereses","T"=>"BIGINT","V"=>"0","L"=>20),

	);
	function __construct(){}
	function get(){ return "captacion_cuentas";}
	function getKey(){ return "numero_cuenta";}
	function numero_cuenta($v=false){
		if($v!==false){$this->mCampos["numero_cuenta"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["numero_cuenta"]);
	}
	function numero_socio($v=false){
		if($v!==false){$this->mCampos["numero_socio"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["numero_socio"]);
	}
	function numero_grupo($v=false){
		if($v!==false){$this->mCampos["numero_grupo"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["numero_grupo"]);
	}
	function numero_solicitud($v=false){
		if($v!==false){$this->mCampos["numero_solicitud"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["numero_solicitud"]);
	}
	function tipo_cuenta($v=false){
		if($v!==false){$this->mCampos["tipo_cuenta"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["tipo_cuenta"]);
	}
	function fecha_apertura($v=false){
		if($v!==false){$this->mCampos["fecha_apertura"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["fecha_apertura"]);
	}
	function fecha_afectacion($v=false){
		if($v!==false){$this->mCampos["fecha_afectacion"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["fecha_afectacion"]);
	}
	function fecha_baja($v=false){
		if($v!==false){$this->mCampos["fecha_baja"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["fecha_baja"]);
	}
	function estatus_cuenta($v=false){
		if($v!==false){$this->mCampos["estatus_cuenta"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["estatus_cuenta"]);
	}
	function saldo_cuenta($v=false){
		if($v!==false){$this->mCampos["saldo_cuenta"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["saldo_cuenta"]);
	}
	function eacp($v=false){
		if($v!==false){$this->mCampos["eacp"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["eacp"]);
	}
	function idusuario($v=false){
		if($v!==false){$this->mCampos["idusuario"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["idusuario"]);
	}
	function inversion_fecha_vcto($v=false){
		if($v!==false){$this->mCampos["inversion_fecha_vcto"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["inversion_fecha_vcto"]);
	}
	function inversion_periodo($v=false){
		if($v!==false){$this->mCampos["inversion_periodo"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["inversion_periodo"]);
	}
	function tasa_otorgada($v=false){
		if($v!==false){$this->mCampos["tasa_otorgada"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["tasa_otorgada"]);
	}
	function dias_invertidos($v=false){
		if($v!==false){$this->mCampos["dias_invertidos"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["dias_invertidos"]);
	}
	function observacion_cuenta($v=false){
		if($v!==false){$this->mCampos["observacion_cuenta"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["observacion_cuenta"]);
	}
	function origen_cuenta($v=false){
		if($v!==false){$this->mCampos["origen_cuenta"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["origen_cuenta"]);
	}
	function tipo_titulo($v=false){
		if($v!==false){$this->mCampos["tipo_titulo"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["tipo_titulo"]);
	}
	function tipo_subproducto($v=false){
		if($v!==false){$this->mCampos["tipo_subproducto"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["tipo_subproducto"]);
	}
	function nombre_mancomunado1($v=false){
		if($v!==false){$this->mCampos["nombre_mancomunado1"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["nombre_mancomunado1"]);
	}
	function nombre_mancomunado2($v=false){
		if($v!==false){$this->mCampos["nombre_mancomunado2"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["nombre_mancomunado2"]);
	}
	function minimo_mancomunantes($v=false){
		if($v!==false){$this->mCampos["minimo_mancomunantes"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["minimo_mancomunantes"]);
	}
	function saldo_conciliado($v=false){
		if($v!==false){$this->mCampos["saldo_conciliado"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["saldo_conciliado"]);
	}
	function fecha_conciliada($v=false){
		if($v!==false){$this->mCampos["fecha_conciliada"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["fecha_conciliada"]);
	}
	function sucursal($v=false){
		if($v!==false){$this->mCampos["sucursal"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["sucursal"]);
	}
	function ultimo_sdpm($v=false){
		if($v!==false){$this->mCampos["ultimo_sdpm"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["ultimo_sdpm"]);
	}
	function oficial_de_captacion($v=false){
		if($v!==false){$this->mCampos["oficial_de_captacion"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["oficial_de_captacion"]);
	}
	function cuenta_de_intereses($v=false){
		if($v!==false){$this->mCampos["cuenta_de_intereses"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["cuenta_de_intereses"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}

class cCaptacion_subproductos {
	private $mCampos	= array(
			"idcaptacion_subproductos" => array("N"=>"idcaptacion_subproductos","T"=>"INT","V"=>"","L"=>10),
			"descripcion_subproductos" => array("N"=>"descripcion_subproductos","T"=>"VARCHAR","V"=>"","L"=>45),
			"descripcion_completa" => array("N"=>"descripcion_completa","T"=>"VARCHAR","V"=>"","L"=>200),
			"fecha_alta" => array("N"=>"fecha_alta","T"=>"DATE","V"=>"2005-12-31","L"=>0),
			"fecha_baja" => array("N"=>"fecha_baja","T"=>"DATE","V"=>"2029-12-31","L"=>0),
			"algoritmo_de_premio" => array("N"=>"algoritmo_de_premio","T"=>"TEXT","V"=>"","L"=>0),
			"algoritmo_de_tasa_incremental" => array("N"=>"algoritmo_de_tasa_incremental","T"=>"TEXT","V"=>"","L"=>0),
			"tipo_de_cuenta" => array("N"=>"tipo_de_cuenta","T"=>"INT","V"=>"10","L"=>4),
			"nombre_del_contrato" => array("N"=>"nombre_del_contrato","T"=>"VARCHAR","V"=>"","L"=>100),
			"contable_movimientos" => array("N"=>"contable_movimientos","T"=>"VARCHAR","V"=>"","L"=>20),
			"contable_intereses_por_pagar" => array("N"=>"contable_intereses_por_pagar","T"=>"VARCHAR","V"=>"","L"=>20),
			"contable_gastos_por_intereses" => array("N"=>"contable_gastos_por_intereses","T"=>"VARCHAR","V"=>"","L"=>20),
			"contable_cuentas_castigadas" => array("N"=>"contable_cuentas_castigadas","T"=>"VARCHAR","V"=>"0","L"=>20),
			"metodo_de_abono_de_interes" => array("N"=>"metodo_de_abono_de_interes","T"=>"ENUM","V"=>"|AL_FIN_DE_MES|AL_VENCIMIENTO|","L"=>0),
			"destino_del_interes" => array("N"=>"destino_del_interes","T"=>"ENUM","V"=>"|CUENTA|NUEVA|CUENTA_INTERESES|","L"=>0),
			"algoritmo_modificador_del_interes" => array("N"=>"algoritmo_modificador_del_interes","T"=>"TEXT","V"=>"","L"=>0),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "captacion_subproductos";}
	function getKey(){ return "idcaptacion_subproductos";}
	function idcaptacion_subproductos($v = false){ if($v !== false){$this->mCampos["idcaptacion_subproductos"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcaptacion_subproductos"]);}
	function descripcion_subproductos($v = false){ if($v !== false){$this->mCampos["descripcion_subproductos"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_subproductos"]);}
	function descripcion_completa($v = false){ if($v !== false){$this->mCampos["descripcion_completa"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_completa"]);}
	function fecha_alta($v = false){ if($v !== false){$this->mCampos["fecha_alta"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_alta"]);}
	function fecha_baja($v = false){ if($v !== false){$this->mCampos["fecha_baja"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_baja"]);}
	function algoritmo_de_premio($v = false){ if($v !== false){$this->mCampos["algoritmo_de_premio"]["V"] =  $v; } return new MQLCampo($this->mCampos["algoritmo_de_premio"]);}
	function algoritmo_de_tasa_incremental($v = false){ if($v !== false){$this->mCampos["algoritmo_de_tasa_incremental"]["V"] =  $v; } return new MQLCampo($this->mCampos["algoritmo_de_tasa_incremental"]);}
	function tipo_de_cuenta($v = false){ if($v !== false){$this->mCampos["tipo_de_cuenta"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_cuenta"]);}
	function nombre_del_contrato($v = false){ if($v !== false){$this->mCampos["nombre_del_contrato"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_del_contrato"]);}
	function contable_movimientos($v = false){ if($v !== false){$this->mCampos["contable_movimientos"]["V"] =  $v; } return new MQLCampo($this->mCampos["contable_movimientos"]);}
	function contable_intereses_por_pagar($v = false){ if($v !== false){$this->mCampos["contable_intereses_por_pagar"]["V"] =  $v; } return new MQLCampo($this->mCampos["contable_intereses_por_pagar"]);}
	function contable_gastos_por_intereses($v = false){ if($v !== false){$this->mCampos["contable_gastos_por_intereses"]["V"] =  $v; } return new MQLCampo($this->mCampos["contable_gastos_por_intereses"]);}
	function contable_cuentas_castigadas($v = false){ if($v !== false){$this->mCampos["contable_cuentas_castigadas"]["V"] =  $v; } return new MQLCampo($this->mCampos["contable_cuentas_castigadas"]);}
	function metodo_de_abono_de_interes($v = false){ if($v !== false){$this->mCampos["metodo_de_abono_de_interes"]["V"] =  $v; } return new MQLCampo($this->mCampos["metodo_de_abono_de_interes"]);}
	function destino_del_interes($v = false){ if($v !== false){$this->mCampos["destino_del_interes"]["V"] =  $v; } return new MQLCampo($this->mCampos["destino_del_interes"]);}
	function algoritmo_modificador_del_interes($v = false){ if($v !== false){$this->mCampos["algoritmo_modificador_del_interes"]["V"] =  $v; } return new MQLCampo($this->mCampos["algoritmo_modificador_del_interes"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

class cCaptacion_tasas {
	private $mCampos	= array(
			"idcaptacion_tasas" => array("N"=>"idcaptacion_tasas","T"=>"INT","V"=>"","L"=>4),
			"tasa_efectiva" => array("N"=>"tasa_efectiva","T"=>"FLOAT","V"=>"0.000","L"=>7),
			"modalidad_cuenta" => array("N"=>"modalidad_cuenta","T"=>"INT","V"=>"0","L"=>4),
			"monto_mayor_a" => array("N"=>"monto_mayor_a","T"=>"FLOAT","V"=>"0.00","L"=>25),
			"monto_menor_a" => array("N"=>"monto_menor_a","T"=>"FLOAT","V"=>"0.00","L"=>25),
			"dias_mayor_a" => array("N"=>"dias_mayor_a","T"=>"INT","V"=>"0","L"=>4),
			"dias_menor_a" => array("N"=>"dias_menor_a","T"=>"INT","V"=>"0","L"=>4),
			"subproducto" => array("N"=>"subproducto","T"=>"INT","V"=>"0","L"=>4),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "captacion_tasas";}
	function getKey(){ return "idcaptacion_tasas";}
	function idcaptacion_tasas($v = false){ if($v !== false){$this->mCampos["idcaptacion_tasas"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcaptacion_tasas"]);}
	function tasa_efectiva($v = false){ if($v !== false){$this->mCampos["tasa_efectiva"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_efectiva"]);}
	function modalidad_cuenta($v = false){ if($v !== false){$this->mCampos["modalidad_cuenta"]["V"] =  $v; } return new MQLCampo($this->mCampos["modalidad_cuenta"]);}
	function monto_mayor_a($v = false){ if($v !== false){$this->mCampos["monto_mayor_a"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_mayor_a"]);}
	function monto_menor_a($v = false){ if($v !== false){$this->mCampos["monto_menor_a"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_menor_a"]);}
	function dias_mayor_a($v = false){ if($v !== false){$this->mCampos["dias_mayor_a"]["V"] =  $v; } return new MQLCampo($this->mCampos["dias_mayor_a"]);}
	function dias_menor_a($v = false){ if($v !== false){$this->mCampos["dias_menor_a"]["V"] =  $v; } return new MQLCampo($this->mCampos["dias_menor_a"]);}
	function subproducto($v = false){ if($v !== false){$this->mCampos["subproducto"]["V"] =  $v; } return new MQLCampo($this->mCampos["subproducto"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}


/*	ORM: Tabla:	catalogos_localidades	-	Generado:	[23/4/2014 11:55]	*/
class cCatalogos_localidades {
	private $mCampos	= array(
			"clave_unica" => array("N"=>"clave_unica","T"=>"INT","V"=>"","L"=>11),
			"nombre_de_la_localidad" => array("N"=>"nombre_de_la_localidad","T"=>"VARCHAR","V"=>"","L"=>45),
			"clave_de_estado" => array("N"=>"clave_de_estado","T"=>"INT","V"=>"0","L"=>4),
			"clave_de_municipio" => array("N"=>"clave_de_municipio","T"=>"INT","V"=>"0","L"=>10),
			"clave_de_localidad" => array("N"=>"clave_de_localidad","T"=>"VARCHAR","V"=>"0","L"=>20),
			"longitud" => array("N"=>"longitud","T"=>"VARCHAR","V"=>"","L"=>45),
			"altitud" => array("N"=>"altitud","T"=>"VARCHAR","V"=>"","L"=>45),
			"latitud" => array("N"=>"latitud","T"=>"VARCHAR","V"=>"","L"=>45),
			"clave_de_pais" => array("N"=>"clave_de_pais","T"=>"VARCHAR","V"=>"MX","L"=>20),

	);
	function __construct(){}
	function get(){ return "catalogos_localidades";}
	function getKey(){ return "clave_unica";}

	function clave_unica($v=false){
		if($v!==false){$this->mCampos["clave_unica"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["clave_unica"]);
	}
	function nombre_de_la_localidad($v=false){
		if($v!==false){$this->mCampos["nombre_de_la_localidad"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["nombre_de_la_localidad"]);
	}
	function clave_de_estado($v=false){
		if($v!==false){$this->mCampos["clave_de_estado"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["clave_de_estado"]);
	}
	function clave_de_municipio($v=false){
		if($v!==false){$this->mCampos["clave_de_municipio"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["clave_de_municipio"]);
	}
	function clave_de_localidad($v=false){
		if($v!==false){$this->mCampos["clave_de_localidad"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["clave_de_localidad"]);
	}
	function longitud($v=false){
		if($v!==false){$this->mCampos["longitud"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["longitud"]);
	}
	function altitud($v=false){
		if($v!==false){$this->mCampos["altitud"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["altitud"]);
	}
	function latitud($v=false){
		if($v!==false){$this->mCampos["latitud"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["latitud"]);
	}
	function clave_de_pais($v=false){
		if($v!==false){$this->mCampos["clave_de_pais"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["clave_de_pais"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}


/*	ORM: Tabla:	personas_actividad_economica_tipos	-	Generado:	[15/12/2014 09:17]	*/
class cPersonas_actividad_economica_tipos {
	private $mCampos	= array(
			"clave_interna" => array("N"=>"clave_interna","T"=>"BIGINT","V"=>"","L"=>20),
			"clave_de_actividad" => array("N"=>"clave_de_actividad","T"=>"VARCHAR","V"=>"","L"=>20),
			"nombre_de_la_actividad" => array("N"=>"nombre_de_la_actividad","T"=>"VARCHAR","V"=>"","L"=>200),
			"descripcion_detallada" => array("N"=>"descripcion_detallada","T"=>"LONGTEXT","V"=>"","L"=>0),
			"productos" => array("N"=>"productos","T"=>"VARCHAR","V"=>"","L"=>200),
			"clasificacion" => array("N"=>"clasificacion","T"=>"VARCHAR","V"=>"","L"=>20),
			"clave_de_superior" => array("N"=>"clave_de_superior","T"=>"BIGINT","V"=>"0","L"=>20),
			"nivel_de_riesgo" => array("N"=>"nivel_de_riesgo","T"=>"INT","V"=>"1","L"=>4),
			"califica_para_pep" => array("N"=>"califica_para_pep","T"=>"INT","V"=>"0","L"=>2)

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "personas_actividad_economica_tipos";}
	function getKey(){ return "clave_interna";}
	function clave_interna($v = false){ if($v !== false){$this->mCampos["clave_interna"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_interna"]);}
	function clave_de_actividad($v = false){ if($v !== false){$this->mCampos["clave_de_actividad"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_actividad"]);}
	function nombre_de_la_actividad($v = false){ if($v !== false){$this->mCampos["nombre_de_la_actividad"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_de_la_actividad"]);}
	function descripcion_detallada($v = false){ if($v !== false){$this->mCampos["descripcion_detallada"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_detallada"]);}
	function productos($v = false){ if($v !== false){$this->mCampos["productos"]["V"] =  $v; } return new MQLCampo($this->mCampos["productos"]);}
	function clasificacion($v = false){ if($v !== false){$this->mCampos["clasificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["clasificacion"]);}
	function clave_de_superior($v = false){ if($v !== false){$this->mCampos["clave_de_superior"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_superior"]);}
	function nivel_de_riesgo($v = false){ if($v !== false){$this->mCampos["nivel_de_riesgo"]["V"] =  $v; } return new MQLCampo($this->mCampos["nivel_de_riesgo"]);}
	function califica_para_pep($v = false){ if($v !== false){$this->mCampos["califica_para_pep"]["V"] =  $v; } return new MQLCampo($this->mCampos["califica_para_pep"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}


/*	ORM: Tabla:	personas_domicilios_paises	-	Generado:	[23/4/2014 11:43]	*/
class cPersonas_domicilios_paises {
	private $mCampos	= array(
			"clave_de_control" => array("N"=>"clave_de_control","T"=>"VARCHAR","V"=>"","L"=>10),
			"nombre_oficial" => array("N"=>"nombre_oficial","T"=>"VARCHAR","V"=>"","L"=>150),
			"es_paraiso_fiscal" => array("N"=>"es_paraiso_fiscal","T"=>"INT","V"=>"0","L"=>11),
			"es_considerado_riesgo" => array("N"=>"es_considerado_riesgo","T"=>"INT","V"=>"0","L"=>11),
			"clave_numerica" => array("N"=>"clave_numerica","T"=>"INT","V"=>"","L"=>11),
			"clave_alfanumerica" => array("N"=>"clave_alfanumerica","T"=>"VARCHAR","V"=>"","L"=>10),

	);
	function __construct(){}
	function get(){ return "personas_domicilios_paises";}
	function getKey(){ return "clave_de_control";}
	function clave_de_control($v=false){
		if($v!==false){$this->mCampos["clave_de_control"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["clave_de_control"]);
	}
	function nombre_oficial($v=false){
		if($v!==false){$this->mCampos["nombre_oficial"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["nombre_oficial"]);
	}
	function es_paraiso_fiscal($v=false){
		if($v!==false){$this->mCampos["es_paraiso_fiscal"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["es_paraiso_fiscal"]);
	}
	function es_considerado_riesgo($v=false){
		if($v!==false){$this->mCampos["es_considerado_riesgo"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["es_considerado_riesgo"]);
	}
	function clave_numerica($v=false){
		if($v!==false){$this->mCampos["clave_numerica"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["clave_numerica"]);
	}
	function clave_alfanumerica($v=false){
		if($v!==false){$this->mCampos["clave_alfanumerica"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["clave_alfanumerica"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}


/*	ORM: Tabla:	sistema_programacion_de_avisos	-	Generado:	[13/5/2014 16:30]	*/
class cSistema_programacion_de_avisos {
	private $mCampos	= array(
			"idprograma" => array("N"=>"idprograma","T"=>"INT","V"=>"","L"=>11),
			"nombre_del_aviso" => array("N"=>"nombre_del_aviso","T"=>"VARCHAR","V"=>"","L"=>100),
			"forma_de_creacion" => array("N"=>"forma_de_creacion","T"=>"VARCHAR","V"=>"","L"=>30),
			"programacion" => array("N"=>"programacion","T"=>"TINYTEXT","V"=>"","L"=>0),
			"destinatarios" => array("N"=>"destinatarios","T"=>"TINYTEXT","V"=>"","L"=>0),
			"microformato" => array("N"=>"microformato","T"=>"TINYTEXT","V"=>"","L"=>0),
			"tipo_de_medios" => array("N"=>"tipo_de_medios","T"=>"VARCHAR","V"=>"","L"=>50),
			"intent_check" => array("N"=>"intent_check","T"=>"TINYTEXT","V"=>"","L"=>0),
			"intent_command" => array("N"=>"intent_command","T"=>"TINYTEXT","V"=>"","L"=>0),

	);
	function __construct(){}
	function get(){ return "sistema_programacion_de_avisos";}
	function getKey(){ return "idprograma";}
	function idprograma($v=false){
		if($v!==false){$this->mCampos["idprograma"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["idprograma"]);
	}
	function nombre_del_aviso($v=false){
		if($v!==false){$this->mCampos["nombre_del_aviso"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["nombre_del_aviso"]);
	}
	function forma_de_creacion($v=false){
		if($v!==false){$this->mCampos["forma_de_creacion"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["forma_de_creacion"]);
	}
	function programacion($v=false){
		if($v!==false){$this->mCampos["programacion"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["programacion"]);
	}
	function destinatarios($v=false){
		if($v!==false){$this->mCampos["destinatarios"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["destinatarios"]);
	}
	function microformato($v=false){
		if($v!==false){$this->mCampos["microformato"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["microformato"]);
	}
	function tipo_de_medios($v=false){
		if($v!==false){$this->mCampos["tipo_de_medios"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["tipo_de_medios"]);
	}
	function intent_check($v=false){
		if($v!==false){$this->mCampos["intent_check"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["intent_check"]);
	}
	function intent_command($v=false){
		if($v!==false){$this->mCampos["intent_command"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["intent_command"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}



/*	ORM: Tabla:	entidad_autorizaciones	-	Generado:	[08/5/2014 15:39]	*/
class cEntidad_autorizaciones {
	private $mCampos	= array(
			"clave_de_autorizacion" => array("N"=>"clave_de_autorizacion","T"=>"INT","V"=>"","L"=>11),
			"uuid" => array("N"=>"uuid","T"=>"VARCHAR","V"=>"","L"=>150),
			"tipo_de_accion" => array("N"=>"tipo_de_accion","T"=>"INT","V"=>"","L"=>11),
			"fecha" => array("N"=>"fecha","T"=>"BIGINT","V"=>"","L"=>20),
			"hora" => array("N"=>"hora","T"=>"VARCHAR","V"=>"","L"=>45),
			"usuario_de_origen" => array("N"=>"usuario_de_origen","T"=>"INT","V"=>"","L"=>11),
			"usuario_de_proceso" => array("N"=>"usuario_de_proceso","T"=>"INT","V"=>"","L"=>11),
			"tipo_de_documento" => array("N"=>"tipo_de_documento","T"=>"INT","V"=>"","L"=>11),
			"numero_de_documento" => array("N"=>"numero_de_documento","T"=>"BIGINT","V"=>"","L"=>20),
			"contrato" => array("N"=>"contrato","T"=>"BIGINT","V"=>"","L"=>20),
			"persona" => array("N"=>"persona","T"=>"BIGINT","V"=>"","L"=>20),
			"fecha_de_origen" => array("N"=>"fecha_de_origen","T"=>"BIGINT","V"=>"","L"=>20),
			"usuario_de_autorizacion" => array("N"=>"usuario_de_autorizacion","T"=>"INT","V"=>"","L"=>11),
			"firma_de_autorizacion" => array("N"=>"firma_de_autorizacion","T"=>"TINYTEXT","V"=>"","L"=>0),
			"metadata" => array("N"=>"metadata","T"=>"TINYTEXT","V"=>"","L"=>0),
			"estado_actual" => array("N"=>"estado_actual","T"=>"INT","V"=>"","L"=>4),

	);
	function __construct(){}
	function get(){ return "entidad_autorizaciones";}
	function getKey(){ return "clave_de_autorizacion";}
	function clave_de_autorizacion($v=false){
		if($v!==false){$this->mCampos["clave_de_autorizacion"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["clave_de_autorizacion"]);
	}
	function uuid($v=false){
		if($v!==false){$this->mCampos["uuid"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["uuid"]);
	}
	function tipo_de_accion($v=false){
		if($v!==false){$this->mCampos["tipo_de_accion"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["tipo_de_accion"]);
	}
	function fecha($v=false){
		if($v!==false){$this->mCampos["fecha"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["fecha"]);
	}
	function hora($v=false){
		if($v!==false){$this->mCampos["hora"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["hora"]);
	}
	function usuario_de_origen($v=false){
		if($v!==false){$this->mCampos["usuario_de_origen"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["usuario_de_origen"]);
	}
	function usuario_de_proceso($v=false){
		if($v!==false){$this->mCampos["usuario_de_proceso"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["usuario_de_proceso"]);
	}
	function tipo_de_documento($v=false){
		if($v!==false){$this->mCampos["tipo_de_documento"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["tipo_de_documento"]);
	}
	function numero_de_documento($v=false){
		if($v!==false){$this->mCampos["numero_de_documento"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["numero_de_documento"]);
	}
	function contrato($v=false){
		if($v!==false){$this->mCampos["contrato"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["contrato"]);
	}
	function persona($v=false){
		if($v!==false){$this->mCampos["persona"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["persona"]);
	}
	function fecha_de_origen($v=false){
		if($v!==false){$this->mCampos["fecha_de_origen"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["fecha_de_origen"]);
	}
	function usuario_de_autorizacion($v=false){
		if($v!==false){$this->mCampos["usuario_de_autorizacion"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["usuario_de_autorizacion"]);
	}
	function firma_de_autorizacion($v=false){
		if($v!==false){$this->mCampos["firma_de_autorizacion"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["firma_de_autorizacion"]);
	}
	function metadata($v=false){
		if($v!==false){$this->mCampos["metadata"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["metadata"]);
	}
	function estado_actual($v=false){
		if($v!==false){$this->mCampos["estado_actual"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["estado_actual"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}

/*	ORM: Tabla:	entidad_reglas	-	Generado:	[08/5/2014 15:40]	*/
class cEntidad_reglas {
	private $mCampos	= array(
			"identidad_reglas" => array("N"=>"identidad_reglas","T"=>"INT","V"=>"","L"=>11),
			"contexto" => array("N"=>"contexto","T"=>"VARCHAR","V"=>"","L"=>20),
			"nombre" => array("N"=>"nombre","T"=>"VARCHAR","V"=>"","L"=>45),
			"evento" => array("N"=>"evento","T"=>"VARCHAR","V"=>"","L"=>40),
			"sujetos" => array("N"=>"sujetos","T"=>"VARCHAR","V"=>"","L"=>100),
			"reglas" => array("N"=>"reglas","T"=>"TINYTEXT","V"=>"","L"=>0),
			"metadata" => array("N"=>"metadata","T"=>"TINYTEXT","V"=>"","L"=>0),

	);
	function __construct(){}
	function get(){ return "entidad_reglas";}
	function getKey(){ return "identidad_reglas";}
	function identidad_reglas($v=false){
		if($v!==false){$this->mCampos["identidad_reglas"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["identidad_reglas"]);
	}
	function contexto($v=false){
		if($v!==false){$this->mCampos["contexto"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["contexto"]);
	}
	function nombre($v=false){
		if($v!==false){$this->mCampos["nombre"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["nombre"]);
	}
	function evento($v=false){
		if($v!==false){$this->mCampos["evento"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["evento"]);
	}
	function sujetos($v=false){
		if($v!==false){$this->mCampos["sujetos"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["sujetos"]);
	}
	function reglas($v=false){
		if($v!==false){$this->mCampos["reglas"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["reglas"]);
	}
	function metadata($v=false){
		if($v!==false){$this->mCampos["metadata"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["metadata"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}

/*	ORM: Tabla:	socios_grupossolidarios	-	Generado:	[04/9/2014 11:03]	*/
class cSocios_grupossolidarios {
	private $mCampos	= array(
			"idsocios_grupossolidarios" => array("N"=>"idsocios_grupossolidarios","T"=>"BIGINT","V"=>"","L"=>20),
			"nombre_gruposolidario" => array("N"=>"nombre_gruposolidario","T"=>"VARCHAR","V"=>"","L"=>100),
			"colonia_gruposolidario" => array("N"=>"colonia_gruposolidario","T"=>"INT","V"=>"0","L"=>11),
			"direccion_gruposolidario" => array("N"=>"direccion_gruposolidario","T"=>"VARCHAR","V"=>"","L"=>100),
			"representante_numerosocio" => array("N"=>"representante_numerosocio","T"=>"BIGINT","V"=>"1","L"=>20),
			"representante_nombrecompleto" => array("N"=>"representante_nombrecompleto","T"=>"VARCHAR","V"=>"","L"=>100),
			"grupo_solidario" => array("N"=>"grupo_solidario","T"=>"BIGINT","V"=>"","L"=>20),
			"vocalvigilancia_numerosocio" => array("N"=>"vocalvigilancia_numerosocio","T"=>"BIGINT","V"=>"","L"=>20),
			"vocalvigilancia_nombrecompleto" => array("N"=>"vocalvigilancia_nombrecompleto","T"=>"VARCHAR","V"=>"","L"=>100),
			"estatusactual" => array("N"=>"estatusactual","T"=>"INT","V"=>"10","L"=>4),
			"nivel_ministracion" => array("N"=>"nivel_ministracion","T"=>"INT","V"=>"1","L"=>2),
			"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"matriz","L"=>20),
			"fecha_de_alta" => array("N"=>"fecha_de_alta","T"=>"DATE","V"=>"2007-04-01","L"=>0),
			"clave_de_persona" => array("N"=>"clave_de_persona","T"=>"BIGINT","V"=>"1","L"=>20),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "socios_grupossolidarios";}
	function getKey(){ return "idsocios_grupossolidarios";}
	function idsocios_grupossolidarios($v = false){ if($v !== false){$this->mCampos["idsocios_grupossolidarios"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsocios_grupossolidarios"]);}
	function nombre_gruposolidario($v = false){ if($v !== false){$this->mCampos["nombre_gruposolidario"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_gruposolidario"]);}
	function colonia_gruposolidario($v = false){ if($v !== false){$this->mCampos["colonia_gruposolidario"]["V"] =  $v; } return new MQLCampo($this->mCampos["colonia_gruposolidario"]);}
	function direccion_gruposolidario($v = false){ if($v !== false){$this->mCampos["direccion_gruposolidario"]["V"] =  $v; } return new MQLCampo($this->mCampos["direccion_gruposolidario"]);}
	function representante_numerosocio($v = false){ if($v !== false){$this->mCampos["representante_numerosocio"]["V"] =  $v; } return new MQLCampo($this->mCampos["representante_numerosocio"]);}
	function representante_nombrecompleto($v = false){ if($v !== false){$this->mCampos["representante_nombrecompleto"]["V"] =  $v; } return new MQLCampo($this->mCampos["representante_nombrecompleto"]);}
	function grupo_solidario($v = false){ if($v !== false){$this->mCampos["grupo_solidario"]["V"] =  $v; } return new MQLCampo($this->mCampos["grupo_solidario"]);}
	function vocalvigilancia_numerosocio($v = false){ if($v !== false){$this->mCampos["vocalvigilancia_numerosocio"]["V"] =  $v; } return new MQLCampo($this->mCampos["vocalvigilancia_numerosocio"]);}
	function vocalvigilancia_nombrecompleto($v = false){ if($v !== false){$this->mCampos["vocalvigilancia_nombrecompleto"]["V"] =  $v; } return new MQLCampo($this->mCampos["vocalvigilancia_nombrecompleto"]);}
	function estatusactual($v = false){ if($v !== false){$this->mCampos["estatusactual"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatusactual"]);}
	function nivel_ministracion($v = false){ if($v !== false){$this->mCampos["nivel_ministracion"]["V"] =  $v; } return new MQLCampo($this->mCampos["nivel_ministracion"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function fecha_de_alta($v = false){ if($v !== false){$this->mCampos["fecha_de_alta"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_alta"]);}
	function clave_de_persona($v = false){ if($v !== false){$this->mCampos["clave_de_persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_persona"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	socios_relaciones	-	Generado:	[22/5/2014 13:04]	*/
class cSocios_relaciones {
	private $mCampos	= array(
			"idsocios_relaciones" => array("N"=>"idsocios_relaciones","T"=>"INT","V"=>"","L"=>10),
			"socio_relacionado" => array("N"=>"socio_relacionado","T"=>"BIGINT","V"=>"1","L"=>20),
			"credito_relacionado" => array("N"=>"credito_relacionado","T"=>"BIGINT","V"=>"1","L"=>20),
			"tipo_relacion" => array("N"=>"tipo_relacion","T"=>"INT","V"=>"99","L"=>4),
			"numero_socio" => array("N"=>"numero_socio","T"=>"BIGINT","V"=>"1","L"=>20),
			"nombres" => array("N"=>"nombres","T"=>"VARCHAR","V"=>"","L"=>45),
			"apellido_paterno" => array("N"=>"apellido_paterno","T"=>"VARCHAR","V"=>"","L"=>45),
			"apellido_materno" => array("N"=>"apellido_materno","T"=>"VARCHAR","V"=>"","L"=>45),
			"domicilio_completo" => array("N"=>"domicilio_completo","T"=>"VARCHAR","V"=>"","L"=>200),
			"telefono_residencia" => array("N"=>"telefono_residencia","T"=>"VARCHAR","V"=>"","L"=>25),
			"telefono_movil" => array("N"=>"telefono_movil","T"=>"VARCHAR","V"=>"","L"=>25),
			"fecha_nacimiento" => array("N"=>"fecha_nacimiento","T"=>"DATE","V"=>"0000-00-00","L"=>0),
			"monto_relacionado" => array("N"=>"monto_relacionado","T"=>"FLOAT","V"=>"0.00","L"=>25),
			"porcentaje_relacionado" => array("N"=>"porcentaje_relacionado","T"=>"FLOAT","V"=>"0.000","L"=>13),
			"fecha_alta" => array("N"=>"fecha_alta","T"=>"DATE","V"=>"0000-00-00","L"=>0),
			"curp" => array("N"=>"curp","T"=>"VARCHAR","V"=>"","L"=>25),
			"observaciones" => array("N"=>"observaciones","T"=>"VARCHAR","V"=>"","L"=>100),
			"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"99","L"=>4),
			"consanguinidad" => array("N"=>"consanguinidad","T"=>"INT","V"=>"99","L"=>4),
			"estatus" => array("N"=>"estatus","T"=>"INT","V"=>"99","L"=>4),
			"dependiente" => array("N"=>"dependiente","T"=>"INT","V"=>"2","L"=>4),
			"codigo" => array("N"=>"codigo","T"=>"BIGINT","V"=>"1","L"=>20),
			"ocupacion" => array("N"=>"ocupacion","T"=>"VARCHAR","V"=>"N/A","L"=>45),
			"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"MATRIZ","L"=>10),
			"eacp" => array("N"=>"eacp","T"=>"VARCHAR","V"=>"EN_TRAMITE","L"=>20),
			"calificacion_del_referente" => array("N"=>"calificacion_del_referente","T"=>"INT","V"=>"1","L"=>4),

	);
	function __construct(){}
	function get(){ return "socios_relaciones";}
	function getKey(){ return "idsocios_relaciones";}
	function idsocios_relaciones($v=false){
		if($v!==false){$this->mCampos["idsocios_relaciones"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["idsocios_relaciones"]);
	}
	function socio_relacionado($v=false){
		if($v!==false){$this->mCampos["socio_relacionado"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["socio_relacionado"]);
	}
	function credito_relacionado($v=false){
		if($v!==false){$this->mCampos["credito_relacionado"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["credito_relacionado"]);
	}
	function tipo_relacion($v=false){
		if($v!==false){$this->mCampos["tipo_relacion"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["tipo_relacion"]);
	}
	function numero_socio($v=false){
		if($v!==false){$this->mCampos["numero_socio"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["numero_socio"]);
	}
	function nombres($v=false){
		if($v!==false){$this->mCampos["nombres"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["nombres"]);
	}
	function apellido_paterno($v=false){
		if($v!==false){$this->mCampos["apellido_paterno"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["apellido_paterno"]);
	}
	function apellido_materno($v=false){
		if($v!==false){$this->mCampos["apellido_materno"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["apellido_materno"]);
	}
	function domicilio_completo($v=false){
		if($v!==false){$this->mCampos["domicilio_completo"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["domicilio_completo"]);
	}
	function telefono_residencia($v=false){
		if($v!==false){$this->mCampos["telefono_residencia"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["telefono_residencia"]);
	}
	function telefono_movil($v=false){
		if($v!==false){$this->mCampos["telefono_movil"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["telefono_movil"]);
	}
	function fecha_nacimiento($v=false){
		if($v!==false){$this->mCampos["fecha_nacimiento"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["fecha_nacimiento"]);
	}
	function monto_relacionado($v=false){
		if($v!==false){$this->mCampos["monto_relacionado"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["monto_relacionado"]);
	}
	function porcentaje_relacionado($v=false){
		if($v!==false){$this->mCampos["porcentaje_relacionado"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["porcentaje_relacionado"]);
	}
	function fecha_alta($v=false){
		if($v!==false){$this->mCampos["fecha_alta"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["fecha_alta"]);
	}
	function curp($v=false){
		if($v!==false){$this->mCampos["curp"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["curp"]);
	}
	function observaciones($v=false){
		if($v!==false){$this->mCampos["observaciones"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["observaciones"]);
	}
	function idusuario($v=false){
		if($v!==false){$this->mCampos["idusuario"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["idusuario"]);
	}
	function consanguinidad($v=false){
		if($v!==false){$this->mCampos["consanguinidad"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["consanguinidad"]);
	}
	function estatus($v=false){
		if($v!==false){$this->mCampos["estatus"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["estatus"]);
	}
	function dependiente($v=false){
		if($v!==false){$this->mCampos["dependiente"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["dependiente"]);
	}
	function codigo($v=false){
		if($v!==false){$this->mCampos["codigo"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["codigo"]);
	}
	function ocupacion($v=false){
		if($v!==false){$this->mCampos["ocupacion"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["ocupacion"]);
	}
	function sucursal($v=false){
		if($v!==false){$this->mCampos["sucursal"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["sucursal"]);
	}
	function eacp($v=false){
		if($v!==false){$this->mCampos["eacp"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["eacp"]);
	}
	function calificacion_del_referente($v=false){
		if($v!==false){$this->mCampos["calificacion_del_referente"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["calificacion_del_referente"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}


/*	ORM: Tabla:	general_log	-	Generado:	[26/5/2014 15:54]	*/
class cGeneral_log {
	private $mCampos	= array(
			"idgeneral_log" => array("N"=>"idgeneral_log","T"=>"INT","V"=>"","L"=>10),
			"fecha_log" => array("N"=>"fecha_log","T"=>"DATE","V"=>"","L"=>0),
			"hour_log" => array("N"=>"hour_log","T"=>"VARCHAR","V"=>"","L"=>20),
			"type_error" => array("N"=>"type_error","T"=>"INT","V"=>"","L"=>10),
			"usr_log" => array("N"=>"usr_log","T"=>"VARCHAR","V"=>"","L"=>60),
			"text_log" => array("N"=>"text_log","T"=>"MEDIUMTEXT","V"=>"","L"=>0),
			"ip_private" => array("N"=>"ip_private","T"=>"VARCHAR","V"=>"","L"=>20),
			"ip_proxy" => array("N"=>"ip_proxy","T"=>"VARCHAR","V"=>"","L"=>20),
			"ip_public" => array("N"=>"ip_public","T"=>"VARCHAR","V"=>"","L"=>20),

	);
	function __construct(){}
	function get(){ return "general_log";}
	function getKey(){ return "idgeneral_log";}
	function idgeneral_log($v=false){
		if($v!==false){$this->mCampos["idgeneral_log"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["idgeneral_log"]);
	}
	function fecha_log($v=false){
		if($v!==false){$this->mCampos["fecha_log"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["fecha_log"]);
	}
	function hour_log($v=false){
		if($v!==false){$this->mCampos["hour_log"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["hour_log"]);
	}
	function type_error($v=false){
		if($v!==false){$this->mCampos["type_error"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["type_error"]);
	}
	function usr_log($v=false){
		if($v!==false){$this->mCampos["usr_log"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["usr_log"]);
	}
	function text_log($v=false){
		if($v!==false){$this->mCampos["text_log"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["text_log"]);
	}
	function ip_private($v=false){
		if($v!==false){$this->mCampos["ip_private"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["ip_private"]);
	}
	function ip_proxy($v=false){
		if($v!==false){$this->mCampos["ip_proxy"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["ip_proxy"]);
	}
	function ip_public($v=false){
		if($v!==false){$this->mCampos["ip_public"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["ip_public"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}


/*	ORM: Tabla:	general_municipios	-	Generado:	[28/5/2014 18:01]	*/
class cGeneral_municipios {
	private $mCampos	= array(
			"idgeneral_municipios" => array("N"=>"idgeneral_municipios","T"=>"INT","V"=>"","L"=>11),
			"clave_de_entidad" => array("N"=>"clave_de_entidad","T"=>"INT","V"=>"4","L"=>4),
			"clave_de_municipio" => array("N"=>"clave_de_municipio","T"=>"INT","V"=>"1","L"=>6),
			"nombre_del_municipio" => array("N"=>"nombre_del_municipio","T"=>"VARCHAR","V"=>"","L"=>100),
			"habitantes" => array("N"=>"habitantes","T"=>"FLOAT","V"=>"","L"=>29),
			"indice_de_marginacion" => array("N"=>"indice_de_marginacion","T"=>"FLOAT","V"=>"0.000000","L"=>21),
			"grado_de_marginacion" => array("N"=>"grado_de_marginacion","T"=>"VARCHAR","V"=>"Alto","L"=>20),
			"lugar_nacional" => array("N"=>"lugar_nacional","T"=>"INT","V"=>"0","L"=>8),

	);
	function __construct(){}
	function get(){ return "general_municipios";}
	function getKey(){ return "idgeneral_municipios";}
	function idgeneral_municipios($v=false){
		if($v!==false){$this->mCampos["idgeneral_municipios"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["idgeneral_municipios"]);
	}
	function clave_de_entidad($v=false){
		if($v!==false){$this->mCampos["clave_de_entidad"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["clave_de_entidad"]);
	}
	function clave_de_municipio($v=false){
		if($v!==false){$this->mCampos["clave_de_municipio"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["clave_de_municipio"]);
	}
	function nombre_del_municipio($v=false){
		if($v!==false){$this->mCampos["nombre_del_municipio"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["nombre_del_municipio"]);
	}
	function habitantes($v=false){
		if($v!==false){$this->mCampos["habitantes"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["habitantes"]);
	}
	function indice_de_marginacion($v=false){
		if($v!==false){$this->mCampos["indice_de_marginacion"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["indice_de_marginacion"]);
	}
	function grado_de_marginacion($v=false){
		if($v!==false){$this->mCampos["grado_de_marginacion"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["grado_de_marginacion"]);
	}
	function lugar_nacional($v=false){
		if($v!==false){$this->mCampos["lugar_nacional"]["V"] =  $v; }
		return new MQLCampo($this->mCampos["lugar_nacional"]);
	}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){
		$mql	= new MQL($this->get(), $this->mCampos, $this->getKey());
		$this->mCampos	= $mql->setData($datos);
	}

}

/*	ORM: Tabla:	socios_otros_parametros	-	Generado:	[30/5/2014 13:25]	*/
class cSocios_otros_parametros {
	private $mCampos	= array(
			"idsocios_otros_parametros" => array("N"=>"idsocios_otros_parametros","T"=>"INT","V"=>"","L"=>11),
			"clave_de_persona" => array("N"=>"clave_de_persona","T"=>"BIGINT","V"=>"","L"=>20),
			"clave_del_parametro" => array("N"=>"clave_del_parametro","T"=>"VARCHAR","V"=>"","L"=>45),
			"valor_del_parametro" => array("N"=>"valor_del_parametro","T"=>"VARCHAR","V"=>"","L"=>45),
			"fecha_de_alta" => array("N"=>"fecha_de_alta","T"=>"DATE","V"=>"","L"=>0),
			"fecha_de_expiracion" => array("N"=>"fecha_de_expiracion","T"=>"DATE","V"=>"","L"=>0),
			"idusuario" => array("N"=>"idusuario","T"=>"VARCHAR","V"=>"","L"=>45),
			"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"","L"=>45),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "socios_otros_parametros";}
	function getKey(){ return "idsocios_otros_parametros";}
	function idsocios_otros_parametros($v = false){ if($v !== false){$this->mCampos["idsocios_otros_parametros"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsocios_otros_parametros"]);}
	function clave_de_persona($v = false){ if($v !== false){$this->mCampos["clave_de_persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_persona"]);}
	function clave_del_parametro($v = false){ if($v !== false){$this->mCampos["clave_del_parametro"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_del_parametro"]);}
	function valor_del_parametro($v = false){ if($v !== false){$this->mCampos["valor_del_parametro"]["V"] =  $v; } return new MQLCampo($this->mCampos["valor_del_parametro"]);}
	function fecha_de_alta($v = false){ if($v !== false){$this->mCampos["fecha_de_alta"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_alta"]);}
	function fecha_de_expiracion($v = false){ if($v !== false){$this->mCampos["fecha_de_expiracion"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_expiracion"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	socios_tipoingreso	-	Generado:	[20/8/2014 10:40]	*/
class cSocios_tipoingreso {
	private $mCampos	= array(
			"idsocios_tipoingreso" => array("N"=>"idsocios_tipoingreso","T"=>"INT","V"=>"0","L"=>4),
			"descripcion_tipoingreso" => array("N"=>"descripcion_tipoingreso","T"=>"VARCHAR","V"=>"","L"=>45),
			"descripcion_detallada" => array("N"=>"descripcion_detallada","T"=>"VARCHAR","V"=>"","L"=>200),
			"parte_social" => array("N"=>"parte_social","T"=>"FLOAT","V"=>"0.00","L"=>25),
			"parte_permanente" => array("N"=>"parte_permanente","T"=>"FLOAT","V"=>"0.00","L"=>25),
			"estado" => array("N"=>"estado","T"=>"INT","V"=>"1","L"=>4),
			"nivel_de_riesgo" => array("N"=>"nivel_de_riesgo","T"=>"INT","V"=>"10","L"=>4),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "socios_tipoingreso";}
	function getKey(){ return "idsocios_tipoingreso";}
	function idsocios_tipoingreso($v = false){ if($v !== false){$this->mCampos["idsocios_tipoingreso"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsocios_tipoingreso"]);}
	function descripcion_tipoingreso($v = false){ if($v !== false){$this->mCampos["descripcion_tipoingreso"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_tipoingreso"]);}
	function descripcion_detallada($v = false){ if($v !== false){$this->mCampos["descripcion_detallada"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_detallada"]);}
	function parte_social($v = false){ if($v !== false){$this->mCampos["parte_social"]["V"] =  $v; } return new MQLCampo($this->mCampos["parte_social"]);}
	function parte_permanente($v = false){ if($v !== false){$this->mCampos["parte_permanente"]["V"] =  $v; } return new MQLCampo($this->mCampos["parte_permanente"]);}
	function estado($v = false){ if($v !== false){$this->mCampos["estado"]["V"] =  $v; } return new MQLCampo($this->mCampos["estado"]);}
	function nivel_de_riesgo($v = false){ if($v !== false){$this->mCampos["nivel_de_riesgo"]["V"] =  $v; } return new MQLCampo($this->mCampos["nivel_de_riesgo"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	socios_aeconomica_dependencias	-	Generado:	[25/11/2014 17:14]	*/
class cSocios_aeconomica_dependencias {
	private $mCampos	= array(
			"idsocios_aeconomica_dependencias" => array("N"=>"idsocios_aeconomica_dependencias","T"=>"INT","V"=>"0","L"=>4),
			"descripcion_dependencia" => array("N"=>"descripcion_dependencia","T"=>"VARCHAR","V"=>"","L"=>100),
			"domicilio_completo" => array("N"=>"domicilio_completo","T"=>"VARCHAR","V"=>"","L"=>200),
			"directivo_principal" => array("N"=>"directivo_principal","T"=>"VARCHAR","V"=>"","L"=>100),
			"telefono" => array("N"=>"telefono","T"=>"VARCHAR","V"=>"","L"=>15),
			"fecha_preferente_de_pago" => array("N"=>"fecha_preferente_de_pago","T"=>"VARCHAR","V"=>"","L"=>20),
			"clave_de_persona" => array("N"=>"clave_de_persona","T"=>"BIGINT","V"=>"1","L"=>25),
			"clave_de_directivo" => array("N"=>"clave_de_directivo","T"=>"BIGINT","V"=>"1","L"=>25),
			"dias_de_avisos" => array("N"=>"dias_de_avisos","T"=>"VARCHAR","V"=>"","L"=>150),
			"periocidad_de_avisos" => array("N"=>"periocidad_de_avisos","T"=>"INT","V"=>"7","L"=>4),
			"ultimo_periodo_enviado" => array("N"=>"ultimo_periodo_enviado","T"=>"INT","V"=>"0","L"=>6),
			"fecha_de_envio" => array("N"=>"fecha_de_envio","T"=>"DATE","V"=>"","L"=>0),
			"oficial_que_cierra" => array("N"=>"oficial_que_cierra","T"=>"INT","V"=>"0","L"=>10),
			"nombre_corto" => array("N"=>"nombre_corto","T"=>"VARCHAR","V"=>"","L"=>20),
			"email_de_envio" => array("N"=>"email_de_envio","T"=>"VARCHAR","V"=>"","L"=>200),
			"producto_preferente" => array("N"=>"producto_preferente","T"=>"INT","V"=>"100","L"=>11),
			"formato_de_envio" => array("N"=>"formato_de_envio","T"=>"INT","V"=>"4001","L"=>6),
			"formato_de_relacion" => array("N"=>"formato_de_relacion","T"=>"INT","V"=>"4501","L"=>6),
			"dias_de_pago_nomina" => array("N"=>"dias_de_pago_nomina","T"=>"VARCHAR","V"=>"","L"=>150),
			"dias_de_liquidacion" => array("N"=>"dias_de_liquidacion","T"=>"VARCHAR","V"=>"","L"=>150),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "socios_aeconomica_dependencias";}
	function getKey(){ return "idsocios_aeconomica_dependencias";}

	function idsocios_aeconomica_dependencias($v = false){ if($v !== false){$this->mCampos["idsocios_aeconomica_dependencias"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsocios_aeconomica_dependencias"]);}
	function descripcion_dependencia($v = false){ if($v !== false){$this->mCampos["descripcion_dependencia"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_dependencia"]);}
	function domicilio_completo($v = false){ if($v !== false){$this->mCampos["domicilio_completo"]["V"] =  $v; } return new MQLCampo($this->mCampos["domicilio_completo"]);}
	function directivo_principal($v = false){ if($v !== false){$this->mCampos["directivo_principal"]["V"] =  $v; } return new MQLCampo($this->mCampos["directivo_principal"]);}
	function telefono($v = false){ if($v !== false){$this->mCampos["telefono"]["V"] =  $v; } return new MQLCampo($this->mCampos["telefono"]);}
	function fecha_preferente_de_pago($v = false){ if($v !== false){$this->mCampos["fecha_preferente_de_pago"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_preferente_de_pago"]);}
	function clave_de_persona($v = false){ if($v !== false){$this->mCampos["clave_de_persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_persona"]);}
	function clave_de_directivo($v = false){ if($v !== false){$this->mCampos["clave_de_directivo"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_directivo"]);}
	function dias_de_avisos($v = false){ if($v !== false){$this->mCampos["dias_de_avisos"]["V"] =  $v; } return new MQLCampo($this->mCampos["dias_de_avisos"]);}
	function periocidad_de_avisos($v = false){ if($v !== false){$this->mCampos["periocidad_de_avisos"]["V"] =  $v; } return new MQLCampo($this->mCampos["periocidad_de_avisos"]);}
	function ultimo_periodo_enviado($v = false){ if($v !== false){$this->mCampos["ultimo_periodo_enviado"]["V"] =  $v; } return new MQLCampo($this->mCampos["ultimo_periodo_enviado"]);}
	function fecha_de_envio($v = false){ if($v !== false){$this->mCampos["fecha_de_envio"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_envio"]);}
	function oficial_que_cierra($v = false){ if($v !== false){$this->mCampos["oficial_que_cierra"]["V"] =  $v; } return new MQLCampo($this->mCampos["oficial_que_cierra"]);}
	function nombre_corto($v = false){ if($v !== false){$this->mCampos["nombre_corto"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_corto"]);}
	function email_de_envio($v = false){ if($v !== false){$this->mCampos["email_de_envio"]["V"] =  $v; } return new MQLCampo($this->mCampos["email_de_envio"]);}
	function producto_preferente($v = false){ if($v !== false){$this->mCampos["producto_preferente"]["V"] =  $v; } return new MQLCampo($this->mCampos["producto_preferente"]);}
	function formato_de_envio($v = false){ if($v !== false){$this->mCampos["formato_de_envio"]["V"] =  $v; } return new MQLCampo($this->mCampos["formato_de_envio"]);}
	function formato_de_relacion($v = false){ if($v !== false){$this->mCampos["formato_de_relacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["formato_de_relacion"]);}
	function dias_de_pago_nomina($v = false){ if($v !== false){$this->mCampos["dias_de_pago_nomina"]["V"] =  $v; } return new MQLCampo($this->mCampos["dias_de_pago_nomina"]);}
	function dias_de_liquidacion($v = false){ if($v !== false){$this->mCampos["dias_de_liquidacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["dias_de_liquidacion"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	sistema_lenguaje	-	Generado:	[02/6/2014 22:33]	*/
class cSistema_lenguaje {
	private $mCampos	= array(
			"idsistema_lenguaje" => array("N"=>"idsistema_lenguaje","T"=>"INT","V"=>"","L"=>11),
			"equivalente" => array("N"=>"equivalente","T"=>"VARCHAR","V"=>"","L"=>50),
			"traduccion" => array("N"=>"traduccion","T"=>"VARCHAR","V"=>"","L"=>100),
			"extension" => array("N"=>"extension","T"=>"VARCHAR","V"=>"","L"=>200),
			"idioma" => array("N"=>"idioma","T"=>"VARCHAR","V"=>"MX","L"=>6),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "sistema_lenguaje";}
	function getKey(){ return "idsistema_lenguaje";}
	function idsistema_lenguaje($v = false){ if($v !== false){$this->mCampos["idsistema_lenguaje"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsistema_lenguaje"]);}
	function equivalente($v = false){ if($v !== false){$this->mCampos["equivalente"]["V"] =  $v; } return new MQLCampo($this->mCampos["equivalente"]);}
	function traduccion($v = false){ if($v !== false){$this->mCampos["traduccion"]["V"] =  $v; } return new MQLCampo($this->mCampos["traduccion"]);}
	function extension($v = false){ if($v !== false){$this->mCampos["extension"]["V"] =  $v; } return new MQLCampo($this->mCampos["extension"]);}
	function idioma($v = false){ if($v !== false){$this->mCampos["idioma"]["V"] =  $v; } return new MQLCampo($this->mCampos["idioma"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	contable_polizasdiarios	-	Generado:	[01/7/2014 13:00]	*/
class cContable_polizasdiarios {
	private $mCampos	= array(
			"idcontable_polizadiarios" => array("N"=>"idcontable_polizadiarios","T"=>"INT","V"=>"","L"=>10),
			"nombre_del_diario" => array("N"=>"nombre_del_diario","T"=>"VARCHAR","V"=>"","L"=>45),
			"registro_inicial" => array("N"=>"registro_inicial","T"=>"INT","V"=>"","L"=>11),
			"registro_final" => array("N"=>"registro_final","T"=>"INT","V"=>"","L"=>11),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "contable_polizasdiarios";}
	function getKey(){ return "idcontable_polizadiarios";}
	function idcontable_polizadiarios($v = false){ if($v !== false){$this->mCampos["idcontable_polizadiarios"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcontable_polizadiarios"]);}
	function nombre_del_diario($v = false){ if($v !== false){$this->mCampos["nombre_del_diario"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_del_diario"]);}
	function registro_inicial($v = false){ if($v !== false){$this->mCampos["registro_inicial"]["V"] =  $v; } return new MQLCampo($this->mCampos["registro_inicial"]);}
	function registro_final($v = false){ if($v !== false){$this->mCampos["registro_final"]["V"] =  $v; } return new MQLCampo($this->mCampos["registro_final"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	contable_catalogotipos	-	Generado:	[16/10/2014 12:41]	*/
class cContable_catalogotipos {
	private $mCampos	= array(
			"idcontable_catalogotipos" => array("N"=>"idcontable_catalogotipos","T"=>"VARCHAR","V"=>"","L"=>10),
			"nombre_del_tipo" => array("N"=>"nombre_del_tipo","T"=>"VARCHAR","V"=>"","L"=>100),
			"naturaleza" => array("N"=>"naturaleza","T"=>"INT","V"=>"","L"=>4),
			"naturaleza_del_sector" => array("N"=>"naturaleza_del_sector","T"=>"INT","V"=>"","L"=>4),
			"operador_del_sector" => array("N"=>"operador_del_sector","T"=>"INT","V"=>"1","L"=>4),
			"naturaleza_real" => array("N"=>"naturaleza_real","T"=>"INT","V"=>"1","L"=>4),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "contable_catalogotipos";}
	function getKey(){ return "idcontable_catalogotipos";}
	function idcontable_catalogotipos($v = false){ if($v !== false){$this->mCampos["idcontable_catalogotipos"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcontable_catalogotipos"]);}
	function nombre_del_tipo($v = false){ if($v !== false){$this->mCampos["nombre_del_tipo"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_del_tipo"]);}
	function naturaleza($v = false){ if($v !== false){$this->mCampos["naturaleza"]["V"] =  $v; } return new MQLCampo($this->mCampos["naturaleza"]);}
	function naturaleza_del_sector($v = false){ if($v !== false){$this->mCampos["naturaleza_del_sector"]["V"] =  $v; } return new MQLCampo($this->mCampos["naturaleza_del_sector"]);}
	function operador_del_sector($v = false){ if($v !== false){$this->mCampos["operador_del_sector"]["V"] =  $v; } return new MQLCampo($this->mCampos["operador_del_sector"]);}
	function naturaleza_real($v = false){ if($v !== false){$this->mCampos["naturaleza_real"]["V"] =  $v; } return new MQLCampo($this->mCampos["naturaleza_real"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	contable_movimientos	-	Generado:	[21/10/2014 14:04]	*/
class cContable_movimientos {
	private $mCampos	= array(
			"ejercicio" => array("N"=>"ejercicio","T"=>"INT","V"=>"2007","L"=>10),
			"periodo" => array("N"=>"periodo","T"=>"INT","V"=>"1","L"=>10),
			"tipopoliza" => array("N"=>"tipopoliza","T"=>"INT","V"=>"1","L"=>10),
			"numeropoliza" => array("N"=>"numeropoliza","T"=>"INT","V"=>"0","L"=>10),
			"numeromovimiento" => array("N"=>"numeromovimiento","T"=>"INT","V"=>"0","L"=>10),
			"numerocuenta" => array("N"=>"numerocuenta","T"=>"BIGINT","V"=>"0","L"=>20),
			"tipomovimiento" => array("N"=>"tipomovimiento","T"=>"INT","V"=>"0","L"=>11),
			"referencia" => array("N"=>"referencia","T"=>"VARCHAR","V"=>"","L"=>45),
			"importe" => array("N"=>"importe","T"=>"DOUBLE","V"=>"0.00","L"=>37),
			"diario" => array("N"=>"diario","T"=>"INT","V"=>"999","L"=>10),
			"moneda" => array("N"=>"moneda","T"=>"INT","V"=>"1","L"=>10),
			"concepto" => array("N"=>"concepto","T"=>"VARCHAR","V"=>"","L"=>100),
			"fecha" => array("N"=>"fecha","T"=>"DATE","V"=>"","L"=>0),
			"cargo" => array("N"=>"cargo","T"=>"DOUBLE","V"=>"0.00","L"=>37),
			"abono" => array("N"=>"abono","T"=>"DOUBLE","V"=>"0.00","L"=>37),
			"clave_unica" => array("N"=>"clave_unica","T"=>"INT","V"=>"","L"=>11),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "contable_movimientos";}
	function getKey(){ return "clave_unica";}
	function ejercicio($v = false){ if($v !== false){$this->mCampos["ejercicio"]["V"] =  $v; } return new MQLCampo($this->mCampos["ejercicio"]);}
	function periodo($v = false){ if($v !== false){$this->mCampos["periodo"]["V"] =  $v; } return new MQLCampo($this->mCampos["periodo"]);}
	function tipopoliza($v = false){ if($v !== false){$this->mCampos["tipopoliza"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipopoliza"]);}
	function numeropoliza($v = false){ if($v !== false){$this->mCampos["numeropoliza"]["V"] =  $v; } return new MQLCampo($this->mCampos["numeropoliza"]);}
	function numeromovimiento($v = false){ if($v !== false){$this->mCampos["numeromovimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["numeromovimiento"]);}
	function numerocuenta($v = false){ if($v !== false){$this->mCampos["numerocuenta"]["V"] =  $v; } return new MQLCampo($this->mCampos["numerocuenta"]);}
	function tipomovimiento($v = false){ if($v !== false){$this->mCampos["tipomovimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipomovimiento"]);}
	function referencia($v = false){ if($v !== false){$this->mCampos["referencia"]["V"] =  $v; } return new MQLCampo($this->mCampos["referencia"]);}
	function importe($v = false){ if($v !== false){$this->mCampos["importe"]["V"] =  $v; } return new MQLCampo($this->mCampos["importe"]);}
	function diario($v = false){ if($v !== false){$this->mCampos["diario"]["V"] =  $v; } return new MQLCampo($this->mCampos["diario"]);}
	function moneda($v = false){ if($v !== false){$this->mCampos["moneda"]["V"] =  $v; } return new MQLCampo($this->mCampos["moneda"]);}
	function concepto($v = false){ if($v !== false){$this->mCampos["concepto"]["V"] =  $v; } return new MQLCampo($this->mCampos["concepto"]);}
	function fecha($v = false){ if($v !== false){$this->mCampos["fecha"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha"]);}
	function cargo($v = false){ if($v !== false){$this->mCampos["cargo"]["V"] =  $v; } return new MQLCampo($this->mCampos["cargo"]);}
	function abono($v = false){ if($v !== false){$this->mCampos["abono"]["V"] =  $v; } return new MQLCampo($this->mCampos["abono"]);}
	function clave_unica($v = false){ if($v !== false){$this->mCampos["clave_unica"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_unica"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}


/*	ORM: Tabla:	contable_centrodecostos	-	Generado:	[01/7/2014 13:01]	*/
class cContable_centrodecostos {
	private $mCampos	= array(
			"idcontable_centrodecostos" => array("N"=>"idcontable_centrodecostos","T"=>"INT","V"=>"","L"=>10),
			"nombre_centrodecostos" => array("N"=>"nombre_centrodecostos","T"=>"VARCHAR","V"=>"","L"=>60),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "contable_centrodecostos";}
	function getKey(){ return "idcontable_centrodecostos";}
	function idcontable_centrodecostos($v = false){ if($v !== false){$this->mCampos["idcontable_centrodecostos"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcontable_centrodecostos"]);}
	function nombre_centrodecostos($v = false){ if($v !== false){$this->mCampos["nombre_centrodecostos"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_centrodecostos"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	contable_polizas_perfil	-	Generado:	[14/10/2014 13:33]	*/
class cContable_polizas_perfil {
	private $mCampos	= array(
			"idcontable_poliza_perfil" => array("N"=>"idcontable_poliza_perfil","T"=>"INT","V"=>"","L"=>11),
			"tipo_de_recibo" => array("N"=>"tipo_de_recibo","T"=>"INT","V"=>"","L"=>11),
			"tipo_de_operacion" => array("N"=>"tipo_de_operacion","T"=>"INT","V"=>"","L"=>11),
			"descripcion" => array("N"=>"descripcion","T"=>"VARCHAR","V"=>"","L"=>90),
			"operacion" => array("N"=>"operacion","T"=>"INT","V"=>"","L"=>11),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "contable_polizas_perfil";}
	function getKey(){ return "idcontable_poliza_perfil";}
	function idcontable_poliza_perfil($v = false){ if($v !== false){$this->mCampos["idcontable_poliza_perfil"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcontable_poliza_perfil"]);}
	function tipo_de_recibo($v = false){ if($v !== false){$this->mCampos["tipo_de_recibo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_recibo"]);}
	function tipo_de_operacion($v = false){ if($v !== false){$this->mCampos["tipo_de_operacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_operacion"]);}
	function descripcion($v = false){ if($v !== false){$this->mCampos["descripcion"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion"]);}
	function operacion($v = false){ if($v !== false){$this->mCampos["operacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["operacion"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}



/*	ORM: Tabla:	empresas_operaciones	-	Generado:	[09/7/2014 11:57]	*/
class cEmpresas_operaciones {
	private $mCampos	= array(
			"idempresas_operaciones" => array("N"=>"idempresas_operaciones","T"=>"INT","V"=>"","L"=>11),
			"clave_de_empresa" => array("N"=>"clave_de_empresa","T"=>"INT","V"=>"99","L"=>11),
			"periodo_marcado" => array("N"=>"periodo_marcado","T"=>"INT","V"=>"0","L"=>11),
			"tipo_de_operacion" => array("N"=>"tipo_de_operacion","T"=>"INT","V"=>"1","L"=>11),
			"fecha_de_operacion" => array("N"=>"fecha_de_operacion","T"=>"DATE","V"=>"","L"=>0),
			"monto" => array("N"=>"monto","T"=>"FLOAT","V"=>"0.00","L"=>25),
			"oficial" => array("N"=>"oficial","T"=>"INT","V"=>"99","L"=>11),
			"periocidad" => array("N"=>"periocidad","T"=>"INT","V"=>"7","L"=>11),
			"observaciones" => array("N"=>"observaciones","T"=>"VARCHAR","V"=>"","L"=>150),
			"fecha_de_cobro" => array("N"=>"fecha_de_cobro","T"=>"DATE","V"=>"","L"=>0),
			"fecha_inicial" => array("N"=>"fecha_inicial","T"=>"DATE","V"=>"","L"=>0),
			"fecha_final" => array("N"=>"fecha_final","T"=>"DATE","V"=>"","L"=>0),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "empresas_operaciones";}
	function getKey(){ return "idempresas_operaciones";}
	function idempresas_operaciones($v = false){ if($v !== false){$this->mCampos["idempresas_operaciones"]["V"] =  $v; } return new MQLCampo($this->mCampos["idempresas_operaciones"]);}
	function clave_de_empresa($v = false){ if($v !== false){$this->mCampos["clave_de_empresa"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_empresa"]);}
	function periodo_marcado($v = false){ if($v !== false){$this->mCampos["periodo_marcado"]["V"] =  $v; } return new MQLCampo($this->mCampos["periodo_marcado"]);}
	function tipo_de_operacion($v = false){ if($v !== false){$this->mCampos["tipo_de_operacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_operacion"]);}
	function fecha_de_operacion($v = false){ if($v !== false){$this->mCampos["fecha_de_operacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_operacion"]);}
	function monto($v = false){ if($v !== false){$this->mCampos["monto"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto"]);}
	function oficial($v = false){ if($v !== false){$this->mCampos["oficial"]["V"] =  $v; } return new MQLCampo($this->mCampos["oficial"]);}
	function periocidad($v = false){ if($v !== false){$this->mCampos["periocidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["periocidad"]);}
	function observaciones($v = false){ if($v !== false){$this->mCampos["observaciones"]["V"] =  $v; } return new MQLCampo($this->mCampos["observaciones"]);}
	function fecha_de_cobro($v = false){ if($v !== false){$this->mCampos["fecha_de_cobro"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_cobro"]);}
	function fecha_inicial($v = false){ if($v !== false){$this->mCampos["fecha_inicial"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_inicial"]);}
	function fecha_final($v = false){ if($v !== false){$this->mCampos["fecha_final"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_final"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}



/*	ORM: Tabla:	empresas_cobranza	-	Generado:	[12/7/2014 13:01]	*/
class cEmpresas_cobranza {
	private $mCampos	= array(
			"idempresas_cobranza" => array("N"=>"idempresas_cobranza","T"=>"INT","V"=>"","L"=>11),
			"clave_de_nomina" => array("N"=>"clave_de_nomina","T"=>"INT","V"=>"","L"=>11),
			"clave_de_credito" => array("N"=>"clave_de_credito","T"=>"BIGINT","V"=>"","L"=>20),
			"parcialidad" => array("N"=>"parcialidad","T"=>"INT","V"=>"0","L"=>11),
			"monto_enviado" => array("N"=>"monto_enviado","T"=>"FLOAT","V"=>"0.00","L"=>29),
			"observaciones" => array("N"=>"observaciones","T"=>"VARCHAR","V"=>"","L"=>100),
			"saldo_inicial" => array("N"=>"saldo_inicial","T"=>"FLOAT","V"=>"0.00","L"=>29),
			"estado" => array("N"=>"estado","T"=>"INT","V"=>"1","L"=>2)

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "empresas_cobranza";}
	function getKey(){ return "idempresas_cobranza";}
	function idempresas_cobranza($v = false){ if($v !== false){$this->mCampos["idempresas_cobranza"]["V"] =  $v; } return new MQLCampo($this->mCampos["idempresas_cobranza"]);}
	function clave_de_nomina($v = false){ if($v !== false){$this->mCampos["clave_de_nomina"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_nomina"]);}
	function clave_de_credito($v = false){ if($v !== false){$this->mCampos["clave_de_credito"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_credito"]);}
	function parcialidad($v = false){ if($v !== false){$this->mCampos["parcialidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["parcialidad"]);}
	function monto_enviado($v = false){ if($v !== false){$this->mCampos["monto_enviado"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_enviado"]);}
	function observaciones($v = false){ if($v !== false){$this->mCampos["observaciones"]["V"] =  $v; } return new MQLCampo($this->mCampos["observaciones"]);}
	function saldo_inicial($v = false){ if($v !== false){$this->mCampos["saldo_inicial"]["V"] =  $v; } return new MQLCampo($this->mCampos["saldo_inicial"]);}
	function estado($v = false){ if($v !== false){$this->mCampos["estado"]["V"] =  $v; } return new MQLCampo($this->mCampos["estado"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	seguimiento_llamadas	-	Generado:	[20/11/2014 17:51]	*/
class cSeguimiento_llamadas {
	private $mCampos	= array(
			"idseguimiento_llamadas" => array("N"=>"idseguimiento_llamadas","T"=>"INT","V"=>"","L"=>10),
			"numero_socio" => array("N"=>"numero_socio","T"=>"BIGINT","V"=>"1","L"=>20),
			"numero_solicitud" => array("N"=>"numero_solicitud","T"=>"BIGINT","V"=>"1","L"=>20),
			"deuda_total" => array("N"=>"deuda_total","T"=>"FLOAT","V"=>"0.00","L"=>25),
			"telefono_uno" => array("N"=>"telefono_uno","T"=>"VARCHAR","V"=>"0","L"=>30),
			"telefono_dos" => array("N"=>"telefono_dos","T"=>"VARCHAR","V"=>"0","L"=>30),
			"fecha_llamada" => array("N"=>"fecha_llamada","T"=>"DATE","V"=>"","L"=>0),
			"hora_llamada" => array("N"=>"hora_llamada","T"=>"TIME","V"=>"","L"=>0),
			"observaciones" => array("N"=>"observaciones","T"=>"TEXT","V"=>"","L"=>0),
			"estatus_llamada" => array("N"=>"estatus_llamada","T"=>"ENUM","V"=>"|efectuado|cancelado|pendiente|vencido|","L"=>0),
			"oficial_a_cargo" => array("N"=>"oficial_a_cargo","T"=>"INT","V"=>"","L"=>4),
			"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"matriz","L"=>20),
			"eacp" => array("N"=>"eacp","T"=>"VARCHAR","V"=>"EN_TRAMITE","L"=>20),
			"grupo_relacionado" => array("N"=>"grupo_relacionado","T"=>"BIGINT","V"=>"99","L"=>20),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "seguimiento_llamadas";}
	function getKey(){ return "idseguimiento_llamadas";}

	function idseguimiento_llamadas($v = false){ if($v !== false){$this->mCampos["idseguimiento_llamadas"]["V"] =  $v; } return new MQLCampo($this->mCampos["idseguimiento_llamadas"]);}
	function numero_socio($v = false){ if($v !== false){$this->mCampos["numero_socio"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_socio"]);}
	function numero_solicitud($v = false){ if($v !== false){$this->mCampos["numero_solicitud"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_solicitud"]);}
	function deuda_total($v = false){ if($v !== false){$this->mCampos["deuda_total"]["V"] =  $v; } return new MQLCampo($this->mCampos["deuda_total"]);}
	function telefono_uno($v = false){ if($v !== false){$this->mCampos["telefono_uno"]["V"] =  $v; } return new MQLCampo($this->mCampos["telefono_uno"]);}
	function telefono_dos($v = false){ if($v !== false){$this->mCampos["telefono_dos"]["V"] =  $v; } return new MQLCampo($this->mCampos["telefono_dos"]);}
	function fecha_llamada($v = false){ if($v !== false){$this->mCampos["fecha_llamada"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_llamada"]);}
	function hora_llamada($v = false){ if($v !== false){$this->mCampos["hora_llamada"]["V"] =  $v; } return new MQLCampo($this->mCampos["hora_llamada"]);}
	function observaciones($v = false){ if($v !== false){$this->mCampos["observaciones"]["V"] =  $v; } return new MQLCampo($this->mCampos["observaciones"]);}
	function estatus_llamada($v = false){ if($v !== false){$this->mCampos["estatus_llamada"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus_llamada"]);}
	function oficial_a_cargo($v = false){ if($v !== false){$this->mCampos["oficial_a_cargo"]["V"] =  $v; } return new MQLCampo($this->mCampos["oficial_a_cargo"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function eacp($v = false){ if($v !== false){$this->mCampos["eacp"]["V"] =  $v; } return new MQLCampo($this->mCampos["eacp"]);}
	function grupo_relacionado($v = false){ if($v !== false){$this->mCampos["grupo_relacionado"]["V"] =  $v; } return new MQLCampo($this->mCampos["grupo_relacionado"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	creditos_productos_otros_parametros	-	Generado:	[21/11/2014 18:24]	*/
class cCreditos_productos_otros_parametros {
	private $mCampos	= array(
			"idcreditos_productos_otros_parametros" => array("N"=>"idcreditos_productos_otros_parametros","T"=>"INT","V"=>"","L"=>11),
			"clave_del_producto" => array("N"=>"clave_del_producto","T"=>"INT","V"=>"","L"=>4),
			"clave_del_parametro" => array("N"=>"clave_del_parametro","T"=>"VARCHAR","V"=>"","L"=>45),
			"valor_del_parametro" => array("N"=>"valor_del_parametro","T"=>"VARCHAR","V"=>"","L"=>45),
			"fecha_de_alta" => array("N"=>"fecha_de_alta","T"=>"DATE","V"=>"2014-01-01","L"=>0),
			"fecha_de_expiracion" => array("N"=>"fecha_de_expiracion","T"=>"DATE","V"=>"2029-01-01","L"=>0)

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_productos_otros_parametros";}
	function getKey(){ return "idcreditos_productos_otros_parametros";}
	function idcreditos_productos_otros_parametros($v = false){ if($v !== false){$this->mCampos["idcreditos_productos_otros_parametros"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_productos_otros_parametros"]);}
	function clave_del_producto($v = false){ if($v !== false){$this->mCampos["clave_del_producto"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_del_producto"]);}
	function clave_del_parametro($v = false){ if($v !== false){$this->mCampos["clave_del_parametro"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_del_parametro"]);}
	function valor_del_parametro($v = false){ if($v !== false){$this->mCampos["valor_del_parametro"]["V"] =  $v; } return new MQLCampo($this->mCampos["valor_del_parametro"]);}
	function fecha_de_alta($v = false){ if($v !== false){$this->mCampos["fecha_de_alta"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_alta"]);}
	function fecha_de_expiracion($v = false){ if($v !== false){$this->mCampos["fecha_de_expiracion"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_expiracion"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}


/*	ORM: Tabla:	usuarios_web_notas	-	Generado:	[05/12/2014 12:04]	*/
class cUsuarios_web_notas {
	private $mCampos	= array(
			"idusuarios_web_notas" => array("N"=>"idusuarios_web_notas","T"=>"INT","V"=>"","L"=>11),
			"tipo" => array("N"=>"tipo","T"=>"VARCHAR","V"=>"default","L"=>16),
			"oficial" => array("N"=>"oficial","T"=>"INT","V"=>"99","L"=>11),
			"oficial_de_origen" => array("N"=>"oficial_de_origen","T"=>"INT","V"=>"99","L"=>11),
			"socio" => array("N"=>"socio","T"=>"BIGINT","V"=>"1","L"=>25),
			"documento" => array("N"=>"documento","T"=>"BIGINT","V"=>"1","L"=>25),
			"fecha" => array("N"=>"fecha","T"=>"DATE","V"=>"2009-01-01","L"=>0),
			"texto" => array("N"=>"texto","T"=>"LONGTEXT","V"=>"","L"=>0),
			"estado" => array("N"=>"estado","T"=>"INT","V"=>"10","L"=>4),
			"relevancia" => array("N"=>"relevancia","T"=>"INT","V"=>"1","L"=>4),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "usuarios_web_notas";}
	function getKey(){ return "idusuarios_web_notas";}
	function idusuarios_web_notas($v = false){ if($v !== false){$this->mCampos["idusuarios_web_notas"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuarios_web_notas"]);}
	function tipo($v = false){ if($v !== false){$this->mCampos["tipo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo"]);}
	function oficial($v = false){ if($v !== false){$this->mCampos["oficial"]["V"] =  $v; } return new MQLCampo($this->mCampos["oficial"]);}
	function oficial_de_origen($v = false){ if($v !== false){$this->mCampos["oficial_de_origen"]["V"] =  $v; } return new MQLCampo($this->mCampos["oficial_de_origen"]);}
	function socio($v = false){ if($v !== false){$this->mCampos["socio"]["V"] =  $v; } return new MQLCampo($this->mCampos["socio"]);}
	function documento($v = false){ if($v !== false){$this->mCampos["documento"]["V"] =  $v; } return new MQLCampo($this->mCampos["documento"]);}
	function fecha($v = false){ if($v !== false){$this->mCampos["fecha"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha"]);}
	function texto($v = false){ if($v !== false){$this->mCampos["texto"]["V"] =  $v; } return new MQLCampo($this->mCampos["texto"]);}
	function estado($v = false){ if($v !== false){$this->mCampos["estado"]["V"] =  $v; } return new MQLCampo($this->mCampos["estado"]);}
	function relevancia($v = false){ if($v !== false){$this->mCampos["relevancia"]["V"] =  $v; } return new MQLCampo($this->mCampos["relevancia"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}


/*	ORM: Tabla:	t_03f996214fba4a1d05a68b18fece8e71	-	Generado:	[09/1/2015 10:31]	*/
class cT_03f996214fba4a1d05a68b18fece8e71 {
	private $mCampos	= array(
			"idusuarios" => array("N"=>"idusuarios","T"=>"INT","V"=>"","L"=>4),
			"f_28fb96d57b21090705cfdf8bc3445d2a" => array("N"=>"f_28fb96d57b21090705cfdf8bc3445d2a","T"=>"VARCHAR","V"=>"","L"=>15),
			"f_34023acbff254d34664f94c3e08d836e" => array("N"=>"f_34023acbff254d34664f94c3e08d836e","T"=>"VARCHAR","V"=>"","L"=>45),
			"nombres" => array("N"=>"nombres","T"=>"VARCHAR","V"=>"","L"=>45),
			"apellidopaterno" => array("N"=>"apellidopaterno","T"=>"VARCHAR","V"=>"","L"=>45),
			"apellidomaterno" => array("N"=>"apellidomaterno","T"=>"VARCHAR","V"=>"","L"=>45),
			"puesto" => array("N"=>"puesto","T"=>"VARCHAR","V"=>"NOTVALID","L"=>45),
			"f_f2cd801e90b78ef4dc673a4659c1482d" => array("N"=>"f_f2cd801e90b78ef4dc673a4659c1482d","T"=>"INT","V"=>"1","L"=>10),
			"periodo_responsable" => array("N"=>"periodo_responsable","T"=>"INT","V"=>"0","L"=>4),
			"estatus" => array("N"=>"estatus","T"=>"ENUM","V"=>"|baja|activo|suspension|","L"=>0),
			"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"matriz","L"=>10),
			"usr_options" => array("N"=>"usr_options","T"=>"TEXT","V"=>"","L"=>0),
			"date_expire" => array("N"=>"date_expire","T"=>"DATE","V"=>"","L"=>0),
			"cuenta_contable_de_caja" => array("N"=>"cuenta_contable_de_caja","T"=>"VARCHAR","V"=>"CUENTA_DE_CUADRE","L"=>100),
			"codigo_de_persona" => array("N"=>"codigo_de_persona","T"=>"BIGINT","V"=>"","L"=>20)
	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "t_03f996214fba4a1d05a68b18fece8e71";}
	function getKey(){ return "idusuarios";}
	function idusuarios($v = false){ if($v !== false){$this->mCampos["idusuarios"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuarios"]);}
	function f_28fb96d57b21090705cfdf8bc3445d2a($v = false){ if($v !== false){$this->mCampos["f_28fb96d57b21090705cfdf8bc3445d2a"]["V"] =  $v; } return new MQLCampo($this->mCampos["f_28fb96d57b21090705cfdf8bc3445d2a"]);}
	function f_34023acbff254d34664f94c3e08d836e($v = false){ if($v !== false){$this->mCampos["f_34023acbff254d34664f94c3e08d836e"]["V"] =  $v; } return new MQLCampo($this->mCampos["f_34023acbff254d34664f94c3e08d836e"]);}
	function nombres($v = false){ if($v !== false){$this->mCampos["nombres"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombres"]);}
	function apellidopaterno($v = false){ if($v !== false){$this->mCampos["apellidopaterno"]["V"] =  $v; } return new MQLCampo($this->mCampos["apellidopaterno"]);}
	function apellidomaterno($v = false){ if($v !== false){$this->mCampos["apellidomaterno"]["V"] =  $v; } return new MQLCampo($this->mCampos["apellidomaterno"]);}
	function puesto($v = false){ if($v !== false){$this->mCampos["puesto"]["V"] =  $v; } return new MQLCampo($this->mCampos["puesto"]);}
	function f_f2cd801e90b78ef4dc673a4659c1482d($v = false){ if($v !== false){$this->mCampos["f_f2cd801e90b78ef4dc673a4659c1482d"]["V"] =  $v; } return new MQLCampo($this->mCampos["f_f2cd801e90b78ef4dc673a4659c1482d"]);}
	function periodo_responsable($v = false){ if($v !== false){$this->mCampos["periodo_responsable"]["V"] =  $v; } return new MQLCampo($this->mCampos["periodo_responsable"]);}
	function estatus($v = false){ if($v !== false){$this->mCampos["estatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function usr_options($v = false){ if($v !== false){$this->mCampos["usr_options"]["V"] =  $v; } return new MQLCampo($this->mCampos["usr_options"]);}
	function date_expire($v = false){ if($v !== false){$this->mCampos["date_expire"]["V"] =  $v; } return new MQLCampo($this->mCampos["date_expire"]);}
	function cuenta_contable_de_caja($v = false){ if($v !== false){$this->mCampos["cuenta_contable_de_caja"]["V"] =  $v; } return new MQLCampo($this->mCampos["cuenta_contable_de_caja"]);}
	function codigo_de_persona($v = false){ if($v !== false){$this->mCampos["codigo_de_persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["codigo_de_persona"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}


?>