<?php

class DmfaImage extends DmfaFile {

  function elAfterCreateUpdate(FieldEFile $el) {
    if (($path = parent::elAfterCreateUpdate($el)) !== false and File::getMime($path) == 'image/jpeg') {
      //if (getOS() != 'win') sys("convert $path -colorspace RGB $path");
    } // for IE
    return $path;
  }

}