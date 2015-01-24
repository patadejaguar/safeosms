<?php  
if($_GET['showsource']) highlight_file($_SERVER['SCRIPT_FILENAME']) && die(); 
?> 
<?php echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?".">"; ?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
<title>Mediawiki to Dokuwiki Converter</title> 
<style> 
<!-- 
body, input{ 
    font-family:"Tahoma","Verdana",sans; 
} 
div{ 
    background-color: sandybrown; 
} 
textarea{ 
    height:300px; 
    width:500px; 
} 
table{display:none;} 
div.advertising{ color: none; display:none } 
--> 
</style> 
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /> 
<meta name="keywords" content="Mediawiki, converter, dokuwiki, wiki, syntax" /> 
<meta name="description" content="Mediawiki to dokuwiki syntax converter, online." /> 
</head> 

<body> 
<h1>Mediawiki to Dokuwiki Converter</h1> 
Hi! I'm Johannes Buchner! You can gimme <a href="http://johbuc6.coconia.net/doku.php?id=mediawiki_to_dokuwiki_converter">feedback here</a>.<br/> 
<a href="http://wiki.splitbrain.org/wiki:mediawiki_to_dokuwiki_converter">Project page on splitbrain</a> | <a href="http://johbuc6.coconia.net/">My little site</a> | <a href="?showsource=1">Source</a>.  
<div class="advertising">Mediawiki to Dokuwiki Converter, Mediawiki to Dokuwiki Converter, converting Wiki Text from Wikipedia to Dokuwiki or from Wikiquotes or any other Mediawiki to Dokuwiki.</div> 
<!-- 
<?php 
    $replacements = array( 
        '^[ ]*=([^=])'=>'<h1> ${1}', 
        '([^=])=[ ]*$'=>'${1} </h1>', 
        '^[ ]*==([^=])'=>'<h2> ${1}', 
        '([^=])==[ ]*$'=>'${1} </h2>', 
        '^[ ]*===([^=])'=>'<h3> ${1}', 
        '([^=])===[ ]*$'=>'${1} </h3>', 
        '^[ ]*====([^=])'=>'<h4> ${1}', 
        '([^=])====[ ]*$'=>'${1} </h4>', 
        '^[ ]*=====([^=])'=>'<h5> ${1}', 
        '([^=])=====[ ]*$'=>'${1} </h5>', 
        '^[ ]*======([^=])'=>'<h6> ${1}', 
        '([^=])======[ ]*$'=>'${1} </h6>', 
         
        '<\/?h1>'=>'======', 
        '<\/?h2>'=>'=====', 
        '<\/?h3>'=>'====', 
        '<\/?h4>'=>'===', 
        '<\/?h5>'=>'==', 
        '<\/?h6>'=>'=', 
         
        '^[\*#]{4}\* ?'=>'          * ', 
        '^[\*#]{3}\* ?'=>'        * ', 
        '^[\*#]{2}\* ?'=>'      * ', 
        '^[\*#]{1}\* ?'=>'    * ', 
        '^\* ?'=>'  * ', 
        '^[\*#]{4}# ?'=>'          \- ', 
        '^[\*\#]{3}\# ?'=>'      \- ', 
        '^[\*\#]{2}\# ?'=>'    \- ', 
        '^[\*\#]{1}\# ?'=>'  \- ', 
        '^\# ?'=>'  - ', 
         
        '([^\[])\[([^\[])'=>'${1}[[${2}', 
        '^\[([^\[])'=>'[[${1}', 
        '([^\]])\]([^\]])'=>'${1}]]${2}', 
        '([^\]])\]$'=>'${1}]]', 
         
        '(\[\[[^| \]]*) ([^|\]]*\]\])'=>'${1}|${2}', 
         
        "'''"=>"**", 
        "''"=>"//", 
         
        "^[ ]*:"=>">", 
        ">:"=>">>", 
        ">>:"=>">>>", 
        ">>>:"=>">>>>", 
        ">>>>:"=>">>>>>", 
        ">>>>>:"=>">>>>>>", 
        ">>>>>>:"=>">>>>>>>", 
         
        "<pre>"=>"<code>", 
        "<br[^>]*>"=>"\\\\\\\\", 
        "<\/pre>"=>"<\/code>" 
    ); 
    $_POST['mediawiki'] = stripslashes($_POST['mediawiki']); 
    $dokuwiki = split("\r\n",stripslashes($_POST['mediawiki'])); 
    if(!empty($dokuwiki)) foreach($replacements as $k=>$v){ 
        for($i=0;$i<count($dokuwiki);$i++) 
            $dokuwiki = preg_replace('/'.$k.'/',$v,$dokuwiki); 
        echo (++$j)."\r\n"; 
        echo $dokuwiki."\r\n"; 
    } 
    $dokuwiki = join("\r\n",$dokuwiki); 
?> 
--> 
<h2>Mediawiki:</h2> 
<form action="converter.php" method="post"> 
<textarea name="mediawiki"><?php echo $_POST['mediawiki'] ?></textarea> 
<input type="submit" value="convert"> 
</form> 
<h2>Dokuwiki:</h2> 
<textarea name="dokuwiki"><?php echo $dokuwiki; ?></textarea> 

<div class="advertising">Mediawiki to Dokuwiki Converter, Mediawiki to Dokuwiki Converter, converting Wiki Text from Wikipedia to Dokuwiki or from Wikiquotes or any other Mediawiki to Dokuwiki.</div> 

</body> 
</html> 