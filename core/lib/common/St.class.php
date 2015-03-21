<?php

/**
 * Функции работы с html/php/text шаблонами через eval и str_replace
 */
class St {

  /**
   * Обрабатывает строку, как PHP-код, делая при этом доступными
   * все элементы массива $__data, как переменные. Т.е. если
   * $__data = array('name' => '123'), то в коде будет доступна
   * переменная $name
   *
   * @param string $__text PHP-код. Вместо одинарных кавычек (') используются
   *                  одинарные наклонные (`)
   *                  Пример кода:
   *                  $_text = 'just` . someFunc(`fallow`) . `your heart';
   *                  Внимание! в начале и в конце строки кавычки не нужны!
   *                  Перед исполнением кода в eval() наклонные одинарные
   *                  кавычки заменяются на двойные. Таким образом, если необходимо
   *                  вывести текст с переменной, ставить кавычки вообще не нужно
   *                  Пример кода:
   *                  $_text = 'just $fallow your heart';
   * @param array $__data Массив, элементы которого будут доступны, как переменные в
   *                  вашем PHP-коде
   * @return mixed Результат обработки eval()
   * @throws Exception
   */
  static function dddd($__text, array $__data) {
    Err::noticeSwitch(false);
    if (isset($data['__text'])) unset($data['__text']);
    if (isset($data['__data'])) unset($data['__data']);
    if (is_array($__data)) extract($__data);
    $__text = str_replace('\\"', '"', $__text);
    $__text = str_replace('`', '\'', $__text);
    $code = 'return '.$__text.';';
    ob_start();
    $r = eval($code);
    $c = ob_get_contents();
    ob_end_clean();
    if (strstr($c, 'error') or strstr($c, 'warning')) throw new Exception("Code: <pre>\"".htmlspecialchars($__text)."\"</pre><div style='color:#FF0000'>$c</div>");
    Err::noticeSwitchBefore();
    return $r;
  }

  static function ssss($__text, array $__data) {
    Err::noticeSwitch(false);
    if (isset($__data['__text'])) unset($__data['__text']);
    if (isset($__data['__data'])) unset($__data['__data']);
    if (is_array($__data)) extract($__data);
    $__text = str_replace('"', '\\"', $__text);
    $__text = str_replace('`', '"', $__text);
    $code = 'return "'.$__text.'";';
    ob_start();
    $r = eval($code);
    $c = ob_get_contents();
    ob_end_clean();
    if (strstr($c, 'error') or strstr($c, 'warning')) throw new Exception("Code:\n==================\n$code\n=================");
    Err::noticeSwitchBefore();
    return $r;
  }

  /**
   * Работает с шаблонами вида "abc {varKey} bca", заменяя слова {varKey} соотвующими
   * значениями из массива $data.
   *
   * @param string $text Исходный текст
   * @param array $data Массив с данными для замены
   * @param bool $onlyExistsInData Заменять только строки {name}, соответствующие
   *                               элементы которых существуют в массиве $data
   * @return string Замененный текст
   */
  static function tttt($text, array $data, $onlyExistsInData = true) {
    if (preg_match_all('/\{(\w+)\}/', $text, $m)) {
      foreach ($m[0] as $i => $v) {
        if ($onlyExistsInData) {
          if (isset($data[$m[1][$i]])) $text = str_replace($v, $data[$m[1][$i]], $text);
        }
        else {
          $text = str_replace($v, isset($data[$m[1][$i]]) ? $data[$m[1][$i]] : '', $text);
        }
      }
    }
    return $text;
  }

  static function hasTttt($text) {
    return preg_match('/\{(\w+)\}/', $text);
  }

  /**
   * Склеивает массив в строку с разделителями, помещая при этом значения
   * массива в шаблон
   *
   * @param array $arr Массив с перечислением
   * @param string $glue Разделитель
   * @param string $tpl Шаблон
   * @param null $key Ключ необходим в том случае, если элементом массива является массив
   *                  Ключем в этом случае будет являтся ключ того элемента этого подмассива,
   *                  который необходимо использовать для склеивания
   * @return string Склеенная по шаблону строка
   */
  static function enum($arr, $glue = ', ', $tpl = '$v', $key = null) {
    if (empty($arr) or !is_array($arr)) return '';
    $results = [];
    foreach ($arr as $k => $v) {
      if ($key) $v = $v[$key];
      $results[] = St::dddd($tpl, [
        'k' => $k,
        'v' => $v
      ]);
    }
    return implode($glue, $results);
  }

  /**
   * Тоже самое, что и St::enum(), только с измененныым порядком параметров
   *
   * @param   array $arr Массив с перечислением
   * @param   string $key Ключ необходим в том случае, если элементом массива является массив
   *                  Ключем в этом случае будет являтся ключ того элемента этого подмассива,
   *                  который необходимо использовать для склеивания
   * @param   string $glue Разделитель
   * @param   string $tpl Шаблон
   * @return  string  Склеенная по шаблону строка
   */
  static function enumK($arr, $key, $glue = ', ', $tpl = '$v') {
    return St::enum($arr, $glue, $tpl, $key);
  }

  function enumSsss($arr, $tpl, $glue = ', ') {
    if (!is_array($arr)) return '';
    foreach ($arr as $v) $results[] = St::ssss($tpl, $v);
    return isset($results) ? implode($glue, $results) : '';
  }

}
