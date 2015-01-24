<?php
/**
 * @see Estado de Cuenta de Creditos
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package creditos
 * @subpackage reportes
 * 
 * 		07Julio08	Formato Monedas
 *		31-Mayo-2008.- cCredito
 *		09Sept2008		Soporte a una Nueva Presentacion
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
$xHP				= new cHPage("Estado de Cuenta de Creditos", HP_REPORT);

$oficial 			= elusuario($iduser);
$output				= (isset($_GET["out"]) ) ? $_GET["out"] : "default";

$credito 			= parametro("pb", false, MQL_INT); $persona 			= parametro("pa", false, MQL_INT);//(isset($_GET["pa"])) ? $_GET["pa"] : false;		//Numero de Socio
$persona			= parametro("persona", $persona, MQL_INT);//
//if(isset($_REQUEST["persona"])){	$persona			= $_REQUEST["persona"]; }
//Agosto-2013
$credito			= parametro("credito", $credito, MQL_INT);

$f15 				= parametro("f15", false, MQL_BOOL);// (isset($_GET["f15"])) ?$_GET["f15"] : "no";
$f14 				= parametro("f14", false, MQL_BOOL);//(isset($_GET["f14"])) ?$_GET["f14"] : "no";
$f16 				= parametro("f16", false, MQL_BOOL);//(isset($_GET["f16"])) ?$_GET["f16"] : "no";
$f18 				= parametro("f18", false, MQL_BOOL); //(isset($_GET["f18"])) ?$_GET["f18"] : "no";		//Mostrar Movimiento Especifico

//$operacion 		= (isset($_GET["f19"])) ?$_GET["f19"] : "no";		//Codigo de Tipo de Operacion.- Mvto Especifico
$operacion			= parametro("f19",false, MQL_INT); $operacion	= parametro("operacion",$operacion, MQL_INT);

$fecha_inicial 		= (isset($_GET["on"])) ? $_GET["on"] : EACP_FECHA_DE_CONSTITUCION ;
$fecha_final 		= (isset($_GET["off"])) ? $_GET["off"] : fechasys();

$PieInts 			= (isset($_GET["dev"])) ? $_GET["dev"] : SYS_NINGUNO;
$ExtInf				= (isset($_GET["ext"])) ? true : false;

if ( $output != OUT_EXCEL ){

	echo $xHP->getHeader();
	
	echo getRawHeader();
	echo "<h2>ESTADO DE CUENTA DE CREDITOS</h2>";
} else {

  	$filename = "estado_de_cuenta-$credito-$persona-" . date("YmdHi") . ".xls";

  	header("Content-type: application/x-msdownload");
	header("Content-Disposition: attachment; filename=$filename");
	header("Pragma: no-cache");
	header("Expires: 0");
}



$varByFechas	= "";
$porSocio		= false;
$tInts			= 0;
$tCap			= 0;
$tOtros			= 0;
$tSdos			= 0;
//Describe Movimientos de Capital
$BCapital		= new cBases(1010);
//Describe Movimientos de Interes
$BInteres		= new cBases(1011);
$BIva			= new cBases(7021);
$xF				= new cFecha();
$xSQL			= new cSQLListas();

$ByCredito			= ($credito != false) ? "" : "";

$solo_este_mvto 	= ($f18 == "yes") ? " AND operaciones_mvtos.tipo_operacion=$operacion " : "";

$SinEstadisticos 	= " AND operaciones_mvtos.valor_afectacion!=0 AND operaciones_tipos.es_estadistico='0' ";
$solo_con_saldos 	= " ";

if ($f14 == "yes"){ $SinEstadisticos 	= "";	}

//if ($f16=="yes"){ $solo_con_saldos 	= " AND saldo_actual > 0 "; }

$sqlcred = "SELECT numero_socio, numero_solicitud,	saldo_actual, monto_autorizado	FROM creditos_solicitud	WHERE	numero_solicitud=$credito $solo_con_saldos	ORDER BY fecha_vencimiento DESC";

	if ( isset($credito) ) { $persona = mifila($sqlcred, "numero_socio");	}
	$NCreditos	= 0;
	//
	if($persona) {
		$sqlcred = "SELECT numero_socio, numero_solicitud, saldo_actual, monto_autorizado FROM creditos_solicitud		WHERE numero_socio=$persona $solo_con_saldos ORDER BY fecha_ministracion, fecha_vencimiento DESC";
		$persona 	= $persona;
		$porSocio	= true;
	}

	$xSoc			= new cSocio($persona); $xSoc->init();
	echo $xSoc->getFicha(true);
	echo "<hr />";
	$mycred =  getRecordset($sqlcred);

		while($rwc = mysql_fetch_array($mycred)) {
			$solicitud			= $rwc["numero_solicitud"];
			$NCreditos++;
			$cFC 				= new cCredito($solicitud);
			$cFC->initCredito();
			$cFC->setForceVista($ExtInf);
			$sdoTemp			= $cFC->getSaldoActual();
			echo $cFC->getFicha(true, "", true);
			$MontoAutorizado	= $rwc["monto_autorizado"];
			echo "<hr />";
			
			$where	= "";
			if ($f15 == "yes"){
				$where = " AND (operaciones_mvtos.docto_afectado=$solicitud) $SinEstadisticos $solo_este_mvto $varByFechas";
			} else {
				$where = " AND (operaciones_mvtos.docto_afectado=$solicitud) $SinEstadisticos $solo_este_mvto $varByFechas";
			}
			$sqlST	= $xSQL->getEstadoDeCuentaDeCreditos($where);
			$trs 	= "";
			$cap 	= 0;
			$ints	= 0;
			$otros	= 0;
			$iva	= 0;
			$sdo	= $MontoAutorizado;
			$fecha	= false;
			$clsMA	= "";
			$mes	= 0;

			//exit($sqlmvto);
			$rs 	= getRecordset($sqlST);;
			$i 	= 1;
			while ($rw =  mysql_fetch_array($rs)){
				$observa 	= substr($rw[6],0,20);
				$sdos 		= 0;
				$mtd		= "";
					if ( $rw["tipo_operacion"] == 120 ){
						if ($i == 1){
							$sdo = $MontoAutorizado;
							$sdos = getFMoney($sdo);
						}
						$sdo 	= $sdo - $rw["monto"];
						$sdos 	= getFMoney($sdo);
						$cap 	= $cap + $rw["monto"];
					} else {
						//si es primer mvto
						if ($i == 1){
							$sdo 	= $MontoAutorizado;
							$sdos 	= getFMoney($sdo);
						}
						//mvtos subsecuentes
						$sdos = $sdo;
						$sdos = getFMoney($sdo);
					}

				$monto 	= getFMoney($rw["monto"]);
				$tdEsp	= "";
				if (  $BCapital->getIsMember($rw["tipo_operacion"]) == true ){

					$tdEsp = "<td class='mny'>$monto</td><td /><td /><td />";
				} elseif ( $BInteres->getIsMember($rw["tipo_operacion"]) == true ){
					$tdEsp = "<td /><td class='mny'>$monto</td><td /><td />";
					$ints	+= $rw["monto"];
				} elseif ( $BIva->getIsMember($rw["tipo_operacion"]) == true ){
					$tdEsp = "<td /><td /><td class='mny'>$monto</td><td />";
					$iva	+= $rw["monto"];
				} else {
					$tdEsp = "<td /><td /><td /><td class='mny'>$monto</td>";
					$otros	+= $rw["monto"];
				}
				$xF->set($rw["fecha_operacion"]);
				$cssFecha	=($xF->mes() != $mes) ? " class='otromes' " : " class='date' ";
				$mtd	= "<tr>
					<td $cssFecha>" . $xF->getFechaDDMM() . "</td>
					<td onclick=\"msgbox('Total Recibo " . $rw["total_recibo"] . "')\">" . $rw[2] . "</td>
					<td>" . $rw[3] . "</td>
					<td class='ajustar'>" . $rw[4] . "</td>
					$tdEsp
					<td class='mny'>$sdos</td>
					<td class='ajustar'>$observa</td>
				</tr>";
				//parche de credito
				if ( ( strtotime($rw["fecha_operacion"]) < strtotime($fecha_inicial)) OR ( strtotime($rw["fecha_operacion"]) > strtotime($fecha_final) ) ){
					$mtd	= "";
				}
				$mes	= $xF->mes();
				$trs .= $mtd;
				$i++;
				//Imprime en pantalla el Aviso de NO COINCIDENCIA
			}
			//$sdo1		=
			if ( ( round($sdo, 2) != round($sdoTemp, 2) ) ){
				if ( FORCE_CUADRE_EN_OPERACIONES == true ){
					if( $cFC->getEstadoActual() == CREDITO_ESTADO_AUTORIZADO OR $cFC->getEstadoActual() == CREDITO_ESTADO_SOLICITADO ){ $sdo = 0; }
					$arrUp 	= array( "saldo_actual" => $sdo);
					$cFC->setUpdate($arrUp);
				}
				if( $cFC->getEstadoActual() != CREDITO_ESTADO_AUTORIZADO AND $cFC->getEstadoActual() != CREDITO_ESTADO_SOLICITADO ){
					if ($output != OUT_EXCEL) {
						//echo "<style> body { background-image: url(\"../images/error_saldos.png\");	background-repeat: repeat; } </style>";
					}
				}
			}
			$tSdos	+= $sdo;
			@mysql_free_result($rs);
	//Imprime la Tabla de Mvtos
			if($PieInts != SYS_NINGUNO){
				$IntAct		= $cFC->getInteresDevengado();
				$IntDevNorm	= $cFC->getInteresNormalDevengado();
				$IntDevMor	= $cFC->getInteresMoratorioDev();
				$IntPerNom	= $IntAct[SYS_INTERES_NORMAL];
				$IntPerMor	= $IntAct[SYS_INTERES_MORATORIO];
				$ints		= ( $IntDevMor + $IntDevNorm + $IntPerMor + $IntPerNom );
				
				
				$trs	.= "<tr><td /><td /><td /><td class='ajustar'>INTS. NORMALES DEVENGADOS</td><td /><td class='mny'>" . getFMoney($IntDevNorm) . "</td><td /><td /><td class='mny' /><td class='ajustar' /></tr>";
				$trs	.= "<tr><td /><td /><td /><td class='ajustar'>INT. NORMAL DEL PERIODO</td><td /><td class='mny'>" . getFMoney($IntPerNom) . "</td><td /><td /><td class='mny' /><td class='ajustar' /></tr>";
				
				$trs	.= "<tr><td /><td /><td /><td class='ajustar'>INTS. MORATORIO DEVENGADOS</td><td /><td class='mny'>" . getFMoney($IntDevMor) . "</td><td /><td /><td class='mny' /><td class='ajustar' /></tr>";
				$trs	.= "<tr><td /><td /><td /><td class='ajustar'>INT. MORATORIO DEL PERIODO</td><td /><td class='mny'>" . getFMoney($IntPerMor) . "</td><td /><td /><td class='mny' /><td class='ajustar' /></tr>";
			}
			echo "<table class='info'>
				<thead>
				<tr>
					<th width='4%'>Fecha</th><th width='4%'>CI</th><th width='3%'>Parc.</th><th width='25%'>Operacion</th>
					<th width='9%'>Capital</th><th width='9%'>Intereses</th><th width='9%'>IVA</th><th width='9%'>Otros</th>
					<th width='9%'>Saldo De Capital</th><th width='19%'>Observaciones</th>
				</tr>
				</thead>
				<tbody>
					$trs
				</tbody>
				
				<tfoot>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<th>SUMA DE PAGOS</th>
					<th class='sumas'>". getFMoney($cap) . "</th>
					<th class='sumas'>". getFMoney($ints) . "</th>
					<th class='sumas'>". getFMoney($iva) . "</th>
					<th class='sumas'>". getFMoney($otros) . "</th>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				</tfoot>
		</table>";
			$trs 	= "";
			$tCap	+= $cap;
			$tOtros	+= $otros;
			$tInts	+= $ints;
			if($porSocio == true){ echo "<br /><hr class='divisor'/><br />";}
		}
	@mysql_free_result($mycred);
	if($porSocio == true){
			echo "<table><tr>
					<td width='5%'>&nbsp;</td>
					<td width='5%'>&nbsp;</td>
					<td width='4%'>&nbsp;</td>
					<th width='30%'>TOTALES</th>
					<th width='9%' class='total'>". getFMoney($tCap) . "</th>
					<th width='9%' class='total'>". getFMoney($tInts) . "</th>
					<th width='9%' class='total'>". getFMoney($tOtros) . "</th>
					<th width='9%' class='total'>". getFMoney($tSdos) . "</th>
					<td width='20%'>&nbsp;</td>
				</tr></table>";
	}
if ( $output != OUT_EXCEL ){
echo getRawFooter();
?>
</body>
<script></script>
</html>
<?php
}
?>