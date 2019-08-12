<?php
/**
 * Modulo de API
 * @author Balam Gonzalez Luis Humberto
 * @version 0.0.01
 * @package api
 */
//=====================================================================================================
include_once("../core/go.login.inc.php");
include_once("../core/core.error.inc.php");
include_once("../core/core.html.inc.php");
include_once("../core/core.init.inc.php");
include_once("../core/core.db.inc.php");
include_once("../core/core.reportes.inc.php");
require_once('../vendor/autoload.php');

$theFile			= __FILE__;
$permiso			= getSIPAKALPermissions($theFile);
if($permiso === false){	header ("location:../404.php?i=999");	}
$_SESSION["current_file"]	= addslashes( $theFile );
//=====================================================================================================
$xHP		= new cHPage("", HP_SERVICE);
//$xQL		= new MQL();
//$xLi		= new cSQLListas();
$xF			= new cFecha();


$clave			= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$fecha			= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);
$persona		= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito		= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta			= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback		= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);$action = parametro("cmd", $action);
$monto			= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT);
$recibo			= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$observaciones	= parametro("idobservaciones");
$letra			= parametro("letra", false, MQL_INT); $letra = parametro("periodo", $letra, MQL_INT); $letra = parametro("parcialidad", $letra, MQL_INT);



//$xSVC			= new MQLService($action, "");


$f3 = \Base::instance();

$f3->route('GET /',
		function() {
			echo 'Hello, world!';
		});

$f3->route('GET /reporte/cartera',
		function($f3) {
			
			$arg1	= $f3->get("GET.desde");
			$arg2	= $f3->get("GET.hasta");
			
			echo "Hello $arg1, world! $arg2";
			
			
			
		});

$f3->route('PUT /reporte/cartera',
		function($f3) {
			
			$arg1	= $f3->get("GET.desde");
			$arg2	= $f3->get("GET.hasta");
			
			echo "Hello ----------- $arg1, world! $arg2";
			
			
			
		});

$f3->set('ONERROR',
		function($f3) {
			// recursively clear existing output buffers:
			while (ob_get_level())
				ob_end_clean();
				// your fresh page here:
				echo $f3->get('ERROR.text');
		}
		);

$f3->run();

?>