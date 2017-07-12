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
    require("../libs/jsrsServer.inc.php");
    include_once("../core/core.deprecated.inc.php");
	include_once("../core/entidad.datos.php");
	include_once("../core/core.config.inc.php");
	include_once("../core/core.common.inc.php");
	
jsrsDispatch("Common_97de3870795ecc1247287ab941d9719b, Common_56f5780b422f3f0f67183f89e99625c4");
/**
 * Funcion que califica a las Referencias
 *
 * @param string $params
 */
function Common_97de3870795ecc1247287ab941d9719b($params){
	$stdDiv		= STD_LITERAL_DIVISOR;
	$DPar		= explode($stdDiv, $params, STD_MAX_ARRAY_JS);
	$control	= trim($DPar[0]);
	$score		= trim($DPar[1]);
	$sql		= "UPDATE socios_relaciones 
    				SET calificacion_del_referente=$score
    				WHERE  idsocios_relaciones=$control";
	$Stat		= my_query($sql);
	if($Stat["stat"] == false){
		return "Hubo un problema al Guardar el Registro";
	} else {
		return "Registro Exitoso por " . $score . "!!";
	}
}
/**
 * Funcion que devuelve si el Domicilio Principal Existe
 * @param integer $idsocio
 */
function Common_56f5780b422f3f0f67183f89e99625c4($idsocio = false) {
	$yatiene	= 0;
	if( isset($idsocio) AND $idsocio != false ){
		$sqlya = "SELECT COUNT(idsocios_vivienda) AS \"contado\"
				FROM socios_vivienda WHERE socio_numero=$idsocio
				AND principal=\"1\" ";
		$yatiene = mifila($sqlya, "contado");
	}
	return $yatiene;
}

?>