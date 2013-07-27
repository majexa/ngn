<?php

/**
 * Места расположения баннеров
 */
define("ADS_PLACE_TOP", 12);
define("ADS_PLACE_UNDER_ADD_MSG", 13);
define("ADS_PLACE_RIGHT_LEFT", 14);
define("ADS_PLACE_RIGHT_LEFT_BIG", 15);

define('ADS_IMAGES_DIR', 'ads/');
define('ADS_IMAGES_PATH', UPLOAD_PATH.ADS_IMAGES_DIR);
define('ADS_IMAGES_LINK', '/'.UPLOAD_DIR.'ads/');

$_CONFIG['adsPlaces'] = [
  ADS_PLACE_RIGHT_LEFT => [
    'title' => 'LEFT 200x60',
    'image' => ADS_IMAGES_LINK.'208x60.gif',
    'cost' => 66,
    'w' => 200,
    'h' => 60,
  ],
  ADS_PLACE_RIGHT_LEFT_BIG => [
    'title' => 'LEFT 200x300',
    'image' => ADS_IMAGES_LINK.'208x60.gif',
    'cost' => 66,
    'w' => 200,
    'h' => 300,
  ],
  ADS_PLACE_UNDER_ADD_MSG => [
    'title' => 'UNDER MSGS 400x60',
    'image' => ADS_IMAGES_LINK.'400x60.gif',
    'cost' => 100,
    'w' => 400,
    'h' => 60,
  ],
  ADS_PLACE_TOP => [
    'title' => 'TOP 200x60',
    'image' => ADS_IMAGES_LINK.'208x60.gif',
    'cost' => 120,
    'w' => 200,
    'h' => 60,
  ],
];
