<?php

class FormatText extends Options2 {

  /**
   * @var Jevix
   */
  public $jevix;

  public $thumbsDir;

  public $iiSizes;

  public $options = [
    'allowedTagsConfigName' => 'tiny.site.allowedTags'
  ];

  function init() {
    $this->jevix = new Jevix();
    $this->jevix->cfgSetTagCutWithContent(['script', 'iframe', 'style']);
    $this->iiSizes = Config::getVar('iiSizes');
    $this->jevix->cfgSetAutoBrMode(false);
  }

  function __call($method, $args) {
    if (Misc::hasPrefix('cfg', $method)) {
      call_user_func_array([$this->jevix, $method], $args);
      return $this;
    }
    return call_user_func_array([$this, $method], $args);
  }

  /**
   * Очищает, типографирует HTML
   *
   * @param   string    HTML
   * @param   string    Директория для картинок. Пример: 13/22/333
   * @return  string    Преобразованный HTML
   */
  function html($text) {
    $text = trim($text);
    $text = str_replace([
      "\r", //"\n",
      "<hr /><br />", "</div><br />", "[b]", "[/b]", "[i]", "[/i]"
    ], [
      "", //"<br/>",
      "<hr/>", "</div>", "<b>", "</b>", "<i>", "</i>"
    ], $text);
    if (isset($this->thumbsDir)) {
      $urlParser = new UrlParserThumbs(WEBROOT_PATH, SITE_WWW, UPLOAD_DIR.'/'.INLINE_IMAGES_TEMP_DIR, UPLOAD_DIR.'/'.INLINE_IMAGES_THUMB_DIR, $this->thumbsDir);
      $urlParser->thumbW = $this->iiSizes['w'];
      $urlParser->thumbH = $this->iiSizes['h'];
      $text = $urlParser->makeClickableLinks($text);
    }
    else {
      $urlParser = new UrlParser();
      $text = $urlParser->makeClickableLinks($text);
    }
    $text = str_replace("</quote><br />", "</quote>", $text);
    $text = str_replace("</quote><br />", "</quote>", $text);
    $text = str_replace('http://mailto:', 'mailto:', $text);
    $text = $this->cleanHtml($text);
    return $text;
  }

  /**
   * Типографиреут текст
   *
   * @param string $text Текст
   * @return string
   */
  function typo($text) {
    return $this->cleanText($text);
  }

  protected function cleanText($text) {
    $errors = [];
    return $this->jevix->parse($text, $errors);
  }

  protected function cleanHtml($html) {
    $tags = [];
    $params = [];
    if (($confTags = Config::getVar($this->options['allowedTagsConfigName'])) !== false) {
      foreach ($confTags as $v) {
        $v = str_replace(',', '|', $v);
        $v = strtolower($v);
        if (preg_match('/^([a-z0-9]+)\[([a-z][a-z0-9|]*)\]$/', $v, $m)) {
          $tags[] = $m[1];
          $params[$m[1]] = explode('|', $m[2]);
        }
        elseif (preg_match('/^([a-z0-9]+)$/', $v, $m)) {
          $tags[] = $m[1];
        }
      }
    }
    $this->jevix->cfgAllowTags($tags);
    if ($params) {
      foreach ($params as $tag => $pms) {
        $this->jevix->cfgAllowTagParams($tag, $pms);
      }
    }
    foreach (['br', 'hr', 'img'] as $v) if (in_array($v, $tags)) $shortTags[] = $v;
    $this->jevix->cfgSetTagShort($shortTags ? $shortTags : []);
    foreach (['param', 'embed', 'td'] as $v) if (in_array($v, $tags)) $emptyTags[] = $v;
    $this->jevix->cfgSetTagIsEmpty(isset($emptyTags) ? $emptyTags : []);
    if (in_array('a', $tags)) $this->jevix->cfgSetTagParamsRequired('a', 'href');
    $errors = [];
    $html = preg_replace('/<img[^>]+\\/>/', '<img$1>', $html); // fix for jevix bug
    $html = $this->jevix->parse($html, $errors);
    $html = str_replace('</quote><br/>', '</quote>', $html);
    return $html;
  }

}
