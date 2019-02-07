var LocalFaker = function(){};

var arrTipoRAC	= [1,2,3,4];
var arrSNums	= ["0","1", "2","3","4","5", "6","7","8","9"];
var arrPlazos	= [12,24,36,48];
faker.locale 	= "es_MX";

var arrDecenas	= [10,20,30,40,50,60,70,80,90,100];
var arrCentenas	= [100,200,300,400,500,600,700,800,909,1000];
var arrCols		= [97000,24000,93000,77000];
var Tipos = {
	TEXTO : "TEXTO",
	MONEDA : "MONEDA"
};
LocalFaker.prototype.run = function(frm){
	var self	= this;
	
	switch(frm){
		case "frmcotizacion":
			self.setNuevaCotizacion();
			break;
		case "id-frmpreclientes":
			self.setNuevoPrecliente();
			break;
		case "frmsolicitudcredito":
			self.setNuevoCredito();
			break;
		case "id-frmperfiltransaccional":
			self.setNuevoPerfilTrans();
			break;
		case "id-frmsolingreso":
			self.setNuevoSolIngreso();
			break;
		case "id-frmvivienda":
			self.setNuevaVivienda();
			break;
		case "id-frmregistrogrupos":
			self.setNuevoGrupo();
			break;
		case "id-frmcreditos_lineas":
			self.setNuevaLinea();
			break;
		default:
			self.genIfExists("idobservaciones", Tipos.TEXTO);
			self.genIfExists("observaciones", Tipos.TEXTO);
			
			self.genIfExists("notas_de_checking", Tipos.TEXTO);
			self.genIfExists("acciones_tomadas", Tipos.TEXTO);
			self.genIfExists("razones_de_reporte", Tipos.TEXTO);
			
			
			
			alert("No hay Prueba para este Formulario");
			break;
	}
}
LocalFaker.prototype.setNuevaLinea	= function(){
	var fa	= $("#fecha_de_alta").val();
	
	var xF	= new FechaGen();
	var ff	= xF.setSumarDias(xF.get(fa), (365+365));
	var self= this;
	
	$("#idsocio").val();
		

	$("#fecha_de_vencimiento").val(ff);

	$("#oficial_de_credito").val( self.rndSel("oficial_de_credito") );
	var tt	= self.tasa();
	$("#tasa_ts").val( tt ); $("#tasa").val( tt );
	
	$("#periocidad").val( self.rndSel("periocidad") );
	
	
	
	
	var pp			= self.prestamo();
	pp				= pp * 5;
	var pp2			= pp * 2;
	
	$("#monto_linea_mny").val(pp);
	$("#monto_linea").val(pp);
	
	$("#monto_hipoteca").val(pp2);
	$("#monto_hipoteca_mny").val(pp2);
	
	$("#numerohipoteca").val( faker.internet.mac() );
	$("#observaciones").val( self.observaciones() );
	//$("#idcreditos_lineas").val(); 
}
LocalFaker.prototype.setNuevaVivienda	= function(){
	var self	= this;
	//self.rndSel("")
	
	$("#idregimendevivienda").val( self.rndSel("idregimendevivienda") );
	$("#idtiempo").val( self.rndSel("idtiempo") );
	//$("#idpais").val( );
	
	
	$("#idnombrecolonia").val( faker.address.city() );
	
	//$("#idtipoacceso").val();
	
	$("#idnombreacceso").val( faker.address.streetName() );
	
	$("#idnumeroexterior").val( faker.address.streetSuffix() );
	
	$("#idcodigopostal").val( self.rnd(arrCols) );
	
	//$("#idnumerointerior").val();
	
	//$("#identidadfederativa").val( self.rndSel("identidadfederativa") );
	$("#idtelefono1").val( faker.phone.phoneNumber() );
	$("#idtelefono2").val( faker.phone.phoneNumber() );
	//$("#idobservaciones").val();
	$("#idtipodevivienda").val( self.rndSel("idtipodevivienda") );
	
	
	
	
	
	
}
LocalFaker.prototype.setNuevoSolIngreso	= function(){
	var self	= this;
	//self.rndSel("")
	//$("#idfecharegistro").val();
	$("#idsucursal").val( self.rndSel("idsucursal") );
	$("#idtipodeingreso").val( self.rndSel("idtipodeingreso"));
	

	
	$("#idnombrecompleto").val( faker.name.firstName() );
	$("#idapellidopaterno").val( faker.name.lastName() );
	$("#idapellidomaterno").val( faker.name.lastName() );
	
	$("#idgenero").val( self.rndSel("idgenero") );
	
	//$("#idpaisdeorigen").val( self.rndSel("idpaisdeorigen") );
	
	$("#idfechanacimiento").val();
	
	$("#identidadfederativanacimiento").val( self.rndSel("identidadfederativanacimiento") );
	
	//$("#idlugardenacimiento").val();
	
	$("#idemail").val( faker.internet.email() );
	$("#idtelefono").val( faker.phone.phoneNumber() );
	
	$("#idprofesion").val( faker.name.jobTitle() );
	
	$("#iddependientes").val( self.rnd(arrTipoRAC) );
	
	$("#idestadocivil").val( self.rndSel("idestadocivil") );
	$("#idregimenmatrimonial").val( self.rndSel("idregimenmatrimonial") );
	$("#idregimenfiscal").val( self.rndSel("idregimenfiscal") );
	$("#idtipoidentificacion").val( self.rndSel("idtipoidentificacion") );
	
	$("#idnumerodocumento").val( faker.internet.mac() );
	
	
	var idnombrecompleto	= $("#idnombrecompleto").val();
	var idapellidopaterno	= $("#idapellidopaterno").val();
	var idapellidomaterno	= $("#idapellidomaterno").val();
	var idgenero			= $("#idgenero").val();
	var idfechanacimiento	= $("#idfechanacimiento").val();
	var identidadfederativa	= $("#identidadfederativanacimiento").val();

	if( EACP_CLAVE_DE_PAIS == "MX"){
		var xMx	= new Mexico();
		if (String($("#idcurp").val() ).length > 10) {
			//code
		} else {
			var mCurp		= xMx.jsGetCURP(idnombrecompleto, idapellidopaterno, idapellidomaterno, idfechanacimiento, idgenero, identidadfederativa );
			mCurp			= mCurp + self.homonimia();
			$("#idcurp").val( mCurp );
			$("#idrfc").val( String(mCurp).substring(0,10) );
		}
	}
	
			
	//$("#idcurp").val();
	//$("#idrfc").val();
	
	$("#idclavefiel").val( faker.random.uuid() );
	
	//$("#idrazonnofiel").val();
	//$("#idobservaciones").val();
	
	$("#idempresa").val( self.rndSel("idempresa") );
	
	//$("#idcajalocal").val();
	$("#idinterno").val(faker.random.alphaNumeric() );
	//$("#idfigurajuridica").val( self.rndSel("idfigurajuridica") );
	//$("#idgrupo").val( self.rndSel("idgrupo") );
	
	//$("#tipoorigen").val();
	//$("#claveorigen").val();	
}
LocalFaker.prototype.rnd = function(arr){
	var rand = arr[Math.floor(Math.random() * arr.length)];
	//console.log(rand);
	return rand;
}
LocalFaker.prototype.rndSel = function(id){
	var self		= this;
	var rnx			= false;
	
	if(document.getElementById(id)){
		
		var x 			= document.getElementById(id);
		if( $(x).is("selected") ){
			var optionVal 	= new Array();
			for (i = 0; i < x.length; i++) { 
				optionVal.push(x.options[i].value);
			}
			var rnx			= self.rnd(optionVal);
		} else {
			var rnx 		= $(x).val();
		}

	}
	
	console.log("El Control " + id + " devuelve " + rnx);
	
	return rnx;

}
LocalFaker.prototype.nombreEntero	= function(){
	return faker.fake("{{name.lastName}} {{name.lastName}} {{name.firstName}}");
}
LocalFaker.prototype.prestamo	= function(){
	var self	= this;
	//var pp	= faker.fake("{{commerce.price}}");
	//var pp2	= Math.floor((Math.random() * 1000) + 100);
	//var pp		= 
	var pp		= self.rnd(arrDecenas);
	var pp2		= self.rnd(arrCentenas);
	pp			= pp * pp2;
	return pp;
}
LocalFaker.prototype.homonimia	= function(){
	var self	= this;

	var pp		= self.rnd(arrSNums);
	var pp2		= self.rnd(arrSNums);
	pp			= pp + pp2;
	return pp;
}
LocalFaker.prototype.pagos	= function(pers){
	//var pp	= faker.fake("{{random.number}}");
	maxx		= entero((360 / pers));
	var pp2		= Math.floor((Math.random() * 3) + 1);
	var pp		= Math.floor((Math.random() * maxx) + 2);
	var pp		= pp*pp2;
	//var pp2	= Math.floor((Math.random() * 1000) + 100);
	//pp		= pp * pp2;
	return pp;
}
LocalFaker.prototype.tasa	= function(){
	//var pp	= faker.fake("{{random.number}}");
	
	var pp		= Math.floor((Math.random() * 6) + 1);
	var pp		= pp*10;
	//var pp2	= Math.floor((Math.random() * 1000) + 100);
	//pp		= pp * pp2;
	return pp;
}
LocalFaker.prototype.observaciones = function(){
	return faker.random.words();
}
LocalFaker.prototype.setNuevaCotizacion = function(){
	var self	= this;
	
	$("#nombre_cliente").val( self.nombreEntero() );
	$("#nombre_atn").val( faker.name.findName() );
	$("#tel").val( faker.phone.phoneNumber() );
	$("#mail").val( faker.internet.email() );
	$("#tipo_rac").val( self.rndSel("tipo_rac") );
	
	
	
	$("#marca").val( self.rndSel("marca") );
	$("#tipo_uso").val( self.rndSel("tipo_uso") );
	$("#segmento").val( self.rndSel("segmento") );
	
	$("#modelo").val( faker.lorem.words() );
	$("#annio").val( faker.lorem.word() );
	
	var pp			= self.prestamo();
	$("#precio_vehiculo_mny").val( pp );
	$("#precio_vehiculo").val( pp );
	
	//$("#monto_anticipo_mny").val();
	//$("#monto_anticipo").val();
	
	$("#entidadfederativa").val( self.rndSel("entidadfederativa") );
	$("#plazo").val( self.rndSel("plazo"));
	$("#precio_vehiculo_mny").trigger("blur");


}
LocalFaker.prototype.setNuevoCredito = function(){
	var self	= this;
	$("#idproducto").val( self.rndSel("idproducto") );
	$("#idperiocidad").val( self.rndSel("idperiocidad") );
	$("#idtipodepago").val( self.rndSel("idtipodepago") );
	$("#idtipolugarcobro").val( self.rndSel("idtipolugarcobro") );
	
	$("#idnumerodepagos").val(self.pagos( $("#idperiocidad").val() ));
	
	var pp			= self.prestamo();
	
	$("#idmonto").val(pp);
	
	
	$("#iddestinodecredito").val( self.rndSel("iddestinodecredito") );
	$("#iddescripciondestino").val( faker.random.words() );
//self.rndSel()
	$("#oficial").val( self.rndSel("oficial") );
	$("#idobservaciones").val( faker.random.words() );

}
LocalFaker.prototype.setNuevoPrecliente = function(){
	var self	= this;
	
	
$("#nombres").val( faker.name.firstName() );
$("#apellido1").val( faker.name.lastName() );
$("#apellido2").val( faker.name.lastName() );

//$("#rfc").val();
//$("#curp").val();

$("#email").val( faker.internet.email() );
$("#telefono").val( faker.phone.phoneNumber() );

$("#producto").val( self.rndSel("producto") );
$("#aplicacion").val( self.rndSel("aplicacion") );

$("#periocidad").val( self.rndSel("periocidad") );

var pp			= self.prestamo();

$("#monto_mny").val(pp);
$("#monto").val(pp);

$("#tasa_interes").val( self.tasa() );
$("#pagos").val( self.pagos( $("#periocidad").val()  ) );



$("#notas").val( faker.lorem.words() );
}

LocalFaker.prototype.setNuevoPerfilTrans	= function(){
	var self	= this;
	//self.rndSel("")
	$("#idpais").val( self.rndSel("idpais") );
	$("#idtipotransaccion").val( self.rndSel("idtipotransaccion") );
	$("#idmonto").val( self.prestamo());
	$("#idnumero").val( self.pagos() );
	$("#idorigen").val( faker.random.words()  );
	$("#idfinalidad").val( faker.random.words()  );
	$("#idobservaciones").val( faker.random.words()  );
}

LocalFaker.prototype.setNuevoGrupo = function(){
	var self	= this;
	
	//$("#idsucursal").val();
	
	$("#idrazonsocial").val( faker.company.companyName() );
	
	//$("#idfechanacimiento").val();
	
	$("#identidadfederativanacimiento").val( self.rndSel("identidadfederativanacimiento") );
	
	$("#idlugardenacimiento").val( faker.address.state() );
	
	$("#idemail").val( faker.internet.email() );
	$("#idtelefono").val( faker.phone.phoneNumber() );
	
	$("#idobservaciones").val( faker.random.words() );
	
	//$("#idsocio2").val();
	//$("#idsocio3").val();
	//$("#idsocio4").val();
	//$("#idsocio5").val();
	//$("#idsocio6").val();
	//$("#idsocio7").val();
	//$("#idsocio8").val();
	//$("#idsocio9").val();
}
LocalFaker.prototype.genIfExists	= function(id, tipo){
	if(document.getElementById(id)){
		switch(tipo){
			case Tipos.TEXTO:
				$("#" + id).val(faker.random.words());
				break;
		}
	}
}