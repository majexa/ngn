<?php

$cmd = 'ping ya.ru';
$process = proc_open($cmd, [['pipe', 'r'],['pipe', 'w'],['pipe', 'w']], $pipes, realpath('./'), []);
if (is_resource($process)) while ($s = fgets($pipes[1])) print $s;