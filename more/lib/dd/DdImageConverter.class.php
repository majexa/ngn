<?php

class DdImageConverter {

  static $smW;

  static $smH;

  static $mdW;

  static $mdH;

  /**
   * both/middle/small
   *
   * @var string
   */
  static $type = 'both';

  static function convert($strName, $pageId) {
    if (self::$type == 'small') {
      if (!self::$smW or !self::$smH) throw new Exception('self::$smW or self::$smH not defined');
    }
    elseif (self::$type == 'middle') {
      if (!self::$mdW or !self::$mdH) throw new Exception('self::$mdW or self::$mdH not defined');
    }
    else {
      if (!self::$smW or !self::$smH or !self::$mdW or !self::$mdH) throw new Exception('self::$smW or self::$smH or self::$mdW or self::$mdH not defined');
    }
    $oFields = O::get('DdFields', $strName);
    $im = new DdItemsManagerPage(new DdItemsPage($pageId), new DdFormPage($oFields, $pageId));
    if (!$imageFields_ = $oFields->getImageFields($strName)) return;
    foreach ($imageFields_ as $k => $v) {
      $imageFields[] = $v['name'];
    }
    $im->imageSizes['smW'] = self::$smW;
    $im->imageSizes['smH'] = self::$smH;
    $im->imageSizes['mdW'] = self::$mdW;
    $im->imageSizes['mdH'] = self::$mdH;
    $im->getNonActive = true;
    if (!$items = $im->items->getItems($pageId)) return;
    foreach ($items as $k => $v) {
      foreach ($v as $fieldName => $v2) {
        if (in_array($fieldName, $imageFields)) {
          $imagePath = $im->getFilePath($v['id'], $fieldName);
          if (self::$type == 'small') {
            $im->makeSmallThumbs(UPLOAD_PATH.'/'.$imagePath);
          }
          elseif (self::$type == 'middle') {
            $im->makeMiddleThumbs(UPLOAD_PATH.'/'.$imagePath);
          }
          else {
            $im->makeThumbs(UPLOAD_PATH.'/'.$imagePath);
          }
        }
      }
    }
  }

}