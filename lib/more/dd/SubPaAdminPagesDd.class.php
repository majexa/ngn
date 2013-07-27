<?php

class SubPaAdminPagesDd extends SubPa {

  /**
   * Page Controller
   *
   * @var CtrlPage
   */
  public $pageCtrl;

  function init() {
    if (!PageControllersCore::exists($this->ctrl->page['controller'])) throw new Exception('Controiller class of module "'.$this->ctrl->page['controller'].'" not exists.');
    $req = new Req();
    $req->params = Arr::sliceFrom($req->params, 3);
    $this->ctrl->d['pageCtrl'] = $this->pageCtrl = PageControllersCore::getController($this->ctrl->router, $this->ctrl->page, ['req' => $req]);
    $this->pageCtrl->strictFilters = false; // Не используем жесткие фильтры
    if (isset($this->req->r['saveAndReturn'])) {
      $this->pageCtrl->completeRedirectType = 'fullpath';
    }
    else {
      $this->pageCtrl->completeRedirectType = 'self';
    }
    // Отключаем в настройках экшн по умолчанию, если его значение - 'blocks'
    if (isset($this->ctrl->page['settings']['defaultAction']) and
      $this->ctrl->page['settings']['defaultAction'] == 'blocks'
    ) $this->ctrl->page['settings']['defaultAction'] = '';
    $this->pageCtrl->setAdminMode(true);
  }

  function action_editContent() {
    // Разделы не имеющие контроллера не могут иметь редактирование содержания
    if (!$this->ctrl->page['controller']) {
      $this->redirect(Tt()->getPath(2).'/'.$this->ctrl->page['parentId']);
      return;
    }
    $this->pageCtrl->dispatch();
    //if ($this->pageCtrl->error404) return;
    // Если экшн без вывода меняем контроллер диспетчера на текущий сабконтролер раздела
    if (!$this->pageCtrl->hasOutput) {
      $this->ctrl->router->controller = $this->pageCtrl;
      return;
    }
    // Редирект для slave-раздела без фильтра на фильтр
    if (isset($this->pageCtrl->subControllers['ddSlave']) and
      $this->pageCtrl->action == 'list'
    ) {
      if ($this->pageCtrl->d['filters']['v'][0] != DdCore::masterFieldName) {
        $this->ctrl->redirect(Tt()->getPath(4).'/v.'.DdCore::masterFieldName.'.'.db()->firstId(DdCore::table(DdCore::getMasterStrName($this->ctrl->page['strName']))));
      }
    }
    $this->initPath();
    if ($this->pageCtrl->json) {
      print json_encode($this->pageCtrl->json);
      $this->hasOutput = false;
      return;
    }
    $this->ctrl->d['tpl'] = 'pages/editContent';
    $this->ctrl->d['pcd'] = $this->pageCtrl->d; // Page Controller Data
    if (isset($this->pageCtrl->d['items'])) {
      if (($paths = Hook::paths('dd/admin/extendItems')) !== false) foreach ($paths as $path) include $path;
      $this->initFilters();
      $this->ctrl->d['pagination'] = $this->ctrl->d['pcd']['pagination'];
    }
    $this->initDdo();
    if (isset($this->pageCtrl->strName)) {
      $oFields = new DdFields($this->pageCtrl->strName);
      $this->ctrl->d['pcd']['fields'] = $oFields->getFields();
    }
    if ($this->ctrl->d['pcd']['action'] == 'edit' or $this->ctrl->d['pcd']['action'] == 'new') $this->ctrl->d['pcd']['tpl'] = 'dd/form';
    $this->ctrl->setPageTitle(LANG_EDIT_SECTION_CONTENTS.' <b>«'.$this->pageCtrl->d['page']['title'].'»</b>');
  }

  protected function initDdo() {
    if (!isset($this->pageCtrl->d['items'])) return;
    $ddo = (new DdoAdminFactory($this->pageCtrl->page))->get()->setPagePath(Tt()->getPath(4));
    $this->ctrl->d['pcd']['grid'] = Ddo::getGrid($this->pageCtrl->d['items'], $ddo);
    $this->ctrl->d['pcd']['grid']['pagination'] = $this->pageCtrl->d['pagination'];
  }

  function action_json_getItems() {
    $this->pageCtrl->dispatch();
    $this->initDdo();
    $this->ctrl->json['body'] = $this->ctrl->d['pcd']['grid']['body'];
    $this->ctrl->json['pagination'] = $this->pageCtrl->d['pagination'];
  }

  protected function initPath() {
    if (!isset($this->pageCtrl)) throw new Exception('$this->pageCtrl not defined');
    if (!isset($this->pageCtrl->masterItem)) return;
    if (empty($this->pageCtrl->masterItem['title'])) throw new Exception("\$this->pageCtrl->masterItem['title'] is empty.");
    $this->ctrl->d['path'][count($this->d['path']) - 1] = [
      'title' => $this->pageCtrl->masterItem['title'],
      'name'  => 'page_edit',
      'link'  => Tt()->getPath()
    ];
  }

  protected function initFilters() {
    $filters = [];
    if (empty($this->pageCtrl->d['page']['strName'])) return;
    if (($paths = Hook::paths('dd/admin/beforeInitFilters')) !== false) foreach ($paths as $path) include $path;
    $filters = Arr::append($filters, DdGridFilters::getAll($this->pageCtrl->d['page']['strName']));
    if (($paths = Hook::paths('dd/admin/afterInitFilters')) !== false) foreach ($paths as $path) include $path;
    $this->ctrl->d['filtersForm'] = (new DdGridFilters($filters, $this->pageCtrl->page['strName']))->form->html();
  }

}