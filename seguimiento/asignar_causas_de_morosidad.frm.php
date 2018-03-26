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
$xHP		= new cHPage("TR.Asignar CAUSAMORA");
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$jxc 		= new TinyAjax();
$jsCampo	= "causa_de_mora";

function jsaGetCreditos($convenio, $estatus, $periocidad, $oficial){
	$xLi		= new cSQLListas();
	$xFil		= new cSQLFiltros();

	$otros		= "";
	$ByProd		= $xFil->CreditosPorProducto($convenio);
	$ByEstat	= $xFil->CreditosPorEstado($estatus);
	$ByOficial	= $xFil->CreditosPorOficial($oficial);
	$ByPeriod	= $xFil->CreditosPorFrecuencia($periocidad);
	
	$sqlCred	= "SELECT
				`creditos_solicitud`.`numero_socio` AS `persona`, `socios`.`nombre`,
				`creditos_solicitud`.`numero_solicitud`                  AS `credito`,
				CONCAT( `creditos_estatus`.`descripcion_estatus`, '-', 
					`creditos_tipoconvenio`.`descripcion_tipoconvenio`)
					AS `producto`,
				
				`creditos_periocidadpagos`.`descripcion_periocidadpagos` AS `periocidad`,
				
				CONCAT(`creditos_solicitud`.`ultimo_periodo_afectado`, '/', `creditos_solicitud`.`pagos_autorizados`) AS 'periodo',
				`creditos_solicitud`.`fecha_ministracion`                AS `otorgado`,
				`creditos_solicitud`.`fecha_vencimiento`                 AS `vencimiento`,
				`creditos_solicitud`.`monto_autorizado`                  AS `monto`,
				`creditos_solicitud`.`saldo_actual`                      AS `saldo`,
				`creditos_estatus`.`descripcion_estatus` AS `estatus`,
				`creditos_causa_de_vencimientos`.`descripcion_de_la_causa` AS `causamora`
FROM     `creditos_solicitud` 
INNER JOIN `socios`  ON `creditos_solicitud`.`numero_socio` = `socios`.`codigo` 
INNER JOIN `creditos_periocidadpagos`  ON `creditos_solicitud`.`periocidad_de_pago` = `creditos_periocidadpagos`.`idcreditos_periocidadpagos` 
INNER JOIN `creditos_tipoconvenio`  ON `creditos_solicitud`.`tipo_convenio` = `creditos_tipoconvenio`.`idcreditos_tipoconvenio` 
INNER JOIN `creditos_estatus`  ON `creditos_solicitud`.`estatus_actual` = `creditos_estatus`.`idcreditos_estatus` 
INNER JOIN `creditos_causa_de_vencimientos`  ON `creditos_solicitud`.`causa_de_mora` = `creditos_causa_de_vencimientos`.`idcreditos_causa_de_vencimientos`
WHERE (`creditos_solicitud`.`saldo_actual` >= " . TOLERANCIA_SALDOS . " ) $ByEstat $ByOficial $ByProd $ByPeriod
				ORDER BY
					`creditos_solicitud`.`saldo_actual` DESC,
					`creditos_solicitud`.`fecha_ministracion`,
					
					`creditos_solicitud`.`fecha_vencimiento`
";

	$xTbl = new cTabla($sqlCred, 2);
	$xChk			= new cHCheckBox();
	$xTbl->setTdClassByType();
	$xTbl->addEspTool($xChk->get("", "chk" . STD_LITERAL_DIVISOR . "_REPLACE_ID_") );
	
	$xTbl->setWidth();
	$xTbl->setWithMetaData();
	
	return $xTbl->Show();
}

function jsaGetLetrasVencidas($producto, $estatus, $periocidad, $oficial){
	$xD		= new cFecha();
	$xL		= new cSQLListas();
	$fecha 	= $xD->getFechaISO($fecha);
	$xFil	= new cSQLFiltros();
	
	$BySaldo		= $xFil->CreditosPorSaldos(TOLERANCIA_SALDOS, ">");
	//Agregar seguimiento
	$BySaldo		= $BySaldo . $xFil->CreditosProductosPorSeguimiento(0);
	$BySaldo		= $BySaldo . " AND (`letras`.`total_sin_otros` >0) ";
	
	//TODO: Corregir echale
	
	$sql	= $xL->getListadoDeLetrasPendientesReporteAcumV101($BySaldo, TASA_IVA, true, false, $producto, $periocidad);
	
	//setLog($sql);
	
	$xT		= new cTabla($sql, 2);
	//$xT->setEventKey("jsGoPanel");
	$xT->setWithMetaData();
	
	
	$xT->setOmitidos("capital");
	$xT->setOmitidos("interes");
	$xT->setOmitidos("otros");
	$xT->setOmitidos("ahorro");
	$xT->setOmitidos("iva");
	$xT->setOmitidos("dias");
	$xT->setOmitidos("monto_ministrado");
	
	$xT->setTitulo("numero_con_atraso", "NUMERO");
	$xT->setTitulo("fecha_de_atraso", "FECHA");
	
	$xT->setForzarTipoSQL("dias", MQL_INT);
	
	$xChk			= new cHCheckBox();
	$xT->setTdClassByType();
	$xT->addEspTool($xChk->get("", "chk" . STD_LITERAL_DIVISOR . "_REPLACE_ID_") );
	
	return $xT->Show( );
}


$jxc ->exportFunction('jsaGetCreditos', array('idproducto', 'idestado', 'idperiocidad', 'idoficial'), "#id-listado-de-creditos");
$jxc ->exportFunction('jsaGetLetrasVencidas', array('idproducto', 'idestado', 'idperiocidad', 'idoficial'), "#id-listado-de-creditos");
$jxc ->process();

$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);


$xHP->init();

$xFRM			= new cHForm("frmAsignarOficiales", "./");
$xFRM->setNoAcordion();
$xFRM->setTitle($xHP->getTitle());
$xSel			= new cHSelect();
$msg			= "";
$xFRM->addSeccion("idopt", "TR.Opciones");
$xFRM->addHElem($xSel->getListaDeProductosDeCredito("", false, true)->get(true) );
$xSEstat		= $xSel->getListaDeEstadosDeCredito();
$xSEstat->addEspOption(SYS_TODAS, SYS_TODAS);
$xSEstat->setOptionSelect(SYS_TODAS);
$xFRM->addHElem( $xSEstat->get(true) );
$xSPer			= $xSel->getListaDePeriocidadDePago();
$xSPer->addEspOption(SYS_TODAS, SYS_TODAS);
$xSPer->setOptionSelect(SYS_TODAS);
$xFRM->addHElem( $xSPer->get(true));

$xSlo	= $xSel->getListaDeOficiales("");
$xSlo->addEspOption(SYS_TODAS, SYS_TODAS);
$xSlo->setOptionSelect(SYS_TODAS);
$xFRM->addHElem($xSlo->get(true) );

$xFRM->endSeccion();

$xFRM->addSeccion("idofi", "TR.CAUSAMORA");
$xFRM->addHElem( $xSel->getListaDeCausaMoraCred()->get(true) );

$xChk	= new cHCheckBox();
$xChk->addEvent("jsMarkAll()", "onchange");
$xFRM->addHElem($xChk->get("TR.TODOS", "idmarktodos"));


$xFRM->endSeccion();
$xFRM->addSeccion("idlista", "TR.LISTA DE CREDITOS");
$xFRM->addHTML("<div id='id-listado-de-creditos'></div>");
$xFRM->endSeccion();

$xFRM->OButton("TR.CREDITOS", "jsaGetCreditos()", $xFRM->ic()->EJECUTAR);
$xFRM->OButton("TR.LETRASVENC", "jsaGetLetrasVencidas()", $xFRM->ic()->EJECUTAR);

$xFRM->OButton("TR.Guardar", "jsSetCausa()", $xFRM->ic()->GUARDAR);


echo $xFRM->get();
$jxc ->drawJavaScript(false, true);
?>
<script>
var Frm 					= document.frmAsignarOficiales;
var divLiteral				= STD_LITERAL_DIVISOR;
var xGen					= new Gen();
var fld						= "<?php echo $jsCampo; ?>";
function jsSetCausa(){
	var vCausa			= $("#idtipodecausa").val();
	$('.coolCheck input:checked').each(function() {
		//var dd			= processMetadata
	    var mID			= $(this).attr('id');
		var aID			= mID.split(divLiteral);
		var cred		= entero(aID[1]);
		xGen.save({tabla: "creditos_solicitud", id : cred, content : fld + "=" +  vCausa});		    
	});		
  	//document.getElementById("PMsg").innerHTML = "";
}
function jsEchoMsg(msg){ xGen.alerta({msg:msg}); }
function jsMarkAll(){
	var isLims 			= Frm.elements.length - 1;
	var isP				= $("#idmarktodos").prop("checked");
	
	for(i=0; i<=isLims; i++){
		var mTyp 	= Frm.elements[i].getAttribute("type");
		var mID 	= Frm.elements[i].getAttribute("id");
		
		//Verificar si es mayor a cero o no nulo
		if ( (mID!=null) && (mID.indexOf("chk@")!= -1) && (mTyp == "checkbox") ) {
			if(isP == true){
				document.getElementById(mID).checked	= true;
			} else {
				document.getElementById(mID).checked	= false;
			}

		}
	}
}


</script>
<?php
$xHP->fin();

?>