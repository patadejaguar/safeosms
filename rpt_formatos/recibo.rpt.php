<?php
/**
 * @since 31/03/2008
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0.1
 *  01/Abril/2008
 * 		- cambios en la fecha
 * 		- Agregar Documento de Destino
 */
//====================================================================================================
//=====================================================================================================
//=====>	INICIO_H
include_once("../core/go.login.inc.php");
include_once("../core/core.error.inc.php");
include_once("../core/core.html.inc.php");
include_once("../core/core.init.inc.php");

@include_once("../libs/dompdf/autoload.inc.php");

$theFile					= __FILE__;
$permiso					= getSIPAKALPermissions($theFile);
if($permiso === false){		header ("location:../404.php?i=999");	}
$_SESSION["current_file"]	= addslashes( $theFile );
//<=====	FIN_H
$iduser = $_SESSION["log_id"];
//=====================================================================================================
$xHP		= new cHPage("", HP_RECIBO);
$recibo		= parametro("idrecibo", 0, MQL_INT); $recibo		= parametro("r", $recibo, MQL_INT); $recibo		= parametro("recibo", $recibo, MQL_INT);
$formato	= parametro("forma", 400, MQL_INT);
$sinTes		= parametro("notesoreria", false, MQL_BOOL);
$out		= parametro("out", SYS_DEFAULT, MQL_STRING);		
$out		= strtolower($out);
$backup		= parametro("backup", false, MQL_BOOL);
$items		= setNoMenorQueCero(RECIBOS_POR_HOJA);

$xRuls		= new cReglaDeNegocio();
$xUsr		= new cSystemUser();

//Agregado para hacer Backups
$senders		= getEmails($_REQUEST);
//end add
$useAlt			= $xRuls->getValorPorRegla($xRuls->reglas()->RECIBOS_USE_TICKETS);
$useAltUser		= $xUsr->getPuedeUsarPrintPOS();

//================
if($formato == 400 AND ($useAlt == true OR $useAltUser == true) ){
	$formato	= 402;
}

$xFMT		= new cHFormatoRecibo($recibo, $formato);
$xFMT->setIgnorarTesoreria($sinTes);
$xFMT->setOut($out);


$txt		= $xFMT->render();

if($xFMT->isInit() === false){
	$xHP->goToPageError(2011);	
} else {
	if($backup == true){
		$xFS		= new cFileSystem();
		$xFS->setPageLayout($xFS->PAGE_PORTRAIT);
		$txt		= $xFS->setRepareHTML($txt, true);
		$xF			= new cFecha();
		$tt			= $xF->getMarca();
		$xFS->setConvertToPDF($txt, "Archivo Recibo $recibo - $tt");
		
	}
	if(count($senders) >= 1){
		$xOH		= new cHObject();
		
		$html		= $xHP->getHeader() . $txt . "</body></html>";
		$title		= $xOH->getTitulize("RECIBO-$recibo");

		
		$dompdf 		= new Dompdf\Dompdf();
		$dompdf->loadHtml($html);
		$dompdf->setPaper("letter", "portrait" );
		
		$dompdf->render();
		
		$archivo		= PATH_TMP . "" . $title . ".pdf";
		$output 		= $dompdf->output();
		
		file_put_contents($archivo, $output);
		$output			= null;
		$body			= "RESPALDO DEL RECIBO $recibo";
		$xMail		= new cNotificaciones();
		foreach ($senders as $idmail => $email){
			$xMail->sendMail($title, $body, $email, array( "path" => $archivo ));
		}
		
		
		$rs		= array("message"  => $xMail->getMessages());
		$cnt	= json_encode($rs);
		
		//HEADERS JSON
		header('Content-type: application/json');
		echo $cnt;
		
	} else {

		if($out == OUT_TXT){
			header('Content-type: text/plain');
			echo $txt;
		} else {
		
			$xHP->init();
			
			if($items>1){
				$xFMT	= new cHFormatoRecibo($recibo, $formato);
				
				$xFMT->setIgnorarTesoreria($sinTes);
				$xFMT->setOut($out);
				
				$txtNP	= $xFMT->render(false);
				for($i = 1; $i <= $items; $i++){
					if($i == $items){
						echo $txt;
					} else {
						echo $txtNP;
					}		
				}
			} else {
				echo $txt;	
			}
			
			$xHP->fin();
		}
	}
}

?>