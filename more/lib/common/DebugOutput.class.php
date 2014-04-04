<?php

trait DebugOutput {

  protected function isDebug() {
    return false;
  }

  protected function output($s) {
    if ($this->isDebug()) output($s);
  }

}