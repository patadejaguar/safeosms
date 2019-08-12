<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 0.0.01
 * @package
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
//=====================================================================================================
$xHP		= new cHPage("TR.Importar PEPS", HP_FORM);
ini_set("max_execution_time", 900);
$DDATA		= $_REQUEST;

$action		= ( isset($DDATA["action"]) ) ? $DDATA["action"] : SYS_CERO;

$tipoingreso	= parametro("tipoingreso", TIPO_INGRESO_PEP, MQL_INT);
$limpiardb		= parametro("idlimpiardb", false, MQL_BOOL);
$tipofuente		= parametro("tipofuente");
$clasex			= parametro("clasex", 0, MQL_INT);
$clasey			= parametro("clasey", 0, MQL_INT);
$clasez			= parametro("clasez", 0, MQL_INT);

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
class cTmp {
	public $INSTITUCION		= 1;
	public $NOMBRE			= 2;
	public $PRIMER_APP		= 3; //NA
	public $SEGUNDO_APP 	= 4;
	public $TELEFONO 		= 5; //NA
	public $TIPO_CONTRATO	= 6; //NA
	public $CARGO 			= 7; //NA
	//8 cargo
	public $DEPTO 			= 9; //NA
	public $REMUNERACION	= 10;//No aplica es clave del puest
	public $PUESTO			= 11;
	public $EXT_TEL			= 15;
	public $MAIL			= 17;
}
/*
 DIRECTORIO DE SERVIDORES PÚBLICOS
Institución,
Nombre,
Primer Apellido,
Segundo Apellido,
Teléfono,
Tipo de Personal,
Nombre del Cargo,
Nombre del Cargo Superior,
Unidad Administrativa,
Clave del Puesto/Remuneración Salarial,
Nombre del Puesto,
Tipo de Vacancia,
Teléfono Directo,
Conmutador,
Extensión,
FAX,
Correo Electrónico, =17
=" ADMINISTRACIÓN FEDERAL DE SERVICIOS EDUCATIVOS EN EL DISTRITO FEDERAL ",=" Vacante",=" Vacante",=" Vacante",=" ",=" Confianza",=" SUBDIRECCIÓN DE EDUCACIÓN FÍSICA",=" DIRECCIÓN TÉCNICA",=" DIRECCIÓN GENERAL DE SERVICIOS EDUCATIVOS IZTAPALAPA.",=" NA2",=" SUBDIRECTOR DE AREA",=" No existe disponibilidad presupuestal para ocuparla",=" ",=" 36018400",=" ",=" ",=" ",
=" ADMINISTRACIÓN FEDERAL DE SERVICIOS EDUCATIVOS EN EL DISTRITO FEDERAL ",=" Selene",=" Orozco",=" Soto",=" ",=" Confianza",=" DEPARTAMENTO DE PLANEACIÓN Y EVALUACIÓN",=" SUBDIRECCIÓN DE PLANEACIÓN EDUCATIVA",=" DIRECCIÓN GENERAL DE PLANEACIÓN  PROGRAMACIÓN Y EVALUACIÓN EDUCATIVA",=" OA1",=" JEFE DE DEPARTAMENTO",="  ",=" ",=" 36018400",=" 21539",=" ",=" selena.orozcos@sepdf.gob.mx",
 */
echo $xHP->init();

$jsb	= new jsBasicForm("frmdocumentos");
//$jxc ->drawJavaScript(false, true);
$ByType	= "";

$xFRM	= new cHForm("frmactividades", "pep-federales.upload.frm.php?action=" . SYS_UNO);
$xFRM->setEnc("multipart/form-data");
$xFRM->setTitle($xHP->getTitle());

$xBtn	= new cHButton();
$xTxt	= new cHText();
$xTxt2	= new cHText();
$xSel	= new cHSelect();
$xF		= new cFecha();
$xT		= new cTipos();
$xLoc	= new cLocal();
$xCache	= new cCache();
$msg	= "";
if($action == SYS_CERO){
	//$xFRM->addHElem("<div class='tx4'><label for='f1'>" . $xFRM->lang("archivo") . "</label><input type='file'  name='f1' id='f1'  /></div>");
	$xFRM->OFile("f1");
	//$xFRM->addHElem( $xTxt2->getDeMoneda("idnumeropagina", $xFRM->lang("numero de", "pagina")) );
	$xFRM->addHElem( $xSel->getListaDeTiposDeIngresoDePersonasGL("tipodeingreso", false, false, SYS_RIESGO_ALTO)->get(true) );
	
	//Agregar Tipo de Fuentes
	$xSel->addOptions(array( "PEPS-POT" => "Directorio POT", 
			"PEPS-SENADORES-XML" => "Senadores en Lista XML", "DIPUTADOS-CSV" => "Diputados en CSV", 
			"CDMX-CONST" => "CDMX Asamblea Constituyente", "CAT-CAND-DIPUTADOS" => "Catalogo de Candidatos a Diputados", "CONGRESO-LOCAL" => "Congresos de los Estados",
			"GOBERNADORES" => "Gobernadores", "MUNICIPALES" => "Presidentes Municipales"
			)
			);
	
	$xFRM->addHElem( $xSel->get("tipofuente", "TR.TIPO_DE FUENTE") );
	
	$xFRM->addHElem( $xSel->getListaDePersonasXClass("classx")->get("TR.CLASE X", true) );
	$xFRM->addHElem( $xSel->getListaDePersonasYClass("classy")->get("TR.CLASE Y", true) );
	$xFRM->addHElem( $xSel->getListaDePersonasZClass("classz")->get("TR.CLASE Z", true) );
	
	
	$xFRM->addHElem( $xTxt->get("idobservaciones", "", "Observaciones") );
	$xFRM->OCheck("TR.LIMPIAR BASE_DE_DATOS", "idlimpiardb");
	$xFRM->addSubmit();
	$xFRM->addFootElement('<input type="hidden" name="MAX_FILE_SIZE" value="1024000">');
	echo $xFRM->get();
} else {
	$doc1				= (isset($_FILES["f1"])) ? $_FILES["f1"] : false;
	$observaciones		= (isset($DDATA["idobservaciones"]) ) ? $DDATA["idobservaciones"] : "";
	
	
	$xFil				= new cFileImporter();
	//$xFil->setCharDelimiter("\",");
	$xFil->setCharDelimiter("|");
	$xTmp				= new cTmp();
	$xFil->setForceClean(true);
	//$xFil->setArrClean(array('/"/', "/=/"));
	$clave_de_actividad	= 9411998;
	//var_dump($_FILES["f1"]);
	if($limpiardb == true){
		//Generar Respaldo
		$xDB		= new cSystemTask();
		$xDB->setBackupDB();
		$xQL		= new MQL();
		$xQL->setRawQuery("DELETE FROM `socios_general` WHERE `codigo`!=". DEFAULT_SOCIO);
		$xQL->setRawQuery("DELETE FROM `socios_vivienda` WHERE `socio_numero` != " . DEFAULT_SOCIO);
		$xQL->setRawQuery("DELETE FROM `socios_relaciones` WHERE `socio_relacionado`!=". DEFAULT_SOCIO);
		$xQL->setRawQuery("TRUNCATE `socios_memo`");
		$xQL->setRawQuery("TRUNCATE `socios_aeconomica`");
	}
	switch ($tipofuente){
		case "PEPS-POT":
	
			if($xFil->processFile($doc1) == true){
				$data				= $xFil->getData();
				$linea				= 0;
				foreach($data as $valores => $cont){
					
					$xFil->setDataRow($cont);
					$xSoc			= new cSocio(false);
					/*$nombre, $apellidopaterno = "", $apellidomaterno = "",
					$rfc = "", $curp = "", $cajalocal = DEFAULT_CAJA_LOCAL,
					$fecha_de_nacimiento = false, $lugar_de_nacimiento = "",
					$tipo_de_ingreso = FALLBACK_PERSONAS_TIPO_ING, $estado_civil = ,
					$genero = , $dependencia = , $regimen_conyugal = ,
					$personalidad_juridica = , $grupo_solidario = , $observaciones = "",
					$identificado_con = 1, $documento_de_identificacion = "0", $codigo = false, $sucursal = false,
					$movil	= "", $correo = "", $dependientes = 0, $fecha = false, $riesgo = AML_PERSONA_BAJO_RIESGO, $clave_fiel = "", 
					$pais = EACP_CLAVE_DE_PAIS, $regimen_fiscal = DEFAULT_REGIMEN_FISCAL*/
					$xSoc->setOmitirAML();
					//var_dump($cont);
					$nnombre		= $xFil->getV($xTmp->NOMBRE);
					$papellido		= $xFil->getV($xTmp->PRIMER_APP);
					$puesto			= $xFil->getV($xTmp->PUESTO);
					$arrPEPS		= array("DIRECTO", "TITUL", "CONSEJER", "COMISION", "ADMINISTRAD", "PRESIDEN", "COORDINAD", "SECRETARI");
					$existe			= false;
					foreach ($arrPEPS as $ix => $cnt){
						if(strpos($puesto, $cnt) !== false){
							$existe	= true;
						}
					}
					if($existe == false){
						$msg		.= "ERROR\tRegistro no Cargado en la Linea $linea ($nnombre $papellido) por su Puesto : $puesto\r\n";
					} else {
						if(trim("$nnombre$papellido") == "" OR $nnombre == "VACANTE" OR $nnombre == "INFORMACION" OR $nnombre == "RESERVADO" OR $nnombre == "INFORMACION RESERVADA"){
							$ready		= false;
							$msg		.= "ERROR\tRegistro no Cargado en la Linea $linea ($nnombre $papellido)\r\n";
						} else {
						$ready	= $xSoc->add($nnombre, $papellido , $xFil->getV($xTmp->SEGUNDO_APP),
								"", "", false, false, "",
								$tipoingreso, DEFAULT_ESTADO_CIVIL,DEFAULT_GENERO,FALLBACK_CLAVE_EMPRESA, DEFAULT_REGIMEN_CONYUGAL, PERSONAS_FIGURA_FISICA, DEFAULT_GRUPO, "",
								1,0, false, false, $xFil->getEntero($xTmp->TELEFONO), $xFil->cleanMail($xFil->getV( $xTmp->MAIL )) );
						}
						//var_dump($ready);
						if($ready == true){
							$xSoc->init();
							//$xSoc->setIDInterno($id)
							$xAct	= new cPersonaActividadEconomica($xSoc->getCodigo());
							//$clave_de_actividad, $ingreso, $antiguedad = DEFAULT_TIEMPO, $nombrecomercial = "", 
							//$codigo_postal = 0, $telefono = 0, $idlocalidad = 0, $nombrelocalidad = "", $nombremunicipio = "", $nombreestado = ""
							$empresa	= $xFil->getV($xTmp->INSTITUCION);
							$xAct->setEmpresa(DEFAULT_EMPRESA, $puesto, $xFil->getV($xTmp->DEPTO));
							$xAct->add($clave_de_actividad, 1, DEFAULT_TIEMPO, $empresa, $xLoc->DomicilioCodigoPostal(), $xFil->getV($xTmp->TELEFONO), $xLoc->DomicilioLocalidadClave());
							$xSoc->setClasificacionesExtras($clasex, $clasey, $clasez);
							//$msg	.= $xAct->getMessages();
							//$msg	.= $xSoc->getMessages();
						}
						//$xSoc->addActividadEconomica($xFil->getV($indice), $ingreso)
					}
					$linea++;
				}
			}
			$msg			.= $xFil->getMessages();
		break;
		case "PEPS-SENADORES-XML":
			$xFil->setType($xFil->TIPO_XML);
			$xFil->setToUTF8();
			$xClean			= new cTiposLimpiadores();

			if($xFil->setSaveFile($doc1) == true){
				//$gestor = @fopen($xFil->getCompletePath(), "r");
				$data		= json_decode(json_encode(simplexml_load_file($xFil->getCompletePath())), true);
				$data		= $data["Dato"];
				foreach ($data as $idx => $cnt){
					$dd			= $cnt["@attributes"];
					$nombre		= setCadenaVal($dd["nombre"]);
					$ddapp		= $xClean->cleanApellidos(setCadenaVal($dd["apellidos"]));
					$papellido	= $ddapp[0];
					$sapellido	= $ddapp[1];
					/*
					$papp		= explode(" ", $dd["apellidos"], 2);
					
					$papellido	= $papp[0];
					$sapellido	= (isset($papp[1])) ? $papp[1] : "";
					$papellido	= setCadenaVal($papellido);
					$sapellido	= setCadenaVal($sapellido);*/
					$telefono	= setNoMenorQueCero($dd["telefono"],0);
					$email		= $xFil->cleanMail($dd["correo"]);
					$fraccion	= setCadenaVal($dd["fraccion"]);
					//{"nombre":"Ang\u00e9lica del Rosario","apellidos":"Araujo Lara",
					//"fraccion":"PRI","telefono":"53-45-30-00",
					//"extension":"3036","piso":"04","oficina":"06",
					//"correo":"angelica.araujo@senado.gob.mx"}
					
					$xSoc	= new cSocio(false);
					$xSoc->setOmitirAML(true);
					$ready	= $xSoc->add($nombre, $papellido , $sapellido,
							"", "", false, false, "",
							$tipoingreso, DEFAULT_ESTADO_CIVIL,DEFAULT_GENERO,FALLBACK_CLAVE_EMPRESA, DEFAULT_REGIMEN_CONYUGAL, PERSONAS_FIGURA_FISICA, DEFAULT_GRUPO, "",
							1,0, false, false, $telefono, $email );
					if($ready == true){
						$msg	.= "OK\tSe agrego al Senador $nombre $papellido $sapellido\r\n";
						$xSoc->init();
						//$xSoc->setIDInterno("PEPS-$idx");
						$xAct	= new cPersonaActividadEconomica($xSoc->getCodigo());
						//$clave_de_actividad, $ingreso, $antiguedad = DEFAULT_TIEMPO, $nombrecomercial = "",
						//$codigo_postal = 0, $telefono = 0, $idlocalidad = 0, $nombrelocalidad = "", $nombremunicipio = "", $nombreestado = ""
						$empresa			= "SENADO DE LA REPUBLICA";
						$puesto				= "SENADOR DEL $fraccion";
						$clave_de_actividad	= "9800113";
						
						$xAct->setEmpresa(DEFAULT_EMPRESA, $puesto, $fraccion);
						$xAct->add($clave_de_actividad, 1, DEFAULT_TIEMPO, $empresa, $xLoc->DomicilioCodigoPostal(), $telefono, $xLoc->DomicilioLocalidadClave());
						$xSoc->setClasificacionesExtras($clasex, $clasey, $clasez);
						//$msg	.= $xAct->getMessages();
						//$msg	.= $xSoc->getMessages();
					}
				}
			}
			
			//$cdata		= json_decode(json_encode(simplexml_load_string($data2)), true);
		break;
		case "DIPUTADOS-CSV":
			$xFil->setType($xFil->TIPO_CSV);
			//$xFil->setArrClean(array("LICENCIA", "(", ")", "Diputado"));
			
			//$xFil->setCharDelimiter("|");
			$xImp		= new cCoreImport();
			if($xFil->processFile($doc1) == true){
				$data				= $xFil->getData();
				$linea				= 0;
				foreach($data as $valores => $cont){
					$xFil->setDataRow($cont);
					$xSoc			= new cSocio(false);
					$ddn			= explode(" ", $cont[0], 2);
					$nc				= setCadenaVal($ddn[1]);//Limpiar acentos
					$nc				= str_replace("LICENCIA", "", $nc);
					$nc				= str_replace("(", "", $nc);
					$nc				= str_replace(")", "", $nc);
					$nc				= str_replace("Diputado", "", $nc);
					$xImp->setNombreCompleto($nc, true);
					$telefono		= "";
					$email			= "";
					$partido		= setCadenaVal($cont[3]);
					$estado		= setCadenaVal($cont[1]);
					$distrito		= setCadenaVal($cont[2]);
					//Diputado|Entidad=1|Distrito / Circunscripción=2|Partido=3
					$idlocalidad	= 1001002;
					$codigopostal	= 15620;
					$des			= $xCache->get("data-idcoles-$estado");
					if(!is_array($des)){
						$xQL		= new MQL();
						$des		= $xQL->getDataRow("SELECT * FROM `tmp_colonias_activas` WHERE `nombre_estado` LIKE '%$estado%' =  LIMIT 0,1");
					}
					if(isset($des["codigo_de_estado"])){
						$codigopostal	= $des["codigo_postal"];
						$idlocalidad	= $des["idlocalidad"];
						$xCache->set("data-idcoles-$estado", $des);
					}
					
					$xSoc	= new cSocio(false);
					$xSoc->setOmitirAML(true);
					$ready	= $xSoc->add($xImp->getNombre(), $xImp->getPrimerAp() , $xImp->getSegundoAp(),
							"", "", false, false, "",
							$tipoingreso, DEFAULT_ESTADO_CIVIL,DEFAULT_GENERO,FALLBACK_CLAVE_EMPRESA, DEFAULT_REGIMEN_CONYUGAL, PERSONAS_FIGURA_FISICA, DEFAULT_GRUPO, "",
							1,0, false, false, $telefono, $email );
					if($ready == true){
						$msg	.= "OK\tSe agrego al Diputado " . $xImp->getNombre() . " " . $xImp->getPrimerAp() .  "\r\n";
						$xSoc->init();
						$xAct	= new cPersonaActividadEconomica($xSoc->getCodigo());
						//$clave_de_actividad, $ingreso, $antiguedad = DEFAULT_TIEMPO, $nombrecomercial = "",
						//$codigo_postal = 0, $telefono = 0, $idlocalidad = 0, $nombrelocalidad = "", $nombremunicipio = "", $nombreestado = ""
						$empresa			= "CAMARA DE DIPUTADOS";
						$puesto				= "DIPUTADO POR $estado";
						$clave_de_actividad	= "9800112";
					
						$xAct->setEmpresa(DEFAULT_EMPRESA, $puesto, "$partido - $distrito");
						$xAct->add($clave_de_actividad, 1, DEFAULT_TIEMPO, $empresa, $codigopostal, $telefono, $idlocalidad);
						$xSoc->setClasificacionesExtras($clasex, $clasey, $clasez);
						//$msg	.= $xAct->getMessages();
						//$msg	.= $xSoc->getMessages();
					}
				}
			}
		break;
		case "CDMX-CONST":
			$xFil->setType($xFil->TIPO_CSV);
			//$xFil->setArrClean(array("LICENCIA", "(", ")", "Diputado"));
				
			//$xFil->setCharDelimiter("|");
			$xImp		= new cCoreImport();
			if($xFil->processFile($doc1) == true){
				$data				= $xFil->getData();
				$linea				= 0;
				foreach($data as $valores => $cont){
					$xFil->setDataRow($cont);
					//V|del 17 de septiembre de 2009 al 16 de septiembre de 2012|Emiliano|Aguilar|Esquivel|Partido Revolucionario Institucional|Plurinominal||41|Mexicana|Agrónomo
					//Legislatura Vigente|Periodo|Nombre(s)|Apellido Paterno|Apellido Materno|Fracción|Distrito Electoral|Fotografía|Edad|Nacionalidad|Escolaridad
					if(isset($cont[2])){
						$legislatura	= setCadenaVal($cont[0]);
						$nombre			= setCadenaVal($cont[2]);
						$papellido		= setCadenaVal($cont[3]);
						$sapellido		= setCadenaVal($cont[4]);
						$telefono		= "";
						$email			= "";
						$partido		= setCadenaVal($cont[5]);
						$distrito		= setCadenaVal($cont[6]);
						
	
							
						$idlocalidad	= 1001002;
						$codigopostal	= 15620;
							
						$xSoc	= new cSocio(false);
						if($nombre !== "" AND $papellido !== ""){
							$xSoc->setOmitirAML(true);
							$ready	= $xSoc->add($nombre, $papellido , $sapellido,
									"", "", false, false, "",
									$tipoingreso, DEFAULT_ESTADO_CIVIL,DEFAULT_GENERO,FALLBACK_CLAVE_EMPRESA, DEFAULT_REGIMEN_CONYUGAL, PERSONAS_FIGURA_FISICA, DEFAULT_GRUPO, "",
									1,0, false, false, $telefono, $email );
							if($ready == true){
								$msg	.= "OK\tSe agrego al Asambleista Constituyente $nombre $papellido $sapellido\r\n";
								$xSoc->init();
								$xAct	= new cPersonaActividadEconomica($xSoc->getCodigo());
								$empresa			= "CIUDAD DE MEXICO";
								$puesto				= "ASAMBLEA CONSTITUYENTE CDMX";
								$xAct->setEmpresa(DEFAULT_EMPRESA, $puesto, "$partido - $distrito");
								$xAct->add($clave_de_actividad, 1, DEFAULT_TIEMPO, $empresa, $codigopostal, $telefono, $xLoc->DomicilioLocalidadClave());
								$xSoc->setClasificacionesExtras($clasex, $clasey, $clasez);
								//$msg	.= $xAct->getMessages();
								//$msg	.= $xSoc->getMessages();
							}
						}
					}
				}
			}
		break;
		case "CAT-CAND-DIPUTADOS":
			
			$xFil->setType($xFil->TIPO_CSV);
			//$xFil->setArrClean(array("LICENCIA", "(", ")", "Diputado"));
			
			//$xFil->setCharDelimiter("|");
			$xImp		= new cCoreImport();
			$xCache		= new cCache();
			if($xFil->processFile($doc1) == true){
				$data				= $xFil->getData();
				$linea				= 0;
				$xImp				= new cCoreImport();
				
				foreach($data as $valores => $cont){
					$xFil->setDataRow($cont);
					//1|1|PAN|GERARDO FEDERICO SALAS DIAZ|J. ROBERTO ZAPATA GUERRA
					//ESTADO|DISTRITO|PARTIDO|CANDIDATO_PROPIETARIO|CANDIDATO_SUPLENTE
					if(isset($cont[2])){
						$estado			= setNoMenorQueCero($cont[0]);
						if($estado >0){
							$dn1			= setCadenaVal($cont[3]);
							$dn2			= setCadenaVal($cont[4]);
							$xImp->setNombreCompleto($dn1);
							$nombre			= $xImp->getNombre();
							$papellido		= $xImp->getPrimerAp();
							$sapellido		= $xImp->getSegundoAp();
							
							$xImp->setNombreCompleto($dn2);
							$nombre2		= $xImp->getNombre();
							$papellido2		= $xImp->getPrimerAp();
							$sapellido2		= $xImp->getSegundoAp();
							
							$telefono		= "";
							$email			= "";
							$partido		= setCadenaVal($cont[2]);
							$distrito		= setCadenaVal($cont[1]);
							
							$idlocalidad	= 1001002;
							$codigopostal	= 15620;
							$idactividad	= "9800112";
							$des			= $xCache->get("data-idcol-$estado");
							$nestado		= $estado;
							if(!is_array($des)){
								$xQL		= new MQL();
								$des		= $xQL->getDataRow("SELECT * FROM `tmp_colonias_activas` WHERE `codigo_de_estado`=$estado LIMIT 0,1");
							}
							if(isset($des["codigo_de_estado"])){
								$codigopostal	= $des["codigo_postal"];
								$idlocalidad	= $des["idlocalidad"];
								$nestado		= $des["nombre_estado"];
								$xCache->set("data-idcol-$estado", $des);
							}
						
							$xSoc			= new cSocio(false);
							$xSoc->setOmitirAML(true);
							if($nombre !== "" AND $papellido !== ""){
								
								$ready	= $xSoc->add($nombre, $papellido , $sapellido,
										"", "", false, false, "",
										$tipoingreso, DEFAULT_ESTADO_CIVIL,DEFAULT_GENERO,FALLBACK_CLAVE_EMPRESA, DEFAULT_REGIMEN_CONYUGAL, PERSONAS_FIGURA_FISICA, DEFAULT_GRUPO, "",
										1,0, false, false, $telefono, $email );
								if($ready == true){
									$msg	.= "OK\tSe agrego al Candidato a Diputado Propietario $nombre $papellido $sapellido\r\n";
									$xSoc->init();
									$xAct	= new cPersonaActividadEconomica($xSoc->getCodigo());
									$empresa			= $nestado;
									$puesto				= "CANDIDATO A DIP. FEDERAL PROP.";
									$xAct->setEmpresa(DEFAULT_EMPRESA, $puesto, "$partido - $distrito");
									$xAct->add($idactividad, 1, DEFAULT_TIEMPO, $empresa, $codigopostal, $telefono, $idlocalidad);
									$xSoc->setClasificacionesExtras($clasex, $clasey, $clasez);
									//$msg	.= $xAct->getMessages();
									//$msg	.= $xSoc->getMessages();
								}
							}
							//Suplente
							$xSoc			= new cSocio(false);
							$xSoc->setOmitirAML(true);
							if($nombre2 !== "" AND $papellido2 !== ""){
							
								$ready	= $xSoc->add($nombre2, $papellido2 , $sapellido2,
										"", "", false, false, "",
										$tipoingreso, DEFAULT_ESTADO_CIVIL,DEFAULT_GENERO,FALLBACK_CLAVE_EMPRESA, DEFAULT_REGIMEN_CONYUGAL, PERSONAS_FIGURA_FISICA, DEFAULT_GRUPO, "",
										1,0, false, false, $telefono, $email );
								if($ready == true){
									$msg	.= "OK\tSe agrego al Candidato a Diputado Suplente $nombre $papellido $sapellido\r\n";
									$xSoc->init();
									$xAct	= new cPersonaActividadEconomica($xSoc->getCodigo());
									$empresa			= $estado;
									$puesto				= "CANDIDATO A DIP. FEDERAL SUPLENTE";
									$xAct->setEmpresa(DEFAULT_EMPRESA, $puesto, "$partido - $distrito");
									$xAct->add($idactividad, 1, DEFAULT_TIEMPO, $empresa, $codigopostal, $telefono, $idlocalidad);
									$xSoc->setClasificacionesExtras($clasex, $clasey, $clasez);
									//$msg	.= $xAct->getMessages();
									//$msg	.= $xSoc->getMessages();
								}
							}
						}
					}
				}
			}
			break;
		case "CONGRESO-LOCAL":
			$xFil->setType($xFil->TIPO_CSV);
			//$xFil->setArrClean(array("LICENCIA", "(", ")", "Diputado"));
			$xCache		= new cCache();
			//$xFil->setCharDelimiter("|");
			$xImp		= new cCoreImport();
			$xClean		= new cTiposLimpiadores();
			if($xFil->processFile($doc1) == true){
				$data				= $xFil->getData();
				$linea				= 0;
				foreach($data as $valores => $cont){
					$xFil->setDataRow($cont);
					//NOMBRE|Distrito|Legislatura|Estado|Afiliacion|email|Telefono|Observaciones
					//DANIEL JESÚS GRANJA PENICHE|Distrito I|LXI Legislatura|Yucatán||||
					
					if(isset($cont[3])){
						$nc				= setCadenaVal($cont[0]);
						$xImp->setNombreCompleto($nc);
						
						$nombre			= $xImp->getNombre();
						$papellido		= $xImp->getPrimerAp();
						$sapellido		= $xImp->getSegundoAp();
						$telefono		= setNoMenorQueCero($cont[6]);
						$email			= $xFil->cleanMail($cont[5]);
						$partido		= setCadenaVal($cont[4]);
						$distrito		= setCadenaVal($cont[1]);
						$estado			= setCadenaVal($cont[3]);
						$legislatura	= setCadenaVal($cont[2]);
						
						$idlocalidad	= 1001002;
						$codigopostal	= 15620;
						$idactividad	= 9800204;
						$des			= $xCache->get("data-idcoles-$estado");
						if(!is_array($des)){
							$xQL		= new MQL();
							$des		= $xQL->getDataRow("SELECT * FROM `tmp_colonias_activas` WHERE `nombre_estado` LIKE '%$estado%' =  LIMIT 0,1");
							
						}
						if(isset($des["codigo_de_estado"])){
							$codigopostal	= $des["codigo_postal"];
							$idlocalidad	= $des["idlocalidad"];
							$xCache->set("data-idcoles-$estado", $des);
						}
						$xSoc	= new cSocio(false);
						//if($nombre !== "" AND $papellido !== ""){
							$xSoc->setOmitirAML(true);
							$ready	= $xSoc->add($nombre, $papellido , $sapellido,
									"", "", false, false, "",
									$tipoingreso, DEFAULT_ESTADO_CIVIL,DEFAULT_GENERO,FALLBACK_CLAVE_EMPRESA, DEFAULT_REGIMEN_CONYUGAL, PERSONAS_FIGURA_FISICA, DEFAULT_GRUPO, "",
									1,0, false, false, $telefono, $email );
							if($ready == true){
								$msg	.= "OK\tSe agrego al Diputado LOCAL $nombre $papellido $sapellido\r\n";
								$xSoc->init();
								$xAct	= new cPersonaActividadEconomica($xSoc->getCodigo());
								$empresa			= $estado;
								$puesto				= "INTEGRANTE DEL CONGRESO LOCAL";
								$xAct->setEmpresa(DEFAULT_EMPRESA, $puesto, "$partido - $distrito");
								$xAct->add($idactividad, 1, DEFAULT_TIEMPO, $empresa, $codigopostal, $telefono, $idlocalidad);
								$xSoc->setClasificacionesExtras($clasex, $clasey, $clasez);
								//$msg	.= $xAct->getMessages();
								//$msg	.= $xSoc->getMessages();
							}
						//}
					}
				}
			}
			break;
			case "GOBERNADORES":
				$xFil->setType($xFil->TIPO_CSV);
				//$xFil->setArrClean(array("LICENCIA", "(", ")", "Diputado"));
				$xCache		= new cCache();
				//$xFil->setCharDelimiter("|");
				$xImp		= new cCoreImport();
				$xClean		= new cTiposLimpiadores();
				if($xFil->processFile($doc1) == true){
					$data				= $xFil->getData();
					$linea				= 0;
					foreach($data as $valores => $cont){
						$xFil->setDataRow($cont);
						//ESTADO|GOBERNADOR|PERIODO|PARTIDO
						//Aguascalientes|Carlos Lozano de la Torre|1 de diciembre de 2010-30 de noviembre de 2016|PRI
							
						if(isset($cont[1])){
							$nc				= setCadenaVal($cont[1]);
							$xImp->setNombreCompleto($nc);
			
							$nombre			= $xImp->getNombre();
							$papellido		= $xImp->getPrimerAp();
							$sapellido		= $xImp->getSegundoAp();
							$telefono		= "";
							$email			= "";
							$partido		= setCadenaVal($cont[3]);
							
							$estado			= setCadenaVal($cont[0]);
							
			
							$idlocalidad	= 1001002;
							$codigopostal	= 15620;
							$idactividad	= 9800204;
							$des			= $xCache->get("data-idcoles-$estado");
							if(!is_array($des)){
								$xQL		= new MQL();
								$des		= $xQL->getDataRow("SELECT * FROM `tmp_colonias_activas` WHERE `nombre_estado` LIKE '%$estado%' =  LIMIT 0,1");
							}
							if(isset($des["codigo_de_estado"])){
								$codigopostal	= $des["codigo_postal"];
								$idlocalidad	= $des["idlocalidad"];
								$xCache->set("data-idcoles-$estado", $des);
							}
			
								
			
								
							$xSoc	= new cSocio(false);
							//if($nombre !== "" AND $papellido !== ""){
								$xSoc->setOmitirAML(true);
								$ready	= $xSoc->add($nombre, $papellido , $sapellido,
										"", "", false, false, "",
										$tipoingreso, DEFAULT_ESTADO_CIVIL,DEFAULT_GENERO,FALLBACK_CLAVE_EMPRESA, DEFAULT_REGIMEN_CONYUGAL, PERSONAS_FIGURA_FISICA, DEFAULT_GRUPO, "",
										1,0, false, false, $telefono, $email );
								if($ready == true){
									$msg	.= "OK\tSe agrego el Gobernador $nombre $papellido $sapellido\r\n";
									$xSoc->init();
									$xAct	= new cPersonaActividadEconomica($xSoc->getCodigo());
									$empresa			= "$estado";
									$puesto				= "GOBERNADOR CONSTITUCIONAL";
									$depto				= $partido;
									$xAct->setEmpresa(DEFAULT_EMPRESA, $puesto, $depto);
									$xAct->add($idactividad, 1, DEFAULT_TIEMPO, $empresa, $codigopostal, $telefono, $idlocalidad);
									$xSoc->setClasificacionesExtras($clasex, $clasey, $clasez);
									//$msg	.= $xAct->getMessages();
									//$msg	.= $xSoc->getMessages();
								} else {
									$msg	.= "ERROR\Al agregar al Gobernador $nombre $papellido $sapellido\r\n";
								}
							//}
						} else  {
							$msg	.= "ERROR\Al agregar al Gobernador " . $cont[1] . "\r\n";
						}
					}
				}
				break;
				case "MUNICIPALES":
					$xFil->setType($xFil->TIPO_CSV);
					//$xFil->setArrClean(array("LICENCIA", "(", ")", "Diputado"));
					$xCache		= new cCache();
					//$xFil->setCharDelimiter("|");
					$xImp		= new cCoreImport();
					$xClean		= new cTiposLimpiadores();
					if($xFil->processFile($doc1) == true){
						$data				= $xFil->getData();
						$linea				= 0;
						foreach($data as $valores => $cont){
							$xFil->setDataRow($cont);
							//Nombre=0|Municipio=1|Estado=2|Direccion=3|telefono=4|email=5|periodo=6|afiliacion=7
							//L.A.E. Antonio Cruz de la Torre Ruvalcaba|Acatic|Jalisco|Hidalgo No. 24 C.P. 45470, Acatic, Jalisco||acatic@jalisco.gob.mx|2015-2018|PAN
								
							if(isset($cont[2])){
								$nc				= setCadenaVal($cont[0]);
								$xImp->setNombreCompleto($nc);
									
								$nombre			= $xImp->getNombre();
								$papellido		= $xImp->getPrimerAp();
								$sapellido		= $xImp->getSegundoAp();
								$telefono		= setNoMenorQueCero($cont[3]);
								$email			= "";
								$partido		= setCadenaVal($cont[7]);
									
								$estado			= setCadenaVal($cont[2]);
								$municipio		= setCadenaVal($cont[1]);
									
								$idlocalidad	= 1001002;
								$codigopostal	= 15620;
								$idactividad	= 9800301;
								$des			= $xCache->get("data-idcolmun-$estado-$municipio");
								if(!is_array($des)){
									$xQL		= new MQL();
									$des		= $xQL->getDataRow("SELECT * FROM `tmp_colonias_activas` WHERE `nombre_estado` LIKE '%$estado%' AND `nombre_municipio` LIKE '%$municipio%' =  LIMIT 0,1");
									
								}
								if(isset($des["codigo_de_estado"])){
									$codigopostal	= $des["codigo_postal"];
									$idlocalidad	= $des["idlocalidad"];
									$xCache->set("data-idcolmun-$estado-$municipio", $des);
								}
									
				
									
				
								$xSoc	= new cSocio(false);
								//if($nombre !== "" AND $papellido !== ""){
								$xSoc->setOmitirAML(true);
								$ready	= $xSoc->add($nombre, $papellido , $sapellido,
										"", "", false, false, "",
										$tipoingreso, DEFAULT_ESTADO_CIVIL,DEFAULT_GENERO,FALLBACK_CLAVE_EMPRESA, DEFAULT_REGIMEN_CONYUGAL, PERSONAS_FIGURA_FISICA, DEFAULT_GRUPO, "",
										1,0, false, false, $telefono, $email );
								if($ready == true){
									$msg	.= "OK\tSe agrego el Gobernador $nombre $papellido $sapellido\r\n";
									$xSoc->init();
									$xAct	= new cPersonaActividadEconomica($xSoc->getCodigo());
									$empresa			= "$estado - $municipio";
									$puesto				= "PRESIDENTE MUNICIPAL";
									$depto				= $partido;
									$xAct->setEmpresa(DEFAULT_EMPRESA, $puesto, $depto);
									$xAct->add($idactividad, 1, DEFAULT_TIEMPO, $empresa, $codigopostal, $telefono, $idlocalidad);
									$xSoc->setClasificacionesExtras($clasex, $clasey, $clasez);
									//$msg	.= $xAct->getMessages();
									//$msg	.= $xSoc->getMessages();
								} else {
									$msg	.= "ERROR\Al agregar al Presidente Municipal $nombre $papellido $sapellido\r\n";
								}
								//}
							} else  {
								$msg	.= "ERROR\Al agregar al Presidente Municipal " . $cont[1] . "\r\n";
							}
						}
					}
					break;
	}
	
	//Diputado federal 
	//Diputado Estatal 9800204
	//Presidente Municipal 9800301
	
	if(MODO_DEBUG == true){
		$xFRM->addLog($msg);
	} else {
		echo JS_CLOSE;
	}
	echo $xFRM->get();
}

//$jsb->show();
?>
<!-- HTML content -->
<script>
</script>
<?php
$xHP->fin();
?>