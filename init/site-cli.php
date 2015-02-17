<?php

if (!file_exists(PROJECT_PATH.'/config/constants/core.php')) {
  LogWriter::v('fff', '!!!');
//  throw new Exception('Ngn Project structure does not esists');
}
require_once PROJECT_PATH.'/config/constants/core.php';
require_once NGN_PATH.'/init/site.php';
require_once NGN_PATH.'/init/more.php';
require_once NGN_PATH.'/init/cli.php';
Lib::enableCache();
