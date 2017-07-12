<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 0.0.01
 * @package core
 * @subpackage templates
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
$xHP		= new cHPage("TR.CARGA DE ARCHIVO", HP_FORM);
$xQL		= new MQL();
$xLog		= new cCoreLog();

class cTmp {
	public $SUCURSAL				= 1;
	public $ID_EMPRESA				= 2;
	public $ID_PERSONA				= 3;
}

$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT);
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);

$tipo			= parametro("tipo", 0, MQL_INT);


//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$xHP->addJsFile("../js/simpleUpload.min.js");
$xHP->init();

?> <style> #idmsg, #iderror { font-size : 1.3em !important; }
/*input[type="file"] { outline: none; cursor: pointer; position: absolute; left: 0; clip: rect(0px 215px 22px 145px); z-index: 2; opacity: 0; }*/
input[type="file"]{ display:none; }
</style><?php

$ByType	= "";
$xFRM	= new cHForm("frmfirmas", "entidades.upload.frm.php?action=" . MQL_ADD);
$xLog	= new cCoreLog();
$xFil	= new cHFile();


$xFRM->setEnc("multipart/form-data");
$xFRM->setTitle($xHP->getTitle());
$xFRM->addCerrar();
$xFRM->setNoAcordion();

$strExtra	= "";

switch ($tipo){
	case 281:
		$xOrig	= new cCreditosLeasing($clave);
		if($xOrig->init() == true){
			$strExtra	= ($xOrig->getEsPersonaMoral() == true) ? " AND ( `personas_documentacion_tipos`.`tags` LIKE '%pm%') " : " AND ( `personas_documentacion_tipos`.`tags` LIKE '%pf%') ";
		}
		break;
}

$rs		= $xQL->getDataRecord("SELECT   `personas_documentacion_tipos`.`clave_de_control`,
         `personas_documentacion_tipos`.`nombre_del_documento`
FROM     `personas_documentacion_tipos` WHERE (( `personas_documentacion_tipos`.`tags` LIKE '%originacion%' ) AND ( `personas_documentacion_tipos`.`tags` LIKE '%todas%' ) $strExtra ) AND (`personas_documentacion_tipos`.`estatus`  =1)");


$xDocs			= new cDocumentos();
$prepath		= $xDocs->getPathPorTipo($tipo);
$xBtn			= new cHButton();

foreach ($rs as $rw){
	$tipodoc	= $rw["clave_de_control"];
	$titulo		= setCadenaVal($rw["nombre_del_documento"]);
	$titulo		= str_replace(" ", "_", $titulo);
	$titulo		= $tipo . "_" . $clave . "_" . $tipodoc . "_" . strtolower($titulo);

	
	$xFil->setDivClass("");
	$xFil->setCSSLabel("button");
	$xFil->setUseProgressBar();
	
	if($xDocs->getFileExists($titulo, $prepath) == true){
		$nf		= $titulo . "." . $xDocs->getExt();
		$btn	= $xBtn->getBasic("TR.VER", "jsVerDocto('$nf')", $xFRM->ic()->VER);
		$xFRM->addHElem("<div class='medio'>
				<label class='button warning' onclick=\"jsVerDocto('$nf')\">El Documento $titulo Ya existe</label>
				<div class='progress'><span class='green' style='width:100%' id='pgr-$titulo'></span></div>
				</div>");
	} else {
		$xFRM->addDivMedio($xFil->getBasic($titulo, "", "Cargar " . $rw["nombre_del_documento"]) );
	}
	
	//$xFil->setDivClass("");

	//$xFRM->addHElem( $xFil->getBasic($titulo, "", $rw["nombre_del_documento"]) );
	//$xFRM->addDivMedio($xFil->getBasic($titulo, "", $rw["nombre_del_documento"]));
	
	//$xFRM->addHTML("<div class=\"button\"><input type=\"file\" name=\"upload\"/></div>");
	//$xFRM->addHTML('<div class="element-file"><label class="title"></label><label class="large"><div class="button">Choose Photo</div><input class="file_input" name="file" type="file"><div class="file_text">No photo selected</div></label></div>');
}

//$xFRM->addAviso("", "idmsg");
$xFRM->addAviso("", "idmsg", false, "notice");

echo $xFRM->get();
?>
<!-- HTML content -->
<script>
var vTipo		= <?php echo $tipo; ?>;
var xG			= new Gen();

$(document).ready(function(){

	$('input[type=file]').change(function(){
		var gid	= $(this).prop("id");
		$(this).simpleUpload("../svc/updoc-req.svc.php?id=" + gid + "&tipo=" + vTipo, {

			start: function(file){
				//upload started
				$('#idmsg').html(file.name);
				$('#iderror').html("");
			},

			progress: function(progress){
				//received progress
				
				//$('#iderror').html("Progress: " + Math.round(progress) + "%");
				
				$('#pgr-' +  gid).css("width", progress + "%");
			},

			success: function(data){
				//upload successful
				$('#idmsg').html(data.message);
				if(data.error == true){
					$('#pgr-' +  gid).attr("class","red");
				} else {
					$('#pgr-' +  gid).attr("class","green");
					//Disable
					
				}
						
				
			},

			error: function(error){
				//upload failed
				//$('#iderror').html("Failure!<br>" + error.name + ": " + error.message);
				$('#idmsg').html(data.message);
				$('#pgr-' +  gid).attr("class","red");
			}

		});

	});

});
function jsVerDocto(n){
	xG.w({url:"../frmutils/ver-docto.frm.php?docto=" + n +  "&tipoorigen=" +  vTipo});
}
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>