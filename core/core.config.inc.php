<?php
$OS 					= strtolower(substr(PHP_OS, 0, 3));
define("SAFE_OS", 	$OS);
$core_file_config		= "core.config.os.$OS.inc.php";
include_once($core_file_config);

if(isset($safe_sesion_en_segundos)){
	ini_set('session.gc_maxlifetime', $safe_sesion_en_segundos);
}
// server should keep session data for AT LEAST 1 hour
//$safe_sesion_en_segundos=3600;

@session_start();
//======================================= INFORMACION DEL PROGRAMA
$codename 								= "RaphRich"; //DevLeo Devian AzusaF-GTO Shuurei VernaF4 Enju Naru nanami IrinaJelavic MioIsurugi MillhioreF LouiseTheZero MioFurinji NagiSanzenin KanadeTachibana D.M.C. 
$version 								= "201903";
$revision 								= "04";

define("SAFE_VERSION",                  $version);
define("SAFE_REVISION",                 $revision);
define("SAFE_FIRM",                  	"SAFE-OSMS-$version.$revision $codename");
define("SAFE_CLEAN_LANG",                 false);

if(defined("SAFE_ON_DEV")){

} else {
	if(isset($EnDesarrollo)){
		define("SAFE_ON_DEV",                 $EnDesarrollo);
		
	} else {
		define("SAFE_ON_DEV",                 false);
	}
}
if(defined("SAFE_USE_MCACHE")){
	
} else {
	$usecc								= (isset($os_en_memcache)) ? $os_en_memcache : false;
	define("SAFE_USE_MCACHE",           $usecc);
}
//======================================= INCLUDE RUNTIME
@ini_set("include_path", $os_path_includes_str);
//======================================= HOST DE TRABAJO
define ("CURRENT_EACP", 1);
//define ("DEFAULT_SUCURSAL", $sucursal);
define ("DEFAULT_SUCURSAL", "matriz");

$url_host								= $V_cf1e8c14e54505f60aa10c;
define("WORK_HOST",                     $V_67e92c8765a9bc7fb2d335);
define("PORT_HOST",                     "3306");
//======================================= MODULOS DEL SISTEMA
define("MMOD_PERSONAS",				"personas");
define("MMOD_SISTEMA",				"sistema");
define("MMOD_CAPTACION",			"captacion");
define("MMOD_COLOCACION",			"colocacion");
define("MMOD_CONTABILIDAD",			"contabilidad");
define("MMOD_AML",					"aml");
define("MMOD_TESORERIA",			"tesoreria");
define("MMOD_BANCOS",				"bancos");
define("MMOD_OPERACIONES",			"operaciones");
define("MMOD_SEGUIMIENTO",			"seguimiento");

define("MMOD_CRED_NOMINA",			"credito.mod.nomina");
define("MMOD_CRED_LINEAS",			"credito.mod.lineas");
define("MMOD_CRED_GRUPOS",			"credito.mod.grupos");
define("MMOD_CRED_LEASING",			"credito.mod.leasing");

//======================================= INFORMACION DE LA CONEXION A LA BASE DE DATOS
define("MY_DB_IN",                      $db_de_trabajo);

define("USR_DB",                        $V_0a744893951e0d1706ff74);
define("PWD_DB",                        $V_9003d1df22eb4d38200150);
define("USR_ERROR",						$V_0a744893951e0d1706ff74);
define("PWD_ERROR",                     $V_9003d1df22eb4d38200150);
define("USR_PERMISSIONS",               $V_0a744893951e0d1706ff74);
define("PWD_PERMISSIONS",               $V_9003d1df22eb4d38200150);
define("USR_LOGIN",                     $V_0a744893951e0d1706ff74);
define("PWD_LOGIN",                     $V_9003d1df22eb4d38200150);
define("RPT_USR_DB",                    $V_0a744893951e0d1706ff74);
define("RPT_PWD_DB",                    $V_9003d1df22eb4d38200150);


//======================================= PATHS DE TRABAJO
define("PATH_HTDOCS",                   $os_path_htdocs);//PATH General
define("CTW_PATH",                      $os_path_ctw);//Path del ContPaq
define("PATH_BACKUPS",                  $os_path_bks);//Path de respaldos
define("PATH_TMP",                      $os_path_tmp);//Path Temporal
define("PATH_LOCAL_VENDOR",             "../libs");//Path Temporal
define("PATH_LOCAL_VIEWS",              "../views");//Path Temporal

define("vIMG_PATH", 					"../images");//Path de Imagenes a Usar en el sistema
//======================================= PATHS DE LOGS
define("vELOG_PHP",                     $os_path_php_log);
define("vELOG_MYSQL",                   $os_path_mysql_log);
define("vELOG_APACHE",                  $os_path_apache_log);

//===================================== INICIAR la configuracion
$xC     								= new cConfiguration();

/**
* @var integer		Ejercicio Fiscal Contable
*/
define("EJERCICIO_FISCAL", date("Y"));
/**
 * @var integer		Mes Fiscal Contable
 */
define("MES_FISCAL", date("m"));
		//Separador de Directorios
define("vLITERAL_SEPARATOR", DIRECTORY_SEPARATOR);
//Valores de Operaciones del Sistema
define("PERMITIR_EXTEMPORANEO", true);
//------------------------------- Llave Criptografica
define("MY_KEY",                        md5("$sucursal-$codename-$version-$revision"));
define("ROTTER_KEY",                    md5( MY_KEY . date("Y-m-d") )  );
//Define el Esquema de Informacion por Fichas
define("iDE_SOCIO", 			100);
define("iDE_GRUPO", 			101);
define("iDE_EMPRESA", 			102);

define("iDE_CREDITO", 			200);
//define("iDE_LEASING", 			281);
define("iDE_CVISTA", 			310);
define("iDE_CINVERSION", 		320);
define("iDE_CAPTACION", 		300);
define("iDE_AVAL", 				210);
define("iDE_RECIBO", 			400);

define("iDE_USUARIO", 			900);
define("iDE_BANCOS", 			700);

define("iDE_RECIBO_INGRESOS",	410);
define("iDE_RECIBO_EGRESOS",	420);

define("iDE_OPERACION", 		500);
define("iDE_GARANTIA", 			220);
define("iDE_PRESUPUESTO", 		280);
define("iDE_PRECLIENTES", 		270);
define("iDE_CPOLIZA", 			600);
define("iDE_CONTABLE", 			699);

define("iDE_AML", 				510);
define("iDE_SEGUIMIENTO", 		120);

		//Define la Division Estandar de Cadenas en una funcion EXPLODE
define("STD_LITERAL_DIVISOR", "@");
define("STD_LITERAL_SEC_DIV", "^");
define("STD_LITERAL_TER_DIV", ".-");
define("STD_NUMERICAL_DIV", ".");	//separador de centavos

		//define el Maximo de parametros en paramtros Javascript de remote script
define("STD_MAX_ARRAY_JS", 6);
if( !defined("GRID_SOURCE") ){
	define("GRID_SOURCE",                       "../libs/grid/");
}
define('TINYAJAX_PATH',                     './libs');
define("PATH_PHP_REPORTS_ENGINE",           $os_path_phpreports_engine);
define("PATH_FACTURAS",           PATH_HTDOCS . "/mfi/facts/");
define("PATH_XSD",           PATH_HTDOCS . "/mfi/xsd/");
define("PATH_CERTS",           PATH_HTDOCS . "/mfi/certs/");
		//Define un Mensaje en STRING para contestar algunas consultas
define("MSG_NO_PARAM_VALID",                "_NO_HAY_PARAMETROS_VALIDOS_");
define("MSG_NO_DATA",                		"MSG_NO_DATA");
define("MSG_ERROR_SAVE",					"MSG_ERROR_SAVE");
define("MSG_READY_SAVE",					"MSG_OK_SAVE");
define("MSG_CONFIRM_SAVE",					"MSG_CONFIRM_SAVE");
define("MSG_DATA_REQUIRED",					"MSG_DATA_REQUIRED");

define("MSG_UPDATE_PLAN_PAGOS",				"MSG_UPDATE_PLAN_PAGOS");
//======================================= CONFIGURACION CSS
		//Define el CCS General
define("CSS_GENERAL_FILE", 					"../css/general.css");
		//Define el CCS de reportes
define("CSS_REPORT_FILE", 					"../css/reporte.css");
define('SAFE_MEMORY_LIMIT',                     '1024M');

//======================================= DATOS DEL ADMIN
define("ADMIN_MAIL",                   	$xC->get("email_del_administrador", "", MMOD_SISTEMA) );
define("ADMIN_MAIL_SMTP_SERVER",       	$xC->get("servidor_smtp_para_notificaciones", "smtp.gmail.com", MMOD_SISTEMA) );
define("ADMIN_MAIL_SMTP_PORT",       	(int) $xC->get("puerto_smtp_para_notificaciones", "587", MMOD_SISTEMA) );
define("ADMIN_MAIL_SMTP_TLS",       	$xC->get("smtp_seguro_para_notificaciones", "tls", MMOD_SISTEMA) );
define("ADMIN_MAIL_SMTP_USR",       	$xC->get("user_smtp_para_notificaciones", ADMIN_MAIL, MMOD_SISTEMA) );
define("ADMIN_MAIL_STORAGE",       		(bool) $xC->get("email_almacenar_en_db", "false", MMOD_SISTEMA) );

define("SAFE_PUSH_APP_TOKEN",       	$xC->get("push_token_de_aplicacion", "ApnBfQBC5oN3ed3", MMOD_SISTEMA) );
define("SAFE_PUSH_APP_SRV",       		$xC->get("push_servidor_de_aplicacion", "https://messages.opensourcemicrofinance.org", MMOD_SISTEMA) );

define("NOMINA_MAIL",                  	$xC->get("email_de_nominas", "software@grupopadio.com.mx", MMOD_SISTEMA) );
define("EACP_MAIL",                    	$xC->get("email_de_la_entidad", "", MMOD_SISTEMA) );
define("ARCHIVO_MAIL",                  $xC->get("email_del_archivo", ADMIN_MAIL, MMOD_SISTEMA) );

define("TASK_USR",                     	$xC->get("usuario_de_trabajos_automaticos", "", MMOD_SISTEMA) );
define("TASK_IP",                     	$xC->get("ip_de_trabajos_automaticos", "170.178.197.201", MMOD_SISTEMA) );

define("USUARIOS_POR_CTX",              (bool)$xC->get("usuarios_login_por_ctx", "false", MMOD_SISTEMA) );

define("ADMIN_MAIL_PWD",                $xC->get("password_del_email_del_administrador", "", MMOD_SISTEMA) );

define("TASK_PWD",                     	$xC->get("contrasenna_de_trabajos_automaticos", "", MMOD_SISTEMA) );
define("SMS_PWD",                      	$xC->get("contrasenna_de_sms_automaticos", "", MMOD_SISTEMA) );
define("SMS_USR",                      	$xC->get("usuario_de_sms_automaticos", "", MMOD_SISTEMA) );

define("SERVER_PROXY_SMS",              $xC->get("servidor_proxy_sms", "", MMOD_SISTEMA) );


define("SAFE_DB_VERSION",              	$xC->get("safe_osms_database_version", "1", MMOD_SISTEMA) );
define("SYSTEM_ON_HOSTING",            	(bool) $xC->get("sistema_en_hosting", "false", MMOD_SISTEMA) );
define("SYSTEM_ON_DEMO",            	(bool) $xC->get("sistema_en_modo_demo", "false", MMOD_SISTEMA) );
define("SYSTEM_ON_LINE",               	(bool) $xC->get("el_sistema_esta_en_linea", "true", MMOD_SISTEMA) );

define("SVC_REMOTE_HOST",				$xC->get("url_de_servicios_remotos", "https://sdn.sipakal.com/", MMOD_SISTEMA) );
define("SVC_ASOCIADA_HOST",				$xC->get("url_de_entidad_transmisora", "https://sdn.sipakal.com/", MMOD_SISTEMA) );

define("SVC_HOST_CONSULTA_SDN",				$xC->get("url_de_consulta_sdn", "https://sdn.sipakal.com/", MMOD_AML) );
define("SVC_HOST_CONSULTA_PEP",				$xC->get("url_de_consulta_pep", "https://sdn.sipakal.com/", MMOD_AML) );

define("SVC_HOST_CONSULTA_GWS",				$xC->get("url_de_consulta_gws", "http://listaspep.com/", MMOD_AML) );
define("SVC_HOST_CONSULTA_WIKI",			$xC->get("url_de_consulta_wiki", "http://es.wikipedia.org/", MMOD_AML) );
define("SVC_USER_CONSULTA_WIKI",			$xC->get("usuario_de_consulta_wiki", "wikiusuario", MMOD_AML) );
define("SVC_PWD_CONSULTA_WIKI",				$xC->get("password_de_consulta_wiki", "pwdusuario", MMOD_AML) );

define("SVC_URL_COUCHDB",					$xC->get("svc_url_couchdb", "", MMOD_SISTEMA) );
define("SVC_DB_COUCHDB",					$xC->get("svc_db_couchdb", "safeosms", MMOD_SISTEMA) );
define("SVC_VIEW_COUCHDB",					$xC->get("svc_vista_couchdb", "tablanosync1", MMOD_SISTEMA) );

define("PERSONAS_COMPARTIR_CON_ASOCIADA",   (bool) $xC->get("compartir_datos_con_entidad_asocidad", "false", MMOD_PERSONAS));


define("AML_RMS_DB_NAME",				$xC->get("nombre_db_del_rms", "simplerisk", MMOD_AML) );
define("AML_RMS_DB_USR",				$xC->get("usuario_db_del_rms", "simplerisk", MMOD_AML) );
define("AML_RMS_DB_PWD",				$xC->get("password_db_del_rms", "simplerisk", MMOD_AML) );
define("AML_RMS_DB_SRV",				$xC->get("servidor_db_del_rms", "localhost", MMOD_AML) );
define("AML_RMS_URL",					$xC->get("url_servidor_rms", "http://localhost/tools/simplerisk/", MMOD_AML) );

//======================================= SETENV

define("AML_PERSONA_DIAS_VENCPF",		$xC->get("aml_dias_vence_perfil_transaccional", "180", MMOD_AML) );
define("AML_MIGRACION_SRV",				$xC->get("aml_migracion_server", "localhost", MMOD_AML) );
define("AML_MIGRACION_DB",				$xC->get("aml_migracion_database", "gpd1601_preprod", MMOD_AML) );
define("AML_MIGRACION_USR",				$xC->get("aml_migracion_user", "", MMOD_AML) );
define("AML_MIGRACION_PWD",				$xC->get("aml_migracion_pwd", "", MMOD_AML) );
//define("AML_PERSONA_DIAS_VENCPF",		$xC->get("aml_dias_vence_perfil_transaccional", "180", MMOD_AML) );

define("SAFE_LANG",						$xC->get("system_language", "en", MMOD_SISTEMA) );
define("SAFE_PAY_VERSION",				$xC->get("system_pay_email_register", "", MMOD_SISTEMA) );
//======================================= MERCADEO
define("MKT_MAIL",                   	$xC->get("email_de_mercadeo", "", MMOD_SISTEMA) );
define("MKT_PWD",                   	$xC->get("password_de_mercadeo", "", MMOD_SISTEMA) );
define("MKT_SERVER",                   	$xC->get("server_de_mercadeo", "", MMOD_SISTEMA) );
define("MKT_PORT",                   	$xC->get("puerto_de_mercadeo", "", MMOD_SISTEMA) );
define("MKT_SERVER_TLS",              	(bool) $xC->get("server_tls_de_mercadeo", "false", MMOD_SISTEMA) );

//======================================= RULES
define("CUR_SUCURSAL",                  $sucursal);

define("USE_OFICIAL_BY_PRODUCTO",       $xC->get("usar_oficial_por_producto"));
/**
 * @var boolean		Activar sesion de deuprado, para mostrar errores en las paginas, usado solo en el desarrollo
 */
if ( !isset($_SESSION["en_depurado"]) ){$_SESSION["en_depurado"]      		= false; }
define("MODO_DEBUG",                    $_SESSION["en_depurado"]);
define("MODO_CORRECION",                (bool) $xC->get("sistema_en_correcion", "true", MMOD_SISTEMA));
define("MODO_MIGRACION",                (bool) $xC->get("sistema_en_migracion", "false", MMOD_SISTEMA));
define("MODO_NOOB",                (bool) $xC->get("ayuda_interactiva", "true", MMOD_SISTEMA));

define("FORCE_UPDATES_ON_BOOT",         (bool) $xC->get("forzar_updates_on_boot", "false", MMOD_SISTEMA));
if ( !isset($_SESSION["log_nivel"]) ){ 	$_SESSION["log_nivel"]      		= 0; }
define("USR_NIVEL",                     $_SESSION["log_nivel"]);
//=============================================================================================================
//---------------------------------------- DATOS FIJOS
//=============================================================================================================
//define("FALLBACK_PERSONAS_ACTIVIDAD_ECONOMICA", 9501009);
define("FALLBACK_CLAVE_LOCALIDAD", 99999999);
define("FALLBACK_CLAVE_ENTIDADFED", 99);
define("FALLBACK_CLAVE_MUNICIPIO", 1);

define("FALLBACK_TIPO_DE_RECIBO", 99);
define("FALLBACK_TIPO_DE_OPERACION", 99);
define("FALLBACK_TIPO_DE_POLIZA", 999);
define("FALLBACK_TIPO_PAGO_CAJA", "ninguno");

define("FALLBACK_PERSONAS_ESTADO", 10);
define("FALLBACK_PERSONAS_REGION", 99);
define("FALLBACK_PERSONAS_FIGURA_JURIDICA",      1);
define("FALLBACK_PERSONAS_TIPO_IDENTIFICACION",      1);
define("FALLBACK_PERSONAS_TIPO_ING", 99);
define("FALLBACK_PERSONAS_DOMICILIO_ID_ESTADO", 98);
define("FALLBACK_PERSONAS_DOMICILIO_ID_MUNICIPIO", 1);
define("FALLBACK_PERSONAS_AE_TIPO_DISPERSION", 1);
define("FALLBACK_PERSONAS_TIPO_MEMBRESIA", 1);

define("FALLBACK_SECTOR_ECONOMICO",      1 );
define("FALLBACK_ACTIVIDAD_ECONOMICA",      9501009 );
define("FALLBACK_ACTIVIDAD_ECONOMICA_SCIAN",      812990);//Otros servicios personales
define("FALLBACK_DOMICILIO",      1 );

define("DEFAULT_CAUSA_DE_MORA",       	99 );

define("FALLBACK_CUENTA_BANCARIA",      99 );
define("FALLBACK_CLAVE_EMPRESA",      	99 );
define("FALLBACK_CLAVE_DE_PERSONA", 1);

define("FALLBACK_CRED_GARANTIAS_TVALUACION", 99);
define("FALLBACK_CRED_TIPO_PERIOCIDAD", 99);
define("FALLBACK_CRED_TIPO_DESTINO", 99);
define("FALLBACK_CREDITOS_DIAS_DE_PAGO",      99);
define("FALLBACK_CREDITOS_LUGAR_DE_PAGO",      1);
define("FALLBACK_CREDITOS_TIPO_DESEMBOLSO",      1);

define("FALLBACK_CREDITOS_TIPO_AUTORIZACION",      1);
define("FALLBACK_CREDITOS_TIPO_PAGO",      2);
define("FALLBACK_CREDITOS_RIESGO",      1);

define("FALLBACK_PERSONAS_REGIMEN_VIV",      99);
define("FALLBACK_PERSONAS_TIPO_VIV",      99);
define("FALLBACK_PERSONAS_CAJALOCAL",      1);


define("FALLBACK_CLAVE_DE_BANCO",      999);
define("FALLBACK_CLAVE_DE_GRUPO",      99);
define("FALLBACK_CLAVE_DE_RECIBO",      1);
define("FALLBACK_CLAVE_DE_CREDITO",      1);
define("FALLBACK_CLAVE_DE_DOCTO",      1);	//Docto o contrato
//---------------------------------------- CONSTANTES CREDITOS
define("CREDITO_AUTORIZACION_RENOVADO", 	3);
define("CREDITO_AUTORIZACION_REESTRUCTURADO",	4);

define("CREDITO_TIPO_CONSUMO",			3);
define("CREDITO_TIPO_COMERCIAL",		1);
define("CREDITO_TIPO_VIVIENDA",			2);
define("CREDITO_TIPO_NINGUNO",			99);
define("CREDITO_TIPO_PAGO_UNICO",		1);
define("CREDITO_TIPO_PAGO_PERIODICO",		2);
define("CREDITO_TIPO_PAGO_INTERES_PERIODICO",	3);
define("CREDITO_TIPO_PAGO_INTERES_COMERCIAL",	6);
define("CREDITO_TIPO_PAGO_CAPITAL_FIJO",	5);
define("CREDITO_TIPO_PAGO_FLAT_PARCIAL",	7);

define("CREDITO_ESTADO_VIGENTE",		10);
define("CREDITO_ESTADO_VENCIDO",		20);
define("CREDITO_ESTADO_MOROSO",			30);

define("CREDITO_ESTADO_CASTIGADO",		50);
define("CREDITO_ESTADO_AUTORIZADO",		98);
define("CREDITO_ESTADO_SOLICITADO",		99);

define("CREDITO_TIPO_AUTORIZACION_NORMAL",	1);
define("CREDITO_TIPO_AUTORIZACION_AUTOMATICA",	2);
define("CREDITO_TIPO_AUTORIZACION_RENOVACION",	3);
define("CREDITO_TIPO_AUTORIZACION_REESTRUCTURA",	4);


define("CREDITO_GARANTIA_ESTADO_PRESENTADO",	1);
define("CREDITO_GARANTIA_ESTADO_RESGUARDADO",	2);
define("CREDITO_GARANTIA_ESTADO_ENTREGADO",	3);


define("CREDITO_GARANTIA_TIPO_INMOBILIARIA",	1);
define("CREDITO_GARANTIA_TIPO_PRENDARIA",		2);

define("CREDITO_GARANTIA_TIPO_VAL_DOCTO",		1);
define("CREDITO_GARANTIA_TIPO_VAL_LEGAL",		2);
define("CREDITO_GARANTIA_TIPO_VAL_EST",		3);	//Estimada


define("CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO",360);
define("INTERES_POR_SALDO_HISTORICO", 		1);//clave del tipo de pago por saldo historico
define("INTERES_POR_SALDO_INSOLUTO", 		2);//clave del tipo de pago por saldo insoluto
//================================================================================================
define("GRUPO_CLAVE_INTEGRANTE",		96);
define("GRUPO_CLAVE_PRESIDENTA",		97);
define("GRUPO_CLAVE_VOCAL",			98);

define("FECHA_TIPO_OPERATIVA",			"OPERATIVO");
define("FECHA_TIPO_NACIMIENTO",			"NACIMIENTO");
define("TIPO_FECHA_OPERATIVA", 			"OPERATIVO");
define("TIPO_FECHA_NACIMIENTO", 		"NACIMIENTO");

define("TIPO_INGRESO_RELACION",         	500);
define("TIPO_INGRESO_USUARIO",         		800);

define("TIPO_INGRESO_PEP",         		501);
define("TIPO_INGRESO_SDN",         		510);


define("TIPO_INGRESO_GRUPO",					300);
define("TIPO_INGRESO_CLIENTE",					200);
//define("TIPO_PERSONA_GRUPO",					10);

define("ESTADO_CIVIL_CASADO",			1);
define("DEFAULT_EMPRESA",			99);
define("DEFAULT_TIPO_RELACION",			99);
define("DEFAULT_ESTADO_CIVIL",			99);
define("DEFAULT_REGIMEN_CONYUGAL",		"NINGUNO");
define("DEFAULT_REGIMEN_FISCAL",		1);
define("DEFAULT_TIPO_IDENTIFICACION",		99);
define("DEFAULT_TIPO_CONSANGUINIDAD",		99);
define("DEFAULT_TIEMPO",			99);
define("DEFAULT_TIPO_DOMICILIO",		99);
define("DEFAULT_GENERO",			99);
define("DEFAULT_CAPTACION_ORIGEN",			99);
define("DEFAULT_PERMISOS", "2@rw,3@rw,4@rw,5@rw,6@rw,7@rw,8@rw,9@rw,10@rw,11@rw,12@rw,13@rw,14@rw,15@rw,31@rw,41@rw,71@rw,72@rw,73@rw,81@rw,99@rw");

define("TIPO_RELACION_CONYUGE",			3);
define("TIPO_RELACION_BENEFICIARIO",		11);
define("TIPO_CONSANGUINIDAD_CONYUGE",		3);
define("TIPO_DOMICILIO_PRINCIPAL",		1);
define("TIPO_DOMICILIO_ORDINARIO",		0);
define("TIPO_VIVIENDA_PROPIA",			1);	//deprecated 20141100
define("PERSONAS_REG_VIV_PROPIA",		1);
define("PERSONAS_REG_VIV_NINGUNO",		99);
define("PERSONAS_TIPO_DOM_FISCAL",		2);
define("PERSONAS_TIPO_DOM_LABORAL",		3);

define("PERSONAS_TIPO_ACCESO_CALLE",	"calle");

define("TIPO_DOMICILIO_PARTICULAR",		1);
define("TIPO_JURIDICO_FISICA",			1);
define("PERIODO_CERO",			0);

define("CREDITO_TIPO_PERIOCIDAD_DIARIO",		1);
define("CREDITO_TIPO_PERIOCIDAD_SEMANAL",		7);
define("CREDITO_TIPO_PERIOCIDAD_QUINCENAL",		15);
define("CREDITO_TIPO_PERIOCIDAD_DECENAL",		10);
define("CREDITO_TIPO_PERIOCIDAD_CATORCENAL",		14);
define("CREDITO_TIPO_PERIOCIDAD_MENSUAL",		30);
define("CREDITO_TIPO_PERIOCIDAD_BIMESTRAL",		60);
define("CREDITO_TIPO_PERIOCIDAD_TRIMESTRAL",		90);
define("CREDITO_TIPO_PERIOCIDAD_ANUAL",		365);

define("CREDITO_TIPO_DIAS_DE_PAGO_PREDETERMINADOS",	1);
define("CREDITO_TIPO_DIAS_DE_PAGO_PERSONALIZADOS",	2);
define("CREDITO_TIPO_DIAS_DE_PAGO_NATURAL",	99);




define("SYS_PRODUCTO_INDIVIDUAL",		200);
define("SYS_PRODUCTO_REVOLVENTES",		300);

define("SYS_PRODUCTO_NOMINA",			100);
define("SYS_PRODUCTO_GRUPOS", 			900);
define("SYS_PRODUCTO_ARREND", 			500);


define("SYS_PRODUCTO_FUERA_NOMINA",		201);	//Deprecated
define("SYS_PRODUCTO_DESCARTADOS",		203);	//Deprecated


define("SYS_FACTOR_DIAS_MES", 			30.4166666666666);

define("CREDITO_PRODUCTO_INDIVIDUAL",			$xC->get("creditos_clave_producto_individual", SYS_PRODUCTO_INDIVIDUAL, MMOD_COLOCACION));
define("CREDITO_PRODUCTO_REVOLVENTES",			$xC->get("creditos_clave_producto_revolventes", SYS_PRODUCTO_REVOLVENTES, MMOD_COLOCACION));
define("CREDITO_PRODUCTO_NOMINA",				$xC->get("creditos_clave_producto_nomina", SYS_PRODUCTO_NOMINA, MMOD_COLOCACION));
define("CREDITO_PRODUCTO_FUERA_NOMINA",			$xC->get("creditos_clave_producto_fuera_nomina", SYS_PRODUCTO_FUERA_NOMINA, MMOD_COLOCACION));

define("CREDITO_PRODUCTO_FUERA_NOMINA_N",		$xC->get("creditos_nombre_producto_fuera_nomina", "DESPEDIDO", MMOD_COLOCACION));

define("CREDITO_PRODUCTO_DESTINO_DESCARTADOS",	$xC->get("creditos_clave_producto_descartados", SYS_PRODUCTO_DESCARTADOS, MMOD_COLOCACION));
define("CREDITO_PRODUCTO_LEASING_PURO",			$xC->get("creditos_clave_producto_arrendamiento_puro", "500", $xC->COLOCACION));
define("CREDITO_DESTINO_LEASING_PURO",			$xC->get("creditos_clave_destino_arrendamiento_puro", "7100", $xC->COLOCACION));

define("CREDITO_LEASING_DIAS_VIG_COT",			$xC->get("creditos_leasing_dias_cotizacion", "10", $xC->COLOCACION));
define("CREDITO_LEASING_LIMITE_DED",			$xC->get("creditos_leasing_limite_deducible", "6000", $xC->COLOCACION));


define("CREDITO_PRODUCTO_GRUPOS", 			$xC->get("creditos_clave_producto_grupos", SYS_PRODUCTO_GRUPOS, MMOD_COLOCACION));
define("CREDITO_DEFAULT_DESTINO",			$xC->get("creditos_clave_producto_por_defecto", SYS_PRODUCTO_NOMINA, MMOD_COLOCACION));
//define("CREDITO_REGLA_FINAL_PLAZO_ACTIVO",	(bool) $xC->get("creditos_final_de_plazo_activo", "true", MMOD_COLOCACION));

define("PLAN_DE_PAGOS_SIN_REDONDEO",		(bool) $xC->get("planes_de_pago_sin_redondeo", "false", MMOD_COLOCACION));
define("PLAN_DE_PAGOS_PLANO",				(bool) $xC->get("planes_de_pago_planos", "true", MMOD_COLOCACION));
define("PLAN_DE_PAGOS_IDFORMA",				(int) $xC->get("planes_de_pago_clave_de_forma", "0", MMOD_COLOCACION));

/* Variable de modulos */
define("MODULO_CAPTACION_ACTIVADO",			(bool) $xC->get("modulo_de_captacion_activado", "true", MMOD_SISTEMA));
define("MODULO_CONTABILIDAD_ACTIVADO",		(bool) $xC->get("modulo_de_contabilidad_activado", "true", MMOD_SISTEMA));
define("MODULO_CAJA_ACTIVADO",				(bool) $xC->get("modulo_de_caja_activado", "true", MMOD_SISTEMA));
define("MODULO_SEGUIMIENTO_ACTIVADO",		(bool) $xC->get("modulo_de_seguimiento_activado", "true", MMOD_SISTEMA));
define("MODULO_AML_ACTIVADO",				(bool) $xC->get("modulo_de_aml_activado", "true", MMOD_SISTEMA));
define("MODULO_LEASING_ACTIVADO",			(bool) $xC->get("modulo_de_leasing_activado", "true", MMOD_SISTEMA));
define("MULTISUCURSAL",						(bool) $xC->get("modulo_de_multisucursal_activado", "true", MMOD_SISTEMA));
define("OPERACION_LIBERAR_ACCIONES",		(bool) $xC->get("liberar_modulos_de_informacion", "true", MMOD_SISTEMA));
define("OPERACION_LIBERAR_SUCURSALES",		(bool) $xC->get("liberar_informacion_de_sucursales", "true", MMOD_SISTEMA));


define("SYS_TEXTO_TODAS",					$xC->get("texto_para_palabra_todas", "TODAS", MMOD_SISTEMA));
define("CSS_TFUENTE_CONTRATOS",				$xC->get("css_tamano_fuente_en_contratos", "10", MMOD_SISTEMA) );
define("CSS_BCONFIG_CONTRATOS",				$xC->get("css_css_body_en_contratos", "margin:0; height: 100%; padding: 0;", MMOD_SISTEMA) );
define("CSS_TFUENTE_RECIBOS",				$xC->get("css_tamano_fuente_en_recibos", "8", MMOD_SISTEMA) );

define("CSS_FORM_BACKGROUND",				$xC->get("css_fondo_en_formas", "background: #63bad8 url(../images/bg.jpg) 50% 0px repeat-x;", MMOD_SISTEMA) );

define("FACTURACION_NUM_CERT",				$xC->get("facturacion.numero_certificado", "20001000000300022762", MMOD_OPERACIONES));

define("FACTURACION_ARCHIVO_CERT",			$xC->get("facturacion.archivo_cert", "20001000000200000192.cer", MMOD_OPERACIONES));
define("FACTURACION_ARCHIVO_PEM",			$xC->get("facturacion.archivo_pem", "20001000000200000192.key.pem", MMOD_OPERACIONES));
define("FACTURACION_USUARIO_NOMBRE",		$xC->get("facturacion.nombre_de_usuario", "UsuarioPruebasWS", MMOD_OPERACIONES));
define("FACTURACION_USUARIO_CLAVE",			$xC->get("facturacion.clave_de_usuario", "b9ec2afa3361a59af4b4d102d3f704eabdf097d4", MMOD_OPERACIONES));
define("FACTURACION_URL_SERVICIO",			$xC->get("facturacion.url_de_servicio", "https://t1demo.facturacionmoderna.com/timbrado/wsdl", MMOD_OPERACIONES));
define("FACTURACION_MAIL_ARCHIVO",			$xC->get("facturacion.email_de_almacenamiento", "tasks@opencorebanking.com", MMOD_OPERACIONES));

define("RECIBOS_POR_HOJA",					$xC->get("recibos.items_por_hoja", "1", MMOD_OPERACIONES));


define("OPERACION_CLAVE_CARGOS_COBRANZA",		601);
define("OPERACION_CLAVE_CARGOS_VARIOS",			600);
define("OPERACION_CLAVE_BON_VARIAS",			803);
define("OPERACION_CLAVE_PLAN_CAPITAL",		410);
define("OPERACION_CLAVE_PLAN_INTERES",		411);
define("OPERACION_CLAVE_PLAN_IVA",			413);
define("OPERACION_CLAVE_PLAN_DESGLOSE",		414);

define("OPERACION_CLAVE_PLAN_AHORRO",		412);
define("OPERACION_CLAVE_MINISTRACION",		110);
define("OPERACION_CLAVE_REESTRUCTURA",		113);

define("OPERACION_CLAVE_PAGO_CAPITAL",		120);
define("OPERACION_CLAVE_PAGO_INTERES",		140);
define("OPERACION_CLAVE_PAGO_MORA",			141);
define("OPERACION_CLAVE_PAGO_CBZA",			145);
define("OPERACION_CLAVE_PAGO_NOT",			177);

define("OPERACION_CLAVE_PAGO_GLIQ",			901);
define("OPERACION_CLAVE_DEV_GLIQ",			353);

define("OPERACION_CLAVE_DEP_INT",			222);
define("OPERACION_CLAVE_DISPOCISION",		117);

define("OPERACION_CLAVE_PAGO_IVA_OTROS",		152);
define("OPERACION_CLAVE_PAGO_IVA_INTS",		151);
define("OPERACION_CLAVE_PAGO_CAPTACION",		220);

define("OPERACION_CLAVE_APORT_CORRIENTE",		701);
define("OPERACION_CLAVE_APORT_VOLUNTARIA",		702);
define("OPERACION_CLAVE_APORT_FONDO",		710);
define("OPERACION_CLAVE_ANUALIDAD_C",		160); //Anualidad de Credito

define("OPERACION_CLAVE_DESVINCULACION",	2101);
define("OPERACION_CLAVE_VINCULACION",		2102);

define("OPERACION_CLAVE_MULTAS",		1002);

define("OPERACION_CLAVE_BANCOS",	9200);
define("OPERACION_CLAVE_EFECTIVO",		9100);

define("OPERACION_CLAVE_COMISION_APERTURA",	147);
define("OPERACION_CLAVE_PAGO_COM_VARIAS",	246);

define("OPERACION_CLAVE_PAGO_SEGURO_V",		157);
define("OPERACION_CLAVE_PAGO_MTTO_V",		172);
define("OPERACION_CLAVE_PAGO_TENEN_V",		171);
define("OPERACION_CLAVE_PAGO_PLACAS",		180);
define("OPERACION_CLAVE_PAGO_PENAS",		148);

define("OPERACION_CLAVE_PAGO_ACC_V",		173);
define("OPERACION_CLAVE_PAGO_GTIAE_V",		174);

define("OPERACION_CLAVE_PAGO_IVA_ARR",		176);

//define("OPERACION_CLAVE_COMISION_CBZA",	145);

define("OPERACION_CLAVE_FIN_DE_MES",	999);

define("RECIBOS_TIPO_ESTADISTICO",		10);
define("RECIBOS_TIPO_PAGO_CREDITO",		2);
define("RECIBOS_TIPO_MINISTRACION",		1);
define("RECIBOS_TIPO_DISPOCISION",		102);
define("RECIBOS_TIPO_PLAN_DE_PAGO",		11);
define("RECIBOS_TIPO_PAGO_APORTACIONES",5);
define("RECIBOS_TIPO_DEPOSITO_VISTA",	3);
define("RECIBOS_TIPO_RETIRO_VISTA",		4);
define("RECIBOS_TIPO_TERCEROS",			20);
define("RECIBOS_TIPO_OINGRESOS",		99);
define("RECIBOS_TIPO_OEGRESOS",			98);
define("RECIBOS_TIPO_PRIMERPAG",		301);
define("RECIBOS_TIPO_DEVCOLOC",			16);	//Devolucione de Colocacion

define("RECIBOS_TIPO_PAGOSPENDS",		22);

define("RECIBOS_TIPO_CARGOSPENDS",		97);

define("RECIBOS_TIPO_BONIFPENDS",		96);
define("RECIBOS_TIPO_CIERRE",			12);
define("RECIBOS_TIPO_PAGOCARGOS",		22);
define("RECIBOS_TIPO_TRASPCTAS",		9);

define("RECIBOS_ORIGEN_COLOCACION",		"colocacion");
define("RECIBOS_ORIGEN_CAPTACION",		"captacion");
define("RECIBOS_ORIGEN_MIXTO",		"mixto");

define("OPERACION_ESTADO_APLICADO",		30);
define("OPERACION_ESTADO_GENERADO",		40);
define("OPERACION_RECIBOS_COBRANZA_LIM",	20);
define("OPERACION_GENERAR_MORA_POR_LETRAS",	true); //genera la mora

define("OPERACION_MONEDA_NOMBRE",			$xC->get("nombre_de_la_moneda_local", "PESOS", MMOD_OPERACIONES)); 
define("OPERACION_MONEDA_SIMBOLO",			$xC->get("simbolo_de_la_moneda_local", "$", MMOD_OPERACIONES));
define("OPERACION_MONEDA_TERMINO",			$xC->get("terminacion_de_numeros_en_letras", "M.N.", MMOD_OPERACIONES));
define("OPERACION_CUADRAR_CON_COBRANZA",	(bool) $xC->get("cuadrar_cobranza_en_pagos", "true", MMOD_OPERACIONES));
define("OPERACION_IGNORAR_IVA",				(bool) $xC->get("ignorar_impuesto_al_consumo", "false", MMOD_OPERACIONES));


define("CONEKTA_API_KEY",			$xC->get("conekta_api_key", "key_Mc1Cs4WcPvKQrMg772YcyA", MMOD_OPERACIONES));

/* Operaciones: Bases Operativas */
define("BASE_IVA_INTERESES",					7012);
define("BASE_IVA_OTROS",						7013);
define("BASE_DOCTOS_PERSONAS_FISICAS",		20001);
define("BASE_DOCTOS_PERSONAS_MORALES",		20002);
define("BASE_ES_PERSONA_MORAL",				20100);
/* Operaciones: Tipos de Pago */
define("OPERACION_PAGO_LETRA_VARIABLE",		"pli");
define("OPERACION_PAGO_LETRA_COMPLETA",		"plc");
define("OPERACION_PAGO_COMPLETO",		"pc");

/* Sistema: Nombres comunes */
define("SYS_INTERES_MORATORIO",			"moratorio");
define("SYS_INTERES_NORMAL",			"normal");
define("SYS_CAPITAL",				"capital");
define("SYS_GASTOS_DE_COBRANZA",		"cobranza");
define("SYS_PENAS",						"penas");
define("SYS_VARIOS",				"otros");
define("SYS_IMPUESTOS",				"impuestos");
define("SYS_TOTAL",				"total");
define("SYS_AHORRO",				"ahorro");
define("SYS_FECHA",				"fecha.mark");
define("SYS_FECHA_VENCIMIENTO",			"fecha_de_vencimiento");

define("SYS_UNO",		1);
define("SYS_DOS",		2);
define("SYS_CERO",		0);

define("SYS_CLIENT_MOB",		"sys.client.type");
define("SYS_MONTO",		"monto");
define("SYS_NUMERO",		"numero");
define("SYS_MONEDA",		"moneda");
define("SYS_NEGATIVO",		"-");

define("SYS_SALDO",		"sys.saldo");

define("SYS_INFO",		"info");
define("SYS_DATOS",		"datos");
define("SYS_ERROR",		"error");
define("SYS_SQL",			"sys.sql");
define("SYS_UUID",		"sys.uuid");

define("SYS_MSG",		"msg");
define("SYS_NINGUNO",		"ninguno");
define("SYS_TODAS",		"todas");
define("SYS_TIPO",		"tipo");
define("SYS_DEFAULT",		"default");
define("SYS_AUTOMATICO",	"automatico");
define("SYS_NORMAL",		"normal");
define("SYS_ESTADO",		"estatus");
define("SYS_RIESGO_BAJO",		10);
define("SYS_RIESGO_MEDIO",		50);
define("SYS_RIESGO_ALTO",		100);

define("SYS_LOG_NIVEL_DEV",		"developer");

define("SYS_ALERTA_POR_EVENTO",		"SYS_ALERTA_POR_EVENTO");
define("SYS_ALERTA_AL_CIERRE",		"SYS_ALERTA_AL_CIERRE");

define("SYS_CREDITO_DIAS_VENCIDOS",		"dias_vencidos");
define("SYS_CREDITO_DIAS_MOROSOS",		"dias_morosos");
define("SYS_CREDITO_DIAS_NORMALES",		"dias_normales");

define("SYS_USER_ESTADO_ACTIVO",		"activo");
define("SYS_USER_ESTADO_BAJA",		"baja");
define("SYS_USER_ESTADO_SUSP",		"suspension");
define("SYS_USER_ID",				"SN_b80bb7740288fda1f201890375a60c8f");
define("SYS_USER_NIVEL",			"SN_d567c9b2d95fbc0a51e94d665abe9da3");
define("SYS_USER_TIPO",				"SN_d567c9b2d95fbc0a51e94d665abe9da3_2");

define("SYS_LOCAL_VARS_LOAD",		"sys.local.var.loaded");

define("SYS_ENTRADAS",		1);
define("SYS_SALIDAS",		-1);

define("SVC_GET", "GET");
define("SVC_PUT", "PUT");
define("SVC_DEL", "DEL");
define("SVC_LIST", "LIST");
//--------------------- Listado de servicios
define("SVC_PERSONAS_ACTIVIDAD_ECONOMICA", "personas.actividad.economica");
//--------------------- VALORES DE ACTIVIDAD ECONOMICA
define("PERSONAS_ACTIVIDAD_ECONOMICA_SECTOR", "SECTOR");
define("PERSONAS_ACTIVIDAD_ECONOMICA_SUBSECTOR", "SUBSECTOR");
define("PERSONAS_ACTIVIDAD_ECONOMICA_RAMA", "RAMA");
define("PERSONAS_ACTIVIDAD_ECONOMICA_SUBRAMA", "SUBRAMA");
define("PERSONAS_ACTIVIDAD_ECONOMICA_CLASE", "CLASE");
define("PERSONAS_ACTIVIDAD_ECONOMICA_PDTO", "PRODUCTO");

define("PERSONAS_OTROS_AHORRO_PREF", "PERSONAS_MONTO_AHORRO_PREFERENTE");

define("AML_ALERT_SMS", 		"AML_ALERT_SMS");
define("AML_ALERT_MAIL",		"AML_ALERT_MAIL");

define("AML_MEDIDA_DOLARES", 		"USD");

define("AML_RISK_INTERNAL_OPERATION", 	101);
define("AML_KYC_DOCTO_NO_VERIFICADO", 	0);
define("AML_KYC_DOCTO_FALSO", 		-1);
define("AML_KYC_DOCTO_REAL", 		1);

define("AML_KYC_DOCTO_ACTIVO", 		1);
define("AML_KYC_DOCTO_INACTIVO", 	0);

//define("AML_KYC_PERFIL_VIGENCIA", 	90);

define("AML_PERSONA_BAJO_RIESGO", 	10);
define("AML_PERSONA_MEDIO_RIESGO", 	50);
define("AML_PERSONA_ALTO_RIESGO", 	100);

define("AML_REPORTE_INMEDIATO", "I");
define("AML_REPORTE_CALIFICADO", "C");

define("AML_CHEQUEO_INMEDIATO", "I");
define("AML_CHEQUEO_DIARIO", "D");
define("AML_CHEQUEO_MENSUAL", "M");

define("AML_OPERACIONES_RETIRO_EFVO", 151);
define("AML_OPERACIONES_PAGOS_EFVO", 101);
define("AML_OPERACIONES_RETIRO_EFVO_INT", 152);
define("AML_OPERACIONES_PAGOS_EFVO_INT", 102);


//define("AML_OPERACIONES_RETIRO_TRANS", 151);
//define("AML_OPERACIONES_PAGOS_EFVO", 101);
//define("AML_OPERACIONES_RETIRO_EFVO_INT", 152);
//define("AML_OPERACIONES_PAGOS_EFVO_INT", 102);


define("AML_DEBITOS_MAYORES_AL_PERFIL", 911100);
define("AML_CREDITOS_MAYORES_AL_PERFIL", 911502);
define("AML_ID_OPERACIONES_ATOMICAS", 101003);
define("AML_ID_OPERACIONES_PERSONAS_ALTO_RIESGO", 101501);
define("AML_ID_OPERACIONES_PERSONAS_PEP", 101510);

define("AML_CLAVE_OPERACIONES_RELEVANTES", 912);
define("AML_CLAVE_OPERACIONES_INUSUALES", 911);
define("AML_CLAVE_OPERACIONES_INTERNAS", 101);




define("AML_OPERACIONES_CLAVE_DIVISA", "07");
//-------------------------------------------------------------------------------------------------------------------------------
define("SYS_FACTOR_DIAS",		84600);
define("SYS_GET",			"GET");
define("SYS_POST",		"POST");
define("SYS_PRINCIPAL",		"SYS.PRINCIPAL");

define("SESSION_SOCIO", 	"socio_en_session");
define("FECHA_FORMATO_ISO", 	"ISO");
define("FECHA_FORMATO_MX", 	"ES_MX");
/* Sistema: formatos de Salida */
define("OUT_HTML",		"html");
define("OUT_TXT",		"txt");
define("OUT_EXCEL",		"xls");
define("OUT_CSV",		"csv");
define("OUT_PDF",		"pdf");
define("OUT_RXML",		"xml");
define("OUT_DOC",		"doc");

define("OUT_DEFAULT",		SYS_DEFAULT);

/* Tesoreria: constantes */
define("TESORERIA_CAJA_ABIERTA", 	"1");
define("TESORERIA_CAJA_CERRADA", 	"0");


define("TESORERIA_COBRO_EFECTIVO", 	"efectivo");
define("TESORERIA_COBRO_CHEQUE", 	"foraneo");
define("TESORERIA_COBRO_DESCTO", 	"descuento");
define("TESORERIA_COBRO_DOCTO", 	"documento");
define("TESORERIA_COBRO_TRANSFERENCIA", "transferencia");
define("TESORERIA_COBRO_MULTIPLE", 	"multiple");
define("TESORERIA_COBRO_INTERNO", 	"cheque.ingreso");	//cheques propios cambiado como efectivo
define("TESORERIA_COBRO_NINGUNO", 	"ninguno");


define("TESORERIA_PAGO_EFECTIVO", 	"efectivo.egreso");
define("TESORERIA_PAGO_CHEQUE", 	"cheque");
define("TESORERIA_PAGO_NINGUNO", 	"ninguno");
define("TESORERIA_PAGO_TRANSFERENCIA", 	"transferencia.egreso");
define("TESORERIA_PAGO_DOCTO", 	"documento.egreso");

define("TESORERIA_RECIBOS_ORIGEN_MIXTO", 	"mixto");
define("TESORERIA_RECIBOS_ORIGEN_CRED", 	"colocacion");
define("TESORERIA_RECIBOS_ORIGEN_CAPT", 	"captacion");

define("TESORERIA_TIPO_INGRESOS", 	"ingresos");
define("TESORERIA_TIPO_EGRESOS", 	"egresos");
define("TESORERIA_MAXIMO_CAMBIO", 	500);
/* Bancos */
define("BANCOS_OPERACION_DEPOSITO", 	"deposito");
define("BANCOS_OPERACION_CHEQUE", 	"cheque");
define("BANCOS_OPERACION_COMISION", 	"comision");
define("BANCOS_OPERACION_RETIRO", 	"retiro");
define("BANCOS_OPERACION_TRASPASO", 	"traspaso");
/* Constantes de memo */
define("MEMOS_TIPO_VINCULACION", 		10);
define("MEMOS_TIPO_DESVINCULACION", 	9);
define("MEMOS_TIPO_PENDIENTE", 	2);
define("MEMOS_TIPO_NOTA_RENOVACION", 	11);
define("MEMOS_TIPO_HISTORIAL", 	1);
define("MEMOS_TIPO_RECORDATORIO",		8);
define("AVISOS_TIPO_RECORDATORIO",		8);

//define("MEMOS_TIPO_LLAMADA", 	);
/* Personas: constantes */
define("PERSONAS_FIGURA_FISICA",			1);
define("PERSONAS_FIGURA_MORAL",			2);
define("PERSONAS_TIPO_IDENT_MORAL",			120);
define("PERSONAS_FIGURA_MORAL_EXENTA",		5);

define("PERSONAS_FISCAL_ASALARIADO",		100);
define("PERSONAS_FISCAL_NINGUNO",		1);
define("FALLBACK_PERSONAS_REGIMEN_FISCAL",		1);

define("PERSONAS_ES_FISICA",			1);
define("PERSONAS_ES_MORAL",			3);

define("PERSONAS_ESTADO_INACTIVO",			20);


define("CREDITO_DEFAULT_TIPO_PAGO",		CREDITO_TIPO_PAGO_PERIODICO);
define("CREDITO_DEFAULT_DIAS_DE_PAGO",		99); //99 fecha + periodo, 1 preestablecidos, 2 personalizados
define("FECHA_OPERATIVA",			"fecha.de.operacion");

//--------------------- VALORES FALLBACK---------------------------------------------------------------------------------------


define("SISTEMA_CAJASLOCALES_ACTIVA",		(bool) $xC->get("usar_sistema_de_cajas_locales", "false", MMOD_PERSONAS));
//Datos del fondo de defuncion
define("CUOTA_DIARIA_DE_DEFUNCION",     $xC->get("cuota_diaria_de_defuncion", 1, MMOD_OPERACIONES));
define("ACTIVE_FORMULA_PARA_DEFUNCION", $xC->get("formula_de_fondo_de_defuncion_activa", 1, MMOD_OPERACIONES));
define("ACTIVE_PAGO_MULTIPLE_DEFUNCION", $xC->get("activar_el_pago_multiple_del_fondo_de_defuncion", "true", MMOD_OPERACIONES));
define("DEFAULT_RECIBO",                $xC->get("numero_de_recibo_por_defecto", FALLBACK_CLAVE_DE_RECIBO, MMOD_OPERACIONES ));
define("DEFAULT_RECIBO_FISCAL",         $xC->get("numero_de_recibo_fiscal_por_defecto", "", MMOD_OPERACIONES ));
define("WORK_IN_SATURDAY",              $xC->get("trabajar_en_dias_sabado", "true", MMOD_OPERACIONES) );//Define si se trabaja en sabado o n
define("WORK_IN_SUNDAY",              	$xC->get("trabajar_en_dias_domingo", "false", MMOD_OPERACIONES) );
define("REVISAR_DIAS_DE_CIERRE",        $xC->get("numero_de_dias_a_revisar_en_cierres", 10, MMOD_OPERACIONES) );
define("DIAS_A_ROTAR_FONDO_DE_DEFUNCION",$xC->get("dias_a_rotar_por_fondo_de_defuncion", 365, MMOD_OPERACIONES) );//Define los dias de rotacion de la cuota de defuncion
define("FORZAR_IVA_AL_PAGO",            (bool) $xC->get("forzar_iva_al_pago", "true", MMOD_OPERACIONES));


define("DEFAULT_SOCIO",                 $xC->get("socio_por_defecto", FALLBACK_CLAVE_DE_PERSONA, MMOD_PERSONAS ) );        //Socio por Defecto
define("DEFAULT_CAJA_LOCAL",     		$xC->get("punto_de_acceso_por_defecto", FALLBACK_PERSONAS_CAJALOCAL, MMOD_PERSONAS ));
define("DEFAULT_GRUPO",                 $xC->get("grupo_por_defecto", FALLBACK_CLAVE_DE_GRUPO, MMOD_PERSONAS ) );        //Grupo por defecto
define("DEFAULT_CREDITO",               $xC->get("numero_de_credito_por_defecto", FALLBACK_CLAVE_DE_CREDITO, MMOD_COLOCACION ));

define("DEFAULT_CHEQUE",                $xC->get("codigo_de_cheque_por_defecto", "0", MMOD_BANCOS ));

define("DEFAULT_TIPO_PAGO",             $xC->get("tipo_de_pago_por_defecto", "efectivo", MMOD_TESORERIA));
define("DEFAULT_TIPO_MEMO",             $xC->get("tipo_de_memo_por_defecto", "0", MMOD_PERSONAS));
define("DEFAULT_USER",                  $xC->get("usuario_del_sistema_por_defecto", 1, MMOD_SISTEMA ));
define("DEFAULT_TIPO_INGRESO",          $xC->get("tipo_de_ingreso_por_defecto", FALLBACK_PERSONAS_TIPO_ING, MMOD_PERSONAS));
define("DEFAULT_TIPO_CONVENIO",         $xC->get("tipo_de_producto_de_credito_por_defecto", 99, MMOD_COLOCACION));
define("DEFAULT_PERIOCIDAD_PAGO",       $xC->get("tipo_de_periocidad_de_pago_por_defecto", 7, MMOD_COLOCACION));
define("DEFAULT_SUBPRODUCTO_CAPTACION", $xC->get("producto_de_captacion_por_defecto", "99", MMOD_CAPTACION) );
define("DEFAULT_CUENTA_BANCARIA",       $xC->get("cuenta_bancaria_por_defecto", FALLBACK_CUENTA_BANCARIA, MMOD_BANCOS) );

define("BANCOS_CUENTA_OMITIDA",       $xC->get("cuenta_bancaria_omitida", "", MMOD_BANCOS) );
define("BANCOS_CUENTA_PREFERENTE",       $xC->get("cuenta_bancaria_preferente", "", MMOD_BANCOS) );

define("DEFAULT_PERSONAS_REGIMEN_VIV",   $xC->get("persona_regimen_de_vivienda_por_defecto", FALLBACK_PERSONAS_REGIMEN_VIV, MMOD_PERSONAS ));
define("DEFAULT_PERSONAS_TIPO_VIV",      $xC->get("persona_tipo_de_vivienda_por_defecto", FALLBACK_PERSONAS_TIPO_VIV, MMOD_PERSONAS ));
define("DEFAULT_PERSONAS_RFC_GENERICO",      $xC->get("persona_id_fiscal_generico", "XAXX010101000", MMOD_PERSONAS ));
define("PERSONAS_CLAVE_ID_POBLACIONAL",      $xC->get("personas_clave_de_idpoblacional", "110", MMOD_PERSONAS ));
define("PERSONAS_BUSCAR_POR",     			 $xC->get("personas_buscar_por", "n", MMOD_PERSONAS ));
define("PERSONAS_HEREDAR_SUCURSAL",     	(bool) $xC->get("personas_heredar_sucursal", "true", MMOD_PERSONAS ));
//define("DEFAULT_PERSONAS_RFC_GENERICO",      $xC->get("persona_id_fiscal_generico", "XAXX010101000", MMOD_PERSONAS ));

define("EXPIRE_PASSWORDS_IN_DAYS",      $xC->get("numero_de_dias_en_que_expira_una_contrasenna", 10,  MMOD_SISTEMA));//Define el Numero de dias en que expira una Contrasenna
define("FORCE_PASSWORD_EXPIRE",         (bool) $xC->get("forzar_la_expiracion_de_contrasennas",  "true", MMOD_SISTEMA) );
define("FORCE_SESSION_LOCKED",          (bool) $xC->get("forzar_bloqueo_de_sesion_por_terminal", "true", MMOD_SISTEMA ));			//Forzar la sesion de usuario por IP
define("ENVIAR_MAIL_LOGS",          (bool) $xC->get("enviar_logs_del_sistema_al_admin", "false", MMOD_SISTEMA ));			//Forzar la sesion de usuario por IP
define("SYS_TRADUCIR_MENUS",          (bool) $xC->get("traducir_menus_en_auto", "false", MMOD_SISTEMA ));			//Forzar la sesion de usuario por IP
define("SYS_FTP_USER",         	 	$xC->get("nombre_de_usuario_ftp", "", MMOD_SISTEMA ));			//Forzar la sesion de usuario por IP
define("SYS_FTP_PWD",          		$xC->get("password_de_usuario_ftp", "", MMOD_SISTEMA ));			//Forzar la sesion de usuario por IP
define("SYS_FTP_SERVER",          	$xC->get("url_del_servidor_ftp", "127.0.0.1", MMOD_SISTEMA ));			//Forzar la sesion de usuario por IP
define("SYS_SEPARADOR_DECIMAL", ".");
define("SYS_FORMATO_FECHA",        $xC->get("formato_de_fecha", "dd-mm-yyyy", MMOD_SISTEMA ));			//Forzar la sesion de usuario por IP
define("SYS_LOG_FILE", "safe-osms.log");
define("SYS_RETARDO", 1000);

//======================================= CONFIGURACION DE HERRAMIENTAS EXTERNAS

//======================================= CONSTANTES CREDITOS


define("CREDITO_GENERAR_MVTO_MORA",			(bool) $xC->get("credito_generar_operacion_en_mvto_a_mora", "false", MMOD_COLOCACION));
define("CREDITO_GENERA_MVTO_VIGENTE",		(bool) $xC->get("credito_generar_operacion_en_mvto_a_vigente", "false", MMOD_COLOCACION));
define("CREDITO_PURGAR_ESTADOS",			(bool) $xC->get("credito_purgar_estados_de_credito", "true", MMOD_COLOCACION));
define("CREDITO_REGISTRAR_ESTADOS",			(bool) $xC->get("credito_registrar_estados_de_credito", "false", MMOD_COLOCACION));

define("CREDITO_CIERRE_FORZAR_DEVENGADOS",	(bool) $xC->get("forzar_devengados_al_cierre_del_dia", "false", MMOD_COLOCACION));
define("CREDITO_GENERAR_DEVENGADOS_ONFLY",	(bool) $xC->get("generar_devengados_al_vuelo", "true", MMOD_COLOCACION));
define("CREDITO_USAR_OFICIAL_SEGUIMIENTO",	(bool) $xC->get("activar_el_uso_de_oficial_de_seguimiento", "true", MMOD_COLOCACION));
define("CREDITO_PRODUCTO_CON_PRESUPUESTO",	$xC->get("clave_de_producto_presupuestado", "200", MMOD_COLOCACION));
define("CREDITO_TASA_COM_APERTURA_GLOBAL",	$xC->get("tasa_de_comision_por_apertura_global", "0", MMOD_COLOCACION)); //Tasa que aplica en otros de plan de pagos
define("CREDITO_TASA_PENA_GLOBAL",			$xC->get("tasa_de_pena_global", "0", MMOD_COLOCACION)); //Tasa que aplica en otros de plan de pagos

define("CREDITO_EN_PLAN_COM_APERTURA",		$xC->get("planes.aplicar_comision_en_primer_pago", "false", MMOD_COLOCACION)); //Tasa que aplica en otros de plan de pagos
define("CREDITO_CONTROLAR_POR_PERIODOS", 	(bool) $xC->get("creditos_controlar_por_periodos", "false", MMOD_COLOCACION));
define("CREDITO_CONTROLAR_POR_ORIGEN", 	(bool) $xC->get("creditos_controlar_por_origen", "true", MMOD_COLOCACION));
define("CREDITO_USAR_AHORRO", 	(bool) $xC->get("creditos_usar_ahorro_en_creds", "false", MMOD_COLOCACION));
//======================================= CONSTANTES DE POBLACION
define("PERSONAS_VIVIENDA_MANUAL", 			(bool) $xC->get("personas_manejar_vivienda_manual", "false", MMOD_PERSONAS));
define("PERSONAS_NOMBRE_ID_POBLACIONAL", 	$xC->get("nombre_del_identificador_poblacional", "C.U.R.P.", MMOD_PERSONAS));
define("PERSONAS_NOMBRE_ID_FISCAL", 		$xC->get("nombre_del_identificador_fiscal", "R.F.C.", MMOD_PERSONAS));
define("PERSONAS_CONTROLAR_POR_EMPRESA", 	(bool) $xC->get("personas_controlar_por_empresas", "true", MMOD_PERSONAS));
define("PERSONAS_CONTROLAR_POR_GRUPO", 		(bool) $xC->get("personas_controlar_por_grupos", "false", MMOD_PERSONAS));
define("PERSONAS_CONTROLAR_POR_APORTS", 	(bool) $xC->get("personas_controlar_por_aportaciones", "false", MMOD_PERSONAS));
define("PERSONAS_CONTROLAR_MICROSEGUROS", 	(bool) $xC->get("personas_controlar_microseguros", "false", MMOD_PERSONAS));

define("PERSONAS_LARGO_IDFISCAL",     		$xC->get("persona_largo_de_id_fiscal", "13", MMOD_PERSONAS ));
define("PERSONAS_LARGO_IDPOBLACIONAL", 		$xC->get("persona_largo_de_id_poblacional", "12", MMOD_PERSONAS ));
define("PERSONAS_ACEPTAR_MENORES", 			(bool) $xC->get("persona_aceptar_menores_de_edad", "false", MMOD_PERSONAS));
define("PERSONAS_ACEPTAR_EXTRANJEROS", 		(bool) $xC->get("personas_aceptar_extranjeros", "true", MMOD_PERSONAS));
define("DIGITOS_DE_CODIGO_POSTAL",          $xC->get("numero_de_digitos_del_codigo_postal", 5, MMOD_PERSONAS) );
define("EDAD_PRODUCTIVA_MAXIMA",            $xC->get("edad_productiva_maxima", "65", MMOD_PERSONAS) );
define("EDAD_PRODUCTIVA_MINIMA",            $xC->get("edad_productiva_minima", "18", MMOD_PERSONAS) );

define("PERSONAS_TITULO_PARTES",		$xC->get("titulo_de_partes_relacionadas", "Referencias Personales", MMOD_PERSONAS));
//define("PERSONAS_VIV_ENTIDADES_OK",         $xC->get("entidades_federativas_habilitadas", "4,31,15,9,27,30,23", MMOD_PERSONAS) );

define("CAPITAL_SOCIAL_EN_CAPTACION", 		(bool) $xC->get("manejar_captal_social_en_captacion", "true", MMOD_CAPTACION));
define("CAPTACION_USE_TASA_DETALLADA", 		(bool) $xC->get("manejar_en_detalle_las_tasas", "true", MMOD_CAPTACION)); //indica si se va a usar las tasas de cuenta corriente por subproducto

define("AML_OFICIAL_DE_CUMPLIMIENTO", $xC->get("clave_de_usuario_del_oficial_de_cumplimiento", 99, MMOD_AML) );
define("AML_TITULO_DE_ALERTA", $xC->get("mensaje_del_sistema_de_alertas_aml", "Sistema Automatizado de Alertas.- S.A.F.E.-OSMS", MMOD_AML) );
define("AML_ALERTAS_POR_SMS", (boolean) $xC->get("aml_alertas_por_sms", "true", MMOD_AML) );
define("AML_CLAVE_RIESGO_OPS_INDIVIDUALES", $xC->get("clave_de_riesgo_de_operaciones_individuales", 912102, MMOD_AML) );
define("AML_CLAVE_RIESGO_OPS_RELEVANTES", $xC->get("clave_de_riesgo_de_operaciones_relevantes", 910000, MMOD_AML) );

define("AML_TOLERA_OPS_MTO_PERFIL", 			$xC->get("minimo_de_exceso_en_operaciones_segun_perfil", 500, MMOD_AML) );
define("AML_TOLERA_OPS_NUM_PERFIL", 			$xC->get("minimo_de_exceso_en_numero_por_perfil", 5, MMOD_AML) );


//GWS

define("AML_GWS_USER", 			$xC->get("usuario_consulta_gws", "", MMOD_AML) );
define("AML_GWS_PWD", 			$xC->get("credencial_consulta_gws", "", MMOD_AML) );
define("AML_GWS_TOKEN", 		$xC->get("token_consulta_gws", "", MMOD_AML) );


define("AML_BUSQUEDA_PERSONAS_REFORZADA",  (bool) $xC->get("busqueda_de_personas_reforzada", "false", MMOD_AML) );
define("AML_BUSQUEDA_POR_SONIDO",  (bool) $xC->get("busqueda_de_personas_por_sonido", "false", MMOD_AML) );
define("AML_PERSONAS_NO_VIGILADAS_LIMITE_INFERIOR",  $xC->get("limite_inferior_para_personas_bloqueadas", "3000", MMOD_AML) );
define("AML_LEYENDA_REPORTE_24_HORAS", 			 $xC->get("leyenda_del_reporte_de_24_horas", "Reporte de 24 Horas", "", MMOD_AML) );
//datos del formato de creditos, socio y cuenta de captacion
define("DIGITOS_DE_SOCIO",                      $xC->get("numero_de_digitos_de_la_clave_de_socios") );
define("DIGITOS_DE_CAJA_LOCAL",                 $xC->get("numero_de_digitos_de_la_clave_de_caja_local") );
define("DIGITOS_DE_CLAVE_DE_SOCIO",             (DIGITOS_DE_SOCIO + DIGITOS_DE_CAJA_LOCAL) );
define("DIGITOS_DE_DOCUMENTO",                  $xC->get("numero_de_digitos_de_documento", 2, MMOD_SISTEMA) );

define("PERSONAS_REL_MANCOMUNADO",               70);
define("PERSONAS_REL_AVAL_QUIRO",               51);
define("PERSONAS_REL_AVAL_HIPO",               52);
define("PERSONAS_REL_REP_LEGAL",               14);
define("PERSONAS_REL_TUTOR_LEGAL",             13);
define("PERSONAS_REL_REF_BANCARIA",             22);
define("PERSONAS_REL_REF_PERSONAL",             21);
define("PERSONAS_REL_REF_COMERCIAL",            23);

define("PERSONAS_REL_RES_SOLIDARIO",            12);

define("PERSONAS_REL_PROP_REAL",             552);
define("PERSONAS_REL_PROV_RECURSOS",        551);

define("PERSONAS_REL_CLASE_AVAL",               5);
define("PERSONAS_REL_CLASE_CAPT",               7);
define("PERSONAS_REL_CLASE_REF",               2);

//Datos de Impuestos
define("EXCENCION_IDE", 			$xC->get("monto_exento_del_ide") );
define("TASA_IDE", 			        $xC->get("tasa_del_ide") );
define("TASA_IVA", 				$xC->get("tasa_del_iva") );

define("PRODUCTO_GARANTIA_LIQUIDA", 		$xC->get("clave_de_producto_de_garantia_liquida", "", MMOD_CAPTACION) );
define("PRODUCTO_CUENTA_INTERESES", 		$xC->get("clave_de_producto_de_cuenta_de_intereses", "", MMOD_CAPTACION) );

define("CAPTACION_PRODUCTO_CAPITALSOCIAL",		30);
define("CAPTACION_PRODUCTO_INTERESES",		PRODUCTO_CUENTA_INTERESES);
define("CAPTACION_PRODUCTO_GARANTIALIQ",	PRODUCTO_GARANTIA_LIQUIDA);
define("CAPTACION_PRODUCTO_ORDINARIO",		$xC->get("clave_de_producto_ordinario",1, MMOD_CAPTACION));



define("TASA_ISR_POR_INTERESES", 			$xC->get("tasa_isr_por_intereses",0, MMOD_CAPTACION) );
define("SALARIOS_EXENTOS_ISR_INTERESES", 	$xC->get("salarios_minimos_exentos_por_isr_por_intereses",0, MMOD_CAPTACION) );
define("SALARIO_VIGENTE_DF", 				$xC->get("salario_vigente_en_el_df", "0", $xC->ENTIDAD_LEGAL) ); //Abril-2012 ;
define("INTERES_TIPO_CALCULO_DEFAULT", 		$xC->get("tipo_de_calculo_de_interes_por_defecto", 2, MMOD_COLOCACION ) );//1= saldo historico 2 = saldos insolutos
//Cuadrar saldos de creditos y captacion VS Movimientos de operacion
define("FORCE_CUADRE_EN_OPERACIONES",		(bool) $xC->get("forzar_el_cuadre_de_documentos_por_movimientos", "true", MMOD_OPERACIONES)); //true
//fecha de inicio en el sistema
//2011-10-09
$fecha_de_inicio_operaciones			= (isset($fecha_de_inicio_operaciones) ) ? $fecha_de_inicio_operaciones : $xC->get("fecha_de_inicio_de_operaciones_en_el_sistema") ; 
define("FECHA_INICIO_OPERACIONES_SISTEMA",	$fecha_de_inicio_operaciones) ; //true
//Prioridad de la fecha de vencimiento en creditos, sobre los dias autorizados

//Define URL para Actualizaciones
define("URL_UPDATES",                           $xC->get("url_de_actualizaciones_automaticas", SVC_REMOTE_HOST, MMOD_SISTEMA) );
//define si la garantia liquida es usada por captacion o una cuenta aparte, ajena a captacion
define("GARANTIA_LIQUIDA_EN_CAPTACION", 		$xC->get("utilizar_garantia_liquida_en_captacion", "true", MMOD_CAPTACION) );
//============================================ LAVADO DE DINERO.- MINIMO
define("VALOR_ACTUAL_DOLAR",                          $xC->get("precio_del_dolar", 15, MMOD_AML) );
//define("MONTO_MINIMO_OPERACIONES_RELEVANTES",   $xC->get("monto_minimo_para_reportar_operaciones", 10000, MMOD_AML) );
define("PERSONAS_PERMITIR_EXTRANJEROS", (bool)  $xC->get("permitir_personas_extranjeras", "false", MMOD_AML) );
//============================================ RIESGOS
define("COSTO_CANASTA_BASICA",                  $xC->get("costo_de_la_canasta_basica") );
//Configuracion de seguimiento
define("SEGUIMIENTO_GENERAR_AL_CIERRE",      	(bool) $xC->get("generar_llamadas_al_cierre", "false", MMOD_SEGUIMIENTO) );
define("DIAS_DE_INTERVALO_POR_LLAMADAS",        $xC->get("dias_de_intervalo_por_llamadas", "2", MMOD_SEGUIMIENTO) );
define("DIAS_DE_ANTICIPACION_PARA_LLAMADAS",    $xC->get("dias_de_anticipacion_por_llamadas","2", MMOD_SEGUIMIENTO) );
define("DIAS_A_ESPERAR_POR_NOTIFICACION",       $xC->get("dias_a_esperar_por_notificacion", "5", MMOD_SEGUIMIENTO) );
define("MINUTOS_A_ESPERAR_POR_LLAMADAS",        $xC->get("minutos_a_esperar_por_llamadas", "15", MMOD_SEGUIMIENTO) );


define("SEGUIMIENTO_NOTIF_CBZA_PREV",        	(bool) $xC->get("generar_notif_cbza_prev", "true", MMOD_SEGUIMIENTO) );
define("SEG_CANAL_NOTIF_CBZA_PREV",        		$xC->get("canal_notif_cbza_prev", "wsms", MMOD_SEGUIMIENTO) );
//define("SEGUIMIENTO_WATHSAPP_NUMERO",   		$xC->get("wathsapp_numero_telefonico", "529811098164", MMOD_SEGUIMIENTO) );
define("SEGUIMIENTO_WATHSAPP_DID",  	 		$xC->get("wathsapp_numero_deviceid", "", MMOD_SEGUIMIENTO) );
//define("SEGUIMIENTO_WATHSAPP_USER",   			$xC->get("wathsapp_nombre_de_usuario", "patadejaguar", MMOD_SEGUIMIENTO) );
define("SEGUIMIENTO_WATHSAPP_PWD",   			$xC->get("wathsapp_password_de_usuario", "", MMOD_SEGUIMIENTO) );
define("SEGUIMIENTO_TELEFONO_PREFIJO",   		$xC->get("telefono_prefijo_int", "52", MMOD_SEGUIMIENTO) );

define("SEGUIMIENTO_ESTADO_PENDIENTE",        	"pendiente" );
define("SEGUIMIENTO_ESTADO_EFECTUADO",        	"efectuado" );
define("SEGUIMIENTO_ESTADO_CANCELADO",        	"cancelado" );
define("SEGUIMIENTO_ESTADO_VENCIDO",        	"vencido" );

//============================================
define("CAPTACION_INVERSIONES_POR_DIA",         $xC->get("usar_inversiones_por_dia", "false", MMOD_CAPTACION) );			//incluir inversiones por dias o por periodos cerrados 30, 60, 90, 180, 360 dias.

define("CLAVE_INVERSION_A_PLAZO",				20);
define("CLAVE_A_LA_VISTA", 						10);

define("CAPTACION_TIPO_PLAZO",						20);
define("CAPTACION_TIPO_VISTA", 						10);


define("CAPTACION_ORIGEN_CONDICIONADO",	2);
define("CAPTACION_DESTINO_CTA_INTERES",			"CUENTA_INTERESES");

define("DEFAULT_CODIGO_DE_ERROR", 				9001);

define("SAFE_HOST_URL", 						$url_host);


//============================================ CONFIGURACION CONTABLE ===================================================================
//1-1-05-01-37-1234	//Catalogo Anterior
//1-1-03-01-01-123456	//Catalogo Nuevo
//1-1-03-01-01-1234567	//Catalogo Nuevo 17Oct2011
//=====================================================================================================
//=====================================================================================================
define("USE_CONTPAQ",                   (bool) $xC->get("usar_contabilidad_tipo_compac", "true", MMOD_CONTABILIDAD) );//Define si se usa el ContPaq para Contabilizar
define("GENERAR_POLIZAS_AL_CIERRE",     (bool) $xC->get("generar_polizas_al_cierre_del_dia", "true", MMOD_CONTABILIDAD));
//define("CONTABLE_CATALOGO_POR_PERSONA",     (bool) $xC->get("usar_contabilidad_por_persona", "false", MMOD_CONTABILIDAD));
define("CONTABLE_CUENTAS_POR_SOCIO",  (bool) $xC->get("usar_contabilidad_por_persona", "false", MMOD_CONTABILIDAD));
define("CONTABLE_EN_MIGRACION",  (bool) 	$xC->get("contabilidad_en_migracion", "true", MMOD_CONTABILIDAD));
//1-00-00-00-00-00-00-00
define("CONTABLE_CATALOGO_MASCARA",     	$xC->get("mascara_de_cuenta_contable", "1-2-2-2-2-2-2-2", MMOD_CONTABILIDAD));
define("CONTABLE_CATALOGO_MASCARA_SQL",     $xC->get("mascara_sql_de_cuenta_contable", "#-##-##-##-##-##-##", MMOD_CONTABILIDAD));

//Otros parametros Contables
define("GENERAR_CONTABILIDAD", 				(bool) $xC->get("generar_contabilidad", "true", MMOD_CONTABILIDAD ));
define("CUENTA_DE_CUADRE", 					$xC->get("cuenta_de_cuadre", "85990000000000", MMOD_CONTABILIDAD) );
define("PREDICT_MOVIMIENTO", 				(bool) $xC->get("predecir_movimiento_en_poliza", "true", MMOD_CONTABILIDAD) );//true);		//predecir Movimiento
define("CDIVISOR", 							$xC->get("caracter_divisor_de_cuenta", "-", MMOD_CONTABILIDAD) ); //"-");
define("CUENTA_CONTABLE_EFECTIVO", 			$xC->get("cuenta_contable_de_efectivo", "", MMOD_CONTABILIDAD) );
define("CUENTA_CONTABLE_IVA_INTERESES", 	$xC->get("cuenta_contable_iva_en_intereses", "", MMOD_CONTABILIDAD) );
define("CUENTA_CONTABLE_IVA_OTROS", 		$xC->get("cuenta_contable_iva_en_otros", "", MMOD_CONTABILIDAD) );
define("DEFAULT_CENTRO_DE_COSTO", 			$xC->get("id_del_centro_de_costo_por_defecto", "1", MMOD_CONTABILIDAD) );
define("CONTABLE_RESULTADO_DEL_PERIODO", 	$xC->get("resultado_del_periodo_contable", "0", MMOD_CONTABILIDAD) );

define("CONTABLE_CLAVE_ACTIVO", 	$xC->get("clave_contable_de_activo", "1", MMOD_CONTABILIDAD) );
define("CONTABLE_CLAVE_PASIVO", 	$xC->get("clave_contable_de_pasivo", "2", MMOD_CONTABILIDAD) );
define("CONTABLE_CLAVE_CAPITAL", 	$xC->get("clave_contable_de_capital", "3", MMOD_CONTABILIDAD) );
define("CONTABLE_CLAVE_INGRESOS", 	$xC->get("clave_contable_de_ingresos", "5", MMOD_CONTABILIDAD) );
define("CONTABLE_CLAVE_EGRESOS", 	$xC->get("clave_contable_de_egresos", "4", MMOD_CONTABILIDAD) );

define("CONTABLE_FAMILIA_INGRESOS", 	$xC->get("ingresos_lista_cuenta_titulo", "", MMOD_CONTABILIDAD) ); //si contiene mas integrantes
define("CONTABLE_FAMILIA_EGRESOS", 	$xC->get("egresos_lista_cuenta_titulo", "", MMOD_CONTABILIDAD) ); //Si contiene mas integrantes


define("CONTABLE_MAYOR_CARTERA_VIG", 	$xC->get("contable_cuenta_mayor_cartera_vig", "", MMOD_CONTABILIDAD) );
define("CONTABLE_MAYOR_CARTERA_VENC", 	$xC->get("contable_cuenta_mayor_cartera_vencida", "", MMOD_CONTABILIDAD) );
define("CONTABLE_MAYOR_CARTERA_REST", 	$xC->get("contable_cuenta_mayor_cartera_restructurada", "", MMOD_CONTABILIDAD) );
define("CONTABLE_MAYOR_CARTERA_REST_VENC", 	$xC->get("contable_cuenta_mayor_cartera_restructurada_vencida", "", MMOD_CONTABILIDAD) );

define("CONTABLE_MAYOR_CARTERA_REN", 	$xC->get("contable_cuenta_mayor_cartera_renovada", "", MMOD_CONTABILIDAD) );
define("CONTABLE_MAYOR_CARTERA_REN_VENC", 	$xC->get("contable_cuenta_mayor_cartera_renovada_vencida", "", MMOD_CONTABILIDAD) );

define("CONTABLE_MAYOR_INT_DEV_VIG", 	$xC->get("contable_cuenta_mayor_interes_dev_vigente", "", MMOD_CONTABILIDAD) );
define("CONTABLE_MAYOR_INT_DEV_VENC", 	$xC->get("contable_cuenta_mayor_interes_dev_vencido", "", MMOD_CONTABILIDAD) );

define("CONTABLE_MAYOR_INGRESO_INT", 	$xC->get("contable_cuenta_mayor_ingreso_por_ints", "", MMOD_CONTABILIDAD) );
define("CONTABLE_MAYOR_INGRESO_MORA", 	$xC->get("contable_cuenta_mayor_ingreso_por_mora", "", MMOD_CONTABILIDAD) );


define("CONTABLE_NAT_ACTIVO_DEUD", 	"AD" );
define("CONTABLE_NAT_ACTIVO_ACREED", 	"AA" );
define("CONTABLE_NAT_PASIVO_DEUD", 	"PD" );
define("CONTABLE_NAT_PASIVO_ACREED", 	"PA" );
define("CONTABLE_NAT_RESULT_DEUD", 	"RD" );
define("CONTABLE_NAT_RESULT_ACREED", 	"RA" );
define("CONTABLE_NAT_ORDEN_DEUD", 	"OD" );
define("CONTABLE_NAT_OREDN_ACREED", 	"OA" );


$DCONTMASQ		= explode("-", CONTABLE_CATALOGO_MASCARA);

$d_niv1 		= isset($DCONTMASQ[0]) ? $DCONTMASQ[0] : 0;		//Titulo
$d_niv2 		= isset($DCONTMASQ[1]) ? $DCONTMASQ[1] : 0;		//Subtitulo
$d_niv3 		= isset($DCONTMASQ[2]) ? $DCONTMASQ[2] : 0;		//Mayor
$d_niv4 		= isset($DCONTMASQ[3]) ? $DCONTMASQ[3] : 0;		//Subcuenta de Mayor
$d_niv5 		= isset($DCONTMASQ[4]) ? $DCONTMASQ[4] : 0;		//Nivel Comun
$d_niv6 		= isset($DCONTMASQ[5]) ? $DCONTMASQ[5] : 0;		//Nivel 
$d_niv7 		= isset($DCONTMASQ[6]) ? $DCONTMASQ[6] : 0;		//Nivel Persona
 
$d_niv8 		= isset($DCONTMASQ[7]) ? $DCONTMASQ[7] : 0;		//Nivel Persona
$d_niv9 		= isset($DCONTMASQ[8]) ? $DCONTMASQ[8] : 0;		//Nivel Persona

$niveles		= count($DCONTMASQ);
$catalogolen 	= "";

$len_max 		= $d_niv1 + $d_niv2 + $d_niv3 + $d_niv4 + $d_niv5 + $d_niv6 + $d_niv7 + $d_niv8 + $d_niv9;
$len_n1 		= $d_niv1;
$len_n2 		= $d_niv1 + $d_niv2;
$len_n3 		= $d_niv1 + $d_niv2 + $d_niv3;
$len_n4 		= $d_niv1 + $d_niv2 + $d_niv3 + $d_niv4;
$len_n5 		= $d_niv1 + $d_niv2 + $d_niv3 + $d_niv4 + $d_niv5;
$len_n6 		= $d_niv1 + $d_niv2 + $d_niv3 + $d_niv4 + $d_niv5 + $d_niv6;
$len_n7 		= $d_niv1 + $d_niv2 + $d_niv3 + $d_niv4 + $d_niv5 + $d_niv6 + $d_niv7;

$len_n8 		= $d_niv1 + $d_niv2 + $d_niv3 + $d_niv4 + $d_niv5 + $d_niv6 + $d_niv7 + $d_niv8;
$len_n9 		= $d_niv1 + $d_niv2 + $d_niv3 + $d_niv4 + $d_niv5 + $d_niv6 + $d_niv7 + $d_niv8 + $d_niv9;

$catalogolen 	= "$len_max@$len_n1@$len_n2@$len_n3@$len_n4@$len_n5@$len_n6@$len_n7@$len_n8@$len_n9";
/*$DCONTMASQ		= explode("-", CONTABLE_CATALOGO_MASCARA_SQL);
$ttot			= 0;
$icnt			= 1;
foreach ($DCONTMASQ AS $niveles){
	$tseg		= strlen($niveles);
	$ttot		+= $tseg;
	$this->DIGITOS_NIVEL[$icnt] 	= $tseg;
	$this->LARGO_NIVEL[$icnt]		= $ttot;
	$icnt++;
}
$this->LARGO_FORMATEADO		= strlen(CONTABLE_CATALOGO_MASCARA_SQL);
//define("LARGO_MAX", $len_max);
$this->LARGO_TOTAL			= $ttot;*/

//setLog("CATALOGO $catalogolen");
//Definir Numeros de Digitos por Nivel
define("DIG_N1", $d_niv1);
define("DIG_N2", $d_niv2);
define("DIG_N3", $d_niv3);
define("DIG_N4", $d_niv4);
define("DIG_N5", $d_niv5);
define("DIG_N6", $d_niv6);
define("DIG_N7", $d_niv7);
//Definir Largos de Cadena por Nivel
define("LARGO_MAX", $len_max);
define("LARGO_N1", $len_n1);
define("LARGO_N2", $len_n2);
define("LARGO_N3", $len_n3);
define("LARGO_N4", $len_n4);
define("LARGO_N5", $len_n5);
define("LARGO_N6", $len_n6);
define("LARGO_N7", $len_n7);

define("ZERO_EXO", str_repeat("0", $len_n9));
define("NINE_EXO", str_repeat("9", $len_n9));

define("CAT_LEN", $catalogolen);
define("CATALOGO_NUM_NIVELES", $niveles);
define("NC_DEUDORA", 1);
define("NC_ACREEDORA", -1);
define("TM_CARGO", 1);		//Tipo de Movimiento Cargo
define("TM_ABONO", -1);		// ""				Abono

define("POLIZA_INGRESOS", 1);
define("POLIZA_EGRESOS", 2);
define("POLIZA_DIARIO", 3);
define("POLIZA_ORDEN", 4);

define("CONTABLE_TIPO_POLIZA_INGRESOS", 1);
define("CONTABLE_TIPO_POLIZA_EGRESOS", 2);
define("CONTABLE_TIPO_POLIZA_DIARIO", 3);
define("CONTABLE_TIPO_POLIZA_ORDEN", 4);
define("CONTABLE_TIPO_POLIZA_NINGUNA", 999);

define("POLIZA_ID_ULTIMAOPERACION", "poliza.ultima.operacion");

define("CUENTA_SALDO", 				1);
define("CUENTA_SALDO_DEUDOR", 		2);
define("CUENTA_SALDO_ACREEDOR", 	3);

//define("CONTABLE_CUENTA_NIVEL_MAYOR", 			3);
define("CONTABLE_CUENTA_NIVEL_MAYOR", 			3);
define("CONTABLE_CUENTA_NIVEL_TITULO", 			1);
define("CONTABLE_CUENTA_NIVEL_SUBTITULO", 		2);

define("DEFAULT_CONTABLE_DIARIO_MVTOS", 1);

define("CAPITAL_VIGENTE_NORMAL", "CAPITAL_VIGENTE_NORMAL");
define("CAPITAL_VIGENTE_REESTRUCTURADO", "CAPITAL_VIGENTE_REESTRUCTURADO");
define("CAPITAL_VIGENTE_RENOVADO", "CAPITAL_VIGENTE_RENOVADO");

define("CAPITAL_VENCIDO_NORMAL", "CAPITAL_VENCIDO_NORMAL");
define("CAPITAL_VENCIDO_REESTRUCTURADO", "CAPITAL_VENCIDO_REESTRUCTURADO");
define("CAPITAL_VENCIDO_RENOVADO", "CAPITAL_VENCIDO_RENOVADO");

define("INTERES_VIGENTE_NORMAL", "INTERES_VIGENTE_NORMAL");
define("INTERES_VIGENTE_REESTRUCTURADO", "INTERES_VIGENTE_REESTRUCTURADO");
define("INTERES_VIGENTE_RENOVADO", "INTERES_VIGENTE_RENOVADO");

define("INTERES_VENCIDO_NORMAL", "INTERES_VENCIDO_NORMAL");
define("INTERES_VENCIDO_REESTRUCTURADO", "INTERES_VENCIDO_REESTRUCTURADO");
define("INTERES_VENCIDO_RENOVADO", "INTERES_VENCIDO_RENOVADO");

//============================================ NIVELES DE USUARIOS ===================================================================
define("USUARIO_TIPO_GERENTE", 						9);
define("USUARIO_TIPO_CAJERO", 						4);
define("USUARIO_TIPO_JEFECAJA",						5);
define("USUARIO_TIPO_OFICIAL_CRED", 				7);
define("USUARIO_TIPO_OFICIAL_CAPT", 				8);
define("USUARIO_TIPO_OFICIAL_AML", 					10);
define("USUARIO_TIPO_CONTABLE", 					6);
define("USUARIO_TIPO_ORIGINADOR", 					3);
//define("USUARIO_TIPO_SEGUIMIENTO", 					6);
//============================================ HUERFANOS ===================================================================
define("CAPTACION_IMPUESTOS_A_DEPOSITOS_ACTIVO",        (bool) $xC->get("impuestos_a_depositos_activo", "false", MMOD_CAPTACION) );
define("TESORERIA_FORZAR_SESSION", 	(bool) $xC->get("forzar_sesiones_de_caja", "true", MMOD_TESORERIA));

//============================================ HEREDADO ==========================================================

define("CTRL_GOSOCIO", 			"<img class='buscador' title=\"Buscar una Persona\" src=\"../images/common/search.png\" onclick=\"goSocio_();\"/>");
define("CTRL_GOCREDIT", 		"<img class='buscador' title=\"Buscar un Credito\" src=\"../images/common/search.png\" onclick=\"goCredit_();\"/>");
define("CTRL_GOLETRAS", 		"<img class='buscador' title=\"Buscar una Parcialidad\" src=\"../images/common/search.png\"  onclick=\"goLetra_();\"/>");
define("CTRL_GOCUENTAS", 		"<img class='buscador' title=\"Buscar una Cuenta de Captacion\" src=\"../images/common/search.png\"  onclick=\"goCuentas_();\"/>");
define("CTRL_GOCUENTAS_A", 		"<img class='buscador' title=\"Buscar una Cuenta Corriente\" src=\"../images/common/search.png\"  onclick=\"goCuentas_(10);\"/>");
define("CTRL_GOCUENTAS_I", 		"<img class='buscador' title=\"Buscar una Cuenta de Inversion\" src=\"../images/common/search.png\"  onclick=\"goCuentas_(20);\"/>");
define("CTRL_GOGRUPOS", 		"<img class='buscador' title=\"Buscar un Grupo\" src=\"../images/common/search.png\"  onclick=\"goGrupos_();\"/>");
define("CTRL_GORECIBOS", 		"<img class='buscador' title=\"Buscar un Recibo de Pago\" src=\"../images/common/search.png\"  onclick=\"goRecibos_();\"/>");
//define("JS_CLOSE", 				"<script>var xG = new Gen(); xG.close(); </script>");


//============================================ MANEJO DE SESIONES ==========================================================
if(!isset($safe_sesion_en_segundos)){
	$safe_sesion_en_segundos		= $xC->get("tiempo_expira_sesiones", "3600", MMOD_SISTEMA);
	@ini_set('session.gc_maxlifetime', $safe_sesion_en_segundos);
}




class cSAFEConfiguracion {
	public $SISTEMA_RESPALDO_POR_MAIL			= false;
	
}
class cConfigCatalogo {
	public $MODULO_CAPTACION_ACTIVADO		= "modulo_de_captacion_activado";
	public $MODULO_LEASING_ACTIVADO			= "modulo_de_leasing_activado";
	public $MODULO_AML_ACTIVADO				= "modulo_de_aml_activado";
	public $MODULO_CAJA_ACTIVADO			= "modulo_de_caja_activado";
	public $MODULO_CONTABILIDAD_ACTIVADO	= "modulo_de_contabilidad_activado";
	public $MODULO_SEGUIMIENTO_ACTIVADO		= "modulo_de_seguimiento_activado";
	
}
class cConfiguration {
    private $mTipo;
    private $mDescripcion;
    private $mValor				= null;
    private $cnn				= false;
    
    public $ENTIDAD_DOMICILIO	= "entidad.domicilio";
    public $ENTIDAD_LEGAL		= "entidad.legal";
    public $ENTIDAD				= "entidad";
    public $CAPTACION			= "captacion";
    public $COLOCACION			= "colocacion";
    public $CONTABILIDAD		= "contabilidad";
    public $OPERACIONES			= "operaciones";
    public $AML					= "aml";
    public $SISTEMA				= "sistema";
    private $mIDConfig			= "configuracion.";
    private $mArrConfig			= array();
    private $mIDCache			= "";
	function __construct(){
		//-" . USR_DB . "
		$this->mIDCache			= MY_DB_IN . "-" . SAFE_VERSION . "-" . SAFE_REVISION;
   		$this->init();
    }
    function get($parametro, $mValor = false, $mTipo = "desconocido"){
    	if(isset($this->mArrConfig[$this->mIDConfig . $parametro])){
			$this->mValor		= $this->mArrConfig[$this->mIDConfig . $parametro];
			//syslog(LOG_INFO, "Valor CONFIG $parametro $mValor --" . $GLOBALS[$this->mIDConfig . $parametro] );
		} else {
			$this->mValor		= null;
		}
    	if($this->mValor === null){
	    	if($this->cnx() == false){    		
	    	} else {
	
	    		$RS = $this->cnx()->query("SELECT * FROM entidad_configuracion WHERE nombre_del_parametro ='$parametro' LIMIT 0,1");
	    		if ( $RS == false ){
	    			syslog(LOG_INFO, $this->cnx()->errno . "|" . $this->cnx()->errno . "|CONFIG $parametro $mValor" );
	    		} else {
		    		if($RS->num_rows <= 0){
		    			$this->add($parametro, ucwords(str_replace("_", " ", $parametro)), $mValor, $mTipo );
		    			$this->mValor   	= $mValor;   
		    			$this->mArrConfig[$this->mIDConfig . $parametro]	= $mValor;
		    		} else {
		    			$Datos 				= $RS->fetch_assoc();
		    			$this->mValor   	= trim( $Datos["valor_del_parametro"] );
		    			if ( $this->mValor  === "true") {
		    				$this->mValor   = true;
		    			}
		    			if ( $this->mValor  === "false") {
		    				$this->mValor   = false;
		    			}
		    			$this->mArrConfig[$this->mIDConfig . $parametro]	= $mValor;
		    			//syslog(LOG_INFO, "Releer CONFIG $parametro $mValor --" . $GLOBALS[$this->mIDConfig . $parametro] );
		    		}
	    		}
	    	}
    	}
    	return $this->mValor;
    }
    function set($parametro, $valor){
        $sql    = "UPDATE entidad_configuracion
                SET valor_del_parametro='$valor'
                WHERE nombre_del_parametro='$parametro' ";
        $res	= false;
        if($this->cnx()){ 
			$this->cnx()->query($sql);
			if(isset($this->mArrConfig[$this->mIDConfig . $parametro])){
				$this->mArrConfig[$this->mIDConfig . $parametro]	= $valor;
			} 
			//Eliminar Cache
			$xCache				= new cCache();
			$xCache->clean($this->mIDCache);
			$res	= true;
		}
		return $res;
    }
    function add($nombre, $descripcion = "", $valor = "1", $tipo = "desconocido"){
        $sql    = "INSERT INTO entidad_configuracion(tipo, nombre_del_parametro, descripcion_del_parametro, valor_del_parametro)
                    VALUES('$tipo', '$nombre', '$descripcion', '$valor') ";
        //syslog(LOG_INFO, $sql );
    	if($this->cnx() !== false){ $this->cnx()->query($sql);   }
    }
    function init(){
		$xCache				= new cCache();
		$this->mArrConfig	= $xCache->get($this->mIDCache);
		if(!is_array($this->mArrConfig)){
			if($this->cnx()){
				$rs			= $this->cnx()->query("SELECT SQL_CACHE `nombre_del_parametro`,`valor_del_parametro` FROM `entidad_configuracion`");
				while($rw	= $rs->fetch_assoc()){
					$id		= $rw["nombre_del_parametro"];
					$val	= trim($rw["valor_del_parametro"]);
					if($val === "true"){ $val= true;}
					if($val === "false"){ $val= false;}
					$this->mArrConfig[$this->mIDConfig . $id]	= $val;
				}
				$rs->free();
				$xCache->set($this->mIDCache, $this->mArrConfig);
			}
		}
    }    
    function cnx(){
    	if($this->cnn == false){
    		$this->cnn		= new mysqli(WORK_HOST, USR_DB, PWD_DB, MY_DB_IN);
    		if($this->cnn->connect_errno){
    			$this->cnn	= false;
    		}
    	}
        return $this->cnn;
    }
}
function getSucursal($sucursal = false){
	if($sucursal == false){
		if(isset($_SESSION["sucursal"])){
			$sucursal			= $_SESSION["sucursal"];
		} else {
			$sucursal			= DEFAULT_SUCURSAL;
		}
	}
	//$_SESSION["sucursal"]		= $sucursal;
	if(defined("MODO_DEBUG") AND isset($_SESSION["sucursal"]) ){
		if( ($sucursal != $_SESSION["sucursal"]) AND MODO_DEBUG == true){
			syslog(E_ERROR, "Cambio de Sucursal de $sucursal a " . $_SESSION["sucursal"] );
		}
	}
	$_SESSION["sucursal"]		= $sucursal;
	return $sucursal;
}
function getSucursalPorPersona($persona = false){
	$persona	= setNoMenorQueCero($persona);
	if(PERSONAS_HEREDAR_SUCURSAL == true AND $persona>DEFAULT_SOCIO){
		$xSoc	= new cSocio($persona);
		if($xSoc->init() == true){
			return $xSoc->getSucursal();
		} else {
			return getSucursal();
		}
	} else {
		return getSucursal();
	}
}

function getCurrentLang($lang	= false){
	if($lang == false){
		if(isset($_SESSION["current.lang"])){
			$lang			= $_SESSION["current.lang"];
		} else {
			$lang			= (defined("SAFE_LANG")) ? SAFE_LANG : "es";
		}		
	}
	$_SESSION["current.lang"] = $lang;
	return $lang;
}

define ("MQL_STRING", "string");
define ("MQL_INT", "int");
define ("MQL_FLOAT", "float");
define ("MQL_BOOL", "boolean");
define ("MQL_RAW", "raw");
define ("MQL_DATE", "date");
define ("MQL_ARR_INT", "array.int");

define ("MQL_ADD", "insert");
define ("MQL_MOD", "update");
define ("MQL_DEL", "delete");
define ("MQL_LOAD", "load");
define ("MQL_TEST", "mql.test");

define ("MQL_USER", USR_DB);
define ("MQL_PASS", PWD_DB);
define ("MQL_SERVER", WORK_HOST);
define ("MQL_DB", MY_DB_IN);

//TABLAS
define("TCAPTACION_CUENTAS", "captacion_cuentas");

define("TPERSONAS_GENERALES", "socios_general");
define("TPERSONAS_FIGURA_JURIDICA", "socios_figura_juridica");
define("TPERSONAS_TIPO_PATRIMONIO", "socios_patrimoniotipo");
define("TPERSONAS_PATRIMONIO", "socios_patrimonio");
define("TPERSONAS_RELACIONES", "socios_relaciones");
define("TPERSONAS_DIRECCIONES", "socios_vivienda");
define("TPERSONAS_DIRECCIONES_TIPO", "socios_viviendatipo");
define("TPERSONAS_DIRECCIONES_REG", "socios_regimenvivienda");

define("TPERSONAS_ACTIVIDAD_ECONOMICA", "socios_aeconomica");
define("TPERSONAS_PERFIL_TRANSACCIONAL", "personas_perfil_transaccional");
define("TPERSONAS_MEMOS", "socios_memo");

define("TPERSONAS_OPARAMS", "socios_otros_parametros");

define("TVISTA_OFICIALES", "oficiales");

define("TCATALOGOS_MUNICIPIOS", "general_municipios");
define("TCATALOGOS_ESTADOS", "general_estados");
define("TCATALOGOS_COLONIAS", "general_colonias");
define("TCATALOGOS_LOCALIDADES", "catalogos_localidades");
define("TCATALOGOS_ACTIVIDADES_ECONOMICAS", "personas_actividad_economica_tipos");
define("TCATALOGOS_USUARIOS_ROLES", "general_niveles");
define("TCATALOGOS_ENTIDAD_SUCURSALES", "general_sucursales");
define("TCATALOGOS_EMPRESAS", "socios_aeconomica_dependencias");
define("TCATALOGOS_PAISES", "personas_domicilios_paises");
define("TCATALOGOS_RELACIONES", "socios_relacionestipos");
define("TCATALOGOS_GRADO_RIESGO", "aml_risk_levels");

define("TCREDITOS_PRODUCTOS", "creditos_tipoconvenio");
define("TCREDITOS_MONTOS", "creditos_montos");
define("TCREDITOS_PRODUCTOS_OTROS_PARAMETROS", "creditos_productos_otros_parametros");
define("TCREDITOS_OTROS_DATOS", "creditos_otros_datos");
define("TCREDITOS_REGISTRO", "creditos_solicitud");
define("TCREDITOS_DESTINO_DETALLE", "creditos_destino_detallado");

define("TBANCOS_ENTIDADES", "bancos_entidades");
define("TBANCOS_CUENTAS", "bancos_cuentas");
define("TBANCOS_OPERACIONES", "bancos_operaciones");

define("TOPERACIONES_RECIBOS", "operaciones_recibos");
define("TOPERACIONES_MVTOS", "operaciones_mvtos");
define("TOPERACIONES_TIPOS", "operaciones_tipos");
define("TOPERACIONES_RECIBOSTIPOS", "operaciones_recibostipo");

define("TTESORERIA_MVTOS", "tesoreria_cajas_movimientos");
define("TTESORERIA_TIPOS_DE_PAGO", "tesoreria_tipos_de_pago");


define("TAML_PERFIL_RIESGO", "aml_riesgo_perfiles");
define("TAML_REGISTRO_DE_RIESGOS", "aml_risk_register");
define("TAML_NIVEL_DE_RIESGOS", "aml_risk_levels");
define("TAML_REGISTRO_DE_ALERTAS", "aml_alerts");
define("TAML_PERSONAS_OMITIDAS", "aml_personas_descartadas");

define("TSYSTEM_LOG", "general_log");
define("TSEGUMIENTO_LLAMADAS", "seguimiento_llamadas");

define("TUSUARIOS_NOTAS", "usuarios_web_notas");
define("TUSUARIOS_REGISTRO", "t_03f996214fba4a1d05a68b18fece8e71");



//if(defined("MODO_DEBUG")){
	//if(MODO_DEBUG == true){
		//if(SYSTEM_ON_HOSTING == true){
			// ini_set("error_log", SYS_LOG_FILE);
			// ini_set("log_errors", "On");
			// ini_set("display_errors", "Off");
			// ini_set("track_errors", "On");
//		}
//	}
//}
class cUsar {
	function getUsarCache(){
		$CACHE_ERRS		= 0;
		$cnx			= null;
		$idx			= "cache.error.count";
		if(isset($_SESSION)){
			$CACHE_ERRS	= ( isset($_SESSION[$idx]) ) ? $_SESSION[$idx] : 0;
			if (!class_exists('Memcache')) {
				$CACHE_ERRS++;
			}
		}
		
		if($CACHE_ERRS <= 0){
			if(isset($GLOBALS["cnx.memcache"])){
				$cnx		= $GLOBALS["cnx.memcache"];
			} else {
				$cnx		= new Memcache();
				if(!$cnx->pconnect('127.0.0.1', 11211)){
					$cnx	= null;
					$CACHE_ERRS++;
					if(isset($_SESSION)){
						$_SESSION[$idx]	= $CACHE_ERRS;
					}
				} else {
					$CACHE_ERRS	= 0;
					//syslog(E_ERROR, "Cache Activo!");
					if(isset($_SESSION)){
						$_SESSION[$idx]	= 0;
					}
					$GLOBALS["cnx.memcache"]	= $cnx;
				}
				
			}
		}
		return $cnx;
	}
	function getUsarFTP(){
		$CACHE_ERRS		= 0;
		$idx			= "ftp.use.server";
		if(isset($_SESSION)){
			if(isset($_SESSION[$idx])){
				if( (int) $_SESSION[$idx] == 1 ){
					return true;
				} else {
					return false;
				}
			}
			if (!function_exists("ftp_connect")){
				$CACHE_ERRS++;
			} else {
				if(SYS_FTP_PWD == "" AND SYS_FTP_USER == ""){
					$CACHE_ERRS++;
				} else {
					if(!ftp_connect(SYS_FTP_SERVER)){
						$CACHE_ERRS++;
					} else {
						$conn_id 		= ftp_connect(SYS_FTP_SERVER);
						if(@ftp_login($conn_id, SYS_FTP_USER, SYS_FTP_PWD)){
							
						} else {
							$CACHE_ERRS++;
						}
					}
				}
			}
			if($CACHE_ERRS >0){
				$_SESSION[$idx] = "0";
				return false;
			} else {
				$_SESSION[$idx] = "1";
			}
		}
		return true;
	}
}

function getClaveCifradoTemporal(){
	$clave = null;
	$ip1 	= ( isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : "";
	$ip2 	= ( isset($_SERVER['HTTP_VIA']) ) ? $_SERVER['HTTP_VIA'] : "";
	$ip3 	= ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) ) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : "";
	$ips	= ( isset($_SERVER['SERVER_ADDR'])) ? $_SERVER['SERVER_ADDR'] : "";
	$ipc	= trim($ip1.$ip2.$ip3);
	if($ipc == ""){
		
	} else {
		$ips	= trim($ipc.$ips) . date("YmdG");
		//syslog(LOG_DEBUG, $ips);
		$clave	= hash("sha256", $ips);
		/*if(function_exists("password_hash")){
			$clave		= password_hash($ips, PASSWORD_DEFAULT);
		} else {
			if(function_exists("crypt")){
				$clave	= crypt($ips);
			} else {
				$clave	= md5($ips);
			}
		}*/
	}
	return $clave;
}
function getUsarCache(){
	$CACHE_ERRS		= 0;
	$cnx			= null;
	$idx			= "cache.error.count";
	if(isset($_SESSION)){
		$CACHE_ERRS	= ( isset($_SESSION[$idx]) ) ? $_SESSION[$idx] : 0;
		if (!class_exists('Memcache')) {
			$CACHE_ERRS++;
		}
	}
	
	if($CACHE_ERRS <= 0){
		if(isset($GLOBALS["cnx.memcache"])){
			$cnx		= $GLOBALS["cnx.memcache"];
		} else {
			$cnx		= new Memcache();
			if(!$cnx->pconnect('127.0.0.1', 11211)){
				$cnx	= null;
				$CACHE_ERRS++;
				if(isset($_SESSION)){
					$_SESSION[$idx]	= $CACHE_ERRS;
				}
			} else {
				$CACHE_ERRS	= 0;
				//syslog(E_ERROR, "Cache Activo!");
				if(isset($_SESSION)){
					$_SESSION[$idx]	= 0;
				}
				$GLOBALS["cnx.memcache"]	= $cnx;
			}
			
		}
	}
	return $cnx;
}

class cCache {
	private $mCacheEnable	= true;
	private $mErrors		= 0;
	public $EXPIRA_UNHORA	= 3600;
	public $EXPIRA_5MIN		= 300;
	public $EXPIRA_MEDHORA	= 1800;
	public $EXPIRA_UNDIA	= 86400;
	public $EXPIRA_MEDDIA	= 43200;
	private $mCnn			= null;
	private $mGID_Errors	= "cache.error.count";

	function __construct(){
		//$this->mCacheEnable		=	$this->setInSession();
	}
	function set($clave, $v1, $expira = 180){
		$res	= false;
		
		if($this->isReady() == true){
			$clave	= MY_DB_IN . "-" . $clave;
			$res	= $this->cnn()->set($clave, $v1, false, $expira);
		}
		$v1			= null;
		return $res;
	}
	function get($clave){
		$val	= null;
		
		if($this->isReady() == true){
			$clave	= MY_DB_IN . "-" . $clave;
			$val	= $this->cnn()->get($clave);
			$val	= ($val === false) ? null : $val;
			//if($val === null){} else { syslog(E_NOTICE, "Usar cache en $clave"); }
		} else {
			//syslog(E_NOTICE, "NO Usar cache en $clave");
		}
		return $val;
	}
	function cnn(){
		$this->mCnn	= getUsarCache();
		if($this->mCnn === null){
			$this->mCacheEnable	= false;
			$this->mErrors++;
		}
		return $this->mCnn;
	}
	function clean($id = false){

		if($this->isReady() == true){
			$cnn	= $this->cnn();
			if($id === false){
				$cnn->flush(); 
			} else {
				$id	= MY_DB_IN . "-" . $id;
				$cnn->delete($id);
			}
		}
	}
	function isReady(){
		if(SAFE_USE_MCACHE == false){
			//syslog(E_NOTICE, "NO Usar Cache por sistema");
			return false;
		}
		
		return ($this->getErrorsCount() >= 1) ? false : true;
	}
	private function getErrorsCount(){
		if(isset($_SESSION)){
			if(isset($_SESSION[$this->mGID_Errors])){
				$this->mErrors = $_SESSION[$this->mGID_Errors];
				//syslog(E_NOTICE, "Lo errores en Session son # " . $this->mErrors);
			} else {
				$_SESSION[$this->mGID_Errors]	= 0;
			}
			//else { 
				//$this->mErrors = 1;
				//syslog(E_NOTICE, "MdA-II ::: " . $this->mErrors);
				//syslog(E_NOTICE, "No hay session, el error es # " . $this->mErrors);
			//}
		} else {
			if(is_null($this->cnn())){
				$this->mErrors = 1;
				//syslog(E_NOTICE, "Los errores son generados por la conexion # " . $this->mErrors);
			}
		}
		//syslog(E_NOTICE, "Los errores son # " . $this->mErrors);
		return $this->mErrors;
	}
}
function url_exists($url) {
	$url	= $url . "/inicio.php";
	
	//check if URL is valid
	if(!filter_var($url, FILTER_VALIDATE_URL)){
		return false;
	}
	
	$agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, $agent);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch,CURLOPT_VERBOSE, FALSE);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	
	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, FALSE);
	//curl_setopt($ch,CURLOPT_SSLVERSION, 3);
	curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, FALSE);
	//curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'SSLv3');
	
	$page=curl_exec($ch);
	//echo curl_error($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if ($httpcode >= 200 && $httpcode < 300)
		return true;
		else
			return false;
}

function get_real_ip()
{
	
	if (isset($_SERVER["HTTP_CLIENT_IP"]))
	{
		return $_SERVER["HTTP_CLIENT_IP"];
	}
	elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
	{
		return $_SERVER["HTTP_X_FORWARDED_FOR"];
	}
	elseif (isset($_SERVER["HTTP_X_FORWARDED"]))
	{
		return $_SERVER["HTTP_X_FORWARDED"];
	}
	elseif (isset($_SERVER["HTTP_FORWARDED_FOR"]))
	{
		return $_SERVER["HTTP_FORWARDED_FOR"];
	}
	elseif (isset($_SERVER["HTTP_FORWARDED"]))
	{
		return $_SERVER["HTTP_FORWARDED"];
	}
	else
	{
		return $_SERVER["REMOTE_ADDR"];
	}
}
function getSafeHost(){
	$host		= SAFE_HOST_URL;
	if(url_exists($host) == false){
		$subh		= (strpos($host, "https") === false) ? substr($host, 0, 7) : substr($host, 0, 8);
		$host		= $subh . $_SERVER["SERVER_ADDR"] . "/";
		if(url_exists($host) == false){
			$host	= $subh . "127.0.0.1/";
		}
	}
	return $host;
}
?>