<?php
//=====================================================================================================
	include_once("core/go.login.inc.php");
	$nivelmin = 2;
		if ($_SESSION["log_nivel"] < $nivelmin) {
			header ("location:inicio.php");
			exit();
		}
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
    require("libs/jsrsServer.inc.php");
    include_once("core/core.deprecated.inc.php");
	include_once("core/entidad.datos.php");
	include_once("core/core.fechas.inc.php");

jsrsDispatch("valorar_convenio, d2ac473559323489421747601740046c");

/*-------------------- SQl Instruccion for query ----------------------------------------- */
function mydat($msql, $mcampo){
	$jinger	= mifila($msql, $mcampo);
	return $jinger;
}

function mytypev($ttab, $ffilt){

}
function valorar_convenio($idconvenio ="99") {
	$info = " alert('NO HAY INFORMACION.- ERROR DE DATOS');";
	$sql_convenio = "SELECT * FROM creditos_tipoconvenio WHERE idcreditos_tipoconvenio=$idconvenio";
		$row = obten_filas($sql_convenio);

			$info = $row["code_valoracion_javascript"];

	unset($row);
	return $info;
}

function d2ac473559323489421747601740046c($strid = "99|99"){
	$datos 			= explode("|", $strid);
	$solicitud 		= $datos[0];
	$sql_u 			= "UPDATE creditos_solicitud SET estatus_actual=50 WHERE numero_solicitud=$solicitud";
	$rs 			= mysql_unbuffered_query($sql_u);
	return "ACTUALIZACION DE REGISTRO SATISFACTORIO - CODIGO $datos[0] | $datos[1]";
}
?>