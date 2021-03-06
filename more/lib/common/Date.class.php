<?php

class Date {

  /**
   * Переформатирует время-дату в условно-произвольном формате в формат принятый в ф-ии date().
   * Условно-произвольный формат даты чувствителен к следующему ряду символов,
   * указанных через пробел и сам пробел: d m y h i s . : , | - \ /
   *
   * @param string $date Время-дата в условно-произвольном формате
   * @param string $outFormat Условно-произвольный формат
   * @param string $inFormat Формат принятый в ф-ии date()
   * @return string Время-дата в формате $outFormat
   */
  function _reformat($date, $outFormat, $inFormat) {
    $inFormat = strtolower($inFormat);
    $regexp = '';
    $n = 1;
    for ($i = 0; $i < strlen($inFormat); $i++) {
      $l = $inFormat[$i];
      if ($l == 'd' or $l == 'm' or $l == 'n' or $l == 'h' or $l == 'i' or $l == 's') {
        $regexp .= '(\d{1,2})';
        if ($l == 'n') $l = 'm';
        $ord[$l] = $n;
        $n++;
      }
      elseif ($l == 'y') {
        $regexp .= '(\d{4}|\d{2})';
        $ord[$l] = $n;
        $n++;
      }
      elseif (preg_match('/[.:,|\-\ \/]/', $l)) $regexp .= $l;
    }

    if (!$regexp or !preg_match("/$regexp/", $date, $m)) {
      return false;
    }

    if ($outFormat == 'timestamp') {
      return mktime(0, 0, 0, $m[$ord['m']], $m[$ord['d']], $m[$ord['y']]);
    }
    $outFormat = str_replace('Y', $m[$ord['y']], $outFormat);
    $outFormat = str_replace('m', $m[$ord['m']], $outFormat);
    $outFormat = str_replace('d', $m[$ord['d']], $outFormat);
    if (isset($ord['h'])) $outFormat = str_replace('H', $m[$ord['h']], $outFormat);
    if (isset($ord['i'])) $outFormat = str_replace('i', $m[$ord['i']], $outFormat);
    if (isset($ord['s'])) $outFormat = str_replace('s', $m[$ord['s']], $outFormat);
    return $outFormat;
  }

  static function reformat($date, $outFormat, $inFormat = ['d.m.Y H:i:s', 'd.m.Y H:i', 'd.m.Y']) {
    if (is_array($inFormat)) {
      foreach ($inFormat as $format) {
        if (($r = self::_reformat($date, $outFormat, $format)) !== false) return $r;
      }
      throw new Exception("Date '$date' not supported by formats: ".implode(', ', $inFormat));
    }
    else {
      return self::_reformat($date, $outFormat, $inFormat);
    }
  }

  /**
   * Возвращает дату и время в формате базы данных MySQL
   *
   * @param int $time
   * @return bool|string|void
   */
  static function db($time = 0) {
    return date(self::DB_FORMAT, $time ? $time : time());
  }

  const DB_FORMAT = 'Y-m-d H:i:s';

  static function dbDate($time = 0) {
    return date('Y-m-d', $time ? $time : time());
  }

  static function dbTime($time = 0) {
    return date('H:i:s', $time ? $time : time());
  }

  /**
   * Преобразует timestamp в дату с текстовым месяцем
   *
   * @param string $tStamp TIMESTAMP
   * @param bool $lowercase Переводить в нижний регистр
   * @param string $monthsType Тип месяца:
   *                    'months' (месяц с прописной буквы в именительном падеже) /
   *                    'months2' (месяц с прописной буквы в родительном падеже)
   * @return string
   * @throws Exception
   */
  static function str($tStamp, $lowercase = true, $monthsType = 'months2') {
    static $months;
    if (!$months) $months = Config::getVar(Locale::striped().ucfirst($monthsType));
    return date('j', $tStamp).' '.($lowercase ? mb_strtolower($months[date('n', $tStamp)], CHARSET) : $months[date('n', $tStamp)]).' '.date('Y', $tStamp);
  }

  function datetimeStrSql($dateSql, $monthsType = 'months2') {
    preg_match('/(\d+)-(\d+)-(\d+) (\d+):(\d+):(\d+)/', $dateSql, $m);
    static $months;
    if (!$months) $months = Config::getVar(Locale::striped().ucfirst($monthsType));
    return (int)$m[3].' '.$months[(int)$m[2]].', '.$m[1].' '.Locale::get('at').' '.$m[4].':'.$m[5];
  }

  function datetimeStr($tStamp, $lowercase = true, $monthsType = 'months') {
    if ($tStamp == 0) return 'не определено';
    return self::str($tStamp, $lowercase, $monthsType).' — '.date('H:i:s', $tStamp);
  }

  /**
   * Варианты входных форматов:
   *   'd.m.Y H:i:s'
   *   'd.m.Y H:i'
   *   'd.m.Y'
   *   'd ru-month Y'
   *   'd ru-month2 Y'
   *
   * @param $str
   * @param $inFormat
   * @param $outFormat
   * @return string
   * @throws Exception
   */
  static function dateParse($str, $inFormat, $outFormat) {
    if ($inFormat == 'd.m H:i') {
      $str = preg_replace('/(\d+\.\d+)( \d+:\d+)/', '$1.'.date('Y').'$2', $str);
      $inFormat = 'd.m.Y H:i';
    }
    elseif (strstr($inFormat, 'month')) {
      $str = mb_strtolower($str, CHARSET);
      if (strstr($str, 'сегодня')) {
        $str = str_replace('сегодня', date('d.m.Y'), $str);
        $inFormat = 'd.m.Y';
      }
      elseif (strstr($str, 'вчера')) {
        $str = str_replace('вчера', date('d.m.Y', mktime(1, 1, 0, date('n'), date('d') - 1, date('Y'))), $str);
        $inFormat = 'd.m.Y';
      }
      else {
        // родительный падеж
        $monthConfigKey2 = preg_replace('/.*([a-z]{2}-month).*/', '$1s2', $inFormat);
        foreach (array_flip(Config::getVar($monthConfigKey2)) as $monthTitle => $n) {
          $str = str_replace(mb_strtolower($monthTitle, CHARSET), $n, $str);
        }
        // именительный падеж (типа не встречается)
        $inFormat = preg_replace('/[a-z]{2}-month/', 'n', $inFormat);
      }
    }
    return self::_reformat($str, $outFormat, $inFormat);
  }

  /**
   * @param string $date Дата в формате DD.MM.YYYY
   * @return bool|string|void
   */
  static function ageFromBirthDate($date) {
    list($d, $m, $y) = explode('.', $date);
    $d = (int)$d;
    $m = (int)$m;
    $y = (int)$y;
    if (($m = (date('m') - $m)) < 0) {
      $y++;
    }
    elseif ($m == 0 && date('d') - $d < 0) {
      $y++;
    }
    return date('Y') - $y;
  }


  const TIME_H = 1;
  const TIME_HM = 2;
  const TIME_HMS = 3;

  static function recentTime($time, $format = Date::TIME_HM) {
    if ($format == Date::TIME_HM) {
      $timeFormat = 'H:i';
    }
    elseif ($format == Date::TIME_HMS) {
      $timeFormat = 'H:i:s';
    }
    else {
      $timeFormat = 'H';
    }
    if (date('dmY', $time) == date('dmY')) {
      return date($timeFormat, $time);
    }
    return Date::str($time).' '.date($timeFormat, $time);
  }

  static function time($time, $format = Date::TIME_HM) {
    if ($format === Date::TIME_HM) return preg_replace('/(\d+:\d+):\d+/', '$1', $time);
    else return preg_replace('/(\d+):\d+:\d+/', '$1', $time);
  }

  static function relativeTime($ts) {
    if (!ctype_digit($ts)) $ts = strtotime($ts);
    $diff = time() - $ts;
    if ($diff == 0) return 'now';
    elseif ($diff > 0) {
      $day_diff = floor($diff / 86400);
      if ($day_diff == 0) {
        if ($diff < 60) return 'just now';
        if ($diff < 120) return '1 minute ago';
        if ($diff < 3600) return floor($diff / 60).' minutes ago';
        if ($diff < 7200) return '1 hour ago';
        if ($diff < 86400) return floor($diff / 3600).' hours ago';
      }
      if ($day_diff == 1) return 'Yesterday';
      if ($day_diff < 7) return $day_diff.' days ago';
      if ($day_diff < 31) return ceil($day_diff / 7).' weeks ago';
      if ($day_diff < 60) return 'last month';
      return date('F Y', $ts);
    }
    else {
      $diff = abs($diff);
      $day_diff = floor($diff / 86400);
      if ($day_diff == 0) {
        if ($diff < 120) return 'in a minute';
        if ($diff < 3600) return 'in '.floor($diff / 60).' minutes';
        if ($diff < 7200) return 'in an hour';
        if ($diff < 86400) return 'in '.floor($diff / 3600).' hours';
      }
      if ($day_diff == 1) return 'Tomorrow';
      if ($day_diff < 4) return date('l', $ts);
      if ($day_diff < 7 + (7 - date('w'))) return 'next week';
      if (ceil($day_diff / 7) < 4) return 'in '.ceil($day_diff / 7).' weeks';
      if (date('n', $ts) == date('n') + 1) return 'next month';
      return date('F Y', $ts);
    }
  }

}