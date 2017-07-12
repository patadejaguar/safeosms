<?php
//=====================================================================================================
//=====>	INICIO_H
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	$theFile					= __FILE__;
	$permiso					= getSIPAKALPermissions($theFile);
	if($permiso === false){		header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//<=====	FIN_H
	$iduser = $_SESSION["log_id"];
//=====================================================================================================

$xHP		= new cHPage("TR.Orden de Cobranza", HP_FORM);
$jxc 		= new TinyAjax();

$xCaja		= new cCaja();
if( $xCaja->getEstatus() == TESORERIA_CAJA_CERRADA ){	$xHP->goToPageError(200); }
function jsaSetCerrarNomina($idNomina){
	$xNom	= new cEmpresasCobranzaPeriodos($idNomina);
	if($xNom->init() == true){
		$xNom->setCerrar();
	}
}
function jsaGetCobranza($empresa, $idperiodo){
	$xL				= new cSQLListas();
    $xF				= new cFecha();
    $sql			= $xL->getListadoDeCobranza($idperiodo, SYS_UNO);
    $xImg			= new cHImg();
    $xBtn			= new cHButton();
    //fecha de ministracion anterior al
    
    $xT	= new cTabla($sql);
    $xT->setKeyField("numero_solicitud");
    $xT->setKey(2);
    $xT->setWidthTool("200px");
    $xT->setKeyTable("creditos_solicitud");
    $xT->setEventKey("jsGetRecibosByCredito");
    $xT->addEspTool("<span>&nbsp;&nbsp;&nbsp;</span>");
    $xT->addEspTool("<div class='coolCheck'><input type='checkbox' id='chk_REPLACE_ID_' onclick='jsSetAlimentarCobros(this, _REPLACE_ID_)' /><label for='chk_REPLACE_ID_'></label></div>");
    
    $xT->OButton("TR.PLAN_DE_PAGOS", "getPlanDePagos(_REPLACE_ID_)", $xBtn->ic()->CALENDARIO);
    $xT->OButton("TR.Ver", "setOcultar(_REPLACE_ID_)", $xBtn->ic()->VER);
	$xT->OButton("TR.Cobranza", "jsSetParaCobros(_REPLACE_ID_)", $xBtn->ic()->DINERO);
	$xT->OButton("TR.ESTADO_DE_CUENTA", "getEstadoDeCuenta(_REPLACE_ID_)", $xBtn->ic()->REPORTE);

	$xT->setWithMetaData();
    $xT->setFootSum(array( 5 => "letra", 7 => "monto"));
    return  $xT->Show();
}

function jsaGetDatosDelEnvio($idEmpresa, $periocidad, $variacion, $periodo){
	$xEmp	= new cEmpresas($idEmpresa); $xEmp->init();
	$tab 		= new TinyAjaxBehavior();
	$periodo	= $periodo + $variacion;
	//agregar observaciones
	$monto 		= $xEmp->getMontoDelPeriodo($periodo, $periocidad);
	$ops		= $xEmp->getOperacionesDePeriodo();
	//$monto		= ($monto < 0 ) ? $monto  * -1: $monto;
	if($monto > 0){
		//$tab -> add(TabSetvalue::getBehavior("idobservaciones", "PERIODO: $periodo | MONTO $ $monto "  ));
	} else {
		//$tab -> add(TabSetvalue::getBehavior("idobservaciones", "PERIODO $periodo POR ENVIAR" ));
	}
	return $tab -> getString();
}

function jsaGetDatosEmpresa($idEmpresa){
	$xEmp		= new cEmpresas($idEmpresa);
	$xF			= new cFecha();
	$ql			= new MQL();
	$xl			= new cSQLListas();
	
	
	$observaciones	= "";
	$xEmp->init();
	$persona		= $xEmp->getClaveDePersona();
	$xF				= new cFecha(0);

	$opts			= "";
	$exT			= "";
	$periocidad		= $xEmp->getPeriocidadPref();
	
	$periodo		= ($periocidad == CREDITO_TIPO_PERIOCIDAD_SEMANAL) ? $xF->semana() : $xF->quincena();
	$periodo		= ( intval($xEmp->getPeriodo()) < 1 ) ? $periodo : intval($xEmp->getPeriodo());
	$data			= $ql->getDataRecord( $xl->getListadoDePeriodoPorEmpresa($idEmpresa, SYS_UNO, true) );
	$variacion		= "0";
	$fecha_inicial	= $xF->getFechaMX();
	$fecha_final	= $xF->getFechaMX();
	$control		= 0;
	$ctrl			= "<label for=\"idperiodo\">Periodo $periodo</label><input type=\"number\" id=\"idperiodo\" onchange=\"jsGetCobranza()\" onblur=\"jsGetCobranza()\" />";
	$contar			= 1;
	$max			= 15;
	foreach ($data as $rw){
		$idsel				= "";
		$idclave			= $rw["codigo"];
		$saldo				= $rw["saldo_activo"];
		if($saldo>0){
			$xPer				= $xEmp->getOPeriodo(false, false, $idclave);
			if($periodo == $xPer->periodo_marcado()->v() AND $periocidad == $xPer->periocidad()->v()){
				$control		= $idclave;
				$periodo 		= $xPer->periodo_marcado()->v();
				$periocidad 	= $xPer->periocidad()->v();
				$fecha_final	= $xPer->fecha_final()->v();
				$fecha_inicial	= $xPer->fecha_inicial()->v();
				$idsel			= " selected=\"true\" ";
			}		
			
			if($contar <= $max){
				$opts .= "<option value=\"" .$idclave . " \"$idsel>" . $rw["nombre_periocidad"] ."[" . $xPer->periodo_marcado()->v(). "]";
				$opts .= "  - DEL: " . $xF->getFechaCorta($rw["fecha_inicial"]) . " - " . $xF->getFechaCorta($rw["fecha_final"]) .  " SALDO " . getFMoney($rw["saldo"]) . ".- ID $idclave </option>";
			}
			$contar++;
		}
	}
	if($opts != ""){
		
		$ctrl	= "<label for='idperiodo'>Periodo</label><select id=\"idperiodo\" name=\"idperiodo\" onblur=\"jsGetCobranza()\" onchange=\"jsGetCobranza()\">$opts</select>";
		
	}
	$ctrl	.= "<input type=\"hidden\" id=\"idvariacion\" value=\"$variacion\" />";
	$ctrl	.= "<input type=\"hidden\" id=\"idperiodo\" value=\"$periodo\" />";
	$ctrl	.= "<input type=\"hidden\" id=\"idperiocidad\" value=\"$periocidad\" />";
	$ctrl	.= "<input type=\"hidden\" id=\"idclavedepersona\" value=\"$periocidad\" />";

	return $ctrl;
}


function jsaSetCambiarFechaMinistracion($credito, $dia, $mes, $anno){
    $fecha	= "$anno-$mes-$dia";
    $xCred	= new cCredito($credito);
    $xCred->init();
    $xCred->setCambiarFechaMinistracion($fecha);
    return "Credito $credito modificado al **$dia-$mes-$anno**";//$xCred->getMessages();
}
function jsaSetGuardarDeposito($tipo_pago, $banco, $monto, $cobranza, $fecha, $observaciones, $empresa, $periodo){
	//'idtipo_pago', 'idcodigodecuenta', 'idmontodeposito', 'idsumacbza', 'idfecha-0', 'idobservaciones', 'idcodigodeempresas', 'idperiodo'
    $diferencia	= $monto - $cobranza;
    $xEmp		= new cEmpresas($empresa); $xEmp->init();
    $persona	= $xEmp->getClaveDePersona();
    $xF			= new cFecha();
    $fecha		= $xF->getFechaISO($fecha);
    $xCaja		= new cCaja();
    $documento	= DEFAULT_CREDITO;
    $nombreemp	= $xEmp->getNombreCorto();
    
    $xPer		= $xEmp->getOPeriodo(false, false, $periodo);
    $periodo2	= $xPer->periodo_marcado()->v();
    $periocidad	= $xPer->periocidad()->v();
    
    $observaciones	= "$empresa-$nombreemp-$periocidad-$periodo2-ID.$periodo-" . $observaciones;
    
	if($tipo_pago == TESORERIA_COBRO_TRANSFERENCIA){
		$op		= $xCaja->setCobroTransferencia(DEFAULT_RECIBO, $banco, $monto, $diferencia, $fecha, $observaciones, $persona, $documento);
	} elseif($tipo_pago == TESORERIA_COBRO_EFECTIVO){
		$xCaja->setCobroEfectivo(DEFAULT_RECIBO, $monto, $cobranza, $observaciones);
	}
	//Agregar operacion de la empresa
	$xEmp->addOperacion($monto, $periodo2, $periocidad, $fecha, -1, false, $observaciones);
}
function jsaGetMontoCobranza($empresa){
	$user	= getUsuarioActual();
	$fecha	= fechasys();
	$sql	= "SELECT SUM(`operaciones_recibos`.`total_operacion`) AS 'total' 
		FROM `operaciones_recibos` `operaciones_recibos` 
				`operaciones_recibos` `operaciones_recibos` 
		INNER JOIN `creditos_solicitud` `creditos_solicitud` 
		ON `operaciones_recibos`.`docto_afectado` = `creditos_solicitud`.
		`numero_solicitud` 
			INNER JOIN `operaciones_recibostipo` `operaciones_recibostipo` 
			ON `operaciones_recibos`.`tipo_docto` = `operaciones_recibostipo`.
			`idoperaciones_recibostipo`
			
			WHERE operaciones_recibostipo.mostrar_en_corte!='0' 
			AND operaciones_recibos.fecha_operacion = '$fecha' 
			AND operaciones_recibos.idusuario=$user 
			AND `creditos_solicitud`.persona_asociada	= $empresa ";
	$sdo	= mifila($sql, "total");
	return "($ " . getFMoney($sdo) . ")";
}
$jxc ->exportFunction('jsaSetGuardarDeposito', array('idtipo_pago', 'idcodigodecuenta', 'idmontodeposito', 'idsumacbza', 'idfecha-0', 'idobservaciones', 'idcodigodeempresas', 'idperiodo'), "#idmsgs" );
$jxc ->exportFunction('jsaGetCobranza', array("idcodigodeempresas", "idperiodo"), "#reports");
$jxc ->exportFunction('jsaGetDatosEmpresa', array("idcodigodeempresas"), "#divperiodo");
$jxc ->exportFunction('jsaGetDatosDelEnvio', array("idcodigodeempresas", "idperiocidad", "idvariacion", "idperiodo"));
$jxc ->exportFunction('jsaSetCambiarFechaMinistracion', array("idcredito", "ideldia4", "idelmes4", "idelanno4"), "#idmsgs");
$jxc ->exportFunction('jsaGetMontoCobranza', array("idcodigodeempresas"), "#saldocorte");
$jxc ->exportFunction('jsaSetCerrarNomina', array("idperiodo"), "#idmsgs");

$jxc ->process();

echo $xHP->getHeader(true);
echo $xHP->setBodyinit();
$txt		= new cHText();
$HFecha		= new cHDate();
$xFRM		= new cHForm("frmcbza", "", "frmsumacbza");
$xBtnN		= new cHButton();
$xSel		= new cHSelect();
$xDiv		= new cHDiv();
$xHSel		= new cHSelect();
$xCB		= new cHCobros();
$xFRM->setTitle($xHP->getTitle());

$xFRM->OButton("TR.Obtener Cobranza", "jsGetCobranza()", $xFRM->ic()->CARGAR, "idgetcbza") ;
$xFRM->OButton("TR.Reporte de Nomina", "jsListaDeNominas()", $xFRM->ic()->SALDO, "idedo") ;
$xFRM->OButton("TR.Cerrar Cobranza", "jsSetLockPeriodo()", $xFRM->ic()->COBROS, "idcerrar");
$xFRM->OButton("TR.Cobranza del dia", "jsGetCobranzaDay()", $xFRM->ic()->DINERO, "idcobhoy");
$xFRM->OButton("TR.Estado_de_cuenta", "jsGetEdoCuentaGeneral()", $xFRM->ic()->REPORTE, "idedocta");
$xFRM->OButton("TR.Cerrar Nomina", "jsSetCerrarNomina()", $xFRM->ic()->CERRAR, "idcerrar");

$xFRM->addFootElement('<input type="hidden" id="idsumacbza" value="0" />');
$xSemp	= $xSel->getListaDeEmpresas("", true);
$xSemp->addEvent("onblur", "jsResetCbza();jsCargarDatosIniciales();");

$xFRM->addDivSolo($xSemp->get(), " ", "tx24", "tx24", array( 1 => array("id" => "divempresa"), 2 => array("id" => "divperiodo") ) );

$xFRM->addHElem( $xCB->get(false, "", "", false) );
$xFRM->addHElem( $xHSel->getListaDeCuentasBancarias("", true, BANCOS_CUENTA_PREFERENTE)->get("TR.Banco de Deposito", true) );
$xFRM->addHElem( $HFecha->get("TR.Fecha de Deposito") );
$xFRM->addHElem( $txt->getDeMoneda("idmontodeposito", "TR.Monto Depositado", 0) );
$xFRM->addObservaciones();

$xFRM->addHTML('<hr id="divavisos" /><div id="reports"></div><input type="hidden" id="idcredito" /><div id="cbzafutura"></div>');
$xFRM->addAviso("", "idmsgs");
echo $xFRM->get();
?>
<script>
var vId				= "";
var xg				= new Gen();
var xG				= new Gen();
var tipoPago		= null;//"transferencia";
var banco			= null;//"99";
var fdeposit		= null;//"2014-01-01";
var idsumacbza		= $("#idsumacbza");
var idFortips		= "#divavisos";
var idFortips2		= "#reports";
var xCred			= new CredGen();
var xEmp			= new EmpGen();
var ordenCbza		= {};
ordenCbza.items		= 0;
ordenCbza.fails		= 0;

var sumaOriginal	= 0;
var numOriginal		= 0;
var mNominaAfect	= false;

function jsResetCbza() {
    idsumacbza.val(0);
    ordenCbza		= {};
    ordenCbza.items	= 0;
    ordenCbza.fails	= 0;
    vId				= "";
    tipoPago		= null; //"transferencia";
    banco			= null; //"99";
    fdeposit		= null; //"2014-01-01";
    $("#idfecha-0").pickadate({format: 'dd-mm-yyyy',formatSubmit:'yyyy-mm-dd'});
    $("#idmontodeposito").val(0);
    $("#octl").html("");
    $(idFortips2).qtip("hide");
    idsumacbza.val(0);
    sumaOriginal	= 0;
    numOriginal		= 0;
    mNominaAfect	= false;
}

function jsSetCobranza(){ getModalTip(idFortips2, $("#itesofe"), "Datos del Pago"); }
function jsCargarDatosIniciales(){ jsaGetDatosEmpresa(); }
function jsCancelLockPeriodo(){ $(idFortips).qtip("hide"); }
function getPlanDePagos(credito){ xg.w({ url : "../rpt_formatos/rptplandepagos.php?credito=" + credito,	w : 800, h: 600, tiny : false }); }
function jsSetLockPeriodo(){
	var idnumerocta	= "#idcodigodecuenta";
    var monto		= redondear(idsumacbza.val());
    var montodep	= redondear($("#idmontodeposito").val());
	tipoPago		= $("#idtipo_pago").val();
	fdeposit		= $("#idfecha-0").val();
	banco			= $(idnumerocta).val();
	
	var success		= true;
	if(ordenCbza.fails > 0){
		success		= false;
		alert("La orden de Cobranza continene ERRORES!");
	}
	if ( flotante(montodep) <= 0 ) {
		success		= false;
		alert("El Monto no puede quedar en Cero");
		$("#idmontodeposito").focus();
		$("#idmontodeposito").select();
	}
	if ( montodep < monto ) {
		success		= false;
		alert("El Monto del Deposito(" + montodep + ") no puede ser menor al de Cobranza(" + monto + ")");
		$("#idmontodeposito").focus();
		$("#idmontodeposito").select();
	}
	//console.log(">>>>" + tipoPago + "<<<<<>>>>" + TESORERIA_COBRO_TRANSFERENCIA + "<<<<<<<<<<<");
    if (tipoPago == TESORERIA_COBRO_EFECTIVO||tipoPago == TESORERIA_COBRO_TRANSFERENCIA) {
		if(tipoPago == TESORERIA_COBRO_TRANSFERENCIA){
			if (entero(banco) == 99) {
			    success	= false;
				alert("El banco debe ser diferente al numero por DEFECTO");
				$(idnumerocta).focus();
			}
		}
    } else {
		success = false;
		xG.alerta({msg:"Solo pago en EFECTIVO o DEPOSITO, No se acepta : " + tipoPago, type:"warn"});
		$("#idtipo_pago").focus();
	}
	if(monto <= 0){
		success = false;
		xG.alerta({msg:"No hay monto para cobrar : " + monto, type:"warn"});
		//$("#idtipo_pago").focus();		
	}
	if(mNominaAfect == true){
		success	= false;
		xG.alerta({msg:"La nomina ya se ha afectado", type:"warn"});
	}
		
    if (success == true) {
		var sip			= prompt("DESEA GUARDAR LAS OPERACIONES POR " + monto + " COMO COBROS EFECTIVOS?\nCONFIRME ESCRIBIENDO 'SI'.");
		if (sip == "SI") {
			xg.dis("#idguardarcobro");
			//agregar deposito
			var goDeposit	= false;
			
			//Agregar Periodo de la Empresa
			var fin	= 0;
			
			  for (var itms in ordenCbza) {
				//console.log("Cobrar " + itms + " Ahora");
				var mObj	= processMetaData("#" + itms);
				if(typeof mObj.monto != "undefined"){
					if(fin ==0){$(document.body).spin("modal");}
					goDeposit	= true;
					fin++;
					getPago(mObj, fin);
				}
			  }
			console.log("Iniciar proceso de Cobranza");
			//if(goDeposit == true){ jsaSetGuardarDeposito(); }
				//xg.disTime("#idguardarcobro");
		}
    } else {
    	xG.alerta({msg:"Revise sus datos porque necesita estar seguro de lo que hara", type:"warn"});
		jsCancelLockPeriodo();
	}
}
function jsSetParaCobros(id) {
	var mObj	= processMetaData("#tr-creditos_solicitud-" + id);
	xCred.goToCobrosDeCredito({credito:mObj.credito, periodo: mObj.letra});
}
function jsSetAlimentarCobros(obj, id) {
	var mObj	= processMetaData("#tr-creditos_solicitud-" + id);
	if (obj.checked == true) {
		ordenCbza["tr-creditos_solicitud-" +  id] = mObj.monto; //crea el objeto con el monto
		//setLog("ID: " + id + " $ " + mObj.monto);
		xCred.getCompareLetra({ credito : id, periodo : mObj.letra, monto : mObj.monto, callback : jsPagoRegistrarError });
		var tt		= flotante(idsumacbza.val()) + flotante(mObj.monto);
		if(flotante(mObj.monto) > 0){
			idsumacbza.val(redondear(tt) );
			$("#sum-monto").html( getInMoney(tt) );
			$("#idmontodeposito").val(tt);
			ordenCbza.items++;
		}
	} else {
		delete ordenCbza["tr-creditos_solicitud-" +  id];
		//alert(typeof ordenCbza["tr-creditos_solicitud-" +  id]);
		var tt		= flotante(idsumacbza.val()) - flotante(mObj.monto);
		jsUpdateSumas(tt);
		ordenCbza.items--;
	}
	var mDifOriginal	= idsumacbza.val() - sumaOriginal;
	//xG.alerta({ message:"Monto Original: " + sumaOriginal + ", Monto de la Cobranza " + idsumacbza.val()  + ", Diferencia " + getInMoney(mDifOriginal) });
	setLog("Monto Original: " + sumaOriginal + ", Monto de la Cobranza " + idsumacbza.val()  + ", Diferencia " + getInMoney(mDifOriginal));
}
function jsUpdateSumas(tt){
	tt = flotante(tt);
	idsumacbza.val( redondear(tt) );
	$("#sum-monto").html( getInMoney(tt) );
	$("#idmontodeposito").val(redondear(tt) );	
}
function jsPagoRegistrarError(err, credito){ 
	if(err == true){	
		$("#chk" + credito).attr('checked', false);
		var idx 	= document.getElementById("chk" + credito);
		delete ordenCbza["tr-creditos_solicitud-" +  credito];
		jsSetAlimentarCobros(idx, credito)
	} 
}
function getPago(mObj, itms, callback){
    if (typeof mObj.credito == "undefined")  {
		console.log("ERROR\tError en la asignacion del credito\n");
    } else {
	var credito			= mObj.credito;
	var claveSocio		= mObj.persona;
	var parcialidad		= mObj.letra;
	var montoLetra		= mObj.monto;
	var periodo			= $.trim($("#idperiodo").val());
	var periocidad		= $("#idperiocidad").val();
	var mcallback		= (typeof callback == "undefined")? function(){} : callback;
	var isCheck			= $('#chk' + credito).prop('checked');
	//$("#chk" + credito).attr('checked', false);
	//socio|solicitud|parcialidad|[deprecated]periocidad|monto a operar|[optional]operacion
	//tipo-de-pago|banco|fecha-de-deposito
		if(flotante(montoLetra) > 0 && isCheck == true){
			var url	= "../frmcaja/frmpagoprocesado.php?p=" + claveSocio + "|" + credito + "|" + parcialidad + "|" + montoLetra + "|plc|" + tipoPago + "|" + banco + "|" + fdeposit + "&procesar=automatico&periodoempresa=" + periodo + "&periocidad=" + periocidad;
			//setLog(url);
			$.ajax({
			    url: url, // relative path to www folder
			    type: "get",
			    contentType: "xml",
			    success: function(xml){
				//var ready	= false;
				$(xml).find("resultados").each(function(index){
				    if(String($.trim($(this).text())).indexOf("ERROR.CANCELADO") != -1 ){
						xG.alerta({message: "La parcialidad " + parcialidad + " del Credito " +  credito + " no se cobra"});
						var tt	= flotante($("#idmontodeposito").val()) - montoLetra;
						jsUpdateSumas(tt);
						$("#options-" + credito).parent().addClass("tr-error");
					} else {
						$("#options-" + credito).parent().addClass("tr-pagar");
						//Eliminar cache
						session(credito +  "." + parcialidad, null);
						//deshabilitar control
						mNominaAfect		= true;
					}
				    
				    if (itms == ordenCbza.items) {
						$(document.body).spin("modal").stop();				//spin
						xg.spin({ callback: getCorteDeRecibos, time : 8000 });
						mcallback;
						setTimeout("jsaSetGuardarDeposito()", 8000);
				    }
				});
			    }
			});
		} else {
			xG.alerta({message: "La parcialidad " + parcialidad + " del Credito " +  credito + " no se cobra por Monto " + montoLetra, type:"warn"});
		}
    }
}
function getEstadoDeCuenta(idcredito) { var url	= "../rpt_edos_cuenta/rptestadocuentacredito.php?credito=" + idcredito ;    xg.w({ url : url, full:true }); }
function getCorteDeRecibos(){
	xg.ena("#idguardarcobro");
    $("#octl").html('<a onclick="getCorteDeRecibos()"><img src="../images/cash_stack_add.png" />Obtener Corte<mark id="saldocorte"></mark></a>');
    var iddep		= $("#idcodigodeempresas").val();
    var ff			= $("#idfecha-0").val();
    var url			= "../rpttesoreria/rpt_caja_corte_sobre_recibos.php?dependencia=" + iddep + "&on=" + ff + "&off=" + ff;
    xg.w({ url : url, full:true });
    
}
function setOcultar(id) {    $("#options-" + id).parent().css("display", "none"); }
function jsGetCobranza(){
    $("#idobservaciones").focus();
    tip(idFortips, "Carga completa!", 4500);
    jsResetCbza();
    jsaGetDatosDelEnvio();
    jsaGetCobranza();
    //jsaGetCobranzaFutura();
	//establecer Numero y monto original
	setTimeout("setEstablacerSumasIniciales()",1000);
}
function setEstablacerSumasIniciales(){
	if ( $("#sum-monto").length > 0 ) {
		sumaOriginal	= flotante($("#sum-monto").html());
		numOriginal		= entero( $("#sum-letra").html() );
	}
}

function jsPrintEstadoCuenta(){
	var idemp	= $("#idcodigodeempresas").val();
	var idper	= $("#idperiodo").val();
	var perio	= $("#idperiocidad").val();
	xg.w({
		url : "../rptempresas/empresas.movimientos.rpt.php?empresa=" + idemp + "&periodo=" + idper + "&periocidad=" + perio
		});
}
function jsSaveExcel() {    tableToExcel( document.getElementById("sqltable")); }
function jsGetCobranzaDay(){ getCorteDeRecibos(); }
function getRecibo(){
    var idr		= $("#idcodigodeempresas").val();
    var per		= $("#idperiocidad").val();
    var vari	= $("#idvariacion").val();
    var obs 	= $("#idobservaciones").val();
    var peri	= $("#idperiodo").val();
    var iF1		= $("#idfecha-10").val();
    var iF2		= $("#idfecha-11").val();
        
    var url	= "../rptcreditos/orden_de_cobranza.recibos.rpt.php?r=" + idr + "&p=" + per + "&v=" + vari + "&o=" + obs + "&periodo=" + peri + "&on=" + iF1 + "&off=" + iF2;
    xg.w({ url : url, w : 800, h : 600 });
}
function generarPlanDePagos(credito) {
    var sURL = '../frmcreditos/frmcreditosplandepagos.php?r=1&c=' + credito;
    xg.w({ url : sURL, w : 800, h : 600 });
    $("#options-" + credito).parent().addClass("tr-plan");
}
function jsGetRecibosByCredito(credito) {
    var mObj	= processMetaData("#tr-creditos_solicitud-" + credito);
    var ht		= "";
    var myId	= "#pk-" + credito;
    var ff	= $("#idfecha-0").val();
    
    	xg.pajax({
		url: "../frmoperaciones/recibos.svc.php?persona=" + mObj.persona + "&documento=" + mObj.credito + "&mx=true&fecha=" + ff,
		finder: "recibo",
		callback : function(obj, final){
			ht	+= "<a>RECIBO :" +  $(obj).attr("codigo") + " - Monto :<mark>" + $(obj).text() + "</mark></a></br>";
			if (final == true) {
				tipSuggest(myId, "" + ht + "");
			}
		}
		});
}
function jsGetEdoCuentaGeneral(){	var id = $("#idcodigodeempresas").val(); xEmp.getEstadoDeCuenta(id); }
function jsListaDeNominas(){ var id = $("#idperiodo").val();  xEmp.getOrdenDeCobranza(id);	}
function jsSetCerrarNomina(){
	xG.confirmar({
		msg: "Al cerrar la Nomina las operaciones se cancelan, no se muestran de nuevo",
		callback:  jsaSetCerrarNomina
		});
}
</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>