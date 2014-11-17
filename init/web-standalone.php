<?php

if (!defined('WEBROOT_PATH')) throw new Exception('Please define WEBROOT_PATH');

// ngn init
define('NGN_ENV_PATH', dirname(dirname(__DIR__)));
define('NGN_PATH', dirname(__DIR__));

// web init
define('PROJECT_PATH', WEBROOT_PATH.'/site');
define('IS_DEBUG', true);
define('LOGS_PATH', PROJECT_PATH.'/logs');

require_once NGN_PATH.'/init/core.php';
require_once NGN_PATH.'/init/web.php';

Ngn::addBasePath(PROJECT_PATH, 5);

