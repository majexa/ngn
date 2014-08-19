<?php

class DdoSettings {

  protected $strName;

  function __construct($strName) {
    Misc::checkString($strName);
    $this->strName = $strName;
  }

  function getLayouts() {
    $staticLayouts = Arr::append(Config::getVar('ddo/staticLayouts'), Config::getVar('ddo/layouts'));
    foreach ($staticLayouts as $v) $layouts[$v[0]]['title'] = $v[1];
    return $layouts;
  }

  protected function getKey($prefix, $suffix = null) {
    return "$prefix.{$this->strName}".($suffix ? '.'.$suffix : '');
  }

  protected function getVar($prefix, $suffix = null) {
    return Config::getVar($this->getKey($prefix, $suffix), true);
  }

  function getShowAll() {
    return $this->getVar('ddo/itemsShow');
  }

  function getShow($layoutName) {
    return $this->getDataLayout('itemsShow', $layoutName);
  }

  function getDataAll($name) {
    return $this->getVar("ddo/$name");
  }

  function getDataLayout($name, $layout) {
    if (($r = $this->getVar("ddo/$name")) === false) return false;
    return isset($r[$layout]) ? $r[$layout] : false;
  }


  /**
   * Возвращает все методы вывода для определенного поля
   *
   * @param   string  Имя поля
   * @return  array
   */
  function getOutputMethods($fieldType) {
    $methods = [
      [
        'name'  => '',
        'title' => 'по умолчанию'
      ]
    ];
    if (($_methods = DdoMethods::getInstance()->field[$fieldType])) {
      foreach ($_methods as $name => $v) {
        $methods[] = [
          'name'  => $name,
          'title' => $v['title']
        ];
      }
    }
    return $methods;
  }

  /**
   * Возвращает все методы вывода текущей структуры для всех лейаутов
   * Пример:
   * array(
   *   'layoutName' => array(
   *     'title' => 'notitle',
   *     'userId' => 'avatar'
   *   )
   * )
   *
   * @return array
   */
  function getOutputMethod() {
    return $this->getVar('ddo/outputMethod');
  }

  function getAllowedFields($layoutName) {
    if (($r = $this->getShow($layoutName)) === false) return [];
    return array_keys($r);
  }

  function getOrder($layoutName) {
    return $this->getVar('ddo/fieldOrder', $layoutName);
  }

  function updateShow($values) {
    SiteConfig::updateVar($this->getKey('ddo/itemsShow'), $values, true);
  }

  function updateOutputMethod($values) {
    SiteConfig::updateVar($this->getKey('ddo/outputMethod'), $values, true);
  }

  function updateTitled($values) {
    SiteConfig::updateVar($this->getKey('ddo/titled'), $values, true);
  }

  /**
   * @param   array   Пример:
   * array(
   *   'fieldName1' => 10,
   *   'fieldName1' => 20
   * )
   */
  function updateOrderIds($oids, $layoutName) {
    SiteConfig::updateVar($this->getKey('ddo/fieldOrder', $layoutName), $oids, true);
  }

}