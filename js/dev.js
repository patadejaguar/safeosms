//EACP_CLAVE_DE_PAIS
var SafeDev = function(){}

SafeDev.prototype.clearCSS	= function(){
	//var elementCount = $( "#test" ).find( "*" ).css( "border", "3px solid red" ).length;
	//$( "body" ).prepend( "<h3>" + elementCount + " elements found</h3>" );
	var bd	= $( "body" );
	bd.find("*").each(function() {
		  $(this).removeClass();
		  $(this).removeAttr( "style" );
		  $(this).removeAttr( "face" );
		  $(this).removeAttr( "width" );
		  $(this).removeAttr( "cellpading" );
		  $(this).removeAttr( "cellspacing" );
	});
	bd.append("<textarea id='msrc'></textarea>")
	$("#msrc").val(bd.html());
}

SafeDev.prototype.reload	= function(){
	window.setInterval("location.reload()", 2500);
}

SafeDev.prototype.recordRAW	= function(opts){
		opts		= (typeof opts == "undefined") ? {} : opts;
	var tbl		= (typeof opts.tabla == "undefined") ? "" : opts.tabla;
	var id		= (typeof opts.id == "undefined") ? "" : opts.id;
	var callB	= (typeof opts.callback == "undefined") ? "" : opts.callback;
	var xG		= new Gen();
	xG.w({url: "../tools/json.dev.php?tabla=" + tbl + "&id=" + id, tiny: true});
}

function serializeForm(idfrm) {
	var fields = $(idfrm).serializeArray();
	var txt		= "";
	var jsq		= "";
	var jsq2	= "";

	var total = fields.length;

	jQuery.each( fields, function( i, field ) {
		str		= '$' + field.name  + '\t= parametro(\"' + field.name +  '\"';
		//console.log(field)
		str		+= ');\n<br />';
		txt		+= str;
		jsq		+= 'var ' + field.name  + '\t= $("#' + field.name  + '").val();\n<br/>';
		jsq2		+= '$("#' + field.name  + '").val();\n<br/>';
		if (i === (total - 1) ) {
			session("var.serialize.php", txt);
			session("var.serialize.js", jsq + jsq2);
			
			var xG		= new Gen();
			xG.w({ url : "../tools/serialize.dev.php?", full:true });
		}
	});
}

