<?php

class DmfaDdTagsTreeMultiselect extends DmfaDdTagsAbstract {

  function source2formFormat($v) {
    if (!$v) return '';
    $ids = [];
    foreach ($v as $collection) {
      foreach ($collection as $tag) {
        if (!in_array($tag['id'], $ids)) $ids[] = $tag['id'];
      }
    }
    return $ids;
  }

  function form2sourceFormat($v) {
    return array_map('intval', $v);
  }

  function afterUpdate($tagIds, $k) {
    if (empty($tagIds)) {
      $this->deleteTagItems($k);
      return;
    }
    if (!empty($tagIds) and !is_array($tagIds)) throw new Exception("$k tagIds: ".getPrr($tagIds));
    $currentTagIds = [];
    $deleteCollectionTags = [];
    $currentCollectionTags = $this->dm->items->getItem($this->dm->id)[$k];
    if (isset($currentCollectionTags)) {
      foreach ($currentCollectionTags as $i => $tags) $currentTagIds[$i] = Arr::last($tags)['id'];
      foreach ($currentTagIds as $i => $currentTagId) {
        if (!in_array($currentTagId, $tagIds)) $deleteCollectionTags[] = $currentCollectionTags[$i];
      }
      foreach ($tagIds as $id) if (!in_array($id, $currentTagIds)) $newTagIds[] = $id;
    }
    if (!$deleteCollectionTags and !$newTagIds) return;
    $tagItems = DdTags::items($this->dm->strName, $k);
    if ($deleteCollectionTags) {
      foreach ($deleteCollectionTags as $tags) {
        foreach ($tags as $tag) {
          $tagItems->deleteByCollection($this->dm->id, $tag['id'], $tag['collection']);
        }
      }
    }
    if ($newTagIds) {
      $collectionTagIds = (new DdTagsTagsTree(new DdTagsGroup($this->dm->strName, $k)))->getParentIds($newTagIds);
      $tagItems->createByIdsCollection($this->dm->id, $collectionTagIds, false);
    }
  }

  function afterCreate($tagIds, $k) {
    $tagItems = DdTags::items($this->dm->strName, $k);
    $collectionTagIds = (new DdTagsTagsTree(new DdTagsGroup($this->dm->strName, $k)))->getParentIds($tagIds);
    $tagItems->createByIdsCollection($this->dm->id, $collectionTagIds, false);
  }

}
