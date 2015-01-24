<?php
include_once ("core.config.inc.php");
include_once ("entidad.datos.php");
include_once ("core.error.inc.php");
include_once ("core.common.inc.php");
include_once ("core.db.inc.php");

include_once ("core.sys.inc.php");

class cLang {
	
	private $mWords		= array(); 
	private $onDB		= true;
	private $mCurrLang	= "es";
	 
	
	function __construct(){
		$this->mCurrLang	= getCurrentLang();
	}
	
	function getSpanish($item){
		$lang							= array();
		$lang["INGRESOS_VARIOS"]		= "Ingresos Varios";
		$lang["DEUDOR"]					= "Deudor";
		$lang["CLAVE_DE_PERSONA"]		= "Cod. de Persona";
		$lang["CLAVE_DE_CREDITO"]		= "Cod. de Credito";
		$lang["CLAVE_INTERNA"]			= "Cod. Interno";
		$lang["CLAVE_DE_CUENTA"]		= "Cod. de Cuenta";
		$lang["CAJA"]					= "Caja";
		$lang["TIPO_DE_OPERACION"]		= "Tipo de Operacion";
		$lang["OFICIAL_DE_CREDITO"]		= "Oficial de Credito";
		$lang["DESCRIPCION"]			= "Descripcion";
		$lang["DOCUMENTO"]				= "Documento";
		$lang["DOCUMENTOS"]				= "Documentos";
		$lang["SUBPRODUCTO"]			= "Subproducto";
		
		$lang["CUENTAS_DE_INVERSION"]	= "Cuentas de Inversion";
		$lang["PERSONAS"]				= "Personas";
		$lang["CATALOGO"]				= "Catalogo";
		$lang["AVALES"]					= "Avales";
		$lang["INGRESO"]				= "Ingreso";
		$lang["JURIDICA"]				= "Juridica";
		$lang["FIGURA"]					= "Figura";
		$lang["TIPO"]					= "Tipo";
		$lang["ESTADO"]					= "Estado";
		$lang["ESTADO_CIVIL"]			= "Estado Civil";
		$lang["CIVIL"]					= "Civil";
		$lang["ENTIDAD"]				= "Entidad";
		$lang["PERFIL"]					= "Perfil";
		$lang["TRANSACCIONAL"]			= "Transaccional";
		
		$lang["BANCARIAS"]				= "Bancarias";
		$lang["BANCARIA"]				= "Bancaria";
		$lang["EXPOSICION"]				= "Expocision";
		$lang["COBROS"]					= "Cobros";
		$lang["TRANSFERENCIA"]			= "Transferencia";
		$lang["OBSERVACION"]			= "Observacion";
		
		$lang["NUMERO_EXTERIOR"]		= "Numero Exterior";
		$lang["NUMERO_INTERIOR"]		= "Numero Interior";
		$lang["ENTIDAD_FEDERATIVA"]		= "Estado";
		$lang["MUNICIPIO"]				= "Municpio";
		$lang["LOCALIDAD"]				= "Localidad";
		$lang["COLONIA"]				= "Colonia";
		$lang["CODIGO_POSTAL"]			= "Codigo Postal";
		$lang["EN"]			= "en";
		
		$lang["RENEGOCIAR"]				= "Renegociar";
		$lang["DIAS"]					= "Dias";
		$lang["AVISO"]					= "Aviso";
		$lang["CONTACTO"]				= "Contacto";
		$lang["CODIGO"]					= "Codigo";
		$lang["TEXTO"]					= "Texto";
		$lang["INTERNA"]				= "Interna";
		$lang["CLAVE"]					= "Clave";
		$lang["CONSANGUINIDAD"]			= "Consanguinidad";
		$lang["PRINCIPAL"]				= "Principal";
		$lang["ALERTAS"]				= "Alertas";
		
		$lang["PERSONA"]				= "Persona";
		$lang["NOMBRE_COMPLETO"]		= "Nombre Completo";
		$lang["NOMBRE"]					= "Nombre";
		$lang["COMPLETO"]				= "Completo";
		$lang["APELLIDO_PATERNO"]		= "Primer Apellido";
		$lang["APELLIDO_MATERNO"]		= "Segundo Apellido";

		$lang["MOROSO"]					= "Moroso";
		$lang["VENCIDO"]				= "Vencido";
		
		$lang["DEPENDIENTE_ECONOMICO"]	= "Dependiente Economico";
		
		$lang["PRIMER"]					= "Primer";
		$lang["SEGUNDO"]				= "Segundo";
		$lang["APELLIDO"]				= "Apellido";
		$lang["NACIMIENTO"]				= "Nacimiento";
		$lang["RELACION"]				= "Relacion";
		$lang["EXHIBIDO"]				= "Exhibido";
		$lang["GASTOS"]				= "Gastos";
		$lang["PARENTESCO"]				= "Parentesco";
		$lang["DIRECTO"]				= "Directo";
		$lang["OCUPACION"]				= "Ocupacion";
		
		$lang["CUENTAS"]				= "Cuentas";
		$lang["INVERSION"]				= "Inversion";
		$lang["A_LA_VISTA"]				= "A La Vista";
		$lang["DEPOSITOS"]				= "Depositos";
		$lang["RETIROS"]				= "Retiros";
		$lang["CONTROL"]				= "Control";
		$lang["CALIFICADO"]				= "Calificado";
		$lang["PONDERADO"]				= "Ponderado";
		$lang["PONDERADAS"]				= "Ponderadas";
		$lang["UNIDADES"]				= "Unidades";
		$lang["UNIDAD"]					= "Unidad";
		$lang["MEDIDA"]					= "Medida";
		$lang["FUNDAMENTO"]				= "Fundamento";
		$lang["FRECUENCIA"]				= "Frecuencia";
		$lang["LEGAL"]					= "Legal";
		
		$lang["HISTORIAL_DE_PERSONA"]		= "Historial de Persona";
		$lang["RELACION_PATRIMONIAL"]		= "Relacion Patrimonial";
		$lang["ACTIVIDAD_ECONOMICA"]		= "Actividad Economica";
		$lang["ACTIVIDAD"]					= "Actividad";
		$lang["ECONOMICA"]					= "Economica";
		$lang["REFERENCIAS_DOMICILIARIAS"]	= "Referencias Domiciliarias";
		$lang["CONDUCTA_INADECUADA"]		= "Conducta Inadecuada";
		
		$lang["TIEMPO"]						= "Tiempo";
		$lang["RESIDENCIA"]					= "Residencia";
		$lang["TIEMPO_DE_RESIDENCIA"]		= "Tiempo de Residencia";
		
		$lang["VIVIENDA"]					= "Vivienda";
		$lang["REGIMEN"]					= "Regimen";
		$lang["DEPARTAMENTO"]				= "Departamento";
		$lang["TELEFONO"]					= "Telefono";
		$lang["TELEFONO_FIJO"]					= "Telefono Fijo";
		$lang["TELEFONO_MOVIL"]					= "Telefono Movil";
		
		$lang["LOCALIDAD"]					= "Localidad";
		$lang["DOCUMENTO_DE_EXISTENCIA"]	= "Acta de Nacimimiento/Constitutiva";
		$lang["IDENTIFICACION_OFICIAL"]		= "Identificacion Oficial";
		$lang["IDENTIFICACION_POBLACIONAL"]	= "C.U.R.P";
		$lang["IDENTIFICACION_FISCAL"]		= "R.F.C.";
		$lang["CORREO_ELECTRONICO"]			= "Correo Electronico";

		
		$lang["COMPROBANTE_DE_DOMICILIO"]	= "Comprobante de Domicilio";
		$lang["OTROS_DOCUMENTOS"]			= "Otros Documentos";
		$lang["FIRMA_RECABADA"]				= "Firma recabada";
		$lang["CUENTA_BANCARIA"]			= "Cuenta Bancaria";
		$lang["EDICION_AVANZADA"]			= "Edicion Avanzada";
		$lang["PLAN_DE_PAGOS"]				= "Plan de Pagos";
		$lang["FLUJO_DE_EFECTIVO"]			= "Flujo de efectivo";
		$lang["SOLICITUD_DE_CREDITO"]		= "Solicitud de Credito";
				
		$lang["RECIBO"]						= "Recibo";
		$lang["SUCURSAL"]				= "Sucursal";
		$lang["REGISTRO"]				= "Registro";
		$lang["DATOS"]					= "Datos";
		$lang["ACCIONES"]				= "Acciones";
		$lang["NOTIFICACIONES"]			= "Notificaciones";
		$lang["NOTAS"]					= "Notas";
		$lang["LLAMADAS"]				= "Llamadas";
		$lang["CREDITO"]				= "Credito";
		$lang["CAPITAL"]				= "Capital";
		$lang["INTERES"]				= "Interes";
		$lang["REALES"]					= "Reales";
		$lang["PUNTO"]					= "Punto";
		$lang["ACCESO"]					= "Acceso";
		$lang["IMPORTAR"]				= "Importar";
		
		$lang["EMPRESA"]				= "Empresa";
		$lang["LA_EMPRESA"]				= "La Empresa";
		$lang["MINISTRACION"]			= "Ministracion";
		$lang["MINISTRADO"]				= "Ministrado";
		$lang["VENCIMIENTO"]			= "Vencimiento";
		
		$lang["INGRESO"]				= "Ingreso";
		$lang["EGRESO"]					= "Egreso";
		
		$lang["INGRESOS"]				= "Ingresos";
		$lang["EGRESOS"]					= "Egresos";

		$lang["NORMAL"]					= "Normal";
		$lang["MORATORIO"]				= "Moratorio";
		$lang["BANCO"]					= "Banco";
				
		$lang["ORDEN"]					= "Orden";
		$lang["RECARGAR"]				= "Recargar";
		
		$lang["PAGARE"]					= "Pagare";
		$lang["CONTRATO"]				= "Contrato";
		$lang["PATRIMONIO"]				= "Patrimonio";
		$lang["SOLICITUD"]				= "Solicitud";
		$lang["SOLICITANTE"]			= "Solicitante";
		$lang["SOLICITADO"]			= "Solicitado";
		
		$lang["ASIGNAR"]				= "Asignar";
		$lang["OBTENER"]				= "Obtener";
		$lang["ESTATUS"]				= "Estado";
		$lang["ESTADO"]					= "Estado";
		$lang["FECHA_INICIAL"]			= "Fecha Inicial";
		$lang["FECHA_FINAL"]			= "Fecha Final";
		
		
		$lang["AUTORIZADO"]				= "Autorizado";
		$lang["NO_AUTORIZADO"]			= "No Autorizado";
		$lang["MARCAR_COMO"]			= "Marcar como";
		$lang["DEPOSITO"]				= "Deposito";
		$lang["RETIRO"]					= "Retiro";
		$lang["RIESGO"]					= "Riesgo";
		$lang["PAGO"]					= "Pago";
		$lang["PAGOS"]					= "Pagos";
		$lang["DOMICILIO"]				= "Domicilio";
		
		$lang["LISTADO"]				= "Listado";
		$lang["CREDITOS"]				= "Creditos";
		
		$lang["IMPORTACION"]			= "Importacion";
		$lang["PAIS"]					= "Pais";
		
		$lang["VALOR"]					= "Valor";
		$lang["CAMBIO"]					= "Cambio";
		$lang["OTROS"]					= "Otros";
		
		$lang["ORIGEN"]					= "Origen";
		$lang["FLUJO"]					= "Flujo";
		$lang["NIVEL"]					= "Nivel";
		
		$lang["CEDULA"]					= "Cedula";
		$lang["RETENCION"]				= "Retencion";
		$lang["INCIDENCIAS"]			= "Incidencias";
		$lang["DESCUENTO"]				= "Descuento";
		
		$lang["CARGA"]				= "Carga";
		$lang["MASIVA"]				= "Masiva";
		$lang["ARCHIVO"]				= "Archivo";
		
		$lang["ES"]						= "es";
		$lang["DE"]						= "De";
		$lang["DEL"]					= "Del";
		$lang["POR"]					= "por";
		
		$lang["DESTINO"]				= "Destino";
		$lang["UTILERIAS"]				= "Utilerias";
		$lang["SISTEMA"]				= "Sistema";
		$lang["BANCOS"]					= "Bancos";
		$lang["CUENTA"]					= "Cuenta";
		$lang["RELACIONADA"]			= "Relacionada";
		
		$lang["CHEQUE"]					= "Cheque";
		$lang["EFECTIVO"]				= "Efectivo";
		$lang["DEPOSITO"]				= "Deposito/Transferencia";
		$lang["NINGUNO"]				= "Ninguno";
		$lang["RECIBIDO"]				= "Recibido";
		$lang["IMPORTE"]				= "Importe";
		$lang["MONEDA"]					= "Moneda";
		$lang["MONTO"]					= "Monto";
		$lang["PERIOCIDAD"]				= "Periocidad";
		$lang["PRODUCTO"]				= "Producto";
		$lang["INTERESES"]				= "Intereses";
		$lang["POLIZA"]					= "Poliza";
		$lang["REPORTE"]				= "Reporte";
		$lang["PARCIALIDAD"]			= "Parcialidad";
		$lang["PARCIALIDADES"]			= "Parcialidades";
		$lang["PENDIENTES"]			= "Pendientes";
		
		$lang["TESORERIA"]				= "Tesoreria";
		$lang["FORMA"]					= "Forma";
		$lang["FORMATO"]				= "Formato";
		
		$lang["COBRO_EN"]				= "Cobro en";
		$lang["AHORRO"]					= "Ahorro";
		$lang["REAL"]					= "Real";
		$lang["MAXIMO"]					= "Maximo";
		$lang["MENSUALES"]					= "Mensuales";
		$lang["MENSUAL"]					= "Mensual";
		$lang["MES"]					= "Mes";
		$lang["DIA"]					= "Dia";
		
		$lang["NUMERO"]					= "Numero";
		
		$lang["CANCELADO"]				= "Cancelado";
		$lang["OPERACION"]				= "Operacion";
		$lang["OPERACIONES"]			= "Operaciones";
		$lang["PAGINA"]					= "Pagina";
		$lang["ACEPTAR"]				= "Aceptar";
		$lang["GENERAR"]				= "Generar";
		$lang["REFRESCAR"]				= "Refrescar";
		$lang["AJUSTAR"]				= "Ajustar";
		
		$lang["ORDEN_DE_DESEMBOLSO"]	= "Orden de Desembolso";
		
		$lang["IR_AL_INICIO"]			= "Ir al inicio";
		$lang["IMPRIMIR"]				= "Imprimir";
		$lang["REIMPRIMIR"]				= "Re-Imprimir";
		$lang["GUARDAR"]				= "Guardar";
		$lang["GUARDAR_COMO"]			= "Guardar como";
		$lang["BUSCAR"]					= "Buscar";
		$lang["AGREGAR"]				= "Agregar";
		$lang["ACTUALIZAR"]				= "Actualizar";
		$lang["REGRESAR"]				= "Regresar";
		$lang["MODIFICAR"]				= "Modificar";
		$lang["EDITAR"]					= "Modificar";
		
		$lang["COMPROMISO"]				= "Compromiso";
		$lang["HORA"]					= "Hora";
		
		$lang["ELIMINAR"]				= "Eliminar";
		$lang["CAMBIAR"]				= "Cambiar";
		$lang["REESTRUCTURAR"]			= "Reestructurar";
		$lang["RENOVAR"]				= "Renovar";
		$lang["CANCELAR"]				= "Cancelar";
		$lang["NUEVA"]					= "Nueva";		
		
		$lang["MOTIVOS"]				= "Motivos";
		$lang["RELACIONADO"]			= "Relacionado";
		$lang["VALIDACION"]				= "Validacion";
		$lang["IDENTIFICACION"]				= "Identificacion";
		$lang["FECHA"]					= "Fecha";
		$lang["PERMANENTE"]					= "Permanente";
		
		$lang["EJECUTAR"]				= "Ejecutar";
		$lang["DESCARGAR"]				= "Descargar";
		$lang["SALIR"]					= "Salir";
		$lang["CONFIRME"]				= "Confirme";
		$lang["REPORTAR"]				= "Reportar";
		$lang["REPORTADO"]				= "Reportado";
		$lang["BORRADO"]				= "Borrado";
		$lang["REPORTES_DE"]			= "Reportes de";
		$lang["CATALOGO_DE"]			= "Catalogo de";
		$lang["NUMERO_DE"]				= "Numero de";
		$lang["MENSAJE"]				= "Mensaje";
		$lang["OBSERVACIONES"]			= "Observaciones";
		$lang["TOTAL"]					= "Total";
		$lang["SALDO"]					= "Saldo";
		$lang["SUMAS"]					= "Sumas";
		
		$lang["PROPIETARIO"]			= "Propietario";
		$lang["CAJERO"]					= "Cajero/a";
		$lang["ELABORA"]				= "Elabora";
		
		$lang["TIPO_DE"]				= "Tipo de";
		$lang["FORMA_DE"]				= "Forma de";
		$lang["MONTO_DE"]				= "Monto de";
		$lang["NOMBRE_DE"]				= "Nombre de";
		$lang["FECHA_DE"]				= "Fecha de";
		$lang["FIRMA_DEL"]				= "Firma del";
		$lang["CLAVE_DE"]				= "Clave de";
		$lang["CONTRATO_DE"]			= "Contrato de";

		$lang["POR_LA"]					= "Por la";
		$lang["VINCULAR_A"]				= "Vincular a";
		
		$lang["PANEL_DE_CONTROL_DE"]	= "Panel de Control de";
		$lang["VALIDACION_DE"]			= "Validacion de";
		$lang["IMPUESTO_AL_CONSUMO"]	= "I.V.A.";
		
		$lang["GARANTIAS"]				= "Garantias";
		$lang["MANDATO"]				= "Mandato";
		$lang["CAPITAL_SOCIAL"]			= "Capital Social";
		$lang["NOMBRES"]			= $lang["NOMBRE_COMPLETO"];
		//$lang["MANDATO"]				= "Mandato";

		$lang["AVALES"]					= "Avales";
		$lang["PUESTO"]					= "Puesto/Cargo";
		$lang["ROL"]					= "Rol/Cargo";
		$lang["USUARIO"]				= "Usuario";
		$lang["EVENTO"]					= "Evento";
		$lang["DESPEDIDO"]				= "Despedido";
		$lang["DESVINCULADO"]			= "Desvinculado";
		$lang["PASSWORD"]				= "Contraseña";
		
		$lang["AHORROS"]				= "Ahorros";
		$lang["ASOCIACION"]				= "Asociacion";
		$lang["CUMPLIMIENTO"]			= "Cumplimiento";
		$lang["VALIDAR"]				= "Validar";
		
		$lang["FALSO"]					= "Falso";
		$lang["REAL"]					= "Real";
		$lang["GENERO"]					= "Genero";
		$lang["INSUFICIENTE"]			= "Informacion Insuficiente";
		
		$lang["RESPALDO"]					= "Respaldo";
		$lang["TAREAS"]					= "Tareas";
		$lang["CORTE"]					= "Corte";
		$lang["BENEFICIARIOS"]			= "beneficiario";
		$lang["AFECTADO"]			= "Afectado";
		$lang["OPERADO"]			= "Operado";
		$lang["LISTA"]			= "Lista";
		$lang["RIESGOS"]			= "Riesgos";
		$lang["CONFIRMADOS"]			= "Confirmado";
		$lang["ESTADO_DE_CUENTA"]		= "Estado de Cuenta";
		$lang["CERRAR"]					= "Cerrar";
		$lang["COBRANZA"]				= "Cobranza";
		
		$lang[MSG_ERROR_SAVE]			= "Error al intentar guardar.";
		$lang[MSG_NO_DATA]			= "NO EXISTEN DATOS VALIDOS";
		$lang[MSG_READY_SAVE]			= "Cambios Guardados.";
		$lang[MSG_CONFIRM_SAVE]		= "Esta seguro de Guardar Cambios?";
		$lang["MSG_NECESITA_CAPTURAR_CANTIDAD"]			= "Necesita Capturar la cantidad correcta";
		$lang["MSG_CANTIDAD_NO_MENOR_A"]			= "Necesita Capturar la cantidad correcta";
		
		//Alias
		$lang["RFC"]						= $lang["IDENTIFICACION_FISCAL"];
		$lang["CURP"]						= $lang["IDENTIFICACION_POBLACIONAL"];
		$lang["NUM"]						= $lang["NUMERO"];
		$lang["COD"]						= $lang["CODIGO"];
		$lang["IVA"]						= $lang["IMPUESTO_AL_CONSUMO"];
		$lang["TELEFONOS"]					= $lang["TELEFONO"];
		$lang["EXPIRACION"]					= $lang["VENCIMIENTO"];
		$lang["ANOTACION"]					= $lang["NOTAS"];
		$lang["PRESENTADO"]					= $lang["EXHIBIDO"];
		$lang["CONVENIO"]					= $lang["PRODUCTO"];
		$lang["OFICIAL"]					= $lang["OFICIAL_DE_CREDITO"];
		$lang["OFICIALES_DE_CREDITO"]		= $lang["OFICIAL_DE_CREDITO"];
		$lang["REPORTES"]					= $lang["REPORTE"];
		$lang["PERFIL_TRANSACCIONAL"]		= $lang["PERFIL"] . " ". $lang["TRANSACCIONAL"];
		$lang["LETRA"]						= $lang["PARCIALIDAD"];
		$lang["PERIODO"]					= $lang["PARCIALIDAD"];
		$lang["OTORGADO"]					= $lang["MINISTRADO"];
		$lang["LETRAS"]						= $lang["PARCIALIDADES"];
		$lang["NUMERO_DE_CUENTA"]			= $lang["CLAVE_DE_CUENTA"];
		
		$lang["PRIMER_APELLIDO"]			= $lang["APELLIDO_PATERNO"];
		$lang["SEGUNDO_APELLIDO"]			= $lang["APELLIDO_MATERNO"];
		$lang["CLAVE_POBLACIONAL"]			= $lang["CURP"];
		$lang["SOCIO"]						= $lang["PERSONA"];
		$lang["DEPENDENCIA"]				= $lang["EMPRESA"];

		$palabra							= "";
		$this->mWords						= $lang;
		if(isset($lang[$item])){
			$palabra	= $lang[$item];
		}
		return $palabra;
	}
	function getEnglish($item){
		
	}
	
	function OCache($key, $value = null){
		$res	= null;
		$key	= trim($key);
		if($key != ""){
			$key	= $this->mCurrLang . ".$key";
			$xCache	= new cCache();
			if($xCache->isReady() == true){
				if($value == null){
					$res	= $xCache->get($key);
				} else {
					$xCache->set($key, $value);
				}
			}
		}

		return $res;
	}
	
	function get($palabra){
		//$palabra	= "";
		$palabra	= strtoupper($palabra);
		$palabra	= str_replace(" ", "_", $palabra);
		if($this->onDB == true){
		
			if( $this->OCache($palabra) == null){
				$sql		= "SELECT * FROM sistema_lenguaje WHERE idioma=\"" . $this->mCurrLang ."\"AND equivalente=\"$palabra\" LIMIT 0,1 ";
				$traduccion	= mifila($sql, "traduccion");
				if(trim($traduccion) == "" OR trim($traduccion) == "0"){
					$palabra					= $this->getSpanish($palabra);
				} else {
					$this->OCache($palabra, $traduccion);
					//setLog("Palabra agregada de :: $traduccion ");
					$palabra					= $traduccion;
				}				

			} else {
				//setLog("Palabra cargada de :: " . $this->OCache($palabra) );
				$palabra	= $this->OCache($palabra);
			}
		} else {
		
			if( $this->mCurrLang == "es"){
				$palabra	= $this->getSpanish($palabra);
			} else {
				$palabra	= $this->getEnglish($palabra);
			}
		}
		$palabra	= cleanString($palabra);
		return $palabra;
	}
	function getTrad($palabra, $palabra2 = ""){
		$wrd		= "";
		if( is_array($palabra )){
			foreach ($palabra as $key => $valor){
				$wrd	.= " " . $this->get($valor);
			}
		} else {
			$wrd		= $this->get($palabra);
			if($palabra2 !== ""){
				$wrd		= "$wrd " . $this->get($palabra2);
			}
		}
		return $wrd;
	}
	function getWords(){
		$wrds		= $this->OCache("listado.palabras.completas");
		if($wrds == null){
			$this->get("");	$wrds = $this->mWords;
			$this->OCache("listado.palabras.completas", 	json_encode($this->mWords));
		} else {
			$wrds	= json_decode($wrds);
		}
		
		return $wrds;
	}
	function getT($strTxt){
		$txt	= trim($strTxt);
		$finder	= substr($strTxt, 0, 3);
		
		if( $finder == "TR."  ){
			$txt	=	"";
			$strTxt	= substr($strTxt, 3);
			$strTxt	= trim($this->clearSimilares($strTxt));
			
			if(strpos($strTxt, " ") !== false){
			$xword	= explode(" ", $strTxt );
				foreach ($xword as $p => $key){
					$wrd	= trim($key);
					$test	= "/[^a-zA-Z_]/"; //$wrd == ""
					if( preg_match($test, $wrd) ){
						$txt	.= ($wrd == "") ? "" : $wrd . " ";
					} else {
						$wrd	= $this->getTrad($wrd);
						if(MODO_DEBUG == true){
							$wrd	= ($wrd == "") ?  ucfirst("[$key] ") : "$wrd ";
						} else {
							$wrd	= ($wrd == "") ?  ucfirst("$key ") : "$wrd ";
						}
						$txt	.= $wrd;
					}
				}
			} else {
				$txt	= $this->getTrad($strTxt);
			}
		
		}
		return trim($txt);
	}
	function clearSimilares($text){
		$text	= strtoupper($text);
		$text	= str_replace("APELLIDO MATERNO", "APELLIDO_MATERNO", $text);
		$text	= str_replace("APELLIDO PATERNO", "APELLIDO_PATERNO", $text);
		$text	= str_replace("NOMBRE COMPLETO", "NOMBRE_COMPLETO", $text);
		$text	= str_replace("CODIGO POSTAL", "CODIGO_POSTAL", $text);
		$text	= str_replace("REGIMEN FISCAL", "REGIMEN_FISCAL", $text);
		$text	= str_replace("TIPO DE OPERACION", "TIPO_DE_OPERACION", $text);
		$text	= str_replace("TIEMPO DE RESIDENCIA", "TIEMPO_DE_RESIDENCIA", $text);
		$text	= str_replace("SOLICITUD DE CREDITO", "SOLICITUD_DE_CREDITO", $text);
		$text	= str_replace("ESTADO DE CUENTA", "ESTADO_DE_CUENTA", $text);
		$text	= str_replace("COMISION POR APERTURA", "COMISION_POR_APERTURA", $text);
		$text	= str_replace("REFERENCIAS DOMICILIARIAS", "REFERENCIAS_DOMICILIARIAS", $text);
		
		$text	= str_replace("LOS_RECURSOS", "LOS_RECURSOS", $text);
		
		$text	= str_replace("IR AL ", "IR_AL ", $text);
		$text	= str_replace("IR A ", "IR_AL ", $text);
		$text	= str_replace("ENVIAR A", "ENVIAR_A", $text);
		$text	= str_replace("ESTADO CIVIL ACTUAL", "ESTADO_CIVIL", $text);
		$text	= str_replace("CENTRODECOSTOS", "CENTRO_DE_COSTOS", $text);
		$text	= str_replace("CENTRO DE COSTOS", "CENTRO_DE_COSTOS", $text);
		
		$text	= str_replace("POR DEFECTO", "POR_DEFECTO", $text);
		$text	= str_replace("PREDETERMINADO", "POR_DEFECTO", $text);
		
		$text	= str_replace("EMAIL", "CORREO_ELECTRONICO", $text);
		//$text	= str_replace("IDEMPRESAS", "clave de empresa", $text);
		$text	= str_replace("FECHA INICIAL", "FECHA_INICIAL", $text);
		$text	= str_replace("FECHA FINAL", "FECHA_FINAL", $text);
		$text	= str_replace("OTORGAMIENTO", "OTORGACION", $text);
		//$text	= str_replace("CODIGO_POSTAL", "CORREO_ELECTRONICO", $text);
		return $text;
	}
	function toDatabase(){
		$ql		= new cSistema_lenguaje();
		$totrad	= array();
		$palabras	= $this->getWords();
		foreach ($palabras as $eq => $tra){
			$id	= $ql->query()->getLastID();
			$ql->idsistema_lenguaje( $id );
			$ql->equivalente($eq);
			$ql->traduccion($tra);
			$ql->extension('');
			$ql->query()->insert()->save();
			$totrad[$id]	= $tra;
		}
		return json_encode($totrad);
	}
}
/*
 
*/


?>