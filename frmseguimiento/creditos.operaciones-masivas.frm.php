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
$xHP		= new cHPage("TR.Operaciones Masivas de Credito");
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$jxc 		= new TinyAjax();
$jsCampo	= (CREDITO_USAR_OFICIAL_SEGUIMIENTO == true) ? "oficial_seguimiento" : "oficial_credito";

function jsaGetCreditos($convenio, $estatus, $periocidad){
	$xLi		= new cSQLListas();
	$sqlCred	=  $xLi->getListadoDeCreditos(false, false, $estatus, $convenio);

	$xTbl 		= new cTabla($sqlCred, 2);
	$xChk		= new cHCheckBox();
	$xTbl->setTdClassByType();
	//$xTbl->setWithMetaData(true);
	$xTbl->OButton("TR.GENERAR PLAN_DE_PAGOS", "jsGetPlanDePagos(_REPLACE_ID_);", $xTbl->ODicIcons()->CALENDARIO);
	$xTbl->addSubQuery("SELECT * FROM `operaciones_recibos` WHERE `tipo_docto`=" . RECIBOS_TIPO_PLAN_DE_PAGO, "docto_afectado", "<mark>PLAN DE PAGOS {{idoperaciones_recibos}}<mark>");
	return $xTbl->Show();
}
function jsaGetCreditosVariados($convenio, $estatus, $periocidad){
	
	$xQL	= new MQL();
	$xQL->setRawQuery("CALL `proc_creds_prox_letras`()");
	
	$sql	= "SELECT
			`tmp_creds_prox_letras`.`docto_afectado` AS `credito`,
			`creditos_solicitud`.`numero_socio`      AS `persona`,
			`creditos_solicitud`.`saldo_actual`		AS `capital`,
			SUM(`tmp_creds_prox_letras`.`capital`) AS `capital_plan`,
			(`creditos_solicitud`.`saldo_actual` -	SUM(`tmp_creds_prox_letras`.`capital`))   AS `diferencia`,
			if( (`creditos_solicitud`.`saldo_actual` >	SUM(`tmp_creds_prox_letras`.`capital`)), 'El Plan Menor al Saldo', 'El Plan Mayor al Saldo') AS `observaciones`  
		FROM
			`creditos_solicitud` `creditos_solicitud` 
				INNER JOIN `tmp_creds_prox_letras` `tmp_creds_prox_letras` 
				ON `creditos_solicitud`.`numero_solicitud` = `tmp_creds_prox_letras`.
				`docto_afectado` 
		WHERE `creditos_solicitud`.`periocidad_de_pago`!=360
		AND `creditos_solicitud`.`saldo_actual` > " . TOLERANCIA_SALDOS . "
			GROUP BY
				`tmp_creds_prox_letras`.`docto_afectado`
			
		HAVING
		 diferencia >0.99 OR  diferencia < -0.99 ORDER BY diferencia DESC ";
	$xTbl 		= new cTabla($sql, 0);
	$xTbl->OButton("TR.PANEL", "var xG=new CredGen();xG.goToPanelControl(_REPLACE_ID_);", $xTbl->ODicIcons()->CONTROL);
	$xTbl->OButton("TR.GENERAR PLAN_DE_PAGOS", "jsGetPlanDePagos(_REPLACE_ID_);", $xTbl->ODicIcons()->CALENDARIO);
	return $xTbl->Show();
}

$jxc ->exportFunction('jsaGetCreditos', array('idproducto', 'idestado', 'idperiocidad', 'idoficial'), "#id-listado-de-creditos");
$jxc ->exportFunction('jsaGetCreditosVariados', array('idproducto', 'idestado', 'idperiocidad', 'idoficial'), "#id-listado-de-creditos");
$jxc ->exportFunction('jsaSetLetrasPends', array('idproducto', 'idestado', 'idperiocidad', 'idoficial'), "#id-listado-de-creditos");
$jxc ->process();

$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);


$xHP->init();

$xFRM			= new cHForm("frmAsignarOficiales", "./");
$xFRM->setTitle($xHP->getTitle());
$xSel			= new cHSelect();
$msg			= "";
$xFRM->addHElem($xSel->getListaDeProductosDeCredito()->get(true) );
$xSEstat		= $xSel->getListaDeEstadosDeCredito();
$xSEstat->addEspOption(SYS_TODAS, SYS_TODAS);
$xSEstat->setOptionSelect(SYS_TODAS);
$xFRM->addHElem( $xSEstat->get(true) );
$xSPer			= $xSel->getListaDePeriocidadDePago();
$xSPer->addEspOption(SYS_TODAS, SYS_TODAS);
$xSPer->setOptionSelect(SYS_TODAS);
$xFRM->addHElem( $xSPer->get(true));
$xFRM->addHTML("<div id='id-listado-de-creditos'></div>");
$xFRM->OButton("TR.Obtener", "jsaGetCreditos()", $xFRM->ic()->EJECUTAR);
$xFRM->OButton("TR.DESCUADRE", "jsaGetCreditosVariados()", $xFRM->ic()->EJECUTAR);
//$xFRM->OButton("TR.Actualizar", "jsaSetLetrasPends()", $xFRM->ic()->EJECUTAR);
echo $xFRM->get();
$jxc ->drawJavaScript(false, true);
?>
<script>
var Frm 					= document.frmAsignarOficiales;
var divLiteral				= STD_LITERAL_DIVISOR;
var xGen					= new Gen();
var fld						= "<?php echo $jsCampo; ?>";
function jsSetOficial(){
	var vOficial		= $("#idoficial").val();
	$('.coolCheck input:checked').each(function() {
	    var mID			= $(this).attr('id');
		var aID			= mID.split(divLiteral);
		var cred		= entero(aID[1]);
		//xGen.save({tabla: "creditos_solicitud", id : cred, content : fld + "=" +  vOficial});		    
	});		
  	//document.getElementById("PMsg").innerHTML = "";
}
function jsGetPlanDePagos(idCredito){
	//var gURL = "../frmcreditos/frmcreditosplandepagos.php?r=1&credito=" + idCredito;
	//var mObj	= processMetaData("#tr-creditos_solicitud-" + idCredito);
	$("#tr-creditos_solicitud-" + idCredito).removeClass(); $("#tr-creditos_solicitud-" + idCredito).addClass("tr-pagar");
	var gURL = "../frmcreditos/plan_de_pagos.frm.php?auto=true&credito=" + idCredito;
	var xGen	= new Gen(); xGen.w({ url : gURL, full:true });
	$("#options-" + idCredito).parent().addClass("tr-plan");
}
function jsEchoMsg(msg){ xGen.alerta({msg:msg}); }
function jsMarkAll(){
	var isLims 			= Frm.elements.length - 1;
	var vOficial		= Frm.cOficial.value;
	for(i=0; i<=isLims; i++){
		var mTyp 	= Frm.elements[i].getAttribute("type");
		var mID 	= Frm.elements[i].getAttribute("id");
		//Verificar si es mayor a cero o no nulo
		if ( (mID!=null) && (mID.indexOf("chk@")!= -1) && (mTyp == "checkbox") ) {
			if ( document.getElementById(mID).checked) {
				document.getElementById(mID).checked = false;
			} else {
				document.getElementById(mID).checked = true;
			}
		}
	}
}
//function jsCargarArchivo(){	xGen.w({ url : "../frmseguimiento/creditos_oficiales.upload.frm.php?", tiny : true, w: 800 }); }
</script>
<?php
$xHP->fin();
?>