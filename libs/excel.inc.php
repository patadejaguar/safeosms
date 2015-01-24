<?php

class excel
{

var $cabecera = '<html xmlns:o="urn:schemas-microsoft-com:office:office"
				xmlns:x="urn:schemas-microsoft-com:office:excel"
				xmlns="http://www.w3.org/TR/REC-html40">

				<head>
				<meta http-equiv=Content-Type content="text/html; charset=us-ascii">
				<meta name=ProgId content=Excel.Sheet>
				<!--[if gte mso 9]><xml>
				 <o:DocumentProperties>';
				 
var $xml = '<xml>
				 <x:ExcelWorkbook>
				  <x:ExcelWorksheets>
				   <x:ExcelWorksheet>
					<x:Name>Sin Definir</x:Name>
					<x:WorksheetOptions>
					 <x:Selected/>
					 <x:ProtectContents>False</x:ProtectContents>
					 <x:ProtectObjects>False</x:ProtectObjects>
					 <x:ProtectScenarios>False</x:ProtectScenarios>
					</x:WorksheetOptions>
				   </x:ExcelWorksheet>
				  </x:ExcelWorksheets>
				  <x:WindowHeight>10005</x:WindowHeight>
				  <x:WindowWidth>10005</x:WindowWidth>
				  <x:WindowTopX>120</x:WindowTopX>
				  <x:WindowTopY>135</x:WindowTopY>
				  <x:ProtectStructure>False</x:ProtectStructure>
				  <x:ProtectWindows>False</x:ProtectWindows>
				 </x:ExcelWorkbook>
				</xml>';
var $titulo = '<o:LastAuthor>Sin Titulo</o:LastAuthor>
				  <o:LastSaved></o:LastSaved>
				  <o:Version>10.2625</o:Version>
				 </o:DocumentProperties>
				 <o:OfficeDocumentSettings>
				  <o:DownloadComponents/>
				 </o:OfficeDocumentSettings>
				</xml>' ;
var $excel ;
var $cerrar;
var $cuerpo = '<table>';
var $header = 'Content-type: application/vnd.ms-excel';

function titulo ($titulo = 'Indefinido',$fecha=''){

$this->titulo = '<o:LastAuthor>'.$titulo.'</o:LastAuthor>
				  <o:LastSaved>'.$fecha.'</o:LastSaved>
				  <o:Version>10.2625</o:Version>
				 </o:DocumentProperties>
				 <o:OfficeDocumentSettings>
				  <o:DownloadComponents/>
				 </o:OfficeDocumentSettings>
				</xml><![endif]-->';
				
				return $this->titulo;
}

function cuerpo_xml ($nombre = 'Sin definir'){

$this->xml .= '<!--[if gte mso 9]><xml>
				 <x:ExcelWorkbook>
				  <x:ExcelWorksheets>
				   <x:ExcelWorksheet>
					<x:Name>'.$nombre.'</x:Name>
					<x:WorksheetOptions>
					 <x:Selected/>
					 <x:ProtectContents>False</x:ProtectContents>
					 <x:ProtectObjects>False</x:ProtectObjects>
					 <x:ProtectScenarios>False</x:ProtectScenarios>
					</x:WorksheetOptions>
				   </x:ExcelWorksheet>
				  </x:ExcelWorksheets>
				  <x:WindowHeight>10005</x:WindowHeight>
				  <x:WindowWidth>10005</x:WindowWidth>
				  <x:WindowTopX>120</x:WindowTopX>
				  <x:WindowTopY>135</x:WindowTopY>
				  <x:ProtectStructure>False</x:ProtectStructure>
				  <x:ProtectWindows>False</x:ProtectWindows>
				 </x:ExcelWorkbook>
				</xml><![endif]-->
        </head>
				<body>';

return $this->xml;
}

function cerrar(){

$this->cerrar .= '</body></html>';

return $this->cerrar; 
}

function td($texto,$tipo=0){

if ($tipo == 1){ 
$h1="<h1>";
$h12 = "</h1>";
}

$this->cuerpo .= '<tr>';
foreach ($texto as $campo => $valor){
$this->cuerpo .= '<td>'.$h1.$valor.$h12.'</td>';
}
$this->cuerpo .= '</tr>';
return $this->cuerpo;
}
/**
* Agregar un TR total
*/
function tr($tr){
	$this->cuerpo .= $tr;
	return $this->cuerpo;
}

function crear_excel(){

$this->excel = $this->cabecera.$this->xml.$this->titulo;

return $this->excel;
}

function exportar($nombre,$opcion=1){

$exportar .= $this->cabecera;
$exportar .= $this->xml;
//$exportar .= $this->titulo;
$exportar .= $this->cuerpo;
$exportar .= $this->cerrar;
if ($opcion == 1 ){
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=".$nombre.".xls");

} 

echo $exportar;
}

} 

?>
