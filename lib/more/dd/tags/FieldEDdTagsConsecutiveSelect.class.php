<?php

DdFieldCore::registerType('ddTagsConsecutiveSelect', [
  'dbType'   => 'VARCHAR',
  'dbLength' => 255,
  'title'    => 'Последовательный выбор тэга',
  'order'    => 260,
  'tags'     => true,
  'tagsTree' => true
]);

class FieldEDdTagsConsecutiveSelect extends FieldEAbstract {

  /**
   * @var DdTagsTagsTree
   */
  protected $tags;

  protected $useTypeJs = true;

  protected function init() {
    parent::init();
    $this->tags = DdTags::get($this->form->strName, $this->baseName);
  }

  protected function preparePostValue() {
    $this->options['value'] = (int)$this->options['value'];
    if (!empty($this->options['value'])) {
      $this->options['value'] = $this->tags->getParentIds2($this->options['value']);
    }
  }

  protected $rootTagId = 0, $parentId, $selectedValue;

  function _html() {
    $d = [
      'name'     => $this->options['name'],
      'baseName' => $this->baseName,
      'required' => !empty($this->options['required'])
    ];
    if (empty($this->options['value'])) {
      $d['items'] = [[
        'options' => $this->getRootOptions()
      ]];
    } else {
      $d['items'] = [[
        'default' => $this->options['value'][2],
        'options' => $this->getRootOptions()
      ]];
      if (count($this->options['value'] > 1)) {
        $this->selectedValue = $this->getSelectedValues();
        $this->parentId = $this->getSecondParentId();
        for ($i = 0; $i < count($this->selectedValue); $i++) {
          $tagId = $this->selectedValue[$i];
          $d['items'][] = [
            'default' => $tagId,
            'options' => ['' => '—'] + Arr::get($this->tags->getTags($this->parentId), 'title', 'id')
          ];
          $this->parentId = $tagId;
        }
      }
    }
    return Tt()->getTpl('dd/consecutiveSelect', $d);
  }

  protected function getRootOptions() {
    return Arr::get($this->tags->getTags($this->rootTagId), 'title', 'id');
  }

  protected function getSelectedValues() {
    return Arr::sliceFrom($this->options['value'], 1);
  }

  protected function getSecondParentId() {
    return $this->options['value'][0];
  }

} 
