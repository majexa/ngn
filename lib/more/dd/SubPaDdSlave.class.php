<?php

class SubPaDdSlave extends SubPa {

  /**
   * @var CtrlDdItems
   */
  protected $ctrl;

  protected $user;

  public $masterItem;

  function init() {
    $this->ctrl->initItems();
    $masterStrName = DdCore::getMasterStrName($this->ctrl->strName);
    if (!empty($this->ctrl->itemId)) {
      $this->ctrl->setItemData();
      if (empty($this->ctrl->itemData)) {
        array_pop($this->ctrl->d['pathData']);
        $this->disable = true;
        return;
      }
      $this->masterItem = $this->ctrl->itemData[DdCore::masterFieldName];
    }
    else {
      // Необходимо получить запись, т.к. $this->itemData существует только
      // при выводе одной записи
      /* @var $oDdItems DdItemsPage */
      $oDdItems = O::get('DdItemsPage', $this->ctrl->page['parentId']);
      $oDdItems->strict = true;
      if (!isset($this->ctrl->d['filters']['v'][0]) or
        $this->ctrl->d['filters']['v'][0] != DdCore::masterFieldName
      ) {
        throw new Exception('Filter "'.DdCore::masterFieldName.'" must be defined');
        return;
      }
      $this->masterItem = $oDdItems->getItemF($this->ctrl->d['filters']['v'][1]);
    }
    if (empty($this->masterItem)) throw new EmptyException('$this->masterItem');
    if ($this->masterItem['authorId'] == Auth::get('id')) {
      $this->ctrl->setPrivs(['edit', 'create'], true);
    }
    $this->ctrl->d['masterItem'] = $this->masterItem;
    // author mode
    $this->initTitle();
    if ($this->ctrl->page->getS('ownerMode') == 'author') {
      $this->ctrl->d['itemUser'] = $this->user = DbModelCore::get('users', $this->masterItem['userId']);
      $this->initUserMenu();
    }
  }

  protected function initUserMenu() {
    if ($this->ctrl->action != 'list' or $this->ctrl->page->getS('ownerMode') != 'author') return;
    $this->ctrl->d['submenu'] = UserMenu::get($this->user, $this->ctrl->page['id'], $this->ctrl->action);
  }

  function setItemsOnItem() {
    $this->ctrl->oManager->oItems->cond->addF($this->masterField['name'], $this->masterItem['id']);
    $this->ctrl->setItemsOnItem();
  }

  protected $title;

  protected function initTitle() {
    if (!empty($this->masterItem['title'])) $this->title = $this->masterItem['title'];
    elseif (!empty($this->ctrl->settings['itemTitle'])) $this->title = $this->ctrl->settings['itemTitle'];
    if (isset($this->title) and $this->ctrl->action == 'list') $this->ctrl->setPageTitle($this->title);
  }

  function initAuthorPath() {
    if ($this->ctrl->page->getS('ownerMode') != 'author') return;
    Misc::checkEmpty($this->user);
    $masterN = count($this->ctrl->d['pathData']) - 2;
    $this->ctrl->d['pathData'] = Arr::injectAfter($this->ctrl->d['pathData'], $masterN, [[
        'link'  => $this->ctrl->d['pathData'][$masterN]['link'].'/u.'.$this->user['id'],
        'title' => UsersCore::name($this->user)
      ]]);
  }

  function initPath() {
    $this->ctrl->callDirect('initPath');
    // Если это slave-контроллер, то ссылку на этот раздел без фильтра нужно убрать
    if ($this->ctrl->adminMode or !$this->ctrl->page['slave']) return;
    if (!isset($this->ctrl->itemData)) {
      $replacedPathN = count($this->ctrl->d['pathData']) - 1;
    }
    else {
      $replacedPathN = count($this->ctrl->d['pathData']) - 2;
    }
    $this->ctrl->d['pathData'][$replacedPathN]['link'] .= '/v.'.DdCore::masterFieldName.'.'.$this->masterItem['id'];
    $this->ctrl->d['pathData'][$replacedPathN]['title'] = $this->title ? : '{empty}';
  }

}