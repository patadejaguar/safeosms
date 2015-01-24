<?php
include_once("../core/core.config.inc.php");
//include_once("../core/core.fechas.inc.php");
ini_set("display_errors", "on");
//ini_set("auto_detect_line_endings", "0");
// boolean 
$arrcmd             = array();
$cmd                = false;
$murl               = URL_UPDATES . "/install/update-server.inc.php?a=u@" . SAFE_VERSION;
$url_txt_files      = $murl . " -O /tmp/safe_actualizaciones.txt";
exec ("wget --no-check-certificate " . $url_txt_files, $arrcmd, $cmd );
//var_dump($arrcmd);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
</head>
<body>
<form name="" method="POST" action="./">
<fieldset>
	<legend><script> document.write(document.title); </script></legend>
	<table border='0' width='100%'  >
		<tbody>
                    <tr>
                        <th width='5%'>Tipo</th>
                        <th width='15%'>Objeto</th>
                        <th width='15%'>Destino</th>
                        <th width='10%'>Fecha de Actualizacion</th>
                        <th width='50%'>Descripcion</th>
                        <th width='5%'>Ejecutar</th>
                    </tr>
<?php
//var_dump($cmd);
    $gestor = @fopen("/tmp/safe_actualizaciones.txt", "r");
        if ($gestor) {
        //Inicializar Variables
            while (!feof($gestor)) {
                $bufer	= fgets($gestor);
                $bufer	= trim($bufer);
                $D	= explode("@", $bufer, 6);
                echo "<tr>
                        <td><input type='hidden' id='id-Tipo-" . $D[1] . "' value='" . $D[1] . "' />
                        <input type='hidden' id='id-" . $D[0] . "' value='" . $D[0] . "' />
                        <img src='../images/safe_updates/" . $D[1] . ".png' title='Actualizacion de " . strtoupper($D[1]) . "' /></td>
                        <td>" . $D[2] . "</td>
                        <td>" . $D[3] . "</td>
                        <td>" . $D[4] . "</td>
                        <td>" . $D[5] . "</td>
                        <th><input type='button' onclick='jsGo(" . $D[0] . ")' value='Go!' /></th>
                    </tr>";
            }
        }
?>
		</tbody>
	</table>
</fieldset>

</form>
</body>
<script  >
function jsGo(id){
    alert(id);
}
</script>
</html>
