<?php

if (!defined('WEBROOT_PATH')) throw new Exception('Please define WEBROOT_PATH');

// ngn init
define('NGN_ENV_PATH', dirname(dirname(__DIR__)));
define('NGN_PATH', dirname(__DIR__));

// web init
define('SITE_PATH', WEBROOT_PATH.'/site');
define('IS_DEBUG', true);

require_once NGN_PATH.'/init/core.php';
require_once NGN_PATH.'/init/web.php';

setConstant('SITE_LIB_PATH', SITE_PATH.'/lib');
Lib::addFolder(NGN_ENV_PATH.'/run/lib');
