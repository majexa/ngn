<?php

require_once NGN_PATH.'/init/core.php';
if (file_exists(PROJECT_PATH.'/config/constants/site.php')) require PROJECT_PATH.'/config/constants/site.php';
if (file_exists(PROJECT_PATH.'/config/constants/database.php')) require PROJECT_PATH.'/config/constants/database.php';
if (!defined('PROJECT_PATH') or !is_dir(PROJECT_PATH)) die2('Dir PROJECT_PATH not defined or "'.PROJECT_PATH.'"');
setConstant('SITE_LIB_PATH', PROJECT_PATH.'/lib');
Ngn::addBasePath(PROJECT_PATH, 5);