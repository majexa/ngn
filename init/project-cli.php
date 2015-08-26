<?php

if (!file_exists(PROJECT_PATH.'/config/constants/core.php')) {
  die('File "'.PROJECT_PATH.'/config/constants/core.php'.'" does not exists.'."\n");
}
require_once PROJECT_PATH.'/config/constants/core.php';
require_once NGN_PATH.'/init/site.php';
require_once NGN_PATH.'/init/more.php';
require_once NGN_PATH.'/init/cli.php';
