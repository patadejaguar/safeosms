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
$xHP		= new cHPage("TR.Plan_de_Pagos", HP_REPORT);
$xF			= new cFecha();
$ql			= new MQL();

$oficial 	= elusuario($iduser);

$idrecibo 	= parametro("idrecibo", false, MQL_INT);
$idrecibo 	= parametro("recibo", $idrecibo, MQL_INT);

$idsolicitud	= parametro("is", false, MQL_INT);
$idsolicitud	= parametro("credito", $idsolicitud, MQL_INT);

$ShowAvales	= parametro("p", false, MQL_BOOL);



if($idrecibo == false ){
	if($idsolicitud != false){
		$xCred			= new cCredito($idsolicitud); $xCred->init();
		$idrecibo		= $xCred->getNumeroDePlanDePagos();
		if(setNoMenorQueCero($idrecibo) > 0){
			
		} else {
			exit(JS_CLOSE);
		}
	} else {
		exit(JS_CLOSE);
	}
}

$xHP->setTitle($xHP->getTitle() .  " # $idrecibo");
echo $xHP->getHeader(true);

echo $xHP->setBodyinit("window.print()");
echo $xHP->getEncabezado();

$PlanBody			= $xHP->h1() . "<hr />";

$xRec				= new cReciboDeOperacion(false, false, $idrecibo); $xRec->init();
$xSoc				= $xRec->getSocio(); $xSoc->init();
$xCred				= $xRec->getCredito(); $xCred->init();
$xF					= new cFecha();

$idsocio 			= $xSoc->getCodigo(); //"numero_socio"
$idsolicitud 		= $xRec->getCodigoDeDocumento(); // docto_afectado
$nombre 			= $xSoc->getNombreCompleto();
// ------------------------------------ DATOS DE LA SOLICITUD.
$tasa_ahorro 		= $xCred->getTasaDeAhorro() * 100;
$tasa_interes 		= $xCred->getTasaDeInteres() * 100;
$dias_totales 		= $xCred->getDiasAutorizados();
$numero_pagos		= $xCred->getPagosAutorizados();
$nombre_otro		= "";

$observaciones		= $xRec->getObservaciones();

$extTool			= "";

	echo $xSoc->getFicha();
	echo $xCred->getFicha(true, "", false);

	$pagoactual		= $xCred->getPeriodoActual();
	
	if ($ShowAvales == true){
		$avals	= $xCred->getAvales_InText();
		echo $avals;
	}
//------------------------------------- DATOS DEL RECIBO
	$sumrec = $xRec->getTotal();
		$sql = "
			SELECT operaciones_mvtos.periodo_socio AS 'parcialidad', fecha_afectacion,
					operaciones_tipos.idoperaciones_tipos  As 'tipo',
					operaciones_tipos.descripcion_operacion AS 'concepto' ,
					operaciones_mvtos.afectacion_real AS 'monto',
					operaciones_mvtos.saldo_actual AS 'saldo',
					operaciones_mvtos.valor_afectacion AS 'afectacion'
			FROM 	`operaciones_mvtos` `operaciones_mvtos`
					INNER JOIN `operaciones_tipos` `operaciones_tipos`
					ON `operaciones_mvtos`.`tipo_operacion` = `operaciones_tipos`.
					`idoperaciones_tipos`

			WHERE operaciones_mvtos.recibo_afectado=$idrecibo
			ORDER BY operaciones_mvtos.periodo_socio, operaciones_tipos.idoperaciones_tipos";
		$rs 		= $ql->getDataRecord($sql);
		$trs 		= "";
		//Parcialidad, evaluador de inciio y final
		$PInit		= 0;
		$PFin		= 0;

		$capital	= 0;
		$interes	= 0;
		$iva		= 0;
		$ahorro		= 0;
		$otros		= 0;
		$total		= 0;
		
		$SUMCap		= 0;
		$SUMInt		= 0;
		$SUMIva		= 0;
		$SUMAh		= 0;
		$SUMOtros	= 0;
		$SumTotal	= 0;
		$saldo		= 0;
		
		$arrLetras	= array();

		$tds		= "";
			foreach ($rs as $rw){
				$PInit			= $rw["parcialidad"];
				$parcialidad	= $rw["parcialidad"];
				$tipo			= $rw["tipo"];
				$afectacion		= $rw["afectacion"];
				$arrLetras[$parcialidad][SYS_FECHA]	= $rw["fecha_afectacion"];
				
				if($PInit != $PFin){ $trs 	.= $tds; $tds	= ""; }				
						switch($tipo){
							case 410:
								$capital	= $rw["monto"];
								$SUMCap		+= $capital;
								$saldo		= $rw["saldo"];
								$arrLetras[$parcialidad][SYS_CAPITAL]	= $capital;
								break;
							case 411:
								$interes	= $rw["monto"];
								$SUMInt 	+= $interes;
								$arrLetras[$parcialidad][SYS_INTERES_NORMAL]	= $interes;
								if($xCred->getFormaDePago() == CREDITO_TIPO_PAGO_INTERES_PERIODICO){
									$saldo		= $rw["saldo"];
								}
								break;
							case 412:
								$ahorro		= $rw["monto"];
								$SUMAh		+= $ahorro;
								$arrLetras[$parcialidad][SYS_AHORRO]	= $ahorro;
								break;
							case 413:
								$iva		= $rw["monto"];
								$SUMIva		+= $iva;
								$arrLetras[$parcialidad][SYS_IMPUESTOS]	= $iva;
								break;
							default:
								$otros		= $rw["monto"];
								$SUMOtros	+= $otros;
								$top		= getInfTOperacion($tipo);
								$nombre_otro=$top["descripcion_operacion"];
								if($afectacion == -1){
									$otros	= ($otros * -1);
								}
								$arrLetras[$parcialidad][SYS_GASTOS_DE_COBRANZA]	= $otros;
								break;
						}
						$total		= $capital + $interes + $ahorro + $iva + $otros;
						$arrLetras[$parcialidad][SYS_TOTAL]	= $total;
						
						$tdAhorro 	= "<td class='mny'>" . getFMoney($ahorro) . "</td>";
						$tdOtros	= "<td class='mny'>" . getFMoney($otros) . "</td>";

						if ( $ahorro == 0){ $tdAhorro 	= ""; }
						if ($otros == 0){
							$tdOtros	= ($capital == 0 AND $nombre_otro != "") ? "<td />" : "";
						}
						if( ($capital > 0) OR ($pagoactual == 0) OR ($xCred->getPagosSinCapital() == true) ){
						/*$tds = "<tr>
							<td>" . $parcialidad . "</td>
							<td class='ctr'>" . getDiaDeLaSemana($rw["fecha_afectacion"]) . "</td><td class='ctr'>" .  $xF->getFechaCorta($rw["fecha_afectacion"]) . "</td>
							<td class='mny'>" . getFMoney($capital) . "</td>
							<td class='mny'>" . getFMoney($interes) . "</td>
							<td class='mny'>" . getFMoney($iva) . "</td>
							$tdAhorro
							$tdOtros
							<td class='mny'>" . getFMoney($total) . "</td>
							<td class='mny'>" . getFMoney($saldo) . "</td>
						</tr>";*/
						}
				
				$PFin	= $PInit;
			}
			//TODO: verificar error en este mvto
			$trs 	.= $tds;
			
			$SumTotal	= $SUMCap + $SUMAh + $SUMInt + $SUMIva + $SUMOtros;
			$netoNivel	= $SumTotal;

			$thAhorro 	= " <th>" . $xHP->lang("Ahorro") .  "</th>";
			$thOtros	= "<th>$nombre_otro</th>";
			$thIVA		= "<th>" . $xHP->lang("impuesto_al_consumo") . "</th>";
			$tfAhorro	= "<th class='mny'>" . getFMoney($SUMAh) . "</th>";
			$tfOtros	= "<th class='mny'>" . getFMoney($SUMOtros) . "</th>";
			$tfIVA		= "<th class='mny'>" . getFMoney($SUMIva) . "</th>";
			if ($SUMAh == 0){
				$thAhorro 	= "";
				$tfAhorro	= "";
			}
			if ( $SUMOtros == 0){
				$thOtros 	= "";
				$tfOtros	= "";
			}
			if($SUMIva == 0){ $thIVA = ""; $tfIVA = 0; }
			foreach ($arrLetras as $parcial => $dat){
				$txt	= "<tr><th>" . $parcial . "</th>";
				 
				$txt		.= (isset($dat[SYS_FECHA])) ? "<td>" . $xF->getDayName($dat[SYS_FECHA]) . "|" . $xF->getFechaCorta($dat[SYS_FECHA]) . "</td>" : "<td />";
				$txt		.= (isset($dat[SYS_CAPITAL])) ? "<td class='mny'>" . getFMoney($dat[SYS_CAPITAL]) . "</td>" : "<td />";
				$txt		.= (isset($dat[SYS_INTERES_NORMAL])) ? "<td class='mny'>" . getFMoney($dat[SYS_INTERES_NORMAL]) . "</td>" : "<td />";

				$txt		.= (isset($dat[SYS_GASTOS_DE_COBRANZA])) ? "<td class='mny'>" . getFMoney($dat[SYS_GASTOS_DE_COBRANZA]) . "</td>" : "";
				$txt		.= (isset($dat[SYS_IMPUESTOS])) ? "<td class='mny'>" . getFMoney($dat[SYS_IMPUESTOS]) . "</td>" : "";
				
				$txt		.= (isset($dat[SYS_AHORRO])) ? "<td class='mny'>" . getFMoney($dat[SYS_AHORRO]) . "</td>" : "";
				
				$txt		.= "<td class='mny'>" .getFMoney($dat[SYS_TOTAL]) . "</td>";
				$netoNivel	-=	$dat[SYS_TOTAL];
				$txt		.= "<th class='mny'>" .getFMoney($netoNivel) . "</th>";
				$txt		.= "<tr>";
				$tmpcap		= (isset($dat[SYS_CAPITAL])) ? $dat[SYS_CAPITAL] : 0;
				if( (setNoMenorQueCero($tmpcap) > 0) OR ($pagoactual == 0) OR ($xCred->getPagosSinCapital() == true) ){
					$trs	.= $txt;
				}
				
			}
			//<th>" . $xHP->lang("dia") . "</th>
$PlanBody .= "<table class='listado'>
  <thead>
    <tr>
      <th>" . $xHP->lang("pago") . "</th>
      
      <th>" . $xHP->lang("fecha de", "pago") . "</th>
      <th>" . $xHP->lang("capital") . "</th>
      <th>" . $xHP->lang("interes") . "</th>
      $thOtros
      $thIVA
     $thAhorro
      <th>" . $xHP->lang("total") . "</th>
      <th>" . $xHP->lang("saldo") . "</th>
    </tr>
    </thead>
    <tbody>
    $trs
	
    <tr>
      <td colspan='2'>" . $xHP->lang("sumas") . "</td>
      <th class='mny'>" . getFMoney($SUMCap) . "</th>
      <th class='mny'>" . getFMoney($SUMInt) . "</th>
      $tfOtros
      
      $tfIVA
      $tfAhorro
      <th class='mny'>" . getFMoney($SumTotal) . "</th>
      <td />
    </tr>
      	

    
    </tbody>
</table>";

$PlanBody .= "
	<table >
	<tr>
	<td><center>" . $xHP->lang("firma del", "solicitante") . "</td>
	<td><center>" . $xHP->lang("por la", "empresa") . "</center></td>
	</tr>
	<tr>
	<td>
		<br />
		<br />
		<br />
	</td>
	</tr>
	<tr>
	<td><center>$nombre</center></td>
	<td><center>$oficial</center></td>
	</tr>
	<tr>
		<th>" . $xHP->lang("observaciones") . "</th><td>$observaciones</td>
	</tr>
	</table>";

	echo $PlanBody;

echo getRawFooter();
	?>
</body>
</html>
