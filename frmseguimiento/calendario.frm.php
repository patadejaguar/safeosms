<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 0.0.01
 * @package
 */
//=====================================================================================================
include_once("../core/go.login.inc.php");
include_once("../core/core.error.inc.php");
include_once("../core/core.html.inc.php");
include_once("../core/core.init.inc.php");
include_once("../core/core.db.inc.php");
$theFile			= __FILE__;
$permiso			= getSIPAKALPermissions($theFile);
if($permiso === false){	header ("location:../404.php?i=999");	}
$_SESSION["current_file"]	= addslashes( $theFile );
//=====================================================================================================
$xHP		= new cHPage("TR.CALENDARIO DE SEGUIMIENTO_DE_CARTERA", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xChk		= new cHCheckBox();

//$jxc = new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto		= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT);
$recibo		= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa	= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT);
$grupo		= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria = parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);

$observaciones= parametro("idobservaciones");

$xHP->addJsFile("../js/fullcalendar.min.js");
$xHP->addJsFile("../js/lang/es.js");
$xHP->addCSS("../css/fullcalendar.min.css");

$xHP->init();

$xFRM		= new cHForm("frmcalendarioseg", "./");
$xFRM->addJsInit("init();");
$xFRM->setTitle($xHP->getTitle());
$xFRM->addCerrar();


$xChk->addEvent("jsRefreshCalendar()", "onclick");
$xFRM->addHElem($xChk->get("TR.MOSTRARTODO", "idtodo", true));
$xFRM->addHElem("<div id='calendario'></div>");
//$xFRM->addJsBasico();
//$xFRM->addCreditBasico();
//$xFRM->addSubmit();

echo $xFRM->get();

//$jxc ->drawJavaScript(false, true);
?>
<script>
var mFecha	= FECHA_ACTUAL;
var xSeg	= new SegGen();
var xG		= new Gen();
var xCss	= new CssGen();
var jsCache = {};
function init(){jsRunCalendar();}
function jsProcesarCompromisos(data){
	var fin 	= Object.keys(data).length;
	$.each( data, function( key, val ) {
		var compromiso	= val;
		if(typeof compromiso != "undefined"){
			//console.log(compromiso.estatus);
			var css		= xCss.get(compromiso.estatus);
			var evt 	= {
						title: xG.moneda(compromiso.monto) + "-" + compromiso.nombre , start: compromiso.fecha + "T" + compromiso.hora, backgroundColor: css.background, 
						icon: "fa-user",
						textColor: "black", 
						borderColor: css.border, 
						allDay:false,
						info: compromiso.notas, id: compromiso.clave, credito:compromiso.credito, persona:compromiso.codigo, tipo:"compromiso" };
			$('#calendario').fullCalendar( 'renderEvent', evt, true);
		}
	});	
}
function jsProcesarNotificaciones(data){
	var fin 	= Object.keys(data).length;
	$.each( data, function( key, val ) {
		var notificacion	= val;
		if(typeof notificacion != "undefined"){
			var css		= xCss.get(notificacion.estatus);
			//console.log(notificacion.estatus);
			var evt 	= {
						title: notificacion.nombre, 
						start: notificacion.fecha + "T" + notificacion.hora,
						backgroundColor: css.background,
						icon: "fa-briefcase", 
						textColor: "black", 
						borderColor: css.border,
						allDay:false,
						info: notificacion.notas, id:notificacion.clave, credito: notificacion.credito, persona:notificacion.codigo, tipo:"notificacion" };
			$('#calendario').fullCalendar( 'renderEvent', evt, true);
		}
	});	
}
function jsProcesarLLamadas(data){
	var events 	= new Array();
	//JSON.parse(data);
	var fin 	= Object.keys(data).length;
	$.each( data, function( key, val ) {
		var llamada	= val;
		//{"codigo":"1901550","nombre":"","credito":"290155006","clave":"243452","fecha":"2015-07-03","hora":"06:00:00","estatus":"efectuado","notas":""}
		if(typeof llamada != "undefined"){
			var css		= xCss.get(llamada.estatus);
			var evt 	= {
						title: llamada.nombre, 
						start: llamada.fecha + "T" + llamada.hora, 
						backgroundColor: css.background,
						
						icon: "fa-phone",
						textColor: "black", 
						borderColor: css.border, 
						allDay : false,
						info: llamada.notas, id:llamada.clave, credito: llamada.credito, persona : llamada.codigo, tipo:"llamada"};
			$('#calendario').fullCalendar( 'renderEvent', evt, true);
		}
	});	
}
function jsRefreshCalendar(){
	//$('#calendario').empty();
	$('#calendario').fullCalendar('viewRender');
	//setLog("nada");
}
function jsRunCalendar(){
	
	
	$('#calendario').fullCalendar({
		disableDragging: true,

		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,list'
		},
	    eventRender: function(event, element) {
	    	if (event.icon){
	    	   element.find("div.fc-content").prepend("<i class='fa " + event.icon +"'></i>");
	    	   element.find(".fc-list-item-marker").empty();
	    	   element.find(".fc-list-item-marker").prepend("<i class='fa " + event.icon +"'></i>");
	    	}
	    	if(event.backgroundColor){
	    		element.find(".fc-list-item-title").css("background-color", event.backgroundColor);
		    	//fc-list-item-title fc-widget-content
		    }
	        //if(event.imageurl) {
	            //eventElement.find("div.fc-content").prepend("<img src='" + event.imageurl +"' width='12' height='12'>");
	        //}
	        //element.find(".fc-event-time").after($("<span class=\"fc-event-icons\"></span>").html("Whatever you want the content of the span to be"));
	        //event.backgroundColor = "#510814";
	        //console.log(event.backgroundColor);
	        
	    	if(event.info){
		    	element.attr('title', event.info); 
		    }
	    },		
		editable: false,
		eventLimit: true, // allow "more" link when too many events
		events: [],
		viewRender : function(view, element){
			var idtodas	= $('#idtodo').prop('checked');
			
			if(view.name == "month"){
				//xG.spinInit();
				var fi	= view.start.toISOString();
				var ff	= view.end.toISOString();
				$('#calendario').fullCalendar( 'removeEvents' );
				
				xSeg.getListaDeLlamadas({callback:jsProcesarLLamadas , fecha : fi, fechaFinal : ff, todo:idtodas});
				xSeg.getListaDeCompromisos({callback:jsProcesarCompromisos , fecha : fi, fechaFinal : ff, todo:idtodas});
				xSeg.getListaDeNotificaciones({callback:jsProcesarNotificaciones , fecha : fi, fechaFinal : ff, todo:idtodas});
			}
			if(view.name == "list"){
//				element.find(".fc-list-item-title").text( $(this).attr("info")  + "----" + $(this).attr("title") );
/*element.find(".fc-list-item-title").each(function(idx, elm){
	
	$(elm).attr("text", "miau");
	//console.log(evt);
}).end();*/
			}
		},
	    eventClick: function(calEvent, jsEvent, view) {
	    	if(view.name == "month"){
		    	
	    	} else {
   				var vURL	= "";
   				
	   			switch(calEvent.tipo){
	   				case "llamada":
	   					xSeg.setLlamadaEstado({clave : calEvent.id });
	   	   			break;
	   				case "compromiso":
	   	   				xSeg.getDetalleDeCompromiso({clave : calEvent.id});
	   	   				break;
	   				case "notificacion":
		   				xSeg.getFormaNotificacion({clave: calEvent.id});
	   	   				break;
	   			}
	    	}
	    },	    
	    dayClick: function(date, jsEvent, view) {
			$('#calendario').fullCalendar('changeView', 'list', date.format() );
		}
	});	
}
</script>
<?php
$xHP->fin();
?>