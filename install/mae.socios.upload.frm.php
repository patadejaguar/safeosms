<?php
/**
 * @see Modulo de Carga de Respaldos a la Matriz
 * @author Balam Gonzalez Luis Humberto
 * @version 1.1
 * @package common
 *  Actualizacion
 * 		16/04/2008
 *		2008-06-10 Se Agrego la Linea de Informacion del Actualizacion de Movimeintos y recibos
 *
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
//=====================================================================================================

$xHP				= new cHPage("TR.Carga de Personas");
	
ini_set("max_execution_time", 600);
$action 			= ( isset($_GET["o"]) )? $_GET["o"] : false ;
$xHP->init();

$xFRM		= new cHForm("frmSendFiles", "mae.socios.upload.frm.php?o=u");
$xFRM->setEnc("multipart/form-data");

$xBtn		= new cHButton();
$xTxt		= new cHText();
$xDate		= new cHDate();
$xSel		= new cHSelect();
$msg		= "";
$ql			= new MQL();
$xDiv		= new cHDiv();
$xFil		= new cHFile();
$xChk		= new cHCheckBox();

$xImp		= new cFileImporter();



//Si la Operacion es Configurar los Datos
if ( $action == false ){
	
	$xFRM->addHElem( $xFil->getBasic("idarchivo","") );
	$xFRM->addHElem( $xChk->get("TR.Afectar Base de Datos", "idaplicar") );
	
	$xFRM->addSubmit();
	$xFRM->addFootElement('<input type="hidden" name="MAX_FILE_SIZE" value="1024000">');
	

} elseif ( $action ==  "u" ) {
		//cargar datos alfanumericos del estados
		
		$sqlEstados			= " SELECT	`general_estados`.`clave_alfanumerica`,	`general_estados`.`nombre` FROM	`general_estados` `general_estados` ";
		$arrEstados			= $ql->getArrayRecord($sqlEstados);
		
		$sqlEmpresas		= "SELECT	`socios_aeconomica_dependencias`.`idsocios_aeconomica_dependencias`, `socios_aeconomica_dependencias`.`descripcion_dependencia` FROM	`socios_aeconomica_dependencias` `socios_aeconomica_dependencias`";
		$arrEmpresas		= $ql->getArrayRecord($sqlEmpresas);
		
		$usrFiles			= array();
		$usrFiles[0]		= $_FILES["idarchivo"];
		$msg				= "";
		$afectable			= parametro("idaplicar", false, MQL_BOOL);

		$prePath			= PATH_BACKUPS;
		$lim				= 1; //sizeof($usrFiles) -1;
		
		$xF					= new cFecha();
		////Arrays de Control
		$arrTipoIdent		= array( 1 => 1, 4 => 9 );
		$arrEmpresas		= array(
						"PARTICULAR" => DEFAULT_EMPRESA,
						"" => DEFAULT_EMPRESA,
						"#N/D" => DEFAULT_EMPRESA,
						"#N/A" => DEFAULT_EMPRESA,
						"COLOMER" => 120,
						"LAB. DENTAL R Y E" =>101,
						"LAB. DENTAL GML" => 102,
						"REPSSA" => 103,
						"RATTMI" => 104,
						"DELTA" => 105,
						"POLIESSA" => 107,
						"ORCA" => 108,
						"SILCER" => 111,
						"MESSINAS" => 112,
						"FERVAB" => 113,
						"DIVACUN" => 115,
						"CADENITA" => 116,
						"HECCSA" => 117,
						"SAGSA" => 118,
						"MAGRA" => 119,
						"COLOMER" => 120,
						"BORDEX" => 121,
						"SERVICLIMAS" => 123,
						"CORECO" => 124,
						"CMV" => 125,
						"MAYCO" => 126,
						"TABLAROCA" => 127,
						"KAUA" => 129,
						"GONELA" => 130,
						"SVM" => 109,
						"ABIMERHI" => 106,
						"PREVE" => 110,
						"SEY" => 110,
						"CASTALDI" =>132,
						"HINO" => 114,
						"LAMOL" => 99,
						"BICIMAYA" => 131,
						"COUNTRY" => 132,
						"KOHLBERG" => 128,
						"POLIOBRAS" => 134,
						"TZUNCACAB" => 135,
						"GML" => 102,
						"RYE" => 101,
						"OH" => 136,
						"GRUPO NICXA" => 137,
						"SAXON" => 138,
						"EXHIBIT" => 122
						);
		$arrGeneroInv		= array(
						1 => 2,
						2 => 1,
						99 => 99
						);
		$arrGenero		= array(
						"HOMBRE" 	=> 1,
						"MUJER" 	=> 2,
						"NINGUNO" 	=> 99,
						""			=> 99,
						"MASCULINO" => 1,
				"MASCULINA" => 1,
						"FEMENINO"	=> 2,
				"FEMENINA"	=> 2
						);
		$arrFJuridica		= array(
								"PERSONA FISICA" 	=> 1,
								"PERSONA MORAL" 	=> 2,
				
				"FISICA" 	=> 1,
				"MORAL" 	=> 2,
								
				"NATURAL" 	=> 1,
				"JURIDICA" 	=> 2,
								
								""			=> 1,
								"NINGUNO"	=> 99
								);
		$arrEcivil		= array(
				"CASADO" 	=> 1,
				"CASADA" 	=> 1,
						"SOLTERO" 	=> 2,
				"SOLTERA" 	=> 2,
						"NINGUNO" 	=> 99,
						"" 		=> 99,
						"DIVORCIADO" 	=> 3,
				"DIVORCIADA" 	=> 3,
						"UNION LIBRE" 	=> 4,
						"VIUDO" 	=> 6
					);
		$arr2RegMat		= array(
						"" => "NINGUNO",
						"MANCOMUNADO" => "SOCIEDAD_CONYUGAL",
						"SEPARADOS" => "BIENES_SEPARADOS"
					);
		$arrVivienda		= array("PROPIA" =>1, "RENTADA"=>2, "NA"=>99, "NINGUNO" => 99);
	
		$doc1				= (isset($_FILES["idarchivo"])) ? $_FILES["idarchivo"] : false;
		$xFi				= new cFileImporter();

		class cTmp {
			public $SUCURSAL				= 1;
			public $ID_EMPRESA				= 2;
			public $ID_PERSONA				= 3;
			public $TIPO_PERSONA			= 4;
			public $OCUPACION				= 5;
			public $FECHA_ALTA				= 6;
			public $PRIMER_APELLIDO			= 7;
			public $SEGUNDO_APELLIDO		= 8;
			public $NOMBRES					= 9;
			public $FECHA_NACIMIENTO		= 10;
			public $ID_FISCAL				= 11;
			public $ID_POBLACIONAL			= 12;
			public $ESTADO_CIVIL			= 13;
			public $REGIMEN_MATRIMONIAL		= 14;
			public $GENERO					= 15;
			public $TIPO_VIVIENDA			= 16;
			public $DEPENDIENTES_ECONOMICOS	= 17;
			public $TIPO_IDENTIFICACION		= 18;
			public $ID_IDENTIFICACION		= 19;
			public $EMPRESA_TRABAJO			= 20;
			public $PUESTO					= 21;
			public $TRABAJO_FECHA_INGRESO	= 22;
			public $NACIONALIDAD			= 23;
			public $CIUDAD_NACIMIENTO		= 24;
			public $INGRESOS_MENSUALES		= 25;
		}
		//Cedula de Identidad
		$tmp	= new cTmp();
		$xFi->setCharDelimiter("|");
		$xFi->setLimitCampos(26);
		//var_dump($_FILES["f1"]);
		if($xFi->processFile($doc1) == true){
			
			$data				= $xFi->getData();
			$conteo				= 1;
			foreach ($data as $rows){
				if($conteo > 1){
					$xFi->setDataRow($rows);
					
					$xSoc	= new cSocio( false );
				/*$nombre, $apellidopaterno = "", $apellidomaterno = "",
			$rfc = "POR_REGISTRAR", $curp = "POR_REGISTRAR", $cajalocal = 99,
			$fecha_de_nacimiento = false, $lugar_de_nacimiento = "DESCONOCIDO",
			
			$tipo_de_ingreso = FALLBACK_PERSONAS_TIPO_ING, $estado_civil = DEFAULT_ESTADO_CIVIL,
			
			$genero = DEFAULT_GENERO, $dependencia = FALLBACK_CLAVE_EMPRESA, $regimen_conyugal = DEFAULT_REGIMEN_CONYUGAL,
			
			$personalidad_juridica = 1, $grupo_solidario = DEFAULT_GRUPO, $observaciones = "",
			
			$identificado_con = 1, $documento_de_identificacion = "0", $codigo = false, $sucursal = false,
			
			$movil	= "", $correo = "", $dependientes = 0, $fecha = false, $riesgo = AML_PERSONA_BAJO_RIESGO, $clave_fiel = "", 
			$pais = EACP_CLAVE_DE_PAIS
			*/
					$idpersona	= $xFi->getEntero( $tmp->ID_PERSONA);
					if($idpersona >  0){
						$xSoc->setOmitirAML();
						$pass	= $xSoc->add(
							$xFi->getV($tmp->NOMBRES), $xFi->getV($tmp->PRIMER_APELLIDO), $xFi->getV($tmp->SEGUNDO_APELLIDO),
							$xFi->getV($tmp->ID_FISCAL), $xFi->getV($tmp->ID_POBLACIONAL), getCajaLocal(),
							$xFi->getV($tmp->FECHA_NACIMIENTO), $xFi->getV($tmp->CIUDAD_NACIMIENTO),
							
							DEFAULT_TIPO_INGRESO, $xFi->getV($tmp->ESTADO_CIVIL, DEFAULT_ESTADO_CIVIL, MQL_INT, $arrEcivil),
							
							$xFi->getV($tmp->GENERO, DEFAULT_GENERO, MQL_INT, $arrGenero), FALLBACK_CLAVE_EMPRESA, DEFAULT_REGIMEN_CONYUGAL,
							
							
							$xFi->getV($tmp->TIPO_PERSONA, FALLBACK_PERSONAS_FIGURA_JURIDICA, MQL_INT, $arrFJuridica), FALLBACK_CLAVE_DE_GRUPO , "",
							
							FALLBACK_PERSONAS_TIPO_IDENTIFICACION, $xFi->getV($tmp->ID_POBLACIONAL), $idpersona, getSucursal(),
							0, "", 0, $xFi->getV($tmp->FECHA_ALTA)
						);
					//sucess
						if($pass == true){
							$ingreso	= $xFi->getFlotante($tmp->INGRESOS_MENSUALES );
							if($ingreso > 0){
								if( $xSoc->init() == true){
									$xSoc->addActividadEconomica($xFi->getV($tmp->EMPRESA_TRABAJO, ""), $ingreso, $xFi->getV($tmp->OCUPACION, ""));
								}
							}
						}
					}
					$msg		.= $xSoc->getMessages();
				}
				$conteo++;
			}
		}
		$msg		.= $xFi->getMessages();
		if(MODO_DEBUG == true){
			$xLog		= new cFileLog();
			$xLog->setWrite($msg);
			$xFRM->addToolbar( $xLog->getLinkDownload("Archivo del proceso", ""));
		}
	//==================================================================================================================
	/*			fclose ($gestor);
				$xLog->setWrite($msg);
				$xFRM->addToolbar( $xLog->getLinkDownload("Archivo del proceso", ""));
		}	else {
			$xFRM->addAviso("EL TIPO DE ARCHIVO DE " . $usrFiles[0]['name'] . "(" .$mExt . ") NO SE ACEPTA");
		}

	}*/


}
if ( !isset($iReg) ){	$iReg	= 0; }
echo $xFRM->get();


$xHP->fin();

?>