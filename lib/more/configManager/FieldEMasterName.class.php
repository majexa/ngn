<?php

class FieldEMasterName extends FieldESelect {

  protected function defineOptions() {
    $this->options['options'] = ['' => '- мастер не задан -'];
    foreach (glob(NGN_PATH.'/masters/*') as $path) {
      if (is_dir($path)) {
        $masterName = basename($path);
        $this->options['options'][$masterName] = basename($path);
      }
    }
  }

}