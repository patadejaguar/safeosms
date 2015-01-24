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
	private $mTCredsAut			= 0;
	private $mTCredsNum			= 0;
	private $mTCredsActivos		= 0;
	private $mTCredsActivosAut		= 0;
		
	function __construct($clave_de_persona){
		$this->mPersona	= $clave_de_persona;
	}
	/**
	 * Retorna un Listado en Formato Pedido de los Creditos Actuales
	 */
	function initDatosDeCredito(){
		$sql	= "SELECT `creditos_solicitud`.* FROM `creditos_solicitud` WHERE (`creditos_solicitud`.`numero_socio` =" . $this->mPersona . ") ";
		$xCred	= new cCreditos_solicitud();
		$mql	= new MQL();
		$data		= $mql->getDataRecord($sql);
		foreach ($data as $row){
			$xCred->setData($row);
			if( $xCred->saldo_actual()->v() > TOLERANCIA_SALDOS ){
				$this->mTCredsActivos++;
				$this->mTCredsSaldo += $xCred->saldo_actual()->v();
				//$this->mAListaDeCreds[ $xCred->numero_solicitud()->v() ][SYS_MONTO]	= $xCred->saldo_actual()->v();
				$this->mAListaDeCreds[ $xCred->numero_solicitud()->v() ] = $row;
				$this->mTCredsActivosAut += $xCred->monto_autorizado()->v();
				//TODO: Acompletar
			}
			$this->mTCredsNum++;
		}
	}
	function getTotalCreditosSaldo(){ return $this->mTCredsSaldo;}
	function getTotalCreditosActivos(){ return $this->mTCredsActivos; }
	function getTotalCreditosActivosAutorizado(){ return $this->mTCredsActivosAut; }
}

class cPersonas_utils {
	function __construct(){
		
	}
	function setCorregirDomicilios($correcion = false){
		//obtener codigo postal
		$msg			= "";
		//verificar si existe persona
		$ql				= new MQL();
		$rs				= $ql->getDataRecord("SELECT * FROM `socios_vivienda`");
		$xViv			= new cSocios_vivienda();
		$xT				= new cTipos();
		$xT->setForceMayus();
		$xT->setToUTF8();
		$xT->setForceClean();
				
		foreach ($rs as $rows){
			$xViv->setData($rows);
			//codigo_postal
			$codigo_postal	= $xViv->codigo_postal()->v();
			$id				= $xViv->idsocios_vivienda()->v();
			$xCol			= new cDomiciliosColonias();
			$idunico		= $xCol->getClavePorCodigoPostal($codigo_postal);
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
			}
		}
		return $msg;
	}
	function setCorregirActividadEconomica($correcion = false){
		//obtener codigo postal
		$msg			= "";
		//verificar si existe persona
		$ql				= new MQL();
		$rs				= $ql->getDataRecord("SELECT * FROM  `socios_aeconomica` ");
		$xAct			= new cSocios_aeconomica();
		//$xT				= new cTipos();
		//$xT->setForceMayus();
		//$xT->setToUTF8();
		//$xT->setForceClean();
		
		foreach ($rs as $rows){
			$xAct->setData($rows);
			$persona	= $xAct->socio_aeconomica()->v();
			$id			= $xAct->idsocios_aeconomica()->v();
			$cp			= $xAct->ae_codigo_postal()->v();
			$iddom		= $xAct->domicilio_vinculado()->v();
			//===========
			
			//==
			$xPerAe		= new cPersonaActividadEconomica($persona);
			$xPerAe->setID($id);
			$xPerAe->init();
			if($xPerAe->isInit() == true){
				if($xPerAe->setUpdatePorEmpresa(true) == false){
					$xPerAe->setUpdatePorDomicilio();
				}
				$msg	.= $xPerAe->getMessages();
			} else {
				$msg	.= "ERROR\tAl procesar el Domicilio\r\n";
			}
			
			//verificar si tiene codigo postal
		}
		$rs				= $ql->getDataRecord("SELECT * FROM  `socios_aeconomica` WHERE `domicilio_vinculado` <= 1 OR `ae_codigo_postal` <= 1 ");
		$xAct			= new cSocios_aeconomica();
		foreach ($rs as $rows){
			$xAct->setData($rows);
			$persona	= $xAct->socio_aeconomica()->v();
			$id			= $xAct->idsocios_aeconomica()->v();
			$cp			= $xAct->ae_codigo_postal()->v();
			$iddom		= $xAct->domicilio_vinculado()->v();
			$idsuc		= $xAct->sucursal()->v();
			$xSuc		= new cSucursal($idsuc);
			if($xSuc->init() == true){
				$xAct->ae_codigo_postal( $xSuc->getCodigoPostal() );
				$xAct->ae_clave_de_localidad($xSuc->getClaveDeLocalidad() );
			}
			$success	= $xAct->query()->update()->save($id);
			if($success != false){
				$xPerAe		= new cPersonaActividadEconomica($persona);
				$xPerAe->setID($id);
				$xPerAe->init();
				if($xPerAe->isInit() == true){
					if($xPerAe->setUpdatePorEmpresa(true) == false){
						$xPerAe->setUpdatePorDomicilio();
					}
					$msg	.= $xPerAe->getMessages();
				} else {
					$msg	.= "ERROR\tAl procesar el Domicilio con ID $id 2\r\n";
				}				
			} else {
				$msg	.= "ERROR\tAl procesar al actualizar $id\r\n";
			}
		}		
		return $msg;
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

?>