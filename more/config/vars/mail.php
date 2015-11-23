<?php
return [
  'method' => 'mail',
  'fromEmail' => 'noreply@'.(defined('SITE_DOMAIN') ? SITE_DOMAIN : 'dummy'),
  'fromName' => defined('SITE_DOMAIN') ? SITE_DOMAIN : 'Dummy',
];
