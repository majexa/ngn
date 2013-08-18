<?php

class DdoAdmin extends Ddo {

  protected function defineOptions() {
    return [
      'fieldOptions' => [
        'getAll' => true
      ]
    ];
  }

  function initFields() {
    parent::initFields();
    foreach ($this->fields as &$f) {
      if (!isset($f['type'])) die2($f);
      if (FieldCore::hasAncestor($f['type'], 'bool')) $f['forceEmpty'] = true;
    }
    return $this;
  }

  protected function initTpls() {
    parent::initTpls();
    /*
    if (isset($this->page['settings']['slavePageId'])) {
      $this->ddddByName['title'] =
        '`<h2><b><a href="`.Tt()->getPath(2).`/'.$this->page['settings']['slavePageId'].
        '/editContent/v.'.DdCore::masterFieldName.'.`.$id.`"title="Зайти в `.$v.`" class="tooltip">`.$v.`</a></b></h2>`';
    } else {
      $this->ddddByName['title'] = '`<a href="`.Tt()->getPath().`?a=edit&itemId=`.$id.`"><h2>`.$v.`</h2></a>`';
    }
    */
    //$this->ddddByName['title'] = '`<a href="`.Tt()->getPath().`?a=edit&itemId=`.$id.`"><h2>`.$v.`</h2></a>`';
    $this->ddddByType['image'] = '$v ? `<a href="`.Misc::getFilePrefexedPath($v, `md_`, `jpg`).`" target="_blank" class="thumb" rel="ngnLightbox[set1]"><img src="`.Misc::getFilePrefexedPath($v, `sm_`, `jpg`).`" /></a><div class="clear"><!-- --></div>` : ``';
    $this->ddddByType['bool'] = '`<a href="" class="iconBtn iconFlag icon_flag`.($v ? `On` : `Off`).` flag`.($v ? `On` : `Off`).` tooltip" title="`.$title.`"><i></i></a>`';
    $this->ddddByType['author'] = '`<a href="`.Tt()->getPath(1).`/users/?a=edit&id=`.$v[`id`].`">`.$authorLogin.`</a>`';
    $this->ddddByType['select'] = '`<a href="`.Tt()->getPath(4).`/v.`.$name.`.`.$v[`k`].`">`.$v[`v`].`</a>`';
    $this->ddddByType['tagsMultiselect'] = 
      '($v ? $title.`: ` : ``).Tt()->enumSsss($v, `<a href="`.Tt()->getPath(4).`/t2.$groupName.$name">$title</a>`)';
    $this->ddddByType['tagsSelect'] =
      '`<a href="`.Tt()->getPath(4).`/t2.`.$v[`groupName`].`.`.$v[`name`].`">`.$v[`title`].`</a>`';
    $this->ddddByType['tags'] = $this->ddddByType['tagsMultiselect'];
    unset($this->tplPathByType['tags']);
    $this->ddddByType['sound'] = $this->ddddByType['sound'] . '.
(
  $o->items[$id][`soundListenTime`] ?
    `<span class="soundListenTime tooltip" title="Общее время прослушивания трека">`.
      round($o->items[$id][`soundListenTime`]/60).` мин.</span>
      <a href="`.Tt()->getPath(4).`/soundStat/`.$o->strName.`/`.$id.`" class="soundStat smIcons stat gray"><i></i>Статистика</a>` :
    `dd`
)
';
  }

  protected function htmlEl_________REMOVE(array $data) {
    return $this->removeTitle(parent::htmlEl($data));
  }
  
}