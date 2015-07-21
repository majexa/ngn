<?php

// На веб-страницах не нужно выводить лог, только если не определен параметр Output Log ('ol')
define('LOG_OUTPUT', isset($_REQUEST['ol']) ? true : false);

require_once NGN_PATH.'/init/more.php';

if (getConstant('IS_DEBUG') and isset($_REQUEST['cc'])) {
  require_once MORE_PATH.'/cmd/cc.php';
  die('cc');
}

if (!is_file(WEBROOT_PATH.'/index.php')) die2('Dir "'.WEBROOT_PATH.'" or "index.php" not exists');

R::set('processTimeStart', getMicrotime());
if (isset($_REQUEST['plain'])) R::set('plainText', true);

Lib::enableCache();
