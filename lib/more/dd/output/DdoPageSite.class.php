<?php

class DdoPageSite extends DdoPage {

  protected function initTpls() {
    parent::initTpls();
    $this->ddddByName['title'] = '`<h2><a href="`.'.$this->ddddItemLink.'.`">`.$v.`</a></h2>`';
    $this->ddddByType['date'] = 'dateStrSql($v, `d.m.Y`, `Y-m-d`)';
    $this->ddddByType['datetime'] = 'datetimeStrSql($v)';
    $this->ddddByType['commentsCount'] = '
$v ? (`<div class="smIcons">
<a class="comments`.($v > 2 ? `2` : ``).` shortComments"
title="комментарии (`.$v.`)" 
href="`.$pagePath.`/`.$id.`#msgs"><i></i>
`.$v.`
</a><div class="clear"><!-- --></div>
</div>`) : ``';

    $this->ddddByType['clicks'] = '$v ? `<div class="smIcons"><span class="view" title="Просмотров"><i></i>`.$v.`</span></div>` : ``';
  }

  /**
   * @param $page
   * @return DdoPageSite
   */
  static function factory($page, $layoutName, array $options = []) {
    $class = empty($page['module']) ? 'DdoPageSite' : 'DdoSpm'.ucfirst($page['module']);
    return new $class($page, $layoutName, $options);
  }

}
