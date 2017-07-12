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

$action		= ( isset($DDATA["action"]) ) ? $DDATA["action"] : SYS_CERO;

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

echo $xHP->init();

$jsb	= new jsBasicForm("frmdocumentos");
//$jxc ->drawJavaScript(false, true);
$ByType	= "";

$xFRM	= new cHForm("frmactividades", "actividades.upload.frm.php?action=" . SYS_UNO);
$xFRM->setEnc("multipart/form-data");
$xFRM->setTitle($xHP->getTitle());

$xBtn	= new cHButton();
$xTxt	= new cHText();
$xTxt2	= new cHText();
$xSel	= new cHSelect();
$xF		= new cFecha();
$xT		= new cTipos();

$msg	= "";
if($action == SYS_CERO){
	$xFRM->addHElem("<div class='tx4'><label for='f1'>" . $xFRM->lang("archivo") . "</label><input type='file'  name='f1' id='f1'  /></div>");
	//$xFRM->addHElem( $xTxt2->getDeMoneda("idnumeropagina", $xFRM->lang("numero de", "pagina")) );
	$xFRM->addHElem( $xTxt->get("idobservaciones", "", "Observaciones") );
	$xFRM->addSubmit();
	$xFRM->addFootElement('<input type="hidden" name="MAX_FILE_SIZE" value="1024000">');
	echo $xFRM->get();
} else {
	$doc1				= (isset($_FILES["f1"])) ? $_FILES["f1"] : false;
	$observaciones		= (isset($DDATA["idobservaciones"]) ) ? $DDATA["idobservaciones"] : "";
	$xFil				= new cFileImporter();
	$xFil->setCharDelimiter("|");
	//var_dump($_FILES["f1"]);
	if($xFil->processFile($doc1) == true){
		$data				= $xFil->getData();
		$linea				= 0;
		foreach($data as $valores => $cont){
			//
			if($linea == 0){
				
			} else {
				//0clave_interna	1clave_de_actividad	2nombre_de_la_actividad	
				//3descripcion_detallada	4productos	5clasificacion	6clave_de_superior	
				//7nivel_de_riesgo	8califica_para_pep
			
				$idactividad		= $cont[0];
				$nombre				= $xT->setNoAcentos($cont[2]);
				$claveuif			= $xT->cSerial(7, $cont[1]);
				
				$esPep				= setNoMenorQueCero($cont[8]);
				$riesgo				= $xT->cInt($cont[7]);
				
				
				$xAct				= new cPersonas_actividad_economica_tipos();
				//eliminar primero
				$xAct->query()->setRawQuery("DELETE FROM personas_actividad_economica_tipos WHERE clave_interna=$idactividad");
				//asignar valores
				$xAct->califica_para_pep($esPep);
				$xAct->clasificacion("CLASE");
				$xAct->clave_de_actividad($claveuif);
				$xAct->clave_de_superior(0);
				$xAct->clave_interna($idactividad);
				$xAct->descripcion_detallada($nombre);
				$xAct->nivel_de_riesgo($riesgo);
				$xAct->nombre_de_la_actividad($nombre);
				$xAct->productos("");
				
				
				/*$xLoc->altitud(0);
				$xLoc->clave_de_estado($idestadoloc);
				$xLoc->clave_de_localidad($idlocalidad);
				$xLoc->clave_de_municipio(1);
				$xLoc->clave_unica( $xT->cInt($idlocalidad) );
				$xLoc->latitud(0);
				$xLoc->longitud(0);
				$xLoc->nombre_de_la_localidad($nombrelocal);
				$xLoc->clave_de_pais($paislocal);*/
				
				$ins	= $xAct->query()->insert();
				$ins->save();
				$msg	.= $ins->getMessages(OUT_TXT);
			}
			
			$linea++;
		}
	}
	$msg			.= $xFil->getMessages();
	if(MODO_DEBUG == true){
		$xFl	= new cFileLog();
		$xFl->setWrite( $msg );
		$xFl->setClose();
		$xFRM->addToolbar( $xFl->getLinkDownload("TR.archivo de eventos", "") );
	} else {
		echo JS_CLOSE;
	}
	echo $xFRM->get();
}

//$jsb->show();
?>
<!-- HTML content -->
<script>
</script>
<?php
$xHP->fin();
?>