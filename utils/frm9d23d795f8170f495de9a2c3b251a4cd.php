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
$xHP				= new cHPage("TR.Eliminar Registro");
//$xAut				= new cAu
$oficial = elusuario(getUsuarioActual());

//Verificar eliminacion de archivos
//Mejorar
?>
<script  >
function cierrame() {	if( window.console ) window.console.log( '' ) ; 	window.close(); }
</script>
<body onLoad="setTimeout('cierrame()',60*5)">
<p class="frmTitle"><script> document.write(document.title); </script></p>
<?php
$filter = (isset($_GET["f"])) ? $_GET["f"] : false;
$table 	= (isset($_GET["t"])) ? $_GET["t"] : false;
$msg	= "";
	
	/*if ($filter == false OR $table == false) {
		$msg		.= "ERROR\tNO SE TIENE UN DATO\r\n";
	} else {
		$insql 		= "DELETE FROM $table WHERE $filter";
		$sqlor		= "SELECT * FROM $table WHERE $filter";
		$filas		= obten_filas($sqlor);
		$cadena		= json_encode($filas);
		$xErr		= new cCoreLog();	$xErr->add("$oficial Elimino $filter de $table.\n ORIGINAL:\n$cadena"); $xErr->guardar( $xErr->OCat()->ELIMINAR_RAW );
		
		$rs			= my_query($insql);
		if($rs["stat"] == true){
			$msg		.= "OK\tSE HA ELIMINADO EL REGISTRO\r\n";
		}
	}*/
	echo JS_CLOSE;
?>
</body>
</html>
