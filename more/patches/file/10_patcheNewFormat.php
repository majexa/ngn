<?php

if (!($dbId = SiteConfig::getConstant('site', 'LAST_DB_PATCH', true))) $dbId = 1510;
ProjectState::update("dbPatchLastIds", ['ngn' => $dbId]);
ProjectState::update("filePatchLastIds", ['ngn' => 10]);
SiteConfig::deleteConstant('site', 'LAST_DB_PATCH');
SiteConfig::deleteConstant('site', 'LAST_FILE_PATCH');