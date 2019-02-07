<?php
include_once("core.config.inc.php");

include_once("core.deprecated.inc.php");
include_once("entidad.datos.php");
include_once("core.common.inc.php");
include_once("core.html.inc.php");
include_once("core.fechas.inc.php");


@include_once("../libs/sql.inc.php");
class cCuentaContableEsquema {
	public $LARGO_NIVEL		= array();
	public $DIGITOS_NIVEL	= array();
	public $LARGO_TOTAL		= 0;
	
	public $CEROS			= "";
	public $NUEVES			= "";
	public $DIVISOR			= "";
	
	public $LARGO_FORMATEADO	= 0;
	
	public $CUENTA_FORMATEADA	= "";
	public $CUENTA_SUPERIOR		= "";
	public $NIVEL_ACTUAL		= 0;
	public $ESQUELETO			= array();
	public $SUPERIORES			= array();
	public $CUENTA				= "";
	public $CUENTARAW			= "";		
	public $DIGITOS_MAYOR		= "";			
	//private $mDOriginal			= array();
	private $mNiveles			= 0;
	function __construct($cuenta = false){
		$xT				= new cTipos();
		$DCONTMASQ		= explode("-", CONTABLE_CATALOGO_MASCARA_SQL);
		$ttot			= 0;
		$icnt			= 1;
		foreach ($DCONTMASQ AS $niveles){
			$tseg		= strlen($niveles);
			$ttot		+= $tseg;
			$this->DIGITOS_NIVEL[$icnt] 	= $tseg;
			$this->LARGO_NIVEL[$icnt]		= $ttot;
			$icnt++;
		}
		$this->mNiveles				= $icnt - 1; //total niveles
		$this->LARGO_FORMATEADO		= strlen(CONTABLE_CATALOGO_MASCARA_SQL);
		$this->LARGO_TOTAL			= $ttot;
		$this->DIGITOS_MAYOR		= $this->LARGO_NIVEL[CONTABLE_CUENTA_NIVEL_MAYOR];
		$this->CEROS				= str_repeat("0", $ttot);
		$this->NUEVES				= str_repeat("9", $ttot);
		$this->DIVISOR				= CDIVISOR;

		if($cuenta != false){ $this->init($cuenta); }
	}
	function init($cuenta){
		//$cuenta		= ($cuenta == false) ? $this->CUENTA
			$cuenta				= preg_replace("/[^0-9]/", "", $cuenta);
			$cuenta 			= trim($cuenta) . $this->CEROS;
			$cuenta 			= substr($cuenta, 0, $this->LARGO_TOTAL);
			//setLog("TOTAL DIGITOS ". $this->mNiveles);	
			$txt				= "";
			$raw				= "";
			for($i=0; $i< $this->mNiveles; $i++){
				$init				= ($i == 0) ? 0 : $this->LARGO_NIVEL[$i];
				$pos				= $i+1;
				$tamanno			= ($i == 0) ? $this->LARGO_NIVEL[$pos] : $this->DIGITOS_NIVEL[$pos];
				
				$this->ESQUELETO[$i]= substr($cuenta, $init, $tamanno);
				$strnivel			= $this->ESQUELETO[$i];
				$txt				.= ($txt == "") ? "$strnivel" : $this->DIVISOR . "$strnivel";
				//setLog("ESQUELETO $strnivel DE $init a $tamanno ($txt)");
				$valor_nivel		= setNoMenorQueCero($strnivel);
				if($valor_nivel > 0){
					//todavia hay valores
					if($i > 0){
						$this->SUPERIORES[$i] 	= substr($raw . $this->CEROS, 0, $this->LARGO_TOTAL);
						$this->CUENTA_SUPERIOR	= $this->SUPERIORES[$i];	
					}
					$raw					.= "$strnivel";
					
				} else {
					$this->NIVEL_ACTUAL	= $i;
					//setLog("NIVEL ACTUAL " . $i);
				}
				if($pos == $this->mNiveles AND $this->NIVEL_ACTUAL == 0){
					$this->NIVEL_ACTUAL	= $this->mNiveles;
					//setLog("NIVEL ACTUAL2 " . $this->mNiveles);
				}
				$this->CUENTA_FORMATEADA	= $txt;
				$this->CUENTARAW			= $raw;
			}
		$this->CUENTA					= $cuenta;
		return $cuenta;
	}
}

/**
 * @var $GA_ccMayor Define un CSS para el tipo de Cuentas en reportes
 */
$GA_cssMayor	= array(
						3 => "mayor",
						1 => "titulo",
						2 => "subtitulo",
						4 => "otra",
						);
function getFilas($sql="") {	return obten_filas($sql); }
/**
 * @deprecated @since 2014.08.15
 */
function cuenta_completa($cuenta){ $xCta = new cCuentaContable($cuenta); return $xCta->getCuentaCompleta($cuenta); }
/**
 * @deprecated @since 2014.08.15
 */
function cuenta_superior($cuenta){ $xEs		= new cCuentaContableEsquema($cuenta);	return $xEs->CUENTA_SUPERIOR; }
/**
 * @deprecated @since 2014.08.15
 */
function getCuentaCompleta($cuenta){ $xCta = new cCuentaContable($cuenta); return $xCta->getCuentaCompleta($cuenta); }
/**
 * @deprecated @since 2014.08.15
 */
function getCuentaFormateada($cuenta){ $xEs		= new cCuentaContableEsquema($cuenta);	return $xEs->CUENTA_FORMATEADA; }
/**
 * @deprecated @since 2014.08.15
 */
function getDigitoAgrupador($cuenta){ $xEs		= new cCuentaContableEsquema($cuenta);	return $xEs->NIVEL_ACTUAL; }
/**
 * @deprecated @since 2014.08.15
 */
function getNombreCuenta($numero){
	$cta	= "";
	$Es		= new cCuentaContableEsquema($numero);
	$numero = $Es->CUENTA;
	if(setNoMenorQueCero($numero) > 1){
		$sql_va = "SELECT numero, nombre, afectable FROM contable_catalogo WHERE numero=$numero LIMIT 0,1";
		$cta	= mifila($sql_va, "nombre");
	}
	return $cta;
}

function setAfectarSuperior($cuenta, $monto, $tipo=1, $periodo = false, $ejercicio = false ){

	$xTip	= new cTipos();

	$myper 	= $xTip->cInt($periodo);

	if ( !isset($ejercicio) OR ($ejercicio == false) ){
		$ejercicio	= EJERCICIO_CONTABLE;
	}
	if ( !isset($periodo) OR ($periodo == false) ){
		$periodo	= EACP_PER_CONTABLE;
	}
	//$cuenta = str_replace(CDIVISOR, "", $cuenta);
	$cuenta = getCuentaCompleta($cuenta);
	//$cuenta = trim($cuenta);
	$ejer 	= $ejercicio;
	$myper	= (int)$myper;

		for($i=$myper; $i<=14; $i++){
					$sql_us = "UPDATE contable_saldos SET imp$i= (imp$i + ($monto))	WHERE ejercicio=$ejer AND cuenta=$cuenta AND tipo=$tipo";
					if($i != 0){
						my_query($sql_us);
					}
		}

		//afectar los periodos
		//5 = niveles del catalogo
	for($i=0; $i <= 5; $i++){
		//Encuentra Superior
		$sql_as 		= "SELECT cuentasuperior
							FROM contable_catalogorelacion
							WHERE subcuenta=$cuenta ";
		$dsup 			= getFilas($sql_as);
		$cta_superior 	= $dsup["cuentasuperior"];
		//14 = Numero de Periodos en la tabla de Contable Saldos
		if( isset( $cta_superior ) ){
			for($i_= $myper; $i_<=14; $i_++){
			//
				$sql_us = "UPDATE contable_saldos SET imp$i_ = (imp$i_ + ($monto))
				WHERE ejercicio=$ejer
				AND cuenta=$cta_superior
				AND tipo=$tipo";
				if( isset( $cta_superior) ){
						my_query($sql_us);
				}
				//saveError(2, 99, "$sql_us - $cta_superior - $i ");
			}
			if ($i != 5){
				$cuenta = $cta_superior;
			}

		}
		//saveError(2, 99, "$sql_us - $sql_as -$cuenta - $cta_superior - $i ");
	}

	//return 0;
}
function getDatosInicialSFecha($cuenta, $naturaleza, $fecha){
	$saldo 		= 0;

	$cuenta 	= str_replace(CDIVISOR, "", $cuenta);
	$ejercicio	= date("Y", strtotime($fecha));
	$periodo	= date("n", strtotime($fecha));
	$cargos		= 0;
	$abonos		= 0;
	$saldo		= 0;
	$mvtos		= 0;
	$IFecha		= date("Y-m", strtotime($fecha)) . "-01";
	$FFecha		= date("Y-m-d", strtotime($fecha));
	$periodo	= setNoMenorQueCero(($periodo -1));
			if($periodo == 0){
				$SFld = "saldo_inicial";
			} else {
				$SFld = "imp$periodo";
			}
	$xCta		= new cCuentaContable($cuenta);
	if($xCta->init() == true){
		$OSaldo	= $xCta->OSaldosDelEjercicio($fecha);
		if($OSaldo->init() == true){
			$saldo	= $OSaldo->getSaldo($OSaldo->SNATURAL, $periodo);
		}
	}

	/**
	 * Obtiene los movimientos restantes para ajustar el saldo,
	 * si la fecha que solicita no es la Inicial del mes
	 */



		if( strtotime($FFecha) > strtotime($IFecha) ){
			$sqlGI = "SELECT
						SUM(`contable_movimientos`.`cargo`) AS 'cargos',
						SUM(`contable_movimientos`.`abono`) AS 'abonos',
						COUNT(`contable_movimientos`.`numeromovimiento`) AS 'mvtos'
						FROM
							`contable_movimientos` `contable_movimientos`
						WHERE
							(`contable_movimientos`.`fecha`>='$IFecha') AND
							(`contable_movimientos`.`fecha`<'$FFecha') AND
							(`contable_movimientos`.`numerocuenta` =$cuenta)
						GROUP BY
							`contable_movimientos`.`numerocuenta`";
			$MDats	= obten_filas($sqlGI);

			
			if(isset($MDats["cargos"])){
				$cargos	= $MDats["cargos"];
				$abonos	= $MDats["abonos"];
				$mvtos	= $MDats["mvtos"];
			} else {
			}

			if($naturaleza == NC_DEUDORA){
				//los cargos se suma
				$saldo	= ($saldo + $cargos) - $abonos;
			} else {
				$saldo	= ($saldo + $abonos) - $cargos;
			}
		}
	$arr	= array(
	"saldo"			=>$saldo,
	"movimientos"	=>$mvtos,
	"cargos"		=>$cargos,
	"abonos"		=>$abonos);
	//if(MODO_DEBUG == true){ $arr["sql"] = ""; }
	return  $arr;
}

class cCuentasPorSector {
	private $mCuenta		= false;
	private $mSumas			= array();
	private $mFecha			= false;
	
	private $mTitulos		= array();
	private $mSubtitulos	= array();
	private $mMayor			= array();
	private $mTxt			= "";
	private $mSumaTitulo	= 0;
	
	function __construct($cuenta, $fecha = false){ $this->mCuenta	= $cuenta; $this->mFecha	= $fecha; }
	function init($include_ceros = true){
		$QL		= new MQL();
		$xL		= new cSQLListas();
		$sql	= $xL->getListadoDeSaldosPorMes($this->mCuenta, $this->mFecha);
		$rs		= $QL->getDataRecord($sql);
		$titAnt	= 0;
		$subAnt	= 0;
		$titCant= 0;
		$subCant= 0;
		$titNam	= "";
		$subNam	= "";
		$xLng	= new cLang();
		$nTot	= $xLng->getT("TR.Total");
		
		foreach ($rs as $rows){
			$nivel	= $rows["nivel"];
			$monto	= $rows["monto"];
			$cuenta	= $rows["numero"];
			$nombre	= $rows["nombre"];
			$xEsq	= new cCuentaContableEsquema($cuenta);
			$txt	= "";
			$fmonto	= ($monto == 0 AND $include_ceros == false) ? "" : getFMoney($monto);
			if($cuenta > 0){
			switch ($nivel){
				case 1:
					
					//$this->mTitulos[$cuenta][SYS_DATOS]	= $rows;
					$txt	.= (($cuenta != $titAnt) AND ($titAnt > 0)) ?  "<tr><td /><th colspan='3' class='total'>$nTot $titNam</th><th class='total cont-cuenta'>" . getFMoney($titCant) . "</th></tr>" : "";
					$txt	.= "<tr><td class='cont-cuenta'>" . $xEsq->CUENTA_FORMATEADA . "</td><td colspan='3'>$nombre</td><td /></tr>";
					$titAnt	= $cuenta;
					$titCant= $monto;
					$titNam	= $nombre;
					//setLog("Asignar monto a titulo por $titCant de la cuenta $cuenta");
					$this->mSumaTitulo	= $titCant;
					break;
				case 2:
					//$this->mSubtitulos[$cuenta][SYS_DATOS]	= $rows;
					$txt	.= (($cuenta != $subAnt) AND ($subAnt > 0)) ?  "<tr><td ></td><th colspan='3' class='total'>$nTot $subNam</th><th class='total cont-cuenta'>" . getFMoney($subCant) . "</th></tr>" : "";
					$txt	.= "<tr><th class='cont-cuenta'>" . $xEsq->CUENTA_FORMATEADA . "</th><td /><td colspan='2'>$nombre</td><td /></tr>";
					$subAnt	= $cuenta;
					$subCant= $monto;
					$subNam	= $nombre;
					break;
				case 3:
					//$this->mMayor[$cuenta]		= $rows;
					//$this->mSubtitulos[$xEsq->CUENTA_SUPERIOR][$cuenta]	= $rows;
					$txt	.= ($monto == 0 AND $include_ceros == false) ? "" : "<tr><td class='cont-cuenta'>" . $xEsq->CUENTA_FORMATEADA . "</td><td /><td /><td >$nombre</td> <td class='mny cont-cuenta'>$fmonto</td></tr>";
					break;
			}
			$this->mTxt	.= $txt;
			}
		}
		$rs					= null;
		
	}
	function render(){
		return "<table>" . $this->mTxt . "</table>";
	}
	function getSumaTitulo(){ return $this->mSumaTitulo;	}
}


class SectorCuenta{
//constructor
	var $mSql;
	var $mEjercicio;
	var $mPeriodo;
	var $mCuenta;
	var $mSuma;
	var $mTitle;
	var $factor;
function __construct($ejercicio, $periodo, $cuenta){
	$this->mPeriodo = $periodo;
	$this->mEjercicio =$ejercicio;
	$this->mCuenta = $cuenta;
	$this->mSuma = 0;
	$this->factor = 1;
	$this->mTitle = $cuenta;
	$this->mSql = "SELECT
	`contable_catalogo`.`numero`,
	`contable_catalogo`.`nombre`,
	`contable_saldos`.`imp" . $this->mPeriodo . "`,
	`contable_saldos`.`ejercicio`,
	`contable_saldos`.`tipo`,
	`contable_catalogo`.`ctamayor`,
	`contable_catalogorelacion`.`cuentasuperior`,
	`contable_catalogotipos`.`naturaleza`
FROM
	`contable_catalogo` `contable_catalogo`
		INNER JOIN `contable_catalogotipos`
		`contable_catalogotipos`
		ON `contable_catalogo`.`tipo` =
		`contable_catalogotipos`.`idcontable_catalogotipos`
			INNER JOIN `contable_saldos` `contable_saldos`
			ON `contable_saldos`.`cuenta` =
			`contable_catalogo`.`numero`
				INNER JOIN `contable_catalogorelacion`
				`contable_catalogorelacion`
				ON `contable_catalogorelacion`.`subcuenta` =
				`contable_saldos`.`cuenta`
WHERE
	(`contable_saldos`.`ejercicio` =" . $this->mEjercicio .") AND
	(`contable_saldos`.`tipo` =1) AND
	(`contable_catalogo`.`ctamayor`=3) AND
	(`contable_catalogorelacion`.`cuentasuperior` =" . $this->mCuenta . ")";
}
function setFactor($factor = 1){ $this->factor = $factor; }
function getTabla($caption) {
	$table 		= "";
	$td 		= "";
	$head 		= "";
	if($caption){
		$this->mTitle = $caption;
		$head = "<tr>
				<td colspan='3' class='csubtitle'>$caption</td>
				</tr>";
	}
	$rs = mysql_query($this->mSql, cnnGeneral());
	if(!$rs){
		saveError(2,$_SESSION["SN_b80bb7740288fda1f201890375a60c8f"], "Depurar :" . mysql_error() . "|Numero: " .mysql_errno() . "|Instruccion SQL:". $this->mSql  . "|EN:" . $_SESSION["current_file"]);
	}
	while ($rw = mysql_fetch_array($rs)){
		$numero 		= $rw["numero"];
		$nombre 		= $rw["nombre"];
		$importe 		= $rw["imp" . $this->mPeriodo] * $this->factor;
			if($importe<0){
				//$importe = $importe * -1;
			}
		$importe_fmt	= getFMoney($importe);

		$td = $td . "
		<tr>
			<td class='cmayor'>$nombre</td>
			<td class='mny' width='20%'>$importe_fmt</td>
			<td width='20%'></td>
		</tr>";
		$this->mSuma 	+= $importe;
	}
	@mysql_free_result($rs);
	$suma_fmt = getFMoney($this->mSuma);
return  "<table width='100%'>
	<tbody>
	$head
	$td
		<tr>
			<td class=\"csubtitle-footer\">TOTAL " . $this->mTitle . "</td>
			<td></td>
			<th class='mny'> " . $suma_fmt . " </th>
		</tr>
	</tbody>
</table>";
}
	function getSuma(){ return  $this->mSuma; }
	function getSQL(){ return $this->mSql; }
}
function setAfectarSaldo($cuenta, $nperiodo, $nejercicio, $naturaleza, $tmvto, $monto ){
	$cuenta 		= getCuentaCompleta($cuenta);
	//distinguir pasivo y activos, naturaleza global
	if($naturaleza == NC_DEUDORA){
		if($tmvto==TM_CARGO){
			//Si es Naturaleza es Deduora y es un Cargo
			$montom = $monto;
			$montos = $monto;
			//Afectar Cargo
			$afect1 = setAfectarSuperior($cuenta, $montom, 2, $nperiodo, $nejercicio);
			//Afectar Saldos
			$afect2 = setAfectarSuperior($cuenta, $montos, 1, $nperiodo, $nejercicio);
		} else {
			//Si es Naturaleza es Deduora y es un ABONO
			$montom = $monto;
			$montos = $monto * -1;
			//Afectar abonos
			$afect1 = setAfectarSuperior($cuenta, $montom, 3, $nperiodo, $nejercicio);
			//Afectar Saldos
			$afect2 = setAfectarSuperior($cuenta, $montos, 1, $nperiodo, $nejercicio);
		}
	} elseif($naturaleza == NC_ACREEDORA) {
		if($tmvto==TM_CARGO){
			//Si es Naturaleza es ACREEDORA y es un Cargo
			$montom = $monto;
			$montos = $monto * -1;
			//Afectar Cargos
			$afect1 = setAfectarSuperior($cuenta, $montom, 2, $nperiodo, $nejercicio);
			//Afectar Saldos
			$afect2 = setAfectarSuperior($cuenta, $montos, 1, $nperiodo, $nejercicio);
		} else {
			//Si es Naturaleza es ACREEDORA y es un ABONO
			$montom = $monto;
			$montos = $monto;
			//Afectar Cargos
			$afect1 = setAfectarSuperior($cuenta, $montom, 3, $nperiodo, $nejercicio);
			//Afectar Saldos
			$afect2 = setAfectarSuperior($cuenta, $montos, 1, $nperiodo, $nejercicio);
		}
	} //End elseif
}
function setRevertirMvto($cuenta, $nperiodo, $nejercicio, $naturaleza, $tmvto, $monto ){
	$cuenta = getCuentaCompleta($cuenta);
	if($naturaleza == NC_DEUDORA){
		if($tmvto==TM_CARGO){
			//Si es Naturaleza es Deduora y es un Cargo
			$montom = $monto * -1;
			$montos = $monto * -1;
			//Afectar Cargo
			$afect1 = setAfectarSuperior($cuenta, $montom, 2, $nperiodo, $nejercicio);
			//Afectar Saldos
			$afect2 = setAfectarSuperior($cuenta, $montos, 1, $nperiodo, $nejercicio);
		} else {
			//Si es Naturaleza es Deduora y es un ABONO
			$montom = $monto * -1;
			$montos = $monto;
			//Afectar Cargo
			$afect1 = setAfectarSuperior($cuenta, $montom, 3, $nperiodo, $nejercicio);
			//Afectar Saldos
			$afect2 = setAfectarSuperior($cuenta, $montos, 1, $nperiodo, $nejercicio);
		}
	} elseif($naturaleza == NC_ACREEDORA) {
		if($tmvto==TM_CARGO){
			//Si es Naturaleza es ACREEDORA y es un Cargo
			$montom = $monto * -1;
			$montos = $monto;
			//Afectar Cargo
			$afect1 = setAfectarSuperior($cuenta, $montom, 2, $nperiodo, $nejercicio);
			//Afectar Saldos
			$afect2 = setAfectarSuperior($cuenta, $montos, 1, $nperiodo, $nejercicio);
		} else {
			//Si es Naturaleza es ACREEDORA y es un ABONO
			$montom = $monto * -1;
			$montos = $monto * -1;
			//Afectar Cargo
			$afect1 = setAfectarSuperior($cuenta, $montom, 3, $nperiodo, $nejercicio);
			//Afectar Saldos
			$afect2 = setAfectarSuperior($cuenta, $montos, 1, $nperiodo, $nejercicio);
		}
	}
}
function jsBasicContable($form){
	$xExo		= new cCuentaContableEsquema();
	$esDef 		= $xExo->CEROS;;
	$esDiv 		= $xExo->DIVISOR;
	$esLargo 	= $xExo->LARGO_TOTAL;
	return "
	<script language=\"javascript\">
	var ExoDefCta	= \"$esDef\";
	var iLargoCta	= $esLargo;
	var FeCorta		= \"" . date("Y-m-d") . "\"
function getCuentaCompleta(idNCuenta){
	var sCuenta = document.getElementById(idNCuenta).value;
	var sFind = /$esDiv/g;

		sCuenta = sCuenta.replace(sFind, \"\");
		document.getElementById(idNCuenta).value = sCuenta.substr(0, iLargoCta);

	if(sCuenta.length < iLargoCta){
		var sCuenta = sCuenta + ExoDefCta;
		document.getElementById(idNCuenta).value = sCuenta.substr(0, iLargoCta);
	} else if (sCuenta.length == iLargoCta) {
		//Cuenta Superior
	} else {
		document.getElementById(idNCuenta).value = sCuenta.substr(0, iLargoCta);
		//Cuenta Superior
	}
}
	function setFechaF_MX(theDate){
	var mDate = document.getElementById(theDate).value;
	var sDate = mDate;
	var findStr = \"/\";
	var rF = new RegExp(findStr , \"g\");
	var rF2 = /-/g
	sDate = sDate.replace(rF, \"\");
	sDate = sDate.replace(rF2, \"\");

	var intLargo = sDate.length;
	//si el formato es ddmmaa 6 caracteres Formato a dd-mm-aa

	if(intLargo==8){
		//00 00 0000
		var intDia = sDate.substr(0,2);
		var intMes = sDate.substr(2,2);
		var intAnno = sDate.substr(4,4);

		document.getElementById(theDate).value = intDia + \"-\" + intMes + \"-\" + intAnno;

	} else if(intLargo==6) {
		var intDia = sDate.substr(0,2);
		var intMes = sDate.substr(2,2);
		var intAnno = sDate.substr(4,2);
		//var tmpDate = new Date(intAnno, intMes, intDia);
		if(parseInt(intAnno) > 70){
			intAnno = \"19\" + intAnno;
		} else {
			intAnno = \"20\" + intAnno;
		}
		document.getElementById(theDate).value = intDia + \"-\" + intMes + \"-\" + intAnno;

	} else {
		alert(\"   LA FECHA NO ES VALIDA     \\n\" +
			\"CAPTURE EN EL FORMATO DDMMAAAA, \\n\" +
			\"     DD-MM-AAA O DD/MM/AAAA     \");
			document.getElementById(theDate).value = FeCorta;
	}
}
function FSendIDtoID(FromID, ToID){
	document.getElementById(ToID).value = document.getElementById(FromID).value;
}
function FStrToEval(toEval){
	eval(toEval);
}
function getCuentaFmt(id){
	var sFind = /$esDiv/g;

	var laCuenta = document.getElementById(id).value;

		laCuenta = laCuenta.replace(sFind, \"\");
		laCuenta = laCuenta + ExoDefCta
		laCuenta = laCuenta.substr(0, iLargoCta);

	var Nivel1 = laCuenta.substr(0," . DIG_N1 . ");
	var Nivel2 = laCuenta.substr(" . LARGO_N1 . "," . DIG_N2 . ");
	var Nivel3 = laCuenta.substr(" . LARGO_N2 . "," . DIG_N3 . ");
	var Nivel4 = laCuenta.substr(" . LARGO_N3 . "," . DIG_N4 . ");
	var Nivel5 = laCuenta.substr(" . LARGO_N4 . "," . DIG_N5 . ");
	var Nivel6 = laCuenta.substr(" . LARGO_N5 . "," . DIG_N6 . ");

	document.getElementById(id).value = Nivel1 + \"$esDiv\" +  Nivel2 + \"$esDiv\" +  Nivel3 + \"$esDiv\" +
										 Nivel4 + \"$esDiv\" +  Nivel5 + \"$esDiv\" +  Nivel6;
}
function getFPesos(cantidad){

	var rF = new RegExp(\",\" , \"g\");
	var rF2 = new RegExp(\"$\" , \"g\");
	var rF3 = new RegExp(\" \" , \"g\");

	var mEnteros = \"\";
	var mCentavos = \"\";
	var comMoney = \"\";

	sPesos = cantidad.replace(rF, \"\");
	sPesos = sPesos.replace(rF2, \"\");
	sPesos = sPesos.replace(rF3, \"\");
	sPesos = sPesos.replace(\"$\", \"\");

		var ripPesos = sPesos.split(\".\");
		mEnteros = ripPesos[0];
		mCentavos = ripPesos[1];
		var mCientos = \"\";
		var mMiles = \"\";
		var mCMiles = \"\";

		var mTamEnteros = mEnteros.length;
		if(mTamEnteros>0 && mTamEnteros<=3){
			mCientos = mEnteros;
		} else if (mTamEnteros>3 && mTamEnteros<=6){
			mCientos = mEnteros.substr(-3);
			mMiles = mEnteros.substr(0, (mTamEnteros - 3));

		} else if (mTamEnteros>6 && mTamEnteros<=9){
			mCientos = mEnteros.substr(-3);
			mMiles = mEnteros.substr(-6);
			mMiles = mMiles.substr(0,3);
			mCMiles = mEnteros.substr(0, (mTamEnteros - 6));
		} else if (mTamEnteros>9){
			mCientos = mEnteros;
		}

		if(mCentavos == undefined){
			mCentavos = \"00\"
		}
		if(mCientos.length==0){
			mCientos = \"0\";
		}
		if(mMiles.length>0){
			mMiles = mMiles + \",\";
		} else {
			mMiles= \"\";
		}
		if(mCMiles.length>0){
			mCMiles = mCMiles + \",\";
		} else {
			mCMiles = \"\";
		}

		comMoney = \"$ \" + mCMiles + mMiles + mCientos + \".\" + mCentavos;
	return comMoney;
}
</script>
";
}
function getInfoCatalogo($cuenta){
	$cuenta		= getCuentaCompleta($cuenta);
	$sql_s 		="SELECT * FROM contable_catalogo WHERE numero=$cuenta LIMIT 0,1";
	$rw			= obten_filas($sql_s);
	return $rw;
}
function getInfCatalogoTipo($tipo_de_cuenta){
	$info = array();
	if($tipo_de_cuenta){
		$SQLExt = "SELECT idcontable_catalogotipos, nombre_del_tipo, naturaleza, naturaleza_del_sector
    				FROM contable_catalogotipos
    				WHERE idcontable_catalogotipos='$tipo_de_cuenta'";
		$info = obten_filas($SQLExt);
	}
	return $info;
}
function Nivel6($codigo){
	$strCod = "000000" . $codigo;
	$strCod = substr($strCod, (DIG_N6 * -1));
	//Fix del ContPaq
	/**
	 * Codigo de socio ordinario 		1001
	 * codigo de Socio a obtener		10001
	 * Codigo de las Cajas Locales
	 * 			a partir de 38			00 0000
	 */

		/*if(USE_CONTPAQ == true){
			//Esqueleto ContPaq 01
			$parte1 = "00";
			$parte2	= "0000";
			$nTrat	= "";
			settype($parte1, "string");
			settype($parte2, "string");
			settype($nTrat, "string");

			$parte1 = "00";
			$parte2	= "0000";
			$nTrat	= "";
				//Interpreta si el Codigo es menor al socio 999 de la Caja Local 37
				if($codigo <= 37999){
					$nTrat	= "00" . $codigo;
					$nTrat	= substr($nTrat, -5);
					//tratar Codigo para que llene los requisitos
					$parte1 = substr($nTrat,0,2);
					$parte2 = "0" . substr($nTrat, -3);
				} else {
					$parte1 = substr($codigo,0,2);
					$parte2 = substr($codigo, -4);
				}
				$strCod	= "$parte1$parte2";
		}*/
	return $strCod;
}
function setCuentaSocio($codigo, $nombre){
	$result = array();
	$sucess = false;
	$cuenta	= getCuentaCompleta($codigo);
	$CExist = getInfoCatalogo($cuenta);
	$NExist = $CExist["nombre"];
	$PExist = $CExist["numero"];
	if(!$NExist){
	//Verificar si la cuenta existe::
		$superior		= cuenta_superior($cuenta);
		$dSuperior		= getInfoCatalogo($superior);
		$NSuperior		= $dSuperior["nombre"];
		//Si no existe la Cuenta Superior
		if(!$NSuperior){
			$sucess = false;
		} else {
			$tipo_cuenta	= $dSuperior["tipo"];
			$mayor			= 4;							//False
			$vEjercicio 	= EJERCICIO_CONTABLE;
			$centro_costo	= $dSuperior["centro_de_costo"];
			$afectable		= 1;							//SI
			$fecha_alta		= fechasys();
			$digito			= $dSuperior["digitoagrupador"];
			$digito			= $digito + 1;
	$sql_ICta = "INSERT INTO contable_catalogo(numero,
												equivalencia,
												nombre,
												tipo,
												ctamayor,
												afectable,
												centro_de_costo,
												fecha_de_alta,
												digitoagrupador)
    									VALUES($cuenta,
    									'00000000000000',
    									'$nombre',
    									'$tipo_cuenta',
    									$mayor,
    									$afectable,
    									$centro_costo,
    									'$fecha_alta',
    									$digito)";
			$squery = my_query($sql_ICta);

				if($squery["stat"]==true){
					$sucess = true;
		//Crea la Relacion Superior a Inferior
						$sql_IRelacion = "INSERT INTO contable_catalogorelacion
												(cuentasuperior, subcuenta, tiporelacion)
    											VALUES($superior, $cuenta, 1)";
						//Crea el saldo de la Cuenta por Defecto
						my_query($sql_IRelacion);
							for($i=1; $i<=3; $i++){

								$sql_ISdoCta = "INSERT INTO contable_saldos(
													cuenta, ejercicio, tipo, saldo_inicial,
													imp1, imp2, imp3, imp4, imp5, imp6, imp7,
													imp8, imp9, imp10, imp11, imp12, imp13, imp14,
													captado)
    													VALUES(
    												$cuenta, $vEjercicio, $i, 0,
    												0, 0, 0, 0, 0, 0, 0,
    												0, 0, 0, 0, 0, 0, 0,
    												'false')";
								$squery = my_query($sql_ISdoCta);
									if($squery["stat"] == false){
										$sucess = false;
									}
							}
				}
		}
	} else {
		$sucess = true;
		$cuenta = $PExist;
	}

	$result["stat"] 	= $sucess;
	$result["code"]		= $cuenta;
	return $result;
}


function getNumsCtasInferiores($cuenta){
	$cuenta = getCuentaCompleta($cuenta);
	$hijas	= 0;
	$sqlInf = "SELECT COUNT(subcuenta) AS 'hijas'
    			FROM contable_catalogorelacion
    			WHERE cuentasuperior = $cuenta";
	$hijas	= mifila($sqlInf, "hijas");
	return $hijas;
}
/**
 * clase de manejo de las Cuentas Contables
 * @package contable
 * @subpackage core
 * @version 1.0
 * @author Balam Gonzalez luis Humberto
 */
class cCuentaContable{
	protected $mCuenta				= ZERO_EXO;
	protected $mMessages			= "";
	protected $mCuentaSuperior		= false;
	protected $mArrayCuenta			= array();
	protected $mArraySuperior		= array();
	private $mOSaldos				= null;

	protected $mCuentaIniciada		= false;
	protected $mSuperiorIniciada	= false;
	
	protected $mNaturaleza			= false;
	protected $mEquivalencia		= "";
	

	protected $mNombreCuenta		= false;
	protected $mTipoDeCuenta		= false;
	protected $mEsMayor				= false;
	protected $mEsAfectable			= false;
	protected $mCentroDeCosto		= false;
	protected $mFechaDeAlta			= false;
	protected $mDigitoAgrupador		= false;
	protected $mCuentasSuperiores	= array();
	protected $mInmediatoSuperior	= false;
	protected $mNivel				= 0;
	public 	  $mRaiseError				= false;
	protected $mFactorDeCuenta		= false;
	
	protected $mEsquema				= null; 
	private $mOTipoDeCuenta			= null; 
	private $mAfectacion			= array(
			"AD" => 1, //activo deudora aumenta con cargos
			"AA" => -1,
			"PA" => -1,
			"PD" => 1,
			"RD" => 1,
			"RA" => -1,
			"OD" => 1,
			"OA" => -1,
			"CA" => -1,
			"CD" => 1
			);
	
	protected $mFallbackSaldos	= array( "imp1" => 0,
					"imp2" => 0, "imp3" => 0, "imp4" => 0,
					"imp5" => 0, "imp6" => 0, "imp7" => 0,
					"imp8" => 0, "imp9" => 0, "imp10" => 0,
					"imp11" => 0, "imp12" => 0,	"imp13" => 0,
					"imp14" => 0, "saldo_inicial" => 0);	 
	
	function __construct($cuenta){
		$this->mEsquema	= new cCuentaContableEsquema($cuenta);
		$this->mCuenta	= $this->mEsquema->CUENTA;
	}
	/**
	 * Obtiene una Cuenta completa
	 * @param integer $cuenta
	 * @return integer Cuenta Completa sin Formato
	 */
	function getCuentaCompleta($cuenta = false, $formato = false){
		$xExq		= ($this->mEsquema == null) ? new cCuentaContableEsquema($cuenta) : $this->mEsquema;
		$cuenta		= ($cuenta == false ) ? $this->mCuenta : $cuenta;
		$cuenta		= ($formato == false) ? $xExq->CUENTA : $xExq->CUENTA_FORMATEADA;
		return $cuenta;
	}
	/**
	 * Inicializa una Cuenta segun el Array Inicial o el Obtenido por una Cuenta Contable
	 * @param array $arrInit
	 * @return array Array con los Datos de la Cuenta
	 */
	function init($arrInit = false){
		$rw			= array();
		$xQl		= new cSQLListas();
		if (!is_array($arrInit)){
			$rw 	= obten_filas($xQl->getInicialDeCuentaContable($this->mCuenta));
		} else {
			$rw		= $arrInit;
		}
		if(isset($rw["ctamayor"])){
			//Valores de la Cuenta para ser usado por el sistema
			$this->mCentroDeCosto		= $rw["centro_de_costo"];
			$this->mEsAfectable			= $rw["afectable"];
			$this->mEsMayor				= $rw["ctamayor"];
			$this->mTipoDeCuenta		= $rw["tipo"];
			$this->mDigitoAgrupador		= $rw["digitoagrupador"];
			$this->mNaturaleza			= $rw["naturaleza"];
			$this->mFactorDeCuenta		= $this->mAfectacion[$rw["tipo"]];
			$this->mNombreCuenta		= $rw["nombre"];
			$this->mEquivalencia		= $rw["equivalencia"];
			$this->mNivel				= $rw["digitoagrupador"];
			$this->mInmediatoSuperior	= $this->mEsquema->CUENTA_SUPERIOR;
			//==================================================
			$this->mArrayCuenta 		= $rw;
			$this->mCuentaIniciada		= true;
		} else {
			$this->mMessages			.= "ERROR\tAl iniciar la cuenta " . $this->mCuenta . " en " . $this->mInmediatoSuperior . " \r\n";
			$this->mCuentaIniciada		= false;
		}
		return $this->mCuentaIniciada; //$this->mArrayCuenta;
	}
	function getNombre(){ return $this->mNombreCuenta; }
	function isInit(){ return $this->mCuentaIniciada; }
	/**
	 * Funcion que genera una relation entre las cuentas
	 */
	function setRelation(){
		$estado		= true;
		if($this->mNivel > CONTABLE_CUENTA_NIVEL_TITULO){
			//genera la Insercion para una Cuenta Superior
			if ( $this->mCuentaSuperior == false ){
				$this->mCuentaSuperior	= $this->getParent();
			}
			$superior	= $this->mCuentaSuperior;
			$cuenta		= $this->mCuenta;
			$xQL		= new MQL();
			$sql 		= "INSERT INTO contable_catalogorelacion (cuentasuperior, subcuenta, tiporelacion) VALUES($superior, $cuenta, 1) ";
			$x 			= $xQL->setRawQuery($sql);
			$estado		= ($x=== false) ? false : true;
			$xQL		= null;
		}
		return $estado;
	}
	function getParent($init = false){ return $this->getCuentaSuperior($init); }
	/**
	 * funcion que devuelve la Cuenta Superior de la actual cuenta,
	 * @param boolean	$inicializar		Inicializa en un Array la Cuenta Superior
	 * @return integer Retorna el Numero de Cuenta Superior Completo sin Formato
	 */
	function getCuentaSuperior($inicializar = false){
		$cuenta	= $this->mCuenta;
		$cuenta = $this->getCuentaCompleta($cuenta);
		//$cuenta	= number_format($cuenta,0, '','');
		$lm		= $this->mEsquema->LARGO_TOTAL;
		$xQl	= new cSQLListas();
		if ( strlen($cuenta) == $this->mEsquema->LARGO_TOTAL ){
			if($this->mInmediatoSuperior == false){
				if($this->mEsquema->NIVEL_ACTUAL <= CONTABLE_CUENTA_NIVEL_TITULO AND ($cuenta != CUENTA_DE_CUADRE) ){
					$cuenta				= false;
					$this->mMessages	.= "ERROR\tCuenta Superio no carga en Titulo y Cuadre : " . $cuenta  . "\n";
				} else {
					$nnivel	= setNoMenorQueCero(($this->mEsquema->NIVEL_ACTUAL - 1));
					if(isset($this->mEsquema->SUPERIORES[ $nnivel ])){
						$cuenta	= $this->mEsquema->SUPERIORES[ $nnivel ];
					} else {
						$this->mMessages	.= "ERROR\tCuenta Superior  de " . $cuenta  . " no existe al nivel $nnivel\n";
						$cuenta	= false;
					}
					
				}
			} else {
				$cuenta		= $this->mInmediatoSuperior;
			}
			//return cuenta_completa($cuenta);
			$cuenta			= setNoMenorQueCero($cuenta);
			$cuenta			= number_format($cuenta,0, '','');
			if ( $inicializar == true AND $cuenta > 0){
				$this->mMessages	.= "SUPERIOR\tLa Cuenta Superior de " . $this->mCuenta . " es $cuenta\r\n";
					$this->mArraySuperior 	= obten_filas( $xQl->getInicialDeCuentaContable($cuenta) );
					if(isset($this->mArraySuperior["tipo"])){ $this->mSuperiorIniciada	= true;	}				
			}
			//Return cuenta como superior
			return $cuenta;

		} else {
			$this->mMessages	.= "CUENTA_INCOMPLETA_O_EXCEDIDA_DE_$lm\r\n";
			$this->mRaiseError	 = true;
			return false;
		}
	}
	/**
	 * Funcion que genera un Array de los Parents al tipo de Parent + Nivel
	 * @return array		Array con Niveles superiores
	 **/
	function getParentsInArray(){ return  $this->mEsquema->SUPERIORES; }
	/**
	 * genera los saldos de la cuenta por el ejercicio solicitado
	 * @param integer $ejercicio Ejercicio a Crear los saldos
	 */
	function setSaldos($ejercicio = false, $ArrSaldos = false, $cuenta_nueva = false){
		$arrTipoSaldos	= array(
							2 => "DEUDORES",
							1 => "SALDOS",
							3 => "ACREEDORES"
							);
		$xQL			= new MQL();
		$cuenta			= $this->mCuenta;
		$ejercicio 		= ( $ejercicio == false ) ? EJERCICIO_CONTABLE : $ejercicio;
		$NoHeredar		= ( $ArrSaldos == false OR !is_array($ArrSaldos) ) ? true : false;
		$ArrSaldos		= ( $ArrSaldos == false OR !is_array($ArrSaldos) ) ? $this->mFallbackSaldos : $ArrSaldos;
		$xLogg			= new cCoreLog();
		$avisos			= "";
		$si				= ( isset($ArrSaldos["saldo_inicial"]) ) ? $ArrSaldos["saldo_inicial"] : 0;
		//periodos
		$p1		= $ArrSaldos["imp1"];
		$p2		= $ArrSaldos["imp2"];
		$p3		= $ArrSaldos["imp3"];
		$p4		= $ArrSaldos["imp4"];
		$p5		= $ArrSaldos["imp5"];
		$p6		= $ArrSaldos["imp6"];
		$p7		= $ArrSaldos["imp7"];
		$p8		= $ArrSaldos["imp8"];
		$p9		= $ArrSaldos["imp9"];
		$p10	= $ArrSaldos["imp10"];
		$p11	= $ArrSaldos["imp11"];
		$p12	= $ArrSaldos["imp12"];
		$p13	= $ArrSaldos["imp13"];
		$p14	= $ArrSaldos["imp14"];
		$idx	= setNoMenorQueCero($cuenta);
		if($idx > 0){
			if(CONTABLE_EN_MIGRACION == true OR $cuenta_nueva == true){
				foreach ($arrTipoSaldos as $tipoN => $nombre){
					$sqlFall 	= "INSERT INTO contable_saldos( cuenta, ejercicio, tipo, saldo_inicial,
					imp1, imp2, imp3, imp4, imp5, imp6, imp7, imp8, imp9, imp10, imp11, imp12, imp13, imp14, captado)
					VALUES(
					$cuenta, $ejercicio, $tipoN, $si,
					$p1, $p2, $p3, $p4, $p5, $p6, $p7,
					$p8, $p9, $p10, $p11, $p12, $p13, $p14,
					'false')";
					$rs			=  $xQL->setRawQuery($sqlFall);
					if($rs == false){
						$xLogg->add( "ERROR\t$cuenta\t$ejercicio\t$nombre\tSaldo de $nombre en Migracion -L1024\r\n", $xLogg->DEVELOPER);
					} else {
						$xLogg->add( "OK\t$cuenta\t$ejercicio\t$nombre\tSaldo de $nombre en Migracion -L1026\r\n", $xLogg->DEVELOPER);
					}
				}
			} else {
				if( $NoHeredar	== false ){		
					$xLogg->add("WARN\tCREANDO SALDOS DE LA CUENTA $cuenta AL EJERCICIO $ejercicio \r\n", $xLogg->DEVELOPER);
					//crear saldos por tipo, saldos cargos y abonos
					for($i=1; $i<=3; $i++){
						$tipo			= $i;
						$sql_ISdoCta 	= "INSERT INTO contable_saldos( cuenta, ejercicio, tipo, saldo_inicial,
										imp1, imp2, imp3, imp4, imp5, imp6, imp7,imp8, imp9, imp10, imp11, imp12, imp13, imp14, captado)
		    							VALUES(
		    							$cuenta, $ejercicio, $tipo, $si,
		    							$p1, $p2, $p3, $p4, $p5, $p6, $p7,
		    							$p8, $p9, $p10, $p11, $p12, $p13, $p14,
		    							'false')";
						$rs			=  $xQL->setRawQuery($sql_ISdoCta);
						if($rs === false){
							$xLogg->add("ERROR\t$cuenta\tAl crear los saldos del Tipo ". $arrTipoSaldos[$i] . " del Ejercicio $ejercicio [L1047]\r\n", $xLogg->DEVELOPER);
						} else {
							$xLogg->add("OK\t$cuenta\tSe Crearon los saldos del Tipo ". $arrTipoSaldos[$i] . " del Ejercicio $ejercicio [L1049]\r\n", $xLogg->DEVELOPER);
						}
					}
				} else {
					
					$ejercicio 		= ( isset($ArrSaldos["ejercicio"]) ) ? $ArrSaldos["ejercicio"] : EJERCICIO_CONTABLE;
					$tipo 			= ( isset($ArrSaldos["tipo"]) ) ? $ArrSaldos["tipo"] : false;
					$tipo			= setNoMenorQueCero($tipo);
					$sql_ISdoCta 	= "INSERT INTO contable_saldos( cuenta, ejercicio, tipo, saldo_inicial,
										imp1, imp2, imp3, imp4, imp5, imp6, imp7, imp8, imp9, imp10, imp11, imp12, imp13, imp14, captado)
		    							VALUES(
		    							$cuenta, $ejercicio, $tipo, $si,
		    							$p1, $p2, $p3, $p4, $p5, $p6, $p7,
		    							$p8, $p9, $p10, $p11, $p12, $p13, $p14,
		    							'false')";
		    		if( $tipo > 0 ){
						$rs			=  $xQL->setRawQuery($sql_ISdoCta);
		    			if($rs == false){
							$xLogg->add("ERROR\t$cuenta\tSe Crearon los saldos del Tipo ". $arrTipoSaldos[$i] . " del Ejercicio $ejercicio [L1067]\r\n", $xLogg->DEVELOPER);
						} else {
							$xLogg->add("OK\t$cuenta\tSe Crearon los saldos del Tipo ". $arrTipoSaldos[$i] . " del Ejercicio $ejercicio [L1069]\r\n", $xLogg->DEVELOPER);
						}
		    		} else {
		    			$xLogg->add("OK\t$cuenta\t$ejercicio\tWARN\tSe OMITIERON los Saldos, Saldo Inicial: $si  [L1072] \r\n", $xLogg->DEVELOPER);
		    		}		
				}
			}
		} else {
			$xLogg->add("OK\t$cuenta\t$ejercicio\tERROR\tCUENTA INVALIDA [L1072] \r\n", $xLogg->DEVELOPER);
		}
		return $xLogg->getMessages();
	}
	/**
	 * Genera los Saldos segun una Cuenta Superior, Copiando todo los datos de la cuenta Suerior, Generalmente usados en altas de Cuentas nuevas
	 * @param int $cuenta_superior		//Numero de Cuenta Superior
	 */
	function setHeredarSaldos($cuenta_superior = false){
		$cuenta				= $this->mCuenta;
		$cuenta_superior	= ( $cuenta_superior == false ) ? $this->getParent(true) : $cuenta_superior;
		$aviso				= "";
		$xLogg				= new cCoreLog();
		$xQL					= new MQL();
		if( setNoMenorQueCero($cuenta_superior) > 0 ){
			$xLogg->add("WARN\tGENERANDO SALDOS DE LA CUENTA $cuenta HEREDADOS DE LA CUENTA $cuenta_superior\r\n", $xLogg->DEVELOPER);
				
			$sql_SeSup 			= "SELECT * FROM contable_saldos WHERE cuenta=$cuenta_superior";
			$rs_SS 				= $xQL->getDataRecord($sql_SeSup);
			$saldo_importados	= 0;
			foreach ($rs_SS as $rwSS){
				$Sejercicio 	= setNoMenorQueCero($rwSS["ejercicio"]);
				$xLogg->add($this->setSaldos($Sejercicio, $rwSS), $xLogg->DEVELOPER);
				$saldo_importados++;
			}
			if($saldo_importados == 0){
				$xLogg->add("ERROR\tNo se Heredaron Saldos\r\n", $xLogg->DEVELOPER);
				$xLogg->add($this->setSaldos(), $xLogg->DEVELOPER);				//generar nuevos
			}
			if(isset($this->mArraySuperior["afectable"])){
				if ( $this->mArraySuperior["afectable"] == 1 AND $this->mDigitoAgrupador != CONTABLE_CUENTA_NIVEL_TITULO){
					$xLogg->add("OK\tAFECTABLE\tLa cuenta SUPERIOR $cuenta_superior es Afectable\r\n", $xLogg->DEVELOPER);
					//Actualizar la Cuenta Superior NO_AFECTABLE
					$sql_USup 			= "UPDATE contable_catalogo SET afectable=0 WHERE numero=$cuenta_superior ";
					$xQL->setRawQuery($sql_USup);
				}
			}
			$xLogg->add("WARN\tNATURALEZA\tSe Actualizo la Naturaleza de la Cuenta Superior\r\n", $xLogg->DEVELOPER);
			//Actualizar los Movimientos a la Cuenta Nueva
			$sql_UMvtos = "UPDATE contable_movimientos set numerocuenta=$cuenta WHERE numerocuenta=$cuenta_superior ";
			$xQL->setRawQuery($sql_UMvtos);
			$xLogg->add( "OK\tMOVIMIENTOS\tSe Traspasaron los Movimientos de la Cuenta $cuenta_superior a la Cuenta $cuenta\r\n", $xLogg->DEVELOPER);
		} else {
			$xLogg->add("WARN\tSolo se generan los saldos por la cuenta\r\n", $xLogg->DEVELOPER);
			$xLogg->add( $this->setSaldos(), $xLogg->DEVELOPER);
		}
		//generar Saldos nuevos heredados
		return $xLogg->getMessages();
	}
	function add($nombre, $tipo=false, $centro_de_costo = false, $es_mayor = false,$digito_agrupador = false,$fecha_de_alta = false, $equivalencia = false, $superior = false ){
		$xLog				= new cCoreLog();
		$cuenta				= setNoMenorQueCero($this->mCuenta);
		$xEsq				= new cCuentaContableEsquema($cuenta);
		$xF					= new cFecha();
		$ready				= ($cuenta <= 0) ? false : true;
		$heredar			= false;
		$afectable			= 1;
		$xLog->add( "WARN\tAlta a la Cuenta $cuenta de Nombre $nombre \r\n");
		//si equivalencia es igual a null 
		$equivalencia		= ($equivalencia == false) ? ZERO_EXO : $equivalencia;
		//si la Fecha no Existe, llevarla a Default.
		$fecha_de_alta		= $xF->getFechaISO($fecha_de_alta);
		//Si el Digito Agrupador No Existe, Buscarlo
		$digito_agrupador	= setNoMenorQueCero($digito_agrupador);
		$digito_agrupador	= ($digito_agrupador <= 0) ? $xEsq->NIVEL_ACTUAL : $digito_agrupador;
		$superior			= $xEsq->CUENTA_SUPERIOR;
		//busca la cuenta superior, si esta no existe, obtener una nueva
		if( $digito_agrupador > CONTABLE_CUENTA_NIVEL_TITULO OR $cuenta == CUENTA_DE_CUADRE){
			$this->setSuperior($superior); 
			$this->getParent(true); //$this->mSuperiorIniciada		= 
			$InfoSuperior					= $this->mArraySuperior;
			$ready							= $this->mSuperiorIniciada; //false
			if($ready == false){
				$xLog->add( "WARN\tError al Iniciar la Cuenta Superior $superior de la cuenta $nombre nivel $digito_agrupador\r\n");
			} else {
				$xLog->add( "OK\tCuenta Superior $superior INICIADA\r\n", $xLog->DEVELOPER);
				//contar cuantos relaciones tiene
				//si las relaciones on mas de uno, entonces no es afectable
				if($InfoSuperior["afectable"] == SYS_UNO){
					$xLog->add( "OK\tLa Cuenta Superior $superior Es afectable\r\n", $xLog->DEVELOPER);
					$heredar				= true;
				}
				$tipo						= ($tipo == false) ? $InfoSuperior["tipo"] : $tipo;
			}
		}
		
		$es_mayor			= setNoMenorQueCero($es_mayor);
		$centro_de_costo	= setNoMenorQueCero( $centro_de_costo);

		if($digito_agrupador == CONTABLE_CUENTA_NIVEL_TITULO){
			if( $tipo == false){
				$ready			= false;
				$xLog->add( "WARN\tEl Tipo de la cuenta $cuenta no puede quedar vacio\r\n");
			}
			$digito_agrupador	= 1;
			$afectable			= 1;
			$es_mayor			= CONTABLE_CUENTA_NIVEL_TITULO;
		} else {
			if($digito_agrupador > 3){
				$es_mayor		= 4;
			} else {
				$es_mayor		= $digito_agrupador;
			}
		}
		/**
		 * Si la Cuenta Superior no Existe, y no es Nivel 1.- Titulo
		 */
		if($ready == true){
			$ql		= new MQL();
			$sql 	= "INSERT INTO contable_catalogo(numero, equivalencia, nombre, tipo, ctamayor,	afectable, centro_de_costo, fecha_de_alta,	digitoagrupador)
			VALUES($cuenta,	'$equivalencia', '$nombre', '$tipo', $es_mayor,	$afectable,
			$centro_de_costo, '$fecha_de_alta',	$digito_agrupador)";
			$exec	= $ql->setRawQuery($sql);
			
			$ql		= null;
			
			if($exec == false){
				$xLog->add( "ERROR\t2.- Error al Agregar la cuenta $cuenta - $superior\r\n");
				$cuenta					= CUENTA_DE_CUADRE;				
			} else {
				$xLog->add("OK\tSe Guardo la Cuenta $cuenta de forma Satisfactoria \r\n");
				$this->mCuentaSuperior	= $superior;
				$this->mDigitoAgrupador	= $digito_agrupador;
				$this->mCuenta			= $cuenta;
				//Iniciar
				$this->init();
				$this->setRelation();
				
				if($heredar == true){
					$msg				= $this->setHeredarSaldos($superior);
					$xLog->add($msg, $xLog->DEVELOPER);
				} else {
					//Solo generar saldos
					$msg 				= $this->setSaldos($xF->anno(), false, true);
					$xLog->add($msg, $xLog->DEVELOPER);
				}
			}
		} else {
			$xLog->add( "ERROR\tError al Agregar la cuenta $cuenta - $superior\r\n");
			$cuenta					= CUENTA_DE_CUADRE;
			
		}
		$this->mMessages		.= $xLog->getMessages();
		return $cuenta;
	}
	/**
	 * Funcion que Obtiene un Digito segun el Nivel de Cuenta
	 * @param int $cuenta 	Numero de Cuenta a Obtener, Opcional
	 * @return integer		Numero de Nivel de la Cuenta
	 */
	function getDigitoAgrupador($cuenta = false){ $cuenta	= ($cuenta == false ) ? $this->mCuenta : $cuenta; $xEs	= new cCuentaContableEsquema($cuenta);	return $xEs->NIVEL_ACTUAL;	}
	function getCentroDeCosto(){ return $this->mCentroDeCosto; }
	function getEquivalencia(){ return $this->mEquivalencia; }
	function getFechaDeAlta(){ return $this->mFechaDeAlta; }
	function getTipoDeCuenta(){ return $this->mTipoDeCuenta; }
	/**
	 * funcion que verifica si la cuenta Existe
	 * @param boolean $crear_si_no_existe		 	Crea la Cuenta si no existe.
	 * @return boolean								True si Existe, False si no
	 */
	function getCountCuenta($crear_si_no_existe = false ){
		$existe	= false;
		$xLi	= new cSQLListas();
		$xQL	= new MQL();
		$datos	= $xQL->getDataRow(	$xLi->getInicialDeCuentaContable($this->mCuenta) );
		$existe	= (isset($datos["numero"])) ? true : false;
		$this->mMessages	.= ($existe == true) ? "OK\tEXISTS.- La cuenta " . $this->mCuenta . " Existe \r\n" : "ERROR\tNOEXISTS.- La cuenta " . $this->mCuenta . " No existe\r\n";
		if($existe == true){
			
		}
		return $existe;
	}
	function getMessages($put = OUT_TXT){ 	$xH		= new cHObject();	return $xH->Out($this->mMessages, $put);	}
	function get(){	return $this->getCuentaCompleta( $this->mCuenta ); }
	/**
	 * Afecta el Saldo de la Cuenta Actual a solicitud
	 * @param integer $tmvto		Tipo de Movimiento CARGO/ABONO
	 * @param float $monto			Monto del Movimiento
	 * @param integer $periodo		Periodo del Movimiento
	 * @param integer $ejercicio	Ejercicio del Movimiento
	 */
	function setAfectarSaldos($tipo_operacion, $monto, $periodo = false, $ejercicio = false, $revertir = false){
		$msg		= "";
		$ejercicio	= ($ejercicio == false) ? EJERCICIO_CONTABLE : $ejercicio;
		$periodo	= ($periodo == false) ? EACP_PER_CONTABLE : $periodo;
		$cuenta 	= $this->mCuenta;
		$xLogg		= new cCoreLog();
		$supers		= $this->mEsquema->SUPERIORES;
		$QL			= new MQL();
		if ( $this->mCuentaIniciada == false ){
			$this->init();
			$xLogg->add("OK\tSe carga la cuenta Nuevamente\r\n", $xLogg->DEVELOPER);
		}
		
		if($this->isInit() == true){
			$naturaleza	= $this->mNaturaleza;
			$supers		= $this->mEsquema->SUPERIORES;
			$strSups	= "";
			foreach ($supers as $idxNiv => $idxCta){
				$strSups	.=  ($strSups == "") ?  "`numero`=$cuenta OR `numero`=$idxCta " : " OR `numero`=$idxCta ";
			}
			$rsSupers		= $QL->getDataRecord("SELECT * FROM `contable_catalogo` WHERE `numero` > 0 AND ($strSups)");
			foreach ($rsSupers as $rows){
				$factor			= $this->mAfectacion[$rows["tipo"]];
				$ctasuperior	= $rows["numero"];
				$sdo			= ($monto*($factor*$tipo_operacion));
				
				$xLogg->add("OK\tLa cuenta $ctasuperior con factor $factor\r\n", $xLogg->DEVELOPER);
				
				
				
				$this->setAfectar( $sdo, CUENTA_SALDO, $periodo, $ejercicio, $ctasuperior, $revertir);
				if($tipo_operacion == TM_CARGO){
					$this->setAfectar($monto, CUENTA_SALDO_DEUDOR, $periodo, $ejercicio, $ctasuperior, $revertir);
				} else {
					$this->setAfectar($monto, CUENTA_SALDO_ACREEDOR, $periodo, $ejercicio, $ctasuperior, $revertir);
				}
			}	
			
		} else {
			$xLogg->add("ERROR\tError al Iniciar la cuenta\r\n", $xLogg->DEVELOPER);
		}
		$xLogg->add($msg, $xLogg->DEVELOPER); $msg = null;
		$this->mMessages	.= $xLogg->getMessages();
		
		return $xLogg->getMessages();
	}	
	function getDatos(){ if ( $this->mCuentaIniciada == false){ $this->init();	} return $this->mArrayCuenta; }
	
	private function setAfectar($monto, $tipo = CUENTA_SALDO, $periodo = false, $ejercicio = false, $cuenta = false, $revertir = false){
		$ejercicio	= (setNoMenorQueCero($ejercicio) <= 0) ? EJERCICIO_CONTABLE : $ejercicio;
		$periodo	= (setNoMenorQueCero($periodo) <= 0) ? EACP_PER_CONTABLE : $periodo;
		$cuenta 	= (setNoMenorQueCero($cuenta) <= 0  ) ? $this->mCuenta : $cuenta;
		$xLog		= new cCoreLog();
		$QL			= new MQL();
		$simbol		= "+";
		$monto		= ($revertir == true) ? $monto * -1 : $monto;
		$ready		= true;
		$exAfect	= "";
		
		for($i = $periodo; $i <= 14; $i++){ 
			$exAfect	.= ($exAfect == "") ?  " imp$i=(imp$i $simbol ($monto)) " : ", imp$i=(imp$i $simbol ($monto))";	
		}
		$okYear		= $QL->setRawQuery("UPDATE contable_saldos SET $exAfect WHERE ejercicio=$ejercicio AND `cuenta`=$cuenta AND tipo=$tipo");
		//Annios futuros
		$exAfect	= "";
		for($i = 1; $i <= 14; $i++){ 
			$exAfect	.= ($exAfect == "") ?  " imp$i=(imp$i $simbol ($monto)) " : ", imp$i=(imp$i $simbol ($monto))";	
		}
		$proxYear	= $QL->setRawQuery("UPDATE contable_saldos SET $exAfect WHERE ejercicio>$ejercicio AND `cuenta`=$cuenta AND tipo=$tipo");
		
		if($okYear == false OR $proxYear == false){
			$xLog->add("ERROR\tAl afectar cuentas de la Cuenta $cuenta en el periodo $periodo/$ejercicio por $monto ($simbol)\r\n", $xLog->DEVELOPER);
			$ready	= false;
		} else {
			$xLog->add("OK\tAfectar cuentas de la Cuenta $cuenta en el periodo $periodo/$ejercicio por $monto ($simbol)\r\n", $xLog->DEVELOPER);
		}
		
		$this->mMessages	.= $xLog->getMessages();
		return "";
	}

	function getMayor(){ return substr($this->get(), 0, $this->mEsquema->DIGITOS_MAYOR);	}
	function getEsCuentaDeCirculante(){
		//$xT		= new cTipos();
		$result				= false;
		$CuentaCirculante	= substr(CUENTA_CONTABLE_EFECTIVO, 0, $this->mEsquema->DIGITOS_MAYOR);
		$CuentaComparada	= $this->getMayor();
		$this->mMessages	.= "WARN\tMAYOR es $CuentaCirculante VS $CuentaComparada\r\n";
		$result				= ( $CuentaCirculante == $CuentaComparada ) ? true : false;
		
		return $result;
	}
	function getFicha($extendido = false, $operaciones = false){
		/*
		 *					<option value="3">Si</option>
					<option value="4" selected>No</option>
					<option value="1">Titulo</option>
					<option value="2">SubTitulo</option>
		 */
		$arrTipos		= array(3 => "MAYOR", 4 => "NINGUNO", 1 => "TITULO", 2 => "SUBTITULO" );
		$cuenta			= $this->getCuentaCompleta();
		$superior		= $this->getParent(true);
		$DCuenta		= $this->getDatos();
		$ql				= new MQL();
		$xLis			= new cSQLListas();
		$xF				= new cFecha();
		
		$DSuperior		= $this->mArraySuperior;
		
		$superiorN		= $DSuperior["nombre"];
		$cuentaN		= $DCuenta["nombre"];

		$tipo			= $DCuenta["nombre_del_tipo"];
		$mayor			= $arrTipos[ $DCuenta["ctamayor"] ];
		$afectable		= ( $DCuenta["afectable"] == 1) ? "AFECTABLE" : "NO_AFECTABLE";
		
		
		$marco			= true;
		$tool			= "";
		$hsaldos		= "";
		$hoperaciones	= "";
		if($extendido == true){
			$sql			= $xLis->getListadoDeSaldosContablesConTitulos($cuenta);
			$xTable			= new cTabla($sql);
			$hsaldos		.= $xTable->Show("TR.Saldos");
		}
		if($operaciones  == true){
			$sql		= $xLis->getListadoDeOperacionesContables($cuenta, $xF->mes(), $xF->anno());

			$xTable			= new cTabla($sql);
			$xTable->setFootSum(array(
				7 => "cargo",
				8 => "abono"
			));
			$hoperaciones	.= $xTable->Show("TR.Operaciones");			
		}
		//cuenta nombre
		//tipo afectable
		//cuenta superior
		$exoFicha 		=  "
			<table>
			<tbody>
				<tr>
					<td>Numero de Cuenta</td>
					<th>$cuenta</th>
					<td>Nombre</td>
					<th>$cuentaN</th>
				</tr>
				<tr>
					<td>Cuenta Superior</td>
					<th>$superior</th>
					<td>Nombre</td>
					<th>$superiorN</th>				
				</tr>
				<tr>
					<td>Tipo de Cuenta</td>
					<th>$tipo</th>
					<td>Caracteristicas</td>
					<th>$mayor / $afectable</th>				
				</tr>
				$tool
			</tbody>
			</table>$hsaldos $hoperaciones";
		//if ($marco == true){
					$exoFicha = "<fieldset>
						<legend>&nbsp;&nbsp;INFORMACI&Oacute;N DE LA CUENTA&nbsp;&nbsp;</legend>
							$exoFicha
					</fieldset>";
		//}
		return $exoFicha;		
	}
	function determineNivel($cuenta = false){
		$cuenta		= ($cuenta == false) ? $this->mCuenta : $cuenta;
		$niveles	= explode(CDIVISOR, $cuenta);
		$nivel		= 0;
		foreach ($niveles as $key => $cta){
			if( intval($cta) <= 0 ){
				break;
			} else {
				$mkr	= isset($niveles[$key-1]) ? $niveles[$key-1] : "";
				$this->mInmediatoSuperior .= $mkr;
				$nivel++;
			}
		}
		return $nivel;
	}
	function getInmediatoSuperior(){ return $this->mInmediatoSuperior; }
	function setSuperior($superior = false){  $this->mInmediatoSuperior = $superior; }
	function getNombreCuenta(){ return $this->mNombreCuenta; }
	function OTipoDeCuenta(){
		if($this->mOTipoDeCuenta == null){ 
			$this->mOTipoDeCuenta = new cContable_catalogotipos();
			$this->mOTipoDeCuenta->setData( $this->mOTipoDeCuenta->query()->initByID($this->mTipoDeCuenta) );
		}
		return $this->mOTipoDeCuenta;
	}

	function setActualizar($nombre = "", $equivalencia = "", $centro_de_costo = false){
		$centro_de_costo 	= setNoMenorQueCero($centro_de_costo);
		$centro_de_costo	= ($centro_de_costo <= 0) ? $this->mCentroDeCosto : $centro_de_costo;
		$nombre				= ($nombre == "") ? $this->mNombreCuenta : $nombre;
		$equivalencia		= ($equivalencia == "") ? $this->mEquivalencia : $equivalencia;
		$sql				= "UPDATE `contable_catalogo` SET `equivalencia`='$equivalencia', `nombre`='$nombre', `centro_de_costo`=$centro_de_costo WHERE `numero` =" . $this->mCuenta;
		$ql					= new MQL(); $ql->setRawQuery($sql);
	}
	function OSaldosDelEjercicio($fecha = false){
		if($this->mOSaldos == null){ $this->mOSaldos	= new cContableSaldo($this->mCuenta, $fecha); }
		return  $this->mOSaldos;
	}
}
/**
 * Efectua las operaciones sobre las Polizas Contables de SAFE
 * @version 1.0.02
 * @package contable
 * @subpackage core
 */
class cPoliza{
	private $mCodigoDePoliza		= false;
	private $mTipoDePoliza			= false;
	private $mNumeroDePoliza		= false;
	private $mDiarioDePoliza		= false;
	private $mEjercicioPoliza		= false;
	private $mPeriodoPoliza			= false;
	private $mFechaPoliza			= false;
	private $mCentroDeCosto			= false;

	private $mPolizaIniciada		= false;
	private $mArrDatosPoliza		= false;
	private $mCodigoUnico			= false;
	private $mOCentroCosto			= null;
	private $mOTipoPoliza			= null;
	
	protected $mMessages			= "";
	protected $mNumeroMvto			= 0;
	protected $mConceptoPoliza		= "";
	protected $mTotalCargos			= 0;
	protected $mTotalAbonos			= 0;
	protected $mDiv					= ".";
	protected $mReestructurarEfvo	= false;

	public $mRaiseError				= false;
	/**
	 * Constructor
	 * @param integer $tipo			Tipo de Poliza
	 * @param integer $numero		[Numero de Poliza]
	 * @param integer $ejercicio	[Ejercicio de la Poliza]
	 * @param integer $periodo		[Periodo de la Poliza]
	 */
	function __construct($tipo, $numero = false, $ejercicio = false, $periodo = false ){
		$ejercicio				= ($ejercicio	== false) ? EJERCICIO_CONTABLE: $ejercicio;
		$periodo 				= ($periodo == false) ? EACP_PER_CONTABLE : $periodo;

		$this->mPeriodoPoliza	= $periodo;
		$this->mEjercicioPoliza	= $ejercicio;
		$this->mTipoDePoliza	= $tipo;
		$this->mNumeroDePoliza	= $numero;
		$this->mCodigoDePoliza	= $this->mEjercicioPoliza . $this->mDiv . $this->mPeriodoPoliza . $this->mDiv . $this->mTipoDePoliza . $this->mDiv . $this->mNumeroDePoliza;
	}
	function init($arrHeredar = false){
		
		if (is_array($arrHeredar)){
			$this->mArrDatosPoliza 	= $arrHeredar;
			$this->mEjercicioPoliza	= $arrHeredar["ejercicio"];
			$this->mPeriodoPoliza	= $arrHeredar["periodo"];
			$this->mTipoDePoliza	= $arrHeredar["tipopoliza"];
			$this->mNumeroDePoliza	= $arrHeredar["numeropoliza"];
		} else {
			$sqle = "SELECT * FROM contable_polizas
						WHERE ejercicio=" . $this->mEjercicioPoliza . "
						AND periodo=" . $this->mPeriodoPoliza . "
						AND tipopoliza=" . $this->mTipoDePoliza . "
						AND numeropoliza=" . $this->mNumeroDePoliza . " LIMIT 0,1";
			$this->mArrDatosPoliza	= obten_filas($sqle);
		}
		
		$this->mFechaPoliza		= $this->mArrDatosPoliza["fecha"];
		$this->mConceptoPoliza	= $this->mArrDatosPoliza["concepto"];
		$this->mDiarioDePoliza	= $this->mArrDatosPoliza["diario"];
		$this->mCentroDeCosto	= $this->mArrDatosPoliza["diario"];
		$this->mCodigoUnico		= $this->mArrDatosPoliza["codigo_unico"];
		$this->mCodigoDePoliza	= $this->mEjercicioPoliza . $this->mDiv . $this->mPeriodoPoliza  . $this->mDiv .  $this->mTipoDePoliza  . $this->mDiv . $this->mNumeroDePoliza;
		$this->mPolizaIniciada	= true;

		return $this->mPolizaIniciada;
	}
	function get(){ 	return $this->mNumeroDePoliza;	}
	/**
	 * Agrega una nueva Poliza Contable
	 * @param string 	$Concepto		Concepto de la Poliza		
	 * @param variant 		$FechaDePoliza	[Fecha de la Poliza]
	 * @param integer 	$NumeroDePoliza	[Numero de Poliza]
	 * @param float 	$Totalcargos	[Total de Cargos]
	 * @param float 	$TotalAbonos	[Total de Abonos]
	 */
	function add($Concepto = "",$FechaDePoliza = false, $NumeroDePoliza = false, $TotalCargos = 0, $TotalAbonos = 0, $usuario = false, $centro_de_costo = 999, $recibo = false){
		$msg					= "";
		
		$ejercicio				= $this->mEjercicioPoliza;
		$periodo				= $this->mPeriodoPoliza;
		$tipo					= $this->mTipoDePoliza;
		$recibo					= setNoMenorQueCero($recibo);
		$FechaDePoliza			= ( $FechaDePoliza == false ) ? fechasys() : $FechaDePoliza;
		$NumeroDePoliza			= ( $NumeroDePoliza == false ) ? $this->getUltimoFolio(true) : $NumeroDePoliza;
		
		$usuario				= ( $usuario == false ) ? getUsuarioActual() : $usuario;
		
		$this->mNumeroDePoliza	= $NumeroDePoliza;
		
		$clase					= 1;
		$impresa				= 'false';
		$DiarioDePoliza 		= $centro_de_costo;
		unset($_SESSION[POLIZA_ID_ULTIMAOPERACION]);	
			$sqlnpol = "INSERT INTO contable_polizas(ejercicio, periodo, tipopoliza,
			numeropoliza, clase, impresa, concepto, fecha, cargos, abonos, diario, idusuario, recibo_relacionado)
		    VALUES ($ejercicio, $periodo, $tipo, $NumeroDePoliza, $clase, '$impresa', '$Concepto',
			'$FechaDePoliza', $TotalCargos,	$TotalAbonos, $DiarioDePoliza, $usuario, $recibo)";
			$xQL				= new MQL();
			$xR	= $xQL->setRawQuery($sqlnpol);
			$xR	= ($xR === false) ? false : true;
		
		if ( $xR == true ){
			$msg	.= "OK\tPOLIZA\tPoliza Agregada con Num: $NumeroDePoliza de Fecha $FechaDePoliza y Tipo $tipo \r\n";
		} else {
			$msg	.= "ERROR\tError al Guardar Poliza con Num: $NumeroDePoliza de Fecha $FechaDePoliza y Tipo $tipo \r\n";
			$this->mRaiseError	= true;
		}
		$this->mMessages		.= $msg;
		$this->mConceptoPoliza	= $Concepto;
		$this->mFechaPoliza		= $FechaDePoliza;
		$this->mNumeroDePoliza	= $NumeroDePoliza;
		$this->mDiarioDePoliza	= $DiarioDePoliza;
		$this->mCentroDeCosto	= $DiarioDePoliza;
		$this->mCodigoDePoliza	= $this->mEjercicioPoliza . $this->mDiv . $this->mPeriodoPoliza  . $this->mDiv .  $this->mTipoDePoliza  . $this->mDiv . $this->mNumeroDePoliza;
		return $msg;
	}
	/**
	 * Retorna un Numero de Polizas Existentes segun Numero dado
	 * Los parametros de Ejercicio, Periodo y tipo lo copia de la Actual Poliza
	 *
	 * @param integer $numero	Numero de Poliza a Buscar
	 * @return integer			Numero de Polizas Buscadas con ese Numero
	 */
	function getCountPolizaByNumero($numero = false){
		$numero	= ( $numero == false ) ? $this->mNumeroDePoliza : $numero;
		$sqle = "SELECT COUNT(numeropoliza) AS 'existentes' FROM contable_polizas
						WHERE ejercicio=" . $this->mEjercicioPoliza . "
						AND periodo=" . $this->mPeriodoPoliza . "
						AND tipopoliza=" . $this->mTipoDePoliza . "
						AND numeropoliza=$numero";
						$hay = mifila($sqle, "existentes");
		return $hay;
	}
	function getFolioDeMvto(){
		$folio		= 1;
		if(isset($_SESSION[POLIZA_ID_ULTIMAOPERACION])){
			$folio 	= intval($_SESSION[POLIZA_ID_ULTIMAOPERACION]);
		} else {
			$sql	= "SELECT MAX(`numeromovimiento`) + 1 AS 'operaciones' FROM `contable_movimientos` WHERE
				(`contable_movimientos`.`ejercicio` =" . $this->mEjercicioPoliza . ") AND
				(`contable_movimientos`.`periodo` =" . $this->mPeriodoPoliza . ") AND
				(`contable_movimientos`.`tipopoliza` =" . $this->mTipoDePoliza . ") AND
				(`contable_movimientos`.`numeropoliza` =" . $this->mNumeroDePoliza . ") ";
			if(MODO_DEBUG == true){ /*setLog($sql);*/ }
			$folio 	= mifila($sql, "operaciones");
			$_SESSION[POLIZA_ID_ULTIMAOPERACION]	= $folio;
		}
		$folio	= setNoMenorQueCero($folio);
		$folio	= ($folio <= 0) ? 1 : $folio;
		return  $folio;
	}
	function setUpdatePoliza($arrFields = false){
		if($this->mPolizaIniciada == false){ $this->init(); }
		/**
		 * 	ejercicio=$idejercicio, periodo=$idperiodo,
			tipopoliza=$idtipopol, numeropoliza=$idpoliza,
			clase=1, impresa='false',
			concepto='$idconceptopol',
			fecha='$idfechapol'
		 */
		$sucess			= false;
		$arrFieldsNoUp	= array("ejercicio",
								"periodo",
								);
		$periodo		= $this->mPeriodoPoliza;
		$ejercicio		= $this->mEjercicioPoliza;
		$updateFecha	= false;
		$updateTipo		= false;
		$updateNumero	= false;
		$BodyUpdate		= "";
		$BodyUpdate		= "";
		$BodyMvtos		= "";
		$msg			= "";
		$xQL			= new MQL();

		$numero			= $this->mNumeroDePoliza;
		$tipo			= $this->mTipoDePoliza;
		$fecha			= $this->mFechaPoliza;

		if ( $arrFields != false AND is_array($arrFields) ){

			//Buscar la Poliza y la Fecha
			foreach ($arrFields as $key=>$value){
				$sucess			= true;
				//Determinar el Periodo y el Ejercicio segun la Fecha
				if ($key == "fecha" AND ($fecha != $this->mFechaPoliza) ){
					$periodo	= date("n", strtotime($value));
					$ejercicio	= date("Y", strtotime($value));
					//Si la Fecha Corresponde a otro periodo o Ejercicio
					if ( ($ejercicio != EJERCICIO_CONTABLE) OR ($periodo != EACP_PER_CONTABLE) ){
						$this->mRaiseError	= true;
						$msg				.= "No es Posible Actualizar la Poliza al Periodo $periodo del Ejercicio $ejercicio\r\n";
						$sucess				= false;
					} else {
						$updateFecha		= true;
						$fecha				= $value;
					}

				} else {
					$msg					.= "La Fecha de Poliza($fecha) no Sufre Cambios\r\n";
				}
				//Determinar si se actualiza el Numero de Poliza
				if ($key == "numeropoliza" AND ($value != $this->mNumeroDePoliza) ){
					$exist	= $this->getCountPolizaByNumero($value);
					//Buscar si ya Existe la Poliza
					if ($exist != 0){
						$numero	= $this->getUltimoFolio(true);
						$this->mRaiseError	=  true;
						$msg				.= "No es Posible Usar el Numero de Poliza $value porque existe, se Actualiza a $numero\r\n";
						$updateNumero		= true;
					} else {
						$numero	= $value;
					}
					$updateNumero			= true;
				} else {
					$msg					.= "El Numero de Poliza($numero) no Sufre Cambios\r\n";
				}
				//determinar si se actualiza el tipo de Poliza
				if ($key == "tipopoliza" AND ($value != $this->mTipoDePoliza) ){
						$tipo		= $value;
						$updateTipo	= true;
				} else {
					$msg					.= "El Tipo de Poliza($tipo) no Sufre Cambios\r\n";
				}
				//Agregar el Numero de Campos
				if ( is_string($value) ){
					$value	= "\"" . $value . "\"";
				}
				if ($BodyUpdate == ""){
					$BodyUpdate .= "$key = $value ";
				} else {
					$BodyUpdate .= ", $key = $value ";
				}
			}	//end foreach
			if ($sucess	== true){
				$sql = "UPDATE contable_polizas
					    SET $BodyUpdate
					    WHERE ejercicio=$ejercicio
						AND periodo=$periodo
						AND tipopoliza=$tipo
						AND numeropoliza=$numero";
				$x 		= $xQL->setRawQuery($sql);
				
				$sucess	= ($x === false ) ? false : true;
			}
			//Actualizar los Movimientos
			if ($updateFecha == true){
				if($BodyMvtos == ""){
					$BodyMvtos	.= " fecha='$fecha' ";
				} else {
					$BodyMvtos	.= ", fecha='$fecha' ";
				}
			}
			if ($updateNumero	== true ){
				if($BodyMvtos == ""){
					$BodyMvtos	.= " numeropoliza=$numero ";
				} else {
					$BodyMvtos	.= ", numeropoliza=$numero ";
				}
			}
			if ($updateTipo	== true ){
				if($BodyMvtos == ""){
					$BodyMvtos	.= " tipopoliza=$tipo ";
				} else {
					$BodyMvtos	.= ", tipopoliza=$tipo ";
				}
			}
			if ($sucess == true){
				$sqlM = "UPDATE contable_movimientos
						    SET $BodyMvtos
						    WHERE ejercicio=$ejercicio
							AND periodo=$periodo
						    AND tipopoliza=" . $this->mTipoDePoliza . "
						    AND numeropoliza= " . $this->mNumeroDePoliza . " ";
			}
			//Actualiza el Tipo y Numero
			if ( $sucess == true ){
				$this->mTipoDePoliza	= $tipo;
				$this->mFechaPoliza		= $fecha;
				$this->mNumeroDePoliza	= $numero;
			}
		} else {
			$this->mRaiseError	= true;
			$msg				.= "ERROR\tLA ACTUALIZACION NO ES VALIDA\r\n";

		}
		$this->mMessages		.= $msg;
		return $msg;
	}
	function setReestructurarEfvo(){	$this->mReestructurarEfvo	= true;	}
	function setTotalAbonos($monto){ $this->mTotalAbonos = $monto; }
	function setTotalCargos($monto){ $this->mTotalCargos = $monto;}
	function setFinalizar($ForzarSumas = false){
		//Forzar Sumas S/N
		$ajuste				= 0;
		$msg				= "";
		$xT					= new cTipos();
		$this->mTotalCargos	= $xT->cFloat($this->mTotalCargos, 2);
		$this->mTotalAbonos	= $xT->cFloat($this->mTotalAbonos, 2);

		if( setNoMenorQueCero($this->mTotalAbonos + $this->mTotalAbonos) <= 0 ){
			$msg	.= "ELIMINAR\tPOLIZA SIN MOVIMIENTOS\r\n";
			$this->setDeletePoliza();
		} else {
			if ( $this->mTotalCargos > $this->mTotalAbonos ){
				$ajuste		= $this->mTotalCargos - $this->mTotalAbonos;
				$this->addMovimiento(CUENTA_DE_CUADRE, 0, $ajuste, "Ajuste $ajuste");
				$msg		.= "CUADRE\tABONO\tSe Ajustan los Abonos por $ajuste\r\n";
			} elseif ( $this->mTotalCargos < $this->mTotalAbonos ){
				$ajuste		= $this->mTotalAbonos - $this->mTotalCargos;
				$this->addMovimiento(CUENTA_DE_CUADRE, $ajuste, 0, "Ajuste $ajuste");
				$msg		.= "CUADRE\tCARGO\tSe ajustan los Cargos por $ajuste\r\n";
			}
			$totalCargos	= $this->mTotalCargos;
			$totalAbonos	= $this->mTotalAbonos;
			if ( $this->mReestructurarEfvo == true ){
					//reestructurar la poliza
					//obtener el listado de cambio de afectacion por suma
					//agregar una cuenta global por sum
					//eliminar las otras cuentas
					//$arrEfvo;
					//FIXME: Componer
					$xCEfvo		= new cCuentaContable(CUENTA_CONTABLE_EFECTIVO);
					$ExtraM		= $xCEfvo->getMayor();
					$sqlRF		= "SELECT
										`contable_movimientos`.`numerocuenta`   AS `NCuenta`,
										`contable_movimientos`.`tipomovimiento` AS `TMvto`,
										SUM(`contable_movimientos`.`importe`)   AS `TImporte`,
										MAX(`contable_movimientos`.`fecha`)     AS `FechaM`,
										SUM(`contable_movimientos`.`cargo`)   AS `cargos`,
										SUM(`contable_movimientos`.`abono`)   AS `abonos`
									FROM
										`contable_movimientos` `contable_movimientos` 
									WHERE
										(`contable_movimientos`.`ejercicio` = " . $this->mEjercicioPoliza . ") 
										AND
										(`contable_movimientos`.`periodo` = " . $this->mPeriodoPoliza . ") 
										AND
										(`contable_movimientos`.`tipopoliza` =" . $this->mTipoDePoliza .") 
										AND
										(`contable_movimientos`.`numeropoliza` =" . $this->mNumeroDePoliza . ")
										AND
										(`contable_movimientos`.`numerocuenta` LIKE \"$ExtraM%\")
									GROUP BY
										`contable_movimientos`.`numerocuenta`,
										`contable_movimientos`.`tipomovimiento` ";
					//$msg .= "$sqlRF\r\n";
					$rsNV		= mysql_query($sqlRF, cnnGeneral() );
					while ( $r1	= mysql_fetch_array($rsNV)){
						$mCuenta	= $r1["NCuenta"];
						$mTMvto		= $r1["TMvto"];
						$mTMonto	= $r1["TImporte"];
						$mFecha		= $r1["FechaM"];
						$mCargos	= $r1["cargos"];
						$mAbonos	= $r1["abonos"];
						
						$sqlDM		= "DELETE FROM `contable_movimientos` WHERE
										(`contable_movimientos`.`ejercicio` = " . $this->mEjercicioPoliza . ") 
										AND
										(`contable_movimientos`.`periodo` = " . $this->mPeriodoPoliza . ") 
										AND
										(`contable_movimientos`.`tipopoliza` =" . $this->mTipoDePoliza .") 
										AND
										(`contable_movimientos`.`numeropoliza` =" . $this->mNumeroDePoliza . ")
										AND
										(`contable_movimientos`.`numerocuenta` = $mCuenta ) AND `contable_movimientos`.`tipomovimiento` = $mTMvto ";
						my_query($sqlDM);
						//$msg 	.= "$sqlDM\r\n";
						$msg	.= $this->addMovimiento($mCuenta, $mCargos, $mAbonos, "", "RST");
					}
			}
			
			$arrUp		= array (
							"cargos" => $totalCargos,
							"abonos" => $totalAbonos
							);
			$msg		.= $this->setUpdatePoliza($arrUp);
		}
		$this->mMessages	.= $msg;
		return $msg;
	}
	function setDeletePoliza(){
		$idpoliza		= $this->mNumeroDePoliza;
		$idperiodo		= $this->mPeriodoPoliza;
		$idtipopol		= $this->mTipoDePoliza;
		$idejercicio	= $this->mEjercicioPoliza;
		$QL				= new MQL();
		$xLog			= new cCoreLog();
		//Eliminar Poliza
		
		
		//Eliminar Movimientos
		$sqlSM = "SELECT * FROM contable_movimientos
		WHERE ejercicio=$idejercicio AND periodo=$idperiodo
		 AND numeropoliza=$idpoliza AND tipopoliza=$idtipopol";
		
		$rs = $QL->getDataRecord($sqlSM);
		
		foreach ($rs as $rw) {
			$idoperacion	= $rw["clave_unica"];
			$xOp			= new cContableOperacion($idoperacion);
			$xOp->init($rw);
			$xOp->setEliminar();
			$xLog->add($xOp->getMessages(), $xLog->DEVELOPER);
		}
		$SQLDP = "DELETE FROM contable_polizas WHERE  ejercicio=$idejercicio	AND periodo=$idperiodo	AND tipopoliza=$idtipopol	AND numeropoliza=$idpoliza ";
		$QL->setRawQuery($SQLDP);
		$this->mMessages	.= $xLog->getMessages();
		return $xLog->getMessages();
	}
	/**
	 * @deprecated @since 2018.08.01
	 */
	function setPorCodigo($CodigoDePoliza, $delimitador = "."){ return $this->initByCodigo($CodigoDePoliza, $delimitador); }
	function initByCodigo($CodigoDePoliza, $delimitador = "."){
		//ejercicio . periodo . tipo . numero
		$d		= explode($delimitador, $CodigoDePoliza);
		$this->mEjercicioPoliza	= setNoMenorQueCero($d[0]);
		$this->mPeriodoPoliza	= setNoMenorQueCero($d[1]);
		$this->mTipoDePoliza	= setNoMenorQueCero($d[2]);
		$this->mNumeroDePoliza	= setNoMenorQueCero($d[3]);
		$this->mCodigoDePoliza	= $CodigoDePoliza;
		unset($_SESSION[ POLIZA_ID_ULTIMAOPERACION ]);
		$this->init();
		return $this->mPolizaIniciada;
	}
	function initById($id){
		$id	= setNoMenorQueCero($id);
		if($id > 0){
			$sql	= "SELECT * FROM contable_polizas WHERE `codigo_unico`=$id LIMIT 0,1";
			$ql		= new MQL();
			$Data	= $ql->getDataRow($sql);
			if(isset($Data["codigo_unico"])){
				unset($_SESSION[ POLIZA_ID_ULTIMAOPERACION ]);
				$this->init($Data);
				$this->mMessages	.= "OK\tLa Poliza Existe con el ID Unico " . $this->mCodigoUnico . "\r\n";
			}
		}
		return $this->mPolizaIniciada;
	}
	/**
	 * @deprecated @since 2018.08.01
	 */
	function setPorRecibo($Recibo){ return $this->initByRecibo($Recibo); }
	function initByRecibo($Recibo){
		$Recibo	= setNoMenorQueCero($Recibo);
		if($Recibo > 0){
			$sql	= "SELECT * FROM contable_polizas WHERE `recibo_relacionado`=$Recibo LIMIT 0,1";
			$ql		= new MQL();
			$Data	= $ql->getDataRow($sql);
			if(isset($Data["codigo_unico"])){
				unset($_SESSION[ POLIZA_ID_ULTIMAOPERACION ]);
				$this->init($Data);
				$this->mMessages	.= "OK\tLa Poliza Existe con el ID Unico " . $this->mCodigoUnico . "\r\n";
			}
		}
		return $this->mPolizaIniciada;
	}
	//FIXME: Verificar Cumplimiento
	/**
	 * Agrega un Movimiento a la Poliza Contable
	 * @param integer $cuenta
	 * @param float $cargo
	 * @param float $abono
	 * @param string $referencia
	 * @param string $concepto
	 * @param integer $NumMvto
	 * @param variant $Fecha
	 */
	function addMovimiento($cuenta, $cargo = 0, $abono = 0, $referencia = "", $concepto = "", $NumMvto = false, $Fecha = false){
		//if($this->mPolizaIniciada == false){ $this->init(); }
		$xT			= new cTipos();
		$xLogg		= new cCoreLog();
		$xQL		= new MQL();
		$msg		= "";
		
		$ejercicio 	= $this->mEjercicioPoliza;
		$periodo 	= $this->mPeriodoPoliza;
		$poliza 	= $this->mNumeroDePoliza;
		$tipo 		= $this->mTipoDePoliza;
		$NumMvto			= setNoMenorQueCero($NumMvto);
		$this->mNumeroMvto	= setNoMenorQueCero($this->mNumeroMvto);
		 //$this->getFolioDeMvto();
		if( $NumMvto <= 1 ){ $NumMvto 	= ( $this->mNumeroMvto > 1  ) ? ($this->mNumeroMvto + 1) : ($this->getFolioDeMvto() + 1); 	}
		$cargo		= $xT->cFloat($cargo, 2);
		$abono		= $xT->cFloat($abono, 2);
		$xLogg->add( "WARN\t----------------------------------------------\r\n", $xLogg->DEVELOPER);
		//Valores de los proximos Movimientos
	
		$TipoMvto	= TM_CARGO;
		$monto 		= 0;
		$Fecha 		= ( $Fecha == false) ? $this->mFechaPoliza : $Fecha ;
		$concepto	= ( $concepto == "" ) ? $this->mConceptoPoliza : $concepto;
		$referencia	= trim($referencia);
		
		$diario 	= ($this->mCentroDeCosto == false) ? DEFAULT_CONTABLE_DIARIO_MVTOS : $this->mCentroDeCosto;
		$xCCont		= new cCuentaContable($cuenta);
		$xCCont->init();
		$cuenta 	= $xCCont->get();

		if ($cargo > 0){
			$TipoMvto		= TM_CARGO;
			$monto 			= $cargo;
			$abono			= 0;
			$this->mTotalCargos	+= $monto;
	
		} else {
			$TipoMvto		= TM_ABONO;
			$monto 			= $abono;
			$cargo			= 0;
			$this->mTotalAbonos	+= $monto;
		}
		//-------------------------------------------------------------------------------------------------------
		if ( $monto != 0 ){
			
			//-------------------------------------------------------------------------------------------------------
			$sqli_mvto = "INSERT INTO contable_movimientos(ejercicio, periodo, tipopoliza, numeropoliza, numeromovimiento,
						numerocuenta, tipomovimiento, referencia, importe, diario, moneda, concepto, fecha, cargo, abono)
						    VALUES($ejercicio, $periodo,
						    $tipo, $poliza,
						    $NumMvto, $cuenta,
						    '$TipoMvto', '$referencia',
						    $monto, $diario, 1,
						    '$concepto', '$Fecha',
						    $cargo, $abono)";
			$rs 		= $xQL->setRawQuery($sqli_mvto);
			$rs			= ($rs === false) ? false : true;
			//setLog($sqli_mvto);
			if($rs == true){
				$xLogg->add( "$NumMvto\t$cuenta\t$TipoMvto\t$cargo\t$abono\t$referencia\r\n", $xLogg->DEVELOPER);
				$xCCont->setAfectarSaldos($TipoMvto, $monto, $periodo, $ejercicio, false);
				//Establecer Numeracion
				$this->mNumeroMvto		= $NumMvto;
				$_SESSION[POLIZA_ID_ULTIMAOPERACION ]	= $NumMvto;
			} else {
				$xLogg->add("ERROR\t$NumMvto\tAl intentar insertar registro\r\n" , $xLogg->DEVELOPER);
			}
		} else {
			$xLogg->add("ERROR\t$NumMvto\tNo existe un saldo que agregar\r\n" , $xLogg->DEVELOPER);
		}
		$xLogg->add($xCCont->getMessages() , $xLogg->DEVELOPER);
		$xLogg->add($msg , $xLogg->DEVELOPER);
		$this->mMessages		.= $xLogg->getMessages();
		return $xLogg->getMessages();
	}


	function setUpdateMovimiento($keyMvto, $cuenta, $cargo, $abono, $referencia, $concepto, $diario){
		$dMvto 			= explode(".", $keyMvto);
		$ejercicio 		= $dMvto[1];
		$periodo 		= $dMvto[2];
		$poliza  		= $dMvto[3];
		$tipopoliza 	= $dMvto[4];
		$mvto 			= $dMvto[5];
		$cuenta 		= getCuentaCompleta($cuenta);
		//purgar Mvto
		if($cargo>0){
			$abono = 0;
		}
		if ($cargo>0){
			$tmvto = 1;
			$monto = $cargo;
		} else {
			$tmvto = -1;
			$monto = $abono;
		}
		
		$sqlDatosMvtoAnterior = "SELECT `contable_movimientos`.* FROM `contable_movimientos` `contable_movimientos`		WHERE
		`contable_movimientos`.`ejercicio` = $ejercicio
		AND `contable_movimientos`.`periodo` = $periodo
		AND `contable_movimientos`.`tipopoliza` = $tipopoliza
		AND `contable_movimientos`.`numeropoliza` = $poliza
		AND `contable_movimientos`.`numeromovimiento` =$mvto";
		$DMAnterior 	= getFilas($sqlDatosMvtoAnterior);
		$AntCuenta 		= $DMAnterior["numerocuenta"];
		$AntTMvto 		= $DMAnterior["tipomovimiento"];
		$AntMonto 		= $DMAnterior["importe"];
		$AntFecha 		= $DMAnterior["fecha"];
		//----------------------- Revertir Afectacion ------------------
		$sqldcta = "SELECT
		`contable_catalogo`.`numero`,
		(`contable_catalogotipos`.`naturaleza` * `contable_catalogotipos`.`naturaleza_del_sector`) AS 'factor'
		FROM
		`contable_catalogotipos` `contable_catalogotipos`
		INNER JOIN `contable_catalogo` `contable_catalogo`
		ON `contable_catalogotipos`.
		`idcontable_catalogotipos` = `contable_catalogo`.
		`tipo`
		WHERE `contable_catalogo`.`numero`=$AntCuenta";
		$dcuenta 			= getFilas($sqldcta);
		$AntNaturaleza 		= $dcuenta["factor"];
		setRevertirMvto($AntCuenta, $periodo, $ejercicio, $AntNaturaleza, $AntTMvto, $AntMonto);
		//----------------------- Eliminar Cuenta ----------------------
		$sqlDelMvtoAnterior = "DELETE FROM `contable_movimientos` WHERE
		`contable_movimientos`.`ejercicio` = $ejercicio
		AND `contable_movimientos`.`periodo` = $periodo
		AND `contable_movimientos`.`tipopoliza` = $tipopoliza
		AND `contable_movimientos`.`numeropoliza` = $poliza
		AND `contable_movimientos`.`numeromovimiento` =$mvto";
		my_query($sqlDelMvtoAnterior);
		//----------------------- Insertar Cuenta ----------------------
		$NSqlcta = "SELECT
		`contable_catalogo`.`numero`,
		(`contable_catalogotipos`.`naturaleza` * `contable_catalogotipos`.`naturaleza_del_sector`) AS 'factor'
		FROM
		`contable_catalogotipos` `contable_catalogotipos`
		INNER JOIN `contable_catalogo` `contable_catalogo`
		ON `contable_catalogotipos`.
		`idcontable_catalogotipos` = `contable_catalogo`.
		`tipo`
		WHERE `contable_catalogo`.`numero`=$cuenta";
		$dNcuenta = getFilas($NSqlcta);
		$naturaleza = $dNcuenta["factor"];
		
		$sqli_mvto = "INSERT INTO contable_movimientos(ejercicio, periodo, tipopoliza, numeropoliza, numeromovimiento,
		numerocuenta, tipomovimiento, referencia, importe, diario, moneda, concepto, fecha, cargo, abono)
		VALUES($ejercicio, $periodo,
		$tipopoliza, $poliza,
		$mvto, $cuenta,
		'$tmvto', '$referencia',
		$monto, $diario, 1,
		'$concepto', '$AntFecha',
		$cargo, $abono)";
		my_query($sqli_mvto);
		setAfectarSaldo($cuenta, $periodo, $ejercicio, $naturaleza, $tmvto, $monto);
	}

	function getUltimoFolio($salvar = true){
		//$ejercicio, $periodo, $tipo
		$ejercicio	= $this->mEjercicioPoliza;
		$periodo	= $this->mPeriodoPoliza;
		$tipo		= $this->mTipoDePoliza;

		$numero 	= 1;
		$SQL = "SELECT (MAX(numero) + 1) AS 'id'
				FROM general_folios_poliza
				WHERE ejercicio=$ejercicio
				AND periodo=$periodo
				AND tipo=$tipo";
		//setLog($SQL);
		$numero = mifila($SQL, "id");
		//TODO: Agregar evaluacion de tipos
		if( !isset($numero) OR ( is_null($numero) ) OR ( $numero == "NULL") OR ( $numero <= 0) ){
			$numero = 1;
		}
		return $numero;
	}
	/**
	 * Mensajes de la Libreria
	 * @param string $put Formato de Salida
	 * @return string	Mesajes de Texto
	 */
	function getMessages($put = OUT_TXT){	$xH		= new cHObject();	return $xH->Out($this->mMessages, $put);	}
	function getDatos(){ return $this->mArrDatosPoliza; }
	function getFicha($fielset = true){
		$xL		= new cLang();
		$xF		= new cFecha();
		$xTipo	= $this->OTipoPoliza()->nombre_del_diario()->v();
		$title	= "<legend>|&nbsp;&nbsp;" . $xL->get("Poliza") . "# " . $this->mCodigoUnico . "&nbsp;&nbsp;|</legend>";
		$table	= "<table>
					<tbody>
						<tr>
						<th>" . $xL->get("fecha") . "</th><td>" . $xF->getFechaCorta($this->mFechaPoliza) . "</td>
						<th>" . $xL->get("tipo") . "</th><td>" . $xTipo. "</td>
						<th>" . $xL->get("numero") . "</th><td>" . $this->mNumeroDePoliza . "</td>
						<th>" . $xL->get("concepto") . "</th><td>" . $this->mConceptoPoliza . "</td>
						</tr>
					</tbody>
				</table>";
		return ($fielset == true) ? "<fieldset>$title $table</fieldset>" : $table;
	}
	function OTipoPoliza(){
		if($this->mOTipoPoliza == null){
			$this->mOTipoPoliza = new cContable_polizasdiarios();
			$this->mOTipoPoliza->setData( $this->mOTipoPoliza->query()->initByID($this->mTipoDePoliza) );
		}
		return $this->mOTipoPoliza;
	}
	function OTipoCentroCosto(){
		$this->mOCentroCosto	= new cContable_centrodecostos();
		$this->mOCentroCosto->setData( $this->mOCentroCosto->query()->initByID($this->mCentroDeCosto) );
		return $this->mOCentroCosto;
	}
	function getListadoDeMovimientos($complementoTD = ""){
		$xL	= new cLang();
		$xQL	= new cSQLListas();
		$sql	= $xQL->getListadoDeMovimientosContables($this->mCodigoDePoliza);
		$ql		= new MQL();
		$td		= "";
		$tcargos	= 0;
		$tabonos	= 0;
		$cnt		= 1;

		$th	= "<tr>
		    <th class='movimiento'>#</th>
		    <th class='cuenta'>" . $xL->getT("TR.Cuenta") . "</th>
		    <th class='nombrecuenta'>" . $xL->getT("TR.Nombre") . "</th>
		    <th class='cargos'>" . $xL->getT("TR.Cargos") . "</th>
		    <th class='abonos'>" . $xL->getT("TR.Abonos") . "</th>
		    <th class='referencia'>" . $xL->getT("TR.Referencia") . "</th>
		    <th class='concepto'>" . $xL->getT("TR.Concepto") . "</th>
  		</tr>";
		$rs		= $ql->getDataRecord($sql);
		foreach ($rs as $rows){
			$operacion	= $rows["operacion"];
			$nid		= $this->mCodigoDePoliza . $this->mDiv . $operacion;
			$cuenta		= $rows["cuenta"];
			
			$pid		= str_replace(".", "_", $nid);
			$cnt		= ($cnt >= 2) ? 1 : ($cnt+1);
			$cls		= ($cnt == 2) ? " class='trOdd' " : "";
			$td			.= "<tr$cls id='$pid'><td>" . $rows["operacion"] .  "</td>";
			$td			.= "<td>" . $cuenta .  "</td>";
			$td			.= "<td  class='nombrecuenta' onclick='var xC = new ContGen(); xC.goToPanel($cuenta)'>" . $rows["nombre"] .  "</td>";
			if( $rows["tipo"] == TM_CARGO ){
				$tcargos	+= $rows["importe"];
				$td			.= "<td onclick='jsEditarMvto(\"$nid\")' class='mny'>" . getFMoney($rows["importe"]) .  "</td><td />";
			} else {
				$tabonos	+= $rows["importe"];
				$td			.= "<td /><td onclick='jsEditarMvto(\"$nid\")' class='mny'>" . getFMoney($rows["importe"]) .  "</td>";
			}
			$td			.= "<td>" . $rows["referencia"] .  "</td>";
			$td			.= "<td>" . $rows["concepto"] .  "</td></tr>";
		}
		$tf	= "<tr>
		    <td />
		    <td />
		    <th>" . $xL->getT("TR.Sumas") . "</th>
		    <th><input type='text' disabled value='" . getFMoney($tcargos) . "' id='idsumacargos' class='mny' /></th>
		    <th><input type='text' disabled value='" . getFMoney($tabonos) . "' id='idsumaabonos' class='mny' /></th>
		    <td />
		    <td />
  		</tr>";		
		return "<table id='movimientocontables'><thead>$th $complementoTD </thead><tbody>$td</tbody><tfoot>$tf</tfoot></table>";
	}
	function getEjercicio(){ return $this->mEjercicioPoliza; }
	function getPeriodo(){ return $this->mPeriodoPoliza; }
	function getNumero(){ return $this->mNumeroDePoliza; }
	function getTipo(){ return $this->mTipoDePoliza; }
	function getCodigo(){ return $this->mCodigoDePoliza; }
	function getCodigoCompuesto(){ return $this->mCodigoDePoliza; }
	function getCodigoUnico(){ return $this->mCodigoUnico; }
	function getCodigoId(){ return $this->mCodigoUnico; }
	
	function getConcepto(){ return $this->mConceptoPoliza; }
	function getFecha(){ return $this->mFechaPoliza; }
	/**
	 * Generar una Prepoliza de perfil
	 * @param integer 	$recibo		Numero de Recibo
	 * @param integer 	$tipo_mvto	Tipo de Operacion
	 * @param float 	$monto		Monto de la Operacion
	 * @param integer	$socio		Numero de Socio
	 * @param integer 	$docto		Numero de documento
	 * @param integer 	$operacion	Tipo de Operacion Contable CARGO/ABONO
	 * @param integer 	$usuario	Usuario de la Operacion
	 */
	function addProforma($recibo, $tipo_mvto, $monto, $socio, $docto, $operacion = 1, $usuario = false, $banco = false, $altervativo = false, $fecha =false){
		if(MODULO_CONTABILIDAD_ACTIVADO == true){
			$xF				= new cFecha();
			$fecha			= $xF->getFechaISO($fecha);
			$usuario		= setNoMenorQueCero($usuario);
			$altervativo	= setNoMenorQueCero($altervativo);
			$usuario		= ( $usuario <= 0 ) ? getUsuarioActual() : $usuario ;
			$sucursal		= getSucursal();
			$banco			= setNoMenorQueCero($banco);
			if($monto != 0){
				$sqlI 		= "INSERT INTO contable_polizas_proforma
				(numero_de_recibo, tipo_de_mvto, monto, socio, documento, contable_operacion, idusuario, sucursal, banco, cuenta_alternativa, fecha)
				VALUES($recibo, $tipo_mvto, $monto, $socio, $docto, '$operacion', $usuario, '$sucursal', $banco, $altervativo, '$fecha')";
				$xQL		= new MQL();
				$xQL->setRawQuery($sqlI);
			}
		}
		return "OK\tPROFORMA\t$socio\t$docto\t$recibo\t$tipo_mvto\t$monto\t$altervativo\r\n";
	}	
}
class cContableOperacion {
	private $mCodigoDeMvto			= false;
	private $mTipoDePoliza			= false;
	private $mNumeroDePoliza		= false;
	private $mNumeroDeMvto			= false;

	private $mEjercicioPoliza		= false;
	private $mPeriodoPoliza			= false;
	private $mNumeroCuenta			= false;
	private $mCentroDeCosto			= false;
	private $mCodigoUnico			= false;
	private $mTipoOperacion			= false;
	private $mMontoOperacion		= 0;
	private $mObj					= null;
	private $mDataInArray			= array();
	private $mInit					= false;
	private $mCargo					= 0;
	private $mAbono					= 0;
	protected $mMessages			= "";
	
	protected $mDiv					= ".";
	protected $mCodigoDePoliza		= "";
	
	function __construct($CodigoUnico = false){
		$this->mCodigoUnico	= setNoMenorQueCero($CodigoUnico);
	}
	function setPorCodigo($CodigoDeOperacion, $delimitador = "."){
		//FIXME: terminar cuando sea necesario
		//ejercicio . periodo . tipo . numero
		$d		= explode($delimitador, $CodigoDeOperacion);
		$this->mEjercicioPoliza	= setNoMenorQueCero($d[0]);
		$this->mPeriodoPoliza	= setNoMenorQueCero($d[1]);
		$this->mTipoDePoliza	= setNoMenorQueCero($d[2]);
		$this->mNumeroDePoliza	= setNoMenorQueCero($d[3]);
		$this->mNumeroDeMvto	= setNoMenorQueCero($d[4]);
		$this->mCodigoDeMvto	= $CodigoDeOperacion;
		$QL						= new MQL();
		//unset($_SESSION[ POLIZA_ID_ULTIMAOPERACION ]);
		$sql	= "SELECT *	FROM `contable_movimientos`
		WHERE
		(`contable_movimientos`.`ejercicio` =" . $this->mEjercicioPoliza . ") AND
		(`contable_movimientos`.`periodo` =" . $this->mPeriodoPoliza . ") AND
		(`contable_movimientos`.`tipopoliza` =" . $this->mTipoDePoliza . ") AND
		(`contable_movimientos`.`numeropoliza` =" . $this->mNumeroDePoliza . ") AND
		(`contable_movimientos`.`numeromovimiento` =" . $this->mNumeroDeMvto . ") LIMIT 0,1";		
		$Data		= $QL->getDataRow($sql);
		$this->init($Data);
	}
	function init($arr = false){
		$xTabla	= new cContable_movimientos();
		if(!is_array($arr)){
			if($this->mCodigoUnico > 0){
				$arr			= $xTabla->query()->initByID($this->mCodigoUnico);
				$this->mInit	= true;
			}
		} else {
			$this->mInit	= true;
		}
		$this->mDataInArray		= $arr;
		$xTabla->setData($arr);
		$this->mEjercicioPoliza	= $xTabla->ejercicio()->v();
		$this->mPeriodoPoliza	= $xTabla->periodo()->v();
		$this->mNumeroDePoliza	= $xTabla->numeropoliza()->v();
		$this->mTipoDePoliza	= $xTabla->tipopoliza()->v();
		$this->mNumeroDeMvto	= $xTabla->numeromovimiento()->v();
		$this->mNumeroCuenta	= $xTabla->numerocuenta()->v();
		$this->mCodigoUnico		= $xTabla->clave_unica()->v();
		$this->mTipoOperacion	= $xTabla->tipomovimiento()->v(); //CARGO ABONO
		$this->mMontoOperacion	= $xTabla->importe()->v();
		$this->mCargo			= $xTabla->cargo()->v();
		$this->mAbono			= $xTabla->abono()->v();
		$this->mObj				= $xTabla;
	}
	function getObj(){ return $this->mObj;	}
	function getEjercicio(){ return $this->mEjercicioPoliza; }
	function getPeriodo(){ return $this->mPeriodoPoliza; }
	//function getNumeroDePoliza(){ return $this->mNumeroDePoliza; }
	//function getTipoDePoliza(){ return $this->mTipoDePoliza; }
	function getCodigoDePoliza(){ return $this->mEjercicioPoliza . $this->mDiv . $this->mPeriodoPoliza . $this->mDiv . $this->mTipoDePoliza . $this->mDiv . $this->mNumeroDePoliza; }
	function getCodigoUnico(){ return $this->mCodigoDeMvto; }
	function setEliminar(){
		$xCta				= new cCuentaContable($this->mNumeroCuenta);
		$xCta->init();

		$xCta->setAfectarSaldos($this->mTipoOperacion, $this->mMontoOperacion,$this->mPeriodoPoliza, $this->mEjercicioPoliza, true);
		$sqlDMM = "DELETE FROM contable_movimientos	WHERE clave_unica=" . $this->mCodigoUnico;
		my_query($sqlDMM);
		
		$this->mMessages	.= $xCta->getMessages();
	}
	function setEditar($cuenta, $cargo = false, $abono = false, $referencia = false, $concepto = false, $diario = DEFAULT_CONTABLE_DIARIO_MVTOS){
		$xLog			= new cCoreLog();
		//,$NumMvto = false, $Fecha = false
		$xCta			= new cCuentaContableEsquema($cuenta);
		$cuenta			= $xCta->CUENTA;
		$rebuild		= false;
		if($cargo !== false){
			$cargo		= setNoMenorQueCero($cargo);
			if($cargo != $this->mCargo){ 
				$rebuild 		= true;
				$this->mCargo	= $cargo;
				$this->mAbono	= 0;
			}
		}
		$referencia				= ($referencia == false) ? $this->mObj->referencia()->v(OUT_TXT) : $referencia;
		$concepto				= ($concepto == false ) ? $this->mObj->concepto()->v(OUT_TXT) : $concepto;
		if($abono !== false){
			$abono		= setNoMenorQueCero($abono);
			if($abono != $this->mAbono){ 
				$rebuild 		= true;
				$this->mAbono	= $abono;
				$this->mCargo	= 0;
			}
		}
		if($this->mNumeroCuenta != $cuenta){ 
			$rebuild 			= true;
			$this->mNumeroCuenta= $cuenta;
		}
		//========================
		if($rebuild == true){
			//eliminar
			$this->setEliminar();
			//agregar
			$xPol		= new cPoliza($this->mTipoDePoliza);
			$xPol->setPorCodigo($this->getCodigoDePoliza() );
			$xPol->addMovimiento($this->mNumeroCuenta, $this->mCargo, $this->mAbono, $referencia, $concepto, $this->mNumeroDeMvto);
			$xLog->add($xPol->getMessages(), $xLog->DEVELOPER);
		} else {
			$sql	= "UPDATE contable_movimientos
			SET referencia='$referencia', concepto='$concepto' 
			WHERE clave_unica= " . $this->mCodigoUnico;
			$ql		= new MQL();
			$ql->setRawQuery($sql);
		}
		$this->mMessages	.= $xLog->getMessages();
		return $xLog->getMessages();
	}
	function getMessages($put = OUT_TXT){	$xH		= new cHObject();	return $xH->Out($this->mMessages, $put);	}	
}

class cContableSaldo {
	private $mSaldos		= array();
	private $mCuenta		= 0;
	private $mEjercicio		= false;
	private $mInit			= false;
	public $SDEUDORES		= 2;
	public $SACREEDORES		= 3;
	public $SNATURAL		= 1;
	private $mMessages		= "";
	function __construct($cuenta, $fecha = false){
		$xF					= new cFecha(0, $fecha);
		$this->mEjercicio	= $xF->anno();
		$this->mCuenta		= setNoMenorQueCero($cuenta);
	}
	function init(){
		$xQL		= new MQL();
		$xLog		= new cCoreLog();
		$ejercicio	= $this->mEjercicio;

		//1 =saldos
		$sql		= "SELECT * FROM `contable_saldos` WHERE `cuenta`=" . $this->mCuenta . " AND ejercicio = $ejercicio";
		$data		= $xQL->getDataRecord($sql);
		if(isset($data["ejercicio"])){
			foreach ($data as $rw){ $this->mSaldos[ $rw["tipo"] ] 	= $rw; } //cargar saldos
			$this->mInit						= true;
			if(!isset($this->mSaldos[ $this->SACREEDORES ])){ 
				$this->mInit = false;
				$xLog->add("ERROR\tSaldo ACREEDOR no existe\r\n", $xLog->DEVELOPER);
			}
			if(!isset($this->mSaldos[ $this->SDEUDORES ])){
				$this->mInit = false;
				$xLog->add("ERROR\tSaldo DEUDOR no existe\r\n", $xLog->DEVELOPER);
			}
			if(!isset($this->mSaldos[ $this->SNATURAL ])){
				$this->mInit = false;
				$xLog->add("ERROR\tSaldo NATURAL no existe\r\n", $xLog->DEVELOPER);
			}			
		}
		$this->mMessages	.= $xLog->getMessages();
		return $this->mInit;		
	}
	function getSaldo($tipo, $periodo = 0){
		$saldo 		= 0;
		$campo		= (setNoMenorQueCero($periodo) <= 0) ? "saldo_inicial" : "imp$periodo";
		
		if(isset($this->mSaldos[$tipo])){
			$saldo	= $this->mSaldos[$tipo][$campo];
		}
		return $saldo;
	}
	function getMessages($put = OUT_TXT){	$xH		= new cHObject();	return $xH->Out($this->mMessages, $put);	}
}

?>