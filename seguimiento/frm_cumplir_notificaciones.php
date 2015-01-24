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
$xHP			= new cHPage("TR.Cumplir notificaciones");


$jxc = new TinyAjax();
function  mostrar_notificaciones($finicial, $ffinal, $estatus){
	$sql = "SELECT
	`seguimiento_notificaciones`.`idseguimiento_notificaciones` AS `codigo`,
	`seguimiento_notificaciones`.`socio_notificado` AS 'socio',
	`socios`.`nombre`,
	`seguimiento_notificaciones`.`numero_solicitud` AS 'solicitud',
	`seguimiento_notificaciones`.`numero_notificacion` AS 'num',
	`seguimiento_notificaciones`.`fecha_notificacion`,
	/*`oficiales`.`nombre_completo` AS 	`oficial_a_cargo`,*/
	/*`seguimiento_notificaciones`.`fecha_vencimiento`,*/
	`seguimiento_notificaciones`.`capital`,
	`seguimiento_notificaciones`.`interes`,
	`seguimiento_notificaciones`.`moratorio`,
	`seguimiento_notificaciones`.`otros_cargos`,
	`seguimiento_notificaciones`.`total` 
FROM
	`seguimiento_notificaciones` `seguimiento_notificaciones` 
		INNER JOIN `socios` `socios` 
		ON `seguimiento_notificaciones`.`socio_notificado` = `socios`.`codigo` 
			INNER JOIN `oficiales` `oficiales` 
			ON `seguimiento_notificaciones`.`oficial_de_seguimiento` = `oficiales`
			.`id` 
WHERE
	(`seguimiento_notificaciones`.`estatus_notificacion` ='$estatus')
		ORDER BY
			`seguimiento_notificaciones`.`idseguimiento_notificaciones`";
	//$cmdCancel = new cCmdByOrder("common/exit.png", "Cumplir Notificacion", "jsSetCumplido(event);", "cmd@_REPLACE_ID_");
	$cmdOk	= "<label for='cmd@_REPLACE_ID_'><input type='checkbox' id='cmd@_REPLACE_ID_' /></label>";
	$cTbl = new cTabla($sql);
	$cTbl->setWidth();
	$cTbl->addTool(2);
	$cTbl->addTool(1);
	//$cTbl->addEspTool($cmdCancel->show());
	$cTbl->addEspTool($cmdOk);
	//$cTbl->addTool(2);
	$cTbl->setTdClassByType();
	$cTbl->setKeyField("idseguimiento_notificaciones");
	return  $cTbl->Show();

}
	$jxc ->exportFunction('mostrar_notificaciones', array('i_fecha_inicial', 'i_fecha_final', 'idEstatus'), "#ilistado_notificaciones");	
	$jxc ->process();
	
	
$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xBtn		= new cHButton();		
$xTxt		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();
$msg		= "";
?>
<!-- <hr /><p class="frmTitle"><script> document.write(document.title); </script></p> -->
<hr /> 
<form name="frm_cumplir_notificaciones" method="post" action="">
<table  >
	<tr>
		<td>Fecha Inicial</td>
		<td><input type="text" name="c_fecha_inicial" id="i_fecha_inicial" value="<?php echo fechasys(); ?>" />
		<?php echo GO_CALENDAR("i_fecha_inicial"); ?></td>
		<td>Fecha Final</td>
		<td><input type="text" name="c_fecha_final" id="i_fecha_final" value="<?php echo fechasys(); ?>" />
		<?php echo GO_CALENDAR("i_fecha_final"); ?></td>
	</tr>
	<tr>
		<td>Estatus Actual</td>
		<!-- 'pendiente','efectuado','comprometido','cancelado','vencido' -->
		<td><select name="cEstatus" id="idEstatus">
				<option value="pendiente">Pendiente</option>
				<option value="efectuado">Efectuado</option>
				<option value="comprometido">Comprometido</option>
				<option value="cancelado">Cancelado</option>
				<option value="vencido">Vencido</option>
			</select>
		</td>
	</tr>
	<tr>
		<td><input type="button" name="cmdShowNot" onclick="mostrar_notificaciones();" value="Mostrar Notificaciones" /></td>
		<td><?php echo $xBtn->getBasic("TR.Dar por Cumplidos las Notificaciones Marcadas", "actionByCmd('Common_3de8e697db5bb95c43c3139743a47d8b', 2);", "verde"); ?></td>
		<td><?php echo $xBtn->getBasic("TR.Cancelar las Notificaciones Marcadas", "actionByCmd('Common_3de8e697db5bb95c43c3139743a47d8b', 4);", "cumplido"); ?></td>
	</tr>
</table>
<p id="PMsg" class="aviso"></p>
<div id="ilistado_notificaciones"></div>
</form>
</body>
<?php
$jxc ->drawJavaScript(false, true);
jsbasic("frm_cumplir_notificaciones", "", ".");
?>
<script>
var Frm = document.frm_cumplir_notificaciones;
var jsrCommonSeguimiento	= "../js/seguimiento.common.js.php";
var divLiteral				= "<?php echo STD_LITERAL_DIVISOR; ?>";
function actualizame(id) { var url = "../utils/frm8db7028bdcdf054882ab54f644a9d36b.php?t=seguimiento_notificaciones&f=idseguimiento_notificaciones=" + id; myurl = window.open(url, ""); myurl.focus(); } function eliminame(id) { var siXtar = confirm("Desea en Realidad Eliminar \n el Registro Seleccionado"); if(siXtar==true){ var sURL = "../utils/frm9d23d795f8170f495de9a2c3b251a4cd.php?t=seguimiento_notificaciones&f=idseguimiento_notificaciones=" + id; delme = window.open(sURL, "", "width=300,height=300,scrollbars=yes,dependent");  document.getElementById("tr-" + id).innerHTML = ""; } else { if( window.console ) window.console.log( '' ) ; window.statusText = "Operacion Cancelada"; } }

function actionByCmd(cmd, stat){
	  	var isLims = Frm.elements.length - 1;
  		
  		for(i=0; i<=isLims; i++){
			var mTyp 	= Frm.elements[i].getAttribute("type");
			var mID 	= Frm.elements[i].getAttribute("id");
			var mVal	= Frm.elements[i].checked;
			//Verificar si es mayor a cero o no nulo
			if ( (mID!=null) && (mID.indexOf("cmd@")!= -1) && (mTyp == "checkbox") && (mVal == true) ){
				//Despedazar el ID para obtener el denominador comun
				var aID	= mID.split("@");
				jsrsExecute(jsrCommonSeguimiento, jsEchoMsg, cmd, aID[1] + divLiteral + stat );
  			}
  		}
  	//document.getElementById("PMsg").innerHTML = "";
}
function jsEchoMsg(msg){
	document.getElementById("PMsg").innerHTML += " <br />" + msg;
}
</script>
</html>
