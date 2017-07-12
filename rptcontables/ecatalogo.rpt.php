<?php
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
$xHP	= new cHPage("TR.Reporte de Catalogo de Cuentas Contables", HP_REPORT);
$xQL	= new MQL();
$xLi	= new cSQLListas();
$xFil	= new cSQLFiltros();
$xT		= new cTipos();
//=====================================================================================================
$fecha_inicial 			= parametro("on", FECHA_INICIO_OPERACIONES_SISTEMA);
$fecha_final 			= parametro("off", fechasys());
$afectables				= parametro("afectables", false, MQL_BOOL);
$tipo_cuentas 			= parametro("f1", SYS_TODAS);			//Tipo de Cuentas: Activo Acreedoras
$out 					= parametro("out", SYS_DEFAULT);
$Cuenta 				= parametro("for", SYS_TODAS);		//solo unica cuenta
$mostrarTodo			= parametro("mostrar", false, MQL_BOOL); //( isset($_GET["v"]) ) ? $_GET["v"] : 0;
$nivel_cuentas			= parametro("nivel", SYS_TODAS, MQL_RAW);
$ejercicio				= parametro("ejercicio", 0, MQL_INT);
$periodo				= parametro("periodo", 0, MQL_INT);
/**
 * Paginacion
 * I.- Parte
 */
$rowLimit					= ($mostrarTodo == true ) ? 99999 : 90; //Paginas aprox
$InitRecords				= parametro("init", 0, MQL_INT);

//marca el Final de los Registros
$EndRecords					= $InitRecords + $rowLimit;
//captura el URI para manipularlo
$mURI						= $_SERVER['REQUEST_URI'];
//encapsula el patrom
$patron						=  "/init=\d*/";
if ( $InitRecords > 0){
	$mURI					= preg_replace($patron, "init=$EndRecords", $mURI);
} else {
	$mURI					.= "&init=$EndRecords";
}
//==================================================================================
$ByNiv						= $xFil->CatalogoContPorNiveles($nivel_cuentas, false, "<=");
$setSql		= "SELECT
	`contable_catalogo`.*,
	`contable_catalogotipos`.`naturaleza` 
FROM
	`contable_catalogo` `contable_catalogo` 
		INNER JOIN `contable_catalogotipos` `contable_catalogotipos` 
		ON `contable_catalogo`.`tipo` = `contable_catalogotipos`.
		`idcontable_catalogotipos` WHERE (`contable_catalogo`.`digitoagrupador` >0 )
	$ByNiv";
$xRPT		= new cReportes();
$xRPT->setOut(OUT_TXT);
$xRPT->addContent("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n");

$rs			= $xQL->getDataRecord($setSql);
$str		= "";
$cnt		= 0;
foreach ($rs as $rw){
	$xCta		= new cCuentaContable($rw["numero"]);
	$xCta->init($rw);
	$nombre		= htmlentities($xCta->getNombre());
	$idcuenta	= $xCta->getCuentaCompleta(false, true);
	$nivel		= $xCta->getDigitoAgrupador();
	$natural	= ($rw["naturaleza"] == TM_CARGO) ? "D" : "A";
	$str	.= "<Ctas CodAgrup=\"" . $xCta->getEquivalencia() . "\" NumCta=\"" . $idcuenta .  "\" Desc=\"$nombre\" Nivel=\"$nivel\" Natur=\"$natural\" />\r\n";	
	$cnt++;
}
$xRPT->addContent("<Catalogo Version=\"1.1\" RFC=\"" . EACP_RFC . "\" Mes=\"" .  $xT->cSerial(2, $periodo). "\" Anio=\"". $ejercicio  ."\" TotalCtas=\"$cnt\" >\r\n");
$xRPT->addContent($str);
$xRPT->addContent("</Catalogo>");
$rs		= null;
$str	= null;

Header('Content-type: text/xml');
echo $xRPT->render();

?>