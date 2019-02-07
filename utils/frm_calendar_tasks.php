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
$xSys				= new cSystemTask();
$xQL				= new MQL();
$xRuls				= new cReglaDeNegocio();

$noUsarChat			= $xRuls->getValorPorRegla($xRuls->reglas()->RN_NO_USAR_HCHAT);
$SyncApp			= $xRuls->getValorPorRegla($xRuls->reglas()->SYNC_APP);		//regla de negocio
$SyncAML			= $xRuls->getValorPorRegla($xRuls->reglas()->SYNC_AML_MIG);		//regla de negocio

function jsaRespaldarDB($fecha){ 
	$xSys	= new cSystemTask(); 
	$xSys->setBackupDB(); 
	return $xSys->getMessages(); 
}
function jsaSetCumplido($Key){ 
	$xNot	= new cSystemUserNotes($Key);
	if($xNot->init() == true){
		$xNot->setInactivo();
	}
	
	return $xNot->getMessages(OUT_HTML);
}
function jsaEliminarLog($fecha){
	$xQL	= new MQL();
	if(SAFE_ON_DEV == true){
		$xQL->setRawQuery("DELETE FROM general_log");
	}
}
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
	$xVis	= new cSQLVistas();
	$sql	= $xVis->getVistaLetrasConNombre($fecha, false, false, "", $producto);
	//$xLi	= new cSQLListas(); $xF	= new cFecha(); $fecha = $xF->getFechaISO($fecha);
	//$sql	= $xLi->getListadoDeLetrasConCreditos($fecha, false, "", "", " AND (`creditos_solicitud`.`saldo_actual`> 1)  AND (`creditos_tipoconvenio`.`omitir_seguimiento` =0) AND `letras`.`letra` > 0 ", $producto);
	
	$xT		= new cTabla($sql, 2,"idtblletrasporvencer");
	$xT->setWithMetaData();
	$xT->setEventKey("var xC=new CredGen();xC.goToPanelControl");
	//$xT->OButton("TR.PAGO", "jsGoToCaja(" . HP_REPLACE_ID . ")", $xT->ODicIcons()->COBROS);
	$xT->setColTitle("letra", "TOTAL");
	$xT->setColSum("capital");
	$xT->setColSum("interes");
	$xT->setColSum("iva");
	$xT->setColSum("otros");
	$xT->setColSum("letra");
	$xT->setKeyTable("creditos_solicitud");
	
	if(MODULO_CAPTACION_ACTIVADO == false){
		$xT->setOmitidos("ahorro");
	}
	
	//$xT->setResumidos("iva");
	if(getEsModuloMostrado(USUARIO_TIPO_CAJERO, MMOD_TESORERIA) == true){
		$xT->OButton("TR.PAGO", "jsGoToCaja(" . HP_REPLACE_ID . ")", $xT->ODicIcons()->COBROS);
	}
	if(getEsModuloMostrado(USUARIO_TIPO_OFICIAL_CRED, MMOD_SEGUIMIENTO) == true){
		$xT->OButton("TR.LLAMADA", "var xC=new CredGen();xC.setAgregarLlamada(" . HP_REPLACE_ID . ")", $xT->ODicIcons()->TELEFONO);
		$xT->OButton("TR.TAREAS", "var xC=new CredGen();xC.setAgregarCompromiso(" . HP_REPLACE_ID . ")", $xT->ODicIcons()->TAREA);
		$xT->OButton("TR.SMS", "var xC=new CredGen();xC.setAgregarNotificacion(" . HP_REPLACE_ID . ")", $xT->ODicIcons()->NOTA);
	}
	
	return $xT->Show();
}
function jsaGetLetrasVencidas($fecha, $producto){
	$xD		= new cFecha();
	$xL		= new cSQLListas();
	$xVis	= new cSQLVistas();
	$xFil	= new cSQLFiltros();
	
	$fecha 	= $xD->getFechaISO($fecha);
	$BySaldo		= $xFil->CreditosPorSaldos(TOLERANCIA_SALDOS, ">");
	//Agregar seguimiento
	$BySaldo		= $BySaldo . $xFil->CreditosProductosPorSeguimiento(0);	
	$BySaldo		= $BySaldo . " AND (`letras`.`total_sin_otros` >0) ";
	
	//TODO: Corregir echale
	
	$sql			= $xL->getListadoDeLetrasPendientesReporteAcumV101($BySaldo, TASA_IVA, true, false, $producto);

	//setLog($sql);
	
	$xT		= new cTabla($sql, 2, "idtblletrasyavencidas");
	//$xT->setOmitidos("persona");
	$xT->setUsarNullPorCero();
	$xT->setEventKey("var xC=new CredGen();xC.goToPanelControl");
	$xT->setWithMetaData();
	$xT->setKeyField("credito");
	$xT->setKeyTable("creditos_solicitud");
	//$xT->setOmitidos("monto_ministrado");
	$xT->setForzarTipoSQL("dias", MQL_INT);
	
	$xT->setTitulo("numero_con_atraso", "NUMERO");
	$xT->setTitulo("fecha_de_atraso", "FECHA");
	$xT->setTitulo("letra_original", "original");
	
	if(MODULO_SEGUIMIENTO_ACTIVADO == false){
		$xT->setOmitidos("seguimiento");
		$xT->setOmitidos("causamora");
	} else {
		$xT->setResumidos("seguimiento");
		$xT->setResumidos("causamora");
	}
	//$xT->setResumidos("nombre");
	$xT->setColSum("monto_ministrado");
	$xT->setColSum("capital");
	$xT->setColSum("historial");
	$xT->setColSum("letra_original");
	$xT->setColSum("total");
	
	//$xT->setResumidos("iva");
	if(getEsModuloMostrado(USUARIO_TIPO_CAJERO, MMOD_TESORERIA) == true){
		$xT->OButton("TR.PAGO", "jsPagoCajaCompleto(" . HP_REPLACE_ID . ")", $xT->ODicIcons()->COBROS);
	}
	if(getEsModuloMostrado(USUARIO_TIPO_OFICIAL_CRED, MMOD_SEGUIMIENTO) == true){
		$xT->OButton("TR.LLAMADA", "var xC=new CredGen();xC.setAgregarLlamada(" . HP_REPLACE_ID . ")", $xT->ODicIcons()->TELEFONO);
		$xT->OButton("TR.TAREAS", "var xC=new CredGen();xC.setAgregarCompromiso(" . HP_REPLACE_ID . ")", $xT->ODicIcons()->TAREA);
		$xT->OButton("TR.SMS", "var xC=new CredGen();xC.setAgregarNotificacion(" . HP_REPLACE_ID . ")", $xT->ODicIcons()->NOTA);
	}
	
	return $xT->Show( );
}

function jsaGetCreditosPorAutorizar($fecha){
	$xD		= new cFecha();
	$fecha 	= $xD->getFechaISO($fecha);
	
	$xDT	= new cHDicccionarioDeTablas();
	$xDT->OTable()->setResumidos("oficial");
	
	return $xDT->getCreditosPorAutorizar($fecha);	
}

function jsaGetCreditosPorMinistrar($fecha){
	$xDT	= new cHDicccionarioDeTablas();
	$xDT->OTable()->setResumidos("oficial");
	
	return $xDT->getCreditosPorMinistrar($fecha);
}

/*function jsaGetLetrasAVencerTodas($fecha){
	$xDT	= new cHDicccionarioDeTablas();
	return $xDT->getCreditosPorAutorizar($fecha);
}*/

function jsaGetLog($fecha, $buscar){
	$xList	= new cSQLListas();
	$xF		= new cFecha();
	$fecha	= $xF->getFechaISO($fecha);
	if(SAFE_ON_DEV == true){
		$sql	= $xList->getListadoDeEventos("", "", SYS_LOG_NIVEL_DEV);
	} else {
		if($buscar == "" OR $buscar == "0"){
			$sql	= $xList->getListadoDeEventos($fecha, $fecha);
		} else {
			$sql	= $xList->getListadoDeEventos("", "", "", "", "", "$buscar");
			
		}
	}
	//iDE_OPERACION;
	$xT	= new cTabla($sql); 
	if(SAFE_ON_DEV == true){
		$xT->addTool(SYS_DOS);
	}
	$xT->setKeyField("idgeneral_log");
	//Agregar el script delete
	return $xT->Show("TR.Log", true, "tablelog");
}

function jsaGetRecibosEmitidos($fecha){
	$xF		= new cFecha();
	$fecha	= $xF->getFechaISO($fecha);
	$xL		= new cSQLListas();
	$otros	= (MODO_DEBUG == true) ? "" : "";
	$xL->setInvertirOrden();
	
	$sql	= $xL->getListadoDeRecibos("", "", "", $fecha, $fecha, $otros);
	//setLog($sql);
	
	$xT		= new cTabla($sql);
	$xT->OButton("TR.RECIBO", "var xR=new RecGen();xR.formato(" . HP_REPLACE_ID . ")", $xT->ODicIcons()->IMPRIMIR);
	$xT->setEventKey("jsGetPanelRecibo");
	$xT->setFootSum(array(
		6 => "total"	
	));
	return $xT->Show();
}


function jsaActualizarIdioma($fecha, $version){
	$version	= setNoMenorQueCero($version);
	$xSys		= new cSystemPatch();
	if($version>0){
		$xSys->setForceVersion($version);
	}
	
	$xSys->patch(true, false, true);
	$xCache		= new cCache();
	$xCache->clean();
	
	return $xSys->getMessages(OUT_HTML);
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
	//$empresa, $tipo , $ConActivos , $EnvioFI 
	$xTabla	= new cTabla($xLi->getListadoDePeriodoPorEmpresa(false, false, true, false, false, $fecha, $fecha));
	$xTabla->setOmitidos("periocidad");
	$xTabla->setTitulo("periodo", "NUMERO");
	$xTabla->setTitulo("nombre_periocidad", "Periocidad");
	$xTabla->OButton("TR.Panel", "var xE = new EmpGen(); xE.getTablaDeCobranza(" . HP_REPLACE_ID . ")", $xTabla->ODicIcons()->CONTROL);
	
	return $xTabla->Show();
}
function jsaListaPeriodosDeEmpresaEmitidos($fecha){
	$xLi	= new cSQLListas();
	$xTabla	= new cTabla($xLi->getListadoDePeriodoPorEmpresa(false, false, false, $fecha, $fecha));
	$xTabla->setOmitidos("periocidad");
	$xTabla->setTitulo("periodo", "NUMERO");
	$xTabla->setTitulo("nombre_periocidad", "Periocidad");
	$xTabla->OButton("TR.Panel", "var xE = new EmpGen(); xE.getTablaDeCobranza(" . HP_REPLACE_ID . ")", $xTabla->ODicIcons()->CONTROL);
	
	return $xTabla->Show();
}

function jsaSetToLocalHost($fecha, $version){
	$xQL		= new MQL();
	$xCache		= new cCache();
	$version	= setNoMenorQueCero($version);
	$xSys		= new cSystemPatch();
	if($version>0){
		$xSys->setForceVersion($version);
	}
	
	// Get HTTP/HTTPS (the possible values for this vary from server to server)
	$lurl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] && !in_array(strtolower($_SERVER['HTTPS']),array('off','no'))) ? 'https' : 'http';
	// Get domain portion
	$lurl .= '://'.$_SERVER['HTTP_HOST'] . "/";
	// Get path to script
	//$myUrl .= $_SERVER['REQUEST_URI'];
	$mailpass		= "Pruebas2019";
	$mailid			= "pruebas@opencorebanking.com";
	
	$xQL->setRawQuery("UPDATE `entidad_configuracion` SET `valor_del_parametro` = '$lurl' WHERE `nombre_del_parametro` = 'url_de_actualizaciones_automaticas'");
	$xQL->setRawQuery("UPDATE `sistema_programacion_de_avisos` SET `destinatarios` = 'CORREO:$mailid|'");
	$xQL->setRawQuery("UPDATE `entidad_configuracion` SET `valor_del_parametro` = '127.0.0.1' WHERE `nombre_del_parametro` = 'url_del_servidor_ftp'");
	$xQL->setRawQuery("UPDATE `socios_general` SET `correo_electronico` = '$mailid' WHERE `codigo` = '1901850'");
	$xQL->setRawQuery("UPDATE `socios_general` SET `correo_electronico` = '$mailid' WHERE `codigo` = '10000'");
	
	$xQL->setRawQuery("UPDATE `entidad_configuracion` SET `valor_del_parametro` = '$mailid' WHERE `nombre_del_parametro` = 'email_del_administrador' ");
	$xQL->setRawQuery("UPDATE `entidad_configuracion` SET `valor_del_parametro` = '$mailid' WHERE `nombre_del_parametro` = 'email_del_archivo' ");
	$xQL->setRawQuery("UPDATE `entidad_configuracion` SET `valor_del_parametro` = '$mailid' WHERE `nombre_del_parametro` = 'email_de_la_entidad' ");
	$xQL->setRawQuery("UPDATE `entidad_configuracion` SET `valor_del_parametro` = '$mailid' WHERE `nombre_del_parametro` = 'email_de_mercadeo' ");
	$xQL->setRawQuery("UPDATE `entidad_configuracion` SET `valor_del_parametro` = '$mailid' WHERE `nombre_del_parametro` = 'email_de_nominas' ");
	$xQL->setRawQuery("UPDATE `entidad_configuracion` SET `valor_del_parametro` = '$mailid' WHERE `nombre_del_parametro` = 'facturacion.email_de_almacenamiento' ");
	
	$xQL->setRawQuery("UPDATE `entidad_configuracion` SET `valor_del_parametro` = '$mailpass' WHERE `nombre_del_parametro` = 'password_del_email_del_administrador'");
	$xQL->setRawQuery("UPDATE `entidad_configuracion` SET `valor_del_parametro` = 'mail.opencorebanking.com' WHERE `nombre_del_parametro` = 'servidor_smtp_para_notificaciones'");
	$xQL->setRawQuery("UPDATE `entidad_configuracion` SET `valor_del_parametro` = '$mailid' WHERE `nombre_del_parametro` = 'smtp_seguro_para_notificaciones' ");
	$xQL->setRawQuery("UPDATE `entidad_configuracion` SET `valor_del_parametro` = '' WHERE `nombre_del_parametro` = 'system_pay_email_register' ");
	$xQL->setRawQuery("UPDATE `entidad_configuracion` SET `valor_del_parametro` = '' WHERE `nombre_del_parametro` = 'smtp_seguro_para_notificaciones' ");
	$xQL->setRawQuery("UPDATE `entidad_configuracion` SET `valor_del_parametro` = '587' WHERE `nombre_del_parametro` = 'puerto_smtp_para_notificaciones' ");
	//$xQL->setRawQuery("UPDATE `entidad_configuracion` SET `valor_del_parametro` = '' WHERE `nombre_del_parametro` = '' ");
	//$xQL->setRawQuery("UPDATE `entidad_configuracion` SET `valor_del_parametro` = '' WHERE `nombre_del_parametro` = '' ");
	
	$xQL->setRawQuery("UPDATE `entidad_configuracion` SET `valor_del_parametro` = 'documentos' WHERE `nombre_del_parametro` = 'nombre_de_usuario_ftp'");
	$xQL->setRawQuery("UPDATE `entidad_configuracion` SET `valor_del_parametro` = 'documentos' WHERE `nombre_del_parametro` = 'password_de_usuario_ftp'");
	$xQL->setRawQuery("UPDATE `entidad_configuracion` SET `valor_del_parametro` = 'http://pruebas:pruebas@localhost:5984/' WHERE `nombre_del_parametro` = 'svc_url_couchdb'");
	
	$xQL->setRawQuery("CALL `proc_creditos_a_final_de_plazo`");
	$xQL->setRawQuery("CALL `proc_creditos_abonos_por_mes`");
	$xQL->setRawQuery("CALL `proc_creditos_letras_pendientes`");
	$xQL->setRawQuery("CALL `proc_historial_de_pagos`");
	$xQL->setRawQuery("CALL `proc_listado_de_ingresos`");
	$xQL->setRawQuery("CALL `proc_perfil_egresos_por_persona`");
	$xQL->setRawQuery("CALL `proc_personas_operaciones_recursivas`");
	$xQL->setRawQuery("CALL `sp_clonar_actividades`");
	$xQL->setRawQuery("CALL `proc_colonias_activas`");
	$xQL->setRawQuery("CALL `sp_correcciones`");
	//$xQL->setRawQuery("");
	
	
	$xCache->clean(false);
	

	
	$xSys->patch(true, false);
	$xCache->clean();
	
	if(SAFE_ON_DEV == true){
		$xQL->setRawQuery("DELETE FROM general_log");
	}
	return $xSys->getMessages(OUT_HTML);
}
function jsaSetActualizarSys($version){
	$version	= setNoMenorQueCero($version);
	
	$xQL		= new MQL();
	$xCache		= new cCache();
	$xSys		= new cSystemPatch();
	$xTask		= new cSystemTask();
	
	if(SAFE_ON_DEV == false){
		$xTask->setBackupDB();
	}
	
	if($version>0){
		$xSys->setForceVersion($version);
	}
	$xSys->patch(true, false);
	$xCache->clean();
	return $xSys->getMessages(OUT_HTML);
}

function jsaSetRunContabilidad(){
	$xUt	= new cSystemTask();
	$subdir	= PATH_HTDOCS . "/install/db/";
	$xUt->setRunSQLPatchByFile($subdir , "purgar_contable.sql");//purgar_contable.sql
	$xUt->setRunSQLPatchByFile($subdir , "contable-estable.sql");//purgar_contable.sql
	$xUt->setRunSQLPatchByFile($subdir , "contabilidad-pruebas.sql");//purgar_contable.sql
	
	$xContUtils	= new cUtileriasParaContabilidad();
	$xContUtils->setGenerarSaldosDelEjercicio(EJERCICIO_CONTABLE);
	
}

function jsaActualizarEstPers(){
	$xPersUt	= new cPersonasUtilerias();
	$xPersUt->setConstruirEstadisticas();
	return "OK";
}

function jsaEnviarSMSCobro($credito){
	$xCred	= new cCredito($credito);
	if($xCred->init() == true){
		$xNot	= new cSeguimientoNotificaciones();
		$xNot->add($credito, false, false, $xCred->getMontoDeParcialidad());
	}
	return $xNot->getMessages(OUT_HTML);
}


function jsaGetResumenDeCaja($caja){ $xCaja		= new cCaja();	$xCaja->init($caja);	return $xCaja->getResumenDeCaja(); }


$jxc ->exportFunction('jsaShowCalendarTasks', array('idDateValue'), "#tcalendar-task");

$jxc ->exportFunction('jsaGetLetrasAVencer', array('idDateValue', 'idproducto'), "#tcalendar-task");
$jxc ->exportFunction('jsaGetLetrasVencidas', array('idDateValue', 'idproducto'), "#tcalendar-task");


$jxc ->exportFunction('jsaGetCreditosPorAutorizar', array('idDateValue', 'idproducto'), "#tcalendar-task");
$jxc ->exportFunction('jsaGetCreditosPorMinistrar', array('idDateValue', 'idproducto'), "#tcalendar-task");

$jxc ->exportFunction('jsaEliminarLog', array('idDateValue'), "#tcalendar-task");

$jxc ->exportFunction('jsaGetLog', array('idDateValue', 'idclave'), "#tcalendar-task");
$jxc ->exportFunction('jsaGetRecibosEmitidos', array('idDateValue'), "#tcalendar-task");

$jxc ->exportFunction('jsaSetCumplido', array('id-KeyEditable'));

$jxc ->exportFunction('jsaGetIngresosDeldia', array('idDateValue'), "#tcalendar-task");
$jxc ->exportFunction('jsaGetIngresosDelMes', array('idDateValue'), "#tcalendar-task");
$jxc ->exportFunction('jsaGetIngresosMensualesPorDependencias', array('idDateValue'), "#tcalendar-task");

$jxc ->exportFunction('jsaRespaldarDB', array('idDateValue'), "#idavisos");
$jxc ->exportFunction('jsaActualizarIdioma', array('idDateValue', 'idclave'), "#idavisos");
$jxc ->exportFunction('jsaActualizarIngresos', array('idDateValue'), "#idavisos");
$jxc ->exportFunction('jsaListaPeriodosDeEmpresa', array('idDateValue'), "#tcalendar-task");
$jxc ->exportFunction('jsaListaPeriodosDeEmpresaEmitidos', array('idDateValue'), "#tcalendar-task");

$jxc ->exportFunction('jsaActualizarProyeccionMensual', array('idDateValue'), "#idavisos");
$jxc ->exportFunction('jsaSetToLocalHost', array('idDateValue', 'idclave'), "#idavisos");

$jxc ->exportFunction('jsaSetActualizarSys', array('idclave'), "#idavisos");
$jxc ->exportFunction('jsaActualizarEstPers', array('idclave'), "#idavisos");


$jxc ->exportFunction('jsaSetRunContabilidad', array('idclave'), "#idavisos");

$jxc ->exportFunction('jsaGetResumenDeCaja', array("idcaja"), "#tcalendar-task");

//$jxc ->exportFunction('jsaEnviarSMSCobro', array('idclave'), "#idavisos");


//jsaRespaldarDB
$jxc ->process();

$xHP->addChartSupport();

if($noUsarChat == false){
	$xHP->addJsFile("https://help.sipakal.com/js/compiled/chat_popup.js");
}

$xHP->init();

$xFRM		= new cHForm("frmcalendartask");
$xUsr		= new cSystemUser();
$xNotif		= new cHNotif();
$alerts		= "";
$xSel		= new cHSelect();
$xUsr->init();
$xFRM->setTitle($xHP->getTitle());
//===================================================== Optiones

$xFRM->addSeccion("idsoptions", "TR.Opciones");
	$xFRM->ODate("idDateValue", false, "TR.FECHA DE FILTRO");
	$xSelC		= $xSel->getListaDeProductosDeCredito("", false, true);
	$xSelC->addEspOption(SYS_TODAS);
	$xSelC->setOptionSelect(SYS_TODAS);
	$xFRM->addHElem($xSelC->get(true));
	$xFRM->OMoneda("idclave", 0, "TR.CLAVE");
$xFRM->endSeccion();


//===================================================== Avisos
$xFRM->addSeccion("idsexavisos", "TR.INFORMACION");
$xFRM->addHElem("<div id=\"tcalendar-task\"></div>");

$xFRM->endSeccion();

//===================================================== Lista de Cajas Abiertas

$sql		=  $xLi->getListadoDeCajasConUsuario(TESORERIA_CAJA_ABIERTA, fechasys());
$xT2			= new cTabla($sql);
//$xT2->setOmitidos("codigo");
$xFRM->addSeccion("idlistacajas", "TR.Cajas_Abiertas");
$xT2->setNoFilas();
$xFRM->addHElem( $xT2->Show() );
$xFRM->endSeccion();

//if(getEsModuloMostrado(USUARIO_TIPO_CAJERO)){

	//$xFRM->addToolbar($xBtn->getBasic("Ingresos del Dia", "jsGetChart()", "grafico", "idcharts", false) );
//} 

$xFRM->OButton("TR.Tareas", "jsGetInformes()", "tarea", "idtareas");

if(MODULO_AML_ACTIVADO == true){
	$xFRM->OButton("TR.Buscar en Lista_Negra", "var xP= new PersGen(); xP.setBuscarEnListas()", $xFRM->ic()->BUSCAR, "idtareas");
	$xFRM->OButton("TR.Reporte de CONDUCTA_INADECUADA", "var xG=new Gen();xG.w({url:'../frmpld/reportar_empleado.frm.php?',tiny:true})", $xFRM->ic()->COMENTARIO, "idhacerreporte");
}



//===================================================== Actividad de Usuarios
$xCh3		= new cChart("iddivevtsuser");
$xCh3->setAlto("480");

$sql3	= "SELECT `general_error_codigos`.`description_error` AS `descripcion`, COUNT(`general_log`.`idgeneral_log`) AS `eventos`
		
			FROM `general_log` `general_log` INNER JOIN `general_error_codigos` `general_error_codigos`
			ON `general_log`.`type_error` = `general_error_codigos`.`idgeneral_error_codigos`
			WHERE (`general_log`.`fecha_log` ='" . fechasys() . "') AND (`general_error_codigos`.`type_err`!='developer') AND `type_error` !=10 AND `type_error` !=9001
					/*AND CONVERT(`usr_log`,UNSIGNED INTEGER)>0*/
			GROUP BY `general_log`.`type_error` ";

//$xCh3->addSQL($xLi->getListadoDeEventosResumen(fechasys(), fechasys()),"descripcion", "eventos");

$xCh3->addSQL($sql3,"descripcion", "eventos");
//$xCh3->setAlto(400);
$xCh3->setHorizontal(true);
$xCh3->setTamanioTitulo(300);
$xCh3->setAutoWith();
$xCh3->setProcess($xCh3->BAR);

$xFRM->addSeccion("idevtsuser", "TR.LOG_FILE");
$xFRM->addHElem($xCh3->getDiv());
$xFRM->addJsInit($xCh3->getJs());
$xFRM->endSeccion();



//============================== Charts de creditos por ministrar
$xCEs			= new cCreditosEstadisticas();
$xFRM->addSeccion("idstatsclientes", "TR.KPIS .- CLIENTES");
$xFRM->addHElem( $xNotif->getDash("TR.Clientes con Creditos", $xCEs->getNumeroClientesConCredito(), $xFRM->ic()->PERSONA, $xNotif->NOTICE) );
$xFRM->endSeccion();

$xChCred		= new cChart("idchartcredito");
$xChCred->addData($xCEs->getNumeroCreditosPorAutorizar(), "TR.Creditos Por Autorizar" );
$xChCred->addData(0,"TR.Creditos Por Autorizar");

$xChCred->addData(0,"TR.Creditos Por Ministrar");
$xChCred->addData($xCEs->getNumeroCreditosPorMinistrar(),"TR.Creditos Por Ministrar");

$xChCred->setProcess($xChCred->BAR);

$xFRM->addSeccion("idstatscreditos", "TR.KPIS .- CREDITOS");
$xFRM->addHElem($xChCred->getDiv());
$xFRM->addJsInit($xChCred->getJs());
$xFRM->endSeccion();

if(getEsModuloMostrado(USUARIO_TIPO_CAJERO) == true){
	//==================== Cartera de Credito
	if(GARANTIA_LIQUIDA_EN_CAPTACION== false){
		$xFRM->OButton("TR.CARTERA GTIALIQ", "jsGetCarteraGtiaLiquida()", $xFRM->ic()->REPORTE5, "carteragtialiq", "blue3");
	}
	
	$xFRM->OButton("TR.Recibos Emitidos", "jsaGetRecibosEmitidos()", $xBtn->ic()->REPORTE);
	$xFRM->OButton("TR.Pagos DEL DIA", "jsaGetLetrasAVencer()", $xFRM->ic()->REPORTE4, "idletrapagosvencs");
	$xFRM->OButton("TR.LETRASVENC", "jsaGetLetrasVencidas()", $xFRM->ic()->REPORTE5, "idletravencs");
}
if(getEsModuloMostrado(USUARIO_TIPO_OFICIAL_CRED, MMOD_COLOCACION)){
	$xFRM->addToolbar($xBtn->getBasic("TR.Creditos Por Autorizar", "jsaGetCreditosPorAutorizar()", "lista", "idcredaut", false) );
	$xFRM->addToolbar($xBtn->getBasic("TR.Creditos Por Ministrar", "jsaGetCreditosPorMinistrar()", "lista", "idcrednpoaut", false) );
}
if(getEsModuloMostrado(USUARIO_TIPO_GERENTE, MMOD_COLOCACION) == true){
	$xFRM->OButton("TR.Actualizar Proyeccion", "jsActualizarProyeccionMensual()", $xFRM->ic()->RECARGAR, "idcmdactualizar", "yellow");
	$xFRM->OButton("TR.Actualizar Estadisticas", "jsActualizarEstPers()", $xFRM->ic()->RECARGAR, "idcmdactualizarespers", "yellow");
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
	
	$xFRM->addSeccion("idstatsproyeccion", "TR.KPIS .- PROYECCION");
	$xFRM->addHElem($xChProy->getDiv());
	$xFRM->addJsInit($xChProy->getJs());
	$xFRM->endSeccion();
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
if(getEsModuloMostrado(USUARIO_TIPO_CAJERO, MMOD_TESORERIA) == true){
	if($xCaja->initByFechaUsuario(fechasys(), getUsuarioActual()) == true){
		if($xCaja->getEstatus() == TESORERIA_CAJA_ABIERTA){
			//cerrar Caja
			$xFRM->OButton("TR.CERRAR CAJA", "var xG=new Gen();xG.w({url:'../frmcaja/cerrar_caja.frm.php?',principal:true});", $xFRM->ic()->CERRAR, "idcmdcerrarcaja", "yellow");
			$xFRM->OButton("TR.REPORTE CAJA", "jsaGetResumenDeCaja()", $xFRM->ic()->DINERO, "idcmfgetrptcaja", "white");
			$OnCaja	= true;
			//Reporte de Caja
		}
	}
}

if(getEsModuloMostrado(USUARIO_TIPO_CAJERO, MMOD_TESORERIA) == true){
	if(PERSONAS_CONTROLAR_POR_EMPRESA == true){
		$xFRM->OButton("TR.NOMINAS POR COBRAR", "jsaListaPeriodosDeEmpresa", $xFRM->ic()->REPORTE2);
		$xFRM->OButton("TR.NOMINAS ENVIADAS", "jsaListaPeriodosDeEmpresaEmitidos", $xFRM->ic()->REPORTE3);
	}
}
if(getEsModuloMostrado(USUARIO_TIPO_CAJERO, MMOD_TESORERIA) == true AND $OnCaja == false){
	$xFRM->OButton("TR.ABRIR_SESSION DE CAJA", "var xG=new Gen();xG.w({url:'../frmcaja/abrir_caja.frm.php?',tiny:true})", $xFRM->ic()->COBROS);
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

$xFRM->addSeccion("idstatsusers", "TR.KPIS .- USUARIO");
$xFRM->addHElem($xChUser->getDiv());
$xFRM->addJsInit($xChUser->getJs());
$xFRM->endSeccion();



//=====================================================
if(MODULO_LEASING_ACTIVADO == true){
	$xCh2		= new cChart("iddivleas");
	$xCh2->setAlto("300");
	
	
	
	$xCh2->addSQL("SELECT   `originacion_leasing`.`paso_proceso` AS `proceso`,`creditos_etapas`.`descripcion` AS `nombre`, EnMiles(SUM( `originacion_leasing`.`precio_vehiculo` ))  AS `monto`
FROM     `originacion_leasing` INNER JOIN `creditos_etapas`  ON `originacion_leasing`.`paso_proceso` = `creditos_etapas`.`idcreditos_etapas` GROUP BY paso_proceso", "nombre", SYS_MONTO);
	
	//$xCh2->setFuncConvert("enmiles");
	
	$xCh2->setProcess($xCh2->BAR);
	
	$xFRM->addSeccion("idstatsleasing", "TR.KPIS .- LEASING");
	
	$xFRM->addHElem($xCh2->getDiv());
	$xFRM->addJsInit($xCh2->getJs());
	
	$xFRM->endSeccion();
}


$xFRM->setNoAcordion();
//$xF->dia() < 10 AND
if( getEsModuloMostrado(USUARIO_TIPO_CONTABLE, MMOD_CONTABILIDAD) == true){
	$xFRM->OButton("TR.Actualizar Ingresos", "jsActualizarIngresos()", $xFRM->ic()->REPORTE4);
}


$xChart			= new cChart("idivchart");



$xFRM->OButton("TR.CALCULAR PLAN_DE_PAGOS", "jsCalcularPlanPagos()", $xFRM->ic()->CALENDARIO, "cmdcalcplan", "ggreen");

if(getEsModuloMostrado(false, MMOD_CRED_LEASING) == true){
	$xFRM->OButton("TR.AGREGAR ARRENDAMIENTO", "jsAgregarLeasing()", $xFRM->ic()->LEASING, "cmdaddleasing", "gorange");
}
if(getEsModuloMostrado(USUARIO_TIPO_GERENTE) == true){
	if($SyncApp == true){
		$xFRM->OButton("TR.Sync App Catalogos", "var xApp=new AppGen();xApp.sync({catalogos:true});", $xFRM->ic()->EJECUTAR, "cmdexecappcat", "yellow");
		$xFRM->OButton("TR.Sync App Avisos", "var xApp=new AppGen();xApp.sync({avisos:true});", $xFRM->ic()->EJECUTAR, "cmdexecappmsg", "yellow");
	}
}

if(getEsModuloMostrado(USUARIO_TIPO_OFICIAL_CRED, MMOD_COLOCACION) == true){
	$xFRM->OButton("TR.AGREGAR PRECLIENTE", "jsAgregarPrecliente()", $xFRM->ic()->CREDITO, "cmdaddprecredito", "gorange");
	$xFRM->OButton("TR.AGREGAR CREDITOS_LINEAS", "jsAgregarLineaCredito()", $xFRM->ic()->CREDITO, "cmdaddlineacredito", "gorange");
}

$xFRM->OButton("TR.Buscar PERSONA", "jsGoBuscarPersona()", $xFRM->ic()->PERSONA, "cmdfindpersona", "blue");
$xFRM->OButton("TR.IR PANEL PERSONA", "jsGoPanelPersona()", $xFRM->ic()->PERSONA, "cmdpanelpers", "persona");
$xFRM->OButton("TR.IR PANEL CREDITO", "jsGoPanelCredito()", $xFRM->ic()->CREDITO, "cmdpanelcred", "credito");
$xFRM->OButton("TR.IR PANEL RECIBO", "jsGoPanelRecibo()", $xFRM->ic()->RECIBO);


if(getEsModuloMostrado(USUARIO_TIPO_OFICIAL_CRED) == true){
	$xFRM->OButton("TR.Actualizar Letras pendientes", "jsActualizarProcLetras()", $xFRM->ic()->EJECUTAR);
}
if(getEsModuloMostrado(USUARIO_TIPO_OFICIAL_AML, MMOD_AML)){
	$xFRM->OButton("TR.Acceso al RMS", "var xg=new Gen();xG.w({url:'"  . AML_RMS_URL . "', tab:true});", $xFRM->ic()->ADELANTE, "cmdbtnaccrms", "green2");
	if($SyncAML == true){
		$xFRM->OButton("TR.SYNC AML", "jsSyncAMLImport()", $xFRM->ic()->SYNC, "cmdbtnsyncaml", "green");
		$xFRM->OButton("TR.SYNC AML Personas", "jsSyncAMLImportPersonas()", $xFRM->ic()->SYNC, "cmdbtnsyncaml2", "green");
		$xFRM->OButton("TR.SYNC AML Creditos", "jsSyncAMLImportCreditos()", $xFRM->ic()->SYNC, "cmdbtnsyncaml3", "green");
	}
}
if(MODO_DEBUG == true){
	$srv	= $xHP->getServerName();
	if(strpos($srv, "localhost") === false AND strpos($srv, "test") === false){
		if(SAFE_ON_DEV == true){
			$xFRM->OButton("Actualizar a Localhost", "jsSetLocalhost()", "grafico", "idsetloc", "red");
		}
	} else {
		$xFRM->OButton("ELiminar LOG", "jsEliminarLog()", "grafico", "idlog", "red");
		$xFRM->OButton("Actualizar a Localhost", "jsSetLocalhost()", "grafico", "idsetloc", "red");
	}
	
	$xFRM->OButton("Obtener LOG", "jsaGetLog()", $xFRM->ic()->GRAFICO1, "idglog", "blue3");
	$xFRM->OButton("TR.Respaldo", "jsaRespaldarDB()", $xFRM->ic()->EJECUTAR, "idrespdb", "green");
	
	$xFRM->OButton("TR.Actualizar Idioma", "jsaActualizarIdioma()", $xFRM->ic()->EJECUTAR, "cmdupdate", "green2");
	$xFRM->OButton("TR.ACTUALIZAR EL SISTEMA", "jsSetActualizarSys()", $xFRM->ic()->EJECUTAR, "cmdusys", "yellow");
	$xFRM->OButton("TR.CONFIGURACION DEL SISTEMA", "var xg=new Gen();xG.w({url:'../frmsystem/opciones.frm.php', principal:true});", $xFRM->ic()->CONTROL, "cmdoptions", "yellow");
	
	$xFRM->OButton("TR.PRODUCTO CREDITO", "var xG=new Gen();xG.w({url:'../frmcreditos/frmdatos_de_convenios_de_creditos.xul.php', principal:true});", $xFRM->ic()->EJECUTAR, "cmdbtn101", "green2");
	$xFRM->OButton("TR.OPERACIONES", "var xG=new Gen();xG.w({url:'../frmtipos/operaciones_tipos.lista.frm.php', principal:true});", $xFRM->ic()->EJECUTAR, "cmdbtn103", "green2");
	
	$xFRM->OButton("TR.USUARIOS", "var xG=new Gen();xG.w({url:'../frmsecurity/usuarios-edicion.frm.php', principal:true});", $xFRM->ic()->EJECUTAR, "cmdbtn102", "green2");
	
	$xFRM->OButton("TR.SUCURSALES", "var xG=new Gen();xG.w({url:'../frmtipos/sucursales.frm.php', principal:true});", $xFRM->ic()->HOME, "cmdbtn102", "green2");
	
	$xFRM->OButton("TR.PERMISOS", "var xG=new Gen();xG.w({url:'../frmsecurity/permisos.frm.php', principal:true});", $xFRM->ic()->EJECUTAR, "cmdbtn103", "green2");
	
	$xFRM->OButton("TR.CONTRATOS", "var xG=new Gen();xG.w({url:'../frmutils/contratos-editor.frm.php', principal:true});", $xFRM->ic()->CONTRATO, "cmdbtn103", "green2");
	
	
}
if($noUsarChat == false){
	$xFRM->addToolbar('<a id="mibew-agent-button" class="yellow" href="https://help.sipakal.com/chat?locale=es" target="_blank" onclick="Mibew.Objects.ChatPopups[\'5b287945db28a4bc\'].open();return false;"><i class="fa fa-question-circle fa-2x"></i>' . $xFRM->getT("TR.AYUDA") . '</a>');
}

if($xFRM->getEnDesarrollo() == true){
	//$xFRM->OButton("TR.", "var xG=new Gen();xG.w({url:''});", $xFRM->ic()->EJECUTAR, "cmdbtn101", "green2");
	$xFRM->OButton("TR.LIMPIAR CONTABILIDAD", "var xG=new Gen();xG.confirmar({msg:'¿ Confirma limpiar contabilidad ?', callback: jsaSetRunContabilidad});", $xFRM->ic()->CONTABLE, "cmdbtncont01", "green2");
}
if(PERSONAS_COMPARTIR_CON_ASOCIADA == true){
	$ctx	= $xUsr->getCTX();
	$xFRM->OButton("TR.ABRIR ASOCIADA", "var xG = new Gen(); xG.w({new:true,full:true, url:'" . SVC_ASOCIADA_HOST . "index.xul.php?ctx=$ctx'});", $xFRM->ic()->VINCULAR, "cmcompartirasoc", "yellow");
}

$idpersona	= $xUsr->getClaveDePersona();
$xFRM->OButton("TR.VER MI USUARIO", "jsVerMiPassword($iduser)", $xFRM->ic()->PASSWORD, "", "white");
$xFRM->OButton("TR.Salir", "var xG = new Gen(); xG.salir()", $xFRM->ic()->SALIR, "cmsalir", "yellow");

//$xFRM->addSeccion("idmastareas", "TR.Tareas");



$horasql		= $xQL->getDataValue("SELECT NOW() AS 'tiempo' ", "tiempo");

$sysinfo		= "";

if (MODO_DEBUG == true AND (SYSTEM_ON_HOSTING == false)){
	$xUL			= new cHUl(); $xUL2		= new cHUl();
	$sysinfo		=  $xUL->li("Base de Datos:" . MY_DB_IN)->li("Servidor: " . WORK_HOST)
	->li("Version S.A.F.E.: " . SAFE_VERSION)
	->li("Revision S.A.F.E: " . SAFE_REVISION)
	
	->li("Path Temporal: " . PATH_TMP)
	->li("Path Backups: " . PATH_BACKUPS)->li("Fecha del Sistema: " . date("Y-m-d H:i:s"))

	->li("SAFE DB version : " . SAFE_DB_VERSION)
	->li("SAFE Host : " . SAFE_HOST_URL)
	->li("SAFE Actualizaciones : " . URL_UPDATES)
	->li("Tiempo SQL : " . $horasql)
	->li("Descargar Actualizacion SQL ", " onclick=\"var xG=new Gen();xG.w({url:'" . URL_UPDATES . "install/updates/sql.php?version=" . SAFE_DB_VERSION . "&out=sql'});\" ")
	->end();
	
	$sysinfo2		= $xUL2
	->li("Usuario Activo: " . $xUsr->getNombreCompleto() )
	->li("ID de Usuario: " . $xUsr->getID())
	->li("Nivel de Usuario: " . $xUsr->getNivel())
	->li("Clave API: " . $xUsr->getCTX())
	
	->li("Sucursal: " . getSucursal())
	->li("Caja Local : " . $xLoc->getCajaLocal())

	
	->li("Localidad : " . $xLoc->DomicilioLocalidad())
	->li("Clave Localidad : " . $xLoc->DomicilioLocalidadClave())
	->li("Municipio : " . $xLoc->DomicilioMunicipio())
	->li("Estado : " . $xLoc->DomicilioEstado())
	->li("Clave Estado : " . $xLoc->DomicilioEstadoClaveABC() )
	->li("C.P. : " . $xLoc->DomicilioCodigoPostal())
	->li("Clave de Oficial AML : " . getOficialAML())
	->end();
	
	$xFRM->addSeccion("idmaslogs", "TR.Configuracion del Sistema");
	$xFRM->addDivSolo($sysinfo, $sysinfo2, "tx24", "tx24" );
	$xFRM->endSeccion();
	$xFRM->addSeccion("idmaxlogs", "TR.Estado del Sistema");
	
	
	$xTt				= new cHTabla();
	
	//$xFRM->addDivSolo($sysinfo, $sysinfo2, "tx24", "tx24" );
	
	if($xSys->getExistsMemcache() == false){
		$xTt->initRow("error");
		$xTt->addTD("La cache No esta funcionando.");
		$xTt->endRow();
		
	} else {
		$xTt->initRow("success");
		$xTt->addTD("La cache esta funcionando.");
		$xTt->endRow();
	}
	if($xSys->getExistsWHPDF() == false){
		$xTt->initRow("error");
		$xTt->addTD("El conversor PDF No esta funcionando. Requiere wkhtmltopdf y Xvfb");
		$xTt->endRow();
		
	} else {
		$xTt->initRow("success");
		$xTt->addTD("El conversor PDF de Documentos esta funcionando.");
		$xTt->endRow();
	}
	
	if($xSys->getExistsUnoconv() == false){
		$xTt->initRow("error");
		$xTt->addTD("El conversor DOCX No esta funcionando. Requiere unoconv");
		$xTt->endRow();
		
	} else {
		$xTt->initRow("success");
		$xTt->addTD("El conversor DOCX esta funcionando.");
		$xTt->endRow();
	}

	$xDoc	= new cDocumentos();
	if($xDoc->FTPConnect() == false){
		$xTt->initRow("error");
		$xTt->addTD("El Servidor FTP No esta funcionando.");
		$xTt->endRow();
		
	} else {
		$xTt->initRow("success");
		$xTt->addTD("El Servidor FTP esta funcionando.");
		$xTt->endRow();
	}

	$xFRM->addHElem($xTt->get());
	
	$xFRM->endSeccion();
}



$xFRM->addAviso("", "idavisos");
$xFRM->OHidden("id-KeyEditable", "", "");
//$xFRM->addHTML($menu);
echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
?>


<!--  <a id="mibew-agent-button" href="https://help.sipakal.com/chat?locale=es" target="_blank" onclick="Mibew.Objects.ChatPopups['5b287945db28a4bc'].open();return false;">
<img src="https://help.sipakal.com/b?i=mgreen&amp;lang=es" border="0" alt="" /></a> -->


<script>
var xG		= new Gen();
var xCred	= new CredGen();
var xP		= new PersGen();
$(document).ready( function(){
	<?php 
		if($noUsarChat == false){ 
	?>
	Mibew.ChatPopup.init({"id":"5b287945db28a4bc","url":"https:\/\/help.sipakal.com\/chat?locale=es","preferIFrame":true,"modSecurity":false,"forceSecure":true,"width":640,"height":640,"resizable":true,"styleLoader":"https:\/\/help.sipakal.com\/chat\/style\/popup\/\/force_secure"});
	<?php
		}
	?>
	//$('#idDateValue').pickadate({format: 'dd-mm-yyyy',formatSubmit:'yyyy-mm-dd'});
	window.localStorage.clear();
});
function jsEliminarLog(){
	xG.confirmar({msg: "¿ Confirma eliminar el LOG? ", callback : jsaEliminarLog});
}
function jsSetLocalhost(){
	xG.confirmar({msg: "¿ Confirma Setear a Localhost ?", callback : jsaSetToLocalHost});
}
function jsSetActualizarSys(){
	xG.confirmar({msg: "¿ Confirma Actualizar el Sistema ?", callback : jsaSetActualizarSys});
}
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
	xc.goToPanelControl(idcredito,{principal:true, tab:false});
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

function jsGetCarteraGtiaLiquida(){
	xG.w({url:"../frmcreditos/cartera-gtia-liquida.frm.php?", tab: false , h: 600, w : 480, tab:true});
}

function jsCalcularPlanPagos(){
	xG.w({url:"../frmcreditos/calculadora.plan.frm.php?", tiny: false , h: 600, w : 480, tab:true});
}
function jsGoToCaja(id){
	var obj	= processMetaData("#tr-creditos_solicitud-" + id);
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
	xCred.goToPanelControl(id, {principal:true, tab:false});
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
	xG.w({ url: xrl, principal : true }); 	
}
function jsGoBuscarPersona(){
	var xrl		= "../utils/frmbuscarsocio.php?a=1";
	xG.w({ url: xrl, principal : true });	
}
function jsPagoCajaCompleto(id){
	var obj 	= processMetaData("#tr-creditos_solicitud-" + id);
	//console.log(obj);
	//console.log("#tr-creditos-solicitud-" + id);
	xG.w({url:"../frmcaja/abonos-a-parcialidades.frm.php?credito="+id + "&monto=" + obj.total, principal : true}); 
}
function jsAgregarTarea(id){
	var obj 	= processMetaData("#tr--" + id);
	//xG.w({url:"../frmcaja/abonos-a-parcialidades.frm.php?credito="+id + "&monto=" + obj.total}); 
}
function jsAgregarLeasing(){
	xG.w({url:"../frmarrendamiento/cotizador.frm.php", tab:true});
}
function jsAgregarPrecliente(){
	xG.w({url:"../frmcreditos/creditos-preclientes.new.frm.php?topanel=true", principal:true});
}
function jsAgregarLineaCredito(){
	xG.w({url:"../frmcreditos/frmcreditoslineas.php?topanel=true", principal:true});
}
function jsActualizarEstPers(){
	xG.confirmar({msg: 'CONFIRMA_ACTUALIZACION . MSG_WARN_DEL_INFO_ANT', callback: jsaActualizarEstPers});
}
function jsActualizarProyeccionMensual(){
	xG.confirmar({msg: 'CONFIRMA_ACTUALIZACION . MSG_WARN_DEL_INFO_ANT', callback: jsaActualizarProyeccionMensual});
}
function jsSyncAMLImport(){
	var idDateValue = $("#idDateValue").val();
	
	xG.confirmar({msg: 'CONFIRMA_ACTUALIZACION . MSG_WARN_DEL_INFO_ANT', callback: function(){
			xG.svc({
				url:'walook-sync.svc.php?action=operaciones&fecha=' + idDateValue,
				callback: function(data){
						if(typeof data != "undefined"){
							xG.alerta({msg : "Proceso Terminado"});
						}
					}
				})
		}});
}
function jsSyncAMLImportPersonas(){
	var idDateValue = $("#idDateValue").val();
	
	xG.confirmar({msg: 'CONFIRMA_ACTUALIZACION . MSG_WARN_DEL_INFO_ANT', callback: function(){
			xG.svc({
				url:'walook-sync.svc.php?action=personas&fecha=' + idDateValue,
				callback: function(data){
						if(typeof data != "undefined"){
							xG.alerta({msg : "Proceso Terminado"});
						}
					}
				})
		}});
}
function jsSyncAMLImportCreditos(){
	var idDateValue = $("#idDateValue").val();
	
	xG.confirmar({msg: 'CONFIRMA_ACTUALIZACION . MSG_WARN_DEL_INFO_ANT', callback: function(){
			xG.svc({
				url:'walook-sync.svc.php?action=creditoss&fecha=' + idDateValue,
				callback: function(data){
						if(typeof data != "undefined"){
							xG.alerta({msg : "Proceso Terminado"});
						}
					}
				})
		}});
}
</script>
<?php $xHP->fin(); ?>