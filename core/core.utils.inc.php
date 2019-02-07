<?php
/**
 * Core Captacion File
 * @author Balam Gonzalez Luis Humberto
 * @version 1.0
 * @package seguimiento
 * Core Captacion File
 * 		10/04/2008 Iniciar Funcion de Notificaciones 360
 */
include_once("core.deprecated.inc.php");
include_once("entidad.datos.php");
include_once("core.config.inc.php");
include_once("core.common.inc.php");
include_once("core.operaciones.inc.php");
include_once("core.creditos.inc.php");
include_once("core.captacion.inc.php");
include_once("core.html.inc.php");
include_once("core.config.inc.php");
include_once("core.fechas.inc.php");

include_once("core.db.dic.php");
@include_once("../libs/sql.inc.php");
//@include_once("../libs/libmail.php");
@include_once '../libs/phpmailer/class.phpmailer.php';
//@include_once '../libs/parse/EnhanceTestFramework.php';
//@include_once '../libs/parse/parse.php';

@include_once("../vendor/autoload.php");
/*
 * CANALES
 * 
 * aml = AML
 * creditos
 * captacion
 * 
 * */

//exec('%systemroot%\system32\shutdown.exe -r -t 0');
/*
 * # registering a service

win32_create_service(array(
service => myservice, # the name of your service
display => sample dummy PHP service, # description
params => c:\path\to\script.php run, # path to the script and parameters
));

# un-registering a service

win32_delete_service(myservice);

# code run as a service

if ($argv[1] == 'run') {
win32_start_service_ctrl_dispatcher('myservice');

while (WIN32_SERVICE_CONTROL_STOP != win32_get_last_control_message()) {
# write script here
# as a general rule, keep it below 30 seconds through each loop iteration
}
}

 */

class cSystemTask{
	private $mSystemCommands 	= array();
	private $mBackupFile		= "";
	private $mMessages			= "";
	function __construct(){

	//Crea el Nombre del Backup File
	$this->mBackupFile		= PATH_BACKUPS . "" .  MY_DB_IN . "_" . SAFE_DB_VERSION . "_" . date("Y-m-d") . ".sql.gz";
//"apagar_el_servidor" => "sudo /sbin/shutdown -P 0",
	$this->mSystemCommands	= array (
							"apagar_el_servidor" => "/usr/bin/apagar_desde_php",
							"reiniciar_el_servidor" => "reboot -n",
							"respaldar_la_base_de_datos" => "mysqldump --opt --add-drop-table --skip-triggers -h " . WORK_HOST . " -u " . USR_DB ." --password=" . PWD_DB . " " . MY_DB_IN . "| gzip > " . $this->mBackupFile . "",
							"respaldar_todas_las_bases_de_datos" => "",
							"instalar_cierre_automatico"
							);
	//mysqldump db_name table_name > table_name.sql
	}
	function setBackupDB_WithMail(){
		$msg		= "";
		$xConf		= new cSAFEConfiguracion();
		if($xConf->SISTEMA_RESPALDO_POR_MAIL == true){
			$fecha		= date("Y-m-d");
			$lns		= system($this->mSystemCommands["respaldar_la_base_de_datos"]);
			//Enviar el Mail SAFE-OSMS Respaldo de la Base de Datos
			$subject	= "SAFE-OSMS Respaldo de la Base de Datos $fecha";
			$body		= "<h3>S.A.F.E. OSMS</h3><h4>Demonio CRON</h4><p>Se Anexa repaldo de la Fecha $fecha</p><hr /><h5>SysAdmin</h5>";
			$file		= array( "path"  => $this->setBackupDB(), "mime" => "multipart/x-gzip");
			$enviar		= true;
			
			if(is_file($this->mBackupFile)){
				$size	= filesize($this->mBackupFile);
				if($size > getMemoriaLibre()){
					$this->mMessages	.= "ERROR\tEl Archivo " . $this->mBackupFile . " es muy grande ($size)  para la Memoria (" . getMemoriaLibre() . ")\r\n";
				} else {
					$enviar	= true;
				}
			} else {
				$enviar	= false;
				$this->mMessages	.= "ERROR\tEl Archivo " . $this->mBackupFile . " No existe\r\n";
			}
			if($enviar == true){
				$msg		.= $this->sendMailToAdminWithFile($subject, $body, $file);
			}
		}
		return $msg;
	}
	function setBackupDB(){
		$fecha		= date("Y-m-d");
		
		$msg		= "SAFE-OSMS Respaldo de la Base de Datos $fecha.\r\n";
		$msg		.= system($this->mSystemCommands["respaldar_la_base_de_datos"]);
		$this->mMessages	.= $msg;
		
		return $this->mBackupFile;
	}
	function getMessages(){return $this->mMessages;}
	function setBackupTable($table){
		$file		= PATH_BACKUPS .  MY_DB_IN . "_$table_" . date("Y-m-d") . ".sql.gz";
		$ce			= system("mysqldump --opt -h " . WORK_HOST . " -u " . USR_DB ." --password=" . PWD_DB . " " . MY_DB_IN . " $table| gzip > $file");
		return $file;
	}
	function setPowerOff(){
		return true;
	}
	/**
	 * funcion que envia un Correo Electronico al Admin con un Archivo
	 *
	 * @param string $subject
	 * @param string $body
	 * @param array $arrFile
	 * @return string
	 * el parametro $aarrFile Indica un array compuesto asi array ("path" =>
	 * "rutal al archivo", "mime" => "MIME/TYPE").
	 *
	 */
	function sendMailToAdminWithFile($subject = "", $body = "", $arrFile = false){
		//TODO: Migrar a enviar mail
			$omsg	= "";
		if (filter_var(ADMIN_MAIL, FILTER_VALIDATE_EMAIL)) {
			//Create a new PHPMailer instance
			$mail = new PHPMailer();
			//Tell PHPMailer to use SMTP
			$mail->IsSMTP();
			//Enable SMTP debugging
			// 0 = off (for production use)
			// 1 = client messages
			// 2 = client and server messages
			$mail->SMTPDebug  = 0;
			$mail->Timeout    = 30;
			//Ask for HTML-friendly debug output
			$mail->Debugoutput = 'html';
			//Set the hostname of the mail server
			$mail->Host       = ADMIN_MAIL_SMTP_SERVER;
			//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
			$mail->Port       = ADMIN_MAIL_SMTP_PORT;
			//Set the encryption system to use - ssl (deprecated) or tls
			//$mail->SMTPSecure = ADMIN_MAIL_SMTP_TLS;
			if(ADMIN_MAIL_SMTP_TLS != ""){
				$mail->SMTPSecure = ADMIN_MAIL_SMTP_TLS;//'tls';
			}			
			//Whether to use SMTP authentication
			$mail->SMTPAuth   = true;
			//Username to use for SMTP authentication - use full email address for gmail
			$mail->Username   = ADMIN_MAIL;//EACP_MAIL;
			//Password to use for SMTP authentication
			$mail->Password   = ADMIN_MAIL_PWD;
			//Set who the message is to be sent from
			$mail->SetFrom(ADMIN_MAIL, 'S.A.F.E. OSMS System Backup');
			//Set an alternative reply-to address
			//$mail->AddReplyTo('replyto@example.com','First Last');
			//Set who the message is to be sent to
			$mail->AddAddress(ADMIN_MAIL, 'SAFE-OSMS Admin');
			//Set the subject line
			$mail->Subject = $subject;
			//Read an HTML message body from an external file, convert referenced images to embedded, convert HTML into a basic plain-text alternative body
			//$mail->MsgHTML(file_get_contents('contents.html'), dirname(__FILE__));
			$mxMsg		= "";
			$mxMsg		.= $body;

			$mail->MsgHTML($mxMsg);
			//Replace the plain text body with one created manually
			//$mail->AltBody = 'This is a plain-text message body';
			//Attach an image file
			if ($arrFile != false AND is_array($arrFile) ){
				//$m->Attach($arrFile["path"], $arrFile["mime"], "inline");
				$mail->AddAttachment($arrFile["path"]);
			}
			//$mail->AddAttachment('images/phpmailer-mini.gif');
		
			//Send the message, check for errors
			if(!$mail->Send()) {
				$omsg	.= "Error: " . $mail->ErrorInfo;
			} else {
				$omsg	.= "Mensaje Enviado con exito.";
			}
		} else {
			$omsg		= "ERROR\tCorreo Electronico Invalido\r\n";
		}
		return $omsg;
	}
	function sendMail($subject = "", $body = "", $to = "", $arrFile = false){
		$xNot		= new cNotificaciones();
		return $xNot->sendMail($subject, $body, $to, $arrFile);
	}
	
	function setProcesarTareas(){
		$xF		= new cFecha();
		$xSuc	= new cSucursal("matriz"); $xSuc->init();
		
		//CRON del Sistema
		//Enviar Notificaciones de Cobranza.
		$xQL	= new MQL();//AND `hora`<=CURRENT_TIME()
		if($xF->hora() >= $xSuc->getHorarioDeEntrada() ){
			$rsNot	= $xQL->getRecordset("SELECT * FROM `seguimiento_notificaciones` WHERE `estatus_notificacion`='pendiente' AND `fecha_notificacion` <=CURRENT_DATE() AND (`canal_de_envio`='sms' OR `canal_de_envio`='email') LIMIT 0,100");
			$xT		= new cSeguimiento_notificaciones();
			if($rsNot){
				while($rw = $rsNot->fetch_assoc()){
					$idx	= $rw[$xT->IDSEGUIMIENTO_NOTIFICACIONES];
					$xNot	= new cSeguimientoNotificaciones($idx);
					if($xNot->init($rw) == true){
						$xNot->enviar();
						sleep(2);//
						$this->mMessages	.= $xNot->getMessages();
					}
				}
				$rsNot->free();
			}
		}
		//setLog($this->mMessages);
	}
	function cmd_exist($cmd) {
		if($this->getEsWindows() == true){
			return false;
		}
		$return = shell_exec(sprintf("which %s", escapeshellarg($cmd)));
		return !empty($return);
	}
	function getExistsPandoc(){
		return $this->cmd_exist("pandoc");
	}
	function getExistsUnoconv(){
		return $this->cmd_exist("unoconv");
	}
	function getExistsMemcache(){
		return $this->cmd_exist("memcached");
	}
	function getEsWindows(){
		$OS 					= strtolower(substr(PHP_OS, 0, 3));
		return ($OS == "win") ? true : false;
	}
	function getExistsWHPDF(){
		$res1	= $this->cmd_exist("wkhtmltopdf");
		$res2	= $this->cmd_exist("Xvfb");
		
		return ($res1 !== false && $res2 !== false) ? true : false;
	}
	function runcmd($orden){
		$res	= shell_exec($orden);
		return ($res === false) ? false : true;
	}
	/**
	 * Ejecuta un Archivo a SQL
	 * @param string $archivo /path/to/file.sql
	 */
	function setRunSQLPatchByFile($subdir, $archivo){
		//"mysqldump --opt --add-drop-table --skip-triggers -h  -u  --password= | gzip > " . $this->mBackupFile . ""
		$oldfile	= $subdir . $archivo;
		$newfile	= sys_get_temp_dir() . "/" . $archivo;
		
		if(file_exists($newfile)){
			$this->runcmd("mysql --host=" . WORK_HOST . " --user=" . USR_DB ." --password=" . PWD_DB . " --force --database=" . MY_DB_IN . " < $newfile");
		} else {
			if(copy($oldfile, $newfile)){
				$this->runcmd("mysql --host=" . WORK_HOST . " --user=" . USR_DB ." --password=" . PWD_DB . " --force --database=" . MY_DB_IN . " < $newfile");
			}
		}
	}
}

/**
 * Funcion que crea o actualiza una tabla en el sistema
 * @param string $NTable Nombre de la Tabla la cual desea trabajar
 * @param integer $TCond Tipo de Operacion 0 = nueva Estructura, 1 = Actaulizacion de la estructura
 * @return	null
 **/
function setStructureTableByDemand($NTable, $TCond = 0, $options = array() ){
	//$TCond 1 = Actualizar, 0 = Nuevo
	/**
		 * Crea la Estructura de una Tabla Determinada
		 */
	$msg	= "";
	$xSt	= new cTableStructure($NTable);
	$msg	= $xSt->setStructureTableByDemand($TCond, $options);
	return $msg;
}
/**
 * @author Son Nguyen
 * @since 11/18/2005
 * @package Framework.Data
 * @subpackage Math
 */
class cRegressionLineal {
	private $mDatas;
	/** constructor */
	function __construct($pDatas){
		$this->mDatas = $pDatas;
	}

	/** get the coefficients */
	function calculate() {
		$n 	= count($this->mDatas);
		$vSumXX = $vSumXY = $vSumX = $vSumY = 0;
		foreach ($this->mDatas AS $x=>$y) {
			$vSumXY 	+= $x*$y;
			$vSumXX 	+= $x*$x;
			$vSumX 		+= $x;
			$vSumY 		+= $y;
		} // rof
		$a = ($n*$vSumXY - $vSumX*$vSumY)/($n*$vSumXX - $vSumX*$vSumX);
		
		$b = ($vSumY - $a*$vSumX)/$n;
		return array($a,$b);
	}
	/** given x, return the prediction */
	function predict($x) {
		list($a,$b) = $this->calculate();
		$y = $a*$x+$b;
		return $y;
	}
}
class cMath {
	private $mArrDias	= array(30 => 12, 7=>52, 15=>24, 360=>1, 365 => 1, 10 => 36, 14=>26, 60 => 6, 90 => 4, 180 => 2, 1 => 365);
	function irr ($investment, $flow) {
		$n		= 0;
		$it 	= count($flow);
		if($it >= 1){
		    for ($n = 0; $n < 100; $n += 0.00001) {
				$pv = 0;
				//corrige el flow
				if(!isset($flow[0])){ $flow[0]	= $investment + 0.01; }
				
				for ($i = 0; $i < $it; $i++) {
					$vv		= $flow[$i];
					if($vv>0){
				    	$pv = $pv + ($flow[$i] / pow(1 + $n, $i + 1));
					}
				}
				if ($pv <= $investment) {
				    return $n;
				}
		    }
		} else {
			return 0;
		}
	}
	function cat($capital, $flujo, $periodos, $periodosMaximo){
		
		$tri		= 0;
		if($capital > 0){
			$tir     = $this->irr($capital, $flujo);
			$tri	= pow((1 + $tir), $periodosMaximo) - 1;
			$tri	= round(($tri * 100), 1);
		}
		return $tri;
	}
	/**
	 * Obtiene un pago presumido aproximado.- Método Francés.
	 * @param float $TasaAnual	Tasa Anualizada en Valor real (30% = 0.30, 60% = 0.60). Esta Tasa Debe Incluir IVA, por ejemplo para el 60% sería 0.6+(0.6*.16) = (0.696) 
	 * @param integer $NumeroDePagos Numero de pagos Totales del Credito
	 * @param float $Capital	Base de Cálculo
	 * @param integer $Frecuencia Frecuencia o Periodicidad de Pago (15 Quincenal, 30 Mensual, etc)
	 * @param int $prec Numero de precisón de cálculo
	 * @return number
	 */
	function getPagoPresumido($TasaAnual,$NumeroDePagos,$Capital,$Frecuencia = 30, $prec=2){
		$arrEq		= $this->mArrDias;
		$EquiFreq	= isset($arrEq[$Frecuencia]) ? $arrEq[$Frecuencia] : 0;
		if ($TasaAnual !=0 AND $EquiFreq >0) {
			$semilla 		= 1/(1+$TasaAnual/$EquiFreq);
			$PagoPresumido 	=  $Capital * (1 - $semilla) / $semilla / (1 - pow($semilla,$NumeroDePagos)) ;
		} else {
			$PagoPresumido 	= $Capital / $NumeroDePagos;
		}
		return round($PagoPresumido, $prec);
	}
	function getValorPresente($TasaAnual,$NumeroDePagos,$Capital,$Frecuencia = 30,$prec=2){
		$arrEq		= $this->mArrDias;
		$EquiFreq	= isset($arrEq[$Frecuencia]) ? $arrEq[$Frecuencia] : 0;
		if ($TasaAnual !=0 AND $EquiFreq >0) {
			$tem	=  pow((1+$TasaAnual), (1/$EquiFreq) ) -1;
			$PV		= $Capital / pow((1+$tem), $NumeroDePagos);
		} else {
			$PV = $Capital / $NumeroDePagos;
		}
		return round($PV, $prec);
	}
	function getPagoLease($TasaAnual,$NumeroPagos,$Capital, $Frecuencia, $Residual = 0.00, $Tipo = 0){
		$arrEq	= $this->mArrDias;
		$EqFreq	= isset($arrEq[$Frecuencia]) ? $arrEq[$Frecuencia] : 0;
		
		$Tasa	= ($TasaAnual / $EqFreq);
		
		$P = (- $Capital * pow(1+$Tasa,$NumeroPagos) + $Residual) /	((1 + $Tasa * $Tipo)*((pow((1 + $Tasa),$NumeroPagos) - 1) / $Tasa));

		return round(($P * (-1)),2);
		/*double MKCalcPayment(int NumPay, double IntRate, double NPV, double FV,
                     BOOL bStart)
{
    IntRate /= 1200.00;
		    double P = (- NPV * pow(1+IntRate,NumPay) + FV) /
               ((1 + IntRate * bStart)*((pow((1 + IntRate),NumPay) - 1) /
				               IntRate));
    return P * (-1); // Just convert it into a positive value.
		}*/
	}
}

class cFileImporter {
	private $mFecha		= "";
	private $mMessages		= "";
	private $mData			= array();
	private $mDelimiter	= ",";
	private $mType			= "csv";
	private $mLimitCampos	= 12;
	private $mPriLineaCol	= true;
	private $mDataRow		= false;
	private $mForceUTF		= false;
	private $mForceClean	= false;
	private $mArrClean		= array();
	private $mExo			= "";
	private $mProbarMB		= false;
	private $mModoRAW		= false;
	public $TIPO_CSV		= "csv";
	public $TIPO_XML		= "xml";
	private $mCompletePath	= "";
	function __construct(){  }
	
	function processFile($file){
		$sucess	= true;
		$xLog	= new cCoreLog();
		
		if( isset($file) AND $file != false ){
			//Obtener Extension
			$DExt 	= explode(".", substr($file['name'], -6));
			$mExt	= $DExt[1];
			
			if($mExt == $this->mType){
				$completePath	= PATH_TMP . $file['name'];
				if(file_exists($completePath) == true){
					unlink($completePath);
					$xLog->add("WARN\tSE ELIMINO EL ARCHIVO " . $file['name'] . "\r\n", $xLog->DEVELOPER);
				}
				if(move_uploaded_file($file['tmp_name'], $completePath )) {
					$xLog->add("OK\tSE GUARDO EXITOSAMENTE EL ARCHIVO " . $file['name'] . "\r\n");
				} else {
					$xLog->add("ERROR\tSE FALLO AL GUARDAR (" . $file['name'] . ") de " . $file['tmp_name'] . " a $completePath\r\n");
					$xLog->add($this->getMsgError($file['error']));
					$sucess				= false;
				}
			}	else {
				$xLog->add("ERROR\tEL TIPO DE ARCHIVO DE " . $file['name'] . "(" .$mExt . ") NO SE ACEPTA\r\n");
				$sucess					= false;
			}
		} else {
			$xLog->add("ERROR\tEL ARCHIVO NO ES VALIDO $file\r\n");
			$sucess					= false;			
		}
		if($sucess == true){
			//analizar el Archivo
			$gestor = @fopen($completePath, "r");
			
			$iReg 	= 0;
			//$cT		= new cTipos();
			//inicializa el LOG del proceso
			//$aliasFil	= getSucursal() . "-carga -batch-de-creditos-" . fechasys();
			//$xLog		= new cFileLog($aliasFil, true);
			if ($gestor) {
				while (!feof($gestor)) {
					$bufer			= fgets($gestor, 4096);
					if (!isset($bufer) ){
						$xLog->add("ERROR\t$iReg\tLa Linea($iReg) no se leyo($bufer)\r\n");
						//$this->mData[]= array(); //Array Vacio
					} else {
						$bufer		= trim($bufer);
						$datos		= array();
						if($this->mExo	== ""){
							if($this->mLimitCampos > 0){
								$datos		= explode($this->mDelimiter, $bufer, $this->mLimitCampos);
							}							
						} else {
							//delimitar por X  echo 
							//$del			= substr_count($this->mExo, "|");
							//$dex			= explode("|", $this->mExo);
							$dex			= explode($this->mDelimiter, $this->mExo);
							$init			= 0;
							foreach ($dex as $snipts){
								$tlen		= strlen($snipts) + 1;
								$datos[]	= trim(substr($bufer, $init, $tlen));
								$init		+= $tlen;	
							}
						}
						$this->mData[]		= $datos;
					}
					$iReg++;
				}
			}
		}
		$this->mMessages	.= $xLog->getMessages();
		
		return $sucess;
	}
	function setExo($str){ $this->mExo	= $str; }
	function setLimitCampos($campos){ $this->mLimitCampos = $campos;}
	function setCharDelimiter($char){ $this->mDelimiter	= $char; }
	function setType($tipo){ $this->mType = $tipo; }
	function getData(){ return $this->mData; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put);	}
	function setPrimeraLinea(){  }
	function setProbarMB($probar = true){ $this->mProbarMB = $probar; }
	function setModoRAW(){ $this->mModoRAW = true; }
	function setDataRow($data){ $this->mDataRow	= $data; return $data; }
	function getFlotante($indice, $fallback = 0 ){	return $this->getV($indice, $fallback, MQL_FLOAT); }
	function getEntero($indice, $fallback = 0 ){	return $this->getV($indice, $fallback, MQL_INT); }
	function getFecha($indice, $fallback = false ){	return $this->getV($indice, $fallback, MQL_DATE); }
	function getBool($indice, $fallback = false ){	return $this->getV($indice, $fallback, MQL_BOOL); }
	
	function getV($indice, $fallback = null, $tipo = MQL_STRING, $equiv = false){
		//Migrar a cCoreImport
		$valor		= null;
		$row		= $this->mDataRow;
		$xT			= new cTipos();
		$xT->setForceEncode();
		$xT->setNoForceMins();
		//CORREGIR ID
		$indice		= $indice - 1;
		if(isset($row[$indice])){
			if(is_array($equiv)){
				$vtmp	= strtoupper($row[$indice]);
				if(isset($equiv[ $vtmp ])){
					$row[$indice]	= $equiv[ $vtmp ]; //cambiar indice por equivalente
				} else {
					$row[$indice]	= $fallback;
					$this->mMessages	.= "ERROR\tNo hay equivalente para " . $vtmp . " del Indice $indice  \r\n";
				}
			}
			if($tipo == MQL_STRING ){
				
				if($this->mForceClean == true){
					$row[$indice]	= $this->cleanString($row[$indice], $this->mArrClean);
				}
				
				//$row[$indice] 	= $xT->setNoAcentos($row[$indice]);
				if($this->mForceUTF == true){
					if($this->mProbarMB == false){
						if(iconv('UTF-8', 'UTF-8//IGNORE', $row[$indice])){
							$row[$indice]	= iconv('UTF-8', 'UTF-8//IGNORE', $row[$indice]);
						} else {
							$row[$indice]	= iconv(mb_detect_encoding($row[$indice]), 'UTF-8//IGNORE', $row[$indice]);
						}
					} else {
						if($this->mModoRAW == true){
							$row[$indice] 	= $row[$indice];
						} else {
							$row[$indice] 	= $xT->setNoAcentos($row[$indice]);
						}
					}
					//if($this->mForceUTF == true){ $cadena	= iconv('UTF-8', 'UTF-8//IGNORE', $cadena); }
					//$dato	= iconv(mb_detect_encoding($dato), 'UTF-8//IGNORE', $dato);				
				}
			}
			return parametro($indice, $fallback, $tipo, $row);
		} else {
			return $fallback;
		}
		
	}
	function setToUTF8(){	$this->mForceUTF		= true;	}
	function cleanCalle($valor = ""){
		$valor		= strtoupper($valor);
		$arr		= array("AVENIDA", "CALLE", "CALE ", "CALLLE", "AVE.", "AVE ", "C.", "C ", "NUM.", "NUM ", "NO ", "NOM.", "SIN NUMERO", "SN", "S/N", "SIN NIM", "LOTE ", "#", "NO.");
		$valor		= str_replace($arr, " ", $valor);
		return 		trim(preg_replace('!\s+!', ' ', $valor));
	}
	function cleanMail($email){
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
				
		} else {
			$email	= "";
		}
		return $email;
	}
	function cleanString($cadena, $otros = false){
		$cleanArr	= array('/\s\s+/', '/(\")/', '[\\\\]', '/(\')/');
		if(is_array($otros)){
			$cleanArr	= array_merge($cleanArr,$otros);
		}
		$cadena 		= preg_replace($cleanArr, ' ', $cadena); //dob
		return trim($cadena);
	}
	function setArrClean($arr){ $this->mArrClean = $arr; }
	function setForceClean($force = true){ $this->mForceClean =$force; }
	function getMsgError($error){
		$message = 'Error uploading file';
		switch( $error ) {
			case UPLOAD_ERR_OK:
				$message = false;;
				break;
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				$message .= ' - file too large (limit of '. ini_get("upload_max_filesize") . ' bytes).';
				break;
			case UPLOAD_ERR_PARTIAL:
				$message .= ' - file upload was not completed.';
				break;
			case UPLOAD_ERR_NO_FILE:
				$message .= ' - zero-length file uploaded.';
				break;
			default:
				$message .= ' - internal error #'. $error;
				break;
		}
		return $message;
	}
	function setSaveFile($file = false){
		$sucess	= true;
		if( isset($file) AND $file != false ){
			//Obtener Extension
			$DExt 	= explode(".", substr($file['name'], -6));
			$mExt	= $DExt[1];
		
			if($mExt == $this->mType){
				$completePath	= PATH_TMP . $file['name'];
				if(file_exists($completePath) == true){
					unlink($completePath);
					$this->mMessages	.= "WARN\tSE ELIMINO EL ARCHIVO " . $file['name'] . "\r\n";
				}
				if(move_uploaded_file($file['tmp_name'], $completePath )) {
					$this->mMessages	.= "OK\tSE GUARDO EXITOSAMENTE EL ARCHIVO " . $file['name'] . "\r\n";
				} else {
					$this->mMessages	.= "ERROR\tSE FALLO AL GUARDAR (" . $file['name'] . ") de " . $file['tmp_name'] . " a $completePath\r\n";
					$this->mMessages	.= $this->getMsgError($file['error']);
					$sucess				= false;
				}
			}	else {
				$this->mMessages		.= "ERROR\tEL TIPO DE ARCHIVO DE " . $file['name'] . "(" .$mExt . ") NO SE ACEPTA\r\n";
				$sucess					= false;
			}
		} else {
			$this->mMessages		.= "ERROR\tEL ARCHIVO NO ES VALIDO $file\r\n";
			$sucess					= false;
		}
		if($sucess == true){
			$this->mCompletePath	= $completePath;
		}
		return $sucess;
	}
	function getCompletePath(){ return $this->mCompletePath; }
	
}
class cFileSystem {
	private $mMessages		= "";
	private $mPageLayout	= "landscape";
	public $PAGE_PORTRAIT	= "portrait";
	function __construct(){}
	function setConvertToDocx($contenido, $titulo){
		//pandoc  -s -S test.htm -o test.docx
		$rawname	= $this->cleanNombreArchivo($titulo);
		$fmt_in		= $rawname . ".html";
		$fmt_out	= $rawname . ".docx";
		$nn			= "";
		$nfile		= $this->setCreateFile($contenido, $fmt_in);
		$res		= false;
		if($nfile !== false){
			$ofile	= PATH_TMP . $fmt_out;
			
			$xFU	= new cSystemTask();
			if($xFU->getExistsUnoconv() == true){
				$res	= $xFU->runcmd("export HOME=" . PATH_TMP .  " && /usr/bin/unoconv --format=docx --output='$ofile' '$nfile'");
				if($res !== false){
					$nn		= $ofile;
				}
			}
			
		}
		return $nn;
	}
	function setConvertToPDF($contenido, $titulo, $args = ""){
		$rawname	= $this->cleanNombreArchivo($titulo);
		//pandoc reports/7/report.html -o reports/7/report.pdf
		$fmt_in		= $rawname . ".html";
		$fmt_out	= $rawname . ".pdf";
		$nn			= "";
		$nfile		= $this->setCreateFile($contenido, $fmt_in);
		$xFU		= new cSystemTask();
		
		
		if($nfile !== false){
			$ofile	= PATH_TMP . $fmt_out;
			if($xFU->getExistsWHPDF() == true){
				//Landscape
				$orient	= ucfirst($this->mPageLayout);
				$res	= $xFU->runcmd('/usr/bin/xvfb-run --server-args="-screen 0, 1920x1080x24" /usr/bin/wkhtmltopdf --page-size Letter --orientation ' . $orient . ' file://' . $nfile . ' ' . $ofile . '  2>&1');
				if($res !== false){
					$nn	= $ofile;
				}
			}
		}
		return $nn;
	}
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put);	}
	function getReadFile($file, $path=""){
		$ff				= $path . $file;
		$cnt			= "";
		if(file_exists($ff)){
			
			$mff 		= fopen($ff, "r");
			$cnt		= fread($mff,filesize($ff));
			fclose($mff);
		}
		return $cnt;
	}
	function setSaveFile($text, $file, $path="", $rem = false){
		$ff				= $path . $file;
		if($rem == true){
			if(file_exists($ff)){
				unlink($ff);
			}
		}
		$mff 		= fopen($ff, "a+");
		@fwrite($mff, $text);
		@fclose($mff);
	}
	function setCreateFile($contenido, $nombre){
		$xLog			= new cCoreLog();
		$nombre			= $this->cleanNombreArchivo($nombre);
		
		$completePath	= PATH_TMP . "" . $nombre;
		$nombre_archivo	= $completePath;
		$res			= true;
		
		if(file_exists($completePath) == true){
			unlink($completePath);
			$xLog->add("WARN\tSE ELIMINO EL ARCHIVO $completePath\r\n", $xLog->DEVELOPER);
			//$xLog->add("ERROR\t $completePath\r\n", $xLog->DEVELOPER);
		}
		//file_put_contents($fichero, $persona, FILE_APPEND | LOCK_EX);
		//$xF	= new cFileLog();
		//$xF->setWrite($text);
		//if (is_writable($nombre_archivo)) {
			if (!$gestor = fopen($nombre_archivo, 'w+')) {
				$xLog->add("ERROR\tNo se puede abrir el Archivo $completePath\r\n", $xLog->DEVELOPER);
				$res	= false;
			}
			
			// Escribir $contenido a nuestro archivo abierto.
			if (fwrite($gestor, $contenido) === FALSE) {
				$xLog->add("ERROR\tNo se puede escribir el Archivo $completePath\r\n", $xLog->DEVELOPER);
				$res	= false;
			}
			if($res === true){
				$xLog->add("OK\tSe escribio el Archivo $completePath\r\n");
			}
			fclose($gestor);
			
		//} else {
		//	$xLog->add("ERROR\tEl Archivo $completePath no se puede escribir\r\n");
		//	$res	= false;
		//}
		
		$this->mMessages	.= $xLog->getMessages();
		return ($res == true) ? $completePath : false;
	}
	function cleanNombreArchivo($f, $cleanExts = false){
		$f	= preg_replace("/\s\s+/", "_", $f);
		
		$f	= str_replace(":", "_", $f);
		$f	= str_replace("-", "_", $f);
		$f	= str_replace(" ", "_", $f);
		if($cleanExts == true){
			$f	= str_replace(".", "_", $f);
		}
		$f	= setCadenaVal($f);
		$f	= strtolower($f);
		
		$f	= preg_replace("/__+/", "_", $f);
		
		return $f;
	}
	function setRepareHTML($html, $incHeaders = false){
		if($incHeaders == true){
			$xHP	= new cHPage("", HP_REPORT);
			$html	= $xHP->init("", true) . $html . $xHP->fin(true);
		}
		$html	= str_replace("../css/", SAFE_HOST_URL . "css/", $html);
		$html	= str_replace("../js/", SAFE_HOST_URL . "js/", $html);
		$html	= str_replace("../images/", SAFE_HOST_URL . "images/", $html);
		return $html;
	}
	function setPageLayout($orient){ $this->mPageLayout =  $orient; }
}
//Eimina datos no validos
class cTiposLimpiadores {
	
	function cleanString($cadena, $otros = false){
		$cleanArr	= array('/\s\s+/', '/(\")/', '[\\\\]', '/(\')/');
		if(is_array($otros)){
			$cleanArr	= array_merge($cleanArr,$otros);
		}
		$cadena 		= preg_replace($cleanArr, ' ', $cadena); //dob
		return $cadena;
	}
	function cleanEmpleo($cadenas, $PorDefecto = ""){
		$cadenas	= str_replace("/", "", $cadenas);
		$cadenas	= $this->cleanString($cadenas, array("/DESCONOCIDO_MIGRADO/","/EMPLEADO_MIGRADO/", "/empleado_migrado/", "/NA/", "/POR_REGISTRAR/", "/DESCONOCIDO/"));
		if($cadenas == "" AND $PorDefecto != ""){ $cadenas	= $PorDefecto;	}
		return $cadenas;
	}
	function cleanCalle($valor = ""){
		$valor		= strtoupper($valor);
		$arr		= array("AVENIDA", "CALLE", "CALE ", "CALLLE", "AVE.", "AVE ", "C.", "C ", "NUM.", "NUM ", "NO ", "NOM.", "SIN NUMERO", "SN", "S/N", "SIN NIM", "LOTE ", "#", "NO.", "NUMERO");
		$valor		= str_replace($arr, " ", $valor);
		return 		trim(preg_replace('!\s+!', ' ', $valor));
	}
	function cleanColonia($valor = ""){
		$valor		= strtoupper($valor);
		$arr		= array("COLONIA", "COL.", "COL");
		$valor		= str_replace($arr, " ", $valor);
		return 		trim(preg_replace('!\s+!', ' ', $valor));
	}	
	function cleanTextoBuscado($txt, $limite = 0){
		$limite	= setNoMenorQueCero($limite);
		
		$txt	= trim(preg_replace('!\s+!', ' ', $txt));
		if($limite >0){
			$txt	= substr($txt, 0, $limite);
		}
		$txt 	= str_replace(" ", "-", $txt); 
		$txt 	= str_replace("*", "", $txt);
		
		return $txt;
	}
	function cleanNombreComp($n, $reverse = false){
		
		$n		= $this->cleanString($n);
		
		$n		= str_replace(" DEL ", " DEL_", $n);
		$n		= str_replace(" DE LA ", " DE_LA_", $n);
		$n		= str_replace(" Y ", " Y_", $n);
		$n		= str_replace(" DE ", " DE_", $n);
		

		
		$dev	= array();
		$nn		= array();
		$nom	= "";
		if($reverse == true){
			$d		= explode(" ", $n, 3);
			$dev[0]	= $d[0];
			$dev[1]	= isset($d[1]) ? $d[1] : "";
			$dev[2]	= isset($d[2]) ? $d[2] : "";
			
			$dev[0]	= str_replace("_", " ", $dev[0]);
			$dev[1]	= str_replace("_", " ", $dev[1]);
			$dev[2]	= str_replace("_", " ", $dev[2]);
			
		} else {
			$d		= explode(" ", $n, 8);
			$cnt	= count($d)-1;
			for($i = 0; $i <= $cnt; $i++){
				$ix				= $cnt - $i;
				if($ix == $cnt){
					$dev[1]		= $d[$ix];
				} else if($ix == ($cnt-1)){
					$dev[0]		= $d[$ix];
				} else {
					$nn[$ix]	= $d[$ix];
				}			
			}
			ksort($nn);
			$nom	= implode(" ", $nn);
			$dev[2]	= str_replace("_", " ", $nom);
			$dev[1]	= str_replace("_", " ", $dev[1]);
			$dev[0]	= str_replace("_", " ", $dev[0]);
		}
		return $dev;
	}
	function cleanApellidos($n){
	
		$n		= $this->cleanString($n);
	
		$n		= str_replace(" DEL ", "_DEL_", $n);
		$n		= str_replace(" DE LA ", "_DE_LA_", $n);
		$n		= str_replace(" Y ", "_Y_", $n);
		$n		= str_replace(" DE ", "_DE_", $n);
		
		
		$d		= explode(" ", $n, 2);
		$cnt	= count($d)-1;
	
		$dev	= array();
		
		$dev[0]	= $d[0];		
		$dev[1]	= (isset($d[1])) ? $d[1] : "-";

		$dev[0]	= str_replace("_", " ", $dev[0]);
		$dev[1]	= str_replace("_", " ", $dev[1]);
	
		return $dev;
	}
	function cleanSucursal($s){
		$s		= $this->cleanString($s);
		$s		= str_replace(" ", "", $s);
		$s		= strtolower($s);
		$s		= substr($s, 0,9);
		return $s;
	}
}

class cDocumentos {
	private $mNombreArchivo	= "";
	private $mTipo			= "";
	private $mEsImagen		= false;
	private $mEsDocto		= false;
	private $mExt			= "";
	private $mCnnFTP		= null;
	private $mPersona		= false;
	private $mMessages		= "";
	public $EXT_PDF			= "PDF";
	private $mReady			= false;
	private $mIdxFileL		= "ftp-list-files-";
	private $mPrePath		= "";
	private $mRutaLocal		= "";
	function __construct($nombre = ""){ $this->mNombreArchivo = $nombre; $this->getTipo();	}
	function getTipo($documento = false){
		$documento	= ($documento == false) ? $this->mNombreArchivo : $documento;
		$ext		= strtoupper(substr($documento, -3));
		
		switch ($ext){
			case "PNG":
				$this->mEsImagen	= true;
				break;
			case "JPG":
				$this->mEsImagen	= true;
				break;
			case "PDF":
				$this->mEsDocto		= true;
				break;
		}
		$this->mExt		= strtolower($ext);
		return $ext;
	}
	function isImagen(){ return $this->mEsImagen; }
	function isDocto(){ return $this->mEsDocto; }
	function getNombreArchivo(){ return $this->mNombreArchivo; }
	function setNombreArchivo($f){
		$this->mNombreArchivo	= $f;
		$this->getTipo($f);
	}
	function getRutaLocal(){ return $this->mRutaLocal; }
	
	function getExt(){ return $this->mExt; }
	
	function getEmbed($archivo = false, $persona = false, $conteo = 0){
		if($this->mCnnFTP == null){ $this->FTPConnect(); }
		$archivo		= ($archivo == false) ? $this->mNombreArchivo : $archivo;
		$mfile			= $this->FTPGetFile($archivo, $persona);
		$rs				= "";
		$ext			= $this->getTipo($archivo);
		if($conteo > 4){
			$rs			= ($ext == $this->EXT_PDF) ? "<a class='button'><i class='fa fa-file-pdf-o fa-2x'></i>" . $archivo . "</a>"  : "<a class='button'><i class='fa fa-file-image-o fa-2x'></i>" . $archivo . "</a>";
		} else {
			$d64		= base64_encode($mfile);
			if($ext == $this->EXT_PDF){
				//$rs		= "<embed src=\"data:application/pdf;base64,$d64\" width=\"80%\" height=\"500\" alt=\"pdf\" type=\"application/pdf\" ></embed>";
				$rs		= "<object type=\"application/pdf\" data=\"data:application/pdf;base64,$d64\" width=\"90%\" height=\"500px\"></object>";
			} else {
				$ext	= strtolower($ext);
				$rs		= "<img src=\"data:image/$ext;base64,$d64\" width=\"90%\" height=\"500px\" />";
			}
		}
		return $rs;
	}
	function getEmbedByName($archivo = "", $prePath="", $conteo = 0){
		if($this->mCnnFTP == null){ $this->FTPConnect(); }
		$archivo		= ($archivo == "") ? $this->mNombreArchivo : $archivo;
		$mfile			= $this->FTPGetFile2($archivo, $prePath);
		$rs				= "";
		$ext			= $this->getTipo($archivo);
		$narchivo		= ($prePath == "") ? "../tmp/$archivo" : "../tmp/$prePath-$archivo";
		if($conteo > 4){
			$rs			= ($ext == $this->EXT_PDF) ? "<a class='button'><i class='fa fa-file-pdf-o fa-2x'></i>" . $archivo . "</a>"  : "<a class='button'><i class='fa fa-file-image-o fa-2x'></i>" . $archivo . "</a>";
		} else {
			//$d64		= base64_encode($mfile);
			if($ext == $this->EXT_PDF){
				//$rs		= "<embed src=\"data:application/pdf;base64,$d64\" width=\"80%\" height=\"500\" alt=\"pdf\" type=\"application/pdf\" ></embed>";
				$rs		= "<object type=\"application/pdf\" data=\"$narchivo\" width=\"90%\" height=\"500px\"></object>";
			} else {
				$ext	= strtolower($ext);
				$rs		= "<img src=\"$narchivo\" width=\"90%\" height=\"500px\" />";
			}
		}
		return $rs;
	}
	function FTPDeleteFile($archivo = "", $prePath = ""){
		if($this->mCnnFTP == null){ $this->FTPConnect(); }

		$mark			= ($prePath == "") ? "" : "$prePath-";
		$ruta_local		= PATH_HTDOCS . "/tmp/$mark" . $archivo;
		$ruta_ftp		= ($prePath == "") ? "./" . $archivo : "./$prePath/" . $archivo;
		$this->mPrePath	= ($prePath == "") ? $this->mPrePath : $prePath;

		if(is_file($ruta_local)){
			@unlink($ruta_local);
		}
		ftp_delete($this->mCnnFTP, $ruta_ftp);
		$this->setCleanCache();
	}
	function FTPGetFile2($archivo = "", $prePath = ""){
		if($this->mCnnFTP == null){ $this->FTPConnect(); }
		$archivo			= ($archivo == "") ? $this->mNombreArchivo : $archivo;
	
		if($prePath !== ""){ ftp_chdir($this->mCnnFTP, $prePath);	}
		$mark				= ($prePath == "") ? "" : "$prePath-";
		$ruta_completa		= PATH_HTDOCS . "/tmp/$mark" . $archivo;
		//TODO: 01/01/2015 Modificar 2014Nov19 mejorar en cache.- validar mejoras
		if(is_file($ruta_completa)){
				
		} else {
			$flocal 			= fopen( $ruta_completa, 'w');
			if (ftp_fget($this->mCnnFTP, $flocal, $archivo, FTP_BINARY, 0)) {
				//setLog( "Se ha escrito satisfactoriamente sobre $flocal");
			} else {
				setLog( "Ha habido un problema durante la descarga de $archivo en $flocal");
			}
		}
		//$data 				= file_get_contents($ruta_completa);
		$this->mRutaLocal		= $ruta_completa;
		return $ruta_completa;
	}
	function FTPGetFile($archivo = false, $persona = false){
		if($this->mCnnFTP == null){ $this->FTPConnect(); }
		$archivo			= ($archivo == false) ? $this->mNombreArchivo : $archivo;
		
		if($persona != false){ ftp_chdir($this->mCnnFTP, $persona);	}
		$mark				= ($persona == false) ? "" : "$persona-";
		
		$ruta_completa		= PATH_TMP . "/$mark" . $archivo;
		//TODO: 01/01/2015 Modificar 2014Nov19 mejorar en cache.- validar mejoras
		if(is_file($ruta_completa)){
			
		} else {
			$flocal 			= fopen( $ruta_completa, 'w');
			if (ftp_fget($this->mCnnFTP, $flocal, $archivo, FTP_BINARY, 0)) {
				//setLog( "Se ha escrito satisfactoriamente sobre $flocal");
			} else {
				setLog( "Ha habido un problema durante la descarga de $archivo en $flocal");
			}
		}
		$this->mRutaLocal		= $ruta_completa;
		$data 				= file_get_contents($ruta_completa);
		return $data;
	}
	function FTPConnect(){
		if(!function_exists("ftp_connect")){
			$conn_id 		= false;
		} else {
			$conn_id 		= ftp_connect(SYS_FTP_SERVER);
		}
		
		// iniciar sesión con nombre de usuario y contraseña
		if($conn_id){
			if(SYS_FTP_PWD !== "" AND SYS_FTP_USER !== ""){
				if(ftp_login($conn_id, SYS_FTP_USER, SYS_FTP_PWD)){
					$this->mCnnFTP	= $conn_id;
					$this->mReady	= true;
				}
			}
		}
		return $conn_id;		
	}
	/**
	 * Develve un array de los archivos existente en un directorio, opcionalmente pueden solo archivos
	 * @param string $dir Subdirectorio
	 * @param string $ext Extension
	 * @return array
	 */
	function FTPListFiles($dir = "", $ext = ""){
		$xCache			= new cCache();
		$this->mPrePath	= $dir;
		$mPath			= ($dir == "") ? "." : "./$dir/";
		
		$contents		= $xCache->get($this->mIdxFileL . $this->mPrePath);

		
		if(!is_array($contents)){		
			if($this->mCnnFTP == null){ $this->FTPConnect(); }
			//Obtener los archivos contenidos en el directorio actual
			$cnt 		= ($this->mReady == true) ? ftp_nlist($this->mCnnFTP, $mPath) : array();
			$contents	= array();
			foreach ($cnt as $idx => $nn){
				if($mPath !== "."){
					$nn		= str_replace($mPath, "", $nn);
				}
				
				
				$contents[$nn]	= $nn;
			}
			$cnt		= null;
			$xCache->set($this->mIdxFileL . $this->mPrePath, $contents);
		}
		if($ext !== ""){
			foreach ($contents as $idn => $nm){
				if(strpos($nm, $ext) == false){
					unset($contents[$idn]);	//eliminar el nodo sine extension
				}				
			}
		}
		return $contents;		
	}
	function FTPMakeDir($nombre){
		if($this->mCnnFTP == null){ $this->FTPConnect(); }
		ftp_mkdir ( $this->mCnnFTP , $nombre );
	}
	function FTPMove($documento = false, $persona = false){
		$ready				= true;
		if($this->mCnnFTP == null){ $this->FTPConnect(); }
		$documento			= ($documento == false) ? $this->mNombreArchivo : $documento;
		
		$persona			= setNoMenorQueCero($persona);
		$persona			= ($persona <= DEFAULT_SOCIO) ? $this->mPersona : $persona;
		$this->mPersona		= $persona;
		if($persona<= DEFAULT_SOCIO){
			$ready = false;
		} else {
			if(!ftp_chdir($this->mCnnFTP, $persona)){
				$this->FTPMakeDir($persona);
				ftp_chdir($this->mCnnFTP, $persona);
			}
			if(!ftp_rename($this->mCnnFTP, "../$documento", "./$documento")){
				$ready			= false;
				//setError("../$documento", "./$documento");
			}
		}
		if($ready == true){
			//Limpiar Cache
			$this->setCleanCache();
		}
		
		return $ready;
		
	}
	function FTPPersonaMvDoc($persona, $doc, $from, $to = ""){
		$persona			= setNoMenorQueCero($persona);
		$doc				= ($doc == false) ? $this->mNombreArchivo : $doc;
		
		$persona			= setNoMenorQueCero($persona);
		$persona			= ($persona <= DEFAULT_SOCIO) ? $this->mPersona : $persona;
		$this->mPersona		= $persona;
		$ready				= false;
		
		if($persona > DEFAULT_SOCIO){
			$odir			= "";
			$ndir			= "$persona" . "/" . "$to";
			if(!ftp_chdir($this->mCnnFTP, $persona)){
				$this->FTPMakeDir($persona);
				//ftp_chdir($this->mCnnFTP, $persona);
			}
			if(!ftp_chdir($this->mCnnFTP, $ndir)){
				$this->FTPMakeDir($ndir);
			}
			//Regresar al root
			ftp_chdir($this->mCnnFTP, '~');
			
			if(!ftp_rename($this->mCnnFTP, $from . $doc , $ndir . $doc )){
				$ready			= false;
				//setError("../$documento", "./$documento");
			} else {
				$this->mMessages .= "$persona\tMoviendo $doc de $from a $ndir\r\n";
				$ready			= true;
			}
			//ftp_chdir($ftp_conn, '~');
			//ftp_chdir($this->mCnnFTP, $ndir);
			
		}
		if($ready == true){
			//Limpiar Cache
			$this->setCleanCache();
		}
		return $ready;
	}
	function FTPUpload($documento, $newName="", $prePath = ""){
		$sucess			= true;
		$completePath	= "";
		$xLog			= new cCoreLog();
		
		if( is_array($documento) ){
			//Obtener Extension
			$DExt 	= explode(".", substr($documento['name'], -6));
			$mExt	= (isset($DExt[1])) ? $DExt[1] : "";
			
			if( ($mExt == "pdf") OR ($mExt == "png") OR ($mExt == "jpg")){
				$this->mNombreArchivo		= $documento['name'];
				$completePath				= PATH_TMP . $documento['name'];
				if(file_exists($completePath)==true){
					unlink($completePath);
					$xLog->add("WARN\tSE ELIMINO EL ARCHIVO " . $this->mNombreArchivo . "\r\n", $xLog->DEVELOPER);
				}
				if(move_uploaded_file($documento['tmp_name'], $completePath )) {
					$xLog->add("OK\tSE GUARDO EXITOSAMENTE EL ARCHIVO " . $this->mNombreArchivo . "\r\n", $xLog->DEVELOPER);
				} else {
					$xLog->add("ERROR\tSE FALLO AL GUARDAR (" . $this->mNombreArchivo . ")\r\n", $xLog->DEVELOPER);
					$sucess				= false;
				}
			}	else {
				$xLog->add("ERROR\tEL TIPO DE ARCHIVO DE " .$this->mNombreArchivo . "(" .$mExt . ") NO ES VALIDO\r\n");
				$sucess					= false;
			}
		} else {
			$xLog->add("ERROR\tEL ARCHIVO NO ES VALIDO $documento\r\n");
			$sucess					= false;
		}
		if($sucess == true){
			if($this->mCnnFTP == null){ $this->FTPConnect(); }
			if($this->mReady == true){
				$this->mNombreArchivo	= ($newName == "" ) ? $this->cleanNombreArchivo($this->mNombreArchivo) : $this->cleanNombreArchivo($newName) . "." . $mExt;
				
				if($prePath !== ""){ //Cambia el directorio si existe pre-path
					$this->mPrePath		= $prePath;
					$this->FTPChangeDir($prePath);
				}
				
				if (ftp_put($this->mCnnFTP, $this->mNombreArchivo, $completePath, FTP_BINARY)) {
					$xLog->add("OK\tSe ha enviado al servidor FTP el Archivo " . $this->mNombreArchivo . "\r\n", $xLog->DEVELOPER);
				} else {
					$xLog->add("ERROR\tNo se pudo enviar al servidor FTP el archivo " . $this->mNombreArchivo . "\r\n");
					$sucess				= false;
				}
			} else {
				$xLog->add("ERROR\tNo se encuentra al servidor FTP\r\n");
				$sucess				= false;	
			}
			//Limpiar Cache
			$this->setCleanCache();
		}
		$this->mMessages			.= $xLog->getMessages();
		//setError($this->mMessages);
		return $sucess;
	}
	function cleanNombreArchivo($f, $cleanExt=false){
		$xFS	= new cFileSystem();
		return $xFS->cleanNombreArchivo($f, $cleanExt);
	}
	function add($tipo, $pagina, $observaciones, $contrato = false, $persona = false, $fichero = "", $fecha = false, $Vencimiento = false){
		$persona	= setNoMenorQueCero($persona);
		$contrato	= setNoMenorQueCero($contrato);
		$xF			= new cFecha();
		$fecha		= $xF->getFechaISO( $fecha );
		$Vencimiento= $xF->getFechaISO($Vencimiento);
		$fichero	= trim($fichero);
		$fichero	= ($fichero == "") ? $this->mNombreArchivo : $fichero;
		$persona	= ($persona <= DEFAULT_SOCIO) ? $this->mPersona : $persona;
		
		$contrato	= ($contrato <= DEFAULT_CREDITO) ? DEFAULT_CREDITO : $contrato;
		//setLog($fecha);
		$fecha		= $xF->getInt($fecha);
		$user		= getUsuarioActual();
		$suc		= getSucursal();
		$ent		= EACP_CLAVE;
		$xDocP		= new cPersonasDocumentacion();
		if($tipo == $xDocP->TIPO_FOTO OR $tipo == $xDocP->TIPO_FIRMA){
			//$img		= "tmp/foto_" . $this->mClavePersona;
			//$fname		= PATH_HTDOCS . "/". $img;
			if(file_exists(PATH_HTDOCS . "/tmp/foto_" . $persona . ".jpg")){
				unlink(PATH_HTDOCS . "/tmp/foto_" . $persona . ".jpg");
			}
			if(file_exists(PATH_HTDOCS . "/tmp/foto_" . $persona . ".png")){
				unlink(PATH_HTDOCS . "/tmp/foto_" . $persona . ".png");
			}
			if(file_exists(PATH_HTDOCS . "/tmp/firma_" . $persona . ".jpg")){
				unlink(PATH_HTDOCS . "/tmp/firma_" . $persona . ".jpg");
			}
			if(file_exists(PATH_HTDOCS . "/tmp/firma_" . $persona . ".png")){
				unlink(PATH_HTDOCS . "/tmp/firma_" . $persona . ".png");
			}
			$this->mMessages		.= "WARN\tEliminar Foto o Firma de la Persona $persona\r\n";
		}
		$sql 		= "INSERT INTO personas_documentacion(
			clave_de_persona, tipo_de_documento, fecha_de_carga, observaciones, archivo_de_documento, valor_de_comprobacion, 
			estado_en_sistema, fecha_de_verificacion, oficial_que_verifico, 
			resultado_de_la_verificacion, notas, version_de_documento, numero_de_pagina, usuario, sucursal, entidad, documento_relacionado, vencimiento)
		VALUES($persona, $tipo, $fecha, '$observaciones', '$fichero', '',
		 1, 0, 0, 0, '', '', '$pagina', $user, '$suc', '$ent', $contrato, '$Vencimiento')";
		$xQL		= new MQL();
			$rs		= $xQL->setRawQuery($sql);
		if($rs === false){
			$this->mMessages		.= "ERROR\tEl Documento de la Persona $persona no se Guardo \r\n";
		} else {
			$this->mMessages		.= "OK\tDocumento de Persona $persona Guardado\r\n";
			//Agregar Eventos
			setAgregarEvento_($this->mMessages, 105, $persona, $contrato);
		}
		return $rs;
	}
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function getFileExists($file, $prePath = ""){
		$ext		= $this->getTipo($file);
		$file		= str_replace(".$ext", "", $file);
		$aFiles		= $this->FTPListFiles($prePath);
		$existe		= false;
		//setError("$file.jpg");
		//print_r($aFiles);

		if (in_array("$file.jpg", $aFiles)) {
			$this->mExt	= "jpg";
			$existe = true;
		}
		if (in_array("$file.png", $aFiles)) {
			$existe = true;
			$this->mExt	= "png";
		}
		if (in_array("$file.pdf", $aFiles)) {
			$existe = true;
			$this->mExt	= "pdf";
		}
		return $existe;
	}
	function getFileList($search, $prePath = ""){
		$arrLst		= array();
		$aFiles		= $this->FTPListFiles($prePath);
		//setLog($aFiles);
		foreach($aFiles as $ff => $ffid){
			
			if(strpos($ffid,$search) !== false){
				$arrLst[$ffid]	= $ffid;
			}
		}
		return $arrLst;
	}
	private function setCleanCache(){
		$xCache	= new cCache();
		$xCache->clean($this->mIdxFileL);
		$xCache->clean($this->mIdxFileL . $this->mPrePath);
	}
	private function FTPChangeDir($dir){
		$res	= true;
		if(!ftp_chdir($this->mCnnFTP, $dir)){
			$this->FTPMakeDir($dir);
			if(!ftp_chdir($this->mCnnFTP, $dir)){
				$res = false;
			}
		}
		return $res;
	}
	function getPathPorTipo($tipo){
		$prepath	= "";
		
		switch ($tipo){
			case 281: //Originación por Arrendamiento
				$prepath	= "originacion";
				break;
		}
		
		return $prepath;
	}
}


class cSistemaEquivalencias {
	private $mTabla	= "";
	private $mEquiv	= array();
	public $PLD_OPERACIONES		= "PLD.operaciones";
	
	function __construct($tabla = ""){
		$this->mTabla	= $tabla;
		if($tabla != ""){ $this->init(); }
	}
	function init($clasificacion = ""){
		$cls	= ($clasificacion == "") ? "" : " AND (`sistema_equivalencias`.`clasificacion` ='$clasificacion') ";
		$ql		= new MQL();
		$sql	= "SELECT * FROM `sistema_equivalencias` WHERE (`sistema_equivalencias`.`tabla` ='" .  $this->mTabla . "') $cls";
		$rs		= $ql->getDataRecord($sql);
		foreach ($rs as $row){
			$this->mEquiv[ strtolower($row["original"])]	= strtolower($row["equivalencia"]);
		}
	}
	function get($valor){
		$valor	= strtolower($valor);
		$equiv	= (isset($this->mEquiv[$valor])) ? $this->mEquiv[$valor] : null;
		
		return $equiv;
	}
	
}

class cReglasDeValidacion  {
	private $mValue		= "";
	function  __construct(){
		
	}
	function empresa($empresa = false){
		$empresa	= setNoMenorQueCero($empresa);
		$ok			= true;
		if(PERSONAS_CONTROLAR_POR_EMPRESA == true){
			if($empresa <= DEFAULT_SOCIO OR $empresa == DEFAULT_EMPRESA OR $empresa == FALLBACK_CLAVE_EMPRESA){
				$ok	= false;			
			}
		}
		if($empresa <= 0){
			$ok = false;
		}
		return $ok;
	}
	function cuenta($clave = false){
		$clave	= setNoMenorQueCero($clave);
		$ready	= true;
		if($clave <= FALLBACK_CLAVE_DE_DOCTO OR $clave == DEFAULT_CUENTA_CORRIENTE){ $ready = false; }
		return $ready;
	}
	function credito($clave = false){
		$clave	= setNoMenorQueCero($clave);
		$ready	= true;
		if($clave <= FALLBACK_CLAVE_DE_DOCTO OR $clave == DEFAULT_CREDITO OR $clave == FALLBACK_CLAVE_DE_CREDITO){ $ready = false; }
		return $ready;
	}
	function v(){
		return $this->mValue;
	}
	function sucursal($v){
		$v	= strtolower($v);
		$ready	= true;
		if($v == SYS_TODAS OR trim($v) == ""){
			$ready	= false;
		} else {
			$this->mValue	= $v;
		}
		return $ready; 
	}
	function recibo($v){
		$v	= setNoMenorQueCero($v);
		$ok	= true;
		if($v <= 0 ){ $ok = false; }
		return $ok;
	}
	function grupo($v){
		$v	= setNoMenorQueCero($v);
		$ok	= true;
		if(PERSONAS_CONTROLAR_POR_GRUPO == true){
			if($v == DEFAULT_GRUPO OR $v == FALLBACK_CLAVE_DE_GRUPO ){
				$ok		= false;
			}
		}
		if($v <= 0){
			$ok	= false;
		}
		return $ok;
	}
	function persona($persona = false){
		$persona	= setNoMenorQueCero($persona);
		$ok			= false;
		if($persona>0 AND $persona > DEFAULT_SOCIO){
			$ok		= true;
		}
		return $ok;
	}
}

class cCoreImport {
	private $mForceUTF		= false;
	private $mForceClean	= false;
	private $mNombreI		= "";
	private $mPrimerAp		= "";
	private $mSegundoAp		= "";
	private $mAcceso		= "";
	private $mNumeroExt		= "";
	private $mColonia		= "";
	private $mMunicipio		= "";
	private $mEntidadFed	= "";
	
	
	private $mCodigoPostal	= 0;
	private $mArrGenero	= array(
						"HOMBRE" 	=> 1,"MUJER" 	=> 2,
						"NINGUNO" 	=> 99,"" => 99,
						"MASCULINO" => 1,"MASCULINA" => 1,
						"FEMENINO"	=> 2,"FEMENINA"	=> 2,
						"H" 	=> 1,"M" 	=> 2
						);
	private $mArrFiguraJ	= array("PERSONA FISICA" 	=> 1,"PERSONA MORAL" 	=> 2,"FISICA" 	=> 1,"MORAL" 	=> 2,"NATURAL" 	=> 1,"JURIDICA" 	=> 2,""	=> 1,"NINGUNO"	=> 99, "F" => 1, "M"=> 2);
	private $mArrEstadoC	= array("CASADO" 	=> 1,"CASADA" 	=> 1,"SOLTERO" 	=> 2,"SOLTERA" 	=> 2,"NINGUNO" 	=> 99,"" 		=> 99,"DIVORCIADO" 	=> 3,"DIVORCIADA" 	=> 3,"UNION LIBRE" 	=> 4,"VIUDO" 	=> 6,"VIUDA" 	=> 6);
	private $mArrRegMat		= array("" => "NINGUNO","MANCOMUNADO" => "SOCIEDAD_CONYUGAL","SEPARADOS" => "BIENES_SEPARADOS");
	private $mRegVivienda	= array("PROPIA" =>1, "RENTADA"=>2, "NA"=>99, "NINGUNO" => 99);
	
	
	function __construct(){
		
	}
	function getGenero($v){ return $this->getV($v, DEFAULT_GENERO, MQL_INT, $this->mArrGenero);	}
	function setNombreCompleto($n, $inverso = false){
		$xLimp	= new cTiposLimpiadores();
		$DD		= $xLimp->cleanNombreComp($n, $inverso);
		$this->mNombreI		= $DD[2];
		$this->mPrimerAp	= $DD[0];
		$this->mSegundoAp	= $DD[1];
	}
	function setDireccionCompleta($n){
		$xLimp	= new cTiposLimpiadores();
		$n			= strtoupper($n);
		$n			= $xLimp->cleanString($n);
		//$arr		= array("AVENIDA", "CALLE", "CALE ", "CALLLE", "AVE.", "AVE ", "C.", "C ", "NUM.", "NUM ", "NO ", "NOM.", "SIN NUMERO", "SN", "S/N", "SIN NIM", "LOTE ", "#", "NO.");
		$arr		= array("NUM.", "NUM ", "NO ", "NOM.", "SIN NUMERO", "LOTE ", "#", "NO.", "NUMERO");
		$n			= str_replace($arr, ",", $n);
		$n			= trim(preg_replace('!\s+!', ' ', $n)); //quitar doble espacios
		//Calle Nombre Numero 46, Colonia 20 Noviembre, San Juan, Guanajuato, C.P. 24026
		$arrCP		= array("C.P.", "CP", "CODIGO POSTAL", "ZP");
		$DD			= explode(",", $n);
		$items		= count($DD);
		$indexCP	= null;
		foreach ($DD as $idx => $cnt){
			//Buscar Codigo Postal
			foreach ($arrCP as $idxb => $bbusq){
				if(strpos($cnt, $bbusq) !== false){
					$indexCP	= $idx;
					$cnt		= str_replace($arrCP, "", $cnt);
					$this->mCodigoPostal	= setNoMenorQueCero($cnt);
				}
			}
			
		}
		$this->mAcceso		= isset($DD[0]) ? $xLimp->cleanCalle($DD[0]) : "";
		$this->mNumeroExt	= isset($DD[1]) ? $DD[1] : "SN";
		$this->mColonia		= isset($DD[2]) ? $xLimp->cleanColonia($DD[2]) : "";
		$this->mMunicipio	= isset($DD[3]) ? $DD[3] : "";
		$this->mEntidadFed	= isset($DD[4]) ? $DD[4] : "";
		//$DD		= $xLimp->cleanNombreComp($n);
		return $n;
	}
	function getCalle(){ return $this->mAcceso; }
	function getNumeroExt(){ return $this->mNumeroExt; }
	function getMunicipio(){ return $this->mMunicipio; }
	function getEntidadFed(){ return $this->mEntidadFed; }
	function getCodigoPostal(){ return $this->mCodigoPostal; }
	function getColonia(){ return $this->mColonia; }
	function getPrimerAp(){ return $this->mPrimerAp; }
	function getSegundoAp(){ return $this->mSegundoAp; }
	function getNombre(){ return $this->mNombreI; } 
	function getEstadoCivil($v){ return $this->getV($v, DEFAULT_ESTADO_CIVIL, MQL_INT, $this->mArrEstadoC);}
	private function getV($valor, $fallback = null, $tipo = MQL_STRING, $equiv = false){
		$ret	= "";
		$xT		= new cTipos();
		if(is_array($equiv)){
			$ret	= (isset($equiv[$valor])) ? $equiv[$valor] : "";
		}
		switch ($tipo){
			case MQL_INT:
				$ret	= setNoMenorQueCero($ret);
				$ret	= ($ret <= 0 AND $fallback !== null) ? $fallback : $ret;
				break;
			case MQL_DATE:
				$xF		= new cFecha();
				$ret	= $xF->getFechaISO($ret);
				break;
			case MQL_BOOL:
				$ret	= $xT->cBool($ret);
				break;
			case MQL_FLOAT:
				$ret	= setNoMenorQueCero($ret);
				$ret	= ($ret <= 0 AND $fallback !== null) ? $fallback : $ret;
				break;
			default:
				$ret	= setCadenaVal($ret);
				$ret	= ($ret == "" AND $fallback !== null) ? $fallback : $ret;
				break;
		}
		return $ret;
	}
	function getRFC($v){
		$xR	= new cReglasDePais();
		return $xR->getValidIDFiscal($v);
		//return $this->getV($v, DEFAULT_PERSONAS_RFC_GENERICO, MQL_STRING);
	}
	function setOcupacion($n){
		
		$arr		= array("PREOFESOR", "PROFESOR (A)", "PROFESORA", "PROFESSOR");
		$n			= str_replace($arr, "PROFESOR(A)", $n);
		$arr		= array("LICENCIADA (O)", "LICENCIADO (A)", "BACHELOR", "BACHELOS", "LIC.", "LICENCIADA", "LICENCIADO");
		$n			= str_replace($arr, "LICENCIAD(O/A)", $n);
		$arr		= array("TECNICO (A)", "TECNICA (O)", "TECNICOS", "TECNICA", "TECNICO" );
		$n			= str_replace($arr, "TECNIC(O/A)", $n);
		$arr		= array("INGENIERA", "INGENIERO (A)", "INGENIERO" );
		$n			= str_replace($arr, "INGENIER(O/A)", $n);
		
		$arr		= array(" OF ");
		$n			= str_replace($arr, " DE ", $n);

		$arr		= array("ARTES");
		$n			= str_replace($arr, "ARTES", $n);		

		$arr		= array("UNIVERSITARIOS","UNIVERCITARIO", "UNIVERSITARIA", "UNIVERSITARIO (A)", "UNIVERSITARIO");
		$n			= str_replace($arr, "UNIVERSITARI(O/A)", $n);		
		//
		$arr		= array("PEDAGIGIA", "PEDAG.");
		$n			= str_replace($arr, "PEDAGOGIA", $n);
		$arr		= array("ADMON.");
		$n			= str_replace($arr, "ADMINISTRACION", $n);
		
		$arr		= array("HISTORUA");
		$n			= str_replace($arr, "HISTORIA", $n);
				
		$arr		= array("EDUC.", "EDUCACCION");
		$n			= str_replace($arr, "EDUCACION", $n);		
		
		
		$n			= trim(preg_replace('!\s+!', ' ', $n)); //quitar doble espacios
		
		return $n;
	}
}

class cTiempoAntiguedad {
	private $mClave			= false;
	private $mObj			= null;
	private $mInit			= false;
	private $mNombre		= "";
	private $mMessages		= "";
	private $mIDCache		= "";
	private $mTabla			= "socios_tiempo";
	private $mTipo			= 0;
	private $mValorArraigoE	= 0; 
	private $mValorArraigoD	= 0; //Arraigo Dom
	
	function __construct($clave = false){ $this->mClave	= setNoMenorQueCero($clave); $this->setIDCache($this->mClave); }
	function getIDCache(){ return $this->mIDCache; }
	function setIDCache($clave = 0){
		$clave = ($clave <= 0) ? $this->mClave : $clave;
		$clave = ($clave <= 0) ? microtime() : $clave;
		$this->mIDCache	= $this->mTabla . "-" . $clave;
	}
	private function setCleanCache(){if($this->mIDCache !== ""){ $xCache = new cCache(); $xCache->clean($this->mIDCache); } }
	function init($data = false){
		$xCache		= new cCache();
		$inCache	= true;
		$xT			= new cSocios_tiempo();//Tabla
		
		
		if(!is_array($data)){
			$data	= $xCache->get($this->mIDCache);
			if(!is_array($data)){
				$xQL		= new MQL();
				$data		= $xQL->getDataRow("SELECT * FROM `" . $this->mTabla . "` WHERE `" . $xT->getKey() . "`=". $this->mClave . " LIMIT 0,1");
				$inCache	= false;
			}
		}
		if(isset($data[$xT->getKey()])){
			$xT->setData($data);
			
			$this->mClave			= $data[$xT->IDSOCIOS_TIEMPO];
			$this->mNombre			= $data[$xT->DESCRIPCION_TIEMPO];
			$this->mValorArraigoE	= $data[$xT->VALOR_ARRAIGO_ECONOMICO];
			$this->mValorArraigoD	= $data[$xT->VALOR_ARRAIGO_RESIDENCIAL];
			$this->mObj		= $xT;
			$this->setIDCache($this->mClave);
			if($inCache == false){	//Si es Cache no se Guarda en Cache
				$xCache->set($this->mIDCache, $data, $xCache->EXPIRA_UNDIA);
			}
			$this->mInit	= true;
			$xT 			= null;
		}
		return $this->mInit;
	}
	function getObj(){ if($this->mObj == null){ $this->init(); }; return $this->mObj; }
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function __destruct(){ $this->mObj = null; $this->mMessages	= "";	}
	function getNombre(){return $this->mNombre;}
	function getClave(){return $this->mClave;}
	function setCuandoSeActualiza(){ $this->setCleanCache(); }
	function add(){}
	function getValorPorFecha($fecha = false, $FechaFinal = false){
		$xF			= new cFecha();
		$xF->set($fecha);
		$FechaFinal	= $xF->getFechaISO($FechaFinal);
		
		$dias		= $xF->setRestarFechas($FechaFinal, $fecha);
		$sql		= "SELECT * FROM `socios_tiempo` WHERE `idsocios_tiempo` >= $dias ORDER BY `idsocios_tiempo` DESC LIMIT 0,1";
		$xQL		= new MQL();
		$data		= $xQL->getDataRow($sql);
		if(isset($data["idsocios_tiempo"])){
			$this->mClave	= $data["idsocios_tiempo"];
		}
		$xQL		= null;
		
		return $this->init($data);
	}
}

class cCouchDB {
	public $MURL		= "http://pruebas:pruebas@localhost:5984";
	public $MDB			= "safeosms";
	private $mMessages	= "";
	private $mCnn		= null;
	private $mVista		= "tablanosync1";
	
	public $SYNC_VISTA1	= "tablanosync1";
	public $SYNC_VISTA2	= "tablanosync2";
	
	function __construct(){
		$this->MURL		= SVC_URL_COUCHDB;
		$this->MDB		= SVC_DB_COUCHDB;
		$this->mVista	= SVC_VIEW_COUCHDB;
	}
	function getTablaNoSync($tabla){
		if($this->mCnn === null){ $this->getCnn(); }
		$arr	= array(
				"startkey" => $tabla,
				"endkey" => $tabla
		);
		$this->mCnn->setQueryParameters($arr);
		
		$data	= $this->mCnn->getView($this->mVista, "porTabla");
		
		return $data->rows;
	}
	function getTablaCatalogo($tabla){
		if($this->mCnn === null){ $this->getCnn(); }
		$arr	= array(
				"startkey" => $tabla,
				"endkey" => $tabla
		);
		$this->mCnn->setQueryParameters($arr);
		
		$data	= $this->mCnn->getView("catalogos", "porTabla");
		
		return $data->rows;
	}
	function getCnn(){
		
		$this->mCnn		= new PHPOnCouch\CouchClient($this->MURL,$this->MDB);
		
		return $this->mCnn;
	}
	function setCnn($cnn){$this->mCnn = $cnn;}
	function getDoc($id){
		if($this->mCnn === null){ $this->getCnn(); }
		$doc		= false;
		try {
			$doc 	= $this->mCnn->getDoc($id);
		} catch ( Exception $e ) {
			if ( $e->getCode() == 404 ) {
				$this->mMessages	.= "Document some_doc_id does not exist !";
			}
		}
		return $doc;
	}
	function delDoc($id, $doc = null){
		if($this->mCnn === null){ $this->getCnn(); }
		$res	= false;
		if($doc === null){
			$doc	= $this->getDoc($id);
		}
		if($doc === null){
			
		} else {
			$this->mCnn->deleteDoc($doc);
		}
		
	}
	function setDoc($docx){
		$id			= 0;
		$H_id		= $docx->_id;
		$save		= false;
		$update		= true;
		$doc		= false;
		if($this->mCnn === null){ $this->getCnn(); }
		// get the document
		try {
			$doc = $this->mCnn->getDoc($H_id);
		} catch (Exception $e) {
			//$this->mMessages	.= "ERROR: ".$e->getMessage()." (".$e->getCode().")<br>\n";
			//El Documento no existe
			if($e->getCode() == 404){
				$save	= true;
			}
		}
		if($save == true){
			try {
				$response 	= $this->mCnn->storeDoc($docx);
				$id			= $response->id;
			} catch (Exception $e) {
				$this->mMessages	.= "ERROR: ".$e->getMessage()." (".$e->getCode().")<br>\n";
			}
			$docx			= null;
			$doc			= null;
		} else {
			//try update
			$arr1		= $this->objectToArray($docx);
			$arr2		= $this->objectToArray($doc);
			foreach ($arr1 as $idx => $vx){
				$arr2[$idx]	= $vx;
			}
			$doc2			= $this->arrayToObject($arr2);
			
			try {
				$response 	= $this->mCnn->storeDoc($doc2);
				$id			= $response->id;
			} catch (Exception $e) {
				$this->mMessages	.= "ERROR: ".$e->getMessage()." (".$e->getCode().")<br>\n";
			}
			
			$arr1		= null;
			$arr2		= null;
			$doc2		= null;
			$docx		= null;
		}
		
		// make changes
		/*$doc->title = 'Some smart content';
		$doc->tags = array('twitter','facebook','msn');
		
		if($doc){
			
		}
		

		echo "Doc recorded. id = ".$response->id." and revision = ".$response->rev."<br>\n";*/
		// Doc recorded. id = BlogPost6576 and revision = 2-456769086
		
		//$doc->_id
		/*try {
			$doc = $client->conflicts()->getDoc("some_doc_id");
		} catch ( Exception $e ) {
			if ( $e->getCode() == 404 ) {
				echo "Document some_doc_id does not exist !";
			}
			exit(1);
		}*/
		
		/*try {
			$response 	= $this->mCnn->storeDoc($doc);
			$id			= $response->id;
		} catch (Exception $e) {
			$this->mMessages	= "ERROR: ".$e->getMessage()." (".$e->getCode().")\n";
		}*/
		//echo "Doc recorded. id = ".$response->id." and revision = ".$response->rev."<br>\n";
		$id;
	}
	function getMessages($put = OUT_TXT){ $xH = new cHObject(); return $xH->Out($this->mMessages, $put); }
	function getArchivo($doc){
		if($this->mCnn === null){ $this->getCnn(); }
		$obj	= false;
		try {
			$obj	= $this->mCnn->getAttachment($doc, "imagen.jpg");
		} catch (Exception $e){
			$this->mMessages .= ' '.  $e->getMessage() . "\n";
		}
		//setError($this->mMessages);
		$doc		= null;
		return $obj;
	}
	function getDoctosByIdInterno($id){
		if($this->mCnn === null){ $this->getCnn(); }
		$arr	= array(
				"startkey" => $id,
				"endkey" => $id
		);
		$this->mCnn->setQueryParameters($arr);
		
		$data	= $this->mCnn->getView("totals", "doctosByIdinterno");
		
		return $data->rows;
		
	}
	function setImporDoctoByIDInterno($id, $idpersona){
		if($this->mCnn === null){ $this->getCnn(); }
		
		$xF			= new cFecha();
		$doc		= $this->getDoc($id);
		
		
		$persona	= $idpersona;
		
		$docto		= DEFAULT_CREDITO;
		if(property_exists($doc, 'idcontrato') == true){
			$docto	= setNoMenorQueCero($doc->idcontrato);
		}
		$tipo		= 9999;
		if(property_exists($doc, 'idtipo') == true){
			$tipo		= setNoMenorQueCero($doc->idtipo);
		}
		$time		= time();
		
		$nid		= setCadenaVal($id) . ".jpg";
		$xLog		= new cCoreLog();
		$ready		= true;
		
		
		
		//$doc		= $dd->value;
		$file		= $this->getArchivo($doc);
		$fechacarga	= $xF->getFechaByInt($time);
		$xpath		= PATH_TMP . "tmp-" . $nid;
		//$xDoc->FTPUpload($documento);
		if($file){
			if(file_put_contents($xpath, $file)){
				$archivo["name"] 		= $nid;
				$archivo["tmp_name"] 	= $xpath;
				$pagina					= $doc->pagina;
				$observaciones			= $doc->observaciones;
				$fecha					= $xF->getFechaByInt($time);
				//$documento['tmp_name']
				
				$xSoc	= new cSocio($persona);
				if($xSoc->init() == true){
					$xDoc		=  new cDocumentos($nid);
					if($xDoc->FTPConnect()){
						if (ftp_put($xDoc->FTPConnect(), $nid, $xpath, FTP_BINARY)) {
							$xLog->add("OK\tSe ha enviado al servidor FTP el Archivo " . $nid . "\r\n", $xLog->DEVELOPER);
						} else {
							$xLog->add("ERROR\tNo se pudo enviar al servidor FTP el archivo " . $nid . "\r\n");
							$ready				= false;
						}
						
					}
					if($ready == true){
						$ready			= $xDoc->FTPMove($nid, $persona);
						if($ready == true){
							$ready		= $xDoc->add($tipo, $pagina, $observaciones, $docto, $persona, $nid, $fecha, false);
							//Actualizar ID
							//$this->delDoc($id);
							//Actualizar ID de Entidad Registro
							$idinterno1		= setNoMenorQueCero($doc->idtemp->entidad1);
							$idinterno2		= setNoMenorQueCero($doc->idtemp->entidad2);
							if($this->mVista == $this->SYNC_VISTA1){
								$doc->idtemp->entidad1	= $xSoc->getClaveDePersona();
							} else {
								$doc->idtemp->entidad2	= $xSoc->getClaveDePersona();
							}
							$this->setDoc($doc);
						}
						
					}
					
					$xLog->add($xDoc->getMessages());
				}
				
				$this->mMessages	.= $xLog->getMessages();
				//setLog($this->mMessages);
			}
		}
		return $ready;
	}
	private function objectToArray($d) {
		if (is_object($d)) {
			$d = get_object_vars($d);
		}
		return $d;
	}
	private function arrayToObject($d) {
		return (object) $d;
	}
	function getCleanID($id){
		$str	= str_replace(":", "_", $id);
		$str	= str_replace("@", "_", $str);
		$str	= str_replace(".", "_", $str);
		return $str;
	}
	/*function getQuery($str, $tabla){
		if($this->mCnn === null){ $this->getCnn(); }
		$arr	= array(
				"startkey" => $tabla,
				"endkey" => $tabla
		);
		$this->mCnn->setQueryParameters($arr);
		
		$data	= $this->mCnn->getView($this->mVista, "porTabla");
		
		return $data->rows;
	}*/
	function setRawQuery($arr){
		/*curl -H 'Content-Type: application/json' \
            -X POST http://127.0.0.1:5984/demo \
            -d '{"company": "Example, Inc."}'
            */
		//$ch = curl_init();
		
		/*$customer = array(
				'firstname' => 'Branko',
				'lastname' => 'Ajzele',
				'username' => 'ajzele',
				'email' => 'branko.ajzele@example.com',
				'pass' => md5('myPass123')
		);*/
		
		//$payload = json_encode($customer);
		
		//curl_setopt($ch, CURLOPT_URL, 'http://127.0.0.1:5984/customers/'.$customer['username']);
		//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT'); /* or PUT */
		//curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		//		'Content-type: application/json',
		//		'Accept: */*'
		//));
		
		//curl_setopt($ch, CURLOPT_USERPWD, 'myDBusername:myDBpass');
		
		//$response = curl_exec($ch);
		
		//curl_close($ch);
		//curl -X PUT http://127.0.0.1:5984/reviews/01 -d '{"reviewer_name":"Ben", "stars":"5", "details":"Love the calzone!"}'
		
		
	}
	function setCreateVistas(){
/*{
  "_id" : "_design/example",
  "views" : {
    "foo" : {
      "map" : "function(doc){ emit(doc._id, doc._rev)}"
    }
  }
}*/
		/*    tablanosync1 : {
        _id: '_design/tablanosync1',
        views: {
          porTabla : {
            map: function (doc) {
                if(doc.idtemp.entidad1 == ""){
                    emit(doc.tabla, doc);
                }
            }.toString()
          }
        }
    }*/
		/*    catalogos : {
        _id: '_design/catalogos',
        views: {
          porTabla : {
            map: function (doc) { emit(doc.tabla, doc) }.toString()
          }
        }
	},*/
		$this->setRunVista("catalogos", '{"views":{"porTabla":{"map":"function (doc) { emit(doc.tabla, doc) }"} } }');
		//$this->setRunVista("tablanosync1", '{"views":{"porTabla":{ "map":"function(doc){ if(doc.idtemp.entidad1 == \'\'){ emit(doc.tabla, doc); } }" } } }');
	}
	private function setRunVista($nombre, $contenido){
		$xSys	= new cSystemTask();
		$cnt	= "Content-Type: application/json";
		$server	= $this->MURL;
		$db		= $this->MDB;
		
		//curl -X PUT http://localhost:5984/dev-task/_design/task -d @task.json
		//setLog("curl -H 'Accept: application/json' -H '$cnt' -X PUT '$server" . $db . "/_design/$nombre' -d '$contenido'");
		$xSys->runcmd("curl -H 'Accept: application/json' -H '$cnt' -X PUT $server" . $db . "/_design/$nombre -d '$contenido'");
	}
} 

//================================================================ JSON
function Memory_Usage($decimals = 2)
{
    $result = 0;

    if (function_exists('memory_get_usage'))
    {
        $result = memory_get_usage() / 1024;
    }

    else
    {
        if (function_exists('exec'))
        {
            $output = array();

            if (substr(strtoupper(PHP_OS), 0, 3) == 'WIN')
            {
                exec('tasklist /FI "PID eq ' . getmypid() . '" /FO LIST', $output);

                $result = preg_replace('/[\D]/', '', $output[5]);
            }

            else
            {
                exec('ps -eo%mem,rss,pid | grep ' . getmypid(), $output);

                $output = explode('  ', $output[0]);

                $result = $output[1];
            }
        }
    }

    return number_format(intval($result) / 1024, $decimals, '.', '');
}								
?>