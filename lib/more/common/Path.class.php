<?php

class Path {

  protected $req;

  function __construct(Req $req) {
    $this->req = $req;
  }

  /**
   * Возвращает путь до текущего раздела без учета QUERY_STRING.
   * Так же можно указать какое количество частей пути нужно получить.
   * Частью пути называется строка отделённая слэшем.
   * Пример:
   * Если текщий URL: http://site.com/path.to/the/page
   * Tt()->getPath(2) вернёт строку '/path.to/the'
   *
   * @param   string  Кол-во частей пути, которое необходимо получить
   * @return  Путь до страницы
   */
  function getPath($paramsN = null) {
    if (!isset($this->req)) {
      $this->req = $req = O::get('Req');
    }
    if ($paramsN === 0) return $this->req->getBase();
    if ($paramsN !== null) {
      for ($i = 0; $i < $paramsN; $i++) $params2[] = isset($this->req->params[$i]) ? $this->req->params[$i] : 0;
      return $this->req->getBase().implode('/', $params2);
    }
    return '/'.$this->req->initPath;
  }

  function getPathRoot() {
    return ($p = Tt()->getPath(0)) ? $p : '/';
  }

  function getPathFrom($n) {
    $params = O::get('Req')->params;
    $s = '';
    for ($i = count($params) - $n; $i < count($params); $i++) $s .= '/'.$params[$i];
    return $s;
  }

  function getPathLast($n) {
    $params = O::get('Req')->params;
    $s = '';
    for ($i = count($params) - $n; $i < count($params); $i++) $s .= '/'.$params[$i];
    return $s;
  }

  function getPathWithoutOrder($path, $newParam = null) {
    return $this->getPathReplaceFilter($path, 'oa?', $newParam);
  }

  function getPathWithoutDate($path, $newParam = null) {
    return $this->getPathReplaceFilter($path, 'd', $newParam);
  }

  function getPathReplaceFilter($path, $filter, $newParam = null) {
    $regex = '/(.*)(\/)('.$filter.'\.[a-zA-Z0-9.-]+)(.*)/';
    if (preg_match($regex, $path)) return preg_replace($regex, '$1'.($newParam ? '$2'.$newParam : '').'$4', $path);
    else
      return $path.($newParam ? '/'.$newParam : '');
  }

}