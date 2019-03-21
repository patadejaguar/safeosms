<?php
	define("DATABASE_MYSQL", 1);
	define("DATABASE_POSGRESQL", 2);
	
	define("MODE_VIEW", 1);
	define("MODE_EDIT", 2);

	include(GRID_SOURCE."class/gridclass.php");
	include(GRID_SOURCE."class/databaseclass.php");
	include(GRID_SOURCE."class/mysqlclass.php");
	include(GRID_SOURCE."class/posgresqlclass.php");
	include(GRID_SOURCE."class/columnclass.php");
	include(GRID_SOURCE."class/excelclass.php");
	
	include(GRID_SOURCE."ajax/functions.php");
	
	
?>
