<?php

class FieldESiteControllers extends FieldESelect {

  protected function defineOptions() {
    return ['options' => PageControllersCore::getTitles()];
  }

}