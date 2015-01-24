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

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->init();

$xFRM		= new cHForm("frm", "contabilidad_importar-catalogo.frm.php?action=" . MQL_TEST);
$xFil		= new cHFile();
$xChk		= new cHCheckBox();
$msg		= "";
if($action == SYS_NINGUNO ){
	$xFRM->addHElem( $xFil->getBasic("idarchivo","") );
	$xFRM->addHElem( $xChk->get("TR.Afectar Base de Datos", "idaplicar") );
	$xFRM->setEnc("multipart/form-data");
} else {
	//
	$doc1					= (isset($_FILES["idarchivo"])) ? $_FILES["idarchivo"] : false;
	$xFi					= new cFileImporter();
	$xT						= new cTipos();
	class cTmp {
		public $CUENTA		= 1;
		public $NATURALEZA	= 2;
		public $NIVEL		= 3;
		public $CTA1 		= 4;
		public $CTA2 		= 5;
		public $CTA3 		= 6;
		public $CTA4 		= 7;
		public $CTA5 		= 8;
		public $CTA6 		= 9;
	}
	$catImport				= array();
	$aplicar				= parametro("idaplicar", false, MQL_BOOL);
	if($aplicar == true ){ $action = MQL_ADD; }
	
	//Cedula de Identidad
	$tmp					= new cTmp();
	$xFi->setCharDelimiter("|");
	$xFi->setLimitCampos(9);
	$xFi->setToUTF8();
	//var_dump($_FILES["f1"]);
	
	
	if($xFi->processFile($doc1) == true){	
		$data				= $xFi->getData();
		$conteo				= 1;
		foreach ($data as $rows){
			if($conteo > 1){
				$xFi->setDataRow($rows);
				$cuenta		= $xFi->getV($tmp->CUENTA, "");
				$xCCont		= new cCuentaContable($cuenta);
				$DetNivel	= $xCCont->determineNivel($cuenta);
				$sucess		= true;
				
				$naturaleza	= $xFi->getV($tmp->NATURALEZA, false);
				
				$nivel		= $xFi->getV($tmp->NIVEL, 0, MQL_INT);
				$nombre		= "";
				$nombre		.= isset($rows[3]) ? $rows[3] : "";
				$nombre		.= isset($rows[4]) ? $rows[4] : "";
				$nombre		.= isset($rows[5]) ? $rows[5] : "";
				$nombre		.= isset($rows[6]) ? $rows[6] : "";
				$nombre		.= isset($rows[7]) ? $rows[7] : "";
				$nombre		.= isset($rows[8]) ? $rows[8] : "";
				
				$nombre		= trim($nombre);
				$superior	= $xCCont->getInmediatoSuperior();
				$cuenta		= $xCCont->getCuentaCompleta($cuenta);
				//$nombre		= trim( $rows[$tmp->CTA1] . $rows[$tmp->CTA2] . $rows[$tmp->CTA3] . $rows[$tmp->CTA4] . $rows[$tmp->CTA5] );
				//$nombre		= trim($xFi->getV($tmp->CTA1, "", MQL_RAW) . $xFi->getV($tmp->CTA2, "", MQL_RAW) . $xFi->getV($tmp->CTA3, "", MQL_RAW) . $xFi->getV($tmp->CTA4, "", MQL_RAW) . $xFi->getV($tmp->CTA5, "", MQL_RAW) );
				
				$nombre		= $xT->setNoAcentos($nombre);
				$nombre		= str_replace("'", "", $nombre);
				$nombre		= strtoupper($nombre);
				
				if($DetNivel != $nivel){
					$msg		.= "ERROR\t$conteo\t[$DetNivel]\t($nivel)\t$cuenta\t$superior\t$nombre\r\n";
					$sucess		= false;
				} else {
					$msg		.= "OK\t$conteo\t[$DetNivel]\t($nivel)\t$cuenta\t$superior\t$nombre\r\n";
					$sucess		= true;
				}
				if( ($sucess == true) AND ($action == MQL_ADD) ){
					if(setNoMenorQueCero($cuenta) > 0){
						$xCta		= new cCuentaContable($cuenta);
						$xCta->add($nombre, $naturaleza, false, false, $nivel, false, false, $superior);
						
						$msg		.= $xCta->getMessages(OUT_TXT);
					}
				}
			} else {
				//$msg		.= "$conteo\t===============\r\n";
			}
			$conteo++;
		}

		$msg		.= $xFi->getMessages(OUT_TXT);
		if(MODO_DEBUG == true){
			$xLog		= new cFileLog();
			$xLog->setWrite($msg);
			$xLog->setClose();
			$xFRM->addToolbar( $xLog->getLinkDownload("TR.Archivo del proceso", ""));
		}		
	}
}


$xFRM->addJsBasico();
//$xFRM->addCreditBasico();

$xFRM->addSubmit();

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>