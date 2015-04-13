<?php

require_once NGN_PATH.'/init/core.php';
Lib::requireIfExists(PROJECT_PATH.'/config/constants/site.php');
Lib::requireIfExists(PROJECT_PATH.'/config/constants/database.php');
setConstant('SITE_LIB_PATH', PROJECT_PATH.'/lib');
Ngn::addBasePath(PROJECT_PATH, 5);