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
$xP			= new cHPage("TR.Calendario de Compromisos", HP_FORM);
$oficial 	= elusuario($iduser);
$jxc 		= new TinyAjax();

function n_week($ignium){
if (!$ignium){
	$mesa 		= date("m");
	$annoa 		= date("Y");
	//$fecha = date("Y-m-d", strtotime("$annoa-$mesa-01"));

} else {
	$mesa 		= date("m", strtotime($ignium)) + 1;
	$annoa 		= date("Y", strtotime($ignium));
	if ($mesa >12){
		$mesa = 1;
		$annoa = $annoa +1;
	}
}
	$xF				= new cFecha(0, "$annoa-$mesa-01" );
	$fecha 			= $xF->get();
	$dias_mes 		= $xF->getDiasDelMes();
	$idm  			= $xF->mes();
	$month 			= $xF->getMesNombre();
	//Valores Iniciales
	$tdweek 		= "";
	$colorweek 		= "#FFDFC6";
	$nsemana 		= 1;
	$tcal 			= "";

	for ($i=1; $i<=$dias_mes; $i++){
		$nowdate = "$annoa-$idm-$i";
		$nday = date("l", strtotime($nowdate));

		if ($nday =="Monday"){

			$dia 		= dia_semana($nowdate);
			$cnowdate 	= fecha_corta($nowdate);
			if ($tcal == "cal_2"){
				$tcal = "cal_1";
				$tdweek = $tdweek . "<td class='$tcal' onclick='show_week(\"$nowdate\")'>SEMANA: $nsemana <br /> $cnowdate</td>";
			} else {
				$tcal = "cal_2";
				$tdweek = $tdweek . "<td class='$tcal' onclick='show_week(\"$nowdate\")'>SEMANA: $nsemana <br /> $cnowdate</td>";
			}
			$nsemana++;
		}
	}
	return "
	<center>
	<table class='calendar_week'>
		<tr>
			<td class='$tcal'><img src='../images/common/query_back_down.png' onclick='back_week(\"$nowdate\");' /></td>
			$tdweek
			<td class='$tcal'><img src='../images/common/query_next_down.png' onclick='next_week(\"$nowdate\");' /></td>
		</tr>
	</table>
	</center>
	<hr />";
}
function b_week($ignium){
if (!$ignium){
	$mesa 		= date("m");
	$annoa 		= date("Y");
	//$fecha = date("Y-m-d", strtotime("$annoa-$mesa-01"));

} else {
	$mesa 		= date("m", strtotime($ignium)) - 1;
	$annoa 		= date("Y", strtotime($ignium));
	if ($mesa < 1){
		$mesa 	= 12;
		$annoa 	= $annoa -1;
	}
}

	$xF				= new cFecha(0, "$annoa-$mesa-01" );
	$fecha 			= $xF->get();
	$dias_mes 		= $xF->getDiasDelMes();
	$idm  			= $xF->mes();
	$month 			= $xF->getMesNombre();	
	//Valores Iniciales
	$tdweek 		= "";
	$colorweek 		= "#FFDFC6";
	$nsemana 		= 1;
	$tcal 			= "";

	for ($i=1; $i<=$dias_mes; $i++){
		$nowdate = "$annoa-$idm-$i";
		$nday = date("l", strtotime($nowdate));

		if ($nday =="Monday"){

			$dia = dia_semana($nowdate);
			$cnowdate = fecha_corta($nowdate);
			if ($tcal == "cal_2"){
				$tcal = "cal_1";
				$tdweek = $tdweek . "<td class='$tcal' onclick='show_week(\"$nowdate\")'>SEMANA: $nsemana <br /> $cnowdate</td>";
			} else {
				$tcal = "cal_2";
				$tdweek = $tdweek . "<td class='$tcal' onclick='show_week(\"$nowdate\")'>SEMANA: $nsemana <br /> $cnowdate</td>";
			}
			$nsemana++;
		}
	}
	return "<hr />
	<center>
	<table class='calendar_week'
	cellpadding='2' cellspacing='2' border='2'>
		<tr>
			<td class='$tcal'><img src='../images/common/query_back_down.png' onclick='back_week(\"$nowdate\");' /></td>
			$tdweek
			<td class='$tcal'><img src='../images/common/query_next_down.png' onclick='next_week(\"$nowdate\");' /></td>
		</tr>
	</table>
	</center>
	<hr />";
}
function s_week($ignium){
	$aImgs 					= array();
	$aImgs["no_cumplido"] 	= "red_dot.png";
	$aImgs["cancelado"] 	= "black_dot.png";
	$aImgs["pendiente"]		= "yellow_dot.png";
	$aImgs["cumplido"] 		= "green_dot.png";

	$aMsg 					= array();
	$aMsg["no_cumplido"] 	= "NO CUMPLIDO";
	$aMsg["cancelado"] 		= "CANCELADO";
	$aMsg["pendiente"]		= "PENDIENTE";
	$aMsg["cumplido"] 		= "CUMPLIDO";


	
	$td 	= "";
	$th 	= "";
	$cls	= "day_1";
	for($i=0; $i<=6; $i++){
		$dia 		= sumardias($ignium, $i);
		$ndia 		= dia_semana($dia);
		$ncdia 		= getFechaLarga($dia);
		$compro 	= "";

		$sqlob = "SELECT socios.codigo,
				socios.nombre,
				seguimiento_compromisos.tipo_compromiso,
				seguimiento_compromisos.anotacion,
				seguimiento_compromisos.idseguimiento_compromisos AS 'id',
				seguimiento_compromisos.estatus_compromiso
				FROM socios, seguimiento_compromisos
				WHERE seguimiento_compromisos.socio_comprometido=socios.codigo
				AND seguimiento_compromisos.fecha_vencimiento='$dia'";
		$rs = mysql_query($sqlob);
			while($rwc = mysql_fetch_array($rs)){

					$imgestat 	= $aImgs[$rwc["estatus_compromiso"]];
					$msgt 		= $aMsg[$rwc["estatus_compromiso"]];

				$id 	= $rwc["id"];
				$tipo	= str_replace("promesa_de_", "", $rwc["tipo_compromiso"]);
				$compro .= "\n
				<tr>
				<td  	class=\"strech\" id=\"c@$id\"
						title='$msgt [$rwc[1]]'
						onclick='show_compromiso(\"$id\");'><img alt=\"Estatus: $rwc[5] \" src=\"../images/seguimiento/$imgestat\" align=\"middle\" title='$msgt [$rwc[1]]' /> $rwc[0] - " . $tipo . "</td>
				</tr>";
			}
		@mysql_free_result($rs);
		//Agregar compromisos de pago
		$sqLetras	= "SELECT
				`creditos_solicitud`.`numero_socio`,
				`socios`.`nombre`,
				`creditos_solicitud`.`tipo_convenio`,
				`letras`.`periodo_socio`,
				`letras`.`letra`,
				`letras`.`fecha_de_pago`
			FROM
				`letras` `letras` 
					INNER JOIN `creditos_solicitud` `creditos_solicitud` 
					ON `letras`.`docto_afectado` = `creditos_solicitud`.`numero_solicitud` 
						INNER JOIN `socios` `socios` 
						ON `creditos_solicitud`.`numero_socio` = `socios`.`codigo`
			WHERE 
			`letras`.`fecha_de_pago` = '$dia'
			AND letra > 0
			AND ( `creditos_solicitud`.`tipo_convenio` = " . CREDITO_PRODUCTO_INDIVIDUAL . "
				OR
			`creditos_solicitud`.`tipo_convenio` = " . CREDITO_PRODUCTO_REVOLVENTES . " ) ";
		/*
		$mql	= new MQL();
		$select	= $mql->select();
		$select->set($sqLetras);
		$datos	= $select->exec();
		
		foreach($datos as $datos){
			$socio		= $datos["numero_socio"];
			$nombre		= $datos["nombre"];
			$credito	= "";
			$parcialidad	= $datos["periodo_socio"];
			$letra		= getFMoney( $datos["letra"]);
			
			$compro .= "\n
				<tr>
				<td  	class=\"strech\" id=\"c@$id\"
						title='$nombre'
						onclick=''><img src=\"../images/bancos.png\" class=\"x16\" /> $socio $parcialidad - $letra </td>
				</tr>";
		}*/
		if($cls == "day_1"){
			$cls = "day_2";
		} else {
			$cls = "day_1";
		}
		$td = $td . "\n <td class='$cls'>
					<table width='100%' aling=\"center\">
						<tbody>
						<tr>
							<td class=\"strech\" title='Compromisos del dia $ncdia'><center>
							<img src=\"../images/common/icon-new.png\" onclick='addCompromisos(\"0|0|$dia\");' alt='Agregar un Compromiso' />
							<img src=\"../images/common/stock_navigator-all-or-sel-toggle.png\" onclick='mostrar_un_dia(\"$dia\");' alt='Mostrar Compromisos' />
							$ndia
							</center></td>
						</tr>
						$compro
						</tbody>
					</table>
					</td>";
		//$th = $th . "<th class='compact'>$ncdia</th>";

	}
	return "<table class='calendar_day'
	cellpadding='2' cellspacing='2' border='2'>
		<tr>
			$td
		<tr>
	</table>";
}

	$jxc ->exportFunction('n_week', array('idfecha'), "#semanas_del_mes");
	$jxc ->exportFunction('b_week', array('idfecha'), "#semanas_del_mes");
	$jxc ->exportFunction('s_week', array('idfecha'), "#calendario_por_semana");
	$jxc ->process();

$xP->init("next_week()");

/**
Clase de scripts basicos
*/

?>
<fieldset>
	<legend>Calendario de Compromisos</legend>
<input type="hidden" value="" id="idfecha" />
<div id='semanas_del_mes'></div>
<div id='calendario_por_semana'></div>
</fieldset>
</body>
<script language='javascript' src='../jsrsClient.js'></script>
<?php $jxc ->drawJavaScript(false, true); ?>
<script  >
var jsrsSeguimiento 	= "./jseguimiento.js.php";
var vLITERAL_SEPARATOR	= "<?php echo STD_LITERAL_DIVISOR; ?>";
function next_week(id){
	var NewID = id;

	if (NewID){
		//alert(NewID);
		document.getElementById("idfecha").value = NewID;
	}
	n_week();
}

function back_week(id){
	var NewID = id;

	if (NewID){
		//alert(NewID);
		document.getElementById("idfecha").value = NewID;
	}
	b_week();
}

function show_week(id){
	var NewID = id;
	if (NewID){
		//alert(NewID);
		document.getElementById("idfecha").value = NewID;
	}
	s_week();
}
function editar_compromiso(Id){
	var pfcred = "../seguimiento/frm_agregar_compromisos.php?clave=" + Id;
	document.getElementById("calendario_por_semana").innerHTML = "" +
	"<iframe src=\"" + pfcred + "\" name=\"FECompromiso\" " +
	" title=\"Editar Compromisos\" width=\"100%\" height=\"600px\" frameborder=0 scrolling=\"auto\" " +
	" style=\"margin-left: 0px; margin-right: 0px; margin-top: 5px; margin-bottom: 0px;\"></iframe>";

}
function show_compromiso(id){
	//jsrsExecute(jsrsSeguimiento, ret_mostrar_un_x, 'getCompromisosById', Id);
	var xSeg	= new SegGen();
	xSeg.getDetalleDeCompromiso({ clave : id });
}
function mostrar_un_dia(vFecha){
	jsrsExecute(jsrsSeguimiento, ret_mostrar_un_x, 'getCompromisosDia', vFecha);
}
function ret_mostrar_un_x(sHTML){
	document.getElementById("calendario_por_semana").innerHTML = "";
	document.getElementById("calendario_por_semana").innerHTML = sHTML;
}
function rptLlamadas(isd){
	//reporte de llamadas
			var mURI 		= "../rptseguimiento/llamadas_individuales.rpt.php?persona=" + isd;
			jsGenericWindow(mURI);
}
function rptCompromisos(isd){
	var xWin	= "../rptseguimiento/rptcompromisos.php?persona=" + isd;
	//Reporte de Compromisos
	jsGenericWindow(xWin);
}
function rptNotificaciones(isd){
	//Reporte de Notificaciones
			var mURI 		= "../rptseguimiento/notificaciones_individuales.rpt.php?persona=" + isd;
			jsGenericWindow(mURI);
}
function addLlamadas(isd){
	//Agregar Llamadas
	var xWin = "editar_llamadas.frm.php?n=" + isd;
	jsGenericWindow(xWin);
}
function addCompromisos(isd){
	//Agregar Compromisos
	var xWin = "frm_agregar_compromisos.php?p=" + isd;
	jsGenericWindow(xWin);
}
function addMemo(isd){
	//Agregar Memo
	var cURL = "../frmsocios/frmhistorialdesocios.php?d=" + isd;
	jsGenericWindow(cURL);
}
/**
* Modificar Estatus de los compromisos
*/
function setCumplido(id){
	jsrsExecute(jsrsSeguimiento, jsMsgBox, 'jsSetEstatusCompromiso', id + vLITERAL_SEPARATOR + "cumplido");
	document.getElementById("fs-" + id).style.visible = "hidden";
}
function setCancelado(id){
	jsrsExecute(jsrsSeguimiento, jsMsgBox, 'jsSetEstatusCompromiso', id + vLITERAL_SEPARATOR + "cancelado");
	document.getElementById("fs-" + id).style.visible = "hidden";
}
function setVencido(id){
	jsrsExecute(jsrsSeguimiento, jsMsgBox, 'jsSetEstatusCompromiso', id + vLITERAL_SEPARATOR + "no_cumplido");
	document.getElementById("fs-" + id).style.visible = "hidden";
}
function jsGenericWindow(mFile, winTop, winLeft, winHeight, winWidth){
		var mIDWin = Math.random();
		var windowName = "myWin" + mIDWin;
        if(!winLeft)	{	var winLeft		= parseInt(screen.width * 0.10);	}
	    if(!winTop)		{	var winTop		= parseInt((screen.height * 0.10) + 100);	}
		if(!winHeight)	{	var winHeight	= parseInt((screen.height - (screen.height * 0.10) ) - 100);	}
		if(!winWidth)	{	var winWidth	= parseInt((screen.width - (screen.width * 0.10)));		}

        var windowFeatures = "width=" + winWidth + ",height=" + winHeight + ",status,scrollbars,resizable,left=" + winLeft + ",top=" + winTop;
        newWindow = window.open(mFile, windowName, windowFeatures);
		newWindow.focus();
}
function jsMsgBox(Msg){
	alert(Msg);

}
function getConsultaRecibo(id){
	var cURL = "../frmextras/frmrecibodepago.php?recibo=" + id;
	jsGenericWindow(cURL);
}
function addNotificacion(strID){
	var cURL = "../seguimiento/add_notif_to_oficial.popup.php?i=&" + strID;
	jsGenericWindow(cURL);
}
</script>
</html>