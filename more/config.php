<?php

require __DIR__.'/config_core.php';

define('TEMP_PATH', SITE_PATH.'/temp');

if ((!defined('SITE_DOMAIN') or !constant('SITE_DOMAIN')) and !empty($_SERVER['HTTP_HOST'])) define('SITE_DOMAIN', $_SERVER['HTTP_HOST']);

if (defined('SITE_DOMAIN')) define('SITE_WWW', 'http://'.SITE_DOMAIN);

if (!defined('LOGS_PATH')) {
  // Абсолютный путь к каталогу с логами
  define('LOGS_PATH', SITE_PATH.'/'.LOGS_DIR);
}

define('UPLOAD_PATH', WEBROOT_PATH.'/'.UPLOAD_DIR);

define('INLINE_IMAGES_DIR', 'ii');
define('INLINE_IMAGES_THUMB_DIR', 'ii_thmb');
define('INLINE_IMAGES_TEMP_DIR', 'ii_tmp');

define('PAGE_PATH_SEP', '.');

if (isset($_SERVER['REMOTE_ADDR']) and $_SERVER['REMOTE_ADDR'] == '127.0.0.1' and !defined('IS_DEBUG')) define('IS_DEBUG', true);

ini_set('magic_quotes_gpc', 0);

