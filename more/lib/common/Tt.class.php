<?php

/**
 * Функции работы с html/php шаблонами через ф-ю require
 */
class Tt {
  use TtPath;

  /**
   * @var Req
   */
  protected $req;

  function __construct(Req $req = null) {
    $this->req = $req ?: O::get('Req');
  }

  /**
   * Выводит шаблон
   *
   * @param string $path
   * @param null $d
   * @param bool $quietly
   * @throws Exception
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
      throw new NotFoundException("Template '$path' not found.") ;
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
   * @param string $path Путь до шаблона
   * @return bool|string
   */
  function exists($path) {
    foreach (Ngn::$basePaths as $basePath) if (file_exists("$basePath/tpl/$path.php")) return "$basePath/tpl/$path.php";
    return false;
  }

  function enumPrefix(array $arr, $glue = ', ', $tpl = '$v', $prefix = '', $postfix = '', $key = null) {
    if (empty($arr) or !is_array($arr)) return '';
    return $prefix.$this->enum($arr, $glue, $tpl, $key).$postfix;
  }

  function enumInlineStyles($arr) {
    if (empty($arr)) return '';
    return $this->enumPrefix($arr, '; ', '$k.`: `.$v', ' style="', '"');
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
