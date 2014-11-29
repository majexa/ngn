<?php

/**
 * @property Req $req
 */
trait TtPath {

  /**
   * Возвращает путь до текущего раздела без учета QUERY_STRING.
   * Так же можно указать какое количество частей пути нужно получить.
   * Частью пути называется строка отделённая слэшем.
   * Пример:
   * Если текщий URL: http://site.com/path.to/the/page
   * Tt()->getPath(2) вернёт строку '/path.to/the'
   *
   * @param   string Кол-во частей пути, которое необходимо получить
   * @return  Путь до страницы
   */
  function getPath($paramsN = null) {
    if ($paramsN === 0) return '/'.$this->req->getBase();
    if ($paramsN !== null) {
      $params2 = [];
      for ($i = 0; $i < $paramsN; $i++) $params2[] = isset($this->req->params[$i]) ? $this->req->params[$i] : 0;
      return '/'.$this->req->getBase().implode('/', $params2);
    }
    return '/'.$this->req->initPath;
  }

  function getPathRoot() {
    return ($p = $this->getPath(0)) ? $p : '/';
  }

  function getPathFrom($n) {
    return implode('/', array_slice($this->req->params, $n, count($this->req->params)));
  }

  function getPathLast($n) {
    $s = '';
    for ($i = count($this->req->params) - $n; $i < count($this->req->params); $i++) $s .= '/'.$this->req->params[$i];
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

  /**
   * Возвращает путь до страницы пользователя
   *
   * @param   integer   ID пользователя
   * @return  string    Путь до страницы пользователя
   */
  function getUserPath($userId, $quietly = false) {
    return '{depricated}';
  }

}