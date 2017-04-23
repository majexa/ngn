<?php

if (!file_exists(PROJECT_PATH.'/config/constants/core.php')) {
  die(__FILE__.': File "'.PROJECT_PATH.'/config/constants/core.php'.'" does not exists.'."\n");
}
require_once PROJECT_PATH.'/config/constants/core.php';
require_once NGN_PATH.'/init/site.php';
require_once NGN_PATH.'/init/more.php';
require_once NGN_PATH.'/init/cli.php';

Lib::requireIfExists(PROJECT_PATH.'/config/constants/database.php');

if (file_exists(NGN_ENV_PATH.'/config/database.php')) {
  require NGN_ENV_PATH.'/config/database.php';
  if (!defined('DB_NAME')) {
    throw new Exception('DB_NAME still ont defined after including '.NGN_ENV_PATH.'/config/database.php');
  }
  if (!defined('DB_USER')) {
    throw new Exception('DB_USER still ont defined after including '.NGN_ENV_PATH.'/config/database.php');
  }
}
