<?php 
/**
 * Generador de Socios Ficticios
 **/
header("Content-type: text/x-csv");
//header("Content-type: text/csv");
//header("Content-type: application/csv");
header("Content-Disposition: attachment; filename=socios.unit." . date("Ymd") . ".csv");

$arrNombres		= array(
					"HUGO", "JONAS", "VICENTE", "ANGEL", "LUIS",
					"TOMAS", "CARLOS", "FERNANDO", "RAFAEL", "ALEJANDRO",
					"MARIA", "JANET", "VILMA", "CARMEN", "CONCEPCION",
					"GUADALUPE", "JHOANA", "SANDRA", "KARINA", "MARISOL"
					);
$arrApellidos	= array (
					"ARANA", "DORANTES", "BALAM", "SAENZ", "VASCO",
					"TALAMANTES", "ARCOS", "GONZALEZ", "XICOTENCATL", "CHI",
					"TAI","GOMEZ","UC","CHAN","PEREZ","PALACIOS","CANTUN",
                    "MARTINEZ","JIMENEZ", "GOMEZ"
					);
//Direcciones
$arrCalles		= array("SIEMPRE VIVA", "EMILIANO ZAPATA", "14", "HORMIGUERO", "TRANZAS",
						"POLITICOS", "PEDERNAL", "ACOSTA", "MIRAFLORES", "91",
						"69", "AMANTITA", "VICIOUS", "45", "CRISTO");
					
//Estado Civil
$arrECivil		= array("SOLTERO", "CASADO", "NINGUNO");
$arrVivienda	= array("PROPIA", "RENTADA", "NA");
$arrActividad	= array("NINGUNO", "ESTUDIANTE", "OBRERO", "EMPLEADO", "EMPRESARIO");

$arrCP			= array( 24000,24005,24008,24009,24010,24014,24019,24020,24023,24024,24025,24026,24027,24028,24029,24030,24035,24036,24037,
						24038,24039,24040,24044,24048,24049,24050,24058,24060,24063,24067,24069,24070,24073,24075,24079,24080,24083,24085,24086,24087,24088,24089,
						24090,24093,24094,24095,24096,24097,24098,24099,24100,24108,24109,24110,24114,24115,24116,24117,24118,24119,24120,24129,24130,24139,24140,
						24150,24153,24154,24155,24156,24157,24158,24160,24166,24167,24169,24170,24178,24179,24180,24185,24186,24187,24188,24189,24190,24195,24197,
						24198,24199,24200,24201,24202,24203,24204,24205,24206,24207,24230,24300 );
//

//date("Y-m-d", strtotime("$fecha+$ndias day"));
for ( $i = 1; $i <= 1000; $i++ ){
	
	$grupo			= 99;
	$cajaLocal		= 1;
	$socio			= $cajaLocal . substr("0000000000000000$i", -5);
	$ApPaterno		= $arrApellidos[ rand(0,19) ];
	$ApMaterno		= $arrApellidos[ rand(0,19) ];
	$xN				= rand(0,19);
	$NombreS		= $arrNombres[ $xN ];
	$tipo			= "FISICA";
	$genero			= ($xN < 9) ? "MASCULINO" : "FEMENINO";
	$FechaIng		= date("Y-m-d");
	$FechaNac		= date("Y-m-d", strtotime(rand(1975, 1995) . "-" . rand( 1,12) . "-" . rand(1,28)) );			//TODO: Mejorar
	$curp			= "XAXXX" . str_replace("-", "", $FechaNac) ;
	$EdoCivil		= $arrECivil[ rand(0, 2) ];
	//direccion
	$calle			= $arrCalles[ rand(0,14) ];
	$numero			= rand(1, 800);
	$codigoPost		= $arrCP[ rand(0, 99) ];				//TODO: Mejorar
	$tVivienda		= $arrVivienda[ rand(0, 2) ];
	$telefono		= "9818111111";					//TODO: Mejorar
	$mobil			= "9818111111";					//TODO: Mejorar
	$tutor			= "";
	$actividad		= $arrActividad[ rand(0, 4) ];
	$ingresoMens	= rand(2000, 8000);
	
	echo "$socio,$grupo,$cajaLocal,$ApPaterno,$ApMaterno,$NombreS,$tipo,$genero,$FechaIng,$FechaNac,$curp,$EdoCivil,$calle,$numero,$codigoPost,$tVivienda,$telefono,$mobil,$tutor,$actividad,$ingresoMens\r\n";
}

?>