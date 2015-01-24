<?php
/**
 * Partes Relacionadas del Socio
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package common
 * @subpackage forms
 * 		22/07/2008	Funciones mejoradas de Datos heredados
 * 		22/7/8		Funciones mejoradas de muestra de Datos en tabla
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
$xHP			= new cHPage("Referencias directas");

$jxc 			= new TinyAjax();
function jsaGetCurpByIdSocio($socio){
	$curp			= "POR_REGISTRAR";
	$xSoc 			= new cSocio($socio);
	$xSoc->init();
	$DSoc	= $xSoc->getDatosInArray();
	$curp			= $DSoc["curp"];
	$tab = new TinyAjaxBehavior();
	$tab -> add(TabSetvalue::getBehavior("idCurp", $curp));
	return $tab -> getString();
}
function JsaGetDatosHeredados($socio){
	$xSoc 			= new cSocio($socio);
	$xSoc->init();
	$sucess			= true;
	$telefono		= "";
	$domicilio		= "";
	$telefonomovil	= "";
	$nombre			= "";
	$appPaterno		= "";
	$appMaterno		= "";
	$NDia			= "";
	$NMes			= "";
	$NAnno			= "";
	$actividad		= "";

/**
 * Si el Numero de Socio es menor a 5 digitos
 */
	//if ( strlen($curp) >= 7){
	$DDom 			= $xSoc->getDatosDomicilio();
	$telefono		= $DDom["telefono_residencial"];
	$telefonomovil		= $DDom["telefono_movil"];
	$nombre			= $xSoc->getNombre();
	$appPaterno		= $xSoc->getApellidoPaterno();
	$appMaterno		= $xSoc->getApellidoMaterno();
	$curp			= $xSoc->getCURP();
	$FNacimiento		= $xSoc->getFechaDeNacimiento();
	$NDia			= date("d", strtotime($FNacimiento) );
	$NMes			= date("n", strtotime($FNacimiento) );
	$NAnno			= date("Y", strtotime($FNacimiento) );
	//$num			= $DDom["Resultado"];
	$DOcup			= $xSoc->getDatosActividadEconomica();
	//$domicilio		= $xSoc->getDomicilio();
	$actividad		= $DOcup["puesto"];
	$socio			= $xSoc->getCodigo();

	//if ($num == 1){ $sucess = true; }
	//}
	//idelmes0 idelanno0 ideldia0
	$tab = new TinyAjaxBehavior();
	if ($sucess == true ){
		$tab->add(TabSetvalue::getBehavior("idTFijo", $telefono));
		//$tab->add(TabSetvalue::getBehavior("idDomicilio", $domicilio));
		$tab->add(TabSetvalue::getBehavior("idTMovil", $telefonomovil));

		$tab->add(TabSetvalue::getBehavior("idNombres", $nombre));
		$tab->add(TabSetvalue::getBehavior("idApPaterno", $appPaterno));
		$tab->add(TabSetvalue::getBehavior("idApMaterno", $appMaterno));
		$tab->add(TabSetvalue::getBehavior('idCurp', $curp));

		$tab->add(TabSetvalue::getBehavior('ideldia0', $NDia));
		$tab->add(TabSetvalue::getBehavior('idelmes0', $NMes));
		$tab->add(TabSetvalue::getBehavior('idelanno0', $NAnno));
		$tab->add(TabSetvalue::getBehavior("idOcupacion", $actividad));
		//$tab->add(TabSetvalue::getBehavior("idNumeroSocio", $socio));
		$tab->add(TabSetvalue::getBehavior("idNumeroSocio", $socio));
		//-    
		
		$tab->add(TabSetvalue::getBehavior("idcolonia", $DDom["colonia"]));
		$tab->add(TabSetvalue::getBehavior("idcalle", $DDom["calle"]));
		$tab->add(TabSetvalue::getBehavior("idnumero", $DDom["numero_exterior"]));
		$tab->add(TabSetvalue::getBehavior("idreferencia", $DDom["referencia"]));
		$tab->add(TabSetvalue::getBehavior("idcodigopostal", $DDom["codigo_postal"]));
		
	}
	//$tab -> add(TabSetvalue::getBehavior('idObservaciones', $xSoc->getMessages() ));

	return $tab -> getString();
	//}
}
function JSsetDomicilioMismo($socio, $HDomicilio){
	$HDomicilio	= strtoupper($HDomicilio);
	
	if($HDomicilio == "MISMO"){
	$xSoc 			= new cSocio($socio);
	$xSoc->init();
	
	
	$DDom 			= $xSoc->getDatosDomicilio(99);
	$domicilio 		= $xSoc->getDomicilio();
	$telefono 		= $DDom["telefono_residencial"];
	$telefonomovil 		= $DDom["telefono_movil"];
			$tab = new TinyAjaxBehavior();
			$tab -> add(TabSetValue::getBehavior('idTFijo', $telefono));
			$tab -> add(TabSetValue::getBehavior('idTMovil', $telefonomovil));
			$tab -> add(TabSetValue::getBehavior('idDomicilio', $domicilio));
			return $tab -> getString();
	}
}
$jxc ->exportFunction('JSsetDomicilioMismo', array('idSocioRelacionado', 'idcalle'));
$jxc ->exportFunction('JsaGetDatosHeredados', array('idNumeroSocio'));
$jxc ->exportFunction('jsaGetCurpByIdSocio', array('idNumeroSocio'));

$jxc ->process();

$idsocio 		= parametro("socio");
$docto			= parametro("d",  DEFAULT_CREDITO);
$relacionado	= parametro("r",  DEFAULT_SOCIO);
$msg			= parametro("msg");



if($msg == "OK"){
	$msg	= $xHP->lang(MSG_READY_SAVE);
}

/* verifica si el socio o datos son validos */
$arrD	= array();
if ( $relacionado == DEFAULT_SOCIO ){
	$arrD	= array(
		"idsocios_relaciones"	=>	0,
		"socio_relacionado"		=>	$idsocio,
		"credito_relacionado"	=>	DEFAULT_CREDITO,
		"tipo_relacion"			=>	11,
		"numero_socio"			=>	DEFAULT_SOCIO,
		"nombres"				=>	'',
		"apellido_paterno"		=>	'',
		"apellido_materno"		=>	'',
		"domicilio_completo"	=>	'mismo',
		"telefono_residencia"	=>	'0',
		"telefono_movil"		=>	'0',
		"fecha_nacimiento"		=>	fechasys(),
		"monto_relacionado"		=>	0,
		"porcentaje_relacionado"	=>	1,
		"curp"					=>	'POR_REGISTRAR',
		"observaciones"			=>	'',
		"consanguinidad"		=>	3,
		"estatus"				=>	0,
		"dependiente"			=>	1,
		"ocupacion"				=>	'POR_REGISTRAR',
		"calificacion_del_referente"	=>	0,
		"codigopostal" 			=> 0
	);
} else {
	$xSoc	= new cSocio($idsocio);
	$arrD	= $xSoc->getDatosRelacionInArray($relacionado);
}
//Parche
$arrD["calle"]			= "";
$arrD["numero"]			= "";
$arrD["colonia"]		= "";
$arrD["codigopostal"]		= "";

//$xH	= new cHTMLObject("Partes Relacionadas de Socios");
//$xH->setHeaders();

$xHP->setTitle(PERSONAS_TITULO_PARTES . ".-" . getNombreSocio($idsocio));

$xHP->addJsFile("../jsrsClient.js");
$xHP->addJsFile("../js/jquery/jquery.js");
$xHP->addJsFile("../js/jquery/jquery.qtip.min.js");
$xHP->addJsFile("../js/curp.js");
$xHP->addJsFile("../js/config.js.php");
$xHP->addJsFile("../js/general.js");
$xHP->addCSS("../css/jquery.qtip.css");

echo $xHP->getHeader();

$jxc ->drawJavaScript(false, true);
?>
<body onload="initComponents()">
<fieldset>
<legend><script> document.write(document.title); </script></legend>
<form name="frmreferencias" id="idfrmreferencias"  action="referencias.directas.cls.php?idsocio=<?php echo $_GET["socio"];  ?>" method="POST">
	<table>
	<tr>
		<td>Tipo de Relaci&oacute;n</td>
			<td>
			<?php
				$sqlCT	= "SELECT
						`socios_relacionestipos`.`idsocios_relacionestipos`,
						`socios_relacionestipos`.`descripcion_relacionestipos`,
						`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` 
					FROM
						`socios_relacionestipos` `socios_relacionestipos` 
							INNER JOIN `eacp_config_bases_de_integracion_miembros` 
							`eacp_config_bases_de_integracion_miembros` 
							ON `socios_relacionestipos`.`idsocios_relacionestipos` = 
							`eacp_config_bases_de_integracion_miembros`.`miembro` 
					WHERE
						(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` =5005) 
					ORDER BY
						`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`";
				$xCT	= new cSelect("relacion", "idTRelacion", $sqlCT);
				$xCT->setEsSql();
				$xCT->setOptionSelect($arrD["tipo_relacion"]);
				$xCT->show(false);
		
			?>
			</td>
		<td>Parentesco C/El socio</td>
		<td><?php
				$sqlCT	= "socios_consanguinidad";
				$xCs	= new cSelect("consan", "idTConsanguinidad", $sqlCT);
				//$xCs->setEsSql();
				$xCs->setOptionSelect($arrD["consanguinidad"]);
				$xCs->show(false);
			?></td>
	</tr>
	<tr>

		<td>Nombre(s)</td>
		<td ><input name="nombres" type="text"
				       onkeyup="getListaSocios(this)"
				       id="idNombres" value="<?php echo $arrD["nombres"]; ?>" /></td>
		<td>Apellido Paterno</td>
		<td><input name="appaterno" type="text" id="idApPaterno"
			   onkeyup="getListaSocios(this)"
			   value="<?php echo $arrD["apellido_paterno"]; ?>" /></td>
	</tr>
	<tr>
		<td>Apellido Materno</td>
		<td><input name="apmaterno" type="text" id="idApMaterno"
			   onkeyup="getListaSocios(this)"
			   value="<?php echo $arrD["apellido_materno"]; ?>" /></td>
		<td>Codigo Postal</td>
		<td><input name="idcodigopostal" type="number" id="idcodigopostal" value="<?php echo $arrD["codigopostal"]; ?>" /></td>
	</tr>
	<tr>
		<td>Calle</td>
		<td><input name="idcalle" type="text" id="idcalle" value="<?php echo $arrD["calle"]; ?>" /></td>
		<td>N&uacute;mero</td>
		<td><input name="idnumero" type="text" id="idnumero" value="<?php echo $arrD["numero"]; ?>" /></td>
	</tr>
	<tr>
		<td>Colonia</td>
		<td><input name="idcolonia" type="text" id="idcolonia" value="<?php echo $arrD["colonia"]; ?>" /></td>
		<td>Localidad</td>
		<th><span id="nombrelocalidad"></span></th>
	</tr>
	<tr> <!--idcolonia idcalle idnumero idreferencia idcodigopostal  -->
		<td>Referencia</td>
		<td colspan="3"><input name="idreferencia" type="text" size="50" 
						id="idreferencia" maxlength='100' /></td>
	</tr>
	<tr>
		<td>Telefono Residencial</td>
		<td><input type="text" name="tfijo" value="<?php echo $arrD["telefono_residencia"]; ?>" id="idTFijo" class="mny" size='10' maxlength='18' /></td>
		<td>Telefono Mov&iacute;l</td>
		<td><input type="text" name="tmovil" value="<?php echo $arrD["telefono_movil"]; ?>" id="idTMovil" class="mny" size='10' maxlength='18' /></td>
	</tr>
	<tr>
		<td>Fecha de Nacimiento</td>
		<td>
		<?php
		$xF	= new cFecha(0, $arrD["fecha_nacimiento"]);
		$xF->show(false, "NACIMIENTO");
		?></td>
		<td>C.U.R.P.</td>
		<td><input type="text" name="curp" id="idCurp" size='15' value="<?php echo $arrD["curp"]; ?>"/></td>
	</tr>
	<tr>
		<td>Ocupaci&oacute;n</td>
		<td colspan="1"><input name="ocupacion" type="text" maxlength="50" size="25" id="idOcupacion" value="<?php echo $arrD["ocupacion"]; ?>" /></td>
		<td>Porcentaje Rel.</td>
		<td><input name="porrel" type="text" id="idporcentaje" maxlength="8" class="mny" size='6' value="<?php echo $arrD["porcentaje_relacionado"]; ?>" /></td>		

	</tr>
	<tr>
		<td>Observaciones</td>
		<td colspan='3'><input name="observaciones" type="text" maxlength="100" size="35" value="<?php echo $arrD["observaciones"]; ?>" /></td>

	</tr>
	<tr>
		<th colspan="4"><input type="button" onclick="goSubmitForm()" name="enviar" value="Guardar y Limpiar Formulario" /></th>
	</tr>

	</table>

		<input name="montorel" type="hidden" value="<?php echo $arrD["monto_relacionado"]; ?>" />
		<input name="SocioRelacionado" type="hidden" value="<?php echo $arrD["socio_relacionado"]; ?>" id="idSocioRelacionado" />
		<input type='hidden' name="depende" value="<?php echo $arrD["dependiente"]; ?>" />
		<input type='hidden' name="DocumentoRelacionado" value="<?php echo $arrD["credito_relacionado"]; ?>" />
		<input type="hidden" name="idsocio" id="idNumeroSocio" value="<?php echo $arrD["numero_socio"]; ?>" />
		
		<p class="aviso"><?php echo $xHP->getMessages($msg); ?></p>
</form>
	<?php
	//Checar compatibilidad numerica entre los dependientes economicos
	$SQL_bene = "SELECT socios_relaciones.idsocios_relaciones AS 'num',
					socios_relacionestipos.descripcion_relacionestipos AS 'relacion',
					socios_consanguinidad.descripcion_consanguinidad AS 'consanguinidad',
					CONCAT(socios_relaciones.nombres ,' ', socios_relaciones.apellido_paterno, ' ', socios_relaciones.apellido_materno) AS 'nombre',
					socios_relaciones.curp AS 'curp',
					CONCAT(socios_relaciones.telefono_residencia, '; ' , socios_relaciones.telefono_movil)  AS 'telefonos'
				FROM
					`socios_relaciones` `socios_relaciones`
						INNER JOIN `socios_consanguinidad` `socios_consanguinidad`
						ON `socios_relaciones`.`consanguinidad` = `socios_consanguinidad`.
						`idsocios_consanguinidad`
							INNER JOIN `socios_relacionestipos` `socios_relacionestipos`
							ON `socios_relaciones`.`tipo_relacion` = `socios_relacionestipos`.
							`idsocios_relacionestipos`
				WHERE
					socio_relacionado=$idsocio
					AND
					(`socios_relaciones`.`credito_relacionado` = 1)";
	$cBenef		= new cTabla($SQL_bene);
	$cBenef->addTool(1);
	$cBenef->addTool(2);
	$cBenef->setKeyField("idsocios_relaciones");
	//$cBenef->Show("", false);
	?>
</fieldset>
</body>
<script>
var jsWorkForm		= document.frmreferencias;
var checkCURP		= true;
var xG				= new Gen();
var osrc			= null;
<?php
	//echo $cBenef->getJSActions();
?>
function resizeMainWindow(){
	var mWidth	= 800;
	var mHeight	= 640;
	window.resizeTo(mWidth, mHeight);
}
function initComponents(){
	resizeMainWindow();
}
function goSubmitForm(){
	var xPass	= (checkCURP == true) ? validarCURP() : true;
	if ( xPass == true ){
		var sPrin = confirm(	"Esta Persona Depende Economicamente\n"
							+ "del Solicitante/Asociado?\n"
							+ "CANCELAR = NO DEPENDE\nACEPTAR  = SI DEPENDE");
		if ( sPrin == false ){
			jsWorkForm.depende.value = 2;		//NO
		} else {
			jsWorkForm.depende.value = 1;		//SI
		}
		if (xPass == true ){
			jsWorkForm.submit();
		}
	}
}
function micurp(){}

function validarCURP(){
	
	var xResult = true;
	if( EACP_CLAVE_DE_PAIS == "MX"){
		var mCurp 	=  new String( jsWorkForm.curp.value );
		var isValCURP 	= CURP(mCurp.toUpperCase());
	
		if ( (isValCURP == false) && (mCurp != "IGNORAR")  ){
			alert("LA CURP {" + mCurp + "} parece no Valida!!\nSi no la conoce escriba 'IGNORAR'");
			jsWorkForm.curp.select();
			jsWorkForm.curp.focus();
			xResult = false;
		} else {
			xResult = true;
		}
	}
	return xResult;
}
//Validar CURP desde http://www.forosdelweb.com/f13/codigo-para-calculo-curp-javascript-425052/
function CURP(curp){
	var rs = true;

	if( EACP_CLAVE_DE_PAIS == "MX"){
		var exp = /^[A-Z]{4}\d{2}(1|0)\d(0|1|2|3)\d(H|M)[A-Z]{5}\d{2}$/;
		rs	= exp.test(curp);
	}	
	return rs;
}
function goSocio_(){
	var isoc 	= jsWorkForm.curp.value;
	var pfSoc 	= "../utils/frmbuscarsocio.php?i=";
	frmSoc 		= window.open(pfSoc + isoc + "&f=frmreferencias&ev=setValuesBySocio()&o=c", "", "width=600,height=400,dependent=yes");
	frmSoc.focus();
}
function getListaSocios(evt) {
	var myId	= evt.id;
	osrc		= "#" + evt.id;
	var xUrl	= "../svc/personas.svc.php?n=" + $("#idNombres").val() + "&p=" + $("#idApPaterno").val() + "&m=" + $("#idApMaterno").val();
	if ( String(evt.value).length >= 3 ) {
		xG.QList({
			url : xUrl,
			id : myId,
			func : "setSocio",
			key : "codigo",
			label : "nombrecompleto"
			});
	}
}

function setSocio(id) {
	$("#idNumeroSocio").val(id);
	setTimeout("JsaGetDatosHeredados()", 1000);
	checkCURP	= false;
	$("#idporcentaje").focus();
	$(osrc).qtip("hide");
}
</script>
</html>
