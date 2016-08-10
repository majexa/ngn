<?php

class AdminModule {

  static $forseListAllow = false;

  static function getModules() {
    $modules = [];
    $order = [];
    $hideAdminModules = Config::getVarVar('adminPriv', 'hideAdminModules');
    $adminClassMap = Config::getVar('adminClassMap', true) ?: [];
    foreach (ClassCore::getClassesByPrefix('CtrlAdmin') as $k => $class) {
      if (($prop = $class::getProperties()) === false) continue;
      $name = ClassCore::classToName('CtrlAdmin', $class);
      if (isset($adminClassMap[$name])) $name = $adminClassMap[$name];
      $prop['name'] = $name;
      if (in_array($prop['name'], $hideAdminModules)) $prop['onMenu'] = false;
      $order[$k] = isset($prop['order']) ? $prop['order'] : 100;
      $modules[$k] = $prop;
    }
    array_multisort($order, SORT_ASC, $modules);
    return $modules;
  }

  static function getListModules() {
    return Arr::filterFunc(self::getModules(), function($v) {
      return AdminModule::isListAllowed($v);
    });
  }

  static function isAllowed($moduleName) {
    if (Misc::isGod()) return true;
    return self::_isAllowed($moduleName);
  }

  static function isListAllowed(array $module) {
    if (!empty($module['alwaysOnMenu'])) return true;
    if (empty($module['onMenu'])) return false;
    if (O::get('Req')->params[0] == 'god') return true;
    return self::_isAllowed($module['name']);
  }

  static protected function _isAllowed($moduleName) {
    // Модуль 'default' по умолчанию разрешен
    if ($moduleName == 'default') return true;
    $flippedClassMap = array_flip(Config::getVar('adminClassMap', true) ?: []);
    if (isset($flippedClassMap[$moduleName])) $moduleName = $flippedClassMap[$moduleName];
    return in_array($moduleName, self::getAllowedModules());
  }

  static function getAllowedModules() {
    return Config::getVarVar('adminPriv', 'allowedAdminModules', true);
  }

  static function getProperties($name) {
    $class = 'CtrlAdmin'.ucfirst($name);
    return $class::getProperties();
  }

  static function getProperty($name, $property) {
    if (($properties = self::getProperties($name)) === false) return false;
    return isset($properties[$property]) ? $properties[$property] : false;
  }

  static function sf($name) {
    $s = '';
    if (file_exists(STATIC_PATH.'/js/ngn/admin/'.$name.'.js')) $s .= Sflm::frontend('js')->getTag(STATIC_DIR.'/js/ngn/admin/'.$name.'.js');
    if (file_exists(STATIC_PATH.'/js/ngn/admin/'.$name.'.css')) $s .= Sflm::frontend('css')->getTag(STATIC_DIR.'/js/ngn/admin/'.$name.'.css');
    return $s;
  }

}