<?php

/**
 * Функции работы с html/php шаблонами через ф-ю require
 */
class Tt {

  /**
   * Выводит шаблон
   *
   * @param string $path
   * @param string $data
   */
  function tpl($path, $d = null, $quietly = false) {
    if (($tplPath = $this->exists($path)) !== false) {
      $clearTplPath = preg_replace('/^(.*).php$/U', '$1', $tplPath);
      $body1 = "Begin Template \"$clearTplPath\"";
      $body2 = "End Template \"$clearTplPath\"";
      if (isset($_REQUEST['debugTpl'])) {
        $openCommentBegin = '<div style="border: 1px solid #077F00; padding: 3px; margin: 2px;">';
        $openCommentEnd = '';
        $closeCommentBegin = '';
        $closeCommentEnd = '</div>';
        $body1 = '<small style="color:#077F00;">Begin Template «<b>'.$clearTplPath.'</b>»</small>';
        $body2 = '<small style="color:#077F00;">End Template «<b>'.$clearTplPath.'</b>»</small>';
      }
      elseif (strstr($tplPath, '.js.php')) {
        $openCommentBegin = $closeCommentBegin = '/* ';
        $openCommentEnd = $closeCommentEnd = ' */';
      }
      else {
        $openCommentBegin = $closeCommentBegin = '<!-- ';
        $openCommentEnd = $closeCommentEnd = ' -->';
      }
      if (getConstant('TEMPLATE_DEBUG')) print "\n".$openCommentBegin.$body1.$openCommentEnd."\n";
      if (Err::$showNotices) {
        Err::noticeSwitch(false);
        $notices = true;
      }
      require $tplPath;
      if (isset($notices)) Err::noticeSwitchBefore();
      if (getConstant('TEMPLATE_DEBUG')) print "\n".$closeCommentBegin.$body2.$closeCommentEnd."\n";
    }
    elseif (!$quietly) {
      throw new Exception("Template '$path' not found.");
    }
  }

  function getTpl($path, $d = null) {
    ob_start();
    $this->tpl($path, $d);
    $c = ob_get_contents();
    ob_end_clean();
    return $c;
  }

  /**
   * Проверяет существует ли шаблон с указанным путём и если он
   * существует возвращает его путь, если нет - false.
   *
   * @param   string  Путь до шаблона
   * @return  mixed
   */
  function exists($path) {
    foreach (Ngn::$basePaths as $basePath) if (file_exists("$basePath/tpl/$path.php")) return "$basePath/tpl/$path.php";
    return false;
  }

  /**
   * @var Req
   */
  protected $req;

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

  function getPathRoot() {
    return ($p = Tt()->getPath(0)) ? $p : '/';
  }

  /**
   * Возвращает текущий адрес сайта вместе с 'http://'
   *
   * @return  string
   */
  function getHostPath() {
    return 'http://'.SITE_DOMAIN;
  }

  /**
   * Возвращает путь до страницы пользователя
   *
   * @param   integer   ID пользователя
   * @return  string    Путь до страницы пользователя
   */
  function getUserPath($userId, $quitely = false) {
    if ($quitely) {
      if (($path = Tt()->getControllerPath('userData', true)) != '') {
        return '//'.SITE_DOMAIN.$path.'/'.$userId;
      }
      return false;
    }
    else {
      return '//'.SITE_DOMAIN.Tt()->getControllerPath('userData').'/'.$userId;
    }
  }

  function getUserTag($userId, $login, $tpl = '`<a href="`.Tt()->getUserPath($id).`">`.$login.`</a>`') {
    if (!PageControllersCore::exists('userData')) return '<span class="user">'.$login.'</span>';
    else
      return St::dddd($tpl, [
        'id'    => $userId,
        'login' => $login
      ]);
  }

  function getUserTag2(array $user) {
    return $this->getUserTag($user['id'], $user['login']);
  }

  /*
  function getControllerPath($controller, $quietly = false) {
    return '/'.PageControllersCore::getControllerPath($controller, $quietly);
  }

  function getStrControllerPath($controller, $strName, $quietly = false) {
    static $paths;
    if (isset($paths[$controller.$strName])) return $paths[$controller.$strName];
    $path = db()->selectCell("SELECT path FROM pages WHERE controller=? AND strName=? ORDER BY id LIMIT 1", $controller, $strName);
    if (!$path) {
      if (!$quietly) Err::warning("Page with controller '$controller' not found");
      return '';
    }
    $paths[$controller.$strName] = $path;
    return $paths[$controller.$strName];
  }
  */

  /**
   * Возвращает URL с исключенными из него параметрами
   *
   * @param string  URL
   * @param array   Параметры для исключения
   */
  function getUrlDeletedParams($url, $params) {
    $parts = parse_url($url);
    parse_str($parts['query'], $out);
    foreach ($out as $k => $v) if (!in_array($k, $params)) $newParams[$k] = $v;
    return isset($newParams) ? $parts['path'].'?'.implode('&', $newParams) : $parts['path'];
  }

  /**
   * Склеивает массив в строку с разделителями, помещая при этом значения
   * массива в шаблон.
   *
   * @param   array   Массив с перечислением
   * @param   string  Разделитель
   * @param   string  Шаблон
   * @param   string  Ключ необходим в том случае, если элементом массива является массив
   *                  Ключем в этом случае будет являтся ключ того элемента этого подмассива,
   *                  который необходимо использовать для склеивания
   * @return  strgin  Склеенная по шаблону строка
   */
  function enum($arr, $glue = ', ', $tpl = '$v', $key = null) {
    if (empty($arr) or !is_array($arr)) return '';
    foreach ($arr as $k => $v) {
      if ($key) $v = $v[$key];
      $results[] = St::dddd($tpl, [
        'k' => $k,
        'v' => $v
      ]);
    }
    return implode($glue, $results);
  }

  function enumPrefix(array $arr, $glue = ', ', $tpl = '$v', $prefix = '', $postfix = '', $key = null) {
    if (empty($arr) or !is_array($arr)) return '';
    return $prefix.$this->enum($arr, $glue, $tpl, $key).$postfix;
  }

  function enumInlineStyles($arr) {
    if (empty($arr)) return '';
    return $this->enumPrefix($arr, '; ', '$k.`: `.$v', ' style="', '"');
  }

  /**
   * Тоже самое, что и Tt()->enum(), только с измененныым порядком параметров
   *
   * @param   array   Массив с перечислением
   * @param   string  Ключ необходим в том случае, если элементом массива является массив
   *                  Ключем в этом случае будет являтся ключ того элемента этого подмассива,
   *                  который необходимо использовать для склеивания
   * @param   string  Разделитель
   * @param   string  Шаблон
   * @return  strgin  Склеенная по шаблону строка
   */
  function enumK($arr, $key, $glue = ', ', $tpl = '$v') {
    return Tt()->enum($arr, $glue, $tpl, $key);
  }

  function enumDddd($arr, $tpl, $glue = ', ') {
    if (!is_array($arr)) return '';
    foreach ($arr as $v) $results[] = St::dddd($tpl, $v);
    return isset($results) ? implode($glue, $results) : '';
  }

  function enumSsss($arr, $tpl, $glue = ', ') {
    if (!is_array($arr)) return '';
    foreach ($arr as $v) $results[] = St::ssss($tpl, $v);
    return isset($results) ? implode($glue, $results) : '';
  }

  function enumSsss2(array $arr, $tpl = '$v', $glue = ', ') {
    foreach ($arr as $k => $v) $results[] = St::ssss($tpl, [
      'k' => $k,
      'v' => $v
    ]);
    return isset($results) ? implode($glue, $results) : '';
  }

  function getDbTree($tree, $tplNode, $tplLeaf = '', $tplNodesBegin = '', $tplNodesEnd = '', $extData = null) {
    if (!$tree) return false;
    $o = new DbTreeTpl();
    $o->setNodes($tree);
    if ($extData) $o->setExtData($extData);
    if (!$tplLeaf) {
      $o->setTpl($tplNode);
    }
    else {
      $o->setNodeTpl($tplNode);
      $o->setLeafTpl($tplLeaf);
      $o->setNodesBeginTpl($tplNodesBegin);
      $o->setNodesEndTpl($tplNodesEnd);
    }
    return $o->html();
  }

  function hasBlocks($action) {
    return !in_array($action, ['new', 'edit', 'complete']);
  }

  function tagParams(array $params) {
    $s = '';
    foreach ($params as $k => $v) {
      if ($v === null) $s .= ' '.$k;
      else $s .= ' '.$k.'="'.$v.'"';
    }
    return $s;
  }

  function httpLink($url) {
    return '<a href="http://'.$url.'" target="_blank">'.$url.'</a>';
  }

}
