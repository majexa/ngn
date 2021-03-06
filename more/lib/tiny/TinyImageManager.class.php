<?php

class TinyImageManager extends TinyUploadManager {
  
  protected $resizeType;
  protected $imageSizes;

  function __construct($attachId, $imageSizes, $isThumb = false, $resizeType = null) {
    parent::__construct($attachId);
    $this->resizeType = $resizeType == 'resize' ? 'resize' : 'resample';
    $this->imageSizes = $imageSizes;
    $this->isThumb = $isThumb;
  }
  
  function process($tempImagePath) {
    if (!($exp = Image::getExtensionByMime(File::getMime($tempImagePath))))
      throw new Exception("Неправильный формат изображения '$tempImagePath'");
    Dir::make($this->folderPath);
    $imageName = File::getUnicName($this->folderPath, $exp);
    $image = $this->folder.'/'.$imageName;
    $imagePath = $this->folderPath.'/'.$imageName;
    copy($tempImagePath, $imagePath);
    unlink($tempImagePath);
    if ($this->isThumb) {
      if (empty($this->imageSizes['smW']))
        throw new Exception("\$this->imageSizes['smW'] is empty");
      if (empty($this->imageSizes['smH']))
        throw new Exception("\$this->imageSizes['smH'] is empty");
      if (empty($this->imageSizes['mdW']))
        throw new Exception("\$this->imageSizes['mdW'] is empty");
      if (empty($this->imageSizes['mdH']))
        throw new Exception("\$this->imageSizes['mdH'] is empty");
      list($width, $height) = getimagesize($imagePath);
      if ($width > $this->imageSizes['smW'] or $height > $this->imageSizes['smH']) {
        $oI = new Image();
        $resizeMethod = $this->resizeType.'AndSave';
        if (!$oI->$resizeMethod($imagePath, Misc::getFilePrefexedPath($imagePath, 'sm_'),
        $this->imageSizes['smW'], $this->imageSizes['smH']))
        $oI->resampleAndSave($imagePath, Misc::getFilePrefexedPath($imagePath, 'md_'),
          $this->imageSizes['mdW'], $this->imageSizes['mdH']);
      }
    }
    return $image;
  }
  
}
