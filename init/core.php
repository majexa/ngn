<?php

if (!defined('NGN_PATH')) define('NGN_PATH', dirname(__DIR__));
define('CORE_PATH', NGN_PATH . '/core');                  // @LibStorageRemove
require_once CORE_PATH.'/lib/common.func.php';

setConstant('VENDORS_PATH', NGN_PATH.'/vendors'); // @LibStorageRemove

define('CHARSET', 'UTF-8');
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', 'utf8_general_ci');

require_once CORE_PATH.'/lib/R.class.php'; // Registry Class
require_once CORE_PATH.'/lib/log/LogWriter.class.php';
require_once CORE_PATH.'/lib/Err.class.php';
require_once CORE_PATH.'/lib/misc.func.php';
require_once CORE_PATH.'/lib/Arr.class.php'; // Array processing functions
require_once CORE_PATH.'/lib/file/Dir.class.php'; // Directory processing functions
require_once CORE_PATH.'/lib/file/File.class.php'; // File processing functions
require_once CORE_PATH.'/lib/file/FileCache.class.php';
require_once CORE_PATH.'/lib/Lib.class.php'; // Librarys, classes
require_once CORE_PATH.'/lib/exceptions/NoFileException.class.php';
require_once CORE_PATH.'/lib/O.class.php';
require_once CORE_PATH.'/lib/Ngn.class.php';
require_once CORE_PATH.'/lib/Misc.class.php'; // Miscellaneous functions
require_once CORE_PATH.'/lib/cli/CliColors.class.php'; // Для отладки

date_default_timezone_set('Europe/Moscow');

// Важно! До установки Lib::$isCache = true никаких обращений к классам
// без предварительного подключения быть не должно
spl_autoload_register(['Lib', 'required']);         // @LibStorageRemove

Err::$show = true;

if (!defined('VENDORS_PATH')) die('VENDORS_PATH not defined (core/init)'); // @LibStorageRemove
if (!file_exists(VENDORS_PATH)) die('Folder "'.VENDORS_PATH.'" does not exists (core/init)'); // @LibStorageRemove

// Здесь ищем сторонние библиотеки
//define('INCL_PATH_DELIMITER', getOS() == 'win' ? ';' : ':');
set_include_path(VENDORS_PATH.PATH_SEPARATOR.get_include_path());  // @LibStorageRemove

set_exception_handler(['Err', 'exceptionHandler']);
set_error_handler(['Err', 'errorHandler']);
register_shutdown_function(['Err', 'shutdownHandler']);

// ------------------- config ------------------

/**
 * Каталог с логами
 */
define('LOGS_DIR', 'logs');

/**
 * Каталог с данными
 */
define('DATA_DIR', 'data');

/**
 * Каталог для загружаеммых на сервер файлов
 */
define('UPLOAD_DIR', 'u');

// ------------------ ngn-env -----------------

setConstant('NGN_ENV_PATH', dirname(NGN_PATH));

// ---------------- core constants -----------------

require CORE_PATH.'/config/constants/default.php';

// Новый базовый путь в папке "core"
Ngn::addBasePath(NGN_PATH.'/core');