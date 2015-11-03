<?php

Sflm::$output = false;
if (!($links = Config::getVar('sflm/unicLinks', true))) return;
foreach ($links as $link) {
  // traveling all links
  output(SITE_DOMAIN.'/'.$link);
  O::di('RouterManager', [
    'routerOptions' => [
      'disableHeaders' => true
    ],
    'req' => new Req([
      'uri' => $link[0]
    ])
  ])
    ->router()
    ->dispatch()
    ->getOutput();
}
