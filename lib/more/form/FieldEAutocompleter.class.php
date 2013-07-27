<?php

abstract class FieldEAutocompleter extends FieldEInput {

  protected $useDefaultJs = true;

  protected function __html($acDefault) {
    return Tt()->getTpl('common/autocompleter',
      [
        'name' => $this->options['name'], 
        'actionKey' => $this->type, 
        'acDefault' => $acDefault, 
        'default' => $this->options['value'], 
        'noJS' => true
      ]);
  }

}