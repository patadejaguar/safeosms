<?php
//=====================================================================================================
	include_once("../core/go.login.inc.php");
	include_once ("../core/core.error.inc.php");
	$permiso = getSIPAKALPermissions(__FILE__);
	if($permiso === false){
		header ("location:../404.php?i=999");	
	}
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");

$idsolicitud = $_GET["solicitud"];
 /* verifica solicitud */
	if (!$idsolicitud) {
		echo "<p class='aviso'>NO EXISTE EL NUMERO DE SOLICITUD</p>";
		exit;
	}
	$isql = "SELECT numero_solicitud, numero_socio FROM creditos_solicitud WHERE numero_solicitud = $idsolicitud";
	if (db_regs($isql) <1){
		echo "<p class='aviso'>NO EXISTE EL NUMERO DE SOLICITUD</p>";
		exit;
	}
	$idsocio =	mifila($isql, "numero_socio");

/*--------------- DATOS DEL MONTO PRESTADO Y CUBIERTO ---------------------- */

//$tipoflujo = $_POST["tipoflujo"];
$origenflujo 		= $_POST["origenflujo"];
$montoflujo 		= $_POST["montoflujo"];
$perflujo 			= $_POST["periocidadflujo"];
$observaciones 		= $_POST["observaciones"];
$describalo 		= $_POST["describalo"];
$fecha 				= fechasys();
$sqltf 				= "SELECT * FROM creditos_origenflujo WHERE idcreditos_origenflujo=$origenflujo";
$dflujo 			= obten_filas($sqltf);
$tipoflujo 			= $dflujo["tipo"];
/* -------- Afectacion neta segun Movimiento --------------*/
	$afectacionneta = $montoflujo / $perflujo;
/*  si son semanal(7) se divide entre 7 para ser diario*/

	$afectacionneta = $afectacionneta * $dflujo["afectacion"];

/*-------------------------------------------------------- */
	$sqlv="solicitud_flujo, socio_flujo, tipo_flujo, origen_flujo, monto_flujo, afectacion_neta, periocidad_flujo, idusuario, observacion_flujo, descripcion_completa, fecha_captura";
	$sqlf= "$idsolicitud, $idsocio, $tipoflujo, $origenflujo, $montoflujo, $afectacionneta, $perflujo, $iduser, '$observaciones', '$describalo', '$fecha'";
	$sql = "INSERT INTO creditos_flujoefvo($sqlv) VALUES ($sqlf)";

	my_query($sql);

	header("location: frmcreditosflujoefvo.php?socio=". $idsocio . "&solicitud=" . $idsolicitud);

?>