<?php

// ngn init
define('NGN_ENV_PATH', str_replace('\\', '/', dirname(dirname(__DIR__))));
define('NGN_PATH', dirname(__DIR__));

// web init
define('IS_DEBUG', true);

require_once NGN_PATH.'/init/more.php';
require_once NGN_PATH.'/init/cli.php';

setConstant('SITE_LIB_PATH', PROJECT_PATH.'/lib');
Lib::addFolder(NGN_ENV_PATH.'/run/lib');
Lib::enableCache();
