<?php

class FieldETextareaTypo extends FieldETextarea {

  function value() {
    $formatText = new FormatText(['allowedTagsConfigName' => 'comments.allowedTags']);
    //$formatText->jevix->cfgSetAutoBrMode(true);
    return $formatText->typo($this->options['value']);
  }

}
