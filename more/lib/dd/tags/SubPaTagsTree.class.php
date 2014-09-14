<?php

class SubCtrlTagsTree extends SubCtrl {

  /**
   * @return DdTagsTagsTree
   */
  protected function getTags() {
    return DdTags::getByGroupId($this->ctrl->req->param(2));
  }
	
  function action_ajax_move() {
    $this->getTags()->move($this->ctrl->req->rq('id'), $this->ctrl->req->rq('toId'), $this->ctrl->req->rq('where'));
  }

  function action_ajax_rename() {
    DbModelCore::update('tags', $this->ctrl->req->rq('id'), ['title' => $this->ctrl->req->rq('title')]);
  }

  function action_ajax_delete() {
    $this->getTags()->delete($this->ctrl->req->rq('id'));
  }
  
  function action_json_create() {
    $this->ctrl->json = $this->getTags()->getItem(
      $this->getTags()->create([
        'title' => $this->ctrl->req->rq('title'),
        'parentId' => $this->ctrl->req->rq('parentId'),
        'userGroupId' => $this->ctrl->userGroup['id']
      ])
    ); 
  }
  
  function action_json_move() {
    $this->ctrl->json = (new ClientTree($this->getTags()))->getTree();
  }
  
  function action_json_getTree() {
    $this->ctrl->json = (new ClientTree($this->getTags()))->getTree();
  }

}
