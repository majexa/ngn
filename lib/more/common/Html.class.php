<?php

class Html {

  /**
   * Добавляет к тэгу строку, в которой можно использовать ключевые символы $1
   * для вставки значения "name" этого тэга и $2 для вставки значения параметра "value".
   *
   * @param   string  Исходный HTML код
   * @param   string  Значение параметра "name" тэга
   * @param   string  HTML код добавляющийся вконце тэга, если значение есть)
   * @return  string  HTML код добавляющийся вконце тэга, если значение пустое)
   */
  static function inputAppend($html, $name, $appender, $emptyAppender = '') {
    $name = str_replace('[', '\\[', $name);
    $name = str_replace(']', '\\]', $name);
    // Заменяем input'ы с заполненным значением
    $html = preg_replace([
        '/<[^>]*name="('.$name.')"[^>]*value="([^>"]+)"[^>]*>/um',
      ], '$0'.$appender, $html);

    // Заменяем пустые input'ы
    $html = preg_replace([
        '/<[^>]*name="('.$name.')"[^>]*value=""[^>]*>/um',
      ], '$0'.$emptyAppender, $html);

    return $html;
  }

  /**
   * Убирает тэг из HTML-кода
   *
   * @param   string  HTML
   * @param   string  Имя тэга
   * @param   array   Необходимый параметр тэга
   *                  Пример: array('img', './u/img.png')
   * @return  string  HTML
   */
  static function removeTag($html, $tagName, $param = null) {
    if ($param) {
      $regex = '/<(?='.$tagName.')([^>]+)'.$param[0].'=("|\'|)'.$param[1].'("|\'|)([^>]*)>/';
    }
    else
      $regex = '/<(?='.$tagName.')([^>]*)>/';
    return preg_replace($regex, '', $html);
  }

  static function addParam($html, $name, $value, $tags = null) {
    if (is_array($tags)) $tags = implode('|', $tags);
    return preg_replace('/<(?!\/)(?='.$tags.')([^>]+)>/', '<$1 '.$name.'="'.$value.'">', $html);
  }


  // -------------------------------------------------------------------------------------------
  static function inputReplace($html, $name, $replacer) {
    $name = str_replace('[', '\[', $name);
    $name = str_replace(']', '\]', $name);
    return preg_replace('/<[^>]*name="'.$name.'"[^>]*>/um', $replacer, $html);
  }

  static function inputAddClass($html, $types, $class) {
    return preg_replace('/(<input(?:[^>]*)type="(?:'.implode('|', $types).')")((?:[^>]*)>)/um', '$1 class="'.$class.'"$2', $html);
  }

  static function inputRevalue($html, $name, $value) {
    return preg_replace('/<([^>]*)name="'.$name.'"([^>]*)value="([^>]*)"([^>]*)>/um', '<$1name="'.$name.'"$2value="'.$value.'"$4>', $html);
  }

  static function inputIsValue($html, $name) {
    return preg_match('/<([^>]*)name="'.$name.'"([^>]*)value="([^>]+)"([^>]*)>/um', $html);
  }

  static function inputPrepend($html, $name, $prepender) {
    return preg_replace('/<[^>]*name="('.$name.')"[^>]*value="([^>"]+)"[^>]*>/um', $prepender.'$0', $html

    );
  }

  static function inputNameToArray($html, $arrayName) {
    return preg_replace('/(<[^>]*name=")([^>^"]*)("[^>]*>)/um', '$1'.$arrayName.'[$2]$3', $html);
  }

  static function inputExists($html, $name) {
    return preg_match('/<[^>]*name="'.$name.'"[^>]*>/um', $html);
  }

  static function replaceParam($html, $name, $value, $tags = null) {
    if (is_array($tags)) $tags = implode('|', $tags);
    return preg_replace('/<('.$tags.')(?!\/)([^>]+)'.$name.'=("|\'|)([^>^"]*)("|\'|)([^>]*)>/', '<$1'.$name.'=$2'.$value.'$4$5>', $html);
  }

  static function replaceParam2($html, $tag, $paramName, $paramOldValue, $paramNewValue) {
    $paramOldValue = str_replace('.', '\.', $paramOldValue);
    $paramOldValue = str_replace('/', '\/', $paramOldValue);
    return preg_replace('/(<'.$tag.')([^>]+)('.$paramName.'=)("|\'|)'.$paramOldValue.'("|\'|)([^>]*>)/', '$1$2$3"'.$paramNewValue.'"$6', $html);
  }

  static function getTagNames($html, $tag, $type = null) {
    if ($type) $typeCond = 'type="'.$type.'"[^>]*';
    preg_match_all('/<'.$tag.'[^>]*'.$typeCond.'name="([a-zA-Z_]*)"[^>]*>/um', $html, $m);
    return $m[1] ? $m[1] : [];
  }

  static function getInputDataNames($html) {
    $names = [];
    $names = Arr::append($names, self::getTagNames($html, 'textarea'));
    $names = Arr::append($names, self::getTagNames($html, 'input', 'text'));
    $names = Arr::append($names, self::getTagNames($html, 'input', 'password'));
    $names = Arr::append($names, self::getTagNames($html, 'input', 'radio'));
    $names = Arr::append($names, self::getTagNames($html, 'input', 'file'));
    $names = Arr::append($names, self::getTagNames($html, 'input', 'button'));
    $names = Arr::append($names, self::getTagNames($html, 'input', 'submit'));
    $names = Arr::append($names, self::getTagNames($html, 'select'));
    return $names;
  }

  static function getInputValue($html, $name) {
    preg_match('/<[^>]*name="'.$name.'"[^>]*value="([^>^"]*)"[^>]*>/um', $html, $m);
    return $m[1];
  }

  static function getParam($html, $attr) {
    preg_match('/<[^>]*'.$attr.'="([^>^"]*)"[^>]*>/um', $html, $m);
    return $m[1];
  }

  static function emptyHtml($html) {
    $html = htmlspecialchars_decode($html);
    $html = strip_tags($html);
    $html = str_replace([' ', " ", "\n", "\r"], '', $html);
    return preg_match('/[a-zA-ZА-Яа-я\-.,_]+/', $html) ? false : true;
  }

  static function toParams(array $params) {
    return Tt()->enum($params, '', ' $k="$v"');
  }

  /**
   * Enter description here ...
   * @param string $name
   * @param array $options
   * @param string $default
   * @param array  (tagId, class, noSelectTag, defaultCaption)
   */
  static function select($name, $options, $default = null, array $opts = []) {
    $dataTags = isset($opts['data']) ? ' '.Tt()->enum($opts['data'], '', '`data-`.$k.`="`.$v.`"`') : '';
    if (empty($opts['noSelectTag'])) {
      $html = "\n<select name=\"$name\"".$dataTags.
        (!empty($opts['tagId']) ? ' id="'.$opts['tagId'].'"' : "").
        (!empty($opts['id']) ? ' id="'.$opts['id'].'"' : "").
        (!empty($opts['class']) ? ' class="'.$opts['class'].'"' : "").'>';
    }
    else {
      $html = '';
    }
    if (!empty($opts['defaultCaption'])) $default = $opts['defaultCaption'];
    foreach ($options as $key => $val) {
      $k = $key;
      if (!empty($opts['defaultCaption'])) $k = $val;
      $html .= "\n\t<option value=\"".$key."\"".($k == $default ? ' selected' : '').">$val</option>";
    }
    if (empty($opts['noSelectTag'])) $html .= "\n</select>\n";
    return $html;
  }

  /**
   * Возвращает имена полей по типам
   *
   * @param   array   Полей с полной информацией о них
   * @param   array   Типы полей
   * @return  array   Имена
   */
  static function getFieldNames($fields, $types) {
    $names = [];
    foreach ($fields as $v) {
      if (in_array($v['type'], $types)) {
        $names[] = $v['name'];
      }
    }
    return $names;
  }

  static function userTag(&$d) {
    return '<a href="'.Tt()->getUserPath($d['userId'] ? $d['userId'] : $d['id']).'">'.$d['login'].'</a>';
  }

  static function replaceFileInput(&$d, $type, $title1) {
    if (!$d['fields']) throw new Exception("\$d['fields'] not defined");
    // Для всех файловых полей добавляем линк на удаление файла
    foreach ($d['fields'] as $v) {
      if ($v['type'] != $type) continue;
      $name = $v['name'];
      $deleteBtn = $v['required'] ? '' : '<a href="'.Tt()->getPath().'?a=deleteFile'.(isset($d['itemId']) ? '&itemId='.$d['itemId'] : '').'&fieldName=$1" '.'target="_blank" onclick="if (confirm(\'Вы уверены?\')) window.location=this.href; '.'return false;" class="ddelete">Удалить</a>';
      $d['form'] = Html::inputAppend($d['form'], $name, '<div class="iconsSet">'.'<a href="'.Tt()->getPath(0).UPLOAD_DIR.'/$2" target="_blank" class="'.$type.'"><i></i>'.$title1.'</a>'.$deleteBtn.'<div class="clear"><!-- --></div></div>');
    }
  }

  static function replaceImageInput(&$d, $type, $title1) {
  }

  /* Укорачивает строку в LABEL-ах */
  static function cutLabel($html, $n = 20) {
    return preg_replace_callback('/(<label for="rub[^>]*)(>\s*<input[^>]*>\s+)(.*)(<\/label>)/U', create_function('$m', '
        return mb_strlen($m[3], CHARSET) > '.$n.' ?
          $m[1].$m[2].\'<span class="tooltip" title="\'.$m[3].\'">\'.Misc::cut($m[3], '.$n.').\'</span>\'.$m[4] :
          $m[1].$m[2].$m[3].$m[4];
        '), $html);
  }

  static function getInnerContent($content, $bef, $aft = '') {
    $len = strlen($bef);
    $posBef = strpos($content, $bef);
    if ($posBef === false) return '';
    $posBef += $len;
    if (empty($aft)) {
      // try to search up to the end of line 
      $posAft = strpos($content, "\n", $posBef);
      if ($posAft === false) $posAft = strpos($content, "\r\n", $posBef);
    }
    else
      $posAft = strpos($content, $aft, $posBef);

    if ($posAft !== false) $rez = substr($content, $posBef, $posAft - $posBef);
    else
      $rez = substr($content, $posBef);
    return $rez;
  }

  static function subDomainLinks($html, $subdomain) {
    return preg_replace('/(href|src|action)="(?!\/\/|http:\/\/)\/*([^"]+)/', '$1="//'.$subdomain.'.'.SITE_DOMAIN.'/$2', $html);
  }

  static function baseDomainLinks($html) {
    return preg_replace_callback('/(href|src|action)="(?!\/\/|http:\/\/|\w+:\w+)\/*([^"]+)/', function($m) {
        return $m[1].'="//'.SITE_DOMAIN.'/'.ltrim($m[2], '/');
      }, $html);
  }

  static function removeTagByClass($s, $class, $simpleTag = false) {
    return $simpleTag ? preg_replace('/<\w+[^>]*class="[^"]*'.$class.'[^"]*"[^>]*>/', '', $s) : preg_replace('/<\w+[^>]*class="[^"]*'.$class.'[^"]*"[^>]*>[^<]+<\/\w+>/', '', $s);
  }

  static function defaultOption($t = '— выберите —') {
    return ['' => $t];
  }

  static function dataParams(array $data) {
    return ' '.Tt()->enum($data, ' ', '`data-`.$k.`="`.$v.`"`');
  }

}
