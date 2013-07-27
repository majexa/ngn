<?php

class FieldEConfigNames extends FieldESelect {

  protected function defineOptions() {
    $this->options['options'] = ['' => '—'];
    $this->options['options'] += SiteConfig::getTitles('vars');
  }

}