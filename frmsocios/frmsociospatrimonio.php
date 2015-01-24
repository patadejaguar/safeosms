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


$iduser = $_SESSION["log_id"];

$xP		= new cHPage("Relacion Patrimonial del Socio");


$idsocio = $_GET["socio"];


echo $xP->getHeader();

echo $xP->setBodyinit("initComponents()");

	if (!$idsocio) {
		echo JS_CLOSE;
	}
?>
<fieldset>
<legend>Balance Patrimonial <?php echo getNombreSocio($idsocio); ?></legend>
<form name="frmpatrimonio" action="frmsociospatrimonio.php?socio=<?php echo $idsocio; ?>" method="POST">
	<table      >
		<tr>
			<td>Tipo</td><td>
			<?php
			$xSel1	= new cSelect("tpatrim", "", TPERSONAS_TIPO_PATRIMONIO);
			echo $xSel1->get();
			?>
			</td>
			<td>Fecha de Expiracion</td><td><?php echo ctrl_date(0); ?></td>
		</tr>
		<tr>
			<td>Documento Presentado</td><td><input name="doctopres" type="text" size="30" maxlength="100" /></td>
			<td>Estado F&iacute;sico</td><td><select name="estatus">
			<?php
			$gssql= "SELECT * FROM socios_patrimonioestatus";
			$gsres = mysql_query($gssql);
			while($gsrow = mysql_fetch_row($gsres)){
				echo "<option value='$gsrow[0]'>$gsrow[1]</option>";
			}
			@mysql_free_result($gsres);
			?>
			</select></td>
		</tr>
		<tr>
			<td>Descripcion</td>
			<td colspan="1"><input name="descripcion" type="text" size="30" maxlength="100" /></td>
			<td>Monto Declarado</td><td><input type='number' name='montop' value='0' class="mny" /></td>
		</tr>
		<tr>
			<td>Observaciones</td><td colspan="3"><input name="observacion" type="text" size="60" maxlength="100" /></td>
		</tr>
		<tr>
			<th colspan="4"><input type="button" value="AGREGAR DATOS" onclick="document.frmpatrimonio.submit();" /></th>
		</tr>
	</table>
</form>
</fieldset>
	<?php
	
	$montop 	= isset($_POST["montop"]) ? $_POST["montop"] : 0;
	$mysoc 		= $idsocio;
if(($montop > 0) AND ($mysoc) ){
	$docto 			= $_POST["doctopres"];
	$tipopat 		= $_POST["tpatrim"];
	$fechaexp 		= $_POST["elanno0"] . "-" . $_POST["elmes0"] . "-" . $_POST["eldia0"];
	$eldocto 		= $_POST["doctopres"];
	$descrip 		= $_POST["descripcion"];
	$observa 		= $_POST["observacion"];
	$estatus 		= $_POST["estatus"];
	$sucursal 		= getSucursal();
	$eacp 			= EACP_CLAVE;
	// determina el Tipo de Activo/Pasivo
	$sqlap 			= "SELECT subclasificacion FROM socios_patrimoniotipo WHERE idsocios_patrimoniotipo=$tipopat";

	$afect 			= mifila($sqlap, "subclasificacion");
	
	$xSoc			= new cSocio($mysoc);
	$xSoc->addPatrimonio($tipopat, $montop, $estatus, $afect, $eldocto, $descrip, $observa, $fechaexp);
		
	echo "<p class='aviso'>EL REGISTRO SE HA EFECTUADO</p>";
}


	echo $xP->setBodyEnd();
	?>

<script  >
	function initComponents(){
		resizeMainWindow();
	}
	function resizeMainWindow(){
		var mWidth	= 720;
		var mHeight	= 480;
		window.resizeTo(mWidth, mHeight);
	}
</script>
<?php
echo $xP->end(); 
?>
