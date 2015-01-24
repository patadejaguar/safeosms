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
function jsaEliminarLog($fecha){ my_query("DELETE FROM general_log");}
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
function jsaGetLetrasAVencer($fecha){
	$xD		= new cFecha();
	$xL		= new cSQLListas();
	$fecha 	= $xD->getFechaISO($fecha);
	$sql	= $xL->getListadoDeLetrasConCreditos($fecha, false, "", "", " AND (`creditos_tipoconvenio`.`tipo_en_sistema` =" . CREDITO_PRODUCTO_INDIVIDUAL . ") ");
	
	$xT		= new cTabla($sql, 2);
	$xT->setEventKey("jsGoPanel");
	return $xT->Show( );
}
function jsaGetCreditosSimplesMinistrados($fecha){
	$xD		= new cFecha();
	$xL		= new cSQLListas();
	$fecha 	= $xD->getFechaISO($fecha);
	$sql	= $xL->getListadoDeCreditos(false, false, false, CREDITO_PRODUCTO_INDIVIDUAL, " AND (DATE_FORMAT(fecha_ministracion, '%d')=DATE_FORMAT('$fecha', '%d')) ");
	$xT		= new cTabla($sql);
	$xT->setEventKey("jsGoPanel");
	return $xT->Show();	
}
function jsaGetCreditosPorAutorizar($fecha){
	$xD		= new cFecha();
	$xL		= new cSQLListas();
	$fecha 	= $xD->getFechaISO($fecha);
	$sql	= $xL->getListadoDeCreditos(false, true, CREDITO_ESTADO_SOLICITADO, false, " AND (SELECT COUNT(*) FROM `creditos_rechazados` WHERE `numero_de_credito`= creditos_solicitud.`numero_solicitud`) = 0 ");
	$xT		= new cTabla($sql);
	$xT->setEventKey("jsGoPanel");
	return $xT->Show();
}

function jsaGetCreditosPorMinistrar($fecha){
	$xD		= new cFecha();
	$xL		= new cSQLListas();
	$fecha 	= $xD->getFechaISO($fecha);
	$sql	= $xL->getListadoDeCreditos(false, true, CREDITO_ESTADO_AUTORIZADO, false, " AND (SELECT COUNT(*) FROM `creditos_rechazados` WHERE `numero_de_credito`= creditos_solicitud.`numero_solicitud`) = 0 ");
	$xT		= new cTabla($sql, 2);
	$xT->setEventKey("jsGoPanel");
	//$xT->setKeyField("creditos_solicitud");
	return $xT->Show();
}

function jsaGetLetrasAVencerTodas($fecha){
	$xD		= new cFecha();
	$xL		= new cSQLListas();
	$fecha 	= $xD->getFechaISO($fecha);
	$sql	= $xL->getListadoDeLetras($fecha);
	$xT		= new cTabla($sql, 2);
	$xT->setEventKey("jsGoPanel");
	//$xT->setKeyField("creditos_solicitud");
	return $xT->Show();
}

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
	$sql	= $xL->getListadoDeRecibos("", "", "", $fecha);
	$xT		= new cTabla($sql); 
	$xT->setEventKey("jsGetPanelRecibo");
	return $xT->Show();
}


function jsaActualizarIdioma($fecha){
	$xSys	= new cSystemPatch();
	$msgs	= $xSys->patch(true, false, true);
	$xCache	= new cCache();
	$xCache->clean();
	return $msgs;
}

$jxc ->exportFunction('jsaShowCalendarTasks', array('idDateValue'), "#tcalendar-task");

$jxc ->exportFunction('jsaGetLetrasAVencer', array('idDateValue'), "#tcalendar-task");
$jxc ->exportFunction('jsaGetLetrasAVencerTodas', array('idDateValue'), "#tcalendar-task");
$jxc ->exportFunction('jsaGetCreditosSimplesMinistrados', array('idDateValue'), "#tcalendar-task");
$jxc ->exportFunction('jsaGetCreditosPorAutorizar', array('idDateValue'), "#tcalendar-task");
$jxc ->exportFunction('jsaGetCreditosPorMinistrar', array('idDateValue'), "#tcalendar-task");
$jxc ->exportFunction('jsaEliminarLog', array('idDateValue'), "#tcalendar-task");
$jxc ->exportFunction('jsaGetLog', array('idDateValue'), "#tcalendar-task");
$jxc ->exportFunction('jsaGetRecibosEmitidos', array('idDateValue'), "#tcalendar-task");

$jxc ->exportFunction('jsaSetCumplido', array('id-KeyEditable'));

$jxc ->exportFunction('jsaGetIngresosDeldia', array('idDateValue'), "#tcalendar-task");
$jxc ->exportFunction('jsaGetIngresosDelMes', array('idDateValue'), "#tcalendar-task");
$jxc ->exportFunction('jsaGetIngresosMensualesPorDependencias', array('idDateValue'), "#tcalendar-task");

$jxc ->exportFunction('jsaRespaldarDB', array('idDateValue'), "#avisos");
$jxc ->exportFunction('jsaActualizarIdioma', array('idDateValue'), "#avisos");

//jsaRespaldarDB
$jxc ->process();

$x		= new jsBasicForm("", iDE_CREDITO, ".");

$xHP->init();

$xFRM	= new cHForm("frmcalendartask");
$xUsr	= new cSystemUser();
$alerts	= "";
$xUsr->init();
if($xUsr->getNivel() >= USUARIO_TIPO_CAJERO OR (OPERACION_LIBERAR_ACCIONES == true)){
	//$xFRM->addToolbar($xBtn->getBasic("Ingresos del Dia", "jsGetChart()", "grafico", "idcharts", false) );
} 

$xFRM->addToolbar($xBtn->getBasic("TR.Tareas", "jsGetInformes()", "tarea", "idtareas", false) );
$xFRM->addToolbar("<input type=\"date\"  id=\"idDateValue\" value=\"" . $xF->get(FECHA_FORMATO_MX) . "\" />");

if($xUsr->getNivel() != USUARIO_TIPO_OFICIAL_AML){

	if($xUsr->getNivel() >= USUARIO_TIPO_OFICIAL_CRED OR (MODO_DEBUG == true) OR (OPERACION_LIBERAR_ACCIONES == true)){
		$xFRM->addToolbar($xBtn->getBasic("TR.Letras Creditos Simples", "jsaGetLetrasAVencer()", "reporte", "idletrav", false) );
		$xFRM->addToolbar($xBtn->getBasic("TR.Letras Pendientes", "jsaGetLetrasAVencerTodas()", "reporte", "idletrave", false) );
		$xFRM->addToolbar($xBtn->getBasic("TR.Creditos Simples", "jsaGetCreditosSimplesMinistrados()", "lista", "idsimplev", false) );
		$xFRM->addToolbar($xBtn->getBasic("TR.Creditos Por Autorizar", "jsaGetCreditosPorAutorizar()", "lista", "idcredaut", false) );
		$xFRM->addToolbar($xBtn->getBasic("TR.Creditos Por Ministrar", "jsaGetCreditosPorMinistrar()", "lista", "idcrednpoaut", false) );
		$xFRM->OButton("TR.Recibos Emitidos", "jsaGetRecibosEmitidos()", $xBtn->ic()->REPORTE);
	}
	if($xUsr->getNivel() >= USUARIO_TIPO_GERENTE OR (OPERACION_LIBERAR_ACCIONES == true)){
		$xFRM->addToolbar($xBtn->getBasic("TR.Ingresos del Dia", "jsGetChart()", "grafico", "idcharts", false) );
		$xFRM->addToolbar($xBtn->getBasic("TR.Ingresos del Mes", "jsGetIngresosMensuales()", "grafico", "idimes", false) );
		$xFRM->addToolbar($xBtn->getBasic("TR.Ingresos por Empresa", "jsGetIngresosMensualesEmpresas()", "grafico", "idimesemp", false));	
	}
} else {
	$xF				= new cFecha();
	$xAl			= new cAml_alerts();
	$xlistas		= new cSQLListas();
	$xBtn			= new cHButton();
	$xImg			= new cHImg();
	//
	$ByEstado		= " AND `estado_en_sistema`= " . SYS_UNO;
	$fecha_inicial	= $xF->getDiaInicial();
	$fecha_final	= $xF->getDiaFinal();
	$sql			= $xlistas->getListadoDeAlertas(false, $fecha_inicial, $fecha_final, false, $ByEstado);
	$xT				= new cTabla($sql);
	$xT->setWithMetaData();	
	$xT->setKeyField( $xAl->getKey() );
	$xT->setKeyTable( $xAl->get() );
	$alerts			.= $xT->Show("TR.Alertas");	
}
if(MODO_DEBUG == true){
	$xFRM->addToolbar($xBtn->getBasic("ELiminar LOG", "jsaEliminarLog()", "grafico", "idlog", false) );
	$xFRM->addToolbar($xBtn->getBasic("Obtener LOG", "jsaGetLog()", "grafico", "idglog", false) );
	$xFRM->addToolbar($xBtn->getBasic("TR.Respaldo", "jsaRespaldarDB()", "ejecutar", "idrespdb", false) );
	$xFRM->OButton("TR.Actualizar", "jsaActualizarIdioma()", $xFRM->ic()->EJECUTAR);
}

$cTbl		= new cTabla($xLi->getListadoDeTareas(getUsuarioActual()));
$cTbl->setKeyField("idusuarios_web_notas");
$cTbl->setKeyTable("usuarios_web_notas");
$cTbl->OButton("TR.Checado", "setUpdateEstatus(_REPLACE_ID_)", $cTbl->ODicIcons()->OK);
$alerts			.=  $cTbl->Show("TR.Tareas");



$xFRM->OButton("TR.Salir", "var xG = new Gen(); xG.salir()", "salir");

$xFRM->addSeccion("idmastareas", "TR.Tareas");
$xFRM->addHElem("<div id=\"tcalendar-task\">$alerts</div>");

$xFRM->endSeccion();



$sysinfo		= "";

if (MODO_DEBUG == true AND (SYSTEM_ON_HOSTING == false)){
	$xUL			= new cHUl(); $xUL2		= new cHUl();
$sysinfo		=  $xUL->li("Base de Datos:" . MY_DB_IN)->li("Servidor: " . WORK_HOST)->li("Sucursal: " . getSucursal())
->li("Version S.A.F.E.: " . SAFE_VERSION)->li("Revision S.A.F.E: " . SAFE_REVISION)->li("Path Temporal: " . PATH_TMP)
->li("Path Backups: " . PATH_BACKUPS)->li("Fecha del Sistema: " . date("Y-m-d H:i:s"))->li("Usuario Activo: " . $xUsr->getNombreCompleto() )->li("ID de Usuario: " . $xUsr->getID())
->li("Nivel de Usuario: " . $xUsr->getNivel())
->li("SAFE DB version : " . SAFE_DB_VERSION)
->end();

$sysinfo2		= $xUL2->li("Caja Local : " . $xLoc->getCajaLocal())
->li("Localidad : " . $xLoc->DomicilioLocalidad())
->li("Clave Localidad : " . $xLoc->DomicilioLocalidadClave())
->li("Municipio : " . $xLoc->DomicilioMunicipio())
->li("Estado : " . $xLoc->DomicilioEstado())
->li("Clave Estado : " . $xLoc->DomicilioEstadoClaveABC() )
->li("C.P. : " . $xLoc->DomicilioCodigoPostal())->end();

$xFRM->addSeccion("idmaslogs", "TR.Sistema");
$xFRM->addDivSolo($sysinfo, $sysinfo2, "tx24", "tx24" );
$xFRM->endSeccion();
}

if(getUsuarioActual(SYS_USER_NIVEL) == USUARIO_TIPO_CAJERO){
	$xNot		= new cHNotif();
	$xCaja		= new cCaja();
	if($xCaja->getEstatus() == TESORERIA_CAJA_CERRADA){
		$xNot->get("", "idestadocaja", $xNot->ERROR);
	} else {
		
	}
}

$xFRM->addAviso("", "idavisos");
$xFRM->OHidden("id-KeyEditable", "", "");
//$xFRM->addHTML($menu);
echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
?>
</body>
<script>

$(document).ready( function(){
	$('#idDateValue').pickadate({format: 'dd-mm-yyyy',formatSubmit:'yyyy-mm-dd'});
	
});

function jsGetPanelRecibo(id){	var xR	= new RecGen(); xR.panel(id); }
function jsGetInformes(){
	jsaShowCalendarTasks();
	//jsaGetIngresosDeldia();
}
function jsGetIngresosMensuales() {
	$('#suchart').empty();
	jsaGetIngresosDelMes();
	tip('#tcalendar-task', "Grafico Generado!", 5000);
	setTimeout("jsGetChart()",4500);
}
function jsGetIngresosMensualesEmpresas() {
	$('#suchart').empty();
	jsaGetIngresosMensualesPorDependencias();
	tip('#tcalendar-task', "Grafico Generado!", 5000);
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
</script>
</html>
