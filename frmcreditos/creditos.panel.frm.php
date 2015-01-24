<?php
//=====================================================================================================
//=====>	INICIO_H
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	$theFile					= __FILE__;
	$permiso					= getSIPAKALPermissions($theFile);
	if($permiso === false){		header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//<=====	FIN_H
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
$xHP				= new cHPage("TR.Panel de Creditos");
$xT					= new cTipos();
/**
 * Define el Recibo
 */
$idrecibo 			= "";
$recibo_de_prestamo = "";

$idsocio 			= (isset($_REQUEST["idsocio"])) ? $_REQUEST["idsocio"] : false;
$idsolicitud 		= (isset($_REQUEST["idsolicitud"])) ? $_REQUEST["idsolicitud"] :  false;
$idsocio			= parametro("persona", $idsocio);
$idsolicitud		= parametro("credito", $idsolicitud);

$idrecibo			= DEFAULT_RECIBO;

echo $xHP->getHeader();
$oFrm				= new cHForm("frmextrasol", "creditos.panel.frm.php");
$xJs				= new jsBasicForm("frmextrasol");
//$xJs->setEstatusDeCreditos($estatus);
$xJs->setIncludeJQuery();
$mSQL				= new cSQLListas();
$pathContrato		= "";
$pathPagare			= "";

$oFrm->setTitle($xHP->getTitle() );
?>
<body>
<?php
if ( setNoMenorQueCero($idsolicitud) <= 0) {
	$idsocio	= getPersonaEnSession();
	$oFrm->addCreditBasico();
	$oFrm->addSubmit();
	echo $oFrm->get();
	//echo $xJs->get();
	$idsolicitud	= DEFAULT_CREDITO;
		//exit( "<p class='aviso'>AGREGUE UN NUMERO DE SOLICITUD</p></body></html>");
} else {

	$oFrm->OButton("TR.Recargar", "jsRecargar()", "refrescar", "refrescar");
	//Tabs
	$xHTabs		= new cHTabs();
	$xBtn		= new cHButton("");
	$xCred 		= new cCredito($idsolicitud);
	 
	if($xCred->init() == false ){
		$idsocio	= getPersonaEnSession();
		$oFrm->addToolbar($xBtn->getRegresar("../index.xul.php?p=frmcreditos/creditos.panel.frm.php", true));
		$oFrm->addCreditBasico();
		$oFrm->addSubmit();
		$oFrm->addAviso($xCred->getMessages());
		//echo $oFrm->get();		
	} else {
		if(setNoMenorQueCero($idsocio) <= 0){ $idsocio	= $xCred->getClaveDePersona(); }
		$xOPdto		= $xCred->getOProductoDeCredito();
		
		if($idsocio != $xCred->getClaveDePersona()){
			$msg		= "ERROR\tLa Persona $idsocio no es la propietaria del credito, el credito marca " .$xCred->getClaveDePersona() . "\r\n";
			$oFrm->addToolbar($xBtn->getRegresar("../index.xul.php?p=frmcreditos/creditos.panel.frm.php", true));
			$oFrm->addAviso($msg);
		} else {
			$oFrm->addHTML( $xCred->getFichaDeSocio(true) );
			$oFrm->addHTML( $xCred->getFicha(true, "", true) );
		
			$codigo_de_oficial	=  $xCred->getClaveDeOficialDeCredito();
			if(MODO_DEBUG == true){
				$oFrm->addToolbar($xBtn->getBasic("TR.EDICION_AVANZADA", "jsActualizarCreditoRAW()", "aviso", "edit-credito2", false ));
				$oFrm->addToolbar($xBtn->getBasic("TESTING", "var xG=new Gen();xG.w({url:'../unit/core.creditos.test.php?credito=$idsolicitud'})", "checar", "test-cred", false ));
			}
			$oFrm->addCreditoComandos($idsolicitud);
			$oFrm->addToolbar( $xBtn->getBasic("TR.ACTUALIZAR DATOS", "jsActualizarCredito()", "editar", "edit-credito", false )  );
			$oFrm->addToolbar( $xBtn->getBasic("TR.IMPRIMIR PAGARE", "printpagare()", "dinero", "view-pagare", false ) );
			$oFrm->addToolbar( $xBtn->getBasic("TR.IMPRIMIR formato NOTARIAL", "cedulanotario($idsolicitud)", "reporte", "view-cedula", false ) );
			$oFrm->addToolbar( $xBtn->getBasic("TR.ORDEN_DE_DESEMBOLSO", "printodes()", "imprimir", "print-order", false ) );
			$oFrm->addToolbar( $xBtn->getBasic("TR.CONTRATO", "contratocredito()", "imprimir", "print-contrato", false ) );
			$oFrm->addToolbar( $xBtn->getBasic("TR.IMPRIMIR RECIBO DE credito", "printrec()", "imprimir", "print-recP", false ));
			$oFrm->addToolbar( $xBtn->getBasic("TR.IMPRIMIR MANDATO", "printMandato()", "imprimir", "print-mandato", false ));
	
			$oFrm->addToolbar( $xBtn->getBasic("TR.ESTADO_DE_CUENTA", "getEstadoDeCuenta($idsolicitud)", "statistics", "estado-cta", false ));
			$oFrm->addToolbar( $xBtn->getBasic("TR.ESTADO_DE_CUENTA Intereses", "getEstadoDeCuentaIntereses($idsolicitud)", $oFrm->ic()->COBROS, "estado-cta2", false ));
			$idrecibo 			= $xCred->getNumeroReciboDeMinistracion();
			$idnumeroplan		= $xCred->getNumeroDePlanDePagos();
			if( setNoMenorQueCero($idnumeroplan) > 0) {
				$oFrm->addToolbar($xBtn->getBasic("TR.PLAN_DE_PAGOS #$idnumeroplan", "printplan($idnumeroplan)", "print", "print-plan", false ) );
				
			}
	
			$oFrm->addToolbar($xBtn->getBasic("TR.RENEGOCIAR", "jsRenegociar()", "editar", "mcediatar", false ));
			
			
			$oFrm->OButton("TR.Parcialidades Pendientes", "var xcg = new CredGen();xcg.getLetrasEnMora($idsolicitud)", $oFrm->ic()->PREGUNTAR);
	
			if($codigo_de_oficial == USUARIO_TIPO_OFICIAL_AML OR OPERACION_LIBERAR_ACCIONES == true){
				
			}
			//Agregar Listado de Recibos
			$cTblx	= new cTabla($mSQL->getListadoDeRecibos("", $xCred->getClaveDePersona(), $xCred->getNumeroDeCredito()));
			$cTblx->setKeyField("idoperaciones_recibos");
			$cTblx->setTdClassByType();
			$cTblx->setEventKey("jsGoPanelRecibos");
			$xHTabs->addTab("TR.RECIBOS", $cTblx->Show());
					
			if ( $codigo_de_oficial == getUsuarioActual() OR OPERACION_LIBERAR_ACCIONES == true){
					$setSql 	= $mSQL->getListadoDeLlamadas($idsolicitud); 
					$setSql3 	= $mSQL->getListadoDeNotificaciones($idsolicitud);
					$c2Tbl 		= new cTabla($mSQL->getListadoDeNotas(false, $idsolicitud), 0);
					$c2Tbl->setWidth();
					$xHTabs->addTab("TR.NOTAS", $c2Tbl->Show());
	
					
					$c4Tbl = new cTabla($mSQL->getListadoDeCompromisos($idsolicitud), 5);
					$c4Tbl->setWidth();
					$xHTabs->addTab("TR.COMPROMISOS" ,  $c4Tbl->Show());
	
					$cTbl = new cTabla($setSql,0);
					$cTbl->addTool(SYS_DOS);
					$cTbl->setKeyField("idseguimiento_llamadas");
					$oFrm->addHTML( $cTbl->getJSActions(true) );
					$xHTabs->addTab("TR.LLAMADAS" , $cTbl->Show() );
	
					$c3Tbl = new cTabla($setSql3, 3);
					$c3Tbl->setWidth();
					$xHTabs->addTab("TR.NOTIFICACIONES" , $c3Tbl->Show() );
					
					//Imprime un Explain de porque el credito tiene este estatus
					$xHTabs->addTab("TR.ESTATUS", $xCred->setDetermineDatosDeEstatus(fechasys(), true, true) );
					$xHTabs->addTab("TR.VALIDACION", "<p class='aviso'>" . $xCred->setVerificarValidez(1, "html") . "</p>");
					//avales
					
					$sqlavales = $mSQL->getListadoDeAvales($idsolicitud, $idsocio);
					$xTblAv		= new cTabla($sqlavales);
					$xTblAv->addTool(SYS_DOS);
					$xTblAv->addTool(SYS_UNO);
					$xHTabs->addTab("TR.AVALES", $xTblAv->Show("TR.Relacion de Avales"));
			}
			
			//==================================================== Otros Datos
			$xTbOD		= new cTabla($mSQL->getListadoDeCreditosOtrosDatos($idsolicitud));
			$xHTabs->addTab("TR.Otros Datos", $xTbOD->Show());
			if($xCred->getPeriocidadDePago() !=  CREDITO_TIPO_PERIOCIDAD_FINAL_DE_PLAZO ){
				$oFrm->addToolbar($xBtn->getBasic("TR.GENERAR PLAN_DE_PAGOS", "regenerarPlanDePagos()", "reporte", "generar-plan", false ) );
				$xHTabs->addTab("TR.Plan_De_pagos", $xCred->getPlanDePago(OUT_HTML, false, true));	
			}		
			if(MODO_DEBUG == true){
				$xHTabs->addTab("TR.Sistema", $xCred->getMessages(OUT_HTML));
				
				$sql		= $mSQL->getListadoDeOperaciones("", $idsolicitud);
				$cEdit		= new cTabla($sql);
				$cEdit->addTool(SYS_UNO);
				$cEdit->addTool(SYS_DOS);

				$cEdit->setTdClassByType();
				$cEdit->setKeyField("idoperaciones_mvtos");
				$xHTabs->addTab("TR.Operaciones", $cEdit->Show());
				$cMovs		= new cTabla($mSQL->getListadoDeSDPMCredito($idsolicitud));
				$xHTabs->addTab("TR.Historial", $cMovs->Show());
			}
			$oFrm->addHTML( $xHTabs->get() );
		}
		
		$oFrm->OButton("TR.Castigos", "jsCastigos($idsolicitud)", "error");
		
		$pathContrato		= $xCred->getPathDelContrato();
		$pathPagare			= $xOPdto->getPathPagare($idsolicitud);
	}
	echo $oFrm->get();
?>
<script >
	var siAvales	= "si";
	var idCredito	= <?php echo $idsolicitud; ?>;
	var idSocio		= <?php echo $idsocio; ?>;
	var idRecibo	= <?php echo $idrecibo; ?>;
	
	var ogen	= new Gen();
	function setNoAvales(){ siAvales = (document.getElementById("idNoAvales").checked) ? "no" : "si"; }
	
	function jsGoPanelRecibos(id){ 	ogen.w({ url: "../frmoperaciones/recibos.panel.frm.php?cNumeroRecibo=" + id}); }
	function printodes(){ 		ogen.w({ url: "../rpt_formatos/rptordendesembolso.php?solicitud=" + idCredito }); }
	function printrec(){ 		ogen.w({ url: "../rpt_formatos/recibo_de_prestamo.pre.rpt.php?credito=" + idCredito }); }
	function printplan(elplan) { 	ogen.w({ url: "../rpt_formatos/rptplandepagos.php?idrecibo=" + elplan + "&p=" + siAvales }); }
	function jsActualizarCredito(){ ogen.w({ url: "../frmcreditos/credito.actualizar.frm.php?credito=" + idCredito, w:600,h:600, tiny : true }); }
	
	function jsRenegociar(){ ogen.w({ url: "../frmcreditos/renegociar.frm.php?credito=" + idCredito, w:500, h:650, tiny : true }); }
	
	function jsActualizarCreditoRAW(){ ogen.w({ url: "../utils/frm8db7028bdcdf054882ab54f644a9d36b.php?t=creditos_solicitud&f=numero_solicitud=" + idCredito }); }
	
	function gogarantias(){ 	ogen.w({ url: "../frmcreditos/frmcreditosgarantias.php?solicitud=" + idCredito }); }
	function goavales() { 		ogen.w({ url: "../frmcreditos/frmcreditosavales.php?s=" + idCredito + "&i=" + idSocio }); }
	function goflujoefvo(){ 	ogen.w({ url: "../frmcreditos/frmcreditosflujoefvo.php?solicitud=" + idCredito }); }
	
	function printsol() {		ogen.w({ url: "../frmcreditos/rptsolicitudcredito1.php?solicitud=" + idCredito }); }
	
	function cedulanotario(lasol){ 	ogen.w({ url: '../rpt_formatos/rptcedulanotario.php?s=' + idCredito }); }
	function printMandato(){ 	ogen.w({ url: "../rpt_formatos/mandato_en_creditos.rpt.php?i=" + idCredito }); }
	function jsEditarPlan(mPlan){ 	ogen.w({ url: '../frmcreditos/plan_de_pagos.edicion.frm.php?i=' + mPlan }); }
	
	function getEstadoDeCuenta(idcredito){ ogen.w({ url: "../rpt_edos_cuenta/rptestadocuentacredito.php?credito=" + idcredito }); }
	function getEstadoDeCuentaIntereses(idcredito){ ogen.w({ url: "../rptcreditos/estado_de_cuenta_de_intereses.rpt.php?credito=" + idcredito }); }
	function jsRecargar(){ window.location = "creditos.panel.frm.php?idsocio=" + idSocio + "&idsolicitud=" + idCredito; }
	function printpagare(){ ogen.w({ url: "<?php echo $pathPagare; ?>" }); }
	function contratocredito(){ ogen.w({ url: "<?php echo $pathContrato; ?>", full: true }); }
	function regenerarPlanDePagos(){ ogen.w({ url: '../frmcreditos/frmcreditosplandepagos.php?r=1&c=' + idCredito + "&s=" + idSocio }); }
	function addLlamada(mKey){ }
	function addAviso(mKey){ }
	function addCompromiso(mKey){ }
	function jsCastigos(idcredito){ ogen.w({ url: '../frmcreditos/castigo_de_cartera.frm.php?credito=' + idcredito, tiny : true, h: 400, w : 600 }); }
</script>	
	<?php 
}
?>
</body>
<?php
echo $xJs->get();
?>
</html>
