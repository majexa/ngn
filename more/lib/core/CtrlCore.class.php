<?php

class CtrlCore {

  static function ctrl($name, Router $router) {
    return O::get(ClassCore::nameToClass('Ctrl', $name), $router)->setPage(self::getVirtualCtrlPageModel($controller));
  }


}