<?php
/**
 * Solicitud de Creditos, forma de captura
 * @author Balam Gonzalez Luis Humberto
 * @version 1.50
 * @package creditos
 * @subpackage forms
 * 		22/07/2008	Funciones mejoradas de riesgo
 * 					Implementacion de php doc
 */
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
$xHP		= new cHPage("Creditos.- Solicitudes");

$oficial 	= elusuario($iduser);


$xHP->setTitle( $xHP->lang( array("solicitud", "de", "credito") ) );
//-------------------------------------------------------------
$jxc		= new TinyAjax();
function calcula_vencimientos($idcuenta){ }
function jsaGetLetrasByNumero($monto=0){
	return convertirletras($monto);
}
function jsaValidarMontoDeCredito($socio, $convenio, $monto){
	$msg			= "";
	$xT			= new cTipos();
	$xSoc 			= new cSocio($socio);
	
	$xSoc->init();
	$datos			= array("tipo_de_producto" => $convenio);
	$monto_maximo		= $xSoc->setPrevalidarCredito($datos, true);
	
	$msg			.= $xSoc->getMessages(OUT_HTML); //"captado $saldo_de_captacion, creditos $saldos_de_creditos, maximo $monto_maximo";
	//END
	if( $monto > $monto_maximo ){
		$monto 		= $monto_maximo;
	}
	$tab 			= new TinyAjaxBehavior();
	$tab -> add(TabSetValue::getBehavior('idmontosol', $monto));
	$tab -> add(TabSetValue::getBehavior('idalertas', $msg));
	return $tab->getString();
}
function jsaGetNumeroDeCredito($idsocio){
	$xSoc		= new cSocio($idsocio);
	$xSoc->init();
	$solicitud	= $xSoc->getIDNuevoDocto(iDE_CREDITO);
		
	$tab 		= new TinyAjaxBehavior();
		
	$tab -> add(TabSetValue::getBehavior('iidsolicitud', $solicitud));
	//tipo de credito y preferencia e pago
	//Mayo/2014
	$xEmp		= new cEmpresas($xSoc->getClaveDeEmpresa()); $xEmp->init();
	$tab -> add(TabSetValue::getBehavior('idtipoconvenio', $xEmp->getProductoPref()));
	$tab -> add(TabSetValue::getBehavior('idperiocidadpagos', $xEmp->getPeriocidadPref()));
	
	return $tab->getString();
}

function js_vcode(){
	return  "<script language=\"javascript\">function js_validar_credito(){	}</script>";

}
function validar_fecha_venc($iddia, $idmes, $idanno, $periocidad){
	//return "$iddia $idmes $idanno $periocidad ";
}
function jsaPrevalidarCredito($socio, $solicitud, $numpagos, $periocidad, $convenio, $contrato, $fechaMin, $fechaVenc, $monto){
		$clave 			= md5($socio . $solicitud . ROTTER_KEY . date("Ymd") );
		$out 			= false;
		$msg 			= "";
		$ctrl			= "";
		$xHO			= new cHObject();
		$xBtn			= new cHButton();
		$arrDatos		= array(
							"numero_de_solicitud" => $solicitud,
							"periocidad_de_pago" => $periocidad,
							"tipo_de_producto" => $convenio,
							"numero_de_pagos" => $numpagos,
							"contrato_corriente_relacionado" => $contrato,
							"fecha_de_ministracion" => $fechaMin,
							"fecha_de_vencimiento" => $fechaVenc
						);
		if( $socio == DEFAULT_SOCIO OR $socio == 0 ){
			$msg	.= "ERROR\t$socio\tClave de Persona no Valido\r\n";
			$out	= false;
		} else {
			if($solicitud == DEFAULT_CREDITO OR $solicitud == 0){
				$msg	.= "ERROR\t$socio\t$solicitud\tNumero de Credito no Valido\r\n";
				$out	= false;				
			} else {
				//Valorar Numero de Creditos por Socio
				$xSoc			= new cSocio($socio);
				if( $xSoc->existe($socio) == false ){
					$msg	.= "ERROR\t$socio\tEl Socio No Existe\r\n";
					$out	= false;					
				} else {
				$xSoc->init();
					$out	= $xSoc->setPrevalidarCredito($arrDatos);
					$msg	.= $xSoc->getMessages();
				}
			}
		}
		
		
		if($out == true){
			$msg	.= "OK\tEL CREDITO HA SIDO VALIDADO POR EL SISTEMA - CUMPLE LOS REQUISITOS\r\n";
			$ctrl	.= $xBtn->getBasic("TR.guardar credito", "jsFormularioValidado('$clave')", "guardar", "idvalidarok");
			$ctrl	.= $xBtn->getBasic("TR.validar nuevamente", "jsPrevalidarCredito()", "checar", "idnuevavalidacion");
		} else {
			//$ctrl = "<input type=\"button\" name=\"cmdSubmit\" onclick=\"jsPrevalidarCredito();\" value=\"VALIDAR CREDITO NUEVAMENTE\" />";
			$ctrl	.= $xBtn->getBasic("TR.validar nuevamente", "jsPrevalidarCredito()", "checar", "idnuevavalidacion");
		}
		$msg	= $xHO->Out($msg, OUT_HTML);
		
		$svalidate = "$msg $ctrl";
	return $svalidate;			
}

function jsRiesgoDelCredito($socio){
	$xhtml	= "";

	if ( isset($socio) ){
		$xSoc 	= new cSocio($socio);
		$xhtml	.= "<fieldset><legend>DATOS  DE ENDEUDAMIENTO</legend>";
		$xhtml	.= $xSoc->getRiesgoComunPorNucleoFamiliar(true);
		$xhtml	.= $xSoc->getRiesgoComunPorAvales(true);
		$xhtml	.= "</fieldset>";
	}
	return $xhtml;
}

function getNombre_FechaDeMinistracion($dia, $mes, $anno){
	$d		= date ("Y-m-d", strtotime($anno . "-" . $mes . "-" . $dia) );
	$xFec	= new cFecha(0, $d );
	return $xFec->getDayName();
}
function jsaCargarDatosDeConvenio($convenio){
	
	$OConv		= new cProductoDeCredito($convenio); $OConv->init();
	$tab 		= new TinyAjaxBehavior();
	$tab -> add(TabSetValue::getBehavior('idnumpagos', $OConv->getNumeroPagosPreferente() ));
	if($OConv->getEsProductoDeNomina() == false){	//false por	que lo determina la empresa	
		$tab -> add(TabSetValue::getBehavior('idperiocidadpagos', $OConv->getPeriocidadPrefente() ));
	}
	//$tab -> add(TabSetValue::getBehavior('idnumpagos', $OConv->obj()->pagos_maximo()->v()));
	return $tab->getString();
		
}
function jsCheckCreditos($persona){
	
}
$jxc ->exportFunction('jsaPrevalidarCredito', array('idsocio', 'iidsolicitud', 'idnumpagos',
			'idperiocidadpagos', 'idtipoconvenio', 'idcontratocorriente',
			'idFechaMinistracion', 'idFechaVencimiento', 'idmontosol'), "#idfrmval");

$jxc ->exportFunction('jsaGetNumeroDeCredito', array( 'idsocio' ));
$jxc ->exportFunction('jsaValidarMontoDeCredito', array('idsocio', 'idtipoconvenio', 'idmontosol') );
$jxc ->exportFunction('jsaGetLetrasByNumero', array('idmontosol'), "#idmontoletras");
$jxc ->exportFunction('jsRiesgoDelCredito', array("idsocio"), "#informacion" );
$jxc ->exportFunction('getNombre_FechaDeMinistracion', array("ideldia1", "idelmes1", "idelanno1"), "#swFechaMin");

$jxc ->exportFunction('jsaCargarDatosDeConvenio', array("idtipoconvenio") );

$jxc ->process();


echo $xHP->getHeader(true);

$xFRM		= new cHForm("frm", "./");
$xBtn		= new cHButton();
$xTxt		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();

$xFRM->addJsBasico();
$xFRM->addPersonaBasico();

?>
<body>
<fieldset>
	<legend><?php echo $xHP->getTitle(); ?></legend>
		<form name="frmsolicitud" id="frmsolicitud" action="clssolicitudcredito_v102.php" method="POST" id="idfrmsolicitud">

		<input type="hidden" name="cFechaMinistracion" id="idFechaMinistracion" />
		<input type="hidden" name="cFechaVencimiento" id="idFechaVencimiento" />

<table>
	<?php if(PERMITIR_EXTEMPORANEO == true){ echo CTRL_FECHA_EXTEMPORANEA;	} ?>
	<tr>
		<td><?php  echo $xL->getT("TR.Codigo de Persona"); ?></td>
		<td><input type="number" name="idsocio" id="idsocio" value="" onchange="envsoc(); jsaGetNumeroDeCredito();jsRiesgoDelCredito()"
			class='mny' size='12' /><?php echo CTRL_GOSOCIO; ?></td>
		<td colspan="2"><input disabled name="nombresocio" type="text" size="40" id='idnombresocio' /></td>
	</tr>
	<tr>
		<td><?php echo $xHP->lang("numero de", "solicitud"); ?></td>
		<td><input type="number" name="idsolicitud" id="iidsolicitud" value="0" onblur="jsaGetNumeroDeCredito(); " onfocus="jsaGetNumeroDeCredito();" class='mny' size='12' /></td>
	</tr>
	<tr>
		<td><?php  echo $xL->getT("TR.Producto"); ?></td>
		<td colspan="2"><?php
			$sqlSc		= "SELECT
						`creditos_tipoconvenio`.`idcreditos_tipoconvenio`,
						`creditos_tipoconvenio`.`descripcion_tipoconvenio`
					FROM
						`creditos_tipoconvenio` `creditos_tipoconvenio`
						 WHERE estatus!='baja' ";
			$xTC 		= new cSelect("tipoconvenio", "idtipoconvenio", $sqlSc);
			$xTC->setOptionSelect(DEFAULT_TIPO_CONVENIO);
			$xTC->addEvent("onblur", "jsaCargarDatosDeConvenio()");
			$xTC->SetEsSql();
			$xTC->show(false);

		?></td>
	</tr>
	<tr>
		<td><?php echo $xHP->lang(array("periocidad", "de", "pagos")); ?></td>
		<td><?php
			$sqlSc		= "SELECT
						`creditos_periocidadpagos`.`idcreditos_periocidadpagos`,
						`creditos_periocidadpagos`.`descripcion_periocidadpagos`
					FROM
						`creditos_periocidadpagos` `creditos_periocidadpagos`
					WHERE
						(`creditos_periocidadpagos`.`idcreditos_periocidadpagos` !=99) ";

			$xTP 		= new cSelect("periocidadpagos", "idperiocidadpagos", $sqlSc);
			$xTP->addEvent("onblur", "sw_fecha_venc");
			$xTP->setOptionSelect(DEFAULT_PERIOCIDAD_PAGO);
			$xTP->SetEsSql();
			$xTP->show(false);

		?></td>
		<td><?php echo $xHP->lang("forma de", "pago"); ?></td>
		<td id="idfp"><?php
			$sqlSc		= "SELECT *
					FROM
						`creditos_tipo_de_pago` `creditos_tipo_de_pago`
					WHERE
						(`creditos_tipo_de_pago`.`idcreditos_tipo_de_pago` !=99) ";

			$xTP 		= new cSelect("tipo_de_pago", "idtipo_de_pago", $sqlSc);
			//$xTP->setOptionSelect(DEFAULT_PERIOCIDAD_PAGO);
			$xTP->SetEsSql();
			$xTP->show(false);

		?></td>
	</tr>
	<tr>
		<td><?php echo $xHP->lang("numero de", "pagos"); ?></td>
		<td><input name="numpagos" type="number" value="2" id="idnumpagos" onblur="jsaValidarMontoDeCredito()" class="mny" size="4"/></td>
		<td><?php echo $xHP->lang("fecha de", "vencimiento"); ?></td>
		<td id="idthvcto"><?php
			$xF0 = new cFecha(0);
			$xF0->set(  $xF0->setSumarDias(EACP_DIAS_MINIMO_CREDITO)  );
			$xF0->show(false, "OPERATIVO");
		?></td>
	</tr>
	<tr>
		<td><?php echo $xHP->lang("monto", "solicitado"); ?></td>
		<td><input name="montosol" type ="number" value = "0.00" id="idmontosol"
			   onchange="jsValidarMonto();jsaGetLetrasByNumero();" onblur=" jsaGetLetrasByNumero();" class="mny"/></td>
		<th colspan="2"><div id="idmontoletras" ></div></th>
	</tr>
	<tr>
		<td><?php echo $xHP->lang(array("destino", "del", "credito")); ?></td>
		<td><?php
			$sqlSc		= "SELECT `creditos_destinos`.`idcreditos_destinos`, CONCAT(`creditos_destinos`.`descripcion_destinos`,'-',(`creditos_destinos`.`tasa_de_iva`*100), '%') AS 'destino' 
					FROM `creditos_destinos` `creditos_destinos`  WHERE (`creditos_destinos`.`idcreditos_destinos` !=99) ";
			$xTD 		= new cSelect("destinocredito", "iddestinocredito", $sqlSc);
			//$xTP->setOptionSelect(DEFAULT_PERIOCIDAD_PAGO);
			$xTD->SetEsSql();
			$xTD->show(false);
		//ctrl_select("creditos_destinos", " name='destinocredito' id='iddestinocredito' ", " WHERE idcreditos_destinos<>99 ");
		?></td>
		<td><?php echo $xHP->lang(array("descripcion", "del", "destino")); ?></td>
		<td><input name='ampliaciondestino' type='text' value='' size="35" maxlength="150" id='idampliaciondestino' /></td>
	</tr>
	<tr>
	<?php if (MODULO_CAPTACION_ACTIVADO == true){ ?>
		<td><?php echo $xHP->lang("contrato de", "ahorro"); ?></td>
		<td><input type="number" class="mny" name="idcuenta" id="idcontratocorriente" value="<?php echo CTA_GLOBAL_CORRIENTE; ?>" size="12" maxlength="18" />
		<?php echo CTRL_GOCUENTAS_A; ?></td>
		<?php } ?>
		<td><?php echo $xHP->lang("observaciones"); ?></td>
		<td><input name='observaciones' type='text' value='' size="35" maxlength="100" id='idobservaciones' /></td>
	</tr>

	<tr>
		<td><?php  echo $xL->getT("TR.Fecha de Ministracion") ?></td>
		<th><?php
			$xF = new cFecha(1);
			$xF->addEvent("onchange", "setEvaluateDate");
			$xF->show(false, "OPERATIVO");

		?></th>
		<th class='warn' id="swFechaMin" colspan='2'></th>
	</tr>
</table>
<p class="aviso" id='idfrmval'><input type="button" name="cmdSubmit" onclick="jsPrevalidarCredito();" value="<?php  echo $xL->getT("TR.VALIDAR CREDITO"); ?>"></p>
<div id="informacion"></div>
<input type="hidden" id="idalertas" />
</form>
<!-- Datos del Socio, si existe -->
<div id="vcodev"></div>
</fieldset>
</body>
<?php
$xJs	= new jsBasicForm("frmsolicitud", iDE_CREDITO, ".");
$xJs->setIncludeCreditos(false);
$xJs->setIncludeCaptacion(true);
$xJs->setIncludeJQuery();

$xJs->show();

$jxc ->drawJavaScript(false, true);


?>
<script>
var wFrm 	= document.frmsolicitud;
var mMonto	= 0;
function jsValidarMonto(){
	mMonto	= flotante($("#idmontosol").val());
	jsaValidarMontoDeCredito();
	setTimeout("jsNotificarValidacion()", 1000);
}
function jsNotificarValidacion() {
	var nMonto	= flotante($("#idmontosol").val());
	if (nMonto < mMonto) {
		alert("El Monto capturado " + mMonto + "  ha sido Cambiado a " + nMonto + "\npor el sistema en base a Politicas de la Institucion.\Consulte a su Administrador." );
	}
	jsaGetLetrasByNumero();
}
function setEvaluateDate(){ getNombre_FechaDeMinistracion(); }

function sw_fecha_venc(){
	var es_final_plazo 					= wFrm.idperiocidadpagos.value;
	//&& es_final_plazo != CREDITO_TIPO_PERIOCIDAD_DIARIO
	if(es_final_plazo != CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO ){
		ocultalo("idthvcto");
		wFrm.idtipo_de_pago.value 		= 2;
		muestralo("idfp");
		muestralo("idnumpagos");
		
	} else {
		muestralo("idthvcto");
		ocultalo("idfp");
		wFrm.idtipo_de_pago.value 		= 1;
		wFrm.numpagos.value 			= 1;
		ocultalo("idnumpagos");
		wFrm.eldia0.focus();
	}
}
function jsFormularioValidado(i){
	if(!i){
		i=0;
	}
	wFrm.action = "./clssolicitudcredito_v102.php?s=" + i;
	wFrm.submit();
}

function jsPrevalidarCredito(){
	//llenar fecha de ministracion
	//lenar fecha de vencimiento
	var mFechaVencimiento	= getVal("idelanno0") + "-" + getVal("idelmes0") + "-" + getVal("ideldia0");
	var mFechaMinistracion	= getVal("idelanno1") + "-" + getVal("idelmes1") + "-" + getVal("ideldia1");
	setVal("idFechaVencimiento", mFechaVencimiento);
	setVal("idFechaMinistracion", mFechaMinistracion);
	jsaPrevalidarCredito();
}

</script>
</html>
