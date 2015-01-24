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

class PosgreSql extends Database
{
	var $db_connect_id;
	
	function PosgreSql($database, $username, $password, $hostname = "localhost")
	{
		Database::Database($database, $username, $password, $hostname);
	}

	function Connect()
	{
		//$db = mysql_connect("localhost", $this->username, $this->password);
		//return mysql_select_db($this->database, $db);
		
		$connect_string = "";
		$connect_string .= "user=".$this->username." ";
		$connect_string .= "password=".$this->password." ";
		$connect_string .= "dbname=".$this->database;
		
		$this->db_connect_id = pg_connect($connect_string);
		
		return true;
	}
	
	function Disconnect()
	{
	    //return mysql_close();
	    return pg_close($this->db_connect_id);
	}
	
	function GetColumnNames($columns, $table)
	{
		$names = array();
		
		$result = mysql_query("SELECT ".$columns." FROM `".$table."` WHERE 1 = 2");
		$num_fields = mysql_num_fields($result);

		for ($i = 0; $i < $num_fields; $i++)
			$names[] = mysql_field_name($result, $i);

		return $names;
	}

	function Select($query)
	{
		return mysql_query($query);
	}
	
	function GetRows($columns, $table, $where_attributes, $order_column, $is_asc_order, $limit)
	{
		$order = "";
		if ($order_column != "")
		{
			$order_type = "";
			if ($is_asc_order == true)
				$order_type = "ASC";
			else
				$order_type = "DESC";
			
			$order = "ORDER BY ".$order_column." ".$order_type;
		}
		
		if ($where_attributes != "")
			$where_attributes = " WHERE ".$where_attributes;
			
		if ($limit != 0)
			$limit = "LIMIT 0,".$limit;
		else
			$limit = "";
			
		return mysql_query("SELECT ".$columns." FROM `".$table."` ".$where_attributes." ".$order." ".$limit);
	}
	
	function Delete($table, $column, $value)
	{
		mysql_query("DELETE FROM `".$table."` WHERE `".$column."` = '".$value."' LIMIT 1");
	}

	function Update($table, $id_column, $id_value, $column_names, $column_values)
	{
		$index = 0;
		$query  = "UPDATE `".$table."` SET ";
		
		foreach ($column_names as $name)
		{
			$query .= "`".$name."` = '".$column_values[$index]."'";
			
			$comma = "";
			if ($index != (count($column_names)-1))
				$comma = ",";
			$query .= $comma;
			
			$index++;
		}
		
		$query .= " WHERE `".$id_column."` = '".$id_value."' LIMIT 1";
		
		@mysql_query($query);
	}
	
	function Insert($table, $column_names, $column_values)
	{
		$index = 0;
		$query  = "INSERT INTO `".$table."` ( ";
		
		//Columns.
		foreach ($column_names as $name)
		{
			$query .= "`".$name."`";
			
			$comma = "";
			if ($index != (count($column_names)-1))
				$comma = ",";
			$query .= $comma;
			
			$index++;
		}
		
		$index = 0;
		$query .= ") VALUES (";

		//Values.
		foreach ($column_names as $name)
		{
			$query .= "'".$column_values[$index]."'";
			
			$comma = "";
			if ($index != (count($column_names)-1))
				$comma = ",";
			$query .= $comma;
			
			$index++;
		}

		$query .= ");";
		
		return @mysql_query($query);
	}
	
	function FreeResult($result)
	{
		//This function is not really necessarry. But if the function exists in PosgreSql it will speed up operations. 
		//@mysql_free_result($result);
	}
	
	function FetchArray($result)
	{
		return mysql_fetch_array($result);
	}
	
	function GetNumberOfRows($result)
	{
		return mysql_num_rows($result);
	}
}
?>
