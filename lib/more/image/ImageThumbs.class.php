<?php

class ImageThumbs {
  
  /**
   * @var Image
   */
  protected $oImage;
  
  protected $imageRoot;
  protected $mime;
  
  function __construct($imageRoot) {
    $this->oImage = new Image();
    $this->imageRoot = $imageRoot;
    $this->mime = File::getMime($imageRoot);
    
    // Если MIME нет в списке дозволенных типов для изображения
    if (!Image::getExtensionByMime($this->mime)) {
      throw new Exception('Mime does not supported');
    }
    $ext = 'jpg';
    // не будет возвращать правильное значение
    $imagePath = $this->getFilePath($itemId, $k, $ext);
    Dir::make(dirname(UPLOAD_PATH.'/'.$imagePath));
    /////////////////////////////////////////
    $imageRoot = UPLOAD_PATH.'/'.$imagePath;
    copy($v['tmp_name'], $imageRoot);
    
    try {
      $this->makeThumbs($imageRoot);
    } catch (Exception $e) {
      // Если не получилось сделать тумбу, удаляем значение поля
      db()->query("UPDATE {$this->oItems->table} SET $k=? WHERE id=?d", '', $itemId);
      // и оригинал
      unlink($imageRoot);
      throw new Exception($e->getMessage());
    }
    
    if (($wmConf = Config::getVar('watermark', true)) and $wmConf['enable']) {
      // Делаем вотермарк для превьюшки
      $oIW = new ImageWatermark(WEBROOT_PATH.'/'.$wmConf['path'], 
        $wmConf['rightOffset'], $wmConf['bottomOffset']);
      if ($wmConf['q'])
        $oIW->jpegQuality = $wmConf['q'];
      if (!$oIW->make(Misc::getFilePrefexedPath($imageRoot, 'md_'))) {
        throw new Exception('watermark error');
      }
    }
  }

  /**
   * Создаёт превьюшки изображения
   *
   * @param   string    Путь до картинки от корня
   */
  function makeThumbs() {
    $this->makeSmallThumbs($this->imageRoot);
    $this->makeMiddleThumbs($this->imageRoot);
  }
  
  protected function makeSmallThumbs($imageRoot) {
    if ($this->smResizeType == 'resample') {      
      $this->oImage->resampleAndSave($imageRoot, 
        Misc::getFilePrefexedPath($imageRoot, 'sm_'),
        $this->imageSizes['smW'], $this->imageSizes['smH']);
    } else {
      $this->oImage->resizeAndSave($imageRoot, 
        Misc::getFilePrefexedPath($imageRoot, 'sm_'),
        $this->imageSizes['smW'], $this->imageSizes['smH']);
    }
  }

  protected function makeMiddleThumbs($imageRoot) {
    if ($this->mdResizeType == 'resize') {
      $this->oImage->resizeAndSave($imageRoot, 
        Misc::getFilePrefexedPath($imageRoot, 'md_'),
        $this->imageSizes['mdW'], $this->imageSizes['mdH']);
    } else {
      $this->oImage->resampleAndSave($imageRoot, 
        Misc::getFilePrefexedPath($imageRoot, 'md_'),
        $this->imageSizes['mdW'], $this->imageSizes['mdH']);
    }
  }
  
}
