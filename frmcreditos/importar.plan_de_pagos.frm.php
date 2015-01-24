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
$credito	= ( isset($DDATA["credito"]) ) ? $DDATA["credito"] : DEFAULT_CREDITO;
$action		= ( isset($DDATA["action"]) ) ? $DDATA["action"] : SYS_CERO;

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

echo $xHP->getHeader();

$jsb	= new jsBasicForm("frmdocumentos");
//$jxc ->drawJavaScript(false, true);
$ByType	= "";
echo $xHP->setBodyinit();

$xFRM	= new cHForm("frmfirmas", "importar.plan_de_pagos.frm.php?credito=$credito&action=" . SYS_UNO);
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
	$doc1					= (isset($_FILES["f1"])) ? $_FILES["f1"] : false;
	$observaciones			= (isset($DDATA["idobservaciones"]) ) ? $DDATA["idobservaciones"] : "";
	$xFil					= new cFileImporter();
	//var_dump($_FILES["f1"]);
	if($xFil->processFile($doc1) == true){
		$data				= $xFil->getData();
		$xPlan				= new cPlanDePagos();
		//eliminar credito
		$xCred				= new cCredito($credito); $xCred->init();
		$fecha_operacion	= $xCred->getFechaDeMinistracion();
		
		if($xCred->getNumeroDePlanDePagos() != false){
			$xPlan->init( $xCred->getNumeroDePlanDePagos() );
			$xPlan->setEliminar();
		}
		$xPlan->initByCredito($credito);
		$xPlan->setClaveDeCredito($credito);
		$xPlan->setClaveDePersona($xCred->getClaveDePersona());
		$idrecibo		= $xPlan->add($observaciones, $fecha_operacion);
		$xPlan->init($idrecibo);
		foreach($data as $valores => $cont){
			//periodo 	fecha 	saldo_inicial 	intereses 	impuesto 	capital 	pago_total 	saldo_final
			//0		1	2		3		4		5		6		7
			//Periodo,Fecha de Pago,Saldo inicial,Intereses,Impuesto,Pago a Capital,Pago Total,Saldo final
			
			if($xT->cInt($cont[0]) > 0){
				$interes	= $xT->cFloat($cont[3],2);
				$fecha		= $xF->getFechaISO( ($cont[1]) );
				$periodo	= $xT->cInt($cont[0]);
				$capital	= $xT->cFloat($cont[5],2);
				$total_parcial	= $xT->cFloat($cont[6],2);
				$saldo_inicial	= $xT->cFloat($cont[2],2);
				
				$saldo_final	= $xT->cFloat($cont[7],2);
				
				$xPlan->setMontoOperado($total_parcial);
				$xPlan->setSaldoInicial($saldo_inicial);
				$xPlan->setSaldoFinal( $saldo_final );
				
				$xPlan->addMvtoDeInteres($interes,$fecha, $periodo);
				$xPlan->addMvtoDeIVA($fecha, $periodo);
				$xPlan->addMvtoDeCapital($capital, $fecha, $periodo);

			} else {
				$msg .= "WARN\tLINEA OMITIDA\r\n";
			}
		}
		$msg			.= $xCred->getMessages();
		$msg			.= $xPlan->getMessages();
		$mObj			= $xPlan->getObjRec();
		if( $mObj != null ){
			$mObj->setFinalizarRecibo(true);
			$msg			.= $mObj->getMessages(OUT_TXT);
		}
	}
	$msg			.= $xFil->getMessages();
	if(MODO_DEBUG == true){
		$xFl	= new cFileLog();
		$xFl->setWrite( $msg );
		$xFl->setClose();
		$xFRM->addHTML( $xFl->getLinkDownload("archivo de eventos") );
	} else {
		echo JS_CLOSE;
	}
	echo $xFRM->get();
}

echo $xHP->setBodyEnd();
//$jsb->show();
?>
<!-- HTML content -->
<script>
</script>
<?php
$xHP->end();
?>