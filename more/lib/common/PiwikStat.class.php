<?php

class PiwikStat {

  function __construct() {
    $this->conf = Config::getVar('piwik');
  }

  function enable() {
    $r = $this->api('SitesManager.getSitesIdFromSiteUrl', [
      'url' => 'http://'.SITE_DOMAIN
    ]);
    if ($r) return;
    $siteId = $this->api('SitesManager.addSite', [
      'siteName' => SITE_TITLE,
      'urls' => 'http://'.SITE_DOMAIN
    ]);
    Misc::checkEmpty($siteId);
    ProjectConfig::updateSubVar('stat', 'siteId', $siteId);
  }
  
  function disable() {}
  
  function api($method, array $params) {
    $params['module'] = 'API';
    $params['method'] = $method;
    $params['token_auth'] = $this->conf['authToken'];
    $params['format'] = 'PHP';
    return unserialize(file_get_contents($this->conf['url'].'?'.http_build_query($params)));
  }

}
