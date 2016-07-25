<?php

;
Misc::checkEmpty($_REQUEST['key'], '$_REQUEST[key]');
print 'Locale.define("'.LOCALE.'", "'.ucfirst($_REQUEST['key']).'", '.json_encode(Config::getVar('locale/'.LOCALE.'/'.$_REQUEST['key'])).");";
