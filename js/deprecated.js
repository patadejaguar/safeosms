function getVal(strID){
    console.log("WARN\tDeprecated function!!");
	return document.getElementById(strID).value;
}
function setVal(strID, strVal){
    console.log("WARN\tDeprecated function!!");
	if( document.getElementById(strID) ){
		document.getElementById(strID).value = strVal;
	}
}

function jsGenericWindow(mFile, winTop, winLeft, winHeight, winWidth){
    console.log("WARN\tDeprecated function!!");
    var mIDWin 			= Math.random();
    var windowName 		= "myWin" + mIDWin;
    if(!winLeft)	{	var winLeft		= parseInt(screen.width * 0.10);	}
    if(!winTop)		{	var winTop		= parseInt((screen.height * 0.10) + 100);	}
    if(!winHeight)	{	var winHeight	= parseInt((screen.height - (screen.height * 0.10) ) - 100);	}
    if(!winWidth)	{	var winWidth	= parseInt((screen.width - (screen.width * 0.10)));		}

	var windowFeatures	= "width=" + winWidth + ",height=" + winHeight + ",status,scrollbars,resizable,left=" + winLeft + ",top=" + winTop
	newWindow			= window.open(mFile, windowName, windowFeatures);
		newWindow.focus();
}
function checkDate(fld) {
    console.log("WARN\tDeprecated function!!");
    var mo, day, yr;
    var entry       = fld.value;
    var reLong      = /\b\d{1,2}[\/-]\d{1,2}[\/-]\d{4}\b/;
    var reShort     = /\b\d{1,2}[\/-]\d{1,2}[\/-]\d{2}\b/;
    var valid       = (reLong.test(entry)) || (reShort.test(entry));
    if (valid) {
	var delimChar = (entry.indexOf("/") != -1) ? "/" : "-";
	var delim1 	= entry.indexOf(delimChar);
	var delim2 	= entry.lastIndexOf(delimChar);
	mo 			= parseInt(entry.substring(0, delim1), 10);
	day 		= parseInt(entry.substring(delim1+1, delim2), 10);
	yr 			= parseInt(entry.substring(delim2+1), 10);
	// handle two-digit year
	if (yr < 100) {
	    var today       = new Date( );
	    // get current century floor (e.g., 2000)
	    var currCent    = parseInt(today.getFullYear( ) / 100) * 100;
	    // two digits up to this year + 15 expands to current century
	    var threshold   = (today.getFullYear( ) + 15) - currCent;
	    if (yr > threshold) {
		yr += currCent - 100;
	    } else {
		yr += currCent;
	    }
	}
	var testDate = new Date(yr, mo-1, day);
	if (testDate.getDate( ) == day) {
	    if (testDate.getMonth( ) + 1 == mo) {
		if (testDate.getFullYear( ) == yr) {
		    // fill field with database-friendly format
		    fld.value = mo + "/" + day + "/" + yr;
		    return true;
		} else {
		    alert("There is a problem with the year entry.");
		}
	    } else {
		alert("There is a problem with the month entry.");
	    }
	} else {
	    alert("There is a problem with the date entry.");
	}
    } else {
	alert("Incorrect date format. Enter as mm/dd/yyyy.");
    }
    return false;
}