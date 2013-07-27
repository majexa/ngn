<?php

class FieldERequestFieldsSelect extends FieldESelect {
  
  static $requiredOptions = ['name', 'action', 'requestedNames'];
  
  protected function init() {
    parent::init();
    foreach ($this->options['requestedNames'] as $name) {
      $this->oForm->createElement([
        'type' => 'virtual',
        'name' => $name,
        'value' => BracketName::getValue($this->oForm->elementsData, $name)
      ]);
    }
  }
  
  function _js() {
    $jsOpts = Arr::jsObj(Arr::filterByKeys($this->options, ['url', 'action']));
    return "
$('{$this->oForm->id()}').getElements('.type_{$this->options['type']}').each(function(el){
  new Ngn.RequestFieldsSelect(el, $jsOpts);
});
";
  }
  
}
