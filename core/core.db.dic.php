<?php
include_once("core.db.inc.php");
include_once("core.config.inc.php");
//============================================================== TABLAS ============================================================

//============================================================== GENERAL ============================================================

/*	ORM: Tabla:	sistemas_modificados	-	Generado:	[11/5/2018 12:31]	*/
class cSistemas_modificados {
	private $mCampos	= array("idsistemas_modificados" => array("N"=>"idsistemas_modificados","T"=>"INT","V"=>"","L"=>11),"tiempo" => array("N"=>"tiempo","T"=>"INT","V"=>"","L"=>11),"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"0","L"=>5),"idtipoobjeto" => array("N"=>"idtipoobjeto","T"=>"VARCHAR","V"=>"T","L"=>4),"idobjeto" => array("N"=>"idobjeto","T"=>"VARCHAR","V"=>"","L"=>40),"identificador" => array("N"=>"identificador","T"=>"VARCHAR","V"=>"0","L"=>25),"idsubobjeto" => array("N"=>"idsubobjeto","T"=>"VARCHAR","V"=>"","L"=>40),"v_antes" => array("N"=>"v_antes","T"=>"VARCHAR","V"=>"","L"=>100),"v_despues" => array("N"=>"v_despues","T"=>"VARCHAR","V"=>"","L"=>100));
	public $IDSISTEMAS_MODIFICADOS = "idsistemas_modificados"; public $TIEMPO = "tiempo"; public $IDUSUARIO = "idusuario"; public $IDTIPOOBJETO = "idtipoobjeto"; public $IDOBJETO = "idobjeto"; public $IDENTIFICADOR = "identificador"; public $IDSUBOBJETO = "idsubobjeto"; public $V_ANTES = "v_antes"; public $V_DESPUES = "v_despues";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "sistemas_modificados";}
	function getKey(){ return "idsistemas_modificados";}
	function idsistemas_modificados($v = false){ if($v !== false){$this->mCampos["idsistemas_modificados"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsistemas_modificados"]);}
	function tiempo($v = false){ if($v !== false){$this->mCampos["tiempo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tiempo"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function idtipoobjeto($v = false){ if($v !== false){$this->mCampos["idtipoobjeto"]["V"] =  $v; } return new MQLCampo($this->mCampos["idtipoobjeto"]);}
	function idobjeto($v = false){ if($v !== false){$this->mCampos["idobjeto"]["V"] =  $v; } return new MQLCampo($this->mCampos["idobjeto"]);}
	function identificador($v = false){ if($v !== false){$this->mCampos["identificador"]["V"] =  $v; } return new MQLCampo($this->mCampos["identificador"]);}
	function idsubobjeto($v = false){ if($v !== false){$this->mCampos["idsubobjeto"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsubobjeto"]);}
	function v_antes($v = false){ if($v !== false){$this->mCampos["v_antes"]["V"] =  $v; } return new MQLCampo($this->mCampos["v_antes"]);}
	function v_despues($v = false){ if($v !== false){$this->mCampos["v_despues"]["V"] =  $v; } return new MQLCampo($this->mCampos["v_despues"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}

/*	ORM: Tabla:	general_dias_festivos	-	Generado:	[28/4/2016 15:24]	*/
class cGeneral_dias_festivos {
	private $mCampos	= array("fecha_marcado" => array("N"=>"fecha_marcado","T"=>"DATE","V"=>"","L"=>0),	"descripcion_festividad" => array("N"=>"descripcion_festividad","T"=>"VARCHAR","V"=>"","L"=>100));
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "general_dias_festivos";}
	function getKey(){ return "fecha_marcado";}
	function fecha_marcado($v = false){ if($v !== false){$this->mCampos["fecha_marcado"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_marcado"]);}
	function descripcion_festividad($v = false){ if($v !== false){$this->mCampos["descripcion_festividad"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_festividad"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	general_sucursales	-	Generado:	[08/6/2015 13:57]	*/
/*	ORM: Tabla:	general_sucursales	-	Generado:	[06/10/2016 17:12]	*/
class cGeneral_sucursales {
	private $mCampos	= array("codigo_sucursal" => array("N"=>"codigo_sucursal","T"=>"VARCHAR","V"=>"","L"=>10),"nombre_sucursal" => array("N"=>"nombre_sucursal","T"=>"VARCHAR","V"=>"","L"=>55),"gerente_sucursal" => array("N"=>"gerente_sucursal","T"=>"BIGINT","V"=>"1","L"=>20),"caja_local_residente" => array("N"=>"caja_local_residente","T"=>"INT","V"=>"1","L"=>4),"titular_de_cobranza" => array("N"=>"titular_de_cobranza","T"=>"BIGINT","V"=>"1","L"=>20),"titular_de_seguimiento" => array("N"=>"titular_de_seguimiento","T"=>"BIGINT","V"=>"1","L"=>20),"titular_de_contabilidad" => array("N"=>"titular_de_contabilidad","T"=>"BIGINT","V"=>"1","L"=>20),"titular_de_inventarios" => array("N"=>"titular_de_inventarios","T"=>"BIGINT","V"=>"1","L"=>20),"titular_de_control_interno" => array("N"=>"titular_de_control_interno","T"=>"BIGINT","V"=>"1","L"=>20),"titular_de_nominas" => array("N"=>"titular_de_nominas","T"=>"BIGINT","V"=>"1","L"=>20),"titular_de_cumplimiento" => array("N"=>"titular_de_cumplimiento","T"=>"BIGINT","V"=>"1","L"=>20),"hora_de_inicio_de_operaciones" => array("N"=>"hora_de_inicio_de_operaciones","T"=>"INT","V"=>"8","L"=>4),"hora_de_fin_de_operaciones" => array("N"=>"hora_de_fin_de_operaciones","T"=>"INT","V"=>"16","L"=>4),"calle" => array("N"=>"calle","T"=>"VARCHAR","V"=>"","L"=>100),"numero_exterior" => array("N"=>"numero_exterior","T"=>"VARCHAR","V"=>"","L"=>25),"numero_interior" => array("N"=>"numero_interior","T"=>"VARCHAR","V"=>"","L"=>25),"colonia" => array("N"=>"colonia","T"=>"VARCHAR","V"=>"","L"=>45),"codigo_postal" => array("N"=>"codigo_postal","T"=>"INT","V"=>"1","L"=>11),"localidad" => array("N"=>"localidad","T"=>"VARCHAR","V"=>"","L"=>50),"municipio" => array("N"=>"municipio","T"=>"VARCHAR","V"=>"","L"=>50),"estado" => array("N"=>"estado","T"=>"VARCHAR","V"=>"","L"=>50),"telefono" => array("N"=>"telefono","T"=>"VARCHAR","V"=>"","L"=>20),"fax" => array("N"=>"fax","T"=>"VARCHAR","V"=>"","L"=>20),"clave_de_persona" => array("N"=>"clave_de_persona","T"=>"BIGINT","V"=>"","L"=>20),"clave_numerica" => array("N"=>"clave_numerica","T"=>"INT","V"=>"0","L"=>11),"centro_de_costo" => array("N"=>"centro_de_costo","T"=>"INT","V"=>"0","L"=>11),);
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
	function centro_de_costo($v = false){ if($v !== false){$this->mCampos["centro_de_costo"]["V"] =  $v; } return new MQLCampo($this->mCampos["centro_de_costo"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	general_municipios	-	Generado:	[28/4/2016 15:18]	*/
/*	ORM: Tabla:	general_municipios	-	Generado:	[09/2/2017 09:54]	*/
/*	ORM: Tabla:	general_municipios	-	Generado:	[15/2/2018 12:10]	*/
class cGeneral_municipios {
	private $mCampos	= array("idgeneral_municipios" => array("N"=>"idgeneral_municipios","T"=>"INT","V"=>"","L"=>11),"clave_de_entidad" => array("N"=>"clave_de_entidad","T"=>"INT","V"=>"4","L"=>4),"clave_de_municipio" => array("N"=>"clave_de_municipio","T"=>"INT","V"=>"1","L"=>6),"nombre_del_municipio" => array("N"=>"nombre_del_municipio","T"=>"VARCHAR","V"=>"","L"=>100),"habitantes" => array("N"=>"habitantes","T"=>"FLOAT","V"=>"","L"=>29),"indice_de_marginacion" => array("N"=>"indice_de_marginacion","T"=>"FLOAT","V"=>"0.000000","L"=>21),"grado_de_marginacion" => array("N"=>"grado_de_marginacion","T"=>"VARCHAR","V"=>"Alto","L"=>20),"lugar_nacional" => array("N"=>"lugar_nacional","T"=>"INT","V"=>"0","L"=>8));
	public $IDGENERAL_MUNICIPIOS = "idgeneral_municipios"; public $CLAVE_DE_ENTIDAD = "clave_de_entidad"; public $CLAVE_DE_MUNICIPIO = "clave_de_municipio"; public $NOMBRE_DEL_MUNICIPIO = "nombre_del_municipio"; public $HABITANTES = "habitantes"; public $INDICE_DE_MARGINACION = "indice_de_marginacion"; public $GRADO_DE_MARGINACION = "grado_de_marginacion"; public $LUGAR_NACIONAL = "lugar_nacional";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "general_municipios";}
	function getKey(){ return "idgeneral_municipios";}
	function idgeneral_municipios($v = false){ if($v !== false){$this->mCampos["idgeneral_municipios"]["V"] =  $v; } return new MQLCampo($this->mCampos["idgeneral_municipios"]);}
	function clave_de_entidad($v = false){ if($v !== false){$this->mCampos["clave_de_entidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_entidad"]);}
	function clave_de_municipio($v = false){ if($v !== false){$this->mCampos["clave_de_municipio"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_municipio"]);}
	function nombre_del_municipio($v = false){ if($v !== false){$this->mCampos["nombre_del_municipio"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_del_municipio"]);}
	function habitantes($v = false){ if($v !== false){$this->mCampos["habitantes"]["V"] =  $v; } return new MQLCampo($this->mCampos["habitantes"]);}
	function indice_de_marginacion($v = false){ if($v !== false){$this->mCampos["indice_de_marginacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["indice_de_marginacion"]);}
	function grado_de_marginacion($v = false){ if($v !== false){$this->mCampos["grado_de_marginacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["grado_de_marginacion"]);}
	function lugar_nacional($v = false){ if($v !== false){$this->mCampos["lugar_nacional"]["V"] =  $v; } return new MQLCampo($this->mCampos["lugar_nacional"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	general_log	-	Generado:	[28/4/2016 15:20]	*/
/*	ORM: Tabla:	general_log	-	Generado:	[20/12/2016 16:44]	*/
class cGeneral_log {
	private $mCampos	= array("idgeneral_log" => array("N"=>"idgeneral_log","T"=>"INT","V"=>"","L"=>10),"fecha_log" => array("N"=>"fecha_log","T"=>"DATE","V"=>"","L"=>0),"hour_log" => array("N"=>"hour_log","T"=>"VARCHAR","V"=>"","L"=>20),"type_error" => array("N"=>"type_error","T"=>"INT","V"=>"","L"=>10),"usr_log" => array("N"=>"usr_log","T"=>"VARCHAR","V"=>"","L"=>60),"text_log" => array("N"=>"text_log","T"=>"LONGTEXT","V"=>"","L"=>0),"ip_private" => array("N"=>"ip_private","T"=>"VARCHAR","V"=>"","L"=>20),"ip_proxy" => array("N"=>"ip_proxy","T"=>"VARCHAR","V"=>"","L"=>20),"ip_public" => array("N"=>"ip_public","T"=>"VARCHAR","V"=>"","L"=>20),"idpersona" => array("N"=>"idpersona","T"=>"BIGINT","V"=>"0","L"=>20),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "general_log";}
	function getKey(){ return "idgeneral_log";}
	function idgeneral_log($v = false){ if($v !== false){$this->mCampos["idgeneral_log"]["V"] =  $v; } return new MQLCampo($this->mCampos["idgeneral_log"]);}
	function fecha_log($v = false){ if($v !== false){$this->mCampos["fecha_log"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_log"]);}
	function hour_log($v = false){ if($v !== false){$this->mCampos["hour_log"]["V"] =  $v; } return new MQLCampo($this->mCampos["hour_log"]);}
	function type_error($v = false){ if($v !== false){$this->mCampos["type_error"]["V"] =  $v; } return new MQLCampo($this->mCampos["type_error"]);}
	function usr_log($v = false){ if($v !== false){$this->mCampos["usr_log"]["V"] =  $v; } return new MQLCampo($this->mCampos["usr_log"]);}
	function text_log($v = false){ if($v !== false){$this->mCampos["text_log"]["V"] =  $v; } return new MQLCampo($this->mCampos["text_log"]);}
	function ip_private($v = false){ if($v !== false){$this->mCampos["ip_private"]["V"] =  $v; } return new MQLCampo($this->mCampos["ip_private"]);}
	function ip_proxy($v = false){ if($v !== false){$this->mCampos["ip_proxy"]["V"] =  $v; } return new MQLCampo($this->mCampos["ip_proxy"]);}
	function ip_public($v = false){ if($v !== false){$this->mCampos["ip_public"]["V"] =  $v; } return new MQLCampo($this->mCampos["ip_public"]);}
	function idpersona($v = false){ if($v !== false){$this->mCampos["idpersona"]["V"] =  $v; } return new MQLCampo($this->mCampos["idpersona"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}


/*	ORM: Tabla:	general_reports	-	Generado:	[28/4/2016 15:23]	*/
/*	ORM: Tabla:	general_reports	-	Generado:	[31/5/2017 10:19]	*/
/*	ORM: Tabla:	general_reports	-	Generado:	[14/2/2018 17:23]	*/
class cGeneral_reports {
	private $mCampos	= array("idgeneral_reports" => array("N"=>"idgeneral_reports","T"=>"VARCHAR","V"=>"0","L"=>100),"descripcion_reports" => array("N"=>"descripcion_reports","T"=>"VARCHAR","V"=>"","L"=>200),"aplica" => array("N"=>"aplica","T"=>"VARCHAR","V"=>"","L"=>35),"idreport" => array("N"=>"idreport","T"=>"INT","V"=>"","L"=>10),"explicacion" => array("N"=>"explicacion","T"=>"MEDIUMTEXT","V"=>"","L"=>0),"order_index" => array("N"=>"order_index","T"=>"INT","V"=>"0","L"=>3),"estatus" => array("N"=>"estatus","T"=>"INT","V"=>"1","L"=>2),"tags" => array("N"=>"tags","T"=>"VARCHAR","V"=>"","L"=>100));
	public $IDGENERAL_REPORTS = "idgeneral_reports"; public $DESCRIPCION_REPORTS = "descripcion_reports"; public $APLICA = "aplica"; public $IDREPORT = "idreport"; public $EXPLICACION = "explicacion"; public $ORDER_INDEX = "order_index"; public $ESTATUS = "estatus"; public $TAGS = "tags";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "general_reports";}
	function getKey(){ return "idreport";}
	function idgeneral_reports($v = false){ if($v !== false){$this->mCampos["idgeneral_reports"]["V"] =  $v; } return new MQLCampo($this->mCampos["idgeneral_reports"]);}
	function descripcion_reports($v = false){ if($v !== false){$this->mCampos["descripcion_reports"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_reports"]);}
	function aplica($v = false){ if($v !== false){$this->mCampos["aplica"]["V"] =  $v; } return new MQLCampo($this->mCampos["aplica"]);}
	function idreport($v = false){ if($v !== false){$this->mCampos["idreport"]["V"] =  $v; } return new MQLCampo($this->mCampos["idreport"]);}
	function explicacion($v = false){ if($v !== false){$this->mCampos["explicacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["explicacion"]);}
	function order_index($v = false){ if($v !== false){$this->mCampos["order_index"]["V"] =  $v; } return new MQLCampo($this->mCampos["order_index"]);}
	function estatus($v = false){ if($v !== false){$this->mCampos["estatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus"]);}
	function tags($v = false){ if($v !== false){$this->mCampos["tags"]["V"] =  $v; } return new MQLCampo($this->mCampos["tags"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	general_niveles	-	Generado:	[14/7/2016 15:47]	*/
/*	ORM: Tabla:	general_niveles	-	Generado:	[25/3/2017 14:10]	*/
class cGeneral_niveles {
	private $mCampos	= array("idgeneral_niveles" => array("N"=>"idgeneral_niveles","T"=>"INT","V"=>"","L"=>4),"descripcion_del_nivel" => array("N"=>"descripcion_del_nivel","T"=>"VARCHAR","V"=>"","L"=>45),"task_events" => array("N"=>"task_events","T"=>"TEXT","V"=>"","L"=>0),"work_time_range" => array("N"=>"work_time_range","T"=>"VARCHAR","V"=>"","L"=>10),"rules_by_user" => array("N"=>"rules_by_user","T"=>"TEXT","V"=>"","L"=>0),"initpage" => array("N"=>"initpage","T"=>"VARCHAR","V"=>"index.xul.php","L"=>100),"taskspage" => array("N"=>"taskspage","T"=>"VARCHAR","V"=>"utils/frm_calendar_tasks.php","L"=>100),"tipo_sistema" => array("N"=>"tipo_sistema","T"=>"INT","V"=>"0","L"=>3),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "general_niveles";}
	function getKey(){ return "idgeneral_niveles";}
	function idgeneral_niveles($v = false){ if($v !== false){$this->mCampos["idgeneral_niveles"]["V"] =  $v; } return new MQLCampo($this->mCampos["idgeneral_niveles"]);}
	function descripcion_del_nivel($v = false){ if($v !== false){$this->mCampos["descripcion_del_nivel"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_del_nivel"]);}
	function task_events($v = false){ if($v !== false){$this->mCampos["task_events"]["V"] =  $v; } return new MQLCampo($this->mCampos["task_events"]);}
	function work_time_range($v = false){ if($v !== false){$this->mCampos["work_time_range"]["V"] =  $v; } return new MQLCampo($this->mCampos["work_time_range"]);}
	function rules_by_user($v = false){ if($v !== false){$this->mCampos["rules_by_user"]["V"] =  $v; } return new MQLCampo($this->mCampos["rules_by_user"]);}
	function initpage($v = false){ if($v !== false){$this->mCampos["initpage"]["V"] =  $v; } return new MQLCampo($this->mCampos["initpage"]);}
	function taskspage($v = false){ if($v !== false){$this->mCampos["taskspage"]["V"] =  $v; } return new MQLCampo($this->mCampos["taskspage"]);}
	function tipo_sistema($v = false){ if($v !== false){$this->mCampos["tipo_sistema"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_sistema"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}
/*	ORM: Tabla:	general_departamentos	-	Generado:	[14/7/2016 15:48]	*/
class cGeneral_departamentos {
	private $mCampos	= array("idgeneral_departamentos" => array("N"=>"idgeneral_departamentos","T"=>"INT","V"=>"","L"=>10),"descripcion_departamento" => array("N"=>"descripcion_departamento","T"=>"VARCHAR","V"=>"","L"=>45),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "general_departamentos";}
	function getKey(){ return "idgeneral_departamentos";}
	function idgeneral_departamentos($v = false){ if($v !== false){$this->mCampos["idgeneral_departamentos"]["V"] =  $v; } return new MQLCampo($this->mCampos["idgeneral_departamentos"]);}
	function descripcion_departamento($v = false){ if($v !== false){$this->mCampos["descripcion_departamento"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_departamento"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
}

/*	ORM: Tabla:	mercadeo_campana	-	Generado:	[09/2/2017 09:56]	*/
class cMercadeo_campana {
	private $mCampos	= array("idmercadeo_campana" => array("N"=>"idmercadeo_campana","T"=>"INT","V"=>"","L"=>11),"nombre" => array("N"=>"nombre","T"=>"VARCHAR","V"=>"","L"=>45),"fecha_inicial" => array("N"=>"fecha_inicial","T"=>"DATE","V"=>"","L"=>0),"fecha_final" => array("N"=>"fecha_final","T"=>"DATE","V"=>"","L"=>0),"oficial" => array("N"=>"oficial","T"=>"INT","V"=>"","L"=>8),"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"","L"=>8),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "mercadeo_campana";}
	function getKey(){ return "idmercadeo_campana";}
	function idmercadeo_campana($v = false){ if($v !== false){$this->mCampos["idmercadeo_campana"]["V"] =  $v; } return new MQLCampo($this->mCampos["idmercadeo_campana"]);}
	function nombre($v = false){ if($v !== false){$this->mCampos["nombre"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre"]);}
	function fecha_inicial($v = false){ if($v !== false){$this->mCampos["fecha_inicial"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_inicial"]);}
	function fecha_final($v = false){ if($v !== false){$this->mCampos["fecha_final"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_final"]);}
	function oficial($v = false){ if($v !== false){$this->mCampos["oficial"]["V"] =  $v; } return new MQLCampo($this->mCampos["oficial"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
}
/*	ORM: Tabla:	mercadeo_envios	-	Generado:	[09/2/2017 09:56]	*/
class cMercadeo_envios {
	private $mCampos	= array("idmercadeo_envios" => array("N"=>"idmercadeo_envios","T"=>"INT","V"=>"","L"=>11),"estatus" => array("N"=>"estatus","T"=>"INT","V"=>"1","L"=>2),"persona" => array("N"=>"persona","T"=>"BIGINT","V"=>"","L"=>20),"tiempo" => array("N"=>"tiempo","T"=>"INT","V"=>"","L"=>11),"campana" => array("N"=>"campana","T"=>"INT","V"=>"","L"=>8),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "mercadeo_envios";}
	function getKey(){ return "idmercadeo_envios";}
	function idmercadeo_envios($v = false){ if($v !== false){$this->mCampos["idmercadeo_envios"]["V"] =  $v; } return new MQLCampo($this->mCampos["idmercadeo_envios"]);}
	function estatus($v = false){ if($v !== false){$this->mCampos["estatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus"]);}
	function persona($v = false){ if($v !== false){$this->mCampos["persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["persona"]);}
	function tiempo($v = false){ if($v !== false){$this->mCampos["tiempo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tiempo"]);}
	function campana($v = false){ if($v !== false){$this->mCampos["campana"]["V"] =  $v; } return new MQLCampo($this->mCampos["campana"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
}
//============================================================== CREDITO ============================================================

/*	ORM: Tabla:	creditos_solicitud	-	Generado:	[30/11/2015 19:13]	*/
/*	ORM: Tabla:	creditos_solicitud	-	Generado:	[09/2/2017 09:58]	*/
/*	ORM: Tabla:	creditos_solicitud	-	Generado:	[23/11/2017 12:16]	*/
class cCreditos_solicitud {
	private $mCampos	= array("numero_solicitud" => array("N"=>"numero_solicitud","T"=>"BIGINT","V"=>"0","L"=>20),"fecha_solicitud" => array("N"=>"fecha_solicitud","T"=>"DATE","V"=>"0000-00-00","L"=>0),"fecha_autorizacion" => array("N"=>"fecha_autorizacion","T"=>"DATE","V"=>"0000-00-00","L"=>0),"monto_solicitado" => array("N"=>"monto_solicitado","T"=>"DOUBLE","V"=>"0.00","L"=>33),"monto_autorizado" => array("N"=>"monto_autorizado","T"=>"DOUBLE","V"=>"0.00","L"=>33),"numero_socio" => array("N"=>"numero_socio","T"=>"BIGINT","V"=>"0","L"=>20),"docto_autorizacion" => array("N"=>"docto_autorizacion","T"=>"VARCHAR","V"=>"NO_AUTORIZADO","L"=>82),"plazo_en_dias" => array("N"=>"plazo_en_dias","T"=>"INT","V"=>"0","L"=>10),"numero_pagos" => array("N"=>"numero_pagos","T"=>"INT","V"=>"0","L"=>5),"tasa_interes" => array("N"=>"tasa_interes","T"=>"FLOAT","V"=>"0.00000","L"=>17),"periocidad_de_pago" => array("N"=>"periocidad_de_pago","T"=>"INT","V"=>"0","L"=>5),"tipo_credito" => array("N"=>"tipo_credito","T"=>"INT","V"=>"99","L"=>4),"estatus_actual" => array("N"=>"estatus_actual","T"=>"INT","V"=>"99","L"=>4),"tipo_autorizacion" => array("N"=>"tipo_autorizacion","T"=>"INT","V"=>"99","L"=>4),"oficial_credito" => array("N"=>"oficial_credito","T"=>"INT","V"=>"99","L"=>4),"fecha_vencimiento" => array("N"=>"fecha_vencimiento","T"=>"DATE","V"=>"0000-00-00","L"=>0),"pagos_autorizados" => array("N"=>"pagos_autorizados","T"=>"INT","V"=>"0","L"=>5),"dias_autorizados" => array("N"=>"dias_autorizados","T"=>"INT","V"=>"0","L"=>10),"periodo_solicitudes" => array("N"=>"periodo_solicitudes","T"=>"INT","V"=>"0","L"=>10),"destino_credito" => array("N"=>"destino_credito","T"=>"INT","V"=>"99","L"=>5),"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"99","L"=>4),"nivel_riesgo" => array("N"=>"nivel_riesgo","T"=>"INT","V"=>"99","L"=>4),"saldo_actual" => array("N"=>"saldo_actual","T"=>"DOUBLE","V"=>"0.00","L"=>33),"fecha_ultimo_mvto" => array("N"=>"fecha_ultimo_mvto","T"=>"DATE","V"=>"","L"=>0),"tipo_convenio" => array("N"=>"tipo_convenio","T"=>"INT","V"=>"99","L"=>6),"interes_diario" => array("N"=>"interes_diario","T"=>"FLOAT","V"=>"0.000000","L"=>25),"saldo_vencido" => array("N"=>"saldo_vencido","T"=>"DOUBLE","V"=>"0.00","L"=>33),"ultimo_periodo_afectado" => array("N"=>"ultimo_periodo_afectado","T"=>"INT","V"=>"0","L"=>4),"sdo_int_ant" => array("N"=>"sdo_int_ant","T"=>"FLOAT","V"=>"0.00","L"=>25),"periodo_notificacion" => array("N"=>"periodo_notificacion","T"=>"INT","V"=>"0","L"=>4),"tasa_moratorio" => array("N"=>"tasa_moratorio","T"=>"FLOAT","V"=>"0.00000","L"=>17),"observacion_solicitud" => array("N"=>"observacion_solicitud","T"=>"VARCHAR","V"=>"","L"=>98),"tasa_ahorro" => array("N"=>"tasa_ahorro","T"=>"FLOAT","V"=>"0.00000","L"=>17),"grupo_asociado" => array("N"=>"grupo_asociado","T"=>"BIGINT","V"=>"999","L"=>20),"descripcion_aplicacion" => array("N"=>"descripcion_aplicacion","T"=>"VARCHAR","V"=>"N/A","L"=>150),"fecha_ministracion" => array("N"=>"fecha_ministracion","T"=>"DATE","V"=>"2005-12-31","L"=>0),"contrato_corriente_relacionado" => array("N"=>"contrato_corriente_relacionado","T"=>"BIGINT","V"=>"2000001","L"=>20),"monto_parcialidad" => array("N"=>"monto_parcialidad","T"=>"FLOAT","V"=>"0.00","L"=>25),"oficial_seguimiento" => array("N"=>"oficial_seguimiento","T"=>"INT","V"=>"99","L"=>4),"fecha_castigo" => array("N"=>"fecha_castigo","T"=>"DATE","V"=>"2006-12-04","L"=>0),"saldo_conciliado" => array("N"=>"saldo_conciliado","T"=>"FLOAT","V"=>"0.00","L"=>25),"notas_auditoria" => array("N"=>"notas_auditoria","T"=>"VARCHAR","V"=>"","L"=>55),"fecha_conciliada" => array("N"=>"fecha_conciliada","T"=>"DATE","V"=>"2006-12-04","L"=>0),"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"MATRIZ","L"=>20),"eacp" => array("N"=>"eacp","T"=>"VARCHAR","V"=>"EN_TRAMITE","L"=>15),"interes_normal_devengado" => array("N"=>"interes_normal_devengado","T"=>"FLOAT","V"=>"0.00","L"=>25),"tipo_de_pago" => array("N"=>"tipo_de_pago","T"=>"INT","V"=>"2","L"=>4),"interes_normal_pagado" => array("N"=>"interes_normal_pagado","T"=>"FLOAT","V"=>"0.00","L"=>25),"interes_moratorio_devengado" => array("N"=>"interes_moratorio_devengado","T"=>"FLOAT","V"=>"0.00","L"=>25),"interes_moratorio_pagado" => array("N"=>"interes_moratorio_pagado","T"=>"FLOAT","V"=>"0.00","L"=>25),"fecha_mora" => array("N"=>"fecha_mora","T"=>"DATE","V"=>"2008-08-01","L"=>0),"fecha_vencimiento_dinamico" => array("N"=>"fecha_vencimiento_dinamico","T"=>"DATE","V"=>"2008-08-01","L"=>0),"tipo_de_calculo_de_interes" => array("N"=>"tipo_de_calculo_de_interes","T"=>"INT","V"=>"2","L"=>2),"causa_de_mora" => array("N"=>"causa_de_mora","T"=>"INT","V"=>"99","L"=>4),"persona_asociada" => array("N"=>"persona_asociada","T"=>"BIGINT","V"=>"0","L"=>20),"perfil_de_intereses" => array("N"=>"perfil_de_intereses","T"=>"INT","V"=>"1","L"=>4),"fuente_de_fondeo" => array("N"=>"fuente_de_fondeo","T"=>"INT","V"=>"1","L"=>4),"fecha_de_primer_pago" => array("N"=>"fecha_de_primer_pago","T"=>"DATE","V"=>"2014-01-01","L"=>0),"operacion_origen" => array("N"=>"operacion_origen","T"=>"BIGINT","V"=>"1","L"=>20),"tipo_de_origen" => array("N"=>"tipo_de_origen","T"=>"INT","V"=>"1","L"=>5),"tipo_de_dias_de_pago" => array("N"=>"tipo_de_dias_de_pago","T"=>"INT","V"=>"1","L"=>3),"tipo_de_lugar_de_pago" => array("N"=>"tipo_de_lugar_de_pago","T"=>"INT","V"=>"1","L"=>4),"tipo_de_dispersion" => array("N"=>"tipo_de_dispersion","T"=>"INT","V"=>"1","L"=>4),"fecha_de_proximo_pago" => array("N"=>"fecha_de_proximo_pago","T"=>"DATE","V"=>"0000-00-00","L"=>0),"omitir_seguimiento" => array("N"=>"omitir_seguimiento","T"=>"INT","V"=>"0","L"=>2),"tasa_cat" => array("N"=>"tasa_cat","T"=>"FLOAT","V"=>"0.00","L"=>13),"fecha_ultimo_capital" => array("N"=>"fecha_ultimo_capital","T"=>"DATE","V"=>"0000-00-00","L"=>0),"recibo_ultimo_capital" => array("N"=>"recibo_ultimo_capital","T"=>"BIGINT","V"=>"0","L"=>20),"estat_sol" => array("N"=>"estat_sol","T"=>"INT","V"=>"0","L"=>3),"criesgo" => array("N"=>"criesgo","T"=>"INT","V"=>"0","L"=>4),"bonificaciones" => array("N"=>"bonificaciones","T"=>"DOUBLE","V"=>"0.00","L"=>25),"gastoscbza" => array("N"=>"gastoscbza","T"=>"DOUBLE","V"=>"0.00","L"=>25),"iva_interes" => array("N"=>"iva_interes","T"=>"DOUBLE","V"=>"0.00","L"=>25),"iva_otros" => array("N"=>"iva_otros","T"=>"DOUBLE","V"=>"0.00","L"=>25));
	public $NUMERO_SOLICITUD	= "numero_solicitud";
	public $FECHA_SOLICITUD	= "fecha_solicitud";
	public $FECHA_AUTORIZACION	= "fecha_autorizacion";
	public $MONTO_SOLICITADO	= "monto_solicitado";
	public $MONTO_AUTORIZADO	= "monto_autorizado";
	public $NUMERO_SOCIO	= "numero_socio";
	public $DOCTO_AUTORIZACION	= "docto_autorizacion";
	public $PLAZO_EN_DIAS	= "plazo_en_dias";
	public $NUMERO_PAGOS	= "numero_pagos";
	public $TASA_INTERES	= "tasa_interes";
	public $PERIOCIDAD_DE_PAGO	= "periocidad_de_pago";
	public $TIPO_CREDITO	= "tipo_credito";
	public $ESTATUS_ACTUAL	= "estatus_actual";
	public $TIPO_AUTORIZACION	= "tipo_autorizacion";
	public $OFICIAL_CREDITO	= "oficial_credito";
	public $FECHA_VENCIMIENTO	= "fecha_vencimiento";
	public $PAGOS_AUTORIZADOS	= "pagos_autorizados";
	public $DIAS_AUTORIZADOS	= "dias_autorizados";
	public $PERIODO_SOLICITUDES	= "periodo_solicitudes";
	public $DESTINO_CREDITO	= "destino_credito";
	public $IDUSUARIO	= "idusuario";
	public $NIVEL_RIESGO	= "nivel_riesgo";
	public $SALDO_ACTUAL	= "saldo_actual";
	public $FECHA_ULTIMO_MVTO	= "fecha_ultimo_mvto";
	public $TIPO_CONVENIO	= "tipo_convenio";
	public $INTERES_DIARIO	= "interes_diario";
	public $SALDO_VENCIDO	= "saldo_vencido";
	public $ULTIMO_PERIODO_AFECTADO	= "ultimo_periodo_afectado";
	public $SDO_INT_ANT	= "sdo_int_ant";
	public $PERIODO_NOTIFICACION	= "periodo_notificacion";
	public $TASA_MORATORIO	= "tasa_moratorio";
	public $OBSERVACION_SOLICITUD	= "observacion_solicitud";
	public $TASA_AHORRO	= "tasa_ahorro";
	public $GRUPO_ASOCIADO	= "grupo_asociado";
	public $DESCRIPCION_APLICACION	= "descripcion_aplicacion";
	public $FECHA_MINISTRACION	= "fecha_ministracion";
	public $CONTRATO_CORRIENTE_RELACIONADO	= "contrato_corriente_relacionado";
	public $MONTO_PARCIALIDAD	= "monto_parcialidad";
	public $OFICIAL_SEGUIMIENTO	= "oficial_seguimiento";
	public $FECHA_CASTIGO	= "fecha_castigo";
	public $SALDO_CONCILIADO	= "saldo_conciliado";
	public $NOTAS_AUDITORIA	= "notas_auditoria";
	public $FECHA_CONCILIADA	= "fecha_conciliada";
	public $SUCURSAL	= "sucursal";
	public $EACP	= "eacp";
	public $INTERES_NORMAL_DEVENGADO	= "interes_normal_devengado";
	public $TIPO_DE_PAGO	= "tipo_de_pago";
	public $INTERES_NORMAL_PAGADO	= "interes_normal_pagado";
	public $INTERES_MORATORIO_DEVENGADO	= "interes_moratorio_devengado";
	public $INTERES_MORATORIO_PAGADO	= "interes_moratorio_pagado";
	public $FECHA_MORA	= "fecha_mora";
	public $FECHA_VENCIMIENTO_DINAMICO	= "fecha_vencimiento_dinamico";
	public $TIPO_DE_CALCULO_DE_INTERES	= "tipo_de_calculo_de_interes";
	public $CAUSA_DE_MORA	= "causa_de_mora";
	public $PERSONA_ASOCIADA	= "persona_asociada";
	public $PERFIL_DE_INTERESES	= "perfil_de_intereses";
	public $FUENTE_DE_FONDEO	= "fuente_de_fondeo";
	public $FECHA_DE_PRIMER_PAGO	= "fecha_de_primer_pago";
	public $OPERACION_ORIGEN	= "operacion_origen";
	public $TIPO_DE_ORIGEN	= "tipo_de_origen";
	public $TIPO_DE_DIAS_DE_PAGO	= "tipo_de_dias_de_pago";
	public $TIPO_DE_LUGAR_DE_PAGO	= "tipo_de_lugar_de_pago";
	public $TIPO_DE_DISPERSION	= "tipo_de_dispersion";
	public $FECHA_DE_PROXIMO_PAGO	= "fecha_de_proximo_pago";
	public $OMITIR_SEGUIMIENTO	= "omitir_seguimiento";
	public $TASA_CAT	= "tasa_cat";
	public $FECHA_ULTIMO_CAPITAL	= "fecha_ultimo_capital";
	public $RECIBO_ULTIMO_CAPITAL	= "recibo_ultimo_capital";
	public $ESTAT_SOL	= "estat_sol";
	public $CRIESGO	= "criesgo";
	public $BONIFICACIONES	= "bonificaciones";
	public $GASTOSCBZA	= "gastoscbza";
	public $IVA_INTERES	= "iva_interes";
	public $IVA_OTROS	= "iva_otros";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_solicitud";}
	function getKey(){ return "numero_solicitud";}
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
	function tasa_ahorro($v = false){ if($v !== false){$this->mCampos["tasa_ahorro"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_ahorro"]);}
	function grupo_asociado($v = false){ if($v !== false){$this->mCampos["grupo_asociado"]["V"] =  $v; } return new MQLCampo($this->mCampos["grupo_asociado"]);}
	function descripcion_aplicacion($v = false){ if($v !== false){$this->mCampos["descripcion_aplicacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_aplicacion"]);}
	function fecha_ministracion($v = false){ if($v !== false){$this->mCampos["fecha_ministracion"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_ministracion"]);}
	function contrato_corriente_relacionado($v = false){ if($v !== false){$this->mCampos["contrato_corriente_relacionado"]["V"] =  $v; } return new MQLCampo($this->mCampos["contrato_corriente_relacionado"]);}
	function monto_parcialidad($v = false){ if($v !== false){$this->mCampos["monto_parcialidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_parcialidad"]);}
	function oficial_seguimiento($v = false){ if($v !== false){$this->mCampos["oficial_seguimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["oficial_seguimiento"]);}
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
	function persona_asociada($v = false){ if($v !== false){$this->mCampos["persona_asociada"]["V"] =  $v; } return new MQLCampo($this->mCampos["persona_asociada"]);}
	function perfil_de_intereses($v = false){ if($v !== false){$this->mCampos["perfil_de_intereses"]["V"] =  $v; } return new MQLCampo($this->mCampos["perfil_de_intereses"]);}
	function fuente_de_fondeo($v = false){ if($v !== false){$this->mCampos["fuente_de_fondeo"]["V"] =  $v; } return new MQLCampo($this->mCampos["fuente_de_fondeo"]);}
	function fecha_de_primer_pago($v = false){ if($v !== false){$this->mCampos["fecha_de_primer_pago"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_primer_pago"]);}
	function operacion_origen($v = false){ if($v !== false){$this->mCampos["operacion_origen"]["V"] =  $v; } return new MQLCampo($this->mCampos["operacion_origen"]);}
	function tipo_de_origen($v = false){ if($v !== false){$this->mCampos["tipo_de_origen"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_origen"]);}
	function tipo_de_dias_de_pago($v = false){ if($v !== false){$this->mCampos["tipo_de_dias_de_pago"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_dias_de_pago"]);}
	function tipo_de_lugar_de_pago($v = false){ if($v !== false){$this->mCampos["tipo_de_lugar_de_pago"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_lugar_de_pago"]);}
	function tipo_de_dispersion($v = false){ if($v !== false){$this->mCampos["tipo_de_dispersion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_dispersion"]);}
	function fecha_de_proximo_pago($v = false){ if($v !== false){$this->mCampos["fecha_de_proximo_pago"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_proximo_pago"]);}
	function omitir_seguimiento($v = false){ if($v !== false){$this->mCampos["omitir_seguimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["omitir_seguimiento"]);}
	function tasa_cat($v = false){ if($v !== false){$this->mCampos["tasa_cat"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_cat"]);}
	function fecha_ultimo_capital($v = false){ if($v !== false){$this->mCampos["fecha_ultimo_capital"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_ultimo_capital"]);}
	function recibo_ultimo_capital($v = false){ if($v !== false){$this->mCampos["recibo_ultimo_capital"]["V"] =  $v; } return new MQLCampo($this->mCampos["recibo_ultimo_capital"]);}
	function estat_sol($v = false){ if($v !== false){$this->mCampos["estat_sol"]["V"] =  $v; } return new MQLCampo($this->mCampos["estat_sol"]);}
	function criesgo($v = false){ if($v !== false){$this->mCampos["criesgo"]["V"] =  $v; } return new MQLCampo($this->mCampos["criesgo"]);}
	function bonificaciones($v = false){ if($v !== false){$this->mCampos["bonificaciones"]["V"] =  $v; } return new MQLCampo($this->mCampos["bonificaciones"]);}
	function gastoscbza($v = false){ if($v !== false){$this->mCampos["gastoscbza"]["V"] =  $v; } return new MQLCampo($this->mCampos["gastoscbza"]);}
	function iva_interes($v = false){ if($v !== false){$this->mCampos["iva_interes"]["V"] =  $v; } return new MQLCampo($this->mCampos["iva_interes"]);}
	function iva_otros($v = false){ if($v !== false){$this->mCampos["iva_otros"]["V"] =  $v; } return new MQLCampo($this->mCampos["iva_otros"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
}

/*	ORM: Tabla:	creditos_plan_de_pagos	-	Generado:	[11/6/2015 15:09]	*/
/*	ORM: Tabla:	creditos_plan_de_pagos	-	Generado:	[21/3/2018 23:59]	*/
class cCreditos_plan_de_pagos {
	private $mCampos	= array("plan_de_pago" => array("N"=>"plan_de_pago","T"=>"INT","V"=>"","L"=>11),"clave_de_credito" => array("N"=>"clave_de_credito","T"=>"BIGINT","V"=>"1","L"=>20),"numero_de_parcialidad" => array("N"=>"numero_de_parcialidad","T"=>"INT","V"=>"1","L"=>11),"fecha_de_pago" => array("N"=>"fecha_de_pago","T"=>"DATE","V"=>"","L"=>0),"capital" => array("N"=>"capital","T"=>"DOUBLE","V"=>"0.00","L"=>29),"interes" => array("N"=>"interes","T"=>"FLOAT","V"=>"0.00","L"=>25),"impuesto" => array("N"=>"impuesto","T"=>"FLOAT","V"=>"0.00","L"=>25),"otros" => array("N"=>"otros","T"=>"FLOAT","V"=>"0.00","L"=>25),"otros_codigo" => array("N"=>"otros_codigo","T"=>"INT","V"=>"0","L"=>11),"saldo_inverso" => array("N"=>"saldo_inverso","T"=>"FLOAT","V"=>"0.00","L"=>33),"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"matriz","L"=>20),"ahorro" => array("N"=>"ahorro","T"=>"FLOAT","V"=>"0.00","L"=>25),"penas" => array("N"=>"penas","T"=>"FLOAT","V"=>"0.00","L"=>25),"gtoscbza" => array("N"=>"gtoscbza","T"=>"FLOAT","V"=>"0.00","L"=>25),"mora" => array("N"=>"mora","T"=>"FLOAT","V"=>"0.00","L"=>25),"descuentos" => array("N"=>"descuentos","T"=>"FLOAT","V"=>"0.00","L"=>25),"iva_castigos" => array("N"=>"iva_castigos","T"=>"FLOAT","V"=>"0.00","L"=>25),"total_base" => array("N"=>"total_base","T"=>"DOUBLE","V"=>"0.00","L"=>29),"total_c_otros" => array("N"=>"total_c_otros","T"=>"DOUBLE","V"=>"0.00","L"=>29),"total_c_castigos" => array("N"=>"total_c_castigos","T"=>"DOUBLE","V"=>"0.00","L"=>29));
	public $PLAN_DE_PAGO = "plan_de_pago"; public $CLAVE_DE_CREDITO = "clave_de_credito"; public $NUMERO_DE_PARCIALIDAD = "numero_de_parcialidad"; public $FECHA_DE_PAGO = "fecha_de_pago"; public $CAPITAL = "capital"; public $INTERES = "interes"; public $IMPUESTO = "impuesto"; public $OTROS = "otros"; public $OTROS_CODIGO = "otros_codigo"; public $SALDO_INVERSO = "saldo_inverso"; public $SUCURSAL = "sucursal"; public $AHORRO = "ahorro"; public $PENAS = "penas"; public $GTOSCBZA = "gtoscbza"; public $MORA = "mora"; public $DESCUENTOS = "descuentos"; public $IVA_CASTIGOS = "iva_castigos"; public $TOTAL_BASE = "total_base"; public $TOTAL_C_OTROS = "total_c_otros"; public $TOTAL_C_CASTIGOS = "total_c_castigos"; 
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_plan_de_pagos";}
	function getKey(){ return "plan_de_pago";}
	function plan_de_pago($v = false){ if($v !== false){$this->mCampos["plan_de_pago"]["V"] =  $v; } return new MQLCampo($this->mCampos["plan_de_pago"]);}
	function clave_de_credito($v = false){ if($v !== false){$this->mCampos["clave_de_credito"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_credito"]);}
	function numero_de_parcialidad($v = false){ if($v !== false){$this->mCampos["numero_de_parcialidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_de_parcialidad"]);}
	function fecha_de_pago($v = false){ if($v !== false){$this->mCampos["fecha_de_pago"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_pago"]);}
	function capital($v = false){ if($v !== false){$this->mCampos["capital"]["V"] =  $v; } return new MQLCampo($this->mCampos["capital"]);}
	function interes($v = false){ if($v !== false){$this->mCampos["interes"]["V"] =  $v; } return new MQLCampo($this->mCampos["interes"]);}
	function impuesto($v = false){ if($v !== false){$this->mCampos["impuesto"]["V"] =  $v; } return new MQLCampo($this->mCampos["impuesto"]);}
	function otros($v = false){ if($v !== false){$this->mCampos["otros"]["V"] =  $v; } return new MQLCampo($this->mCampos["otros"]);}
	function otros_codigo($v = false){ if($v !== false){$this->mCampos["otros_codigo"]["V"] =  $v; } return new MQLCampo($this->mCampos["otros_codigo"]);}
	function saldo_inverso($v = false){ if($v !== false){$this->mCampos["saldo_inverso"]["V"] =  $v; } return new MQLCampo($this->mCampos["saldo_inverso"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function ahorro($v = false){ if($v !== false){$this->mCampos["ahorro"]["V"] =  $v; } return new MQLCampo($this->mCampos["ahorro"]);}
	function penas($v = false){ if($v !== false){$this->mCampos["penas"]["V"] =  $v; } return new MQLCampo($this->mCampos["penas"]);}
	function gtoscbza($v = false){ if($v !== false){$this->mCampos["gtoscbza"]["V"] =  $v; } return new MQLCampo($this->mCampos["gtoscbza"]);}
	function mora($v = false){ if($v !== false){$this->mCampos["mora"]["V"] =  $v; } return new MQLCampo($this->mCampos["mora"]);}
	function descuentos($v = false){ if($v !== false){$this->mCampos["descuentos"]["V"] =  $v; } return new MQLCampo($this->mCampos["descuentos"]);}
	function iva_castigos($v = false){ if($v !== false){$this->mCampos["iva_castigos"]["V"] =  $v; } return new MQLCampo($this->mCampos["iva_castigos"]);}
	function total_base($v = false){ if($v !== false){$this->mCampos["total_base"]["V"] =  $v; } return new MQLCampo($this->mCampos["total_base"]);}
	function total_c_otros($v = false){ if($v !== false){$this->mCampos["total_c_otros"]["V"] =  $v; } return new MQLCampo($this->mCampos["total_c_otros"]);}
	function total_c_castigos($v = false){ if($v !== false){$this->mCampos["total_c_castigos"]["V"] =  $v; } return new MQLCampo($this->mCampos["total_c_castigos"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	creditos_firmantes	-	Generado:	[24/7/2017 15:41]	*/
class cCreditos_firmantes {
	private $mCampos	= array("idcreditos_firmantes" => array("N"=>"idcreditos_firmantes","T"=>"INT","V"=>"","L"=>11),"credito" => array("N"=>"credito","T"=>"BIGINT","V"=>"","L"=>20),"persona" => array("N"=>"persona","T"=>"BIGINT","V"=>"","L"=>20),"rol_firmante" => array("N"=>"rol_firmante","T"=>"VARCHAR","V"=>"","L"=>50),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_firmantes";}
	function getKey(){ return "idcreditos_firmantes";}
	function idcreditos_firmantes($v = false){ if($v !== false){$this->mCampos["idcreditos_firmantes"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_firmantes"]);}
	function credito($v = false){ if($v !== false){$this->mCampos["credito"]["V"] =  $v; } return new MQLCampo($this->mCampos["credito"]);}
	function persona($v = false){ if($v !== false){$this->mCampos["persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["persona"]);}
	function rol_firmante($v = false){ if($v !== false){$this->mCampos["rol_firmante"]["V"] =  $v; } return new MQLCampo($this->mCampos["rol_firmante"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}

//--- creditos_rechazados 		Generado: 13-09-24 12
/*	ORM: Tabla:	creditos_rechazados	-	Generado:	[29/6/2017 17:48]	*/
/*	ORM: Tabla:	creditos_rechazados	-	Generado:	[30/6/2017 13:14]	*/
/*	ORM: Tabla:	creditos_rechazados	-	Generado:	[25/4/2018 18:16]	*/
class cCreditos_rechazados {
	private $mCampos	= array("idcreditos_rechazados" => array("N"=>"idcreditos_rechazados","T"=>"INT","V"=>"","L"=>11),"numero_de_credito" => array("N"=>"numero_de_credito","T"=>"BIGINT","V"=>"","L"=>20),"fecha_de_rechazo" => array("N"=>"fecha_de_rechazo","T"=>"DATE","V"=>"","L"=>0),"razones" => array("N"=>"razones","T"=>"MEDIUMTEXT","V"=>"","L"=>0),"notas" => array("N"=>"notas","T"=>"VARCHAR","V"=>"","L"=>200),"claverechazo" => array("N"=>"claverechazo","T"=>"INT","V"=>"0","L"=>3),"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"0","L"=>8),"tiempo" => array("N"=>"tiempo","T"=>"INT","V"=>"0","L"=>11),"estatusactivo" => array("N"=>"estatusactivo","T"=>"INT","V"=>"1","L"=>2));
	public $IDCREDITOS_RECHAZADOS = "idcreditos_rechazados"; public $NUMERO_DE_CREDITO = "numero_de_credito"; public $FECHA_DE_RECHAZO = "fecha_de_rechazo"; public $RAZONES = "razones"; public $NOTAS = "notas"; public $CLAVERECHAZO = "claverechazo"; public $IDUSUARIO = "idusuario"; public $TIEMPO = "tiempo"; public $ESTATUSACTIVO = "estatusactivo";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_rechazados";}
	function getKey(){ return "idcreditos_rechazados";}
	function idcreditos_rechazados($v = false){ if($v !== false){$this->mCampos["idcreditos_rechazados"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_rechazados"]);}
	function numero_de_credito($v = false){ if($v !== false){$this->mCampos["numero_de_credito"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_de_credito"]);}
	function fecha_de_rechazo($v = false){ if($v !== false){$this->mCampos["fecha_de_rechazo"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_rechazo"]);}
	function razones($v = false){ if($v !== false){$this->mCampos["razones"]["V"] =  $v; } return new MQLCampo($this->mCampos["razones"]);}
	function notas($v = false){ if($v !== false){$this->mCampos["notas"]["V"] =  $v; } return new MQLCampo($this->mCampos["notas"]);}
	function claverechazo($v = false){ if($v !== false){$this->mCampos["claverechazo"]["V"] =  $v; } return new MQLCampo($this->mCampos["claverechazo"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function tiempo($v = false){ if($v !== false){$this->mCampos["tiempo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tiempo"]);}
	function estatusactivo($v = false){ if($v !== false){$this->mCampos["estatusactivo"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatusactivo"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
}
/*	ORM: Tabla:	creditos_sic_notas	-	Generado:	[07/7/2017 10:15]	*/
class cCreditos_sic_notas {
	private $mCampos	= array("idcreditos_sic_notas" => array("N"=>"idcreditos_sic_notas","T"=>"INT","V"=>"","L"=>11),"credito" => array("N"=>"credito","T"=>"BIGINT","V"=>"","L"=>20),"clave_nota" => array("N"=>"clave_nota","T"=>"VARCHAR","V"=>"","L"=>4),"texto_nota" => array("N"=>"texto_nota","T"=>"VARCHAR","V"=>"","L"=>50),"estatus" => array("N"=>"estatus","T"=>"INT","V"=>"0","L"=>4),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_sic_notas";}
	function getKey(){ return "idcreditos_sic_notas";}
	function idcreditos_sic_notas($v = false){ if($v !== false){$this->mCampos["idcreditos_sic_notas"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_sic_notas"]);}
	function credito($v = false){ if($v !== false){$this->mCampos["credito"]["V"] =  $v; } return new MQLCampo($this->mCampos["credito"]);}
	function clave_nota($v = false){ if($v !== false){$this->mCampos["clave_nota"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_nota"]);}
	function texto_nota($v = false){ if($v !== false){$this->mCampos["texto_nota"]["V"] =  $v; } return new MQLCampo($this->mCampos["texto_nota"]);}
	function estatus($v = false){ if($v !== false){$this->mCampos["estatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}

/*	ORM: Tabla:	creditos_tipo_de_pago	-	Generado:	[07/11/2013 15:11]	*/
/*	ORM: Tabla:	creditos_tipo_de_pago	-	Generado:	[07/7/2017 10:16]	*/
/*	ORM: Tabla:	creditos_tipo_de_pago	-	Generado:	[24/2/2018 13:25]	*/
class cCreditos_tipo_de_pago {
	private $mCampos	= array("idcreditos_tipo_de_pago" => array("N"=>"idcreditos_tipo_de_pago","T"=>"INT","V"=>"","L"=>10),"descripcion" => array("N"=>"descripcion","T"=>"VARCHAR","V"=>"","L"=>45),"con_capital" => array("N"=>"con_capital","T"=>"INT","V"=>"1","L"=>2),"estatus" => array("N"=>"estatus","T"=>"INT","V"=>"1","L"=>2));
	public $IDCREDITOS_TIPO_DE_PAGO = "idcreditos_tipo_de_pago"; public $DESCRIPCION = "descripcion"; public $CON_CAPITAL = "con_capital"; public $ESTATUS = "estatus";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_tipo_de_pago";}
	function getKey(){ return "idcreditos_tipo_de_pago";}
	function idcreditos_tipo_de_pago($v = false){ if($v !== false){$this->mCampos["idcreditos_tipo_de_pago"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_tipo_de_pago"]);}
	function descripcion($v = false){ if($v !== false){$this->mCampos["descripcion"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion"]);}
	function con_capital($v = false){ if($v !== false){$this->mCampos["con_capital"]["V"] =  $v; } return new MQLCampo($this->mCampos["con_capital"]);}
	function estatus($v = false){ if($v !== false){$this->mCampos["estatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}

//--- creditos_nivelesriesgo 		Generado: 13-09-24 16
/*	ORM: Tabla:	creditos_nivelesriesgo	-	Generado:	[07/7/2017 10:16]	*/
class cCreditos_nivelesriesgo {
	private $mCampos	= array("idcreditos_nivelesriesgo" => array("N"=>"idcreditos_nivelesriesgo","T"=>"INT","V"=>"0","L"=>4),"descripcion_nivelesriesgo" => array("N"=>"descripcion_nivelesriesgo","T"=>"VARCHAR","V"=>"","L"=>65),"nivel_riesgo" => array("N"=>"nivel_riesgo","T"=>"INT","V"=>"0","L"=>4),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_nivelesriesgo";}
	function getKey(){ return "idcreditos_nivelesriesgo";}
	function idcreditos_nivelesriesgo($v = false){ if($v !== false){$this->mCampos["idcreditos_nivelesriesgo"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_nivelesriesgo"]);}
	function descripcion_nivelesriesgo($v = false){ if($v !== false){$this->mCampos["descripcion_nivelesriesgo"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_nivelesriesgo"]);}
	function nivel_riesgo($v = false){ if($v !== false){$this->mCampos["nivel_riesgo"]["V"] =  $v; } return new MQLCampo($this->mCampos["nivel_riesgo"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	creditos_nivelesdegrupo	-	Generado:	[21/4/2018 13:28]	*/
class cCreditos_nivelesdegrupo {
	private $mCampos	= array("idcreditos_nivelesdegrupo" => array("N"=>"idcreditos_nivelesdegrupo","T"=>"INT","V"=>"","L"=>11),"nivel" => array("N"=>"nivel","T"=>"INT","V"=>"1","L"=>4),"monto_xintegrante" => array("N"=>"monto_xintegrante","T"=>"FLOAT","V"=>"0.00","L"=>25),"tasa_ahorro" => array("N"=>"tasa_ahorro","T"=>"FLOAT","V"=>"0.000","L"=>13),"tasa_normal" => array("N"=>"tasa_normal","T"=>"FLOAT","V"=>"0.000","L"=>13),"tasa_moratorio" => array("N"=>"tasa_moratorio","T"=>"FLOAT","V"=>"0.000","L"=>13),"dias_maximo" => array("N"=>"dias_maximo","T"=>"INT","V"=>"30","L"=>11));
	public $IDCREDITOS_NIVELESDEGRUPO = "idcreditos_nivelesdegrupo"; public $NIVEL = "nivel"; public $MONTO_XINTEGRANTE = "monto_xintegrante"; public $TASA_AHORRO = "tasa_ahorro"; public $TASA_NORMAL = "tasa_normal"; public $TASA_MORATORIO = "tasa_moratorio"; public $DIAS_MAXIMO = "dias_maximo";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_nivelesdegrupo";}
	function getKey(){ return "idcreditos_nivelesdegrupo";}
	function idcreditos_nivelesdegrupo($v = false){ if($v !== false){$this->mCampos["idcreditos_nivelesdegrupo"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_nivelesdegrupo"]);}
	function nivel($v = false){ if($v !== false){$this->mCampos["nivel"]["V"] =  $v; } return new MQLCampo($this->mCampos["nivel"]);}
	function monto_xintegrante($v = false){ if($v !== false){$this->mCampos["monto_xintegrante"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_xintegrante"]);}
	function tasa_ahorro($v = false){ if($v !== false){$this->mCampos["tasa_ahorro"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_ahorro"]);}
	function tasa_normal($v = false){ if($v !== false){$this->mCampos["tasa_normal"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_normal"]);}
	function tasa_moratorio($v = false){ if($v !== false){$this->mCampos["tasa_moratorio"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_moratorio"]);}
	function dias_maximo($v = false){ if($v !== false){$this->mCampos["dias_maximo"]["V"] =  $v; } return new MQLCampo($this->mCampos["dias_maximo"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	creditos_flujoefvo	-	Generado:	[14/5/2015 11:49]	*/
/*	ORM: Tabla:	creditos_flujoefvo	-	Generado:	[24/3/2017 15:38]	*/
class cCreditos_flujoefvo {
	private $mCampos	= array("idcreditos_flujoefvo" => array("N"=>"idcreditos_flujoefvo","T"=>"INT","V"=>"","L"=>10),"solicitud_flujo" => array("N"=>"solicitud_flujo","T"=>"BIGINT","V"=>"1","L"=>20),"socio_flujo" => array("N"=>"socio_flujo","T"=>"BIGINT","V"=>"1","L"=>20),"tipo_flujo" => array("N"=>"tipo_flujo","T"=>"INT","V"=>"99","L"=>4),"origen_flujo" => array("N"=>"origen_flujo","T"=>"INT","V"=>"99","L"=>4),"monto_flujo" => array("N"=>"monto_flujo","T"=>"FLOAT","V"=>"0.00","L"=>25),"afectacion_neta" => array("N"=>"afectacion_neta","T"=>"FLOAT","V"=>"0.00","L"=>25),"periocidad_flujo" => array("N"=>"periocidad_flujo","T"=>"INT","V"=>"99","L"=>4),"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"99","L"=>4),"observacion_flujo" => array("N"=>"observacion_flujo","T"=>"VARCHAR","V"=>"","L"=>150),"descripcion_completa" => array("N"=>"descripcion_completa","T"=>"VARCHAR","V"=>"","L"=>200),"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"MATRIZ","L"=>10),"fecha_captura" => array("N"=>"fecha_captura","T"=>"DATE","V"=>"2007-12-31","L"=>0),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_flujoefvo";}
	function getKey(){ return "idcreditos_flujoefvo";}
	function idcreditos_flujoefvo($v = false){ if($v !== false){$this->mCampos["idcreditos_flujoefvo"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_flujoefvo"]);}
	function solicitud_flujo($v = false){ if($v !== false){$this->mCampos["solicitud_flujo"]["V"] =  $v; } return new MQLCampo($this->mCampos["solicitud_flujo"]);}
	function socio_flujo($v = false){ if($v !== false){$this->mCampos["socio_flujo"]["V"] =  $v; } return new MQLCampo($this->mCampos["socio_flujo"]);}
	function tipo_flujo($v = false){ if($v !== false){$this->mCampos["tipo_flujo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_flujo"]);}
	function origen_flujo($v = false){ if($v !== false){$this->mCampos["origen_flujo"]["V"] =  $v; } return new MQLCampo($this->mCampos["origen_flujo"]);}
	function monto_flujo($v = false){ if($v !== false){$this->mCampos["monto_flujo"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_flujo"]);}
	function afectacion_neta($v = false){ if($v !== false){$this->mCampos["afectacion_neta"]["V"] =  $v; } return new MQLCampo($this->mCampos["afectacion_neta"]);}
	function periocidad_flujo($v = false){ if($v !== false){$this->mCampos["periocidad_flujo"]["V"] =  $v; } return new MQLCampo($this->mCampos["periocidad_flujo"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function observacion_flujo($v = false){ if($v !== false){$this->mCampos["observacion_flujo"]["V"] =  $v; } return new MQLCampo($this->mCampos["observacion_flujo"]);}
	function descripcion_completa($v = false){ if($v !== false){$this->mCampos["descripcion_completa"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_completa"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function fecha_captura($v = false){ if($v !== false){$this->mCampos["fecha_captura"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_captura"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}
/*	ORM: Tabla:	creditos_tflujo	-	Generado:	[24/3/2017 15:39]	*/
class cCreditos_tflujo {
	private $mCampos	= array("idcreditos_tflujo" => array("N"=>"idcreditos_tflujo","T"=>"INT","V"=>"0","L"=>4),"descripcion_tflujo" => array("N"=>"descripcion_tflujo","T"=>"VARCHAR","V"=>"","L"=>45),"tipo_flujo" => array("N"=>"tipo_flujo","T"=>"INT","V"=>"0","L"=>4),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_tflujo";}
	function getKey(){ return "idcreditos_tflujo";}
	function idcreditos_tflujo($v = false){ if($v !== false){$this->mCampos["idcreditos_tflujo"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_tflujo"]);}
	function descripcion_tflujo($v = false){ if($v !== false){$this->mCampos["descripcion_tflujo"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_tflujo"]);}
	function tipo_flujo($v = false){ if($v !== false){$this->mCampos["tipo_flujo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_flujo"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}
/*	ORM: Tabla:	creditos_origenflujo	-	Generado:	[24/3/2017 15:39]	*/
class cCreditos_origenflujo {
	private $mCampos	= array("idcreditos_origenflujo" => array("N"=>"idcreditos_origenflujo","T"=>"INT","V"=>"0","L"=>4),"descripcion_origenflujo" => array("N"=>"descripcion_origenflujo","T"=>"VARCHAR","V"=>"","L"=>65),"origen_flujo" => array("N"=>"origen_flujo","T"=>"INT","V"=>"0","L"=>4),"afectacion" => array("N"=>"afectacion","T"=>"INT","V"=>"0","L"=>10),"tipo" => array("N"=>"tipo","T"=>"INT","V"=>"0","L"=>10),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_origenflujo";}
	function getKey(){ return "idcreditos_origenflujo";}
	function idcreditos_origenflujo($v = false){ if($v !== false){$this->mCampos["idcreditos_origenflujo"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_origenflujo"]);}
	function descripcion_origenflujo($v = false){ if($v !== false){$this->mCampos["descripcion_origenflujo"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_origenflujo"]);}
	function origen_flujo($v = false){ if($v !== false){$this->mCampos["origen_flujo"]["V"] =  $v; } return new MQLCampo($this->mCampos["origen_flujo"]);}
	function afectacion($v = false){ if($v !== false){$this->mCampos["afectacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["afectacion"]);}
	function tipo($v = false){ if($v !== false){$this->mCampos["tipo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}
/*	ORM: Tabla:	creditos_periocidadflujo	-	Generado:	[24/3/2017 15:40]	*/
class cCreditos_periocidadflujo {
	private $mCampos	= array("idcreditos_periocidadflujo" => array("N"=>"idcreditos_periocidadflujo","T"=>"INT","V"=>"0","L"=>4),"descripcion_periocidadflujo" => array("N"=>"descripcion_periocidadflujo","T"=>"VARCHAR","V"=>"","L"=>65),"periocidad_flujo" => array("N"=>"periocidad_flujo","T"=>"INT","V"=>"0","L"=>4),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_periocidadflujo";}
	function getKey(){ return "idcreditos_periocidadflujo";}
	function idcreditos_periocidadflujo($v = false){ if($v !== false){$this->mCampos["idcreditos_periocidadflujo"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_periocidadflujo"]);}
	function descripcion_periocidadflujo($v = false){ if($v !== false){$this->mCampos["descripcion_periocidadflujo"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_periocidadflujo"]);}
	function periocidad_flujo($v = false){ if($v !== false){$this->mCampos["periocidad_flujo"]["V"] =  $v; } return new MQLCampo($this->mCampos["periocidad_flujo"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	creditos_lineas	-	Generado:	[29/3/2016 17:00]	*/
/*	ORM: Tabla:	creditos_lineas	-	Generado:	[01/7/2017 10:48]	*/
class cCreditos_lineas {
	private $mCampos	= array("idcreditos_lineas" => array("N"=>"idcreditos_lineas","T"=>"INT","V"=>"","L"=>10),"numero_socio" => array("N"=>"numero_socio","T"=>"BIGINT","V"=>"1","L"=>20),"monto_linea" => array("N"=>"monto_linea","T"=>"DOUBLE","V"=>"0.00","L"=>33),"observaciones" => array("N"=>"observaciones","T"=>"VARCHAR","V"=>"","L"=>100),"numerohipoteca" => array("N"=>"numerohipoteca","T"=>"VARCHAR","V"=>"","L"=>100),"monto_hipoteca" => array("N"=>"monto_hipoteca","T"=>"DOUBLE","V"=>"0.00","L"=>33),"fecha_de_vencimiento" => array("N"=>"fecha_de_vencimiento","T"=>"DATE","V"=>"0000-00-00","L"=>0),"fecha_de_alta" => array("N"=>"fecha_de_alta","T"=>"DATE","V"=>"0000-00-00","L"=>0),"estado" => array("N"=>"estado","T"=>"INT","V"=>"1","L"=>4),"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"99","L"=>4),"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"MATRIZ","L"=>20),"eacp" => array("N"=>"eacp","T"=>"VARCHAR","V"=>"EN_TRAMITE","L"=>20),"fecha_ultima_operacion" => array("N"=>"fecha_ultima_operacion","T"=>"DATE","V"=>"0000-00-00","L"=>0),"saldo_disponible" => array("N"=>"saldo_disponible","T"=>"DOUBLE","V"=>"0.00","L"=>33),"oficial_de_credito" => array("N"=>"oficial_de_credito","T"=>"INT","V"=>"99","L"=>11),"fecha_de_cancelacion" => array("N"=>"fecha_de_cancelacion","T"=>"DATE","V"=>"0000-00-00","L"=>0),"razones_de_cancelacion" => array("N"=>"razones_de_cancelacion","T"=>"VARCHAR","V"=>"","L"=>100),"tasa" => array("N"=>"tasa","T"=>"FLOAT","V"=>"0.000","L"=>13),"periocidad" => array("N"=>"periocidad","T"=>"INT","V"=>"30","L"=>4),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_lineas";}
	function getKey(){ return "idcreditos_lineas";}
	function idcreditos_lineas($v = false){ if($v !== false){$this->mCampos["idcreditos_lineas"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_lineas"]);}
	function numero_socio($v = false){ if($v !== false){$this->mCampos["numero_socio"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_socio"]);}
	function monto_linea($v = false){ if($v !== false){$this->mCampos["monto_linea"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_linea"]);}
	function observaciones($v = false){ if($v !== false){$this->mCampos["observaciones"]["V"] =  $v; } return new MQLCampo($this->mCampos["observaciones"]);}
	function numerohipoteca($v = false){ if($v !== false){$this->mCampos["numerohipoteca"]["V"] =  $v; } return new MQLCampo($this->mCampos["numerohipoteca"]);}
	function monto_hipoteca($v = false){ if($v !== false){$this->mCampos["monto_hipoteca"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_hipoteca"]);}
	function fecha_de_vencimiento($v = false){ if($v !== false){$this->mCampos["fecha_de_vencimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_vencimiento"]);}
	function fecha_de_alta($v = false){ if($v !== false){$this->mCampos["fecha_de_alta"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_alta"]);}
	function estado($v = false){ if($v !== false){$this->mCampos["estado"]["V"] =  $v; } return new MQLCampo($this->mCampos["estado"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function eacp($v = false){ if($v !== false){$this->mCampos["eacp"]["V"] =  $v; } return new MQLCampo($this->mCampos["eacp"]);}
	function fecha_ultima_operacion($v = false){ if($v !== false){$this->mCampos["fecha_ultima_operacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_ultima_operacion"]);}
	function saldo_disponible($v = false){ if($v !== false){$this->mCampos["saldo_disponible"]["V"] =  $v; } return new MQLCampo($this->mCampos["saldo_disponible"]);}
	function oficial_de_credito($v = false){ if($v !== false){$this->mCampos["oficial_de_credito"]["V"] =  $v; } return new MQLCampo($this->mCampos["oficial_de_credito"]);}
	function fecha_de_cancelacion($v = false){ if($v !== false){$this->mCampos["fecha_de_cancelacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_cancelacion"]);}
	function razones_de_cancelacion($v = false){ if($v !== false){$this->mCampos["razones_de_cancelacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["razones_de_cancelacion"]);}
	function tasa($v = false){ if($v !== false){$this->mCampos["tasa"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa"]);}
	function periocidad($v = false){ if($v !== false){$this->mCampos["periocidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["periocidad"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}

/*	ORM: Tabla:	creditos_productos_costos	-	Generado:	[04/3/2016 11:10]	*/
/*	ORM: Tabla:	creditos_productos_costos	-	Generado:	[10/1/2017 17:58]	*/
/*	ORM: Tabla:	creditos_productos_costos	-	Generado:	[18/5/2018 17:30]	*/
class cCreditos_productos_costos {
	private $mCampos	= array("idcreditos_productos_costos" => array("N"=>"idcreditos_productos_costos","T"=>"INT","V"=>"","L"=>11),"clave_de_producto" => array("N"=>"clave_de_producto","T"=>"INT","V"=>"","L"=>11),"clave_de_operacion" => array("N"=>"clave_de_operacion","T"=>"INT","V"=>"0","L"=>11),"unidades" => array("N"=>"unidades","T"=>"FLOAT","V"=>"","L"=>17),"unidad_de_medida" => array("N"=>"unidad_de_medida","T"=>"INT","V"=>"0","L"=>2),"editable" => array("N"=>"editable","T"=>"INT","V"=>"0","L"=>2),"en_plan" => array("N"=>"en_plan","T"=>"INT","V"=>"0","L"=>2),"exigencia" => array("N"=>"exigencia","T"=>"INT","V"=>"0","L"=>2),"estatus" => array("N"=>"estatus","T"=>"INT","V"=>"1","L"=>2),"aplicar_desde" => array("N"=>"aplicar_desde","T"=>"DATE","V"=>"","L"=>0),"aplicar_hasta" => array("N"=>"aplicar_hasta","T"=>"DATE","V"=>"","L"=>0));
	public $IDCREDITOS_PRODUCTOS_COSTOS = "idcreditos_productos_costos"; public $CLAVE_DE_PRODUCTO = "clave_de_producto"; public $CLAVE_DE_OPERACION = "clave_de_operacion"; public $UNIDADES = "unidades"; public $UNIDAD_DE_MEDIDA = "unidad_de_medida"; public $EDITABLE = "editable"; public $EN_PLAN = "en_plan"; public $EXIGENCIA = "exigencia"; public $ESTATUS = "estatus"; public $APLICAR_DESDE = "aplicar_desde"; public $APLICAR_HASTA = "aplicar_hasta";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_productos_costos";}
	function getKey(){ return "idcreditos_productos_costos";}
	function idcreditos_productos_costos($v = false){ if($v !== false){$this->mCampos["idcreditos_productos_costos"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_productos_costos"]);}
	function clave_de_producto($v = false){ if($v !== false){$this->mCampos["clave_de_producto"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_producto"]);}
	function clave_de_operacion($v = false){ if($v !== false){$this->mCampos["clave_de_operacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_operacion"]);}
	function unidades($v = false){ if($v !== false){$this->mCampos["unidades"]["V"] =  $v; } return new MQLCampo($this->mCampos["unidades"]);}
	function unidad_de_medida($v = false){ if($v !== false){$this->mCampos["unidad_de_medida"]["V"] =  $v; } return new MQLCampo($this->mCampos["unidad_de_medida"]);}
	function editable($v = false){ if($v !== false){$this->mCampos["editable"]["V"] =  $v; } return new MQLCampo($this->mCampos["editable"]);}
	function en_plan($v = false){ if($v !== false){$this->mCampos["en_plan"]["V"] =  $v; } return new MQLCampo($this->mCampos["en_plan"]);}
	function exigencia($v = false){ if($v !== false){$this->mCampos["exigencia"]["V"] =  $v; } return new MQLCampo($this->mCampos["exigencia"]);}
	function estatus($v = false){ if($v !== false){$this->mCampos["estatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus"]);}
	function aplicar_desde($v = false){ if($v !== false){$this->mCampos["aplicar_desde"]["V"] =  $v; } return new MQLCampo($this->mCampos["aplicar_desde"]);}
	function aplicar_hasta($v = false){ if($v !== false){$this->mCampos["aplicar_hasta"]["V"] =  $v; } return new MQLCampo($this->mCampos["aplicar_hasta"]);}
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
/*	ORM: Tabla:	creditos_preclientes	-	Generado:	[28/4/2016 15:15]	*/
/*	ORM: Tabla:	creditos_preclientes	-	Generado:	[21/9/2016 10:28]	*/
/*	ORM: Tabla:	creditos_preclientes	-	Generado:	[28/9/2016 14:55]	*/
/*	ORM: Tabla:	creditos_preclientes	-	Generado:	[29/9/2016 14:17]	*/
/*	ORM: Tabla:	creditos_preclientes	-	Generado:	[02/1/2018 16:29]	*/
/*	ORM: Tabla:	creditos_preclientes	-	Generado:	[15/5/2018 16:16]	*/
/*	ORM: Tabla:	creditos_preclientes	-	Generado:	[24/5/2018 18:28]	*/
class cCreditos_preclientes {
	private $mCampos	= array("idcontrol" => array("N"=>"idcontrol","T"=>"INT","V"=>"","L"=>11),"nombres" => array("N"=>"nombres","T"=>"VARCHAR","V"=>"","L"=>200),"apellido1" => array("N"=>"apellido1","T"=>"VARCHAR","V"=>"","L"=>25),"apellido2" => array("N"=>"apellido2","T"=>"VARCHAR","V"=>"","L"=>25),"rfc" => array("N"=>"rfc","T"=>"VARCHAR","V"=>"","L"=>20),"curp" => array("N"=>"curp","T"=>"VARCHAR","V"=>"","L"=>20),"telefono" => array("N"=>"telefono","T"=>"VARCHAR","V"=>"0","L"=>20),"fecha_de_registro" => array("N"=>"fecha_de_registro","T"=>"DATE","V"=>"","L"=>0),"producto" => array("N"=>"producto","T"=>"INT","V"=>"99","L"=>11),"periocidad" => array("N"=>"periocidad","T"=>"INT","V"=>"99","L"=>11),"pagos" => array("N"=>"pagos","T"=>"INT","V"=>"99","L"=>11),"aplicacion" => array("N"=>"aplicacion","T"=>"INT","V"=>"99","L"=>11),"notas" => array("N"=>"notas","T"=>"TEXT","V"=>"","L"=>0),"monto" => array("N"=>"monto","T"=>"DOUBLE","V"=>"0.00","L"=>33),"email" => array("N"=>"email","T"=>"VARCHAR","V"=>"","L"=>40),"idpersona" => array("N"=>"idpersona","T"=>"BIGINT","V"=>"0","L"=>20),"idcredito" => array("N"=>"idcredito","T"=>"BIGINT","V"=>"0","L"=>20),"idorigen" => array("N"=>"idorigen","T"=>"INT","V"=>"1","L"=>4),"idestado" => array("N"=>"idestado","T"=>"INT","V"=>"1","L"=>2),"idoficial" => array("N"=>"idoficial","T"=>"INT","V"=>"0","L"=>8),"idexterno" => array("N"=>"idexterno","T"=>"VARCHAR","V"=>"","L"=>60),"tipocuota_id" => array("N"=>"tipocuota_id","T"=>"INT","V"=>"2","L"=>2),"tasa_interes" => array("N"=>"tasa_interes","T"=>"FLOAT","V"=>"0.000","L"=>13));
	public $IDCONTROL = "idcontrol"; public $NOMBRES = "nombres"; public $APELLIDO1 = "apellido1"; public $APELLIDO2 = "apellido2"; public $RFC = "rfc"; public $CURP = "curp"; public $TELEFONO = "telefono"; public $FECHA_DE_REGISTRO = "fecha_de_registro"; public $PRODUCTO = "producto"; public $PERIOCIDAD = "periocidad"; public $PAGOS = "pagos"; public $APLICACION = "aplicacion"; public $NOTAS = "notas"; public $MONTO = "monto"; public $EMAIL = "email"; public $IDPERSONA = "idpersona"; public $IDCREDITO = "idcredito"; public $IDORIGEN = "idorigen"; public $IDESTADO = "idestado"; public $IDOFICIAL = "idoficial"; public $IDEXTERNO = "idexterno"; public $TIPOCUOTA_ID = "tipocuota_id"; public $TASA_INTERES = "tasa_interes";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_preclientes";}
	function getKey(){ return "idcontrol";}
	function idcontrol($v = false){ if($v !== false){$this->mCampos["idcontrol"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcontrol"]);}
	function nombres($v = false){ if($v !== false){$this->mCampos["nombres"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombres"]);}
	function apellido1($v = false){ if($v !== false){$this->mCampos["apellido1"]["V"] =  $v; } return new MQLCampo($this->mCampos["apellido1"]);}
	function apellido2($v = false){ if($v !== false){$this->mCampos["apellido2"]["V"] =  $v; } return new MQLCampo($this->mCampos["apellido2"]);}
	function rfc($v = false){ if($v !== false){$this->mCampos["rfc"]["V"] =  $v; } return new MQLCampo($this->mCampos["rfc"]);}
	function curp($v = false){ if($v !== false){$this->mCampos["curp"]["V"] =  $v; } return new MQLCampo($this->mCampos["curp"]);}
	function telefono($v = false){ if($v !== false){$this->mCampos["telefono"]["V"] =  $v; } return new MQLCampo($this->mCampos["telefono"]);}
	function fecha_de_registro($v = false){ if($v !== false){$this->mCampos["fecha_de_registro"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_registro"]);}
	function producto($v = false){ if($v !== false){$this->mCampos["producto"]["V"] =  $v; } return new MQLCampo($this->mCampos["producto"]);}
	function periocidad($v = false){ if($v !== false){$this->mCampos["periocidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["periocidad"]);}
	function pagos($v = false){ if($v !== false){$this->mCampos["pagos"]["V"] =  $v; } return new MQLCampo($this->mCampos["pagos"]);}
	function aplicacion($v = false){ if($v !== false){$this->mCampos["aplicacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["aplicacion"]);}
	function notas($v = false){ if($v !== false){$this->mCampos["notas"]["V"] =  $v; } return new MQLCampo($this->mCampos["notas"]);}
	function monto($v = false){ if($v !== false){$this->mCampos["monto"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto"]);}
	function email($v = false){ if($v !== false){$this->mCampos["email"]["V"] =  $v; } return new MQLCampo($this->mCampos["email"]);}
	function idpersona($v = false){ if($v !== false){$this->mCampos["idpersona"]["V"] =  $v; } return new MQLCampo($this->mCampos["idpersona"]);}
	function idcredito($v = false){ if($v !== false){$this->mCampos["idcredito"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcredito"]);}
	function idorigen($v = false){ if($v !== false){$this->mCampos["idorigen"]["V"] =  $v; } return new MQLCampo($this->mCampos["idorigen"]);}
	function idestado($v = false){ if($v !== false){$this->mCampos["idestado"]["V"] =  $v; } return new MQLCampo($this->mCampos["idestado"]);}
	function idoficial($v = false){ if($v !== false){$this->mCampos["idoficial"]["V"] =  $v; } return new MQLCampo($this->mCampos["idoficial"]);}
	function idexterno($v = false){ if($v !== false){$this->mCampos["idexterno"]["V"] =  $v; } return new MQLCampo($this->mCampos["idexterno"]);}
	function tipocuota_id($v = false){ if($v !== false){$this->mCampos["tipocuota_id"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipocuota_id"]);}
	function tasa_interes($v = false){ if($v !== false){$this->mCampos["tasa_interes"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_interes"]);}
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


/*	ORM: Tabla:	creditos_destino_detallado	-	Generado:	[28/2/2015 16:00]	*/
class cCreditos_destino_detallado {
	private $mCampos	= array(
		"idcreditos_destino_detallado" => array("N"=>"idcreditos_destino_detallado","T"=>"INT","V"=>"","L"=>11),
		"clave_de_presupuesto" => array("N"=>"clave_de_presupuesto","T"=>"BIGINT","V"=>"","L"=>25),
		"clave_de_persona" => array("N"=>"clave_de_persona","T"=>"BIGINT","V"=>"0","L"=>25),
		"clave_de_empresa" => array("N"=>"clave_de_empresa","T"=>"BIGINT","V"=>"0","L"=>25),
		"clave_de_destino" => array("N"=>"clave_de_destino","T"=>"INT","V"=>"0","L"=>11),
		"fecha_de_pago" => array("N"=>"fecha_de_pago","T"=>"DATE","V"=>"0000-00-00","L"=>0),
		"monto" => array("N"=>"monto","T"=>"FLOAT","V"=>"0.000","L"=>33),
		"observaciones" => array("N"=>"observaciones","T"=>"VARCHAR","V"=>"","L"=>100),
		"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"matriz","L"=>20),
		"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"1","L"=>11),
		"estado_actual" => array("N"=>"estado_actual","T"=>"INT","V"=>"0","L"=>2),
		"credito_vinculado" => array("N"=>"credito_vinculado","T"=>"BIGINT","V"=>"1","L"=>25),
		"cheque_de_pago" => array("N"=>"cheque_de_pago","T"=>"BIGINT","V"=>"0","L"=>12),
		"notas_del_pago" => array("N"=>"notas_del_pago","T"=>"VARCHAR","V"=>"","L"=>50),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_destino_detallado";}
	function getKey(){ return "idcreditos_destino_detallado";}
	function idcreditos_destino_detallado($v = false){ if($v !== false){$this->mCampos["idcreditos_destino_detallado"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_destino_detallado"]);}
	function clave_de_presupuesto($v = false){ if($v !== false){$this->mCampos["clave_de_presupuesto"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_presupuesto"]);}
	function clave_de_persona($v = false){ if($v !== false){$this->mCampos["clave_de_persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_persona"]);}
	function clave_de_empresa($v = false){ if($v !== false){$this->mCampos["clave_de_empresa"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_empresa"]);}
	function clave_de_destino($v = false){ if($v !== false){$this->mCampos["clave_de_destino"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_destino"]);}
	function fecha_de_pago($v = false){ if($v !== false){$this->mCampos["fecha_de_pago"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_pago"]);}
	function monto($v = false){ if($v !== false){$this->mCampos["monto"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto"]);}
	function observaciones($v = false){ if($v !== false){$this->mCampos["observaciones"]["V"] =  $v; } return new MQLCampo($this->mCampos["observaciones"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function estado_actual($v = false){ if($v !== false){$this->mCampos["estado_actual"]["V"] =  $v; } return new MQLCampo($this->mCampos["estado_actual"]);}
	function credito_vinculado($v = false){ if($v !== false){$this->mCampos["credito_vinculado"]["V"] =  $v; } return new MQLCampo($this->mCampos["credito_vinculado"]);}
	function cheque_de_pago($v = false){ if($v !== false){$this->mCampos["cheque_de_pago"]["V"] =  $v; } return new MQLCampo($this->mCampos["cheque_de_pago"]);}
	function notas_del_pago($v = false){ if($v !== false){$this->mCampos["notas_del_pago"]["V"] =  $v; } return new MQLCampo($this->mCampos["notas_del_pago"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	creditos_presupuestos	-	Generado:	[27/2/2015 06:00]	*/
class cCreditos_presupuestos {
	private $mCampos	= array(
		"clave_de_presupuesto" => array("N"=>"clave_de_presupuesto","T"=>"INT","V"=>"","L"=>11),
		"clave_de_persona" => array("N"=>"clave_de_persona","T"=>"BIGINT","V"=>"1","L"=>25),
		"fecha_de_elaboracion" => array("N"=>"fecha_de_elaboracion","T"=>"DATE","V"=>"","L"=>0),
		"total_presupuesto" => array("N"=>"total_presupuesto","T"=>"FLOAT","V"=>"0.00","L"=>25),
		"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"matriz","L"=>20),
		"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"1","L"=>11),
		"estado_actual" => array("N"=>"estado_actual","T"=>"INT","V"=>"0","L"=>2),
		"notas" => array("N"=>"notas","T"=>"VARCHAR","V"=>"","L"=>100),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_presupuestos";}
	function getKey(){ return "clave_de_presupuesto";}
	function clave_de_presupuesto($v = false){ if($v !== false){$this->mCampos["clave_de_presupuesto"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_presupuesto"]);}
	function clave_de_persona($v = false){ if($v !== false){$this->mCampos["clave_de_persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_persona"]);}
	function fecha_de_elaboracion($v = false){ if($v !== false){$this->mCampos["fecha_de_elaboracion"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_elaboracion"]);}
	function total_presupuesto($v = false){ if($v !== false){$this->mCampos["total_presupuesto"]["V"] =  $v; } return new MQLCampo($this->mCampos["total_presupuesto"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function estado_actual($v = false){ if($v !== false){$this->mCampos["estado_actual"]["V"] =  $v; } return new MQLCampo($this->mCampos["estado_actual"]);}
	function notas($v = false){ if($v !== false){$this->mCampos["notas"]["V"] =  $v; } return new MQLCampo($this->mCampos["notas"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}
/*	ORM: Tabla:	creditos_periodos	-	Generado:	[29/3/2016 18:23]	*/
class cCreditos_periodos {
	private $mCampos	= array(
			"idcreditos_periodos" => array("N"=>"idcreditos_periodos","T"=>"INT","V"=>"","L"=>10),
			"descripcion_periodos" => array("N"=>"descripcion_periodos","T"=>"VARCHAR","V"=>"","L"=>45),
			"fecha_inicial" => array("N"=>"fecha_inicial","T"=>"DATE","V"=>"0000-00-00","L"=>0),
			"fecha_final" => array("N"=>"fecha_final","T"=>"DATE","V"=>"0000-00-00","L"=>0),
			"fecha_reunion" => array("N"=>"fecha_reunion","T"=>"DATE","V"=>"0000-00-00","L"=>0),
			"periodo_responsable" => array("N"=>"periodo_responsable","T"=>"INT","V"=>"99","L"=>4),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_periodos";}
	function getKey(){ return "idcreditos_periodos";}
	function idcreditos_periodos($v = false){ if($v !== false){$this->mCampos["idcreditos_periodos"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_periodos"]);}
	function descripcion_periodos($v = false){ if($v !== false){$this->mCampos["descripcion_periodos"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_periodos"]);}
	function fecha_inicial($v = false){ if($v !== false){$this->mCampos["fecha_inicial"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_inicial"]);}
	function fecha_final($v = false){ if($v !== false){$this->mCampos["fecha_final"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_final"]);}
	function fecha_reunion($v = false){ if($v !== false){$this->mCampos["fecha_reunion"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_reunion"]);}
	function periodo_responsable($v = false){ if($v !== false){$this->mCampos["periodo_responsable"]["V"] =  $v; } return new MQLCampo($this->mCampos["periodo_responsable"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	creditos_eventos	-	Generado:	[28/4/2016 15:22]	*/
class cCreditos_eventos {
	private $mCampos	= array(
			"idcontrol" => array("N"=>"idcontrol","T"=>"INT","V"=>"","L"=>11),
			"personas" => array("N"=>"personas","T"=>"BIGINT","V"=>"","L"=>20),
			"credito" => array("N"=>"credito","T"=>"BIGINT","V"=>"","L"=>20),
			"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"","L"=>20),
			"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"","L"=>11),
			"fecha" => array("N"=>"fecha","T"=>"DATE","V"=>"","L"=>0),
			"tiempo" => array("N"=>"tiempo","T"=>"INT","V"=>"","L"=>11),
			"evento" => array("N"=>"evento","T"=>"VARCHAR","V"=>"","L"=>40),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_eventos";}
	function getKey(){ return "idcontrol";}
	function idcontrol($v = false){ if($v !== false){$this->mCampos["idcontrol"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcontrol"]);}
	function personas($v = false){ if($v !== false){$this->mCampos["personas"]["V"] =  $v; } return new MQLCampo($this->mCampos["personas"]);}
	function credito($v = false){ if($v !== false){$this->mCampos["credito"]["V"] =  $v; } return new MQLCampo($this->mCampos["credito"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function fecha($v = false){ if($v !== false){$this->mCampos["fecha"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha"]);}
	function tiempo($v = false){ if($v !== false){$this->mCampos["tiempo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tiempo"]);}
	function evento($v = false){ if($v !== false){$this->mCampos["evento"]["V"] =  $v; } return new MQLCampo($this->mCampos["evento"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}


/*	ORM: Tabla:	catalogo_creditos_productos_otros_parametros	-	Generado:	[03/4/2018 22:34]	*/
class cCatalogo_creditos_productos_otros_parametros {
	private $mCampos	= array("nombre_del_parametro" => array("N"=>"nombre_del_parametro","T"=>"VARCHAR","V"=>"","L"=>45),"descripcion_del_parametro" => array("N"=>"descripcion_del_parametro","T"=>"VARCHAR","V"=>"","L"=>45),"tipo_de_parametro" => array("N"=>"tipo_de_parametro","T"=>"VARCHAR","V"=>"","L"=>45),"codigo_de_evaluacion" => array("N"=>"codigo_de_evaluacion","T"=>"TEXT","V"=>"","L"=>0));
	public $NOMBRE_DEL_PARAMETRO = "nombre_del_parametro"; public $DESCRIPCION_DEL_PARAMETRO = "descripcion_del_parametro"; public $TIPO_DE_PARAMETRO = "tipo_de_parametro"; public $CODIGO_DE_EVALUACION = "codigo_de_evaluacion";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "catalogo_creditos_productos_otros_parametros";}
	function getKey(){ return "nombre_del_parametro";}
	function nombre_del_parametro($v = false){ if($v !== false){$this->mCampos["nombre_del_parametro"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_del_parametro"]);}
	function descripcion_del_parametro($v = false){ if($v !== false){$this->mCampos["descripcion_del_parametro"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_del_parametro"]);}
	function tipo_de_parametro($v = false){ if($v !== false){$this->mCampos["tipo_de_parametro"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_parametro"]);}
	function codigo_de_evaluacion($v = false){ if($v !== false){$this->mCampos["codigo_de_evaluacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["codigo_de_evaluacion"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}


//--- creditos_periocidadpagos 		Generado: 13-10-11 12
/*	ORM: Tabla:	creditos_periocidadpagos	-	Generado:	[21/11/2017 10:22]	*/
class cCreditos_periocidadpagos {
	private $mCampos	= array("idcreditos_periocidadpagos" => array("N"=>"idcreditos_periocidadpagos","T"=>"INT","V"=>"0","L"=>4),"descripcion_periocidadpagos" => array("N"=>"descripcion_periocidadpagos","T"=>"VARCHAR","V"=>"","L"=>45),"periocidad_de_pago" => array("N"=>"periocidad_de_pago","T"=>"INT","V"=>"0","L"=>4),"titulo_en_informe" => array("N"=>"titulo_en_informe","T"=>"ENUM","V"=>"|PAGO","L"=>0),"tolerancia_en_dias_para_vencimiento" => array("N"=>"tolerancia_en_dias_para_vencimiento","T"=>"INT","V"=>"89","L"=>4),"estatusactivo" => array("N"=>"estatusactivo","T"=>"INT","V"=>"1","L"=>2),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_periocidadpagos";}
	function getKey(){ return "idcreditos_periocidadpagos";}
	function idcreditos_periocidadpagos($v = false){ if($v !== false){$this->mCampos["idcreditos_periocidadpagos"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_periocidadpagos"]);}
	function descripcion_periocidadpagos($v = false){ if($v !== false){$this->mCampos["descripcion_periocidadpagos"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_periocidadpagos"]);}
	function periocidad_de_pago($v = false){ if($v !== false){$this->mCampos["periocidad_de_pago"]["V"] =  $v; } return new MQLCampo($this->mCampos["periocidad_de_pago"]);}
	function titulo_en_informe($v = false){ if($v !== false){$this->mCampos["titulo_en_informe"]["V"] =  $v; } return new MQLCampo($this->mCampos["titulo_en_informe"]);}
	function tolerancia_en_dias_para_vencimiento($v = false){ if($v !== false){$this->mCampos["tolerancia_en_dias_para_vencimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["tolerancia_en_dias_para_vencimiento"]);}
	function estatusactivo($v = false){ if($v !== false){$this->mCampos["estatusactivo"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatusactivo"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}

//--- creditos_modalidades 		Generado: 13-10-21 13
/*	ORM: Tabla:	creditos_modalidades	-	Generado:	[03/4/2018 22:52]	*/
class cCreditos_modalidades {
	private $mCampos	= array("idcreditos_modalidades" => array("N"=>"idcreditos_modalidades","T"=>"INT","V"=>"0","L"=>4),"descripcion_modalidades" => array("N"=>"descripcion_modalidades","T"=>"VARCHAR","V"=>"","L"=>65),"tipo_credito" => array("N"=>"tipo_credito","T"=>"VARCHAR","V"=>"0","L"=>4),"tasa_de_iva" => array("N"=>"tasa_de_iva","T"=>"FLOAT","V"=>"0.1600","L"=>25));
	public $IDCREDITOS_MODALIDADES = "idcreditos_modalidades"; public $DESCRIPCION_MODALIDADES = "descripcion_modalidades"; public $TIPO_CREDITO = "tipo_credito"; public $TASA_DE_IVA = "tasa_de_iva";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_modalidades";}
	function getKey(){ return "idcreditos_modalidades";}
	function idcreditos_modalidades($v = false){ if($v !== false){$this->mCampos["idcreditos_modalidades"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_modalidades"]);}
	function descripcion_modalidades($v = false){ if($v !== false){$this->mCampos["descripcion_modalidades"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_modalidades"]);}
	function tipo_credito($v = false){ if($v !== false){$this->mCampos["tipo_credito"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_credito"]);}
	function tasa_de_iva($v = false){ if($v !== false){$this->mCampos["tasa_de_iva"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_de_iva"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	creditos_rechazos_tipo	-	Generado:	[03/4/2018 23:10]	*/
class cCreditos_rechazos_tipo {
	private $mCampos	= array("idcreditos_rechazos_tipo" => array("N"=>"idcreditos_rechazos_tipo","T"=>"INT","V"=>"","L"=>11),"descripcion" => array("N"=>"descripcion","T"=>"VARCHAR","V"=>"","L"=>45));
	public $IDCREDITOS_RECHAZOS_TIPO = "idcreditos_rechazos_tipo"; public $DESCRIPCION = "descripcion";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_rechazos_tipo";}
	function getKey(){ return "idcreditos_rechazos_tipo";}
	function idcreditos_rechazos_tipo($v = false){ if($v !== false){$this->mCampos["idcreditos_rechazos_tipo"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_rechazos_tipo"]);}
	function descripcion($v = false){ if($v !== false){$this->mCampos["descripcion"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	creditos_destinos	-	Generado:	[08/2/2016 13:03]	*/
class cCreditos_destinos {
	private $mCampos	= array("idcreditos_destinos" => array("N"=>"idcreditos_destinos","T"=>"INT","V"=>"","L"=>4),"descripcion_destinos" => array("N"=>"descripcion_destinos","T"=>"VARCHAR","V"=>"","L"=>45),"destino_credito" => array("N"=>"destino_credito","T"=>"INT","V"=>"0","L"=>4),"capital_vencido_renovado" => array("N"=>"capital_vencido_renovado","T"=>"VARCHAR","V"=>"0","L"=>20),"capital_vencido_reestructurado" => array("N"=>"capital_vencido_reestructurado","T"=>"VARCHAR","V"=>"0","L"=>20),"capital_vencido_normal" => array("N"=>"capital_vencido_normal","T"=>"VARCHAR","V"=>"0","L"=>20),"capital_vigente_renovado" => array("N"=>"capital_vigente_renovado","T"=>"VARCHAR","V"=>"0","L"=>20),"capital_vigente_reestructurado" => array("N"=>"capital_vigente_reestructurado","T"=>"VARCHAR","V"=>"0","L"=>20),"capital_vigente_normal" => array("N"=>"capital_vigente_normal","T"=>"VARCHAR","V"=>"0","L"=>20),"interes_cobrado" => array("N"=>"interes_cobrado","T"=>"VARCHAR","V"=>"0","L"=>20),"moratorio_cobrado" => array("N"=>"moratorio_cobrado","T"=>"VARCHAR","V"=>"0","L"=>20),"interes_vencido_renovado" => array("N"=>"interes_vencido_renovado","T"=>"VARCHAR","V"=>"0","L"=>20),"interes_vencido_reestructurado" => array("N"=>"interes_vencido_reestructurado","T"=>"VARCHAR","V"=>"0","L"=>20),"interes_vencido_normal" => array("N"=>"interes_vencido_normal","T"=>"VARCHAR","V"=>"0","L"=>20),"interes_vigente_renovado" => array("N"=>"interes_vigente_renovado","T"=>"VARCHAR","V"=>"0","L"=>20),"interes_vigente_reestructurado" => array("N"=>"interes_vigente_reestructurado","T"=>"VARCHAR","V"=>"0","L"=>20),"interes_vigente_normal" => array("N"=>"interes_vigente_normal","T"=>"VARCHAR","V"=>"0","L"=>20),"tasa_de_iva" => array("N"=>"tasa_de_iva","T"=>"FLOAT","V"=>"0.1600","L"=>25),"estatusactivo" => array("N"=>"estatusactivo","T"=>"INT","V"=>"1","L"=>2));
	public $IDCREDITOS_DESTINOS = "idcreditos_destinos"; public $DESCRIPCION_DESTINOS = "descripcion_destinos"; public $DESTINO_CREDITO = "destino_credito"; public $CAPITAL_VENCIDO_RENOVADO = "capital_vencido_renovado"; public $CAPITAL_VENCIDO_REESTRUCTURADO = "capital_vencido_reestructurado"; public $CAPITAL_VENCIDO_NORMAL = "capital_vencido_normal"; public $CAPITAL_VIGENTE_RENOVADO = "capital_vigente_renovado"; public $CAPITAL_VIGENTE_REESTRUCTURADO = "capital_vigente_reestructurado"; public $CAPITAL_VIGENTE_NORMAL = "capital_vigente_normal"; public $INTERES_COBRADO = "interes_cobrado"; public $MORATORIO_COBRADO = "moratorio_cobrado"; public $INTERES_VENCIDO_RENOVADO = "interes_vencido_renovado"; public $INTERES_VENCIDO_REESTRUCTURADO = "interes_vencido_reestructurado"; public $INTERES_VENCIDO_NORMAL = "interes_vencido_normal"; public $INTERES_VIGENTE_RENOVADO = "interes_vigente_renovado"; public $INTERES_VIGENTE_REESTRUCTURADO = "interes_vigente_reestructurado"; public $INTERES_VIGENTE_NORMAL = "interes_vigente_normal"; public $TASA_DE_IVA = "tasa_de_iva"; public $ESTATUSACTIVO = "estatusactivo";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_destinos";}
	function getKey(){ return "idcreditos_destinos";}
	function idcreditos_destinos($v = false){ if($v !== false){$this->mCampos["idcreditos_destinos"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_destinos"]);}
	function descripcion_destinos($v = false){ if($v !== false){$this->mCampos["descripcion_destinos"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_destinos"]);}
	function destino_credito($v = false){ if($v !== false){$this->mCampos["destino_credito"]["V"] =  $v; } return new MQLCampo($this->mCampos["destino_credito"]);}
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
	function tasa_de_iva($v = false){ if($v !== false){$this->mCampos["tasa_de_iva"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_de_iva"]);}
	function estatusactivo($v = false){ if($v !== false){$this->mCampos["estatusactivo"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatusactivo"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	creditos_tipo_de_dispersion	-	Generado:	[03/4/2018 23:20]	*/
class cCreditos_tipo_de_dispersion {
	private $mCampos	= array("idcreditos_tipo_de_dispersion" => array("N"=>"idcreditos_tipo_de_dispersion","T"=>"INT","V"=>"","L"=>11),"descripcion" => array("N"=>"descripcion","T"=>"VARCHAR","V"=>"","L"=>100),"equivalente_en_tesoreria" => array("N"=>"equivalente_en_tesoreria","T"=>"VARCHAR","V"=>"ninguno","L"=>40));
	public $IDCREDITOS_TIPO_DE_DISPERSION = "idcreditos_tipo_de_dispersion"; public $DESCRIPCION = "descripcion"; public $EQUIVALENTE_EN_TESORERIA = "equivalente_en_tesoreria";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_tipo_de_dispersion";}
	function getKey(){ return "idcreditos_tipo_de_dispersion";}
	function idcreditos_tipo_de_dispersion($v = false){ if($v !== false){$this->mCampos["idcreditos_tipo_de_dispersion"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_tipo_de_dispersion"]);}
	function descripcion($v = false){ if($v !== false){$this->mCampos["descripcion"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion"]);}
	function equivalente_en_tesoreria($v = false){ if($v !== false){$this->mCampos["equivalente_en_tesoreria"]["V"] =  $v; } return new MQLCampo($this->mCampos["equivalente_en_tesoreria"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
}


/*	ORM: Tabla:	creditos_tipoconvenio	-	Generado:	[18/11/2014 17:52]	*/
/*	ORM: Tabla:	creditos_tipoconvenio	-	Generado:	[04/8/2015 13:01]	*/
/*	ORM: Tabla:	creditos_tipoconvenio	-	Generado:	[18/5/2018 16:30]	*/
class cCreditos_tipoconvenio {
	private $mCampos	= array("idcreditos_tipoconvenio" => array("N"=>"idcreditos_tipoconvenio","T"=>"INT","V"=>"0","L"=>4),"descripcion_tipoconvenio" => array("N"=>"descripcion_tipoconvenio","T"=>"VARCHAR","V"=>"","L"=>100),"tasa_ahorro" => array("N"=>"tasa_ahorro","T"=>"FLOAT","V"=>"0.00000","L"=>17),"tipo_convenio" => array("N"=>"tipo_convenio","T"=>"INT","V"=>"0","L"=>4),"razon_garantia" => array("N"=>"razon_garantia","T"=>"FLOAT","V"=>"0.00000","L"=>17),"creditos_mayores_a" => array("N"=>"creditos_mayores_a","T"=>"DOUBLE","V"=>"0.00","L"=>33),"porciento_garantia_liquida" => array("N"=>"porciento_garantia_liquida","T"=>"FLOAT","V"=>"0.00000","L"=>17),"monto_fondo_obligatorio" => array("N"=>"monto_fondo_obligatorio","T"=>"FLOAT","V"=>"0.0000","L"=>25),"porcentaje_otro_credito" => array("N"=>"porcentaje_otro_credito","T"=>"FLOAT","V"=>"0.00000","L"=>17),"aplica_gastos_notariales" => array("N"=>"aplica_gastos_notariales","T"=>"INT","V"=>"0","L"=>2),"numero_creditos_maximo" => array("N"=>"numero_creditos_maximo","T"=>"INT","V"=>"1","L"=>2),"dias_maximo" => array("N"=>"dias_maximo","T"=>"INT","V"=>"0","L"=>5),"pagos_maximo" => array("N"=>"pagos_maximo","T"=>"INT","V"=>"0","L"=>5),"tipo_autorizacion" => array("N"=>"tipo_autorizacion","T"=>"INT","V"=>"1","L"=>4),"nivel_riesgo" => array("N"=>"nivel_riesgo","T"=>"INT","V"=>"1","L"=>4),"porcentaje_ica" => array("N"=>"porcentaje_ica","T"=>"FLOAT","V"=>"0.00000","L"=>17),"estatus_predeterminado" => array("N"=>"estatus_predeterminado","T"=>"INT","V"=>"98","L"=>4),"leyenda_docto_autorizacion" => array("N"=>"leyenda_docto_autorizacion","T"=>"VARCHAR","V"=>"","L"=>50),"interes_normal" => array("N"=>"interes_normal","T"=>"FLOAT","V"=>"0.00000","L"=>17),"interes_moratorio" => array("N"=>"interes_moratorio","T"=>"FLOAT","V"=>"0.00000","L"=>17),"tolerancia_dias_no_pago" => array("N"=>"tolerancia_dias_no_pago","T"=>"INT","V"=>"5","L"=>4),"maximo_otorgable" => array("N"=>"maximo_otorgable","T"=>"DOUBLE","V"=>"0.00","L"=>33),"tolerancia_dias_primer_abono" => array("N"=>"tolerancia_dias_primer_abono","T"=>"INT","V"=>"0","L"=>4),"numero_avales" => array("N"=>"numero_avales","T"=>"INT","V"=>"2","L"=>4),"nivel_autorizacion_oficial" => array("N"=>"nivel_autorizacion_oficial","T"=>"INT","V"=>"6","L"=>4),"code_valoracion_javascript" => array("N"=>"code_valoracion_javascript","T"=>"MEDIUMTEXT","V"=>"","L"=>0),"minimo_otorgable" => array("N"=>"minimo_otorgable","T"=>"FLOAT","V"=>"1000.00","L"=>25),"descripcion_completa" => array("N"=>"descripcion_completa","T"=>"MEDIUMTEXT","V"=>"","L"=>0),"oficial_seguimiento" => array("N"=>"oficial_seguimiento","T"=>"INT","V"=>"99","L"=>4),"valoracion_php" => array("N"=>"valoracion_php","T"=>"MEDIUMTEXT","V"=>"","L"=>0),"tipo_de_credito" => array("N"=>"tipo_de_credito","T"=>"INT","V"=>"1","L"=>4),"php_monto_maximo" => array("N"=>"php_monto_maximo","T"=>"MEDIUMTEXT","V"=>"","L"=>0),"tipo_de_convenio" => array("N"=>"tipo_de_convenio","T"=>"ENUM","V"=>"|1|3|","L"=>0),"tipo_de_garantia" => array("N"=>"tipo_de_garantia","T"=>"ENUM","V"=>"|todas|cuenta_inversion|aportacion|","L"=>0),"estatus" => array("N"=>"estatus","T"=>"ENUM","V"=>"|baja|activo|","L"=>0),"tasa_iva" => array("N"=>"tasa_iva","T"=>"FLOAT","V"=>"0.00000","L"=>17),"contable_cartera_vigente" => array("N"=>"contable_cartera_vigente","T"=>"VARCHAR","V"=>"0","L"=>20),"contable_cartera_vencida" => array("N"=>"contable_cartera_vencida","T"=>"VARCHAR","V"=>"0","L"=>20),"contable_intereses_devengados" => array("N"=>"contable_intereses_devengados","T"=>"VARCHAR","V"=>"0","L"=>20),"contable_intereses_anticipados" => array("N"=>"contable_intereses_anticipados","T"=>"VARCHAR","V"=>"0","L"=>20),"contable_intereses_cobrados" => array("N"=>"contable_intereses_cobrados","T"=>"VARCHAR","V"=>"0","L"=>20),"contable_intereses_moratorios" => array("N"=>"contable_intereses_moratorios","T"=>"VARCHAR","V"=>"0","L"=>20),"iva_incluido" => array("N"=>"iva_incluido","T"=>"ENUM","V"=>"|1|0|","L"=>0),"comision_por_apertura" => array("N"=>"comision_por_apertura","T"=>"FLOAT","V"=>"0.00000","L"=>17),"codigo_de_contrato" => array("N"=>"codigo_de_contrato","T"=>"INT","V"=>"0","L"=>4),"contable_cartera_castigada" => array("N"=>"contable_cartera_castigada","T"=>"VARCHAR","V"=>"0","L"=>20),"path_del_contrato" => array("N"=>"path_del_contrato","T"=>"VARCHAR","V"=>"","L"=>100),"tipo_de_integracion" => array("N"=>"tipo_de_integracion","T"=>"TINYINT","V"=>"1","L"=>2),"contable_intereses_vencidos" => array("N"=>"contable_intereses_vencidos","T"=>"VARCHAR","V"=>"0","L"=>20),"base_de_calculo_de_interes" => array("N"=>"base_de_calculo_de_interes","T"=>"INT","V"=>"2","L"=>2),"capital_vencido_renovado" => array("N"=>"capital_vencido_renovado","T"=>"VARCHAR","V"=>"0","L"=>20),"capital_vencido_reestructurado" => array("N"=>"capital_vencido_reestructurado","T"=>"VARCHAR","V"=>"0","L"=>20),"capital_vencido_normal" => array("N"=>"capital_vencido_normal","T"=>"VARCHAR","V"=>"0","L"=>20),"capital_vigente_renovado" => array("N"=>"capital_vigente_renovado","T"=>"VARCHAR","V"=>"0","L"=>20),"capital_vigente_reestructurado" => array("N"=>"capital_vigente_reestructurado","T"=>"VARCHAR","V"=>"0","L"=>20),"capital_vigente_normal" => array("N"=>"capital_vigente_normal","T"=>"VARCHAR","V"=>"0","L"=>20),"interes_cobrado" => array("N"=>"interes_cobrado","T"=>"VARCHAR","V"=>"0","L"=>20),"moratorio_cobrado" => array("N"=>"moratorio_cobrado","T"=>"VARCHAR","V"=>"0","L"=>20),"interes_vencido_renovado" => array("N"=>"interes_vencido_renovado","T"=>"VARCHAR","V"=>"0","L"=>20),"interes_vencido_reestructurado" => array("N"=>"interes_vencido_reestructurado","T"=>"VARCHAR","V"=>"0","L"=>20),"interes_vencido_normal" => array("N"=>"interes_vencido_normal","T"=>"VARCHAR","V"=>"0","L"=>20),"interes_vigente_renovado" => array("N"=>"interes_vigente_renovado","T"=>"VARCHAR","V"=>"0","L"=>20),"interes_vigente_reestructurado" => array("N"=>"interes_vigente_reestructurado","T"=>"VARCHAR","V"=>"0","L"=>20),"interes_vigente_normal" => array("N"=>"interes_vigente_normal","T"=>"VARCHAR","V"=>"0","L"=>20),"tipo_de_interes" => array("N"=>"tipo_de_interes","T"=>"INT","V"=>"0","L"=>11),"aplica_mora_por_cobranza" => array("N"=>"aplica_mora_por_cobranza","T"=>"INT","V"=>"0","L"=>4),"pre_modificador_de_interes" => array("N"=>"pre_modificador_de_interes","T"=>"MEDIUMTEXT","V"=>"","L"=>0),"pos_modificador_de_interes" => array("N"=>"pos_modificador_de_interes","T"=>"MEDIUMTEXT","V"=>"","L"=>0),"pre_modificador_de_ministracion" => array("N"=>"pre_modificador_de_ministracion","T"=>"MEDIUMTEXT","V"=>"","L"=>0),"pre_modificador_de_autorizacion" => array("N"=>"pre_modificador_de_autorizacion","T"=>"MEDIUMTEXT","V"=>"","L"=>0),"pre_modificador_de_vencimiento" => array("N"=>"pre_modificador_de_vencimiento","T"=>"MEDIUMTEXT","V"=>"","L"=>0),"pre_modificador_de_solicitud" => array("N"=>"pre_modificador_de_solicitud","T"=>"MEDIUMTEXT","V"=>"","L"=>0),"clave_de_tipo_de_producto" => array("N"=>"clave_de_tipo_de_producto","T"=>"VARCHAR","V"=>"UK","L"=>10),"perfil_de_interes" => array("N"=>"perfil_de_interes","T"=>"INT","V"=>"99","L"=>11),"fuente_de_fondeo_predeterminado" => array("N"=>"fuente_de_fondeo_predeterminado","T"=>"INT","V"=>"1","L"=>11),"tipo_de_periocidad_preferente" => array("N"=>"tipo_de_periocidad_preferente","T"=>"INT","V"=>"7","L"=>4),"numero_de_pagos_preferente" => array("N"=>"numero_de_pagos_preferente","T"=>"INT","V"=>"0","L"=>11),"tipo_en_sistema" => array("N"=>"tipo_en_sistema","T"=>"INT","V"=>"1","L"=>4),"omitir_seguimiento" => array("N"=>"omitir_seguimiento","T"=>"INT","V"=>"0","L"=>4),"nombre_corto" => array("N"=>"nombre_corto","T"=>"VARCHAR","V"=>"","L"=>20));
	public $IDCREDITOS_TIPOCONVENIO = "idcreditos_tipoconvenio"; public $DESCRIPCION_TIPOCONVENIO = "descripcion_tipoconvenio"; public $TASA_AHORRO = "tasa_ahorro"; public $TIPO_CONVENIO = "tipo_convenio"; public $RAZON_GARANTIA = "razon_garantia"; public $CREDITOS_MAYORES_A = "creditos_mayores_a"; public $PORCIENTO_GARANTIA_LIQUIDA = "porciento_garantia_liquida"; public $MONTO_FONDO_OBLIGATORIO = "monto_fondo_obligatorio"; public $PORCENTAJE_OTRO_CREDITO = "porcentaje_otro_credito"; public $APLICA_GASTOS_NOTARIALES = "aplica_gastos_notariales"; public $NUMERO_CREDITOS_MAXIMO = "numero_creditos_maximo"; public $DIAS_MAXIMO = "dias_maximo"; public $PAGOS_MAXIMO = "pagos_maximo"; public $TIPO_AUTORIZACION = "tipo_autorizacion"; public $NIVEL_RIESGO = "nivel_riesgo"; public $PORCENTAJE_ICA = "porcentaje_ica"; public $ESTATUS_PREDETERMINADO = "estatus_predeterminado"; public $LEYENDA_DOCTO_AUTORIZACION = "leyenda_docto_autorizacion"; public $INTERES_NORMAL = "interes_normal"; public $INTERES_MORATORIO = "interes_moratorio"; public $TOLERANCIA_DIAS_NO_PAGO = "tolerancia_dias_no_pago"; public $MAXIMO_OTORGABLE = "maximo_otorgable"; public $TOLERANCIA_DIAS_PRIMER_ABONO = "tolerancia_dias_primer_abono"; public $NUMERO_AVALES = "numero_avales"; public $NIVEL_AUTORIZACION_OFICIAL = "nivel_autorizacion_oficial"; public $CODE_VALORACION_JAVASCRIPT = "code_valoracion_javascript"; public $MINIMO_OTORGABLE = "minimo_otorgable"; public $DESCRIPCION_COMPLETA = "descripcion_completa"; public $OFICIAL_SEGUIMIENTO = "oficial_seguimiento"; public $VALORACION_PHP = "valoracion_php"; public $TIPO_DE_CREDITO = "tipo_de_credito"; public $PHP_MONTO_MAXIMO = "php_monto_maximo"; public $TIPO_DE_CONVENIO = "tipo_de_convenio"; public $TIPO_DE_GARANTIA = "tipo_de_garantia"; public $ESTATUS = "estatus"; public $TASA_IVA = "tasa_iva"; public $CONTABLE_CARTERA_VIGENTE = "contable_cartera_vigente"; public $CONTABLE_CARTERA_VENCIDA = "contable_cartera_vencida"; public $CONTABLE_INTERESES_DEVENGADOS = "contable_intereses_devengados"; public $CONTABLE_INTERESES_ANTICIPADOS = "contable_intereses_anticipados"; public $CONTABLE_INTERESES_COBRADOS = "contable_intereses_cobrados"; public $CONTABLE_INTERESES_MORATORIOS = "contable_intereses_moratorios"; public $IVA_INCLUIDO = "iva_incluido"; public $COMISION_POR_APERTURA = "comision_por_apertura"; public $CODIGO_DE_CONTRATO = "codigo_de_contrato"; public $CONTABLE_CARTERA_CASTIGADA = "contable_cartera_castigada"; public $PATH_DEL_CONTRATO = "path_del_contrato"; public $TIPO_DE_INTEGRACION = "tipo_de_integracion"; public $CONTABLE_INTERESES_VENCIDOS = "contable_intereses_vencidos"; public $BASE_DE_CALCULO_DE_INTERES = "base_de_calculo_de_interes"; public $CAPITAL_VENCIDO_RENOVADO = "capital_vencido_renovado"; public $CAPITAL_VENCIDO_REESTRUCTURADO = "capital_vencido_reestructurado"; public $CAPITAL_VENCIDO_NORMAL = "capital_vencido_normal"; public $CAPITAL_VIGENTE_RENOVADO = "capital_vigente_renovado"; public $CAPITAL_VIGENTE_REESTRUCTURADO = "capital_vigente_reestructurado"; public $CAPITAL_VIGENTE_NORMAL = "capital_vigente_normal"; public $INTERES_COBRADO = "interes_cobrado"; public $MORATORIO_COBRADO = "moratorio_cobrado"; public $INTERES_VENCIDO_RENOVADO = "interes_vencido_renovado"; public $INTERES_VENCIDO_REESTRUCTURADO = "interes_vencido_reestructurado"; public $INTERES_VENCIDO_NORMAL = "interes_vencido_normal"; public $INTERES_VIGENTE_RENOVADO = "interes_vigente_renovado"; public $INTERES_VIGENTE_REESTRUCTURADO = "interes_vigente_reestructurado"; public $INTERES_VIGENTE_NORMAL = "interes_vigente_normal"; public $TIPO_DE_INTERES = "tipo_de_interes"; public $APLICA_MORA_POR_COBRANZA = "aplica_mora_por_cobranza"; public $PRE_MODIFICADOR_DE_INTERES = "pre_modificador_de_interes"; public $POS_MODIFICADOR_DE_INTERES = "pos_modificador_de_interes"; public $PRE_MODIFICADOR_DE_MINISTRACION = "pre_modificador_de_ministracion"; public $PRE_MODIFICADOR_DE_AUTORIZACION = "pre_modificador_de_autorizacion"; public $PRE_MODIFICADOR_DE_VENCIMIENTO = "pre_modificador_de_vencimiento"; public $PRE_MODIFICADOR_DE_SOLICITUD = "pre_modificador_de_solicitud"; public $CLAVE_DE_TIPO_DE_PRODUCTO = "clave_de_tipo_de_producto"; public $PERFIL_DE_INTERES = "perfil_de_interes"; public $FUENTE_DE_FONDEO_PREDETERMINADO = "fuente_de_fondeo_predeterminado"; public $TIPO_DE_PERIOCIDAD_PREFERENTE = "tipo_de_periocidad_preferente"; public $NUMERO_DE_PAGOS_PREFERENTE = "numero_de_pagos_preferente"; public $TIPO_EN_SISTEMA = "tipo_en_sistema"; public $OMITIR_SEGUIMIENTO = "omitir_seguimiento"; public $NOMBRE_CORTO = "nombre_corto";
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
	function omitir_seguimiento($v = false){ if($v !== false){$this->mCampos["omitir_seguimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["omitir_seguimiento"]);}
	function nombre_corto($v = false){ if($v !== false){$this->mCampos["nombre_corto"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_corto"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
}

/*	ORM: Tabla:	creditos_montos	-	Generado:	[28/6/2016 12:31]	*/
/*	ORM: Tabla:	creditos_montos	-	Generado:	[12/9/2017 16:02]	*/
/*	ORM: Tabla:	creditos_montos	-	Generado:	[24/5/2018 17:19]	*/
class cCreditos_montos {
	private $mCampos	= array("idcreditos_montos" => array("N"=>"idcreditos_montos","T"=>"INT","V"=>"","L"=>11),"clave_de_credito" => array("N"=>"clave_de_credito","T"=>"BIGINT","V"=>"0","L"=>20),"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"matriz","L"=>20),"marca_tiempo" => array("N"=>"marca_tiempo","T"=>"INT","V"=>"0","L"=>10),"marca_acceso" => array("N"=>"marca_acceso","T"=>"INT","V"=>"0","L"=>10),"interes_n_dev" => array("N"=>"interes_n_dev","T"=>"DOUBLE","V"=>"0.00","L"=>25),"interes_n_pag" => array("N"=>"interes_n_pag","T"=>"DOUBLE","V"=>"0.00","L"=>25),"interes_m_dev" => array("N"=>"interes_m_dev","T"=>"DOUBLE","V"=>"0.00","L"=>25),"interes_m_pag" => array("N"=>"interes_m_pag","T"=>"DOUBLE","V"=>"0.00","L"=>25),"interes_n_corr" => array("N"=>"interes_n_corr","T"=>"DOUBLE","V"=>"0.00","L"=>25),"interes_m_corr" => array("N"=>"interes_m_corr","T"=>"DOUBLE","V"=>"0.00","L"=>25),"cargos_cbza" => array("N"=>"cargos_cbza","T"=>"DOUBLE","V"=>"0.00","L"=>25),"imptos_int_n" => array("N"=>"imptos_int_n","T"=>"DOUBLE","V"=>"0.00","L"=>25),"imptos_int_m" => array("N"=>"imptos_int_m","T"=>"DOUBLE","V"=>"0.00","L"=>25),"imptos_otros" => array("N"=>"imptos_otros","T"=>"DOUBLE","V"=>"0.00","L"=>25),"penas" => array("N"=>"penas","T"=>"DOUBLE","V"=>"0.00","L"=>25),"bonificaciones" => array("N"=>"bonificaciones","T"=>"DOUBLE","V"=>"0.00","L"=>25),"capital_exigible" => array("N"=>"capital_exigible","T"=>"DOUBLE","V"=>"0.00","L"=>25),"f_primer_atraso" => array("N"=>"f_primer_atraso","T"=>"DATE","V"=>"0000-00-00","L"=>0),"f_ultimo_atraso" => array("N"=>"f_ultimo_atraso","T"=>"DATE","V"=>"0000-00-00","L"=>0),"otros1_id" => array("N"=>"otros1_id","T"=>"INT","V"=>"0","L"=>5),"otros1_m" => array("N"=>"otros1_m","T"=>"DOUBLE","V"=>"0.00","L"=>25),"otros2_id" => array("N"=>"otros2_id","T"=>"INT","V"=>"0","L"=>5),"otros2_m" => array("N"=>"otros2_m","T"=>"DOUBLE","V"=>"0.00","L"=>25),"otros_nc" => array("N"=>"otros_nc","T"=>"DOUBLE","V"=>"0.00","L"=>25),"usuario" => array("N"=>"usuario","T"=>"INT","V"=>"0","L"=>6),"guardar" => array("N"=>"guardar","T"=>"INT","V"=>"0","L"=>2),"t_iva_int_n" => array("N"=>"t_iva_int_n","T"=>"FLOAT","V"=>"0.000","L"=>17),"t_iva_m" => array("N"=>"t_iva_m","T"=>"FLOAT","V"=>"0.000","L"=>17),"t_iva_o" => array("N"=>"t_iva_o","T"=>"FLOAT","V"=>"0.000","L"=>17),"otros_si" => array("N"=>"otros_si","T"=>"DOUBLE","V"=>"0.00","L"=>25),"dispocision" => array("N"=>"dispocision","T"=>"DOUBLE","V"=>"0.00","L"=>33),"saldo_plan" => array("N"=>"saldo_plan","T"=>"DOUBLE","V"=>"0.00","L"=>33),"abonos_ops" => array("N"=>"abonos_ops","T"=>"DOUBLE","V"=>"0.00","L"=>33),"periodo_min" => array("N"=>"periodo_min","T"=>"INT","V"=>"0","L"=>5),"periodo_max" => array("N"=>"periodo_max","T"=>"INT","V"=>"0","L"=>5),"periodo_last" => array("N"=>"periodo_last","T"=>"INT","V"=>"0","L"=>5),"bon_int" => array("N"=>"bon_int","T"=>"DOUBLE","V"=>"0.00","L"=>25),"bon_mora" => array("N"=>"bon_mora","T"=>"DOUBLE","V"=>"0.00","L"=>25),"bon_otros" => array("N"=>"bon_otros","T"=>"DOUBLE","V"=>"0.00","L"=>25),"sdo_exig_fut" => array("N"=>"sdo_exig_fut","T"=>"DOUBLE","V"=>"0.00","L"=>33),"sdo_exig_act" => array("N"=>"sdo_exig_act","T"=>"DOUBLE","V"=>"0.00","L"=>33),"ints_tot_calc" => array("N"=>"ints_tot_calc","T"=>"DOUBLE","V"=>"0.00","L"=>33));
	public $IDCREDITOS_MONTOS = "idcreditos_montos"; public $CLAVE_DE_CREDITO = "clave_de_credito"; public $SUCURSAL = "sucursal"; public $MARCA_TIEMPO = "marca_tiempo"; public $MARCA_ACCESO = "marca_acceso"; public $INTERES_N_DEV = "interes_n_dev"; public $INTERES_N_PAG = "interes_n_pag"; public $INTERES_M_DEV = "interes_m_dev"; public $INTERES_M_PAG = "interes_m_pag"; public $INTERES_N_CORR = "interes_n_corr"; public $INTERES_M_CORR = "interes_m_corr"; public $CARGOS_CBZA = "cargos_cbza"; public $IMPTOS_INT_N = "imptos_int_n"; public $IMPTOS_INT_M = "imptos_int_m"; public $IMPTOS_OTROS = "imptos_otros"; public $PENAS = "penas"; public $BONIFICACIONES = "bonificaciones"; public $CAPITAL_EXIGIBLE = "capital_exigible"; public $F_PRIMER_ATRASO = "f_primer_atraso"; public $F_ULTIMO_ATRASO = "f_ultimo_atraso"; public $OTROS1_ID = "otros1_id"; public $OTROS1_M = "otros1_m"; public $OTROS2_ID = "otros2_id"; public $OTROS2_M = "otros2_m"; public $OTROS_NC = "otros_nc"; public $USUARIO = "usuario"; public $GUARDAR = "guardar"; public $T_IVA_INT_N = "t_iva_int_n"; public $T_IVA_M = "t_iva_m"; public $T_IVA_O = "t_iva_o"; public $OTROS_SI = "otros_si"; public $DISPOCISION = "dispocision"; public $SALDO_PLAN = "saldo_plan"; public $ABONOS_OPS = "abonos_ops"; public $PERIODO_MIN = "periodo_min"; public $PERIODO_MAX = "periodo_max"; public $PERIODO_LAST = "periodo_last"; public $BON_INT = "bon_int"; public $BON_MORA = "bon_mora"; public $BON_OTROS = "bon_otros"; public $SDO_EXIG_FUT = "sdo_exig_fut"; public $SDO_EXIG_ACT = "sdo_exig_act"; public $INTS_TOT_CALC = "ints_tot_calc";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_montos";}
	function getKey(){ return "idcreditos_montos";}
	function idcreditos_montos($v = false){ if($v !== false){$this->mCampos["idcreditos_montos"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_montos"]);}
	function clave_de_credito($v = false){ if($v !== false){$this->mCampos["clave_de_credito"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_credito"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function marca_tiempo($v = false){ if($v !== false){$this->mCampos["marca_tiempo"]["V"] =  $v; } return new MQLCampo($this->mCampos["marca_tiempo"]);}
	function marca_acceso($v = false){ if($v !== false){$this->mCampos["marca_acceso"]["V"] =  $v; } return new MQLCampo($this->mCampos["marca_acceso"]);}
	function interes_n_dev($v = false){ if($v !== false){$this->mCampos["interes_n_dev"]["V"] =  $v; } return new MQLCampo($this->mCampos["interes_n_dev"]);}
	function interes_n_pag($v = false){ if($v !== false){$this->mCampos["interes_n_pag"]["V"] =  $v; } return new MQLCampo($this->mCampos["interes_n_pag"]);}
	function interes_m_dev($v = false){ if($v !== false){$this->mCampos["interes_m_dev"]["V"] =  $v; } return new MQLCampo($this->mCampos["interes_m_dev"]);}
	function interes_m_pag($v = false){ if($v !== false){$this->mCampos["interes_m_pag"]["V"] =  $v; } return new MQLCampo($this->mCampos["interes_m_pag"]);}
	function interes_n_corr($v = false){ if($v !== false){$this->mCampos["interes_n_corr"]["V"] =  $v; } return new MQLCampo($this->mCampos["interes_n_corr"]);}
	function interes_m_corr($v = false){ if($v !== false){$this->mCampos["interes_m_corr"]["V"] =  $v; } return new MQLCampo($this->mCampos["interes_m_corr"]);}
	function cargos_cbza($v = false){ if($v !== false){$this->mCampos["cargos_cbza"]["V"] =  $v; } return new MQLCampo($this->mCampos["cargos_cbza"]);}
	function imptos_int_n($v = false){ if($v !== false){$this->mCampos["imptos_int_n"]["V"] =  $v; } return new MQLCampo($this->mCampos["imptos_int_n"]);}
	function imptos_int_m($v = false){ if($v !== false){$this->mCampos["imptos_int_m"]["V"] =  $v; } return new MQLCampo($this->mCampos["imptos_int_m"]);}
	function imptos_otros($v = false){ if($v !== false){$this->mCampos["imptos_otros"]["V"] =  $v; } return new MQLCampo($this->mCampos["imptos_otros"]);}
	function penas($v = false){ if($v !== false){$this->mCampos["penas"]["V"] =  $v; } return new MQLCampo($this->mCampos["penas"]);}
	function bonificaciones($v = false){ if($v !== false){$this->mCampos["bonificaciones"]["V"] =  $v; } return new MQLCampo($this->mCampos["bonificaciones"]);}
	function capital_exigible($v = false){ if($v !== false){$this->mCampos["capital_exigible"]["V"] =  $v; } return new MQLCampo($this->mCampos["capital_exigible"]);}
	function f_primer_atraso($v = false){ if($v !== false){$this->mCampos["f_primer_atraso"]["V"] =  $v; } return new MQLCampo($this->mCampos["f_primer_atraso"]);}
	function f_ultimo_atraso($v = false){ if($v !== false){$this->mCampos["f_ultimo_atraso"]["V"] =  $v; } return new MQLCampo($this->mCampos["f_ultimo_atraso"]);}
	function otros1_id($v = false){ if($v !== false){$this->mCampos["otros1_id"]["V"] =  $v; } return new MQLCampo($this->mCampos["otros1_id"]);}
	function otros1_m($v = false){ if($v !== false){$this->mCampos["otros1_m"]["V"] =  $v; } return new MQLCampo($this->mCampos["otros1_m"]);}
	function otros2_id($v = false){ if($v !== false){$this->mCampos["otros2_id"]["V"] =  $v; } return new MQLCampo($this->mCampos["otros2_id"]);}
	function otros2_m($v = false){ if($v !== false){$this->mCampos["otros2_m"]["V"] =  $v; } return new MQLCampo($this->mCampos["otros2_m"]);}
	function otros_nc($v = false){ if($v !== false){$this->mCampos["otros_nc"]["V"] =  $v; } return new MQLCampo($this->mCampos["otros_nc"]);}
	function usuario($v = false){ if($v !== false){$this->mCampos["usuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["usuario"]);}
	function guardar($v = false){ if($v !== false){$this->mCampos["guardar"]["V"] =  $v; } return new MQLCampo($this->mCampos["guardar"]);}
	function t_iva_int_n($v = false){ if($v !== false){$this->mCampos["t_iva_int_n"]["V"] =  $v; } return new MQLCampo($this->mCampos["t_iva_int_n"]);}
	function t_iva_m($v = false){ if($v !== false){$this->mCampos["t_iva_m"]["V"] =  $v; } return new MQLCampo($this->mCampos["t_iva_m"]);}
	function t_iva_o($v = false){ if($v !== false){$this->mCampos["t_iva_o"]["V"] =  $v; } return new MQLCampo($this->mCampos["t_iva_o"]);}
	function otros_si($v = false){ if($v !== false){$this->mCampos["otros_si"]["V"] =  $v; } return new MQLCampo($this->mCampos["otros_si"]);}
	function dispocision($v = false){ if($v !== false){$this->mCampos["dispocision"]["V"] =  $v; } return new MQLCampo($this->mCampos["dispocision"]);}
	function saldo_plan($v = false){ if($v !== false){$this->mCampos["saldo_plan"]["V"] =  $v; } return new MQLCampo($this->mCampos["saldo_plan"]);}
	function abonos_ops($v = false){ if($v !== false){$this->mCampos["abonos_ops"]["V"] =  $v; } return new MQLCampo($this->mCampos["abonos_ops"]);}
	function periodo_min($v = false){ if($v !== false){$this->mCampos["periodo_min"]["V"] =  $v; } return new MQLCampo($this->mCampos["periodo_min"]);}
	function periodo_max($v = false){ if($v !== false){$this->mCampos["periodo_max"]["V"] =  $v; } return new MQLCampo($this->mCampos["periodo_max"]);}
	function periodo_last($v = false){ if($v !== false){$this->mCampos["periodo_last"]["V"] =  $v; } return new MQLCampo($this->mCampos["periodo_last"]);}
	function bon_int($v = false){ if($v !== false){$this->mCampos["bon_int"]["V"] =  $v; } return new MQLCampo($this->mCampos["bon_int"]);}
	function bon_mora($v = false){ if($v !== false){$this->mCampos["bon_mora"]["V"] =  $v; } return new MQLCampo($this->mCampos["bon_mora"]);}
	function bon_otros($v = false){ if($v !== false){$this->mCampos["bon_otros"]["V"] =  $v; } return new MQLCampo($this->mCampos["bon_otros"]);}
	function sdo_exig_fut($v = false){ if($v !== false){$this->mCampos["sdo_exig_fut"]["V"] =  $v; } return new MQLCampo($this->mCampos["sdo_exig_fut"]);}
	function sdo_exig_act($v = false){ if($v !== false){$this->mCampos["sdo_exig_act"]["V"] =  $v; } return new MQLCampo($this->mCampos["sdo_exig_act"]);}
	function ints_tot_calc($v = false){ if($v !== false){$this->mCampos["ints_tot_calc"]["V"] =  $v; } return new MQLCampo($this->mCampos["ints_tot_calc"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}


/*	ORM: Tabla:	creditos_etapas	-	Generado:	[25/1/2017 11:30]	*/
/*	ORM: Tabla:	creditos_etapas	-	Generado:	[03/4/2018 22:47]	*/
class cCreditos_etapas {
	private $mCampos	= array("idcreditos_etapas" => array("N"=>"idcreditos_etapas","T"=>"INT","V"=>"","L"=>11),"descripcion" => array("N"=>"descripcion","T"=>"VARCHAR","V"=>"","L"=>50),"tags" => array("N"=>"tags","T"=>"VARCHAR","V"=>"","L"=>40));
	public $IDCREDITOS_ETAPAS = "idcreditos_etapas"; public $DESCRIPCION = "descripcion"; public $TAGS = "tags";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_etapas";}
	function getKey(){ return "idcreditos_etapas";}
	function idcreditos_etapas($v = false){ if($v !== false){$this->mCampos["idcreditos_etapas"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_etapas"]);}
	function descripcion($v = false){ if($v !== false){$this->mCampos["descripcion"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion"]);}
	function tags($v = false){ if($v !== false){$this->mCampos["tags"]["V"] =  $v; } return new MQLCampo($this->mCampos["tags"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	creditos_productos_etapas	-	Generado:	[25/1/2017 11:32]	*/
class cCreditos_productos_etapas {
	private $mCampos	= array("idcreditos_productos_etapas" => array("N"=>"idcreditos_productos_etapas","T"=>"INT","V"=>"","L"=>11),"producto" => array("N"=>"producto","T"=>"INT","V"=>"","L"=>8),"etapa" => array("N"=>"etapa","T"=>"INT","V"=>"1","L"=>4),"nombre" => array("N"=>"nombre","T"=>"VARCHAR","V"=>"","L"=>80),"tags" => array("N"=>"tags","T"=>"VARCHAR","V"=>"","L"=>50),"permisos" => array("N"=>"permisos","T"=>"VARCHAR","V"=>"","L"=>100),"orden" => array("N"=>"orden","T"=>"INT","V"=>"0","L"=>3),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_productos_etapas";}
	function getKey(){ return "idcreditos_productos_etapas";}
	function idcreditos_productos_etapas($v = false){ if($v !== false){$this->mCampos["idcreditos_productos_etapas"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_productos_etapas"]);}
	function producto($v = false){ if($v !== false){$this->mCampos["producto"]["V"] =  $v; } return new MQLCampo($this->mCampos["producto"]);}
	function etapa($v = false){ if($v !== false){$this->mCampos["etapa"]["V"] =  $v; } return new MQLCampo($this->mCampos["etapa"]);}
	function nombre($v = false){ if($v !== false){$this->mCampos["nombre"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre"]);}
	function tags($v = false){ if($v !== false){$this->mCampos["tags"]["V"] =  $v; } return new MQLCampo($this->mCampos["tags"]);}
	function permisos($v = false){ if($v !== false){$this->mCampos["permisos"]["V"] =  $v; } return new MQLCampo($this->mCampos["permisos"]);}
	function orden($v = false){ if($v !== false){$this->mCampos["orden"]["V"] =  $v; } return new MQLCampo($this->mCampos["orden"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}
/*	ORM: Tabla:	creditos_productos_promo	-	Generado:	[28/1/2017 09:56]	*/
class cCreditos_productos_promo {
	private $mCampos	= array("idcreditos_productos_promo" => array("N"=>"idcreditos_productos_promo","T"=>"INT","V"=>"","L"=>11),"tipo_promocion" => array("N"=>"tipo_promocion","T"=>"INT","V"=>"1","L"=>2),"fecha_inicial" => array("N"=>"fecha_inicial","T"=>"DATE","V"=>"","L"=>0),"fecha_final" => array("N"=>"fecha_final","T"=>"DATE","V"=>"","L"=>0),"tipo_operacion" => array("N"=>"tipo_operacion","T"=>"INT","V"=>"0","L"=>8),"condiciones" => array("N"=>"condiciones","T"=>"TEXT","V"=>"","L"=>0),"num_items" => array("N"=>"num_items","T"=>"INT","V"=>"0","L"=>4),"descuento" => array("N"=>"descuento","T"=>"FLOAT","V"=>"0.0000","L"=>13),"precio" => array("N"=>"precio","T"=>"DOUBLE","V"=>"0.00","L"=>25),"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"todas","L"=>20),"estatus" => array("N"=>"estatus","T"=>"INT","V"=>"1","L"=>2),"producto" => array("N"=>"producto","T"=>"INT","V"=>"0","L"=>8),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_productos_promo";}
	function getKey(){ return "idcreditos_productos_promo";}
	function idcreditos_productos_promo($v = false){ if($v !== false){$this->mCampos["idcreditos_productos_promo"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_productos_promo"]);}
	function tipo_promocion($v = false){ if($v !== false){$this->mCampos["tipo_promocion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_promocion"]);}
	function fecha_inicial($v = false){ if($v !== false){$this->mCampos["fecha_inicial"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_inicial"]);}
	function fecha_final($v = false){ if($v !== false){$this->mCampos["fecha_final"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_final"]);}
	function tipo_operacion($v = false){ if($v !== false){$this->mCampos["tipo_operacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_operacion"]);}
	function condiciones($v = false){ if($v !== false){$this->mCampos["condiciones"]["V"] =  $v; } return new MQLCampo($this->mCampos["condiciones"]);}
	function num_items($v = false){ if($v !== false){$this->mCampos["num_items"]["V"] =  $v; } return new MQLCampo($this->mCampos["num_items"]);}
	function descuento($v = false){ if($v !== false){$this->mCampos["descuento"]["V"] =  $v; } return new MQLCampo($this->mCampos["descuento"]);}
	function precio($v = false){ if($v !== false){$this->mCampos["precio"]["V"] =  $v; } return new MQLCampo($this->mCampos["precio"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function estatus($v = false){ if($v !== false){$this->mCampos["estatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus"]);}
	function producto($v = false){ if($v !== false){$this->mCampos["producto"]["V"] =  $v; } return new MQLCampo($this->mCampos["producto"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}
/*	ORM: Tabla:	creditos_productos_req	-	Generado:	[27/1/2017 18:31]	*/
/*	ORM: Tabla:	creditos_productos_req	-	Generado:	[22/2/2017 16:40]	*/
/*	ORM: Tabla:	creditos_productos_req	-	Generado:	[30/5/2017 17:56]	*/
class cCreditos_productos_req {
	private $mCampos	= array("idcreditos_productos_req" => array("N"=>"idcreditos_productos_req","T"=>"INT","V"=>"","L"=>11),"producto" => array("N"=>"producto","T"=>"INT","V"=>"0","L"=>8),"tipo_req" => array("N"=>"tipo_req","T"=>"INT","V"=>"1","L"=>4),"descripcion" => array("N"=>"descripcion","T"=>"VARCHAR","V"=>"","L"=>50),"numero" => array("N"=>"numero","T"=>"INT","V"=>"1","L"=>4),"ruta_validacion" => array("N"=>"ruta_validacion","T"=>"VARCHAR","V"=>"../svc/validad.svc.php","L"=>150),"escore" => array("N"=>"escore","T"=>"FLOAT","V"=>"0.000","L"=>13),"etapa" => array("N"=>"etapa","T"=>"VARCHAR","V"=>"","L"=>40),"requerido" => array("N"=>"requerido","T"=>"INT","V"=>"1","L"=>2),"clave" => array("N"=>"clave","T"=>"INT","V"=>"0","L"=>6),"etapa_id" => array("N"=>"etapa_id","T"=>"INT","V"=>"0","L"=>11),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_productos_req";}
	function getKey(){ return "idcreditos_productos_req";}
	function idcreditos_productos_req($v = false){ if($v !== false){$this->mCampos["idcreditos_productos_req"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_productos_req"]);}
	function producto($v = false){ if($v !== false){$this->mCampos["producto"]["V"] =  $v; } return new MQLCampo($this->mCampos["producto"]);}
	function tipo_req($v = false){ if($v !== false){$this->mCampos["tipo_req"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_req"]);}
	function descripcion($v = false){ if($v !== false){$this->mCampos["descripcion"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion"]);}
	function numero($v = false){ if($v !== false){$this->mCampos["numero"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero"]);}
	function ruta_validacion($v = false){ if($v !== false){$this->mCampos["ruta_validacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["ruta_validacion"]);}
	function escore($v = false){ if($v !== false){$this->mCampos["escore"]["V"] =  $v; } return new MQLCampo($this->mCampos["escore"]);}
	function etapa($v = false){ if($v !== false){$this->mCampos["etapa"]["V"] =  $v; } return new MQLCampo($this->mCampos["etapa"]);}
	function requerido($v = false){ if($v !== false){$this->mCampos["requerido"]["V"] =  $v; } return new MQLCampo($this->mCampos["requerido"]);}
	function clave($v = false){ if($v !== false){$this->mCampos["clave"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave"]);}
	function etapa_id($v = false){ if($v !== false){$this->mCampos["etapa_id"]["V"] =  $v; } return new MQLCampo($this->mCampos["etapa_id"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
}
/*	ORM: Tabla:	creditos_causa_de_vencimientos	-	Generado:	[13/2/2018 11:27]	*/
class cCreditos_causa_de_vencimientos {
	private $mCampos	= array("idcreditos_causa_de_vencimientos" => array("N"=>"idcreditos_causa_de_vencimientos","T"=>"INT","V"=>"","L"=>11),"descripcion_de_la_causa" => array("N"=>"descripcion_de_la_causa","T"=>"VARCHAR","V"=>"","L"=>80));
	public $IDCREDITOS_CAUSA_DE_VENCIMIENTOS = "idcreditos_causa_de_vencimientos"; public $DESCRIPCION_DE_LA_CAUSA = "descripcion_de_la_causa";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_causa_de_vencimientos";}
	function getKey(){ return "idcreditos_causa_de_vencimientos";}
	function idcreditos_causa_de_vencimientos($v = false){ if($v !== false){$this->mCampos["idcreditos_causa_de_vencimientos"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_causa_de_vencimientos"]);}
	function descripcion_de_la_causa($v = false){ if($v !== false){$this->mCampos["descripcion_de_la_causa"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_de_la_causa"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	creditos_garantiasestatus	-	Generado:	[03/4/2018 22:49]	*/
class cCreditos_garantiasestatus {
	private $mCampos	= array("idcreditos_garantiasestatus" => array("N"=>"idcreditos_garantiasestatus","T"=>"INT","V"=>"0","L"=>4),"descripcion_garantiasestatus" => array("N"=>"descripcion_garantiasestatus","T"=>"VARCHAR","V"=>"","L"=>45),"estatus_actual" => array("N"=>"estatus_actual","T"=>"INT","V"=>"0","L"=>4));
	public $IDCREDITOS_GARANTIASESTATUS = "idcreditos_garantiasestatus"; public $DESCRIPCION_GARANTIASESTATUS = "descripcion_garantiasestatus"; public $ESTATUS_ACTUAL = "estatus_actual";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_garantiasestatus";}
	function getKey(){ return "idcreditos_garantiasestatus";}
	function idcreditos_garantiasestatus($v = false){ if($v !== false){$this->mCampos["idcreditos_garantiasestatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_garantiasestatus"]);}
	function descripcion_garantiasestatus($v = false){ if($v !== false){$this->mCampos["descripcion_garantiasestatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_garantiasestatus"]);}
	function estatus_actual($v = false){ if($v !== false){$this->mCampos["estatus_actual"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus_actual"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}


//-------------------------------------------------------------- LEASING ----------------------------------------------
/*	ORM: Tabla:	vehiculos_segmento	-	Generado:	[06/10/2016 16:52]	*/
/*	ORM: Tabla:	vehiculos_segmento	-	Generado:	[20/2/2018 18:02]	*/
class cVehiculos_segmento {
	private $mCampos	= array("idvehiculos_segmento" => array("N"=>"idvehiculos_segmento","T"=>"INT","V"=>"","L"=>11),"nombre_segmento" => array("N"=>"nombre_segmento","T"=>"VARCHAR","V"=>"","L"=>100));
	public $IDVEHICULOS_SEGMENTO = "idvehiculos_segmento"; public $NOMBRE_SEGMENTO = "nombre_segmento";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "vehiculos_segmento";}
	function getKey(){ return "idvehiculos_segmento";}
	function idvehiculos_segmento($v = false){ if($v !== false){$this->mCampos["idvehiculos_segmento"]["V"] =  $v; } return new MQLCampo($this->mCampos["idvehiculos_segmento"]);}
	function nombre_segmento($v = false){ if($v !== false){$this->mCampos["nombre_segmento"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_segmento"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	vehiculos_marcas	-	Generado:	[06/10/2016 16:53]	*/
/*	ORM: Tabla:	vehiculos_marcas	-	Generado:	[07/2/2018 14:28]	*/
class cVehiculos_marcas {
	private $mCampos	= array("idvehiculos_marcas" => array("N"=>"idvehiculos_marcas","T"=>"INT","V"=>"","L"=>11),"nombre_marca" => array("N"=>"nombre_marca","T"=>"VARCHAR","V"=>"","L"=>80));
	public $IDVEHICULOS_MARCAS = "idvehiculos_marcas"; public $NOMBRE_MARCA = "nombre_marca";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "vehiculos_marcas";}
	function getKey(){ return "idvehiculos_marcas";}
	function idvehiculos_marcas($v = false){ if($v !== false){$this->mCampos["idvehiculos_marcas"]["V"] =  $v; } return new MQLCampo($this->mCampos["idvehiculos_marcas"]);}
	function nombre_marca($v = false){ if($v !== false){$this->mCampos["nombre_marca"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_marca"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	leasing_comisiones	-	Generado:	[06/10/2016 16:53]	*/
/*	ORM: Tabla:	leasing_comisiones	-	Generado:	[12/10/2016 18:07]	*/
/*	ORM: Tabla:	leasing_comisiones	-	Generado:	[14/10/2016 15:09]	*/
class cLeasing_comisiones {
	private $mCampos	= array("idleasing_comisiones" => array("N"=>"idleasing_comisiones","T"=>"INT","V"=>"","L"=>11),"tipo_de_originador" => array("N"=>"tipo_de_originador","T"=>"INT","V"=>"0","L"=>4),"tasa_comision" => array("N"=>"tasa_comision","T"=>"FLOAT","V"=>"0.0000","L"=>13),"comision_ejecutivo" => array("N"=>"comision_ejecutivo","T"=>"FLOAT","V"=>"0.0000","L"=>13),"comision_regional" => array("N"=>"comision_regional","T"=>"FLOAT","V"=>"0.0000","L"=>13),"bono" => array("N"=>"bono","T"=>"DOUBLE","V"=>"0.00","L"=>37),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "leasing_comisiones";}
	function getKey(){ return "idleasing_comisiones";}
	function idleasing_comisiones($v = false){ if($v !== false){$this->mCampos["idleasing_comisiones"]["V"] =  $v; } return new MQLCampo($this->mCampos["idleasing_comisiones"]);}
	function tipo_de_originador($v = false){ if($v !== false){$this->mCampos["tipo_de_originador"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_originador"]);}
	function tasa_comision($v = false){ if($v !== false){$this->mCampos["tasa_comision"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_comision"]);}
	function comision_ejecutivo($v = false){ if($v !== false){$this->mCampos["comision_ejecutivo"]["V"] =  $v; } return new MQLCampo($this->mCampos["comision_ejecutivo"]);}
	function comision_regional($v = false){ if($v !== false){$this->mCampos["comision_regional"]["V"] =  $v; } return new MQLCampo($this->mCampos["comision_regional"]);}
	function bono($v = false){ if($v !== false){$this->mCampos["bono"]["V"] =  $v; } return new MQLCampo($this->mCampos["bono"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}
/*	ORM: Tabla:	leasing_bonos	-	Generado:	[26/5/2017 15:59]	*/
class cLeasing_bonos {
	private $mCampos	= array("idleasing_bonos" => array("N"=>"idleasing_bonos","T"=>"INT","V"=>"","L"=>11),"clave_leasing" => array("N"=>"clave_leasing","T"=>"INT","V"=>"0","L"=>11),"tipo_bono" => array("N"=>"tipo_bono","T"=>"INT","V"=>"0","L"=>4),"tipo_destino" => array("N"=>"tipo_destino","T"=>"INT","V"=>"0","L"=>6),"tasa_bono" => array("N"=>"tasa_bono","T"=>"FLOAT","V"=>"0.0000","L"=>13),"monto_bono" => array("N"=>"monto_bono","T"=>"DOUBLE","V"=>"0.00","L"=>25),"fecha" => array("N"=>"fecha","T"=>"DATE","V"=>"2017-01-01","L"=>0),"fecha_de_pago" => array("N"=>"fecha_de_pago","T"=>"DATE","V"=>"2017-01-01","L"=>0),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "leasing_bonos";}
	function getKey(){ return "idleasing_bonos";}
	function idleasing_bonos($v = false){ if($v !== false){$this->mCampos["idleasing_bonos"]["V"] =  $v; } return new MQLCampo($this->mCampos["idleasing_bonos"]);}
	function clave_leasing($v = false){ if($v !== false){$this->mCampos["clave_leasing"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_leasing"]);}
	function tipo_bono($v = false){ if($v !== false){$this->mCampos["tipo_bono"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_bono"]);}
	function tipo_destino($v = false){ if($v !== false){$this->mCampos["tipo_destino"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_destino"]);}
	function tasa_bono($v = false){ if($v !== false){$this->mCampos["tasa_bono"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_bono"]);}
	function monto_bono($v = false){ if($v !== false){$this->mCampos["monto_bono"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_bono"]);}
	function fecha($v = false){ if($v !== false){$this->mCampos["fecha"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha"]);}
	function fecha_de_pago($v = false){ if($v !== false){$this->mCampos["fecha_de_pago"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_pago"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	leasing_activos	-	Generado:	[05/6/2017 15:41]	*/
/*	ORM: Tabla:	leasing_activos	-	Generado:	[07/6/2017 16:31]	*/
/*	ORM: Tabla:	leasing_activos	-	Generado:	[13/7/2017 12:31]	*/
/*	ORM: Tabla:	leasing_activos	-	Generado:	[22/12/2017 14:43]	*/
/*	ORM: Tabla:	leasing_activos	-	Generado:	[20/2/2018 17:39]	*/
class cLeasing_activos {
	private $mCampos	= array("idleasing_activos" => array("N"=>"idleasing_activos","T"=>"INT","V"=>"","L"=>11),"clave_leasing" => array("N"=>"clave_leasing","T"=>"INT","V"=>"","L"=>11),"persona" => array("N"=>"persona","T"=>"BIGINT","V"=>"","L"=>20),"credito" => array("N"=>"credito","T"=>"BIGINT","V"=>"","L"=>20),"descripcion" => array("N"=>"descripcion","T"=>"VARCHAR","V"=>"","L"=>60),"proveedor" => array("N"=>"proveedor","T"=>"BIGINT","V"=>"1","L"=>20),"fecha_compra" => array("N"=>"fecha_compra","T"=>"DATE","V"=>"","L"=>0),"fecha_registro" => array("N"=>"fecha_registro","T"=>"DATE","V"=>"","L"=>0),"fecha_mtto" => array("N"=>"fecha_mtto","T"=>"DATE","V"=>"","L"=>0),"fecha_seguro" => array("N"=>"fecha_seguro","T"=>"DATE","V"=>"","L"=>0),"tipo_activo" => array("N"=>"tipo_activo","T"=>"INT","V"=>"","L"=>11),"tipo_seguro" => array("N"=>"tipo_seguro","T"=>"INT","V"=>"","L"=>4),"tasa_depreciacion" => array("N"=>"tasa_depreciacion","T"=>"FLOAT","V"=>"0.000","L"=>13),"valor_nominal" => array("N"=>"valor_nominal","T"=>"DOUBLE","V"=>"0.00","L"=>37),"serie" => array("N"=>"serie","T"=>"VARCHAR","V"=>"","L"=>20),"factura" => array("N"=>"factura","T"=>"VARCHAR","V"=>"","L"=>20),"placas" => array("N"=>"placas","T"=>"VARCHAR","V"=>"","L"=>20),"motor" => array("N"=>"motor","T"=>"VARCHAR","V"=>"","L"=>20),"marca" => array("N"=>"marca","T"=>"INT","V"=>"0","L"=>6),"color" => array("N"=>"color","T"=>"VARCHAR","V"=>"","L"=>25),"valor_venta" => array("N"=>"valor_venta","T"=>"DOUBLE","V"=>"0.00","L"=>37),"valor_residual" => array("N"=>"valor_residual","T"=>"DOUBLE","V"=>"0.00","L"=>37),"monto_anticipo" => array("N"=>"monto_anticipo","T"=>"DOUBLE","V"=>"0.00","L"=>37),"aseguradora" => array("N"=>"aseguradora","T"=>"BIGINT","V"=>"1","L"=>20),"status" => array("N"=>"status","T"=>"INT","V"=>"1","L"=>4),"baja_id" => array("N"=>"baja_id","T"=>"INT","V"=>"0","L"=>4),"baja_fecha" => array("N"=>"baja_fecha","T"=>"DATE","V"=>"2019-12-01","L"=>0),"serie_nal" => array("N"=>"serie_nal","T"=>"VARCHAR","V"=>"","L"=>20),"annio" => array("N"=>"annio","T"=>"VARCHAR","V"=>"","L"=>10),"segmento" => array("N"=>"segmento","T"=>"INT","V"=>"1","L"=>2));
	public $IDLEASING_ACTIVOS = "idleasing_activos"; public $CLAVE_LEASING = "clave_leasing"; public $PERSONA = "persona"; public $CREDITO = "credito"; public $DESCRIPCION = "descripcion"; public $PROVEEDOR = "proveedor"; public $FECHA_COMPRA = "fecha_compra"; public $FECHA_REGISTRO = "fecha_registro"; public $FECHA_MTTO = "fecha_mtto"; public $FECHA_SEGURO = "fecha_seguro"; public $TIPO_ACTIVO = "tipo_activo"; public $TIPO_SEGURO = "tipo_seguro"; public $TASA_DEPRECIACION = "tasa_depreciacion"; public $VALOR_NOMINAL = "valor_nominal"; public $SERIE = "serie"; public $FACTURA = "factura"; public $PLACAS = "placas"; public $MOTOR = "motor"; public $MARCA = "marca"; public $COLOR = "color"; public $VALOR_VENTA = "valor_venta"; public $VALOR_RESIDUAL = "valor_residual"; public $MONTO_ANTICIPO = "monto_anticipo"; public $ASEGURADORA = "aseguradora"; public $STATUS = "status"; public $BAJA_ID = "baja_id"; public $BAJA_FECHA = "baja_fecha"; public $SERIE_NAL = "serie_nal"; public $ANNIO = "annio"; public $SEGMENTO = "segmento";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "leasing_activos";}
	function getKey(){ return "idleasing_activos";}
	function idleasing_activos($v = false){ if($v !== false){$this->mCampos["idleasing_activos"]["V"] =  $v; } return new MQLCampo($this->mCampos["idleasing_activos"]);}
	function clave_leasing($v = false){ if($v !== false){$this->mCampos["clave_leasing"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_leasing"]);}
	function persona($v = false){ if($v !== false){$this->mCampos["persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["persona"]);}
	function credito($v = false){ if($v !== false){$this->mCampos["credito"]["V"] =  $v; } return new MQLCampo($this->mCampos["credito"]);}
	function descripcion($v = false){ if($v !== false){$this->mCampos["descripcion"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion"]);}
	function proveedor($v = false){ if($v !== false){$this->mCampos["proveedor"]["V"] =  $v; } return new MQLCampo($this->mCampos["proveedor"]);}
	function fecha_compra($v = false){ if($v !== false){$this->mCampos["fecha_compra"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_compra"]);}
	function fecha_registro($v = false){ if($v !== false){$this->mCampos["fecha_registro"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_registro"]);}
	function fecha_mtto($v = false){ if($v !== false){$this->mCampos["fecha_mtto"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_mtto"]);}
	function fecha_seguro($v = false){ if($v !== false){$this->mCampos["fecha_seguro"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_seguro"]);}
	function tipo_activo($v = false){ if($v !== false){$this->mCampos["tipo_activo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_activo"]);}
	function tipo_seguro($v = false){ if($v !== false){$this->mCampos["tipo_seguro"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_seguro"]);}
	function tasa_depreciacion($v = false){ if($v !== false){$this->mCampos["tasa_depreciacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_depreciacion"]);}
	function valor_nominal($v = false){ if($v !== false){$this->mCampos["valor_nominal"]["V"] =  $v; } return new MQLCampo($this->mCampos["valor_nominal"]);}
	function serie($v = false){ if($v !== false){$this->mCampos["serie"]["V"] =  $v; } return new MQLCampo($this->mCampos["serie"]);}
	function factura($v = false){ if($v !== false){$this->mCampos["factura"]["V"] =  $v; } return new MQLCampo($this->mCampos["factura"]);}
	function placas($v = false){ if($v !== false){$this->mCampos["placas"]["V"] =  $v; } return new MQLCampo($this->mCampos["placas"]);}
	function motor($v = false){ if($v !== false){$this->mCampos["motor"]["V"] =  $v; } return new MQLCampo($this->mCampos["motor"]);}
	function marca($v = false){ if($v !== false){$this->mCampos["marca"]["V"] =  $v; } return new MQLCampo($this->mCampos["marca"]);}
	function color($v = false){ if($v !== false){$this->mCampos["color"]["V"] =  $v; } return new MQLCampo($this->mCampos["color"]);}
	function valor_venta($v = false){ if($v !== false){$this->mCampos["valor_venta"]["V"] =  $v; } return new MQLCampo($this->mCampos["valor_venta"]);}
	function valor_residual($v = false){ if($v !== false){$this->mCampos["valor_residual"]["V"] =  $v; } return new MQLCampo($this->mCampos["valor_residual"]);}
	function monto_anticipo($v = false){ if($v !== false){$this->mCampos["monto_anticipo"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_anticipo"]);}
	function aseguradora($v = false){ if($v !== false){$this->mCampos["aseguradora"]["V"] =  $v; } return new MQLCampo($this->mCampos["aseguradora"]);}
	function status($v = false){ if($v !== false){$this->mCampos["status"]["V"] =  $v; } return new MQLCampo($this->mCampos["status"]);}
	function baja_id($v = false){ if($v !== false){$this->mCampos["baja_id"]["V"] =  $v; } return new MQLCampo($this->mCampos["baja_id"]);}
	function baja_fecha($v = false){ if($v !== false){$this->mCampos["baja_fecha"]["V"] =  $v; } return new MQLCampo($this->mCampos["baja_fecha"]);}
	function serie_nal($v = false){ if($v !== false){$this->mCampos["serie_nal"]["V"] =  $v; } return new MQLCampo($this->mCampos["serie_nal"]);}
	function annio($v = false){ if($v !== false){$this->mCampos["annio"]["V"] =  $v; } return new MQLCampo($this->mCampos["annio"]);}
	function segmento($v = false){ if($v !== false){$this->mCampos["segmento"]["V"] =  $v; } return new MQLCampo($this->mCampos["segmento"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}

/*	ORM: Tabla:	vehiculos_tenencia	-	Generado:	[06/10/2016 16:53]	*/
class cVehiculos_tenencia {
	private $mCampos	= array("idvehiculos_tenencia" => array("N"=>"idvehiculos_tenencia","T"=>"INT","V"=>"","L"=>11),"entidadfederativa" => array("N"=>"entidadfederativa","T"=>"INT","V"=>"0","L"=>4),"cobrogestoria" => array("N"=>"cobrogestoria","T"=>"FLOAT","V"=>"0.00","L"=>21),"placas" => array("N"=>"placas","T"=>"FLOAT","V"=>"0.00","L"=>21),"tenencia" => array("N"=>"tenencia","T"=>"FLOAT","V"=>"0.0000","L"=>13),"limitetenencia" => array("N"=>"limitetenencia","T"=>"DOUBLE","V"=>"0.00","L"=>25),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "vehiculos_tenencia";}
	function getKey(){ return "idvehiculos_tenencia";}
	function idvehiculos_tenencia($v = false){ if($v !== false){$this->mCampos["idvehiculos_tenencia"]["V"] =  $v; } return new MQLCampo($this->mCampos["idvehiculos_tenencia"]);}
	function entidadfederativa($v = false){ if($v !== false){$this->mCampos["entidadfederativa"]["V"] =  $v; } return new MQLCampo($this->mCampos["entidadfederativa"]);}
	function cobrogestoria($v = false){ if($v !== false){$this->mCampos["cobrogestoria"]["V"] =  $v; } return new MQLCampo($this->mCampos["cobrogestoria"]);}
	function placas($v = false){ if($v !== false){$this->mCampos["placas"]["V"] =  $v; } return new MQLCampo($this->mCampos["placas"]);}
	function tenencia($v = false){ if($v !== false){$this->mCampos["tenencia"]["V"] =  $v; } return new MQLCampo($this->mCampos["tenencia"]);}
	function limitetenencia($v = false){ if($v !== false){$this->mCampos["limitetenencia"]["V"] =  $v; } return new MQLCampo($this->mCampos["limitetenencia"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}
/*	ORM: Tabla:	leasing_tipo_rac	-	Generado:	[06/10/2016 16:54]	*/
class cLeasing_tipo_rac {
	private $mCampos	= array("idleasing_tipo_rac" => array("N"=>"idleasing_tipo_rac","T"=>"INT","V"=>"","L"=>11),"nombre_tipo_rac" => array("N"=>"nombre_tipo_rac","T"=>"VARCHAR","V"=>"","L"=>40),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "leasing_tipo_rac";}
	function getKey(){ return "idleasing_tipo_rac";}
	function idleasing_tipo_rac($v = false){ if($v !== false){$this->mCampos["idleasing_tipo_rac"]["V"] =  $v; } return new MQLCampo($this->mCampos["idleasing_tipo_rac"]);}
	function nombre_tipo_rac($v = false){ if($v !== false){$this->mCampos["nombre_tipo_rac"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_tipo_rac"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}
/*	ORM: Tabla:	leasing_tasas	-	Generado:	[06/10/2016 16:54]	*/
/*	ORM: Tabla:	leasing_tasas	-	Generado:	[14/10/2016 09:50]	*/
/*	ORM: Tabla:	leasing_tasas	-	Generado:	[20/1/2017 14:24]	*/
/*	ORM: Tabla:	leasing_tasas	-	Generado:	[25/5/2017 12:53]	*/
/*	ORM: Tabla:	leasing_tasas	-	Generado:	[15/6/2017 15:22]	*/
class cLeasing_tasas {
	private $mCampos	= array("idleasing_tasas" => array("N"=>"idleasing_tasas","T"=>"INT","V"=>"","L"=>11),"tipo_de_rac" => array("N"=>"tipo_de_rac","T"=>"INT","V"=>"0","L"=>4),"tasa_ofrecida" => array("N"=>"tasa_ofrecida","T"=>"FLOAT","V"=>"0.0000","L"=>13),"limite_inferior" => array("N"=>"limite_inferior","T"=>"INT","V"=>"0","L"=>4),"limite_superior" => array("N"=>"limite_superior","T"=>"INT","V"=>"0","L"=>4),"frecuencia" => array("N"=>"frecuencia","T"=>"INT","V"=>"30","L"=>4),"comision_apertura" => array("N"=>"comision_apertura","T"=>"FLOAT","V"=>"0.0000","L"=>13),"tasa_marginal" => array("N"=>"tasa_marginal","T"=>"FLOAT","V"=>"0.0000","L"=>13),"tasa_vec" => array("N"=>"tasa_vec","T"=>"FLOAT","V"=>"0.0000","L"=>13),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "leasing_tasas";}
	function getKey(){ return "idleasing_tasas";}
	function idleasing_tasas($v = false){ if($v !== false){$this->mCampos["idleasing_tasas"]["V"] =  $v; } return new MQLCampo($this->mCampos["idleasing_tasas"]);}
	function tipo_de_rac($v = false){ if($v !== false){$this->mCampos["tipo_de_rac"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_rac"]);}
	function tasa_ofrecida($v = false){ if($v !== false){$this->mCampos["tasa_ofrecida"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_ofrecida"]);}
	function limite_inferior($v = false){ if($v !== false){$this->mCampos["limite_inferior"]["V"] =  $v; } return new MQLCampo($this->mCampos["limite_inferior"]);}
	function limite_superior($v = false){ if($v !== false){$this->mCampos["limite_superior"]["V"] =  $v; } return new MQLCampo($this->mCampos["limite_superior"]);}
	function frecuencia($v = false){ if($v !== false){$this->mCampos["frecuencia"]["V"] =  $v; } return new MQLCampo($this->mCampos["frecuencia"]);}
	function comision_apertura($v = false){ if($v !== false){$this->mCampos["comision_apertura"]["V"] =  $v; } return new MQLCampo($this->mCampos["comision_apertura"]);}
	function tasa_marginal($v = false){ if($v !== false){$this->mCampos["tasa_marginal"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_marginal"]);}
	function tasa_vec($v = false){ if($v !== false){$this->mCampos["tasa_vec"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_vec"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	leasing_escenarios	-	Generado:	[06/10/2016 16:54]	*/
class cLeasing_escenarios {
	private $mCampos	= array("idleasing_escenarios" => array("N"=>"idleasing_escenarios","T"=>"INT","V"=>"","L"=>11),"frecuencia" => array("N"=>"frecuencia","T"=>"INT","V"=>"30","L"=>4),"plazo" => array("N"=>"plazo","T"=>"INT","V"=>"12","L"=>4),"descripcion_escenario" => array("N"=>"descripcion_escenario","T"=>"VARCHAR","V"=>"","L"=>40),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "leasing_escenarios";}
	function getKey(){ return "idleasing_escenarios";}
	function idleasing_escenarios($v = false){ if($v !== false){$this->mCampos["idleasing_escenarios"]["V"] =  $v; } return new MQLCampo($this->mCampos["idleasing_escenarios"]);}
	function frecuencia($v = false){ if($v !== false){$this->mCampos["frecuencia"]["V"] =  $v; } return new MQLCampo($this->mCampos["frecuencia"]);}
	function plazo($v = false){ if($v !== false){$this->mCampos["plazo"]["V"] =  $v; } return new MQLCampo($this->mCampos["plazo"]);}
	function descripcion_escenario($v = false){ if($v !== false){$this->mCampos["descripcion_escenario"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_escenario"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}
/*	ORM: Tabla:	vehiculos_gps	-	Generado:	[06/10/2016 16:55]	*/
class cVehiculos_gps {
	private $mCampos	= array("idvehiculos_gps" => array("N"=>"idvehiculos_gps","T"=>"INT","V"=>"","L"=>11),"nombre_gps" => array("N"=>"nombre_gps","T"=>"VARCHAR","V"=>"","L"=>40),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "vehiculos_gps";}
	function getKey(){ return "idvehiculos_gps";}
	function idvehiculos_gps($v = false){ if($v !== false){$this->mCampos["idvehiculos_gps"]["V"] =  $v; } return new MQLCampo($this->mCampos["idvehiculos_gps"]);}
	function nombre_gps($v = false){ if($v !== false){$this->mCampos["nombre_gps"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_gps"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}
/*	ORM: Tabla:	leasing_originadores	-	Generado:	[06/10/2016 16:55]	*/
/*	ORM: Tabla:	leasing_originadores	-	Generado:	[07/10/2016 17:07]	*/
/*	ORM: Tabla:	leasing_originadores	-	Generado:	[14/10/2016 15:01]	*/
/*	ORM: Tabla:	leasing_originadores	-	Generado:	[24/1/2017 10:13]	*/
class cLeasing_originadores {
	private $mCampos	= array("idleasing_originadores" => array("N"=>"idleasing_originadores","T"=>"INT","V"=>"","L"=>11),"tipo_de_originador" => array("N"=>"tipo_de_originador","T"=>"INT","V"=>"1","L"=>4),"nombre_originador" => array("N"=>"nombre_originador","T"=>"VARCHAR","V"=>"","L"=>100),"rfc_originador" => array("N"=>"rfc_originador","T"=>"VARCHAR","V"=>"","L"=>15),"clave_de_persona" => array("N"=>"clave_de_persona","T"=>"BIGINT","V"=>"0","L"=>20),"clave_banco" => array("N"=>"clave_banco","T"=>"INT","V"=>"0","L"=>8),"cuenta_clabe" => array("N"=>"cuenta_clabe","T"=>"VARCHAR","V"=>"","L"=>40),"cuenta_bancaria" => array("N"=>"cuenta_bancaria","T"=>"VARCHAR","V"=>"","L"=>40),"frecuencia_de_pago" => array("N"=>"frecuencia_de_pago","T"=>"INT","V"=>"7","L"=>6),"email_de_contacto" => array("N"=>"email_de_contacto","T"=>"VARCHAR","V"=>"","L"=>40),"tipo_de_comision" => array("N"=>"tipo_de_comision","T"=>"INT","V"=>"1","L"=>4),"comision" => array("N"=>"comision","T"=>"FLOAT","V"=>"0.0000","L"=>13),"meta" => array("N"=>"meta","T"=>"DOUBLE","V"=>"0.00","L"=>37),"frecuencia_meta" => array("N"=>"frecuencia_meta","T"=>"INT","V"=>"0","L"=>4),"direccion" => array("N"=>"direccion","T"=>"VARCHAR","V"=>"","L"=>150),"telefono" => array("N"=>"telefono","T"=>"VARCHAR","V"=>"","L"=>15),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "leasing_originadores";}
	function getKey(){ return "idleasing_originadores";}
	function idleasing_originadores($v = false){ if($v !== false){$this->mCampos["idleasing_originadores"]["V"] =  $v; } return new MQLCampo($this->mCampos["idleasing_originadores"]);}
	function tipo_de_originador($v = false){ if($v !== false){$this->mCampos["tipo_de_originador"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_originador"]);}
	function nombre_originador($v = false){ if($v !== false){$this->mCampos["nombre_originador"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_originador"]);}
	function rfc_originador($v = false){ if($v !== false){$this->mCampos["rfc_originador"]["V"] =  $v; } return new MQLCampo($this->mCampos["rfc_originador"]);}
	function clave_de_persona($v = false){ if($v !== false){$this->mCampos["clave_de_persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_persona"]);}
	function clave_banco($v = false){ if($v !== false){$this->mCampos["clave_banco"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_banco"]);}
	function cuenta_clabe($v = false){ if($v !== false){$this->mCampos["cuenta_clabe"]["V"] =  $v; } return new MQLCampo($this->mCampos["cuenta_clabe"]);}
	function cuenta_bancaria($v = false){ if($v !== false){$this->mCampos["cuenta_bancaria"]["V"] =  $v; } return new MQLCampo($this->mCampos["cuenta_bancaria"]);}
	function frecuencia_de_pago($v = false){ if($v !== false){$this->mCampos["frecuencia_de_pago"]["V"] =  $v; } return new MQLCampo($this->mCampos["frecuencia_de_pago"]);}
	function email_de_contacto($v = false){ if($v !== false){$this->mCampos["email_de_contacto"]["V"] =  $v; } return new MQLCampo($this->mCampos["email_de_contacto"]);}
	function tipo_de_comision($v = false){ if($v !== false){$this->mCampos["tipo_de_comision"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_comision"]);}
	function comision($v = false){ if($v !== false){$this->mCampos["comision"]["V"] =  $v; } return new MQLCampo($this->mCampos["comision"]);}
	function meta($v = false){ if($v !== false){$this->mCampos["meta"]["V"] =  $v; } return new MQLCampo($this->mCampos["meta"]);}
	function frecuencia_meta($v = false){ if($v !== false){$this->mCampos["frecuencia_meta"]["V"] =  $v; } return new MQLCampo($this->mCampos["frecuencia_meta"]);}
	function direccion($v = false){ if($v !== false){$this->mCampos["direccion"]["V"] =  $v; } return new MQLCampo($this->mCampos["direccion"]);}
	function telefono($v = false){ if($v !== false){$this->mCampos["telefono"]["V"] =  $v; } return new MQLCampo($this->mCampos["telefono"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	leasing_originadores_tipos	-	Generado:	[07/10/2016 17:51]	*/
class cLeasing_originadores_tipos {
	private $mCampos	= array("idleasing_originadores_tipos" => array("N"=>"idleasing_originadores_tipos","T"=>"INT","V"=>"","L"=>11),"nombre_tipo_originador" => array("N"=>"nombre_tipo_originador","T"=>"VARCHAR","V"=>"","L"=>40),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "leasing_originadores_tipos";}
	function getKey(){ return "idleasing_originadores_tipos";}
	function idleasing_originadores_tipos($v = false){ if($v !== false){$this->mCampos["idleasing_originadores_tipos"]["V"] =  $v; } return new MQLCampo($this->mCampos["idleasing_originadores_tipos"]);}
	function nombre_tipo_originador($v = false){ if($v !== false){$this->mCampos["nombre_tipo_originador"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_tipo_originador"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	leasing_usuarios	-	Generado:	[06/10/2016 16:55]	*/
/*	ORM: Tabla:	leasing_usuarios	-	Generado:	[08/10/2016 22:50]	*/
/*	ORM: Tabla:	leasing_usuarios	-	Generado:	[24/10/2016 12:52]	*/
/*	ORM: Tabla:	leasing_usuarios	-	Generado:	[27/12/2016 11:58]	*/
/*	ORM: Tabla:	leasing_usuarios	-	Generado:	[24/3/2018 14:21]	*/
class cLeasing_usuarios {
	private $mCampos	= array("idleasing_usuarios" => array("N"=>"idleasing_usuarios","T"=>"INT","V"=>"","L"=>11),"originador" => array("N"=>"originador","T"=>"INT","V"=>"0","L"=>8),"nombre" => array("N"=>"nombre","T"=>"VARCHAR","V"=>"","L"=>100),"pin" => array("N"=>"pin","T"=>"VARCHAR","V"=>"","L"=>40),"correo_electronico" => array("N"=>"correo_electronico","T"=>"VARCHAR","V"=>"","L"=>80),"estatus" => array("N"=>"estatus","T"=>"INT","V"=>"1","L"=>2),"administrador" => array("N"=>"administrador","T"=>"INT","V"=>"0","L"=>2),"idusuario" => array("N"=>"idusuario","T"=>"VARCHAR","V"=>"0","L"=>8),"telefono" => array("N"=>"telefono","T"=>"VARCHAR","V"=>"","L"=>15),"tasa_com" => array("N"=>"tasa_com","T"=>"FLOAT","V"=>"0.0000","L"=>13));
	public $IDLEASING_USUARIOS = "idleasing_usuarios"; public $ORIGINADOR = "originador"; public $NOMBRE = "nombre"; public $PIN = "pin"; public $CORREO_ELECTRONICO = "correo_electronico"; public $ESTATUS = "estatus"; public $ADMINISTRADOR = "administrador"; public $IDUSUARIO = "idusuario"; public $TELEFONO = "telefono"; public $TASA_COM = "tasa_com";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "leasing_usuarios";}
	function getKey(){ return "idleasing_usuarios";}
	function idleasing_usuarios($v = false){ if($v !== false){$this->mCampos["idleasing_usuarios"]["V"] =  $v; } return new MQLCampo($this->mCampos["idleasing_usuarios"]);}
	function originador($v = false){ if($v !== false){$this->mCampos["originador"]["V"] =  $v; } return new MQLCampo($this->mCampos["originador"]);}
	function nombre($v = false){ if($v !== false){$this->mCampos["nombre"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre"]);}
	function pin($v = false){ if($v !== false){$this->mCampos["pin"]["V"] =  $v; } return new MQLCampo($this->mCampos["pin"]);}
	function correo_electronico($v = false){ if($v !== false){$this->mCampos["correo_electronico"]["V"] =  $v; } return new MQLCampo($this->mCampos["correo_electronico"]);}
	function estatus($v = false){ if($v !== false){$this->mCampos["estatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus"]);}
	function administrador($v = false){ if($v !== false){$this->mCampos["administrador"]["V"] =  $v; } return new MQLCampo($this->mCampos["administrador"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function telefono($v = false){ if($v !== false){$this->mCampos["telefono"]["V"] =  $v; } return new MQLCampo($this->mCampos["telefono"]);}
	function tasa_com($v = false){ if($v !== false){$this->mCampos["tasa_com"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_com"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	leasing_plazos	-	Generado:	[06/10/2016 16:56]	*/
class cLeasing_plazos {
	private $mCampos	= array("idleasing_plazos" => array("N"=>"idleasing_plazos","T"=>"INT","V"=>"","L"=>11),"frecuencia" => array("N"=>"frecuencia","T"=>"INT","V"=>"30","L"=>4),"limite_inferior" => array("N"=>"limite_inferior","T"=>"INT","V"=>"0","L"=>4),"limite_superior" => array("N"=>"limite_superior","T"=>"INT","V"=>"0","L"=>4),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "leasing_plazos";}
	function getKey(){ return "idleasing_plazos";}
	function idleasing_plazos($v = false){ if($v !== false){$this->mCampos["idleasing_plazos"]["V"] =  $v; } return new MQLCampo($this->mCampos["idleasing_plazos"]);}
	function frecuencia($v = false){ if($v !== false){$this->mCampos["frecuencia"]["V"] =  $v; } return new MQLCampo($this->mCampos["frecuencia"]);}
	function limite_inferior($v = false){ if($v !== false){$this->mCampos["limite_inferior"]["V"] =  $v; } return new MQLCampo($this->mCampos["limite_inferior"]);}
	function limite_superior($v = false){ if($v !== false){$this->mCampos["limite_superior"]["V"] =  $v; } return new MQLCampo($this->mCampos["limite_superior"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}
/*	ORM: Tabla:	leasing_residual	-	Generado:	[06/10/2016 16:56]	*/
/*	ORM: Tabla:	leasing_residual	-	Generado:	[15/2/2017 16:00]	*/
class cLeasing_residual {
	private $mCampos	= array("idleasing_residual" => array("N"=>"idleasing_residual","T"=>"INT","V"=>"","L"=>11),"frecuencia" => array("N"=>"frecuencia","T"=>"INT","V"=>"30","L"=>4),"limite_inferior" => array("N"=>"limite_inferior","T"=>"INT","V"=>"0","L"=>4),"limite_superior" => array("N"=>"limite_superior","T"=>"INT","V"=>"0","L"=>4),"porciento_residual" => array("N"=>"porciento_residual","T"=>"FLOAT","V"=>"0.0000","L"=>13),"porciento_final" => array("N"=>"porciento_final","T"=>"FLOAT","V"=>"0.0000","L"=>13),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "leasing_residual";}
	function getKey(){ return "idleasing_residual";}
	function idleasing_residual($v = false){ if($v !== false){$this->mCampos["idleasing_residual"]["V"] =  $v; } return new MQLCampo($this->mCampos["idleasing_residual"]);}
	function frecuencia($v = false){ if($v !== false){$this->mCampos["frecuencia"]["V"] =  $v; } return new MQLCampo($this->mCampos["frecuencia"]);}
	function limite_inferior($v = false){ if($v !== false){$this->mCampos["limite_inferior"]["V"] =  $v; } return new MQLCampo($this->mCampos["limite_inferior"]);}
	function limite_superior($v = false){ if($v !== false){$this->mCampos["limite_superior"]["V"] =  $v; } return new MQLCampo($this->mCampos["limite_superior"]);}
	function porciento_residual($v = false){ if($v !== false){$this->mCampos["porciento_residual"]["V"] =  $v; } return new MQLCampo($this->mCampos["porciento_residual"]);}
	function porciento_final($v = false){ if($v !== false){$this->mCampos["porciento_final"]["V"] =  $v; } return new MQLCampo($this->mCampos["porciento_final"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}
/*	ORM: Tabla:	leasing_tramites_cat	-	Generado:	[21/7/2017 17:29]	*/
class cLeasing_tramites_cat {
	private $mCampos	= array("idleasing_tramites_cat" => array("N"=>"idleasing_tramites_cat","T"=>"INT","V"=>"","L"=>11),"nombre_tramite" => array("N"=>"nombre_tramite","T"=>"VARCHAR","V"=>"","L"=>50),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "leasing_tramites_cat";}
	function getKey(){ return "idleasing_tramites_cat";}
	function idleasing_tramites_cat($v = false){ if($v !== false){$this->mCampos["idleasing_tramites_cat"]["V"] =  $v; } return new MQLCampo($this->mCampos["idleasing_tramites_cat"]);}
	function nombre_tramite($v = false){ if($v !== false){$this->mCampos["nombre_tramite"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_tramite"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	originacion_leasing	-	Generado:	[06/10/2016 16:58]	*/
/*	ORM: Tabla:	originacion_leasing	-	Generado:	[10/10/2016 13:15]	*/
/*	ORM: Tabla:	originacion_leasing	-	Generado:	[11/10/2016 10:39]	*/
/*	ORM: Tabla:	originacion_leasing	-	Generado:	[13/10/2016 17:15]	*/
/*	ORM: Tabla:	originacion_leasing	-	Generado:	[25/10/2016 11:58]	*/
/*	ORM: Tabla:	originacion_leasing	-	Generado:	[26/10/2016 15:07]	*/
/*	ORM: Tabla:	originacion_leasing	-	Generado:	[28/10/2016 14:57]	*/
/*	ORM: Tabla:	originacion_leasing	-	Generado:	[05/11/2016 14:07]	*/
/*	ORM: Tabla:	originacion_leasing	-	Generado:	[09/11/2016 18:42]	*/
/*	ORM: Tabla:	originacion_leasing	-	Generado:	[30/12/2016 17:32]	*/
/*	ORM: Tabla:	originacion_leasing	-	Generado:	[02/1/2017 14:48]	*/
/*	ORM: Tabla:	originacion_leasing	-	Generado:	[18/2/2017 12:43]	*/
/*	ORM: Tabla:	originacion_leasing	-	Generado:	[25/5/2017 16:01]	*/
/*	ORM: Tabla:	originacion_leasing	-	Generado:	[21/7/2017 14:16]	*/
/*	ORM: Tabla:	originacion_leasing	-	Generado:	[05/9/2017 19:12]	*/
/*	ORM: Tabla:	originacion_leasing	-	Generado:	[12/9/2017 10:06]	*/
/*	ORM: Tabla:	originacion_leasing	-	Generado:	[15/9/2017 15:08]	*/
/*	ORM: Tabla:	originacion_leasing	-	Generado:	[08/11/2017 14:40]	*/
/*	ORM: Tabla:	originacion_leasing	-	Generado:	[27/1/2018 12:14]	*/
/*	ORM: Tabla:	originacion_leasing	-	Generado:	[24/3/2018 14:20]	*/
/*	ORM: Tabla:	originacion_leasing	-	Generado:	[25/3/2018 13:57]	*/
class cOriginacion_leasing {
	private $mCampos	= array("idoriginacion_leasing" => array("N"=>"idoriginacion_leasing","T"=>"INT","V"=>"","L"=>11),"fecha_origen" => array("N"=>"fecha_origen","T"=>"DATE","V"=>"0000-00-00","L"=>0),"persona" => array("N"=>"persona","T"=>"BIGINT","V"=>"0","L"=>20),"credito" => array("N"=>"credito","T"=>"BIGINT","V"=>"0","L"=>20),"marca" => array("N"=>"marca","T"=>"INT","V"=>"1","L"=>4),"modelo" => array("N"=>"modelo","T"=>"VARCHAR","V"=>"","L"=>100),"annio" => array("N"=>"annio","T"=>"VARCHAR","V"=>"","L"=>6),"tipo_leasing" => array("N"=>"tipo_leasing","T"=>"INT","V"=>"1","L"=>2),"tipo_uso" => array("N"=>"tipo_uso","T"=>"INT","V"=>"0","L"=>4),"tipo_rac" => array("N"=>"tipo_rac","T"=>"INT","V"=>"0","L"=>4),"tipo_gps" => array("N"=>"tipo_gps","T"=>"INT","V"=>"1","L"=>4),"originador" => array("N"=>"originador","T"=>"INT","V"=>"0","L"=>8),"suboriginador" => array("N"=>"suboriginador","T"=>"INT","V"=>"0","L"=>8),"precio_vehiculo" => array("N"=>"precio_vehiculo","T"=>"DOUBLE","V"=>"0.00","L"=>37),"monto_aliado" => array("N"=>"monto_aliado","T"=>"DOUBLE","V"=>"0.00","L"=>37),"monto_accesorios" => array("N"=>"monto_accesorios","T"=>"DOUBLE","V"=>"0.00","L"=>37),"monto_anticipo" => array("N"=>"monto_anticipo","T"=>"DOUBLE","V"=>"0.00","L"=>37),"monto_tenencia" => array("N"=>"monto_tenencia","T"=>"DOUBLE","V"=>"","L"=>37),"monto_garantia" => array("N"=>"monto_garantia","T"=>"DOUBLE","V"=>"0.00","L"=>37),"monto_mtto" => array("N"=>"monto_mtto","T"=>"DOUBLE","V"=>"0.00","L"=>37),"comision_originador" => array("N"=>"comision_originador","T"=>"FLOAT","V"=>"0.0000","L"=>13),"comision_apertura" => array("N"=>"comision_apertura","T"=>"FLOAT","V"=>"0.0000","L"=>13),"tasa_iva" => array("N"=>"tasa_iva","T"=>"FLOAT","V"=>"0.0000","L"=>13),"tasa_compra" => array("N"=>"tasa_compra","T"=>"FLOAT","V"=>"0.0000","L"=>13),"financia_seguro" => array("N"=>"financia_seguro","T"=>"INT","V"=>"0","L"=>2),"financia_tenencia" => array("N"=>"financia_tenencia","T"=>"INT","V"=>"0","L"=>2),"domicilia" => array("N"=>"domicilia","T"=>"INT","V"=>"0","L"=>2),"paso_proceso" => array("N"=>"paso_proceso","T"=>"INT","V"=>"0","L"=>4),"describe_aliado" => array("N"=>"describe_aliado","T"=>"VARCHAR","V"=>"","L"=>150),"usuario" => array("N"=>"usuario","T"=>"INT","V"=>"0","L"=>8),"nombre_cliente" => array("N"=>"nombre_cliente","T"=>"VARCHAR","V"=>"","L"=>150),"nombre_atn" => array("N"=>"nombre_atn","T"=>"VARCHAR","V"=>"","L"=>150),"oficial" => array("N"=>"oficial","T"=>"INT","V"=>"0","L"=>8),"total_credito" => array("N"=>"total_credito","T"=>"DOUBLE","V"=>"0.00","L"=>37),"segmento" => array("N"=>"segmento","T"=>"INT","V"=>"0","L"=>4),"entidadfederativa" => array("N"=>"entidadfederativa","T"=>"INT","V"=>"0","L"=>4),"plazo" => array("N"=>"plazo","T"=>"INT","V"=>"0","L"=>4),"tasa_credito" => array("N"=>"tasa_credito","T"=>"FLOAT","V"=>"0.0000","L"=>13),"tasa_tiie" => array("N"=>"tasa_tiie","T"=>"FLOAT","V"=>"0.0000","L"=>13),"monto_gps" => array("N"=>"monto_gps","T"=>"DOUBLE","V"=>"0.00","L"=>37),"monto_directo" => array("N"=>"monto_directo","T"=>"DOUBLE","V"=>"0.00","L"=>37),"monto_seguro" => array("N"=>"monto_seguro","T"=>"DOUBLE","V"=>"0.00","L"=>37),"monto_placas" => array("N"=>"monto_placas","T"=>"DOUBLE","V"=>"0.00","L"=>37),"monto_gestoria" => array("N"=>"monto_gestoria","T"=>"DOUBLE","V"=>"0.00","L"=>37),"monto_notario" => array("N"=>"monto_notario","T"=>"DOUBLE","V"=>"0.00","L"=>37),"monto_residual" => array("N"=>"monto_residual","T"=>"DOUBLE","V"=>"0.00","L"=>37),"cuota_vehiculo" => array("N"=>"cuota_vehiculo","T"=>"DOUBLE","V"=>"0.00","L"=>37),"cuota_aliado" => array("N"=>"cuota_aliado","T"=>"DOUBLE","V"=>"0.00","L"=>37),"cuota_accesorios" => array("N"=>"cuota_accesorios","T"=>"DOUBLE","V"=>"0.00","L"=>37),"cuota_tenencia" => array("N"=>"cuota_tenencia","T"=>"DOUBLE","V"=>"0.00","L"=>37),"cuota_mtto" => array("N"=>"cuota_mtto","T"=>"DOUBLE","V"=>"0.00","L"=>37),"cuota_gps" => array("N"=>"cuota_gps","T"=>"DOUBLE","V"=>"0.00","L"=>37),"cuota_seguro" => array("N"=>"cuota_seguro","T"=>"DOUBLE","V"=>"0.00","L"=>37),"monto_comision" => array("N"=>"monto_comision","T"=>"DOUBLE","V"=>"0.00","L"=>37),"monto_originador" => array("N"=>"monto_originador","T"=>"DOUBLE","V"=>"0.00","L"=>37),"cuota_garantia" => array("N"=>"cuota_garantia","T"=>"DOUBLE","V"=>"0.00","L"=>37),"es_moral" => array("N"=>"es_moral","T"=>"INT","V"=>"0","L"=>2),"estatus" => array("N"=>"estatus","T"=>"INT","V"=>"1","L"=>2),"renta_deposito" => array("N"=>"renta_deposito","T"=>"DOUBLE","V"=>"0.00","L"=>37),"renta_proporcional" => array("N"=>"renta_proporcional","T"=>"DOUBLE","V"=>"0.00","L"=>37),"renta_extra" => array("N"=>"renta_extra","T"=>"DOUBLE","V"=>"0.00","L"=>37),"residuales" => array("N"=>"residuales","T"=>"VARCHAR","V"=>"","L"=>60),"mail" => array("N"=>"mail","T"=>"VARCHAR","V"=>"","L"=>50),"tel" => array("N"=>"tel","T"=>"VARCHAR","V"=>"","L"=>15),"cuota_iva" => array("N"=>"cuota_iva","T"=>"DOUBLE","V"=>"0.00","L"=>37),"vecs" => array("N"=>"vecs","T"=>"VARCHAR","V"=>"","L"=>60),"tasas" => array("N"=>"tasas","T"=>"VARCHAR","V"=>"","L"=>60),"montoajuste" => array("N"=>"montoajuste","T"=>"FLOAT","V"=>"0.00","L"=>21),"administrado" => array("N"=>"administrado","T"=>"INT","V"=>"0","L"=>2),"opts" => array("N"=>"opts","T"=>"VARCHAR","V"=>"","L"=>100),"noivarent" => array("N"=>"noivarent","T"=>"INT","V"=>"0","L"=>2),"com_agencia" => array("N"=>"com_agencia","T"=>"FLOAT","V"=>"0.0000","L"=>13),"gps_list" => array("N"=>"gps_list","T"=>"VARCHAR","V"=>"","L"=>60),"montocom_agen" => array("N"=>"montocom_agen","T"=>"FLOAT","V"=>"0.00","L"=>21));
	public $IDORIGINACION_LEASING = "idoriginacion_leasing"; public $FECHA_ORIGEN = "fecha_origen"; public $PERSONA = "persona"; public $CREDITO = "credito"; public $MARCA = "marca"; public $MODELO = "modelo"; public $ANNIO = "annio"; public $TIPO_LEASING = "tipo_leasing"; public $TIPO_USO = "tipo_uso"; public $TIPO_RAC = "tipo_rac"; public $TIPO_GPS = "tipo_gps"; public $ORIGINADOR = "originador"; public $SUBORIGINADOR = "suboriginador"; public $PRECIO_VEHICULO = "precio_vehiculo"; public $MONTO_ALIADO = "monto_aliado"; public $MONTO_ACCESORIOS = "monto_accesorios"; public $MONTO_ANTICIPO = "monto_anticipo"; public $MONTO_TENENCIA = "monto_tenencia"; public $MONTO_GARANTIA = "monto_garantia"; public $MONTO_MTTO = "monto_mtto"; public $COMISION_ORIGINADOR = "comision_originador"; public $COMISION_APERTURA = "comision_apertura"; public $TASA_IVA = "tasa_iva"; public $TASA_COMPRA = "tasa_compra"; public $FINANCIA_SEGURO = "financia_seguro"; public $FINANCIA_TENENCIA = "financia_tenencia"; public $DOMICILIA = "domicilia"; public $PASO_PROCESO = "paso_proceso"; public $DESCRIBE_ALIADO = "describe_aliado"; public $USUARIO = "usuario"; public $NOMBRE_CLIENTE = "nombre_cliente"; public $NOMBRE_ATN = "nombre_atn"; public $OFICIAL = "oficial"; public $TOTAL_CREDITO = "total_credito"; public $SEGMENTO = "segmento"; public $ENTIDADFEDERATIVA = "entidadfederativa"; public $PLAZO = "plazo"; public $TASA_CREDITO = "tasa_credito"; public $TASA_TIIE = "tasa_tiie"; public $MONTO_GPS = "monto_gps"; public $MONTO_DIRECTO = "monto_directo"; public $MONTO_SEGURO = "monto_seguro"; public $MONTO_PLACAS = "monto_placas"; public $MONTO_GESTORIA = "monto_gestoria"; public $MONTO_NOTARIO = "monto_notario"; public $MONTO_RESIDUAL = "monto_residual"; public $CUOTA_VEHICULO = "cuota_vehiculo"; public $CUOTA_ALIADO = "cuota_aliado"; public $CUOTA_ACCESORIOS = "cuota_accesorios"; public $CUOTA_TENENCIA = "cuota_tenencia"; public $CUOTA_MTTO = "cuota_mtto"; public $CUOTA_GPS = "cuota_gps"; public $CUOTA_SEGURO = "cuota_seguro"; public $MONTO_COMISION = "monto_comision"; public $MONTO_ORIGINADOR = "monto_originador"; public $CUOTA_GARANTIA = "cuota_garantia"; public $ES_MORAL = "es_moral"; public $ESTATUS = "estatus"; public $RENTA_DEPOSITO = "renta_deposito"; public $RENTA_PROPORCIONAL = "renta_proporcional"; public $RENTA_EXTRA = "renta_extra"; public $RESIDUALES = "residuales"; public $MAIL = "mail"; public $TEL = "tel"; public $CUOTA_IVA = "cuota_iva"; public $VECS = "vecs"; public $TASAS = "tasas"; public $MONTOAJUSTE = "montoajuste"; public $ADMINISTRADO = "administrado"; public $OPTS = "opts"; public $NOIVARENT = "noivarent"; public $COM_AGENCIA = "com_agencia"; public $GPS_LIST = "gps_list"; public $MONTOCOM_AGEN = "montocom_agen";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "originacion_leasing";}
	function getKey(){ return "idoriginacion_leasing";}
	function idoriginacion_leasing($v = false){ if($v !== false){$this->mCampos["idoriginacion_leasing"]["V"] =  $v; } return new MQLCampo($this->mCampos["idoriginacion_leasing"]);}
	function fecha_origen($v = false){ if($v !== false){$this->mCampos["fecha_origen"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_origen"]);}
	function persona($v = false){ if($v !== false){$this->mCampos["persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["persona"]);}
	function credito($v = false){ if($v !== false){$this->mCampos["credito"]["V"] =  $v; } return new MQLCampo($this->mCampos["credito"]);}
	function marca($v = false){ if($v !== false){$this->mCampos["marca"]["V"] =  $v; } return new MQLCampo($this->mCampos["marca"]);}
	function modelo($v = false){ if($v !== false){$this->mCampos["modelo"]["V"] =  $v; } return new MQLCampo($this->mCampos["modelo"]);}
	function annio($v = false){ if($v !== false){$this->mCampos["annio"]["V"] =  $v; } return new MQLCampo($this->mCampos["annio"]);}
	function tipo_leasing($v = false){ if($v !== false){$this->mCampos["tipo_leasing"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_leasing"]);}
	function tipo_uso($v = false){ if($v !== false){$this->mCampos["tipo_uso"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_uso"]);}
	function tipo_rac($v = false){ if($v !== false){$this->mCampos["tipo_rac"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_rac"]);}
	function tipo_gps($v = false){ if($v !== false){$this->mCampos["tipo_gps"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_gps"]);}
	function originador($v = false){ if($v !== false){$this->mCampos["originador"]["V"] =  $v; } return new MQLCampo($this->mCampos["originador"]);}
	function suboriginador($v = false){ if($v !== false){$this->mCampos["suboriginador"]["V"] =  $v; } return new MQLCampo($this->mCampos["suboriginador"]);}
	function precio_vehiculo($v = false){ if($v !== false){$this->mCampos["precio_vehiculo"]["V"] =  $v; } return new MQLCampo($this->mCampos["precio_vehiculo"]);}
	function monto_aliado($v = false){ if($v !== false){$this->mCampos["monto_aliado"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_aliado"]);}
	function monto_accesorios($v = false){ if($v !== false){$this->mCampos["monto_accesorios"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_accesorios"]);}
	function monto_anticipo($v = false){ if($v !== false){$this->mCampos["monto_anticipo"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_anticipo"]);}
	function monto_tenencia($v = false){ if($v !== false){$this->mCampos["monto_tenencia"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_tenencia"]);}
	function monto_garantia($v = false){ if($v !== false){$this->mCampos["monto_garantia"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_garantia"]);}
	function monto_mtto($v = false){ if($v !== false){$this->mCampos["monto_mtto"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_mtto"]);}
	function comision_originador($v = false){ if($v !== false){$this->mCampos["comision_originador"]["V"] =  $v; } return new MQLCampo($this->mCampos["comision_originador"]);}
	function comision_apertura($v = false){ if($v !== false){$this->mCampos["comision_apertura"]["V"] =  $v; } return new MQLCampo($this->mCampos["comision_apertura"]);}
	function tasa_iva($v = false){ if($v !== false){$this->mCampos["tasa_iva"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_iva"]);}
	function tasa_compra($v = false){ if($v !== false){$this->mCampos["tasa_compra"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_compra"]);}
	function financia_seguro($v = false){ if($v !== false){$this->mCampos["financia_seguro"]["V"] =  $v; } return new MQLCampo($this->mCampos["financia_seguro"]);}
	function financia_tenencia($v = false){ if($v !== false){$this->mCampos["financia_tenencia"]["V"] =  $v; } return new MQLCampo($this->mCampos["financia_tenencia"]);}
	function domicilia($v = false){ if($v !== false){$this->mCampos["domicilia"]["V"] =  $v; } return new MQLCampo($this->mCampos["domicilia"]);}
	function paso_proceso($v = false){ if($v !== false){$this->mCampos["paso_proceso"]["V"] =  $v; } return new MQLCampo($this->mCampos["paso_proceso"]);}
	function describe_aliado($v = false){ if($v !== false){$this->mCampos["describe_aliado"]["V"] =  $v; } return new MQLCampo($this->mCampos["describe_aliado"]);}
	function usuario($v = false){ if($v !== false){$this->mCampos["usuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["usuario"]);}
	function nombre_cliente($v = false){ if($v !== false){$this->mCampos["nombre_cliente"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_cliente"]);}
	function nombre_atn($v = false){ if($v !== false){$this->mCampos["nombre_atn"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_atn"]);}
	function oficial($v = false){ if($v !== false){$this->mCampos["oficial"]["V"] =  $v; } return new MQLCampo($this->mCampos["oficial"]);}
	function total_credito($v = false){ if($v !== false){$this->mCampos["total_credito"]["V"] =  $v; } return new MQLCampo($this->mCampos["total_credito"]);}
	function segmento($v = false){ if($v !== false){$this->mCampos["segmento"]["V"] =  $v; } return new MQLCampo($this->mCampos["segmento"]);}
	function entidadfederativa($v = false){ if($v !== false){$this->mCampos["entidadfederativa"]["V"] =  $v; } return new MQLCampo($this->mCampos["entidadfederativa"]);}
	function plazo($v = false){ if($v !== false){$this->mCampos["plazo"]["V"] =  $v; } return new MQLCampo($this->mCampos["plazo"]);}
	function tasa_credito($v = false){ if($v !== false){$this->mCampos["tasa_credito"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_credito"]);}
	function tasa_tiie($v = false){ if($v !== false){$this->mCampos["tasa_tiie"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_tiie"]);}
	function monto_gps($v = false){ if($v !== false){$this->mCampos["monto_gps"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_gps"]);}
	function monto_directo($v = false){ if($v !== false){$this->mCampos["monto_directo"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_directo"]);}
	function monto_seguro($v = false){ if($v !== false){$this->mCampos["monto_seguro"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_seguro"]);}
	function monto_placas($v = false){ if($v !== false){$this->mCampos["monto_placas"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_placas"]);}
	function monto_gestoria($v = false){ if($v !== false){$this->mCampos["monto_gestoria"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_gestoria"]);}
	function monto_notario($v = false){ if($v !== false){$this->mCampos["monto_notario"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_notario"]);}
	function monto_residual($v = false){ if($v !== false){$this->mCampos["monto_residual"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_residual"]);}
	function cuota_vehiculo($v = false){ if($v !== false){$this->mCampos["cuota_vehiculo"]["V"] =  $v; } return new MQLCampo($this->mCampos["cuota_vehiculo"]);}
	function cuota_aliado($v = false){ if($v !== false){$this->mCampos["cuota_aliado"]["V"] =  $v; } return new MQLCampo($this->mCampos["cuota_aliado"]);}
	function cuota_accesorios($v = false){ if($v !== false){$this->mCampos["cuota_accesorios"]["V"] =  $v; } return new MQLCampo($this->mCampos["cuota_accesorios"]);}
	function cuota_tenencia($v = false){ if($v !== false){$this->mCampos["cuota_tenencia"]["V"] =  $v; } return new MQLCampo($this->mCampos["cuota_tenencia"]);}
	function cuota_mtto($v = false){ if($v !== false){$this->mCampos["cuota_mtto"]["V"] =  $v; } return new MQLCampo($this->mCampos["cuota_mtto"]);}
	function cuota_gps($v = false){ if($v !== false){$this->mCampos["cuota_gps"]["V"] =  $v; } return new MQLCampo($this->mCampos["cuota_gps"]);}
	function cuota_seguro($v = false){ if($v !== false){$this->mCampos["cuota_seguro"]["V"] =  $v; } return new MQLCampo($this->mCampos["cuota_seguro"]);}
	function monto_comision($v = false){ if($v !== false){$this->mCampos["monto_comision"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_comision"]);}
	function monto_originador($v = false){ if($v !== false){$this->mCampos["monto_originador"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_originador"]);}
	function cuota_garantia($v = false){ if($v !== false){$this->mCampos["cuota_garantia"]["V"] =  $v; } return new MQLCampo($this->mCampos["cuota_garantia"]);}
	function es_moral($v = false){ if($v !== false){$this->mCampos["es_moral"]["V"] =  $v; } return new MQLCampo($this->mCampos["es_moral"]);}
	function estatus($v = false){ if($v !== false){$this->mCampos["estatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus"]);}
	function renta_deposito($v = false){ if($v !== false){$this->mCampos["renta_deposito"]["V"] =  $v; } return new MQLCampo($this->mCampos["renta_deposito"]);}
	function renta_proporcional($v = false){ if($v !== false){$this->mCampos["renta_proporcional"]["V"] =  $v; } return new MQLCampo($this->mCampos["renta_proporcional"]);}
	function renta_extra($v = false){ if($v !== false){$this->mCampos["renta_extra"]["V"] =  $v; } return new MQLCampo($this->mCampos["renta_extra"]);}
	function residuales($v = false){ if($v !== false){$this->mCampos["residuales"]["V"] =  $v; } return new MQLCampo($this->mCampos["residuales"]);}
	function mail($v = false){ if($v !== false){$this->mCampos["mail"]["V"] =  $v; } return new MQLCampo($this->mCampos["mail"]);}
	function tel($v = false){ if($v !== false){$this->mCampos["tel"]["V"] =  $v; } return new MQLCampo($this->mCampos["tel"]);}
	function cuota_iva($v = false){ if($v !== false){$this->mCampos["cuota_iva"]["V"] =  $v; } return new MQLCampo($this->mCampos["cuota_iva"]);}
	function vecs($v = false){ if($v !== false){$this->mCampos["vecs"]["V"] =  $v; } return new MQLCampo($this->mCampos["vecs"]);}
	function tasas($v = false){ if($v !== false){$this->mCampos["tasas"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasas"]);}
	function montoajuste($v = false){ if($v !== false){$this->mCampos["montoajuste"]["V"] =  $v; } return new MQLCampo($this->mCampos["montoajuste"]);}
	function administrado($v = false){ if($v !== false){$this->mCampos["administrado"]["V"] =  $v; } return new MQLCampo($this->mCampos["administrado"]);}
	function opts($v = false){ if($v !== false){$this->mCampos["opts"]["V"] =  $v; } return new MQLCampo($this->mCampos["opts"]);}
	function noivarent($v = false){ if($v !== false){$this->mCampos["noivarent"]["V"] =  $v; } return new MQLCampo($this->mCampos["noivarent"]);}
	function com_agencia($v = false){ if($v !== false){$this->mCampos["com_agencia"]["V"] =  $v; } return new MQLCampo($this->mCampos["com_agencia"]);}
	function gps_list($v = false){ if($v !== false){$this->mCampos["gps_list"]["V"] =  $v; } return new MQLCampo($this->mCampos["gps_list"]);}
	function montocom_agen($v = false){ if($v !== false){$this->mCampos["montocom_agen"]["V"] =  $v; } return new MQLCampo($this->mCampos["montocom_agen"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
}
/*	ORM: Tabla:	originacion_requisitos	-	Generado:	[27/1/2017 18:31]	*/
class cOriginacion_requisitos {
	private $mCampos	= array("idoriginacion_requisitos" => array("N"=>"idoriginacion_requisitos","T"=>"INT","V"=>"","L"=>11),"requisito" => array("N"=>"requisito","T"=>"INT","V"=>"","L"=>11),"ruta" => array("N"=>"ruta","T"=>"VARCHAR","V"=>"","L"=>150),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "originacion_requisitos";}
	function getKey(){ return "idoriginacion_requisitos";}
	function idoriginacion_requisitos($v = false){ if($v !== false){$this->mCampos["idoriginacion_requisitos"]["V"] =  $v; } return new MQLCampo($this->mCampos["idoriginacion_requisitos"]);}
	function requisito($v = false){ if($v !== false){$this->mCampos["requisito"]["V"] =  $v; } return new MQLCampo($this->mCampos["requisito"]);}
	function ruta($v = false){ if($v !== false){$this->mCampos["ruta"]["V"] =  $v; } return new MQLCampo($this->mCampos["ruta"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	leasing_financiero	-	Generado:	[11/10/2016 17:52]	*/
class cLeasing_financiero {
	private $mCampos	= array("idleasing_financiero" => array("N"=>"idleasing_financiero","T"=>"INT","V"=>"","L"=>11),"describe_financiero" => array("N"=>"describe_financiero","T"=>"VARCHAR","V"=>"","L"=>40),"frecuencia" => array("N"=>"frecuencia","T"=>"INT","V"=>"30","L"=>4),"limite_inferior" => array("N"=>"limite_inferior","T"=>"INT","V"=>"0","L"=>4),"limite_superior" => array("N"=>"limite_superior","T"=>"INT","V"=>"0","L"=>4),"tasa_financiero" => array("N"=>"tasa_financiero","T"=>"FLOAT","V"=>"0.0000","L"=>13),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "leasing_financiero";}
	function getKey(){ return "idleasing_financiero";}
	function idleasing_financiero($v = false){ if($v !== false){$this->mCampos["idleasing_financiero"]["V"] =  $v; } return new MQLCampo($this->mCampos["idleasing_financiero"]);}
	function describe_financiero($v = false){ if($v !== false){$this->mCampos["describe_financiero"]["V"] =  $v; } return new MQLCampo($this->mCampos["describe_financiero"]);}
	function frecuencia($v = false){ if($v !== false){$this->mCampos["frecuencia"]["V"] =  $v; } return new MQLCampo($this->mCampos["frecuencia"]);}
	function limite_inferior($v = false){ if($v !== false){$this->mCampos["limite_inferior"]["V"] =  $v; } return new MQLCampo($this->mCampos["limite_inferior"]);}
	function limite_superior($v = false){ if($v !== false){$this->mCampos["limite_superior"]["V"] =  $v; } return new MQLCampo($this->mCampos["limite_superior"]);}
	function tasa_financiero($v = false){ if($v !== false){$this->mCampos["tasa_financiero"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_financiero"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}
/*	ORM: Tabla:	leasing_rentas	-	Generado:	[07/6/2017 17:02]	*/
/*	ORM: Tabla:	leasing_rentas	-	Generado:	[13/6/2017 16:04]	*/
class cLeasing_rentas {
	private $mCampos	= array("idleasing_renta" => array("N"=>"idleasing_renta","T"=>"INT","V"=>"","L"=>11),"clave_leasing" => array("N"=>"clave_leasing","T"=>"INT","V"=>"","L"=>11),"credito" => array("N"=>"credito","T"=>"BIGINT","V"=>"","L"=>20),"periodo" => array("N"=>"periodo","T"=>"INT","V"=>"","L"=>4),"fecha" => array("N"=>"fecha","T"=>"DATE","V"=>"2017-01-01","L"=>0),"deducible" => array("N"=>"deducible","T"=>"DOUBLE","V"=>"0.00","L"=>37),"no_deducible" => array("N"=>"no_deducible","T"=>"DOUBLE","V"=>"0.00","L"=>37),"iva_ded" => array("N"=>"iva_ded","T"=>"DOUBLE","V"=>"0.00","L"=>37),"iva_no_ded" => array("N"=>"iva_no_ded","T"=>"DOUBLE","V"=>"0.00","L"=>37),"total" => array("N"=>"total","T"=>"DOUBLE","V"=>"0.00","L"=>37),"fecha_max" => array("N"=>"fecha_max","T"=>"DATE","V"=>"","L"=>0),"clave_no_ded" => array("N"=>"clave_no_ded","T"=>"INT","V"=>"99","L"=>8),"fecha_pago" => array("N"=>"fecha_pago","T"=>"DATE","V"=>"2017-01-01","L"=>0),"recibo_pago" => array("N"=>"recibo_pago","T"=>"BIGINT","V"=>"1","L"=>20),"suma_pagos" => array("N"=>"suma_pagos","T"=>"DOUBLE","V"=>"0.00","L"=>37),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "leasing_rentas";}
	function getKey(){ return "idleasing_renta";}
	function idleasing_renta($v = false){ if($v !== false){$this->mCampos["idleasing_renta"]["V"] =  $v; } return new MQLCampo($this->mCampos["idleasing_renta"]);}
	function clave_leasing($v = false){ if($v !== false){$this->mCampos["clave_leasing"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_leasing"]);}
	function credito($v = false){ if($v !== false){$this->mCampos["credito"]["V"] =  $v; } return new MQLCampo($this->mCampos["credito"]);}
	function periodo($v = false){ if($v !== false){$this->mCampos["periodo"]["V"] =  $v; } return new MQLCampo($this->mCampos["periodo"]);}
	function fecha($v = false){ if($v !== false){$this->mCampos["fecha"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha"]);}
	function deducible($v = false){ if($v !== false){$this->mCampos["deducible"]["V"] =  $v; } return new MQLCampo($this->mCampos["deducible"]);}
	function no_deducible($v = false){ if($v !== false){$this->mCampos["no_deducible"]["V"] =  $v; } return new MQLCampo($this->mCampos["no_deducible"]);}
	function iva_ded($v = false){ if($v !== false){$this->mCampos["iva_ded"]["V"] =  $v; } return new MQLCampo($this->mCampos["iva_ded"]);}
	function iva_no_ded($v = false){ if($v !== false){$this->mCampos["iva_no_ded"]["V"] =  $v; } return new MQLCampo($this->mCampos["iva_no_ded"]);}
	function total($v = false){ if($v !== false){$this->mCampos["total"]["V"] =  $v; } return new MQLCampo($this->mCampos["total"]);}
	function fecha_max($v = false){ if($v !== false){$this->mCampos["fecha_max"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_max"]);}
	function clave_no_ded($v = false){ if($v !== false){$this->mCampos["clave_no_ded"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_no_ded"]);}
	function fecha_pago($v = false){ if($v !== false){$this->mCampos["fecha_pago"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_pago"]);}
	function recibo_pago($v = false){ if($v !== false){$this->mCampos["recibo_pago"]["V"] =  $v; } return new MQLCampo($this->mCampos["recibo_pago"]);}
	function suma_pagos($v = false){ if($v !== false){$this->mCampos["suma_pagos"]["V"] =  $v; } return new MQLCampo($this->mCampos["suma_pagos"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
}

/*	ORM: Tabla:	vehiculos_usos	-	Generado:	[06/10/2016 16:58]	*/
/*	ORM: Tabla:	vehiculos_usos	-	Generado:	[27/2/2018 12:01]	*/
class cVehiculos_usos {
	private $mCampos	= array("idvehiculos_usos" => array("N"=>"idvehiculos_usos","T"=>"INT","V"=>"","L"=>11),"descripcion_uso" => array("N"=>"descripcion_uso","T"=>"VARCHAR","V"=>"","L"=>50),"limitededucible" => array("N"=>"limitededucible","T"=>"FLOAT","V"=>"0.00","L"=>25));
	public $IDVEHICULOS_USOS = "idvehiculos_usos"; public $DESCRIPCION_USO = "descripcion_uso"; public $LIMITEDEDUCIBLE = "limitededucible";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "vehiculos_usos";}
	function getKey(){ return "idvehiculos_usos";}
	function idvehiculos_usos($v = false){ if($v !== false){$this->mCampos["idvehiculos_usos"]["V"] =  $v; } return new MQLCampo($this->mCampos["idvehiculos_usos"]);}
	function descripcion_uso($v = false){ if($v !== false){$this->mCampos["descripcion_uso"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_uso"]);}
	function limitededucible($v = false){ if($v !== false){$this->mCampos["limitededucible"]["V"] =  $v; } return new MQLCampo($this->mCampos["limitededucible"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
}
/*	ORM: Tabla:	creditos_notarios	-	Generado:	[06/10/2016 17:33]	*/
class cCreditos_notarios {
	private $mCampos	= array("idcreditos_notarios" => array("N"=>"idcreditos_notarios","T"=>"INT","V"=>"","L"=>11),"nombre_notario" => array("N"=>"nombre_notario","T"=>"VARCHAR","V"=>"","L"=>100),"direccion" => array("N"=>"direccion","T"=>"VARCHAR","V"=>"","L"=>150),"numero_notario" => array("N"=>"numero_notario","T"=>"VARCHAR","V"=>"","L"=>4),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_notarios";}
	function getKey(){ return "idcreditos_notarios";}
	function idcreditos_notarios($v = false){ if($v !== false){$this->mCampos["idcreditos_notarios"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_notarios"]);}
	function nombre_notario($v = false){ if($v !== false){$this->mCampos["nombre_notario"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_notario"]);}
	function direccion($v = false){ if($v !== false){$this->mCampos["direccion"]["V"] =  $v; } return new MQLCampo($this->mCampos["direccion"]);}
	function numero_notario($v = false){ if($v !== false){$this->mCampos["numero_notario"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_notario"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}
/*	ORM: Tabla:	vehiculos_gps_costeo	-	Generado:	[10/10/2016 17:03]	*/
/*	ORM: Tabla:	vehiculos_gps_costeo	-	Generado:	[12/10/2016 17:55]	*/
class cVehiculos_gps_costeo {
	private $mCampos	= array("idvehiculos_gps_costeo" => array("N"=>"idvehiculos_gps_costeo","T"=>"INT","V"=>"","L"=>11),"limite_inferior" => array("N"=>"limite_inferior","T"=>"INT","V"=>"0","L"=>4),"limite_superior" => array("N"=>"limite_superior","T"=>"INT","V"=>"0","L"=>4),"monto_gps" => array("N"=>"monto_gps","T"=>"DOUBLE","V"=>"0.00","L"=>37),"tipo_de_gps" => array("N"=>"tipo_de_gps","T"=>"INT","V"=>"0","L"=>4),"frecuencia" => array("N"=>"frecuencia","T"=>"INT","V"=>"30","L"=>4),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "vehiculos_gps_costeo";}
	function getKey(){ return "idvehiculos_gps_costeo";}
	function idvehiculos_gps_costeo($v = false){ if($v !== false){$this->mCampos["idvehiculos_gps_costeo"]["V"] =  $v; } return new MQLCampo($this->mCampos["idvehiculos_gps_costeo"]);}
	function limite_inferior($v = false){ if($v !== false){$this->mCampos["limite_inferior"]["V"] =  $v; } return new MQLCampo($this->mCampos["limite_inferior"]);}
	function limite_superior($v = false){ if($v !== false){$this->mCampos["limite_superior"]["V"] =  $v; } return new MQLCampo($this->mCampos["limite_superior"]);}
	function monto_gps($v = false){ if($v !== false){$this->mCampos["monto_gps"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_gps"]);}
	function tipo_de_gps($v = false){ if($v !== false){$this->mCampos["tipo_de_gps"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_gps"]);}
	function frecuencia($v = false){ if($v !== false){$this->mCampos["frecuencia"]["V"] =  $v; } return new MQLCampo($this->mCampos["frecuencia"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

//=====================================================================================================================

/*	ORM: Tabla:	socios_aeconomica	-	Generado:	[12/5/2016 11:35]	*/
/*	ORM: Tabla:	socios_aeconomica	-	Generado:	[08/4/2017 14:14]	*/
/*	ORM: Tabla:	socios_aeconomica	-	Generado:	[22/11/2017 17:18]	*/
class cSocios_aeconomica {
	private $mCampos	= array("idsocios_aeconomica" => array("N"=>"idsocios_aeconomica","T"=>"INT","V"=>"","L"=>10),"socio_aeconomica" => array("N"=>"socio_aeconomica","T"=>"BIGINT","V"=>"0","L"=>20),"tipo_aeconomica" => array("N"=>"tipo_aeconomica","T"=>"BIGINT","V"=>"99","L"=>20),"sector_economico" => array("N"=>"sector_economico","T"=>"BIGINT","V"=>"99","L"=>20),"nombre_ae" => array("N"=>"nombre_ae","T"=>"VARCHAR","V"=>"","L"=>100),"domicilio_ae" => array("N"=>"domicilio_ae","T"=>"VARCHAR","V"=>"","L"=>100),"localidad_ae" => array("N"=>"localidad_ae","T"=>"VARCHAR","V"=>"","L"=>50),"municipio_ae" => array("N"=>"municipio_ae","T"=>"VARCHAR","V"=>"","L"=>50),"estado_ae" => array("N"=>"estado_ae","T"=>"VARCHAR","V"=>"","L"=>40),"telefono_ae" => array("N"=>"telefono_ae","T"=>"VARCHAR","V"=>"","L"=>18),"extension_ae" => array("N"=>"extension_ae","T"=>"VARCHAR","V"=>"","L"=>10),"numero_empleado" => array("N"=>"numero_empleado","T"=>"VARCHAR","V"=>"","L"=>10),"antiguedad_ae" => array("N"=>"antiguedad_ae","T"=>"INT","V"=>"0","L"=>10),"departamento_ae" => array("N"=>"departamento_ae","T"=>"VARCHAR","V"=>"","L"=>45),"monto_percibido_ae" => array("N"=>"monto_percibido_ae","T"=>"DOUBLE","V"=>"0.00","L"=>33),"dependencia_ae" => array("N"=>"dependencia_ae","T"=>"INT","V"=>"99","L"=>10),"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"99","L"=>8),"fecha_alta" => array("N"=>"fecha_alta","T"=>"DATE","V"=>"","L"=>0),"puesto" => array("N"=>"puesto","T"=>"VARCHAR","V"=>"NA","L"=>65),"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"MATRIZ","L"=>20),"fecha_de_verificacion" => array("N"=>"fecha_de_verificacion","T"=>"DATE","V"=>"2012-01-01","L"=>0),"oficial_de_verificacion" => array("N"=>"oficial_de_verificacion","T"=>"INT","V"=>"1","L"=>10),"estado_actual" => array("N"=>"estado_actual","T"=>"INT","V"=>"99","L"=>3),"numero_de_seguridad_social" => array("N"=>"numero_de_seguridad_social","T"=>"VARCHAR","V"=>"","L"=>20),"domicilio_vinculado" => array("N"=>"domicilio_vinculado","T"=>"INT","V"=>"1","L"=>11),"ae_clave_de_localidad" => array("N"=>"ae_clave_de_localidad","T"=>"BIGINT","V"=>"0","L"=>20),"ae_codigo_postal" => array("N"=>"ae_codigo_postal","T"=>"INT","V"=>"0","L"=>8),"notas_de_verificacion" => array("N"=>"notas_de_verificacion","T"=>"VARCHAR","V"=>"","L"=>150),"fecha_de_ingreso" => array("N"=>"fecha_de_ingreso","T"=>"DATE","V"=>"0000-00-00","L"=>0),"empleado_tipo_de_dispersion" => array("N"=>"empleado_tipo_de_dispersion","T"=>"INT","V"=>"100","L"=>4),"clave_scian" => array("N"=>"clave_scian","T"=>"BIGINT","V"=>"0","L"=>20),"descripcion" => array("N"=>"descripcion","T"=>"VARCHAR","V"=>"","L"=>100));
	public $IDSOCIOS_AECONOMICA	= "idsocios_aeconomica";
	public $SOCIO_AECONOMICA	= "socio_aeconomica";
	public $TIPO_AECONOMICA	= "tipo_aeconomica";
	public $SECTOR_ECONOMICO	= "sector_economico";
	public $NOMBRE_AE	= "nombre_ae";
	public $DOMICILIO_AE	= "domicilio_ae";
	public $LOCALIDAD_AE	= "localidad_ae";
	public $MUNICIPIO_AE	= "municipio_ae";
	public $ESTADO_AE	= "estado_ae";
	public $TELEFONO_AE	= "telefono_ae";
	public $EXTENSION_AE	= "extension_ae";
	public $NUMERO_EMPLEADO	= "numero_empleado";
	public $ANTIGUEDAD_AE	= "antiguedad_ae";
	public $DEPARTAMENTO_AE	= "departamento_ae";
	public $MONTO_PERCIBIDO_AE	= "monto_percibido_ae";
	public $DEPENDENCIA_AE	= "dependencia_ae";
	public $IDUSUARIO	= "idusuario";
	public $FECHA_ALTA	= "fecha_alta";
	public $PUESTO	= "puesto";
	public $SUCURSAL	= "sucursal";
	public $FECHA_DE_VERIFICACION	= "fecha_de_verificacion";
	public $OFICIAL_DE_VERIFICACION	= "oficial_de_verificacion";
	public $ESTADO_ACTUAL	= "estado_actual";
	public $NUMERO_DE_SEGURIDAD_SOCIAL	= "numero_de_seguridad_social";
	public $DOMICILIO_VINCULADO	= "domicilio_vinculado";
	public $AE_CLAVE_DE_LOCALIDAD	= "ae_clave_de_localidad";
	public $AE_CODIGO_POSTAL	= "ae_codigo_postal";
	public $NOTAS_DE_VERIFICACION	= "notas_de_verificacion";
	public $FECHA_DE_INGRESO	= "fecha_de_ingreso";
	public $EMPLEADO_TIPO_DE_DISPERSION	= "empleado_tipo_de_dispersion";
	public $CLAVE_SCIAN	= "clave_scian";
	public $DESCRIPCION	= "descripcion";
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
	function notas_de_verificacion($v = false){ if($v !== false){$this->mCampos["notas_de_verificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["notas_de_verificacion"]);}
	function fecha_de_ingreso($v = false){ if($v !== false){$this->mCampos["fecha_de_ingreso"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_ingreso"]);}
	function empleado_tipo_de_dispersion($v = false){ if($v !== false){$this->mCampos["empleado_tipo_de_dispersion"]["V"] =  $v; } return new MQLCampo($this->mCampos["empleado_tipo_de_dispersion"]);}
	function clave_scian($v = false){ if($v !== false){$this->mCampos["clave_scian"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_scian"]);}
	function descripcion($v = false){ if($v !== false){$this->mCampos["descripcion"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}

/*	ORM: Tabla:	socios_aeconomica_sector	-	Generado:	[08/4/2017 14:15]	*/
class cSocios_aeconomica_sector {
	private $mCampos	= array("idsocios_aeconomica_sector" => array("N"=>"idsocios_aeconomica_sector","T"=>"INT","V"=>"","L"=>4),"descripcion_aeconomica_sector" => array("N"=>"descripcion_aeconomica_sector","T"=>"VARCHAR","V"=>"","L"=>100),"tags" => array("N"=>"tags","T"=>"VARCHAR","V"=>"","L"=>20),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "socios_aeconomica_sector";}
	function getKey(){ return "idsocios_aeconomica_sector";}
	function idsocios_aeconomica_sector($v = false){ if($v !== false){$this->mCampos["idsocios_aeconomica_sector"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsocios_aeconomica_sector"]);}
	function descripcion_aeconomica_sector($v = false){ if($v !== false){$this->mCampos["descripcion_aeconomica_sector"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_aeconomica_sector"]);}
	function tags($v = false){ if($v !== false){$this->mCampos["tags"]["V"] =  $v; } return new MQLCampo($this->mCampos["tags"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}

/*	ORM: Tabla:	socios_vivienda	-	Generado:	[10/2/2016 11:31]	*/
/*	ORM: Tabla:	socios_vivienda	-	Generado:	[28/8/2017 15:18]	*/
/*	ORM: Tabla:	socios_vivienda	-	Generado:	[22/11/2017 16:39]	*/
class cSocios_vivienda {
	private $mCampos	= array("idsocios_vivienda" => array("N"=>"idsocios_vivienda","T"=>"INT","V"=>"","L"=>10),"socio_numero" => array("N"=>"socio_numero","T"=>"BIGINT","V"=>"1","L"=>20),"tipo_regimen" => array("N"=>"tipo_regimen","T"=>"INT","V"=>"99","L"=>4),"calle" => array("N"=>"calle","T"=>"VARCHAR","V"=>"","L"=>60),"numero_exterior" => array("N"=>"numero_exterior","T"=>"VARCHAR","V"=>"","L"=>45),"numero_interior" => array("N"=>"numero_interior","T"=>"VARCHAR","V"=>"","L"=>45),"colonia" => array("N"=>"colonia","T"=>"VARCHAR","V"=>"","L"=>150),"localidad" => array("N"=>"localidad","T"=>"VARCHAR","V"=>"","L"=>100),"estado" => array("N"=>"estado","T"=>"VARCHAR","V"=>"","L"=>100),"municipio" => array("N"=>"municipio","T"=>"VARCHAR","V"=>"","L"=>100),"telefono_residencial" => array("N"=>"telefono_residencial","T"=>"VARCHAR","V"=>"","L"=>20),"telefono_movil" => array("N"=>"telefono_movil","T"=>"VARCHAR","V"=>"","L"=>20),"tiempo_residencia" => array("N"=>"tiempo_residencia","T"=>"INT","V"=>"99","L"=>10),"referencia" => array("N"=>"referencia","T"=>"VARCHAR","V"=>"","L"=>200),"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"99","L"=>4),"principal" => array("N"=>"principal","T"=>"ENUM","V"=>"|0|1|","L"=>0),"tipo_domicilio" => array("N"=>"tipo_domicilio","T"=>"INT","V"=>"99","L"=>4),"codigo_postal" => array("N"=>"codigo_postal","T"=>"INT","V"=>"24000","L"=>11),"fecha_alta" => array("N"=>"fecha_alta","T"=>"DATE","V"=>"2005-12-31","L"=>0),"codigo" => array("N"=>"codigo","T"=>"BIGINT","V"=>"1","L"=>20),"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"MATRIZ","L"=>20),"eacp" => array("N"=>"eacp","T"=>"VARCHAR","V"=>"EN_TRAMITE","L"=>20),"coordenadas_gps" => array("N"=>"coordenadas_gps","T"=>"VARCHAR","V"=>"00,00,00","L"=>20),"tipo_de_acceso" => array("N"=>"tipo_de_acceso","T"=>"VARCHAR","V"=>"calle","L"=>20),"fecha_de_verificacion" => array("N"=>"fecha_de_verificacion","T"=>"DATE","V"=>"2012-01-01","L"=>0),"oficial_de_verificacion" => array("N"=>"oficial_de_verificacion","T"=>"INT","V"=>"1","L"=>10),"estado_actual" => array("N"=>"estado_actual","T"=>"INT","V"=>"99","L"=>3),"clave_de_localidad" => array("N"=>"clave_de_localidad","T"=>"INT","V"=>"","L"=>10),"clave_de_pais" => array("N"=>"clave_de_pais","T"=>"VARCHAR","V"=>"MX","L"=>10),"nombre_de_pais" => array("N"=>"nombre_de_pais","T"=>"VARCHAR","V"=>"Mexico","L"=>100),"clave_de_municipio" => array("N"=>"clave_de_municipio","T"=>"INT","V"=>"0","L"=>6),"clave_de_entidadfederativa" => array("N"=>"clave_de_entidadfederativa","T"=>"INT","V"=>"0","L"=>6),"construye" => array("N"=>"construye","T"=>"INT","V"=>"0","L"=>2));
	public $IDSOCIOS_VIVIENDA	= "idsocios_vivienda";
	public $SOCIO_NUMERO	= "socio_numero";
	public $TIPO_REGIMEN	= "tipo_regimen";
	public $CALLE	= "calle";
	public $NUMERO_EXTERIOR	= "numero_exterior";
	public $NUMERO_INTERIOR	= "numero_interior";
	public $COLONIA	= "colonia";
	public $LOCALIDAD	= "localidad";
	public $ESTADO	= "estado";
	public $MUNICIPIO	= "municipio";
	public $TELEFONO_RESIDENCIAL	= "telefono_residencial";
	public $TELEFONO_MOVIL	= "telefono_movil";
	public $TIEMPO_RESIDENCIA	= "tiempo_residencia";
	public $REFERENCIA	= "referencia";
	public $IDUSUARIO	= "idusuario";
	public $PRINCIPAL	= "principal";
	public $TIPO_DOMICILIO	= "tipo_domicilio";
	public $CODIGO_POSTAL	= "codigo_postal";
	public $FECHA_ALTA	= "fecha_alta";
	public $CODIGO	= "codigo";
	public $SUCURSAL	= "sucursal";
	public $EACP	= "eacp";
	public $COORDENADAS_GPS	= "coordenadas_gps";
	public $TIPO_DE_ACCESO	= "tipo_de_acceso";
	public $FECHA_DE_VERIFICACION	= "fecha_de_verificacion";
	public $OFICIAL_DE_VERIFICACION	= "oficial_de_verificacion";
	public $ESTADO_ACTUAL	= "estado_actual";
	public $CLAVE_DE_LOCALIDAD	= "clave_de_localidad";
	public $CLAVE_DE_PAIS	= "clave_de_pais";
	public $NOMBRE_DE_PAIS	= "nombre_de_pais";
	public $CLAVE_DE_MUNICIPIO	= "clave_de_municipio";
	public $CLAVE_DE_ENTIDADFEDERATIVA	= "clave_de_entidadfederativa";
	public $CONSTRUYE	= "construye";
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
	function clave_de_municipio($v = false){ if($v !== false){$this->mCampos["clave_de_municipio"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_municipio"]);}
	function clave_de_entidadfederativa($v = false){ if($v !== false){$this->mCampos["clave_de_entidadfederativa"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_entidadfederativa"]);}
	function construye($v = false){ if($v !== false){$this->mCampos["construye"]["V"] =  $v; } return new MQLCampo($this->mCampos["construye"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}

/*	ORM: Tabla:	socios_baja	-	Generado:	[26/12/2014 12:56]	*/
/*	ORM: Tabla:	socios_baja	-	Generado:	[04/4/2018 16:12]	*/
class cSocios_baja {
	private $mCampos	= array("idsocios_baja" => array("N"=>"idsocios_baja","T"=>"INT","V"=>"","L"=>10),"numero_de_socio" => array("N"=>"numero_de_socio","T"=>"BIGINT","V"=>"","L"=>20),"fecha_de_baja" => array("N"=>"fecha_de_baja","T"=>"DATE","V"=>"","L"=>0),"razon_de_la_baja" => array("N"=>"razon_de_la_baja","T"=>"INT","V"=>"","L"=>10),"observaciones_de_baja" => array("N"=>"observaciones_de_baja","T"=>"VARCHAR","V"=>"","L"=>100),"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"matriz","L"=>20),"docto_presentado" => array("N"=>"docto_presentado","T"=>"VARCHAR","V"=>"ninguno","L"=>40),"fecha_de_documento" => array("N"=>"fecha_de_documento","T"=>"DATE","V"=>"","L"=>0),"fecha_de_vencimiento" => array("N"=>"fecha_de_vencimiento","T"=>"DATE","V"=>"","L"=>0));
	public $IDSOCIOS_BAJA = "idsocios_baja"; public $NUMERO_DE_SOCIO = "numero_de_socio"; public $FECHA_DE_BAJA = "fecha_de_baja"; public $RAZON_DE_LA_BAJA = "razon_de_la_baja"; public $OBSERVACIONES_DE_BAJA = "observaciones_de_baja"; public $SUCURSAL = "sucursal"; public $DOCTO_PRESENTADO = "docto_presentado"; public $FECHA_DE_DOCUMENTO = "fecha_de_documento"; public $FECHA_DE_VENCIMIENTO = "fecha_de_vencimiento";
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
/*	ORM: Tabla:	socios_baja_razones	-	Generado:	[04/4/2018 16:13]	*/
class cSocios_baja_razones {
	private $mCampos	= array("idsocios_baja_razones" => array("N"=>"idsocios_baja_razones","T"=>"INT","V"=>"","L"=>10),"descripcion_razon_de_baja" => array("N"=>"descripcion_razon_de_baja","T"=>"VARCHAR","V"=>"","L"=>60),"destino_de_derechos" => array("N"=>"destino_de_derechos","T"=>"ENUM","V"=>"|entidad|beneficiarios|gastos|ingresos|concentradora|","L"=>0),"numero_de_forma" => array("N"=>"numero_de_forma","T"=>"INT","V"=>"0","L"=>4));
	public $IDSOCIOS_BAJA_RAZONES = "idsocios_baja_razones"; public $DESCRIPCION_RAZON_DE_BAJA = "descripcion_razon_de_baja"; public $DESTINO_DE_DERECHOS = "destino_de_derechos"; public $NUMERO_DE_FORMA = "numero_de_forma";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "socios_baja_razones";}
	function getKey(){ return "idsocios_baja_razones";}
	function idsocios_baja_razones($v = false){ if($v !== false){$this->mCampos["idsocios_baja_razones"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsocios_baja_razones"]);}
	function descripcion_razon_de_baja($v = false){ if($v !== false){$this->mCampos["descripcion_razon_de_baja"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_razon_de_baja"]);}
	function destino_de_derechos($v = false){ if($v !== false){$this->mCampos["destino_de_derechos"]["V"] =  $v; } return new MQLCampo($this->mCampos["destino_de_derechos"]);}
	function numero_de_forma($v = false){ if($v !== false){$this->mCampos["numero_de_forma"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_de_forma"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}

/*	ORM: Tabla:	socios_memotipos	-	Generado:	[15/3/2016 13:11]	*/
/*	ORM: Tabla:	socios_memotipos	-	Generado:	[25/5/2018 15:10]	*/
class cSocios_memotipos {
	private $mCampos	= array("tipo_memo" => array("N"=>"tipo_memo","T"=>"INT","V"=>"","L"=>4),"descripcion_memo" => array("N"=>"descripcion_memo","T"=>"VARCHAR","V"=>"","L"=>45));
	public $TIPO_MEMO = "tipo_memo"; public $DESCRIPCION_MEMO = "descripcion_memo";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "socios_memotipos";}
	function getKey(){ return "tipo_memo";}
	function tipo_memo($v = false){ if($v !== false){$this->mCampos["tipo_memo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_memo"]);}
	function descripcion_memo($v = false){ if($v !== false){$this->mCampos["descripcion_memo"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_memo"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	socios_memo	-	Generado:	[15/3/2016 13:12]	*/
/*	ORM: Tabla:	socios_memo	-	Generado:	[25/5/2018 15:10]	*/
class cSocios_memo {
	private $mCampos	= array("idsocios_memo" => array("N"=>"idsocios_memo","T"=>"INT","V"=>"","L"=>4),"numero_socio" => array("N"=>"numero_socio","T"=>"BIGINT","V"=>"1","L"=>20),"numero_gposolidario" => array("N"=>"numero_gposolidario","T"=>"BIGINT","V"=>"1","L"=>20),"numero_solicitud" => array("N"=>"numero_solicitud","T"=>"BIGINT","V"=>"1","L"=>20),"fecha_memo" => array("N"=>"fecha_memo","T"=>"DATE","V"=>"0000-00-00","L"=>0),"texto_memo" => array("N"=>"texto_memo","T"=>"VARCHAR","V"=>"","L"=>250),"tipo_memo" => array("N"=>"tipo_memo","T"=>"INT","V"=>"99","L"=>4),"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"99","L"=>4),"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"MATRIZ","L"=>10),"eacp" => array("N"=>"eacp","T"=>"VARCHAR","V"=>"","L"=>20),"archivado" => array("N"=>"archivado","T"=>"INT","V"=>"1","L"=>2));
	public $IDSOCIOS_MEMO = "idsocios_memo"; public $NUMERO_SOCIO = "numero_socio"; public $NUMERO_GPOSOLIDARIO = "numero_gposolidario"; public $NUMERO_SOLICITUD = "numero_solicitud"; public $FECHA_MEMO = "fecha_memo"; public $TEXTO_MEMO = "texto_memo"; public $TIPO_MEMO = "tipo_memo"; public $IDUSUARIO = "idusuario"; public $SUCURSAL = "sucursal"; public $EACP = "eacp"; public $ARCHIVADO = "archivado";
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
	function archivado($v = false){ if($v !== false){$this->mCampos["archivado"]["V"] =  $v; } return new MQLCampo($this->mCampos["archivado"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}



/*	ORM: Tabla:	socios_viviendatipo	-	Generado:	[31/8/2015 16:00]	*/
/*	ORM: Tabla:	socios_viviendatipo	-	Generado:	[25/5/2018 15:11]	*/
class cSocios_viviendatipo {
	private $mCampos	= array("idsocios_viviendatipo" => array("N"=>"idsocios_viviendatipo","T"=>"INT","V"=>"0","L"=>4),"descripcion_viviendatipo" => array("N"=>"descripcion_viviendatipo","T"=>"VARCHAR","V"=>"","L"=>45),"tipo_domicilio" => array("N"=>"tipo_domicilio","T"=>"INT","V"=>"0","L"=>4));
	public $IDSOCIOS_VIVIENDATIPO = "idsocios_viviendatipo"; public $DESCRIPCION_VIVIENDATIPO = "descripcion_viviendatipo"; public $TIPO_DOMICILIO = "tipo_domicilio";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "socios_viviendatipo";}
	function getKey(){ return "idsocios_viviendatipo";}
	function idsocios_viviendatipo($v = false){ if($v !== false){$this->mCampos["idsocios_viviendatipo"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsocios_viviendatipo"]);}
	function descripcion_viviendatipo($v = false){ if($v !== false){$this->mCampos["descripcion_viviendatipo"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_viviendatipo"]);}
	function tipo_domicilio($v = false){ if($v !== false){$this->mCampos["tipo_domicilio"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_domicilio"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}




/*	ORM: Tabla:	socios_regimenvivienda	-	Generado:	[31/8/2015 16:00]	*/
class cSocios_regimenvivienda {
	private $mCampos	= array(
			"idsocios_regimenvivienda" => array("N"=>"idsocios_regimenvivienda","T"=>"INT","V"=>"0","L"=>4),
			"descipcion_regimenvivienda" => array("N"=>"descipcion_regimenvivienda","T"=>"VARCHAR","V"=>"","L"=>45),
			"tipo_regimen" => array("N"=>"tipo_regimen","T"=>"INT","V"=>"0","L"=>4),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "socios_regimenvivienda";}
	function getKey(){ return "idsocios_regimenvivienda";}
	function idsocios_regimenvivienda($v = false){ if($v !== false){$this->mCampos["idsocios_regimenvivienda"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsocios_regimenvivienda"]);}
	function descipcion_regimenvivienda($v = false){ if($v !== false){$this->mCampos["descipcion_regimenvivienda"]["V"] =  $v; } return new MQLCampo($this->mCampos["descipcion_regimenvivienda"]);}
	function tipo_regimen($v = false){ if($v !== false){$this->mCampos["tipo_regimen"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_regimen"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}






//--- creditos_estatus 		Generado: 13-11-01 13
/*	ORM: Tabla:	creditos_estatus	-	Generado:	[07/11/2016 10:40]	*/
/*	ORM: Tabla:	creditos_estatus	-	Generado:	[03/4/2018 22:45]	*/
class cCreditos_estatus {
	private $mCampos	= array("idcreditos_estatus" => array("N"=>"idcreditos_estatus","T"=>"INT","V"=>"0","L"=>4),"descripcion_estatus" => array("N"=>"descripcion_estatus","T"=>"VARCHAR","V"=>"","L"=>30),"estatus_actual" => array("N"=>"estatus_actual","T"=>"INT","V"=>"0","L"=>4),"titulo_general" => array("N"=>"titulo_general","T"=>"VARCHAR","V"=>"","L"=>40),"orden_clasificacion" => array("N"=>"orden_clasificacion","T"=>"INT","V"=>"0","L"=>4),"respetar_plan_de_pagos" => array("N"=>"respetar_plan_de_pagos","T"=>"ENUM","V"=>"|0|1|","L"=>0),"tit_solicitados" => array("N"=>"tit_solicitados","T"=>"VARCHAR","V"=>"","L"=>30),"tit_autorizados" => array("N"=>"tit_autorizados","T"=>"VARCHAR","V"=>"","L"=>30),"tit_proceso" => array("N"=>"tit_proceso","T"=>"VARCHAR","V"=>"","L"=>30));
	public $IDCREDITOS_ESTATUS = "idcreditos_estatus"; public $DESCRIPCION_ESTATUS = "descripcion_estatus"; public $ESTATUS_ACTUAL = "estatus_actual"; public $TITULO_GENERAL = "titulo_general"; public $ORDEN_CLASIFICACION = "orden_clasificacion"; public $RESPETAR_PLAN_DE_PAGOS = "respetar_plan_de_pagos"; public $TIT_SOLICITADOS = "tit_solicitados"; public $TIT_AUTORIZADOS = "tit_autorizados"; public $TIT_PROCESO = "tit_proceso";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_estatus";}
	function getKey(){ return "idcreditos_estatus";}
	function idcreditos_estatus($v = false){ if($v !== false){$this->mCampos["idcreditos_estatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_estatus"]);}
	function descripcion_estatus($v = false){ if($v !== false){$this->mCampos["descripcion_estatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_estatus"]);}
	function estatus_actual($v = false){ if($v !== false){$this->mCampos["estatus_actual"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus_actual"]);}
	function titulo_general($v = false){ if($v !== false){$this->mCampos["titulo_general"]["V"] =  $v; } return new MQLCampo($this->mCampos["titulo_general"]);}
	function orden_clasificacion($v = false){ if($v !== false){$this->mCampos["orden_clasificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["orden_clasificacion"]);}
	function respetar_plan_de_pagos($v = false){ if($v !== false){$this->mCampos["respetar_plan_de_pagos"]["V"] =  $v; } return new MQLCampo($this->mCampos["respetar_plan_de_pagos"]);}
	function tit_solicitados($v = false){ if($v !== false){$this->mCampos["tit_solicitados"]["V"] =  $v; } return new MQLCampo($this->mCampos["tit_solicitados"]);}
	function tit_autorizados($v = false){ if($v !== false){$this->mCampos["tit_autorizados"]["V"] =  $v; } return new MQLCampo($this->mCampos["tit_autorizados"]);}
	function tit_proceso($v = false){ if($v !== false){$this->mCampos["tit_proceso"]["V"] =  $v; } return new MQLCampo($this->mCampos["tit_proceso"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}

/*	ORM: Tabla:	creditos_reconvenio	-	Generado:	[11/11/2013 11:50]	*/
/*	ORM: Tabla:	creditos_reconvenio	-	Generado:	[07/11/2016 10:41]	*/
class cCreditos_reconvenio {
	private $mCampos	= array("idcreditos_reconvenio" => array("N"=>"idcreditos_reconvenio","T"=>"INT","V"=>"","L"=>10),"numero_solicitud" => array("N"=>"numero_solicitud","T"=>"BIGINT","V"=>"0","L"=>20),"fecha_reconvenio" => array("N"=>"fecha_reconvenio","T"=>"DATE","V"=>"0000-00-00","L"=>0),"monto_reconvenido" => array("N"=>"monto_reconvenido","T"=>"DOUBLE","V"=>"0.00","L"=>33),"periocidad_reconvenida" => array("N"=>"periocidad_reconvenida","T"=>"INT","V"=>"0","L"=>4),"tasa_reconvenida" => array("N"=>"tasa_reconvenida","T"=>"FLOAT","V"=>"0.000","L"=>13),"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"99","L"=>4),"codigo" => array("N"=>"codigo","T"=>"BIGINT","V"=>"1","L"=>20),"pagos_reconvenidos" => array("N"=>"pagos_reconvenidos","T"=>"INT","V"=>"1","L"=>4),"dias" => array("N"=>"dias","T"=>"INT","V"=>"1","L"=>10),"vence" => array("N"=>"vence","T"=>"DATE","V"=>"","L"=>0),"interes_diario_re" => array("N"=>"interes_diario_re","T"=>"DOUBLE","V"=>"0.00","L"=>33),"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"MATRIZ","L"=>10),"interes_pendiente" => array("N"=>"interes_pendiente","T"=>"DOUBLE","V"=>"0.00","L"=>33),"eacp" => array("N"=>"eacp","T"=>"VARCHAR","V"=>"","L"=>25),"credito_origen" => array("N"=>"credito_origen","T"=>"BIGINT","V"=>"0","L"=>20),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_reconvenio";}
	function getKey(){ return "idcreditos_reconvenio";}
	function idcreditos_reconvenio($v = false){ if($v !== false){$this->mCampos["idcreditos_reconvenio"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_reconvenio"]);}
	function numero_solicitud($v = false){ if($v !== false){$this->mCampos["numero_solicitud"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_solicitud"]);}
	function fecha_reconvenio($v = false){ if($v !== false){$this->mCampos["fecha_reconvenio"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_reconvenio"]);}
	function monto_reconvenido($v = false){ if($v !== false){$this->mCampos["monto_reconvenido"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_reconvenido"]);}
	function periocidad_reconvenida($v = false){ if($v !== false){$this->mCampos["periocidad_reconvenida"]["V"] =  $v; } return new MQLCampo($this->mCampos["periocidad_reconvenida"]);}
	function tasa_reconvenida($v = false){ if($v !== false){$this->mCampos["tasa_reconvenida"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_reconvenida"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function codigo($v = false){ if($v !== false){$this->mCampos["codigo"]["V"] =  $v; } return new MQLCampo($this->mCampos["codigo"]);}
	function pagos_reconvenidos($v = false){ if($v !== false){$this->mCampos["pagos_reconvenidos"]["V"] =  $v; } return new MQLCampo($this->mCampos["pagos_reconvenidos"]);}
	function dias($v = false){ if($v !== false){$this->mCampos["dias"]["V"] =  $v; } return new MQLCampo($this->mCampos["dias"]);}
	function vence($v = false){ if($v !== false){$this->mCampos["vence"]["V"] =  $v; } return new MQLCampo($this->mCampos["vence"]);}
	function interes_diario_re($v = false){ if($v !== false){$this->mCampos["interes_diario_re"]["V"] =  $v; } return new MQLCampo($this->mCampos["interes_diario_re"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function interes_pendiente($v = false){ if($v !== false){$this->mCampos["interes_pendiente"]["V"] =  $v; } return new MQLCampo($this->mCampos["interes_pendiente"]);}
	function eacp($v = false){ if($v !== false){$this->mCampos["eacp"]["V"] =  $v; } return new MQLCampo($this->mCampos["eacp"]);}
	function credito_origen($v = false){ if($v !== false){$this->mCampos["credito_origen"]["V"] =  $v; } return new MQLCampo($this->mCampos["credito_origen"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

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

/*	ORM: Tabla:	creditos_datos_originacion	-	Generado:	[07/11/2016 11:03]	*/
/*	ORM: Tabla:	creditos_datos_originacion	-	Generado:	[09/3/2017 17:21]	*/
class cCreditos_datos_originacion {
	private $mCampos	= array("idcreditos_datos_originacion" => array("N"=>"idcreditos_datos_originacion","T"=>"INT","V"=>"","L"=>11),"credito" => array("N"=>"credito","T"=>"BIGINT","V"=>"0","L"=>25),"tipo_originacion" => array("N"=>"tipo_originacion","T"=>"INT","V"=>"1","L"=>6),"clave_vinculada" => array("N"=>"clave_vinculada","T"=>"BIGINT","V"=>"0","L"=>25),"tiempo" => array("N"=>"tiempo","T"=>"INT","V"=>"0","L"=>11),"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"0","L"=>8),"monto_vinculado" => array("N"=>"monto_vinculado","T"=>"DOUBLE","V"=>"0.00","L"=>37),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_datos_originacion";}
	function getKey(){ return "idcreditos_datos_originacion";}
	function idcreditos_datos_originacion($v = false){ if($v !== false){$this->mCampos["idcreditos_datos_originacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_datos_originacion"]);}
	function credito($v = false){ if($v !== false){$this->mCampos["credito"]["V"] =  $v; } return new MQLCampo($this->mCampos["credito"]);}
	function tipo_originacion($v = false){ if($v !== false){$this->mCampos["tipo_originacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_originacion"]);}
	function clave_vinculada($v = false){ if($v !== false){$this->mCampos["clave_vinculada"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_vinculada"]);}
	function tiempo($v = false){ if($v !== false){$this->mCampos["tiempo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tiempo"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function monto_vinculado($v = false){ if($v !== false){$this->mCampos["monto_vinculado"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_vinculado"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}
//---------------------------------------------------------------------  FIN CREDITOS ----------------------------------------------------------------------------------------------------
/*	ORM: Tabla:	operaciones_tipos	-	Generado:	[10/12/2013 11:35]	*/
/*	ORM: Tabla:	operaciones_tipos	-	Generado:	[25/8/2016 12:39]	*/
class cOperaciones_tipos {
	private $mCampos	= array(
			"idoperaciones_tipos" => array("N"=>"idoperaciones_tipos","T"=>"INT","V"=>"","L"=>4),
			"descripcion_operacion" => array("N"=>"descripcion_operacion","T"=>"VARCHAR","V"=>"","L"=>100),
			"clasificacion" => array("N"=>"clasificacion","T"=>"INT","V"=>"0","L"=>4),
			"subclasificacion" => array("N"=>"subclasificacion","T"=>"INT","V"=>"0","L"=>4),
			"cuenta_contable" => array("N"=>"cuenta_contable","T"=>"VARCHAR","V"=>"","L"=>100),
			"descripcion" => array("N"=>"descripcion","T"=>"VARCHAR","V"=>"","L"=>100),
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
			"codigo_de_valoracion" => array("N"=>"codigo_de_valoracion","T"=>"MEDIUMTEXT","V"=>"","L"=>0),
			"periocidad_afectada" => array("N"=>"periocidad_afectada","T"=>"ENUM","V"=>"|ninguna|todas|vencimiento|periodico|","L"=>0),
			"integra_parcialidad" => array("N"=>"integra_parcialidad","T"=>"ENUM","V"=>"|1|0|","L"=>0),
			"es_estadistico" => array("N"=>"es_estadistico","T"=>"ENUM","V"=>"|1|0|","L"=>0),
			"formula_de_calculo" => array("N"=>"formula_de_calculo","T"=>"TEXT","V"=>"","L"=>0),
			"formula_de_cancelacion" => array("N"=>"formula_de_cancelacion","T"=>"MEDIUMTEXT","V"=>"","L"=>0),
			"importancia_de_neutralizacion" => array("N"=>"importancia_de_neutralizacion","T"=>"INT","V"=>"0","L"=>4),
			"preservar_movimiento" => array("N"=>"preservar_movimiento","T"=>"ENUM","V"=>"|0|1|","L"=>0),
			"tasa_iva" => array("N"=>"tasa_iva","T"=>"FLOAT","V"=>"0.000","L"=>17),
			"nombre_corto" => array("N"=>"nombre_corto","T"=>"VARCHAR","V"=>"","L"=>15),
			"estatus" => array("N"=>"estatus","T"=>"INT","V"=>"","L"=>2),
			"precio" => array("N"=>"precio","T"=>"FLOAT","V"=>"0.00","L"=>25),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "operaciones_tipos";}
	function getKey(){ return "idoperaciones_tipos";}
	function idoperaciones_tipos($v = false){ if($v !== false){$this->mCampos["idoperaciones_tipos"]["V"] =  $v; } return new MQLCampo($this->mCampos["idoperaciones_tipos"]);}
	function descripcion_operacion($v = false){ if($v !== false){$this->mCampos["descripcion_operacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_operacion"]);}
	function clasificacion($v = false){ if($v !== false){$this->mCampos["clasificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["clasificacion"]);}
	function subclasificacion($v = false){ if($v !== false){$this->mCampos["subclasificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["subclasificacion"]);}
	function cuenta_contable($v = false){ if($v !== false){$this->mCampos["cuenta_contable"]["V"] =  $v; } return new MQLCampo($this->mCampos["cuenta_contable"]);}
	function descripcion($v = false){ if($v !== false){$this->mCampos["descripcion"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion"]);}
	function recibo_que_afecta($v = false){ if($v !== false){$this->mCampos["recibo_que_afecta"]["V"] =  $v; } return new MQLCampo($this->mCampos["recibo_que_afecta"]);}
	function tipo_operacion($v = false){ if($v !== false){$this->mCampos["tipo_operacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_operacion"]);}
	function visible_reporte($v = false){ if($v !== false){$this->mCampos["visible_reporte"]["V"] =  $v; } return new MQLCampo($this->mCampos["visible_reporte"]);}
	function class_efectivo($v = false){ if($v !== false){$this->mCampos["class_efectivo"]["V"] =  $v; } return new MQLCampo($this->mCampos["class_efectivo"]);}
	function mvto_que_afecta($v = false){ if($v !== false){$this->mCampos["mvto_que_afecta"]["V"] =  $v; } return new MQLCampo($this->mCampos["mvto_que_afecta"]);}
	function afectacion_en_recibo($v = false){ if($v !== false){$this->mCampos["afectacion_en_recibo"]["V"] =  $v; } return new MQLCampo($this->mCampos["afectacion_en_recibo"]);}
	function afectacion_en_notificacion($v = false){ if($v !== false){$this->mCampos["afectacion_en_notificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["afectacion_en_notificacion"]);}
	function producto_aplicable($v = false){ if($v !== false){$this->mCampos["producto_aplicable"]["V"] =  $v; } return new MQLCampo($this->mCampos["producto_aplicable"]);}
	function constituye_fondo_automatico($v = false){ if($v !== false){$this->mCampos["constituye_fondo_automatico"]["V"] =  $v; } return new MQLCampo($this->mCampos["constituye_fondo_automatico"]);}
	function integra_vencido($v = false){ if($v !== false){$this->mCampos["integra_vencido"]["V"] =  $v; } return new MQLCampo($this->mCampos["integra_vencido"]);}
	function afectacion_en_sdpm($v = false){ if($v !== false){$this->mCampos["afectacion_en_sdpm"]["V"] =  $v; } return new MQLCampo($this->mCampos["afectacion_en_sdpm"]);}
	function cargo_directo($v = false){ if($v !== false){$this->mCampos["cargo_directo"]["V"] =  $v; } return new MQLCampo($this->mCampos["cargo_directo"]);}
	function codigo_de_valoracion($v = false){ if($v !== false){$this->mCampos["codigo_de_valoracion"]["V"] =  $v; } return new MQLCampo($this->mCampos["codigo_de_valoracion"]);}
	function periocidad_afectada($v = false){ if($v !== false){$this->mCampos["periocidad_afectada"]["V"] =  $v; } return new MQLCampo($this->mCampos["periocidad_afectada"]);}
	function integra_parcialidad($v = false){ if($v !== false){$this->mCampos["integra_parcialidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["integra_parcialidad"]);}
	function es_estadistico($v = false){ if($v !== false){$this->mCampos["es_estadistico"]["V"] =  $v; } return new MQLCampo($this->mCampos["es_estadistico"]);}
	function formula_de_calculo($v = false){ if($v !== false){$this->mCampos["formula_de_calculo"]["V"] =  $v; } return new MQLCampo($this->mCampos["formula_de_calculo"]);}
	function formula_de_cancelacion($v = false){ if($v !== false){$this->mCampos["formula_de_cancelacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["formula_de_cancelacion"]);}
	function importancia_de_neutralizacion($v = false){ if($v !== false){$this->mCampos["importancia_de_neutralizacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["importancia_de_neutralizacion"]);}
	function preservar_movimiento($v = false){ if($v !== false){$this->mCampos["preservar_movimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["preservar_movimiento"]);}
	function tasa_iva($v = false){ if($v !== false){$this->mCampos["tasa_iva"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_iva"]);}
	function nombre_corto($v = false){ if($v !== false){$this->mCampos["nombre_corto"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_corto"]);}
	function estatus($v = false){ if($v !== false){$this->mCampos["estatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus"]);}
	function precio($v = false){ if($v !== false){$this->mCampos["precio"]["V"] =  $v; } return new MQLCampo($this->mCampos["precio"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

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
/*	ORM: Tabla:	operaciones_mvtos	-	Generado:	[07/11/2016 10:43]	*/
class cOperaciones_mvtos {
	private $mCampos	= array("idoperaciones_mvtos" => array("N"=>"idoperaciones_mvtos","T"=>"BIGINT","V"=>"","L"=>20),"fecha_operacion" => array("N"=>"fecha_operacion","T"=>"DATE","V"=>"0000-00-00","L"=>0),"fecha_afectacion" => array("N"=>"fecha_afectacion","T"=>"DATE","V"=>"0000-00-00","L"=>0),"recibo_afectado" => array("N"=>"recibo_afectado","T"=>"BIGINT","V"=>"1","L"=>20),"socio_afectado" => array("N"=>"socio_afectado","T"=>"BIGINT","V"=>"1","L"=>20),"docto_afectado" => array("N"=>"docto_afectado","T"=>"BIGINT","V"=>"1","L"=>20),"tipo_operacion" => array("N"=>"tipo_operacion","T"=>"INT","V"=>"99","L"=>4),"afectacion_real" => array("N"=>"afectacion_real","T"=>"DOUBLE","V"=>"0.00","L"=>33),"afectacion_cobranza" => array("N"=>"afectacion_cobranza","T"=>"TINYINT","V"=>"0","L"=>4),"afectacion_contable" => array("N"=>"afectacion_contable","T"=>"TINYINT","V"=>"0","L"=>4),"valor_afectacion" => array("N"=>"valor_afectacion","T"=>"FLOAT","V"=>"0.00","L"=>9),"fecha_vcto" => array("N"=>"fecha_vcto","T"=>"DATE","V"=>"0000-00-00","L"=>0),"estatus_mvto" => array("N"=>"estatus_mvto","T"=>"INT","V"=>"99","L"=>3),"codigo_eacp" => array("N"=>"codigo_eacp","T"=>"VARCHAR","V"=>"EN_TRAMITE","L"=>15),"periodo_socio" => array("N"=>"periodo_socio","T"=>"INT","V"=>"1","L"=>4),"periodo_contable" => array("N"=>"periodo_contable","T"=>"TINYINT","V"=>"0","L"=>3),"periodo_cobranza" => array("N"=>"periodo_cobranza","T"=>"TINYINT","V"=>"0","L"=>3),"periodo_seguimiento" => array("N"=>"periodo_seguimiento","T"=>"TINYINT","V"=>"0","L"=>3),"periodo_mensual" => array("N"=>"periodo_mensual","T"=>"TINYINT","V"=>"0","L"=>3),"periodo_semanal" => array("N"=>"periodo_semanal","T"=>"TINYINT","V"=>"0","L"=>3),"periodo_anual" => array("N"=>"periodo_anual","T"=>"TINYINT","V"=>"0","L"=>3),"saldo_anterior" => array("N"=>"saldo_anterior","T"=>"DOUBLE","V"=>"0.00","L"=>33),"saldo_actual" => array("N"=>"saldo_actual","T"=>"DOUBLE","V"=>"0.00","L"=>33),"detalles" => array("N"=>"detalles","T"=>"VARCHAR","V"=>"","L"=>80),"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"99","L"=>4),"afectacion_estadistica" => array("N"=>"afectacion_estadistica","T"=>"DOUBLE","V"=>"0.00","L"=>33),"docto_neutralizador" => array("N"=>"docto_neutralizador","T"=>"BIGINT","V"=>"1","L"=>20),"cadena_heredada" => array("N"=>"cadena_heredada","T"=>"VARCHAR","V"=>"","L"=>20),"tasa_asociada" => array("N"=>"tasa_asociada","T"=>"FLOAT","V"=>"0.0000","L"=>15),"dias_asociados" => array("N"=>"dias_asociados","T"=>"INT","V"=>"0","L"=>4),"grupo_asociado" => array("N"=>"grupo_asociado","T"=>"BIGINT","V"=>"0","L"=>20),"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"matriz","L"=>20),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "operaciones_mvtos";}
	function getKey(){ return "idoperaciones_mvtos";}
	function idoperaciones_mvtos($v = false){ if($v !== false){$this->mCampos["idoperaciones_mvtos"]["V"] =  $v; } return new MQLCampo($this->mCampos["idoperaciones_mvtos"]);}
	function fecha_operacion($v = false){ if($v !== false){$this->mCampos["fecha_operacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_operacion"]);}
	function fecha_afectacion($v = false){ if($v !== false){$this->mCampos["fecha_afectacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_afectacion"]);}
	function recibo_afectado($v = false){ if($v !== false){$this->mCampos["recibo_afectado"]["V"] =  $v; } return new MQLCampo($this->mCampos["recibo_afectado"]);}
	function socio_afectado($v = false){ if($v !== false){$this->mCampos["socio_afectado"]["V"] =  $v; } return new MQLCampo($this->mCampos["socio_afectado"]);}
	function docto_afectado($v = false){ if($v !== false){$this->mCampos["docto_afectado"]["V"] =  $v; } return new MQLCampo($this->mCampos["docto_afectado"]);}
	function tipo_operacion($v = false){ if($v !== false){$this->mCampos["tipo_operacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_operacion"]);}
	function afectacion_real($v = false){ if($v !== false){$this->mCampos["afectacion_real"]["V"] =  $v; } return new MQLCampo($this->mCampos["afectacion_real"]);}
	function afectacion_cobranza($v = false){ if($v !== false){$this->mCampos["afectacion_cobranza"]["V"] =  $v; } return new MQLCampo($this->mCampos["afectacion_cobranza"]);}
	function afectacion_contable($v = false){ if($v !== false){$this->mCampos["afectacion_contable"]["V"] =  $v; } return new MQLCampo($this->mCampos["afectacion_contable"]);}
	function valor_afectacion($v = false){ if($v !== false){$this->mCampos["valor_afectacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["valor_afectacion"]);}
	function fecha_vcto($v = false){ if($v !== false){$this->mCampos["fecha_vcto"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_vcto"]);}
	function estatus_mvto($v = false){ if($v !== false){$this->mCampos["estatus_mvto"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus_mvto"]);}
	function codigo_eacp($v = false){ if($v !== false){$this->mCampos["codigo_eacp"]["V"] =  $v; } return new MQLCampo($this->mCampos["codigo_eacp"]);}
	function periodo_socio($v = false){ if($v !== false){$this->mCampos["periodo_socio"]["V"] =  $v; } return new MQLCampo($this->mCampos["periodo_socio"]);}
	function periodo_contable($v = false){ if($v !== false){$this->mCampos["periodo_contable"]["V"] =  $v; } return new MQLCampo($this->mCampos["periodo_contable"]);}
	function periodo_cobranza($v = false){ if($v !== false){$this->mCampos["periodo_cobranza"]["V"] =  $v; } return new MQLCampo($this->mCampos["periodo_cobranza"]);}
	function periodo_seguimiento($v = false){ if($v !== false){$this->mCampos["periodo_seguimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["periodo_seguimiento"]);}
	function periodo_mensual($v = false){ if($v !== false){$this->mCampos["periodo_mensual"]["V"] =  $v; } return new MQLCampo($this->mCampos["periodo_mensual"]);}
	function periodo_semanal($v = false){ if($v !== false){$this->mCampos["periodo_semanal"]["V"] =  $v; } return new MQLCampo($this->mCampos["periodo_semanal"]);}
	function periodo_anual($v = false){ if($v !== false){$this->mCampos["periodo_anual"]["V"] =  $v; } return new MQLCampo($this->mCampos["periodo_anual"]);}
	function saldo_anterior($v = false){ if($v !== false){$this->mCampos["saldo_anterior"]["V"] =  $v; } return new MQLCampo($this->mCampos["saldo_anterior"]);}
	function saldo_actual($v = false){ if($v !== false){$this->mCampos["saldo_actual"]["V"] =  $v; } return new MQLCampo($this->mCampos["saldo_actual"]);}
	function detalles($v = false){ if($v !== false){$this->mCampos["detalles"]["V"] =  $v; } return new MQLCampo($this->mCampos["detalles"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function afectacion_estadistica($v = false){ if($v !== false){$this->mCampos["afectacion_estadistica"]["V"] =  $v; } return new MQLCampo($this->mCampos["afectacion_estadistica"]);}
	function docto_neutralizador($v = false){ if($v !== false){$this->mCampos["docto_neutralizador"]["V"] =  $v; } return new MQLCampo($this->mCampos["docto_neutralizador"]);}
	function cadena_heredada($v = false){ if($v !== false){$this->mCampos["cadena_heredada"]["V"] =  $v; } return new MQLCampo($this->mCampos["cadena_heredada"]);}
	function tasa_asociada($v = false){ if($v !== false){$this->mCampos["tasa_asociada"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_asociada"]);}
	function dias_asociados($v = false){ if($v !== false){$this->mCampos["dias_asociados"]["V"] =  $v; } return new MQLCampo($this->mCampos["dias_asociados"]);}
	function grupo_asociado($v = false){ if($v !== false){$this->mCampos["grupo_asociado"]["V"] =  $v; } return new MQLCampo($this->mCampos["grupo_asociado"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	operaciones_promociones	-	Generado:	[25/8/2016 15:46]	*/
class cOperaciones_promociones {
	private $mCampos	= array(
			"idoperaciones_promociones" => array("N"=>"idoperaciones_promociones","T"=>"INT","V"=>"","L"=>11),
			"tipo_promocion" => array("N"=>"tipo_promocion","T"=>"INT","V"=>"1","L"=>2),
			"fecha_inicial" => array("N"=>"fecha_inicial","T"=>"DATE","V"=>"","L"=>0),
			"fecha_final" => array("N"=>"fecha_final","T"=>"DATE","V"=>"","L"=>0),
			"tipo_operacion" => array("N"=>"tipo_operacion","T"=>"INT","V"=>"0","L"=>8),
			"condiciones" => array("N"=>"condiciones","T"=>"TEXT","V"=>"","L"=>0),
			"num_items" => array("N"=>"num_items","T"=>"INT","V"=>"0","L"=>4),
			"descuento" => array("N"=>"descuento","T"=>"FLOAT","V"=>"0.0000","L"=>13),
			"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"todas","L"=>20),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "operaciones_promociones";}
	function getKey(){ return "idoperaciones_promociones";}
	function idoperaciones_promociones($v = false){ if($v !== false){$this->mCampos["idoperaciones_promociones"]["V"] =  $v; } return new MQLCampo($this->mCampos["idoperaciones_promociones"]);}
	function tipo_promocion($v = false){ if($v !== false){$this->mCampos["tipo_promocion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_promocion"]);}
	function fecha_inicial($v = false){ if($v !== false){$this->mCampos["fecha_inicial"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_inicial"]);}
	function fecha_final($v = false){ if($v !== false){$this->mCampos["fecha_final"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_final"]);}
	function tipo_operacion($v = false){ if($v !== false){$this->mCampos["tipo_operacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_operacion"]);}
	function condiciones($v = false){ if($v !== false){$this->mCampos["condiciones"]["V"] =  $v; } return new MQLCampo($this->mCampos["condiciones"]);}
	function num_items($v = false){ if($v !== false){$this->mCampos["num_items"]["V"] =  $v; } return new MQLCampo($this->mCampos["num_items"]);}
	function descuento($v = false){ if($v !== false){$this->mCampos["descuento"]["V"] =  $v; } return new MQLCampo($this->mCampos["descuento"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}



/*	ORM: Tabla:	letras	-	Generado:	[08/12/2017 18:13]	*/
/*	ORM: Tabla:	letras	-	Generado:	[19/1/2018 16:24]	*/
class cLetrasVista {
	private $mCampos	= array("codigo_de_base" => array("N"=>"codigo_de_base","T"=>"INT","V"=>"","L"=>10),"socio_afectado" => array("N"=>"socio_afectado","T"=>"BIGINT","V"=>"1","L"=>20),"persona" => array("N"=>"persona","T"=>"BIGINT","V"=>"1","L"=>20),"credito" => array("N"=>"credito","T"=>"BIGINT","V"=>"1","L"=>20),"parcialidad" => array("N"=>"parcialidad","T"=>"INT","V"=>"1","L"=>4),"docto_afectado" => array("N"=>"docto_afectado","T"=>"BIGINT","V"=>"1","L"=>20),"periodo_socio" => array("N"=>"periodo_socio","T"=>"INT","V"=>"1","L"=>4),"fecha_de_pago" => array("N"=>"fecha_de_pago","T"=>"DATE","V"=>"","L"=>0),"fecha_de_vencimiento" => array("N"=>"fecha_de_vencimiento","T"=>"DATE","V"=>"","L"=>0),"capital" => array("N"=>"capital","T"=>"DOUBLE","V"=>"","L"=>39),"interes" => array("N"=>"interes","T"=>"DOUBLE","V"=>"","L"=>39),"iva" => array("N"=>"iva","T"=>"DOUBLE","V"=>"","L"=>39),"ahorro" => array("N"=>"ahorro","T"=>"DOUBLE","V"=>"","L"=>39),"capital_exigible" => array("N"=>"capital_exigible","T"=>"DOUBLE","V"=>"","L"=>39),"interes_exigible" => array("N"=>"interes_exigible","T"=>"DOUBLE","V"=>"","L"=>39),"iva_exigible" => array("N"=>"iva_exigible","T"=>"DOUBLE","V"=>"","L"=>39),"ahorro_exigible" => array("N"=>"ahorro_exigible","T"=>"DOUBLE","V"=>"","L"=>39),"otros_exigible" => array("N"=>"otros_exigible","T"=>"DOUBLE","V"=>"","L"=>39),"interes_moratorio" => array("N"=>"interes_moratorio","T"=>"DOUBLE","V"=>"","L"=>39),"mora" => array("N"=>"mora","T"=>"DOUBLE","V"=>"","L"=>39),"iva_moratorio" => array("N"=>"iva_moratorio","T"=>"DOUBLE","V"=>"","L"=>39),"dias" => array("N"=>"dias","T"=>"DECIMAL","V"=>"","L"=>57),"otros" => array("N"=>"otros","T"=>"DOUBLE","V"=>"","L"=>39),"letra" => array("N"=>"letra","T"=>"DOUBLE","V"=>"","L"=>43),"total_sin_otros" => array("N"=>"total_sin_otros","T"=>"DOUBLE","V"=>"","L"=>39),"clave_otros" => array("N"=>"clave_otros","T"=>"DECIMAL","V"=>"","L"=>21));
	public $CODIGO_DE_BASE = "codigo_de_base"; public $SOCIO_AFECTADO = "socio_afectado"; public $PERSONA = "persona"; public $CREDITO = "credito"; public $PARCIALIDAD = "parcialidad"; public $DOCTO_AFECTADO = "docto_afectado"; public $PERIODO_SOCIO = "periodo_socio"; public $FECHA_DE_PAGO = "fecha_de_pago"; public $FECHA_DE_VENCIMIENTO = "fecha_de_vencimiento"; public $CAPITAL = "capital"; public $INTERES = "interes"; public $IVA = "iva"; public $AHORRO = "ahorro"; public $CAPITAL_EXIGIBLE = "capital_exigible"; public $INTERES_EXIGIBLE = "interes_exigible"; public $IVA_EXIGIBLE = "iva_exigible"; public $AHORRO_EXIGIBLE = "ahorro_exigible"; public $OTROS_EXIGIBLE = "otros_exigible"; public $INTERES_MORATORIO = "interes_moratorio"; public $MORA = "mora"; public $IVA_MORATORIO = "iva_moratorio"; public $DIAS = "dias"; public $OTROS = "otros"; public $LETRA = "letra"; public $TOTAL_SIN_OTROS = "total_sin_otros"; public $CLAVE_OTROS = "clave_otros";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "letras";}
	function getKey(){ return "";}
	function codigo_de_base($v = false){ if($v !== false){$this->mCampos["codigo_de_base"]["V"] =  $v; } return new MQLCampo($this->mCampos["codigo_de_base"]);}
	function socio_afectado($v = false){ if($v !== false){$this->mCampos["socio_afectado"]["V"] =  $v; } return new MQLCampo($this->mCampos["socio_afectado"]);}
	function persona($v = false){ if($v !== false){$this->mCampos["persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["persona"]);}
	function credito($v = false){ if($v !== false){$this->mCampos["credito"]["V"] =  $v; } return new MQLCampo($this->mCampos["credito"]);}
	function parcialidad($v = false){ if($v !== false){$this->mCampos["parcialidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["parcialidad"]);}
	function docto_afectado($v = false){ if($v !== false){$this->mCampos["docto_afectado"]["V"] =  $v; } return new MQLCampo($this->mCampos["docto_afectado"]);}
	function periodo_socio($v = false){ if($v !== false){$this->mCampos["periodo_socio"]["V"] =  $v; } return new MQLCampo($this->mCampos["periodo_socio"]);}
	function fecha_de_pago($v = false){ if($v !== false){$this->mCampos["fecha_de_pago"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_pago"]);}
	function fecha_de_vencimiento($v = false){ if($v !== false){$this->mCampos["fecha_de_vencimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_vencimiento"]);}
	function capital($v = false){ if($v !== false){$this->mCampos["capital"]["V"] =  $v; } return new MQLCampo($this->mCampos["capital"]);}
	function interes($v = false){ if($v !== false){$this->mCampos["interes"]["V"] =  $v; } return new MQLCampo($this->mCampos["interes"]);}
	function iva($v = false){ if($v !== false){$this->mCampos["iva"]["V"] =  $v; } return new MQLCampo($this->mCampos["iva"]);}
	function ahorro($v = false){ if($v !== false){$this->mCampos["ahorro"]["V"] =  $v; } return new MQLCampo($this->mCampos["ahorro"]);}
	function capital_exigible($v = false){ if($v !== false){$this->mCampos["capital_exigible"]["V"] =  $v; } return new MQLCampo($this->mCampos["capital_exigible"]);}
	function interes_exigible($v = false){ if($v !== false){$this->mCampos["interes_exigible"]["V"] =  $v; } return new MQLCampo($this->mCampos["interes_exigible"]);}
	function iva_exigible($v = false){ if($v !== false){$this->mCampos["iva_exigible"]["V"] =  $v; } return new MQLCampo($this->mCampos["iva_exigible"]);}
	function ahorro_exigible($v = false){ if($v !== false){$this->mCampos["ahorro_exigible"]["V"] =  $v; } return new MQLCampo($this->mCampos["ahorro_exigible"]);}
	function otros_exigible($v = false){ if($v !== false){$this->mCampos["otros_exigible"]["V"] =  $v; } return new MQLCampo($this->mCampos["otros_exigible"]);}
	function interes_moratorio($v = false){ if($v !== false){$this->mCampos["interes_moratorio"]["V"] =  $v; } return new MQLCampo($this->mCampos["interes_moratorio"]);}
	function mora($v = false){ if($v !== false){$this->mCampos["mora"]["V"] =  $v; } return new MQLCampo($this->mCampos["mora"]);}
	function iva_moratorio($v = false){ if($v !== false){$this->mCampos["iva_moratorio"]["V"] =  $v; } return new MQLCampo($this->mCampos["iva_moratorio"]);}
	function dias($v = false){ if($v !== false){$this->mCampos["dias"]["V"] =  $v; } return new MQLCampo($this->mCampos["dias"]);}
	function otros($v = false){ if($v !== false){$this->mCampos["otros"]["V"] =  $v; } return new MQLCampo($this->mCampos["otros"]);}
	function letra($v = false){ if($v !== false){$this->mCampos["letra"]["V"] =  $v; } return new MQLCampo($this->mCampos["letra"]);}
	function total_sin_otros($v = false){ if($v !== false){$this->mCampos["total_sin_otros"]["V"] =  $v; } return new MQLCampo($this->mCampos["total_sin_otros"]);}
	function clave_otros($v = false){ if($v !== false){$this->mCampos["clave_otros"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_otros"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}


/*	ORM: Tabla:	socios_estadocivil	-	Generado:	[02/3/2015 14:34]	*/
class cSocios_estadocivil {
	private $mCampos	= array(
			"idsocios_estadocivil" => array("N"=>"idsocios_estadocivil","T"=>"INT","V"=>"0","L"=>4),
			"descripcion_estadocivil" => array("N"=>"descripcion_estadocivil","T"=>"VARCHAR","V"=>"","L"=>45),
			"valor_scoring" => array("N"=>"valor_scoring","T"=>"FLOAT","V"=>"0.000","L"=>13),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "socios_estadocivil";}
	function getKey(){ return "idsocios_estadocivil";}
	function idsocios_estadocivil($v = false){ if($v !== false){$this->mCampos["idsocios_estadocivil"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsocios_estadocivil"]);}
	function descripcion_estadocivil($v = false){ if($v !== false){$this->mCampos["descripcion_estadocivil"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_estadocivil"]);}
	function valor_scoring($v = false){ if($v !== false){$this->mCampos["valor_scoring"]["V"] =  $v; } return new MQLCampo($this->mCampos["valor_scoring"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	operaciones_recibos	-	Generado:	[15/6/2015 18:13]	*/
/*	ORM: Tabla:	operaciones_recibos	-	Generado:	[01/9/2016 14:47]	*/
/*	ORM: Tabla:	operaciones_recibos	-	Generado:	[06/9/2016 10:28]	*/
/*	ORM: Tabla:	operaciones_recibos	-	Generado:	[31/10/2017 12:57]	*/
/*	ORM: Tabla:	operaciones_recibos	-	Generado:	[07/5/2018 16:03]	*/
class cOperaciones_recibos {
	private $mCampos	= array("idoperaciones_recibos" => array("N"=>"idoperaciones_recibos","T"=>"BIGINT","V"=>"","L"=>20),"fecha_operacion" => array("N"=>"fecha_operacion","T"=>"DATE","V"=>"0000-00-00","L"=>0),"numero_socio" => array("N"=>"numero_socio","T"=>"BIGINT","V"=>"1","L"=>20),"docto_afectado" => array("N"=>"docto_afectado","T"=>"BIGINT","V"=>"1","L"=>20),"tipo_docto" => array("N"=>"tipo_docto","T"=>"INT","V"=>"99","L"=>4),"total_operacion" => array("N"=>"total_operacion","T"=>"DOUBLE","V"=>"0.00","L"=>33),"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"99","L"=>6),"observacion_recibo" => array("N"=>"observacion_recibo","T"=>"VARCHAR","V"=>"","L"=>200),"cheque_afectador" => array("N"=>"cheque_afectador","T"=>"VARCHAR","V"=>"N/A","L"=>20),"cadena_distributiva" => array("N"=>"cadena_distributiva","T"=>"VARCHAR","V"=>"N/A","L"=>150),"tipo_pago" => array("N"=>"tipo_pago","T"=>"VARCHAR","V"=>"efectivo","L"=>25),"indice_origen" => array("N"=>"indice_origen","T"=>"INT","V"=>"99","L"=>4),"grupo_asociado" => array("N"=>"grupo_asociado","T"=>"BIGINT","V"=>"99","L"=>20),"recibo_fiscal" => array("N"=>"recibo_fiscal","T"=>"VARCHAR","V"=>"N/A","L"=>15),"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"MATRIZ","L"=>10),"eacp" => array("N"=>"eacp","T"=>"VARCHAR","V"=>"EN_TRAMITE","L"=>20),"clave_de_moneda" => array("N"=>"clave_de_moneda","T"=>"VARCHAR","V"=>"MXN","L"=>6),"unidades_en_moneda" => array("N"=>"unidades_en_moneda","T"=>"DOUBLE","V"=>"0.0000","L"=>33),"origen_aml" => array("N"=>"origen_aml","T"=>"INT","V"=>"0","L"=>4),"archivo_fisico" => array("N"=>"archivo_fisico","T"=>"VARCHAR","V"=>"","L"=>200),"persona_asociada" => array("N"=>"persona_asociada","T"=>"BIGINT","V"=>"0","L"=>20),"fecha_de_registro" => array("N"=>"fecha_de_registro","T"=>"DATE","V"=>"0000-00-00","L"=>0),"periodo_de_documento" => array("N"=>"periodo_de_documento","T"=>"INT","V"=>"0","L"=>4),"cuenta_bancaria" => array("N"=>"cuenta_bancaria","T"=>"BIGINT","V"=>"0","L"=>20),"montohist" => array("N"=>"montohist","T"=>"DOUBLE","V"=>"0.00","L"=>33),"tiempo" => array("N"=>"tiempo","T"=>"INT","V"=>"0","L"=>11),"idtipocbza" => array("N"=>"idtipocbza","T"=>"INT","V"=>"1","L"=>4),"idusuario_cbza" => array("N"=>"idusuario_cbza","T"=>"INT","V"=>"1","L"=>6));
	public $IDOPERACIONES_RECIBOS = "idoperaciones_recibos"; public $FECHA_OPERACION = "fecha_operacion"; public $NUMERO_SOCIO = "numero_socio"; public $DOCTO_AFECTADO = "docto_afectado"; public $TIPO_DOCTO = "tipo_docto"; public $TOTAL_OPERACION = "total_operacion"; public $IDUSUARIO = "idusuario"; public $OBSERVACION_RECIBO = "observacion_recibo"; public $CHEQUE_AFECTADOR = "cheque_afectador"; public $CADENA_DISTRIBUTIVA = "cadena_distributiva"; public $TIPO_PAGO = "tipo_pago"; public $INDICE_ORIGEN = "indice_origen"; public $GRUPO_ASOCIADO = "grupo_asociado"; public $RECIBO_FISCAL = "recibo_fiscal"; public $SUCURSAL = "sucursal"; public $EACP = "eacp"; public $CLAVE_DE_MONEDA = "clave_de_moneda"; public $UNIDADES_EN_MONEDA = "unidades_en_moneda"; public $ORIGEN_AML = "origen_aml"; public $ARCHIVO_FISICO = "archivo_fisico"; public $PERSONA_ASOCIADA = "persona_asociada"; public $FECHA_DE_REGISTRO = "fecha_de_registro"; public $PERIODO_DE_DOCUMENTO = "periodo_de_documento"; public $CUENTA_BANCARIA = "cuenta_bancaria"; public $MONTOHIST = "montohist"; public $TIEMPO = "tiempo"; public $IDTIPOCBZA = "idtipocbza"; public $IDUSUARIO_CBZA = "idusuario_cbza";
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
	function periodo_de_documento($v = false){ if($v !== false){$this->mCampos["periodo_de_documento"]["V"] =  $v; } return new MQLCampo($this->mCampos["periodo_de_documento"]);}
	function cuenta_bancaria($v = false){ if($v !== false){$this->mCampos["cuenta_bancaria"]["V"] =  $v; } return new MQLCampo($this->mCampos["cuenta_bancaria"]);}
	function montohist($v = false){ if($v !== false){$this->mCampos["montohist"]["V"] =  $v; } return new MQLCampo($this->mCampos["montohist"]);}
	function tiempo($v = false){ if($v !== false){$this->mCampos["tiempo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tiempo"]);}
	function idtipocbza($v = false){ if($v !== false){$this->mCampos["idtipocbza"]["V"] =  $v; } return new MQLCampo($this->mCampos["idtipocbza"]);}
	function idusuario_cbza($v = false){ if($v !== false){$this->mCampos["idusuario_cbza"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario_cbza"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
}


/*	ORM: Tabla:	operaciones_recibostipo	-	Generado:	[14/8/2014 14:45]	*/
/*	ORM: Tabla:	operaciones_recibostipo	-	Generado:	[31/10/2017 13:29]	*/
class cOperaciones_recibostipo {
	private $mCampos	= array("idoperaciones_recibostipo" => array("N"=>"idoperaciones_recibostipo","T"=>"INT","V"=>"0","L"=>4),"descripcion_recibostipo" => array("N"=>"descripcion_recibostipo","T"=>"VARCHAR","V"=>"","L"=>45),"detalles_del_concepto" => array("N"=>"detalles_del_concepto","T"=>"VARCHAR","V"=>"","L"=>100),"subclasificacion" => array("N"=>"subclasificacion","T"=>"FLOAT","V"=>"","L"=>0),"nombre_sublasificacion" => array("N"=>"nombre_sublasificacion","T"=>"VARCHAR","V"=>"","L"=>50),"mostrar_en_corte" => array("N"=>"mostrar_en_corte","T"=>"ENUM","V"=>"|1|0|","L"=>0),"tipo_poliza_generada" => array("N"=>"tipo_poliza_generada","T"=>"INT","V"=>"","L"=>4),"afectacion_en_flujo_efvo" => array("N"=>"afectacion_en_flujo_efvo","T"=>"ENUM","V"=>"|aumento|disminucion|ninguna|","L"=>0),"path_formato" => array("N"=>"path_formato","T"=>"VARCHAR","V"=>"","L"=>100),"origen" => array("N"=>"origen","T"=>"ENUM","V"=>"|colocacion|captacion|otros|mixto|","L"=>0),);
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
/*	ORM: Tabla:	general_structure	-	Generado:	[31/10/2017 13:30]	*/
class cGeneral_structure {
	private $mCampos	= array("index_struct" => array("N"=>"index_struct","T"=>"INT","V"=>"","L"=>11),"tabla" => array("N"=>"tabla","T"=>"VARCHAR","V"=>"","L"=>100),"campo" => array("N"=>"campo","T"=>"VARCHAR","V"=>"","L"=>100),"valor" => array("N"=>"valor","T"=>"VARCHAR","V"=>"","L"=>250),"tipo" => array("N"=>"tipo","T"=>"VARCHAR","V"=>"","L"=>20),"longitud" => array("N"=>"longitud","T"=>"INT","V"=>"0","L"=>4),"descripcion" => array("N"=>"descripcion","T"=>"VARCHAR","V"=>"","L"=>200),"titulo" => array("N"=>"titulo","T"=>"VARCHAR","V"=>"","L"=>100),"control" => array("N"=>"control","T"=>"ENUM","V"=>"|text|textarea|select|hidden|","L"=>0),"sql_select" => array("N"=>"sql_select","T"=>"TEXT","V"=>"","L"=>0),"orientacion" => array("N"=>"orientacion","T"=>"ENUM","V"=>"|izquierda|derecha|","L"=>0),"order_index" => array("N"=>"order_index","T"=>"INT","V"=>"0","L"=>10),"script_field" => array("N"=>"script_field","T"=>"MEDIUMTEXT","V"=>"","L"=>0),"help_text" => array("N"=>"help_text","T"=>"MEDIUMTEXT","V"=>"","L"=>0),"tab_num" => array("N"=>"tab_num","T"=>"VARCHAR","V"=>"","L"=>20),"css_class" => array("N"=>"css_class","T"=>"VARCHAR","V"=>"normalfield","L"=>20),"input_events" => array("N"=>"input_events","T"=>"VARCHAR","V"=>"","L"=>120),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "general_structure";}
	function getKey(){ return "index_struct";}
	function index_struct($v = false){ if($v !== false){$this->mCampos["index_struct"]["V"] =  $v; } return new MQLCampo($this->mCampos["index_struct"]);}
	function tabla($v = false){ if($v !== false){$this->mCampos["tabla"]["V"] =  $v; } return new MQLCampo($this->mCampos["tabla"]);}
	function campo($v = false){ if($v !== false){$this->mCampos["campo"]["V"] =  $v; } return new MQLCampo($this->mCampos["campo"]);}
	function valor($v = false){ if($v !== false){$this->mCampos["valor"]["V"] =  $v; } return new MQLCampo($this->mCampos["valor"]);}
	function tipo($v = false){ if($v !== false){$this->mCampos["tipo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo"]);}
	function longitud($v = false){ if($v !== false){$this->mCampos["longitud"]["V"] =  $v; } return new MQLCampo($this->mCampos["longitud"]);}
	function descripcion($v = false){ if($v !== false){$this->mCampos["descripcion"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion"]);}
	function titulo($v = false){ if($v !== false){$this->mCampos["titulo"]["V"] =  $v; } return new MQLCampo($this->mCampos["titulo"]);}
	function control($v = false){ if($v !== false){$this->mCampos["control"]["V"] =  $v; } return new MQLCampo($this->mCampos["control"]);}
	function sql_select($v = false){ if($v !== false){$this->mCampos["sql_select"]["V"] =  $v; } return new MQLCampo($this->mCampos["sql_select"]);}
	function orientacion($v = false){ if($v !== false){$this->mCampos["orientacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["orientacion"]);}
	function order_index($v = false){ if($v !== false){$this->mCampos["order_index"]["V"] =  $v; } return new MQLCampo($this->mCampos["order_index"]);}
	function script_field($v = false){ if($v !== false){$this->mCampos["script_field"]["V"] =  $v; } return new MQLCampo($this->mCampos["script_field"]);}
	function help_text($v = false){ if($v !== false){$this->mCampos["help_text"]["V"] =  $v; } return new MQLCampo($this->mCampos["help_text"]);}
	function tab_num($v = false){ if($v !== false){$this->mCampos["tab_num"]["V"] =  $v; } return new MQLCampo($this->mCampos["tab_num"]);}
	function css_class($v = false){ if($v !== false){$this->mCampos["css_class"]["V"] =  $v; } return new MQLCampo($this->mCampos["css_class"]);}
	function input_events($v = false){ if($v !== false){$this->mCampos["input_events"]["V"] =  $v; } return new MQLCampo($this->mCampos["input_events"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}



/*	ORM: Tabla:	bancos_cuentas	-	Generado:	[17/9/2014 10:36]	*/
/*	ORM: Tabla:	bancos_cuentas	-	Generado:	[04/7/2017 15:25]	*/
class cBancos_cuentas {
	private $mCampos	= array("idbancos_cuentas" => array("N"=>"idbancos_cuentas","T"=>"BIGINT","V"=>"","L"=>20),"descripcion_cuenta" => array("N"=>"descripcion_cuenta","T"=>"VARCHAR","V"=>"","L"=>45),"fecha_de_apertura" => array("N"=>"fecha_de_apertura","T"=>"DATE","V"=>"","L"=>0),"estatus_actual" => array("N"=>"estatus_actual","T"=>"ENUM","V"=>"|activo|baja|","L"=>0),"consecutivo_actual" => array("N"=>"consecutivo_actual","T"=>"VARCHAR","V"=>"","L"=>15),"saldo_actual" => array("N"=>"saldo_actual","T"=>"DOUBLE","V"=>"","L"=>33),"eacp" => array("N"=>"eacp","T"=>"VARCHAR","V"=>"EN_TRAMITE","L"=>15),"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"todas","L"=>20),"codigo_contable" => array("N"=>"codigo_contable","T"=>"VARCHAR","V"=>"00000000000000","L"=>20),"entidad_bancaria" => array("N"=>"entidad_bancaria","T"=>"INT","V"=>"0","L"=>10),"tipo_de_cuenta" => array("N"=>"tipo_de_cuenta","T"=>"ENUM","V"=>"|cheques|inversion|","L"=>0),);
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

/*	ORM: Tabla:	bancos_operaciones	-	Generado:	[19/8/2015 19:32]	*/
class cBancos_operaciones {
	private $mCampos	= array(
			"idcontrol" => array("N"=>"idcontrol","T"=>"INT","V"=>"","L"=>10),
			"tipo_operacion" => array("N"=>"tipo_operacion","T"=>"ENUM","V"=>"|cheque|deposito|comision|retiro|traspaso|","L"=>0),
			"numero_de_documento" => array("N"=>"numero_de_documento","T"=>"VARCHAR","V"=>"0","L"=>20),
			"cuenta_bancaria" => array("N"=>"cuenta_bancaria","T"=>"BIGINT","V"=>"0","L"=>20),
			"recibo_relacionado" => array("N"=>"recibo_relacionado","T"=>"BIGINT","V"=>"0","L"=>25),
			"fecha_expedicion" => array("N"=>"fecha_expedicion","T"=>"DATE","V"=>"2014-01-01","L"=>0),
			"beneficiario" => array("N"=>"beneficiario","T"=>"VARCHAR","V"=>"","L"=>80),
			"monto_descontado" => array("N"=>"monto_descontado","T"=>"DOUBLE","V"=>"0.00","L"=>33),
			"monto_real" => array("N"=>"monto_real","T"=>"DOUBLE","V"=>"0.00","L"=>33),
			"estatus" => array("N"=>"estatus","T"=>"ENUM","V"=>"|autorizado|noautorizado|cancelado|","L"=>0),
			"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"","L"=>10),
			"usuario_autorizo" => array("N"=>"usuario_autorizo","T"=>"INT","V"=>"0","L"=>10),
			"eacp" => array("N"=>"eacp","T"=>"VARCHAR","V"=>"EN_TRAMITE","L"=>15),
			"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"matriz","L"=>20),
			"numero_de_socio" => array("N"=>"numero_de_socio","T"=>"BIGINT","V"=>"0","L"=>20),
			"clave_de_conciliacion" => array("N"=>"clave_de_conciliacion","T"=>"VARCHAR","V"=>"","L"=>20),
			"clave_de_moneda" => array("N"=>"clave_de_moneda","T"=>"VARCHAR","V"=>"MXN","L"=>4),
			"tipo_de_exhibicion" => array("N"=>"tipo_de_exhibicion","T"=>"VARCHAR","V"=>"efectivo","L"=>20),
			"cuenta_de_origen" => array("N"=>"cuenta_de_origen","T"=>"BIGINT","V"=>"0","L"=>20),
			"documento_de_origen" => array("N"=>"documento_de_origen","T"=>"BIGINT","V"=>"0","L"=>20),

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
	function documento_de_origen($v = false){ if($v !== false){$this->mCampos["documento_de_origen"]["V"] =  $v; } return new MQLCampo($this->mCampos["documento_de_origen"]);}
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
/*	ORM: Tabla:	tesoreria_cajas_movimientos	-	Generado:	[25/10/2016 12:14]	*/
class cTesoreria_cajas_movimientos {
	private $mCampos	= array("idtesoreria_cajas_movimientos" => array("N"=>"idtesoreria_cajas_movimientos","T"=>"INT","V"=>"","L"=>10),"codigo_de_caja" => array("N"=>"codigo_de_caja","T"=>"VARCHAR","V"=>"","L"=>100),"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"1","L"=>10),"documento" => array("N"=>"documento","T"=>"BIGINT","V"=>"0","L"=>20),"recibo" => array("N"=>"recibo","T"=>"BIGINT","V"=>"0","L"=>25),"tipo_de_movimiento" => array("N"=>"tipo_de_movimiento","T"=>"INT","V"=>"0","L"=>4),"tipo_de_exposicion" => array("N"=>"tipo_de_exposicion","T"=>"VARCHAR","V"=>"ninguno","L"=>25),"fecha" => array("N"=>"fecha","T"=>"DATE","V"=>"","L"=>0),"hora" => array("N"=>"hora","T"=>"TIME","V"=>"","L"=>0),"monto_del_movimiento" => array("N"=>"monto_del_movimiento","T"=>"DOUBLE","V"=>"0.00","L"=>33),"monto_recibido" => array("N"=>"monto_recibido","T"=>"DOUBLE","V"=>"0.00","L"=>33),"monto_en_cambio" => array("N"=>"monto_en_cambio","T"=>"DOUBLE","V"=>"0.00","L"=>33),"banco" => array("N"=>"banco","T"=>"INT","V"=>"999","L"=>4),"numero_de_cheque" => array("N"=>"numero_de_cheque","T"=>"VARCHAR","V"=>"0","L"=>20),"observaciones" => array("N"=>"observaciones","T"=>"VARCHAR","V"=>"","L"=>100),"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"matriz","L"=>20),"eacp" => array("N"=>"eacp","T"=>"VARCHAR","V"=>"","L"=>20),"cuenta_bancaria" => array("N"=>"cuenta_bancaria","T"=>"BIGINT","V"=>"0","L"=>20),"documento_descontado" => array("N"=>"documento_descontado","T"=>"BIGINT","V"=>"0","L"=>20),"moneda_de_operacion" => array("N"=>"moneda_de_operacion","T"=>"VARCHAR","V"=>"MXN","L"=>10),"unidades_de_moneda" => array("N"=>"unidades_de_moneda","T"=>"DOUBLE","V"=>"0.0000","L"=>33),"persona" => array("N"=>"persona","T"=>"BIGINT","V"=>"1","L"=>20),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "tesoreria_cajas_movimientos";}
	function getKey(){ return "idtesoreria_cajas_movimientos";}
	function idtesoreria_cajas_movimientos($v = false){ if($v !== false){$this->mCampos["idtesoreria_cajas_movimientos"]["V"] =  $v; } return new MQLCampo($this->mCampos["idtesoreria_cajas_movimientos"]);}
	function codigo_de_caja($v = false){ if($v !== false){$this->mCampos["codigo_de_caja"]["V"] =  $v; } return new MQLCampo($this->mCampos["codigo_de_caja"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function documento($v = false){ if($v !== false){$this->mCampos["documento"]["V"] =  $v; } return new MQLCampo($this->mCampos["documento"]);}
	function recibo($v = false){ if($v !== false){$this->mCampos["recibo"]["V"] =  $v; } return new MQLCampo($this->mCampos["recibo"]);}
	function tipo_de_movimiento($v = false){ if($v !== false){$this->mCampos["tipo_de_movimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_movimiento"]);}
	function tipo_de_exposicion($v = false){ if($v !== false){$this->mCampos["tipo_de_exposicion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_exposicion"]);}
	function fecha($v = false){ if($v !== false){$this->mCampos["fecha"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha"]);}
	function hora($v = false){ if($v !== false){$this->mCampos["hora"]["V"] =  $v; } return new MQLCampo($this->mCampos["hora"]);}
	function monto_del_movimiento($v = false){ if($v !== false){$this->mCampos["monto_del_movimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_del_movimiento"]);}
	function monto_recibido($v = false){ if($v !== false){$this->mCampos["monto_recibido"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_recibido"]);}
	function monto_en_cambio($v = false){ if($v !== false){$this->mCampos["monto_en_cambio"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_en_cambio"]);}
	function banco($v = false){ if($v !== false){$this->mCampos["banco"]["V"] =  $v; } return new MQLCampo($this->mCampos["banco"]);}
	function numero_de_cheque($v = false){ if($v !== false){$this->mCampos["numero_de_cheque"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_de_cheque"]);}
	function observaciones($v = false){ if($v !== false){$this->mCampos["observaciones"]["V"] =  $v; } return new MQLCampo($this->mCampos["observaciones"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function eacp($v = false){ if($v !== false){$this->mCampos["eacp"]["V"] =  $v; } return new MQLCampo($this->mCampos["eacp"]);}
	function cuenta_bancaria($v = false){ if($v !== false){$this->mCampos["cuenta_bancaria"]["V"] =  $v; } return new MQLCampo($this->mCampos["cuenta_bancaria"]);}
	function documento_descontado($v = false){ if($v !== false){$this->mCampos["documento_descontado"]["V"] =  $v; } return new MQLCampo($this->mCampos["documento_descontado"]);}
	function moneda_de_operacion($v = false){ if($v !== false){$this->mCampos["moneda_de_operacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["moneda_de_operacion"]);}
	function unidades_de_moneda($v = false){ if($v !== false){$this->mCampos["unidades_de_moneda"]["V"] =  $v; } return new MQLCampo($this->mCampos["unidades_de_moneda"]);}
	function persona($v = false){ if($v !== false){$this->mCampos["persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["persona"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}


/*	ORM: Tabla:	tesoreria_tipos_de_pago	-	Generado:	[19/1/2015 15:30]	*/
/*	ORM: Tabla:	tesoreria_tipos_de_pago	-	Generado:	[01/12/2016 17:53]	*/
/*	ORM: Tabla:	tesoreria_tipos_de_pago	-	Generado:	[31/10/2017 19:20]	*/
class cTesoreria_tipos_de_pago {
	private $mCampos	= array("tipo_de_pago" => array("N"=>"tipo_de_pago","T"=>"VARCHAR","V"=>"","L"=>25),"tipo_de_movimiento" => array("N"=>"tipo_de_movimiento","T"=>"INT","V"=>"0","L"=>4),"descripcion" => array("N"=>"descripcion","T"=>"VARCHAR","V"=>"","L"=>45),"descripcion_completa" => array("N"=>"descripcion_completa","T"=>"VARCHAR","V"=>"","L"=>100),"equivalente_aml" => array("N"=>"equivalente_aml","T"=>"INT","V"=>"0","L"=>5),"activo" => array("N"=>"activo","T"=>"INT","V"=>"1","L"=>2),"formato" => array("N"=>"formato","T"=>"VARCHAR","V"=>"","L"=>50),"eq_contable" => array("N"=>"eq_contable","T"=>"INT","V"=>"99","L"=>6),"admitidos" => array("N"=>"admitidos","T"=>"VARCHAR","V"=>"2@rw,3@rw,4@rw,5@rw,6@rw,7@rw,8@rw,9@rw,10@rw,11@rw,12@rw,13@rw,14@rw,15@rw,99@rw,31@rw,41@rw,71@rw,81@rw,31@rw,41@rw,71@rw","L"=>200),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "tesoreria_tipos_de_pago";}
	function getKey(){ return "tipo_de_pago";}
	function tipo_de_pago($v = false){ if($v !== false){$this->mCampos["tipo_de_pago"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_pago"]);}
	function tipo_de_movimiento($v = false){ if($v !== false){$this->mCampos["tipo_de_movimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_movimiento"]);}
	function descripcion($v = false){ if($v !== false){$this->mCampos["descripcion"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion"]);}
	function descripcion_completa($v = false){ if($v !== false){$this->mCampos["descripcion_completa"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_completa"]);}
	function equivalente_aml($v = false){ if($v !== false){$this->mCampos["equivalente_aml"]["V"] =  $v; } return new MQLCampo($this->mCampos["equivalente_aml"]);}
	function activo($v = false){ if($v !== false){$this->mCampos["activo"]["V"] =  $v; } return new MQLCampo($this->mCampos["activo"]);}
	function formato($v = false){ if($v !== false){$this->mCampos["formato"]["V"] =  $v; } return new MQLCampo($this->mCampos["formato"]);}
	function eq_contable($v = false){ if($v !== false){$this->mCampos["eq_contable"]["V"] =  $v; } return new MQLCampo($this->mCampos["eq_contable"]);}
	function admitidos($v = false){ if($v !== false){$this->mCampos["admitidos"]["V"] =  $v; } return new MQLCampo($this->mCampos["admitidos"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	tesoreria_monedas	-	Generado:	[08/3/2014 10:49]	*/
/*	ORM: Tabla:	tesoreria_monedas	-	Generado:	[25/10/2016 13:02]	*/
class cTesoreria_monedas {
	private $mCampos	= array("clave_de_moneda" => array("N"=>"clave_de_moneda","T"=>"VARCHAR","V"=>"","L"=>6),"nombre_de_la_moneda" => array("N"=>"nombre_de_la_moneda","T"=>"VARCHAR","V"=>"","L"=>100),"quivalencia_en_moneda_local" => array("N"=>"quivalencia_en_moneda_local","T"=>"FLOAT","V"=>"0.0000","L"=>17),"pais_de_origen" => array("N"=>"pais_de_origen","T"=>"VARCHAR","V"=>"MX","L"=>4),"instrumento" => array("N"=>"instrumento","T"=>"INT","V"=>"1","L"=>4),"simbolo" => array("N"=>"simbolo","T"=>"VARCHAR","V"=>"","L"=>4),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "tesoreria_monedas";}
	function getKey(){ return "clave_de_moneda";}
	function clave_de_moneda($v = false){ if($v !== false){$this->mCampos["clave_de_moneda"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_moneda"]);}
	function nombre_de_la_moneda($v = false){ if($v !== false){$this->mCampos["nombre_de_la_moneda"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_de_la_moneda"]);}
	function quivalencia_en_moneda_local($v = false){ if($v !== false){$this->mCampos["quivalencia_en_moneda_local"]["V"] =  $v; } return new MQLCampo($this->mCampos["quivalencia_en_moneda_local"]);}
	function pais_de_origen($v = false){ if($v !== false){$this->mCampos["pais_de_origen"]["V"] =  $v; } return new MQLCampo($this->mCampos["pais_de_origen"]);}
	function instrumento($v = false){ if($v !== false){$this->mCampos["instrumento"]["V"] =  $v; } return new MQLCampo($this->mCampos["instrumento"]);}
	function simbolo($v = false){ if($v !== false){$this->mCampos["simbolo"]["V"] =  $v; } return new MQLCampo($this->mCampos["simbolo"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	tesoreria_tipos_de_denominaciones	-	Generado:	[25/10/2016 12:16]	*/
class cTesoreria_tipos_de_denominaciones {
	private $mCampos	= array("denominacion" => array("N"=>"denominacion","T"=>"INT","V"=>"","L"=>11),"nombre" => array("N"=>"nombre","T"=>"VARCHAR","V"=>"","L"=>45),"valor_contra_uno" => array("N"=>"valor_contra_uno","T"=>"FLOAT","V"=>"","L"=>25),"tipo_de_valor" => array("N"=>"tipo_de_valor","T"=>"VARCHAR","V"=>"","L"=>20),);
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

/*	ORM: Tabla:	tesoreria_tipos_de_denominaciones	-	Generado:	[09/9/2014 12:05]	*/

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

/*	ORM: Tabla:	tesoreria_valoracion_diaria	-	Generado:	[25/10/2016 12:12]	*/
class cTesoreria_valoracion_diaria {
	private $mCampos	= array("idcontrol" => array("N"=>"idcontrol","T"=>"INT","V"=>"","L"=>11),"denominacion" => array("N"=>"denominacion","T"=>"VARCHAR","V"=>"","L"=>10),"fecha" => array("N"=>"fecha","T"=>"DATE","V"=>"","L"=>0),"valor" => array("N"=>"valor","T"=>"FLOAT","V"=>"","L"=>23),"tiempo" => array("N"=>"tiempo","T"=>"INT","V"=>"0","L"=>11),"usuario" => array("N"=>"usuario","T"=>"INT","V"=>"0","L"=>8),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "tesoreria_valoracion_diaria";}
	function getKey(){ return "idcontrol";}
	function idcontrol($v = false){ if($v !== false){$this->mCampos["idcontrol"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcontrol"]);}
	function denominacion($v = false){ if($v !== false){$this->mCampos["denominacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["denominacion"]);}
	function fecha($v = false){ if($v !== false){$this->mCampos["fecha"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha"]);}
	function valor($v = false){ if($v !== false){$this->mCampos["valor"]["V"] =  $v; } return new MQLCampo($this->mCampos["valor"]);}
	function tiempo($v = false){ if($v !== false){$this->mCampos["tiempo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tiempo"]);}
	function usuario($v = false){ if($v !== false){$this->mCampos["usuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["usuario"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*--------------------------------------------------------------------------- END Tesoreria*/


/*	ORM: Tabla:	general_menu	-	Generado:	[21/1/2014 11:20]	*/
/*	ORM: Tabla:	general_menu	-	Generado:	[22/9/2016 13:37]	*/
/*	ORM: Tabla:	general_menu	-	Generado:	[23/4/2018 16:58]	*/
class cGeneral_menu {
	private $mCampos	= array("idgeneral_menu" => array("N"=>"idgeneral_menu","T"=>"INT","V"=>"","L"=>10),"menu_parent" => array("N"=>"menu_parent","T"=>"INT","V"=>"9999","L"=>4),"menu_title" => array("N"=>"menu_title","T"=>"VARCHAR","V"=>"","L"=>45),"menu_file" => array("N"=>"menu_file","T"=>"VARCHAR","V"=>"404.php","L"=>100),"menu_destination" => array("N"=>"menu_destination","T"=>"VARCHAR","V"=>"principal","L"=>45),"menu_description" => array("N"=>"menu_description","T"=>"VARCHAR","V"=>"","L"=>150),"menu_image" => array("N"=>"menu_image","T"=>"VARCHAR","V"=>"null.png","L"=>45),"menu_rules" => array("N"=>"menu_rules","T"=>"VARCHAR","V"=>"2@rw,3@rw,4@rw,5@rw,6@rw,7@rw,8@rw,9@rw,10@rw,11@rw,12@rw,13@rw,14@rw,15@rw,99@rw,31@rw,41@rw,71@rw,81@rw,31@rw,41@rw,71@rw","L"=>200),"menu_type" => array("N"=>"menu_type","T"=>"ENUM","V"=>"|general|command|parent|","L"=>0),"menu_order" => array("N"=>"menu_order","T"=>"INT","V"=>"0","L"=>5),"menu_help_id" => array("N"=>"menu_help_id","T"=>"INT","V"=>"9999","L"=>6),"menu_showin_toolbar" => array("N"=>"menu_showin_toolbar","T"=>"ENUM","V"=>"|false|true|","L"=>0));
	public $IDGENERAL_MENU = "idgeneral_menu"; public $MENU_PARENT = "menu_parent"; public $MENU_TITLE = "menu_title"; public $MENU_FILE = "menu_file"; public $MENU_DESTINATION = "menu_destination"; public $MENU_DESCRIPTION = "menu_description"; public $MENU_IMAGE = "menu_image"; public $MENU_RULES = "menu_rules"; public $MENU_TYPE = "menu_type"; public $MENU_ORDER = "menu_order"; public $MENU_HELP_ID = "menu_help_id"; public $MENU_SHOWIN_TOOLBAR = "menu_showin_toolbar";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "general_menu";}
	function getKey(){ return "idgeneral_menu";}
	function idgeneral_menu($v = false){ if($v !== false){$this->mCampos["idgeneral_menu"]["V"] =  $v; } return new MQLCampo($this->mCampos["idgeneral_menu"]);}
	function menu_parent($v = false){ if($v !== false){$this->mCampos["menu_parent"]["V"] =  $v; } return new MQLCampo($this->mCampos["menu_parent"]);}
	function menu_title($v = false){ if($v !== false){$this->mCampos["menu_title"]["V"] =  $v; } return new MQLCampo($this->mCampos["menu_title"]);}
	function menu_file($v = false){ if($v !== false){$this->mCampos["menu_file"]["V"] =  $v; } return new MQLCampo($this->mCampos["menu_file"]);}
	function menu_destination($v = false){ if($v !== false){$this->mCampos["menu_destination"]["V"] =  $v; } return new MQLCampo($this->mCampos["menu_destination"]);}
	function menu_description($v = false){ if($v !== false){$this->mCampos["menu_description"]["V"] =  $v; } return new MQLCampo($this->mCampos["menu_description"]);}
	function menu_image($v = false){ if($v !== false){$this->mCampos["menu_image"]["V"] =  $v; } return new MQLCampo($this->mCampos["menu_image"]);}
	function menu_rules($v = false){ if($v !== false){$this->mCampos["menu_rules"]["V"] =  $v; } return new MQLCampo($this->mCampos["menu_rules"]);}
	function menu_type($v = false){ if($v !== false){$this->mCampos["menu_type"]["V"] =  $v; } return new MQLCampo($this->mCampos["menu_type"]);}
	function menu_order($v = false){ if($v !== false){$this->mCampos["menu_order"]["V"] =  $v; } return new MQLCampo($this->mCampos["menu_order"]);}
	function menu_help_id($v = false){ if($v !== false){$this->mCampos["menu_help_id"]["V"] =  $v; } return new MQLCampo($this->mCampos["menu_help_id"]);}
	function menu_showin_toolbar($v = false){ if($v !== false){$this->mCampos["menu_showin_toolbar"]["V"] =  $v; } return new MQLCampo($this->mCampos["menu_showin_toolbar"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}

//================================================================================= END AML

/*	ORM: Tabla:	aml_alerts	-	Generado:	[15/12/2014 14:11]	*/
/*	ORM: Tabla:	aml_alerts	-	Generado:	[13/5/2017 12:23]	*/
/*	ORM: Tabla:	aml_alerts	-	Generado:	[16/5/2017 16:10]	*/
/*	ORM: Tabla:	aml_alerts	-	Generado:	[29/11/2017 18:20]	*/
class cAml_alerts {
	private $mCampos	= array("clave_de_control" => array("N"=>"clave_de_control","T"=>"INT","V"=>"","L"=>11),"tipo_de_aviso" => array("N"=>"tipo_de_aviso","T"=>"INT","V"=>"1","L"=>11),"persona_de_destino" => array("N"=>"persona_de_destino","T"=>"BIGINT","V"=>"1","L"=>20),"documento_relacionado" => array("N"=>"documento_relacionado","T"=>"BIGINT","V"=>"1","L"=>20),"persona_de_origen" => array("N"=>"persona_de_origen","T"=>"BIGINT","V"=>"1","L"=>20),"fecha_de_origen" => array("N"=>"fecha_de_origen","T"=>"INT","V"=>"0","L"=>11),"fecha_de_checking" => array("N"=>"fecha_de_checking","T"=>"INT","V"=>"0","L"=>11),"hora_de_proceso" => array("N"=>"hora_de_proceso","T"=>"INT","V"=>"0","L"=>11),"medio_de_envio" => array("N"=>"medio_de_envio","T"=>"VARCHAR","V"=>"MAIL","L"=>20),"estado_en_sistema" => array("N"=>"estado_en_sistema","T"=>"INT","V"=>"1","L"=>11),"riesgo_calificado" => array("N"=>"riesgo_calificado","T"=>"INT","V"=>"0","L"=>11),"mensaje" => array("N"=>"mensaje","T"=>"MEDIUMTEXT","V"=>"","L"=>0),"usuario" => array("N"=>"usuario","T"=>"INT","V"=>"1","L"=>8),"sucursal" => array("N"=>"sucursal","T"=>"INT","V"=>"1","L"=>4),"entidad" => array("N"=>"entidad","T"=>"INT","V"=>"1","L"=>11),"fecha_de_registro" => array("N"=>"fecha_de_registro","T"=>"INT","V"=>"0","L"=>11),"notas_de_checking" => array("N"=>"notas_de_checking","T"=>"TEXT","V"=>"","L"=>0),"tipo_de_documento" => array("N"=>"tipo_de_documento","T"=>"INT","V"=>"0","L"=>5),"tercero_relacionado" => array("N"=>"tercero_relacionado","T"=>"BIGINT","V"=>"1","L"=>20),"resultado_de_checking" => array("N"=>"resultado_de_checking","T"=>"INT","V"=>"0","L"=>2),"usuario_checking" => array("N"=>"usuario_checking","T"=>"INT","V"=>"0","L"=>8),"envio_rms" => array("N"=>"envio_rms","T"=>"INT","V"=>"0","L"=>10));
	public $CLAVE_DE_CONTROL	= "clave_de_control";
	public $TIPO_DE_AVISO	= "tipo_de_aviso";
	public $PERSONA_DE_DESTINO	= "persona_de_destino";
	public $DOCUMENTO_RELACIONADO	= "documento_relacionado";
	public $PERSONA_DE_ORIGEN	= "persona_de_origen";
	public $FECHA_DE_ORIGEN	= "fecha_de_origen";
	public $FECHA_DE_CHECKING	= "fecha_de_checking";
	public $HORA_DE_PROCESO	= "hora_de_proceso";
	public $MEDIO_DE_ENVIO	= "medio_de_envio";
	public $ESTADO_EN_SISTEMA	= "estado_en_sistema";
	public $RIESGO_CALIFICADO	= "riesgo_calificado";
	public $MENSAJE	= "mensaje";
	public $USUARIO	= "usuario";
	public $SUCURSAL	= "sucursal";
	public $ENTIDAD	= "entidad";
	public $FECHA_DE_REGISTRO	= "fecha_de_registro";
	public $NOTAS_DE_CHECKING	= "notas_de_checking";
	public $TIPO_DE_DOCUMENTO	= "tipo_de_documento";
	public $TERCERO_RELACIONADO	= "tercero_relacionado";
	public $RESULTADO_DE_CHECKING	= "resultado_de_checking";
	public $USUARIO_CHECKING	= "usuario_checking";
	public $ENVIO_RMS	= "envio_rms";
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
	function usuario_checking($v = false){ if($v !== false){$this->mCampos["usuario_checking"]["V"] =  $v; } return new MQLCampo($this->mCampos["usuario_checking"]);}
	function envio_rms($v = false){ if($v !== false){$this->mCampos["envio_rms"]["V"] =  $v; } return new MQLCampo($this->mCampos["envio_rms"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}

/*	ORM: Tabla:	aml_risk_register	-	Generado:	[20/1/2015 17:48]	*/
/*	ORM: Tabla:	aml_risk_register	-	Generado:	[16/5/2017 16:11]	*/
class cAml_risk_register {
	private $mCampos	= array("clave_de_riesgo" => array("N"=>"clave_de_riesgo","T"=>"INT","V"=>"","L"=>11),"tipo_de_riesgo" => array("N"=>"tipo_de_riesgo","T"=>"INT","V"=>"0","L"=>11),"persona_relacionada" => array("N"=>"persona_relacionada","T"=>"BIGINT","V"=>"0","L"=>20),"fecha_de_reporte" => array("N"=>"fecha_de_reporte","T"=>"BIGINT","V"=>"0","L"=>20),"hora_de_reporte" => array("N"=>"hora_de_reporte","T"=>"BIGINT","V"=>"0","L"=>20),"escore" => array("N"=>"escore","T"=>"DOUBLE","V"=>"0.000000","L"=>37),"usuario_de_origen" => array("N"=>"usuario_de_origen","T"=>"INT","V"=>"0","L"=>11),"documento_relacionado" => array("N"=>"documento_relacionado","T"=>"BIGINT","V"=>"0","L"=>20),"tipo_de_documento" => array("N"=>"tipo_de_documento","T"=>"INT","V"=>"1","L"=>4),"instrumento_financiero" => array("N"=>"instrumento_financiero","T"=>"INT","V"=>"","L"=>11),"fecha_de_envio" => array("N"=>"fecha_de_envio","T"=>"BIGINT","V"=>"0","L"=>20),"estado_de_envio" => array("N"=>"estado_de_envio","T"=>"INT","V"=>"","L"=>11),"fecha_de_checking" => array("N"=>"fecha_de_checking","T"=>"BIGINT","V"=>"","L"=>20),"oficial_de_checking" => array("N"=>"oficial_de_checking","T"=>"INT","V"=>"","L"=>11),"firma_de_checking" => array("N"=>"firma_de_checking","T"=>"TINYTEXT","V"=>"","L"=>0),"monto_total_relacionado" => array("N"=>"monto_total_relacionado","T"=>"DOUBLE","V"=>"0.000","L"=>37),"metadata" => array("N"=>"metadata","T"=>"TEXT","V"=>"","L"=>0),"notas_de_checking" => array("N"=>"notas_de_checking","T"=>"VARCHAR","V"=>"","L"=>200),"tipo_de_operacion" => array("N"=>"tipo_de_operacion","T"=>"VARCHAR","V"=>"","L"=>4),"razones_de_reporte" => array("N"=>"razones_de_reporte","T"=>"TEXT","V"=>"","L"=>0),"acciones_tomadas" => array("N"=>"acciones_tomadas","T"=>"TEXT","V"=>"","L"=>0),"tercero_relacionado" => array("N"=>"tercero_relacionado","T"=>"BIGINT","V"=>"1","L"=>20),"mensajes_del_sistema" => array("N"=>"mensajes_del_sistema","T"=>"MEDIUMTEXT","V"=>"","L"=>0),"reporte_inmediato" => array("N"=>"reporte_inmediato","T"=>"INT","V"=>"","L"=>3),);
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
/*	ORM: Tabla:	aml_risk_catalog	-	Generado:	[25/10/2016 14:18]	*/
class cAml_risk_catalog {
	private $mCampos	= array("clave_de_control" => array("N"=>"clave_de_control","T"=>"INT","V"=>"","L"=>11),"descripcion" => array("N"=>"descripcion","T"=>"VARCHAR","V"=>"","L"=>150),"tipo_de_riesgo" => array("N"=>"tipo_de_riesgo","T"=>"INT","V"=>"1","L"=>11),"valor_ponderado" => array("N"=>"valor_ponderado","T"=>"DOUBLE","V"=>"0.000000","L"=>37),"unidades_ponderadas" => array("N"=>"unidades_ponderadas","T"=>"DOUBLE","V"=>"0.0000","L"=>37),"unidad_de_medida" => array("N"=>"unidad_de_medida","T"=>"VARCHAR","V"=>"","L"=>10),"forma_de_reportar" => array("N"=>"forma_de_reportar","T"=>"VARCHAR","V"=>"C","L"=>4),"frecuencia_de_chequeo" => array("N"=>"frecuencia_de_chequeo","T"=>"VARCHAR","V"=>"D","L"=>4),"fundamento_legal" => array("N"=>"fundamento_legal","T"=>"MEDIUMTEXT","V"=>"","L"=>0),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "aml_risk_catalog";}
	function getKey(){ return "clave_de_control";}
	function clave_de_control($v = false){ if($v !== false){$this->mCampos["clave_de_control"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_control"]);}
	function descripcion($v = false){ if($v !== false){$this->mCampos["descripcion"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion"]);}
	function tipo_de_riesgo($v = false){ if($v !== false){$this->mCampos["tipo_de_riesgo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_riesgo"]);}
	function valor_ponderado($v = false){ if($v !== false){$this->mCampos["valor_ponderado"]["V"] =  $v; } return new MQLCampo($this->mCampos["valor_ponderado"]);}
	function unidades_ponderadas($v = false){ if($v !== false){$this->mCampos["unidades_ponderadas"]["V"] =  $v; } return new MQLCampo($this->mCampos["unidades_ponderadas"]);}
	function unidad_de_medida($v = false){ if($v !== false){$this->mCampos["unidad_de_medida"]["V"] =  $v; } return new MQLCampo($this->mCampos["unidad_de_medida"]);}
	function forma_de_reportar($v = false){ if($v !== false){$this->mCampos["forma_de_reportar"]["V"] =  $v; } return new MQLCampo($this->mCampos["forma_de_reportar"]);}
	function frecuencia_de_chequeo($v = false){ if($v !== false){$this->mCampos["frecuencia_de_chequeo"]["V"] =  $v; } return new MQLCampo($this->mCampos["frecuencia_de_chequeo"]);}
	function fundamento_legal($v = false){ if($v !== false){$this->mCampos["fundamento_legal"]["V"] =  $v; } return new MQLCampo($this->mCampos["fundamento_legal"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	aml_risk_levels	-	Generado:	[07/2/2014 12:25]	*/
/*	ORM: Tabla:	aml_risk_levels	-	Generado:	[25/10/2016 14:17]	*/
class cAml_risk_levels {
	private $mCampos	= array("clave_de_control" => array("N"=>"clave_de_control","T"=>"INT","V"=>"","L"=>11),"nombre_del_nivel" => array("N"=>"nombre_del_nivel","T"=>"VARCHAR","V"=>"","L"=>50),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "aml_risk_levels";}
	function getKey(){ return "clave_de_control";}
	function clave_de_control($v = false){ if($v !== false){$this->mCampos["clave_de_control"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_control"]);}
	function nombre_del_nivel($v = false){ if($v !== false){$this->mCampos["nombre_del_nivel"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_del_nivel"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	aml_risk_types	-	Generado:	[07/2/2014 13:40]	*/
/*	ORM: Tabla:	aml_risk_types	-	Generado:	[25/10/2016 14:17]	*/
class cAml_risk_types {
	private $mCampos	= array("clave_de_control" => array("N"=>"clave_de_control","T"=>"INT","V"=>"","L"=>11),"nombre_del_riesgo" => array("N"=>"nombre_del_riesgo","T"=>"VARCHAR","V"=>"0","L"=>100),"valor_ponderado" => array("N"=>"valor_ponderado","T"=>"DOUBLE","V"=>"","L"=>37),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "aml_risk_types";}
	function getKey(){ return "clave_de_control";}
	function clave_de_control($v = false){ if($v !== false){$this->mCampos["clave_de_control"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_control"]);}
	function nombre_del_riesgo($v = false){ if($v !== false){$this->mCampos["nombre_del_riesgo"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_del_riesgo"]);}
	function valor_ponderado($v = false){ if($v !== false){$this->mCampos["valor_ponderado"]["V"] =  $v; } return new MQLCampo($this->mCampos["valor_ponderado"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	aml_instrumentos_financieros	-	Generado:	[25/10/2016 14:16]	*/
class cAml_instrumentos_financieros {
	private $mCampos	= array("tipo_de_instrumento" => array("N"=>"tipo_de_instrumento","T"=>"INT","V"=>"","L"=>11),"nombre_de_instrumento" => array("N"=>"nombre_de_instrumento","T"=>"VARCHAR","V"=>"","L"=>50),"descripcion" => array("N"=>"descripcion","T"=>"VARCHAR","V"=>"","L"=>200),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "aml_instrumentos_financieros";}
	function getKey(){ return "tipo_de_instrumento";}
	function tipo_de_instrumento($v = false){ if($v !== false){$this->mCampos["tipo_de_instrumento"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_instrumento"]);}
	function nombre_de_instrumento($v = false){ if($v !== false){$this->mCampos["nombre_de_instrumento"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_de_instrumento"]);}
	function descripcion($v = false){ if($v !== false){$this->mCampos["descripcion"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}


/*	ORM: Tabla:	aml_riesgo_matrices	-	Generado:	[14/11/2014 11:20]	*/
/*	ORM: Tabla:	aml_riesgo_matrices	-	Generado:	[20/10/2016 11:31]	*/
/*	ORM: Tabla:	aml_riesgo_matrices	-	Generado:	[17/11/2016 11:27]	*/
class cAml_riesgo_matrices {
	private $mCampos	= array("idaml_riesgo_matrices" => array("N"=>"idaml_riesgo_matrices","T"=>"INT","V"=>"","L"=>11),"nombre" => array("N"=>"nombre","T"=>"VARCHAR","V"=>"","L"=>40),"clasificacion" => array("N"=>"clasificacion","T"=>"VARCHAR","V"=>"","L"=>40),"descripcion" => array("N"=>"descripcion","T"=>"VARCHAR","V"=>"","L"=>100),"clave_riesgo" => array("N"=>"clave_riesgo","T"=>"INT","V"=>"0","L"=>10),"riesgo" => array("N"=>"riesgo","T"=>"INT","V"=>"0","L"=>4),"define" => array("N"=>"define","T"=>"VARCHAR","V"=>"","L"=>20),"estatus" => array("N"=>"estatus","T"=>"INT","V"=>"","L"=>2),"usuario" => array("N"=>"usuario","T"=>"INT","V"=>"0","L"=>6),"tiempo" => array("N"=>"tiempo","T"=>"INT","V"=>"0","L"=>10),"finalizador" => array("N"=>"finalizador","T"=>"INT","V"=>"0","L"=>2),"probabilidad" => array("N"=>"probabilidad","T"=>"INT","V"=>"1","L"=>4),"impacto" => array("N"=>"impacto","T"=>"FLOAT","V"=>"1.0000","L"=>17),"consecuencia" => array("N"=>"consecuencia","T"=>"INT","V"=>"1","L"=>4),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "aml_riesgo_matrices";}
	function getKey(){ return "idaml_riesgo_matrices";}
	function idaml_riesgo_matrices($v = false){ if($v !== false){$this->mCampos["idaml_riesgo_matrices"]["V"] =  $v; } return new MQLCampo($this->mCampos["idaml_riesgo_matrices"]);}
	function nombre($v = false){ if($v !== false){$this->mCampos["nombre"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre"]);}
	function clasificacion($v = false){ if($v !== false){$this->mCampos["clasificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["clasificacion"]);}
	function descripcion($v = false){ if($v !== false){$this->mCampos["descripcion"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion"]);}
	function clave_riesgo($v = false){ if($v !== false){$this->mCampos["clave_riesgo"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_riesgo"]);}
	function riesgo($v = false){ if($v !== false){$this->mCampos["riesgo"]["V"] =  $v; } return new MQLCampo($this->mCampos["riesgo"]);}
	function define($v = false){ if($v !== false){$this->mCampos["define"]["V"] =  $v; } return new MQLCampo($this->mCampos["define"]);}
	function estatus($v = false){ if($v !== false){$this->mCampos["estatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus"]);}
	function usuario($v = false){ if($v !== false){$this->mCampos["usuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["usuario"]);}
	function tiempo($v = false){ if($v !== false){$this->mCampos["tiempo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tiempo"]);}
	function finalizador($v = false){ if($v !== false){$this->mCampos["finalizador"]["V"] =  $v; } return new MQLCampo($this->mCampos["finalizador"]);}
	function probabilidad($v = false){ if($v !== false){$this->mCampos["probabilidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["probabilidad"]);}
	function impacto($v = false){ if($v !== false){$this->mCampos["impacto"]["V"] =  $v; } return new MQLCampo($this->mCampos["impacto"]);}
	function consecuencia($v = false){ if($v !== false){$this->mCampos["consecuencia"]["V"] =  $v; } return new MQLCampo($this->mCampos["consecuencia"]);}
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
/*	ORM: Tabla:	aml_listanegra_int	-	Generado:	[22/9/2016 13:26]	*/
/*	ORM: Tabla:	aml_listanegra_int	-	Generado:	[22/9/2016 18:37]	*/
class cAml_listanegra_int {
	private $mCampos	= array(
			"clave_interna" => array("N"=>"clave_interna","T"=>"INT","V"=>"","L"=>11),
			"persona" => array("N"=>"persona","T"=>"BIGINT","V"=>"","L"=>25),
			"fecha_de_registro" => array("N"=>"fecha_de_registro","T"=>"DATE","V"=>"2015-01-01","L"=>0),
			"fecha_de_vencimiento" => array("N"=>"fecha_de_vencimiento","T"=>"DATE","V"=>"2015-01-01","L"=>0),
			"riesgo" => array("N"=>"riesgo","T"=>"INT","V"=>"0","L"=>4),
			"observaciones" => array("N"=>"observaciones","T"=>"VARCHAR","V"=>"","L"=>100),
			"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"matriz","L"=>20),
			"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"1","L"=>6),
			"idmotivo" => array("N"=>"idmotivo","T"=>"INT","V"=>"0","L"=>8),
			"estatus" => array("N"=>"estatus","T"=>"INT","V"=>"1","L"=>2),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "aml_listanegra_int";}
	function getKey(){ return "clave_interna";}
	function clave_interna($v = false){ if($v !== false){$this->mCampos["clave_interna"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_interna"]);}
	function persona($v = false){ if($v !== false){$this->mCampos["persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["persona"]);}
	function fecha_de_registro($v = false){ if($v !== false){$this->mCampos["fecha_de_registro"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_registro"]);}
	function fecha_de_vencimiento($v = false){ if($v !== false){$this->mCampos["fecha_de_vencimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_vencimiento"]);}
	function riesgo($v = false){ if($v !== false){$this->mCampos["riesgo"]["V"] =  $v; } return new MQLCampo($this->mCampos["riesgo"]);}
	function observaciones($v = false){ if($v !== false){$this->mCampos["observaciones"]["V"] =  $v; } return new MQLCampo($this->mCampos["observaciones"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function idmotivo($v = false){ if($v !== false){$this->mCampos["idmotivo"]["V"] =  $v; } return new MQLCampo($this->mCampos["idmotivo"]);}
	function estatus($v = false){ if($v !== false){$this->mCampos["estatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}


//================================================================================= END AML
//================================================================================= RIESGOS
/*	ORM: Tabla:	riesgos_probabilidad	-	Generado:	[17/11/2016 10:33]	*/
class cRiesgos_probabilidad {
	private $mCampos	= array("idriesgos_probabilidad" => array("N"=>"idriesgos_probabilidad","T"=>"INT","V"=>"","L"=>4),"nombre_probabilidad" => array("N"=>"nombre_probabilidad","T"=>"VARCHAR","V"=>"","L"=>20),"multiplo" => array("N"=>"multiplo","T"=>"FLOAT","V"=>"1.0000","L"=>13),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "riesgos_probabilidad";}
	function getKey(){ return "idriesgos_probabilidad";}
	function idriesgos_probabilidad($v = false){ if($v !== false){$this->mCampos["idriesgos_probabilidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["idriesgos_probabilidad"]);}
	function nombre_probabilidad($v = false){ if($v !== false){$this->mCampos["nombre_probabilidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_probabilidad"]);}
	function multiplo($v = false){ if($v !== false){$this->mCampos["multiplo"]["V"] =  $v; } return new MQLCampo($this->mCampos["multiplo"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}
/*	ORM: Tabla:	riesgos_consecuencias	-	Generado:	[17/11/2016 11:21]	*/
class cRiesgos_consecuencias {
	private $mCampos	= array("idriesgos_consecuencias" => array("N"=>"idriesgos_consecuencias","T"=>"INT","V"=>"","L"=>11),"nombre_consecuencia" => array("N"=>"nombre_consecuencia","T"=>"VARCHAR","V"=>"","L"=>40),"multiplo" => array("N"=>"multiplo","T"=>"FLOAT","V"=>"1.0000","L"=>13),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "riesgos_consecuencias";}
	function getKey(){ return "idriesgos_consecuencias";}
	function idriesgos_consecuencias($v = false){ if($v !== false){$this->mCampos["idriesgos_consecuencias"]["V"] =  $v; } return new MQLCampo($this->mCampos["idriesgos_consecuencias"]);}
	function nombre_consecuencia($v = false){ if($v !== false){$this->mCampos["nombre_consecuencia"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_consecuencia"]);}
	function multiplo($v = false){ if($v !== false){$this->mCampos["multiplo"]["V"] =  $v; } return new MQLCampo($this->mCampos["multiplo"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}
/*	ORM: Tabla:	aml_riesgo_producto	-	Generado:	[05/12/2016 14:27]	*/
class cAml_riesgo_producto {
	private $mCampos	= array("idaml_riesgo_producto" => array("N"=>"idaml_riesgo_producto","T"=>"INT","V"=>"","L"=>11),"tipo_de_producto" => array("N"=>"tipo_de_producto","T"=>"INT","V"=>"","L"=>3),"clave_de_producto" => array("N"=>"clave_de_producto","T"=>"INT","V"=>"","L"=>6),"nivel_de_riesgo" => array("N"=>"nivel_de_riesgo","T"=>"INT","V"=>"","L"=>4),"observaciones" => array("N"=>"observaciones","T"=>"VARCHAR","V"=>"","L"=>50),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "aml_riesgo_producto";}
	function getKey(){ return "idaml_riesgo_producto";}
	function idaml_riesgo_producto($v = false){ if($v !== false){$this->mCampos["idaml_riesgo_producto"]["V"] =  $v; } return new MQLCampo($this->mCampos["idaml_riesgo_producto"]);}
	function tipo_de_producto($v = false){ if($v !== false){$this->mCampos["tipo_de_producto"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_producto"]);}
	function clave_de_producto($v = false){ if($v !== false){$this->mCampos["clave_de_producto"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_producto"]);}
	function nivel_de_riesgo($v = false){ if($v !== false){$this->mCampos["nivel_de_riesgo"]["V"] =  $v; } return new MQLCampo($this->mCampos["nivel_de_riesgo"]);}
	function observaciones($v = false){ if($v !== false){$this->mCampos["observaciones"]["V"] =  $v; } return new MQLCampo($this->mCampos["observaciones"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

//================================================================================= END RIESGOS
/*	ORM: Tabla:	personas_share	-	Generado:	[02/5/2018 21:44]	*/
/*	ORM: Tabla:	personas_share	-	Generado:	[02/5/2018 22:12]	*/
class cPersonas_share {
	private $mCampos	= array("idpersonas_share" => array("N"=>"idpersonas_share","T"=>"INT","V"=>"","L"=>11),"persona_id" => array("N"=>"persona_id","T"=>"BIGINT","V"=>"","L"=>25),"personas_share_id" => array("N"=>"personas_share_id","T"=>"BIGINT","V"=>"","L"=>25),"tiempo" => array("N"=>"tiempo","T"=>"INT","V"=>"0","L"=>11),"url_share" => array("N"=>"url_share","T"=>"VARCHAR","V"=>"","L"=>150),"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"0","L"=>8));
	public $IDPERSONAS_SHARE = "idpersonas_share"; public $PERSONA_ID = "persona_id"; public $PERSONAS_SHARE_ID = "personas_share_id"; public $TIEMPO = "tiempo"; public $URL_SHARE = "url_share"; public $IDUSUARIO = "idusuario";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "personas_share";}
	function getKey(){ return "idpersonas_share";}
	function idpersonas_share($v = false){ if($v !== false){$this->mCampos["idpersonas_share"]["V"] =  $v; } return new MQLCampo($this->mCampos["idpersonas_share"]);}
	function persona_id($v = false){ if($v !== false){$this->mCampos["persona_id"]["V"] =  $v; } return new MQLCampo($this->mCampos["persona_id"]);}
	function personas_share_id($v = false){ if($v !== false){$this->mCampos["personas_share_id"]["V"] =  $v; } return new MQLCampo($this->mCampos["personas_share_id"]);}
	function tiempo($v = false){ if($v !== false){$this->mCampos["tiempo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tiempo"]);}
	function url_share($v = false){ if($v !== false){$this->mCampos["url_share"]["V"] =  $v; } return new MQLCampo($this->mCampos["url_share"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
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

/*	ORM: Tabla:	socios_general	-	Generado:	[14/12/2015 17:29]	*/
/*	ORM: Tabla:	socios_general	-	Generado:	[12/11/2016 12:42]	*/
/*	ORM: Tabla:	socios_general	-	Generado:	[02/3/2018 11:37]	*/
class cSocios_general {
	private $mCampos	= array("codigo" => array("N"=>"codigo","T"=>"BIGINT","V"=>"0","L"=>20),"nombrecompleto" => array("N"=>"nombrecompleto","T"=>"VARCHAR","V"=>"","L"=>200),"apellidopaterno" => array("N"=>"apellidopaterno","T"=>"VARCHAR","V"=>"","L"=>25),"apellidomaterno" => array("N"=>"apellidomaterno","T"=>"VARCHAR","V"=>"","L"=>25),"rfc" => array("N"=>"rfc","T"=>"VARCHAR","V"=>"POR_REGISTRAR","L"=>20),"curp" => array("N"=>"curp","T"=>"VARCHAR","V"=>"POR_REGISTRAR","L"=>20),"fechaentrevista" => array("N"=>"fechaentrevista","T"=>"DATE","V"=>"2005-12-31","L"=>0),"fechaalta" => array("N"=>"fechaalta","T"=>"DATE","V"=>"2005-12-31","L"=>0),"estatusactual" => array("N"=>"estatusactual","T"=>"INT","V"=>"99","L"=>4),"region" => array("N"=>"region","T"=>"INT","V"=>"99","L"=>4),"cajalocal" => array("N"=>"cajalocal","T"=>"INT","V"=>"99","L"=>4),"fechanacimiento" => array("N"=>"fechanacimiento","T"=>"DATE","V"=>"2005-12-31","L"=>0),"lugarnacimiento" => array("N"=>"lugarnacimiento","T"=>"VARCHAR","V"=>"POR_REGISTRAR","L"=>45),"tipoingreso" => array("N"=>"tipoingreso","T"=>"INT","V"=>"99","L"=>4),"estadocivil" => array("N"=>"estadocivil","T"=>"INT","V"=>"99","L"=>4),"genero" => array("N"=>"genero","T"=>"INT","V"=>"99","L"=>4),"eacp" => array("N"=>"eacp","T"=>"VARCHAR","V"=>"EN_TRAMITE","L"=>15),"observaciones" => array("N"=>"observaciones","T"=>"VARCHAR","V"=>"","L"=>100),"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"99","L"=>4),"grupo_solidario" => array("N"=>"grupo_solidario","T"=>"BIGINT","V"=>"99","L"=>20),"personalidad_juridica" => array("N"=>"personalidad_juridica","T"=>"INT","V"=>"1","L"=>4),"dependencia" => array("N"=>"dependencia","T"=>"INT","V"=>"99","L"=>4),"regimen_conyugal" => array("N"=>"regimen_conyugal","T"=>"ENUM","V"=>"|NINGUNO|SOCIEDAD_CONYUGAL|BIENES_SEPARADOS|","L"=>0),"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"MATRIZ","L"=>10),"fecha_de_revision" => array("N"=>"fecha_de_revision","T"=>"DATE","V"=>"2008-06-01","L"=>0),"tipo_de_identificacion" => array("N"=>"tipo_de_identificacion","T"=>"INT","V"=>"220","L"=>5),"documento_de_identificacion" => array("N"=>"documento_de_identificacion","T"=>"VARCHAR","V"=>"0","L"=>18),"correo_electronico" => array("N"=>"correo_electronico","T"=>"VARCHAR","V"=>"","L"=>40),"telefono_principal" => array("N"=>"telefono_principal","T"=>"VARCHAR","V"=>"","L"=>20),"dependientes_economicos" => array("N"=>"dependientes_economicos","T"=>"INT","V"=>"0","L"=>4),"pais_de_origen" => array("N"=>"pais_de_origen","T"=>"VARCHAR","V"=>"MX","L"=>4),"titulo_personal" => array("N"=>"titulo_personal","T"=>"VARCHAR","V"=>"NA","L"=>40),"nivel_de_riesgo_aml" => array("N"=>"nivel_de_riesgo_aml","T"=>"INT","V"=>"1","L"=>4),"clave_de_firma_electronica" => array("N"=>"clave_de_firma_electronica","T"=>"VARCHAR","V"=>"","L"=>100),"descuento_preferente" => array("N"=>"descuento_preferente","T"=>"FLOAT","V"=>"0.0000","L"=>29),"regimen_fiscal" => array("N"=>"regimen_fiscal","T"=>"INT","V"=>"1","L"=>4),"nacionalidad_extranjera" => array("N"=>"nacionalidad_extranjera","T"=>"INT","V"=>"0","L"=>4),"xclasificacion" => array("N"=>"xclasificacion","T"=>"INT","V"=>"0","L"=>4),"yclasificacion" => array("N"=>"yclasificacion","T"=>"INT","V"=>"0","L"=>4),"zclasificacion" => array("N"=>"zclasificacion","T"=>"INT","V"=>"0","L"=>4),"sitioweb" => array("N"=>"sitioweb","T"=>"VARCHAR","V"=>"","L"=>80),"idinterna" => array("N"=>"idinterna","T"=>"VARCHAR","V"=>"","L"=>20),"nss" => array("N"=>"nss","T"=>"VARCHAR","V"=>"","L"=>20));
	public $CODIGO = "codigo"; public $NOMBRECOMPLETO = "nombrecompleto"; public $APELLIDOPATERNO = "apellidopaterno"; public $APELLIDOMATERNO = "apellidomaterno"; public $RFC = "rfc"; public $CURP = "curp"; public $FECHAENTREVISTA = "fechaentrevista"; public $FECHAALTA = "fechaalta"; public $ESTATUSACTUAL = "estatusactual"; public $REGION = "region"; public $CAJALOCAL = "cajalocal"; public $FECHANACIMIENTO = "fechanacimiento"; public $LUGARNACIMIENTO = "lugarnacimiento"; public $TIPOINGRESO = "tipoingreso"; public $ESTADOCIVIL = "estadocivil"; public $GENERO = "genero"; public $EACP = "eacp"; public $OBSERVACIONES = "observaciones"; public $IDUSUARIO = "idusuario"; public $GRUPO_SOLIDARIO = "grupo_solidario"; public $PERSONALIDAD_JURIDICA = "personalidad_juridica"; public $DEPENDENCIA = "dependencia"; public $REGIMEN_CONYUGAL = "regimen_conyugal"; public $SUCURSAL = "sucursal"; public $FECHA_DE_REVISION = "fecha_de_revision"; public $TIPO_DE_IDENTIFICACION = "tipo_de_identificacion"; public $DOCUMENTO_DE_IDENTIFICACION = "documento_de_identificacion"; public $CORREO_ELECTRONICO = "correo_electronico"; public $TELEFONO_PRINCIPAL = "telefono_principal"; public $DEPENDIENTES_ECONOMICOS = "dependientes_economicos"; public $PAIS_DE_ORIGEN = "pais_de_origen"; public $TITULO_PERSONAL = "titulo_personal"; public $NIVEL_DE_RIESGO_AML = "nivel_de_riesgo_aml"; public $CLAVE_DE_FIRMA_ELECTRONICA = "clave_de_firma_electronica"; public $DESCUENTO_PREFERENTE = "descuento_preferente"; public $REGIMEN_FISCAL = "regimen_fiscal"; public $NACIONALIDAD_EXTRANJERA = "nacionalidad_extranjera"; public $XCLASIFICACION = "xclasificacion"; public $YCLASIFICACION = "yclasificacion"; public $ZCLASIFICACION = "zclasificacion"; public $SITIOWEB = "sitioweb"; public $IDINTERNA = "idinterna"; public $NSS = "nss";
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
	function nacionalidad_extranjera($v = false){ if($v !== false){$this->mCampos["nacionalidad_extranjera"]["V"] =  $v; } return new MQLCampo($this->mCampos["nacionalidad_extranjera"]);}
	function xclasificacion($v = false){ if($v !== false){$this->mCampos["xclasificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["xclasificacion"]);}
	function yclasificacion($v = false){ if($v !== false){$this->mCampos["yclasificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["yclasificacion"]);}
	function zclasificacion($v = false){ if($v !== false){$this->mCampos["zclasificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["zclasificacion"]);}
	function sitioweb($v = false){ if($v !== false){$this->mCampos["sitioweb"]["V"] =  $v; } return new MQLCampo($this->mCampos["sitioweb"]);}
	function idinterna($v = false){ if($v !== false){$this->mCampos["idinterna"]["V"] =  $v; } return new MQLCampo($this->mCampos["idinterna"]);}
	function nss($v = false){ if($v !== false){$this->mCampos["nss"]["V"] =  $v; } return new MQLCampo($this->mCampos["nss"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	socios_relacionestipos	-	Generado:	[03/2/2016 10:36]	*/
/*	ORM: Tabla:	socios_relacionestipos	-	Generado:	[13/1/2017 12:58]	*/
class cSocios_relacionestipos {
	private $mCampos	= array("idsocios_relacionestipos" => array("N"=>"idsocios_relacionestipos","T"=>"INT","V"=>"","L"=>4),"descripcion_relacionestipos" => array("N"=>"descripcion_relacionestipos","T"=>"VARCHAR","V"=>"","L"=>50),"subclasificacion" => array("N"=>"subclasificacion","T"=>"INT","V"=>"0","L"=>4),"descripcion_larga" => array("N"=>"descripcion_larga","T"=>"VARCHAR","V"=>"","L"=>80),"tipo_relacion" => array("N"=>"tipo_relacion","T"=>"INT","V"=>"0","L"=>4),"puntos_en_scoring" => array("N"=>"puntos_en_scoring","T"=>"FLOAT","V"=>"0.00","L"=>13),"requiere_domicilio" => array("N"=>"requiere_domicilio","T"=>"INT","V"=>"0","L"=>2),"requiere_actividadeconomica" => array("N"=>"requiere_actividadeconomica","T"=>"INT","V"=>"0","L"=>2),"requiere_validacion" => array("N"=>"requiere_validacion","T"=>"INT","V"=>"0","L"=>2),"tiene_vinculo_patrimonial" => array("N"=>"tiene_vinculo_patrimonial","T"=>"INT","V"=>"0","L"=>2),"mostrar" => array("N"=>"mostrar","T"=>"INT","V"=>"1","L"=>2),"checar_aml" => array("N"=>"checar_aml","T"=>"INT","V"=>"1","L"=>2),"tags" => array("N"=>"tags","T"=>"VARCHAR","V"=>"","L"=>50),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "socios_relacionestipos";}
	function getKey(){ return "idsocios_relacionestipos";}
	function idsocios_relacionestipos($v = false){ if($v !== false){$this->mCampos["idsocios_relacionestipos"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsocios_relacionestipos"]);}
	function descripcion_relacionestipos($v = false){ if($v !== false){$this->mCampos["descripcion_relacionestipos"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_relacionestipos"]);}
	function subclasificacion($v = false){ if($v !== false){$this->mCampos["subclasificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["subclasificacion"]);}
	function descripcion_larga($v = false){ if($v !== false){$this->mCampos["descripcion_larga"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_larga"]);}
	function tipo_relacion($v = false){ if($v !== false){$this->mCampos["tipo_relacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_relacion"]);}
	function puntos_en_scoring($v = false){ if($v !== false){$this->mCampos["puntos_en_scoring"]["V"] =  $v; } return new MQLCampo($this->mCampos["puntos_en_scoring"]);}
	function requiere_domicilio($v = false){ if($v !== false){$this->mCampos["requiere_domicilio"]["V"] =  $v; } return new MQLCampo($this->mCampos["requiere_domicilio"]);}
	function requiere_actividadeconomica($v = false){ if($v !== false){$this->mCampos["requiere_actividadeconomica"]["V"] =  $v; } return new MQLCampo($this->mCampos["requiere_actividadeconomica"]);}
	function requiere_validacion($v = false){ if($v !== false){$this->mCampos["requiere_validacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["requiere_validacion"]);}
	function tiene_vinculo_patrimonial($v = false){ if($v !== false){$this->mCampos["tiene_vinculo_patrimonial"]["V"] =  $v; } return new MQLCampo($this->mCampos["tiene_vinculo_patrimonial"]);}
	function mostrar($v = false){ if($v !== false){$this->mCampos["mostrar"]["V"] =  $v; } return new MQLCampo($this->mCampos["mostrar"]);}
	function checar_aml($v = false){ if($v !== false){$this->mCampos["checar_aml"]["V"] =  $v; } return new MQLCampo($this->mCampos["checar_aml"]);}
	function tags($v = false){ if($v !== false){$this->mCampos["tags"]["V"] =  $v; } return new MQLCampo($this->mCampos["tags"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	socios_consanguinidad	-	Generado:	[04/12/2014 14:06]	*/
/*	ORM: Tabla:	socios_consanguinidad	-	Generado:	[04/4/2018 16:18]	*/
class cSocios_consanguinidad {
	private $mCampos	= array("idsocios_consanguinidad" => array("N"=>"idsocios_consanguinidad","T"=>"INT","V"=>"0","L"=>4),"descripcion_consanguinidad" => array("N"=>"descripcion_consanguinidad","T"=>"VARCHAR","V"=>"","L"=>45),"grado_de_consanguinidad" => array("N"=>"grado_de_consanguinidad","T"=>"INT","V"=>"1","L"=>2),"grado_de_afinidad" => array("N"=>"grado_de_afinidad","T"=>"INT","V"=>"0","L"=>2));
	public $IDSOCIOS_CONSANGUINIDAD = "idsocios_consanguinidad"; public $DESCRIPCION_CONSANGUINIDAD = "descripcion_consanguinidad"; public $GRADO_DE_CONSANGUINIDAD = "grado_de_consanguinidad"; public $GRADO_DE_AFINIDAD = "grado_de_afinidad";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "socios_consanguinidad";}
	function getKey(){ return "idsocios_consanguinidad";}
	function idsocios_consanguinidad($v = false){ if($v !== false){$this->mCampos["idsocios_consanguinidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsocios_consanguinidad"]);}
	function descripcion_consanguinidad($v = false){ if($v !== false){$this->mCampos["descripcion_consanguinidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_consanguinidad"]);}
	function grado_de_consanguinidad($v = false){ if($v !== false){$this->mCampos["grado_de_consanguinidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["grado_de_consanguinidad"]);}
	function grado_de_afinidad($v = false){ if($v !== false){$this->mCampos["grado_de_afinidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["grado_de_afinidad"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	socios_relacionesestatus	-	Generado:	[04/4/2018 16:43]	*/
class cSocios_relacionesestatus {
	private $mCampos	= array("idsocios_relacionesestatus" => array("N"=>"idsocios_relacionesestatus","T"=>"INT","V"=>"0","L"=>4),"deescripcion_relacionesestatus" => array("N"=>"deescripcion_relacionesestatus","T"=>"VARCHAR","V"=>"","L"=>45),"estatus" => array("N"=>"estatus","T"=>"INT","V"=>"0","L"=>4));
	public $IDSOCIOS_RELACIONESESTATUS = "idsocios_relacionesestatus"; public $DEESCRIPCION_RELACIONESESTATUS = "deescripcion_relacionesestatus"; public $ESTATUS = "estatus";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "socios_relacionesestatus";}
	function getKey(){ return "idsocios_relacionesestatus";}
	function idsocios_relacionesestatus($v = false){ if($v !== false){$this->mCampos["idsocios_relacionesestatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsocios_relacionesestatus"]);}
	function deescripcion_relacionesestatus($v = false){ if($v !== false){$this->mCampos["deescripcion_relacionesestatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["deescripcion_relacionesestatus"]);}
	function estatus($v = false){ if($v !== false){$this->mCampos["estatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	socios_genero	-	Generado:	[19/1/2015 06:00]	*/
/*	ORM: Tabla:	socios_genero	-	Generado:	[22/2/2018 12:25]	*/
class cSocios_genero {
	private $mCampos	= array("idsocios_genero" => array("N"=>"idsocios_genero","T"=>"INT","V"=>"0","L"=>4),"descripcion_genero" => array("N"=>"descripcion_genero","T"=>"VARCHAR","V"=>"","L"=>45),"genero" => array("N"=>"genero","T"=>"INT","V"=>"0","L"=>4));
	public $IDSOCIOS_GENERO = "idsocios_genero"; public $DESCRIPCION_GENERO = "descripcion_genero"; public $GENERO = "genero";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "socios_genero";}
	function getKey(){ return "idsocios_genero";}
	function idsocios_genero($v = false){ if($v !== false){$this->mCampos["idsocios_genero"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsocios_genero"]);}
	function descripcion_genero($v = false){ if($v !== false){$this->mCampos["descripcion_genero"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_genero"]);}
	function genero($v = false){ if($v !== false){$this->mCampos["genero"]["V"] =  $v; } return new MQLCampo($this->mCampos["genero"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}

/*	ORM: Tabla:	socios_patrimoniotipo	-	Generado:	[19/1/2015 06:00]	*/
/*	ORM: Tabla:	socios_patrimoniotipo	-	Generado:	[22/2/2018 12:53]	*/
class cSocios_patrimoniotipo {
	private $mCampos	= array("idsocios_patrimoniotipo" => array("N"=>"idsocios_patrimoniotipo","T"=>"INT","V"=>"0","L"=>4),"descripcion_patrimoniotipo" => array("N"=>"descripcion_patrimoniotipo","T"=>"VARCHAR","V"=>"","L"=>65),"subclasificacion" => array("N"=>"subclasificacion","T"=>"INT","V"=>"1","L"=>4),"tipo_patrimonio" => array("N"=>"tipo_patrimonio","T"=>"INT","V"=>"","L"=>4),"unidad" => array("N"=>"unidad","T"=>"INT","V"=>"1","L"=>4));
	public $IDSOCIOS_PATRIMONIOTIPO = "idsocios_patrimoniotipo"; public $DESCRIPCION_PATRIMONIOTIPO = "descripcion_patrimoniotipo"; public $SUBCLASIFICACION = "subclasificacion"; public $TIPO_PATRIMONIO = "tipo_patrimonio"; public $UNIDAD = "unidad";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "socios_patrimoniotipo";}
	function getKey(){ return "idsocios_patrimoniotipo";}
	function idsocios_patrimoniotipo($v = false){ if($v !== false){$this->mCampos["idsocios_patrimoniotipo"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsocios_patrimoniotipo"]);}
	function descripcion_patrimoniotipo($v = false){ if($v !== false){$this->mCampos["descripcion_patrimoniotipo"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_patrimoniotipo"]);}
	function subclasificacion($v = false){ if($v !== false){$this->mCampos["subclasificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["subclasificacion"]);}
	function tipo_patrimonio($v = false){ if($v !== false){$this->mCampos["tipo_patrimonio"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_patrimonio"]);}
	function unidad($v = false){ if($v !== false){$this->mCampos["unidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["unidad"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	socios_patrimonioestatus	-	Generado:	[22/2/2018 12:24]	*/
class cSocios_patrimonioestatus {
	private $mCampos	= array("idsocios_patrimonioestatus" => array("N"=>"idsocios_patrimonioestatus","T"=>"INT","V"=>"0","L"=>4),"descripcion_patrimonioestatus" => array("N"=>"descripcion_patrimonioestatus","T"=>"VARCHAR","V"=>"","L"=>45),"estatus_actual" => array("N"=>"estatus_actual","T"=>"INT","V"=>"","L"=>4),"estado_presentado" => array("N"=>"estado_presentado","T"=>"INT","V"=>"","L"=>4),"tags" => array("N"=>"tags","T"=>"VARCHAR","V"=>"","L"=>50));
	public $IDSOCIOS_PATRIMONIOESTATUS = "idsocios_patrimonioestatus"; public $DESCRIPCION_PATRIMONIOESTATUS = "descripcion_patrimonioestatus"; public $ESTATUS_ACTUAL = "estatus_actual"; public $ESTADO_PRESENTADO = "estado_presentado"; public $TAGS = "tags";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "socios_patrimonioestatus";}
	function getKey(){ return "idsocios_patrimonioestatus";}
	function idsocios_patrimonioestatus($v = false){ if($v !== false){$this->mCampos["idsocios_patrimonioestatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsocios_patrimonioestatus"]);}
	function descripcion_patrimonioestatus($v = false){ if($v !== false){$this->mCampos["descripcion_patrimonioestatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_patrimonioestatus"]);}
	function estatus_actual($v = false){ if($v !== false){$this->mCampos["estatus_actual"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus_actual"]);}
	function estado_presentado($v = false){ if($v !== false){$this->mCampos["estado_presentado"]["V"] =  $v; } return new MQLCampo($this->mCampos["estado_presentado"]);}
	function tags($v = false){ if($v !== false){$this->mCampos["tags"]["V"] =  $v; } return new MQLCampo($this->mCampos["tags"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}

/*	ORM: Tabla:	socios_patrimonio	-	Generado:	[19/1/2015 06:00]	*/
/*	ORM: Tabla:	socios_patrimonio	-	Generado:	[22/2/2018 11:57]	*/
class cSocios_patrimonio {
	private $mCampos	= array("idsocios_patrimonio" => array("N"=>"idsocios_patrimonio","T"=>"INT","V"=>"","L"=>10),"socio_patrimonio" => array("N"=>"socio_patrimonio","T"=>"BIGINT","V"=>"0","L"=>20),"tipo_patrimonio" => array("N"=>"tipo_patrimonio","T"=>"INT","V"=>"0","L"=>4),"monto_patrimonio" => array("N"=>"monto_patrimonio","T"=>"DOUBLE","V"=>"0.00","L"=>33),"afectacion_patrimonio" => array("N"=>"afectacion_patrimonio","T"=>"DOUBLE","V"=>"0.00","L"=>33),"fecha_expiracion" => array("N"=>"fecha_expiracion","T"=>"DATE","V"=>"0000-00-00","L"=>0),"observaciones" => array("N"=>"observaciones","T"=>"VARCHAR","V"=>"","L"=>100),"descripcion" => array("N"=>"descripcion","T"=>"VARCHAR","V"=>"","L"=>100),"documento_presentado" => array("N"=>"documento_presentado","T"=>"VARCHAR","V"=>"","L"=>45),"solicitud_relacionada" => array("N"=>"solicitud_relacionada","T"=>"BIGINT","V"=>"1","L"=>20),"estatus_actual" => array("N"=>"estatus_actual","T"=>"INT","V"=>"99","L"=>4),"codigo" => array("N"=>"codigo","T"=>"INT","V"=>"1","L"=>10),"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"MATRIZ","L"=>10),"eacp" => array("N"=>"eacp","T"=>"VARCHAR","V"=>"EN_TRAMITE","L"=>20),"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"","L"=>10),"fecha_de_alta" => array("N"=>"fecha_de_alta","T"=>"DATE","V"=>"","L"=>0),"tamannio" => array("N"=>"tamannio","T"=>"INT","V"=>"0","L"=>6),"idtipounidad" => array("N"=>"idtipounidad","T"=>"INT","V"=>"1","L"=>3),"estatus" => array("N"=>"estatus","T"=>"INT","V"=>"1","L"=>2));
	public $IDSOCIOS_PATRIMONIO = "idsocios_patrimonio"; public $SOCIO_PATRIMONIO = "socio_patrimonio"; public $TIPO_PATRIMONIO = "tipo_patrimonio"; public $MONTO_PATRIMONIO = "monto_patrimonio"; public $AFECTACION_PATRIMONIO = "afectacion_patrimonio"; public $FECHA_EXPIRACION = "fecha_expiracion"; public $OBSERVACIONES = "observaciones"; public $DESCRIPCION = "descripcion"; public $DOCUMENTO_PRESENTADO = "documento_presentado"; public $SOLICITUD_RELACIONADA = "solicitud_relacionada"; public $ESTATUS_ACTUAL = "estatus_actual"; public $CODIGO = "codigo"; public $SUCURSAL = "sucursal"; public $EACP = "eacp"; public $IDUSUARIO = "idusuario"; public $FECHA_DE_ALTA = "fecha_de_alta"; public $TAMANNIO = "tamannio"; public $IDTIPOUNIDAD = "idtipounidad"; public $ESTATUS = "estatus";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "socios_patrimonio";}
	function getKey(){ return "idsocios_patrimonio";}
	function idsocios_patrimonio($v = false){ if($v !== false){$this->mCampos["idsocios_patrimonio"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsocios_patrimonio"]);}
	function socio_patrimonio($v = false){ if($v !== false){$this->mCampos["socio_patrimonio"]["V"] =  $v; } return new MQLCampo($this->mCampos["socio_patrimonio"]);}
	function tipo_patrimonio($v = false){ if($v !== false){$this->mCampos["tipo_patrimonio"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_patrimonio"]);}
	function monto_patrimonio($v = false){ if($v !== false){$this->mCampos["monto_patrimonio"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_patrimonio"]);}
	function afectacion_patrimonio($v = false){ if($v !== false){$this->mCampos["afectacion_patrimonio"]["V"] =  $v; } return new MQLCampo($this->mCampos["afectacion_patrimonio"]);}
	function fecha_expiracion($v = false){ if($v !== false){$this->mCampos["fecha_expiracion"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_expiracion"]);}
	function observaciones($v = false){ if($v !== false){$this->mCampos["observaciones"]["V"] =  $v; } return new MQLCampo($this->mCampos["observaciones"]);}
	function descripcion($v = false){ if($v !== false){$this->mCampos["descripcion"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion"]);}
	function documento_presentado($v = false){ if($v !== false){$this->mCampos["documento_presentado"]["V"] =  $v; } return new MQLCampo($this->mCampos["documento_presentado"]);}
	function solicitud_relacionada($v = false){ if($v !== false){$this->mCampos["solicitud_relacionada"]["V"] =  $v; } return new MQLCampo($this->mCampos["solicitud_relacionada"]);}
	function estatus_actual($v = false){ if($v !== false){$this->mCampos["estatus_actual"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus_actual"]);}
	function codigo($v = false){ if($v !== false){$this->mCampos["codigo"]["V"] =  $v; } return new MQLCampo($this->mCampos["codigo"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function eacp($v = false){ if($v !== false){$this->mCampos["eacp"]["V"] =  $v; } return new MQLCampo($this->mCampos["eacp"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function fecha_de_alta($v = false){ if($v !== false){$this->mCampos["fecha_de_alta"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_alta"]);}
	function tamannio($v = false){ if($v !== false){$this->mCampos["tamannio"]["V"] =  $v; } return new MQLCampo($this->mCampos["tamannio"]);}
	function idtipounidad($v = false){ if($v !== false){$this->mCampos["idtipounidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["idtipounidad"]);}
	function estatus($v = false){ if($v !== false){$this->mCampos["estatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
}




/*	ORM: Tabla:	socios_cajalocal	-	Generado:	[22/7/2015 19:22]	*/
class cSocios_cajalocal {
	private $mCampos	= array(
			"idsocios_cajalocal" => array("N"=>"idsocios_cajalocal","T"=>"INT","V"=>"0","L"=>4),
			"descripcion_cajalocal" => array("N"=>"descripcion_cajalocal","T"=>"VARCHAR","V"=>"","L"=>45),
			"ultimosocio" => array("N"=>"ultimosocio","T"=>"BIGINT","V"=>"0","L"=>20),
			"region" => array("N"=>"region","T"=>"INT","V"=>"0","L"=>4),
			"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"MATRIZ","L"=>10),
			"codigo_postal" => array("N"=>"codigo_postal","T"=>"INT","V"=>"0","L"=>10),
			"localidad" => array("N"=>"localidad","T"=>"VARCHAR","V"=>"campeche","L"=>60),
			"estado" => array("N"=>"estado","T"=>"VARCHAR","V"=>"campeche","L"=>60),
			"municipio" => array("N"=>"municipio","T"=>"VARCHAR","V"=>"campeche","L"=>60),
			"clave_de_centro" => array("N"=>"clave_de_centro","T"=>"VARCHAR","V"=>"campeche","L"=>25),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "socios_cajalocal";}
	function getKey(){ return "idsocios_cajalocal";}
	function idsocios_cajalocal($v = false){ if($v !== false){$this->mCampos["idsocios_cajalocal"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsocios_cajalocal"]);}
	function descripcion_cajalocal($v = false){ if($v !== false){$this->mCampos["descripcion_cajalocal"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_cajalocal"]);}
	function ultimosocio($v = false){ if($v !== false){$this->mCampos["ultimosocio"]["V"] =  $v; } return new MQLCampo($this->mCampos["ultimosocio"]);}
	function region($v = false){ if($v !== false){$this->mCampos["region"]["V"] =  $v; } return new MQLCampo($this->mCampos["region"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function codigo_postal($v = false){ if($v !== false){$this->mCampos["codigo_postal"]["V"] =  $v; } return new MQLCampo($this->mCampos["codigo_postal"]);}
	function localidad($v = false){ if($v !== false){$this->mCampos["localidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["localidad"]);}
	function estado($v = false){ if($v !== false){$this->mCampos["estado"]["V"] =  $v; } return new MQLCampo($this->mCampos["estado"]);}
	function municipio($v = false){ if($v !== false){$this->mCampos["municipio"]["V"] =  $v; } return new MQLCampo($this->mCampos["municipio"]);}
	function clave_de_centro($v = false){ if($v !== false){$this->mCampos["clave_de_centro"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_centro"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	socios_region	-	Generado:	[23/7/2015 18:20]	*/
class cSocios_region {
	private $mCampos	= array(
			"idsocios_region" => array("N"=>"idsocios_region","T"=>"INT","V"=>"0","L"=>4),
			"descripcion_region" => array("N"=>"descripcion_region","T"=>"VARCHAR","V"=>"","L"=>100),
			"oficial_de_credito" => array("N"=>"oficial_de_credito","T"=>"INT","V"=>"99","L"=>4),
			"region" => array("N"=>"region","T"=>"INT","V"=>"","L"=>4)
	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "socios_region";}
	function getKey(){ return "idsocios_region";}
	function idsocios_region($v = false){ if($v !== false){$this->mCampos["idsocios_region"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsocios_region"]);}
	function descripcion_region($v = false){ if($v !== false){$this->mCampos["descripcion_region"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_region"]);}
	function oficial_de_credito($v = false){ if($v !== false){$this->mCampos["oficial_de_credito"]["V"] =  $v; } return new MQLCampo($this->mCampos["oficial_de_credito"]);}
	function region($v = false){ if($v !== false){$this->mCampos["region"]["V"] =  $v; } return new MQLCampo($this->mCampos["region"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	personas_documentacion	-	Generado:	[14/2/2014 16:22]	*/
/*	ORM: Tabla:	personas_documentacion	-	Generado:	[20/9/2016 18:12]	*/
/*	ORM: Tabla:	personas_documentacion	-	Generado:	[08/12/2016 17:44]	*/
class cPersonas_documentacion {
	private $mCampos	= array("clave_de_control" => array("N"=>"clave_de_control","T"=>"INT","V"=>"","L"=>11),"clave_de_persona" => array("N"=>"clave_de_persona","T"=>"BIGINT","V"=>"0","L"=>20),"tipo_de_documento" => array("N"=>"tipo_de_documento","T"=>"INT","V"=>"0","L"=>4),"fecha_de_carga" => array("N"=>"fecha_de_carga","T"=>"INT","V"=>"0","L"=>11),"observaciones" => array("N"=>"observaciones","T"=>"VARCHAR","V"=>"","L"=>100),"archivo_de_documento" => array("N"=>"archivo_de_documento","T"=>"VARCHAR","V"=>"","L"=>200),"valor_de_comprobacion" => array("N"=>"valor_de_comprobacion","T"=>"VARCHAR","V"=>"","L"=>100),"estado_en_sistema" => array("N"=>"estado_en_sistema","T"=>"INT","V"=>"1","L"=>11),"fecha_de_verificacion" => array("N"=>"fecha_de_verificacion","T"=>"INT","V"=>"0","L"=>11),"oficial_que_verifico" => array("N"=>"oficial_que_verifico","T"=>"INT","V"=>"0","L"=>6),"resultado_de_la_verificacion" => array("N"=>"resultado_de_la_verificacion","T"=>"INT","V"=>"0","L"=>2),"notas" => array("N"=>"notas","T"=>"VARCHAR","V"=>"","L"=>100),"version_de_documento" => array("N"=>"version_de_documento","T"=>"VARCHAR","V"=>"","L"=>20),"numero_de_pagina" => array("N"=>"numero_de_pagina","T"=>"VARCHAR","V"=>"1","L"=>10),"usuario" => array("N"=>"usuario","T"=>"INT","V"=>"1","L"=>6),"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"","L"=>20),"entidad" => array("N"=>"entidad","T"=>"VARCHAR","V"=>"","L"=>20),"documento_relacionado" => array("N"=>"documento_relacionado","T"=>"BIGINT","V"=>"","L"=>20),"vencimiento" => array("N"=>"vencimiento","T"=>"DATE","V"=>"","L"=>0),"estatus" => array("N"=>"estatus","T"=>"INT","V"=>"1","L"=>2),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "personas_documentacion";}
	function getKey(){ return "clave_de_control";}
	function clave_de_control($v = false){ if($v !== false){$this->mCampos["clave_de_control"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_control"]);}
	function clave_de_persona($v = false){ if($v !== false){$this->mCampos["clave_de_persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_persona"]);}
	function tipo_de_documento($v = false){ if($v !== false){$this->mCampos["tipo_de_documento"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_documento"]);}
	function fecha_de_carga($v = false){ if($v !== false){$this->mCampos["fecha_de_carga"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_carga"]);}
	function observaciones($v = false){ if($v !== false){$this->mCampos["observaciones"]["V"] =  $v; } return new MQLCampo($this->mCampos["observaciones"]);}
	function archivo_de_documento($v = false){ if($v !== false){$this->mCampos["archivo_de_documento"]["V"] =  $v; } return new MQLCampo($this->mCampos["archivo_de_documento"]);}
	function valor_de_comprobacion($v = false){ if($v !== false){$this->mCampos["valor_de_comprobacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["valor_de_comprobacion"]);}
	function estado_en_sistema($v = false){ if($v !== false){$this->mCampos["estado_en_sistema"]["V"] =  $v; } return new MQLCampo($this->mCampos["estado_en_sistema"]);}
	function fecha_de_verificacion($v = false){ if($v !== false){$this->mCampos["fecha_de_verificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_verificacion"]);}
	function oficial_que_verifico($v = false){ if($v !== false){$this->mCampos["oficial_que_verifico"]["V"] =  $v; } return new MQLCampo($this->mCampos["oficial_que_verifico"]);}
	function resultado_de_la_verificacion($v = false){ if($v !== false){$this->mCampos["resultado_de_la_verificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["resultado_de_la_verificacion"]);}
	function notas($v = false){ if($v !== false){$this->mCampos["notas"]["V"] =  $v; } return new MQLCampo($this->mCampos["notas"]);}
	function version_de_documento($v = false){ if($v !== false){$this->mCampos["version_de_documento"]["V"] =  $v; } return new MQLCampo($this->mCampos["version_de_documento"]);}
	function numero_de_pagina($v = false){ if($v !== false){$this->mCampos["numero_de_pagina"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_de_pagina"]);}
	function usuario($v = false){ if($v !== false){$this->mCampos["usuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["usuario"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function entidad($v = false){ if($v !== false){$this->mCampos["entidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["entidad"]);}
	function documento_relacionado($v = false){ if($v !== false){$this->mCampos["documento_relacionado"]["V"] =  $v; } return new MQLCampo($this->mCampos["documento_relacionado"]);}
	function vencimiento($v = false){ if($v !== false){$this->mCampos["vencimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["vencimiento"]);}
	function estatus($v = false){ if($v !== false){$this->mCampos["estatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	personas_xclasificacion	-	Generado:	[22/7/2015 12:25]	*/
class cPersonas_xclasificacion {
	private $mCampos	= array(
			"idpersonas_xclasificacion" => array("N"=>"idpersonas_xclasificacion","T"=>"INT","V"=>"","L"=>4),
			"descripcion_xclasificacion" => array("N"=>"descripcion_xclasificacion","T"=>"VARCHAR","V"=>"","L"=>80),
			"xclasificacion_etiquetas" => array("N"=>"xclasificacion_etiquetas","T"=>"VARCHAR","V"=>"","L"=>40),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "personas_xclasificacion";}
	function getKey(){ return "idpersonas_xclasificacion";}
	function idpersonas_xclasificacion($v = false){ if($v !== false){$this->mCampos["idpersonas_xclasificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["idpersonas_xclasificacion"]);}
	function descripcion_xclasificacion($v = false){ if($v !== false){$this->mCampos["descripcion_xclasificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_xclasificacion"]);}
	function xclasificacion_etiquetas($v = false){ if($v !== false){$this->mCampos["xclasificacion_etiquetas"]["V"] =  $v; } return new MQLCampo($this->mCampos["xclasificacion_etiquetas"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}
/*	ORM: Tabla:	personas_yclasificacion	-	Generado:	[22/7/2015 12:25]	*/
class cPersonas_yclasificacion {
	private $mCampos	= array(
			"idpersonas_yclasificacion" => array("N"=>"idpersonas_yclasificacion","T"=>"INT","V"=>"","L"=>4),
			"descripcion_yclasificacion" => array("N"=>"descripcion_yclasificacion","T"=>"VARCHAR","V"=>"","L"=>80),
			"yclasificacion_etiquetas" => array("N"=>"yclasificacion_etiquetas","T"=>"VARCHAR","V"=>"","L"=>40),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "personas_yclasificacion";}
	function getKey(){ return "idpersonas_yclasificacion";}
	function idpersonas_yclasificacion($v = false){ if($v !== false){$this->mCampos["idpersonas_yclasificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["idpersonas_yclasificacion"]);}
	function descripcion_yclasificacion($v = false){ if($v !== false){$this->mCampos["descripcion_yclasificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_yclasificacion"]);}
	function yclasificacion_etiquetas($v = false){ if($v !== false){$this->mCampos["yclasificacion_etiquetas"]["V"] =  $v; } return new MQLCampo($this->mCampos["yclasificacion_etiquetas"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}


/*	ORM: Tabla:	personas_zclasificacion	-	Generado:	[22/7/2015 12:25]	*/
class cPersonas_zclasificacion {
	private $mCampos	= array(
			"idpersonas_zclasificacion" => array("N"=>"idpersonas_zclasificacion","T"=>"INT","V"=>"","L"=>4),
			"descripcion_zclasificacion" => array("N"=>"descripcion_zclasificacion","T"=>"VARCHAR","V"=>"","L"=>80),
			"zclasificacion_etiquetas" => array("N"=>"zclasificacion_etiquetas","T"=>"VARCHAR","V"=>"","L"=>40)
	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "personas_zclasificacion";}
	function getKey(){ return "idpersonas_zclasificacion";}
	function idpersonas_zclasificacion($v = false){ if($v !== false){$this->mCampos["idpersonas_zclasificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["idpersonas_zclasificacion"]);}
	function descripcion_zclasificacion($v = false){ if($v !== false){$this->mCampos["descripcion_zclasificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_zclasificacion"]);}
	function zclasificacion_etiquetas($v = false){ if($v !== false){$this->mCampos["zclasificacion_etiquetas"]["V"] =  $v; } return new MQLCampo($this->mCampos["zclasificacion_etiquetas"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	personas_datos_colegiacion	-	Generado:	[26/4/2016 15:19]	*/
/*	ORM: Tabla:	personas_datos_colegiacion	-	Generado:	[19/7/2017 18:21]	*/
class cPersonas_datos_colegiacion {
	private $mCampos	= array("idpersonas_datos_colegiacion" => array("N"=>"idpersonas_datos_colegiacion","T"=>"INT","V"=>"","L"=>11),"clave_de_persona" => array("N"=>"clave_de_persona","T"=>"BIGINT","V"=>"","L"=>25),"dia_de_pago" => array("N"=>"dia_de_pago","T"=>"VARCHAR","V"=>"","L"=>10),"tipo_de_lugar_de_pago" => array("N"=>"tipo_de_lugar_de_pago","T"=>"INT","V"=>"1","L"=>4),"tipo_de_afiliacion" => array("N"=>"tipo_de_afiliacion","T"=>"INT","V"=>"0","L"=>6),"datos_de_emergencia" => array("N"=>"datos_de_emergencia","T"=>"VARCHAR","V"=>"","L"=>100),"grado_academico" => array("N"=>"grado_academico","T"=>"INT","V"=>"0","L"=>4),"numero_de_colegiacion" => array("N"=>"numero_de_colegiacion","T"=>"VARCHAR","V"=>"0","L"=>15),"dato1" => array("N"=>"dato1","T"=>"VARCHAR","V"=>"","L"=>40),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "personas_datos_colegiacion";}
	function getKey(){ return "idpersonas_datos_colegiacion";}
	function idpersonas_datos_colegiacion($v = false){ if($v !== false){$this->mCampos["idpersonas_datos_colegiacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["idpersonas_datos_colegiacion"]);}
	function clave_de_persona($v = false){ if($v !== false){$this->mCampos["clave_de_persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_persona"]);}
	function dia_de_pago($v = false){ if($v !== false){$this->mCampos["dia_de_pago"]["V"] =  $v; } return new MQLCampo($this->mCampos["dia_de_pago"]);}
	function tipo_de_lugar_de_pago($v = false){ if($v !== false){$this->mCampos["tipo_de_lugar_de_pago"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_lugar_de_pago"]);}
	function tipo_de_afiliacion($v = false){ if($v !== false){$this->mCampos["tipo_de_afiliacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_afiliacion"]);}
	function datos_de_emergencia($v = false){ if($v !== false){$this->mCampos["datos_de_emergencia"]["V"] =  $v; } return new MQLCampo($this->mCampos["datos_de_emergencia"]);}
	function grado_academico($v = false){ if($v !== false){$this->mCampos["grado_academico"]["V"] =  $v; } return new MQLCampo($this->mCampos["grado_academico"]);}
	function numero_de_colegiacion($v = false){ if($v !== false){$this->mCampos["numero_de_colegiacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_de_colegiacion"]);}
	function dato1($v = false){ if($v !== false){$this->mCampos["dato1"]["V"] =  $v; } return new MQLCampo($this->mCampos["dato1"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}


/*	ORM: Tabla:	personas_datos_extranjero	-	Generado:	[03/2/2016 13:19]	*/
/*	ORM: Tabla:	personas_datos_extranjero	-	Generado:	[16/1/2018 10:35]	*/
class cPersonas_datos_extranjero {
	private $mCampos	= array("idpersonas_datos_extranjero" => array("N"=>"idpersonas_datos_extranjero","T"=>"INT","V"=>"","L"=>11),"clave_de_persona" => array("N"=>"clave_de_persona","T"=>"BIGINT","V"=>"","L"=>25),"clave_permiso_de_residencia" => array("N"=>"clave_permiso_de_residencia","T"=>"VARCHAR","V"=>"","L"=>100),"fecha_de_inicio_residencia" => array("N"=>"fecha_de_inicio_residencia","T"=>"DATE","V"=>"0000-00-00","L"=>0),"fecha_de_vencimiento" => array("N"=>"fecha_de_vencimiento","T"=>"DATE","V"=>"2029-12-31","L"=>0),"pais_de_nacionalidad" => array("N"=>"pais_de_nacionalidad","T"=>"VARCHAR","V"=>"","L"=>5));
	public $IDPERSONAS_DATOS_EXTRANJERO = "idpersonas_datos_extranjero"; public $CLAVE_DE_PERSONA = "clave_de_persona"; public $CLAVE_PERMISO_DE_RESIDENCIA = "clave_permiso_de_residencia"; public $FECHA_DE_INICIO_RESIDENCIA = "fecha_de_inicio_residencia"; public $FECHA_DE_VENCIMIENTO = "fecha_de_vencimiento"; public $PAIS_DE_NACIONALIDAD = "pais_de_nacionalidad"; 
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "personas_datos_extranjero";}
	function getKey(){ return "idpersonas_datos_extranjero";}
	function idpersonas_datos_extranjero($v = false){ if($v !== false){$this->mCampos["idpersonas_datos_extranjero"]["V"] =  $v; } return new MQLCampo($this->mCampos["idpersonas_datos_extranjero"]);}
	function clave_de_persona($v = false){ if($v !== false){$this->mCampos["clave_de_persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_persona"]);}
	function clave_permiso_de_residencia($v = false){ if($v !== false){$this->mCampos["clave_permiso_de_residencia"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_permiso_de_residencia"]);}
	function fecha_de_inicio_residencia($v = false){ if($v !== false){$this->mCampos["fecha_de_inicio_residencia"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_inicio_residencia"]);}
	function fecha_de_vencimiento($v = false){ if($v !== false){$this->mCampos["fecha_de_vencimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_vencimiento"]);}
	function pais_de_nacionalidad($v = false){ if($v !== false){$this->mCampos["pais_de_nacionalidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["pais_de_nacionalidad"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
}

/*	ORM: Tabla:	personas_membresia_tipo	-	Generado:	[30/7/2015 12:32]	*/
/*	ORM: Tabla:	personas_membresia_tipo	-	Generado:	[16/1/2018 10:36]	*/
class cPersonas_membresia_tipo {
	private $mCampos	= array("idpersonas_membresia_tipo" => array("N"=>"idpersonas_membresia_tipo","T"=>"INT","V"=>"","L"=>4),"descripcion_membresia_tipo" => array("N"=>"descripcion_membresia_tipo","T"=>"VARCHAR","V"=>"","L"=>80));
	public $IDPERSONAS_MEMBRESIA_TIPO = "idpersonas_membresia_tipo"; public $DESCRIPCION_MEMBRESIA_TIPO = "descripcion_membresia_tipo";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "personas_membresia_tipo";}
	function getKey(){ return "idpersonas_membresia_tipo";}
	function idpersonas_membresia_tipo($v = false){ if($v !== false){$this->mCampos["idpersonas_membresia_tipo"]["V"] =  $v; } return new MQLCampo($this->mCampos["idpersonas_membresia_tipo"]);}
	function descripcion_membresia_tipo($v = false){ if($v !== false){$this->mCampos["descripcion_membresia_tipo"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_membresia_tipo"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}


/*	ORM: Tabla:	personas_checklist	-	Generado:	[12/8/2015 19:26]	*/
/*	ORM: Tabla:	personas_checklist	-	Generado:	[02/6/2017 17:24]	*/
class cPersonas_checklist {
	private $mCampos	= array("idpersonas_checklist" => array("N"=>"idpersonas_checklist","T"=>"INT","V"=>"","L"=>11),"clave_de_persona" => array("N"=>"clave_de_persona","T"=>"BIGINT","V"=>"0","L"=>20),"fecha_de_checklist" => array("N"=>"fecha_de_checklist","T"=>"DATE","V"=>"","L"=>0),"entregaa" => array("N"=>"entregaa","T"=>"INT","V"=>"0","L"=>2),"entregab" => array("N"=>"entregab","T"=>"INT","V"=>"0","L"=>2),"entregac" => array("N"=>"entregac","T"=>"INT","V"=>"0","L"=>2),"entregad" => array("N"=>"entregad","T"=>"INT","V"=>"0","L"=>2),"entregae" => array("N"=>"entregae","T"=>"INT","V"=>"0","L"=>2),"entregaf" => array("N"=>"entregaf","T"=>"INT","V"=>"0","L"=>2),"entregag" => array("N"=>"entregag","T"=>"INT","V"=>"0","L"=>2),"entregah" => array("N"=>"entregah","T"=>"INT","V"=>"0","L"=>2),"entregai" => array("N"=>"entregai","T"=>"INT","V"=>"0","L"=>2),"entregaj" => array("N"=>"entregaj","T"=>"INT","V"=>"0","L"=>2),"entregak" => array("N"=>"entregak","T"=>"INT","V"=>"0","L"=>2),"entregal" => array("N"=>"entregal","T"=>"INT","V"=>"0","L"=>2),"entregam" => array("N"=>"entregam","T"=>"INT","V"=>"0","L"=>2),"entregan" => array("N"=>"entregan","T"=>"INT","V"=>"0","L"=>2),"entregao" => array("N"=>"entregao","T"=>"INT","V"=>"0","L"=>2),"entregap" => array("N"=>"entregap","T"=>"INT","V"=>"0","L"=>2),"entregaq" => array("N"=>"entregaq","T"=>"INT","V"=>"0","L"=>2),"entregar" => array("N"=>"entregar","T"=>"INT","V"=>"0","L"=>2),"entregas" => array("N"=>"entregas","T"=>"INT","V"=>"0","L"=>2),"entregat" => array("N"=>"entregat","T"=>"INT","V"=>"0","L"=>2),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "personas_checklist";}
	function getKey(){ return "idpersonas_checklist";}
	function idpersonas_checklist($v = false){ if($v !== false){$this->mCampos["idpersonas_checklist"]["V"] =  $v; } return new MQLCampo($this->mCampos["idpersonas_checklist"]);}
	function clave_de_persona($v = false){ if($v !== false){$this->mCampos["clave_de_persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_persona"]);}
	function fecha_de_checklist($v = false){ if($v !== false){$this->mCampos["fecha_de_checklist"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_checklist"]);}
	function entregaa($v = false){ if($v !== false){$this->mCampos["entregaa"]["V"] =  $v; } return new MQLCampo($this->mCampos["entregaa"]);}
	function entregab($v = false){ if($v !== false){$this->mCampos["entregab"]["V"] =  $v; } return new MQLCampo($this->mCampos["entregab"]);}
	function entregac($v = false){ if($v !== false){$this->mCampos["entregac"]["V"] =  $v; } return new MQLCampo($this->mCampos["entregac"]);}
	function entregad($v = false){ if($v !== false){$this->mCampos["entregad"]["V"] =  $v; } return new MQLCampo($this->mCampos["entregad"]);}
	function entregae($v = false){ if($v !== false){$this->mCampos["entregae"]["V"] =  $v; } return new MQLCampo($this->mCampos["entregae"]);}
	function entregaf($v = false){ if($v !== false){$this->mCampos["entregaf"]["V"] =  $v; } return new MQLCampo($this->mCampos["entregaf"]);}
	function entregag($v = false){ if($v !== false){$this->mCampos["entregag"]["V"] =  $v; } return new MQLCampo($this->mCampos["entregag"]);}
	function entregah($v = false){ if($v !== false){$this->mCampos["entregah"]["V"] =  $v; } return new MQLCampo($this->mCampos["entregah"]);}
	function entregai($v = false){ if($v !== false){$this->mCampos["entregai"]["V"] =  $v; } return new MQLCampo($this->mCampos["entregai"]);}
	function entregaj($v = false){ if($v !== false){$this->mCampos["entregaj"]["V"] =  $v; } return new MQLCampo($this->mCampos["entregaj"]);}
	function entregak($v = false){ if($v !== false){$this->mCampos["entregak"]["V"] =  $v; } return new MQLCampo($this->mCampos["entregak"]);}
	function entregal($v = false){ if($v !== false){$this->mCampos["entregal"]["V"] =  $v; } return new MQLCampo($this->mCampos["entregal"]);}
	function entregam($v = false){ if($v !== false){$this->mCampos["entregam"]["V"] =  $v; } return new MQLCampo($this->mCampos["entregam"]);}
	function entregan($v = false){ if($v !== false){$this->mCampos["entregan"]["V"] =  $v; } return new MQLCampo($this->mCampos["entregan"]);}
	function entregao($v = false){ if($v !== false){$this->mCampos["entregao"]["V"] =  $v; } return new MQLCampo($this->mCampos["entregao"]);}
	function entregap($v = false){ if($v !== false){$this->mCampos["entregap"]["V"] =  $v; } return new MQLCampo($this->mCampos["entregap"]);}
	function entregaq($v = false){ if($v !== false){$this->mCampos["entregaq"]["V"] =  $v; } return new MQLCampo($this->mCampos["entregaq"]);}
	function entregar($v = false){ if($v !== false){$this->mCampos["entregar"]["V"] =  $v; } return new MQLCampo($this->mCampos["entregar"]);}
	function entregas($v = false){ if($v !== false){$this->mCampos["entregas"]["V"] =  $v; } return new MQLCampo($this->mCampos["entregas"]);}
	function entregat($v = false){ if($v !== false){$this->mCampos["entregat"]["V"] =  $v; } return new MQLCampo($this->mCampos["entregat"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}

/*	ORM: Tabla:	aml_personas_descartadas	-	Generado:	[06/3/2015 12:13]	*/
/*	ORM: Tabla:	aml_personas_descartadas	-	Generado:	[23/9/2016 15:49]	*/
class cAml_personas_descartadas {
	private $mCampos	= array(
			"idaml_personas_descartadas" => array("N"=>"idaml_personas_descartadas","T"=>"INT","V"=>"","L"=>11),
			"clave_de_persona" => array("N"=>"clave_de_persona","T"=>"BIGINT","V"=>"1","L"=>20),
			"clave_de_oficial" => array("N"=>"clave_de_oficial","T"=>"INT","V"=>"0","L"=>6),
			"clave_de_motivo" => array("N"=>"clave_de_motivo","T"=>"INT","V"=>"1","L"=>4),
			"fecha_de_captura" => array("N"=>"fecha_de_captura","T"=>"DATE","V"=>"","L"=>0),
			"fecha_de_vencimiento" => array("N"=>"fecha_de_vencimiento","T"=>"DATE","V"=>"","L"=>0),
			"descripcion_del_motivo" => array("N"=>"descripcion_del_motivo","T"=>"TEXT","V"=>"","L"=>0),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "aml_personas_descartadas";}
	function getKey(){ return "idaml_personas_descartadas";}
	function idaml_personas_descartadas($v = false){ if($v !== false){$this->mCampos["idaml_personas_descartadas"]["V"] =  $v; } return new MQLCampo($this->mCampos["idaml_personas_descartadas"]);}
	function clave_de_persona($v = false){ if($v !== false){$this->mCampos["clave_de_persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_persona"]);}
	function clave_de_oficial($v = false){ if($v !== false){$this->mCampos["clave_de_oficial"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_oficial"]);}
	function clave_de_motivo($v = false){ if($v !== false){$this->mCampos["clave_de_motivo"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_motivo"]);}
	function fecha_de_captura($v = false){ if($v !== false){$this->mCampos["fecha_de_captura"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_captura"]);}
	function fecha_de_vencimiento($v = false){ if($v !== false){$this->mCampos["fecha_de_vencimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_vencimiento"]);}
	function descripcion_del_motivo($v = false){ if($v !== false){$this->mCampos["descripcion_del_motivo"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_del_motivo"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}
/*	ORM: Tabla:	personas_aports_tipos	-	Generado:	[21/4/2018 21:45]	*/
/*	ORM: Tabla:	personas_aports_tipos	-	Generado:	[30/4/2018 17:27]	*/
class cPersonas_aports_tipos {
	private $mCampos	= array("personas_aports_tipos_id" => array("N"=>"personas_aports_tipos_id","T"=>"INT","V"=>"","L"=>11),"operacion_tipo_id" => array("N"=>"operacion_tipo_id","T"=>"INT","V"=>"0","L"=>4),"tasa_rendimiento" => array("N"=>"tasa_rendimiento","T"=>"FLOAT","V"=>"0.000","L"=>13),"pagadero" => array("N"=>"pagadero","T"=>"INT","V"=>"30","L"=>4),"estatusactivo" => array("N"=>"estatusactivo","T"=>"INT","V"=>"1","L"=>2));
	public $PERSONAS_APORTS_TIPOS_ID = "personas_aports_tipos_id"; public $OPERACION_TIPO_ID = "operacion_tipo_id"; public $TASA_RENDIMIENTO = "tasa_rendimiento"; public $PAGADERO = "pagadero"; public $ESTATUSACTIVO = "estatusactivo";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "personas_aports_tipos";}
	function getKey(){ return "personas_aports_tipos_id";}
	function personas_aports_tipos_id($v = false){ if($v !== false){$this->mCampos["personas_aports_tipos_id"]["V"] =  $v; } return new MQLCampo($this->mCampos["personas_aports_tipos_id"]);}
	function operacion_tipo_id($v = false){ if($v !== false){$this->mCampos["operacion_tipo_id"]["V"] =  $v; } return new MQLCampo($this->mCampos["operacion_tipo_id"]);}
	function tasa_rendimiento($v = false){ if($v !== false){$this->mCampos["tasa_rendimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_rendimiento"]);}
	function pagadero($v = false){ if($v !== false){$this->mCampos["pagadero"]["V"] =  $v; } return new MQLCampo($this->mCampos["pagadero"]);}
	function estatusactivo($v = false){ if($v !== false){$this->mCampos["estatusactivo"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatusactivo"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	personas_ae_scian	-	Generado:	[19/5/2016 17:48]	*/
/*	ORM: Tabla:	personas_ae_scian	-	Generado:	[08/4/2017 14:04]	*/
class cPersonas_ae_scian {
	private $mCampos	= array("clave_interna" => array("N"=>"clave_interna","T"=>"BIGINT","V"=>"","L"=>20),"clave_de_actividad" => array("N"=>"clave_de_actividad","T"=>"VARCHAR","V"=>"","L"=>20),"nombre_de_la_actividad" => array("N"=>"nombre_de_la_actividad","T"=>"VARCHAR","V"=>"","L"=>200),"clasificacion" => array("N"=>"clasificacion","T"=>"VARCHAR","V"=>"","L"=>20),"clave_de_superior" => array("N"=>"clave_de_superior","T"=>"BIGINT","V"=>"0","L"=>20),"clave_aml" => array("N"=>"clave_aml","T"=>"VARCHAR","V"=>"9999999","L"=>20),"sector" => array("N"=>"sector","T"=>"INT","V"=>"81","L"=>3),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "personas_ae_scian";}
	function getKey(){ return "clave_interna";}
	function clave_interna($v = false){ if($v !== false){$this->mCampos["clave_interna"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_interna"]);}
	function clave_de_actividad($v = false){ if($v !== false){$this->mCampos["clave_de_actividad"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_actividad"]);}
	function nombre_de_la_actividad($v = false){ if($v !== false){$this->mCampos["nombre_de_la_actividad"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_de_la_actividad"]);}
	function clasificacion($v = false){ if($v !== false){$this->mCampos["clasificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["clasificacion"]);}
	function clave_de_superior($v = false){ if($v !== false){$this->mCampos["clave_de_superior"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_superior"]);}
	function clave_aml($v = false){ if($v !== false){$this->mCampos["clave_aml"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_aml"]);}
	function sector($v = false){ if($v !== false){$this->mCampos["sector"]["V"] =  $v; } return new MQLCampo($this->mCampos["sector"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}

/*	ORM: Tabla:	personas_morales_anx	-	Generado:	[12/8/2016 11:52]	*/
/*	ORM: Tabla:	personas_morales_anx	-	Generado:	[25/9/2017 19:10]	*/
class cPersonas_morales_anx {
	private $mCampos	= array("idpersonas_morales_anx" => array("N"=>"idpersonas_morales_anx","T"=>"INT","V"=>"","L"=>11),"persona" => array("N"=>"persona","T"=>"BIGINT","V"=>"","L"=>20),"idregistro_publico" => array("N"=>"idregistro_publico","T"=>"VARCHAR","V"=>"","L"=>20),"fecha_de_constitucion" => array("N"=>"fecha_de_constitucion","T"=>"DATE","V"=>"","L"=>0),"idacta_constitucion" => array("N"=>"idacta_constitucion","T"=>"VARCHAR","V"=>"","L"=>50),"idpoder_representante" => array("N"=>"idpoder_representante","T"=>"VARCHAR","V"=>"","L"=>20),"fechapoder_representante" => array("N"=>"fechapoder_representante","T"=>"DATE","V"=>"","L"=>0),"nombre_notario" => array("N"=>"nombre_notario","T"=>"VARCHAR","V"=>"","L"=>100),"clave_notaria" => array("N"=>"clave_notaria","T"=>"VARCHAR","V"=>"","L"=>20),"idregistro1" => array("N"=>"idregistro1","T"=>"VARCHAR","V"=>"","L"=>20),"idregistro2" => array("N"=>"idregistro2","T"=>"VARCHAR","V"=>"","L"=>20),"activo" => array("N"=>"activo","T"=>"INT","V"=>"0","L"=>2),"fecha_de_baja" => array("N"=>"fecha_de_baja","T"=>"DATE","V"=>"","L"=>0),"notaria_poder" => array("N"=>"notaria_poder","T"=>"VARCHAR","V"=>"","L"=>10),"notario_poder" => array("N"=>"notario_poder","T"=>"VARCHAR","V"=>"","L"=>100),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "personas_morales_anx";}
	function getKey(){ return "idpersonas_morales_anx";}
	function idpersonas_morales_anx($v = false){ if($v !== false){$this->mCampos["idpersonas_morales_anx"]["V"] =  $v; } return new MQLCampo($this->mCampos["idpersonas_morales_anx"]);}
	function persona($v = false){ if($v !== false){$this->mCampos["persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["persona"]);}
	function idregistro_publico($v = false){ if($v !== false){$this->mCampos["idregistro_publico"]["V"] =  $v; } return new MQLCampo($this->mCampos["idregistro_publico"]);}
	function fecha_de_constitucion($v = false){ if($v !== false){$this->mCampos["fecha_de_constitucion"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_constitucion"]);}
	function idacta_constitucion($v = false){ if($v !== false){$this->mCampos["idacta_constitucion"]["V"] =  $v; } return new MQLCampo($this->mCampos["idacta_constitucion"]);}
	function idpoder_representante($v = false){ if($v !== false){$this->mCampos["idpoder_representante"]["V"] =  $v; } return new MQLCampo($this->mCampos["idpoder_representante"]);}
	function fechapoder_representante($v = false){ if($v !== false){$this->mCampos["fechapoder_representante"]["V"] =  $v; } return new MQLCampo($this->mCampos["fechapoder_representante"]);}
	function nombre_notario($v = false){ if($v !== false){$this->mCampos["nombre_notario"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_notario"]);}
	function clave_notaria($v = false){ if($v !== false){$this->mCampos["clave_notaria"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_notaria"]);}
	function idregistro1($v = false){ if($v !== false){$this->mCampos["idregistro1"]["V"] =  $v; } return new MQLCampo($this->mCampos["idregistro1"]);}
	function idregistro2($v = false){ if($v !== false){$this->mCampos["idregistro2"]["V"] =  $v; } return new MQLCampo($this->mCampos["idregistro2"]);}
	function activo($v = false){ if($v !== false){$this->mCampos["activo"]["V"] =  $v; } return new MQLCampo($this->mCampos["activo"]);}
	function fecha_de_baja($v = false){ if($v !== false){$this->mCampos["fecha_de_baja"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_baja"]);}
	function notaria_poder($v = false){ if($v !== false){$this->mCampos["notaria_poder"]["V"] =  $v; } return new MQLCampo($this->mCampos["notaria_poder"]);}
	function notario_poder($v = false){ if($v !== false){$this->mCampos["notario_poder"]["V"] =  $v; } return new MQLCampo($this->mCampos["notario_poder"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}

/*	ORM: Tabla:	personas_consulta_lista	-	Generado:	[25/8/2016 15:47]	*/
/*	ORM: Tabla:	personas_consulta_lista	-	Generado:	[22/9/2016 17:56]	*/
/*	ORM: Tabla:	personas_consulta_lista	-	Generado:	[16/12/2016 13:19]	*/
class cPersonas_consulta_lista {
	private $mCampos	= array("idpersonas_consulta_lista" => array("N"=>"idpersonas_consulta_lista","T"=>"INT","V"=>"","L"=>11),"persona" => array("N"=>"persona","T"=>"BIGINT","V"=>"0","L"=>20),"fecha" => array("N"=>"fecha","T"=>"DATE","V"=>"","L"=>0),"tiempo" => array("N"=>"tiempo","T"=>"INT","V"=>"0","L"=>10),"url" => array("N"=>"url","T"=>"TEXT","V"=>"","L"=>0),"tipo" => array("N"=>"tipo","T"=>"VARCHAR","V"=>"","L"=>15),"proveedor" => array("N"=>"proveedor","T"=>"VARCHAR","V"=>"interno","L"=>15),"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"0","L"=>8),"coincidente" => array("N"=>"coincidente","T"=>"INT","V"=>"0","L"=>2),"razones" => array("N"=>"razones","T"=>"VARCHAR","V"=>"","L"=>100),"textocoincidente" => array("N"=>"textocoincidente","T"=>"VARCHAR","V"=>"","L"=>150),"contenido" => array("N"=>"contenido","T"=>"MEDIUMTEXT","V"=>"","L"=>0),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "personas_consulta_lista";}
	function getKey(){ return "idpersonas_consulta_lista";}
	function idpersonas_consulta_lista($v = false){ if($v !== false){$this->mCampos["idpersonas_consulta_lista"]["V"] =  $v; } return new MQLCampo($this->mCampos["idpersonas_consulta_lista"]);}
	function persona($v = false){ if($v !== false){$this->mCampos["persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["persona"]);}
	function fecha($v = false){ if($v !== false){$this->mCampos["fecha"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha"]);}
	function tiempo($v = false){ if($v !== false){$this->mCampos["tiempo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tiempo"]);}
	function url($v = false){ if($v !== false){$this->mCampos["url"]["V"] =  $v; } return new MQLCampo($this->mCampos["url"]);}
	function tipo($v = false){ if($v !== false){$this->mCampos["tipo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo"]);}
	function proveedor($v = false){ if($v !== false){$this->mCampos["proveedor"]["V"] =  $v; } return new MQLCampo($this->mCampos["proveedor"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function coincidente($v = false){ if($v !== false){$this->mCampos["coincidente"]["V"] =  $v; } return new MQLCampo($this->mCampos["coincidente"]);}
	function razones($v = false){ if($v !== false){$this->mCampos["razones"]["V"] =  $v; } return new MQLCampo($this->mCampos["razones"]);}
	function textocoincidente($v = false){ if($v !== false){$this->mCampos["textocoincidente"]["V"] =  $v; } return new MQLCampo($this->mCampos["textocoincidente"]);}
	function contenido($v = false){ if($v !== false){$this->mCampos["contenido"]["V"] =  $v; } return new MQLCampo($this->mCampos["contenido"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}
/*	ORM: Tabla:	personas_proveedores	-	Generado:	[18/1/2017 16:39]	*/
/*	ORM: Tabla:	personas_proveedores	-	Generado:	[03/2/2018 20:47]	*/
class cPersonas_proveedores {
	private $mCampos	= array("idpersonas_proveedores" => array("N"=>"idpersonas_proveedores","T"=>"INT","V"=>"","L"=>11),"persona" => array("N"=>"persona","T"=>"BIGINT","V"=>"0","L"=>20),"alias" => array("N"=>"alias","T"=>"VARCHAR","V"=>"","L"=>20),"estatus" => array("N"=>"estatus","T"=>"INT","V"=>"1","L"=>2));
	public $IDPERSONAS_PROVEEDORES = "idpersonas_proveedores"; public $PERSONA = "persona"; public $ALIAS = "alias"; public $ESTATUS = "estatus";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "personas_proveedores";}
	function getKey(){ return "idpersonas_proveedores";}
	function idpersonas_proveedores($v = false){ if($v !== false){$this->mCampos["idpersonas_proveedores"]["V"] =  $v; } return new MQLCampo($this->mCampos["idpersonas_proveedores"]);}
	function persona($v = false){ if($v !== false){$this->mCampos["persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["persona"]);}
	function alias($v = false){ if($v !== false){$this->mCampos["alias"]["V"] =  $v; } return new MQLCampo($this->mCampos["alias"]);}
	function estatus($v = false){ if($v !== false){$this->mCampos["estatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	personas_aseguradoras	-	Generado:	[07/6/2017 12:57]	*/
/*	ORM: Tabla:	personas_aseguradoras	-	Generado:	[03/2/2018 20:46]	*/
class cPersonas_aseguradoras {
	private $mCampos	= array("idpersonas_aseguradoras" => array("N"=>"idpersonas_aseguradoras","T"=>"INT","V"=>"","L"=>11),"persona" => array("N"=>"persona","T"=>"BIGINT","V"=>"","L"=>20),"alias" => array("N"=>"alias","T"=>"VARCHAR","V"=>"","L"=>40),"estatus" => array("N"=>"estatus","T"=>"INT","V"=>"1","L"=>2));
	public $IDPERSONAS_ASEGURADORAS = "idpersonas_aseguradoras"; public $PERSONA = "persona"; public $ALIAS = "alias"; public $ESTATUS = "estatus";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "personas_aseguradoras";}
	function getKey(){ return "idpersonas_aseguradoras";}
	function idpersonas_aseguradoras($v = false){ if($v !== false){$this->mCampos["idpersonas_aseguradoras"]["V"] =  $v; } return new MQLCampo($this->mCampos["idpersonas_aseguradoras"]);}
	function persona($v = false){ if($v !== false){$this->mCampos["persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["persona"]);}
	function alias($v = false){ if($v !== false){$this->mCampos["alias"]["V"] =  $v; } return new MQLCampo($this->mCampos["alias"]);}
	function estatus($v = false){ if($v !== false){$this->mCampos["estatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
}
//============================================================================ END PERSONAS
/*	ORM: Tabla:	creditos_garantias	-	Generado:	[14/6/2016 12:56]	*/
/*	ORM: Tabla:	creditos_garantias	-	Generado:	[26/12/2016 11:51]	*/
/*	ORM: Tabla:	creditos_garantias	-	Generado:	[27/1/2017 09:38]	*/
class cCreditos_garantias {
	private $mCampos	= array("idcreditos_garantias" => array("N"=>"idcreditos_garantias","T"=>"INT","V"=>"","L"=>8),"socio_garantia" => array("N"=>"socio_garantia","T"=>"BIGINT","V"=>"0","L"=>20),"solicitud_garantia" => array("N"=>"solicitud_garantia","T"=>"BIGINT","V"=>"1","L"=>20),"tipo_garantia" => array("N"=>"tipo_garantia","T"=>"INT","V"=>"99","L"=>4),"fecha_recibo" => array("N"=>"fecha_recibo","T"=>"DATE","V"=>"0000-00-00","L"=>0),"fecha_adquisicion" => array("N"=>"fecha_adquisicion","T"=>"DATE","V"=>"0000-00-00","L"=>0),"tipo_valuacion" => array("N"=>"tipo_valuacion","T"=>"INT","V"=>"99","L"=>4),"monto_valuado" => array("N"=>"monto_valuado","T"=>"DOUBLE","V"=>"0.00","L"=>25),"observaciones" => array("N"=>"observaciones","T"=>"VARCHAR","V"=>"","L"=>80),"documento_presentado" => array("N"=>"documento_presentado","T"=>"VARCHAR","V"=>"","L"=>100),"estatus_actual" => array("N"=>"estatus_actual","T"=>"INT","V"=>"1","L"=>4),"fecha_resguardo" => array("N"=>"fecha_resguardo","T"=>"DATE","V"=>"0000-00-00","L"=>0),"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"99","L"=>4),"propietario" => array("N"=>"propietario","T"=>"VARCHAR","V"=>"","L"=>60),"fecha_devolucion" => array("N"=>"fecha_devolucion","T"=>"DATE","V"=>"0000-00-00","L"=>0),"estado_presentado" => array("N"=>"estado_presentado","T"=>"INT","V"=>"99","L"=>4),"idsocio_duenno" => array("N"=>"idsocio_duenno","T"=>"BIGINT","V"=>"1","L"=>20),"descripcion" => array("N"=>"descripcion","T"=>"VARCHAR","V"=>"NA","L"=>100),"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"MATRIZ","L"=>10),"observaciones_del_resguardo" => array("N"=>"observaciones_del_resguardo","T"=>"VARCHAR","V"=>"","L"=>60),"eacp" => array("N"=>"eacp","T"=>"VARCHAR","V"=>"EN_TRAMITE","L"=>20),"caracteristica1" => array("N"=>"caracteristica1","T"=>"VARCHAR","V"=>"","L"=>40),"caracteristica2" => array("N"=>"caracteristica2","T"=>"VARCHAR","V"=>"","L"=>40),"caracteristica3" => array("N"=>"caracteristica3","T"=>"VARCHAR","V"=>"","L"=>40),"caracteristica4" => array("N"=>"caracteristica4","T"=>"VARCHAR","V"=>"","L"=>40),"marca" => array("N"=>"marca","T"=>"INT","V"=>"0","L"=>6),"extras" => array("N"=>"extras","T"=>"VARCHAR","V"=>"","L"=>100),"domicilio_vinculado" => array("N"=>"domicilio_vinculado","T"=>"INT","V"=>"0","L"=>11),"caracteristica5" => array("N"=>"caracteristica5","T"=>"VARCHAR","V"=>"","L"=>40),"tipo_origen" => array("N"=>"tipo_origen","T"=>"INT","V"=>"0","L"=>4),"clave_origen" => array("N"=>"clave_origen","T"=>"BIGINT","V"=>"0","L"=>20),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "creditos_garantias";}
	function getKey(){ return "idcreditos_garantias";}
	function idcreditos_garantias($v = false){ if($v !== false){$this->mCampos["idcreditos_garantias"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcreditos_garantias"]);}
	function socio_garantia($v = false){ if($v !== false){$this->mCampos["socio_garantia"]["V"] =  $v; } return new MQLCampo($this->mCampos["socio_garantia"]);}
	function solicitud_garantia($v = false){ if($v !== false){$this->mCampos["solicitud_garantia"]["V"] =  $v; } return new MQLCampo($this->mCampos["solicitud_garantia"]);}
	function tipo_garantia($v = false){ if($v !== false){$this->mCampos["tipo_garantia"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_garantia"]);}
	function fecha_recibo($v = false){ if($v !== false){$this->mCampos["fecha_recibo"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_recibo"]);}
	function fecha_adquisicion($v = false){ if($v !== false){$this->mCampos["fecha_adquisicion"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_adquisicion"]);}
	function tipo_valuacion($v = false){ if($v !== false){$this->mCampos["tipo_valuacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_valuacion"]);}
	function monto_valuado($v = false){ if($v !== false){$this->mCampos["monto_valuado"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_valuado"]);}
	function observaciones($v = false){ if($v !== false){$this->mCampos["observaciones"]["V"] =  $v; } return new MQLCampo($this->mCampos["observaciones"]);}
	function documento_presentado($v = false){ if($v !== false){$this->mCampos["documento_presentado"]["V"] =  $v; } return new MQLCampo($this->mCampos["documento_presentado"]);}
	function estatus_actual($v = false){ if($v !== false){$this->mCampos["estatus_actual"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus_actual"]);}
	function fecha_resguardo($v = false){ if($v !== false){$this->mCampos["fecha_resguardo"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_resguardo"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function propietario($v = false){ if($v !== false){$this->mCampos["propietario"]["V"] =  $v; } return new MQLCampo($this->mCampos["propietario"]);}
	function fecha_devolucion($v = false){ if($v !== false){$this->mCampos["fecha_devolucion"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_devolucion"]);}
	function estado_presentado($v = false){ if($v !== false){$this->mCampos["estado_presentado"]["V"] =  $v; } return new MQLCampo($this->mCampos["estado_presentado"]);}
	function idsocio_duenno($v = false){ if($v !== false){$this->mCampos["idsocio_duenno"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsocio_duenno"]);}
	function descripcion($v = false){ if($v !== false){$this->mCampos["descripcion"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function observaciones_del_resguardo($v = false){ if($v !== false){$this->mCampos["observaciones_del_resguardo"]["V"] =  $v; } return new MQLCampo($this->mCampos["observaciones_del_resguardo"]);}
	function eacp($v = false){ if($v !== false){$this->mCampos["eacp"]["V"] =  $v; } return new MQLCampo($this->mCampos["eacp"]);}
	function caracteristica1($v = false){ if($v !== false){$this->mCampos["caracteristica1"]["V"] =  $v; } return new MQLCampo($this->mCampos["caracteristica1"]);}
	function caracteristica2($v = false){ if($v !== false){$this->mCampos["caracteristica2"]["V"] =  $v; } return new MQLCampo($this->mCampos["caracteristica2"]);}
	function caracteristica3($v = false){ if($v !== false){$this->mCampos["caracteristica3"]["V"] =  $v; } return new MQLCampo($this->mCampos["caracteristica3"]);}
	function caracteristica4($v = false){ if($v !== false){$this->mCampos["caracteristica4"]["V"] =  $v; } return new MQLCampo($this->mCampos["caracteristica4"]);}
	function marca($v = false){ if($v !== false){$this->mCampos["marca"]["V"] =  $v; } return new MQLCampo($this->mCampos["marca"]);}
	function extras($v = false){ if($v !== false){$this->mCampos["extras"]["V"] =  $v; } return new MQLCampo($this->mCampos["extras"]);}
	function domicilio_vinculado($v = false){ if($v !== false){$this->mCampos["domicilio_vinculado"]["V"] =  $v; } return new MQLCampo($this->mCampos["domicilio_vinculado"]);}
	function caracteristica5($v = false){ if($v !== false){$this->mCampos["caracteristica5"]["V"] =  $v; } return new MQLCampo($this->mCampos["caracteristica5"]);}
	function tipo_origen($v = false){ if($v !== false){$this->mCampos["tipo_origen"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_origen"]);}
	function clave_origen($v = false){ if($v !== false){$this->mCampos["clave_origen"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_origen"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}
/*	ORM: Tabla:	personas_documentacion_tipos	-	Generado:	[21/2/2014 15:15]	*/
/*	ORM: Tabla:	personas_documentacion_tipos	-	Generado:	[09/12/2016 11:30]	*/
/*	ORM: Tabla:	personas_documentacion_tipos	-	Generado:	[23/2/2017 16:02]	*/
/*	ORM: Tabla:	personas_documentacion_tipos	-	Generado:	[04/6/2018 11:35]	*/
/* ORM: Tabla: personas_documentacion_tipos - Generado: [14/6/2018 18:21] */
class cPersonas_documentacion_tipos {
	private $mCampos = array("clave_de_control" => array("N"=>"clave_de_control","T"=>"INT","V"=>"","L"=>11),"nombre_del_documento" => array("N"=>"nombre_del_documento","T"=>"VARCHAR","V"=>"","L"=>100),"clasificacion" => array("N"=>"clasificacion","T"=>"VARCHAR","V"=>"","L"=>4),"vigencia_dias" => array("N"=>"vigencia_dias","T"=>"INT","V"=>"90","L"=>4),"almacen" => array("N"=>"almacen","T"=>"INT","V"=>"1","L"=>2),"checklist" => array("N"=>"checklist","T"=>"VARCHAR","V"=>"","L"=>10),"tags" => array("N"=>"tags","T"=>"VARCHAR","V"=>"","L"=>60),"estatus" => array("N"=>"estatus","T"=>"INT","V"=>"1","L"=>2),"es_ident" => array("N"=>"es_ident","T"=>"INT","V"=>"0","L"=>2));
	public $CLAVE_DE_CONTROL = "clave_de_control"; public $NOMBRE_DEL_DOCUMENTO = "nombre_del_documento"; public $CLASIFICACION = "clasificacion"; public $VIGENCIA_DIAS = "vigencia_dias"; public $ALMACEN = "almacen"; public $CHECKLIST = "checklist"; public $TAGS = "tags"; public $ESTATUS = "estatus"; public $ES_IDENT = "es_ident";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "personas_documentacion_tipos";}
	function getKey(){ return "clave_de_control";}
	function clave_de_control($v = false){ if($v !== false){$this->mCampos["clave_de_control"]["V"] = $v; } return new MQLCampo($this->mCampos["clave_de_control"]);}
	function nombre_del_documento($v = false){ if($v !== false){$this->mCampos["nombre_del_documento"]["V"] = $v; } return new MQLCampo($this->mCampos["nombre_del_documento"]);}
	function clasificacion($v = false){ if($v !== false){$this->mCampos["clasificacion"]["V"] = $v; } return new MQLCampo($this->mCampos["clasificacion"]);}
	function vigencia_dias($v = false){ if($v !== false){$this->mCampos["vigencia_dias"]["V"] = $v; } return new MQLCampo($this->mCampos["vigencia_dias"]);}
	function almacen($v = false){ if($v !== false){$this->mCampos["almacen"]["V"] = $v; } return new MQLCampo($this->mCampos["almacen"]);}
	function checklist($v = false){ if($v !== false){$this->mCampos["checklist"]["V"] = $v; } return new MQLCampo($this->mCampos["checklist"]);}
	function tags($v = false){ if($v !== false){$this->mCampos["tags"]["V"] = $v; } return new MQLCampo($this->mCampos["tags"]);}
	function estatus($v = false){ if($v !== false){$this->mCampos["estatus"]["V"] = $v; } return new MQLCampo($this->mCampos["estatus"]);}
	function es_ident($v = false){ if($v !== false){$this->mCampos["es_ident"]["V"] = $v; } return new MQLCampo($this->mCampos["es_ident"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey()); }
	function setData($datos){ $mql = new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}

/*	ORM: Tabla:	personas_perfil_transaccional	-	Generado:	[02/2/2016 16:22]	*/
/*	ORM: Tabla:	personas_perfil_transaccional	-	Generado:	[25/5/2018 17:26]	*/
class cPersonas_perfil_transaccional {
	private $mCampos	= array("idpersonas_perfil_transaccional" => array("N"=>"idpersonas_perfil_transaccional","T"=>"INT","V"=>"","L"=>11),"clave_de_persona" => array("N"=>"clave_de_persona","T"=>"BIGINT","V"=>"1","L"=>20),"fecha_de_registro" => array("N"=>"fecha_de_registro","T"=>"BIGINT","V"=>"0","L"=>20),"fecha_de_vencimiento" => array("N"=>"fecha_de_vencimiento","T"=>"BIGINT","V"=>"0","L"=>20),"clave_de_tipo_de_perfil" => array("N"=>"clave_de_tipo_de_perfil","T"=>"INT","V"=>"0","L"=>11),"pais_de_origen" => array("N"=>"pais_de_origen","T"=>"VARCHAR","V"=>"MXN","L"=>6),"maximo_de_operaciones" => array("N"=>"maximo_de_operaciones","T"=>"INT","V"=>"0","L"=>11),"cantidad_maxima" => array("N"=>"cantidad_maxima","T"=>"DOUBLE","V"=>"0.000","L"=>37),"operaciones_calculadas" => array("N"=>"operaciones_calculadas","T"=>"INT","V"=>"0","L"=>6),"cantidad_calculada" => array("N"=>"cantidad_calculada","T"=>"DOUBLE","V"=>"0.000","L"=>37),"fecha_de_calculo" => array("N"=>"fecha_de_calculo","T"=>"BIGINT","V"=>"0","L"=>20),"afectacion" => array("N"=>"afectacion","T"=>"INT","V"=>"0","L"=>3),"observaciones" => array("N"=>"observaciones","T"=>"VARCHAR","V"=>"","L"=>100),"recurso_origen" => array("N"=>"recurso_origen","T"=>"VARCHAR","V"=>"","L"=>100),"recurso_aplicacion" => array("N"=>"recurso_aplicacion","T"=>"VARCHAR","V"=>"","L"=>100),"res_origen_id" => array("N"=>"res_origen_id","T"=>"INT","V"=>"1","L"=>4),"res_aplicacion_id" => array("N"=>"res_aplicacion_id","T"=>"INT","V"=>"1","L"=>4));
	public $IDPERSONAS_PERFIL_TRANSACCIONAL = "idpersonas_perfil_transaccional"; public $CLAVE_DE_PERSONA = "clave_de_persona"; public $FECHA_DE_REGISTRO = "fecha_de_registro"; public $FECHA_DE_VENCIMIENTO = "fecha_de_vencimiento"; public $CLAVE_DE_TIPO_DE_PERFIL = "clave_de_tipo_de_perfil"; public $PAIS_DE_ORIGEN = "pais_de_origen"; public $MAXIMO_DE_OPERACIONES = "maximo_de_operaciones"; public $CANTIDAD_MAXIMA = "cantidad_maxima"; public $OPERACIONES_CALCULADAS = "operaciones_calculadas"; public $CANTIDAD_CALCULADA = "cantidad_calculada"; public $FECHA_DE_CALCULO = "fecha_de_calculo"; public $AFECTACION = "afectacion"; public $OBSERVACIONES = "observaciones"; public $RECURSO_ORIGEN = "recurso_origen"; public $RECURSO_APLICACION = "recurso_aplicacion"; public $RES_ORIGEN_ID = "res_origen_id"; public $RES_APLICACION_ID = "res_aplicacion_id";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "personas_perfil_transaccional";}
	function getKey(){ return "idpersonas_perfil_transaccional";}
	function idpersonas_perfil_transaccional($v = false){ if($v !== false){$this->mCampos["idpersonas_perfil_transaccional"]["V"] =  $v; } return new MQLCampo($this->mCampos["idpersonas_perfil_transaccional"]);}
	function clave_de_persona($v = false){ if($v !== false){$this->mCampos["clave_de_persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_persona"]);}
	function fecha_de_registro($v = false){ if($v !== false){$this->mCampos["fecha_de_registro"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_registro"]);}
	function fecha_de_vencimiento($v = false){ if($v !== false){$this->mCampos["fecha_de_vencimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_vencimiento"]);}
	function clave_de_tipo_de_perfil($v = false){ if($v !== false){$this->mCampos["clave_de_tipo_de_perfil"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_tipo_de_perfil"]);}
	function pais_de_origen($v = false){ if($v !== false){$this->mCampos["pais_de_origen"]["V"] =  $v; } return new MQLCampo($this->mCampos["pais_de_origen"]);}
	function maximo_de_operaciones($v = false){ if($v !== false){$this->mCampos["maximo_de_operaciones"]["V"] =  $v; } return new MQLCampo($this->mCampos["maximo_de_operaciones"]);}
	function cantidad_maxima($v = false){ if($v !== false){$this->mCampos["cantidad_maxima"]["V"] =  $v; } return new MQLCampo($this->mCampos["cantidad_maxima"]);}
	function operaciones_calculadas($v = false){ if($v !== false){$this->mCampos["operaciones_calculadas"]["V"] =  $v; } return new MQLCampo($this->mCampos["operaciones_calculadas"]);}
	function cantidad_calculada($v = false){ if($v !== false){$this->mCampos["cantidad_calculada"]["V"] =  $v; } return new MQLCampo($this->mCampos["cantidad_calculada"]);}
	function fecha_de_calculo($v = false){ if($v !== false){$this->mCampos["fecha_de_calculo"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_calculo"]);}
	function afectacion($v = false){ if($v !== false){$this->mCampos["afectacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["afectacion"]);}
	function observaciones($v = false){ if($v !== false){$this->mCampos["observaciones"]["V"] =  $v; } return new MQLCampo($this->mCampos["observaciones"]);}
	function recurso_origen($v = false){ if($v !== false){$this->mCampos["recurso_origen"]["V"] =  $v; } return new MQLCampo($this->mCampos["recurso_origen"]);}
	function recurso_aplicacion($v = false){ if($v !== false){$this->mCampos["recurso_aplicacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["recurso_aplicacion"]);}
	function res_origen_id($v = false){ if($v !== false){$this->mCampos["res_origen_id"]["V"] =  $v; } return new MQLCampo($this->mCampos["res_origen_id"]);}
	function res_aplicacion_id($v = false){ if($v !== false){$this->mCampos["res_aplicacion_id"]["V"] =  $v; } return new MQLCampo($this->mCampos["res_aplicacion_id"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}

/*	ORM: Tabla:	personas_perfil_transaccional_tipos	-	Generado:	[25/5/2018 17:23]	*/
class cPersonas_perfil_transaccional_tipos {
	private $mCampos	= array("idpersonas_perfil_transaccional_tipos" => array("N"=>"idpersonas_perfil_transaccional_tipos","T"=>"INT","V"=>"","L"=>11),"nombre_del_perfil" => array("N"=>"nombre_del_perfil","T"=>"VARCHAR","V"=>"","L"=>100),"tipo_de_exhibicion" => array("N"=>"tipo_de_exhibicion","T"=>"VARCHAR","V"=>"","L"=>100),"afectacion" => array("N"=>"afectacion","T"=>"INT","V"=>"1","L"=>11));
	public $IDPERSONAS_PERFIL_TRANSACCIONAL_TIPOS = "idpersonas_perfil_transaccional_tipos"; public $NOMBRE_DEL_PERFIL = "nombre_del_perfil"; public $TIPO_DE_EXHIBICION = "tipo_de_exhibicion"; public $AFECTACION = "afectacion";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "personas_perfil_transaccional_tipos";}
	function getKey(){ return "idpersonas_perfil_transaccional_tipos";}
	function idpersonas_perfil_transaccional_tipos($v = false){ if($v !== false){$this->mCampos["idpersonas_perfil_transaccional_tipos"]["V"] =  $v; } return new MQLCampo($this->mCampos["idpersonas_perfil_transaccional_tipos"]);}
	function nombre_del_perfil($v = false){ if($v !== false){$this->mCampos["nombre_del_perfil"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_del_perfil"]);}
	function tipo_de_exhibicion($v = false){ if($v !== false){$this->mCampos["tipo_de_exhibicion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_exhibicion"]);}
	function afectacion($v = false){ if($v !== false){$this->mCampos["afectacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["afectacion"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
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


/*	ORM: Tabla:	general_tmp	-	Generado:	[23/10/2017 15:32]	*/
class cGeneral_tmp {
	private $mCampos	= array("field_id1" => array("N"=>"field_id1","T"=>"BIGINT","V"=>"","L"=>25),"field_id2" => array("N"=>"field_id2","T"=>"FLOAT","V"=>"0.0000","L"=>33),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "general_tmp";}
	function getKey(){ return "field_id1";}
	function field_id1($v = false){ if($v !== false){$this->mCampos["field_id1"]["V"] =  $v; } return new MQLCampo($this->mCampos["field_id1"]);}
	function field_id2($v = false){ if($v !== false){$this->mCampos["field_id2"]["V"] =  $v; } return new MQLCampo($this->mCampos["field_id2"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}

/*	ORM: Tabla:	general_colonias	-	Generado:	[10/3/2014 15:08]	*/
class cGeneral_colonias {
	private $mCampos	= array(
			"idgeneral_colonia" => array("N"=>"idgeneral_colonia","T"=>"INT","V"=>"","L"=>10),
			"codigo_postal" => array("N"=>"codigo_postal","T"=>"INT","V"=>"0","L"=>10),
			"nombre_colonia" => array("N"=>"nombre_colonia","T"=>"VARCHAR","V"=>"","L"=>100),
			"tipo_colonia" => array("N"=>"tipo_colonia","T"=>"VARCHAR","V"=>"colonia","L"=>100),
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

/*	ORM: Tabla:	general_estados	-	Generado:	[10/3/2016 16:28]	*/
/*	ORM: Tabla:	general_estados	-	Generado:	[23/4/2018 11:37]	*/
class cGeneral_estados {
	private $mCampos	= array("idgeneral_estados" => array("N"=>"idgeneral_estados","T"=>"INT","V"=>"","L"=>11),"clave_alfanumerica" => array("N"=>"clave_alfanumerica","T"=>"VARCHAR","V"=>"CC","L"=>4),"clave_numerica" => array("N"=>"clave_numerica","T"=>"TINYINT","V"=>"4","L"=>4),"nombre" => array("N"=>"nombre","T"=>"VARCHAR","V"=>"","L"=>60),"clave_en_sic" => array("N"=>"clave_en_sic","T"=>"VARCHAR","V"=>"","L"=>8),"codigo_postal_inicial" => array("N"=>"codigo_postal_inicial","T"=>"INT","V"=>"0","L"=>11),"codigo_postal_final" => array("N"=>"codigo_postal_final","T"=>"INT","V"=>"0","L"=>11),"operacion_habilitada" => array("N"=>"operacion_habilitada","T"=>"INT","V"=>"1","L"=>2));
	public $IDGENERAL_ESTADOS = "idgeneral_estados"; public $CLAVE_ALFANUMERICA = "clave_alfanumerica"; public $CLAVE_NUMERICA = "clave_numerica"; public $NOMBRE = "nombre"; public $CLAVE_EN_SIC = "clave_en_sic"; public $CODIGO_POSTAL_INICIAL = "codigo_postal_inicial"; public $CODIGO_POSTAL_FINAL = "codigo_postal_final"; public $OPERACION_HABILITADA = "operacion_habilitada";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "general_estados";}
	function getKey(){ return "idgeneral_estados";}
	function idgeneral_estados($v = false){ if($v !== false){$this->mCampos["idgeneral_estados"]["V"] =  $v; } return new MQLCampo($this->mCampos["idgeneral_estados"]);}
	function clave_alfanumerica($v = false){ if($v !== false){$this->mCampos["clave_alfanumerica"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_alfanumerica"]);}
	function clave_numerica($v = false){ if($v !== false){$this->mCampos["clave_numerica"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_numerica"]);}
	function nombre($v = false){ if($v !== false){$this->mCampos["nombre"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre"]);}
	function clave_en_sic($v = false){ if($v !== false){$this->mCampos["clave_en_sic"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_en_sic"]);}
	function codigo_postal_inicial($v = false){ if($v !== false){$this->mCampos["codigo_postal_inicial"]["V"] =  $v; } return new MQLCampo($this->mCampos["codigo_postal_inicial"]);}
	function codigo_postal_final($v = false){ if($v !== false){$this->mCampos["codigo_postal_final"]["V"] =  $v; } return new MQLCampo($this->mCampos["codigo_postal_final"]);}
	function operacion_habilitada($v = false){ if($v !== false){$this->mCampos["operacion_habilitada"]["V"] =  $v; } return new MQLCampo($this->mCampos["operacion_habilitada"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	general_contratos	-	Generado:	[23/6/2014 10:54]	*/
/*	ORM: Tabla:	general_contratos	-	Generado:	[14/1/2017 13:54]	*/
/*	ORM: Tabla:	general_contratos	-	Generado:	[03/2/2018 12:56]	*/
class cGeneral_contratos {
	private $mCampos	= array("idgeneral_contratos" => array("N"=>"idgeneral_contratos","T"=>"INT","V"=>"","L"=>10),"tipo_contrato" => array("N"=>"tipo_contrato","T"=>"INT","V"=>"0","L"=>4),"estatus" => array("N"=>"estatus","T"=>"ENUM","V"=>"|alta|baja|","L"=>0),"titulo_del_contrato" => array("N"=>"titulo_del_contrato","T"=>"VARCHAR","V"=>"","L"=>100),"texto_del_contrato" => array("N"=>"texto_del_contrato","T"=>"LONGTEXT","V"=>"","L"=>0),"tags" => array("N"=>"tags","T"=>"VARCHAR","V"=>"","L"=>40),"ruta" => array("N"=>"ruta","T"=>"VARCHAR","V"=>"","L"=>120));
	public $IDGENERAL_CONTRATOS = "idgeneral_contratos"; public $TIPO_CONTRATO = "tipo_contrato"; public $ESTATUS = "estatus"; public $TITULO_DEL_CONTRATO = "titulo_del_contrato"; public $TEXTO_DEL_CONTRATO = "texto_del_contrato"; public $TAGS = "tags"; public $RUTA = "ruta";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "general_contratos";}
	function getKey(){ return "idgeneral_contratos";}
	function idgeneral_contratos($v = false){ if($v !== false){$this->mCampos["idgeneral_contratos"]["V"] =  $v; } return new MQLCampo($this->mCampos["idgeneral_contratos"]);}
	function tipo_contrato($v = false){ if($v !== false){$this->mCampos["tipo_contrato"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_contrato"]);}
	function estatus($v = false){ if($v !== false){$this->mCampos["estatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus"]);}
	function titulo_del_contrato($v = false){ if($v !== false){$this->mCampos["titulo_del_contrato"]["V"] =  $v; } return new MQLCampo($this->mCampos["titulo_del_contrato"]);}
	function texto_del_contrato($v = false){ if($v !== false){$this->mCampos["texto_del_contrato"]["V"] =  $v; } return new MQLCampo($this->mCampos["texto_del_contrato"]);}
	function tags($v = false){ if($v !== false){$this->mCampos["tags"]["V"] =  $v; } return new MQLCampo($this->mCampos["tags"]);}
	function ruta($v = false){ if($v !== false){$this->mCampos["ruta"]["V"] =  $v; } return new MQLCampo($this->mCampos["ruta"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	captacion_cuentas	-	Generado:	[15/7/2015 14:45]	*/
/*	ORM: Tabla:	captacion_cuentas	-	Generado:	[02/11/2016 12:09]	*/
class cCaptacion_cuentas {
	private $mCampos	= array("numero_cuenta" => array("N"=>"numero_cuenta","T"=>"BIGINT","V"=>"1","L"=>20),"numero_socio" => array("N"=>"numero_socio","T"=>"BIGINT","V"=>"1","L"=>20),"numero_grupo" => array("N"=>"numero_grupo","T"=>"BIGINT","V"=>"1","L"=>20),"numero_solicitud" => array("N"=>"numero_solicitud","T"=>"BIGINT","V"=>"1","L"=>20),"tipo_cuenta" => array("N"=>"tipo_cuenta","T"=>"INT","V"=>"99","L"=>10),"fecha_apertura" => array("N"=>"fecha_apertura","T"=>"DATE","V"=>"0000-00-00","L"=>0),"fecha_afectacion" => array("N"=>"fecha_afectacion","T"=>"DATE","V"=>"0000-00-00","L"=>0),"fecha_baja" => array("N"=>"fecha_baja","T"=>"DATE","V"=>"0000-00-00","L"=>0),"estatus_cuenta" => array("N"=>"estatus_cuenta","T"=>"INT","V"=>"99","L"=>4),"saldo_cuenta" => array("N"=>"saldo_cuenta","T"=>"DOUBLE","V"=>"0.00","L"=>33),"eacp" => array("N"=>"eacp","T"=>"VARCHAR","V"=>"","L"=>10),"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"99","L"=>10),"inversion_fecha_vcto" => array("N"=>"inversion_fecha_vcto","T"=>"DATE","V"=>"","L"=>0),"inversion_periodo" => array("N"=>"inversion_periodo","T"=>"INT","V"=>"1","L"=>4),"tasa_otorgada" => array("N"=>"tasa_otorgada","T"=>"FLOAT","V"=>"0.0000","L"=>13),"dias_invertidos" => array("N"=>"dias_invertidos","T"=>"INT","V"=>"0","L"=>10),"observacion_cuenta" => array("N"=>"observacion_cuenta","T"=>"VARCHAR","V"=>"","L"=>200),"origen_cuenta" => array("N"=>"origen_cuenta","T"=>"INT","V"=>"0","L"=>10),"tipo_titulo" => array("N"=>"tipo_titulo","T"=>"INT","V"=>"1","L"=>10),"tipo_subproducto" => array("N"=>"tipo_subproducto","T"=>"INT","V"=>"99","L"=>10),"nombre_mancomunado1" => array("N"=>"nombre_mancomunado1","T"=>"VARCHAR","V"=>"","L"=>50),"nombre_mancomunado2" => array("N"=>"nombre_mancomunado2","T"=>"VARCHAR","V"=>"","L"=>50),"minimo_mancomunantes" => array("N"=>"minimo_mancomunantes","T"=>"INT","V"=>"1","L"=>4),"saldo_conciliado" => array("N"=>"saldo_conciliado","T"=>"DOUBLE","V"=>"0.00","L"=>33),"fecha_conciliada" => array("N"=>"fecha_conciliada","T"=>"DATE","V"=>"2006-12-31","L"=>0),"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"MATRIZ","L"=>10),"ultimo_sdpm" => array("N"=>"ultimo_sdpm","T"=>"DOUBLE","V"=>"0.000","L"=>33),"oficial_de_captacion" => array("N"=>"oficial_de_captacion","T"=>"INT","V"=>"99","L"=>10),"cuenta_de_intereses" => array("N"=>"cuenta_de_intereses","T"=>"BIGINT","V"=>"0","L"=>20),"recibo_de_inversion" => array("N"=>"recibo_de_inversion","T"=>"BIGINT","V"=>"0","L"=>20),"tasa_gat" => array("N"=>"tasa_gat","T"=>"FLOAT","V"=>"0.00","L"=>13),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "captacion_cuentas";}
	function getKey(){ return "numero_cuenta";}
	function numero_cuenta($v = false){ if($v !== false){$this->mCampos["numero_cuenta"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_cuenta"]);}
	function numero_socio($v = false){ if($v !== false){$this->mCampos["numero_socio"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_socio"]);}
	function numero_grupo($v = false){ if($v !== false){$this->mCampos["numero_grupo"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_grupo"]);}
	function numero_solicitud($v = false){ if($v !== false){$this->mCampos["numero_solicitud"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_solicitud"]);}
	function tipo_cuenta($v = false){ if($v !== false){$this->mCampos["tipo_cuenta"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_cuenta"]);}
	function fecha_apertura($v = false){ if($v !== false){$this->mCampos["fecha_apertura"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_apertura"]);}
	function fecha_afectacion($v = false){ if($v !== false){$this->mCampos["fecha_afectacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_afectacion"]);}
	function fecha_baja($v = false){ if($v !== false){$this->mCampos["fecha_baja"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_baja"]);}
	function estatus_cuenta($v = false){ if($v !== false){$this->mCampos["estatus_cuenta"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus_cuenta"]);}
	function saldo_cuenta($v = false){ if($v !== false){$this->mCampos["saldo_cuenta"]["V"] =  $v; } return new MQLCampo($this->mCampos["saldo_cuenta"]);}
	function eacp($v = false){ if($v !== false){$this->mCampos["eacp"]["V"] =  $v; } return new MQLCampo($this->mCampos["eacp"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function inversion_fecha_vcto($v = false){ if($v !== false){$this->mCampos["inversion_fecha_vcto"]["V"] =  $v; } return new MQLCampo($this->mCampos["inversion_fecha_vcto"]);}
	function inversion_periodo($v = false){ if($v !== false){$this->mCampos["inversion_periodo"]["V"] =  $v; } return new MQLCampo($this->mCampos["inversion_periodo"]);}
	function tasa_otorgada($v = false){ if($v !== false){$this->mCampos["tasa_otorgada"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_otorgada"]);}
	function dias_invertidos($v = false){ if($v !== false){$this->mCampos["dias_invertidos"]["V"] =  $v; } return new MQLCampo($this->mCampos["dias_invertidos"]);}
	function observacion_cuenta($v = false){ if($v !== false){$this->mCampos["observacion_cuenta"]["V"] =  $v; } return new MQLCampo($this->mCampos["observacion_cuenta"]);}
	function origen_cuenta($v = false){ if($v !== false){$this->mCampos["origen_cuenta"]["V"] =  $v; } return new MQLCampo($this->mCampos["origen_cuenta"]);}
	function tipo_titulo($v = false){ if($v !== false){$this->mCampos["tipo_titulo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_titulo"]);}
	function tipo_subproducto($v = false){ if($v !== false){$this->mCampos["tipo_subproducto"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_subproducto"]);}
	function nombre_mancomunado1($v = false){ if($v !== false){$this->mCampos["nombre_mancomunado1"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_mancomunado1"]);}
	function nombre_mancomunado2($v = false){ if($v !== false){$this->mCampos["nombre_mancomunado2"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_mancomunado2"]);}
	function minimo_mancomunantes($v = false){ if($v !== false){$this->mCampos["minimo_mancomunantes"]["V"] =  $v; } return new MQLCampo($this->mCampos["minimo_mancomunantes"]);}
	function saldo_conciliado($v = false){ if($v !== false){$this->mCampos["saldo_conciliado"]["V"] =  $v; } return new MQLCampo($this->mCampos["saldo_conciliado"]);}
	function fecha_conciliada($v = false){ if($v !== false){$this->mCampos["fecha_conciliada"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_conciliada"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function ultimo_sdpm($v = false){ if($v !== false){$this->mCampos["ultimo_sdpm"]["V"] =  $v; } return new MQLCampo($this->mCampos["ultimo_sdpm"]);}
	function oficial_de_captacion($v = false){ if($v !== false){$this->mCampos["oficial_de_captacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["oficial_de_captacion"]);}
	function cuenta_de_intereses($v = false){ if($v !== false){$this->mCampos["cuenta_de_intereses"]["V"] =  $v; } return new MQLCampo($this->mCampos["cuenta_de_intereses"]);}
	function recibo_de_inversion($v = false){ if($v !== false){$this->mCampos["recibo_de_inversion"]["V"] =  $v; } return new MQLCampo($this->mCampos["recibo_de_inversion"]);}
	function tasa_gat($v = false){ if($v !== false){$this->mCampos["tasa_gat"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_gat"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}
/*	ORM: Tabla:	captacion_subproductos	-	Generado:	[02/11/2016 12:14]	*/
/*	ORM: Tabla:	captacion_subproductos	-	Generado:	[24/11/2017 16:16]	*/
class cCaptacion_subproductos {
	private $mCampos	= array("idcaptacion_subproductos" => array("N"=>"idcaptacion_subproductos","T"=>"INT","V"=>"","L"=>10),"descripcion_subproductos" => array("N"=>"descripcion_subproductos","T"=>"VARCHAR","V"=>"","L"=>45),"descripcion_completa" => array("N"=>"descripcion_completa","T"=>"VARCHAR","V"=>"","L"=>200),"fecha_alta" => array("N"=>"fecha_alta","T"=>"DATE","V"=>"2005-12-31","L"=>0),"fecha_baja" => array("N"=>"fecha_baja","T"=>"DATE","V"=>"2029-12-31","L"=>0),"algoritmo_de_premio" => array("N"=>"algoritmo_de_premio","T"=>"TEXT","V"=>"","L"=>0),"algoritmo_de_tasa_incremental" => array("N"=>"algoritmo_de_tasa_incremental","T"=>"TEXT","V"=>"","L"=>0),"tipo_de_cuenta" => array("N"=>"tipo_de_cuenta","T"=>"INT","V"=>"10","L"=>4),"nombre_del_contrato" => array("N"=>"nombre_del_contrato","T"=>"VARCHAR","V"=>"","L"=>100),"contable_movimientos" => array("N"=>"contable_movimientos","T"=>"VARCHAR","V"=>"","L"=>20),"contable_intereses_por_pagar" => array("N"=>"contable_intereses_por_pagar","T"=>"VARCHAR","V"=>"","L"=>20),"contable_gastos_por_intereses" => array("N"=>"contable_gastos_por_intereses","T"=>"VARCHAR","V"=>"","L"=>20),"contable_cuentas_castigadas" => array("N"=>"contable_cuentas_castigadas","T"=>"VARCHAR","V"=>"0","L"=>20),"metodo_de_abono_de_interes" => array("N"=>"metodo_de_abono_de_interes","T"=>"ENUM","V"=>"|AL_FIN_DE_MES|AL_VENCIMIENTO|","L"=>0),"destino_del_interes" => array("N"=>"destino_del_interes","T"=>"ENUM","V"=>"|CUENTA|NUEVA|CUENTA_INTERESES|","L"=>0),"algoritmo_modificador_del_interes" => array("N"=>"algoritmo_modificador_del_interes","T"=>"TEXT","V"=>"","L"=>0),"estatus" => array("N"=>"estatus","T"=>"INT","V"=>"1","L"=>2));
	public $IDCAPTACION_SUBPRODUCTOS	= "idcaptacion_subproductos";
	public $DESCRIPCION_SUBPRODUCTOS	= "descripcion_subproductos";
	public $DESCRIPCION_COMPLETA	= "descripcion_completa";
	public $FECHA_ALTA	= "fecha_alta";
	public $FECHA_BAJA	= "fecha_baja";
	public $ALGORITMO_DE_PREMIO	= "algoritmo_de_premio";
	public $ALGORITMO_DE_TASA_INCREMENTAL	= "algoritmo_de_tasa_incremental";
	public $TIPO_DE_CUENTA	= "tipo_de_cuenta";
	public $NOMBRE_DEL_CONTRATO	= "nombre_del_contrato";
	public $CONTABLE_MOVIMIENTOS	= "contable_movimientos";
	public $CONTABLE_INTERESES_POR_PAGAR	= "contable_intereses_por_pagar";
	public $CONTABLE_GASTOS_POR_INTERESES	= "contable_gastos_por_intereses";
	public $CONTABLE_CUENTAS_CASTIGADAS	= "contable_cuentas_castigadas";
	public $METODO_DE_ABONO_DE_INTERES	= "metodo_de_abono_de_interes";
	public $DESTINO_DEL_INTERES	= "destino_del_interes";
	public $ALGORITMO_MODIFICADOR_DEL_INTERES	= "algoritmo_modificador_del_interes";
	public $ESTATUS	= "estatus";
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
	function estatus($v = false){ if($v !== false){$this->mCampos["estatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	captacion_tasas	-	Generado:	[02/11/2016 12:14]	*/
class cCaptacion_tasas {
	private $mCampos	= array("idcaptacion_tasas" => array("N"=>"idcaptacion_tasas","T"=>"INT","V"=>"","L"=>4),"tasa_efectiva" => array("N"=>"tasa_efectiva","T"=>"FLOAT","V"=>"0.0000","L"=>13),"modalidad_cuenta" => array("N"=>"modalidad_cuenta","T"=>"INT","V"=>"0","L"=>4),"monto_mayor_a" => array("N"=>"monto_mayor_a","T"=>"DOUBLE","V"=>"0.00","L"=>37),"monto_menor_a" => array("N"=>"monto_menor_a","T"=>"DOUBLE","V"=>"0.00","L"=>37),"dias_mayor_a" => array("N"=>"dias_mayor_a","T"=>"INT","V"=>"0","L"=>4),"dias_menor_a" => array("N"=>"dias_menor_a","T"=>"INT","V"=>"0","L"=>4),"subproducto" => array("N"=>"subproducto","T"=>"INT","V"=>"0","L"=>6),);
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
/*	ORM: Tabla:	captacion_sdpm_historico	-	Generado:	[18/4/2016 17:34]	*/
class cCaptacion_sdpm_historico {
	private $mCampos	= array(
			"idcaptacion_sdpm_historico" => array("N"=>"idcaptacion_sdpm_historico","T"=>"INT","V"=>"","L"=>10),
			"ejercicio" => array("N"=>"ejercicio","T"=>"INT","V"=>"","L"=>10),
			"periodo" => array("N"=>"periodo","T"=>"INT","V"=>"","L"=>10),
			"cuenta" => array("N"=>"cuenta","T"=>"BIGINT","V"=>"0","L"=>25),
			"fecha" => array("N"=>"fecha","T"=>"DATE","V"=>"","L"=>0),
			"dias" => array("N"=>"dias","T"=>"INT","V"=>"","L"=>10),
			"tasa" => array("N"=>"tasa","T"=>"FLOAT","V"=>"","L"=>17),
			"monto" => array("N"=>"monto","T"=>"DOUBLE","V"=>"","L"=>29),
			"recibo" => array("N"=>"recibo","T"=>"BIGINT","V"=>"0","L"=>25),
			"numero_de_socio" => array("N"=>"numero_de_socio","T"=>"BIGINT","V"=>"1","L"=>25),
			"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"matriz","L"=>20),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "captacion_sdpm_historico";}
	function getKey(){ return "idcaptacion_sdpm_historico";}
	function idcaptacion_sdpm_historico($v = false){ if($v !== false){$this->mCampos["idcaptacion_sdpm_historico"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcaptacion_sdpm_historico"]);}
	function ejercicio($v = false){ if($v !== false){$this->mCampos["ejercicio"]["V"] =  $v; } return new MQLCampo($this->mCampos["ejercicio"]);}
	function periodo($v = false){ if($v !== false){$this->mCampos["periodo"]["V"] =  $v; } return new MQLCampo($this->mCampos["periodo"]);}
	function cuenta($v = false){ if($v !== false){$this->mCampos["cuenta"]["V"] =  $v; } return new MQLCampo($this->mCampos["cuenta"]);}
	function fecha($v = false){ if($v !== false){$this->mCampos["fecha"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha"]);}
	function dias($v = false){ if($v !== false){$this->mCampos["dias"]["V"] =  $v; } return new MQLCampo($this->mCampos["dias"]);}
	function tasa($v = false){ if($v !== false){$this->mCampos["tasa"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa"]);}
	function monto($v = false){ if($v !== false){$this->mCampos["monto"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto"]);}
	function recibo($v = false){ if($v !== false){$this->mCampos["recibo"]["V"] =  $v; } return new MQLCampo($this->mCampos["recibo"]);}
	function numero_de_socio($v = false){ if($v !== false){$this->mCampos["numero_de_socio"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_de_socio"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}
/*	ORM: Tabla:	captacion_cuentasorigen	-	Generado:	[24/11/2017 16:44]	*/
class cCaptacion_cuentasorigen {
	private $mCampos	= array("idcaptacion_cuentasorigen" => array("N"=>"idcaptacion_cuentasorigen","T"=>"INT","V"=>"0","L"=>4),"descripcion_cuentasorigen" => array("N"=>"descripcion_cuentasorigen","T"=>"VARCHAR","V"=>"","L"=>150),"origen_cuenta" => array("N"=>"origen_cuenta","T"=>"INT","V"=>"0","L"=>4),"estatusactivo" => array("N"=>"estatusactivo","T"=>"INT","V"=>"1","L"=>2));
	public $IDCAPTACION_CUENTASORIGEN	= "idcaptacion_cuentasorigen";
	public $DESCRIPCION_CUENTASORIGEN	= "descripcion_cuentasorigen";
	public $ORIGEN_CUENTA	= "origen_cuenta";
	public $ESTATUSACTIVO	= "estatusactivo";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "captacion_cuentasorigen";}
	function getKey(){ return "idcaptacion_cuentasorigen";}
	function idcaptacion_cuentasorigen($v = false){ if($v !== false){$this->mCampos["idcaptacion_cuentasorigen"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcaptacion_cuentasorigen"]);}
	function descripcion_cuentasorigen($v = false){ if($v !== false){$this->mCampos["descripcion_cuentasorigen"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_cuentasorigen"]);}
	function origen_cuenta($v = false){ if($v !== false){$this->mCampos["origen_cuenta"]["V"] =  $v; } return new MQLCampo($this->mCampos["origen_cuenta"]);}
	function estatusactivo($v = false){ if($v !== false){$this->mCampos["estatusactivo"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatusactivo"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	captacion_cuentasestatus	-	Generado:	[03/4/2018 22:27]	*/
class cCaptacion_cuentasestatus {
	private $mCampos	= array("idcaptacion_cuentasestatus" => array("N"=>"idcaptacion_cuentasestatus","T"=>"INT","V"=>"0","L"=>4),"descripcion_cuentasestatus" => array("N"=>"descripcion_cuentasestatus","T"=>"VARCHAR","V"=>"","L"=>45));
	public $IDCAPTACION_CUENTASESTATUS = "idcaptacion_cuentasestatus"; public $DESCRIPCION_CUENTASESTATUS = "descripcion_cuentasestatus";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "captacion_cuentasestatus";}
	function getKey(){ return "idcaptacion_cuentasestatus";}
	function idcaptacion_cuentasestatus($v = false){ if($v !== false){$this->mCampos["idcaptacion_cuentasestatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcaptacion_cuentasestatus"]);}
	function descripcion_cuentasestatus($v = false){ if($v !== false){$this->mCampos["descripcion_cuentasestatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_cuentasestatus"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	captacion_cuentastipos	-	Generado:	[03/4/2018 22:28]	*/
class cCaptacion_cuentastipos {
	private $mCampos	= array("idcaptacion_cuentastipos" => array("N"=>"idcaptacion_cuentastipos","T"=>"INT","V"=>"0","L"=>4),"descripcion_cuentastipos" => array("N"=>"descripcion_cuentastipos","T"=>"VARCHAR","V"=>"","L"=>45),"tipo_cuenta" => array("N"=>"tipo_cuenta","T"=>"INT","V"=>"","L"=>4));
	public $IDCAPTACION_CUENTASTIPOS = "idcaptacion_cuentastipos"; public $DESCRIPCION_CUENTASTIPOS = "descripcion_cuentastipos"; public $TIPO_CUENTA = "tipo_cuenta";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "captacion_cuentastipos";}
	function getKey(){ return "idcaptacion_cuentastipos";}
	function idcaptacion_cuentastipos($v = false){ if($v !== false){$this->mCampos["idcaptacion_cuentastipos"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcaptacion_cuentastipos"]);}
	function descripcion_cuentastipos($v = false){ if($v !== false){$this->mCampos["descripcion_cuentastipos"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_cuentastipos"]);}
	function tipo_cuenta($v = false){ if($v !== false){$this->mCampos["tipo_cuenta"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_cuenta"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	captacion_tipotitulo	-	Generado:	[03/4/2018 22:31]	*/
class cCaptacion_tipotitulo {
	private $mCampos	= array("idcaptacion_tipotitulo" => array("N"=>"idcaptacion_tipotitulo","T"=>"INT","V"=>"0","L"=>4),"descripcion_tipotitulo" => array("N"=>"descripcion_tipotitulo","T"=>"VARCHAR","V"=>"","L"=>45),"tipo_titulo" => array("N"=>"tipo_titulo","T"=>"INT","V"=>"0","L"=>4));
	public $IDCAPTACION_TIPOTITULO = "idcaptacion_tipotitulo"; public $DESCRIPCION_TIPOTITULO = "descripcion_tipotitulo"; public $TIPO_TITULO = "tipo_titulo";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "captacion_tipotitulo";}
	function getKey(){ return "idcaptacion_tipotitulo";}
	function idcaptacion_tipotitulo($v = false){ if($v !== false){$this->mCampos["idcaptacion_tipotitulo"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcaptacion_tipotitulo"]);}
	function descripcion_tipotitulo($v = false){ if($v !== false){$this->mCampos["descripcion_tipotitulo"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_tipotitulo"]);}
	function tipo_titulo($v = false){ if($v !== false){$this->mCampos["tipo_titulo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_titulo"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}



/*	ORM: Tabla:	personas_actividad_economica_tipos	-	Generado:	[15/12/2014 09:17]	*/
/*	ORM: Tabla:	personas_actividad_economica_tipos	-	Generado:	[22/11/2016 09:26]	*/
class cPersonas_actividad_economica_tipos {
	private $mCampos	= array("clave_interna" => array("N"=>"clave_interna","T"=>"BIGINT","V"=>"","L"=>20),"clave_de_actividad" => array("N"=>"clave_de_actividad","T"=>"VARCHAR","V"=>"","L"=>20),"nombre_de_la_actividad" => array("N"=>"nombre_de_la_actividad","T"=>"VARCHAR","V"=>"","L"=>200),"descripcion_detallada" => array("N"=>"descripcion_detallada","T"=>"LONGTEXT","V"=>"","L"=>0),"productos" => array("N"=>"productos","T"=>"VARCHAR","V"=>"","L"=>200),"clasificacion" => array("N"=>"clasificacion","T"=>"VARCHAR","V"=>"","L"=>20),"clave_de_superior" => array("N"=>"clave_de_superior","T"=>"BIGINT","V"=>"0","L"=>20),"nivel_de_riesgo" => array("N"=>"nivel_de_riesgo","T"=>"INT","V"=>"1","L"=>4),"califica_para_pep" => array("N"=>"califica_para_pep","T"=>"INT","V"=>"0","L"=>2),"scian" => array("N"=>"scian","T"=>"VARCHAR","V"=>"","L"=>20),);
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
	function scian($v = false){ if($v !== false){$this->mCampos["scian"]["V"] =  $v; } return new MQLCampo($this->mCampos["scian"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	personas_domicilios_paises	-	Generado:	[03/2/2016 15:52]	*/
class cPersonas_domicilios_paises {
	private $mCampos	= array(
			"clave_de_control" => array("N"=>"clave_de_control","T"=>"VARCHAR","V"=>"","L"=>10),
			"nombre_oficial" => array("N"=>"nombre_oficial","T"=>"VARCHAR","V"=>"","L"=>150),
			"es_paraiso_fiscal" => array("N"=>"es_paraiso_fiscal","T"=>"INT","V"=>"0","L"=>11),
			"es_considerado_riesgo" => array("N"=>"es_considerado_riesgo","T"=>"INT","V"=>"0","L"=>11),
			"clave_numerica" => array("N"=>"clave_numerica","T"=>"INT","V"=>"","L"=>11),
			"clave_alfanumerica" => array("N"=>"clave_alfanumerica","T"=>"VARCHAR","V"=>"","L"=>10),
			"gentilicio" => array("N"=>"gentilicio","T"=>"VARCHAR","V"=>"","L"=>40),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "personas_domicilios_paises";}
	function getKey(){ return "clave_de_control";}
	function clave_de_control($v = false){ if($v !== false){$this->mCampos["clave_de_control"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_control"]);}
	function nombre_oficial($v = false){ if($v !== false){$this->mCampos["nombre_oficial"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_oficial"]);}
	function es_paraiso_fiscal($v = false){ if($v !== false){$this->mCampos["es_paraiso_fiscal"]["V"] =  $v; } return new MQLCampo($this->mCampos["es_paraiso_fiscal"]);}
	function es_considerado_riesgo($v = false){ if($v !== false){$this->mCampos["es_considerado_riesgo"]["V"] =  $v; } return new MQLCampo($this->mCampos["es_considerado_riesgo"]);}
	function clave_numerica($v = false){ if($v !== false){$this->mCampos["clave_numerica"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_numerica"]);}
	function clave_alfanumerica($v = false){ if($v !== false){$this->mCampos["clave_alfanumerica"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_alfanumerica"]);}
	function gentilicio($v = false){ if($v !== false){$this->mCampos["gentilicio"]["V"] =  $v; } return new MQLCampo($this->mCampos["gentilicio"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}



//============================================================== SISTEMA ============================================================

/*	ORM: Tabla:	sistema_programacion_de_avisos	-	Generado:	[28/4/2016 15:25]	*/
class cSistema_programacion_de_avisos {
	private $mCampos	= array("idprograma" => array("N"=>"idprograma","T"=>"INT","V"=>"","L"=>11),"nombre_del_aviso" => array("N"=>"nombre_del_aviso","T"=>"VARCHAR","V"=>"","L"=>100),"forma_de_creacion" => array("N"=>"forma_de_creacion","T"=>"VARCHAR","V"=>"","L"=>30),"programacion" => array("N"=>"programacion","T"=>"VARCHAR","V"=>"","L"=>100),"destinatarios" => array("N"=>"destinatarios","T"=>"TEXT","V"=>"","L"=>0),"microformato" => array("N"=>"microformato","T"=>"TEXT","V"=>"","L"=>0),"tipo_de_medios" => array("N"=>"tipo_de_medios","T"=>"VARCHAR","V"=>"","L"=>50),"intent_check" => array("N"=>"intent_check","T"=>"TEXT","V"=>"","L"=>0),"intent_command" => array("N"=>"intent_command","T"=>"TEXT","V"=>"","L"=>0),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "sistema_programacion_de_avisos";}
	function getKey(){ return "idprograma";}
	function idprograma($v = false){ if($v !== false){$this->mCampos["idprograma"]["V"] =  $v; } return new MQLCampo($this->mCampos["idprograma"]);}
	function nombre_del_aviso($v = false){ if($v !== false){$this->mCampos["nombre_del_aviso"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_del_aviso"]);}
	function forma_de_creacion($v = false){ if($v !== false){$this->mCampos["forma_de_creacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["forma_de_creacion"]);}
	function programacion($v = false){ if($v !== false){$this->mCampos["programacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["programacion"]);}
	function destinatarios($v = false){ if($v !== false){$this->mCampos["destinatarios"]["V"] =  $v; } return new MQLCampo($this->mCampos["destinatarios"]);}
	function microformato($v = false){ if($v !== false){$this->mCampos["microformato"]["V"] =  $v; } return new MQLCampo($this->mCampos["microformato"]);}
	function tipo_de_medios($v = false){ if($v !== false){$this->mCampos["tipo_de_medios"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_medios"]);}
	function intent_check($v = false){ if($v !== false){$this->mCampos["intent_check"]["V"] =  $v; } return new MQLCampo($this->mCampos["intent_check"]);}
	function intent_command($v = false){ if($v !== false){$this->mCampos["intent_command"]["V"] =  $v; } return new MQLCampo($this->mCampos["intent_command"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
}

/*	ORM: Tabla:	sistema_avisos_db	-	Generado:	[28/4/2016 15:20]	*/
class cSistema_avisos_db {
	private $mCampos	= array(
			"idsistema_avisos_db" => array("N"=>"idsistema_avisos_db","T"=>"INT","V"=>"","L"=>11),
			"creado" => array("N"=>"creado","T"=>"INT","V"=>"0","L"=>11),
			"enviado" => array("N"=>"enviado","T"=>"INT","V"=>"0","L"=>11),
			"canal" => array("N"=>"canal","T"=>"VARCHAR","V"=>"","L"=>10),
			"origen" => array("N"=>"origen","T"=>"VARCHAR","V"=>"","L"=>45),
			"destinatarios" => array("N"=>"destinatarios","T"=>"TEXT","V"=>"","L"=>0),
			"titulo" => array("N"=>"titulo","T"=>"VARCHAR","V"=>"","L"=>100),
			"contenido" => array("N"=>"contenido","T"=>"TEXT","V"=>"","L"=>0),
			"comando_attach" => array("N"=>"comando_attach","T"=>"VARCHAR","V"=>"","L"=>200),
			"comando_enviar" => array("N"=>"comando_enviar","T"=>"VARCHAR","V"=>"","L"=>200),
			"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"0","L"=>6),
			"adjunto1" => array("N"=>"adjunto1","T"=>"VARCHAR","V"=>"","L"=>150),
			"adjunto2" => array("N"=>"adjunto2","T"=>"VARCHAR","V"=>"","L"=>150),
			"adjunto3" => array("N"=>"adjunto3","T"=>"VARCHAR","V"=>"","L"=>150),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "sistema_avisos_db";}
	function getKey(){ return "idsistema_avisos_db";}
	function idsistema_avisos_db($v = false){ if($v !== false){$this->mCampos["idsistema_avisos_db"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsistema_avisos_db"]);}
	function creado($v = false){ if($v !== false){$this->mCampos["creado"]["V"] =  $v; } return new MQLCampo($this->mCampos["creado"]);}
	function enviado($v = false){ if($v !== false){$this->mCampos["enviado"]["V"] =  $v; } return new MQLCampo($this->mCampos["enviado"]);}
	function canal($v = false){ if($v !== false){$this->mCampos["canal"]["V"] =  $v; } return new MQLCampo($this->mCampos["canal"]);}
	function origen($v = false){ if($v !== false){$this->mCampos["origen"]["V"] =  $v; } return new MQLCampo($this->mCampos["origen"]);}
	function destinatarios($v = false){ if($v !== false){$this->mCampos["destinatarios"]["V"] =  $v; } return new MQLCampo($this->mCampos["destinatarios"]);}
	function titulo($v = false){ if($v !== false){$this->mCampos["titulo"]["V"] =  $v; } return new MQLCampo($this->mCampos["titulo"]);}
	function contenido($v = false){ if($v !== false){$this->mCampos["contenido"]["V"] =  $v; } return new MQLCampo($this->mCampos["contenido"]);}
	function comando_attach($v = false){ if($v !== false){$this->mCampos["comando_attach"]["V"] =  $v; } return new MQLCampo($this->mCampos["comando_attach"]);}
	function comando_enviar($v = false){ if($v !== false){$this->mCampos["comando_enviar"]["V"] =  $v; } return new MQLCampo($this->mCampos["comando_enviar"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function adjunto1($v = false){ if($v !== false){$this->mCampos["adjunto1"]["V"] =  $v; } return new MQLCampo($this->mCampos["adjunto1"]);}
	function adjunto2($v = false){ if($v !== false){$this->mCampos["adjunto2"]["V"] =  $v; } return new MQLCampo($this->mCampos["adjunto2"]);}
	function adjunto3($v = false){ if($v !== false){$this->mCampos["adjunto3"]["V"] =  $v; } return new MQLCampo($this->mCampos["adjunto3"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}
/*	ORM: Tabla:	sistema_eliminados	-	Generado:	[01/11/2016 11:26]	*/
/*	ORM: Tabla:	sistema_eliminados	-	Generado:	[11/8/2017 14:10]	*/
class cSistema_eliminados {
	private $mCampos	= array("idsistema_eliminados" => array("N"=>"idsistema_eliminados","T"=>"INT","V"=>"","L"=>11),"tipoobjeto" => array("N"=>"tipoobjeto","T"=>"INT","V"=>"0","L"=>4),"contenido" => array("N"=>"contenido","T"=>"LONGTEXT","V"=>"","L"=>0),"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"0","L"=>6),"tiempo" => array("N"=>"tiempo","T"=>"INT","V"=>"0","L"=>11),"persona" => array("N"=>"persona","T"=>"BIGINT","V"=>"0","L"=>20),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "sistema_eliminados";}
	function getKey(){ return "idsistema_eliminados";}
	function idsistema_eliminados($v = false){ if($v !== false){$this->mCampos["idsistema_eliminados"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsistema_eliminados"]);}
	function tipoobjeto($v = false){ if($v !== false){$this->mCampos["tipoobjeto"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipoobjeto"]);}
	function contenido($v = false){ if($v !== false){$this->mCampos["contenido"]["V"] =  $v; } return new MQLCampo($this->mCampos["contenido"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function tiempo($v = false){ if($v !== false){$this->mCampos["tiempo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tiempo"]);}
	function persona($v = false){ if($v !== false){$this->mCampos["persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["persona"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	sistema_permisos	-	Generado:	[15/3/2017 18:38]	*/
/*	ORM: Tabla:	sistema_permisos	-	Generado:	[16/3/2017 19:05]	*/
class cSistema_permisos {
	private $mCampos	= array("idsistema_permisos" => array("N"=>"idsistema_permisos","T"=>"INT","V"=>"","L"=>11),"accion" => array("N"=>"accion","T"=>"VARCHAR","V"=>"","L"=>50),"denegado" => array("N"=>"denegado","T"=>"VARCHAR","V"=>"","L"=>200),"descripcion" => array("N"=>"descripcion","T"=>"VARCHAR","V"=>"","L"=>60),"tipo_objeto" => array("N"=>"tipo_objeto","T"=>"VARCHAR","V"=>"","L"=>20),"nombre_objeto" => array("N"=>"nombre_objeto","T"=>"VARCHAR","V"=>"","L"=>40),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "sistema_permisos";}
	function getKey(){ return "idsistema_permisos";}
	function idsistema_permisos($v = false){ if($v !== false){$this->mCampos["idsistema_permisos"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsistema_permisos"]);}
	function accion($v = false){ if($v !== false){$this->mCampos["accion"]["V"] =  $v; } return new MQLCampo($this->mCampos["accion"]);}
	function denegado($v = false){ if($v !== false){$this->mCampos["denegado"]["V"] =  $v; } return new MQLCampo($this->mCampos["denegado"]);}
	function descripcion($v = false){ if($v !== false){$this->mCampos["descripcion"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion"]);}
	function tipo_objeto($v = false){ if($v !== false){$this->mCampos["tipo_objeto"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_objeto"]);}
	function nombre_objeto($v = false){ if($v !== false){$this->mCampos["nombre_objeto"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_objeto"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	entidad_autorizaciones	-	Generado:	[08/5/2014 15:39]	*/
/*	ORM: Tabla:	entidad_autorizaciones	-	Generado:	[01/11/2016 11:15]	*/
class cEntidad_autorizaciones {
	private $mCampos	= array("clave_de_autorizacion" => array("N"=>"clave_de_autorizacion","T"=>"INT","V"=>"","L"=>11),"uuid" => array("N"=>"uuid","T"=>"VARCHAR","V"=>"","L"=>150),"tipo_de_accion" => array("N"=>"tipo_de_accion","T"=>"INT","V"=>"","L"=>11),"fecha" => array("N"=>"fecha","T"=>"BIGINT","V"=>"","L"=>20),"hora" => array("N"=>"hora","T"=>"VARCHAR","V"=>"","L"=>45),"usuario_de_origen" => array("N"=>"usuario_de_origen","T"=>"INT","V"=>"","L"=>11),"usuario_de_proceso" => array("N"=>"usuario_de_proceso","T"=>"INT","V"=>"","L"=>11),"tipo_de_documento" => array("N"=>"tipo_de_documento","T"=>"INT","V"=>"","L"=>11),"numero_de_documento" => array("N"=>"numero_de_documento","T"=>"BIGINT","V"=>"","L"=>20),"contrato" => array("N"=>"contrato","T"=>"BIGINT","V"=>"","L"=>20),"persona" => array("N"=>"persona","T"=>"BIGINT","V"=>"","L"=>20),"fecha_de_origen" => array("N"=>"fecha_de_origen","T"=>"BIGINT","V"=>"","L"=>20),"usuario_de_autorizacion" => array("N"=>"usuario_de_autorizacion","T"=>"INT","V"=>"","L"=>11),"firma_de_autorizacion" => array("N"=>"firma_de_autorizacion","T"=>"TEXT","V"=>"","L"=>0),"metadata" => array("N"=>"metadata","T"=>"TEXT","V"=>"","L"=>0),"estado_actual" => array("N"=>"estado_actual","T"=>"INT","V"=>"","L"=>4),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "entidad_autorizaciones";}
	function getKey(){ return "clave_de_autorizacion";}
	function clave_de_autorizacion($v = false){ if($v !== false){$this->mCampos["clave_de_autorizacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_autorizacion"]);}
	function uuid($v = false){ if($v !== false){$this->mCampos["uuid"]["V"] =  $v; } return new MQLCampo($this->mCampos["uuid"]);}
	function tipo_de_accion($v = false){ if($v !== false){$this->mCampos["tipo_de_accion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_accion"]);}
	function fecha($v = false){ if($v !== false){$this->mCampos["fecha"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha"]);}
	function hora($v = false){ if($v !== false){$this->mCampos["hora"]["V"] =  $v; } return new MQLCampo($this->mCampos["hora"]);}
	function usuario_de_origen($v = false){ if($v !== false){$this->mCampos["usuario_de_origen"]["V"] =  $v; } return new MQLCampo($this->mCampos["usuario_de_origen"]);}
	function usuario_de_proceso($v = false){ if($v !== false){$this->mCampos["usuario_de_proceso"]["V"] =  $v; } return new MQLCampo($this->mCampos["usuario_de_proceso"]);}
	function tipo_de_documento($v = false){ if($v !== false){$this->mCampos["tipo_de_documento"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_documento"]);}
	function numero_de_documento($v = false){ if($v !== false){$this->mCampos["numero_de_documento"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_de_documento"]);}
	function contrato($v = false){ if($v !== false){$this->mCampos["contrato"]["V"] =  $v; } return new MQLCampo($this->mCampos["contrato"]);}
	function persona($v = false){ if($v !== false){$this->mCampos["persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["persona"]);}
	function fecha_de_origen($v = false){ if($v !== false){$this->mCampos["fecha_de_origen"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_origen"]);}
	function usuario_de_autorizacion($v = false){ if($v !== false){$this->mCampos["usuario_de_autorizacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["usuario_de_autorizacion"]);}
	function firma_de_autorizacion($v = false){ if($v !== false){$this->mCampos["firma_de_autorizacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["firma_de_autorizacion"]);}
	function metadata($v = false){ if($v !== false){$this->mCampos["metadata"]["V"] =  $v; } return new MQLCampo($this->mCampos["metadata"]);}
	function estado_actual($v = false){ if($v !== false){$this->mCampos["estado_actual"]["V"] =  $v; } return new MQLCampo($this->mCampos["estado_actual"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	entidad_reglas	-	Generado:	[08/5/2014 15:40]	*/
/*	ORM: Tabla:	entidad_reglas	-	Generado:	[01/11/2016 11:16]	*/
/*	ORM: Tabla:	entidad_reglas	-	Generado:	[03/1/2017 17:56]	*/
class cEntidad_reglas {
	private $mCampos	= array("identidad_reglas" => array("N"=>"identidad_reglas","T"=>"INT","V"=>"","L"=>11),"contexto" => array("N"=>"contexto","T"=>"VARCHAR","V"=>"","L"=>20),"nombre" => array("N"=>"nombre","T"=>"VARCHAR","V"=>"","L"=>45),"evento" => array("N"=>"evento","T"=>"VARCHAR","V"=>"","L"=>40),"sujetos" => array("N"=>"sujetos","T"=>"VARCHAR","V"=>"","L"=>100),"reglas" => array("N"=>"reglas","T"=>"TEXT","V"=>"","L"=>0),"metadata" => array("N"=>"metadata","T"=>"TEXT","V"=>"","L"=>0),"valor" => array("N"=>"valor","T"=>"INT","V"=>"0","L"=>2),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "entidad_reglas";}
	function getKey(){ return "identidad_reglas";}
	function identidad_reglas($v = false){ if($v !== false){$this->mCampos["identidad_reglas"]["V"] =  $v; } return new MQLCampo($this->mCampos["identidad_reglas"]);}
	function contexto($v = false){ if($v !== false){$this->mCampos["contexto"]["V"] =  $v; } return new MQLCampo($this->mCampos["contexto"]);}
	function nombre($v = false){ if($v !== false){$this->mCampos["nombre"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre"]);}
	function evento($v = false){ if($v !== false){$this->mCampos["evento"]["V"] =  $v; } return new MQLCampo($this->mCampos["evento"]);}
	function sujetos($v = false){ if($v !== false){$this->mCampos["sujetos"]["V"] =  $v; } return new MQLCampo($this->mCampos["sujetos"]);}
	function reglas($v = false){ if($v !== false){$this->mCampos["reglas"]["V"] =  $v; } return new MQLCampo($this->mCampos["reglas"]);}
	function metadata($v = false){ if($v !== false){$this->mCampos["metadata"]["V"] =  $v; } return new MQLCampo($this->mCampos["metadata"]);}
	function valor($v = false){ if($v !== false){$this->mCampos["valor"]["V"] =  $v; } return new MQLCampo($this->mCampos["valor"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
}
/*	ORM: Tabla:	entidad_configuracion	-	Generado:	[10/2/2018 13:23]	*/
class cEntidad_configuracion {
	private $mCampos	= array("tipo" => array("N"=>"tipo","T"=>"VARCHAR","V"=>"","L"=>40),"nombre_del_parametro" => array("N"=>"nombre_del_parametro","T"=>"VARCHAR","V"=>"","L"=>80),"descripcion_del_parametro" => array("N"=>"descripcion_del_parametro","T"=>"VARCHAR","V"=>"","L"=>200),"valor_del_parametro" => array("N"=>"valor_del_parametro","T"=>"VARCHAR","V"=>"","L"=>200));
	public $TIPO = "tipo"; public $NOMBRE_DEL_PARAMETRO = "nombre_del_parametro"; public $DESCRIPCION_DEL_PARAMETRO = "descripcion_del_parametro"; public $VALOR_DEL_PARAMETRO = "valor_del_parametro";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "entidad_configuracion";}
	function getKey(){ return "nombre_del_parametro";}
	function tipo($v = false){ if($v !== false){$this->mCampos["tipo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo"]);}
	function nombre_del_parametro($v = false){ if($v !== false){$this->mCampos["nombre_del_parametro"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_del_parametro"]);}
	function descripcion_del_parametro($v = false){ if($v !== false){$this->mCampos["descripcion_del_parametro"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_del_parametro"]);}
	function valor_del_parametro($v = false){ if($v !== false){$this->mCampos["valor_del_parametro"]["V"] =  $v; } return new MQLCampo($this->mCampos["valor_del_parametro"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	entidad_pagos_perfil	-	Generado:	[04/9/2015 17:45]	*/
/*	ORM: Tabla:	entidad_pagos_perfil	-	Generado:	[22/11/2016 18:17]	*/
/*	ORM: Tabla:	entidad_pagos_perfil	-	Generado:	[20/10/2017 11:08]	*/
class cEntidad_pagos_perfil {
	private $mCampos	= array("identidad_pagos_perfil" => array("N"=>"identidad_pagos_perfil","T"=>"INT","V"=>"","L"=>11),"tipo_de_membresia" => array("N"=>"tipo_de_membresia","T"=>"INT","V"=>"0","L"=>4),"tipo_de_operacion" => array("N"=>"tipo_de_operacion","T"=>"INT","V"=>"0","L"=>6),"periocidad" => array("N"=>"periocidad","T"=>"INT","V"=>"30","L"=>4),"monto" => array("N"=>"monto","T"=>"DOUBLE","V"=>"0.00","L"=>25),"prioridad" => array("N"=>"prioridad","T"=>"INT","V"=>"0","L"=>3),"rotacion" => array("N"=>"rotacion","T"=>"VARCHAR","V"=>"","L"=>10),"fecha_de_aplicacion" => array("N"=>"fecha_de_aplicacion","T"=>"DATE","V"=>"2015-01-01","L"=>0),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "entidad_pagos_perfil";}
	function getKey(){ return "identidad_pagos_perfil";}
	function identidad_pagos_perfil($v = false){ if($v !== false){$this->mCampos["identidad_pagos_perfil"]["V"] =  $v; } return new MQLCampo($this->mCampos["identidad_pagos_perfil"]);}
	function tipo_de_membresia($v = false){ if($v !== false){$this->mCampos["tipo_de_membresia"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_membresia"]);}
	function tipo_de_operacion($v = false){ if($v !== false){$this->mCampos["tipo_de_operacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_operacion"]);}
	function periocidad($v = false){ if($v !== false){$this->mCampos["periocidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["periocidad"]);}
	function monto($v = false){ if($v !== false){$this->mCampos["monto"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto"]);}
	function prioridad($v = false){ if($v !== false){$this->mCampos["prioridad"]["V"] =  $v; } return new MQLCampo($this->mCampos["prioridad"]);}
	function rotacion($v = false){ if($v !== false){$this->mCampos["rotacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["rotacion"]);}
	function fecha_de_aplicacion($v = false){ if($v !== false){$this->mCampos["fecha_de_aplicacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_aplicacion"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}

/*	ORM: Tabla:	entidad_calificacion	-	Generado:	[04/2/2016 10:28]	*/
/*	ORM: Tabla:	entidad_calificacion	-	Generado:	[08/4/2018 11:39]	*/
class cEntidad_calificacion {
	private $mCampos	= array("identidad_calificacion" => array("N"=>"identidad_calificacion","T"=>"INT","V"=>"","L"=>11),"tipo_de_objeto" => array("N"=>"tipo_de_objeto","T"=>"INT","V"=>"","L"=>4),"clave_de_documento" => array("N"=>"clave_de_documento","T"=>"BIGINT","V"=>"0","L"=>20),"fecha_de_revision" => array("N"=>"fecha_de_revision","T"=>"DATE","V"=>"","L"=>0),"usuario" => array("N"=>"usuario","T"=>"INT","V"=>"","L"=>6),"topico" => array("N"=>"topico","T"=>"VARCHAR","V"=>"","L"=>100),"cumple" => array("N"=>"cumple","T"=>"INT","V"=>"","L"=>2),"tiempo" => array("N"=>"tiempo","T"=>"INT","V"=>"","L"=>10),"vencimiento" => array("N"=>"vencimiento","T"=>"INT","V"=>"","L"=>10),"riesgo" => array("N"=>"riesgo","T"=>"FLOAT","V"=>"0.000","L"=>13));
	public $IDENTIDAD_CALIFICACION = "identidad_calificacion"; public $TIPO_DE_OBJETO = "tipo_de_objeto"; public $CLAVE_DE_DOCUMENTO = "clave_de_documento"; public $FECHA_DE_REVISION = "fecha_de_revision"; public $USUARIO = "usuario"; public $TOPICO = "topico"; public $CUMPLE = "cumple"; public $TIEMPO = "tiempo"; public $VENCIMIENTO = "vencimiento"; public $RIESGO = "riesgo";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "entidad_calificacion";}
	function getKey(){ return "identidad_calificacion";}
	function identidad_calificacion($v = false){ if($v !== false){$this->mCampos["identidad_calificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["identidad_calificacion"]);}
	function tipo_de_objeto($v = false){ if($v !== false){$this->mCampos["tipo_de_objeto"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_objeto"]);}
	function clave_de_documento($v = false){ if($v !== false){$this->mCampos["clave_de_documento"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_documento"]);}
	function fecha_de_revision($v = false){ if($v !== false){$this->mCampos["fecha_de_revision"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_revision"]);}
	function usuario($v = false){ if($v !== false){$this->mCampos["usuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["usuario"]);}
	function topico($v = false){ if($v !== false){$this->mCampos["topico"]["V"] =  $v; } return new MQLCampo($this->mCampos["topico"]);}
	function cumple($v = false){ if($v !== false){$this->mCampos["cumple"]["V"] =  $v; } return new MQLCampo($this->mCampos["cumple"]);}
	function tiempo($v = false){ if($v !== false){$this->mCampos["tiempo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tiempo"]);}
	function vencimiento($v = false){ if($v !== false){$this->mCampos["vencimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["vencimiento"]);}
	function riesgo($v = false){ if($v !== false){$this->mCampos["riesgo"]["V"] =  $v; } return new MQLCampo($this->mCampos["riesgo"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}

/*	ORM: Tabla:	entidad_creditos_proyecciones	-	Generado:	[23/8/2016 20:06]	*/
/*	ORM: Tabla:	entidad_creditos_proyecciones	-	Generado:	[01/11/2016 11:17]	*/
class cEntidad_creditos_proyecciones {
	private $mCampos	= array("identidad_proyeccion" => array("N"=>"identidad_proyeccion","T"=>"INT","V"=>"","L"=>11),"periocidad" => array("N"=>"periocidad","T"=>"INT","V"=>"30","L"=>4),"fecha_inicial" => array("N"=>"fecha_inicial","T"=>"DATE","V"=>"","L"=>0),"fecha_final" => array("N"=>"fecha_final","T"=>"DATE","V"=>"","L"=>0),"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"todas","L"=>20),"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"0","L"=>6),"capital" => array("N"=>"capital","T"=>"DOUBLE","V"=>"0.00","L"=>39),"interes" => array("N"=>"interes","T"=>"DOUBLE","V"=>"0.00","L"=>39),"iva" => array("N"=>"iva","T"=>"DOUBLE","V"=>"0.00","L"=>39),"ahorros" => array("N"=>"ahorros","T"=>"DOUBLE","V"=>"0.00","L"=>39),"total" => array("N"=>"total","T"=>"DOUBLE","V"=>"0.00","L"=>39),"tipo" => array("N"=>"tipo","T"=>"INT","V"=>"1","L"=>4),"descripcion" => array("N"=>"descripcion","T"=>"VARCHAR","V"=>"","L"=>60),"clave" => array("N"=>"clave","T"=>"VARCHAR","V"=>"","L"=>40),"otros" => array("N"=>"otros","T"=>"DOUBLE","V"=>"0.00","L"=>39),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "entidad_creditos_proyecciones";}
	function getKey(){ return "identidad_proyeccion";}
	function identidad_proyeccion($v = false){ if($v !== false){$this->mCampos["identidad_proyeccion"]["V"] =  $v; } return new MQLCampo($this->mCampos["identidad_proyeccion"]);}
	function periocidad($v = false){ if($v !== false){$this->mCampos["periocidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["periocidad"]);}
	function fecha_inicial($v = false){ if($v !== false){$this->mCampos["fecha_inicial"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_inicial"]);}
	function fecha_final($v = false){ if($v !== false){$this->mCampos["fecha_final"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_final"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function capital($v = false){ if($v !== false){$this->mCampos["capital"]["V"] =  $v; } return new MQLCampo($this->mCampos["capital"]);}
	function interes($v = false){ if($v !== false){$this->mCampos["interes"]["V"] =  $v; } return new MQLCampo($this->mCampos["interes"]);}
	function iva($v = false){ if($v !== false){$this->mCampos["iva"]["V"] =  $v; } return new MQLCampo($this->mCampos["iva"]);}
	function ahorros($v = false){ if($v !== false){$this->mCampos["ahorros"]["V"] =  $v; } return new MQLCampo($this->mCampos["ahorros"]);}
	function total($v = false){ if($v !== false){$this->mCampos["total"]["V"] =  $v; } return new MQLCampo($this->mCampos["total"]);}
	function tipo($v = false){ if($v !== false){$this->mCampos["tipo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo"]);}
	function descripcion($v = false){ if($v !== false){$this->mCampos["descripcion"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion"]);}
	function clave($v = false){ if($v !== false){$this->mCampos["clave"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave"]);}
	function otros($v = false){ if($v !== false){$this->mCampos["otros"]["V"] =  $v; } return new MQLCampo($this->mCampos["otros"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}


//============================================================== PERSONAS ============================================================
/*	ORM: Tabla:	personas_pagos_perfil	-	Generado:	[12/9/2016 17:15]	*/
/*	ORM: Tabla:	personas_pagos_perfil	-	Generado:	[23/11/2016 10:59]	*/
class cPersonas_pagos_perfil {
	private $mCampos	= array("idpersonas_pagos_perfil" => array("N"=>"idpersonas_pagos_perfil","T"=>"INT","V"=>"","L"=>11),"clave_de_persona" => array("N"=>"clave_de_persona","T"=>"BIGINT","V"=>"0","L"=>25),"tipo_de_operacion" => array("N"=>"tipo_de_operacion","T"=>"INT","V"=>"0","L"=>6),"periocidad" => array("N"=>"periocidad","T"=>"INT","V"=>"30","L"=>4),"monto" => array("N"=>"monto","T"=>"DOUBLE","V"=>"0.00","L"=>25),"prioridad" => array("N"=>"prioridad","T"=>"INT","V"=>"0","L"=>2),"rotacion" => array("N"=>"rotacion","T"=>"VARCHAR","V"=>"","L"=>20),"fecha_de_aplicacion" => array("N"=>"fecha_de_aplicacion","T"=>"DATE","V"=>"2015-01-01","L"=>0),"membresia" => array("N"=>"membresia","T"=>"INT","V"=>"0","L"=>4),"estatus" => array("N"=>"estatus","T"=>"INT","V"=>"1","L"=>2),"finalizador" => array("N"=>"finalizador","T"=>"INT","V"=>"0","L"=>2),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "personas_pagos_perfil";}
	function getKey(){ return "idpersonas_pagos_perfil";}
	function idpersonas_pagos_perfil($v = false){ if($v !== false){$this->mCampos["idpersonas_pagos_perfil"]["V"] =  $v; } return new MQLCampo($this->mCampos["idpersonas_pagos_perfil"]);}
	function clave_de_persona($v = false){ if($v !== false){$this->mCampos["clave_de_persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_persona"]);}
	function tipo_de_operacion($v = false){ if($v !== false){$this->mCampos["tipo_de_operacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_operacion"]);}
	function periocidad($v = false){ if($v !== false){$this->mCampos["periocidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["periocidad"]);}
	function monto($v = false){ if($v !== false){$this->mCampos["monto"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto"]);}
	function prioridad($v = false){ if($v !== false){$this->mCampos["prioridad"]["V"] =  $v; } return new MQLCampo($this->mCampos["prioridad"]);}
	function rotacion($v = false){ if($v !== false){$this->mCampos["rotacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["rotacion"]);}
	function fecha_de_aplicacion($v = false){ if($v !== false){$this->mCampos["fecha_de_aplicacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_aplicacion"]);}
	function membresia($v = false){ if($v !== false){$this->mCampos["membresia"]["V"] =  $v; } return new MQLCampo($this->mCampos["membresia"]);}
	function estatus($v = false){ if($v !== false){$this->mCampos["estatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus"]);}
	function finalizador($v = false){ if($v !== false){$this->mCampos["finalizador"]["V"] =  $v; } return new MQLCampo($this->mCampos["finalizador"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	personas_pagos_plan	-	Generado:	[04/9/2015 19:23]	*/
class cPersonas_pagos_plan {
	private $mCampos	= array(
			"idpersonas_aportaciones_plan" => array("N"=>"idpersonas_aportaciones_plan","T"=>"INT","V"=>"","L"=>11),
			"tipo_de_operacion" => array("N"=>"tipo_de_operacion","T"=>"INT","V"=>"0","L"=>6),
			"persona" => array("N"=>"persona","T"=>"BIGINT","V"=>"1","L"=>25),
			"periodo" => array("N"=>"periodo","T"=>"INT","V"=>"0","L"=>4),
			"ejercicio" => array("N"=>"ejercicio","T"=>"INT","V"=>"0","L"=>5),
			"periocidad" => array("N"=>"periocidad","T"=>"INT","V"=>"30","L"=>4),
			"monto" => array("N"=>"monto","T"=>"DOUBLE","V"=>"0.00","L"=>25),
			"observaciones" => array("N"=>"observaciones","T"=>"VARCHAR","V"=>"","L"=>100),
			"estado" => array("N"=>"estado","T"=>"INT","V"=>"1","L"=>2),
			"fecha_de_cancelacion" => array("N"=>"fecha_de_cancelacion","T"=>"DATE","V"=>"0000-00-00","L"=>0),
			"tipo_de_membresia" => array("N"=>"tipo_de_membresia","T"=>"INT","V"=>"0","L"=>4),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "personas_pagos_plan";}
	function getKey(){ return "idpersonas_aportaciones_plan";}
	function idpersonas_aportaciones_plan($v = false){ if($v !== false){$this->mCampos["idpersonas_aportaciones_plan"]["V"] =  $v; } return new MQLCampo($this->mCampos["idpersonas_aportaciones_plan"]);}
	function tipo_de_operacion($v = false){ if($v !== false){$this->mCampos["tipo_de_operacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_operacion"]);}
	function persona($v = false){ if($v !== false){$this->mCampos["persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["persona"]);}
	function periodo($v = false){ if($v !== false){$this->mCampos["periodo"]["V"] =  $v; } return new MQLCampo($this->mCampos["periodo"]);}
	function ejercicio($v = false){ if($v !== false){$this->mCampos["ejercicio"]["V"] =  $v; } return new MQLCampo($this->mCampos["ejercicio"]);}
	function periocidad($v = false){ if($v !== false){$this->mCampos["periocidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["periocidad"]);}
	function monto($v = false){ if($v !== false){$this->mCampos["monto"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto"]);}
	function observaciones($v = false){ if($v !== false){$this->mCampos["observaciones"]["V"] =  $v; } return new MQLCampo($this->mCampos["observaciones"]);}
	function estado($v = false){ if($v !== false){$this->mCampos["estado"]["V"] =  $v; } return new MQLCampo($this->mCampos["estado"]);}
	function fecha_de_cancelacion($v = false){ if($v !== false){$this->mCampos["fecha_de_cancelacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_de_cancelacion"]);}
	function tipo_de_membresia($v = false){ if($v !== false){$this->mCampos["tipo_de_membresia"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_membresia"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}

/*	ORM: Tabla:	socios_grupossolidarios	-	Generado:	[04/9/2014 11:03]	*/
/*	ORM: Tabla:	socios_grupossolidarios	-	Generado:	[06/10/2016 17:14]	*/
/*	ORM: Tabla:	socios_grupossolidarios	-	Generado:	[21/4/2018 12:50]	*/
class cSocios_grupossolidarios {
	private $mCampos	= array("idsocios_grupossolidarios" => array("N"=>"idsocios_grupossolidarios","T"=>"BIGINT","V"=>"","L"=>20),"nombre_gruposolidario" => array("N"=>"nombre_gruposolidario","T"=>"VARCHAR","V"=>"","L"=>100),"colonia_gruposolidario" => array("N"=>"colonia_gruposolidario","T"=>"INT","V"=>"0","L"=>11),"direccion_gruposolidario" => array("N"=>"direccion_gruposolidario","T"=>"VARCHAR","V"=>"","L"=>100),"representante_numerosocio" => array("N"=>"representante_numerosocio","T"=>"BIGINT","V"=>"1","L"=>20),"representante_nombrecompleto" => array("N"=>"representante_nombrecompleto","T"=>"VARCHAR","V"=>"","L"=>100),"grupo_solidario" => array("N"=>"grupo_solidario","T"=>"BIGINT","V"=>"","L"=>20),"vocalvigilancia_numerosocio" => array("N"=>"vocalvigilancia_numerosocio","T"=>"BIGINT","V"=>"","L"=>20),"vocalvigilancia_nombrecompleto" => array("N"=>"vocalvigilancia_nombrecompleto","T"=>"VARCHAR","V"=>"","L"=>100),"estatusactual" => array("N"=>"estatusactual","T"=>"INT","V"=>"10","L"=>4),"nivel_ministracion" => array("N"=>"nivel_ministracion","T"=>"INT","V"=>"1","L"=>2),"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"matriz","L"=>20),"fecha_de_alta" => array("N"=>"fecha_de_alta","T"=>"DATE","V"=>"2007-04-01","L"=>0),"clave_de_persona" => array("N"=>"clave_de_persona","T"=>"BIGINT","V"=>"1","L"=>20));
	public $IDSOCIOS_GRUPOSSOLIDARIOS = "idsocios_grupossolidarios"; public $NOMBRE_GRUPOSOLIDARIO = "nombre_gruposolidario"; public $COLONIA_GRUPOSOLIDARIO = "colonia_gruposolidario"; public $DIRECCION_GRUPOSOLIDARIO = "direccion_gruposolidario"; public $REPRESENTANTE_NUMEROSOCIO = "representante_numerosocio"; public $REPRESENTANTE_NOMBRECOMPLETO = "representante_nombrecompleto"; public $GRUPO_SOLIDARIO = "grupo_solidario"; public $VOCALVIGILANCIA_NUMEROSOCIO = "vocalvigilancia_numerosocio"; public $VOCALVIGILANCIA_NOMBRECOMPLETO = "vocalvigilancia_nombrecompleto"; public $ESTATUSACTUAL = "estatusactual"; public $NIVEL_MINISTRACION = "nivel_ministracion"; public $SUCURSAL = "sucursal"; public $FECHA_DE_ALTA = "fecha_de_alta"; public $CLAVE_DE_PERSONA = "clave_de_persona";
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
/*	ORM: Tabla:	socios_relaciones	-	Generado:	[30/4/2015 16:06]	*/
/*	ORM: Tabla:	socios_relaciones	-	Generado:	[04/5/2017 18:04]	*/
class cSocios_relaciones {
	private $mCampos	= array("idsocios_relaciones" => array("N"=>"idsocios_relaciones","T"=>"INT","V"=>"","L"=>10),"socio_relacionado" => array("N"=>"socio_relacionado","T"=>"BIGINT","V"=>"1","L"=>20),"credito_relacionado" => array("N"=>"credito_relacionado","T"=>"BIGINT","V"=>"1","L"=>20),"tipo_relacion" => array("N"=>"tipo_relacion","T"=>"INT","V"=>"99","L"=>4),"numero_socio" => array("N"=>"numero_socio","T"=>"BIGINT","V"=>"1","L"=>20),"nombres" => array("N"=>"nombres","T"=>"VARCHAR","V"=>"","L"=>100),"apellido_paterno" => array("N"=>"apellido_paterno","T"=>"VARCHAR","V"=>"","L"=>40),"apellido_materno" => array("N"=>"apellido_materno","T"=>"VARCHAR","V"=>"","L"=>40),"domicilio_completo" => array("N"=>"domicilio_completo","T"=>"VARCHAR","V"=>"","L"=>200),"telefono_residencia" => array("N"=>"telefono_residencia","T"=>"VARCHAR","V"=>"","L"=>20),"telefono_movil" => array("N"=>"telefono_movil","T"=>"VARCHAR","V"=>"","L"=>20),"fecha_nacimiento" => array("N"=>"fecha_nacimiento","T"=>"DATE","V"=>"0000-00-00","L"=>0),"monto_relacionado" => array("N"=>"monto_relacionado","T"=>"DOUBLE","V"=>"0.00","L"=>33),"porcentaje_relacionado" => array("N"=>"porcentaje_relacionado","T"=>"FLOAT","V"=>"0.000","L"=>13),"fecha_alta" => array("N"=>"fecha_alta","T"=>"DATE","V"=>"0000-00-00","L"=>0),"curp" => array("N"=>"curp","T"=>"VARCHAR","V"=>"","L"=>25),"observaciones" => array("N"=>"observaciones","T"=>"VARCHAR","V"=>"","L"=>80),"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"99","L"=>6),"consanguinidad" => array("N"=>"consanguinidad","T"=>"INT","V"=>"99","L"=>4),"estatus" => array("N"=>"estatus","T"=>"INT","V"=>"99","L"=>4),"dependiente" => array("N"=>"dependiente","T"=>"INT","V"=>"2","L"=>2),"codigo" => array("N"=>"codigo","T"=>"BIGINT","V"=>"1","L"=>20),"ocupacion" => array("N"=>"ocupacion","T"=>"VARCHAR","V"=>"N/A","L"=>45),"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"matriz","L"=>10),"eacp" => array("N"=>"eacp","T"=>"VARCHAR","V"=>"EN_TRAMITE","L"=>12),"calificacion_del_referente" => array("N"=>"calificacion_del_referente","T"=>"INT","V"=>"1","L"=>4),"dato_extra_1" => array("N"=>"dato_extra_1","T"=>"VARCHAR","V"=>"","L"=>100),"dato_extra_2" => array("N"=>"dato_extra_2","T"=>"VARCHAR","V"=>"","L"=>100),"dato_extra_3" => array("N"=>"dato_extra_3","T"=>"VARCHAR","V"=>"","L"=>100),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "socios_relaciones";}
	function getKey(){ return "idsocios_relaciones";}
	function idsocios_relaciones($v = false){ if($v !== false){$this->mCampos["idsocios_relaciones"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsocios_relaciones"]);}
	function socio_relacionado($v = false){ if($v !== false){$this->mCampos["socio_relacionado"]["V"] =  $v; } return new MQLCampo($this->mCampos["socio_relacionado"]);}
	function credito_relacionado($v = false){ if($v !== false){$this->mCampos["credito_relacionado"]["V"] =  $v; } return new MQLCampo($this->mCampos["credito_relacionado"]);}
	function tipo_relacion($v = false){ if($v !== false){$this->mCampos["tipo_relacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_relacion"]);}
	function numero_socio($v = false){ if($v !== false){$this->mCampos["numero_socio"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_socio"]);}
	function nombres($v = false){ if($v !== false){$this->mCampos["nombres"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombres"]);}
	function apellido_paterno($v = false){ if($v !== false){$this->mCampos["apellido_paterno"]["V"] =  $v; } return new MQLCampo($this->mCampos["apellido_paterno"]);}
	function apellido_materno($v = false){ if($v !== false){$this->mCampos["apellido_materno"]["V"] =  $v; } return new MQLCampo($this->mCampos["apellido_materno"]);}
	function domicilio_completo($v = false){ if($v !== false){$this->mCampos["domicilio_completo"]["V"] =  $v; } return new MQLCampo($this->mCampos["domicilio_completo"]);}
	function telefono_residencia($v = false){ if($v !== false){$this->mCampos["telefono_residencia"]["V"] =  $v; } return new MQLCampo($this->mCampos["telefono_residencia"]);}
	function telefono_movil($v = false){ if($v !== false){$this->mCampos["telefono_movil"]["V"] =  $v; } return new MQLCampo($this->mCampos["telefono_movil"]);}
	function fecha_nacimiento($v = false){ if($v !== false){$this->mCampos["fecha_nacimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_nacimiento"]);}
	function monto_relacionado($v = false){ if($v !== false){$this->mCampos["monto_relacionado"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_relacionado"]);}
	function porcentaje_relacionado($v = false){ if($v !== false){$this->mCampos["porcentaje_relacionado"]["V"] =  $v; } return new MQLCampo($this->mCampos["porcentaje_relacionado"]);}
	function fecha_alta($v = false){ if($v !== false){$this->mCampos["fecha_alta"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_alta"]);}
	function curp($v = false){ if($v !== false){$this->mCampos["curp"]["V"] =  $v; } return new MQLCampo($this->mCampos["curp"]);}
	function observaciones($v = false){ if($v !== false){$this->mCampos["observaciones"]["V"] =  $v; } return new MQLCampo($this->mCampos["observaciones"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function consanguinidad($v = false){ if($v !== false){$this->mCampos["consanguinidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["consanguinidad"]);}
	function estatus($v = false){ if($v !== false){$this->mCampos["estatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus"]);}
	function dependiente($v = false){ if($v !== false){$this->mCampos["dependiente"]["V"] =  $v; } return new MQLCampo($this->mCampos["dependiente"]);}
	function codigo($v = false){ if($v !== false){$this->mCampos["codigo"]["V"] =  $v; } return new MQLCampo($this->mCampos["codigo"]);}
	function ocupacion($v = false){ if($v !== false){$this->mCampos["ocupacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["ocupacion"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function eacp($v = false){ if($v !== false){$this->mCampos["eacp"]["V"] =  $v; } return new MQLCampo($this->mCampos["eacp"]);}
	function calificacion_del_referente($v = false){ if($v !== false){$this->mCampos["calificacion_del_referente"]["V"] =  $v; } return new MQLCampo($this->mCampos["calificacion_del_referente"]);}
	function dato_extra_1($v = false){ if($v !== false){$this->mCampos["dato_extra_1"]["V"] =  $v; } return new MQLCampo($this->mCampos["dato_extra_1"]);}
	function dato_extra_2($v = false){ if($v !== false){$this->mCampos["dato_extra_2"]["V"] =  $v; } return new MQLCampo($this->mCampos["dato_extra_2"]);}
	function dato_extra_3($v = false){ if($v !== false){$this->mCampos["dato_extra_3"]["V"] =  $v; } return new MQLCampo($this->mCampos["dato_extra_3"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
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
/*	ORM: Tabla:	socios_tipoingreso	-	Generado:	[14/2/2017 13:10]	*/
class cSocios_tipoingreso {
	private $mCampos	= array("idsocios_tipoingreso" => array("N"=>"idsocios_tipoingreso","T"=>"INT","V"=>"0","L"=>5),"descripcion_tipoingreso" => array("N"=>"descripcion_tipoingreso","T"=>"VARCHAR","V"=>"","L"=>45),"descripcion_detallada" => array("N"=>"descripcion_detallada","T"=>"VARCHAR","V"=>"","L"=>100),"parte_social" => array("N"=>"parte_social","T"=>"FLOAT","V"=>"0.00","L"=>25),"parte_permanente" => array("N"=>"parte_permanente","T"=>"FLOAT","V"=>"0.00","L"=>25),"estado" => array("N"=>"estado","T"=>"INT","V"=>"1","L"=>4),"nivel_de_riesgo" => array("N"=>"nivel_de_riesgo","T"=>"INT","V"=>"10","L"=>4),"tipo_de_persona" => array("N"=>"tipo_de_persona","T"=>"INT","V"=>"0","L"=>2),);
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
	function tipo_de_persona($v = false){ if($v !== false){$this->mCampos["tipo_de_persona"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_persona"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}
/*	ORM: Tabla:	socios_tiempo	-	Generado:	[20/2/2018 10:49]	*/
class cSocios_tiempo {
	private $mCampos	= array("idsocios_tiempo" => array("N"=>"idsocios_tiempo","T"=>"INT","V"=>"0","L"=>4),"descripcion_tiempo" => array("N"=>"descripcion_tiempo","T"=>"VARCHAR","V"=>"","L"=>45),"valor_arraigo_residencial" => array("N"=>"valor_arraigo_residencial","T"=>"FLOAT","V"=>"0.00","L"=>13),"valor_arraigo_economico" => array("N"=>"valor_arraigo_economico","T"=>"FLOAT","V"=>"0.00","L"=>13),"valor_experiencia_economica" => array("N"=>"valor_experiencia_economica","T"=>"FLOAT","V"=>"0.00","L"=>13),"valor_calificacion_por_referencia" => array("N"=>"valor_calificacion_por_referencia","T"=>"FLOAT","V"=>"0.00","L"=>13));
	public $IDSOCIOS_TIEMPO = "idsocios_tiempo"; public $DESCRIPCION_TIEMPO = "descripcion_tiempo"; public $VALOR_ARRAIGO_RESIDENCIAL = "valor_arraigo_residencial"; public $VALOR_ARRAIGO_ECONOMICO = "valor_arraigo_economico"; public $VALOR_EXPERIENCIA_ECONOMICA = "valor_experiencia_economica"; public $VALOR_CALIFICACION_POR_REFERENCIA = "valor_calificacion_por_referencia";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "socios_tiempo";}
	function getKey(){ return "idsocios_tiempo";}
	function idsocios_tiempo($v = false){ if($v !== false){$this->mCampos["idsocios_tiempo"]["V"] =  $v; } return new MQLCampo($this->mCampos["idsocios_tiempo"]);}
	function descripcion_tiempo($v = false){ if($v !== false){$this->mCampos["descripcion_tiempo"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_tiempo"]);}
	function valor_arraigo_residencial($v = false){ if($v !== false){$this->mCampos["valor_arraigo_residencial"]["V"] =  $v; } return new MQLCampo($this->mCampos["valor_arraigo_residencial"]);}
	function valor_arraigo_economico($v = false){ if($v !== false){$this->mCampos["valor_arraigo_economico"]["V"] =  $v; } return new MQLCampo($this->mCampos["valor_arraigo_economico"]);}
	function valor_experiencia_economica($v = false){ if($v !== false){$this->mCampos["valor_experiencia_economica"]["V"] =  $v; } return new MQLCampo($this->mCampos["valor_experiencia_economica"]);}
	function valor_calificacion_por_referencia($v = false){ if($v !== false){$this->mCampos["valor_calificacion_por_referencia"]["V"] =  $v; } return new MQLCampo($this->mCampos["valor_calificacion_por_referencia"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	socios_aeconomica_dependencias	-	Generado:	[16/1/2015 16:00]	*/
/*	ORM: Tabla:	socios_aeconomica_dependencias	-	Generado:	[03/11/2016 17:57]	*/
/*	ORM: Tabla:	socios_aeconomica_dependencias	-	Generado:	[01/12/2017 10:41]	*/
class cSocios_aeconomica_dependencias {
	private $mCampos	= array("idsocios_aeconomica_dependencias" => array("N"=>"idsocios_aeconomica_dependencias","T"=>"INT","V"=>"0","L"=>4),"descripcion_dependencia" => array("N"=>"descripcion_dependencia","T"=>"VARCHAR","V"=>"","L"=>100),"domicilio_completo" => array("N"=>"domicilio_completo","T"=>"VARCHAR","V"=>"","L"=>200),"directivo_principal" => array("N"=>"directivo_principal","T"=>"VARCHAR","V"=>"","L"=>100),"telefono" => array("N"=>"telefono","T"=>"VARCHAR","V"=>"","L"=>15),"fecha_preferente_de_pago" => array("N"=>"fecha_preferente_de_pago","T"=>"VARCHAR","V"=>"","L"=>20),"clave_de_persona" => array("N"=>"clave_de_persona","T"=>"BIGINT","V"=>"1","L"=>25),"clave_de_directivo" => array("N"=>"clave_de_directivo","T"=>"BIGINT","V"=>"1","L"=>25),"dias_de_avisos" => array("N"=>"dias_de_avisos","T"=>"VARCHAR","V"=>"","L"=>150),"periocidad_de_avisos" => array("N"=>"periocidad_de_avisos","T"=>"INT","V"=>"7","L"=>4),"ultimo_periodo_enviado" => array("N"=>"ultimo_periodo_enviado","T"=>"INT","V"=>"0","L"=>6),"fecha_de_envio" => array("N"=>"fecha_de_envio","T"=>"DATE","V"=>"0000-00-00","L"=>0),"oficial_que_cierra" => array("N"=>"oficial_que_cierra","T"=>"INT","V"=>"0","L"=>8),"nombre_corto" => array("N"=>"nombre_corto","T"=>"VARCHAR","V"=>"","L"=>20),"email_de_envio" => array("N"=>"email_de_envio","T"=>"VARCHAR","V"=>"","L"=>200),"producto_preferente" => array("N"=>"producto_preferente","T"=>"INT","V"=>"100","L"=>11),"formato_de_envio" => array("N"=>"formato_de_envio","T"=>"INT","V"=>"4001","L"=>6),"formato_de_relacion" => array("N"=>"formato_de_relacion","T"=>"INT","V"=>"4501","L"=>6),"dias_de_pago_nomina" => array("N"=>"dias_de_pago_nomina","T"=>"VARCHAR","V"=>"","L"=>150),"dias_de_liquidacion" => array("N"=>"dias_de_liquidacion","T"=>"VARCHAR","V"=>"","L"=>150),"comision_por_encargo" => array("N"=>"comision_por_encargo","T"=>"FLOAT","V"=>"0.000","L"=>13),"tasa_preferente" => array("N"=>"tasa_preferente","T"=>"FLOAT","V"=>"0.000","L"=>13),"estatus" => array("N"=>"estatus","T"=>"INT","V"=>"1","L"=>2));
	public $IDSOCIOS_AECONOMICA_DEPENDENCIAS	= "idsocios_aeconomica_dependencias";
	public $DESCRIPCION_DEPENDENCIA	= "descripcion_dependencia";
	public $DOMICILIO_COMPLETO	= "domicilio_completo";
	public $DIRECTIVO_PRINCIPAL	= "directivo_principal";
	public $TELEFONO	= "telefono";
	public $FECHA_PREFERENTE_DE_PAGO	= "fecha_preferente_de_pago";
	public $CLAVE_DE_PERSONA	= "clave_de_persona";
	public $CLAVE_DE_DIRECTIVO	= "clave_de_directivo";
	public $DIAS_DE_AVISOS	= "dias_de_avisos";
	public $PERIOCIDAD_DE_AVISOS	= "periocidad_de_avisos";
	public $ULTIMO_PERIODO_ENVIADO	= "ultimo_periodo_enviado";
	public $FECHA_DE_ENVIO	= "fecha_de_envio";
	public $OFICIAL_QUE_CIERRA	= "oficial_que_cierra";
	public $NOMBRE_CORTO	= "nombre_corto";
	public $EMAIL_DE_ENVIO	= "email_de_envio";
	public $PRODUCTO_PREFERENTE	= "producto_preferente";
	public $FORMATO_DE_ENVIO	= "formato_de_envio";
	public $FORMATO_DE_RELACION	= "formato_de_relacion";
	public $DIAS_DE_PAGO_NOMINA	= "dias_de_pago_nomina";
	public $DIAS_DE_LIQUIDACION	= "dias_de_liquidacion";
	public $COMISION_POR_ENCARGO	= "comision_por_encargo";
	public $TASA_PREFERENTE	= "tasa_preferente";
	public $ESTATUS	= "estatus";
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
	function comision_por_encargo($v = false){ if($v !== false){$this->mCampos["comision_por_encargo"]["V"] =  $v; } return new MQLCampo($this->mCampos["comision_por_encargo"]);}
	function tasa_preferente($v = false){ if($v !== false){$this->mCampos["tasa_preferente"]["V"] =  $v; } return new MQLCampo($this->mCampos["tasa_preferente"]);}
	function estatus($v = false){ if($v !== false){$this->mCampos["estatus"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus"]);}
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


/*	ORM: Tabla:	contable_centrodecostos	-	Generado:	[08/6/2015 17:10]	*/
class cContable_centrodecostos {
	private $mCampos	= array(
		"idcontable_centrodecostos" => array("N"=>"idcontable_centrodecostos","T"=>"INT","V"=>"","L"=>10),
		"nombre_centrodecostos" => array("N"=>"nombre_centrodecostos","T"=>"VARCHAR","V"=>"","L"=>60),
		"equivalente" => array("N"=>"equivalente","T"=>"VARCHAR","V"=>"00","L"=>10),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "contable_centrodecostos";}
	function getKey(){ return "idcontable_centrodecostos";}
	function idcontable_centrodecostos($v = false){ if($v !== false){$this->mCampos["idcontable_centrodecostos"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcontable_centrodecostos"]);}
	function nombre_centrodecostos($v = false){ if($v !== false){$this->mCampos["nombre_centrodecostos"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_centrodecostos"]);}
	function equivalente($v = false){ if($v !== false){$this->mCampos["equivalente"]["V"] =  $v; } return new MQLCampo($this->mCampos["equivalente"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}


/*	ORM: Tabla:	contable_polizas_perfil	-	Generado:	[30/10/2015 13:30]	*/
class cContable_polizas_perfil {
	private $mCampos	= array(
			"idcontable_poliza_perfil" => array("N"=>"idcontable_poliza_perfil","T"=>"INT","V"=>"","L"=>11),
			"tipo_de_recibo" => array("N"=>"tipo_de_recibo","T"=>"INT","V"=>"","L"=>4),
			"tipo_de_operacion" => array("N"=>"tipo_de_operacion","T"=>"INT","V"=>"","L"=>6),
			"descripcion" => array("N"=>"descripcion","T"=>"VARCHAR","V"=>"","L"=>90),
			"operacion" => array("N"=>"operacion","T"=>"INT","V"=>"0","L"=>11),
			"formula_posterior" => array("N"=>"formula_posterior","T"=>"TEXT","V"=>"","L"=>0),
			"cuenta_alternativa" => array("N"=>"cuenta_alternativa","T"=>"BIGINT","V"=>"0","L"=>25),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "contable_polizas_perfil";}
	function getKey(){ return "idcontable_poliza_perfil";}
	function idcontable_poliza_perfil($v = false){ if($v !== false){$this->mCampos["idcontable_poliza_perfil"]["V"] =  $v; } return new MQLCampo($this->mCampos["idcontable_poliza_perfil"]);}
	function tipo_de_recibo($v = false){ if($v !== false){$this->mCampos["tipo_de_recibo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_recibo"]);}
	function tipo_de_operacion($v = false){ if($v !== false){$this->mCampos["tipo_de_operacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_operacion"]);}
	function descripcion($v = false){ if($v !== false){$this->mCampos["descripcion"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion"]);}
	function operacion($v = false){ if($v !== false){$this->mCampos["operacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["operacion"]);}
	function formula_posterior($v = false){ if($v !== false){$this->mCampos["formula_posterior"]["V"] =  $v; } return new MQLCampo($this->mCampos["formula_posterior"]);}
	function cuenta_alternativa($v = false){ if($v !== false){$this->mCampos["cuenta_alternativa"]["V"] =  $v; } return new MQLCampo($this->mCampos["cuenta_alternativa"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}


/*	ORM: Tabla:	empresas_operaciones	-	Generado:	[09/7/2014 11:57]	*/
/*	ORM: Tabla:	empresas_operaciones	-	Generado:	[27/10/2017 12:51]	*/
/*	ORM: Tabla:	empresas_operaciones	-	Generado:	[14/4/2018 10:16]	*/
class cEmpresas_operaciones {
	private $mCampos	= array("idempresas_operaciones" => array("N"=>"idempresas_operaciones","T"=>"INT","V"=>"","L"=>11),"clave_de_empresa" => array("N"=>"clave_de_empresa","T"=>"INT","V"=>"99","L"=>11),"periodo_marcado" => array("N"=>"periodo_marcado","T"=>"INT","V"=>"0","L"=>11),"tipo_de_operacion" => array("N"=>"tipo_de_operacion","T"=>"INT","V"=>"1","L"=>11),"fecha_de_operacion" => array("N"=>"fecha_de_operacion","T"=>"DATE","V"=>"","L"=>0),"monto" => array("N"=>"monto","T"=>"FLOAT","V"=>"0.00","L"=>25),"oficial" => array("N"=>"oficial","T"=>"INT","V"=>"99","L"=>11),"periocidad" => array("N"=>"periocidad","T"=>"INT","V"=>"7","L"=>11),"observaciones" => array("N"=>"observaciones","T"=>"VARCHAR","V"=>"","L"=>150),"fecha_de_cobro" => array("N"=>"fecha_de_cobro","T"=>"DATE","V"=>"","L"=>0),"fecha_inicial" => array("N"=>"fecha_inicial","T"=>"DATE","V"=>"","L"=>0),"fecha_final" => array("N"=>"fecha_final","T"=>"DATE","V"=>"","L"=>0),"unid" => array("N"=>"unid","T"=>"VARCHAR","V"=>"","L"=>20));
	public $IDEMPRESAS_OPERACIONES = "idempresas_operaciones"; public $CLAVE_DE_EMPRESA = "clave_de_empresa"; public $PERIODO_MARCADO = "periodo_marcado"; public $TIPO_DE_OPERACION = "tipo_de_operacion"; public $FECHA_DE_OPERACION = "fecha_de_operacion"; public $MONTO = "monto"; public $OFICIAL = "oficial"; public $PERIOCIDAD = "periocidad"; public $OBSERVACIONES = "observaciones"; public $FECHA_DE_COBRO = "fecha_de_cobro"; public $FECHA_INICIAL = "fecha_inicial"; public $FECHA_FINAL = "fecha_final"; public $UNID = "unid";
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
	function unid($v = false){ if($v !== false){$this->mCampos["unid"]["V"] =  $v; } return new MQLCampo($this->mCampos["unid"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}

/*	ORM: Tabla:	empresas_cobranza	-	Generado:	[07/6/2016 11:45]	*/
class cEmpresas_cobranza {
	private $mCampos	= array(
			"idempresas_cobranza" => array("N"=>"idempresas_cobranza","T"=>"INT","V"=>"","L"=>11),
			"clave_de_nomina" => array("N"=>"clave_de_nomina","T"=>"INT","V"=>"","L"=>11),
			"clave_de_credito" => array("N"=>"clave_de_credito","T"=>"BIGINT","V"=>"","L"=>20),
			"parcialidad" => array("N"=>"parcialidad","T"=>"INT","V"=>"0","L"=>11),
			"monto_enviado" => array("N"=>"monto_enviado","T"=>"DOUBLE","V"=>"0.00","L"=>29),
			"observaciones" => array("N"=>"observaciones","T"=>"VARCHAR","V"=>"","L"=>100),
			"saldo_inicial" => array("N"=>"saldo_inicial","T"=>"DOUBLE","V"=>"0.00","L"=>29),
			"estado" => array("N"=>"estado","T"=>"INT","V"=>"1","L"=>2),
			"recibo" => array("N"=>"recibo","T"=>"BIGINT","V"=>"0","L"=>20),
			"tiempocobro" => array("N"=>"tiempocobro","T"=>"INT","V"=>"0","L"=>11),

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
	function recibo($v = false){ if($v !== false){$this->mCampos["recibo"]["V"] =  $v; } return new MQLCampo($this->mCampos["recibo"]);}
	function tiempocobro($v = false){ if($v !== false){$this->mCampos["tiempocobro"]["V"] =  $v; } return new MQLCampo($this->mCampos["tiempocobro"]);}
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


/*	ORM: Tabla:	seguimiento_compromisos	-	Generado:	[15/7/2015 14:46]	*/
class cSeguimiento_compromisos {
	private $mCampos	= array(
			"idseguimiento_compromisos" => array("N"=>"idseguimiento_compromisos","T"=>"INT","V"=>"","L"=>10),
			"socio_comprometido" => array("N"=>"socio_comprometido","T"=>"BIGINT","V"=>"0","L"=>20),
			"oficial_de_seguimiento" => array("N"=>"oficial_de_seguimiento","T"=>"INT","V"=>"","L"=>4),
			"fecha_vencimiento" => array("N"=>"fecha_vencimiento","T"=>"DATE","V"=>"","L"=>0),
			"hora_vencimiento" => array("N"=>"hora_vencimiento","T"=>"TIME","V"=>"","L"=>0),
			"tipo_compromiso" => array("N"=>"tipo_compromiso","T"=>"VARCHAR","V"=>"","L"=>40),
			"anotacion" => array("N"=>"anotacion","T"=>"TEXT","V"=>"","L"=>0),
			"credito_comprometido" => array("N"=>"credito_comprometido","T"=>"BIGINT","V"=>"0","L"=>20),
			"estatus_compromiso" => array("N"=>"estatus_compromiso","T"=>"VARCHAR","V"=>"pendiente","L"=>20),
			"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"matriz","L"=>20),
			"eacp" => array("N"=>"eacp","T"=>"VARCHAR","V"=>"EN_TRAMITE","L"=>20),
			"grupo_relacionado" => array("N"=>"grupo_relacionado","T"=>"BIGINT","V"=>"99","L"=>20),
			"lugar_de_compromiso" => array("N"=>"lugar_de_compromiso","T"=>"INT","V"=>"99","L"=>4),
			"monto_comprometido" => array("N"=>"monto_comprometido","T"=>"DOUBLE","V"=>"0.00","L"=>29),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "seguimiento_compromisos";}
	function getKey(){ return "idseguimiento_compromisos";}
	function idseguimiento_compromisos($v = false){ if($v !== false){$this->mCampos["idseguimiento_compromisos"]["V"] =  $v; } return new MQLCampo($this->mCampos["idseguimiento_compromisos"]);}
	function socio_comprometido($v = false){ if($v !== false){$this->mCampos["socio_comprometido"]["V"] =  $v; } return new MQLCampo($this->mCampos["socio_comprometido"]);}
	function oficial_de_seguimiento($v = false){ if($v !== false){$this->mCampos["oficial_de_seguimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["oficial_de_seguimiento"]);}
	function fecha_vencimiento($v = false){ if($v !== false){$this->mCampos["fecha_vencimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_vencimiento"]);}
	function hora_vencimiento($v = false){ if($v !== false){$this->mCampos["hora_vencimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["hora_vencimiento"]);}
	function tipo_compromiso($v = false){ if($v !== false){$this->mCampos["tipo_compromiso"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_compromiso"]);}
	function anotacion($v = false){ if($v !== false){$this->mCampos["anotacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["anotacion"]);}
	function credito_comprometido($v = false){ if($v !== false){$this->mCampos["credito_comprometido"]["V"] =  $v; } return new MQLCampo($this->mCampos["credito_comprometido"]);}
	function estatus_compromiso($v = false){ if($v !== false){$this->mCampos["estatus_compromiso"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus_compromiso"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function eacp($v = false){ if($v !== false){$this->mCampos["eacp"]["V"] =  $v; } return new MQLCampo($this->mCampos["eacp"]);}
	function grupo_relacionado($v = false){ if($v !== false){$this->mCampos["grupo_relacionado"]["V"] =  $v; } return new MQLCampo($this->mCampos["grupo_relacionado"]);}
	function lugar_de_compromiso($v = false){ if($v !== false){$this->mCampos["lugar_de_compromiso"]["V"] =  $v; } return new MQLCampo($this->mCampos["lugar_de_compromiso"]);}
	function monto_comprometido($v = false){ if($v !== false){$this->mCampos["monto_comprometido"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_comprometido"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}



/*	ORM: Tabla:	seguimiento_notificaciones	-	Generado:	[07/7/2015 12:45]	*/
/*	ORM: Tabla:	seguimiento_notificaciones	-	Generado:	[16/2/2018 10:51]	*/
class cSeguimiento_notificaciones {
	private $mCampos	= array("idseguimiento_notificaciones" => array("N"=>"idseguimiento_notificaciones","T"=>"INT","V"=>"","L"=>10),"socio_notificado" => array("N"=>"socio_notificado","T"=>"BIGINT","V"=>"1","L"=>20),"numero_solicitud" => array("N"=>"numero_solicitud","T"=>"BIGINT","V"=>"1","L"=>20),"numero_notificacion" => array("N"=>"numero_notificacion","T"=>"INT","V"=>"1","L"=>4),"fecha_notificacion" => array("N"=>"fecha_notificacion","T"=>"DATE","V"=>"0000-00-00","L"=>0),"oficial_de_seguimiento" => array("N"=>"oficial_de_seguimiento","T"=>"INT","V"=>"1","L"=>4),"capital" => array("N"=>"capital","T"=>"DOUBLE","V"=>"0.00","L"=>25),"interes" => array("N"=>"interes","T"=>"DOUBLE","V"=>"0.00","L"=>25),"moratorio" => array("N"=>"moratorio","T"=>"DOUBLE","V"=>"0.00","L"=>25),"otros_cargos" => array("N"=>"otros_cargos","T"=>"DOUBLE","V"=>"0.00","L"=>25),"impuestos" => array("N"=>"impuestos","T"=>"DOUBLE","V"=>"","L"=>25),"total" => array("N"=>"total","T"=>"FLOAT","V"=>"0.00","L"=>25),"observaciones" => array("N"=>"observaciones","T"=>"VARCHAR","V"=>"","L"=>100),"estatus_notificacion" => array("N"=>"estatus_notificacion","T"=>"VARCHAR","V"=>"pendiente","L"=>20),"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"matriz","L"=>20),"eacp" => array("N"=>"eacp","T"=>"VARCHAR","V"=>"EN_TRAMITE","L"=>20),"grupo_relacionado" => array("N"=>"grupo_relacionado","T"=>"BIGINT","V"=>"99","L"=>20),"hora" => array("N"=>"hora","T"=>"TIME","V"=>"","L"=>0),"usuario" => array("N"=>"usuario","T"=>"INT","V"=>"1","L"=>11),"canal_de_envio" => array("N"=>"canal_de_envio","T"=>"VARCHAR","V"=>"personal","L"=>20),"formato" => array("N"=>"formato","T"=>"INT","V"=>"0","L"=>11),"tiempo_entrega" => array("N"=>"tiempo_entrega","T"=>"INT","V"=>"0","L"=>11),"idresultado" => array("N"=>"idresultado","T"=>"INT","V"=>"","L"=>3),"nota_entrega" => array("N"=>"nota_entrega","T"=>"VARCHAR","V"=>"","L"=>150));
	public $IDSEGUIMIENTO_NOTIFICACIONES = "idseguimiento_notificaciones"; public $SOCIO_NOTIFICADO = "socio_notificado"; public $NUMERO_SOLICITUD = "numero_solicitud"; public $NUMERO_NOTIFICACION = "numero_notificacion"; public $FECHA_NOTIFICACION = "fecha_notificacion"; public $OFICIAL_DE_SEGUIMIENTO = "oficial_de_seguimiento"; public $CAPITAL = "capital"; public $INTERES = "interes"; public $MORATORIO = "moratorio"; public $OTROS_CARGOS = "otros_cargos"; public $IMPUESTOS = "impuestos"; public $TOTAL = "total"; public $OBSERVACIONES = "observaciones"; public $ESTATUS_NOTIFICACION = "estatus_notificacion"; public $SUCURSAL = "sucursal"; public $EACP = "eacp"; public $GRUPO_RELACIONADO = "grupo_relacionado"; public $HORA = "hora"; public $USUARIO = "usuario"; public $CANAL_DE_ENVIO = "canal_de_envio"; public $FORMATO = "formato"; public $TIEMPO_ENTREGA = "tiempo_entrega"; public $IDRESULTADO = "idresultado"; public $NOTA_ENTREGA = "nota_entrega";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "seguimiento_notificaciones";}
	function getKey(){ return "idseguimiento_notificaciones";}
	function idseguimiento_notificaciones($v = false){ if($v !== false){$this->mCampos["idseguimiento_notificaciones"]["V"] =  $v; } return new MQLCampo($this->mCampos["idseguimiento_notificaciones"]);}
	function socio_notificado($v = false){ if($v !== false){$this->mCampos["socio_notificado"]["V"] =  $v; } return new MQLCampo($this->mCampos["socio_notificado"]);}
	function numero_solicitud($v = false){ if($v !== false){$this->mCampos["numero_solicitud"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_solicitud"]);}
	function numero_notificacion($v = false){ if($v !== false){$this->mCampos["numero_notificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero_notificacion"]);}
	function fecha_notificacion($v = false){ if($v !== false){$this->mCampos["fecha_notificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_notificacion"]);}
	function oficial_de_seguimiento($v = false){ if($v !== false){$this->mCampos["oficial_de_seguimiento"]["V"] =  $v; } return new MQLCampo($this->mCampos["oficial_de_seguimiento"]);}
	function capital($v = false){ if($v !== false){$this->mCampos["capital"]["V"] =  $v; } return new MQLCampo($this->mCampos["capital"]);}
	function interes($v = false){ if($v !== false){$this->mCampos["interes"]["V"] =  $v; } return new MQLCampo($this->mCampos["interes"]);}
	function moratorio($v = false){ if($v !== false){$this->mCampos["moratorio"]["V"] =  $v; } return new MQLCampo($this->mCampos["moratorio"]);}
	function otros_cargos($v = false){ if($v !== false){$this->mCampos["otros_cargos"]["V"] =  $v; } return new MQLCampo($this->mCampos["otros_cargos"]);}
	function impuestos($v = false){ if($v !== false){$this->mCampos["impuestos"]["V"] =  $v; } return new MQLCampo($this->mCampos["impuestos"]);}
	function total($v = false){ if($v !== false){$this->mCampos["total"]["V"] =  $v; } return new MQLCampo($this->mCampos["total"]);}
	function observaciones($v = false){ if($v !== false){$this->mCampos["observaciones"]["V"] =  $v; } return new MQLCampo($this->mCampos["observaciones"]);}
	function estatus_notificacion($v = false){ if($v !== false){$this->mCampos["estatus_notificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatus_notificacion"]);}
	function sucursal($v = false){ if($v !== false){$this->mCampos["sucursal"]["V"] =  $v; } return new MQLCampo($this->mCampos["sucursal"]);}
	function eacp($v = false){ if($v !== false){$this->mCampos["eacp"]["V"] =  $v; } return new MQLCampo($this->mCampos["eacp"]);}
	function grupo_relacionado($v = false){ if($v !== false){$this->mCampos["grupo_relacionado"]["V"] =  $v; } return new MQLCampo($this->mCampos["grupo_relacionado"]);}
	function hora($v = false){ if($v !== false){$this->mCampos["hora"]["V"] =  $v; } return new MQLCampo($this->mCampos["hora"]);}
	function usuario($v = false){ if($v !== false){$this->mCampos["usuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["usuario"]);}
	function canal_de_envio($v = false){ if($v !== false){$this->mCampos["canal_de_envio"]["V"] =  $v; } return new MQLCampo($this->mCampos["canal_de_envio"]);}
	function formato($v = false){ if($v !== false){$this->mCampos["formato"]["V"] =  $v; } return new MQLCampo($this->mCampos["formato"]);}
	function tiempo_entrega($v = false){ if($v !== false){$this->mCampos["tiempo_entrega"]["V"] =  $v; } return new MQLCampo($this->mCampos["tiempo_entrega"]);}
	function idresultado($v = false){ if($v !== false){$this->mCampos["idresultado"]["V"] =  $v; } return new MQLCampo($this->mCampos["idresultado"]);}
	function nota_entrega($v = false){ if($v !== false){$this->mCampos["nota_entrega"]["V"] =  $v; } return new MQLCampo($this->mCampos["nota_entrega"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}

/*	ORM: Tabla:	seguimiento_lugar_de_compromiso	-	Generado:	[09/7/2015 13:58]	*/
class cSeguimiento_lugar_de_compromiso {
	private $mCampos	= array(
			"idseguimiento_lugar_de_compromiso" => array("N"=>"idseguimiento_lugar_de_compromiso","T"=>"INT","V"=>"","L"=>11),
			"descripcion_del_lugar" => array("N"=>"descripcion_del_lugar","T"=>"VARCHAR","V"=>"","L"=>45),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "seguimiento_lugar_de_compromiso";}
	function getKey(){ return "idseguimiento_lugar_de_compromiso";}
	function idseguimiento_lugar_de_compromiso($v = false){ if($v !== false){$this->mCampos["idseguimiento_lugar_de_compromiso"]["V"] =  $v; } return new MQLCampo($this->mCampos["idseguimiento_lugar_de_compromiso"]);}
	function descripcion_del_lugar($v = false){ if($v !== false){$this->mCampos["descripcion_del_lugar"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_del_lugar"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
}




/*	ORM: Tabla:	usuarios_web_notas	-	Generado:	[05/12/2014 12:04]	*/
/*	ORM: Tabla:	usuarios_web_notas	-	Generado:	[07/3/2018 16:37]	*/
class cUsuarios_web_notas {
	private $mCampos	= array("idusuarios_web_notas" => array("N"=>"idusuarios_web_notas","T"=>"INT","V"=>"","L"=>11),"tipo" => array("N"=>"tipo","T"=>"VARCHAR","V"=>"default","L"=>16),"oficial" => array("N"=>"oficial","T"=>"INT","V"=>"99","L"=>11),"oficial_de_origen" => array("N"=>"oficial_de_origen","T"=>"INT","V"=>"99","L"=>11),"socio" => array("N"=>"socio","T"=>"BIGINT","V"=>"1","L"=>25),"documento" => array("N"=>"documento","T"=>"BIGINT","V"=>"1","L"=>25),"fecha" => array("N"=>"fecha","T"=>"DATE","V"=>"2009-01-01","L"=>0),"texto" => array("N"=>"texto","T"=>"LONGTEXT","V"=>"","L"=>0),"estado" => array("N"=>"estado","T"=>"INT","V"=>"10","L"=>4),"relevancia" => array("N"=>"relevancia","T"=>"INT","V"=>"1","L"=>4),"tiempo" => array("N"=>"tiempo","T"=>"INT","V"=>"0","L"=>11));
	public $IDUSUARIOS_WEB_NOTAS = "idusuarios_web_notas"; public $TIPO = "tipo"; public $OFICIAL = "oficial"; public $OFICIAL_DE_ORIGEN = "oficial_de_origen"; public $SOCIO = "socio"; public $DOCUMENTO = "documento"; public $FECHA = "fecha"; public $TEXTO = "texto"; public $ESTADO = "estado"; public $RELEVANCIA = "relevancia"; public $TIEMPO = "tiempo";
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
	function tiempo($v = false){ if($v !== false){$this->mCampos["tiempo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tiempo"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}
/*	ORM: Tabla:	t_03f996214fba4a1d05a68b18fece8e71	-	Generado:	[14/1/2016 12:24]	*/
/*	ORM: Tabla:	t_03f996214fba4a1d05a68b18fece8e71	-	Generado:	[15/3/2017 16:20]	*/
/*	ORM: Tabla:	t_03f996214fba4a1d05a68b18fece8e71	-	Generado:	[29/12/2017 12:45]	*/
class cT_03f996214fba4a1d05a68b18fece8e71 {
	private $mCampos	= array("idusuarios" => array("N"=>"idusuarios","T"=>"INT","V"=>"","L"=>4),"f_28fb96d57b21090705cfdf8bc3445d2a" => array("N"=>"f_28fb96d57b21090705cfdf8bc3445d2a","T"=>"VARCHAR","V"=>"","L"=>62),"f_34023acbff254d34664f94c3e08d836e" => array("N"=>"f_34023acbff254d34664f94c3e08d836e","T"=>"VARCHAR","V"=>"","L"=>62),"nombres" => array("N"=>"nombres","T"=>"VARCHAR","V"=>"","L"=>40),"apellidopaterno" => array("N"=>"apellidopaterno","T"=>"VARCHAR","V"=>"","L"=>40),"apellidomaterno" => array("N"=>"apellidomaterno","T"=>"VARCHAR","V"=>"","L"=>40),"puesto" => array("N"=>"puesto","T"=>"VARCHAR","V"=>"NOTVALID","L"=>40),"f_f2cd801e90b78ef4dc673a4659c1482d" => array("N"=>"f_f2cd801e90b78ef4dc673a4659c1482d","T"=>"INT","V"=>"1","L"=>4),"periodo_responsable" => array("N"=>"periodo_responsable","T"=>"INT","V"=>"0","L"=>4),"estatus" => array("N"=>"estatus","T"=>"ENUM","V"=>"|baja|activo|suspension|","L"=>0),"sucursal" => array("N"=>"sucursal","T"=>"VARCHAR","V"=>"matriz","L"=>15),"usr_options" => array("N"=>"usr_options","T"=>"TEXT","V"=>"","L"=>0),"date_expire" => array("N"=>"date_expire","T"=>"DATE","V"=>"","L"=>0),"cuenta_contable_de_caja" => array("N"=>"cuenta_contable_de_caja","T"=>"VARCHAR","V"=>"CUENTA_DE_CUADRE","L"=>25),"codigo_de_persona" => array("N"=>"codigo_de_persona","T"=>"BIGINT","V"=>"1","L"=>20),"alias" => array("N"=>"alias","T"=>"VARCHAR","V"=>"","L"=>20),"corporativo" => array("N"=>"corporativo","T"=>"INT","V"=>"0","L"=>2),"pin_app" => array("N"=>"pin_app","T"=>"VARCHAR","V"=>"","L"=>10));
	public $IDUSUARIOS	= "idusuarios";
	public $F_28FB96D57B21090705CFDF8BC3445D2A	= "f_28fb96d57b21090705cfdf8bc3445d2a";
	public $F_34023ACBFF254D34664F94C3E08D836E	= "f_34023acbff254d34664f94c3e08d836e";
	public $NOMBRES	= "nombres";
	public $APELLIDOPATERNO	= "apellidopaterno";
	public $APELLIDOMATERNO	= "apellidomaterno";
	public $PUESTO	= "puesto";
	public $F_F2CD801E90B78EF4DC673A4659C1482D	= "f_f2cd801e90b78ef4dc673a4659c1482d";
	public $PERIODO_RESPONSABLE	= "periodo_responsable";
	public $ESTATUS	= "estatus";
	public $SUCURSAL	= "sucursal";
	public $USR_OPTIONS	= "usr_options";
	public $DATE_EXPIRE	= "date_expire";
	public $CUENTA_CONTABLE_DE_CAJA	= "cuenta_contable_de_caja";
	public $CODIGO_DE_PERSONA	= "codigo_de_persona";
	public $ALIAS	= "alias";
	public $CORPORATIVO	= "corporativo";
	public $PIN_APP	= "pin_app";
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
	function alias($v = false){ if($v !== false){$this->mCampos["alias"]["V"] =  $v; } return new MQLCampo($this->mCampos["alias"]);}
	function corporativo($v = false){ if($v !== false){$this->mCampos["corporativo"]["V"] =  $v; } return new MQLCampo($this->mCampos["corporativo"]);}
	function pin_app($v = false){ if($v !== false){$this->mCampos["pin_app"]["V"] =  $v; } return new MQLCampo($this->mCampos["pin_app"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}

/*	ORM: Tabla:	catalogos_localidades	-	Generado:	[07/5/2015 17:15]	*/
/*	ORM: Tabla:	catalogos_localidades	-	Generado:	[08/6/2018 10:38]	*/
class cCatalogos_localidades {
	private $mCampos	= array("clave_unica" => array("N"=>"clave_unica","T"=>"INT","V"=>"","L"=>11),"nombre_de_la_localidad" => array("N"=>"nombre_de_la_localidad","T"=>"VARCHAR","V"=>"","L"=>45),"clave_de_estado" => array("N"=>"clave_de_estado","T"=>"INT","V"=>"0","L"=>4),"clave_de_municipio" => array("N"=>"clave_de_municipio","T"=>"INT","V"=>"0","L"=>10),"clave_de_localidad" => array("N"=>"clave_de_localidad","T"=>"VARCHAR","V"=>"0","L"=>20),"longitud" => array("N"=>"longitud","T"=>"VARCHAR","V"=>"","L"=>45),"altitud" => array("N"=>"altitud","T"=>"VARCHAR","V"=>"","L"=>45),"latitud" => array("N"=>"latitud","T"=>"VARCHAR","V"=>"","L"=>45),"clave_de_pais" => array("N"=>"clave_de_pais","T"=>"VARCHAR","V"=>"MX","L"=>20));
	public $CLAVE_UNICA = "clave_unica"; public $NOMBRE_DE_LA_LOCALIDAD = "nombre_de_la_localidad"; public $CLAVE_DE_ESTADO = "clave_de_estado"; public $CLAVE_DE_MUNICIPIO = "clave_de_municipio"; public $CLAVE_DE_LOCALIDAD = "clave_de_localidad"; public $LONGITUD = "longitud"; public $ALTITUD = "altitud"; public $LATITUD = "latitud"; public $CLAVE_DE_PAIS = "clave_de_pais";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "catalogos_localidades";}
	function getKey(){ return "clave_unica";}
	
	function clave_unica($v = false){ if($v !== false){$this->mCampos["clave_unica"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_unica"]);}
	function nombre_de_la_localidad($v = false){ if($v !== false){$this->mCampos["nombre_de_la_localidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_de_la_localidad"]);}
	function clave_de_estado($v = false){ if($v !== false){$this->mCampos["clave_de_estado"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_estado"]);}
	function clave_de_municipio($v = false){ if($v !== false){$this->mCampos["clave_de_municipio"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_municipio"]);}
	function clave_de_localidad($v = false){ if($v !== false){$this->mCampos["clave_de_localidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_localidad"]);}
	function longitud($v = false){ if($v !== false){$this->mCampos["longitud"]["V"] =  $v; } return new MQLCampo($this->mCampos["longitud"]);}
	function altitud($v = false){ if($v !== false){$this->mCampos["altitud"]["V"] =  $v; } return new MQLCampo($this->mCampos["altitud"]);}
	function latitud($v = false){ if($v !== false){$this->mCampos["latitud"]["V"] =  $v; } return new MQLCampo($this->mCampos["latitud"]);}
	function clave_de_pais($v = false){ if($v !== false){$this->mCampos["clave_de_pais"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_de_pais"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
}

/*	ORM: Tabla:	catalogos_tipo_de_dispersion	-	Generado:	[03/4/2018 22:36]	*/
class cCatalogos_tipo_de_dispersion {
	private $mCampos	= array("tipo_de_dispersion" => array("N"=>"tipo_de_dispersion","T"=>"INT","V"=>"","L"=>11),"descripcion" => array("N"=>"descripcion","T"=>"VARCHAR","V"=>"","L"=>100),"requiere_extras" => array("N"=>"requiere_extras","T"=>"INT","V"=>"0","L"=>2));
	public $TIPO_DE_DISPERSION = "tipo_de_dispersion"; public $DESCRIPCION = "descripcion"; public $REQUIERE_EXTRAS = "requiere_extras";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "catalogos_tipo_de_dispersion";}
	function getKey(){ return "tipo_de_dispersion";}
	function tipo_de_dispersion($v = false){ if($v !== false){$this->mCampos["tipo_de_dispersion"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_dispersion"]);}
	function descripcion($v = false){ if($v !== false){$this->mCampos["descripcion"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion"]);}
	function requiere_extras($v = false){ if($v !== false){$this->mCampos["requiere_extras"]["V"] =  $v; } return new MQLCampo($this->mCampos["requiere_extras"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}

//---------------------------------------------------------------------- TEMPORAL
/*	ORM: Tabla:	tmp_colonias_activas	-	Generado:	[08/5/2015 12:49]	*/
/*	ORM: Tabla:	tmp_colonias_activas	-	Generado:	[23/11/2017 10:02]	*/
class cTmp_colonias_activas {
	private $mCampos	= array("codigo_postal" => array("N"=>"codigo_postal","T"=>"INT","V"=>"0","L"=>10),"nombre" => array("N"=>"nombre","T"=>"VARCHAR","V"=>"","L"=>100),"numero" => array("N"=>"numero","T"=>"BIGINT","V"=>"0","L"=>21),"codigo_de_estado" => array("N"=>"codigo_de_estado","T"=>"INT","V"=>"4","L"=>4),"codigo_de_municipio" => array("N"=>"codigo_de_municipio","T"=>"INT","V"=>"1","L"=>4),"nombre_municipio" => array("N"=>"nombre_municipio","T"=>"VARCHAR","V"=>"","L"=>100),"clave_alfanumerica" => array("N"=>"clave_alfanumerica","T"=>"VARCHAR","V"=>"CC","L"=>4),"nombre_estado" => array("N"=>"nombre_estado","T"=>"VARCHAR","V"=>"","L"=>60),"clave_en_sic" => array("N"=>"clave_en_sic","T"=>"VARCHAR","V"=>"","L"=>8),"idlocalidad" => array("N"=>"idlocalidad","T"=>"INT","V"=>"0","L"=>11),"nombre_localidad" => array("N"=>"nombre_localidad","T"=>"VARCHAR","V"=>"","L"=>100));
	public $CODIGO_POSTAL		= "codigo_postal";
	public $NOMBRE				= "nombre";
	public $NUMERO				= "numero";
	public $CODIGO_DE_ESTADO	= "codigo_de_estado";
	public $CODIGO_DE_MUNICIPIO	= "codigo_de_municipio";
	public $NOMBRE_MUNICIPIO	= "nombre_municipio";
	public $CLAVE_ALFANUMERICA	= "clave_alfanumerica";
	public $NOMBRE_ESTADO		= "nombre_estado";
	public $CLAVE_EN_SIC		= "clave_en_sic";
	public $IDLOCALIDAD			= "idlocalidad";
	public $NOMBRE_LOCALIDAD	= "nombre_localidad";
	
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "tmp_colonias_activas";}
	function getKey(){ return "codigo_postal";}
	function codigo_postal($v = false){ if($v !== false){$this->mCampos["codigo_postal"]["V"] =  $v; } return new MQLCampo($this->mCampos["codigo_postal"]);}
	function nombre($v = false){ if($v !== false){$this->mCampos["nombre"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre"]);}
	function numero($v = false){ if($v !== false){$this->mCampos["numero"]["V"] =  $v; } return new MQLCampo($this->mCampos["numero"]);}
	function codigo_de_estado($v = false){ if($v !== false){$this->mCampos["codigo_de_estado"]["V"] =  $v; } return new MQLCampo($this->mCampos["codigo_de_estado"]);}
	function codigo_de_municipio($v = false){ if($v !== false){$this->mCampos["codigo_de_municipio"]["V"] =  $v; } return new MQLCampo($this->mCampos["codigo_de_municipio"]);}
	function nombre_municipio($v = false){ if($v !== false){$this->mCampos["nombre_municipio"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_municipio"]);}
	function clave_alfanumerica($v = false){ if($v !== false){$this->mCampos["clave_alfanumerica"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_alfanumerica"]);}
	function nombre_estado($v = false){ if($v !== false){$this->mCampos["nombre_estado"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_estado"]);}
	function clave_en_sic($v = false){ if($v !== false){$this->mCampos["clave_en_sic"]["V"] =  $v; } return new MQLCampo($this->mCampos["clave_en_sic"]);}
	function idlocalidad($v = false){ if($v !== false){$this->mCampos["idlocalidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["idlocalidad"]);}
	function nombre_localidad($v = false){ if($v !== false){$this->mCampos["nombre_localidad"]["V"] =  $v; } return new MQLCampo($this->mCampos["nombre_localidad"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
}



/*	ORM: Tabla:	eacp_config_bases_de_integracion	-	Generado:	[10/8/2015 12:38]	*/
class cEacp_config_bases_de_integracion {
	private $mCampos	= array(
			"codigo_de_base" => array("N"=>"codigo_de_base","T"=>"INT","V"=>"","L"=>10),
			"descripcion" => array("N"=>"descripcion","T"=>"VARCHAR","V"=>"","L"=>100),
			"tipo_de_base" => array("N"=>"tipo_de_base","T"=>"VARCHAR","V"=>"","L"=>25),

	);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "eacp_config_bases_de_integracion";}
	function getKey(){ return "codigo_de_base";}
	function codigo_de_base($v = false){ if($v !== false){$this->mCampos["codigo_de_base"]["V"] =  $v; } return new MQLCampo($this->mCampos["codigo_de_base"]);}
	function descripcion($v = false){ if($v !== false){$this->mCampos["descripcion"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion"]);}
	function tipo_de_base($v = false){ if($v !== false){$this->mCampos["tipo_de_base"]["V"] =  $v; } return new MQLCampo($this->mCampos["tipo_de_base"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }

}



/*	ORM: Tabla:	eacp_config_bases_de_integracion_miembros	-	Generado:	[10/8/2015 12:37]	*/
/*	ORM: Tabla:	eacp_config_bases_de_integracion_miembros	-	Generado:	[24/10/2017 15:50]	*/
class cEacp_config_bases_de_integracion_miembros {
	private $mCampos	= array("ideacp_config_bases_de_integracion_miembros" => array("N"=>"ideacp_config_bases_de_integracion_miembros","T"=>"INT","V"=>"","L"=>10),"codigo_de_base" => array("N"=>"codigo_de_base","T"=>"INT","V"=>"","L"=>10),"miembro" => array("N"=>"miembro","T"=>"INT","V"=>"","L"=>5),"afectacion" => array("N"=>"afectacion","T"=>"FLOAT","V"=>"1.0000","L"=>25),"descripcion_de_la_relacion" => array("N"=>"descripcion_de_la_relacion","T"=>"VARCHAR","V"=>"","L"=>45),"subclasificacion" => array("N"=>"subclasificacion","T"=>"INT","V"=>"0","L"=>6),);
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "eacp_config_bases_de_integracion_miembros";}
	function getKey(){ return "ideacp_config_bases_de_integracion_miembros";}
	function ideacp_config_bases_de_integracion_miembros($v = false){ if($v !== false){$this->mCampos["ideacp_config_bases_de_integracion_miembros"]["V"] =  $v; } return new MQLCampo($this->mCampos["ideacp_config_bases_de_integracion_miembros"]);}
	function codigo_de_base($v = false){ if($v !== false){$this->mCampos["codigo_de_base"]["V"] =  $v; } return new MQLCampo($this->mCampos["codigo_de_base"]);}
	function miembro($v = false){ if($v !== false){$this->mCampos["miembro"]["V"] =  $v; } return new MQLCampo($this->mCampos["miembro"]);}
	function afectacion($v = false){ if($v !== false){$this->mCampos["afectacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["afectacion"]);}
	function descripcion_de_la_relacion($v = false){ if($v !== false){$this->mCampos["descripcion_de_la_relacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["descripcion_de_la_relacion"]);}
	function subclasificacion($v = false){ if($v !== false){$this->mCampos["subclasificacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["subclasificacion"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}

/*	ORM: Tabla:	originacion_grupos	-	Generado:	[21/4/2018 11:43]	*/
class cOriginacion_grupos {
	private $mCampos	= array("originacion_grupos_id" => array("N"=>"originacion_grupos_id","T"=>"INT","V"=>"","L"=>11),"grupo_id" => array("N"=>"grupo_id","T"=>"BIGINT","V"=>"0","L"=>20),"nivel_id" => array("N"=>"nivel_id","T"=>"INT","V"=>"1","L"=>4),"estatusactivo" => array("N"=>"estatusactivo","T"=>"INT","V"=>"1","L"=>2),"tiempo" => array("N"=>"tiempo","T"=>"INT","V"=>"0","L"=>11),"presidenta_id" => array("N"=>"presidenta_id","T"=>"BIGINT","V"=>"0","L"=>20),"fecha_solicitud" => array("N"=>"fecha_solicitud","T"=>"DATE","V"=>"","L"=>0),"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"0","L"=>8),"fecha_autorizacion" => array("N"=>"fecha_autorizacion","T"=>"DATE","V"=>"","L"=>0),"suma_solicitado" => array("N"=>"suma_solicitado","T"=>"DOUBLE","V"=>"0.00","L"=>37),"suma_autorizado" => array("N"=>"suma_autorizado","T"=>"DOUBLE","V"=>"0.00","L"=>37));
	public $ORIGINACION_GRUPOS_ID = "originacion_grupos_id"; public $GRUPO_ID = "grupo_id"; public $NIVEL_ID = "nivel_id"; public $ESTATUSACTIVO = "estatusactivo"; public $TIEMPO = "tiempo"; public $PRESIDENTA_ID = "presidenta_id"; public $FECHA_SOLICITUD = "fecha_solicitud"; public $IDUSUARIO = "idusuario"; public $FECHA_AUTORIZACION = "fecha_autorizacion"; public $SUMA_SOLICITADO = "suma_solicitado"; public $SUMA_AUTORIZADO = "suma_autorizado";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "originacion_grupos";}
	function getKey(){ return "originacion_grupos_id";}
	function originacion_grupos_id($v = false){ if($v !== false){$this->mCampos["originacion_grupos_id"]["V"] =  $v; } return new MQLCampo($this->mCampos["originacion_grupos_id"]);}
	function grupo_id($v = false){ if($v !== false){$this->mCampos["grupo_id"]["V"] =  $v; } return new MQLCampo($this->mCampos["grupo_id"]);}
	function nivel_id($v = false){ if($v !== false){$this->mCampos["nivel_id"]["V"] =  $v; } return new MQLCampo($this->mCampos["nivel_id"]);}
	function estatusactivo($v = false){ if($v !== false){$this->mCampos["estatusactivo"]["V"] =  $v; } return new MQLCampo($this->mCampos["estatusactivo"]);}
	function tiempo($v = false){ if($v !== false){$this->mCampos["tiempo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tiempo"]);}
	function presidenta_id($v = false){ if($v !== false){$this->mCampos["presidenta_id"]["V"] =  $v; } return new MQLCampo($this->mCampos["presidenta_id"]);}
	function fecha_solicitud($v = false){ if($v !== false){$this->mCampos["fecha_solicitud"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_solicitud"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function fecha_autorizacion($v = false){ if($v !== false){$this->mCampos["fecha_autorizacion"]["V"] =  $v; } return new MQLCampo($this->mCampos["fecha_autorizacion"]);}
	function suma_solicitado($v = false){ if($v !== false){$this->mCampos["suma_solicitado"]["V"] =  $v; } return new MQLCampo($this->mCampos["suma_solicitado"]);}
	function suma_autorizado($v = false){ if($v !== false){$this->mCampos["suma_autorizado"]["V"] =  $v; } return new MQLCampo($this->mCampos["suma_autorizado"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}


/*	ORM: Tabla:	grupos_solicitud	-	Generado:	[21/4/2018 11:45]	*/
class cGrupos_solicitud {
	private $mCampos	= array("grupos_solicitud_id" => array("N"=>"grupos_solicitud_id","T"=>"INT","V"=>"","L"=>11),"originacion_grupos_id" => array("N"=>"originacion_grupos_id","T"=>"INT","V"=>"0","L"=>11),"grupo_id" => array("N"=>"grupo_id","T"=>"BIGINT","V"=>"0","L"=>20),"persona_id" => array("N"=>"persona_id","T"=>"BIGINT","V"=>"1","L"=>20),"tiempo" => array("N"=>"tiempo","T"=>"INT","V"=>"0","L"=>11),"idusuario" => array("N"=>"idusuario","T"=>"INT","V"=>"0","L"=>8),"monto_solicitado" => array("N"=>"monto_solicitado","T"=>"DOUBLE","V"=>"0.00","L"=>33),"monto_autorizado" => array("N"=>"monto_autorizado","T"=>"DOUBLE","V"=>"0.00","L"=>33),"razon_rechazo_id" => array("N"=>"razon_rechazo_id","T"=>"INT","V"=>"1","L"=>4));
	public $GRUPOS_SOLICITUD_ID = "grupos_solicitud_id"; public $ORIGINACION_GRUPOS_ID = "originacion_grupos_id"; public $GRUPO_ID = "grupo_id"; public $PERSONA_ID = "persona_id"; public $TIEMPO = "tiempo"; public $IDUSUARIO = "idusuario"; public $MONTO_SOLICITADO = "monto_solicitado"; public $MONTO_AUTORIZADO = "monto_autorizado"; public $RAZON_RECHAZO_ID = "razon_rechazo_id";
	function __construct($campos = false){ if(is_array($campos)){ $this->mCampos = $campos; } }
	function get(){ return "grupos_solicitud";}
	function getKey(){ return "grupos_solicitud_id";}
	function grupos_solicitud_id($v = false){ if($v !== false){$this->mCampos["grupos_solicitud_id"]["V"] =  $v; } return new MQLCampo($this->mCampos["grupos_solicitud_id"]);}
	function originacion_grupos_id($v = false){ if($v !== false){$this->mCampos["originacion_grupos_id"]["V"] =  $v; } return new MQLCampo($this->mCampos["originacion_grupos_id"]);}
	function grupo_id($v = false){ if($v !== false){$this->mCampos["grupo_id"]["V"] =  $v; } return new MQLCampo($this->mCampos["grupo_id"]);}
	function persona_id($v = false){ if($v !== false){$this->mCampos["persona_id"]["V"] =  $v; } return new MQLCampo($this->mCampos["persona_id"]);}
	function tiempo($v = false){ if($v !== false){$this->mCampos["tiempo"]["V"] =  $v; } return new MQLCampo($this->mCampos["tiempo"]);}
	function idusuario($v = false){ if($v !== false){$this->mCampos["idusuario"]["V"] =  $v; } return new MQLCampo($this->mCampos["idusuario"]);}
	function monto_solicitado($v = false){ if($v !== false){$this->mCampos["monto_solicitado"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_solicitado"]);}
	function monto_autorizado($v = false){ if($v !== false){$this->mCampos["monto_autorizado"]["V"] =  $v; } return new MQLCampo($this->mCampos["monto_autorizado"]);}
	function razon_rechazo_id($v = false){ if($v !== false){$this->mCampos["razon_rechazo_id"]["V"] =  $v; } return new MQLCampo($this->mCampos["razon_rechazo_id"]);}
	function query(){ return new MQL($this->get(), $this->mCampos, $this->getKey());	}
	function setData($datos){ $mql	= new MQL($this->get(), $this->mCampos, $this->getKey()); $this->mCampos = $mql->setData($datos); }
	
}




?>