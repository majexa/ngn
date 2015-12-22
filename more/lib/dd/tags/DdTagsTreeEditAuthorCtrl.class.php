<?php

trait DdTagsTreeEditAuthorCtrl {

  function oProcessTags(DdTagsTagsBase $tags) {
    $tags->getSelectCond()->addF('userId', Auth::get('id'));
  }

}
