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
$xHP		= new cHPage();
$xHP->setTitle($xHP->lang(array("registro", "de", "garantia", "real")));

$jxc = new TinyAjax();

function JSsetDatosHeredados($socio, $form){
	$DSocio = getDatosSocio($socio);
	$DDom = getDatosDomicilio($socio);
	$telefono 		= $DDom["telefono_residencial"];
	$telefonomovil 	= $DDom["telefono_movil"];
	$domicilio = domicilio($socio);

			$tab = new TinyAjaxBehavior();
			$tab -> add(TabSetValue::getBehavior('idTFijo', $telefono));
			$tab -> add(TabSetValue::getBehavior('idDomicilio', $domicilio));
			$tab -> add(TabSetValue::getBehavior('idTMovil', $telefonomovil));

			$tab -> add(TabSetValue::getBehavior('idNombres', $DSocio["nombrecompleto"]));
			$tab -> add(TabSetValue::getBehavior('idApPaterno', $DSocio["apellidopaterno"]));
			$tab -> add(TabSetValue::getBehavior('idApMaterno', $DSocio["apellidomaterno"]));
			$tab -> add(TabSetValue::getBehavior('idCurp', $DSocio["curp"]));
			return $tab -> getString();
}
function JSsetDomicilioMismo($socio, $HDomicilio, $form){
	if($HDomicilio == "mismo"){
	$domicilio = domicilio($socio);
	$DDom = getDatosDomicilio($socio);
	$telefono = $DDom["telefono_residencial"];
	$telefonomovil = $DDom["telefono_movil"];
			$tab = new TinyAjaxBehavior();
			$tab -> add(TabSetValue::getBehavior('idTFijo', $telefono));
			$tab -> add(TabSetValue::getBehavior('idTMovil', $telefonomovil));
			$tab -> add(TabSetValue::getBehavior('idDomicilio', $domicilio));
			return $tab -> getString();
	}
}
$jxc ->exportFunction('JSsetDatosHeredados', array('idNumeroSocio', 'frmreferencias'));
$jxc ->exportFunction('JSsetDomicilioMismo', array('idSocioRelacionado', 'idDomicilio', 'frmreferencias'));
$jxc ->process();

$idsolicitud 			= (isset($_GET["solicitud"])) ? $_GET["solicitud"] : DEFAULT_CREDITO;
$montovaluado			= (isset($_POST["montoval"])) ? $_POST["montoval"] : SYS_CERO;
$doctopresentado 		= (isset($_POST["docto"])) ? $_POST["docto"] : SYS_CERO;

$action				= parametro("x", SYS_NINGUNO, MQL_RAW);
$msg				= "";

/* verifica si el Numero de Solicitud exista */
if  ($idsolicitud == DEFAULT_CREDITO) { echo JS_CLOSE; }
$xCred				= new cCredito($idsolicitud); $xCred->init();
$idsocio			= $xCred->getClaveDePersona();

echo $xHP->getHeader(true);

$xSQL			= new cSQLListas();
?>
<body>
<fieldset>
<legend>INGRESO DE GARANTIAS</legend>
<form name="frmagarantias" action="./frmcreditosgarantias.php?solicitud=<?php echo $idsolicitud; ?>&x=<?php echo getRndKey(); ?>" method="POST">
<table >
<tbody>
	<?php
	if(PERMITIR_EXTEMPORANEO== true){ echo CTRL_FECHA_EXTEMPORANEA;	}

	?>
<tr>
	<td>Tipo</td>
	<td><?php
         ctrl_select("creditos_tgarantias", "  name=\"tipogarantia\" id=\"idtipogarantia\" ");
	?></td>
	<td>Tipo de Valuacion</td>
	<td><?php
         ctrl_select("creditos_tvaluacion", " name=\"tipoval\" id=\"idtipoval\" ");
	?></td>
	</tr>
	<tr>
	<td colspan="1">Fecha de Adquisici&oacute;n</td><td><?php
	echo ctrl_date(0, 0, "", "NACIMIENTO");
	?></td>
	<td>Monto Valuado</td><td><input type="number" name="montoval" value="0.00" class="mny" /></td>
	</tr>
		<tr>
		<td>Descr&iacute;balo</td>
		<td colspan="3"><input name="describelo" type="text" size="60" maxlength="200" /></td>
	</tr>
	<tr>
		<td>Docto. Presentado</td>
		<td colspan="3"><input name="docto" type="text" size="40" maxlength="200" /></td>
	</tr>
	<tr>
	<td>Clave Persona Prop.</td>
	<td><input name='idsocio' type='number' value='<?php echo $idsocio; ?>' size='12' onchange="envsoc();" class='mny' /></td>
	<td>Nombre Propietario</td>
	<td><input name='nombresocio' type='text' value='<?php echo getNombreSocio($idsocio); ?>' size="30" maxlength="80" /></td>
	</tr>
	<tr>
	<td>Estado Fisico</td>
	<td><?php
			$gssql= "SELECT * FROM socios_patrimonioestatus";
			$cEFis = new cSelect("estadofisico", "", $gssql);
			$cEFis->setEsSql();
			$cEFis->show(false);
		?>
	</td>
	<td>Observaciones</td>
	<td colspan="1"><input name="observaciones" type="text" size="30" maxlength="100" /></td>
	</tr>
	<tr>
		<th colspan="4"><input type="button" value="GUARDAR REGISTRO" onclick="frmSubmit();" /></th>
	</tr>
</tbody>
</table>
<?php
//jsbasic("frmagarantias", "", ".");
$js		= new jsBasicForm("frmagarantias");
echo $js->get();
$nkey					= getRndKey();
/* ------------------------------------------------------------------------------------------------*/
if (($montovaluado>TOLERANCIA_SALDOS) and $action!=$nkey) {
	
		$tipogarantia				= $_POST["tipogarantia"];
		$fecharecibo				= fechasys();
			if(PERMITIR_EXTEMPORANEO== true){
				if(isset($_POST["elanno98"])){
						$fecharecibo = $_POST["elanno98"] . "-" . $_POST["elmes98"] . "-" . $_POST["eldia98"];
				}
			}
		$fechaadquisicion			= $_POST["elanno0"] ."-". $_POST["elmes0"] ."-". $_POST["eldia0"];
		$tipovaluacion				= $_POST["tipoval"];
		$observaciones				= $_POST["observaciones"];
		$sociopropietario 			= $_POST["idsocio"];
		$propietario 				= $_POST["nombresocio"];
		$estadofisico 				= $_POST["estadofisico"];
		$descrito 					= $_POST["describelo"];
		
		$xCred						= new cCredito($idsolicitud);
		$xCred->init();
		$xCred->addGarantiaReal($tipogarantia, $tipovaluacion, $montovaluado, $sociopropietario, $propietario, $fechaadquisicion, $doctopresentado,
								$estadofisico, $descrito, $observaciones, $fecharecibo);
		
}
?>

<p class="aviso"><?php echo $msg; ?></p>
</form>
</fieldset>
<?php

	$sql_final = $xSQL->getListadoDeGarantiasReales("", $idsolicitud); // $sqlb17_alt . " AND solicitud_garantia=$idsolicitud ";
	$myTab = new cTabla($sql_final);
	$myTab-> addTool(SYS_UNO);
	$myTab-> addTool(SYS_DOS);
	$myTab->setKeyField("idcreditos_garantias");
	$myTab->Show("", false);

?>
</body>
<script  >
	<?php
		echo  $myTab->getJSActions();
	?>
</script>
</html>
