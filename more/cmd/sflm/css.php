<?php

$frontend = isset($_SERVER['argv'][2]) ? $_SERVER['argv'][2] : 'default';
Sflm::setFrontendName($frontend);
$cacheFile = UPLOAD_PATH.'/css/cache/'.$frontend.'.css';
$cachePaths = [];
if (file_exists($cacheFile)) {
  $cacheModification = date('d.m.Y H:i:s', filemtime($cacheFile));
  if (preg_match_all('/\/\*--\|([^|]*)\|--\*\//s', file_get_contents($cacheFile), $m)) $cachePaths = $m[1];
} else {
  $cacheModification = '-';
}
print "Cache paths ($cacheModification):\n".implode("\n", $cachePaths)."\n";
