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

$xFRM	= new cHForm("frmfirmas", "entidades.upload.frm.php?action=" . SYS_UNO);
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
				$idlocalidad		= $cont[0];
				$nombrelocal		= $xT->setNoAcentos($cont[1]);
				$estadolocal		= $cont[2];
				$paislocal			= $cont[3];
				$idestadoloc		= $xT->cInt($cont[4]);
				//cosulta de pais
				
				$municipio			= EACP_CLAVE_DE_MUNICIPIO;
				$estado				= EACP_CLAVE_DE_ENTIDADFED;
				if(setNoMenorQueCero( $idestadoloc ) == 0){
					$D				= obten_filas("SELECT * FROM general_estados WHERE nombre LIKE '%$estadolocal%' LIMIT 0,1");
					if(isset($D["clave_numerica"])){
						$idestadoloc	= $D["clave_numerica"];
					}
				}
				//calcular pais
				$D			= obten_filas("SELECT * FROM `personas_domicilios_paises` WHERE `nombre_oficial` LIKE '%$paislocal%' LIMIT 0,1");
				if(isset($D["clave_de_control"])){
					$paislocal	= $D["clave_de_control"];
				} else {
					$paislocal	= EACP_CLAVE_DE_PAIS;
				}			
				
				$xLoc				= new cCatalogos_localidades();
				$xLoc->altitud(0);
				$xLoc->clave_de_estado($idestadoloc);
				$xLoc->clave_de_localidad($idlocalidad);
				$xLoc->clave_de_municipio(1);
				$xLoc->clave_unica( $xT->cInt($idlocalidad) );
				$xLoc->latitud(0);
				$xLoc->longitud(0);
				$xLoc->nombre_de_la_localidad($nombrelocal);
				$xLoc->clave_de_pais($paislocal);
				$ins	= $xLoc->query()->insert();
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