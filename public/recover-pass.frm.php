<?php
//------------------------- includes -------------------------
include_once("../core/core.config.inc.php");
include_once("../core/entidad.datos.php");
include_once("../core/core.db.inc.php");
include_once("../core/core.db.dic.php");
include_once("../core/core.lang.inc.php");
include_once("../core/core.html.inc.php");
include_once("../core/core.security.inc.php");
include_once("../core/core.error.inc.php");
require_once("../libs/TinyAjax.php");
//-------------------------------------------------------------

$xLng		= new cLang();

$now = gmdate('D, d M Y H:i:s') . ' GMT';
header('Expires: 0'); 											// rfc2616 - Section 14.21
header('Last-Modified: ' . $now);
header('Cache-Control: no-store, no-cache, must-revalidate'); 	// HTTP/1.1
header('Cache-Control: pre-check=0, post-check=0, max-age=0');	// HTTP/1.1
header('Pragma: no-cache');

$xUsr	= new cSystemUser();


?>
<!doctype html>
<html>
  <head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title></title>


<link href="../css/jquery-ui/jquery-ui.css" rel="stylesheet">
<link href="../css/jquery.qtip.css" rel="stylesheet">
<link href="../css/picker/default.css" rel="stylesheet">
<link href="../css/picker/default.date.css" rel="stylesheet">
<link href="../css/picker/default.time.css" rel="stylesheet">
<link href="../css/visualize.css" rel="stylesheet">
<link href="../css/visualize-light.css" rel="stylesheet">
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/tinybox.css" rel="stylesheet">
<link href="../css/multi-select.css" rel="stylesheet">
<link href="../css/amaran.min.css" rel="stylesheet">
<link href="../css/chartist.min.css" rel="stylesheet">


<script src="../js/jquery/jquery.js"></script>
<script src="../js/jquery/excanvas.js"></script>
<script src="../js/jquery/jquery.cookie.js"></script>
<script src="../js/base64.js"></script>
<script src="../js/jquery/all-jquery.ui.js"></script>
<script src="../js/general.js"></script>
<script src="../js/jquery/jquery.qtip.min.js"></script>
<script src="../js/jquery/visualize.jQuery.js"></script>
<script src="../js/jquery/jquery.accordion.js"></script>
<script src="../js/picker.js"></script>
<script src="../js/picker.date.js"></script>
<script src="../js/picker.time.js"></script>
<script src="../js/multi-select.min.js"></script>
<script src="../js/tinybox.js"></script>
<script src="../js/deprecated.js"></script>
<script src="../js/md5.js"></script>
<script src="../js/jscrypt/aes.js"></script>
<script src="../js/jquery/jquery.amaran.min.js"></script>
<script src="../js/spin.min.js"></script>
<script src="../js/hotkeys.min.js"></script>
<script src="../js/moment.min.js"></script>
<script src="../js/notify.min.js"></script>
<script src="../js/picker-lang/es_ES.js"></script>
<script src="../js/happy.js"></script>
<script src="../js/happy.methods.js"></script>
<script src="../js/xdate.js"></script>
<script src="../js/xdate.i18n.js"></script>
<script src="../js/chartist.min.js"></script>
<script src="../js/chartist-plugin-barlabels.min.js"></script>
<script src="../js/mexico.js"></script>

  </head>
  <body class="">
  
  
  
    <style>
      body,table td{font-family:sans-serif;font-size:14px}.body,body{background-color:#f6f6f6}.btn,.btn a,.content,.wrapper{box-sizing:border-box}.btn a,h1{text-transform:capitalize}.align-center,.btn table td,.footer,h1{text-align:center}.clear,.footer{clear:both}.first,.mt0{margin-top:0}.last,.mb0{margin-bottom:0}img{border:none;-ms-interpolation-mode:bicubic;max-width:100%}body{-webkit-font-smoothing:antialiased;line-height:1.4;margin:0;padding:0;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%}.container,.content{display:block;max-width:580px;padding:10px}table{border-collapse:separate;mso-table-lspace:0;mso-table-rspace:0;width:100%}table td{vertical-align:top}.body{width:100%}.btn a,.btn table td{background-color:#fff}.container{Margin:0 auto!important;width:580px}.btn,.footer,.main{width:100%}.content{Margin:0 auto}.main{background:#fff;border-radius:3px}.wrapper{padding:20px}.content-block{padding-bottom:10px;padding-top:10px}.footer{Margin-top:10px}h1,h2,h3,h4,ol,p,ul{font-family:sans-serif;margin:0}.footer a,.footer p,.footer span,.footer td{color:#999;font-size:12px;text-align:center}h1,h2,h3,h4{color:#000;font-weight:400;line-height:1.4;Margin-bottom:30px}.btn a,a{color:#3498db}h1{font-size:35px;font-weight:300}.btn a,ol,p,ul{font-size:14px}ol,p,ul{font-weight:400;Margin-bottom:15px}ol li,p li,ul li{list-style-position:inside;margin-left:5px}a{text-decoration:underline}.btn a,.powered-by a{text-decoration:none}.btn>tbody>tr>td{padding-bottom:15px}.btn table{width:auto}.btn table td{border-radius:5px}.btn a{border:1px solid #3498db;border-radius:5px;cursor:pointer;display:inline-block;font-weight:700;margin:0;padding:12px 25px}.btn-primary a,.btn-primary table td{background-color:#3498db}.btn-primary a{border-color:#3498db;color:#fff}.align-right{text-align:right}.align-left{text-align:left}.preheader{color:transparent;display:none;height:0;max-height:0;max-width:0;opacity:0;overflow:hidden;mso-hide:all;visibility:hidden;width:0}hr{border:0;border-bottom:1px solid #f6f6f6;Margin:20px 0}@media only screen and (max-width:620px){table[class=body] h1{font-size:28px!important;margin-bottom:10px!important}table[class=body] a,table[class=body] ol,table[class=body] p,table[class=body] span,table[class=body] td,table[class=body] ul{font-size:16px!important}table[class=body] .article,table[class=body] .wrapper{padding:10px!important}table[class=body] .content{padding:0!important}table[class=body] .container{padding:0!important;width:100%!important}table[class=body] .main{border-left-width:0!important;border-radius:0!important;border-right-width:0!important}table[class=body] .btn a,table[class=body] .btn table{width:100%!important}table[class=body] .img-responsive{height:auto!important;max-width:100%!important;width:auto!important}}@media all{.btn-primary a:hover,.btn-primary table td:hover{background-color:#34495e!important}.ExternalClass{width:100%}.ExternalClass,.ExternalClass div,.ExternalClass font,.ExternalClass p,.ExternalClass span,.ExternalClass td{line-height:100%}.apple-link a{color:inherit!important;font-family:inherit!important;font-size:inherit!important;font-weight:inherit!important;line-height:inherit!important;text-decoration:none!important}.btn-primary a:hover{border-color:#34495e!important}}
    </style>
    <form>
    
    <table border="0" cellpadding="0" cellspacing="0" class="body">
      <tr>
        <td>&nbsp;</td>
        <td class="container">
          <div class="content">

            <!-- START CENTERED WHITE CONTAINER -->
            <span class="preheader"></span>
            <h1>Recuperaci&oacute;n de Contrase&ntilde;a</h1>
            <table class="main">

              <!-- START MAIN CONTENT AREA -->
              <tr>
                <td class="wrapper">
                  <table border="0" cellpadding="0" cellspacing="0">
                    <tr>
                      <td>
                        <p>Estimado(a) usuario(a)</p>
                        <div>Capture su correo electr&oacute;nico de usuario, donde se le enviar&aacute; una liga de recuperaci&oacute;n</div>
                        <div>
                        <hr />
                        <div>
                        	<input type="email" id="idemail">
                        </div>
                        </div>
                        <hr />
                        <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                          <tbody>
                            <tr>
                              <td align="left">
                                <table border="0" cellpadding="0" cellspacing="0">
                                  <tbody>
                                    <tr>
                                      <td> <a onclick="jsRecoverPass()">Recuperar Contrase&ntilde;a</a> </td>
                                    </tr>
                                  </tbody>
                                </table>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                        <p></p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>

            <!-- END MAIN CONTENT AREA -->
            </table>

            <!-- START FOOTER -->
            <div class="footer">
              <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td class="content-block">
                    <span class="apple-link"><?php echo EACP_NAME; ?></span><a href=""></a>
                  </td>
                </tr>
                <tr>
                  <td class="content-block powered-by">
                    <a href="https://www.sipakal.com/">SAFE-OSMS</a>.
                  </td>
                </tr>
              </table>
            </div>
            <!-- END FOOTER -->

          <!-- END CENTERED WHITE CONTAINER -->
          </div>
        </td>
        <td>&nbsp;</td>
      </tr>
    </table>
  
</form>
 
<script>
var xG	= new Gen();

function jsRecoverPass(){
	var idmail	= $("#idemail").val();
	xG.svc({
		url: "pc.svc.php?cmd=RECOVER-PASS&email=" +idmail,
		callback: function(data){
			if(typeof data.message != "undefined"){
				
				if(data.error == true){
					xG.aviso({msg: data.message, raw:true, tipo:"error"});
				} else {
					xG.aviso({msg: data.message, raw:true, tipo:"info"});
				}
			}
		}
	});
}   
</script>  
  
  </body>

  
  
</html>
<?php

?>