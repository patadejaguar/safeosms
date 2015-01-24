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

class Excel
{
	var $data = "";
	
	function Excel()
	{
	}
	
	function SetData($excel_info)
	{
		$excel_info = str_replace("\r","",$excel_info);
		$excel_info = str_replace("\t","",$excel_info);
		$excel_info = str_replace("\n","",$excel_info);
		$excel_info = str_replace("\011","",$excel_info);
		$excel_info = str_replace("\0113","",$excel_info);
		$excel_info = str_replace("excel_tab","\t",$excel_info);
		$this->data = str_replace("excel_new_line","\n",$excel_info);
	}
	
	function GenerateExcelFile($output_file)
	{
		@$fp = fopen($output_file, "wb");
		if (!is_resource($fp))
		{
		    //die("Cannot open $output_file");
		    return false;
		}
		
		@fwrite($fp, $this->data);
		@fclose($fp);
		
		return true;
	}
}

?>
