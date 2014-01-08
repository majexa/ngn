<?php

trait DebugOutput {

  protected function isDebug() {
    return true;
  }

  protected function output($s) {
    if ($this->isDebug()) output($s);
  }

}