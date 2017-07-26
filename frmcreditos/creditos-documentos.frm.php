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
$xHP		= new cHPage("TR.DOCUMENTACION", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc 		= new TinyAjax();
//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$pathPagare		= "";
$pathContrato	= "";

$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto		= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo		= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa	= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$observaciones	= parametro("idobservaciones");

$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());




if($credito <= DEFAULT_CREDITO){
	$xFRM->addCreditBasico();
	$xFRM->addSubmit();
} else {
	$xCred	= new cCredito($credito);
	if($xCred->init() == true){
		$persona	= $xCred->getClaveDePersona();
		$xFRM->addCerrar();
		$xFRM->OButton("TR.imprimir solicitud", "var xP= new CredGen();xP.getImprimirSolicitud($credito)", $xFRM->ic()->REPORTE);
		if($xCred->getEstadoActual() !== CREDITO_ESTADO_SOLICITADO){
			$xFRM->OButton("TR.IMPRIMIR PAGARE", "printpagare()", "dinero", "view-pagare");
			$xFRM->OButton("TR.CONTRATO", "contratocredito()", "imprimir", "print-contrato");
			$xFRM->OButton("TR.CARATULA", "var xC=new CredGen();xC.getImprimirCaratula($credito)", $xFRM->ic()->FORMATO);
			
		}
		
		switch($xCred->getEstadoActual()){
			case CREDITO_ESTADO_AUTORIZADO:
				$xFRM->OButton("TR.imprimir solicitud", "var xP= new CredGen();xP.getImprimirSolicitud($credito)", $xFRM->ic()->REPORTE);
				$xFRM->OButton("TR.Formato SIC", "var xC=new CredGen(); xC.getFormatoSIC($credito)", $xFRM->ic()->LEGAL);
				$xFRM->OButton("TR.IMPRIMIR formato NOTARIAL", "cedulanotario($credito)", "reporte", "view-cedula");
				$xFRM->OButton("TR.ORDEN_DE_DESEMBOLSO", "printodes()", "imprimir", "print-order");
				$xFRM->OButton("TR.IMPRIMIR MANDATO", "printMandato()", "imprimir", "print-mandato");
				$xFRM->OButton("TR.IMPRIMIR RECIBO DE credito", "printrec()", "imprimir", "print-recP");
				break;
			case CREDITO_ESTADO_SOLICITADO:
				$xFRM->OButton("TR.imprimir solicitud", "var xP= new CredGen();xP.getImprimirSolicitud($credito)", $xFRM->ic()->REPORTE);
				$xFRM->OButton("TR.Formato SIC", "var xC=new CredGen(); xC.getFormatoSIC($credito)", $xFRM->ic()->LEGAL);
				$xFRM->OButton("TR.IMPRIMIR formato NOTARIAL", "cedulanotario($credito)", "reporte", "view-cedula");
				$xFRM->OButton("TR.IMPRIMIR MANDATO", "printMandato()", "imprimir", "print-mandato");
				break;
			default :

				
				if($xCred->getSaldoActual()<=0){
					$xFRM->OButton("TR.CARTA_FINIQUITO", "var xC=new CredGen(); xC.getFormatoFiniquito($credito)", $xFRM->ic()->LEGAL);
				}
				$xFRM->OButton("TR.ESTADO_DE_CUENTA Intereses", "getEstadoDeCuentaIntereses($credito)", $xFRM->ic()->COBROS, "estado-cta2");
				$recibo		= $xCred->getNumeroReciboDeMinistracion();
				$xFRM->OButton("TR.IMPRIMIR RECIBO DE credito", "printrec()", "imprimir", "print-recP");
				break;
		}
		$xorigen			= $xCred->getTipoDeOrigen();
		$xT					= new cTipos();
		$xorigen			= $xT->cSerial(3, $xorigen);
		//Agregar Documentos segun tags
		$sql				= "SELECT `idgeneral_contratos`,`titulo_del_contrato`,`ruta` FROM `general_contratos` WHERE (`tags` LIKE '%$xorigen%' OR `tags` LIKE '%" . SYS_TODAS .  "%') AND `estatus`='alta' AND `tipo_contrato`=" . iDE_CREDITO;
		$rs					= $xQL->getDataRecord($sql);
		
		foreach ($rs as $rw){
			$url			= $rw["ruta"] . "&credito=" . $credito;
			$xFRM->OButton($rw["titulo_del_contrato"], "var xG=new Gen();xG.w({url:'$url',full:true, precall:getOArgs})", $xFRM->ic()->REPORTE5, "", "white");
		}

		$pathContrato		= $xCred->getPathDelContrato();
		$pathPagare			= $xCred->getOProductoDeCredito()->getPathPagare($credito);
		//================== Listado de Tramites
		if($xCred->getEsArrendamientoPuro() == true){
			$xFRM->addSeccion("idfrmtram", "TR.ARRENDAMIENTO");
			$xSelTram		= $xSel->getListadoGenerico("leasing_tramites_cat", "idtramite");
			$xSelTram->setLabel("TR.TRAMITE");
			
			$xFRM->addHElem( $xSelTram->get(true) );
			$xFRM->endSeccion();
		}
		
		
	} else {
		
	}
	
}

echo $xFRM->get();
?>
<script>
var xGen	= new Gen();
var idCredito	= <?php echo $credito; ?>;
var idSocio		= <?php echo $persona; ?>;
var idRecibo	= <?php echo $recibo; ?>;
var xRec		= new RecGen();
function getEstadoDeCuentaIntereses(idcredito){ xGen.w({ url: "../rptcreditos/estado_de_cuenta_de_intereses.rpt.php?credito=" + idcredito }); }
function printrec(){ xGen.w({ url: "../rpt_formatos/recibo_de_prestamo.pre.rpt.php?credito=" + idCredito }); }
function cedulanotario(lasol){ 	xGen.w({ url: '../rpt_formatos/rptcedulanotario.php?credito=' + idCredito }); }
function printMandato(){ xGen.w({ url: "../rpt_formatos/mandato_en_creditos.rpt.php?credito=" + idCredito }); }
function printpagare(){ xGen.w({ url: "<?php echo $pathPagare; ?>" }); }
function contratocredito(){ xGen.w({ url: "<?php echo $pathContrato; ?>", full: true }); }
function printodes(){ xGen.w({ url: "../rpt_formatos/rptordendesembolso.php?solicitud=" + idCredito }); }
function getOArgs(){
	str	= "&tramite=" + $("#idtramite").val();
	return str;
}
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>