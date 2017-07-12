DIV_DEC 		= ".";
DIV_MIL			= ",";
SEPARADOR_DECIMAL	= ".";
var IMG_LOADING		= "<span><img src=\"../images/loading.gif\" ></span>";
var SCREENW			= window.innerWidth;
var SCREENH			= window.innerHeight;
var ID_WORK			= "id.work.current";
var ID_WORK_PROP	= "id.work.current.property";
var ID_PERSONA		= "id.persona.current";
var ID_CP_ACTUAL	= "id.cp.current";
var SYS_ERROR		= "error";
var SYS_MSG			= "msg";
var SYS_NUMERO		= "numero";
var TINYAJAX_CALLB	= "tinyajax.callback";

var UPDWIN		= null;
var Gen			= function(){};
var CredGen		= function(){};
var PersGen		= function(){};
var PersAEGen	= function(){};
var PersVivGen	= function(){};
var CaptGen		= function(){};
var RecGen		= function(){};
var TesGen		= function(){};
var EmpGen		= function(){};
var ContGen		= function(){};
var GroupGen	= function(){};
var AmlGen		= function(){};
var DomGen		= function(){};
var BanGen		= function(){};
var PlanGen		= function(){};
var ValidGen	= function(){};
var SegGen		= function(){};
var FechaGen	= function(){};

//---------------------------- Funciones generales
/*function parseFechaMX(str) {
	// this example parses dates like "month/date/year"
	var parts = str.split('-');
	if (parts.length == 3) {
		return new XDate(
			parseInt(parts[2]), // year
			parseInt(parts[1] ? parts[1]-1 : 0), // month
			parseInt(parts[0]) // date
		);
	}
}
if(typeof XDate != "undefined"){
	XDate.parsers.push(parseFechaMX);
}*/

function setLog(msg){ if(MODO_DEBUG == true){ console.log(msg); } }
	
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
	if(mNum == true){
		if ((charCode >= 48 && charCode <= 57)||(charCode >= 96 && charCode <= 105)){	setTimeout(callbackF, 0); isKeyCode		= true;	}
	} else {
		if (charCode >= 65 && charCode <= 90){	setTimeout(callbackF, 0); isKeyCode		= true;	}
	}
	return isKeyCode;
}
Gen.prototype.winOrigen		= function(){
	dsrc	= null;
	if (window.parent){ dsrc = window.parent.document; }
	if (opener){ dsrc = opener.document; }
	return dsrc;
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
Gen.prototype.spin	= function(opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	var callback	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var tim3		= (typeof opts.time == "undefined") ? 3000 : opts.time;
	var spinner 	= $(document.body).spin("modal");
	var m3Fun		= function(){
						if($("#spin_modal_overlay").length >0){
								spinner.stop(); $(document.body).spin("modal").stop();
						}
						callback();
						}
	setTimeout(m3Fun, tim3);
	return true;
}
Gen.prototype.spinInit	= function(){ $(document.body).spin("modal"); }
Gen.prototype.spinEnd	= function(){
		if($("#spin_modal_overlay").length >0){
				$(document.body).spin("modal").stop();
		}
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
Gen.prototype.desactiva		= function(id){
	$(id).css('pointer-events', 'none');
	$(id).css('color', '#808080');
	$(id).attr('disabled', 'disabled');
}
Gen.prototype.activa		= function(id){
	$(id).css('pointer-events', 'all');
	$(id).css('color', '#fffed6');
	$(id).removeAttr('disabled');
}
Gen.prototype.activarForma	= function(activar,conButtons){
	activar			= (typeof activar == "undefined") ? false : activar;
	conButtons		= (typeof conButtons == "undefined") ? true : conButtons;
	
	if(activar == false){
		$('input, select').attr('disabled', 'disabled');
		if(conButtons == true){
			$('a').css('pointer-events', 'none');
		}
	} else {
		$('input, select').removeAttr('disabled');
		if(conButtons == true){
			$('a').css('pointer-events', 'all');
		}
	}
}
Gen.prototype.soloLeerForma	= function(activar,conButtons){
	activar			= (typeof activar == "undefined") ? false : activar;
	conButtons		= (typeof conButtons == "undefined") ? true : conButtons;
	
	$('input, select').removeAttr('disabled');
	if(activar == false){
		$('input, select').attr('readonly', 'readonly');
		if(conButtons == true){
			$('a').css('pointer-events', 'none');
		}
	} else {
		$('input, select').removeAttr('readonly');
		
		if(conButtons == true){
			$('a').css('pointer-events', 'all');
		}
	}
}
Gen.prototype.verControl	= function(id, ver){
	ver			= (typeof ver == "undefined") ? false : ver;
	if(ver == false){
		$("#" + id).parent().css("display", "none");
	} else {
		$("#" + id).parent().css("display", "inline-block");
	}
}
Gen.prototype.verDiv	= function(id, ver){
	ver			= (typeof ver == "undefined") ? false : ver;
	if(ver == false){
		$("#" + id).css("display", "none");
	} else {
		$("#" + id).css("display", "inline-block");
	}
}
Gen.prototype.aMonedaForm = function(){
	var aMny 		= $( ":input[class=mny]" );
	$.each( aMny, function( key, val ) {
		var idxo	= val.id;
		if(String(idxo).indexOf("_mny") !== -1){
			var idxd= String(idxo).replace("_mny", "");
			idxo	= "#" + idxo;
			idxd	= "#" + idxd;
			var vv	=  flotante($(idxo).val() );
			$(idxd).val( vv );
			$(idxo).val( getFMoney(vv));
		}
	});	
}
Gen.prototype.aMoneda	= function(opts){
	var charCode 	= 0;
	opts			= (typeof opts == "undefined") ? {} : opts;
	var id0			= (typeof opts.idDesde == "undefined") ? "" : "#" + opts.idDesde;
	var id1			= (typeof opts.idPara == "undefined") ? "" : "#" + opts.idPara;
	
	if (typeof opts.evt != "undefined" ) {
		charCode = ( opts.evt.charCode) ? opts.evt.charCode : ((opts.evt.which) ? opts.evt.which : opts.evt.keyCode);
	}
	$(id0).blur(function(){
		var vv		=  flotante($(id0).val() );
		$(id1).val( vv );
		$(id0).val( getFMoney(vv));
	});
	if ( (charCode >= 48 && charCode <= 57)||(charCode >= 96 && charCode <= 105)||charCode==188||charCode==190||charCode==110||charCode==46||charCode==8 ){
		
	} else {
		var vv		=  flotante($(id0).val() );
		$(id1).val( vv );
		$(id0).val( getFMoney(vv));
	}
	return false;
}

function session(v1,v2){
	if(typeof v2 == "undefined"){
		return window.localStorage.getItem(v1);
	} else {
		window.localStorage.setItem(v1, v2);
		return 0;
	}
}
Gen.prototype.formF9key	= function(evt){
	var key = evt.which || evt.keyCode;
	
	switch(key){
		case 120:
			$("#btn_guardar").click();
			break;
	}
}

function getClientSize() {
  var width = 0, height = 0;

  if(typeof(window.innerWidth) == 'number') {
        width 	= window.innerWidth;
        height 	= window.innerHeight;
  } else if (document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)) {
        width 	= document.documentElement.clientWidth;
        height	= document.documentElement.clientHeight;
  } else if (document.body && (document.body.clientWidth || document.body.clientHeight)) {
        width 	= document.body.clientWidth;
        height	= document.body.clientHeight;
  }
  return {width: width, height: height};
}

Gen.prototype.w	= function(opts){
	var mSz		= getClientSize();
	var LimAl 	= mSz.height;
	var LimAn	= mSz.width;
	opts		= (typeof opts == "undefined") ? {} : opts;
	var callbackF 	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var url		= (typeof opts.url == "undefined") ? "" : opts.url;
	//var args	= (typeof opts.args == "undefined") ? "" : opts.args;
	var wd		= (typeof opts.w == "undefined") ? entero((LimAn * 0.85)) : entero(opts.w);
	var hg		= (typeof opts.h == "undefined") ? entero((LimAl * 0.75)) : entero(opts.h);
	var ifull	= (typeof opts.full == "undefined") ? false : opts.full;
	var otags	= (typeof opts.tags == "undefined") ? true : opts.tags;
	var isBlank	= (typeof opts.blank == "undefined") ? false : opts.blank;
	var isTab	= (typeof opts.tab == "undefined") ? false : opts.tab;
	var wm		= this;
	var tiny	= (typeof opts.tiny == "undefined") ? false : opts.tiny;
		wd		= ((wd > LimAn) && isBlank == false) ? LimAn : wd;
		hg		= ((hg > LimAl) && isBlank == false) ? LimAl : hg;
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
		if (isBlank == true) {
			//name = "tabshifted";
			name 	= "_blank";
		}
		if (isTab == true) {
			specs 	= "";
			name 	= "_blank";
		}		
		UPDWIN		= window.open(url,name,specs);
		if (UPDWIN == null) {
			wm.alerta({ msg : "TR.Error al Abrir la Ventana"});
		} else {
			UPDWIN.focus();	
		}
	} else {
		if (otags == true) { url	= url + "&tinybox=true"; }
		TINY.box.show({iframe: url ,boxid:'frameless', width:wd, height:hg, fixed:false, maskid:'bluemask',maskopacity:40,closejs: callbackF });
        $('html,body').animate({ scrollTop: 0 }, 700);
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
					var fin		= (index == size) ? true : false;
					callback(this, fin);
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
Gen.prototype.svc 	= function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	opts.method	= "GET";
	opts.result	= "json";
	opts.url	= "../svc/" + opts.url;
	return this.pajax(opts);
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
				setLog("Error en conversion");
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
	var mon		= (typeof opts.moneda == "undefined") ? AML_CLAVE_MONEDA_LOCAL : opts.moneda;
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
	if (msg != "") { this.alerta({msg:msg}); }
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
	var pos2	= "bottom center";

	$(id).qtip({
		content: { text: msg, title: {text: xTitle, button: true} },
		position: {	my: pos1,at: pos2 	},
		show: { ready: true,solo : true,show : "focus",hide : "blur"},
		style: {classes: 'ui-tooltip-shadow ui-tooltip-tipped'},
		events: {
			render: function(event, api) {
				if (delay == false) {	} else { setTimeout(api.hide, delay); setTimeout(mcall, delay);	}
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
Gen.prototype.empty 	= function (id){ $(id).empty(); }
Gen.prototype.happy 	= function (){
	var ready			= true;
	var self			= this;
	if( $(".unhappyMessage").length > 0){
		ready			= false;
		self.alerta({msg:"No se puede guardar . Revise los Campos ."});
	}
	return ready;
}
Gen.prototype.getMetadata 	= function(id){ var str	= $(id).attr("data-info"); return getObjectByData(str); }
Gen.prototype.moneda 		= function(vF, decimals){
	var decs 	= (typeof decimals == "undefined") ? 2 : decimals;
	var v		= new Number(vF); v	= v.formatMoney(decs, DIV_DEC, DIV_MIL);	return v;
}
Gen.prototype.addDocuments	= function(t,v){
	var self		= this;
	//t=Configuracion.credito.origen.arrendamiento
	self.w({url: "../frmutils/subir-archivo.frm.php?tipo=" + t + "&clave=" + v, tab:true})
}
Gen.prototype.notify	= function(opts){
	opts 		= (typeof opts == "undefined" ) ? {} : opts;
	var msg		= (typeof opts.msg == "undefined") ? "" : opts.msg;
	var msg		= (typeof opts.message == "undefined") ? msg : opts.message;
	
	var callB	= (typeof opts.callback == "undefined") ? "" : opts.callback;
	var lvl		= (typeof opts.nivel == "undefined") ? "info" : opts.nivel;
	lvl			= (typeof opts.tipo == "undefined") ? lvl : opts.tipo;
	lvl			= (typeof opts.type == "undefined") ? lvl : opts.type;
	var tit		= (typeof opts.title == "undefined") ? "Mensaje del Sistema" : opts.title;
	
	
	var self	= this;

  if (!("Notification" in window)) {
    self.alerta(opts);
  } else if (Notification.permission === "granted") {
    var notification = new Notification(msg);
  } else if (Notification.permission !== 'denied') {
    Notification.requestPermission(function (permission) {
      if (permission === "granted") {
        var notification = new Notification(msg);
      }
    });
  }
/*  var options = {
      body: theBody,
      icon: theIcon
  }
  var n = new Notification(theTitle,options);*/
}
//=================================================================
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
//function markItem(id){ ;  }
Gen.prototype.markTR	= function(opts){
	opts 		= (typeof opts == "undefined" ) ? {} : opts;
	var src		= (typeof opts.src == "undefined") ? "" : opts.src;
	var cssto	= (typeof opts.toclass == "undefined") ? "tr-pagar" : opts.toclass;
	//var msg		= (typeof opts.content == "undefined") ? "" : opts.content;
	if( $(src).is("tr") ){
		$(src).removeClass();
		$(src).addClass(cssto);
	}  else {
		var mP	= $(src).parent();
		if( $(mP).is("tr") ){
			$(mP).removeClass();
			$(mP).addClass(cssto);			
		} else {
			//console.log($(mP).get(0).tagName);
			var mP1	= $(mP).parent();
			if( $(mP1).is("tr") ){
				$(mP1).removeClass();
				$(mP1).addClass(cssto);			
			} else {
				
				var mP2	= $(mP1).parent();
				if( $(mP2).is("tr") ){
					$(mP2).removeClass();
					$(mP2).addClass(cssto);			
				}				
			}
		}
	}
}

Gen.prototype.disableSelect	= function(id){
	var idx = id + "_dis";
	var idm	= id + "_mig";
	//
	if(document.getElementById(idx)){
		
		//Cambiar id original
		$("#" + id).attr("name", idm);
		$("#" + id).attr("id", idm);
		//=== Asignar valor
		$("#" + idx).val( $("#" + idm).val() );
		
		$("#" + idx).attr("name", id);
		$("#" + idx).attr("id", id);
		
		$("#" + idm).attr("disabled", "disabled");
	}
}
Gen.prototype.onlyreadInput	= function(id){
	//
	if(document.getElementById(idx)){
		$("#" + idm).attr("readonly", "readonly");
	}
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
	var xG	= new Gen(); var w= entero((xG.ancho()/2));
	session(Configuracion.opciones.dialogID, content.attr("id"));
	content.dialog({resizable: false,height: "auto", width: w, modal: true, title:title });
}

Number.prototype.formatMoney = function(c, d, t){
	var n = this; c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
	return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};
function getInMoney(vF){ var xG = new Gen(); return xG.moneda(vF); }
function getFMoney(vF){ var xG = new Gen(); return xG.moneda(vF); }
function NativoFloat(vF) {
		if (SEPARADOR_DECIMAL != DIV_DEC) {
			var v	= new Number(vF);
			v		= v.formatMoney(2, DIV_DEC);
		} else {
			var v	= vF;
		}
		return v;
}
function enmiles(vF){ vF = entero(vF); vF = vF/1000;var xG = new Gen(); return xG.moneda(vF,0);}
function redondear(numer0, decimales){
		decimales	= ( typeof decimales == "undefined" ) ? 2 : decimales;
		var ar		= [1,10,100,1000,10000,100000,1000000];
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
  var from = "ÃÀÁÄÂÈÉËÊÌÍÏÎÒÓÖÔÙÚÜÛãàáäâèéëêìíïîòóöôùúüûÑñÇç", to   = "AAAAAEEEEIIIIOOOOUUUUaaaaaeeeeiiiioooouuuunncc", mapping = {};
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
	n		= nm.replace(/(?![+-]?\d*\.?\d+|e[+-]\d+)[^0-9]/g, '');
	return numero(parseFloat(n));
}
function entero(n){
	n	= String(n).replace(/(?![+-]?\d*\.?\d+|e[+-]\d+)[^0-9]/g, '');
	return numero(parseInt(n));
}
function numero(n){	if(typeof n == 'number' && !isNaN(n) && isFinite(n) && n != null && $.trim(n) != ""){ return n; } else { return 0;	} }

if (typeof jQuery != "undefined") {
	jQuery.fn.reset = function () {  $(this).each (function() { this.reset(); }); }
		jQuery.extend({
			  confirm: function(message, title, onTrue, onFalse) {
				jQuery("<div></div>").dialog({
				   // Remove the closing 'X' from the dialog
				   open: function(event, ui) { jQuery(".ui-dialog-titlebar-close").hide(); }, 
						buttons: {
							"Si": function() {
								jQuery(this).dialog("close");
								//onCompleted(sender, true);
								setTimeout(onTrue,1);
								return true;
							},
							"No": function() {
								jQuery(this).dialog("close");
								//onCompleted(sender, false);
								setTimeout(onFalse,1);
								return false;
							}
						},
						close: function(event, ui) { jQuery(this).remove(); },
						resizable: false,
						title: title,
						modal: true
					}).text(message);
				}
		});
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
Gen.prototype.Cache	= function(opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	var setC		= (typeof opts.set == "undefined") ? null : opts.set;
	//var getC		= (typeof opts.get == "undefined") ? null : opts.get;
	var idxC		= (typeof opts.clave == "undefined") ? null : opts.clave;
	var tm			= (typeof opts.tiempo == "undefined") ? null : opts.clave;
	var fnd			= function(){ session(idxC, null); }
	if(idxC !== null){
		if(setC !== null){
			session(idxC, setC);
		}
		var getC	= session(idxC);
		if(getC !== null){
			setLog("en cache " + idxC);
			
			return getC;
		}
	}
	return null;
}
Gen.prototype.hash = function(s){
  return s.split("").reduce(function(a,b){a=((a<<5)-a)+b.charCodeAt(0);return a&a},0);              
}
Gen.prototype.QFrame	= function(opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	var callback	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var id			= (typeof opts.id == "undefined") ? null : opts.id;
	var Func		= (typeof opts.func == "undefined") ? "console.log" : opts.func;
	var vURL		= (typeof opts.url == "undefined") ? "" : opts.url;
	var ixFrame		= $("#" + id);
	var vAlto		= entero((this.alto()-ixFrame.offset().top));
	$("#" + id).attr("height", vAlto );
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
	var idx			= (typeof opts.valor == "undefined") ? "" : opts.valor;
	var tbl			= (typeof opts.tabla == "undefined") ? "" : opts.tabla;
	var fnd			= (typeof opts.buscar == "undefined") ? "" : "&buscar=" + opts.buscar;
	var fld			= (typeof opts.buscado == "undefined") ? "" : "&en=" + opts.buscado;
	var mKey		= (typeof opts.key == "undefined") ? "indice" : opts.key;
	var Lab			= (typeof opts.label == "undefined") ? "etiqueta" : opts.label;
	var PreSave		= (typeof opts.presaved == "undefined") ? "" : opts.presaved; //prefijo en stored key
	$.cookie.json 	= true;
	var mURL		= SVC_REMOTE_HOST;
	
	var self		= this;
	
	if(vURL == "" && tbl != ""){
		vURL		= "../svc/tabla.svc.php?action=list&tabla=" + tbl + "&clave=" + idx + fld + fnd;
	}
	var idxC		= self.hash(vURL);
	var str			= self.Cache({clave:idxC});
	if(str == null){
		setLog("No se encontro " + idxC);
	} else {
		$("#" + id).empty();
		$("#" + id).append(str);
		vURL		= "";
	}
	
	if(vURL == ""){
		
	} else {
		$.getJSON( vURL, function( data ) {
			var str     = "";
			//var fin		
			$.each( data, function( key, val ) {
				//$("#" + id).append("<option value='" + val[mKey] + "' label='" + val[Lab] + "' >");
				//setLog(val);
				str += "<option value='" + val[mKey] + "' label='" + val[Lab] + "' >" + val[Lab] + "</option >" ;
				if (PreSave != ""){ session(PreSave + val[mKey], JSON.stringify(val));	}	//guardar en stored
			});
			$("#" + id).empty();
			$("#" + id).append(str);
			self.Cache({clave:idxC, set: str});
		});
	}
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
	opts			= (typeof opts == "undefined") ? {} : opts;
	var callback	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var frm			= (typeof opts.form == "undefined") ? "" : opts.form;
	var control		= (typeof opts.control == "undefined") ? null : opts.control;
	var extra		= (typeof opts.extra == "undefined") ? "" : opts.extra;
	var value		= (typeof opts.value == "undefined") ? "" : opts.value;
	var vURL		= (typeof opts.url == "undefined") ? "" : opts.url;
	var src			= null;
	var closeTiny	= false;
	var closeOpen	= false;
	var process		= false;
	var self		= this;
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
		if (closeTiny == true){ process	= true;  try { window.parent.TINY.box.hide(); } catch(e){ process = false; };	}
		if (closeOpen == true ){ process = true; window.close(); if(!window.closed){ process = false; }; setLog("Ma xx");  }
	} else {
		//Checar TODO: Inicio Limpio @todo
		top.location	= (vURL);
		process 		= true;
	}
	//try {$(window).qtip("hide");} catch(e){}
	if (process == false) { self.go(); }
}
Gen.prototype.go 	= function (opts){
	opts = (typeof opts == "undefined" ) ? {} : opts;
	var page	= (typeof opts.url == "undefined") ? "" : opts.url;
	var msg		= (typeof opts.msg == "undefined") ? "" : opts.msg;
	var delay	= (typeof opts.delay == "undefined") ? 4000 : opts.delay;
	var self	= this;
	var isFrame	= false;
	//var mfunc	= function(){ top.location	= url; };
	if (msg != "") { this.alerta({msg:msg}); }
	//var mopts	= {};
	var msrc	= null;
	if (window.parent){ msrc = window.parent.document; }
	if (opener){ msrc = opener.document; }
	if (window!=window.top) { isFrame = true; }
	if(msrc == null){} else {}
	//top.location	= "../index.xul.php" + page;
	if(String(top.location).indexOf("index") != -1 && isFrame == true){
		var mFrame	= window.top.document.getElementById("idFPrincipal");
		if(page == ""){ page = "../utils/frm_calendar_tasks.php"; }
		mFrame.src	= page;
		//set frame
		//self.QFrame({ url : page, id : 'idFPrincipal' });
	} else {
		top.location= SAFE_HOST_URL +  page;
	}
}
Gen.prototype.error	= function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var msg	= (typeof opts.msg == "undefined") ? "" : opts.msg;
	console.log(msg);
}
Gen.prototype.lang	= function(words){
	var wrd		= "";
	if (typeof words == "string") {
		words 	= String(words).split(" ");
	}
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
	var canc 	= (typeof opts.cancelar == "undefined") ? function(){} : opts.cancelar;
	var msgNV	= (typeof opts.alert == "undefined") ? "" : opts.alert;
	var msgTit	= (typeof opts.titulo == "undefined") ? "SAFE-OSMS" : opts.titulo;
	var metaO	= this;
	if (evalF == true) {
		if ($.trim(msg) != "") { msg	= metaO.lang(msg);	}
		//if( confirm(msg) == false){ setTimeout(canc, 10); } else { setTimeout(callB, 10);	}
		jQuery.confirm(msg, msgTit, callB, canc);
	} else {
		if ($.trim(msgNV) != "") { msg	= metaO.lang(msgNV); metaO.alerta({ msg : msg });	}
	}
}

Gen.prototype.alerta	= function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var msg		= (typeof opts.msg == "undefined") ? "" : opts.msg;
	var msg		= (typeof opts.message == "undefined") ? msg : opts.message;
	
	var callB	= (typeof opts.callback == "undefined") ? "" : opts.callback;
	var lvl		= (typeof opts.nivel == "undefined") ? "info" : opts.nivel;
	lvl			= (typeof opts.tipo == "undefined") ? lvl : opts.tipo;
	lvl			= (typeof opts.type == "undefined") ? lvl : opts.type;
	var tit		= (typeof opts.title == "undefined") ? "Mensaje del Sistema" : opts.title;
	var icn		= (typeof opts.icon == "undefined") ? "" : opts.icon;
	var raw		= (typeof opts.raw == "undefined") ? false : opts.raw;
	var info	= (typeof opts.info == "undefined") ? "" : opts.info;
	var solo	= (typeof opts.solo == "undefined") ? false : opts.solo;
	var metaO	= this;
	if ($.trim(msg) != "" && raw == false) { msg	= metaO.lang(msg);	}
	
	var mth 	= "awesome blue";
	if(lvl == "error"){
		mth	= "awesome error";
		icn	= "fa-exclamation-triangle";		
	}
	if (lvl == "ok"||lvl == 1||lvl == "success"||mth == "success"||mth == "ok") {
		mth	= "awesome ok";
		icn	= "fa-check-circle-o";
	}
	if (lvl == "warn"||lvl == 2||lvl == "warning"||mth == "warn"||mth == "warning"||mth=="info") {
		mth	= "awesome warning";
		icn	= "fa-info-circle";
	}
	if(icn == ""){
		icn	= "fa-dot-circle-o";
	}
	var opciones = { message : msg, info : info, title : tit,overlay:false };
	if(solo == true){ opciones.clearAll = true; }
	if(icn !== ""){ opciones.icon = 'fa ' + icn; }
	
	$.amaran({ content:opciones, theme : mth, position :'top right'});
	//theme:'awesome green'
}

Gen.prototype.save	= function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var tbl		= (typeof opts.tabla == "undefined") ? "" : opts.tabla;
	var id		= (typeof opts.id == "undefined") ? "" : opts.id;
	var callB	= (typeof opts.callback == "undefined") ? "" : opts.callback;
	var content	= (typeof opts.content == "undefined") ? "" : opts.content;
	var idform	= (typeof opts.form == "undefined") ? "" : opts.form;
	var nclos	= (typeof opts.close == "undefined") ? false : opts.close;
	var evt		= (typeof opts.evt == "undefined") ? null : opts.evt;
	var tt		= this;
	$.cookie.json 	= true;
	//Guardar Accion: jsAccionPostGuardarRegistro
	//Actualizar las formas de Moneda
	tt.aMonedaForm();
	//------------------------------	
	if(content == "" && idform != ""){
		content	= $("#" + idform).serialize();
	}
	var mURL	= "../svc/save.svc.php?tabla=" + tbl + "&id=" +  id + "&" + content;
	//var si		= confirm(this.lang("Confirma Eliminar el Registro"));
	//if (si) {
		$.getJSON( mURL, function( data ) {
			  //var str     = "";
			  if (data.error == true) {
				tt.alerta({msg:data.message, nivel:"error"});
			  } else {
				tt.alerta({msg:data.message, nivel:"ok"});
				if(nclos == true){
					var idFC = function(){ tt.close(); }
					setTimeout(idFC,1000);
				} else {
					if(evt != null){
						//Deshabilitar Guardado
						var src = evt.target || evt.srcElement;
						$('#' +  src.id).css('pointer-events', 'none');
					}
				}
			  }
			}
		);		
	//}
}
Gen.prototype.crudAdd	= function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var tbl		= (typeof opts.tabla == "undefined") ? "" : opts.tabla;
	var id		= (typeof opts.id == "undefined") ? "" : opts.id; //idfrm
	var callB	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	
	var nclos	= (typeof opts.close == "undefined") ? false : opts.close; //Cerrar
	var evt		= (typeof opts.evt == "undefined") ? null : opts.evt;
	//var content	= (typeof opts.content == "undefined") ? "" : opts.content;
	var tt		= this;
	$.cookie.json 	= true;
	//Guardar Accion: jsAccionPostGuardarRegistro
	//Actualizar las formas de Moneda
	tt.aMonedaForm();
	//------------------------------
	var sargs	= $("#" + id).serialize();
	tbl			= (tbl == "") ? $("#" + id).attr("data-tabla") : tbl;
	var mURL	= "../svc/add.svc.php?tabla=" + tbl + "&ix=0&" + sargs;
	tt.spinInit();
	//var si		= confirm(this.lang("Confirma Eliminar el Registro"));
	//if (si) {
		$.getJSON( mURL, function( data ) {
			  //var str     = "";
			  if (data.error == true) {
				tt.spinEnd();
				tt.alerta({msg:data.message});
			  } else {
				tt.spinEnd();
				tt.alerta({msg:data.message, nivel:"ok"});
				//$("#tr-" + tbl + "-" + id).empty();
				//setTimeout(callB,10);
				try {
					callB(data);
				} catch (e) {
						
				}
				if(nclos == true){
					tt.close();
				} else {
					if(evt != null){
						//Deshabilitar Guardado
						var src = evt.target || evt.srcElement;
						$('#' +  src.id).css('pointer-events', 'none');
					}
				}
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
	var self	= this;
	$.cookie.json 	= true;
	var mURL	= "../svc/rm.svc.php?tabla=" + tbl + "&id=" +  id;
	
	var siDel	= function(){
		$.getJSON( mURL, function( data ) {
			  //var str     = "";
			  if (data.error == true) {
				self.alerta({ msg : data.message } );
			  } else {
				$("#tr-" + tbl + "-" + id).empty();
				setTimeout(callB,10);
			  }
			}
		);		
	}
	self.confirmar({ msg: "Confirma Eliminar el Registro", callback: siDel});
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
Gen.prototype.cleanText = function(id){
	var xString		= new String($(id).val());
	validString		= xString.replace(/[|&;$%@"<>()+,]/g, "");///\W/g, "");
	$(id).val(validString);
	//setLog(validString);
	return true;
}
Gen.prototype.enc	= function(str){
		if(typeof base64 !== "undefined" && typeof Aes !== "undefined"){
				str	= Aes.Ctr.encrypt(str, SYS_UUID_TMP, 256)
				str	= base64.encode(str);
		}
		//setLog(str);
	return str;
}
Gen.prototype.dec	= function(str){
		if(typeof base64 !== "undefined" && typeof Aes !== "undefined"){
			str	= base64.decode(str);
			str	= Aes.Ctr.decrypt(str, SYS_UUID_TMP, 256)
		}
	return str;
}
var TableW 		= function(){}
TableW.prototype.add	= function(opts){
	opts	= (typeof opts == "undefined") ? {} : opts;
	var mID	= (typeof opts.id == "undefined") ? "" : opts.id;
	
	var Des	= (typeof opts.destino == "undefined") ? "" : opts.destino;
	var Cols= (typeof opts.cols == "undefined") ? [] : opts.cols;
	if (!document.getElementById(mID)) {
		
		var tt	= document.createElement("TABLE");
		var hh	= document.createElement("THEADER");
		var tr1	= document.createElement("TR");
		trH		= tr1;
		
		for(var ik = 0; ik < Cols.length; ik++){
			var ccol	= Cols[ik];
			var col		= document.createElement("TH");
			col.innerHTML	= ccol;
			trH.appendChild(col);
		}
		hh.appendChild(trH);
		var ff	= document.createElement("TFOOTER");
		var bb	= document.createElement("TBODY");
		//tt.createAttr("id", mID);
		//tt.createAttr("class", "listado");
		
		tt.appendChild(hh);
		tt.appendChild(bb);
		tt.appendChild(ff);
		//setLog(tt.innerHTML);
		$(Des).append(tt);
	
	}
}
TableW.prototype.addRow	= function(opts){
	opts	= (typeof opts == "undefined") ? {} : opts;
	var mID	= (typeof opts.id == "undefined") ? "" : opts.id;
	var mVal= (typeof opts.vals == "undefined") ? [] : opts.vals;
	var TID	= (typeof opts.tableid == "undefined") ? "" : opts.tableid;
	
	var tr1	= document.createElement("TR");
	trH		= tr1;
	
	for(var ik = 0; ik < mVal.length; ik++){
		var ccol	= mVal[ik];
		var col		= document.createElement("TD");
		col.innerHTML	= ccol;
		//document.createTextNode(ccol)
		//col.appendChild();
		trH.appendChild(col);
	}
	$(TID).find("tbody").append(trH);
}
TableW.prototype.addCol	= function(opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
}
//---------------------- INIT CREDITOS
CredGen.prototype.getImprimirSolicitud	= function(idcredito){
	var sURL = '../frmcreditos/rptsolicitudcredito1.php?solicitud=' + idcredito;
	var xGen	= new Gen(); xGen.w({ url : sURL, h : 600, w : 800 });
}
CredGen.prototype.getImprimirOrdenDeDesembolso	= function(idcredito){
	var sURL = '../rpt_formatos/rptordendesembolso.php?solicitud=' + idcredito;
	var xGen	= new Gen(); xGen.w({ url : sURL, h : 600, w : 800, full:true });
}
CredGen.prototype.getImprimirReciboDeDesembolso	= function(idcredito){
	var sURL = '../rpt_formatos/recibo_de_prestamo.rpt.php?credito=' + idcredito;
	var xGen	= new Gen(); xGen.w({ url : sURL, h : 600, w : 800, full:true });
}
CredGen.prototype.getFormaAvales	= function(idcredito){
	//var aURL 	= "../frmcreditos/frmcreditosavales.php?s=" + idcredito;
	var aURL 	= "../frmsocios/registro-personas_fisicas.frm.php?iddocumentorelacionado=" + idcredito + "&relaciones=" + iDE_CREDITO + "";
	var xGen	= new Gen(); xGen.w({ url : aURL, h : 780, w : 860, tiny: true });
}
CredGen.prototype.getVincularAvales	= function(idcredito){
	var aURL 	= "../frmcreditos/vincular.avales.frm.php?credito=" + idcredito;
	var xGen	= new Gen(); xGen.w({ url : aURL, h : 640, w : 800, tiny: true });
}
CredGen.prototype.getFormaFlujoEfectivo	= function(idcredito){
	var xURL 	= "../frmcreditos/frmcreditosflujoefvo.php?credito=" + idcredito;
	var xGen	= new Gen(); xGen.w({ url : xURL, h : 800, w : 800, tiny: true });
}
CredGen.prototype.getFormaGarantias	= function(idcredito){
	var gURL = "../frmcreditos/frmcreditosgarantias.php?solicitud=" + idcredito;
	var xGen	= new Gen(); xGen.w({ url : gURL, tab: true });
}

CredGen.prototype.getFormaAutorizacion	= function(idcredito){
	var gURL = "../frmcreditos/frmcreditosautorizados.php?credito=" + idcredito;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 800, w : 900, tiny: true });
}
CredGen.prototype.getFormaMinistracion	= function(idcredito){
	var gURL = "../frmcreditos/frmcreditosministracion.php?credito=" + idcredito;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 800, w : 900, tiny: true });
}
CredGen.prototype.getFormaPlanPagos	= function(idCredito){
	//var gURL = "../frmcreditos/frmcreditosplandepagos.php?r=1&credito=" + idCredito;
	var gURL = "../frmcreditos/plan_de_pagos.frm.php?credito=" + idCredito;
	var xGen	= new Gen(); xGen.w({ url : gURL, tab:true });
}

CredGen.prototype.getImprimirPlanPagos	= function(idrecibo, incluirAvales){
	incluirAvales = (typeof incluirAvales == "undefined") ? "no" : "si";
	var gURL = "../rpt_formatos/rptplandepagos.php?idrecibo=" + idrecibo + "&p=" + incluirAvales;
	var xGen	= new Gen(); xGen.w({ url : gURL, full : true });
}
CredGen.prototype.getImprimirPlanPagosPorCred	= function(idcredito){
	incluirAvales = (typeof incluirAvales == "undefined") ? "no" : "si";
	var gURL = "../rpt_formatos/rptplandepagos.php?credito=" + idcredito;
	var xGen	= new Gen(); xGen.w({ url : gURL, full : true });
}
CredGen.prototype.getImprimirMandato	= function(idcredito){
	var gURL = "../rpt_formatos/mandato_en_creditos.rpt.php?credito=" + idcredito;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 600, w : 750 });
}
CredGen.prototype.getImprimirPagare	= function(idcredito){
	var gURL = "../frmcreditos/creditos.formatos.frm.php?action=pagare&credito=" + idcredito;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 600, w : 750 });
}
CredGen.prototype.getImprimirContrato	= function(idcredito){
	var gURL = "../frmcreditos/creditos.formatos.frm.php?action=contrato&credito=" + idcredito;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 600, w : 750 });
}
CredGen.prototype.getImprimirCaratula	= function(idcredito){
	var gURL = "../frmcreditos/creditos.formatos.frm.php?action=caratula&credito=" + idcredito;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 600, w : 750 });
}
CredGen.prototype.goToPanelControl	= function(idcredito){
	var gURL = "../frmcreditos/creditos.panel.frm.php?credito=" + idcredito;
	var xGen	= new Gen(); xGen.w({ url : gURL, tab:true });
}
CredGen.prototype.getFormatoSIC	= function(idcredito){
	var gURL	= "../rpt_formatos/autorizacion-sic.rpt.php?credito=" + idcredito;
	var xGen	= new Gen(); xGen.w({ url : gURL, full : true });
}
CredGen.prototype.getFormatoFiniquito	= function(idcredito){
	var gURL	= "../rpt_formatos/credito.carta-finiquito.rpt.php?credito=" + idcredito;
	var xGen	= new Gen(); xGen.w({ url : gURL, full : true });
}
CredGen.prototype.goToCobrosDeCredito	= function(opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	var monto		= (typeof opts.monto == "undefined") ? 0 : opts.monto;
	var periodo		= (typeof opts.periodo == "undefined") ? 0 : opts.periodo;
	var idcredito	= (typeof opts.credito == "undefined") ? 0 : opts.credito;
	var jscall		= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var gURL 		= "../frmcaja/frmcobrosdecreditos2.php?credito=" + idcredito + "&periodo=" + periodo;
	var xGen 		= new Gen(); xGen.w({ url : gURL, h : 900, w : 900 });
}
CredGen.prototype.getListarCreditos = function(idpersona){
    var thisF 		= this;
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
			session("docto-descripcion-" + key, vals);
			str += "<option value=\"" + key + "\">" +  vals + "</option>";
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
	var desc		= session("docto-descripcion-" + idcredito);
	var xG			= new Gen();
	if(desc == null){
	$.cookie.json 	= true;
	if(entero(idcredito) > DEFAULT_CREDITO){
		$.getJSON(srUp, function( data ) {
			var str 	= $.trim(decodeEntities(data.descripcion));
			if (str == ""){
				xG.alerta({msg : "1.- MSG_NO_DATA - Credito " + idcredito});
			} else {
				$("#" + dest).val( str );
			}
		});
	} else {
		xG.alerta({msg :"2.- MSG_NO_DATA - Credito " + idcredito + "/" + DEFAULT_CREDITO });
	}
	} else {
		$("#" + dest).val( session("docto-descripcion-" + idcredito) );
	}
}
/*Gen.prototype.DataList	= function(opts){

	opts			= (typeof opts == "undefined") ? {} : opts;
	var callback	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var id			= (typeof opts.id == "undefined") ? null : opts.id;
	
	*/
CredGen.prototype.getPanelDeLinea	= function(opts){

	opts			= (typeof opts == "undefined") ? {} : opts;
	var callback	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var id			= (typeof opts.id == "undefined") ? null : opts.id;
	xG				= new Gen();
	xG.w({url:"../frmcreditos/creditos-lineas.edit.frm.php?clave=" + id, callback: callback});
}
CredGen.prototype.getReporteDeLinea	= function(opts){

	opts			= (typeof opts == "undefined") ? {} : opts;
	var callback	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var id			= (typeof opts.id == "undefined") ? null : opts.id;
	xG				= new Gen();
	xG.w({url:"../rptcreditos/lineas-de-credito.rpt.php?clave=" + id, tab:true, callback: callback});
}
CredGen.prototype.getPrincipal = function(idpersona){
    var thisF = this;
	if (typeof idpersona == "undefined"){
		if ($('#idsocio').length > 0){
			idpersona = $('#idsocio').val();
	    } else {
			idpersona = false;
		}
	}
	if ($('#idsolicitud').length > 0 && idpersona != false) {
	    var srUp = "../svc/creditos.svc.php?persona=" + idpersona;
	    var xG 			= new  Gen();
	    $.cookie.json 	= true;
	    var mURL 		= SVC_REMOTE_HOST;
		
		//obtener el rol del form para mostrar parametros
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
		var vKey		= hex_md5(srUp);
		var	vCache		= session(vKey);
		if(vCache == null){
			//setear numeros
			$('#idsolicitud').val(0);
			if ($('#nombresolicitud').length > 0){ $("#nombresolicitud").val(''); }
			//setLog(srUp);
			$.getJSON(srUp, function( data ) { 
			var str = "";
			var cnt	= 0;
			$.each( data, function( key, vals ) {
				if (cnt == 0) {
					$("#idsolicitud").val(key);
					session(vKey, key);
					if ($('#nombresolicitud').length > 0){ $("#nombresolicitud").val(vals);	}
				}
				session("docto-descripcion-" + key, vals);
				cnt++;
			});
			});
		} else {
			$("#idsolicitud").val(vCache);
			if ($('#nombresolicitud').length > 0){ $("#nombresolicitud").val(session("docto-descripcion-" + vCache));	}
			//setLog("En Cache" + vCache + " & " + session("docto-descripcion-" + vCache));
		}
	}
}
CredGen.prototype.getCompareLetra = function (opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	var monto		= (typeof opts.monto == "undefined") ? 0 : opts.monto;
	var periodo		= (typeof opts.periodo == "undefined") ? 0 : opts.periodo;
	var credito		= (typeof opts.credito == "undefined") ? 0 : opts.credito;
	var jscall		= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var url			= "../svc/letras.svc.php?credito=" + credito + "&letra=" + periodo + "&periodo=" + periodo;
	var xg			= new Gen();
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
				var ierror	= false;
				if(mdif != 0){
					alert("PLAN DE PAGO DIFERENTE AL DESCUENTO!\nMONTO DEL DESCUENTO............ : " + monto + "\nSALDO EN SISTEMA.................... : " + valor + "\nDIFERENCIA................................ : " + mdif + "\nCOBRO:SE AFECTARA EL ULTIMO PERIODO\n" + data.aviso);
					ierror = true;
				}
				if (typeof data.aviso != "undefined") {
					if ($.trim(data.aviso) != "") {
						xg.alerta({ msg : data.aviso });
					}					
				}
				jscall(ierror, credito);			//callback
		    } else {
				xg.alerta({ msg : "No existe la Parcialidad en Sistema!" });
				jscall(true, credito);			//callback
			}
		}
		});
	} else {
		var valor	= redondear(session(credito +  "." + periodo));
		var mdif	= redondear( valor - monto);
		if(mdif != 0){
			alert("PLAN DE PAGO DIFERENTE AL DESCUENTO!\nMONTO DEL DESCUENTO............ : " + monto + "\nSALDO EN SISTEMA.................... : " + valor + "\nDIFERENCIA................................ : " + mdif + "\nCOBRO:SE AFECTARA EL ULTIMO PERIODO");
			jscall(true, credito);			//callback
		}
	}
}
CredGen.prototype.getCheckLetraEnvioAnt = function (opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	var monto		= (typeof opts.monto == "undefined") ? 0 : opts.monto;
	var letra		= (typeof opts.letra == "undefined") ? 0 : opts.letra;
	var periodo		= (typeof opts.periodo == "undefined") ? 0 : opts.periodo;
	var empresa		= (typeof opts.empresa == "undefined") ? 0 : opts.empresa;
	var frecuencia	= (typeof opts.frecuencia == "undefined") ? 0 : opts.frecuencia;
	var credito		= (typeof opts.credito == "undefined") ? 0 : opts.credito;
	var jscall		= (typeof opts.callback == "undefined") ? function(ierr, idcred){} : opts.callback;
	var url			= "../svc/letrasenvioanterior.svc.php?credito=" + credito + "&letra=" + letra + "&periodo=" + periodo  + "&frecuencia=" + frecuencia + "&empresa=" + empresa;
	var xG			= new Gen();
	
	xG.pajax({
	url : url, result : "json",
	callback : function(data){
	    try { data = JSON.parse(data); } catch (e){}
	    if (typeof data != "undefined") {
			ierror		= data.error;
			jscall(ierror, credito);			//callback
			if(ierror == true){
				xG.alerta({msg:data.message});
			}
	    }
	}
	});
}
CredGen.prototype.getLetrasEnMora	= function(idcredito){
	var gURL = "../frmcreditos/creditos.letras-pendientes.frm.php?credito=" + idcredito;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 600, w : 750, tiny : true });
}
CredGen.prototype.getReporteLetrasEnMora	= function(idcredito, fecha){
	ByFecha	= (typeof fecha == "undefined") ? "" : "&on=" + fecha + "&off=" + fecha;
	var gURL = "../rptcreditos/lista_de_letras_pendientes.rpt.php?credito=" + idcredito + ByFecha;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 600, w : 750 });
}
CredGen.prototype.setNuevaNota	= function(idcredito){
	var xPer	= new PersGen();
	//6 Reporte de Llamada, 7 compromiso
	//xPer.setAgregarMemo({docto: mObj.credito, persona: mObj.codigo, otros : "&idtipodememo=6"});
	xPer.setAgregarMemo({docto: idcredito, persona: 0});
}
CredGen.prototype.setNuevaNotaCaja	= function(idcredito){
	var xPer	= new PersGen();
	//6 Reporte de Llamada, 7 compromiso
	//xPer.setAgregarMemo({docto: mObj.credito, persona: mObj.codigo, otros : "&idtipodememo=6"});
	xPer.setAgregarMemo({docto: idcredito, persona: 0,otros : "&tipo=12&lista=true"});
}
CredGen.prototype.setAgregarCompromiso	= function(idcredito){
	var gURL	= "../seguimiento/frm_agregar_compromisos.php?credito=" + idcredito;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 600, w : 800, tiny : true });
}
CredGen.prototype.setAgregarLlamada	= function(idcredito){
	var gURL	= "../frmseguimiento/llamadas.frm.php?credito=" + idcredito;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 600, w : 800, tiny : true });
}
CredGen.prototype.setAgregarNotificacion	= function(idcredito){
	var gURL	= "../frmseguimiento/notificaciones.add.frm.php?credito=" + idcredito;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 600, w : 800, tiny : true });
}
CredGen.prototype.getExpedienteDeCobranza	= function(idcredito){ var xSeg = new SegGen(); xSeg.getExpediente({ credito : idcredito }); }

CredGen.prototype.setMinistrarToPasivo	= function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var callback= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var credito	= (typeof opts.credito == "undefined") ? 0 : opts.credito;
	var gURL	= "../frmcreditos/ministrar-a-cuenta.frm.php?credito=" + credito;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 600, w : 800, tiny : true });
}
CredGen.prototype.getEstadoDeCuenta		= function (credito){
	var ogen	= new Gen();
	ogen.w({ url: "../rpt_edos_cuenta/rptestadocuentacredito.php?credito=" + credito });
}

CredGen.prototype.addCredito	= function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var persona	= (typeof opts.persona == "undefined") ? "" : "&persona=" + opts.persona;
	var monto	= (typeof opts.monto == "undefined") ? "" : "&monto="  + opts.monto;
	var producto	= (typeof opts.producto == "undefined") ? "" : "&producto="  + opts.producto;
	var callback	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	
	var idorigen	= (typeof opts.idorigen == "undefined") ? "" : "&idorigen=" + opts.idorigen; //id del origen
	var origen	= (typeof opts.origen == "undefined") ? "" : "&origen=" + opts.origen;	//tipo de origen
	var frecuencia	= (typeof opts.frecuencia == "undefined") ? "" : "&frecuencia=" + opts.frecuencia;//
	var pagos	= (typeof opts.pagos == "undefined") ? "" : "&pagos=" + opts.pagos;//
	var destino	= (typeof opts.destino == "undefined") ? "" : "&destino=" + opts.destino;//
	var oficial	= (typeof opts.oficial == "undefined") ? "" : "&oficial=" + opts.oficial;
	var url		= "../frmcreditos/solicitud_de_credito.frm.php?ix=0" + persona + producto + monto + origen + idorigen + frecuencia + pagos + destino + oficial;
	var gn		= new Gen(); gn.w({ url : url, blank : true, tab:true});
}

CredGen.prototype.getImprimirPolizaCheque	= function(idcredito){
	var gURL = "../rpt_formatos/poliza.cheque.rpt.php?credito=" + idcredito;
	var xGen	= new Gen(); xGen.w({ url : gURL, full:true });
}
CredGen.prototype.getFormaValidacion	= function(idCredito){
	var gURL = "../frmcreditos/creditos.validacion.frm.php?credito=" + idCredito;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 600, w : 750, tiny:true });
}
CredGen.prototype.getCalculadora	= function(idCredito){
	var gURL = "../frmcreditos/calculadora.pagos.frm.php?credito=" + idCredito;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 600, w : 750, tiny:true });
}
CredGen.prototype.goToCajaCobros	= function(opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	var persona		= (typeof opts.persona == "undefined") ? "" : "&persona=" + opts.persona;
	var credito		= (typeof opts.credito == "undefined") ? "" : "&credito="  + opts.credito;
	var parcial		= (typeof opts.parcialidad == "undefined") ? "" : "&parcialidad="  + opts.parcialidad;
	var callback	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var url			= "../frmcaja/frmcobrosdecreditos2.php?ix=0" + persona + credito + parcial;
	var gn			= new Gen(); gn.w({ url : url, blank : true, w : 1024, h:800, tiny:true});
}
CredGen.prototype.getHistorialNomina	= function(idCredito){
	var gURL = "../rpt_edos_cuenta/historial_de_nomina.rpt.php?credito=" + idCredito;
	var xGen	= new Gen(); xGen.w({ url : gURL, full:true, blank:true });
}
CredGen.prototype.getDocumentos	= function(idcredito){
	var gURL	= "../frmcreditos/creditos-documentos.frm.php?credito=" + idcredito;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 600, w : 800, tiny : true });
}
CredGen.prototype.getCuotaDePago	= function(opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	var vCapital	= (typeof opts.capital == "undefined") ? 0 : opts.capital;
	var vTasaAnual	= (typeof opts.tasa == "undefined") ? 0 : opts.tasa;
	var vTasaIVA	= (typeof opts.iva == "undefined") ? 0 : opts.iva;
	var vFrecuencia	= (typeof opts.frecuencia == "undefined") ? 0 : opts.frecuencia;
	var vPagos		= (typeof opts.pagos == "undefined") ? 0 : opts.pagos;
	var vResidual	= (typeof opts.residual == "undefined") ? 0 : opts.residual;
	var vTipo		= 0;
	vResidual		= redondear(vResidual);
	if(vPagos == 48){
		//setLog("Base : " + vCapital + " - Residual : " + vResidual + "  - Pagos : " + vPagos + " - Frecuencia : " + vFrecuencia +  " - Tasa " + vTasaAnual + " - Tasa IVA : " + vTasaIVA + " ");
	}
	//conversiones
	vTasaAnual		= flotante(vTasaAnual);
	if(vTasaAnual <= 1){
		vTasaAnual 	= vTasaAnual * 100;
	}
	//console.log(vTasaAnual);
	vTasaAnual		= flotante(vTasaAnual) + flotante(vTasaAnual * vTasaIVA);
	//console.log(vTasaAnual);
	vTasaFactor		= 1;
	switch(vFrecuencia){
		case 15:
			vTasaFactor	= 24;
		break;
		case 10:
			vTasaFactor	= 36;
		break;
		case 7:
			vTasaFactor	= 52;
		break;
		case 14:
			vTasaFactor	= 26;
		break;
		case 30:
			vTasaFactor	= 12;
		break;
		default:
			vTasaFactor	= 12;
			break;
	}
	var Tasa	= (vTasaAnual/vTasaFactor) /100;
	//console.log(Tasa);
	var P		= (-vCapital * Math.pow(1+Tasa,vPagos) + vResidual) / ((1 + Tasa * vTipo)*((Math.pow((1 + Tasa),vPagos) - 1) / Tasa));
	return redondear((P* (-1)),2);
		/*    
		    double P = (- NPV * pow(1+IntRate,NumPay) + FV) /               ((1 + IntRate * bStart)*((pow((1 + IntRate),NumPay) - 1) /              IntRate));
		*/	
}
//Leasing
CredGen.prototype.getLeasingPropuesta	= function(id){
	var gURL = "../rpt_formatos/leasing-propuesta.rpt.php?clave=" + id;
	var xGen	= new Gen(); xGen.w({ url : gURL, full:true, blank:true });	
}
CredGen.prototype.getLeasingCotizacion	= function(id){
	var gURL = "../rpt_formatos/leasing-cotizacion.rpt.php?clave=" + id;
	var xGen	= new Gen(); xGen.w({ url : gURL, full:true, blank:true });	
}

// --------------------- END CREDITO
//---------------------- PERSONAS
PersGen.prototype.goToAgregarFisicas	= function(opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	var callback	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var otros		= (typeof opts.otros == "undefined") ? "" : opts.otros;
	var rfc			= (typeof opts.rfc == "undefined") ? "" : "&rfc=" + opts.rfc;
	var curp		= (typeof opts.curp == "undefined") ? "" : "&curp=" + opts.curp;
	var email		= (typeof opts.email == "undefined") ? "" : "&email=" + opts.email;
	var tel			= (typeof opts.telefono == "undefined") ? "" : "&telefono=" + opts.telefono;
	var app1		= (typeof opts.apellido1 == "undefined") ? "" : "&primerapellido=" + opts.apellido1;
	var app2		= (typeof opts.apellido2 == "undefined") ? "" : "&segundoapellido=" + opts.apellido2;
	var nombre		= (typeof opts.nombres == "undefined") ? "" : "&nombre=" + opts.nombres;
	var nombrecomp	= (typeof opts.nombrecompleto == "undefined") ? "" : "&nombrecompleto=" + opts.nombrecompleto;
	var tipoorigen	= (typeof opts.tipoorigen == "undefined") ? "" : "&tipoorigen=" + opts.tipoorigen;
	var claveorigen	= (typeof opts.claveorigen == "undefined") ? "" : "&claveorigen=" + opts.claveorigen;
	var sURL 		= '../frmsocios/registro-personas_fisicas.frm.php?' + rfc + curp + email + tel + app1 + app2 + nombre + nombrecomp + tipoorigen + claveorigen + otros;
	var xGen		= new Gen(); xGen.w({ url : sURL, tab:true, callback : callback });
}
PersGen.prototype.goToAgregarMorales	= function(opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	var callback	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var otros		= (typeof opts.otros == "undefined") ? "" : opts.otros;
	var rfc			= (typeof opts.rfc == "undefined") ? "" : "&rfc=" + opts.rfc;
	var email		= (typeof opts.email == "undefined") ? "" : "&email=" + opts.email;
	var tel			= (typeof opts.telefono == "undefined") ? "" : "&telefono=" + opts.telefono;
	var nombre		= (typeof opts.nombre == "undefined") ? "" : "&nombre=" + opts.nombre;
	var tipoorigen	= (typeof opts.tipoorigen == "undefined") ? "" : "&tipoorigen=" + opts.tipoorigen;
	var claveorigen	= (typeof opts.claveorigen == "undefined") ? "" : "&claveorigen=" + opts.claveorigen;	
	var sURL 		= '../frmsocios/registro-personas_morales.frm.php?' + rfc + email + tel + nombre + tipoorigen + claveorigen + otros;
	var xGen		= new Gen(); xGen.w({ url : sURL, tab : true });
}
PersGen.prototype.goToAgregarFisicasRelacion	= function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var callback	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var otros		= (typeof opts.otros == "undefined") ? "" : opts.otros;
	this.goToAgregarFisicas({ otros : "domicilio=true&idtipodeingreso="  +  TIPO_INGRESO_RELACION + otros, callback : callback });
}

PersGen.prototype.goToPanel	= function(idpersona, tiny){
	tiny		= (typeof tiny == "undefined") ? false : tiny;
	var sURL 	= '../frmsocios/socios.panel.frm.php?socio=' + idpersona;
	var xGen	= new Gen(); xGen.w({ url : sURL, h : 600, w : 780, tiny : tiny, tab:true });
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
PersGen.prototype.getVerVivienda		= function(idpersona){
	var URIL = "../frmsocios/personas-vivienda.panel.frm.php?persona=" + idpersona;
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
	var srUp = "../frmsocios/frmupdatesocios.php?persona=" + idpersona;
	var xG	= new Gen(); xG.w({ url: srUp, tiny : true });
}
PersGen.prototype.setAgregarPerfilTransaccional	= function(idpersona){
	var srUp = "../frmsocios/perfil_transaccional.frm.php?persona=" + idpersona;
	var xG	= new Gen(); xG.w({ url: srUp, tiny : true });
}
PersGen.prototype.setAgregarOtrasReferencias	= function(idpersona){
	var srUp = "../frmsocios/personas.otras-referencias.frm.php?persona=" + idpersona;
	var xG	= new Gen(); xG.w({ url: srUp, tiny : true });
}
PersGen.prototype.getFormaBusqueda	= function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var control	= (typeof opts.control == "undefined") ? "idsocio" : opts.control;
	var jcallb	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var vTipoI	= "";
	//define el tipo de persona
	if (typeof $('#' + control).closest('form').attr('data-tipodepersona') != "undefined") {
		vTipoI	= $('#' + control).closest('form').attr('data-tipodepersona');
		vTipoI	= "&tipodeingreso=" + vTipoI;
	}
	session("idpersona.control.dx", control);
	var xG		= new Gen(); xG.w({ url : "../utils/frmbuscarsocio.php?control="  + control + vTipoI, tiny : true, h: 600, w : 800, callback:jcallb});
}

PersGen.prototype.getBuscarCreditos	= function(){
	var idxp	= $.trim(session("idpersona.control.dx"));
	
	if(idxp == null||idxp == ""){
		if($('#idsocio').length > 0 ){ session("idpersona.control.dx", "idsocio"); idxp = "idsocio"; }
	}
	if (idxp != "") {
		var persona	= $("#" + idxp).val();
		var stt		= "";
		//Roles y eventos por tag
		if (typeof $('#' + idxp).closest('form').attr('data-role') != "undefined") {
				var FRol		= $('#idsolicitud').closest('form').attr('data-role');
				switch (FRol) {
					case "ministracion":
						stt	= "&estado=" + CREDITO_ESTADO_AUTORIZADO;
						break;
					case "autorizacion":
						stt	= "&estado=" + CREDITO_ESTADO_SOLICITADO;
						break;
					case Configuracion.credito.eventos.pago:
						stt	= "&evento=" + Configuracion.credito.eventos.pago;
						break;
				}			
		}

		var xG		= new Gen(); xG.w({ url : "../utils/frmscreditos_.php?persona="  + persona + stt, tiny : true, h: 600, w : 800});
	}
}
PersGen.prototype.getBuscarCuentas	= function(){
	var idxp	= $.trim(session("idpersona.control.dx"));
	
	if(idxp == null||idxp == ""){
		if($('#idsocio').length > 0 ){ session("idpersona.control.dx", "idsocio"); idxp = "idsocio"; }
	}	
	var strTipo	= "";
	
	if (idxp != "") {
		var persona	= $("#" + idxp).val();
		if( $('#' + idxp).closest('form').length > 0 ){
			if (typeof $('#' + idxp).closest('form').attr('data-role') != "undefined" ) {
				var FRol		= $('#' + idxp).closest('form').attr('data-role');
				if(FRol=="inversion"||FRol=="320"||FRol==320){
					strTipo		= "&tipo=" + CAPTACION_TIPO_PLAZO;
				} else if(FRol=="vista"||FRol=="310"||FRol==310){
					strTipo		= "&tipo=" + CAPTACION_TIPO_VISTA;
				}
			}
			
		}
		var xG		= new Gen(); xG.w({ url : "../utils/frmcuentas_.php?persona="  + persona + strTipo, tiny : true, h: 600, w : 800});
	}
}
PersGen.prototype.getBuscarGrupos	= function(id){
	var xG		= new Gen(); xG.w({ url : "../utils/frmsgrupos.php?control="  + id, tiny : true, h: 600, w : 800});
}
PersGen.prototype.getDocumento	= function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var docto	= (typeof opts.docto == "undefined") ? "" : opts.docto;
	var id		= (typeof opts.id == "undefined") ? "" : opts.id;
	var persona	= (typeof opts.persona == "undefined") ? "" : opts.persona;
	var xG		= new Gen(); xG.w({ url : "../frmsocios/socios.docto.frm.php?persona="  + persona + "&docto=" + docto + "&id=" + id, tiny : true, h: 600, w : 800});
}
PersGen.prototype.getNombre		= function(idpersona, dest){
	var srUp 	= "../svc/personas.svc.php?persona=" + idpersona;
	dest		= (typeof dest == "undefined") ? "nombresocio" : dest;
	var xG		= new Gen();
	$.cookie.json 	= true;
	var mURL	= SVC_REMOTE_HOST;
	//if(entero(idpersona) <= DEFAULT_SOCIO && entero(session("persona.indice-activo")) > 0){}
	if(entero(idpersona) > DEFAULT_SOCIO){
		//Buscar el cache
		var vCache	= session("persona-nombre-" + idpersona);
		if(vCache == null ||vCache == ""){
			$.getJSON( srUp, function( data ) {
				var str     = "";
					$.each( data, function( key, val ) {
						var mName	= decodeEntities(val["nombrecompleto"]);
						$("#" +  dest).val(mName);
						session("persona-nombre-" + idpersona, mName);
						session(ID_PERSONA, idpersona);
					});
			});
		} else {
			$("#" +  dest).val( vCache );
		}
		var xCred	= new CredGen();	xCred.getPrincipal();
		var xCta	= new CaptGen();	xCta.getPrincipal({ persona : idpersona});
	} else {
		xG.alerta({ msg : "1.- MSG_NO_DATA Persona : " + idpersona});
	}
}
PersGen.prototype.setAgregarMemo	= function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var id		= (typeof opts.docto == "undefined") ? "" : "&docto=" + opts.docto;
	var persona	= (typeof opts.persona == "undefined") ? "" : opts.persona;
	var str		= (typeof opts.otros == "undefined") ? "" : opts.otros;
	var xG		= new Gen(); xG.w({ url : "../frmsocios/frmhistorialdesocios.php?persona="  + persona + id + str, tiny : true, h: 600, w : 600});
}

PersGen.prototype.showBuscarPersonas	= function(opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	var paterno		= (typeof opts.paterno == "undefined") ? "" : "&p=" + opts.paterno;
	var materno		= (typeof opts.materno == "undefined") ? "" : "&m=" + opts.materno;
	var nombre		= (typeof opts.nombre == "undefined") ? "" : "&n=" + opts.nombre;
	//var jcallback	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var vURL		= "../svc/personas.svc.php?ix=0" + nombre + paterno + materno;
	var oxd			= new Gen();
	//function(obj, final)
	/*			message : msg,
			info : "",
			icon : 'fa ' + icn,
			title : tit*/
	oxd.pajax({
		url: vURL,
		finder: "persona",
		result : "json",
		callback : function(obj, final){
				//"record_19":{"codigo":"20100839","nombrecompleto":"CAAMAL  CHAN LUIS ANTONIO ","apellidopaterno":"CAAMAL ","apellidomaterno":"CHAN","nombre":"LUIS ANTONIO "}
				for( dd in obj ){
					var v = obj[dd];
					oxd.alerta({ title : v.codigo, msg : v.nombrecompleto, icon : "fa-male", type: "green", raw : true });
				}
		}
	});	
	
}
PersGen.prototype.setBuscarPorIDs	= function(opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	var fiscal		= (typeof opts.fiscal == "undefined") ? "" : "&rfc=" + opts.fiscal;
	var poblacional	= (typeof opts.poblacional == "undefined") ? "" : "&curp=" + opts.poblacional;

	var jcallback	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var vURL		= "../svc/personas-buscarxid.svc.php?ix=0" + fiscal + poblacional;
	var oxd			= new Gen();
	$.cookie.json 	= true;
	$.getJSON( vURL, function( data ) {
		
		if (typeof data.existe == "undefined") {
			xG.alerta({msg : "Error al procesar el registro"});
		} else {
			if(MODO_DEBUG == true){
				xG.alerta({msg : data.messages});
			}
			jcallback(data.existe);
		}
	});		
}
PersGen.prototype.setBuscarEnListas	= function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var xG		= new Gen(); xG.w({ url : "../frmsocios/buscar_en_listas.frm.php?", tiny : true, h: 600, w : 800});
}
PersGen.prototype.getRiesgoDeCredito	= function(idpersona){
	//opts		= (typeof opts == "undefined") ? {} : opts;
	var xG		= new Gen(); xG.w({ url : "../frmsocios/personas.riesgo-creditos.frm.php?persona=" + idpersona, tiny : true, h: 600, w : 800});
}
PersGen.prototype.setAddReferenciaBancaria	= function(opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	var idbanco		= (typeof opts.banco == "undefined") ? "" : "&banco=" + opts.banco;
	var idfecha		= (typeof opts.fecha == "undefined") ? "" : "&fecha=" + opts.fecha;
	var idmonto		= (typeof opts.limite == "undefined") ? "" : "&limite=" + opts.limite;
	var idcuenta	= (typeof opts.cuenta == "undefined") ? "" : "&cuenta=" + opts.cuenta;
	var idtipo		= (typeof opts.tipo == "undefined") ? "" : "&tipo=" + opts.tipo;
	var idtarjeta	= (typeof opts.tarjeta == "undefined") ? "" : "&tarjeta=" + opts.tarjeta;
	var idpersona	= (typeof opts.persona == "undefined") ? "" : "&persona=" + opts.persona;
	var callback	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var srUp 		= "../svc/referencia-bancaria.add.svc.php?" + idcuenta +  idpersona + idmonto + idfecha + idtipo + idbanco + idtarjeta;
	
	var xG			= new Gen();
		$.cookie.json 	= true;
		$.getJSON( srUp, function( data ) {
			if (typeof data.error == "undefined") {
				xG.alerta({msg : "Error al procesar el registro"});
			} else {
				if (data.error == true) {
					xG.alerta({msg : data.messages});
				} else {
					xG.alerta({msg : "La referencia se agrega", type : "ok"});
					xG.alerta({msg : data.messages, type : "warn"});
					callback(data);
				}
			}
		});	
}
PersGen.prototype.setAddReferenciaComercial	= function(opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	var idnombre	= (typeof opts.nombre == "undefined") ? "" : "&nombre=" + opts.nombre;
	var iddireccion	= (typeof opts.direccion == "undefined") ? "" : "&direccion=" + opts.direccion;
	var idtelefono	= (typeof opts.telefono == "undefined") ? "" : "&telefono=" + opts.telefono;
	var idpersona	= (typeof opts.persona == "undefined") ? "" : "&persona=" + opts.persona;
	var callback	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	
	var srUp 		= "../svc/referencia-comercial.add.svc.php?" + idnombre +  idpersona + idtelefono + iddireccion;
	var xG			= new Gen();
		$.cookie.json 	= true;
		$.getJSON( srUp, function( data ) {
			if (typeof data.error == "undefined") {
				xG.alerta({msg : "Error al procesar el registro"});
			} else {
				if (data.error == true) {
					xG.alerta({msg : data.messages});
				} else {
					xG.alerta({msg : "La referencia se agrega", type : "ok"});
					xG.alerta({msg : data.messages, type : "warn"});
					callback(data);
				}
			}
		});	
}
PersGen.prototype.setNuevaCajaLocal	= function(){
	var xG		= new Gen(); xG.w({ url : "../frmtipos/socios_caja_local.frm.php?action=load", tiny : true, h: 600, w : 800});
}
PersGen.prototype.setEditarCajaLocal	= function(clave){
	var xG		= new Gen(); xG.w({ url : "../frmtipos/socios_caja_local.frm.php?action=load&clave=" + clave, tiny : true, h: 600, w : 800});
}
PersGen.prototype.setCobroMembresia		= function(idpersona, idmes){
	idmes		= (typeof idmes == "undefined") ? "" : "&mes=" + idmes;
	var srUp 	= "../frmcaja/cobro_de_membresia.frm.php?persona=" + idpersona + idmes;
	var xG		= new Gen(); xG.w({ url: srUp, tab : true });
}
PersGen.prototype.setFormaCheck		= function(idpersona){
	var srUp = "../frmsocios/personas.checklist.frm.php?persona=" + idpersona;
	var xG	= new Gen(); xG.w({ url: srUp, tiny : true });
}
PersGen.prototype.getFormaColegiacion	= function(idpersona){
	var srUp 	= "../frmsocios/datos-de-colegiacion.frm.php?persona=" + idpersona;
	var xG		= new Gen(); xG.w({ url: srUp, tiny : true });
}
PersGen.prototype.setCheckList	= function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var idpe	= (typeof opts.persona == "undefined") ? "" : opts.persona;
	var xval	= (typeof opts.valor == "undefined") ? "" : opts.valor;
	var dc		= (typeof opts.docto == "undefined") ? "" : opts.docto;
	var callB	= (typeof opts.callback == "undefined") ? "" : opts.callback;
	var tt		= new Gen();
	$.cookie.json 	= true;
	var mURL	= "../svc/checklist.svc.php?persona=" + idpe + "&docto=" +  dc + "&valor=" + xval;
	$.getJSON( mURL, function( data ) {
			  //var str     = "";
			  if (data.error == true) {
				tt.alerta({msg:data.message});
			  } else {
				tt.alerta({msg:data.message, nivel:"ok"});
				setTimeout(callB,10);
			  }
			}
	);
}
PersGen.prototype.getReporteAportaciones	= function(idpersona){
	var gURL = "../rpt_edos_cuenta/persona.aportaciones.rpt.php?persona=" + idpersona;
	var xGen	= new Gen(); xGen.w({ url : gURL, full:true });
}
PersGen.prototype.getPerfilAportaciones	= function(idpersona){
	var gURL = "../frmsocios/personas-pagos-perfil.frm.php?persona=" + idpersona;
	var xGen	= new Gen(); xGen.w({ url : gURL, tab:true });
}
PersGen.prototype.getReporteAportacionesDet	= function(idpersona){
	var gURL = "../rpt_edos_cuenta/persona.aportaciones.rpt.php?detalle=true&persona=" + idpersona;
	var xGen	= new Gen(); xGen.w({ url : gURL, full:true });
}
PersGen.prototype.getReporteSeguro	= function(idpersona, clave){
	var gURL = "../rpt_edos_cuenta/persona.seguros.rpt.php?persona=" + idpersona + "&clave=" + clave;
	var xGen	= new Gen(); xGen.w({ url : gURL, full:true });
}
PersGen.prototype.getReportePagosNoDoc	= function(idpersona){
	var gURL = "../rpt_edos_cuenta/persona.otros.ingresos.rpt.php?persona=" + idpersona;
	var xGen	= new Gen(); xGen.w({ url : gURL, full:true });
}
PersGen.prototype.setBaja	= function(idpersona){
	var srUp 	= "../frmsocios/frm_baja_de_socios.php?persona=" + idpersona;
	var xG		= new Gen(); xG.w({ url: srUp, tiny : true });
}
PersGen.prototype.eliminar	= function(id9persona){
	var xG		= new Gen();
	var siDel	= function(){
		$.cookie.json 	= true;
		var mURL	= "../svc/personas.del.svc.php?persona=" + id9persona;
		$.getJSON( mURL, function( data ) {
				  //var str     = "";
				  if (data.error == true) {
					xG.alerta({msg:data.message});
				  } else {
					xG.alerta({msg:data.message, nivel:"ok"});
					//setTimeout(callB,10);
				  }
				}
		);
	}
	xG.confirmar({msg:"CONFIRMA ELIMINAR A LA PERSONA . ES IRREVERSIBLE .", callback: siDel});
}
PersGen.prototype.confirmarEliminar	= function(idpersona){
	var tt		= new Gen();
}
PersGen.prototype.setUnificar	= function(idpersona, persona2){
	var srUp 	= "../frmsocios/personas.unificar.frm.php?persona=" + idpersona + "&persona2=" + persona2;
	var xG		= new Gen(); xG.w({ url: srUp, tiny : true });
}
PersGen.prototype.setFormaDatosExt		= function(idpersona){
	var srUp = "../frmsocios/personas.datos-extranjero.frm.php?persona=" + idpersona;
	var xG	= new Gen(); xG.w({ url: srUp, tiny : true });
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
	depende			= (depende == true) ? "true" : depende;
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
PersGen.prototype.addCredito = function(idpersona){ var xCred = new CredGen(); xCred.addCredito({ persona : idpersona }); }
PersGen.prototype.addPresupuesto = function(idpersona){
	var gn			= new Gen();
	gn.w({ url : "../frmcreditos/presupuesto-de-credito.frm.php?persona=" + idpersona, tab : true, w : 900 });
}
PersGen.prototype.addLeasing = function(idpersona){
	var gn			= new Gen();
	gn.w({ url : "../frmarrendamiento/cotizador.frm.php?persona=" + idpersona, tab : true, w : 900 });
}
PersGen.prototype.getPresupuesto = function(id){
	var gn			= new Gen();
	gn.w({ url : "../frmcreditos/presupuesto-de-credito.frm.php?id=" + id, tiny : true, w : 900 });
}
PersGen.prototype.getReportePresupuesto = function(id){
	var gn			= new Gen();
	gn.w({ url : "../rptcreditos/presupuesto-de-credito.rpt.php?id=" + id, blank : true, w : 900 });
}

PersGen.prototype.getListaDeNotas = function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var jscall	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var idp		= (typeof opts.persona == "undefined") ? "" : "&persona=" + opts.persona;
	var idc		= (typeof opts.credito == "undefined") ? "" : "&credito=" + opts.credito;
	var idt		= (typeof opts.tipo == "undefined") ? "" : "&tipo=" + opts.tipo;
	var ide		= (typeof opts.estado == "undefined") ? "&estado=false" : "&estado=" + opts.estado;
	var vURL	= "../svc/memos.svc.php?ix=0" + idp + idc + idt + ide;
	var oxd		= new Gen();
	$.cookie.json 	= true;
	$.getJSON( vURL, function( data ) {
		if (typeof data == "undefined") {
			xG.alerta({msg : "Error al procesar el registro"});
		} else {
			//xG.alerta({msg : data.messages});
			jscall(data);
			data=null;
		}
	});		
}
PersGen.prototype.addDatosExtr	= function(opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	var persona		= (typeof opts.persona == "undefined") ? 0 : opts.persona;
	var fechaI		= (typeof opts.fechainicial == "undefined") ? false : opts.fechainicial;
	var fechaF		= (typeof opts.fechafinal == "undefined") ? "" : "&off=" + opts.fechafinal;
	var docto		= (typeof opts.documento == "undefined") ? 0 : opts.documento;
	var nacional	= (typeof opts.nacionalidad == "undefined") ? "" : "&nacionalidad=" + opts.nacionalidad;
	var callback	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var url			= "../svc/personas.datos-extranjeros.add.svc.php?persona=" + persona + "&on=" + fechaI + "&documento=" + docto + fechaF + nacional;
	var xg			= new Gen();	
	xg.pajax({
		url : url, result : "json",
		callback : function(data){
		    try { data = JSON.parse(data); } catch (e){}
		    if (typeof data != "undefined") {
				//var valor	= redondear(data.monto,2);
				if (data.error == true) {
					xg.alerta({ msg : data.message, nivel : "error" });
				} else {
					xg.alerta({ msg : data.message, nivel : 1 });
					callback(data);
				}
		    }
		}
	});
}
PersGen.prototype.setNombre	= function(idpersona, nombre, iddestino){}

PersGen.prototype.getVerNota		= function(id){
	var URIL = "../frmsocios/personas.notas.frm.php?clave=" + id;
	var xG	= new Gen(); xG.w({ url: URIL, tiny : true, h:480, w:640 });
}
//--------------------------- Personas vivienda
PersVivGen.prototype.getVerVivienda		= function(id){
	var URIL = "../frmsocios/personas-vivienda.panel.frm.php?clave=" + id;
	var xG	= new Gen(); xG.w({ url: URIL, tiny : true, h:480, w:640 });
}
//--------------------------- INIT CAPTACION
CaptGen.prototype.goToPanel	= function(idcuenta){
	var gURL = "../frmcaptacion/cuentas.panel.frm.php?cuenta=" + idcuenta;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 600, w : 800, tiny : true, tab:true });
}
CaptGen.prototype.getImprimirMandato	= function(idcuenta){
	var gURL = "../rpt_formatos/mandato_en_depositos.rpt.php?cuenta=" + idcuenta;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 600, w : 750 });
}
CaptGen.prototype.getImprimirContrato	= function(murl){
	var gURL = murl;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 600, w : 750 });
}
CaptGen.prototype.getVerFirmas	= function(idcuenta){
	var gURL = "../rpt_formatos/mandato_en_depositos.rpt.php?cuenta=" + idcuenta;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 600, w : 750 , tiny : true});
}
CaptGen.prototype.setActualizarDatos	= function(idcuenta){
	var gURL = "../utils/frm8db7028bdcdf054882ab54f644a9d36b.php?t=captacion_cuentas&f=numero_cuenta=" + idcuenta;
	var xGen	= new Gen(); xGen.w({ url : gURL, h : 600, w : 800, tiny : true });
}

CaptGen.prototype.getEstadoDeCuentaVista	= function(idcuenta){
	var gURL = "../rpt_edos_cuenta/rpt_estado_cta_ahorro.php?cuenta=" + idcuenta;
	var xGen	= new Gen(); xGen.w({ url : gURL, tiny : false, full:true });
}
CaptGen.prototype.getEstadoDeCuentaInversion	= function(idcuenta){
	var gURL = "../rpt_edos_cuenta/rpt_estado_cta_inversion.php?cuenta=" + idcuenta;
	var xGen	= new Gen(); xGen.w({ url : gURL, tiny : false, full:true });
}
CaptGen.prototype.getEstadoDeCuentaSDPM	= function(idcuenta){
	var gURL = "../rptcaptacion/rpt_estado_cta_sdpm.php?cuenta=" + idcuenta;
	var xGen	= new Gen(); xGen.w({ url : gURL, full:true });
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
		if ($('#idsocio').length > 0){ persona = $("#idsocio").val(); }
	}
	
	if($('#' + control).length > 0 ){
		if( $('#' + control).closest('form').length > 0 ){
			//alert($('#' + control).closest('form').attr('data-role'));
			if (typeof $('#' + control).closest('form').attr('data-role') != "undefined" ) {
				var FRol		= $('#' + control).closest('form').attr('data-role');
				if(tipo <=0){
					if(FRol=="inversion"||FRol=="320"||FRol==320){
						tipo		= CAPTACION_TIPO_PLAZO;
					} else if(FRol=="vista"||FRol=="310"||FRol==310){
						tipo		= CAPTACION_TIPO_VISTA;
					}
				}
			}
		}
	
		if (entero(persona) <= 0) {
			console.log("No hay valores de busqueda " + persona);
		} else {
			var srUp 		= "../svc/cuentas.svc.php?tipo=" + tipo + "&subtipo=" + subtipo + "&persona=" + persona;
			console.log(srUp);
			var xG			= new Gen();
			$.cookie.json 	= true;
			var mURL		= SVC_REMOTE_HOST;
			session(ID_PERSONA, persona);
			$.getJSON( srUp, function( data ){
				var str     = "";
				$.each( data, function( key, val ){
					$("#" + control).val( decodeEntities(val["cuenta"]) );
				});
			});		
		}
	}
}
CaptGen.prototype.setNuevoDepositoVista	= function(opts){
	opts = (typeof opts == "undefined" ) ? {} : opts;
	var idcuenta	= (typeof opts.cuenta == "undefined" ) ? "" : "&cuenta=" + opts.cuenta;
	var idpersona	= (typeof opts.persona == "undefined" ) ? "" : "&persona=" + opts.persona;
	var idmonto		= (typeof opts.monto == "undefined" ) ? "" : "&monto=" + opts.monto;
	var idfecha		= (typeof opts.fecha == "undefined" ) ? "" : "&idfechaactual=" + opts.fecha;
	var idfpago		= (typeof opts.forma_de_pago == "undefined" ) ? "" : "&tipodepago=" + opts.forma_de_pago;
	var idobserva	= (typeof opts.observaciones == "undefined" ) ? "" : "&idobservaciones=" + opts.observaciones;
	var idbanco		= (typeof opts.cuenta_bancaria == "undefined" ) ? "" : "&cuentabancaria=" + opts.cuenta_bancaria;
	var idempresa	= (typeof opts.empresa == "undefined" ) ? "" : "&empresa=" + opts.empresa;
	
	var callB		= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	//var callF		= (typeof opts.callbackFin == "undefined") ? function(){} : opts.callbackFin;
	var srUp 		= "../svc/vista-deposito.svc.php?rmt=true" + idcuenta +  idpersona + idmonto + idfecha + idfpago +  idobserva + idbanco + idempresa;
	
	var xG			= new Gen();
		$.cookie.json 	= true;
		$.getJSON( srUp, function( data ) {
			if (typeof data.error == "undefined") {
				xG.alerta({msg : "Error al procesar el Deposito"});
			} else {
				if (data.error == true) {
					xG.alerta({msg : data.messages});
				} else {
					if (data.alta == true) {
						xG.alerta({msg : "La cuenta fue creada", type : "warn"});
					}
					xG.alerta({msg : data.messages, type : "ok"});
					callB(data.recibo);
				}
			}
		});

}
//--------------------------- END CAPTACION
RecGen.prototype.panel			= function(clave){
	var xGen	= new Gen(); xGen.w({ url: "../frmoperaciones/recibos.panel.frm.php?cNumeroRecibo=" + clave, h:600, w : 800, tiny : true});
}
RecGen.prototype.reporte			= function(clave){
	var xGen	= new Gen(); xGen.w({ url: "../rptoperaciones/rpt_consulta_recibos_individual.php?recibo=" + clave, h:600, w : 800});
}
RecGen.prototype.formato			= function(clave){
	var xGen	= new Gen(); xGen.w({ url: "../rpt_formatos/recibo.rpt.php?recibo=" + clave, full: true});
}
RecGen.prototype.formatoNT			= function(clave){
	var xGen	= new Gen(); xGen.w({ url: "../rpt_formatos/recibo.rpt.php?notesoreria=true&recibo=" + clave, full: true});
}
RecGen.prototype.factura			= function(clave){
	var xGen	= new Gen(); xGen.w({ url: "../rpt_formatos/factura.xml.php?recibo=" + clave, h:600, w : 800});
}
RecGen.prototype.addBancos			= function(clave){
	var xGen	= new Gen(); xGen.w({ url: "../frmbancos/movimientos_bancarios.frm.php?origen=recibo&item=" + clave, h:600, w : 800, tiny : true});
}
RecGen.prototype.addTesoreria			= function(clave){
	var xGen	= new Gen(); xGen.w({ url: "../frmtesoreria/tesoreria_operaciones.frm.php?origen=recibo&item=" + clave, h:600, w : 800, tiny : true});
}
RecGen.prototype.getRecibosPorCredito			= function(credito,periodo){
	var strP = (typeof periodo == "undefined" ) ? "" : "&periodo=" + periodo;
	
	var xGen	= new Gen(); xGen.w({ url: "../frmoperaciones/recibos-por-credito.frm.php?credito=" + credito + strP, isTab : true});
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
	var xG			= new Gen();
	var xUrl		= "../svc/poliza_por_recibo.svc.php?action=LIST&lim=1&recibo=" + recibo;
	$.getJSON( xUrl, function( data ) {
		if (typeof data.codigo == "undefined") {
			//alert("No existe la Poliza por el recibo");
			setTimeout(jscall, 10);
			xG.alerta({ msg : "No existe la POLIZA"});
		} else {
			if (open == true) {
				var xCont	= new ContGen();
				xCont.goToPoliza(data.codigo);
			}
		}
	});
	
	//
}
RecGen.prototype.eliminar	= function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var id		= (typeof opts.recibo == "undefined") ? "" : opts.recibo;
	var nomina	= (typeof opts.nomina == "undefined") ? "" : "&nomina=" + opts.nomina;
	var letra  	= (typeof opts.letra == "undefined") ? "" : "&letra=" + opts.letra;
	var callB	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	//agregar confirm y no confirm
	var xG		= new Gen();
	$.cookie.json 	= true;
	var mURL	= "../svc/recibos.del.svc.php?id=" +  id + letra + nomina;
	$.getJSON( mURL, function( data ) {
		  //var str     = "";
		  if (data.error == true) {
			xG.alerta({msg : data.message});
		  } else {
			xG.alerta({msg : data.message, type : "ok"});
			callB(id);
		  }
		}
	);		
}
RecGen.prototype.confirmaEliminar	= function(id){
	//opts		= (typeof opts == "undefined") ? {} : opts;
	var self	= this;
	var xG		= new Gen();
	var onDel	= function(){ xG.spinEnd(); xG.close(); }
	var readyF	= function(){ xG.spinInit(); self.eliminar({ recibo : id, callback: onDel }); }
	xG.confirmar({ msg : "Eliminar Recibo y Operaciones?\nEl cambio es permanente." , callback : readyF }); 
}
RecGen.prototype.editar	= function(id){
	//opts		= (typeof opts == "undefined") ? {} : opts;
	var xurl 	= "../utils/frm8db7028bdcdf054882ab54f644a9d36b.php?t=operaciones_recibos&f=idoperaciones_recibos=" + id;
	var xG	= new Gen(); xG.w({url : xurl, h : 600, W : 800, tiny : true });
}
RecGen.prototype.getReporteEmitidos			= function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	//var id		= (typeof opts.recibo == "undefined") ? "" : opts.recibo;
	var empresa	= (typeof opts.empresa == "undefined") ? "" : "&empresa=" + opts.empresa;
	var cajero	= (typeof opts.cajero == "undefined") ? "" : "&cajero=" + opts.cajero;
	var on		= (typeof opts.desde == "undefined") ? FECHA_ACTUAL : opts.desde;
	var off		= (typeof opts.hasta == "undefined") ? FECHA_ACTUAL : opts.hasta;
	var URL		= "../rpttesoreria/rpt_caja_corte_sobre_recibos.php?on=" + on + "&off=" + off + empresa + cajero;
	var xGen	= new Gen(); xGen.w({ url: URL, h:800, w : 800});
}

//------------------------ END RECIBOS
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

//------------------------------------------------------------------ Reubicar

TesGen.prototype.goCerrarCaja	= function(mFecha){
	mArg			= (typeof mFecha == "undefined") ? "" : "&fecha=" + mFecha;
	var gn		= new Gen();
	gn.w({ url : "../frmcaja/cerrar_caja.frm.php?o=null" + mArg });
}
TesGen.prototype.setAgregarPago	= function(opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	var recibo		= (typeof opts.recibo == "undefined") ? 0 : opts.recibo;
	var tipo		= (typeof opts.tipo == "undefined") ? 0 : opts.tipo;
	var docto		= (typeof opts.documento == "undefined") ? 0 : opts.documento;
	var monto		= (typeof opts.monto == "undefined") ? 0 : opts.monto;
	var callback	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var url			= "../svc/tesoreria.pagos.svc.php?recibo=" + recibo + "&tipo=" + tipo + "&monto=" + monto;
	
	var xg			= new Gen();	
	xg.pajax({
		url : url, result : "json",
		callback : function(data){
		    try { data = JSON.parse(data); } catch (e){}
		    if (typeof data != "undefined") {
				//var valor	= redondear(data.monto,2);
				if (data.error == true) {
					xg.alerta({ msg : data.message, nivel : "error" });
				} else {
					xg.alerta({ msg : data.message, nivel : 1 });
					callback(data);
				}
		    }
		}
	});
}
//------------------------------------------------------------------ Empresas
EmpGen.prototype.getCedulaAhorro = function(id){ var gn	= new Gen(); gn.w({ url : "../rptempresas/incidencias_de_captacion.rpt.php?empresa=" + id }); }
EmpGen.prototype.getOrdenDeCobranza = function(idnom){ var xg = new Gen(); xg.w({ url : "../rptcreditos/orden_de_cobranza.rpt.php?nomina=" + idnom, w : 800, h : 600 }); }
EmpGen.prototype.getEstadoDeCuenta = function(id){ var xg = new Gen(); xg.w({ url : "../rptempresas/empresas.movimientos.rpt.php?empresa=" + id, w : 800, h : 600 }); }
EmpGen.prototype.setActualizarDatos = function(id){ var gn	= new Gen(); gn.w({ url : "../frmsocios/agregar-empresas.frm.php?step=update&empresa=" + id, tiny : true }); }
EmpGen.prototype.getTablaDeCobranza = function(idnom){ var xg = new Gen(); xg.w({ url : "../frmcreditos/tabla_de_cobranza.frm.php?nomina=" + idnom, w : 800, h : 600, tab : true }); }
EmpGen.prototype.getTablaDeCaptacion = function(id){ var xg = new Gen(); xg.w({ url : "../frmcaja/empresas-captacion.frm.php?empresa=" + id, w : 800, h : 600, tiny : false, full:true }); }
EmpGen.prototype.setAgregar = function(){ var gn	= new Gen(); gn.w({ url : "../frmempresas/empresas.new.frm.php?", tiny : true }); }
EmpGen.prototype.goToPanel = function(id){ var gn	= new Gen(); gn.w({ url : "../frmempresas/empresas.panel.frm.php?empresa=" + id, tab:true }); }

EmpGen.prototype.setBuscar	= function(opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	var id			= (typeof opts.persona == "undefined") ? "" : "&persona=" + opts.persona;

	var jcallback	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var vURL		= "../svc/empresas.buscar.svc.php?ix=0" + id;
	var oxd			= new Gen();
	$.cookie.json 	= true;
	$.getJSON( vURL, function( data ) {
		
		if (typeof data.existe == "undefined") {
			xG.alerta({msg : "Error al procesar el registro"});
		} else {
			xG.alerta({msg : data.messages, tipo: "aviso"});
			jcallback(data.existe);
		}
	});		
}
//------------------------------------------------------------------ end Empresas



ContGen.prototype.ImprimirPoliza	= function(clave){
	var mRPT		= "../rptcontables/rpt_auxiliar_de_polizas.php?codigo=" + clave;
	var xGen		= new Gen();
	xGen.w({ url : mRPT });
}

ContGen.prototype.goToPanel	= function(clave){
	var mRPT		= "../frmcontabilidad/cuenta.panel.frm.php?cuenta=" + clave;
	var xGen		= new Gen();
	xGen.w({ url : mRPT, tiny : true, tab:true });
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
//------------------------------------------------------------------ AML Funciones
AmlGen.prototype.goToCambiarNivel	= function(idpersona){
	var gn		= new Gen();
	gn.w({ url : "../frmpld/registro_persona_riesgosa.frm.php?persona=" + idpersona, tiny : true, h: 600, w : 800 });
}
AmlGen.prototype.getReporteDeTransacciones	= function(idpersona){ var gn= new Gen(); gn.w({ url : "../rptpld/transacciones_por_persona.rpt.php?persona=" + idpersona, tiny : false, h: 600, w : 800 }); }
AmlGen.prototype.getReporteDeTransaccionesPorNucleo	= function(idpersona){ var gn= new Gen(); gn.w({ url : "../rptpld/transacciones_por_nucleo.rpt.php?persona=" + idpersona, tiny : false, h: 600, w : 800 }); }
AmlGen.prototype.getReporteDePerfilTransaccional	= function(idpersona){ var gn= new Gen(); gn.w({ url : "../rptpld/perfil_transaccional.rpt.php?persona=" + idpersona, tiny : false, h: 600, w : 800 }); }
AmlGen.prototype.getReporteDeAlertas	= function(idpersona){ var gn= new Gen(); gn.w({ url : "../rptpld/personas.alertas.rpt.php?persona=" + idpersona, tiny : false, blank:true }); }
AmlGen.prototype.getConsultaListaNegra	= function(idpersona){ var gn= new Gen(); gn.w({ url : "../frmsocios/personas.consulta-lista-negra.frm.php?persona=" + idpersona, tab : true }); }
AmlGen.prototype.getConsultaPEPS		= function(idpersona){ var gn= new Gen(); gn.w({ url : "../frmsocios/personas.consulta-peps.frm.php?persona=" + idpersona, tab : true }); }
AmlGen.prototype.getConsultaListas		= function(idpersona){ var gn= new Gen(); gn.w({ url : "../frmsocios/personas.consulta-en-listas.frm.php?persona=" + idpersona, tab : true }); }

AmlGen.prototype.addCuestionario		= function(idcredito){ var gn= new Gen(); gn.w({ url : "../frmcreditos/creditos.perfil-aml.frm.php?credito=" + idcredito, tiny : true, h: 600, w : 800 }); }
AmlGen.prototype.setDictamenConsulta	= function(id){ var gn= new Gen(); gn.w({ url : "../frmsocios/personas.consulta-listas.dictamen.frm.php?clave=" + id, tiny : true, h: 600, w : 800 }); }

AmlGen.prototype.getPanelDeAlerta	= function(id){ var gn= new Gen(); gn.w({ url : "../frmpld/alertas-panel.frm.php?clave=" + id, tab : true, h: 600, w : 800 }); }
//------------------------------------------------------------------ Grupos Funciones

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
DomGen.prototype.getBuscarColonias	= function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var ccb		= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var xG		= new Gen();
	xG.w({url:"../frmsocios/buscar-colonias.frm.php?", tiny:true});
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
		var xG	= new Gen();
		
		xG.LoadFromCache({
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
	var xUrl	= "../svc/colonias.svc.php?action=LIST&lim=55&cp=" + obj.value + ByEstado;
	
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
ValidGen.prototype.NoVacio	= function(v){ return (String(v).length <=0) ? false : true; }
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

PersAEGen.prototype.getBuscarActs	= function(idf){
	var strTipo	= "";
	if (idf != "") {
		var idfrm	= $("#" + idf).val();
		if( $('#' + idf).closest('form').length > 0 ){
			var xG	= new Gen(); xG.w({ url : "../frmsocios/buscar.actividades.frm.php?idcontrol="  + idf, tiny : true, h: 600, w : 800});
		}		
	}
}
PersAEGen.prototype.getBuscarActsSCIAN	= function(idf){
	var strTipo	= "";
	if (idf != "") {
		var idfrm	= $("#" + idf).val();
		if( $('#' + idf).closest('form').length > 0 ){
			var xG	= new Gen(); xG.w({ url : "../frmsocios/buscar.actividades-scian.frm.php?idcontrol="  + idf, tiny : true, h: 600, w : 800});
		}		
	}
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
	var nGen		=  new Gen();
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
PlanGen.prototype.setEliminarLetra = function (opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	var monto		= (typeof opts.monto == "undefined") ? 0 : opts.monto;
	var periodo		= (typeof opts.periodo == "undefined") ? 0 : opts.periodo;
	var credito		= (typeof opts.credito == "undefined") ? 0 : opts.credito;
	var jscall		= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var url			= "../svc/letras.svc.php?credito=" + credito + "&letra=" + periodo + "&periodo=" + periodo + "&cmd=delete";
	var xg			= new Gen();
	
	var siElim		= confirm(xg.lang("CONFIRMA ELIMINAR LA PARCIALIDAD"));
	if(siElim == true){
	xg.pajax({
		url : url, result : "json",
		callback : function(data){
		    try { data = JSON.parse(data); } catch (e){}
		    if (typeof data != "undefined") {
				if (typeof data.aviso != "undefined") {
					if ($.trim(data.aviso) != "") {
						xg.alerta({ msg : data.aviso });
					}					
				}				
		    } else {
				
			}
		}
	});
	}
}
PlanGen.prototype.setAnualidadLetra = function (opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	var monto		= (typeof opts.monto == "undefined") ? 0 : opts.monto;
	var periodo		= (typeof opts.periodo == "undefined") ? 0 : opts.periodo;
	var credito		= (typeof opts.credito == "undefined") ? 0 : opts.credito;
	
	var jscall		= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var url			= "../svc/setanualidad.svc.php?credito=" + credito + "&letra=" + periodo + "&periodo=" + periodo + "&monto=" + monto;
	var xg			= new Gen();
	xg.pajax({
		url : url, result : "json",
		callback : function(data){
		    try { data = JSON.parse(data); } catch (e){}
		    if (typeof data != "undefined") {
				if (typeof data.message != "undefined") {
					if ($.trim(data.message) != "") {
						xg.alerta({ msg : data.message });
					}					
				}				
		    } else {
				
			}
		}
	});
}
PlanGen.prototype.setPagoEspecial = function (opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	var monto		= (typeof opts.monto == "undefined") ? 0 : opts.monto;
	var periodo		= (typeof opts.periodo == "undefined") ? 0 : opts.periodo;
	var credito		= (typeof opts.credito == "undefined") ? 0 : opts.credito;
	
	var jscall		= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var url			= "../svc/setpagoesp.svc.php?credito=" + credito + "&letra=" + periodo + "&periodo=" + periodo + "&monto=" + monto;
	var xg			= new Gen();
	xg.pajax({
		url : url, result : "json",
		callback : function(data){
		    try { data = JSON.parse(data); } catch (e){}
		    if (typeof data != "undefined") {
				if (typeof data.message != "undefined") {
					if ($.trim(data.message) != "") {
						xg.alerta({ msg : data.message });
					}					
				}				
		    } else {
				
			}
		}
	});
}
SegGen.prototype.setLlamadaCancelada = function(id){
		this.setLlamadaEstado({estado : SEGUIMIENTO_ESTADO_CANCELADO , clave : id});
}
SegGen.prototype.setLlamadaEfectuada = function(id){
		this.setLlamadaEstado({estado : SEGUIMIENTO_ESTADO_EFECTUADO , clave : id});
}
SegGen.prototype.setLlamadaEstado = function (opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var estado	= (typeof opts.estado == "undefined") ? "" : "&estado=" + opts.estado;
	var clave	= (typeof opts.clave == "undefined") ? 0 : opts.clave;
	var jscall	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var xG		= new Gen();
	var xUrl	= "../frmseguimiento/llamadas-cambiar_estado.frm.php?id=" + clave + estado;
	xG.w({ url : xUrl, tiny : true, w : 480, callback : jscall});	
}

SegGen.prototype.setAgregarNotaLlamada	= function(id){
	var mObj	= processMetaData("#tr-seguimiento_llamadas-" + id);
	//"codigo=1901387|nombre=NOEL MORENO OSORIO|credito=290138701|clave=243117|fecha=2015-02-21|hora=08:00:00|estatus=pendiente|resultados=LLAMADAS DIARIAS AUTOMATICAS : 355977"
	var xPer	= new PersGen();
	//6 Reporte de Llamada, 7 compromiso
	xPer.setAgregarMemo({docto: mObj.credito, persona: mObj.codigo, otros : "&idtipodememo=6"});
}

SegGen.prototype.getDetalleDeCompromiso = function (opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	//var estado	= (typeof opts.estado == "undefined") ? "" : "&estado=" + opts.estado;
	var clave	= (typeof opts.clave == "undefined") ? 0 : opts.clave;
	var jscall	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var xG		= new Gen();
	var xUrl	= "../frmseguimiento/compromisos-detalle.frm.php?id=" + clave;
	xG.w({ url : xUrl, tiny : true, w : 900, callback : jscall });
}

SegGen.prototype.setEditarCompromiso = function (opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	//var estado	= (typeof opts.estado == "undefined") ? "" : "&estado=" + opts.estado;
	var clave	= (typeof opts.clave == "undefined") ? 0 : opts.clave;
	var jscall	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var xG		= new Gen();
	var xUrl	= "../seguimiento/frm_agregar_compromisos.php?id=" + clave;
	xG.w({ url : xUrl, tiny : true, w : 900, callback : jscall });
}
SegGen.prototype.setAgregarNotaVisita	= function(idpersona,idcredito){ //6 Reporte de Llamada, 7 compromiso  5 reporte de vista
	var xPer	= new PersGen(); xPer.setAgregarMemo({docto: idcredito, persona: idpersona, otros : "&idtipodememo=5"});
}
SegGen.prototype.setAgregarNotaCompromiso	= function(idpersona,idcredito){ //6 Reporte de Llamada, 7 compromiso  5 reporte de vista
	var xPer	= new PersGen(); xPer.setAgregarMemo({docto: idcredito, persona: idpersona, otros : "&idtipodememo=7"});
}
SegGen.prototype.getListaDeLlamadas = function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var jscall	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var fi		= (typeof opts.fecha == "undefined") ? "" : "&fecha=" + opts.fecha;
	var ff		= (typeof opts.fechaFinal == "undefined") ? "" : "&fechafinal=" + opts.fechaFinal;
	var idto	= (typeof opts.todo == "undefined") ? "" : "&todo=" + opts.todo;
	var vURL		= "../svc/llamadas.svc.php?ix=0" + fi + ff + idto;
	var oxd			= new Gen();
	$.cookie.json 	= true;
	$.getJSON( vURL, function( data ) {
		
		if (typeof data == "undefined") {
			xG.alerta({msg : "Error al procesar el registro"});
		} else {
			//xG.alerta({msg : data.messages});
			jscall(data);
			data=null;
		}
	});		
}
SegGen.prototype.getListaDeCompromisos = function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var jscall	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var fi		= (typeof opts.fecha == "undefined") ? "" : "&fecha=" + opts.fecha;
	var ff		= (typeof opts.fechaFinal == "undefined") ? "" : "&fechafinal=" + opts.fechaFinal;
	var idto	= (typeof opts.todo == "undefined") ? "" : "&todo=" + opts.todo;
	
	var vURL		= "../svc/compromisos.svc.php?ix=0" + fi + ff + idto;
	var oxd			= new Gen();
	$.cookie.json 	= true;
	$.getJSON( vURL, function( data ) {
		if (typeof data == "undefined") {
			xG.alerta({msg : "Error al procesar el registro"});
		} else {
			//xG.alerta({msg : data.messages});
			jscall(data);
			data=null;
		}
	});
}
SegGen.prototype.getListaDeNotificaciones = function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var jscall	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var fi		= (typeof opts.fecha == "undefined") ? false : "&fecha=" + opts.fecha;
	var ff		= (typeof opts.fechaFinal == "undefined") ? false : "&fechafinal=" + opts.fechaFinal;
	var idto	= (typeof opts.todo == "undefined") ? "" : "&todo=" + opts.todo;
	
	var vURL		= "../svc/notificaciones.svc.php?ix=0" + fi + ff + idto;
	var oxd			= new Gen();
	$.cookie.json 	= true;
	$.getJSON( vURL, function( data ) {
		if (typeof data == "undefined") {
			xG.alerta({msg : "Error al procesar el registro"});
		} else {
			//xG.alerta({msg : data.messages});
			jscall(data);
			data=null;
		}
	});		
}
SegGen.prototype.getExpediente = function (opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var persona	= (typeof opts.persona == "undefined") ? "" : "&persona=" + opts.persona;
	var credito	= (typeof opts.credito == "undefined") ? "" : "&credito=" + opts.credito;
	var jscall	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var xG		= new Gen();
	var xUrl	= "../rptseguimiento/expediente_integral.rpt.php?id=0" + persona + credito;
	xG.w({ url : xUrl,  w : 900, callback : jscall });
}

SegGen.prototype.setEstadoDeCompromiso = function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var jscall	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var clave	= (typeof opts.clave == "undefined") ? 0 : opts.clave;
	var estado	= (typeof opts.estado == "undefined") ? "" : "&estado="+opts.estado;
	var vURL		= "../svc/compromisos.edit.svc.php?clave=" + clave + estado;
	var oxd			= new Gen();
	$.cookie.json 	= true;
	$.getJSON( vURL, function( data ) {
		if (typeof data == "undefined") {
			xG.alerta({msg : "Error al procesar el registro"});
		} else {
			xG.alerta({msg : data.messages});
			jscall(data);
			data=null;
		}
	});
}

SegGen.prototype.getListaDeAtrasos = function(opts){
	opts		= (typeof opts == "undefined") ? {} : opts;
	var jscall	= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var fi		= (typeof opts.fecha == "undefined") ? false : "&fecha=" + opts.fecha;
	var ff		= (typeof opts.fechaFinal == "undefined") ? false : "&fechafinal=" + opts.fechaFinal;
	var vURL		= "../svc/creditos.atrasos.svc.php?ix=0" + fi + ff;
	var oxd			= new Gen();
	$.cookie.json 	= true;
	$.getJSON( vURL, function( data ) {
		if (typeof data == "undefined") {
			xG.alerta({msg : "Error al procesar el registro"});
		} else {
			//xG.alerta({msg : data.messages});
			jscall(data);
			data=null;
		}
	});		
}

var validacion = {
		nozero : function(v){
				var xVal	= new ValidGen();
				return xVal.NoCero(v);
		},
		fechaNacimiento : function(v){
			var xF		= new FechaGen();
			v 			= xF.get(v);
			var xG		= new Gen();
			var ok		= true;
			var xDate	= new XDate(v);
			var annios	= entero(xDate.diffYears(FECHA_ACTUAL));
			if(PERSONAS_ACEPTAR_MENORES == false && annios < EDAD_PRODUCTIVA_MINIMA){
				ok	= false;
				xG.alerta({ msg : "La edad(" + annios + ") debe ser mayor o igual a " + EDAD_PRODUCTIVA_MINIMA });
			}
			if(annios > EDAD_PRODUCTIVA_MAXIMA){
				oK	= false;
				xG.alerta({ msg : "La edad(" + annios + ") debe ser menor o igual a " + EDAD_PRODUCTIVA_MAXIMA });
			}
			return ok;
		},
		empresa: function(v){
			var ok	= true;
			v		= entero(v);
			if(PERSONAS_CONTROLAR_POR_EMPRESA == true){
				if(v == FALLBACK_CLAVE_EMPRESA||v == DEFAULT_EMPRESA||v == 0){
					ok	= false;
				}
			}
			return ok;
		},
		actividadeconomica : function(v){
			var ok 		= true;
				if(MODULO_AML_ACTIVADO == true){
					if(v <= 99){ ok = false; } //normal mexico > 100 actividades
				}
			return ok;
		},
		calle : function(v){
			var ok 		= true;
			var xS		= new String(v);
			if($.trim(v) == ""){ ok = false; }
			if(entero(v) <= 0){	//Si es nombre que lleve tres lineas
				if(xS.length < 4){
					ok	= false;
				}
			}
			return ok;
		},
		novacio : function(v){
				return (String(v).length <=0) ? false : true;
		},
		persona : function(v){
			var ok		= true;
			if(entero(v) <= DEFAULT_SOCIO){ ok = false; }
			return ok;
		},
		email: function (val) {
			var re = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/
			return re.test(val);    
		},		
		codigopostal : function(v){
			var ok		= false;
			var xPais	= ($("#idpais").length > 0) ? $("#idpais").val() : EACP_CLAVE_DE_PAIS;
			var xCP		= ($("#idcodigopostal").length > 0) ? entero($("#idcodigopostal").val()) : entero(v);
			var xVal	= new ValidGen();
			var xG		= new Gen();
			var ccnt	= {};
			var isLoad	= false;
			var postCP 	= function(obj){
				xG.spinInit();
				ccnt	= obj;
				if(typeof jsaGetMunicipios != "undefined"){ jsaGetMunicipios();	}
				if(typeof jsaGetLocalidades != "undefined"){ jsaGetLocalidades(); }
				setTimeout(postpostCP, 1000);
			};
			var postpostCP 	= function(){
				var xG		= new Gen();
				
				for(mob in ccnt){
					var m		= ccnt[mob];
					if (flotante(m.estado) > 0) {
						//{"codigo":"380004","clavepostal":"24026","nombre":"Fraccionamiento","estado":"4","municipio":"2",
						//"colonia":"Viveros","nombre_del_municipio":"Campeche","nombre_del_estado":"CAMPECHE","buscador":"24026-380004"}
						//===== Entidad federativa
						if($("#identidadfederativa").length > 0){ $("#identidadfederativa").val(m.estado); }
						//===== Municipio
						if($("#idmunicipio").length > 0){ $("#idmunicipio").val(m.municipio); }
						if($("#idnombremunicipio").length > 0){ $("#idnombremunicipio").val(m.nombre_del_municipio); }
						//===== Localidad
						if($("#idnombrelocalidad").length > 0){ $("#idnombrelocalidad").val(m.ciudad); }
						//===== Colonia
						if($("#idnombrecolonia").length > 0){
							if($("#idnombrecolonia").val() == ""){
								$("#idnombrecolonia").val(m.nombre);
							}
						}
						
					}
				}
				xG.spinEnd();
				isLoad	= true;
			};
			
			if(PERSONAS_VIVIENDA_MANUAL == true){
				ok		= true;
				if(typeof jsaGetMunicipios != "undefined"){ jsaGetMunicipios();	}
				if(typeof jsaGetLocalidades != "undefined"){ jsaGetLocalidades();	}				
			} else {
				ok		= xVal.NoCero(v);
				if(xPais == EACP_CLAVE_DE_PAIS && xCP != session(ID_CP_ACTUAL)){
					
					session(ID_CP_ACTUAL, xCP);
					if(xCP > 0 && isLoad == false){
						xG.pajax({
							url : "../svc/colonias.svc.php?limit=1&cp=" + xCP,
							finder : "codigo",
							result : "json",
							callback: postCP
						});
					}
				}
			}
			
			return ok;
		}
	
}
if(typeof XDate != "undefined"){
	XDate.defaultLocale = 'es';
}
FechaGen.prototype.get	= function(v){
	//Cambiar separadores
	var xDat	= new String(v);
	
	xDat		= new String(xDat.replace(/\//g, "-"));
	xDat		= xDat.split("-");
	var xF		= new XDate(FECHA_ACTUAL);
	var xG		= new Gen();
	if(typeof xDat[2] != "undefined"){
		if(entero(xDat[2]) > 100){
			var mm	= entero(xDat[1]) -1;
			var xF	= new XDate(entero(xDat[2]), mm, entero(xDat[0]));
		} else {
			var mm	= entero(xDat[1]) -1;
			var xF	= new XDate(entero(xDat[0]), mm, entero(xDat[2]));
		}
	} else {
		xG.alerta({msg : "Fecha invalida " + v});
	}
	return xF.toString("yyyy-MM-dd");
}
FechaGen.prototype.getRestarFechas	= function(FechaAnterior, FechaCalculo) {
	var xF		= new XDate(FechaAnterior);
	return xF.diffDays(FechaCalculo);
}
FechaGen.prototype.setSumarDias	= function(vFecha, days){
	var xF		= new XDate(vFecha);
	xF.addDays(days);
	return xF.toString("yyyy-MM-dd");
}
FechaGen.prototype.setRestarDias	= function(vFecha, days){
	var xF		= new XDate(vFecha);
	days		= days * -1;
	xF.addDays(days);
	return xF.toString("yyyy-MM-dd");
}

//------------------- MIGRACION
if (typeof goCredit_ == "undefined") {	var goCredit_ = function(){ var xP	= new PersGen(); xP.getBuscarCreditos();} }
if (typeof goCuentas_ == "undefined") {	var goCuentas_ = function(){ var xP	= new PersGen(); xP.getBuscarCuentas();} }
if (typeof goGrupos_ == "undefined") {	var goGrupos_ = function(){ var xP	= new PersGen(); xP.getBuscarGrupos();} }
//------------------- BANCOS
BanGen.prototype.setNuevoDeposito = function (opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	var monto		= (typeof opts.monto == "undefined") ? 0 : opts.monto;
	var cuenta		= (typeof opts.cuenta == "undefined") ? 0 : opts.cuenta;
	var documento	= (typeof opts.documento == "undefined") ? DEFAULT_CREDITO : opts.documento;
	var recibo		= (typeof opts.recibo == "undefined") ? DEFAULT_RECIBO : opts.recibo;
	var persona		= (typeof opts.persona == "undefined") ? DEFAULT_SOCIO : opts.persona;
	var fecha		= (typeof opts.fecha == "undefined") ? false : opts.fecha;
	var jscall		= (typeof opts.callback == "undefined") ? function(){} : opts.callback;
	var notas		= (typeof opts.observaciones == "undefined") ? "" : opts.observaciones;
	
	var url			= "../svc/bancos.add.deposito.svc.php?documento=" + documento + "&monto=" + monto + "&cuentabancaria=" + cuenta + "&recibo=" + recibo + "&persona=" + persona + "&idfechaactual=" + fecha + "&idobservaciones=" + notas;
	var xg			= new Gen();
	xg.pajax({
		url : url, result : "json",
		callback : function(data){
		    try { data = JSON.parse(data); } catch (e){}
		    if (typeof data != "undefined") {
				if (typeof data.error != "undefined") {
					if ($.trim(data.message) != "") {
						xg.alerta({ msg : data.message });
					}
					setTimeout(jscall,10);
				}				
		    } else {
				
			}
		}
	});
}
var CssGen	= function(){}
	
CssGen.prototype.get 	= function(stat){
		var obj = {background : "#ffecec", border : "#f5aca6"};
		switch(stat){
			case "vencido":
				obj = {background : "#ffecec", border : "#f5aca6"};
				break;
			case "cancelado":
				obj = {background : "#fff8c4", border : "#f2c779"};
				break;
			case "pendiente":
				obj = {background : "#e3f7fc", border : "#8ed9f6"};
				break;
			case "efectuado":
				obj = {background : "#e9ffd9", border : "#a6ca8a"};
				break;			
		}
		return obj;
	}

/*	this.PENDIENTE	= "pendiente";
	this.CANCELADO	= "cancelado";
	this.EFECTUADO	= "efectuado";
	this.VENCIDO	= "vencido";*/