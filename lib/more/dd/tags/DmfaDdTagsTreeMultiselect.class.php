<?php

class DmfaDdTagsTreeMultiselect extends DmfaDdTagsAbstract {

  function afterCreateUpdate($tagIds, $k) {
    if (empty($tagIds)) $this->deleteTagItems($k);
    else {
      // die2($this->dm->id);
      $collectionTagIds = (new DdTagsTagsTree(new DdTagsGroup($this->dm->strName, $k)))->getParentIds($tagIds);
      DdTags::items($this->dm->strName, $k)->createByIdsCollection($this->dm->id, $collectionTagIds);
    }
  }

}
