<?php

class FieldEAdminModules extends FieldESelect {

  protected function defineOptions() {
    $this->options['options'] = ['' => '—'];
    AdminModule::$forseListAllow = true;
    $this->options['options'] += Arr::get(AdminModule::getModules(), 'title', 'name');
    AdminModule::$forseListAllow = false;
  }

}