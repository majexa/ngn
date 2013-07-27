<?php

class FieldEImagedRadio extends FieldERadio {

  public $markerHtml = '<div class="marker"><i></i></div>';

  function _js() {
    return <<<JS
$('{$this->oForm->id()}').getElements('.type_{$this->type}').each(function(el) {
  Ngn.frm.imagedRadio(el);
});
JS;
  }

}
