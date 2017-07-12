<?php
//=====================================================================================================
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	include_once("../core/core.db.inc.php");
	$theFile			= __FILE__;
	$permiso			= getSIPAKALPermissions($theFile);
	if($permiso === false){	header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//=====================================================================================================
$xHP			= new cHPage("TR.Calendario de llamadas");
$jxc 			= new TinyAjax();
function jsaGetCallsToToday($fecha, $efectuadas, $vencidas, $canceladas, $mark){
	$xF			= new cFecha();
	$fecha		= $xF->getFechaISO($fecha);
	$cCalls		= new cHDicccionarioDeTablas();
	return $cCalls->getLlamadas($fecha, $fecha, $mark, $efectuadas, $canceladas, $vencidas);
}
function jsaSaveNote($id, $note){
	$msg	= "";
	$date	= date("Y-m-d");
	$sqlUN = "UPDATE seguimiento_llamadas
    			SET observaciones=CONCAT(observaciones, '\n$date\t', '$note')
    			WHERE  idseguimiento_llamadas=$id";

    $n = my_query($sqlUN);
    if ($n["stat"] != false ){
    	$msg = "Se Actualizo la Llamada #$id con la Nota [$note]";
    }
    return $msg;
}




$jxc ->exportFunction('jsaGetCallsToToday', array('idfecha-0', "idChkEfectuadas", "idChkVencidas", "idChkCanceladas", "idMarkRecord"), "#divcalendar");
$jxc ->exportFunction('jsaSaveNote', array('idIDNote', "idTxtNote"), "#idmsg");

$jxc ->process();

$xFRM		= new cHForm("frmnav", "./");
$xBtn		= new cHButton();
$xTxt		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();
$xChk		= new cHCheckBox();

$xHP->init("jsaGetCallsToToday()");



$xFRM		= new cHForm("frmcalendario");
$xFRM->setTitle($xHP->getTitle());
$xFRM->OHidden("idMarkRecord", 0);

$xDate->setDivClass("tx24");

$xFRM->OHidden("idTxtNote", "");
$xFRM->OHidden("idIDNote", "");
$xChk->setDivClass("");
$xDate->setDivClass("");

$xFRM->addHTML("<idv id='divcalendar'></div>");
$xFRM->OButton("TR.Obtener Lista", "jsaGetCallsToToday()", $xFRM->ic()->EJECUTAR);

$xFRM->addToolbar($xChk->get("TR.Incluir Efectuadas", "idChkEfectuadas"));
$xFRM->addToolbar($xChk->get("TR.Incluir Canceladas", "idChkCanceladas"));
$xFRM->addToolbar($xChk->get("TR.Incluir Vencidas", "idChkVencidas"));
$xFRM->addToolbar($xDate->get("TR.Fecha"));
$xFRM->OButton("TR.Anterior", "getBackRecords()", $xFRM->ic()->ATRAS, "idanterior");
$xFRM->OButton("TR.Inicio", "getFirstRecords()", $xFRM->ic()->HOME, "idinicio");
$xFRM->OButton("TR.Siguiente", "getNextRecords()", $xFRM->ic()->ADELANTE, "idsiguiente");

echo $xFRM->get();
 
$jxc ->drawJavaScript(false, true);
?>
</body>
<script  >
var MINUTE 				= 60 * 1000;
var HOUR 				= 60 * MINUTE;
var DAY 				= 24 * HOUR;
var WEEK 				= 7  * DAY;
var CURDATE				= "<?php echo fechasys(); ?>";
var jsrsSeguimiento 	= "./jseguimiento.js.php";
var vLITERAL_SEPARATOR	= "<?php echo STD_LITERAL_DIVISOR; ?>";
var LIMITRECORDS		= 20;
var xG					= new Gen();
/**
* Return la Fecha seleccionada
*/
function flatSelected(cal, date) {
	document.getElementById("idfecha-0").value = date;
	jsaGetCallsToToday();
}
function isDisabled(date) {
  var today = new Date();
  return (Math.abs(date.getTime() - today.getTime()) / DAY) > 45;
}
/**
* Procesos del Form
*/

function jsMsgBox(Msg){
	if ( Msg != ""){
		alert(Msg);
	}
}
function getConsultaRecibo(strID){

	var cURL = "../frmextras/frmrecibodepago.php?recibo=" + strID;

	xG.w({url: cURL, tiny : true});
}
function jsSetAction(strID){
	var vActionType = document.getElementById("ids-" +  strID).value;

	switch(vActionType){
		case "set-cumplido":
			$("#idTxtNote").val("efectuado"); $("#idIDNote").val(strID); jsaSetEstadoLlamada();
			//jsrsExecute(jsrsSeguimiento, jsMsgBox, 'Common_84fb77b61619740746901b9329ff2c9d', strID + vLITERAL_SEPARATOR + "efectuado");
			var mMsg	= confirm("Desea Agregar el Resultado de la Llamada");
			if ( mMsg == false ){
				document.getElementById("tr-" + strID).innerHTML = "";
			} else {
					document.getElementById("td-" + strID).innerHTML = "<textarea id='id-txta-" + strID + "' onchange='saveNotes(" + strID + ");' name='c-txta-" + strID + "' cols='40' rows='2'></textarea>";
					document.getElementById("id-txta-" + strID).focus();
			}

		break;
		case "set-cancelado":
			$("#idTxtNote").val("efectuado"); $("#idIDNote").val(strID); jsaSetEstadoLlamada();
		break;
		//Agregar Llamada
		case "add-llamada":
			//Agregar primero el HTML
			document.getElementById("td-" + strID).innerHTML = "Fecha (AAAA-MM-DD): <input type='input' name='cFechaLlamada-" + strID + "' value='" + CURDATE + "' id='idFechaLlamada-" + strID + "'/> <br /> " +
																"Hora (HH:MM): <input type='input' name='cHoraLlamada-" + strID + "' value='12:00' id='idHoraLlamada-" + strID + "'/> <br />" +
																"<input type='button' name='cSaveLlamada-" + strID + "' value='Guardar Llamada' onclick='jsReplicateCall(" + strID + ")'/> ";
			document.getElementById("idFechaLlamada-" + strID).focus();
			document.getElementById("idFechaLlamada-" + strID).select();
		break;
		case "add-compromiso":
			//Agregar Compromisos
			var mSocio		= document.getElementById("socio-" + strID).value;
			var mCredito	= document.getElementById("credito-" + strID).value;

			var xWin = "frm_agregar_compromisos.php?persona=" + mSocio + "&credito=" + mCredito;
			xG.w({url: xWin, tiny : true});
		break;
		case "add-memo":
			//Agregar Memo
			var mSocio		= document.getElementById("socio-" + strID).value;
			var mCredito	= document.getElementById("credito-" + strID).value;
			var mGrupo		= document.getElementById("grupo-" + strID).value;
			var cURL = "../frmsocios/frmhistorialdesocios.php?d=6" + vLITERAL_SEPARATOR + mSocio
															+ vLITERAL_SEPARATOR + mCredito + vLITERAL_SEPARATOR + mGrupo;
			xG.w({url: cURL, tiny : true});
		break;
		case "add-notif-1":
			addNotificacion(strID, 1);
		break;
		case "add-notif-2":
			addNotificacion(strID, 2);
		break;
		case "add-notif-3":
			addNotificacion(strID, 3);
		break;
		case "add-notif-e":
			addNotificacion(strID, 4);
		break;
		case "set-vivienda":
			var mSocio	= document.getElementById("socio-" + strID).value;
			var mURI = "../frmsocios/frmsociosvivienda.php?socio=" + mSocio;
			xG.w({url: mURI, tiny : true});
		break;
		case "set-notes":
					document.getElementById("td-" + strID).innerHTML = "<textarea id='id-txta-" + strID + "' onchange='saveNotes(" + strID + ");' name='c-txta-" + strID + "' cols='40' rows='2'></textarea>";
					document.getElementById("id-txta-" + strID).focus();
		break;
		//Informacion
		case "info-llamadas":
			var mSocio		= document.getElementById("socio-" + strID).value;
			var mCredito	= document.getElementById("credito-" + strID).value;
			var mURI 		= "../rptseguimiento/llamadas_individuales.rpt.php?persona=" + mSocio + "&credito=" + mCredito;
			xG.w({url: mURI, tiny : true});
		break;
		case "info-notificaciones":
			var mSocio		= document.getElementById("socio-" + strID).value;
			var mCredito	= document.getElementById("credito-" + strID).value;
			var mURI 		= "../rptseguimiento/notificaciones_individuales.rpt.php?persona=" + mSocio + "&credito=" + mCredito;
			xG.w({url: mURI, tiny : true});
		break;
		case "info-compromisos":
			var mSocio		= document.getElementById("socio-" + strID).value;
			var mCredito	= document.getElementById("credito-" + strID).value;
			var mURI 		= "../rptseguimiento/rptcompromisos.php?persona=" + mSocio + "&credito=" + mCredito;
			xG.w({url: mURI, tiny : true});
		break;
		case "info-moral":
			var mSocio		= document.getElementById("socio-" + strID).value;
			var mURI 		= "../rptseguimiento/historial_individual.rpt.php?perona=" + mSocio;
			xG.w({url: mURI, tiny : true});
		break;
		case "info-creditos":
			var mCredito	= document.getElementById("credito-" + strID).value;
			var mURI 		= "../rpt_edos_cuenta/rptestadocuentacredito.php?credito=" + mCredito;
			xG.w({url: mURI, tiny : true});
		break;
		case "set-none":
		break;
		case "set-edit":
			//Editar Llamada
			var cURL = "../seguimiento/editar_llamadas.frm.php?x=" + strID;
			xG.w({url: cURL, tiny : true});
		break;
		default:
			jsMsgBox(">>Accion no Implementada!!");
		break;
	}
	//$("#ids-" + strID).val("set-none");

}
function saveNotes(strID){
	//alert(document.getElementById("id-txta-" + strID).value);
	document.getElementById("idTxtNote").value 	= document.getElementById("id-txta-" + strID).value;
	document.getElementById("idIDNode").value	= strID;
	jsaSaveNote();
	document.getElementById("td-" + strID).innerHTML	= "";
}
function jsReplicateCall(strID){
			var mSocio		= document.getElementById("socio-" + strID).value;
			var mCredito	= document.getElementById("credito-" + strID).value;
			var mHora		= document.getElementById("idHoraLlamada-" + strID).value;
			var mFecha		= document.getElementById("idFechaLlamada-" + strID).value;
			var cmpSTR		= mSocio + vLITERAL_SEPARATOR + mCredito + vLITERAL_SEPARATOR + mFecha + vLITERAL_SEPARATOR + mHora;
			jsrsExecute(jsrsSeguimiento, jsMsgBox, 'Common_eb8d3f1b179bfca7a3d31880b4d66778', cmpSTR);
			document.getElementById("td-" + strID).innerHTML = "";
}
function addNotificacion(strID, intTipo){
	var mSocio		= document.getElementById("socio-" + strID).value;
	var mCredito	= document.getElementById("credito-" + strID).value;
	var mGrupo		= document.getElementById("grupo-" + strID).value;

	var cURL = "../seguimiento/add_notif_to_oficial.popup.php?i=&s=" + mSocio +
														"&c="	+ mCredito + "&g=" + mGrupo + "&t=" + intTipo;
	xG.w({url: cURL, tiny : true});
}
function getNextRecords(){
	var	mMark	= new Number(document.getElementById("idMarkRecord").value);
		mMark	+= LIMITRECORDS;

		document.getElementById("idMarkRecord").value = mMark;
		jsaGetCallsToToday();
}
function getBackRecords(){
	var	mMark	= new Number(document.getElementById("idMarkRecord").value);
		mMark	-= LIMITRECORDS;
		if (mMark < 0 ){
			mMark	= 0;
		}
		document.getElementById("idMarkRecord").value = mMark;
		jsaGetCallsToToday();
}
function getFirstRecords(){
		mMark	= 0;
		document.getElementById("idMarkRecord").value = mMark;
		jsaGetCallsToToday();
}
</script>
</html>