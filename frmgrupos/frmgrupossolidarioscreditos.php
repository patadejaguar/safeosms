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

	include_once "../core/core.deprecated.inc.php";
	include_once "../core/core.fechas.inc.php";
	include_once "../core/entidad.datos.php";
	include_once "../core/core.config.inc.php";
	include_once "../core/core.common.inc.php";
	
	$oficial = elusuario($iduser);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Integracion de Prestamos en Grupos Solidarios</title>
</head>
<link href="<?php echo CSS_GENERAL_FILE; ?>" rel="stylesheet" type="text/css">
<?php
jsbasic("myformgs", "1", ".");
?>
<script   >
var jrsFile = "../clsfunctions.inc.php";

//	var idgrupo = document.frmgposolidario.idgrupo.value;
	function envRep() {
	idsoc = document.frmgposolidario.idsociogpo.value;
		if (idsoc != '') {
			jsrsExecute(jrsFile, larep, 'nombre', idsoc);
		}
	}
	function larep(larev) {
		document.frmgposolidario.nombresociogpo.value = larev;
	}
	function envVV() {
	idsocvv = document.frmgposolidario.idsociogpovv.value;
		if (idsocvv != '') {
			jsrsExecute(jrsFile, lavv, 'nombre', idsocvv);
		}
	}
	function lavv(lavvv) {
		document.frmgposolidario.nombresociogpovv.value = lavvv;
	}
	function insert_frm() {
		document.frmgposolidario.submit();
	}
</script>
<body>
<hr />
<p class="frmTitle"><script> document.write(document.title ); </script></p>
<hr />
<form name='myformgs' action='frmgrupossolidarioscreditos.php' method='post'>
	<table   border='0'>
		<tr>
			<td>Clave de Grupo</td><td><input type='text' name='idgrupo' value='' onchange="envgpo();"></td>
			<td>Nombre del Grupo</td><td><input name='nombregrupo' type='text' disabled value='' size="60" maxlength="100"></td>
		</tr>
	</table>
<hr />
<input type='button' name='btsend' value='ENVIAR DATOS'onClick='frmSubmit();'>
</form>
<?php
$idgrupo = $_POST["idgrupo"];
	if (!$idgrupo) {
		exit($msg_rec_exit . $fhtm);
	}
	// Verifica que no haya una Planeacion Previa;

	// Cuenta los Creditos por Grupos Solidarios
	//$sqlcc = "SELECT COUNT(numero_solicitud) AS 'loscreditos' FROM creditos_solicitud WHERE estatus_actual!=99 AND grupo_asociado=$idgrupo";
	// determina si es de una a 4 Min
	//Datos del Tipo de Creditos
	//
	$sqlgpo 				= "SELECT * FROM socios_grupossolidarios WHERE idsocios_grupossolidarios=$idgrupo";
	$info_grupo 			= the_row($sqlgpo);
	$nivel 					= $info_grupo["nivel_ministracion"];
	//Datos del Nivel de Grupo
	$pnivel 				= $nivel + 1;
	$sqldnivel 				= "SELECT * FROM creditos_nivelesdegrupo WHERE nivel=$pnivel";
	$dtsnivel 				= obten_filas($sqldnivel);
	$monto 					= $dtsnivel["monto_xintegrante"];				//Nivel de Ministracion
	if ( !isset($monto) ){
		$monto		= 0;
	}
	//
	$socio_rep 				= $info_grupo["representante_numerosocio"];
	if ($socio_rep == 1) {
		exit("<p class='aviso'>LOS DATOS DEL GRUPO NO ESTAN CORRECTOS - NO SE ACEPTA PUBLICO GENERAL</p>");
	}
	//
		$xGr	= new cGrupo($idgrupo, true);
		$xGr->init();
		echo $xGr->getFicha(true);
	//
	$sqlgs = "SELECT codigo, nombrecompleto FROM socios_general WHERE grupo_solidario=$idgrupo LIMIT 0,100 ";
	$rsgs = mysql_query($sqlgs);
	echo "<hr /><form name='myformls' action='clsgrupossolidarioscreditos.php?grupo=$idgrupo' method='post'>
	<table   border='0'>
	<caption>Nivel de Ministracion $nivel<caption>
	<tr>
	<th>Clave de Persona</th>
	<th>Nombre Completo</th>
	<th>Monto que se le Autoriza</th>
	<th>Observaciones</th>
	</tr>";
	$foliorec =folios(4);				// Folio de Recibo
	$idrecibo = $foliorec;				// Numero de Recibo: user + tipooper + folio obtenido
		$i=0;
	while ($rwgs = mysql_fetch_array($rsgs)) {
		$codigo 	= $rwgs[0];
		$nombre 	= getNombreSocio($codigo);
		$folioop	= folios(2);
		$idoper 	= ($folioop + $i);

		echo "<tr>
			<td>$codigo</td>
			<td>$nombre</td>
			<td><input type='text' name='monto$i' value='$monto' class='mny'>
			<input type='hidden' name='idsocio$i' value='$codigo'>
			<input type='hidden' name='operacion$i' value='$idoper'>
			<td><input type='text' name='observacion$i' value='' size='50'></td>

		</tr>";
		$i++;
	}

	echo "</table>
	<p class='aviso'>Este Grupo Solidario tiene el Nivel $nivel para Pasar al Nivel $pnivel <br />
	La Planeacion Actual Eliminar&aacute; las anteriores <br />
	N&oacute; se Agrega&aacute; la Plenaci&oacute;n si el Credito ya Fue Autorizado</p>
	<input type='hidden' name='integrantes' value='$i' />
	<input type='hidden' name='idrecibo' value='$idrecibo' />
	<input type='button' name='btsend' value='ALMACENAR PLANEACION DE CREDITO'onClick='envCrypt();' />
	<input type='hidden' name='ordencompleta' value='' />
		</form>";

	@mysql_free_result($rsgs);
	//--------------------------------------- DATOS DEL RECIBO -----------------------------------------------
									// PERIODOS
	$percont = EACP_PER_CONTABLE;	// Periodo Contable
	$percbza = EACP_PER_COBRANZA;	// Periodo Cobranza.
	$perseg = EACP_PER_SEGUIMIENTO;	// Periodo de Seguimiento.
        
	$permens    = dnmes();				// Periodo de dias en el mes
	$persem     = 0;			// Periodo de dias en la semana.
	$peranual   = $anno;				// A?o Natural.
								// DATOS GENERALES
	$fechaop=fechasys();			// fecha de la Operacion y el recibo.
	$diasasoc = 0;					// DIAS ASOCIADOS
	$tasaasoc = 0;					// TASA ASOCIADA
	$fechaafect = $fechaop;			// FECHA DE AFECTACION
$prCad = ", '$fechaop', '$fechaop', $idrecibo, ";
$sgCad = ", 0, 0, 0, '$fechaop', 40, '$eacp_codigo_cnbv', 0, $percont, $percbza, $perseg, $permens, $persem, $peranual, 0, ";
$trCad = ", 1, 'Elaborado: $oficial',0, 0, $idgrupo)";
$mCad = "', $iduser, ";
//$trCad = ", 0, 0, $idgrupo)";
?>
</body>
<script>
	function envCrypt() {

	totalorden 	= "";
	finalizar 	= document.myformls.length - 4;
	i=0;
		for( ;i<finalizar; ) {
			var is = i+1;
			var io = i+2;
			var id = i+3;
			monto = document.myformls.elements[i].value;

			socio = document.myformls.elements[is].value;

			idoper = document.myformls.elements[io].value;

			observ = document.myformls.elements[id].value;

			i=id +1;
			if(totalorden=="") {
				totalorden = "(" + idoper + "<?php echo $prCad; ?>" + socio + ",1 ,112 ," + monto + "<?php echo $sgCad; ?>" + monto + ", '" + observ + "<?php echo $mCad; ?>" + monto + "<?php echo $trCad; ?>";
			} else {
				totalorden = totalorden + ", (" + idoper + "<?php echo $prCad; ?>" + socio + ",1 ,112 ," + monto + "<?php echo $sgCad; ?>" + monto + ", '" + observ + "<?php echo $mCad; ?>" + monto + "<?php echo $trCad; ?>";
			}

		}
		document.myformls.ordencompleta.value = totalorden;

		document.myformls.submit();
	}
</script>
</html>
