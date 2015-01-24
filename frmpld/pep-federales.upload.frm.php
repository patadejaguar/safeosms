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

$DDATA		= $_REQUEST;

$action		= ( isset($DDATA["action"]) ) ? $DDATA["action"] : SYS_CERO;

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

$msg	= "";
if($action == SYS_CERO){
	$xFRM->addHElem("<div class='tx4'><label for='f1'>" . $xFRM->lang("archivo") . "</label><input type='file'  name='f1' id='f1'  /></div>");
	//$xFRM->addHElem( $xTxt2->getDeMoneda("idnumeropagina", $xFRM->lang("numero de", "pagina")) );
	$xFRM->addHElem( $xTxt->get("idobservaciones", "", "Observaciones") );
	$xFRM->addSubmit();
	$xFRM->addFootElement('<input type="hidden" name="MAX_FILE_SIZE" value="1024000">');
	echo $xFRM->get();
} else {
	$doc1				= (isset($_FILES["f1"])) ? $_FILES["f1"] : false;
	$observaciones		= (isset($DDATA["idobservaciones"]) ) ? $DDATA["idobservaciones"] : "";
	$xFil				= new cFileImporter();
	$xFil->setCharDelimiter("\",");
	$xTmp					= new cTmp();
	//var_dump($_FILES["f1"]);
	if($xFil->processFile($doc1) == true){
		$data				= $xFil->getData();
		$linea				= 0;
		foreach($data as $valores => $cont){
			//
			$xFil->setDataRow($data);
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
			$ready	= $xSoc->add($xFil->getV($xTmp->NOMBRE), $xFil->getV($xTmp->PRIMER_APP), $xFil->getV($xTmp->SEGUNDO_APP),
					"", "", false, false, "",
					TIPO_INGRESO_PEP, DEFAULT_ESTADO_CIVIL,DEFAULT_GENERO,FALLBACK_CLAVE_EMPRESA, DEFAULT_REGIMEN_CONYUGAL, PERSONAS_FIGURA_FISICA, DEFAULT_GRUPO, "",
					1,0, false, false, $xFil->getEntero($xTmp->TELEFONO), $xFil->cleanMail($xFil->getV( $xTmp->MAIL )) );
			if($ready == true){
				$xAct	= new cPersonaActividadEconomica();
				//$clave_de_actividad, $ingreso, $antiguedad = DEFAULT_TIEMPO, $nombrecomercial = "", 
				//$codigo_postal = 0, $telefono = 0, $idlocalidad = 0, $nombrelocalidad = "", $nombremunicipio = "", $nombreestado = ""
				$xAct->add($clave_de_actividad, $ingreso);
			}
			//$xSoc->addActividadEconomica($xFil->getV($indice), $ingreso)
			$linea++;
		}
	}
	$msg			.= $xFil->getMessages();
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