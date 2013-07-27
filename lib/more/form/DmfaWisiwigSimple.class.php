<?php

/**
 * Без поддержки вложенных файлов и изображений
 */
class DmfaWisiwigSimple extends Dmfa {

  function form2sourceFormat($v) {
    return $oFormatText = O::get('FormatText', [
      'allowedTagsConfigName' => 'tiny.simple.allowedTags'
    ])->html($v);
  }
  
}