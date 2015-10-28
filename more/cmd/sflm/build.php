<?php

Sflm::$output = true;
foreach (Config::getVar('sflm/unicLinks') as $v) {
  // traveling all links
  output(SITE_DOMAIN.'/'.$v);
  O::di('RouterManager', [
    'routerOptions' => [
      'disableHeaders' => true
    ],
    'req' => new Req([
      'uri' => $v[0]
    ])
  ])
    ->router()
    ->dispatch()
    ->getOutput();
  // uglify
  $uglified = $file = UPLOAD_DIR.'/js/cache/'.Sflm::frontendName(true).'.js';
  //sys("uglifyjs $file --compress --mangle -o $uglified", true);
}

