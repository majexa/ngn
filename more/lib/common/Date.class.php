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
   * Возвращает время в формате базы данных MySQL
   *
   * @param int $time
   * @return bool|string|void
   */
  static function db($time = 0) {
    return date('Y-m-d H:i:s', $time ? $time : time());
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
    if (!$months) $months = Config::getVar('ru'.ucfirst($monthsType));
    return date('j', $tStamp).' '.($lowercase ? mb_strtolower($months[date('n', $tStamp)], CHARSET) : $months[date('n', $tStamp)]).' '.date('Y', $tStamp);
  }

  function datetimeStrSql($dateSql, $monthsType = 'months2') {
    preg_match('/(\d+)-(\d+)-(\d+) (\d+):(\d+):(\d+)/', $dateSql, $m);
    static $months;
    if (!$months) $months = Config::getVar('ru'.ucfirst($monthsType));
    return $m[3].' '.mb_strtolower($months[(int)$m[2]], CHARSET).' '.$m[1].' в '.$m[4].':'.$m[5];
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
        //$monthConfigKey1 = preg_replace('/.*([a-z]{2}-month).*/', '$1s', $inFormat);
        //foreach (array_flip(Config::getVar($monthConfigKey1)) as $monthTitle => $n)
        //  $str = str_replace($monthTitle, $n, $str);
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

  static function time($time, $format = Date::TIME_HM) {
    if ($format === Date::TIME_HM) return preg_replace('/(\d+:\d+):\d+/', '$1', $time);
    else return preg_replace('/(\d+):\d+:\d+/', '$1', $time);
  }

}