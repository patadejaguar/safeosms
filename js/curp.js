function getCURP(nombre, paterno, materno, fecha, genero, estado) {
	var mNombre = new String(nombre);
	var mPaterno = new String(paterno);
	var mFecha = new String(fecha);
	var mMaterno = new String(materno);
	var curp = new String();
	//quitar espacios externos
	mNombre = mNombre.replace(/^\s+|\s+$/, '');
	mPaterno = mPaterno.replace(/^\s+|\s+$/, '');
	mMaterno = mMaterno.replace(/^\s+|\s+$/, '');

	//quitar articulos de apellidos
	pat_temp = mPaterno
			.replace(/\b(de(l)?|la(s)?|los|y|a|mac|von|van)\s+/i, '');
	pat_temp = pat_temp.replace(/\bmc/, '');
	mat_temp = mMaterno
			.replace(/\b(de(l)?|la(s)?|los|y|a|mac|von|van)\s+/i, '');
	mat_temp = mat_temp.replace(/\bmc/, '');

	//quitar mNombres comunes, solo si no van solos, ademas de articulos
	nom_temp = mNombre.replace(/\b(j(ose|\.)?|ma(ria|\.)?)\s+/i, '');
	nom_temp = nom_temp
			.replace(/\b(de(l)?|la(s)?|los|y|a|mac|von|van)\s+/i, '');
	nom_temp = nom_temp.replace(/\bmc/, '');

	//empezar a construir curp con inicial mPaterno + primera vocal mPaterno + inicial mMaterno + inicial mNombre
	//var xItel   =
	/** @version 1.PACTH_BALAM */
	//var curp    = pat_temp.charAt(0) + pat_temp.substring(1).match(/[aeiou]/i);
	curp = pat_temp.charAt(0) + pat_temp.substring(1).match(/[aeiou]/i);
	curp += mat_temp.charAt(0);
	curp += nom_temp.charAt(0);

	var malas = Array("BUEI", "BUEY", "CACA", "CACO", "CAGA", "CAGO", "CAKA",
			"CAKO", "COGE", "COJA", "KOGE", "KOJO", "KAKA", "KULO", "MAME",
			"MAMO", "MEAR", "MEAS", "MEON", "MION", "COJE", "COJI", "COJO",
			"CULO", "FETO", "GUEY", "JOTO", "KACA", "KACO", "KAGA", "KAGO",
			"MOCO", "MULA", "PEDA", "PEDO", "PENE", "PUTA", "PUTO", "QULO",
			"RATA", "RUIN");

	//si se encuentra una mala palabra, sustituir la segunda letra con 'X'
	if (curp.match(malas.join('|')))
		curp = pat_temp.charAt(0) + 'X' + mat_temp.charAt(0)
				+ nom_temp.charAt(0);
	var vFechas = mFecha.split("-");
	//Fecha de Nacimiento
	//Anno
	var aNac = new String(vFechas[0]);
	if (aNac.length <= 1) {
		aNac = "0" + aNac;
	}
	//Mes de Nacimiento
	var mNac = new String(vFechas[1]);
	if (mNac.length <= 1) {
		mNac = "0" + mNac;
	}
	//Anno de Nacimiento
	var dNac = new String(vFechas[2]);
	if (dNac.length <= 1) {
		dNac = "0" + dNac;
	}

	curp += aNac.substr(-2, 2);
	curp += mNac.substr(-2, 2);
	curp += dNac.substr(-2, 2);
	//Genero
	curp += genero;
	//Estado de Nacimiento
	curp += estado;

	//Consonantes de los apellidos + nopmbres

	var mConsAP = mPaterno.replace(/[aeiou]/gi, "");
	curp += mConsAP.substring(1, 2);

	var mConsAM = mMaterno.replace(/[aeiou]/gi, "");
	curp += mConsAM.substring(1, 2);

	var mConsN = mNombre.replace(/[aeiou]/gi, "");
	curp += mConsN.substring(1, 2);

	curp = curp.toUpperCase();

	return curp;
}
