<?php

class FieldEAdminModules extends FieldESelect {

  protected function defineOptions() {
    AdminModule::$forseListAllow = true;
    $r = [
      'options' => array_merge(['' => '—'], Arr::get(AdminModule::getModules(), 'title', 'name'))
    ];
    AdminModule::$forseListAllow = false;
    return $r;
  }

}