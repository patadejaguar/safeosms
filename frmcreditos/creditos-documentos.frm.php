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
		//$xFRM->OButton("TR.imprimir solicitud", "var xP= new CredGen();xP.getImprimirSolicitud($credito)", $xFRM->ic()->REPORTE);
		

		$xorigen			= $xCred->getTipoDeOrigen();
		$xT					= new cTipos();
		$xorigen			= $xT->cSerial(3, $xorigen);
		//Agregar Documentos segun tags
		$sql				= "SELECT `idgeneral_contratos`,`titulo_del_contrato`,`ruta` FROM `general_contratos` WHERE (`tags` LIKE '%$xorigen%' OR `tags` LIKE '%" . SYS_TODAS .  "%') AND `estatus`='alta' AND `tipo_contrato`=" . iDE_CREDITO;
		$rs					= $xQL->getDataRecord($sql);
		$xFRM->addSeccion("iddoctobyprint", "TR.DOCUMENTOS / IMPRIMIR");
		$xTt				= new cHTabla();
		
		$xTt->addTH("TR.NOMBRE");
		$xTt->addTH("TR.HERRAMIENTAS");
		$it		= 0;

		$ArrGenericos	= array();
		
		$ArrGenericos[]	= array(
				"ruta" => "", "titulo" => $xFRM->l()->getT("TR.SOLICITUD"),
				"imprimir" => "var xP= new CredGen();xP.getImprimirSolicitud($credito)","doc" => "","pdf" => ""
		);
		
		if($xCred->getEsArrendamientoPuro() == false){
			
			if($xCred->getEstadoActual() !== CREDITO_ESTADO_SOLICITADO){
				$ArrGenericos[]	= array(
						"ruta" => "", "titulo" => $xFRM->l()->getT("TR.PAGARE"),
						"imprimir" => "printpagare()","doc" => "","pdf" => ""
				);
				$ArrGenericos[]	= array(
						"ruta" => "", "titulo" => $xFRM->l()->getT("TR.CONTRATO"),
						"imprimir" => "contratocredito()","doc" => "","pdf" => ""
				);
				$ArrGenericos[]	= array(
						"ruta" => "", "titulo" => $xFRM->l()->getT("TR.CARATULA"),
						"imprimir" => "var xC=new CredGen();xC.getImprimirCaratula($credito)","doc" => "","pdf" => ""
				);
				
				//$xFRM->OButton("TR.IMPRIMIR PAGARE", "printpagare()", "dinero", "view-pagare");
				//$xFRM->OButton("TR.CONTRATO", "contratocredito()", "imprimir", "print-contrato");
				//$xFRM->OButton("TR.CARATULA", "var xC=new CredGen();xC.getImprimirCaratula($credito)", $xFRM->ic()->FORMATO);
				
			}
			if($xCred->getEstadoActual() === CREDITO_ESTADO_SOLICITADO OR $xCred->getEstadoActual() === CREDITO_ESTADO_AUTORIZADO){

				$ArrGenericos[]	= array(
						"ruta" => "", "titulo" => $xFRM->l()->getT("TR.Formato SIC"),
						"imprimir" => "var xC=new CredGen(); xC.getFormatoSIC($credito)","doc" => "","pdf" => ""
				);
				$ArrGenericos[]	= array(
						"ruta" => "", "titulo" => $xFRM->l()->getT("TR.MANDATO"),
						"imprimir" => "printMandato()","doc" => "","pdf" => ""
				);
				$ArrGenericos[]	= array(
						"ruta" => "", "titulo" => $xFRM->l()->getT("TR.IMPRIMIR formato NOTARIAL"),
						"imprimir" => "cedulanotario($credito)","doc" => "","pdf" => ""
				);
			}
			switch($xCred->getEstadoActual()){
				case CREDITO_ESTADO_AUTORIZADO:
					//$xFRM->OButton("TR.imprimir solicitud", "var xP= new CredGen();xP.getImprimirSolicitud($credito)", $xFRM->ic()->REPORTE);
					//$xFRM->OButton("TR.Formato SIC", "var xC=new CredGen(); xC.getFormatoSIC($credito)", $xFRM->ic()->LEGAL);
					//$xFRM->OButton("TR.IMPRIMIR formato NOTARIAL", "cedulanotario($credito)", "reporte", "view-cedula");
					//$xFRM->OButton("TR.ORDEN_DE_DESEMBOLSO", "printodes()", "imprimir", "print-order");
					//$xFRM->OButton("TR.IMPRIMIR MANDATO", "printMandato()", "imprimir", "print-mandato");
					//$xFRM->OButton("TR.IMPRIMIR RECIBO DE credito", "printrec()", "imprimir", "print-recP");
					
					$ArrGenericos[]	= array(
							"ruta" => "", "titulo" => $xFRM->l()->getT("TR.ORDEN_DE_DESEMBOLSO"),
							"imprimir" => "printodes()","doc" => "","pdf" => ""
					);
					$ArrGenericos[]	= array(
							"ruta" => "", "titulo" => $xFRM->l()->getT("TR.IMPRIMIR RECIBO DE credito"),
							"imprimir" => "printrec()","doc" => "","pdf" => ""
					);
					break;
				case CREDITO_ESTADO_SOLICITADO:
					//$xFRM->OButton("TR.imprimir solicitud", "var xP= new CredGen();xP.getImprimirSolicitud($credito)", $xFRM->ic()->REPORTE);
					//$xFRM->OButton("TR.Formato SIC", "var xC=new CredGen(); xC.getFormatoSIC($credito)", $xFRM->ic()->LEGAL);
					//$xFRM->OButton("TR.IMPRIMIR formato NOTARIAL", "cedulanotario($credito)", "reporte", "view-cedula");
					//$xFRM->OButton("TR.IMPRIMIR MANDATO", "printMandato()", "imprimir", "print-mandato");
					break;
				default :
					
					
					if($xCred->getSaldoActual()<=0){
						$ArrGenericos[]	= array(
								"ruta" => "", "titulo" => $xFRM->l()->getT("TR.CARTA_FINIQUITO"),
								"imprimir" => "var xC=new CredGen(); xC.getFormatoFiniquito($credito)","doc" => "","pdf" => ""
						);
						//$xFRM->OButton("TR.CARTA_FINIQUITO", "var xC=new CredGen(); xC.getFormatoFiniquito($credito)", $xFRM->ic()->LEGAL);
					}
					$ArrGenericos[]	= array(
							"ruta" => "", "titulo" => $xFRM->l()->getT("TR.ESTADO_DE_CUENTA Intereses"),
							"imprimir" => "getEstadoDeCuentaIntereses($credito)","doc" => "","pdf" => ""
					);
					//$xFRM->OButton("TR.ESTADO_DE_CUENTA Intereses", "getEstadoDeCuentaIntereses($credito)", $xFRM->ic()->COBROS, "estado-cta2");
					//$xFRM->OButton("TR.IMPRIMIR RECIBO DE credito", "printrec()", "imprimir", "print-recP");
					$recibo		= $xCred->getNumeroReciboDeMinistracion();
					$ArrGenericos[]	= array(
							"ruta" => "", "titulo" => $xFRM->l()->getT("TR.IMPRIMIR RECIBO DE credito"),
							"imprimir" => "printrec()","doc" => "","pdf" => ""
					);
					
					break;
			}
		}
		foreach ($ArrGenericos as $rwx){
			$cssTag	= ($it == 0) ? "tags blue" : "tags green";
			$cssTr	= ($it == 0) ? "" : "trOdd";
			
			if($it ==1 ){
				$it = 0;
			} else {
				$it++;
			}
			
			$xTt->initRow($cssTr);
			
			//$url			= $rwx["ruta"] . "&credito=" . $credito;
			//$url2			= $rwx["ruta"] . "&credito=" . $credito . "&out=" . OUT_PDF;
			//$url3			= $rwx["ruta"] . "&credito=" . $credito . "&out=" . OUT_DOC;
			
			$xTt->addTD($rwx["titulo"]);
			
			$xBtn			= new cHButton();
			$xHl			= new cHUl("", "ul", $cssTag);
			
			$xHl->setTags("");
			if(isset($rwx["imprimir"]) AND $rwx["imprimir"] !== "" ){
				$xHl->li($xBtn->getBasic("TR.IMPRIMIR", $rwx["imprimir"], $xFRM->ic()->IMPRIMIR, "", false, true));
			}
			//
			if(isset($rwx["pdf"]) AND $rwx["pdf"] !== "" ){
				$xHl->li($xBtn->getBasic("TR.PDF", $rwx["pdf"], $xFRM->ic()->PDF, "", false, true));
			}
			if(isset($rwx["doc"]) AND $rwx["doc"] !== "" ){
				$xHl->li($xBtn->getBasic("TR.WORD", $rwx["doc"], $xFRM->ic()->REPORTE5, "", false, true));
			}

			//$xHl->li($xBtn->getBasic("TR.PDF", "var xG=new Gen();xG.w({url:'$url2',blank:true, precall:getOArgs})", $xFRM->ic()->PDF, "", false, true));
			//$xHl->li($xBtn->getBasic("TR.WORD", "var xG=new Gen();xG.w({url:'$url3',blank:true, precall:getOArgs})", $xFRM->ic()->REPORTE5, "", false, true));
			
			$xTt->addTD($xHl->get(), " class='toolbar-24' ");
		
			//$xFRM->OButton($rw["titulo_del_contrato"], "var xG=new Gen();xG.w({url:'$url',blank:true, precall:getOArgs})", $xFRM->ic()->REPORTE5, "", "white");
			$xTt->endRow();
		}
		
		//Formatos Nuevos
		foreach ($rs as $rw){
			$cssTag	= ($it == 0) ? "tags blue" : "tags green";
			$cssTr	= ($it == 0) ? "" : "trOdd";
			
			if($it ==1 ){
				$it = 0;
			} else {
				$it++;
			}
			
			$xTt->initRow($cssTr);
			
			$url			= $rw["ruta"] . "&credito=" . $credito;
			$url2			= $rw["ruta"] . "&credito=" . $credito . "&out=" . OUT_PDF;
			$url3			= $rw["ruta"] . "&credito=" . $credito . "&out=" . OUT_DOC;
			
			$xTt->addTD($rw["titulo_del_contrato"]);
			$idint			= $rw["idgeneral_contratos"];
			$xBtn			= new cHButton();
			$xHl			= new cHUl("", "ul", $cssTag);
			
			$xHl->setTags("");
			$xHl->li($xBtn->getBasic("TR.IMPRIMIR", "var xG=new Gen();xG.w({url:'$url',blank:true, precall:getOArgs})", $xFRM->ic()->IMPRIMIR, "", false, true));
			$xHl->li($xBtn->getBasic("TR.PDF", "var xG=new Gen();xG.w({url:'$url2',blank:true, precall:getOArgs})", $xFRM->ic()->PDF, "", false, true));
			$xHl->li($xBtn->getBasic("TR.WORD", "var xG=new Gen();xG.w({url:'$url3',blank:true, precall:getOArgs})", $xFRM->ic()->REPORTE5, "", false, true));
			if(MODO_DEBUG == true){
				$xHl->li($xBtn->getBasic("TR.EDITAR", "var xG=new Gen();xG.editForm({id:$idint})", $xFRM->ic()->EDITAR, "", false, true));
			}
			$xTt->addTD($xHl->get(), " class='toolbar-24' ");
			
			
			//$xFRM->OButton($rw["titulo_del_contrato"], "var xG=new Gen();xG.w({url:'$url',blank:true, precall:getOArgs})", $xFRM->ic()->REPORTE5, "", "white");
			$xTt->endRow();
		}
		$xFRM->addHElem($xTt->get() );
		$xFRM->endSeccion();
		
		
		//================== Documentos Existentes
		//$xLi->getListadoDePersonasDoctos($persona)
		
		$xTblD		= new cTabla($xLi->getListadoDePersonasDoctos($xCred->getClaveDePersona(), true, $xCred->getClaveDeCredito()), 0, "iddoctos");
		$xTblD->addEliminar(USUARIO_TIPO_GERENTE);
		$xTblD->setKeyField("clave");
		$xTblD->setKeyTable("personas_documentacion");
		$xTblD->setOmitidos("archivo_de_documento");
		$xTblD->OButton("TR.VER", "var xP=new PersGen();xP.getDocumento({id:" . HP_REPLACE_ID . "})", $xFRM->ic()->VER, "idview");
		
		
		$xFRM->addSeccion("idlistadocs", "TR.DOCUMENTOS / ARCHIVO");
		$xFRM->addHElem( $xTblD->Show() );
		$xFRM->endSeccion();
		
		$pathContrato		= $xCred->getPathDelContrato();
		$pathPagare			= $xCred->getOProductoDeCredito()->getPathPagare($credito);
		//================== Listado de Tramites
		if($xCred->getEsArrendamientoPuro() == true){
			$xFRM->addSeccion("idfrmtram", "TR.ARRENDAMIENTO");
			$xSelTram		= $xSel->getListadoGenerico("leasing_tramites_cat", "idtramite");
			$xSelTram->setLabel("TR.TRAMITE");
			$xFRM->addHElem( $xSelTram->get(true) );
			//Lista de Vehiculos por Arrendamiento
			$xFRM->addHElem( $xSel->getListaDeVehiculosPorCreds("",0, $xCred->getClaveDeCredito())->get(true) );
			
			$xFRM->endSeccion();
		} else {
			$xFRM->OHidden("idtramite", "0");
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
	str	= str + "&idvehiculo=" + $("#idvehiculo").val();
	return str;
}
</script>
<?php
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
