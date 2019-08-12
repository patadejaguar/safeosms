<?php
include_once ("core.config.inc.php");
include_once ("core.error.inc.php");
include_once ("core.init.inc.php");
include_once ("core.db.inc.php");
//@session_start();



$xC     = new cConfiguration();
//========================================= DATOS DEL SISTEMA ===========================================
define("SYS_FECHA_DE_MIGRACION", 				$xC->get("fecha_de_migracion_al_sistema", "2012-01-01", $xC->SISTEMA) );
//========================================= DATOS GENERALES DE LA ENTIDAD ===========================================
define("EACP_ID_DE_PERSONA", 	    			$xC->get("clave_de_persona_en_sistema", 10000, 	$xC->ENTIDAD));
define("EACP_NAME", 							$xC->get("nombre_de_la_entidad", "", 				$xC->ENTIDAD));
define("EACP_DESCRIPTION",      				$xC->get("descripcion_de_la_entidad", "", 			$xC->ENTIDAD));
define("EACP_PATH_LOGO", 						vIMG_PATH . "/logo.png");
//========================================= DATOS LEGALES DE LA ENTIDAD ===========================================
define("EACP_CLAVE", 							$xC->get("registro_ante_la_cnbv", "", 						$xC->ENTIDAD_LEGAL));
define("EACP_CLAVE_CASFIN", 					$xC->get("registro_casfin", "", 							$xC->ENTIDAD_LEGAL));
define("EACP_RFC", 								$xC->get("rfc_de_la_entidad", "", 							$xC->ENTIDAD_LEGAL));
define("EACP_REGISTRO_PATRONAL",				$xC->get("registro_patronal_imss", "", 						$xC->ENTIDAD_LEGAL));
define("EACP_FECHA_DE_CONSTITUCION", 			$xC->get("fecha_de_constitucion", date("Y-m-d"), 			$xC->ENTIDAD_LEGAL));
define("ENTIDAD_CLAVE_SIC", 					$xC->get("entidad_clave_en_el_sic", "SinRegistro",			$xC->ENTIDAD_LEGAL) ); //Abril-2012 ;
define("ENTIDAD_NOMBRE_SIC", 					$xC->get("entidad_corto_en_el_sic", "FINANCIERA", 			$xC->ENTIDAD_LEGAL) ); //Abril-2012 ;
define("EACP_DOCTO_CONSTITUCION", 				$xC->get("descripcion_del_documento_constitutivo", "", 		$xC->ENTIDAD_LEGAL));
define("EACP_REP_LEGAL", 	    				$xC->get("nombre_del_representante_legal", "", 				$xC->ENTIDAD_LEGAL));
define("EACP_RFC_REP_LEGAL", 	    			$xC->get("rfc_del_representante_legal", "", 				$xC->ENTIDAD_LEGAL));
define("EACP_CURP_REP_LEGAL", 	    			$xC->get("curp_del_representante_legal", "", 				$xC->ENTIDAD_LEGAL));
define("EACP_REGIMEN_FISCAL", 	    			$xC->get("regimen_fiscal_de_la_entidad", "REGIMEN GENERAL DE LEY PERSONAS MORALES", $xC->ENTIDAD_LEGAL));
define("EACP_DOCTO_REP_LEGAL",  				$xC->get("descripcion_del_documento_de_asignacion_del_representante", "", 			$xC->ENTIDAD_LEGAL));
define("EACP_PDTE_VIGILANCIA",  				$xC->get("nombre_del_presidente_del_consejo_de_vigilancia", "",						$xC->ENTIDAD_LEGAL));
//========================================= DATOS RELACIONADOS AL DOMICILIO ===========================================
define("EACP_DOMICILIO_CALLE",					$xC->get("domicilio.calle", "", 							$xC->ENTIDAD_DOMICILIO) );
define("EACP_DOMICILIO_NUM_EXT",				$xC->get("domicilio.numero_exterior", "", 					$xC->ENTIDAD_DOMICILIO));
define("EACP_DOMICILIO_NUM_INT",				$xC->get("domicilio.numero_interior", "", 					$xC->ENTIDAD_DOMICILIO));
define("EACP_DOMICILIO_PAIS",					$xC->get("domicilio.nombre_del_pais", "MEXICO",				$xC->ENTIDAD_DOMICILIO ));
define("EACP_MUNICIPIO", 						$xC->get("domicilio.municipio", "", 						$xC->ENTIDAD_DOMICILIO ));
define("EACP_LOCALIDAD", 						$xC->get("domicilio.localidad", "", 						$xC->ENTIDAD_DOMICILIO ));
define("EACP_COLONIA", 							$xC->get("domicilio.colonia", "", 							$xC->ENTIDAD_DOMICILIO ));
define("EACP_CODIGO_POSTAL", 					$xC->get("domicilio.codigo_postal", "", 					$xC->ENTIDAD_DOMICILIO ));
define("EACP_ESTADO", 							$xC->get("domicilio.estado", "", 							$xC->ENTIDAD_DOMICILIO ));
define("EACP_DOMICILIO_CORTO",  				$xC->get("domicilio.domicilio_integrado", "", 				$xC->ENTIDAD_DOMICILIO ));
define("EACP_HORARIO_DE_TRABAJO", 				$xC->get("horario_general_en_texto", "", 					$xC->OPERACIONES));
define("EJERCICIO_CONTABLE", 					date("Y"));
define("EACP_CLAVE_DE_PAIS", 					$xC->get("domicilio.clave_de_pais", "MX", 					$xC->ENTIDAD_DOMICILIO ));
define("EACP_CLAVE_DE_LOCALIDAD", 				$xC->get("domicilio.clave_de_localidad", "0", 				$xC->ENTIDAD_DOMICILIO ));
define("EACP_CLAVE_DE_MUNICIPIO", 				$xC->get("domicilio.clave_de_municipio", "0", 				$xC->ENTIDAD_DOMICILIO ));
define("EACP_CLAVE_DE_ENTIDAD_SIC", 			$xC->get("domicilio.clave_de_estado_en_sic", "YUC", 		$xC->ENTIDAD_DOMICILIO ));
define("EACP_CLAVE_DE_ENTIDADFED", 				$xC->get("domicilio.clave_alfabetica_del_estado", "YN", 	$xC->ENTIDAD_DOMICILIO) ); //"CC");
define("EACP_CLAVE_NUM_ENTIDADFED", 			$xC->get("domicilio.clave_numerica_del_estado", "31", 		$xC->ENTIDAD_DOMICILIO) ); //"CC");
define("EACP_TELEFONO_PRINCIPAL", 				$xC->get("domicilio.telefono_principal", "", 				$xC->ENTIDAD_DOMICILIO));
//========================================= OTROS DATOS ===========================================
//========================================= DATOS DE COLOCACION ===========================================
define("EACP_PER_SOLICITUDES", 					$xC->get("periodo_de_solicitudes_actual", "", 							$xC->COLOCACION));
define("EACP_PER_SOLICITUDES_ANT", 				$xC->get("periodo_de_solicitudes_anterior", "", 						$xC->COLOCACION));
define("PQ_DIA_PRIMERA_QUINCENA", 				$xC->get("pagos_quincenales.primera_quincena", 15, 						$xC->COLOCACION));
define("PQ_DIA_SEGUNDA_QUINCENA", 				$xC->get("pagos_quincenales.segunda_quincena", 30, 						$xC->COLOCACION));

define("INTERES_DIAS_MAXIMO_DE_MORA", 			$xC->get("dias_maximo_de_mora_a_calcular", 190, 						$xC->COLOCACION));
define("INTERES_DIAS_MAXIMO_A_DEVENGAR", 		$xC->get("dias_maximos_en_que_se_acumulan_intereses_devengados", 120, 	$xC->COLOCACION));
define("DIAS_PAGO_UNICOS", 						$xC->get("dias_para_vencimiento_en_pago_unico",30, 						$xC->COLOCACION));	//dias para vencer en Pagos a final de plazo
define("DIAS_PAGO_VARIOS", 						$xC->get("dias_para_vencimiento_en_pagos_varios",90, 					$xC->COLOCACION));	// '   '     '      '   '  en Parcialidades
define("PM_DIA_DE_PAGO", 						$xC->get("pagos_mensuales.dia_de_pago",2,								$xC->COLOCACION));
define("PS_DIA_DE_PAGO", 						$xC->get("pagos_semanales.dia_de_pago",1,								$xC->COLOCACION));
define("DIAS_ESPERA_CREDITO", 					$xC->get("dias_en_espera_maxima_para_ministracion_de_creditos",45, 		$xC->COLOCACION));
define("CREDITO_TIPO_POR_DESTINO", 				$xC->get("determinar_modalidad_por_destino","false", 					$xC->COLOCACION));
define("EACP_INCLUDE_INTERES_IN_PAGARE", 		$xC->get("incluir_intereses_en_pagare", "true", 						$xC->COLOCACION));
define("EACP_DIAS_MINIMO_CREDITO", 				$xC->get("dias_de_credito_minimo_otorgable", 1, 						$xC->COLOCACION));
//========================================= DATOS DE CAPTACION ===========================================
define("EACP_MINIMO_INVERSION", 				$xC->get("saldo_minimo_de_inversion", 1, 					$xC->CAPTACION));
define("INVERSION_MONTO_MINIMO", 				$xC->get("saldo_minimo_de_inversion", 1, 					$xC->CAPTACION));
define("EACP_MINIMO_A_LA_VISTA", 				$xC->get("deposito_minimo", 1, 								$xC->CAPTACION));
define("A_LA_VISTA_MONTO_MINIMO", 				$xC->get("deposito_minimo", 1, 								$xC->CAPTACION));
define("CTA_GLOBAL_CORRIENTE", 					$xC->get("numero_de_contrato_por_defecto", "200001", 		$xC->CAPTACION));
define("DEFAULT_CUENTA_CORRIENTE", 				CTA_GLOBAL_CORRIENTE);
define("INVERSION_DIAS_MINIMOS", 				$xC->get("inversion_dias_minimos", 7, 						$xC->CAPTACION));
//========================================= DATOS DE OPERACIONES ===========================================
define("EACP_DIAS_INTERES", 						$xC->get("divisor_en_dias_del_interes", 360, 				$xC->OPERACIONES));
define("DIAS_PARA_EDITAR_RECIBOS", 				$xC->get("numero_de_dias_maximo_para_editar_recibos",15, 	$xC->OPERACIONES) );
define("DEUDORES_DIVERSOS_DIAS",				$xC->get("dias_de_espera_para_deudores",30, 				$xC->OPERACIONES));
define("DEUDORES_DIVERSOS_MAXIMO",				$xC->get("maximo_otorgable_a_comprobar",1000, 				$xC->OPERACIONES));
define("TESORERIA_MONTO_MAXIMO_OPERADO", 		$xC->get("monto_maximo_operado_en_caja", 9999999, 			$xC->OPERACIONES));
define("TOLERANCIA_SALDOS", 						$xC->get("monto_tolerable_en_operaciones", 0.10, 			$xC->OPERACIONES) );//0.99
define("SPLIT_INTERES_MORATORIO", 				$xC->get("dividir_interes_moratorio_del_normal", "true", 	$xC->OPERACIONES) ); // true

//========================================= DATOS DE AML/PLD/FT ===========================================
define("AML_FECHA_DE_INICIO", 					$xC->get("aml_fecha_de_activacion", date("Y-m-d"), 				$xC->AML) );
define("AML_KYC_DIAS_PARA_REVISAR_DOCTOS", 	$xC->get("aml_dias_para_revisar_documentos_de_personas", 3, 	$xC->AML) );
define("AML_KYC_DIAS_PARA_COMPLETAR_PERFIL", 	$xC->get("aml_dias_para_completar_perfil_transaccional", 3, 	$xC->AML) );


define("AML_CLAVE_MONEDA_LOCAL", 				strtoupper($xC->get("aml_clave_de_moneda_local", "MXN", 		$xC->AML) ));
define("EACP_CLAVE_MONEDA_LOCAL", 				strtoupper($xC->get("aml_clave_de_moneda_local", "MXN", 		$xC->OPERACIONES) ));

//========================================= DATOS DE CONTABILIDAD ===========================================
define("COSTE_POR_ACCION", 						$xC->get("costo_por_accion", "1000", 							$xC->CONTABILIDAD) );
define("EACP_TASA_RESERVA", 						$xC->get("tasa_de_reserva_en_aportaciones", 0,					$xC->CONTABILIDAD) );



//Coste de Acciones


//2012-05-04 : Parametro que marca si la modalidad de credito(consumo, comercial) es determinada por el destino



$ldopts	= true;
/*
if(isset($_SESSION)){
	if(isset($_SESSION["sucursal"])){
		include_once ("core.common.inc.php");
		$xSuc		= new cSucursal();
		if($xSuc->init() == true){
			//valores tomados de la sucursal
			define("DEFAULT_CODIGO_POSTAL", 					$xSuc->getCodigoPostal());
			define("DEFAULT_NOMBRE_COLONIA", 				$xSuc->getColonia());
			define("DEFAULT_NOMBRE_LOCALIDAD", 				$xSuc->getClaveDeLocalidad());
			define("DEFAULT_NOMBRE_MUNICIPIO", 				$xSuc->getMunicipio());
			define("DEFAULT_NOMBRE_ESTADO", 					$xSuc->getEstado());
			$ldopts	= false;
		}
	}
}
*/
if($ldopts == true){

	//valores tomados de la sucursal
	define("DEFAULT_CODIGO_POSTAL", 					$xC->get("domicilio.codigo_postal", "", $xC->ENTIDAD_DOMICILIO ));
	define("DEFAULT_NOMBRE_COLONIA", 				$xC->get("domicilio.colonia", "", $xC->ENTIDAD_DOMICILIO ));
	define("DEFAULT_NOMBRE_LOCALIDAD", 				$xC->get("domicilio.localidad", "", $xC->ENTIDAD_DOMICILIO ));
	define("DEFAULT_NOMBRE_MUNICIPIO", 				$xC->get("domicilio.municipio", "", $xC->ENTIDAD_DOMICILIO ));
	define("DEFAULT_NOMBRE_ESTADO", 					$xC->get("domicilio.estado", "", $xC->ENTIDAD_DOMICILIO ));
	
}


//2014-02-13


$periodolocal								= date("n");
define("EACP_PER_CONTABLE",  			$periodolocal);
define("EACP_PER_SEGUIMIENTO", 			$periodolocal);
define("EACP_PER_COBRANZA", 				$periodolocal);



function go_calendar($id, $type = "default"){ return ""; }

define("JS_CLOSE", 				"<script>var xG = new Gen(); xG.close(); </script>");


/* variables varias */
function select_bool($name){ return "";}
/* ---------------------------------- Funciones Generales asequibles --------------------------- */
/**
 * Funcion que Genera codigo javascript basico para el manejo de formulaios.
 *
 * @param string $idform corresponde al nombre del formulario de tarbajo, su tag name
 * @param string $isol corresponde al numero de solicitud manejada, cuando es perzonalizado
 * @param string $xpath corresponde al path de librerias .js
 */
function jsbasic($idform, $isol = "1", $xpath = "") {
	$jslog		= (MODO_DEBUG == true) ? "alert(\"MODULO PENDIENTE DE ACTUALIZAR!!\");" : "console.log(\"Modulo depreciado!!\");";
$js = "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"$xpath./js/jscalendar/calendar-green.css\" title=\"green\" />
<script type=\"text/javascript\" src=\"$xpath./js/jscalendar/calendar.js\"></script>
<script type=\"text/javascript\" src=\"$xpath./js/jscalendar/lang/calendar-es.js\"></script>
<script type=\"text/javascript\" src=\"$xpath./js/jscalendar/calendar-setup.js\"></script>

<script language='javascript' src='$xpath./jsrsClient.js'></script>
<script type='text/javascript' src='$xpath./md5.js'></script>
<script language='javascript'>
var jsrFile = \"$xpath./clsfunctions.inc.php\";

$jslog
var setToGo = true;
	function frmSubmit(evaluate){
        if ( evaluate ){
			setCheckForm(document.$idform);
        }
        if (setToGo == true ){
            document.$idform.submit();
        }
	}
	/* funcion que retorna el numero de socio */
	function envsoc(vtipocuenta, vtipocredito){
	    var tipocuenta = vtipocuenta;
	    var tipocredito = vtipocredito;
    
	    if (!tipocuenta || tipocuenta==NaN) {
		    tipocuenta=0;
	    }
	    if (!tipocredito || tipocredito==NaN) {
		    tipocredito=0;
	    }
	    //Inicia Variables
	    var ensoc = 0;
	    if (document.$idform.idsocio){
		    var ensoc = document.$idform.idsocio.value;
	    }
	    if((ensoc!='') && (ensoc!=0) && (ensoc!=NaN)) {
		    jsrsExecute(jsrFile, retsocio, 'nombre', ensoc);

		    jsrsExecute(jsrFile, retpri, 'prioricred', ensoc);
		    jsrsExecute(jsrFile, pricta, 'prioricta', ensoc  + '|' + tipocuenta);

	    } else {
		    var siBuscar = confirm(\"EL SOCIO NUM. [\" + ensoc + \"] NO EXISTE O ESTA INACTIVO. �DESEA BUSCARLO?\");
		    if(siBuscar){
			    goSocio_();
		    }
	    }
	}
	function retsocio(xsoc){
		var nsoc = xsoc;
		document.$idform.nombresocio.value = nsoc;

		if (nsoc == '0') {
			var siBuscar = confirm(\"EL SOCIO NUM. [\" + nsoc + \"] NO EXISTE O ESTA INACTIVO. DESEA BUSCARLO?\");
			if(siBuscar){
				goSocio_();
			}
		}
	}
	// .- FUNCION QUE RETORNA EL NOMBRE DE LA SOLICITUD.
	function envsol(){
		if(document.$idform.idsolicitud) {
			var ensol= document.$idform.idsolicitud.value;
				if((ensol!='') && (ensol!=0) && (ensol!=NaN)) {
					jsrsExecute(jsrFile, retornasol,'dsolicitud', ensol);
				}
		}
	}
	function retornasol(xsol){
	var nsol = xsol;
		document.$idform.nombresolicitud.value = nsol;

		if (nsol == 'NO EXISTEN DATOS') {
			var siBuscar = confirm(\"EL CREDITO NUM. [\" + nsol + \"] NO EXISTE O ESTA INACTIVO. DESEA BUSCARLO?\");
			if(siBuscar){
				goCredit_();
			}
		}
		// Checa si la Solicitud no es cero
		if (nsol == '0') {
			var siBuscar = confirm(\"EL CREDITO NUM. [\" + nsol + \"] NO EXISTE O ESTA INACTIVO. DESEA BUSCARLO?\");
			if(siBuscar){
				goCredit_();
			}
		}
	}
	function chk_solicitud() {
		thesol= document.$idform.idsolicitud.value;

			jsrsExecute(jsrFile, existe_sol,'solicitudexistente', thesol);

	}
	function existe_sol(mid) {
		midd = parseFloat(mid);
		//midd2 = parseFloat(document.$idform.idsolicitud.value);
		if (midd!=0 || midd!='' ) {
			alert('LA SOLICITUD ' + midd +  ' YA EXISTE');

			midd = midd + 1;
			document.$idform.idsolicitud.value = midd + 1;
			document.$idform.idsolicitud.focus();
			document.$idform.idsolicitud.select();
			chk_solicitud();
		}
	}

	//.- FUNCION QUE RETORNA EL NOMBRE DEL GPO SOLIDARIO
	function envgpo() {
		idgpo          = document.$idform.idgrupo.value;
		jsrsExecute(jsrFile, retornagpo,'mostrargrupo', idgpo + ' 1');
	}
	function retornagpo(nombredev) {
		document.$idform.nombregrupo.value = nombredev;
	}
	// .- FUNCION QUE LISTA LOS CREDITOS
	function listarcreds() {
	}
	function dlistarcreds(lstcreds) {
	}
	//.- FUNCION QUE LISTA LAS PARCIALIDADES
	function listpar() {
	}
	function obtpar(laspar) {
	}
	//.- FUNCION OBTIENE DETALLES DE LA CUENTA DE CAPTACION
	function envcta(inttipo) {
		if(inttipo=='') {
			var inttipo = 0;
		}
		if(document.$idform.idcuenta){
			lacta = document.$idform.idcuenta.value;
				if (lacta!='' || lacta!=NaN || lacta!=0) {
					jsrsExecute(jsrFile, obtcta,'mostrarcuenta', lacta + '|' + inttipo);
				}
		}
	}
	function obtcta(depcta)  {
		var ccta    = depcta;
		document.$idform.nombrecuenta.value = ccta;
			if(ccta == 'NO EXISTEN DATOS') {
				alert('NO EXISTE EL NUMERO DE CUENTA  O ESTA INACTIVA.- NUMERO INVALIDO');
				return 0;
			}
	}
	function listarctas(int_tipo) {
	}
	function pricta(escta) {
		if (escta!='' || escta!=NaN || escta!=0) {
			var micta = escta;
			if(document.$idform.idcuenta){
				document.$idform.idcuenta.value = micta;
			}
		}
	}
	function showctas(lasctas) {
	}
	function envparc() {
	var misol = 0;
		if(document.$idform.idsolicitud){
			misol= document.$idform.idsolicitud.value;
		}
			if (misol!='' || misol!=NaN || misol!=0) {
				jsrsExecute(jsrFile, darparc,'damecredito', misol + ' 27');
				var ncta = document.$idform.nombresolicitud.value
					if (ncta==\"\" || ncta==NaN){
						envsol();
					}
			}
	}
	function darparc(laparc)  {
		var uparc = parseInt(laparc)+1;
		if (document.$idform.idparcialidad){
			document.$idform.idparcialidad.value = uparc;
		}

	}
	function retpri(toval) {
	var myval = toval;
		if (document.$idform.idsolicitud){
			document.$idform.idsolicitud.value = myval;
			//x = document.$idform.idsolicitud.parentNode;
			//x.innerHTML = myval;
		}
	}
	function lstgarantias() {
	}
	function devgar(lecr) {
	}
	// funcion que checa que el valor no sea cero
	function chkmonto(myval) {
		theval = parseFloat(myval);
		if (theval <= 0) {
			alert('EL VALOR DEBE SER MAYOR A CERO');
		}
		if (isNaN(theval)) {
			alert('EL VALOR NO PUEDE SER NULO O NO NUMERICO');
		}
	}
function notnan(isthis) {
	res = isthis;
	if (res =='') {
		alert('EL VALOR DEBE ESTAR EN BLANCO');
	}
	if (isNaN(res)) {
		alert('EL VALOR NO PUEDE SER NULO');
	}
}
function muestralo(id_e) {
		var mist_s = document.getElementById(id_e);
		mist_s.style.visibility='visible';
}
function ocultalo(id_e) {
		var mist_e = document.getElementById(id_e);
		mist_e.style.visibility='hidden';
}
function msgbox(string_alert) {
		alert (string_alert);

}
var i=1;
function img_dyn(img) {
		var name_img = img.name;
		var img1 = new Image;
		var img2 = new Image;
		img1.src = 'images/' + name_img + '.png';
		img2.src = 'images/' + name_img + '_down.png';

		if (i==1) {
				img.src = img1.src;
				i=2;
		} else  {
				img.src = img2.src;
				i=1;
		}
}
function ShowButton(objName, ImageName, points) {
		objName.src=ImageName
		objName.height = points;
		objName.width = points;
}
function cierrame() {
		//	alert(document.title);
		window.close();
}
function goCredit_(){
	var isoc = document.$idform.idsocio.value;
	var pfcred = \"../utils/frmscreditos_.php?i=\";
		frmCred = window.open(pfcred + isoc + \"&f=$idform\", \"\", \"width=600,height=400,dependent=yes\");
		frmCred.focus();
}
function goSocio_(){
	var isoc = document.$idform.idsocio.value;
	var pfSoc = \"../utils/frmbuscarsocio.php?i=\";
		frmSoc = window.open(pfSoc + isoc + \"&f=$idform\", \"\", \"width=600,height=400,dependent=yes\");
		frmSoc.focus();
}
function goLetra_(){
	var isoc = document.$idform.idsolicitud.value;
	var pfSoc = \"../utils/frmletras.php?i=\";
		frmSoc = window.open(pfSoc + isoc + \"&f=$idform\", \"\", \"width=420,height=600,scrollbars,dependent=yes\");
		frmSoc.focus();
}
function goCuentas_(tipoc){
var vTipoC = \"\";
	if(tipoc){
		vTipoC = \"&a=\" + tipoc;
	}
	var isoc = document.$idform.idsocio.value;
	var pfSoc = \"../utils/frmcuentas_.php?i=\";
		frmSoc = window.open(pfSoc + isoc + \"&f=$idform\" + vTipoC, \"\", \"width=600,height=400,scrollbars,dependent=yes\");
		frmSoc.focus();
}
function goGrupos_(){
	var iGrp = document.$idform.idgrupo.value;
	var pfGrp = \"../utils/frmsgrupos.php?i=\";
		frmGrp = window.open(pfGrp + iGrp + \"&f=$idform\", \"\", \"width=600,height=600,scrollbars,dependent=yes\");
		frmGrp.focus();
}


function m_menu(evt){
	var i_mnu = document.getElementById(\"menuh\");
    evt = (evt) ? evt : ((event) ? event : null);
    if (evt){
            var left, top;
            if (evt.pageX) {
                left = evt.pageX;
                top = evt.pageY;
            } else if (evt.offsetX || evt.offsetY) {
                left = evt.offsetX;
                top = evt.offsetY;
            } else if (evt.clientX) {
                left = evt.clientX;
                top = evt.clientY;
            }
    i_mnu.style.top = top;
    i_mnu.style.left = left;
    setTimeout('ocultalo(\"menuh\")',60*20);
    }

}
function jsRestarFechas(date1, date2) {
    var DSTAdjuste = 0;
    // ------------------------------------
    oneMinute = 1000 * 60;
    var oneDay = oneMinute * 60 * 24;
    // ------------------------------------
    date1.setHours(0);
    date1.setMinutes(0);
    date1.setSeconds(0);

    date2.setHours(0);
    date2.setMinutes(0);
    date2.setSeconds(0);
    // ------------------------------------
    if (date2 > date1) {
        DSTAdjuste =
            (date2.getTimezoneOffset() - date1.getTimezoneOffset()) * oneMinute;
    } else {
        DSTAdjuste =
            (date1.getTimezoneOffset() - date2.getTimezoneOffset()) * oneMinute;
    }
    var diff = Math.abs(	date2.getTime() - date1.getTime()	) - DSTAdjuste;
    return Math.ceil(diff/oneDay);
}

function setCheckForm(vFrm){
	  	var isLims 			= vFrm.elements.length - 1;

  		for(i=0; i<=isLims; i++){
			var mTyp 	= vFrm.elements[i].getAttribute(\"type\");
			var mCls	= vFrm.elements[i].getAttribute(\"class\");
			if (mTyp == \"text\" || mTyp == \"hidden\" || mTyp == \"textarea\"){
				setToGo = isNotEmpty(vFrm.elements[i]);
				if ( mCls == \"mny\" ){
					setToGo = isNumber(vFrm.elements[i]);
				}
			}
  		}
}

// validates that the field value string has one or more characters in it
function isNotEmpty(elem) {
    var str = elem.value;
    var re = /.+/;
    if(!str.match(re)) {
        alert(\"Este Campo es Requerido\");
        return false;
    } else {
        return true;
    }
}

//validates that the entry is a positive or negative number
function isNumber(elem) {
    var str = elem.value;
    var re = /^[-]?\d*\.?\d*$/;
    str = str.toString( );
    if (!str.match(re)) {
        alert(\"Agregue un Numero Valido en el Campo.\");
        return false;
    }
    return true;
}

function checkDate(fld) {
    var mo, day, yr;
    var entry       = fld.value;
    var reLong      = /\b\d{1,2}[\/-]\d{1,2}[\/-]\d{4}\b/;
    var reShort     = /\b\d{1,2}[\/-]\d{1,2}[\/-]\d{2}\b/;
    var valid       = (reLong.test(entry)) || (reShort.test(entry));
    if (valid) {
        var delimChar = (entry.indexOf(\"/\") != -1) ? \"/\" : \"-\";
        var delim1 = entry.indexOf(delimChar);
        var delim2 = entry.lastIndexOf(delimChar);
        mo = parseInt(entry.substring(0, delim1), 10);
        day = parseInt(entry.substring(delim1+1, delim2), 10);
        yr = parseInt(entry.substring(delim2+1), 10);
        // handle two-digit year
        if (yr < 100) {
            var today       = new Date( );
            // get current century floor (e.g., 2000)
            var currCent    = parseInt(today.getFullYear( ) / 100) * 100;
            // two digits up to this year + 15 expands to current century
            var threshold   = (today.getFullYear( ) + 15) - currCent;
            if (yr > threshold) {
                yr += currCent - 100;
            } else {
                yr += currCent;
            }
        }
        var testDate = new Date(yr, mo-1, day);
        if (testDate.getDate( ) == day) {
            if (testDate.getMonth( ) + 1 == mo) {
                if (testDate.getFullYear( ) == yr) {
                    // fill field with database-friendly format
                    fld.value = mo + \"/\" + day + \"/\" + yr;
                    return true;
                } else {
                    alert(\"There is a problem with the year entry.\");
                }
            } else {
                alert(\"There is a problem with the month entry.\");
            }
        } else {
            alert(\"There is a problem with the date entry.\");
        }
    } else {
        alert(\"Incorrect date format. Enter as mm/dd/yyyy.\");
    }
    return false;
}

function jsSumarDias(vFecha, days){
    var mDays   = parseInt(days);
    var vFecha	= new String(vFecha);
    var sDays	= 86400000 * mDays;
    var sDate   = vFecha.split('-');
    var varDate = new Date(sDate[0], parseInt(sDate[1]-1), parseInt(sDate[2])-1, 0,0,0 );

    var vDate	= varDate.getTime()+sDays;
	varDate.setTime( vDate );
	
    var mMonth  = varDate.getMonth()+1;
    var mDate	= varDate.getDate()+1;
    if (mMonth == 0){
        alert('Error al Determinar el Mes ' + mMonth + ' en la Fecha ' + vFecha);
    }
	return varDate.getFullYear() + '-' + mMonth + '-' + mDate;
}
function jsRestarDias(vFecha, days){
	
    var mDays   = new Number(days);
    var vFecha	= new String(vFecha);
    var sDays	= 86400000 * mDays;
    var sDate   = vFecha.split('-');
    var varDate = new Date(sDate[0], parseInt(sDate[1]-1), parseInt(sDate[2])-1, 0,0,0 );

    var vDate	= varDate.getTime()-sDays;

	varDate.setTime(vDate);
    var mMonth  = varDate.getMonth()+1;
    var mDate	= varDate.getDate()+1;
    
    if (mMonth == 0){
        alert('Error al Determinar el Mes ' + mMonth + ' en la Fecha ' + vFecha);
    }
	return varDate.getFullYear() + '-' + mMonth + '-' + mDate;
}
function jsGenericWindow(mFile, winTop, winLeft, winHeight, winWidth){
		var mIDWin = Math.random();
		var windowName = \"myWin\" + mIDWin;
        if(!winLeft)	{	var winLeft		= parseInt(screen.width * 0.10);	}
	    if(!winTop)		{	var winTop		= parseInt((screen.height * 0.10) + 100);	}
		if(!winHeight)	{	var winHeight	= parseInt((screen.height - (screen.height * 0.10) ) - 100);	}
		if(!winWidth)	{	var winWidth	= parseInt((screen.width - (screen.width * 0.10)));		}

        var windowFeatures = \"width=\" + winWidth + \",height=\" + winHeight + \",status,scrollbars,resizable,left=\" + winLeft + \",top=\" + winTop
        newWindow = window.open(mFile, windowName, windowFeatures);
		newWindow.focus();
}
function jsRoundPesos(mCantidad){
	var mStrCantidad	= new String(mCantidad);
	var rF = new RegExp(\",\" , \"g\");
	var rF2 = new RegExp(\"$\" , \"g\");
	var rF3 = new RegExp(\" \" , \"g\");

	mStrCantidad = mStrCantidad.replace(rF, \"\");
	mStrCantidad = mStrCantidad.replace(rF2, \"\");
	mStrCantidad = mStrCantidad.replace(rF3, \"\");
	mStrCantidad = mStrCantidad.replace(\"$\", \"\");

		mStrCantidad	+= \".00\";
	var arrCantidad		= mStrCantidad.split(\".\");

	return arrCantidad[0] + \".\" + arrCantidad[1];
}

</script>";
	echo $js;
}

define("MNU_BASIC", "");
function cmd_irpt($idrep = ""){
	return "<img src='images/help.gif' onClick='info_rpt($idrep);' title='Mostrar Descripcion del Reporte' />";
}

//.---------------------------------------------------------------------------------------------------------------------------
$regresar 	= "<html><body onload='javascript:history.back();'></body></html>";
$cerrar 	= "<html><body onload='javascript:window.close();'></body></html>";
//VARIABLES GLOBALES
define("VJS_REGRESAR", $regresar);


/**
 * @deprecated 1.9.0
 **/
/*$limite_udis 					=	5000;	//Limite Máximo en UDIs que se otorga un credito revolvente
$monto_min_cred 				=	500;
$dias_tolerancia_credito 		=	2;
$num_pagos_maximo				=	172;
$dias_gracia 					=	15;
$depositovista_dias 			=	60;*/


//===========================================================================================================================================

?>