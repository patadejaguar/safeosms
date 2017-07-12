<?php
include_once ("../core/core.deprecated.inc.php");
include_once ("../core/core.config.inc.php");
//ini_set("display_errors", "on");

ini_set("max_execution_time", 1800);
function purgedbf($db = "ctw10005.dbf", $key = "EJE", $id = "2007"){

	$pathdbase = CTW_PATH . "/" . $db;
	//$db = dbase_open($ipdbase . $dirdbase . $nombredb . $db, 0);
	$rs = dbase_open($pathdbase, 2);
	//echo $pathdbase;

	$num_rows = dbase_numrecords ($rs);
	$o_num_rows = dbase_numrecords ($rs);
	if ($num_rows > 100000) {
				$num_rows = 100000;				//Eliminar la Consulta a 50000
	}
	for ($i=1; $i <= $num_rows; $i++){
	    $field = dbase_get_record_with_names ($rs, $i);

	    if (trim ($field[$key])!= $id)   {
	        if (dbase_delete_record ($rs, $i)) {
	           // print "Registro $i Marcado para Eliminar de un total de $o_num_rows; se Busco $field[$key]. <br >";
	            //break; # Exit the loop
	        }
	    }
	}
dbase_pack($rs);
dbase_close ($rs);
echo "<p>PURGA EFECTUADA DE $db SEGUN $key sobre $id A LAS " . date("H:i:s") . " HRS </p>";
}
//purgedbf("ctw10005.dbf");
//purgedbf("ctw10005.dbf", "TIPO", "1");
//purgedbf("ctw10004.dbf");
function importar_ctw($db = "ctw10005.dbf", $id = "2007"){
	//elimina la anterior
	mysql_unbuffered_query("TRUNCATE compacw_importados");

	$pathdbase = CTW_PATH . "/" . $db;
	//$db = dbase_open($ipdbase . $dirdbase . $nombredb . $db, 0);
	$rs = dbase_open($pathdbase, 2);
	//echo $pathdbase;

	$num_rows = dbase_numrecords ($rs);
	//$o_num_rows = dbase_numrecords ($rs);
	//$num_rows = 100000;				//Eliminar la Consulta a 50000

for ($i=1; $i <= $num_rows; $i++){
    $field = dbase_get_record_with_names ($rs, $i);

    if (trim($field["EJE"])== $id and trim($field["TIPO"])==1)   {
		//

			$values_sql = " ('" . $field["CUENTA"] . "', " . $field["IMP1"] . ") ";
			$sql_ex = "INSERT INTO compacw_importados(cuenta, saldo) VALUES " . $values_sql;
			mysql_unbuffered_query($sql_ex);

		//
    }
}	//

//dbase_pack($rs);
dbase_close ($rs);


	echo "<p>DATOS IMPORTADOS:  " . date("H:i:s") . " HRS </p>";
}

function volcarDBF($db = "ctw10005"){
	$results	= array();
$mFile 	= $db . date("Ymd") . ".txt";
//Abrir el Archivo para Escribir
	$TFile	= fopen(PATH_TMP . $mFile, "a");

	$pathdbase = CTW_PATH . vLITERAL_SEPARATOR . $db . ".dbf";
	//$db = dbase_open($ipdbase . $dirdbase . $nombredb . $db, 0);
	$rs = dbase_open($pathdbase, 0);
	//echo $pathdbase;
	$results[SYS_MSG] .= "Abrir $pathdbase <br />";
	$num_rows = dbase_numrecords ($rs);
	//$o_num_rows = dbase_numrecords ($rs);
	if ($num_rows > 100000) {
				//$num_rows = 100000;				//Eliminar la Consulta a 50000
		}
	$results[SYS_MSG] .= "Numero de Filas $num_rows <br />";
	if(isset($rs)){
		$results[SYS_MSG] .= "Cerrando " . dbase_get_header_info($rs) . " <br />";
		for ($i=1; $i <= $num_rows; $i++){
    		//$field = dbase_get_record_with_names($rs, $i);
    		$field = dbase_get_record($rs, $i);
    		$lim 	= sizeof($field);
    		$strW	= "";
    			for($a=0; $a<$lim; $a++){
		    		if($a == 0){
    					$strW .= trim($field[$a]);
    				} else {
	    				$strW .= STD_LITERAL_DIVISOR . trim($field[$a]);
    				}
    			}
	    		$strW .= "\n";
    			@fwrite($TFile, $strW);
        //if (dbase_delete_record ($rs, $i)) {
           // print "Registro $i Marcado para Eliminar de un total de $o_num_rows; se Busco $field[$key]. <br >";
            //break; # Exit the loop
        //}
		}
	} else {
		//dbase_get_header_info($rs);
	}
//dbase_pack($rs);
//$results[SYS_MSG] .= " <br />";
dbase_close ($rs);
$results[SYS_MSG] .= "Cerrando $pathdbase <br />";
fclose($TFile);
$results[SYS_MSG] .= "Cerrando $mFile <br />";
return $results;
}
?>