<?php

class Tpl {

  /**
   * Приводит текст вида "http://site1.com, http://site2.com, ..." в
   * список тэгов:
   * <a href="http://site1.com" target="_blank">http://site1.com</a>,
   * <a href="http://site2.com" target="_blank">http://site2.com</a>,
   * <a href="..." target="_blank">...</a>
   *
   * @param string $t Ссылки через запятую
   * @param string $delimiter Разделитель
   * @param string $tpl Шаблон ссылки
   * @return mixed|string
   */
  static function urls($t, $delimiter = ',', $tpl = '<li><a href="$1" target="_blank">$1</a></li>') {
    if (trim($t) == '') return '';
    return preg_replace('/([^'.$delimiter.']*)'.$delimiter.'/u', $tpl.$delimiter, $t.$delimiter);
  }

  /**
   * Преобразует URL в читаемый адрес без http[s]://
   *
   * @param $url
   * @return mixed
   */
  static function clearUrl($url) {
    $url = preg_replace('/^https?:\/\/(.*)$/', '$1', $url);
    $url = O::get('IdnaConvert')->decode($url);
    if (preg_match('/^([^\/]*)\/*$/', $url)) return preg_replace('/^([^\/]*)\/*$/', '$1', $url);
    return $url;
  }

  /**
   * Возвращает URL с исключенными из него GET-параметрами
   *
   * @param string $url URL
   * @param array $params Параметры для исключения
   * @return string
   */
  static function removeUrlGetParams($url, array $params) {
    $parts = parse_url($url);
    parse_str($parts['query'], $out);
    foreach ($out as $k => $v) if (!in_array($k, $params)) $newParams[$k] = $v;
    return isset($newParams) ? $parts['path'].'?'.implode('&', $newParams) : $parts['path'];
  }

  static function ol($items) {
    $s = '<ol>';
    foreach ($items as $item) $s .= '<li>'.$item.'</li>';
    return $s.'</ol>';
  }

}