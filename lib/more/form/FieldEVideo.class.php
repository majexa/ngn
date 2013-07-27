<?php

class FieldEVideo extends FieldEFile {

  protected function validate2() {
    try {
      die2(O::get('VideoDecoder')->getInfo($this->options['value']['tmp_name']));
    } catch (Exception $e) {
      if ($e->getCode() == 311 or $e->getCode() == 322) {
        throw new NgnValidError(
          'Вы пытаетесь загрузить файл не являющийся видео или этот формат просто не поддерживается', 444);
      } else {
        throw new Exception($e->getMessage());
      }
    }
  }

  // реализовать с очередью
  // function afterCreateUpdate($itemId, DataManagerAbstract $dm) {
  //     $data[$k] = '[processing]';
  //     $data['active'] = 0;
  /**
   *

  NgnQueueCore::addJob('DdItemsManagerPage', 'videoConvert', array(
    'pageId' => $this->pageId,
    'itemId' => $itemId,
    'filePath' => $filePath,
    'fieldName' => $k
  ));
    
  static function videoConvert(array $data) {
    Arr::checkEmpty($data, array('pageId', 'itemId', 'filePath', 'fieldName'));
    if (!($page = NgnOrmCore::getTable('Pages')->find($data['pageId'])))
      throw new Exception('Page not found');
    $oItems = new DdItemsPage($page->id);
    $newFilePath = UPLOAD_PATH.'/'.File::stripExt($data['filePath']);
    rename(UPLOAD_PATH.'/'.$data['filePath'], $newFilePath);
    if (empty($page->settings['mdW'])) {
      $imageSizes = DdItemsManagerPage::$defaultImageSizes;
    } else {
      $imageSizes = array(
        'mdW' => $page->settings['mdW'],
        'mdH' => $page->settings['mdH']
      );
    }
    $oVideoPreview = new VideoPreview();
    $oVideoPreview->makePreview(
      $newFilePath,
      $imageSizes['mdW'],
      $imageSizes['mdH']
    );
    $oVM = new VideoManager();
    $videoFile = $oVM->make(
      $newFilePath,
      dirname($newFilePath),
      VIDEO_3_W,
      VIDEO_3_H
    );
    $oItems->update($data['itemId'], array(
      $data['fieldName'] => str_replace(UPLOAD_PATH, '', $videoFile),
      $data['fieldName'].'_dur' => $oVideoPreview->getDurationSec($videoFile),
      'active' => 1
    ));
  }
    

   */
  

}
