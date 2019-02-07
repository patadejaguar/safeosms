<?php
include_once("core.config.inc.php");
include_once("entidad.datos.php");
include_once("core.db.dic.php");
include_once("core.db.inc.php");

include_once("core.deprecated.inc.php");
include_once("core.fechas.inc.php");
include_once("core.html.inc.php");

include_once("core.common.inc.php");
include_once("core.personas.inc.php");

include_once("core.creditos.inc.php");
include_once("core.creditos.utils.inc.php");
include_once("core.operaciones.inc.php");


//=====================================================================================================
class cPersonasEstadisticas {
	private $mPersona		= false;
	private $mTCredsSaldo			= 0;
	private $mTCredsAut				= 0;
	private $mTCredsNum				= 0;
	private $mTCredsActivos			= 0;
	private $mTCredsActivosAut		= 0;
	private $mAListaDeCreds			= array();
	private $mCreditoPrioritario	= 0;
	private $mTCuentasCaptacion		= 0;
	private $mTotalCompromisos		= 0;
	function __construct($clave_de_persona){
		$this->mPersona	= $clave_de_persona;
	}
	/**
	 * Retorna un Listado en Formato Pedido de los Creditos Actuales
	 */
	function initDatosDeCredito($SoloEstadisticas = false){
		$xCache		= new cCache();
		$idxc		= "personas-creditos-estadisticas-". $this->mPersona;
		$idxe		= "tmp_personas_estadisticas-". $this->mPersona;
		$inCache	= true;
		if($SoloEstadisticas == true){
			$data	= $xCache->get($idxe);
			if(!is_array($data)){
				$mql		= new MQL();
				$data		= $mql->getDataRow("SELECT * FROM `tmp_personas_estadisticas` WHERE `persona`=" . $this->mPersona . " LIMIT 0,1");
				$inCache	= false;
				$mql		= null;
			}
			if(isset($data["persona"])){
				$this->mTCredsActivos		= $data["creditos_con_saldo"];
				$this->mTCredsSaldo			= $data["total_actual"];
				$this->mTCredsActivosAut	= $data["total_autorizado"];
				$this->mCreditoPrioritario	= $data["credito_activo"];
				$this->mTCredsNum			= $data["creditos"];
				$this->mTCuentasCaptacion	= $data["cuentas"];
				if($inCache == false){
					$xCache->set($idxe, $data, $xCache->EXPIRA_5MIN);
				}
			}
			$this->mTotalCompromisos		+= $this->mTCredsNum;
			$this->mTotalCompromisos		+= $this->mTCuentasCaptacion;
		} else {
			$sql	= "SELECT `creditos_solicitud`.* FROM `creditos_solicitud` WHERE (`creditos_solicitud`.`numero_socio` =" . $this->mPersona . ") ORDER BY `creditos_solicitud`.`saldo_actual` DESC,`creditos_solicitud`.`fecha_ministracion` DESC";
			$xCred	= new cCreditos_solicitud();
			$data	= $xCache->get($idxc);
			if(!is_array($data)){
				$mql		= new MQL();
				$data		= $mql->getDataRecord($sql);
				$mql		= null;
				$inCache	= false;
			}
			if(isset($data[$xCred->NUMERO_SOCIO])){
				foreach ($data as $row){
					$xCred->setData($row);
					$monto		= $row[$xCred->SALDO_ACTUAL];
					$credito	= $row[$xCred->NUMERO_SOLICITUD];
					$aut		= $row[$xCred->MONTO_AUTORIZADO];
					
					if( $monto > TOLERANCIA_SALDOS ){
						$this->mTCredsActivos++;
						$this->mTCredsSaldo 				+= $monto;
						$this->mAListaDeCreds[$credito] 	= $row;
						$this->mTCredsActivosAut 			+= $aut;
						//TODO: Acompletar
					}
					if($this->mCreditoPrioritario <= DEFAULT_CREDITO){
						$this->mCreditoPrioritario			= $credito;
					}
					$this->mTCredsNum++;
				}
				if($inCache == false){
					$xCache->set($idxc, $data, $xCache->EXPIRA_5MIN);
				}
			}
		}
		return  $this->mAListaDeCreds;//temporal
	}
	function getDatosDeCreditos(){ return $this->mAListaDeCreds; }
	function getTotalCreditosSaldo(){ return $this->mTCredsSaldo;}
	function getTotalCreditosActivos(){ return $this->mTCredsActivos; }
	function getTotalCreditosActivosAutorizado(){ return $this->mTCredsActivosAut; }
	function getTotalColocacionActual($tipo_de_convenio = false){
		$datos				= array();
		$ByConvenio			= "";
		$tipo_de_convenio	= setNoMenorQueCero($tipo_de_convenio);
		if ( $tipo_de_convenio > 0 ){
			$ByConvenio		= " AND (`creditos_solicitud`.`tipo_convenio` = $tipo_de_convenio) ";
		}
		$sql = "SELECT
					`creditos_solicitud`.`numero_socio`,
					COUNT(`creditos_solicitud`.`numero_solicitud`) AS `numero`,
					SUM(`creditos_solicitud`.`monto_autorizado`) AS `monto`,
					AVG(`creditos_solicitud`.`dias_autorizados`) AS `dias`,
					SUM(`creditos_solicitud`.`saldo_actual`) AS `saldo`
				FROM
					`creditos_solicitud` `creditos_solicitud`
				WHERE
					(`creditos_solicitud`.`numero_socio` =" . $this->mPersona. ")
						$ByConvenio
						GROUP BY
						`creditos_solicitud`.`numero_socio`";
		$datos	= obten_filas($sql);
		if(!isset($datos["numero"])){
			$datos[SYS_NUMERO]	= 0;
			$datos[SYS_MONTO]	= 0;
			$datos["dias"]		= 0;
			$datos["saldo"]		= 0;
			$datos[SYS_SALDO]	= 0;
		} else {
			$datos[SYS_SALDO]		= $datos["saldo"];
		}
		return $datos;
	}
	function getTotalCuentasCaptacion(){return $this->mTCuentasCaptacion; }
	function getTotalCompromisos(){ return $this->mTotalCompromisos; }
	function getTotalCaptacionActual(){
		$sql 	= "SELECT
			`captacion_cuentas`.`numero_socio`,
			SUM(`captacion_cuentas`.`saldo_cuenta`) AS 'saldo'
			FROM
			`captacion_cuentas` `captacion_cuentas`
			WHERE (`captacion_cuentas`.`numero_socio`=" . $this->mPersona . ") GROUP BY `captacion_cuentas`.`numero_socio`";
		$datos 	= obten_filas($sql);
		if(!isset($datos["saldo"])){
			$datos[SYS_NUMERO]		= 0;
			$datos[SYS_MONTO]		= 0;
			$datos["dias"]			= 0;
			$datos["saldo"]			= 0;
			$datos[SYS_SALDO]		= 0;
		} else {
			$datos[SYS_SALDO]		= $datos["saldo"];
		}
		return $datos;
	}
	function getDatosAvalesOtorgados(){
		$sql	= "SELECT

				COUNT(`socios_relaciones`.`socio_relacionado`) AS `relaciones`,
				COUNT(`creditos_solicitud`.`numero_solicitud`) AS `creditos`,
				SUM(`creditos_solicitud`.`saldo_actual`)       AS `monto`, 
				SUM((`creditos_solicitud`.`monto_autorizado` / `creditos_solicitud`.`dias_autorizados`)) AS `diario`				
			FROM
				`socios_relaciones` `socios_relaciones`
					INNER JOIN `eacp_config_bases_de_integracion_miembros`
					`eacp_config_bases_de_integracion_miembros`
					ON `socios_relaciones`.`tipo_relacion` =
					`eacp_config_bases_de_integracion_miembros`.`miembro`
						INNER JOIN `creditos_solicitud` `creditos_solicitud`
						ON `socios_relaciones`.`credito_relacionado` = `creditos_solicitud`.
						`numero_solicitud`
			WHERE
				(`socios_relaciones`.`numero_socio` =" .  $this->mPersona . ") AND

				(`creditos_solicitud`.`saldo_actual` >" . TOLERANCIA_SALDOS . ") AND
				(`eacp_config_bases_de_integracion_miembros`.`codigo_de_base` = 5002)
			GROUP BY
				`eacp_config_bases_de_integracion_miembros`.`codigo_de_base`,
				`socios_relaciones`.`numero_socio`	";
		
		$datos 		= obten_filas($sql);
			if(!isset($datos["monto"])){
			$datos[SYS_NUMERO]		= 0;
			$datos[SYS_MONTO]		= 0;
			$datos[SYS_SALDO]		= 0;
			$datos["relaciones"]	= 0;
			$datos["diario"]	= 0;
		} else {
			$datos[SYS_SALDO]		= $datos["monto"];
			$datos[SYS_NUMERO]		= $datos["creditos"];
		}
		return $datos;		
	}
	function getDatosDependientesEconomicos(){
		$sql	= "
SELECT
	
				COUNT(`socios_relaciones`.`numero_socio`) AS `relaciones`,
				COUNT(`creditos_solicitud`.`numero_solicitud`) AS `creditos`,
				SUM(`creditos_solicitud`.`saldo_actual`)       AS `monto`,
				SUM((`creditos_solicitud`.`monto_autorizado` / `creditos_solicitud`.`dias_autorizados`)) AS `diario`
FROM
	`creditos_solicitud` `creditos_solicitud` 
		INNER JOIN `socios_relaciones` `socios_relaciones` 
		ON `creditos_solicitud`.`numero_socio` = `socios_relaciones`.
		`numero_socio` 
WHERE
				(`socios_relaciones`.`socio_relacionado` =" .  $this->mPersona . ") AND
	
				(`creditos_solicitud`.`saldo_actual` >" . TOLERANCIA_SALDOS . ") 
				AND
				(`socios_relaciones`.`dependiente` = '1')
GROUP BY
	`socios_relaciones`.`socio_relacionado`
	";
		//setLog($sql);
		$datos 		= obten_filas($sql);
		if(!isset($datos["monto"])){
			$datos[SYS_NUMERO]		= 0;
			$datos[SYS_MONTO]		= 0;
			$datos[SYS_SALDO]		= 0;
			$datos["relaciones"]	= 0;
			$datos["diario"]	= 0;
		} else {
			$datos[SYS_SALDO]		= $datos["monto"];
			$datos[SYS_NUMERO]		= $datos["creditos"];
		}
		return $datos;
	}
	function getDatosPatrimonioActivo(){
		$sql = "SELECT
			`socios_patrimonio`.`socio_patrimonio`,
			COUNT(`socios_patrimonio`.`idsocios_patrimonio`) AS `articulos`,
			MAX(`socios_patrimonio`.`fecha_expiracion`) AS `fecha`,
			SUM(`socios_patrimonio`.`monto_patrimonio`) AS `monto`,
			SUM(IF(`socios_patrimonio`.`monto_patrimonio` < 0,0, `socios_patrimonio`.`monto_patrimonio`)) AS  `activo`,
			SUM(IF(`socios_patrimonio`.`monto_patrimonio` < 0,(`socios_patrimonio`.`monto_patrimonio` * `socios_patrimonio`.`afectacion_patrimonio`),0)) AS  `pasivo`,
			`socios_patrimonio`.`estatus_actual`
			 
		FROM
			`socios_patrimonio` `socios_patrimonio` 
		WHERE
			(`socios_patrimonio`.`socio_patrimonio` =" .  $this->mPersona . ") AND
			(`socios_patrimonio`.`fecha_expiracion` >NOW()) 
		GROUP BY
			`socios_patrimonio`.`socio_patrimonio`";
		$datos 						= obten_filas($sql);
		if(!isset($datos["monto"])){
			$datos["activo"]		= 0;
			$datos["pasivo"]		= 0;
			$datos["capital"]		= 0;
			$datos["fecha"]			= fechasys();
			$datos["articulos"]		= 0;
			$datos[SYS_MONTO]		= 0;
		}
		return $datos;
	}
	function getCreditoPrioritario(){ return $this->mCreditoPrioritario; }
	function getTotalDomicilios(){ 
		$xQL	= new MQL();
		$numero	= setNoMenorQueCero($xQL->getDataValue("SELECT COUNT(*) AS `numero` FROM `socios_vivienda` WHERE `socio_numero`=" . $this->mPersona, "numero"));
		$xQL	= null;
		return $numero;
	}
}

class cPersonasUtilerias {
	function __construct(){}
	function setCorregirDomicilios($correcion = false){
		//obtener codigo postal
		$msg			= "";
		//verificar si existe persona
		$ql				= new MQL();
		
		$xViv			= new cSocios_vivienda();
		$xT				= new cTipos();
		$arrCP			= array();
		$arrLocal		= array();
		$xLog			= new cCoreLog();
		$xT->setForceMayus();
		$xT->setToUTF8();
		$xT->setForceClean();
		$ql->setRawQuery("CALL `sp_correcciones`()");
		$rs				= $ql->getDataRecord("SELECT * FROM `socios_vivienda`");
		
		foreach ($rs as $rows){
			$xViv->setData($rows);
			//codigo_postal
			$codigo_postal	= $xViv->codigo_postal()->v();
			$id				= $xViv->idsocios_vivienda()->v();
			$nombrecolonia	= trim($xViv->colonia()->v());
			
			if(setNoMenorQueCero($codigo_postal) > 0){
				$xTmp		= new cTmp_colonias_activas();
				if(isset($arrCP[$codigo_postal])){
					$xLog->add("WARN\tCargar Colonia en Memoria $codigo_postal \r\n", $xLog->DEVELOPER);
					//var_dump($arrCP[$codigo_postal]); exit;
					$xTmp->setData($arrCP[$codigo_postal]);
				} else {
					$xTmp->setData( $xTmp->query()->initByID($codigo_postal) );
					if($xTmp->codigo_postal()->v() > 0){
						$xLog->add("WARN\tBuscar Colonia en Memoria $codigo_postal \r\n", $xLog->DEVELOPER);
						$arrCP[$codigo_postal] 	= $xTmp->query()->getCampos(true);
					}
				}
				if($xTmp->codigo_postal()->v() <= 0){
					$xLog->add("ERROR\tAl actualizar la Vivienda con CP $codigo_postal \r\n");
				} else {
					$nmunicipio		= setCadenaVal($xTmp->nombre_estado()->v());
					$nestado		= setCadenaVal($xTmp->nombre_municipio()->v() );
					$ncolonia		= setCadenaVal($xTmp->nombre()->v());
					$xLog->add("OK\tActualizar CP $codigo_postal $ncolonia en $nmunicipio de $nestado \r\n");
					//validar si el nombre es corto
					$xsize			= strlen($nombrecolonia);
					if($xsize <= 10){
						if($xsize <= 3){
							$xViv->colonia($ncolonia);
						} else {
							$sql		= "SELECT * FROM general_colonias WHERE codigo_postal = $codigo_postal AND nombre_colonia LIKE '%$nombrecolonia%' LIMIT 0,1";
							$D			= $ql->getDataRow($sql);
							if(isset($D["nombre_colonia"])){
								$xViv->colonia($D["nombre_colonia"]);
							} else {
								$xViv->colonia($ncolonia);
							}
						}
					}

					$xViv->estado( $nmunicipio );
					$xViv->municipio( $nestado );
					$idlocalidad	= $xViv->clave_de_localidad()->v();
					$xLoc			= new cDomicilioLocalidad($idlocalidad);
					//validar localidad
					if(isset($arrLocal[$idlocalidad])){
							$xLoc->init($arrLocal[$idlocalidad]);
							$xLog->add("WARN\tCargar localidad en Memoria $idlocalidad \r\n", $xLog->DEVELOPER);
					} else {
						if($xLoc->init() == true){
							$xLog->add("WARN\tIniciar localidad $idlocalidad \r\n", $xLog->DEVELOPER);
							$arrLocal[$idlocalidad]	= $xLoc->getDatosInArray();	
						} else {
							$xLog->add("ERROR\tAl Iniciar localidad $idlocalidad \r\n", $xLog->DEVELOPER);
						}
					}
					//
					if($xLoc->getClaveDeEstado() != $xTmp->codigo_de_estado()->v()){
						$xLog->add("ERROR\tLa clave de estado " . $xTmp->codigo_de_estado()->v() . " del CP $codigo_postal no es la Misma de la $idlocalidad - " . $xLoc->getClaveDeEstado() . " \r\n");
						$xViv->clave_de_localidad( $xTmp->idlocalidad()->v() );
					}
					if($correcion == true){
						$res	= $xViv->query()->update()->save( $xViv->idsocios_vivienda()->v() );
						if($rs == false){
							$xLog->add("ERROR\tAl actualizar la Vivienda con ID $id \r\n");
						} else {
							$xLog->add("OK\tExito al actualizar la Vivienda con ID $id \r\n");
						}
					}
				}
			} else {
				$xLog->add("ERROR\tAl actualizar la Vivienda con CP $codigo_postal \r\n");
			}
			//$xCol			= new cDomiciliosColonias();
			
			/*$idunico		= $xCol->getClavePorCodigoPostal($codigo_postal);
			//corregir codigo postal
			//optener CP por sucursal
			if($idunico <= 0){
				//asignar codigo postal
				//iniciar por sucursal
				$xSuc	= new cSucursal($xViv->sucursal()->v());
				if($xSuc->init() == true){
					$codigo_postal	= $xSuc->getCodigoPostal();
					$idunico		= $xCol->getClavePorCodigoPostal($codigo_postal);
					$xViv->codigo_postal( $codigo_postal );
				}
			}
			//verificar principal
			if(trim( $xViv->principal()->v()) == ""){
				$xViv->principal( TIPO_DOMICILIO_PRINCIPAL );
			}
			//corregir pais
			if(trim( $xViv->nombre_de_pais()->v()) == ""){
				$xViv->nombre_de_pais( strtoupper(EACP_DOMICILIO_PAIS) );
			} else {
				$xViv->nombre_de_pais( strtoupper($xViv->nombre_de_pais()->v()) );
			}
			if($idunico > 0){
				//cargar colonia

				$estado		= $xViv->estado()->v();
				
				//if($xViv->clave_de_pais()->v() != $xCol->get)
				if($estado != $xCol->getNombreEstado()){
					//$msg		.= "ERROR\tEl Estado " . $xViv->estado()->v() .  " es diferente a " . $xT->cChar( $xCol->getNombreEstado()) . "\r\n";
					//$estado		= $xT->cChar( $xCol->getNombreEstado());
				}
				if($correcion == true){
					//$msg		.= "OK\tCorrecion autorizada\r\n";
						$xViv->estado( $xT->cChar($xCol->getNombreEstado()));
						$xViv->municipio( $xT->cChar($xCol->getNombreMunicipio()) );
						$xViv->localidad( $xT->cChar($xCol->getNombreLocalidad()) );
						$xViv->clave_de_localidad( $xCol->getClaveDeLocalidad()  );

						if(PERSONAS_PERMITIR_EXTRANJEROS == false){
							$xViv->clave_de_pais( EACP_CLAVE_DE_PAIS );
						}
						
						$qup	= $xViv->query()->update();
						$rx		= $qup->save($id);
						if($rx == true){
							$msg		.= "OK\tVivienda con CP $codigo_postal e ID $id actualizado a la Colonia $idunico\r\n";
						} else {
							$msg		.= "ERROR\tAl actualizar la Vivienda con CP $codigo_postal e ID $id . Colonia $idunico\r\n";
						}
						
						if(MODO_DEBUG == true){ $msg	.= $qup->getMessages(); }
				}
			} else {
				$msg		.= "WARN\tCodigo omitido por ser $codigo_postal\r\n";
			}*/
		}
		return $xLog->getMessages();
	}
	function setCorregirActividadEconomica($correcion = false){
		$xLog			= new cCoreLog();
		//obtener codigo postal
		$msg			= "";
		//verificar si existe persona
		$xQL			= new MQL();
		$xQL->setRawQuery("CALL `sp_correcciones`()");
		
		$rs				= $xQL->getDataRecord("SELECT * FROM  `socios_aeconomica` ");
		$xAct			= new cSocios_aeconomica();
		$arrSucursalCP	= array();
		
		foreach ($rs as $rows){
			$xAct->setData($rows);
			$persona		= $xAct->socio_aeconomica()->v();
			$id				= $xAct->idsocios_aeconomica()->v();
			$cp				= $xAct->ae_codigo_postal()->v();
			$iddom			= $xAct->domicilio_vinculado()->v();
			$empresa		= $xAct->dependencia_ae()->v();
			$idlocalidad	= $xAct->ae_clave_de_localidad()->v();
			$sucursal		= $xAct->sucursal()->v();
			$procesar		= true;
			//====== Intentar por Domicilio
			if($iddom > 0){
				$xDom		= new cPersonasVivienda($persona);
				$xDom->setID($iddom);
				if($xDom->init() == true){
					//if($persona == 1000440){setLog($xDom->getEstado(OUT_TXT));}
					$xLog->add("OK\t$persona\tSe actualiza Actividad por Domicilio $iddom de la persona " . $xDom->getClaveDePersona() . " \r\n", $xLog->DEVELOPER);
					//============================= CORREGIR DOMICILIO DE LA EMPRESA
					if(trim($xDom->getCalle()) == "" OR trim($xDom->getNumeroExterior()) == "" OR trim($xDom->getCalleConNumero()) == "" ){
						if($empresa !== FALLBACK_CLAVE_EMPRESA){
							$xEmp		= new cEmpresas($empresa);
							$xEmp->init();
							$xDomE		= new cPersonasVivienda($xEmp->getClaveDePersona());
							$xDomE->setID($xEmp->getClaveDeDomicilio());
							if($xDomE->init(false, $xEmp->getDatosDeDomicilio())){
								if(trim($xDomE->getCalle()) != "" AND trim($xDomE->getNumeroExterior()) != ""){
									//
									$sqlDD	= "UPDATE socios_vivienda 
	    										SET
											calle='" . $xDomE->getCalle() . "', numero_exterior='" . $xDomE->getNumeroExterior() . "', numero_interior='" . $xDomE->getNumeroInterior() . "', colonia='" . $xDomE->getColonia() . "',
											localidad='" . $xDomE->getLocalidad() . "', estado='" .  $xDomE->getEstado(OUT_TXT). "', municipio='" . $xDomE->getMunicipio() . "',
											telefono_residencial='" . $xDomE->getTelefonoFijo() . "', telefono_movil='" . $xDomE->getTelefonoMovil() . "', referencia='" . $xDomE->getReferencia() . "',
											codigo_postal=" . $xDomE->getCodigoPostal() . ", clave_de_localidad=" . $xDomE->getClaveDeLocalidad() .", clave_de_pais='" . $xDomE->getClaveDePais() . "', 
											nombre_de_pais='" . $xDomE->getNombreDePais() . "', clave_de_municipio=" . $xDomE->getClaveDeMunicipio() . ", clave_de_entidadfederativa=" .$xDomE->getClaveDeEstado() . " WHERE idsocios_vivienda=$iddom";
									$oadom	= $xQL->setRawQuery($sqlDD);
									$xLog->add("OK\t$persona\tSe actualiza la Vivienda de la Actividad Economica con ID $iddom por una Actual con ID " . $xEmp->getClaveDeDomicilio() . "\r\n", $xLog->DEVELOPER);
									$xDom->init();
								}
							}
						}
					}
					$xAct->ae_codigo_postal( $xDom->getCodigoPostal() );
					$xAct->ae_clave_de_localidad( $xDom->getClaveDeLocalidad() );
					$xAct->domicilio_ae( $xDom->getDireccionBasica() );
					$xAct->localidad_ae( $xDom->getLocalidad() );
					$xAct->municipio_ae( $xDom->getMunicipio() );
					$xAct->estado_ae( $xDom->getEstado(OUT_TXT) );
					
					$procesar	= false;
						
				}
			}
			//====== Intentar por Empresa
			if($procesar == true){
				$xPerAe		= new cPersonaActividadEconomica($persona);
				$xPerAe->setID($id);
				$xPerAe->init($rows);
				if($empresa != FALLBACK_CLAVE_EMPRESA AND $empresa != DEFAULT_EMPRESA){
					if($xPerAe->isInit() == true){
						if($xPerAe->setUpdatePorEmpresa(true) == false){
							if($xPerAe->setUpdatePorDomicilio() > 0){
								$xLog->add("OK\t$persona\tSe actualiza Actividad por Empresa $empresa\r\n", $xLog->DEVELOPER);
								$procesar = false;
							}
						}
					}
					$xLog->add($xPerAe->getMessages(), $xLog->DEVELOPER);
						//$xLog->add("ERROR\tAl procesar La Actividad con id $id\r\n");
				}
			}
			//====== Intentar por Codigo postal
			if($procesar == true){
				if($cp <= 0){
					//iniciar CP por Sucursal
					$xSuc	= new cSucursal($sucursal);
					if(isset($arrSucursalCP[$sucursal])){
						$cp				= $arrSucursalCP[$sucursal];
						$xAct->ae_codigo_postal($cp);
					} else {
						if($xSuc->init() == true){
							$cp							= $xSuc->getCodigoPostal();
							$arrSucursalCP[$sucursal]	= $cp;
							$xAct->ae_codigo_postal($cp);
						} else {
							$cp			= EACP_CODIGO_POSTAL; //Extremo
						}
					}
					$xLog->add("WARN\t$persona\tCodigo Postal ajustado  a $cp\r\n", $xLog->DEVELOPER);
				}
				
				$xCol = new cTmp_colonias_activas();
				$xCol->setData( $xCol->query()->initByID($cp) );
				//== Corrige si no existe el codigo postal
				if($xCol->codigo_postal()->v() <= 0){
					$xSuc	= new cSucursal($sucursal);
					if(isset($arrSucursalCP[$sucursal])){
						$cp				= $arrSucursalCP[$sucursal];
					} else {
						if($xSuc->init() == true){
							$cp			= $xSuc->getCodigoPostal();
						} else {
							$cp			= EACP_CODIGO_POSTAL; //Extremo
						}
						$xLog->add("WARN\t$persona\tCodigo Postal No encontrado a $cp\r\n", $xLog->DEVELOPER);
					}
					$xCol->setData( $xCol->query()->initByID($cp) );
				}
				//domicilio_ae, localidad_ae, municipio_ae, estado_ae,
				//domicilio_vinculado, ae_clave_de_localidad, ae_codigo_postal				
				$xAct->localidad_ae( setCadenaVal($xCol->nombre_localidad()->v()) );
				$xAct->municipio_ae( setCadenaVal($xCol->nombre_municipio()->v() ));
				$xAct->estado_ae( setCadenaVal($xCol->nombre_estado()->v()) );
				$xAct->ae_clave_de_localidad( $xCol->idlocalidad()->v() );
				$xLog->add("WARN\t$persona\tCarga de Actividad a por Codigo Postal\r\n", $xLog->DEVELOPER);
			}
			//verificar si tiene codigo postal
			if($correcion == true){
				$res		= $xAct->query()->update()->save($id);
				if($res == false){
					$xLog->add("ERROR\t$persona\tEn la actualizacion de la Actividad con ID $id\r\n", $xLog->DEVELOPER);
				} else {
					$xLog->add("OK\t$persona\tEn la actualizacion de la Actividad con ID $id\r\n", $xLog->DEVELOPER);
				}
			}
		}
		return $xLog->getMessages();
	}
	function setCrearArbolRelaciones(){
		$QL		= new MQL();
		//$id		= $this->mPersona;
		$sql	= "SELECT `socios_relaciones`.`socio_relacionado`, `socios_relaciones`.`numero_socio` FROM `socios_relaciones` `socios_relaciones`  /*WHERE `socio_relacionado`=1901873*/ ";
		$QL->setRawQuery("DELETE FROM `personas_relaciones_recursivas`");
		
		$rs		= $QL->getDataRecord($sql);
		$data	= array();
		$datos	= array();//array persona => relacion = nivel
		$nodos	= array();
		$puntos	= array();
		
		$Directos	= array();
		$Niveles	= array();
		
		foreach ($rs as $rw){
			$persona			= $rw["socio_relacionado"];
			$relacion			= $rw["numero_socio"];
			$data[$persona][]	= $relacion;
		}
		foreach ($data as $clave => $contenido){

			foreach ($contenido as $idx => $xrelacion1){
				$arrCurrRel	= array();
				$arrCurrRel[$clave] = true;
				$isql		= "";	//existe relacion				
				//Nivel 1
				 $isql	.=" ('$clave', '$xrelacion1', '1', '$clave') ";
				 
				//Nivel 2
				if(isset($data[$xrelacion1]) AND !isset($arrCurrRel[$xrelacion1]) ){
					$DN2	= $data[$xrelacion1];
					$arrCurrRel[$xrelacion1] = true;
					foreach ($DN2 as $idx2 => $xrelacion2){
						$isql	.=", ('$clave', '$xrelacion2', '2', '$xrelacion1') ";
						
						//Nivel 3
						if(isset($data[$xrelacion2]) AND !isset($arrCurrRel[$xrelacion2])){
							$DN3	= $data[$xrelacion2];
							$arrCurrRel[$xrelacion2] = true;
							foreach ($DN3 as $idx3 => $xrelacion3){
								$isql	.=", ('$clave', '$xrelacion3', '3', '$xrelacion2') ";
								
								//Nivel 4
								if(isset($data[$xrelacion3]) AND !isset($arrCurrRel[$xrelacion3])){
									$DN4	= $data[$xrelacion3];
									$arrCurrRel[$xrelacion3] = true;
									foreach ($DN4 as $idx4 => $xrelacion4){
										$isql	.=", ('$clave', '$xrelacion4', '4', '$xrelacion3') ";
										
										//Nivel 5
										if(isset($data[$xrelacion4]) AND !isset($arrCurrRel[$xrelacion4])){
											$DN5	= $data[$xrelacion4];
											$arrCurrRel[$xrelacion4] = true;
											foreach ($DN5 as $idx5 => $xrelacion5){
												$isql	.=", ('$clave', '$xrelacion5', '5', '$xrelacion4') ";
												
												if(isset($data[$xrelacion5]) AND !isset($arrCurrRel[$xrelacion5])){
													$DN6	= $data[$xrelacion5];
													$arrCurrRel[$xrelacion5] = true;
													foreach ($DN6 as $idx6 => $xrelacion6){
														$isql	.=", ('$clave', '$xrelacion6', '6', '$xrelacion5') ";
													}
												}
											}
										}
									}
								}
							}
						}
					}
					
				}/* else { echo "$xrelacion1 No hay N2\r\n";}*/ //si hay nivel 2
				//Terminar Insert
				$QL->setRawQuery("INSERT INTO `personas_relaciones_recursivas` (`persona`, `relacion`, `nivel`, `proxy`) VALUES $isql ");
			}
			
		}
		//foreach ($datos as $personas => $relacion){
			//$QL->setRawQuery("");
		//}	
	}
	function setConstruirEstadisticas($idpersona = false){
		$xQL		= new MQL();
		$idpersona	= setNoMenorQueCero($idpersona);
		
		if($idpersona <= DEFAULT_SOCIO){
			$xQL->setCall("sp_personas_estadisticas");
		}
		$rs			= $xQL->getRecordset("SELECT numero_socio, descripcion FROM creditos WHERE estatusactivo=1 AND saldo_actual>0");
		if($rs){
			
			while($rw = $rs->fetch_assoc()){
				$descripcion	= $rw["descripcion"];
				$idpersona		= $rw["numero_socio"];
				
				$xQL->setRawQuery("UPDATE `tmp_personas_estadisticas` SET `inf_creditos` = SUBSTR( CONCAT(`inf_creditos`,';$descripcion'),1,250) WHERE `persona`= $idpersona ");
				
			}
			$rs->free();
		}
	}
}
class cIDLegal {
	private $mID		= "";
	private $mTamannio	= 0;
	
	function __construct($id = ""){
		$this->mID			= $id;
		$this->mTamannio	= strlen($id);
	}
	function getFechaCreacion(){
		$idlegal	= $this->mID;
		$fecha		= "";
		$xT			= new cTipos();
		//$uso	= substr($idlegal, $start)
		$tm			= strlen($idlegal);
		$patron		= "/[^0-9]/";///"/[#\$%-_!\?,\*]|[[:space:]]/"; [^a-zA-Z0-9\s\p{P}]
		$idlegal	= preg_replace($patron, "", $idlegal);
		
		if(EACP_CLAVE_DE_PAIS == "MX"){
			//$idlegal	= substr($idlegal, 3,6);
			$fecha		= substr($idlegal, 0,2) . "-" . substr($idlegal, 3,2) . "-" . substr($idlegal, 4,2);
		}
		return $fecha;
	}
	function getLugarCreacion(){
		$lugar		= "";
		if(EACP_CLAVE_DE_PAIS == "MX"){
			//4 BAG		1234
			//6 810822  56789 10
			//1 H		11
			//2 CC		12 13
			//3 LNS		14 15 16
			//2 12		17 18
			if($this->mTamannio == 18){
				$CODEST	= substr($this->mID, 12,2);
				$CODEST	= strtoupper($CODEST);
				$sqlEstados		= "SELECT * FROM `general_estados` WHERE (`general_estados`.`clave_alfanumerica` ='$CODEST')";
				$D			= obten_filas($sqlEstados);
				if(isset($D["nombre"])){
					$lugar		= strtoupper($D["nombre"]);
				}
			}
		}
		return $lugar;
	}
	function evaluar(){
		
	}
}
class cPersonasBuscadores {
	function __construct(){
		
	}
	function setBuscarPorIDPoblacional($idpoblacional, $estricto = false, $solobuscar = false){
		$WSoc		= ($estricto == false) ? " (curp LIKE '$idpoblacional%') " : " (curp = '$idpoblacional') ";
		$xQL		= new MQL();
		$sql		= "SELECT * FROM socios_general WHERE $WSoc LIMIT 0,1";
		$datos		= $xQL->getDataRow($sql);
		
		if($solobuscar == true){ $datos	=(isset($datos["codigo"])) ? true : false; }
		return $datos;
	}
	function setBuscarPorIDFiscal($idfiscal, $estricto = false, $solobuscar = false){
		$WSoc		= ($estricto == false) ? " (rfc LIKE '$idfiscal%') " : " (rfc = '$idfiscal') ";
		$xQL		= new MQL();
		$sql		= "SELECT * FROM socios_general WHERE $WSoc LIMIT 0,1";
		
		$datos		= $xQL->getDataRow($sql);
		if($solobuscar == true){ $datos	= (isset($datos["codigo"])) ? true : false; }
		return $datos;
	}
	
}

class cPersonasShare {
	private $mClave			= false;
	private $mObj			= null;
	private $mInit			= false;
	private $mNombre		= "";
	private $mMessages		= "";
	private $mIDCache		= "";
	private $mTabla			= "";
	private $mTipo			= 0;
	private $mUsuario		= 0;
	private $mFecha			= false;
	private $mTiempo		= 0;
	private $mTexto			= "";
	private $mObservacion	= "";
	private $mPersonaActual	= 0;
	private $mPersonaImport	= 0;
	
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
		$xT			= new cPersonas_share();//Tabla
		
		
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
			
			$this->mClave			= $data[$xT->getKey()];
			$this->mPersonaActual	= $data[$xT->PERSONA_ID];
			$this->mPersonaImport	= $data[$xT->PERSONAS_SHARE_ID];
			
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
	function getNombre(){return $this->mNombre; }
	function getClave(){return $this->mClave; }
	function getTipo(){ return $this->mTipo; }
	function setCuandoSeActualiza(){ $this->setCleanCache(); }
	function add($personaImportada){
		
		$xSuc		= new cSucursal(getSucursal());
		$xSuc->init();
		$idpersona	= $xSuc->getNuevaClaveDePersona();
		if($this->initByImportado($personaImportada) == true){
			$this->mMessages	.= "ERROR\tLa Persona Importada $personaImportada ya Existe, no se agrega\r\n";
			return false;
		}
		
		//Guardar en Share
		$xShare	= new cPersonas_share();
		$xShare->idusuario(getUsuarioActual());
		$xShare->persona_id($idpersona);
		$xShare->idpersonas_share("NULL");
		$xShare->personas_share_id($personaImportada);
		
		$xShare->tiempo(time());
		$xShare->url_share(SVC_ASOCIADA_HOST);
		$res	= $xShare->query()->insert()->save();
		if($res === false){
			return false;
		} else {
			$this->mPersonaImport	= $personaImportada;
			$this->mPersonaActual	= $idpersona;
			return true;
		}
	}
	function add2($personaImportada, $persona = false){
		$res		= false;
		$idpersona	= setNoMenorQueCero($persona);
		if($this->initByImportado($personaImportada) == true){
			$this->mMessages	.= "ERROR\tLa Persona Importada $personaImportada ya Existe, no se agrega\r\n";
			return false;
		}
		if($persona > DEFAULT_SOCIO AND $personaImportada > DEFAULT_SOCIO){
			//Guardar en Share
			$xShare	= new cPersonas_share();
			$xShare->idusuario(getUsuarioActual());
			$xShare->persona_id($idpersona);
			$xShare->idpersonas_share("NULL");
			$xShare->personas_share_id($personaImportada);
			
			$xShare->tiempo(time());
			$xShare->url_share(SVC_ASOCIADA_HOST);
			$res	= $xShare->query()->insert()->save();
			
			$this->setCuandoSeActualiza();
		}
		if($res === false){
			return false;
		} else {
			$this->mPersonaImport	= $personaImportada;
			$this->mPersonaActual	= $idpersona;
			return true;
		}
	}
	function getPersonaActual(){ return $this->mPersonaActual; }
	function getPersonaImportada(){ return $this->mPersonaImport; }
	function initByImportado($idimportado){
		$sql	= "SELECT * FROM `personas_share` WHERE `personas_share_id`=$idimportado ORDER BY `tiempo` DESC LIMIT 0,1";
		$xQL	= new MQL();
		$data	= $xQL->getDataRow($sql);
		return $this->init($data);
	}
	function initByPersona($idpersona){
		$sql	= "SELECT * FROM `personas_share` WHERE `persona_id`=$idpersona ORDER BY `tiempo` DESC LIMIT 0,1";
		$xQL	= new MQL();
		$data	= $xQL->getDataRow($sql);
		return $this->init($data);
	}
}
?>