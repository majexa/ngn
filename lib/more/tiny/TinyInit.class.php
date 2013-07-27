<?php

class TinyInit extends Options2 {

  protected $themeAdvancedStyles;

  protected $clearTags;

  public $cssFile;

  protected $theme;

  static $requiredOptions = ['themeType'];

  protected $tags;

  function init() {
    $this->tags = Config::getVar('tiny.'.$this->options['themeType'].'.allowedTags');
    $this->theme = ($this->options['themeType'] == 'site' ? 'advanced' : 'advanced');

    // Инициализация пользовательских CSS-классов 
    $_classes = Config::getVar('tiny.'.$this->options['themeType'].'.classes', true);
    $classes[] = 'Ссылка на скачивание=ifLink';
    $classes[] = 'Превьюшка=iiLink';
    foreach ($_classes as $v) $classes[] = "{$v['title']}={$v['class']}";
    if (is_array($classes)) $this->themeAdvancedStyles = implode(';', $classes);

    // Заменяем , на |
    foreach ($this->tags as $k => $v) $this->tags[$k] = str_replace(',', '|', $v);
    $this->clearTags = Misc::clearConfigTags($this->tags);
    $this->initCssFile();
  }

  protected function initCssFile() {
    $this->cssFile = empty($this->options['cssFile']) ? Sflm::get('css')->sflm->getCachedUrl('s2/css/common/tiny.css', true) : $this->options['cssFile'];
  }

  function getTheme() {
    return $this->theme;
  }

  /*
   * Возвращает список css-классов через ";"
   */
  function getThemeAdvancedStyles() {
    return $this->themeAdvancedStyles;
  }

  function getValidElements() {
    return implode(',', $this->tags);
  }

  function getPlugins() {
    if ($this->options['themeType'] == 'site') {
      return 'safari,fullscreen,inlinepopups';
    }
    else {
      return 'safari,inlinepopups,imageuploader,imagesuploader,fileuploader'.(in_array('table', $this->clearTags) ? ',table' : '').',fullscreen';
    }
  }

  function getThemeAdvancedDisable() {
    $disableBtns = [];
    $disableBtns[] = 'help';
    if (!in_array('s', $this->clearTags)) $disableBtns[] = 'strikethrough';
    if (!in_array('blockquote', $this->clearTags)) $disableBtns[] = 'blockquote';
    if (!in_array('sup', $this->clearTags)) $disableBtns[] = 'sup';
    if (!in_array('sub', $this->clearTags)) $disableBtns[] = 'sub';
    if (!in_array('sub', $this->clearTags)) $disableBtns[] = 'sub';
    if (!in_array('u', $this->clearTags)) $disableBtns[] = 'underline';
    if (!in_array('ul', $this->clearTags) or !in_array('li', $this->clearTags)) $disableBtns[] = 'bullist';
    if (!in_array('ol', $this->clearTags) or !in_array('li', $this->clearTags)) $disableBtns[] = 'numlist';
    return implode(',', Arr::append($disableBtns, Config::getVar('tiny.admin.disableBtns')));
  }

  function getThemeAdvancedBlockformats() {
    $tags = ['p'];
    foreach (['h2', 'h3', 'h4'] as $_tag) if (in_array($_tag, $this->clearTags)) $tags[] = $_tag;
    return implode(',', $tags);
  }

  function getTableButtons() {
    return in_array('table', $this->clearTags) ? 'tablecontrols' : '';
  }

}
