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
$xHP				= new cHPage("TR.Poliza Contable", HP_FORM);
$oficial 	= elusuario($iduser);

//$xHT		= new cHButton("");

$jxc = new TinyAjax();
//Mvtos
//Polizas
function jsaSavePoliza($idejercicio, $idperiodo, $idtipopol, $idpoliza, $idfechapol, $idconceptopol, $cargos, $abonos) {
	$msg 	= "";
	$sqle = "SELECT count(numeropoliza) AS 'ids' FROM contable_polizas
	WHERE ejercicio=$idejercicio
	AND periodo=$idperiodo
	AND tipopoliza=$idtipopol
	AND numeropoliza=$idpoliza";
	$hay = mifila($sqle, "ids");
	if($hay==0){
		$idfechapol = getFechaUS($idfechapol);
	$sqlnpol = "INSERT INTO contable_polizas(ejercicio, periodo, tipopoliza, numeropoliza, clase, impresa, concepto, fecha, cargos, abonos, diario)
    VALUES($idejercicio,
	$idperiodo, $idtipopol,
	$idpoliza, 	1,
	'false', '$idconceptopol',
	'$idfechapol', 	$cargos, 	$abonos,
	999)";
	//echo $sqle;
	$xbs = my_query($sqlnpol);
		if ($xbs["stat"] != false){
			$msg	= "Se ha Agregado la Poliza Exitosamente!!";
		}
	} else {
		//jsaSearchPoliza($idejercicio, $idperiodo, $idtpol, $idpoliza);
	}

	return $msg;
}
function jsaEditarPoliza($idtipopol, $idpoliza, $idfechapol, $idconceptopol, $cargos, $abonos, $NumeroAnterior) {
	$msg		= "";
	$idfechapol = getFechaUS($idfechapol);
	$cPol 		= new cPoliza($idtipopol, $idpoliza);
	//"numeropoliza" 	=> $idpoliza,
	$arrUPol	= array("tipopoliza" 	=> $idtipopol,
						"concepto" 		=> "$idconceptopol",
						"fecha"			=> "$idfechapol",
						"cargos"		=> $cargos,
						"abonos"		=> $abonos);
	$cPol->setUpdatePoliza($arrUPol);

	if ($cPol->mRaiseError == true ){
		$msg		.= $cPol->getMessages("html");
	} else {
		$msg	.= "Poliza $idpoliza guardada";
	}
	//my_query($sqlup);
	//return  "<p class='aviso'>$sqlup</p>";
	//ACTUALIZAR MOVIMIENTOS
	/**
	 * PASO 1 .- Seleccionar los Movimientos
	 * PASO 2 .- Revertir el Movimiento
	 * PASO 3 .- Eliminar el Movimiento
	 * PASO 4 .- Agregar con los mismo Datos el Movimiento
	 */
	return 	$msg;
}
function jsaSearchPoliza($idejercicio, $idperiodo, $idtipopol, $idpoliza){
	$sqle = "SELECT * FROM contable_polizas WHERE ejercicio=$idejercicio
	AND periodo=$idperiodo
	AND tipopoliza=$idtipopol
	AND numeropoliza=$idpoliza";
	$dpol = getFilas($sqle);

	if($dpol["numeropoliza"]){
			$tab = new TinyAjaxBehavior();
			$tab -> add(TabSetValue::getBehavior('idejercicio', $dpol["ejercicio"]));
			$tab -> add(TabSetValue::getBehavior('idperiodo', $dpol["periodo"]));
			$tab -> add(TabSetValue::getBehavior('idtipopol', $dpol["tipopoliza"]));
			$tab -> add(TabSetValue::getBehavior('idnumeropol', $dpol["numeropoliza"]));
			$tab -> add(TabSetValue::getBehavior('idNumeroAnterior', $dpol["numeropoliza"]));
			$tab -> add(TabSetValue::getBehavior('idconceptopol', $dpol["concepto"]));
			$tab -> add(TabSetValue::getBehavior('idcargos', $dpol["cargos"]));
			$tab -> add(TabSetValue::getBehavior('idabonos', $dpol["abonos"]));
			$tab -> add(TabSetValue::getBehavior('idfechapol', getFechaMX($dpol["fecha"])));
			return $tab -> getString();
	}
}


function update_sumas_pol($idejercicio, $idperiodo, $idtipopol, $idpoliza, $form) {
$abonos	= 0;
$cargos	= 0;
$sql_c = "SELECT * FROM suma_mvtos_poliza
		WHERE ejercicio=$idejercicio
		AND periodo=$idperiodo
		AND numeropoliza=$idpoliza
		AND tipopoliza=$idtipopol
		/*AND tipomovimiento=1 */";


	$rs			= mysql_query($sql_c, cnnGeneral());
	while($rw 	= mysql_fetch_array($rs) ){
		if ($rw["tipomovimiento"] == 1){
			$cargos		= $rw["saldos"];
		} else {
			$abonos		= $rw["saldos"];
		}
	}
//-------------------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------------------------------------------------
			$tab = new TinyAjaxBehavior();
			$tab -> add(TabSetValue::getBehavior('idcargos', $cargos));
			$tab -> add(TabSetValue::getBehavior('idabonos', $abonos));
			return $tab -> getString();

}

function jsaDeletePoliza($idejercicio, $idperiodo, $idtipopol, $idpoliza){
	//Eliminar Poliza
	$SQLDP = "DELETE FROM contable_polizas WHERE  ejercicio=$idejercicio
				AND periodo=$idperiodo
				AND tipopoliza=$idtipopol
				AND numeropoliza=$idpoliza ";
	my_query($SQLDP);
	//Eliminar Movimientos
	$sqlSM = "SELECT * FROM contable_movimientos
	WHERE ejercicio=$idejercicio AND periodo=$idperiodo
	 AND numeropoliza=$idpoliza AND tipopoliza=$idtipopol";
	$rs = mysql_query($sqlSM, cnnGeneral());
	while ($rw = mysql_fetch_array($rs)) {
		$MTipoMvto		= $rw["tipomovimiento"];
		$MCuenta		= $rw["numerocuenta"];
		$MMonto			= $rw["importe"];
		$DCta			= getInfCatalogoTipo($MCuenta);
		$DTipo			= getInfCatalogoTipo($DCta["tipo"]);
		$MNaturaleza	= ($DTipo["naturaleza"] * $DTipo["naturaleza_del_sector"]);
		setRevertirMvto($MCuenta, $idperiodo, $idejercicio, $MNaturaleza, $MTipoMvto, $MMonto);

	}
	@mysql_free_result($rs);
	$sqlDMM = "DELETE FROM contable_movimientos
	WHERE ejercicio=$idejercicio AND periodo=$idperiodo AND numeropoliza=$idpoliza AND tipopoliza=$idtipopol";
	my_query($sqlDMM);
}
function jsGetUltimoFolio($idejercicio, $idperiodo, $idtipopol){
	return getFolioPoliza($idejercicio, $idperiodo, $idtipopol, false);
}
function jsSetUltimoFolio($idejercicio, $idperiodo, $idtipopol){
	//$x = getFolioPoliza($idejercicio, $idperiodo, $idtipopol, true);
}
$jxc ->exportFunction('jsaSavePoliza', array('idejercicio', 'idperiodo', 'idtipopol', 'idnumeropol', 'idfechapol', 'idconceptopol', 'idcargos', "idabonos"), "#imsg");
$jxc ->exportFunction('update_sumas_pol', array('idejercicio', 'idperiodo', 'idtipopol', 'idnumeropol', 'idfrmpol'));
$jxc ->exportFunction('jsaSearchPoliza', array('idejercicio', 'idperiodo',
											'idtipopol', 'idnumeropol',
											'idfrmpol'));
$jxc ->exportFunction('jsaEditarPoliza', array('idtipopol',
										'idnumeropol',
										'idfechapol',
										'idconceptopol',
										'idcargos',
										'idabonos',
										'idNumeroAnterior'), "#ipolmsg");
$jxc ->exportFunction('jsaDeletePoliza', array('idejercicio',
											'idperiodo',
											'idtipopol',
											'idnumeropol',
											'idfrmpol'), "#ipolmsg");

//$jxc ->exportFunction('verifyExistPoliza', array('idcproperties'));
$jxc ->exportFunction('jsGetUltimoFolio', array('idejercicio', 'idperiodo', 'idtipopol'), "#idnumeropol");
$jxc ->exportFunction('jsSetUltimoFolio', array('idejercicio', 'idperiodo', 'idtipopol') );
$jxc ->process();

$arrValues			= array (
					"ejercicio" => EJERCICIO_CONTABLE,
					"periodo" => EACP_PER_CONTABLE,
					"fecha" => false,
					"tipo" => 1,
					"numero" => 0,
					"onload" => "iniciar_poliza()"
					);
if ( isset($_GET["p"]) ){
	$DiPol			= explode(STD_LITERAL_DIVISOR, $_GET["p"], 5 );
	$arrValues		= array (
					"ejercicio" => $DiPol[0],
					"periodo" => $DiPol[1],
					"fecha" => ($DiPol[0] . "-" . $DiPol[1] . "-01"),
					"tipo" => $DiPol[2],
					"numero" => $DiPol[3],
					"onload" => "iniciar_poliza(); chkPolizaRegistrada();"
					);
}


$xHP->init($arrValues["onload"]);

?>
<form name="frmpol" method="post" action="" id="idfrmpol">
		<input type="hidden" name="ejercicio" id="idejercicio" value="<?php echo $arrValues["ejercicio"]; ?>" >
		<input type="hidden" name="periodo" id="idperiodo" value="<?php echo $arrValues["periodo"]; ?>" />
		<input type='hidden' id='idcproperties' value='0' name="cproperties" />
		<input type='hidden' id='idNumeroAnterior' value='<?php echo $arrValues["numero"] ?>' name="cNumeroAnterior" />
<fieldset>
<legend>[ POLIZA V 1.1.01]</legend>
	<table  >
		<tr>
			<th width="11%">Fecha</th>
			<td width="25%"><input type='text' name='fechapol' value='<?php echo SysDate_MX( $arrValues["fecha"] ); ?>' id="idfechapol"
							size="15" onchange="setFechaFmt();" /></td>
			<th>Tipo</th><td><select name="tipopol" id="idtipopol" onchange="" >
								<option value="1">Ingreso</option>
								<option value="2">Egreso</option>
								<option value="3">Diario</option>
								<option value="4">Orden</option>
							</select></td>
		</tr>
		<tr>
			<th width="12%">Numero</th>
			<td width="52%"><input type='text' name='numeropol' value='<?php echo $arrValues["numero"] ?>' id="idnumeropol"
							onkeypress="chrEventPoliza(event);"
							onfocus="jsObtenFolio();"
							onblur="jsGetExists();"
							size="5" class='mny' />
							<img style="" src="../images/common/search.png" align='middle' onclick="setShowFindPol()" />
			</td>
			<td>Concepto</td><td><input type='text' name='conceptopol' value='' size="60" id="idconceptopol"
			onblur="setCGuardar(1);" /></td>
		</tr>
	</table>
</fieldset>
<fieldset>
<legend>[ MOVIMIENTOS ]</legend>
	<iframe src="frm_poliza_movimientos.php" name="framepoliza"
			id="idframepoliza"   height="400px" 
			style="margin-left: 0px; margin-right: 0px; margin-top: 0px; margin-bottom: 0px; width:100%"
			 ></iframe>
</fieldset>
	<table >
		<tr>
			<td>TOTALES</td>
			<td>&nbsp;</td>
			<td width="30%"><input type="text" value="0" name="cargos" id="idcargos" size='10' class="imny" disabled /></td>
			<td width="30%"><input type="text" value="0" name="abonos" id="idabonos" size='10' class="imny" disabled /></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>

	</table>

			<table
 				       >
			<tbody>
				<tr>
					<td><img src="../images/common/tnuevo.png" id="tnuevo" onclick="cmdTNuevo(event);" /></td>
					<td><img src="../images/common/taceptar.png" id="taceptar" onclick="cmdTAceptar(event);" /></td>
					<td><img src="../images/common/teliminar.png" id="teliminar" onclick="cmdTEliminar(event);" /></td>
					<td><img src="../images/common/timprimir.png" id="timprimir" onclick="cmdTImprimir(event);" /></td>
				</tr>
			</tbody>
			</table>
	<p class="aviso" id="ipolmsg"></p>
</form>
</body>
<script   src="../jsrsClient.js" ></script>
<?php
$jxc ->drawJavaScript(false, true); 
?>
<script   >
var vEjercicio 	= parseInt("<?php echo $arrValues["ejercicio"]; ?>");
var chFecha 	= false;		//Si la Fecha es Modificado
var chTipo 		= false;		//Si el Tipo es Modificado
var chNumero 	= false;		//Si el Numero el Modificado
var FeCorta 	= "<?php echo date('d-m-Y') ?>";
var cEjerc 		= document.getElementById("idejercicio");
var cPer 		= document.getElementById("idperiodo");
var cTPol 		= document.getElementById("idtipopol");
var vNPol 		= document.getElementById("idnumeropol");
var divAvisos 	= document.getElementById("ipolmsg");
var onEdit 		= false;
var onAsClicked = false;
var mFRM 		= document.frmpol;
var vMes		= cPer;
var vDiv		= "<?php echo STD_LITERAL_DIVISOR; ?>";
//
var cPFecha 	= document.getElementById("idfechapol");

var onAlta 		= false;
var wFrmP 		= document.frmpol;
var jsrsFCont	= './jcontable.js.php';
/**
*	Notificacion de Cambios en la Poliza
*/
var vNotifEdit	= true;

function jsObtenFolio(){

	if ( (onEdit == false) && (onAlta == false) ){
		jsGetUltimoFolio();
	} else {
		wLog("No se Obtuvo el Ultimo Folio de Poliza");
	}
}
/*
* Funcion que busca si Existe la Poliza
*/
function jsGetExists(){
	//Buscar la Poliza sin notificar Cambios
	vNotifEdit	= false;
	wLog("");
	chkPolizaRegistrada();
}
function resizeMainWindow(){
	var mWidth	= 1024;
	var mHeight	= 680;
	window.resizeTo(mWidth, mHeight);
}
function setFrameSRC(){
	var mIframe = document.getElementById("idframepoliza");
	var vEjerc 	= document.getElementById("idejercicio").value;
	var vPer 	= document.getElementById("idperiodo").value;
	var vTPol 	= document.getElementById("idtipopol").value;
	var vNPol 	= document.getElementById("idnumeropol").value;
	mIframe.src = encodeURI("frm_poliza_movimientos.php?n=" + vNPol + "&p=" + vPer + "&e=" + vEjerc + "&t="  + vTPol);
	//divAvisos.innerHTML = mIframe.src;
}

function setCGuardar(vAhoraEdit){

	if(onEdit == false){
		var siGCambios = confirm("LA POLIZA HA SIDO CREADA.\n" +
								 "*** DESEA GUARDARLA? ***");
		if(siGCambios == true){

			goGuardarPoliza();
			//Cargar el FRAME
			setFrameSRC();
			//Guarda el Ultimo Folio
			jsSetUltimoFolio();
		} else if (siGCambios == false) {
			onEdit = false;
			document.getElementById("idnumeropol").focus();
			document.getElementById("idnumeropol").select();
		}
	} else if (onEdit == true && vNotifEdit == true) {
		var siSCambios = confirm("LA POLIZA HA SIDO MODIFICADA.\n DESEA GUARDARLA?" );
			if(siSCambios == true){
				jsaEditarPoliza();
				//La Poliza no esta a edicion
				onEdit = false;
				//Cargar el FRAME
				setFrameSRC();
			} else if (siSCambios == false) {
				//onEdit = false;
				document.getElementById("idnumeropol").focus();
				document.getElementById("idnumeropol").select();
			}
	}
	if (vAhoraEdit == 1){
		onEdit		= true;
		vNotifEdit 	= false;
	}
}
function iniciar_poliza(){
	document.getElementById("idnumeropol").value = <?php echo $arrValues["tipo"]; ?>;
	//alert(wFrmP.tipopol.value);
	onEdit = false;
	cPFecha.focus();
	cPFecha.select();
	//AddToolTip("idfechapol", 50101);
	resizeMainWindow();
}
function goGuardarPoliza(){
	if(onEdit == false){
		/**
		* Obtiene nuevamente el ultimo folio
		**/
		//jsGetUltimoFolio();
		divAvisos.innerHTML = "SE HA CONFIRMADO EL FOLIO DE POLIZA";
		jsaSavePoliza();
		divAvisos.innerHTML += "<br /> SE HA GUARDADO LA POLIZA";
		//Activa la Alta de la Poliza
		onAlta				= true;

	} else {

	}
}
function chkPolizaRegistrada(){
	var aDPoliza 		= new String(document.getElementById("idfechapol").value);
	var vDatePoliza 	= aDPoliza.split("-");
	//obtiene ejercicio
	var cvEjercicio 	= vDatePoliza[2];
	//obtiene mes
	var cvMes 			= vDatePoliza[1];
	//obtiene tipo
	var cvTipoPoliza 	= document.getElementById("idtipopol").value;
	//obtiene numero
	var cvNumeroPoliza 	= document.getElementById("idnumeropol").value;
	//consulta = ejercicio + mes + tipo_poliza + numero
	var cClavePoliza 	= cvEjercicio + vDiv + cvMes + vDiv + cvTipoPoliza + vDiv + cvNumeroPoliza;

	document.getElementById("idcproperties").value = cClavePoliza;
	wLog("la Poliza a Buscar es la " + cClavePoliza);
	vEjercicio			= cvEjercicio;
	vMes				= cvMes;
	wLog("Cambiando el Periodo a " + vEjercicio + "-" + vMes);
	verifyExistPoliza();

	divAvisos.innerHTML = "BUSCANDO POLIZA " + cClavePoliza + " ... ";
		//setTimeout("alExistirPoliza(" + cvEjercicio + "," + cvMes + ")", 1000);
	document.getElementById("idnumeropol").focus();
}
function alExistirPoliza(mEjercicio, tMes){
	//parseFloat(document.getElementById("idcproperties").value) > 0
	if (!mEjercicio){
		mEjercicio	= vEjercicio;
	}
	if(!tMes){
		tMes		= vMes;
	}
	if(onAlta == false && onEdit == true){
			var pKey		= document.getElementById("idcproperties").value;
			onEdit 			= true;
			cEjerc.value 	= mEjercicio;			//Asigna el Ejercicio
			cPer.value 		= tMes;					//Asigna el Mes
			jsaSearchPoliza();
			divAvisos.innerHTML = "LA POLIZA  EXISTE [" + pKey +
									"] EXISTE Y SE CARGA PARA EDITAR";
	setFrameSRC();

	} else {
		//Agrega la Muestra que la Poliza esta de Alta
		onAlta				= true;
		divAvisos.innerHTML = "**** VA A AGREGAR UNA POLIZA NUEVA ****";
	}

}
function setPolizaValues(msg){
	update_sumas_pol();
}
/* Funciones de Presion de Teclas en el Numero de Poliza*/
function chrEventPoliza(evt){
    evt=(evt) ? evt:event;
    var charCode = (evt.charCode) ? evt.charCode :
        ((evt.which) ? evt.which : evt.keyCode);
         switch(charCode){
			//Buscar Poliza Registrada
		    case 113: //F2
		    wLog("Buscando Poliza ");
				chkPolizaRegistrada();
		    break;
		    case 115: //F4
				setShowFindPol();
		    break;
         }
}
function setPolizaUpdateSaldos(){
	update_sumas_pol();
}
function setFechaFmt(){
	var mDate 	= document.getElementById("idfechapol").value;
	var sDate 	= mDate;
	var findStr = "/";
	var rF 		= new RegExp(findStr , "g");
	var rF2 	= /-/g 
	sDate 		= sDate.replace(rF, "");
	sDate 		= sDate.replace(rF2, "");

	var intLargo = sDate.length;
	//si el formato es ddmmaa 6 caracteres Formato a dd-mm-aa

	if(intLargo==8){
		//00 00 0000
		var intDia 	= sDate.substr(0,2);
		var intMes 	= sDate.substr(2,2);
		var intAnno = sDate.substr(4,4);

		document.getElementById("idfechapol").value = intDia + "-" + intMes + "-" + intAnno;

	} else if(intLargo==6) {
		var intDia 	= sDate.substr(0,2);
		var intMes 	= sDate.substr(2,2);
		var intAnno = sDate.substr(4,2);
		//var tmpDate = new Date(intAnno, intMes, intDia);
		if(parseInt(intAnno) > 70){
			intAnno = "19" + intAnno;
		} else {
			intAnno = "20" + intAnno;
		}

		document.getElementById("idfechapol").value = intDia + "-" + intMes + "-" + intAnno;

	} else {
		alert("   LA FECHA NO ES VALIDA     \n" +
			"CAPTURE EN EL FORMATO DDMMAAAA, \n" +
			"     DD-MM-AAA O DD/MM/AAAA     ");
			document.getElementById("idfechapol").value = FeCorta;
	}
}
function AddToolTip(IDElement, HelpIndex){
	//jsrsExecute(jsrsFCont, p_str_to_eval, 'JSAddToolTip', HelpIndex + "|" + IDElement);
}
function p_str_to_eval(toEval){
	eval(toEval);
}
function cmdTEliminar(evt){
	var siDel = confirm("EN REALIDAD DESEA ELIMINAR LA POLIZA\n" +
						"       Y SUS MOVIMIENTOS?           ");
		if (siDel){
			jsaDeletePoliza();
		}
	onAsClicked = true;
	imgAsClicked(evt.target.id);
	cmdTNuevo(evt);
}
function cmdTNuevo(evt){
	document.frmpol.reset();
	wLog( "Iniciando Nuevo Procedimiento en le Periodo " + vEjercicio + "-" + vMes );
	setFrameSRC();
	onAsClicked 		= true;
	imgAsClicked(evt.target.id);
	document.getElementById("idfechapol").select();
	document.getElementById("idfechapol").focus();
}
function cmdTAceptar(evt){
	setPolizaUpdateSaldos();
	vNotifEdit	= true;
	setCGuardar();
	onAsClicked = true;
	imgAsClicked(evt.target.id);
}
function cmdTImprimir(evt){
	setPolizaUpdateSaldos();
	vNotifEdit	= true;
	setCGuardar();
	onAsClicked = true;
	imgAsClicked(evt.target.id);
	execReport();
}
function imgAsClicked(sId){

	mImg = document.getElementById(sId);

	sPATHImg = "<?php echo vIMG_PATH; ?>";
	var HayCadena = sId;
	var PATHImg = mImg.src;

	if(onAsClicked == true){
		if(PATHImg.indexOf("_")!= -1){
			var sNImg 		= HayCadena.replace("_", HayCadena);
			mImg.src 		= sPATHImg + "/common/" + sNImg + ".png";
			onAsClicked 	= false;
		} else {
			mImg.src = sPATHImg + "/common/" + HayCadena + "_.png";
			onAsClicked = true;
			setTimeout("imgAsClicked('" + HayCadena + "')", 100);
		}
	}
}

function execReport(){

	var mMostrar	= "detalle";
	var mTipo		= document.getElementById("idtipopol").value;
	var mFolios		= document.getElementById("idnumeropol").value + "|" + document.getElementById("idnumeropol").value;
	var dInicial	= jsGetFechaUS(document.getElementById("idfechapol").value);
	var dFinal		= jsGetFechaUS(document.getElementById("idfechapol").value);
	var mFecha		= dInicial + "|" + dFinal;
	var mRPT		= "../rptcontables/rpt_auxiliar_de_polizas.php?f=" + mFecha + "&n=" + mFolios + "&v=" + mMostrar + "&t=" + mTipo + "&c=true";

		var mIDWin = Math.random();
		var windowName = "myWin" + mIDWin;
		var winLeft		= parseInt(screen.width * 0.10);
	    var winTop		= parseInt((screen.height * 0.10) + 100);
		var winHeight	= parseInt((screen.height - (screen.height * 0.10) ) - 100);
		var winWidth	= parseInt((screen.width - (screen.width * 0.10)));

        var windowFeatures = "width=" + winWidth + ",height=" + winHeight + ",status,scrollbars,resizable,left=" + winLeft + ",top=" + winTop ;
        newWindow = window.open(mRPT, windowName, windowFeatures);
		newWindow.focus();
}
function jsGetFechaUS(mFechaMX){
	var vFechaUS = mFechaMX.split("-");
	return vFechaUS[2] + "-" + vFechaUS[1] + "-" + vFechaUS[0];
}
/**
		Funcion que Activa/Desactiva los Elementos del Form Poliza
*/
function IOComponents(){

}
function setShowFindPol(){
	var mFPol	= "../frmcontabilidad/buscar_polizas.frm.php";
        newWindow = window.open(mFPol, "FindPoliza", "status,scrollbars,resizable=false");
		newWindow.focus();
}
//Busca una Poliza segun el IDC properties
function verifyExistPoliza(){
	var CID		= document.getElementById("idcproperties").value;
	wLog("Enviando " + CID + " a Busqueda");
	jsrsExecute(jsrsFCont, jsConfirmExistPoliza, 'jrsVerifyExistPoliza', CID);
}
function jsConfirmExistPoliza(nums){
	wLog("Se Buscaron " + nums + " Polizas");
	if ( parseInt(nums) == 0 ){
		//si no existe la Poliza se va a agrega
		onAlta											= true;
		onEdit											= false;
		vEjercicio 										= parseInt("<?php echo EJERCICIO_CONTABLE; ?>");
		vMes											= parseInt("<?php echo EACP_PER_CONTABLE; ?>");
		document.getElementById("idejercicio").value	= vEjercicio;
		document.getElementById("idperiodo").value		= vMes;
		wLog("Se Actualizo el Periodo a " + vEjercicio + "-" + vMes);
		document.getElementById("idcproperties").value 	= 0;
	} else {
		onEdit	= true;
		onAlta	= false;
		document.getElementById("idcproperties").value 	= nums;
			wLog("Cargando Poliza para Edicion ");
		alExistirPoliza();
	}
}
function wLog(strTxt){
	if( window.console ) { window.console.log( strTxt ); }
}
function jsExportarPoliza(){
	var vEjerc 	= document.getElementById("idejercicio").value;
	var vPer 	= document.getElementById("idperiodo").value;
	var vTPol 	= document.getElementById("idtipopol").value;
	var vNPol 	= document.getElementById("idnumeropol").value;
}
</script>
</html>
