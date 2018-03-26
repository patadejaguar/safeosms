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
$xHP		= new cHPage("", HP_FORM);

$DDATA		= $_REQUEST;

$action		= ( isset($DDATA["action"]) ) ? $DDATA["action"] : SYS_NINGUNO;

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

echo $xHP->init();



$ByType	= "";

$xFRM	= new cHForm("frmfirmas", "creditos_oficiales.upload.frm.php?action=" . MQL_ADD);
$xFRM->setTitle($xHP->getTitle());
$xLog	= new cCoreLog();

if($action == SYS_NINGUNO){
	$xFRM->OHidden("MAX_FILE_SIZE", "1024000");
	$xFRM->OFile("idarchivo","",  "TR.Archivo");
	$xFRM->addObservaciones();
	$xFRM->addSubmit();
} else {
	$doc1				= (isset($_FILES["idarchivo"])) ? $_FILES["idarchivo"] : false;
	$observaciones		= parametro("idobservaciones", "");
	$delimiter			= parametro("idlimitador", "|");
	$xFil				= new cFileImporter();
	$xFil->setCharDelimiter($delimiter);
	//var_dump($_FILES["f1"]);
	if($xFil->processFile($doc1) == true){
		$data				= $xFil->getData();
		$linea				= 0;
		foreach($data as $valores => $cont){
			//
			$xFil->setDataRow($cont);
			$idcredito		= $xFil->getEntero(1);
			$idoficial		= $xFil->getEntero(2);
			if($idcredito > DEFAULT_CREDITO AND $idoficial > 0){
				$xCred		= new cCredito($idcredito);
				if($xCred->init() == true){
					if($xCred->setCambiarOficialCred($idoficial) == true){
						$xLog->add("OK\tEl Credito $idcredito Actualizado al Oficial $idoficial\r\n");
					} else {
						$xLog->add("ERROR\tEl Credito $idcredito No se Actualiza al Oficial $idoficial\r\n");
					}
				} else {
					$xLog->add("ERROR\tEl Credito $idcredito No se Inicia\r\n");
				}
				$xLog->add($xCred->getMessages(), $xLog->DEVELOPER);
			} else {
				$xLog->add("WARN\tEl Credito $idcredito No se Importa\r\n");
			}
			$linea++;
		}
	}
	$xLog->add($xFil->getMessages(), $xLog->DEVELOPER);
	$xFRM->addLog($xLog->getMessages());

	
}
echo $xFRM->get();
?>
<!-- HTML content -->
<script>
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>