<?php

class FieldEPageId extends FieldEHiddenWithRow {

  function _js() {
    $json = Arr::jsObj(empty($this->options['dd']) ? [] : ['dd' => true]);
    return <<<JS
$('{$this->oForm->id()}').getElements('.type_pageId').each(function(el){
  new Ngn.frm.Page.Id(el, $json);
});
JS;
  }

}