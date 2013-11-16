<?php

function q($q, $print = false, $execute = true) {
  if ($print)
    pr($q);
  if ($execute)
    return db()->select($q);
}

function request($k) {
  return htmlspecialchars($_REQUEST[$k], null, CHARSET);
}

function params($n = false) {
  return $n === false ? O::get('Req')->params : O::get('Req')->params[$n];
}

function ob_get($func, $params) {
  foreach ($params as &$param)
    $param = Arr::formatValue($param);
  ob_start();
  eval("$func(" . implode(', ', $params) . ");");
  $c = ob_get_contents();
  ob_end_clean();
  return $c;
}

/**
 * @return DbSite
 */
function db() {
  $db = O::get('DbSite');
  if (!$db->link) {
    $db->disconnect();
    output('disconnect');
    $db = O::get('DbSite');
    if (!$db->link) {
      die2('=((');
    }
  }
  return $db;
}

/**
 * @return Tt
 */
function Tt() {
  return O::get('Tt');
}

/**
 * @return Path
 */
function Path() {
  return O::get('Path');
}

function none() {}

function quoting(&$v) {
  $v = "'".mysql_real_escape_string($v)."'";
}

function _toLower($s) {
  return mb_strtolower($s, CHARSET);
}

function toLower($s) {
  return is_array($s) ? array_map('_toLower', $s) : toLower($s);
}

function sys($cmd, $output = false) {
  if ($output and !getConstant('OUTPUT_DISABLE')) output('Cmd: '.$cmd, $output);
  LogWriter::str('sys', $cmd);
  ob_start();
  system($cmd);
  $c = ob_get_contents();
  ob_end_clean();
  if ($output and !getConstant('OUTPUT_DISABLE') and $c) output("Cmd output: $c", $output);
  return $c;
}

function red($t) {
  //if (!R::get('plainText'))
  print "<h1 style='color:#FF0000'>";
  //if (R::get('plainText')) $t = (new CliColors)->getColoredString($t, 'red');
  pr($t);
  //if (!R::get('plainText'))
  print "</h1>";
}
