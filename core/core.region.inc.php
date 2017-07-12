<?php
include_once ("core.config.inc.php");
include_once ("entidad.datos.php");
include_once ("core.error.inc.php");
include_once ("core.common.inc.php");
include_once ("core.db.inc.php");
include_once ("core.contable.inc.php");
include_once ("core.contable.utils.inc.php");

class cReglasDePais {
	private $mValidIDFiscal			= "";
	private $mValidIDPoblacional	= "";
	private $mValido				= false;
	private $mMessages				= ""; 
	function __construct(){
		$this->mValidIDFiscal	= DEFAULT_PERSONAS_RFC_GENERICO;
	}
	function getValidIDFiscal($idfiscal){
		
		switch (EACP_CLAVE_DE_PAIS){
			case "MX":
				$xpr	= "/^[a-zA-Z]{3,4}(\d{6})((\D|\d){3})?$/";
				if(preg_match($xpr, $idfiscal)){
					$this->mValidIDFiscal	= $idfiscal;		//cumple
					$this->mValido			= true;
				}
				break;
			default:
				$idfiscal				= preg_replace("/[^0-9]/", "", $idfiscal);
				$this->mValidIDFiscal	= setCadenaVal($idfiscal);
				break;
		}
		return $this->mValidIDFiscal;
	}
	function getValidIDPoblacional($idpoblacional){
		switch (EACP_CLAVE_DE_PAIS){
			case "MX":
				$xVal	= new Validate_EsMX();
				$this->mValido = $xVal->dni($idpoblacional);
				$this->mValidIDPoblacional = ($this->mValido == true) ? $idpoblacional : "";
				
				break;
		}
		return $this->mValidIDPoblacional;
	}	
	function isValid(){ return $this->mValido; }
	function getTelMovil($telefono){
		$xT		= new cTipos();
		$tel	= $xT->cInt($telefono);
		$tm		= strlen($tel);
		if(MODO_DEBUG == true){ $this->mMessages .= "WARN\tTelefono $telefono longitud $tm\r\n"; }		
		switch (EACP_CLAVE_DE_PAIS){
			case "MX":

				switch ($tm){
					case 13:
						$tel	= substr($tel, -10);
						break;
					case 12:
						$tel	= null;
						$this->mValido	= false;
						break;
					case 10:
						break;
					case 7:
						$tel	= null;
						$this->mValido	= false;
						break;
					default:
						$tel	= null;
						$this->mValido	= false;
						break;
				}
				
				//10 98164 == 7 
				//981 10 98164 == 10
				//044 981 10 98164 == 13 
				//01 981 10 98164 == 12
				if($tel != null){ $tel	= "52$tel"; }
				break;
		}
		return $tel;
	}
	function getMessages($put = OUT_TXT){ $xH	= new cHObject(); return $xH->Out($this->mMessages, $put);	}
	
	
}


class cXMLCatalogoContable {
	private $mInit		= "";
	private $mTotalC	= 0;
	private $mPeriodo	= 0;
	private $mAnno		= 0;
	private $mEnd		= "</Catalogo>";
	private $mBody		= "";
	function __construct($periodo, $anno){
		$this->mPeriodo	= $periodo;
		$this->mAnno	= $anno;
		
		//$this->mInit	= "<Catalogo Version="1.0" RFC="SIN120706RX7" TotalCtas="98" Mes="07" Ano="2014">";
	}
	function addCuenta($equivalente, $numero, $nombre, $nivel, $naturaleza){
		
		
		/*<Ctas CodAgrup="1" NumCta="100" Desc="CAJA" Nivel="1" Natur="D"/>
		 * <Ctas CodAgrup="2" NumCta="120" Desc="BANCOS" Nivel="1" Natur="D"/>
		 * <Ctas CodAgrup="2.1" NumCta="1200001" Desc="BANCOMER CTA 0190864250" SubCtaDe="120" Nivel="2" Natur="D"/>*/
		/*
		 <Ctas CodAgrup="25.5" NumCta="1960001" Desc="Año 2012" SubCtaDe="196" Nivel="2" Natur="D"/>
		 <Ctas CodAgrup="25.5" NumCta="1960002" Desc="Año 2013" SubCtaDe="196" Nivel="2" Natur="D"/>
		 <Ctas CodAgrup="25.5" NumCta="1960003" Desc="Año 2014" SubCtaDe="196" Nivel="2" Natur="D"/> 
		 **/
		/*
		<Ctas CodAgrup="61" NumCta="500" Desc="COSTO DIRECTO" Nivel="1" Natur="A"/>
		
		<Ctas CodAgrup="61.1" NumCta="5000001" Desc="Honorarios Profesionales" SubCtaDe="500" Nivel="2" Natur="A"/>
		<Ctas CodAgrup="61.1" NumCta="5000002" Desc="Sueldos y Salarios" SubCtaDe="500" Nivel="2" Natur="A"/>
		<Ctas CodAgrup="61.1" NumCta="5000018" Desc="Gastos de Viaje" SubCtaDe="500" Nivel="2" Natur="A"/>
		
		<Ctas CodAgrup="61.1" NumCta="50000180001" Desc="Hospedaje" SubCtaDe="5000018" Nivel="3" Natur="A"/>
		<Ctas CodAgrup="61.1" NumCta="50000180002" Desc="Alimentacion" SubCtaDe="5000018" Nivel="3" Natur="A"/>
		<Ctas CodAgrup="61.1" NumCta="50000180003" Desc="Transporte" SubCtaDe="5000018" Nivel="3" Natur="A"/>
		<Ctas CodAgrup="61.1" NumCta="50000180004" Desc="Pasajes" SubCtaDe="5000018" Nivel="3" Natur="A"/>
		 */
	}
	function render(){
		
	}
}
class cXMLPolizaContable {
	function __construct(){
	
	}	
}
class cXMLBalanzaContable {
	function __construct(){
	
	}	
}




/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * Specific validation methods for data used in MX (Mexico)
 *
 * PHP versions 4
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category  Validate
 * @package   Validate_EsMX
 * @author    Pablo Fischer <pfischer@php.net>
 * @copyright 2006 The PHP Group
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/Validate_esMX
 */

/**
 * Data validation class for Mexico
 *
 * This class provides methods to validate:
 *  - DNI - Nacional Identity document (aka CURP)
 *  - Postal code
 *  - Region (states)
 *  - Phone numbers
 *
 * @category  Validate
 * @package   Validate_EsMX
 * @author    Pablo Fischer <pfischer@php.net>
 * @copyright 2006 The PHP Group
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/Validate_esMX
 */
class Validate_EsMX
{
    /**
     * Validates a postal code
     *
     * Validates the given postal code in two ways:
     *
     *  - Check that the postal code really exists. It uses a file where
     *    all postal codes are.
     *  - Doing a simple regexp: postal codes should be formed of 5 numbers.
     *
     * @param int    $postalCode  Postal code to validate
     * @param bool   $strongCheck True  = It uses a file and checks that the
     *                            postal code exists (Default)
     *                            False = It uses a regexp to do the validation.
     * @param string $dir         Optional; /path/to/data/dir
     *
     * @access public
     * @return bool    Passed / Not passed
     */
    function postalCode($postalCode, $strongCheck = false, $dir = null)
    {
        if ($strongCheck) {
            static $postalCodes;

            if (!isset($postalCodes)) {
                if ($dir != null && (is_file($dir . '/esMX_postcodes.txt'))) {
                    $file = $dir . '/esMX_postcodes.txt';
                } else {
                    $file = '@DATADIR@/Validate_esMX/esMX_postcodes.txt';
                }

                if (!file_exists($file)) {
                    return false;
                }
                $postalCodes = file($file, FILE_IGNORE_NEW_LINES);
            }
            if (!is_array($postalCodes)) {
                return false;
            }
            return in_array((int)$postalCode, $postalCodes);
        }
        return (bool)preg_match('/^[0-9]{5}$/', $postalCode);
    }

    /**
     * Validates the DNI (CURP)
     *
     * It uses the following explanation:
     *
     *  http://web2.tramitanet.gob.mx/info/curp/gifs/ayuda.gif
     *
     * @param string $dni The CURP code
     *
     * @access  public
     * @return  bool    Passed / Not passed
     */
    function dni($dni)
    {
        $dns = strtoupper($dni);
        //Clean it
        $dni = str_replace(array('-', ' '), '', $dni);
        //How big is it?
        if (strlen($dni) !== 18) {
            return false;
        }
        $regexp = '/^([A-Z][AEIOU][A-Z]{2})([0-9]{2})(0?[1-9]|1[0-2])'.
            '(0[1-9]|[1-2][0-9]|3[0-1])(H|M)([A-Z]{2})'.
            '([B-DF-HJ-NP-TV-Z]{3})([0-9]|[A-Z])([0-9]|[A-Z])/i';
        if (preg_match($regexp, $dni, $matches) === 1) {
            //Check the region.
            if (isset($matches[6])) {
                //Not a state or NE (foreign)
                if ($matches[6] != 'NE' && !Validate_esMX::region($matches[6])) {
                    return false;
                }
            } else {
                return false;
            }

            //Unique key
            if (isset($matches[8]) && isset($matches[2])) {
                if ((int)$matches[2]{0} == 0) { //On 2000
                    if (!preg_match('/^[A-Z]/i', $matches[8])) {
                        //If born in 2000 the unique key should be a letter.
                        return false;
                    }
                } else {
                    if (!preg_match('/^[0-9]/i', $matches[8])) {
                        return false;
                    }
                }
            } else {
                return false;
            }

            if (isset($matches[9])) {
                //There's no sense in continue the process if $dni is < 17 chars
                if (strlen($dni) < 17) {
                    return false;
                }
                //CURP algorithm to get the digitVerifier.
                $algChar            = '';
                $curpVerifier       = '';
                $counterDigit       = '';
                $l_digito           = '';
                $l_posicion         = '';
                $digitModule        = '';
                $digitVerifier      = '';
                $combinations       = '0123456789ABCDEFGHIJKLMN-OPQRSTUVWXYZ*';
                $combinationsValues = explode(',', '00,01,02,03,04,05,06,07,08,09'.
                                            ',10,11,12,13,14,15,16,17,18,19,20,'.
                                            '21,22,23,24,25,26,27,28,29,30,31,'.
                                            '32,33,34,35,36,37');
                for ($i=0; $i<strlen($dni); $i++) {
                    $algChar = $dni{$i};
                    if ($algChar == '') {
                        $algChar = '*';
                    }

                    $combinationPos = strpos($combinations, $algChar);
                    if ($combinationPos > -1) {
                        $curpVerifier = $curpVerifier .
                            $combinationsValues[$combinationPos];
                    } else {
                        $curpVerifier = $curpVerifier . '00';
                    }
                }

                for ($i=1; $i<strlen($dni); $i++) {
                    $counterDigit += $curpVerifier{($i*2-1)} * (19 - $i);
                }

                $digitModule = $counterDigit % 10;
                if ($digitModule == 0) {
                    $digitVerifier = '0';
                } else {
                    $digitVerifier = 10 - $digitModule;
                }

                if (strlen($digitVerifier) > 1) {
                    $digitVerifier = substr($digitVerifier, -1);
                }

                if ($digitVerifier != $matches[9]) {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
        return true;
    }

    /**
     * Validates a "region" (aka state) code
     *
     * @param string $region Region/State code
     *
     * @access  public
     * @return  bool    Passed / Not passed
     */
    function region($region)
    {
        switch (strtoupper($region)) {
        case 'AS': //Aguascalientes
        case 'BC': //Baja California
        case 'BS': //Baja California Sur
        case 'CC': //Campeche
        case 'CL': //Coahuila
        case 'CM': //Colima
        case 'CS': //Chiapas
        case 'CH': //Chihuahua
        case 'DF': //Distrito Federal
        case 'DG': //Durango
        case 'GT': //Guanajuato
        case 'GR': //Guerrero
        case 'HG': //Hidalgo
        case 'JC': //Jalisco
        case 'MC': //Mexico
        case 'MN': //Michoac�n
        case 'MS': //Morelos
        case 'NT': //Nayarit
        case 'NL': //Nuevo Le�n
        case 'OC': //Oaxaca
        case 'PL': //Puebla
        case 'QT': //Quer�taro
        case 'QR': //Quintana Roo
        case 'SP': //San Luis Potos�
        case 'SL': //Sinaloa
        case 'SR': //Sonora
        case 'TC': //Tabasco
        case 'TS': //Tamaulipas
        case 'TL': //Tlaxcala
        case 'VZ': //Veracruz
        case 'YN': //Yucat�n
        case 'ZS': //Zacatecas
            return true;
        }
        return false;
    }

    /**
     * Check that the given telephone number is valid.
     *
     * According to COFETEL (Comision Federal de Telecomunicaciones) the telephone
     * numbers can have 7 or 8 digits. Only 3 states can have 8 digit numbers,
     * which are: Distrito Federal, Guadalajara and Monterrey. Others need
     * to have 7 digits.
     *
     * In the case of a required area code the required length should be 12
     * (including the 01). This is for all states.
     *
     * @param string $phone           Phone number
     * @param bool   $requireAreaCode require the area code? (default: true)
     *
     * @access  public
     * @return  bool    Passed / Not passed
     */
    function phone($phone, $requireAreaCode = true)
    {
        return Validate_esMX::phoneNumber($phone, $requireAreaCode);
    }

    /**
     * Check that the given telephone number is valid.
     *
     * According to COFETEL (Comision Federal de Telecomunicaciones) the telephone
     * numbers can have 7 or 8 digits. Only 3 states can have 8 digit numbers,
     * which are: Distrito Federal, Guadalajara and Monterrey. Others need
     * to have 7 digits.
     *
     * In the case of a required area code the required length should be 12
     * (including the 01). This is for all states.
     *
     * @param string $phone           Phone number
     * @param bool   $requireAreaCode Require the area code? (default: true)
     *
     * @access  public
     * @return  bool    Passed / Not passed
     */
    function phoneNumber($phone, $requireAreaCode = true)
    {
        $phone = str_replace(array('(', ')', '-', '+', '.', ' '), '', $phone);
        if ($requireAreaCode) {
            $regexp = '/^01[0-9]{10}$/';
            $match  = (preg_match($regexp, $phone)) ? true : false;
            return $match;
        } else {
            $regexp = '/^[0-9]{7,8}$/';
            $match  = (preg_match($regexp, $phone)) ? true : false;
            return $match;
        }
    }
}


?>
