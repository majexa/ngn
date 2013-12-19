<?php

// NGN core
require_once NGN_PATH.'/init/core.php';

Ngn::addBasePath(NGN_PATH.'/more', 1);

// Определение этой константы должно проходить в "project/site/config/constants/core"
if (PROJECT_KEY == '') die('Constant PROJECT_KEY is empty');

// Для удачной инициализации NGN необходимо, что бы были определены следующие константы:
if (!is_dir(NGN_PATH)) die('Dir "'.NGN_PATH.'" not exists');

if (!function_exists('imagecreate')) die('Extension "gd" is not loaded');
if (!function_exists('mb_strstr')) die('Extension "mbstring" is not loaded');
if (!function_exists('mysql_connect')) die('Extension "mysql" is not loaded');
if (!function_exists('finfo_file')) die('Extension "fileinfo" is not loaded');

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
require NGN_PATH.'/config/constants/more.php';
// ---------------------------------------------------
require LIB_PATH.'/more/config.php';
// ---------------------------------------------------

if (!is_writable(SITE_PATH.'/'.DATA_DIR.'/cache')) die('"'.SITE_PATH.'/'.DATA_DIR.'/cache" is not writable (init/more.php)');

// Включаем кэширование списка классов
// Кэшировать нужно с помощью NgnCache. Значит нужно его подключить
if (!defined('DATA_PATH')) define('DATA_PATH', SITE_PATH.'/'.DATA_DIR);

require_once LIB_PATH.'/core/NgnCache.class.php';
// Очитка кэша. Нельзя помещать в web-init, потому что web-init включается уже после 
// включения кэширования библиотек

if (getConstant('IS_DEBUG') and isset($_REQUEST['cc'])) NgnCache::clean();

// Переключаем загрузку классов на кэширующий метод
Lib::$isCache = true;

Err::noticeSwitch(true);

if (getConstant('IS_DEBUG') and isset($_REQUEST['cc'])) {
  require_once LIB_PATH.'/core/Memc.class.php';
  require_once LIB_PATH.'/core/Mem.class.php';
  require_once LIB_PATH.'/more/core/UrlCache.class.php';
  NgnCache::clean();
  Mem::clean();
  UrlCache::clearCache();
  require_once LIB_PATH.'/more/sflm/SflmBase.class.php';
  require_once LIB_PATH.'/more/sflm/SflmJs.class.php';
  require_once LIB_PATH.'/more/sflm/SflmCss.class.php';
  require_once LIB_PATH.'/more/sflm/Sflm.class.php';
  Sflm::clearCache();
  die('cc');
}

require LIB_PATH.'/more/common/date.func.php';
require LIB_PATH.'/more/common/tpl.func.php';