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
$xHP				= new cHPage("TR.Calendario de Tareas", HP_FORM);
$oficial 			= elusuario($iduser);
$jxc 				= new TinyAjax();
$xF					= new cFecha();
$xBtn				= new cHButton("");
$xLoc				= new cLocal();
$xLi				= new cSQLListas();


function jsaRespaldarDB($fecha){ $xSys	= new cSystemTask(); $msg	= $xSys->setBackupDB(); return $msg; }
function jsaSetCumplido($Key){ $sql = "UPDATE usuarios_web_notas SET estado=40 WHERE idusuarios_web_notas=$Key"; my_query($sql); }
function jsaEliminarLog($fecha){ $xQL	= new MQL(); $xQL->setRawQuery("DELETE FROM general_log");}
function jsaShowCalendarTasks($date){
	$xD			= new cFecha();
	$date 		= $xD->getFechaISO($date);
	$xLi		= new cSQLListas();
	$cTbl		= new cTabla($xLi->getListadoDeTareas(getUsuarioActual(), $date));
	$cTbl->setKeyField("idusuarios_web_notas");
	$cTbl->setKeyTable("usuarios_web_notas");
	$cTbl->OButton("TR.Checado", "setUpdateEstatus(_REPLACE_ID_)", $cTbl->ODicIcons()->OK);
	return  $cTbl->Show("TR.Tareas");
}
function jsaGetIngresosDeldia($fecha){
	$xD		= new cFecha();
	$fecha 	= $xD->getFechaISO($fecha);
	$sql	= new cSQLListas();
	$xT		= new cTabla($sql->getSumaDeIngresosPorFechas($fecha, $fecha));
	$xT->setKeyField($sql->getClave());
	$xT->setPrepareChart();
	$xT->setFootSum(false);
	return $xT->Show("Reporte de Ingresos", true, "tingresos");
}
function jsaGetIngresosDelMes($fecha){
	$xD		= new cFecha();
	$fecha 	= $xD->getFechaISO($fecha);
	$fi		= $xD->getDiaInicial();
	$ff		= $xD->getDiaFinal();
	$sql	= new cSQLListas();
	$xT		= new cTabla($sql->getSumaDeIngresosPorFechas($fi, $ff));
	$xT->setKeyField($sql->getClave());
	$xT->setPrepareChart();
	$xT->setFootSum(false);
	return $xT->Show("Reporte de Ingresos Mensuales", true, "tingresos");
}
function jsaGetIngresosMensualesPorDependencias($fecha){
	$xD		= new cFecha();
	$fecha 	= $xD->getFechaISO($fecha);
	$fi		= $xD->getDiaInicial();
	$ff		= $xD->getDiaFinal();
	$sql	= new cSQLListas();
	$xT		= new cTabla($sql->getBasesPorFechasPorDependencia($fi, $ff, 2002));
	$xT->setKeyField($sql->getClave());
	$xT->setPrepareChart();
	$xT->setFootSum(false);
	return $xT->Show("Reporte de Ingresos Mensuales por Empresas", true, "tingresos");
}
function jsaGetLetrasAVencer($fecha, $producto){
	$xD		= new cFecha();
	$xL		= new cSQLListas();
	$fecha 	= $xD->getFechaISO($fecha); //AND (`creditos_tipoconvenio`.`tipo_en_sistema` =" . CREDITO_PRODUCTO_INDIVIDUAL . ")
	$filtro	= " AND (`creditos_solicitud`.`saldo_actual`> " . TOLERANCIA_SALDOS .  ")  AND (`creditos_tipoconvenio`.`omitir_seguimiento` =0) ";
	$sql	= $xL->getListadoDeLetrasConCreditos($fecha, false, "", "", $filtro, $producto);
	
	$sql2	= "SELECT `socios`.`codigo`,
	`socios`.`nombre`,
	`creditos_solicitud`.`numero_solicitud` AS `credito`,
	`creditos_solicitud`.`numero_pagos` AS `parcialidad`,
	CONCAT(DATE_FORMAT('$fecha', '%Y-%m-'), DATE_FORMAT(fecha_ministracion, '%d'))
	AS `fecha_de_pago`,
	`creditos_solicitud`.`saldo_actual` AS `capital`,
	setNoMenorCero(`creditos_solicitud`.`interes_normal_devengado` -
	`creditos_solicitud`.`interes_normal_pagado`) AS `interes`,
	setNoMenorCero(`creditos_solicitud`.`iva_interes`) AS `iva`,
	setNoMenorCero(`creditos_solicitud`.`gastoscbza` -
	`creditos_solicitud`.`bonificaciones` +  `creditos_solicitud`.`iva_otros` ) AS `otros`
	FROM     `creditos_solicitud`
	INNER JOIN `socios`  ON `creditos_solicitud`.`numero_socio` = `socios`.`codigo`
	INNER JOIN `creditos_a_final_de_plazo`  ON `creditos_solicitud`.`numero_solicitud` = `creditos_a_final_de_plazo`.`credito`
	WHERE    ( `creditos_solicitud`.`saldo_actual` >" . TOLERANCIA_SALDOS . " )	AND (DATE_FORMAT(`fecha_ministracion`, '%d')=DATE_FORMAT('$fecha', '%d'))

	";
	
	//$sql	= "$sql UNION $sql2";
	//setLog($sql);
	
	$xT		= new cTabla($sql, 2);
	$xT->setWithMetaData();
	$xT->setEventKey("jsGoPanel");
	$xT->OButton("TR.PAGO", "jsGoToCaja(" . HP_REPLACE_ID . ")", $xT->ODicIcons()->COBROS);
	$arrSum	= array( );
	if(MODULO_CAPTACION_ACTIVADO == false){
		$xT->setOmitidos("ahorro");
	}
	$xT->setFootSum( $arrSum );
	
	$t1	= $xT->Show();
	
	
	$xT2	= new cTabla($sql2, 2);
	$xT2->setWithMetaData();
	$xT2->setEventKey("jsGoPanel");
	$xT2->OButton("TR.PAGO", "jsGoToCaja2(" . HP_REPLACE_ID . ")", $xT2->ODicIcons()->COBROS);
	$xT2->setFootSum( array( ) );
	
	$t1	.= $xT2->Show();
	
	
	return $t1;
}
function jsaGetLetrasVencidas($fecha, $producto){
	$xD		= new cFecha();
	$xL		= new cSQLListas();
	$fecha 	= $xD->getFechaISO($fecha);
	$xFil	= new cSQLFiltros();
	
	$BySaldo		= $xFil->CreditosPorSaldos(TOLERANCIA_SALDOS, ">");
	//Agregar seguimiento
	$BySaldo		= $BySaldo . $xFil->CreditosProductosPorSeguimiento(0);	
	$sql	= $xL->getListadoDeLetrasPendientesReporteAcum($BySaldo, TASA_IVA, true, false, $producto);

	$xT		= new cTabla($sql, 2);
	$xT->setEventKey("jsGoPanel");
	$xT->setWithMetaData();
	$xT->OButton("TR.PAGO", "jsPagoCajaCompleto(" . HP_REPLACE_ID . ")", $xT->ODicIcons()->COBROS);
	
	return $xT->Show( );
}

function jsaGetCreditosPorAutorizar($fecha){
	$xD		= new cFecha();
	$fecha 	= $xD->getFechaISO($fecha);
	$xDT	= new cHDicccionarioDeTablas();
	return $xDT->getCreditosPorAutorizar($fecha);	
}

function jsaGetCreditosPorMinistrar($fecha){
	$xDT	= new cHDicccionarioDeTablas();
	return $xDT->getCreditosPorMinistrar($fecha);
}

/*function jsaGetLetrasAVencerTodas($fecha){
	$xDT	= new cHDicccionarioDeTablas();
	return $xDT->getCreditosPorAutorizar($fecha);
}*/

function jsaGetLog($fecha){
	$xList	= new cSQLListas();
	$sql	= $xList->getListadoDeEventos("", "", SYS_LOG_NIVEL_DEV);
	$xT	= new cTabla($sql); 
	$xT->addTool(SYS_DOS);
	$xT->setKeyField("idgeneral_log");
	//Agregar el script delete
	return $xT->Show("TR.Log", true, "tablelog");
}

function jsaGetRecibosEmitidos($fecha){
	$xF		= new cFecha();
	$fecha	= $xF->getFechaISO($fecha);
	$xL		= new cSQLListas();
	$otros	= (MODO_DEBUG == true) ? "" : "";
	
	$sql	= $xL->getListadoDeRecibos("", "", "", $fecha, $fecha, $otros);
	//setLog($sql);
	
	$xT		= new cTabla($sql); 
	$xT->setEventKey("jsGetPanelRecibo");
	$xT->setFootSum(array(
		6 => "total"	
	));
	return $xT->Show();
}


function jsaActualizarIdioma($fecha){
	$xSys	= new cSystemPatch();
	$msgs	= $xSys->patch(true, false, true);
	$xCache	= new cCache();
	$xCache->clean();
	return $msgs;
}

function jsaActualizarIngresos(){
	$xQL	= new MQL();
	$xQL->setRawQuery("CALL `proc_listado_de_ingresos`;");
}
function jsaActualizarProyeccionMensual($Fecha){
		$xProy		= new cCreditosProyecciones();
		$xProy->addProyeccionMensual($Fecha, $xProy->PROY_SISTEMA, SYS_TODAS);
}

function jsaListaPeriodosDeEmpresa($fecha){
	$xLi	= new cSQLListas();
	$xTabla	= new cTabla($xLi->getListadoDePeriodoPorEmpresa(false, false, false, false, false, $fecha));
	$xTabla->setOmitidos("periocidad");
	
	return $xTabla->Show();
}
function jsaListaPeriodosDeEmpresaEmitidos($fecha){
	$xLi	= new cSQLListas();
	$xTabla	= new cTabla($xLi->getListadoDePeriodoPorEmpresa(false, false, false, $fecha));
	$xTabla->setOmitidos("periocidad");

	return $xTabla->Show();
}
$jxc ->exportFunction('jsaShowCalendarTasks', array('idDateValue'), "#tcalendar-task");

$jxc ->exportFunction('jsaGetLetrasAVencer', array('idDateValue', 'idproducto'), "#tcalendar-task");
$jxc ->exportFunction('jsaGetLetrasVencidas', array('idDateValue', 'idproducto'), "#tcalendar-task");


$jxc ->exportFunction('jsaGetCreditosPorAutorizar', array('idDateValue', 'idproducto'), "#tcalendar-task");
$jxc ->exportFunction('jsaGetCreditosPorMinistrar', array('idDateValue', 'idproducto'), "#tcalendar-task");
$jxc ->exportFunction('jsaEliminarLog', array('idDateValue'), "#tcalendar-task");
$jxc ->exportFunction('jsaGetLog', array('idDateValue'), "#tcalendar-task");
$jxc ->exportFunction('jsaGetRecibosEmitidos', array('idDateValue'), "#tcalendar-task");

$jxc ->exportFunction('jsaSetCumplido', array('id-KeyEditable'));

$jxc ->exportFunction('jsaGetIngresosDeldia', array('idDateValue'), "#tcalendar-task");
$jxc ->exportFunction('jsaGetIngresosDelMes', array('idDateValue'), "#tcalendar-task");
$jxc ->exportFunction('jsaGetIngresosMensualesPorDependencias', array('idDateValue'), "#tcalendar-task");

$jxc ->exportFunction('jsaRespaldarDB', array('idDateValue'), "#avisos");
$jxc ->exportFunction('jsaActualizarIdioma', array('idDateValue'), "#avisos");
$jxc ->exportFunction('jsaActualizarIngresos', array('idDateValue'), "#avisos");
$jxc ->exportFunction('jsaListaPeriodosDeEmpresa', array('idDateValue'), "#tcalendar-task");
$jxc ->exportFunction('jsaListaPeriodosDeEmpresaEmitidos', array('idDateValue'), "#tcalendar-task");

$jxc ->exportFunction('jsaActualizarProyeccionMensual', array('idDateValue'), "#avisos");

//jsaRespaldarDB
$jxc ->process();

$xHP->addChartSupport();
$xHP->init();

$xFRM		= new cHForm("frmcalendartask");
$xUsr		= new cSystemUser();
$xNotif		= new cHNotif();
$alerts		= "";
$xSel		= new cHSelect();
$xUsr->init();
$xFRM->addSeccion("idsoptions", "TR.Opciones");
	$xFRM->ODate("idDateValue", false, "TR.FECHA DE FILTRO");
	$xSelC		= $xSel->getListaDeProductosDeCredito();
	$xSelC->addEspOption(SYS_TODAS);
	$xSelC->setOptionSelect(SYS_TODAS);
	$xFRM->addHElem($xSelC->get(true));
	$xFRM->OMoneda("idclave", 0, "TR.CLAVE");
$xFRM->endSeccion();
$xFRM->addSeccion("idsexavisos", "TR.AVISOS");
$xFRM->addHElem("<div id=\"tcalendar-task\"></div>");

$xFRM->setTitle($xHP->getTitle());

//if(getEsModuloMostrado(USUARIO_TIPO_CAJERO)){

	//$xFRM->addToolbar($xBtn->getBasic("Ingresos del Dia", "jsGetChart()", "grafico", "idcharts", false) );
//} 

$xFRM->OButton("TR.Tareas", "jsGetInformes()", "tarea", "idtareas");

if(MODULO_AML_ACTIVADO == true){
	$xFRM->OButton("TR.Buscar en Lista_Negra", "var xP= new PersGen(); xP.setBuscarEnListas()", $xFRM->ic()->BUSCAR, "idtareas");
}

if($xUsr->getNivel() != USUARIO_TIPO_OFICIAL_AML  OR (MODO_DEBUG == true) ){
$xCEs	= new cCreditosEstadisticas();
	$xFRM->addHElem( $xNotif->getDash("TR.Clientes con Creditos", $xCEs->getNumeroClientesConCredito(), $xFRM->ic()->PERSONA, $xNotif->NOTICE) );
	if($xUsr->getNivel() >= USUARIO_TIPO_OFICIAL_CRED OR (MODO_DEBUG == true) OR (OPERACION_LIBERAR_ACCIONES == true)){
		$xFRM->addToolbar($xBtn->getBasic("TR.Pagos FECHA ACTUAL", "jsaGetLetrasAVencer()", "reporte", "idletrav", false) );
		$xFRM->addToolbar($xBtn->getBasic("TR.Pagos Vencidos", "jsaGetLetrasVencidas()", "reporte", "idletrav", false) );
		//$xFRM->addToolbar($xBtn->getBasic("TR.Letras Pendientes", "jsaGetLetrasAVencerTodas()", "reporte", "idletrave", false) );
		//$xFRM->addToolbar($xBtn->getBasic("TR.Creditos Simples", "jsaGetCreditosSimplesMinistrados()", "lista", "idsimplev", false) );
		//$xFRM->OButton("TR.Creditos Por Autorizar", "jsaGetCreditosPorAutorizar()", $xFRM->ic()->LIBERAR, "idcredd");
		
		$xFRM->addToolbar($xBtn->getBasic("TR.Creditos Por Autorizar", "jsaGetCreditosPorAutorizar()", "lista", "idcredaut", false) );
		$xFRM->addToolbar($xBtn->getBasic("TR.Creditos Por Ministrar", "jsaGetCreditosPorMinistrar()", "lista", "idcrednpoaut", false) );
		$xFRM->OButton("TR.Recibos Emitidos", "jsaGetRecibosEmitidos()", $xBtn->ic()->REPORTE);
	}
	if($xUsr->getNivel() >= USUARIO_TIPO_GERENTE OR (OPERACION_LIBERAR_ACCIONES == true)){
		//$xFRM->addToolbar($xBtn->getBasic("TR.Ingresos del Dia", "jsGetChart()", "grafico", "idcharts", false) );
		//$xFRM->addToolbar($xBtn->getBasic("TR.Ingresos del Mes", "jsGetIngresosMensuales()", "grafico", "idimes", false) );
		$xFRM->OButton("TR.Actualizar Proyeccion Mensual de Credito", "jsaActualizarProyeccionMensual()", $xFRM->ic()->RECARGAR);
	}
	$xChCred		= new cChart("idchartcredito");
	$xChCred->addData($xCEs->getNumeroCreditosPorAutorizar(), "TR.Creditos Por Autorizar" );
	$xChCred->addData(0,"TR.Creditos Por Autorizar");
	
	
	$xChCred->addData(0,"TR.Creditos Por Ministrar");
	$xChCred->addData($xCEs->getNumeroCreditosPorMinistrar(),"TR.Creditos Por Ministrar");
	
	$xChCred->setProcess($xChCred->BAR);

	$xFRM->addHElem($xChCred->getDiv());
	$xFRM->addJsInit($xChCred->getJs());
	//$xFRM->addHElem( $xNotif->getDash("TR.Creditos Por Autorizar", $xCEs->getNumeroCreditosPorAutorizar(), "fa-circle", $xNotif->SUCCESS) );
	//$xFRM->addHElem( $xNotif->getDash("TR.Creditos Por Ministrar", $xCEs->getNumeroCreditosPorMinistrar(), "fa-circle-o", $xNotif->NOTICE) );
	
}
//==================== Proyecciones del Sistema
$xChProy	= new cChart("idproymens");
$xProy		= new cCreditosProyecciones();

if($xProy->getProyeccionMensual(fechasys(), $xProy->PROY_SISTEMA, SYS_TODAS) == true){
	$xChProy->addData($xProy->getCapital(), "TR.CAPITAL");
	$xChProy->addData(0, "TR.CAPITAL");
	$xChProy->addData(0, "TR.CAPITAL");
	$xChProy->addData(0, "TR.CAPITAL");
	$xChProy->addData(0, "TR.CAPITAL");
	
	$xChProy->addData(0, "TR.Interes");
	$xChProy->addData($xProy->getInteres(), "TR.Interes");
	$xChProy->addData(0, "TR.Interes");
	$xChProy->addData(0, "TR.Interes");
	$xChProy->addData(0, "TR.Interes");
	
	$xChProy->addData(0, "TR.IVA");
	$xChProy->addData(0, "TR.IVA");
	$xChProy->addData($xProy->getIVA(), "TR.IVA");
	$xChProy->addData(0, "TR.IVA");
	$xChProy->addData(0, "TR.IVA");
	
	$xChProy->addData(0, "TR.Otros");
	$xChProy->addData(0, "TR.Otros");
	$xChProy->addData(0, "TR.Otros");
	$xChProy->addData($xProy->getOtros(), "TR.Otros");
	$xChProy->addData(0, "TR.Otros");
	
	$xChProy->addData(0, "TR.Total");
	$xChProy->addData(0, "TR.Total");
	$xChProy->addData(0, "TR.Total");
	$xChProy->addData(0, "TR.Total");
	$xChProy->addData($xProy->getTotal(), "TR.Total");
	
	$xChProy->setFuncConvert("enmiles");
	$xChProy->setProcess($xChProy->BAR);
	$xFRM->addHElem($xChProy->getDiv());
	$xFRM->addJsInit($xChProy->getJs());
}
//$xFRM->addHElem( $xNotif->get("Nada") );
//$xFRM->addHElem( $xNotif->getDash("Creditos", "1000", "info", $xNotif->WARNING) );
//$xFRM->addHElem( $xNotif->getDash("Pagos", "1400", "info", $xNotif->SUCCESS) );
//$xFRM->addHElem("<div class='tx4'><h3>HOLA</h3> ");
$xUsrE		= new cUserEstadisticas(getUsuarioActual());
$xTE		= new cTesoreriaEstadisticas();
//$xFRM->addHElem( $xNotif->getDash("TR.CAJAS_ABIERTAS", $xTE->getNumeroCajasAbiertas(), "moneda", $xNotif->NOTICE) );
//$xFRM->addHElem( $xNotif->getDash("TR.Tareas pendientes", $xUsrE->getNumeroTareasPendientes(), "tarea", $xNotif->SUCCESS) );
$xChUser	= new cChart("idchartuser");
$xChUser->addData($xUsrE->getNumeroTareasPendientes(), "TR.Tareas pendientes");

$xChUser->addData(0, "TR.CAJAS_ABIERTAS");
$xChUser->addData($xTE->getNumeroCajasAbiertas(), "TR.CAJAS_ABIERTAS");

//Checar si las caja del usuario está abierta
$xCaja		= new cCaja();
$OnCaja		= false;
if($xCaja->initByFechaUsuario(fechasys(), getUsuarioActual()) == true){
	if($xCaja->getEstatus() == TESORERIA_CAJA_ABIERTA){
		//cerrar Caja
		$xFRM->OButton("TR.CERRAR CAJA", "var xG=new Gen();xG.w({url:'../frmcaja/cerrar_caja.frm.php?',tab:true});", $xFRM->ic()->CERRAR);
		$OnCaja	= true;
	}
}
if(getEsModuloMostrado(false, MMOD_TESORERIA) == true){
	$xFRM->OButton("TR.NOMINAS POR COBRAR", "jsaListaPeriodosDeEmpresa", $xFRM->ic()->REPORTE2);
	$xFRM->OButton("TR.NOMINAS ENVIADAS", "jsaListaPeriodosDeEmpresaEmitidos", $xFRM->ic()->REPORTE3);
}
if(getEsModuloMostrado(false, MMOD_TESORERIA) == true AND $OnCaja == false){
	$xFRM->OButton("TR.ABRIR_SESSION DE CAJA", "var xG=new Gen();xG.w({url:'../frmcaja/abrir_caja.frm.php?',tab:true})", $xFRM->ic()->COBROS);
	//Agregar Lista de Nominas Enviadas
}



if( $xUsr->getNivel() == USUARIO_TIPO_OFICIAL_AML OR (MODO_DEBUG == true)  ) {
	$xAEs	= new cAMLEstadisticas();
	//$xFRM->addHElem( $xNotif->getDash("TR.Alertas pendientes", $xAEs->getNumeroAlertasPendientes(), "info", $xNotif->WARNING) );
	//$xFRM->addHElem( $xNotif->getDash("TR.Alertas por enviar", $xAEs->getNumeroRiesgosPorReportar(), "alerta", $xNotif->ERROR) );
	
	$xChUser->addData(0, "TR.Tareas pendientes");
	$xChUser->addData(0, "TR.Tareas pendientes");
	$xChUser->addData(0, "TR.Tareas pendientes");
	
	$xChUser->addData(0, "TR.CAJAS_ABIERTAS");
	$xChUser->addData(0, "TR.CAJAS_ABIERTAS");
	
	$xChUser->addData(0, "TR.Alertas pendientes");
	$xChUser->addData(0, "TR.Alertas pendientes");
	$xChUser->addData($xAEs->getNumeroAlertasPendientes(), "TR.Alertas pendientes");
	$xChUser->addData(0, "TR.Alertas pendientes");
	
	$xChUser->addData(0, "TR.Alertas por enviar");
	$xChUser->addData(0, "TR.Alertas por enviar");
	$xChUser->addData(0, "TR.Alertas por enviar");
	$xChUser->addData($xAEs->getNumeroRiesgosPorReportar(), "TR.Alertas por enviar");
	
	
}
$xChUser->setAlto("300");
$xChUser->setProcess($xChUser->BAR);

$xFRM->addHElem($xChUser->getDiv());
$xFRM->addJsInit($xChUser->getJs());

$xFRM->setNoAcordion();
if(MODO_DEBUG == true){
	$xFRM->addToolbar($xBtn->getBasic("ELiminar LOG", "jsaEliminarLog()", "grafico", "idlog", false) );
	$xFRM->addToolbar($xBtn->getBasic("Obtener LOG", "jsaGetLog()", "grafico", "idglog", false) );
	$xFRM->addToolbar($xBtn->getBasic("TR.Respaldo", "jsaRespaldarDB()", "ejecutar", "idrespdb", false) );
	$xFRM->OButton("TR.Actualizar Idioma", "jsaActualizarIdioma()", $xFRM->ic()->EJECUTAR);
}
//$xF->dia() < 10 AND
if( getUsuarioActual(SYS_USER_NIVEL) >= USUARIO_TIPO_CONTABLE){
	$xFRM->OButton("TR.Actualizar Ingresos", "jsActualizarIngresos()", $xFRM->ic()->REPORTE4);
}

$xTask			= new cSystemTask();
$xTask->setProcesarTareas();
$alerts			.= $xTask->getMessages();
$xChart			= new cChart("idivchart");



$xFRM->OButton("TR.CALCULAR PLAN_DE_PAGOS", "jsCalcularPlanPagos()", $xFRM->ic()->CALENDARIO);

$xFRM->OButton("TR.Buscar PERSONA", "jsGoBuscarPersona()", $xFRM->ic()->PERSONA, "", "blue");

$xFRM->OButton("TR.IR PANEL PERSONA", "jsGoPanelPersona()", $xFRM->ic()->PERSONA);
$xFRM->OButton("TR.IR PANEL CREDITO", "jsGoPanelCredito()", $xFRM->ic()->CREDITO);
$xFRM->OButton("TR.IR PANEL RECIBO", "jsGoPanelRecibo()", $xFRM->ic()->RECIBO);

if(getUsuarioActual(SYS_USER_NIVEL)>USUARIO_TIPO_OFICIAL_CRED){
	$xFRM->OButton("TR.Actualizar Letras pendientes", "jsActualizarProcLetras()", $xFRM->ic()->EJECUTAR);
}
$idpersona	= $xUsr->getClaveDePersona();

$xFRM->OButton("TR.VER MI USUARIO", "jsVerMiPassword($iduser)", $xFRM->ic()->PASSWORD, "", "white");
$xFRM->OButton("TR.Salir", "var xG = new Gen(); xG.salir()", $xFRM->ic()->SALIR);

//$xFRM->addSeccion("idmastareas", "TR.Tareas");


$xFRM->endSeccion();



$sysinfo		= "";

if (MODO_DEBUG == true AND (SYSTEM_ON_HOSTING == false)){
	$xUL			= new cHUl(); $xUL2		= new cHUl();
	$sysinfo		=  $xUL->li("Base de Datos:" . MY_DB_IN)->li("Servidor: " . WORK_HOST)->li("Sucursal: " . getSucursal())
	->li("Version S.A.F.E.: " . SAFE_VERSION)->li("Revision S.A.F.E: " . SAFE_REVISION)->li("Path Temporal: " . PATH_TMP)
	->li("Path Backups: " . PATH_BACKUPS)->li("Fecha del Sistema: " . date("Y-m-d H:i:s"))->li("Usuario Activo: " . $xUsr->getNombreCompleto() )->li("ID de Usuario: " . $xUsr->getID())
	->li("Nivel de Usuario: " . $xUsr->getNivel())
	->li("Clave API: " . $xUsr->getCTX())
	->li("SAFE DB version : " . SAFE_DB_VERSION)
	->li("Clave de Oficial AML : " . getOficialAML())
	->end();
	
	$sysinfo2		= $xUL2->li("Caja Local : " . $xLoc->getCajaLocal())
	->li("Localidad : " . $xLoc->DomicilioLocalidad())
	->li("Clave Localidad : " . $xLoc->DomicilioLocalidadClave())
	->li("Municipio : " . $xLoc->DomicilioMunicipio())
	->li("Estado : " . $xLoc->DomicilioEstado())
	->li("Clave Estado : " . $xLoc->DomicilioEstadoClaveABC() )
	->li("C.P. : " . $xLoc->DomicilioCodigoPostal())
	->end();
	
	$xFRM->addSeccion("idmaslogs", "TR.Sistema");
	$xFRM->addDivSolo($sysinfo, $sysinfo2, "tx24", "tx24" );
	$xFRM->endSeccion();
}



$xFRM->addAviso("", "idavisos");
$xFRM->OHidden("id-KeyEditable", "", "");
//$xFRM->addHTML($menu);
echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
?>

<script>
var xG		= new Gen();
var xCred	= new CredGen();
var xP		= new PersGen();
$(document).ready( function(){
	//$('#idDateValue').pickadate({format: 'dd-mm-yyyy',formatSubmit:'yyyy-mm-dd'});
	window.localStorage.clear();
});

function jsGetPanelRecibo(id){	var xR	= new RecGen(); xR.panel(id); }
function jsGetInformes(){
	jsaShowCalendarTasks();
	//jsaGetIngresosDeldia();
}
function jsGetIngresosMensuales() {
	$('#suchart').empty();
	jsaGetIngresosDelMes();
	
	setTimeout("jsGetChart()",4500);
}
function jsGetIngresosMensualesEmpresas() {
	$('#suchart').empty();
	jsaGetIngresosMensualesPorDependencias();
	setTimeout("jsGetChart()",4500);
}
function setUpdateEstatus(id){
	$("#id-KeyEditable").val(id);
	jsaSetCumplido();
	setTimeout("jsaShowCalendarTasks()", 1000);
}
function jsGoPanel(idcredito){
	var xc	= new CredGen();
	xc.goToPanelControl(idcredito);
}
function jsGetChart(mType){
	mType	= (typeof mType == "undefined") ? "bar" : mType;
	$('#tcalendar-task').empty();
	$('#tingresos')
	   .visualize({
		width: SCREENW*0.6,
		height: SCREENH*0.45,
		type : mType,
		barMargin: 2
		})
	   .appendTo('#tcalendar-task')
	   .trigger('visualizeRefresh');
}
function jsCalcularPlanPagos(){
	xG.w({url:"../frmcreditos/calculadora.plan.frm.php?", tiny: false , h: 600, w : 480, tab:true});
}
function jsGoToCaja(id){
	var obj	= processMetaData("#tr-letras-" + id);
	xCred.goToCobrosDeCredito({persona:obj.codigo, credito:obj.credito, periodo: obj.parcialidad});
}
function jsGoToCaja2(id){
	var obj	= processMetaData("#tr-creditos_solicitud-" + id);
	xCred.goToCobrosDeCredito({persona:obj.codigo, credito:obj.credito, periodo: obj.parcialidad});
}
function jsGoAcreditoAutorizacion(id){
	var obj	= processMetaData("#tr-creditos_solicitud-" + id);
	xCred.getFormaAutorizacion(obj.numero_de_solicitud);
}
function jsActualizarIngresos(){
	xG.confirmar({
		msg: "Actualizar Ingresos bloquearia por algunos minutos las operaciones ¿ Desea continuar ?",
		callback: jsaActualizarIngresos
		});
}
function jsGoPanelPersona(){
	var id = $("#idclave").val();
	xP.goToPanel(id);
}
function jsGoPanelCredito(){
	var id = $("#idclave").val();
	xCred.goToPanelControl(id);
}
function jsGoPanelRecibo(){
	var id = $("#idclave").val();
	var xRec	= new RecGen();
	xRec.panel(id);
}
function jsActualizarProcLetras(){
	xG.confirmar({
		msg: "Actualizar Letras Pendientes de pago ¿ Desea continuar ?",
		callback: jsaActualizarProcLetras
		});
}
function jsaActualizarProcLetras(){
	xG.spinInit();
	xG.pajax({
		result:"json",
		url: "../svc/procs-letras.svc.php",
		callback: function(rs){ var xG = new Gen(); xG.spinEnd(); }
		});
}
function jsVerMiPassword(id){ 
	var xrl		= "../frmsocios/socios.usuario.frm.php?usuario=" + id;
	xG.w({ url: xrl, tiny : true }); 	
}
function jsGoBuscarPersona(){
	var xrl		= "../utils/frmbuscarsocio.php?a=1";
	xG.w({ url: xrl, tiny : true });	
}
function jsPagoCajaCompleto(id){
	var obj 	= processMetaData("#tr-creditos_letras_del_dia-" + id);
	xG.w({url:"../frmcaja/abonos-a-parcialidades.frm.php?credito="+id + "&monto=" + obj.total}); 
}
</script>
<?php $xHP->fin(); ?>