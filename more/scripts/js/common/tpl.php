<?php

// examples:
// /s2/js/common/tpl?name=tplName&controller=/path/to/controller/action
// /s2/js/common/tpl?name=tplName&path=/path/to/tpl

Misc::checkEmpty($_REQUEST, ['name', 'path']);
if (isset($_REQUEST['controller'])) {
  try {
  $html = file_get_contents('http://'.SITE_DOMAIN.'/'.ltrim($_REQUEST['controller'], '/'));
  } catch (Exception $e) {
    throw new Exception("controller not found by path '{$_REQUEST['controller']}'");
  }
} else {
  $html = Tt()->getTpl($_REQUEST['path']);
}
print "Ngn.tpls.{$_REQUEST['name']} = ".Arr::jsString($html).";";
