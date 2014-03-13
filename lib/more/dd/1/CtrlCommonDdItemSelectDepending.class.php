<?php

class CtrlCommonDdItemSelectDepending extends CtrlCommon {

  function action_ajax_default() {
    $tags = new DdItems($this->req->reqNotEmpty('strName'));
    if (!empty($this->req->r['itemSort'])) $tags->cond->setOrder($this->req->r['itemSort'].' ASC');
    $tags->addTagFilter($this->req->reqNotEmpty('parentTagFieldName'), $this->req->r['id']);
    $opt = $tags->getItems();
    if (empty($opt)) return;

    $this->tt->tpl('dd/consecutiveSelectAjax', [
      'name'    => $this->req->reqNotEmpty('fieldName'),
      'options' => Arr::get($opt, 'title', 'id'),
      'default' => key(Arr::get($opt, 'title', 'id')),
    ]);
  }

}