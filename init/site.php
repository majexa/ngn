<?php

require_once NGN_PATH.'/init/core.php';
if (file_exists(SITE_PATH.'/config/constants/site.php')) require SITE_PATH.'/config/constants/site.php';
if (file_exists(SITE_PATH.'/config/constants/database.php')) require SITE_PATH.'/config/constants/database.php';
require NGN_PATH.'/config/constants/site.php';
if (!defined('SITE_PATH') or !is_dir(SITE_PATH)) die2('Dir SITE_PATH not defined or "'.SITE_PATH.'"');
require NGN_PATH.'/config/version.php';
setConstant('SITE_LIB_PATH', SITE_PATH.'/lib');
Ngn::addBasePath(SITE_PATH, 5);