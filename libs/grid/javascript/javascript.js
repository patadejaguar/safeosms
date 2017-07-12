// Detect if the browser is IE or not.
// If it is not IE, we assume that the browser is NS.
var IE = document.all?true:false

// If NS -- that is, !IE -- then set up for mouse capture
//if (!IE) document.captureEvents(Event.MOUSEMOVE)

// Set-up to use getMouseXY function onMouseMove
document.onmousemove = getMouseXY;
document.onkeydown = getMouseXY;


// Temporary variables to hold mouse x-y pos.s
var MouseX = 0;
var MouseY = 0;

var MouseDown = false;
var MouseStartDragX = -1;
var LeftCellOrgWidth = -1;
var RightCellOrgWidth = -1;
var MinimumCellWidth = 10;
var LeftCell = "gridbasic2_cell_0";
var RightCell = "gridbasic2_cell_1";
var NumberOfRows = 0;
var LeftColumnIndex = -1;
var RightColumnIndex = -1;
var ColumnName = "";
var GridId = "";
var IsInputClick = false;

// Main function to retrieve mouse x-y pos.s
function getMouseXY(e) 
{
	var targ;
	if (!e) 
		e = window.event;
	if (e.target) 
		targ = e.target;
	else if (e.srcElement) 
		targ = e.srcElement;
	
	if (targ != null)
	{
		if (targ.nodeType == 3) // defeat Safari bug
			targ = targ.parentNode;
	}
	
	var code;
	if (e.keyCode) 
		code = e.keyCode;
	else if (e.which) 
		code = e.which;
	
	if (e.pageX || e.pageY)
	{  // grab the x-y pos.s if browser is NS
		MouseX = e.pageX;
		MouseY = e.pageY;
	} 
	else if (e.clientX || e.clientY)
	{
		//What is wrong ???
		if (e == "[object]" && document.body == "[object]")
		{
			if (e.clientX != null && document.body.scrollLeft != null)
				MouseX = e.clientX + document.body.scrollLeft;
			else
				MouseX = 0;
			
			if (e.clientY != null && document.body.scrollTop != null)
				MouseY = e.clientY + document.body.scrollTop;
			else
				MouseY = 0;
		}
	}
	
	// catch possible negative values in NS4
	if (MouseX < 0){MouseX = 0;}
	if (MouseY < 0){MouseY = 0;}

	//MoveSquare();
	UpdateColumnWidth();
}

function GetMouseX()
{
	return MouseX;
}

function GetMouseY()
{
	return MouseY;
}

function SetMouseDown(down)
{
	MouseDown = down;
	
	if (MouseDown == true)
	{
		//MouseDown.
		if (MouseStartDragX == -1)
		{	
			MouseStartDragX = GetMouseX();
	
			LeftCellOrgWidth = RemovePxFromString(document.getElementById(LeftCell).style.width);
			//alert(LeftCellOrgWidth);
			RightCellOrgWidth = RemovePxFromString(document.getElementById(RightCell).style.width);
		}
	}
	else
	{
		//MouseUp.
		SaveColumnWidth();
		MouseStartDragX = -1;
		LeftCellOrgWidth = -1;
		RightCellOrgWidth = -1;
	}
}

function UpdateColumnWidth()
{
	
	if (MouseDown == true && IsInputClick == false)
	{
		
		var length = GetMouseX() - MouseStartDragX;
		
		if ((LeftCellOrgWidth+length) > MinimumCellWidth && (RightCellOrgWidth+(length*-1)) > MinimumCellWidth)
		{
			//Column names.
			//alert(ColumnName+LeftColumnIndex);
			document.getElementById(ColumnName+LeftColumnIndex).style.width = (LeftCellOrgWidth+length)+"px";
			document.getElementById(ColumnName+RightColumnIndex).style.width = (RightCellOrgWidth+(length*-1))+"px";
			
			var i = 0;
			for (i = 0; i < NumberOfRows; i++)
			{
				//Rows.
				//Number of rows may not be correct at the last page.
				if (document.getElementById(LeftCell+i) != null)
				{
					document.getElementById(LeftCell+i).style.width = (LeftCellOrgWidth+length)+"px";
					document.getElementById(RightCell+i).style.width = (RightCellOrgWidth+(length*-1))+"px";
				}
			}
		}
	}
	IsInputClick = false;
	//alert('aa');
}

function SetCellNames(gridid, left, right, numberofrows, leftcolumnindex, leftcolumnname)
{
	GridId = gridid;
	LeftCell = left;
	RightCell = right;
	NumberOfRows = numberofrows;
	LeftColumnIndex = leftcolumnindex;
	RightColumnIndex = leftcolumnindex+1;
	ColumnName = leftcolumnname;
}

function SaveColumnWidth()
{
	if (LeftColumnIndex != -1 && RightColumnIndex != -1)
	{
		var left_width = RemovePxFromString(document.getElementById(ColumnName+LeftColumnIndex).style.width);
		var right_width = RemovePxFromString(document.getElementById(ColumnName+RightColumnIndex).style.width);
		//xajax_AjaxSaveColumnWidth(GridId, LeftColumnIndex, left_width, RightColumnIndex, right_width);
	}
}

function RemovePxFromString(str)
{
	var num = new String(str);
	num = num.replace("px", "");
	return parseInt(num);
}

function GetFormValues(form_input_id, number_of_fields)
{
	var str = "";
	var i = 0;
	
	for (i = 0; i < number_of_fields; i++)
	{
		var form_value = ""; //maybe hidden field.
		
		if (document.getElementById(form_input_id+i) != null)
		{	
			var no_single_quote = new String(document.getElementById(form_input_id+i).value);
			no_single_quote = no_single_quote.replace("'", "");
			
			//form_value = document.getElementById(form_input_id+i).value; 
			form_value = no_single_quote;
		}
		str += form_value;
		
		if (i != (number_of_fields-1))
			str += "value_separator_grid";
	}
	
	return str;
}

function SetIsInputClick(InputClick)
{
	IsInputClick = InputClick;
}
function jsActivateEvents(evt){
	//evt.addEventListener("onkeyup", jsEventsKeys, false);
	evt.onkeyup	= jsEventsKeys;
	evt.select();
}
function jsEventsKeys(event){
	event.preventDefault();
	switch (event.keyCode) {
		case 13:
			//console.log( document.getElementById("cmdsave").id );
			document.getElementById("cmdsave").click();
			break;
		case 27:
			//console.log( document.getElementById("cmdsave").id );
			document.getElementById("cmdcancel").click();
			break;		
		default:
			break;
	}
}
