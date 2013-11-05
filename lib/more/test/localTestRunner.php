<?php

if (!empty($_SERVER['argv'][0])) die('Usage: '.basename(__FILE__, '.php')." {ngn-env root folder}\n");
$rootFolderName = $_SERVER['argv'][0];
$libPath = NGN_ENV_PATH."/$rootFolderName/lib";
print `php ~/ngn-env/run/run.php "(new TestRunner)->local('$libPath')" NGN_ENV_PATH/$rootFolderName/lib`;