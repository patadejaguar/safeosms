<?php

// Kickstart the framework
$f3=require('../../libs/fatfree/lib/base.php');


$f3->route("GET /@persona", function ($f3){
	echo "Manejo de Personas 2";
});

$f3->route("GET /", function ($f3){
	
	echo "Manejo de Personas";
	
});
	
$f3->run();

?>