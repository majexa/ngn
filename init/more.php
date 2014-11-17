<?php

// NGN core
require_once NGN_PATH.'/init/core.php';

// NGN more
define('MORE_PATH', NGN_PATH . '/more');

// Новый базовый путь в папке more
Ngn::addBasePath(MORE_PATH, 1);

// Для удачной инициализации NGN необходимо, что бы были определены следующие константы:
if (!is_dir(NGN_PATH)) die('Dir "'.NGN_PATH.'" not exists'."\n".getBacktrace(false));

if (!defined('PROJECT_PATH')) die('Error: PROJECT_PATH not defined'."\n".getBacktrace(false));
if (file_exists(PROJECT_PATH.'/config/constants/more.php')) {
  // опциональное определение констант, что определяются в следующем require
  require PROJECT_PATH.'/config/constants/more.php';
}

if (!is_writable(PROJECT_PATH.'/'.DATA_DIR.'/cache')) die('Error: "'.PROJECT_PATH.'/'.DATA_DIR.'/cache" is not writable'."\n".getBacktrace(false));

if (!defined('DATA_PATH')) define('DATA_PATH', PROJECT_PATH.'/'.DATA_DIR);
if ((!defined('SITE_DOMAIN') or !constant('SITE_DOMAIN')) and !empty($_SERVER['HTTP_HOST'])) define('SITE_DOMAIN', $_SERVER['HTTP_HOST']);

define('SITE_WWW', 'http://'.SITE_DOMAIN);

if (!defined('LOGS_PATH')) {
  // Абсолютный путь к каталогу с логами
  define('LOGS_PATH', PROJECT_PATH.'/'.LOGS_DIR);
}

define('UPLOAD_PATH', WEBROOT_PATH.'/'.UPLOAD_DIR);

// Очитка кэша. Нельзя помещать в web-init, потому что web-init включается уже после
// включения кэширования библиотек
if (getConstant('IS_DEBUG') and isset($_REQUEST['cc'])) FileCache::clean();

Err::noticeSwitch(true);