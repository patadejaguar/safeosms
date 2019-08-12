<?php
// *****************************************************************************
// *  slGrid 2.0                                                            *
// *  http://slgrid.senzalimiti.sk                                             *
// *                                                                           *
// *  Copyright (c) 2006 Senza Limiti s.r.o.                                   *
// *                                                                           *
// *  This program is free software; you can redistribute it and/or            *
// *  modify it under the terms of the GNU General Public License              *
// *  as published by the Free Software Foundation; either version 2           *
// *  of the License, or (at your option) any later version.                   *
// *                                                                           *
// *  This program is distributed in the hope that it will be useful,          *
// *  but WITHOUT ANY WARRANTY; without even the implied warranty of           *
// *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            *
// *  GNU General Public License for more details.                             *
// *                                                                           *
// *  For commercial licenses please contact Senza Limiti at                   *
// *  - http://www.senzalimiti.sk                                              *
// *  - info(at)senzalimiti.sk                                                 *
// *****************************************************************************
if( !defined("GRID_SOURCE") ){
	//define("GRID_SOURCE", str_replace(DIRECTORY_SEPARATOR, '/', realpath(dirname(__FILE__))));
	define("GRID_SOURCE", "");
}
//include_once '../../core/core.db.inc.php';
// TODO: Modificado para que corra el sistema

//error_reporting(E_ALL|E_NOTICE);
	include_once("class/gridclasses.php");

@session_start();

include 'HTML/AJAX/Server.php';
include_once './ajax/functions.php';


$gridajax 	= new gridajax();

$server 	= new HTML_AJAX_Server();
$server->registerClass($gridajax);
$server->handleRequest();
?>
