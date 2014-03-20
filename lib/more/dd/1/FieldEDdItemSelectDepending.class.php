<?php

class FieldEDdItemSelectDepending extends FieldEAbstract {

  protected $useTypeJs = true, $parentOptions;

  static $ddTags = true, $ddTagItems = true;

  function _html() {
    $items = (new DdItems($this->options['settings']['parentStrName']));
    if (isset($this->options['settings']['itemsSort'])) $items->cond->setOrder($this->options['settings']['itemsSort']);
    $parentOptions = Arr::get($items->getItems(), 'title', 'id');
    if (!empty($this->options['ddFilter'])) $parentOptions = Arr::filterByKeys($parentOptions, $this->options['ddFilter']);
    asort($parentOptions);
    $parentOpts['class'] = $this->options['settings']['parentStrName'];
    $opts['class'] = $this->options['settings']['strName'];
    if (empty($this->options['value'])) $selectedParentId = key($parentOptions);
    else $selectedParentId = (new DdItems($this->options['settings']['strName']))->getItem($this->options['value'])[$this->options['settings']['parentTagFieldName']]['id'];
    $items = new DdItems($this->options['settings']['strName']);
    if (isset($this->options['settings']['itemsSort'])) $items->cond->setOrder($this->options['settings']['itemsSort']);
    if ($selectedParentId) $items->addTagFilter($this->options['settings']['parentTagFieldName'], $selectedParentId);
    $this->options['options'] = Arr::get($items->getItemsSimple(), 'title', 'id');
    if (!$this->options['value']) $this->options['value'] = key($this->options['options']);
    $html = Html::select($this->options['settings']['parentTagFieldName'], $parentOptions, $selectedParentId, $parentOpts);
    $html .= Html::select($this->options['name'], $this->options['options'], $this->options['value'], $opts);
    $html .= '<div class="data" '.Html::dataParams([
        'parentTagFieldName' => $this->options['settings']['parentTagFieldName'],
        'strName'            => $this->options['settings']['strName'],
        'fieldName'          => $this->options['name'],
        'itemsSort'          => isset($this->options['settings']['itemsSort']) ? $this->options['settings']['itemsSort'] : '',
      ]).'></div>';
    return $html;
  }

}