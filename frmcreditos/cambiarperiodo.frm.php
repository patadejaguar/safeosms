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

	$xHP		= new cHPage("TR.Cambiar Periodo de Credito");

$oficial 	= elusuario($iduser);
//$periodosolicitudes
$action 	= parametro("a");
//$aviso		= variables("")
$aviso 		= parametro("aviso");

echo $xHP->getHeader();
?>
<body>
<fieldset>
	<legend>Cambiar Periodo de Solicitudes de Credito</legend>
<form name='frmupdateperiodo'  action="cambiarperiodo.frm.php" method="post">
	<table>
		<tr>
			<th>PERIODO ACTUAL</th>
			<td class='aviso'><?php echo EACP_PER_SOLICITUDES;  ?> </td>
		</tr>	
		<tr>
			<th>PERIODO NUEVO</th>
			<td><?php
				$sql	= "SELECT * FROM creditos_periodos WHERE fecha_final >='" . fechasys() . "'";
				$xTO = new cSelect("cPeriodo", "idPeriodo", $sql);
				$xTO->setEsSql();
				$xTO->addEspOption("nuevo", "Agregar uno Nuevo");
				$xTO->addEvent("onblur", "jsToAction");
				$xTO->setOptionSelect(EACP_PER_SOLICITUDES);
				$xTO->show(false);				
			?>
			</td>
		</tr>
		<tr>
			<th colspan='2'>
				<input type="submit" value="ENVIAR">
				<input type="button" value="Agregar un Periodo Nuevo" onClick="addPeriodo();;" >
				<input type="button" name="sendme" value="SALIR" onClick="setTerminar();">			
			</th>
		</tr>
	</table>

</form>
<p class="aviso"><?php echo $aviso; ?></p>

<?php
$sqlp = $sqlb11 . " ORDER BY fecha_reunion DESC";
$tbl = new cTabla($sqlp);
$tbl->setEventKey("setPeriodo");
$tbl->Show("", false);
$per = ( isset($_POST["cPeriodo"])  ) ? $_POST["cPeriodo"] : EACP_PER_SOLICITUDES ;
if ( $per != EACP_PER_SOLICITUDES ){
	$xP		= new cPeriodoDeCredito($per);
	$xHT	= new cHTMLObject();
	echo $xHT->setInHTML( $xP->setCambiar($per) );
}
?>
</fieldset>
</body>
<script  >
	function setPeriodo(Id){
		document.frmupdateperiodo.cPeriodo.value = Id;
	}
	function setTerminar(){
		window.location = "../utils/clssalir.php";
	}
	function jsToAction(){
		if ( document.getElementById("idPeriodo").value == "nuevo" ){
			jsGenericWindow("./frmperiodos.php");
		}
			
	}
	function addPeriodo(){
		jsGenericWindow("./frmperiodos.php");
	}
</script>
<?php
$jsb	= new jsBasicForm("frmupdateperiodo");
$jsb->setIncludeCaptacion(false);
//$jsb->setInputProp("descripcion_de_la_cuenta", "name", "nombrecuenta" );

$jsb->show();
?>
</html>
