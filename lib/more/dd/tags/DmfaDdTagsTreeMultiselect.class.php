<?php

class DmfaDdTagsTreeMultiselect extends DmfaDdTagsAbstract {

  function afterUpdate($tagIds, $k) {
    if (empty($tagIds)) {
      $this->deleteTagItems($k);
      return;
    }
    $tagItems = DdTags::items($this->dm->strName, $k);
    $currentTags = $this->dm->items->getItem($this->dm->id)[$k];
    //die2([$this->dm->id, $currentTags]);
    //die2($this->dm->items->getItemF($this->dm->id));
    //$currentTagIds = $currentTags ? Arr::get($currentTags, 'id') : [];
    $currentTagIds = $currentTags;
    $newTagIds = [];
    $deleteTagIds = [];
    foreach ($tagIds as $id) if (!in_array($id, $currentTagIds)) $newTagIds[] = $id;
    foreach ($currentTagIds as $id) if (!in_array($id, $tagIds)) $deleteTagIds[] = $id;
    die2($deleteTagIds);
    $collectionTagIds = (new DdTagsTagsTree(new DdTagsGroup($this->dm->strName, $k)))->getParentIds($newTagIds);
    foreach ($deleteTagIds as $id) $tagItems->deleteByCollection($this->dm->id, $id);
    $tagItems->createByIdsCollection($this->dm->id, $collectionTagIds, false);
    // delete tag by id does not work. need to check if it is a collection
    $tagItems->updateCounts($deleteTagIds);
  }

  function afterCreate($tagIds, $k) {
    $tagItems = DdTags::items($this->dm->strName, $k);
    $collectionTagIds = (new DdTagsTagsTree(new DdTagsGroup($this->dm->strName, $k)))->getParentIds($tagIds);



    $tagItems->createByIdsCollection($this->dm->id, $collectionTagIds, false);
  }

}
