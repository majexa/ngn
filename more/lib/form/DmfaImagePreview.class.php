<?php

class DmfaImagePreview extends DmfaImage {

  function elAfterCreateUpdate(FieldEFile $el) {
    if (($imageRoot = parent::elAfterCreateUpdate($el)) === false) return false;
    try {
      $this->dm->makeThumbs($imageRoot);
    } catch (Exception $e) {
      // Если не получилось сделать тумбу
      if ($el['required']) {
        // И поле обязательно, удаляем запись
        $this->dm->delete($this->dm->id);
      }
      else {
        // Очищаем поле
        $this->dm->updateField($this->dm->id, $el['name'], '');
      }
      // и удаляем оригинал
      File::delete($imageRoot);
      throw new NgnValidError($e->getMessage());
    }
    if (($wmConf = Config::getVar('watermark', true)) and $wmConf['enable']) {
      // Делаем вотермарк для превьюшки
      $watermark = new ImageWatermark(WEBROOT_PATH.'/'.$wmConf['path'], $wmConf['rightOffset'], $wmConf['bottomOffset']);
      if ($wmConf['q']) $watermark->jpegQuality = $wmConf['q'];
      $watermark->make(Misc::getFilePrefexedPath($imageRoot, 'md_'));
      copy($imageRoot, Misc::getFilePrefexedPath($imageRoot, 'orig_'));
      $watermark->make($imageRoot);
    }
  }

}