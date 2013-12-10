<?php

if (!($dbId = SiteConfig::getConstant('site', 'LAST_DB_PATCH', true))) $dbId = 1540;
SiteConfig::updateVar("dbPatchLastIds", ['ngn' => $dbId]);
SiteConfig::updateVar("filePatchLastIds", ['ngn' => 10]);
SiteConfig::deleteConstant('site', 'LAST_DB_PATCH');
SiteConfig::deleteConstant('site', 'LAST_FILE_PATCH');