<?php
/**
 * Solicitud de Credito
 *
 * @author Balam Gonzalez Luis Humberto
 * @version 1.1.0
 * @package creditos
 * @subpackage formatos
 * 		07Julio08	Fecha Actual, Original y Lugar de la Solicitud
 */
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
$xHP			= new cHPage("TR.Solicitud de de Credito", HP_REPORT);
$xLoc			= new cLocal();
$xList			= new cSQLListas();

$oficial = elusuario($iduser);

echo $xHP->getHeader(true);

echo $xHP->setBodyinit("javascript:window.print()");

echo getRawHeader();
$sqlDic			= new cSQLListas();

$idsolicitud 	= (isset($_GET["solicitud"])) ? $_GET["solicitud"] : DEFAULT_CREDITO;
$entidad		= EACP_NAME;

if($idsolicitud == DEFAULT_CREDITO ){
	echo JS_CLOSE;
} else {
	$xCred			= new cCredito($idsolicitud);
	$xCred->init();
	
	$siavales 		= (isset($_GET["avales"])) ? $_GET["avales"] : SYS_NINGUNO;
	$sigarantias 	= (isset($_GET["garantias"])) ? $_GET["garantias"] : SYS_NINGUNO;
	$sipatrimonio 	= (isset($_GET["patrimonio"])) ? $_GET["patrimonio"] : SYS_NINGUNO;
	$siflujo 		= (isset($_GET["flujo"])) ? $_GET["flujo"] : SYS_NINGUNO;
	$sihistorial 	= (isset($_GET["historial"])) ? $_GET["historial"] : SYS_NINGUNO;

	$nowdate 		= fechasys();



	
	
	$rwc				= $xCred->getDatosDeCredito();

	$fecha_de_solicitud		= $rwc["fecha_solicitud"];
	// datos generales del socio
	$idsocio 			= $xCred->getClaveDePersona();	// Numero de Socio

	$xSocio				= new cSocio($idsocio);
	$xSocio->init();

	$mynom 				= $xSocio->getNombreCompleto();
	$thisdom 			= $xSocio->getDomicilio();

	$sqlconv 			= "SELECT * FROM creditos_tipoconvenio WHERE idcreditos_tipoconvenio =" . $xCred->getClaveDeConvenio();
	$dconv 				= obten_filas($sqlconv);
	$convenio 			= $dconv["descripcion_tipoconvenio"];
	$mod_del_conv 		= $dconv["tipo_de_integracion"];
?>
<!-- -->
<h2>SOLICITUD DE CR&Eacute;DITO [<?php echo $convenio; ?> # <?php echo $idsolicitud; ?>]</h2>
<fieldset>
<table >
	<tbody>
		<tr>
			<th width="25%" class="der">Lugar</th>
			<td width="75%"><?php echo $xLoc->DomicilioMunicipio() . ", " . $xLoc->DomicilioEstado() ?></td>
		</tr>
		<tr>
			<th class="der">Fecha Actual</th>
			<td><?php echo getFechaLarga(); ?></td>
		</tr>
		<tr>
			<th class="der">Fecha de Solicitud</th>
			<td><?php echo getFechaLarga($fecha_de_solicitud); ?></td>
		</tr>
	</tbody>
</table>
</fieldset>

<?php
	
	$sqlconyuge = "SELECT numero_socio, CONCAT(nombres, ' ', apellido_paterno, ' ', apellido_materno)
					AS 'Nombre_Completo',
	 				CONCAT( telefono_residencia, ', ', telefono_movil) as 'telefonos', ocupacion
					from socios_relaciones where consanguinidad=3
					AND socio_relacionado=$idsocio";
		$conyuge 		= obten_filas($sqlconyuge);
		$txtConyuge		= "";
		

		//$conyuge[0] - $conyuge[1] - Tel(s): $conyuge[2] Ocupaci&oacute;n: $conyuge[3]
		$rwy			= $xSocio->getDatosInArray();
		$idconyuge		= $xSocio->getClaveDePersonaDeConyuge();
		
		if($conyuge > 0){
			$xCon		= new cSocio($idconyuge); $xCon->init();
			$DConT		= $xCon->getTelefonos();
			$txtConyuge	.= $idconyuge ."-" . $xCon->getNombreCompleto();
			$txtConyuge	.= " TEL(S): " . $DConT["principal"] ;
			$conyuge_ocup	= $xCon->getDatosActividadEconomica();
			$txtConyuge	.= " OCUPACI&Oacute;N: " . $conyuge_ocup["puesto"];
			
		}
		
		$genero 		= eltipo("socios_genero", $rwy[15]);
		$civil 			= eltipo("socios_estadocivil", $rwy[14]);
		$ocupacion 		= volcartabla("socios_aeconomica", 18, "socio_aeconomica=$idsocio");
		$edad 			= (int)( (restarfechas($nowdate, $rwy[11]) ) / 365) . " A&ntilde;os";
		
		$dependientes		= $rwy["dependientes_economicos"];
		$telp			= $rwy["telefono_principal"];
		$mail			= $rwy["correo_electronico"];
		
		$grupo_assoc 		= $rwy["grupo_solidario"];
		//$regimen_mat =
		$DDom 			=  $xSocio->getDatosDomicilio(99);
		$eldom			= "<tr>
						<th >Calle/Codigo Postal</th>
						<td>" . $DDom["calle"] . "/" . $DDom["codigo_postal"] . "</td>
						<th >Num. Ext./Int.</th>
						<td>" . $DDom["numero_exterior"] . "/" . $DDom["numero_interior"] . "</td>
					</tr>
					<tr>
						<th >Colonia</th>
						<td>" . $DDom["colonia"] . "</td>
						<th >Telefono(s)</th>
						<td>" . $DDom["telefono_residencial"] . "/" . $DDom["telefono_movil"] . "</td>
					</tr>
					<tr>
						<th >Localidad</th>
						<td>" . $DDom["localidad"] . "</td>
						<th >Municipio/Estado</th>
						<td>" . $DDom["municipio"] . "/" . $DDom["estado"] . "</td>
					</tr>";
		echo "
		<fieldset>
		<legend>[ Datos Generales del Solicitante ]</legend>
			<table border='0' width='100%'>
				<tr>
					<th width='15%'>Clave de Persona</th>
					<td width='35%'>$rwy[0]</td>
					<th width='15%'>Nombre(s)</th>
					<td width='35%'>$rwy[1]</td>
				</tr>
				<tr>
					<th>Apellido Paterno</th><td>$rwy[2]</td>
					<th>Apellido Materno</th><td>$rwy[3]</td>
				</tr>
				<tr>
					<th>R. F. C.</th>
					<td>$rwy[4]</td>
					<th>C. U. R. P.</th>
					<td>$rwy[5]</td>
				</tr>
				<tr>
					<th>Genero</th>
					<td>$genero</td>
					<th>Estado Civil</th>
					<td>$civil</td>
				</tr>
				<tr>
					<th>Edad</th><td>$edad</td>
					<th>Ocupaci&oacute;n</th><td>$ocupacion</td>
				</tr>
				<tr>
					<th>Tel&eacute;fono Principal</th>
					<td>$telp</td>
					<th>Correo Electr&oacute;nico</th>
					<td>$mail</td>
				</tr>
$eldom
				<tr>
					<th>R&eacute;gimen Matrimonial</th>
					<td>$rwy[22]</td>
					<th>Conyuge</th>
					<td>$txtConyuge</td>
				</tr>
			</table>
			</fieldset>";


	// datos de la solicitud
	$perpagos 			= eltipo("creditos_periocidadpagos", $rwc[10]);
	//$eldest = eltipo("creditos_destinos", $rwc[19]);
	$montosol 			= number_format($rwc[3], 2, '.', ',');
	$montoletras 			= convertirletras($rwc[3]);
	//$descripcion_dest = $rwc[35];
	$tasa 				= $rwc[9] * 100;

	$plazo_en_meses			= round( ($rwc["plazo_en_dias"] / 30.4166666666666666666666 ), 0 );

	$credito_destino_extendido 	= $rwc["descripcion_aplicacion"];

	$tasa_ahorro			= $rwc["tasa_ahorro"];
	$contrato_corriente		= $rwc["contrato_corriente_relacionado"];

	if ($contrato_corriente == CTA_GLOBAL_CORRIENTE ){
		$contrato_corriente == "_NO_ASIGNADO_";
	}
	$tasa_ahorro				= $tasa_ahorro	* 100;

	echo "
		<fieldset>
		<legend>[ Datos Generales del Credito ]</legend>
	<table width='100%'>
		<tr>
			<th>Numero de Solicitud</td>
			<td>$rwc[0]</td>
			<th>Periocidad de Pago</td>
			<td>$perpagos</td>
		</tr>

		<!-- <tr>
			<th>Clasificacion del Destino</td>
			<td></td>
			<th>Destino del Recurso</td>
			<td></td>
		</tr> -->
<tr>
	<th>Plazo En Meses (Aprox)</td>
	<td><b>$plazo_en_meses Meses</b></td>
</tr>
		<tr>
			<th>Destino del Recurso</td>
			<td colspan='3'>$credito_destino_extendido</td>
		</tr>
		<tr>
			<th>Tasa de Interes Anual Bruta</td>
			<td>$tasa %</td>
			<th>Numero de Pagos</td>
			<td>$rwc[8]</td>
		</tr>
		<tr>
			<th>Monto Solicitado</td>
			<td><b>$montosol</b></td>
			<th colspan='2'>( $montoletras )</td>
		</tr>
		<tr>
			<th>Contrato de Deposito Relacionado</td>
			<td>$contrato_corriente</td>
			<th>Tasa de Ahorro</td>
			<td>$tasa_ahorro %</td>
		</tr>
		</table>
		</fieldset>";

	// dependientes economicos.

	$sqlde = "SELECT
			`socios_relacionestipos`.`descripcion_relacionestipos` AS `tipo_de_relacion`,
			`socios_relaciones`.`curp`,
			CONCAT(`socios_relaciones`.`nombres`, ' ',
		    `socios_relaciones`.`apellido_paterno`, ' ',
		    `socios_relaciones`.`apellido_materno`)                    AS `nombre_completo`,
			`socios_relaciones`.`domicilio_completo`  AS `domicilio`
		
		FROM
			`socios_relaciones` `socios_relaciones` 
				INNER JOIN `socios_relacionestipos` `socios_relacionestipos` 
				ON `socios_relaciones`.`tipo_relacion` = `socios_relacionestipos`.
				`idsocios_relacionestipos` 
		WHERE
			(`socios_relaciones`.`socio_relacionado` = $idsocio) AND consanguinidad != 99 AND estatus=10 AND `socios_relaciones`.tipo_relacion!=21";
		$xTblPar	= new cTabla($sqlde);
		$xTblPar->Show( "Dependientes Economicos y Familia del Solicitante", false);

	// referencias personales
//======================================================================================================================
	$sqldp = "SELECT
			`socios_relacionestipos`.`descripcion_relacionestipos` AS `tipo_de_referencia`,
			`socios_relaciones`.`curp`,
			CONCAT(`socios_relaciones`.`nombres`, ' ',
		    `socios_relaciones`.`apellido_paterno`, ' ',
		    `socios_relaciones`.`apellido_materno`)                    AS `nombre_completo`,
			`socios_relaciones`.`domicilio_completo`  AS `domicilio`
		
		FROM
			`socios_relaciones` `socios_relaciones` 
				INNER JOIN `socios_relacionestipos` `socios_relacionestipos` 
				ON `socios_relaciones`.`tipo_relacion` = `socios_relacionestipos`.
				`idsocios_relacionestipos` 
		WHERE socio_relacionado=$idsocio AND (`socios_relaciones`.tipo_relacion=21
		OR `socios_relaciones`.tipo_relacion=22 OR `socios_relaciones`.tipo_relacion=23) AND estatus=10";

		$xTblRPer	= new cTabla($sqldp);
		$xTblRPer->Show( "Referencias Personales, Comerciales y Bancarias", false);

if ($siflujo == "yes") {

	$sql_flujo = $sqlDic->getFlujoDeEfectivo($idsolicitud);
	$rsf 		= getRecordset($sql_flujo);
	$suma_E 	= 0;
	$suma_I 	= 0;
	$tds 		= "";
	$tde 		= "";

	while($rw = mysql_fetch_array($rsf)){
		$monto = $rw["monto"];
		if($monto>0){
			$monto = getFMoney($monto);
			$tde = "<td class='mny'>$monto</td>
			<td></td>";
		} else {
			$monto = getFMoney($monto);
			$tde = "<td></td>
			<td class='mny'>$monto</td>";
		}
		$tds = $tds . "
		<tr>
			<td>$rw[2]</td>
			<td>$rw[5]</td>
			<td>$rw[3]</td>
			<td class='mny'>" . getFMoney($rw[6]) . "</td>
			$tde

		</tr>";
	}
	@mysql_free_result($rsf);
		$sql_suma_E = "SELECT * FROM sumas_flujo_efectivo WHERE solicitud=$idsolicitud AND tipo=2";
		$suma_E = getFMoney(mifila($sql_suma_E, "sumas"));
		$sql_suma_I = "SELECT * FROM sumas_flujo_efectivo WHERE solicitud=$idsolicitud AND tipo=1";
		$suma_I = getFMoney(mifila($sql_suma_I, "sumas"));
		$neto = ($suma_I + $suma_E);
		$neto = getFMoney($neto);
	echo "
	<fieldset>
	<legend>[ Flujo de Efectivo ]</legend>
	<table width='100%'>
  <tr>
    <th>Origen</th>
    <th>Descripci&oacute;n</th>
    <th>Frecuencia</th>
    <th>Declarado</th>
    <th>INGRESOS</th>
    <th>EGRESOS</th>

  </tr>
  		$tds
	<tr>

	<th colspan='2'>CAPACIDAD DIARIA</th>
	<th>$neto</th>
	<td>SUMAS</td>
	<th>$suma_I</th>
	<th>$suma_E</th>
	</tr>
</table>
</fieldset>
";
}
		// relacion patrimonial
	if ($sipatrimonio == "yes") {
			echo "<fieldset>
			<legend>[ Balance Patrimonial ]</legend>";
			$sqlactivos = "SELECT socios_patrimoniotipo.descripcion_patrimoniotipo AS 'Tipo_de_patrimonio', socios_patrimonio.monto_patrimonio AS 'Monto_Patrimonio', ";
			$sqlactivos .= " socios_patrimonio.fecha_expiracion AS 'Fecha_Expiracion', socios_patrimonio.documento_presentado AS 'Documento_Presentado' ";
			$sqlactivos .= " FROM socios_patrimonio, socios_patrimoniotipo WHERE socios_patrimonio.socio_patrimonio=$idsocio ";
			$sqlactivos .= " AND socios_patrimoniotipo.idsocios_patrimoniotipo=socios_patrimonio.tipo_patrimonio";
				// activos.
				//echo $sqlactivos;
				sqltabla($sqlactivos, "", "fieldnames");
			echo "</fieldset>";
	}
		// garantias
	if ($sigarantias == "yes") {
			echo "<fieldset>
			<legend>[ Relacion de Garantias Ofrecidas por el Solicitante ]</legend>";
			//TODO: mejorar presentacion de la solicitud de credito
			$sqlgarantias = "SELECT creditos_tgarantias.descripcion_tgarantias AS 'Tipo_de_Garantia',
					/*creditos_tvaluacion.descripcion_tvaluacion AS 'Tipo_de_Valuacion', */
					
					creditos_garantias.descripcion,
					creditos_garantias.documento_presentado AS 'Docto_o_Fact',
					
					FORMAT(creditos_garantias.monto_valuado, 2) AS 'Monto_valuado',
					creditos_garantias.fecha_adquisicion AS 'Fecha_de_Adquisicion'
					FROM creditos_tvaluacion, creditos_garantias, creditos_tgarantias
					WHERE creditos_tgarantias.idcreditos_tgarantias=creditos_garantias.tipo_garantia
					AND creditos_tvaluacion.idcreditos_tvaluacion=creditos_garantias.tipo_valuacion
					AND creditos_garantias.solicitud_garantia=$idsolicitud ";
				sqltabla($sqlgarantias, "", "fieldnames");
			echo "</fieldset>";
	}
		// avales
	if ($siavales == "yes") {
			$sqlavales = $xList->getListadoDeAvales($idsolicitud, $idsocio);
			$xTblAv		= new cTabla($sqlavales);
			$xTblAv->Show("TR.Relacion de Avales presentados por la persona", false);

	}
	if ($sihistorial == "yes") {
			$xTblH		= new cTabla($sqlDic->getListadoDeNotas($idsocio, $idsolicitud));
			$xTblH->Show("TR.Historial Moral del Socio", false);
			
	}

	if($mod_del_conv==3){
			echo "<fieldset>
			<legend>[ Integrantes del Grupo Solidario ]</legend>";

			$sqltb = "SELECT
				`socios_general`.`codigo`,
				CONCAT(`socios_general`.`nombrecompleto`, ' ',
				`socios_general`.`apellidopaterno`, ' ',
				`socios_general`.`apellidomaterno`) AS 'nombre'
			FROM
				`socios_general` `socios_general`
			WHERE
				`socios_general`.`grupo_solidario`= $grupo_assoc
				/* Filtrar el Grupo Global */
				AND
				`socios_general`.`grupo_solidario`!= " . DEFAULT_GRUPO ."
				/* Excluir a la Representante */
				AND
				`socios_general`.`codigo` !=$idsocio
			LIMIT 0,50";
			$cGrupo = new cTabla($sqltb);
			$xtbl = $cGrupo->Show();
			echo $xtbl;
			echo "</fieldset>";
	}

	echo "<hr /><table >
	<tr>
	<td><h4>PROTESTA DE VERDAD</h4></td>
	<td><center>Recibe la Solicitud</center></td>
	</tr>
	
	<tr>
	<td style=\"width: 50%;\">
		<p>Bajo protesta de decir la verdad, los firmantes manifiestan que los datos son ver&iacute;dicos
		y que las firmas que calzan este documento son las que usan en todos sus documentos
		p&uacute;blicos y privados, y que con su firma autorizan asimismo a MAE DEL GOLFO, S.A. DE C.V. SOFOM ENR para que traten los datos contenidos en este documento para cualquier fin comercial o de otra naturaleza que estimen conveniente.</p>
		<br />
		<br />
	</td>
	</tr>
	<tr>
	<td><center>$mynom
	<br />
	Fecha en que Debe Presentarse:  ___/________/_____</center></td>
		<td><center>$oficial</center></td>
	</tr>
	</table>";
?>
<table      >
	<tbody>
		<tr>
			<td colspan="0">
			<fieldset>
				<legend>[&nbsp;&nbsp;DICTAMEN DEL COMIT&Eacute; DE CR&Eacute;DITO&nbsp;&nbsp;]</legend>
				<br />
				<hr />
				<br />
				<hr />
				<br />
				<hr />
				<br />
				<hr />
			</fieldset></td>

		</tr>

	</tbody>
	<tfoot>
		<tr>
			<h4>AVISO DE PRIVACIDAD</h4>
			<p>Para poder iniciar el proceso de an&aacute;lisis de su capacidad crediticia y financiera es necesario que nos proporcione
			ciertos datos personales,
			financieros y patrimoniales que pudieran ser considerados datos sensibles por lo que por este medio autorizo expresamente a
			MAE DEL GOLFO, S.A. DE C.V., SOFOM ENR,
			as&iacute; como sus subsidiarias, afiliadas, controladora y dem&aacute;s empresas relacionadas para usar, mantener,
			administrar y tratar la informaci&oacute;n proporcionada incluso ante terceras personas ajenas a
			GRUPO PADIO en caso de ser requerido para integrar debidamente el an&aacute;lisis de su solicitud de cr&eacute;dito. Para
			mayor informaci&oacute;n acerca del tratamiento de los derechos que pueden hacer valer, puede contactar a
			nuestro departamento especializado de datos personales al correo electr&oacute;nico: contacto@grupopadio.com.mx,
			en donde podr&aacute;n atender sus solicitudes y ejercer sus derechos ARCO.</p>
		</tr>
		<tr>
			<br ><br >
			<h4><?php echo $mynom; ?></h4>
		</tr>
		<tr>
			<h4>LEYENDA PARA PERSONAS POLITICAMENTE EXPUESTAS</h4>
			<p>He desempe&ntilde;ado en los últimos doce meses com jefe de estado o de gobierno, l&iacute;der pol&iacute;tico, funcionario gubernamental,
			judicial o militar, ejecutivo de empresas estatales o funcionario directivo de un partido politico, o
			mi c&oacute;nyugue tiene parentesco por consanguinidad o afinidad hasta en un segundo grado, o sociedades en las que mantenga
			v&iacute;nculos patrimoniales con algunas de las personas se&ntilde;aladas anteriormente.</p>
		</tr>
		<tr>
			<br ><br >
			<h4><?php echo $mynom; ?></h4>
		</tr>
		
		<tr>
			<h4>LEYENDA PARA PERSONAS POLITICAMENTE EXPUESTAS</h4>
			<p>He desempe&ntilde;ado en los &uacute;ltimos doce meses como jefe de estado o de gobierno, l&iacute;der pol&iacute;tico, funcionario gubernamental,
			judicial o militar, ejecutivo de empresas estatales o funcionario directivo de un partido politico, o
			mi c&oacute;nyugue tiene parentesco por consanguinidad o afinidad hasta en un segundo grado, o sociedades en las que mantenga
			v&iacute;nculos patrimoniales con algunas de las personas se&ntilde;aladas anteriormente.</p>
			<p>NO (&nbsp;&nbsp;&nbsp;)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SI (&nbsp;&nbsp;&nbsp;) Especif&iacute;que:____________________________________</p>
		</tr>
		<tr>
			<br ><br >
			<h4><?php echo $mynom; ?></h4>
		</tr>
	</tfoot>
</table>
	<?php

}
echo getRawFooter();
?>
</body>
</html>
