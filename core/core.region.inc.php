<?php
include_once ("core.config.inc.php");
include_once ("entidad.datos.php");
include_once ("core.error.inc.php");
include_once ("core.common.inc.php");
include_once ("core.db.inc.php");
include_once ("core.contable.inc.php");
include_once ("core.contable.utils.inc.php");

class cReglasDePais {
	private $mValidIDFiscal	= "";
	private $mValido		= false;
	private $mMessages		= ""; 
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
		}
		return $this->mValidIDFiscal;
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
?>