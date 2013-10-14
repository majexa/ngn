<?php

class DmfaDdTagsTreeMultiselect extends DmfaDdTagsAbstract {

  function afterCreateUpdate($tagIds, $k) {
    if (empty($tagIds)) {
      $this->deleteTagItems($k);
      return;
    }
    $tagItems = DdTags::items($this->dm->strName, $k);
    $currentTags = $this->dm->getItem($this->dm->id)[$k]; // не зачем получать всю запись
    $currentTagIds = $currentTags ? Arr::get($currentTags, 'id') : [];
    $newTagIds = [];
    $deleteTagIds = [];
    foreach ($tagIds as $id) if (!in_array($id, $currentTagIds)) $newTagIds[] = $id;
    foreach ($currentTagIds as $id) if (!in_array($id, $tagIds)) $deleteTagIds[] = $id;
    //if ($k == 'regRegion') prr([$tagIds, $currentTagIds, $newTagIds, $deleteTagIds]);
    $collectionTagIds = (new DdTagsTagsTree(new DdTagsGroup($this->dm->strName, $k)))->getParentIds($newTagIds);
    foreach ($deleteTagIds as $id) $tagItems->deleteByCollection($this->dm->id, $id);
    $tagItems->createByIdsCollection($this->dm->id, $collectionTagIds, false);
    // delete tag by id does not work. need to check if it is a collection
    $tagItems->updateCounts($deleteTagIds);
  }

}
