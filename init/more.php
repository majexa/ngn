<?php

// NGN core
require_once NGN_PATH.'/init/core.php';

// NGN more
define('MORE_PATH', NGN_PATH . '/more');

// Новый базовый путь в папке more
Ngn::addBasePath(MORE_PATH, 1);

if (!defined('PROJECT_PATH')) die('Error: PROJECT_PATH not defined'."\n".getBacktrace(false));

Lib::requireIfExists(PROJECT_PATH.'/config/constants/more.php');

if (!is_writable(PROJECT_PATH.'/'.DATA_DIR.'/cache')) die('Error: "'.PROJECT_PATH.'/'.DATA_DIR.'/cache" is not writable'."\n".getBacktrace(false));

if (!defined('DATA_PATH')) define('DATA_PATH', PROJECT_PATH.'/'.DATA_DIR);
if (!defined('TEMP_PATH')) define('TEMP_PATH', PROJECT_PATH.'/temp');
if ((!defined('SITE_DOMAIN') or !constant('SITE_DOMAIN')) and !empty($_SERVER['HTTP_HOST'])) define('SITE_DOMAIN', $_SERVER['HTTP_HOST']);

define('SITE_WWW', 'http://'.SITE_DOMAIN);

if (!defined('LOGS_PATH')) {
  // Абсолютный путь к каталогу с логами
  define('LOGS_PATH', PROJECT_PATH.'/'.LOGS_DIR);
}

define('UPLOAD_PATH', WEBROOT_PATH.'/'.UPLOAD_DIR);

// sflm init

require_once MORE_PATH.'/lib/sflm/SflmBase.class.php';
require_once MORE_PATH.'/lib/sflm/SflmJs.class.php';
require_once MORE_PATH.'/lib/sflm/SflmCss.class.php';
require_once MORE_PATH.'/lib/sflm/SflmCache.class.php';
require_once MORE_PATH.'/lib/sflm/Sflm.class.php';

Sflm::$absBasePaths['m'] = WEBROOT_PATH.'/m';

// @todo why not used standard php method
Err::noticeSwitch(true);

define('MORE_ENABLED', true);