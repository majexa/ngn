<?php

class DmfaDdUserImage extends DmfaImagePreview {

  /**
   * @var DdItemsManagerPage
   */
  protected $dm;

  function deleteAttaches($fieldName) {
    File::delete($this->getAttachePath().'/'.$this->dm->authorId.'.jpg');
    File::delete($this->getAttachePath().'/sm_'.$this->dm->authorId.'.jpg');
    File::delete($this->getAttachePath().'/md_'.$this->dm->authorId.'.jpg');
    UsersCore::cleanAvatarCache($this->dm->authorId);
  }
  
  function getAttacheFolder() {
    return 'user';
  }
  
  function getAttachePath() {
    return UPLOAD_PATH.'/'.$this->getAttacheFolder();
  }
  
  function getAttacheFilenameByEl(FieldEFile $el) {
    Misc::checkEmpty($this->dm->items[$this->dm->id]['authorId']);
    return $this->dm->items[$this->dm->id]['authorId'];
  }
  
  function elBeforeDelete(FieldEFile $el) {
    UsersCore::cleanAvatarCache($this->dm->authorId);
  }
  
  function elAfterUpdate(FieldEFile $el) {
    UsersCore::cleanAvatarCache($this->dm->authorId);
  }

}