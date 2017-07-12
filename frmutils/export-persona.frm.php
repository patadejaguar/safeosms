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
$xHP		= new cHPage("", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto		= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo		= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa	= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT);
$grupo		= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria = parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);

$observaciones= parametro("idobservaciones");

$xHP->init();

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());

//$xFRM->addJsBasico();
$sql		= "SELECT DISTINCT INFORMATION_SCHEMA.COLUMNS.TABLE_SCHEMA,
INFORMATION_SCHEMA.TABLES.TABLE_TYPE ,
INFORMATION_SCHEMA.COLUMNS.TABLE_NAME AS 'tabla', 
INFORMATION_SCHEMA.COLUMNS.COLUMN_NAME AS 'columna'

FROM
INFORMATION_SCHEMA.COLUMNS
INNER JOIN
INFORMATION_SCHEMA.TABLES

ON
INFORMATION_SCHEMA.TABLES.TABLE_SCHEMA = INFORMATION_SCHEMA.COLUMNS.TABLE_SCHEMA

WHERE
INFORMATION_SCHEMA.COLUMNS.TABLE_SCHEMA='matriz' AND INFORMATION_SCHEMA.TABLES.TABLE_TYPE NOT LIKE \"%VIEW%\"
AND
((column_name LIKE '%persona%')
OR
(column_name LIKE '%socio%')
)
AND

(column_name NOT LIKE 'idsocio%')
AND
(column_name NOT LIKE 'idpersona%')
AND
(column_name NOT LIKE 'idaml_personas_%')
AND
(column_name NOT LIKE 'representante_numero_%') /*solo en exportacion*/
AND
(column_name NOT LIKE 'vocal_vigilancia_numero_%') /*solo en exportacion*/

AND
INFORMATION_SCHEMA.COLUMNS.TABLE_NAME NOT LIKE 'vw_%'
AND
INFORMATION_SCHEMA.COLUMNS.TABLE_NAME NOT LIKE 'tmp_%'
AND
INFORMATION_SCHEMA.COLUMNS.TABLE_NAME NOT LIKE 'temp_%'
AND

INFORMATION_SCHEMA.COLUMNS.TABLE_NAME NOT LIKE 'vv_%'
AND
INFORMATION_SCHEMA.COLUMNS.TABLE_NAME NOT LIKE 'sumas_%'

AND
INFORMATION_SCHEMA.COLUMNS.TABLE_NAME != 'creditos'
AND
INFORMATION_SCHEMA.COLUMNS.TABLE_NAME != 'letras'
AND
INFORMATION_SCHEMA.COLUMNS.TABLE_NAME != 'domicilios'
AND
INFORMATION_SCHEMA.COLUMNS.TABLE_NAME != 'general_folios'
AND
INFORMATION_SCHEMA.COLUMNS.TABLE_NAME != 'creditos_a_final_de_plazo'

AND
INFORMATION_SCHEMA.COLUMNS.TABLE_NAME != 'listado_de_ingresos'
AND
INFORMATION_SCHEMA.COLUMNS.TABLE_NAME != 'operaciones'
AND
INFORMATION_SCHEMA.COLUMNS.TABLE_NAME != 'operaciones_sumas'

AND
INFORMATION_SCHEMA.COLUMNS.TABLE_NAME NOT LIKE 'creditos_letras_%'
AND
INFORMATION_SCHEMA.COLUMNS.TABLE_NAME != 'historial_de_pagos'
AND
INFORMATION_SCHEMA.COLUMNS.TABLE_NAME != 'personas_operaciones_recursivas'
AND
INFORMATION_SCHEMA.COLUMNS.TABLE_NAME != 'primeras_letras'
AND
INFORMATION_SCHEMA.COLUMNS.TABLE_NAME != 'operaciones_no_estadisticas'
AND
INFORMATION_SCHEMA.COLUMNS.TABLE_NAME NOT LIKE 'migracion_%'
AND
INFORMATION_SCHEMA.COLUMNS.TABLE_NAME != 'solicitudes'";

$rs		= $xQL->getDataRecord($sql);
$xLog	= new cCoreLog();
foreach ($rs as $rw){
	$tabla		= $rw["tabla"];
	$columna	= $rw["columna"];
	//$xLog->add( "mysql -e \"SELECT * FROM $tabla WHERE $columna=\$PERSONA\" --user=root --password=\$MPASS \$MDB >> \$MDB-\$PERSONA.txt\n");
	//'
	$xLog->add( "mysqldump --skip-triggers --compact --extended-insert=FALSE --no-create-info --user=root --password=\$MPASS -B \$MDB --tables $tabla --where='" . $columna . "='+\$PERSONA >> \$MDB-\$PERSONA.sql\n");
}
$xFRM->addLog($xLog->getMessages());
echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>