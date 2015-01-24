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

class Column
{
	var $is_asc_order = true;
	var $name;
	var $user_defined_name = "";
	var $is_id = false;
	var $width = 0;
	var $show = true;
	var $editable = true;
	var $plugins = array();	
	
	function Column($name)
	{
		$this->name = $name;
	}
}

?>
