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
$xHP		= new cHPage("TR.Carga Masiva de Captacion", HP_FORM);
ini_set("max_execution_time", 600);

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();

$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);

$xHP->init();

$xFRM		= new cHForm("frmcargamasiva", "vista.carga-masiva.frm.php?action=" . MQL_TEST);
$msg		= "";
$xSel		= new cHSelect();
$xChk		= new cHCheckBox();

$xFRM->setEnc("multipart/form-data");

if($action == SYS_NINGUNO){
	$xFRM->OFile("idarchivo", "");
	
	$xFRM->addHElem( $xSel->getListaDeEmpresas()->get("TR.Empresas" , true));
	$xFRM->addHElem( $xSel->getListaDeCaptacionProductos()->get("TR.Producto de Destino" , true));
	$xFRM->addHElem( $xChk->get("TR.Omitir Importacion", "idimporta") );
	
	//if( MODO_MIGRACION == true ){
		$xFRM->addHElem( $xChk->get("TR.Omitir AML", "idaml") );
	//}
	
	$xFRM->addSubmit("TR.Probar");
	$xFRM->OButton("TR.Guardar", "setEnviarDocto()", "ejecutar");
		
} else  {
	$xFi				= new cFileImporter();
	
	$doc1					= (isset($_FILES["idarchivo"])) ? $_FILES["idarchivo"] : false;
	$observaciones			= parametro("idobservaciones");
	$importar				= parametro("idimporta", false, MQL_BOOL);
	$aml					= parametro("idaml", false, MQL_BOOL);
	
	$producto_destino		= parametro("idproductocaptacion", CAPTACION_PRODUCTO_ORDINARIO, MQL_INT);	
	class cTmp {
		public $ID_PERSONA			= 1;
		public $ID_FISCAL			= 2;
		public $ID_POBLACIONAL		= 3;
		
		public $NOMBRE_PERSONA		= 4;
		public $PRIMER_APELLIDO		= 5;
		public $SEGUNDO_APELLIDO	= 6;
		
		public $ID_CUENTA			= 7;
		public $DEPOSITO			= 8;
		
		public $OBSERVACIONES		= 9;
		public $RETIRO				= 10;
	}
	//Cedula de Identidad
	$tmp	= new cTmp();
	$xFi->setCharDelimiter("|");
	$xFi->setLimitCampos(10);
	//TODO: Evaluar carga de personas desde otra entidad
	//var_dump($_FILES["f1"]);
	if($xFi->processFile($doc1) == true){
		
		$data				= $xFi->getData();
		$conteo				= 1;
		$msg				.= "";
		foreach ($data as $rows){
			
			if($conteo > 1){ //Omitir primera columna
				$xFi->setDataRow($rows);
				//$xHT = new cHTabla();
				//$xHT->addTR();
				$sucess		= true;
				
				$persona	= $xFi->getV($tmp->ID_PERSONA, false, MQL_INT);
				$cuenta		= $xFi->getV($tmp->ID_CUENTA, false, MQL_INT);
				$deposito 	= $xFi->getV($tmp->DEPOSITO, 0, MQL_FLOAT);
				$retiro 	= $xFi->getV($tmp->RETIRO, 0, MQL_FLOAT);
				
				$rfc		= $xFi->getV($tmp->ID_FISCAL);
				$curp		= $xFi->getV($tmp->ID_POBLACIONAL);
				
				$nombre		= $xFi->getV($tmp->NOMBRE_PERSONA);
				$apellido1	= $xFi->getV($tmp->PRIMER_APELLIDO);
				$apellido2	= $xFi->getV($tmp->SEGUNDO_APELLIDO);
				
				$xSoc		= new cSocio($persona);
				//buscar por RFC/CURP
				if($persona == false){
					if($xSoc->initByIDLegal($rfc) == false){
						if($xSoc->initByIDLegal($curp) == false){
							$sucess	= false;
							$msg	.= "ERROR\tLa persona $persona por el ID_POBLACIONAL $curp ni por ID_FISCAL $rfc \r\n";							
						} else {
							$persona	= $xSoc->getCodigo();
							$sucess		= true;
						}
					} else {
						$persona		= $xSoc->getCodigo();
						$sucess			= true;
					}
				}
				
				
				if($importar == false){
					if($xSoc->existe($persona) == false){
						//$sucess	= false;
						//$msg	.= "ERROR\tLa persona $persona NO EXISTE\r\n";
						if(PERSONAS_COMPARTIR_CON_ASOCIADA == true){
						//Intentar cargar desde servidor externo
							$cargar	= $xSoc->getImportarDesdeAsociada(TPERSONAS_GENERALES);
							if($cargar == true){
								$xSoc->getImportarDesdeAsociada(TPERSONAS_DIRECCIONES);
								$xSoc->getImportarDesdeAsociada(TPERSONAS_ACTIVIDAD_ECONOMICA);
							}
							$xSoc		= new cSocio($persona);
							if($xSoc->existe($persona) == false){
								$sucess	= false;
								$msg	.= "ERROR\tLa persona $persona NO EXISTE en ASOCIADA\r\n";
							} else {
								$sucess	= true;
								$msg	.= "OK\tLa persona $persona ha sido importada\r\n";
							}						
						}
						if($sucess == false){  }
					}
				}
				//Agregar
				if($xSoc->existe($persona) == false){
						//Dar de Alta
						$persona	= ( setNoMenorQueCero($persona) > 0 ) ? $persona : false;
						$xSoc		= new cSocio($persona);
						if($aml == true){
							$xSoc->setOmitirAML();
						}
						$xSoc->add($nombre, $apellido1, $apellido2, $rfc, $curp);
						if($xSoc->init() == true){
							$persona	= $xSoc->getCodigo();
							$sucess		= true;
						}
						
				}
				//verificar cuenta de captacion
				if($sucess == true){
					if($cuenta == false){
						$cuenta	= $xSoc->getCuentaDeCaptacionPrimaria(CAPTACION_TIPO_VISTA, $producto_destino);
						$cuenta	= ($cuenta == 0) ? false : $cuenta;
					} else {					
						if( $xSoc->existeCuenta($cuenta) == false ){
							$msg	.= "WARN\tLa cuenta $cuenta NO EXISTE. Se genera una NUEVA\r\n";
							$cuenta	= false;
	
						}
					}
				}
				//Verifica que solo haya un tipo de operacion
				if($sucess == true){
					if($deposito > 0 AND $retiro > 0){
						$sucess	= false;
						$msg	.= "ERROR\tSolo se admite un tipo de operacion por LINEA\r\n";
					}
				}
				//Verifica que solo haya un tipo de operacion
				if($sucess == true){
					if($deposito > 0 AND $retiro > 0){
						$sucess	= false;
						$msg	.= "ERROR\tSolo se admite un tipo de operacion por LINEA\r\n";
					}
				}				
				//Verifica que haya un monto de retiro/deposito
				if($sucess == true){
					if($retiro  == 0 AND $deposito == 0){
						$sucess	= false;
						$msg	.= "ERROR\tNo existe monto de operacion ($deposito|$retiro)\r\n";						
					}
				}
				if($sucess == true AND $action == MQL_ADD){
					$xCta		= new cCuentaALaVista($cuenta);
					if($cuenta == false){
						$cuenta	= $xCta->setNuevaCuenta(DEFAULT_CAPTACION_ORIGEN, $producto_destino, $persona);
						/*
						 	$origen, $subproducto, $socio,
							$observaciones = "", $credito = 1,
							$mancomunado1 = "", $mancomunado2 = "",
							$grupo = 99, $fecha_alta = false,
							$tipo_de_cuenta = 20, $tipo_de_titulo = 99, $DiasInvertidos = false,
							$tasa = false, $CuentaDeIntereses	= false, $FechaVencimiento = false
							*/
					}
					if($retiro > 0){ $xCta->setRetiro($retiro); }
					if($deposito > 0){ $xCta->setDeposito($deposito); }
					$msg		.= $xCta->getMessages();
				}
			}
			$conteo++;
		}
		$xFRM->addAviso($msg);
		$xFRM->addSubmit();
	}
	if(MODO_DEBUG == true){
		$xF	= new cFileLog();
		$xF->setWrite($msg);
		$xF->setClose();
		$xFRM->addToolbar( $xF->getLinkDownload("TR.Archivo de eventos", "") );
	}
}
/*$xFRM->addJsBasico();
$xFRM->addCreditBasico();*/

echo $xFRM->get();
?>
<script>
var mact	= "<?php echo MQL_ADD; ?>";
function setEnviarDocto(){
	$("#id-frmcargamasiva").attr("action", "vista.carga-masiva.frm.php?action=" + mact);
	$("#id-frmcargamasiva").submit(); 
}
</script>
<?php 
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>