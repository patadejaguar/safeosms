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
<title>matriz -- eacp_config_bases_de_integracion</title>
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
<table class="bd"  ><tr><td class="hr"><h2>Integracion de Bases de Operacion</h2></td></tr></table>
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
    case "view":
      viewrec($recid);
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
<tr><td>Origen de Datos: eacp_config_bases_de_integracion</td></tr>
<tr><td>Registros Mostrados <?php echo $startrec + 1 ?> - <?php echo $reccount ?> of <?php echo $count ?></td></tr>
</table>
<hr size="1" noshade>
<form action="eacp_config_bases_de_integracion.raw.php" method="post">
<table class="bd"     >
<tr>
<td><b>Fltro Personal</b>&nbsp;</td>
<td><input type="text" name="filter" value="<?php echo $filter ?>"></td>
<td><select name="filter_field">
<option value="">Todos los Registros</option>
<option value="<?php echo "codigo_de_base" ?>"<?php if ($filterfield == "codigo_de_base") { echo "selected"; } ?>><?php echo htmlspecialchars("Codigo") ?></option>
<option value="<?php echo "descripcion" ?>"<?php if ($filterfield == "descripcion") { echo "selected"; } ?>><?php echo htmlspecialchars("Descripcion") ?></option>
</select></td>
<td><input type="checkbox" name="wholeonly"<?php echo $checkstr ?>>Palabras Exactas solamente</td>
</td></tr>
<tr>
<td>&nbsp;</td>
<td><input type="submit" name="action" value="Aplicar Filtro"></td>
<td><a href="eacp_config_bases_de_integracion.raw.php?a=reset">Quitar Filtro</a></td>
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
<td class="hr">&nbsp;</td>
<td class="hr"><a class="hr" href="eacp_config_bases_de_integracion.raw.php?order=<?php echo "codigo_de_base" ?>&type=<?php echo $ordtypestr ?>"><?php echo htmlspecialchars("Codigo") ?></a></td>
<td class="hr"><a class="hr" href="eacp_config_bases_de_integracion.raw.php?order=<?php echo "descripcion" ?>&type=<?php echo $ordtypestr ?>"><?php echo htmlspecialchars("Descripcion") ?></a></td>
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
<td class="<?php echo $style ?>"><a href="eacp_config_bases_de_integracion.raw.php?a=view&recid=<?php echo $i ?>">Vista Previa</a></td>
<td class="<?php echo $style ?>"><a href="eacp_config_bases_de_integracion.raw.php?a=edit&recid=<?php echo $i ?>">Editar</a></td>
<td class="<?php echo $style ?>"><a href="eacp_config_bases_de_integracion.raw.php?a=del&recid=<?php echo $i ?>">Eliminar</a></td>
<td class="<?php echo $style ?>"><?php echo htmlspecialchars($row["codigo_de_base"]) ?></td>
<td class="<?php echo $style ?>"><?php echo htmlspecialchars($row["descripcion"]) ?></td>
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
<td class="hr"><?php echo htmlspecialchars("Codigo")."&nbsp;" ?></td>
<td class="dr"><?php echo htmlspecialchars($row["codigo_de_base"]) ?></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("Descripcion")."&nbsp;" ?></td>
<td class="dr"><?php echo htmlspecialchars($row["descripcion"]) ?></td>
</tr>
</table>
<?php } ?>

<?php function showroweditor($row, $iseditmode)
  {
  global $conn;
?>
<table class="tbl"       >
<tr>
<td class="hr"><?php echo htmlspecialchars("Codigo")."&nbsp;" ?></td>
<td class="dr"><input type="text" name="codigo_de_base" value="<?php echo str_replace('"', '&quot;', trim($row["codigo_de_base"])) ?>"></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("Descripcion")."&nbsp;" ?></td>
<td class="dr"><textarea cols="35" rows="4" name="descripcion" maxlength="100"><?php echo str_replace('"', '&quot;', trim($row["descripcion"])) ?></textarea></td>
</tr>
</table>
<?php } ?>

<?php function showpagenav($page, $pagecount)
{
?>
<table class="bd"     >
<tr>
<td><a href="eacp_config_bases_de_integracion.raw.php?a=add">Agregar Registro</a>&nbsp;</td>
<?php if ($page > 1) { ?>
<td><a href="eacp_config_bases_de_integracion.raw.php?page=<?php echo $page - 1 ?>">&lt;&lt;&nbsp;Anterior</a>&nbsp;</td>
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
<td><a href="eacp_config_bases_de_integracion.raw.php?page=<?php echo $j ?>"><?php echo $j ?></a></td>
<?php } } } else { ?>
<td><a href="eacp_config_bases_de_integracion.raw.php?page=<?php echo $startpage ?>"><?php echo $startpage ."..." .$count ?></a></td>
<?php } } } ?>
<?php if ($page < $pagecount) { ?>
<td>&nbsp;<a href="eacp_config_bases_de_integracion.raw.php?page=<?php echo $page + 1 ?>">Siguiente&nbsp;&gt;&gt;</a>&nbsp;</td>
<?php } ?>
</tr>
</table>
<?php } ?>

<?php function showrecnav($a, $recid, $count)
{
?>
<table class="bd"     >
<tr>
<td><a href="eacp_config_bases_de_integracion.raw.php">Inicio de Pagina</a></td>
<?php if ($recid > 0) { ?>
<td><a href="eacp_config_bases_de_integracion.raw.php?a=<?php echo $a ?>&recid=<?php echo $recid - 1 ?>">Primer Registro</a></td>
<?php } if ($recid < $count - 1) { ?>
<td><a href="eacp_config_bases_de_integracion.raw.php?a=<?php echo $a ?>&recid=<?php echo $recid + 1 ?>">Proximo Registro</a></td>
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
<td><a href="eacp_config_bases_de_integracion.raw.php">Inicio de Pagina</a></td>
</tr>
</table>
<hr size="1" noshade>
<form enctype="multipart/form-data" action="eacp_config_bases_de_integracion.raw.php" method="post">
<p><input type="hidden" name="sql" value="insert"></p>
<?php
$row = array(
  "codigo_de_base" => "",
  "descripcion" => "");
showroweditor($row, false);
?>
<p><input type="submit" name="action" value="Guardar y regresar"></p>
</form>
<?php } ?>

<?php function viewrec($recid)
{
  $res = sql_select();
  $count = sql_getrecordcount();
  mysql_data_seek($res, $recid);
  $row = mysql_fetch_assoc($res);
  showrecnav("view", $recid, $count);
?>
<br>
<?php showrow($row, $recid) ?>
<br>
<hr size="1" noshade>
<table class="bd"     >
<tr>
<td><a href="eacp_config_bases_de_integracion.raw.php?a=add">Agregar Registro</a></td>
<td><a href="eacp_config_bases_de_integracion.raw.php?a=edit&recid=<?php echo $recid ?>">Editar Registro</a></td>
<td><a href="eacp_config_bases_de_integracion.raw.php?a=del&recid=<?php echo $recid ?>">Eliminar Registro</a></td>
</tr>
</table>
<?php
  mysql_free_result($res);
} ?>

<?php function editrec($recid)
{
  $res = sql_select();
  $count = sql_getrecordcount();
  mysql_data_seek($res, $recid);
  $row = mysql_fetch_assoc($res);
  showrecnav("edit", $recid, $count);
?>
<br>
<form enctype="multipart/form-data" action="eacp_config_bases_de_integracion.raw.php" method="post">
<input type="hidden" name="sql" value="update">
<input type="hidden" name="xcodigo_de_base" value="<?php echo $row["codigo_de_base"] ?>">
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
<form action="eacp_config_bases_de_integracion.raw.php" method="post">
<input type="hidden" name="sql" value="delete">
<input type="hidden" name="xcodigo_de_base" value="<?php echo $row["codigo_de_base"] ?>">
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
  $sql = "SELECT `codigo_de_base`, `descripcion` FROM `eacp_config_bases_de_integracion`";
  if (isset($filterstr) && $filterstr!='' && isset($filterfield) && $filterfield!='') {
    $sql .= " where " .sqlstr($filterfield) ." like '" .$filterstr ."'";
  } elseif (isset($filterstr) && $filterstr!='') {
    $sql .= " where (`codigo_de_base` like '" .$filterstr ."') or (`descripcion` like '" .$filterstr ."')";
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
  $sql = "SELECT COUNT(*) FROM `eacp_config_bases_de_integracion`";
  if (isset($filterstr) && $filterstr!='' && isset($filterfield) && $filterfield!='') {
    $sql .= " where " .sqlstr($filterfield) ." like '" .$filterstr ."'";
  } elseif (isset($filterstr) && $filterstr!='') {
    $sql .= " where (`codigo_de_base` like '" .$filterstr ."') or (`descripcion` like '" .$filterstr ."')";
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

  $sql = "insert into `eacp_config_bases_de_integracion` (`codigo_de_base`, `descripcion`) values (" .sqlvalue(@$_POST["codigo_de_base"], false).", " .sqlvalue(@$_POST["descripcion"], true).")";
  mysql_query($sql, $conn) or die(mysql_error());
}

function sql_update()
{
  global $conn;
  global $_POST;

  $sql = "update `eacp_config_bases_de_integracion` set `codigo_de_base`=" .sqlvalue(@$_POST["codigo_de_base"], false).", `descripcion`=" .sqlvalue(@$_POST["descripcion"], true) ." where " .primarykeycondition();
  mysql_query($sql, $conn) or die(mysql_error());
}

function sql_delete()
{
  global $conn;

  $sql = "delete from `eacp_config_bases_de_integracion` where " .primarykeycondition();
  mysql_query($sql, $conn) or die(mysql_error());
}
function primarykeycondition()
{
  global $_POST;
  $pk = "";
  $pk .= "(`codigo_de_base`";
  if (@$_POST["xcodigo_de_base"] == "") {
    $pk .= " IS NULL";
  }else{
  $pk .= " = " .sqlvalue(@$_POST["xcodigo_de_base"], false);
  };
  $pk .= ")";
  return $pk;
}
 ?>
