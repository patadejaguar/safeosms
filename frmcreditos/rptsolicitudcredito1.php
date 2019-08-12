<?php
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

$xHP		= new cHPage();
$xHP->setTitle( $xHP->lang(array("imprimir", "solicitud", "de", "credito") ) );

echo $xHP->getHeader();
?>
<body>
<?php
$idsolicitud = $_GET["solicitud"];
	if (!$idsolicitud) {
		exit("NO HAY SUFICIENTES DATOS PARA LLEVAR A CABO EL INFORME");		
	}
// enumera los conceptos a imprimir, por defecto todos estan aceptado.
?>
<fieldset>
<legend>IMPRIMIR SOLICITUD DE CREDITO.- OPCIONES DE IMPRESION</legend>
<form name="frmlist" action="rptsolicitudcredito1.php" method="post">
<table>
	<tr>
		<td><input name="siavales" type="checkbox" checked>AGREGAR RELACION DE AVALES</td>
	</tr>
	<tr>
		<td><input name="sigarantia" type="checkbox" checked>AGREGAR RELACION DE GARANTIAS</td>
	</tr>
	<tr>
		<td><input name="siflujo" type="checkbox" checked>AGREGAR FLUJO DE EFECTIVO</td>
	</tr>
	<tr>
		<td><input name="sipatrimonio" type="checkbox" >AGREGAR RELACION PATRIMONIAL</td>
	</tr>
	<tr>
		<td><input name="sihistorial" type="checkbox" checked>AGREGAR HISTORIAL MORAL</td>
	</tr>
</table>
<p class="aviso"> <input type="button" name="sendme" value="VER / IMPRIMIR SOLICITUD" onClick="somser();"></p>
</form>
</fieldset>
</body>
	<script>
		function somser() {
			paval = 'not';
			pgar = 'not';
			pefvo = 'not';
			ppat = 'not';
			phis = 'not';

			// incluir avales
			if (document.frmlist.siavales.checked) {
				paval = 'yes';
			} else {
				paval = 'not';
			}
			// incluir garantias
			if (document.frmlist.sigarantia.checked) {
				pgar = 'yes';
			} else {
				pgar = 'not';
			}
			// Incluir flujo efvo
			if (document.frmlist.siflujo.checked) {
				pefvo = 'yes';
			} else {
				pefvo = 'not';
			}
			// Incluir patrimonio
			if (document.frmlist.sipatrimonio.checked) {
				ppat = 'yes';
			}  else {
				ppat = 'not';
			}
			// Incluir Historial
			if (document.frmlist.sihistorial.checked) {
				phis = 'yes';
			}  else {
				phis = 'not';
			}
			
			var URL = '../rpt_formatos/rptsolicitudcredito2.php?solicitud=<?php echo $idsolicitud ?>&avales=' + paval + '&garantias=' + pgar + '&flujo=' + pefvo + '&patrimonio=' + ppat + '&historial=' + phis;
			
			prep = window.open(URL, "", "resizable,fullscreen,scrollbars,menubar");
			prep.focus();
			window.close();
		}
	</script>
</html>
