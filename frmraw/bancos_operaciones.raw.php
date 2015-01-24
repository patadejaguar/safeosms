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
<title>matriz -- bancos_operaciones</title>
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
<table class="bd"  ><tr><td class="hr"><h2>Modificacion de Movimientos Bancarios</h2></td></tr></table>
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
    case "update":
      sql_update();
      break;
  }

  switch ($a) {
    case "view":
      viewrec($recid);
      break;
    case "edit":
      editrec($recid);
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
<tr><td>Origen de Datos: bancos_operaciones</td></tr>
<tr><td>Registros Mostrados <?php echo $startrec + 1 ?> - <?php echo $reccount ?> of <?php echo $count ?></td></tr>
</table>
<hr size="1" noshade>
<form action="bancos_operaciones.raw.php" method="post">
<table class="bd"     >
<tr>
<td><b>Fltro Personal</b>&nbsp;</td>
<td><input type="text" name="filter" value="<?php echo $filter ?>"></td>
<td><select name="filter_field">
<option value="">Todos los Registros</option>
<option value="<?php echo "tipo_operacion" ?>"<?php if ($filterfield == "tipo_operacion") { echo "selected"; } ?>><?php echo htmlspecialchars("Tipo de Operacion") ?></option>
<option value="<?php echo "numero_de_documento" ?>"<?php if ($filterfield == "numero_de_documento") { echo "selected"; } ?>><?php echo htmlspecialchars("Documento") ?></option>
<option value="<?php echo "lp_cuenta_bancaria" ?>"<?php if ($filterfield == "lp_cuenta_bancaria") { echo "selected"; } ?>><?php echo htmlspecialchars("cuenta_bancaria") ?></option>
<option value="<?php echo "recibo_relacionado" ?>"<?php if ($filterfield == "recibo_relacionado") { echo "selected"; } ?>><?php echo htmlspecialchars("Recibo Relacionado") ?></option>
<option value="<?php echo "fecha_expedicion" ?>"<?php if ($filterfield == "fecha_expedicion") { echo "selected"; } ?>><?php echo htmlspecialchars("Fecha") ?></option>
<option value="<?php echo "beneficiario" ?>"<?php if ($filterfield == "beneficiario") { echo "selected"; } ?>><?php echo htmlspecialchars("beneficiario") ?></option>
<option value="<?php echo "monto_descontado" ?>"<?php if ($filterfield == "monto_descontado") { echo "selected"; } ?>><?php echo htmlspecialchars("Monto Descontado") ?></option>
<option value="<?php echo "monto_real" ?>"<?php if ($filterfield == "monto_real") { echo "selected"; } ?>><?php echo htmlspecialchars("Monto Real") ?></option>
<option value="<?php echo "estatus" ?>"<?php if ($filterfield == "estatus") { echo "selected"; } ?>><?php echo htmlspecialchars("estatus") ?></option>
<option value="<?php echo "lp_idusuario" ?>"<?php if ($filterfield == "lp_idusuario") { echo "selected"; } ?>><?php echo htmlspecialchars("idusuario") ?></option>
<option value="<?php echo "lp_usuario_autorizo" ?>"<?php if ($filterfield == "lp_usuario_autorizo") { echo "selected"; } ?>><?php echo htmlspecialchars("usuario_autorizo") ?></option>
</select></td>
<td><input type="checkbox" name="wholeonly"<?php echo $checkstr ?>>Palabras Exactas solamente</td>
</td></tr>
<tr>
<td>&nbsp;</td>
<td><input type="submit" name="action" value="Aplicar Filtro"></td>
<td><a href="bancos_operaciones.raw.php?a=reset">Quitar Filtro</a></td>
</tr>
</table>
</form>
<hr size="1" noshade>
<?php showpagenav($page, $pagecount); ?>
<br>
<table class="tbl"       >
<tr>
<td class="hr">&nbsp;</td>
<td class="hr">&nbsp;</td>
<td class="hr"><a class="hr" href="bancos_operaciones.raw.php?order=<?php echo "tipo_operacion" ?>&type=<?php echo $ordtypestr ?>"><?php echo htmlspecialchars("Tipo de Operacion") ?></a></td>
<td class="hr"><a class="hr" href="bancos_operaciones.raw.php?order=<?php echo "numero_de_documento" ?>&type=<?php echo $ordtypestr ?>"><?php echo htmlspecialchars("Documento") ?></a></td>
<td class="hr"><a class="hr" href="bancos_operaciones.raw.php?order=<?php echo "lp_cuenta_bancaria" ?>&type=<?php echo $ordtypestr ?>"><?php echo htmlspecialchars("cuenta_bancaria") ?></a></td>
<td class="hr"><a class="hr" href="bancos_operaciones.raw.php?order=<?php echo "recibo_relacionado" ?>&type=<?php echo $ordtypestr ?>"><?php echo htmlspecialchars("Recibo Relacionado") ?></a></td>
<td class="hr"><a class="hr" href="bancos_operaciones.raw.php?order=<?php echo "fecha_expedicion" ?>&type=<?php echo $ordtypestr ?>"><?php echo htmlspecialchars("Fecha") ?></a></td>
<td class="hr"><a class="hr" href="bancos_operaciones.raw.php?order=<?php echo "beneficiario" ?>&type=<?php echo $ordtypestr ?>"><?php echo htmlspecialchars("beneficiario") ?></a></td>
<td class="hr"><a class="hr" href="bancos_operaciones.raw.php?order=<?php echo "monto_descontado" ?>&type=<?php echo $ordtypestr ?>"><?php echo htmlspecialchars("Monto Descontado") ?></a></td>
<td class="hr"><a class="hr" href="bancos_operaciones.raw.php?order=<?php echo "monto_real" ?>&type=<?php echo $ordtypestr ?>"><?php echo htmlspecialchars("Monto Real") ?></a></td>
<td class="hr"><a class="hr" href="bancos_operaciones.raw.php?order=<?php echo "estatus" ?>&type=<?php echo $ordtypestr ?>"><?php echo htmlspecialchars("estatus") ?></a></td>
<td class="hr"><a class="hr" href="bancos_operaciones.raw.php?order=<?php echo "lp_idusuario" ?>&type=<?php echo $ordtypestr ?>"><?php echo htmlspecialchars("idusuario") ?></a></td>
<td class="hr"><a class="hr" href="bancos_operaciones.raw.php?order=<?php echo "lp_usuario_autorizo" ?>&type=<?php echo $ordtypestr ?>"><?php echo htmlspecialchars("usuario_autorizo") ?></a></td>
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
<td class="<?php echo $style ?>"><a href="bancos_operaciones.raw.php?a=view&recid=<?php echo $i ?>">Vista Previa</a></td>
<td class="<?php echo $style ?>"><a href="bancos_operaciones.raw.php?a=edit&recid=<?php echo $i ?>">Editar</a></td>
<td class="<?php echo $style ?>"><?php echo htmlspecialchars($row["tipo_operacion"]) ?></td>
<td class="<?php echo $style ?>"><?php echo htmlspecialchars($row["numero_de_documento"]) ?></td>
<td class="<?php echo $style ?>"><?php echo htmlspecialchars($row["lp_cuenta_bancaria"]) ?></td>
<td class="<?php echo $style ?>"><?php echo htmlspecialchars($row["recibo_relacionado"]) ?></td>
<td class="<?php echo $style ?>"><?php echo htmlspecialchars($row["fecha_expedicion"]) ?></td>
<td class="<?php echo $style ?>"><?php echo htmlspecialchars($row["beneficiario"]) ?></td>
<td class="<?php echo $style ?>"><?php echo htmlspecialchars($row["monto_descontado"]) ?></td>
<td class="<?php echo $style ?>"><?php echo htmlspecialchars($row["monto_real"]) ?></td>
<td class="<?php echo $style ?>"><?php echo htmlspecialchars($row["estatus"]) ?></td>
<td class="<?php echo $style ?>"><?php echo htmlspecialchars($row["lp_idusuario"]) ?></td>
<td class="<?php echo $style ?>"><?php echo htmlspecialchars($row["lp_usuario_autorizo"]) ?></td>
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
<td class="hr"><?php echo htmlspecialchars("Tipo de Operacion")."&nbsp;" ?></td>
<td class="dr"><?php echo htmlspecialchars($row["tipo_operacion"]) ?></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("Documento")."&nbsp;" ?></td>
<td class="dr"><?php echo htmlspecialchars($row["numero_de_documento"]) ?></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("cuenta_bancaria")."&nbsp;" ?></td>
<td class="dr"><?php echo htmlspecialchars($row["lp_cuenta_bancaria"]) ?></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("Recibo Relacionado")."&nbsp;" ?></td>
<td class="dr"><?php echo htmlspecialchars($row["recibo_relacionado"]) ?></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("Fecha")."&nbsp;" ?></td>
<td class="dr"><?php echo htmlspecialchars($row["fecha_expedicion"]) ?></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("beneficiario")."&nbsp;" ?></td>
<td class="dr"><?php echo htmlspecialchars($row["beneficiario"]) ?></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("Monto Descontado")."&nbsp;" ?></td>
<td class="dr"><?php echo htmlspecialchars($row["monto_descontado"]) ?></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("Monto Real")."&nbsp;" ?></td>
<td class="dr"><?php echo htmlspecialchars($row["monto_real"]) ?></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("estatus")."&nbsp;" ?></td>
<td class="dr"><?php echo htmlspecialchars($row["estatus"]) ?></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("idusuario")."&nbsp;" ?></td>
<td class="dr"><?php echo htmlspecialchars($row["lp_idusuario"]) ?></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("usuario_autorizo")."&nbsp;" ?></td>
<td class="dr"><?php echo htmlspecialchars($row["lp_usuario_autorizo"]) ?></td>
</tr>
</table>
<?php } ?>

<?php function showroweditor($row, $iseditmode)
  {
  global $conn;
?>
<table class="tbl"      >
<tr>
<td class="hr"><?php echo htmlspecialchars("idcontrol")."&nbsp;" ?></td>
<td class="dr"><input type="text" name="idcontrol" value="<?php echo str_replace('"', '&quot;', trim($row["idcontrol"])) ?>"></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("Tipo de Operacion")."&nbsp;" ?></td>
<td class="dr"><select name="tipo_operacion">
<option value=""></option>
<?php
  $lookupvalues = array("cheque","deposito","comision");

  reset($lookupvalues);
  foreach($lookupvalues as $val){
  $caption = $val;
  if ($row["tipo_operacion"] == $val) {$selstr = " selected"; } else {$selstr = ""; }
 ?><option value="<?php echo $val ?>"<?php echo $selstr ?>><?php echo $caption ?></option>
<?php } ?></select>
</td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("Documento")."&nbsp;" ?></td>
<td class="dr"><input type="text" name="numero_de_documento" maxlength="20" value="<?php echo str_replace('"', '&quot;', trim($row["numero_de_documento"])) ?>"></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("cuenta_bancaria")."&nbsp;" ?></td>
<td class="dr"><select name="cuenta_bancaria">
<option value=""></option>
<?php
  $sql = "select `idbancos_cuentas`, `descripcion_cuenta` from `bancos_cuentas`";
  $res = mysql_query($sql, $conn) or die(mysql_error());

  while ($lp_row = mysql_fetch_assoc($res)){
  $val = $lp_row["idbancos_cuentas"];
  $caption = $lp_row["descripcion_cuenta"];
  if ($row["cuenta_bancaria"] == $val) {$selstr = " selected"; } else {$selstr = ""; }
 ?><option value="<?php echo $val ?>"<?php echo $selstr ?>><?php echo $caption ?></option>
<?php } ?></select>
</td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("Recibo Relacionado")."&nbsp;" ?></td>
<td class="dr"><input type="text" name="recibo_relacionado" value="<?php echo str_replace('"', '&quot;', trim($row["recibo_relacionado"])) ?>"></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("Fecha")."&nbsp;" ?></td>
<td class="dr"><input type="text" name="fecha_expedicion" value="<?php echo str_replace('"', '&quot;', trim($row["fecha_expedicion"])) ?>"></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("beneficiario")."&nbsp;" ?></td>
<td class="dr"><textarea cols="35" rows="4" name="beneficiario" maxlength="80"><?php echo str_replace('"', '&quot;', trim($row["beneficiario"])) ?></textarea></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("Monto Descontado")."&nbsp;" ?></td>
<td class="dr"><input type="text" name="monto_descontado" value="<?php echo str_replace('"', '&quot;', trim($row["monto_descontado"])) ?>"></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("Monto Real")."&nbsp;" ?></td>
<td class="dr"><input type="text" name="monto_real" value="<?php echo str_replace('"', '&quot;', trim($row["monto_real"])) ?>"></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("estatus")."&nbsp;" ?></td>
<td class="dr"><select name="estatus">
<option value=""></option>
<?php
  $lookupvalues = array("autorizado","noautorizado","cancelado");

  reset($lookupvalues);
  foreach($lookupvalues as $val){
  $caption = $val;
  if ($row["estatus"] == $val) {$selstr = " selected"; } else {$selstr = ""; }
 ?><option value="<?php echo $val ?>"<?php echo $selstr ?>><?php echo $caption ?></option>
<?php } ?></select>
</td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("usuario_autorizo")."&nbsp;" ?></td>
<td class="dr"><select name="usuario_autorizo">
<option value=""></option>
<?php
  $sql = "select `idusuarios`, `nombreusuario` from `usuarios`";
  $res = mysql_query($sql, $conn) or die(mysql_error());

  while ($lp_row = mysql_fetch_assoc($res)){
  $val = $lp_row["idusuarios"];
  $caption = $lp_row["nombreusuario"];
  if ($row["usuario_autorizo"] == $val) {$selstr = " selected"; } else {$selstr = ""; }
 ?><option value="<?php echo $val ?>"<?php echo $selstr ?>><?php echo $caption ?></option>
<?php } ?></select>
</td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("eacp")."&nbsp;" ?></td>
<td class="dr"><input type="text" name="eacp" value="<?php echo str_replace('"', '&quot;', trim($row["eacp"])) ?>"></td>
</tr>
<tr>
<td class="hr"><?php echo htmlspecialchars("sucursal")."&nbsp;" ?></td>
<td class="dr"><input type="text" name="sucursal" maxlength="15" value="<?php echo str_replace('"', '&quot;', trim($row["sucursal"])) ?>"></td>
</tr>
</table>
<?php } ?>

<?php function showpagenav($page, $pagecount)
{
?>
<table class="bd"     >
<tr>
<?php if ($page > 1) { ?>
<td><a href="bancos_operaciones.raw.php?page=<?php echo $page - 1 ?>">&lt;&lt;&nbsp;Anterior</a>&nbsp;</td>
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
<td><a href="bancos_operaciones.raw.php?page=<?php echo $j ?>"><?php echo $j ?></a></td>
<?php } } } else { ?>
<td><a href="bancos_operaciones.raw.php?page=<?php echo $startpage ?>"><?php echo $startpage ."..." .$count ?></a></td>
<?php } } } ?>
<?php if ($page < $pagecount) { ?>
<td>&nbsp;<a href="bancos_operaciones.raw.php?page=<?php echo $page + 1 ?>">Siguiente&nbsp;&gt;&gt;</a>&nbsp;</td>
<?php } ?>
</tr>
</table>
<?php } ?>

<?php function showrecnav($a, $recid, $count)
{
?>
<table class="bd"     >
<tr>
<td><a href="bancos_operaciones.raw.php">Inicio de Pagina</a></td>
<?php if ($recid > 0) { ?>
<td><a href="bancos_operaciones.raw.php?a=<?php echo $a ?>&recid=<?php echo $recid - 1 ?>">Primer Registro</a></td>
<?php } if ($recid < $count - 1) { ?>
<td><a href="bancos_operaciones.raw.php?a=<?php echo $a ?>&recid=<?php echo $recid + 1 ?>">Proximo Registro</a></td>
<?php } ?>
</tr>
</table>
<hr size="1" noshade>
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
<td><a href="bancos_operaciones.raw.php?a=edit&recid=<?php echo $recid ?>">Editar Registro</a></td>
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
<form enctype="multipart/form-data" action="bancos_operaciones.raw.php" method="post">
<input type="hidden" name="sql" value="update">
<input type="hidden" name="xidcontrol" value="<?php echo $row["idcontrol"] ?>">
<?php showroweditor($row, true); ?>
<p><input type="submit" name="action" value="Guardar y regresar"></p>
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
  $sql = "SELECT * FROM (SELECT t1.`idcontrol`, t1.`tipo_operacion`, t1.`numero_de_documento`, t1.`cuenta_bancaria`, lp3.`descripcion_cuenta` AS `lp_cuenta_bancaria`, t1.`recibo_relacionado`, t1.`fecha_expedicion`, t1.`beneficiario`, t1.`monto_descontado`, t1.`monto_real`, t1.`estatus`, t1.`idusuario`, lp10.`nombreusuario` AS `lp_idusuario`, t1.`usuario_autorizo`, lp11.`nombreusuario` AS `lp_usuario_autorizo`, t1.`eacp`, t1.`sucursal` FROM `bancos_operaciones` AS t1 LEFT OUTER JOIN `bancos_cuentas` AS lp3 ON (t1.`cuenta_bancaria` = lp3.`idbancos_cuentas`) LEFT OUTER JOIN `usuarios` AS lp10 ON (t1.`idusuario` = lp10.`idusuarios`) LEFT OUTER JOIN `usuarios` AS lp11 ON (t1.`usuario_autorizo` = lp11.`idusuarios`)) subq";
  if (isset($filterstr) && $filterstr!='' && isset($filterfield) && $filterfield!='') {
    $sql .= " where " .sqlstr($filterfield) ." like '" .$filterstr ."'";
  } elseif (isset($filterstr) && $filterstr!='') {
    $sql .= " where (`tipo_operacion` like '" .$filterstr ."') or (`numero_de_documento` like '" .$filterstr ."') or (`lp_cuenta_bancaria` like '" .$filterstr ."') or (`recibo_relacionado` like '" .$filterstr ."') or (`fecha_expedicion` like '" .$filterstr ."') or (`beneficiario` like '" .$filterstr ."') or (`monto_descontado` like '" .$filterstr ."') or (`monto_real` like '" .$filterstr ."') or (`estatus` like '" .$filterstr ."') or (`lp_idusuario` like '" .$filterstr ."') or (`lp_usuario_autorizo` like '" .$filterstr ."')";
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
  $sql = "SELECT COUNT(*) FROM (SELECT t1.`idcontrol`, t1.`tipo_operacion`, t1.`numero_de_documento`, t1.`cuenta_bancaria`, lp3.`descripcion_cuenta` AS `lp_cuenta_bancaria`, t1.`recibo_relacionado`, t1.`fecha_expedicion`, t1.`beneficiario`, t1.`monto_descontado`, t1.`monto_real`, t1.`estatus`, t1.`idusuario`, lp10.`nombreusuario` AS `lp_idusuario`, t1.`usuario_autorizo`, lp11.`nombreusuario` AS `lp_usuario_autorizo`, t1.`eacp`, t1.`sucursal` FROM `bancos_operaciones` AS t1 LEFT OUTER JOIN `bancos_cuentas` AS lp3 ON (t1.`cuenta_bancaria` = lp3.`idbancos_cuentas`) LEFT OUTER JOIN `usuarios` AS lp10 ON (t1.`idusuario` = lp10.`idusuarios`) LEFT OUTER JOIN `usuarios` AS lp11 ON (t1.`usuario_autorizo` = lp11.`idusuarios`)) subq";
  if (isset($filterstr) && $filterstr!='' && isset($filterfield) && $filterfield!='') {
    $sql .= " where " .sqlstr($filterfield) ." like '" .$filterstr ."'";
  } elseif (isset($filterstr) && $filterstr!='') {
    $sql .= " where (`tipo_operacion` like '" .$filterstr ."') or (`numero_de_documento` like '" .$filterstr ."') or (`lp_cuenta_bancaria` like '" .$filterstr ."') or (`recibo_relacionado` like '" .$filterstr ."') or (`fecha_expedicion` like '" .$filterstr ."') or (`beneficiario` like '" .$filterstr ."') or (`monto_descontado` like '" .$filterstr ."') or (`monto_real` like '" .$filterstr ."') or (`estatus` like '" .$filterstr ."') or (`lp_idusuario` like '" .$filterstr ."') or (`lp_usuario_autorizo` like '" .$filterstr ."')";
  }
  $res = mysql_query($sql, $conn) or die(mysql_error());
  $row = mysql_fetch_assoc($res);
  reset($row);
  return current($row);
}

function sql_update()
{
  global $conn;
  global $_POST;

  $sql = "update `bancos_operaciones` set `idcontrol`=" .sqlvalue(@$_POST["idcontrol"], false).", `tipo_operacion`=" .sqlvalue(@$_POST["tipo_operacion"], true).", `numero_de_documento`=" .sqlvalue(@$_POST["numero_de_documento"], true).", `cuenta_bancaria`=" .sqlvalue(@$_POST["cuenta_bancaria"], false).", `recibo_relacionado`=" .sqlvalue(@$_POST["recibo_relacionado"], false).", `fecha_expedicion`=" .sqlvalue(@$_POST["fecha_expedicion"], true).", `beneficiario`=" .sqlvalue(@$_POST["beneficiario"], true).", `monto_descontado`=" .sqlvalue(@$_POST["monto_descontado"], false).", `monto_real`=" .sqlvalue(@$_POST["monto_real"], false).", `estatus`=" .sqlvalue(@$_POST["estatus"], true).", `usuario_autorizo`=" .sqlvalue(@$_POST["usuario_autorizo"], false).", `eacp`=" .sqlvalue(@$_POST["eacp"], true).", `sucursal`=" .sqlvalue(@$_POST["sucursal"], true) ." where " .primarykeycondition();
  mysql_query($sql, $conn) or die(mysql_error());
}
function primarykeycondition()
{
  global $_POST;
  $pk = "";
  $pk .= "(`idcontrol`";
  if (@$_POST["xidcontrol"] == "") {
    $pk .= " IS NULL";
  }else{
  $pk .= " = " .sqlvalue(@$_POST["xidcontrol"], false);
  };
  $pk .= ")";
  return $pk;
}
 ?>
