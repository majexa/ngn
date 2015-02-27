<?php

class DdoItemElements {

  protected $ddo;

  function __construct(Ddo $ddo, array $item) {
    $this->ddo = $ddo;
    $this->item = $item;
  }

  function html() {
    $extraClasses = [];
    if (($itemClasses = Config::getVarVar('dd', 'useFieldNameAsItemClass', true))) {
      foreach ($itemClasses as $v) {
        $extraClasses[] = $v['field'].'_'.Ddo::getFlatValue($this->item[$v['field']]);
      }
    }
    $s = '';
    $s .= '<div class="item'.($this->item['active'] ? '' : ' nonActive'). //
      (!empty($this->item['image']) ? ' isImage' : ''). //
      ($extraClasses ? ' '.implode(' ', $extraClasses) : ''). //
      '" data-id="'.$this->item['id'].'" data-userId="'.$this->item['userId'].'">';
    $s .= '<div class="itemBody">';
    $fields = array_values($this->ddo->fields);
    $group = [];
    if ($this->ddo->groupElementsColsN) {
      for ($n = 0; $n < count(array_values($this->ddo->fields)); $n++) {
        $field = $fields[$n];
        if (DdFieldCore::isGroup($field['type']) or !$this->ddo->groupElements) $group[] = $n;
      }
      $fieldsN = count($group) / $this->ddo->groupElementsColsN;
    }
    for ($n = 0; $n < count($fields); $n++) {
      $fields[$n]['evenNum'] = $n % 2;
      // Открывающийся тэг группы
      if ($this->ddo->groupElementsColsN) for ($col = 1; $col < $this->ddo->groupElementsColsN; $col++) if ($n == $group[$fieldsN * $col]) print '</div><!-- Close col --><div class="col col'.$n.'">';
      if ($this->ddo->groupElementsColsN) if ($n == 0) print '<div class="col col'.$n.'">';
      if ($this->ddo->groupElements and $n == 0 or $this->ddo->isGroupped($fields[$n]['name'])) {
        // Если это первый элемент или это элемент после Заголовка
        $s .= $this->ddo->hgrpBeginDddd($fields[$n]['type'], $fields[$n]['name'], $fields[$n]['evenNum']);
      }
      $type = DdFieldCore::getType($fields[$n]['type'], false);
      if (empty($type['noElementTag'])) {
        $el = $this->item[$fields[$n]['name']]; // $el содержит текущее значение элемента записи
        $s .= St::dddd($this->ddo->elBeginDddd, $fields[$n]);
        $s .= $this->ddo->_el($el, $fields[$n]['name'], $this->item);
        $s .= $this->ddo->elEnd;
      }
      // Закрывающийся тэг группы
      if ($this->ddo->groupElements and isset($fields[$n + 1]) and $this->ddo->isGroupped($fields[$n + 1]['name'])
      ) {
        // Если это последний элемент или элемент перед Заголовком
        $s .= '</div><!-- Close fields group -->';
      }
    }
    // Закрывающийся тэг группы
    if ($this->ddo->groupElements) $s .= '</div><!-- Close fields group -->';
    if ($this->ddo->groupElementsColsN) $s .= '</div><!-- Close col -->';
    $s .= '<div class="clear"><!-- --></div>';
    $s .= '</div>';
    $s .= '</div>';
    return $s;
  }

}