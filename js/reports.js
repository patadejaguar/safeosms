var RepGen		= function(){
				this.recordsByPage	= 90;
				};

var mURI		= "current.location";
RepGen.prototype.init = function (){
	this.setPageAction();
}
RepGen.prototype.setPagePagination	= function (start){
start = (typeof start == "undefined") ? 0 : start;
 var currURL	= document.URL;
 var ireg		= new RegExp("init=\d+$", "g");
 currURL		= String(currURL).replace(/&init=\d+$/g, "");
 currURL		+= "&init=" + start;
	session(mURI, currURL);
	console.log(currURL);
	document.onkeydown = setCharPagination;
	
}

RepGen.prototype.print	= function(){
				window.print();
}

function setCharPagination(evt){
    evt=(evt) ? evt:event;
    var charCode = (evt.charCode) ? evt.charCode :
        ((evt.which) ? evt.which : evt.keyCode);
    switch(charCode){
        case 33:		//PageUp
            history.back();
        break;
        case 34:        //PageDown

            location.href = session(mURI);
        break;
        case 27:        //Escape
            window.close();
        break;
        default:
            //return false;
        break;
    }
}

