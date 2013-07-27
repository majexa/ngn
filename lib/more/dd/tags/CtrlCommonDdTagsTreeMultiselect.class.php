<?php

class CtrlCommonDdTagsTreeMultiselect extends CtrlCommon {

  function action_ajax_default() {
    $tags = (new DdTagsTagsTree(new DdTagsGroup($this->req->param(2), $this->req->param(3))));
    $this->ajaxOutput = Tt()->getTpl('dd/tagsTreeMultiselectInner', FieldEDdTagsTreeMultiselect::getTplData($tags, $tags->group->name, null, $this->req->param(4), true));
  }

}