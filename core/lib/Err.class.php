<?php

function error($text) {
  Err::error($text);
}

class Err {

  static $show = false;

  static $last;

  /**
   * Выводит сообщение об ошибке, если включен режим отладки
   *
   * @param $errno
   * @param string $errstr Текст ошибки
   * @param $errfile
   * @param $errline
   * @param array $trace
   */
  static protected function output($errno, $errstr, $errfile, $errline, array $trace = []) {
    self::$last = [
      'message' => $errstr,
      'code'    => $errno,
      'file'    => $errfile,
      'line'    => $errline,
      'trace'   => getBacktrace(false)
    ];
    if (!self::$show) return;
    if (!defined('IS_DEBUG') or IS_DEBUG === false) return;
    $plainText = R::get('plainText');
    print $plainText ? "\n" : '<p class="error">';
    if (!$plainText) $errstr = str_replace("\n", "<br />", $errstr);
    else $errstr = strip_tags($errstr);
    print $errstr ? $errstr.($plainText ? "\n---------------\n" : '<hr />') : '';
    print empty($trace) ? getBacktrace(!$plainText) : _getBacktrace(Arr::append([
      [
        'file' => $errfile,
        'line' => $errline
      ]
    ], $trace), !$plainText);
    print $plainText ? "\n" : '</p>';
  }

  static function outputException(Exception $e) {
    ob_start();
    self::output($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTrace());
    return ob_get_clean();
  }

  static protected function _error($errno, $errstr, $errfile, $errline) {
    if (!headers_sent()) header('HTTP/1.0 404 Not Found');
    self::output($errno, $errstr, $errfile, $errline);
    LogWriter::v('errors', 'error: '.$errstr);
    die();
  }

  /**
   * Критическая ошибка. Приостановить выполнение всей программы
   *
   * @param string $text Текст ошибки
   */
  static function error($text) {
    self::_error(0, $text, 'DUMMY', 123);
  }

  static protected function _warning($errno, $errstr, $errfile, $errline) {
    self::output($errno, $errstr, $errfile, $errline);
    LogWriter::v('warnings', $errstr);
  }

  /**
   * Выводит сообщение об ошибке, но не влияет на ход выполнения программы
   *
   * @param string $text Текст ошибки
   */
  static function warning($text) {
    self::_warning(0, $text, 'DUMMY', 123);
  }

  static function exceptionHandler(Exception $e) {
    if (!headers_sent()) header('HTTP/1.0 404 Not Found');
    self::output($e->getCode(), "Uncaught exception: <i>".$e->getMessage().'</i><br /><br />', $e->getFile(), $e->getLine(), $e->getTrace());
    if (!is_a($e, 'NotLoggableError')) self::log($e);
  }

  static function shutdownHandler() {
    $error = error_get_last();
    if ($error !== null) {
      LogWriter::v('errors', $error['message'], [
        [
          'file' => $error['file'],
          'line' => $error['line']
        ]
      ]);
    }
  }

  static function setEntryCmd($cmd) {
    self::$errorExtra['entryCmd'] = $cmd;
  }

  static $errorExtra = [];

  static function log(Exception $e) {
    LogWriter::html('errors', $e->getMessage(), getFullTrace($e), array_merge([
      'exceptionClass' => get_class($e)
    ], self::$errorExtra), true);
  }

  static function logWarning(Exception $e) {
    LogWriter::html('warnings', $e->getMessage(), getFullTrace($e));
  }

  static function _log($text, array $trace) {
    LogWriter::str('errors', $text);
    LogWriter::html('errors', $text, $trace);
  }

  static function errorHandler($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
      // Этот код ошибки не включен в error_reporting
      return;
    }
    if ($errno === E_NOTICE) {
      if (self::$showNotices) {
        self::output($errno, $errstr, $errfile, $errline);
        self::_log($errstr, debug_backtrace());
      }
    }
    else {
      self::output($errno, $errstr, $errfile, $errline);
      self::_log($errstr, debug_backtrace());
    }
  }

  static function sql($message, $info, $die = false) {
    throw new Exception('SQL error: <i>'.$info['message'].'</i> (Code: '.$info['code'].')<pre>'.$info['query'].'</pre>');
  }

  // используется только в одном месте
  static function sqlDie($message, $info) {
    self::error(var_dump($info, true));
  }

  static $showNoticesLast;
  static $showNotices = false;

  /**
   * Переключает режим отображения нотисов
   *
   * @param bool $flag Включить/выключить
   */
  static function noticeSwitch($flag) {
    self::$showNoticesLast = self::$showNotices;
    self::$showNotices = $flag;
  }

  static function noticeSwitchBefore() {
    self::$showNotices = self::$showNoticesLast;
  }

  static function getErrorText(Exception $e) {
    $br = R::get('plainText') ? "\n" : '<br>';
    return $e->getMessage().$br.$e->getFile().':'.$e->getLine().$br._getBacktrace($e->getTrace(), !R::get('plainText'));
  }

  static function getTraceAsString(Exception $exception) {
    $rtn = "";
    $count = 0;
    foreach ($exception->getTrace() as $frame) {
      $args = "";
      if (isset($frame['args'])) {
        $args = array();
        foreach ($frame['args'] as $arg) {
          if (is_string($arg)) {
            if (strstr($arg, "\n")) $args[] = "MULTILINE";
            else $args[] = "'".$arg."'";
          }
          elseif (is_array($arg)) {
            $args[] = "Array";
          }
          elseif (is_null($arg)) {
            $args[] = 'NULL';
          }
          elseif (is_bool($arg)) {
            $args[] = ($arg) ? "true" : "false";
          }
          elseif (is_object($arg)) {
            $args[] = get_class($arg);
          }
          elseif (is_resource($arg)) {
            $args[] = get_resource_type($arg);
          }
          else {
            $args[] = $arg;
          }
        }
        $args = join(", ", $args);
      }
      $rtn .= sprintf("#%s %s(%s): %s(%s)\n", $count, isset($frame['file']) ? $frame['file'] : 'empty', isset($frame['line']) ? $frame['line'] : 'empty', $frame['function'], $args);
      $count++;
    }
    return $rtn;
  }


}