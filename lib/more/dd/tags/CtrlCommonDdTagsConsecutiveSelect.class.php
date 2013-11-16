<?php

class CtrlCommonDdTagsConsecutiveSelect extends CtrlCommon {

  public $paramActionN = 3;

  function action_ajax_default() {
    $tags = new DdTagsTagsTree(new DdTagsGroup($this->req->param(2), $this->req->r['name']));
    $tags->getSelectCond()->setOrder('oid, title');
    if (!($tags = $tags->getTags($this->req->r['id']))) return;
    $this->path->tpl('dd/consecutiveSelectAjax', [
      'name'    => $this->req->r['name'],
      'options' => ['' => '—'] + Arr::get($tags, 'title', 'id')
    ]);
  }

}
