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
$xHP		= new cHPage("Idioma", HP_FORM);

$DDATA		= $_REQUEST;

$action		= ( isset($DDATA["action"]) ) ? $DDATA["action"] : SYS_CERO;

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

echo $xHP->init();

$jsb	= new jsBasicForm("frmdocumentos");
//$jxc ->drawJavaScript(false, true);
$ByType	= "";

$xFRM	= new cHForm("frmfirmas", "../install/lang-upload.frm.php?action=" . SYS_UNO);
$xFRM->setEnc("multipart/form-data");
$xFRM->setTitle($xHP->getTitle());

$xBtn	= new cHButton();
$xTxt	= new cHText();
$xTxt2	= new cHText();
$xSel	= new cHSelect();
$xF		= new cFecha();
$xT		= new cTipos();
$xQL	= new MQL();


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
	$xFil->setCharDelimiter(",");
	
	//var_dump($_FILES["f1"]);
	if($xFil->processFile($doc1) == true){
		$data				= $xFil->getData();
		$linea				= 0;
		foreach($data as $idx => $cont){
			//
			if($linea == 0){
				
			} else {
				
				$xFil->setDataRow($cont);
				//cosulta de pais
				$tipo			= $xFil->getEntero(1);
				$clave			= $xFil->getEntero(2);
				$trad			= $xFil->getV(3);
				switch($tipo){
					case 1:
						$sql	= "UPDATE `general_menu` SET `menu_title`=TRIM('$trad') WHERE `idgeneral_menu`=$clave ";
						//$xFRM->addTag($sql);
						$xQL->setRawQuery($sql);
						break;
					case 2:
						//Mensajes
						$sql	= "UPDATE `sistema_mensajes` SET `mensaje` =TRIM('$trad') WHERE CRC32(`topico`)='$clave' ";
						$xQL->setRawQuery($sql);
						break;
					case 3:
						//Palabras
						$sql	= "UPDATE `sistema_lenguaje` SET `traduccion` =TRIM('$trad') WHERE CRC32(`equivalente`)='$clave' AND `idioma`='ES'";
						$xQL->setRawQuery($sql);
						break;
				}
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