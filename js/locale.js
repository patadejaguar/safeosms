//EACP_CLAVE_DE_PAIS
var LocalGen	= function(){};

LocalGen.prototype.validarDNI   = function(dni){
    var xPais   = (typeof EACP_CLAVE_DE_PAIS == "undefined") ? "MX" : EACP_CLAVE_DE_PAIS;
    var xLargo	= 18;
	var valid	= true;
	var xDNI	= new String(dni);
	switch(xPais){
        case "HN":
			if(xDNI.length == PERSONAS_LARGO_IDPOBLACIONAL){ valid = true; } else { valid = false; }
            break;		
	}
	return valid;
}
/**
 * Numero de Identidad Fiscal
 **/
LocalGen.prototype.validarNIF   = function(nif, EsNatural){
    EsNatural   = (typeof EsNatural == "undefined") ? true : false;
    var xPais   = (typeof EACP_CLAVE_DE_PAIS == "undefined") ? "MX" : EACP_CLAVE_DE_PAIS;
    var xLargo	= 13;
	var valid	= true;
	var xNIF	= new String(nif);
    switch(xPais){
        case "HN":
			if(xNIF.length == PERSONAS_LARGO_IDFISCAL){ valid = true; } else { valid = false; }
            break;
    }
    return valid;
}
function validate_DNI(dni)
{
    // Comprobamos si tiene longitud 9
    if(dni.length == 9)
    {
        // Extraemos los 8 primeros caracteres
        numbers_DNI = dni.substring(0,8);
 
        // Función que comprueba si un número es un
        // entero no negativo
        var isInteger = function(n)
        {
            var intRegex = /^\d+$/;
            if(intRegex.test(n)) return true;
            return false;
        }
 
        // Comprobamos si los 8 primeros caracteres
        // son números
        if(!isInteger(numbers_DNI)) return false;
 
        // Extraemos el último caracter
        letter_DNI = dni.substring(8);
 
        // Función que hemos elaborado antes para
        // el cálculo de la letra
        var get_letter_DNI = function(dni)
        {
            var table = "TRWAGMYFPDXBNJZSQVHLCKE";
            return table.charAt(dni % 23);
        }
 
        // Calculamos la letra de las cifras que se
        // han introducido
        letter_calculated = get_letter_DNI(numbers_DNI);
 
        // Si la letra es correcta damos por válido el DNI
        if(letter_calculated == letter_DNI) return true;
    }
 
    return false;
}
