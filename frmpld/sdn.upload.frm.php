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
ini_set("max_execution_time", 600);
	
$xHP		= new cHPage("TR.CARGA LISTA OFACS", HP_FORM);
$xQL		= new MQL();
$DDATA		= $_REQUEST;
$action		= ( isset($DDATA["action"]) ) ? $DDATA["action"] : SYS_CERO;


$tipoingreso	= parametro("tipoingreso", TIPO_INGRESO_SDN, MQL_INT);
//$limpiardb		= parametro("idlimpiardb", false, MQL_BOOL);
//$tipofuente		= parametro("tipofuente");
$clasex			= parametro("clasex", 0, MQL_INT);
$clasey			= parametro("clasey", 0, MQL_INT);
$clasez			= parametro("clasez", 0, MQL_INT);

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

echo $xHP->getHeader();

$jsb	= new jsBasicForm("frmdocumentos");
//$jxc ->drawJavaScript(false, true);
$ByType	= "";
echo $xHP->setBodyinit();

$xFRM	= new cHForm("frmsdn", "sdn.upload.frm.php?action=" . SYS_UNO);
$xFRM->setEnc("multipart/form-data");
$xFRM->setTitle($xHP->getTitle());

$arrOps	= array(
		"ADR" => $xHP->lang("domicilio") . "(ADD.PIP)", 
		"SDN" => $xHP->lang("persona") . "(SDN.PIP)", 
		"ALT" => $xHP->lang("alias") . "(ALT.PIP)"
		);

$xBtn	= new cHButton();
$xTxt	= new cHText();
$xTxt2	= new cHText();
$xSel	= new cHSelect("idtipoimportacion", $arrOps);
$xF	= new cFecha();
$xT	= new cTipos();



$msg	= "";
if($action == SYS_CERO){
	$xFRM->addHElem("<div class='tx4'><label for='f1'>" . $xFRM->lang("archivo") . "</label><input type='file'  name='f1' id='f1'  /></div>");
	$xFRM->addHElem( $xSel->get("", "TR.tipo de importacion") );
	
	$xFRM->addHElem( $xSel->getListaDePersonasXClass("classx")->get("TR.CLASE X", true) );
	$xFRM->addHElem( $xSel->getListaDePersonasYClass("classy")->get("TR.CLASE Y", true) );
	$xFRM->addHElem( $xSel->getListaDePersonasZClass("classz")->get("TR.CLASE Z", true) );
	
	//$xFRM->addHElem( $xTxt2->getDeMoneda("idnumeropagina", $xFRM->lang("numero de", "pagina")) );
	$xFRM->addHElem( $xTxt->get("idobservaciones", "", "Observaciones") );
	$xFRM->OCheck("TR.LIMPIAR BASE_DE_DATOS", "idlimpiardb");
	$xFRM->addSubmit();
	$xFRM->addFootElement('<input type="hidden" name="MAX_FILE_SIZE" value="1024000">');
	echo $xFRM->get();
} else {
	$doc1				= (isset($_FILES["f1"])) ? $_FILES["f1"] : false;
	$observaciones		= (isset($DDATA["idobservaciones"]) ) ? $DDATA["idobservaciones"] : "";
	$tipoimportacion	= (isset($DDATA["idtipoimportacion"]) ) ? $DDATA["idtipoimportacion"] : "SDN";
	$limpiardb			= parametro("idlimpiardb", false, MQL_BOOL);
	$xFil				= new cFileImporter();
	$limit				= ($tipoimportacion == "SDN") ? 12 : 6;
	$xFil->setLimitCampos($limit);
	$xFil->setCharDelimiter("|");
	$xFil->setType("PIP");
	//var_dump($_FILES["f1"]);
	if($xFil->processFile($doc1) == true){
	$data			= $xFil->getData();
	if($limpiardb == true){
		$xDB		= new cSystemTask();
		$xDB->setBackupDB();
				
		$xQL		= new MQL();
		$xQL->setRawQuery("DELETE FROM `socios_general` WHERE `codigo`!=". DEFAULT_SOCIO);
		$xQL->setRawQuery("DELETE FROM `socios_vivienda` WHERE `socio_numero` != " . DEFAULT_SOCIO);
		$xQL->setRawQuery("DELETE FROM `socios_relaciones` WHERE `socio_relacionado`!=". DEFAULT_SOCIO);
		$xQL->setRawQuery("TRUNCATE `socios_memo`");
		$xQL->setRawQuery("TRUNCATE `socios_aeconomica`");
	}
		foreach($data as $valores => $cont){
			$id	= $xT->cInt($cont[0]);
			switch($tipoimportacion){
				case "SDN":
					$xFil->setDataRow($cont);
					/*
4149|"CAVIEDES CRUZ, Leonardo"|"individual"|"SDNT"|
-5  - 6  - 7  - 8  -  9 - 10 - 11
-0- |-0- |-0- |-0- |-0- |-0- |-0-
-12 
|"DOB 23 Nov 1952; Cedula No. 16593470 (Colombia); Passport AB151486 (Colombia); alt. Passport AC444270 (Colombia); alt. Passport OC444290 (Colombia)."
536|"CIMEX IBERICA"|-0- |"CUBA"|-0- |-0- |-0- |-0- |-0- |-0- |-0- |-0-  
					 * */
				$tipo		= trim($xT->cChar( str_replace('"', "", $cont[2] ) ));
				$nombres	= strtoupper( str_replace('"', "", $cont[1] ) );
				if($id > 0){
					if($tipo == "individual" OR $tipo == "-0-"){
						$xSoc		= new cSocio($id);
						$programa	= strtoupper( str_replace('"', "", $cont[3] ) );
						$idpoblacion	= "$programa-$id";
						
						$apellido1		= "";
						$apellido2		= "";
						$sitioweb		= "";
						$email			= "";
						$pobservaciones	= trim($xFil->cleanString($xFil->getV(12) ));
						$pobservaciones	= ($pobservaciones == "-0-") ? "" : $pobservaciones; 
						$figura			= FALLBACK_PERSONAS_FIGURA_JURIDICA;
						if($tipo == "-0-"){
							//$nombres	= $nombres;
							$figura		= PERSONAS_FIGURA_MORAL;
						} else {
							$DNom		= explode(",", $nombres, 2);
							$nombres	= isset($DNom[1]) ? $DNom[1] : "";
							$apellidos	= explode(" ", $DNom[0], 2);
							$apellido1	= isset($apellidos[0])  ? $apellidos[0] : "";
							$apellido2	= isset($apellidos[1])  ? $apellidos[1] : "";
						}
						if(setNoMenorQueCero($id) > DEFAULT_SOCIO){	if($xSoc->existe() == true ){ $xSoc->setDeleteSocio(); }	}
						$trexOb			= explode(";", $pobservaciones);
						if(count($trexOb)>=2){
							foreach ($trexOb as $idx => $xcnt){
								if(strpos($xcnt, "WEBSITE") !== false){
									$xcnt	= str_replace("WEBSITE", "", $xcnt);
									$xcnt	= trim($xcnt);
									$sitioweb	= $xcnt;
									$xcnt		= "";
								}
								if(strpos($xcnt, "EMAIL ADDRESS") !== false){
									$xcnt	= str_replace("EMAIL ADDRESS", "", $xcnt);
									$xcnt	= trim($xcnt);
									$email	= $xcnt;
									$xcnt	= "";
								}								
								$pobservaciones	= $xcnt;
							}
						}				
						$nombres	= trim($nombres);
						$xSoc->setOmitirAML();
						$xSoc->add($nombres, $apellido1, $apellido2, $idpoblacion, $idpoblacion, getCajaLocal(), false,
							   $programa, TIPO_INGRESO_SDN, DEFAULT_ESTADO_CIVIL, DEFAULT_GENERO, FALLBACK_CLAVE_EMPRESA,
								DEFAULT_REGIMEN_CONYUGAL, $figura, DEFAULT_GRUPO, $pobservaciones,
								FALLBACK_PERSONAS_TIPO_IDENTIFICACION, "0", false, false,
								$email, "0", 0, false, AML_PERSONA_ALTO_RIESGO);
						$xSoc->setClasificacionesExtras($clasex, $clasey, $clasez);
						
						if($sitioweb != ""){
							$xSoc->setSitioWeb($sitioweb);
						}
						$msg .=  $xSoc->getMessages(OUT_TXT);
					} else {
						$msg .= "WARN\tID $id ($nombres) Omitido por ser tipo $tipo\r\n";
					}
				}
			break;
			case "ADR":
/*
7820|10280|"Avenida Los Angeles No. 5183, Colonia Las Palmas"|"Tijuana, Baja California CP 22440"|"Mexico"|-0- 
7820|10281|"Calle Colima 2316, Colonia Francisco I. Madero"|"Tijuana, Baja California CP 22150"|"Mexico"|-0-
8132|7280|"Avenue 11 de Septiembre 2155, Edificio Panoramico, Torre C, Oficina 805, Providencia"|"Santiago"|"Chile"|-0- 
8210|7523|"Ibrahim Saeed Lootah Building, Al Ramool Street, P.O. Box 10631 & 638, Rashidya"|"Dubai"|"United Arab Emirates"|-0- 
  */				
				//DOMICILIOS
				$xSoc	= new cSocio($id);
				if($xSoc->existe($id) == true ){
					$xFil->setDataRow($cont);
						//$xFil->cleanString($cadena);

						$calle		= "";
						$numero		= "";
						$cp			= 99999999;
						$ciudad		= "";
						$referencia	= "";
						$colonia	= "";
						
						$pais		= "";
						$estado		= "";
						$municipio	= "";
						$idlocal	= 99999999;
						$idpais		= EACP_CLAVE_DE_PAIS;
						$localidad	= "";
						
						$DDir		= explode(",", trim($xFil->cleanString($xFil->getV(3), array("/-0-/"))));
						$numDs		= count($DDir);
						
						if($numDs > 1){ //si hay mas datos
							//=btener calle y numero
							$DCalle				= explode(" ", $DDir[0]);
							$idxNum				= count($DCalle) - 1;
							$DCalle[$idxNum]	= str_replace("-", " ", $DCalle[$idxNum]);
							if(setNoMenorQueCero($DCalle[$idxNum]) > 0){
								$numero	= $DCalle[$idxNum];		//asignar numero
								unset($DCalle[$idxNum]);		//quitar numero
							}
							$calle	= trim($xFil->cleanCalle( implode(" ", $DCalle) ));
							//obtener colonia
							$idxcolonia	= ($numDs-1);
							$colonia	= trim($DDir[ $idxcolonia ]);
							//quitar colonia y calle, pegar referencia
							unset($DDir[0]); unset($DDir[$idxcolonia]);
							$referencia	= trim(implode(",", $DDir));
						} else {
							$calle		= $DDir[0];
						}
						
						$DEstado		= explode(",", trim($xFil->cleanString($xFil->getV(4), array("/-0-/")) ));
						$numEs			= count($DEstado);
						if($numEs > 1){
							$municipio	= $DEstado[0];
							$DXEstado	= explode(" ", $DEstado[1]);
							$idxcp		= count($DXEstado)-1;
							if( setNoMenorQueCero($DXEstado[$idxcp]) > 0 ){
								$cp		= setNoMenorQueCero($DXEstado[$idxcp]);
								unset($DXEstado[$idxcp]);
								$estado	= $xFil->cleanString( implode(" ", $DXEstado), array("/CP/", "/C.P./") );
							} else {
								$estado	= $DEstado[1];
							}
						} else {
							$estado		= $DEstado[0];
						}
						//Obtener pais
						$pais			= trim($xFil->cleanString($xFil->getV(5), array("/-0-/")));
						$sqlPais		= "SELECT * FROM `personas_domicilios_paises` WHERE `nombre_oficial` SOUNDS LIKE '%$pais%' LIMIT 0,1";
						$idpais			= mifila($sqlPais, "clave_de_control");
						$idpais			= ($idpais == 0) ? "XX" : $idpais;
						//Obtener Localidad
						if($idpais == EACP_CLAVE_DE_PAIS){
							$sqllocal		= "SELECT * FROM `catalogos_localidades` WHERE `nombre_de_la_localidad` LIKE '%$estado%' LIMIT 0,1";
						} else {
							$sqllocal		= "SELECT * FROM `catalogos_localidades` WHERE `nombre_de_la_localidad` LIKE '%$pais%' LIMIT 0,1";
						}
						$DLocal			= $xQL->getDataRow($sqllocal);
						$idlocal		= (isset($DLocal["clave_unica"])) ? $DLocal["clave_unica"] : 0;
						$idlocal		= setNoMenorQueCero($idlocal);
						if( $idlocal > 0){
							$localidad	= $DLocal["nombre_de_la_localidad"];
						} 
						if($idpais == "XX" AND $idlocal <= 0){
							$estado			= "";
							$municipio		= "";
							$localidad		= "";
							$idlocal		= 99999999;
						}
						$xSoc->addVivienda($calle, $numero, $cp, "", $referencia, "0", "0", SYS_UNO, FALLBACK_PERSONAS_REGIMEN_VIV, FALLBACK_PERSONAS_TIPO_VIV, DEFAULT_TIEMPO,
								 $colonia, "calle", "",
								 $idlocal, $idpais,
								$pais, $estado, $municipio, $localidad);
						//Actualizar pais
				}
				$msg 		.=  $xSoc->getMessages(OUT_TXT);
			break;
			case "ALT":
/*
1597|1190|"aka"|"OCTOBER HOLDING COMPANY"|-0- 
1661|1229|"aka"|"PESMAR S.A."|-0-
6857|4607|"aka"|"CARDENAS GUILLEN, Oscar"|-0-
*/
				//$someword = strtolower(preg_replace("/[^a-z]+/i", "-", $theword));
				$xSoc	= new cSocio($id);
				if($xSoc->existe($id) == true ){
					$xFil->setDataRow($cont);
					$eq			= array("AKA" => 511, "FKA" => 512);
					$xRel		= new cSocios_relaciones();
					//$xRel->apellido_materno()
					$idrelacion	= $xFil->getEntero(2);
					$tipo		= trim($xFil->cleanString( $xFil->getV(3, "NULL")));
					$nombres	= $xFil->cleanString($xFil->getV(4, "NULL"));
					$nombre		= "";
					$apellido1	= "";
					$apellido2	= "";
					if( strpos($nombres, ",") !== false ){
						//es nombre de persona
						$ds		= explode(",", $nombres, 2);
						$nombre	= trim($ds[1]);
						if( strpos($ds[0], " ") !== false ){
							$cs		= explode(" ", $ds[0], 2);
							$apellido1	= trim($cs[0]);
							$apellido2	= trim($cs[1]);
						} else {
							$apellido1	= trim($ds[0]);
						}
					} else {
						$nombre	= trim($nombres);
					}
					//echo "$idrelacion [$tipo] $nombres ($nombre|$apellido1|$apellido2)<br />\r\n";
					$xRel->apellido_materno($apellido2);
					$xRel->apellido_paterno($apellido1);
					$xRel->calificacion_del_referente(0);
					$xRel->codigo($id);
					$xRel->consanguinidad(99);
					$xRel->credito_relacionado(DEFAULT_CREDITO);
					$xRel->curp("");
					$xRel->dependiente(1);
					$xRel->domicilio_completo("");
					$xRel->eacp(EACP_CLAVE);
					$xRel->estatus(10);
					$xRel->fecha_alta(fechasys());
					$xRel->fecha_nacimiento($xSoc->getFechaDeNacimiento() );
					$xRel->idsocios_relaciones( $xRel->query()->getLastID() );
					$xRel->idusuario( getUsuarioActual());
					$xRel->monto_relacionado(0);
					$xRel->nombres($nombre );
					$xRel->numero_socio( $idrelacion );
					$xRel->observaciones("");
					$xRel->ocupacion($tipo);
					$xRel->porcentaje_relacionado(100);
					$xRel->socio_relacionado($id);
					$xRel->sucursal(getSucursal());
					$xRel->telefono_movil(0);
					$xRel->telefono_residencia(0);
					$xRel->tipo_relacion( $eq[$tipo] );
					$xRel->query()->insert()->save();
				}				
				break;
			} //end swicth
		}
	}
	$xFRM->addAvisoRegistroOK();
	if(MODO_DEBUG == true){
		$msg			.= $xFil->getMessages();
		$xFRM->addLog($msg);
	}
	$xFRM->addAtras();
	echo $xFRM->get();
}

echo $xHP->setBodyEnd();
//$jsb->show();
?>
<!-- HTML content -->
<script>
</script>
<?php
$xHP->end();
?>