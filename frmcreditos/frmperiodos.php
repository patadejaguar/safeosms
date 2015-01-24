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

$oficial 			= elusuario($iduser);
$periodo_propuesto	= ( isset( $_GET["p"] ) ) ? $_GET["p"] : date("Y") . date("m"); 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Periodos de Sesiones.- Altas</title>
</head>
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
<script  >
	function changedate() {
		document.frmperiodos.elmes0.value = <?php echo dmes(); ?>;
		document.frmperiodos.elmes1.value = <?php echo dmes(); ?>;
		document.frmperiodos.elmes2.value = <?php echo dmes(); ?>;
	}
</script>

<body>
<fieldset>
	<legend>Periodos de Sesiones.- Altas</legend>
<form name="frmperiodos" action="frmperiodos.php?a=i" method="post">
	<table  >
		<tr>
			<td>Descripcion del Periodo</td>
			<td colspan="3"><input name='descripcion' type='text' value='' size="80" maxlength="100" onchange="changedate();"></td>
		</tr>
		<tr>
		<td>Identificador</td><td><input type='text' name='idperiodo' value='<?php echo $periodo_propuesto; ?>' size='10' class='mny'></td>
		<td>Fecha de Reunion</td><td><?php echo ctrl_date(0); ?></td>
		</tr>
		<tr>
			<td>Fecha Inicial</td><td><?php echo ctrl_date(1); ?></td>
			<td>Fecha Final</td><td><?php echo ctrl_date(2); ?></td>
		</tr>
		<tr>
			<th>Oficial de Credito</th>
			<td colspan='3'><?php
				$sqlTO = "SELECT id, nombre_completo FROM oficiales /* WHERE estatus='activo' */ ";
				$xTO = new cSelect("cOficial", "idOficial", $sqlTO);
				$xTO->setEsSql();
				
				$xTO->addEspOption("todas", "Todos");
				$xTO->show(false);
				$xTO->setOptionSelect("todas");
			?></td>		
		</tr>
	</table>

	<input type='button' name='btnsend' value='Guardar Registro' onClick='frmperiodos.submit();' class="button">
	<?php
	$xBtn	= new cHButton("");
	echo $xBtn->getSalir(); 
	?>	
</form>

<?php
/**
 * 			Imprime Datos de las sesiones de creditos
 */


$action			= ( isset($_GET["a"]) ) ? $_GET["a"] : "x";
$descripcion 	= ( isset($_POST["descripcion"] )) ? $_POST["descripcion"] : "";
if($action == "i" AND strlen($descripcion) > 1){
	$idperiodo 		= $_POST["idperiodo"];
	
	
	$reunion 	= $_POST["elanno0"] . "-" . $_POST["elmes0"] . "-" . $_POST["eldia0"];
	$inicial 	= $_POST["elanno1"] . "-" . $_POST["elmes1"] . "-" . $_POST["eldia1"];
	$final 		= $_POST["elanno2"] . "-" . $_POST["elmes2"] . "-" . $_POST["eldia2"];
	$resp		= $_POST["cOficial"];
	
	$xP			= new cPeriodoDeCredito();
	$xP->add($inicial, $final, $resp, $reunion, $descripcion, $ideperiodo );
}

$cTbl = new cTabla("select creditos_periodos.idcreditos_periodos AS 'codigo_de_periodo',
					creditos_periodos.descripcion_periodos AS 'nombre_periodo',
					creditos_periodos.fecha_inicial AS 'fecha_de_inicio',
					creditos_periodos.fecha_final AS 'fecha_de_termino',
					creditos_periodos.fecha_reunion AS 'fecha_de_reunion',
					CONCAT(usuarios.nombres, ' ',usuarios.apellidopaterno, ' ', usuarios.apellidomaterno) AS 'oficial_responsable'
				FROM 
				
					`creditos_periodos` `creditos_periodos` 
						INNER JOIN `usuarios` `usuarios` 
						ON `creditos_periodos`.`periodo_responsable` = `usuarios`.`idusuarios`
				
				ORDER BY fecha_reunion ", 0);
$cTbl->setWidth();
$cTbl->addTool(1);
$cTbl->setKeyField("idcreditos_periodos");
$cTbl->Show("", false);

?>
<p class='aviso'>AGREGUE LA DESCRIPCION QUE IDENTIFIQUE A LA SESION DE CREDITO ACTUAL, 
EN CASO DE NO LLEVAR PERIODOS DE CREDITO AGREGUE UN PERIODO DESDE EL INICIO DE AÑO HASTA EL FIN DE AÑO, UN PERIODO POR A&Ntilde;O</p>
</fieldset>
</body>
<script  >
<?php
	echo $cTbl->getJSActions();
?>

</script>
</html>
