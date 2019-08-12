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
$xHP		= new cHPage("TR.VALIDAR DOCUMENTOS", HP_FORM);
$jxc 		= new TinyAjax();
$xQL		= new MQL();

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);

function jsaMarcarFalso($documento, $observaciones, $persona){
	$xAml	= new cAMLPersonas($persona);
	$xAml->init();
	$xAml->setGuardarDocumentoValidado($documento, AML_KYC_DOCTO_FALSO, $observaciones);
	return $xAml->getMessages(OUT_HTML);
}
function jsaMarcarReal($documento, $observaciones, $persona){
	$xAml	= new cAMLPersonas($persona);
	$xAml->init();
	$xAml->setGuardarDocumentoValidado($documento, AML_KYC_DOCTO_REAL, $observaciones);
	return $xAml->getMessages(OUT_HTML);
}
function jsaMarcarSinInfo($documento, $observaciones, $persona){
	$xAml	= new cAMLPersonas($persona);
	$xAml->init();
	$xAml->setGuardarDocumentoValidado($documento, AML_KYC_DOCTO_NO_VERIFICADO, $observaciones);
	return $xAml->getMessages(OUT_HTML);
}
$jxc ->exportFunction('jsaMarcarFalso', array('iddocumento', 'idobservaciones', 'idpersona'), '#idmsg');
$jxc ->exportFunction('jsaMarcarReal', array('iddocumento', 'idobservaciones', 'idpersona'), '#idmsg');
$jxc ->exportFunction('jsaMarcarSinInfo', array('iddocumento', 'idobservaciones', 'idpersona'), '#idmsg');

$jxc ->process();
$ByID		= "";
if($clave >0){
	$xDoc	= new cPersonasDocumentacion($clave);
	if($xDoc->init() == true){
		$persona	= $xDoc->getClaveDePersona();
		$ByID		= " AND (`personas_documentacion`.`clave_de_control`=$clave) ";
	}	
}

//$jxc ->drawJavaScript(false, true);
$xHP->init();

$xFRM	= new cHForm("frmvalidardocumentos", "validar_documentos.frm.php");
$xBtn	= new cHButton();		
$xTxt	= new cHText();
$xAt	= new cHTextArea();


$xFRM->setTitle($xHP->getTitle());
if($persona <= DEFAULT_SOCIO){
	$xFRM->addPersonaBasico();
	$xFRM->addEnviar();
} else {
	$xFRM->OHidden("idsocio", $persona);
	$xFRM->addCerrar();
	$xFRM->addAtras();
	
	$sql	= "SELECT
		`personas_documentacion`.`clave_de_control`,
		`personas_documentacion`.`clave_de_persona`,
		`socios`.`nombre`,
		`personas_documentacion`.`fecha_de_carga`,
		`personas_documentacion_tipos`.`nombre_del_documento`,
		`personas_documentacion`.`observaciones`,
		`personas_documentacion`.`estado_en_sistema`,
		`personas_documentacion`.`tipo_de_documento`  
	FROM
		`personas_documentacion` `personas_documentacion` 
			INNER JOIN `socios` `socios` 
			ON `personas_documentacion`.`clave_de_persona` = `socios`.`codigo` 
				INNER JOIN `personas_documentacion_tipos` 
				`personas_documentacion_tipos` 
				ON `personas_documentacion`.`tipo_de_documento` = 
				`personas_documentacion_tipos`.`clave_de_control` 
	WHERE
			`personas_documentacion`.`clave_de_persona` = $persona $ByID
		";
	$xTabla		= new cPersonas_documentacion();
	$rs			= $xQL->getDataRecord($sql);
	$nobserva	= $xFRM->lang("observaciones");
	
	foreach ($rs as $rows){
		//$iddocto	= $rows["clave_de_control"];
		//$xDoc		= new cPersonasDocumentacion($iddocto);
		
		$xTabla->setData($rows);
		if($persona>DEFAULT_SOCIO){
			$xSoc	= new cSocio($persona);
			if($xSoc->init() == true){
				$xFRM->addHElem( $xSoc->getFicha(false, false, "", true) );
			}
		}
		$socio		= $xTabla->clave_de_persona()->v();
		$tipo		= $xTabla->tipo_de_documento()->v();
		
		$ndocto		= "";
		$xTipoD	= new cPersonasDocumentacionTipos($tipo);
		if($xTipoD->init() == true){
			$ndocto	= $xTipoD->getNombre();
		}
		$id			= $xTabla->clave_de_control()->v();
		//var_dump($rows);
		$str		= "<div class='tx1'>";
		
		$str		.= "<div class='tx34'>";
		$str		.= "<fieldset><legend>$socio - " . $ndocto . "</legend>";
		//$xDoc		= new cDocumentos($ql->);
		//XXX: Modificar 1.- Asunto de documento
		//$str		.= "<img src='../frmsocios/documento.png.php?persona=$socio&tipo=" . $tipo . "' class='docto' onclick=\"jsToImage('$socio&tipo=" . $tipo . "')\">";
		
		$str		.= "</fieldset></div>";
		
		$str		.= "<div class='tx14'>";
		
		$str		.= $xAt->get("idobservaciones-$id", "", $nobserva);
		
		$str		.= "<div class='tx1'><table>";
		
		$str		.= "<tr><td style='height:3.4em'>";
		$str		.= $xBtn->getBasic("TR.MARCARCOMO REAL", "jsMarcarReal($id, $socio)", "bien" );

		$str		.= "</td></tr>";
		$str		.= "<tr><td style='height:3.4em'>";
		$str		.= $xBtn->getBasic("TR.MARCARCOMO FALSO", "jsMarcarFalso($id, $socio)", "mal" );

		$str		.= "</td></tr>";
		$str		.= "<tr><td style='height:3.4em'>";
		$str		.= $xBtn->getBasic("TR.MARCARCOMO DUDOSO", "jsMarcarInsuficiente($id, $socio)", "aviso" );

		$str		.= "</td></tr>";

		$str		.= "</table></div>";
		
		$str		.= "</div>";
		
		$str		.= "<input type='hidden' id='iddocumento' /><input type='hidden'  id=idobservaciones' /><input type='hidden' id='idpersona' value='$socio' />";
		$str		.= "<p class='aviso' id='idmsg'></p>";
		$str		.= "</div>";
		$str		.= "<hr />";
		//echo $str;
		$xFRM->addHElem($str);
	}
}
echo $xFRM->get();


?>
<!-- HTML content -->
<script>
	function jsMarcarFalso(id, persona){
		$("#idobservaciones").val( $("#idobservaciones-" + id).val() );	$("#iddocumento").val(id); $("#idpersona").val(id);
		jsaMarcarFalso();
	}
	function jsMarcarReal(id, persona){
		$("#idobservaciones").val( $("#idobservaciones-" + id).val() );	$("#iddocumento").val(id); $("#idpersona").val(id);
		jsaMarcarReal();
	}
	function jsMarcarInsuficiente(id, persona){
		$("#idobservaciones").val( $("#idobservaciones-" + id).val() );	$("#iddocumento").val(id); $("#idpersona").val(id);
		jsaMarcarSinInfo();
	}
</script>
<?php
$xHP->fin();
?>