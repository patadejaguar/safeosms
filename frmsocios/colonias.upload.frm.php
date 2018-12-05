<?php
/**
 * @see Modulo de Carga de Respaldos a la Matriz
 * @author Balam Gonzalez Luis Humberto
 * @version 1.2.03
 * @package common
 * Actualizacion
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
	$iduser = $_SESSION["log_id"];
//=====================================================================================================
$xHP			= new cHPage("TR.Carga Automatizada de colonias");
$xFi			= new cFileImporter();
$xQL			= new MQL();
$xLis			= new cSQLListas();
$xF				= new cFecha();
$xLog			= new cCoreLog();

$xT				= new cTipos();
ini_set("max_execution_time", 600);

$xHP->init();

$xFRM			= new cHForm("frmcolonias", "colonias.upload.frm.php");

$doc1			= (isset($_FILES["idarchivo"])) ? $_FILES["idarchivo"] : false;
$sieliminar		= parametro("ideliminar", false, MQL_BOOL);

$corregir		= parametro("idcorregir", false, MQL_BOOL);

class cTmp {
	public $CP		= 1;
	public $NOM		= 2;
	public $TIPO	= 3;
	public $EDO		= 5;
	public $MUN		= 4;
	public $ID_EDO	= 8;
	public $CIUDAD	= 6;
	public $ID_MUN	= 12; 
	/*$ncolonia		= $cT->cChar($datos[1]);
	$tcolonia		= $cT->cChar($datos[2]);
	$estado			= $cT->cChar($datos[4]);
	$municipio		= $cT->cChar($datos[3]);
	
	$numEstado		= $cT->cInt($datos[7]);
		
	$ciudad			= $cT->cChar($datos[5]);*/	
}
$tmp			= new cTmp();
$mFecha			= $xF->get();
$mSucursal		= getSucursal();

if($doc1 == false){
	$xFRM->setTitle($xHP->getTitle());
	$xFRM->OFileText("idarchivo", "TR.Archivo a Importar");
	$xFRM->OCheck("TR.Eliminar Datos", "ideliminar");
	$xFRM->OCheck("TR.Corregir Datos", "idcorregir");
	
	$xFRM->addGuardar();
} else {
	if($sieliminar == true){
		$xQL->setRawQuery("DELETE FROM general_colonias");
	}
	$xFi->setType("txt");
	$xFi->setCharDelimiter("|");
	$xFi->setLimitCampos(15);
	
	$xFi->setToUTF8();
	$xFi->setProbarMB();
	//$xFi->setModoRAW();
	
	if($xFi->processFile($doc1) == true){
		$data		= $xFi->getData();
		$conteo		= 1;
		foreach ($data as $rows){
			$xFi->setDataRow($rows);
			 $cp		= $xFi->getEntero($tmp->CP);
			 $nombre	= $xFi->getV($tmp->NOM);
			 
			 /*public $CP		= 1;
			 public $NOM		= 2;
			 public $TIPO	= 3;
			 public $EDO		= 5;
			 public $MUN		= 4;
			 public $ID_EDO	= 8;
			 public $CIUDAD	= 6;
			 public $ID_MUN	= 12;*/ 
			 
			 
			 if($cp > 0 AND $nombre != ""){
			 	$xCol	= new cGeneral_colonias();
			 	$xCol->ciudad_colonia( $xFi->getV($tmp->CIUDAD, "") );
			 	$xCol->codigo_de_estado($xFi->getEntero($tmp->ID_EDO) );
			 	$xCol->codigo_de_municipio($xFi->getEntero($tmp->ID_MUN) );
			 	$xCol->codigo_postal($cp);
			 	$xCol->estado_colonia($xFi->getV($tmp->EDO, ""));
			 	$xCol->fecha_de_revision($mFecha);
			 	$xCol->municipio_colonia($xFi->getV($tmp->MUN, ""));
			 	$xCol->nombre_colonia($nombre);
			 	$xCol->sucursal($sucursal);
			 	$xCol->tipo_colonia( $xT->setNoAcentos($rows[2]) );
			 	$xCol->idgeneral_colonia( $xCol->query()->getLastID() );
			 	$res = $xCol->query()->insert()->save();
			 	if($res == false){
			 		$xLog->add("ERROR\tAl agregar el CP $cp  -$nombre-\r\n");
			 	} else {
			 		$xLog->add("OK\tSe agrega el CP $cp con Nombre $nombre\r\n");
			 	}
			 } else {
				$xLog->add("ERROR\tAl agregar el CP $cp -- $nombre --\r\n");
			}
		}
	} else { 
		$xLog->add($xFi->getMessages(), $xLog->DEVELOPER);
	}
	if($corregir == true){
		$xQL->setRawQuery("CALL `proc_colonias_activas`");
		$xQL->setRawQuery("CALL `sp_correcciones`()");
	}
	$xFRM->addLog($xLog->getMessages());
}

echo $xFRM->get();

$xHP->fin();

?>
