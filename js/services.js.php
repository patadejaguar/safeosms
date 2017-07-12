<?php
include_once("../core/core.config.inc.php");
header("Content-type:text/javascript");

?>
var CloudConfig		= {
	server : "<?php echo SAFE_HOST_URL; ?>svc.php",
	apiKey : "<?php echo getClaveCifradoTemporal(); ?>",
	ctx : "",
	commands : {
		select : "select",
		insert : "insert",
		update : "update",
		stat : "status"
	}
}
var  CloudSys  =  function (){}

CloudSys.prototype.enc	= function(str){
	str	= Aes.Ctr.encrypt(str, CloudConfig.apiKey, 256)
	str	= base64.encode(str);
	return str;
}
CloudSys.prototype.dec	= function(str){
	str	= base64.decode(str);
	str	= Aes.Ctr.decrypt(str, CloudConfig.apiKey, 256)
	return str;
}

CloudSys.prototype.select	= function(opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	var callback	= (typeof opts.callback == "undefined") ? function(args){} : opts.callback;
	var mObj		= (typeof opts.obj == "undefined") ? {} : opts.obj;
	
	this.query({
			obj : mObj,
			cmd : CloudConfig.commands.select,
			callback : callback
		});
}
CloudSys.prototype.insert	= function(opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	var callback	= (typeof opts.callback == "undefined") ? function(args){} : opts.callback;
	var mObj		= (typeof opts.obj == "undefined") ? {} : opts.obj;
	
	this.query({
			obj : mObj,
			cmd : CloudConfig.commands.insert,
			callback : callback
		});
}

CloudSys.prototype.stat	= function(opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	var callback	= (typeof opts.callback == "undefined") ? function(args){} : opts.callback;
	var mObj		= (typeof opts.obj == "undefined") ? {} : opts.obj;
	
	this.query({
			obj : mObj,
			cmd : CloudConfig.commands.stat,
			callback : callback
		});
}

CloudSys.prototype.query	= function(opts){
	opts			= (typeof opts == "undefined") ? {} : opts;
	var callback	= (typeof opts.callback == "undefined") ? function(args){} : opts.callback;
	var mData		= (typeof opts.obj == "undefined") ? {} : this.enc(JSON.stringify(opts.obj));
	var mCmd		= (typeof opts.cmd == "undefined") ? this.enc(CloudConfig.commands.select) : this.enc(opts.cmd);
	var mCtx		= this.enc(CloudConfig.ctx);
	$.post(CloudConfig.server, {
		data : mData,
		cmd : mCmd,
		ctx : mCtx
	  },
  function(data,status){
	data	= xG.dec(data);
	//alert(data);
	data	= JSON.parse(data);
	callback(data);
	/*for (var rw in data){
	  var row = data[rw];
	  var strL	= "";
	  
	  for(cnt in row){
		strL += "<td>" + row[cnt] + "</td>";
	  }
	  $("#records").append("<tr>" +  strL +  "</tr>");
	}*/
  });
}

