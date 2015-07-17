<?php

class DmfaTextareaTypo extends Dmfa {

   function elBeforeCreateUpdate(FieldEAbstract $el) {
     $formatText = O::get('FormatText', [
       'allowedTagsConfigName' => 'comments.allowedTags'
     ]);
     $formatText->jevix->cfgSetAutoBrMode(true);
     $this->dm->data[$this->options['name'].'_f'] = $formatText->html($this->options['value']);
   }

}