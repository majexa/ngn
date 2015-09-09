<?php

/**
 * MooTools Dependencies Manger
 */
class SflmMtDependencies {

  protected $mootoolsReposRoot, $buildFolder, $files, $dependencies = [], $data = [];

  function __construct($mootoolsReposRoot) {
    $this->mootoolsReposRoot = $mootoolsReposRoot;
    $this->loadFiles('core');
    $this->loadFiles('more');
    $this->loadDependencies();
  }

  protected function getContents($file) {
    $c = file_get_contents($file);
    $c = preg_replace('/\\/\\*<\d+\\.\d+compat>\\*\\/.*\\/\\*<\\/\d+\\.\d+compat>\\*\\//Ums', '', $c);
    $c = preg_replace('/\\/\\/<\d+\\.\d+compat>.*\\/\\/<\\/\d+\\.\d+compat>/Ums', '', $c);
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
    foreach ($this->names() as $name) {
      if (!preg_match('/[^.a-zA-Z0-9_$]'.$name.'/', $code)) continue;
      $package = $this->find($name);
      if (in_array($package['package'], $this->parsedPackages)) {
        Sflm::log("Mt-package '{$package['package']}' already parsed (name: $name)");
      }
      Sflm::log("Adding mt-package '{$package['package']}' (name: $name)");
      $r .= file_get_contents($package['file']);
    }
    return $r;
  }

  /**
   * Возвращает содержание файла в пакете
   *
   * @param string $name Пакет
   * @return mixed|string
   */
  function contents($name) {
    return file_get_contents($this->find($name)['file']);
  }

//
//  function build($package, $buildFolder) {
//    file_put_contents($buildFolder.'/'.$package.'.js', $this->packageContents($package));
//  }

  protected function _loadDependencies($file) {
    $c = $this->getContents($file);
    preg_match_all('|/\\*(.*)\\*/|msU', $c, $m);
    foreach ($m[1] as $v) {
      if (!preg_match('/name: ([^\n]+)/', $v, $m2)) continue;
      $m2[1] = trim($m2[1]);
      $this->data[$m2[1]]['package'] = $m2[1];
      $this->data[$m2[1]]['file'] = (string)$file;
      $this->addData($m2[1], $v, 'provides');
      $this->addData($m2[1], $v, 'requires');
    }
  }

  protected function addData($package, $v, $keyword) {
    if (preg_match('/'.$keyword.': ([^\n]+)/', $v, $m)) {
      $this->data[$package][$keyword] = array_map('trim', explode(',', trim(trim($m[1]), '[]')));
    }
  }

  protected function loadFiles($type) {
    $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->mootoolsReposRoot."/$type/Source"), RecursiveIteratorIterator::SELF_FIRST);
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
