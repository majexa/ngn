<?php

class FieldEImagesPreview extends FieldEImage {

  function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'multiple' => true
    ]);
  }

}
