
Date.prototype.getISO	= function(){
    var md  = new String(this.getFullYear() + "-" + (this.getMonth() + 1) + "-" + this.getDate());
    var v   = md.split("-");
    var d1  = new String(v[0]);
    var d2  = new String(v[1])
    var d3  = new String(v[2])
    d1 = (d1.length ==2 ) ? "20" + d1 : d1;
    d2 = (d2.length ==1 ) ? "0" + d2 : d2;
    d3 = (d3.length ==1 ) ? "0" + d3 : d3;
    return d1 + "-" + d2 + "-" + d3;	
}

function checkDate(fld) {
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
                yr 			+= currCent - 100;
            } else {
                yr 			+= currCent;
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

function jsSumarDias(vFecha, days){
    var mDays   = parseInt(days);
    vFecha	= new String(vFecha);
    var sDays	= 86400000 * mDays;
    var sDate   = vFecha.split('-');
    var varDate = new Date(sDate[0], parseInt(sDate[1]-1), parseInt(sDate[2])-1, 0,0,0 );

    var vDate	= varDate.getTime()+sDays;
	varDate.setTime( vDate );
	
    var mMonth  = varDate.getMonth()+1;
    var mDate	= varDate.getDate()+1;
    if (mMonth == 0){
        alert('Error al Determinar el Mes ' + mMonth + ' en la Fecha ' + vFecha);
    }
	return varDate.getFullYear() + '-' + mMonth + '-' + mDate;
}
function jsRestarDias(vFecha, days){
	
    var mDays   = new Number(days);
    vFecha	= new String(vFecha);
    var sDays	= 86400000 * mDays;
    var sDate   = vFecha.split('-');
    var varDate = new Date(sDate[0], parseInt(sDate[1]-1), parseInt(sDate[2])-1, 0,0,0 );

    var vDate	= varDate.getTime()-sDays;

	varDate.setTime(vDate);
    var mMonth  = varDate.getMonth()+1;
    var mDate	= varDate.getDate()+1;
    
    if (mMonth == 0){
        jsSetTrace('Error al Determinar el Mes ' + mMonth + ' en la Fecha ' + vFecha);
    }
	return varDate.getFullYear() + '-' + mMonth + '-' + mDate;
}
Number.prototype.formatMoney = function(c, d, t){
var n = this; c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

