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
$xHP		= new cHPage("TR.BONIFICACIONES DE CREDITO", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto		= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT);
$recibo		= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$parcialidad	= parametro("idparcialidad", 1, MQL_INT);
$observaciones	= parametro("idobservaciones");
$tipo			= parametro("idtipodebonificacion", 0, MQL_INT);
$xHP->init();

$xFRM		= new cHForm("frm", "./frmcreditos_descuentos.php");
$xFRM->setTitle($xHP->getTitle());
//$xFRM->addJsBasico();
if($credito <= DEFAULT_CREDITO){
	$xFRM->addCreditBasico();
	$xFRM->addSubmit();
} else {
	$xCred		= new cCredito($credito);
	if($xCred->init() == true){
		if($action == SYS_NINGUNO){
			$xFRM->setAction("./frmcreditos_descuentos.php?action=" . MQL_ADD);
			$xFRM->addGuardar();
			$xFRM->addHElem( $xCred->getFicha(true, "", false, true) );
			$xFRM->OHidden("idsolicitud", $credito);
			$xFRM->addFecha();
			if($xCred->getPeriocidadDePago() == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO ){
				//
				$xTxt		= new cHText();
				//$xTxt->setDivClass("");
				$xFRM->addHElem( $xTxt->getNumero("idparcialidad", $xCred->getPeriodoActual()+1, "TR.Numero de Parcialidad") );
			} else {
				$plan		= $xCred->getNumeroDePlanDePagos();
				if($plan != false){
				$xPlan		= new cPlanDePagos($plan); $xPlan->init();
				$parcs		= $xPlan->getParcsPendientes();
				//$txt		= "";
				$arrD		= array();
					foreach ($parcs as $p){
						//setLog( $p[SYS_NUMERO]. " " . $xF->getFechaDDMM($p[SYS_FECHA]) . " ". getFMoney($p[SYS_TOTAL]));
						if( setNoMenorQueCero($p[SYS_TOTAL]) > 0){ $arrD[$p[SYS_NUMERO]]	= $p[SYS_NUMERO]. " " . $xF->getFechaDDMM($p[SYS_FECHA]) . " ". getFMoney($p[SYS_TOTAL]); }
					}
					$xSel		= new cHSelect();
					$xSel->addOptions($arrD);
					//$xSel->setEnclose(false);
					$xFRM->addHElem( $xSel->get("idparcialidad", "TR.Numero de Parcialidad", $xCred->getPeriodoActual()+1));
				} else {
					if(MODO_CORRECION == true){
								$xTxt		= new cHText();
								//$xTxt->setDivClass("");
								$xFRM->addHElem( $xTxt->getNumero("idparcialidad", $xCred->getPeriodoActual()+1, "TR.Numero de Parcialidad") );
					}
				}
				$xFRM->addHElem($xSel->getListaDeBonificacionesCredito()->get(true));
			}
			$xFRM->addMonto();
			$xFRM->addObservaciones();
										
		} else {
			//Agregar
			$xFRM->addCerrar();
			$xPagos	= new cCreditosPagos($credito);
			$xPagos->init();
			$recibo	= $xPagos->addBonificacion($monto, $parcialidad, $observaciones, TESORERIA_COBRO_NINGUNO, $tipo, $fecha);
			
			$xRec	= new cReciboDeOperacion(false, false, $recibo);
			if($xRec->init() == true){
				$xFRM->addHElem( $xRec->getFicha(true) );
				$xFRM->addAvisoRegistroOK();
				$xFRM->addImprimir();
				$xFRM->addJsCode($xRec->getJsPrint());
			}
		}
	}
}

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
exit;
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
include_once "../core/entidad.datos.php";
include_once "../core/core.deprecated.inc.php";
include_once "../core/core.fechas.inc.php";
include_once "../core/core.config.inc.php";
include_once "../core/core.creditos.inc.php";
include_once "../core/core.operaciones.inc.php";
include_once "../core/core.common.inc.php";
include_once "../core/core.html.inc.php";

$oficial = elusuario($iduser);

$xHPag		= new cHPage("Descuentos de Creditos");

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Descuentos de Creditos</title>
</head>
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
<?php
jsbasic("myformdc", "", ".");
?>
<body>
<fieldset>
	<legend><script> document.write(document.title); </script></legend>
	
<form name='myformdc' action='frmcreditos_descuentos.php' method='post'>
	<table width='100%' border='0'  >
	<tr>
	<td>Clave de Persona</td>
	<td><input type="text" name="idsocio" onchange="envsoc();" size='12' maxlength='20' class='mny' />
	<?php echo CTRL_GOSOCIO; ?></td>
	<td  colspan='2'><input disabled name="nombresocio" type="text" size="50"></td>
	</tr>
		<td>N&uacute;mero de Solicitud</td>
		<td><input type="text" name="idsolicitud" onchange="envsol();" size='12' maxlength='20' class='mny'  />
		<?php echo CTRL_GOCREDIT; ?></td>
		<td colspan='2'><input disabled name="nombresolicitud" type="text" size="50"></td>
	</tr>
	<tr>
	<td>Parcialidad</td>
	<td><input type='text' name='idparcialidad' value='0'  class='mny' size="3"  /> 
	<?php echo CTRL_GOLETRAS; ?></td>
	</tr>
		<tr>
			<td>Concepto del Descuento</td><td colspan="3">
		<?php
		$gssql= "SELECT * FROM operaciones_tipos WHERE class_efectivo=8";
		$mGS = new cSelect("tipodescuento", "", $gssql);
		$mGS->setEsSql();
		$mGS->show(false);
		?></td>
		</tr>
		<tr>
			<td>Monto</td><td><input type='text' name='monto' value='0' class='mny' size="12" /></td>
		</tr>
		<tr>
			<td>Observaciones</td><td colspan="3"><input name='observaciones' type='text' value='' size="55" maxlength="100"></td>
		</tr>
	</table>
<input type='button' name='btsend' value='GUARDAR DATOS'onClick='frmSubmit();'>
</form>
</fieldset>
<?php
	$socio 				= $_POST["idsocio"];
	$documento 			= $_POST["idsolicitud"];
	$tipo 				= $_POST["tipodescuento"];
	$monto 				= $_POST["monto"];
	$parcialidad 		= $_POST["idparcialidad"];
	$observaciones  	= $_POST["observaciones"];
	$fecha_operacion	= fechasys();
if ( isset($socio) AND $monto>0) {

	$xBtn		= new cHButton("id-cmdImprimir");
	$xRec		= new cReciboDeOperacion(96, false);
	$recibo		= $xRec->setNuevoRecibo($socio, $documento, $fecha_operacion, $parcialidad, 96, $observaciones);
	
	$xRec->setNuevoMvto($fecha_operacion, $monto, $tipo, $parcialidad, $observaciones, -1, TM_ABONO);
	
	$xRec->setFinalizarRecibo();
		
	echo $xRec->getFichaSocio();
	echo $xRec->getFicha(true, "<tr><th colspan='4'>" . $xBtn->getImprimirRecibo() . "</th></tr>");

} // end if
?>
</body>
<script  >
<?php
if ( isset($socio) AND $monto>0) {
	echo $xRec->getJsPrint(); 
}
?>
</script>
</html>
