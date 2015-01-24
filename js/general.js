DIV_DEC 		= ".";
DIV_MIL			= ",";
SEPARADOR_DECIMAL	= ".";
var IMG_LOADING		= "<span><img src=\"../images/loading.gif\" ></span>";
var SCREENW		= window.innerWidth;
var SCREENH		= window.innerHeight;
var ID_WORK			= "id.work.current";
var ID_WORK_PROP	= "id.work.current.property";
var ID_PERSONA		= "id.persona.current";

var UPDWIN		= null;
var Gen			= function(){};
var CredGen		= function(){};
var PersGen		= function(){};
var PersAEGen	= function(){};
var CaptGen		= function(){};
var RecGen		= function(){};
var TesGen		= function(){};
var EmpGen		= function(){};
var ContGen		= function(){};
var GroupGen	= function(){};
var AmlGen		= function(){};
var DomGen		= function(){};

var ValidGen	= function(){};

Gen.prototype.alto	= function(){
		var mSz	= getClientSize();
		return mSz.height;
}
Gen.prototype.ancho	= function(){
		var mSz	= getClientSize();
		return mSz.width;
}
Gen.prototype.isKey	= function(opts){
	var charCode = 0;
	opts			= (typeof opts == "undefined") ? {} : opts;
	if (typeof opts.evt != "undefined" ) {
		charCode = ( opts.evt.charCode) ? opts.evt.charCode : ((opts.evt.which) ? opts.evt.which : opts.evt.keyCode);
	}
	var callbackF 	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var mNum 		= (typeof opts.numerico == "undefined") ? false : true;
	var isKeyCode	= false;
	if (mNum == true) {
		if ((charCode >= 48 && charCode <= 57)||(charCode >= 96 && charCode <= 105)){	setTimeout(callbackF, 0); isKeyCode		= true;	}
	} else {
		if (charCode >= 65 && charCode <= 90){	setTimeout(callbackF, 0); isKeyCode		= true;	}
	}
	return isKeyCode;
}
Gen.prototype.isTextKey	= function(opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	return this.isKey(opts);
}
Gen.prototype.isNumberKey	= function(opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	opts.numerico	= true;
	return this.isKey(opts);
}
Gen.prototype.getLog	= function(opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	//DroidError("Query " + this.getIn() + oper);
	var callback	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;

	var AjxOpts	= {
		url		: "../svc/log.svc.php" , // cmd + tabla + primaryKey + registro
		contentType	: "json",
		success		: function(rs){
			if (typeof rs.message != "undefined") {
				if ($.trim(rs.message) != "") {
					callback(rs.message);
					setLog(rs.message);
				}
				
			}
		}
	};

	$.ajax(AjxOpts);	
}
Gen.prototype.clearSession	= function(){ window.localStorage.clear(); }
function session(v1,v2){
	if(typeof v2 == "undefined"){
		return window.localStorage.getItem(v1);
	} else {
		window.localStorage.setItem(v1, v2);
		return 0;
	}
}

function getClientSize() {
  var width = 0, height = 0;

  if(typeof(window.innerWidth) == 'number') {
        width = window.innerWidth;
        height = window.innerHeight;
  } else if (document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)) {
        width = document.documentElement.clientWidth;
        height = document.documentElement.clientHeight;
  } else if (document.body && (document.body.clientWidth || document.body.clientHeight)) {
        width = document.body.clientWidth;
        height = document.body.clientHeight;
  }
  return {width: width, height: height};
}

Gen.prototype.w	= function(opts){
		var mSz	= getClientSize();
		var LimAl 	= mSz.height;
		var LimAn	= mSz.width;
	opts			= (typeof opts == "undefined") ? {} : opts;
	var callbackF 	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var url		= (typeof opts.url == "undefined") ? "" : opts.url;
	//var args	= (typeof opts.args == "undefined") ? "" : opts.args;
	var wd		= (typeof opts.w == "undefined") ? entero((LimAn * 0.85)) : entero(opts.w);
	var hg		= (typeof opts.h == "undefined") ? entero((LimAl * 0.75)) : entero(opts.h);
	var ifull	= (typeof opts.full == "undefined") ? false : opts.full;
	var otags	= (typeof opts.tags == "undefined") ? true : opts.tags;

	var tiny	= (typeof opts.tiny == "undefined") ? false : opts.tiny;
		wd		= (wd > LimAn ) ? LimAn : wd;
		hg		= (hg > LimAl) ? LimAl : hg;
		tp		= entero( (LimAl - hg)/2 );
		lf		= entero( (LimAn - wd)/2 );
	if(ifull == true){
		wd		= LimAn;
		hg		= LimAl;
		tp		= 0;
		lf		= 0;
	}
	var name	= "";
	var specs	= "resizable=no,modal=yes,scrollbars=yes,location=no,status=no,height=" + hg + ",width=" + wd + ",top=" + tp + ",left=" + lf;
	if(tiny == false){
		UPDWIN		= window.open(url,name,specs); UPDWIN.focus();
	} else {
		if (otags == true) { url	= url + "&tinybox=true"; }
		TINY.box.show({iframe: url ,boxid:'frameless', width:wd, height:hg, fixed:false, maskid:'bluemask',maskopacity:40,closejs: callbackF });
	}
}
Gen.prototype.rz	= function(opts){
	var mSz		= getClientSize();
	var LimAl 	= mSz.height;
	var LimAn	= mSz.width;
	opts		= (typeof opts == "undefined") ? {} : opts;
	var callbackF 	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var url		= (typeof opts.url == "undefined") ? "" : opts.url;
	var w		= (typeof opts.w == "undefined") ? LimAn : opts.w;
	var h		= (typeof opts.h == "undefined") ? LimAl : opts.h;
	if (opener) { window.resizeTo(w, h); }
}
Gen.prototype.pajax	= function(opts){
	//DroidError("Query " + this.getIn() + oper);
	opts		= (typeof opts == "undefined") ? {} : opts;
	var callback	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var finder	= (typeof opts.finder == "undefined") ? "" : opts.finder;
	var murl	= (typeof opts.url == "undefined") ? "" : opts.url;
	var frm		= (typeof opts.form == "undefined") ? "" : opts.form;
	var method	= (typeof opts.method == "undefined") ? "GET" : opts.method;
	var res		= (typeof opts.result == "undefined") ? "xml" : opts.result;
	var extra	= (typeof opts.extra == "undefined") ? "" : opts.extra;

	var AjxOpts	= {
		url		: murl, // cmd + tabla + primaryKey + registro
		type		: method,
		contentType	: res,
		success		: function(rs){
			if (res == "xml") {
				//read nodes string
				var size	= $(rs).find(finder).size();
				$(rs).find(finder).each(function(index){
					index		= index+1;
					var final	= (index == size) ? true : false;
					callback(this, final);
					});
			} else {
				callback(rs);
			}
		}
	};
	//agregar datos del Form
	if (frm != ""){
		AjxOpts["data"]	= frm.serialize() + extra;
	}

	$.ajax(AjxOpts);
}
Gen.prototype.sigma	= function(opts){
	opts = (typeof opts == "undefined" ) ? {} : opts;
	var callback	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var murl		= (typeof opts.url == "undefined") ? "" : opts.url;
	var mid			= (typeof opts.id == "undefined") ? "" : opts.id;
	if(typeof sigma != "undefined"){
		  sigma.parsers.json(murl, {
			    container: mid,
			    settings: {
			    	labelThreshold: 0,
			    	edgeLabelSize: 'proportional'
			    }
			  });		
	}
}
Gen.prototype.letras	= function(opts){
	//DroidError("Query " + this.getIn() + oper);
	var callback	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var mid		= (typeof opts.id == "undefined") ? "" : opts.id;
	var mny		= (typeof opts.monto == "undefined") ? 0 : opts.monto;
	var AjxOpts	= {
		url		: "../svc/cantidad_en_letras.php?cantidad=" + mny, // cmd + tabla + primaryKey + registro
		contentType	: "json",
		success		: function(rs){
			if (typeof rs.letras != "undefined") {
				if (mid != ""){ $("#" + mid).val(rs.letras); }
				callback(rs.letras);
			} else {
				console.log("Error en conversion");
			}
		}
	};

	$.ajax(AjxOpts);
}

Gen.prototype.equivalencia	= function(opts){
	//DroidError("Query " + this.getIn() + oper);
	var callback	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var mid		= (typeof opts.id == "undefined") ? "" : opts.id;
	var mny		= (typeof opts.monto == "undefined") ? 0 : opts.monto;
	var mon		= (typeof opts.moneda == "undefined") ? "MXN" : opts.moneda;
	var AjxOpts	= {
		url		: "../svc/equivalente.moneda.svc.php?arg1=" + mny + "&arg2=" + mon, // cmd + tabla + primaryKey + registro
		contentType	: "json",
		success		: function(rs){
			if (typeof rs.equivalencia != "undefined") {
				if (mid != ""){ $("#" + mid).val(rs.equivalencia); }
				callback(rs);
			} else {
				console.log("Error en conversion");
			}
		}
	};

	$.ajax(AjxOpts);
}

Gen.prototype.home 	= function (opts){
	opts = (typeof opts == "undefined" ) ? {} : opts;
	var page	= (typeof opts.page == "undefined") ? "" : "?page=" + opts.page;
	var msg		= (typeof opts.msg == "undefined") ? "" : opts.msg;
	var delay	= (typeof opts.delay == "undefined") ? 4000 : opts.delay;
	var mfunc	= function(){ top.location	= url; };
	if (msg != "") {
		alert(msg);
	}
	top.location	= "../index.xul.php" + page;
}

Gen.prototype.tip 	= function (opts){
	opts = (typeof opts == "undefined" ) ? {} : opts;
	var id		= (typeof opts.element == "undefined") ? window : opts.element;
	var msg		= (typeof opts.msg == "undefined") ? "" : opts.msg;
	var delay	= (typeof opts.delay == "undefined") ? 4000 : opts.delay;
	var mcall	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var pos1	= (typeof opts.position == "undefined")  ? "top center" : opts.position;
	var xTitle	= (typeof opts.title == "undefined") ? session("please_wait") : opts.title;
	//deseable obtener el tip
	//var pos1	= "top center";
	var pos2	= "bottom center";
	/*				my: 'botton left',
				at: 'botton center'*/
	$(id).qtip({
		content: {
			text: msg,
				title: {
						text: xTitle,
						button: true
					}
		},
		position: {
			my: pos1, // Use the corner...top
			at: pos2 // ...and opposite corner bottom
		},
		show: {
			//event: false, // Don't specify a show event...
			ready: true, // ... but show the tooltip when ready,
			solo : true,
			show : "focus",
			hide : "blur"
		},
		style: {
			classes: 'ui-tooltip-shadow ui-tooltip-tipped'
		},
		events: {
			render: function(event, api) {
				if (delay == false) {	} else {
					setTimeout(api.hide, delay); // Hide after 1 second
					setTimeout(mcall, delay); // Hide after 1 second
				}

			}
		}
	});
}

Gen.prototype.winTip 	= function (opts){
	opts = (typeof opts == "undefined" ) ? {} : opts;
	var id		= (typeof opts.element == "undefined") ? window : opts.element;
	var msg		= (typeof opts.content == "undefined") ? "" : opts.content;
	msg		= (typeof opts.msg == "undefined") ? msg : opts.msg;

	var delay	= (typeof opts.delay == "undefined") ? 4000 : opts.delay;
	var mTitle	= (typeof opts.title == "undefined") ? "Window" : opts.title;
	$(id).qtip({
		content: {
			text: msg,
			title: {
				text: mTitle,
				button: true
			}
		},
		position: {
			my: "top left", // Use the corner...top
			at: "bottom center" // ...and opposite corner bottom
		},
		show: {
			//event: false, // Don't specify a show event...
			ready: true, // ... but show the tooltip when ready,
			solo : true,
			event : false
			/*show : "focus",
			hide : "blur"*/
		},
		style: {
			classes: 'ui-tooltip-shadow ui-tooltip-tipped'
		},
		events: {
			render: function(event, api) {
				//setTimeout(api.hide, 1000); // Hide after 1 second
			}
		}
	});
}
Gen.prototype.inputMD5	= function(evt){ evt.value	= hex_md5(evt.value); }
Gen.prototype.disTime 	= function (id, mTime){
				mTime	= (typeof mTime == "undefined") ? 5000 : mTime;
				var mpro	= $(id).attr("onclick");
				$(id).attr("onclick", "messageTimer()");
				setTimeout("enableTimer('" + id + "','" +  mpro + "')", mTime);
}
var enableTimer 	= function(id, mtr){ $(id).attr("onclick", mtr); }
var messageTimer	= function(){ alert("Item no activo, espere que termine el proceso..."); }

Gen.prototype.dis 	= function (id){
				session(ID_WORK, id);
				var mpro	= $(id).attr("onclick");
				session(ID_WORK_PROP, mpro);
				$(id).attr("onclick", "messageTimer()");
}
Gen.prototype.ena 	= function (){
				var id		= session(ID_WORK);
				if (id == null) {
								console.log("ERROR ID sin existir");
				} else {
								var prop	= session(ID_WORK_PROP);
								$(id).attr("onclick", prop);
								session(ID_WORK, null);
								session(ID_WORK_PROP, null);
				}
}



function tipSuggest(id, msg){
	$(id).qtip({
		content: {
			text: msg
		},
		position: {
			my: "top center", // Use the corner...top
			at: "bottom center" // ...and opposite corner bottom
		},
		show: {
			//event: false, // Don't specify a show event...
			ready: true, // ... but show the tooltip when ready,
			solo : true,
			show : "focus",
			hide : "blur"
		},
		style: {
			classes: 'ui-tooltip-shadow ui-tooltip-tipped'
		},
		events: {
			render: function(event, api) {
				//setTimeout(api.hide, 1000); // Hide after 1 second
			}
		}
	});
}
function tipList(id, msg, title){
	title	= (typeof title == "undefined") ? "" : title;
	$(id).qtip({
		content: {
			text: msg,
			title: {
				text: title,
				button: true
			}
		},

		position: {
			my: "top left", // Use the corner...top
			at: "bottom center" // ...and opposite corner botton
		},
		show: {
			event: false, // Don't specify a show event...
			ready: true, // ... but show the tooltip when ready,
			solo : true
		},
		hide: false,
		style: {
			classes: 'ui-tooltip-shadow ui-tooltip-tipped'
		},
		events: {
			render: function(event, api) {
				//setTimeout(api.hide, 5500); // Hide after 1 second
			}
		}
	});
}
function tipMsg(id, msg){
	$(id).qtip({
		content: {
			text: msg
		},
		position: {
			my: "center", // Use the corner...top
			at: "center" // ...and opposite corner botton
		},
		show: {
			event: false, // Don't specify a show event...
			ready: true, // ... but show the tooltip when ready,
			solo : true
		},
		style: {
			classes: 'ui-tooltip-shadow ui-tooltip-green'
		},
		events: {
			render: function(event, api) {
				setTimeout(api.hide, 2500); // Hide after 1 second
			}
		}
	});
}
function getModalTip(element, content, title){
		title	= (typeof title == "undefined") ? "" : title;
		$(element).qtip(
		{
				id: 'modal',
				content: { text: content, title: { text: title, button: true }},
				position: {
					my: 'center',
					at: 'center'
				},
				show: {	event: false, solo: true, modal: true, ready : true },
				hide: false, style: 'ui-tooltip-light ui-tooltip-rounded', events: { render: function(event, api) {}}
		});
}

Number.prototype.formatMoney = function(c, d, t){
	var n = this; c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
	return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};
function getInMoney(vF){ var v	= new Number(vF); v	= v.formatMoney(2, DIV_DEC, DIV_MIL);	return v; }
function NativoFloat(vF) {
		if (SEPARADOR_DECIMAL != DIV_DEC) {
			var v	= new Number(vF);
			v		= v.formatMoney(2, DIV_DEC);
		} else {
			var v	= vF;
		}
		return v;
}
function redondear(numer0, decimales){
		decimales	= ( typeof decimales == "undefined" ) ? 2 : decimales;
		var ar		= [1,10,100,1000,10000];
	var original	= flotante(numer0);
	var result		= Math.round(original*ar[decimales])/ar[decimales] ;
	return result;
}
String.prototype.printf = function() {
  var args = arguments;
  return this.replace(/{(\d+)}/g, function(match, number) {
    return typeof args[number] != 'undefined'
      ? args[number]
      : match
    ;
  });
};

String.prototype.printa = function(args) {
  //var args = arguments;
  return this.replace(/{(\d+)}/g, function(match, number) {
    return typeof args[number] != 'undefined'
      ? args[number]
      : match
    ;
  });
};


function titulize(txt){ var tst	= new RegExp(" ", "g"); return String(txt).replace(/[^-A-Za-z0-9]+/g, "_"); }
//thanks to http://www.etnassoft.com/2011/03/03/eliminar-tildes-con-javascript/
var normalize = (function() {
  var from = "ÃÀÁÄÂÈÉËÊÌÍÏÎÒÓÖÔÙÚÜÛãàáäâèéëêìíïîòóöôùúüûÑñÇç",
      to   = "AAAAAEEEEIIIIOOOOUUUUaaaaaeeeeiiiioooouuuunncc",
      mapping = {};

  for(var i = 0, j = from.length; i < j; i++ )
      mapping[ from.charAt( i ) ] = to.charAt( i );

  return function( str ) {
      var ret = [];
      for( var i = 0, j = str.length; i < j; i++ ) {
	  var c = str.charAt( i );
	  if( mapping.hasOwnProperty( str.charAt( i ) ) )
	      ret.push( mapping[ c ] );
	  else
	      ret.push( c );
      }
      return ret.join( '' );
  }

})();

function jsTipo(o){
	var type = typeof o;
    //If typeof return something different than object then returns it.
	if (type !== 'object') {
		return type;
	 //If it is an instance of the Array then return "array"
	} else if (Object.prototype.toString.call(o) === '[object Array]') {
		return 'array';
	 //If it is null then return "null"
	} else if (o === null) {
		return 'null';
       //if it gets here then it is an "object"
	} else {
		return 'object';
	}
}
function flotante(n){
	var nm	= new String(n);
	//1.000,00 1,000.0
	if (DIV_DEC != SEPARADOR_DECIMAL) {
		if (nm.indexOf(".") != -1 && nm.indexOf(DIV_DEC) != -1 ) {
				nm	= nm.replace(".", "");
		}
		nm	= nm.replace(DIV_DEC, SEPARADOR_DECIMAL);
	}
	n		= nm.replace(/[^\d\.\-\ ]/g, '');
	return numero(parseFloat(n));
}
function entero(n){	n	= (n == "") ? 0 : n;	return numero(parseInt(n)); }
function numero(n){	if(typeof n == 'number' && !isNaN(n) && isFinite(n) && n != null){ return n; } else { return 0;	} }

if (typeof jQuery != "undefined") {
	jQuery.fn.reset = function () {  $(this).each (function() { this.reset(); }); }
}

function tip(id, msg, delay, cont, callback){
		delay 			= (typeof delay == "undefined") ? 1000 : delay;
		cont 			= (typeof cont == "undefined") ? "<span><img src=\"../images/loading.gif\" ></span>" : cont;
		cont 			= (cont == null) ? "<span><img src=\"../images/loading.gif\" ></span>" : cont;
		cont 			= (cont == false) ? "<span><img src=\"../images/loading.gif\" ></span>" : cont;
		msg				= (typeof msg == "undefined") ? "Espere por favor!.." : msg;
		var callback 	= (typeof callback == "undefined") ? function(){} : callback;
		if(delay > 1000){
			$(id).qtip({
				content: { text: cont,	title: { text: msg, button: false } },
				position: {	my: "center", at: "center"	},
				show: { event: false, ready: true, solo : true },
				style: { classes: 'ui-tooltip-shadow ui-tooltip-green' },
				events: {
					render: function(event, api) {
						setTimeout(api.hide, delay);
						setTimeout(callback, delay);
					}
				}
			});
		} else {
			$(id).qtip({ content: { text: msg },
				position: { my: "center", at: "center" },
				show: { event: false, ready: true, solo : true },
				style: { classes: 'ui-tooltip-shadow ui-tooltip-green'},
				events: {
					render: function(event, api) {
						setTimeout(api.hide, 2500);
						setTimeout(callback, 2600);
					}
				}
			});
		}
}

var tableToExcel = (function() {
        /*var uri = 'data:application/vnd.ms-excel;base64,'
        , template = "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns=\"http://www.w3.org/TR/REC-html40\"><!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\"><html><head><meta http-equiv=\"Content-type\" content=\"text/html;charset=\"iso-8859-1\" /><style id=\"Classeur1_16681_Styles\"></style></head><body><div id=\"Classeur1_16681\" align=center x:publishsource=\"Excel\"><table x:str border=0   style='border-collapse: collapse'>{table}</table></div></body></html>"
        , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
        , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
        return function(table, name) { if (!table.nodeType) table = document.getElementById(table);
        var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML};
        window.location.href = uri + base64(format(template, ctx));
        }*/
})()

function jsRegresarConTemporizador(opts){
	opts = (typeof opts == "undefined" ) ? {} : opts;
	var url		= (typeof opts.url == "undefined") ? "../index.xul.php" : opts.url;
	var msg		= (typeof opts.msg == "undefined") ? "LO SENTIMOS, HA OCURRIDO UN ERROR DESCONOCIDO!" : opts.msg;
	var delay	= (typeof opts.delay == "undefined") ? 4000 : opts.delay;
	var mfunc	= function(){ top.location	= url; };
	alert(msg);
	top.location	= url;

}
var getObjectByData = function(arg){
		var margs	= String(arg).split("|");
		var itm		= margs.length;
		var obj		= {};
		for(var ix=0; ix <= itm; ix++){
				var idesc	= String(margs[ix]).split("=");
				obj[idesc[0]] = idesc[1];
		}
		return obj;
}
function processMetaData(id){ var str	= $(id).attr("data-info"); return getObjectByData(str); }
function jsGoEstadoDeCuentaDeCredito(credito) {
	var ogen	= new Gen();
	ogen.w({ url: "../rpt_edos_cuenta/rptestadocuentacredito.php?credito=" + credito });
}
function jsGoEstadoDeCuentaDeCreditosPorPersona(persona) {
	var ogen	= new Gen();
	ogen.w({ url: "../rpt_edos_cuenta/rptestadocuentacredito.php?f14=yes&persona=" + persona });
}
function jsGoReciboDeCobranza(socio, credito, parcialidad, oargs){
	oargs		= (typeof oargs == "undefined") ? "" : oargs;
	var ogen	= new Gen();
	ogen.w({ url: "../frmcaja/frmcobrosdecreditos2.php?idsocio=" + socio + "&idsolicitud=" + credito + "&idparcialidad=" + parcialidad  + oargs});
}
function msgbox(msg){ alert(msg); }
function getListaSocios(evt) {
	var gn		= new Gen();
	var ht		= "";
	var myId	= "#" + evt.id;
	if ( String(evt.value).length >= 3 ) {
	gn.pajax({
		url: "../frmsocios/socios.svc.php?nombre=" + $("#idNombres").val() + "&apaterno=" + $("#idApPaterno").val() + "&amaterno=" + $("#idApMaterno").val(),
		finder: "persona",
		callback : function(obj, final){
			ht	+= "<li><a onclick=\"setSocio(" + $(obj).attr("codigo") + ",'" + myId + "')\">" + $(obj).attr("codigo") + "-" + $(obj).text() + "</a></li>";
			//alert($(evt).attr("codigo"));
			if (final == true) {
				tipList(myId, "<ol class=\"rounded-list\">" + ht + "</ol>", "Personas");
			}
		}
	});
	}
}
Gen.prototype.QFrame	= function(opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	var callback	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var id			= (typeof opts.id == "undefined") ? null : opts.id;
	var Func		= (typeof opts.func == "undefined") ? "console.log" : opts.func;
	var vURL		= (typeof opts.url == "undefined") ? "" : opts.url;
	var ixFrame		= $("#" + id);
	$("#" + id).attr("height", (this.alto()-ixFrame.offset().top) );
	ixFrame.attr("src", vURL);
}

Gen.prototype.QList	= function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var callback	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var id		= (typeof opts.id == "undefined") ? null : opts.id;
	var Func	= (typeof opts.func == "undefined") ? "console.log" : opts.func;
	var vURL	= (typeof opts.url == "undefined") ? "" : opts.url;

	var mKey	= (typeof opts.key == "undefined") ? "" : opts.key;
	var Lab		= (typeof opts.label == "undefined") ? "" : opts.label;

	$.cookie.json 	= true;
	var mURL	= SVC_REMOTE_HOST;

	$.getJSON( vURL, function( data ) {
		  var str     = "";
		  $.each( data, function( key, val ) {
		    str += "<li><a onclick=\""  + Func + "(" + val[mKey] + ")\">" + val[Lab] + "</a></li>";
		  });
		  tipList("#" + id, "<ol class=\"rounded-list\">" + str + "</ol>");
		});
}
Gen.prototype.DataList	= function(opts){
	//DroidError("Query " + this.getIn() + oper);
	opts			= (typeof opts == "undefined") ? {} : opts;
	var callback	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var id			= (typeof opts.id == "undefined") ? null : opts.id; //od datalist
	var Func		= (typeof opts.func == "undefined") ? "console.log" : opts.func;
	var vURL		= (typeof opts.url == "undefined") ? "" : opts.url;

	var mKey		= (typeof opts.key == "undefined") ? "" : opts.key;
	var Lab			= (typeof opts.label == "undefined") ? "" : opts.label;
	var PreSave		= (typeof opts.presaved == "undefined") ? "" : opts.presaved; //prefijo en stored key
	$.cookie.json 	= true;
	var mURL		= SVC_REMOTE_HOST;
	
	$.getJSON( vURL, function( data ) {
		  var str     = "";
		  
		  $.each( data, function( key, val ) {
			//$("#" + id).append("<option value='" + val[mKey] + "' label='" + val[Lab] + "' >");
			str += "<option value='" + val[mKey] + "' label='" + val[Lab] + "' >" + val[Lab] + "</option >" ;
			if (PreSave != ""){ session(PreSave + val[mKey], JSON.stringify(val));	}	//guardar en stored
		  });
		  $("#" + id).empty();
		  $("#" + id).append(str);
		});
}
Gen.prototype.closeTip	= function(obj){
	if (typeof obj != "undefined") {
		if (typeof obj.parentNode != "undefined") {
			var xObj	= obj.parentNode;
			$(xObj).empty();
			$(xObj).css("display", "none");
		}
	}	
}
Gen.prototype.close	= function(opts){
	//DroidError("Query " + this.getIn() + oper);
	opts		= (typeof opts == "undefined") ? {} : opts;
	var callback	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var frm		= (typeof opts.form == "undefined") ? "" : opts.form;
	var control	= (typeof opts.control == "undefined") ? null : opts.control;
	var extra	= (typeof opts.extra == "undefined") ? "" : opts.extra;
	var value	= (typeof opts.value == "undefined") ? "" : opts.value;
	var vURL	= (typeof opts.url == "undefined") ? "" : opts.url;
	var src		= null;
	var closeTiny	= false;
	var closeOpen	= false;
	var process	= false;
	if (vURL == "") {
		if (window.parent){
			if (typeof window.parent.TINY != "undefined"){ closeTiny = true; src = window.parent.document; }
		} else {
			this.error({ msg : "No tiene ventana modal..."});
		}
		if (opener){
			closeOpen	= true;
			src		= opener.document;
		} else {
			this.error({ msg : "No es ventana abierta..."});
		}
		if ( frm != "" && control != null) {
			if (value != "") {
				src.frm.control.value	= value;
			}
			src.frm.control.focus();
			src.frm.control.select();
			process	= true;
		}
		if (closeTiny == true){ process	= true;  try { window.parent.TINY.box.hide() } catch(e){ process = false; };	}
		if (closeOpen == true ){ process = true; window.close(); }
	} else {
		//Checar TODO: Inicio Limpio @todo
		top.location=(vURL);
		process = true;
	}
	//try {$(window).qtip("hide");} catch(e){}
	if (process == false) { history.back(); }
}

Gen.prototype.error	= function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var msg	= (typeof opts.msg == "undefined") ? "" : opts.msg;
	console.log(msg);
}
Gen.prototype.lang	= function(words){
	if (typeof words == "string") {
		words = String(words).split(" ");
	}
	var wrd		= "";
	if (typeof jsonWords != "undefined") {
		for(i=0; i<words.length; i++) {
			var mwrd	= String(words[i]).toUpperCase();
			if (typeof jsonWords[mwrd] != "undefined" ) {
				wrd += jsonWords[mwrd] + " ";
			} else {
				wrd += ""+ mwrd + " ";
			}
		}
	}
	return wrd;
}
Gen.prototype.confirmar	= function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var msg		= (typeof opts.msg == "undefined") ? "" : opts.msg;
	var callB	= (typeof opts.callback == "undefined") ? "" : opts.callback;
	var evalF 	= (typeof opts.evaluador == "undefined") ? true : opts.evaluador;
	var msgNV	= (typeof opts.alert == "undefined") ? "" : opts.alert;
	var metaO	= this;
	if (evalF == true) {
		if ($.trim(msg) != "") { msg	= metaO.lang(msg);	}
		if( confirm(msg) == false){ } else { setTimeout(callB, 10);	}		
	} else {
		if ($.trim(msgNV) != "") { msg	= metaO.lang(msgNV); metaO.alerta({ msg : msg });	}
	}
}

Gen.prototype.alerta	= function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var msg		= (typeof opts.msg == "undefined") ? "" : opts.msg;
	var callB	= (typeof opts.callback == "undefined") ? "" : opts.callback;
	var lvl		= (typeof opts.nivel == "undefined") ? "" : opts.nivel;
	var tit		= (typeof opts.title == "undefined") ? "Mensaje del Sistema" : opts.title;
	var metaO	= this;
	if ($.trim(msg) != "") { msg	= metaO.lang(msg);	}
	var mth		= 'awesome error';
	if (lvl == "ok"||lvl == 1||lvl == "success") {
		mth		= 'awesome green';
	}
	$.amaran({
		content:{
			message : msg,
			info : "",
			icon : 'fa fa-warning',
			title : tit
			},
				 theme: mth
			 	
	});
	//theme:'awesome green'
}

Gen.prototype.save	= function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var tbl		= (typeof opts.tabla == "undefined") ? "" : opts.tabla;
	var id		= (typeof opts.id == "undefined") ? "" : opts.id;
	var callB	= (typeof opts.callback == "undefined") ? "" : opts.callback;
	var content	= (typeof opts.content == "undefined") ? "" : opts.content;
	var tt		= this;
	$.cookie.json 	= true;
	var mURL	= "../svc/save.svc.php?tabla=" + tbl + "&id=" +  id + "&" + content;
	//var si		= confirm(this.lang("Confirma Eliminar el Registro"));
	//if (si) {
		$.getJSON( mURL, function( data ) {
			  //var str     = "";
			  if (data.error == true) {
				tt.alerta({msg:data.message});
			  } else {
				tt.alerta({msg:data.message, nivel:"ok"});
				//$("#tr-" + tbl + "-" + id).empty();
			  }
			}
		);		
	//}
}
Gen.prototype.rmRecord	= function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var tbl		= (typeof opts.tabla == "undefined") ? "" : opts.tabla;
	var id		= (typeof opts.id == "undefined") ? "" : opts.id;
	var callB	= (typeof opts.callback == "undefined") ? "" : opts.callback;
	$.cookie.json 	= true;
	var mURL	= "../svc/rm.svc.php?tabla=" + tbl + "&id=" +  id;
	var si		= confirm(this.lang("Confirma Eliminar el Registro"));
	if (si) {
		$.getJSON( mURL, function( data ) {
			  //var str     = "";
			  if (data.error == true) {
				alert(data.message);
			  } else {
				$("#tr-" + tbl + "-" + id).empty();
			  }
			}
		);		
	}
}

Gen.prototype.editar	= function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var tbl		= (typeof opts.tabla == "undefined") ? "" : opts.tabla;
	var id		= (typeof opts.id == "undefined") ? "" : opts.id;
	var callB	= (typeof opts.callback == "undefined") ? "" : opts.callback;
	//$.cookie.json 	= true;
	var mURL	= "../utils/frm8db7028bdcdf054882ab54f644a9d36b.php?tabla=" + tbl + "&clave=" +  id;
	this.w({ url : mURL, tiny : true});
}

CredGen.prototype.getImprimirSolicitud	= function(idcredito){
	var sURL = '../frmcreditos/rptsolicitudcredito1.php?solicitud=' + idcredito;
	var xGen	= new Gen(); xGen.w({ url : sURL, h : 600, w : 800 });
}
CredGen.prototype.getImprimirOrdenDeDesembolso	= function(idcredito){
	var sURL = '../rpt_formatos/rptordendesembolso.php?solicitud=' + idcredito;
	var xGen	= new Gen(); xGen.w({ url : sURL, h : 600, w : 800, tiny: true });
}
CredGen.prototype.getFormaAvales	= function(idcredito){
	//var aURL 	= "../frmcreditos/frmcreditosavales.php?s=" + idcredito;
	var aURL 	= "../frmsocios/registro-personas_fisicas.frm.php?iddocumentorelacionado=" + idcredito + "&relaciones=" + iDE_CREDITO + "";
	var xGen	= new Gen(); xGen.w({ url : aURL, h : 640, w : 800, tiny: true });
}
CredGen.prototype.getVincularAvales	= function(idcredito){
	var aURL 	= "../frmcreditos/vincular.avales.frm.php?credito=" + idcredito;
	var xGen	= new Gen(); xGen.w({ url : aURL, h : 640, w : 800, tiny: true });
}
CredGen.prototype.getFormaFlujoEfectivo	= function(idcredito){
	var xURL 	= "../frmcreditos/frmcreditosflujoefvo.php?solicitud=" + idcredito;
	var xGen	= new Gen(); xGen.w({ url : xURL, h : 600, w : 800, tiny: true });
}
CredGen.prototype.getFormaGarantias	= function(idcredito){
	var gURL = "../frmcreditos/frmcreditosgarantias.php?solicitud=" + idcredito;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 600, w : 800, tiny: true });
}

CredGen.prototype.getFormaAutorizacion	= function(idcredito){
	var gURL = "../frmcreditos/frmcreditosautorizados.php?credito=" + idcredito;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 600, w : 800, tiny: true });
}
CredGen.prototype.getFormaPlanPagos	= function(idCredito){
	var gURL = "../frmcreditos/frmcreditosplandepagos.php?r=1&credito=" + idCredito;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 600, w : 860 });
}

CredGen.prototype.getImprimirPlanPagos	= function(idrecibo, incluirAvales){
	incluirAvales = (typeof incluirAvales == "undefined") ? "no" : "si";
	var gURL = "../rpt_formatos/rptplandepagos.php?idrecibo=" + idrecibo + "&p=" + incluirAvales;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 600, w : 750 });
}
CredGen.prototype.getImprimirMandato	= function(idcredito){
	var gURL = "../rpt_formatos/mandato_en_creditos.rpt.php?credito=" + idcredito;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 600, w : 750 });
}

CredGen.prototype.goToPanelControl	= function(idcredito){
	var gURL = "../frmcreditos/creditos.panel.frm.php?credito=" + idcredito;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 600, w : 800 });
}
CredGen.prototype.getListarCreditos = function(idpersona){
        var thisF = this;
	if (typeof idpersona == "undefined") {
                if ($('#idsocio').length > 0){
			idpersona = $('#idsocio').val();
                } else {
			idpersona = false;
		}
        }
	if ($('#divcredito').length > 0 && idpersona != false) {
	    var srUp = "../svc/creditos.svc.php?persona=" + idpersona;
	    var xG = new  Gen(); $.cookie.json = true; var mURL = SVC_REMOTE_HOST;
	    $.getJSON(srUp, function( data ) {
		var str = "";
		var cnt	= 0;
		$.each( data, function( key, vals ) {
			//$("#nombresolicitud").val( vals["dato"] ); str
			str += "<option value=\"" + key + "\">" +  vals + "</option>";
			//console.log(vals);
			cnt++;
		  });
		str	= "<select id=\"idsolicitud\" name=\"idsolicitud\"> " + str + "</select>";
		if (cnt >= 1) {
				$('#divcredito').empty();
				$('#divcredito').append(str);
		}
		
		});
	}
}
CredGen.prototype.setAgregarBancos = function(idcredito){ var xGen = new Gen(); xGen.w({ tiny : true, h : 600, w : 460, url : "../frmcreditos/creditos.datos-bancarios.frm.php?credito=" + idcredito }); }
CredGen.prototype.setAgregarAML = function(idcredito){ var xGen = new Gen(); xGen.w({ tiny : true, h : 600, w : 460, url : "../frmcreditos/creditos.perfil_aml.frm.php?credito=" + idcredito }); }
CredGen.prototype.getDescripcion	= function(idcredito, dest){
	dest 			= (typeof dest == "undefined") ? "nombresolicitud" : dest;
	var srUp 		= "../svc/credito.svc.php?credito=" + idcredito;
	$.cookie.json 	= true;
	$.getJSON(srUp, function( data ) {
		var str 	= $.trim(decodeEntities(data.descripcion));
		if (str == ""){
			//var siBuscar = confirm("EL CREDITO SOLICITADO NO EXISTE\nO ESTA INACTIVO. DESEA BUSCARLO?");
			//if(siBuscar){ goCredit_(); } else { jsCredRegresarCaptura(); }			
		} else {
			$("#" + dest).val( str );
		}
	});	
}

CredGen.prototype.getPrincipal = function(idpersona){
    var thisF = this;
	if (typeof idpersona == "undefined") {
        if ($('#idsocio').length > 0){
			idpersona = $('#idsocio').val();
        } else {
			idpersona = false;
		}
    }
	if ($('#idsolicitud').length > 0 && idpersona != false) {
	    var srUp = "../svc/creditos.svc.php?persona=" + idpersona;
	    var xG = new  Gen();
	    $.cookie.json = true;
	    var mURL = SVC_REMOTE_HOST;
		//obtener el rol del form para mostrar parametros
		//$('#idsolicitud').closest('form').attr('id');
		if( $('#idsolicitud').closest('form').length > 0 ){
		if (typeof $('#idsolicitud').closest('form').attr('data-role') != "undefined") {
			var FRol		= $('#idsolicitud').closest('form').attr('data-role');
			var oopts		= "";
			switch (FRol) {
				case "ministracion":
					oopts	= "&estado=" + CREDITO_ESTADO_AUTORIZADO;
					break;
				case "autorizacion":
					oopts	= "&estado=" + CREDITO_ESTADO_SOLICITADO;
					break;
			}
			srUp	= srUp + oopts;
		}
		}
	    //setear numeros
	    $('#idsolicitud').val(0);
	    if ($('#nombresolicitud').length > 0){ $("#nombresolicitud").val(''); }
	    $.getJSON(srUp, function( data ) { 
		var str = "";
		var cnt	= 0;
		$.each( data, function( key, vals ) {
			if (cnt == 0) {
				$("#idsolicitud").val(key);
				if ($('#nombresolicitud').length > 0){ $("#nombresolicitud").val(vals);	}
			}
			cnt++;
		});
		});

	}
}
CredGen.prototype.getCompareLetra = function (opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	var monto		= (typeof opts.monto == "undefined") ? 0 : opts.monto;
	var periodo		= (typeof opts.periodo == "undefined") ? 0 : opts.periodo;
	var credito		= (typeof opts.credito == "undefined") ? 0 : opts.credito;
	var url		= "../svc/letras.svc.php?credito=" + credito + "&letra=" + periodo + "&periodo=" + periodo;
	
	if (session(credito +  "." + periodo) == null) {
		xg.pajax({
		url : url, result : "json",
		callback : function(data){
		    try { data = JSON.parse(data); } catch (e){}
		    if (typeof data != "undefined") {
				var valor	= redondear(data.monto,2);
				var vcomp	= redondear(monto,2);
				var mdif	= redondear(valor - monto);
				session(credito +  "." + periodo, valor );
				if(mdif != 0){
					alert("PLAN DE PAGO DIFERENTE AL DESCUENTO!\nMONTO DEL DESCUENTO............ : " + monto + "\nSALDO EN SISTEMA.................... : " + valor + "\nDIFERENCIA................................ : " + mdif + "\nCOBRO:SE AFECTARA EL ULTIMO PERIODO");
				}
		    }
		}
		});
	} else {
		var valor	= redondear(session(credito +  "." + periodo));
		var mdif	= redondear( valor - monto);
		if(mdif != 0){
			alert("PLAN DE PAGO DIFERENTE AL DESCUENTO!\nMONTO DEL DESCUENTO............ : " + monto + "\nSALDO EN SISTEMA.................... : " + valor + "\nDIFERENCIA................................ : " + mdif + "\nCOBRO:SE AFECTARA EL ULTIMO PERIODO");
		}
	}
}
CredGen.prototype.getLetrasEnMora	= function(idcredito){
	var gURL = "../frmcreditos/creditos.letras-pendientes.frm.php?credito=" + idcredito;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 600, w : 750, tiny : true });
}
Gen.prototype.LoadFromCache	= function(opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	var callback	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var idx			= (typeof opts.indice == "undefined") ? null : opts.indice;
	//var campo		= (typeof opts.campo == "undefined") ? null : opts.campo;
	if (idx != null) {
		var mjs		= session(idx);
		if (mjs != null) {
			try {
			mjs		= jQuery.parseJSON( mjs );
			callback(mjs);
			} catch (e){}
		}
	}
}
PersGen.prototype.goToAgregarFisicas	= function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var callback	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var otros		= (typeof opts.otros == "undefined") ? "" : opts.otros;
	
	var sURL = '../frmsocios/registro-personas_fisicas.frm.php?' + otros;
	var xGen	= new Gen(); xGen.w({ url : sURL, h : 600, w : 800, tiny : true , callback : callback });
}
PersGen.prototype.goToAgregarFisicasRelacion	= function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var callback	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var otros		= (typeof opts.otros == "undefined") ? "" : opts.otros;
	this.goToAgregarFisicas({ otros : "domicilio=true&idtipodeingreso="  +  TIPO_INGRESO_RELACION + otros, callback : callback });
}
PersGen.prototype.goToAgregarMorales	= function(oargs){
	if (typeof oargs == "undefined") { oargs = ""; }
	var sURL = '../frmsocios/registro-personas_morales.frm.php?' + oargs;
	var xGen	= new Gen(); xGen.w({ url : sURL, h : 600, w : 800, tiny : true });
}
PersGen.prototype.goToPanel	= function(idpersona, tiny){
	tiny	= (typeof tiny == "undefined") ? false : tiny;
	var sURL = '../frmsocios/socios.panel.frm.php?socio=' + idpersona;
	var xGen	= new Gen(); xGen.w({ url : sURL, h : 600, w : 750, tiny : tiny });
}
PersGen.prototype.getExpediente	= function(idpersona){
	var sURL = '../rpt_edos_cuenta/rpt_estado_cuenta_socio.php?socio=' + idpersona;
	var xGen	= new Gen(); xGen.w({ url : sURL, h : 600, w : 750 });
}
PersGen.prototype.getImprimirSolicitud	= function(idpersona){
	var sURL = '../rpt_formatos/rptsolicitudingreso.php?socio=' + idpersona;
	var xGen	= new Gen(); xGen.w({ url : sURL, h : 600, w : 750 });
}
PersGen.prototype.setAgregarDocumentos	= function(idpersona){
	var sURL ="../frmsocios/personas_documentos.frm.php?persona=" + idpersona;
	var xGen	= new Gen(); xGen.w({ url : sURL, tiny : true });
}
PersGen.prototype.getImagenDocumentos	= function(params){
	var sURL = "../frmsocios/documento.png.php?persona=" + params;;
	var xGen	= new Gen(); xGen.w({ url : sURL, tiny : true });
}
PersGen.prototype.setVerificarRelacionados	= function(idpersona, idrelacionado){
	var URIL	= "../frmsocios/socios.verificacion.frm.php?t=d&s=" + idpersona +"&i=" + idrelacionado;
	var xG	= new Gen(); xG.w({ url: URIL, tiny : true });
}
PersGen.prototype.setVerificarActividadE		= function(idpersona, idactividad){
	var URIL	= "../frmsocios/socios.verificacion.frm.php?t=t&s=" + idpersona +"&i=" + idactividad;
	var xG	= new Gen(); xG.w({ url: URIL, tiny : true });
}
PersGen.prototype.setAgregarVivienda		= function(idpersona){
	var URIL = "../frmsocios/frmsociosvivienda.php?socio=" + idpersona;
	var xG	= new Gen(); xG.w({ url: URIL, tiny : true });
}
PersGen.prototype.setAgregarRelaciones		= function(idpersona){
	//var URIL = "../frmsocios/referencias.directas.frm.php?socio=" + idpersona;
	var mcallback	= (typeof onCloseVentanaRelaciones == "undefined") ? function(){} : onCloseVentanaRelaciones;
	var URIL = "../frmsocios/registro-personas_fisicas.frm.php?nacimiento=false&legal=false&idpersonarelacionado=" + idpersona + "&relaciones=" + iDE_SOCIO;
	var xG	= new Gen(); xG.w({ url: URIL, tiny : true, callback: mcallback });
}
PersGen.prototype.setAgregarActividadE		= function(idpersona){
	var URIL = "../frmsocios/frmsociosaeconomica.php?socio=" + idpersona;
	var xG	= new Gen(); xG.w({ url: URIL, tiny : true });
}
PersGen.prototype.setAgregarPatrimonio		= function(idpersona){
	var URIL = "../frmsocios/frmsociospatrimonio.php?socio=" + idpersona;
	var xG	= new Gen(); xG.w({ url: URIL, tiny : true });
}
PersGen.prototype.getSigmaRelaciones		= function(idxp, id){
	var URIL 	= "../svc/personas.relaciones.arbol.svc.php?persona=" + idxp;
	var xG		= new Gen(); xG.sigma({ url: URIL, id : id });
}
PersGen.prototype.setActualizarDatos		= function(idpersona){
	var srUp = "../frmsocios/frmupdatesocios.php?elsocio=" + idpersona;
	var xG	= new Gen(); xG.w({ url: srUp, tiny : true });
}
PersGen.prototype.setAgregarPerfilTransaccional	= function(idpersona){
	var srUp = "../frmsocios/perfil_transaccional.frm.php?persona=" + idpersona;
	var xG	= new Gen(); xG.w({ url: srUp, tiny : true });
}
PersGen.prototype.getFormaBusqueda	= function(origen){
	session("idpersona.control.dx", origen);
	var xG		= new Gen(); xG.w({ url : "../utils/frmbuscarsocio.php?control="  + origen, tiny : true, h: 600, w : 800});
}
PersGen.prototype.getBuscarCreditos	= function(){
	var idxp	= $.trim(session("idpersona.control.dx"));
	if (idxp != "") {
		var persona	= $("#" + idxp).val();
		var xG		= new Gen(); xG.w({ url : "../utils/frmscreditos_.php?persona="  + persona, tiny : true, h: 600, w : 800});
	}
}
if (typeof goCredit_ == "undefined") {	var goCredit_ = function(){ var xP		= new PersGen(); xP.getBuscarCreditos();} }
/*
	function goCuentas_(tipoc){
		var vTipoC	= \"\";
		if(typeof tipoc == 'undefined'){
			if(jsTypeCaptacion == 0){ } else { vTipoC	= \"&a=\" + jsTypeCaptacion; }
		} else { vTipoC	= \"&a=\" + tipoc; }
		var isoc 	= jsWorkForm.$idsocio.value;
		var urlcap 	= \"../utils/frmcuentas_.php?i=\" + isoc + \"&c=$idcuenta" . "$markSubproducto&f=" . $this->mForm . "\" + vTipoC;
		console.log(urlcap);
		mGlo.w({ url: urlcap, tiny: true});
	}
*/
PersGen.prototype.getDocumento	= function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var id		= (typeof opts.docto == "undefined") ? "" : opts.docto;
	var persona	= (typeof opts.persona == "undefined") ? "" : opts.persona;
	var xG		= new Gen(); xG.w({ url : "../frmsocios/socios.docto.frm.php?persona="  + persona + "&docto=" + id, tiny : true, h: 600, w : 600});
}
PersGen.prototype.getNombre		= function(idpersona, dest){
		//var		idpersona		= $(evt.d)
		var srUp = "../svc/personas.svc.php?persona=" + idpersona;
		dest	= (typeof dest == "undefined") ? "nombresocio" : dest;
		var xG	= new Gen();
	$.cookie.json 	= true;
		var mURL	= SVC_REMOTE_HOST;

	$.getJSON( srUp, function( data ) {
		  var str     = "";
		  $.each( data, function( key, val ) {
				$("#" +  dest).val(  decodeEntities(val["nombrecompleto"]) );
		  });
		});
	var xCred	= new CredGen();
	xCred.getPrincipal();
	var xCta	= new CaptGen(); xCta.getPrincipal({ persona : idpersona});
}
CaptGen.prototype.getImprimirMandato	= function(idcredito){
	var gURL = "../rpt_formatos/mandato_en_depositos.rpt.php?cuenta=" + idcredito;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 600, w : 750 });
}
CaptGen.prototype.getImprimirContrato	= function(murl){
	var gURL = murl;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 600, w : 750 });
}
CaptGen.prototype.getVerFirmas	= function(idcredito){
	var gURL = "../rpt_formatos/mandato_en_depositos.rpt.php?cuenta=" + idcredito;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 600, w : 750 , tiny : true});
}
CaptGen.prototype.setActualizarDatos	= function(idcredito){
	var gURL = "../utils/frm8db7028bdcdf054882ab54f644a9d36b.php?t=captacion_cuentas&f=numero_cuenta=" + idcredito;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 600, w : 800, tiny : true });
}

CaptGen.prototype.getEstadoDeCuentaVista	= function(idcuenta){
	var gURL = "../rpt_edos_cuenta/rpt_estado_cta_ahorro.php?cuenta=" + idcuenta;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 600, w : 800, tiny : false });
}
CaptGen.prototype.getEstadoDeCuentaSDPM	= function(idcuenta){
	var gURL = "../rptcaptacion/rpt_estado_cta_sdpm.php?cuenta=" + idcuenta;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 600, w : 800, tiny : true });
}
CaptGen.prototype.getDescripcion	= function(idcuenta, dest){
	est 	= (typeof dest == "undefined") ? "nombrecuenta" : dest;
	if (entero(idcuenta)<=0) {
		
	} else {
		var srUp = "../svc/cuenta.svc.php?cuenta=" + idcuenta;
		var xG	= new Gen();
		$.cookie.json 	= true;
		var mURL	= SVC_REMOTE_HOST;
		$.getJSON( srUp, function( data ) {
			  var str     = "";
			$("#" + dest).val( decodeEntities(data.descripcion) );
		});
	}
}
CaptGen.prototype.getPrincipal	= function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var control	= (typeof opts.control == "undefined") ? "idcuenta" : opts.control;
	var persona	= (typeof opts.persona == "undefined") ? 0 : opts.persona;
	var tipo	= (typeof opts.tipo == "undefined") ? 0 : opts.tipo;
	var subtipo	= (typeof opts.subtipo == "undefined") ? 0 : opts.subtipo;
	if (entero(persona) <= 0) {
		if ($('#idsocio').length > 0){ persona = $("#idsocio").val();	}
	}
	//if($('#' + control).length > 0 ){
	if( $('#' + control).closest('form').length > 0 ){
		//alert($('#' + control).closest('form').attr('data-role'));
		if (typeof $('#' + control).closest('form').attr('data-role') != "undefined" ) {
			var FRol		= $('#' + control).closest('form').attr('data-role');
			switch (FRol) {
				case "inversion":
					tipo		= (tipo == 0) ? CAPTACION_TIPO_PLAZO : tipo;
					break;
			}
		}
	}
	//}
	//(entero(tipo)+entero(subtipo)) <= 0|| 
	if (entero(persona) <= 0) {
		console.log("No hay valores de busqueda " + persona);
	} else {
		var srUp 		= "../svc/cuentas.svc.php?tipo=" + tipo + "&subtipo=" + subtipo + "&persona=" + persona;
		var xG			= new Gen();
		$.cookie.json 	= true;
		var mURL		= SVC_REMOTE_HOST;
		
		
		$.getJSON( srUp, function( data ){
			var str     = "";
			$.each( data, function( key, val ){
				$("#" + control).val( decodeEntities(val["cuenta"]) );
			});
		});		
	}
}
RecGen.prototype.panel			= function(clave){
	var xGen	= new Gen(); xGen.w({ url: "../frmoperaciones/recibos.panel.frm.php?cNumeroRecibo=" + clave, h:600, w : 800, tiny : true});
}
RecGen.prototype.reporte			= function(clave){
	var xGen	= new Gen(); xGen.w({ url: "../rptoperaciones/rpt_consulta_recibos_individual.php?recibo=" + clave, h:600, w : 800});
}
RecGen.prototype.factura			= function(clave){
	var xGen	= new Gen(); xGen.w({ url: "../rpt_formatos/factura.xml.php?recibo=" + clave, h:600, w : 800});
}

RecGen.prototype.getExisteFactura	= function(opts){
	opts = (typeof opts == "undefined" ) ? {} : opts;
	var recibo		= (typeof opts.recibo == "undefined") ? 0 : opts.recibo;
	var open		= (typeof opts.open == "undefined") ? false : opts.open;
	
	var xUrl		= "../svc/factura_por_recibo.svc.php?action=LIST&lim=1&recibo=" + cuenta;
	$.getJSON( xUrl, function( data ) {
		//$("#" + control).val( decodeEntities(data.nombre_de_cuenta) );
	});
	
	//var xGen	= new Gen(); xGen.w({ url: "../rpt_formatos/factura.xml.php?recibo=" + clave, h:600, w : 800});
}
RecGen.prototype.getExistePolizaContable	= function(opts){
	opts = (typeof opts == "undefined" ) ? {} : opts;
	var recibo		= (typeof opts.recibo == "undefined") ? 0 : opts.recibo;
	var open		= (typeof opts.open == "undefined") ? false : opts.open;
	var jscall		= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var xRec		= this;
	var xUrl		= "../svc/poliza_por_recibo.svc.php?action=LIST&lim=1&recibo=" + recibo;
	$.getJSON( xUrl, function( data ) {
		if (typeof data.codigo == "undefined") {
			//alert("No existe la Poliza por el recibo");
			setTimeout(jscall, 10);
		} else {
			if (open == true) {
				var xCont	= new ContGen();
				xCont.goToPoliza(data.codigo);
			}
		}
	});
	
	//
}
Gen.prototype.salir		= function (opts){ top.location=("../salir.php"); }
Gen.prototype.tipModal	= function (opts){

	opts = (typeof opts == "undefined" ) ? {} : opts;
	var id		= (typeof opts.element == "undefined") ? window : opts.element;
	var msg		= (typeof opts.msg == "undefined") ? "<span><img src=\"../images/loading.gif\" ></span>" : opts.msg;
	//var msg		= (typeof opts.msg == "undefined") ? null : opts.msg;
	var delay	= (typeof opts.delay == "undefined") ? 4000 : opts.delay;
	var mcall	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var onrnd	= (typeof opts.onRender == "undefined") ? function(){} : opts.onRender;

	var oTitle	= (typeof opts.title =="undefined") ? { } : { text: opts.title, button: true	};

	$(id).qtip({
	id: 'modal',
	content: { text: msg, title : oTitle },
	position: {
		my: 'center',
		at: 'center',
		target: $(window)
	},
	show: {
		event: false, solo: true, modal: true,	ready : true
	},
	hide: false,
	style: 'ui-tooltip-light ui-tooltip-rounded',
	events: {
			render: function(event, api){
				//setTimeout(api.hide, delay);
				setTimeout(mcall, delay);
				setTimeout(onrnd, 0);
			}
	}
	});
}

Gen.prototype.getTCampos = function (obj, osrc){
	var idKey		= "N";
	var nGen		=  new Gen();
	var xUrl		= "../svc/tabla.svc.php?tabla=" + $("#" + osrc).val();
	//console.log(xUrl);
	nGen.DataList({
		url : xUrl,
		id : obj.id,
		key : idKey,
		label : "N"
	});
}
Gen.prototype.getTValores = function (obj, evt, osrc){
	var nGen		=  new Gen();
	var xUrl		= "../svc/tabla.svc.php?action=list&tabla=" + $("#" + osrc).val() + "&clave=" + obj.value;
	var dlSRC		= "dl" + obj.id;
	
	if (nGen.isNumberKey({evt:evt}) == true || nGen.isTextKey({evt:evt}) == true ) {
		if ( String(obj.value).length >= 3 ) {
			//console.log(xUrl);
			nGen.DataList({
				url : xUrl,
				id : dlSRC,
				key : "indice",
				label : "etiqueta"
			});
		}
	}
}

PersGen.prototype.buscar	= function(obj, evt) {
	
	evt				= (evt) ? evt:event;
	var charCode 	= (evt.charCode) ? evt.charCode : ((evt.which) ? evt.which : evt.keyCode);
	var idKey		= "codigo";
	var dlSRC		= "dl" + obj.id;
	var nGen		= new Gen();
	var str			= new String(obj.value);
	var DP			= str.split(" ");
	var nm			= (typeof DP[2] != "undefined") ? $.trim(DP[2]) : "";
	var ap			= (typeof DP[0] != "undefined") ? $.trim(DP[0]) : "";
	var am			= (typeof DP[1] != "undefined") ? $.trim(DP[1]) : "";
	var xUrl		= "../svc/personas.svc.php?nombre=" + nm + "&apaterno=" + ap + "&amaterno=" + am + "";
	if ((charCode >= 65 && charCode <= 90)) {
		if ( String(obj.value).length >= 3 ) {
			$("#" + dlSRC).empty();
			nGen.DataList({
				url : xUrl,
				id : dlSRC,
				key : idKey,
				label : "nombrecompleto"
				});	
		}
	}
}


PersGen.prototype.addRelacion	= function(opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	var persona		= (typeof opts.persona == "undefined") ? 0 : opts.persona;
	var relacionado	= (typeof opts.relacionado == "undefined") ? 0 : opts.relacionado;
	var tipo		= (typeof opts.tipo == "undefined") ? 0 : opts.tipo;
	var depende		= (typeof opts.depende == "undefined") ? 0 : opts.depende;
	var parentesco	= (typeof opts.parentesco == "undefined") ? 0 : opts.parentesco;
	var docto		= (typeof opts.documento == "undefined") ? 0 : opts.documento;
	var callback	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var url			= "../svc/referencias.add.svc.php?persona=" + persona + "&relacionado=" + relacionado + "&tipo=" + tipo + "&parentesco=" + parentesco + "&depende=" + depende + "&documento=" + docto;
	var xg			= new Gen();	
	xg.pajax({
		url : url, result : "json",
		callback : function(data){
		    try { data = JSON.parse(data); } catch (e){}
		    if (typeof data != "undefined") {
				//var valor	= redondear(data.monto,2);
				if (data.error == true) {
					xg.alerta({ msg : data.msg, nivel : "error" });
				} else {
					xg.alerta({ msg : data.msg, nivel : 1 });
					callback(data);
				}
		    }
		}
	});
}

PersGen.prototype.setNombre	= function(idpersona, nombre, iddestino){}

TesGen.prototype.goCerrarCaja	= function(){
	var gn		= new Gen();
	gn.w({ url : "../frmcaja/cerrar_caja.frm.php" });
}

EmpGen.prototype.getCedulaAhorro = function(id){ var gn	= new Gen(); gn.w({ url : "../rptempresas/incidencias_de_captacion.rpt.php?empresa=" + id }); }
EmpGen.prototype.getOrdenDeCobranza = function(idnom){ var xg = new Gen(); xg.w({ url : "../rptcreditos/orden_de_cobranza.rpt.php?nomina=" + idnom, w : 800, h : 600 }); }
EmpGen.prototype.getEstadoDeCuenta = function(id){ var xg = new Gen(); xg.w({ url : "../rptempresas/empresas.movimientos.rpt.php?empresa=" + id, w : 800, h : 600 }); }
EmpGen.prototype.setActualizarDatos = function(id){ var gn	= new Gen(); gn.w({ url : "../frmsocios/agregar-empresas.frm.php?empresa=" + id, tiny : true }); }

function setLog(msg){ console.log(msg); }

ContGen.prototype.ImprimirPoliza	= function(clave){
	var mRPT		= "../rptcontables/rpt_auxiliar_de_polizas.php?codigo=" + clave;
	var xGen		= new Gen();
	xGen.w({ url : mRPT });
}

ContGen.prototype.goToPanel	= function(clave){
	var mRPT		= "../frmcontabilidad/cuenta.panel.frm.php?cuenta=" + clave;
	var xGen		= new Gen();
	xGen.w({ url : mRPT, tiny : true });
}

var decodeEntities = (function() {
  // this prevents any overhead from creating the object each time
  var element = document.createElement('div');

  function decodeHTMLEntities (str) {
    if(str && typeof str === 'string') {
      // strip script/html tags
      str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '');
      str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '');
      element.innerHTML = str;
      str = element.textContent;
      element.textContent = '';
    }
    return str;
  }

  return decodeHTMLEntities;
})();

AmlGen.prototype.goToCambiarNivel	= function(idpersona){
	var gn		= new Gen();
	gn.w({ url : "../frmpld/registro_persona_riesgosa.frm.php?persona=" + idpersona, tiny : true, h: 600, w : 800 });
}
AmlGen.prototype.getReporteDeTransacciones	= function(idpersona){ var gn= new Gen(); gn.w({ url : "../rptpld/transacciones_por_persona.rpt.php?persona=" + idpersona, tiny : false, h: 600, w : 800 }); }
AmlGen.prototype.getReporteDeTransaccionesPorNucleo	= function(idpersona){ var gn= new Gen(); gn.w({ url : "../rptpld/transacciones_por_nucleo.rpt.php?persona=" + idpersona, tiny : false, h: 600, w : 800 }); }
AmlGen.prototype.getReporteDePerfilTransaccional	= function(idpersona){ var gn= new Gen(); gn.w({ url : "../rptpld/perfil_transaccional.rpt.php?persona=" + idpersona, tiny : false, h: 600, w : 800 }); }
AmlGen.prototype.getReporteDeAlertas	= function(idpersona){ var gn= new Gen(); gn.w({ url : "../rptpld/personas.alertas.rpt.php?persona=" + idpersona, tiny : false, h: 600, w : 800 }); }
AmlGen.prototype.getConsultaListaNegra	= function(idpersona){ var gn= new Gen(); gn.w({ url : "../frmsocios/personas.consulta-lista-negra.frm.php?persona=" + idpersona, tiny : true, h: 600, w : 800 }); }

AmlGen.prototype.addCuestionario		= function(idcredito){ var gn= new Gen(); gn.w({ url : "../frmcreditos/creditos.perfil-aml.frm.php?credito=" + idcredito, tiny : true, h: 600, w : 800 }); }
GroupGen.prototype.getDescripcion	= function(idgrupo, dest){
	dest 			= (typeof dest == "undefined") ? "nombregrupo" : dest;
	var srUp 		= "../svc/grupo.svc.php?grupo=" + idgrupo;
	$.cookie.json 	= true;
	var mURL		= SVC_REMOTE_HOST;
	$.getJSON( srUp, function( data ) {
		$("#" + dest).val( decodeEntities(data.descripcion) );
	});	
}

function serializeForm(idfrm) {
	var fields = $(idfrm).serializeArray();
	var txt		= "";
	var jsq		= "";
	var total = fields.length;
	
	jQuery.each( fields, function( i, field ) {
		str		= '$' + field.name  + '\t= parametro(\"' + field.name +  '\"';
		//console.log(field)
		str		+= ');\n';
		txt		+= str;
		jsq		+= 'var ' + field.name  + '\t= $("#' + field.name  + '").val();\n';
		if (i === (total - 1) ) {
			session("var.serialize", txt + "\n\n\n" + jsq);
			var xG		= new Gen();
			xG.w({ url : "../tools/serialize.dev.php?", tiny : true, h: 600, w : 800 });
		}
	});
}

DomGen.prototype.getColoniasNombreXA = function (obj, evt){
	evt				= (evt) ? evt:event;
	var charCode 	= (evt.charCode) ? evt.charCode : ((evt.which) ? evt.which : evt.keyCode);
	var idKey		= "colonia";
	var dlSRC		= "dl" + obj.id;
	var nGen		=  new Gen();
	var ByEstado	= "";
	var ByCP		= "";
	if ($("#identidadfederativa").length > 0) {
		ByEstado	= "&e=" + $("#identidadfederativa").val() ;
	}
	if ($("#idcodigopostal").length > 0) {
		if (flotante($("#idcodigopostal").val()) > 0) {
			ByCP	= "&cp=" + $("#idcodigopostal").val() ;
		}
	}	
	//Politica dl
	var xUrl	= "../svc/colonias.svc.php?action=LIST&lim=25&n=" + obj.value + ByEstado + ByCP;
	if ((charCode >= 65 && charCode <= 90)) {
		if ( String(obj.value).length >= 3 ) {
			$("#" + dlSRC).empty();
			nGen.DataList({
				url : xUrl,
				id : dlSRC,
				key : idKey,
				label : "nombre"
				});	
		}
	}
}
DomGen.prototype.setColoniasXCP = function (obj){
		var mAsignar	= function (objs){
			//{"codigo":"380006","clavepostal":"24026","nombre":"Colonia Granjas(Campeche, Campeche)","estado":"4","municipio":"2","colonia":"Granjas","nombre_del_municipio":"Campeche","nombre_del_estado":"CAMPECHE"
			if ($("#idcp_" + obj.id).length >0) { $("#idcp_" + obj.id).val(objs.codigo); }
			//if ($("#iddescripcion" + obj.id).length >0) { $("#iddescripcion" + obj.id).val( decodeEntities( objs.nombre) ); }
			if ($("#identidadfederativa").length >0) { $("#identidadfederativa").val(objs.estado); }
			if ($("#idmunicipio").length >0) { $("#idmunicipio").val(objs.municipio); }
			if ($("#idnombrecolonia").length >0) { $("#idnombrecolonia").val(objs.colonia); }
			if ($("#idnombremunicipio").length >0) { $("#idnombremunicipio").val(objs.nombre_del_municipio); }
			//
			//idlocalidad
			//
			//
			//idnombrelocalidad
			//
			
		};
		nGen.LoadFromCache({
			callback : mAsignar,
			indice : "cp-" + obj.value
			});	
}
DomGen.prototype.getColoniasXCP = function (obj){

	var idKey		= "codigo";
	var dlSRC		= "dl" + obj.id;
	var nGen		=  new Gen();
	var ByEstado	= "";
	//if ($("#identidadfederativa").length > 0) { ByEstado	= "&e=" + $("#identidadfederativa").val() ;	}
	var xUrl	= "../svc/colonias.svc.php?action=LIST&lim=25&cp=" + obj.value + ByEstado;
	
	if ( String(obj.value).length >= 3 ) {
		$("#" + dlSRC).empty();
		
		nGen.DataList({
			url : xUrl,
			id : dlSRC,
			key : idKey,
			label : "nombre",
			presaved : "cp-"
			});
			//console.log($("#" + dlSRC).html());
	}
}

DomGen.prototype.getMunicipioNombreXA = function (obj, evt){
	evt				= (evt) ? evt:event;
	var charCode 	= (evt.charCode) ? evt.charCode : ((evt.which) ? evt.which : evt.keyCode);
	var idKey		= "nombre_del_municipio";
	var dlSRC		= "dl" + obj.id;
	var nGen		=  new Gen();
	var ByEstado	= "";
	if ($("#identidadfederativa").length > 0) {
		ByEstado	= "&e=" + $("#identidadfederativa").val() ;
	}
	//Politica dl
	var xUrl	= "../svc/municipios.svc.php?action=LIST&lim=25&n=" + obj.value + ByEstado;
	//console.log(xUrl);
	if ((charCode >= 65 && charCode <= 90)) {
		if ( String(obj.value).length >= 3 ) {
			$("#" + dlSRC).empty();
			nGen.DataList({
				url : xUrl,
				id : dlSRC,
				key : idKey,
				label : "nombre_del_municipio"
				});	
		}
	}
}

DomGen.prototype.getLocalidadNombreXA = function (obj, evt){
	evt				= (evt) ? evt:event;
	var charCode 	= (evt.charCode) ? evt.charCode : ((evt.which) ? evt.which : evt.keyCode);
	var idKey		= "nombre_de_la_localidad";
	var dlSRC		= "dl" + obj.id;
	var nGen		=  new Gen();
	var ByEstado	= "";
	if ($("#identidadfederativa").length > 0) {
		ByEstado	= "&e=" + $("#identidadfederativa").val() ;
	}
	//Politica dl
	var xUrl	= "../svc/localidades.svc.php?action=LIST&lim=25&n=" + obj.value + ByEstado;
	//console.log(xUrl);
	if ((charCode >= 65 && charCode <= 90)) {
		if ( String(obj.value).length >= 3 ) {
			$("#" + dlSRC).empty();
			nGen.DataList({
				url : xUrl,
				id : dlSRC,
				key : idKey,
				label : "nombre_de_la_localidad"
				});	
		}
	}
}
DomGen.prototype.setAccionPorPais = function (osrc){
	//if ($("#iddescripcion" + obj.id).length >0) {
	var mpais	= osrc.value;
	if(mpais != EACP_CLAVE_DE_PAIS){
		if ($("#identidadfederativa").length >0) {
			$("#identidadfederativa").val(98);
			$("#identidadfederativa").css("display", "none");			
		}
		if (typeof jsaGetMunicipios != "undefined") { jsaGetMunicipios();	}
		if (typeof jsaGetLocalidades != "undefined") { jsaGetLocalidades();	}
	} else {
		if ($("#identidadfederativa").length >0) {
			$("#identidadfederativa").val(LOCAL_DOMICILIO_CLAVE_ENTIDAD);
			$("#identidadfederativa").css("display", "inherit");		
		}		
		if (typeof jsaGetMunicipios != "undefined") { jsaGetMunicipios();	}
		if (typeof jsaGetLocalidades != "undefined") { jsaGetLocalidades();	}
	}	
}
DomGen.prototype.setAccionPorEstado = function (osrc){
	//if ($("#iddescripcion" + obj.id).length >0) {
	var mpais	= osrc.value;
	if (typeof jsaGetMunicipios != "undefined") { jsaGetMunicipios();	}
	if (typeof jsaGetLocalidades != "undefined") { jsaGetLocalidades();	}
}
ValidGen.prototype.NoCero	= function(v){ return ( flotante(v) <= 0) ? false : true; }
ValidGen.prototype.NoVacio	= function(v){ return ( $.trim(v) == "") ? false : true; }
ValidGen.prototype.EnArray	= function(v, arr){
	var siEsta	= false;
	for (var i in arr) {
		if (v == arr[i]) { siEsta = true; }
	}
	return siEsta;
}
ContGen.prototype.getCuentasPorCodigo = function (obj, evt){
	evt				= (evt) ? evt:event;
	var charCode 	= (evt.charCode) ? evt.charCode : ((evt.which) ? evt.which : evt.keyCode);
	var idKey		= "numero_de_cuenta";
	var dlSRC		= "dl" + obj.id;
	var nGen		=  new Gen();

	//Politica dl
	var xUrl	= "../svc/catalogo.svc.php?action=LIST&lim=5&cuenta=" + obj.value;
	var xG		= new Gen();
	if (xG.isNumberKey({evt:evt}) == true || xG.isTextKey({evt:evt}) == true ) {
		if ( String(obj.value).length >= 2 ) {
			$("#" + dlSRC).empty();
			nGen.DataList({
				url : xUrl,
				id : dlSRC,
				key : idKey,
				label : "nombre_de_cuenta"
				});	
		}		
	}
}

ContGen.prototype.getNombreDeCuenta = function (opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	var control		= (typeof opts.control == "undefined") ? "idcuenta" : opts.control;
	var cuenta		= (typeof opts.cuenta == "undefined") ? 0 : opts.cuenta;
	
	var xUrl		= "../svc/cuenta.contable.svc.php?action=LIST&lim=1&cuenta=" + cuenta;
	$.getJSON( xUrl, function( data ) {
		$("#" + control).val( decodeEntities(data.nombre_de_cuenta) );
	});	
}

ContGen.prototype.setEditarMovimiento = function (opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	var control		= (typeof opts.control == "undefined") ? "idcuenta" : opts.control; //idunica
	var clave		= (typeof opts.clave == "undefined") ? 0 : opts.clave;
	var mcall		= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var xG			= new Gen();
	var xUrl		= "../frmcontabilidad/modificar.movimiento.frm.php?action=LIST&lim=1&id=" + clave;
	xG.w({ url : xUrl, tiny : true, w : 480, callback : mcall});
}
ContGen.prototype.goToPoliza	= function(id){ var xG	= new Gen(); xG.w({url : "../frmcontabilidad/poliza_movimientos.frm.php?codigo=" +id, h : 600, W : 800, tiny : true }); }
function jsCloseWithTimer(timer) {
	var xG		= new Gen();
	var xFun 	= function(){ xG.close(); }
	setTimeout(xFun, timer);
}
PersAEGen.prototype.getListaDeActividades	 = function (obj, evt){
	evt				= (evt) ? evt:event;
	var charCode 	= (evt.charCode) ? evt.charCode : ((evt.which) ? evt.which : evt.keyCode);
	var idKey		= "clave_de_actividad";
	var dlSRC		= "dl" + obj.id;
	var nGen		=  new Gen();
	var xUrl	= "../svc/personas.actividades.economicas.php?action=LIST&lim=5&arg=" + obj.value;
	//var xUrl	= "../svc/personas.actividades.economicas.php?action=LIST&lim=5&arg=" + msrc.value;
	if (nGen.isNumberKey({evt:evt}) == true || nGen.isTextKey({evt:evt}) == true ){
		
		if ( String(obj.value).length >= 3 ) {
			$("#" + dlSRC).empty();
			
			nGen.DataList({
				url : xUrl,
				id : dlSRC,
				key : idKey,
				label : "nombre_de_la_actividad",
				presaved : "ae-"
				});	
		}
	} else {
		this.setActividadPorCodigo(obj);
	}
}
PersAEGen.prototype.setActividadPorCodigo	 = function (obj){
	var mAsignar	= function (objs){
		if ($("#iddescripcion" + obj.id).length >0) {
				//{"codigo":"380006","clavepostal":"24026","nombre":"Colonia Granjas(Campeche, Campeche)","estado":"4","municipio":"2","colonia":"Granjas"}
				//{"clave_interna":"99","clave_de_actividad":"9999999","nombre_de_la_actividad":"DESCONOCIDO_MIGRADO","descripcion_detallada":"","productos":"","clasificacion":"CLASE","clave_de_superior":"0"}}
				//if ($("#iddescripcion" + obj.id).length >0) {
			$("#iddescripcion" + obj.id).val(objs.nombre_de_la_actividad);
		}
	}
	nGen.LoadFromCache({
		callback : mAsignar,
		indice : "ae-" + obj.value
	});

}
