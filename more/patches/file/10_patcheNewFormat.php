<?php

$dbId = SiteConfig::getConstant('site', 'LAST_DB_PATCH');
$fileId = SiteConfig::getConstant('site', 'LAST_FILE_PATCH');
SiteConfig::updateVar("dbPatchLastIds", ['ngn' => $dbId]);
SiteConfig::updateVar("filePatchLastIds", ['ngn' => 10]);
SiteConfig::deleteConstant('site', 'LAST_DB_PATCH');
SiteConfig::deleteConstant('site', 'LAST_FILE_PATCH');