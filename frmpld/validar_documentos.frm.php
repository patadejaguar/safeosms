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
$xHP		= new cHPage("", HP_FORM);

$DDATA		= $_REQUEST;
$jxc 		= new TinyAjax();
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


$xHP->setTitle( $xHP->lang("validacion de", "documentos") );
echo $xHP->getHeader();

$jsb	= new jsBasicForm("");
//$jxc ->drawJavaScript(false, true);
echo $xHP->setBodyinit();

$xFRM	= new cHForm("frmvalidardocumentos", "./");
$xBtn	= new cHButton();		
$xTxt	= new cHText();
$xAt	= new cHTextArea();


$xFRM->setTitle($xHP->getTitle());
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
	(`personas_documentacion`.`estado_en_sistema` =1)";
$ql		= new cPersonas_documentacion();
$tdocto	= new cPersonas_documentacion_tipos();

$sel	= $ql->query()->select();
$sel->set($sql);
$data	= $sel->exec();
foreach ($data as $rows){
	$ql->setData($rows);
	$socio		= $ql->clave_de_persona()->v();
	$tipo		= $ql->tipo_de_documento()->v();
	$tdocto->setData( $tdocto->query()->initByID($tipo) );
	$id			= $ql->clave_de_control()->v();
	//var_dump($rows);
	$str		= "<div class='tx1'>";
	
	$str		.= "<div class='tx34'>";
	$str		.= "<fieldset><legend>$socio - " . $tdocto->nombre_del_documento()->v() . "</legend>";
	//$xDoc		= new cDocumentos($ql->);
	//XXX: Modificar 1.- Asunto de documento
	//$str		.= "<img src='../frmsocios/documento.png.php?persona=$socio&tipo=" . $tipo . "' class='docto' onclick=\"jsToImage('$socio&tipo=" . $tipo . "')\">";
	
	$str		.= "</fieldset></div>";
	
	$str		.= "<div class='tx14'>";
	
	$str		.= $xAt->get("idobservaciones-$id", "", $xFRM->lang("observaciones"));
	
	$str		.= "<div class='tx1'><table>";
	$str		.= "<tr><th>" . $xFRM->lang("validacion") . "</th></tr>";
	$str		.= "<tr><td>";
	$str		.= $xBtn->getBasic( $xFRM->lang("marcar como", "real"), "jsMarcarFalso($id, $socio)", "bien" );
	$str		.= "</td></tr>";
	$str		.= "<tr><td>";
	$str		.= $xBtn->getBasic( $xFRM->lang("marcar como", "falso"), "jsMarcarReal($id, $socio)", "mal" );
	$str		.= "</td></tr>";
	$str		.= "<tr><td>";
	$str		.= $xBtn->getBasic( $xFRM->lang("marcar como", "insuficiente"), "jsMarcarInsuficiente($id, $socio)", "aviso" );
	$str		.= "</td></tr>";
	//$str		.= $rows["clave_de_control"];
	$str		.= "</table></div>";
	
	//$str		.= "<div class='tx1'>";
	
	//$str		.= "</div>";
	
	$str		.= "</div>";
	
	$str		.= "<input type='hidden' id='iddocumento' /><input type='hidden'  id=idobservaciones' /><input type='hidden' id='idpersona' />";
	$str		.= "<p class='aviso' id='idmsg'></p>";
	$str		.= "</div>";
	$str		.= "<hr />";
	//echo $str;
	$xFRM->addHElem($str);
}
echo $xFRM->get();
echo $xHP->setBodyEnd();
$jsb->show();
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
$xHP->end();
?>