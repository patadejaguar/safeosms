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
/**
 * Reporte de
 *
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package seguimiento
 * @subpackage reports
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
$xRuls		= new cReglaDeNegocio();
$simple		= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_ESTADO_CUENTA_SIMPLE);		//regla de negocio
$simpleF	= $xRuls->getValorPorRegla($xRuls->reglas()->CREDITOS_ESTADO_CUENTA_FSIMPLE);		//regla de negocio
$simpleF	= ($simpleF == true) ? false : true;
//=====================================================================================================
$xHP		= new cHPage("TR.ESTADO_DE_CUENTA ", HP_REPORT);
$xL			= new cSQLListas();
$xF			= new cFecha();
$xQL		= new MQL();
$xFil		= new cSQLFiltros();

//===========  Individual
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
//===========  General
$out 			= parametro("out", SYS_DEFAULT);
$simple			= parametro("simple", $simple, MQL_BOOL);
$FechaInicial	= parametro("on", false, MQL_DATE); $FechaInicial	= parametro("fecha-0", $FechaInicial, MQL_DATE); $FechaInicial = ($FechaInicial == false) ? FECHA_INICIO_OPERACIONES_SISTEMA : $xF->getFechaISO($FechaInicial);
$FechaFinal		= parametro("off", false, MQL_DATE); $FechaFinal	= parametro("fecha-1", $FechaFinal, MQL_DATE); $FechaFinal = ($FechaFinal == false) ? fechasys() : $xF->getFechaISO($FechaFinal);
$jsEvent		= ($out != OUT_EXCEL) ? "initComponents()" : "";
$senders		= getEmails($_REQUEST);

$ByCredito		= $xFil->CreditoPorClave($credito);
$ByPersona		= $xFil->CreditoPorPersona($persona);
$Individual		= ($credito > DEFAULT_CREDITO) ? true : false;

$sql			= $xL->getInicialDeCreditos() . " WHERE `numero_solicitud` > " . DEFAULT_CREDITO . " $ByCredito $ByPersona";

$titulo			= "";
$archivo		= "";

$xRPT			= new cReportes($titulo);
$xRPT->setTitle($xHP->getTitle());
$xRPT->setFile($archivo);
$xRPT->setOut($out);
$xRPT->setSQL($sql);

//============ Reporte
//$xT		= new cTabla($sql, 2);
//$xT->setTipoSalida($out);
$xFMT		= new cFormato();
$xFMT->setOut($out);
if($Individual == true){
	$xCred		= new cCredito($credito);
	if($xCred->init() == true){
		$persona= $xCred->getClaveDePersona();
		$xSoc		= new cSocio($persona);
		if($xSoc->init() == true){
			
			$xRPT->addContent($xSoc->getFicha(true));
			$xRPT->addContent($xCred->getFicha(true, "", $simpleF));
			
			$xProd		= $xCred->getOProductoDeCredito();
			$emulado	= false;		//Estado de Cuenta Emulado
			if($xProd->getOOtrosParametros() !== null){
				$val		= $xProd->getOOtrosParametros()->get($xProd->getOOtrosParametros()->ESTADOCUENTA_EMUL);
				$emulado	= (setNoMenorQueCero($val)>0) ? true : false;
			}
			//$emulado		= true;
			$xRPT->addContent($xFMT->setCreditoParsearEstadoDeCuenta($credito, $simple, $emulado, $xCred->getMontoDeParcialidad()) );
		}	
	}
} else {
	//procesar formato
	$xSoc		= new cSocio($persona);
	if($xSoc->init() == true){
		$xRPT->addContent($xSoc->getFicha(true));
		$rs				= $xQL->getDataRecord($sql);
		foreach ($rs as $rw){
			$credito	= $rw["numero_solicitud"];
			$xCred		= new cCredito($credito);
			if($xCred->init() == true){
				$xProd		= $xCred->getOProductoDeCredito();
				$emulado	= false;		//Estado de Cuenta Emulado
				if($xProd->getOOtrosParametros() !== null){
					$val		= $xProd->getOOtrosParametros()->get($xProd->getOOtrosParametros()->ESTADOCUENTA_EMUL);
					$emulado	= (setNoMenorQueCero($val)>0) ? true : false;
				}
				
				$xRPT->addContent($xFMT->setCreditoParsearEstadoDeCuenta($credito, $simple, $emulado, $xCred->getMontoDeParcialidad()) );
			}			
		}		
	}
}
$body		= $xRPT->getEncabezado($xHP->getTitle(), $FechaInicial, $FechaFinal);
$xRPT->setBodyMail($body);
//$xRPT->addContent($body);

//$xT->setEventKey("jsGoPanel");
//$xT->setKeyField("creditos_solicitud");
//$xRPT->addContent( $xT->Show(  ) );
//============ Agregar HTML
//$xRPT->addContent( $xHP->init($jsEvent) );
//$xRPT->addContent( $xHP->end() );


$xRPT->setResponse();
$xRPT->setSenders($senders);
echo $xRPT->render(true);
?>