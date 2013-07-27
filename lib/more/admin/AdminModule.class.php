<?php

class AdminModule {

  static $forseListAllow = false;

  static function getModules() {
    $modules = [];
    $order = [];
    $hideAdminModules = Config::getVarVar('adminPriv', 'hideAdminModules');
    foreach (ClassCore::getClassesByPrefix('CtrlAdmin') as $k => $class) {
      if (($prop = $class::getProperties()) === false) continue;
      $prop['name'] = ClassCore::classToName('CtrlAdmin', $class);
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

  static function isAllowed($module) {
    if (Misc::isGod()) return true;
    return self::_isAllowed($module);
  }

  static function isListAllowed($v) {
    if (!empty($v['alwaysOnMenu'])) {
      //die2($v);
      return true;
    }
    if (empty($v['onMenu'])) return false;
    if (O::get('Req')->params[0] == 'god' and Misc::isGod()) return true;
    return self::_isAllowed($v);
  }

  static function _isAllowed($module) {
    // Модуль 'default' по умолчанию разрешен
    if ($module == 'default') return true;
    return in_array($module, self::getAllowedModules());
  }

  static protected $allowedAdminModules;

  static function getAllowedModules() {
    if (isset(self::$allowedAdminModules)) return self::$allowedAdminModules;
    self::$allowedAdminModules = Config::getVarVar('adminPriv', 'allowedAdminModules', true);
    return self::$allowedAdminModules;
  }

  static function getProperties($name) {
    if (file_exists(LIB_PATH.'/more/admin/'.$name.'/properties.php')) {
      $file = LIB_PATH.'/more/admin/'.$name.'/properties.php';
    }
    elseif (file_exists(SITE_LIB_PATH.'/more/admin/'.$name.'/properties.php')) {
      $file = SITE_LIB_PATH.'/more/admin/'.$name.'/properties.php';
    }
    else {
      return false;
    }
    $props = include $file;
    if (defined('LANG_ADMIN_MODULE_'.$name)) {
      $props['title'] = constant('LANG_ADMIN_MODULE_'.$name);
    }
    return $props;
  }

  static function getProperty($name, $property) {
    $properties = self::getProperties($name);
    return $properties[$property];
  }

  static function sf($name) {
    $s = '';
    if (file_exists(STATIC_PATH.'/js/ngn/admin/'.$name.'.js')) $s .= Sflm::get('js')->getTag(STATIC_DIR.'/js/ngn/admin/'.$name.'.js');
    if (file_exists(STATIC_PATH.'/js/ngn/admin/'.$name.'.css')) $s .= Sflm::get('css')->getTag(STATIC_DIR.'/js/ngn/admin/'.$name.'.css');
    return $s;
  }

}