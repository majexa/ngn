<?php

class DdoAdmin extends Ddo {

  protected function defineOptions() {
    return [
      'fieldOptions' => [
        'getAll' => true
      ]
    ];
  }

  public function initFields() {
    parent::initFields();
    $this->fields = array_filter($this->fields, function ($f) {
      if (FieldCore::hasAncestor($f['type'], 'header')) return false;
      return true;
    });
    foreach ($this->fields as &$f) {
      if (FieldCore::hasAncestor($f['type'], 'bool')) $f['forceEmpty'] = true;
    }
    return $this;
  }

  protected function initTpls() {
    parent::initTpls();
    $this->ddddByType['image'] = '$v ? `<a href="`.Misc::getFilePrefexedPath($v, `md_`, `jpg`).`" target="_blank" class="thumb" rel="ngnLightbox[set1]"><img src="`.Misc::getFilePrefexedPath($v, `sm_`, `jpg`).`" /></a><div class="clear"><!-- --></div>` : ``';
    $this->ddddByType['bool'] = '`<a href="" class="iconBtn iconFlag icon_flag`.($v ? `On` : `Off`).` flag`.($v ? `On` : `Off`).` tooltip" title="`.$title.`"><i></i></a>`';
    $this->ddddByType['author'] = '`<a href="`.Tt()->getPath(1).`/users/?a=edit&id=`.$v[`id`].`">`.$authorLogin.`</a>`';
    $this->ddddByType['select'] = '`<a href="`.Tt()->getPath(4).`/v.`.$name.`.`.$v[`k`].`">`.$v[`v`].`</a>`';
    $this->ddddByType['tagsMultiselect'] = '($v ? $title.`: ` : ``).St::enumSsss($v, `<a href="`.Tt()->getPath(4).`/t2.$groupName.$name">$title</a>`)';
    $this->ddddByType['tagsSelect'] = '`<a href="`.Tt()->getPath(4).`/t2.`.$v[`groupName`].`.`.$v[`name`].`">`.$v[`title`].`</a>`';
    $this->ddddByType['tags'] = $this->ddddByType['tagsMultiselect'];
    unset($this->tplPathByType['tags']);
  }

  protected function htmlEl_________REMOVE(array $data) {
    return $this->removeTitle(parent::htmlEl($data));
  }

}