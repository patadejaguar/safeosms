<?php 
//compatibilidad
$idgarantia = isset($_GET["idg"]) ? $_GET["idg"] : false;
if($idgarantia == false){
	header("location: ../utils/404.php");
} else {
	header("location: ../rpt_formatos/entrega_de_garantias.form.php?clave=$idgarantia");
}
?>