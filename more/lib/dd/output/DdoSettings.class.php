<?php

class DdoSettings {

  protected $strName;

  function __construct($strName) {
    Misc::checkString($strName);
    $this->strName = $strName;
  }

  function getLayouts() {
    $staticLayouts = Arr::append(Config::getVar('ddo/staticLayouts'), Config::getVar('ddo/layouts'));
    $layouts = [];
    foreach ($staticLayouts as $v) $layouts[$v[0]]['title'] = $v[1];
    return $layouts;
  }

  protected function getKey($prefix, $suffix = null) {
    return "ddo/$prefix.{$this->strName}".($suffix ? '.'.$suffix : '');
  }

  function getVar($prefix, $suffix = null) {
    return Config::getVar($this->getKey($prefix, $suffix), true);
  }

  function getShowAll() {
    return $this->getVar('itemsShow');
  }

  function getShow($layoutName) {
    return $this->getLayoutSettings('itemsShow', $layoutName);
  }

  function getDataAll($name) {
    return $this->getVar($name);
  }

  /**
   * Возвращает массив конфигурации anyNgnBasePath/config/ddo/name.strName.php
   *
   * @param $name
   * @param $layout
   * @return bool
   */
  function getLayoutSettings($name, $layout) {
    if (($r = $this->getVar($name)) === false) return false;
    return isset($r[$layout]) ? $r[$layout] : false;
  }

  /**
   * Возвращает все методы вывода для определенного поля
   *
   * @param string $fieldName Имя поля
   * @return array
   */
  function getOutputMethods($fieldName) {
    $methods = [
      [
        'name'  => '',
        'title' => 'по умолчанию'
      ]
    ];
    if (($_methods = DdoMethods::getInstance()->field[$fieldName])) {
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
    return $this->getVar('outputMethod');
  }

  function getAllowedFields($layoutName) {
    if (($r = $this->getShow($layoutName)) === false) return [];
    return array_keys($r);
  }

  /**
   * Конфиг находится тут: ddo/fieldOrder.strName.layoutName
   *
   * @param $layoutName
   * @return array|bool|mixed
   */
  function getOrder($layoutName) {
    return $this->getVar('fieldOrder', $layoutName);
  }

  function updateShow($values) {
    ProjectConfig::updateVar($this->getKey('itemsShow'), $values, true);
  }

  function updateOutputMethod($values) {
    ProjectConfig::updateVar($this->getKey('outputMethod'), $values, true);
  }

  function updateTitled($values) {
    ProjectConfig::updateVar($this->getKey('titled'), $values, true);
  }

  /**
   * @param   array   Пример:
   * array(
   *   'fieldName1' => 10,
   *   'fieldName1' => 20
   * )
   */
  function updateOrderIds($oids, $layoutName) {
    ProjectConfig::updateVar($this->getKey('ddo/fieldOrder', $layoutName), $oids, true);
  }

}