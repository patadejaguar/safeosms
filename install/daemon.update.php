<?php ini_set('include_path', ini_get('include_path') .':/home/sipakal/Dropbox/htdocs/reports:/home/sipakal/Dropbox/htdocs/libs:/home/sipakal/Dropbox/htdocs/core'); 
include_once ('/home/sipakal/Dropbox/htdocs/core/core.db.inc.php');
$xPatch = new cSystemPatch();
$xPatch->patch(true);
?>