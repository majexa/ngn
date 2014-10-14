<?php

class CtrlAdminConfigManager extends CtrlAdmin {

  static $properties = [
    //'descr'  => 'Управление конфигурационными файлами',
    'onMenu' => true,
    'order'  => 80
  ];

  protected $defaultConstantName = 'more';
  protected $defaultVarName = 'admins';
  protected $defaultStructType = 'array';
  protected $configType;
  protected $configName;

  protected function init() {
    if (!isset($this->req->params[2])) $this->req->params[2] = 'constants';
    $this->d['configType'] = $this->configType = $this->req->params[2] == 'vvv' ? 'vvv' : 'constants';
    if ($this->configType == 'vvv') $this->configType = 'vars';
    if ($this->configType == 'vars') $this->d['configName'] = $this->configName = isset($this->req->params[3]) ? $this->req->params[3] : $this->defaultVarName;
    else
      $this->d['configName'] = $this->configName = isset($this->req->params[3]) ? $this->req->params[3] : $this->defaultConstantName;
    $this->d['sections'] = SiteConfig::getTitles($this->configType);
    $this->d['canUpdate'] = SiteConfig::hasSiteVar($this->configName);
  }

  function action_default() {
    Sflm::frontend('js')->addPath('i/js/ngn/Ngn.initConfigManager.js');
    $form = ConfigManagerFormFactory::get($this->configType, $this->configName);
    if ($form->update()) {
      $this->redirect();
      return;
    }
    $this->d['form'] = $form->html();
  }

  function action_ajax_deleteValue() {
    if ($this->configType == 'constants') throw new Exception('Deleting of constants not allowed');
    $form = ConfigManagerFormFactory::get($this->configType, $this->configName);
    $vars = Config::getVar($this->configName);
    $key = $this->req->r['name'];
    $key = substr($key, 1, strlen($key));
    eval('unset($vars'.$key.');');
    if ($form->getType() == 'array') $vars = array_values($vars);
    SiteConfig::updateVar($this->configName, $form->formatForUpdate($vars));
  }

  function action_deleteSiteConfig() {
    if ($this->configType == 'constants') throw new Exception('You can not delete constants sections');
    SiteConfig::deleteVarSection($this->configName);
    $this->redirect();
  }

}

CtrlAdminConfigManager::$properties['title'] = Lang::get('adminModuleConfigManager');
