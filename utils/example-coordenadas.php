<?php
/*
 * Created on 18/07/2008
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html  xml:lang="es">
<head>
<title>Obtener coordenadas de google maps</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="Pragma" content="no-cache" />
<meta name="Author" content="Agencia Creativa Comunicaci�n Multimedia S.L.L." />
<meta name="Description" content="obtener coordenadas de google maps de un punto, buscas la direcci�n y pinchas sobre el mapa para obtener o conseguir las coordenadas"  />
<meta name="keywords" content="obtener coordenadas, maps, google maps, yahoo maps, conseguir coordenadas"  />
<meta name="Author" content="Agencia Creativa Comunicaci�n Multimedia S.L.L." />
<!-- localhost -->
<!-- <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAGAEGVZIXwwutieHF4QOL4RTO1SqiNHIgnBS6h2WJCouEneHWvxTQIpVD2BE9UfN5OoH45QbZmveqEw"
      type="text/javascript"></script> -->
<!-- servidor -->
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAGAEGVZIXwwutieHF4QOL4RRpYoRN0FKJQ7RZd_AzeNEq2PLjZhR3W4-qrIA2KyJ0hRw-barmpbORVQ"
      type="text/javascript"></script>
    <script type="text/javascript">
    //<![CDATA[

	// Inicializaci�n de variables.
    var map      = null;
    var geocoder = null;

    function load() {                                      // Abre LLAVE 1.
      if (GBrowserIsCompatible()) {						   // Abre LLAVE 2.
        map = new GMap2(document.getElementById("map"));

        map.setCenter(new GLatLng(37.567236,-1.803499), 15);
        map.addControl(new GSmallMapControl());
	   	map.addControl(new GMapTypeControl());

        geocoder = new GClientGeocoder();

        //---------------------------------//
        //   MARCADOR AL HACER CLICK
		//---------------------------------//
		GEvent.addListener(map, "click",
			function(marker, point) {
 		 		if (marker) {
               		null;
              		} else {
          			map.clearOverlays();
					var marcador = new GMarker(point);
					map.addOverlay(marcador);
					//marcador.openInfoWindowHtml("<b><br>Coordenadas:<br></b>Latitud : "+point.y+"<br>Longitud : "+point.x+"<a href=http://www.mundivideo.com/fotos_pano.htm?lat="+point.y+"&lon="+point.x+"&mapa=3 TARGET=fijo><br><br>Fotografias</a>");
					//marcador.openInfoWindowHtml("<b>Coordenadas:</b> "+point.y+","+point.x);
					document.form_mapa.coordenadas.value = point.y+","+point.x;
					}
  			}
			);
        //---------------------------------//
        //   FIN MARCADOR AL HACER CLICK
		//---------------------------------//

      } // Cierra LLAVE 1.
    }   // Cierra LLAVE 2.

    //---------------------------------//
    //           GEOCODER
	//---------------------------------//
    function showAddress(address, zoom) {
    	if (geocoder) {
        	geocoder.getLatLng(address,
          		function(point) {
            		if (!point) {
            			alert(address + " not found");
            		} else {
            			map.setCenter(point, zoom);
            			var marker = new GMarker(point);
            			map.addOverlay(marker);
            			//marker.openInfoWindowHtml("<b>"+address+"</b><br>Coordenadas:<br>Latitud : "+point.y+"<br>Longitud : "+point.x+"<a href=http://www.mundivideo.com/fotos_pano.htm?lat="+point.y+"&lon="+point.x+"&mapa=3 TARGET=fijo><br><br>Fotografias</a>");
       			     // marker.openInfoWindowHtml("<b>Coordenadas:</b> "+point.y+","+point.x);
       			      document.form_mapa.coordenadas.value = point.y+","+point.x;
               		}
               	}
        	);
      	}}
    //---------------------------------//
    //     FIN DE GEOCODER
	//---------------------------------//

    //]]>
     </script>

     </head>
     <body onload="load();"  onunload="GUnload();">
     <h1 style="border: 1px solid #CCC;background-color: #EEE;color: #999;font-family: verdana;">OBTENER COORDENADAS DE UN PUNTO EN GOOGLE MAPS</h1>

     <form name="form_mapa" action="#" onsubmit=" showAddress(this.address.value, this.zoom.value=parseFloat(this.zoom.value)); return false">


      	<p style="font-size: 10px;font-family: verdana;font-weight: bold;">Direcci�n a buscar: <input type="text" name="address" value="" style="width: 400px;font-size: 10px;font-family: verdana;font-weight: bold;" />

     	<input type="hidden" size="1" name="zoom" value=15 />

        <input type="submit" value="Ver" /></p>
        <p style="font-size: 10px;font-family: verdana;font-weight: bold;">Coordenadas: <input type="text" name="coordenadas" value="" style="width: 400px;font-size: 10px;font-family: verdana;font-weight: bold;" />
        </p>

      </form>
<div style="width: 700px; border-width: 1px; border-style: solid; border-color: #979797; padding:8px 8px 8px 8px;">
        <div id="map" style="width: 700px; height: 500px"></div>
       	</div>

 		<form name="form_mapa_1" action="#" onclick=" showAddress(this.address.value, 15); return false">

 		</form>

 		<p style="border: 1px solid #CCC;background-color: #EEE;color: #999;font-family: verdana;"><a href="http://www.agenciacreativa.net" style="text-decoration: none;color: #000;text-align: center;" target="_blank">http://www.agenciacreativa.net</a></p>

     </body>
     </html>