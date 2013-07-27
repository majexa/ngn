<?php

class CtrlCommonSlice extends CtrlCommon {
  
  protected function init() {
    if (!Misc::isAdmin()) throw new Exception('You are not admin');
    $this->hasOutput = false;
  }

  function action_json_save() {
    Slice::replace(Arr::filterByKeys($this->req->r, ['title', 'id', 'text']));
  }

  function action_ajax_save() {
    DbModelCore::replace('slices', $this->req->r['id'], $this->req->r, true);
    print DbModelCore::get('slices', $this->req->r['id'])->r['text'];
  }
  
  function action_ajax_savePos() {
    Slice::savePos($this->req->rq('id'), [
      'x' => (int)$this->req->rq('x').'px',
      'y' => (int)$this->req->rq('y').'px'
    ]);
    $this->ajaxSuccess = true;
  }
  
}