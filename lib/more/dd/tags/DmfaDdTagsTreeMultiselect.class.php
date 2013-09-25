<?php

class DmfaDdTagsTreeMultiselect extends DmfaDdTagsAbstract {

  function afterCreateUpdate($tagIds, $k) {
    if (empty($tagIds)) {
      $this->deleteTagItems($k);
      return;
    }
    $tagItems = DdTags::items($this->dm->strName, $k);
    $currentTags = $this->dm->getItem($this->dm->id)[$k];
    $currentTagIds = $currentTags ? Arr::get($currentTags, 'id') : [];
    $newTagIds = [];
    $deleteTagIds = [];
    foreach ($tagIds as $id) if (!in_array($id, $currentTagIds)) $newTagIds[] = $id;
    foreach ($currentTagIds as $id) if (!in_array($id, $tagIds)) $deleteTagIds[] = $id;
    $collectionTagIds = (new DdTagsTagsTree(new DdTagsGroup($this->dm->strName, $k)))->getParentIds($newTagIds);
    $tagItems->createByIdsCollection($this->dm->id, $collectionTagIds, false);
    $tagItems->updateCounts($deleteTagIds);
  }

}
