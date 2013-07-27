<?php

class DdoPage extends Ddo {

  /**
   * @var DbModelPages
   */
  protected $page;

  /**
   * Массив с данными мастер-раздела
   *
   * @var array
   */
  protected $masterPage;

  function __construct(DbModelPages $page, $layoutName, array $options = []) {
    $this->page = $page;
    parent::__construct($this->page['strName'], $layoutName, $options);
    if (!empty($this->page['settings']['slavePageId'])) {
      $this->masterPage = DbModelCore::get('pages', $this->page['settings']['slavePageId']);
      $this->ddddItemLink = '`'.Tt()->getPath(0).$this->masterPage['path'].'/v.'.DdCore::masterFieldName.'.`.$id';
    }
    else {
      if (PageControllersCore::isProfiles($page['controller'])) $this->ddddItemLink = '`'.PageControllersCore::getControllerPath('userData').'/`.$authorId';
      else $this->ddddItemLink = '$pagePath.`/`.$id';
    }
  }

  protected function getSettings() {
    return new DdoPageSettings($this->page);
  }

  function initFields() {
    parent::initFields();
    if (($hookPaths = Hook::paths('ddo/initFields', $this->page['module'])) !== false) foreach ($hookPaths as $v) include $v;
    return $this;
  }

  protected function htmlEl(array $data) {
    $html = parent::htmlEl($data);
    if ($this->page->getS('ownerMode') != 'userGroup') return $html;
    return Html::subDomainLinks($html, DbModelCore::get('userGroup', $data['userGroupId'])->r['name']);
  }

  protected function getPagePath() {
    return isset($this->pagePath) ? $this->pagePath : $this->page['path'];
  }

  function itemsBegin() {
    if (($s = parent::itemsBegin()) === '') return '';

    return Sflm::get('css')->getTag(Sflm::get('css')->sflm->getCachedUrl('s2/css/common/ddItemWidth.css?pageId='.$this->page['id'])).$s;
  }

}