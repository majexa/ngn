<?php

class SiteRequest {

  /**
   * @var Req
   */
  protected $req;

  function __construct(Req $req = null) {
    $this->req = $req ? $req : O::get('Req');
  }

  function getAbsBase() {
    if (($subdomain = $this->getSubdomain()) !== false) return '//'.$subdomain.'.'.SITE_DOMAIN;
    else return $this->getAbsSiteBase();
  }

  function getAbsSiteBase() {
    return '//'.SITE_DOMAIN.$this->req->getBase();
  }

  protected $subdomain;

  function getSubdomain() {
    return false;
    if (isset($this->subdomain)) return $this->subdomain;
    $domainParts = explode('.', $_SERVER['HTTP_HOST']);
    $baseDomainLevel = Misc::siteDomainLevel();
    $curLevel = count($domainParts);
    if ($curLevel == $baseDomainLevel) return false;
    if ($curLevel > $baseDomainLevel + 1) throw new Exception('Number of domain parts is incorrect ('.$curLevel.'). Must be '.($baseDomainLevel + 1));
    $this->subdomain = $domainParts[0];
    return $this->subdomain;
  }

  static function url($subdomain) {
    return '//'.$subdomain.'.'.SITE_DOMAIN;
  }

}