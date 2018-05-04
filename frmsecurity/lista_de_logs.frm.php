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
$xHP		= new cHPage("Logs del Sistema", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$jxc 		= new TinyAjax();

function jsaGetFiles($uLike){
	//Crea Archivos a partir de un Read de Files en el Archivo
	//principal
	$xT			= new cHTabla();
	$d 			= dir(PATH_TMP);
	
	$uLike		= trim($uLike);
	
	while($entry = $d->read()) {
		
		
		$OName 		= $entry;
		$siTxt 		= strpos($OName, ".txt");
		$siLog		= strpos($OName, "log");
		$i			= 0;
		if ($siTxt === false OR $siLog === false){
			
		} else {
			$dobj		= $OName;
			
			//setLog($dobj);
			$mFile 		= str_replace(".txt", "", $dobj);
			$xF			= new cFileLog($mFile);
			
			if($uLike === ""){
				$xT->initRow();
				$xT->addTD($xF->getLinkDownload($mFile));
				$xT->endRow();
				
				$i++;
			} else {
				if ( strpos($mFile, $uLike) !== false ){
					$xT->initRow("success");
					$xT->addTD($xF->getLinkDownload($mFile));
					$xT->endRow();
					
					$i++;
				}
			}
			if ($i > 25){
				break;
			}

			
		}
		
	}
	$d->close();
	return	$xT->get();
}
$jxc ->exportFunction('jsaGetFiles', array('idBuscar'), "#idLsFiles");
//$tab = new TinyAjaxBehavior();
//$tab -> add(TabSetValue::getBehavior("idide", $x));
//return $tab -> getString();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
$jxc ->process();


$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);$action	= strtolower($action);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa		= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo			= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria 	= parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);
$observaciones	= parametro("idobservaciones");

$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xFRM->setNoAcordion();

$xFRM->addEnviar("TR.EJECUTAR", "jsaGetFiles()");

$xFRM->OText("idBuscar", "", "TR.BUSCAR");


$xFRM->addSeccion("idbusq", "TR.RESULTADOS");
$xFRM->addHElem("<div class='solo' id='idLsFiles'></div>");
$xFRM->endSeccion();

echo $xFRM->get();
?>
<script>

</script>
<?php
$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>