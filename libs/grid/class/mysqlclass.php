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

class MySql extends Database
{
	private $mCnn	= false;
	function MySql($database, $username, $password, $hostname = "localhost")
	{
		Database::Database($database, $username, $password, $hostname);
	}

	function Connect()
	{
		//$this->mCnn	= ($this->mCnn === false) ? mysqli_connect($this->hostname, $this->username, $this->password, $this->database) : $this->mCnn;
		//if($this->mCnn == false){
			$this->mCnn	= new mysqli($this->hostname, $this->username, $this->password, $this->database);
		//}
		return $this->mCnn;
	}
	
	function Disconnect(){
		//return $this->mCnn->close();
	}
	
	function GetColumnNames($columns, $table)
	{
		$names 		= array();
		$rs			= $this->Connect()->query("SELECT ".$columns." FROM ".$table." LIMIT 1");
		while($obj	= $rs->fetch_field()){
			$names[]= $obj->name;
		}
		//$rs->free();
		return $names;
	}

	function Select($query)
	{
		$rs			= $this->Connect()->query($query);
		$data		= array();
		while ($row = $rs->fetch_assoc()) { 	$data[]		= $row;}
		$rs->free();
		return $data;
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
			
		$sql = "SELECT ".$columns." FROM ".$table." ".$where_attributes." ".$order." ".$limit;
		#var_dump($sql);
		$rs	= $this->Connect()->query($sql);
		return $rs;
	}
	
	function Delete($table, $column, $value)
	{
		$this->Connect()->query("DELETE FROM `".$table."` WHERE `".$column."` = '".$value."' LIMIT 1");
		//$rs->free();
		return true;
	}

	function Update($table, $id_column, $id_value, $column_names, $column_values){
		//$GLOBALS[] $query;
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
		#var_dump($query);
		$this->Connect()->query($query);
		
		return true;
	}
	
	function Insert($table, $column_names, $column_values)
	{
		$index 	= 0;
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
		//syslog(LOG_DEBUG, $query);
		$this->Connect()->query($query);
		return true;//@mysql_query($query);
	}
	
	function FreeResult($result)
	{
		//$result->free();
	    //@mysql_free_result($result);
	}
	
	function FetchArray($rs){ return $rs->fetch_assoc(); }
	
	function GetNumberOfRows($table, $where)
	{
		$sql 	= sprintf("SELECT COUNT(*) as TOTALFOUND FROM $table %s", !empty($where) ? "WHERE $where": "");
		$rs		= $this->Connect()->query($sql);
		$data	= $rs->fetch_assoc();
		$nrows	= isset($data["TOTALFOUND"]) ? $data["TOTALFOUND"] : 0;
		$data	= null;
		$rs->free();	
		return $nrows;
	}
}
?>
