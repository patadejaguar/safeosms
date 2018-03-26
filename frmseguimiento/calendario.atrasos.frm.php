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
$xHP		= new cHPage("TR.CALENDARIO DE MORA", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();

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



$xFRM		= new cHForm("frm", "./");
$xFRM->setTitle($xHP->getTitle());
$xFRM->addCerrar();

$xFRM->addJsInit("init();");
$xFRM->addHElem("<div id='calendario'></div>");
//$xFRM->addJsBasico();
//$xFRM->addCreditBasico();
//$xFRM->addSubmit();

echo $xFRM->get();
$xSuc		= new cSucursal();

//$jxc ->drawJavaScript(false, true);
?>
<script>
var mFecha	= FECHA_ACTUAL;
var xSeg	= new SegGen();
var xG		= new Gen();
var xCss	= new CssGen();
var xCred	= new CredGen();

var HrInit	= "<?php echo sprintf("%'.02d", $xSuc->getHorarioDeEntrada()); ?>";
var jsCache = {};
function init(){jsRunCalendar();}

function jsProcesarAtrasos(data){
	var events 	= new Array();
	//JSON.parse(data);
	//var fin 	= Object.keys(data).length;
	var iters	= null;
	var icnt	= 0;
	var imt		= 300;
	moment.utc();
	//var vvts	= [];
	//console.log(data);
	//console.log("loading..");
	//try { data = JSON.parse(data); } catch (e){}
	
	$.each( data, function( key, val ) {
		var atraso	= val;

		if(typeof atraso != "undefined"){
			var	xF		= new moment(atraso.fecha_de_pago + "T"+ HrInit + ":00:00");
			var segs	= icnt * imt;
			xF.add(segs, "seconds");
						
			var xInfo	= "Fecha : " + atraso.fecha_de_pago   + " - " + xG.lang("parcialidad") + " # " + atraso.parcialidad + " " + xG.lang("monto") + " : " + atraso.letra;
			var css		= xCss.get("vencido");
			var xTitle	= atraso.nombre + " - $ " + getFMoney(atraso.letra);	
			/*icon: "fa-info",*/
			var evt 	= {
						title: xTitle,
						start: xF,
						end:xF.seconds(imt),
						info : xInfo,
						textColor: "black",
						allDay : false,
						borderColor: xCss.border,
						backgroundColor: css.background,
						credito : atraso.credito,
						persona : atraso.persona,
						periodo : atraso.parcialidad
						};
			$('#calendario').fullCalendar( 'renderEvent', evt, true);
			icnt++;
			
			//setLog(iters);
			//vvts.push(evt);
		}
	});
	xG.spinEnd();
	//$('#calendario').fullCalendar( 'renderEvents', vvts, true);
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
	    	}
	    	if(event.info){ element.attr('title', event.info); }
	    },		
		editable: false,
		eventLimit: true, // allow "more" link when too many events
		events: [],
		viewRender : function(view, element){
			
			switch(view.name){
				case "month":
					var fi	= view.start.toISOString();
					var ff	= view.end.toISOString();
					$('#calendario').fullCalendar( 'removeEvents' );
					
					var vURL		= "../svc/creditos.atrasos.svc.php?acumulado=true&fecha=" + fi +"&fechafinal=" + ff;
					//setLog(vURL);
					$.cookie.json 	= true;
					$.getJSON( vURL, function(data){
						if (typeof data == "undefined") {
							xG.alerta({msg : "Error al procesar el registro"});
						} else {
							var fin 	= Object.keys(data).length;
							$.each( data, function( key, val ){
								var atraso	= val;
								var css		= xCss.get("vencido");
								if(typeof atraso != "undefined"){
									var evt 	= { title: atraso.pagos + " $ " + atraso.monto, start: atraso.fecha_de_pago, icon: "fa-money", textColor: "black", allDay : true, borderColor: xCss.border,backgroundColor: css.background };
									$('#calendario').fullCalendar( 'renderEvent', evt, true);
								}
							});							
						}
					});
				break;
				case "list":
					
					var fi	= view.start.toISOString();
					var ff	= view.end.toISOString();
					$('#calendario').fullCalendar( 'removeEvents' );
					xG.spinInit();
					xSeg.getListaDeAtrasos({callback:jsProcesarAtrasos , fecha : fi, fechaFinal : ff});
					break;
			}
		},
	    eventClick: function(calEvent, jsEvent, view) {
	    	switch(view.name){
	    		case "list":
		    		var cred	= calEvent.credito;
		    		xCred.goToPanelControl(cred);
		    	break;
	    	}
	    },
	    dayClick: function(date, jsEvent, view) {
	        $('#calendario').fullCalendar('changeView', 'list', date.format() );
	   },
	   displayEventTime: false	
	});	
}
</script>
<?php
$xHP->fin();
?>