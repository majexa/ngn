<?php

/**
 * @method void oProcessTags(DdTagsTagsBase $tags)
 */
trait DdTagsTreeEditAbstractCtrl {
  use ObjectProcessorCtrl;

  abstract protected function getTagsGroupId();

  protected $tags;

  protected function getTags() {
    if (isset($this->tags)) return $this->tags;
    $this->tags = DdTags::getByGroupId($this->getTagsGroupId());
    $this->objectProcess($this->tags, 'tags');
    return $this->tags;
  }

  function action_json_getTree() {
    Sflm::frontend('css')->addPath('i/css/common/tree.css');
    $this->json['tree'] = (new ClientTree($this->getTags()))->getTree();
  }

  function action_json_create() {
    $this->json = $this->getTags()->getItem( //
      $this->getTags()->create([
        'title'    => $this->req->rq('title'),
        'parentId' => $this->req->rq('parentId'),
        //'userGroupId' => $this->getTagsGroupId()
      ]) //
    );
  }

  function action_ajax_rename() {
    $this->getTags()->update($this->req->rq('id'), ['title' => $this->req->rq('title')]);
  }

  function action_ajax_delete() {
    $this->getTags()->delete($this->req->rq('id'));
  }

  function action_ajax_move() {
    $this->getTags()->move($this->req->rq('id'), $this->req->rq('toId'), $this->req->rq('where'));
  }

}
