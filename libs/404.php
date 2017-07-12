<?php
$id = $_GET["i"];
if (!$id) {
	$id = 999;
}
header ("location:../404.php?i=$id");
?>
