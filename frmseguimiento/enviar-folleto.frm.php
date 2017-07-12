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
$xHP		= new cHPage("TR.ENVIAR MERCADEO", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$jxc 		= new TinyAjax();
$tab 		= new TinyAjaxBehavior();

//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();

function jsaGetListaDePersonas($campannia, $producto, $crear, $pendiente){
	$xT			= new cTipos();
	$campannia	= setNoMenorQueCero($campannia);
	$crear		= $xT->cBool($crear);
	$pendiente	= $xT->cBool($pendiente);

	if($crear == true){
		$sql	= "SELECT   `socios_general`.*, `creditos_solicitud`.*
		FROM `socios_general` INNER JOIN `creditos_solicitud`  ON `socios_general`.`codigo` = `creditos_solicitud`.`numero_socio` 
		WHERE (`creditos_solicitud`.`saldo_actual` > " . TOLERANCIA_SALDOS . " ) AND ( `creditos_solicitud`.`tipo_convenio` = $producto ) AND `correo_electronico` REGEXP '^[^@]+@[^@]+\.[^@]{2,}$' ";
		
		$xQL	= new MQL();
		$rs		= $xQL->getDataRecord($sql);
		$tiempo	= time();
		
		foreach ($rs as $rw){
			$persona	= $rw["codigo"];
			$D			= $xQL->getDataRow("SELECT * FROM `mercadeo_envios` WHERE `persona`=$persona AND `campana`=$campannia");
			if(isset($D["estatus"])){
				 if($D["estatus"] == 1){ //pendiente de envio
				 	//$xQL->setRawQuery("DELETE FROM `mercadeo_envios` WHERE `persona`=$persona AND `campana`=$campannia");
				 } else {
				 	//setError();
				 }
				 //si no, no se elimina nada
			} else {
				$xQL->setRawQuery("INSERT INTO `mercadeo_envios`(`persona`,`tiempo`,`campana`) VALUES ($persona, $tiempo, $campannia)");
			}
			
			
		}
	}
	$ByPend		= ($pendiente == true) ? " AND (`mercadeo_envios`.`estatus`=1) " : "";
	$xLi		= new cSQLListas();
	$sql		= "SELECT   `mercadeo_envios`.`idmercadeo_envios` AS `clave`,
				`socios_general`.`codigo`,
		`socios_tipoingreso`.`descripcion_tipoingreso` AS `tipo_de_ingreso`,
         TRIM(CONCAT(`socios_general`.`nombrecompleto`,' ',
         `socios_general`.`apellidopaterno`,' ',
         `socios_general`.`apellidomaterno`)) AS `nombre`,
         `socios_general`.`correo_electronico` AS `email`,
         `socios_general`.`telefono_principal` AS `telefono`,
         IF(`mercadeo_envios`.`estatus` = 0, 'ENVIADO', 'PENDIENTE') AS `estatus`
FROM     `socios_general` 
INNER JOIN `mercadeo_envios`  ON `socios_general`.`codigo` = `mercadeo_envios`.`persona` 
INNER JOIN `socios_tipoingreso`  ON `socios_general`.`tipoingreso` = `socios_tipoingreso`.`idsocios_tipoingreso` 

WHERE  ( `mercadeo_envios`.`campana` = $campannia ) $ByPend ";
	
	$xChk	= new cHCheckBox();
	
	$xTbl	= new cTabla($sql);
	//$xTbl->setWithMetaData();
	//$xTbl->addEspTool($xChk->get(""))
	//$xTbl->OButton("TR.Enviar", "jsEnviarMercadeo(" . HP_REPLACE_ID . ")", $xTbl->ODicIcons()->EJECUTAR);
	
	$xTbl->OCheckBox("jsEnviarMercadeo(" . HP_REPLACE_ID . ")", "estatus", "chkid");
	
	return $xTbl->Show();
}

$jxc ->exportFunction('jsaGetListaDePersonas', array('idcampana', 'idproducto', 'addpersona', 'solopendiente'), "#lista_de_personas");

$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto		= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo		= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa	= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo		= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria = parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);

$observaciones= parametro("idobservaciones");

$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());



$xFRM->addSeccion("idopt", "TR.Opciones");

$xFRM->addHElem( $xSel->getListadoGenerico("mercadeo_campana", "idcampana")->get("TR.CAMPANNIA", true) );
//$xFRM->addHElem( $xSel->getListaDeTiposDeIngresoDePersonas("tipoingreso")->get("TR.TIPO_DE PERSONA", true) );
$xFRM->addHElem( $xSel->getListaDeProductosDeCredito("idproducto", false, true)->get(true) );
$xFRM->OCheck("TR.AGREGAR PERSONAS A CAMPANNIA", "addpersona");
$xFRM->OCheck("TR.SOLO PENDIENTE", "solopendiente");

$xFRM->endSeccion();

$xFRM->addSeccion("idlista", "TR.LISTA DE PERSONAS");
$xFRM->addHTML("<div id='lista_de_personas'></div>");
$xFRM->endSeccion();

$xFRM->OButton("TR.Obtener", "jsaGetListaDePersonas()", $xFRM->ic()->EJECUTAR);
$xFRM->OButton("TR.NUEVA CAMPANNIA", "jsNuevaCampannia()", $xFRM->ic()->EJECUTAR);
$xFRM->OButton("TR.ENVIAR CAMPANNIA", "jsEnviarCampannia()", $xFRM->ic()->EJECUTAR);

echo $xFRM->get();
?>
<script>
var xG		= new Gen();
function jsEnviarCampannia(){
	
}
function jsEnviarMercadeo(id){
	//var dd	= processMetaData("#tr-mercadeo_envios-" + id);
	var idsi	= $('#chkid-' + id).prop('checked');
	if(idsi == true){
	//if(typeof dd.clave !== "undefined"){
		var mUrl	= "mercadeo-envia-folleto.svc.php?clave=" + id;
		xG.svc({url: mUrl, callback: jsResponse});
		xG.spinInit();
	//}
	}
}
function jsResponse(dd){
	xG.spinEnd();
	if(dd.error == true){
		xG.alerta({msg:dd.message, nivel:"error"});
		
	} else {
		var id = dd.clave;
		$("#tr-mercadeo_envios-" + id).addClass("tr-pagar");
		//$("#chkid-" + id).parent().addClass("tr-pagar");
	}
}
</script>
<?php

$jxc ->drawJavaScript(false, true);


$xHP->fin();
?>