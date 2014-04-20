<?php

class UestSflmStaticDependencies extends NgnTestCase {

  function test() {
    Sflm::clearCache();
    Sflm::setFrontendName('dummy');
    //die2(Sflm::frontend('js')->getPaths());
    $this->checkPackage('admin');
  }

  protected function checkPackage($package) {
    $frontend = Sflm::setFrontend('js', $package);
    $frontend->addLib('core');
    //$frontend
    $allPaths = $frontend->getPaths();
    // die2($allPaths);
    $frontend->classes->pathWithSourceProcessor = function($path) use ($allPaths, $package) {
      if (!in_array($path, $allPaths)) throw new Exception("Path '$path' does not exists in package '$package' paths");
    };
    foreach ($frontend->getPaths() as $path) $frontend->classes->processPath($path);
  }

}