<?php

function getConstant($name) {
  if (defined($name)) return constant($name);
  return false;
}

function setConstant($name, $value) {
  if (!defined($name)) define($name, $value);
}

function htmlspecialcharsR_a(&$item) {
  if (is_object($item)) return;
  $item = htmlspecialchars($item);
}

function htmlspecialcharsR(&$data) {
  if (!is_array($data)) return;
  array_walk_recursive($data, 'htmlspecialcharsR_a');
}

/**
 * Обёртка для HTML-вывода print_r()
 *
 * @param  $var
 */
function pr($var, $html = true, $trace = true, $offset = 0) {
  if (!getConstant('IS_DEBUG')) return;
  if (R::get('plainText')) $html = false;
  if ($html) print '<pre>';
  if ($html) htmlspecialcharsR($var);
  print print_r($var, true);
  if ($html) print '</pre>';
  if (!$html) print "\n";
  if ($trace) {
    $backtrace = getBacktrace($html, $offset);
    if (!$html) $backtrace = CliColors::colored($backtrace, 'green');
    print $backtrace;
  }
  print $html ? '<hr />' : "-----------END-OF-PR----------\n";
}

function prr($var, $html = true) {
  pr($var, $html, true);
}

function prrc($var) {
  print CliColors::colored(print_r($var, true), 'cyan')."\n--\n";
}

function getPrr($v, $html = true) {
  ob_start();
  prr($v, $html); // var_export($v, true); - в случаях рекурсий объектов вызывает fatal-ошибку
  $c = ob_get_contents();
  ob_end_clean();
  return $c;
}

function getPr($v, $html = true) {
  ob_start();
  pr($v, $html); // var_export($v, true); - в случаях рекурсий объектов вызывает fatal-ошибку
  $c = ob_get_contents();
  ob_end_clean();
  return $c;
}

function output($str, $output = false, $forcePlain = true) {
  if (LOG_OUTPUT === true or $output) {
    print ((R::get('plainText') or $forcePlain) ? "<" : "<p>") //
      .$str. //
      ((R::get('plainText') or $forcePlain) ? ">\n" : "</p>");
  }
  LogWriter::str('output', $str);
}

function output2($str, $output = false, $forcePlain = true) {
  outputColor($str, 'cyan', $output, $forcePlain);
}

function output3($str, $output = false, $forcePlain = true) {
  outputColor($str, 'yellow', $output, $forcePlain);
}

function outputColor($str, $color, $output = false, $forcePlain = true) {
  if (LOG_OUTPUT === true or $output) {
    print
      ((R::get('plainText') or $forcePlain) ? "<" : "<p>"). //
      CliColors::colored($str, $color). //
      ((R::get('plainText') or $forcePlain) ? ">\n" : "</p>");
  }
  LogWriter::str('output', $str);
}

function getBacktrace($html = true, $offset = 0) {
  return _getBacktrace(debug_backtrace(), $html, $offset);
}

function _getBacktrace(array $trace, $html = true, $offset = 0, $length = 0) {
  $s = '';
  if ($length and $length < count($trace)) {
    $l = $offset + $length;
  }
  else {
    $l = count($trace);
  }
  for ($i = $offset; $i < $l; $i++) {
    if (isset($trace[$i]['file'])) {
      $s .= $trace[$i]['file'].':'.$trace[$i]['line'].($html ? '<br />' : "\n");
    }
    elseif (isset($trace[$i]['class'])) {
      continue;
      //$s .= $trace[$i]['class'].$trace[$i]['type'].$trace[$i]['function'].'('.implode(', ', $trace[$i]['args']).')'.($html ? '<br />' : "\n");
    }
  }
  return $s;
}

function getTraceText(Exception $e, $html = true) {
  $trace = [
    [
      'file' => $e->getFile(),
      'line' => $e->getLine()
    ]
  ];
  $s = '';
  foreach (Arr::append($trace, $e->getTrace()) as $v) {
    if (isset($v['file'])) {
      $s .= $v['file'].':'.$v['line'].($html ? '<br />' : "\n");
    }
  }
  return $s;
}

function getFullTrace(Exception $e) {
  return Arr::append([
    [
      'file' => $e->getFile(),
      'line' => $e->getLine()
    ]
  ], method_exists($e, 'getBacktrace') ? $e->getBacktrace() : $e->getTrace());
}

function die2($t = '', $html = true) {
  sendHeader();
  pr($t, $html, true, 2);
  exit(1);
}

function die3($t) {
  die2(htmlspecialchars($t));
}

function sendHeader() {
  if (!headers_sent()) header('Content-type: text/html; charset='.CHARSET);
}

/**
 * Получает текущее значение секунд и милесекунд
 *
 * @return float
 */
function getMicrotime() {
  list($usec, $sec) = explode(" ", microtime());
  return ((float)$usec + (float)$sec);
}

function setProcessTimeStart($k = '') {
  R::set('processTimeStart'.$k, getMicrotime());
}

function getProcessTime($k = '') {
  return getMicrotime() - R::get('processTimeStart'.$k);
}

/**
 * Возвращает тип операционной системы (win/unix)
 *
 * @return string win/unix
 */
function getOS() {
  if ((isset($_SERVER['SERVER_SOFTWARE']) and strstr($_SERVER['SERVER_SOFTWARE'], 'Win32')) or (isset($_SERVER['OS']) and strstr($_SERVER['OS'], 'Win'))
  ) return 'win';
  else
    return 'linux';
}

function redirect($path, $forceHttp = false) {
  if (!strstr($path, 'http://') and !strstr($path, 'https://')) $path = '/'.ltrim($path, '/');
  (getConstant('JS_REDIRECT') and !$forceHttp) ? jsRedirect($path) : header('Location: '.$path);
  print "\n"; // если после хедера нет никакого вывода, редирект не осуществляется
}

function jsRedirect($path) {
  header('Location: /default/jsRedirect?r='.urlencode($path));
}

function set_time_limit_q($n) {
  if (ini_get('safe_mode') or !function_exists('set_time_limit')) return;
  set_time_limit($n);
}
