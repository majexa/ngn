<?php

class FieldEMasterName extends FieldESelect {

  protected function defineOptions() {
    $options = ['' => '- мастер не задан -'];
    foreach (glob(NGN_PATH.'/masters/*') as $path) {
      if (is_dir($path)) {
        $masterName = basename($path);
        $options[$masterName] = basename($path);
      }
    }
    return ['options' => $options];
  }

}