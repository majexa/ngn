<?php

if (!($dbId = ProjectConfig::getConstant('site', 'LAST_DB_PATCH', true))) $dbId = 1510;
ProjectState::update("dbPatchLastIds", ['ngn' => $dbId]);
ProjectState::update("filePatchLastIds", ['ngn' => 10]);
ProjectConfig::deleteConstant('site', 'LAST_DB_PATCH');
ProjectConfig::deleteConstant('site', 'LAST_FILE_PATCH');