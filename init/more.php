<?php

// NGN core
require_once NGN_PATH.'/init/core.php';

// NGN more ----------
define('MORE_PATH', NGN_PATH . '/more');
Ngn::addBasePath(MORE_PATH, 1);

// Определение этой константы должно проходить в "project/site/config/constants/core"
if (!PROJECT_KEY) die('Constant PROJECT_KEY is empty');

// Для удачной инициализации NGN необходимо, что бы были определены следующие константы:
if (!is_dir(NGN_PATH)) die('Dir "'.NGN_PATH.'" not exists');

/*
if (!function_exists('imagecreate')) die('Extension "gd" is not loaded');
if (!function_exists('mb_strstr')) die('Extension "mbstring" is not loaded');
if (!function_exists('mysql_connect')) die('Extension "mysql" is not loaded');
if (!function_exists('finfo_file')) die('Extension "fileinfo" is not loaded');
*/

// Проверка версии PHP
list($ver) = explode('.', phpversion());

if ($ver < 5) die("Minimal PHP version for NGN is 5.0.4. Your version is ".phpversion());

// Проверка установки short_open_tag = On в php.ini
if (!ini_get('short_open_tag')) die("Change the value of php.ini short_open_tag = On");

// ------------------ more constants -----------------
if (file_exists(SITE_PATH.'/config/constants/more.php')) {
  // опциональное определение констант, что определяются в следующем require
  require SITE_PATH.'/config/constants/more.php';
}
require MORE_PATH.'/config/constants/default.php';
require MORE_PATH.'/config.php'; // what is it? name it
// ---------------------------------------------------
require_once MORE_PATH.'/lib/sflm/SflmBase.class.php';
require_once MORE_PATH.'/lib/sflm/SflmJs.class.php';
require_once MORE_PATH.'/lib/sflm/SflmCss.class.php';
require_once MORE_PATH.'/lib/sflm/SflmCache.class.php';
require_once MORE_PATH.'/lib/sflm/Sflm.class.php';
// ---------------------------------------------------

Sflm::$absBasePaths['m'] = WEBROOT_PATH.'/m';

if (!defined('SITE_PATH')) die('Error: SITE_PATH not defined'."\n".getBacktrace(false));
if (!is_writable(SITE_PATH.'/'.DATA_DIR.'/cache')) die('Error: "'.SITE_PATH.'/'.DATA_DIR.'/cache" is not writable'."\n".getBacktrace(false));

// Включаем кэширование списка классов
// Кэшировать нужно с помощью FileCache. Значит нужно его подключить
if (!defined('DATA_PATH')) define('DATA_PATH', SITE_PATH.'/'.DATA_DIR);

// Очитка кэша. Нельзя помещать в web-init, потому что web-init включается уже после
// включения кэширования библиотек
if (getConstant('IS_DEBUG') and isset($_REQUEST['cc'])) FileCache::clean();

// Переключаем загрузку классов на кэширующий метод
Lib::$isCache = true;

Err::noticeSwitch(true);

if (getConstant('IS_DEBUG') and isset($_REQUEST['cc'])) {
  require_once CORE_PATH.'/lib/Memc.class.php';
  require_once CORE_PATH.'/lib/Mem.class.php';
  require_once MORE_PATH.'/lib/core/UrlCache.class.php';
  FileCache::clean();
  Mem::clean();
  UrlCache::clearCache();
  Sflm::clearCache();
  die('cc');
}

require MORE_PATH.'/lib/common/date.func.php';
require MORE_PATH.'/lib/common/tpl.func.php';
