<?php

trait SflmMtDependenciesOrder {

  /**
   * @param string $require ้อั ะมหลิม
   * @return int
   * @throws Exception
   */
  function getDepth($require, $depth = 0) {
    $package = $this->find($require);
    if (empty($package['requires'])) {
      return $depth;
    } else {
      return $this->getRequiresDepth($package['requires'], $depth + 1);
    }
  }

  protected function getRequiresDepth(array $requires, $_depth) {
    $max = $_depth;
    foreach ($requires as $require) {
      $depth = $this->getDepth($require, $_depth);
      if ($depth > $max) $max = $depth;
    }
    return $max;
  }

  protected function setPackagesOrder() {
    foreach ($this->data as $package => $v) {
      $this->data[$package]['order'] = $this->getDepth($package);
    }
    $this->data = Arr::sortByOrderKey($this->data, 'order');
  }

}