<?php
$id = isset($_GET["i"]) ? $_GET["i"] : 999;
header ("location:../404.php?i=$id");
?>
