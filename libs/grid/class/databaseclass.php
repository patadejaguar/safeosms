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

class Database
{
	var $database; 
	var $username; 
	var $password;
	var $hostname;

	var $connection; 
	
	function Database($database, $username, $password, $hostname = "localhost")
	{
		$this->database = $database;
		$this->username = $username;
		$this->password = $password;
		$this->hostname = $hostname;
	}

	function Connect()
	{
	}
	
	function Disconnect()
	{
	}
	
	function Select()
	{
	}

	function Update()
	{
	}
	
	function Delete()
	{
	}
	
	function GetColumnNames()
	{
	}
	
	function GetRows($columns, $table, $order_column, $where_attributes, $is_asc_order, $limit)
	{
	}
	
	function FreeResult($result)
	{
	}
	
	function FetchArray($result)
	{
	}
	
	function GetNumberOfRows($result)
	{
	}
}

?>
