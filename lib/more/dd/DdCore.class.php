<?php

class DdCore {

  static function table($strName) {
    return 'dd_i_'.$strName;
  }

  static function tables() {
    return array_filter(db()->tables(), function($table) {
      return preg_match('/^dd_i_.*?/', $table);
    });
  }

  static function filesDir($strName) {
    return 'dd/'.$strName;
  }

  static function isStaticController($name) {
    return PageControllersCore::hasAncestor($name, 'ddStatic');
  }

  static function isDdController($name) {
    return PageControllersCore::hasAncestor($name, 'dd');
  }

  static function isItemsController($name) {
    return PageControllersCore::hasAncestor($name, 'ddItems');
  }

  /**
   * @return DdItemsManagerPage
   */
  static function getItemsManager($pageId, array $options = []) {
    throw new Exception('Depricated');
    /*
    if (($page = DbModelCore::get('pages', $pageId)) === false) {
      throw new Exception("No page by id=$pageId");
    }
    $fieldsOptions = isset($options['fieldsOptions']) ? $options['fieldsOptions'] : [];
    $fields = null;
    if (($paths = Hook::paths('dd/beforeInitItemsManager')) !== false) foreach ($paths as $path) include $path;
    if ($page['slave']) {
      $masterPageId = $page['parentId'];
      $masterStrName = DbModelCore::take('pages', $masterPageId)->r['strName'];
      $items = new DdSlaveItemsPage($page['id'], $masterStrName, $masterPageId);
      $form = new DdFormPageSlave(new DdFields($page['strName'], $fieldsOptions), $page['id'], $masterStrName, $masterPageId);
    }
    else {
      $items = new DdItemsPage($page['id']);
      $form = new DdFormPage(!empty($fields) ? $fields : O::gett('DdFields', $page['strName'], $fieldsOptions), $page['id']);
    }
    if (($paths = Hook::paths('dd/initItemsManager', $page['module'])) !== false) foreach ($paths as $path) include $path;
    $im = new DdItemsManagerPage($items, $form, $options);
    $im->defaultActive = (!empty($page['settings']['premoder'])) ? 0 : 1;
    if (isset($page['settings']['order']) and $page['settings']['order'] == 'oid') {
      $im->setOidAddMode(true);
    }
    if (DdCore::isStaticController($page['controller'])) {
      if (empty($options['staticId'])) $options['staticId'] = $page['id'];
      $im->setStaticId($options['staticId']);
    }
    return $im;
    */
  }

  const masterFieldName = 'mstr';

  static function getSlaveStrName($masterStrName) {
    return $masterStrName.'Slave';
  }

  static function getMasterStrName($slaveStrName) {
    return Misc::removeSuffix('Slave', $slaveStrName);
  }

  /**
   * @param array Массив с элементами формата:
   *              array(
   *                'pageId' => 111,
   *                'itemId' => 123,
   *              )
   * @return array
   */
  static function extendItemsData(array $items) {
    foreach ($items as &$v) $v = array_merge(O::get('DdItemsPage', $v['pageId'])->getItemF($v['itemId']), $v);
    return $items;
  }

  static function htmlItem($pageId, $layoutName, $id) {
    $ddo = new DdoPage(DbModelCore::get('pages', $pageId), $layoutName);
    $items = new DdItemsPage($pageId);
    $ddo->setItem($items->getItem($id));
    return $ddo->els();
  }

  static function htmlItems($pageId, $layoutName, array $items) {
    $ddo = new DdoPage(DbModelCore::get('pages', $pageId), $layoutName);
    $ddo->setItems($items);
    return $ddo->els();
  }

  static function isMasterController($controllerName) {
    return ClassCore::hasAncestor('CtrlPage'.ucfirst($controllerName), 'CtrlPageDdItemsMaster');
  }

  static $pathTranslation = [];

  static function adminMenuItem($module, $strName, $title, $class = false) {
    $req = Req::get();
    return [
      'link'  => Tt()->getPath(1)."/$module/$strName",
      'class' => $strName.($class ? ' '.$class : ''),
      'sel'   => (isset($req->params[2]) and $req->params[1] == $module and $req->params[2] == $strName),
      'title' => $title
    ];
  }

  /**
   * Возвращает ItemsManager с возможностью изменения недоступных полей и системных
   * @static
   * @param $strName
   * @return DdItemsManager
   */
  static function imSystem($strName) {
    $class = 'DdItemsManager'.ucfirst($strName);
    $class = class_exists($class) ? $class : 'DdItemsManager';
    return new $class(new DdItems($strName), new DdForm(new DdFields($strName, ['getDisallowed' => true]), $strName));
  }

  static function exportItems($strName, DbCond $cond) {
    $items = new DdItems($strName);
    $items->cond = $cond;
    $dumper = new DbDumper(null, ['noHeaders' => true]);
    $ids = $items->getItemIds();
    $dumper->cond->addF('id', $ids);
    $r = $dumper->getDump(self::table($strName));
    $dumper = new DbDumper(null, ['noHeaders' => true]);
    $dumper->cond->addF('itemId', $ids);
    $r .= $dumper->getDump('tagItems');
    return $r;
  }

}