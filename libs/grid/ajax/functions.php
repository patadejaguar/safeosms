<?php

class gridajax
{

function AjaxSort($grid_id, $column_order_key)
{
	$_SESSION[$grid_id]->order_column_index = $column_order_key; 

	if ($_SESSION[$grid_id]->columns[$column_order_key]->is_asc_order == true)
		$_SESSION[$grid_id]->columns[$column_order_key]->is_asc_order = false;
	else 
		$_SESSION[$grid_id]->columns[$column_order_key]->is_asc_order = true;

	return ($_SESSION[$grid_id]->CreateGrid());
}

function AjaxSaveColumnWidth($grid_id, $left_index, $left_width, $right_index, $right_width)
{
	$_SESSION[$grid_id]->columns[$left_index]->width = $left_width;
	$_SESSION[$grid_id]->columns[$right_index]->width = $right_width;
}

function AjaxChangePage($grid_id, $next)
{
	
	$_SESSION[$grid_id]->ChangePage($next);

	return ($_SESSION[$grid_id]->createGrid());
}

function AjaxGoToPage($grid_id, $go_to_page)
{
	
	$_SESSION[$grid_id]->GoToPage($go_to_page);

	return ($_SESSION[$grid_id]->CreateGrid());
}

function AjaxCreateExcelFile($grid_id)
{

	return ($_SESSION[$grid_id]->CreateExcelFile());
}

function AjaxDeleteRow($grid_id, $column_name, $column_value)
{
	$_SESSION[$grid_id]->DeleteRow($column_name, $column_value);
	
	return $_SESSION[$grid_id]->CreateGrid();
}

function AjaxShowEditForm($grid_id, $id_column_value)
{
	$_SESSION[$grid_id]->id_row_value_edit_form = $id_column_value;

	return $_SESSION[$grid_id]->CreateGrid();
}

function AjaxSaveForm($id_column, $id_value, $grid_id, $values_string)
{
	$values = array();
	$values = split("value_separator_grid", $_SESSION[$grid_id]->StripTags(trim($values_string)));

	$_SESSION[$grid_id]->SaveRow($id_column, $id_value, $values);
	$_SESSION[$grid_id]->id_row_value_edit_form = ""; //Remove inputs. 

	return( $_SESSION[$grid_id]->CreateGrid());
}

function AjaxSaveInsertForm($id_column, $grid_id, $values_string)
{
	$values = array();
	$values = split("value_separator_grid", $_SESSION[$grid_id]->StripTags(trim($values_string)));

	$_SESSION[$grid_id]->InsertRow($id_column, $values);

	return( $_SESSION[$grid_id]->CreateGrid());
}


function AjaxSetSelectedRow($grid_id, $row_id)
{
	$objResponse = new xajaxResponse();

	if ($row_id != $_SESSION[$grid_id]->selected_row_id)
	{
		$_SESSION[$grid_id]->selected_row_id = $row_id;
		$objResponse->addAssign($grid_id, "innerHTML", $_SESSION[$grid_id]->CreateGrid());
	}
		
	return $objResponse;
}

}
?>
