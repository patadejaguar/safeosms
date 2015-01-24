<?php session_start();
  if (isset($_GET["order"])) $order = @$_GET["order"];
  if (isset($_GET["type"])) $ordtype = @$_GET["type"];

  if (isset($_POST["filter"])) $filter = @$_POST["filter"];
  if (isset($_POST["filter_field"])) $filterfield = @$_POST["filter_field"];
  $wholeonly = false;
  if (isset($_POST["wholeonly"])) $wholeonly = @$_POST["wholeonly"];

  if (!isset($order) && isset($_SESSION["order"])) $order = $_SESSION["order"];
  if (!isset($ordtype) && isset($_SESSION["type"])) $ordtype = $_SESSION["type"];
  if (!isset($filter) && isset($_SESSION["filter"])) $filter = $_SESSION["filter"];
  if (!isset($filterfield) && isset($_SESSION["filter_field"])) $filterfield = $_SESSION["filter_field"];
include_once("../core/core.config.inc.php");
?>

<html>
<head>
<title>matriz -- general_structure</title>
<meta name="generator" http-equiv="content-type" content="text/html">
<style type="text/css">
  body {
    background-color: #FFFFFF;
    color: #000000;
    font-family: Arial;
    font-size: 12px;
  }
  .bd {
    background-color: #FFFFFF;
    color: #000000;
    font-family: Arial;
    font-size: 12px;
  }
  .tbl {
    background-color: #FFFFFF;
  }
  a:link { 
    color: #FF0000;
    font-family: Arial;
    font-size: 12px;
  }
  a:active { 
    color: #0000FF;
    font-family: Arial;
    font-size: 12px;
  }
  a:visited { 
    color: #800080;
    font-family: Arial;
    font-size: 12px;
  }
  .hr {
    background-color: #068FE6;
    color: #FFFFFF;
    font-family: Arial;
    font-size: 12px;
  }
  a.hr:link {
    color: #FFFFFF;
    font-family: Arial;
    font-size: 12px;
  }
  a.hr:active {
    color: #FFFFFF;
    font-family: Arial;
    font-size: 12px;
  }
  a.hr:visited {
    color: #FFFFFF;
    font-family: Arial;
    font-size: 12px;
  }
  .dr {
    background-color: #FFFFFF;
    color: #000000;
    font-family: Arial;
    font-size: 12px;
  }
  .sr {
    background-color: #FFFCD9;
    color: #000000;
    font-family: Arial;
    font-size: 12px;
  }
</style>
</head>
<body>
<table class="bd"  ><tr><td class="hr"><h2>Editar Bases</h2></td></tr></table>
<?php
  $conn = connect();
  $showrecs = 20;
  $pagerange = 10;

  $a = @$_GET["a"];
  $recid = @$_GET["recid"];
  $page = @$_GET["page"];
  if (!isset($page)) $page = 1;

  $sql = @$_POST["sql"];

  switch ($sql) {
    case "insert":
      sql_insert();
      break;
    case "update":
      sql_update();
      break;
    case "delete":
      sql_delete();
      break;
  }

  switch ($a) {
    case "add":
      addrec();
      break;
    case "edit":
      editrec($recid);
      break;
    case "del":
      deleterec($recid);
      break;
    default:
      select();
      break;
  }

  if (isset($order)) $_SESSION["order"] = $order;
  if (isset($ordtype)) $_SESSION["type"] = $ordtype;
  if (isset($filter)) $_SESSION["filter"] = $filter;
  if (isset($filterfield)) $_SESSION["filter_field"] = $filterfield;
  if (isset($wholeonly)) $_SESSION["wholeonly"] = $wholeonly;

  mysql_close($conn);
?>
<table class="bd"  ><tr><td class="hr"></td></tr></table>
</body>
</html>

<?php function select()
  {
  global $a;
  global $showrecs;
  global $page;
  global $filter;
  global $filterfield;
  global $wholeonly;
  global $order;
  global $ordtype;


  if ($a == "reset") {
    $filter = "";
    $filterfield = "";
    $wholeonly = "";
    $order = "";
    $ordtype = "";
  }

  $checkstr = "";
  if ($wholeonly) $checkstr = " checked";
  if ($ordtype == "asc") { $ordtypestr = "desc"; } else { $ordtypestr = "asc"; }
  $res = sql_select();
  $count = sql_getrecordcount();
  if ($count % $showrecs != 0) {
    $pagecount = intval($count / $showrecs) + 1;
  }
  else {
    $pagecount = intval($count / $showrecs);
  }
  $startrec = $showrecs * ($page - 1);
  if ($startrec < $count) {mysql_data_seek($res, $startrec);}
  $reccount = min($showrecs * $page, $count);
?>
<table class="bd"     >
<tr><td>Origen de Datos: general_structure</td></tr>
<tr><td>Registros Mostrados <?php echo $startrec + 1 ?> - <?php echo $reccount ?> of <?php echo $count ?></td></tr>
</table>
<hr size="1" noshade>
<form action="general_structure.raw.php" method="post">
<table class="bd"     >
<tr>
<td><b>Fltro Personal</b>&nbsp;</td>
<td><input type="text" name="filter" value="<?php echo $filter ?>"></td>
<td><select name="filter_field">
<option value="">Todos los Registros</option>
<option value="<?php echo "index_struct" ?>"<?php if ($filterfield == "index_struct") { echo "selected"; } ?>><?php echo htmlspecialchars("index_struct") ?></option>
<option value="<?php echo "tabla" ?>"<?php if ($filterfield == "tabla") { echo "selected"; } ?>><?php echo htmlspecialchars("tabla") ?></option>
<option value="<?php echo "campo" ?>"<?php if ($filterfield == "campo") { echo "selected"; } ?>><?php echo htmlspecialchars("campo") ?></option>
<option value="<?php echo "valor" ?>"<?php if ($filterfield == "valor") { echo "selected"; } ?>><?php echo htmlspecialchars("valor") ?></option>
<option value="<?php echo "tipo" ?>"<?php if ($filterfield == "tipo") { echo "selected"; } ?>><?php echo htmlspecialchars("tipo") ?></option>
<option value="<?php echo "longitud" ?>"<?php if ($filterfield == "longitud") { echo "selected"; } ?>><?php echo htmlspecialchars("longitud") ?></option>
<option value="<?php echo "descripcion" ?>"<?php if ($filterfield == "descripcion") { echo "selected"; } ?>><?php echo htmlspecialchars("descripcion") ?></option>
<option value="<?php echo "titulo" ?>"<?php if ($filterfield == "titulo") { echo "selected"; } ?>><?php echo htmlspecialchars("titulo") ?></option>
<option value="<?php echo "control" ?>"<?php if ($filterfield == "control") { echo "selected"; } ?>><?php echo htmlspecialchars("control") ?></option>
<option value="<?php echo "sql_select" ?>"<?php if ($filterfield == "sql_select") { echo "selected"; } ?>><?php echo htmlspecialchars("sql_select") ?></option>
<option value="<?php echo "orientacion" ?>"<?php if ($filterfield == "orientacion") { echo "selected"; } ?>><?php echo htmlspecialchars("orientacion") ?></option>
<option value="<?php echo "order_index" ?>"<?php if ($filterfield == "order_index") { echo "selected"; } ?>><?php echo htmlspecialchars("order_index") ?></option>
<option value="<?php echo "script_field" ?>"<?php if ($filterfield == "script_field") { echo "selected"; } ?>><?php echo htmlspecialchars("JavaScript Evaluate Code") ?></option>
<option value="<?php echo "help_text" ?>"<?php if ($filterfield == "help_text") { echo "selected"; } ?>><?php echo htmlspecialchars("help_text") ?></option>
</select></td>
<td><input type="checkbox" name="wholeonly"<?php echo $checkstr ?>>Palabras Exactas solamente</td>
</td></tr>
<tr>
<td>&nbsp;</td>
<td><input type="submit" name="action" value="Aplicar Filtro"></td>
<td><a href="general_structure.raw.php?a=reset">Quitar Filtro</a></td>
</tr>
</table>
</form>
<hr size="1" noshade>
<?php showpagenav($page, $pagecount); ?>
<br>
<table class="tbl"      >
<tr>
<td class="hr">&nbsp;</td>
<td class="hr">&nbsp;</td>
<td class="hr"><a class="hr" href="general_structure.raw.php?order=<?php echo "index_struct" ?>&type=<?php echo $ordtypestr ?>"><?php echo htmlspecialchars("index_struct") ?></a></td>
<td class="hr"><a class="hr" href="general_structure.raw.php?order=<?php echo "tabla" ?>&type=<?php echo $ordtypestr ?>"><?php echo htmlspecialchars("tabla") ?></a></td>
<td class="hr"><a class="hr" href="general_structure.raw.php?order=<?php echo "campo" ?>&type=<?php echo $ordtypestr ?>"><?php echo htmlspecialchars("campo") ?></a></td>
<td class="hr"><a class="hr" href="general_structure.raw.php?order=<?php echo "valor" ?>&type=<?php echo $ordtypestr ?>"><?php echo htmlspecialchars("valor") ?></a></td>
<td class="hr"><a class="hr" href="general_structure.raw.php?order=<?php echo "tipo" ?>&type=<?php echo $ordtypestr ?>"><?php echo htmlspecialchars("tipo") ?></a></td>
<td class="hr"><a class="hr" href="general_structure.raw.php?order=<?php echo "longitud" ?>&type=<?php echo $ordtypestr ?>"><?php echo htmlspecialchars("longitud") ?></a></td>
<td class="hr"><a class="hr" href="general_structure.raw.php?order=<?php echo "descripcion" ?>&type=<?php echo $ordtypestr ?>"><?php echo htmlspecialchars("descripcion") ?></a></td>
<td class="hr"><a class="hr" href="general_structure.raw.php?order=<?php echo "titulo" ?>&type=<?php echo $ordtypestr ?>"><?php echo htmlspecialchars("titulo") ?></a></td>
<td class="hr"><a class="hr" href="general_structure.raw.php?order=<?php echo "control" ?>&type=<?php echo $ordtypestr ?>"><?php echo htmlspecialchars("control") ?></a></td>
<td class="hr"><a class="hr" href="general_structure.raw.php?order=<?php echo "sql_select" ?>&type=<?php echo $ordtypestr ?>"><?php echo htmlspecialchars("sql_select") ?></a></td>
<td class="hr"><a class="hr" href="general_structure.raw.php?order=<?php echo "orientacion" ?>&type=<?php echo $ordtypestr ?>"><?php echo htmlspecialchars("orientacion") ?></a></td>
<td class="hr"><a class="hr" href="general_structure.raw.php?order=<?php echo "order_index" ?>&type=<?php echo $ordtypestr ?>"><?php echo htmlspecialchars("order_index") ?></a></td>
<td class="hr"><a class="hr" href="general_structure.raw.php?order=<?php echo "script_field" ?>&type=<?php echo $ordtypestr ?>"><?php echo htmlspecialchars("JavaScript Evaluate Code") ?></a></td>
<td class="hr"><a class="hr" href="general_structure.raw.php?order=<?php echo "help_text" ?>&type=<?php echo $ordtypestr ?>"><?php echo htmlspecialchars("help_text") ?></a></td>
</tr>
<?php
  for ($i = $startrec; $i < $reccount; $i++)
  {
    $row = mysql_fetch_assoc($res);
    $style = "dr";
    if ($i % 2 != 0) {
      $style = "sr";
    }
?>
<tr>
<td class="<?php echo $style ?>"><a href="general_structure.raw.php?a=edit&recid=<?php echo $i ?>">Editar</a></td>
<td class="<?php echo $style ?>"><a href="general_structure.raw.php?a=del&recid=<?php echo $i ?>">Eliminar</a></td>
<td class="<?php echo $style ?>"><?php echo htmlspecialchars($row["index_struct"]) ?></td>
<td class="<?php echo $style ?>"><?php echo htmlspecialchars($row["tabla"]) ?></td>
<td class="<?php echo $style ?>"><?php echo htmlspecialchars($row["campo"]) ?></td>
<td class="<?php echo $style ?>"><?php echo htmlspecialchars($row["valor"]) ?></td>
<td class="<?php echo $style ?>"><?php echo htmlspecialchars($row["tipo"]) ?></td>
<td class="<?php echo $style ?>"><?php echo htmlspecialchars($row["longitud"]) ?></td>
<td class="<?php echo $style ?>"><?php echo htmlspecialchars($row["descripcion"]) ?></td>
<td class="<?php echo $style ?>"><?php echo htmlspecialchars($row["titulo"]) ?></td>
<td class="<?php echo $style ?>"><?php echo htmlspecialchars($row["control"]) ?></td>
<td class="<?php echo $style ?>"><?php echo htmlspecialchars($row["sql_select"]) ?></td>
<td class="<?php echo $style ?>"><?php echo htmlspecialchars($row["orientacion"]) ?></td>
<td class="<?php echo $style ?>"><?php echo htmlspecialchars($row["order_index"]) ?></td>
<td class="<?php echo $style ?>"><?php echo htmlspecialchars($row["script_field"]) ?></td>
<td class="<?php echo $style ?>"><?php echo htmlspecialchars($row["help_text"]) ?></td>
</tr>
<?php
  }
  mysql_free_result($res);
?>
</table>
<br>
<?php showpagenav($page, $pagecount); ?>
<?php } ?>

<?php function showrow($row, $recid)
  {
?>
<table class="tbl"      >
<tr>
<td class="hr"><?php echo htmlspecialchars("index_struct")."&nbsp;" ?></td>
<td class="dr"><?php echo htmlspecialchars($row["index_struct"]) ?></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("tabla")."&nbsp;" ?></td>
<td class="dr"><?php echo htmlspecialchars($row["tabla"]) ?></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("campo")."&nbsp;" ?></td>
<td class="dr"><?php echo htmlspecialchars($row["campo"]) ?></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("valor")."&nbsp;" ?></td>
<td class="dr"><?php echo htmlspecialchars($row["valor"]) ?></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("tipo")."&nbsp;" ?></td>
<td class="dr"><?php echo htmlspecialchars($row["tipo"]) ?></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("longitud")."&nbsp;" ?></td>
<td class="dr"><?php echo htmlspecialchars($row["longitud"]) ?></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("descripcion")."&nbsp;" ?></td>
<td class="dr"><?php echo htmlspecialchars($row["descripcion"]) ?></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("titulo")."&nbsp;" ?></td>
<td class="dr"><?php echo htmlspecialchars($row["titulo"]) ?></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("control")."&nbsp;" ?></td>
<td class="dr"><?php echo htmlspecialchars($row["control"]) ?></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("sql_select")."&nbsp;" ?></td>
<td class="dr"><?php echo htmlspecialchars($row["sql_select"]) ?></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("orientacion")."&nbsp;" ?></td>
<td class="dr"><?php echo htmlspecialchars($row["orientacion"]) ?></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("order_index")."&nbsp;" ?></td>
<td class="dr"><?php echo htmlspecialchars($row["order_index"]) ?></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("JavaScript Evaluate Code")."&nbsp;" ?></td>
<td class="dr"><?php echo htmlspecialchars($row["script_field"]) ?></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("help_text")."&nbsp;" ?></td>
<td class="dr"><?php echo htmlspecialchars($row["help_text"]) ?></td>
</tr>
</table>
<?php } ?>

<?php function showroweditor($row, $iseditmode)
  {
  global $conn;
?>
<table class="tbl"      >
<tr>
<td class="hr"><?php echo htmlspecialchars("index_struct")."&nbsp;" ?></td>
<td class="dr"><input type="text" name="index_struct" value="<?php echo str_replace('"', '&quot;', trim($row["index_struct"])) ?>"></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("tabla")."&nbsp;" ?></td>
<td class="dr"><textarea cols="35" rows="4" name="tabla" maxlength="100"><?php echo str_replace('"', '&quot;', trim($row["tabla"])) ?></textarea></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("campo")."&nbsp;" ?></td>
<td class="dr"><textarea cols="35" rows="4" name="campo" maxlength="100"><?php echo str_replace('"', '&quot;', trim($row["campo"])) ?></textarea></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("valor")."&nbsp;" ?></td>
<td class="dr"><textarea cols="35" rows="4" name="valor" maxlength="250"><?php echo str_replace('"', '&quot;', trim($row["valor"])) ?></textarea></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("tipo")."&nbsp;" ?></td>
<td class="dr"><input type="text" name="tipo" maxlength="20" value="<?php echo str_replace('"', '&quot;', trim($row["tipo"])) ?>"></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("longitud")."&nbsp;" ?></td>
<td class="dr"><input type="text" name="longitud" value="<?php echo str_replace('"', '&quot;', trim($row["longitud"])) ?>"></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("descripcion")."&nbsp;" ?></td>
<td class="dr"><textarea cols="35" rows="4" name="descripcion" maxlength="200"><?php echo str_replace('"', '&quot;', trim($row["descripcion"])) ?></textarea></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("titulo")."&nbsp;" ?></td>
<td class="dr"><textarea cols="35" rows="4" name="titulo" maxlength="100"><?php echo str_replace('"', '&quot;', trim($row["titulo"])) ?></textarea></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("control")."&nbsp;" ?></td>
<td class="dr"><select name="control">
<option value=""></option>
<?php
  $lookupvalues = array("text","textarea","select","hidden");

  reset($lookupvalues);
  foreach($lookupvalues as $val){
  $caption = $val;
  if ($row["control"] == $val) {$selstr = " selected"; } else {$selstr = ""; }
 ?><option value="<?php echo $val ?>"<?php echo $selstr ?>><?php echo $caption ?></option>
<?php } ?></select>
</td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("sql_select")."&nbsp;" ?></td>
<td class="dr"><textarea cols="35" rows="4" name="sql_select" maxlength="200"><?php echo str_replace('"', '&quot;', trim($row["sql_select"])) ?></textarea></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("orientacion")."&nbsp;" ?></td>
<td class="dr"><select name="orientacion">
<option value=""></option>
<?php
  $lookupvalues = array("izquierda","derecha");

  reset($lookupvalues);
  foreach($lookupvalues as $val){
  $caption = $val;
  if ($row["orientacion"] == $val) {$selstr = " selected"; } else {$selstr = ""; }
 ?><option value="<?php echo $val ?>"<?php echo $selstr ?>><?php echo $caption ?></option>
<?php } ?></select>
</td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("order_index")."&nbsp;" ?></td>
<td class="dr"><input type="text" name="order_index" value="<?php echo str_replace('"', '&quot;', trim($row["order_index"])) ?>"></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("JavaScript Evaluate Code")."&nbsp;" ?></td>
<td class="dr"><textarea cols="35" rows="4" name="script_field" maxlength="65535"><?php echo str_replace('"', '&quot;', trim($row["script_field"])) ?></textarea></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("help_text")."&nbsp;" ?></td>
<td class="dr"><textarea cols="35" rows="4" name="help_text" maxlength="65535"><?php echo str_replace('"', '&quot;', trim($row["help_text"])) ?></textarea></td>
</tr>
</table>
<?php } ?>

<?php function showpagenav($page, $pagecount)
{
?>
<table class="bd"     >
<tr>
<td><a href="general_structure.raw.php?a=add">Agregar Registro</a>&nbsp;</td>
<?php if ($page > 1) { ?>
<td><a href="general_structure.raw.php?page=<?php echo $page - 1 ?>">&lt;&lt;&nbsp;Anterior</a>&nbsp;</td>
<?php } ?>
<?php
  global $pagerange;

  if ($pagecount > 1) {

  if ($pagecount % $pagerange != 0) {
    $rangecount = intval($pagecount / $pagerange) + 1;
  }
  else {
    $rangecount = intval($pagecount / $pagerange);
  }
  for ($i = 1; $i < $rangecount + 1; $i++) {
    $startpage = (($i - 1) * $pagerange) + 1;
    $count = min($i * $pagerange, $pagecount);

    if ((($page >= $startpage) && ($page <= ($i * $pagerange)))) {
      for ($j = $startpage; $j < $count + 1; $j++) {
        if ($j == $page) {
?>
<td><b><?php echo $j ?></b></td>
<?php } else { ?>
<td><a href="general_structure.raw.php?page=<?php echo $j ?>"><?php echo $j ?></a></td>
<?php } } } else { ?>
<td><a href="general_structure.raw.php?page=<?php echo $startpage ?>"><?php echo $startpage ."..." .$count ?></a></td>
<?php } } } ?>
<?php if ($page < $pagecount) { ?>
<td>&nbsp;<a href="general_structure.raw.php?page=<?php echo $page + 1 ?>">Siguiente&nbsp;&gt;&gt;</a>&nbsp;</td>
<?php } ?>
</tr>
</table>
<?php } ?>

<?php function showrecnav($a, $recid, $count)
{
?>
<table class="bd"     >
<tr>
<td><a href="general_structure.raw.php">Inicio de Pagina</a></td>
<?php if ($recid > 0) { ?>
<td><a href="general_structure.raw.php?a=<?php echo $a ?>&recid=<?php echo $recid - 1 ?>">Primer Registro</a></td>
<?php } if ($recid < $count - 1) { ?>
<td><a href="general_structure.raw.php?a=<?php echo $a ?>&recid=<?php echo $recid + 1 ?>">Proximo Registro</a></td>
<?php } ?>
</tr>
</table>
<hr size="1" noshade>
<?php } ?>

<?php function addrec()
{
?>
<table class="bd"     >
<tr>
<td><a href="general_structure.raw.php">Inicio de Pagina</a></td>
</tr>
</table>
<hr size="1" noshade>
<form enctype="multipart/form-data" action="general_structure.raw.php" method="post">
<p><input type="hidden" name="sql" value="insert"></p>
<?php
$row = array(
  "index_struct" => "",
  "tabla" => "",
  "campo" => "",
  "valor" => "",
  "tipo" => "",
  "longitud" => "",
  "descripcion" => "",
  "titulo" => "",
  "control" => "",
  "sql_select" => "",
  "orientacion" => "",
  "order_index" => "",
  "script_field" => "",
  "help_text" => "");
showroweditor($row, false);
?>
<p><input type="submit" name="action" value="Guardar y regresar"></p>
</form>
<?php } ?>
<?php function editrec($recid)
{
  $res = sql_select();
  $count = sql_getrecordcount();
  mysql_data_seek($res, $recid);
  $row = mysql_fetch_assoc($res);
  showrecnav("edit", $recid, $count);
?>
<br>
<form enctype="multipart/form-data" action="general_structure.raw.php" method="post">
<input type="hidden" name="sql" value="update">
<input type="hidden" name="xindex_struct" value="<?php echo $row["index_struct"] ?>">
<?php showroweditor($row, true); ?>
<p><input type="submit" name="action" value="Guardar y regresar"></p>
</form>
<?php
  mysql_free_result($res);
} ?>

<?php function deleterec($recid)
{
  $res = sql_select();
  $count = sql_getrecordcount();
  mysql_data_seek($res, $recid);
  $row = mysql_fetch_assoc($res);
  showrecnav("del", $recid, $count);
?>
<br>
<form action="general_structure.raw.php" method="post">
<input type="hidden" name="sql" value="delete">
<input type="hidden" name="xindex_struct" value="<?php echo $row["index_struct"] ?>">
<?php showrow($row, $recid) ?>
<p><input type="submit" name="action" value="Confirmar"></p>
</form>
<?php
  mysql_free_result($res);
} ?>

<?php function connect()
{
  $conn = mysql_connect("localhost", RPT_USR_DB, RPT_PWD_DB);
  mysql_select_db("matriz");
  return $conn;
}

function sqlvalue($val, $quote)
{
  if ($quote)
    $tmp = sqlstr($val);
  else
    $tmp = $val;
  if ($tmp == "")
    $tmp = "NULL";
  elseif ($quote)
    $tmp = "'".$tmp."'";
  return $tmp;
}

function sqlstr($val)
{
  return str_replace("'", "''", $val);
}

function sql_select()
{
  global $conn;
  global $order;
  global $ordtype;
  global $filter;
  global $filterfield;
  global $wholeonly;

  $filterstr = sqlstr($filter);
  if (!$wholeonly && isset($wholeonly) && $filterstr!='') $filterstr = "%" .$filterstr ."%";
  $sql = "SELECT `index_struct`, `tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text` FROM `general_structure`";
  if (isset($filterstr) && $filterstr!='' && isset($filterfield) && $filterfield!='') {
    $sql .= " where " .sqlstr($filterfield) ." like '" .$filterstr ."'";
  } elseif (isset($filterstr) && $filterstr!='') {
    $sql .= " where (`index_struct` like '" .$filterstr ."') or (`tabla` like '" .$filterstr ."') or (`campo` like '" .$filterstr ."') or (`valor` like '" .$filterstr ."') or (`tipo` like '" .$filterstr ."') or (`longitud` like '" .$filterstr ."') or (`descripcion` like '" .$filterstr ."') or (`titulo` like '" .$filterstr ."') or (`control` like '" .$filterstr ."') or (`sql_select` like '" .$filterstr ."') or (`orientacion` like '" .$filterstr ."') or (`order_index` like '" .$filterstr ."') or (`script_field` like '" .$filterstr ."') or (`help_text` like '" .$filterstr ."')";
  }
  if (isset($order) && $order!='') $sql .= " order by `" .sqlstr($order) ."`";
  if (isset($ordtype) && $ordtype!='') $sql .= " " .sqlstr($ordtype);
  $res = mysql_query($sql, $conn) or die(mysql_error());
  return $res;
}

function sql_getrecordcount()
{
  global $conn;
  global $order;
  global $ordtype;
  global $filter;
  global $filterfield;
  global $wholeonly;

  $filterstr = sqlstr($filter);
  if (!$wholeonly && isset($wholeonly) && $filterstr!='') $filterstr = "%" .$filterstr ."%";
  $sql = "SELECT COUNT(*) FROM `general_structure`";
  if (isset($filterstr) && $filterstr!='' && isset($filterfield) && $filterfield!='') {
    $sql .= " where " .sqlstr($filterfield) ." like '" .$filterstr ."'";
  } elseif (isset($filterstr) && $filterstr!='') {
    $sql .= " where (`index_struct` like '" .$filterstr ."') or (`tabla` like '" .$filterstr ."') or (`campo` like '" .$filterstr ."') or (`valor` like '" .$filterstr ."') or (`tipo` like '" .$filterstr ."') or (`longitud` like '" .$filterstr ."') or (`descripcion` like '" .$filterstr ."') or (`titulo` like '" .$filterstr ."') or (`control` like '" .$filterstr ."') or (`sql_select` like '" .$filterstr ."') or (`orientacion` like '" .$filterstr ."') or (`order_index` like '" .$filterstr ."') or (`script_field` like '" .$filterstr ."') or (`help_text` like '" .$filterstr ."')";
  }
  $res = mysql_query($sql, $conn) or die(mysql_error());
  $row = mysql_fetch_assoc($res);
  reset($row);
  return current($row);
}

function sql_insert()
{
  global $conn;
  global $_POST;

  $sql = "insert into `general_structure` (`index_struct`, `tabla`, `campo`, `valor`, `tipo`, `longitud`, `descripcion`, `titulo`, `control`, `sql_select`, `orientacion`, `order_index`, `script_field`, `help_text`) values (" .sqlvalue(@$_POST["index_struct"], false).", " .sqlvalue(@$_POST["tabla"], true).", " .sqlvalue(@$_POST["campo"], true).", " .sqlvalue(@$_POST["valor"], true).", " .sqlvalue(@$_POST["tipo"], true).", " .sqlvalue(@$_POST["longitud"], false).", " .sqlvalue(@$_POST["descripcion"], true).", " .sqlvalue(@$_POST["titulo"], true).", " .sqlvalue(@$_POST["control"], true).", " .sqlvalue(@$_POST["sql_select"], true).", " .sqlvalue(@$_POST["orientacion"], true).", " .sqlvalue(@$_POST["order_index"], false).", " .sqlvalue(@$_POST["script_field"], true).", " .sqlvalue(@$_POST["help_text"], true).")";
  mysql_query($sql, $conn) or die(mysql_error());
}

function sql_update()
{
  global $conn;
  global $_POST;

  $sql = "update `general_structure` set `index_struct`=" .sqlvalue(@$_POST["index_struct"], false).", `tabla`=" .sqlvalue(@$_POST["tabla"], true).", `campo`=" .sqlvalue(@$_POST["campo"], true).", `valor`=" .sqlvalue(@$_POST["valor"], true).", `tipo`=" .sqlvalue(@$_POST["tipo"], true).", `longitud`=" .sqlvalue(@$_POST["longitud"], false).", `descripcion`=" .sqlvalue(@$_POST["descripcion"], true).", `titulo`=" .sqlvalue(@$_POST["titulo"], true).", `control`=" .sqlvalue(@$_POST["control"], true).", `sql_select`=" .sqlvalue(@$_POST["sql_select"], true).", `orientacion`=" .sqlvalue(@$_POST["orientacion"], true).", `order_index`=" .sqlvalue(@$_POST["order_index"], false).", `script_field`=" .sqlvalue(@$_POST["script_field"], true).", `help_text`=" .sqlvalue(@$_POST["help_text"], true) ." where " .primarykeycondition();
  mysql_query($sql, $conn) or die(mysql_error());
}

function sql_delete()
{
  global $conn;

  $sql = "delete from `general_structure` where " .primarykeycondition();
  mysql_query($sql, $conn) or die(mysql_error());
}
function primarykeycondition()
{
  global $_POST;
  $pk = "";
  $pk .= "(`index_struct`";
  if (@$_POST["xindex_struct"] == "") {
    $pk .= " IS NULL";
  }else{
  $pk .= " = " .sqlvalue(@$_POST["xindex_struct"], false);
  };
  $pk .= ")";
  return $pk;
}
 ?>
