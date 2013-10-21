<?php

if (!empty($_SERVER['argv'][0])) die('Usage: '.basename(__FILE__, '.php').' {folder}');
$folderName = $_SERVER['argv'][0];
$libPath = NGN_ENV_PATH."/$folderName/lib";
print `php ~/ngn-env/run/run.php "(new TestRunner)->local('$folderName')" NGN_ENV_PATH/$folderName/lib`;