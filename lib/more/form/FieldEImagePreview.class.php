<?php

class FieldEImagePreview extends FieldEImage {

  function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'currentFileClass' => 'image lightbox',
      'rowClass' => 'elImagePreview'
    ]);
  }
  
  protected function getCurrentValue() {
    if (!($v = parent::getCurrentValue())) return false;
    return Misc::getFilePrefexedPath($v, 'sm_');
  }
  
  protected function htmlNav() {
    if (!($v = $this->getCurrentValue())) return '';
    $deleteBtn = (!empty($this->form->options['deleteFileUrl']) and empty($this->options['required'])) ?
      '<a href="'.$this->form->options['deleteFileUrl'].'&fieldName='.$this->options['name'].'" class="iconBtn noborder delete confirm" title="Удалить"><i></i></a>' :
      '';
    return
'
<div class="fileNav">
  <div class="fileNavImagePreview">
    '.$deleteBtn.'
    <a href="'.parent::getCurrentValue().'" class="thumb lightbox" title="Текущее изображение"><img src="'.$this->getCurrentValue().'" /></a>
  </div>
</div>
';
  }

}
