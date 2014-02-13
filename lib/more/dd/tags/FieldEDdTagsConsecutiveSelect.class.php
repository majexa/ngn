<?php

DdFieldCore::registerType('ddTagsConsecutiveSelect', [
  'dbType'   => 'VARCHAR',
  'dbLength' => 255,
  'title'    => 'Последовательный выбор тэга',
  'order'    => 260,
]);

class FieldEDdTagsConsecutiveSelect extends FieldEAbstract {

  static $ddTags = true, $ddTagsTree = true;

  /**
   * @var DdTagsTagsTree
   */
  protected $tags;

  protected $useTypeJs = true;

  protected function init() {
    parent::init();
    if (isset($this->options['rootTagId'])) $this->rootTagId = $this->options['rootTagId'];
    $this->tags = DdTags::get($this->form->strName, $this->baseName);
  }

  protected function formatValue() {
    if (empty($this->options['value'])) return null;
    try {
      return $this->tags->getParentIds2($this->options['value']);
    } catch (NotFoundException $e) {
      throw new NotFoundException("Getting parents for tag name={$this->options['name']}, id={$this->options['value']} error");
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
      $d['items'] = [
        [
          'options' => $this->getRootOptions()
        ]
      ];
    }
    else {
      $d['items'] = [
        [
          'default' => isset($this->options['value'][$this->firstN()]) ? $this->options['value'][$this->firstN()]['id'] : null,
          'options' => $this->getRootOptions()
        ]
      ];
      if (count($this->options['value']) > 1) {
        $selectedValues = $this->getSelectedValues();
        $parentId = $this->getSecondParentId();
        for ($i = 0; $i < count($selectedValues); $i++) {
          $tagId = $selectedValues[$i];
          $d['items'][] = [
            'default' => $tagId,
            'options' => ['' => '—'] + Arr::get($this->tags->getTags($parentId), 'title', 'id')
          ];
          $parentId = $tagId;
        }
      }
    }
    return Tt()->getTpl('dd/consecutiveSelect', $d);
  }

  protected function getRootOptions() {
    if (isset($this->options['rootTagId'])) $this->rootTagId = $this->options['rootTagId'];
    return Arr::get($this->tags->getTags($this->rootTagId), 'title', 'id');
  }

  protected function firstN() {
    return 0;
  }

  protected function getSelectedValues() {
    return Arr::get(Arr::sliceFrom($this->options['value'], $this->firstN() + 1), 'id');
  }

  protected function getSecondParentId() {
    return $this->options['value'][$this->firstN()]['id'];
  }

} 
