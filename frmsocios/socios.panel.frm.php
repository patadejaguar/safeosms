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
$xHP		= new cHPage("TR.Panel de Control de personas", HP_FORM);
$jxc 		= new TinyAjax();
$xql		= new cSQLListas();
$ql			= new MQL();
$xImg		= new cHImg();
$xF			= new cFecha();
$jsTabs		= "";
$idempresa	= 0;
$oficial 	= elusuario($iduser);
$idsocio 	= parametro("idsocio", false, MQL_INT); $idsocio 	= parametro("persona", $idsocio, MQL_INT); $idsocio 	= parametro("socio", $idsocio, MQL_INT);
$xJsB		= new jsBasicForm("extrasocios");

function jsaReVivienda($idsocio){
	$ql		= new cSQLListas();
	$sqlvv = "SELECT socios_vivienda.idsocios_vivienda AS 'num',
		
					CONCAT('Calle ', socios_vivienda.calle, ' Num. ',
					socios_vivienda.numero_exterior, '-', socios_vivienda.numero_interior, ' Col. ',
				  	socios_vivienda.colonia, ', ', socios_vivienda.localidad, ', ', estado)
					AS 'domicilio_completo',
					getBooleanMX(socios_vivienda.principal) AS 'es_principal'
				    FROM socios_vivienda WHERE socio_numero=$idsocio  LIMIT 0,10";
		
		$cTbl = new cTabla($sqlvv);
		$cTbl->setWidth();
		//$cTbl->addTool(1);
		$cTbl->addTool(2);
		$cTbl->OButton("TR.Verificar", "jsVerificar(_REPLACE_ID_)", $cTbl->ODicIcons()->SALUD);
	
		$cTbl->setKeyField("idsocios_vivienda");
			
		return $cTbl->Show();
}
function jsaReActividadE($idsocio){
	$ql		= new cSQLListas();
	


		
	$myCab = new cTabla($ql->getListadoDeActividadesEconomicas($idsocio));
	$myCab->setWidth();
	$myCab->addTool(2);
	$myCab->OButton("TR.Verificar", "jsVerificarAE(_REPLACE_ID_)", $myCab->ODicIcons()->SALUD);
	$myCab->setKeyField("idsocios_aeconomica");
	return  $myCab->Show();
}
function jsaReRelaciones($idsocio){
	//Checar compatibilidad numerica entre los dependientes economicos
	$sqlL		= new cSQLListas();
	$cBenef		= new cTabla($sqlL->getListadoDeRelaciones($idsocio));
	$xTbl	= new cHTabla("idtblrels");$xHSel		= new cHSelect(); $xChk	= new cHCheckBox(); $xText	= new cHText(); $xText->setDivClass(""); $xChk->setDivClass("");
	$xBtn	= new cHButton(); $xUl		= new cHUl(); $li = $xUl->getO(); $li->setT("ul"); $li->setClass("tags blue");
	$li->add($xBtn->getBasic("TR.Guadar", "jsGuardarReferencia()", $xBtn->ic()->GUARDAR, "idguardar", false, true), "");
	$xTbl->initRow();
	$xTbl->addTD($xText->getDeNombreDePersona());
	$xTbl->addTD($xHSel->getListaDeTiposDeRelaciones("", "")->get("") );
	$xTbl->addTD($xHSel->getListaDeTiposDeParentesco()->get("")  );
	$xTbl->addTD($xChk->get("TR.es dependiente_economico", "dependiente") );
	$xTbl->addRaw("<td class='toolbar-24'>". $xUl->get() . "</td>" );
	$xTbl->endRow();
		
	$cBenef->addTool(1);
	$cBenef->addTool(2);
	$cBenef->setKeyField("idsocios_relaciones");	
	return $xTbl->get(). $cBenef->Show();
}
function jsaRePatrimonio($idsocio){
	$ql		= new cSQLListas();
	//$ql->getLi
	$sql_cnn = "SELECT socios_patrimonio.idsocios_patrimonio AS 'id', socios_patrimoniotipo.descripcion_patrimoniotipo AS 'tipo_de_patrimonio',
						socios_patrimonio.fecha_expiracion, socios_patrimonio.observaciones,
						socios_patrimonio.descripcion, socios_patrimonio.documento_presentado,
						socios_patrimonio.monto_patrimonio AS 'valor'
				FROM socios_patrimonio, socios_patrimoniotipo ";
	$sql_cnn .= " WHERE socios_patrimoniotipo.idsocios_patrimoniotipo=socios_patrimonio.tipo_patrimonio AND socios_patrimonio.socio_patrimonio=$idsocio";
	$sql_cnn .= " LIMIT 0,100";
	
	$myTab = new cTabla($sql_cnn);
	//$myTab->addTool(1);
	$myTab->addTool(2);
	$myTab->setKeyField("idsocios_patrimonio");
	
	return $myTab->Show();	
}

function jsaSetDocumentoVerificado(){ }
function jsaSetDocumentoFalso(){ }

function jsaValidarDocumentacion($persona){
	$xAml	= new cAMLPersonas($persona);
	$xAml->init($persona);
	$xAml->setVerificarDocumentosCompletos();
	$xAml->setVerificarDocumentosVencidos();
	return $xAml->getMessages(OUT_HTML);
}
function jsaValidarRiesgo($persona){
	$xAml	= new cAMLPersonas($persona);
	$xAml->init($persona);
	$xAml->setAnalizarNivelDeRiesgo();
	//$xAml->setVerificarDocumentosCompletos();
	//$xAml->setVerificarDocumentosVencidos();
	return $xAml->getMessages(OUT_HTML);
}

function jsaValidarPerfilTransaccional($persona){
	$xAml		= new cAMLPersonas($persona);
	$xAml->init();
	$validar	= false; //(MODO_DEBUG == true) ? true : false;
	$xAml->setVerificarPerfilTransaccional(false, $validar);
	$xAml->setVerificarOperacionesSemestrales();
	
	return $xAml->getMessages(OUT_HTML);
}

function jsaCumplimiento($idsocio){
	$xAl		= new cAml_alerts();
	$xlistas	= new cSQLListas();
	$sql		= $xlistas->getListadoDeAlertas(false, false, false, $idsocio);
	$xT			= new cTabla($sql);
	$xT->setKeyField( $xAl->getKey() );
	$xT->setKeyTable( $xAl->get() );
	return $xT->Show();	
}


function jsaAddDescuento($idpersona, $descuento){
	$xSoc		= new cSocio($idpersona); $xSoc->init();
	$xSoc->setMontoAhorroPreferente($descuento);
	return $xSoc->getMessages();
}

function jsaAddDescuentoDesdeEmpresa($idpersona, $descuento){
	$xSoc		= new cSocio($idpersona); $xSoc->init();
	$xSoc->setMontoAhorroPreferente($descuento);
	return $xSoc->getMessages();
}


function jsaSetEnviarParaAsociada($idpersona){
	$xSoc		= new cSocio($idpersona); $xSoc->init();
	$xSoc->setMontoAhorroPreferente(0);
	return $xSoc->getMessages();	
}

function jsaGetOperaciones($idpersona, $fecha){
	
}
function jsaGetListadoDeNominas($idempresa){
	$xEmp		= new cEmpresas($idEmpresa);
	$xF			= new cFecha();
	$ql			= new MQL();
	$xl			= new cSQLListas();	
}
function jsaActualizarEmpresa($idempresa){
	$xEmp	= new cEmpresas($idempresa);
	$xEmp->init();
	$xEmp->setActualizarPorPersona();
	return $xEmp->getMessages(OUT_HTML);
}

function jsaActualizarSucursal($idsucursal){
	$xSuc	= new cSucursal($idsucursal);
	if($xSuc->init() == true){
		$xSuc->setActualizarPorPersona();
	}
	return $xSuc->getMessages(OUT_HTML);
}
function jsaActualizarUsuario($idusuario){
	$xUser	= new cSystemUser($idusuario);	
	$xUser->setActualizarPorPersona();
	return $xUser->getMessages(OUT_HTML);
}
$jxc ->exportFunction('jsaRePatrimonio', array('idsocio' ), "#tab-4");
$jxc ->exportFunction('jsaReActividadE', array('idsocio' ), "#tab-3");
$jxc ->exportFunction('jsaReRelaciones', array('idsocio' ), "#tab-2");
$jxc ->exportFunction('jsaCumplimiento', array('idsocio' ), "#tab-6");
$jxc ->exportFunction('jsaReVivienda', array('idsocio' ), "#tab-1");
$jxc ->exportFunction('jsaValidarDocumentacion', array('idsocio' ), "#fb_extrasocios");
$jxc ->exportFunction('jsaValidarRiesgo', array('idsocio' ), "#fb_extrasocios");
$jxc ->exportFunction('jsaValidarPerfilTransaccional', array('idsocio' ), "#fb_extrasocios");

$jxc ->exportFunction('jsaAddDescuento', array('idsocio', 'iddescuento'), "#fb_extrasocios");
$jxc ->exportFunction('jsaAddDescuentoDesdeEmpresa', array('idmodificado', 'idcantidad'), "#fb_extrasocios");
$jxc ->exportFunction('jsaSetEnviarParaAsociada', array('idsocio' ), "#fb_extrasocios");
$jxc ->exportFunction('jsaActualizarEmpresa', array('idempresa' ), "#fb_extrasocios");
$jxc ->exportFunction('jsaActualizarSucursal', array('idsucursal' ), "#fb_extrasocios");
$jxc ->exportFunction('jsaActualizarUsuario', array('idusuario' ), "#fb_extrasocios");

$jxc ->process();

$xHP->addJsFile("../jsrsClient.js");

echo $xHP->getHeader();

echo $xJsB->setIncludeJQuery(); 

//$xJsB	= new jsBasicForm("extrasocios");
?>
<body>
<?php

if ( setNoMenorQueCero($idsocio) <= DEFAULT_SOCIO){
	$xFRM	= new cHForm("extrasocios", "socios.panel.frm.php");
	$xBtn	= new cHButton();
	$xTxt	= new cHText();
	
	$xFRM->setTitle( $xHP->getTitle() ); 
	$xFRM->addPersonaBasico();
	$xFRM->addSubmit();
	
	echo $xFRM->get();

} else {
	$xSoc 		= new cSocio($idsocio, true);
	
	$xHTabs		= new cHTabs();
	$xBtn		= new cHButton("");
	$oFrm		= new cHForm("extrasocios", "");
	$xHSel		= new cHSelect();
	$oFrm->OButton("TR.Recargar", "jsRecargar()", $oFrm->ic()->RECARGAR);
	$oFrm->addHTML( $xSoc->getFicha(true) );

	$oFrm->addPersonaComandos($idsocio);
	if(getEsModuloMostrado(USUARIO_TIPO_OFICIAL_CAPT) == true OR getEsModuloMostrado(USUARIO_TIPO_OFICIAL_CRED) == true){ 
		//Agregar otra opciones
		$oFrm->addToolbar( $xBtn->getBasic("TR.Actualizar Datos", "updateDat()", "editar", "edit-socio", false ) );
		$oFrm->addToolbar( $xBtn->getBasic("TR.Agregar Descuento Solicitado", "jsAddDescuento()", "dinero", "edit-descuento", false ) );
		$oFrm->OButton("TR.Reporte SIC", "jsGetCirculoDeCredito()", $xBtn->ic()->REPORTE);
		$oFrm->addToolbar( $xBtn->getBasic("TR.Enviar a Empresa Asociada", "jsaSetEnviarParaAsociada()", $xBtn->ic()->EXPORTAR , "edit-aasoc", false ) );
	}
	//===============================================================================	
	$setSql4	= $xql->getListadoDeNotas($idsocio);
	$c4Tbl = new cTabla($setSql4);
	$c4Tbl->setKeyField("idsocios_memo");
	$c4Tbl->addTool(2);
	
	$xHTabs->addTab($oFrm->lang("NOTAS"), $c4Tbl->Show()); //1
	$xHTabs->addTab("TR.DOMICILIO", "" );//2		
	$xHTabs->addTab(PERSONAS_TITULO_PARTES, ""); //3
	$xHTabs->addTab("TR.ACTIVIDAD_ECONOMICA", "" ); //tab4
	// Tabla de Relacion Patrimonial
	$xHTabs->addTab("TR.PATRIMONIO", ""); //tab5?
	//=======================================================================
	$cnt	= "";
	$xB		= new cBases();
	$mems	= ($xSoc->getEsPersonaFisica() == true) ? $xB->getMembers_InArray(false, BASE_DOCTOS_PERSONAS_FISICAS) : $xB->getMembers_InArray(false, BASE_DOCTOS_PERSONAS_MORALES);
	$rsDocs	= $ql->getDataRecord($xql->getListadoDePersonasDoctos($idsocio));
	$xTbl	= new cHTabla();

	foreach ($rsDocs as $rows){
		$iddocto				= $rows["archivo_de_documento"];
		$fecha					= $xF->getFechaByInt( $rows["fecha_de_carga"]);
		$xTbl->initRow();
		$xTbl->addTD($rows["tipo"]);
		$xTbl->addTD($xF->getFechaCorta($fecha));
		$xTbl->addTD($xBtn->getBasic($rows["archivo_de_documento"], "var xPers = new PersGen();xPers.getDocumento({persona:$idsocio, docto: '" . $rows["archivo_de_documento"] . "'});", $oFrm->ic()->TIPO) );
		$xTbl->addTD($rows["observaciones"]);
		$xTbl->endRow();
	}
	$xHTabs->addTab("TR.DOCUMENTOS", $xTbl->get()); //tabs
	if(getEsModuloMostrado(USUARIO_TIPO_OFICIAL_AML) == true){
		$xDiv3		= new cHDiv("tx1", "msgcumplimiento");
		$oFrm->addToolbar( $xBtn->getBasic("TR.validar documentos", "jsaValidarDocumentacion()", "documentos", "idvalidadoc", false  ) );
		$oFrm->addToolbar( $xBtn->getBasic("TR.validar perfil_transaccional", "jsaValidarPerfilT()", "perfil", "validaperfil", false ) );
		$oFrm->addToolbar( $xBtn->getBasic("TR.validar riesgo", "jsaValidarRiesgo()", "riesgo", "validariesgo", false ) );
		$oFrm->addToolbar( $xBtn->getBasic("TR.Actualizar Nivel de Riesgo", "jsActualizarNivelDeRiesgo($idsocio)", "riesgo", "actualizarriesgo", false ) );
		$oFrm->OButton("TR.ARBOL_DE_RELACIONES", "jsSigmaRelaciones()", $oFrm->ic()->EXPORTAR);
		$oFrm->OButton("TR.Consulta en LISTA_NEGRA", "var xAML = new AmlGen(); xAML.getConsultaListaNegra($idsocio)", $oFrm->ic()->REGISTROS);
		$xHTabs->addTab("TR.cumplimiento", $xDiv3->get()); //tab6
		$jsTabs	.= ",\n selected: 6\n";
		$xT		= new cTabla($xql->getListadoDePerfil($idsocio) );
		$xT->addTool(SYS_DOS);	
		$xHTabs->addTab("TR.perfil_transaccional", $xT->Show() ); //tab6
	}
	
	if($xSoc->getEsEmpresaConConvenio(true) == true){
		$xT2		= new cHTabs("idcomoempresa");
		$idempresa	= $xSoc->getOEmpresa()->getClaveDeEmpresa();
		$oFrm->addEmpresaComandos($idempresa);

		$xTCreds	= new cTabla($xql->getListadoDeCreditos(false, false, false, false, " AND (`creditos_solicitud`.`persona_asociada` = $idempresa) ", false), 2 );
		$xTPers		= new cTabla($xql->getListadoDeSocios(" (`socios_general`.`dependencia` = $idempresa)  ") );
		$xTAhorro	= new cTabla($xql->getListadoDeIncidenciasAhorro($idempresa));
		$xTPeriodo	= new cTabla($xql->getListadoDePeriodoPorEmpresa($idempresa) );
		$xTPeriodo->setTdClassByType();
		
		$xTPeriodo->setEventKey("var xG = new EmpGen(); xG.getOrdenDeCobranza");
		$xTCreds->setTdClassByType(); $xTPers->setTdClassByType(); $xTAhorro->setTdClassByType();
		
		$xModAhorro	= "<input type=\"number\" id=\"id" . HP_REPLACE_ID ."\" onblur=\"jsModificarAhorro(this," . HP_REPLACE_ID . ")\" />";
		$xTPers->addEspTool($xModAhorro);
		
		$xT2->addTab("TR.Trabajadores", $xTPers->Show());
		$xTCreds->setFootSum(array(8 => "saldo"));
		$xT2->addTab("TR.Creditos por empresa", $xTCreds->Show());
		//Ahorro por Empresa
		$xT2->addTab("TR.Ahorro por empresa", $xTAhorro->Show());
		$xT2->addTab("TR.Periodos de Empresa", $xTPeriodo->Show());
		$xHTabs->addTab("TR.empresa $idempresa", $xT2->get() ); //tab4
		$oFrm->OButton("TR.Cedula de Incidencias de Ahorro", "jsGetCedulaDeAhorro()", "deposito");
		$oFrm->OButton("TR.Actualizar Empresa", "jsaActualizarEmpresa()", $oFrm->ic()->EJECUTAR);
		$oFrm->OHidden("idempresa", $idempresa);
	}
	if($xSoc->getEsSucursal() == true){
		$oFrm->OButton("TR.Actualizar Sucursal", "jsaActualizarSucursal()", $oFrm->ic()->EJECUTAR);
		$oFrm->OHidden("idsucursal", $xSoc->getIDSucursalAsociada());
	}
	if($xSoc->getEsUsuario(true)){
		$oFrm->OButton("TR.Actualizar Usuario", "jsaActualizarUsuario()", $oFrm->ic()->EJECUTAR);
		$oFrm->OHidden("idusuario", $xSoc->getOUsuario()->getID());
	}
	//Agregar convenios
	$xTListaCreds	= new cTabla($xql->getListadoDeCreditos($idsocio), 2);
	$xTListaCreds->OButton("TR.Panel", "jsGoToPanelCredito(" . HP_REPLACE_ID . ")", $xTListaCreds->ODicIcons()->CONTROL);
	$xHTabs->addTab("TR.Creditos", $xTListaCreds->Show() );
	//agregar cuenta de ahorro
	$xTListaCapt	= new cTabla($xql->getListadoDeCuentasDeCapt($idsocio));
	$xHTabs->addTab("TR.Captacion", $xTListaCapt->Show() );
	//Actualizar Descuentos
	$xDiv2			= new cHDiv("inv", "iddivdescuento");
	$xFRM10 		= new cHForm("frmdescuento");
	$xFRM10->addSubmit("", "jsGuardarDescuento()", "jsCancelarAccion()");
	$xFRM10->OMoneda("iddescuento", 0, "TR.Monto");
	//======================================== AML

	/*Validacion*/
	if(MODO_DEBUG == true){	$xHTabs->addTab("TR.Validacion", $xSoc->getValidacion(OUT_HTML)); }
	
	$xDiv2->addHElem($xFRM10->get());
	$oFrm->addHTML($xHTabs->get());
	$oFrm->addHTML($xDiv2->get());
	
	$oFrm->OHidden("idsocio", $idsocio); $oFrm->OHidden("idmodificado", ""); $oFrm->OHidden("idcantidad", "0");
	$oFrm->addFooterBar("&nbsp;");
	
	echo $oFrm->get();
}
?>
</body>
<script>
var mSocio		= <?php echo  ($idsocio === false) ? "0" : $idsocio; ?>;
var xG			= new Gen();
var xPG			= new PersGen();

if (mSocio != 0) {
$(function() {
	$( "#tab" ).tabs({
			select: function(event, ui){
				selected = ui.panel.id;
					switch (selected){
					case 1:
						
						break;
					case "tab-1":
						jsaReVivienda();
						break;
					case "tab-2":
						jsaReRelaciones();
						break;
					case "tab-3":
						jsaReActividadE();
						break;
					case "tab-4":
						jsaRePatrimonio();
						break;
					case "tab-6":
						jsaCumplimiento();
						break;
				}
		    }<?php echo $jsTabs; ?>	
		});
});

}
function jsModificarAhorro(evt, idpersona){
	if(flotante(evt.value) >0 ){
		$("#idmodificado").val(idpersona);
		$("#idcantidad").val(flotante(evt.value));
		var siguarda	= confirm("DESEA GUARDAR EL DESCUENTO PREFERENTE POR " + evt.value);
		if(siguarda){ jsaAddDescuentoDesdeEmpresa();	}
	}
}
function addPatrim(){
	var srURL = "../frmsocios/frmsociospatrimonio.php?socio=<?php echo $idsocio; ?>";
	xG.w({ url: srURL, tiny : true });
}
function updateDat(){
	var srUp = "../frmsocios/frmupdatesocios.php?elsocio=<?php echo $idsocio; ?>";
	xG.w({ url: srUp, tiny : true });
}
function addHistorial(){
	var sDiv	= "<?php echo STD_LITERAL_DIVISOR; ?>";
	var srURL 	= "../frmsocios/frmhistorialdesocios.php?d=1" + sDiv + <?php echo $idsocio; ?> + sDiv + "1" + sDiv + "99" + sDiv + "NOTA_DEL_SOCIO" ;
	xG.w({ url: srURL, tiny : true });
}	
function jsVerificar(id){
	var URIL	= "../frmsocios/socios.verificacion.frm.php?t=d&s=" + mSocio +"&i=" + id;
	xG.w({ url: URIL, tiny : true });		
}
function jsVerificarAE(id){
	var URIL	= "../frmsocios/socios.verificacion.frm.php?t=t&s=" + mSocio +"&i=" + id;
	xG.w({ url: URIL, tiny : true });		
}
function jsUp(t, f, id) {
	var url = "../utils/frm8db7028bdcdf054882ab54f644a9d36b.php?t=" + t + "&f=" + f + "=" + id;
	xG.w({ url: url, tiny : true });
}
function jsDel(t, f, id) {
	var siXtar = confirm("Desea en Realidad Eliminar \n el Registro Seleccionado");
	if(siXtar==true){
		var sURL = "../utils/frm9d23d795f8170f495de9a2c3b251a4cd.php?t=" + t + "&f=" + f + "=" + id;
			delme = window.open(sURL, "", "width=300,height=300,scrollbars=yes,dependent");
			//delme.focus();
			document.getElementById("tr-" + t + "-" + id).innerHTML = "";
	} else {
			if( window.console ) { window.console.log( '' ); }
			window.statusText = "Operacion Cancelada";

	}
}
function jsAddDocumentos(){
	var sURL = "../frmsocios/personas_documentos.frm.php?persona=" + mSocio;
	xG.w({ url: sURL, tiny : true });
}
function jsToImage(uxl){
	var xrl		= "../frmsocios/documento.png.php?persona=" + uxl;
	xG.w({ url: xrl, tiny : true });  
}
function jsaValidarPerfilT(){	jsaValidarPerfilTransaccional();	}
function jsActualizarNivelDeRiesgo(id){	
	var xML = new AmlGen(); xML.goToCambiarNivel(id);
}
function jsCancelarAccion(){	$(window).qtip("hide");    }
function jsAddDescuento(){ getModalTip(window, $("#iddivdescuento"), xG.lang(["actualizar", "descuento"]));	}
function jsGuardarDescuento(){	jsaAddDescuento();	setTimeout("jsCancelarAccion()", 2000);	}
function jsGetCedulaDeAhorro(){
	var EmpG	= new EmpGen();
	var idemp	= '<?php echo $idempresa; ?>';
	EmpG.getCedulaAhorro(idemp);
}
function jsGetCirculoDeCredito(){
	var xrl		= "../rptlegal/circulo_de_credito.rpt.php?persona=" + mSocio;
	xG.w({ url: xrl, tiny : true });  
}
function jsGetOperaciones(){ 	}
function jsGoToPanelCredito(idx){ var xCred = new CredGen(); xCred.goToPanelControl(idx); }
function jsListaDeNominas(idnomina){ var EmpG	= new EmpGen(); EmpG.getOrdenDeCobranza(idnomina);	}
function jsRecargar(){ window.location = "socios.panel.frm.php?persona=" + mSocio; }

function jsGuardarReferencia(){
	var idrelacionado		= $("#idpersona").val();
	var idtipoderelacion	= $("#idtipoderelacion").val();
	var idtipodeparentesco	= $("#idtipodeparentesco").val();
	var stat				= $('#depende').prop('checked');
	xPG.addRelacion({ persona : mSocio, relacionado : idrelacionado, tipo : idtipoderelacion, parentesco : idtipodeparentesco, depende : stat, callback : jsGetRelaciones });
	$("#idpersona").val(0);
}
function jsGetRelaciones(){ jsaReRelaciones(); }
function jsSigmaRelaciones(){ 
	var xrl		= "../frmsocios/socios.relaciones.sigma.frm.php?persona=" + mSocio;
	xG.w({ url: xrl, tiny : true }); 	
}
</script>
<?php
echo $xJsB->get();
$jxc ->drawJavaScript(false, true);
?>
</html>
