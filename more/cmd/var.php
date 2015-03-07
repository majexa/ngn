<?php

//print_r($_SERVER['argv']);
print json_encode(Config::getVar($_SERVER['argv'][2], true));