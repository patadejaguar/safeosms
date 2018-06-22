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
$xHP		= new cHPage("TR.IMPORTAR DOCUMENTO", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$jxc 		= new TinyAjax();

//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
function jsaImportarDocto($id){
	$xF			= new cFecha();
	
	$sd			= explode("_", $id);
	$persona	= setNoMenorQueCero($sd[1]);
	$docto		= setNoMenorQueCero($sd[2]);
	$tipo		= setNoMenorQueCero($sd[3]);
	$time		= setNoMenorQueCero($sd[4]);
	$nid		= "$id.jpg";
	$xDB		= new cCouchDB();
	$xLog		= new cCoreLog();
	$ready		= true;
	
	$xDB->getCnn();
	$doc			= $xDB->getDoc($id);
	//$doc		= $dd->value;
	$file		= $xDB->getArchivo($doc);
	$fechacarga	= $xF->getFechaByInt($time);
	$xpath		= PATH_TMP . "tmp-" . $nid;
	//$xDoc->FTPUpload($documento);
	
	if(file_put_contents($xpath, $file)){
		$archivo["name"] 		= $nid;
		$archivo["tmp_name"] 	= $xpath;
		$pagina					= $doc->pagina;
		$observaciones			= $doc->observaciones;
		$fecha					= $xF->getFechaByInt($time);
		//$documento['tmp_name']
		
		$xSoc	= new cSocio($persona);
		if($xSoc->init() == true){
			$xDoc		=  new cDocumentos($nid);
			if($xDoc->FTPConnect()){
				if (ftp_put($xDoc->FTPConnect(), $nid, $xpath, FTP_BINARY)) {
					$xLog->add("OK\tSe ha enviado al servidor FTP el Archivo " . $nid . "\r\n", $xLog->DEVELOPER);
				} else {
					$xLog->add("ERROR\tNo se pudo enviar al servidor FTP el archivo " . $nid . "\r\n");
					$ready				= false;
				}
			
			}
			if($ready == true){
				$ready			= $xDoc->FTPMove($nid, $persona);
				if($ready == true){
					$ready		= $xDoc->add($tipo, $pagina, $observaciones, $docto, $persona, $nid, $fecha, false);
					$xDB->delDoc($id);
				}
				
			}
			
			$xLog->add($xDoc->getMessages());

			
			
			if($ready == true){
				return "EXITO";
			} else {
				return "ERROR" . $xLog->getMessages();
			}
		}
	}
	
	//var_dump();
	//file_put_contents( string $filename , mixed $data [, int $flags = 0 [, resource $context ]] )
	/*	if($xSoc->init() == true){
	//if($doc1 !== false){
		$ready		= $xSoc->setGuardarDocumento($tipodedocto, $nombrearchivo, $pagina, $observaciones, $fechacarga, $archivonuevo, $fechavenc);
		if($ready == true){
			$xFRM->addAvisoRegistroOK($xSoc->getMessages());
		} else {
			$xFRM->addAvisoRegistroError($xSoc->getMessages());
		}
	}*/
	
}
$jxc ->exportFunction('jsaImportarDocto', array('iddocto'), "#idmsg");

$jxc ->process();
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
$observaciones	= parametro("idobservaciones");

$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xFRM->addCerrar();

$xDB			= new cCouchDB();
$xDB->getCnn();
//$xPC	= new cCreditosPreclientes();
$data 			= $xDB->getTablaNoSync("documentos");

$xTb			= new cHTabla();
$xBtn			= new cHButton();

$xTb->addTH("TR.PERSONA");
$xTb->addTH("TR.NOMBRE");
$xTb->addTH("TR.CONTRATO");
$xTb->addTH("TR.DESCRIPCION");

$xTb->addTH("TR.FECHA");
$xTb->addTH("TR.TIPO");
$xTb->addTH("TR.HERRAMIENTAS");

foreach ($data as $obj){
	$v			= $obj->value;
	$id			= $obj->id;
	
	$sd			= explode("_", $obj->id);
	$persona	= setNoMenorQueCero($sd[1]);
	$docto		= setNoMenorQueCero($sd[2]);
	$tipo		= setNoMenorQueCero($sd[3]);
	$time		= setNoMenorQueCero($sd[4]);
	
	$xTipoDc	= new cPersonasDocumentacionTipos($tipo); $xTipoDc->init();
	
	
	$fecha		= $xF->getFechaByInt($time);
	$nombre		= "";
	
	
	//echo("$persona ---  $docto --- $time");
	
	//var_dump($xDB->getArchivo($v));
	
	
	
	if($docto > DEFAULT_CREDITO AND $persona <= DEFAULT_SOCIO){
		$xDoc	= new cCredito($docto); 
		if($xDoc->init() == true){
			$persona	= $xDoc->getClaveDePersona();
		}
	}
	
	if($persona > DEFAULT_SOCIO){
		$xSoc	= new cSocio($persona); $xSoc->init();
		$nombre	= $xSoc->getNombreCompleto();
		$xTb->initRow("", " id='th-$id' ");
		$xTb->addTD($persona);
		$xTb->addTD($nombre);
		$rundoc	= true;
		$desc	= "";
		$xDoc	= new cCredito($docto);
		if($xDoc->init() == true){
			$desc	= $xDoc->getDescripcion();
		} else {
			$xDoc	= new cCuentaDeCaptacion($docto);
			if($xDoc->init() == true){
				$desc	= $xDoc->getDescription();
			} else {
				$rundoc		= false;
				$docto		= 0;
			}
		}
		if($rundoc == true){		
			$xTb->addTD($docto);
			$xTb->addTD($desc);
		} else {
			$xTb->addTD("");
			$xTb->addTD("");
		}
		
		$xTb->addTD( date("d/m/Y H:s:i", $time) );
		$xTb->addTD($xTipoDc->getNombre());
		//$xFRM->ic()->DESCARGAR
		$xTb->addTD($xBtn->getBasic("TR.IMPORTAR", "jsImportarDocto('$id')" ), " id='tt-$id' ");
		$xTb->endRow();
	}
	//var_dump($v);
}
$xFRM->OHidden("iddocto", 0);

$xFRM->addHElem($xTb->get());
$xFRM->addAviso("", "idmsg");


echo $xFRM->get();

$jxc ->drawJavaScript(false, true);
?>
<script>
var  xG	= new Gen();

function jsImportarDocto(id){
	$("#iddocto").val(id);
	xG.confimar({
		msg : "MSG_CONFIRMA_IMPORTAR",
		callback :  jsImportarConfirmado
	});
}
function jsImportarConfirmado(){
	var idx = $("#iddocto").val();
	$("tt-" + idx).html("");
	//xG.postajax("");
	jsaImportarDocto();
}
</script>
<?php
$xHP->fin();
?>