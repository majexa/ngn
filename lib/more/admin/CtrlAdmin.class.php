<?php

/**
 * use config var "adminTopLinks" to extend admin menu.
 * format:
 * [
 * 'link'  => $this->tt->getPath(1).'/asd',
 * 'class' => 'asd',
 * 'title' => 'Asd'
 * ]
 *
 */
abstract class CtrlAdmin extends CtrlCp {

  static function getProperties() {
    if (!isset(static::$properties)) return false;
    return static::$properties;
  }

  public $userId;

  public $settings;

  public $god;

  /**
   * @var PrivMsgs
   */
  public $pribMsgs;

  protected $theme;

  /**
   * Определяет формаировать ли имена шаблонов для экшенов с формой
   *
   * @var bool
   */
  protected $prepareMainFormTpl = false;

  protected function getParamActionN() {
    return 2;
  }

  /**
   * beforeInit() используется пока что только в контроллерах админки,
   * т.к. init() определяется в дочерних контроллерах без вызова родительского init()
   * Поэтому внедрение дополнительного метода beforeInit() будет гораздо дешевле
   * в плане переписывания каждого из методов init() классов семейства CtrlAdmin...
   */
  protected function beforeInit() {
    parent::beforeInit();
    $this->d['name'] = 'admin';
    if (!isset($this->d['tpl'])) $this->d['tpl'] = 'default';
    $this->initPrivMsgs();
    $this->initModules();
    $this->initTopLinks();
    $this->setModuleTitle($this->d['adminModuleTitle']);
    $this->d['oController'] = 1;
    $this->d['msg'] = Auth::get('msg');
    $this->d['god'] = $this->god = $this->req->params[0] == 'god' ? true : false;
    if (($paths = Hook::paths('admin/moduleInit/'.$this->getName())) !== false) foreach ($paths as $path) include $path;
  }

  protected function prepareMainFormTpl() {
    if (!$this->prepareMainFormTpl) return;
    if (!in_array($this->action, ['new', 'edit'])) return;
    $this->d['headerTpl'] = 'admin/modules/'.$this->d['adminModule'].'/header';
    $this->d['tpl'] = 'admin/common/form';
  }

  protected function prepareTplPath() {
    if ($this->tt->exists('admin/modules/'.$this->d['adminModule'].'/'.$this->action)) {
      $this->d['tpl'] = 'admin/modules/'.$this->d['adminModule'].'/'.$this->action;
    }
    elseif ($this->tt->exists('admin/modules/'.$this->d['tpl'])) {
      $this->d['tpl'] = 'admin/modules/'.$this->d['tpl'];
    }
    $this->prepareMainFormTpl();
  }

  protected function initMainTpl() {
    if (file_exists(NGN_ENV_PATH.'/config/server.php')) {
      $server = require NGN_ENV_PATH.'/config/server.php';
      if ($server['sType'] == 'test' and $this->req['forceAuth']) {
        parent::initMainTpl();
        return;
      }
    }
    if (!($this->userId = Auth::get('id')) or (!Misc::isAdmin() and !Misc::isGod())) {
      $this->actionDisabled = true;
      $this->d['mainTpl'] = 'admin/auth';
    }
    else {
      parent::initMainTpl();
    }
  }

  protected function initPrivMsgs() {
    if (!AdminModule::isAllowed('privMsgs')) return;
    $this->pribMsgs = new PrivMsgs($this->userId);
    $this->d['newMsgsCount'] = $this->pribMsgs->getNewMsgsCount();
  }

  protected function afterInit() {
    $this->savePath();
  }

  protected function initModules() {
    $this->d['adminModules'] = Arr::toAssoc(AdminModule::getListModules(true), 'name');
    // Добавляем циферку новых сообщений к ссылке на приватки
    if (isset($this->d['adminModules']['privMsgs']) and !empty($this->d['newMsgsCount'])) {
      $this->d['adminModules']['privMsgs']['title'] .= ' (<b>'.$this->d['newMsgsCount'].'</b>)';
    }
    $this->d['adminModuleTitle'] = AdminModule::getProperty($this->getName(), 'title');
    $this->d['adminModule'] = $this->getName();
    if (!$this->d['adminModuleTitle']) $this->d['adminModuleTitle'] = LANG_HOME;
  }

  protected function initTopLinks() {
    if (($r = Config::getVar('adminTopLinks', true)) !== false) $links = $r;
    else $links = [];
    if ($this->d['adminModules']) {
      foreach ($this->d['adminModules'] as $v) {
        $links[] = [
          'link'  => $this->tt->getPath(1).'/'.$v['name'],
          'class' => isset($v['class']) ? $v['class'] : $v['name'],
          'sel'   => $v['name'] == $this->d['adminModule'],
          'title' => $v['title']
        ];
      }
    }
    $links[] = [
      'link'   => $this->tt->getPathRoot(),
      'class'  => 'site',
      'title'  => LANG_SITE,
      'target' => '_blank'
    ];
    $links[] = [
      'link'  => $this->tt->getPath().'?logout=1',
      'class' => 'logout',
      'title' => LANG_EXIT
    ];
    $this->setTopLinks($links);
  }

  protected function savePath() {
    Settings::set('admin_last_path', $_SERVER['REQUEST_URI']);
  }

  protected function getLastPath() {
    return Settings::get('admin_last_path');
  }

  protected function getSettings() {
    return Settings::get('admin_'.$this->getName());
  }

  protected function setSettings($settings) {
    return Settings::set('admin_'.$this->getName(), $settings);
  }

  protected function action_setSettings() {
    $this->setSettings($this->req->r['settings']);
    $this->redirect();
  }

  function action_default() {
  }

  protected function getName() {
    return ClassCore::classToName('CtrlAdmin', get_class($this));
  }

  protected function extendTplData() {
    parent::extendTplData();
    foreach (Hook::paths('admin/common') as $path) include $path;
    $this->extendMainContentCssClass('am_'.$this->d['adminModule']);
  }

  function action_success() {
    $this->d['tpl'] = 'admin/common/success';
  }

  protected function addSimilarHeaderLinks(array $options, $class) {
    if (!isset($this->d['headerLinks'])) $this->d['headerLinks'] = [];
    foreach ($options as $name => $title) {
      $this->d['headerLinks'][] = [
        'title' => $title,
        'link'  => $this->tt->getPath(2).'/'.$name,
        'class' => $class
      ];
    }
  }

}
