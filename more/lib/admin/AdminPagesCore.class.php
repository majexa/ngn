<?php

class AdminPagesCore {

  static function getEditContentSubController($controller) {
    $classes = array_map(
      function($v) {
        return 'SubPaAdminPages'.ucfirst($v);
      },
      ClassCore::getAncestorNames(ClassCore::nameToClass('CtrlPage', $controller), 'CtrlPage')
    );
    foreach ($classes as $class) if (class_exists($class)) return $class;
    return false;
  }

}