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



function KeyPress(e){
     
	var evtobj = window.event? event : e
      if (evtobj.keyCode == 90 && evtobj.ctrlKey) {
         // Ctrl + Z
         
		 	var ta = $('body').html()
            var converted = htmlDocx.asBlob(ta);
			saveAs(converted, 'test.docx');
     }
      if (evtobj.keyCode == 88 && evtobj.ctrlKey) {
         // Ctrl + x
			var specialElementHandlers = { '#bypassme': function(element, renderer) { return true; } };
			var pdf = new jsPDF('p', 'pt', 'letter');
			var ta 	= $('body').html()
			//console.log(ta.html());
			pdf.fromHTML(ta, 0.5, 0.5, {'width': 7.5, // max width of content on PDF
										'elementHandlers': specialElementHandlers
										});
			pdf.save('reporte.pdf');
     }	 
}



/*var ReportMenu = [{
        name: 'pdf',
        img: '../images/pdf.png',
        title: 'Exportar a PDF',
        fun: function () {
			var specialElementHandlers = { '#bypassme': function(element, renderer) { return true; } };
			var pdf = new jsPDF('p', 'pt', 'letter');
			var ta = $('body').html()
			//console.log(ta.html());
			pdf.fromHTML(ta, 0.5, 0.5, {
'width': 7.5, // max width of content on PDF
'elementHandlers': specialElementHandlers
});
			pdf.save('reporte.pdf');
        }
    }, {
        name: 'docx',
        img: '../images/docx.png',
        title: 'Exportar a Docx',
        fun: function () {
			var ta = $('body').html()
            var converted = htmlDocx.asBlob(ta);
			saveAs(converted, 'test.docx');
        }
    }, {
        name: 'print',
        img: '../images/printer.png',
        title: 'delete button',
        fun: function () {
            alert('i am delete button')
        }
    }];*/
