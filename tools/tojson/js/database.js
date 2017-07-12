var XDatabase = {
        properties : [], //properties  table
        
        Connect : function (){
                args    = this.properties;
                db      = false;
                if(window.openDatabase){
                    //penDatabase(shortName, version, displayName, maxSize);
                    try {
                        args.database   = (typeof args.database == 'undefined') ? "DroidMart.db" : args.database;
                        args.size       = (typeof args.size == 'undefined') ? 5000000 : args.size;
                        args.version    = (typeof args.version == 'undefined') ? "1.0" : args.version;
                        args.title      = (typeof args.title == 'undefined') ? "DroidMart Database" : args.title;
                    
                        db              = window.openDatabase(args.database, args.version, args.title, args.size);
                    } catch(e) {
                        db      = false;
                    }
                    
                }
    
                return db;
        },
        Query : function (o){
                o.sql       = (typeof o.sql == 'undefined') ? "" : o.sql;
                o.values    = (typeof o.values == 'undefined') ? "" : o.values;
                o.callback  = (typeof o.callback == 'undefined') ? function (){} : o.callback;
                cnn     = this.Connect();
                if(cnn != false){
                    cnn.transaction( function(transaction){
                    transaction.executeSql(o.sql, o.values, o.callback, this.QueryError);
                    });
                    
                }               
        },
        QueryError : function (transaction, error){
                console.log(error.message); return true; 
        }
}
var XSql	= {
	table : "",
	primaryKey : "",
	
	_LsFields : [],
	_LsJoins : [],
	_Where : "",
	_Limit : "",
	update : function(o){
                o.fields  	= (typeof o.fields == 'undefined') ? "" : o.fields;
                o.values   	= (typeof o.values == 'undefined') ? "" : o.values;
                o.where    	= (typeof o.where == 'undefined') ? "" : o.where;
		o.enclose    	= (typeof o.enclose == 'undefined') ? false : o.enclose;
		
		var m_where	= (o.where != "") ? " WHERE " + o.where : "";
		var m_fields	= o.fields;
		var m_values	= o.values;
		var m_table	= this.table;
		var chr		= (o.enclose == false) ? "" : "'";
		var strSQL	= "";
		if (typeof m_fields == "array"||typeof m_fields == "object"){
			for(i=0; i < m_fields.length; i++){
				//
				strSQL	+= (i==0) ? m_fields[i] + "=" + chr + m_values[i] + chr + " " : ", " + m_fields[i] + "=" + chr + m_values[i] + chr + " ";
			}
		} else {
			strSQL	= m_fields + " =" + chr + m_values + chr + "";
		}
		//console.log("NAM::::" + (typeof o.fields) );
		var sql			= "UPDATE " + m_table + " SET " + strSQL + m_where;
		//console.log("SQL 1000 : " + sql); 
		return sql;
	},
	where : function(s){
	},
	select : function(){
		var str		= "";
		var strJ 	= "";
		//w	= (w == "") ? "" : " WHERE " + w;
		/*
		 */
		for(i=0; i<= this._LsFields.length; i++){
			var x	= this._LsFields[i];
			//0 name //1 alias //2 table
			if(x[0] == "*"){
				str += (i==0 ) ? "`" + x[2] + "`.`" + x[0] + "` " : ", `" + x[2] + "`.`" + x[0] + "` ";
			} else {
				str += (i==0 ) ? "`" + x[2] + "`.`" + x[0] + "` AS `" + x[1] + "` " : ", `" + x[2] + "`.`" + x[0] + "` AS `" + x[1] + "` ";
			}
		}
		if(this._LsJoins.length > 1){
			for(i=0; i<= this._LsJoins.length; i++){
				//strJ	+= this._LsJoins[i];
			}
		} else {
			strJ	= this.table;
		}
		return "SELECT " + str + " FROM " + strJ + " " + w;
	},
	field : function (o){
		/* name:
		 * alias:
		 * table
		 **/		
		//objeto
		if(typeof o == 'object'){
			o.name 	= (typeof o.name == 'undefined') ? "" : o.name;
			o.alias = (typeof o.alias == 'undefined') ? o.name : o.alias;
			o.table = (typeof o.table == 'undefined') ? this.table : o.table;
			this._LsFields.push([o.name, o.alias, o.table]);
		} else {
			//
			this._LsFields.push([o, o, this.table]);
		}
	},
	join : function(o){
		//t1.f1 =
		var strJ	= "";

		if(typeof o == 'object'){
			//parent: child: parentKey: childKey
			o.parent	= (typeof o.parent == 'undefined') ? this.table : o.parent;
			o.parentKey	= (typeof o.parentKey == 'undefined') ? this.primaryKey : o.parentKey;
			o.child		= (typeof o.child == 'undefined') ? "" : o.child;
			o.childKey	= (typeof o.childKey == 'undefined') ? "" : o.childKey;
			strJ		+= " INNER JOIN ";
			strJ		+= "`" + o.child + "` `" + o.child + "`";
			strJ		+= " ON ";
			
		} else {
			this._LsJoins.push(o);
		}
	}
}
//Esta clase crea un objeto SQL de un string create
