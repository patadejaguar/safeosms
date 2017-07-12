/*
 * @author Luis Balam
 * @description DDL is DDL util class for SQLite, make easy SQL
 * @version  0.9.45
 */

var cnx		= {
	open : false,
	cnn	: false,
	get : function(){ return this.cnn; }
	};

var DDL		= function(){
	this._db_name		= "DroidMart";			//change the name database
	this._db_size		= 4*1024*1024;				//change the size database //4MB
	this._db_title		= "DroidMart Database";	//
	this._db_version	= "1.0";				//version of the database
	};
DDL.prototype.cnn = function(){
	var db      	= false;
	if(window.openDatabase){
		if ( cnx.open == true ) {
			db			= cnx.get();
		} else {
			db			= window.openDatabase(this._db_name, this._db_version, this._db_title, this._db_size);
			cnx.cnn		= db;
			cnx.open	= true;
		}
		//console.log(cnx.open);
	}
	return db;
}	
DDL.prototype.create	= function(obj, run){
	//obj		= {};
//	console.log(Object.keys(obj).length);
	var mm		= obj.getFields;
	strCR		= "";
	strCR		+= "CREATE TABLE IF NOT EXISTS \"" + obj.name + "\" (";
	
	for( i=0; i<mm.length; i++){
		var _mobj	= obj[ mm[i] ];
		if( _mobj.name){
			strCR 	+= "\"" + _mobj.name + "\" ";
			strCR 	+= "" + _mobj.type + " ";
			strCR 	+= (typeof _mobj.size != "undefined") ? "(" + _mobj.size + ") " : "";
			strCR 	+= (typeof _mobj.def != "undefined" ) ? " DEFAULT '" + _mobj.def + "'" : "";
			strCR 	+= ", ";
		}
	}
	//primary KEY
	strCR 	+= " PRIMARY KEY (\"" + obj.primaryKey + "\")";
	strCR 	+= ") ";
	//if options is run()
	if(typeof run != "undefined"){
		this.sendQuery(obj, { sql : strCR });
	}
	return	strCR;
}
DDL.prototype.select	= function(obj, opts){
	(typeof opts == "undefined") ? opts = {} : null;
	//where limit key
	var strLimit	= "";
	var strWhere	= "";
	var closedVals	= /text|varchar|string|date/g;
	var mm			= (typeof opts.fields == "undefined") ? obj.getFields : opts.fields;
	
	if(typeof opts.where != "undefined"){
		strWhere	= " WHERE " + opts.where + " ";
	} else if (typeof opts.key != "undefined"){
		if(closedVals.test( obj[obj.primaryKey].type ) == true ){
			strWhere 	= " WHERE " + obj.primaryKey +"='" + opts.key + "' ";
		} else {
			strWhere	= " WHERE " + obj.primaryKey +"=" + opts.key + " ";
		}		
		
	}
	if (typeof opts.limit != "undefined" ) {
		if(typeof opts.limit == "array"){
			strLimit	= ( opts.limit[1] ) ? " LIMIT " + opts.limit[0] + "," + opts.limit[1] : " LIMIT 0," + opts.limit[0];
		} else {
			strLimit	= " LIMIT 0," + opts.limit;
		}
	}
	
	strCR			= "";
	strCR			+= "SELECT ";
	
	for( i=0; i<mm.length; i++){
		var _mobj	= obj[ mm[i] ];
		if( _mobj.name){
			strCR	+= "`" + obj.name + "`.`" + _mobj.name + "`";
			strCR	+= (i == (mm.length-1)) ? "" : ", ";
		}
	}
	strCR		+= " FROM `" + obj.name + "` ";
	strCR		+= strWhere;
	strCR		+= strLimit;
	//console.log(strCR);
	return strCR;
}

DDL.prototype.insert	= function(obj, opts){
	var strLimit	= "";
	//options
	(typeof opts == "undefined") ? opts = {} : null;
	
	var values		= (typeof opts.values == "undefined") ? {} : opts.values;
	var symbols		= (typeof opts.values == "undefined") ? true : false;
	var run			= (typeof opts.run != "undefined") ? opts.run : false;
	var strVals		= "";
	var strFlds		= "";
	var strRun 		= "";
	var datRun		= [];
	var closedVals	= /text|varchar|string|date/g;
	//
	var mm			= obj.getFields;
	strCR			= "";
	strCR			+= "INSERT INTO ";
	strCR			+= "" + obj.name + " ";
	
	//si existe
	if( symbols == true|typeof opts.values != "object"){
		for( i=0; i<mm.length; i++){
			var _mobj	= obj[ mm[i] ];
			if( _mobj.name){
				strFlds	+= "" + _mobj.name + "";
				strFlds	+= (i == (mm.length-1)) ? "" : ", ";
			}
		}
		strVals		= obj.getSymbols.toString();
	} else {
		for( i=0; i<mm.length; i++){
			var _mobj	= obj[ mm[i] ];
			var _val	= "";
			if( _mobj.name){
				strFlds	+= "" + _mobj.name + "";
				strFlds	+= (i == (mm.length-1)) ? "" : ", ";
				//exists object value
				if( opts.values[ _mobj.name ] ){
					_val	= opts.values[ _mobj.name ];
				} else {
					_val	= (typeof _mobj.def  == "undefined") ? "" : _mobj.def;
				}
				strVals	+= (  closedVals.test( _mobj.type)) ? "'" + _val + "'" : _val;
				datRun.push((  closedVals.test( _mobj.type)) ? "'" + _val + "'" : _val);
				strVals	+= (i == (mm.length-1)) ? "" : ", ";
			}
		}
		
	}
	strCR		+= "(" + strFlds + ")";
	if(run == true){
		strRun	= strCR + " VALUES ( " + obj.getSymbols.toString() + ") ";
		this.sendQuery(obj, {
						sql : strRun,
						data : datRun
					   });
	}
	strCR		+= " VALUES ";
	strCR		+= "(" + strVals + ")";
	strCR		+= "";

	return strCR;
}

DDL.prototype.del	= function(obj, opts){
	var strLimit	= "";
	//options
	(typeof opts == "undefined") ? opts = {} : null;
	var strCR 		= "";
	var strWhere	= "";
	var where		= (typeof opts.where == "undefined") ? "" : " WHERE " + opts.where;
	var key			= (typeof opts.key == "undefined") ? "" : opts.key;
	strCR			+= "DELETE FROM "
	strCR			+= obj.name + " ";
	var closedVals	= /text|varchar|string|date/g;
	if( where != ""){
		strWhere	= where;
	} else {
		if( closedVals.test( obj[obj.primaryKey].type ) == true ){
			key		= "'" + key + "'";
		}
		strWhere	=  " WHERE " + obj.primaryKey	+ "=" + key;
	}
	strCR			+= strWhere;
	//if options is run()
	if(typeof opts.run != "undefined"){
		this.sendQuery(obj, { sql : strCR });
	}
	return strCR;
}
DDL.prototype.getRow	= function (obj, _key, callback){
	    var db      = false;
		var sql		= obj.select({ key : _key, limit: 1});
        if(window.openDatabase){
            try {
                db              = window.openDatabase(this._db_name, this._db_version, this._db_title, this._db_size);
	            db.transaction(function(transaction){
		            transaction.executeSql(sql, [], callback
										   ,function(){ console.log( "ERROR SQL : " + sql );});
	            });
            } catch(e) {
                db      	= false;
            }
        }
		
}

DDL.prototype.setPutRows	= function (obj, opts){
		var mkey	= (typeof opts.key == "undefined") ? 0 : opts.key;
		var inputs	= (typeof opts.inputs == "undefined") ? [] : opts.inputs;
		var mflds	= (typeof opts.fields == "undefined") ? [] : opts.fields;
	    var db      = false;
		var sql		= obj.select({ key : mkey, limit: 1});
        if(window.openDatabase){
            try {
                db              = window.openDatabase(this._db_name, this._db_version, this._db_title, this._db_size);
	            db.transaction(function(transaction){
		            transaction.executeSql(sql, [], function (t, results){
														for (var i=0; i<results.rows.length; i++) {
															var row 	= results.rows.item(i);
															for(var ix = 0; ix <= inputs.length; ix++){
																//
																if( mflds[ix]|mflds[ix] != "" ){
																	//checar si son inputs
																	if(typeof $("#"+ inputs[ix]).get(0) != "undefined"){
																		var ty	= $("#"+ inputs[ix]).get(0).tagName;			//Element type
																		if (ty == "INPUT"){
																			$("#"+ inputs[ix]).val( row[ mflds[ix] ] );
																		} else {
																			$("#"+ inputs[ix]).html( row[ mflds[ix] ] );
																		}
																	}
																}
															}
														}
													}
										   ,function(){ console.log( "ERROR SQL : " + sql );});
	            });
            } catch(e) {
                db      	= false;
            }
        }
		
}
DDL.prototype.enclosed		= function(val, field, obj){
	var closedVals	= /text|varchar|string|date/g;
		return (closedVals.test( obj[field].type ) == true ) ? "'" + val + "'" : val;
}
DDL.prototype.sendQuery	= function (obj, opts){
	(typeof opts == "undefined") ? opts = {} : null;
	    var db      	= this.cnn();
		var sql			= (typeof opts.sql	== "undefined") ? "" : opts.sql;
		var data		= (typeof opts.data	== "undefined") ? [] : opts.data;
		var callback	= (typeof opts.callback	== "undefined") ? function(t,r){} : opts.callback;
        if(window.openDatabase){
            if(db != false){
	            db.transaction( function(transaction){
					//console.log("Execute Query:" + sql);
		            transaction.executeSql(sql, data,
										   callback,
										   function(err){console.log("Query Error :#" + err.code + ", System says :" + err.message + "\nSQL " + sql + "\nData :" + data.toString() );});
	            });
            } else {
				throw new Error('Error: The database is null');
			}
        }
	return true;
}
DDL.prototype.getRowsInHTML	= function (obj, opts){
	(typeof opts == "undefined") ? opts = {} : null;
	    var db      	= false;
		var sql			= "";
		
		if (typeof opts.sql	== "undefined"){
			if(typeof opts.where	!= "undefined"){
				sql		= (typeof opts.limit	== "undefined") ? obj.select({ where : opts.where }) : obj.select({ limit : opts.limit, where : opts.where }) ;
			} else {
				sql		= (typeof opts.limit	== "undefined") ? obj.select() : obj.select({ limit : opts.limit }) ;
			}
		} else {
			sql			= opts.sql;
		}
		var targetID	= (typeof opts.targetID	== "undefined") ? "" : opts.targetID;
		var ofields		= (typeof opts.fields	== "undefined") ? obj.getFields : opts.fields;
		var otitles		= (typeof opts.titles	== "undefined") ? ofields : opts.titles;
		var otools		= (typeof opts.tools	== "undefined") ? "" : opts.tools;
		//tools = command(REPLACE_ID)
		//a,b,c,d
		/*
		 * [field, title, format]
		 */
		var txtRpt		= "";
		var txtRow		= "";
		var txtHead		= "";

		for(ix=0; ix<otitles.length; ix++){
			txtHead		+= "<th>" + String(otitles[ix]).toUpperCase() + "</th>";
		}
		if( otools != ""){
			txtHead		+= "<th>---</th>";
		}
		txtRpt			+= "<tr>" + txtHead + "</tr>";
		//console.log(sql); console.log(txtHead);
        if(window.openDatabase){
            try {
                db		= window.openDatabase(this._db_name, this._db_version, this._db_title, this._db_size);
	            db.transaction( function(transaction){
		            transaction.executeSql(sql, [],
										   function(t, results){
											var non		= 1;
											var css		= "";
											//get Row in HTML
												for (var i=0; i<results.rows.length; i++) {
													var row     = results.rows.item(i);
													if( non == 2){
														css		= " class=\"alt\" "
														non		= 1;
													} else {
														css		= "";
														non++;
													}
													txtRow		= "<tr>";
													for (ii=0; ii<ofields.length; ii++) {
														txtRow	+= "<td";
														txtRow	+= css;
														txtRow	+= ">";
														txtRow	+= row[ ofields[ii] ];
														txtRow	+= "</td>";
													}
													//tool
													if(otools != ""){
														txtRow	+= "<td class=\"alt\">" + String(otools).replace(/REPLACE_BY_ID/g, row[obj.primaryKey]) + "</td>";
													}
													//
													txtRow		+= "</tr>";
													txtRpt	+= txtRow;
												}
												$("#" + targetID).html("<table>" + txtRpt + "</table>");
											},
										   function(){});
	            });
            } catch(e) {
                db      = false;
            }
        }
	return true;
}
DDL.prototype.update	= function(obj, opts){
	(typeof opts == "undefined") ? opts = {} : null;
	
	//run
	//fields
	//fields 	= array
	//values	= array
	//where
	var fields		= (typeof opts.fields == "undefined") ? obj.getFields : opts.fields;
	var values		= (typeof opts.values == "undefined") ? [] : opts.values;
	//var where		= (typeof opts.where == "undefined") ? "" : opts.where;
	//var key			= (typeof opts.key	== "undefined") ? "" : opts.key;
	var run			= (typeof opts.run	== "undefined") ? false : opts.run;
	var closedVals	= /text|varchar|string|date/g;
	var strWhere	= "";
	var sql			= "";
	
	sql				+= "UPDATE " + obj.name;
	sql				+= " SET ";
	
	if(typeof opts.where != "undefined"){
		strWhere	= " WHERE " + opts.where + " ";
	} else if (typeof opts.key != "undefined"){
		if(closedVals.test( obj[obj.primaryKey].type ) == true ){
			strWhere 	= " WHERE " + obj.primaryKey +"='" + opts.key + "' ";
		} else {
			strWhere	= " WHERE " + obj.primaryKey +"=" + opts.key + " ";
		}
	}
	
	for( i=0; i<fields.length; i++){
		//if value exists
		if( values[i] ){
			if(obj[ fields[i] ].name){
			sql		+= obj[ fields[i] ].name + "=" +  this.enclosed(values[i], fields[i], obj);
			sql 	+= (i==(fields.length-1)) ? "" : ", ";
			} else {
				console.log("El Campo " + fields[i] + " No existe o esta incorrecto");
			}
		}
	}
	sql				+= strWhere;
	if(run == true){
		strRun	= strCR + " VALUES ( " + obj.getSymbols.toString() + ") ";
		this.sendQuery(obj, {
						sql : sql
					   });
	}
	return sql;
}

DDL.prototype.getRowsInList	= function (obj, opts){
	(typeof opts == "undefined") ? opts = {} : null;
	    var db      	= false;
		var sql			= "";
		
		if (typeof opts.sql	== "undefined"){
			if(typeof opts.where	!= "undefined"){
				sql		= (typeof opts.limit	== "undefined") ? obj.select({ where : opts.where }) : obj.select({ limit : opts.limit, where : opts.where }) ;
			} else {
				sql		= (typeof opts.limit	== "undefined") ? obj.select() : obj.select({ limit : opts.limit }) ;
			}
		} else {
			sql			= opts.sql;
		}
		var targetID	= (typeof opts.targetID	== "undefined") ? "" : opts.targetID;
		var ofields		= (typeof opts.fields	== "undefined") ? obj.getFields : opts.fields;
		var onclick		= (typeof opts.onclick	== "undefined") ? "" : " onclick=\"" + opts.onclick + "\" ";
		var tools		= (typeof opts.tools	== "undefined") ? "" : opts.tools;
		//tools = command(REPLACE_ID)
		//a,b,c,d
		/*
		 * [field, title, format]
		 */
		var txtRpt		= "";
		var txtRow		= "";
		
		//console.log(sql); console.log(txtHead);
        if(window.openDatabase){
            try {
                db		= window.openDatabase(this._db_name, this._db_version, this._db_title, this._db_size);
	            db.transaction( function(transaction){
		            transaction.executeSql(sql, [],
										   function(t, results){
											//txtRpt			+= "<ul data-role=\"listview\" data-divider-theme=\"a\" data-inset=\"true\" " + filter + " id=\"\"  >";
											//get Row in HTML
												for (var i=0; i<results.rows.length; i++) {
													var row     = results.rows.item(i);
													txtRow		= "<li " + String(onclick).replace(/REPLACE_BY_ID/g, row[obj.primaryKey]) + " >";

													for (ii=0; ii<ofields.length; ii++) {
														txtRow	+= (row[ ofields[ii] ]) ? row[ ofields[ii] ] : "";
														txtRow	+= (ii==(ofields.length-1) ) ? "" : "&curren;";
														//txtRow	+= (ii==(ofields.length-1) ) ? "" : "<br />";
													}
													//tool
													if(tools != ""){
														txtRow		+= "<span class=\"ui-li-count\">";
														txtRow		+= String(tools).replace(/REPLACE_BY_ID/g, row[obj.primaryKey]);
														txtRow		+= "</span>";
													}
													//
													txtRow			+= "</li>";
													txtRpt			+= txtRow;
												}
												//txtRpt		+= "</ul>";
												//console.log(txtRpt);
												$("#" + targetID).append(txtRpt);
												$("#" + targetID).listview('refresh');
											},
										   function(){});
	            });
            } catch(e) {
                db      = false;
            }
        }
	return true;
}
DDL.prototype.getRowsInOptions	= function (obj, opts){
	(typeof opts == "undefined") ? opts = {} : null;
	    var db      	= false;
		var sql			= "";
		
		if (typeof opts.sql	== "undefined"){
			if(typeof opts.where	!= "undefined"){
				sql		= (typeof opts.limit	== "undefined") ? obj.select({ where : opts.where }) : obj.select({ limit : opts.limit, where : opts.where }) ;
			} else {
				sql		= (typeof opts.limit	== "undefined") ? obj.select() : obj.select({ limit : opts.limit }) ;
			}
		} else {
			sql			= opts.sql;
		}
		var targetID	= (typeof opts.targetID	== "undefined") ? "" : opts.targetID;
		var fieldVal	= (typeof opts.fieldValue	== "undefined") ? obj.primaryKey : opts.fieldValue;
		var fieldTit	= (typeof opts.fieldTitle	== "undefined") ? "" : opts.fieldTitle;
		//a,b,c,d
		/*
		 * [field, title, format]
		 */
		var txtRpt		= "";
		var txtRow		= "";
		
		//console.log(sql); console.log(txtHead);
        if(window.openDatabase){
            try {
                db		= this.cnn();
	            db.transaction( function(transaction){
		            transaction.executeSql(sql, [],
										   function(t, results){
											//txtRpt			+= "<ul data-role=\"listview\" data-divider-theme=\"a\" data-inset=\"true\" " + filter + " id=\"\"  >";
											//get Row in HTML
												for (var i=0; i<results.rows.length; i++) {
													var row     = results.rows.item(i);
													txtRow		= "<option value=\"" + row[fieldVal] + "\">";
													txtRow		+= row[fieldTit];
													txtRow		+= "</option>";
													txtRpt		+= txtRow;
												}
												console.log(txtRpt);
												$("#" + targetID).append(txtRpt);
												//$("#" + targetID).listview('refresh');
											},
										   function(){});
	            });
            } catch(e) {
                db      = false;
            }
        }
	return true;
}
