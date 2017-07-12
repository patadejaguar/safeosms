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
$xHP				= new cHPage("TR.Recibo general", HP_RECIBO);
$xLang				= new cLang();
$xQL				= new MQL();

$recibo 			= parametro("recibo", false);
if($recibo == false){ header("location:../404.php?i=" . DEFAULT_CODIGO_DE_ERROR); }

echo $xHP->getHeader();
echo $xHP->setBodyinit();

echo getRawHeader();

$sqlrec 		= "SELECT * FROM operaciones_recibos WHERE idoperaciones_recibos=$recibo";
	$xRec		= new cReciboDeOperacion();
	$xRec->setNumeroDeRecibo($recibo, true);
	$DRec		= $xRec->getDatosReciboInArray();
	$idsocio 	= $DRec[ "numero_socio" ];
	$tiporec 	= $DRec[ "tipo_docto" ];
	$docto 		= $DRec[ "docto_afectado" ];
	$oficial	= $xRec->getOUsuario()->getNombreCompleto();
	
	$xTipo		= $xRec->getOTipoRecibo();
	
	$eltitulo 	= $xTipo->getNombre();
	echo "<hr /><p class='bigtitle'>$eltitulo</p><h />";
//	$anotacion = "";
	$totaloperacion		= $DRec[ "total_operacion" ];
	$total_fmt			= getFMoney( $totaloperacion );
	$montoletras		= convertirletras($totaloperacion);
// obtiene datos del socio
	$xSoc				= new cSocio($idsocio);
	$xSoc->init();
	
	$DSoc				= $xSoc->getDatosInArray();
	if ($idsocio != DEFAULT_SOCIO) {
		$nombre = $xSoc->getNombreCompleto();
	} else {
		$nombre  		= $DRec[  "cadena_distributiva" ];
	}
	$direccion  		= sociodom($idsocio);
	$rfc 				= $DSoc[ "rfc" ];
	$curp 				= $DSoc[ "curp" ];
// obtiene datos del documento que ayudaran al detalle en contabilidad
	$observaciones = mifila($sqlrec, "observacion_recibo");
echo "<table width='100%'  border='0'>
  <tr>
    <td class='title'>" . $xLang->getT("TR.CLAVE_DE_PERSONA") . "</td>
    <td>$idsocio</td>
    <td class='title'>" . $xLang->getT("TR.NOMBRE_COMPLETO") . "</td>
    <td>$nombre</td>
  </tr>
  <tr>
  	<td class='title'>" . $xLang->getT("TR.DOMICILIO") . "</td>
  	<td colspan='3'>$direccion</td>
  </tr>
  <tr>
    <td class='title'>" . $xLang->getT("TR.RFC") . "</td> <td>$rfc</td>
    <td class='title'>" . $xLang->getT("TR.CURP") . "</td><td>$curp</td>
  </tr>
  <tr>
    <td class='title'>Rec. Fiscal</td>	<td><b>" . $DRec["recibo_fiscal"] . "</b></td>
    <td class='title'>Tipo de Pago</td>    <td>" . strtoupper( $DRec["tipo_pago"] ) . "</td>
  </tr>
</table>
<hr>\n";

echo "<table width='100%'  border='0'>
  <tr>
  	<th scope='col' width='8%'>#Op.</th>
    <th scope='col' width='50%'>Concepto</th>
    <th scope='col'  width='20%'>Monto</th>
    <th scope='col' width='22%'>Destino</th>
  </tr>";
$sqlmvto	= "SELECT * FROM operaciones_mvtos WHERE recibo_afectado=$recibo";
$rs			= $xQL->getDataRecord($sqlmvto);
	//$rsmvto = mysql_query($sqlmvto, cnnGeneral());
	//while($rwm = mysql_fetch_array($rsmvto)) {
	foreach ($rs as $rwm){
		
		
		$montomvto 		= getFMoney( $rwm[ "afectacion_real" ] );
		$documento		= $rwm["docto_afectado"];
		$documento		= ($documento == DEFAULT_CREDITO) ? "" : $documento;
		$operacion		= $rwm["idoperaciones_mvtos"];
		$mobservaciones	= $rwm["detalles"];
		$xTipoOp		= new cTipoDeOperacion($rwm[ "tipo_operacion" ]);
		$xTipoOp->init();
		$tipomvto 	= $xTipoOp->getNombre();
		echo " <tr>
			<td>$operacion</td>
    		<td>$tipomvto</td>
		    <td class='money'>$montomvto</td>
		    <td>$documento-$mobservaciones</td>
			</tr>";
	}
	echo "</table>
	<hr>";

	$rs	= null;

echo "	<table border='0'  >
		<tr>
			<td class='title'>TOTAL RECIBO: ($montoletras)</td>
			<td class='mny'>$total_fmt</td>
		</tr>
		<tr>
			<td>Observaciones</td><td>$observaciones</td>
		</tr>
	</table>";
	echo "
	<hr />
	<table border='0' width='100%' align='center'>
	<tr>
	
	<td><center>Por la Caja</center></td>
	<td><center>Firma de Conformidad</center></td>
	
	</tr>
	<tr>
	<td><br /><br /></td>
	</tr>
	<tr>
	
	<td><center>
		$oficial<br />
		" . date("Y-m-d H:s:i") . "|$recibo</center>
		
	</td>
	
	<td><center>$nombre</center></td>
	
	</tr>
	</table>";
	echo getRawFooter();
?>
</body>
</html>
