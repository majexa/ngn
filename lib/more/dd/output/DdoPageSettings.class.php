<?php

class DdoPageSettings extends DdoSettings {

  protected $page;

  function __construct(DbModelPages $page) {
    $this->page = $page;
    parent::__construct($this->page['strName']);
  }

  protected function getKey($prefix, $suffix = null) {
    if (!empty($this->page['module'])) {
      foreach (PageModuleCore::getAncestorNames($this->page['module']) as $module) {
        $key = $prefix.'.'.$module.($suffix ? '.'.$suffix : '');
        if (($r = Config::getVar($key, true)) !== false) {
          return $key;
        }
      }
    }
    return parent::getKey($prefix, $suffix);
  }

  function delete() {
    //Dir::deleteFiles(SITE_PATH."/config/vars/ddo", '*.'.$this->pageModule.'.*');
  }

  function renameField($old, $new) {
    if (!empty($this->page['module'])) {
    }
    else {
      $prefixes = ['fieldOrder'];
      $prefixesSubvalues = ['itemsShow', 'outputMethod'];
      foreach ($prefixes as $prefix) {
        $key = "ddo/$prefix.{$this->page['strName']}";
        if (($file = SiteConfig::hasSiteVar($key)) !== false) {
          SiteConfig::updateVar($key, Arr::replaceKey(include $file, $old, $new));
        }
      }
      foreach ($prefixesSubvalues as $prefix) {
        $key = "ddo/$prefix.{$this->page['strName']}";
        if (($file = SiteConfig::hasSiteVar($key)) !== false) {
          $r = include $file;
          foreach ($r as $k => $v) $r[$k] = Arr::replaceKey($v, $old, $new);
          SiteConfig::updateVar($key, $r);
        }
      }
    }
  }

  function rename($newModule) {
    //SiteConfig::renameVar('ddo/fieldOrder', $this->pageModule, $newModule);
    //SiteConfig::renameVar('ddo/itemsShow', $this->pageModule, $newModule);
    //SiteConfig::renameVar('ddo/outputMethod', $this->pageModule, $newModule);
  }

}