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
$xHP		= new cHPage("TR.Generar Seguimiento");
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$jxc 		= new TinyAjax();
$jsCampo	= (CREDITO_USAR_OFICIAL_SEGUIMIENTO == true) ? "oficial_seguimiento" : "oficial_credito";

function jsaGetCreditos($convenio, $estatus, $periocidad, $oficial){
	$xLi			= new cSQLListas();
	$oficial		= setNoMenorQueCero($oficial);
	$ByOficial		= (CREDITO_USAR_OFICIAL_SEGUIMIENTO == true) ? "	AND	(`creditos_solicitud`.`oficial_seguimiento` != $oficial) " : "	AND	(`creditos_solicitud`.`oficial_credito` != $oficial) ";
	$sqlCred		= (CREDITO_USAR_OFICIAL_SEGUIMIENTO == true) ? $xLi->getListadoDeCreditosConOficialSeguimiento(false, $estatus, $periocidad, $convenio, $ByOficial) : $xLi->getListadoDeCreditosConOficial(false, $estatus, $periocidad, $convenio, $ByOficial);

	$xTbl = new cTabla($sqlCred, 2);
	$xChk			= new cHCheckBox();
	$xTbl->setTdClassByType();
	$xTbl->OButton("TR.Notificacion", "var xC=new CredGen();xC.setAgregarNotificacion(" . HP_REPLACE_ID . ");", $xTbl->ODicIcons()->AVISO);
	$xTbl->OButton("TR.Llamada", "var xC=new CredGen();xC.setAgregarLlamada(" . HP_REPLACE_ID . ");", $xTbl->ODicIcons()->TELEFONO);
	$xTbl->OButton("TR.COMPROMISO", "var xC=new CredGen();xC.setAgregarCompromiso(" . HP_REPLACE_ID . ");", $xTbl->ODicIcons()->GRUPO);
	$xTbl->OButton("TR.Nota", "var xC=new CredGen();xC.setNuevaNota(" . HP_REPLACE_ID . ");", $xTbl->ODicIcons()->NOTA);
	$xTbl->OButton("TR.Panel", "var xC=new CredGen();xC.goToPanelControl(" . HP_REPLACE_ID . ");", $xTbl->ODicIcons()->CONTROL);
	
	//$xTbl->addEspTool($xChk->get("", "chk" . STD_LITERAL_DIVISOR . "_REPLACE_ID_") );
	//$xTbl->setWidth();
	return $xTbl->Show();
}
function jsaGetLetrasVencidas($fecha, $producto){
	$xD		= new cFecha();
	$xL		= new cSQLListas();
	$xVis	= new cSQLVistas();
	$xFil	= new cSQLFiltros();
	
	$fecha 	= $xD->getFechaISO($fecha);
	
	$BySaldo		= $xFil->CreditosPorSaldos(TOLERANCIA_SALDOS, ">");
	//Agregar seguimiento
	$BySaldo		= $BySaldo . $xFil->CreditosProductosPorSeguimiento(0);
	$BySaldo		= $BySaldo . " AND (`letras`.`total_sin_otros` >0) ";
	
	//TODO: Corregir echale
	
	$sql			= $xL->getListadoDeLetrasPendientesReporteAcumV101($BySaldo, TASA_IVA, true, false, $producto);
	
	//setLog($sql);
	
	$xT		= new cTabla($sql, 2, "idtblletrasyavencidas");
	
	$xT->setKeyTable("creditos_solicitud");
	$xT->setKeyField("credito");
	
	//$xT->setOmitidos("persona");
	$xT->setUsarNullPorCero();
	$xT->setEventKey("var xC=new CredGen();xC.goToPanelControl");
	$xT->setWithMetaData();
	//$xT->setKeyField("credito");
	
	//$xT->setOmitidos("monto_ministrado");
	$xT->setForzarTipoSQL("dias", MQL_INT);
	
	$xT->setTitulo("numero_con_atraso", "NUMERO");
	$xT->setTitulo("fecha_de_atraso", "FECHA");
	$xT->setTitulo("letra_original", "original");
	
	if(MODULO_SEGUIMIENTO_ACTIVADO == false){
		$xT->setOmitidos("seguimiento");
		$xT->setOmitidos("causamora");
	} else {
		$xT->setResumidos("seguimiento");
		$xT->setResumidos("causamora");
	}
	//$xT->setResumidos("nombre");
	$xT->setColSum("monto_ministrado");
	$xT->setColSum("capital");
	$xT->setColSum("historial");
	$xT->setColSum("letra_original");
	$xT->setColSum("total");
	
	//$xT->setOmitidos("persona");
	$xT->setOmitidos("capital");
	$xT->setOmitidos("interes");
	$xT->setOmitidos("iva");
	$xT->setOmitidos("otros");
	$xT->setOmitidos("iva_moratorio");
	$xT->setOmitidos("moratorio");
	
	$xT->setColTitle("total", "Con Mora");
	$xT->setColTitle("original", "Monto Original");
	$xT->OCheckBox("jsAddColaTareas(" . HP_REPLACE_ID . ")", "credito", "chk");
	//$xT->setResumidos("iva");

	if(getEsModuloMostrado(USUARIO_TIPO_OFICIAL_CRED, MMOD_SEGUIMIENTO) == true){
		$xT->OButton("TR.LLAMADA", "var xC=new CredGen();xC.setAgregarLlamada(" . HP_REPLACE_ID . ")", $xT->ODicIcons()->TELEFONO);
		$xT->OButton("TR.TAREAS", "var xC=new CredGen();xC.setAgregarCompromiso(" . HP_REPLACE_ID . ")", $xT->ODicIcons()->TAREA);
		$xT->OButton("TR.SMS", "var xC=new CredGen();xC.setAgregarNotificacion(" . HP_REPLACE_ID . ", '$fecha')", $xT->ODicIcons()->NOTA);
	}
	return $xT->Show();
}
$jxc ->exportFunction('jsaGetLetrasVencidas', array('idfechaactual','idproducto'), "#id-listado-de-creditos");
$jxc ->exportFunction('jsaGetCreditos', array('idproducto', 'idestado', 'idperiocidad', 'idoficial'), "#id-listado-de-creditos");
$jxc ->process();

$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

//$xHP->addTabulatorSuport();
$xHP->init();

$xFRM			= new cHForm("frmGenerarSeguimiento", "./");
$xFRM->setTitle($xHP->getTitle());

$xSel			= new cHSelect();
$msg			= "";

$xFRM->addFecha();

$xsLO			= $xSel->getListaDeOficialesConCredito(); $xsLO->addTodas(true);
$xsLP			= $xSel->getListaDeProductosDeCreditoConSeguimiento(); $xsLP->addTodas(true);

$xFRM->addHElem($xsLO->get(true) );
$xFRM->addHElem($xsLP->get(true) );
$xSEstat		= $xSel->getListaDeEstadosDeCredito();
$xSEstat->addEspOption(SYS_TODAS, SYS_TODAS);
$xSEstat->setOptionSelect(SYS_TODAS);
$xFRM->addHElem( $xSEstat->get(true) );
$xSPer			= $xSel->getListaDePeriocidadDePago();
$xSPer->addEspOption(SYS_TODAS, SYS_TODAS);
$xSPer->setOptionSelect(SYS_TODAS);
$xFRM->addHElem( $xSPer->get(true));
$xFRM->addHTML("<div id='id-listado-de-creditos'></div>");

$xFRM->addGuardar("jsSetOficial()");
$xFRM->OButton("TR.Obtener", "jsaGetCreditos()", $xFRM->ic()->EJECUTAR, "idgetcreditos", "blue2");

//$xFRM->OButton("TR.Pagos DEL DIA", "jsaGetLetrasAVencer()", $xFRM->ic()->REPORTE4, "idletrapagosvencs");
$xFRM->OButton("TR.LETRASVENC", "jsaGetLetrasVencidas()", $xFRM->ic()->REPORTE5, "idletravencs");

//$xFRM->OButton("TR.Guardar", "jsSetOficial()", $xFRM->ic()->GUARDAR);

//$xFRM->OButton("TR.Cargar Archivo", "jsCargarArchivo()", $xFRM->ic()->EXPORTAR);
//$xFRM->addJsBasico();
//$xFRM->addCreditBasico();
//$xFRM->addSubmit();
echo $xFRM->get();
$jxc ->drawJavaScript(false, true);
?>
<script>
var Frm 					= document.frmAsignarOficiales;
var divLiteral				= STD_LITERAL_DIVISOR;
var xGen					= new Gen();
var xG						= new Gen();
var ordenCbza				= {};
ordenCbza.items				= 0;
ordenCbza.fails				= 0;

var fld						= "<?php echo $jsCampo; ?>";
var arrTareas				= [];

function jsSetOficial(){
	var vOficial		= $("#idoficial").val();
	$('.coolCheck input:checked').each(function() {
	    var mID			= $(this).attr('id');
		var aID			= mID.split(divLiteral);
		var cred		= entero(aID[1]);
		xGen.save({tabla: "creditos_solicitud", id : cred, content : fld + "=" +  vOficial});		    
	});		
  	//document.getElementById("PMsg").innerHTML = "";
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
function jsCargarArchivo(){	xGen.w({ url : "../frmseguimiento/creditos_oficiales.upload.frm.php?", tiny : true, w: 800 }); }
function jsTest(){ $("#sqltable").tabulator({fitColumns:true}); }

function jsAddColaTareas(id){
	var cid	= "chk-" + id;
	
	if(document.getElementById(cid).checked == true){
		ordenCbza["tr-creditos_solicitud-" +  id] = 1;
		xG.alerta({msg: "Agregar el Credito # " + id});
		xG.markTR({src:"#" + cid});
	} else {
		delete ordenCbza["tr-creditos_solicitud-" +  id];
		xG.alerta({msg: "Quitar el Credito # " + id, tipo:"warn"});
		xG.markTR({src:"#" + cid});
	}

}

</script>
<?php
$xHP->fin();
?>