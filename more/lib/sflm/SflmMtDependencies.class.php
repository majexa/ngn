<?php

/**
 * MooTools Dependencies Manger
 */
class SflmMtDependencies {
use SflmMtDependenciesOrder;

  protected $mootoolsReposRoot, $buildFolder, $files, $dependencies = [], $data = [];

  function __construct() {
    $this->loadFiles('core');
    $this->loadFiles('more');
    $this->loadDependencies();
  }

  protected function getContents($file) {
    $c = file_get_contents($file);
    $c = preg_replace('/\\/\\*<\d+\\.\d+compat>\\*\\/.*\\/\\*<\\/\d+\\.\d+compat>\\*\\//Ums', '', $c);
    $c = preg_replace('/\\/\\/<\d+\\.\d+compat>.*\\/\\/<\\/\d+\\.\d+compat>/Ums', '', $c);
    $c = str_replace("\r", '', $c);
    return $c;
  }

  protected function loadDependencies() {
    foreach ($this->files as $file) {
      $this->_loadDependencies($file);
    }
  }

  protected $parsedPackages = [];

  /**
   * Анализирует JavaScript на наличие в нём MooTools библиотек и возвращает JavaScript-код с ними
   *
   * @param $code
   * @return string
   * @throws Exception
   */
  function parse($code) {
    $r = '';
    $r .= $this->addNamespace($code);
    if (strstr($code, 'window.addEvent(')) {
      $r .= $this->parseContentsR('Element.Event');
    }
    if (strstr($code, 'domready')) {
      $r .= $this->parseContentsR('DOMReady');
    }
    foreach ($this->names() as $name) {
      if (!strstr($code, $name)) continue;
      $r .= $this->parseContentsR($name);
    }
    return $r;
  }

  protected $namespaceAdded = false;

  protected function addNamespace($code) {
    if ($this->namespaceAdded) return '';
    if (strstr($code, Sflm::$namespace.'.')) {
      $this->namespaceAdded = true;
      return "var ".Sflm::$namespace." = {};\n";
    }
    return '';
  }

  /**
   * Возвращает содержание файла в пакете
   *
   * @param string $name Пакет
   * @return mixed|string
   */
  function contents($name) {
    Sflm::log("Mt: getting contents of '$name' package");
    return $this->getContents($this->find($name)['file']);
  }

  function parseContentsR($name, $source = 'root') {
    $r = '';
    $package = $this->find($name);
    if (in_array($package['package'], $this->parsedPackages)) {
      return '';
    }
    $this->parsedPackages[] = $package['package'];
    if (!empty($package['requires'])) {
      foreach ($package['requires'] as $_name) {
        $r .= $this->parseContentsR($_name, $package['package']);
      }
    }
    Sflm::log('Mt: adding "'.$package['package'].'", src: '.$source);
    $r .= $this->getContents($package['file']);
    return $r;
  }

  protected function _loadDependencies($file) {
    $c = $this->getContents($file);
    preg_match_all('|/\\*\\n---(.*)\.\.\.\\n|msU', $c, $m);
    foreach ($m[1] as $mtDocComment) {
      $d = sfYaml::load($mtDocComment);
      $this->data[$d['name']]['package'] = $d['name'];
      $this->data[$d['name']]['file'] = (string)$file;
      $this->data[$d['name']]['provides'] = (array)$d['provides'];
      if (isset($d['requires'])) {
        $this->data[$d['name']]['requires'] = (array)$d['requires'];
      }
    }
  }

  protected function addData($package, $v, $keyword) {
    if (preg_match('/'.$keyword.': ([^\n]+)/', $v, $m)) {
      $this->data[$package][$keyword] = array_map('trim', explode(',', trim(trim($m[1]), '[]')));
      foreach ($this->data[$package][$keyword] as $k => $v) {
        if (strstr($v, '/')) {
          $this->data[$package][$keyword][$k] = preg_replace('/.*\\/(.*)/', '$1', $v);
        }
      }
    }
  }

  protected function loadFiles($type) {
    $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(NGN_ENV_PATH."/mootools-$type/Source"), RecursiveIteratorIterator::SELF_FIRST);
    foreach ($objects as $object) {
      if (is_dir($object)) continue;
      if (!Misc::hasSuffix('.js', $object)) continue;
      $this->files[Misc::removeSuffix('.js', basename($object))] = $object;
    }
  }

  /**
   * @param $name
   * @throws Exception
   * @return array
   */
  function find($name) {
    if (strstr($name, '/')) {
      $name = explode('/', $name)[1];
    }
    if (isset($this->data[$name])) {
      return $this->data[$name];
    }
    foreach ($this->data as $package => $v) {
      if (in_array($name, $v['provides'])) {
        return $v;
      }
    }
    throw new Exception("MtPackage '$name' not found");
  }

  protected $names;

  function names() {
    if (isset($this->names)) return $this->names;
    $r = [];
    foreach ($this->data as $name => $v) {
      if ($name == 'Core') continue;
      $r = array_merge($r, $v['provides']);
    }
    unset($r[array_search('$', $r)]);
    unset($r[array_search('$$', $r)]);
    return $this->names = array_values($r);
  }

}
