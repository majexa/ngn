<?php

require_once NGN_PATH.'/init/core.php';
require SITE_PATH.'/config/constants/site.php';
require NGN_PATH.'/config/constants/site.php';
setConstant('NGN_ENV_PATH', dirname(NGN_PATH));
if (!defined('SITE_PATH') or !is_dir(SITE_PATH) or !is_dir(SITE_PATH.'/config')) die2('Dir SITE_PATH not defined or "'.SITE_PATH.'" or "SITE_PATH/config" not exists');
require NGN_PATH.'/config/version.php';
setConstant('SITE_LIB_PATH', SITE_PATH.'/lib');
Ngn::addBasePath(NGN_PATH.'/site', 2);