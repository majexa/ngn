<?php

/**
 * DD tags autocomplete
 */
class CtrlCommonDdTagsAc extends CtrlCommon {

  function action_json_default() {
    $r = DdTags::get($this->req->rq('strName'), $this->req->rq('fieldName'))->search($this->req->rq('search'));
    foreach ($r as $v) {
      $this->json[] = [
        $v['id'],
        $v['title'].($v['parentTitle'] ? ' ← '.$v['parentTitle'] : ''),
        $v['title'],
      ];
    }
  }

}
