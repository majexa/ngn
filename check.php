<?php

if (!function_exists('imagecreate')) die('Extension "gd" is not loaded');
if (!function_exists('mb_strstr')) die('Extension "mbstring" is not loaded');
if (!function_exists('mysql_connect')) die('Extension "mysql" is not loaded');
if (!function_exists('finfo_file')) die('Extension "fileinfo" is not loaded');

// Проверка версии PHP
list($ver) = explode('.', phpversion());

if ($ver < 5) die("Minimal PHP version for NGN is 5.0.4. Your version is ".phpversion());

// Проверка установки short_open_tag = On в php.ini
if (!ini_get('short_open_tag')) die("Change the value of php.ini short_open_tag = On");

