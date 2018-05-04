<?php
/**
 * @author Balam Gonzalez Luis Humberto
* @version 0.0.01
* @package
*/
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
$xHP		= new cHPage("TR.Entrega de Garantias", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$jxc 		= new TinyAjax();
function jsaGetListaDeGarantias($idcredito){
	if($idcredito > DEFAULT_CREDITO){
		$xLi		= new cSQLListas();
		$sql_final = $xLi->getListadoDeGarantiasReales("", $idcredito, false, CREDITO_GARANTIA_ESTADO_RESGUARDADO);
		$myTab 		= new cTabla($sql_final);
		$myTab->setEventKey("setToGoGuardar");
		$myTab->setKeyField("idcreditos_garantias");
		return $myTab->Show();
	}
}
$jxc ->exportFunction('jsaGetListaDeGarantias', array('idsolicitud'), "#idlistado");
$jxc ->process();

$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$clave			= parametro("id", 0, MQL_INT); $clave = parametro("clave", $clave, MQL_INT);
$xHP->init();
$observaciones	= parametro("idobservaciones");

$xFRM			= new cHForm("frm", "frmdevgarantiaresguardo.php");
$msg			= "";
$xFRM->setTitle($xHP->getTitle());
//$xFRM->addJsBasico();
if($clave <= 0){
	$xFRM->addCreditBasico();
	$xFRM->OButton("TR.Obtener Garantias", "jsaGetListaDeGarantias()", $xFRM->ic()->EJECUTAR);
	$xFRM->addHTML("<div id='idlistado'></div>");
	$xFRM->OHidden("clave", 0);
	$xFRM->setAction("frmdevgarantiaresguardo.php?action=" . MQL_MOD);
	$xFRM->addCerrar();
	
} else {
	//$xFRM->addAtras();
	$xGar	= new cCreditosGarantias($clave);
	if($xGar->init() == true){
		$xFRM->addHElem( $xGar->getFicha() );
		$xFRM->OHidden("clave", $clave);
		if($action == MQL_MOD){
			if($xGar->setEstatus(CREDITO_GARANTIA_ESTADO_ENTREGADO, $fecha, $observaciones) == true){
				$xFRM->addAvisoRegistroOK();
				$xFRM->OButton("TR.Imprimir Acuse", "getRecibo()", $xFRM->ic()->IMPRIMIR);
			} else {
				$xFRM->addAvisoRegistroError();
			}
			$xFRM->addLog($xGar->getMessages());
			$xFRM->addCerrar();
		} else {
			$xFRM->ODate("idfechaactual", $fecha, "TR.Fecha de resguardo");
			$xFRM->addObservaciones();
			$xFRM->setAction("frmdevgarantiaresguardo.php?action=" . MQL_MOD);
			$xFRM->addGuardar();
		}
	}
	//

}
//$xFRM->addSubmit();
echo $xFRM->get();
?>
<script>
	var xG	= new Gen(); 
	var idG	= "<?php echo $clave; ?>";
	function setToGoGuardar(id){
		$("#clave").val(id);
		xG.confirmar({
			msg : "Desea Guardar el Resguardo?",
			callback : setSave
			});
		
	}
	function setSave(){
		$("#id-frm").submit();
	}
	function getRecibo(){
		xG.w({ url : "../rpt_formatos/entrega_de_garantias.rpt.php?clave=" + idG, fullscreen: true});
		//xG.w({ url : "../rpt_formatos/rptreciboresguardo.php?clave=" + idG, fullscreen: true});
	}
</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin(); exit;
?>
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
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");

$oficial = elusuario($iduser);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Devoluci&oacute;n de Garantias en Resguardo</title>
</head>
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
<?php
jsbasic("myformdgr", "", ".");
?>
<body>
<fieldset>
	<legend>Devoluci&oacute;n de Garantias en Resguardo</legend>


<form name='myformdgr' action='frmdevgarantiaresguardo.php' method='post'>
	<table   border='0'>
		<tr>
			<td>Clave de Persona</td>
			<td><input type='text' name='idsocio' value='' onchange="envsoc();">
			<?php echo CTRL_GOSOCIO; ?></td>
			<td colspan='2'><input name='nombresocio' type='text' disabled value='' size="40"></td>
		</tr>
		<tr>
			<td>N&uacute;mero de Solicitud</td>
			<td><input type='text' name='idsolicitud' value='' onchange="envsol();">
			<?php echo CTRL_GOCREDIT; ?></td>
			<td  colspan='2'><input name='nombresolicitud' type='text' disabled value='' size="40"></td>
		</tr>
	</table>
<input type='button' name='btsend' value='ENVIAR DATOS'onClick='frmSubmit();'>
</form>
</fieldset>

<?php
$idsolicitud = $_POST["idsolicitud"];
	if (!$idsolicitud) {
		exit($msg_rec_warn . $fhtm);
	}
	// si el saldo del credito es mayor a cero, nanay, no se devuelve la garantia
	$saldo 		= volcarsol($idsolicitud, 22);
	$sdovenc 	= volcarsol($idsolicitud, 26);
	if($saldo > TOLERANCIA_SALDOS) {
			exit("<p class='aviso'>EL CREDITO TIENE SALDO POR PAGAR</p></body></html>");
	}
	if ($sdovenc > 0) {
			//exit("<p class='aviso'>EL CREDITO TIENE SALDO VENCIDO POR PAGAR</p></body></html>");
	}
	// Imprime la FICHA
	$xCred		= new cCredito($idsolicitud);
	$xCred->init();
	echo $xCred->getFicha();
	
	echo "
		<br>
	";
	//
	$sqli = "SELECT idcreditos_garantias, estatus_actual FROM creditos_garantias WHERE solicitud_garantia=$idsolicitud AND estatus_actual=2";
	$rsi = mysql_query($sqli);
		while($rwi = mysql_fetch_array($rsi)) {
			$idgar= $rwi[0];

		// Checa si la Garantia ya se entreg?
			$estatus = $rwi[1];
			if ($estatus == 3) {
				echo("<p class='aviso'>LA GARANTIA YA SE HA ENTREGADO</p></body></html>");
			}
			echo "<form name='myform$idgar' action='' method='post'>";
				minificha(4, $idgar);
			echo "
			<input type='hidden' name='idgar' value='$idgar'>
			</form>
			<input type='button' name='btend' value='GUARDAR / IMPRIMIR ENTREGA'onClick='frmEntrega(document.myform$idgar.idgar.value);'>
			<hr>
			";
		}
	@mysql_free_result($rsi);
?>
</body>
<script  >
	function frmEntrega(lavar) {
		var mivar = lavar;
			url = "../rpt_formatos/entrega_de_garantias.rpt.php?clave=" + mivar;
				miwin = window.open(url);
				miwin.focus();
	}
</script>
</html>