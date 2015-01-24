<?php
/**
 * @since 1.2 - 02/04/2008
 * @author Balam Gonzalez luis
 * @version 1.2
 * @package creditos
 * @subpackage formularios
 *
 * 		Actualizaciones
 * 		- Depurar Fecha  en Planes de pagos
 * 		- Se Reviso la Compatibilidad con dias festivos
 *		- 2008-05-28	Se agrego el s?porte del Tipo de pago
 *		- 2008-06-16	Se agrego un fieldset
 *		- se agrego el soporte de llamada por GET.
 * 		- 20080619 Se agrego el soporte a creditos reconvenidos
 * 		- Nuevo Formato de Plan de Pagos
 */
//=====================================================================================================
//TODO: 2012-04-13 Reescribir Plan de pagos
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
$xHP					= new cHPage("Generar Planes de Pagos");
$jxc 					= new TinyAjax();
$xF						= new cFecha();

$solicitud 				= (isset($_POST["idsolicitud"])) ? $_POST["idsolicitud"] : 0;
$altSocio				= DEFAULT_SOCIO;
$altCred				= DEFAULT_CREDITO;
$altOC					= 0;
$altIDOC				= OPERACION_CLAVE_DE_COBRANZA;// 1005;

$creditosDiasDePago		= CREDITO_DEFAULT_DIAS_DE_PAGO;
$defFechaMinistracion	= fechasys();
$defFechaAbono			= fechasys();
$defCuentaCorriente		= CTA_GLOBAL_CORRIENTE;
$defFormaDePago			= CREDITO_DEFAULT_TIPO_PAGO;

$urlsend				= "";
$idrecibo				= DEFAULT_RECIBO;
$urctr					= "";
$msgAlertas				= "";
$msgPIE					= "";
$rmt					= (isset($_GET["r"]) ) ? $_GET["r"] : 0;

$cmd					= (isset($_GET["cmd"]) ) ? $_GET["cmd"] : SYS_NINGUNO;

if ( $rmt == SYS_UNO){
	$altCred			= (isset($_GET["c"])) ? $_GET["c"] : DEFAULT_CREDITO;
	$altCred			= (isset($_GET["credito"])) ? $_GET["credito"] : $altCred;
	$xF				= new cFecha();
	$altOC				= (isset($_GET["o"])) ? $_GET["o"] : $altOC;
	$altIDOC			= (isset($_GET["i"])) ? $_GET["i"] : $altIDOC;
	//Inicizalizar Credito
	$xCred				= new cCredito($altCred);
	$xCred->init();
	$altSocio			= $xCred->getClaveDePersona();

	$defFechaMinistracion		= $xCred->getFechaDeMinistracion();
	$periocidad					= $xCred->getPeriocidadDePago();
	$defFechaAbono				= $xF->setSumarDias($periocidad, $defFechaMinistracion);
	$defCuentaCorriente			= $xCred->getContratoCorriente();
	$defFormaDePago				= $xCred->getTipoDePago();
	if($xCred->getPeriocidadDePago() == CREDITO_TIPO_PERIOCIDAD_QUINCENAL ){
		$creditosDiasDePago 	= CREDITO_TIPO_DIAS_DE_PAGO_PREDETERMINADOS;
	}
}

//-------------------------------------------------------------
function jsaGetControlDias($mod, $Msolicitud){
	$txt			= "";
	if ( isset($mod) AND isset($Msolicitud) ){
		$mod			= intval($mod);
		$periocidad 	= 15;
		$p_quincena 	= PQ_DIA_PRIMERA_QUINCENA;
		$s_quincena 	= PQ_DIA_SEGUNDA_QUINCENA;
		$xF				= new cFecha();
		$xT				= new cTipos();
	//---------------------------------------------------------
		$xCred			= new cCredito($Msolicitud);
		$xCred->init();
		$periocidad	= $xCred->getPeriocidadDePago();
	//cargar si es Nomina
		$xPlanGen		= new cPlanDePagosGenerador();
		$xPlanGen->initPorCredito($Msolicitud);
		
		switch ($mod){
			case CREDITO_TIPO_DIAS_DE_PAGO_PERSONALIZADOS:
				if($periocidad== CREDITO_TIPO_PERIOCIDAD_QUINCENAL){
					$p_quincena	= $xPlanGen->getDiaAbono1();
					$s_quincena	= $xPlanGen->getDiaAbono2();
					
					$txt		= "
						<th>Primer Dia de Abono en el Mes</th>
						<td><input type=\"text\" name=\"dia_primer_abono\" id=\"iddia_primer_abono\" size=\"3\" class=\"mny\" value=\"$p_quincena\" /></td>

						<th>Segundo Dia de Abono en el Mes</th>
						<td><input type=\"text\" name=\"dia_segundo_abono\" id=\"iddia_segundo_abono\" size=\"3\" class=\"mny\" value=\"$s_quincena\" /></td>
						";
				} elseif ($periocidad == CREDITO_TIPO_PERIOCIDAD_SEMANAL){
					
					$txt		= "
						<td>Primer Dia de Abono</td>
						<td><select name=\"dia_primer_abono\" id=\"iddia_primer_abono\" >
							<option value=\"1\">Lunes</option>
							<option value=\"2\">Martes</option>
							<option value=\"3\">Miercoles</option>
							<option value=\"4\">Jueves</option>
							<option value=\"5\">Viernes</option>
							<option value=\"6\">Sabado</option>
						</select> </td>";
				} elseif ($periocidad == CREDITO_TIPO_PERIOCIDAD_DECENAL){
					$txt		= "<td>Dias de Abono(Decenal)</td>
						<td><input type=\"text\" name=\"dia_primer_abono\" onblur=\"jsCambiarDiaPago(this)\" id=\"iddia_primer_abono\" value=\"10,20,30\" /></td>";						
				} else {
					$pago_mensual		= PM_DIA_DE_PAGO;
					$xF->set($xCred->getFechaDeMinistracion());
					$pago_mensual		= date("j", $xF->getInt());
					$pago_mensual		= $xPlanGen->getDiaAbono1();
					$txt		= "<td>Dia de Abono</td>
						<td><input type=\"number\" name=\"dia_primer_abono\" onblur=\"jsCambiarDiaPago(this)\" id=\"iddia_primer_abono\" value=\"" . $pago_mensual . "\" /></td>";
				}
				break;
		}
	}
	return $txt;
}

function jsa_getDatosDeCredito($solicitud){

	$xCred						= new cCredito($solicitud);
	$xCred->initCredito();
	$xF							= new cFecha();
	$xT							= new cTipos();

	$dCreds 					= $xCred->getDatosDeCredito();
	$periocidad					= $xCred->getPeriocidadDePago();
	$FMinistracion				= $xCred->getFechaDeMinistracion();
	$contrato_corriente			= $xCred->getContratoCorriente();
	
	$xPlanGen					= new cPlanDePagosGenerador();
	$xPlanGen->initPorCredito($solicitud);
	$FPrimerAb					= $xPlanGen->getFechaDePrimerPago();
	
	$xF							= new cFecha(0, $FMinistracion);
	$FM_d						= $xF->dia();
	$FM_a						= $xF->anno();
	$FM_m						= $xF->mes();

	$xF2						= new cFecha(1, $FPrimerAb);
	$xF2->set($FPrimerAb);
	$PA_d						= $xF2->dia();
	
	$PA_a						= $xF2->anno();
	$PA_m						= $xF2->mes();

	$tab = new TinyAjaxBehavior();
	//setLog("$PA_d --- $PA_m ---- $PA_a  - - - - - $FPrimerAb");
	$tab -> add(TabSetvalue::getBehavior('ideldia1', $FM_d));
	$tab -> add(TabSetvalue::getBehavior('idelmes1', $FM_m));
	$tab -> add(TabSetvalue::getBehavior('idelanno1', $FM_a));

	$tab -> add(TabSetvalue::getBehavior('ideldia0', $PA_d));
	$tab -> add(TabSetvalue::getBehavior('idelmes0', $PA_m));
	$tab -> add(TabSetvalue::getBehavior('idelanno0', $PA_a));

	$tab -> add(TabSetvalue::getBehavior('idDescripcionSolicitud', $xCred->getShortDescription() ));
	$tab -> add(TabSetvalue::getBehavior('idcuenta', $contrato_corriente ));
	
	//$xCred->getPeriocidadDePago() != CREDITO_TIPO_PERIOCIDAD_CATORCENAL
	if($xCred->getTipoEnSistema() == CREDITO_PRODUCTO_NOMINA ){
		$tab -> add(TabSetvalue::getBehavior('idFormaDePago', $xCred->getTipoDePago() ));
		//setLog("El pago es " . $xCred->getPeriocidadDePago() );
		if($xCred->getPeriocidadDePago() != CREDITO_TIPO_PERIOCIDAD_CATORCENAL){
			$tab -> add(TabSetvalue::getBehavior('idtipo_plan_pagos', CREDITO_TIPO_DIAS_DE_PAGO_PERSONALIZADOS ));
		}
	}
	
	return $tab -> getString();
}
function getNombre_FechaDeMinistracion($dia, $mes, $anno){
	$d		= date ("Y-m-d", strtotime($anno . "-" . $mes . "-" . $dia) );
	$xFec	= new cFecha(0, $d );
	return $xFec->getDayName();
}
function getNombre_FechaDeAbono($dia, $mes, $anno){
	$d		= date ("Y-m-d", strtotime($anno . "-" . $mes . "-" . $dia) );
	$xFec	= new cFecha(0, $d );
	return $xFec->getDayName();
}

$jxc ->exportFunction("jsaGetControlDias", array("idtipo_plan_pagos", "iidsolicitud"), "#exCtrl");
$jxc ->exportFunction('jsa_getDatosDeCredito', array("iidsolicitud"));
$jxc ->exportFunction('getNombre_FechaDeMinistracion', array("ideldia1", "idelmes1", "idelanno1"), "#swFechaMin");
$jxc ->exportFunction('getNombre_FechaDeAbono', array("ideldia0", "idelmes0", "idelanno0"), "#swFechaAb");
$jxc ->process();

echo $xHP->getHeader();

$xJs		= new jsBasicForm("frmplanpagos");
$xJs->setConCreditos();
$xJs->setEstatusDeCreditos(CREDITO_ESTADO_AUTORIZADO);
if($rmt == SYS_UNO){ $xJs->setLoadDefaults(); }

$xFRM		= new cHForm("frmplanpagos", "frmcreditosplandepagos.php?cmd=10");
$xBtn		= new cHButton();
$xTxt		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();
$xJs->setNameForm( $xFRM->getName() );
?>
<body>
<?php
if($cmd == SYS_NINGUNO){
?>
<fieldset>
<legend>Plan de Pagos.- Generar</legend>
<form name="frmplanpagos" action="frmcreditosplandepagos.php?cmd=10" method="post">
	<fieldset>
		<legend>Datos Generales</legend>
	<table>
	<?php
	if(PERMITIR_EXTEMPORANEO == true){ echo CTRL_FECHA_EXTEMPORANEA; }
	?>
		<tr>
			<td>Clave de Persona</td>
			<td><input type='number' name='idsocio' id='idsocio' value='<?php echo $altSocio; ?>' onblur="envsoc()" onchange="envsoc()"
						size='12' class='mny' />
			<?php echo CTRL_GOSOCIO; ?></td>
			<td><input disabled name='nombresocio' type='text' value='' size="40"></td>
		</tr>
		<tr>
			<td>Numero de Solicitud</td>
			<td><input type="number" name="idsolicitud" onchange="envsol();" id="iidsolicitud" onblur='jsa_getDatosDeCredito()'
				   value="<?php echo $altCred; ?>" class='mny' size='12' />
			<?php echo CTRL_GOCREDIT; ?></td>
			<td><input disabled name="nombresolicitud" id="idDescripcionSolicitud" type="text" size="40"></td>
		</tr>
		<?php
		if(MODULO_CAPTACION_ACTIVADO == true){
		?>
		<tr>
			<td colspan='1' title='Cuenta de Captacion Relacionada'>Cuenta de Captacion</td>
			<td><input type="number" name="idcuenta" value="<?php echo $defCuentaCorriente; ?>" id="idcuenta" size='12' class='mny' />
			<?php echo CTRL_GOCUENTAS_A; ?>
			</td>
			<td><?php
			$xTxt = new cHText("nombrecuenta");
			$xTxt->setProperty("disabled", "true");
			$xTxt->setProperty("size", "40");
			echo $xTxt->get();
			?></td>
		</tr>
		<?php 
		} else { echo "<input type='hidden' id='idcuenta' name='idcuenta' value='$defCuentaCorriente' />"; }
		?>
	</table>
	</fieldset>
	<fieldset>
	<legend>Caracteristicas del Pago</legend>
		<table>
			<tr>
				<td title='Forma en que Pagar&aacute; el Credito'>Forma en que Pagar&aacute; el credito</td>
				<td colspan="3" >
				<?php
					$sqlTP	= "SELECT * FROM creditos_tipo_de_pago WHERE idcreditos_tipo_de_pago !=1 ";
					$cSel = new cSelect("cFormaDePago","idFormaDePago", $sqlTP);
					//$cSel->setNRows(2);
					$cSel->setOptionSelect($defFormaDePago);
					$cSel->setEsSql();
					$cSel->show(false);
				?>
		            </td>
		     </tr>
		    
		    <tr>
			<td title='Modalidad en que Fijas los dias de Pagos'>Modalidad en que Fijas los dias de Pagos</td>
			<td colspan="3">
			<?php
				$sqlTP	= "SELECT * FROM creditos_dias_de_pago ";
				$cSel = new cSelect("tipo_plan_pagos","idtipo_plan_pagos", $sqlTP);
				//$cSel->setNRows(2);
				$cSel->setOptionSelect($creditosDiasDePago);
				$cSel->addEvent("onchange", "jsGetControlDias");
				$cSel->addEvent("onblur", "jsGetControlDias");
				$cSel->setEsSql();
				$cSel->show(false);
			?>
			</td>
			
		    <tr>
			<td title='Modalidad en que Fijas el redondeo'>Modalidad de Redondeo</td>
			<td colspan="3">
				<select id="redondeo" name="redondeo">
					<option value="50">Redondear a .50</option>
					<option value="100">Redondear a 1</option>
					<option value="0" selected>Ninguno</option>
				</select>
			</td>
			
			</tr>
			<tr id="exCtrl"></tr>
		</table>
	</fieldset>

	<fieldset>
		<legend>Fechas</legend>
		<table>

		<tr>
			<td>Fecha de Ministraci&oacute;n (propuesta)</td>
			<td id="id-td-fecha1" ><?php
			$xF = new cFecha(1, $defFechaMinistracion);
			$xF->addEvent("onchange", "setEvaluateDate");
			$xF->addEvent("onblur", "setEvaluateDate");
			$xF->show(false, "OPERATIVO");?></td>
			<td><p id="swFechaMin" class="aviso"></p></td>
		</tr>
		<tr>
			<td>Fecha del Primer Abono (Obligatorio)</td>
			<td id="id-td-fecha2"><?php
			$xF = new cFecha(0, $defFechaAbono);
			$xF->addEvent("onchange", "setEvaluateDate");
			$xF->addEvent("onblur", "setEvaluateDate");
			$xF->show(false, "OPERATIVO");
			?></td>
			<td><p id="swFechaAb" class="aviso"></p></td>
		</tr>
	</table>
	</fieldset>

	<fieldset>
		<legend>Otros</legend>
	<table>
	<tr>
		<td>Otros Cargos</td>
		<td ><?php
			//Sql de especiales
		$sqlEsp = "SELECT
			`eacp_config_bases_de_integracion_miembros`.`miembro`,
			`operaciones_tipos`.`descripcion_operacion`
		FROM
			`operaciones_tipos` `operaciones_tipos`
				INNER JOIN `eacp_config_bases_de_integracion_miembros`
				`eacp_config_bases_de_integracion_miembros`
				ON `operaciones_tipos`.`idoperaciones_tipos` =
				`eacp_config_bases_de_integracion_miembros`.`miembro`
		WHERE
			(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` =1001)
		ORDER BY
			`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`";

		$cEsps = new cSelect("cMvtosEsp","idMvtosEsp", $sqlEsp);
		$cEsps->setEsSql();
		$cEsps->setOptionSelect($altIDOC);
		$SEsp = $cEsps->show();
		echo $SEsp;
			 ?></td>
			 <td>Monto</td>
			 <td><input name='cMontoCargoExtra' type='text' class="mny" value="<?php echo $altOC; ?>" /></td>
	</tr>
	<?php if(MODO_MIGRACION == true){ /*  echo '<tr><td>Plan Anterior</td><td><input type="checkbox" name="plananterior" id="plananterior" ></td><td></td></tr>'; }*/ } 	?>
		<tr>
			<td>Observaciones</td>
			<td><input type="text" name="observaciones" value="" size="35"/></td>
		</tr>
		<tr>
			<th colspan='4'><input type="button" name="sendme" value="Generar" onClick="validar_formulario();"/></th>
		</tr>
	</table>
	</fieldset>

	<p class="aviso">*** LA FECHA DE PRIMER ABONO ES OBLIGATORIO ****<br />
	LOS DIAS DE PAGOS PREESTRABLECIDOS SON <?php echo PQ_DIA_PRIMERA_QUINCENA; ?> Y <?php echo PQ_DIA_SEGUNDA_QUINCENA; ?>
	PARA PAGOS QUINCENALES <br />
	Y DIA <?php echo PM_DIA_DE_PAGO; ?> PARA PAGOS MENSUALES <br />
	LOS CARGOS EXTRAS SE DIVIDEN EN PARTES IGUALES POR LETRAS<br />
	</p>
</form>
<?php
$jxc ->drawJavaScript(false, true);
echo $xJs->get();
?>
<script>
var mywFrm  				= document.frmplanpagos;
var Wo					= new Gen();
var PQ_DIA_PRIMERA_QUINCENA		= <?php echo PQ_DIA_PRIMERA_QUINCENA; ?>;
var PQ_DIA_SEGUNDA_QUINCENA		= <?php echo PQ_DIA_SEGUNDA_QUINCENA; ?>;

function validar_formulario(){ mywFrm.submit();}
function nueva_cuenta_captacion(socio, credito){
	if(socio && credito){
		var siAltaCuenta = confirm("DESEA DAR DE ALTA A LA CUENTA DE CAPTACION \n*********** PARA ESTE CREDITO? ***********");
		if(siAltaCuenta){ Wo.w({ url : "../captacion/frmcaptacioncuentas.php?o=2&s=" + socio + "&c=" + credito }); }
	}
}

function setEvaluateDate(id){
	mDia = entero(document.getElementById("ideldia" + id).value);
	mMes = entero(document.getElementById("idelmes" + id).value);
	mAnn = entero(document.getElementById("idelanno" + id).value);
	<?php 
		if ( WORK_IN_SATURDAY == true ) {
			echo "var mTrabInSat = true;";
		} else {
			echo "var mTrabInSat = false;";
		}
	?>
	var mDate	= new Date(mAnn, (mMes - 1), mDia, 0, 0, 0);
	var mDay	= mDate.getDay(); 
	if ( mDay == 0){
		alert ("EL DIA[" + mDia + "/" + mMes + "/" + mAnn + "] ES DOMINGO\n******VERIFIQUE SUS DATOS********");
	}
	if ( (mDay == 6)  && (mTrabInSat == false) ){
		alert ("EL DIA ES SABADO\nDIA INHABIL SEGUN SU CONFIGURACION\n*****VERIFIQUE SUS DATOS****");
	}
	getNombre_FechaDeMinistracion();
	getNombre_FechaDeAbono();
}
function jsGetControlDias(){ jsaGetControlDias();}
function jsCambiarDiaPago(sc){	$("#ideldia0").val(sc.value); }
</script>
</fieldset>
<?php

} else {
$xPlanGen	= new cPlanDePagosGenerador();

$oficial	= getUsuarioActual(); $xFecha	= new cFecha();
$msgM		= ""; $msgC		= ""; 
$msg		= "============================  LOG DE PLAN DE PAGOS =========================\r\n";
$msg		.= "============================  GENERADO POR $oficial \r\n";
$msg		.= "============================  FECHA " . date("Y-m-d H:s:i") . " \r\n";
$xLog		= new cFileLog("log-de-plan-de-pago-$solicitud", true);
//DATOS PREDEFINIDOS
//$DatosPlanAnterior			= false;
$OPCION_ANUAL_FLAT			= true;
$decenales					= array();
$dia_1_ab 					= PQ_DIA_PRIMERA_QUINCENA;
$dia_2_ab 					= PQ_DIA_SEGUNDA_QUINCENA;
$dia_3_ab 					= 30;
//==========================================================
$xCred						= new cCredito($solicitud);
$xCred->init();
$dsol   					= $xCred->getDatosDeCredito();
$estatus 					= $xCred->getEstadoActual();
$monto_autorizado 			= $xCred->getMontoAutorizado();
$PAGOS_AUTORIZADOS 			= $xCred->getPagosAutorizados();
$PERIOCIDAD_DE_PAGO 		= $xCred->getPeriocidadDePago();
$socio 						= $xCred->getClaveDePersona();
$DProducto					= $xCred->getOProductoDeCredito();
$tasa_ahorro 				= $DProducto->getTasaDeAhorro();
$tasa_interes 				= $xCred->getTasaDeInteres();
$dias_autorizados 			= $xCred->getDiasAutorizados();
$saldo_historico			= $xCred->getMontoAutorizado();
$saldo_actual				= $xCred->getSaldoActual();
$MontoCubierto				= ($saldo_historico -  $saldo_actual);		//Cuanto ha abonado
// ---------------------------------- Datos del Convenio -----------------------------------------
$dias_tolerancia_no_pago	= $DProducto->getDiasTolerados();
$tasa_iva					= $xCred->getTasaIVA();
$iva_incluido				= $DProducto->getTasaIncluyeIVA();
$tipo_de_autorizacion		= $xCred->getTipoDeAutorizacion();
$tipo_de_integracion		= $DProducto->getTipoDeIntegracion();
$tipo_de_calculo			= $xCred->getTipoDeCalculoDeInteres();
$interes_normal_pagado		= $xCred->getInteresNormalPagado();
$credito_abonado			= (($monto_autorizado > ($saldo_actual + TOLERANCIA_SALDOS)) AND $estatus < CREDITO_ESTADO_AUTORIZADO ) ? true : false;
// --------------------- DATOS OBTENIDOS DEL FORM
$cuenta_captacion			= (isset($_POST["idcuenta"])) ? $_POST["idcuenta"] : DEFAULT_CUENTA_CORRIENTE;
$observaciones 			 	= $_POST["observaciones"];
$fecha_primer_abono 	    = $_POST["elanno0"] . "-" . $_POST["elmes0"] . "-" . $_POST["eldia0"];
$fecha_ministracion 	    = $_POST["elanno1"] . "-" . $_POST["elmes1"] . "-" . $_POST["eldia1"];
$tipo_de_plan 		    	= $_POST["tipo_plan_pagos"];
$monto_extra			    = $_POST["cMontoCargoExtra"];
$tipo_monto_extra		    = $_POST["cMvtosEsp"];
$FormaDePago			    = isset($_POST["cFormaDePago"]) ? $_POST["cFormaDePago"] : 1;
$FormaDeRedondeo		    = isset($_POST["redondeo"]) ? $_POST["redondeo"] : 0;

$BaseDinamicaCalculo		= $xCred->getMontoAutorizado();

//MAE
$PlanMalo					= (isset($_POST["plananterior"] ) ) ? $_POST["plananterior"] : "off"; $PlanMalo	= ( $PlanMalo == "on" ) ? true : false;
//----------------------------------DATOS DEL RECIBO------------------------
$fecha_operacion			= fechasys();			// fecha de la Operacion y el recibo.
if(PERMITIR_EXTEMPORANEO == true){ $fecha_operacion = (isset($_POST["elanno98"])) ? $_POST["elanno98"] . "-" . $_POST["elmes98"] . "-" . $_POST["eldia98"] : fechasys(); }
// si el Plan es mensual/semanal
if($PERIOCIDAD_DE_PAGO >= CREDITO_TIPO_PERIOCIDAD_MENSUAL){ $dia_1_ab 		= PM_DIA_DE_PAGO; }
if($PERIOCIDAD_DE_PAGO >= CREDITO_TIPO_PERIOCIDAD_QUINCENAL){ $dia_1_ab 	= PQ_DIA_PRIMERA_QUINCENA; $dia_2_ab = PQ_DIA_SEGUNDA_QUINCENA; }
if($PERIOCIDAD_DE_PAGO >= CREDITO_TIPO_PERIOCIDAD_CATORCENAL){ $dia_1_ab 	= 14; $dia_2_ab = 28; }
if($PERIOCIDAD_DE_PAGO == CREDITO_TIPO_PERIOCIDAD_DECENAL){ $dia_1_ab 		= 10; $dia_2_ab	= 20; $dia_3_ab = 30; }
if($PERIOCIDAD_DE_PAGO == CREDITO_TIPO_PERIOCIDAD_SEMANAL){ $dia_1_ab 		= 1;  } //LUNES  date("N", strtotime($fecha_primer_abono));
$sucess				= true;
//Tipo de Plan de Pagos
switch( $tipo_de_plan ){
    case CREDITO_TIPO_DIAS_DE_PAGO_PERSONALIZADOS:
            switch( $PERIOCIDAD_DE_PAGO ){
            	case CREDITO_TIPO_PERIOCIDAD_MENSUAL:
            		$dia_1_ab 		= (isset($_POST["dia_primer_abono"])) ? $_POST["dia_primer_abono"] : false;
            		$sucess			= ($dia_1_ab == false) ? false : true;
            		break;
            	case CREDITO_TIPO_PERIOCIDAD_DECENAL:
            		$decenales		= (isset($_POST["dia_primer_abono"])) ? explode(",", $_POST["dia_primer_abono"]) : array(10,20,30);
            		$dia_1_ab		= (isset($decenales[0])) ? $decenales[0] : 10;
            		$dia_2_ab		= (isset($decenales[1])) ? $decenales[1] : 20;
            		$dia_3_ab		= (isset($decenales[2])) ? $decenales[2] : 30;
            		break;
            	case CREDITO_TIPO_PERIOCIDAD_QUINCENAL:
            		$dia_1_ab 		= (isset($_POST["dia_primer_abono"])) ? $_POST["dia_primer_abono"] : false;
            		$dia_2_ab 		= (isset($_POST["dia_segundo_abono"])) ? $_POST["dia_segundo_abono"] : false;
            		$sucess			= ($dia_2_ab == false) ? false : true;
            		break;
            	case CREDITO_TIPO_PERIOCIDAD_CATORCENAL:
            		$dia_1_ab 		= (isset($_POST["dia_primer_abono"])) ? $_POST["dia_primer_abono"] : false;
            		$dia_2_ab 		= (isset($_POST["dia_segundo_abono"])) ? $_POST["dia_segundo_abono"] : false;
            		$sucess			= ($dia_2_ab == false) ? false : true;
            		break;
            	case CREDITO_TIPO_PERIOCIDAD_SEMANAL:
            		$dia_1_ab 		= (isset($_POST["dia_primer_abono"])) ? $_POST["dia_primer_abono"] : false;
            		$sucess			= ($dia_1_ab == false) ? false : true;
            		break;
            	case CREDITO_TIPO_PERIOCIDAD_DIARIO:
            		break;
            }
    break;
    default:
    	
    	break;
}
// --------------------------------- CONDICIONES ----------------------------------------------
if($tasa_ahorro > 0 && ($cuenta_captacion == CTA_GLOBAL_CORRIENTE OR $cuenta_captacion == 0) ){								//Cuenta de Captacion no Debe ser igual a la definida
	$msgAlertas .= "ERROR\tEL CONTRATO DE CAPTACION RELACIONADO NO DEBE SER IGUAL AL ESTABLECIDO POR DEFAULT, YA QUE LA TASA DE AHORRO PARA ESTE CREDITO ES DE $tasa_ahorro, 
			DEBE TENER UNA CUENTA DE CAPTACION PROPIA PARA EFECTUAR LOS DEPOSITOS DE AHORRO\r\n";
	$sucess			= false;
}//Monto Autorizado Mayor  a cero
if($monto_autorizado <= 0 ){ $msgAlertas .= "EL MONTO AUTORIZADO ES CERO"; $sucess = false; } //Periocidad Diferente a Pago Unico
if($PERIOCIDAD_DE_PAGO == CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO OR $PERIOCIDAD_DE_PAGO == FALLBACK_CRED_TIPO_PERIOCIDAD){ $msgAlertas .= "NO APLICA POR EL TIPO DE FRECUENCIA DE PAGOS"; $sucess = false; }
//Si los dias entre la Ministracion y la fecha de Primer abono superan a la periocidad mas la tolerancia: Salir
if ( (restarfechas($fecha_primer_abono, $fecha_ministracion) > ($dias_tolerancia_no_pago + $PERIOCIDAD_DE_PAGO)) AND ($tipo_de_plan == 99) ){
    $msgAlertas .= "ERROR\tLOS DIAS DEL PRIMER ABONO NO DEBEN SER MAYORES A $dias_tolerancia_no_pago MAS LA PERIOCIDAD DE $PERIOCIDAD_DE_PAGO\r\n";
    $sucess			= false;
}
//--------------------------------- END ELIMINACION
if( date("Y-m-d", strtotime($fecha_primer_abono)) <= date("Y-m-d", strtotime($fecha_ministracion))){
	$msgAlertas .= "ERROR\tLA FECHA DEL PRIMER ABONO($fecha_primer_abono) NO PUEDE SER LA MISMA O MENOR QUE LA FECHA DE MINISTRACION($fecha_ministracion)\r\n";
	$sucess			= false;
}
//----------------------------------  ELIMINAR EL PLAN DE PAGOS ANTERIOR
$xPlan				= new cPlanDePagos();
if($xCred->getNumeroDePlanDePagos() != false ){
	$xPlan->init( $xCred->getNumeroDePlanDePagos() );
	$xPlan->setEliminar();
}
if(MODO_CORRECION == true){
	//my_query("DELETE FROM operaciones_mvtos WHERE socio_afectado=$socio AND docto_afectado=$solicitud AND (tipo_operacion=410 OR tipo_operacion=412 OR tipo_operacion=413)");
}
if($sucess == true){
$FInteres_normal			= new cFormula("interes_normal");
$factor_interes				= $xPlan->getFactorIVA($iva_incluido);
$DatosDePagos				= array();
if( $xCred->initPagosEfectuados() == true){
	$DatosDePagos			= $xCred->getListadoDePagos();
}
//=========================== Corrige el Monto extra de bonificaciones ================================================================
$bonificaciones				= 0;
$xB			= new cBases(7022); //base son bonificaciones
$xB->init();
if( $xB->getIsMember($tipo_monto_extra) == true){
	$msg	.= "$socio\t$solicitud\tLa operacion $tipo_monto_extra es de Bonificaciones\r\n";
	$bonificaciones			= round( ($monto_extra / $PAGOS_AUTORIZADOS), 2);
	$monto_extra			= 0;
}

//=====================================================================================================================================
$total_ahorro				= ($monto_autorizado * $tasa_ahorro);
$parcialidad_capital 		= ($FormaDePago == CREDITO_TIPO_PAGO_INTERES_PERIODICO OR $FormaDePago == CREDITO_TIPO_PAGO_INTERES_COMERCIAL) ? 0 : ($monto_autorizado / $PAGOS_AUTORIZADOS);
$parcialidad_ahorro 		= round( ($total_ahorro / $PAGOS_AUTORIZADOS), 2);
$parcialidad_interes		= 0;
$parcialidad_iva			= 0;
$parcialidad_cargo			= round( ($monto_extra / $PAGOS_AUTORIZADOS), 2);
$saldo_inicial 				= 0;
$saldo_final 				= 0;
$interes_normal 	 	   	= 0;
$interes_iva		  	 	= 0;
//*************************************************************************************************************************************
//-----------------------------------------     CALCULO DE UNA PARCIALIDAD PRESUMIDA    -----------------------------------------------
//*************************************************************************************************************************************
$suma_de_pagos				= 0;
$saldo_insoluto				= $monto_autorizado;
$dias_estimados             = 0;
$estimado_periodico_interes	= 0;
$fecha_de_pago				= $fecha_primer_abono;

	//PAGO NORMALES
	for ($simletras1=1; $simletras1 <= $PAGOS_AUTORIZADOS; $simletras1++){
		$fecha_de_referencia		= ($simletras1 == 1) ? $fecha_primer_abono : $fecha_de_pago;
		$saldo_final 				= $saldo_inicial - $parcialidad_capital;
		$xPlanGen->setTipoDeCreditoEnSistema($xCred->getTipoEnSistema());
		$xPlanGen->setPagosAutorizados($PAGOS_AUTORIZADOS);
		$xPlanGen->setDiasDeAbonoFijo($dia_1_ab, $dia_2_ab, $dia_3_ab);
		$xPlanGen->setTipoDePlanDePago($tipo_de_plan);
		$xPlanGen->setPeriocidadDePago($PERIOCIDAD_DE_PAGO);
		$xPlanGen->setSaldoInicial($saldo_inicial);
		$xPlanGen->setSaldoFinal($saldo_final);
		$fecha_de_pago				= $xPlanGen->getFechaDePago($fecha_de_referencia, $simletras1);
	}
	/*if( $PERIOCIDAD_DE_PAGO == CREDITO_TIPO_PERIOCIDAD_DIARIO){
		$fecha_de_pago			= $xCred->getFechaDeVencimiento();
	}*/
	

$dias_estimados		    		= $xF->setRestarFechas($fecha_de_pago, $fecha_ministracion);
$dias_desviados					= $dias_estimados - ( $PAGOS_AUTORIZADOS * $PERIOCIDAD_DE_PAGO );
$desviacion_total				= 1 + ( ($dias_desviados / ( $PAGOS_AUTORIZADOS * $PERIOCIDAD_DE_PAGO ) ) / 10 );
$desviacion                    	= ( $tipo_de_plan != 99 ) ? 0.013 - ( 0.00013 * $PAGOS_AUTORIZADOS ) : 0;
$estimado_dias_promedio			= ( ( $dias_estimados / $PAGOS_AUTORIZADOS ) * (1 + $desviacion)  );

if ( $tipo_de_calculo == INTERES_POR_SALDO_HISTORICO ){
	$estimado_periodico_interes = ( ($monto_autorizado * $tasa_interes * $dias_estimados ) /  EACP_DIAS_INTERES ) * (1 +  $tasa_iva);
	$parcialidad_presumida      = ( ($monto_autorizado + $total_ahorro + $monto_extra + $estimado_periodico_interes)  / $PAGOS_AUTORIZADOS);
	
} else {
	//Recompocision para el tipo de Pago sobre Saldos Insolutos
	$estimado_periodico_interes = ( ( $tasa_interes /  EACP_DIAS_INTERES ) * $estimado_dias_promedio ) * (1 +  $tasa_iva);
	$parcialidad_presumida      = ( ( $monto_autorizado * $estimado_periodico_interes ) / ( 1 - ( pow( (1 + $estimado_periodico_interes), ($PAGOS_AUTORIZADOS * -1) ) ) ) ) + ( ($total_ahorro + $monto_extra) / $PAGOS_AUTORIZADOS);
	
}
$parcialidad_presumida			= ($parcialidad_presumida * $desviacion_total);
$parcialidad_presumida			= (PLAN_DE_PAGOS_SIN_REDONDEO == true ) ? $parcialidad_presumida : round($parcialidad_presumida, 2);

//======================================   modificar la parcialidad
$total_de_intereses				= 0;
$desviacion_simulada			= 0;
$primera_parcialidad			= 0;
$ultima_parcialidad				= 0;

	switch($FormaDePago){
		case CREDITO_TIPO_PAGO_INTERES_COMERCIAL:
			$parcialidad_presumida	= ($saldo_insoluto * ($tasa_interes * $factor_interes) * $PERIOCIDAD_DE_PAGO) / EACP_DIAS_INTERES;
			
			$msgC .= "$socio\t$solicitud\tINTERES COMERCIAL : Interes = $parcialidad_presumida\r\n";
			break;
		case CREDITO_TIPO_PAGO_INTERES_PERIODICO:
			break;
		
		case CREDITO_TIPO_PAGO_PERIODICO:
			for($simulaciones=1;$simulaciones<=10;$simulaciones++){
				$sumar_dias						= 0;
				
				for ($i=1; $i <= $PAGOS_AUTORIZADOS; $i++){
					$saldo_inicial 			= ($i ==1) ? $monto_autorizado : $saldo_final;
					$fecha_de_referencia 	= ($i ==1) ? $fecha_primer_abono : $fecha_de_pago;
					$xPlanGen->setTipoDeCreditoEnSistema($xCred->getTipoEnSistema());
					$xPlanGen->setPagosAutorizados($PAGOS_AUTORIZADOS);
					$xPlanGen->setDiasDeAbonoFijo($dia_1_ab, $dia_2_ab, $dia_3_ab);
					$xPlanGen->setTipoDePlanDePago($tipo_de_plan);
					$xPlanGen->setPeriocidadDePago($PERIOCIDAD_DE_PAGO);
					$fecha_de_pago			= $xPlanGen->getFechaDePago($fecha_de_referencia, $i);
					// ------------------------------------ Obtiene la Fecha de Pago ----------------------------------------------
					$dias_normales			= ($i == 1) ?  restarfechas($fecha_de_pago, $fecha_ministracion) : restarfechas($fecha_de_pago, $fecha_de_referencia);
					if(PLAN_DE_PAGOS_PLANO == true){ $dias_normales		= $PERIOCIDAD_DE_PAGO;	}
					$saldo_insoluto         = $saldo_inicial;
					
					eval ( $FInteres_normal->getFormula() );
					
					if($PlanMalo == true){ $interes_normal		= $saldo_insoluto * ( $tasa_interes /12 );	}
					
					$interes_simulado		= (PLAN_DE_PAGOS_SIN_REDONDEO == true ) ? $interes_normal : round($interes_normal, 2);
					$iva_simulado 			= (PLAN_DE_PAGOS_SIN_REDONDEO == true ) ? ($interes_normal * $tasa_iva) : round(($interes_normal * $tasa_iva), 2);
					$parcialidad_simulada	= ($parcialidad_presumida - ($interes_simulado + $iva_simulado)); // + $parcialidad_cargo + $parcialidad_ahorro));
					
					$saldo_final 			= $saldo_inicial - $parcialidad_simulada;
					$sumar_dias            	+=  $dias_normales;
					if(MODO_DEBUG == true){
						$msg				.= ($i == 1) ? "PERSONA\tCREDITO\t[SIMULACION/DE]\tINTERES\tIVA\tCAPITAL\tFI\tFF\r\n" : "";
						$msg				.= "$socio\t$solicitud\t[$simulaciones/$i]\t$interes_simulado\t$iva_simulado\t$parcialidad_simulada\t$fecha_de_referencia\t$fecha_de_pago\r\n";
					}
					$xPlanGen->setSaldoInicial($saldo_inicial);
					$xPlanGen->setSaldoFinal($saldo_final);
							
					if($i == $PAGOS_AUTORIZADOS){
						$desviacion_simulada	= ($saldo_final != 0) ? ($saldo_final / $PAGOS_AUTORIZADOS) : 0;
						//echo "<p class='aviso'>$desviacion_simulada	= ($saldo_final != 0) ? ($saldo_final / $PAGOS_AUTORIZADOS)</p>";
						//Verificar db ser años que dura en credito
						if($sumar_dias > 367){
							$factor_annios			= ($sumar_dias / 365); 
							$desviacion_simulada	= ($desviacion_simulada/$factor_annios);
							$msg					.= "DIVIDIR ANIOS $factor_annios por $desviacion_simulada\r\n";
						}
						$desviacion_simulada	= (PLAN_DE_PAGOS_SIN_REDONDEO == true ) ? $desviacion_simulada : round($desviacion_simulada,2);
						$parcialidad_presumida	+=  $desviacion_simulada;
						
						$msg					.= "$socio\t$solicitud\t[$simulaciones/$i]\tLa Parcialidad Presumida es $parcialidad_presumida por $desviacion_simulada Desviados, Saldo Final $saldo_final en dias $sumar_dias\r\n";
					}
				}
			}
		break;
	}

//*****************************************************************************************************************************************
//************************************************  FIN DEL PRECALCULO ********************************************************************
//*****************************************************************************************************************************************
$msg	.= "$socio\t$solicitud\tLa Parcialidad Presumida es $parcialidad_presumida por $PAGOS_AUTORIZADOS pagos\r\n";
$msg	.= "$socio\t$solicitud\tDias estimados son de $dias_estimados \r\n";
$msgM	.= "$socio\t$solicitud\tPeriodo\tDias\tSdoInicia\tSdoFinal\t Capital \tInteres\tIVA\tOtros\tAhorro\tTotal\tFInicial\tFFinal\r\n";
//===================================================================================================================
//-----------------------------------------------------------------------------
$xPlan->initByCredito($solicitud);
$idrecibo					= $xPlan->add($observaciones, $fecha_operacion);

$i 									= 1;
$sumar_dias							= 0;
for ($i=1; $i <= $PAGOS_AUTORIZADOS; $i++){
	$letra							= $i;
	$saldo_inicial 					= ($letra ==1) ? $monto_autorizado : $saldo_final;
	$fecha_de_referencia 			= ($letra ==1) ? $fecha_primer_abono : $fecha_de_pago;
	$saldo_final 					= $saldo_inicial - $parcialidad_capital;
	
	$xPlanGen->setTipoDeCreditoEnSistema($xCred->getTipoEnSistema());
	$xPlanGen->setPagosAutorizados($PAGOS_AUTORIZADOS);	
	$xPlanGen->setDiasDeAbonoFijo($dia_1_ab, $dia_2_ab, $dia_3_ab);
	$xPlanGen->setTipoDePlanDePago($tipo_de_plan);
	$xPlanGen->setPeriocidadDePago($PERIOCIDAD_DE_PAGO);
	
	$fecha_de_pago				= $xPlanGen->getFechaDePago($fecha_de_referencia, $letra);
	// ------------------------------------ Obtiene la Fecha de Pago ----------------------------------------------
	$dias_normales				= ($letra == 1) ?  restarfechas($fecha_de_pago, $fecha_ministracion) : restarfechas($fecha_de_pago, $fecha_de_referencia);
	if($FormaDePago != CREDITO_TIPO_PAGO_INTERES_PERIODICO){ if(PLAN_DE_PAGOS_PLANO == true){ $dias_normales	= $PERIOCIDAD_DE_PAGO; }	}

	$saldo_insoluto         	= $saldo_inicial;
	eval ( $FInteres_normal->getFormula() );
	if($PlanMalo == true){ 
		$interes_normal			= ( ($saldo_insoluto * ( $tasa_interes /12) ) /4);
		$msgC .= "$socio\t$solicitud\tPlan con Errores : Interes = $interes_normal\r\n";
	}
	//TODO: Modifica el INTERES COMERCIAL
	if($FormaDePago == CREDITO_TIPO_PAGO_INTERES_COMERCIAL){
			$LAnterior				= setNoMenorQueCero( $letra );
			$interes_normal			= ($BaseDinamicaCalculo * ($tasa_interes * $factor_interes) * $PERIOCIDAD_DE_PAGO) / EACP_DIAS_INTERES;//$parcialidad_presumida;
			if( isset($DatosDePagos[$LAnterior]) ){
				$abonos				= $DatosDePagos[$LAnterior][SYS_CAPITAL];
				$BaseDinamicaCalculo	-= $abonos;
				if(MODO_DEBUG == true){ $msgC .= "WARN\tPagos FLAT Ajuste de base a $BaseDinamicaCalculo por Abono de $abonos\r\n"; }
				$saldo_inicial		= $saldo_final;
				$saldo_final		= $saldo_inicial - $abonos;
			}			
	}
	$parcialidad_interes    	= (PLAN_DE_PAGOS_SIN_REDONDEO == true ) ? $interes_normal : round($interes_normal, 2);
	$parcialidad_iva 			= (PLAN_DE_PAGOS_SIN_REDONDEO == true ) ? ($interes_normal * $tasa_iva) : round(($interes_normal * $tasa_iva), 2);
	$sumar_dias					+=  $dias_normales;

	if($xCred->getPagosSinCapital($FormaDePago) == true){
		$parcialidad_presumida = ($parcialidad_iva + $interes_normal);
		
		if($letra == $PAGOS_AUTORIZADOS){ $parcialidad_presumida = $monto_autorizado + ($parcialidad_iva + $interes_normal); }
	} else {
		if($FormaDePago == CREDITO_TIPO_PAGO_CAPITAL_FIJO){ 
			$parcialidad_presumida = ($monto_autorizado / $PAGOS_AUTORIZADOS) + ($parcialidad_iva + $interes_normal);
		}
		if($FormaDePago == CREDITO_TIPO_PAGO_FLAT_PARCIAL){
			//TODO: Activar pagos FLAT
			$parcialidad_capital		= ($monto_autorizado / $PAGOS_AUTORIZADOS);
			$parcialidad_interes		= ($OPCION_ANUAL_FLAT == true) ? (($saldo_historico * $PERIOCIDAD_DE_PAGO) * ($tasa_interes * $factor_interes)) / EACP_DIAS_INTERES : $parcialidad_capital * ($tasa_interes * $factor_interes);
			$parcialidad_iva 			= (PLAN_DE_PAGOS_SIN_REDONDEO == true ) ? ($parcialidad_interes * $tasa_iva) : round(($parcialidad_interes * $tasa_iva), 2);
			$parcialidad_presumida		= $parcialidad_capital + $parcialidad_interes + $parcialidad_iva;
			$msgAlertas .= "WARN\tPagos FLAT\r\n";
		}
		if ($parcialidad_presumida < ($parcialidad_iva + $interes_normal - 0.01)) {
			 $msgAlertas .= "ERROR\tEL INTERES($parcialidad_interes) + IVA($parcialidad_iva) NO DEBE SER MENOR A $parcialidad_presumida\r\n";
		}
	}
	$xPlan->setSaldoInicial($saldo_inicial);
	$xPlan->setSaldoFinal($saldo_final);
//----------------------------------------------------------------------------------------------------------------------------
	$total_parcial			= $parcialidad_presumida + $parcialidad_cargo + $parcialidad_ahorro;
	$total_parcial			= getCantidadRendonda($total_parcial, $FormaDeRedondeo);
	if($letra == 1){ $primera_parcialidad	= $total_parcial; }
	//PAGOS FLAT
	
	//OPERACIONES -------------------------------------------------------------------------
	$total_de_intereses		+= $parcialidad_interes;														//Total de Intereses sin afectacion
	//TODO: verificar Pagos sin Capital
	if($xCred->getPagosSinCapital($FormaDePago) == true ){
			$msgPIE					.= "ADD\tInteres de $parcialidad_interes Modificado en base a pagos de $interes_normal_pagado\r\n";
			if($interes_normal_pagado > 0){
				$total_parcial				= $total_parcial - ( $parcialidad_interes + $parcialidad_iva);						//quitar el interes a operar
				if($interes_normal_pagado < $parcialidad_interes){
					$parcialidad_interes	= ($parcialidad_interes - $interes_normal_pagado);
					$interes_normal_pagado	= 0;
					$ultima_parcialidad 	= ($letra-1);													//Regresa a la parcialidad anterior
				} else {
					$interes_normal_pagado	= ($interes_normal_pagado - $parcialidad_interes);
					$interes_normal_pagado	= ($interes_normal_pagado < 0) ? 0 : $interes_normal_pagado;
					$parcialidad_interes	= 0; //setea a cero
					if($interes_normal_pagado == 0){ $ultima_parcialidad = $letra; }
				}
				$parcialidad_iva 			= (PLAN_DE_PAGOS_SIN_REDONDEO == true ) ? ($parcialidad_interes * $tasa_iva) : round(($parcialidad_interes * $tasa_iva), 2);
				$total_parcial				= $total_parcial + ($parcialidad_interes  + $parcialidad_iva);						//Sumar el interes que resta
				$total_parcial				= setNoMenorQueCero($total_parcial);
			}
	}
	
	$xPlan->setMontoOperado($total_parcial);
	$total_parcial			= $xPlan->addMvtoDeInteres($parcialidad_interes, $fecha_de_pago, $letra);
	$total_parcial			= $xPlan->addMvtoOtros($parcialidad_cargo, $fecha_de_pago, $letra, $tipo_monto_extra, true);
	$total_parcial			= $xPlan->addMvtoDeIVA($fecha_de_pago, $letra);
	$total_parcial			= $xPlan->addMvtoDeAhorro($parcialidad_ahorro, $fecha_de_pago, $letra);
//Capital
		$parcialidad_capital	=  $total_parcial;
		$msgPIE					.= "ADD\t$parcialidad_interes\t$parcialidad_cargo\t$parcialidad_ahorro\t$parcialidad_capital\r\n";
		if($parcialidad_capital > 0 ){
			//evaluar el Plan de Pagos
			$monto_capital_de_la_parcialidad	= 0;
			switch ($FormaDePago){
				//SI esCAPITAL + INTERES Periodico
				case CREDITO_TIPO_PAGO_PERIODICO:
					$monto_capital_de_la_parcialidad 	= $total_parcial;
				break;
				case CREDITO_TIPO_PAGO_CAPITAL_FIJO:
					$monto_capital_de_la_parcialidad	= ($monto_autorizado / $PAGOS_AUTORIZADOS);
				break;
				case CREDITO_TIPO_PAGO_FLAT_PARCIAL:
					$monto_capital_de_la_parcialidad	= ($monto_autorizado / $PAGOS_AUTORIZADOS);
				break;
			}
			if($xCred->getPagosSinCapital($FormaDePago) == true){
				if(  $letra == $PAGOS_AUTORIZADOS ){ 
					$monto_capital_de_la_parcialidad = $monto_autorizado;
					
				} else { 
					$monto_capital_de_la_parcialidad = 0; 
				}		
			}
			/**
			 * repara el capital en el caso que sea menor que el saldo final
			 */
			if ($saldo_inicial < $monto_capital_de_la_parcialidad){
				$saldo_final					= 0;
				$monto_capital_de_la_parcialidad 		= $saldo_inicial;
				//$msg	        .= "$socio\t$solicitud\tL-$i\tDE $saldo_inicial a Saldo Final :: $saldo_final || $monto_capital_de_la_parcialidad --- $monto_capital_de_la_parcialidad 		= $saldo_inicial;\r\n";
			} else {
				//forza el fin del plan de pagos
				if ( $letra == $PAGOS_AUTORIZADOS ){ $monto_capital_de_la_parcialidad = $saldo_inicial; }				
				$saldo_final	= $saldo_inicial - $monto_capital_de_la_parcialidad;
				//$msg	        .= "$socio\t$solicitud\tL-$i\tSaldo Inicial $saldo_inicial A Saldo Final $saldo_final | $monto_capital_de_la_parcialidad\r\n";
			}
			//corrige el saldo final
			if ( $saldo_final < 0){ $saldo_final = 0; }
			$xPlan->setSaldoFinal($saldo_final);
			$total_parcial		= $xPlan->addMvtoDeCapital($monto_capital_de_la_parcialidad, $fecha_de_pago, $letra);

			//si el saldo final termina es menor al saldo final
			//el saldo inicial es de la proxima letra
			if ( $xCred->getPagosSinCapital( $FormaDePago) == false){
				if( (($saldo_actual <= $saldo_inicial) AND ( $saldo_final > 0 ))
				     AND  ($estatus != CREDITO_ESTADO_AUTORIZADO AND $estatus != CREDITO_ESTADO_SOLICITADO )
				     AND ($MontoCubierto >0 )
				){
					$LetraActualizada	= ( $monto_capital_de_la_parcialidad <= $MontoCubierto ) ? 0 : ($monto_capital_de_la_parcialidad - $MontoCubierto);
					$mod			= 0;
					//274 - 275 = -1
					if( ($MontoCubierto - $monto_capital_de_la_parcialidad) <(TOLERANCIA_SALDOS*-1) ){
						if($LetraActualizada > TOLERANCIA_SALDOS){
							$mod	= false;
						}
					}
					$LetraActualizada	= ($LetraActualizada < 0) ? 0 : $LetraActualizada;
					$InteresActual		= $mod;
					$AhorroActual		= $mod;
					$ExtraActual		= $mod;
					
					//obtener el monto del capital
					$xPlan->init();
					$xPlan->setActualizarParcialidad($letra, $LetraActualizada, $InteresActual, $AhorroActual, $tipo_monto_extra, $ExtraActual);
					$msgC	        	.= "$socio\t$solicitud\tL-$letra\tLetra Neutralizada " . getFMoney($LetraActualizada) . ", Interes a " . getFMoney($InteresActual) .", Ahorro a $AhorroActual, SI $saldo_inicial, SF ". getFMoney($saldo_final) . ", Amort ". getFMoney($MontoCubierto) . " \r\n";
					$MontoCubierto		-= $monto_capital_de_la_parcialidad;
				}
			} else {
				//Actualizar pago de letra a saldo de capital si existe.
				if($credito_abonado == true){
					$xPlan->init();
					$saldo_letra		= $xCred->getSaldoActual();
					$xPlan->setActualizarParcialidad($letra, $saldo_letra);
				}
			}
		}
		//2015-01-05 Agregar Bonificaciones
		if($bonificaciones != 0){
			$xPlan->addBonificacion($bonificaciones, $fecha_de_pago, $letra, $tipo_monto_extra);
		}
		//===================================================================================================================================
		$TParcial	= $parcialidad_ahorro + $parcialidad_capital + $parcialidad_cargo + $parcialidad_interes + $parcialidad_iva - $bonificaciones;
		$msgM	.=  "$socio\t$solicitud\tPer. $letra\t$dias_normales,$sumar_dias\t" . getFMoney($saldo_inicial) . "\t" . getFMoney($saldo_final) . "\t" . getFMoney($parcialidad_capital) ."";
		$msgM	.=  "\t" . getFMoney($parcialidad_interes) . "\t" . getFMoney($parcialidad_iva) . "\t" . getFMoney($parcialidad_cargo) . "\t" . getFMoney($parcialidad_ahorro) . "\t" . getFMoney($TParcial) . "\t$fecha_de_referencia\t$fecha_de_pago\r\n";
}//end FOR

$fecha_final					= $fecha_de_pago;
$fecha_de_vencimiento			= $fecha_final;
$dias_netos						= restarfechas($fecha_final, $fecha_ministracion);
$dias_normales					= $dias_netos;
$interes_diario					= ($total_de_intereses / $dias_netos);
//===================================================================================================================================
$OProd							= $xCred->getOProductoDeCredito();
$OPer							= $xCred->getOPeriocidad();
$fecha_de_mora					= $xF->setSumarDias($OProd->getDiasTolerados()+1, $fecha_de_vencimiento);
$vencimiento_dinamico			= $xF->setSumarDias($OPer->getDiasToleradosEnVencer(), $fecha_de_mora);

$arrUpdate	= array (
			"plazo_en_dias" => $dias_netos,
			"dias_autorizados" => $dias_netos,
			"fecha_vencimiento" => $fecha_de_vencimiento,
			"monto_parcialidad" => $primera_parcialidad,
			"contrato_corriente_relacionado" => $cuenta_captacion,
			"tipo_de_pago" => $FormaDePago,
			"fecha_ministracion"=> $fecha_ministracion,
			"interes_diario"	=> $interes_diario,
			"fecha_mora" => $fecha_de_mora,
			"fecha_vencimiento_dinamico" => $vencimiento_dinamico,
			"fecha_de_primer_pago" => $fecha_primer_abono
			);
if(($xCred->getSaldoActual() == $xCred->getMontoAutorizado() ) OR $xCred->getEsAfectable() == false) {	$arrUpdate["ultimo_periodo_afectado"]	= SYS_CERO; }
//Pagos de solo interes
if($xCred->getPagosSinCapital() == true){ 	$arrUpdate["ultimo_periodo_afectado"]	= $ultima_parcialidad; }
	$xCred->setUpdate($arrUpdate);
// -------------------------------------------- Actualiza el Saldo del Recibo
	$xCred->init();
	$xFRM->addHTML($xCred->getFicha(true, "", false, true));

	$xFRM->addHTML($xPlan->getFicha());

	$sqlparc = "SELECT periodo_socio AS 'parcialidad', MAX(fecha_afectacion) AS 'fecha_de_pago', SUM((afectacion_real * valor_afectacion)) AS 'total_parcialidad',
					 MAX(saldo_anterior) AS 'saldo_anterior_', MIN(saldo_actual) AS 'saldo_actual_' FROM operaciones_mvtos
				WHERE recibo_afectado=$idrecibo GROUP BY periodo_socio ORDER BY periodo_socio";

	$cTMvtos	= new cTabla($sqlparc);
	$cTMvtos->setWidth();
	$xFRM->addHTML( $cTMvtos->Show());

	$urctr 		= $xCred->getPathDelContrato();
	$urlsend 	= $DProducto->getPathPagare($solicitud);

	$xFRM->addAviso("Dias Totales: $dias_netos -- Vence: $fecha_de_vencimiento");
	$xFRM->addToolbar($xBtn->getBasic( $xFRM->lang("imprimir", "plan de pagos"), "jsImprimirPlanPagos($idrecibo)", "lista", "cm1", false ));
	$xFRM->addToolbar( $xBtn->getBasic( $xFRM->lang("imprimir", "orden de desembolso"), "jsImprimirOrdenDesembolso()", "lista", "cm2",false ));
	$xFRM->addToolbar( $xBtn->getBasic( $xFRM->lang(array("imprimir", "CONTRATO de", "credito")), "jsImprimirContrato()", "lista", "cm3",false ));
	$xFRM->addToolbar( $xBtn->getBasic( $xFRM->lang("imprimir", "recibo"), "jsImprimirReciboDePrestamo()", "lista", "cm4",false ));
	$xFRM->addToolbar( $xBtn->getBasic( $xFRM->lang("imprimir", "mandato"), "jsImprimirMandato()", "lista", "cm5",false ));
	$xFRM->addToolbar( $xBtn->getBasic( $xFRM->lang("imprimir", "pagare"), "jsImprimirPagare()", "lista", "cm6",false ));
	$xFRM->addHElem("<div class='tx4'><label for='idNoAvales'>No Mostrar Avales</label><input name=\"noAvales\" id=\"idNoAvales\" type=\"checkbox\" onchange=\"setNoAvales()\" /></div>");
}

//Graba los Mensages del LOG y cierra el Archivo
$msg	.= $xPlan->getMessages();
$msg	.= $msgM;
$msg	.= $msgC;
$msg	.= $msgPIE;

if ( MODO_DEBUG == true){ $xLog->setWrite($msg); $xLog->setClose(); $xFRM->addToolbar( $xLog->getLinkDownload("Archivo de Eventos", "") );	}
	
}
$xFRM->addAviso($msgAlertas);
echo $xFRM->get();

?>
</body>
<script >
var siAvales	= "si";
var vCredito	= <?php echo $solicitud; ?>;
var urlPagare	= "<?php echo $urlsend; ?>";
var urlContrato	= "<?php echo $urctr; ?>";
var Wo			= new Gen();
var Wc			= new CredGen();
function setNoAvales(){	siAvales	= (document.getElementById("idNoAvales").checked) ? "no" : "si";}
function jsImprimirPlanPagos(idrecibo){ Wc.getImprimirPlanPagos(idrecibo, siAvales); }
function jsImprimirPagare() { Wo.w({ url : urlPagare }); }
function jsImprimirMandato(){ Wc.getImprimirMandato(vCredito);}
function jsImprimirContrato(){ Wo.w({ url : urlContrato }); }
function jsImprimirOrdenDesembolso(){ Wc.getImprimirOrdenDeDesembolso(vCredito);}
function jsImprimirReciboDePrestamo() { Wo.w({ url : "../rpt_formatos/recibo_de_prestamo.pre.rpt.php?credito=" + vCredito }); }
</script>
</html>