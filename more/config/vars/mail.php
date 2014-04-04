<?php
return [
  'method' => 'mail',
  'fromEmail' => 'noreplay@'.(defined('SITE_DOMAIN') ? SITE_DOMAIN : 'dummy'),
  'fromName' => defined('SITE_DOMAIN') ? SITE_DOMAIN : 'Dummy',
];
