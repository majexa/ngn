<?php

class Misc {

  static function cut($s, $length, $end = '...', $strip = true) {
    $s = trim($s);
    $s = preg_replace('/\s+/u', ' ', $s);
    $s = str_replace('&nbsp;', ' ', $s);
    if (mb_strlen($s, CHARSET) > $length) {
      if ($strip) $s = strip_tags($s);
      $s = mb_substr($s, 0, $length, CHARSET);
      return preg_replace('/(.*)\s+(.*)/u', '$1', $s).$end;
    }
    return $s;
  }

  static function cutUrl($s, $length) {
    return Misc::cut(preg_replace('/(.*)\/?/U', '$1', preg_replace('/https?:\/\/(.+)/', '$1', $s)), $length);
  }

  static function randString($len = 20, $lower = false) {
    return self::_randString($lower ? 'abcdefghijklnmopqrstuvwxyz0123456789' : 'abcdefghijklnmopqrstuvwxyzABCDEFGHIJKLNMOPQRSTUVWXYZ0123456789', $len);
  }

  static function randNum($len = 5) {
    return self::_randString('0123456789', $len);
  }

  static protected function _randString($chars, $len = 20) {
    $string = '';
    mt_srand((double)microtime() * 1000000);
    for ($i = 0; $i < $len; $i++) $string .= $chars[mt_rand(0, strlen($chars) - 1)];
    return $string;
  }

  /**
   * Проверяет является ли текущий пользователь разработчиком
   *
   * @return bool
   */
  static function isDeveloper() {
    if (in_array($_SERVER['REMOTE_ADDR'], Config::getVar('developer-ips')) or
    (defined('IS_DEVELOPER') and IS_DEVELOPER === true)
    ) {
      return true;
    }
    return false;
  }

  /**
   * Проверяет является ли текущий пользователь богом, т.е. имеет неограниченные
   * права доступа на сайте
   *
   * @return bool
   */
  static function isGod($quietly = true) {
    if (!getConstant('ALLOW_GOD_MODE')) {
      if (!$quietly) throw new Exception('God mod not allowed');
      return false;
    }
    if (!($gods = Config::getVar('gods'))) return false;
    return in_array(Auth::get('id'), $gods);
  }

  /**
   * Проверяет является ли текущий пользователь админом, т.е. имеет доступ к
   * разделам администрирования /admin
   *
   * @return bool
   */
  static function isAdmin() {
    if (!Auth::get('id')) return false;
    if (Misc::isGod()) return true;
    if (!($admins = Config::getVar('admins', true))) return false;
    return in_array(Auth::get('id'), $admins);
  }

  /**
   * Переводит строку формата "nasd-asd-asd" в "nasdAsdAsd"
   *
   * @param   string  Строка формата "nasd-asd-asd"
   * @return  string
   */
  static function camelCase($str, $sep = '-') {
    return preg_replace_callback('/'.$sep.'\D/', create_function('$m', 'return strtoupper($m[0][1]);'), $str);
  }

  /**
   * Переводит строку формата "nasdAsdAsd" в "nasd-asd-asd"
   *
   * @param   string  Строка формата "nasdAsdAsd"
   * @return  string
   */
  static function hyphenate($str, $sep = '-') {
    return preg_replace_callback('/[A-Z]/', create_function('$m', 'return "'.$sep.'".strtolower($m[0]);'), $str);
  }

  const SECONDS_DAY = 86400;
  const SECONDS_HOUR = 3600;
  const SECONDS_MIN = 60;

  static function timeStr($sec) {
    $days = floor($sec / SECONDS_DAY);
    $incompleteDaySec = $sec % Misc::SECONDS_DAY; // Секунд в нецелом дне
    $hours = floor($incompleteDaySec / self::SECONDS_HOUR);
    $incompleteHourSec = $incompleteDaySec % self::SECONDS_HOUR; // Секунд в нецелом часе
    $min = floor($incompleteHourSec / self::SECONDS_MIN);
    $incompleteMinSec = $incompleteHourSec % self::SECONDS_MIN; // Секунд в нецеой минуте
    return "$days дней, $hours часов, $min минут, $incompleteMinSec секунд";
  }


  static function getWebFileAbsPath($webpath) {
    return WEBROOT_PATH.'/'.Misc::clearFirstSlash($webpath);
  }

  static function parseId($str) {
    //prr($str);
    if (!preg_match('/[a-z_]/i', $str[0])) $str[0] = '_';
    for ($i = 0; $i < strlen($str); $i++) {
      if (!preg_match('/[a-z0-9_]/i', $str[$i])) $str[$i] = '_';
    }
    return $str;
  }

  static function name2id($name) {
    $name = str_replace('-', '_', $name);
    $name = str_replace('[', '-', $name);
    return str_replace(']', '', $name);
  }

  static function name2lastKey($name) {
    return preg_replace('/^.*\[([a-z0-9_-]+)\]?/i', '$1', $name);
  }

  static function domain2dbname($domain) {
    $domain = str_replace('-', '_', $domain);
    $domain = str_replace('/', '_', $domain);
    $domain = str_replace('.', '_', $domain);
    return $domain;
  }

  static function domain($s) {
    $s = Misc::translate($s, true);
    $s = str_replace('_', '-', $s);
    $s = str_replace('.', '-', $s);
    while (is_numeric($s[0])) $s = substr($s, 1, strlen($s));
    if (!$s) throw new Exception('Converted string is empty.');
    return $s;
  }

  static function validDomain($domain) {
    return preg_match('/[0-9a-zа-я\-.]/', $domain);
  }

  static function validEmail($email) {
    return preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', $email);
  }

  static function validName($name) {
    return preg_match('/^[a-z][a-z0-9-_]*$/i', $name);
  }

  static function quoted2arr($s) {
    $arr = explode(',', $s);
    foreach ($arr as &$v) $v = trim($v);
    return $arr;
  }

  static function getFileExtension($filePath) {
    return strtolower(substr($filePath, strrpos($filePath, '.') + 1, strlen($filePath)));
  }

  static function getFilePrefexedPath($path, $prefix, $extention = null) {
    preg_match('/(.*\/)([^\/]+)/', $path, $m);
    if (strstr($m[2], '.') and $extention) $m[2] = preg_replace('/(.*)(\.\w+)/', '$1.'.$extention, $m[2]);
    return $m[1].$prefix.$m[2];
  }

  static function getFilePrefexedPath2($path, $prefix) {
    return self::getFilePrefexedPath($path, $prefix, File::getExtension($path));
  }

  static function replaceExtension($file, $extention) {
    return preg_replace('/(.*\/)([\w-_]+)(\.\w+)/', '$1$2.'.$extention, $file);
  }

  static function getFileDisprefexedPath($path) {
    return preg_replace('/(.*\/)[a-z]+_([\w-_]+\.\w+)/', '$1$2', $path);
  }

  static function translate($in, $toLower = false) {
    $out = [];
    $replacers = [

      'а' => 'a',
      'б' => 'b',
      'в' => 'v',
      'г' => 'g',
      'д' => 'd',
      'е' => 'e',

      'ё' => 'e',
      'ж' => 'zh',
      'з' => 'z',
      'и' => 'i',
      'й' => 'j',
      'к' => 'k',

      'л' => 'l',
      'м' => 'm',
      'н' => 'n',
      'о' => 'o',
      'п' => 'p',
      'р' => 'r',

      'с' => 's',
      'т' => 't',
      'у' => 'u',
      'ф' => 'f',
      'х' => 'h',

      'ц' => 'c',
      'ч' => 'ch',
      'ш' => 'sh',
      'щ' => 'sh',
      'ъ' => '',
      'ъ' => '',

      'ы' => 'i',
      'ь' => '',
      'э' => 'e',
      'ю' => 'u',
      'я' => 'ya',
      ' ' => '-',
      '_' => '-',
      '.' => '-',
      '/' => '-',

      'А' => 'A',
      'Б' => 'B',
      'В' => 'V',
      'Г' => 'G',
      'Д' => 'D',
      'Е' => 'E',

      'Ё' => 'E',
      'Ж' => 'Zh',
      'З' => 'Z',
      'И' => 'I',
      'Й' => 'J',
      'К' => 'K',

      'Л' => 'L',
      'М' => 'M',
      'Н' => 'N',
      'О' => 'O',
      'П' => 'P',
      'Р' => 'R',

      'С' => 'S',
      'Т' => 'T',
      'У' => 'U',
      'Ф' => 'F',
      'Х' => 'H',

      'Ц' => 'C',
      'Ч' => 'Ch',
      'Ш' => 'Sh',
      'Щ' => 'Sh',
      'Ъ' => '',
      'Ь' => '',

      'Ы' => 'I',
      'Э' => 'E',
      'Ю' => 'U',
      'Я' => 'Ya',
      '&' => 'n'
    ];

    $english = explode(' ', 'q w e r t y u i o p a s d f g h j k l z x c v b n m 1 2 3 4 5 6 7 8 9 0 - _ .');

    $in = trim($in);
    $in = strtr($in, $replacers);

    for ($i = 0; $i < strlen($in); $i++) if (in_array(strtolower($in[$i]), $english, true)) $out[] = $in[$i];

    if (count($out) > 0) $out = implode($out);
    else
      $out = $in;

    if ($toLower) $out = strtolower($out);

    $out = preg_replace('/--+/', '-', $out);

    return $out;
  }

  static function detranslate($in, $toLower = false) {
    $out = [];
    $replacers = array_flip([
      'а' => 'a',
      'б' => 'b',
      'в' => 'v',
      'г' => 'g',
      'д' => 'd',
      'е' => 'e',
      'ё' => 'e',
      'ж' => 'zh',
      'з' => 'z',
      'и' => 'i',
      'й' => 'j',
      'к' => 'k',
      'л' => 'l',
      'м' => 'm',
      'н' => 'n',
      'о' => 'o',
      'п' => 'p',
      'р' => 'r',
      'с' => 's',
      'т' => 't',
      'у' => 'u',
      'ф' => 'f',
      'х' => 'h',
      'ц' => 'c',
      'ч' => 'ch',
      'ш' => 'sh',
      'щ' => 'sh',
      'ъ' => '',
      'ъ' => '',
      'ы' => 'i',
      'ь' => '',
      'э' => 'e',
      'ю' => 'u',
      'я' => 'ya',
      ' ' => '-',
      '_' => '-',
      '/' => '-',
      'А' => 'A',
      'Б' => 'B',
      'В' => 'V',
      'Г' => 'G',
      'Д' => 'D',
      'Е' => 'E',
      'Ё' => 'E',
      'Ж' => 'Zh',
      'З' => 'Z',
      'И' => 'I',
      'Й' => 'J',
      'К' => 'K',
      'Л' => 'L',
      'М' => 'M',
      'Н' => 'N',
      'О' => 'O',
      'П' => 'P',
      'Р' => 'R',
      'С' => 'S',
      'Т' => 'T',
      'У' => 'U',
      'Ф' => 'F',
      'Х' => 'H',
      'Ц' => 'C',
      'Ч' => 'Ch',
      'Ш' => 'Sh',
      'Щ' => 'Sh',
      'Ъ' => '',
      'Ь' => '',
      'Ы' => 'I',
      'Э' => 'E',
      'Ю' => 'U',
      'Я' => 'Ya',
      '&' => 'n'
    ]);
    $english = explode(' ', 'q w e r t y u i o p a s d f g h j k l z x c v b n m 1 2 3 4 5 6 7 8 9 0 - _ .');
    $in = trim($in);
    $in = strtr($in, $replacers);
    for ($i = 0; $i < strlen($in); $i++) if (in_array(strtolower($in[$i]), $english, true)) $out[] = $in[$i];
    if (count($out) > 0) $out = implode($out);
    else
      $out = $in;
    if ($toLower) $out = strtolower($out);
    $out = preg_replace('/--+/', '-', $out);
    return $out;
  }


  static function getIncluded($file, $d = []) {
    if (!file_exists($file)) throw new Exception("File '$file' not found");
    ob_start();
    include $file;
    $c = ob_get_contents();
    ob_end_clean();
    return $c;
  }

  static function getIncludedByRequest($file, $r = []) {
    if (!file_exists($file)) throw new Exception("File '$file' not found");
    ob_start();
    $_REQUEST = $r;
    include $file;
    $c = ob_get_contents();
    ob_end_clean();
    return $c;
  }

  static function iconvR($in, $out, $d) {
    if (is_array($d)) {
      foreach ($d as $k => $v) $d[$k] = self::iconvR($in, $out, $v);
      return $d;
    }
    else return iconv($in, $out, $d);
  }

  static function plural($t) {
    $l = mb_substr($t, mb_strlen($t, CHARSET) - 1, 1, CHARSET);
    if ($l == 'и' or $l == 'ы') return true;
    return false;
  }

  static function getHttpClientInfo() {
    return [
      'REMOTE_ADDR'     => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
      'REQUEST_URI'     => isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '',
      'HTTP_REFERER'    => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
      'HTTP_USER_AGENT' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : ''
    ];
  }

  static function getIPInfo() {
    $in = [];
    if (isset($_SERVER['REMOTE_ADDR'])) {
      $in['host'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);
      $in['ip'] = gethostbyname($in['host']);
    }
    if (isset($_SERVER["HTTP_FORWARDED_FOR"])) {
      $in['ip'] = $_SERVER["HTT?_FORWARDED_FOR"].",via:".$in[ip];
      $in['host'] = @gethostbyaddr($_SERVER["HTT?_FORWARDED_FOR"]);
    }
    if (isset($_SERVER['HTTP_VIA'])) {
      $in['host'] = $in['host'].",Via:".$_SERVER['HTTP_VIA'];
    }
    if (!$in['ip']) $in['ip'] = $_SERVER['REMOTE_ADDR'];
    if (!$in['host']) $in['host'] = $in["ip"];
    return $in;
  }

  static function wordEnd($t, $a1 = 'сотка', $a2 = 'сотки', $a3 = 'соток') {
    if ($t % 10 == 1 && $t % 100 < 10 && $t % 100 > 20) return $a1;
    if ($t % 100 > 10 && $t % 100 < 20) return $a3;
    if ($t % 10 > 1 && $t % 10 < 5) return $a2;
    return $a3;
  }

  static function validUrl($url) {
    return preg_match('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', $url);
  }

  static function validPhone($phone) {
    return preg_match('/^\+\d{11}$/', $phone);
  }

  static function checkConst($name) {
    if (!defined($name)) throw new Exception("$name not defined");
  }

  static function checkEmpty($v, $title = '$v') {
    if (empty($v)) throw new EmptyException($title);
    return $v;
  }

  static function checkArray($v, $title = '$v is not array') {
    if (!is_array($v)) throw new Exception($title);
  }

  static function checkNumeric($v) {
    if (!is_numeric($v)) throw new Exception(getPrr($v).' is not numeric');
  }

  static function checkValidUrl($url) {
    if (!self::validUrl($url)) throw new Exception("Url '$url' is not valid");
  }

  static function ddFilterDateLastDays($n) {
    return 'd.'.date('d.m.Y', mktime(0, 0, 0, date('n'), (date('d') - $n), date('Y'))).'-'.date('d.m.Y');
  }

  static function ddFilterDateLastMonths($n) {
    return 'd.'.date('d.m.Y', mktime(0, 0, 0, (date('n') - $n), date('d'), date('Y'))).'-'.date('d.m.Y');
  }

  static function ddFilterDateLastYears($n) {
    return 'd.'.date('d.m.Y', mktime(0, 0, 0, date('n'), date('d') - $n, (date('Y') - $n))).'-'.date('d.m.Y');
  }

  static function getHostUrl($url) {
    $u = parse_url($url);
    return $u['scheme'].'://'.$u['host'];
  }

  static function getHost($address) {
    $r = parse_url($address);
    return $r['host'];
    //return preg_replace('/([^\/]+)\/.*/', '$1', $address);
  }

  /**
   * Выбирает тэги без квадратных скобок с параметрами
   *
   * @param   array   Массив с тэгами
   *                  Пример:
   *                  array('a[class,href]')
   */
  static function clearConfigTags(array $tags) {
    $clearTags = [];
    foreach ($tags as $v) $clearTags[] = preg_replace('/([^\[^\]]*)(\[.+\])*/', '$1', $v);
    return $clearTags;
  }

  static function colorAllocate($im, $color) {
    return imagecolorallocate($im, '0x'.substr($color, 0, 2), '0x'.substr($color, 2, 2), '0x'.substr($color, 4, 2));
  }

  static function weekdays() {
    return [
      1 => 'Понедельник',
      2 => 'Вторник',
      3 => 'Среда',
      4 => 'Четверг',
      5 => 'Пятница',
      6 => 'Суббота',
      7 => 'Воскресенье',
    ];
  }

  static function secondsToTime($seconds) {
    // extract hours
    $hours = floor($seconds / (60 * 60));

    // extract minutes
    $divisorForMinutes = $seconds % (60 * 60);
    $minutes = floor($divisorForMinutes / 60);

    // extract the remaining seconds
    $divisorForSeconds = $divisorForMinutes % 60;
    $seconds = ceil($divisorForSeconds);

    // return the final array
    return [
      $hours, $minutes, $seconds,
    ];
  }

  static function secondsToTimeFormat($seconds) {
    $r = self::secondsToTime($seconds);
    return sprintf('%02d:%02d:%02d', $r[0], $r[1], $r[2]);
  }

  static function phpIniFileSizeToBytes($s) {
    preg_match('/(\d+)(\w+)/', $s, $m);
    if (strtolower($m[2]) == 'm') return $m[1] * 1024 * 1024;
    elseif (strtolower($m[2]) == 'k') return $m[1] * 1024;
    else return $m[1];
  }

  static function stripHost($url) {
    $url = parse_url($url);
    return $url['path'].($url['query'] ? '?'.$url['query'] : '');
  }

  // ------------ Domains ---------------

  /**
   * Преобразует ссылки и экшены форм в тексте в абсолютные
   * (если они таковыми не явлюяются) и добавляет к
   * базовому домену сабдомен
   *
   * @param   string  Текст
   * @param   string  Сабдомен (Пример: myname)
   * @return  string
   */
  static function extendSubdomain($c, $subdomain) {
    return preg_replace('/(href|action)="(?!\/\/)\/?([^"]*)"/', '$1="http://'.$subdomain.'.'.SITE_DOMAIN.'/$2"', $c);
  }

  /**
   * Возвращает уровень текущего домена
   *
   * @return integer
   */
  static function siteDomainLevel() {
    return substr_count(SITE_DOMAIN, '.') + 1;
  }

  static function removePrefix($prefix, $str) {
    $prefix = str_replace('\\', '\\\\', $prefix);
    $prefix = str_replace('/', '\\/', $prefix);
    $prefix = str_replace('.', '\\.', $prefix);
    try {
      preg_replace("/^$prefix(.*)/", '$1', $str);
    } catch (Exception $e) {
      throw new Exception("/^$prefix(.*)/");
    }
    return preg_replace("/^$prefix(.*)/", '$1', $str);
  }

  static function removeSuffix($suffix, $str) {
    $suffix = str_replace('/', '\\/', $suffix);
    return preg_replace("/(.*)$suffix$/", '$1', $str);
  }

  static function hasPrefix($prefix, $str) {
    $prefix = str_replace('\\', '\\\\', $prefix);
    $prefix = str_replace('/', '\\/', $prefix);
    $prefix = str_replace('.', '\\.', $prefix);
    return preg_match("/^$prefix.*/", $str);
  }

  static function hasSuffix($suffix, $str) {
    $suffix = str_replace('/', '\\/', $suffix);
    return preg_match("/.*$suffix$/", $str);
  }

  static function notRealized() {
    throw new Exception('Not realized');
  }

  static function clearLastSlash($t) {
    if ($t[strlen($t) - 1] == '/') $t = substr($t, 0, strlen($t) - 1);
    return $t;
  }

  static function clearFirstSlash($t) {
    if ($t[0] == '/') return substr($t, 1, strlen($t));
    else return $t;
  }

  static function trimSlashes($t) {
    return preg_replace('/^\/(.*)\/$/', '$1', $t);
  }

  static function cleanupSpaces($t) {
    return preg_replace('/\s+/', ' ', $t);
  }

  static function hex2rgb($hexStr, $returnAsString = false, $seperator = ',') {
    $hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
    $rgbArray = [];
    if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
      $colorVal = hexdec($hexStr);
      $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
      $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
      $rgbArray['blue'] = 0xFF & $colorVal;
    }
    elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
      $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
      $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
      $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
    }
    else {
      return false; //Invalid hex color code
    }
    return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray; // returns the rgb string or the associative array
  }

  static function addParam($url, $k, $v) {
    return $url.(strstr($url, '?') ? '&' : '?')."$k=$v";
  }

  static function plural2single($s) {
    return preg_replace('/^(.*)s$/', '$1', $s);
  }

  static function single2plural($s) {
    return $s.'s';
  }

  static function str2regexp($str) {
    return str_replace([
        '!',
        '/',
        '(',
        ')',
        '{',
        '}',
        '[',
        ']',
        '|',
        '^',
        '?'
      ], [
        '\!',
        '\\/',
        '\\(',
        '\\)',
        '\\{',
        '\\}',
        '\\[',
        '\\]',
        '\\|',
        '\\^',
        '\\?'
      ], $str);
  }

  static function price($v) {
    return round(floatval(str_replace(',', '.', $v)) * 100) / 100;
  }

  static function lcfirst($str) {
    return mb_strtolower(mb_substr($str, 0, 1, CHARSET), CHARSET).mb_substr($str, 1, mb_strlen($str, CHARSET), CHARSET);
  }

  static function ucfirst($str) {
    return mb_strtoupper(mb_substr($str, 0, 1, CHARSET), CHARSET).mb_substr($str, 1, mb_strlen($str, CHARSET), CHARSET);
  }

  static function parsePhone($v) {
    return preg_replace('/(\d)(\d{3})(\d{7})/', '+$1 ($2) $3', $v);
  }

  static function formatPrice($v) {
    return number_format($v, 0, '.', ' ');
  }

  static function firstIsUpper($str) {
    $chr = mb_substr($str, 0, 1, CHARSET);
    return mb_strtoupper($chr, CHARSET) == $chr;
  }

}
