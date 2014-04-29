<?php

abstract class FieldEAutocompleter extends FieldEInput {

  public $useTypeJs = true;

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