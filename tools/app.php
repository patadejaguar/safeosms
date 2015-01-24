<?php
//=====>	INICIO_H
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");

//<=====	FIN_H

//=====================================================================================================
include_once("../core/entidad.datos.php");
include_once("../core/core.deprecated.inc.php");
include_once("../core/core.fechas.inc.php");
include_once("../libs/sql.inc.php");
include_once("../core/core.config.inc.php");
require_once("." . TINYAJAX_PATH . "/TinyAjax.php");

class cXMLReport {
	private $mADocHead		= array();
	private $mADocFoot		= array();
	private $mAGrpHead		= array();
	private $mAGrpFoot		= array();
	private $mAGrpField		= array();
	private $mAGrpTit		= array();
	
	private $mACSS			= array();
	
	private $mAFieldT		= array();		//Titulo o Alias del campo
	private $mAFieldN		= array();		//Nombre del campo
	private $mAFieldD		= array();		//Nombre de la tabla
	//

	private $mSQL			= "";
	private $mBodySQL		= "";
	private $mFromSQL		= "";
	
	private $mXML			= "";
	private $mTitle			= "";
	
	private $mPageHead		= "";
	private $mPageFoot		= "";
	
	private $mNumCols		= 0;
	
	function __construct( $titulo = "", $sql = ""){
		$this->mSQL			= $sql;
		$this->mTitle		= $titulo;
	}
	function addDocHead(){
		
	}
	function addDocFoot(){
		
	}
	function getCampos($sql= ""){
		$this->mSQL		= ( $sql != "" ) ? $sql : $this->mSQL;
		
		$fin			= strpos( strtoupper( $this->mSQL), "FROM");
		
		$str			= substr($this->mSQL, 0, $fin);
		$str			= str_replace("SELECT", "", $str);
		$str			= trim($str);
		$str			= str_replace("\r", "", $str);
		$str			= str_replace("\n", "", $str);
		$arrFld			= explode(",", $str);
		$posicion		= 1;
		foreach( $arrFld as $clave => $value ){
			$valor		= trim($value);
			//eliminar comillas `tabla`.`campo` AS 'alias'
			$valor		= str_replace("`", "", $valor);
			$valor		= str_replace("'", "", $valor);
			$valor		= str_replace("\"", "", $valor);
			
			$tabla		= "";
			$campo		= "";
			$titulo		= "";
			
			//seleccionar la tabla, eliminar tabla.
			if( strpos($valor, ".") > 0 ){
				$DTab		= explode(".", $valor);
				$tabla		= $DTab[0];
				
				//eliminar la tabla : .campo AS alias
				$valor		= str_replace("$tabla.", "", $valor);
				unset($DTab);
				$this->mAFieldD[$posicion]	= $tabla;
			}
			//seleccionar el alias
			if( (strpos($valor, " AS ") > 0) OR (strpos($valor, " as ") > 0) ){
				$valor		= str_replace(" AS ", "|", $valor);
				$valor		= str_replace(" as ", "|", $valor);
				
				$VFld		= explode("|", $valor);
				$campo		= $VFld[0];
				$titulo		= $VFld[1];
			} else {
				$campo		= $valor;
				$titulo		= $valor;
			}
			
			$this->mAFieldN[$posicion] = $campo;
			$this->mAFieldT[$posicion] = $titulo;
			$this->mNumCols	= $posicion;
			$posicion++;
			
		}
		return $str;
		
	}
	function getCamposInArray(){
		return $this->mAFieldT;
	}
	function compileFromSQL($sql){
		$arrWithTilde = array(
						"varchar" => "varchar",
						"date" => "date",
						"text" => "text",
						"tinytext" => "tinytext",
						"enum" => "enum",
						"blob" => "blob",
						"string" => "string",
						"longtext" => "longtext",
						"datetime" => "datetime",
						"time" => "time"
						);

		$arrTypesPHP 	= array(
						"enum"		=> "string",
						"varchar"	=>"string",
						"tinytext"	=>"string",
						"float"		=>"float",
						"decimal"	=>"float",
						"int"		=>"integer",
						"integer"	=>"integer",
						"text"		=>"string",
						"longtext"	=>"string",
						"blob"		=>"string",
						"tinyint"	=>"integer",
						"datetime"	=>"string",
						"date"		=>"string",
						"time"		=>"string"
						);
		$arrTypesNum 	= array(
						"float"		=>"float",
						"decimal"	=>"float",
						"int"		=>"integer",
						"integer"	=>"integer",
						"tinyint"	=>"integer",
						);

								
		//$IniMvtos		= strpos($texto_contrato, "---");
		$this->addCSS("../css/xml.css", "print");
		$this->addCSS("../css/xml.css", "screen");
		$this->getCampos($sql);
		//obtener info del field
		foreach ($this->mAFieldN as $clave => $valor){
			//propiedades extras
			
			$mNumFormat			= "";
			$mAlin				= "ALIGN='CENTER'";
			
			$xTInfo				= new cTableStructure($this->mAFieldD[$clave]);
			$IField				= $xTInfo->getInfoField($valor);
			
			$FTipo				= strtolower( trim($IField["tipo"]) );
			$FNombre			= $IField["campo"];
			if ( $arrTypesPHP[$FTipo] == "float" ){
				$mNumFormat		= "NUMBERFORMATEX='2'";
			}
			if( isset($arrTypesNum[$FTipo]) ){
				$mAlin			= "ALIGN='LEFT'";
			}
			/**/
			$this->mAGrpField[]	= "<COL  TYPE='FIELD' $mAlin  CELLCLASS='FIELDS' $mNumFormat >" . $this->mAFieldT[$clave]  . "</COL>\r\n";
			$this->mAGrpTit[]	= "<COL ALIGN='CENTER'  CELLCLASS='GROUP_HEADER' >" . strtoupper($this->mAFieldT[$clave])  . "</COL>\r\n";
		}
	}
	function addPageFoot($HtmlContent	= ""){
		//_NUMERO_COLUMNAS_
		$this->mPageFoot	= "							<ROW>
								<!-- PIE DE PAGINA -->
								<COL ALIGN='CENTER'  CELLCLASS='FOOTER'  COLSPAN='_NUMERO_COLUMNAS_' >
									<XHTML>
									$HtmlContent
									</XHTML>
								</COL>
							</ROW>";
	}
	function addPageHead($HtmlContent	= ""){
		//_NUMERO_COLUMNAS_
		$this->mPageHead	= "<ROW>
								<!-- CONTENIDO DEL ENCABEZADO DE PAGINA -->
								<COL ALIGN='CENTER'  CELLCLASS='FOOTER'  COLSPAN='_NUMERO_COLUMNAS_' >
									<XHTML>
									$HtmlContent
									</XHTML>
								</COL>
							</ROW> ";
	}
	/**
	 * Agrega un Tipo de Archivo CSS
	 * @param string $css
	 * @param string $media
	 */
	function addCSS($css, $media = "print"){
		$this->mACSS[ $media ] = $css;
	}
	//function 
	function get(){
		//compilar CSS
		$css			= "";
		$fields			= "";
		$titleFields	= "";
		
		foreach($this->mACSS as $key => $value ){
			$css	.= "<CSS MEDIA='$key'>$value</CSS>\r\n";
		}
		foreach ($this->mAGrpField as $key => $value ){
			$fields	.= $value;
		}
		foreach ($this->mAGrpTit as $key => $value ){
			$titleFields	.= $value;
		}
		$xml	= "<?xml version='1.0' encoding='ISO-8859-1' standalone='no'?>
	<!DOCTYPE REPORT SYSTEM 'PHPReport.dtd'>
	<REPORT MARGINWIDTH='2' MARGINHEIGHT='2' >
	<TITLE>" . $this->mTitle . "</TITLE>
	<BACKGROUND_COLOR>#FFFFFF</BACKGROUND_COLOR>
	<SQL>" . $this->mSQL. "</SQL>
	<INTERFACE>mysql</INTERFACE>
	<CONNECTION>localhost</CONNECTION>
	<DATABASE></DATABASE>
	<NO_DATA_MSG>NO EXISTEN DATOS</NO_DATA_MSG>
					" . $css .  "
					<!-- PAGINA -->
					<PAGE BORDER='0' SIZE='0' CELLSPACING='2' CELLPADDING='0'>
						<!-- ENCABEZADO DE PAGINA -->
						<HEADER>
						" . $this->mPageHead . "
							<!-- TITULO DE REPORTE -->
							<ROW>
								<COL ALIGN='CENTER'  CELLCLASS='FOOTER'  COLSPAN='_NUMERO_COLUMNAS_' >" . $this->mTitle . "</COL>
							</ROW>
							
							<ROW>
								<COL ALIGN='CENTER'  CELLCLASS='FOOTER'  WIDTH='100%'  COLSPAN='_NUMERO_COLUMNAS_' >
									<XHTML>
										<hr />
									</XHTML>
								</COL>
							</ROW>
						</HEADER>
						<!-- PIE DE PAGINA -->
						<FOOTER>
							" . $this->mPageFoot . "
						</FOOTER>
					</PAGE>
					<!-- GRUPOS DE DATOS -->
					<GROUPS>
						<GROUP NAME='MAIN' EXPRESSION='id'>
							<!-- ENCABEZADO DE GRUPO -->
							<HEADER>
								<!-- TITULO DE LOS CAMPOS -->
								<ROW>
								$titleFields
								</ROW>
							</HEADER>
							<!-- AREA DE VALORES DE CAMPO -->
							<FIELDS>
								<ROW>
								$fields
								</ROW>
							</FIELDS>
							<!-- PIE DE GRUPO -->
							<FOOTER>
							
							<ROW>
								<COL  TYPE='EXPRESSION' ALIGN='CENTER'  CELLCLASS='GROUP_FOOTER'  COLSPAN='_NUMERO_COLUMNAS_' ></COL>
							</ROW>
								<ROW>
									<COL ALIGN='CENTER'  CELLCLASS='GROUP_FOOTER'  WIDTH='100%'  COLSPAN='_NUMERO_COLUMNAS_' >
										<XHTML>
											<hr />
										</XHTML>
									</COL>
								</ROW>
							<ROW>
							</ROW>
							</FOOTER>
						</GROUP>
					
					</GROUPS>
					<!-- DOCUMENTO -->
					<DOCUMENT>
						
						<FOOTER>
							<ROW>
							</ROW>
						</FOOTER>
					
					</DOCUMENT>
					
					</REPORT>";
		$xml		= str_replace("_NUMERO_COLUMNAS_", $this->mNumCols, $xml);
		return $xml;
	}
}
$jxc = new TinyAjax();
//$HReporte, $FReporte
function jsaCompilar($titulo, $sql, $HPagina, $FPagina){
	//$HPagina	= addslashes($HPagina);
	//$FPagina	= addslashes($FPagina);
	$xRep		= new cXMLReport($titulo, $sql);
	$xRep->getCampos($sql);
	$xRep->addPageFoot($FPagina);
	$xRep->addPageHead($HPagina);
	
	$xRep->compileFromSQL($sql);
	return highlight_string($xRep->get(), true);
}
function jsaSetBodyReport($sql){
	$_SESSION["ReportSQL"]	= trim($sql);
	//$_SESSION["yaml_file") = "";
	$xRep		= new cXMLReport("", $sql);
	$xRep->getCampos($sql);
	$arrDatos	= $xRep->getCamposInArray();
	
	$strCells	= ""; //$arrDatos;
	$block		= "b";
	
	foreach($arrDatos as $clave => $valor){
		// 
		$block	= ($block == "a") ? "b" : "a";
		$strCells	.= "<div class=\"ui-block-$block\">$clave|$valor</div>";
	}
	return $strCells;
}
//function jsaUpdate
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
$jxc ->exportFunction('jsaSetBodyReport', array('idSQL'), "#idBodyReport");
//, 'idCabezaReporte', 'idPieReporte'
$jxc ->exportFunction('jsaCompilar', array('idTitulo', 'idSQL', 'idCabezaPagina', 'idPiePagina'), "#idCodeGenerated");
$jxc ->process();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>
        </title>
        <link rel="stylesheet" href="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.css" />
        <!--  <link rel="stylesheet" href="my.css" /> -->
        <style>
            /* App custom styles */
        </style>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js">
        </script>
        <script src="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.js">
        </script>
    </head>
    <body>
        <div data-role="page" data-theme="b" id="page1">
            <div data-theme="a" data-role="header">
                <h3>
                    Generador de Reportes
                </h3>
            </div>
            <div data-role="content">
                <div data-role="navbar" data-iconpos="bottom">
                    <ul>

                         <li>
                            <a href="#page2" data-theme="" data-icon="info">
                               Propiedades
                            </a>
                        </li> 
                        <li>
                            <a href="#page4" data-theme="" data-icon="grid">
                                Reporte
                            </a>
                        </li>
                        <li>
                            <a href="#page3" data-theme="" data-icon="gear">
                                Compilar
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div data-theme="a" data-role="footer">
                <h3>
                </h3>
            </div>
        </div>
<!--  pagina 2 -->        
        <div data-role="page" data-theme="b" id="page2">
            <div data-theme="a" data-role="header">
                <a data-role="button" data-transition="fade" data-theme="b" href="#page1" data-icon="home" data-iconpos="left">
                    Inicio
                </a>          
                <h3>
                    Propiedades
                </h3>
            </div>
            <div data-role="content">
                <form action="">
                <div data-role="collapsible-set" data-theme="" data-content-theme="">
                
                    <div data-role="collapsible" data-collapsed="true">
                        <h3>
                            Datos de Acceso
                        </h3>
                    <div data-role="fieldcontain">
                        <fieldset data-role="controlgroup">
                            <label for="textinput2">
                                Servidor
                            </label>
                            <input id="textinput2" placeholder="Anote e Nombre del Servidor MySQL" value="localhost" type="text" />
                        </fieldset>
                    </div>
                    <div data-role="fieldcontain">
                        <fieldset data-role="controlgroup">
                            <label for="textinput3">
                                Usuario
                            </label>
                            <input id="textinput3" placeholder="Nombre del usuario con el que accede a MySQL" value="root" type="text" />
                        </fieldset>
                    </div>
                <div data-role="fieldcontain">
                    <fieldset data-role="controlgroup">
                        <label for="textinput4">
                            Contraseña
                        </label>
                        <input id="textinput4" placeholder="" value="" type="text" />
                    </fieldset>
                </div>
                
                <div data-role="fieldcontain">
                    <fieldset data-role="controlgroup">
                        <label for="textinput5">
                            Base de Datos
                        </label>
                        <input id="textinput5" placeholder="" value="" type="text" />
                    </fieldset>
                </div>
                    </div>
                    
                    <div data-role="collapsible" data-collapsed="false">
                        <h3>
                            Datos de Reporte
                        </h3>
  
                                        

                <div data-role="fieldcontain">
                    <fieldset data-role="controlgroup">
                        <label for="idTitulo">
                            Titulo del Reporte
                        </label>
                        <input id="idTitulo" placeholder="Titulo del Reporte" value="" type="text" />
                    </fieldset>
                </div>                
                <div data-role="fieldcontain">
                    <fieldset data-role="controlgroup">
                        <label for="idSQL">
                            SQL
                        </label>
                        <textarea id="idSQL" title="Cadena SQL donde se obtendra el reporte" onblur="jsSetBodyReport()">
                        <?php
                        echo ( isset($_SESSION["ReportSQL"]) ? trim($_SESSION["ReportSQL"]) : "" ); 
                        ?>
                        </textarea>
                    </fieldset>
                </div>
                </div>
                </div>
                                
                </form>
		</div>
            <div data-theme="a" data-role="footer">
                <h3>
                </h3>
            </div>
        </div>
<!--  pagina 3 -->        
        <div data-role="page" id="page3">
            <div data-theme="a" data-role="header">
                <a data-role="button" data-transition="fade" data-theme="b" href="#page1" data-icon="home" data-iconpos="left">
                    Inicio
                </a>          
                <h3>
                    Compilación
                </h3>
                <a data-role="button" data-transition="fade" data-theme="b" onclick="jsCompilar()" data-icon="gear" data-iconpos="left">
                    Compilar
                </a>                  
            </div>
            <div data-role="content">
                <div>
                    <code id='idCodeGenerated'>
                        
                    </code>
                </div>
            </div>
            <div data-theme="a" data-role="footer">
                <h3>
                </h3>
            </div>
        </div>
<!--  pagina 4 -->        
        <div data-role="page" data-theme="b" id="page4">
            <div data-theme="a" data-role="header">
                <a data-role="button" data-transition="fade" data-theme="b" href="#page1" data-icon="home" data-iconpos="left">
                    Inicio
                </a>            
                <h3>
                    Reporte
                </h3>
            </div>
            <div data-role="content">
                <div data-role="collapsible-set" data-theme="" data-content-theme="">
                
                    <div data-role="collapsible" data-collapsed="true">
                        <h3>
                            Encabezado de Pagina
                        </h3>
                        <div data-role="fieldcontain">
                            <fieldset data-role="controlgroup">
                                <label for="idCabezaPagina">
                                </label>
                                <textarea id="idCabezaPagina" placeholder=""><?php echo getRawHeader();?>
                                </textarea>
                            </fieldset>
                        </div>
                    </div>
                    
                    <div data-role="collapsible" data-collapsed="true">
                        <h3>
                            Encabezado de Reporte
                        </h3>
                        <div data-role="fieldcontain">
                            <fieldset data-role="controlgroup">
                                <textarea id="idCabezaReporte" placeholder="">
                                
                                </textarea>
                            </fieldset>
                        </div>
                    </div>
                    <div data-role="collapsible" data-collapsed="false">
                        <h3>
                            Cuerpo del Reporte
                        </h3>
                        <div class="ui-grid-a" id="idBodyReport">
                            <div class="ui-block-a">
                            A
                            </div>
                            <div class="ui-block-b">
                            B
                            </div>
                            <div class="ui-block-a">
                            A1
                            </div>
                            <div class="ui-block-b">
                            B1
                            </div>
                        </div>
                    </div>
                    <div data-role="collapsible" data-collapsed="true">
                        <h3>
                            Pie de Reporte
                        </h3>
                        <div data-role="fieldcontain">
                            <fieldset data-role="controlgroup">
                                <textarea id="idPieReporte" placeholder="Descripcion que contendra en Reporte en  su Base">
                                
                                </textarea>
                            </fieldset>
                        </div>
                    </div>
                    <div data-role="collapsible" data-collapsed="true">
                        <h3>
                            Pie de Pagina
                        </h3>
                        <div data-role="fieldcontain">
                            <fieldset data-role="controlgroup">
                                <label for="idPiePagina">
                                </label>
                                <textarea id="idPiePagina" placeholder=""><?php echo getRawFooter(); ?>
                                </textarea>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
        	function jsCompilar(){
            	//alert("compilando ....");
            	jsaCompilar();
        	}
        	function jsSetBodyReport(){
            	jsaSetBodyReport();
        	}

        </script>
        <?php
        $jxc ->drawJavaScript(false, true); 
        ?>
    </body>
</html>