<?php
/**
 * Edicion de socios
 *
 * @author Balam Gonzalez Luis Humberto
 * @version 1.1.1
 * @package common
 * @subpackage forms
 * Actualizacion de estatus
 */
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
$xHP		= new cHPage("", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE); 
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);


$xHP->init("setDefaults()");

$xFRM		= new cHForm("frm", "./");
$xSel		= new cHSelect();
$pais		= EACP_CLAVE_DE_PAIS;
?>
<fieldset>
<legend>Actualizacion / correccion de Datos</legend>

<?php
$elsocio 	= ( isset($_GET["elsocio"]) ) ? $_GET["elsocio"] : false;
$action		= ( isset($_GET["a"]) ) ? $_GET["a"] : false;

if ( $action == false ){
	if ( $elsocio != false )  {

		$xSoc		= new cSocio($elsocio);
		$xSoc->init();
		$DSoc		= $xSoc->getDatosInArray();
		$xF			= new cFecha(0, $DSoc["fecha_de_nacimiento"]);

		$anac			= $xF->anno();
		$mnac 			= $xF->mes();
		$dnac 			= $xF->dia();
		/*
		codigo, nombrecompleto, apellidopaterno, 
		apellidomaterno, rfc, curp, fechaentrevista, fechaalta, estatusactual, 
		region, cajalocal, fechanacimiento, lugarnacimiento, tipoingreso, estadocivil, 
		genero, eacp, observaciones, idusuario, grupo_solidario, personalidad_juridica, dependencia, 
		regimen_conyugal, sucursal, fecha_de_revision, tipo_de_identificacion, documento_de_identificacion 
		 */
		$pais 			= $xSoc->getPaisDeOrigen();
	}

?>
<form name ='frmsolingreso' METHOD='POST' ACTION='frmupdatesocios.php?a=ok'>
		<input type='hidden' name='uidsocio' value='<?php echo $DSoc["codigo_de_socio"]; ?>'>
<table border='0' width='100%'>
	<tr>
		<td><?php echo $xFRM->l()->getT("TR.clave_de_persona"); ?></td>
		<td><input disabled type='text' name='idsocio' value='<?php echo $DSoc["codigo_de_socio"]; ?>'></td>
	</tr>
	<tr>
		<td><?php echo $xFRM->l()->getT("TR.punto de acceso"); ?></td>
		<td><?php
		$sGen	= new cSelect("cajalocal", "", "socios_cajalocal");
		$sGen->setOptionSelect($DSoc["cajalocal"]);
		$sGen->show(false);

		?></td>
		<td><?php echo $xFRM->l()->getT("TR.Figura_juridica"); ?></td>
		<td><?php
		$gssql= "SELECT * FROM socios_figura_juridica";
		$cFJ = new cSelect("figura", "id-figura", $gssql);
		$cFJ->addEvent("onchange", "getCambiosFigura");
		$cFJ->setEsSql();
		$cFJ->setOptionSelect($DSoc["personalidad_juridica"]);
		$cFJ->show(false);
		?>
		</td>		
	</tr>
	<tr>
		<td><?php echo $xFRM->l()->getT("TR.Region"); ?></td>
		<td><?php
		$sGen	= new cSelect("region", "", "socios_region");
		$sGen->setOptionSelect($DSoc["region"]);
		$sGen->show(false);
		?></td>
			<td><?php echo $xFRM->l()->getT("TR.Nombre completo"); ?></td>
			<td><input type="text" name="nombre" value="<?php echo $DSoc["nombrecompleto"]; ?>"></td>
	</tr>
	<tr>
			<td><?php echo $xFRM->l()->getT("TR.primer apellido"); ?></td>
			<td> <input type ="text" name="paterno" value="<?php echo $DSoc["apellidopaterno"]; ?>"> </td>
			<td><?php echo $xFRM->l()->getT("TR.Segundo Apellido"); ?></td>
			<td><input type="text" name="materno" value="<?php echo $DSoc["apellidomaterno"]; ?>"></td>
	</tr>
	<tr>
		<td><?php echo $xFRM->l()->getT("TR.CURP"); ?></td>
		<td><input type="text" name="curp" value="<?php echo $DSoc["curp"]; ?>"></td>
		<td><?php echo $xFRM->l()->getT("TR.rfc"); ?></td><td><input type="text" name="rfc" value="<?php echo $DSoc["rfc"]; ?>"></td>
	</tr>
	<tr>
		<td><?php echo $xFRM->l()->getT("TR.fecha de nacimiento"); ?></td><td>
		<input name='dnac' type='text' value='<?php echo $dnac; ?>' size="4" maxlength="2">
		<input name='mnac' type='text' value='<?php echo $mnac; ?>' size="4" maxlength="2">
		<input name='anac' type='text' value='<?php echo $anac; ?>' size="6" maxlength="4"></td>
		<td><?php echo $xFRM->l()->getT("TR.lugar_de_nacimiento"); ?></td>
		<td><input name="lnacimiento" type="text" value="<?php echo $DSoc["lugarnacimiento"]; ?>"></td></tr>
	<tr>
		<td><?php echo $xFRM->l()->getT("TR.Genero"); ?></td>
		<td>
		<?php
		$gssql= "SELECT * FROM socios_genero";
		$sGen	= new cSelect("genero", "idgenero", $gssql);
		$sGen->setEsSql();
		$sGen->setOptionSelect($DSoc["genero"]);
		$sGen->show(false);
		?>
		</td>
		<td><?php echo $xFRM->l()->getT("TR.Estado Civil"); ?></td>
		<td>
		<?php
		$gssql= "SELECT * FROM socios_estadocivil";
		$sGen	= new cSelect("ecivil", "idecivil", $gssql);
		$sGen->setEsSql();
		$sGen->setOptionSelect($DSoc["estadocivil"]);
		$sGen->show(false);
		?>
		</td></tr>

		<tr>
		<td><?php echo $xFRM->l()->getT("TR.Grupo"); ?></td>
		<td>
		<?php
		$gssql= "SELECT * FROM socios_grupossolidarios";
		$sGen	= new cSelect("gruposol", "", $gssql);
		$sGen->setEsSql();
		$sGen->setOptionSelect($DSoc["grupo_solidario"]);
		$sGen->show(false);
		?>
		</td>
		<td><?php echo $xFRM->l()->getT("TR.Tipo de Ingreso"); ?></td><td>
		<?php
		$gssql= "SELECT * FROM socios_tipoingreso";
		$sGen	= new cSelect("tingreso", "", $gssql);
		$sGen->setEsSql();
		$sGen->setOptionSelect($DSoc["tipoingreso"]);
		$sGen->show(false);
		?>
		</td>
		</tr>

		<tr>
			<td><?php echo $xFRM->l()->getT("TR.Estado actual"); ?></td>
			<td><?php
				$sGen	= new cSelect("sestatus", "", "socios_estatus");
				$sGen->setOptionSelect($DSoc["estatusactual"]);
				$sGen->show(false);
			?></td>
			<td><?php echo $xFRM->l()->getT("TR.Empresa"); ?></td>
			<td>
		<?php
			$sGen	= new cSelect("dependencia", "", "socios_aeconomica_dependencias");
			$sGen->setOptionSelect($DSoc["dependencia"]);
			$sGen->show(false);
		?>
		</td>
		</tr>
		<tr>
			<td><?php echo $xFRM->l()->getT("TR.tipo de identificacion"); ?></td>
			<td><select id='id-TipoDeIdentificacion' name='c-TipoDeIdentificacion'>
				<option value='1'>Credencial I.F.E.</option>
				<option value='2'>Pasaporte</option>
				<option value='3'>Licencia de manejo</option>
				<option value='4'>Forma Migratoria FM1</option>
				<option value='5'>Cartilla Militar</option>
				<option value='6'>Libreta de Mar</option>
				<option value='9'>Otras[CURP, Acta]</option>
				<option value='99'>No Aplica/Desconocido</option>
			</select></td>
			<td><?php echo $xFRM->l()->getT("TR.Documento de identificacion"); ?></td>
			<td><input name='c-DocumentoDeIdentificacion' id='id-DocumentoDeIdentificacion' type='text' size="15" value='<?php echo $DSoc["documento_de_identificacion"]; ?>' /></td>
		</tr>
		<tr>
			<td><?php echo $xFRM->l()->getT("TR.correo_electronico"); ?></td>
			<td><input name="idcorreo" id="idcorreo" type="text" value="<?php echo $DSoc["correo_electronico"]; ?>" maxlength="30"  /></td>
			
			<td><?php echo $xFRM->l()->getT("TR.Tel. Principal"); ?></td>
			<td><input name="idmovil" id="idmovil" type="number" value="<?php echo $DSoc["telefono_principal"]; ?>" class="mny" maxlength="19"  /></td>
		</tr>
		<tr>
			<td><?php echo $xFRM->l()->getT("TR.dependientes_economicos"); ?></td>
			<td><input name="deconomicos" id="deconomicos" type="number" size="3" value="<?php echo $DSoc["dependientes_economicos"]; ?>" class="mny" maxlength="4"  /></td>

		<td><?php echo $xFRM->l()->getT("TR.Pais de Origen"); ?></td>
		<td><?php echo $xSel->getListaDePaises("", $pais)->get(); ?></td>
		</tr>
		<tr>
		<td><?php echo $xFRM->l()->getT("TR.Observaciones"); ?></td>
		<td colspan="3"><input name="observaciones" type="text" value= "<?php echo $DSoc["observaciones"]; ?>" size="80"></td>
		</tr>		
		<tr>
			<th colspan='4'><input type="button" name="Guardar" value="Guardar Datos" onClick="frmsolingreso.submit();"></th>
		</tr>
		</table>

</form>

<?php
} else {
		$idsocio = ( isset($_POST["uidsocio"]) ) ? $_POST["uidsocio"] : false;
		if ($idsocio != false ){
		
			$nombre 			= $_POST["nombre"];
			$paterno 			= $_POST["paterno"];
			$materno 			= $_POST["materno"];
			$rfc 				= $_POST["rfc"];
			$curp 				= $_POST["curp"];
			$fentrevista 			= fechasys();				// Fecha de Entervista
			$falta 				= fechasys();					// Fecha de Alta no Modificada
			$region 			= $_POST["region"];
			$cajalocal 			= $_POST["cajalocal"];
			$fnacimiento 			= $_POST["anac"] . "-" . $_POST["mnac"] . "-" . $_POST["dnac"];
			$lnacimiento			= $_POST["lnacimiento"];
			$ecivil 			= $_POST["ecivil"];
			$genero 			= $_POST["genero"];
			$observaciones 			= $_POST["observaciones"];
			$gposol				= $_POST["gruposol"];
			$tingreso 			= $_POST["tingreso"];
			$personalidad			= $_POST["figura"];
			
			$eacp 				= EACP_CLAVE;
			$dependencia 			= $_POST["dependencia"];
			$estatus 			= $_POST["sestatus"];
			
			$identicado_con			= $_POST["c-TipoDeIdentificacion"];
			$documento_de_identificacion	= $_POST["c-DocumentoDeIdentificacion"];
			$mail				= $_POST["idcorreo"];
			$telefono			= $_POST["idmovil"];
			$dependientes			= $_POST["deconomicos"];
			$pais				= parametro("idpais", EACP_CLAVE_DE_PAIS);
			
				$xSoc	= new cSocio($idsocio);
				$xArr	= array( "nombrecompleto" => $nombre,
								 "apellidopaterno" => $paterno,
								"apellidomaterno" => $materno,
								"rfc" => $rfc,
								"curp" => $curp,
								"fecha_de_revision" => $falta,
								"estatusactual" => $estatus,
								"region" => $region,
								"cajalocal" => $cajalocal,
								"fechanacimiento" => $fnacimiento,
								"lugarnacimiento" => $lnacimiento,
								"estadocivil" => $ecivil,
								"genero" => $genero,
								"observaciones" => $observaciones,
								"grupo_solidario" => $gposol,
								"tipoingreso" => $tingreso,
								"dependencia" => $dependencia,
								"tipo_de_identificacion" => $identicado_con, 
								"documento_de_identificacion" => $documento_de_identificacion,
								"personalidad_juridica" => $personalidad,
								"correo_electronico" => $mail,
								"telefono_principal" => $telefono,
								"dependientes_economicos" => $dependientes,
								"pais_de_origen" => $pais
								);
				$xSoc->setUpdate( $xArr	);
				echo "<p class='aviso'>LA ACTUALIZACION SE HA EFECTUADO SATISFACTORIAMENTE</p>";
				$xSoc->init();
				echo $xSoc->getFicha(true);
			}
}
?>
</fieldset>
</body>
<script type="text/javascript">
function setDefaults(){
	<?php
		if ( $action == false){
			echo "document.getElementById(\"id-TipoDeIdentificacion\").value	=  " .$DSoc["tipo_de_identificacion"] . ";";
		} else {
			echo "setTimeout('window.close()',2000);";
		}
	?>
}
</script>
</html>