<?php

class Req extends ArrayAccesseble {
  use Options;

  /**
   * Cтрока запроса
   *
   * @var string
   */
  public $initPath;

  /**
   * Cтрока запроса без QUERY_STRING прошедшая санитаризацию
   *
   * @var string
   */
  public $path;

  /**
   * Параметры, начиная с первого нужного
   *
   * @var array
   */
  public $params;

  /**
   * Исходные параметра
   *
   * @var array
   */
  public $initParams;

  /**
   * Есть ли слэш на конце URL'а
   *
   * @var bool
   */
  public $lastSlash = false;

  /**
   * @var integer Номер текущей страницы (/pg{N})
   */
  public $pg;

  /**
   * @var $_REQUEST
   */
  public $r;

  /**
   * @var $_POST
   */
  public $p;

  /**
   * @var $_GET
   */
  public $g;

  /**
   * @var $_SESSION
   */
  public $s;

  /**
   * @var $_FILES
   */
  public $files;

  function __construct(array $options = []) {
    $this->setOptions($options);
    if (!isset($this->options['uri'])) $this->options['uri'] = $_SERVER['REQUEST_URI'];
    // Берём путь из REQUEST_URI
    if (!trim($this->options['uri'], '/')) {
      $path = '/';
    } else {
      $uriData = parse_url($this->options['uri']);
      if (empty($uriData['path'])) throw new Exception(var_export($uriData, true));
      $path = $uriData['path'];
    }
    if ($path[0] == '/') $path = substr($path, 1, strlen($path)); // Убираем первый слэш
    if ($path[strlen($path) - 1] == '/') {
      $path = substr($path, 0, strlen($path) - 1); // Убираем первый слэш
      $this->lastSlash = true;
    }
    $this->initPath = $path;
    $this->setPathParams();
    $this->path = implode('/', $this->params);
    if ($this->params) {
      foreach ($this->params as $p) {
        if (preg_match('/pg([a-z]*)(\d+)/', $p, $m)) {
          $this->pg[$m[1] ? : ''] = $m[2];
        }
      }
    }
    $new = [];
    $this->g = $_GET;
    if (!empty($uriData['query'])) {
      parse_str($uriData['query'], $d);
      $this->g = $d;
      $new = $d;
    }
    foreach ($_REQUEST as $k => $v) $new[str_replace('amp;', '', $k)] = $v;
    $this->r = $new;
    if (!empty($this->r['a'])) $this->r['action'] = $this->r['a'];
    $this->p = $_POST;
    $this->files = self::convertFiles($_FILES);
  }

  static function convertFiles(array $FILES) {
    $files = [];
    foreach ($FILES as $key => $data) $files[$key] = self::fixFilesArray($data);
    return $files;
  }

  static protected function fixFilesArray($data) {
    if (!isset($data['tmp_name']) or !is_array($data['tmp_name'])) return $data;
    $fileKeys = ['error', 'name', 'size', 'tmp_name', 'type'];
    $files = $data;
    foreach ($fileKeys as $k) unset($files[$k]);
    foreach (array_keys($data['tmp_name']) as $key) {
      $files[$key] = self::fixFilesArray([
        'error'    => isset($data['error'][$key]) ? $data['error'][$key] : null,
        'name'     => isset($data['name'][$key]) ? $data['name'][$key] : null,
        'type'     => isset($data['type'][$key]) ? $data['type'][$key] : null,
        'tmp_name' => $data['tmp_name'][$key],
        'size'     => isset($data['size'][$key]) ? $data['size'][$key] : null
      ]);
    }
    return $files;
  }

  /**
   * Разбирает строку пути к странице со слэшами на параметры
   *
   * @return array|bool
   */
  private function setPathParams() {
    if ($this->params) return $this->params;
    $this->params = [];
    if (!$this->initPath) return false;
    $s = $this->initPath;
    $params = explode('/', $s);
    $this->initParams = $params;
    $newParams = [];
    $n = 0;
    if (defined('FIRST_URL_PARAM_N')) {
      for ($i = FIRST_URL_PARAM_N; $i < count($params); $i++) {
        $newParams[$n] = $params[$i];
        $n++;
      }
      $this->params = $newParams;
    }
    else {
      $this->params = $this->initParams;
    }
    return $this->params;
  }

  protected $base;

  function getBase() {
    if (isset($this->base)) return $this->base;
    $firstParamN = defined('FIRST_URL_PARAM_N') ? FIRST_URL_PARAM_N : 0;
    $p = [];
    for ($i = 0; $i < $firstParamN; $i++) {
      $p[] = $this->initParams[$i];
    }
    return $this->base = implode('/', $p);
  }

  function getAbsBase() {
    return 'http://'.SITE_DOMAIN;
  }

  /**
   * @api
   * Возвращает значение параметра $n и проверяет его на `empty()`
   *
   * @param integer $n Начинается с нуля http://site.com/item/123. для item $n=0.
   * @return mixed
   * @throws NotLoggableError
   */
  function param($n) {
    if (empty($this->params[$n])) throw new NotLoggableError("params[$n] is empty");
    return $this->params[$n];
  }

  /**
   * @api
   * Возвращает значение `$_REQUEST[$name]` и проверяет его на `isset()`
   *
   * @param $name
   * @return mixed
   * @throws Exception
   */
  function rq($name) {
    if (!isset($this->r[$name])) throw new NotLoggableError("\$_REQUEST[$name] not defined");
    return $this->r[$name];
  }

  /**
   * @api
   * Возвращает значение `$_REQUEST[$name]` и проверяет его на `empty()`
   *
   * @param $name
   * @return mixed
   * @throws Exception
   */
  function reqNotEmpty($name) {
    if (empty($this->r[$name])) throw new Exception("\$_REQUEST[$name] can not be empty. URI: ".$_SERVER['REQUEST_URI'].'. r: '.getPrr($this->r));
    return $this->r[$name];
  }

  /**
   * @api
   * Возвращает значение `$_REQUEST[$name]` или пустую строку, если не прошла проверка на `empty()`
   *
   * @param $name
   * @return string
   */
  function reqAnyway($name) {
    if (empty($this->r[$name])) return '';
    return $this->r[$name];
  }

  function path($offset = 0, $limit = null) {
    if (!$offset and !$this->params) return '';
    return '/'.implode('/', array_slice($this->params, $offset, $limit ? : count($this->params)));
  }

  function isDebug() {
    return isset($_COOKIE['debugKey']) and ($k = Ngn::debugKey()) and $_COOKIE['debugKey'] == $k;
  }

  function forceAuth() {
    return !empty($_COOKIE['forceAuth']) or !empty($this->r['forceAuth']);
  }

  /**
   * @return Req
   */
  static function get() {
    return O::get('Req');
  }

}
