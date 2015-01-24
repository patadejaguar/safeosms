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
$xHP		= new cHPage("", HP_FORM);
$xQL		= new MQL();
$DDATA		= $_REQUEST;
$action		= ( isset($DDATA["action"]) ) ? $DDATA["action"] : SYS_CERO;

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
	//$xFRM->addHElem( $xTxt2->getDeMoneda("idnumeropagina", $xFRM->lang("numero de", "pagina")) );
	$xFRM->addHElem( $xTxt->get("idobservaciones", "", "Observaciones") );
	
	$xFRM->addSubmit();
	$xFRM->addFootElement('<input type="hidden" name="MAX_FILE_SIZE" value="1024000">');
	echo $xFRM->get();
} else {
	$doc1			= (isset($_FILES["f1"])) ? $_FILES["f1"] : false;
	$observaciones		= (isset($DDATA["idobservaciones"]) ) ? $DDATA["idobservaciones"] : "";
	$tipoimportacion	= (isset($DDATA["idtipoimportacion"]) ) ? $DDATA["idtipoimportacion"] : "SDN";
	$xFil			= new cFileImporter();
	$limit			= ($tipoimportacion == "SDN") ? 12 : 6;
	$xFil->setLimitCampos($limit);
	$xFil->setCharDelimiter("|");
	$xFil->setType("PIP");
	//var_dump($_FILES["f1"]);
	if($xFil->processFile($doc1) == true){
	$data			= $xFil->getData();
	
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
					 * */
				$tipo	= $xT->cChar( str_replace('"', "", $cont[2] ) );
				if($id > 0){
					if($tipo == "individual" OR $tipo == "-0-"){
						$xSoc		= new cSocio($id);
						$programa	= strtoupper( str_replace('"', "", $cont[3] ) );
						$idpoblacion	= "$programa-$id";
						$nombres	= strtoupper( str_replace('"', "", $cont[1] ) );
						$apellido1	= "";
						$apellido2	= "";
						$pobservaciones	= trim($xFil->cleanString($xFil->getV(12) ));
						if($tipo == "-0-"){
							//$nombres	= $nombres;
						} else {
							$DNom		= explode(",", $nombres, 2);
							$nombres	= isset($DNom[1]) ? $DNom[1] : "";
							$apellidos	= explode(" ", $DNom[0], 2);
							$apellido1	= isset($apellidos[0])  ? $apellidos[0] : "";
							$apellido2	= isset($apellidos[1])  ? $apellidos[1] : "";
						}
						if(setNoMenorQueCero($id) > DEFAULT_SOCIO){	if($xSoc->existe() == true ){ $xSoc->setDeleteSocio(); }	}

						$nombres	= trim($nombres);
						$xSoc->setOmitirAML();
						$xSoc->add($nombres, $apellido1, $apellido2, $idpoblacion, $idpoblacion, getCajaLocal(), false,
							   $programa, TIPO_INGRESO_SDN, DEFAULT_ESTADO_CIVIL, DEFAULT_GENERO, FALLBACK_CLAVE_EMPRESA,
								DEFAULT_REGIMEN_CONYUGAL, FALLBACK_PERSONAS_FIGURA_JURIDICA, DEFAULT_GRUPO, $pobservaciones,
								FALLBACK_PERSONAS_TIPO_IDENTIFICACION, "0", false, false,
								"", "0", 0, false, AML_PERSONA_ALTO_RIESGO);
						$msg .=  $xSoc->getMessages(OUT_TXT);
					} else {
						$msg .= "WARN\tID $id Omitido por ser tipo $tipo\r\n";
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
						$idpais			= ($idpais == 0) ? EACP_CLAVE_DE_PAIS : $idpais;
						//Obtener Localidad
						$sqllocal		= "SELECT * FROM `catalogos_localidades` WHERE `nombre_de_la_localidad` LIKE '%$estado $pais%' LIMIT 0,1";
						$DLocal			= $xQL->getDataRow($sqllocal);
						if( setNoMenorQueCero($DLocal["clave_unica"]) > 0){
							$idlocal	= setNoMenorQueCero($DLocal["clave_unica"]);
							$localidad	= $DLocal["nombre_de_la_localidad"];
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
	$msg			.= $xFil->getMessages();
	if(MODO_DEBUG == true){
		$xFl	= new cFileLog();
		$xFl->setWrite( $msg );
		$xFl->setClose();
		$xFRM->addHTML( $xFl->getLinkDownload("archivo de eventos") );
	} else {
		echo JS_CLOSE;
	}
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