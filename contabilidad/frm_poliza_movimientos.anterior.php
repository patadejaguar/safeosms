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

	$xHP				= new cHPage("", HP_FORM);
	
$jxc 			= new TinyAjax();
//Mvtos
$poliza 		= parametro("n", false, MQL_INT);
$periodo		= parametro("p", false, MQL_INT);
$tipo 			= parametro("t", false, MQL_INT);
$ejercicio 		= parametro("e", false, MQL_INT);
$keypoliza 		= "";
$str_action 	= "frm_poliza_movimientos.php?n=$poliza&p=$periodo&e=$ejercicio&t=$tipo";
$m_mvto 		= 1;
$me_mvto 		= 2;
$d_concepto 	= "";
$k_mvto			= "";

$fecha 			= fechasys();

function save_mvto($keymvto, $cuenta, $cargo, $abono, $referencia, $concepto, $mfecha, $form){
	if( isset($cuenta) and ($cargo>0 or $abono >0)){
		$dkeys 		= explode("@", $keymvto);
		$nejercicio = $dkeys[1];
		$nperiodo 	= $dkeys[2];
		$npoliza 	= $dkeys[3];
		$ntipo 		= $dkeys[4];
		$nmvto 		= $dkeys[5];
		//Valores de los proximos Movimientos
		$ProxMCargo	= 0;
		$ProxMAbono	= 0;
	
		$tmvto 		= 1;
		$monto 		= 0;
		$fecha 		= getFechaUS($mfecha);		//Conservar
		$diario 	= 999;
		$cuenta 	= getCuentaCompleta($cuenta);
		/**
		 * verificar la Fecha
		 */
		$valFecha = validarFechaUS($fecha);
		if($valFecha = false){
			$fecha = $mfecha;
			$valFecha = validarFechaUS($mfecha);
		}
		if($valFecha = false){
			$fecha = fechasys();
		}
	/**
	 * Agrega el Movimiento
	 *
	 */
		if ($cargo > 0){
			$tmvto 			= TM_CARGO;
			$monto 			= $cargo;
	
		} else {
			$tmvto 			= TM_ABONO;
			$monto 			= $abono;
		}
		$ProxMAbono		= $cargo;
		$ProxMCargo		= $abono;
		//-------------------------------------------------------------------------------------------------------
		$sqldcta = "SELECT
		`contable_catalogo`.`numero`,
		(`contable_catalogotipos`.`naturaleza` * `contable_catalogotipos`.`naturaleza_del_sector`) AS 'factor'
	FROM
		`contable_catalogotipos` `contable_catalogotipos`
			INNER JOIN `contable_catalogo` `contable_catalogo`
			ON `contable_catalogotipos`.
			`idcontable_catalogotipos` = `contable_catalogo`.
			`tipo`
	WHERE `contable_catalogo`.`numero`= $cuenta LIMIT 0,1";
	
		$dcuenta = getFilas($sqldcta);
		//var_dump($dcuenta);
		$naturaleza = $dcuenta["factor"];
		//variables de mensajes
		$afect1 = "";
		$afect2 = "";
		//1=CARGO
		//2=ABONO
		//3=SALDO
		setAfectarSaldo($cuenta, $nperiodo, $nejercicio, $naturaleza, $tmvto, $monto);
		$referencia 	= trim($referencia);
		$concepto 		= trim($concepto);
		//-------------------------------------------------------------------------------------------------------
	$sqli_mvto = "INSERT INTO contable_movimientos(ejercicio, periodo, tipopoliza, numeropoliza, numeromovimiento,
	numerocuenta, tipomovimiento, referencia, importe, diario, moneda, concepto, fecha, cargo, abono)
	    VALUES($nejercicio, $nperiodo,
	    $ntipo, $npoliza,
	    $nmvto, $cuenta,
	    '$tmvto', '$referencia',
	    $monto, $diario, 1,
	    '$concepto', '$fecha',
	    $cargo, $abono)";
		my_query($sqli_mvto);
	
		$notes = "";
	
		$nmvto = $nmvto + 1;
		$nk_mvto = "i@$nejercicio@$nperiodo@$npoliza@$ntipo@$nmvto";
	
				$tab = new TinyAjaxBehavior();
				$tab -> add(TabSetValue::getBehavior('idkeymvto', $nk_mvto));
				$tab -> add(TabSetValue::getBehavior('idfecha', $fecha));
				$tab -> add(TabSetValue::getBehavior('idd_concepto', $concepto));
				$tab -> add(TabSetValue::getBehavior('idcuenta', $cuenta));
				if( PREDICT_MOVIMIENTO == true){
					$tab -> add(TabSetValue::getBehavior('idcargo', $ProxMCargo));
					$tab -> add(TabSetValue::getBehavior('idabono', $ProxMAbono));
				}
				if($referencia!=""){
					$tab -> add(TabSetValue::getBehavior('idreferencia', $referencia));
				}
				if($concepto!=""){
					$tab -> add(TabSetValue::getBehavior('idconcepto', $concepto));
				}
				$tab -> add(TabSetValue::getBehavior('icontarrows', $nmvto));
	
				return $tab -> getString();
	} // en if
}
function MostrarMvtos($vejercicio, $vperiodo, $vpoliza, $vtipo){
/**
 * Muestra la Actualizacion de Registros
 */

$sqlpm = "SELECT
	`contable_movimientos`.`numeromovimiento` AS 'idm',
	`contable_movimientos`.`numerocuenta` AS 'cuenta',
	`contable_catalogo`.`nombre` AS 'nombre_de_la_cuenta',
	`contable_movimientos`.`tipomovimiento`,
	`contable_movimientos`.`referencia`,
	`contable_movimientos`.`importe`,
	`contable_movimientos`.`diario`,
	`contable_movimientos`.`concepto`
FROM
	`contable_catalogo` `contable_catalogo`
		INNER JOIN `contable_movimientos` `contable_movimientos`
		ON `contable_catalogo`.`numero` = `contable_movimientos`.`numerocuenta`
WHERE
	(`contable_movimientos`.`ejercicio` =$vejercicio) AND
	(`contable_movimientos`.`periodo` =$vperiodo) AND
	(`contable_movimientos`.`tipopoliza` =$vtipo) AND
	(`contable_movimientos`.`numeropoliza` =$vpoliza)
ORDER BY
	`contable_movimientos`.`numeromovimiento` ";

$rsm 			=  getRecordset($sqlpm);

$tds			= "";

while ($rw = mysql_fetch_array($rsm)) {

	$tm 			= "";
	
	$nombrecuenta 	= htmlentities($rw["nombre_de_la_cuenta"]);
	$monto 			= cmoney($rw["importe"]);
	if($rw["tipomovimiento"] == "1"){ //1=Cargo -1=Abono
		$tm = "<td class=\"mny\">$monto</td>
		<td class=\"mny\"></td>";
	} else {
		$tm = "<td class=\"mny\"></td>
		<td class=\"imny\">$monto</td>";
	}
	$concepto = substr($rw[7],0,20);
//	'i@$vejercicio@$vperiodo@$vpoliza@$vtipo@$rw[0]'
	$tds = $tds . "<tr id=\"tr@$vejercicio@$vperiodo@$vpoliza@$vtipo@$rw[0]\">
					<th ><input type=\"button\" id=\"cmd@$vejercicio@$vperiodo@$vpoliza@$vtipo@$rw[0]\" value=\"$rw[0]\" class=\"rwMvtoG\" onclick=\"menu_x_id(event);\" /></th>
					<td >" . substr($rw["cuenta"], 0, 20) . "</td>
					<td  class='estrecho'>$nombrecuenta</td>
					$tm
					<td >$rw[4]</td>
					<td >$concepto
					<!-- </td>
					<td>$rw[6] --></td>
				</tr>";
}
/** Asigna los Valores al Formulario */
	$tbl = $tds;

	return $tbl;
}
function listar_cuentas($idcta) {
	//$idcta = substr($idcta, 1, -1);
	$sucess		= true;
	$tds 		= "";
	$i 			= 1;
	$xCta		= new cCuentaContableEsquema($idcta);
	
	$sql = "SELECT numero, nombre FROM contable_catalogo WHERE numero LIKE '" . $xCta->CUENTARAW . "%' AND afectable=1  ORDER BY numero LIMIT 0,10";
	if(MODO_DEBUG == true){  setLog($sql); }
	if ( (trim($idcta) == "") OR ( (int)$idcta == 0 ) ){
		$sucess		= false;
	}
	if($sucess == true){
		$rs = getRecordset($sql);

		while ($row = mysql_fetch_array($rs)) {
			$ctaformateada  	= $row[0];
			$nombrecuenta 		= substr(htmlentities($row[1]),0, 20);
			if($i ==2){	$i = 1;	} else { $i++; }
			//
			$tds .= " \n
			<option value=\"$row[0]\" >$ctaformateada - $nombrecuenta</option>";

		}
	} else {
			$tds .= " \n<option >NO HAY CUENTAS PARA ($idcta)</option>";
	}
	if($sucess	= true){
	return "<select name=\"underCuenta\" id=\"idUnderCuenta\" size=\"10\"
			onclick=\"setNumeroCuenta(this.value);\"
			onblur=\"setNoSearchCatalog();\"
			onkeyup=\"setActionByKey(event);\" >
				$tds
			</select>";
	}
}
function jsTAEliminarMvto($keyMvto){
	//obtiene los datos del movimiento
	$dkeys 			= explode("@", $keyMvto);
	$nejercicio 	= $dkeys[1];
	$nperiodo 		= $dkeys[2];
	$npoliza 		= $dkeys[3];
	$ntipo 			= $dkeys[4];
	$nmvto 			= $dkeys[5];
	$SQLDMvtos = "SELECT * FROM contable_movimientos
	WHERE
	ejercicio=$nejercicio
	AND periodo=$nperiodo
	AND tipopoliza=$ntipo
	AND numeropoliza=$npoliza
	AND numeromovimiento=$nmvto";
	$dw = getFilas($SQLDMvtos);
	//invierte el movimiento
	//invierte el saldo
		$MTipoMvto		= $dw["tipomovimiento"];
		$MCuenta		= $dw["numerocuenta"];
		$MMonto			= $dw["importe"];
		$DCta			= getInfCatalogoTipo($MCuenta);
		$DTipo			= getInfCatalogoTipo($DCta["tipo"]);
		$MNaturaleza	= ($DTipo["naturaleza"] * $DTipo["naturaleza_del_sector"]);
		setRevertirMvto($MCuenta, $nperiodo, $nejercicio, $MNaturaleza, $MTipoMvto, $MMonto);

	$sqlDMM = "DELETE FROM contable_movimientos
	WHERE ejercicio=$nejercicio AND periodo=$nperiodo
	AND numeropoliza=$npoliza
	AND tipopoliza=$ntipo
	AND numeromovimiento=$nmvto";
	my_query($sqlDMM);

}
function setListaCuentas($cuenta){	return listar_cuentas($cuenta); }
function UpdateEditMvto($keyMvto, $cuenta, $cargo, $abono, $referencia, $concepto, $diario){
	$dMvto 			= explode("@", $keyMvto);
	$ejercicio 		= $dMvto[1];
	$periodo 		= $dMvto[2];
	$poliza  		= $dMvto[3];
	$tipopoliza 	= $dMvto[4];
	$mvto 			= $dMvto[5];
	$cuenta 		= getCuentaCompleta($cuenta);
	//purgar Mvto
	if($cargo>0){
		$abono = 0;
	}
	if ($cargo>0){
		$tmvto = 1;
		$monto = $cargo;
	} else {
		$tmvto = -1;
		$monto = $abono;
	}

	$sqlDatosMvtoAnterior = "SELECT
	`contable_movimientos`.*
FROM
	`contable_movimientos` `contable_movimientos`
WHERE
	`contable_movimientos`.`ejercicio` = $ejercicio
	AND `contable_movimientos`.`periodo` = $periodo
	AND `contable_movimientos`.`tipopoliza` = $tipopoliza
	AND `contable_movimientos`.`numeropoliza` = $poliza
	AND `contable_movimientos`.`numeromovimiento` =$mvto";
	$DMAnterior 	= getFilas($sqlDatosMvtoAnterior);
	$AntCuenta 		= $DMAnterior["numerocuenta"];
	$AntTMvto 		= $DMAnterior["tipomovimiento"];
	$AntMonto 		= $DMAnterior["importe"];
	$AntFecha 		= $DMAnterior["fecha"];
	//----------------------- Revertir Afectacion ------------------
	$sqldcta = "SELECT
	`contable_catalogo`.`numero`,
	(`contable_catalogotipos`.`naturaleza` * `contable_catalogotipos`.`naturaleza_del_sector`) AS 'factor'
FROM
	`contable_catalogotipos` `contable_catalogotipos`
		INNER JOIN `contable_catalogo` `contable_catalogo`
		ON `contable_catalogotipos`.
		`idcontable_catalogotipos` = `contable_catalogo`.
		`tipo`
WHERE `contable_catalogo`.`numero`=$AntCuenta";
	$dcuenta 			= getFilas($sqldcta);
	$AntNaturaleza 		= $dcuenta["factor"];
	setRevertirMvto($AntCuenta, $periodo, $ejercicio, $AntNaturaleza, $AntTMvto, $AntMonto);
	//----------------------- Eliminar Cuenta ----------------------
	$sqlDelMvtoAnterior = "DELETE
		FROM
			`contable_movimientos`
		WHERE
			`contable_movimientos`.`ejercicio` = $ejercicio
			AND `contable_movimientos`.`periodo` = $periodo
			AND `contable_movimientos`.`tipopoliza` = $tipopoliza
			AND `contable_movimientos`.`numeropoliza` = $poliza
			AND `contable_movimientos`.`numeromovimiento` =$mvto";
	my_query($sqlDelMvtoAnterior);
	//----------------------- Insertar Cuenta ----------------------
	$NSqlcta = "SELECT
	`contable_catalogo`.`numero`,
	(`contable_catalogotipos`.`naturaleza` * `contable_catalogotipos`.`naturaleza_del_sector`) AS 'factor'
FROM
	`contable_catalogotipos` `contable_catalogotipos`
		INNER JOIN `contable_catalogo` `contable_catalogo`
		ON `contable_catalogotipos`.
		`idcontable_catalogotipos` = `contable_catalogo`.
		`tipo`
WHERE `contable_catalogo`.`numero`=$cuenta";
	$dNcuenta = getFilas($NSqlcta);
	$naturaleza = $dNcuenta["factor"];

$sqli_mvto = "INSERT INTO contable_movimientos(ejercicio, periodo, tipopoliza, numeropoliza, numeromovimiento,
numerocuenta, tipomovimiento, referencia, importe, diario, moneda, concepto, fecha, cargo, abono)
    VALUES($ejercicio, $periodo,
    $tipopoliza, $poliza,
    $mvto, $cuenta,
    '$tmvto', '$referencia',
    $monto, $diario, 1,
    '$concepto', '$AntFecha',
    $cargo, $abono)";
	my_query($sqli_mvto);
	setAfectarSaldo($cuenta, $periodo, $ejercicio, $naturaleza, $tmvto, $monto);
}
/**
 * Funcion que Checa si es afectable
 */
	$jxc ->exportFunction('nombre_cuenta', array('idcuenta', "frmpolizamvtos"));
	$jxc ->exportFunction('save_mvto', array('idkeymvto', "idcuenta", "idcargo", "idabono", "idreferencia", "idconcepto" , "idfecha", "frmpolizamvtos"));
	$jxc ->exportFunction('UpdateEditMvto', array('idkeymvtoU', "idCuentaU", "idCargoU",
												"idAbonoU", "idReferenciaU", "idConceptoU",
												"idDiarioU", "frmpolizamvtos"), "#idSwCatalogo");
	$jxc ->exportFunction('listar_cuentas', array('idcuenta'), '#idSwCatalogo');
	$jxc ->exportFunction('setListaCuentas', array('idcuentaU'), '#idSwCatalogo');
	$jxc ->exportFunction('jsTAEliminarMvto', array('idkeymvtoToDelete'));
	$jxc ->process();

if($poliza != false)	{
	$sqlpol 	= "SELECT * FROM contable_polizas WHERE ejercicio=$ejercicio
			AND periodo=$periodo AND tipopoliza=$tipo AND numeropoliza=$poliza";

	$dpol 		= getFilas($sqlpol);
	$fecha 		= $dpol["fecha"];
	$d_concepto = $dpol["concepto"];

	$sql_umvto 	= "SELECT MAX(numeromovimiento) AS 'mmvto' FROM contable_movimientos
			WHERE ejercicio=$ejercicio AND periodo=$periodo
			AND numeropoliza=$poliza AND tipopoliza=$tipo";
	$m_mvto 	= mifila($sql_umvto, "mmvto") + 1;
	$k_mvto 	= "i@$ejercicio@$periodo@$poliza@$tipo@$m_mvto";
}

$xHP->init("InitMvtos()");

?>
<form name="frmpolizamvtos" id="idfrmpolizamvtos" method="post">
<?php
$itype = "hidden";
?>
<input type='<?php echo $itype; ?>' id='idkeymvtoToDelete' />
<table  style="font-weight: normal;">
<tbody id="TBMovimientos">
  <tr>
    <th width='5%'>...</th>
    <th width='10%'>Cuenta</th>
    <th width='35%'>Nombre de la Cuenta</th>
    <th width='10%'>Cargo</th>
    <th width='10%'>Abono</th>
    <th width='10%'>Referencia</th>
    <th width='20%'>Concepto</th>
  </tr>
<?php
if( isset($poliza) ){
	echo MostrarMvtos($ejercicio, $periodo, $poliza, $tipo);
}
?>
	<tr id="trNuevoMvto">
		<th id="tdContar"><input type="button" id="icmd" class="rwMvtoG" disabled onfocus="" /></th>
		<td ><input type='text' id='idcuenta'
			name='cuenta' value='   ' onchange=""
			onkeyup='setCharAction(event); clearMenu();'
			onblur="setBlurGo(this);"
			onfocus="jsFocusCuenta(this)"
			size="16" maxlength="20" /></td>
		<td id="tdNombreCuenta"></td>
		<td ><input type='text' id='idcargo' 		name='cargo' 		value='0'	size="12"	maxlength="16" 	onblur='check_cargo();' class='mny' onfocus="validar_cuenta();" /></td>
		<td ><input type='text' id='idabono' 		name='abono' 		value='0'	size="12"	maxlength="16"	onblur='check_abono();' class='mny' onfocus="validar_cuenta();" /></td>
		<td ><input type='text' id='idreferencia' 	name='referencia' 	value='   '	size="20"	maxlength="40"	/></td>
		<td ><input type='text' id='idconcepto' 	name='concepto' 	value='   '	size="30"	maxlength="80"	onblur="addMvto();" />
		<!-- </td>
		<td > -->
			<input type='<?php echo $itype; ?>' id='idkeymvto' 		value='<?php echo $k_mvto; ?>' 			name='keymvto'>
			<input type='<?php echo $itype; ?>' id='idd_concepto' 	value='<?php echo $d_concepto; ?>' 		name='d_concepto'>
			<input type='<?php echo $itype; ?>' id='idfecha' 		value='<?php echo $fecha; ?>' 			name="fecha" />
			<input type='<?php echo $itype; ?>' id='iddiario' 		value='999'								name="diario" />
			<input type='<?php echo $itype; ?>' id='icontarrows' 	value='<?php echo $m_mvto; ?>' 			size="3" />
		</td>
	</tr>
</tbody>
</table>
<div style="position:absolute; left: 26px; top: 101px; width: 400px; height: 94px; visibility:hidden;" id="idSwCatalogo" >
</div>
<div style="position:absolute; left: 26px; top: 101px; width: 298px; height: 94px; visibility:hidden;" id="idMnuAction">
</div>
</form>
</body>
<script src='../jsrsClient.js'></script>
<?php $jxc ->drawJavaScript(false, true); ?>
<script>
var vEjercicio 			= parseInt("<?php echo EJERCICIO_CONTABLE; ?>");
var onEditM 			= false;											//Variable que especifica si estan Editando el Movimiento
var onAltaC 			= false;											//Variable que indica si estan dando de alta el Movimiento
var wFrm 				= document.getElementById("idfrmpolizamvtos");		//Vriable FORM de trabajo
var dSwCatalogo 		= document.getElementById("idSwCatalogo");
var rwContados 			= wFrm.icontarrows;									//Variable Numerode ROWS del FORM
var dMnuAction 			= document.getElementById("idMnuAction");
var mnuTop 				= 0;
var mnuLeft 			= 0;
var iLargoCta 			= entero("<?php echo LARGO_MAX; ?>");
var rRowOnEdit;
var onSearchInCatalog 	= false;
var ctrlCuenta 			= "idcuenta";
var jsrsFile			= './jcontable.js.php';
var sFakeCuenta			= "<?php echo ZERO_EXO; ?>";
var vDLit				= "<?php echo STD_LITERAL_DIVISOR; ?>";

function InitMvtos(){
	wFrm.cuenta.focus();
	wFrm.cuenta.select();
	//document.body.addEventListener("onclick", clearMenu, false);
	setLog("Movimientos iniciados");
}
function jsFocusCuenta(obj){ setLog("Cuenta in focus"); obj.select();}
function check_cargo() {
	if( flotante(wFrm.cargo.value)>0 ) { wFrm.abono.value = 0; wFrm.referencia.focus(); wFrm.referencia.select();	}
}
function check_abono() {
	if (flotante(wFrm.abono.value)>0 ) { wFrm.cargo.value = 0; wFrm.referencia.focus(); wFrm.referencia.select(); }
}

/* Funcion que devuelve el numero de cuenta buscado*/
function setNumeroCuenta(valueid) {
		setLog("setNumeroCuenta " + valueid);
		if( !ctrlCuenta || ctrlCuenta == "" || ctrlCuenta == null ){
			ctrlCuenta = "idcuenta";
		}
		var mcCuenta = document.getElementById(ctrlCuenta);
			mcCuenta.value = valueid;
			if(ctrlCuenta!="idcuenta"){
				nombre_cuenta_U();
			} else {
				nombre_cuenta();
			}
			mcCuenta.focus();
			mcCuenta.select();
		//resetea el Catalogo
			purgeCatalog();
		//Niega que se este buscando en  el catalogo
		onSearchInCatalog = false;
}
/**
* Funcion del Numero de Cuenta a Agregar el control idcuenta
* donde se captura en numero de cuenta del Movimiento
*/
function setCharAction(evt){
    evt=(evt) ? evt:event;
    var charCode = (evt.charCode) ? evt.charCode :

        ((evt.which) ? evt.which : evt.keyCode);
        ctrlCuenta = "idcuenta";
		//forza el Numero de Cuenta
		if ( document.getElementById(ctrlCuenta).value == undefined ){
			document.getElementById(ctrlCuenta).value = sFakeCuenta;
		}
        switch(charCode){
        	//Buscar en el catalogo
        	case 113:		//F2
        		if ( (document.getElementById(ctrlCuenta).value!= sFakeCuenta) && (document.getElementById(ctrlCuenta).value != "   ") && (document.getElementById(ctrlCuenta).value != "")  ){
					mostrar_catalogo();
				} else {
					purgeCatalog();
				}

        		break;
			//
        	case 40:		//Flecha Abajo
        		setBlurGo();
        		break;
        	case 121:	// F10
    			//Salvar Movimiento Editado
				//goUpdate();
        	break;
        	default:
        		if(onSearchInCatalog == true){
			        mostrar_catalogo();
        		} else {
    	 		//desaparece el catalogo siempre
					purgeCatalog();
		     		onSearchInCatalog = false;
        		}
        	 break;
        }
}
/* Funcion de Evaluacion de la Cuenta */
function str_to_eval(toEval){
	setLog("eval:  " + toEval);
	eval(toEval);
}
/* Busca si la Cuenta es valida*/
function validar_cuenta(){
	var cta = wFrm.cuenta.value;

	var sFind = /<?php echo CDIVISOR; ?>/g;

		cta = cta.replace(sFind, "");
		wFrm.cuenta.value = cta.substr(0, iLargoCta);

	if (cta.length < iLargoCta) {
		var cta = cta + "<?php echo ZERO_EXO; ?>";
		wFrm.cuenta.value = cta.substr(0, iLargoCta);
		nombre_cuenta();
		chkAfectable();
	} else if (cta.length == iLargoCta) {
		nombre_cuenta();
		chkAfectable();
	} else {
		wFrm.cuenta.value = cta.substr(0, iLargoCta);
		nombre_cuenta();
		chkAfectable();
	}
}
/* funcion que checa que la cuenta sea afectable */
function chkAfectable(){
	var mCuenta = document.getElementById("idcuenta").value;
	jsrsExecute(jsrsFile, str_to_eval, 'JS_chkCuenta', mCuenta);
}
/** Funcion que busca generar un numero de cuenta */
function CuentaNueva(sNumero){
	var urlCN = "./frm_catalogo_cuentas.php?c=";
	var wCatalogo = window.open(urlCN + sNumero, "wCatalogo", "width=600,height=400");
	wCatalogo.focus();
}
//Muestra el Catalogo
function mostrar_catalogo(ctrlSet){
	if(!ctrlSet){ var iBtn = "icmd"; } else { var iBtn = ctrlSet; }
	var mBtn = document.getElementById(iBtn);
	onSearchInCatalog = true;
	//pocisionar el Catalogo
	oTop 	= entero(mBtn.offsetParent.offsetTop) + 20;
	oLeft	= entero(mBtn.offsetLeft);
	dSwCatalogo.style.top = oTop + "px";
	dSwCatalogo.style.left = oLeft + "px";

	listar_cuentas();
	dSwCatalogo.style.visibility = "visible";
	isCatalogAuto();
}

function addMvto(){
	//Guarda el Movimiento
	//Clave del Mvto
	var mKeyM 		= document.getElementById("idkeymvto").value;
	var dKey 		= mKeyM.split(vDLit);
	var mNKeyM 		= dKey[1] + vDLit + dKey[2] + vDLit + dKey[3] + vDLit + dKey[4] + vDLit + dKey[5];
	//Numero de Cuenta
	var mCuenta 	= document.getElementById("idcuenta").value;
	//nombre de la cuenta
	//var mNCuenta = document.getElementById("idncuenta").value;
	var mNCuenta 	= document.getElementById("tdNombreCuenta").innerHTML;
	//
	//cargo
	var mCargo 		= document.getElementById("idcargo").value;
	//abono
	var mAbono 		= document.getElementById("idabono").value;
	//referencia
	var mReferencia = document.getElementById("idreferencia").value;
	//concepto
	var mConcepto 	= document.getElementById("idconcepto").value;
	//diario
	var mDiario 	= "NINGUNO";		//document.getElementById("iddiario").value;

	if((mCuenta!='' || mCuenta!=0) && (mCargo > 0 || mAbono > 0)) {
		//Salvar el Movimiento
		save_mvto();
		//Agrega el Row
		var mTRMvtos 	= document.getElementById("TBMovimientos");
		var nTr 		= mTRMvtos.insertRow((mTRMvtos.rows.length -1));

		var stMvto	 = "<th> <input type=\"button\" id=\"cmd@" + mNKeyM +
						"\" class=\"rwMvtoG\" onclick=\"menu_x_id(event);\"  value =\"" + rwContados.value + "\"/></th>" +
						"<td> " + mCuenta + "</td>" +
						"<td class=\"strech\">" + mNCuenta + "</td>" +
						"<td class=\"imny\">" + getFPesos(mCargo) + "</td>" +
						"<td class=\"imny\">" + getFPesos(mAbono) + "</td>" +
						"<td>" + mReferencia + "</td>" +
						"<td>" + mConcepto +  "</td>" +
						"";
						//<td>" + mDiario + "</td>
		nTr.innerHTML		= stMvto;

		if(mConcepto=="" ||  mConcepto=="     "){
			wFrm.concepto.value = wFrm.d_concepto.value;
		}
		rwContados.value = parseInt(rwContados.value) + 1;
	}
		setTimeout("supFocus()", 400);

}
function supFocus(){
		parent.setPolizaValues();
		document.getElementById("idcuenta").focus();
		document.getElementById("idcuenta").select();
}
function nombre_cuenta(){
	var mCuenta 		= document.getElementById("idcuenta").value;
	jsrsExecute(jsrsFile, ret_nombre_cuenta, 'JSgetNombreCuenta', mCuenta);
}
function ret_nombre_cuenta(sCuenta){

	document.getElementById("tdNombreCuenta").innerHTML = sCuenta;
}
function menu_x_id(evt){
	idT 					= evt.target.id;
	cmdBtn 					= document.getElementById(idT);
	//posicionar el Menu
	oTop 					= parseInt(cmdBtn.offsetParent.offsetTop) + 20;
	oLeft 					= cmdBtn.offsetLeft;
	dMnuAction.style.top 	= oTop + "px";
	dMnuAction.style.left 	= oLeft + "px";
	jsrsExecute(jsrsFile, ret_menu_x_id, 'mnuActionMvto', idT);

}
function ret_menu_x_id(id){
	dMnuAction.innerHTML 		= id;
	dMnuAction.style.visibility = "visible";
}
function ActionEliminar(item_){
	clearMenu();
	//alert("eliminar " + item_);
	document.getElementById("idkeymvtoToDelete").value = item_;
	sDel 			= confirm("CONFIRMA ELIMINAR EL MVTO CON CLAVE " + item_);
	if(sDel){
		jsTAEliminarMvto();
		rRows 		= item_.split(vDLit);
		//0 type
		//1 Ejercicio
		//2 periodo
		//3 poliza
		//4 tipo
		//5 mvto
		rRowDel = "cmd@" + rRows[1] + vDLit + rRows[2] + vDLit  + rRows[3] + vDLit + rRows[4] + vDLit + rRows[5];
			var xTD 		= document.getElementById(rRowDel).parentNode;
			var xTR 		= xTD.parentNode;
			xTR.innerHTML 	= "";
		//Actualizar sumas de la Poliza
		parent.setPolizaUpdateSaldos();
	}
}
function ActionEditar(item_){
	clearMenu();
	if(rRowOnEdit){
    	var mUKey 			= document.getElementById("idkeymvtoU").value;
    	jsrsExecute(jsrsFile, ret_mvto_no_edit, 'JSRetMvtoSalvado', mUKey);
	}
	rRowOnEdit 				= item_;
	jsrsExecute(jsrsFile, ret_mvto_a_edit, 'JSsetMvtoEditable', item_);
}

function ret_mvto_a_edit(sHTML){

	rRows = rRowOnEdit.split(vDLit);
	//0 type
	//1 Ejercicio
	//2 periodo
	//3 poliza
	//4 tipo
	//5 mvto
	rRowOnEdit 				= "cmd@" + rRows[1] + vDLit + rRows[2] + vDLit  + rRows[3] +vDLit + rRows[4] + vDLit + rRows[5];
		var xTD 			= document.getElementById(rRowOnEdit).parentNode;
		var xTR 			= xTD.parentNode;
		xTR.innerHTML 		= sHTML;
		document.getElementById("idCuentaU").focus();
		document.getElementById("idCuentaU").select();

}
function ActionCancelar(){
	clearMenu();
	document.getElementById("idcuenta").focus();
	document.getElementById("idcuenta").select();	
}
/**
*	Hace el Menu Action(eliminar, editar) invisible
*/
function clearMenu(){
		dMnuAction.style.visibility 	= 'hidden';
		dMnuAction.style.top 			= "0px";
		dMnuAction.style.left 			= "0px";
		dMnuAction.style.height 		= "0px";
		/* dMnuAction.style.width = "0px"; */
		dMnuAction.innerHTML 			= "";
    	//
}

/**
*funcion que agrega los Datos Heredados de la Poliza a los Mvtos
*/
//Funciones para el Update Poliza
function check_cargoU() {
	CargoU_ = document.getElementById("idCargoU").value;
	if(parseFloat(CargoU_)>0)	{
		document.getElementById("idAbonoU").value = 0;
		document.getElementById("idReferenciaU").focus();
		document.getElementById("idReferenciaU").select();
	}
}
function check_abonoU() {
	AbonoU_ = document.getElementById("idAbonoU").value;
	if(parseFloat(AbonoU_)>0)	{
		document.getElementById("idCargoU").value = 0;
		document.getElementById("idReferenciaU").focus();
		document.getElementById("idReferenciaU").select();
	}
}
function nombre_cuenta_U(){
	var mUCuenta = document.getElementById("idCuentaU").value;
	jsrsExecute(jsrsFile, ret_nombre_cuentaU, 'JSgetNombreCuenta', mUCuenta);
}
function ret_nombre_cuentaU(sUCuenta){
	document.getElementById("tdNombreCuentaU").innerHTML = sUCuenta;
}
function validar_cuenta_U(){
	var cta = document.getElementById("idCuentaU").value;

	var sFind = /<?php echo CDIVISOR; ?>/g;

		cta = cta.replace(sFind, "");
		document.getElementById("idCuentaU").value = cta.substr(0, iLargoCta);

	if (cta.length < iLargoCta) {
		var cta = cta + sFakeCuenta;
		document.getElementById("idCuentaU").value = cta.substr(0, iLargoCta);
		nombre_cuenta_U();
		chkAfectable_U();
	} else if (cta.length ==iLargoCta) {
		nombre_cuenta_U();
		chkAfectable_U();
	} else {
		document.getElementById("idCuentaU").value = cta.substr(0, iLargoCta);
		nombre_cuenta_U();
		chkAfectable_U();
	}
}
function chkAfectable_U(){
	var mUCuenta = document.getElementById("idCuentaU").value;
	jsrsExecute(jsrsFile, str_to_eval, 'JSchkCuentaU', mUCuenta);
}
function charEventUp(evt) {
    evt				= (evt) ? evt:event;

    var mUKey 		= document.getElementById("idkeymvtoU").value;

    var charCode 	= (evt.charCode) ? evt.charCode :
        ((evt.which) ? evt.which : evt.keyCode);

        ctrlCuenta 	= "idCuentaU";

		var cmdKey 	= evt.target.parentNode.parentNode.id;
			rRows 	= cmdKey.split(vDLit);
			cmdKey 	= "cmd@" + rRows[1] + vDLit + rRows[2] + vDLit  + rRows[3] + vDLit + rRows[4] + vDLit + rRows[5];
        switch(charCode){
        	case 27:	//Escape
    			jsrsExecute(jsrsFile, ret_mvto_no_edit, 'JSRetMvtoSalvado', mUKey);
    			rRowOnEdit = null;
        	break;
        	case 121:	// F10
    			//Salvar Movimiento Editado
				goUpdate();
        	break;
        	case 113:	// F2
				mostrar_catalogo(cmdKey);
        	break;
        	case 40:	//Arrow Down
        		setBlurGo();
        	break;
        	default:
        		if(onSearchInCatalog == true){
			        mostrar_catalogo(cmdKey);
        		} else {
    	 		//desaparece el catalogo siempre
					purgeCatalog(cmdKey);
		     		onSearchInCatalog = false;
        		}
        	break;
        }

}
function ret_mvto_no_edit(sHTML){
	var mUCuenta 			= document.getElementById("idCuentaU");
    	//Cancelar Update
    	var tdEdit 			= mUCuenta.parentNode;
    	var trEdit 			= tdEdit.parentNode;
    	trEdit.innerHTML 	= "";
    	trEdit.innerHTML 	= sHTML;
    	//
		wFrm.cuenta.focus();
		wFrm.cuenta.select();
}
/**
 * Funcion que Obtiene un Formato Moneda
 * @param {string} cantidad
 */
function getFPesos(cantidad){	return getInMoney(cantidad); }
/**
 * Funcion que se Centra el el Catalogo de Busqueda
 */
function setBlurGo(){
	if(onSearchInCatalog==true){
		document.getElementById("idUnderCuenta").focus();
	} else {
		/* Si la Cuenta es Nula o esta desaparecida */
		/*validar porque esta cuenta da error*/
		if(!ctrlCuenta || ctrlCuenta == "" || ctrlCuenta == null){
			ctrlCuenta = "idcuenta";
		}
		document.getElementById(ctrlCuenta).focus();
		document.getElementById(ctrlCuenta).select();
	}
}
function isCatalogAuto(){
	//document.getElementById("idSwCatalogo").addEventListener("onkeyup", setActionByKey, true);
}
/**
*	Funcion del catalogo de Cuentas que se muestra para Buscar
*/
function setActionByKey(evt){
	evt = (evt) ? evt : ((event) ? event : null);
	var mChar	= (evt.charCode) ? evt.charCode : ((evt.which) ? evt.which : evt.keyCode);
	var mVal	= document.getElementById("idUnderCuenta").value;
	switch (mChar){
		case 13:		//Entrar
			setNumeroCuenta(mVal);
		//Seleccionar y Cerrar
			break;
		case 27:		//Escape
		//salir
			if(!ctrlCuenta || ctrlCuenta == "" || ctrlCuenta == null){
				ctrlCuenta = "idcuenta";
			}
			document.getElementById(ctrlCuenta).focus();
			document.getElementById(ctrlCuenta).select();
			//purage el catalogo
			purgeCatalog();
			break;
	}
}
/**
*	Funcion para Purgar el Catalogo
*/
function purgeCatalog(){
	dSwCatalogo.style.visibility 	= "hidden";
	dSwCatalogo.innerHTML 			= "";
	dSwCatalogo.style.top 			= "0px";
	dSwCatalogo.style.left 			= "0px";
	dSwCatalogo.style.height 		= "0px";
	onSearchInCatalog 				= false;
}
/**
*	Funcion que determina si debe Cerrar el SELECT
*/
function setNoSearchCatalog(){
	if(onSearchInCatalog==true){
		//setTimeout("goToCuenta()", 300);
		var mVal	= document.getElementById("idUnderCuenta").value;
		setTimeout("setNumeroCuenta(" +  mVal + ")", 300);
	} else {
		setTimeout("goToCuenta()", 300);
		purgeCatalog();
	}
}
function goToCuenta(){
		document.getElementById(ctrlCuenta).focus();
		document.getElementById(ctrlCuenta).select();
}
function goUpdate(){
	var mUKey 	= document.getElementById("idkeymvtoU").value;
	var siUp 	= confirm("CONFIRMA ACTUALIZAR EL MOVIMIENTO?");

	if(siUp){
    	UpdateEditMvto();
    	jsrsExecute(jsrsFile, ret_mvto_no_edit, 'JSRetMvtoSalvado', mUKey);
    	rRowOnEdit = null;
    	parent.setPolizaValues();
	} else {
    	jsrsExecute(jsrsFile, ret_mvto_no_edit, 'JSRetMvtoSalvado', mUKey);
    	rRowOnEdit = null;
	}
}
function setPolMsg(msg){
	parent.document.getElementById("ipolmsg").innerHTML = msg;
}

function cwLog(strTxt){
	if( window.console ) { 
		window.console.log( strTxt );
	 }
}
</script>
</html>