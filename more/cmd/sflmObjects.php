<?php

Sflm::setFrontendName('default');
$existingObjectsFile = DATA_PATH.'/cache/zend_cache---'.'jsExistingObjectsdefault';
if (file_exists($existingObjectsFile)) {
  $existingObjectsModification = date('d.m.Y H:i:s', filemtime($existingObjectsFile));
  $existingObjects = Sflm::frontend('js')->classes->existingObjects;
} else {
  $existingObjects = [];
  $existingObjectsModification = '-';
}
$cacheFile = UPLOAD_PATH.'/js/cache/default.js';
if (file_exists($cacheFile)) {
  $cacheClassesDefinition = Sflm::frontend('js')->classes->parseNgnClassesDefinition(file_get_contents($cacheFile));
  $cacheModification = date('d.m.Y H:i:s', filemtime($cacheFile));
} else {
  $cacheClassesDefinition = [];
  $cacheModification = '-';
}
print Cli::columns([
  array_merge( //
    ["Existing objects ($existingObjectsModification)"], //
    $existingObjects //
  ),
  array_merge( //
    ["Cache objects ($cacheModification)"], //
    $cacheClassesDefinition //
  ),
], true);
