<?php
include_once ("core.config.inc.php");
include_once ("entidad.datos.php");
include_once ("core.common.inc.php");
include_once ("core.init.inc.php");
include_once ("core.html.inc.php");
include_once ("core.lang.inc.php");
include_once ("core.db.inc.php");
include_once ("core.db.dic.php");

//ini_set("max_execution_time", 600);

$xL = new cLang ();
define ( "CTRL_FECHA_EXTEMPORANEA", "<tr><td>" . $xL->getT ( "TR.Fecha_de_Captura" ) . "</td><td colspan='2'>" . ctrl_date ( 98 ) . "</td></tr>" );
/**
 * Devuelve la Fecha del Sistema (Servidor)
 */
function fechasys() {
	return date ( "Y-m-d" );
}
function get_fecha_de_sistema($date) {
	$d = date ( "Y-m-d" );
	if ($date) {
		$d = date ( "Y-m-d", strtotime ( $date ) );
	}
	return $d;
}
function restarfechas($el_restado, $por_restar) {
	$xF = new cFecha ();
	return $xF->setRestarFechas ( $el_restado, $por_restar );
}
// suma dias a una fecha y lo devuelve en formato fecha aaaa-mm-dd
function sdDate($fecha, $ndias) {
	return date ( "Y-m-d", strtotime ( "$fecha+$ndias day" ) );
}
// suma dias a una fecha, otra funcion
function hazsuma($fecha, $ndias) {
	return date ( "Y-m-d", strtotime ( "$fecha+$ndias day" ) );
}
// -------------------------------------------------------------------------
function sumardias($fecha, $ndias) {
	return date ( "Y-m-d", strtotime ( "$fecha+$ndias day" ) );
}
// Resta dias a una fecha dada y lo devuelve como una fecha Corta
function restardias($mfecha, $ndias) {
	return date ( "Y-m-d", strtotime ( "$mfecha-$ndias day" ) );
}
// Retorna un Dia de la Semana en Nombre segun fecha determinada.
function diasemanal($lfecha) {
	return date ( "l", strtotime ( "$lfecha" ) );
}
// retorna el dia transcurrido en el a�o.
function danno() {
	return date ( "z" );
}
// devuelve el anno segun fecha dada
function esteanno($lafecha) {
	$dnaa = date ( "Y", strtotime ( "$lafecha" ) );
	return $dnaa;
}
// retorna el numero de dias transcurrido en el mes.
function dnmes() {
	return date ( "d" );
}
// retorna el Numero de mes Natural
function dmes() {
	return date ( "m" );
}
/**
 *
 * @deprecated retorna la fecha en Formato AnnO + MES fecha que inicia, mes a sumar, dia a establecer;
 *            
 */
function annomes($lafecha, $masm, $dayme = 0) {
	if ($masm < 0) {
		$monthmenos = strtotime ( "$lafecha-$masm month" );
		$masm = $masm * (- 1);
		$maxim = date ( "t", $monthmenos );
		$theanno = date ( "Y", $monthmenos );
		$themonth = date ( "m", $monthmenos );
	} elseif ($masm == 0) {
		$monthmas = strtotime ( "$lafecha" );
		$theanno = date ( "Y", $monthmas );
		$themonth = date ( "m", $monthmas );
		$maxim = date ( "t", $monthmas );
	} elseif ($masm > 0) {
		$monthmas = strtotime ( "$lafecha+$masm month" );
		$theanno = date ( "Y", $monthmas );
		$themonth = date ( "m", $monthmas );
		$maxim = date ( "t", $monthmas );
	}
	if ($dayme == 0) {
		$dayme = 1;
	}
	// */
	
	if ($dayme > $maxim) {
		$dayme = $maxim;
	}
	
	return $theanno . "-" . $themonth . "-" . $dayme;
}
// retorna los dias transcurridos en el mes segun fecha dada
function numdmes($lafecha) {
	return date ( "d", strtotime ( "$lafecha" ) );
}
/**
 * @deprecated @since 2014.09.09
 */
function getMesesInSelectOptions() {
	$xF = new cFecha ( 0 );
	$txt = "";
	foreach ( $xF->getMesesInArray () as $key => $value ) {
		$txt .= "<option value='$key'>$key - $value</option>";
	}
	return $txt;
}
// RETORNA El NUMERO DE MES SEGUN FECHA DADA
function elmes($lafecha) {
	return date ( "m", strtotime ( "$lafecha" ) );
}
// Funcion que RETORNA EL DIA SEGUN FECHA DADA
function esundia($lafecha) {
	return date ( "d", strtotime ( "$lafecha" ) );
}
// FUNCION QUE CREA UN CONTROL DIA + MES + AnO
function ctrldate($index = 0, $events = "", $tipo = "OPERATIVO") {
	$cFecha = new cFecha ( $index );
	return $cFecha->show ( true, $tipo );
}
// FUNCION QUE CREA UN CONTROL DIA + MES + AnO
function ctrl_date($index = 0, $fecha = 0, $events = "", $tipo = "OPERATIVO") {
	$cFecha = new cFecha ( $index, $fecha );
	return $cFecha->show ( true, $tipo );
}
function fecha_larga($dateme = "now") {
	$xF = new cFecha ( 0, $dateme );
	$xF->init ();
	$comp = $xF->getFechaLarga ();
	return $comp;
}
function getFechaMediana($dateme = "now") {
	$xF = new cFecha ( 0, $dateme );
	$xF->init ();
	$comp = $xF->getFechaMediana ( $dateme );
	return $comp;
}
function dia_semana($date) {
	return getDiaDeLaSemana ( $date );
}
function fecha_corta($dateme = "now") {
	$xF = new cFecha ( 0, $dateme );
	$xF->init ();
	$comp = $xF->getFechaCorta ();
	return $comp;
}
function set_dia_abono_quincenal($date, $dia_primer_periodo, $dia_segundo_periodo) {
	$xF = new cFecha ( 0, $date );
	$fecha_de_pago = $xF->getDiaAbonoQuincenal ( $dia_primer_periodo, $dia_segundo_periodo, $date );
	return $fecha_de_pago;
}
function set_dia_abono_mensual($date, $dia_en_el_mes) {
	$dia_abono_mensual = $date;
	$dias_del_mes = date ( "j", strtotime ( $date ) );
	if (intval ( $dia_en_el_mes ) > intval ( $dias_del_mes )) {
		$dia_en_el_mes = $dias_del_mes;
	}
	$dia_abono_mensual = date ( "Y-m", strtotime ( $dia_abono_mensual ) ) . "-$dia_en_el_mes";
	
	return $dia_abono_mensual;
}
function set_dia_abono_semanal($date, $dia_de_la_semana) {
	$xF = new cFecha ( 0, $date );
	$xF->init ();
	$dia_de_fecha = $xF->get ();
	if ($dia_de_la_semana != date ( "N", $dia_de_fecha )) {
		// obtener Diferencia
	}
	
	return $date;
}
function getFechaMX($fecha_EN_US) {
	$DivisorMysql = "-";
	$anno = 0;
	$mes = 0;
	$dia = 0;
	$pos_2 = strpos ( $fecha_EN_US, "/" );
	// si tiene // como divisor, devolver
	if ($pos_2 > 0) {
		$fecha_EN_US = str_replace ( "/", "-", $fecha_EN_US );
	}
	// aaaa-mm-dd
	$DPart = explode ( "-", $fecha_EN_US );
	$dia = $DPart [0]; // Dia
	$mes = $DPart [1]; // Mes
	$anno = $DPart [2]; // Anno
	                   // verifica si el Elim1 es mayor a 31<br />
	if ($mes > 12) {
		$anno = $DPart [2];
		$dia = $DPart [1];
		$mes = $DPart [0];
		if ($dia > 31) {
			$anno = $DPart [0];
			$dia = $DPart [2];
			$mes = $DPart [1];
		}
	}
	if ($dia > 31) {
		$anno = $DPart [0];
		$dia = $DPart [2];
		$mes = $DPart [1];
	}
	if (strlen ( $anno ) <= 2) {
		$anno = "20" . $anno;
	}
	if ($anno <= 1990) {
		$anno = date ( "Y" );
	}
	if (strlen ( $mes ) <= 1) {
		$mes = "0" . $mes;
	}
	if (strlen ( $dia ) <= 1) {
		$dia = "0" . $dia;
	}
	return ("$dia-$mes-$anno");
}
function SysDate_MX($fecha = false) {
	$fecha = ($fecha == false) ? date ( "d-m-Y" ) : date ( "d-m-Y", strtotime ( $fecha ) );
	return $fecha;
}
function SysDate_US() {
	return (date ( "Y-m-d" ));
}

/**
 *
 *
 * Clase para el Manejo de Fechas
 * 
 * @author Balam Gonzalez Luis Humberto
 * @package core.fechas
 */
class cFecha {
	public $DENACIMIENTO	= "NACIMIENTO";
	public $DEOPERATIVA		= "OPERATIVO";
	
	private $mIndex = 0;
	private $mFecha = false;
	private $mRDate = 0;
	private $mAMeses = array ();
	private $mAHours = array ();
	private $mCurrentDay = false;
	private $mCurrentMonth = false;
	private $mCurrentAnno = false;
	private $mCurrentHour = false;
	private $mCurrentWeekday = false;
	private $mCurrentNumberWeek = false;
	private $mASpanishWeekDay = array ();
	private $mLimitByMonths = array ();
	private $mIncludeNameDay = false;
	private $mFormat = "ISO";
	private $mArrFormat 		= array( "ISO" => "Y-m-d", "ES_MX" => "d-m-Y");
	private $mEvents 			= array();
	private $mEventsValue 		= array();
	/**
	 * Variable que define si la fecha es inhabil
	 * 
	 * @var boolean
	 */
	private $mIsHabil 			= false;
	private $mDiasDelMes 		= "1";
	private $mAnnosOperativos 	= 5;
	private $mAnnoMaximo 		= 2029;
	private $mMessages 			= "";
	/**
	 * funcion constructora
	 * 
	 * @param integer $index
	 *        	de ID del Control de fecha
	 * @param date $fecha
	 *        	del control
	 * @return Null No retorna nada, por lo pronto
	 */
	function __construct($index = 0, $fecha = false) {
		$this->mIndex = $index;
		switch (SAFE_LANG){
			case "es":
				$this->mAMeses = array (1 => "ENERO", 2 => "FEBRERO", 3 => "MARZO", 4 => "ABRIL", 5 => "MAYO", 6 => "JUNIO", 7 => "JULIO", 8 => "AGOSTO", 9 => "SEPTIEMBRE", 10 => "OCTUBRE", 11 => "NOVIEMBRE", 12 => "DICIEMBRE",
						"01" => "ENERO", "02" => "FEBRERO", "03" => "MARZO", "04" => "ABRIL", "05" => "MAYO", "06" => "JUNIO", "07" => "JULIO",	"08" => "AGOSTO", "09" => "SEPTIEMBRE",	"10" => "OCTUBRE", "11" => "NOVIEMBRE","12" => "DICIEMBRE");
				// Dias de la Semana en Array
				$this->mASpanishWeekDay = array (7 => "Domingo", 1 => "Lunes", 2 => "Martes", 3 => "Miercoles", 4 => "Jueves", 5 => "Viernes", 6 => "Sabado");				
				break;
			case "en":
				$this->mAMeses = array (1 =>"JANUARY", 2 =>"FEBRUARY", 3 =>"MARCH", 4 =>"APRIL",5 =>"MAY", 6 =>"JUNE", 7 =>"JULY", 8 =>"AUGUST", 9 =>"SEPTEMBER", 10 =>"OCTOBER", 11 =>"NOVEMBER", 12 =>"DECEMBER",
						"01" =>"JANUARY","02" =>"FEBRUARY","03" =>"MARCH","04" =>"APRIL","05" =>"MAY","06" =>"JUNE","07" =>"JULY","08" =>"AUGUST","09" =>"SEPTEMBER","10" =>"OCTOBER","11" =>"NOVEMBER","12" =>"DECEMBER");
				$this->mASpanishWeekDay = array (7 =>"Sunday",1 =>"Monday",2 =>"Tuesday",3 =>"Wednesday",4 =>"Thursday",5 =>"Friday",6 =>"Saturday");				
				break;
			default:
				$this->mAMeses = array (1 =>"JANUARY", 2 =>"FEBRUARY", 3 =>"MARCH", 4 =>"APRIL",5 =>"MAY", 6 =>"JUNE", 7 =>"JULY", 8 =>"AUGUST", 9 =>"SEPTEMBER", 10 =>"OCTOBER", 11 =>"NOVEMBER", 12 =>"DECEMBER",
						"01" =>"JANUARY","02" =>"FEBRUARY","03" =>"MARCH","04" =>"APRIL","05" =>"MAY","06" =>"JUNE","07" =>"JULY","08" =>"AUGUST","09" =>"SEPTEMBER","10" =>"OCTOBER","11" =>"NOVEMBER","12" =>"DECEMBER");
				$this->mASpanishWeekDay = array (7 =>"Sunday",1 =>"Monday",2 =>"Tuesday",3 =>"Wednesday",4 =>"Thursday",5 =>"Friday",6 =>"Saturday");
				break;
		}

		$this->mLimitByMonths = array (
				1 => 31,
				2 => 28,
				3 => 31,
				4 => 30,
				5 => 31,
				6 => 30,
				7 => 31,
				8 => 31,
				9 => 30,
				10 => 31,
				11 => 30,
				12 => 31 
		);
		$this->mAHours = array (
				"8:00" => "8:00 AM",
				"8:30" => "8:30 AM",
				"9:00" => "9:00 AM",
				"9:30" => "9:30 AM",
				"10:00" => "10:00 AM",
				"10:30" => "10:30 AM",
				"11:00" => "11:00 AM",
				"11:30" => "11:30 AM",
				"12:00" => "12:00 PM",
				"12:30" => "12:30 PM",
				"13:00" => "1:00 PM",
				"13:30" => "1:30 PM",
				"14:00" => "2:00 PM",
				"14:30" => "2:30 PM",
				"15:00" => "3:00 PM",
				"15:30" => "3:30 PM",
				"16:00" => "4:00 PM",
				"16:30" => "4:30 PM",
				"17:00" => "5:00 PM",
				"17:30" => "5:30 PM",
				"18:00" => "6:00 PM",
				"18:30" => "6:30 PM",
				"19:00" => "7:00 PM" 
		);
		// Establece defaults de dia mes anno
		if (($fecha == false) or (! isset ( $fecha ))) {
			$fecha = date ( "Y-m-d" );
		}
		
		$this->mFecha 			= date ( "Y-m-d", strtotime ( $fecha ) );
		$this->init();
		$this->mCurrentHour 	= date ( "G" );
		// Anno Maximo Operativo
		$annoConst 				= (defined ( "EACP_FECHA_DE_CONSTITUCION" )) ? explode ( "-", EACP_FECHA_DE_CONSTITUCION, 2 ) : 2012;
		$annoA					= date ( "Y" ) - $annoConst [0]; // 1998 - 2012 = 14
		$this->mAnnosOperativos = ($annoA > $this->mAnnosOperativos) ? $annoA : $this->mAnnosOperativos;
		unset ( $annoConst );
	}
	function setFechaPorSemana($semana, $dia = false, $AnnoFecha = false) {
		$anno 	= $this->anno($AnnoFecha);
		$fi 	= "$anno-01-01";
		$di 	= date ( "N", strtotime ( $fi ) );
		$dias 	= ($semana * 7) - ($di);
		$d 		= new DateTime ( "$anno-01-01" );
		$fecha = date ( "Y-m-d", strtotime ( "$anno-01-01 +$dias day" ) ); // $d->modify("+$semana week");
		if ($dia != false) {
			if ($dia > 0 and $dia <= 7) {
				$WDias = array (
						1 => "Monday",
						2 => "Tuesday",
						3 => "Wednesday",
						4 => "Thursday",
						5 => "Friday",
						6 => "Saturday",
						7 => "Sunday" 
				);
				$tim 		= strtotime ( $fecha );
				$fecha 		= strtotime ( "Last " . $WDias [$dia], $tim );
			}
		}
		$this->set( $fecha );
		$this->init();
	}
	function setFechaPorQuincena($quincena) {
		$qnas = array (
				1 => 1,
				2 => 1,
				3 => 2,
				4 => 2,
				5 => 3,
				6 => 3,
				7 => 4,
				8 => 4,
				9 => 5,
				10 => 5,
				11 => 6,
				12 => 6,
				13 => 7,
				14 => 7,
				15 => 8,
				16 => 8,
				17 => 9,
				18 => 9,
				19 => 10,
				20 => 10,
				21 => 11,
				22 => 11,
				23 => 12,
				24 => 12 
		);
		$pares = array (
				2 => 1,
				4 => 2,
				6 => 3,
				8 => 4,
				10 => 5,
				12 => 6,
				14 => 7,
				16 => 8,
				18 => 9,
				20 => 10,
				22 => 11,
				24 => 12 
		);
		$anno = $this->anno ();
		$fecha = (isset ( $pares [$quincena] )) ? date ( "Y-m-t", strtotime ( "$anno-" . $qnas [$quincena] . "-16" ) ) : "$anno-" . $qnas [$quincena] . "-15";
		$this->set ( $fecha );
		$this->init ();
	}
	function init($formato = false) {
		$xT = new cTipos ();
		$fecha 	= $this->mFecha;
		$this->mCurrentAnno 		= setNoMenorQueCero( date ( "Y", strtotime ( $fecha ) ), true );
		$this->mCurrentMonth 		= setNoMenorQueCero( date ( "n", strtotime ( $fecha ) ), true );
		$this->mCurrentDay 			= setNoMenorQueCero( date ( "j", strtotime ( $fecha ) ), true );
		$this->mDiasDelMes 			= setNoMenorQueCero( date ( "t", strtotime ( $fecha ) ), true );
		$this->mCurrentWeekday 		= setNoMenorQueCero( date ( "N", strtotime ( $fecha ) ), true );
		$this->mCurrentNumberWeek 	= setNoMenorQueCero( date ( "W", strtotime ( $fecha ) ), true );
		$this->mFormat 				= ($formato == false) ? $this->mFormat : $formato;
	}
	/**
	 * Retorna un Control Select con Dias Mes Anno
	 * @param boolean $ret
	 * si retorna el Control o no.
	 * @param string $type
	 * Tipo de Control fecha, OPERATIVO/NACIMIENTO.
	 */
	function show($ret = false, $type = false) {
		if ($this->mFecha == false) {
			$this->mFecha = date ( "Y-m-d" );
			$this->init ();
		}
		// Tipo Fecha de Nacimiento: NACIMIENTO
		// Tipo Prestamo o Captacion: OPERATIVO
		$elanno = $this->mCurrentAnno;
		$hoy = $this->mCurrentDay;
		$estemes = $this->mCurrentMonth;
		/**
		 *
		 * @var integer Fecha de Mayoria de Edad
		 *     
		 */
		$mAnnoEdadLegal = $this->mCurrentAnno - 18;
		
		$imes = 1;
		$nmes = 1;
		$s1_primo = "";
		$s2 = "";
		$pEvts = "";
		
		foreach ( $this->mEvents as $key => $value ) {
			$tmpValue = $this->mIndex;
			if (isset ( $this->mEventsValue [$key] )) {
				$tmpValue = $this->mEventsValue [$key];
			}
			$pEvts .= " $key=\"$value('" . $tmpValue . "');\" ";
		}
		
		$s1 = "<select name='eldia" . $this->mIndex . "' id='ideldia" . $this->mIndex . "' $pEvts class='x20s100'>";
		while ( $imes <= 31 ) {
			if ($imes == $hoy) {
				$select = "selected";
			} else {
				$select = "";
			}
			$s1_primo = $s1_primo . "<option value='$imes' $select>$imes</option> \n";
			$imes ++;
		}
		
		$s1 = $s1 . $s1_primo . "</select>";
		
		// ----------------------------------------------------------------------
		$array_mes = $this->mAMeses;
		while ( $nmes <= 12 ) {
			if ($nmes == $estemes) {
				$select = "selected";
			} else {
				$select = "";
			}
			// $idem = $
			$s2 = $s2 . "<option value='$nmes' $select>$array_mes[$nmes]</option>";
			$nmes ++;
		}
		
		$s2 = "<select name='elmes" . $this->mIndex . "' id='idelmes" . $this->mIndex . "' $pEvts class='x50s100'>" . $s2 . "</select>";
		$s3 = "<input type='text' name='elanno" . $this->mIndex . "' value='$elanno' id='idelanno" . $this->mIndex . "'  $pEvts class='x25s100'>";
		// condicionante de fecha
		if ($type != false) {
			$txtAOpt = "";
			switch ($type) {
				
				case "NACIMIENTO" :
					for($ia = 0; $ia <= EDAD_PRODUCTIVA_MAXIMA; $ia ++) {
						$anno = date ( "Y" ) - $ia;
						$mSel = "";
						if ($anno == $mAnnoEdadLegal) {
							$mSel = " selected='true' ";
						}
						$txtAOpt .= "<option value='$anno' $mSel>$anno</option>";
					}
					break;
				case "OPERATIVO" :
					$annoAnt = $this->mAnnosOperativos;
					for($ia = ($annoAnt * - 1); $ia <= $annoAnt; $ia ++) {
						$anno = date ( "Y" ) + $ia;
						$select = ($anno == $this->mCurrentAnno) ? "selected='true'" : "";
						// 2012-01-06: filtra si es maximo al anno permitido
						$txtAOpt .= ($anno <= $this->mAnnoMaximo) ? "<option value='$anno' $select>$anno</option>" : "";
					}
					break;
				default :
					break;
			}
			$s3 = "<select  name='elanno" . $this->mIndex . "' id='idelanno" . $this->mIndex . "' class='x25s100'>
		$txtAOpt
		</select>";
		}
		$this->mRDate = $s1 . $s2 . $s3;
		
		if ($ret == false) {
			echo $this->mRDate;
		} else {
			return $this->mRDate;
		}
	}
	/**
	 * Funcion que retorna un HTML:SELECT con las horas del dia
	 * 
	 * @param boolean $ret
	 *        	se imprime o se retorna un valor
	 * @return string del SELECT o se imprime con echo
	 */
	function getHours($ret = false, $titulo = "", $id = "") {
		$id 			= ($id == "") ? "id-hours-" . $this->mIndex : $id;
		$name 			= ($id == "") ? "cHours-" . $this->mIndex : $id;
		$options 		= "";
		$xL 			= new cLang ();
		$titulo = ($titulo == "") ? "" : $xL->getT ( $titulo );
		foreach ( $this->mAHours as $key => $value ) {
			$options .= "<option value=\"$key\">$value</option>";
		}
		$cHours = "<select id=\"$id\" name=\"$name\" >
		$options
		</select>";
		return ($titulo == "") ? $cHours : "<div class=\"tx4\"><label for=\"$id\">$titulo</label>$cHours</div>";
	}
	/**
	 * Devguelve un HTML:SELECT con horas selecionables
	 * 
	 * @param $ret Retornar?        	
	 * @param $ctrl Nombre
	 *        	Control, propiedad HTML:name
	 * @param $id ID
	 *        	Control, propiedad HTML:id
	 * @return string compatible
	 */
	function getSelectHour($ret = false, $ctrl = false, $id = false) {
		if ($id == false) {
			$id = "id-hours-" . $this->mIndex;
		}
		if ($ctrl == false) {
			$ctrl = "cHours-" . $this->mIndex;
		}
		$options = "";
		foreach ( $this->mAHours as $key => $value ) {
			$options .= "<option value=\"$key\">$value</option>";
		}
		$cHours = "<select id=\"$id\" name=\"$ctrl\">
		$options
		</select>";
		return $cHours;
	}
	/**
	 * function que retorna un <SELECT> para HTML
	 *
	 * @param string $name
	 *        	HTML del Control
	 * @param string $id
	 *        	HTML del Control
	 * @param array $arrEvents
	 *        	de eventos del tipo onclick => evento
	 * @return string SELECT
	 */
	function getSelectMeses($name, $id = false, $arrEvents = false) {
		$events = "";
		$options = "";
		if ($id == false) {
			$id = "id-$name";
		}
		if (is_array ( $arrEvents )) {
			foreach ( $arrEvents as $key => $value ) {
				$events .= " $key =\"$value\" ";
			}
		}
		$cMeses = $this->mAMeses;
		foreach ( $cMeses as $key => $value ) {
			$sMes = "";
			if ($key == $this->mCurrentMonth) {
				$sMes = "selected=\"true\" ";
			}
			$options .= "<option value='$key' $sMes>$value</option> ";
		}
		$ctrl = "<select id=\"$id\" name=\"$name\" $events>
				$options
				</select>";
		return $ctrl;
	}
	function set($mFecha = false) {
		if ($mFecha != false) {
			$this->mFecha = date ( "Y-m-d", strtotime ( $mFecha ) );
			$this->init ();
		}
	}
	function getJSEvaluateDateCode() {	}
	function setSeparador($separador) {
		$this->mArrFormat [$this->mFormat] = str_replace ( "-", $separador, $this->mArrFormat [$this->mFormat] );
		// error_log($this->mArrFormat[ $this->mFormat ]);
	}
	/**
	 * Funcion que devuelve la Fecha Actual segun Formato dado
	 * 
	 * @return date Actual
	 *        
	 */
	function get($format = false) {
		$format = ($format == false) ? $this->mFormat : $format;
		return date ( $this->mArrFormat [$format], strtotime ( $this->mFecha ) );
	}
	/**
	 * funcion que resta dos fechas en formato ISO
	 * 
	 * @param date $FechaSustraendo
	 *        	a la que se resta
	 * @param date $FechaMinuendo
	 *        	restada
	 * @return integer de Dias
	 */
	function setRestarFechas($FechaSustraendo, $FechaMinuendo) {
		$fecha1 = strtotime ( trim ( $FechaSustraendo ) );
		$fecha2 = strtotime ( trim ( $FechaMinuendo ) );
		$s = ($fecha1 - $fecha2);
		$d = ($s / 86400); // Dias
		$d = round ( $d, 0 );
		return $d;
	}
	/**
	 * Agrega Eventos de la seleccion
	 */
	function addEvent($event, $funcion, $value = false) {
		$this->mEvents [$event] = $funcion;
		if ($value != false) {
			$this->mEventsValue [$event] = $value;
		}
	}
	/**
	 * Suma Dias a una Fecha en formato ISO
	 * 
	 * @param $dias Dias
	 *        	sumar
	 * @param $fecha Fecha
	 *        	la que se va a sumar
	 * @return date en formato ISO AAAA-MM-DD
	 */
	function setSumarDias($dias, $fecha = false) {
		if ($fecha == false) {
			$fecha = $this->mFecha;
		}
		return date ( "Y-m-d", strtotime ( "$fecha+$dias days" ) );
	}
	/**
	 * resta dias a una Fecha en formato ISO
	 * 
	 * @param $dias Dias
	 *        	restar
	 * @param $fecha Fecha
	 *        	la que se va a restar
	 * @return date en formato ISO AAAA-MM-DD
	 */
	function setRestarDias($dias, $fecha = false) {
		$fecha = ($fecha == false) ? $this->mFecha : $fecha;
		return date ( "Y-m-d", strtotime ( "$fecha-$dias days" ) );
	}
	function setRestarMeses($meses, $fecha = false) {
		$fecha = ($fecha == false) ? $this->mFecha : $fecha;
		return date ( "Y-m-d", strtotime ( "$fecha-$meses month" ) );
	}
	function setSumarMeses($meses, $fecha = false) {
		$fecha = ($fecha == false) ? $this->mFecha : $fecha;
		return date ( "Y-m-d", strtotime ( "$fecha+$meses month" ) );
	}
	function getJavaScript() {
	}
	/**
	 * Devuelve el nombre del dia de la semana en Español
	 * 
	 * @param $date [OPCIONAL]Fecha
	 *        	obtener el nombre
	 * @return string del día de la semana en Español
	 */
	function getDayName($date = false) {
		if ($date == false) {
			$date = $this->mFecha;
		}
		$dias_semana = date ( "N", strtotime ( $date ) );
		return $this->mASpanishWeekDay [$dias_semana];
	}
	/**
	 * Obtiene una fecha evaluada que es LABORABLE, seleccionando de una Tabla/DB datos previamente guardado
	 * 
	 * @param date $dia_a_evaluar
	 *        	se evalua
	 * @return date Laborable
	 */
	function getDiaHabil($dia_a_evaluar = false) {
		if ($dia_a_evaluar == false) {
			$dia_a_evaluar = $this->mFecha;
		}
		// Dias no Laborables(En Ingles) segun las Necesidades de la Empresa
		$dias_no_laborables = array ();
		if (WORK_IN_SATURDAY == false) {
			$dias_no_laborables ["Saturday"] = 6;
		}
		$Operador 			= "+";
		$dias_no_laborables["Sunday"] = 7;
		
		$fecha_no_festiva 	= $dia_a_evaluar;
		$xCache				= new cCache();
		$idx				= "general_dias_festivos-rs";
		$rs					= $xCache->get($idx);
		if($rs === null){
			$xQL			= new MQL();
			$rs				= $xQL->getDataRecord("SELECT * FROM general_dias_festivos");
			$xCache->set($idx, $rs);
		}
		foreach ($rs as $rw){
			if (strtotime ( $dia_a_evaluar ) == strtotime ( $rw ["fecha_marcado"] )) {
				$fecha_no_festiva = date ( "Y-m-d", strtotime ( "$dia_a_evaluar" . $Operador . "1 day" ) );
			}
		}
		// Evalua los Dias inhabiles
		for($i = 0; $i <= 5; $i ++) {
			$dia_en_la_semana = date ( "l", strtotime ( $fecha_no_festiva ) );
			if (array_key_exists ( $dia_en_la_semana, $dias_no_laborables )) {
				$fecha_no_festiva 	= date ( "Y-m-d", strtotime ( "$fecha_no_festiva" . $Operador . "1 day" ) );
			} else {
				$this->mIsHabil		= true;
				// Salir del bucle
				break;
			}
		}
		return $fecha_no_festiva;
	}
	function getEsHabil(){ return $this->mIsHabil; }
	function getFechaMediana($dateme = false) {
		$this->set ( $dateme );
		$comp = $this->dia () . " DE " . $this->getMesNombre () . " DE " . $this->anno ();
		return $comp;
	}
	function getFechaCorta($dateme = false) {
		$comp = "";
		$this->set ( $dateme );
		$comp = $this->dia () . "/" . substr ( $this->getMesNombre (), 0, 3 ) . "/" . $this->anno ();
		return $comp;
	}
	function getFechaDDMM($fecha = false) {
		$fecha = ($fecha == false) ? $this->get () : $fecha;
		$fecha = $this->getFechaISO ( $fecha );
		$comp = "";
		$this->set ( $fecha );
		$comp = $this->dia () . "/" . substr ( $this->getMesNombre (), 0, 3 );
		return $comp;
	}
	function getFechaLarga($dateme = false) {
		$this->set ( $dateme );
		return $this->getDayName () . "," . $this->dia () . " DE " . $this->mAMeses [$this->mes ()] . " DE " . $this->anno ();
	}
	function getMesNombre($dateme = false) {
		$this->set ( $dateme );
		return $this->mAMeses [$this->mes ()];
	}
	function getBuscarFechaPorDiaDeSemana($dia, $fecha_inicial = false) {
		$fecha_inicial = ($fecha_inicial == false) ? $this->get () : $fecha_inicial;
		$sucess = false;
		$fecha = $fecha_inicial;
		$dia = ($dia < 1) ? 1 : $dia;
		$dia = ($dia > 7) ? 7 : $dia;
		
		for($i = 0; $i <= 7; $i ++) {
			if ($sucess == false) {
				$diaActual = $this->setSumarDias ( $i, $fecha_inicial );
				$mrkDia = date ( "N", strtotime ( $diaActual ) );
				if (intval ( $mrkDia ) == intval ( $dia )) {
					$sucess = true;
					$fecha = $diaActual; //Evaluar en windows. Nota: No hay soporte Windows
					$this->mMessages .= "$i\t$fecha_inicial\t$dia\tFECHA.OK\tEncontrado el dia $mrkDia en la fecha $diaActual\r\n";
				} else {
					$this->mMessages .= "$i\t$fecha_inicial\t$dia\tFECHA\tBuscando en la fecha $diaActual, Dia $mrkDia \r\n";
				}
			}
		}
		return $fecha;
	}
	/**
	 * Obtiene el Dia Inicial del Mes según fecha dada
	 * @param string $dateme        	
	 */
	function getDiaInicial($dateme = false) {
		if ($dateme !== false) {
			$this->set ( $dateme );
		}
		return date ( "Y-m-", strtotime ( $this->get () ) ) . "01";
	}
	function getFechaInicialDelAnno($dateme = false) {
		if ($dateme !== false) {
			$this->set ( $dateme );
		}
		return date("Y-01-01", $this->getInt());
	}	
	/**
	 * Obtiene el Dia Final del Mes según fecha dada
	 * 
	 * @param string $dateme        	
	 */
	function getDiaFinal($dateme = false) {
		if ($dateme !== false) {
			$this->set ( $dateme );
		}
		return date ( "Y-m-t", strtotime ( $this->get () ) );
	}
	function getFechaMesAnterior($dateme = false, $MesesAnteriores = 1) {
		$dateme = ($dateme == false) ? $this->mFecha : $dateme;
		return date ( "Y-m-d", strtotime ( "$dateme-$MesesAnteriores month" ) );
	}
	function dia($fecha = false) {
		if($fecha != false){ $this->set($fecha); }
		return $this->mCurrentDay;
	}
	function anno($fecha = false) {
		if($fecha != false){ $this->set($fecha); }
		return $this->mCurrentAnno;
	}
	function mes($fecha = false) {
		if($fecha != false){ $this->set($fecha); }
		return $this->mCurrentMonth;
	}
	function semana($fecha = false) {
		if($fecha != false){
			$this->mCurrentNumberWeek 	= setNoMenorQueCero( date ( "W", strtotime ( $fecha ) ), true );
		}
		return $this->mCurrentNumberWeek;
	}
	function quincena($fecha = false) {
		$fecha = ($fecha == false) ? $this->get () : $fecha;
		$dia = date ( "j", $this->getInt ( $fecha ) );
		$mes = date ( "n", $this->getInt ( $fecha ) ) - 1; // iniciar de cero, nov 11 = 22 completo, qna 21 y 22
		return ($dia >= 16) ? ($mes + 1) * 2 : ($mes * 2) + 1;
	}
	/**
	 * Obtiene los dias del mes
	 * 
	 * @param string $dateme        	
	 */
	function getDiasDelMes($dateme = false) {
		$this->set ( $dateme );
		$this->mDiasDelMes = date ( "t", strtotime ( $this->get () ) );
		return $this->mDiasDelMes;
	}
	function getFechaMaximaOperativa() {
		$AnnoFinal		= date ( "Y" ) + $this->mAnnosOperativos;
		$FechaMaxima 	= ($AnnoFinal > $this->mAnnoMaximo) ? $this->mAnnoMaximo . "-12-31" : $AnnoFinal . "-12-31";
		return $FechaMaxima;
	}
	function getFechaMinimaOperativa(){ return $this->getFechaISO(EACP_FECHA_DE_CONSTITUCION);	}
	function getDiaAbonoQuincenal($dia_primer_periodo, $dia_segundo_periodo, $date = false) {
		$fecha_de_pago 	= ($date == false) ? $this->mFecha : $date;
		$this->set( $fecha_de_pago );
		$dias_del_mes 	= $this->getDiasDelMes($fecha_de_pago);
		$dia_en_el_mes 	= $this->dia($fecha_de_pago);
		
		$dif_mes 				= 0;
		$dias_ajustados 		= 0;
		$diferencia_en_el_mes 	= 0;
		
		$i_primer_periodo 		= $dia_primer_periodo - 8;
		if ($i_primer_periodo < 0) {
			$i_primer_periodo 	= 0;
		}
		$f_primer_periodo 		= $dia_primer_periodo + 8;
		$i_segundo_periodo 		= $dia_segundo_periodo - 8;
		if ($i_segundo_periodo < $f_primer_periodo) {
			$i_segundo_periodo 	= $f_primer_periodo + 1;
		}
		$f_segundo_periodo = $dia_segundo_periodo + 8;
		if ($f_segundo_periodo > $dias_del_mes) {
			$f_segundo_periodo 	= $dias_del_mes;
		}
		
		if ($dia_en_el_mes >= $i_primer_periodo && $dia_en_el_mes <= $f_primer_periodo) {
			$fecha_de_pago = date ( "Y-m", strtotime ( $date ) ) . "-" . $dia_primer_periodo;
		} elseif ($dia_en_el_mes >= $i_segundo_periodo && $dia_en_el_mes <= $f_segundo_periodo) {
			$fecha_de_pago = date ( "Y-m", strtotime ( $date ) ) . "-" . $dia_segundo_periodo;
		} elseif ($dia_en_el_mes > $f_segundo_periodo) {
			$diferencia_en_el_mes 	= $dias_del_mes - $dia_en_el_mes;
			$dias_ajustados 		= $diferencia_en_el_mes + $dia_primer_periodo;
			$fecha_de_pago 			= sumardias ( $date, $dias_ajustados );
		}
		return $fecha_de_pago;
	}
	function getDiaAbonoSemanal($DiaSemana, $Fecha = false) {
		return $this->getBuscarFechaPorDiaDeSemana ( $DiaSemana, $Fecha );
	}
	function getDiaAbonoDecenal($dia1, $dia2, $dia3, $fecha = false) {
		$fecha = ($fecha == false) ? $this->mFecha : $fecha;
		// reparar el array
		$d = array (
				$dia1,
				$dia2,
				$dia3 
		);
		asort ( $d );
		$dia1 			= isset ( $d [0] ) ? $d [0] : 10;
		$dia2 			= isset ( $d [1] ) ? $d [1] : 20;
		$dia3 			= isset ( $d [2] ) ? $d [2] : 30;
		
		$ifecha 		= strtotime ( $fecha );
		$dia_del_mes 	= intval ( date ( "j", $ifecha ) );
		$dias_del_mes 	= intval ( date ( "t", $ifecha ) );
		
		$dia3 			= ($dia3 > $dias_del_mes) ? $dias_del_mes : $dia3;
		$res 			= $fecha;
		$lim0 			= intval ( $dia1 / 2 );
		$lim1 			= $dia1 + intval ( $dia1 / 2 );
		$lim2 			= $dia2 + intval ( ($dia2 - $dia1) / 2 );
		if ($dia_del_mes <= $lim0) {
			$res 			= $this->setRestarDias ( $lim0, date ( "Y-m-", $ifecha ) . "01" );
			$dias_del_mes 	= intval ( date ( "t", strtotime ( $res ) ) );
			$dia3			= ($dia3 > $dias_del_mes) ? $dias_del_mes : $dia3;
			$res = date ( "Y-m-", strtotime ( $res ) ) . $dia3;
		} elseif ($dia_del_mes > $lim0 and ($dia_del_mes <= $lim1)) {
			$res = date ( "Y-m-", $ifecha ) . $dia1;
		} elseif (($dia_del_mes > $lim1) and ($dia_del_mes <= $lim2)) {
			$res = date ( "Y-m-", $ifecha ) . $dia2;
		} elseif ($dia_del_mes > $lim2) {
			$res 			= date ( "Y-m-", $ifecha ) . $dia3;
		}
		return $res;
	}
	function getMesesInArray() {
		//2014-10-20
		$arrM = $this->mAMeses;
		// limpiar el array
		unset ( $arrM ["01"], $arrM ["02"], $arrM ["03"], $arrM ["04"], $arrM ["05"], $arrM ["06"], $arrM ["07"], $arrM ["08"], $arrM ["09"]);//, $arrM ["10"], $arrM ["11"], $arrM ["12"] );
		return $arrM;
	}
	function getAnnosInArray(){
		$annoCur	= date ( "Y" );
		if(defined("EACP_FECHA_DE_CONSTITUCION")){
			$annV	= date("Y", strtotime(EACP_FECHA_DE_CONSTITUCION));
			$this->mAnnosOperativos	= (($annoCur - $annV) > 1) ? ($annoCur - $annV) : $this->mAnnosOperativos;
		}
				 
		$annoAnt 	= $this->mAnnosOperativos;
		$annoMax	= $this->mAnnoMaximo;
		
		$arrAnnios	= array();
		for($i = 0; $i<= $this->mAnnosOperativos; $i++){
			$idx				= $annoCur - $i;
			$arrAnnios[$idx]	= $idx;
		}
		for($i = $annoCur; $i<= $annoMax; $i++){
			$arrAnnios[$i]	= $i;
		}
		
		return $arrAnnios;
	}
	function getDiasDeSemanaInArray() {	return $this->mASpanishWeekDay;	}
	function getDiaDeAbonoMensual($dia_en_el_mes, $date = false) {
		$date = ($date == false) ? $this->mFecha : $date;
		$dia_abono_mensual = $date;
		$dias_del_mes = date ( "t", strtotime ( $date ) );
		if (intval ( $dia_en_el_mes ) > intval ( $dias_del_mes )) {
			$dia_en_el_mes = $dias_del_mes;
		}
		$dia_abono_mensual = date ( "Y-m", strtotime ( $dia_abono_mensual ) ) . "-$dia_en_el_mes";
		return $dia_abono_mensual;
	}
	function getFechaISO($fecha_ES_MX = false) {
		$fecha_ES_MX 	= ($fecha_ES_MX == false) ? $this->mFecha : $fecha_ES_MX;
		$fecha_ES_MX 	= str_replace("/", "-", $fecha_ES_MX);
		$D 				= explode("-", $fecha_ES_MX, 3);
		if(setNoMenorQueCero($D[0]) <= 0 OR (!isset($D[1])) ){
			if (MODO_DEBUG == true) {
				//setLog( "Fecha fallida $fecha_ES_MX" );
			}			
		} else {
			$anno 			= $this->anno();
			$mes 			= $this->mes();
			$dia 			= $this->dia();
			
			if (setNoMenorQueCero( $D[0] ) > 31) {
				$anno 		= setNoMenorQueCero ( $D[0] );
				$mes 		= setNoMenorQueCero ( $D[1] );
				$dia 		= setNoMenorQueCero ( $D[2] );
				//setLog($fecha_ES_MX);
			} else {
				if (isset ( $D [2] )) {
					$panno 	= setNoMenorQueCero ( $D [2] );
					if ($panno > 31) {
						$anno = $panno;
					}
				}
				if (! isset ( $D [1] )) {
					if (MODO_DEBUG == true) {
						//setLog ( "Fecha fallida $fecha_ES_MX" );
					}
				}
				$mes = setNoMenorQueCero ( $D [1] );
				$dia = setNoMenorQueCero ( $D [0] );
			}
			
			$anno = ($anno > 2099 or $anno < 1900) ? $this->anno () : $anno;
			$mes = ($mes < 1 or $mes > 12) ? $this->mes () : $mes;
			$dia = ($dia < 1 or $dia > 31) ? $this->dia () : $dia;
			$fecha_ES_MX = mktime ( 0, 0, 0, $mes, $dia, $anno );
			$this->mFecha = date ( "Y-m-d", $fecha_ES_MX );
		}
		return $this->mFecha;
	}
	function getFechaMX($fecha = false, $separador = false) {
		$separador = ($separador == false) ? "/" : $separador;
		$fecha = ($fecha == false) ? $this->getInt () : strtotime ( $fecha );
		$fmt = str_replace ( "-", $separador, "d-m-Y" );
		return date ( $fmt, $fecha );
	}
	function getEscalaTiempo($fechaInicial) {
		$dias = $this->setRestarFechas ( $this->get (), $fechaInicial );
		$sql = "SELECT * FROM socios_tiempo WHERE idsocios_tiempo > $dias ORDER BY idsocios_tiempo DESC LIMIT 0,1";
		$id = getFila ( $sql, "idsocios_tiempo" );
		if ($id < DEFAULT_TIEMPO) {
			$id = DEFAULT_TIEMPO;
		}
		return $id;
	}
	function getSelectSemanas($id, $mark = 1) {
		$options 		= array ();
		for($i = 1; $i <= 52; $i ++) {
			$this->setFechaPorSemana ( $i );
			$options [$i] = "SEMANA $i -" . $this->getFechaCorta();
		}
		return new cHSelect ( $id, $options );
	}
	function getSelectQuincenas($id, $mark = 1) {
		$options = array ();
		for($i = 1; $i <= 24; $i ++) {
			$this->setFechaPorQuincena ( $i );
			$options [$i] = "QUINCENA $i -" . $this->getFechaCorta();
		}
		return new cHSelect ( $id, $options );
	}
	function getSelectDeMeses($id, $mark = 1) {
		$options = array ();
		for($i = 1; $i <= 12; $i ++) {
			// $this->setFechaPorQuincena($i);
			$options [$i] = $this->getMesNombre ( $this->anno () . "-$i-01" ) . "/" . $this->anno ();
		}
		$xSel	= new cHSelect ( $id, $options );
		
		return $xSel;
	}
	function getInt($fecha = false) {
		$fecha = ($fecha == false) ? $this->get () : $fecha;
		return strtotime ( $fecha );
	}
	function getFechaByInt($entero) {
		$this->mFecha = date ( "Y-m-d", $entero );
		return $this->mFecha;
	}
	function getMessages($put = OUT_TXT) {
		$xH = new cHObject ();
		return $xH->Out ( $this->mMessages, $put );
	}
	function getDiasHabilesEnRango($fecha_final, $fecha_inicial = false) {
		$fecha_inicial = ($fecha_inicial == false) ? $this->get () : $fecha_inicial;
		// Dias no Laborables(En Ingles) segun las Necesidades de la Empresa
		$dias_no_laborables = 0;
		$sabados = 0;
		$domingos = 0;
		$sql = "SELECT COUNT(fecha_marcado) AS 'festivos' FROM general_dias_festivos WHERE `fecha_marcado` >='$fecha_inicial' AND `fecha_marcado` <='$fecha_final' ";
		$dias_no_laborables = mifila ( $sql, "festivos" );
		$dias_en_rango = $this->setRestarFechas ( $fecha_final, $fecha_inicial );
		for($iniciar = 0; $iniciar <= $dias_en_rango; $iniciar ++) {
			$dia = intval ( date ( "N", strtotime ( $this->setSumarDias ( $iniciar, $fecha_inicial ) ) ) );
			if ($dia == 7) {
				$dias_no_laborables ++;
			} // DOMINGO
			if (WORK_IN_SATURDAY == false) {
				if ($dia == 6) {
					$dias_no_laborables ++;
				}
			}
		}
		return $dias_no_laborables;
	}
	function out($fmt, $fecha = false) {
		$fecha = ($fecha == false) ? $this->get () : $fecha;
		return date ( $fmt, strtotime ( $fecha ) );
	}
	function getEsActual($fechacomparada) {
		return ($this->getInt ( $fechacomparada ) == $this->getInt ( fechasys () )) ? true : false;
	}
	function getMarca() {
		$date = $this->getInt ();
		return date ( "Ymd.His", $date );
	}
	
	function getFechaDeInicioDeSemana($fecha = false) {
		// $this->mCurrentWeekday = $xT->cInt( date("N", strtotime($fecha)), true );
		$fecha = ($fecha == false) ? $this->get () : $fecha;
		$fecha = false;
		$inicio = 1;
		
		for($i = 0; $i <= 6; $i ++) {
			$dif = $i; // * -1;
			$dia = date ( "N", strtotime ( $this->setRestarDias ( $dif, $fecha ) ) );
			if ($dia == $inicio) {
				$fecha = $this->setRestarDias ( $dif, $fecha );
			}
		}
		return $fecha;
	}
	function getDiasCorrientesDeMes() {
		return $this->dia ();/* $this->setRestarFechas($this->get(), $this->anno() . "-" . $this->mes() . "-01" );*/ }
	function getDiaDeLaSemana($fecha = false) {
		$fecha = ($fecha == false) ? $this->get () : $fecha;
		return date ( "N", strtotime ( $fecha ) );
	}
	function getOTrimestre($fecha = false){
		$fecha = ($fecha == false) ? $this->get () : $fecha;
		return new cFechaTrimestre($fecha);
	}
	function getAnnosOperativos(){ return $this->mAnnosOperativos; }
	function getFechaFinAnnio(){ return $this->anno() . "-12-31"; }
	function getCompare($f2, $f1 = false){
		$f2	= $this->getInt($f2);
		$f1	= $this->getInt($f1);
		return ($f2 == $f1) ? true : false;
	}
}
class cFechaTrimestre {
	private $mFechaInicial	= false;
	private $mFechaFinal	= false;
	private $mID			= 1;
	function __construct($fecha = false) {
		$xF	= new cFecha();
		$xF->set( $xF->getFechaISO($fecha) );
		$mes	= $xF->mes();
		$annio	= $xF->anno();
		
		if($mes <= 3){
			$this->mID	= 1;
			$this->mFechaInicial	= "$annio-01-01";
			$this->mFechaFinal		= "$annio-03-31";
		} elseif ($mes >=4 AND $mes <=6){
			$this->mID	= 2;
			$this->mFechaInicial	= "$annio-04-01";
			$this->mFechaFinal		= "$annio-06-30";			
		} elseif ($mes >=7 AND $mes <=9){
			$this->mID	= 3;
			$this->mFechaInicial	= "$annio-07-01";
			$this->mFechaFinal		= "$annio-09-30";			
		} else {
			$this->mID	= 4;
			$this->mFechaInicial	= "$annio-10-01";
			$this->mFechaFinal		= "$annio-12-31";
		}
	}
	function getFechaInicial() { return $this->mFechaInicial;	}
	function getFechaFinal() { return $this->mFechaFinal; 	}
}
function validarFechaUS($date) {
	$sucess = true;
	$date = strtotime ( $date );
	// Tipos
	$TMPAnno = date ( "Y", $date );
	$TMPMes = date ( "m", $date );
	$TMPDia = date ( "d", $date );
	
	settype ( $TMPAnno, "integer" );
	settype ( $TMPMes, "integer" );
	settype ( $TMPDia, "integer" );
	$TMPAnno = date ( "Y", $date );
	$TMPMes = date ( "m", $date );
	$TMPDia = date ( "d", $date );
	if (($TMPMes == 0) or ($TMPMes > 12)) {
		$sucess = false;
	}
	if (($TMPDia == 0) or ($TMPDia > 31)) {
		$sucess = false;
	}
	if ($TMPAnno < 1970) {
		$sucess = false;
	}
	// $sucess = checkdate($TMPMes, $TMPDia, $TMPAnno);
	return $sucess;
}
function getFechaCorta($dateme = false) {
	$xF = new cFecha ( 0, $dateme );
	$comp = $xF->getFechaCorta ();
	return $comp;
}
function getDiaDeLaSemana($date) {
	$xF = new cFecha ( 0, $date );
	$xF->init ();
	return $xF->getDayName ( $date );
}
function getFechaLarga($dateme = "now") {
	return fecha_larga ( $dateme );
}
function getFechaUS($fecha_ES_MX) {
	$xF = new cFecha ( 0 );
	return $xF->getFechaISO ( $fecha_ES_MX );
}
function getNextDate($date, $number = 1, $type = "Month") {
	$RDate = $date;
	
	$anno = date ( "Y", strtotime ( $date ) );
	$month = date ( "m", strtotime ( $date ) );
	$day = date ( "d", strtotime ( $date ) );
	
	$MasAnnos = 0;
	$MasMonths = 0;
	$MasDays = 0;
	
	switch ($type) {
		case "Month" : // 10 /12 = 1.5
			$MasAnnos = floor ( $number / 12 );
			// 18 - 12 = 6
			$MasMonths = $number - ($MasAnnos * 12);
			
			$month += $MasMonths;
			if ($month > 12) {
				$anno += 1;
				$month -= 12;
			}
			$anno += $MasAnnos;
			// validar la fecha
			if (checkdate ( $month, $day, $anno ) == false) {
				// Ajustar los Dias
				$MaxDay = date ( "t", strtotime ( "$anno-$month-01" ) );
				
				if ($day > $MaxDay) {
					$day = $MaxDay;
				}
			}
			$RDate = date ( "Y-m-d", strtotime ( "$anno-$month-$day" ) );
			break;
	}
	return $RDate;
}

?>