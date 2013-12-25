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
   * @param   string Текст ошибки (опционально)
   * @param   bool Прекращать ли выполнение скрипта
   * @param   bool Отправляет сообщение об ошибке на e-mail администратора
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
    //print "Error ($errno): ";
    //print "Error: ";
    if (!$plainText) $errstr = str_replace("\n", "<br />", $errstr);
    else $errstr = strip_tags($errstr);
    print $errstr ? $errstr.($plainText ? "\n---------------\n" : '<hr />') : '';
    print empty($trace) ? getBacktrace(!$plainText) : _getBacktrace(Arr::append([
      [
        'file' => $errfile,
        'line' => $errline
      ]
    ], $trace), !$plainText);
    print($plainText ? "\n" : '</p>');
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
   * @param   string Текст ошибки
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
   * @param   string Текст ошибки
   */
  static function warning($text) {
    self::_warning(0, $text, 'DUMMY', 123);
  }

  static function exceptionHandler(Exception $e) {
    if (!headers_sent()) header('HTTP/1.0 404 Not Found');
    $text = "Uncaught exception: <i>".$e->getMessage().'</i><br /><br />';
    self::output($e->getCode(), $text, $e->getFile(), $e->getLine(), $e->getTrace());
    if (is_a($e, 'NotLoggableError')) die2('!!!');
    self::log($e);
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

  static function log(Exception $e) {
    LogWriter::html('errors', $e->getMessage(), getFullTrace($e), [
      'exceptionClass' => get_class($e)
    ], true);
  }

  static function logWarning(Exception $e) {
    LogWriter::html('warnings', $e->getMessage(), getFullTrace($e));
  }

  static function _log($text, array $trace) {
    LogWriter::html('errors', $text, $trace);
  }

  static function errorHandler($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
      // Этот код ошибки не включен в error_reporting
      return;
    }
    if ($errno === E_NOTICE) {
      if (self::$showNotices) throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
    else {
      throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
    /*
    switch ($errno) {
      case E_WARNING:
        self::_warning(
          $errno,
          "<b>%% Warning</b>: <i>$errstr</i> in <b>$errfile</b>: $errline %%",
          $errfile, $errline
        );
        break;
      case E_ERROR:
        self::_error(
          $errno,
          "<b>%% Fatal error</b>: <i>$errstr</i> in <b>$errfile</b>: $errline %%",
          $errfile, $errline
        );
        break;
      case E_NOTICE:
        if (self::$showNotices) {
          self::_warning(
            $errno,
            "%% <b>Notice {$errno}</b>: <i>$errstr</i> in <b>$errfile</b>: $errline %%",
            $errfile, $errline
          );
        }
        break;
      default:
        self::_warning(
          $errno,
          "%% <b>Other {$errno}</b>: <i>$errstr</i> in <b>$errfile</b>: $errline %%",
          $errfile, $errline
        );
    }
    */
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
   * @param   bool Включить/выключить
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

}